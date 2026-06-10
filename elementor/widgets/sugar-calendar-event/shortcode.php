<?php
if (! defined('ABSPATH')) exit;

// OUP_Event_Meta_Shortcode
if (! class_exists('OUP_Event_Meta_Shortcode')) :

    class OUP_Event_Meta_Shortcode
    {
        public static function register(): void
        {
            add_shortcode('oup_event_meta', [self::class, 'render']);
        }

        public static function render(array $atts): string
        {
            $atts     = shortcode_atts(['event_id' => 0], $atts, 'oup_event_meta');
            $event_id = (int) $atts['event_id'] ?: (int) get_the_ID();

            if (! $event_id) return '';

            $post = get_post($event_id);
            if (! $post || $post->post_status !== 'publish') return '';

            $event = self::build_event($event_id);
            if (! $event) return '';

            ob_start();
            self::render_meta_block($event);
            return ob_get_clean();
        }

        private static function build_event(int $event_id): ?array
        {
            $rows = self::get_sc_event_rows($event_id);
            if (empty($rows)) return null;

            $row          = $rows[0];
            $start_ts     = self::resolve_ts($row->start);
            $end_ts       = self::resolve_ts($row->end);
            $recurrence   = trim((string) ($row->recurrence ?? ''));
            $is_recurring = $recurrence !== '' && $recurrence !== 'never';

            $event = [
                'id'             => $event_id,
                'start_ts'       => $start_ts,
                'end_ts'         => $end_ts,
                'location'       => self::get_venue_string((int) $row->id, $event_id),
                'duration_lines' => [],
                'occurrences'    => [],
                'is_recurring'   => $is_recurring,
            ];

            if ($is_recurring) {
                $members = [];
                foreach ($rows as $r) {
                    $s = self::resolve_ts($r->start);
                    $e = self::resolve_ts($r->end);
                    if (! $s) continue;
                    $members[] = [
                        'start_ts' => $s,
                        'end_ts'   => $e,
                        'lines'    => self::format_duration($s, $e),
                    ];
                }
                usort($members, fn($a, $b) => $a['start_ts'] <=> $b['start_ts']);

                $event['occurrences'] = self::calculate_occurrences(
                    $members,
                    $recurrence,
                    max(1, (int) ($row->recurrence_interval ?? 1)),
                    (string) ($row->recurrence_end ?? ''),
                    (int) ($row->recurrence_count ?? 0)
                );
            } else {
                $event['duration_lines'] = self::format_duration($start_ts, $end_ts);
            }

            return $event;
        }

        private static function render_meta_block(array $event): void
        {
            $notice   = trim((string) get_field('follow_up_event', $event['id']));
            $location = $event['location'];
            $has_date = $event['is_recurring'] ? ! empty($event['occurrences']) : ! empty($event['duration_lines']);

            if (! $has_date && ! $notice && ! $location) return;
?>
            <ul class="sce-meta">
                <?php self::render_date_items($event); ?>
                <?php if ($notice) : ?>
                    <li class="sce-meta-item sce-meta-notice">
                        <span class="sce-meta-icon"><?php echo self::svg_note(); ?></span>
                        <span><?php echo wp_kses_post(nl2br(esc_html($notice))); ?></span>
                    </li>
                <?php endif; ?>

                <?php if ($location) : ?>
                    <li class="sce-meta-item sce-meta-location">
                        <span class="sce-meta-icon"><?php echo self::svg_location(); ?></span>
                        <span><?php echo esc_html($location); ?></span>
                    </li>
                <?php endif; ?>
            </ul>
        <?php
        }

        private static function render_date_items(array $event): void
        {
            if ($event['is_recurring'] && ! empty($event['occurrences'])) {
                foreach ($event['occurrences'] as $occurrence) {
                    foreach ($occurrence['lines'] as $line) {
                        self::render_date_item($line);
                    }
                }
                return;
            }

            if (empty($event['duration_lines'])) return;

            echo '<li class="sce-meta-item sce-meta-date">';
            echo '<span class="sce-meta-icon">' . self::svg_calendar() . '</span>';
            echo '<div class="sce-meta-lines">';
            foreach ($event['duration_lines'] as $line) {
                echo '<span class="sce-meta-line">' . esc_html($line) . '</span>';
            }
            echo '</div></li>';
        }

        private static function render_date_item(string $line): void
        {
            echo '<li class="sce-meta-item sce-meta-date">';
            echo '<span class="sce-meta-icon">' . self::svg_calendar() . '</span>';
            echo '<div class="sce-meta-lines"><span class="sce-meta-line">' . esc_html($line) . '</span></div>';
            echo '</li>';
        }

        private static function get_sc_event_rows(int $post_id): array
        {
            global $wpdb;
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}sc_events WHERE object_id = %d ORDER BY start ASC",
                $post_id
            )) ?: [];
        }

        private static function resolve_ts(mixed $value): int
        {
            if (empty($value)) return 0;
            if (is_numeric($value)) return (int) $value;
            try {
                return (new \DateTime($value, new \DateTimeZone('UTC')))->getTimestamp();
            } catch (\Exception) {
                return 0;
            }
        }

        private static function get_venue_string(int $sc_event_row_id, int $post_id): string
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

        private static function build_occurrence(int $ts, int $duration): array
        {
            $end_ts = $ts + $duration;
            return [
                'start_ts' => $ts,
                'end_ts'   => $end_ts,
                'lines'    => self::format_duration($ts, $end_ts),
            ];
        }

        private static function calculate_occurrences(
            array  $members,
            string $rule,
            int    $interval            = 1,
            string $recurrence_end      = '',
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
                    $occurrences[] = self::build_occurrence($ts, $duration);
                    $ts += self::get_step_seconds($rule, $interval, $ts);
                }
                return $occurrences;
            }

            // Case 2: Until date
            $end_boundary = (! empty($recurrence_end) && $recurrence_end !== '0000-00-00 00:00:00')
                ? self::resolve_ts($recurrence_end)
                : 0;

            if ($end_boundary > 0) {
                $occurrences = [];
                $ts          = $first_start;
                while ($ts <= $end_boundary) {
                    $occurrences[] = self::build_occurrence($ts, $duration);
                    $ts += self::get_step_seconds($rule, $interval, $ts);
                }
                return $occurrences;
            }

            // Case 3: Fallback — post count
            $occurrences = [];
            $ts          = $first_start;
            foreach ($members as $_) {
                $occurrences[] = self::build_occurrence($ts, $duration);
                $ts += self::get_step_seconds($rule, $interval, $ts);
            }
            return $occurrences;
        }

        private static function get_step_seconds(string $rule, int $interval, int $from_ts): int
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

        private static function format_duration(int $start_ts, int $end_ts = 0): array
        {
            if (! $start_ts) return [];

            $tz       = wp_timezone();
            $tz_label = ' ' . (new \DateTime('@' . $start_ts))->setTimezone($tz)->format('T');
            $time_str = date_i18n('g:iA', $start_ts);
            $same_day = $end_ts && date_i18n('Ymd', $start_ts) === date_i18n('Ymd', $end_ts);

            if ($same_day) $time_str .= ' – ' . date_i18n('g:iA', $end_ts);

            $lines = [date_i18n('l, j F Y', $start_ts) . ' at ' . $time_str . $tz_label];

            if ($end_ts && ! $same_day) {
                $lines[] = date_i18n('l, j F Y', $end_ts) . ' at ' . date_i18n('g:iA', $end_ts) . $tz_label;
            }

            return $lines;
        }

        // SVG Icons 
        private static function svg_calendar(): string
        {
            return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
			<line x1="8" y1="2" x2="8" y2="6"/>
			<line x1="16" y1="2" x2="16" y2="6"/>
			<line x1="3" y1="10" x2="21" y2="10"/>
		</svg>';
        }

        private static function svg_note(): string
        {
            return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="28" viewBox="0 0 22 28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<path d="M5.15 15.125H16.85"/>
			<path d="M5.15 12.2H16.85"/>
			<path d="M5.15 9.275H16.85"/>
			<path d="M3.8 19.4C3.305 19.4 2.88125 19.2237 2.52875 18.8712C2.17625 18.5187 2 18.095 2 17.6V6.8C2 6.305 2.17625 5.88125 2.52875 5.52875C2.88125 5.17625 3.305 5 3.8 5H18.2C18.695 5 19.1187 5.17625 19.4712 5.52875C19.8237 5.88125 20 6.305 20 6.8V23L16.4 19.4H3.8Z"/>
		</svg>';
        }

        private static function svg_location(): string
        {
            return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<path d="M21 10C21 17 12 23 12 23S3 17 3 10A9 9 0 0 1 21 10Z"/>
			<circle cx="12" cy="10" r="3"/>
		</svg>';
        }
    }

    OUP_Event_Meta_Shortcode::register();

