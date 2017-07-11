<?php
/*
 * Reward Points for Action Tab Settings
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSRewardPointsForAction')) {

    class RSRewardPointsForAction {

        public static function init() {
            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_reward_points_for_action', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_reward_points_for_action', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system

            if (class_exists('bbPress')) {
                add_filter('woocommerce_rewardsystem_reward_points_for_action_settings', array(__CLASS__, 'add_field_for_create_topic'));
            }

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));

            add_action('admin_head', array(__CLASS__, 'rs_validation_for_input_field_in_reward_points_tab'));

            add_filter('woocommerce_rewardsystem_reward_points_for_action_settings', array(__CLASS__, 'reward_system_add_settings_to_action'));

            add_action('publish_post', array(__CLASS__, 'on_post_publish'), 10, 2);

            add_action('fp_action_to_reset_settings_rewardsystem_reward_points_for_action', array(__CLASS__, 'rs_function_to_reset_action_tab'));
        }

        public static function add_field_for_create_topic($settings) {
            $updated_settings = array();
            foreach ($settings as $section) {
                $updated_settings[] = $section;
                if (isset($section['id']) && 'rs_reward_points_for_action_setting' == $section['id'] &&
                        isset($section['type']) && 'sectionend' == $section['type']) {
                    $updated_settings[] = array(
                        'name' => __('Reward Points for bbPress', 'rewardsystem'),
                        'type' => 'title',
                        'id' => '_rs_reward_point_for_topic'
                    );
                    $updated_settings[] = array(
                        'name' => __('Topic Creation Reward Points', 'rewardsystem'),
                        'desc' => __('Enable this option to award Reward Points for Topic Creation in bbPress', 'rewardsystem'),
                        'id' => 'rs_enable_reward_points_for_create_topic',
                        'std' => 'no',
                        'default' => 'no',
                        'type' => 'checkbox',
                        'newids' => 'rs_enable_reward_points_for_create_topic',
                    );

                    $updated_settings[] = array(
                        'name' => __('Topic Creation Reward Points', 'rewardsystem'),
                        'desc' => __('Enter the Reward Points that will be given for Topic Creation', 'rewardsystem'),
                        'id' => 'rs_reward_points_for_creatic_topic',
                        'css' => 'min-width:150px;',
                        'std' => '',
                        'default' => '',
                        'type' => 'text',
                        'newids' => 'rs_reward_points_for_creatic_topic',
                        'desc_tip' => true,
                    );
                    $updated_settings[] = array(
                        'name' => __('Topic Reply Reward Points', 'rewardsystem'),
                        'desc' => __('Enable this option to award Reward Points for Topic Reply in bbPress', 'rewardsystem'),
                        'id' => 'rs_enable_reward_points_for_reply_topic',
                        'std' => 'no',
                        'default' => 'no',
                        'type' => 'checkbox',
                        'newids' => 'rs_enable_reward_points_for_reply_topic',
                    );

                    $updated_settings[] = array(
                        'name' => __('Topic Reply Reward Points', 'rewardsystem'),
                        'desc' => __('Enter the Reward Points that will be given for Topic Reply', 'rewardsystem'),
                        'id' => 'rs_reward_points_for_reply_topic',
                        'css' => 'min-width:150px;',
                        'std' => '',
                        'default' => '',
                        'type' => 'text',
                        'newids' => 'rs_reward_points_for_reply_topic',
                        'desc_tip' => true,
                    );

                    $updated_settings[] = array(
                        'type' => 'sectionend',
                        'id' => '_rs_reward_point_for_topic'
                    );
                }
            }
            return $updated_settings;
        }

        /*
         * Function to Define Name of the tab.
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_reward_points_for_action'] = __('Action Reward Points', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function for label Settings in Reward Points For Action.
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;
            return apply_filters('woocommerce_rewardsystem_reward_points_for_action_settings', array(
                array(
                    'name' => __('Action Reward Points Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_reward_points_for_action_setting',
                ),
                array(
                    'name' => __('Account Sign up Reward Points is Awarded for', 'rewardsystem'),
                    'desc' => __('Select the Account Sign up Points Reward type', 'rewardsystem'),
                    'id' => 'rs_select_account_signup_points_award',
                    'type' => 'select',
                    'css' => 'min-width:150px;',
                    'newids' => 'rs_select_account_signup_points_award',
                    'std' => '1',
                    'default' => '1',
                    'options' => array(
                        '1' => __('All Users', 'rewardsystem'),
                        '2' => __('Referred Users', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Account Sign up Reward Points after First Purchase', 'rewardsystem'),
                    'desc' => __('Enable this option to award account sign up reward points after first purchase', 'rewardsystem'),
                    'id' => 'rs_reward_signup_after_first_purchase',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_reward_signup_after_first_purchase',
                ),
                array(
                    'name' => __('Prevent Product Purchase Reward Points for First Purchase', 'rewardsystem'),
                    'desc' => __('Enable this option to prevent product purchase reward points for first purchase', 'rewardsystem'),
                    'id' => 'rs_signup_points_with_purchase_points',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_signup_points_with_purchase_points',
                ),
                array(
                    'name' => __('Account Sign up Reward Points', 'rewardsystem'),
                    'desc' => __('Enter the Reward Points that will be given for account sign up', 'rewardsystem'),
                    'id' => 'rs_reward_signup',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_reward_signup',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Restrict Product Review Reward Points to One Review per Product per User', 'rewardsystem'),
                    'desc' => __('Enable this option to restrict product review reward points to one review per product per user', 'rewardsystem'),
                    'id' => 'rs_restrict_reward_product_review',
                    'css' => 'min-width:150px;',
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'newids' => 'rs_restrict_reward_product_review',
                ),
                array(
                    'name' => __('Product Review Reward Points should be awarded only for Purchased User', 'rewardsystem'),
                    'desc' => __('Enable this option to award product review reward points only for purchased user', 'rewardsystem'),
                    'id' => 'rs_reward_for_comment_product_review',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_reward_for_comment_product_review',
                ),
                array(
                    'name' => __('Product Review Reward Points', 'rewardsystem'),
                    'desc' => __('Enter the Reward Points that will be given for Product Review', 'rewardsystem'),
                    'id' => 'rs_reward_product_review',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_reward_product_review',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Blog Post Comment Reward Points', 'rewardsystem'),
                    'desc' => __('Enable this option to award blog post comment reward points', 'rewardsystem'),
                    'id' => 'rs_reward_for_comment_Post',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_reward_for_comment_Post',
                ),
                array(
                    'name' => __('Blog Post Comment Reward Points', 'rewardsystem'),
                    'desc' => __('Enter the reward points that will be given for blog post comment', 'rewardsystem'),
                    'id' => 'rs_reward_post_review',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_reward_post_review',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Product Creation Reward Points', 'rewardsystem'),
                    'desc' => __('Enable this option to award product creation reward points', 'rewardsystem'),
                    'id' => 'rs_reward_for_enable_product_create',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_reward_for_enable_product_create',
                ),
                array(
                    'name' => __('Product Creation Reward Points', 'rewardsystem'),
                    'desc' => __('Enter the reward points that will be given for product creation', 'rewardsystem'),
                    'id' => 'rs_reward_Product_create',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_reward_Product_create',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Page Comment Reward Points', 'rewardsystem'),
                    'desc' => __('Enable this option to award page comment reward points', 'rewardsystem'),
                    'id' => 'rs_reward_for_comment_Page',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_reward_for_comment_Page',
                ),
                array(
                    'name' => __('Page Comment Reward Points', 'rewardsystem'),
                    'desc' => __('Enter the reward points that will be given for page comment', 'rewardsystem'),
                    'id' => 'rs_reward_page_review',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_reward_page_review',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Blog Post Creation Reward Points', 'rewardsystem'),
                    'desc' => __('Enable this option to award blog post creation reward points', 'rewardsystem'),
                    'id' => 'rs_reward_for_Creating_Post',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_reward_for_Creating_Post',
                ),
                array(
                    'name' => __('Blog Post Creation Reward Points', 'rewardsystem'),
                    'desc' => __('Enter the Reward Points that will be given for blog post creation', 'rewardsystem'),
                    'id' => 'rs_reward_post',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_reward_post',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Referral Account Signup Reward Points is Awarded ', 'rewardsystem'),
                    'desc' => __('Select Referral Reward Account Signup Points Reward type ', 'rewardsystem'),
                    'id' => 'rs_select_referral_points_award',
                    'type' => 'select',
                    'css' => 'min-width:150px;',
                    'newids' => 'rs_select_referral_points_award',
                    'std' => '1',
                    'default' => '1',
                    'options' => array(
                        '1' => __('Instantly', 'rewardsystem'),
                        '2' => __('After Referral Places Minimum Number of Successful Order(s)', 'rewardsystem'),
                        '3' => __('After Referral Spents the Minimum Amount in Site', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Number of Successful Order(s)', 'rewardsystem'),
                    'desc' => __('Please Enter the Minimum Number Of Sucessful Orders', 'rewardsystem'),
                    'id' => 'rs_number_of_order_for_referral_points',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_number_of_order_for_referral_points',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Minimum Amount to be Spent by the User', 'rewardsystem'),
                    'desc' => __('Please Enter the Minimum Amount Spent by User', 'rewardsystem'),
                    'id' => 'rs_amount_of_order_for_referral_points',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_amount_of_order_for_referral_points',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Account Sign up Referral Reward Points after First Purchase', 'rewardsystem'),
                    'desc' => __('Enable this option to award referral reward points for account signup after first purchase', 'rewardsystem'),
                    'id' => 'rs_referral_reward_signup_after_first_purchase',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_referral_reward_signup_after_first_purchase',
                ),
                array(
                    'name' => __('Referral Reward Points for Account Signup', 'rewardsystem'),
                    'desc' => __('Please Enter the Referral Reward Points that will be earned for Account Signup', 'rewardsystem'),
                    'id' => 'rs_referral_reward_signup',
                    'css' => 'min-width:150px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rs_referral_reward_signup',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Enable Reward Points for Getting Referred', 'rewardsystem'),
                    'desc' => __('Enable the Reward Points that will be earned for Getting Referred', 'rewardsystem'),
                    'id' => 'rs_referral_reward_signup_getting_refer',
                    'std' => '2',
                    'type' => 'select',
                    'newids' => 'rs_referral_reward_signup_getting_refer',
                    'std' => '2',
                    'default' => '2',
                    'options' => array(
                        '1' => __('Enable', 'rewardsystem'),
                        '2' => __('Disable', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Enable Reward Points for Getting Referred after first purchase', 'rewardsystem'),
                    'desc' => __('Enable the Reward Points that will be earned for Getting Referred after first purchase', 'rewardsystem'),
                    'id' => 'rs_referral_reward_getting_refer_after_first_purchase',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_referral_reward_getting_refer_after_first_purchase',
                ),
                array(
                    'name' => __('Reward Points for Getting Referred', 'rewardsystem'),
                    'desc' => __('Please Enter the Reward Points that will be earned for Getting Referred', 'rewardsystem'),
                    'id' => 'rs_referral_reward_getting_refer',
                    'css' => 'min-width:150px;',
                    'std' => '1000',
                    'default' => '1000',
                    'type' => 'text',
                    'newids' => 'rs_referral_reward_getting_refer',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => 'rs_reward_points_for_action_setting'),
                array(
                    'name' => __('Daily Login Reward Points Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_point_action'
                ),
                array(
                    'name' => __('Daily Login Reward Points', 'rewardsystem'),
                    'desc' => __('Enable this option to award daily login reward points', 'rewardsystem'),
                    'id' => 'rs_enable_reward_points_for_login',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_enable_reward_points_for_login',
                ),
                array(
                    'name' => __('Daily Login Reward Points', 'rewardsystem'),
                    'desc' => __('Enter the Reward Points that will be given for Daily Login Reward Points', 'rewardsystem'),
                    'id' => 'rs_reward_points_for_login',
                    'css' => 'min-width:150px;',
                    'std' => '10',
                    'default' => '10',
                    'type' => 'text',
                    'newids' => 'rs_reward_points_for_login',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_point_action'),
                array(
                    'name' => __('Payment Gateway Reward Points Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_point_for_payment_gateway',
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_point_for_payment_gateway'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSRewardPointsForAction::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSRewardPointsForAction::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            foreach (RSRewardPointsForAction::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function reward_system_add_settings_to_action($settings) {
            $updated_settings = array();
            $mainvariable = array();
            global $woocommerce;
            foreach ($settings as $section) {
                if (isset($section['id']) && '_rs_reward_point_for_payment_gateway' == $section['id'] &&
                        isset($section['type']) && 'sectionend' == $section['type']) {
                    if (function_exists('WC')) {
                        foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                            $updated_settings[] = array(
                                'name' => __($gateway->title . ' Reward Type', 'rewardsystem'),
                                'desc' => __('Please Select Reward Type for ' . $gateway->title, 'rewardsystem'),
                                'id' => 'rs_reward_type_for_payment_gateways_' . $gateway->id,
                                'css' => 'min-width:150px;',
                                'std' => '',
                                'default' => '',
                                'type' => 'select',
                                'newids' => 'rs_reward_type_for_payment_gateways_' . $gateway->id,
                                'desc_tip' => true,
                                'options' => array(
                                    '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                    '2' => __('By Percentage of Cart Total', 'rewardsystem'),
                                ),
                            );
                            $updated_settings[] = array(
                                'name' => __($gateway->title . ' Reward Points', 'rewardsystem'),
                                'desc' => __('Please Enter Reward Points for ' . $gateway->title, 'rewardsystem'),
                                'id' => 'rs_reward_payment_gateways_' . $gateway->id,
                                'css' => 'min-width:150px;',
                                'std' => '',
                                'default' => '',
                                'type' => 'text',
                                'newids' => 'rs_reward_payment_gateways_' . $gateway->id,
                                'desc_tip' => true,
                            );
                            $updated_settings[] = array(
                                'name' => __($gateway->title . ' Reward Points in Percent %', 'rewardsystem'),
                                'desc' => __('Please Enter Reward Points for ' . $gateway->title . ' in Percent %', 'rewardsystem'),
                                'id' => 'rs_reward_points_for_payment_gateways_in_percent_' . $gateway->id,
                                'css' => 'min-width:150px;',
                                'std' => '',
                                'default' => '',
                                'type' => 'text',
                                'newids' => 'rs_reward_points_for_payment_gateways_in_percent_' . $gateway->id,
                                'desc_tip' => true,
                            );
                        }
                    } else {
                        if (class_exists('WC_Payment_Gateways')) {
                            $paymentgateway = new WC_Payment_Gateways();
                            foreach ($paymentgateway->payment_gateways()as $gateway) {
                                $updated_settings[] = array(
                                    'name' => __($gateway->title . ' Reward Type', 'rewardsystem'),
                                    'desc' => __('Please Select Reward Type for ' . $gateway->title, 'rewardsystem'),
                                    'id' => 'rs_reward_type_for_payment_gateways_' . $gateway->id,
                                    'css' => 'min-width:150px;',
                                    'std' => '',
                                    'default' => '',
                                    'type' => 'select',
                                    'newids' => 'rs_reward_type_for_payment_gateways_' . $gateway->id,
                                    'desc_tip' => true,
                                    'options' => array(
                                        '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                        '2' => __('By Percentage of Cart Total', 'rewardsystem'),
                                    ),
                                );
                                $updated_settings[] = array(
                                    'name' => __($gateway->title . ' Reward Points', 'rewardsystem'),
                                    'desc' => __('Please Enter Reward Points for ' . $gateway->title, 'rewardsystem'),
                                    'id' => 'rs_reward_payment_gateways_' . $gateway->id,
                                    'css' => 'min-width:150px;',
                                    'std' => '',
                                    'default' => '',
                                    'type' => 'text',
                                    'newids' => 'rs_reward_payment_gateways_' . $gateway->id,
                                    'desc_tip' => true,
                                );
                                $updated_settings[] = array(
                                    'name' => __($gateway->title . ' Reward Points in Percent %', 'rewardsystem'),
                                    'desc' => __('Please Enter Reward Points for ' . $gateway->title . ' in Percent %', 'rewardsystem'),
                                    'id' => 'rs_reward_points_for_payment_gateways_in_percent_' . $gateway->id,
                                    'css' => 'min-width:150px;',
                                    'std' => '',
                                    'default' => '',
                                    'type' => 'text',
                                    'newids' => 'rs_reward_points_for_payment_gateways_in_percent_' . $gateway->id,
                                    'desc_tip' => true,
                                );
                            }
                        }
                    }
                    $updated_settings[] = array(
                        'type' => 'sectionend', 'id' => '_rs_reward_system_payment_gateway',
                    );
                }
                $newsettings = array('type' => 'sectionend', 'id' => '_rs_reward_system_pg_end');
                $updated_settings[] = $section;
            }
            return $updated_settings;
        }

        public static function rs_validation_for_input_field_in_reward_points_tab() {
            global $woocommerce;
            if (function_exists('WC')) {
                foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                    self::rs_script_for_validation($gateway);
                }
            } else {
                if (class_exists('WC_Payment_Gateways')) {
                    $paymentgateway = new WC_Payment_Gateways();
                    foreach ($paymentgateway->payment_gateways()as $gateway) {
                        self::rs_script_for_validation($gateway);
                    }
                }
            }
        }

        public static function rs_script_for_validation($gateway) {
            ?>
            <script type="text/javascript">
                jQuery(function () {
                    jQuery('body').on('blur', '#rs_reward_payment_gateways_<?php echo $gateway->id; ?>', function () {
                        jQuery('.wc_error_tip').fadeOut('100', function () {
                            jQuery(this).remove();
                        });
                        return this;
                    });
                    jQuery('body').on('keyup change', '#rs_reward_payment_gateways_<?php echo $gateway->id; ?>', function () {
                        var value = jQuery(this).val();
                        console.log(woocommerce_admin.i18n_mon_decimal_error);
                        var regex = new RegExp("[^\+0-9\%.\\" + woocommerce_admin.mon_decimal_point + "]+", "gi");
                        var newvalue = value.replace(regex, '');
                        if (value !== newvalue) {
                            jQuery(this).val(newvalue);
                            if (jQuery(this).parent().find('.wc_error_tip').size() == 0) {
                                var offset = jQuery(this).position();
                                jQuery(this).after('<div class="wc_error_tip">' + woocommerce_admin.i18n_mon_decimal_error + " Negative Values are not allowed" + '</div>');
                                jQuery('.wc_error_tip')
                                        .css('left', offset.left + jQuery(this).width() - (jQuery(this).width() / 2) - (jQuery('.wc_error_tip').width() / 2))
                                        .css('top', offset.top + jQuery(this).height())
                                        .fadeIn('100');
                            }
                        }
                        return this;
                    });
                    jQuery("body").click(function () {
                        jQuery('.wc_error_tip').fadeOut('100', function () {
                            jQuery(this).remove();
                        });

                    });
                    if (jQuery('#rs_reward_type_for_payment_gateways_<?php echo $gateway->id; ?>').val() == '1') {
                        jQuery('#rs_reward_payment_gateways_<?php echo $gateway->id; ?>').closest('tr').show();
                        jQuery('#rs_reward_points_for_payment_gateways_in_percent_<?php echo $gateway->id; ?>').closest('tr').hide();
                    } else {
                        jQuery('#rs_reward_payment_gateways_<?php echo $gateway->id; ?>').closest('tr').hide();
                        jQuery('#rs_reward_points_for_payment_gateways_in_percent_<?php echo $gateway->id; ?>').closest('tr').show();
                    }

                    jQuery('#rs_reward_type_for_payment_gateways_<?php echo $gateway->id; ?>').change(function () {
                        if (jQuery('#rs_reward_type_for_payment_gateways_<?php echo $gateway->id; ?>').val() == '1') {
                            jQuery('#rs_reward_payment_gateways_<?php echo $gateway->id; ?>').closest('tr').show();
                            jQuery('#rs_reward_points_for_payment_gateways_in_percent_<?php echo $gateway->id; ?>').closest('tr').hide();
                        } else {
                            jQuery('#rs_reward_payment_gateways_<?php echo $gateway->id; ?>').closest('tr').hide();
                            jQuery('#rs_reward_points_for_payment_gateways_in_percent_<?php echo $gateway->id; ?>').closest('tr').show();
                        }
                    });
                });
            </script>
            <?php
        }

        public static function on_post_publish($ID, $post) {
            // A function to perform actions when a post is published.
            $user_ID = get_current_user_id();
            $post_id = $ID;
            //$title = $post_id->post_title;
            $earned_points = get_option('rs_reward_post');
            $date = rs_function_to_get_expiry_date_in_unixtimestamp();
            $enableoptforpost = get_option('rs_reward_for_Creating_Post');
            $meta_value = get_post_meta($post_id, 'rewardpointsforblogpost', true);
            if ($enableoptforpost == 'yes') {
                $retrived_value = get_option('fp_rs_list_blog_posts');
                if (!in_array($ID, $retrived_value)) {
                    if ($earned_points != "") {
                        if ($meta_value != "yes") {
                            RSPointExpiry::insert_earning_points($user_ID, $earned_points, '', $date, 'RPFP', '', '', '', '');
                            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_ID);
                            RSPointExpiry::record_the_points($user_ID, $earned_points, '', $date, 'RPFP', '', '', '', $post_id, '', '', '', $totalpoints, '', '');
                            update_post_meta($post_id, 'rewardpointsforblogpost', 'yes');
                        }
                    }
                    $previous_value = get_option('fp_rs_list_blog_posts');
                    if ($previous_value != "") {
                        $current_id = $ID;
                        $combined_id = array_merge($previous_value, $current_id);
                        update_option('fp_rs_list_blog_posts', $ID);
                    } else {
                        update_option('fp_rs_list_blog_posts', $ID);
                    }
                }
            }
            $current_id[] = $ID;
            update_option('fp_rs_list_blog_posts', $current_id);
        }

        public static function rs_function_to_reset_action_tab() {
            $settings = RSRewardPointsForAction::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

    }

    RSRewardPointsForAction::init();
}