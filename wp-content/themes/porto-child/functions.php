<?php

add_action('wp_enqueue_scripts', 'porto_child_css', 1001);

// Load CSS
function porto_child_css() {
    // porto child theme styles
    wp_deregister_style( 'styles-child' );
    wp_register_style( 'styles-child', get_stylesheet_directory_uri() . '/style.css' );
    wp_enqueue_style( 'styles-child' );

    //custom css
    wp_deregister_style( 'styles-custom' );
    wp_register_style( 'styles-custom', get_stylesheet_directory_uri() . '/css/custom.css' );
    wp_enqueue_style( 'styles-custom' );

    if (is_rtl()) {
        wp_deregister_style( 'styles-child-rtl' );
        wp_register_style( 'styles-child-rtl', get_stylesheet_directory_uri() . '/style_rtl.css' );
        wp_enqueue_style( 'styles-child-rtl' );
    }
}

/*
add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );
function quantity_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {
	if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
		$html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" method="post" enctype="multipart/form-data">';
		$html .= woocommerce_quantity_input( array(), $product, false );
		$html .= '<button type="submit" class="button alt">' . esc_html( $product->add_to_cart_text() ) . '</button>';
		$html .= '</form>';
	}
	return $html;
}
*/

function action_woocommerce_before_main_content() {
  echo '<div style="margin-bottom:40px;">'.do_shortcode( '[rev_slider alias="bannershop"]' ).'</div>';
};

add_action( 'woocommerce_before_main_content', 'action_woocommerce_before_main_content', 10, 2 );

function filter_woocommerce_shipping_package_name( $sprintf, $i, $package ) {
    $sprintf = 'วิธีจัดส่ง';    
    return $sprintf;
};

// add the filter
add_filter( 'woocommerce_shipping_package_name', 'filter_woocommerce_shipping_package_name', 10, 3 );
