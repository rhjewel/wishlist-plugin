<?php
/**
 * Main plugin bootstrap.
 *
 * @package WishFlow
 */

namespace WishFlow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {
	/**
	 * Plugin singleton.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Settings instance.
	 *
	 * @var Settings
	 */
	public $settings;

	/**
	 * Database instance.
	 *
	 * @var Database
	 */
	public $database;

	/**
	 * Session instance.
	 *
	 * @var Session_Manager
	 */
	public $session;

	/**
	 * Wishlist manager instance.
	 *
	 * @var Wishlist_Manager
	 */
	public $wishlist;

	/**
	 * Get singleton.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Bootstrap plugin services.
	 *
	 * @return void
	 */
	public function init() {
		$this->settings = new Settings();
		$this->database = new Database();
		$this->session  = new Session_Manager( $this->settings );
		$this->wishlist = new Wishlist_Manager( $this->database, $this->session, $this->settings );

		( new Assets( $this->settings ) )->register();
		( new Shortcodes( $this->wishlist, $this->settings ) )->register();
		( new Ajax( $this->wishlist, $this->settings ) )->register();
		( new Admin( $this->settings ) )->register();
		( new Hooks( $this->wishlist, $this->settings ) )->register();
		( new WooCommerce( $this->wishlist, $this->settings ) )->register();

		add_action( 'wp_login', array( $this, 'merge_guest_wishlist' ), 10, 2 );
	}

	/**
	 * Merge guest wishlist after login.
	 *
	 * @param string   $user_login User login.
	 * @param \WP_User $user       User object.
	 * @return void
	 */
	public function merge_guest_wishlist( $user_login, $user ) {
		unset( $user_login );

		if ( ! $this->settings->get_bool( 'merge_after_login' ) ) {
			return;
		}

		$guest_id = $this->session->get_session_id( false );
		if ( $guest_id && $user instanceof \WP_User ) {
			$this->database->merge_guest_to_user( $guest_id, (int) $user->ID );
			$this->session->clear_session();
		}
	}
}

