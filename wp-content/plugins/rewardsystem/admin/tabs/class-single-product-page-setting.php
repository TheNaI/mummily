<?php

/*
 * Single Product Page Setting
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FPRewardSystemSingleProductPageTab')) {

    class FPRewardSystemSingleProductPageTab {
        /* Construct the Object */

        public static function init() {

            // Add Filter for WooCommerce Update Options Reward System
            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_settings'));

            // Call to register the admin settings in the Reward System Submenu with general Settings tab
            add_action('woocommerce_rs_settings_tabs_rewardsystem_single_producttab', array(__CLASS__, 'reward_system_register_admin_settings'));

            add_action('woocommerce_update_options_rewardsystem_single_producttab', array(__CLASS__, 'reward_system_update_settings'));

            // call the init function to update the default settings on page load
            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));
            
            add_action('fp_action_to_reset_settings_rewardsystem_single_producttab', array(__CLASS__, 'rs_function_to_reset_singleproductpage_tab'));
        }

        public static function reward_system_tab_settings($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_single_producttab'] = __('Single Product Page', 'rewardsystem');
            return $setting_tabs;
        }

        // Add Admin Fields in the Array Format

        public static function reward_system_admin_fields() {
            global $woocommerce;
            return apply_filters('woocommerce_rewardsystem_single_productpage_settings', array(
                array(
                    'name' => __('Single Product Page Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_point_single_product_page_settings'
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_point_single_product_page_settings'),
                array(
                    'name' => __('Custom CSS Settings', 'rewardsystem'),
                    'type' => 'title',
                    'desc' => 'Try !important if styles doesn\'t apply ',
                    'id' => '_rs_single_product_page_custom_css_settings',
                ),
                array(
                    'name' => __('Custom CSS', 'rewardsystem'),
                    'desc' => __('Enter the Custom CSS for the Cart Page ', 'rewardsystem'),
                    'id' => 'rs_single_product_page_custom_css',
                    'css' => 'min-width:350px; min-height:350px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'textarea',
                    'newids' => 'rs_single_product_page_custom_css',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_single_product_page_custom_css_settings'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of Crowdfunding in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields(FPRewardSystemSingleProductPageTab::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in crowdfunding
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(FPRewardSystemSingleProductPageTab::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (FPRewardSystemSingleProductPageTab::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }
        
        public static function rs_function_to_reset_singleproductpage_tab() {
            $settings = FPRewardSystemSingleProductPageTab::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);            
        }

    }

    FPRewardSystemSingleProductPageTab::init();
}
    