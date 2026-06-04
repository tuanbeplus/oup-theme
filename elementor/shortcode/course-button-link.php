<?php
/**
 * Create a custom variable to specify the login page path using your course ID.
 */
function get_custom_ld_register_link() {
    $course_id = get_the_ID();
    if (!$course_id) {
        return '#';
    }

    // 1. Check custom button URL first
    $course_meta = get_post_meta($course_id, '_sfwd-courses', true);
    if (is_array($course_meta) && !empty($course_meta['sfwd-courses_custom_button_url'])) {
        return esc_url($course_meta['sfwd-courses_custom_button_url']);
    }

    // 2. Try to get payment buttons HTML
    $course_price_type = function_exists('learndash_get_setting') ? learndash_get_setting($course_id, 'course_price_type') : '';
    if (function_exists('learndash_payment_buttons') && in_array($course_price_type, ['paynow', 'subscribe', 'free'])) {
        global $post;
        $original_post = $post ? clone $post : null;
        
        $registration_page_id = class_exists('LearnDash_Settings_Section') 
            ? (int) \LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Registration_Pages', 'registration')
            : 0;
            
        if ($registration_page_id) {
            $post = get_post($registration_page_id);
            if ($post) {
                setup_postdata($post);
            }
        }
        $course_price = '';
        if (function_exists('learndash_get_course_price')) {
            $price_arr = learndash_get_course_price($course_id);
            if (isset($price_arr['price']) && !empty($price_arr['price'])) {
                $course_price = $price_arr['price'];
            }
        }
        $price_text = !empty($course_price) ? " for $" . esc_html($course_price) : "";
        
        $button_label_filter = function($label) use ($price_text) {
            return 'Enrol Now' . $price_text;
        };
        add_filter('learndash_payment_button_label', $button_label_filter, 99);
        
        $payment_buttons = learndash_payment_buttons($course_id);
        
        remove_filter('learndash_payment_button_label', $button_label_filter, 99);
        
        if ($registration_page_id) {
            $post = $original_post;
            if ($post) {
                setup_postdata($post);
            } else {
                wp_reset_postdata();
            }
        }

        if (!empty($payment_buttons)) {
            // Check if it's a simple link (no form)
            if (preg_match('/href=["\']([^"\']+)["\']/', $payment_buttons, $matches) && strpos($payment_buttons, '<form') === false) {
                return esc_url($matches[1]);
            }
            
            // Otherwise, it is a payment gateway form. Store it for rendering in footer.
            if (!isset($GLOBALS['ld_checkout_forms'])) {
                $GLOBALS['ld_checkout_forms'] = [];
            }
            $GLOBALS['ld_checkout_forms'][$course_id] = $payment_buttons;
            
            return '#ld-checkout-' . $course_id;
        }
    }

    // Return the hash link to prevent right-click "Open in new tab" from navigating away
    return '#ld-checkout-' . $course_id;
}

// Hook to print hidden forms and JS triggers in footer
add_action('wp_footer', function() {
    $checkout_forms = isset($GLOBALS['ld_checkout_forms']) ? $GLOBALS['ld_checkout_forms'] : [];
    ?>
    <div class="ld-hidden-checkout-forms" style="display:none !important;" aria-hidden="true">
        <?php foreach ($checkout_forms as $id => $html) : ?>
            <div id="ld-checkout-form-<?php echo esc_attr($id); ?>"><?php echo $html; ?></div>
        <?php endforeach; ?>
    </div>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $(document).on('click', 'a[href^="#ld-checkout-"]', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            var courseId = href.replace('#ld-checkout-', '');
            if (!courseId) return;

            var $btn = $(this);
            var $btnText = $btn.find('.elementor-button-text');
            if ($btnText.length) {
                $btnText.text('Redirecting to Checkout...');
            } else {
                $btn.text('Redirecting to Checkout...');
            }
            $btn.css({
                opacity: 0.7,
                pointerEvents: 'none'
            });

            var $form = $('#ld-checkout-form-' + courseId + ' form');
            if ($form.length) {
                $form.submit();
            } else {
                window.location.href = '<?php echo esc_url(home_url('/registration/?ld_register_id=')); ?>' + courseId;
            }
        });
    });
    </script>
    <?php
});

add_shortcode('ld_register_link', 'get_custom_ld_register_link');


?>