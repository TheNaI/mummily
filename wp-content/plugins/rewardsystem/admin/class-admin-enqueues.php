<?php
/*
 * Admin Side Enqueues
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSAdminEnqueues')) {

    class RSAdminEnqueues {

        public static function init() {
            add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_script'));

            add_action('admin_head', array(__CLASS__, 'import_user_points_to_reward_system'));

            add_action('wp_ajax_get_user_list_of_ids', array(__CLASS__, 'perform_ajax_scenario_getting_list_of_user_ids'));

            add_action('wp_ajax_user_points_split_option', array(__CLASS__, 'perform_ajax_splitted_ids_for_user_ids'));
        }

        public static function admin_enqueue_script() {
            $deps = array();
            $verison = '1';
            $plugin_folder = get_plugins('/' . 'rewardsystem');
            $plugin_file = 'rewardsystem.php';
            if (isset($plugin_folder[$plugin_file]['Version'])) {
                $verison = $plugin_folder[$plugin_file]['Version'];
            }
            wp_register_script('rewardsystem_admin', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/admin.js", $deps, $verison);
            $localize_script = array(
                'reset_confirm_msg' => __('Are you sure want to Reset?', 'rewardsystem'),
                'field_ids' => '#_rewardsystem_assign_buying_points[type=text],#_rewardsystempoints[type=text],#_rewardsystempercent[type=text],'
                . '#_referralrewardsystempoints[type=text],#_referralrewardsystempercent[type=text],#_socialrewardsystempoints_facebook[type=text],'
                . '#_socialrewardsystempercent_facebook[type=text],#_socialrewardsystempoints_twitter[type=text],'
                . '#_socialrewardsystempercent_twitter[type=text],#_socialrewardsystempoints_google[type=text],#_socialrewardsystempercent_google[type=text],'
                . '#_socialrewardsystempoints_vk[type=text],#_socialrewardsystempercent_vk[type=text],#rs_max_earning_points_for_user[type=text],'
                . '#rs_earn_point[type=text],#rs_earn_point_value[type=text],#rs_redeem_point[type=text],#rs_redeem_point_value[type=text],'
                . '#rs_fixed_max_redeem_discount[type=text],#rs_global_reward_points[type=text],#rs_global_referral_reward_point[type=text],'
                . '#rs_global_reward_percent[type=text],#rs_global_referral_reward_percent[type=text],#rs_referral_cookies_expiry_in_days[type=text],'
                . '#rs_referral_cookies_expiry_in_min[type=text],#rs_referral_cookies_expiry_in_hours[type=text],'
                . '#_rs_select_referral_points_referee_time_content[type=text],#rs_percent_max_redeem_discount[type=text],#rs_point_to_be_expire[type=number],'
                . '#rs_fixed_max_earn_points[type=text],#rs_percent_max_earn_points[type=text],#rs_reward_signup[type=text],'
                . '#rs_reward_product_review[type=text],#rs_referral_reward_signup[type=text],#rs_reward_points_for_login[type=text],'
                . '#rs_reward_user_role_administrator[type=text],#rs_reward_user_role_editor[type=text],#rs_reward_user_role_author[type=text],'
                . '#rs_reward_user_role_contributor[type=text],#rs_reward_user_role_subscriber[type=text],#rs_reward_user_role_customer[type=text],'
                . '#rs_reward_user_role_shop_manager[type=text],#rs_reward_addremove_points[type=text],#rs_percentage_cart_total_redeem[type=text],'
                . '#rs_first_time_minimum_user_points[type=text],#rs_minimum_user_points_to_redeem[type=text],#rs_minimum_redeeming_points[type=text],'
                . '#rs_maximum_redeeming_points[type=text],#rs_minimum_cart_total_points[type=text],#rs_percentage_cart_total_redeem_checkout[type=text],'
                . '#rs_local_reward_points[type=text],#rs_local_reward_percent[type=text],#rs_local_referral_reward_point[type=text],'
                . '#rs_local_referral_reward_percent[type=text],#rs_local_reward_points_facebook[type=text],#rs_local_reward_percent_facebook[type=text],'
                . '#rs_local_reward_points_twitter[type=text],#rs_local_reward_percent_twitter[type=text],#rs_local_reward_points_google[type=text],'
                . '#rs_local_reward_percent_google[type=text],#rs_local_reward_points_vk[type=text],#rs_local_reward_percent_vk[type=text],'
                . '#rs_global_social_facebook_reward_points[type=text],#rs_global_social_facebook_reward_percent[type=text],'
                . '#rs_global_social_twitter_reward_points[type=text],#rs_global_social_twitter_reward_percent[type=text],'
                . '#rs_global_social_google_reward_points[type=text],#rs_global_social_google_reward_percent[type=text],'
                . '#rs_global_social_vk_reward_points[type=text],#rs_global_social_vk_reward_percent[type=text],'
                . '#rs_global_social_facebook_reward_points_individual[type=text],#rs_global_social_facebook_reward_percent_individual[type=text],'
                . '#rs_global_social_twitter_reward_points_individual[type=text],#rs_global_social_twitter_reward_percent_individual[type=text],'
                . '#rs_global_social_google_reward_points_individual[type=text],#rs_global_social_google_reward_percent_individual[type=text],'
                . '#rs_global_social_vk_reward_points_individual[type=text],#rs_global_social_vk_reward_percent_individual[type=text],'
                . '#earningpoints[type=text],#rs_minimum_edit_userpoints[type=text],#rs_minimum_userpoints[type=text],#redeemingpoints[type=text],'
                . '#rs_mail_cron_time[type=text],#rs_point_voucher_reward_points[type=text],#rs_point_bulk_voucher_points[type=text],'
                . '#rs_minimum_points_encashing_request[type=text],#rs_maximum_points_encashing_request[type=text],#_reward_points[type=text],'
                . '#_reward_percent[type=text],#_referral_reward_points[type=text],#_referral_reward_percent[type=text],#rs_category_points[type=text],'
                . '#rs_category_percent[type=text],#referral_rs_category_points[type=text],#referral_rs_category_percent[type=text],'
                . '#social_facebook_rs_category_points[type=text],#social_facebook_rs_category_percent[type=text],'
                . '#social_twitter_rs_category_points[type=text],#social_twitter_rs_category_percent[type=text],#social_google_rs_category_points[type=text],'
                . '#social_google_rs_category_percent[type=text],#social_vk_rs_category_points[type=text],#social_vk_rs_category_percent[type=text]',
            );
            wp_localize_script('rewardsystem_admin', 'rewardsystem', $localize_script);
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
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('rewardsystem_admin');
            if (function_exists('wp_enqueue_media')) {
                wp_enqueue_media();
            } else {
                wp_enqueue_style('thickbox');
                wp_enqueue_script('media-upload');
                wp_enqueue_script('thickbox');
            }            
            wp_register_script('wp_reward_jquery_ui', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/jquery-ui.js", array('jquery'));
            wp_register_script('wp_jscolor_rewards', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/jscolor/jscolor.js", array('jquery'));
            wp_register_style('wp_reward_jquery_ui_css', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/css/jquery-ui.css");
            wp_register_script('wp_reward_jquery_ui', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/jquery-ui.js", array('jquery'));

            if (isset($_GET['tab'])) {
                if ($_GET['tab'] == 'rewardsystem_reports_in_csv') {

                    wp_enqueue_script('wp_reward_jquery_ui');
                    wp_enqueue_style('wp_reward_jquery_ui_css');
                }
                if ($_GET['tab'] == 'rewardsystem_update') {

                    wp_enqueue_script('wp_reward_jquery_ui');
                    wp_enqueue_style('wp_reward_jquery_ui_css');
                }
                if ($_GET['tab'] == 'rewardsystem_socialrewards') {
                     wp_enqueue_script('wp_jscolor_rewards');
                 }
                do_action_ref_array('enqueuescriptforadmin', array(&$enqueuescript));
            }
        }

        // Import User Reward Points from Old Version to Latest Version
        public static function import_user_points_to_reward_system() {
            wp_enqueue_script('jquery');
            $security = rs_function_to_create_security();
            $isadmin = is_admin() ? 'yes' : 'no';
            ?>
            <script type="text/javascript">
                jQuery(function () {
                    jQuery('.gif_rs_sumo_reward_button').css('display', 'none');
                });
                jQuery(document).ready(function () {
                    jQuery('#rs_add_old_points').click(function () {
                        if (confirm("Are you sure you want to Add the Existing points?")) {
                            jQuery('.gif_rs_sumo_reward_button').css('display', 'inline-block');
                            var dataparam = ({
                                action: 'get_user_list_of_ids'
                            });
                            function getData(id) {
                                console.log(id);
                                return jQuery.ajax({
                                    type: 'POST',
                                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                    data: ({
                                        action: 'user_points_split_option',
                                        ids: id,
                                        secure: "<?php echo $security; ?>",
                                        state: "<?php echo $isadmin; ?>"
                                    }),
                                    success: function (response) {
                                        console.log(response);
                                    },
                                    dataType: 'json',
                                    async: false
                                });
                            }
                            jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                    function (response) {
                                        console.log(response);
                                        if (response !== 'success') {
                                            var j = 1;
                                            var i, j, temparray, chunk = 10;
                                            for (i = 0, j = response.length; i < j; i += chunk) {
                                                temparray = response.slice(i, i + chunk);
                                                getData(temparray);
                                            }
                                            jQuery.when(getData()).done(function (a1) {
                                                console.log('Ajax Done Successfully');
                                                location.reload();
                                            });
                                        } else {
                                            var newresponse = response.replace(/\s/g, '');
                                            if (newresponse === 'success') {
                                                //jQuery('.submit .button-primary').trigger('click');
                                            }
                                        }
                                    }, 'json');
                            return false;
                        }
                    });
                });

            </script>
            <?php
        }

        //Perform Ajax Scenario for Updating User Points
        public static function perform_ajax_scenario_getting_list_of_user_ids() {
            $args = array(
                'fields' => 'ID',
                'meta_key' => '_my_reward_points',
                'meta_value' => '',
                'meta_compare' => '!='
            );
            $get_users = get_users($args);

            echo json_encode($get_users);

            exit();
        }

        // Perform Splitted User IDs in Reward System Function
        public static function perform_ajax_splitted_ids_for_user_ids() {
            $verify_security = isset($_POST['security']) ? self::rs_function_to_verify_secure($_POST['security']) : false;
            if (isset($_POST['ids']) && $verify_security && isset($_POST['state']) && $_POST['state'] == 'yes') {
                foreach ($_POST['ids'] as $eachid) {
                    self::insert_user_points_in_database($eachid);
                }
            }

            exit();
        }

        // Insert User Points in Database

        public static function insert_user_points_in_database($user_id) {
            global $wpdb;
            $user_points = get_user_meta($user_id, '_my_reward_points', true);
            $table_name = $wpdb->prefix . "rspointexpiry";
            $currentdate = time();
            $query = $wpdb->get_row("SELECT * FROM $table_name WHERE userid = $user_id and expirydate = 999999999999", ARRAY_A);
            if (!empty($query)) {
                $id = $query['id'];
                $oldearnedpoints = $query['earnedpoints'];
                $oldearnedpoints = $oldearnedpoints + $user_points;
                $wpdb->update($table_name, array('earnedpoints' => $oldearnedpoints), array('id' => $id));
            } else {
                $wpdb->insert($table_name, array(
                    'earnedpoints' => $user_points,
                    'usedpoints' => '',
                    'expiredpoints' => '0',
                    'userid' => $user_id,
                    'earneddate' => $currentdate,
                    'expirydate' => '999999999999',
                    'checkpoints' => 'OUP',
                    'orderid' => '',
                    'totalearnedpoints' => '',
                    'totalredeempoints' => '',
                    'reasonindetail' => ''
                ));
            }
        }

        public static function default_value_for_earning_and_redeem_points() {
            add_option('rs_earn_point', '1');
            add_option('rs_earn_point_value', '1');
            add_option('rs_redeem_point', '1');
            add_option('rs_redeem_point_value', '1');
            add_option('rs_redeem_point_for_cash_back', '1');
            add_option('rs_redeem_point_value_for_cash_back', '1');
        }

    }

    RSAdminEnqueues::init();
}