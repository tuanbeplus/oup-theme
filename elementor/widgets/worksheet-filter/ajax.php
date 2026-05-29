<?php
/**
 * AJAX Handlers
 *
 * @package Onwards-Upwards-Psychology-Theme
 */

if (!defined('ABSPATH')) {
    exit; 
}

add_action( 'wp_ajax_filter_worksheets', 'oup_ajax_filter_worksheets_handler' );
add_action( 'wp_ajax_nopriv_filter_worksheets', 'oup_ajax_filter_worksheets_handler' );

function oup_ajax_filter_worksheets_handler() {

    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'worksheet_filter_nonce' ) ) {
        echo '<p class="no-results-msg">Invalid security token. Please refresh the page.</p>';
        wp_die();
    }
    $search_text = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $category    = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '*';
    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 9;
    
    $allowed_orderby = ['date', 'title', 'rand', 'menu_order'];
    $orderby = isset($_POST['orderby']) && in_array($_POST['orderby'], $allowed_orderby) ? $_POST['orderby'] : 'date';
    
    $allowed_order = ['ASC', 'DESC'];
    $order = isset($_POST['order']) && in_array(strtoupper($_POST['order']), $allowed_order) ? strtoupper($_POST['order']) : 'DESC';

    $args = array(
        'post_type' => 'worksheet',
        'posts_per_page' => $posts_per_page,
        'orderby' => $orderby,
        'order' => $order,
        'post_status' => 'publish',
    );

    if ( ! empty( $search_text ) ) {
        $args['s'] = $search_text;
    }

    if ( $category !== '*' && !empty($category) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'worksheet-category',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        );
    }
    
    $query = new \WP_Query($args);

    if ( $query->have_posts() ) {
        if ( ! class_exists( '\OupElementorWidgets\Widgets\WorksheetFilter\Widget_WorksheetFilter' ) ) {
            require_once get_stylesheet_directory() . '/elementor/widgets/worksheet-filter/widget.php';
        }

        while ( $query->have_posts() ) {
            $query->the_post();

            if ( class_exists( '\OupElementorWidgets\Widgets\WorksheetFilter\Widget_WorksheetFilter' ) ) {
                \OupElementorWidgets\Widgets\WorksheetFilter\Widget_WorksheetFilter::render_card(get_the_ID());
            }
        }
        wp_reset_postdata();
    } else {
        echo '<p class="no-results-msg">' . esc_html__('No worksheets found matching your criteria.', 'oup') . '</p>';
    }

    wp_die();
}
