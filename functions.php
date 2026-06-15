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
        if ( oup_should_load_stripe_js() ) {
            wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', array(), null, false);
        }
        wp_localize_script('oup-main-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    }
}
/**
 * Helper function to determine if Stripe JS should be loaded
 */
function oup_should_load_stripe_js() {
    global $post;
    if ( is_singular( 'sfwd-courses' ) || is_post_type_archive( 'sfwd-courses' ) ) {
        return true;
    }
    if ( is_a( $post, 'WP_Post' ) ) {
        $elementor_data = get_post_meta( $post->ID, '_elementor_data', true );
        if ( ! empty( $elementor_data ) && strpos( $elementor_data, 'course-filter' ) !== false ) {
            return true;
        }
    }
    return false;
}
/**
 * Turn off the default LearnDash interface on the course detail page.
 */
add_filter('learndash_template_preprocess_filter', function($run, $post_id) {
    if ( is_singular('sfwd-courses') ) {
        return false;
    }
    return $run;
}, 10, 2);

// Shortcode for Course Accordion to show all lessons, topics, and quizzes
if (!function_exists('oup_course_accordion_shortcode_callback')) {
    function oup_course_accordion_shortcode_callback($atts) {
        $atts = shortcode_atts(array(
            'course_id'     => 0,
            'default_state' => 'first_expanded',
            'max_items'     => 'multiple',
            'anim_duration' => 400
        ), $atts, 'oup_course_accordion');

        $course_id = intval($atts['course_id']);
        if (!$course_id) {
            $course_id = get_the_ID();
        }

        if (!$course_id || get_post_type($course_id) !== 'sfwd-courses' || !function_exists('learndash_get_course_lessons_list')) {
            return '';
        }


        $lessons_list = \learndash_get_course_lessons_list($course_id, null, ['nopaging' => true]);
        if (!empty($lessons_list) && is_array($lessons_list)) {
            foreach ($lessons_list as $key => $lesson) {
                $lesson_id = isset($lesson['id']) ? $lesson['id'] : $lesson['post']->ID;
                $lessons_list[$key]['topics']  = function_exists('learndash_get_topic_list') ? \learndash_get_topic_list($lesson_id, $course_id) : [];
                $lessons_list[$key]['quizzes'] = function_exists('learndash_get_lesson_quiz_list') ? \learndash_get_lesson_quiz_list($lesson_id, null, $course_id) : [];
            }
        }

        if (empty($lessons_list)) {
            return '';
        }

        ob_start();
        get_template_part('elementor/shortcode/course-accordion', null, [
            'lessons_list'  => $lessons_list,
            'default_state' => $atts['default_state'],
            'max_items'     => $atts['max_items'],
            'anim_duration' => intval($atts['anim_duration'])
        ]);
        return ob_get_clean();
    }
}
add_filter( 'term_link', 'custom_worksheet_category_term_link', 10, 3 );
function custom_worksheet_category_term_link( $url, $term, $taxonomy ) {
    if ( 'worksheet-category' === $taxonomy || 'worksheets-audience' === $taxonomy) {
        $archive_url = get_post_type_archive_link( 'worksheet' );
        
    
        if ( ! $archive_url ) {
            $archive_url = home_url( '/worksheet/' ); 
        }
        $url = $archive_url;
    }
    return $url;
}
add_action( 'template_redirect', 'redirect_worksheet_category_archive' );
function redirect_worksheet_category_archive() {
    if ( is_tax( 'worksheet-category' ) ) {
        wp_redirect( get_post_type_archive_link( 'worksheet' ), 301 );
        exit;
    }
}

add_filter('term_link', 'custom_post_category_term_link', 10, 3);
function custom_post_category_term_link($url, $term, $taxonomy)
{
    if ('category' === $taxonomy || 'post_tag' === $taxonomy) {
        $archive_url = home_url('/blogs/');
        $url = $archive_url;
    }
    return $url;
}

add_action('template_redirect', 'redirect_post_category_archive');
function redirect_post_category_archive()
{
    if (is_category() || is_tag()) {
        wp_redirect(home_url('/blogs/'), 301);
        exit;
    }
}

add_shortcode('oup_course_accordion', 'oup_course_accordion_shortcode_callback');

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
/* Widgets Load */
require_once get_stylesheet_directory() . '/elementor/widgets-load.php';

/* WooCommerce */
require_once get_stylesheet_directory() . '/inc/woo.php';

/* AJAX Handlers */
require_once get_stylesheet_directory() . '/elementor/widgets/archive-posts-filter/ajax.php';
require_once get_stylesheet_directory() . '/elementor/widgets/worksheet-filter/ajax.php';
require_once get_stylesheet_directory() . '/elementor/widgets/course-filter/ajax.php';
require_once get_stylesheet_directory() . '/elementor/widgets/sugar-calendar-event/shortcode.php';

// Hooks
require_once get_stylesheet_directory() . '/inc/hooks.php';