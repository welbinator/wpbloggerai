<?php
/*
Plugin Name: WP Blogger
Description: A plugin that creates blog posts using ChatGPT API.
Version: 1.0
Author: Your Name
Author URI: Your Website
Text Domain: wpblogger
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

define('WPBLOGGER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WPBLOGGER_PLUGIN_URL', plugin_dir_url(__FILE__));


// Include Guzzle using the Composer autoloader
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

// Require necessary files.
require_once WPBLOGGER_PLUGIN_PATH . 'includes/functions.php';
require_once WPBLOGGER_PLUGIN_PATH . 'admin/admin-menu.php';

function wpblogger_enqueue_scripts() {
    wp_enqueue_script('wpblogger-admin-script', plugin_dir_url(__FILE__) . 'admin/js/admin.js', array('jquery'), '1.0', true);

    wp_localize_script('wpblogger-admin-script', 'wpblogger_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}

add_action('admin_enqueue_scripts', 'wpblogger_enqueue_scripts');


