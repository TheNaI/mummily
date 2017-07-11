<?php
/*
 * Referral Reward Log Tab
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSReferralRewardLog')) {

    class RSReferralRewardLog {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_referrallog', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_referrallog', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               
            
            add_action('woocommerce_admin_field_display_referral_reward_log', array(__CLASS__, 'rs_list_referral_rewards_log'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_referrallog'] = __('Referral Reward Table', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {

            return apply_filters('woocommerce_rewardsystem_referral_reward_settings', array(
                array(
                    'name' => __('Referral Reward Table', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_referral_setting',
                ),
                array(
                    'type' => 'display_referral_reward_log',
                ),
                array('type' => 'sectionend', 'id' => 'rs_referral_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSReferralRewardLog::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSReferralRewardLog::reward_system_admin_fields());
        }

        public static function rs_list_referral_rewards_log() {
            ?>
            <style type="text/css">
                p.sumo_reward_points {
                    display:none;
                }
                #mainforms {
                    display:none;
                }
            </style>
            <?php
            if ((!isset($_GET['view']))) {
                $newwp_list_table_for_users = new WP_List_Table_for_Referral_Table();
                $newwp_list_table_for_users->prepare_items();
                $newwp_list_table_for_users->search_box('Search Users', 'search_id');
                $newwp_list_table_for_users->display();
            } else {
                $newwp_list_table_for_users = new WP_List_Table_for_View_Referral_Table();
                $newwp_list_table_for_users->prepare_items();
                $newwp_list_table_for_users->search_box('Search', 'search_id');
                $newwp_list_table_for_users->display();
                ?>
                <a href="<?php echo remove_query_arg(array('view'), get_permalink()); ?>">Go Back</a>
                <?php
            }
        }

    }

    RSReferralRewardLog::init();
}