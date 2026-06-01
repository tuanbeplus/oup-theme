<?php

namespace OupElementorWidgets\Widgets\BlogDetailToc;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit;

class Widget_BlogDetailToc extends Widget_Base
{
    public function get_name()
    {
        return 'blog-detail-toc';
    }

    public function get_title()
    {
        return __('Blog Detail TOC', 'oup');
    }

    public function get_icon()
    {
        return 'eicon-table-of-contents';
    }

    public function get_categories()
    {
        return ['oup'];
    }

    public function get_style_depends()
    {
        return ['oup-blog-detail-toc-style'];
    }

    public function get_script_depends()
    {
        return ['oup-blog-detail-toc-script'];
    }

    protected function register_controls()
    {
        // Content
        $this->start_controls_section('section_content', [
            'label' => __('Content', 'oup'),
        ]);

        $this->add_control('html_tag', [
            'label'   => __('HTML Tag', 'oup'),
            'type'    => Controls_Manager::SELECT,
            'default' => 'div',
            'options' => ['div' => 'div', 'nav' => 'nav', 'aside' => 'aside'],
        ]);

        $this->add_control('toc_title', [
            'label'   => __('Title', 'oup'),
            'type'    => Controls_Manager::TEXT,
            'default' => __('Table of Contents', 'oup'),
        ]);

        $this->add_control('anchors_by_tags', [
            'label'    => __('Include Tags', 'oup'),
            'type'     => Controls_Manager::SELECT2,
            'multiple' => true,
            'options'  => ['h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6'],
            'default'  => ['h2'],
        ]);

        $this->add_control('exclude_tags', [
            'label'    => __('Exclude Tags', 'oup'),
            'type'     => Controls_Manager::SELECT2,
            'multiple' => true,
            'options'  => ['h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6'],
            'default'  => [],
        ]);

        $this->add_control('container', [
            'label'       => __('Container', 'oup'),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => '.elementor-section',
            'description' => __('Confine TOC to headings inside a specific CSS selector.', 'oup'),
        ]);

        $this->add_control('marker_view', [
            'label'   => __('Marker View', 'oup'),
            'type'    => Controls_Manager::SELECT,
            'default' => 'bullets',
            'options' => [
                'bullets' => __('Bullets', 'oup'),
                'number'  => __('Number', 'oup'),
            ],
        ]);

        $this->add_control('show_icon', [
            'label'        => __('Show Icon', 'oup'),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __('Show', 'oup'),
            'label_off'    => __('Hide', 'oup'),
            'return_value' => 'yes',
            'default'      => '',
            'condition'    => ['marker_view' => 'bullets'],
        ]);

        $this->add_control('icon', [
            'label'     => __('Icon', 'oup'),
            'type'      => Controls_Manager::ICONS,
            'default'   => ['value' => 'fas fa-circle', 'library' => 'fa-solid'],
            'condition' => ['marker_view' => 'bullets', 'show_icon' => 'yes'],
        ]);

        $this->add_control('no_headings_message', [
            'label'   => __('No Headings Found Message', 'oup'),
            'type'    => Controls_Manager::TEXT,
            'default' => __('No headings were found on this page.', 'oup'),
        ]);

        $this->add_control('word_wrap', [
            'label'        => __('Word Wrap', 'oup'),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __('Yes', 'oup'),
            'label_off'    => __('No', 'oup'),
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->end_controls_section();

        // Style: Typography
        $this->start_controls_section('section_style_typography', [
            'label' => __('Typography', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'heading_typography',
            'selector' => '{{WRAPPER}} .table-of-content-all__heading',
        ]);

        $this->end_controls_section();

        // Style: Link States
        $this->start_controls_section('section_style_states', [
            'label' => __('Link States', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->start_controls_tabs('tabs_heading_states');

        // Normal
        $this->start_controls_tab('tab_normal', ['label' => __('Normal', 'oup')]);

        $this->add_control('heading_color', [
            'label'     => __('Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .table-of-content-all__heading' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('heading_bg_color', [
            'label'     => __('Background', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .table-of-content-all__item' => 'background-color: {{VALUE}};'],
        ]);

        $this->end_controls_tab();

        // Hover
        $this->start_controls_tab('tab_hover', ['label' => __('Hover', 'oup')]);

        $this->add_control('heading_color_hover', [
            'label'     => __('Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .table-of-content-all__item:hover .table-of-content-all__heading' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('heading_bg_color_hover', [
            'label'     => __('Background', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .table-of-content-all__item:hover' => 'background-color: {{VALUE}};'],
        ]);

        $this->add_control('heading_hover_transition', [
            'label'     => __('Transition Duration (s)', 'oup'),
            'type'      => Controls_Manager::SLIDER,
            'range'     => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.05]],
            'selectors' => [
                '{{WRAPPER}} .table-of-content-all__heading' => 'transition: color {{SIZE}}s ease;',
                '{{WRAPPER}} .table-of-content-all__item'    => 'transition: background-color {{SIZE}}s ease;',
                '{{WRAPPER}} .table-of-content-all__icon i'  => 'transition: color {{SIZE}}s ease;',
                '{{WRAPPER}} .table-of-content-all__marker'  => 'transition: color {{SIZE}}s ease;',
            ],
        ]);

        $this->end_controls_tab();

        // Active
        $this->start_controls_tab('tab_active', ['label' => __('Active', 'oup')]);

        $this->add_control('heading_color_active', [
            'label'     => __('Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .table-of-content-all__item.active .table-of-content-all__heading' => 'color: {{VALUE}};'],
        ]);

        $this->add_control('heading_bg_color_active', [
            'label'     => __('Background', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .table-of-content-all__item.active' => 'background-color: {{VALUE}};'],
        ]);

        $this->add_control('icon_color_active', [
            'label'     => __('Icon / Marker Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .table-of-content-all__item.active .table-of-content-all__icon i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .table-of-content-all__item.active .table-of-content-all__marker' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();

        // Style: Spacing
        $this->start_controls_section('section_style_spacing', [
            'label' => __('Spacing', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('heading_spacing', [
            'label'     => __('Spacing Between Headings', 'oup'),
            'type'      => Controls_Manager::SLIDER,
            'range'     => ['px' => ['min' => 0, 'max' => 40]],
            'selectors' => ['{{WRAPPER}} .table-of-content-all__item' => 'margin-bottom: {{SIZE}}{{UNIT}};'],
        ]);

        $this->add_control('icon_spacing', [
            'label'     => __('Spacing Between Icon and Heading', 'oup'),
            'type'      => Controls_Manager::SLIDER,
            'range'     => ['px' => ['min' => 0, 'max' => 40]],
            'selectors' => [
                '{{WRAPPER}} .table-of-content-all__icon'   => 'margin-right: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .table-of-content-all__marker' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
            'condition' => ['marker_view' => 'bullets', 'show_icon' => 'yes'],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $tag             = $settings['html_tag'] ?: 'div';
        $anchors         = $settings['anchors_by_tags'] ?: ['h2'];
        $exclude_tags    = $settings['exclude_tags'] ?: [];
        $container       = $settings['container'] ?: '';
        $marker_view     = $settings['marker_view'];
        $show_icon       = $settings['show_icon'] === 'yes';
        $icon            = $settings['icon'] ?? null;
        $toc_title       = $settings['toc_title'] ?: __('Table of Contents', 'oup');
        $no_headings_msg = $settings['no_headings_message'];
        $word_wrap       = $settings['word_wrap'] === 'yes';

        $toc_id       = 'toc-' . uniqid();
        $list_classes = 'table-of-content-all__list' . ($word_wrap ? ' table-of-content-all__wrap' : '');

        printf(
            '<%1$s class="table-of-content-all all" id="%2$s"
                data-toc-tags="%3$s"
                data-toc-exclude="%4$s"
                %5$s
                data-toc-marker="%6$s"
                data-toc-icon="%7$s"
                data-toc-noheadings="%8$s"
            >',
            esc_attr($tag),
            esc_attr($toc_id),
            esc_attr(implode(',', $anchors)),
            esc_attr(implode(',', $exclude_tags)),
            $container ? 'data-toc-container="' . esc_attr($container) . '"' : '',
            esc_attr($marker_view),
            esc_attr($show_icon ? ($icon['value'] ?? '') : ''),
            esc_attr($no_headings_msg)
        );

        if ($toc_title) {
            echo '<p class="table-of-content-all__title">' . esc_html($toc_title) . '</p>';
        }
        echo '<ul class="' . esc_attr($list_classes) . '"></ul>';
        echo '</' . esc_attr($tag) . '>';
    }
}
