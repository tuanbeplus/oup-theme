<?php

namespace OupElementorWidgets\Widgets\ArchivePostsFilter;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (! defined('ABSPATH')) {
    exit;
}

class Widget_ArchivePostsFilter extends Widget_Base
{
    public function get_name()
    {
        return 'archive-posts-filter';
    }
    public function get_title()
    {
        return __('Archive Posts Filter', 'oup');
    }
    public function get_icon()
    {
        return 'eicon-filter';
    }
    public function get_categories()
    {
        return ['oup'];
    }
    public function get_script_depends()
    {
        return ['oup-archive-posts-filter-script'];
    }
    public function get_style_depends()
    {
        return ['oup-archive-posts-filter-style'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_settings', ['label' => __('Settings', 'oup')]);

        $this->add_control('posts_per_page', [
            'label'   => __('Posts per page', 'oup'),
            'type'    => Controls_Manager::NUMBER,
            'default' => 6,
            'min'     => 1,
        ]);
        $this->add_control('post_type', [
            'label'   => __('Post Type', 'oup'),
            'type'    => Controls_Manager::TEXT,
            'default' => 'post',
        ]);
        $this->add_control('taxonomy', [
            'label'       => __('Taxonomy', 'oup'),
            'type'        => Controls_Manager::TEXT,
            'default'     => 'category',
            'description' => __('Taxonomy slug (e.g. category, post_tag, or a custom taxonomy).', 'oup'),
        ]);
        $this->add_control('orderby', [
            'label'   => __('Order By', 'oup'),
            'type'    => Controls_Manager::SELECT,
            'default' => 'date',
            'options' => ['date' => __('Date', 'oup'), 'title' => __('Title', 'oup')],
        ]);
        $this->add_control('order', [
            'label'   => __('Order', 'oup'),
            'type'    => Controls_Manager::SELECT,
            'default' => 'DESC',
            'options' => ['DESC' => __('Descending', 'oup'), 'ASC' => __('Ascending', 'oup')],
        ]);

        $this->end_controls_section();

        // Style: Tabs
        $this->start_controls_section('style_tabs', ['label' => __('Filter Tabs', 'oup'), 'tab' => Controls_Manager::TAB_STYLE]);

        $this->add_control('tabs_gap', [
            'label' => __('Gap between tabs', 'oup'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'selectors'  => ['{{WRAPPER}} .apf-tabs' => 'gap: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_control('tabs_bottom_spacing', [
            'label' => __('Bottom spacing', 'oup'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'selectors'  => ['{{WRAPPER}} .apf-tabs' => 'margin-bottom: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'tab_typography',
            'selector' => '{{WRAPPER}} .apf-tab',
        ]);
        $this->add_responsive_control('tab_padding', [
            'label' => __('Padding', 'oup'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', 'rem'],
            'selectors'  => ['{{WRAPPER}} .apf-tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->end_controls_section();

        // Style: Grid
        $this->start_controls_section('style_grid', ['label' => __('Posts Grid', 'oup'), 'tab' => Controls_Manager::TAB_STYLE]);

        $this->add_responsive_control('grid_columns', [
            'label'          => __('Columns', 'oup'),
            'type'           => Controls_Manager::SELECT,
            'default'        => '3',
            'tablet_default' => '2',
            'mobile_default' => '1',
            'options'        => ['1' => '1', '2' => '2', '3' => '3', '4' => '4'],
            'selectors'      => ['{{WRAPPER}} .apf-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);'],
        ]);
        $this->add_control('grid_gap', [
            'label' => __('Gap', 'oup'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'selectors'  => ['{{WRAPPER}} .apf-grid' => 'gap: {{SIZE}}{{UNIT}};'],
        ]);

        $this->end_controls_section();
    }

    // Single-term only — no multi-term AND logic needed
    private function build_query_args(
        ?int   $term_id,
        string $post_type,
        string $taxonomy,
        int    $per_page,
        string $orderby,
        string $order,
        int    $paged = 1
    ): array {
        $args = [
            'post_type'      => $post_type,
            'posts_per_page' => $per_page,
            'paged'          => $paged,
            'post_status'    => 'publish',
            'orderby'        => $orderby,
            'order'          => $order,
        ];

        if ($term_id !== null) {
            $args['tax_query'] = [[
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => [$term_id],
                'operator' => 'IN',
            ]];
        }

        return $args;
    }

    protected function render()
    {
        $settings  = $this->get_settings_for_display();
        $per_page  = max(1, (int) ($settings['posts_per_page'] ?? 6));
        $post_type = sanitize_key($settings['post_type'] ?? 'post');
        $taxonomy  = sanitize_key($settings['taxonomy'] ?? 'category');
        $orderby   = $settings['orderby'] ?? 'date';
        $order     = $settings['order'] ?? 'DESC';

        $raw_terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ]);
        if (is_wp_error($raw_terms)) $raw_terms = [];

        $preloaded = [];
        $max_pages = [];

        // Preload "all" tab
        $all_query = new \WP_Query($this->build_query_args(null, $post_type, $taxonomy, $per_page, $orderby, $order, 1));
        ob_start();
        if ($all_query->have_posts()) {
            while ($all_query->have_posts()) {
                $all_query->the_post();
                oup_render_archive_post_card(get_the_ID(), $taxonomy);
            }
            wp_reset_postdata();
        }
        $all_html = ob_get_clean();
        $preloaded['all'] = $all_query->found_posts > 0
            ? $all_html
            : '<div class="apf-empty">' . esc_html__('No posts found.', 'oup') . '</div>';
        $max_pages['all'] = max(1, (int) $all_query->max_num_pages);

        // Preload each single term tab
        foreach ($raw_terms as $term) {
            $tid   = (string) $term->term_id;
            $query = new \WP_Query(
                $this->build_query_args((int) $tid, $post_type, $taxonomy, $per_page, $orderby, $order, 1)
            );

            ob_start();
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    oup_render_archive_post_card(get_the_ID(), $taxonomy);
                }
                wp_reset_postdata();
            }
            $term_html = ob_get_clean();

            $preloaded[$tid] = $query->found_posts > 0
                ? $term_html
                : '<div class="apf-empty">' . esc_html__('No posts found.', 'oup') . '</div>';
            $max_pages[$tid] = max(1, (int) $query->max_num_pages);
        }
?>
        <div
            class="archive-posts-filter-widget"
            data-post-type="<?= esc_attr($post_type) ?>"
            data-taxonomy="<?= esc_attr($taxonomy) ?>"
            data-per-page="<?= esc_attr($per_page) ?>"
            data-orderby="<?= esc_attr($orderby) ?>"
            data-order="<?= esc_attr($order) ?>"
            data-max-pages='<?= esc_attr(wp_json_encode($max_pages)) ?>'>

            <?php if (! empty($raw_terms)) : ?>
                <div class="apf-tabs" role="tablist" aria-label="<?= esc_attr__('Filter posts by category', 'oup') ?>">
                    <button class="apf-tab active" data-term="all" role="tab" aria-selected="true">
                        <?= esc_html__('All', 'oup') ?>
                    </button>
                    <?php foreach ($raw_terms as $term) : ?>
                        <button class="apf-tab" data-term="<?= esc_attr($term->term_id) ?>" role="tab" aria-selected="false">
                            <?= esc_html($term->name) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="apf-grid" role="list">
                <?= $preloaded['all'] ?? '' ?>
            </div>

            <div class="apf-sentinel" aria-hidden="true"></div>

            <script type="application/json" class="apf-preloaded-data">
                <?= wp_json_encode($preloaded) ?>
            </script>
        </div>
<?php
    }
}