<?php 
namespace OupElementorWidgets\Widgets\CourseAccordion;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

class Widget_CourseAccordion extends Widget_Base {
    public function get_name() {
        return 'course-accordion';
    }
    public function get_title() {
        return __( 'Course Accordion', 'oup' );
    }

    public function get_icon() {
        return 'eicon-accordion';
    }

    public function get_categories() {
        return [ 'oup' ];
    }

    public function get_script_depends() {
        return [ 'oup-course-accordion-script' ];
    }

    public function get_style_depends() {
        return [ 'oup-course-accordion-style' ];
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

    protected function register_controls() {
        $this->start_controls_section(
            'section_layout',
            [
                'label' => __( 'Layout', 'oup' ),
                'tab' => Controls_Manager::TAB_CONTENT,
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
                    '{{WRAPPER}} .course-accordion-header' => 'justify-content: {{VALUE}};',
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
                    '{{WRAPPER}} .course-accordion-header' => 'flex-direction: {{VALUE}};',
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
                    '{{WRAPPER}} .course-accordion-content' => 'margin-top: {{SIZE}}{{UNIT}};',
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
                'selector' => '{{WRAPPER}} .course-accordion-header',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'accordion_border_normal',
                'selector' => '{{WRAPPER}} .course-accordion-header',
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
                'selector' => '{{WRAPPER}} .course-accordion-header:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'accordion_border_hover',
                'selector' => '{{WRAPPER}} .course-accordion-header:hover',
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
                'selector' => '{{WRAPPER}} .course-accordion-header.active',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'accordion_border_active',
                'selector' => '{{WRAPPER}} .course-accordion-header.active',
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
                    '{{WRAPPER}} .course-accordion-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .course-accordion-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'selector' => '{{WRAPPER}} .course-accordion-title',
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
                    '{{WRAPPER}} .course-accordion-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow_normal',
                'selector' => '{{WRAPPER}} .course-accordion-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'title_text_stroke_normal',
                'selector' => '{{WRAPPER}} .course-accordion-title',
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
                    '{{WRAPPER}} .course-accordion-header:hover .course-accordion-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow_hover',
                'selector' => '{{WRAPPER}} .course-accordion-header:hover .course-accordion-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'title_text_stroke_hover',
                'selector' => '{{WRAPPER}} .course-accordion-header:hover .course-accordion-title',
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
                    '{{WRAPPER}} .course-accordion-header.active .course-accordion-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow_active',
                'selector' => '{{WRAPPER}} .course-accordion-header.active .course-accordion-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'title_text_stroke_active',
                'selector' => '{{WRAPPER}} .course-accordion-header.active .course-accordion-title',
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
                    '{{WRAPPER}} .course-accordion-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .course-accordion-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .course-accordion-header' => 'gap: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .course-accordion-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .course-accordion-icon i' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .course-accordion-header:hover .course-accordion-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .course-accordion-header:hover .course-accordion-icon i' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .course-accordion-header.active .course-accordion-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .course-accordion-header.active .course-accordion-icon i' => 'color: {{VALUE}};',
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
                'selector' => '{{WRAPPER}} .course-accordion-content',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'content_border',
                'selector' => '{{WRAPPER}} .course-accordion-content',
            ]
        );

        $this->add_responsive_control(
            'content_border_radius',
            [
                'label' => __( 'Border Radius', 'oup' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .course-accordion-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .course-accordion-content-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        // Validation: Ensure LearnDash is active
        if ( ! function_exists( 'learndash_course_get_lessons' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="oup-alert" style="padding: 20px; background: #fff3cd; color: #856404; border-radius: 8px; border: 1px dashed #ffeeba;">[Course Accordion] LearnDash plugin is required.</div>';
            }
            return;
        }

        $settings = $this->get_settings_for_display();
        $course_id = ( isset( $settings['course_select'] ) && $settings['course_select'] === 'current' ) ? get_the_ID() : intval( $settings['course_select'] );

        // Validation: Verify course ID
        if ( empty( $course_id ) || get_post_type( $course_id ) !== 'sfwd-courses' ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="oup-alert" style="padding: 20px; background: #fff3cd; color: #856404; border-radius: 8px; border: 1px dashed #ffeeba;">[Course Accordion] Please select a valid course or edit from a course page.</div>';
            }
            return;
        }

      
        $transient_key = 'oup_course_accordion_' . $course_id;
        $lessons_list  = get_transient( $transient_key );

        if ( false === $lessons_list ) {
         
            $lessons_list = \learndash_get_course_lessons_list( $course_id, null, [ 'nopaging' => true ] );
            set_transient( $transient_key, $lessons_list, HOUR_IN_SECONDS );
        }

        if ( empty( $lessons_list ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="oup-alert" style="padding: 20px; background: #fff3cd; color: #856404; border-radius: 8px; border: 1px dashed #ffeeba;">[Course Accordion] No lessons found for the course "' . esc_html( get_the_title( $course_id ) ) . '".</div>';
            }
            return;
        }

        $default_state = !empty($settings['default_state']) ? $settings['default_state'] : 'first_expanded';
        $max_items = !empty($settings['max_items_expanded']) ? $settings['max_items_expanded'] : 'one';
        $anim_duration = (isset($settings['animation_duration']['size']) && is_numeric($settings['animation_duration']['size'])) ? $settings['animation_duration']['size'] : 400;
        
        ?>
        <div class="course-accordion-wrapper" data-max-items="<?php echo esc_attr($max_items); ?>" data-anim-duration="<?php echo esc_attr($anim_duration); ?>">
            <?php foreach ( $lessons_list as $index => $lesson ) :
                $lesson_post = $lesson['post'];
                $title       = $lesson_post->post_title;
                $topics      = isset( $lesson['topics'] ) ? $lesson['topics'] : ( isset( $lesson['topic'] ) ? $lesson['topic'] : [] );
                $quizzes     = isset( $lesson['quizzes'] ) ? $lesson['quizzes'] : ( isset( $lesson['quiz'] ) ? $lesson['quiz'] : [] );

                if ( empty( $title ) ) continue;

                $title_tag = !empty($settings['title_html_tag']) ? $settings['title_html_tag'] : 'h2';
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
                <div class="course-accordion-item">
                    <button class="course-accordion-header <?php echo esc_attr($active_class); ?>" aria-expanded="<?php echo esc_attr($aria_expanded); ?>">
                        <<?php echo esc_html($title_tag); ?> class="course-accordion-title"><?php echo esc_html( $title ); ?></<?php echo esc_html($title_tag); ?>>
                        <span class="course-accordion-icon">
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
                    <div class="course-accordion-content" style="display: <?php echo esc_attr($display); ?>;">
                        <div class="course-accordion-content-inner">
                            <?php if ( empty( $topics ) && empty( $quizzes ) ) : ?>
                                <p class="no-steps-msg"><?php esc_html_e( 'No steps found for this lesson.', 'oup' ); ?></p>
                            <?php else : ?>
                                <ul class="course-accordion-steps">
                                    <?php if ( ! empty( $topics ) ) : ?>
                                        <?php foreach ( $topics as $topic ) :
                                     
                                            $topic_post = is_a( $topic, 'WP_Post' ) ? $topic : ( isset( $topic['post'] ) ? $topic['post'] : null );
                                            if ( ! $topic_post ) continue;
                                        ?>
                                            <li class="course-accordion-step-item course-accordion-topic">
                                                <a href="<?php echo esc_url( get_permalink( $topic_post->ID ) ); ?>">
                                                    <span class="step-icon">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                                    </span>
                                                    <span class="step-title"><?php echo esc_html( $topic_post->post_title ); ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if ( ! empty( $quizzes ) ) : ?>
                                        <?php foreach ( $quizzes as $quiz ) :
                                            $quiz_post = is_a( $quiz, 'WP_Post' ) ? $quiz : ( isset( $quiz['post'] ) ? $quiz['post'] : null );
                                            if ( ! $quiz_post ) continue;
                                        ?>
                                            <li class="course-accordion-step-item course-accordion-quiz">
                                                <a href="<?php echo esc_url( get_permalink( $quiz_post->ID ) ); ?>">
                                                    <span class="step-icon">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
                                                    </span>
                                                    <span class="step-title"><?php echo esc_html( $quiz_post->post_title ); ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
?>
