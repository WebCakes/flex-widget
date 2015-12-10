<?php
/**
 * Legacy support.
 *
 * @package   FlexWidget
 * @copyright Copyright (c) 2014, WebCakes, Inc. & Blazer Six, Inc.
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Class to add support for features and data from previous versions.
 *
 * @package FlexWidget
 * @since 1.0.0
 */
class Flex_Widget_Legacy {
	/**
	 * Load legacy support.
	 *
	 * @since 1.0.0
	 */
	public function load() {
		add_filter( 'flex_widget_output', array( $this, 'output' ), 10, 4 );
		add_filter( 'flex_widget_fields', array( $this, 'fields' ), 10, 2 );
		add_action( 'flex_widget_field-legacy', array( $this, 'display_fields' ), 10, 2 );
		add_filter( 'flex_widget_instance', array( $this, 'sanitize_data' ), 10, 4 );
	}

	/**
	 * Legacy widget output.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $output   HTML output.
	 * @param array   $args     Registered sidebar arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array   $instance The widget instance settings.
	 * @param string  $id_base  Base widget type id.
	 * @return string HTML output.
	 */
	public function output( $output, $args, $instance, $id_base ) {
		if ( 'flex' != $id_base || ! empty( $instance['image_id'] ) || empty( $instance['image'] ) ) {
			return $output;
		}

		// Legacy output.
		$output = ( empty( $instance['title'] ) ) ? '' : $args['before_title'] . $instance['title'] . $args['after_title'];

		// Add the image.
		$output = sprintf(
			'%s<img src="%s" alt="%s">%s',
			$instance['link_open'],
			esc_url( $instance['image'] ),
			( empty( $instance['alt'] ) ) ? '' : esc_attr( $instance['alt'] ),
			$instance['link_close']
		);

		// Add the text.
		if ( ! empty( $instance['text'] ) ) {
			$output .= apply_filters( 'the_content', $instance['text'] );
		}

		// Add a more link.
		if ( ! empty( $instance['link_open'] ) && ! empty( $instance['link_title'] ) ) {
			$output .= '<p class="more">' . $instance['link_open'] . $instance['link_title'] . $instance['link_close'] . '</p>';
		}

		return $output;
	}

	/**
	 * Remove the image size field for versions of WordPress older than 3.5.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $fields  List of field ids.
	 * @param string $id_base Base widget type id.
	 * @return array
	 */
	public function fields( $fields, $id_base ) {
		if ( 'flex' == $id_base && is_flex_widget_legacy() ) {
			$key = array_search( 'image_size', $fields );
			if ( false !== $key ) {
				unset( $fields[ $key ] );
			}

			// Add a field for the old widget stuff.
			array_unshift( $fields, 'legacy' );
		}

		return $fields;
	}

	/**
	 * Display legacy fields in the widget edit form.
	 *
	 * @since 1.0.0
	 *
	 * @param array     $instance The widget instance settings.
	 * @param WP_Widget $widget   Widget instance.
	 */
	public function display_fields( $instance, $widget ) {
		if ( is_flex_widget_legacy() || ! empty( $instance['image'] ) ) :
			?>
			<div class="flex-widget-legacy-fields">
				<?php if ( ! is_flex_widget_legacy() ) : ?>
					<p>
						<em><?php _e( 'These fields are here to maintain your data from an earlier version.', 'flex-widget' ); ?></em>
					</p>
					<p>
						<em><?php _e( 'Select an image, then clear these values, and they will disappear when you save the widget.', 'flex-widget' ); ?></em>
					</p>
				<?php endif; ?>

				<p>
					<label for="<?php echo esc_attr( $widget->get_field_id( 'image' ) ); ?>"><?php _e( 'Image URL:', 'flex-widget' ); ?></label>
					<input type="text" name="<?php echo esc_attr( $widget->get_field_name( 'image' ) ); ?>" id="<?php echo esc_attr( $widget->get_field_id( 'image' ) ); ?>" value="<?php echo esc_url( $instance['image'] ); ?>" class="widefat">
				</p>
				<p>
					<label for="<?php echo esc_attr( $widget->get_field_id( 'alt' ) ); ?>"><?php _e( 'Alternate Text:', 'flex-widget' ); ?></label>
					<input type="text" name="<?php echo esc_attr( $widget->get_field_name( 'alt' ) ); ?>" id="<?php echo esc_attr( $widget->get_field_id( 'alt' ) ); ?>" value="<?php echo esc_attr( $instance['alt'] ); ?>" class="widefat">
				</p>
			</div>
			<?php
		endif;
	}

	/**
	 * Sanitize legacy field values.
	 *
	 * Called in Flex_Widget::update().
	 *
	 * @since 1.0.0
	 *
	 * @param array  $instance     Merged widget settings.
	 * @param array  $new_instance New widget settings.
	 * @param array  $old_instance Previous widget settings.
	 * @param string $id_base      Base widget type id.
	 * @return array Sanitized settings.
	 */
	public function sanitize_data( $instance, $new_instance, $old_instance, $id_base ) {
		if ( 'flex' == $id_base ) {
			// Legacy image URL.
			$instance['image'] = empty( $new_instance['image'] ) ? '' : esc_url_raw( $new_instance['image'] );
			if ( empty( $instance['image'] ) ) {
				unset( $instance['image'] );
			}

			// Legacy alt text.
			$instance['alt'] = empty( $new_instance['alt'] ) ? '' : wp_strip_all_tags( $instance['alt'] );
			if ( empty( $instance['alt'] ) ) {
				unset( $instance['alt'] );
			}
		}

		return $instance;
	}
}
