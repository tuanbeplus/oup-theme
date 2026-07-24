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

// Add meta box to show number of group members
add_action( 'add_meta_boxes', 'oup_forum_stats_metabox' );
function oup_forum_stats_metabox() {
    add_meta_box(
        'oup_forum_stats',
        '👥 Group Members',
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


function oup_manual_sync_users_to_forums() {
    if ( ! function_exists('learndash_get_course_users_access_from_meta') ) return;

    $course_4457 = learndash_get_course_users_access_from_meta(4457);
    if (!is_array($course_4457)) $course_4457 = [];
    $course_2873 = learndash_get_course_users_access_from_meta(2873);
    if (!is_array($course_2873)) $course_2873 = [];
    $group_1 = array_unique(array_merge($course_4457, $course_2873));

    foreach ($group_1 as $user_id) {
        oup_grant_user_forum_access( $user_id, 5261 );
    }
    
    $course_2847 = learndash_get_course_users_access_from_meta(2847);
    if (!is_array($course_2847)) $course_2847 = [];
    $course_2848 = learndash_get_course_users_access_from_meta(2848);
    if (!is_array($course_2848)) $course_2848 = [];
    $course_2849 = learndash_get_course_users_access_from_meta(2849);
    if (!is_array($course_2849)) $course_2849 = [];
    $group_2 = array_unique(array_merge($course_2847, $course_2848, $course_2849));

    foreach ($group_2 as $user_id) {
        oup_grant_user_forum_access( $user_id, 5710 );
    }
}

function oup_grant_user_forum_access( $user_id, $forum_id ) {
    $allowed_forums = get_user_meta( $user_id, 'oup_allowed_forums', true );
    if ( ! is_array( $allowed_forums ) ) $allowed_forums = [];
    
    if ( ! in_array( $forum_id, $allowed_forums ) ) {
        $allowed_forums[] = $forum_id;
        update_user_meta( $user_id, 'oup_allowed_forums', $allowed_forums );
    }

    if ( function_exists('bbp_add_user_subscription') && function_exists('bbp_is_user_subscribed') ) {
        if ( ! bbp_is_user_subscribed( $user_id, $forum_id ) ) {
            bbp_add_user_subscription( $user_id, $forum_id );
        }
    }
}

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

/**
 * LearnDash: Show per-section step counts for non-enrolled users.
 *
 * On the single course page, if the visitor does NOT have access, keep
 * each section subheading (Welcome!, Understanding ADHD, …) but replace
 * the individual lesson items underneath with a step count, e.g.:
 *
 *   Welcome!          → 1 Step
 *   Understanding ADHD → 10 Steps
 *   Pillars of ADHD   → 6 Steps
 *
 * How it works:
 *  1. `preg_split` the content by `ld-accordion__subheading` spans.
 *  2. Count `ld-accordion__item--lesson` occurrences in each section.
 *  3. Inject a step-count element right after each subheading.
 *  4. Hide the individual lesson items with CSS (`.oup-course-no-access`).
 */
add_filter( 'learndash_content', 'oup_course_steps_for_visitors', 20, 2 );
function oup_course_steps_for_visitors( $content, $post ) {
    // Only target course single pages.
    if ( ! $post || 'sfwd-courses' !== get_post_type( $post ) ) {
        return $content;
    }

    if ( ! is_singular( 'sfwd-courses' ) ) {
        return $content;
    }

    $course_id = $post->ID;
    $user_id   = get_current_user_id();

    // Enrolled users see the full content.
    if ( $user_id && function_exists( 'sfwd_lms_has_access' ) && sfwd_lms_has_access( $course_id, $user_id ) ) {
        return $content;
    }

    // ── Split content by subheading spans ────────────────────────────────
    // Each subheading is a <span … class="…ld-accordion__subheading…">…</span>.
    $split_pattern = '/(<span[^>]*class="[^"]*\bld-accordion__subheading\b[^"]*"[^>]*>.*?<\/span>)/s';
    $parts         = preg_split( $split_pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE );

    // If no subheadings were found, nothing to transform.
    if ( count( $parts ) <= 1 ) {
        return $content;
    }

    // ── Rebuild the content ─────────────────────────────────────────────
    // $parts layout:
    //   [0] everything before the first subheading
    //   [1] first subheading span
    //   [2] section content (lesson items + whitespace) until next subheading
    //   [3] second subheading span
    //   [4] section content …
    //   …   last section content also includes all remaining closing tags,
    //        scripts, sidebar, etc.

    $new_content = $parts[0]; // keep everything before first subheading

    for ( $i = 1; $i < count( $parts ); $i += 2 ) {
        $subheading_html = $parts[ $i ];
        $section_html    = isset( $parts[ $i + 1 ] ) ? $parts[ $i + 1 ] : '';

        // Count lesson items in this section.
        $item_count = preg_match_all(
            '/class="[^"]*\bld-accordion__item\s+ld-accordion__item--lesson\b/',
            $section_html
        );

        // Build the step-count element.
        $step_label = sprintf(
            _n( '%d Step', '%d Steps', $item_count, 'oup-theme' ),
            $item_count
        );

        $step_html = '<div class="oup-course-section-steps">'
            . '<svg class="oup-course-section-steps__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor">'
            . '<path fill-rule="evenodd" clip-rule="evenodd" d="M3.81815 4C3.3663 4 3 4.35258 3 4.7875V16.6C3 17.0349 3.3663 17.3875 3.81815 17.3875H9.54516C9.97914 17.3875 10.3953 17.5534 10.7022 17.8488C11.0091 18.1442 11.1815 18.5448 11.1815 18.9625C11.1815 19.3974 11.5478 19.75 11.9996 19.75C12.4514 19.75 12.8185 19.3974 12.8185 18.9625C12.8185 18.5448 12.9909 18.1442 13.2978 17.8488C13.6047 17.5534 14.0209 17.3875 14.4548 17.3875H20.1819C20.6337 17.3875 21 17.0349 21 16.6V4.7875C21 4.35258 20.6337 4 20.1819 4H15.273C14.1881 4 13.1476 4.41484 12.3804 5.15327C12.2426 5.28594 12.1156 5.4271 12 5.57549C11.8844 5.4271 11.7574 5.28594 11.6196 5.15327C10.8524 4.41484 9.81195 4 8.72702 4H3.81815ZM11.1815 7.9375V16.2345C10.6882 15.9604 10.1246 15.8125 9.54516 15.8125H4.63629V5.575H8.72702C9.37798 5.575 10.0023 5.82391 10.4626 6.26696C10.9229 6.71001 11.1815 7.31093 11.1815 7.9375ZM14.4548 15.8125C13.8754 15.8125 13.3118 15.9604 12.8185 16.2345V7.9375C12.8185 7.31093 13.0771 6.71001 13.5374 6.26696C13.9977 5.82391 14.622 5.575 15.273 5.575H19.3637V15.8125H14.4548Z"/>'
            . '</svg>'
            . '<span class="oup-course-section-steps__text">'
            . esc_html( $step_label )
            . '</span>'
            . '</div>';

        // Subheading → step count → original section HTML (items hidden via CSS).
        $new_content .= $subheading_html . "\n" . $step_html . $section_html;
    }

    // ── Add marker class so CSS/JS can target the lesson items ──────────
    $new_content = str_replace(
        'ld-accordion ld-accordion--course',
        'ld-accordion ld-accordion--course oup-course-no-access',
        $new_content
    );

    // ── Remove lesson items securely from HTML (for QA) ─────────────────
    // We use DOMDocument to safely strip the lesson items from the HTML source
    // so they are not sent to the browser at all.
    if ( class_exists( 'DOMDocument' ) ) {
        $dom = new DOMDocument();
        libxml_use_internal_errors( true );
        
        // Force UTF-8 encoding without adding <html><body> wrappers
        $dom->loadHTML( '<?xml encoding="utf-8" ?>' . $new_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
        
        $xpath = new DOMXPath( $dom );
        $lessons = $xpath->query( '//div[contains(@class, "ld-accordion__item--lesson")]' );
        
        if ( $lessons && $lessons->length > 0 ) {
            foreach ( $lessons as $lesson ) {
                $lesson->parentNode->removeChild( $lesson );
            }
            $new_content = $dom->saveHTML();
            $new_content = str_replace( '<?xml encoding="utf-8" ?>', '', $new_content );
        }
        
        libxml_clear_errors();
    }

    return $new_content;
}

/**
 * Attach Worksheet PDF files to Gravity Forms
 */
add_filter( 'gform_notification', 'oup_attach_worksheet_files_to_email', 10, 3 );
function oup_attach_worksheet_files_to_email( $notification, $form, $entry ) {

    $target_form_id = (int) get_field( 'download_worksheets_gform_id', 'option' );
    
    if ( ! $target_form_id || $form['id'] != $target_form_id ) {
        return $notification;
    }
    $attachments = isset( $notification['attachments'] ) ? $notification['attachments'] : array();
    $hidden_field_id = '';
    foreach ( $form['fields'] as $field ) {
        if ( $field->type == 'hidden' && ( $field->defaultValue == '{embed_post:ID}' || $field->inputName == 'worksheet_post_id' ) ) {
            $hidden_field_id = $field->id;
            break;
        }
    }
    if ( empty( $hidden_field_id ) ) {
        return $notification;
    }

    $post_id = rgar( $entry, (string) $hidden_field_id );
    
    if ( empty( $post_id ) || 'worksheet' !== get_post_type( $post_id ) ) {
        return $notification;
    }
    $worksheet_files = get_field( 'worksheet_files', $post_id );
    if ( ! empty( $worksheet_files ) && is_array( $worksheet_files ) ) {
        foreach ( $worksheet_files as $row ) {
            $file = $row['attachment_file'];
            if ( empty( $file ) ) continue;

            $file_id = is_array( $file ) ? ( $file['ID'] ?? 0 ) : ( is_numeric( $file ) ? $file : 0 );
            
            if ( $file_id && ( $path = get_attached_file( $file_id ) ) && file_exists( $path ) ) {
                $attachments[] = $path;
            } elseif ( is_string( $file ) ) {
                $upload = wp_upload_dir();
                $path   = str_replace( $upload['baseurl'], $upload['basedir'], $file );
                if ( file_exists( $path ) ) $attachments[] = $path;
            }
        }
    }
    $notification['attachments'] = $attachments;

    return $notification;
}