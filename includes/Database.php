<?php
/**
 * Wishlist database operations.
 *
 * @package WishFlow
 */

namespace WishFlow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Database {
	/**
	 * Get table name.
	 *
	 * @return string
	 */
	public function get_table_name() {
		global $wpdb;
		return $wpdb->prefix . 'wishflow_items';
	}

	/**
	 * Insert item.
	 *
	 * @param string $owner_type   Owner type.
	 * @param string $owner_id     Owner ID.
	 * @param int    $post_id      Post ID.
	 * @param int    $variation_id Variation ID.
	 * @param int    $quantity     Quantity.
	 * @return bool
	 */
	public function insert_item( $owner_type, $owner_id, $post_id, $variation_id = 0, $quantity = 1 ) {
		global $wpdb;

		$post_type = get_post_type( $post_id );
		if ( ! $post_type ) {
			return false;
		}

		$inserted = $wpdb->query(
			$wpdb->prepare(
				"INSERT IGNORE INTO {$this->get_table_name()} (owner_type, owner_id, post_id, post_type, variation_id, quantity, created_at) VALUES (%s, %s, %d, %s, %d, %d, %s)",
				$owner_type,
				$owner_id,
				$post_id,
				$post_type,
				$variation_id,
				max( 1, $quantity ),
				current_time( 'mysql' )
			)
		);

		return (bool) $inserted;
	}

	/**
	 * Delete item.
	 *
	 * @param string $owner_type   Owner type.
	 * @param string $owner_id     Owner ID.
	 * @param int    $post_id      Post ID.
	 * @param int    $variation_id Variation ID.
	 * @return bool
	 */
	public function delete_item( $owner_type, $owner_id, $post_id, $variation_id = 0 ) {
		global $wpdb;

		return (bool) $wpdb->delete(
			$this->get_table_name(),
			array(
				'owner_type'   => $owner_type,
				'owner_id'     => $owner_id,
				'post_id'      => $post_id,
				'variation_id' => $variation_id,
			),
			array( '%s', '%s', '%d', '%d' )
		);
	}

	/**
	 * Check item.
	 *
	 * @param string $owner_type   Owner type.
	 * @param string $owner_id     Owner ID.
	 * @param int    $post_id      Post ID.
	 * @param int    $variation_id Variation ID.
	 * @return bool
	 */
	public function item_exists( $owner_type, $owner_id, $post_id, $variation_id = 0 ) {
		global $wpdb;

		return (bool) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$this->get_table_name()} WHERE owner_type = %s AND owner_id = %s AND post_id = %d AND variation_id = %d LIMIT 1",
				$owner_type,
				$owner_id,
				$post_id,
				$variation_id
			)
		);
	}

	/**
	 * Get items.
	 *
	 * @param string $owner_type Owner type.
	 * @param string $owner_id   Owner ID.
	 * @return array
	 */
	public function get_items( $owner_type, $owner_id ) {
		global $wpdb;

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->get_table_name()} WHERE owner_type = %s AND owner_id = %s ORDER BY created_at DESC",
				$owner_type,
				$owner_id
			)
		);
	}

	/**
	 * Get count.
	 *
	 * @param string $owner_type Owner type.
	 * @param string $owner_id   Owner ID.
	 * @return int
	 */
	public function get_count( $owner_type, $owner_id ) {
		global $wpdb;

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->get_table_name()} WHERE owner_type = %s AND owner_id = %s",
				$owner_type,
				$owner_id
			)
		);
	}

	/**
	 * Merge guest owner into user owner.
	 *
	 * @param string $guest_id Guest session ID.
	 * @param int    $user_id  User ID.
	 * @return void
	 */
	public function merge_guest_to_user( $guest_id, $user_id ) {
		$items = $this->get_items( 'guest', $guest_id );

		foreach ( $items as $item ) {
			$this->insert_item( 'user', (string) $user_id, (int) $item->post_id, (int) $item->variation_id, (int) $item->quantity );
			$this->delete_item( 'guest', $guest_id, (int) $item->post_id, (int) $item->variation_id );
		}
	}
}

