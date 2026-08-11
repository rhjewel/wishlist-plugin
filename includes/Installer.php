<?php
/**
 * Installer helpers.
 *
 * @package WishFlow
 */

namespace WishFlow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Installer {
	/**
	 * Create wishlist table.
	 *
	 * @return void
	 */
	public static function create_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name      = $wpdb->prefix . 'wishflow_items';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			owner_type varchar(20) NOT NULL,
			owner_id varchar(64) NOT NULL,
			post_id bigint(20) unsigned NOT NULL,
			post_type varchar(64) NOT NULL,
			variation_id bigint(20) unsigned NOT NULL DEFAULT 0,
			quantity int unsigned NOT NULL DEFAULT 1,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY owner_item (owner_type, owner_id, post_id, variation_id),
			KEY post_id (post_id),
			KEY post_type (post_type),
			KEY owner_lookup (owner_type, owner_id)
		) {$charset_collate};";

		dbDelta( $sql );
		update_option( 'wishflow_db_version', WISHFLOW_VERSION );
	}

	/**
	 * Add default settings.
	 *
	 * @return void
	 */
	public static function add_default_options() {
		$settings = get_option( Settings::OPTION_NAME );

		if ( is_array( $settings ) ) {
			update_option( Settings::OPTION_NAME, wp_parse_args( $settings, Settings::defaults() ) );
			return;
		}

		add_option( Settings::OPTION_NAME, Settings::defaults() );
	}

	/**
	 * Create and configure the default wishlist page.
	 *
	 * Reuse an existing Wishlist page when possible and add the shortcode only
	 * when it is missing. This keeps activation idempotent without replacing any
	 * existing page content.
	 *
	 * @return int Wishlist page ID, or 0 when the page could not be created.
	 */
	public static function create_wishlist_page() {
		$settings = get_option( Settings::OPTION_NAME, array() );
		$settings = is_array( $settings ) ? $settings : array();
		$page_id  = 0;
		$existing_page = get_page_by_path( 'wishlist', OBJECT, 'page' );

		if (
			$existing_page instanceof \WP_Post
			&& 'publish' === $existing_page->post_status
		) {
			$page_id = (int) $existing_page->ID;
			$content = self::normalize_wishlist_shortcode( $existing_page->post_content );

			if ( $content !== $existing_page->post_content ) {
				$result = wp_update_post(
					array(
						'ID'           => $page_id,
						'post_content' => $content,
					),
					true
				);

				if ( is_wp_error( $result ) ) {
					return 0;
				}
			}
		} else {
			$page_id = wp_insert_post(
				array(
					'post_title'   => __( 'Wishlist', 'wishflow' ),
					'post_name'    => 'wishlist',
					'post_content' => '[wishlist_page]',
					'post_status'  => 'publish',
					'post_type'    => 'page',
				),
				true
			);

			if ( is_wp_error( $page_id ) ) {
				return 0;
			}
		}

		$settings['wishlist_page_id'] = (int) $page_id;
		update_option( Settings::OPTION_NAME, wp_parse_args( $settings, Settings::defaults() ) );

		return (int) $page_id;
	}

	/**
	 * Ensure page content contains exactly one wishlist shortcode.
	 *
	 * WordPress has not registered plugin shortcodes when an activation hook
	 * runs, so has_shortcode() cannot be used reliably here.
	 *
	 * @param string $content Existing page content.
	 * @return string Normalized page content.
	 */
	private static function normalize_wishlist_shortcode( $content ) {
		$pattern = '/\[wishlist_page(?:\s[^\]]*)?\]/';
		$count   = 0;

		$content = preg_replace_callback(
			$pattern,
			static function ( $matches ) use ( &$count ) {
				++$count;
				return 1 === $count ? $matches[0] : '';
			},
			(string) $content
		);

		$content = trim( preg_replace( "/\n{3,}/", "\n\n", $content ) );

		if ( 0 === $count ) {
			$content = $content ? $content . "\n\n[wishlist_page]" : '[wishlist_page]';
		}

		return $content;
	}
}
