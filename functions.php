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
        wp_enqueue_script('oup-main-script', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery', 'wc-add-to-cart'), OUP_THEME_VER, true);
        wp_localize_script('oup-main-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    }
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

/**
 * Shortcode: [oup_worksheet_files]
 */
function oup_worksheet_files_shortcode( $atts ) {
	
	$atts = shortcode_atts(
        array(
            'heading' => 'Worksheet File',
        ),
        $atts,
        'oup_worksheet_files'
    );

    if ( ! is_singular() ) {
        return '';
    }

    $post_id = get_the_ID();

    if ( ! have_rows( 'worksheet_files', $post_id ) ) {
        return '';
    }

    ob_start();

    echo '<div class="oup-worksheet-files">';
	if ( ! empty( $atts['heading'] ) ) {
        echo '<h2 class="oup-worksheet-files__heading">' . esc_html( $atts['heading'] ) . '</h2>';
    }
    echo '<ul class="oup-worksheet-files-list">';

    while ( have_rows( 'worksheet_files', $post_id ) ) {
        the_row();

        $file      = get_sub_field( 'attachment_file' );
        $file_name = get_sub_field( 'file_name' );

        if ( empty( $file ) ) {
            continue;
        }

        // File field may return array, URL, or ID depending on ACF setting
        if ( is_array( $file ) ) {
            $url          = $file['url'] ?? '';
            $default_name = $file['title'] ?? basename( $url );
        } elseif ( is_numeric( $file ) ) {
            $url          = wp_get_attachment_url( $file );
            $default_name = get_the_title( $file );
        } else {
            $url          = $file;
            $default_name = basename( $url );
        }

        $label = ! empty( $file_name ) ? $file_name : $default_name;

        echo '<li class="oup-worksheet-file">';
        echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">';
		echo '<div class="oup-worksheet-file__icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path d="M288.6 76.8C344.8 20.6 436 20.6 492.2 76.8C548.4 133 548.4 224.2 492.2 280.4L328.2 444.4C293.8 478.8 238.1 478.8 203.7 444.4C169.3 410 169.3 354.3 203.7 319.9L356.5 167.3C369 154.8 389.3 154.8 401.8 167.3C414.3 179.8 414.3 200.1 401.8 212.6L249 365.3C239.6 374.7 239.6 389.9 249 399.2C258.4 408.5 273.6 408.6 282.9 399.2L446.9 235.2C478.1 204 478.1 153.3 446.9 122.1C415.7 90.9 365 90.9 333.8 122.1L169.8 286.1C116.7 339.2 116.7 425.3 169.8 478.4C222.9 531.5 309 531.5 362.1 478.4L492.3 348.3C504.8 335.8 525.1 335.8 537.6 348.3C550.1 360.8 550.1 381.1 537.6 393.6L407.4 523.6C329.3 601.7 202.7 601.7 124.6 523.6C46.5 445.5 46.5 318.9 124.6 240.8L288.6 76.8z"/></svg></div>';
        echo '<div>' . esc_html( $label ) . '</div>';
        echo '</a>';
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';

    return ob_get_clean();
}
add_shortcode( 'oup_worksheet_files', 'oup_worksheet_files_shortcode' );