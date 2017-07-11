<?php
/*
Plugin Name: WooCommerce Popup Signup & Login Forms
Plugin URI: http://web-cude.com/woo-forms/
Description: With this addon for WooCommerce, you can easily create a Signup & Login process for your shop.
Version: 1.1
Text Domain: woo_sp_forms
Domain Path: /languages
Author: Alex Kuimov
Author URI: http://web-cude.com
*/

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

	//default setting
	register_activation_hook( __FILE__, 'woo_sp_plugin_activate');
	function woo_sp_plugin_activate(){
		update_option('woo_sp_forms_redirect_after_login', ' ');
	    update_option('woo_sp_forms_redirect_after_reg', ' ');
		update_option('woo_sp_forms_menu', ' ');
		update_option('woo_sp_forms_login_title', 'Login');
		update_option('woo_sp_forms_logout_title', 'Logout');
		update_option('woo_sp_forms_reg_title', 'Register');
		update_option('woo_sp_forms_main_color', '#33b5e5');
		update_option('woo_sp_forms_text_color', '#757575');
		update_option('woo_sp_forms_bg_color', '#ffffff');
		update_option('woo_sp_forms_simple_reg', 'n');
	}	

	//require functions
	require_once(plugin_dir_path(__FILE__).'woo-sp-functions.php');
	//require scripts
	require_once(plugin_dir_path(__FILE__).'woo-sp-scripts.php');
	//require styles
	require_once(plugin_dir_path(__FILE__).'woo-sp-styles.php');
	//require shortcodes
	require_once(plugin_dir_path(__FILE__).'woo-sp-shortcodes.php');
	//require redirects
	require_once(plugin_dir_path(__FILE__).'woo-sp-redirects.php');
	//require core
	require_once(plugin_dir_path(__FILE__).'woo-sp-core.php');
	//require admin
	require_once(plugin_dir_path(__FILE__).'woo-sp-forms-admin.php');

}	

?>