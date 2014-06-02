<?php
defined('ABSPATH') OR exit;
/*
Plugin Name: Picturefill.WP
Plugin URI: http://github.com/kylereicks/picturefill.js.wp
Description: A wordpress plugin to load responsive/retina images via picturefill.js.
Author: Kyle Reicks
Version: 1.3.2
Author URI: http://github.com/kylereicks/
*/

define('PICTUREFILL_WP_PATH', get_template_directory() . '/inc/picturefill/');
define('PICTUREFILL_WP_URL', get_template_directory_uri() . '/inc/picturefill/');
define('PICTUREFILL_WP_VERSION', '1.3.2');

require_once(PICTUREFILL_WP_PATH . 'inc/class-picturefill-wp.php');

require_once(PICTUREFILL_WP_PATH . 'inc/functions-picturefill-wp.php');

register_deactivation_hook(__FILE__, array('Picturefill_WP', 'deactivate'));

add_action('after_setup_theme', array('Picturefill_WP', 'get_instance'));
