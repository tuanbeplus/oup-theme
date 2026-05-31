<?php
/**
 * Archive Post Card Template
 *
 * @package OUP
 */

if (! defined('ABSPATH')) {
    exit;
}

/** @var int    $post_id  Passed from oup_render_archive_post_card() in ajax.php */
/** @var string $taxonomy Passed from oup_render_archive_post_card() in ajax.php — used for filter only, NOT for the tag badge */

// ── Data ──────────────────────────────────────────────────────────────────────

$post_url  = get_permalink($post_id);
$post_title = get_the_title($post_id);
$post_date  = get_the_date('j M Y', $post_id);

// Lấy excerpt trực tiếp từ post object để tránh bị filter của Elementor/WP override
$post_obj     = get_post($post_id);
$post_excerpt = $post_obj->post_excerpt
    ? wp_strip_all_tags($post_obj->post_excerpt)
    : wp_trim_words(wp_strip_all_tags($post_obj->post_content), 25, '…');

// Featured image
$thumb_id  = get_post_thumbnail_id($post_id);
$thumb_url = $thumb_id
    ? wp_get_attachment_image_url($thumb_id, 'large')
    : null;
$thumb_alt = $thumb_id
    ? trim(wp_strip_all_tags(get_post_meta($thumb_id, '_wp_attachment_image_alt', true)))
    : esc_attr($post_title);

// Tag badge — always from 'post_tag', sort by term_id asc, take the first one
$tags = get_the_terms($post_id, 'post_tag');
if (!is_wp_error($tags) && !empty($tags)) {
    usort($tags, fn($a, $b) => $a->term_id - $b->term_id);
    $first_tag = $tags[0];
} else {
    $first_tag = null;
}
$tag_label = $first_tag ? $first_tag->name : '';
$tag_slug  = $first_tag ? $first_tag->slug : '';

// Author
$author_id    = get_post_field('post_author', $post_id);
$author_name  = get_the_author_meta('display_name', $author_id);
$author_roles = get_userdata($author_id)->roles ?? [];
$author_role  = !empty($author_roles) ? ucfirst(reset($author_roles)) : '';

?>
<article class="apf-card" role="listitem">
    <a class="apf-card__inner" href="<?= esc_url($post_url) ?>" aria-label="<?= esc_attr($post_title) ?>">

        <?php if ($thumb_url) : ?>
            <div class="apf-card__thumb">
                <img
                    class="apf-card__img"
                    src="<?= esc_url($thumb_url) ?>"
                    alt="<?= esc_attr($thumb_alt) ?>"
                    loading="lazy"
                    decoding="async"
                >
            </div>
        <?php endif; ?>

        <div class="apf-card__body">

            <div class="apf-card__meta">
                <span class="apf-card__date"><?= esc_html($post_date) ?></span>
                <?php if ($tag_label) : ?>
                    <span class="apf-card__tag <?= esc_attr($tag_slug) ?>">
                        <?= esc_html($tag_label) ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($post_title) : ?>
                <h3 class="apf-card__title"><?= esc_html($post_title) ?></h3>
            <?php endif; ?>

            <?php if ($post_excerpt) : ?>
                <p class="apf-card__excerpt"><?= esc_html($post_excerpt) ?></p>
            <?php endif; ?>

            <?php if ($author_name || $author_role) : ?>
                <div class="apf-card__author">
                    <?php if ($author_name) : ?>
                        <span class="apf-card__author-name"><?= esc_html($author_name) ?></span>
                    <?php endif; ?>
                    <?php if ($author_role) : ?>
                        <span class="apf-card__author-role"><?= esc_html($author_role) ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div><!-- .apf-card__body -->

    </a><!-- .apf-card__inner -->
</article><!-- .apf-card -->