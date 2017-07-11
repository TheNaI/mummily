<?php

/*
 * Advanced Tab
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSAdvancedSetting')) {

    class RSAdvancedSetting {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_advanced', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_advanced', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings')); // call the init function to update the default settings on page load

            add_action('fp_action_to_reset_settings_rewardsystem_advanced', array(__CLASS__, 'rs_function_to_reset_advanced_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_advanced'] = __('Advanced', 'rewardsystem');
            return $setting_tabs;
        }

        public static function reward_system_admin_fields() {
            return apply_filters('woocommerce_rewardsystem_advanced_settings', array(
                array(
                    'name' => __('Advanced Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_advanced_setting',
                ),
                array(
                    'name' => __('Reset Button in Tabs', 'rewardsystem'),
                    'desc' => 'When Set to Show Reset Button will be displayed across all tabs',
                    'id' => 'rs_show_hide_reset_all',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_show_hide_reset_all',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => 'rs_advanced_setting'),
                    )
            );
        }

        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSAdvancedSetting::reward_system_admin_fields());
        }

        public static function reward_system_update_settings() {
            woocommerce_update_options(RSAdvancedSetting::reward_system_admin_fields());
        }

        public static function reward_system_default_settings() {
            foreach (RSAdvancedSetting::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function rs_function_to_reset_advanced_tab() {
            $settings = RSAdvancedSetting::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

    }

    RSAdvancedSetting::init();
}