<?php
/**
 * Asset loading.
 *
 * @package WishFlow
 */

namespace WishFlow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Assets {
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin' ) );
	}

	public function frontend() {
		wp_enqueue_style( 'wishflow', WISHFLOW_URL . 'assets/css/frontend.css', array(), WISHFLOW_VERSION );
		wp_enqueue_script( 'wishflow', WISHFLOW_URL . 'assets/js/frontend.js', array(), WISHFLOW_VERSION, true );

		wp_localize_script(
			'wishflow',
			'wishflowData',
			array(
				'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( 'wishflow_nonce' ),
				'ajaxEnabled'    => $this->settings->get_bool( 'enable_ajax' ),
				'toastEnabled'   => $this->settings->get_bool( 'enable_toast' ),
				'toastPosition'  => esc_attr( $this->settings->get( 'toast_position' ) ),
				'loginUrl'       => wp_login_url( get_permalink() ),
				'buttonText'     => esc_html( $this->settings->get( 'button_text' ) ),
				'addedText'      => esc_html( $this->settings->get( 'added_button_text' ) ),
			)
		);
	}

	public function admin( $hook ) {
		if ( false === strpos( $hook, 'wishflow' ) ) {
			return;
		}

		wp_enqueue_style( 'wishflow-admin', WISHFLOW_URL . 'assets/css/admin.css', array(), WISHFLOW_VERSION );
		wp_enqueue_script( 'wishflow-admin', WISHFLOW_URL . 'assets/js/admin.js', array(), WISHFLOW_VERSION, true );
	}
}
