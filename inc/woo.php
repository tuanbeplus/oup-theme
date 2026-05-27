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
