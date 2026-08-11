<?php
/**
 * Shortcodes.
 *
 * @package EgnsWishlist
 */

namespace Egns\Wishlist;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes {
	private $wishlist;
	private $settings;

	public function __construct( Wishlist_Manager $wishlist, Settings $settings ) {
		$this->wishlist = $wishlist;
		$this->settings = $settings;
	}

	public function register() {
		add_shortcode( 'wishlist_button', array( $this, 'button' ) );
		add_shortcode( 'wishlist_page', array( $this, 'page' ) );
		add_shortcode( 'my_wishlist', array( $this, 'page' ) );
		add_shortcode( 'wishlist_count', array( $this, 'count' ) );
		add_shortcode( 'wishlist_link', array( $this, 'link' ) );
	}

	public function button( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'id'        => get_the_ID(),
				'show_text' => $this->settings->get( 'show_text' ),
				'show_icon' => $this->settings->get( 'show_icon' ),
				'class'     => '',
			),
			$atts,
			'wishlist_button'
		);

		$post_id = absint( $atts['id'] );
		if ( ! $post_id || ! $this->settings->is_post_type_enabled( get_post_type( $post_id ) ) ) {
			return '';
		}

		return Template_Loader::render(
			'wishlist-button',
			array(
				'post_id'    => $post_id,
				'is_added'   => $this->wishlist->is_added( $post_id ),
				'settings'   => $this->settings,
				'show_text'  => 'yes' === $atts['show_text'],
				'show_icon'  => 'yes' === $atts['show_icon'],
				'extra_class' => sanitize_html_class( $atts['class'] ),
			)
		);
	}

	public function page() {
		return Template_Loader::render(
			'wishlist-page',
			array(
				'items'    => $this->wishlist->get_items(),
				'settings' => $this->settings,
			)
		);
	}

	public function count() {
		return '<span class="egwl-count">' . esc_html( (string) $this->wishlist->get_count() ) . '</span>';
	}

	public function link( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'text' => __( 'Wishlist', 'wishflow' ),
			),
			$atts,
			'wishlist_link'
		);

		$page_id = absint( $this->settings->get( 'wishlist_page_id' ) );
		$url     = $page_id ? get_permalink( $page_id ) : home_url( '/' );

		return sprintf(
			'<a href="%1$s" class="egwl-link">%2$s <span>%3$s</span></a>',
			esc_url( $url ),
			esc_html( $atts['text'] ),
			esc_html( (string) $this->wishlist->get_count() )
		);
	}
}

