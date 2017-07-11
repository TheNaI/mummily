<?php
//register and enqueue scripts
add_action('wp_head','woo_sp_scripts');
function woo_sp_scripts() {
	wp_register_script('woo-sp-script', plugins_url('assets/js/script.js', __FILE__));
	wp_enqueue_script('woo-sp-script');
} 

add_action('admin_enqueue_scripts', 'woo_sp_scripts_admin');
function woo_sp_scripts_admin() {
	//enqueue the scripts in setting page of plugin
	if(isset($_GET['page']) && $_GET['page'] == 'woo-sp-forms'){
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_style('wp-color-picker');
		wp_register_script('woo-sp-forms-admin-script', plugins_url('assets/js/admin_script.js', __FILE__));
		wp_enqueue_script('woo-sp-forms-admin-script');
	}	
}	

?>