<?php

/**
 * Theme functions and definitions
 *
 * @package Onwards-Upwards-Psychology-Theme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('OUP_THEME_VER', '1.0.0' . time());

/**
 * Enqueue styles and scripts
 */
if (!function_exists('enqueue_oup_styles_and_scripts')) {
    add_action('wp_enqueue_scripts', 'enqueue_oup_styles_and_scripts');
    function enqueue_oup_styles_and_scripts()
    {
        wp_enqueue_style('oup-main-style', get_stylesheet_directory_uri() . '/assets/css/main.css', array(), OUP_THEME_VER);
        wp_enqueue_script('oup-main-script', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), OUP_THEME_VER, true);
        wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', array(), null, false);
        wp_localize_script('oup-main-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    }
}
/**
 * Turn off the default LearnDash interface on the course detail page.
 */
add_filter('learndash_template_preprocess_filter', function($run, $post_id) {
    if ( is_singular('sfwd-courses') ) {
        return false;
    }
    return $run;
}, 10, 2);

/**
* Get the price of the current LearnDash course.
 */
function get_learndash_course_price() {
    $course_id = get_the_ID();
    $course_settings = get_post_meta($course_id, '_sfwd-courses', true);
    
    if (isset($course_settings['sfwd-courses_course_price']) && !empty($course_settings['sfwd-courses_course_price'])) {
        return $course_settings['sfwd-courses_course_price'];
    } else {
        return 'Free';
    }
}

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
// Shortcode for Course Accordion to show all lessons, topics, and quizzes
if (!function_exists('oup_course_accordion_shortcode_callback')) {
    function oup_course_accordion_shortcode_callback($atts) {
        $atts = shortcode_atts(array(
            'course_id'     => 0,
            'default_state' => 'first_expanded',
            'max_items'     => 'multiple',
            'anim_duration' => 400
        ), $atts, 'oup_course_accordion');

        $course_id = intval($atts['course_id']);
        if (!$course_id) {
            $course_id = get_the_ID();
        }

        if (!$course_id || get_post_type($course_id) !== 'sfwd-courses' || !function_exists('learndash_get_course_lessons_list')) {
            return '';
        }


        $lessons_list = \learndash_get_course_lessons_list($course_id, null, ['nopaging' => true]);
        if (!empty($lessons_list) && is_array($lessons_list)) {
            foreach ($lessons_list as $key => $lesson) {
                $lesson_id = isset($lesson['id']) ? $lesson['id'] : $lesson['post']->ID;
                $lessons_list[$key]['topics']  = function_exists('learndash_get_topic_list') ? \learndash_get_topic_list($lesson_id, $course_id) : [];
                $lessons_list[$key]['quizzes'] = function_exists('learndash_get_lesson_quiz_list') ? \learndash_get_lesson_quiz_list($lesson_id, null, $course_id) : [];
            }
        }

        if (empty($lessons_list)) {
            return '';
        }

        ob_start();
        get_template_part('elementor/shortcode/course-accordion', null, [
            'lessons_list'  => $lessons_list,
            'default_state' => $atts['default_state'],
            'max_items'     => $atts['max_items'],
            'anim_duration' => intval($atts['anim_duration'])
        ]);
        return ob_get_clean();
    }
}
add_shortcode('ld_register_link', 'get_custom_ld_register_link');
add_shortcode('ld_course_price', 'get_learndash_course_price');
add_shortcode('oup_course_accordion', 'oup_course_accordion_shortcode_callback');

/**
 * Shortcode: [oup_author_role]
 * @return string
 */
if (!function_exists('oup_author_role_shortcode')) {
    function oup_author_role_shortcode($atts)
    {
        $atts      = shortcode_atts(['post_id' => 0], $atts, 'oup_author_role');
        $post_id   = $atts['post_id'] ? (int) $atts['post_id'] : get_the_ID();
        $author_id = (int) get_post_field('post_author', $post_id);

        if (!$author_id) {
            return '';
        }

        $user  = get_userdata($author_id);
        $roles = $user->roles ?? [];

        if (empty($roles)) {
            return '';
        }

        return esc_html(ucfirst(reset($roles)));
    }
    add_shortcode('oup_author_role', 'oup_author_role_shortcode');
}

/* Widgets Load */
require_once get_stylesheet_directory() . '/elementor/widgets-load.php';

/* WooCommerce */
require_once get_stylesheet_directory() . '/inc/woo.php';

/* AJAX Handlers */
require_once get_stylesheet_directory() . '/elementor/widgets/archive-posts-filter/ajax.php';
require_once get_stylesheet_directory() . '/elementor/widgets/worksheet-filter/ajax.php';
require_once get_stylesheet_directory() . '/elementor/widgets/course-filter/ajax.php';


