<?php
/**
 * Empty wishlist template.
 *
 * @package EgnsWishlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p class="egwl-empty"><?php echo esc_html( $settings->get( 'empty_message' ) ); ?></p>

