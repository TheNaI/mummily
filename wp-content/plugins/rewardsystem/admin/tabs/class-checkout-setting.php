<?php

/*
 * Checkout Tab Settings
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSCheckout')) {

    class RSCheckout {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_checkout', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_checkout', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));

            add_action('admin_head', array(__CLASS__, 'rs_purchase_product_using_point'));

            add_action('woocommerce_admin_field_rs_product_for_purchase', array(__CLASS__, 'rs_purchase_selected_product_using_points'));

            add_action('woocommerce_admin_field_rs_hide_gateway', array(__CLASS__, 'rs_selected_product_hide_gateway'));

            add_action('woocommerce_update_option_rs_hide_gateway', array(__CLASS__, 'save_selected_product_hide_gateway'));

            add_action('fp_action_to_reset_settings_rewardsystem_checkout', array(__CLASS__, 'rs_function_to_reset_checkout_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_checkout'] = __('Checkout', 'rewardsystem');
            return $setting_tabs;
        }

        public static function reward_system_admin_fields() {
            $categorylist = fp_rs_get_product_category();
            return apply_filters('woocommerce_rewardsystem_checkout_settings', array(
                array(
                    'name' => __('Checkout Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_checkout_setting',
                ),
                array(
                    'name' => __('Redeeming Field in Checkout Page', 'rewardsystem'),
                    'desc' => __('Show/Hide Redeeming Field in Checkout Page of WooCommerce', 'rewardsystem'),
                    'id' => 'rs_show_hide_redeem_field_checkout',
                    'std' => '2',
                    'default' => '2',
                    'type' => 'select',
                    'newids' => 'rs_show_hide_redeem_field_checkout',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Points for Current Order in Order Details Page', 'rewardsystem'),
                    'id' => 'rs_show_hide_total_points_checkout_field',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_show_hide_total_points_checkout_field',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Current Order Points Caption', 'rewardsystem'),
                    'id' => 'rs_total_earned_point_caption_checkout',
                    'css' => 'min-width:150px;',
                    'std' => 'Points that can be earned:',
                    'default' => 'Points that can be earned:',
                    'type' => 'text',
                    'newids' => 'rs_total_earned_point_caption_checkout',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeeming Field Type', 'rewardsystem'),
                    'desc' => __('Select the type of Redeeming to used in Checkout Page of WooCommerce', 'rewardsystem'),
                    'id' => 'rs_redeem_field_type_option_checkout',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_redeem_field_type_option_checkout',
                    'options' => array(
                        '1' => __('Default', 'rewardsystem'),
                        '2' => __('Button', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Coupon Field', 'rewardsystem'),
                    'id' => 'rs_show_hide_coupon_field_checkout',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_show_hide_coupon_field_checkout',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Percentage of Cart Total to be Redeemed', 'rewardsystem'),
                    'desc' => __('Enter the Percentage of the cart total that has to be Redeemed', 'rewardsystem'),
                    'id' => 'rs_percentage_cart_total_redeem_checkout',
                    'css' => 'min-width:550px;',
                    'std' => '100 ',
                    'default' => '100',
                    'type' => 'text',
                    'newids' => 'rs_percentage_cart_total_redeem_checkout',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeeming Field label', 'rewardsystem'),
                    'desc' => __('This Text will be displayed as redeeming field label in checkout page', 'rewardsystem'),
                    'id' => 'rs_reedming_field_label_checkout',
                    'css' => 'min-width:550px;',
                    'std' => 'Have Reward Points ?',
                    'default' => 'Have Reward Points ?',
                    'type' => 'text',
                    'newids' => 'rs_reedming_field_label_checkout',
                    'class' => 'rs_reedming_field_label_checkout',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeeming Field Link label', 'rewardsystem'),
                    'desc' => __('This Text will be displayed as redeeming field link label in checkout page', 'rewardsystem'),
                    'id' => 'rs_reedming_field_link_label_checkout',
                    'css' => 'min-width:550px;',
                    'std' => 'Redeem it',
                    'default' => 'Redeem it',
                    'type' => 'text',
                    'newids' => 'rs_reedming_field_link_label_checkout',
                    'class' => 'rs_reedming_field_link_label_checkout',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeeming Link Call to Action', 'rewardsystem'),
                    'desc' => __('Show/Hide Redeem It Link Call To Action in WooCommerce', 'rewardsystem'),
                    'id' => 'rs_show_hide_redeem_it_field_checkout',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_show_hide_redeem_it_field_checkout',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('SUMO Reward Points Payment Gateway is', 'rewardsystem'),
                    'desc' => __('SUMO Reward Points Payment Gateway is Visible or Hidden for Selected Products And Categories', 'rewardsystem'),
                    'id' => 'rs_show_hide_reward_points_gatewy',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_show_hide_reward_points_gatewy',
                    'options' => array(
                        '1' => __('Visible for Selected Products/Categories', 'rewardsystem'),
                        '2' => __('Hidden for Selected Products/Categories', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Product Purchase Using SUMO Reward Points Payment Gateway for Selected Product(s)', 'rewardsystem'),
                    'desc' => __('Enable this option to purchase the selected product(s) using SUMO Reward Points Payment Gateway', 'rewardsystem'),
                    'id' => 'rs_enable_selected_product_for_purchase_using_points',
                    'class' => 'rs_enable_selected_product_for_purchase_using_points',
                    'newids' => 'rs_enable_selected_product_for_purchase_using_points',
                    'type' => 'checkbox',
                ),
                array(
                    'type' => 'rs_product_for_purchase',
                ),
                array(
                    'name' => __('Product Purchase Using SUMO Reward Points Payment Gateway for Selected Category', 'rewardsystem'),
                    'desc' => __('Enable this option to purchase the product(s) in selected category using SUMO Reward Points Payment Gateway', 'rewardsystem'),
                    'id' => 'rs_enable_selected_category_for_purchase_using_points',
                    'class' => 'rs_enable_selected_category_for_purchase_using_points',
                    'newids' => 'rs_enable_selected_category_for_purchase_using_points',
                    'type' => 'checkbox',
                ),
                array(
                    'name' => __('Select Category', 'rewardsystem'),
                    'desc' => __('Select Categories for Purchase Using SUMO Reward Points Payment Gateway', 'rewardsystem'),
                    'id' => 'rs_select_category_for_purchase_using_points',
                    'class' => 'rs_select_category_for_purchase_using_points',
                    'css' => 'min-width:350px',
                    'std' => '',
                    'default' => '',
                    'type' => 'multiselect',
                    'newids' => 'rs_select_category_for_purchase_using_points',
                    'options' => $categorylist,
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('For Other Product(s) display SUMO Reward Points Payment Gateway', 'rewardsystem'),
                    'desc' => __('Enable this option to display SUMO Reward Points Payment Gateway for other product(s)', 'rewardsystem'),
                    'id' => 'rs_enable_gateway_visible_to_all_product',
                    'class' => 'rs_enable_gateway_visible_to_all_product',
                    'newids' => 'rs_enable_gateway_visible_to_all_product',
                    'type' => 'checkbox',
                    'std' => 'yes',
                    'default' => 'yes',
                ),
                array(
                    'name' => __('SUMO Reward Points Payment Gateway is hidden for Selected Product(s)', 'rewardsystem'),
                    'desc' => __('Enable this option to hide SUMO Reward Points Payment Gateway for selected product(s) (Don\'t select point price product)', 'rewardsystem'),
                    'id' => 'rs_enable_selected_product_for_hide_gateway',
                    'class' => 'rs_enable_selected_product_for_hide_gateway',
                    'newids' => 'rs_enable_selected_product_for_hide_gateway',
                    'type' => 'checkbox',
                ),
                array(
                    'type' => 'rs_hide_gateway',
                ),
                array(
                    'name' => __('SUMO Reward Points Payment Gateway is hidden for Selected Category', 'rewardsystem'),
                    'desc' => __('Enable this option to hide SUMO Reward Points Payment Gateway for product(s) in selected cateogry (Don\'t select category that contain point price product)', 'rewardsystem'),
                    'id' => 'rs_enable_selected_category_to_hide_gateway',
                    'class' => 'rs_enable_selected_category_to_hide_gateway',
                    'newids' => 'rs_enable_selected_category_to_hide_gateway',
                    'type' => 'checkbox',
                ),
                array(
                    'name' => __('Select Category', 'rewardsystem'),
                    'desc' => __('Select Category to hide SUMO Reward Points Payment Gateway', 'rewardsystem'),
                    'id' => 'rs_select_category_to_hide_gateway',
                    'class' => 'rs_select_category_to_hide_gateway',
                    'css' => 'min-width:350px',
                    'std' => '',
                    'default' => '',
                    'type' => 'multiselect',
                    'newids' => 'rs_select_category_to_hide_gateway',
                    'options' => $categorylist,
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message when other products added to Cart Page', 'rewardsystem'),
                    'desc' => __('Error Message when other products added to Cart Page', 'rewardsystem'),
                    'id' => 'rs_errmsg_when_other_products_added_to_cart_page',
                    'css' => 'min-width:550px;',
                    'std' => '[productname] is removed from the Cart.Because it can be purchased only through Reward points',
                    'default' => '[productname] is removed from the Cart.Because it can be purchased only through Reward points',
                    'type' => 'textarea',
                    'newids' => 'rs_errmsg_when_other_products_added_to_cart_page',
                    'class' => 'rs_errmsg_when_other_products_added_to_cart_page',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeeming Button Message ', 'rewardsystem'),
                    'desc' => __('Enter the Message for the Redeeming Button', 'rewardsystem'),
                    'id' => 'rs_redeeming_button_option_message_checkout',
                    'css' => 'min-width:550px;',
                    'std' => '[cartredeempoints] points worth of [currencysymbol] [pointsvalue] will be Redeemed',
                    'default' => '[cartredeempoints] points worth of [currencysymbol] [pointsvalue] will be Redeemed',
                    'type' => 'textarea',
                    'newids' => 'rs_redeeming_button_option_message_checkout',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => 'rs_checkout_setting'),
                array(
                    'name' => __('Guest Registration Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_checkout_force_login',
                ),
                array(
                    'name' => __('Force Guest to Create Account when Points associated Product is in Cart', 'rewardsystem'),
                    'id' => 'rs_enable_acc_creation_for_guest_checkout_page',
                    'css' => 'min-width:150px;',
                    'std' => 'no',
                    'default' => 'no',
                    'newids' => 'rs_enable_acc_creation_for_guest_checkout_page',
                    'type' => 'checkbox',
                ),
                array('type' => 'sectionend', 'id' => '_rs_checkout_force_login'),
                array(
                    'name' => __('Custom CSS Settings', 'rewardsystem'),
                    'type' => 'title',
                    'desc' => 'Try !important if styles doesn\'t apply ',
                    'id' => '_rs_checkout_custom_css_settings',
                ),
                array(
                    'name' => __('Custom CSS', 'rewardsystem'),
                    'desc' => __('Enter the Custom CSS for the Cart Page ', 'rewardsystem'),
                    'id' => 'rs_checkout_page_custom_css',
                    'css' => 'min-width:350px; min-height:350px;',
                    'std' => '#rs_apply_coupon_code_field { } #mainsubmi { } .fp_apply_reward{ }',
                    'default' => '#rs_apply_coupon_code_field { } #mainsubmi { } .fp_apply_reward{ }',
                    'type' => 'textarea',
                    'newids' => 'rs_checkout_page_custom_css',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_checkout_custom_css_settings'),
                array(
                    'name' => __('Shortcodes used in Checkout', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_shortcodes_in_checkout',
                ),
                array(
                    'type' => 'title',
                    'desc' => '<b>[cartredeempoints]</b> - To display points can redeem based on cart total amount<br><br>'
                    . '<b>[currencysymbol]</b> - To display currency symbol<br><br>'
                    . '<b>[pointsvalue]</b> - To display currency value equivalent of redeeming points<br><br>'
                    . '<b>[productname]</b> - To display product name<br><br>',
                ),
                array('type' => 'sectionend', 'id' => 'rs_shortcodes_in_checkout'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSCheckout::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSCheckout::reward_system_admin_fields());
            update_option('rs_select_product_for_purchase_using_points', $_POST['rs_select_product_for_purchase_using_points']);
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSCheckout::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function rs_purchase_product_using_point() {
            global $woocommerce;
            if (isset($_GET['tab'])) {
                if ($_GET['tab'] == 'rewardsystem_checkout') {
                    echo rs_common_ajax_function_to_select_products('rs_select_product_for_purchase_using_points');
                    echo rs_common_ajax_function_to_select_products('rs_select_product_for_hide_gateway');
                    if ((float) $woocommerce->version <= (float) ('2.2.0')) {
                        echo rs_common_chosen_function('#rs_select_category_for_purchase_using_points');
                        echo rs_common_chosen_function('#rs_select_category_to_hide_gateway');
                    } else {
                        echo rs_common_select_function('#rs_select_category_for_purchase_using_points');
                        echo rs_common_select_function('#rs_select_category_to_hide_gateway');
                    }
                }
            }
        }

        /*
         * Function to select the products which are going to be buy using Reward Points
         */

        public static function rs_purchase_selected_product_using_points() {
            $field_id = "rs_select_product_for_purchase_using_points";
            $field_label = "Select Product(s)";
            $getproducts = get_option('rs_select_product_for_purchase_using_points');
            echo rs_function_to_add_field_for_product_select($field_id, $field_label, $getproducts);
        }


        public static function rs_selected_product_hide_gateway() {
            $field_id = "rs_select_product_for_hide_gateway";
            $field_label = "Select Product(s)";
            $getproducts = get_option('rs_select_product_for_hide_gateway');
            echo rs_function_to_add_field_for_product_select($field_id, $field_label, $getproducts);
        }

        public static function save_selected_product_hide_gateway() {
            global $woocommerce;
            update_option('rs_select_product_for_hide_gateway', $_POST['rs_select_product_for_hide_gateway']);
        }

        public static function rs_function_to_reset_checkout_tab() {
            $settings = RSCheckout::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

    }

    RSCheckout::init();
}