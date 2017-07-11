<?php

/*
 * Form for Send Points Setting Tab
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFormForSendPoints')) {

    class RSFormForSendPoints {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_form_for_send_points_tab', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_form_for_send_points_tab', array(__CLASS__, 'reward_system_update_settings'));

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings')); // call the init function to update the default settings on page load

            add_action('woocommerce_admin_field_rs_select_user_for_send', array(__CLASS__, 'rs_select_user_to_send_point'));

            add_action('fp_action_to_reset_settings_rewardsystem_form_for_send_points_tab', array(__CLASS__, 'rs_function_to_reset_form_for_send_points_tab'));
        }

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_form_for_send_points_tab'] = __('Send Points', 'rewardsystem');
            return $setting_tabs;
        }

        public static function reward_system_admin_fields() {
            global $woocommerce;
            return apply_filters('woocommerce_rewardsystem_form_for_send_points_settings', array(                
                array(
                    'name' => __('Send Point Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_send_point_settings'
                ),
                array(
                    'name' => __('Enable Send Points', 'rewardsystem'),
                    'id' => 'rs_enable_msg_for_send_point',
                    'newids' => 'rs_enable_msg_for_send_point',
                    'std' => '2',
                    'default' => '2',
                    'class' => 'rs_enable_msg_for_send_point',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Current Reward Points Label', 'rewardsystem'),
                    'id' => 'rs_total_send_points_request',
                    'std' => 'Current Reward Points',
                    'default' => 'Current Reward Points',
                    'type' => 'text',
                    'newids' => 'rs_total_send_points_request',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Restriction on Sending Points', 'rewardsystem'),
                    'id' => 'rs_limit_for_send_point',
                    'newids' => 'rs_limit_for_send_point',
                    'std' => '2',
                    'default' => '2',
                    'class' => 'rs_limit_for_send_point',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Maximum Points which can be Sent', 'rewardsystem'),
                    'id' => 'rs_limit_send_points_request',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_limit_send_points_request',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when Send Points is greater than the Maximum Points', 'rewardsystem'),
                    'id' => 'rs_err_when_point_greater_than_limit',
                    'css' => 'min-width:500px;',
                    'std' => 'Please Enter Points less than {limitpoints}',
                    'default' => 'Please Enter Points less than {limitpoints}',
                    'type' => 'text',
                    'newids' => 'rs_err_when_point_greater_than_limit',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Send Points Request Approval Type', 'rewardsystem'),
                    'id' => 'rs_request_approval_type',
                    'newids' => 'rs_request_approval_type',
                    'std' => '1',
                    'default' => '1',
                    'class' => 'rs_request_approval_type',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Manual Approval', 'rewardsystem'),
                        '2' => __('Auto Approval', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Points to Send Label', 'rewardsystem'),
                    'id' => 'rs_points_to_send_request',
                    'std' => 'Points to Send',
                    'default' => 'Points to Send',
                    'type' => 'text',
                    'newids' => 'rs_points_to_send_request',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('User Selection type for Sending Points', 'rewardsystem'),
                    'id' => 'rs_select_send_points_user_type',
                    'newids' => 'rs_select_send_points_user_type',
                    'std' => '1',
                    'default' => '1',
                    'class' => 'rs_select_send_points_user_type',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('All Users', 'rewardsystem'),
                        '2' => __('Selected User(s)', 'rewardsystem'),                        
                    ),
                    'desc_tip' => true,
                ),
                array(                    
                    'type' => 'rs_select_user_for_send',                    
                ),
                array(
                    'name' => __('Select the User to Send Label', 'rewardsystem'),
                    'id' => 'rs_select_user_label',
                    'css' => 'min-width:400px;',
                    'std' => 'Select the user to send',
                    'default' => 'Select the user to send',
                    'type' => 'text',
                    'newids' => 'rs_select_user_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Send Point Form Submit Button Label', 'rewardsystem'),
                    'id' => 'rs_select_points_submit_label',
                    'css' => 'min-width:400px;',
                    'std' => 'Submit',
                    'default' => 'Submit',
                    'type' => 'text',
                    'newids' => 'rs_select_points_submit_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when User doesn\'t have Points in their Account', 'rewardsystem'),
                    'id' => 'rs_msg_when_user_have_no_points',
                    'css' => 'min-width:500px;',
                    'std' => 'You have no Points to Send',
                    'default' => 'You have no Points to Send',
                    'type' => 'text',
                    'newids' => 'rs_msg_when_user_have_no_points',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Message to display when Send Points Request is Submitted Successfully', 'rewardsystem'),
                    'id' => 'rs_message_send_point_request_submitted',
                    'css' => 'min-width:500px;',
                    'std' => 'Send Point Request Submitted',
                    'default' => 'Send Point Request Submitted',
                    'type' => 'textarea',
                    'newids' => 'rs_message_send_point_request_submitted',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Message to display when Send Points Request is Submitted via Auto Approval', 'rewardsystem'),
                    'id' => 'rs_message_send_point_request_submitted_for_auto',
                    'css' => 'min-width:500px;',
                    'std' => 'Points has been sent Successfully',
                    'default' => 'Points has been sent Successfully',
                    'type' => 'textarea',
                    'newids' => 'rs_message_send_point_request_submitted_for_auto',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to be displayed when Points to Send field is left Empty', 'rewardsystem'),
                    'id' => 'rs_err_when_point_field_empty',
                    'css' => 'min-width:500px;',
                    'std' => 'Points to send shouldn\'t be empty',
                    'default' => 'Points to send shouldn\'t be empty',
                    'type' => 'text',
                    'newids' => 'rs_err_when_point_field_empty',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to be displayed when User entered Points more than the Available Points', 'rewardsystem'),
                    'id' => 'rs_error_msg_when_points_is_more',
                    'css' => 'min-width:500px;',
                    'std' => 'Please Enter the Points less than your Current Points',
                    'default' => 'Please Enter the Points less than your Current Points',
                    'type' => 'text',
                    'newids' => 'rs_error_msg_when_points_is_more',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to be displayed when User entered Points less than the Available Points', 'rewardsystem'),
                    'id' => 'rs_error_msg_when_points_is_less',
                    'css' => 'min-width:500px;',
                    'std' => 'Negative Values will not be allowed',
                    'default' => 'Negative Values will not be allowed',
                    'type' => 'text',
                    'newids' => 'rs_error_msg_when_points_is_less',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to be displayed when Select User field is left Empty', 'rewardsystem'),
                    'id' => 'rs_err_for_empty_user',
                    'css' => 'min-width:500px;',
                    'std' => 'The user to select shouldn\'t be empty',
                    'default' => 'The user to select shouldn\'t be empty',
                    'type' => 'text',
                    'newids' => 'rs_err_for_empty_user',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to be displayed when entered Points is not a Number', 'rewardsystem'),
                    'id' => 'rs_err_when_point_is_not_number',
                    'css' => 'min-width:500px;',
                    'std' => 'Please Enter only the Number',
                    'default' => 'Please Enter only the Number',
                    'type' => 'text',
                    'newids' => 'rs_err_when_point_is_not_number',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_send_point_setting'),
                array(
                    'name' => __('Shortcode used in Send Points', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_shortcode_for_send_points'
                ),
                array(
                    'type' => 'title',
                    'desc' => '<b>{limitpoints}</b> - To display send points limitation'
                ),
                array('type' => 'sectionend', 'id' => 'rs_shortcode_for_send_points'),
            ));
        }

        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSFormForSendPoints::reward_system_admin_fields());
        }

        public static function reward_system_update_settings() {
            woocommerce_update_options(RSFormForSendPoints::reward_system_admin_fields());
            update_option('rs_select_users_list_for_send_point',$_POST['rs_select_users_list_for_send_point']);
        }

        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSFormForSendPoints::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function rs_select_user_to_send_point() {
            $field_id = "rs_select_users_list_for_send_point";
            $field_label = "Select User(s)";
            $getuser = get_option('rs_select_users_list_for_send_point');
            echo rs_function_to_add_field_for_user_select($field_id, $field_label, $getuser);
            ?>
            <style type="text/css">
                .chosen-container-single {
                    position:absolute;
                }

            </style>
            <?php

        }

        public static function rs_function_to_reset_form_for_send_points_tab() {
            $settings = RSFormForSendPoints::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

    }

    RSFormForSendPoints::init();
}