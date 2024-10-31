<?php
/*
Plugin Name: Random image light box
Plugin URI: http://www.gopiplus.com/work/2020/10/11/wordpress-plugin-random-image-light-box/
Description: Random image light box plugin allows you to show one random image with on click light box effect in the website.
Author: Gopi Ramasamy
Version: 1.3
Author URI: http://www.gopiplus.com/work/about/
Donate link: http://www.gopiplus.com/work/2020/10/11/wordpress-plugin-random-image-light-box/
Tags: plugin, widget, image, random, gallery, light box
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: random-image-light-box
Domain Path: /languages
*/

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
	die('You are not allowed to call this page directly.');
}

if(!defined('RILBP_DIR')) 
	define('RILBP_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

if ( ! defined( 'RILBP_ADMIN_URL' ) )
	define( 'RILBP_ADMIN_URL', admin_url() . 'options-general.php?page=random-image-light-box' );

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'rilb-register.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'rilb-query.php');

function rilb_textdomain() {
	  load_plugin_textdomain( 'random-image-light-box', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_shortcode( 'random-image-light-box', array( 'rilb_cls_shortcode', 'rilb_shortcode' ) );

add_action('wp_enqueue_scripts', array('rilb_cls_registerhook', 'rilb_frontscripts'));
add_action('plugins_loaded', 'rilb_textdomain');
add_action('widgets_init', array('rilb_cls_registerhook', 'rilb_widgetloading'));
add_action('admin_enqueue_scripts', array('rilb_cls_registerhook', 'rilb_adminscripts'));
add_action('admin_menu', array('rilb_cls_registerhook', 'rilb_addtomenu'));

register_activation_hook(RILBP_DIR . 'random-image-light-box.php', array('rilb_cls_registerhook', 'rilb_activation'));
register_deactivation_hook(RILBP_DIR . 'random-image-light-box.php', array('rilb_cls_registerhook', 'rilb_deactivation'));
?>