<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForFormForCashBack')) {

    class RSFunctionForFormForCashBack {

        public static function init() {

            add_shortcode('rsencashform', array(__CLASS__, 'encashing_front_end_form'));

            add_action('wp_ajax_rs_encash_form_value', array(__CLASS__, 'process_encashing_points_to_users'));
        }

        public static function encashing_front_end_form() {
            if (is_user_logged_in()) {
                wp_enqueue_script('encashform', false, array(), '', true);
                if (get_option('rs_enable_disable_encashing') == '1') {
                    if (is_user_logged_in()) {
                        $user_ID = get_current_user_id();
                        if (RSPointExpiry::get_sum_of_total_earned_points($user_ID) > 0) {
                            $userid = get_current_user_id();
                            $banning_type = FPRewardSystem::check_banning_type($userid);
                            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                                ob_start();
                                $encash_form_style_option = get_option('rs_encash_form_inbuilt_design');
                                ?>

                                <style type="text/css">
                                <?php
                                if ($encash_form_style_option == '1') {
                                    echo get_option('rs_encash_form_default_css');
                                } else {
                                    echo get_option('rs_encash_form_custom_css');
                                }
                                ?>

                                </style>
                                <?php
                                $rs_minimum_points_for_encash = get_option('rs_minimum_points_encashing_request');
                                $rs_maximum_points_for_encash = get_option('rs_maximum_points_encashing_request');
                                $minimum_encash_to_find = "[minimum_encash_points]";
                                $maximum_encash_to_find = "[maximum_encash_points]";
                                $rs_error_mesage_minimum_encash = get_option('rs_error_message_points_lesser_than_minimum_points');

                                $rs_current_points_less_than_minimum_points = get_option('rs_error_message_currentpoints_less_than_minimum_points');
                                $rs_current_points_less_than_minimum_points_replaced = str_replace($minimum_encash_to_find, $rs_minimum_points_for_encash, $rs_current_points_less_than_minimum_points);
                                $user_ID = get_current_user_id();
                                $currentuserpoints = RSPointExpiry::get_sum_of_total_earned_points($user_ID);
                                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                $currentuserpoints = round($currentuserpoints, $roundofftype);
                                $rs_error_mesage_minimum_encash_replaced = str_replace($minimum_encash_to_find, $rs_minimum_points_for_encash != '' ? $rs_minimum_points_for_encash : '0', $rs_error_mesage_minimum_encash);
                                $rs_error_mesage_min_max_encash_replaced = str_replace($maximum_encash_to_find, $rs_maximum_points_for_encash != '' ? $rs_maximum_points_for_encash : $currentuserpoints, $rs_error_mesage_minimum_encash_replaced);

                                echo '<form id="encashing_form" method="post" enctype="multipart/form-data">';
                                echo '<div class ="rs_encash_points_value"><p><label><b>' . get_option("rs_encashing_points_label") . '</b></label></p><p><input type = "text" id = "rs_encash_points_value" name = "rs_encash_points_value" value=""></p></div>';
                                echo '<div class = "error" for = "rs_encash_points_value" id ="points_empty_error">' . addslashes(get_option("rs_error_message_points_empty_encash")) . '</div>';
                                echo '<div class = "error" for = "rs_encash_points_value" id ="points_number_error">' . addslashes(get_option("rs_error_message_points_number_val_encash")) . '</div>';
                                echo '<div class = "error" for = "rs_encash_points_value" id ="points_greater_than_earnpoints_error">' . addslashes(get_option("rs_error_message_points_greater_than_earnpoints")) . '</div>';
                                echo '<div class = "error" for = "rs_encash_points_value" id ="currentpoints_lesser_than_minimumpoints_error">' . addslashes($rs_current_points_less_than_minimum_points_replaced) . '</div>';
                                echo '<div class = "error" for = "rs_encash_points_value" id ="points_lesser_than_minpoints_error">' . addslashes($rs_error_mesage_min_max_encash_replaced) . '</div>';
                                echo '<div class ="rs_encash_points_reason"><p><label><b>' . addslashes(get_option("rs_encashing_reason_label")) . '</b></label></p><p><textarea name ="rs_encash_points_reason" id="rs_encash_points_reason" rows= "3" cols= "50"></textarea></p></div>';
                                echo '<div class = "error" for = "rs_encash_points_reason" id ="reason_empty_error">' . addslashes(get_option("rs_error_message_reason_encash_empty")) . '</div>';
                                echo '<div class ="rs_encash_payment_method"><p><label><b>' . addslashes(get_option("rs_encashing_payment_method_label")) . '</b></label></p><p><select id= "rs_encash_payment_method"><option value="encash_through_paypal_method">PayPal</option><option value="encash_through_custom_payment">Custom Payment</option></select></p></div>';
                                echo '<div class ="rs_encash_paypal_address"><p><label><b>' . addslashes(get_option("rs_encashing_payment_paypal_label")) . '</b></label></p><p><input type = "text" id = "rs_encash_paypal_address" name = "rs_encash_paypal_address" value=""></p></div>';
                                echo '<div class = "error" for = "rs_encash_paypal_address" id ="paypal_email_empty_error">' . addslashes(get_option("rs_error_message_paypal_email_empty")) . '</div>';
                                echo '<div class = "error" for = "rs_encash_paypal_address" id ="paypal_email_format_error">' . addslashes(get_option("rs_error_message_paypal_email_wrong")) . '</div>';
                                echo '<div class ="rs_encash_custom_payment_option_value"><p><label><b>' . addslashes(get_option("rs_encashing_payment_custom_label")) . '</b></label></p><p><textarea name ="rs_encash_custom_payment_option_value" id="rs_encash_custom_payment_option_value" rows= "3" cols= "50"></textarea></p></div>';
                                echo '<div class = "error" for = "rs_encash_custom_payment_option_value" id ="paypal_custom_option_empty_error">' . addslashes(get_option("rs_error_custom_payment_field_empty")) . '</div>';
                                echo '<div class ="rs_encash_submit"><input type = "submit" name= "rs_encash_submit_button" value="' . addslashes(get_option("rs_encashing_submit_button_label")) . '" id="rs_encash_submit_button"></div>';
                                echo '<div class = "success_info" for = "rs_encash_submit_button" id ="encash_form_success_info"><b>' . addslashes(get_option("rs_message_encashing_request_submitted")) . '</b></div>';
                                echo '</form>';
                                $getcontent = ob_get_clean();
                                return $getcontent;
                            } else {
                                echo get_option("rs_message_for_banned_users_encashing");
                            }
                        } else {
                            echo get_option("rs_message_users_nopoints_encashing");
                        }
                    } else {
                        ?>
                        <p><?php ob_start(); ?> <a href="<?php echo wp_login_url(); ?>" title="__('Login', 'rewardsystem')"><?php echo get_option("rs_encashing_login_link_label"); ?></a>
                            <?php
                            $message_for_guest = get_option("rs_message_for_guest_encashing");
                            $guest_encash_string_to_find = "[rssitelogin]";
                            $guest_encash_string_to_replace = ob_get_clean();
                            $guest_encash_replaced_content = str_replace($guest_encash_string_to_find, $guest_encash_string_to_replace, $message_for_guest);
                            echo $guest_encash_replaced_content;
                        }
                    }
                } else {
                    $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                    $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                    $linkforlogin = add_query_arg('redirect_to', get_permalink(), $myaccountlink);
                    $message = get_option('rs_message_shortcode_guest_display');
                    $login = get_option('rs_message_shortcode_login_name');
                    echo '<br>' . $message . ' <a href=' . $linkforlogin . '> ' . $login . '</a>';
                }
            }

            public static function process_encashing_points_to_users() {
                global $wpdb;
                if (isset($_POST['points_to_encash']) && isset($_POST['reason_to_encash']) && isset($_POST['payment_method']) && isset($_POST['converted_value_of_points']) && isset($_POST['username_of_encash_request']) && isset($_POST['encash_default_status'])) {
                    $custom_option_details_for_encashing = '';
                    $encasher_userid = $_POST['userid_of_encash_request'];
                    $encasher_username = $_POST['username_of_encash_request'];
                    $points_to_be_encashed = $_POST['points_to_encash'];
                    $converted_value_of_encash_points = $_POST['converted_value_of_points'];
                    $current_points_for_user = $_POST['encasher_current_points'];
                    $reason_for_encashing = $_POST['reason_to_encash'];
                    $payment_method_for_encashing = $_POST['payment_method'];
                    $paypal_email_for_encashing = $_POST['paypal_email_id'];
                    if (isset($_POST['custom_payment_details'])) {
                        $custom_option_details_for_encashing = $_POST['custom_payment_details'];
                    }
                    $table_name = $wpdb->prefix . "sumo_reward_encashing_submitted_data";
                    $user_id = get_current_user_id();
                    $date = rs_function_to_get_expiry_date_in_unixtimestamp();
                    $default_status_of_encash_request = $_POST['encash_default_status'];
                    $wpdb->insert($table_name, array('userid' => $encasher_userid, 'userloginname' => $encasher_username, 'pointstoencash' => $points_to_be_encashed, 'encashercurrentpoints' => $current_points_for_user, 'reasonforencash' => $reason_for_encashing, 'encashpaymentmethod' => $payment_method_for_encashing, 'paypalemailid' => $paypal_email_for_encashing, 'otherpaymentdetails' => $custom_option_details_for_encashing, 'status' => $default_status_of_encash_request, 'pointsconvertedvalue' => $converted_value_of_encash_points, 'date' => date('Y-m-d H:i:s')));
                    $redeempoints = RSPointExpiry::perform_calculation_with_expiry($points_to_be_encashed, $user_id);
                    $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_id);
                    $equredeemamt = RSPointExpiry::redeeming_conversion_settings($redeempoints);
                    RSPointExpiry::record_the_points($user_id, '0', $points_to_be_encashed, $date, 'CBRP', '0', $equredeemamt, '0', '0', '0', '0', '', $totalpoints, '', '0');
                }
                exit();
            }

        }

        RSFunctionForFormForCashBack::init();
    }