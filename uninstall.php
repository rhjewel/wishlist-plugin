<?php
/**
 * Uninstall cleanup.
 *
 * @package EgnsWishlist
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option( 'egwl_settings', array() );

if ( is_array( $settings ) && isset( $settings['delete_on_uninstall'] ) && 'yes' === $settings['delete_on_uninstall'] ) {
	global $wpdb;

	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}egwl_items" );
	delete_option( 'egwl_settings' );
	delete_option( 'egwl_db_version' );
}

