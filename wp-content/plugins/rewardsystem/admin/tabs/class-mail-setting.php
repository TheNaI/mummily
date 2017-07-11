<?php
/*
 * Mail Tab Setting
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSMail')) {

    class RSMail {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_mail', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_mail', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));
            
            add_action('admin_head', array(__CLASS__, 'add_header_script_for_js'));
            
            add_action('fp_action_to_reset_settings_rewardsystem_mail', array(__CLASS__, 'rs_function_to_reset_mail_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_mail'] = __('Email', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;
            $checkmandrill = 'wpmandrill/wpmandrill.php';
            if (function_exists('is_plugin_active')) {
                if (is_plugin_active($checkmandrill)) {
                    $arraymailoption = array(
                        '1' => 'mail()',
                        '2' => 'wp_mail()',
                        '3' => 'wpmandrill',
                    );
                } else {
                    $arraymailoption = array(
                        '1' => 'mail()',
                        '2' => 'wp_mail()',
                    );
                }
            } else {
                $arraymailoption = array(
                    '1' => 'mail()',
                    '2' => 'wp_mail()',
                );
            }
            return apply_filters('woocommerce_rewardsystem_mail_settings', array(
                array(
                    'name' => __('Email Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_mail_setting'
                ),
                array(
                    'name' => __('Select Email Function', 'rewardsystem'),
                    'id' => 'rs_select_mail_function',
                    'css' => 'min-width:150px;',
                    'std' => '2',
                    'default' => '2',
                    'newids' => 'rs_select_mail_function',
                    'type' => 'select',
                    'options' => $arraymailoption,
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_mail_settings'),
                array(
                    'name' => __('Email Cron Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_cron_settings',
                ),
                array(
                    'name' => __('Email Cron Time Type', 'rewardsystem'),
                    'id' => 'rs_mail_cron_type',
                    'css' => 'min-width:150px;',
                    'type' => 'select',
                    'newids' => 'rs_mail_cron_type',
                    'desc_tip' => true,
                    'options' => array('minutes' => 'Minutes', 'hours' => 'Hours', 'days' => 'Days'),
                    'std' => 'days',
                    'default' => 'days',
                ),
                array(
                    'name' => __('Email Cron Time', 'rewardsystem'),
                    'desc' => __('Please Enter time after which Email cron job should run', 'rewardsystem'),
                    'id' => 'rs_mail_cron_time',
                    'newids' => 'rs_mail_cron_time',
                    'css' => 'min-width:150px;',
                    'type' => 'text',
                    'desc_tip' => true,
                    'std' => '3',
                    'default' => '3',
                ),
                array('type' => 'sectionend', 'id' => '_rs_mail_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSMail::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSMail::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSMail::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function add_header_script_for_js() {
            global $woocommerce;
            if (isset($_GET['tab'])) {
                if ($_GET['tab'] == 'rewardsystem_mail') {
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                    <?php if ((float) $woocommerce->version > (float) ('2.2.0')) { ?>
                                var troubleemail = jQuery('#rs_select_mail_function').val();
                                if (troubleemail === '1') {
                                    jQuery('.prependedrc').remove();
                                    jQuery('#rs_select_mail_function').parent().append('<span class="prependedrc">For WooCommerce 2.3 or higher version mail() function will not load the WooCommerce default template. This option will be deprecated </span>');
                                } else {
                                    jQuery('.prependedrc').remove();
                                }
                                jQuery('#rs_select_mail_function').change(function () {
                                    if (jQuery(this).val() === '1') {
                                        jQuery('.prependedrc').remove();
                                        jQuery('#rs_select_mail_function').parent().append('<span class="prependedrc">For WooCommerce 2.3 or higher version mail() function will not load the WooCommerce default template. This option will be deprecated </span>');
                                    } else {
                                        jQuery('.prependedrc').remove();
                                    }
                                });

                    <?php } ?>
                        });
                    </script>
                    <?php
                }
            }
        }
        
        public static function rs_function_to_reset_mail_tab() {
            $settings = RSMail::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);            
        }

    }

    RSMail::init();
}