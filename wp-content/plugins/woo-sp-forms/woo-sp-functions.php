<?php
//languages textdomain settings
add_action('plugins_loaded','woo_sp_forms_languages');
function woo_sp_forms_languages() {
	load_plugin_textdomain('woo_sp_forms', false, dirname(plugin_basename( __FILE__ ) ).'/languages/');
}

//get menu lists
function woo_sp_forms_get_all_menus(){
	return get_terms('nav_menu', array('hide_empty' => true)); 
}

//do shortcode
add_filter('widget_text', 'do_shortcode');
?>