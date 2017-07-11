<?php
/*
 * Reset Tab Setting
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSReset')) {

    class RSReset {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_reset', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_reset', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('woocommerce_admin_field_reset_field', array(__CLASS__, 'add_admin_field_to_reward_system'));

            add_action('wp_ajax_nopriv_rsresetuserdata', array(__CLASS__, 'process_reset_data_users'));

            add_action('wp_ajax_rsresetuserdata', array(__CLASS__, 'process_reset_data_users'));

            add_action('wp_ajax_rssplitajaxpreviousorder', array(__CLASS__, 'process_reset_previous_order'));

            add_action('woocommerce_admin_field_reset_tab_settings', array(__CLASS__, 'rs_field_to_reset_tab'));

            add_action('wp_ajax_rsresettabsettings', array(__CLASS__, 'reset_reward_system_admin_settings'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_reset'] = __('Reset', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;

            return apply_filters('woocommerce_rewardsystem_reset_settings', array(
                array(
                    'name' => __('Reset Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reset_setting'
                ),
                array(
                    'type' => 'reset_field',
                ),
                array('type' => 'sectionend', 'id' => '_rs_reset_setting'),
                array(
                    'name' => __('Setting to Reset Plugin Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reset_tab_setting'
                ),
                array(
                    'type' => 'reset_tab_settings',
                ),
                array('type' => 'sectionend', 'id' => '_rs_reset_tab_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSReset::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSReset::reward_system_admin_fields());
        }

        public static function add_admin_field_to_reward_system() {
            global $woocommerce;
            ?>
            <style type="text/css">
                p.sumo_reward_points {
                    display:none;
                }
                #mainforms {
                    display:none;
                }
            </style>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_reset_data_for"><?php _e('Reset Data for', 'rewardsystem'); ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="radio" name="rs_reset_data_all_users" id="rs_reset_data_all_users" class="rs_reset_data_for_users" value="1" checked="checked"/>All Users<br>
                    <input type="radio" name="rs_reset_data_all_users" id="rs_reset_data_selected_users" class="rs_reset_data_for_users" value="2"/>Selected Users<br>
                </td>
            </tr>
            <?php
            $field_id = "rs_reset_selected_user_data";
            $field_label = "Select Users to Reset Data";
            $getuser = get_option('rs_reset_selected_user_data');
            echo rs_function_to_add_field_for_user_select($field_id, $field_label, $getuser);
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_reset_user_reward_points">
                        <?php _e('Reset User Reward Points', 'rewardsystem'); ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox" name="rs_reset_user_reward_points" id="rs_reset_user_reward_points" value="1" checked="checked"/>
                </td>
            </tr>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_reset_user_log">
                        <?php _e('Reset User Logs', 'rewardsystem'); ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox" name="rs_reset_user_log" id="rs_reset_user_log" value="1" checked="checked"/>
                </td>
            </tr>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_reset_master_log">
                        <?php _e('Reset Master Logs', 'rewardsystem'); ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox" name="rs_reset_master_log" id="rs_reset_master_log" value="1" checked="checked"/>
                </td>
            </tr>
            <tr valign="top">
                <th class="titledesc"scope="row">
                    <label for="rs_reset_previous_order">
                        <?php _e('Reset Points for Previous Order', 'rewardsystem'); ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox" name="rs_reset_previous_order" id="rs_reset_previous_order" value="1"/>
                </td>
            </tr>

            <tr valign="top">
                <th class="titledesc"scope="row">
                    <label for="rs_reset_referral_log_table">
                        <?php _e('Reset Referral Reward Table', 'rewardsystem'); ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox" name="rs_reset_referral_log_table" id="rs_reset_referral_log_table" value="1"/>
                </td>
            </tr>

            <tr valign="top">
                <td>
                </td>
                <td>
                    <input type="submit" class="button-primary" name="rs_reset_data_submit" id="rs_reset_data_submit" value="Reset Data" />
                    <img class="gif_rs_sumo_reward_button_for_reset" src="<?php echo WP_PLUGIN_URL; ?>/rewardsystem/admin/images/update.gif" style="width:32px;height:32px;position:absolute"/><br>
                    <div class="rs_reset_success_data">

                    </div>
                </td>
            </tr>
            <?php
            if (isset($_GET['page']) == 'rewardsystem_callback') {
                if (isset($_GET['tab'])) {
                    if ($_GET['tab'] == 'rewardsystem_reset') {
                        if ((float) $woocommerce->version <= (float) ('2.2.0')) {
                            echo rs_common_chosen_function('#rs_reset_selected_user_data');
                        } else {
                            if ((float) $woocommerce->version > (float) ('2.2.0') && (float) $woocommerce->version < (float) ('3.0.0')) {
                                echo rs_common_select_function('#rs_reset_selected_user_data');
                            }
                        }
                    }
                }
            }
            echo rs_common_ajax_function_to_select_user('rs_reset_selected_user_data');
            ?>        
            <script type="text/javascript">
                jQuery(function () {
                    var initialdata = jQuery('.rs_reset_data_for_users').filter(":checked").val();
                    if (initialdata === '1') {
                        jQuery('#rs_reset_selected_user_data').closest('tr').hide();
                    } else {
                        jQuery('#rs_reset_selected_user_data').closest('tr').show();
                    }
                    //Get a Value on Change of Radio Button
                    jQuery('.rs_reset_data_for_users').change(function () {
                        var presentdata = jQuery(this).filter(":checked").val();
                        if (presentdata === '1') {
                            jQuery('#rs_reset_selected_user_data').closest('tr').hide();
                            jQuery('#rs_reset_master_log').parent().parent().css('display', 'block');
                        } else {
                            jQuery('#rs_reset_selected_user_data').closest('tr').show();
                            jQuery('#rs_reset_master_log').parent().parent().css('display', 'none');
                        }
                    });
                    jQuery('.gif_rs_sumo_reward_button_for_reset').css('display', 'none');
                    jQuery('#rs_reset_data_submit').click(function () {
                        if (confirm("Are You Sure ? Do You Want to Reset Your Data?") == true) {
                            jQuery('.gif_rs_sumo_reward_button_for_reset').css('display', 'inline-block');
                            var resetoptions = jQuery('.rs_reset_data_for_users').filter(":checked").val();
                            var selectedusers = jQuery('#rs_reset_selected_user_data').val();
                            var resetuserpoints = jQuery('#rs_reset_user_reward_points').filter(":checked").val();
                            var resetuserlogs = jQuery('#rs_reset_user_log').filter(":checked").val();
                            var resetmasterlogs = jQuery('#rs_reset_master_log').filter(":checked").val();
                            var resetpreviousorder = jQuery('#rs_reset_previous_order').filter(":checked").val();
                            var resetreferrallog = jQuery('#rs_reset_referral_log_table').filter(":checked").val();
                            jQuery(this).attr('data-clicked', '1');
                            var dataclicked = jQuery(this).attr('data-clicked');
                            var dataparam = ({
                                action: 'rsresetuserdata',
                                resetdatafor: resetoptions,
                                rsselectedusers: selectedusers,
                                rsresetuserpoints: resetuserpoints,
                                rsresetuserlogs: resetuserlogs,
                                rsresetmasterlogs: resetmasterlogs,
                                resetpreviousorder: resetpreviousorder,
                                resetreferrallog: resetreferrallog,
                            });

                            function getorder(id) {
                                return jQuery.ajax({
                                    type: 'POST',
                                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                    data: ({
                                        action: 'rssplitajaxpreviousorder',
                                        ids: id,
                                        proceedanyway: dataclicked,
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
                                        if (response != 'success') {
                                            if (response) {
                                                var j = 1;
                                                var i, j, temparray, chunk = 10;
                                                for (i = 0, j = response.length; i < j; i += chunk) {
                                                    temparray = response.slice(i, i + chunk);
                                                    getorder(temparray);
                                                }
                                                jQuery.when(getorder()).done(function (a1) {
                                                    console.log('Ajax Done Successfully');
                                                    jQuery('.gif_rs_sumo_reward_button_for_reset').css('display', 'none');
                                                    jQuery('.rs_reset_success_data').fadeIn();
                                                    jQuery('.rs_reset_success_data').html("Data Resetted Successfully");
                                                    jQuery('.rs_reset_success_data').fadeOut(5000);
                                                    location.reload(true);
                                                });
                                            } else {
                                                jQuery('.gif_rs_sumo_reward_button_for_reset').css('display', 'none');
                                                jQuery('.rs_reset_success_data').fadeIn();
                                                jQuery('.rs_reset_success_data').html("Data Resetted Successfully");
                                                jQuery('.rs_reset_success_data').fadeOut(5000);
                                                location.reload(true);
                                            }

                                        }



                                    }, 'json');
                            return false;
                        } else {
                            return false;
                        }

                    });
                });
            </script>
            <?php
        }

        public static function process_reset_previous_order() {
            if (isset($_POST['ids'])) {
                $products = $_POST['ids'];
                foreach ($products as $product) {
                    delete_post_meta($product, 'reward_points_awarded');
                }
            }
            exit();
        }

        public static function process_reset_data_users() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $table_name2 = $wpdb->prefix . 'rsrecordpoints';
            $order_id = array();
            if (isset($_POST['resetdatafor'])) {

                if (isset($_POST['resetpreviousorder'])) {
                    $resetpreviousorder = $_POST['resetpreviousorder'];
                    if ($resetpreviousorder == '1') {
                        $args = array('post_type' => 'shop_order', 'numberposts' => '-1', 'meta_query' => array(array('key' => 'reward_points_awarded', 'compare' => 'EXISTS')), 'post_status' => 'published', 'fields' => 'ids', 'cache_results' => false);
                        $order_id = get_posts($args);
                    }
                }

                if (isset($_POST['rsresetuserpoints'])) {
                    $proceeduserrewardpoints = $_POST['rsresetuserpoints'];
                } else {
                    $proceeduserrewardpoints = '';
                }
                if (isset($_POST['rsresetuserlogs'])) {
                    $proceeduserlogs = $_POST['rsresetuserlogs'];
                } else {
                    $proceeduserlogs = '';
                }
                if (isset($_POST['rsresetmasterlogs'])) {
                    $proceedmasterlogs = $_POST['rsresetmasterlogs'];
                } else {
                    $proceedmasterlogs = "";
                }
                if (isset($_POST['resetreferrallog'])) {
                    $proceedreferrallog = $_POST['resetreferrallog'];
                } else {
                    $proceedreferrallog = '';
                }

                if ($_POST['resetdatafor'] == '2') {   //Selected User                    
                    if (isset($_POST['rsselectedusers'])) {
                         $newarray =  $_POST['rsselectedusers'];
                        if (!is_array($_POST['rsselectedusers'])) {
                            $newarray = explode(',', $_POST['rsselectedusers']);
                        }
                        if (is_array($newarray) && !empty($newarray)) {
                            foreach ($newarray as $selecteduserid) {
                                if ($proceeduserrewardpoints == '1') {
                                    delete_user_meta($selecteduserid, 'rs_earned_points_before_delete');
                                    delete_user_meta($selecteduserid, 'rs_user_total_earned_points');
                                    delete_user_meta($selecteduserid, 'rs_expired_points_before_delete');
                                    delete_user_meta($selecteduserid, 'rs_redeem_points_before_delete');
                                    delete_user_meta($selecteduserid, '_my_reward_points');
                                    $wpdb->query("DELETE FROM $table_name WHERE userid = $selecteduserid");
                                }

                                if ($proceeduserlogs == '1') {
                                    $wpdb->query("UPDATE $table_name2 SET showuserlog = true WHERE userid = $selecteduserid");
                                    delete_user_meta($selecteduserid, '_my_points_log');
                                }

                                if ($proceedmasterlogs == '1') {
                                    $wpdb->query("UPDATE $table_name2 SET showmasterlog = true WHERE userid  = $selecteduserid");
                                }

                                if ($proceedreferrallog == '1') {
                                    $getdatas = get_option('rs_referral_log');

                                    if (isset($getdatas[$selecteduserid])) {
                                        unset($getdatas[$selecteduserid]);
                                        update_option('rs_referral_log', $getdatas);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    //If not then All Users
                    $get_var = $wpdb->get_col('SELECT ID FROM wp_users');
                    $user_ids = implode(',', $get_var);
                    if ($proceeduserrewardpoints == '1') {
                        $wpdb->query("DELETE FROM $table_name WHERE userid IN ($user_ids)");
                        $wpdb->query("DELETE FROM wp_usermeta WHERE meta_key IN ('_my_reward_points','rs_earned_points_before_delete','rs_user_total_earned_points','rs_expired_points_before_delete','rs_redeem_points_before_delete') AND user_id IN ($user_ids)");
                    }
                    if ($proceeduserlogs == '1') {
                        $wpdb->query("UPDATE $table_name2 SET showuserlog = true WHERE userid IN ($user_ids)");
                        $wpdb->query("DELETE FROM wp_usermeta WHERE meta_key IN ('_my_points_log') AND user_id IN ($user_ids)");
                    }
                    if ($proceedmasterlogs == '1') {
                        $wpdb->query("UPDATE $table_name2 SET showmasterlog = true WHERE userid IN ($user_ids)");
                        delete_option('rsoveralllog');
                    }
                    if ($proceedreferrallog == '1') {
                        delete_option('rs_referral_log', true);
                    }
                }
                echo json_encode($order_id);
            }
            exit();
        }

        public static function rs_field_to_reset_tab() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">                    
                    <label for="rs_reset_tab_label"><?php _e('Click the Button to Reset the Entire Plugin settings (Excluding Plugin Data)', 'rewardsystem'); ?></label>
                </th>
                <td>
                    <input type="submit" class="button-primary" name="rs_reset_tab" id="rs_reset_tab" value="Reset" />
                    <img class="gif_rs_reset_tab_settings" src="<?php echo WP_PLUGIN_URL; ?>/rewardsystem/admin/images/update.gif" style="width:32px;height:32px;position:absolute"/><br>
                    <div class="rs_reset_tab_setting_success">
                    </div>
                </td>
            </tr>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('.gif_rs_reset_tab_settings').css('display', 'none');
                    jQuery('#rs_reset_tab').click(function () {
                        if (confirm("Are You Sure ? Do You Want to Reset Your Tab Settings?") == true) {
                            jQuery('.gif_rs_reset_tab_settings').css('display', 'inline-block');
                            jQuery(this).attr('data-clicked', '1');
                            var dataclicked = jQuery(this).attr('data-clicked');
                            var dataparam = ({
                                action: 'rsresettabsettings',
                                dataclicked: dataclicked
                            });

                            jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                    function (response) {
                                        jQuery('.gif_rs_reset_tab_settings').css('display', 'none');
                                        jQuery('.rs_reset_tab_setting_success').fadeIn();
                                        jQuery('.rs_reset_tab_setting_success').html("Settings Resetted Successfully");
                                        jQuery('.rs_reset_tab_setting_success').fadeOut(5000);
                                        location.reload(true);
                                    }, 'json');
                            return false;
                        } else {
                            return false;
                        }
                    });
                });
            </script>
            <?php
        }

        public static function reset_reward_system_admin_settings() {
            if (isset($_POST['dataclicked'])) {
                foreach (RSGeneralTabSetting::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                        update_option('rs_earn_point', '1');
                        update_option('rs_earn_point_value', '1');
                        update_option('rs_redeem_point', '1');
                        update_option('rs_redeem_point_value', '1');
                        update_option('rs_redeem_point_for_cash_back', '1');
                        update_option('rs_redeem_point_value_for_cash_back', '1');
                    }
                }
                foreach (RSRewardPointsForAction::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSMemberLevel::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSMessage::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (FPRewardSystemShopPageTab::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (FPRewardSystemSingleProductPageTab::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }


                foreach (RSCart::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSCheckout::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSMyaccount::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSUpdate::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSStatus::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSReferAFriend::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSSocialReward::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSMail::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSSms::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSTroubleshoot::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSLocalization::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSFormForCashBack::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSCouponRewardPoints::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                delete_option('rewards_dynamic_rule_couponpoints');

                foreach (RSNominee::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                foreach (RSFormForSendPoints::reward_system_admin_fields() as $setting) {
                    if (isset($setting['newids']) && isset($setting['std'])) {
                        delete_option($setting['newids']);
                        add_option($setting['newids'], $setting['std']);
                    }
                }

                delete_option('rewards_dynamic_rule_manual');

                delete_transient('woocommerce_cache_excluded_uris');
            }
            exit();
        }

    }

    RSReset::init();
}