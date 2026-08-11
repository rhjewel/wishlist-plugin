<?php

/**
 * Plugin Name: WishFlow
 * Plugin URI: https://www.egenslab.com/
 * Description: WishFlow is a powerful WordPress wishlist plugin for WooCommerce products, posts, and custom post types, making it easy for users to save and manage their favorite content.
 * Version: 1.0.0
 * Author: Egens Lab
 * Author URI: https://www.egenslab.com/
 * Text Domain: wishflow
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 *
 * @package WishFlow
 */

if (! defined('ABSPATH')) {
	exit;
}

define('WISHFLOW_VERSION', '1.0.0');
define('WISHFLOW_FILE', __FILE__);
define('WISHFLOW_PATH', plugin_dir_path(__FILE__));
define('WISHFLOW_URL', plugin_dir_url(__FILE__));
define('WISHFLOW_BASENAME', plugin_basename(__FILE__));

spl_autoload_register(
	static function ($class) {
		$prefix = 'WishFlow\\';

		if (0 !== strpos($class, $prefix)) {
			return;
		}

		$relative = str_replace($prefix, '', $class);
		$file     = WISHFLOW_PATH . 'includes/' . str_replace('\\', '/', $relative) . '.php';

		if (file_exists($file)) {
			require_once $file;
		}
	}
);

register_activation_hook(__FILE__, array('\WishFlow\Activator', 'activate'));
register_deactivation_hook(__FILE__, array('\WishFlow\Deactivator', 'deactivate'));

add_action(
	'init',
	static function () {
		load_plugin_textdomain('wishflow', false, dirname(WISHFLOW_BASENAME) . '/languages');
		\WishFlow\Plugin::instance()->init();
	}
);
