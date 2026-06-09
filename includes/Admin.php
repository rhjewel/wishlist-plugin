<?php
/**
 * Admin settings pages.
 *
 * @package EgnsWishlist
 */

namespace Egns\Wishlist;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin {
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function register() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function menu() {
		add_menu_page(
			__( 'Egns-wishlist', 'egns-wishlist' ),
			__( 'Egns-wishlist', 'egns-wishlist' ),
			'manage_options',
			'egns-wishlist',
			array( $this, 'render' ),
			'dashicons-heart',
			56
		);

		foreach ( $this->sections() as $slug => $section ) {
			add_submenu_page(
				'egns-wishlist',
				$section['title'],
				$section['title'],
				'manage_options',
				'egns-wishlist' === $slug ? 'egns-wishlist' : 'egns-wishlist-' . $slug,
				array( $this, 'render' )
			);
		}
	}

	public function register_settings() {
		register_setting(
			'egwl_settings_group',
			Settings::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => Settings::defaults(),
			)
		);
	}

	public function sanitize( $input ) {
		$defaults = Settings::defaults();
		$current  = get_option( Settings::OPTION_NAME, array() );
		$current  = wp_parse_args( is_array( $current ) ? $current : array(), $defaults );
		$input    = is_array( $input ) ? $input : array();
		$output   = $current;
		$section  = isset( $_POST['egwl_section'] ) ? sanitize_key( wp_unslash( $_POST['egwl_section'] ) ) : 'egns-wishlist';
		$fields   = $this->section_fields( $section );

		foreach ( $fields['checkboxes'] as $key ) {
			$output[ $key ] = isset( $input[ $key ] ) ? 'yes' : 'no';
		}

		if ( in_array( 'wishlist_page_id', $fields['fields'], true ) ) {
			$output['wishlist_page_id'] = isset( $input['wishlist_page_id'] ) ? absint( $input['wishlist_page_id'] ) : 0;
		}

		if ( in_array( 'enabled_post_types', $fields['fields'], true ) ) {
			$output['enabled_post_types'] = isset( $input['enabled_post_types'] ) && is_array( $input['enabled_post_types'] ) ? array_map( 'sanitize_key', $input['enabled_post_types'] ) : array();
			$output['enabled_post_types'] = array_values( array_diff( $output['enabled_post_types'], array( 'attachment' ) ) );
		}

		$select_keys = array( 'auto_display_position', 'icon_type', 'toast_position', 'wc_single_position', 'wc_loop_position' );
		foreach ( $select_keys as $key ) {
			if ( in_array( $key, $fields['fields'], true ) ) {
				$output[ $key ] = isset( $input[ $key ] ) ? sanitize_key( $input[ $key ] ) : $defaults[ $key ];
			}
		}

		if ( in_array( 'icon_size', $fields['fields'], true ) ) {
			$output['icon_size'] = isset( $input['icon_size'] ) ? max( 8, absint( $input['icon_size'] ) ) : 18;
		}

		$text_keys = array(
			'button_text',
			'added_button_text',
			'remove_button_text',
			'button_css_class',
			'icon_color',
			'added_icon_color',
			'added_message',
			'removed_message',
			'already_added_message',
		);

		foreach ( $text_keys as $key ) {
			if ( in_array( $key, $fields['fields'], true ) ) {
				$output[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : $defaults[ $key ];
			}
		}

		if ( in_array( 'empty_message', $fields['fields'], true ) ) {
			$output['empty_message'] = isset( $input['empty_message'] ) ? sanitize_textarea_field( $input['empty_message'] ) : $defaults['empty_message'];
		}

		if ( in_array( 'normal_icon', $fields['fields'], true ) ) {
			$output['normal_icon'] = isset( $input['normal_icon'] ) ? wp_kses( $input['normal_icon'], Icons::allowed_svg_tags() ) : Icons::normal_icon();
		}

		if ( in_array( 'added_icon', $fields['fields'], true ) ) {
			$output['added_icon'] = isset( $input['added_icon'] ) ? wp_kses( $input['added_icon'], Icons::allowed_svg_tags() ) : Icons::added_icon();
		}

		return wp_parse_args( $output, $defaults );
	}

	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$section = $this->current_section();
		$options = $this->settings->all();
		?>
		<div class="wrap egwl-admin">
			<h1><?php echo esc_html( $this->sections()[ $section ]['title'] ); ?></h1>

			<form method="post" action="options.php">
				<?php settings_fields( 'egwl_settings_group' ); ?>
				<input type="hidden" name="egwl_section" value="<?php echo esc_attr( $section ); ?>">

				<div class="egwl-admin-grid">
					<section class="egwl-panel">
						<?php $this->render_section_fields( $section, $options ); ?>
					</section>
				</div>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	private function sections() {
		return array(
			'egns-wishlist'  => array( 'title' => __( 'General Settings', 'egns-wishlist' ) ),
			'post-types'     => array( 'title' => __( 'Post Type Settings', 'egns-wishlist' ) ),
			'button'         => array( 'title' => __( 'Button Settings', 'egns-wishlist' ) ),
			'icon'           => array( 'title' => __( 'Icon Settings', 'egns-wishlist' ) ),
			'wishlist-page'  => array( 'title' => __( 'Wishlist Page Settings', 'egns-wishlist' ) ),
			'woocommerce'    => array( 'title' => __( 'WooCommerce Settings', 'egns-wishlist' ) ),
			'notification'   => array( 'title' => __( 'Notification Settings', 'egns-wishlist' ) ),
		);
	}

	private function current_section() {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : 'egns-wishlist';
		$page = str_replace( 'egns-wishlist-', '', $page );
		$page = 'egns-wishlist' === $page ? 'egns-wishlist' : $page;

		return array_key_exists( $page, $this->sections() ) ? $page : 'egns-wishlist';
	}

	private function section_fields( $section ) {
		$fields = array(
			'egns-wishlist' => array(
				'checkboxes' => array( 'enabled', 'enable_guest', 'enable_ajax', 'redirect_guest_login', 'merge_after_login', 'delete_on_uninstall' ),
				'fields'     => array( 'wishlist_page_id' ),
			),
			'post-types'    => array(
				'checkboxes' => array( 'auto_display' ),
				'fields'     => array( 'enabled_post_types', 'auto_display_position' ),
			),
			'button'        => array(
				'checkboxes' => array( 'show_text', 'show_icon' ),
				'fields'     => array( 'button_text', 'added_button_text', 'remove_button_text', 'button_css_class' ),
			),
			'icon'          => array(
				'checkboxes' => array(),
				'fields'     => array( 'icon_type', 'normal_icon', 'added_icon', 'icon_size', 'icon_color', 'added_icon_color' ),
			),
			'wishlist-page' => array(
				'checkboxes' => array( 'show_featured_image', 'show_title', 'show_post_type', 'show_date_added', 'show_remove_button', 'show_view_button', 'enable_share' ),
				'fields'     => array( 'empty_message' ),
			),
			'woocommerce'   => array(
				'checkboxes' => array( 'enable_wc', 'wc_show_price', 'wc_show_stock', 'wc_show_add_to_cart', 'wc_remove_after_cart' ),
				'fields'     => array( 'wc_single_position', 'wc_loop_position' ),
			),
			'notification'  => array(
				'checkboxes' => array( 'enable_toast' ),
				'fields'     => array( 'added_message', 'removed_message', 'already_added_message', 'toast_position' ),
			),
		);

		return isset( $fields[ $section ] ) ? $fields[ $section ] : $fields['egns-wishlist'];
	}

	private function render_section_fields( $section, $options ) {
		if ( 'egns-wishlist' === $section ) {
			$pages = get_pages();
			$this->checkbox( 'enabled', __( 'Enable Wishlist', 'egns-wishlist' ), $options );
			$this->checkbox( 'enable_guest', __( 'Enable Guest Wishlist', 'egns-wishlist' ), $options );
			$this->checkbox( 'enable_ajax', __( 'Enable AJAX', 'egns-wishlist' ), $options );
			$this->checkbox( 'redirect_guest_login', __( 'Redirect Guest to Login', 'egns-wishlist' ), $options );
			$this->checkbox( 'merge_after_login', __( 'Merge Guest Wishlist After Login', 'egns-wishlist' ), $options );
			$this->checkbox( 'delete_on_uninstall', __( 'Delete Data on Uninstall', 'egns-wishlist' ), $options );
			?>
			<label>
				<span><?php esc_html_e( 'Wishlist Page', 'egns-wishlist' ); ?></span>
				<select name="<?php echo esc_attr( Settings::OPTION_NAME ); ?>[wishlist_page_id]">
					<option value="0"><?php esc_html_e( 'Select page', 'egns-wishlist' ); ?></option>
					<?php foreach ( $pages as $page ) : ?>
						<option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( (int) $options['wishlist_page_id'], (int) $page->ID ); ?>><?php echo esc_html( $page->post_title ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<?php
			return;
		}

		if ( 'post-types' === $section ) {
			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			unset( $post_types['attachment'] );
			?>
			<div class="egwl-check-list">
				<?php foreach ( $post_types as $post_type ) : ?>
					<label>
						<input type="checkbox" name="<?php echo esc_attr( Settings::OPTION_NAME ); ?>[enabled_post_types][]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, $options['enabled_post_types'], true ) ); ?>>
						<?php echo esc_html( $post_type->labels->singular_name ); ?>
					</label>
				<?php endforeach; ?>
			</div>
			<?php
			$this->checkbox( 'auto_display', __( 'Auto Display Button', 'egns-wishlist' ), $options );
			$this->select( 'auto_display_position', __( 'Auto Display Position', 'egns-wishlist' ), array(
				'manual'         => __( 'Manual Shortcode Only', 'egns-wishlist' ),
				'before_content' => __( 'Before Content', 'egns-wishlist' ),
				'after_content'  => __( 'After Content', 'egns-wishlist' ),
				'after_title'    => __( 'After Title', 'egns-wishlist' ),
			), $options );
			return;
		}

		if ( 'button' === $section ) {
			$this->text( 'button_text', __( 'Button Text', 'egns-wishlist' ), $options );
			$this->text( 'added_button_text', __( 'Added Button Text', 'egns-wishlist' ), $options );
			$this->text( 'remove_button_text', __( 'Remove Button Text', 'egns-wishlist' ), $options );
			$this->checkbox( 'show_text', __( 'Show Text', 'egns-wishlist' ), $options );
			$this->checkbox( 'show_icon', __( 'Show Icon', 'egns-wishlist' ), $options );
			$this->text( 'button_css_class', __( 'Button CSS Class', 'egns-wishlist' ), $options );
			return;
		}

		if ( 'icon' === $section ) {
			$this->select( 'icon_type', __( 'Icon Type', 'egns-wishlist' ), array( 'svg' => 'SVG', 'custom' => __( 'Custom HTML', 'egns-wishlist' ) ), $options );
			$this->textarea( 'normal_icon', __( 'Normal Icon', 'egns-wishlist' ), $options );
			$this->textarea( 'added_icon', __( 'Added Icon', 'egns-wishlist' ), $options );
			$this->number( 'icon_size', __( 'Icon Size', 'egns-wishlist' ), $options );
			$this->text( 'icon_color', __( 'Icon Color', 'egns-wishlist' ), $options );
			$this->text( 'added_icon_color', __( 'Added Icon Color', 'egns-wishlist' ), $options );
			return;
		}

		if ( 'wishlist-page' === $section ) {
			$this->textarea( 'empty_message', __( 'Empty Wishlist Message', 'egns-wishlist' ), $options );
			$this->checkbox( 'show_featured_image', __( 'Show Featured Image', 'egns-wishlist' ), $options );
			$this->checkbox( 'show_title', __( 'Show Title', 'egns-wishlist' ), $options );
			$this->checkbox( 'show_post_type', __( 'Show Post Type', 'egns-wishlist' ), $options );
			$this->checkbox( 'show_date_added', __( 'Show Date Added', 'egns-wishlist' ), $options );
			$this->checkbox( 'show_remove_button', __( 'Show Remove Button', 'egns-wishlist' ), $options );
			$this->checkbox( 'show_view_button', __( 'Show View Button', 'egns-wishlist' ), $options );
			$this->checkbox( 'enable_share', __( 'Enable Share Wishlist', 'egns-wishlist' ), $options );
			return;
		}

		if ( 'woocommerce' === $section ) {
			if ( ! class_exists( 'WooCommerce' ) ) {
				echo '<p>' . esc_html__( 'WooCommerce is not active.', 'egns-wishlist' ) . '</p>';
			}

			$this->checkbox( 'enable_wc', __( 'Enable WooCommerce Support', 'egns-wishlist' ), $options );
			$this->checkbox( 'wc_show_price', __( 'Show Price', 'egns-wishlist' ), $options );
			$this->checkbox( 'wc_show_stock', __( 'Show Stock Status', 'egns-wishlist' ), $options );
			$this->checkbox( 'wc_show_add_to_cart', __( 'Show Add to Cart', 'egns-wishlist' ), $options );
			$this->checkbox( 'wc_remove_after_cart', __( 'Remove After Add to Cart', 'egns-wishlist' ), $options );
			$this->select( 'wc_single_position', __( 'Single Product Button Position', 'egns-wishlist' ), array(
				'woocommerce_before_add_to_cart_button' => __( 'Before Add to Cart', 'egns-wishlist' ),
				'woocommerce_after_add_to_cart_button'  => __( 'After Add to Cart', 'egns-wishlist' ),
				'woocommerce_before_single_product_summary' => __( 'After Product Thumbnail', 'egns-wishlist' ),
			), $options );
			$this->select( 'wc_loop_position', __( 'Shop Loop Button Position', 'egns-wishlist' ), array(
				'woocommerce_before_shop_loop_item_title' => __( 'After Product Thumbnail', 'egns-wishlist' ),
				'woocommerce_after_shop_loop_item'        => __( 'After Product', 'egns-wishlist' ),
				'woocommerce_after_shop_loop_item_title'  => __( 'After Product Title', 'egns-wishlist' ),
			), $options );
			return;
		}

		$this->checkbox( 'enable_toast', __( 'Enable Toast', 'egns-wishlist' ), $options );
		$this->text( 'added_message', __( 'Added Message', 'egns-wishlist' ), $options );
		$this->text( 'removed_message', __( 'Removed Message', 'egns-wishlist' ), $options );
		$this->text( 'already_added_message', __( 'Already Added Message', 'egns-wishlist' ), $options );
		$this->select( 'toast_position', __( 'Toast Position', 'egns-wishlist' ), array(
			'bottom-right' => __( 'Bottom Right', 'egns-wishlist' ),
			'bottom-left'  => __( 'Bottom Left', 'egns-wishlist' ),
			'top-right'    => __( 'Top Right', 'egns-wishlist' ),
			'top-left'     => __( 'Top Left', 'egns-wishlist' ),
		), $options );
	}

	private function checkbox( $key, $label, $options ) {
		?>
		<label class="egwl-toggle">
			<input type="checkbox" name="<?php echo esc_attr( Settings::OPTION_NAME ); ?>[<?php echo esc_attr( $key ); ?>]" value="yes" <?php checked( 'yes', $options[ $key ] ); ?>>
			<span><?php echo esc_html( $label ); ?></span>
		</label>
		<?php
	}

	private function text( $key, $label, $options ) {
		?>
		<label>
			<span><?php echo esc_html( $label ); ?></span>
			<input type="text" name="<?php echo esc_attr( Settings::OPTION_NAME ); ?>[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $options[ $key ] ); ?>">
		</label>
		<?php
	}

	private function number( $key, $label, $options ) {
		?>
		<label>
			<span><?php echo esc_html( $label ); ?></span>
			<input type="number" min="8" name="<?php echo esc_attr( Settings::OPTION_NAME ); ?>[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $options[ $key ] ); ?>">
		</label>
		<?php
	}

	private function textarea( $key, $label, $options ) {
		?>
		<label>
			<span><?php echo esc_html( $label ); ?></span>
			<textarea rows="5" name="<?php echo esc_attr( Settings::OPTION_NAME ); ?>[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $options[ $key ] ); ?></textarea>
		</label>
		<?php
	}

	private function select( $key, $label, $choices, $options ) {
		?>
		<label>
			<span><?php echo esc_html( $label ); ?></span>
			<select name="<?php echo esc_attr( Settings::OPTION_NAME ); ?>[<?php echo esc_attr( $key ); ?>]">
				<?php foreach ( $choices as $value => $choice_label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $options[ $key ], $value ); ?>><?php echo esc_html( $choice_label ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<?php
	}
}

