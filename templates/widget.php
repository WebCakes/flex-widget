<?php
/**
 * Default widget template.
 *
 * Copy this template to /flex-widget/widget.php in your theme or
 * child theme to make edits.
 *
 * @package   FlexWidget
 * @copyright Copyright (c) 2014, WebCakes, Inc. & Blazer Six, Inc.
 * @license   GPL-2.0+
 * @since     4.0.0
 */
?>

<?php
if ( ! empty( $title ) ) :
	echo $before_title . $title . $after_title;
endif;
?>

<?php if ( ! empty( $image_id ) ) : ?>
	<p class="flex">
		<?php
		echo $link_open;
		echo wp_get_attachment_image( $image_id, $image_size );
		echo $link_close;
		?>
	</p>
<?php endif; ?>

<?php
if ( ! empty( $text ) ) :
	echo wpautop( $text );
endif;
?>

<?php if ( ! empty( $link_text ) ) : ?>
	<p class="more">
		<?php
		echo $text_link_open;
		echo $link_text;
		echo $text_link_close;
		?>
	</p>
<?php endif; ?>
