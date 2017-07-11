<?php
/*
 * User Reward Points Tab
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSUserRewardPoints')) {

    class RSUserRewardPoints {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_user_reward_points', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_user_reward_points', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('woocommerce_admin_field_rs_wplist_for_user_reward_points', array(__CLASS__, 'rs_list_of_user_reward_points_log'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_user_reward_points'] = __('User Reward Points', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;
            return apply_filters('woocommerce_rewardsystem_user_reward_points_settings', array(
                array(
                    'name' => __('User Reward Points Log', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_user_reward_points_setting',
                ),
                array(
                    'type' => 'rs_wplist_for_user_reward_points',
                ),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields(RSUserRewardPoints::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSUserRewardPoints::reward_system_admin_fields());
        }

        public static function rs_list_of_user_reward_points_log() {
            if (isset($_GET['tab']) && $_GET['tab'] == 'rewardsystem_user_reward_points') {
                global $wpdb;
                $table_name = $wpdb->prefix . 'rspointexpiry';
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
                if ((!isset($_GET['view'])) && (!isset($_GET['edit']))) {
                    $newwp_list_table_for_users = new WP_List_Table_for_Users();
                    $newwp_list_table_for_users->prepare_items();
                    $newwp_list_table_for_users->search_box('Search Users', 'search_id');
                    $newwp_list_table_for_users->display();
                } elseif (isset($_GET['view'])) {
                    $newwp_list_table_for_users = new WP_List_Table_for_View_Log();
                    $newwp_list_table_for_users->prepare_items();
                    $newwp_list_table_for_users->search_box('Search', 'search_id');
                    $newwp_list_table_for_users->display();
                    ?>
                    <a href="<?php echo remove_query_arg(array('view'), get_permalink()); ?>">Go Back</a>
                    <?php
                } else {
                    $user_ID = $_GET['edit'];
                    $date = rs_function_to_get_expiry_date_in_unixtimestamp();
                    if (isset($_POST['my_reward_points']) != '') {
                        $earned_points = $_POST['my_reward_points'];
                        if (isset($_POST['submitrs'])) {
                            $reason = $_POST['rs_reward_edit_reason'];
                            RSPointExpiry::insert_earning_points($user_ID, $earned_points, '0', $date, 'MAURP', '', $earned_points, '', $reason);
                            $equearnamt = RSPointExpiry::earning_conversion_settings($earned_points);
                            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_ID);
                            RSPointExpiry::record_the_points($user_ID, $earned_points, '0', $date, 'MAURP', $equearnamt, '0', '0', '0', '0', '0', $reason, $totalpoints, '', '0');
                        }
                        if (isset($_POST['rs_remove_point'])) {

                            $getusermeta = $wpdb->get_results("SELECT SUM((earnedpoints-usedpoints)) as availablepoints FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid=$user_ID", ARRAY_A);
                            $currentpoints = $getusermeta[0]['availablepoints'];
                            if ($_POST['my_reward_points'] <= $currentpoints) {
                                $used_points = $_POST['my_reward_points'];
                                $userid = $user_ID;
                                $reason = $_POST['rs_reward_edit_reason'];
                                $pointsredeemed = RSPointExpiry::perform_calculation_with_expiry($used_points, $userid);
                                $equredeemamt = RSPointExpiry::earning_conversion_settings($used_points);
                                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($userid);
                                RSPointExpiry::record_the_points($userid, '0', $used_points, $date, 'MRURP', '0', $equredeemamt, '0', '0', '0', '0', $reason, $totalpoints, '', '0');
                                $newredirect = add_query_arg('saved', 'true', get_permalink());
                                wp_safe_redirect($newredirect);
                                exit();
                            }
                        }
                    }
                    $getusermeta = $wpdb->get_results("SELECT SUM((earnedpoints-usedpoints)) as availablepoints FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid=$user_ID", ARRAY_A);
                    $my_rewards = (float)$getusermeta[0]['availablepoints'];
                    ?>
                    <style type = "text/css">
                        p.sumo_reward_points {
                            display:none;
                        }
                        #mainforms {
                            display:none;
                        }
                    </style>
                    <h3><?php _e('Update User Reward Points', 'rewardsystem'); ?></h3>
                    <table class="form-table">
                        <tr valign ="top">
                            <th class="titledesc" scope="row">
                                <label for="rs_reward_current_user_points"><?php _e('Current Points for User', 'rewardsystem'); ?></label>
                            </th>
                            <td class="forminp forminp-text">
                                <input type="text" class=""  style="min-width:150px;" readonly="readonly" id="my_current_reward_points" value="<?php
                                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                echo round($my_rewards, $roundofftype);
                                ?>"/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="rs_reward_addremove_points"><?php _e('Enter Points', 'rewardsystem'); ?></label>
                            </th>
                            <td class="forminp forminp-text">
                                <input type="text" class="" value="" style="min-width:150px;" required='required' id="my_reward_points" name="my_reward_points">
                                <div class='rs_add_remove_points_errors' style="color: red;font-size:14px;"></div>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="rs_reward_edit_reason"><?php _e('Reason in Detail'); ?></label>
                            </th>
                            <td class="forminp forminp-text">
                                <textarea cols='40' rows='5' name='rs_reward_edit_reason' required='required'></textarea>
                            </td>
                        </tr>

                        <tr valign="top">
                            <td>
                                <input type='submit' name='submitrs' id='submitrs'  class='button-primary' value='Add Points'/>

                            </td>
                            <td style="width:10px;">
                                <input type='submit' name='rs_remove_point' id='rs_remove_point' class='button-primary' value='Remove Points' />
                            </td>
                            <td>
                                <a href="<?php echo remove_query_arg(array('edit', 'saved'), get_permalink()); ?>"><input type='submit' name='rs_go_back' id='rs_go_back' class='button-primary' value='Go Back'/></a>
                            </td>

                        </tr>
                        <tr valign="top">

                        </tr>
                    </table>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery('#rs_remove_point').click(function (e) {
                                var points = Number (jQuery('#my_reward_points').val());
                                var totalpoints = '<?php echo $my_rewards; ?>';
                                console.log(totalpoints);
                                console.log(points);
                                if (totalpoints < points) {
                                    e.preventDefault();
                                    jQuery('.rs_add_remove_points_errors').fadeIn();
                                    jQuery('.rs_add_remove_points_errors').html('You entered point is more than the current points');
                                    jQuery('.rs_add_remove_points_errors').fadeOut(5000);
                                }
                            });
                        });
                    </script>

                    <?php
                }
            }
        }

    }

    RSUserRewardPoints::init();
}