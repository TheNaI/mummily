<?php

/*
 * Cart Tab Settings
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSCart')) {

    class RSCart {

        public static function init() {

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'), 999);

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings

            add_action('woocommerce_rs_settings_tabs_rewardsystem_cart', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab

            add_action('woocommerce_update_options_rewardsystem_cart', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system

            if (class_exists('sumorewardcoupons')) {
                add_filter('woocommerce_rewardsystem_cart_settings', array(__CLASS__, 'setting_for_sumo_coupons'));
            }

            if (class_exists('SUMODiscounts')) {
                add_filter('woocommerce_rewardsystem_cart_settings', array(__CLASS__, 'setting_for_hide_redeem_field_when_sumo_discount_is_active'));
            }

            add_action('admin_head', array(__CLASS__, 'validate_maximum_minimum'), 10);

            add_action('admin_head', array(__CLASS__, 'rs_redeeming_selected_products_categories'));

            add_action('woocommerce_admin_field_exclude_product_selection', array(__CLASS__, 'rs_select_product_to_exclude'));

            add_action('woocommerce_admin_field_include_product_selection', array(__CLASS__, 'rs_select_product_to_include'));

            add_action('fp_action_to_reset_settings_rewardsystem_cart', array(__CLASS__, 'rs_function_to_reset_cart_tab'));
        }

        public static function setting_for_sumo_coupons($settings) {
            $updated_settings = array();
            foreach ($settings as $section) {
                $updated_settings[] = $section;
                if (isset($section['id']) && '_rs_cart_dont_allow_redeem' == $section['id'] &&
                        isset($section['type']) && 'sectionend' == $section['type']) {
                    $updated_settings[] = array(
                        'type' => 'title',
                        'id' => '_rs_reward_points_sumo_coupon',
                    );
                    $updated_settings[] = array(
                        'name' => __('Don\'t allow Earn Points when SUMO Coupon is applied', 'rewardsystem'),
                        'desc' => __(' Don\'t allow Earn Points when SUMO Coupon is applied', 'rewardsystem'),
                        'id' => '_rs_not_allow_earn_points_if_sumo_coupon',
                        'css' => 'min-width:550px;',
                        'type' => 'checkbox',
                        'std' => 'no',
                        'default' => 'no',
                        'newids' => '_rs_not_allow_earn_points_if_sumo_coupon',
                    );

                    $updated_settings[] = array(
                        'name' => __('Don\'t allow Redeem when SUMO Coupon is applied', 'rewardsystem'),
                        'desc' => __('Don\'t allow Redeem when SUMO Coupon is applied', 'rewardsystem'),
                        'id' => 'rs_dont_allow_redeem_if_sumo_coupon',
                        'css' => 'min-width:550px;',
                        'type' => 'checkbox',
                        'std' => 'no',
                        'default' => 'no',
                        'newids' => 'rs_dont_allow_redeem_if_sumo_coupon',
                    );


                    $updated_settings[] = array(
                        'type' => 'sectionend',
                        'id' => '_rs_reward_points_sumo_coupon'
                    );
                }
            }
            return $updated_settings;
        }

        public static function setting_for_hide_redeem_field_when_sumo_discount_is_active($settings) {

            $updated_settings = array();

            foreach ($settings as $section) {
                if (isset($section['id']) && ('rs_cart_setting' === $section['id']) &&
                        isset($section['type']) && ('sectionend' === $section['type'])) {
                    $updated_settings[] = array(
                        'name' => __('Show Redeeming Field', 'rewardsystem'),
                        'id' => 'rs_show_redeeming_field',
                        'std' => '1',
                        'default' => '1',
                        'type' => 'select',
                        'newids' => 'rs_show_redeeming_field',
                        'options' => array(
                            '1' => __('Always', 'rewardsystem'),
                            '2' => __('When Price is not altered through SUMO Discounts Plugin', 'rewardsystem'),
                        ),
                        'desc_tip' => true,
                    );
                }
                $updated_settings[] = $section;
            }
            return $updated_settings;
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_cart'] = __('Cart', 'rewardsystem');
            return $setting_tabs;
        }

        public static function reward_system_admin_fields() {
            global $woocommerce;
            $categorylist = fp_rs_get_product_category();
            return apply_filters('woocommerce_rewardsystem_cart_settings', array(
                array(
                    'type' => 'title',
                    'id' => 'rs_cart_setting',
                ),
                array(
                    'name' => __('Apply Redeeming Before Tax', 'rewardsystem'),
                    'desc' => 'Works with WooCommerce Versions 2.2 or older',
                    'id' => 'rs_apply_redeem_before_tax',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_apply_redeem_before_tax',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                    'desc_tip' => false,
                ),
                array(
                    'name' => __('Free Shipping when Reward Points is Redeemed', 'rewardsystem'),
                    'id' => 'rs_apply_shipping_tax',
                    'css' => 'min-width:150px;',
                    'std' => '2',
                    'default' => '2',
                    'type' => 'select',
                    'newids' => 'rs_apply_shipping_tax',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeeming/WooCommerce Coupon Field display', 'rewardsystem'),
                    'id' => 'rs_show_hide_redeem_field',
                    'css' => '',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_show_hide_redeem_field',
                    'options' => array(
                        '1' => __('Display Both', 'rewardsystem'),
                        '2' => __('Hide coupon', 'rewardsystem'),
                        '3' => __('Hide Redeem', 'rewardsystem'),
                        '4' => __('Hide Both', 'rewardsystem'),
                        '5' => __('Hide one when use', 'rewardsystem')
                    ),
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => 'rs_cart_setting'),
                array(
                    'type' => 'title',
                    'id' => 'rs_cart_setting1',
                ),
                array(
                    'name' => __('Points that can be Earned Message display in Cart Totals Table', 'rewardsystem'),
                    'id' => 'rs_show_hide_total_points_cart_field',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_show_hide_total_points_cart_field',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Points Earned in Order Caption', 'rewardsystem'),
                    'id' => 'rs_total_earned_point_caption',
                    'css' => 'min-width:150px;',
                    'std' => 'Points that can be earned:',
                    'default' => 'Points that can be earned:',
                    'type' => 'text',
                    'newids' => 'rs_total_earned_point_caption',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeemed Points is applied on', 'rewardsystem'),
                    'id' => 'rs_apply_redeem_basedon_cart_or_product_total',
                    'newids' => 'rs_apply_redeem_basedon_cart_or_product_total',
                    'class' => 'rs_apply_redeem_basedon_cart_or_product_total',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Cart Subtotal', 'rewardsystem'),
                        '2' => __('Product Total', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Enable Redeeming for Selected Products', 'rewardsystem'),
                    'desc' => __('Enable this option to allow redeeming for selected product(s)', 'rewardsystem'),
                    'id' => 'rs_enable_redeem_for_selected_products',
                    'css' => 'min-width:150px;',
                    'type' => 'checkbox',
                    'newids' => 'rs_enable_redeem_for_selected_products',
                ),
                array(
                    'type' => 'include_product_selection',
                ),
                array(
                    'name' => __('Exclude Products for Redeeming', 'rewardsystem'),
                    'desc' => __('Enable this option to prevent redeeming for selected product(s)', 'rewardsystem'),
                    'id' => 'rs_exclude_products_for_redeeming',
                    'class' => 'rs_exclude_products_for_redeeming',
                    'std' => '',
                    'default' => '',
                    'type' => 'checkbox',
                    'newids' => 'rs_exclude_products_for_redeeming',
                ),
                array(
                    'type' => 'exclude_product_selection',
                ),
                array(
                    'name' => __('Enable Redeeming for Selected Category', 'rewardsystem'),
                    'desc' => __('Enable this option to allow redeeming for selected category', 'rewardsystem'),
                    'id' => 'rs_enable_redeem_for_selected_category',
                    'class' => 'rs_enable_redeem_for_selected_category',
                    'std' => '',
                    'default' => '',
                    'type' => 'checkbox',
                    'newids' => 'rs_enable_redeem_for_selected_category',
                ),
                array(
                    'name' => __('Select Category', 'rewardsystem'),
                    'desc' => __('Select Category to enable redeeming', 'rewardsystem'),
                    'id' => 'rs_select_category_to_enable_redeeming',
                    'class' => 'rs_select_category_to_enable_redeeming',
                    'css' => 'min-width:350px',
                    'std' => '',
                    'default' => '',
                    'type' => 'multiselect',
                    'newids' => 'rs_select_category_to_enable_redeeming',
                    'options' => $categorylist,
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Exclude Category for Redeeming', 'rewardsystem'),
                    'desc' => __('Enable this option to prevent redeeming for selected category', 'rewardsystem'),
                    'id' => 'rs_exclude_category_for_redeeming',
                    'std' => '',
                    'default' => '',
                    'type' => 'checkbox',
                    'newids' => 'rs_exclude_category_for_redeeming',
                ),
                array(
                    'name' => __('Select Category', 'rewardsystem'),
                    'desc' => __('Select Category to enable redeeming', 'rewardsystem'),
                    'id' => 'rs_exclude_category_to_enable_redeeming',
                    'class' => 'rs_exclude_category_to_enable_redeeming',
                    'css' => 'min-width:350px',
                    'std' => '',
                    'default' => '',
                    'type' => 'multiselect',
                    'newids' => 'rs_exclude_category_to_enable_redeeming',
                    'options' => $categorylist,
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeeming Field Type', 'rewardsystem'),
                    'id' => 'rs_redeem_field_type_option',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_redeem_field_type_option',
                    'options' => array(
                        '1' => __('Default', 'rewardsystem'),
                        '2' => __('Button', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Percentage of Cart Total to be Redeemed', 'rewardsystem'),
                    'desc' => __('Enter the Percentage of the cart total that has to be Redeemed', 'rewardsystem'),
                    'id' => 'rs_percentage_cart_total_redeem',
                    'css' => 'min-width:150px;',
                    'std' => '100 ',
                    'default' => '100',
                    'type' => 'text',
                    'newids' => 'rs_percentage_cart_total_redeem',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeeming Button Notice', 'rewardsystem'),
                    'desc' => __('Enter the Message for the Redeeming Button', 'rewardsystem'),
                    'id' => 'rs_redeeming_button_option_message',
                    'css' => 'min-width:550px;',
                    'std' => '[cartredeempoints] points worth of [currencysymbol] [pointsvalue] will be Redeemed',
                    'default' => '[cartredeempoints] points worth of [currencysymbol] [pointsvalue] will be Redeemed',
                    'type' => 'textarea',
                    'newids' => 'rs_redeeming_button_option_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Auto Redeeming of Points in Cart', 'rewardsystem'),
                    'desc' => __('Enable this option to redeem points automatically in cart when the product is added', 'rewardsystem'),
                    'id' => 'rs_enable_disable_auto_redeem_points',
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'newids' => 'rs_enable_disable_auto_redeem_points',
                ),
                array(
                    'name' => __('Auto Redeeming of Points in Checkout', 'rewardsystem'),
                    'desc' => __('Enable this option to redeem points automatically in checkout when the page is redirected to checkout directly from shop page', 'rewardsystem'),
                    'id' => 'rs_enable_disable_auto_redeem_checkout',
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'newids' => 'rs_enable_disable_auto_redeem_checkout',
                ),
                array(
                    'name' => __('Maximum Redeeming Threshold Percentage for Auto Redeeming', 'rewardsystem'),
                    'desc' => __('Enter the Percentage of the cart total that has to be Auto Redeemed', 'rewardsystem'),
                    'id' => 'rs_percentage_cart_total_auto_redeem',
                    'css' => 'min-width:150px;',
                    'std' => '100 ',
                    'default' => '100',
                    'type' => 'text',
                    'newids' => 'rs_percentage_cart_total_auto_redeem',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => 'rs_cart_setting1'),
                array(
                    'type' => 'title',
                    'desc' => '',
                    'id' => '_rs_cart_dont_allow_redeem'
                ),
                array(
                    'name' => __('Restrict Product Purchase Reward Points when Reward Points is Redeemed', 'rewardsystem'),
                    'desc' => __('Enable this option to prevent earning points in an order when the points are redeemed', 'rewardsystem'),
                    'id' => 'rs_enable_redeem_for_order',
                    'css' => 'min-width:150px;',
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'newids' => 'rs_enable_redeem_for_order',
                ),
                array(
                    'name' => __('Restrict Product Purchase Reward Points when WooCommerce Coupon is applied', 'rewardsystem'),
                    'desc' => __('Enable this option to prevent earning points in an order when coupon is applied', 'rewardsystem'),
                    'id' => 'rs_disable_point_if_coupon',
                    'css' => 'min-width:150px;',
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'newids' => 'rs_disable_point_if_coupon',
                ),
                array(
                    'name' => __('Restrict Product Purchase Reward Points when SUMO Reward Points Payment Gateway is used', 'rewardsystem'),
                    'desc' => __('Enable this option to prevent earning points in an order when SUMO Reward Points Payment Gateway is used', 'rewardsystem'),
                    'id' => 'rs_disable_point_if_reward_points_gateway',
                    'css' => 'min-width:150px;',
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'newids' => 'rs_disable_point_if_reward_points_gateway',
                ),
                array(
                    'name' => __('Message to display when using SUMO Reward Points Payment Gateway to restrict earn points', 'rewardsystem'),
                    'desc' => __('Enter the message to display when using SUMO Reward Points Gateway', 'rewardsystem'),
                    'id' => 'rs_restriction_msg_for_reward_gatweway',
                    'css' => 'min-width:150px;',
                    'type' => 'textarea',
                    'std' => 'You cannot earn points if you use [paymentgatewaytitle] Gateway',
                    'default' => 'You cannot earn points if you use [paymentgatewaytitle] Gateway',
                    'newids' => 'rs_restriction_msg_for_reward_gatweway',
                    'desc_tip' => true
                ),
                array('type' => 'sectionend', 'id' => '_rs_cart_dont_allow_redeem'),
                array(
                    'type' => 'title',
                    'desc' => '',
                    'id' => '_rs_cart_remaining_setting'
                ),
                array(
                    'name' => __('Minimum Points required for Redeeming for the First Time', 'rewardsystem'),
                    'desc' => __('Enter Minimum Points to be Earned for Redeeming First Time in Cart/Checkout', 'rewardsystem'),
                    'id' => 'rs_first_time_minimum_user_points',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_first_time_minimum_user_points',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('First Time Redeeming Minimum Points', 'rewardsystem'),
                    'id' => 'rs_show_hide_first_redeem_error_message',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_first_redeem_error_message',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Error Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when the user doesn\'t have enough points for first time redeeming', 'rewardsystem'),
                    'id' => 'rs_min_points_first_redeem_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'You need Minimum of [firstredeempoints] Points when redeeming for the First time',
                    'default' => 'You need Minimum of [firstredeempoints] Points when redeeming for the First time',
                    'type' => 'textarea',
                    'newids' => 'rs_min_points_first_redeem_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Minimum Points required for Redeeming after First Redeeming', 'rewardsystem'),
                    'desc' => __('Enter Minimum Balance Points for Redeeming in Cart/Checkout', 'rewardsystem'),
                    'id' => 'rs_minimum_user_points_to_redeem',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_minimum_user_points_to_redeem',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Minimum Points required for Redeeming after First Redeeming', 'rewardsystem'),
                    'id' => 'rs_show_hide_after_first_redeem_error_message',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_after_first_redeem_error_message',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Error Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when the Current User doesn\'t have minimum points for Redeeming ', 'rewardsystem'),
                    'id' => 'rs_min_points_after_first_error',
                    'css' => 'min-width:550px;',
                    'std' => 'You need minimum of [points_after_first_redeem] Points for Redeeming',
                    'default' => 'You need minimum of [points_after_first_redeem] Points for Redeeming',
                    'type' => 'textarea',
                    'newids' => 'rs_min_points_after_first_error',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Minimum Points to Redeem in Cart', 'rewardsystem'),
                    'desc' => __('Enter Minimum Points for Redeeming', 'rewardsystem'),
                    'id' => 'rs_minimum_redeeming_points',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_minimum_redeeming_points',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Maximum Points which can be Redeemed in Cart', 'rewardsystem'),
                    'desc' => __('Enter Maximum Points for Redeeming', 'rewardsystem'),
                    'id' => 'rs_maximum_redeeming_points',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_maximum_redeeming_points',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when User enters less than Minimum Points[Default Type]', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when Entered Points is less than Minimum Redeeming Points which is set in this Page', 'rewardsystem'),
                    'id' => 'rs_minimum_redeem_point_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Please Enter Points more than [rsminimumpoints]',
                    'default' => 'Please Enter Points more than [rsminimumpoints]',
                    'type' => 'text',
                    'newids' => 'rs_minimum_redeem_point_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when User enters more than Maximum Points[Default Type]', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when Entered Points is more than Maximum Redeeming Points which is set in this Page', 'rewardsystem'),
                    'id' => 'rs_maximum_redeem_point_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Please Enter Points less than [rsmaximumpoints]',
                    'default' => 'Please Enter Points less than [rsmaximumpoints]',
                    'type' => 'text',
                    'newids' => 'rs_maximum_redeem_point_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when User enters less than the Minimum Points  or more than Maximum Points[Default Type]', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when Maximum and Minimum Redeeming Points are Equal which is set in this Page', 'rewardsystem'),
                    'id' => 'rs_minimum_and_maximum_redeem_point_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Please Enter [rsequalpoints] Points',
                    'default' => 'Please Enter [rsequalpoints] Points',
                    'type' => 'text',
                    'newids' => 'rs_minimum_and_maximum_redeem_point_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when User enters less than Minimum Points[Button Type]', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when Entered Points is less than Minimum Redeeming Points which is set in this Page', 'rewardsystem'),
                    'id' => 'rs_minimum_redeem_point_error_message_for_button_type',
                    'css' => 'min-width:550px;',
                    'std' => 'You cannot redeem because the current points to be redeemed is less than [rsminimumpoints] Points',
                    'default' => 'You cannot redeem because the current points to be redeemed is less than [rsminimumpoints] Points',
                    'type' => 'text',
                    'newids' => 'rs_minimum_redeem_point_error_message_for_button_type',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when User enters more than Maximum Points[Button Type]', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when Entered Points is more than Maximum Redeeming Points which is set in this Page', 'rewardsystem'),
                    'id' => 'rs_maximum_redeem_point_error_message_for_button_type',
                    'css' => 'min-width:550px;',
                    'std' => 'You cannot redeem because the current points to be redeemed is more than [rsmaximumpoints] points',
                    'default' => 'You cannot redeem because the current points to be redeemed is more than [rsmaximumpoints] points',
                    'type' => 'text',
                    'newids' => 'rs_maximum_redeem_point_error_message_for_button_type',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when User enters less than the Minimum Points  or more than Maximum Points[Button Type]', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when Maximum and Minimum Redeeming Points are Equal which is set in this Page', 'rewardsystem'),
                    'id' => 'rs_minimum_and_maximum_redeem_point_error_message_for_buttontype',
                    'css' => 'min-width:550px;',
                    'std' => 'You cannot redeem because the points to be redeemed is not equal to [rsequalpoints] Points ',
                    'default' => 'You cannot redeem because the points to be redeemed is not equal to [rsequalpoints] Points',
                    'type' => 'text',
                    'newids' => 'rs_minimum_and_maximum_redeem_point_error_message_for_buttontype',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Minimum Cart Total to Earn Point(s)', 'rewardsystem'),
                    'desc' => __('Enter Minimum Cart Total for Earned Points', 'rewardsystem'),
                    'id' => 'rs_minimum_cart_total_for_earning',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_minimum_cart_total_for_earning',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Minimum Cart Total to Earn Point(s)', 'rewardsystem'),
                    'id' => 'rs_show_hide_minimum_cart_total_earn_error_message',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_minimum_cart_total_earn_error_message',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Error Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when the user doesn\'t have enough Cart Total for Earning', 'rewardsystem'),
                    'id' => 'rs_min_cart_total_for_earning_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'You need Minimum of [carttotal] carttotal to Earn Points',
                    'default' => 'You need Minimum of [carttotal] carttotal to Earn Points',
                    'type' => 'textarea',
                    'newids' => 'rs_min_cart_total_for_earning_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Maximum Cart Total to Earn Point(s)', 'rewardsystem'),
                    'desc' => __('Enter Maximum Cart Total to Earn Points', 'rewardsystem'),
                    'id' => 'rs_maximum_cart_total_for_earning',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_maximum_cart_total_for_earning',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Maximum Cart Total to Earn Point(s)', 'rewardsystem'),
                    'id' => 'rs_show_hide_maximum_cart_total_earn_error_message',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_maximum_cart_total_earn_error_message',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Error Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when the user doesn\'t have enough Maximum Cart Total for Earning', 'rewardsystem'),
                    'id' => 'rs_max_cart_total_for_earning_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'You Cannot Earn Points Because you Reach the Maximum Cart total [carttotal] for earn Points',
                    'default' => 'You Cannot Earn Points Because you Reach the Maximum Cart total [carttotal] for earn Points',
                    'type' => 'textarea',
                    'newids' => 'rs_max_cart_total_for_earning_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Minimum Cart Total to Redeem Point(s)', 'rewardsystem'),
                    'desc' => __('Enter Minimum Cart Total for Redeeming', 'rewardsystem'),
                    'id' => 'rs_minimum_cart_total_points',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_minimum_cart_total_points',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Minimum Cart Total to Redeem Point(s)', 'rewardsystem'),
                    'id' => 'rs_show_hide_minimum_cart_total_error_message',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_minimum_cart_total_error_message',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Error Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when current Cart total is less than minimum Cart Total for Redeeming', 'rewardsystem'),
                    'id' => 'rs_min_cart_total_redeem_error',
                    'css' => 'min-width:550px;',
                    'std' => 'You need minimum cart Total of [currencysymbol][carttotal] in order to Redeem',
                    'default' => 'You need minimum cart Total of [currencysymbol][carttotal] in order to Redeem',
                    'type' => 'textarea',
                    'newids' => 'rs_min_cart_total_redeem_error',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Maximum Cart Total to Redeem Point(s)', 'rewardsystem'),
                    'desc' => __('Enter Maximum Cart Total for Redeeming', 'rewardsystem'),
                    'id' => 'rs_maximum_cart_total_points',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_maximum_cart_total_points',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Maximum Cart Total to Redeem Point(s)', 'rewardsystem'),
                    'id' => 'rs_show_hide_maximum_cart_total_error_message',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_maximum_cart_total_error_message',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Error Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when current Cart total is less than Maximum Cart Total for Redeeming', 'rewardsystem'),
                    'id' => 'rs_max_cart_total_redeem_error',
                    'css' => 'min-width:550px;',
                    'std' => 'You Cannot Redeem Points Because you Reach the Maximum Cart total [currencysymbol][carttotal]',
                    'default' => 'You Cannot Redeem Points Because you Reach the Maximum Cart total [currencysymbol][carttotal]',
                    'type' => 'textarea',
                    'newids' => 'rs_max_cart_total_redeem_error',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeeming Field Label', 'rewardsystem'),
                    'id' => 'rs_show_hide_redeem_caption',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_redeem_caption',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Redeeming Field Label', 'rewardsystem'),
                    'desc' => __('Enter the Label which will be displayed in Redeem Field', 'rewardsystem'),
                    'id' => 'rs_redeem_field_caption',
                    'css' => 'min-width:550px;',
                    'std' => 'Redeem your Reward Points:',
                    'default' => 'Redeem your Reward Points:',
                    'type' => 'text',
                    'newids' => 'rs_redeem_field_caption',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeeming Field Placeholder', 'rewardsystem'),
                    'id' => 'rs_show_hide_redeem_placeholder',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_redeem_placeholder',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Placeholder', 'rewardsystem'),
                    'desc' => __('Enter the Placeholder which will be displayed in Redeem Field', 'rewardsystem'),
                    'id' => 'rs_redeem_field_placeholder',
                    'css' => 'min-width:550px;',
                    'std' => 'Reward Points to Enter',
                    'default' => 'Reward Points to Enter',
                    'type' => 'text',
                    'newids' => 'rs_redeem_field_placeholder',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeeming Field Submit Button Caption', 'rewardsystem'),
                    'desc' => __('Enter the Label which will be displayed in Submit Button', 'rewardsystem'),
                    'id' => 'rs_redeem_field_submit_button_caption',
                    'css' => 'min-width:550px;margin-bottom:40px;',
                    'std' => 'Apply Reward Points',
                    'default' => 'Apply Reward Points',
                    'type' => 'text',
                    'newids' => 'rs_redeem_field_submit_button_caption',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_cart_remaining_setting'),
                array(
                    'name' => __('Cart Redeem Error Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_cart_redeem_error_settings'
                ),
                array(
                    'name' => __('Redeeming Field Empty Error Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when Redeem Field has Empty Value', 'rewardsystem'),
                    'id' => 'rs_redeem_empty_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'No Reward Points Entered',
                    'default' => 'No Reward Points Entered',
                    'type' => 'text',
                    'newids' => 'rs_redeem_empty_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Unwanted Characters in Redeeming Field Error Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when redeeming field value contain characters', 'rewardsystem'),
                    'id' => 'rs_redeem_character_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Please Enter Only Numbers',
                    'default' => 'Please Enter Only Numbers',
                    'type' => 'text',
                    'newids' => 'rs_redeem_character_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Insufficient Points for Redeeming Error Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when Entered Reward Points is more than Earned Reward Points', 'rewardsystem'),
                    'id' => 'rs_redeem_max_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Reward Points you entered is more than Your Earned Reward Points ',
                    'default' => 'Reward Points you entered is more than Your Earned Reward Points ',
                    'type' => 'text',
                    'newids' => 'rs_redeem_max_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Current User Points is Empty Error Message', 'rewardsystem'),
                    'id' => 'rs_show_hide_points_empty_error_message',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_points_empty_error_message',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Error Message', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when the Current User Points is Empty', 'rewardsystem'),
                    'id' => 'rs_current_points_empty_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'You don\'t have Points for Redeeming',
                    'default' => 'You don\'t have Points for Redeeming',
                    'type' => 'text',
                    'newids' => 'rs_current_points_empty_error_message',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_cart_redeem_error_settings'),
                array(
                    'name' => __('Coupon Label Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_cart_redeem_error_settings'
                ),
                array(
                    'name' => __('Coupon Label Settings', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed in Cart Subtotal', 'rewardsystem'),
                    'id' => 'rs_coupon_label_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Redeemed Points Value',
                    'default' => 'Redeemed Points Value',
                    'type' => 'text',
                    'newids' => 'rs_coupon_label_message',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_cart_redeem_error_settings'),
                array(
                    'name' => __('Extra Class Name for Button', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_cart_custom_class_name',
                ),
                array(
                    'name' => __('Extra Class Name for Redeeming Field Submit Button', 'rewardsystem'),
                    'desc' => __('Add Extra Class Name to the Cart Apply Reward Points Button, Don\'t Enter dot(.) before Class Name', 'rewardsystem'),
                    'id' => 'rs_extra_class_name_apply_reward_points',
                    'css' => 'min-width:550px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_extra_class_name_apply_reward_points',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_cart_custom_class_name'),
                array(
                    'name' => __('Custom CSS Settings', 'rewardsystem'),
                    'type' => 'title',
                    'desc' => 'Try !important if styles doesn\'t apply ',
                    'id' => '_rs_cart_custom_css_settings',
                ),
                array(
                    'name' => __('Custom CSS', 'rewardsystem'),
                    'desc' => __('Enter the Custom CSS for the Cart Page ', 'rewardsystem'),
                    'id' => 'rs_cart_page_custom_css',
                    'css' => 'min-width:350px; min-height:350px;',
                    'std' => '#rs_apply_coupon_code_field { } #mainsubmi { } .fp_apply_reward{ }',
                    'default' => '#rs_apply_coupon_code_field { } #mainsubmi { } .fp_apply_reward{ }',
                    'type' => 'textarea',
                    'newids' => 'rs_cart_page_custom_css',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_cart_custom_css_settings'),
                array(
                    'name' => __('Shortcodes used in Cart', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_shortcodes_in_cart',
                ),
                array(
                    'type' => 'title',
                    'desc' => '<b>[cartredeempoints]</b> - To display points can redeem based on cart total amount<br><br>'
                    . '<b>[currencysymbol]</b> - To display currency symbol<br><br>'
                    . '<b>[pointsvalue]</b> - To display currency value equivalent of redeeming points<br><br>'
                    . '<b>[paymentgatewaytitle]</b> - To display payment gateway title<br><br>'
                    . '<b>[firstredeempoints] </b> - To display points required for first time redeeming<br><br>'
                    . '<b>[points_after_first_redeem]</b> - To display points required after first redeeming<br><br>'
                    . '<b>[rsminimumpoints]</b> - To display minimum points required to redeem<br><br>'
                    . '<b>[rsmaximumpoints]</b> - To display maximum points required to redeem<br><br>'
                    . '<b>[rsequalpoints]</b> - To display exact points to redeem<br><br>'
                    . '<b>[carttotal]</b> - To display cart total value<br><br>',
                ),
                array('type' => 'sectionend', 'id' => 'rs_shortcodes_in_cart'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSCart::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSCart::reward_system_admin_fields());
            update_option('rs_select_products_to_enable_redeeming', $_POST['rs_select_products_to_enable_redeeming']);
            update_option('rs_exclude_products_to_enable_redeeming', $_POST['rs_exclude_products_to_enable_redeeming']);
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSCart::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function validate_maximum_minimum() {
            if (isset($_GET['tab'])) {
                if ($_GET['tab'] == 'rewardsystem_cart') {
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery('#rs_maximum_cart_total_points').keyup(function () {
                                var maximum_cart_total_redeem = jQuery('#rs_maximum_cart_total_points').val();
                                if (maximum_cart_total_redeem != '') {
                                    jQuery('#rs_maximum_cart_total_points').val(maximum_cart_total_redeem);

                                }
                            });
                            jQuery('#rs_minimum_cart_total_points').keyup(function () {
                                var mimimum_cart_total_redeem = jQuery('#rs_minimum_cart_total_points').val();
                                if (mimimum_cart_total_redeem != '') {
                                    jQuery('#rs_minimum_cart_total_points').val(mimimum_cart_total_redeem);
                                }
                            });


                            jQuery('#rs_maximum_cart_total_for_earning').keyup(function () {
                                var maximum_cart_total_earn = jQuery('#rs_maximum_cart_total_for_earning').val();
                                if (maximum_cart_total_earn != '') {
                                    jQuery('#rs_maximum_cart_total_for_earning').val(maximum_cart_total_earn);

                                }
                            });
                            jQuery('#rs_minimum_cart_total_for_earning').keyup(function () {
                                var mimimum_cart_total_earn = jQuery('#rs_minimum_cart_total_for_earning').val();
                                if (mimimum_cart_total_earn != '') {
                                    jQuery('#rs_minimum_cart_total_for_earning').val(mimimum_cart_total_earn);
                                }
                            });

                            jQuery('.button-primary').click(function (e) {
                                if (jQuery('#rs_maximum_cart_total_points').val() != '' && jQuery('#rs_minimum_cart_total_points').val() != '') {
                                    var maximum_cart_total_redeem = Number(jQuery('#rs_maximum_cart_total_points').val());
                                    var mimimum_cart_total_redeem = Number(jQuery('#rs_minimum_cart_total_points').val());
                                    if (maximum_cart_total_redeem < mimimum_cart_total_redeem) {
                                        e.preventDefault();
                                        jQuery('#rs_maximum_cart_total_points').focus();
                                        jQuery("#rs_maximum_cart_total_points").after("<div class='validation' style='color:red;margin-bottom: 20px;'>Please enter cart total greater than mimimum cart total for redeem points</div>");

                                    }
                                }
                                if (jQuery('#rs_maximum_cart_total_for_earning').val() != '' && jQuery('#rs_minimum_cart_total_for_earning').val() != '') {
                                    var maximum_cart_total_redeem = Number(jQuery('#rs_maximum_cart_total_for_earning').val());
                                    var mimimum_cart_total_redeem = Number(jQuery('#rs_minimum_cart_total_for_earning').val());
                                    if (maximum_cart_total_redeem < mimimum_cart_total_redeem) {
                                        e.preventDefault();
                                        jQuery('#rs_maximum_cart_total_for_earning').focus();
                                        jQuery("#rs_maximum_cart_total_for_earning").after("<div class='validation1' style='color:red;margin-bottom: 20px;'>Please enter cart total greater than mimimum cart total for earn points</div>");

                                    }
                                }
                                jQuery('#rs_maximum_cart_total_points').keyup(function () {
                                    jQuery(".validation").hide();

                                });
                                jQuery('#rs_maximum_cart_total_for_earning').keyup(function () {
                                    jQuery(".validation1").hide();
                                });

                            });
                        });
                    </script>
                    <?php

                }
            }
        }

        public static function rs_redeeming_selected_products_categories() {
            global $woocommerce;
            if (isset($_GET['tab'])) {
                if ($_GET['tab'] == 'rewardsystem_cart') {
                    echo rs_common_ajax_function_to_select_products('rs_ajax_chosen_select_products_redeem');
                    if ((float) $woocommerce->version <= (float) ('2.2.0')) {
                        echo rs_common_chosen_function('#rs_select_category_to_enable_redeeming');
                        echo rs_common_chosen_function('#rs_exclude_category_to_enable_redeeming');
                        echo rs_common_chosen_function('#rs_select_category_for_purchase_using_points');
                    } else {
                        echo rs_common_select_function('#rs_select_category_to_enable_redeeming');
                        echo rs_common_select_function('#rs_exclude_category_to_enable_redeeming');
                        echo rs_common_select_function('#rs_select_category_for_purchase_using_points');
                    }
                }
            }
        }

        /*
         * Function to select products to exclude
         */

        public static function rs_select_product_to_exclude() {
            $field_id = "rs_exclude_products_to_enable_redeeming";
            $field_label = "Select Product(s)";
            $getproducts = get_option('rs_exclude_products_to_enable_redeeming');
            echo rs_function_to_add_field_for_product_select($field_id, $field_label, $getproducts);
        }

        /*
         * Function to select products to include
         */

        public static function rs_select_product_to_include() {
            $field_id = "rs_select_products_to_enable_redeeming";
            $field_label = "Select Product(s)";
            $getproducts = get_option('rs_select_products_to_enable_redeeming');
            echo rs_function_to_add_field_for_product_select($field_id, $field_label, $getproducts);
        }

        public static function rs_function_to_reset_cart_tab() {
            $settings = RSCart::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

    }

    RSCart::init();
}