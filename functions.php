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

/* Widgets Load */
require_once get_stylesheet_directory() . '/elementor/widgets-load.php';

/* WooCommerce */
require_once get_stylesheet_directory() . '/inc/woo.php';

// Post Filter
if (! defined('ABSPATH')) exit;
add_action('wp_ajax_oup_load_archive_posts',        'oup_ajax_load_archive_posts');
add_action('wp_ajax_nopriv_oup_load_archive_posts', 'oup_ajax_load_archive_posts');

function oup_ajax_load_archive_posts()
{
    $paged     = max(1, (int) ($_POST['paged']     ?? 1));
    $per_page  = max(1, (int) ($_POST['per_page']  ?? 6));
    $terms_raw = sanitize_text_field($_POST['terms']     ?? 'all');
    $post_type = sanitize_key($_POST['post_type'] ?? 'post');
    $taxonomy  = sanitize_key($_POST['taxonomy']  ?? 'category');
    $orderby   = in_array($_POST['orderby'] ?? '', ['date', 'title'], true) ? $_POST['orderby'] : 'date';
    $order     = in_array(strtoupper($_POST['order'] ?? ''), ['ASC', 'DESC'], true) ? strtoupper($_POST['order']) : 'DESC';

    $args = [
        'post_type'      => $post_type,
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'orderby'        => $orderby,
        'order'          => $order,
    ];

    if ($terms_raw !== 'all') {
        $term_ids = array_values(array_filter(
            array_map('intval', explode(',', $terms_raw)),
            fn($id) => $id > 0
        ));

        if (! empty($term_ids)) {
            if (count($term_ids) === 1) {
                $args['tax_query'] = [[
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => [$term_ids[0]],
                    'operator' => 'IN',
                ]];
            } else {
                $tax_query = ['relation' => 'AND'];
                foreach ($term_ids as $tid) {
                    $tax_query[] = [
                        'taxonomy' => $taxonomy,
                        'field'    => 'term_id',
                        'terms'    => [$tid],
                        'operator' => 'IN',
                    ];
                }
                $args['tax_query'] = $tax_query;
            }
        }
    }

    $query = new WP_Query($args);
    $found = $query->found_posts;

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            oup_render_archive_post_card(get_the_ID(), $taxonomy);
        }
        wp_reset_postdata();
    }
    $html = ob_get_clean();

    if ($found === 0) {
        $html = '<div class="apf-empty">' . esc_html__('No posts found.', 'oup') . '</div>';
    }

    wp_send_json_success([
        'html'      => $html,
        'max_pages' => max(1, (int) $query->max_num_pages),
    ]);
}

function oup_render_archive_post_card(int $post_id, string $taxonomy = 'category')
{
    $post      = get_post($post_id);
    $date      = get_the_date('j M Y', $post);
    $title     = get_the_title($post);
    $permalink = get_permalink($post);
    $excerpt   = wp_trim_words(get_the_excerpt($post), 28, '…');

    $thumb_html = '';
    if (has_post_thumbnail($post_id)) {
        $thumb_html = get_the_post_thumbnail($post_id, 'large', ['class' => 'apf-card__img']);
    }

    $tags     = wp_get_post_terms($post_id, 'post_tag', ['fields' => 'all', 'orderby' => 'term_id', 'order' => 'ASC']);
    $tag_html = '';
    if (! is_wp_error($tags) && ! empty($tags)) {
        $tag_html = sprintf(
            '<span class="apf-card__tag %s">%s</span>',
            esc_attr($tags[0]->slug),
            esc_html($tags[0]->name)
        );
    }

    $author_id   = (int) $post->post_author;
    $author_name = get_the_author_meta('display_name', $author_id);
    $user        = get_userdata($author_id);
    $author_role = '';
    if ($user && ! empty($user->roles)) {
        $wp_roles    = wp_roles();
        $role_slug   = $user->roles[0];
        $author_role = $wp_roles->roles[$role_slug]['name'] ?? ucfirst($role_slug);
    }
?>
    <article class="apf-card" data-post-id="<?= esc_attr($post_id) ?>">
        <a href="<?= esc_url($permalink) ?>" class="apf-card__inner" tabindex="0">
            <?php if ($thumb_html) : ?>
                <div class="apf-card__thumb"><?= $thumb_html ?></div>
            <?php endif; ?>
            <div class="apf-card__body">
                <div class="apf-card__meta">
                    <time class="apf-card__date" datetime="<?= esc_attr(get_the_date('Y-m-d', $post)) ?>">
                        <?= esc_html($date) ?>
                    </time>
                    <?= $tag_html ?>
                </div>
                <h3 class="apf-card__title"><?= esc_html($title) ?></h3>
                <?php if ($excerpt) : ?>
                    <p class="apf-card__excerpt"><?= esc_html($excerpt) ?></p>
                <?php endif; ?>
                <div class="apf-card__author">
                    <span class="apf-card__author-name"><?= esc_html($author_name) ?></span>
                    <?php if ($author_role) : ?>
                        <span class="apf-card__author-role"><?= esc_html($author_role) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </article>
<?php
}
