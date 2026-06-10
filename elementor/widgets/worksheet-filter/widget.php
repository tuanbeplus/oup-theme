<?php

namespace OupElementorWidgets\Widgets\WorksheetFilter;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Widget_WorksheetFilter extends Widget_Base
{

    public function get_name()
    {
        return 'worksheet-filter';
    }
    public function get_title()
    {
        return __('Worksheet Filter', 'oup');
    }
    public function get_icon()
    {
        return 'eicon-search-results';
    }
    public function get_categories()
    {
        return ['oup'];
    }
    public function get_script_depends()
    {
        return ['oup-worksheet-filter-script'];
    }
    public function get_style_depends()
    {
        return ['oup-worksheet-filter-style'];
    }

    protected function register_controls()
    {

        $this->start_controls_section('content_section', [
            'label' => __('Content', 'oup'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('posts_per_page', [
            'label'   => __('Posts Per Page', 'oup'),
            'type'    => Controls_Manager::NUMBER,
            'min'     => -1,
            'default' => 9,
        ]);

        $this->add_control('categories_limit', [
            'label'       => __('Categories Limit', 'oup'),
            'type'        => Controls_Manager::NUMBER,
            'min'         => 0,
            'default'     => 0,
            'description' => __('Leave empty to show all categories.', 'oup'),
        ]);

        $this->add_control('orderby', [
            'label'   => __('Order By', 'oup'),
            'type'    => Controls_Manager::SELECT,
            'default' => 'date',
            'options' => [
                'date'       => __('Date', 'oup'),
                'title'      => __('Title', 'oup'),
                'rand'       => __('Random', 'oup'),
                'menu_order' => __('Menu Order', 'oup'),
            ],
        ]);

        $this->add_control('order', [
            'label'   => __('Order', 'oup'),
            'type'    => Controls_Manager::SELECT,
            'default' => 'DESC',
            'options' => ['DESC' => 'DESC', 'ASC' => 'ASC'],
        ]);

        $this->end_controls_section();

        // Layout & Spacing
        $this->start_controls_section('style_section_layout', [
            'label' => __('Layout & Spacing', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('top_bar_margin_bottom', [
            'label'      => __('Space Below Top Bar', 'oup'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'range'      => ['px' => ['min' => 0, 'max' => 100]],
            'selectors'  => ['{{WRAPPER}} .worksheet-top-bar' => 'margin-bottom: {{SIZE}}{{UNIT}};'],
        ]);

        $this->add_responsive_control('grid_gap', [
            'label'      => __('Grid Gap', 'oup'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'range'      => ['px' => ['min' => 0, 'max' => 100]],
            'selectors'  => ['{{WRAPPER}} .worksheet-grid' => 'gap: {{SIZE}}{{UNIT}};'],
        ]);

        $this->end_controls_section();

        // Card
        $this->start_controls_section('style_section_card', [
            'label' => __('Card Settings', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('card_padding', [
            'label'      => __('Content Padding', 'oup'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors'  => ['{{WRAPPER}} .worksheet-card-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->add_control('card_bg_color', [
            'label'     => __('Background Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .worksheet-card-item, {{WRAPPER}} .worksheet-card-content' => 'background-color: {{VALUE}};'],
        ]);

        $this->add_control('card_border_radius', [
            'label'      => __('Border Radius', 'oup'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors'  => [
                '{{WRAPPER}} .worksheet-card-item'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .worksheet-card-image'   => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
                '{{WRAPPER}} .worksheet-card-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // Title
        $this->start_controls_section('style_section_title', [
            'label' => __('Title', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('title_color', [
            'label'     => __('Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .worksheet-card-title' => 'color: {{VALUE}};'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'selector' => '{{WRAPPER}} .worksheet-card-title',
        ]);

        $this->add_responsive_control('title_margin', [
            'label'      => __('Margin', 'oup'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors'  => ['{{WRAPPER}} .worksheet-card-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->end_controls_section();

        // Badge
        $this->start_controls_section('style_section_badge', [
            'label' => __('Badge (Category)', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('badge_color', [
            'label'     => __('Text Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .worksheet-card-badge' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('badge_bg_color', [
            'label'     => __('Background Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .worksheet-card-badge' => 'background-color: {{VALUE}};'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'badge_typography',
            'selector' => '{{WRAPPER}} .worksheet-card-badge',
        ]);

        $this->add_control('badge_border_radius', [
            'label'      => __('Border Radius', 'oup'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors'  => ['{{WRAPPER}} .worksheet-card-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->end_controls_section();

        // Top Filter Buttons
        $this->start_controls_section('style_section_top_filters', [
            'label' => __('Top Filter Buttons', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('filter_buttons_gap', [
            'label'      => __('Gap Between Buttons', 'oup'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'range'      => ['px' => ['min' => 0, 'max' => 50]],
            'selectors'  => ['{{WRAPPER}} .worksheet-filters' => 'gap: {{SIZE}}{{UNIT}};'],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'top_filter_typography',
            'selector' => '{{WRAPPER}} .worksheet-filters .filter-btn',
        ]);

        $this->start_controls_tabs('top_filter_tabs');

        $this->start_controls_tab('top_filter_normal', ['label' => __('Normal', 'oup')]);

        $this->add_control('top_filter_color', [
            'label'     => __('Text Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .worksheet-filters .filter-btn' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('top_filter_bg', [
            'label'     => __('Background Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .worksheet-filters .filter-btn' => 'background-color: {{VALUE}};'],
        ]);

        $this->add_control('top_filter_border_color', [
            'label'     => __('Border Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .worksheet-filters .filter-btn' => 'border-color: {{VALUE}};'],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('top_filter_active', ['label' => __('Hover & Active', 'oup')]);

        $this->add_control('top_filter_color_active', [
            'label'     => __('Text Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .worksheet-filters .filter-btn:hover, {{WRAPPER}} .worksheet-filters .filter-btn.active' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('top_filter_bg_active', [
            'label'     => __('Background Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .worksheet-filters .filter-btn:hover, {{WRAPPER}} .worksheet-filters .filter-btn.active' => 'background-color: {{VALUE}};'],
        ]);

        $this->add_control('top_filter_border_color_active', [
            'label'     => __('Border Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .worksheet-filters .filter-btn:hover, {{WRAPPER}} .worksheet-filters .filter-btn.active' => 'border-color: {{VALUE}};'],
        ]);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control('top_filter_border_radius', [
            'label'      => __('Border Radius', 'oup'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'separator'  => 'before',
            'selectors'  => ['{{WRAPPER}} .worksheet-filters .filter-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->end_controls_section();

        // Search Box
        $this->start_controls_section('style_section_search', [
            'label' => __('Search Box', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'search_typography',
            'label'    => __('Input Typography', 'oup'),
            'selector' => '{{WRAPPER}} #worksheet-search-input',
        ]);

        $this->add_control('search_text_color', [
            'label'     => __('Text Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} #worksheet-search-input' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('search_placeholder_color', [
            'label'     => __('Placeholder Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} #worksheet-search-input::placeholder'              => 'color: {{VALUE}};',
                '{{WRAPPER}} #worksheet-search-input::-webkit-input-placeholder' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('search_bg_color', [
            'label'     => __('Background Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'separator' => 'before',
            'selectors' => ['{{WRAPPER}} .search-input-wrapper' => 'background-color: {{VALUE}};'],
        ]);

        $this->add_control('search_border_color', [
            'label'     => __('Border Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .search-input-wrapper' => 'border-color: {{VALUE}};'],
        ]);

        $this->add_control('search_border_radius', [
            'label'      => __('Border Radius', 'oup'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors'  => ['{{WRAPPER}} .search-input-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->add_control('search_icon_color', [
            'label'     => __('Icon Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'separator' => 'before',
            'selectors' => ['{{WRAPPER}} .search-input-wrapper .search-icon path' => 'stroke: {{VALUE}};'],
        ]);

        $this->end_controls_section();
    }

    public static function render_card($post_id)
    {
        $title     = get_the_title($post_id);
        $permalink = get_permalink($post_id);
        $thumbnail = get_the_post_thumbnail_url($post_id, 'medium_large');
        $alt       = '';

        if ($thumbnail) {
            $thumbnail_id = get_post_thumbnail_id($post_id);
            $alt          = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
        }

        if (empty($alt)) {
            $alt = $title;
        }

        $categories = wp_get_post_terms($post_id, 'worksheet-category');
?>
        <article class="worksheet-card-item">
            <a href="<?php echo esc_url($permalink); ?>" class="worksheet-card-link">
                <div class="worksheet-card-image">
                    <?php if ($thumbnail) : ?>
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy">
                    <?php endif; ?>
                </div>
                <div class="worksheet-card-content">
                    <h3 class="worksheet-card-title"><?php echo esc_html($title); ?></h3>
                    <div class="worksheet-card-footer">
                        <?php if (! empty($categories) && ! is_wp_error($categories)) : ?>
                            <span class="worksheet-card-badge"><?php echo esc_html($categories[0]->name); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </article>
    <?php
    }

    protected function render()
    {
        $settings       = $this->get_settings_for_display();
        $posts_per_page = isset($settings['posts_per_page']) && $settings['posts_per_page'] !== '' ? intval($settings['posts_per_page']) : 9;
        $categories_limit = ! empty($settings['categories_limit']) ? absint($settings['categories_limit']) : 0;
        $orderby        = ! empty($settings['orderby']) ? esc_attr($settings['orderby']) : 'date';
        $order          = ! empty($settings['order']) ? esc_attr($settings['order']) : 'DESC';
        $nonce          = wp_create_nonce('worksheet_filter_nonce');
    ?>
        <div class="worksheet-filter-wrapper"
            data-posts-per-page="<?php echo esc_attr($posts_per_page); ?>"
            data-orderby="<?php echo esc_attr($orderby); ?>"
            data-order="<?php echo esc_attr($order); ?>"
            data-nonce="<?php echo esc_attr($nonce); ?>"
            data-ajaxurl="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">

            <div class="worksheet-top-bar">
                <div class="worksheet-filters">
                    <button class="filter-btn active" data-filter="*"><?php esc_html_e('All', 'oup'); ?></button>
                    <?php
                    $term_args = ['taxonomy' => 'worksheet-category', 'hide_empty' => false];
                    if ($categories_limit > 0) {
                        $term_args['number'] = $categories_limit;
                    }
                    $terms = get_terms($term_args);
                    if (! is_wp_error($terms) && ! empty($terms)) {
                        foreach ($terms as $term) {
                            printf(
                                '<button class="filter-btn" data-filter="%s">%s</button>',
                                esc_attr($term->slug),
                                esc_html($term->name)
                            );
                        }
                    }
                    ?>
                </div>

                <div class="worksheet-search">
                    <div class="search-input-wrapper">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="#231F20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <input type="text" id="worksheet-search-input" placeholder="<?php esc_attr_e('Search worksheet', 'oup'); ?>">
                    </div>
                </div>
            </div>

            <div id="worksheet-ajax-results" class="worksheet-grid">
                <?php
                $query = new \WP_Query([
                    'post_type'      => 'worksheet',
                    'posts_per_page' => $posts_per_page,
                    'orderby'        => $orderby,
                    'order'          => $order,
                    'post_status'    => 'publish',
                ]);

                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        static::render_card(get_the_ID());
                    }
                    wp_reset_postdata();
                } else {
                    echo '<p class="no-results-msg">' . esc_html__('No worksheets found.', 'oup') . '</p>';
                }
                ?>
            </div>

        </div>
<?php
    }

    protected function content_template() {}
}