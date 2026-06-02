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
    }

    // HELPERS
    private function get_site_timezone(): \DateTimeZone
    {
        $tz_string = get_option('timezone_string');

        if (! $tz_string) {
            $offset    = (float) get_option('gmt_offset', 0);
            $sign      = $offset >= 0 ? '+' : '-';
            $abs       = abs($offset);
            $hours     = (int) $abs;
            $minutes   = (int) round(($abs - $hours) * 60);
            $tz_string = sprintf('UTC%s%02d:%02d', $sign, $hours, $minutes);
        }

        try {
            return new \DateTimeZone($tz_string);
        } catch (\Exception $e) {
            return new \DateTimeZone('UTC');
        }
    }

    private function resolve_ts($value): int
    {
        if (empty($value)) {
            return 0;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        try {
            $dt = new \DateTime($value, new \DateTimeZone('UTC'));
            return $dt->getTimestamp();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function is_past_event(int $start_ts): bool
    {
        if (! $start_ts) {
            return false;
        }

        $tz        = $this->get_site_timezone();
        $today     = new \DateTime('today midnight', $tz);
        $event_day = new \DateTime('@' . $start_ts);
        $event_day->setTimezone($tz);
        $event_day->setTime(0, 0, 0);

        return $event_day < $today;
    }

    private function get_all_events(int $limit = 6, bool $ignore_past = false): array
    {
        $ids = get_posts([
            'post_type'      => 'sc_event',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'no_found_rows'  => true,
            'fields'         => 'ids',
        ]);

        if (empty($ids)) {
            return [];
        }

        $events = [];

        foreach ($ids as $event_id) {
            $data = $this->build_event_data($event_id);

            if (! $data) {
                continue;
            }

            if (! $ignore_past && $this->is_past_event($data['start_ts'])) {
                continue;
            }

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

        if (! $post || $post->post_status !== 'publish') {
            return null;
        }

        $data = [
            'id'             => $event_id,
            'title'          => get_the_title($event_id),
            'excerpt'        => get_the_excerpt($event_id),
            'thumbnail_id'   => get_post_thumbnail_id($event_id),
            'start'          => '',
            'end'            => '',
            'start_ts'       => 0,
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

            $data['location'] = (string) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT meta_value
                     FROM {$wpdb->prefix}sc_eventmeta
                     WHERE sc_event_id = %d
                     AND meta_key = %s
                     LIMIT 1",
                    $sc_event->id,
                    'location'
                )
            );
        }

        if (! $data['start_ts']) {
            return null;
        }

        $data['duration_lines'] = $this->format_duration(
            $data['start_ts'],
            $this->resolve_ts($data['end'])
        );

        return $data;
    }

    private function format_duration(int $start_ts, int $end_ts = 0): array
    {
        $lines = [];

        if (! $start_ts) {
            return $lines;
        }

        $tz_label = ' AEDT';

        $date_str = date_i18n('l, j F Y', $start_ts);
        $time_str = date_i18n('g:iA', $start_ts);

        if ($end_ts && date_i18n('Ymd', $start_ts) === date_i18n('Ymd', $end_ts)) {
            $time_str .= ' – ' . date_i18n('g:iA', $end_ts);
        }

        $lines[] = $date_str . ' at ' . $time_str . $tz_label;

        if ($end_ts && date_i18n('Ymd', $start_ts) !== date_i18n('Ymd', $end_ts)) {
            $lines[] = date_i18n('l, j F Y', $end_ts)
                . ' at ' . date_i18n('g:iA', $end_ts)
                . $tz_label;
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

    // Render
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $is_editor      = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $posts_per_page = ! empty($settings['posts_per_page']) ? (int) $settings['posts_per_page'] : 6;

        $events = $this->get_all_events($posts_per_page, $is_editor);

        if (empty($events)) {
            if ($is_editor) {
                echo '<div class="sce-placeholder"><p>'
                    . esc_html__('Sugar Calendar Event Cards: No upcoming events found. Make sure events are published and set to a future date.', 'oup')
                    . '</p></div>';
            }
            return;
        }

        $img_size = ! empty($settings['featured_image_size']) ? $settings['featured_image_size'] : 'large';

        echo '<div class="sce-event-list">';

        foreach ($events as $event) :

            $notice   = trim((string) get_field('follow_up_event', $event['id']));
            $footnote = trim((string) get_field('foot_note', $event['id']));
            $pricing  = get_field('event_ticket_pricing', $event['id']);
            $pricing  = is_array($pricing) ? array_values(array_filter($pricing)) : [];

            $past_badge = '';
            if ($is_editor && $this->is_past_event($event['start_ts'])) {
                $past_badge = '<div class="sce-past-badge">'
                    . esc_html__('⚠ This event is in the past — it will be hidden on the front-end.', 'oup')
                    . '</div>';
            }

            $has_meta = ! empty($event['duration_lines']) || ! empty($notice) || ! empty($event['location']);
?>

            <div class="sce-card">

                <?php echo $past_badge; ?>

                <?php
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

                <h2 class="sce-title"><?php echo esc_html($event['title']); ?></h2>

                <?php if (! empty($event['excerpt'])) : ?>
                    <div class="sce-excerpt"><?php echo wp_kses_post(wpautop($event['excerpt'])); ?></div>
                <?php endif; ?>

                <?php if ($has_meta) : ?>
                    <div class="sce-meta">

                        <?php if (! empty($event['duration_lines'])) : ?>
                            <div class="sce-meta-item sce-meta-date">
                                <span class="sce-meta-icon"><?php echo $this->svg_calendar(); ?></span>
                                <div class="sce-meta-lines">
                                    <?php foreach ($event['duration_lines'] as $line) : ?>
                                        <span class="sce-meta-line"><?php echo esc_html($line); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (! empty($notice)) : ?>
                            <div class="sce-meta-item sce-meta-notice">
                                <span class="sce-meta-icon"><?php echo $this->svg_note(); ?></span>
                                <span><?php echo wp_kses_post(nl2br(esc_html($notice))); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (! empty($event['location'])) : ?>
                            <div class="sce-meta-item sce-meta-location">
                                <span class="sce-meta-icon"><?php echo $this->svg_location(); ?></span>
                                <span><?php echo esc_html($event['location']); ?></span>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

                <?php if (! empty($pricing)) :
                    $rows = array_chunk($pricing, 2);
                    foreach ($rows as $row) : ?>
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
                <?php endforeach;
                endif; ?>

                <?php if (! empty($footnote)) : ?>
                    <p class="sce-footnote"><?php echo wp_kses_post(nl2br(esc_html($footnote))); ?></p>
                <?php endif; ?>

                <div class="sce-button-wrap">
                    <a href="#" class="sce-button">
                        <?php esc_html_e('Sign Up', 'oup'); ?>
                    </a>
                </div>

            </div>

<?php endforeach;

        echo '</div><!-- .sce-event-list -->';
    }

    protected function content_template() {}
}
