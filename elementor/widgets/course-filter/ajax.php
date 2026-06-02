<?php
/**
 * AJAX Handlers for Course Filter
 *
 * @package Onwards-Upwards-Psychology-Theme
 */

if (!defined('ABSPATH')) {
    exit; 
}

add_action( 'wp_ajax_filter_courses', 'oup_ajax_filter_courses_handler' );
add_action( 'wp_ajax_nopriv_filter_courses', 'oup_ajax_filter_courses_handler' );

function oup_ajax_filter_courses_handler() {

    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'course_filter_nonce' ) ) {
        echo '<p class="no-results-msg">Invalid security token. Please refresh the page.</p>';
        wp_die();
    }
    
    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 9;
    
    $allowed_orderby = ['date', 'title', 'rand', 'menu_order'];
    $orderby = isset($_POST['orderby']) && in_array($_POST['orderby'], $allowed_orderby) ? $_POST['orderby'] : 'date';
    
    $allowed_order = ['ASC', 'DESC'];
    $order = isset($_POST['order']) && in_array(strtoupper($_POST['order']), $allowed_order) ? strtoupper($_POST['order']) : 'DESC';

    $audience = isset($_POST['course_audience']) ? sanitize_text_field($_POST['course_audience']) : '*';
    $subject  = isset($_POST['course_subject']) ? sanitize_text_field($_POST['course_subject']) : '*';
    $level    = isset($_POST['course_learning_level']) ? sanitize_text_field($_POST['course_learning_level']) : '*';

    $args = array(
        'post_type'      => 'sfwd-courses',
        'posts_per_page' => $posts_per_page,
        'orderby'        => $orderby,
        'order'          => $order,
        'post_status'    => 'publish',
    );

    $meta_query = array();

    if ( $audience !== '*' && !empty($audience) ) {
        $meta_query[] = array(
            'key'     => 'course_audience',
            'value'   => $audience,
            'compare' => 'LIKE'
        );
    }

    if ( $subject !== '*' && !empty($subject) ) {
        $meta_query[] = array(
            'key'     => 'course_subject',
            'value'   => $subject,
            'compare' => 'LIKE'
        );
    }

    if ( $level !== '*' && !empty($level) ) {
        $meta_query[] = array(
            'key'     => 'course_learning_level',
            'value'   => $level,
            'compare' => 'LIKE'
        );
    }

    if ( count($meta_query) > 0 ) {
        $meta_query['relation'] = 'AND';
        $args['meta_query'] = $meta_query;
    }

    $query = new \WP_Query($args);

    if ( $query->have_posts() ) {
        if ( ! class_exists( '\OupElementorWidgets\Widgets\CourseFilter\Widget_CourseFilter' ) ) {
            require_once get_stylesheet_directory() . '/elementor/widgets/course-filter/widget.php';
        }

        while ( $query->have_posts() ) {
            $query->the_post();

            if ( class_exists( '\OupElementorWidgets\Widgets\CourseFilter\Widget_CourseFilter' ) ) {
                \OupElementorWidgets\Widgets\CourseFilter\Widget_CourseFilter::render_card(get_the_ID());
            }
        }
        wp_reset_postdata();
    } else {
        echo '<p class="no-results-msg">' . esc_html__('No courses found matching your criteria.', 'oup') . '</p>';
    }

    wp_die();
}
