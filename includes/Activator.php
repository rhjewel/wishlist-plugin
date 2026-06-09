<?php
/**
 * Activation tasks.
 *
 * @package EgnsWishlist
 */

namespace Egns\Wishlist;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activator {
	/**
	 * Activate plugin.
	 *
	 * @return void
	 */
	public static function activate() {
		Installer::create_tables();
		Installer::add_default_options();
	}
}

