<?php
/**
 * Installer helpers.
 *
 * @package EgnsWishlist
 */

namespace Egns\Wishlist;

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

		$table_name      = $wpdb->prefix . 'egwl_items';
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
		update_option( 'egwl_db_version', EGWL_VERSION );
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
}

