<?php

/**
 * Template loading.
 *
 * @package WishFlow
 */

namespace WishFlow;

if (! defined('ABSPATH')) {
	exit;
}

class Template_Loader
{
	/**
	 * Render template.
	 *
	 * @param string $template Template name.
	 * @param array  $args     Template args.
	 * @return string
	 */
	public static function render($template, $args = array())
	{
		$file = WISHFLOW_PATH . 'templates/' . sanitize_file_name($template) . '.php';

		if (! file_exists($file)) {
			return '';
		}

		ob_start();
		extract($args, EXTR_SKIP);
		include $file;
		return ob_get_clean();
	}
}
