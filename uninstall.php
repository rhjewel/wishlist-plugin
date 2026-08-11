<?php

/**
 * Uninstall cleanup.
 *
 * @package WishFlow
 */

if (! defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

$settings = get_option('wishflow_settings', array());

if (is_array($settings) && isset($settings['delete_on_uninstall']) && 'yes' === $settings['delete_on_uninstall']) {
	global $wpdb;

	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wishflow_items");
	delete_option('wishflow_settings');
	delete_option('wishflow_db_version');
}
