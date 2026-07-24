<?php

/**
 * Theme functions and definitions
 *
 * @package Onwards-Upwards-Psychology-Theme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('OUP_THEME_VER', '1.1.0');

/**
 * Enqueue styles and scripts
 */
if (!function_exists('enqueue_oup_styles_and_scripts')) {
    add_action('wp_enqueue_scripts', 'enqueue_oup_styles_and_scripts');
    function enqueue_oup_styles_and_scripts()
    {
        wp_enqueue_style('oup-main-style', get_stylesheet_directory_uri() . '/assets/css/main.css', array(), OUP_THEME_VER);
        wp_enqueue_script('oup-main-script', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery', 'wc-add-to-cart'), OUP_THEME_VER, true);
        wp_localize_script('oup-main-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
        
        // Custom LearnDash scripts
        wp_enqueue_script('oup-learndash-custom', get_stylesheet_directory_uri() . '/assets/js/learndash-custom.js', array('jquery'), OUP_THEME_VER, true);
    }
}

/* Widgets Load */
require_once get_stylesheet_directory() . '/elementor/widgets-load.php';

/* WooCommerce */
require_once get_stylesheet_directory() . '/inc/woo.php';

// Hooks
require_once get_stylesheet_directory() . '/inc/hooks.php';

// Shortcodes
require_once get_stylesheet_directory() . '/inc/shortcodes.php';

/* AJAX Handlers */
require_once get_stylesheet_directory() . '/elementor/widgets/archive-posts-filter/ajax.php';
require_once get_stylesheet_directory() . '/elementor/widgets/worksheet-filter/ajax.php';
require_once get_stylesheet_directory() . '/elementor/widgets/course-filter/ajax.php';
require_once get_stylesheet_directory() . '/elementor/widgets/sugar-calendar-event/shortcode.php';

/* Course Button Link */
$button_file = get_stylesheet_directory() . '/elementor/shortcode/course-button-link.php';
if (file_exists($button_file)) {
    require_once $button_file;
}
/* Course Price Shortcode */
$currency_file = get_stylesheet_directory() . '/elementor/shortcode/course-currency.php';
if (file_exists($currency_file)) {
    require_once $currency_file;
}
