<?php
if (! defined('ABSPATH')) {
    exit;
}

// Search Hook
add_action('pre_get_posts', 'oup_exclude_search_post_types');
function oup_exclude_search_post_types(WP_Query $query)
{
    if (! $query->is_main_query() || is_admin() || ! $query->is_search()) {
        return;
    }

    $excluded_post_types = [
        'sfwd-topic',
        'sfwd-lessons',
        'elementor_library',
        'sugarcalendar_venue',
        'sc_speakers',
        'e-floating-buttons',   
        'attachment',
        'sfwd-quiz',
        'sfwd-certificates',
        'sfwd-transactions',
        'sfwd-essays',
        'sfwd-assignment',
        'ld-exam',
        'groups',
    ];

    $post_types = get_post_types(['public' => true]);
    $post_types = array_diff($post_types, $excluded_post_types);

    $query->set('post_type', $post_types);
}

add_action( 'plugins_loaded', 'oup_bypass_learndash_stripe_403', 1 );
function oup_bypass_learndash_stripe_403() {
    if ( isset( $_GET['ld_stripe_connect'] ) && $_GET['ld_stripe_connect'] === 'success' ) {
        unset( $_GET['ld_stripe_connect'] );

        unset( $_GET['session_id'] );
    }
}
//add class to success page
add_filter('body_class', 'oup_add_custom_class_to_success_page');
function oup_add_custom_class_to_success_page($classes) {
    if (is_page('registration-success')) {
        $classes[] = 'page-registration-success'; 
    }
    return $classes;
}

// Add Profile Link to LearnDash Focus Mode User Menu
add_filter( 'learndash_focus_header_user_dropdown_items', 'oup_add_profile_to_focus_menu', 10, 3 );
function oup_add_profile_to_focus_menu( $menu_items, $course_id, $user_id ) {
    $profile_item = array(
        'profile' => array(
            'url'   => site_url( '/profile/' ), 
            'label' => __( 'My Profile', 'learndash' ),
        )
    );
    if ( isset( $menu_items['logout'] ) ) {
        $logout = $menu_items['logout'];
        unset( $menu_items['logout'] );
        $menu_items['profile'] = $profile_item['profile'];
        $menu_items['logout']  = $logout;
    } else {
        $menu_items['profile'] = $profile_item['profile'];
    }

    return $menu_items;
}

// Change the LearnDash Course content heading
add_filter( 'gettext_with_context', function ( $translation, $text, $context, $domain ) {
    if (
        'learndash' === $domain &&
        '%s Content' === $text &&
        'placeholder: Course' === $context
    ) {
        return 'Overview';
    }
    return $translation;
}, 10, 4 );

// Redirect Worksheet Category Archive
add_action( 'template_redirect', 'redirect_worksheet_category_archive' );
function redirect_worksheet_category_archive() {
    if ( is_tax( 'worksheet-category' ) ) {
        wp_redirect( get_post_type_archive_link( 'worksheet' ), 301 );
        exit;
    }
}

// Custom Post Category Term Link
add_filter('term_link', 'custom_post_category_term_link', 10, 3);
function custom_post_category_term_link($url, $term, $taxonomy)
{
    if ('category' === $taxonomy || 'post_tag' === $taxonomy) {
        $archive_url = home_url('/blogs/');
        $url = $archive_url;
    }
    return $url;
}

// Redirect Post Category Archive
add_action('template_redirect', 'redirect_post_category_archive');
function redirect_post_category_archive()
{
    if (is_category() || is_tag()) {
        wp_redirect(home_url('/blogs/'), 301);
        exit;
    }
}

// Custom Worksheet Category Term Link
add_filter( 'term_link', 'custom_worksheet_category_term_link', 10, 3 );
function custom_worksheet_category_term_link( $url, $term, $taxonomy ) {
    if ( 'worksheet-category' === $taxonomy || 'worksheets-audience' === $taxonomy) {
        $archive_url = get_post_type_archive_link( 'worksheet' );
        
        if ( ! $archive_url ) {
            $archive_url = home_url( '/worksheet/' ); 
        }
        $url = $archive_url;
    }
    return $url;
}

// ── Pre-login URL: write current page into a cookie on every page except register
add_action( 'wp_head', 'oup_save_referrer_cookie' );
function oup_save_referrer_cookie() {
    // Don't overwrite the referrer when the user is already on the register page.
    // Adjust slugs if your register page uses a different URL.
    if ( is_page( array( 'register', 'sign-up', 'registration' ) ) ) {
        return;
    }
    ?>
    <script>
    (function () {
        try {
            var url = window.location.href;
            if ( url.indexOf( '/wp-admin' ) === -1 && url.indexOf( '/wp-login' ) === -1 ) {
                // Expires in 1 hour; readable by PHP on the same request after login
                document.cookie = 'oup_login_redirect=' + encodeURIComponent( url )
                    + '; path=/; SameSite=Lax; max-age=3600';
            }
        } catch (e) {}
    })();
    </script>
    <?php
}

// ── After WooCommerce login: redirect back to the stored URL (server-side, reliable)
add_filter( 'woocommerce_login_redirect', 'oup_woo_login_redirect', 20, 2 );
function oup_woo_login_redirect( $redirect, $user ) {
    if ( empty( $_COOKIE['oup_login_redirect'] ) ) {
        return $redirect;
    }

    $url = esc_url_raw( urldecode( $_COOKIE['oup_login_redirect'] ) );

    // Safety: only redirect within the same site
    if ( strpos( $url, home_url() ) !== 0 ) {
        return $redirect;
    }

    // Clear the cookie
    setcookie( 'oup_login_redirect', '', time() - 3600, '/', COOKIE_DOMAIN );

    return $url;
}
