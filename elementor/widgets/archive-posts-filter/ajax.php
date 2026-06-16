<?php

if (! defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_oup_load_archive_posts',        'oup_ajax_load_archive_posts');
add_action('wp_ajax_nopriv_oup_load_archive_posts', 'oup_ajax_load_archive_posts');

function oup_ajax_load_archive_posts()
{
    $paged     = max(1, (int) ($_POST['paged'] ?? 1));
    $per_page  = max(1, (int) ($_POST['per_page'] ?? 6));
    $terms_raw = sanitize_text_field($_POST['terms'] ?? 'all');
    $post_type = sanitize_key($_POST['post_type'] ?? 'post');
    $taxonomy  = sanitize_key($_POST['taxonomy'] ?? 'category');
    $search    = sanitize_text_field($_POST['search'] ?? '');

    $orderby = in_array($_POST['orderby'] ?? '', ['date', 'title'], true) ? $_POST['orderby'] : 'date';
    $order   = in_array(strtoupper($_POST['order'] ?? ''), ['ASC', 'DESC'], true) ? strtoupper($_POST['order']) : 'DESC';

    $args = [
        'post_type'      => $post_type,
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'orderby'        => $orderby,
        'order'          => $order,
    ];

    if ($search !== '') {
        $args['s'] = $search;
    }

    // Single-term filter only — take the first valid term_id
    if ($search === '' && $terms_raw !== 'all') {
        $term_id = (int) $terms_raw;
        if ($term_id > 0) {
            $args['tax_query'] = [[
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => [$term_id],
                'operator' => 'IN',
            ]];
        }
    }

    if ($search !== '') {
        add_filter('posts_search', 'oup_search_include_excerpt', 10, 2);
    }

    $query = new WP_Query($args);

    if ($search !== '') {
        remove_filter('posts_search', 'oup_search_include_excerpt', 10);
    }

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            oup_render_archive_post_card(get_the_ID(), $taxonomy);
        }
        wp_reset_postdata();
    }
    $html = ob_get_clean();

    if ($query->found_posts === 0) {
        $html = '<div class="apf-empty">' . esc_html__('No posts found.', 'oup') . '</div>';
    }

    wp_send_json_success(['html' => $html, 'max_pages' => max(1, (int) $query->max_num_pages)]);
}

function oup_search_include_excerpt($search, $wp_query)
{
    global $wpdb;
    if (empty($search)) return $search;

    $q    = $wp_query->query_vars;
    $n    = ! empty($q['exact']) ? '' : '%';
    $term = $wpdb->esc_like($q['s']);
    $search = " AND (
        ({$wpdb->posts}.post_title LIKE '{$n}{$term}{$n}')
        OR ({$wpdb->posts}.post_excerpt LIKE '{$n}{$term}{$n}')
    )";

    return $search;
}

function oup_render_archive_post_card(int $post_id, string $taxonomy = 'category')
{
    include __DIR__ . '/render-card.php';
}
