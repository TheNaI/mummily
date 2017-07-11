<?php

/*
 * SMS Tab Setting
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSSms')) {

    class RSSms {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_sms', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_sms', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings')); // call the init function to update the default settings on page load
            
            add_action('fp_action_to_reset_settings_rewardsystem_sms', array(__CLASS__, 'rs_function_to_reset_sms_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_sms'] = __('SMS', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;

            return apply_filters('woocommerce_rewardsystem_sms_settings', array(
                array(
                    'name' => __('SMS Notification Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_sms_setting'
                ),
                array(
                    'title' => __('Notification through SMS', 'woocommerce'),
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'id' => 'rs_enable_send_sms_to_user',
                    'newids' => 'rs_enable_send_sms_to_user',
                ),
                array(
                    'name' => __('SMS API', 'rewardsystem'),
                    'desc' => __('Here you can choose the sms sending APi', 'rewardsystem'),
                    'id' => 'rs_sms_sending_api_option',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'options' => array('1' => 'Twilio', '2' => 'Nexmo'),
                    'newids' => 'rs_sms_sending_api_option',
                    'desc_tip' => true,
                ),
                array(
                    'title' => __('Send SMS on Earning Points', 'woocommerce'),
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'id' => 'rs_send_sms_earning_points',
                    'newids' => 'rs_send_sms_earning_points',
                ),
                array(
                    'title' => __('Send SMS on Redeeming Points', 'woocommerce'),
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'id' => 'rs_send_sms_redeeming_points',
                    'newids' => 'rs_send_sms_redeeming_points',
                ),
                array(
                    'name' => __('Twilio Account SID', 'rewardsystem'),
                    'desc' => __('Enter Twilio Account Id', 'rewardsystem'),
                    'id' => 'rs_twilio_secret_account_id',
                    'css' => 'min-width:550px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_twilio_secret_account_id',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Twilio Account Auth Token', 'rewardsystem'),
                    'desc' => __('Enter Twilio Auth Token', 'rewardsystem'),
                    'id' => 'rs_twilio_auth_token_id',
                    'css' => 'min-width:550px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_twilio_auth_token_id',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Twilio From Number', 'rewardsystem'),
                    'desc' => __('Enter Twilio From Number', 'rewardsystem'),
                    'id' => 'rs_twilio_from_number',
                    'css' => 'min-width:550px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_twilio_from_number',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Nexmo Key', 'rewardsystem'),
                    'desc' => __('Enter Nexmo Key', 'rewardsystem'),
                    'id' => 'rs_nexmo_key',
                    'css' => 'min-width:550px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_nexmo_key',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Nexmo  Secret', 'rewardsystem'),
                    'desc' => __('Enter Nexmo Secret', 'rewardsystem'),
                    'id' => 'rs_nexmo_secret',
                    'css' => 'min-width:550px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_nexmo_secret',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('SMS Content', 'rewardsystem'),
                    'desc' => __('Enter the SMS Content Here', 'rewardsystem'),
                    'id' => 'rs_points_sms_content',
                    'css' => 'min-width:550px;',
                    'std' => 'Hi {username}, {rewardpoints} points is in your account use it to make discount {sitelink}',
                    'default' => 'Hi {username}, {rewardpoints} points is in your account use it to make discount {sitelink}',
                    'type' => 'textarea',
                    'newids' => 'rs_points_sms_content',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_sms_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSSms::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSSms::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSSms::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }
        
        public static function rs_function_to_reset_sms_tab() {
            $settings = RSSms::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);            
        }

    }

    RSSms::init();
}