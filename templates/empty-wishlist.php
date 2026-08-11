<?php

/**
 * Empty wishlist template.
 *
 * @package WishFlow
 */

if (! defined('ABSPATH')) {
	exit;
}
?>
<p class="wishflow-empty"><?php echo esc_html($settings->get('empty_message')); ?></p>