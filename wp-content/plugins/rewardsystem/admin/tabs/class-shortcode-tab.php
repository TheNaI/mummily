<?php

/*
 * Advanced Tab
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSShortcode')) {

    class RSShortcode {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_shortcode', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_shortcode', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_shortcode'] = __('Shortcodes', 'rewardsystem');
            return $setting_tabs;
        }

        public static function reward_system_admin_fields() {
            ?>
            <style type="text/css">
                p.sumo_reward_points{
                    display: none;
                }
                #mainforms{
                    display: none;
                }
            </style>
            <?php
            return apply_filters('woocommerce_rewardsystem_shortcode_settings', array(
                array(
                    'name' => __('Shortcodes', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_shortcode',
                ),
                array(
                    'type' => 'title',
                    'desc' => '<h3>User Reward Points Info Shortcodes</h3><br><br>'
                    . '<b>[rs_my_reward_points]</b> - Use this Shortcode to display Reward Points of Current User<br><br>'
                    . '<b>[rs_user_total_earned_points]</b> - Use this Shortcode to display Total Points Earned by a User<br><br>'
                    . '<b>[rs_user_total_points_in_value]</b> - Use this Shortcode to display Total Points in Currency Value<br><br>'
                    . '<b>[rs_user_total_redeemed_points]</b> - Use this Shortcode to display Total Points Redeemed by a User<br><br>'
                    . '<b>[rs_user_total_expired_points]</b> - Use this Shortcode to display Total Points Expired for a User<br><br>'
                    . '<b>[rs_my_rewards_log]</b> - Use this Shortcode to display Log of Reward Points <br><br>'
                    . '<h3>Referral System Shortcodes</h3><br><br>'
                    . '<b>[rs_generate_referral referralbutton="show" referraltable="show"]</b> - Use this Shortcode to display Referral Link Generation and its Table.Shortcode Parameters are referralbutton and referraltable, make it as Show/Hide.<br><br>'
                    . '<b>[rs_generate_static_referral]</b> - Use this Shortcode to display Static URL Link<br><br>'
                    . '<b>[rs_view_referral_table]</b> - Use this Shortcode to display Referral Table<br><br>'
                    . '<b>[rs_refer_a_friend]</b> - Use this Shortcode to display Refer a Friend Form on any Page/Post<br><br>'
                    . '<h3>Cashback Shortcodes</h3><br><br>'
                    . '<b>[rs_my_cashback_log]</b> - Use this Shortcode to display My Cashback Table<br><br>'
                    . '<b>[rsencashform]</b> - Use this Shortcode to display Cashback Form<br><br>'
                    . '<h3>Member Level Shortcodes</h3><br><br>'
                    . '<b>[rs_my_current_earning_level_name]</b> - Use this Shortcode to display current Member Level of the User<br><br>'
                    . '<b>[rs_next_earning_level_points]</b> - Use this Shortcode to display points needed to reach the next Member Level<br><br>'
                    . '<b>[rs_rank_based_current_reward_points]</b> - Use this Shortcode to display Current Earned Points of all User<br><br>'
                    . '<b>[rs_rank_based_total_earned_points]</b> - Use this Shortcode to display Total Earned Points of all User<br><br>'
                    . '<h3>Unsubscribe Email Shortcode</h3><br><br>'
                    . '<b>[rs_unsubscribe_email]</b> - Use this Shortcode to display Unsubscribe Email Checkbox<br><br>'
                    . '<h3>Gift Voucher Shortcode</h3><br><br>'
                    . '<b>[rs_redeem_vouchercode]</b> - Use this Shortcode to display Redeeming Voucher Field <br><br>'
                    . '<h3>Nominee Table Shortcode</h3><br><br>'
                    . '<b>[rs_nominee_table]</b> - Use this Shortcode to display Nominee Table<br><br>'
                    . '<h3>Send Points Shortocode</h3><br><br>'
                    . '<b>[rssendpoints]</b> - Use this Shortcode to display Send Points Form'
                ),                
                array('type' => 'sectionend', 'id' => 'rs_shortcode'),
                    )
            );
        }

        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields(RSShortcode::reward_system_admin_fields());
        }

        public static function reward_system_update_settings() {
            woocommerce_update_options(RSShortcode::reward_system_admin_fields());
        }

    }

    RSShortcode::init();
}