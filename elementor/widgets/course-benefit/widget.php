<?php
namespace OupElementorWidgets\Widgets\CourseBenefit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Widget_CourseBenefit extends Widget_Base {

    public function get_name() {
        return 'course-benefit';
    }

    public function get_title() {
        return __( 'Course Benefit', 'oup' );
    }

    public function get_icon() {
        return 'eicon-featured-image';
    }

    public function get_categories() {
        return [ 'oup' ];
    }

    public function get_script_depends() {
        return [ 'oup-course-benefit-script' ];
    }

    public function get_style_depends() {
        return [ 'oup-course-benefit-style' ];
    }


    public static function get_all_courses() {
        $courses = get_posts([
            'post_type'      => 'sfwd-courses',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC'
        ]);

        $options = [
            'current' => __( 'Current Course', 'oup' )
        ];

        if ( is_array( $courses ) && ! empty( $courses ) ) {
            foreach ( $courses as $course ) {
                if ( isset( $course->ID ) && isset( $course->post_title ) ) {
                    $options[$course->ID] = $course->post_title;
                }
            }
        }

        return $options;
    }


    public static function get_acf_field_choices( $field_name ) {
        if ( function_exists( 'acf_get_field' ) ) {
            $field = acf_get_field( $field_name );
            if ( is_array( $field ) && ! empty( $field['choices'] ) ) {
                return $field['choices'];
            }
        
            $group_field = acf_get_field( 'course_info' );
            if ( is_array( $group_field ) && ! empty( $group_field['sub_fields'] ) ) {
                foreach ( $group_field['sub_fields'] as $sub_field ) {
                    if ( is_array( $sub_field ) && isset( $sub_field['name'] ) && $sub_field['name'] === $field_name && ! empty( $sub_field['choices'] ) ) {
                        return $sub_field['choices'];
                    }
                }
            }
        }
        return [];
    }


    public static function get_label_for_choice( $value, $field_name ) {
        if ( empty( $value ) ) {
            return '';
        }
        $choices = self::get_acf_field_choices( $field_name );
        
        if ( is_array( $value ) ) {
            $labels = [];
            foreach ( $value as $v ) {
                $labels[] = isset( $choices[$v] ) ? $choices[$v] : $v;
            }
            return implode( ', ', $labels );
        }
        
        return isset( $choices[$value] ) ? $choices[$value] : $value;
    }

