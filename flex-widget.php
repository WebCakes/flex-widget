<?php
/**
 * Flex Widget
 *
 * @package   FlexWidget
 * @author    Brady Vercher
 * @copyright Copyright (c) 2014, Blazer Six, Inc.
 * @license   GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Flex Widget
 * Plugin URI: https://wordpress.org/extend/plugins/flex-widget/
 * Description: A flex widget utilizing the new WordPress media manager.
 * Version: 4.1.1
 * Author: Blazer Six
 * Author URI: http://www.blazersix.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: flex-widget
 * Domain Path: /languages
 */

/**
 * Main plugin instance.
 *
 * @since 4.0.0
 * @type Flex_Widget $flex_widget
 */
global $flex_widget;

if ( ! defined( 'FW_DIR' ) ) {
	/**
	 * Plugin directory path.
	 *
	 * @since 4.0.0
	 * @type string FW_DIR
	 */
	define( 'FW_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Check if the installed version of WordPress supports the new media manager.
 *
 * @since 3.0.0
 */
function is_flex_widget_legacy() {
	/**
	 * Whether the installed version of WordPress supports the new media manager.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $is_legacy
	 */
	return apply_filters( 'is_flex_widget_legacy', version_compare( get_bloginfo( 'version' ), '3.4.2', '<=' ) );
}

/**
 * Include functions and libraries.
 */
require_once( FW_DIR . 'includes/class-flex-widget.php' );
require_once( FW_DIR . 'includes/class-flex-widget-legacy.php' );
require_once( FW_DIR . 'includes/class-flex-widget-plugin.php' );
require_once( FW_DIR . 'includes/class-flex-widget-template-loader.php' );

/**
 * Deprecated main plugin class.
 *
 * @since      3.0.0
 * @deprecated 4.0.0
 */
class Flex_Widget_Loader extends Flex_Widget_Plugin {}

// Initialize and load the plugin.
$flex_widget = new Flex_Widget_Plugin();
add_action( 'plugins_loaded', array( $flex_widget, 'load' ) );
