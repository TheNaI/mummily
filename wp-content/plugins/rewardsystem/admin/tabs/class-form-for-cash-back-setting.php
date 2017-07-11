<?php

/*
 * Form for Cashback Tab Setting
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFormForCashBack')) {

    class RSFormForCashBack {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_form_for_cash_back', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_form_for_cash_back', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings')); // call the init function to update the default settings on page load
            
            add_action('fp_action_to_reset_settings_rewardsystem_form_for_cash_back', array(__CLASS__, 'rs_function_to_reset_form_for_cashback_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_form_for_cash_back'] = __('Form for Cashback', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;

            return apply_filters('woocommerce_rewardsystem_form_for_cash_back_settings', array(                
                array(
                    'name' => __('Cashback Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_point_encashing_settings'
                ),
                array(
                    'name' => __('Enable Cashback for Reward Points', 'rewardsystem'),
                    'desc' => __('Enable this option to provide the feature to Cashback the Reward Points earned by the Users', 'rewardsystem'),
                    'id' => 'rs_enable_disable_encashing',
                    'std' => '2',
                    'default' => '2',
                    'type' => 'select',
                    'newids' => 'rs_enable_disable_encashing',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Minimum Points for Cashback of Reward Points', 'rewardsystem'),
                    'desc' => __('Enter the Minimum points that the user should have in order to Submit the Cashback Request', 'rewardsystem'),
                    'id' => 'rs_minimum_points_encashing_request',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_minimum_points_encashing_request',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Maximum Points for Cashback of Reward Points', 'rewardsystem'),
                    'desc' => __('Enter the Maximum points that the user should enter order to Submit the Cashback Request', 'rewardsystem'),
                    'id' => 'rs_maximum_points_encashing_request',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_maximum_points_encashing_request',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Points for Cashback Label', 'rewardsystem'),
                    'desc' => __('Please Enter Points the Label for Cashback', 'rewardsystem'),
                    'id' => 'rs_encashing_points_label',
                    'css' => 'min-width:300px;',
                    'std' => 'Points for Cashback',
                    'default' => 'Points for Cashback',
                    'type' => 'text',
                    'newids' => 'rs_encashing_points_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Payment Method Label', 'rewardsystem'),
                    'desc' => __('Please Enter Payment Method Label for Cashback', 'rewardsystem'),
                    'id' => 'rs_encashing_payment_method_label',
                    'css' => 'min-width:300px;',
                    'std' => 'Payment Method',
                    'default' => 'Payment Method',
                    'type' => 'text',
                    'newids' => 'rs_encashing_payment_method_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Display Payment Method', 'rewardsystem'),
                    'id' => 'rs_select_payment_method',
                    'std' => '3',
                    'default' => '3',
                    'type' => 'select',
                    'newids' => 'rs_select_payment_method',
                    'options' => array(
                        '1' => __('PayPal', 'rewardsystem'),
                        '2' => __('Custom Payment', 'rewardsystem'),
                        '3' => __('Both', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Reason for Cashback Label', 'rewardsystem'),
                    'desc' => __('Please Enter label for Reason Cashback', 'rewardsystem'),
                    'id' => 'rs_encashing_reason_label',
                    'css' => 'min-width:300px;',
                    'std' => 'Reason for Cashback',
                    'default' => 'Reason for Cashback',
                    'type' => 'text',
                    'newids' => 'rs_encashing_reason_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('PayPal Email Address Label', 'rewardsystem'),
                    'desc' => __('Please Enter PayPal Email Address Label for Cashback', 'rewardsystem'),
                    'id' => 'rs_encashing_payment_paypal_label',
                    'css' => 'min-width:300px;',
                    'std' => 'PayPal Email Address',
                    'default' => 'PayPal Email Address',
                    'type' => 'text',
                    'newids' => 'rs_encashing_payment_paypal_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Custom Payment Details Label', 'rewardsystem'),
                    'desc' => __('Please Enter Custom Payment Details Label for Cashback', 'rewardsystem'),
                    'id' => 'rs_encashing_payment_custom_label',
                    'css' => 'min-width:300px;',
                    'std' => 'Custom Payment Details',
                    'default' => 'Custom Payment Details',
                    'type' => 'text',
                    'newids' => 'rs_encashing_payment_custom_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Cashback Form Submit Button Label', 'rewardsystem'),
                    'desc' => __('Please Enter Cashback Form Submit Button Label ', 'rewardsystem'),
                    'id' => 'rs_encashing_submit_button_label',
                    'css' => 'min-width:200px;',
                    'std' => 'Submit',
                    'default' => 'Submit',
                    'type' => 'text',
                    'newids' => 'rs_encashing_submit_button_label',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_point_checkout_settings'),
                array(
                    'name' => __('Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_message_settings_encashing'
                ),
                array(
                    'name' => __('Message displayed for Guest', 'rewardsystem'),
                    'desc' => __('Please Enter Message displayed for Guest', 'rewardsystem'),
                    'id' => 'rs_message_for_guest_encashing',
                    'css' => 'min-width:500px;',
                    'std' => 'Please [rssitelogin] to Cashback your Reward Points.',
                    'default' => 'Please [rssitelogin] to Cashback your Reward Points.',
                    'type' => 'textarea',
                    'newids' => 'rs_message_for_guest_encashing',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Login Link for Guest Label', 'rewardsystem'),
                    'desc' => __('Please Enter Login link for Guest Label', 'rewardsystem'),
                    'id' => 'rs_encashing_login_link_label',
                    'css' => 'min-width:200px;',
                    'std' => 'Login',
                    'default' => 'Login',
                    'type' => 'text',
                    'newids' => 'rs_encashing_login_link_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Message displayed for Banned Users', 'rewardsystem'),
                    'desc' => __('Please Enter Message Displayed for Banned Users', 'rewardsystem'),
                    'id' => 'rs_message_for_banned_users_encashing',
                    'css' => 'min-width:500px;',
                    'std' => 'You cannot Cashback Your points',
                    'default' => 'You cannot Cashback Your points',
                    'type' => 'textarea',
                    'newids' => 'rs_message_for_banned_users_encashing',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Message displayed when Users don\'t have Reward Points', 'rewardsystem'),
                    'desc' => __('Please Enter Message to be Displayed when Users dont have Reward Points', 'rewardsystem'),
                    'id' => 'rs_message_users_nopoints_encashing',
                    'css' => 'min-width:500px;',
                    'std' => 'You Don\'t have points for Cashback',
                    'default' => 'You Don\'t have points for Cashback',
                    'type' => 'textarea',
                    'newids' => 'rs_message_users_nopoints_encashing',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Message displayed when Cashback Request is Submitted', 'rewardsystem'),
                    'desc' => __('Please Enter Message to be Displayed when Cashback Request is Submitted', 'rewardsystem'),
                    'id' => 'rs_message_encashing_request_submitted',
                    'css' => 'min-width:500px;',
                    'std' => 'Cashback Request Submitted',
                    'default' => 'Cashback Request Submitted',
                    'type' => 'textarea',
                    'newids' => 'rs_message_encashing_request_submitted',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_message_settings_encashing'),
                array(
                    'name' => __('CSV Settings (Export CSV for Paypal Mass Payment)', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_csv_message_settings_encashing'
                ),
                array(
                    'name' => __('Custom Note for Paypal', 'rewardsystem'),
                    'desc' => __('A Custom Note for Paypal', 'rewardsystem'),
                    'id' => 'rs_encashing_paypal_custom_notes',
                    'css' => 'min-width:200px;',
                    'std' => 'Thanks for your Business',
                    'default' => 'Thanks for your Business',
                    'type' => 'textarea',
                    'newids' => 'rs_encashing_paypal_custom_notes',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_csv_message_settings_encashing'),
                array(
                    'name' => __('Error Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_error_settings_encashing'
                ),
                array(
                    'name' => __('Error Message displayed when Points for Cashback Field is left Empty', 'rewardsystem'),
                    'desc' => __('Please Enter Message to be Displayed when Points for Cashback Field is Empty', 'rewardsystem'),
                    'id' => 'rs_error_message_points_empty_encash',
                    'css' => 'min-width:500px;',
                    'std' => 'Points for Cashback Field cannot be empty',
                    'default' => 'Points for Cashback Field cannot be empty',
                    'type' => 'text',
                    'newids' => 'rs_error_message_points_empty_encash',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message displayed when Points to Cashback Value is not a Number', 'rewardsystem'),
                    'desc' => __('Please Enter Message to be Displayed when Points To Cashback Field value is not a number', 'rewardsystem'),
                    'id' => 'rs_error_message_points_number_val_encash',
                    'css' => 'min-width:500px;',
                    'std' => 'Please Enter only Numbers',
                    'default' => 'Please Enter only Numbers',
                    'type' => 'text',
                    'newids' => 'rs_error_message_points_number_val_encash',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message displayed when Points entered for Cashback is more than the Points Earned', 'rewardsystem'),
                    'desc' => __('Please Enter Message to be Displayed when Points entered for Cashback is more than the Points Earned', 'rewardsystem'),
                    'id' => 'rs_error_message_points_greater_than_earnpoints',
                    'css' => 'min-width:500px;',
                    'std' => 'Points Entered for Cashback is more than the Earned Points',
                    'default' => 'Points Entered for Cashback is more than the Earned Points',
                    'type' => 'text',
                    'newids' => 'rs_error_message_points_greater_than_earnpoints',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message displayed when Current User Points is less than the Minimum Points for Cashback', 'rewardsystem'),
                    'desc' => __('Please Enter Message to be Displayed when Points entered for Cashback is more than the Maximum Points for Cashback', 'rewardsystem'),
                    'id' => 'rs_error_message_currentpoints_less_than_minimum_points',
                    'css' => 'min-width:500px;',
                    'std' => 'You need a Minimum of [minimum_encash_points] points in order for Cashback',
                    'default' => 'You need a Minimum of [minimum_encash_points] points in order for Cashback',
                    'type' => 'textarea',
                    'newids' => 'rs_error_message_currentpoints_less_than_minimum_points',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message displayed when Points entered to Cashback is less than the Minimum Points and more than Maximum Points', 'rewardsystem'),
                    'desc' => __('Please Enter Message to be Displayed when Points entered to Cashback is less than the Minimum Points and more than Maximum Points', 'rewardsystem'),
                    'id' => 'rs_error_message_points_lesser_than_minimum_points',
                    'css' => 'min-width:500px;',
                    'std' => 'Please Enter Between [minimum_encash_points] and [maximum_encash_points] ',
                    'default' => 'Please Enter Between [minimum_encash_points] and [maximum_encash_points]',
                    'type' => 'textarea',
                    'newids' => 'rs_error_message_points_lesser_than_minimum_points',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message displayed when Reason to Cashback Field is Empty', 'rewardsystem'),
                    'desc' => __('Please Enter Message to be Displayed when Reason To Cashback Field is Empty', 'rewardsystem'),
                    'id' => 'rs_error_message_reason_encash_empty',
                    'css' => 'min-width:500px;',
                    'std' => 'Reason to Encash Field cannot be empty',
                    'default' => 'Reason to Encash Field cannot be empty',
                    'type' => 'text',
                    'newids' => 'rs_error_message_reason_encash_empty',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message displayed when PayPal Email Address is Empty', 'rewardsystem'),
                    'desc' => __('Please Enter Message to be Displayed when PayPal Email Address is Empty', 'rewardsystem'),
                    'id' => 'rs_error_message_paypal_email_empty',
                    'css' => 'min-width:500px;',
                    'std' => 'Paypal Email Field cannot be empty',
                    'default' => 'Paypal Email Field cannot be empty',
                    'type' => 'text',
                    'newids' => 'rs_error_message_paypal_email_empty',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message displayed when PayPal Email Address Format is wrong', 'rewardsystem'),
                    'desc' => __('Please Enter Message to be Displayed when PayPal Email Address format is wrong', 'rewardsystem'),
                    'id' => 'rs_error_message_paypal_email_wrong',
                    'css' => 'min-width:500px;',
                    'std' => 'Enter a Correct Email Address',
                    'default' => 'Enter a Correct Email Address',
                    'type' => 'text',
                    'newids' => 'rs_error_message_paypal_email_wrong',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message displayed when Custom Payment Details field is left Empty', 'rewardsystem'),
                    'desc' => __('Please Enter Message to be Displayed when Custom Payment Details field is Empty', 'rewardsystem'),
                    'id' => 'rs_error_custom_payment_field_empty',
                    'css' => 'min-width:500px;',
                    'std' => 'Custom Payment Details Field cannot be empty',
                    'default' => 'Custom Payment Details Field cannot be empty',
                    'type' => 'text',
                    'newids' => 'rs_error_custom_payment_field_empty',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_error_settings_encashing'),
                array(
                    'name' => __('Cashback Form CSS Customization Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_style_settings_encashing'
                ),
                array(
                    'name' => __('Inbuilt Design', 'rewardsystem'),
                    'desc' => __('Please Select you want to load the Inbuilt Design', 'rewardsystem'),
                    'id' => 'rs_encash_form_inbuilt_design',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'options' => array('1' => 'Inbuilt Design'),
                    'newids' => 'rs_encash_form_inbuilt_design',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Inbuilt CSS (Non Editable)', 'rewardsystem'),
                    'desc' => __('These are element IDs in the Shop Page ', 'rewardsystem'),
                    'css' => 'min-width:550px;min-height:260px;margin-bottom:80px;',
                    'id' => 'rs_encash_form_default_css',
                    'std' => '#encashing_form{}
.rs_encash_points_value{}
.error{color:#ED0514;}
.rs_encash_points_reason{}
.rs_encash_payment_method{}
.rs_encash_paypal_address{}
.rs_encash_custom_payment_option_value{}
.rs_encash_submit{}
#rs_encash_submit_button{}
.success_info{}
#encash_form_success_info{}',
                    'default' => '#encashing_form{}
.rs_encash_points_value{}
.error{color:#ED0514;}
.rs_encash_points_reason{}
.rs_encash_payment_method{}
.rs_encash_paypal_address{}
.rs_encash_custom_payment_option_value{}
.rs_encash_submit{}
#rs_encash_submit_button{}
.success_info{}
#encash_form_success_info{}',
                    'type' => 'textarea',
                    'newids' => 'rs_encash_form_default_css',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Custom Design', 'rewardsystem'),
                    'desc' => __('Please Select you want to load the Custom Design', 'rewardsystem'),
                    'id' => 'rs_encash_form_inbuilt_design',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'options' => array('2' => 'Custom Design'),
                    'newids' => 'rs_encash_form_inbuilt_design',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Custom CSS', 'rewardsystem'),
                    'desc' => __('Customize the following element of Cashback Request form', 'galaxyfunder'),
                    'css' => 'min-width:550px;min-height:260px;margin-bottom:80px;',
                    'id' => 'rs_encash_form_custom_css',
                    'std' => '',
                    'default' => '',
                    'type' => 'textarea',
                    'newids' => 'rs_encash_form_custom_css',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_style_settings_encashing'),
                array(
                    'name' => __('Shortcode used in Form for Cashback', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_shortcode_for_cashback'
                ),
                array(
                    'type' => 'title',
                    'desc' => '<b>[minimum_encash_points]</b> - To display minimum points required to get cashback<br><br>'
                    . '<b>[maximum_encash_points]</b> - To display maximum points required to get cashback<br><br>'
                    . '<b>[rssitelogin]</b> - To display login link for guests',
                ),
                array('type' => 'sectionend', 'id' => 'rs_shortcode_for_cashback'),
            ));
        }

        /*
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSFormForCashBack::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSFormForCashBack::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSFormForCashBack::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }
        
        public static function rs_function_to_reset_form_for_cashback_tab() {
            $settings = RSFormForCashBack::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);            
        }

    }

    RSFormForCashBack::init();
}