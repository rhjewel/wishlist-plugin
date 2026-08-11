<?php
/**
 * Frontend display hooks.
 *
 * @package WishFlow
 */

namespace WishFlow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hooks {
	private $wishlist;
	private $settings;

	public function __construct( Wishlist_Manager $wishlist, Settings $settings ) {
		$this->wishlist = $wishlist;
		$this->settings = $settings;
	}

	public function register() {
		if ( ! $this->settings->get_bool( 'auto_display' ) ) {
			return;
		}

		$position = $this->settings->get( 'auto_display_position' );

		if ( in_array( $position, array( 'before_content', 'after_content' ), true ) ) {
			add_filter( 'the_content', array( $this, 'content_button' ) );
		}

		if ( 'after_title' === $position ) {
			add_filter( 'the_title', array( $this, 'title_button' ), 10, 2 );
		}
	}

	public function content_button( $content ) {
		if ( ! is_singular() || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		$button = $this->render_current_button();
		if ( ! $button ) {
			return $content;
		}

		return 'before_content' === $this->settings->get( 'auto_display_position' ) ? $button . $content : $content . $button;
	}

	public function title_button( $title, $post_id ) {
		if ( ! is_singular() || ! in_the_loop() || ! is_main_query() || get_the_ID() !== (int) $post_id ) {
			return $title;
		}

		return $title . $this->render_current_button();
	}

	private function render_current_button() {
		$post_id = get_the_ID();
		if ( ! $post_id || ! $this->settings->is_post_type_enabled( get_post_type( $post_id ) ) ) {
			return '';
		}

		return Template_Loader::render(
			'wishlist-button',
			array(
				'post_id'     => $post_id,
				'is_added'    => $this->wishlist->is_added( $post_id ),
				'settings'    => $this->settings,
				'show_text'   => $this->settings->get_bool( 'show_text' ),
				'show_icon'   => $this->settings->get_bool( 'show_icon' ),
				'extra_class' => '',
			)
		);
	}
}

