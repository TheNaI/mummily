<?php
/**
 * Product loop sale flash
 *
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $post, $product, $porto_settings;

$labels = '';
if ($porto_settings['product-hot']) {
    $featured = $product->is_featured();
    if ($featured) {
        $hot_html = '<div class="onhot">'. ((isset($porto_settings['product-hot-label']) && $porto_settings['product-hot-label']) ? $porto_settings['product-hot-label'] : __('Hot', 'porto')) .'</div>';
        $labels .= $hot_html;
    }
}
if ($porto_settings['product-sale']) {
    if ($product->is_on_sale()) {
        $percentage = 0;
        if ($product->get_regular_price())
            $percentage = - round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
        if ($porto_settings['product-sale-percent'] && $percentage)
            $sales_html = '<div class="onsale">'. $percentage .'%</div>';
        else
            $sales_html = apply_filters('woocommerce_sale_flash', '<div class="onsale">'. ((isset($porto_settings['product-sale-label']) && $porto_settings['product-sale-label']) ? $porto_settings['product-sale-label'] : __('Sale', 'porto')) .'</div>', $post, $product);
        $labels .= $sales_html;
    }
}

if( $labels ){
	echo '<div class="labels">' . $labels . '</div>';
}

$availability = $product->get_availability();
if ( $availability['availability'] == __( 'Out of stock', 'porto' )) {
    if ($porto_settings['product-stock']) {
        echo apply_filters( 'woocommerce_stock_html', '<div class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</div>', $availability['availability'] );
    }
} else {
	if ( $porto_settings['add-to-cart-notification'] != '2' ){
		echo '<div data-link="' . get_permalink( wc_get_page_id( 'cart' ) ) . '" class="viewcart ' . str_replace('minicart-icon', 'viewcart', $porto_settings['minicart-icon']) .' viewcart-'.$product->get_id().'" title="' . __('View Cart', 'porto') . '"></div>';
	}
}