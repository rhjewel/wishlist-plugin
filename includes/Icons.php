<?php
/**
 * Icon helpers.
 *
 * @package WishFlow
 */

namespace WishFlow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Icons {
	/**
	 * Default outline heart.
	 *
	 * @return string
	 */
	public static function normal_icon() {
		return '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M12.1 21.35l-1.1-1C5.4 15.24 2 12.16 2 8.38 2 5.3 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.08C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.3 22 8.38c0 3.78-3.4 6.86-9 11.97l-.9 1z" fill="none" stroke="currentColor" stroke-width="2"/></svg>';
	}

	/**
	 * Default filled heart.
	 *
	 * @return string
	 */
	public static function added_icon() {
		return '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M12.1 21.35l-1.1-1C5.4 15.24 2 12.16 2 8.38 2 5.3 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.08C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.3 22 8.38c0 3.78-3.4 6.86-9 11.97l-.9 1z" fill="currentColor"/></svg>';
	}

	/**
	 * Strict SVG tags for wp_kses.
	 *
	 * @return array
	 */
	public static function allowed_svg_tags() {
		return array(
			'svg'      => array(
				'viewbox'       => true,
				'viewBox'       => true,
				'width'         => true,
				'height'        => true,
				'aria-hidden'   => true,
				'focusable'     => true,
				'role'          => true,
				'class'         => true,
				'xmlns'         => true,
				'fill'          => true,
				'stroke'        => true,
				'stroke-width'  => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
			),
			'path'     => array(
				'd'               => true,
				'fill'            => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'class'           => true,
			),
			'circle'   => array(
				'cx' => true,
				'cy' => true,
				'r'  => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
			),
			'rect'     => array(
				'x' => true,
				'y' => true,
				'width' => true,
				'height' => true,
				'rx' => true,
				'fill' => true,
				'stroke' => true,
			),
			'polygon'  => array(
				'points' => true,
				'fill' => true,
				'stroke' => true,
			),
			'polyline' => array(
				'points' => true,
				'fill' => true,
				'stroke' => true,
			),
			'g'        => array(
				'fill' => true,
				'stroke' => true,
				'class' => true,
			),
		);
	}
}

