<?php

namespace OupElementorWidgets\Widgets\SugarCalendarEvent;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

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

    protected function register_controls(): void
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
        $this->add_control('button_use_event_link', [
            'label'        => __('Link to Single Event Page', 'oup'),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __('Yes', 'oup'),
            'label_off'    => __('No', 'oup'),
            'return_value' => 'yes',
            'default'      => 'yes',
            'description'  => __('When enabled, the button links to the single event page instead of a custom URL.', 'oup'),
        ]);
        $this->add_control('button_url', [
            'label'         => __('Button URL', 'oup'),
            'type'          => Controls_Manager::URL,
            'placeholder'   => __('https://...', 'oup'),
            'default'       => ['url' => '#', 'is_external' => false, 'nofollow' => false],
            'show_external' => true,
            'condition'     => ['button_use_event_link!' => 'yes'],
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

        // Style: Card
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

        // Style: Featured Image
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

        // Style: Typography
        $this->start_controls_section('style_typography', [
            'label' => __('Typography', 'oup'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'label'    => __('Title', 'oup'),
            'selector' => '{{WRAPPER}} .sce-title, {{WRAPPER}} .sce-title a',
        ]);
        $this->add_control('title_color', [
            'label'     => __('Title Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .sce-title'   => 'color: {{VALUE}};',
                '{{WRAPPER}} .sce-title a' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_control('title_hover_color', [
            'label'     => __('Title Hover Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .sce-title a:hover' => 'color: {{VALUE}};'],
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

        // Style: Button
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
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors'  => ['{{WRAPPER}} .sce-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->add_responsive_control('button_padding', [
            'label'      => __('Padding', 'oup'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'selectors'  => ['{{WRAPPER}} .sce-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->end_controls_section();

        // Style: Slider
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
    private function get_site_timezone(): \DateTimeZone
    {
        return wp_timezone();
    }

    private function resolve_ts(mixed $value): int
    {
        if (empty($value)) return 0;
        if (is_numeric($value)) return (int) $value;
        try {
            return (new \DateTime($value, new \DateTimeZone('UTC')))->getTimestamp();
        } catch (\Exception) {
            return 0;
        }
    }

    private function get_sc_event_permalink(int $post_id, int $start_ts, bool $is_recurring = false): string
    {
        if (function_exists('sugar_calendar_get_event_link')) {
            $url = sugar_calendar_get_event_link($post_id);
            if (! empty($url) && $url !== '#') return $url;
        }

        if (function_exists('SC') && is_callable([SC(), 'events']) && method_exists(SC()->events, 'get_link')) {
            $url = SC()->events->get_link($post_id);
            if (! empty($url) && $url !== '#') return $url;
        }

        $base_slug = 'events';
        $sc_option = get_option('sc_event_permalink_base');

        if (! empty($sc_option)) {
            $base_slug = trim($sc_option, '/');
        } else {
            $post_type_obj = get_post_type_object('sc_event');
            if (
                $post_type_obj &&
                ! empty($post_type_obj->rewrite['slug']) &&
                ! str_starts_with($post_type_obj->rewrite['slug'], 'sc_')
            ) {
                $base_slug = trim($post_type_obj->rewrite['slug'], '/');
            }
        }

        $post_slug = get_post_field('post_name', $post_id);
        if (empty($post_slug)) {
            return get_permalink($post_id) ?: '#';
        }

        $url = trailingslashit(home_url()) . trailingslashit($base_slug) . trailingslashit($post_slug);

        if ($is_recurring && $start_ts > 0) {
            $url .= trailingslashit(gmdate('Y-m-d', $start_ts));
        }

        return $url;
    }

    private function resolve_button_url(array $event, array $settings): array
    {
        $event_sign_up_link = get_field('event_sign_up_link', $event['id']);

        if (! empty($event_sign_up_link)) {
            return [
                'url'         => $event_sign_up_link,
                'is_external' => true,
                'nofollow'    => true,
            ];
        }

        if (($settings['button_use_event_link'] ?? 'yes') === 'yes') {
            return [
                'url'         => $event['permalink'] ?: $this->get_sc_event_permalink($event['id'], $event['start_ts'], $event['is_recurring']),
                'is_external' => false,
                'nofollow'    => false,
            ];
        }

        return [
            'url'         => ! empty($settings['button_url']['url']) ? $settings['button_url']['url'] : '#',
            'is_external' => ! empty($settings['button_url']['is_external']),
            'nofollow'    => ! empty($settings['button_url']['nofollow']),
        ];
    }

    private function get_venue_string(int $sc_event_row_id, int $post_id): string
    {
        global $wpdb;
        static $cache = [];

        if (isset($cache[$sc_event_row_id])) return $cache[$sc_event_row_id];

        $venue_id = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT venue_id FROM {$wpdb->prefix}sc_events WHERE id = %d",
            $sc_event_row_id
        ));

        if ($venue_id <= 0) {
            return $cache[$sc_event_row_id] = trim((string) get_post_meta($post_id, 'location', true));
        }

        $venue_post = get_post($venue_id);
        if (! $venue_post || $venue_post->post_status !== 'publish') {
            return $cache[$sc_event_row_id] = '';
        }

        $name    = trim($venue_post->post_title);
        $address = trim((string) get_post_meta($venue_id, 'sugarcalendar_venue_address_1', true));

        return $cache[$sc_event_row_id] = $address !== '' ? "{$name}, {$address}" : $name;
    }

    private function get_sc_event_rows(int $post_id): array
    {
        global $wpdb;

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}sc_events WHERE object_id = %d ORDER BY start ASC",
            $post_id
        )) ?: [];
    }

    private function get_step_seconds(string $rule, int $interval, int $from_ts): int
    {
        $rule = strtolower(trim($rule));

        if ($rule === 'daily')  return DAY_IN_SECONDS  * $interval;
        if ($rule === 'weekly') return WEEK_IN_SECONDS * $interval;

        try {
            $dt = (new \DateTime('@' . $from_ts))->setTimezone(new \DateTimeZone('UTC'));
            $dt->modify($rule === 'yearly' ? "+{$interval} year" : "+{$interval} month");
            return max(DAY_IN_SECONDS, $dt->getTimestamp() - $from_ts);
        } catch (\Exception) {
            return ($rule === 'yearly' ? 365 : 30) * DAY_IN_SECONDS * $interval;
        }
    }

    private function build_occurrence(int $ts, int $duration): array
    {
        $end_ts = $ts + $duration;
        return [
            'start_ts' => $ts,
            'end_ts'   => $end_ts,
            'lines'    => $this->format_duration($ts, $end_ts),
        ];
    }

    private function calculate_occurrences(
        array  $members,
        string $rule,
        int    $interval             = 1,
        string $recurrence_end       = '',
        int    $recurrence_end_count = 0
    ): array {
        if (empty($members)) return [];

        $first_start = $members[0]['start_ts'];
        $duration    = max(0, $members[0]['end_ts'] - $first_start);

        // Case 1: After X times
        if ($recurrence_end_count > 0) {
            $occurrences = [];
            $ts          = $first_start;
            for ($i = 0; $i < $recurrence_end_count; $i++) {
                $occurrences[] = $this->build_occurrence($ts, $duration);
                $ts += $this->get_step_seconds($rule, $interval, $ts);
            }
            return $occurrences;
        }

        // Case 2: Until date
        $end_boundary = (! empty($recurrence_end) && $recurrence_end !== '0000-00-00 00:00:00')
            ? $this->resolve_ts($recurrence_end)
            : 0;

        if ($end_boundary > 0) {
            $occurrences = [];
            $ts          = $first_start;
            while ($ts <= $end_boundary) {
                $occurrences[] = $this->build_occurrence($ts, $duration);
                $ts += $this->get_step_seconds($rule, $interval, $ts);
            }
            return $occurrences;
        }

        // Case 3: Fallback — post count
        $occurrences = [];
        $ts          = $first_start;
        foreach ($members as $_) {
            $occurrences[] = $this->build_occurrence($ts, $duration);
            $ts += $this->get_step_seconds($rule, $interval, $ts);
        }

        return $occurrences;
    }

    private function get_event_expiry_ts(array $event): int
    {
        if ($event['is_recurring'] && ! empty($event['occurrences'])) {
            $last = end($event['occurrences']);
            return $last['end_ts'] ?: $last['start_ts'];
        }

        return $event['end_ts'] ?: $event['start_ts'];
    }

    private function format_duration(int $start_ts, int $end_ts = 0): array
    {
        if (! $start_ts) return [];

        $tz       = $this->get_site_timezone();
        $tz_label = ' ' . (new \DateTime('@' . $start_ts))->setTimezone($tz)->format('T');
        $time_str = date_i18n('g:iA', $start_ts);
        $same_day = $end_ts && date_i18n('Ymd', $start_ts) === date_i18n('Ymd', $end_ts);

        if ($same_day) {
            $time_str .= ' – ' . date_i18n('g:iA', $end_ts);
        }

        $lines = [date_i18n('l, j F Y', $start_ts) . ' at ' . $time_str . $tz_label];

        if ($end_ts && ! $same_day) {
            $lines[] = date_i18n('l, j F Y', $end_ts) . ' at ' . date_i18n('g:iA', $end_ts) . $tz_label;
        }

        return $lines;
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

        foreach ($ids as $post_id) {
            $rows = $this->get_sc_event_rows($post_id);
            if (empty($rows)) continue;

            $row    = $rows[0];
            $flat[] = [
                'post_id'              => $post_id,
                'title'                => get_the_title($post_id),
                'recurrence_rule'      => trim((string) ($row->recurrence ?? '')),
                'recurrence_interval'  => max(1, (int) ($row->recurrence_interval ?? 1)),
                'recurrence_end'       => (string) ($row->recurrence_end ?? ''),
                'recurrence_end_count' => (int) ($row->recurrence_count ?? 0),
                'start_ts'             => $this->resolve_ts($row->start),
                'end_ts'               => $this->resolve_ts($row->end),
                'sc_id'                => (int) $row->id,
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
                'id'             => $post_id,
                'title'          => get_the_title($post_id),
                'permalink'      => '',
                'excerpt'        => get_the_excerpt($post_id),
                'thumbnail_id'   => get_post_thumbnail_id($post_id),
                'start_ts'       => $primary['start_ts'],
                'end_ts'         => $primary['end_ts'],
                'location'       => $this->get_venue_string($primary['sc_id'], $post_id),
                'duration_lines' => [],
                'occurrences'    => [],
                'is_recurring'   => $is_recurring,
            ];

            if ($is_recurring) {
                $event['occurrences'] = $this->calculate_occurrences(
                    $members,
                    $primary['recurrence_rule'],
                    $primary['recurrence_interval'],
                    $primary['recurrence_end'],
                    $primary['recurrence_end_count']
                );
            } else {
                $event['duration_lines'] = $this->format_duration($primary['start_ts'], $primary['end_ts']);
            }

            if (! $event['start_ts']) continue;
            if (($this->get_event_expiry_ts($event) ?: PHP_INT_MAX) <= $now) continue;

            $event['permalink'] = $this->get_sc_event_permalink($post_id, $event['start_ts'], $is_recurring);

            $events[] = $event;
        }

        usort($events, fn($a, $b) => $a['start_ts'] <=> $b['start_ts']);

        return $limit > 0 ? array_slice($events, 0, $limit) : $events;
    }

    // Rendering
    private function render_card(array $event, array $settings, string $img_size): void
    {
        $footnote     = trim((string) get_field('foot_note', $event['id']));
        $pricing      = get_field('event_ticket_pricing', $event['id']);
        $pricing      = is_array($pricing) ? array_values(array_filter($pricing)) : [];
        $btn_text     = ! empty($settings['button_text']) ? $settings['button_text'] : __('Sign Up', 'oup');
        $resolved     = $this->resolve_button_url($event, $settings);
        $btn_url      = $resolved['url'];
        $btn_target   = $resolved['is_external'] ? ' target="_blank"' : '';
        $btn_nofollow = $resolved['nofollow'] ? ' rel="nofollow"' : '';
        $event_link   = $event['permalink'] ?: $this->get_sc_event_permalink($event['id'], $event['start_ts'], $event['is_recurring']);
?>
        <div class="sce-card">

            <?php
            if (! empty($event['thumbnail_id'])) :
                $img = wp_get_attachment_image($event['thumbnail_id'], $img_size, false, ['class' => 'sce-img']);
                if ($img) :
            ?>
                    <div class="sce-image"><?php echo $img; ?></div>
            <?php endif;
            endif; ?>

            <h3 class="sce-title">
                <a href="<?php echo esc_url($event_link); ?>">
                    <?php echo esc_html($event['title']); ?>
                </a>
            </h3>

            <?php if (! empty($event['excerpt'])) : ?>
                <div class="sce-excerpt"><?php echo wp_kses_post(wpautop($event['excerpt'])); ?></div>
            <?php endif; ?>

            <?php echo do_shortcode('[oup_event_meta event_id="' . (int) $event['id'] . '"]'); ?>

            <?php if (! empty($footnote)) : ?>
                <p class="sce-footnote"><?php echo wp_kses_post(nl2br(esc_html($footnote))); ?></p>
            <?php endif; ?>

            <div class="sce-spacer"></div>

            <?php if (! empty($pricing)) :
                $rows = array_chunk($pricing, 2);
            ?>
                <div class="sce-pricing-wrap">
                    <?php foreach ($rows as $row) : ?>
                        <div class="sce-pricing">
                            <?php foreach ($row as $i => $ticket) :
                                $ticket_name  = trim((string) ($ticket['ticket_name'] ?? ''));
                                $ticket_price = trim((string) ($ticket['price'] ?? ''));
                                if (! $ticket_name && ! $ticket_price) continue;
                            ?>
                                <?php if ($i > 0) : ?><div class="sce-price-divider"></div><?php endif; ?>
                                <div class="sce-price-item">
                                    <div class="sce-price-amount-wrap">
                                        <span class="sce-price-amount">$<?php echo esc_html($ticket_price); ?></span>
                                        <span class="sce-price-suffix"><?php esc_html_e('inc. GST', 'oup'); ?></span>
                                    </div>
                                    <span class="sce-price-label"><?php echo esc_html($ticket_name); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="sce-button-wrap">
                <a href="<?php echo esc_url($btn_url); ?>" class="sce-button" <?php echo $btn_target . $btn_nofollow; ?>>
                    <?php echo esc_html($btn_text); ?>
                </a>
            </div>

        </div>
    <?php
    }

    private function build_swiper_config(array $settings, int $total): array
    {
        $needs_scroll = $total > 2;
        $show_nav     = $needs_scroll && ($settings['enable_navigation'] ?? '') === 'yes';
        $show_pag     = $needs_scroll && ($settings['enable_pagination'] ?? '') === 'yes';

        $config = [
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
            $config['autoplay'] = [
                'delay'                => (int) ($settings['autoplay_delay'] ?? 3000),
                'disableOnInteraction' => false,
            ];
        }

        return $config + ['_show_nav' => $show_nav, '_show_pag' => $show_pag];
    }

    protected function render(): void
    {
        $settings       = $this->get_settings_for_display();
        $is_editor      = \Elementor\Plugin::$instance->editor->is_edit_mode();
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

        $swiper_cfg = $this->build_swiper_config($settings, count($events));
        $show_nav   = $swiper_cfg['_show_nav'];
        $show_pag   = $swiper_cfg['_show_pag'];
        unset($swiper_cfg['_show_nav'], $swiper_cfg['_show_pag']);

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