<?php
/**
 * Wishlist page template.
 *
 * @package EgnsWishlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="egwl-page">
	<div class="egwl-panel">
		<h2 class="egwl-page-title"><?php esc_html_e( 'My Wishlist', 'wishflow' ); ?></h2>

		<?php if ( empty( $items ) ) : ?>
			<?php echo \Egns\Wishlist\Template_Loader::render( 'empty-wishlist', array( 'settings' => $settings ) ); ?>
		<?php else : ?>
			<div class="egwl-table" role="table" aria-label="<?php esc_attr_e( 'Wishlist items', 'wishflow' ); ?>">
				<div class="egwl-table-head" role="row">
					<span aria-hidden="true"></span>
					<span role="columnheader"><?php esc_html_e( 'Item Name', 'wishflow' ); ?></span>
					<span role="columnheader"><?php esc_html_e( 'Unit Price', 'wishflow' ); ?></span>
					<span role="columnheader"><?php esc_html_e( 'Actions', 'wishflow' ); ?></span>
				</div>

				<div class="egwl-items" role="rowgroup">
					<?php foreach ( $items as $item ) : ?>
						<?php
						if ( 'publish' !== get_post_status( (int) $item->post_id ) ) {
							continue;
						}

						echo \Egns\Wishlist\Template_Loader::render(
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
