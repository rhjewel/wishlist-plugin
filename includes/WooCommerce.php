<?php
/**
 * WooCommerce integrations.
 *
 * @package EgnsWishlist
 */

namespace Egns\Wishlist;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooCommerce {
	private $wishlist;
	private $settings;

	public function __construct( Wishlist_Manager $wishlist, Settings $settings ) {
		$this->wishlist = $wishlist;
		$this->settings = $settings;
	}

	public function register() {
		if ( ! class_exists( 'WooCommerce' ) || ! $this->settings->get_bool( 'enable_wc' ) ) {
			return;
		}

		$single_hook = $this->settings->get( 'wc_single_position' );
		$loop_hook   = $this->settings->get( 'wc_loop_position' );

		if ( $single_hook && function_exists( 'add_action' ) ) {
			add_action( $single_hook, array( $this, 'product_button' ), 25 );
		}

		if ( $loop_hook ) {
			add_action( $loop_hook, array( $this, 'product_button' ), 25 );
		}

		if ( $this->settings->get_bool( 'wc_remove_after_cart' ) ) {
			add_action( 'woocommerce_add_to_cart', array( $this, 'remove_after_add_to_cart' ), 10, 6 );
		}
	}

	public function product_button() {
		$post_id = get_the_ID();
		if ( ! $post_id || 'product' !== get_post_type( $post_id ) || ! $this->settings->is_post_type_enabled( 'product' ) ) {
			return;
		}

		echo Template_Loader::render(
			'wishlist-button',
			array(
				'post_id'     => $post_id,
				'is_added'    => $this->wishlist->is_added( $post_id ),
				'settings'    => $this->settings,
				'show_text'   => $this->settings->get_bool( 'show_text' ),
				'show_icon'   => $this->settings->get_bool( 'show_icon' ),
				'extra_class' => 'egwl-wc-button',
			)
		);
	}

	/**
	 * Remove a product from wishlist after it is added to cart.
	 *
	 * @param string $cart_item_key Cart item key.
	 * @param int    $product_id    Product ID.
	 * @param int    $quantity      Quantity.
	 * @param int    $variation_id  Variation ID.
	 * @param array  $variation     Variation data.
	 * @param array  $cart_item     Cart item data.
	 * @return void
	 */
	public function remove_after_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item ) {
		unset( $cart_item_key, $quantity, $variation, $cart_item );
		$this->wishlist->remove( absint( $product_id ), absint( $variation_id ) );
	}
}
