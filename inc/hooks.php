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

// Remove Private: Forum
add_filter('private_title_format', function($format, $post) {
    if (in_array($post->post_type, ['forum'])) {
        return '%s';
    }
    return $format;
}, 10, 2);

// Redirect bbPress Forum Archive to 404 
add_filter( 'template_include', 'oup_redirect_forum_archive_to_404', 999 );
function oup_redirect_forum_archive_to_404( $template ) {
    if ( is_post_type_archive( 'forum' ) || ( function_exists( 'bbp_is_forum_archive' ) && bbp_is_forum_archive() ) ) {
        global $wp_query;
        $wp_query->set_404();
        status_header( 404 );
        nocache_headers();
        return get_query_template( '404' ) ?: get_index_template();
    }
    return $template;
}


add_filter( 'get_the_excerpt', 'oup_force_bbpress_content_in_excerpt', 1, 2 );
function oup_force_bbpress_content_in_excerpt( $excerpt, $post ) {
    if ( function_exists( 'is_bbpress' ) && is_bbpress() ) {
        return apply_filters( 'the_content', $post->post_content );
    }
    return $excerpt;
}


add_filter('get_the_archive_title', function($title) {
    if (is_tax('topic-tag')) {
        $title = single_term_title('', false);
    } elseif (function_exists('bbp_is_search') && bbp_is_search()) {
        $title = 'Search Forums';
    }
    return $title;
});


// Course to Forum Mapping
function oup_get_course_forum_mapping() {
    return [
        // Forum NeuroNurturers
        4457 => 5261, 
        2873 => 5261,
        
        // Forum NeuroNexus Network
        2847 => 5710,
        2848 => 5710,
        2849 => 5710,
    ];
}

/**
 * Grant forum access keys to a specific user
 */
function oup_grant_user_forum_access( $user_id, $forum_id ) {
    // Grant access key in user meta
    $allowed_forums = get_user_meta( $user_id, 'oup_allowed_forums', true );
    if ( ! is_array( $allowed_forums ) ) {
        $allowed_forums = [];
    }
    if ( ! in_array( $forum_id, $allowed_forums ) ) {
        $allowed_forums[] = $forum_id;
        update_user_meta( $user_id, 'oup_allowed_forums', $allowed_forums );
    }

    // Auto-subscribe the user to the forum for group email notifications
    if ( function_exists('bbp_add_user_subscription') && function_exists('bbp_is_user_subscribed') ) {
        // Duplicate check: Only add if not already subscribed
        if ( ! bbp_is_user_subscribed( $user_id, $forum_id ) ) {
            bbp_add_user_subscription( $user_id, $forum_id );
        }
    }
}

/**
 * Sync existing course users
 */
function oup_sync_existing_course_users_to_forums() {
    if ( get_option('oup_forums_synced') ) return; 

    $mapping = oup_get_course_forum_mapping();

    foreach ( $mapping as $course_id => $forum_id ) {
        // Get all users who have access to this course
        $course_users = get_users([
            'meta_query' => [
                [
                    'key'     => 'course_' . $course_id . '_access_from',
                    'compare' => 'EXISTS'
                ]
            ],
            'fields' => 'ID'
        ]);
        
        if ( !empty($course_users) ) {
            foreach ( $course_users as $user_id ) {
                oup_grant_user_forum_access( $user_id, $forum_id );
            }
        }
    }
    update_option('oup_forums_synced', true); 
}
add_action('init', 'oup_sync_existing_course_users_to_forums');

/**
 *  forum access when a new user purchases a course
 */
add_action( 'learndash_update_course_access', 'oup_auto_grant_forum_access_on_purchase', 10, 4 );
function oup_auto_grant_forum_access_on_purchase( $user_id, $course_id, $course_access_list, $remove ) {
    if ( $remove ) return; 

    $mapping = oup_get_course_forum_mapping();
    if ( isset( $mapping[ $course_id ] ) ) {
        $forum_id = $mapping[ $course_id ];
        oup_grant_user_forum_access( $user_id, $forum_id );
    }
}

/**
 * Bypass Private Forum restriction for authorized users
 */
add_filter( 'map_meta_cap', 'oup_allow_read_private_forum', 10, 4 );
function oup_allow_read_private_forum( $caps, $cap, $user_id, $args ) {
    if ( $cap === 'read_forum' && !empty($args[0]) ) {
        $forum_id = $args[0];
        
        $allowed_forums = get_user_meta( $user_id, 'oup_allowed_forums', true );
        
        if ( is_array($allowed_forums) && in_array( $forum_id, $allowed_forums ) ) {
            $caps = array( 'read' ); 
        }
    }
    return $caps;
}

// Add meta box to show number of group members
add_action( 'add_meta_boxes', 'oup_forum_stats_metabox' );
function oup_forum_stats_metabox() {
    add_meta_box(
        'oup_forum_stats',
        '👥 Group Members Count',
        'oup_forum_stats_metabox_content',
        'forum',
        'side',
        'high'
    );
}

function oup_forum_stats_metabox_content( $post ) {
    if ( function_exists('bbp_get_forum_subscribers') ) {
        $subs = bbp_get_forum_subscribers($post->ID);
        $count = is_array($subs) ? count($subs) : 0;
        echo '<div style="font-size: 15px; padding: 10px 0; text-align: center; border: 1px solid #c3c4c7; background: #f0f0f1; border-radius: 4px;">';
        echo '<strong>Total Members:</strong><br/>';
        echo '<span style="color: #2271b1; font-size: 24px; font-weight: bold; line-height: 1.5;">' . $count . '</span>';
        echo '</div>';
    }
}
