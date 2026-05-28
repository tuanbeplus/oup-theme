<?php
namespace OupElementorWidgets\Widgets\LogoSlider;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;


class Widget_LogoSlider extends Widget_Base {

    public function get_name() {
        return 'logo-slider';
    }

    public function get_title() {
        return __( 'Logo Slider', 'oup' );
    }

    public function get_icon() {
        return 'eicon-slider-push';
    }

    public function get_categories() {
        return [ 'oup' ];
    }

    public function get_script_depends() {
        return [ 'swiper' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Logos', 'oup' ),
            ]
        );

        $this->add_control(
            'logo_gallery',
            [
                'label' => __( 'Add Logos', 'oup' ),
                'type' => \Elementor\Controls_Manager::GALLERY,
                'default' => [],
            ]
        );

        $this->add_control(
            'logo_link_to',
            [
                'label' => __( 'Link', 'oup' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __( 'None', 'oup' ),
                    'custom' => __( 'Custom URL', 'oup' ),
                ],
            ]
        );

        $this->add_control(
            'logo_link',
            [
                'label' => __( 'Custom URL', 'oup' ),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'oup' ),
                'condition' => [
                    'logo_link_to' => 'custom',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Image_Size::get_type(),
            [
                'name' => 'logo_image', 
                'default' => 'full',
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_slider_options',
            [
                'label' => __( 'Slider Settings', 'oup' ),
            ]
        );

        $this->add_responsive_control(
            'slides_per_view',
            [
                'label' => __( 'Slides Per View', 'oup' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'default' => 6,
                'tablet_default' => 4,
                'mobile_default' => 2,
            ]
        );

        $this->add_responsive_control(
            'slides_to_scroll',
            [
                'label' => __( 'Slides to Scroll', 'oup' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'default' => 1,
            ]
        );



        $this->add_control(
            'navigation',
            [
                'label' => __( 'Navigation', 'oup' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'both' => __( 'Arrows and Dots', 'oup' ),
                    'arrows' => __( 'Arrows', 'oup' ),
                    'dots' => __( 'Dots', 'oup' ),
                    'none' => __( 'None', 'oup' ),
                ],
            ]
        );

        $this->add_control(
            'lazyload',
            [
                'label' => __( 'Lazyload', 'oup' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __( 'Autoplay', 'oup' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => __( 'Pause on Hover', 'oup' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pause_on_interaction',
            [
                'label' => __( 'Pause on Interaction', 'oup' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => __( 'Autoplay Speed (ms)', 'oup' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'infinite_loop',
            [
                'label' => __( 'Infinite Loop', 'oup' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'speed',
            [
                'label' => __( 'Animation Speed (ms)', 'oup' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 500,
            ]
        );

        $this->add_control(
            'direction',
            [
                'label' => __( 'Direction', 'oup' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'ltr',
                'options' => [
                    'ltr' => __( 'Left', 'oup' ),
                    'rtl' => __( 'Right', 'oup' ),
                ],
            ]
        );

        $this->end_controls_section();

        // STYLE TAB
        $this->start_controls_section(
            'section_style_logo',
            [
                'label' => __( 'Logo Style', 'oup' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'space_between',
            [
                'label' => __( 'Space Between (Gap)', 'oup' ),
                'type' => Controls_Manager::NUMBER,
            ]
        );

        $this->add_responsive_control(
            'slider_padding',
            [
                'label' => __( 'Container Padding', 'oup' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .oup-logo-slider-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'logo_height',
            [
                'label' => __( 'Height', 'oup' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .oup-logo-slider-container .oup-logo-swiper .swiper-slide .oup-logo-img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );



        $this->start_controls_tabs( 'tabs_logo_style' );

        $this->start_controls_tab(
            'tab_logo_normal',
            [
                'label' => __( 'Normal', 'oup' ),
            ]
        );

        $this->add_control(
            'logo_opacity_normal',
            [
                'label' => __( 'Opacity', 'oup' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'max' => 1, 'min' => 0.1, 'step' => 0.05 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .oup-logo-img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'logo_css_filters_normal',
                'selector' => '{{WRAPPER}} .oup-logo-img',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_logo_hover',
            [
                'label' => __( 'Hover', 'oup' ),
            ]
        );

        $this->add_control(
            'logo_opacity_hover',
            [
                'label' => __( 'Opacity', 'oup' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'max' => 1, 'min' => 0.1, 'step' => 0.05 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .oup-logo-img:hover' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'logo_css_filters_hover',
                'selector' => '{{WRAPPER}} .oup-logo-img:hover',
            ]
        );

        $this->add_control(
            'logo_transition_duration',
            [
                'label' => __( 'Transition Duration (s)', 'oup' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0.3,
                ],
                'range' => [
                    'px' => [ 'max' => 3, 'min' => 0.1, 'step' => 0.1 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .oup-logo-img' => 'transition: all {{SIZE}}s ease;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // NAVIGATION STYLE TAB
        $this->start_controls_section(
            'section_style_navigation',
            [
                'label' => __( 'Navigation', 'oup' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'navigation!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'heading_arrows',
            [
                'label' => __( 'Arrows', 'oup' ),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'navigation' => [ 'both', 'arrows' ],
                ],
            ]
        );

        $this->add_control(
            'arrows_size',
            [
                'label' => __( 'Size', 'oup' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 10, 'max' => 60 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .oup-logo-slider-container .elementor-swiper-button i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .oup-logo-slider-container .elementor-swiper-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'navigation' => [ 'both', 'arrows' ],
                ],
            ]
        );

        $this->add_control(
            'arrows_color',
            [
                'label' => __( 'Color', 'oup' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .oup-logo-slider-container .elementor-swiper-button' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .oup-logo-slider-container .elementor-swiper-button i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .oup-logo-slider-container .elementor-swiper-button svg' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'navigation' => [ 'both', 'arrows' ],
                ],
            ]
        );

        $this->add_control(
            'arrows_bg_color',
            [
                'label' => __( 'Background Color', 'oup' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .oup-logo-slider-container .elementor-swiper-button' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'navigation' => [ 'both', 'arrows' ],
                ],
            ]
        );

        $this->add_control(
            'heading_dots',
            [
                'label' => __( 'Dots', 'oup' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'navigation' => [ 'both', 'dots' ],
                ],
            ]
        );

        $this->add_control(
            'dots_size',
            [
                'label' => __( 'Size', 'oup' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 5, 'max' => 20 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .oup-logo-slider-container .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'navigation' => [ 'both', 'dots' ],
                ],
            ]
        );

        $this->add_control(
            'dots_color',
            [
                'label' => __( 'Color', 'oup' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .oup-logo-slider-container .swiper-pagination-bullet' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    'navigation' => [ 'both', 'dots' ],
                ],
            ]
        );

        $this->add_control(
            'dots_active_color',
            [
                'label' => __( 'Active Color', 'oup' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .oup-logo-slider-container .swiper-pagination-bullet-active' => 'background: {{VALUE}}; opacity: 1;',
                ],
                'condition' => [
                    'navigation' => [ 'both', 'dots' ],
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( empty( $settings['logo_gallery'] ) ) {
            return;
        }

        $swiper_settings = [
            'slidesPerView' => !empty($settings['slides_per_view_mobile']) ? $settings['slides_per_view_mobile'] : 2,
            'slidesPerGroup' => !empty($settings['slides_to_scroll_mobile']) ? $settings['slides_to_scroll_mobile'] : 1,
            'spaceBetween'  => $settings['space_between_mobile'] !== '' ? $settings['space_between_mobile'] : 20,
            'loop'          => $settings['infinite_loop'] === 'yes',
            'speed'         => $settings['speed'] ?: 500,
            'breakpoints'   => [
                768 => [
                    'slidesPerView' => !empty($settings['slides_per_view_tablet']) ? $settings['slides_per_view_tablet'] : 4,
                    'slidesPerGroup' => !empty($settings['slides_to_scroll_tablet']) ? $settings['slides_to_scroll_tablet'] : 1,
                    'spaceBetween'  => $settings['space_between_tablet'] !== '' ? $settings['space_between_tablet'] : 30,
                ],
                1024 => [
                    'slidesPerView' => !empty($settings['slides_per_view']) ? $settings['slides_per_view'] : 6,
                    'slidesPerGroup' => !empty($settings['slides_to_scroll']) ? $settings['slides_to_scroll'] : 1,
                    'spaceBetween'  => $settings['space_between'] !== '' ? $settings['space_between'] : 45,
                ]
            ]
        ];

        $show_arrows = in_array( $settings['navigation'], [ 'both', 'arrows' ] );
        $show_dots = in_array( $settings['navigation'], [ 'both', 'dots' ] );

        if ( $show_arrows ) {
            $swiper_settings['navigation'] = [
                'nextEl' => '.elementor-swiper-button-next',
                'prevEl' => '.elementor-swiper-button-prev',
            ];
        }
        
        if ( $show_dots ) {
            $swiper_settings['pagination'] = [
                'el' => '.swiper-pagination',
                'type' => 'bullets',
                'clickable' => true,
            ];
        }

        if ( $settings['autoplay'] === 'yes' ) {
            $swiper_settings['autoplay'] = [
                'delay' => !empty($settings['autoplay_speed']) ? $settings['autoplay_speed'] : 5000,
                'disableOnInteraction' => $settings['pause_on_interaction'] === 'yes',
            ];
            if ( $settings['pause_on_hover'] === 'yes' ) {
                $swiper_settings['autoplay']['pauseOnMouseEnter'] = true;
            }
            if ( $settings['direction'] === 'rtl' ) {
                $swiper_settings['autoplay']['reverseDirection'] = true;
            }
        }
        
        $dir_attr = '';

        ?>
        <div class="oup-logo-slider-container" <?php echo $dir_attr; ?>>
            <div class="swiper oup-logo-swiper" data-swiper-settings='<?php echo esc_attr( wp_json_encode( $swiper_settings ) ); ?>'>
                <div class="swiper-wrapper">
                    <?php foreach ( $settings['logo_gallery'] as $index => $image ) : ?>
                        <?php if ( ! empty( $image['url'] ) ) : ?>
                            <div class="swiper-slide oup-logo-slide">
                                <?php
                                $fake_settings = [
                                    'image' => $image,
                                    'image_size' => $settings['logo_image_size'],
                                    'image_custom_dimension' => $settings['logo_image_custom_dimension'] ?? '',
                                ];
                                $img_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $fake_settings, 'image' );
                                $img_class = 'oup-logo-img';
                                
                                if ( $settings['lazyload'] === 'yes' ) {
                                    $img_html = str_replace( '<img ', '<img loading="lazy" class="' . $img_class . '" ', $img_html );
                                } else {
                                    $img_html = str_replace( '<img ', '<img class="' . $img_class . '" ', $img_html );
                                }
                                
                                if ( $settings['logo_link_to'] === 'custom' && ! empty( $settings['logo_link']['url'] ) ) {
                                    $link_key = 'logo_link_' . $index;
                                    $this->add_link_attributes( $link_key, $settings['logo_link'] );
                                    echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>' . $img_html . '</a>';
                                } else {
                                    echo $img_html;
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <?php if ( $show_arrows ) : ?>
                <div class="elementor-swiper-button elementor-swiper-button-prev">
                    <i class="eicon-chevron-left" aria-hidden="true"></i>
                    <span class="elementor-screen-only"><?php echo __( 'Previous', 'oup' ); ?></span>
                </div>
                <div class="elementor-swiper-button elementor-swiper-button-next">
                    <i class="eicon-chevron-right" aria-hidden="true"></i>
                    <span class="elementor-screen-only"><?php echo __( 'Next', 'oup' ); ?></span>
                </div>
            <?php endif; ?>

            <?php if ( $show_dots ) : ?>
                <div class="swiper-pagination"></div>
            <?php endif; ?>
        </div>
        <?php
    }
}

