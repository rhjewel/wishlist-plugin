<?php

/**
 * AJAX handlers.
 *
 * @package WishFlow
 */

namespace WishFlow;

if (! defined('ABSPATH')) {
	exit;
}

class Ajax
{
	private $wishlist;
	private $settings;

	public function __construct(Wishlist_Manager $wishlist, Settings $settings)
	{
		$this->wishlist = $wishlist;
		$this->settings = $settings;
	}

	public function register()
	{
		add_action('wp_ajax_wishflow_toggle', array($this, 'toggle'));
		add_action('wp_ajax_nopriv_wishflow_toggle', array($this, 'toggle'));
		add_action('wp_ajax_wishflow_remove', array($this, 'remove'));
		add_action('wp_ajax_nopriv_wishflow_remove', array($this, 'remove'));
	}

	public function toggle()
	{
		check_ajax_referer('wishflow_nonce', 'nonce');

		$post_id      = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
		$variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
		$result       = $this->wishlist->toggle($post_id, $variation_id);

		$this->send_response($result, $post_id, $variation_id);
	}

	public function remove()
	{
		check_ajax_referer('wishflow_nonce', 'nonce');

		$post_id      = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
		$variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
		$result       = $this->wishlist->remove($post_id, $variation_id);

		$this->send_response($result, $post_id, $variation_id);
	}

	private function send_response($result, $post_id, $variation_id)
	{
		if (is_wp_error($result)) {
			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
					'code'    => $result->get_error_code(),
				)
			);
		}

		$status  = isset($result['status']) ? $result['status'] : 'updated';
		$message = $this->settings->get('added_message');

		if ('removed' === $status) {
			$message = $this->settings->get('removed_message');
		} elseif ('exists' === $status) {
			$message = $this->settings->get('already_added_message');
		}

		wp_send_json_success(
			array(
				'status'      => $status,
				'isAdded'     => $this->wishlist->is_added($post_id, $variation_id),
				'count'       => $this->wishlist->get_count(),
				'message'     => $message,
				'buttonText'  => $this->settings->get('button_text'),
				'addedText'   => $this->settings->get('added_button_text'),
			)
		);
	}
}
