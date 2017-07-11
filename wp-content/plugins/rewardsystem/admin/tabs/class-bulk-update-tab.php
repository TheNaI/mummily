<?php
/*
 * Bulk Update Tab Settings
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSUpdate')) {

    class RSUpdate {

        public static function init() {
            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_update', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_update', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));

            add_action('woocommerce_admin_field_selected_products', array(__CLASS__, 'rs_select_products_to_update'));

            add_action('woocommerce_admin_field_selected_social_products', array(__CLASS__, 'rs_select_products_to_update_social'));

            add_action('admin_head', array(__CLASS__, 'rs_add_update_chosen_reward_system'));

            add_action('woocommerce_admin_field_previous_order_button', array(__CLASS__, 'rs_apply_points_for_previous_order_button'));

            add_action('admin_head', array(__CLASS__, 'rs_send_ajax_points_to_previous_orders'));

            add_action('wp_ajax_nopriv_previousorderpoints', array(__CLASS__, 'rs_process_ajax_points_to_previous_order'));

            add_action('wp_ajax_previousorderpoints', array(__CLASS__, 'rs_process_ajax_points_to_previous_order'));

            add_action('wp_ajax_rssplitajaxoptimizationforpreviousorder', array(__CLASS__, 'process_chunk_ajax_request_for_previous_orders'));

            add_action('woocommerce_admin_field_button_social', array(__CLASS__, 'rs_save_button_for_update_social'));

            add_action('woocommerce_admin_field_button', array(__CLASS__, 'rs_save_button_for_update'));

            add_action('woocommerce_admin_field_previous_order_button_range', array(__CLASS__, 'rs_add_date_picker'));

            add_action('admin_head', array(__CLASS__, 'check_trigger_button_rewardsystem'));

            add_action('wp_ajax_nopriv_previousproductvalue', array(__CLASS__, 'get_ajax_request_for_previous_product'));

            add_action('wp_ajax_previousproductvalue', array(__CLASS__, 'get_ajax_request_for_previous_product'));

            add_action('woocommerce_admin_field_button_point_price', array(__CLASS__, 'rs_save_button_for_update_point_price'));

            add_action('woocommerce_admin_field_selected_products_point', array(__CLASS__, 'rs_select_products_to_update_point_price'));

            add_action('wp_ajax_nopriv_previoussocialproductvalue', array(__CLASS__, 'get_ajax_request_for_previous_social_product'));

            add_action('wp_ajax_previoussocialproductvalue', array(__CLASS__, 'get_ajax_request_for_previous_social_product'));

            add_action('wp_ajax_rssplitajaxoptimization', array(__CLASS__, 'process_chunk_ajax_request_in_rewardsystem'));

            add_action('wp_ajax_rssplitajaxoptimizationsocial', array(__CLASS__, 'process_chunk_ajax_request_in_social_rewardsystem'));

            add_action('wp_ajax_previousproductpointpricevalue', array(__CLASS__, 'get_ajax_request_for_previous_product_point_price'));

            add_action('wp_ajax_nopriv_previousproductpointpricevalue', array(__CLASS__, 'get_ajax_request_for_previous_product_point_price'));

            add_action('wp_ajax_rssplitajaxoptimizationforpointprice', array(__CLASS__, 'process_chunk_ajax_request_in_rewardsystem_point_price'));

            add_action('fp_action_to_reset_settings_rewardsystem_update', array(__CLASS__, 'rs_function_to_reset_update_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_update'] = __('Bulk Update', 'rewardsystem');
            return $setting_tabs;
        }

        public static function reward_system_admin_fields() {
            global $woocommerce;
            $getproductlist = array();
            $categorylist = array();
            $socialproductlists = array();
            $socialproductids = array();
            $socialproducttitles = array();
            $getproductids = array();
            $getproducttitles = array();
            $categoryname = array();
            $categoryid = array();
            $ajaxproductsearch = array();
            $rsproductids = array();
            $rsproduct_name = array();
            $ajaxproductsearchsocial = array();
            $rsproductidssocial = array();
            $rsproduct_namesocial = array();

            if (isset($_GET['tab'])) {
                if ($_GET['tab'] == 'rewardsystem_update') {
                    $categorylist = fp_rs_get_product_category();
                }
            }
            return apply_filters('woocommerce_rewardsystem_update_settings', array(
                array(
                    'name' => __('Bulk Update Settings for Existing Products/Existing Categories', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_update_setting',
                ),
                array(
                    'name' => __('Product/Category Selection', 'rewardsystem'),
                    'id' => 'rs_which_product_selection',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'class' => 'rs_which_product_selection',
                    'default' => '1',
                    'newids' => 'rs_which_product_selection',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('All Products', 'rewardsystem'),
                        '2' => __('Selected Products', 'rewardsystem'),
                        '3' => __('All Categories', 'rewardsystem'),
                        '4' => __('Selected Categories', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Selected Particular Products', 'rewardsystem'),
                    'type' => 'selected_products',
                    'id' => 'rs_select_particular_products',
                    'class' => 'rs_select_particular_products',
                    'newids' => 'rs_select_particular_products',
                ),
                array(
                    'name' => __('Select Particular Categories', 'rewardsystem'),
                    'id' => 'rs_select_particular_categories',
                    'css' => 'min-width:350px;',
                    'std' => '1',
                    'class' => 'rs_select_particular_categories',
                    'default' => '1',
                    'newids' => 'rs_select_particular_categories',
                    'type' => 'multiselect',
                    'options' => $categorylist,
                ),
                array(
                    'name' => __('Enable SUMO Reward Points', 'rewardsystem'),
                    'id' => 'rs_local_enable_disable_reward',
                    'css' => 'min-width:150px;',
                    'std' => '2',
                    'default' => '2',
                    'desc_tip' => true,
                    'desc' => __('Enable will Turn On Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
                            . 'Disable will Turn Off Reward Points for Product Purchase and Category/Product Settings will be considered if it is available.', 'rewardsystem'),
                    'newids' => 'rs_local_enable_disable_reward',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Reward Type', 'rewardsystem'),
                    'id' => 'rs_local_reward_type',
                    'class' => 'show_if_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_local_reward_type',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Reward Points', 'rewardsystem'),
                    'desc' => __('Please Enter Reward Points', 'rewardsystem'),
                    'id' => 'rs_local_reward_points',
                    'class' => 'show_if_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_points',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Reward Points in Percent %', 'rewardsystem'),
                    'desc' => __('Please Enter Percentage value of Reward Points', 'rewardsystem'),
                    'id' => 'rs_local_reward_percent',
                    'class' => 'show_if_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_percent',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Referral Reward Type', 'rewardsystem'),
                    'id' => 'rs_local_referral_reward_type',
                    'class' => 'show_if_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_local_referral_reward_type',
                    'type' => 'select',
                    'desc_tip' => true,
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Referral Reward Points', 'rewardsystem'),
                    'desc' => __('Please Enter Referral Reward Points', 'rewardsystem'),
                    'id' => 'rs_local_referral_reward_point',
                    'class' => 'show_if_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_referral_reward_point',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Referral Reward Points in Percent %', 'rewardsystem'),
                    'desc' => __('Please Enter Percentage value of Reward Points', 'rewardsystem'),
                    'id' => 'rs_local_referral_reward_percent',
                    'class' => 'show_if_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_referral_reward_percent',
                    'placeholder' => '',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Getting Referred Reward Type', 'rewardsystem'),
                    'id' => 'rs_local_referral_reward_type_get_refer',
                    'class' => 'show_if_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_local_referral_reward_type_get_refer',
                    'type' => 'select',
                    'desc_tip' => true,
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Referral Reward Points for Getting Referred', 'rewardsystem'),
                    'desc' => __('Please Enter Referral Reward Points for getting referred', 'rewardsystem'),
                    'id' => 'rs_local_referral_reward_point_for_getting_referred',
                    'class' => 'show_if_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_referral_reward_point_for_getting_referred',
                    'placeholder' => '',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Referral Reward Points in Percent % for Getting Referred', 'rewardsystem'),
                    'desc' => __('Please Enter Percentage value of Reward Points for getting referred', 'rewardsystem'),
                    'id' => 'rs_local_referral_reward_percent_for_getting_referred',
                    'class' => 'show_if_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_referral_reward_percent_for_getting_referred',
                    'placeholder' => '',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Test Button', 'rewardsystem'),
                    'desc' => __('This is for testing button', 'rewardsystem'),
                    'id' => 'rs_sumo_reward_button',
                    'std' => '',
                    'default' => '',
                    'type' => 'button',
                    'desc_tip' => true,
                    'newids' => 'rs_sumo_reward_button',
                ),
                array('type' => 'sectionend', 'id' => 'rs_update_setting'),
                array(
                    'name' => __('Update Point Price Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_update_point_priceing'
                ),
                array(
                    'name' => __('Product/Category Selection', 'rewardsystem'),
                    'id' => 'rs_which_point_precing_product_selection',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'class' => 'rs_which_point_precing_product_selection',
                    'default' => '1',
                    'newids' => 'rs_which_point_precing_product_selection',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('All Products', 'rewardsystem'),
                        '2' => __('Selected Products', 'rewardsystem'),
                        '3' => __('All Categories', 'rewardsystem'),
                        '4' => __('Selected Categories', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'type' => 'selected_products_point',
                ),
                array(
                    'name' => __('Select Particular Categories', 'rewardsystem'),
                    'id' => 'rs_select_particular_categories_for_point_price',
                    'css' => 'min-width:350px;',
                    'std' => '1',
                    'class' => 'rs_select_particular_categories_for_point_price',
                    'default' => '1',
                    'newids' => 'rs_select_particular_categories_for_point_price',
                    'type' => 'multiselect',
                    'options' => $categorylist,
                ),
                array(
                    'name' => __('Enable Points Prices', 'rewardsystem'),
                    'id' => 'rs_local_enable_disable_point_price',
                    'css' => 'min-width:150px;',
                    'std' => '2',
                    'default' => '2',
                    'desc_tip' => true,
                    'desc' => __('Enable will Turn On Points Price for Product Purchase and Product Settings will be considered if it is available. '
                            . 'Disable will Turn Off Points Price for Product Purchase and Product Settings will be considered if it is available.', 'rewardsystem'),
                    'newids' => 'rs_local_enable_disable_point_price',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Pricing Type', 'rewardsystem'),
                    'id' => 'rs_local_point_pricing_type',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'desc' => __('Enable will Turn On Points Price for Product Purchase and Product Settings will be considered if it is available. '
                            . 'Disable will Turn Off Points Price for Product Purchase and Product Settings will be considered if it is available.', 'rewardsystem'),
                    'newids' => 'rs_local_point_pricing_type',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Currency & Point Price', 'rewardsystem'),
                        '2' => __('Only Point Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Points Prices Type ', 'rewardsystem'),
                    'id' => 'rs_local_point_price_type',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'desc' => __('Enable will Turn On Points Price for Product Purchase and Product Settings will be considered if it is available. '
                            . 'Disable will Turn Off Points Price for Product Purchase and Product Settings will be considered if it is available.', 'rewardsystem'),
                    'newids' => 'rs_local_point_price_type',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By Fixed', 'rewardsystem'),
                        '2' => __('Based On Conversion', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('By Fixed Points', 'rewardsystem'),
                    'desc' => __('Please Enter Price Points', 'rewardsystem'),
                    'id' => 'rs_local_price_points',
                    'class' => 'show_if_price_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_price_points',
                    'placeholder' => '',
                    'desc' => __('When left empty, Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Test Button', 'rewardsystem'),
                    'desc' => __('This is for testing button', 'rewardsystem'),
                    'id' => 'rs_sumo_point_price_button',
                    'std' => '',
                    'default' => '',
                    'type' => 'button_point_price',
                    'desc_tip' => true,
                    'newids' => 'rs_sumo_point_price_button',
                ),
                array('type' => 'sectionend', 'id' => 'rs_update_setting'),
                array(
                    'name' => __('Bulk Update Social Settings for Existing Products/Existing Categories', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_update_social_settings'
                ),
                array(
                    'name' => __('Product/Category Selection', 'rewardsystem'),
                    'id' => 'rs_which_social_product_selection',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'class' => 'rs_which_social_product_selection',
                    'default' => '1',
                    'newids' => 'rs_which_social_product_selection',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('All Products', 'rewardsystem'),
                        '2' => __('Selected Products', 'rewardsystem'),
                        '3' => __('All Categories', 'rewardsystem'),
                        '4' => __('Selected Categories', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Selected Particular Products', 'rewardsystem'),
                    'type' => 'selected_social_products',
                    'id' => 'rs_select_particular_social_products',
                    'class' => 'rs_select_particular_social_categories',
                    'newids' => 'rs_select_particular_social_products',
                ),
                array(
                    'name' => __('Select Particular Categories', 'rewardsystem'),
                    'id' => 'rs_select_particular_social_categories',
                    'css' => 'min-width:350px;',
                    'std' => '1',
                    'class' => 'rs_select_particular_social_categories',
                    'default' => '1',
                    'newids' => 'rs_select_particular_social_categories',
                    'type' => 'multiselect',
                    'options' => $categorylist,
                ),
                array(
                    'name' => __('Enable SUMO Reward Points for Social Promotion', 'rewardsystem'),
                    'id' => 'rs_local_enable_disable_social_reward',
                    'css' => 'min-width:150px;',
                    'std' => '2',
                    'default' => '2',
                    'desc_tip' => true,
                    'desc' => __('Enable will Turn On Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
                            . 'Disable will Turn Off Reward Points for Product Purchase and Category/Product Settings will be considered if it is available.', 'rewardsystem'),
                    'newids' => 'rs_local_enable_disable_social_reward',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Facebook Like Reward Type', 'rewardsystem'),
                    'id' => 'rs_local_reward_type_for_facebook',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_local_reward_type_for_facebook',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Facebook Like Reward Points', 'rewardsystem'),
                    'desc' => __('Please Enter Reward Points for Facebook', 'rewardsystem'),
                    'id' => 'rs_local_reward_points_facebook',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_points_facebook',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Facebook Like Reward Points in Percent %', 'rewardsystem'),
                    'desc' => __('Please Enter Percentage value of Reward Points', 'rewardsystem'),
                    'id' => 'rs_local_reward_percent_facebook',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_percent_facebook',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Facebook Share Reward Type', 'rewardsystem'),
                    'id' => 'rs_local_reward_type_for_facebook_share',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_local_reward_type_for_facebook_share',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Facebook Share Reward Points', 'rewardsystem'),
                    'desc' => __('Please Enter Reward Points for Facebook Share', 'rewardsystem'),
                    'id' => 'rs_local_reward_points_facebook_share',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_points_facebook_share',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Facebook Share Reward Points in Percent %', 'rewardsystem'),
                    'desc' => __('Please Enter Percentage value of Reward Points', 'rewardsystem'),
                    'id' => 'rs_local_reward_percent_facebook_share',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_percent_facebook_share',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Twitter Tweet Reward Type', 'rewardsystem'),
                    'id' => 'rs_local_reward_type_for_twitter',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_local_reward_type_for_twitter',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Twitter Tweet Reward Points', 'rewardsystem'),
                    'desc' => __('Please Enter Reward Points for Twitter', 'rewardsystem'),
                    'id' => 'rs_local_reward_points_twitter',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_points_twitter',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Twitter Tweet Reward Points in Percent %', 'rewardsystem'),
                    'desc' => __('Please Enter Percentage value of Reward Points for Twitter', 'rewardsystem'),
                    'id' => 'rs_local_reward_percent_twitter',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_percent_twitter',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Twitter Follow Reward Type', 'rewardsystem'),
                    'id' => 'rs_local_reward_type_for_twitter_follow',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_local_reward_type_for_twitter_follow',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Twitter Follow Reward Points', 'rewardsystem'),
                    'desc' => __('Please Enter Reward Points for Twitter', 'rewardsystem'),
                    'id' => 'rs_local_reward_points_twitter_follow',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_points_twitter_follow',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Twitter Follow Reward Points in Percent %', 'rewardsystem'),
                    'desc' => __('Please Enter Percentage value of Reward Points for Twitter', 'rewardsystem'),
                    'id' => 'rs_local_reward_percent_twitter_follow',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_percent_twitter_follow',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Google+1 Reward Type', 'rewardsystem'),
                    'id' => 'rs_local_reward_type_for_google',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_local_reward_type_for_google',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Google+1 Reward Points', 'rewardsystem'),
                    'desc' => __('Please Enter Reward Points for Google+', 'rewardsystem'),
                    'id' => 'rs_local_reward_points_google',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_points_google',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Google+1 Reward Points in Percent %', 'rewardsystem'),
                    'desc' => __('Please Enter Percentage value of Reward Points for Google+', 'rewardsystem'),
                    'id' => 'rs_local_reward_percent_google',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_percent_google',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('VK.com Like Reward Type', 'rewardsystem'),
                    'id' => 'rs_local_reward_type_for_vk',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_local_reward_type_for_vk',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('VK.com Like Reward Points', 'rewardsystem'),
                    'desc' => __('Please Enter Reward Points for VK', 'rewardsystem'),
                    'id' => 'rs_local_reward_points_vk',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_points_vk',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('VK.com Like Reward Points in Percent %', 'rewardsystem'),
                    'desc' => __('Please Enter Percentage value of Reward Points for VK', 'rewardsystem'),
                    'id' => 'rs_local_reward_percent_vk',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_percent_vk',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Instagram Reward Type', 'rewardsystem'),
                    'id' => 'rs_local_reward_type_for_instagram',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_local_reward_type_for_instagram',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Instagram Reward Points', 'rewardsystem'),
                    'desc' => __('Please Enter Reward Points for Instagram', 'rewardsystem'),
                    'id' => 'rs_local_reward_points_instagram',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_points_instagram',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Instagram Reward Points in Percent %', 'rewardsystem'),
                    'desc' => __('Please Enter Percentage value of Reward Points for Instagram', 'rewardsystem'),
                    'id' => 'rs_local_reward_percent_instagram',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_percent_instagram',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('OK.ru Share Reward Type', 'rewardsystem'),
                    'id' => 'rs_local_reward_type_for_ok_follow',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_local_reward_type_for_ok_follow',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                        '2' => __('By Percentage of Product Price', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('OK.ru Share Reward Points', 'rewardsystem'),
                    'desc' => __('Please Enter Reward Points for Ok.ru', 'rewardsystem'),
                    'id' => 'rs_local_reward_points_ok_follow',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_points_ok_follow',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('OK.ru Share Reward Points in Percent %', 'rewardsystem'),
                    'desc' => __('Please Enter Percentage value of Reward Points for OK.ru', 'rewardsystem'),
                    'id' => 'rs_local_reward_percent_ok_follow',
                    'class' => 'show_if_social_enable_in_update',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_local_reward_percent_ok_follow',
                    'desc' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                            . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Test Button', 'rewardsystem'),
                    'desc' => __('This is for testing button', 'rewardsystem'),
                    'id' => 'rs_sumo_reward_button',
                    'std' => '',
                    'type' => 'button_social',
                    'desc_tip' => true,
                    'newids' => 'rs_sumo_reward_button',
                ),
                array('type' => 'sectionend', 'id' => '_rs_update_redeem_settings'),
                array(
                    'name' => __('Apply Reward Points for Previous Orders', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_apply_reward_points',
                ),
                array(
                    'name' => __('Award Points for', 'rewardsystem'),
                    'id' => 'rs_sumo_select_order_range',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'options' => array(
                        '1' => 'Any Old Orders',
                        '2' => 'Orders Placed Between Specific Date Range'
                    ),
                    'newids' => 'rs_sumo_select_order_range',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Test Button', 'rewardsystem'),
                    'desc' => __("This is for Previous Order Reward Points", 'rewardsystem'),
                    'id' => 'rs_sumo_reward_previous_order_points',
                    'std' => '',
                    'type' => 'previous_order_button_range',
                    'desc_tip' => true,
                    'newids' => 'rs_sumo_reward_previous_order_points',
                ),
                array(
                    'name' => __('Test Button', 'rewardsystem'),
                    'desc' => __("This is for Previous Order Reward Points", 'rewardsystem'),
                    'id' => 'rs_sumo_reward_previous_order_points',
                    'std' => '',
                    'type' => 'previous_order_button',
                    'desc_tip' => true,
                    'newids' => 'rs_sumo_reward_previous_order_points',
                ),
                array('type' => 'sectionend', 'id' => '_rs_update_social_settings'),
                array('type' => 'sectionend', 'id' => 'rs_update_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields(RSUpdate::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSUpdate::reward_system_admin_fields());
            update_option('rs_select_particular_social_products', $_POST['rs_select_particular_social_products']);
            update_option('rs_select_particular_products', $_POST['rs_select_particular_products']);
            update_option('rs_select_particular_products_for_point_price', $_POST['rs_select_particular_products_for_point_price']);
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSUpdate::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function rs_select_products_to_update() {
            $field_id = "rs_select_particular_products";
            $field_label = "Select Particular Products";
            $getproducts = get_option('rs_select_particular_products');
            echo rs_function_to_add_field_for_product_select($field_id, $field_label, $getproducts);
        }

        public static function rs_select_products_to_update_point_price() {
            $field_id = "rs_select_particular_products_for_point_price";
            $field_label = "Select Particular Products";
            $getproducts = get_option('rs_select_particular_products_for_point_price');
            echo rs_function_to_add_field_for_product_select($field_id, $field_label, $getproducts);
        }

        public static function rs_select_products_to_update_social($value) {
            $field_id = "rs_select_particular_social_products";
            $field_label = "Select Particular Products";
            $getproducts = get_option('rs_select_particular_social_products');
            echo rs_function_to_add_field_for_product_select($field_id, $field_label, $getproducts);
        }

        public static function rs_add_update_chosen_reward_system() {
            global $woocommerce;
            if (isset($_GET['page'])) {
                if ($_GET['page'] == 'rewardsystem_callback') {
                    if (isset($_GET['tab'])) {
                        if ($_GET['tab'] == 'rewardsystem_update') {
                            echo rs_common_ajax_function_to_select_products('rs_select_particular_products');
                            echo rs_common_ajax_function_to_select_products('rs_select_particular_social_products');
                            echo rs_common_ajax_function_to_select_products('rs_select_particular_products_for_point_price');
                            if ((float) $woocommerce->version <= (float) ('2.2.0')) {
                                echo rs_common_chosen_function('#rs_select_particular_categories_for_point_price');
                                echo rs_common_chosen_function('#rs_select_particular_products');
                                echo rs_common_chosen_function('#rs_select_particular_social_products');
                                echo rs_common_chosen_function('#rs_select_particular_categories');
                                echo rs_common_chosen_function('#rs_select_particular_social_categories');
                            } else {
                                echo rs_common_select_function('#rs_select_particular_categories_for_point_price');
                                echo rs_common_select_function('#rs_select_particular_categories');
                                echo rs_common_select_function('#rs_select_particular_social_categories');
                            }
                        }
                    }
                }
            }
        }

        public static function check_trigger_button_rewardsystem() {
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function () {
                    jQuery('.rs_sumo_point_price_button').click(function () {
                        jQuery('.gif_rs_sumo_point_price_button').css('display', 'inline-block');
                        var whichproduct = jQuery('#rs_which_point_precing_product_selection').val();
                        var enabledisablepoints = jQuery('#rs_local_enable_disable_point_price').val();
                        var pointpricetype = jQuery('#rs_local_point_price_type').val();
                        var selectparticularproducts = jQuery('#rs_select_particular_products_for_point_price').val();                        
                        var pricepoints = jQuery('#rs_local_price_points').val();
                        var selectedcategories = jQuery('#rs_select_particular_categories_for_point_price').val();
                        var pointpricingtype = jQuery('#rs_local_point_pricing_type').val();
                        jQuery(this).attr('data-clicked', '1');
                        var dataclicked = jQuery(this).attr('data-clicked');
                        var dataparam = ({
                            action: 'previousproductpointpricevalue',
                            proceedanyway: dataclicked,
                            whichproduct: whichproduct,
                            enabledisablepoints: enabledisablepoints,
                            pointpricetype: pointpricetype,
                            selectedproducts: selectparticularproducts,
                            pricepoints: pricepoints,
                            selectedcategories: selectedcategories,
                            pointpricingtype: pointpricingtype,
                        });

                        function getDatapointprice(id) {
                            return jQuery.ajax({
                                type: 'POST',
                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                data: ({action: 'rssplitajaxoptimizationforpointprice',
                                    ids: id,
                                    enabledisablepoints: enabledisablepoints,
                                    selectedproducts: selectparticularproducts,
                                    pointpricetype: pointpricetype,
                                    pricepoints: pricepoints,
                                    pointpricetype: pointpricetype,
                                    selectedcategories: selectedcategories,
                                    pointpricingtype: pointpricingtype,
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
                                    if (response !== 'success') {
                                        var j = 1;
                                        var i, j, temparray, chunk = 10;
                                        for (i = 0, j = response.length; i < j; i += chunk) {
                                            temparray = response.slice(i, i + chunk);
                                            getDatapointprice(temparray);
                                        }
                                        jQuery.when(getDatapointprice()).done(function (a1) {
                                            console.log('Ajax Done Successfully');
                                            jQuery('.submit .button-primary').trigger('click');
                                        });
                                    } else {
                                        var newresponse = response.replace(/\s/g, '');
                                        if (newresponse === 'success') {
                                            jQuery('.submit .button-primary').trigger('click');
                                        }
                                    }
                                }, 'json');
                        return false;

                    });


                    jQuery('.rs_sumo_reward_button').click(function () {
                        jQuery('.gif_rs_sumo_reward_button').css('display', 'inline-block');
                        var whichproduct = jQuery('#rs_which_product_selection').val();
                        var enabledisablereward = jQuery('#rs_local_enable_disable_reward').val();
                        var selectparticularproducts = jQuery('#rs_select_particular_products').val();                        
                        var selectedcategories = jQuery('#rs_select_particular_categories').val();
                        var rewardtype = jQuery('#rs_local_reward_type').val();
                        var rewardpoints = jQuery('#rs_local_reward_points').val();
                        var rewardpercent = jQuery('#rs_local_reward_percent').val();
                        var referralrewardtype = jQuery('#rs_local_referral_reward_type').val();
                        var referralrewardpoint = jQuery('#rs_local_referral_reward_point').val();
                        var referralrewardpercent = jQuery('#rs_local_referral_reward_percent').val();
                        var referralrewardtyperefer = jQuery('#rs_local_referral_reward_type_get_refer').val();
                        var referralpointforgettingrefer = jQuery('#rs_local_referral_reward_point_for_getting_referred').val();
                        var referralrewardpercentgettingrefer = jQuery('#rs_local_referral_reward_percent_for_getting_referred').val();

                        jQuery(this).attr('data-clicked', '1');
                        var dataclicked = jQuery(this).attr('data-clicked');
                        var dataparam = ({
                            action: 'previousproductvalue',
                            proceedanyway: dataclicked,
                            whichproduct: whichproduct,
                            enabledisablereward: enabledisablereward,
                            selectedproducts: selectparticularproducts,
                            selectedcategories: selectedcategories,
                            rewardtype: rewardtype,
                            rewardpoints: rewardpoints,
                            rewardpercent: rewardpercent,
                            referralrewardtype: referralrewardtype,
                            referralrewardpoint: referralrewardpoint,
                            referralrewardpercent: referralrewardpercent,
                            referralrewardtyperefer: referralrewardtyperefer,
                            referralpointforgettingrefer: referralpointforgettingrefer,
                            referralrewardpercentgettingrefer: referralrewardpercentgettingrefer,
                        });
                        function getData(id) {
                            return jQuery.ajax({
                                type: 'POST',
                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                data: ({action: 'rssplitajaxoptimization',
                                    ids: id,
                                    enabledisablereward: enabledisablereward,
                                    selectedproducts: selectparticularproducts,
                                    selectedcategories: selectedcategories,
                                    rewardtype: rewardtype,
                                    rewardpoints: rewardpoints,
                                    rewardpercent: rewardpercent,
                                    referralrewardtype: referralrewardtype,
                                    referralrewardpoint: referralrewardpoint,
                                    referralrewardpercent: referralrewardpercent,
                                    referralrewardtyperefer: referralrewardtyperefer,
                                    referralpointforgettingrefer: referralpointforgettingrefer,
                                    referralrewardpercentgettingrefer: referralrewardpercentgettingrefer,
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
                                    if (response !== 'success') {
                                        var j = 1;
                                        var i, j, temparray, chunk = 10;
                                        for (i = 0, j = response.length; i < j; i += chunk) {
                                            temparray = response.slice(i, i + chunk);
                                            getData(temparray);
                                        }
                                        jQuery.when(getData()).done(function (a1) {
                                            console.log('Ajax Done Successfully');
                                            jQuery('.submit .button-primary').trigger('click');
                                        });
                                    } else {
                                        var newresponse = response.replace(/\s/g, '');
                                        if (newresponse === 'success') {
                                            jQuery('.submit .button-primary').trigger('click');
                                        }
                                    }
                                }, 'json');
                        return false;
                    });
                    jQuery('.rs_sumo_reward_button_social').click(function () {
                        jQuery('.gif_rs_sumo_reward_button_social').css('display', 'inline-block');
                        var whichproduct = jQuery('#rs_which_social_product_selection').val();
                        var enabledisablereward = jQuery('#rs_local_enable_disable_social_reward').val();
                        var selectparticularproducts = jQuery('#rs_select_particular_social_products').val();
                        var selectedcategories = jQuery('#rs_select_particular_social_categories').val();
                        var rewardtypefacebook = jQuery('#rs_local_reward_type_for_facebook').val();
                        var facebookrewardpoints = jQuery('#rs_local_reward_points_facebook').val();
                        var facebookrewardpercent = jQuery('#rs_local_reward_percent_facebook').val();
                        var rewardtypefacebook_share = jQuery('#rs_local_reward_type_for_facebook_share').val();
                        var facebookrewardpoints_share = jQuery('#rs_local_reward_points_facebook_share').val();
                        var facebookrewardpercent_share = jQuery('#rs_local_reward_percent_facebook_share').val();
                        var rewardtypetwitter = jQuery('#rs_local_reward_type_for_twitter').val();
                        var twitterrewardpoints = jQuery('#rs_local_reward_points_twitter').val();
                        var twitterrewardpercent = jQuery('#rs_local_reward_percent_twitter').val();
                        var rewardtypegoogle = jQuery('#rs_local_reward_type_for_google').val();
                        var googlerewardpoints = jQuery('#rs_local_reward_points_google').val();
                        var googlerewardpercent = jQuery('#rs_local_reward_percent_google').val();
                        var rewardtypevk = jQuery('#rs_local_reward_type_for_vk').val();
                        var vkrewardpoints = jQuery('#rs_local_reward_points_vk').val();
                        var vkrewardpercent = jQuery('#rs_local_reward_percent_vk').val();
                        var rewardtypetwitter_follow = jQuery('#rs_local_reward_type_for_twitter_follow').val();
                        var twitterrewardpoints_follow = jQuery('#rs_local_reward_points_twitter_follow').val();
                        var twitterrewardpercent_follow = jQuery('#rs_local_reward_percent_twitter_follow').val();
                        var rewardtypeinstagram = jQuery('#rs_local_reward_type_for_instagram').val();
                        var instagramrewardpoints = jQuery('#rs_local_reward_points_instagram').val();
                        var instagramrewardpercent = jQuery('#rs_local_reward_percent_instagram').val();
                        var rewardtypeok_follow = jQuery('#rs_local_reward_type_for_ok_follow').val();
                        var okrewardpoints_follow = jQuery('#rs_local_reward_points_ok_follow').val();
                        var okrewardpercent_follow = jQuery('#rs_local_reward_percent_ok_follow').val();
                        jQuery(this).attr('data-clicked', '1');
                        var dataclicked = jQuery(this).attr('data-clicked');
                        var dataparam = ({
                            action: 'previoussocialproductvalue',
                            proceedanyway: dataclicked,
                            whichproduct: whichproduct,
                            enabledisablereward: enabledisablereward,
                            selectedproducts: selectparticularproducts,
                            selectedcategories: selectedcategories,
                            rewardtypefacebook: rewardtypefacebook,
                            facebookrewardpoints: facebookrewardpoints,
                            facebookrewardpercent: facebookrewardpercent,
                            rewardtypefacebook_share: rewardtypefacebook_share,
                            facebookrewardpoints_share: facebookrewardpoints_share,
                            facebookrewardpercent_share: facebookrewardpercent_share,
                            rewardtypetwitter: rewardtypetwitter,
                            twitterrewardpoints: twitterrewardpoints,
                            twitterrewardpercent: twitterrewardpercent,
                            rewardtypegoogle: rewardtypegoogle,
                            googlerewardpoints: googlerewardpoints,
                            googlerewardpercent: googlerewardpercent,
                            rewardtypevk: rewardtypevk,
                            vkrewardpoints: vkrewardpoints,
                            vkrewardpercent: vkrewardpercent,
                            rewardtypetwitter_follow: rewardtypetwitter_follow,
                            twitterrewardpoints_follow: twitterrewardpoints_follow,
                            twitterrewardpercent_follow: twitterrewardpercent_follow,
                            rewardtypeinstagram: rewardtypeinstagram,
                            instagramrewardpoints: instagramrewardpoints,
                            instagramrewardpercent: instagramrewardpercent,
                            rewardtypeok_follow: rewardtypeok_follow,
                            okrewardpoints_follow: okrewardpoints_follow,
                            okrewardpercent_follow: okrewardpercent_follow,
                        });
                        function getDataSocial(id) {
                            return jQuery.ajax({
                                type: 'POST',
                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                data: ({action: 'rssplitajaxoptimizationsocial', ids: id, enabledisablereward: enabledisablereward,
                                    selectedproducts: selectparticularproducts,
                                    selectedcategories: selectedcategories,
                                    rewardtypefacebook: rewardtypefacebook,
                                    facebookrewardpoints: facebookrewardpoints,
                                    facebookrewardpercent: facebookrewardpercent,
                                    rewardtypefacebook_share: rewardtypefacebook_share,
                                    facebookrewardpoints_share: facebookrewardpoints_share,
                                    facebookrewardpercent_share: facebookrewardpercent_share,
                                    rewardtypetwitter: rewardtypetwitter,
                                    twitterrewardpoints: twitterrewardpoints,
                                    twitterrewardpercent: twitterrewardpercent,
                                    rewardtypegoogle: rewardtypegoogle,
                                    googlerewardpoints: googlerewardpoints,
                                    googlerewardpercent: googlerewardpercent,
                                    rewardtypevk: rewardtypevk,
                                    vkrewardpoints: vkrewardpoints,
                                    vkrewardpercent: vkrewardpercent,
                                    rewardtypetwitter_follow: rewardtypetwitter_follow,
                                    twitterrewardpoints_follow: twitterrewardpoints_follow,
                                    twitterrewardpercent_follow: twitterrewardpercent_follow,
                                    rewardtypeinstagram: rewardtypeinstagram,
                                    instagramrewardpoints: instagramrewardpoints,
                                    instagramrewardpercent: instagramrewardpercent,
                                    rewardtypeok_follow: rewardtypeok_follow,
                                    okrewardpoints_follow: okrewardpoints_follow,
                                    okrewardpercent_follow: okrewardpercent_follow,
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
                                    if (response !== 'success') {
                                        var j = 1;
                                        var i, j, temparray, chunk = 10;
                                        for (i = 0, j = response.length; i < j; i += chunk) {
                                            temparray = response.slice(i, i + chunk);
                                            getDataSocial(temparray);
                                        }
                                        jQuery.when(getDataSocial()).done(function (a1) {
                                            console.log('Ajax Done Successfully');
                                            jQuery('.submit .button-primary').trigger('click');
                                        });
                                    } else {
                                        var newresponse = response.replace(/\s/g, '');
                                        if (newresponse === 'success') {
                                            jQuery('.submit .button-primary').trigger('click');
                                        }
                                    }
                                }, 'json');
                        return false;
                    });
                    jQuery('.rs_sumo_undo_reward').click(function () {
                        jQuery(this).attr('data-clicked', '0');
                        var dataclicked = jQuery(this).attr('data-clicked');
                        var dataparam = ({
                            action: 'previousproductvalue',
                            proceedanyway: dataclicked,
                        });
                        jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                function (response) {
                                    var newresponse = response.replace(/\s/g, '');
                                    if (newresponse === 'success') {
                                        jQuery('.rs_sumo_rewards').fadeIn();
                                        jQuery('.rs_sumo_rewards').html('Successfully Disabled from Existing Products');
                                        jQuery('.rs_sumo_rewards').fadeOut(5000);
                                    }
                                });
                        return false;
                    });
                });
            </script>
            <?php
        }

        public static function rs_save_button_for_update_point_price() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">                    
                </th>
                <td class="forminp forminp-select">
                    <input type="submit" class="rs_sumo_point_price_button button-primary" value="Save and Update"/>
                    <img class="gif_rs_sumo_point_price_button" src="<?php echo WP_PLUGIN_URL; ?>/rewardsystem/admin/images/update.gif" style="width:32px;height:32px;position:absolute"/>          
                    <div class='rs_sumo_point_price_button' style='margin-bottom:10px; margin-top:10px; color:green;'></div>
                </td>
            </tr>
            <?php
        }

        public static function rs_save_button_for_update() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">                    
                </th>
                <td class="forminp forminp-select">
                    <input type="submit" class="rs_sumo_reward_button button-primary" value="Save and Update"/>
                    <img class="gif_rs_sumo_reward_button" src="<?php echo WP_PLUGIN_URL; ?>/rewardsystem/admin/images/update.gif" style="width:32px;height:32px;position:absolute"/>         
                    <div class='rs_sumo_rewards' style='margin-bottom:10px; margin-top:10px; color:green;'></div>
                </td>
            </tr>
            <?php
        }

        public static function rs_save_button_for_update_social() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">                    
                </th>
                <td class="forminp forminp-select">
                    <input type="submit" class="rs_sumo_reward_button_social button-primary" value="Save and Update"/>
                    <img class="gif_rs_sumo_reward_button_social" src="<?php echo WP_PLUGIN_URL; ?>/rewardsystem/admin/images/update.gif" style="width:32px;height:32px;position:absolute"/>          
                    <div class='rs_sumo_rewards_social' style='margin-bottom:10px; margin-top:10px; color:green;'></div>
                </td>
            </tr>
            <?php
        }

        public static function rs_apply_points_for_previous_order_button() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_sumo_rewards_for_previous_order_label"><?php _e('Apply Reward Points to Previous Orders', 'rewardsystem'); ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="submit" class="rs_sumo_rewards_for_previous_order button-primary" value="Apply Points for Previous Orders"/>
                    <img class="gif_rs_sumo_reward_button_for_previous_order" src="<?php echo WP_PLUGIN_URL; ?>/rewardsystem/admin/images/update.gif" style="width:32px;height:32px;position:absolute"/>
                    <div class="rs_sumo_rewards_previous_order" style="margin-bottom:10px;margin-top:10px; color:green;"></div>
                </td>
            </tr>
            <?php
        }

        public static function rs_add_date_picker() {
            ?>
            <script type="text/javascript">
                jQuery(function () {
                    jQuery("#rs_from_date").datepicker({
                        defaultDate: "+1w",
                        changeMonth: true,
                        dateFormat: 'yy-mm-dd',
                        numberOfMonths: 1,
                        onClose: function (selectedDate) {
                            jQuery("#to").datepicker("option", "minDate", selectedDate);
                        }
                    });
                    jQuery('#rs_from_date').datepicker('setDate', '-1');
                    jQuery("#rs_to_date").datepicker({
                        defaultDate: "+1w",
                        changeMonth: true,
                        dateFormat: 'yy-mm-dd',
                        numberOfMonths: 1,
                        onClose: function (selectedDate) {
                            jQuery("#from").datepicker("option", "maxDate", selectedDate);
                        }

                    });
                    jQuery("#rs_to_date").datepicker('setDate', new Date());
                });
            </script>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_sumo_rewards_for_selecting_particular_date"><?php _e('Select from Specific Date', 'rewardsystem'); ?></label>
                </th>
                <td class="forminp forminp-select">
                    From <input type="text" id="rs_from_date" value=""/> To <input type="text" id="rs_to_date" value=""/>
                </td>
            </tr>
            <?php
        }

        public function get_ajax_request_for_previous_product_point_price() {
            global $woocommerce;
            global $post;
            if (isset($_POST['proceedanyway'])) {
                if ($_POST['proceedanyway'] == '1') {
                    if ($_POST['whichproduct'] == '1') {
                        $args = array('post_type' => 'product', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', 'cache_results' => false);
                        $products = get_posts($args);
                        echo json_encode($products);
                    } elseif ($_POST['whichproduct'] == '2') {
                        if (!is_array($_POST['selectedproducts'])) {
                            $_POST['selectedproducts'] = explode(',', $_POST['selectedproducts']);
                        }
                        if (is_array($_POST['selectedproducts'])) {

                            foreach ($_POST['selectedproducts']as $particularpost) {
                                $checkprod = rs_get_product_object($particularpost);
                                if (is_object($checkprod) && ($checkprod->is_type('simple') || ($checkprod->is_type('subscription')) || $checkprod->is_type('booking') || $checkprod->is_type('lottery'))) {
                                    if ($_POST['enabledisablepoints'] == '1') {
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_rewardsystem_enable_point_price', 'yes');
                                    } else {
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_rewardsystem_enable_point_price', 'no');
                                    }
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_rewardsystem_point_price_type', $_POST['pointpricetype']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_rewardsystem__points', $_POST['pricepoints']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_rewardsystem_enable_point_price_type', $_POST['pointpricingtype']);
                                } else {
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_enable_reward_points_price', $_POST['enabledisablepoints']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_enable_reward_points_price_type', $_POST['pointpricetype']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_enable_reward_points_pricing_type', $_POST['pointpricingtype']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, 'price_points', $_POST['pricepoints']);
                                }
                            }
                        }
                        echo json_encode("success");
                    } elseif ($_POST['whichproduct'] == '3') {
                        $allcategories = get_terms('product_cat');
                        $args = array('post_type' => 'product', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', 'cache_results' => false);
                        $products = get_posts($args);
                        foreach ($products as $product) {
                            $checkproducts = rs_get_product_object($product);
                            if ((float) $woocommerce->version >= (float) '3.0') {
                                $id = $checkproducts->get_id();
                            } else {
                                $id = $checkproducts->id;
                            }
                            if (is_object($checkproducts) && ($checkproducts->is_type('simple') || ($checkproducts->is_type('subscription')) || $checkproducts->is_type('booking') || $checkproducts->is_type('lottery'))) {
                                $term = get_the_terms($product, 'product_cat');
                                if (is_array($term)) {
                                    foreach ($allcategories as $mycategory) {
                                        if ($_POST['enabledisablepoints'] == '1') {
                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'enable_point_price_category', 'yes');
                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_enable_point_price', 'yes');
                                        } else {
                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'enable_point_price_category', 'no');
                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_enable_point_price', 'no');
                                        }
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_point_price_type', $_POST['pointpricetype']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_enable_point_price_type', $_POST['pointpricingtype']);

                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem__points', $_POST['pricepoints']);



                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'point_price_category_type', $_POST['pointpricetype']);

                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'rs_category_points_price', $_POST['pricepoints']);
                                    }
                                }
                            } elseif (is_object($checkproducts) && (rs_check_variable_product_type($checkproducts) || ($checkproducts->is_type('variable-subscription')))) {
                                if (is_array($checkproducts->get_available_variations())) {
                                    foreach ($checkproducts->get_available_variations() as $getvariation) {
                                        $term = get_the_terms($id, 'product_cat');
                                        if (is_array($term)) {
                                            foreach ($allcategories as $mycategory) {
                                                if ($_POST['enabledisablepoints'] == '1') {
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'enable_point_price_category', 'yes');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points_price', '1');
                                                } else {
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'enable_point_price_category', 'no');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points_price', '2');
                                                }

                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points_price_type', $_POST['pointpricetype']);


                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], 'price_points', $_POST['pricepoints']);


                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'point_price_category_type', $_POST['pointpricetype']);

                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'rs_category_points_price', $_POST['pricepoints']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        echo json_encode("success");
                    } else {
                        $mycategorylist = $_POST['selectedcategories'];
                        $args = array('post_type' => 'product', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', 'cache_results' => false);
                        $products = get_posts($args);
                        foreach ($products as $product) {
                            $checkproducts = rs_get_product_object($product);
                            if ((float) $woocommerce->version >= (float) '3.0') {
                                $id = $checkproducts->get_id();
                            } else {
                                $id = $checkproducts->id;
                            }
                            if (is_object($checkproducts) && ($checkproducts->is_type('simple') || ($checkproducts->is_type('subscription')) || $checkproducts->is_type('booking') || $checkproducts->is_type('lottery'))) {
                                if (is_array($mycategorylist)) {
                                    foreach ($mycategorylist as $eachlist) {
                                        $term = get_the_terms($product, 'product_cat');
                                        if (is_array($term)) {
                                            foreach ($term as $termidlist) {
                                                if ($eachlist == $termidlist->term_id) {
                                                    if ($_POST['enabledisablepoints'] == '1') {
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'enable_point_price_category', 'yes');
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_enable_point_price', 'yes');
                                                    } else {
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'enable_point_price_category', 'no');
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_enable_point_price', 'no');
                                                    }

                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_point_price_type', $_POST['pointpricetype']);
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_enable_point_price_type', $_POST['pointpricingtype']);

                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem__points', $_POST['pricepoints']);

                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'point_price_category_type', $_POST['pointpricingtype']);


                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'rs_category_points_price', $_POST['pricepoints']);
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'point_price_category_type', $_POST['pointpricetype']);
                                                }
                                            }
                                        }
                                    }
                                }
                            } elseif (is_object($checkproducts) && (rs_check_variable_product_type($checkproducts) || ($checkproducts->is_type('variable-subscription')))) {
                                $mycategorylist = $_POST['selectedcategories'];
                                if (is_array($checkproducts->get_available_variations())) {
                                    foreach ($checkproducts->get_available_variations() as $getvariation) {
                                        if (is_array($mycategorylist)) {
                                            foreach ($mycategorylist as $eachlist) {
                                                $term = get_the_terms($id, 'product_cat');
                                                if (is_array($term)) {
                                                    foreach ($term as $termidlist) {
                                                        if ($eachlist == $termidlist->term_id) {
                                                            if ($_POST['enabledisablepoints'] == '1') {
                                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'enable_point_price_category', 'yes');
                                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points_price', '1');
                                                            } else {
                                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'enable_point_price_category', 'no');
                                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points_price', '2');
                                                            }

                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points_price_type', $_POST['pointpricetype']);

                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], 'price_points', $_POST['pricepoints']);
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'point_price_category_type', $_POST['pointpricetype']);
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points_pricing_type', $_POST['pointpricingtype']);


                                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'point_price_category_type', $_POST['pointpricingtype']);

                                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'rs_category_points_price', $_POST['pricepoints']);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        echo json_encode("success");
                    }
                }

                if ($_POST['proceedanyway'] == '0') {
                    $args = array('post_type' => 'product', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', 'cache_results' => false);
                    $products = get_posts($args);
                    foreach ($products as $product) {
                        $checkproducts = rs_get_product_object($product);
                        if (is_object($checkproducts) && ($checkproducts->is_type('simple') || ($checkproducts->is_type('subscription')) || $checkproducts->is_type('booking'))) {
                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_enable_point_price', 'no');
                        } elseif (is_object($checkproducts) && (rs_check_variable_product_type($checkproducts) || ($checkproducts->is_type('variable-subscription')))) {
                            if (is_array($checkproducts->get_available_variations())) {
                                foreach ($checkproducts->get_available_variations() as $getvariation) {
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points_price', '2');
                                }
                            }
                        }
                    }
                    echo json_encode("success");
                }
                exit();
            }
        }

        public function get_ajax_request_for_previous_product() {
            global $woocommerce;
            global $post;
            if (isset($_POST['proceedanyway'])) {
                if ($_POST['proceedanyway'] == '1') {
                    if ($_POST['whichproduct'] == '1') {
                        $args = array('post_type' => 'product', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', 'cache_results' => false);
                        $products = get_posts($args);
                        echo json_encode($products);
                    } elseif ($_POST['whichproduct'] == '2') {
                        if (!is_array($_POST['selectedproducts'])) {
                            $_POST['selectedproducts'] = explode(',', $_POST['selectedproducts']);
                        }
                        if (is_array($_POST['selectedproducts'])) {

                            foreach ($_POST['selectedproducts']as $particularpost) {
                                $checkprod = rs_get_product_object($particularpost);
                                if (is_object($checkprod) && ($checkprod->is_type('simple') || ($checkprod->is_type('subscription')) || $checkprod->is_type('booking'))) {
                                    if ($_POST['enabledisablereward'] == '1') {
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_rewardsystemcheckboxvalue', 'yes');
                                    } else {
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_rewardsystemcheckboxvalue', 'no');
                                    }
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_rewardsystem_options', $_POST['rewardtype']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_rewardsystempoints', $_POST['rewardpoints']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_rewardsystempercent', $_POST['rewardpercent']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_referral_rewardsystem_options', $_POST['referralrewardtype']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_referralrewardsystempoints', $_POST['referralrewardpoint']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_referralrewardsystempercent', $_POST['referralrewardpercent']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_referral_rewardsystem_options_getrefer', $_POST['referralrewardtyperefer']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_referralrewardsystempoints_for_getting_referred', $_POST['referralpointforgettingrefer']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_referralrewardsystempercent_for_getting_referred', $_POST['referralrewardpercentgettingrefer']);
                                } else {
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_enable_reward_points', $_POST['enabledisablereward']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_select_reward_rule', $_POST['rewardtype']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_reward_points', $_POST['rewardpoints']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_reward_percent', $_POST['rewardpercent']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_select_referral_reward_rule', $_POST['referralrewardtype']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_referral_reward_points', $_POST['referralrewardpoint']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_referral_reward_percent', $_POST['referralrewardpercent']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_select_referral_reward_rule_getrefer', $_POST['referralrewardtyperefer']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_referral_reward_points_getting_refer', $_POST['referralpointforgettingrefer']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_referral_reward_percent_getting_refer', $_POST['referralrewardpercentgettingrefer']);
                                }
                            }
                        }
                        echo json_encode("success");
                    } elseif ($_POST['whichproduct'] == '3') {
                        $allcategories = get_terms('product_cat');
                        $args = array('post_type' => 'product', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', 'cache_results' => false);
                        $products = get_posts($args);
                        foreach ($products as $product) {
                            $checkproducts = rs_get_product_object($product);
                            if ((float) $woocommerce->version >= (float) '3.0') {
                                $id = $checkproducts->get_id();
                            } else {
                                $id = $checkproducts->id;
                            }
                            if (is_object($checkproducts) && ($checkproducts->is_type('simple') || ($checkproducts->is_type('subscription')) || $checkproducts->is_type('booking'))) {
                                $term = get_the_terms($product, 'product_cat');
                                if (is_array($term)) {
                                    foreach ($allcategories as $mycategory) {
                                        if ($_POST['enabledisablereward'] == '1') {
                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'enable_reward_system_category', 'yes');
                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystemcheckboxvalue', 'yes');
                                        } else {
                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'enable_reward_system_category', 'no');
                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystemcheckboxvalue', 'no');
                                        }
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_options', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystempoints', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystempercent', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referral_rewardsystem_options', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referralrewardsystempoints', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referralrewardsystempercent', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referralrewardsystempoints_for_getting_referred', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referralrewardsystempercent_for_getting_referred', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referral_rewardsystem_options_getrefer', '');


                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'enable_rs_rule', $_POST['rewardtype']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'rs_category_points', $_POST['rewardpoints']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'rs_category_percent', $_POST['rewardpercent']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'referral_enable_rs_rule_refer', $_POST['referralrewardtyperefer']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'referral_rs_category_points_get_refered', $_POST['referralpointforgettingrefer']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'referral_rs_category_percent_get_refer', $_POST['referralrewardpercentgettingrefer']);



                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'referral_enable_rs_rule', $_POST['referralrewardtype']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'referral_rs_category_points', $_POST['referralrewardpoint']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'referral_rs_category_percent', $_POST['referralrewardpercent']);
                                    }
                                }
                            } elseif (is_object($checkproducts) && (rs_check_variable_product_type($checkproducts) || ($checkproducts->is_type('variable-subscription')))) {
                                if (is_array($checkproducts->get_available_variations())) {
                                    foreach ($checkproducts->get_available_variations() as $getvariation) {
                                        $term = get_the_terms($id, 'product_cat');
                                        if (is_array($term)) {
                                            foreach ($allcategories as $mycategory) {
                                                if ($_POST['enabledisablereward'] == '1') {
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'enable_reward_system_category', 'yes');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points', $_POST['enabledisablereward']);
                                                } else {
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'enable_reward_system_category', 'no');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points', $_POST['enabledisablereward']);
                                                }

                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_select_reward_rule', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_reward_points', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_reward_percent', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_select_referral_reward_rule', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_referral_reward_points', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_referral_reward_percent', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_referral_reward_points_getting_refer', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_referral_reward_percent_getting_refer', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_select_referral_reward_rule_getrefer', '');


                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'enable_rs_rule', $_POST['rewardtype']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'rs_category_points', $_POST['rewardpoints']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'rs_category_percent', $_POST['rewardpercent']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'referral_enable_rs_rule_refer', $_POST['referralrewardtyperefer']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'referral_rs_category_points_get_refered', $_POST['referralpointforgettingrefer']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'referral_rs_category_percent_get_refer', $_POST['referralrewardpercentgettingrefer']);



                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'referral_enable_rs_rule', $_POST['referralrewardtype']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'referral_rs_category_points', $_POST['referralrewardpoint']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'referral_rs_category_percent', $_POST['referralrewardpercent']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        echo json_encode("success");
                    } else {
                        $mycategorylist = $_POST['selectedcategories'];
                        $args = array('post_type' => 'product', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', 'cache_results' => false);
                        $products = get_posts($args);
                        foreach ($products as $product) {
                            $checkproducts = rs_get_product_object($product);
                            if ((float) $woocommerce->version >= (float) '3.0') {
                                $id = $checkproducts->get_id();
                            } else {
                                $id = $checkproducts->id;
                            }
                            if (is_object($checkproducts) && ($checkproducts->is_type('simple') || ($checkproducts->is_type('subscription')) || $checkproducts->is_type('booking'))) {
                                if (is_array($mycategorylist)) {
                                    foreach ($mycategorylist as $eachlist) {
                                        $term = get_the_terms($product, 'product_cat');
                                        if (is_array($term)) {
                                            foreach ($term as $termidlist) {
                                                if ($eachlist == $termidlist->term_id) {
                                                    if ($_POST['enabledisablereward'] == '1') {
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'enable_reward_system_category', 'yes');
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystemcheckboxvalue', 'yes');
                                                    } else {
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'enable_reward_system_category', 'no');
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystemcheckboxvalue', 'no');
                                                    }

                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_options', '');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystempoints', '');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystempercent', '');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referral_rewardsystem_options', '');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referralrewardsystempoints', '');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referralrewardsystempercent', '');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referral_rewardsystem_options_getrefer', '');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referralrewardsystempoints_for_getting_referred', '');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referralrewardsystempercent_for_getting_referred', '');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'referral_enable_rs_rule_refer', $_POST['referralrewardtyperefer']);
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'referral_rs_category_points_get_refered', $_POST['referralpointforgettingrefer']);
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'referral_rs_category_percent_get_refer', $_POST['referralrewardpercentgettingrefer']);



                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'enable_rs_rule', $_POST['rewardtype']);
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'rs_category_points', $_POST['rewardpoints']);
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'rs_category_percent', $_POST['rewardpercent']);


                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'referral_enable_rs_rule', $_POST['referralrewardtype']);
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'referral_rs_category_points', $_POST['referralrewardpoint']);
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'referral_rs_category_percent', $_POST['referralrewardpercent']);
                                                }
                                            }
                                        }
                                    }
                                }
                            } elseif (is_object($checkproducts) && (rs_check_variable_product_type($checkproducts) || ($checkproducts->is_type('variable-subscription')))) {
                                $mycategorylist = $_POST['selectedcategories'];
                                if (is_array($checkproducts->get_available_variations())) {
                                    foreach ($checkproducts->get_available_variations() as $getvariation) {
                                        if (is_array($mycategorylist)) {
                                            foreach ($mycategorylist as $eachlist) {
                                                $term = get_the_terms($id, 'product_cat');
                                                if (is_array($term)) {
                                                    foreach ($term as $termidlist) {
                                                        if ($eachlist == $termidlist->term_id) {
                                                            if ($_POST['enabledisablereward'] == '1') {
                                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'enable_reward_system_category', 'yes');
                                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points', $_POST['enabledisablereward']);
                                                            } else {
                                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'enable_reward_system_category', 'no');
                                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points', $_POST['enabledisablereward']);
                                                            }

                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_select_reward_rule', '');
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_reward_points', '');
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_reward_percent', '');
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_select_referral_reward_rule', '');
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_referral_reward_points', '');
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_referral_reward_percent', '');

                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_referral_reward_points_getting_refer', '');
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_referral_reward_percent_getting_refer', '');
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_select_referral_reward_rule_getrefer', '');

                                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'enable_rs_rule', $_POST['rewardtype']);
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'rs_category_points', $_POST['rewardpoints']);
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'rs_category_percent', $_POST['rewardpercent']);


                                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'referral_enable_rs_rule', $_POST['referralrewardtype']);
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'referral_rs_category_points', $_POST['referralrewardpoint']);
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'referral_rs_category_percent', $_POST['referralrewardpercent']);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        echo json_encode("success");
                    }
                }
                if ($_POST['proceedanyway'] == '0') {
                    $args = array('post_type' => 'product', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', 'cache_results' => false);
                    $products = get_posts($args);
                    foreach ($products as $product) {
                        $checkproducts = rs_get_product_object($product);
                        if (is_object($checkproducts) && ($checkproducts->is_type('simple') || ($checkproducts->is_type('subscription')) || $checkproducts->is_type('booking'))) {
                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystemcheckboxvalue', 'no');
                        } elseif (is_object($checkproducts) && (rs_check_variable_product_type($checkproducts) || ($checkproducts->is_type('variable-subscription')))) {
                            if (is_array($checkproducts->get_available_variations())) {
                                foreach ($checkproducts->get_available_variations() as $getvariation) {
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points', '2');
                                }
                            }
                        }
                    }
                    echo json_encode("success");
                }
                exit();
            }
        }

        public function get_ajax_request_for_previous_social_product($post_id) {
            global $woocommerce;
            global $post;
            if (isset($_POST['proceedanyway'])) {
                if ($_POST['proceedanyway'] == '1') {
                    if ($_POST['whichproduct'] == '1') {
                        $args = array('post_type' => 'product', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', 'cache_results' => false);
                        $products = get_posts($args);
                        echo json_encode($products);
                    } elseif ($_POST['whichproduct'] == '2') {
                        if (!is_array($_POST['selectedproducts'])) {
                            $_POST['selectedproducts'] = explode(',', $_POST['selectedproducts']);
                        }
                        if (is_array($_POST['selectedproducts'])) {
                            foreach ($_POST['selectedproducts']as $particularpost) {
                                $checkprod = rs_get_product_object($particularpost);
                                if ($_POST['enabledisablereward'] == '1') {
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystemcheckboxvalue', 'yes');
                                } else {
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystemcheckboxvalue', 'no');
                                }
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_social_rewardsystem_options_facebook', $_POST['rewardtypefacebook']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempoints_facebook', $_POST['facebookrewardpoints']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempercent_facebook', $_POST['facebookrewardpercent']);

                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_social_rewardsystem_options_facebook_share', $_POST['rewardtypefacebook_share']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempoints_facebook_share', $_POST['facebookrewardpoints_share']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempercent_facebook_share', $_POST['facebookrewardpercent_share']);



                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_social_rewardsystem_options_twitter', $_POST['rewardtypetwitter']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempoints_twitter', $_POST['twitterrewardpoints']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempercent_twitter', $_POST['twitterrewardpercent']);


                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_social_rewardsystem_options_twitter_follow', $_POST['rewardtypetwitter_follow']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempoints_twitter_follow', $_POST['twitterrewardpoints_follow']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempercent_twitter_follow', $_POST['twitterrewardpercent_follow']);


                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_social_rewardsystem_options_ok_follow', $_POST['rewardtypeok_follow']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempoints_ok_follow', $_POST['okrewardpoints_follow']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempercent_ok_follow', $_POST['okrewardpercent_follow']);


                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_social_rewardsystem_options_google', $_POST['rewardtypegoogle']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempoints_google', $_POST['googlerewardpoints']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempercent_google', $_POST['googlerewardpercent']);


                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_social_rewardsystem_options_vk', $_POST['rewardtypevk']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempoints_vk', $_POST['vkrewardpoints']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempercent_vk', $_POST['vkrewardpercent']);


                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_social_rewardsystem_options_instagram', $_POST['rewardtypeinstagram']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempoints_instagram', $_POST['instagramrewardpoints']);
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($particularpost, '_socialrewardsystempercent_instagram', $_POST['instagramrewardpercent']);
                            }
                        }
                        echo json_encode("success");
                    } elseif ($_POST['whichproduct'] == '3') {
                        $allcategories = get_terms('product_cat');

                        $args = array('post_type' => 'product', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', 'cache_results' => false);
                        $products = get_posts($args);
                        foreach ($products as $product) {



                            $term = get_the_terms($product, 'product_cat');
                            if (is_array($term)) {

                                if (is_array($allcategories)) {
                                    foreach ($allcategories as $mycategory) {
                                        if ($_POST['enabledisablereward'] == '1') {
                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'enable_social_reward_system_category', 'yes');
                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystemcheckboxvalue', 'yes');
                                        } else {
                                            RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'enable_social_reward_system_category', 'no');
                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystemcheckboxvalue', 'no');
                                        }
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_facebook', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_facebook', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_facebook', '');

                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_twitter', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_twitter', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_twitter', '');

                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_twitter_follow', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_twitter_follow', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_twitter_follow', '');


                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_google', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_google', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_google', '');


                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_vk', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_vk', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_vk', '');

                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_instagram', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_instagram', '');
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_instagram', '');


                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_facebook_enable_rs_rule', $_POST['rewardtypefacebook']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_facebook_rs_category_points', $_POST['facebookrewardpoints']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_facebook_rs_category_percent', $_POST['facebookrewardpercent']);


                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_facebook_share_enable_rs_rule', $_POST['rewardtypefacebook_share']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_facebook_share_rs_category_points', $_POST['facebookrewardpoints_share']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_facebook_share_rs_category_percent', $_POST['facebookrewardpercent_share']);


                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_twitter_enable_rs_rule', $_POST['rewardtypetwitter']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_twitter_rs_category_points', $_POST['twitterrewardpoints']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_twitter_rs_category_percent', $_POST['twitterrewardpercent']);

                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_google_enable_rs_rule', $_POST['rewardtypegoogle']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_google_rs_category_points', $_POST['googlerewardpoints']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_google_rs_category_percent', $_POST['googlerewardpercent']);


                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_vk_enable_rs_rule', $_POST['rewardtypevk']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_vk_rs_category_points', $_POST['vkrewardpoints']);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($mycategory->term_id, 'social_vk_rs_category_percent', $_POST['vkrewardpercent']);
                                    }
                                }
                            }
                        }
                        echo json_encode("success");
                    } else {
                        $mycategorylist = $_POST['selectedcategories'];

                        $args = array('post_type' => 'product', 'posts_per_page' => '-1', 'post_status' => 'publish', 'fields' => 'ids', 'cache_results' => false);
                        $products = get_posts($args);
                        foreach ($products as $product) {
                            $checkproducts = rs_get_product_object($product);

                            if (is_array($mycategorylist)) {
                                foreach ($mycategorylist as $eachlist) {
                                    $term = get_the_terms($product, 'product_cat');
                                    if (is_array($term)) {
                                        foreach ($term as $termidlist) {
                                            if ($eachlist == $termidlist->term_id) {
                                                if ($_POST['enabledisablereward'] == '1') {
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'enable_social_reward_system_category', 'yes');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystemcheckboxvalue', 'yes');
                                                } else {
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'enable_social_reward_system_category', 'no');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystemcheckboxvalue', 'no');
                                                }

                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_facebook', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_facebook', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_facebook', '');

                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_twitter', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_twitter', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_twitter', '');

                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_twitter_follow', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_twitter_follow', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_twitter_follow', '');


                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_google', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_google', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_google', '');


                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_vk', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_vk', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_vk', '');

                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_instagram', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_instagram', '');
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_instagram', '');



                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_facebook_enable_rs_rule', $_POST['rewardtypefacebook']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_facebook_rs_category_points', $_POST['facebookrewardpoints']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_facebook_rs_category_percent', $_POST['facebookrewardpercent']);


                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_facebook_share_enable_rs_rule', $_POST['rewardtypefacebook_share']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_facebook_share_rs_category_points', $_POST['facebookrewardpoints_share']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_facebook_share_rs_category_percent', $_POST['facebookrewardpercent_share']);


                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_twitter_enable_rs_rule', $_POST['rewardtypetwitter']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_twitter_rs_category_points', $_POST['twitterrewardpoints']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_twitter_rs_category_percent', $_POST['twitterrewardpercent']);

                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_google_enable_rs_rule', $_POST['rewardtypegoogle']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_google_rs_category_points', $_POST['googlerewardpoints']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_google_rs_category_percent', $_POST['googlerewardpercent']);


                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_vk_enable_rs_rule', $_POST['rewardtypevk']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_vk_rs_category_points', $_POST['vkrewardpoints']);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_woocommerce_term_meta($eachlist, 'social_vk_rs_category_percent', $_POST['vkrewardpercent']);
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        echo json_encode("success");
                    }
                }
                exit();
            }
        }

        public static function process_chunk_ajax_request_in_rewardsystem() {
            if (isset($_POST['ids'])) {
                $products = $_POST['ids'];
                foreach ($products as $product) {
                    $checkproduct = rs_get_product_object($product);
                    if (is_object($checkproduct) && ($checkproduct->is_type('simple') || ($checkproduct->is_type('subscription')) || $checkproduct->is_type('booking'))) {
                        if ($_POST['enabledisablereward'] == '1') {
                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystemcheckboxvalue', 'yes');
                        } else {
                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystemcheckboxvalue', 'no');
                        }
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_options', $_POST['rewardtype']);
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystempoints', $_POST['rewardpoints']);
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystempercent', $_POST['rewardpercent']);
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referral_rewardsystem_options', $_POST['referralrewardtype']);
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referralrewardsystempoints', $_POST['referralrewardpoint']);
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referralrewardsystempercent', $_POST['referralrewardpercent']);
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referral_rewardsystem_options_getrefer', $_POST['referralrewardtyperefer']);
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referralrewardsystempoints_for_getting_referred', $_POST['referralpointforgettingrefer']);
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_referralrewardsystempercent_for_getting_referred', $_POST['referralrewardpercentgettingrefer']);
                    } else {
                        if (is_object($checkproduct) && (rs_check_variable_product_type($checkproduct) || ($checkproduct->is_type('variable-subscription')))) {
                            if (is_array($checkproduct->get_available_variations())) {
                                foreach ($checkproduct->get_available_variations() as $getvariation) {
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points', $_POST['enabledisablereward']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_select_reward_rule', $_POST['rewardtype']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_reward_points', $_POST['rewardpoints']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_reward_percent', $_POST['rewardpercent']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_select_referral_reward_rule', $_POST['referralrewardtype']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_referral_reward_points', $_POST['referralrewardpoint']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_referral_reward_percent', $_POST['referralrewardpercent']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_select_referral_reward_rule_getrefer', $_POST['referralrewardtyperefer']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_referral_reward_points_getting_refer', $_POST['referralpointforgettingrefer']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_referral_reward_percent_getting_refer', $_POST['referralrewardpercentgettingrefer']);
                                }
                            }
                        }
                    }
                }
            }

            exit();
        }

        public static function process_chunk_ajax_request_in_rewardsystem_point_price() {
            if (isset($_POST['ids'])) {
                $products = $_POST['ids'];
                foreach ($products as $product) {
                    $checkproduct = rs_get_product_object($product);
                    if (is_object($checkproduct) && ($checkproduct->is_type('simple') || ($checkproduct->is_type('subscription')) || $checkproduct->is_type('booking') || $checkproduct->is_type('lottery'))) {
                        if ($_POST['enabledisablepoints'] == '1') {
                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_enable_point_price', 'yes');
                        } else {
                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_enable_point_price', 'no');
                        }
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_enable_point_price_type', $_POST['pointpricingtype']);

                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem_point_price_type', $_POST['pointpricetype']);
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_rewardsystem__points', $_POST['pricepoints']);
                    } else {
                        if (is_object($checkproduct) && (rs_check_variable_product_type($checkproduct) || ($checkproduct->is_type('variable-subscription')))) {
                            if (is_array($checkproduct->get_available_variations())) {
                                foreach ($checkproduct->get_available_variations() as $getvariation) {
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points_price', $_POST['enabledisablepoints']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points_pricing_type', $_POST['pointpricingtype']);

                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], 'price_points', $_POST['pricepoints']);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($getvariation['variation_id'], '_enable_reward_points_price_type', $_POST['pointpricetype']);
                                }
                            }
                        }
                    }
                }
            }

            exit();
        }

        public static function process_chunk_ajax_request_in_social_rewardsystem() {
            if (isset($_POST['ids'])) {
                $products = $_POST['ids'];
                foreach ($products as $product) {


                    if ($_POST['enabledisablereward'] == '1') {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystemcheckboxvalue', 'yes');
                    } else {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystemcheckboxvalue', 'no');
                    }
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_facebook', $_POST['rewardtypefacebook']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_facebook', $_POST['facebookrewardpoints']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_facebook', $_POST['facebookrewardpercent']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_facebook_share', $_POST['rewardtypefacebook_share']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_facebook_share', $_POST['facebookrewardpoints_share']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_facebook_share', $_POST['facebookrewardpercent_share']);

                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_twitter', $_POST['rewardtypetwitter']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_twitter', $_POST['twitterrewardpoints']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_twitter', $_POST['twitterrewardpercent']);

                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_twitter_follow', $_POST['rewardtypetwitter_follow']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_twitter_follow', $_POST['twitterrewardpoints_follow']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_twitter_follow', $_POST['twitterrewardpercent_follow']);

                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_ok_follow', $_POST['rewardtypeok_follow']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_ok_follow', $_POST['okrewardpoints_follow']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_ok_follow', $_POST['okrewardpercent_follow']);


                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_google', $_POST['rewardtypegoogle']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_google', $_POST['googlerewardpoints']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_google', $_POST['googlerewardpercent']);


                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_vk', $_POST['rewardtypevk']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_vk', $_POST['vkrewardpoints']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_vk', $_POST['vkrewardpercent']);


                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_social_rewardsystem_options_instagram', $_POST['rewardtypeinstagram']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempoints_instagram', $_POST['instagramrewardpoints']);
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product, '_socialrewardsystempercent_instagram', $_POST['instagramrewardpercent']);
                }
            }
            exit();
        }

        public static function rs_process_ajax_points_to_previous_order() {
            if (isset($_POST['proceedanyway'])) {
                if ($_POST['proceedanyway'] == '1') {
                    $orderstatuslist = get_option('rs_order_status_control');
                    $new_order = array('wc-completed');
                    foreach ($orderstatuslist as $each_order) {
                        $new_order[] = 'wc-' . $each_order;
                    }
                    $args = array('post_type' => 'shop_order', 'numberposts' => '-1', 'meta_query' => array(array('key' => 'reward_points_awarded', 'compare' => 'NOT EXISTS')), 'post_status' => $new_order, 'fields' => 'ids', 'cache_results' => false);
                    $order_id = get_posts($args);
                    echo json_encode($order_id);
                }
            }
            exit();
        }

        public static function rs_send_ajax_points_to_previous_orders() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('.gif_rs_sumo_reward_button_for_previous_order').css('display', 'none');
                    jQuery('.rs_sumo_rewards_for_previous_order').click(function () {
                        jQuery('.gif_rs_sumo_reward_button_for_previous_order').css('display', 'inline-block');
                        jQuery(this).attr('data-clicked', '1');
                        var dataclicked = jQuery(this).attr('data-clicked');
                        var fromdate = jQuery('#rs_from_date').val();
                        var todate = jQuery('#rs_to_date').val();
                        if (jQuery('#rs_sumo_select_order_range').val() === '1') {
                            var dataparam = ({
                                action: 'previousorderpoints',
                                proceedanyway: dataclicked,
                            });
                            function getData(id) {
                                return jQuery.ajax({
                                    type: 'POST',
                                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                    data: ({
                                        action: 'rssplitajaxoptimizationforpreviousorder',
                                        ids: id,
                                        proceedanyway: dataclicked,
                                        //fromdate: fromdate,
                                        //todate: todate,
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
                                                getData(temparray);
                                            }
                                            jQuery.when(getData()).done(function (a1) {
                                                console.log('Ajax Done Successfully');
                                                location.reload();
                                                jQuery('.rs_sumo_rewards_previous_order').fadeIn();
                                                if (response != '') {
                                                    jQuery('.rs_sumo_rewards_previous_order').html('Points Successfully Added to Previous Order');
                                                } else {
                                                    jQuery('.rs_sumo_rewards_previous_order').html('There is no order to give points');
                                                }
                                                jQuery('.rs_sumo_rewards_previous_order').fadeOut(5000);
                                            });
                                        }
                                    }, 'json');
                        } else {
                            var dataparam = ({
                                action: 'previousorderpoints',
                                proceedanyway: dataclicked,
                                fromdate: fromdate,
                                todate: todate,
                            });
                            function getDataforDate(id) {
                                return jQuery.ajax({
                                    type: 'POST',
                                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                    data: ({
                                        action: 'rssplitajaxoptimizationforpreviousorder',
                                        ids: id,
                                        proceedanyway: dataclicked,
                                        //fromdate: fromdate,
                                        //todate: todate,
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
                                        // alert(response);
                                        if (response != 'success') {
                                            var j = 1;
                                            var i, j, temparray, chunk = 10;
                                            for (i = 0, j = response.length; i < j; i += chunk) {
                                                temparray = response.slice(i, i + chunk);
                                                getDataforDate(temparray);
                                            }
                                            jQuery.when(getDataforDate()).done(function (a1) {
                                                console.log('Ajax Done Successfully');
                                                location.reload();
                                                jQuery('.rs_sumo_rewards_previous_order').fadeIn();
                                                if (response != '') {
                                                    jQuery('.rs_sumo_rewards_previous_order').html('Points Successfully Added to Previous Order');
                                                } else {
                                                    jQuery('.rs_sumo_rewards_previous_order').html('There is no order to give points');
                                                }
                                                jQuery('.rs_sumo_rewards_previous_order').fadeOut(5000);
                                            });
                                        }
                                    }, 'json');
                        }
                        return false;
                    });
                });</script>
            <?php
        }

        public static function process_chunk_ajax_request_for_previous_orders() {
            if (isset($_POST['ids'])) {
                $products = $_POST['ids'];
                foreach ($products as $product) {
                    $order = new WC_Order($product);
                    $modified_date = get_the_time('Y-m-d', $product);
                    if (isset($_POST['fromdate']) && ($_POST['todate'])) {
                        if (($_POST['fromdate'] <= $modified_date) && $modified_date <= $_POST['todate']) {
                            $points_awarded_for_this_order = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product, 'reward_points_awarded');
                            if ($points_awarded_for_this_order != 'yes') {
                                $new_obj = new RewardPointsOrder($product, $apply_previous_order_points = 'yes');
                                $new_obj->update_earning_points_for_user();
                                $order_user_id = rs_get_order_obj($order);
                                $order_user_id = $order_user_id['order_userid'];
                                update_user_meta($order_user_id, 'rsfirsttime_redeemed', 1);
                                add_post_meta($product, 'reward_points_awarded', 'yes');
                            }
                        }
                    } else {
                        $points_awarded_for_this_order = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product, 'reward_points_awarded');
                        if ($points_awarded_for_this_order != 'yes') {
                            $new_obj = new RewardPointsOrder($product, $apply_previous_order_points = 'yes');
                            $new_obj->update_earning_points_for_user();
                            $order_user_id = rs_get_order_obj($order);
                            $order_user_id = $order_user_id['order_userid'];
                            update_user_meta($order_user_id, 'rsfirsttime_redeemed', 1);
                            add_post_meta($product, 'reward_points_awarded', 'yes');
                        }
                    }
                }
            }
            exit();
        }

        public static function rs_function_to_reset_update_tab() {
            $settings = RSUpdate::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

    }

    RSUpdate::init();
}