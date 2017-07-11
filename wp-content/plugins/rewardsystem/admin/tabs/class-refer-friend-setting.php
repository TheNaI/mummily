<?php
/*
 * Refer a Friend Setting
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSReferAFriend')) {

    class RSReferAFriend {

        public static function init() {
            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_referfriend', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_referfriend', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));            

            add_action('wp_ajax_nopriv_rs_refer_a_friend_ajax', array(__CLASS__, 'reward_system_process_ajax_request'));

            add_action('wp_ajax_rs_refer_a_friend_ajax', array(__CLASS__, 'reward_system_process_ajax_request'));
            
            add_action('fp_action_to_reset_settings_rewardsystem_referfriend', array(__CLASS__, 'rs_function_to_reset_refer_a_friend_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_referfriend'] = __('Refer a Friend', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            return apply_filters('woocommerce_rewardsystem_referfriend_settings', array(                
                array(
                    'name' => __('Refer a Friend Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_referfriend_status'
                ),
                array(
                    'name' => __('Friend Name Label', 'rewardsystem'),
                    'desc' => __('Enter Friend Name Label which will be available in Frontend when you use shortcode', 'rewardsystem'),
                    'id' => 'rs_my_rewards_friend_name_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Your Friend Name',
                    'default' => 'Your Friend Name',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_friend_name_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Friend Email Label', 'rewardsystem'),
                    'desc' => __('Enter Friend Email Label which will be available in Frontend when you use shortcode', 'rewardsystem'),
                    'id' => 'rs_my_rewards_friend_email_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Your Friend Email',
                    'default' => 'Your Friend Email',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_friend_email_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Friend Email Subject Label', 'rewardsystem'),
                    'desc' => __('Enter Friend Subject which will be appear in Frontend when you use shortcode', 'rewardsystem'),
                    'id' => 'rs_my_rewards_friend_subject_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Your Subject',
                    'default' => 'Your Subject',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_friend_subject_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Friend Email Message Label', 'rewardsystem'),
                    'desc' => __('Enter Friend Email Message which will be appear in frontend when you use shortcode', 'rewardsystem'),
                    'id' => 'rs_my_rewards_friend_message_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Your Message',
                    'default' => 'Your Message',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_friend_message_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Prefilled Message for Refer a Friend', 'rewardsystem'),
                    'desc' => __('This Message will be displayed in the Message field along with the Referral link', 'rewardsystem'),
                    'id' => 'rs_friend_referral_link',
                    'css' => 'min-width:550px',
                    'std' => 'You can Customize your message here.[site_referral_url]',
                    'default' => 'You can Customize your message here.[site_referral_url]',
                    'type' => 'textarea',
                    'newids' => 'rs_friend_referral_link',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Show/Hide I agree to the Terms and Condition Field', 'rewardsystem'),
                    'id' => 'rs_show_hide_iagree_termsandcondition_field',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_iagree_termsandcondition_field',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Hide', 'rewardsystem'),
                        '2' => __('Show', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('I Agree Field Label', 'rewardsystem'),
                    'desc' => __('This Caption will be displayed for the I agree field in Refer a Friend Form', 'rewardsystem'),
                    'id' => 'rs_refer_friend_iagreecaption_link',
                    'css' => 'min-width:550px',
                    'std' => 'I agree to the {termsandconditions}',
                    'default' => 'I agree to the {termsandconditions}',
                    'type' => 'textarea',
                    'newids' => 'rs_refer_friend_iagreecaption_link',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Terms and Conditions Label', 'rewardsystem'),
                    'desc' => __('This Caption will be displayed for terms and condition', 'rewardsystem'),
                    'id' => 'rs_refer_friend_termscondition_caption',
                    'css' => 'min-width:550px',
                    'std' => 'Terms and Conditions',
                    'default' => 'Terms and Conditions',
                    'type' => 'textarea',
                    'newids' => 'rs_refer_friend_termscondition_caption',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Terms and Conditions URL', 'rewardsystem'),
                    'desc' => __('Enter the URL for Terms and Conditions', 'rewardsystem'),
                    'id' => 'rs_refer_friend_termscondition_url',
                    'css' => 'min-width:550px',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_refer_friend_termscondition_url',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_referfriend_status'),
                array(
                    'name' => __('Field Placeholder Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_field_placeholder_settings'
                ),
                array(
                    'name' => __('Friend Name Field Placeholder', 'rewardsystem'),
                    'desc' => __('Enter Friend Name Field Placeholder which will be appear in frontend when you use shortcode', 'rewardsystem'),
                    'id' => 'rs_my_rewards_friend_name_placeholder',
                    'css' => 'min-width:550px;',
                    'std' => 'Enter your Friend Name',
                    'default' => 'Enter your Friend Name',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_friend_name_placeholder',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Friend Email Field Placeholder', 'rewardsystem'),
                    'desc' => __('Enter Friend Email Field Placeholder which will be appear in frontend when you use shortcode', 'rewardsystem'),
                    'id' => 'rs_my_rewards_friend_email_placeholder',
                    'css' => 'min-width:550px;',
                    'std' => 'Enter your Friend Email',
                    'default' => 'Enter your Friend Email',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_friend_email_placeholder',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Friend Email Subject Field Placeholder', 'rewardsystem'),
                    'desc' => __('Enter Friend Email Subject Field Placeholder which will be appear in frontend when you use shortcode', 'rewardsystem'),
                    'id' => 'rs_my_rewards_friend_email_subject_placeholder',
                    'css' => 'min-width:550px;',
                    'std' => 'Enter your Subject',
                    'default' => 'Enter your Subject',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_friend_email_subject_placeholder',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Friend Email Message Field Placeholder', 'rewardsystem'),
                    'desc' => __('Enter Friend Email Message Field Placeholder which will be appear in frontend when you use shortcode', 'rewardsystem'),
                    'id' => 'rs_my_rewards_friend_email_message_placeholder',
                    'css' => 'min-width:550px;',
                    'std' => 'Enter your Message',
                    'default' => 'Enter your Message',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_friend_email_message_placeholder',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_field_placeholder_settings'),
                array(
                    'name' => __('Error Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_referfriend_error_settings'
                ),
                array(
                    'name' => __('Error Message to display when Friend Name Field is left empty', 'rewardsystem'),
                    'desc' => __('Enter your Error Message which will be appear in frontend if the Friend Name is Empty','rewardsystem'),
                    'id' => 'rs_my_rewards_friend_name_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Please Enter your Friend Name',
                    'default' => 'Please Enter your Friend Name',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_friend_name_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when Friend Email Field is left empty', 'rewardsystem'),
                    'desc' => __('Enter your Error Message which will be appear in frontend if the Friend Email is Empty','rewardsystem'),
                    'id' => 'rs_my_rewards_friend_email_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Please Enter your Friend Email',
                    'default' => 'Please Enter your Friend Email',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_friend_email_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when Email format is not valid', 'rewardsystem'),
                    'desc' => __('Enter your Error Message which will be appear in frontend if the Friend Email is not Valid','rewardsystem'),
                    'id' => 'rs_my_rewards_friend_email_is_not_valid',
                    'css' => 'min-width:550px;',
                    'std' => 'Enter Email is not Valid',
                    'default' => 'Enter Email is not Valid',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_friend_email_is_not_valid',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when Email Subject is left empty', 'rewardsystem'),
                    'desc' => __('Enter your Error Message which will be appear in frontend if the Email Subject is Empty','rewardsystem'),
                    'id' => 'rs_my_rewards_email_subject_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Email Subject should not be left blank',
                    'default' => 'Email Subject should not be left blank',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_email_subject_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when Email Message is left empty', 'rewardsystem'),
                    'desc' => __('Enter your Error Message which will be appear in frontend if the Email Message is Empty','rewardsystem'),
                    'id' => 'rs_my_rewards_email_message_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Please Enter your Message',
                    'default' => 'Please Enter your Message',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_email_message_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message to display when I agree checkbox is unchecked', 'rewardsystem'),
                    'desc' => __('Enter your Error Message which will be appear in frontend if i agree is unchecked','rewardsystem'),
                    'id' => 'rs_iagree_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Please Accept our Terms and Condition',
                    'default' => 'Please Accept our Terms and Condition',
                    'type' => 'text',
                    'newids' => 'rs_iagree_error_message',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_referfriend_error_settings'),
                array(
                    'name' => __('Custom CSS Settings', 'rewardsystem'),
                    'type' => 'title',
                    'desc' => 'Try !important if styles doesn\'t apply ',
                    'id' => '_rs_refer_a_friend_custom_css_settings'
                ),
                array(
                    'name' => __('Custom CSS', 'rewardsystem'),
                    'desc' => __('Enter the Custom CSS which will be applied on top of Refer a Friend Shortcode', 'rewardsystem'),
                    'id' => 'rs_refer_a_friend_custom_css',
                    'css' => 'min-width:350px;min-height:350px;',
                    'std' => '#rs_refer_a_friend_form { } #rs_friend_name { } #rs_friend_email { } #rs_friend_subject { } #rs_your_message { } #rs_refer_submit { }',
                    'default' => '#rs_refer_a_friend_form { } #rs_friend_name { } #rs_friend_email { } #rs_friend_subject { } #rs_your_message { } #rs_refer_submit { }',
                    'type' => 'textarea',
                    'newids' => 'rs_refer_a_friend_custom_css',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_refer_a_friend_custom_css_settings'),
                array(
                    'name' => __('Shortcodes used in Refer a Friend', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_shortcodes_in_refer_a_friend',
                ),
                array(
                    'type' => 'title',
                    'desc' => '<b>[site_referral_url]</b> - To display referrer url<br><br>'
                    . '<b>{termsandconditions}</b> - To display the link for terms and conditions',
                ),
                array('type' => 'sectionend', 'id' => 'rs_shortcodes_in_refer_a_friend'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSReferAFriend::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSReferAFriend::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            foreach (RSReferAFriend::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        

        public static function reward_system_process_ajax_request() {
            global $woocommerce;
            if (isset($_POST)) {
                if (isset($_POST['friendname'])) {
                    $friendname = $_POST['friendname'];
                }
                if (isset($_POST['friendemail'])) {
                    $friendemail = $_POST['friendemail'];
                }
                if (isset($_POST['friendsubject'])) {
                    $friendsubject = $_POST['friendsubject'];
                }
                if (isset($_POST['friendmessage'])) {
                    
                }
                $name_n = explode(",", $friendname);
                $email_n = explode(",", $friendemail);
                foreach ($email_n as $key => $value) {
                    $friendmessage = __('Hi ', 'rewardsystem') . $name_n[$key] . '<br>';
                    $friendmessage .= $_POST['friendmessage'];
                    ob_start();
                    wc_get_template('emails/email-header.php', array('email_heading' => $friendsubject));
                    echo wpautop(stripslashes($friendmessage));
                    wc_get_template('emails/email-footer.php');
                    $woo_rs_msg = ob_get_clean();
                    $headers = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    $headers .= "From: " . get_option('woocommerce_email_from_name') . " <" . get_option('woocommerce_email_from_address') . ">\r\n";
                    $headers .= "Reply-To: " . get_option('woocommerce_email_from_name') . " <" . get_option('woocommerce_email_from_address') . ">\r\n";
                    if (get_option('rs_select_mail_function') == '1') {
                        mail($value, $friendsubject, $woo_rs_msg, $headers);
                    } else {
                        if ((float) $woocommerce->version <= (float) ('2.2.0')) {
                            wp_mail($value, $friendsubject, $woo_rs_msg, $headers);
                        } else {
                            $mailer = WC()->mailer();
                            $mailer->send($value, $friendsubject, $woo_rs_msg, $headers);
                        }
                    }
                    error_reporting(E_ALL);
                    ini_set('display_errors', '1');
                }
            }
            exit();
        }
        
        public static function rs_function_to_reset_refer_a_friend_tab() {
            $settings = RSReferAFriend::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);            
        }

    }

    RSReferAFriend::init();
}