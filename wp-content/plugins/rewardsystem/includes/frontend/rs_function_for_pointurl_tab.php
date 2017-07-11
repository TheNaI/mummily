<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForPointURL')) {

    class RSFunctionForPointURL {

        public static function init() {

            add_action('wp_head', array(__CLASS__, 'rs_function_to_award_points_for_url_click'));
        }

        public static function rs_common_function_for_awarding_points($uniqueid, $user_id) {
            $get_point_for_url = get_option('points_for_url_click');
            if (is_array($get_point_for_url) && !empty($get_point_for_url)) {
                if (array_key_exists($uniqueid, $get_point_for_url)) {
                    $uniqvalue = $get_point_for_url[$uniqueid];
                    $date = rs_function_to_get_expiry_date_in_unixtimestamp();
                    $current_userid = $user_id;
                    $current_date = strtotime(date('y-m-d'));
                    $time_limit = $uniqvalue['time_limit'];
                    $expiry_date = strtotime($uniqvalue['expiry_time']);
                    $count_limit = $uniqvalue['count_limit'];
                    $count = $uniqvalue['count'];
                    $current_usage_count = $uniqvalue['current_usage_count'];
                    $earned_points = $uniqvalue['points'];
                    $user_id = $uniqvalue['used_by'];
                    $checkpoints = 'RPFURL';
                    if ($time_limit == '2') {
                        if ($current_date <= $expiry_date) {
                            if ($count_limit == '1') {
                                if (!in_array($current_userid, (array) $user_id)) {
                                    RSPointExpiry::insert_earning_points($current_userid, $earned_points, 0, $date, $checkpoints, 0, '', '', $reasonindetail = '');
                                    $equearnamt = RSPointExpiry::earning_conversion_settings($earned_points);
                                    $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($current_userid);
                                    RSPointExpiry::record_the_points($current_userid, $earned_points, '0', $date, $checkpoints, $equearnamt, '0', '0', '0', '0', '0', '', $totalpoints, '', '0');
                                }
                            } else {
                                if ($count != '') {
                                    if ($current_usage_count <= $count) {
                                        if (!in_array($current_userid, (array) $user_id)) {
                                            RSPointExpiry::insert_earning_points($current_userid, $earned_points, 0, $date, $checkpoints, 0, '', '', $reasonindetail = '');
                                            $equearnamt = RSPointExpiry::earning_conversion_settings($earned_points);
                                            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($current_userid);
                                            RSPointExpiry::record_the_points($current_userid, $earned_points, '0', $date, $checkpoints, $equearnamt, '0', '0', '0', '0', '0', '', $totalpoints, '', '0');
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if ($count_limit == '1') {
                            if (!in_array($current_userid, (array) $user_id)) {
                                RSPointExpiry::insert_earning_points($current_userid, $earned_points, 0, $date, $checkpoints, 0, '', '', $reasonindetail = '');
                                $equearnamt = RSPointExpiry::earning_conversion_settings($earned_points);
                                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($current_userid);
                                RSPointExpiry::record_the_points($current_userid, $earned_points, '0', $date, $checkpoints, $equearnamt, '0', '0', '0', '0', '0', '', $totalpoints, '', '0');
                            }
                        } else {
                            if ($count != '') {
                                if ($current_usage_count <= $count) {
                                    if (!in_array($current_userid, (array) $user_id)) {
                                        RSPointExpiry::insert_earning_points($current_userid, $earned_points, 0, $date, $checkpoints, 0, '', '', $reasonindetail = '');
                                        $equearnamt = RSPointExpiry::earning_conversion_settings($earned_points);
                                        $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($current_userid);
                                        RSPointExpiry::record_the_points($current_userid, $earned_points, '0', $date, $checkpoints, $equearnamt, '0', '0', '0', '0', '0', '', $totalpoints, '', '0');
                                    }
                                }
                            }
                        }
                    }
                }
                // update_option('points_for_url_click', $get_point_for_url);
            }
        }

        public static function rs_function_to_award_points_for_url_click() {
            if (isset($_GET['rsid'])) {
                $uniqueid = $_GET['rsid'];
                if (is_user_logged_in()) {
                    $user_id = get_current_user_id(); 
                    $get_point_for_url = get_option('points_for_url_click');
                    $offer_name = $get_point_for_url[$uniqueid]['name'];
                    $count_limit = $get_point_for_url[$uniqueid]['count_limit'];
                    $url_user_id = $get_point_for_url[$uniqueid]['used_by'];
                    $time_limit = $get_point_for_url[$uniqueid]['time_limit'];
                    $current_count = $get_point_for_url[$uniqueid]['current_usage_count'];
                    $current_date = strtotime(date('y-m-d'));
                    $expiry_date = strtotime($get_point_for_url[$uniqueid]['expiry_time']);
                    $count = $get_point_for_url[$uniqueid]['count'];
                    $get_point_to_display = $get_point_for_url[$uniqueid]['points'];
                    $message_to_display = get_option('rs_success_message_for_pointurl');
                    $replace_points = str_replace('[points]', $get_point_to_display, $message_to_display);
                    $get_name_to_display = $get_point_for_url[$uniqueid]['name'];
                    $replace_name = str_replace('[offer_name]', $get_name_to_display, $replace_points);
                    ?>
                    <style type="text/css">
                        .rs_success_msg_for_pointurl {
                            width: 100%;                        
                            font-size: 20px;
                            font-weight: bold;
                            padding: 15px;
                            text-align: center;
                            background-color: black;
                            z-index: 999999;
                            position:fixed;
                            color: #fff;
                        }

                        .sk_failure_msg_for_pointsurl {
                            width: 100%;                        
                            font-size: 20px;
                            font-weight: bold;
                            padding: 15px;
                            text-align: center;
                            background-color: black;
                            z-index: 999999;
                            position:fixed;
                            color: #fff;
                        }      
                    </style>
                    <?php
                    if (!in_array($user_id, (array) $url_user_id)) {
                        if ($time_limit == '2') {
                            if ($current_date <= $expiry_date) {
                                if ($count_limit == '1') {
                                    ?>                    
                                    <div class="rs_success_msg_for_pointurl"><?php echo $replace_name; ?></div>
                                    <?php
                                    $updated_count = $current_count + 1;
                                    $get_point_for_url[$uniqueid]['current_usage_count'] = $updated_count;
                                    $get_point_for_url[$uniqueid]['used_by'][] = $user_id;
                                    self::rs_common_function_for_awarding_points($uniqueid, $user_id);
                                } else {
                                    if ($current_count < $count) {
                                        ?>                            
                                        <div class="rs_success_msg_for_pointurl"><?php echo $replace_name; ?></div>
                                        <?php
                                        $updated_count = $current_count + 1;
                                        $get_point_for_url[$uniqueid]['current_usage_count'] = $updated_count;
                                        $get_point_for_url[$uniqueid]['used_by'][] = $user_id;
                                        self::rs_common_function_for_awarding_points($uniqueid, $user_id);
                                    } else {
                                        $faliure_msg = get_option('sk_failure_message_for_couponurl_for_count_limit1');
                                        ?>                            
                                        <div class="sk_failure_msg_for_pointsurl"><?php echo $faliure_msg; ?></div>
                                        <?php
                                    }
                                }
                            } else {
                                $faliure_msg = get_option('sk_failure_message_for_couponurl_for_time_limit1');
                                $replace_name = str_replace('[offer_name]', $offer_name, $faliure_msg)
                                ?>                            
                                <div class="sk_failure_msg_for_pointsurl"><?php echo $replace_name; ?></div>
                                <?php
                            }
                        } else {
                            if ($count_limit == '1') {
                                ?>                    
                                <div class="rs_success_msg_for_pointurl"><?php echo $replace_name; ?></div>
                                <?php
                                $updated_count = $current_count + 1;
                                $get_point_for_url[$uniqueid]['current_usage_count'] = $updated_count;
                                $get_point_for_url[$uniqueid]['used_by'][] = $user_id;
                                self::rs_common_function_for_awarding_points($uniqueid, $user_id);
                            } else {
                                if ($current_count < $count) {
                                    ?>                            
                                    <div class="rs_success_msg_for_pointurl"><?php echo $replace_name; ?></div>
                                    <?php
                                    $updated_count = $current_count + 1;
                                    $get_point_for_url[$uniqueid]['current_usage_count'] = $updated_count;
                                    $get_point_for_url[$uniqueid]['used_by'][] = $user_id;
                                    self::rs_common_function_for_awarding_points($uniqueid, $user_id);
                                } else {
                                    $faliure_msg = get_option('sk_failure_message_for_couponurl_for_count_limit1');
                                    ?>                            
                                    <div class="sk_failure_msg_for_pointsurl"><?php echo $faliure_msg; ?></div>
                                    <?php
                                }
                            }
                        }
                    } else {
                        $faliure_msg = get_option('sk_failure_message_for_couponurl_for_more_than_one1');
                        ?>                    
                        <div class="sk_failure_msg_for_pointsurl"><?php echo $faliure_msg; ?></div>
                        <?php
                    }
                    update_option('points_for_url_click', $get_point_for_url);
                }
            }
        }

    }

    RSFunctionForPointURL::init();
}