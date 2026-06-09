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
	<?php if ( empty( $items ) ) : ?>
		<?php echo \Egns\Wishlist\Template_Loader::render( 'empty-wishlist', array( 'settings' => $settings ) ); ?>
	<?php else : ?>
		<div class="egwl-items">
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
	<?php endif; ?>
</div>

