<?php

/**
 * Settings access.
 *
 * @package WishFlow
 */

namespace WishFlow;

if (! defined('ABSPATH')) {
	exit;
}

class Settings
{
	const OPTION_NAME = 'wishflow_settings';

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	public static function defaults()
	{
		$default_post_types = class_exists('WooCommerce') ? array('product') : array('post');

		return array(
			'enabled'                 => 'yes',
			'enable_guest'            => 'yes',
			'enable_ajax'             => 'yes',
			'wishlist_page_id'        => 0,
			'redirect_guest_login'    => 'no',
			'merge_after_login'       => 'yes',
			'delete_on_uninstall'     => 'no',
			'enabled_post_types'      => $default_post_types,
			'auto_display'            => 'no',
			'auto_display_position'   => 'manual',
			'button_text'             => 'Add to Wishlist',
			'added_button_text'       => 'Added to Wishlist',
			'remove_button_text'      => 'Remove',
			'show_text'               => 'yes',
			'show_icon'               => 'yes',
			'button_css_class'        => '',
			'icon_type'               => 'svg',
			'normal_icon'             => Icons::normal_icon(),
			'added_icon'              => Icons::added_icon(),
			'icon_size'               => 18,
			'icon_color'              => 'inherit',
			'added_icon_color'        => '#e0245e',
			'enable_toast'            => 'yes',
			'added_message'           => 'Item added to wishlist.',
			'removed_message'         => 'Item removed from wishlist.',
			'already_added_message'   => 'Item already exists in wishlist.',
			'toast_position'          => 'bottom-right',
			'empty_message'           => 'Your wishlist is empty.',
			'show_featured_image'     => 'yes',
			'show_title'              => 'yes',
			'show_post_type'          => 'yes',
			'show_date_added'         => 'no',
			'show_remove_button'      => 'yes',
			'show_view_button'        => 'yes',
			'enable_share'            => 'no',
			'enable_wc'               => 'yes',
			'wc_show_price'           => 'yes',
			'wc_show_add_to_cart'     => 'yes',
			'wc_remove_after_cart'    => 'no',
			'wc_single_position'      => 'woocommerce_after_add_to_cart_button',
			'wc_loop_position'        => 'woocommerce_before_shop_loop_item_title',
		);
	}

	/**
	 * Get all settings.
	 *
	 * @return array
	 */
	public function all()
	{
		$options = get_option(self::OPTION_NAME, array());
		return wp_parse_args(is_array($options) ? $options : array(), self::defaults());
	}

	/**
	 * Get a setting.
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Fallback.
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		$options = $this->all();
		return array_key_exists($key, $options) ? $options[$key] : $default;
	}

	/**
	 * Check yes/no setting.
	 *
	 * @param string $key Setting key.
	 * @return bool
	 */
	public function get_bool($key)
	{
		return 'yes' === $this->get($key);
	}

	/**
	 * Enabled post types.
	 *
	 * @return array
	 */
	public function enabled_post_types()
	{
		$post_types = $this->get('enabled_post_types', array());
		$post_types = is_array($post_types) ? $post_types : array();
		return array_values(array_diff(array_map('sanitize_key', $post_types), array('attachment')));
	}

	/**
	 * Is post type enabled.
	 *
	 * @param string $post_type Post type.
	 * @return bool
	 */
	public function is_post_type_enabled($post_type)
	{
		return in_array($post_type, $this->enabled_post_types(), true);
	}
}
