# Flex Widget

A *Flexible* Wordpress widget that gives extreme flexibility to sidebars in WordPress when templating content such as text, links and images.

__Requires:__ 3.5  
__Tested up to:__ 4.0  
__License:__ [GPL-2.0+](http://www.gnu.org/licenses/gpl-2.0.html)

The Flex Widget is specifically designed with templating in mind.  
The main idea of the plugin is to provide all the structure necessary for your template within the sidebar register using arguments.

In the case that not all the flex widgets have the same structure within the widget sidebar, using the template system you can set a Template Name within the widget.  
Using the Template Name, you can filter what to output based on the value within your custom template. ( See FAQ Section )

## Installation

Clone the Flex Widget repository and insert the files into your Wordpress plugin's directory ( wp-content/plugins/flex-widget ).

## FAQ

### Is there a way to filter the widget output?

Absolutely. Changing the output can be done a few different ways, but the most common alternatives involve using the "`flex_widget_output`" filter or overriding the template in your theme.

To use the template method, copy `widget.php` from the `/templates` directory in the plugin to a `/flex-widget` directory in your theme. Then update as you wish. It's also possible to create a custom template specific to each sidebar in your theme using the following default template hierarchy:

* `{theme}/flex-widget/{sidebar_id}_widget.php`
* `{theme}/flex-widget/widget.php`
* `{plugin}/templates/widget.php`

**Always use a [child theme](https://codex.wordpress.org/Child*Themes) to make changes if you acquired your theme from a third-party and you expect it to be updated. Otherwise, you run the risk of losing your customizations.**

### How do I add alt text to images in the widget?

When selecting an image in the media modal (the popup to select images), the right sidebar will be titled "Attachment Details" and contains a field for entering alt text. After entering your alt text, click the "Update Image" button to use the selected image in your widget. Most browsers don't show the alt text, so you'll need to view the HTML source to make sure it exists.

### How do I center the widget?

The widget can be centered using CSS. Custom CSS should be added a child theme or using a plugin like [Simple Custom CSS](https://wordpress.org/plugins/simple-custom-css/) or [Jetpack](https://wordpress.org/plugins/jetpack/). The following snippet will center the contents of the widget:

````
.widget_flex {
     text-align: center;
}
````

### Can I remove the width and height attributes?

The widget uses the core function `wp_get_attachment_image()` to display the image and it would be more trouble than it's worth to remove those attributes. Some basic CSS will typically allow you to make the image responsive if necessary:

````
.widget_flex img {
	height: auto;
	max-width: 100%;
}
````

## To Do: 

## Future Features: 

## Credits

Forked and Maintained by [WebCakes](http://www.webcakes.ca)
Originally Built by [Brady Vercher](http://twitter.com/bradyvercher)  
Forked from the [flex-widget repository](https://github.com/blazersix/flex-widget).
Copyright 2014  [WebCakes, Inc.](http://www.webcakes.ca) & [Blazer Six, Inc.](http://www.blazersix.com/)
