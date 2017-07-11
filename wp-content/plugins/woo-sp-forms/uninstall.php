<?php 
//delete option
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

delete_option('woo_sp_forms_redirect_after_login');
delete_option('woo_sp_forms_redirect_after_reg');
delete_option('woo_sp_forms_menu');
delete_option('woo_sp_forms_login_title');
delete_option('woo_sp_forms_logout_title');
delete_option('woo_sp_forms_reg_title');
delete_option('woo_sp_forms_main_color');
delete_option('woo_sp_forms_text_color');
delete_option('woo_sp_forms_bg_color');
delete_option('woo_sp_forms_simple_reg');