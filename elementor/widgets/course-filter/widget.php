<?php
namespace OupElementorWidgets\Widgets\CourseFilter;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Widget_CourseFilter extends Widget_Base
{
    public function get_name()
    {
        return 'course-filter';
    }

    public function get_title()
    {
        return __('Course Filter', 'oup');
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
        return ['oup-course-filter-script'];
    }

    public function get_style_depends()
    {
        return ['oup-course-filter-style'];
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'oup'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'oup'),
                'type' => Controls_Manager::NUMBER,
                'min' => -1,
                'default' => '',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => __('Order By', 'oup'),
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => __('Date', 'oup'),
                    'title' => __('Title', 'oup'),
                    'rand' => __('Random', 'oup'),
                    'menu_order' => __('Menu Order', 'oup'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Order', 'oup'),
                'type' => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'DESC' => 'DESC',
                    'ASC' => 'ASC'
                ],
            ]
        );

        $this->end_controls_section();

        // STYLE TAB
        
        $this->start_controls_section(
            'style_section_layout',
            [
                'label' => __('Layout & Spacing', 'oup'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'top_bar_margin_bottom',
            [
                'label' => __('Space Below Top Bar', 'oup'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .course-top-bar' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'grid_gap',
            [
                'label' => __('Grid Gap (Space Between Cards)', 'oup'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .course-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Columns', 'oup'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'selectors' => [
                    '{{WRAPPER}} .course-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->end_controls_section();

        // 1. Card Style
        $this->start_controls_section(
            'style_section_card',
            [
                'label' => __('Card Settings', 'oup'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => __('Content Padding', 'oup'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .course-card-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_bg_color',
            [
                'label' => __('Background Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-card-item' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .course-card-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => __('Card Border Radius', 'oup'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .course-card-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .course-card-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
                    '{{WRAPPER}} .course-card-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // 2. Title Style
        $this->start_controls_section(
            'style_section_title',
            [
                'label' => __('Title', 'oup'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-card-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .course-card-title',
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __('Margin', 'oup'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .course-card-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // 3. Meta Style
        $this->start_controls_section(
            'style_section_meta',
            [
                'label' => __('Meta (Level, Subject...)', 'oup'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label' => __('Text Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-card-meta .meta-item' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'meta_icon_color',
            [
                'label' => __('Icon Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-card-meta .meta-item svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                    '{{WRAPPER}} .course-card-meta .meta-item svg' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typography',
                'selector' => '{{WRAPPER}} .course-card-meta .meta-item',
            ]
        );

        $this->end_controls_section();

        // 4. Filters Style
        $this->start_controls_section(
            'style_section_top_filters',
            [
                'label' => __('Top Filter Dropdowns', 'oup'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'filter_buttons_gap',
            [
                'label' => __('Space Between Filters (Gap)', 'oup'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .course-filters' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'top_filter_typography',
                'selector' => '{{WRAPPER}} .course-filters .custom-select-trigger, {{WRAPPER}} .course-filters label',
            ]
        );

        $this->start_controls_tabs('top_filter_tabs');

        $this->start_controls_tab(
            'top_filter_normal',
            [
                'label' => __('Normal', 'oup'),
            ]
        );

        $this->add_control(
            'top_filter_color',
            [
                'label' => __('Text Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-filters .custom-select-trigger' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'top_filter_bg',
            [
                'label' => __('Background Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-filters .custom-select-trigger' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'top_filter_border_color',
            [
                'label' => __('Border Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-filters .custom-select-trigger' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'top_filter_active',
            [
                'label' => __('Open & Hover', 'oup'),
            ]
        );

        $this->add_control(
            'top_filter_color_active',
            [
                'label' => __('Text Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-filters .custom-select-wrapper.open .custom-select-trigger' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'top_filter_bg_active',
            [
                'label' => __('Background Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-filters .custom-select-wrapper.open .custom-select-trigger' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'top_filter_border_color_active',
            [
                'label' => __('Border Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-filters .custom-select-wrapper.open .custom-select-trigger' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'top_filter_border_radius',
            [
                'label' => __('Border Radius', 'oup'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .course-filters .custom-select-trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // 5. Button Style
        $this->start_controls_section(
            'style_section_button',
            [
                'label' => __('Button Style', 'oup'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'oup'),
                'selector' => '{{WRAPPER}} .course-btn',
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __('Normal', 'oup'),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Text Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => __('Background Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __('Hover', 'oup'),
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label' => __('Text Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_bg_color',
            [
                'label' => __('Background Color', 'oup'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .course-btn:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'oup'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .course-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'oup'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .course-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    public static function get_acf_field_choices($field_name)
    {
        if (function_exists('acf_get_field')) {
            $field = acf_get_field($field_name);
            if ($field && !empty($field['choices'])) {
                return $field['choices'];
            }
        }

        $options = self::get_distinct_meta_values($field_name);
        $assoc = [];
        foreach ($options as $opt) {
            $assoc[$opt] = $opt;
        }
        return $assoc;
    }

    public static function get_distinct_meta_values($meta_key)
    {
        global $wpdb;
        $values = $wpdb->get_col($wpdb->prepare("
            SELECT DISTINCT meta_value 
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = %s 
            AND p.post_type = 'sfwd-courses' 
            AND p.post_status = 'publish'
            AND pm.meta_value != ''
        ", $meta_key));

        $options = [];
        foreach ($values as $val) {
            // Check if it's serialized array (ACF select can save as array)
            $unserialized = @unserialize($val);
            if ($unserialized !== false && is_array($unserialized)) {
                foreach ($unserialized as $u) {
                    if (!in_array($u, $options))
                        $options[] = $u;
                }
            } else {
                if (!in_array($val, $options))
                    $options[] = $val;
            }
        }
        return $options;
    }

    public static function render_card($post_id)
    {
        $title = get_the_title($post_id);
        $permalink = get_permalink($post_id);
        $thumbnail = get_the_post_thumbnail_url($post_id, 'medium_large');
        if (!$thumbnail) {
            $thumbnail = includes_url('images/media/default.png');
        }
        $thumbnail_id = get_post_thumbnail_id($post_id);
        $alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
        if (empty($alt)) {
            $alt = $title;
        }

        $description = get_post_meta($post_id, 'description', true);
        if (empty($description)) {
            $description = "Insert one sentence about this course";
        }

        $get_labels = function($meta_val, $field_name) {
            if (empty($meta_val)) return '';
            $choices = self::get_acf_field_choices($field_name);
            $labels = [];
            $vals = is_array($meta_val) ? $meta_val : [$meta_val];
            foreach ($vals as $v) {
                $labels[] = isset($choices[$v]) ? $choices[$v] : $v;
            }
            return implode(', ', $labels);
        };

        // Audience
        $audience_val = get_post_meta($post_id, 'course_audience', true);
        $audience = $get_labels($audience_val, 'course_audience');

        // Subject
        $subject_val = get_post_meta($post_id, 'course_subject', true);
        $subject = $get_labels($subject_val, 'course_subject');

        // Level
        $level_val = get_post_meta($post_id, 'course_learning_level', true);
        $level = $get_labels($level_val, 'course_learning_level');

        // Price from LearnDash
        $course_price = '';
        if (function_exists('learndash_get_course_price')) {
            $price_arr = learndash_get_course_price($post_id);
            if (isset($price_arr['price']) && !empty($price_arr['price'])) {
                $course_price = $price_arr['price'];
            }
        } else {
            $course_meta = get_post_meta($post_id, '_sfwd-courses', true);
            if (is_array($course_meta) && isset($course_meta['sfwd-courses_course_price'])) {
                $course_price = $course_meta['sfwd-courses_course_price'];
            }
        }
        $price_text = !empty($course_price) ? " for $" . esc_html($course_price) : "";

        $permalink = get_permalink($post_id);

        
        $button_link = $permalink;
        $course_meta = get_post_meta($post_id, '_sfwd-courses', true);
        if (is_array($course_meta) && !empty($course_meta['sfwd-courses_custom_button_url'])) {
            $button_link = $course_meta['sfwd-courses_custom_button_url'];
        }

        ?>
        <article class="course-card-item">
            <div class="course-card-image">
                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy">
            </div>

            <div class="course-card-content">
                <h3 class="course-card-title">
                    <a href="<?php echo esc_url($permalink); ?>" class="course-card-link">
                        <?php echo esc_html($title); ?>
                    </a>
                </h3>
                <p class="course-card-desc"><?php echo esc_html($description); ?></p>

                <div class="course-card-meta">
                    <div class="meta-item">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6 7.25C6.78527 7.25 7.55812 7.34205 8.31934 7.52637C9.07843 7.71021 9.82856 7.98667 10.5693 8.35645H10.5703C10.8499 8.50107 11.0738 8.70957 11.2461 8.99023C11.4155 9.26627 11.5 9.56633 11.5 9.90039V11.5H0.5V9.90039C0.5 9.56633 0.584525 9.26627 0.753906 8.99023C0.926246 8.70957 1.15008 8.50107 1.42969 8.35645C2.17072 7.98648 2.9213 7.71028 3.68066 7.52637C4.44188 7.34205 5.21473 7.25 6 7.25ZM6 0.5C6.69134 0.5 7.27115 0.739895 7.76562 1.23438C8.2601 1.72885 8.5 2.30866 8.5 3C8.5 3.69134 8.2601 4.27115 7.76562 4.76562C7.27115 5.2601 6.69134 5.5 6 5.5C5.30866 5.5 4.72885 5.2601 4.23438 4.76562C3.7399 4.27115 3.5 3.69134 3.5 3C3.5 2.30866 3.7399 1.72886 4.23438 1.23438C4.72886 0.739895 5.30866 0.5 6 0.5Z"
                                stroke="#231F20" stroke-linejoin="round" />
                        </svg>
                        <span><?php echo esc_html($audience ?: 'Audience'); ?></span>
                    </div>
                    <div class="meta-item">
                        <svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M7.5 3.38889C7.5 2.62271 7.205 1.88791 6.6799 1.34614C6.1548 0.804364 5.44261 0.5 4.7 0.5H0.5V11.3333H5.4C5.95695 11.3333 6.4911 11.5616 6.88492 11.9679C7.27875 12.3743 7.5 12.9254 7.5 13.5M7.5 3.38889V13.5M7.5 3.38889C7.5 2.62271 7.795 1.88791 8.3201 1.34614C8.8452 0.804364 9.55739 0.5 10.3 0.5H14.5V11.3333H9.6C9.04305 11.3333 8.5089 11.5616 8.11508 11.9679C7.72125 12.3743 7.5 12.9254 7.5 13.5"
                                stroke="#1E1E1E" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span><?php echo esc_html($subject ?: 'Subject'); ?></span>
                    </div>
                          <div class="meta-item">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.4 10.9L7 8.9175L9.6 10.9L8.625 7.6825L11.225 5.83H8.04L7 2.45L5.96 5.83H2.775L5.375 7.6825L4.4 10.9ZM7 13.5C6.10083 13.5 5.25583 13.3294 4.465 12.9881C3.67417 12.6469 2.98625 12.1838 2.40125 11.5988C1.81625 11.0138 1.35313 10.3258 1.01188 9.535C0.670625 8.74417 0.5 7.89917 0.5 7C0.5 6.10083 0.670625 5.25583 1.01188 4.465C1.35313 3.67417 1.81625 2.98625 2.40125 2.40125C2.98625 1.81625 3.67417 1.35313 4.465 1.01188C5.25583 0.670625 6.10083 0.5 7 0.5C7.89917 0.5 8.74417 0.670625 9.535 1.01188C10.3258 1.35313 11.0138 1.81625 11.5988 2.40125C12.1838 2.98625 12.6469 3.67417 12.9881 4.465C13.3294 5.25583 13.5 6.10083 13.5 7C13.5 7.89917 13.3294 8.74417 12.9881 9.535C12.6469 10.3258 12.1838 11.0138 11.5988 11.5988C11.0138 12.1838 10.3258 12.6469 9.535 12.9881C8.74417 13.3294 7.89917 13.5 7 13.5Z"
                                stroke="#231F20" stroke-linejoin="round" />
                        </svg>
                        <span><?php echo esc_html($level ?: 'Level'); ?></span>
                    </div>
                </div>

                <div class="course-card-action">
                    <?php 
                    $is_enrolled = false;
                    if ( is_user_logged_in() && function_exists('sfwd_lms_has_access') ) {
                        $is_enrolled = sfwd_lms_has_access( $post_id, get_current_user_id() );
                    }

                    $course_price_type = function_exists('learndash_get_setting') ? learndash_get_setting( $post_id, 'course_price_type' ) : '';

                    if ( $is_enrolled ) {
                        ?>
                        <a href="<?php echo esc_url($permalink); ?>" class="course-btn">Start Learning</a>
                        <?php
                    } elseif ( $course_price_type === 'closed' ) {
                        ?>
                        <a href="#" class="course-btn" style="opacity: 0.6; pointer-events: none;">Closed</a>
                        <?php
                    } else {
                        $payment_buttons = '';

                        if ( function_exists('learndash_payment_buttons') && in_array($course_price_type, ['paynow', 'subscribe', 'free']) ) { 
                            global $post;
                            $original_post = clone $post;
                            
                            $registration_page_id = class_exists('LearnDash_Settings_Section') 
                                ? (int) \LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Registration_Pages', 'registration')
                                : 0;
                                
                            if ($registration_page_id) {
                                $post = get_post($registration_page_id);
                                if ( $post ) setup_postdata($post);
                            }

                            $button_label_filter = function($label) use ($price_text) {
                                return 'Enrol Now ' . $price_text;
                            };
                            add_filter('learndash_payment_button_label', $button_label_filter, 99);
                            
                            $payment_buttons = learndash_payment_buttons( $post_id );
                            
                            remove_filter('learndash_payment_button_label', $button_label_filter, 99);
                            
                            if ($registration_page_id) {
                                $post = clone $original_post;
                                if ( $post ) wp_reset_postdata();
                            }

                           // FIX: Prevent duplicate Stripe JS
                            if ( strpos($payment_buttons, '<script') !== false ) {
                                if ( ! isset( $GLOBALS['oup_stripe_script_extracted'] ) ) {
                                    preg_match('/<script\b[^>]*>([\s\S]*?)<\/script>/i', $payment_buttons, $matches);
                                    if ( !empty($matches[0]) ) {
                                        $GLOBALS['oup_stripe_script_extracted'] = $matches[0];
                                        if ( ! wp_doing_ajax() ) {
                                            add_action('wp_footer', function() {
                                                echo $GLOBALS['oup_stripe_script_extracted'];
                                            }, 999);
                                        }
                                    }
                                }

                                $payment_buttons = preg_replace('/<script\b[^>]*>([\s\S]*?)<\/script>/i', '', $payment_buttons);
                            }
                        }

                        if ( !empty($payment_buttons) ) {
                            echo '<div class="custom-ld-payment-btn">';
                            echo $payment_buttons;
                            echo '</div>';
                        } else {
                            ?>
                            <a href="<?php echo esc_url($button_link); ?>" class="course-btn">Enrol Now<?php echo $price_text; ?></a>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </article>
        <?php
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $posts_per_page = (isset($settings['posts_per_page']) && $settings['posts_per_page'] !== '') ? intval($settings['posts_per_page']) : -1;
        $posts_per_page_tablet = (isset($settings['posts_per_page_tablet']) && $settings['posts_per_page_tablet'] !== '') ? intval($settings['posts_per_page_tablet']) : $posts_per_page;
        $posts_per_page_mobile = (isset($settings['posts_per_page_mobile']) && $settings['posts_per_page_mobile'] !== '') ? intval($settings['posts_per_page_mobile']) : $posts_per_page_tablet;

        $allowed_orderby = ['date', 'title', 'rand', 'menu_order'];
        $orderby = (!empty($settings['orderby']) && in_array($settings['orderby'], $allowed_orderby)) ? $settings['orderby'] : 'date';

        $allowed_order = ['ASC', 'DESC'];
        $order = (!empty($settings['order']) && in_array(strtoupper($settings['order']), $allowed_order)) ? strtoupper($settings['order']) : 'DESC';

        $nonce = wp_create_nonce('course_filter_nonce');

        $audiences = self::get_acf_field_choices('course_audience');
        $subjects = self::get_acf_field_choices('course_subject');
        $levels = self::get_acf_field_choices('course_learning_level');

        ?>
        <div class="course-filter-wrapper" 
            data-posts-per-page="<?php echo esc_attr($posts_per_page); ?>"
            data-posts-per-page-tablet="<?php echo esc_attr($posts_per_page_tablet); ?>"
            data-posts-per-page-mobile="<?php echo esc_attr($posts_per_page_mobile); ?>"
            data-orderby="<?php echo esc_attr($orderby); ?>" 
            data-order="<?php echo esc_attr($order); ?>"
            data-nonce="<?php echo esc_attr($nonce); ?>" 
            data-ajaxurl="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">

            <div class="course-top-bar">
                <div class="course-filters">
                    <div class="filter-group">
                        <label>Best for</label>
                        <div class="select-wrapper">
                            <select id="course_audience">
                                <option value="*">All</option>
                                <?php foreach ($audiences as $val => $label): ?>
                                    <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filter-group">
                        <label>Subject</label>
                        <div class="select-wrapper">
                            <select id="course_subject">
                                <option value="*">All</option>
                                <?php foreach ($subjects as $val => $label): ?>
                                    <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filter-group">
                        <label>Level</label>
                        <div class="select-wrapper">
                            <select id="course_learning_level">
                                <option value="*">All</option>
                                <?php foreach ($levels as $val => $label): ?>
                                    <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filter-clear-group">
                        <button type="button" class="course-clear-filters" aria-label="Clear all course filters">
                            Clear filters
                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L9 9M9 1L1 9" stroke="#9677D7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="course-ajax-results" class="course-grid">
                <?php
                $args = array(
                    'post_type' => 'sfwd-courses',
                    'posts_per_page' => $posts_per_page,
                    'orderby' => $orderby,
                    'order' => $order,
                    'post_status' => 'publish'
                );

                $query = new \WP_Query($args);
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        self::render_card(get_the_ID());
                    }
                    wp_reset_postdata();
                } else {
                    echo '<p class="no-results-msg">' . esc_html__('No courses found.', 'oup') . '</p>';
                }
                ?>
            </div>
        </div>
        <?php
    }

    protected function content_template()
    {
    }
}
