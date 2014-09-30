<?php
/**
 * Flex Widget
 *
 * @package   FlexWidget
 * @copyright Copyright (c) 2014, WebCakes, Inc. & Blazer Six, Inc.
 * @license   GPL-2.0+
 * @since     3.0.0
 */

/**
 * The main plugin class for loading the widget and attaching hooks.
 *
 * @package FlexWidget
 * @since   3.0.0
 */
class Flex_Widget_Plugin {
	/**
	 * Set up the widget.
	 *
	 * @since 3.0.0
	 */
	public function load() {
		self::load_textdomain();
		add_action( 'widgets_init', array( $this, 'register_widget' ) );

		$compat = new Flex_Widget_Legacy();
		$compat->load();

		if ( is_flex_widget_legacy() ) {
			return;
		}

		add_action( 'init', array( $this, 'register_assets' ) );
		add_action( 'sidebar_admin_setup', array( $this, 'enqueue_admin_assets' ) );
		add_filter( 'screen_settings', array( $this, 'widgets_screen_settings' ), 10, 2 );
		add_action( 'wp_ajax_flex_widget_preferences', array( $this, 'ajax_save_user_preferences' ) );
	}

	/**
	 * Localize the plugin strings.
	 *
	 * @since 3.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'flex-widget', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' );
	}

	/**
	 * Register the image widget.
	 *
	 * @since 3.0.0
	 */
	public function register_widget() {
		register_widget( 'Flex_Widget' );
	}

	/**
	 * Register and localize generic scripts and styles.
	 *
	 * A preliminary attempt has been made to abstract the
	 * 'flex-widget-control' script a bit in order to allow it to be
	 * re-used anywhere a similiar media selection feature is needed.
	 *
	 * Custom image size labels need to be added using the
	 * 'image_size_names_choose' filter.
	 *
	 * @since 3.0.0
	 */
	public function register_assets() {
		wp_register_style(
			'flex-widget-admin',
			dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/flex-widget.css'
		);

		wp_register_script(
			'flex-widget-admin',
			dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/flex-widget.js',
			array( 'media-upload', 'media-views' )
		);

		wp_localize_script(
			'flex-widget-admin',
			'FlexWidget',
			array(
				'l10n' => array(
					'frameTitle'      => __( 'Choose an Attachment', 'flex-widget' ),
					'frameUpdateText' => __( 'Update Attachment', 'flex-widget' ),
					'fullSizeLabel'   => __( 'Full Size', 'flex-widget' ),
					'imageSizeNames'  => self::get_image_size_names(),
				),
				'screenOptionsNonce' => wp_create_nonce( 'save-flex-preferences' ),
			)
		);
	}

	/**
	 * Add checkboxes to the screen options tab on the Widgets screen for
	 * togglable fields.
	 *
	 * @since 4.1.0
	 *
	 * @param string    $settings Screen options output.
	 * @param WP_Screen $screen   Current screen.
	 * @return string
	 */
	public function widgets_screen_settings( $settings, $screen ) {
		if ( 'widgets' !== $screen->id ) {
			return $settings;
		}

		$settings .= sprintf( '<h5>%s</h5>', __( 'Flex Widget', 'flex-widget' ) );

		$fields = array(
			'widget_template'   => __( 'Widget Template', 'flex-widget' ),
			'image_size'   => __( 'Image Size', 'flex-widget' ),
			'link'         => __( 'Link', 'flex-widget' ),
			'link_classes' => __( 'Link Classes', 'flex-widget' ),
			'link_title'    => __( 'Link Title', 'flex-widget' ),
			'new_window'   => __( 'New Window', 'flex-widget' ),
			'title'         => __( 'Title', 'flex-widget' ),
			'text'         => __( 'Text', 'flex-widget' ),
		);

		/**
		 * List of hideable fields.
		 *
		 * @since 4.1.0
		 *
		 * @param array $fields List of fields with ids as keys and labels as values.
		 */
		$fields = apply_filters( 'flex_widget_hideable_fields', $fields );
		$hidden_fields = $this->get_hidden_fields();

		foreach ( $fields as $id => $label ) {
			$settings .= sprintf(
				'<label><input type="checkbox" value="%1$s"%2$s class="flex-widget-field-toggle"> %3$s</label>',
				esc_attr( $id ),
				checked( in_array( $id, $hidden_fields ), false, false ),
				esc_html( $label )
			);
		}

		return $settings;
	}

	/**
	 * Enqueue scripts needed for selecting media.
	 *
	 * @since 3.0.0
	 *
	 * @param string $hook_suffix Screen id.
	 */
	public function enqueue_admin_assets() {
		wp_enqueue_media();
		wp_enqueue_script( 'flex-widget-admin' );
		wp_enqueue_style( 'flex-widget-admin' );
	}

	/**
	 * Get localized image size names.
	 *
	 * The 'image_size_names_choose' filter exists in core and should be
	 * hooked by plugin authors to provide localized labels for custom image
	 * sizes added using add_image_size().
	 *
	 * @see image_size_input_fields()
	 * @see http://core.trac.wordpress.org/ticket/20663
	 *
	 * @since 3.0.0
	 *
	 * @return array Array of thumbnail sizes.
	 */
	public static function get_image_size_names() {
		return apply_filters(
			'image_size_names_choose',
			array(
				'thumbnail' => __( 'Thumbnail', 'flex-widget' ),
				'medium'    => __( 'Medium', 'flex-widget' ),
				'large'     => __( 'Large', 'flex-widget' ),
				'full'      => __( 'Full Size', 'flex-widget' ),
			)
		);
	}

	/**
	 * Retrieve a list of hidden fields.
	 *
	 * @since 4.1.0
	 *
	 * @return array List of field ids.
	 */
	public static function get_hidden_fields() {
		$hidden_fields = get_user_option( 'flex_hidden_fields', get_current_user_id() );

		// Fields that are hidden by default.
		if ( false === $hidden_fields ) {
			/* $hidden_fields = array( 'link_classes' ); */
			$hidden_fields = array();
		}

		/**
		 * List of hidden field ids.
		 *
		 * @since 4.1.0
		 *
		 * @param array $hidden_fields List of hidden field ids.
		 */
		return (array) apply_filters( 'flex_widget_hidden_fields', $hidden_fields );
	}

	/**
	 * AJAX callback to save the user's hidden fields.
	 *
	 * @since 4.1.0
	 */
	public function ajax_save_user_preferences() {
		$nonce_action = 'save-flex-preferences';
		check_ajax_referer( $nonce_action, 'nonce' );
		$data = array( 'nonce' => wp_create_nonce( $nonce_action ) );

		if ( ! $user = wp_get_current_user() ) {
			wp_send_json_error( $data );
		}

		$hidden = isset( $_POST['hidden'] ) ? explode( ',', $_POST['hidden'] ) : array();
		if ( is_array( $hidden ) ) {
			update_user_option( $user->ID, 'flex_hidden_fields', $hidden );
		}

		wp_send_json_success( $data );
	}
}
