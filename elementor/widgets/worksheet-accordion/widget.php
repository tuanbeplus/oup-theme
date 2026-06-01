<?php 
namespace OupElementorWidgets\Widgets\WorksheetAccordion;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

class Widget_WorksheetAccordion extends Widget_Base {
    public function get_name() {
        return 'worksheet-accordion';
    }
    public function get_title() {
        return __( 'Worksheet Accordion', 'oup' );
    }

    public function get_icon() {
        return 'eicon-accordion';
    }

    public function get_categories() {
        return [ 'oup' ];
    }

    public function get_script_depends() {
        return [ 'oup-worksheet-accordion-script' ];
    }

    public function get_style_depends() {
        return [ 'oup-worksheet-accordion-style' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_layout',
            [
                'label' => __( 'Layout', 'oup' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );



        $this->add_control(
            'item_position',
            [
                'label' => __( 'Item Position', 'oup' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __( 'Start', 'oup' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'oup' ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => __( 'End', 'oup' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                    'space-between' => [
                        'title' => __( 'Space Between', 'oup' ),
                        'icon' => 'eicon-h-align-stretch',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-header' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_heading',
            [
                'label' => __( 'Icon', 'oup' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'icon_align',
            [
                'label' => __( 'Position', 'oup' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'row-reverse' => [
                        'title' => __( 'Start', 'oup' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'row' => [
                        'title' => __( 'End', 'oup' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-header' => 'flex-direction: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_expand',
            [
                'label' => __( 'Expand Icon', 'oup' ),
                'type' => Controls_Manager::ICONS,
            ]
        );

        $this->add_control(
            'icon_collapse',
            [
                'label' => __( 'Collapse Icon', 'oup' ),
                'type' => Controls_Manager::ICONS,
                'condition' => [
                    'icon_expand[value]!' => '',
                ],
            ]
        );

        $this->add_control(
            'title_html_tag',
            [
                'label' => __( 'Title HTML Tag', 'oup' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_interactions',
            [
                'label' => __( 'Interactions', 'oup' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'default_state',
            [
                'label' => __( 'Default State', 'oup' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'first_expanded' => __( 'First expanded', 'oup' ),
                    'all_collapsed' => __( 'All collapsed', 'oup' ),
                    'all_expanded' => __( 'All expanded', 'oup' ),
                ],
            ]
        );

        $this->add_control(
            'max_items_expanded',
            [
                'label' => __( 'Max Items Expanded', 'oup' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'one',
                'options' => [
                    'one' => __( 'One', 'oup' ),
                    'multiple' => __( 'Multiple', 'oup' ),
                ],
            ]
        );

        $this->add_control(
            'animation_duration',
            [
                'label' => __( 'Animation Duration', 'oup' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'ms' ],
                'range' => [
                    'ms' => [
                        'min' => 0,
                        'max' => 2000,
                        'step' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'ms',
                    'size' => 400,
                ],
            ]
        );

        $this->end_controls_section();

        // 1. Accordion Style
        $this->start_controls_section(
            'section_accordion_style',
            [
                'label' => __( 'Accordion', 'oup' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );



        $this->add_control(
            'distance_from_content',
            [
                'label' => __( 'Distance from content', 'oup' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-content' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'accordion_tabs' );

        // Normal Tab
        $this->start_controls_tab(
            'accordion_normal',
            [
                'label' => __( 'Normal', 'oup' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'accordion_background_normal',
                'selector' => '{{WRAPPER}} .worksheet-accordion-header',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'accordion_border_normal',
                'selector' => '{{WRAPPER}} .worksheet-accordion-item',
            ]
        );

        $this->end_controls_tab();

        // Hover Tab
        $this->start_controls_tab(
            'accordion_hover',
            [
                'label' => __( 'Hover', 'oup' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'accordion_background_hover',
                'selector' => '{{WRAPPER}} .worksheet-accordion-header:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'accordion_border_hover',
                'selector' => '{{WRAPPER}} .worksheet-accordion-item:hover',
            ]
        );

        $this->end_controls_tab();

        // Active Tab
        $this->start_controls_tab(
            'accordion_active',
            [
                'label' => __( 'Active', 'oup' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'accordion_background_active',
                'selector' => '{{WRAPPER}} .worksheet-accordion-header.active',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'accordion_border_active',
                'selector' => '{{WRAPPER}} .worksheet-accordion-item.active',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
            'accordion_border_radius',
            [
                'label' => __( 'Border Radius', 'oup' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'accordion_padding',
            [
                'label' => __( 'Padding', 'oup' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // 2. Header Style
        $this->start_controls_section(
            'section_style_header',
            [
                'label' => __( 'Header', 'oup' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'header_title_heading',
            [
                'label' => __( 'Title', 'oup' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .worksheet-accordion-title',
            ]
        );

        $this->start_controls_tabs( 'title_tabs' );

        // Normal Tab
        $this->start_controls_tab(
            'title_normal',
            [
                'label' => __( 'Normal', 'oup' ),
            ]
        );

        $this->add_control(
            'title_color_normal',
            [
                'label' => __( 'Color', 'oup' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow_normal',
                'selector' => '{{WRAPPER}} .worksheet-accordion-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'title_text_stroke_normal',
                'selector' => '{{WRAPPER}} .worksheet-accordion-title',
            ]
        );

        $this->end_controls_tab();

        // Hover Tab
        $this->start_controls_tab(
            'title_hover',
            [
                'label' => __( 'Hover', 'oup' ),
            ]
        );

        $this->add_control(
            'title_color_hover',
            [
                'label' => __( 'Color', 'oup' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-header:hover .worksheet-accordion-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow_hover',
                'selector' => '{{WRAPPER}} .worksheet-accordion-header:hover .worksheet-accordion-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'title_text_stroke_hover',
                'selector' => '{{WRAPPER}} .worksheet-accordion-header:hover .worksheet-accordion-title',
            ]
        );

        $this->end_controls_tab();

        // Active Tab
        $this->start_controls_tab(
            'title_active',
            [
                'label' => __( 'Active', 'oup' ),
            ]
        );

        $this->add_control(
            'title_color_active',
            [
                'label' => __( 'Color', 'oup' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-header.active .worksheet-accordion-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow_active',
                'selector' => '{{WRAPPER}} .worksheet-accordion-header.active .worksheet-accordion-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'title_text_stroke_active',
                'selector' => '{{WRAPPER}} .worksheet-accordion-header.active .worksheet-accordion-title',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        // --- ICON SUB-SECTION ---
        $this->add_control(
            'header_icon_heading',
            [
                'label' => __( 'Icon', 'oup' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __( 'Size', 'oup' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .worksheet-accordion-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_spacing',
            [
                'label' => __( 'Spacing', 'oup' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-header' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'icon_tabs' );

        // Normal Tab
        $this->start_controls_tab(
            'icon_normal',
            [
                'label' => __( 'Normal', 'oup' ),
            ]
        );

        $this->add_control(
            'icon_color_normal',
            [
                'label' => __( 'Color', 'oup' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .worksheet-accordion-icon i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Hover Tab
        $this->start_controls_tab(
            'icon_hover',
            [
                'label' => __( 'Hover', 'oup' ),
            ]
        );

        $this->add_control(
            'icon_color_hover',
            [
                'label' => __( 'Color', 'oup' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-header:hover .worksheet-accordion-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .worksheet-accordion-header:hover .worksheet-accordion-icon i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Active Tab
        $this->start_controls_tab(
            'icon_active',
            [
                'label' => __( 'Active', 'oup' ),
            ]
        );

        $this->add_control(
            'icon_color_active',
            [
                'label' => __( 'Color', 'oup' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-header.active .worksheet-accordion-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .worksheet-accordion-header.active .worksheet-accordion-icon i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        // 3. Content Style
        $this->start_controls_section(
            'section_style_content',
            [
                'label' => __( 'Content', 'oup' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_background',
                'selector' => '{{WRAPPER}} .worksheet-accordion-content',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'content_border',
                'selector' => '{{WRAPPER}} .worksheet-accordion-content',
            ]
        );

        $this->add_responsive_control(
            'content_border_radius',
            [
                'label' => __( 'Border Radius', 'oup' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __( 'Padding', 'oup' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .worksheet-accordion-content-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $accordion_items = get_field('worksheet_accordion');
        
        if ( ! $accordion_items || ! is_array( $accordion_items ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="oup-alert" style="padding: 20px; background: #fff3cd; color: #856404; border-radius: 8px; border: 1px dashed #ffeeba;">[Worksheet Accordion] No ACF data found. Please enter data in "worksheet_accordion" field.</div>';
            }
            return;
        }

        $default_state = !empty($settings['default_state']) ? $settings['default_state'] : 'first_expanded';
        $max_items = !empty($settings['max_items_expanded']) ? $settings['max_items_expanded'] : 'one';
        $anim_duration = (isset($settings['animation_duration']['size']) && is_numeric($settings['animation_duration']['size'])) ? $settings['animation_duration']['size'] : 400;
        
        ?>
        <div class="worksheet-accordion-wrapper" data-max-items="<?php echo esc_attr($max_items); ?>" data-anim-duration="<?php echo esc_attr($anim_duration); ?>">
            <?php foreach ( $accordion_items as $index => $item ) : 
                $title = isset($item['title']) ? $item['title'] : '';
                $content = isset($item['content']) ? $item['content'] : '';
                
                if ( empty( $title ) || empty( $content ) ) continue;

                $title_tag = !empty($settings['title_html_tag']) ? $settings['title_html_tag'] : 'h3';
                $is_active = false;
                if ($default_state === 'all_expanded') {
                    $is_active = true;
                } elseif ($default_state === 'first_expanded' && $index === 0) {
                    $is_active = true;
                }
                
                $active_class = $is_active ? 'active' : '';
                $aria_expanded = $is_active ? 'true' : 'false';
                $display = $is_active ? 'block' : 'none';
            ?>
                <div class="worksheet-accordion-item <?php echo esc_attr($active_class); ?>">
                    <button class="worksheet-accordion-header <?php echo esc_attr($active_class); ?>" aria-expanded="<?php echo esc_attr($aria_expanded); ?>">
                        <<?php echo esc_html($title_tag); ?> class="worksheet-accordion-title"><?php echo esc_html( $title ); ?></<?php echo esc_html($title_tag); ?>>
                        <span class="worksheet-accordion-icon">
                            <?php if ( ! empty( $settings['icon_expand']['value'] ) || ! empty( $settings['icon_collapse']['value'] ) ) : ?>
                                <?php if ( ! empty( $settings['icon_expand']['value'] ) ) : ?>
                                    <span class="elementor-accordion-icon-closed">
                                        <?php \Elementor\Icons_Manager::render_icon( $settings['icon_expand'], [ 'aria-hidden' => 'true' ] ); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ( ! empty( $settings['icon_collapse']['value'] ) ) : ?>
                                    <span class="elementor-accordion-icon-opened">
                                        <?php \Elementor\Icons_Manager::render_icon( $settings['icon_collapse'], [ 'aria-hidden' => 'true' ] ); ?>
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="elementor-accordion-icon-closed">
                                    <svg width="19" height="10" viewBox="0 0 19 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 1.5L8.8415 7.92381C9.21852 8.25371 9.78148 8.25371 10.1585 7.92381L17.5 1.5" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                                </span>
                            <?php endif; ?>
                        </span>
                    </button>
                    <div class="worksheet-accordion-content" style="display: <?php echo esc_attr($display); ?>;">
                        <div class="worksheet-accordion-content-inner">
                            <?php echo wp_kses_post( $content ); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
?>
