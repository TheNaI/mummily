<?php
/*
 * MyAccount Tab Setting
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSMyaccount')) {

    class RSMyaccount {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_myaccount', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_myaccount', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));

            add_action('admin_head', array(__CLASS__, 'add_script_to_dashboard'));

            add_action('admin_head', array(__CLASS__, 'rs_chosen_user_role'));

            add_action('woocommerce_admin_field_rs_select_exclude_user_for_referral_link', array(__CLASS__, 'rs_exclude_user_as_hide_referal_link'));

            add_action('woocommerce_admin_field_rs_select_user_for_referral_link', array(__CLASS__, 'rs_include_user_as_hide_referal_link'));

            add_action('fp_action_to_reset_settings_rewardsystem_myaccount', array(__CLASS__, 'rs_function_to_reset_myaccount_tab'));

            add_action('woocommerce_admin_field_image_uploader', array(__CLASS__, 'rs_add_upload_your_facebook_share_image'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_myaccount'] = __('My Account', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;
            global $wp_roles;
            foreach ($wp_roles->roles as $values => $key) {
                $userroleslug[] = $values;
                $userrolename[] = $key['name'];
            }
            $newcombineduserrole = array_combine((array) $userroleslug, (array) $userrolename);
            return apply_filters('woocommerce_rewardsystem_myaccount_settings', array(
                array(
                    'name' => __('My Account Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_myaccount_setting',
                ),
                array(
                    'name' => __('Generate Referral Link Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_my_generate_referral_settings'
                ),
                array(
                    'name' => __('Generate Referral Link', 'rewardsystem'),
                    'id' => 'rs_show_hide_generate_referral',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_generate_referral',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Referral System of SUMO Reward Points is accessible by', 'rewardsystem'),
                    'id' => 'rs_select_type_of_user_for_referral',
                    'css' => 'min-width:100px;',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('All Users', 'rewardsystem'),
                        '2' => __('Include User(s)', 'rewardsystem'),
                        '3' => __('Exclude User(s)', 'rewardsystem'),
                        '4' => __('Include User Role(s)', 'rewardsystem'),
                        '5' => __('Exclude User Role(s)', 'rewardsystem'),
                    ),
                    'newids' => 'rs_select_type_of_user_for_referral',
                    'desc' => __('Referral System includes Referral Table,Refer A Friend Form and Generate Referral Link', 'rewardsystem'),
                    'desc_tip' => true
                ),
                array(
                    'type' => 'rs_select_user_for_referral_link',
                ),
                array(
                    'type' => 'rs_select_exclude_user_for_referral_link',
                ),
                array(
                    'name' => __('Select the User Role for Providing access to Referral System', 'rewardsystem'),
                    'id' => 'rs_select_users_role_for_show_referral_link',
                    'css' => 'min-width:343px;',
                    'std' => '',
                    'default' => '',
                    'placeholder' => 'Select for a User Role',
                    'type' => 'multiselect',
                    'options' => $newcombineduserrole,
                    'newids' => 'rs_select_users_role_for_show_referral_link',
                    'desc_tip' => false,
                ),
                array(
                    'name' => __('Select the User Role for Preventing access to Referral System', 'rewardsystem'),
                    'id' => 'rs_select_exclude_users_role_for_show_referral_link',
                    'css' => 'min-width:343px;',
                    'std' => '',
                    'default' => '',
                    'placeholder' => 'Select for a User Role',
                    'type' => 'multiselect',
                    'options' => $newcombineduserrole,
                    'newids' => 'rs_select_exclude_users_role_for_show_referral_link',
                    'desc_tip' => false,
                ),
                array(
                    'name' => __('Fallback Message for Referral Restriction', 'rewardsystem'),
                    'id' => 'rs_display_msg_when_access_is_prevented',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_display_msg_when_access_is_prevented',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Fallback Message for Referral Restriction', 'rewardsystem'),
                    'id' => 'rs_msg_for_restricted_user',
                    'css' => 'min-width:550px',
                    'std' => 'Referral System is currently restricted for your account',
                    'default' => 'Referral System is currently restricted for your account',
                    'type' => 'text',
                    'newids' => 'rs_msg_for_restricted_user',
                ),
                array(
                    'name' => __('Facebook Share Button', 'rewardsystem'),
                    'id' => 'rs_account_show_hide_facebook_like_button',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_account_show_hide_facebook_like_button',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Title used for Facebook Share', 'rewardsystem'),
                    'desc' => __('Enter the title of website that shown in Facebook Share', 'rewardsystem'),
                    'type' => 'text',
                    'id' => 'rs_facebook_title',
                    'std' => get_bloginfo(),
                    'default' => get_bloginfo(),
                    'css' => 'min-width:150px;',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Description used for Facebook Share', 'rewardsystem'),
                    'desc' => __('Enter the description of website that shown in Facebook Share', 'rewardsystem'),
                    'type' => 'text',
                    'id' => 'rs_facebook_description',
                    'std' => get_option('blogdescription'),
                    'default' => get_option('blogdescription'),
                    'css' => 'min-width:150px;',
                    'desc_tip' => true,
                ),
                array(
                    'type' => 'image_uploader',
                ),
                array(
                    'name' => __('Twitter Tweet Button', 'rewardsystem'),
                    'id' => 'rs_account_show_hide_twitter_tweet_button',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_account_show_hide_twitter_tweet_button',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Google+1 Button', 'rewardsystem'),
                    'id' => 'rs_acount_show_hide_google_plus_button',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'desc_tip' => true,
                    'newids' => 'rs_acount_show_hide_google_plus_button',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Referral Link Table Position', 'rewardsystem'),
                    'id' => 'rs_display_generate_referral',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_display_generate_referral',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Before My Account ', 'rewardsystem'),
                        '2' => __('After My Account', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Generate Referral Link Label', 'rewardsystem'),
                    'id' => 'rs_generate_link_label',
                    'css' => 'min-width:550px',
                    'std' => 'Generate Referral Link',
                    'default' => 'Generate Referral Link',
                    'type' => 'text',
                    'newids' => 'rs_generate_link_label',
                ),
                array(
                    'name' => __('S.No Label', 'rewardsystem'),
                    'id' => 'rs_generate_link_sno_label',
                    'css' => 'min-width:550px',
                    'std' => 'S.No',
                    'default' => 'S.No',
                    'type' => 'text',
                    'newids' => 'rs_generate_link_sno_label',
                ),
                array(
                    'name' => __('Date Label', 'rewardsystem'),
                    'id' => 'rs_generate_link_date_label',
                    'css' => 'min-width:550px',
                    'std' => 'Date',
                    'default' => 'Date',
                    'type' => 'text',
                    'newids' => 'rs_generate_link_date_label',
                ),
                array(
                    'name' => __('Referral Link Label', 'rewardsystem'),
                    'id' => 'rs_generate_link_referrallink_label',
                    'css' => 'min-width:550px',
                    'std' => 'Referral Link',
                    'default' => 'Referral Link',
                    'type' => 'text',
                    'newids' => 'rs_generate_link_referrallink_label',
                ),
                array(
                    'name' => __('Social Label', 'rewardsystem'),
                    'id' => 'rs_generate_link_social_label',
                    'css' => 'min-width:550px',
                    'std' => 'Social',
                    'default' => 'Social',
                    'type' => 'text',
                    'newids' => 'rs_generate_link_social_label',
                ),
                array(
                    'name' => __('Action Label', 'rewardsystem'),
                    'id' => 'rs_generate_link_action_label',
                    'css' => 'min-width:550px',
                    'std' => 'Action',
                    'default' => 'Action',
                    'type' => 'text',
                    'newids' => 'rs_generate_link_action_label',
                ),
                array(
                    'name' => __('Generate Referral Link Button Label', 'rewardsystem'),
                    'id' => 'rs_generate_link_button_label',
                    'css' => 'min-width:550px',
                    'std' => 'Generate Referral Link',
                    'default' => 'Generate Referral Link',
                    'type' => 'text',
                    'newids' => 'rs_generate_link_button_label',
                ),
                array(
                    'name' => __('Generate Referral Link based on Username/User ID', 'rewardsystem'),
                    'id' => 'rs_generate_referral_link_based_on_user',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_generate_referral_link_based_on_user',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Username', 'rewardsystem'),
                        '2' => __('User ID', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Type of Referral Link to be displayed', 'rewardsystem'),
                    'id' => 'rs_show_hide_generate_referral_link_type',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_generate_referral_link_type',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Default', 'rewardsystem'),
                        '2' => __('Static Url', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Prefill Generate Referral Link', 'rewardsystem'),
                    'id' => 'rs_prefill_generate_link',
                    'css' => 'min-width:550px',
                    'std' => site_url(),
                    'default' => site_url(),
                    'type' => 'text',
                    'newids' => 'rs_prefill_generate_link',
                ),
                array(
                    'name' => __('My Referral Link Label', 'rewardsystem'),
                    'id' => 'rs_my_referral_link_button_label',
                    'css' => 'min-width:550px',
                    'std' => 'My Referral Link',
                    'default' => 'My Referral Link',
                    'type' => 'text',
                    'newids' => 'rs_my_referral_link_button_label',
                ),
                array(
                    'name' => __('Static Referral Link', 'rewardsystem'),
                    'id' => 'rs_static_generate_link',
                    'css' => 'min-width:550px',
                    'std' => site_url(),
                    'default' => site_url(),
                    'type' => 'text',
                    'newids' => 'rs_static_generate_link',
                ),
                array('type' => 'sectionend', 'id' => '_rs_my_generate_referral_settings'),
                array(
                    'name' => __('My Account Gift Voucher Redeem Table', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_my_account_voucher_table_settings'
                ),
                array(
                    'name' => __('Gift Voucher Field', 'rewardsystem'),
                    'id' => 'rs_show_hide_redeem_voucher',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_redeem_voucher',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Redeem your Gift Voucher Label', 'rewardsystem'),
                    'id' => 'rs_redeem_your_gift_voucher_label',
                    'css' => 'min-width:350px;',
                    'std' => 'Redeem your Gift Voucher',
                    'default' => 'Redeem your Gift Voucher',
                    'newids' => 'rs_redeem_your_gift_voucher_label',
                    'type' => 'text',
                ),
                array(
                    'name' => __('Redeem your Gift Voucher Field Placeholder', 'rewardsystem'),
                    'id' => 'rs_redeem_your_gift_voucher_placeholder',
                    'css' => 'min-width:350px;',
                    'std' => 'Please enter your Reward Code',
                    'default' => 'Please enter your Reward Code',
                    'newids' => 'rs_redeem_your_gift_voucher_placeholder',
                    'type' => 'text',
                ),
                array(
                    'name' => __('Redeem Gift Voucher Button Label', 'rewardsystem'),
                    'id' => 'rs_redeem_gift_voucher_button_label',
                    'css' => 'min-width:350px;',
                    'std' => 'Redeem Gift Voucher',
                    'default' => 'Redeem Gift Voucher',
                    'newids' => 'rs_redeem_gift_voucher_button_label',
                    'type' => 'text',
                ),
                array(
                    'name' => __('Voucher Field Position', 'rewardsystem'),
                    'id' => 'rs_redeem_voucher_position',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_redeem_voucher_position',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Before My Account', 'rewardsystem'),
                        '2' => __('After My Account', 'rewardsystem'),
                    ),
                ),
                array('type' => 'sectionend', 'id' => '_rs_my_account_voucher_table_settings'),
                array(
                    'name' => __('Your Subscribe Link Settings in My Account Page', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_my_account_subscribe_link_settings'
                ),
                array(
                    'name' => __('Your Subscribe Link', 'rewardsystem'),
                    'id' => 'rs_show_hide_your_subscribe_link',
                    'newids' => 'rs_show_hide_your_subscribe_link',
                    'class' => 'rs_show_hide_your_subscribe_link',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Unsubscribe field Caption', 'rewardsystem'),
                    'desc' => __('Enter the text that will be displayed as the Unsubscribe field Caption', 'rewardsystem'),
                    'id' => 'rs_unsub_field_caption',
                    'css' => 'min-width:550px;',
                    'std' => 'Unsubscribe Here To Stop Receiving Reward Points Emails',
                    'type' => 'text',
                    'newids' => 'rs_unsub_field_caption',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_my_account_subscribe_link_settings'),
                array(
                    'name' => __('My Account Reward Table Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_my_account_rewards_table_settings'
                ),
                array(
                    'name' => __('Points Log Sorting', 'rewardsystem'),
                    'id' => 'rs_points_log_sorting',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_points_log_sorting',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Ascending Order', 'rewardsystem'),
                        '2' => __('Descending Order', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('My Cashback Table', 'rewardsystem'),
                    'id' => 'rs_my_cashback_table',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'desc_tip' => true,
                    'default' => '1',
                    'newids' => 'rs_my_cashback_table',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('My Rewards Table', 'rewardsystem'),
                    'id' => 'rs_my_reward_table',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'desc_tip' => true,
                    'default' => '1',
                    'newids' => 'rs_my_reward_table',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Search Box in My Rewards Table', 'rewardsystem'),
                    'id' => 'rs_show_hide_search_box_in_my_rewards_table',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'desc_tip' => true,
                    'default' => '1',
                    'newids' => 'rs_show_hide_search_box_in_my_rewards_table',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Points Expire Column', 'rewardsystem'),
                    'id' => 'rs_my_reward_points_expire',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'desc_tip' => true,
                    'default' => '1',
                    'newids' => 'rs_my_reward_points_expire',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Username Column', 'rewardsystem'),
                    'id' => 'rs_my_reward_points_user_name_hide',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'desc_tip' => true,
                    'default' => '1',
                    'newids' => 'rs_my_reward_points_user_name_hide',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Page Size in My Rewards Table', 'rewardsystem'),
                    'id' => 'rs_show_hide_page_size_my_rewards',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'desc_tip' => true,
                    'default' => '1',
                    'newids' => 'rs_show_hide_page_size_my_rewards',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array('type' => 'sectionend', 'id' => '_rs_my_account_rewards_table_settings'),
                array(
                    'name' => __('My Cashback Table Label Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_my_cashback_label_settings'
                ),
                array(
                    'name' => __('My Cashback Label', 'rewardsystem'),
                    'desc' => __('Enter the My Cashback Label', 'rewardsystem'),
                    'id' => 'rs_my_cashback_title',
                    'css' => 'min-width:550px;',
                    'std' => 'My Cashback',
                    'default' => 'My Cashback',
                    'type' => 'text',
                    'newids' => 'rs_my_cashback_title',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('S.No Label', 'rewardsystem'),
                    'desc' => __('Enter the Serial Number Label', 'rewardsystem'),
                    'id' => 'rs_my_cashback_sno_label',
                    'css' => 'min-width:550px;',
                    'std' => 'S.No',
                    'default' => 'S.No',
                    'type' => 'text',
                    'newids' => 'rs_my_cashback_sno_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Username Label', 'rewardsystem'),
                    'desc' => __('Enter the Username Label', 'rewardsystem'),
                    'id' => 'rs_my_cashback_userid_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Username',
                    'default' => 'Username',
                    'type' => 'text',
                    'newids' => 'rs_my_cashback_userid_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Requested for Cashback Label', 'rewardsystem'),
                    'desc' => __('Enter the Requested for Cashback Label', 'rewardsystem'),
                    'id' => 'rs_my_cashback_requested_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Requested for Cashback',
                    'default' => 'Requested for Cashback',
                    'type' => 'text',
                    'newids' => 'rs_my_cashback_requested_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Status Label', 'rewardsystem'),
                    'desc' => __('Enter the Status On Label', 'rewardsystem'),
                    'id' => 'rs_my_cashback_status_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Status',
                    'default' => 'Status',
                    'type' => 'text',
                    'newids' => 'rs_my_cashback_status_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Action Label', 'rewardsystem'),
                    'desc' => __('Enter the Action On Label', 'rewardsystem'),
                    'id' => 'rs_my_cashback_action_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Action',
                    'default' => 'Action',
                    'type' => 'rs_action_for_cash_back',
                    'newids' => 'rs_my_cashback_action_label',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_my_cashback_label_settings'),
                array(
                    'name' => __('Referrer Label Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_referrer_label_settings'
                ),
                array(
                    'name' => __('To display the Message to Referral Person', 'rewardsystem'),
                    'id' => 'rs_show_hide_generate_referral_message',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_generate_referral_message',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Message to display the Referral Person', 'rewardsystem'),
                    'id' => 'rs_show_hide_generate_referral_message_text',
                    'css' => 'min-width:550px',
                    'std' => 'You are being referred by [rs_referred_user_name]',
                    'default' => 'You are being referred by [rs_referred_user_name]',
                    'type' => 'text',
                    'newids' => 'rs_show_hide_generate_referral_message_text',
                ),
                array('type' => 'sectionend', 'id' => '_rs_referrer_label_settings'),
                array(
                    'name' => __('My Reward Table Label Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_my_reward_label_settings'
                ),
                array(
                    'name' => __('Reward Table Postion', 'rewardsystem'),
                    'id' => 'rs_reward_table_position',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_reward_table_position',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('After My Account', 'rewardsystem'),
                        '2' => __('Before My Account', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('My Rewards Label', 'rewardsystem'),
                    'desc' => __('Enter the My Rewards Label', 'rewardsystem'),
                    'id' => 'rs_my_rewards_title',
                    'css' => 'min-width:550px;',
                    'std' => 'My Rewards',
                    'default' => 'My Rewards',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_title',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Display Reward Points Label Position', 'rewardsystem'),
                    'id' => 'rs_reward_point_label_position',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_reward_point_label_position',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Before Points', 'rewardsystem'),
                        '2' => __('After Points', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Total Points Label', 'rewardsystem'),
                    'desc' => __('Enter the Total Points Label', 'rewardsystem'),
                    'id' => 'rs_my_rewards_total',
                    'css' => 'min-width:550px;',
                    'std' => 'Total Points: ',
                    'default' => 'Total Points:',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_total',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Display Currency Value of Total Points', 'rewardsystem'),
                    'id' => 'rs_reward_currency_value',
                    'css' => 'min-width:150px;',
                    'std' => '2',
                    'default' => '2',
                    'newids' => 'rs_reward_currency_value',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('S.No Label', 'rewardsystem'),
                    'desc' => __('Enter the Serial Number Label', 'rewardsystem'),
                    'id' => 'rs_my_rewards_sno_label',
                    'css' => 'min-width:550px;',
                    'std' => 'S.No',
                    'default' => 'S.No',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_sno_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Username Label', 'rewardsystem'),
                    'desc' => __('Enter the Username Label', 'rewardsystem'),
                    'id' => 'rs_my_rewards_userid_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Username',
                    'default' => 'Username',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_userid_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Reward for Label', 'rewardsystem'),
                    'desc' => __('Enter the Reward for Label', 'rewardsystem'),
                    'id' => 'rs_my_rewards_rewarder_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Reward for',
                    'default' => 'Reward for',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_rewarder_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Earned Points Label', 'rewardsystem'),
                    'desc' => __('Enter the Earned Points Label', 'rewardsystem'),
                    'id' => 'rs_my_rewards_points_earned_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Earned Points',
                    'default' => 'Earned Points',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_points_earned_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeemed Points Label', 'rewardsystem'),
                    'desc' => __('Enter the Redeemed Points Label', 'rewardsystem'),
                    'id' => 'rs_my_rewards_redeem_points_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Redeemed Points',
                    'default' => 'Redeemed Points',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_redeem_points_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Total Points Label', 'rewardsystem'),
                    'desc' => __('Enter the Total Points Label', 'rewardsystem'),
                    'id' => 'rs_my_rewards_total_points_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Total Points',
                    'default' => 'Total Points',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_total_points_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Earned Date Label', 'rewardsystem'),
                    'desc' => __('Enter the Date Label', 'rewardsystem'),
                    'id' => 'rs_my_rewards_date_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Earned Date',
                    'default' => 'Earned Date',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_date_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Points Expires On', 'rewardsystem'),
                    'desc' => __('Enter the Point Expired On Label', 'rewardsystem'),
                    'id' => 'rs_my_rewards_points_expired_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Points Expires On',
                    'default' => 'Points Expires On',
                    'type' => 'text',
                    'newids' => 'rs_my_rewards_points_expired_label',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_my_reward_label_settings'),
                array(
                    'name' => __('My Referral Table Label Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_my_referal_label_settings'
                ),
                array(
                    'name' => __('Referral Table ', 'rewardsystem'),
                    'id' => 'rs_show_hide_referal_table',
                    'css' => 'min-width:150px;',
                    'std' => '2',
                    'default' => '2',
                    'default' => '2',
                    'newids' => 'rs_show_hide_referal_table',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('Referral Table Label', 'rewardsystem'),
                    'desc' => __('Enter the Referral Table Label', 'rewardsystem'),
                    'id' => 'rs_referal_table_title',
                    'css' => 'min-width:550px;',
                    'std' => 'Referral Table',
                    'default' => 'Referral Table',
                    'type' => 'text',
                    'newids' => 'rs_referal_table_title',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('S.No Label', 'rewardsystem'),
                    'desc' => __('Enter the Serial Number Label', 'rewardsystem'),
                    'id' => 'rs_my_referal_sno_label',
                    'css' => 'min-width:550px;',
                    'std' => 'S.No',
                    'default' => 'S.No',
                    'type' => 'text',
                    'newids' => 'rs_my_referal_sno_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Referral Username Label', 'rewardsystem'),
                    'desc' => __('Enter the Referral Username Label', 'rewardsystem'),
                    'id' => 'rs_my_referal_userid_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Username',
                    'default' => 'Username',
                    'type' => 'text',
                    'newids' => 'rs_my_referal_userid_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Total Referral Points Label', 'rewardsystem'),
                    'desc' => __('Enter the Total Referral Points Label', 'rewardsystem'),
                    'id' => 'rs_my_total_referal_points_label',
                    'css' => 'min-width:550px;',
                    'std' => 'Total Referral Points',
                    'default' => 'Total Referral Points',
                    'type' => 'text',
                    'newids' => 'rs_my_total_referal_points_label',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_my_referal_label_settings'),
                array(
                    'name' => __('Extra Class Name for Button', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_myaccount_custom_class_name',
                ),
                array(
                    'name' => __('Extra Class Name for Generate Referral Link Button', 'rewardsystem'),
                    'desc' => __('Add Extra Class Name to the My Account Generate Referral Link Button, Don\'t Enter dot(.) before Class Name', 'rewardsystem'),
                    'id' => 'rs_extra_class_name_generate_referral_link',
                    'css' => 'min-width:550px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_extra_class_name_generate_referral_link',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Extra Class Name for Redeem Gift Voucher Button', 'rewardsystem'),
                    'desc' => __('Add Extra Class Name to the My Account Redeem Gift Voucher Button, Don\'t Enter dot(.) before Class Name', 'rewardsystem'),
                    'id' => 'rs_extra_class_name_redeem_gift_voucher_button',
                    'css' => 'min-width:550px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_extra_class_name_redeem_gift_voucher_button',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_myaccount_custom_class_name'),
                array(
                    'name' => __('Custom CSS Settings', 'rewardsystem'),
                    'type' => 'title',
                    'desc' => 'Try !important if styles doesn\'t apply ',
                    'id' => '_rs_my_reward_custom_css_settings'
                ),
                array(
                    'name' => __('Custom CSS', 'rewardsystem'),
                    'desc' => __('Enter the Custom CSS for My Account Page', 'rewardsystem'),
                    'id' => 'rs_myaccount_custom_css',
                    'css' => 'min-width:350px;min-height:350px;',
                    'std' => '#generate_referral_field { }  '
                    . '#rs_redeem_voucher_code { }  '
                    . '#ref_generate_now { } '
                    . ' #rs_submit_redeem_voucher { }',
                    'type' => 'textarea',
                    'newids' => 'rs_myaccount_custom_css',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_my_reward_custom_css_settings'),
                array(
                    'name' => __('Shortcodes used in My Account', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_shortcodes_in_myaccount',
                ),
                array(
                    'type' => 'title',
                    'desc' => '<b>[rs_referred_user_name]</b> - To display referrer name',
                ),
                array('type' => 'sectionend', 'id' => 'rs_shortcodes_in_myaccount'),
                array('type' => 'sectionend', 'id' => 'rs_myaccount_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSMyaccount::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSMyaccount::reward_system_admin_fields());
            update_option('rs_select_exclude_users_list_for_show_referral_link', $_POST['rs_select_exclude_users_list_for_show_referral_link']);
            update_option('rs_select_include_users_for_show_referral_link', $_POST['rs_select_include_users_for_show_referral_link']);
            update_option('rs_fbshare_image_url_upload', $_POST['rs_fbshare_image_url_upload']);
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSMyaccount::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function rs_exclude_user_as_hide_referal_link() {
            $field_id = "rs_select_exclude_users_list_for_show_referral_link";
            $field_label = "Select the Users for Preventing access to Referral System";
            $getuser = get_option('rs_select_exclude_users_list_for_show_referral_link');
            echo rs_function_to_add_field_for_user_select($field_id, $field_label, $getuser);
            ?>
            <style type="text/css">
                .chosen-container-single {
                    position:absolute;
                }

            </style>
            <?php
        }

        public static function rs_include_user_as_hide_referal_link() {
            $field_id = "rs_select_include_users_for_show_referral_link";
            $field_label = "Select the Users for Providing access to Referral System";
            $getuser = get_option('rs_select_include_users_for_show_referral_link');
            echo rs_function_to_add_field_for_user_select($field_id, $field_label, $getuser);
            ?>
            <style type="text/css">
                .chosen-container-single {
                    position:absolute;
                }

            </style>
            <?php
        }

        public static function rs_chosen_user_role() {
            global $woocommerce;
            if (isset($_GET['page'])) {
                if (isset($_GET['tab'])) {
                    if ($_GET['tab'] == 'rewardsystem_myaccount') {
                        if ((float) $woocommerce->version > (float) ('2.2.0')) {
                            echo rs_common_select_function('#rs_select_users_role_for_show_referral_link');
                            echo rs_common_select_function('#rs_select_exclude_users_role_for_show_referral_link');
                        } else {
                            echo rs_common_chosen_function('#rs_select_users_role_for_show_referral_link');
                            echo rs_common_chosen_function('#rs_select_exclude_users_role_for_show_referral_link');
                        }
                    }
                }
            }
        }

        public static function add_script_to_dashboard() {
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function () {
                    jQuery('#changepagesize').change(function (e) {
                        e.preventDefault();
                        var pageSize = jQuery(this).val();
                        jQuery('.footable').data('page-size', pageSize);
                        jQuery('.footable').trigger('footable_initialized');
                    });

                    jQuery('#changepagesizes').change(function (e) {
                        e.preventDefault();
                        var pageSize = jQuery(this).val();
                        jQuery('.footable').data('page-size', pageSize);
                        jQuery('.footable').trigger('footable_initialized');
                    });

                    jQuery('#changepagesizer').change(function (e) {
                        e.preventDefault();
                        var pageSize = jQuery(this).val();
                        jQuery('.footable').data('page-size', pageSize);
                        jQuery('.footable').trigger('footable_initialized');
                    });
                    jQuery('#changepagesizertemplates').change(function (e) {
                        e.preventDefault();
                        var pageSize = jQuery(this).val();
                        jQuery('.footable').data('page-size', pageSize);
                        jQuery('.footable').trigger('footable_initialized');
                    });
                });</script>
            <?php
        }

        public static function rs_function_to_reset_myaccount_tab() {
            $settings = RSMyaccount::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

        public static function rs_add_upload_your_facebook_share_image() {
            ?>           
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_fbshare_image_url_upload"><?php _e('Image used for Facebook Share', 'rewardsystem'); ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="text" id="rs_fbshare_image_url_upload" name="rs_fbshare_image_url_upload" value="<?php echo get_option('rs_fbshare_image_url_upload'); ?>"/>
                    <input type="submit" id="rs_fbimage_upload_button" name="rs_fbimage_upload_button" value="Upload Image"/>
                </td>
            </tr>            
            <?php
            rs_ajax_for_upload_your_gift_voucher('#rs_fbimage_upload_button');
        }

    }

    RSMyaccount::init();
}