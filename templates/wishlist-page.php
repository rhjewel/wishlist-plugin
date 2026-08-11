<?php

/**
 * Wishlist page template.
 *
 * @package WishFlow
 */

if (! defined('ABSPATH')) {
	exit;
}
?>
<div class="wishflow-page">
	<div class="wishflow-panel">
		<h2 class="wishflow-page-title"><?php esc_html_e('My Wishlist', 'wishflow'); ?></h2>

		<?php if (empty($items)) : ?>
			<?php echo \WishFlow\Template_Loader::render('empty-wishlist', array('settings' => $settings)); ?>
		<?php else : ?>
			<div class="wishflow-table" role="table" aria-label="<?php esc_attr_e('Wishlist items', 'wishflow'); ?>">
				<div class="wishflow-table-head" role="row">
					<span aria-hidden="true"></span>
					<span role="columnheader"><?php esc_html_e('Item Name', 'wishflow'); ?></span>
					<span role="columnheader"><?php esc_html_e('Unit Price', 'wishflow'); ?></span>
					<span role="columnheader"><?php esc_html_e('Actions', 'wishflow'); ?></span>
				</div>

				<div class="wishflow-items" role="rowgroup">
					<?php foreach ($items as $item) : ?>
						<?php
						if ('publish' !== get_post_status((int) $item->post_id)) {
							continue;
						}

						echo \WishFlow\Template_Loader::render(
							'wishlist-item',
							array(
								'item'     => $item,
								'settings' => $settings,
							)
						);
						?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>