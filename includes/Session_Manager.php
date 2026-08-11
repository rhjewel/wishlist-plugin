<?php
/**
 * Guest session handling.
 *
 * @package EgnsWishlist
 */

namespace Egns\Wishlist;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Session_Manager {
	const COOKIE_NAME = 'egwl_session_id';

	/**
	 * Settings.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Constructor.
	 *
	 * @param Settings $settings Settings.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Get current owner.
	 *
	 * @param bool $create_guest Create a guest session when missing.
	 * @return array|\WP_Error
	 */
	public function get_owner( $create_guest = true ) {
		if ( is_user_logged_in() ) {
			return array(
				'type' => 'user',
				'id'   => (string) get_current_user_id(),
			);
		}

		if ( ! $this->settings->get_bool( 'enable_guest' ) ) {
			return new \WP_Error( 'guest_disabled', __( 'Guest wishlist is disabled.', 'wishflow' ) );
		}

		if ( $this->settings->get_bool( 'redirect_guest_login' ) ) {
			return new \WP_Error( 'login_required', __( 'Please log in to use the wishlist.', 'wishflow' ) );
		}

		$session_id = $this->get_session_id( $create_guest );

		if ( ! $session_id ) {
			return new \WP_Error( 'missing_session', __( 'Could not create wishlist session.', 'wishflow' ) );
		}

		return array(
			'type' => 'guest',
			'id'   => $session_id,
		);
	}

	/**
	 * Get or create guest session ID.
	 *
	 * @param bool $create Create if missing.
	 * @return string
	 */
	public function get_session_id( $create = true ) {
		$session_id = isset( $_COOKIE[ self::COOKIE_NAME ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ) ) : '';

		if ( $session_id ) {
			return $session_id;
		}

		if ( ! $create || headers_sent() ) {
			return '';
		}

		$session_id = wp_hash( wp_generate_uuid4() . microtime() );
		$session_id = substr( preg_replace( '/[^a-zA-Z0-9]/', '', $session_id ), 0, 64 );

		setcookie( self::COOKIE_NAME, $session_id, time() + YEAR_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
		$_COOKIE[ self::COOKIE_NAME ] = $session_id;

		return $session_id;
	}

	/**
	 * Clear guest session.
	 *
	 * @return void
	 */
	public function clear_session() {
		if ( headers_sent() ) {
			return;
		}

		setcookie( self::COOKIE_NAME, '', time() - HOUR_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
		unset( $_COOKIE[ self::COOKIE_NAME ] );
	}
}

