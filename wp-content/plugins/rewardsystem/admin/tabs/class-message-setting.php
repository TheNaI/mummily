<?php

/*
 * Message Tab Setting
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSMessage')) {

    class RSMessage {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_message', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_message', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));

            add_action('fp_action_to_reset_settings_rewardsystem_message', array(__CLASS__, 'rs_function_to_reset_message_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_message'] = __('Messages', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;
            return apply_filters('woocommerce_rewardsystem_message_settings', array(
                array(
                    'name' => __('Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_message_setting',
                ),
                array('type' => 'sectionend', 'id' => 'rs_message_setting'),
                array(
                    'name' => __('Shop Page and Category Page Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_shop_page_msg',
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message for Simple Products – Logged in Users', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_simple_in_shop',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_simple_in_shop',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message for Simple Products - Guests', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_simple_in_shop_guest',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_simple_in_shop_guest',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Earn Point(s) Message for Simple Products', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on Shop Page', 'rewardsystem'),
                    'id' => 'rs_message_in_shop_page_for_simple',
                    'css' => 'min-width:550px;',
                    'std' => 'Earn [rewardpoints] Reward Points',
                    'default' => 'Earn [rewardpoints] Reward Points',
                    'type' => 'textarea',
                    'newids' => 'rs_message_in_shop_page_for_simple',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Earn Point(s) Message display Position for Simple Products', 'rewardsystem'),
                    'id' => 'rs_message_position_for_simple_products_in_shop_page',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_message_position_for_simple_products_in_shop_page',
                    'options' => array(
                        '1' => __('Before Product Price', 'rewardsystem'),
                        '2' => __('After Product Price', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message for Variable Products', 'rewardsystem'),
                    'id' => 'rs_enable_display_earn_message_for_variation',
                    'css' => 'min-width:150px;',
                    'type' => 'checkbox',
                    'newids' => 'rs_enable_display_earn_message_for_variation',
                ),
                array(
                    'type' => 'sectionend',
                    'id' => '_rs_shop_page_msg'
                ),
                array(
                    'name' => __('Single Product Page Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_single__product_page_msg',
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message as Notice for Simple Products – Logged in Users', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_single_product',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_single_product',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message as Notice for Simple Products – Guests', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_single_product_guest',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_single_product_guest',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Earn Point(s) Notice Message for Simple Products', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on top of Single Product Page', 'rewardsystem'),
                    'id' => 'rs_message_for_single_product_point_rule',
                    'css' => 'min-width:550px;',
                    'std' => 'Purchase this Product and Earn [rewardpoints] Reward Points ([equalamount])',
                    'default' => 'Purchase this Product and Earn [rewardpoints] Reward Points ([equalamount])',
                    'type' => 'textarea',
                    'newids' => 'rs_message_for_single_product_point_rule',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message for Simple Products', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_shop_archive_single',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_shop_archive_single',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Earn Point(s) Message for Simple Products', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on Shop Page and Single Product Page', 'rewardsystem'),
                    'id' => 'rs_message_in_single_product_page',
                    'css' => 'min-width:550px;',
                    'std' => 'Earn [rewardpoints] Reward Points',
                    'default' => 'Earn [rewardpoints] Reward Points',
                    'type' => 'textarea',
                    'newids' => 'rs_message_in_single_product_page',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message in Variation Level for Variable Products', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_variable_in_single_product_page',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_variable_in_single_product_page',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Earn Point(s) Message for Variations of Variable Product', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on Variation Page of Variable Product', 'rewardsystem'),
                    'id' => 'rs_message_for_single_product_variation',
                    'css' => 'min-width:550px;',
                    'std' => 'Earn [variationrewardpoints] Reward Points',
                    'default' => 'Earn [variationrewardpoints] Reward Points',
                    'type' => 'textarea',
                    'newids' => 'rs_message_for_single_product_variation',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Earn Point(s) Message display Position for Simple Products', 'rewardsystem'),
                    'id' => 'rs_message_position_in_single_product_page_for_simple_products',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_message_position_in_single_product_page_for_simple_products',
                    'options' => array(
                        '1' => __('Before Product Price', 'rewardsystem'),
                        '2' => __('After Product Price', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Earn Point(s) Message display Position for Variable Products', 'rewardsystem'),
                    'id' => 'rs_message_position_in_single_product_page_for_variable_products',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_message_position_in_single_product_page_for_variable_products',
                    'options' => array(
                        '1' => __('Before Product Price', 'rewardsystem'),
                        '2' => __('After Product Price', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message as Notice for Variable Products – Logged in Users', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_variable_product',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_variable_product',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Earn Point(s) Notice Message for Variable Products', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on top of Variable Product', 'rewardsystem'),
                    'id' => 'rs_message_for_variation_products',
                    'css' => 'min-width:550px;',
                    'std' => 'Purchase this Product and Earn [variationrewardpoints] Reward Points ([variationpointsvalue])',
                    'default' => 'Purchase this Product and Earn [variationrewardpoints] Reward Points ([variationpointsvalue])',
                    'type' => 'textarea',
                    'newids' => 'rs_message_for_variation_products',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message for Variable Products', 'rewardsystem'),
                    'id' => 'rs_enable_display_earn_message_for_variation_single_product',
                    'css' => 'min-width:150px;',
                    'type' => 'checkbox',
                    'newids' => 'rs_enable_display_earn_message_for_variation_single_product',
                ),
                array('type' => 'sectionend', 'id' => '_rs_single__product_page_msg'),
                array(
                    'name' => __('Cart Page Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_cart_page_msg',
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message for Guests', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_guest',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_guest',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Earn Point(s) Message for Guests', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on top of Cart Page for Guest', 'rewardsystem'),
                    'id' => 'rs_message_for_guest_in_cart',
                    'css' => 'min-width:550px;',
                    'std' => 'Earn Reward Points for Product Purchase, Product Review and Sign up, etc [loginlink]',
                    'default' => 'Earn Reward Points for Product Purchase, Product Review and Sign up, etc [loginlink]',
                    'type' => 'textarea',
                    'newids' => 'rs_message_for_guest_in_cart',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message for each Product', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_each_products',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_each_products',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Earn Point(s) Message for each Product', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed in each Products added in the Cart', 'rewardsystem'),
                    'id' => 'rs_message_product_in_cart',
                    'css' => 'min-width:550px;',
                    'std' => 'Purchase [titleofproduct] and Earn <strong>[rspoint]</strong> Reward Points ([carteachvalue])',
                    'default' => 'Purchase [titleofproduct] and Earn <strong>[rspoint]</strong> Reward Points ([carteachvalue])',
                    'type' => 'textarea',
                    'newids' => 'rs_message_product_in_cart',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Total Points that can be Earned', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_total_points',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_total_points',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Message for Total Points that can be Earned', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on top of Cart Page', 'rewardsystem'),
                    'id' => 'rs_message_total_price_in_cart',
                    'css' => 'min-width:550px;',
                    'std' => 'Complete the Purchase and Earn <strong>[totalrewards]</strong> Reward Points ([totalrewardsvalue])',
                    'default' => 'Complete the Purchase and Earn <strong>[totalrewards]</strong> Reward Points ([totalrewardsvalue])',
                    'type' => 'textarea',
                    'newids' => 'rs_message_total_price_in_cart',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Available Reward Points', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_my_rewards',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_my_rewards',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Available Reward Points Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on top of Cart Page with Your Reward Points ', 'rewardsystem'),
                    'id' => 'rs_message_user_points_in_cart',
                    'css' => 'min-width:550px;',
                    'std' => 'My Reward Points [userpoints] ([userpoints_value])',
                    'default' => 'My Reward Points [userpoints] ([userpoints_value])',
                    'type' => 'textarea',
                    'newids' => 'rs_message_user_points_in_cart',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Redeemed Points Message', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_redeem_points',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_redeem_points',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Redeemed Points Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on top of Cart Page', 'rewardsystem'),
                    'id' => 'rs_message_user_points_redeemed_in_cart',
                    'css' => 'min-width:550px;',
                    'std' => '[redeempoints] Reward Points Redeemed. Balance [redeemeduserpoints] Reward Points',
                    'default' => '[redeempoints] Reward Points Redeemed. Balance [redeemeduserpoints] Reward Points',
                    'type' => 'textarea',
                    'newids' => 'rs_message_user_points_redeemed_in_cart',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_cart_page_msg'),
                array(
                    'name' => __('Checkout Page Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_checkout_page_msg',
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message for Guests', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_guest_checkout_page',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_guest_checkout_page',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Earn Point(s) Message for Guests', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on top of Checkout Page for Guest', 'rewardsystem'),
                    'id' => 'rs_message_for_guest_in_checkout',
                    'css' => 'min-width:550px;',
                    'std' => 'Earn Reward Points for Product Purchase, Product Review and Sign up, etc [loginlink]',
                    'default' => 'Earn Reward Points for Product Purchase, Product Review and Sign up, etc [loginlink]',
                    'type' => 'textarea',
                    'newids' => 'rs_message_for_guest_in_checkout',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Earn Point(s) Message for each Product', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_each_products_checkout_page',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_each_products_checkout_page',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Earn Point(s) Message for each Product', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed in each Products added in the Checkout', 'rewardsystem'),
                    'id' => 'rs_message_product_in_checkout',
                    'css' => 'min-width:550px;',
                    'std' => 'Purchase [titleofproduct] and Earn <strong>[rspoint]</strong> Reward Points ([carteachvalue])',
                    'default' => 'Purchase [titleofproduct] and Earn <strong>[rspoint]</strong> Reward Points ([carteachvalue])',
                    'type' => 'textarea',
                    'newids' => 'rs_message_product_in_checkout',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Total Points that can be Earned', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_total_points_checkout_page',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_total_points_checkout_page',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Message for Total Points that can be Earned', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on top of Checkout Page', 'rewardsystem'),
                    'id' => 'rs_message_total_price_in_checkout',
                    'css' => 'min-width:550px;',
                    'std' => 'Complete the Purchase and Earn <strong>[totalrewards]</strong> Reward Points ([totalrewardsvalue])',
                    'default' => 'Complete the Purchase and Earn <strong>[totalrewards]</strong> Reward Points ([totalrewardsvalue])',
                    'type' => 'textarea',
                    'newids' => 'rs_message_total_price_in_checkout',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Available Reward Points', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_my_rewards_checkout_page',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_my_rewards_checkout_page',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Available Reward Points Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on top of Checkout Page with Your Reward Points ', 'rewardsystem'),
                    'id' => 'rs_message_user_points_in_checkout',
                    'css' => 'min-width:550px;',
                    'std' => 'My Reward Points [userpoints] ([userpoints_value])',
                    'default' => 'My Reward Points [userpoints] ([userpoints_value])',
                    'type' => 'textarea',
                    'newids' => 'rs_message_user_points_in_checkout',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Redeemed Points Message', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_redeem_points_checkout_page',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_redeem_points_checkout_page',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Redeemed Points Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed on top of Checkout Page', 'rewardsystem'),
                    'id' => 'rs_message_user_points_redeemed_in_checkout',
                    'css' => 'min-width:550px;',
                    'std' => '[redeempoints] Reward Points Redeemed. Balance [redeemeduserpoints] Reward Points',
                    'default' => '[redeempoints] Reward Points Redeemed. Balance [redeemeduserpoints] Reward Points',
                    'type' => 'textarea',
                    'newids' => 'rs_message_user_points_redeemed_in_checkout',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Payment Gateway Reward Points Message', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_payment_gateway_reward_points',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_payment_gateway_reward_points',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Payment Gateway Reward Points Message', 'rewardsystem'),
                    'desc' => __('Enter the Message for Payment Gateway Reward Points', 'rewardsystem'),
                    'id' => 'rs_message_payment_gateway_reward_points',
                    'css' => 'min-width:550px;',
                    'std' => 'Use this [paymentgatewaytitle] and Earn [paymentgatewaypoints] Reward Points',
                    'default' => 'Use this [paymentgatewaytitle] and Earn [paymentgatewaypoints] Reward Points',
                    'type' => 'textarea',
                    'newids' => 'rs_message_payment_gateway_reward_points',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_checkout_page_msg'),
                array(
                    'name' => __('Cart and Checkout Page Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_cart_checkout_page_msg',
                ),
                array(
                    'name' => __('Show/Hide Reward Points Redeeming Success Message', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_for_redeem',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_for_redeem',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Reward Points Redeeming Success Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when Reward Points are redeemed in cart', 'rewardsystem'),
                    'id' => 'rs_success_coupon_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Reward Points Successfully Added',
                    'default' => 'Reward Points Successfully Added',
                    'type' => 'text',
                    'newids' => 'rs_success_coupon_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeemed Points Removal Message', 'rewardsystem'),
                    'desc' => __('Enter the Message to be displayed when Redeem Point is removed', 'rewardsystem'),
                    'id' => 'rs_remove_redeem_points_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Reward Points has been removed.',
                    'default' => 'Reward Points has been removed.',
                    'type' => 'text',
                    'newids' => 'rs_remove_redeem_points_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message for Maximum Redeeming Threshold Value', 'rewardsystem'),
                    'desc' => __('Error Message for Maximum Discount Type', 'rewardsystem'),
                    'id' => 'rs_errmsg_for_max_discount_type',
                    'css' => 'min-width:550px;',
                    'std' => 'Maximum Discount has been Limited to [percentage] %',
                    'default' => 'Maximum Discount has been Limited to [percentage] %',
                    'type' => 'textarea',
                    'newids' => 'rs_errmsg_for_max_discount_type',
                    'class' => 'rs_errmsg_for_max_discount_type',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Message displayed in Cart When the Order contain only Redeeming', 'rewardsystem'),
                    'desc' => __('Message displayed in Cart When the Order contain only Redeeming', 'rewardsystem'),
                    'id' => 'rs_errmsg_for_redeeming_in_order',
                    'css' => 'min-width:550px;',
                    'std' => 'Since,You Redeemed Your Reward Points in this Order, You Cannot Earn Reward Points For this Order',
                    'default' => 'Since,You Redeemed Your Reward Points in this Order, You Cannot Earn Reward Points For this Order',
                    'type' => 'textarea',
                    'newids' => 'rs_errmsg_for_redeeming_in_order',
                    'class' => 'rs_errmsg_for_redeeming_in_order',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Message displayed in Cart When the Order contain only Coupon', 'rewardsystem'),
                    'desc' => __('Message displayed in Cart When the Order contain only Coupon', 'rewardsystem'),
                    'id' => 'rs_errmsg_for_coupon_in_order',
                    'css' => 'min-width:550px;',
                    'std' => 'Since You have used Coupon in this Order, You Cannot Earn Reward Points For this Order',
                    'default' => 'Since You have used Coupon in this Order, You Cannot Earn Reward Points For this Order',
                    'type' => 'textarea',
                    'newids' => 'rs_errmsg_for_coupon_in_order',
                    'class' => 'rs_errmsg_for_coupon_in_order',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Points/Coupon Redeeming Restriction Message for Point Priced Products', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_errmsg_for_point_price_coupon',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_errmsg_for_point_price_coupon',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Points/Coupon Redeeming Restriction Message for Point Priced Products', 'rewardsystem'),
                    'id' => 'rs_errmsg_for_redeem_in_point_price_prt',
                    'css' => 'min-width:550px;',
                    'std' => 'Points not Redeem for Point Price Product',
                    'default' => 'Points not Redeem for Point Price Product',
                    'type' => 'textarea',
                    'newids' => 'rs_errmsg_for_redeem_in_point_price_prt',
                    'class' => 'rs_errmsg_for_redeem_in_point_price_prt',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide Points Calculation Caution Message', 'rewardsystem'),
                    'id' => 'rs_show_hide_message_notice_for_redeeming',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_show_hide_message_notice_for_redeeming',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Points Calculation Caution Message', 'rewardsystem'),
                    'id' => 'rs_msg_for_redeem_when_tax_enabled',
                    'css' => 'min-width:550px;',
                    'std' => 'Actual Points which can be Redeemed may differ based on Tax Configuration',
                    'default' => 'Actual Points which can be Redeemed may differ based on Tax Configuration',
                    'type' => 'textarea',
                    'newids' => 'rs_msg_for_redeem_when_tax_enabled',
                    'class' => 'rs_msg_for_redeem_when_tax_enabled',
                ),
                array('type' => 'sectionend', 'id' => '_rs_cart_checkout_page_msg'),
                array(
                    'name' => __('Shortcode Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_message_guest',
                ),
                array(
                    'name' => __('Message to be dispalyed if the User is not Logged In', 'rewardsystem'),
                    'id' => 'rs_message_shortcode_guest_display',
                    'css' => 'min-width:550px;',
                    'std' => 'Please Login to View the Contents of this Page',
                    'default' => 'Please Login to View the Contents of this Page',
                    'type' => 'text',
                    'newids' => 'rs_message_shortcode_guest_display',
                    'class' => 'rs_message_shortcode_guest_display',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Login Name', 'rewardsystem'),
                    'id' => 'rs_message_shortcode_login_name',
                    'css' => 'min-width:550px;',
                    'std' => 'Login',
                    'default' => 'Login',
                    'type' => 'text',
                    'newids' => 'rs_message_shortcode_login_name',
                    'class' => 'rs_message_shortcode_login_name',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_message_guest'),
                array(
                    'name' => __('Unsubscription Link Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_unsub_link',
                ),
                array(
                    'name' => __('Unsubscribe Link Message', 'rewardsystem'),
                    'desc' => __('This link is to unsubscribe your email', 'rewardsystem'),
                    'id' => 'rs_unsubscribe_link_for_email',
                    'css' => 'min-width:550px;',
                    'std' => 'If you want to unsubscribe from SUMO Reward Points Emails,click here...{rssitelinkwithid}',
                    'default' => 'If you want to unsubscribe from SUMO Reward Points Emails,click here...{rssitelinkwithid}',
                    'type' => 'textarea',
                    'newids' => 'rs_unsubscribe_link_for_email',
                    'class' => 'rs_unsubscribe_link_for_email',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_unsub_link'),
                array(
                    'name' => __('Cart Error Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_cart_error_msg',
                ),
                array(
                    'name' => __('Error Message to add Normal Product with Point Price Product', 'rewardsystem'),
                    'desc' => __('Message displayed in cart when to add normal product with point price product', 'rewardsystem'),
                    'id' => 'rs_errmsg_for_normal_product_with_point_price',
                    'css' => 'min-width:550px;',
                    'std' => 'Cannot add normal product with point pricing product',
                    'default' => 'Cannot add normal product with point pricing product',
                    'type' => 'textarea',
                    'newids' => 'rs_errmsg_for_normal_product_with_point_price',
                    'class' => 'rs_errmsg_for_normal_product_with_point_price',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to add Point Price Product with Normal Product', 'rewardsystem'),
                    'desc' => __('Message displayed in cart when to add point price product with normal product', 'rewardsystem'),
                    'id' => 'rs_errmsg_for_point_price_product_with_normal',
                    'css' => 'min-width:550px;',
                    'std' => 'Cannot Purchase Point Pricing Product with Normal product',
                    'default' => 'Cannot Purchase Point Pricing Product with Normal product',
                    'type' => 'textarea',
                    'newids' => 'rs_errmsg_for_point_price_product_with_normal',
                    'class' => 'rs_errmsg_for_point_price_product_with_normal',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Same Point Priced Product added twice to Cart Error Message', 'rewardsystem'),
                    'desc' => __('Message displayed in cart when to add same point price product ', 'rewardsystem'),
                    'id' => 'rs_errmsg_for_point_price_product_with_same',
                    'css' => 'min-width:550px;',
                    'std' => 'You cannot add same product to cart',
                    'default' => 'You cannot add same product to cart',
                    'type' => 'textarea',
                    'newids' => 'rs_errmsg_for_point_price_product_with_same',
                    'class' => 'rs_errmsg_for_point_price_product_with_same',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_cart_error_msg'),
                array(
                    'name' => __('Shortcodes used in Messages', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_shortcode_in_messages',
                ),
                array(
                    'type' => 'title',
                    'desc' => '<b>For Single Product Page – Simple Product</b><br><br>
                        <b>[rewardpoints]</b> - To display earn points<br><br>
                        <b>[equalamount]</b> - To display currency value equivalent of earn points<br><br>
                        <b>For Single Product Page – Variable Product</b><br><br>
                        <b>[variationrewardpoints]</b> - To display earn points<br><br>
                        <b>[variationpointsvalue]</b> - To display currency value equivalent of earn points<br><br>
                        <b>For Cart/Checkout Page</b><br><br>
                        <b>[loginlink]</b> - To display login link for guests<br><br>
                        <b>[rspoint]</b> - To display earning points for each product<br><br>
                        <b>[carteachvalue]</b> - To display currency value equivalent of earning points for each product<br><br>
                        <b>[totalrewards]</b> - To display total earning points<br><br>
                        <b>[totalrewardsvalue]</b> - To display currency value equivalent of total earning points<br><br>
                        <b>[userpoints]</b> - To display total available points<br><br>
                        <b>[userpoints_value]</b> - To display currency value equivalent of total available points<br><br>
                        <b>[redeempoints]</b> - To display points redeemed<br><br>
                        <b>[redeemeduserpoints]</b> - To display available points after redeeming<br><br>
                        <b>{rssitelinkwithid}</b> - To display unsubscribe link from emails<br><br>
                        <b>[paymentgatewaytitle]</b> - To display payment gateway title in Checkout<br><br>
                        <b>[paymentgatewaypoints]</b> - To display sumo reward points payment gateway points in Checkout<br><br>
                        <b>[percentage]</b> - To display maximum threshold value to redeem',
                ),
                array('type' => 'sectionend', 'id' => 'rs_shortcode_in_messages'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSMessage::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSMessage::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSMessage::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function rs_function_to_reset_message_tab() {
            $settings = RSMessage::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

    }

    RSMessage::init();
}