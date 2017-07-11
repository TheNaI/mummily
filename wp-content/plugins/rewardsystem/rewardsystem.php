<?php

/*
 * Plugin Name: SUMO Reward Points
 * Plugin URI:
 * Description: SUMO Reward Points is a WooCommerce Loyalty Reward System using which you can Reward your Customers using Reward Points for Purchasing Products, Writing Reviews, Sign up on your site etc
 * Version:17.7
 * Author: Fantastic Plugins
 * Author URI:http://fantasticplugins.com
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FPRewardSystem')) {

    class FPRewardSystem {
        /*
         * SUMO Reward System Version
         *  @var $string 
         */

        public $version = '16.3';

        /*
         * @var FPRewardSystem
         */
        protected static $_instance = null;

        /*
         * Load Reward System Class in Single Instance
         */

        public static function instance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /* Cloning has been forbidden */

        public function __clone() {
            _doing_it_wrong(__FUNCTION__, __('You are not allowed to perform this action!!!', 'rewardsystem'), $this->version);
        }

        /*
         * Unserialize the class data has been forbidden
         */

        public function __wakeup() {
            _doing_it_wrong(__FUNCTION__, __('You are not allowed to perform this action!!!', 'rewardsystem'), $this->version);
        }

        /*
         * Reward System Constructor
         */

        public function __construct() {
            /* Include once will help to avoid fatal error by load the files when you call init hook */
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            $this->header_already_sent_problem();
            /* Display warning if woocommerce is not active */
            add_action('init', array($this, 'woocommerce_dependency_warning_message'));
            if (!$this->if_woocommerce_is_active()) {
                return;
            }
            $this->rs_translate_file();
            $this->list_of_constants();
            include_once('includes/class-rs-install.php');
            add_action('init', array($this, 'include_files'));
            add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_script'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_script'));
            $this->init_hooks();
            /* Load WooCommerce Enqueue Script to Load the Script and Styles by filtering the WooCommerce Screen IDS */
            if (isset($_GET['page'])) {
                if (($_GET['page'] == 'rewardsystem_callback')) {
                    add_filter('woocommerce_screen_ids', array($this, 'reward_system_load_default_enqueues'), 9, 1);
                }
            }
            $this->rewardgateway();
        }

        /*
         * Function to Prevent Header Error that says You have already sent the header.
         */

        public function header_already_sent_problem() {
            ob_start();
        }

        /*
         * Function to check wheather Woocommerce is active or not
         */

        public function if_woocommerce_is_active() {

            if (is_multisite() && !is_plugin_active_for_network('woocommerce/woocommerce.php') && !is_plugin_active('woocommerce/woocommerce.php')) {
                return false;
            } else if (!is_plugin_active('woocommerce/woocommerce.php')) {
                return false;
            }
            return true;
        }

        /*         * Call this function in the 'init' hook.* */

        public function woocommerce_dependency_warning_message() {

            if (!$this->if_woocommerce_is_active() && is_admin()) {
                $error = "<div class='error'><p> SUMO Reward Points requires WooCommerce Plugin should be Active !!! </p></div>";
                echo $error;
            }
            return;
        }

        /*
         * @var $list_of_constants in a array
         */

        public function list_of_constants() {
            $list_of_constants = apply_filters('fprewardsystem_constants', array(
                'REWARDSYSTEM_PLUGIN_BASENAME' => plugin_basename(__FILE__),
                'REWARDSYSTEM_PLUGIN_FILE' => __FILE__,
                'REWARDSYSTEM_PLUGIN_DIR_URL' => plugin_dir_url(__FILE__),
            ));
            if (is_array($list_of_constants) && !empty($list_of_constants)) {
                foreach ($list_of_constants as $constantname => $value) {
                    $this->define_constant($constantname, $value);
                }
            }
        }

        /*
         * Include Files 
         */

        public function include_files() {

            //welcome page include file
            include_once 'admin/welcome.php';
            //WP_List Table Files
            include_once('admin/wpliststable/class_wp_list_table_for_newgift_voucher.php');
            include_once('admin/wpliststable/class_wp_list_table_for_nominee_user_list.php');
            include_once('admin/wpliststable/class_wp_list_table_for_users.php');
            include_once('admin/wpliststable/class_wp_list_table_master_log.php');
            include_once('admin/wpliststable/class_wp_list_table_referral_table.php');
            include_once('admin/wpliststable/class_wp_list_table_view_log_user.php');
            include_once('admin/wpliststable/class_wp_list_table_view_referral_table.php');
            include_once('includes/frontend/compatibility/rewardpoints_wc2point6.php');
            include_once('includes/class_wpml_support.php');
            if (is_admin()) {
                $this->include_admin_files();
            }
            if (!is_admin() || defined('DOING_AJAX')) {
                $this->include_frontend_files();
            }
            include('includes/frontend/rs_referral_log_count.php');
            include('includes/frontend/rs_function_for_member_level.php');
            include('includes/frontend/rs_function_to_apply_coupon.php');
            include('includes/frontend/rs_function_for_order_tab.php');
            include('includes/frontend/rs_function_for_sms_tab.php');
            include_once('includes/frontend/rs_function_for_saving_meta_values.php');
            include_once('includes/rewardsystem-core-functions.php');
            include_once('includes/frontend/rs_jquery.php');
            include_once('includes/class-reward-points-orders.php');
            include_once('includes/main_functions_for_point_expiry.php');
            include('includes/frontend/rs_function_for_coupon_reward_point_tab.php');
        }

        /*
         * Include Admin Files
         */

        public function include_admin_files() {
            include_once('admin/class-reward-system-tab-management.php');
            include_once('admin/tabs/class-general-setting.php');
            include_once('admin/tabs/class-reward-points-for-action-setting.php');
            include_once('admin/tabs/class-member-level-setting.php');
            include_once('admin/tabs/class-user-reward-points-tab.php');
            include_once('admin/tabs/class-add-remove-points-tab.php');
            include_once('admin/tabs/class-message-setting.php');
            include_once('admin/tabs/class-shop-page-setting.php');
            include_once('admin/tabs/class-single-product-page-setting.php');
            include_once('admin/tabs/class-cart-setting.php');
            include_once('admin/tabs/class-checkout-setting.php');
            include_once('admin/tabs/class-myaccount-setting.php');
            include_once('admin/tabs/class-masterlog-tab.php');
            include_once('admin/tabs/class-referral-reward-log-tab.php');
            include_once('admin/tabs/class-bulk-update-tab.php');
            include_once('admin/tabs/class-order-status-setting.php');
            include_once('admin/tabs/class-refer-friend-setting.php');
            include_once('admin/tabs/class-social-rewards-setting.php');
            include_once('admin/tabs/class-email-template-tab.php');
            include_once('admin/tabs/class-mail-setting.php');
            include_once('admin/tabs/class-sms-setting.php');
            include_once('admin/tabs/class-order-setting.php');
            include_once('admin/tabs/class-gift-voucher-tab.php');
            include_once('admin/tabs/class-import-export-tab.php');
            include_once('admin/tabs/class-form-for-cash-back-setting.php');
            include_once('admin/tabs/class-request-for-cash-back-tab.php');
            include_once('admin/tabs/class-coupon-reward-points-tab.php');
            include_once('admin/tabs/class-manual-referral-link-tab.php');
            include_once('admin/tabs/class-reports-in-csv-tab.php');
            include_once('admin/tabs/class-reset-setting.php');
            include_once('admin/tabs/class-troubleshoot-setting.php');
            include_once('admin/tabs/class-localization-setting.php');
            include_once('admin/tabs/class-form-for-send-points-setting.php');
            include_once('admin/tabs/class-request-for-send-points-tab.php');
            include_once('admin/tabs/class-nominee-setting.php');
            include_once('admin/tabs/class-point-url-setting.php');
            include_once('admin/tabs/class-shortcode-tab.php');
            include_once('admin/tabs/class-advanced-setting.php');
            include_once('admin/tabs/class-support-tab.php');
            //product/product category settings
            include_once('admin/settings/class-simple-product-settings.php');
            include_once('admin/settings/class-variable-product-settings.php');
            include_once('admin/settings/class-category-product-settings.php');
            include_once('admin/settings/class-buying-reward-points.php');
            //admin related js files
            include_once('admin/class-admin-enqueues.php');
            include('admin/wc_class_encashing_wplist.php');
            include('admin/wc_class_send_point_wplist.php');
        }

        /*
         * Include Admin Files
         */

        public function include_frontend_files() {
            include('includes/frontend/rs_function_for_general_tab.php');
            include('includes/frontend/rs_function_for_shoppage_tab.php');
            include('includes/frontend/rs_function_for_singleproductpage_tab.php');
            include('includes/frontend/rs_function_for_cart_tab.php');
            include('includes/frontend/rs_function_for_checkout.php');
            include('includes/frontend/rs_function_for_myaccount_tab.php');
            include('includes/frontend/rs_function_for_refer_a_friend.php');
            include('includes/frontend/rs_function_for_social_reward_tab.php');
            include('includes/frontend/rs_function_for_email_template.php');
            include('includes/frontend/rs_function_for_mail_tab.php');
            include('includes/frontend/rs_function_for_gift_voucher_tab.php');
            include('includes/frontend/rs_function_for_new_gift_voucher.php');
            include('includes/frontend/rs_function_for_form_for_cash_back.php');
            include('includes/frontend/rs_function_for_manual_referral_link_tab.php');
            include('includes/frontend/rs_function_for_troubleshoot_tab.php');
            include('includes/frontend/rs_free_product_main_function.php');
            include('includes/frontend/rs_function_for_send_points.php');
            include('includes/frontend/rs_function_for_nominee.php');
            include('includes/frontend/rs_function_for_pointurl_tab.php');
            include('includes/frontend/rs_wc_booking_compatabilty.php');
            include('includes/frontend/rs_price_rule_checker_for_variant.php');
            include('includes/frontend/rs_function_to_add_registration_points.php');
            include('includes/class-simple-product.php');
            include('includes/class-variable-product.php');
        }

        public function rewardgateway() {

            include('admin/class_rewardgateway.php');

            add_action('plugins_loaded', 'init_reward_gateway_class');
        }

        public function init_hooks() {
            register_activation_hook(__FILE__, array('RSInstall', 'install'));
            register_activation_hook(__FILE__, array('FPRewardSystem', 'sumo_reward_points_welcome_screen_activate'));
        }

        // welcome page function
        public static function sumo_reward_points_welcome_screen_activate() {
            set_transient('_welcome_screen_activation_redirect_reward_points', true, 30);
        }

        /*
         * Translate File
         * 
         */

        public function rs_translate_file() {
            load_plugin_textdomain('rewardsystem', false, dirname(plugin_basename(__FILE__)) . '/languages');
        }

        /*
         * Define Constant
         * @param string $name
         * @param string|bool $value
         */

        protected function define_constant($name, $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }

        /*
         * Load the Default JAVASCRIPT and CSS
         */

        public function reward_system_load_default_enqueues() {
            global $my_admin_page;
            $newscreenids = get_current_screen();
            if (isset($_GET['page'])) {
                if (($_GET['page'] == 'rewardsystem_callback')) {
                    $array[] = $newscreenids->id;
                    return $array;
                } else {
                    $array[] = '';
                    return $array;
                }
            }
        }

        public static function check_banning_type($userid) {
            $earning = get_option('rs_enable_banning_users_earning_points');
            $redeeming = get_option('rs_enable_banning_users_redeeming_points');
            $banned_user_list = get_option('rs_banned_users_list');
            if (is_array($banned_user_list)) {
                $banned_user_list = $banned_user_list;
            } else {
                $banned_user_list = explode(',', $banned_user_list);
            }

            if (in_array($userid, (array) $banned_user_list)) {
                if ($earning == 'no' && $redeeming == 'no') {
                    return "no_banning";
                }
                if ($earning == 'no' && $redeeming == 'yes') {
                    return 'redeemingonly';
                }
                if ($earning == 'yes' && $redeeming == 'no') {
                    return 'earningonly';
                }
                if ($earning == 'yes' && $redeeming == 'yes') {
                    return 'both';
                }
            } else {
                $getarrayofuserdata = get_userdata(get_current_user_id());
                $banninguserrole = get_option('rs_banning_user_role');
                if (in_array(isset($getarrayofuserdata->roles[0]) ? $getarrayofuserdata->roles[0] : '0', (array) $banninguserrole)) {
                    if ($earning == 'no' && $redeeming == 'no') {
                        return "no_banning";
                    }
                    if ($earning == 'no' && $redeeming == 'yes') {
                        return 'redeemingonly';
                    }
                    if ($earning == 'yes' && $redeeming == 'no') {
                        return 'earningonly';
                    }
                    if ($earning == 'yes' && $redeeming == 'yes') {
                        return 'both';
                    }
                }
            }
        }

        // welcome page register css file
        public function admin_enqueue_script() {
            if (isset($_GET['page']) && $_GET['page'] == 'sumo-reward-points-welcome-page') {
                wp_register_style('wp_reward_welcome_page', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/css/rewardpoints_welcome_page_style.css");
                wp_enqueue_style('wp_reward_welcome_page');
            }
        }

        public function wp_enqueue_script() {
            wp_register_script('wp_reward_footable', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/footable.js");
            wp_register_script('wp_reward_footable_sort', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/footable.sort.js");
            wp_register_script('wp_reward_footable_paging', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/footable.paginate.js");
            wp_register_script('wp_reward_footable_filter', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/footable.filter.js");
            wp_register_style('wp_reward_footable_css', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/css/footable.core.css");
            wp_register_style('wp_reward_bootstrap_css', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/css/bootstrap.css");
            wp_register_style('wp_reward_facebook', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/css/style.css");
            wp_enqueue_script('jquery');
            wp_enqueue_script('wp_reward_facebook');
            wp_enqueue_script('wp_reward_footable');
            wp_enqueue_script('wp_reward_footable_sort');
            wp_enqueue_script('wp_reward_footable_paging');
            wp_enqueue_script('wp_reward_footable_filter');
            wp_enqueue_style('wp_reward_footable_css');
            wp_enqueue_style('wp_reward_bootstrap_css');
            $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
            wp_register_script('wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array('jquery', 'select2'), WC_VERSION);
            wp_localize_script('wc-enhanced-select', 'wc_enhanced_select_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'search_customers_nonce' => wp_create_nonce('search-customers')
            ));
            wp_enqueue_script('wc-enhanced-select');
            wp_enqueue_script('select2');
            $assets_path = str_replace(array('http:', 'https:'), '', WC()->plugin_url()) . '/assets/';
            wp_enqueue_style('select2', $assets_path . 'css/select2.css');
        }

    }

    FPRewardSystem::instance();
}