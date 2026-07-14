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

// ── Pre-login URL: write current page into a cookie (logged-out users only, not register page)
add_action( 'wp_head', 'oup_save_referrer_cookie' );
function oup_save_referrer_cookie() {
    // Only save for guests — once the user is logged in we stop overwriting so
    // the redirect cookie survives until it is consumed.
    if ( is_user_logged_in() ) {
        return;
    }
    // Don't overwrite the referrer when already on the register/login page.
    // Adjust slugs if your pages use different URLs.
    if ( is_page( array( 'register', 'sign-up', 'registration' ) ) ) {
        return;
    }
    ?>
    <script>
    (function () {
        try {
            var url = window.location.href;
            if ( url.indexOf( '/wp-admin' ) === -1 && url.indexOf( '/wp-login' ) === -1 ) {
                // 1-hour expiry; survives the post-login redirect
                document.cookie = 'oup_login_redirect=' + encodeURIComponent( url )
                    + '; path=/; SameSite=Lax; max-age=3600';
            }
        } catch (e) {}
    })();
    </script>
    <?php
}

// ── After any login (LearnDash, WooCommerce, native WP): redirect to stored URL
// Works client-side so it is compatible with AJAX-based login forms (LearnDash).
add_action( 'wp_head', 'oup_cookie_redirect_after_login' );
function oup_cookie_redirect_after_login() {
    if ( ! is_user_logged_in() ) {
        return;
    }
    ?>
    <script>
    (function () {
        try {
            // Read the redirect cookie
            function getCookie( name ) {
                var match = document.cookie.match( '(?:^|;)\\s*' + name + '=([^;]*)' );
                return match ? decodeURIComponent( match[1] ) : null;
            }

            var redirect = getCookie( 'oup_login_redirect' );
            if ( ! redirect ) { return; }

            // Immediately clear the cookie so it is not consumed again
            document.cookie = 'oup_login_redirect=; path=/; max-age=0; SameSite=Lax';

            // Safety: only redirect to the same origin
            var a = document.createElement( 'a' );
            a.href = redirect;
            if ( a.hostname === window.location.hostname ) {
                window.location.replace( redirect );
            }
        } catch (e) {}
    })();
    </script>
    <?php
}

/**
 * Add Enrolled Users column to LearnDash Courses admin list.
 */
add_filter( 'manage_sfwd-courses_posts_columns', function ( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['ld_course_image'] = 'Image';
			$new['ld_enrolled_users'] = 'Enrolled Users';
		}
	}
	return $new;
} );
add_action( 'manage_sfwd-courses_posts_custom_column', function ( $column, $post_id ) {
	if ( 'ld_course_image' === $column ) {
		if ( has_post_thumbnail( $post_id ) ) {
			echo get_the_post_thumbnail( $post_id, 'medium', array( 'style' => 'width: 100px; height: auto; border-radius: 8px; border: 1px solid #eee;' ) );
		} else {
			echo '-';
		}
	} elseif ( 'ld_enrolled_users' === $column ) {
		$user_ids = learndash_get_course_users_access_from_meta( $post_id );
		echo is_array( $user_ids ) ? count( $user_ids ) : 0;
	}
}, 10, 2 );
