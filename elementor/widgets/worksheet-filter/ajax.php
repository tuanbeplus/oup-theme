<?php
if (! defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_filter_worksheets',        'oup_ajax_filter_worksheets_handler');
add_action('wp_ajax_nopriv_filter_worksheets', 'oup_ajax_filter_worksheets_handler');

function oup_ajax_filter_worksheets_handler()
{
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'worksheet_filter_nonce')) {
        echo '<p class="no-results-msg">' . esc_html__('Invalid security token. Please refresh the page.', 'oup') . '</p>';
        wp_die();
    }

    $search_text    = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $category       = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '*';
    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 9;

    $allowed_orderby = ['date', 'title', 'rand', 'menu_order'];
    $orderby = isset($_POST['orderby']) && in_array($_POST['orderby'], $allowed_orderby, true)
        ? $_POST['orderby'] : 'date';

    $allowed_order = ['ASC', 'DESC'];
    $order = isset($_POST['order']) && in_array(strtoupper($_POST['order']), $allowed_order, true)
        ? strtoupper($_POST['order']) : 'DESC';

    $args = [
        'post_type'      => 'worksheet',
        'posts_per_page' => $posts_per_page,
        'orderby'        => $orderby,
        'order'          => $order,
        'post_status'    => 'publish',
    ];

    if (! empty($search_text)) {
        $args['s'] = $search_text;
    }

    if ($category !== '*' && ! empty($category)) {
        $args['tax_query'] = [[
            'taxonomy' => 'worksheet-category',
            'field'    => 'slug',
            'terms'    => $category,
        ]];
    }

    $widget_class = '\OupElementorWidgets\Widgets\WorksheetFilter\Widget_WorksheetFilter';

    if (! class_exists($widget_class)) {
        require_once get_stylesheet_directory() . '/elementor/widgets/worksheet-filter/widget.php';
    }

    $query = new \WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $widget_class::render_card(get_the_ID());
        }
        wp_reset_postdata();
    } else {
        echo '<p class="no-results-msg">' . esc_html__('No worksheets found matching your criteria.', 'oup') . '</p>';
    }

    wp_die();
}