<?php

namespace OupElementorWidgets\Widgets\BlogDetailBreadcrumb;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (! defined('ABSPATH')) {
    exit;
}

class Widget_BlogDetailBreadcrumb extends Widget_Base
{

    public function get_name()
    {
        return 'blog-detail-breadcrumb';
    }
    public function get_title()
    {
        return __('Blog Detail Breadcrumb', 'oup');
    }
    public function get_icon()
    {
        return 'eicon-navigation-horizontal';
    }
    public function get_categories()
    {
        return ['oup'];
    }
    public function get_style_depends()
    {
        return ['oup-blog-detail-breadcrumb-style'];
    }

    protected function register_controls()
    {

        $this->start_controls_section('section_settings', [
            'label' => __('Settings', 'oup'),
        ]);

        $this->add_control('blog_label', [
            'label'   => __('Blog Label', 'oup'),
            'type'    => Controls_Manager::TEXT,
            'default' => __('Blog', 'oup'),
        ]);

        $this->add_control('blog_url', [
            'label'       => __('Blog Page URL', 'oup'),
            'type'        => Controls_Manager::URL,
            'placeholder' => home_url('/blog/'),
            'default'     => ['url' => home_url('/blog/')],
        ]);

        $this->add_control('taxonomy', [
            'label'       => __('Category Taxonomy', 'oup'),
            'type'        => Controls_Manager::TEXT,
            'default'     => 'category',
            'description' => __('Taxonomy used for the middle crumb (e.g. category, post_tag, or a custom taxonomy).', 'oup'),
        ]);

        $this->add_control('tag_taxonomy', [
            'label'       => __('Tag Taxonomy (for badge color)', 'oup'),
            'type'        => Controls_Manager::TEXT,
            'default'     => 'post_tag',
            'description' => __('Taxonomy whose first term slug drives the pill background color. Must match the slugs in the CSS tag map.', 'oup'),
        ]);

        $this->end_controls_section();

        $this->start_controls_section('style_section', [
            'label' => __('Breadcrumb', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'crumb_typography',
            'selector' => '{{WRAPPER}} .bdb-crumb, {{WRAPPER}} .bdb-separator',
        ]);

        $this->add_control('crumb_color', [
            'label'     => __('Text Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .bdb-crumb'     => 'color: {{VALUE}};',
                '{{WRAPPER}} .bdb-separator' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('crumb_link_color', [
            'label'     => __('Link Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .bdb-crumb a' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('crumb_link_hover_color', [
            'label'     => __('Link Hover Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#0091B3',
            'selectors' => ['{{WRAPPER}} .bdb-crumb a:hover' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('separator_char', [
            'label'   => __('Separator', 'oup'),
            'type'    => Controls_Manager::TEXT,
            'default' => '>',
        ]);

        $this->add_control('crumb_gap', [
            'label'      => __('Gap between items', 'oup'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'default'    => ['size' => 10, 'unit' => 'px'],
            'selectors'  => ['{{WRAPPER}} .bdb-breadcrumb' => 'gap: {{SIZE}}{{UNIT}};'],
        ]);

        $this->add_control('pill_padding', [
            'label'      => __('Pill Padding', 'oup'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', 'rem'],
            'default'    => [
                'top'      => '5',
                'right'    => '18',
                'bottom'   => '5',
                'left'     => '18',
                'unit'     => 'px',
                'isLinked' => false,
            ],
            'selectors' => [
                '{{WRAPPER}} .bdb-pill' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('pill_radius', [
            'label'      => __('Pill Border Radius', 'oup'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'default'    => ['size' => 30, 'unit' => 'px'],
            'selectors'  => ['{{WRAPPER}} .bdb-pill' => 'border-radius: {{SIZE}}{{UNIT}};'],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings     = $this->get_settings_for_display();
        $post_id      = get_the_ID();
        $taxonomy     = sanitize_key($settings['taxonomy']     ?? 'category');
        $tag_taxonomy = sanitize_key($settings['tag_taxonomy'] ?? 'post_tag');
        $blog_label   = esc_html($settings['blog_label'] ?: __('Blog', 'oup'));
        $blog_url     = esc_url($settings['blog_url']['url'] ?? home_url('/blog/'));
        $separator    = esc_html($settings['separator_char'] ?: '>');
        $post_title   = get_the_title($post_id);

        $categories = get_the_terms($post_id, $taxonomy);
        $category   = (! is_wp_error($categories) && ! empty($categories)) ? $categories[0] : null;
        $cat_name   = $category ? $category->name : '';
        $cat_url    = $blog_url;

        $tags = get_the_terms($post_id, $tag_taxonomy);
        if (! is_wp_error($tags) && ! empty($tags)) {
            usort($tags, fn($a, $b) => $a->term_id - $b->term_id);
            $tag_slug = sanitize_html_class($tags[0]->slug);
        } else {
            $tag_slug = '';
        }
?>
        <nav class="bdb-breadcrumb-wrapper" aria-label="<?php esc_attr_e('Breadcrumb', 'oup'); ?>">
            <ol class="bdb-breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">

                <li class="bdb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span class="bdb-crumb">
                        <a href="<?php echo $blog_url; ?>" itemprop="item">
                            <span itemprop="name"><?php echo $blog_label; ?></span>
                        </a>
                    </span>
                    <meta itemprop="position" content="1">
                </li>

                <li class="bdb-item bdb-item--sep" aria-hidden="true">
                    <span class="bdb-separator"><?php echo $separator; ?></span>
                </li>

                <?php if ($cat_name) : ?>
                    <li class="bdb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span class="bdb-crumb">
                            <a href="<?php echo $cat_url; ?>" itemprop="item">
                                <span itemprop="name"><?php echo esc_html($cat_name); ?></span>
                            </a>
                        </span>
                        <meta itemprop="position" content="2">
                    </li>

                    <li class="bdb-item bdb-item--sep" aria-hidden="true">
                        <span class="bdb-separator"><?php echo $separator; ?></span>
                    </li>
                <?php endif; ?>

                <li class="bdb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span
                        class="bdb-crumb bdb-pill<?php echo $tag_slug ? ' ' . $tag_slug : ''; ?>"
                        itemprop="name"
                        aria-current="page"
                        title="<?php echo esc_attr($post_title); ?>"><?php echo esc_html($post_title); ?></span>
                    <meta itemprop="position" content="<?php echo $cat_name ? 3 : 2; ?>">
                </li>

            </ol>
        </nav>
<?php
    }
}