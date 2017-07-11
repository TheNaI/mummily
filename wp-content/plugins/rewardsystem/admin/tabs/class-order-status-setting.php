<?php

/*
 * Order Status Settings 
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSStatus')) {

    class RSStatus {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_status', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_status', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));

            add_action('admin_head', array(__CLASS__, 'rs_select_status'));

            add_action('fp_action_to_reset_settings_rewardsystem_status', array(__CLASS__, 'rs_function_to_reset_order_status_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_status'] = __('Status', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            $newcombinedarray = fp_rs_get_all_order_status();
            return apply_filters('woocommerce_rewardsystem_status_settings', array(
                array(
                    'name' => __('Status Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_status_setting',
                ),
                array(
                    'name' => __('Status to award Points for Product Review', 'rewardsystem'),
                    'desc' => __('Here you can set on which Status Reward Points for Product Review should be applied', 'rewardsystem'),
                    'id' => 'rs_review_reward_status',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'options' => array('1' => 'Approve', '2' => 'Unapprove'),
                    'newids' => 'rs_review_reward_status',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Reward Points will be awarded when Order Status reaches', 'rewardsystem'),
                    'desc' => __('Here you can set Reward Points should awarded on which Status of Order', 'rewardsystem'),
                    'id' => 'rs_order_status_control',
                    'css' => 'min-width:150px;',
                    'std' => array('completed'),
                    'default' => array('completed'),
                    'type' => 'multiselect',
                    'options' => $newcombinedarray,
                    'newids' => 'rs_order_status_control',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeemed Points will be deducted when the Order Status reaches', 'rewardsystem'),
                    'desc' => __('Here you can set Reward Points should awarded on which Status of Order', 'rewardsystem'),
                    'id' => 'rs_order_status_control_redeem',
                    'css' => 'min-width:150px;',
                    'std' => array('completed', 'pending', 'processing', 'on-hold'),
                    'default' => array('completed', 'pending', 'processing', 'on-hold'),
                    'type' => 'multiselect',
                    'options' => $newcombinedarray,
                    'newids' => 'rs_order_status_control_redeem',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Order(s) placed through SUMO Reward Points Payment Gateway will go to', 'rewardsystem'),
                    'desc' => __('Here you can set what should be the order status after successful payment with SUMO Reward Points Gateway', 'rewardsystem'),
                    'id' => 'rs_order_status_after_gateway_purchase',
                    'std' => 'completed',
                    'default' => 'completed',
                    'type' => 'radio',
                    'options' => $newcombinedarray,
                    'newids' => 'rs_order_status_after_gateway_purchase',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => 'rs_status_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSStatus::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSStatus::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSStatus::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function rs_select_status() {
            global $woocommerce;
            if (isset($_GET['tab'])) {
                if ($_GET['tab'] == 'rewardsystem_status') {
                    if ((float) $woocommerce->version <= (float) ('2.2.0')) {
                        echo rs_common_chosen_function('#rs_order_status_control');
                        echo rs_common_chosen_function('#rs_order_status_control_redeem');
                    } else {
                        echo rs_common_select_function('#rs_order_status_control');
                        echo rs_common_select_function('#rs_order_status_control_redeem');
                    }
                }
            }
        }

        public static function rs_function_to_reset_order_status_tab() {
            $settings = RSStatus::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

    }

    RSStatus::init();
}