<?php
/**
 * Wishlist item template.
 *
 * @package EgnsWishlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id   = (int) $item->post_id;
$post_type = get_post_type_object( get_post_type( $post_id ) );
$product   = null;

if ( class_exists( 'WooCommerce' ) && 'product' === get_post_type( $post_id ) && function_exists( 'wc_get_product' ) ) {
	$product = wc_get_product( $post_id );
}
?>
<article class="egwl-item" data-post-id="<?php echo esc_attr( $post_id ); ?>">
	<?php if ( $settings->get_bool( 'show_featured_image' ) ) : ?>
		<a class="egwl-item-image" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
			<?php echo get_the_post_thumbnail( $post_id, 'thumbnail' ); ?>
		</a>
	<?php endif; ?>

	<div class="egwl-item-content">
		<?php if ( $settings->get_bool( 'show_title' ) ) : ?>
			<h3 class="egwl-item-title"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
		<?php endif; ?>

		<div class="egwl-item-meta">
			<?php if ( $settings->get_bool( 'show_post_type' ) && $post_type ) : ?>
				<span><?php echo esc_html( $post_type->labels->singular_name ); ?></span>
			<?php endif; ?>
			<?php if ( $settings->get_bool( 'show_date_added' ) ) : ?>
				<span><?php echo esc_html( mysql2date( get_option( 'date_format' ), $item->created_at ) ); ?></span>
			<?php endif; ?>
		</div>

		<?php if ( $product && $settings->get_bool( 'wc_show_price' ) ) : ?>
			<div class="egwl-product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
		<?php endif; ?>

		<?php if ( $product && $settings->get_bool( 'wc_show_stock' ) ) : ?>
			<div class="egwl-product-stock"><?php echo wp_kses_post( wc_get_stock_html( $product ) ); ?></div>
		<?php endif; ?>
	</div>

	<div class="egwl-item-actions">
		<?php if ( $product && $settings->get_bool( 'wc_show_add_to_cart' ) && $product->is_type( 'simple' ) ) : ?>
			<a class="button egwl-cart-button" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-product_id="<?php echo esc_attr( $post_id ); ?>"><?php echo esc_html( $product->add_to_cart_text() ); ?></a>
		<?php endif; ?>

		<?php if ( $settings->get_bool( 'show_view_button' ) ) : ?>
			<a class="button egwl-view-button" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php esc_html_e( 'View', 'egns-wishlist' ); ?></a>
		<?php endif; ?>

		<?php if ( $settings->get_bool( 'show_remove_button' ) ) : ?>
			<button type="button" class="button egwl-remove-button" data-post-id="<?php echo esc_attr( $post_id ); ?>"><?php echo esc_html( $settings->get( 'remove_button_text' ) ); ?></button>
		<?php endif; ?>
	</div>
</article>
