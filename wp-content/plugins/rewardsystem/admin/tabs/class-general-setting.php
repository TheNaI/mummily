<?php
/*
 * General Tab Setting
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSGeneralTabSetting')) {

    class RSGeneralTabSetting {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings

            add_action('woocommerce_rs_settings_tabs_rewardsystem_general', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab

            add_action('woocommerce_update_options_rewardsystem_general', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system

            if (class_exists('SUMOSubscriptions')) {
                add_filter('woocommerce_rewardsystem_general_settings', array(__CLASS__, 'add_custom_field_to_general_tab'));
            }

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'), 999);

            add_action('admin_head', array(__CLASS__, 'rs_chosen_for_general_tab'));

            add_action('woocommerce_admin_field_rs_select_user_to_restrict_ban', array(__CLASS__, 'rs_select_user_to_ban'));

            add_action('admin_head', array(__CLASS__, 'get_woocommerce_upload_field'));

            add_action('woocommerce_admin_field_uploader', array(__CLASS__, 'rs_add_upload_your_gift_voucher'));

            add_action('woocommerce_admin_field_earning_conversion', array(__CLASS__, 'reward_system_earning_points_conversion'));

            add_action('woocommerce_admin_field_redeeming_conversion', array(__CLASS__, 'reward_system_redeeming_points_conversion'));

            add_action('woocommerce_admin_field_redeeming_conversion_for_cash_back', array(__CLASS__, 'reward_system_redeeming_points_conversion_for_cash_back'));

            add_action('woocommerce_admin_field_rs_refresh_button', array(__CLASS__, 'refresh_button_for_expired'));

            add_action('admin_head', array(__CLASS__, 'rs_send_ajax_to_refresh_expired_points'));

            add_action('wp_ajax_nopriv_rsrefreshexpiredpoints', array(__CLASS__, 'rs_process_ajax_to_get_all_user_id'));

            add_action('wp_ajax_rsrefreshexpiredpoints', array(__CLASS__, 'rs_process_ajax_to_get_all_user_id'));

            add_action('wp_ajax_rssplitrefreshexpiredpoints', array(__CLASS__, 'process_ajax_to_refresh_user_points'));

            add_action('fp_action_to_reset_settings_rewardsystem_general', array(__CLASS__, 'rs_function_to_reset_general_tab'));
        }

        /*
         * @param $settingstab RSGeneralTabSetting 
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_general'] = __('General', 'rewardsystem');
            return $setting_tabs;
        }

        public static function reward_system_admin_fields() {
            global $woocommerce;
            $list_of_user_roles = fp_rs_get_user_roles();
            return apply_filters('woocommerce_rewardsystem_general_settings', array(
                array(
                    'name' => __('General Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_general_tab_setting',
                ),
                array(
                    'type' => 'rs_refresh_button',
                ),
                array(
                    'name' => __('Menu Name', 'rewardsystem'),
                    'desc' => __('Enter the Menu Name to be displayed in Dashboard', 'rewardsystem'),
                    'id' => 'rs_brand_name',
                    'class' => 'rs_brand_name',
                    'css' => 'min-width:150px;',
                    'std' => 'SUMO Reward Points',
                    'default' => 'SUMO Reward Points',
                    'desc_tip' => true,
                    'newids' => 'rs_brand_name',
                    'type' => 'text',
                ),
                array(
                    'name' => __('Sale Priced Products', 'rewardsystem'),
                    'desc' => __('Enable this option to prevent earning of points on products that have “sale price"', 'rewardsystem'),
                    'id' => 'rs_pointx_not_award_when_sale_price',
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'newids' => 'rs_pointx_not_award_when_sale_price',
                ),
                array(
                    'name' => __('Maximum Threshold for Total Points per User', 'rewardsystem'),
                    'desc' => __('Enable this option to provide maximum threshold for total points per user', 'rewardsystem'),
                    'id' => 'rs_enable_disable_max_earning_points_for_user',
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'newids' => 'rs_enable_disable_max_earning_points_for_user',
                ),
                array(
                    'name' => __('Maximum Threshold Value for Total Points per User', 'rewardsystem'),
                    'desc' => __('Enter a Fixed or Decimal Number greater than 0', 'rewardsystem'),
                    'id' => 'rs_max_earning_points_for_user',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'desc_tip' => true,
                    'newids' => 'rs_max_earning_points_for_user',
                    'type' => 'text',
                ),
                array(
                    'name' => __('Calculate Reward Points based on Discounted Price', 'rewardsystem'),
                    'desc' => __('Enable this option to calculate reward points based on discounted price i.e the price after applying coupon(s)', 'rewardsystem'),
                    'id' => 'rs_enable_disable_reward_point_based_coupon_amount',
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'newids' => 'rs_enable_disable_reward_point_based_coupon_amount',
                ),
                array('type' => 'sectionend', 'id' => 'rs_general_tab_setting'),
                array(
                    'name' => __('Global Settings for Point Pricing', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_global_Point_price'
                ),
                array(
                    'name' => __('Point Pricing', 'rewardsystem'),
                    'id' => 'rs_enable_disable_point_priceing',
                    'default' => '1',
                    'std' => '1',
                    'newids' => 'rs_enable_disable_point_priceing',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Label for Point', 'rewardsystem'),
                    'desc' => __('Enter label value to display point', 'rewardsystem'),
                    'id' => 'rs_label_for_point_value',
                    'css' => 'min-width:150px;',
                    'default' => '/Pt',
                    'std' => '/Pt',
                    'desc_tip' => true,
                    'newids' => 'rs_label_for_point_value',
                    'type' => 'text',
                ),
                array(
                    'name' => __('Display Position of Point Price Label', 'rewardsystem'),
                    'id' => 'rs_sufix_prefix_point_price_label',
                    'default' => '1',
                    'std' => '1',
                    'newids' => 'rs_sufix_prefix_point_price_label',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Before', 'rewardsystem'),
                        '2' => __('After', 'rewardsystem'),
                    ),
                ),
                array('type' => 'sectionend', 'id' => '_rs_global_Point_price'),
                array(
                    'name' => __('Global Settings for Product Purchase Reward Points', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_global_reward_points'
                ),
                array(
                    'name' => __('Global Level Point Pricing', 'rewardsystem'),
                    'id' => 'rs_local_enable_disable_point_price_for_product',
                    'css' => 'min-width:150px;',
                    'std' => '2',
                    'default' => '2',
                    'desc_tip' => true,
                    'newids' => 'rs_local_enable_disable_point_price_for_product',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Point Price Type', 'rewardsystem'),
                    'id' => 'rs_global_point_price_type',
                    'css' => 'min-width:150px;',
                    'std' => '2',
                    'default' => '2',
                    'desc_tip' => true,
                    'newids' => 'rs_global_point_price_type',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Fixed', 'rewardsystem'),
                        '2' => __('Based On Conversion', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Pricing Point(s)', 'rewardsystem'),
                    'desc' => __('Please Enter Price Points', 'rewardsystem'),
                    'id' => 'rs_local_price_points_for_product',
                    'class' => 'rs_local_price_points_for_product',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_price_points_for_product',
                    'placeholder' => '',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Global Level Reward Points', 'rewardsystem'),
                    'id' => 'rs_global_enable_disable_sumo_reward',
                    'css' => 'min-width:150px;',
                    'std' => '2',
                    'default' => '2',
                    'placeholder' => '',
                    'desc_tip' => true,
                    'desc' => __('Global Settings will be considered when Product and Category Settings are Enabled and Values are Empty. '
                            . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order.', 'rewardsystem'),
                    'newids' => 'rs_global_enable_disable_sumo_reward',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Reward Type', 'rewardsystem'),
                    'desc' => __('Select Reward Type by Points/Percentage', 'rewardsystem'),
                    'id' => 'rs_global_reward_type',
                    'class' => 'show_if_enable_in_general',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_global_reward_type',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Reward Points', 'rewardsystem'),
                    'id' => 'rs_global_reward_points',
                    'class' => 'show_if_enable_in_general',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_global_reward_points',
                    'placeholder' => '',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Reward Points in Percent %', 'rewardsystem'),
                    'id' => 'rs_global_reward_percent',
                    'class' => 'show_if_enable_in_general',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_global_reward_percent',
                    'placeholder' => '',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Referral Reward Type', 'rewardsystem'),
                    'desc' => __('Select Reward Type by Points/Percentage', 'rewardsystem'),
                    'id' => 'rs_global_referral_reward_type',
                    'class' => 'show_if_enable_in_general',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_global_referral_reward_type',
                    'type' => 'select',
                    'desc_tip' => true,
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Referral Reward Points', 'rewardsystem'),
                    'id' => 'rs_global_referral_reward_point',
                    'class' => 'show_if_enable_in_general',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_global_referral_reward_point',
                    'placeholder' => '',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Referral Reward Points in Percent %', 'rewardsystem'),
                    'id' => 'rs_global_referral_reward_percent',
                    'class' => 'show_if_enable_in_general',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_global_referral_reward_percent',
                    'placeholder' => '',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Getting Referred Reward Type', 'rewardsystem'),
                    'desc' => __('Select Reward Type by Points/Percentage', 'rewardsystem'),
                    'id' => 'rs_global_referral_reward_type_refer',
                    'class' => 'show_if_enable_in_general',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_global_referral_reward_type_refer',
                    'type' => 'select',
                    'desc_tip' => true,
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Reward Points for Getting Referred', 'rewardsystem'),
                    'id' => 'rs_global_referral_reward_point_get_refer',
                    'class' => 'show_if_enable_in_general',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_global_referral_reward_point_get_refer',
                    'placeholder' => '',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Reward Points in Percent % For Getting Referred', 'rewardsystem'),
                    'id' => 'rs_global_referral_reward_percent_get_refer',
                    'class' => 'show_if_enable_in_general',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_global_referral_reward_percent_get_refer',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_global_reward_points'),
                array(
                    'name' => __('Earning Points Conversion Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_point_conversion'
                ),
                array(
                    'type' => 'earning_conversion',
                ),
                array('type' => 'sectionend', 'id' => '_rs_point_conversion'),
                array(
                    'name' => __('Redeeming Points Conversion Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_redeem_point_conversion'
                ),
                array(
                    'type' => 'redeeming_conversion',
                ),
                array('type' => 'sectionend', 'id' => '_rs_redeem_point_conversion'),
                array(
                    'name' => __('Redeeming Points Conversion Settings for Cashback', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_redeem_point_conversion_for_cash_back'
                ),
                array(
                    'type' => 'redeeming_conversion_for_cash_back',
                ),
                array('type' => 'sectionend', 'id' => '_rs_redeem_point_conversion_cash_back'),
                array(
                    'name' => __('Points Expiry Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_point_setting'
                ),
                array(
                    'name' => __('Validity Period for Points', 'rewardsystem'),
                    'type' => 'number',
                    'id' => 'rs_point_to_be_expire',
                    'class' => 'rs_point_to_be_expire',
                    'newids' => 'rs_point_to_be_expire',
                    'css' => 'min-width:150px;',
                    'custom_attributes' => array(
                        'min' => '0'
                    ),
                    'std' => '',
                    'default' => '',
                    'desc' => __('day(s)', 'rewardsystem'),
                ),
                array('type' => 'sectionend', 'id' => '_rs_point_setting'),
                array(
                    'name' => __('Redeeming Coupon Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_redeeming_coupon_setting'
                ),
                array(
                    'name' => __('Prevent Coupon Usage when points are redeemed', 'rewardsystem'),
                    'id' => 'rs_coupon_applied_individual',
                    'class' => 'rs_coupon_applied_individual',
                    'newids' => 'rs_coupon_applied_individual',
                    'css' => 'min-width:150px;',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'desc' => __('Enable this option to prevent coupon usage when points are redeemed', 'rewardsystem'),
                ),
                array(
                    'name' => __('Error Message', 'rewardsystem'),
                    'desc' => __('Text for Error Message for redeeming Coupon When applied with other coupon', 'rewardsystem'),
                    'id' => 'rs_coupon_applied_individual_error_msg',
                    'class' => 'rs_coupon_applied_individual_error_msg',
                    'newids' => 'rs_coupon_applied_individual_error_msg',
                    'css' => 'min-width:400px;',
                    'std' => 'Coupon cannot be applied when points are redeemed',
                    'default' => 'Coupon cannot be applied when points are redeemed',
                    'type' => 'textarea',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_redeeming_coupon_setting'),
                array(
                    'name' => __('Maximum Redeeming Threshold Value (Discount) Settings for Order', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_discount_control'
                ),
                array(
                    'name' => __('Maximum Redeeming Threshold Value (Discount) Type', 'rewardsystem'),
                    'id' => 'rs_max_redeem_discount',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'newids' => 'rs_max_redeem_discount',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By Fixed Value', 'rewardsystem'),
                        '2' => __('By Percentage of Cart Total', 'rewardsystem'),
                    ),
                    'desc_tip' => false,
                ),
                array(
                    'name' => __('Maximum Redeeming Threshold Value (Discount) for Order in ' . get_woocommerce_currency_symbol(), 'rewardsystem'),
                    'desc' => __('Enter a Fixed or Decimal Number greater than 0', 'rewardsystem'),
                    'id' => 'rs_fixed_max_redeem_discount',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_fixed_max_redeem_discount',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Maximum Redeeming Threshold Value (Discount) for Order in Percentage %', 'rewardsystem'),
                    'desc' => __('Enter a Fixed or Decimal Number greater than 0', 'rewardsystem'),
                    'id' => 'rs_percent_max_redeem_discount',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_percent_max_redeem_discount',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_discount_control'),
                array(
                    'name' => __('Minimum Cart Total Settings for SUMO Reward Points Payment Gateway', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_discount_control_for_gateway'
                ),
                array(
                    'name' => __('Minimum Cart Total for using SUMO Reward Points Payment Gateway', 'rewardsystem'),
                    'desc' => __('Enter the Minimum Cart Total that can be used using SUMO Reward Points Payment Gateway', 'rewardsystem'),
                    'id' => 'rs_max_redeem_discount_for_sumo_reward_points',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_max_redeem_discount_for_sumo_reward_points',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_discount_control_for_gateway'),
                array(
                    'name' => __('Round Off Settings for Display of Reward Points', 'rewardsystem'),
                    'desc' => __('This Settings is used only for display purpose and not for any calculation purpose', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_round_off_settings',
                ),
                array(
                    'name' => __('Round Off Type', 'rewardsystem'),
                    'id' => 'rs_round_off_type',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('2 Decimal Places', 'rewardsystem'),
                        '2' => __('Whole Number', 'rewardsystem'),
                    ),
                    'newids' => 'rs_round_off_type',
                    'desc_tip' => false,
                ),
                array('type' => 'sectionend', 'id' => '_rs_round_off_settings'),
                array(
                    'name' => __('Referral Link Cookies Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_referral_cookies_settings'
                ),
                array(
                    'name' => __('Referral Link Cookies Expires in', 'rewardsystem'),
                    'id' => 'rs_referral_cookies_expiry',
                    'css' => 'min-width:150px;',
                    'std' => '3',
                    'default' => '3',
                    'newids' => 'rs_referral_cookies_expiry',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Minutes', 'rewardsystem'),
                        '2' => __('Hours', 'rewardsystem'),
                        '3' => __('Days', 'rewardsystem'),
                    ),
                    'desc_tip' => false,
                ),
                array(
                    'name' => __('Referral Link Cookies Expiry in Minutes', 'rewardsystem'),
                    'desc' => __('Enter a Fixed Number greater than or equal to 0', 'rewardsystem'),
                    'id' => 'rs_referral_cookies_expiry_in_min',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_referral_cookies_expiry_in_min',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Referral Link Cookies Expiry in Hours', 'rewardsystem'),
                    'desc' => __('Enter a Fixed Number greater than or equal to 0', 'rewardsystem'),
                    'id' => 'rs_referral_cookies_expiry_in_hours',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_referral_cookies_expiry_in_hours',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Referral Link Cookies Expiry in Days', 'rewardsystem'),
                    'desc' => __('Enter a Fixed Number greater than or equal to 0', 'rewardsystem'),
                    'id' => 'rs_referral_cookies_expiry_in_days',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'text',
                    'newids' => 'rs_referral_cookies_expiry_in_days',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Delete Cookies After X Number of Purchase(s)', 'rewardsystem'),
                    'desc' => __('Enable this option to delete cookies after X number of purchase(s)', 'rewardsystem'),
                    'id' => 'rs_enable_delete_referral_cookie_after_first_purchase',
                    'css' => 'min-width:150px;',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_enable_delete_referral_cookie_after_first_purchase',
                ),
                array(
                    'name' => __('Number of Purchase(s)', 'rewardsystem'),
                    'desc' => __('Number of Purchase(s) in which cookie to be deleted', 'rewardsystem'),
                    'id' => 'rs_no_of_purchase',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_no_of_purchase',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_referral_cookies_settings'),
                array(
                    'name' => __('Linking Referrals for Life Time Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_life_time_referral',
                ),
                array(
                    'name' => __('Linking Referrals for Life Time', 'rewardsystem'),
                    'desc' => __('Enable this option to link referrals for life time', 'rewardsystem'),
                    'id' => 'rs_enable_referral_link_for_life_time',
                    'css' => 'min-width:150px;',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_enable_referral_link_for_life_time',
                ),
                array('type' => 'sectionend', 'id' => '_rs_life_time_referral'),
                array(
                    'name' => __('Referrer Earning Restriction Settings', 'rewardsystem'),
                    'type' => 'title',
                    'desc' => __('For eg: If A Refers B then A is the Referrer and B is the Referral', 'rewardsystem'),
                    'id' => '_rs_ban_referee_points_time',
                ),
                array(
                    'name' => __('Referrer should earn points only after the user(Buyer or Referral) is X days old', 'rewardsystem'),
                    'id' => '_rs_select_referral_points_referee_time',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => '_rs_select_referral_points_referee_time',
                    'type' => 'select',
                    'desc_tip' => false,
                    'options' => array(
                        '1' => __('Unlimited', 'rewardsystem'),
                        '2' => __('Limited', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Number of Day(s)', 'rewardsystem'),
                    'desc' => __('Enter Fixed Number greater than or equal to 0', 'rewardsystem'),
                    'id' => '_rs_select_referral_points_referee_time_content',
                    'css' => 'min-width:150px;',
                    'newids' => '_rs_select_referral_points_referee_time_content',
                    'type' => 'text',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('If the Referred Person\'s account is deleted, the Referral Points', 'rewardsystem'),
                    'id' => '_rs_reward_referal_point_user_deleted',
                    'css' => 'min-width:150px;',
                    'std' => '2',
                    'default' => '2',
                    'newids' => '_rs_reward_referal_point_user_deleted',
                    'type' => 'select',
                    'desc_tip' => false,
                    'options' => array(
                        '1' => __('Should be Revoked', 'rewardsystem'),
                        '2' => __('Shouldn\'t be Revoked', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Applies for Referral account created', 'rewardsystem'),
                    'id' => '_rs_time_validity_to_redeem',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => '_rs_time_validity_to_redeem',
                    'type' => 'select',
                    'desc_tip' => false,
                    'options' => array(
                        '1' => __('Any time', 'rewardsystem'),
                        '2' => __('Within specific number of days', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Number of Day(s)', 'rewardsystem'),
                    'desc' => __('Enter Fixed Number greater than or equal to 0', 'rewardsystem'),
                    'id' => '_rs_days_for_redeeming_points',
                    'css' => 'min-width:150px;',
                    'newids' => '_rs_days_for_redeeming_points',
                    'type' => 'text',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_ban_referee_points_time'),
                array(
                    'name' => __('Gift Icon Settings', 'rewardsystem'),
                    'desc' => __('For Simple Product, Shop, Category and Product Page is Supported. For Variable Products, Shop and Category Page is not Supported', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_gift_icon_selection',
                ),
                array(
                    'name' => __('Gift Icon', 'rewardsystem'),
                    'id' => '_rs_enable_disable_gift_icon',
                    'css' => 'min-width:150px;',
                    'std' => '2',
                    'default' => '2',
                    'newids' => '_rs_enable_disable_gift_icon',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                    'desc_tip' => false,
                ),
                array(
                    'type' => 'uploader',
                ),
                array('type' => 'sectionend', 'id' => '_rs_gift_icon_selection'),
                array(
                    'name' => __('Date and Time display Settings for Log', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_dispaly_time_format',
                ),
                array(
                    'name' => __('Date and Time display Format Type', 'rewardsystem'),
                    'id' => 'rs_dispaly_time_format',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_dispaly_time_format',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Default', 'rewardsystem'),
                        '2' => __('WordPress Format', 'rewardsystem'),
                    ),
                    'desc' => __('If Default is selected as Date and Time Format Type, then the date and time should be displayed as d-m-Y h:i:s A. If WordPress Format is selected, then the date and time format in WordPress settings is consider as date and time format', 'rewardsystem'),
                ),
                array('type' => 'sectionend', 'id' => 'rs_dispaly_time_format'),
                array(
                    'name' => __('Restrict/Ban User(s)/Userrole(s)', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_ban_users',
                ),
                array(
                    'name' => __('Earning Points', 'rewardsystem'),
                    'desc' => __('Ban Users from Earning Points', 'rewardsystem'),
                    'id' => 'rs_enable_banning_users_earning_points',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'checkbox',
                    'newids' => 'rs_enable_banning_users_earning_points',
                ),
                array(
                    'name' => __('Redeeming Points', 'rewardsystem'),
                    'desc' => __('Ban Users from Redeeming Points', 'rewardsystem'),
                    'id' => 'rs_enable_banning_users_redeeming_points',
                    'css' => 'min-width:150px;',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_enable_banning_users_redeeming_points',
                ),
                array(
                    'type' => 'rs_select_user_to_restrict_ban',
                ),
                array(
                    'name' => __('Select the Userrole(s)', 'rewardsystem'),
                    'id' => 'rs_banning_user_role',
                    'css' => 'min-width:343px;',
                    'std' => '',
                    'default' => '',
                    'placeholder' => 'Search for a User Role',
                    'type' => 'multiselect',
                    'options' => $list_of_user_roles,
                    'newids' => 'rs_banning_user_role',
                    'desc' => __('Here you select the userroles whom you wish to ban from earning and using Reward Points', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_ban_users'),
                array('type' => 'sectionend', 'id' => '_rs_reward_point_general_settings'),
                array(
                    'name' => __('Custom CSS Settings', 'rewardsystem'),
                    'type' => 'title',
                    'desc' => 'Try !important if styles doesn\'t apply ',
                    'id' => '_rs_general_custom_css_settings',
                ),
                array(
                    'name' => __('Custom CSS', 'rewardsystem'),
                    'desc' => __('Enter the Custom CSS ', 'rewardsystem'),
                    'id' => 'rs_general_custom_css',
                    'css' => 'min-width:350px; min-height:350px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'textarea',
                    'newids' => 'rs_general_custom_css',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_point_general_settings'),
            ));
        }

        /*
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */

        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields(RSGeneralTabSetting::reward_system_admin_fields());
        }

        /*
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */

        public static function reward_system_update_settings() {
            woocommerce_update_options(RSGeneralTabSetting::reward_system_admin_fields());
            update_option('rs_banned_users_list', $_POST['rs_banned_users_list']);
            update_option('rs_earn_point', $_POST['rs_earn_point']);
            update_option('rs_earn_point_value', $_POST['rs_earn_point_value']);
            update_option('rs_redeem_point', $_POST['rs_redeem_point']);
            update_option('rs_redeem_point_value', $_POST['rs_redeem_point_value']);
            update_option('rs_redeem_point_for_cash_back', $_POST['rs_redeem_point_for_cash_back']);
            update_option('rs_redeem_point_value_for_cash_back', $_POST['rs_redeem_point_value_for_cash_back']);
        }

        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSGeneralTabSetting::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function add_custom_field_to_general_tab($settings) {
            $updated_settings = array();
            foreach ($settings as $section) {
                if (isset($section['id']) && 'rs_general_tab_setting' == $section['id'] &&
                        isset($section['type']) && 'sectionend' == $section['type']) {
                    $updated_settings[] = array(
                        'name' => __('Don\'t Award Points for Renewal Orders of SUMO Subscriptions', 'rewardsystem'),
                        'desc' => __('If You Enable this option, Reward Points for Renewal orders will not be awarded.', 'rewardsystem'),
                        'id' => 'rs_award_point_for_renewal_order',
                        'std' => 'no',
                        'type' => 'checkbox',
                        'newids' => 'rs_award_point_for_renewal_order',
                    );
                    $updated_settings[] = array(
                        'name' => __('Don\'t Award Referral Product Purchase Points for Renewal Orders of SUMO Subscriptions', 'rewardsystem'),
                        'desc' => __('If You Enable this option, Referral Product Purchase Points for Renewal orders will not be awarded.', 'rewardsystem'),
                        'id' => 'rs_award_referral_point_for_renewal_order',
                        'std' => 'no',
                        'type' => 'checkbox',
                        'newids' => 'rs_award_referral_point_for_renewal_order',
                    );
                }
                $updated_settings[] = $section;
            }

            return $updated_settings;
        }

        /*
         * Function for choosen in Select user role for banning
         */

        public static function rs_chosen_for_general_tab() {
            global $woocommerce;
            if (isset($_GET['page'])) {
                if ($_GET['page'] == 'rewardsystem_callback') {
                    if ((float) $woocommerce->version > (float) ('2.2.0')) {
                        echo rs_common_select_function('#rs_banning_user_role');
                    } else {
                        echo rs_common_chosen_function('#rs_banning_user_role');
                    }
                }
            }
        }

        /*
         * Function to Select user for banning
         */

        public static function rs_select_user_to_ban() {
            ?>
            <style type="text/css">
                .chosen-container-single {
                    position:absolute;
                }
            </style>
            <?php
            $field_id = "rs_banned_users_list";
            $field_label = "Select the User(s)";
            $getuser = get_option('rs_banned_users_list');
            echo rs_function_to_add_field_for_user_select($field_id, $field_label, $getuser);
        }

        public static function get_woocommerce_upload_field() {
            if (isset($_REQUEST['rs_image_url_upload'])) {
                update_option('rs_image_url_upload', $_POST['rs_image_url_upload']);
            }
        }

        /*
         * Function For Upload Your own Gift
         */

        public static function rs_add_upload_your_gift_voucher() {
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label for="rs_image_url_upload"><?php _e('Upload your own Gift Icon', 'rewardsystem'); ?></label>
                    </th>
                    <td class="forminp forminp-select">
                        <input type="text" id="rs_image_url_upload" name="rs_image_url_upload" value="<?php echo get_option('rs_image_url_upload'); ?>"/>
                        <input type="submit" id="rs_image_upload_button" name="rs_image_upload_button" value="Upload Image"/>
                    </td>
                </tr>
            </table>
            <?php
            rs_ajax_for_upload_your_gift_voucher('#rs_image_upload_button');
        }

        public static function reward_system_earning_points_conversion() {
            ?>
            <tr valign="top">

                <td class="forminp forminp-text">
                    <input type="number" step="any" min="0" value="<?php echo get_option('rs_earn_point'); ?>" style="max-width:50px;" id="rs_earn_point" name="rs_earn_point"> <?php _e('Earning Point(s)', 'rewardsystem'); ?>
                    &nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
            <?php echo get_woocommerce_currency_symbol(); ?> <input type="number" step="any" min="0" value="<?php echo get_option('rs_earn_point_value'); ?>" style="max-width:50px;" id="rs_earn_point_value" name="rs_earn_point_value">
                </td>
            </tr>

            <?php
        }

        public static function reward_system_redeeming_points_conversion() {
            ?>
            <tr valign="top">
                <td class="forminp forminp-text">
                    <input type="number" step="any" min="0" value="<?php echo get_option('rs_redeem_point'); ?>" style="max-width:50px;" id="rs_redeem_point" name="rs_redeem_point"> <?php _e('Redeeming Point(s)', 'rewardsystem'); ?>
                    &nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
            <?php echo get_woocommerce_currency_symbol(); ?> 	<input type="number" step="any" min="0" value="<?php echo get_option('rs_redeem_point_value'); ?>" style="max-width:50px;" id="rs_redeem_point_value" name="rs_redeem_point_value"></td>
            </td>
            </tr>
            <?php
        }

        public static function reward_system_redeeming_points_conversion_for_cash_back() {
            ?>
            <tr valign="top">
                <td class="forminp forminp-text">
                    <input type="number" step="any" min="0" value="<?php echo get_option('rs_redeem_point_for_cash_back'); ?>" style="max-width:50px;" id="rs_redeem_point_for_cash_back" name="rs_redeem_point_for_cash_back"> <?php _e('Redeeming Point(s)', 'rewardsystem'); ?>
                    &nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
            <?php echo get_woocommerce_currency_symbol(); ?> 	<input type="number" step="any" min="0" value="<?php echo get_option('rs_redeem_point_value_for_cash_back'); ?>" style="max-width:50px;" id="rs_redeem_point_value_for_cash_back" name="rs_redeem_point_value_for_cash_back"></td>
            </td>
            </tr>
            <?php
        }

        public static function refresh_button_for_expired() {
            ?>
            <tr valign="top">
                <th>
                    <label for="rs_refresh_button" style="font-size:14px;font-weight:600;"><?php _e('Update Expired Points for All Users', 'rewardsystem'); ?></label>
                </th>
                <td>
                    <input type="button" class="rs_refresh_button" value="<?php _e('Update Expired Points', 'rewardsystem'); ?>"  id="rs_refresh_button" name="rs_refresh_button"/>
                    <img class="gif_rs_refresh_button" src="<?php echo WP_PLUGIN_URL; ?>/rewardsystem/admin/images/update.gif" style="width:32px;height:32px;position:absolute"/>
                </td>
            </tr>
            <?php
        }

        public static function rs_send_ajax_to_refresh_expired_points() {
            if (isset($_GET['page'])) {
                if ($_GET['page'] == 'rewardsystem_callback') {
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery('.gif_rs_refresh_button').css('display', 'none');
                            jQuery('.rs_refresh_button').click(function () {
                                jQuery('.gif_rs_refresh_button').css('display', 'inline-block');
                                jQuery(this).attr('data-clicked', '1');
                                var dataclicked = jQuery(this).attr('data-clicked');
                                var dataparam = ({
                                    action: 'rsrefreshexpiredpoints',
                                    proceedanyway: dataclicked,
                                });
                                function getDataforDate(id) {
                                    return jQuery.ajax({
                                        type: 'POST',
                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                        data: ({
                                            action: 'rssplitrefreshexpiredpoints',
                                            ids: id,
                                            proceedanyway: dataclicked,
                                        }),
                                        success: function (response) {
                                            console.log(response);
                                        },
                                        dataType: 'json',
                                        async: false
                                    });
                                }
                                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                        function (response) {
                                            console.log(response);
                                            if (response != 'success') {
                                                var j = 1;
                                                var i, j, temparray, chunk = 10;
                                                for (i = 0, j = response.length; i < j; i += chunk) {
                                                    temparray = response.slice(i, i + chunk);
                                                    getDataforDate(temparray);
                                                }
                                                location.reload();
                                                console.log('Ajax Done Successfully');

                                            }
                                        }, 'json');
                                return false;
                            });
                        });
                    </script>
                    <?php
                }
            }
        }

        public static function rs_process_ajax_to_get_all_user_id() {
            if (isset($_POST['proceedanyway'])) {
                if ($_POST['proceedanyway'] == '1') {
                    $args = array(
                        'fields' => 'ID',
                    );
                    $get_users = get_users($args);

                    echo json_encode($get_users);
                }
            }
            exit();
        }

        public static function process_ajax_to_refresh_user_points() {
            if (isset($_POST['ids'])) {
                $userids = $_POST['ids'];
                foreach ($userids as $userid) {
                    RSPointExpiry::check_if_expiry_on_admin($userid);
                }
            }
            exit();
        }

        public static function rs_function_to_reset_general_tab() {
            $settings = RSGeneralTabSetting::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
            update_option('rs_earn_point', '1');
            update_option('rs_earn_point_value', '1');
            update_option('rs_redeem_point', '1');
            update_option('rs_redeem_point_value', '1');
            update_option('rs_redeem_point_for_cash_back', '1');
            update_option('rs_redeem_point_value_for_cash_back', '1');
        }

    }

    RSGeneralTabSetting::init();
}