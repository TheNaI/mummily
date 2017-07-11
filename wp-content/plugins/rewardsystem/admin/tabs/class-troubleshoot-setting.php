<?php
/*
 * Troubleshoot Setting Tab
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSTroubleshoot')) {

    class RSTroubleshoot {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_troubleshoot', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_troubleshoot', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('woocommerce_admin_field_rs_add_old_version_points', array(__CLASS__, 'add_old_points_for_all_user'));

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings')); // call the init function to update the default settings on page load
            
            add_action('fp_action_to_reset_settings_rewardsystem_troubleshoot', array(__CLASS__, 'rs_function_to_reset_troubleshoot_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_troubleshoot'] = __('Troubleshoot', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;

            return apply_filters('woocommerce_rewardsystem_troubleshoot_settings', array(
                array(
                    'name' => __('Troubleshoot Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_troubleshoot_setting'
                ),
                array(
                    'name' => __('Troubleshoot Option for Cart Page', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_point_troubleshoot_cart_page'
                ),
                array(
                    'name' => __('Troubleshoot Before Cart Hook', 'rewardsystem'),
                    'desc' => __('Here you can select the different hooks in Cart Page', 'rewardsystem'),
                    'id' => 'rs_reward_point_troubleshoot_before_cart',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'options' => array('1' => 'woocommerce_before_cart', '2' => 'woocommerce_before_cart_table'),
                    'newids' => 'rs_reward_point_troubleshoot_before_cart',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Redeeming Field display Position in Cart Page', 'rewardsystem'),
                    'desc' => __('Here you can select the Redeem Point Position Options for Cart Page', 'rewardsystem'),
                    'id' => 'rs_reward_point_troubleshoot_after_cart',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'options' => array('1' => 'woocommerce_after_cart_table', '2' => 'woocommerce_cart_coupon'),
                    'newids' => 'rs_reward_point_troubleshoot_after_cart',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Enqueue Tipsy jQuery Library in SUMO Reward Points', 'rewardsystem'),
                    'desc' => __('Here you can select to change the load tipsy option if some jQuery conflict occurs', 'rewardsystem'),
                    'id' => 'rs_reward_point_enable_tipsy_social_rewards',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'options' => array('1' => 'Enable ', '2' => 'Disable'),
                    'newids' => 'rs_reward_point_enable_tipsy_social_rewards',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Enqueue jQuery UI Library in SUMO Reward Points', 'rewardsystem'),
                    'desc' => __('Here you can select whether to enqueue the jQuery UI library available within SUMO Reward Points', 'rewardsystem'),
                    'id' => 'rs_reward_point_enable_jquery',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'options' => array('1' => 'Enqueue ', '2' => 'Do not Enqueue'),
                    'newids' => 'rs_reward_point_enable_jquery',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_point_troubleshoot_cart_page'),
                array(
                    'name' => __('Experimental Features', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_point_table'
                ),
                array(
                    'type' => 'rs_add_old_version_points',
                ),
                array(
                    'name' => __('SUMO Reward Points Payment Gateway for Manual Order', 'rewardsystem'),
                    'desc' => __('Enable or Disable SUMO Reward Points Payment Gateway for Manual Order', 'rewardsystem'),
                    'id' => 'rs_gateway_for_manual_order',
                    'newids' => 'rs_gateway_for_manual_order',
                    'std' => '2',
                    'default' => '2',
                    'css' => 'min-width:150px;',
                    'type' => 'select',
                    'desc_tip' => false,
                    'options' => array(
                        '1' => 'Enable',
                        '2' => 'Disable'
                    ),
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_point_table'),
                array(
                    'name' => __('Plugin Scripts Troubleshoot Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_point_troubleshoot_all_page'
                ),
                array(
                    'name' => __('Load SUMO Reward Points Script/Styles in', 'rewardsystem'),
                    'desc' => __('For Footer of the Site Option is experimental why because if your theme doesn\'t contain wp_footer hook then it won\'t work', 'rewardsystem'),
                    'id' => 'rs_load_script_styles',
                    'newids' => 'rs_load_script_styles',
                    'css' => 'min-width:150px;',
                    'type' => 'select',
                    'desc_tip' => false,
                    'options' => array(
                        'wp_head' => 'Header of the Site',
                        'wp_footer' => 'Footer of the Site (Experimental)'
                    ),
                    'std' => 'wp_head',
                    'default' => 'wp_head',
                ),
                array(
                    'name' => __('Memory Exhaust Issues', 'rewardsystem'),
                    'desc' => __('Enable or Disable Memory Exhaust Troubleshoot', 'rewardsystem'),
                    'id' => 'rs_load_memory_unit',
                    'newids' => 'rs_load_memory_unit',
                    'css' => 'min-width:150px;',
                    'type' => 'select',
                    'desc_tip' => false,
                    'options' => array(
                        '1' => 'Enable',
                        '2' => 'Disable'
                    ),
                    'std' => '2',
                    'default' => '2',
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_point_troubleshoot_all_page'),
                array('type' => 'sectionend', 'id' => '_rs_troubleshoot_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSTroubleshoot::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSTroubleshoot::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSTroubleshoot::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function add_old_points_for_all_user() {
            ?>
            <tr valign="top">
                <th>
                    <label for="rs_add_old_points_label" style="font-size:14px;font-weight:600;"><?php _e('Add the Old Available Points to User(s)', 'rewardsystem'); ?></label>
                </th>
                <td>
                    <input type="button" value="<?php _e('Add Old Points', 'rewardsystem'); ?>"  id="rs_add_old_points" name="rs_add_old_points" /><b><span style="font-size: 18px;">(Experimental)</span></b>
                    <img class="gif_rs_sumo_reward_button" src="<?php echo WP_PLUGIN_URL; ?>/rewardsystem/admin/images/update.gif" style="width:32px;height:32px;position:absolute"/>                         
                </td>
            </tr>
            <?php
        }
        
        public static function rs_function_to_reset_troubleshoot_tab() {
            $settings = RSTroubleshoot::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);            
        }

    }

    RSTroubleshoot::init();
}