<?php

namespace OupElementorWidgets\Widgets\BlogSearch;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (! defined('ABSPATH')) {
    exit;
}

class Widget_BlogSearch extends Widget_Base
{
    public function get_name()
    {
        return 'blog-search';
    }
    public function get_title()
    {
        return __('Blog Search', 'oup');
    }
    public function get_icon()
    {
        return 'eicon-search';
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
        return ['oup-blog-search-style'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', ['label' => __('Content', 'oup')]);
        $this->add_control('placeholder', ['label' => __('Placeholder text', 'oup'), 'type' => Controls_Manager::TEXT, 'default' => __('Search blog', 'oup')]);
        $this->add_control('debounce',    ['label' => __('Debounce (ms)', 'oup'),    'type' => Controls_Manager::NUMBER, 'default' => 400, 'min' => 0]);
        $this->end_controls_section();

        $this->start_controls_section('section_style', ['label' => __('Search Box', 'oup'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('max_width',   ['label' => __('Max Width', 'oup'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%'], 'default' => ['unit' => 'px', 'size' => 600], 'selectors' => ['{{WRAPPER}} .bs-wrap' => 'max-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('icon_color',  ['label' => __('Icon Color', 'oup'),  'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .bs-icon path' => 'stroke: {{VALUE}};']]);
        $this->add_control('border_color', ['label' => __('Border Color', 'oup'), 'type' => Controls_Manager::COLOR, 'default' => '#0091B3', 'selectors' => ['{{WRAPPER}} .bs-wrap' => 'border-color: {{VALUE}};']]);
        $this->add_control('bg_color',    ['label' => __('Background', 'oup'),   'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .bs-wrap' => 'background: {{VALUE}};']]);
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings    = $this->get_settings_for_display();
        $placeholder = esc_attr($settings['placeholder'] ?? __('Search blog', 'oup'));
        $debounce    = (int) ($settings['debounce'] ?? 400);
?>
        <div class="blog-search-widget" data-debounce="<?= esc_attr($debounce) ?>">
            <div class="bs-wrap">
                <svg class="bs-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z"
                        stroke="#231F20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <input
                    class="bs-input"
                    type="search"
                    placeholder="<?= $placeholder ?>"
                    autocomplete="off"
                    aria-label="<?= $placeholder ?>">
            </div>
        </div>
<?php
    }
}
