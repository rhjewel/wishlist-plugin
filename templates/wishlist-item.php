<?php
/**
 * Wishlist item template.
 *
 * @package EgnsWishlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id    = (int) $item->post_id;
$post_type  = get_post_type_object( get_post_type( $post_id ) );
$product    = null;
$price_html = '';

if ( class_exists( 'WooCommerce' ) && 'product' === get_post_type( $post_id ) && function_exists( 'wc_get_product' ) ) {
	$product = wc_get_product( $post_id );

	if ( $product && $settings->get_bool( 'wc_show_price' ) ) {
		$price_html = $product->get_price_html();
	}
}

if ( ! $price_html ) {
	$price_methods = array(
		'tour'       => 'get_tour_starting_price',
		'hotel'      => 'get_hotel_starting_price',
		'transport'  => 'get_transport_starting_price',
		'experience' => 'get_exp_starting_price',
	);
	$helper_classes = array( '\Egns\Helper\Egns_Helper', '\Egns_Core\Egns_Helper' );
	$post_type_name = get_post_type( $post_id );
	$price_method   = isset( $price_methods[ $post_type_name ] ) ? $price_methods[ $post_type_name ] : '';

	foreach ( $helper_classes as $helper_class ) {
		if ( $price_method && is_callable( array( $helper_class, $price_method ) ) ) {
			$price_html = call_user_func( array( $helper_class, $price_method ), $post_id );
			break;
		}
	}
}

$price_html = apply_filters( 'egwl_wishlist_item_price_html', $price_html, $post_id, $item );
?>
<article class="egwl-item" data-post-id="<?php echo esc_attr( $post_id ); ?>" role="row">
	<div class="egwl-remove-cell" role="cell">
		<?php if ( $settings->get_bool( 'show_remove_button' ) ) : ?>
			<button type="button" class="egwl-remove-button" data-post-id="<?php echo esc_attr( $post_id ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from wishlist', 'egns-wishlist' ), get_the_title( $post_id ) ) ); ?>">&times;</button>
		<?php endif; ?>
	</div>

	<div class="egwl-product-cell" role="cell">
		<?php if ( $settings->get_bool( 'show_featured_image' ) && has_post_thumbnail( $post_id ) ) : ?>
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
		</div>
	</div>

	<div class="egwl-product-price" role="cell" data-label="<?php esc_attr_e( 'Unit Price', 'egns-wishlist' ); ?>">
		<?php echo $price_html ? wp_kses_post( $price_html ) : '&mdash;'; ?>
	</div>

	<div class="egwl-item-actions" role="cell" data-label="<?php esc_attr_e( 'Actions', 'egns-wishlist' ); ?>">
		<?php if ( $product && $settings->get_bool( 'wc_show_add_to_cart' ) && $product->is_type( 'simple' ) ) : ?>
			<a class="button egwl-cart-button" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-product_id="<?php echo esc_attr( $post_id ); ?>"><?php echo esc_html( $product->add_to_cart_text() ); ?></a>
		<?php endif; ?>

		<?php if ( $settings->get_bool( 'show_view_button' ) ) : ?>
			<a class="button egwl-view-button" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php esc_html_e( 'View', 'egns-wishlist' ); ?></a>
		<?php endif; ?>
	</div>
</article>
