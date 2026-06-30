<?php
/**
 * WooCommerce functions
 */

/**
 * Force currency to AUD
 */
add_filter( 'woocommerce_currency_symbol', function( $currency_symbol, $currency ) {
    if ( $currency === 'AUD' ) {
        $currency_symbol = 'AUD';
    }
    return $currency_symbol;

}, 10, 2 );

/**
 * Change size of product image in shop page
 */
add_filter(
    'single_product_archive_thumbnail_size',
    function( $size ) {
        return 'medium_large';
    }
);

/**
 * Add product tags to shop page
 */
add_action( 'woocommerce_before_shop_loop_item_title', 'oup_custom_loop_product_tags', 10);
function oup_custom_loop_product_tags() {
    global $product;
	
    if ( ! $product ) {
        return;
    }

    $tags = get_the_terms(
        $product->get_id(),
        'product_tag'
    );

    if ( empty( $tags ) || is_wp_error( $tags ) ) {
        return;
    }

    echo '<div class="product-tags">';
    foreach ( $tags as $tag ) {

        echo '<div class="product-tag '. $tag->slug .'">';
        echo esc_html( $tag->name );
        echo '</div>';

    }
    echo '</div>';
}
//avatar upload
add_action( 'woocommerce_edit_account_form_tag', 'oup_enctype_edit_account_form' );
function oup_enctype_edit_account_form() {
    echo 'enctype="multipart/form-data"';
}
//avatar field
add_action( 'woocommerce_edit_account_form_start', 'oup_add_avatar_upload_field' );
function oup_add_avatar_upload_field() {
    $user_id = get_current_user_id();
    $avatar_id = get_user_meta( $user_id, 'oup_custom_avatar', true );
    $avatar_url = $avatar_id ? wp_get_attachment_image_url( $avatar_id, 'thumbnail' ) : get_avatar_url( $user_id, ['size' => 150] );
    ?>
    <div class="oup-avatar-upload-wrapper" style="text-align: center; margin-bottom: 30px;">
        <div class="oup-avatar-preview" style="position: relative; width: 150px; height: 150px; margin: 0 auto 15px;">
            <img src="<?php echo esc_url( $avatar_url ); ?>" alt="Avatar" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #ffffff; box-shadow: 0 10px 20px rgba(0,0,0,0.08);" />
            <label for="oup_avatar" style="position: absolute; bottom: 0; right: 0; background: var(--e-global-color-secondary, #2271b1); color: #fff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.15); transition: transform 0.2s;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            </label>
        </div>
        <p style="font-size: 0.85em; color: #888; margin-bottom: 5px;">Click the icon to upload a new avatar (JPG, PNG)</p>
        <input type="file" class="woocommerce-Input" name="oup_avatar" id="oup_avatar" accept="image/jpeg, image/png" style="display: none;" onchange="if(this.files[0]) { var reader = new FileReader(); reader.onload = function(e) { document.querySelector('.oup-avatar-preview img').src = e.target.result; }; reader.readAsDataURL(this.files[0]); }" />
    </div>
    <?php
}
//save avatar
add_action( 'woocommerce_save_account_details', 'oup_save_avatar_upload', 10, 1 );
function oup_save_avatar_upload( $user_id ) {
    if ( isset( $_FILES['oup_avatar'] ) && ! empty( $_FILES['oup_avatar']['name'] ) ) {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        $attachment_id = media_handle_upload( 'oup_avatar', 0 );

        if ( is_wp_error( $attachment_id ) ) {
            wc_add_notice( 'Error uploading avatar: ' . $attachment_id->get_error_message(), 'error' );
        } else {
       
            $old_avatar = get_user_meta( $user_id, 'oup_custom_avatar', true );
            if ( $old_avatar ) { wp_delete_attachment( $old_avatar, true ); }
            
 
            update_user_meta( $user_id, 'oup_custom_avatar', $attachment_id );
        }
    }
}
//change avatar
add_filter( 'get_avatar', 'oup_custom_user_avatar', 10, 5 );
function oup_custom_user_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
    $user = false;

    if ( is_numeric( $id_or_email ) ) {
        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );
    } elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
        $id = (int) $id_or_email->user_id;
        $user = get_user_by( 'id' , $id );
    } elseif ( is_string( $id_or_email ) ) {
        $user = get_user_by( 'email', $id_or_email );
    }

    if ( $user && is_object( $user ) ) {
        $avatar_id = get_user_meta( $user->ID, 'oup_custom_avatar', true );
        if ( $avatar_id ) {
            $image_url = wp_get_attachment_image_url( $avatar_id, [$size, $size] );
            if ( $image_url ) {
                $avatar = "<img alt='{$alt}' src='{$image_url}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
            }
        }
    }
    return $avatar;
}
//change avatar url
add_filter( 'get_avatar_url', 'oup_custom_user_avatar_url', 10, 3 );
function oup_custom_user_avatar_url( $url, $id_or_email, $args ) {
    $user = false;
    if ( is_numeric( $id_or_email ) ) { $user = get_user_by( 'id' , (int) $id_or_email ); }
    elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) { $user = get_user_by( 'id' , (int) $id_or_email->user_id ); }
    elseif ( is_string( $id_or_email ) ) { $user = get_user_by( 'email', $id_or_email ); }

    if ( $user && is_object( $user ) ) {
        $avatar_id = get_user_meta( $user->ID, 'oup_custom_avatar', true );
        if ( $avatar_id ) {
            $custom_url = wp_get_attachment_image_url( $avatar_id, [$args['size'], $args['size']] );
            if ( $custom_url ) { return $custom_url; }
        }
    }
    return $url;
}
//redirect edit profile
add_filter( 'edit_profile_url', 'oup_custom_edit_profile_url', 10, 3 );
function oup_custom_edit_profile_url( $url, $user_id, $scheme ) {

    if ( ! is_admin() && class_exists( 'WooCommerce' ) ) {
        return wc_get_endpoint_url( 'edit-account', '', wc_get_page_permalink( 'myaccount' ) );
    }
    return $url;
}

// Product Metadata
add_action( 'woocommerce_single_product_summary', 'oup_product_metadata', 11 );
function oup_product_metadata() {
    $pages = get_field('pages') ?? '';
    $audience = get_field('audience') ?? '';
    if ( is_array( $audience ) && !empty( $audience )) {
        $count = count( $audience );
		if ( $count > 1 ) {
			$last = array_pop( $audience );
			$audience = implode( ', ', $audience ) . ' and ' . $last;
		} else {
			$audience = reset( $audience );
		}
    }
    ?>
    <div class="product-metadata">
        <?php if (!empty($pages)): ?>
            <p><strong>Pages:&nbsp;</strong><?php echo esc_html($pages); ?></p>
        <?php endif; ?>
        <?php if (!empty($audience)): ?>
            <p><strong>Audience:&nbsp;</strong><?php echo esc_html($audience); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

//change thankyou page message
add_filter( 'woocommerce_thankyou_order_received_text', function( $text, $order ) {
    return "YASSSSS!<br> We've received your order and have all the happy wiggles!<br> Here's confirmation of the good things to come, with a follow up in your email inbox too.";
}, 10, 2 );