    protected function register_controls() {
        // CONTENT TAB
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content Settings', 'oup' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'course_select',
            [
                'label'   => __( 'Select Course', 'oup' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'current',
                'options' => self::get_all_courses(),
            ]
        );

        $this->end_controls_section();

        // STYLE TAB
        // 1. General Style
        $this->start_controls_section(
            'section_style_general',
            [
                'label' => __( 'General Layout', 'oup' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'wrapper_padding',
            [
                'label'      => __( 'Padding', 'oup' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .course-benefit-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'wrapper_background',
                'selector' => '{{WRAPPER}} .course-benefit-wrapper',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'wrapper_border',
                'selector' => '{{WRAPPER}} .course-benefit-wrapper',
            ]
        );

        $this->add_responsive_control(
            'wrapper_border_radius',
            [
                'label'      => __( 'Border Radius', 'oup' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .course-benefit-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // 2. Features Grid Layout Style
        $this->start_controls_section(
            'section_style_grid',
            [
                'label' => __( 'Grid Layout', 'oup' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'feature_columns',
            [
                'label'   => __( 'Columns', 'oup' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'selectors' => [
                    '{{WRAPPER}} .course-features-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_responsive_control(
            'grid_row_gap',
            [
                'label'      => __( 'Row Gap (Vertical)', 'oup' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 150, 'step' => 5 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .course-features-grid' => 'row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'grid_col_gap',
            [
                'label'      => __( 'Column Gap (Horizontal)', 'oup' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 150, 'step' => 5 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .course-features-grid' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // 3. Feature Item Card Style
        $this->start_controls_section(
            'section_style_features',
            [
                'label' => __( 'Feature Item Card', 'oup' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'feature_item_align',
            [
                'label'   => __( 'Alignment', 'oup' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __( 'Left', 'oup' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'oup' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => __( 'Right', 'oup' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .course-feature-item' => 'align-items: {{VALUE}}; text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'feature_item_padding',
            [
                'label'      => __( 'Padding', 'oup' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .course-feature-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'feature_item_border_radius',
            [
                'label'      => __( 'Border Radius', 'oup' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .course-feature-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Tabs Normal vs Hover for items
        $this->start_controls_tabs( 'tabs_feature_item' );

        // Normal State Tab
        $this->start_controls_tab(
            'tab_feature_item_normal',
            [
                'label' => __( 'Normal', 'oup' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'feature_item_background',
                'selector' => '{{WRAPPER}} .course-feature-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'feature_item_border',
                'selector' => '{{WRAPPER}} .course-feature-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'feature_item_box_shadow',
                'selector' => '{{WRAPPER}} .course-feature-item',
            ]
        );

        $this->end_controls_tab();

        // Hover State Tab
        $this->start_controls_tab(
            'tab_feature_item_hover',
            [
                'label' => __( 'Hover', 'oup' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'feature_item_background_hover',
                'selector' => '{{WRAPPER}} .course-feature-item:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'feature_item_border_hover',
                'selector' => '{{WRAPPER}} .course-feature-item:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'feature_item_box_shadow_hover',
                'selector' => '{{WRAPPER}} .course-feature-item:hover',
            ]
        );

        $this->add_control(
            'hover_transition_duration',
            [
                'label'   => __( 'Transition Duration (ms)', 'oup' ),
                'type'    => Controls_Manager::SLIDER,
                'range'   => [
                    'px' => [ 'min' => 0, 'max' => 2000, 'step' => 100 ],
                ],
                'default' => [
                    'size' => 300,
                ],
                'selectors' => [
                    '{{WRAPPER}} .course-feature-item' => 'transition: all {{SIZE}}ms ease;',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        // 4. Feature Icon Style
        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => __( 'Feature Icon', 'oup' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'feature_icon_size',
            [
                'label'      => __( 'Icon Size (Width/Height)', 'oup' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px' => [ 'min' => 10, 'max' => 150 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .course-feature-icon-wrapper img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
                    '{{WRAPPER}} .course-feature-icon-wrapper'     => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'feature_icon_spacing',
            [
                'label'      => __( 'Spacing Below Icon', 'oup' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .course-feature-icon-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_wrapper_padding',
            [
                'label'      => __( 'Icon Wrapper Padding', 'oup' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .course-feature-icon-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'icon_wrapper_bg_color',
            [
                'label'     => __( 'Background Color', 'oup' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-feature-icon-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'icon_wrapper_border',
                'selector' => '{{WRAPPER}} .course-feature-icon-wrapper',
            ]
        );

        $this->add_responsive_control(
            'icon_wrapper_border_radius',
            [
                'label'      => __( 'Border Radius', 'oup' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .course-feature-icon-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // 5. Feature Subheading (Text) Style
        $this->start_controls_section(
            'section_style_subheading',
            [
                'label' => __( 'Subheading Title', 'oup' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'subheading_typography',
                'selector' => '{{WRAPPER}} .course-feature-subheading',
            ]
        );

        $this->add_responsive_control(
            'subheading_max_width',
            [
                'label'      => __( 'Max Width', 'oup' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range'      => [
                    'px' => [ 'min' => 100, 'max' => 800 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .course-feature-subheading' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'subheading_margin',
            [
                'label'      => __( 'Margin', 'oup' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .course-feature-subheading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Normal vs Hover color tabs
        $this->start_controls_tabs( 'tabs_subheading_text' );

        $this->start_controls_tab(
            'tab_subheading_normal',
            [
                'label' => __( 'Normal', 'oup' ),
            ]
        );

        $this->add_control(
            'subheading_color',
            [
                'label'     => __( 'Color', 'oup' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-feature-subheading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_subheading_hover',
            [
                'label' => __( 'Hover', 'oup' ),
            ]
        );

        $this->add_control(
            'subheading_color_hover',
            [
                'label'     => __( 'Color (Hover)', 'oup' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-feature-item:hover .course-feature-subheading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render() {
        // Validation: Ensure ACF is active
        if ( ! function_exists( 'get_field' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="oup-alert" style="padding: 20px; background: #fff3cd; color: #856404; border-radius: 8px; border: 1px dashed #ffeeba;">' . esc_html__( '[Course Benefit] Advanced Custom Fields (ACF) is required.', 'oup' ) . '</div>';
            }
            return;
        }

        $settings  = $this->get_settings_for_display();
        $course_id = ( isset( $settings['course_select'] ) && $settings['course_select'] === 'current' ) ? get_the_ID() : intval( $settings['course_select'] );

        // Validation: Verify course post type is valid
        if ( empty( $course_id ) || get_post_type( $course_id ) !== 'sfwd-courses' ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="oup-alert" style="padding: 20px; background: #fff3cd; color: #856404; border-radius: 8px; border: 1px dashed #ffeeba;">' . esc_html__( '[Course Benefit] Please select a valid course or edit from a course page.', 'oup' ) . '</div>';
            }
            return;
        }

        // Query: Retrieve fields (Try ACF Group 'course_info' first)
        $course_info = get_field( 'course_info', $course_id );
        $features = [];

        if ( is_array( $course_info ) ) {
            $features = isset( $course_info['feature'] ) ? $course_info['feature'] : [];
        } else {
            // Fallback to top-level fields
            $features = get_field( 'feature', $course_id );
        }

        // Validation: Ensure features is an array and not empty
        if ( ! is_array( $features ) || empty( $features ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="oup-alert" style="padding: 20px; background: #fff3cd; color: #856404; border-radius: 8px; border: 1px dashed #ffeeba;">' . sprintf( __( '[Course Benefit] No feature repeater data found for the course "%s". Please populate the ACF fields.', 'oup' ), esc_html( get_the_title( $course_id ) ) ) . '</div>';
            }
            return;
        }

        ?>
        <section class="course-benefit-wrapper">
            <ul class="course-features-grid">
                <?php foreach ( $features as $feat ) : 
                    if ( ! is_array( $feat ) ) {
                        continue;
                    }

                    $icon        = isset( $feat['icon'] ) ? $feat['icon'] : '';
                    $sub_heading = isset( $feat['sub_heading'] ) ? $feat['sub_heading'] : '';

                    if ( empty( $icon ) && empty( $sub_heading ) ) {
                        continue;
                    }

                    $icon_url  = '';
                    $icon_alt  = '';
                    $icon_slug = '';
                    $width     = '';
                    $height    = '';

                    if ( ! empty( $icon ) ) {
                        if ( is_array( $icon ) && isset( $icon['url'] ) ) {
                            $icon_url  = $icon['url'];
                            $icon_alt  = ! empty( $icon['alt'] ) ? $icon['alt'] : $sub_heading;
                            $width     = isset( $icon['width'] ) ? $icon['width'] : '';
                            $height    = isset( $icon['height'] ) ? $icon['height'] : '';
                            $icon_slug = isset( $icon['name'] ) ? $icon['name'] : ( isset( $icon['title'] ) ? sanitize_title( $icon['title'] ) : '' );
                        } elseif ( is_numeric( $icon ) ) {
                            $image_src = wp_get_attachment_image_src( $icon, 'thumbnail' );
                            if ( $image_src ) {
                                $icon_url = $image_src[0];
                                $width    = $image_src[1];
                                $height   = $image_src[2];
                            }
                            $icon_alt  = get_post_meta( $icon, '_wp_attachment_image_alt', true );
                            $icon_post = get_post( $icon );
                            if ( $icon_post ) {
                                $icon_slug = $icon_post->post_name;
                            }
                        } else {
                            $icon_url  = $icon;
                            $icon_alt  = $sub_heading;
                            $icon_slug = sanitize_title( pathinfo( $icon, PATHINFO_FILENAME ) );
                        }
                    }

                    // Validation: Sanitize subheading text and generate safe slug class
                    $sub_heading_clean = sanitize_text_field( $sub_heading );
                    $sub_heading_slug  = sanitize_title( $sub_heading_clean );
                    ?>
                    <li class="course-feature-item feature-<?php echo esc_attr( $sub_heading_slug ); ?>">
                        <?php if ( ! empty( $icon_url ) ) : ?>
                            <div class="course-feature-icon-wrapper icon-<?php echo esc_attr( $sub_heading_slug ); ?> icon-slug-<?php echo esc_attr( $icon_slug ); ?>">
                                <img src="<?php echo esc_url( $icon_url ); ?>" 
                                     alt="<?php echo esc_attr( $icon_alt ); ?>" 
                                     <?php if ( ! empty( $width ) ) echo 'width="' . esc_attr( $width ) . '"'; ?>
                                     <?php if ( ! empty( $height ) ) echo 'height="' . esc_attr( $height ) . '"'; ?>
                                     loading="lazy" />
                            </div>
                        <?php endif; ?>
                        <?php if ( ! empty( $sub_heading_clean ) ) : ?>
                            <p class="course-feature-subheading text-<?php echo esc_attr( $sub_heading_slug ); ?>">
                                <?php echo esc_html( $sub_heading_clean ); ?>
                            </p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php
    }
}
