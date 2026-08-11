<?php

/**
 * Optional integration compatibility helpers.
 *
 * @package WishFlow
 */

namespace WishFlow;

if (! defined('ABSPATH')) {
	exit;
}

final class Wishflow_core
{
	/**
	 * Get a WooCommerce product only when WooCommerce is available.
	 *
	 * @param int $post_id Post ID.
	 * @return \WC_Product|null
	 */
	public static function get_product($post_id)
	{
		if (
			! class_exists('WooCommerce')
			|| ! function_exists('wc_get_product')
			|| 'product' !== get_post_type($post_id)
		) {
			return null;
		}

		$product = wc_get_product(absint($post_id));
		return $product instanceof \WC_Product ? $product : null;
	}

	/**
	 * Get item price HTML from an available integration.
	 *
	 * Theme-specific classes are checked before use, so WishFlow remains safe
	 * with any WordPress theme.
	 *
	 * @param int      $post_id Post ID.
	 * @param Settings $settings Plugin settings.
	 * @param object   $item Wishlist database row.
	 * @return string
	 */
	public static function get_item_price_html($post_id, Settings $settings, $item)
	{
		$price_html = '';
		$product    = self::get_product($post_id);

		if ($product && $settings->get_bool('wc_show_price')) {
			$price_html = $product->get_price_html();
		}

		if (! $price_html) {
			$price_html = self::get_theme_price_html($post_id);
		}

		return (string) apply_filters('wishflow_wishlist_item_price_html', $price_html, $post_id, $item);
	}

	/**
	 * Get price HTML from a supported theme helper when present.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private static function get_theme_price_html($post_id)
	{
		$price_methods = array(
			'tour'       => 'get_tour_starting_price',
			'hotel'      => 'get_hotel_starting_price',
			'transport'  => 'get_transport_starting_price',
			'experience' => 'get_exp_starting_price',
		);
		$post_type     = get_post_type($post_id);
		$price_method  = isset($price_methods[$post_type]) ? $price_methods[$post_type] : '';

		if (! $price_method) {
			return '';
		}

		$helper_classes = apply_filters(
			'wishflow_price_helper_classes',
			array('\Egns\Helper\Egns_Helper', '\Egns_Core\Egns_Helper'),
			$post_id
		);
		$helper_classes = is_array($helper_classes) ? $helper_classes : array();

		foreach ($helper_classes as $helper_class) {
			if (! is_string($helper_class) || ! class_exists($helper_class)) {
				continue;
			}

			if (is_callable(array($helper_class, $price_method))) {
				return (string) call_user_func(array($helper_class, $price_method), $post_id);
			}
		}

		return '';
	}
}
