=== Flex Widget ===
Tags: flex, flexible, templating, custom, image widget, widget, media, media manager, sidebar, image, photo, picture
Requires at least: 3.5
Tested up to: 4.0
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A *Flexible* Wordpress widget that gives extreme flexibility to sidebars in WordPress when templating content such as text, links and images.

== Description ==

The Flex Widget is specifically designed with templating in mind.  
The main idea of the plugin is to allow the structure necessary for your widget and template to be customized in a custom widget/sidebar specific template. ( See FAQ Section )

In the case that not all the flex widgets have the same structure within the widget sidebar, using the custom template system you can set a 'Widget Template' within the widget.  
Using the Widget Template field, you can filter what to output based on the value, within your custom template. ( See FAQ Section )

= Additional Resources =

* [Have a question?](https://github.com/WebCakes/flex-widget/issues)
* [Contribute on GitHub](https://github.com/WebCakes/flex-widget)
* [Follow @webcakes](https://twitter.com/webcakes)
* [Follow @bradyvercher](https://twitter.com/bradyvercher)
* [Hire WebCakes](http://www.blazersix.com/)
* [Hire Blazer Six](http://www.blazersix.com/)

= Translation Credits =

* Finnish (fi) - Tomi Mäenpää [v4.1.1](Simple Image Widget)
* French (fr_FR) - Alexandre Sadowski [v4.0.1](Simple Image Widget)

== Installation ==

Download a zip of the Latest Release from GitHub ( https://github.com/WebCakes/flex-widget/releases/latest ) or clone the current Flex Widget repository ( https://github.com/WebCakes/flex-widget/tree/master ) and insert the files into the Wordpress plugin directory ( wp-content/plugins/flex-widget ).

Next you will need to activate the plugin in the Wordpress backend.

Your new widget should now appear in the list of available widgets.

== Frequently Asked Questions ==

= Is there a way to filter the widget output? =

Absolutely. Changing the output can be done a few different ways, but the most common alternatives involve using the "`flex_widget_output`" filter or overriding the template in your theme.

To use the template method, copy "`widget.php`" from the "`/templates`" directory in the plugin to a "`/flex-widget`" directory in your theme. Then update as you wish. It's also possible to create a custom template specific to each sidebar in your theme using the following default template hierarchy:

* `{theme}/flex-widget/{sidebar_id}_widget.php`
* `{theme}/flex-widget/widget.php`
* `{plugin}/templates/widget.php`

_Always use a [child theme](https://codex.wordpress.org/Child_Themes) to make changes if you acquired your theme from a third-party and you expect it to be updated. Otherwise, you run the risk of losing your customizations._

= That's great but I have many flex widgets in my sidebar, can I template them individually? =

Well, using the Widget Template field within the widget, you can effectively filter either from the general `{theme}/flex-widget/widget.php` template or from a sidebar specific `{theme}/flex-widget/{sidebar_id}_widget.php` template.

_But How?_

Using the `$widget_template` hook within your custom template you can use a switch/case (preferred) or a basic if/else statement and check for your widget template value.

If you created a Widget Template by the name of: `tasty-treats`  
Then you can filter your output like so: 

````
<?php
switch( $widget_template ) {
  case 'tasty-treats' :
    ?>
    <!-- My Unique Widget HTML or PHP Code Here! -->
    <?php
    break;
  default :
    ?>
      <!-- My Fallback / Default HTML or PHP Code Here! -->
    <?php
}
?>
````

= How do I add alt text to images in the widget? =

When selecting an image in the media modal (the popup to select images), the right sidebar will be titled "Attachment Details" and contains a field for entering alt text. After entering your alt text, click the "Update Image" button to use the selected image in your widget. Most browsers don't show the alt text, so you'll need to view the HTML source to make sure it exists.

= How do I center the widget? =

The widget can be centered using CSS. Custom CSS should be added a child theme or using a plugin like [Simple Custom CSS](https://wordpress.org/plugins/simple-custom-css/) or [Jetpack](https://wordpress.org/plugins/jetpack/). The following snippet will center the contents of the widget:

`.widget_flex {
     text-align: center;
}`

= Can I remove the width and height attributes? =

The widget uses the core function `wp_get_attachment_image()` to display the image and it would be more trouble than it's worth to remove those attributes. Some basic CSS will typically allow you to make the image responsive if necessary:

`.widget_flex img {
	height: auto;
	max-width: 100%;
}`

== Screenshots ==

1. A new flex widget.
2. The widget after selecting an image.

== Changelog ==

= 1.0.1 =
* Fix issue where the 'Open New Window' wouldn't be able to save as 'unchecked' after saving it 'checked'.

= 1.0.0 = ( Simple Image Widget Version 4.1.1 )
* Convert all Simple Image instances to Flex
* Add necessary descriptions and details for Flex Widget making sure to reference Simple Image Widget as much as possible

