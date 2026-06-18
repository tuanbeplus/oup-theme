<?php
if (! defined('ABSPATH')) {
    exit;
}

// Search Hook
add_action('pre_get_posts', 'oup_exclude_search_post_types');
function oup_exclude_search_post_types(WP_Query $query)
{
    if (! $query->is_main_query() || is_admin() || ! $query->is_search()) {
        return;
    }

    $excluded_post_types = [
        'sfwd-topic',
        'sfwd-lessons',
        'elementor_library',
        'sugarcalendar_venue',
        'sc_speakers',
        'e-floating-buttons',   
        'attachment',
        'sfwd-quiz',
        'sfwd-certificates',
        'sfwd-transactions',
        'sfwd-essays',
        'sfwd-assignment',
        'ld-exam',
        'groups',
    ];

    $post_types = get_post_types(['public' => true]);
    $post_types = array_diff($post_types, $excluded_post_types);

    $query->set('post_type', $post_types);
}

add_action( 'plugins_loaded', 'oup_bypass_learndash_stripe_403', 1 );
function oup_bypass_learndash_stripe_403() {
    if ( isset( $_GET['ld_stripe_connect'] ) && $_GET['ld_stripe_connect'] === 'success' ) {
        unset( $_GET['ld_stripe_connect'] );

        unset( $_GET['session_id'] );
    }
}

add_filter('body_class', 'oup_add_custom_class_to_success_page');
function oup_add_custom_class_to_success_page($classes) {
    if (is_page('registration-success')) {
        $classes[] = 'page-registration-success'; 
    }
    return $classes;
}
