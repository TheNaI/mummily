<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForTroubleshoot')) {

    class RSFunctionForTroubleshoot {

        public static function init() {

            if (get_option('rs_load_script_styles') == 'wp_head') {
                add_action('wp_head', array(__CLASS__, 'rs_load_script_from_header_or_footer'));
            } else {
                add_action('wp_footer', array(__CLASS__, 'rs_load_script_from_header_or_footer'));
            }
        }

        public static function rs_load_script_from_header_or_footer() {

            RSFUnctinforVariableProduct::display_purchase_msg_for_variable_product();

            RSFunctionForEmailTemplate::get_the_checkboxvalue_from_myaccount_page();

            RSFunctionForMyAccount::add_script_to_my_account();

            if (get_option('rs_show_hide_nominee_field') == '1') {

                RSFunctionForMyAccount::ajax_for_saving_nominee();
            }

            RSFunctionForSocialRewards::add_fb_style_hide_comment_box();

            RSBookingCompatibility::booking_compatible();

            RSFunctionForCart::validation_in_my_cart();
            
            RSFunctionForCart::test_coupon();
            
            RSFunctionForMyAccount::rs_chosen_for_nominee_in_my_account_tab();
            
            RSFunctionForGiftVoucher::rs_script_to_redeem_voucher();
            
            RSFunctionForMyAccount::rs_script_to_generate_referral_link();
        }

    }

    RSFunctionForTroubleshoot::init();
}