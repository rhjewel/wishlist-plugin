<?php
/**
 * Wishlist item template.
 *
 * @package WishFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WishFlow\Wishflow_core;

$post_id    = (int) $item->post_id;
$post_type  = get_post_type_object( get_post_type( $post_id ) );
$product    = Wishflow_core::get_product( $post_id );
$price_html = Wishflow_core::get_item_price_html( $post_id, $settings, $item );
?>
<article class="wishflow-item" data-post-id="<?php echo esc_attr( $post_id ); ?>" role="row">
	<div class="wishflow-remove-cell" role="cell">
		<?php if ( $settings->get_bool( 'show_remove_button' ) ) : ?>
			<button type="button" class="wishflow-remove-button" data-post-id="<?php echo esc_attr( $post_id ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from wishlist', 'wishflow' ), get_the_title( $post_id ) ) ); ?>">&times;</button>
		<?php endif; ?>
	</div>

	<div class="wishflow-product-cell" role="cell">
		<?php if ( $settings->get_bool( 'show_featured_image' ) && has_post_thumbnail( $post_id ) ) : ?>
			<a class="wishflow-item-image" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
				<?php echo get_the_post_thumbnail( $post_id, 'thumbnail' ); ?>
			</a>
		<?php endif; ?>

		<div class="wishflow-item-content">
			<?php if ( $settings->get_bool( 'show_title' ) ) : ?>
				<h3 class="wishflow-item-title"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
			<?php endif; ?>

			<div class="wishflow-item-meta">
				<?php if ( $settings->get_bool( 'show_post_type' ) && $post_type ) : ?>
					<span><?php echo esc_html( $post_type->labels->singular_name ); ?></span>
				<?php endif; ?>
				<?php if ( $settings->get_bool( 'show_date_added' ) ) : ?>
					<span><?php echo esc_html( mysql2date( get_option( 'date_format' ), $item->created_at ) ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="wishflow-product-price" role="cell" data-label="<?php esc_attr_e( 'Unit Price', 'wishflow' ); ?>">
		<?php echo $price_html ? wp_kses_post( $price_html ) : '&mdash;'; ?>
	</div>

	<div class="wishflow-item-actions" role="cell" data-label="<?php esc_attr_e( 'Actions', 'wishflow' ); ?>">
		<?php if ( $product && $settings->get_bool( 'wc_show_add_to_cart' ) && $product->is_type( 'simple' ) ) : ?>
			<a class="button wishflow-cart-button" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-product_id="<?php echo esc_attr( $post_id ); ?>"><?php echo esc_html( $product->add_to_cart_text() ); ?></a>
		<?php endif; ?>

		<?php if ( $settings->get_bool( 'show_view_button' ) ) : ?>
			<a class="button wishflow-view-button" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php esc_html_e( 'View', 'wishflow' ); ?></a>
		<?php endif; ?>
	</div>
</article>
