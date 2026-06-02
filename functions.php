<?php

/**
 * Theme functions and definitions
 *
 * @package Onwards-Upwards-Psychology-Theme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('OUP_THEME_VER', '1.0.0' . time());

/**
 * Enqueue styles and scripts
 */
if (!function_exists('enqueue_oup_styles_and_scripts')) {
    add_action('wp_enqueue_scripts', 'enqueue_oup_styles_and_scripts');
    function enqueue_oup_styles_and_scripts()
    {
        wp_enqueue_style('oup-main-style', get_stylesheet_directory_uri() . '/assets/css/main.css', array(), OUP_THEME_VER);
        wp_enqueue_script('oup-main-script', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), OUP_THEME_VER, true);
        wp_localize_script('oup-main-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    }
}

/**
 * Shortcode: [oup_author_role]
 * @return string
 */
if (!function_exists('oup_author_role_shortcode')) {
    function oup_author_role_shortcode($atts)
    {
        $atts      = shortcode_atts(['post_id' => 0], $atts, 'oup_author_role');
        $post_id   = $atts['post_id'] ? (int) $atts['post_id'] : get_the_ID();
        $author_id = (int) get_post_field('post_author', $post_id);

        if (!$author_id) {
            return '';
        }

        $user  = get_userdata($author_id);
        $roles = $user->roles ?? [];

        if (empty($roles)) {
            return '';
        }

        return esc_html(ucfirst(reset($roles)));
    }
    add_shortcode('oup_author_role', 'oup_author_role_shortcode');
}

/* Widgets Load */
require_once get_stylesheet_directory() . '/elementor/widgets-load.php';

/* WooCommerce */
require_once get_stylesheet_directory() . '/inc/woo.php';

/* AJAX Handlers */
require_once get_stylesheet_directory() . '/elementor/widgets/archive-posts-filter/ajax.php';
require_once get_stylesheet_directory() . '/elementor/widgets/worksheet-filter/ajax.php';
require_once get_stylesheet_directory() . '/elementor/widgets/course-filter/ajax.php';
