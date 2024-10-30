<?php
/*
Plugin Name: Moving image slider
Plugin URI: http://www.gopiplus.com/work/2021/05/26/moving-image-slider-wordpress-plugin/
Description: Moving image slider wordpress plugin moves the image within the fixed frame. This script will create the attractive image slider in the page and get the user attraction easily.
Author: Gopi Ramasamy
Version: 1.1
Author URI: http://www.gopiplus.com/work/about/
Donate link: http://www.gopiplus.com/work/2021/05/26/moving-image-slider-wordpress-plugin/
Tags: plugin, widget, moving, image, slider
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: moving-image-slider
Domain Path: /languages
*/

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
	die('You are not allowed to call this page directly.');
}

if(!defined('MISLIDERS_DIR')) 
	define('MISLIDERS_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
	
if ( ! defined( 'MISLIDERS_ADMIN_URL' ) )
	define( 'MISLIDERS_ADMIN_URL', admin_url() . 'options-general.php?page=moving-image-slider' );

require_once(MISLIDERS_DIR . 'classes' . DIRECTORY_SEPARATOR . 'mislider-register.php');
require_once(MISLIDERS_DIR . 'classes' . DIRECTORY_SEPARATOR . 'mislider-query.php');

function mislider_textdomain() {
	  load_plugin_textdomain( 'moving-image-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_shortcode( 'moving-image-slider', array( 'mislider_cls_shortcode', 'mislider_shortcode' ) );

add_action('wp_enqueue_scripts', array('mislider_cls_registerhook', 'mislider_frontscripts'));
add_action('plugins_loaded', 'mislider_textdomain');
add_action('widgets_init', array('mislider_cls_registerhook', 'mislider_widgetloading'));
add_action('admin_enqueue_scripts', array('mislider_cls_registerhook', 'mislider_adminscripts'));
add_action('admin_menu', array('mislider_cls_registerhook', 'mislider_addtomenu'));

register_activation_hook(MISLIDERS_DIR . 'moving-image-slider.php', array('mislider_cls_registerhook', 'mislider_activation'));
register_deactivation_hook(MISLIDERS_DIR . 'moving-image-slider.php', array('mislider_cls_registerhook', 'mislider_deactivation'));
?>