endif;

// OUP_Event_Pricing_Shortcode
if (! class_exists('OUP_Event_Pricing_Shortcode')) :

    class OUP_Event_Pricing_Shortcode
    {
        public static function register(): void
        {
            add_shortcode('oup_event_pricing', [self::class, 'render']);
        }

        public static function render(array $atts): string
        {
            $atts     = shortcode_atts(['event_id' => 0], $atts, 'oup_event_pricing');
            $event_id = (int) $atts['event_id'] ?: (int) get_the_ID();

            if (! $event_id) return '';

            $post = get_post($event_id);
            if (! $post || $post->post_status !== 'publish') return '';

            $pricing = get_field('event_ticket_pricing', $event_id);
            $pricing = is_array($pricing) ? array_values(array_filter($pricing)) : [];

            if (empty($pricing)) return '';

            ob_start();
            self::render_pricing_block($pricing);
            return ob_get_clean();
        }

        private static function render_pricing_block(array $pricing): void
        {
        ?>
            <div class="sce-pricing-wrap">
                <?php foreach ($pricing as $ticket) :
                    $name  = trim((string) ($ticket['ticket_name'] ?? ''));
                    $price = trim((string) ($ticket['price'] ?? ''));
                    if (! $name && ! $price) continue;
                ?>
                    <div class="sce-price-item">
                        <div class="sce-price-amount-wrap">
                            <span class="sce-price-amount">$<?php echo esc_html($price); ?></span>
                            <span class="sce-price-suffix"><?php esc_html_e('inc. GST', 'oup'); ?></span>
                        </div>
                        <span class="sce-price-label"><?php echo esc_html($name); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
<?php
        }
    }

    OUP_Event_Pricing_Shortcode::register();

endif;