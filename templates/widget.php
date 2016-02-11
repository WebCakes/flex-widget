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
 * @since     1.0.0

 * Useful Variables for Custom Templating:
 *   $widget_template -- The sanitized slug value of 'Widget Template' within the widget.
 *                    -- Use this as a filter for templates for specific widget(s) ( See FAQ section, within the README )
 *   $link            -- The raw value of 'Link' within the widget
 *   $link_title      -- The sanitized value of 'Link Title' within the widget
 *   $link_classes    -- The raw value of 'Link Class'
 *   $link_open       -- An anchor output that includes href, class, title, and target if available
 *   $link_close      -- Closing the anchor
 *   $title           -- The sanitized value of 'Title' within the widget
 *   $title_raw       -- The raw value of 'Title' within the widget
 *   $text            -- The sanitized value of 'Text' within the widget
 *   $text_raw        -- The raw value of 'Text' within the widget
 */

?>

<?php // Image
  if ( ! empty( $image_id ) ) : ?>

    <?php
    echo $link_open;
    echo wp_get_attachment_image( $image_id, $image_size );
    echo $link_close;
    ?>

<?php endif; ?>

<?php // No Image but has Link, Wrap our inputs
  if ( ! empty( $link ) ) : ?>

    <?php // Title
      if ( ! empty( $title ) ) :
        $before_title = ( empty( $before_title ) ) ? '<h1>' : $before_title;
        $after_title = ( empty( $after_title ) ) ? '</h1>' : $after_title;

        echo $before_title . $link_open . $title . $link_close . $after_title;
      endif;
    ?>

    <?php // Text
      if ( ! empty( $text ) ) :
        echo $link_open;
          echo $text_raw;
        echo $link_close;
      endif;
    ?>

    <?php // Link
      if ( ( empty( $title ) AND empty( $text ) ) AND !empty( $link_title ) ) :
        echo $link_open . $link_title . $link_close;
      endif;
    ?>

  <?php else : ?>

    <?php // Title
      if ( ! empty( $title ) ) :
        $before_title = ( empty( $before_title ) ) ? '<h1>' : $before_title;
        $after_title = ( empty( $after_title ) ) ? '</h1>' : $after_title;

        echo $before_title . $title . $after_title;
      endif;
    ?>

    <?php // Text
      if ( ! empty( $text ) ) :
        echo wpautop( $text );
      endif;
    ?>

<?php endif; ?>
