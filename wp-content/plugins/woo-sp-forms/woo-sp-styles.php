<?php
//register & enqueue styles
add_action('wp_enqueue_scripts', 'woo_sp_style');
function woo_sp_style() {
	wp_register_style('woo-sp-style', plugins_url('assets/css/style.css', __FILE__), false, false, 'all');
	wp_enqueue_style('woo-sp-style');
}
?>