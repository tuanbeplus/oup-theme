<?php
function get_learndash_course_price() {
    $course_id = get_the_ID();
    if ( ! $course_id ) return '';

    // Get enrollment mode from LearnDash
    $price_type = function_exists( 'learndash_get_setting' )
        ? learndash_get_setting( $course_id, 'course_price_type' )
        : '';

    // Get raw price value
    $raw_price = function_exists( 'learndash_get_setting' )
        ? learndash_get_setting( $course_id, 'course_price' )
        : '';

    // Fallback to meta if API not available
    if ( empty( $raw_price ) ) {
        $course_meta = get_post_meta( $course_id, '_sfwd-courses', true );
        $raw_price   = is_array( $course_meta ) && ! empty( $course_meta['sfwd-courses_course_price'] )
            ? $course_meta['sfwd-courses_course_price']
            : '';
        if ( empty( $price_type ) ) {
            $price_type = is_array( $course_meta ) && ! empty( $course_meta['sfwd-courses_course_price_type'] )
                ? $course_meta['sfwd-courses_course_price_type']
                : '';
        }
    }

    // Read currency from LearnDash payment settings first, fall back to WooCommerce
    $active_currency = '';
    $ld_defaults = get_option( 'learndash_settings_payments_defaults', [] );
    if ( ! empty( $ld_defaults['currency'] ) ) {
        $active_currency = $ld_defaults['currency'];
    }
    $currency_prefix = '';  
    if ( ! empty( $active_currency ) ) {
        if ( function_exists( 'learndash_get_currency_symbol' ) ) {
            $currency_prefix = learndash_get_currency_symbol( $active_currency );
        }elseif (function_exists('get_woocommerce_currency_symbol')) {
            $currency_prefix = get_woocommerce_currency_symbol( $active_currency );
        }
    }

    // Format price string
    $format_price = function( $price ) use ( $currency_prefix ) {
        $price = is_numeric( $price ) ? number_format( (float) $price, 0 ) : $price;
        return $currency_prefix ? $currency_prefix . $price : $price;
    };

    switch ( $price_type ) {
        case 'open':
            // No registration required — openly accessible
            return 'Free';

        case 'free':
            // Registration required, no payment
            return 'Free';

        case 'paynow':
            // One-time payment
            return ! empty( $raw_price ) ? $format_price( $raw_price ) : 'Paid';

        case 'subscribe':
            // Recurring subscription
            $billing_cycle = function_exists( 'learndash_get_setting' )
                ? learndash_get_setting( $course_id, 'course_price_billing_p3' )
                : '';
            $billing_unit  = function_exists( 'learndash_get_setting' )
                ? learndash_get_setting( $course_id, 'course_price_billing_t3' )
                : '';
            $price_str = ! empty( $raw_price ) ? $format_price( $raw_price ) : 'Paid';
            if ( ! empty( $billing_cycle ) && ! empty( $billing_unit ) ) {
                $price_str .= ' / ' . $billing_cycle . ' ' . $billing_unit;
            }
            return $price_str;

        case 'closed':
            // Closed — only accessible via admin enroll or integration
            return ! empty( $raw_price ) ? $format_price( $raw_price ) : 'Closed';

        default:
            // Unknown or not set — fallback
            return ! empty( $raw_price ) ? $format_price( $raw_price ) : 'Free';
    }
}
add_shortcode('ld_course_price', 'get_learndash_course_price');
?>