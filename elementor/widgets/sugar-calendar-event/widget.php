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
    public function get_name()
    {
        return 'sugar-calendar-event';
    }

    public function get_title()
    {
        return __('Sugar Calendar Event Card', 'oup');
    }

    public function get_icon()
    {
        return 'eicon-calendar';
    }

    public function get_categories()
    {
        return ['oup'];
    }

    public function get_style_depends()
    {
        return ['oup-sugar-calendar-event-style'];
    }

    public function get_script_depends()
    {
        return ['oup-sugar-calendar-event-script'];
    }

    public function is_dynamic_content(): bool
    {
        return true;
    }

    protected function register_controls()
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

        // Image
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
            'default'       => [
                'url'         => '#',
                'is_external' => false,
                'nofollow'    => false,
            ],
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
            'options'        => [
                'flex' => __('Show', 'oup'),
                'none' => __('Hide', 'oup'),
            ],
            'default'        => 'flex',
            'tablet_default' => 'flex',
            'mobile_default' => 'none',
            'selectors'      => [
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'display: {{VALUE}} !important;',
            ],
            'condition'      => ['enable_slider' => 'yes', 'enable_navigation' => 'yes'],
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
            'options'        => [
                'block' => __('Show', 'oup'),
                'none'  => __('Hide', 'oup'),
            ],
            'default'        => 'block',
            'tablet_default' => 'block',
            'mobile_default' => 'block',
            'selectors'      => [
                '{{WRAPPER}} .swiper-pagination' => 'display: {{VALUE}} !important;',
            ],
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
            'condition' => [
                'enable_slider'   => 'yes',
                'enable_autoplay' => 'yes',
            ],
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

        // STYLE: Card
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

        $this->add_control('card_box_shadow', [
            'label'     => __('Box Shadow', 'oup'),
            'type'      => Controls_Manager::BOX_SHADOW,
            'selectors' => ['{{WRAPPER}} .sce-card' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};'],
        ]);

        $this->end_controls_section();

        // STYLE: Image
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

        // STYLE: Typography
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

        // STYLE: Button
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

        // STYLE: Slider
        $this->start_controls_section('style_slider', [
            'label'     => __('Slider', 'oup'),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => ['enable_slider' => 'yes'],
        ]);

        $this->add_control('nav_color', [
            'label'     => __('Arrow Color', 'oup'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .sce-swiper .swiper-button-prev, {{WRAPPER}} .sce-swiper .swiper-button-next' => 'color: {{VALUE}};'],
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

    private function resolve_ts($value): int
    {
        if (empty($value)) return 0;
        if (is_numeric($value)) return (int) $value;

        try {
            $dt = new \DateTime($value, new \DateTimeZone('UTC'));
            return $dt->getTimestamp();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function get_venue_string(int $sc_event_row_id, int $post_id): string
    {
        global $wpdb;
        $venue_id = 0;

        if (function_exists('sugar_calendar_get_event_by_object')) {
            $sc_obj = sugar_calendar_get_event_by_object($post_id, 'post');

            if (! empty($sc_obj)) {
                $venue_id = (int) ($sc_obj->venue_id ?? 0);
            }
        }

        if ($venue_id > 0) {
            $venue_post = get_post($venue_id);

            if ($venue_post && $venue_post->post_status === 'publish') {
                $venue_name = trim($venue_post->post_title);
                $address_1  = trim((string) get_post_meta($venue_id, 'sugarcalendar_venue_address_1', true));

                if ($venue_name !== '') {
                    return $address_1 !== ''
                        ? $venue_name . ', ' . $address_1
                        : $venue_name;
                }
            }
        }

        $legacy_location = (string) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value
                 FROM {$wpdb->prefix}sc_eventmeta
                 WHERE sc_event_id = %d AND meta_key = %s
                 LIMIT 1",
                $sc_event_row_id,
                'location'
            )
        );

        return trim($legacy_location);
    }

    // Only fetch upcoming events — past events are always excluded
    private function get_all_events(int $limit = 6): array
    {
        $ids = get_posts([
            'post_type'      => 'sc_event',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'no_found_rows'  => true,
            'fields'         => 'ids',
        ]);

        if (empty($ids)) return [];

        $now    = time();
        $events = [];

        foreach ($ids as $event_id) {
            $data = $this->build_event_data($event_id);

            if (! $data) continue;

            // Skip past events — same behaviour in editor and frontend
            $ref_ts = $data['end_ts'] > 0 ? $data['end_ts'] : $data['start_ts'];
            if ($ref_ts < $now) continue;

            $events[] = $data;
        }

        usort($events, fn($a, $b) => $a['start_ts'] <=> $b['start_ts']);

        if ($limit > 0) {
            $events = array_slice($events, 0, $limit);
        }

        return $events;
    }

    private function build_event_data(int $event_id): ?array
    {
        global $wpdb;

        $post = get_post($event_id);
        if (! $post || $post->post_status !== 'publish') return null;

        $data = [
            'id'             => $event_id,
            'title'          => get_the_title($event_id),
            'excerpt'        => get_the_excerpt($event_id),
            'thumbnail_id'   => get_post_thumbnail_id($event_id),
            'start'          => '',
            'end'            => '',
            'start_ts'       => 0,
            'end_ts'         => 0,
            'location'       => '',
            'duration_lines' => [],
        ];

        $sc_event = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}sc_events WHERE object_id = %d",
                $event_id
            )
        );

        if ($sc_event) {
            $data['start']    = $sc_event->start;
            $data['end']      = $sc_event->end;
            $data['start_ts'] = $this->resolve_ts($sc_event->start);
            $data['end_ts']   = $this->resolve_ts($sc_event->end);

            // Use the new venue-aware helper instead of the old raw meta query
            $data['location'] = $this->get_venue_string((int) $sc_event->id, $event_id);
        }

        if (! $data['start_ts']) return null;

        $data['duration_lines'] = $this->format_duration($data['start_ts'], $data['end_ts']);

        return $data;
    }

    private function format_duration(int $start_ts, int $end_ts = 0): array
    {
        if (! $start_ts) return [];

        $tz       = $this->get_site_timezone();
        $dt       = new \DateTime('@' . $start_ts);
        $dt->setTimezone($tz);
        $tz_label = ' ' . $dt->format('T');

        $date_str = date_i18n('l, j F Y', $start_ts);
        $time_str = date_i18n('g:iA', $start_ts);

        // Same-day range: append end time on same line
        if ($end_ts && date_i18n('Ymd', $start_ts) === date_i18n('Ymd', $end_ts)) {
            $time_str .= ' – ' . date_i18n('g:iA', $end_ts);
        }

        $lines[] = $date_str . ' at ' . $time_str . $tz_label;

        // Multi-day: add end date as second line
        if ($end_ts && date_i18n('Ymd', $start_ts) !== date_i18n('Ymd', $end_ts)) {
            $lines[] = date_i18n('l, j F Y', $end_ts) . ' at ' . date_i18n('g:iA', $end_ts) . $tz_label;
        }

        return $lines;
    }

    // SVG Icons
    private function svg_calendar(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>';
    }

    private function svg_note(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="28" viewBox="0 0 22 28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M5.15 15.125H16.85"/>
            <path d="M5.15 12.2H16.85"/>
            <path d="M5.15 9.275H16.85"/>
            <path d="M3.8 19.4C3.305 19.4 2.88125 19.2237 2.52875 18.8712C2.17625 18.5187 2 18.095 2 17.6V6.8C2 6.305 2.17625 5.88125 2.52875 5.52875C2.88125 5.17625 3.305 5 3.8 5H18.2C18.695 5 19.1187 5.17625 19.4712 5.52875C19.8237 5.88125 20 6.305 20 6.8V23L16.4 19.4H3.8Z"/>
        </svg>';
    }

    private function svg_location(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 10C21 17 12 23 12 23S3 17 3 10A9 9 0 0 1 21 10Z"/>
            <circle cx="12" cy="10" r="3"/>
        </svg>';
    }

    // Card renderer (shared by grid and swiper)
    private function render_card(array $event, array $settings, string $img_size): void
    {
        $notice   = trim((string) get_field('follow_up_event', $event['id']));
        $footnote = trim((string) get_field('foot_note', $event['id']));
        $pricing  = get_field('event_ticket_pricing', $event['id']);
        $pricing  = is_array($pricing) ? array_values(array_filter($pricing)) : [];

        $has_meta = ! empty($event['duration_lines']) || ! empty($notice) || ! empty($event['location']);

        $btn_url      = ! empty($settings['button_url']['url']) ? $settings['button_url']['url'] : '#';
        $btn_target   = ! empty($settings['button_url']['is_external']) ? ' target="_blank"' : '';
        $btn_nofollow = ! empty($settings['button_url']['nofollow']) ? ' rel="nofollow"' : '';
        $btn_text     = ! empty($settings['button_text']) ? $settings['button_text'] : __('Sign Up', 'oup');
?>

        <div class="sce-card">

            <?php
            // Featured image — fixed 430×243 aspect ratio via CSS
            if (! empty($event['thumbnail_id'])) :
                $img = wp_get_attachment_image(
                    $event['thumbnail_id'],
                    $img_size,
                    false,
                    ['class' => 'sce-img']
                );
                if ($img) : ?>
                    <div class="sce-image"><?php echo $img; ?></div>
            <?php endif;
            endif; ?>

            <h3 class="sce-title"><?php echo esc_html($event['title']); ?></h3>

            <?php if (! empty($event['excerpt'])) : ?>
                <div class="sce-excerpt"><?php echo wp_kses_post(wpautop($event['excerpt'])); ?></div>
            <?php endif; ?>

            <?php if ($has_meta) : ?>
                <ul class="sce-meta">

                    <?php if (! empty($event['duration_lines'])) : ?>
                        <li class="sce-meta-item sce-meta-date">
                            <span class="sce-meta-icon"><?php echo $this->svg_calendar(); ?></span>
                            <div class="sce-meta-lines">
                                <?php foreach ($event['duration_lines'] as $line) : ?>
                                    <span class="sce-meta-line"><?php echo esc_html($line); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </li>
                    <?php endif; ?>

                    <?php if (! empty($notice)) : ?>
                        <li class="sce-meta-item sce-meta-notice">
                            <span class="sce-meta-icon"><?php echo $this->svg_note(); ?></span>
                            <span><?php echo wp_kses_post(nl2br(esc_html($notice))); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if (! empty($event['location'])) : ?>
                        <li class="sce-meta-item sce-meta-location">
                            <span class="sce-meta-icon"><?php echo $this->svg_location(); ?></span>
                            <span><?php echo esc_html($event['location']); ?></span>
                        </li>
                    <?php endif; ?>

                </ul>
            <?php endif; ?>

            <?php if (! empty($footnote)) : ?>
                <p class="sce-footnote"><?php echo wp_kses_post(nl2br(esc_html($footnote))); ?></p>
            <?php endif; ?>

            <?php // Spacer pushes pricing + button to the bottom of the card 
            ?>
            <div class="sce-spacer"></div>

            <?php if (! empty($pricing)) :
                $rows = array_chunk($pricing, 2); ?>
                <div class="sce-pricing-wrap">
                    <?php foreach ($rows as $row) : ?>
                        <div class="sce-pricing">
                            <?php foreach ($row as $i => $ticket) :
                                $ticket_name  = trim((string) ($ticket['ticket_name'] ?? ''));
                                $ticket_price = trim((string) ($ticket['price'] ?? ''));
                                if (! $ticket_name && ! $ticket_price) continue;
                            ?>
                                <?php if ($i > 0) : ?>
                                    <div class="sce-price-divider"></div>
                                <?php endif; ?>

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

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $is_editor      = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $posts_per_page = ! empty($settings['posts_per_page']) ? (int) $settings['posts_per_page'] : 6;
        $enable_slider  = ! empty($settings['enable_slider']) && $settings['enable_slider'] === 'yes';

        $events = $this->get_all_events($posts_per_page);

        if (empty($events)) {
            if ($is_editor) {
                echo '<div class="sce-placeholder"><p>'
                    . esc_html__('Sugar Calendar Event Cards: No upcoming events found. Make sure events are published and set to a future date.', 'oup')
                    . '</p></div>';
            }
            return;
        }

        $img_size = ! empty($settings['featured_image_size']) ? $settings['featured_image_size'] : 'large';

        // Grid layout
        if (! $enable_slider) {
            echo '<div class="sce-event-list">';
            foreach ($events as $event) {
                $this->render_card($event, $settings, $img_size);
            }
            echo '</div>';
            return;
        }

        // Swiper layout — navigation/pagination only shown when > 2 slides
        $total_events = count($events);
        $needs_scroll = $total_events > 2;
        $show_nav     = $needs_scroll && ! empty($settings['enable_navigation']) && $settings['enable_navigation'] === 'yes';
        $show_pag     = $needs_scroll && ! empty($settings['enable_pagination']) && $settings['enable_pagination'] === 'yes';

        $swiper_settings = [
            'slidesPerView' => 1,
            'spaceBetween'  => 24,
            'loop'          => $needs_scroll && ! empty($settings['enable_loop']) && $settings['enable_loop'] === 'yes',
            'navigation'    => $show_nav,
            'pagination'    => $show_pag,
            'breakpoints'   => [
                768  => ['slidesPerView' => 2, 'spaceBetween' => 36],
                1025 => ['slidesPerView' => 2, 'spaceBetween' => 50],
            ],
        ];

        if (! empty($settings['enable_autoplay']) && $settings['enable_autoplay'] === 'yes') {
            $swiper_settings['autoplay'] = [
                'delay'                => ! empty($settings['autoplay_delay']) ? (int) $settings['autoplay_delay'] : 3000,
                'disableOnInteraction' => false,
            ];
        }

        $unique_id = 'sce-swiper-' . $this->get_id();
    ?>

        <div class="sce-swiper-outer">
            <div class="swiper sce-swiper" id="<?php echo esc_attr($unique_id); ?>" data-swiper="<?php echo esc_attr(wp_json_encode($swiper_settings)); ?>">
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

    protected function content_template() {}
}