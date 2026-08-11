<?php

/**
 * Wishlist business logic.
 *
 * @package WishFlow
 */

namespace WishFlow;

if (! defined('ABSPATH')) {
	exit;
}

class Wishlist_Manager
{
	private $database;
	private $session;
	private $settings;

	public function __construct(Database $database, Session_Manager $session, Settings $settings)
	{
		$this->database = $database;
		$this->session  = $session;
		$this->settings = $settings;
	}

	public function add($post_id, $variation_id = 0, $quantity = 1)
	{
		$owner = $this->get_valid_owner_and_post($post_id);
		if (is_wp_error($owner)) {
			return $owner;
		}

		if ($this->database->item_exists($owner['type'], $owner['id'], $post_id, $variation_id)) {
			return array('status' => 'exists');
		}

		$this->database->insert_item($owner['type'], $owner['id'], $post_id, $variation_id, $quantity);
		return array('status' => 'added');
	}

	public function remove($post_id, $variation_id = 0)
	{
		$owner = $this->session->get_owner(false);
		if (is_wp_error($owner)) {
			return $owner;
		}

		$this->database->delete_item($owner['type'], $owner['id'], absint($post_id), absint($variation_id));
		return array('status' => 'removed');
	}

	public function toggle($post_id, $variation_id = 0)
	{
		if ($this->is_added($post_id, $variation_id)) {
			return $this->remove($post_id, $variation_id);
		}

		return $this->add($post_id, $variation_id);
	}

	public function is_added($post_id, $variation_id = 0)
	{
		$owner = $this->session->get_owner(false);
		if (is_wp_error($owner)) {
			return false;
		}

		return $this->database->item_exists($owner['type'], $owner['id'], absint($post_id), absint($variation_id));
	}

	public function get_items()
	{
		$owner = $this->session->get_owner(false);
		if (is_wp_error($owner)) {
			return array();
		}

		return $this->database->get_items($owner['type'], $owner['id']);
	}

	public function get_count()
	{
		$owner = $this->session->get_owner(false);
		if (is_wp_error($owner)) {
			return 0;
		}

		return $this->database->get_count($owner['type'], $owner['id']);
	}

	private function get_valid_owner_and_post(&$post_id)
	{
		if (! $this->settings->get_bool('enabled')) {
			return new \WP_Error('disabled', __('Wishlist is disabled.', 'wishflow'));
		}

		$post_id = absint($post_id);
		if (! $post_id || 'publish' !== get_post_status($post_id)) {
			return new \WP_Error('invalid_post', __('Invalid wishlist item.', 'wishflow'));
		}

		$post_type = get_post_type($post_id);
		if (! $this->settings->is_post_type_enabled($post_type)) {
			return new \WP_Error('unsupported_post_type', __('This item type is not supported by wishlist.', 'wishflow'));
		}

		return $this->session->get_owner(true);
	}
}
