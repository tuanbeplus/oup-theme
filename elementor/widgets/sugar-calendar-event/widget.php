<?php

namespace OupElementorWidgets\Widgets\SugarCalendarEvent;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;

if (! defined('ABSPATH')) {
    exit;
}

class Widget_SugarCalendarEvent extends Widget_Base
{
    public function get_name(): string
    {
        return 'sugar-calendar-event';
    }

    public function get_title(): string
    {
        return __('Sugar Calendar Event Card', 'oup');
    }

    public function get_icon(): string
    {
        return 'eicon-calendar';
    }

    public function get_categories(): array
    {
        return ['oup'];
    }

    public function get_style_depends(): array
    {
        return ['oup-sugar-calendar-event-style'];
    }

    public function get_script_depends(): array
    {
        return ['oup-sugar-calendar-event-script'];
    }

    public function is_dynamic_content(): bool
    {
        return true;
    }

    // Controls
    protected function register_controls(): void
    {
        $this->register_content_controls();
        $this->register_style_controls();
    }

    private function register_content_controls(): void
    {
        // Query
        $this->start_controls_section('section_query', [
            'label' => __('Query', 'oup'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('posts_per_page', [
            'label'       => __('Posts Per Page', 'oup'),
            'type'        => Controls_Manager::NUMBER,
            'min'         => 1,
            'max'         => 100,
            'default'     => 6,
            'description' => __('Maximum number of upcoming events to display.', 'oup'),
        ]);
        $this->end_controls_section();

        // Featured Image
        $this->start_controls_section('section_image', [
            'label' => __('Featured Image', 'oup'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), [
            'name'    => 'featured_image',
            'default' => 'large',
            'exclude' => ['custom'],
        ]);
        $this->end_controls_section();

        // Button
        $this->start_controls_section('section_button', [
            'label' => __('Button', 'oup'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('button_text', [
            'label'   => __('Button Text', 'oup'),
            'type'    => Controls_Manager::TEXT,
            'default' => __('Sign Up', 'oup'),
        ]);
        $this->add_control('button_url', [
            'label'         => __('Button URL', 'oup'),
            'type'          => Controls_Manager::URL,
            'placeholder'   => __('https://...', 'oup'),
            'default'       => ['url' => '#', 'is_external' => false, 'nofollow' => false],
            'show_external' => true,
        ]);
        $this->end_controls_section();

        // Slider
        $this->start_controls_section('section_slider', [
            'label' => __('Slider', 'oup'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('enable_slider', [
            'label'        => __('Enable Slider', 'oup'),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __('Yes', 'oup'),
            'label_off'    => __('No', 'oup'),
            'return_value' => 'yes',
            'default'      => '',
        ]);
        $this->add_control('enable_navigation', [
            'label'        => __('Navigation Arrows', 'oup'),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __('Yes', 'oup'),
            'label_off'    => __('No', 'oup'),
            'return_value' => 'yes',
            'default'      => 'yes',
            'condition'    => ['enable_slider' => 'yes'],
        ]);
        $this->add_responsive_control('nav_visibility', [
            'label'          => __('Navigation Visibility', 'oup'),
            'type'           => Controls_Manager::SELECT,
            'options'        => ['flex' => __('Show', 'oup'), 'none' => __('Hide', 'oup')],
            'default'        => 'flex',
            'tablet_default' => 'flex',
            'mobile_default' => 'none',
            'selectors'      => [
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'display: {{VALUE}} !important;',
            ],
            'condition' => ['enable_slider' => 'yes', 'enable_navigation' => 'yes'],
        ]);
        $this->add_control('enable_pagination', [
            'label'        => __('Pagination Dots', 'oup'),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __('Yes', 'oup'),
            'label_off'    => __('No', 'oup'),
            'return_value' => 'yes',
            'default'      => 'yes',
            'condition'    => ['enable_slider' => 'yes'],
        ]);
        $this->add_responsive_control('pag_visibility', [
            'label'          => __('Pagination Visibility', 'oup'),
            'type'           => Controls_Manager::SELECT,
            'options'        => ['block' => __('Show', 'oup'), 'none' => __('Hide', 'oup')],
            'default'        => 'block',
            'tablet_default' => 'block',
            'mobile_default' => 'block',
            'selectors'      => ['{{WRAPPER}} .swiper-pagination' => 'display: {{VALUE}} !important;'],
            'condition'      => ['enable_slider' => 'yes', 'enable_pagination' => 'yes'],
        ]);
        $this->add_control('enable_autoplay', [
            'label'        => __('Autoplay', 'oup'),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __('Yes', 'oup'),
            'label_off'    => __('No', 'oup'),
            'return_value' => 'yes',
            'default'      => '',
            'condition'    => ['enable_slider' => 'yes'],
        ]);
        $this->add_control('autoplay_delay', [
            'label'     => __('Autoplay Delay (ms)', 'oup'),
            'type'      => Controls_Manager::NUMBER,
            'min'       => 500,
            'max'       => 10000,
            'step'      => 100,
            'default'   => 3000,
            'condition' => ['enable_slider' => 'yes', 'enable_autoplay' => 'yes'],
        ]);
        $this->add_control('enable_loop', [
            'label'        => __('Loop', 'oup'),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __('Yes', 'oup'),
            'label_off'    => __('No', 'oup'),
            'return_value' => 'yes',
            'default'      => '',
            'condition'    => ['enable_slider' => 'yes'],
        ]);
        $this->end_controls_section();
    }

    private function register_style_controls(): void
    {
        // Card
        $this->start_controls_section('style_card', [
            'label' => __('Card', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('card_bg_color', [
            'label'     => __('Background Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .sce-card' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_responsive_control('card_padding', [
            'label'      => __('Padding', 'oup'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', 'rem'],
            'selectors'  => ['{{WRAPPER}} .sce-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->add_control('card_border_radius', [
            'label'      => __('Border Radius', 'oup'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors'  => ['{{WRAPPER}} .sce-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->end_controls_section();

        // Featured Image
        $this->start_controls_section('style_image', [
            'label' => __('Featured Image', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_responsive_control('image_border_radius', [
            'label'      => __('Border Radius', 'oup'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors'  => ['{{WRAPPER}} .sce-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->add_responsive_control('image_margin_bottom', [
            'label'      => __('Bottom Spacing', 'oup'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em'],
            'selectors'  => ['{{WRAPPER}} .sce-image' => 'margin-bottom: {{SIZE}}{{UNIT}};'],
        ]);
        $this->end_controls_section();

        // Typography
        $this->start_controls_section('style_typography', [
            'label' => __('Typography', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'label'    => __('Title', 'oup'),
            'selector' => '{{WRAPPER}} .sce-title',
        ]);
        $this->add_control('title_color', [
            'label'     => __('Title Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .sce-title' => 'color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'excerpt_typography',
            'label'    => __('Excerpt', 'oup'),
            'selector' => '{{WRAPPER}} .sce-excerpt',
        ]);
        $this->add_control('excerpt_color', [
            'label'     => __('Excerpt Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .sce-excerpt' => 'color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'meta_typography',
            'label'    => __('Meta (Date / Notice / Location)', 'oup'),
            'selector' => '{{WRAPPER}} .sce-meta-item',
        ]);
        $this->add_control('meta_color', [
            'label'     => __('Meta Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .sce-meta-item' => 'color: {{VALUE}};'],
        ]);
        $this->end_controls_section();

        // Button
        $this->start_controls_section('style_button', [
            'label' => __('Button', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('button_bg_color', [
            'label'     => __('Background Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .sce-button' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_control('button_text_color', [
            'label'     => __('Text Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .sce-button' => 'color: {{VALUE}};'],
        ]);
        $this->add_control('button_hover_bg_color', [
            'label'     => __('Hover Background Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .sce-button:hover' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'button_typography',
            'label'    => __('Typography', 'oup'),
            'selector' => '{{WRAPPER}} .sce-button',
        ]);
        $this->add_responsive_control('button_border_radius', [
            'label'      => __('Border Radius', 'oup'),
            'size_units' => ['px', '%'],
            'type'       => Controls_Manager::DIMENSIONS,
            'selectors'  => ['{{WRAPPER}} .sce-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->add_responsive_control('button_padding', [
            'label'      => __('Padding', 'oup'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'selectors'  => ['{{WRAPPER}} .sce-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->end_controls_section();

        // Slider
        $this->start_controls_section('style_slider', [
            'label'     => __('Slider', 'oup'),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => ['enable_slider' => 'yes'],
        ]);
        $this->add_control('nav_color', [
            'label'     => __('Arrow Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .sce-swiper .swiper-button-prev, {{WRAPPER}} .sce-swiper .swiper-button-next' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_control('pagination_color', [
            'label'     => __('Pagination Active Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .sce-swiper .swiper-pagination-bullet-active' => 'background: {{VALUE}};'],
        ]);
        $this->end_controls_section();
    }

    // Helpers
    private function get_sc_event_rows(int $post_id): array
    {
        global $wpdb;

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}sc_events WHERE object_id = %d ORDER BY start ASC",
            $post_id
        )) ?: [];
    }

    private function resolve_ts($value): int
    {
        if (empty($value)) return 0;
        if (is_numeric($value)) return (int) $value;

        try {
            return (new \DateTime($value, new \DateTimeZone('UTC')))->getTimestamp();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function get_event_expiry_ts(array $event): int
    {
        if ($event['is_recurring'] && ! empty($event['occurrences'])) {
            $last = end($event['occurrences']);
            return $last['end_ts'] ?: $last['start_ts'];
        }

        return $event['end_ts'] ?: $event['start_ts'];
    }

    private function get_all_events(int $limit = 0): array
    {
        $ids = get_posts([
            'post_type'      => ['sc_event', 'sc_recurring_event'],
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'no_found_rows'  => true,
            'fields'         => 'ids',
        ]);

        if (empty($ids)) return [];

        $now  = time();
        $flat = [];

        foreach ($ids as $event_id) {
            $rows = $this->get_sc_event_rows($event_id);
            if (empty($rows)) continue;

            $row    = $rows[0];
            $flat[] = [
                'post_id'              => $event_id,
                'title'                => get_the_title($event_id),
                'recurrence_rule'      => trim((string) ($row->recurrence ?? '')),
                'recurrence_interval'  => max(1, (int) ($row->recurrence_interval ?? 1)),
                'recurrence_end'       => (string) ($row->recurrence_end ?? ''),
                'recurrence_end_count' => (int) ($row->recurrence_count ?? 0),
                'start_ts'             => $this->resolve_ts($row->start),
                'end_ts'               => $this->resolve_ts($row->end),
            ];
        }

        $groups = [];
        foreach ($flat as $item) {
            $is_recurring = ! empty($item['recurrence_rule']) && $item['recurrence_rule'] !== 'never';
            $key          = $is_recurring
                ? 'recurring|' . strtolower(trim($item['title']))
                : 'single|' . $item['post_id'];
            $groups[$key][] = $item;
        }

        $events = [];
        foreach ($groups as $key => $members) {
            usort($members, fn($a, $b) => $a['start_ts'] <=> $b['start_ts']);

            $primary      = $members[0];
            $post_id      = $primary['post_id'];
            $is_recurring = str_starts_with($key, 'recurring|');

            $event = [
                'id'           => $post_id,
                'title'        => get_the_title($post_id),
                'excerpt'      => get_the_excerpt($post_id),
                'thumbnail_id' => get_post_thumbnail_id($post_id),
                'start_ts'     => $primary['start_ts'],
                'end_ts'       => $primary['end_ts'],
                'is_recurring' => $is_recurring,
                'occurrences'  => [],
            ];

            if ($is_recurring) {
                $first_start = $primary['start_ts'];
                $duration    = max(0, $primary['end_ts'] - $first_start);
                $rule        = $primary['recurrence_rule'];
                $interval    = $primary['recurrence_interval'];
                $rec_end     = $primary['recurrence_end'];
                $rec_count   = $primary['recurrence_end_count'];

                $build = fn(int $ts) => [
                    'start_ts' => $ts,
                    'end_ts'   => $ts + $duration,
                ];

                $step = function (int $ts) use ($rule, $interval): int {
                    $r = strtolower(trim($rule));
                    if ($r === 'daily')  return DAY_IN_SECONDS * $interval;
                    if ($r === 'weekly') return WEEK_IN_SECONDS * $interval;
                    try {
                        $dt = (new \DateTime('@' . $ts))->setTimezone(new \DateTimeZone('UTC'));
                        $dt->modify($r === 'yearly' ? "+{$interval} year" : "+{$interval} month");
                        return max(DAY_IN_SECONDS, $dt->getTimestamp() - $ts);
                    } catch (\Exception $e) {
                        return ($r === 'yearly' ? 365 : 30) * DAY_IN_SECONDS * $interval;
                    }
                };

                $ts  = $first_start;
                $occ = [];

                if ($rec_count > 0) {
                    for ($i = 0; $i < $rec_count; $i++) {
                        $occ[] = $build($ts);
                        $ts   += $step($ts);
                    }
                } elseif (! empty($rec_end) && $rec_end !== '0000-00-00 00:00:00') {
                    $boundary = $this->resolve_ts($rec_end);
                    while ($ts <= $boundary) {
                        $occ[] = $build($ts);
                        $ts   += $step($ts);
                    }
                } else {
                    foreach ($members as $ignored) {
                        $occ[] = $build($ts);
                        $ts   += $step($ts);
                    }
                }

                $event['occurrences'] = $occ;
            }

            if (! $event['start_ts']) continue;

            $expiry = $this->get_event_expiry_ts($event);
            if ($expiry > 0 && $expiry <= $now) continue;

            $events[] = $event;
        }

        usort($events, fn($a, $b) => $a['start_ts'] <=> $b['start_ts']);

        return $limit > 0 ? array_slice($events, 0, $limit) : $events;
    }

    // Rendering
    private function render_card(array $event, array $settings, string $img_size): void
    {
        $footnote     = trim((string) get_field('foot_note', $event['id']));
        $btn_url      = ! empty($settings['button_url']['url']) ? $settings['button_url']['url'] : '#';
        $btn_target   = ! empty($settings['button_url']['is_external']) ? ' target="_blank"' : '';
        $btn_nofollow = ! empty($settings['button_url']['nofollow']) ? ' rel="nofollow"' : '';
        $btn_text     = ! empty($settings['button_text']) ? $settings['button_text'] : __('Sign Up', 'oup');
?>
        <div class="sce-card">

            <?php if (! empty($event['thumbnail_id'])) :
                $img = wp_get_attachment_image($event['thumbnail_id'], $img_size, false, ['class' => 'sce-img']);
                if ($img) : ?>
                    <div class="sce-image"><?php echo $img; ?></div>
            <?php endif;
            endif; ?>

            <h3 class="sce-title"><?php echo esc_html($event['title']); ?></h3>

            <?php if (! empty($event['excerpt'])) : ?>
                <div class="sce-excerpt"><?php echo wp_kses_post(wpautop($event['excerpt'])); ?></div>
            <?php endif; ?>

            <?php echo do_shortcode('[oup_event_meta event_id="' . (int) $event['id'] . '"]'); ?>

            <?php if (! empty($footnote)) : ?>
                <p class="sce-footnote"><?php echo wp_kses_post(nl2br(esc_html($footnote))); ?></p>
            <?php endif; ?>

            <div class="sce-spacer"></div>

            <?php echo do_shortcode('[oup_event_pricing event_id="' . (int) $event['id'] . '"]'); ?>

            <div class="sce-button-wrap">
                <a href="<?php echo esc_url($btn_url); ?>" class="sce-button" <?php echo $btn_target . $btn_nofollow; ?>>
                    <?php echo esc_html($btn_text); ?>
                </a>
            </div>

        </div>
    <?php
    }

    protected function render(): void
    {
        $settings  = $this->get_settings_for_display();
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();

        $posts_per_page = $is_editor ? 0 : (int) ($settings['posts_per_page'] ?? 6);
        $enable_slider  = ($settings['enable_slider'] ?? '') === 'yes';
        $events         = $this->get_all_events($posts_per_page);

        if (empty($events)) {
            echo '<div class="sce-placeholder"><p>' . esc_html__('No upcoming events found.', 'oup') . '</p></div>';
            return;
        }

        $img_size = $settings['featured_image_size'] ?? 'large';

        if (! $enable_slider) {
            echo '<div class="sce-event-list">';
            foreach ($events as $event) {
                $this->render_card($event, $settings, $img_size);
            }
            echo '</div>';
            return;
        }

        $total        = count($events);
        $needs_scroll = $total > 2;
        $show_nav     = $needs_scroll && ($settings['enable_navigation'] ?? '') === 'yes';
        $show_pag     = $needs_scroll && ($settings['enable_pagination'] ?? '') === 'yes';

        $swiper_cfg = [
            'slidesPerView' => 1,
            'spaceBetween'  => 24,
            'loop'          => $needs_scroll && ($settings['enable_loop'] ?? '') === 'yes',
            'navigation'    => $show_nav,
            'pagination'    => $show_pag,
            'breakpoints'   => [
                768  => ['slidesPerView' => 2, 'spaceBetween' => 36],
                1025 => ['slidesPerView' => 2, 'spaceBetween' => 50],
            ],
        ];

        if (($settings['enable_autoplay'] ?? '') === 'yes') {
            $swiper_cfg['autoplay'] = [
                'delay'                => (int) ($settings['autoplay_delay'] ?? 3000),
                'disableOnInteraction' => false,
            ];
        }

        $uid = 'sce-swiper-' . $this->get_id();
    ?>
        <div class="sce-swiper-outer">
            <div class="swiper sce-swiper" id="<?php echo esc_attr($uid); ?>" data-swiper="<?php echo esc_attr(wp_json_encode($swiper_cfg)); ?>">
                <div class="swiper-wrapper">
                    <?php foreach ($events as $event) : ?>
                        <div class="swiper-slide">
                            <?php $this->render_card($event, $settings, $img_size); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($show_nav) : ?>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                <?php endif; ?>
                <?php if ($show_pag) : ?>
                    <div class="swiper-pagination"></div>
                <?php endif; ?>
            </div>
        </div>
<?php
    }

    protected function content_template(): void {}
}
