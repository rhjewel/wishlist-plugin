<?php
/**
 * Wishlist button template.
 *
 * @package WishFlow
 */

use WishFlow\Icons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$normal_icon = $settings->get( 'normal_icon' );
$added_icon  = $settings->get( 'added_icon' );
$classes     = trim( 'wishflow-button ' . ( $is_added ? 'is-added ' : '' ) . $settings->get( 'button_css_class' ) . ' ' . $extra_class );
$icon_size   = absint( $settings->get( 'icon_size' ) );
$style       = '--wishflow-icon-size:' . $icon_size . 'px;--wishflow-icon-color:' . esc_attr( $settings->get( 'icon_color' ) ) . ';--wishflow-added-icon-color:' . esc_attr( $settings->get( 'added_icon_color' ) ) . ';';
?>
<button
	type="button"
	class="<?php echo esc_attr( $classes ); ?>"
	data-post-id="<?php echo esc_attr( $post_id ); ?>"
	data-post-type="<?php echo esc_attr( get_post_type( $post_id ) ); ?>"
	aria-pressed="<?php echo $is_added ? 'true' : 'false'; ?>"
	style="<?php echo esc_attr( $style ); ?>"
>
	<?php if ( $show_icon ) : ?>
		<span class="wishflow-icon wishflow-icon-normal" aria-hidden="true">
			<?php echo wp_kses( $normal_icon, Icons::allowed_svg_tags() ); ?>
		</span>
		<span class="wishflow-icon wishflow-icon-added" aria-hidden="true">
			<?php echo wp_kses( $added_icon, Icons::allowed_svg_tags() ); ?>
		</span>
	<?php endif; ?>

	<?php if ( $show_text ) : ?>
		<span class="wishflow-text"><?php echo esc_html( $is_added ? $settings->get( 'added_button_text' ) : $settings->get( 'button_text' ) ); ?></span>
	<?php endif; ?>
</button>

