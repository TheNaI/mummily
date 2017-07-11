<?php

/*
 * Shop Page Setting
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FPRewardSystemShopPageTab')) {

    class FPRewardSystemShopPageTab {

        public static function init() {
            
            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_settings'));

            // Call to register the admin settings in the Reward System Submenu with general Settings tab
            add_action('woocommerce_rs_settings_tabs_rewardsystem_shoptab', array(__CLASS__, 'reward_system_register_admin_settings'));

            add_action('woocommerce_update_options_rewardsystem_shoptab', array(__CLASS__, 'reward_system_update_settings'));

            // call the init function to update the default settings on page load
            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));
            
            add_action('fp_action_to_reset_settings_rewardsystem_shoptab', array(__CLASS__, 'rs_function_to_reset_shoppage_tab'));
        }

        public static function reward_system_tab_settings($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_shoptab'] = __('Shop Page', 'rewardsystem');
            return $setting_tabs;
        }

        // Add Admin Fields in the Array Format

        public static function reward_system_admin_fields() {
            global $woocommerce;
            return apply_filters('woocommerce_rewardsystem_shoppage_settings', array(
                array(
                    'name' => __('Shop Page Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_point_shop_page_settings'
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_point_shop_page_settings'),
                array(
                    'name' => __('Custom CSS Settings', 'rewardsystem'),
                    'type' => 'title',
                    'desc' => 'Try !important if styles doesn\'t apply ',
                    'id' => '_rs_shop_page_custom_css_settings',
                ),
                array(
                    'name' => __('Custom CSS', 'rewardsystem'),
                    'desc' => __('Enter the Custom CSS for the Cart Page ', 'rewardsystem'),
                    'id' => 'rs_shop_page_custom_css',
                    'css' => 'min-width:350px; min-height:350px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'textarea',
                    'newids' => 'rs_shop_page_custom_css',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_shop_page_custom_css_settings'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of Crowdfunding in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields(FPRewardSystemShopPageTab::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in crowdfunding
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(FPRewardSystemShopPageTab::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (FPRewardSystemShopPageTab::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }
        
        public static function rs_function_to_reset_shoppage_tab() {
            $settings = FPRewardSystemShopPageTab::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);            
        }

    }

    FPRewardSystemShopPageTab::init();
}