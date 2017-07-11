<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSRegistrationPoints')) {

    class RSRegistrationPoints {

        public static function init() {

            add_action('user_register', array(__CLASS__, 'rs_account_signup_type'), 10, 1);

            $orderstatuslist = get_option('rs_order_status_control');
            if (is_array($orderstatuslist)) {
                foreach ($orderstatuslist as $value) {
                    add_action('woocommerce_order_status_' . $value, array(__CLASS__, 'referal_reward_points_signup'));
                }
            }

            add_action('wp_head', array(__CLASS__, 'reward_points_for_login'));
        }

        public static function rs_account_signup_type($user_id) {
            $account_signup_type = get_option('rs_select_account_signup_points_award');
            if ($account_signup_type == '1') {
                self::rs_add_registration_rewards_points($user_id);
            } else {
                if (isset($_COOKIE['rsreferredusername'])) {
                    self::rs_add_registration_rewards_points($user_id);
                }
            }
        }

        public static function rs_add_registration_rewards_points($user_id) {
            $get_registed_user = get_post_meta($user_id, 'rs_registered_user', true);
            if ($get_registed_user == '') {
                $option = get_option('rs_select_referral_points_award');
                $enableoptforreg = get_option('rs_reward_signup_after_first_purchase');
                $enableoptforrefreg = get_option('rs_referral_reward_signup_after_first_purchase');
                $enablegetrefer = get_option('rs_referral_reward_signup_getting_refer');
                $enablefirstpurchse = get_option('rs_referral_reward_getting_refer_after_first_purchase');
                if ($enablegetrefer == '1') {
                    if ($enablefirstpurchse == 'yes') {
                        self::referal_reward_points_for_get_refer_after_first_purchase($user_id);
                    } else {
                        self::referal_reward_points_for_get_refer_instatly($user_id);
                    }
                }
                if (($enableoptforreg == 'yes')) {
                    // After First Purchase Registration Points
                    self::rs_add_regpoints_to_user_after_first_purchase($user_id);
                    self::rs_add_regpoints_to_refuser_only_after_first_purchase($user_id);
                    if ($enableoptforrefreg != 'yes') {
                        // Instant Referral Registration Points
                        if ($option == '1') {
                            self::rs_add_regpoints_to_refuser_instantly($user_id);
                        }
                    }
                } else {
                    // Instant Registration Points
                    self::rs_add_regpoints_to_user_instantly($user_id);
                    self::rs_add_regpoints_to_refuser_only_after_first_purchase($user_id);
                    if ($enableoptforrefreg != 'yes') {
                        // Instant Referral Registration Points
                        if ($option == '1') {
                            self::rs_add_regpoints_to_refuser_instantly($user_id);
                        }
                    }
                }
                do_action('fp_reward_point_for_registration');
                update_post_meta($user_id, 'rs_registered_user', 1);
            }
        }

        public static function referal_reward_points_for_get_refer_after_first_purchase($user_id) {
            $referral_get_refer_point = get_option('rs_referral_reward_getting_refer');

            if (isset($_COOKIE['rsreferredusername'])) {
                // Update the Referred Person Registration Count           
                $user_info = new WP_User($user_id);
                $registered_date = $user_info->user_registered;
                $limitation = false;
                $modified_registered_date = date('Y-m-d h:i:sa', strtotime($registered_date));
                $delay_days = get_option('_rs_select_referral_points_referee_time_content');
                $checking_date = date('Y-m-d h:i:sa', strtotime($modified_registered_date . ' + ' . $delay_days . ' days '));
                $modified_checking_date = strtotime($checking_date);
                $current_date = date('Y-m-d h:i:sa');
                $modified_current_date = strtotime($current_date);
                //Is for Immediatly
                if (get_option('_rs_select_referral_points_referee_time') == '1') {
                    $limitation = true;
                } else {
                    // Is for Limited Time with Number of Days
                    if ($modified_current_date > $modified_checking_date) {
                        $limitation = true;
                    } else {
                        $limitation = false;
                    }
                }
                if ($limitation == true) {
                    $referreduser = get_user_by('login', $_COOKIE['rsreferredusername']);
                    if ($referreduser != false) {
                        $refuserid = $referreduser->ID;
                    } else {
                        $refuserid = $_COOKIE['rsreferredusername'];
                    }

                    $banning_type = FPRewardSystem::check_banning_type($refuserid);
                    if ($banning_type != 'earningonly' && $banning_type != 'both') {
                        $referral_get_refer_point = RSMemberFunction::user_role_based_reward_points($refuserid, $referral_get_refer_point);
                        $mainpoints = array();
                        $mainpoints[$user_id] = array('userid' => $user_id, 'refpoints' => $referral_get_refer_point);
                        RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($user_id, 'rs_get_data_for_reward_points_get_refer', $mainpoints);
                    }
                }
            }
        }

        public static function referal_reward_points_for_get_refer_instatly($user_id) {
            if (isset($_COOKIE['rsreferredusername'])) {
                global $wpdb;
                $registration_points = RSMemberFunction::user_role_based_reward_points($user_id, get_option('rs_referral_reward_getting_refer'));
                $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                $enabledisablepoints = get_option('rs_enable_disable_max_earning_points_for_user');
                $currentregistrationpoints = $registration_points;
                if ($enabledisablepoints == 'yes') {
                    if (($currentregistrationpoints <= $restrictuserpoints) || ($restrictuserpoints == '')) {
                        $currentregistrationpoints = $currentregistrationpoints;
                    } else {
                        $currentregistrationpoints = $restrictuserpoints;
                    }
                }
                $date = rs_function_to_get_expiry_date_in_unixtimestamp();
                RSPointExpiry::insert_earning_points($user_id, $registration_points, '0', $date, 'RRPGR', '', '', '', '');
                $equearnamt = RSPointExpiry::earning_conversion_settings($registration_points);
                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_id);
                RSPointExpiry::record_the_points($user_id, $registration_points, '0', $date, 'RRPGR', $equearnamt, '0', '0', '0', '0', '0', '', $totalpoints, '', '0');
                add_user_meta($user_id, '_points_awarded_get_refer', '1');
            }
        }

        public static function referal_reward_points_signup($order_id) {
            global $wpdb;
            $option = get_option('rs_select_referral_points_award');
            $order = new WC_Order($order_id);
            $user_id = rs_get_order_obj($order);
            $user_id = $user_id['order_userid'];
            if ($user_id != '') {
                $order_ids = $wpdb->get_results("SELECT posts.ID
			FROM $wpdb->posts as posts
			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
			WHERE   meta.meta_key       = '_customer_user'
			AND     posts.post_type     IN ('" . implode("','", wc_get_order_types('order-count')) . "')
			AND     posts.post_status   IN ('" . implode("','", array_keys(wc_get_order_statuses())) . "')
			AND     meta_value          = $user_id
		", ARRAY_A);
                $order_count = count($order_ids);                
                $enableoptforrefreg = get_option('rs_referral_reward_signup_after_first_purchase');
                $enablefirstpurchase = get_option('rs_reward_signup_after_first_purchase');
                if ($option == '1') {
                    if ($enableoptforrefreg == 'yes' || $enablefirstpurchase == 'yes') {
                        self::reward_points_after_first_purchase($order_id);
                    }
                }
                if ($option == '2') {
                    $get_order_count = get_option('rs_number_of_order_for_referral_points');
                    if ($get_order_count != '') {
                        if ($get_order_count <= $order_count) {
                            self::reward_points_after_first_purchase($order_id);
                        }
                    }
                }

                if ($option == '3') {
                    $get_order_amount = get_option('rs_amount_of_order_for_referral_points');
                    if ($get_order_amount != '') {
                        foreach ($order_ids as $values) {
                            $total[] = get_post_meta($values['ID'], '_order_total', true);
                        }
                        $order_total = array_sum($total);
                        if ($get_order_amount <= $order_total) {
                            self::reward_points_after_first_purchase($order_id);
                        }
                    }
                }
            }
            $enablegetrefer = get_option('rs_referral_reward_signup_getting_refer');
            $enablefirstpurchse = get_option('rs_referral_reward_getting_refer_after_first_purchase');
            if ($enablegetrefer == '1') {
                if ($enablefirstpurchse == 'yes') {
                    self::reward_points_after_first_purchase_get_refer($order_id);
                }
            }
        }

        public static function reward_points_after_first_purchase_get_refer($order_id) {
            global $wpdb;
            $order = new WC_Order($order_id);
            $user_id = rs_get_order_obj($order);
            $user_id = $user_id['order_userid'];
            if ($user_id != '') {
                $table_name = $wpdb->prefix . 'rspointexpiry';
                if (RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($user_id, 'rs_after_first_purchase_get_refer') != 'yes') {
                    $fetchdata = array();
                    $fetchdata = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($user_id, 'rs_get_data_for_reward_points_get_refer');
                    if (is_array($fetchdata)) {
                        $refregpoints = $fetchdata[$user_id]['refpoints'];
                        $refuserid = $fetchdata[$user_id]['userid'];
                        $checkredeeming = RSPointExpiry::check_redeeming_in_order($order_id, $refuserid);
                        $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                        $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                        if ($user_id) {
                            if (RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($user_id, '_points_awarded_get_refer') != '1') {
                                $new_obj = new RewardPointsOrder($order_id, $apply_previous_order_points = 'no');
                                if ($enabledisablemaxpoints == 'yes') {
                                    $new_obj->check_point_restriction($restrictuserpoints, $refregpoints, $pointsredeemed = 0, $event_slug = 'RRPGR', $user_id, $nomineeid = '', $refuserid, $productid = '', $variationid = '', $reasonindetail = '');
                                } else {
                                    $equearnamt = RSPointExpiry::earning_conversion_settings($refregpoints);
                                    $valuestoinsert = array('pointstoinsert' => $refregpoints, 'pointsredeemed' => 0, 'event_slug' => 'RRPGR', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $user_id, 'referred_id' => $refuserid, 'product_id' => '', 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $refregpoints, 'totalredeempoints' => 0);
                                    $new_obj->total_points_management($valuestoinsert);
                                }
                                add_user_meta($user_id, '_points_awarded_get_refer', '1');
                                add_user_meta($user_id, 'rs_after_first_purchase_get_refer', 'yes');
                            }
                        }
                    }
                }
            }
        }

        public static function reward_points_after_first_purchase($order_id) {
            global $wpdb;
            $order = new WC_Order($order_id);
            $user_id = rs_get_order_obj($order);
            $user_id = $user_id['order_userid'];
            $table_name = $wpdb->prefix . 'rspointexpiry';
            if (RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($user_id, 'rs_after_first_purchase') != 'yes') {
                $fetchdata = array();
                $fetchdata = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($user_id, 'rs_get_data_for_reward_points');
                if (is_array($fetchdata)) {
                    $curregpoints = $fetchdata[$user_id]['points'];
                    $refregpoints = $fetchdata[$user_id]['refpoints'];
                    $userid = $fetchdata[$user_id]['userid'];
                    $refuserid = $fetchdata[$user_id]['refuserid'];
                    $previouslog = get_option('rs_referral_log');
                    $checkredeeming = RSPointExpiry::check_redeeming_in_order($order_id, $user_id);
                    $enableoption = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, 'rs_check_enable_option_for_redeeming');
                    $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                    $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                    $date = rs_function_to_get_expiry_date_in_unixtimestamp();
                    if ($user_id) {
                        if (RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($userid, '_points_awarded') != '1') {
                            $oldpoints = $wpdb->get_results("SELECT SUM((earnedpoints-usedpoints)) as availablepoints FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid=$userid", ARRAY_A);
                            $totaloldpoints = $oldpoints[0]['availablepoints'];
                            $currentregistrationpoints = $totaloldpoints + $curregpoints;
                            if ($enableoption == 'yes' && $checkredeeming == false) {
                                RSPointExpiry::insert_earning_points($user_id, $curregpoints, '0', $date, 'RRP', '', '', '', '');
                                $equearnamt = RSPointExpiry::earning_conversion_settings($curregpoints);
                                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_id);
                                RSPointExpiry::record_the_points($user_id, $curregpoints, '0', $date, 'RRP', $equearnamt, '0', $order_id, $productid = '', $variationid = '', '0', '', $totalpoints, '', '0');
                            } else {
                                RSPointExpiry::insert_earning_points($user_id, $curregpoints, '0', $date, 'RRP', '', '', '', '');
                                $equearnamt = RSPointExpiry::earning_conversion_settings($curregpoints);
                                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_id);
                                RSPointExpiry::record_the_points($user_id, $curregpoints, '0', $date, 'RRP', $equearnamt, '0', $order_id, $productid = '', $variationid = '', '0', '', $totalpoints, '', '0');
                            }
                            add_user_meta($user_id, '_points_awarded', '1');
                        }

                        if ($refuserid) {
                            if (RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($user_id, 'rs_referrer_regpoints_awarded') != '1') {
                                $new_obj = new RewardPointsOrder($order_id, $apply_previous_order_points = 'no');
                                if ($enabledisablemaxpoints == 'yes') {
                                    $new_obj->check_point_restriction($restrictuserpoints, $refregpoints, $pointsredeemed = 0, $event_slug = 'RRRP', $user_id, $nomineeid = '', $refuserid, $productid = '', $variationid = '', $reasonindetail = '');
                                } else {
                                    $equearnamt = RSPointExpiry::earning_conversion_settings($refregpoints);
                                    $valuestoinsert = array('pointstoinsert' => $refregpoints, 'pointsredeemed' => 0, 'event_slug' => 'RRRP', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $refuserid, 'referred_id' => $user_id, 'product_id' => '', 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $refregpoints, 'totalredeempoints' => 0);
                                    $new_obj->total_points_management($valuestoinsert);
                                    $previouslog = get_option('rs_referral_log');
                                    RS_Referral_Log::main_referral_log_function($refuserid, $user_id, $refregpoints, array_filter((array) $previouslog));
                                    update_user_meta($user_id, '_rs_i_referred_by', $refuserid);
                                }
                                add_user_meta($user_id, 'rs_referrer_regpoints_awarded', '1');
                            }
                        }
                        add_user_meta($user_id, 'rs_after_first_purchase', 'yes');
                    }
                }
            }
        }

        /* After First Purchase Referral Registration Points */

        public static function rs_add_regpoints_to_refuser_only_after_first_purchase($user_id) {
            $referral_registration_points = get_option('rs_referral_reward_signup');
            $registration_points = get_option('rs_reward_signup');
            if (isset($_COOKIE['rsreferredusername'])) {
                $user_info = new WP_User($user_id);
                $registered_date = $user_info->user_registered;
                $limitation = false;
                $modified_registered_date = date('Y-m-d h:i:sa', strtotime($registered_date));
                $delay_days = get_option('_rs_select_referral_points_referee_time_content');
                $checking_date = date('Y-m-d h:i:sa', strtotime($modified_registered_date . ' + ' . $delay_days . ' days '));
                $modified_checking_date = strtotime($checking_date);
                $current_date = date('Y-m-d h:i:sa');
                $modified_current_date = strtotime($current_date);
                //Is for Immediatly
                if (get_option('_rs_select_referral_points_referee_time') == '1') {
                    $limitation = true;
                } else {
                    // Is for Limited Time with Number of Days
                    if ($modified_current_date > $modified_checking_date) {
                        $limitation = true;
                    } else {
                        $limitation = false;
                    }
                }
                if ($limitation == true) {
                    $referreduser = get_user_by('login', $_COOKIE['rsreferredusername']);
                    if ($referreduser != false) {
                        $refuserid = $referreduser->ID;
                    } else {
                        $refuserid = $_COOKIE['rsreferredusername'];
                    }
                    $banning_type = FPRewardSystem::check_banning_type($refuserid);
                    if ($banning_type != 'earningonly' && $banning_type != 'both') {
                        $referral_registration_points = RSMemberFunction::user_role_based_reward_points($refuserid, $referral_registration_points);
                        $registration_points = RSMemberFunction::user_role_based_reward_points($user_id, $registration_points);
                        $mainpoints = array();
                        $mainpoints[$user_id] = array('userid' => $user_id, 'points' => $registration_points, 'refuserid' => $refuserid, 'refpoints' => $referral_registration_points);
                        RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($user_id, 'rs_get_data_for_reward_points', $mainpoints);
                        $previouslog = get_option('rs_referral_log');
                    }
                }
            }
        }

        /* Instant Registration Points */

        public static function rs_add_regpoints_to_user_instantly($user_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $registration_points = RSMemberFunction::user_role_based_reward_points($user_id, get_option('rs_reward_signup'));
            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
            $enabledisablepoints = get_option('rs_enable_disable_max_earning_points_for_user');
            $oldpoints = $wpdb->get_results("SELECT SUM((earnedpoints-usedpoints)) as availablepoints FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid=$user_id", ARRAY_A);
            $totaloldpoints = $oldpoints[0]['availablepoints'];
            $currentregistrationpoints = $registration_points;
            if ($enabledisablepoints == 'yes') {
                if (($currentregistrationpoints <= $restrictuserpoints) || ($restrictuserpoints == '')) {
                    $currentregistrationpoints = $currentregistrationpoints;
                } else {
                    $currentregistrationpoints = $restrictuserpoints;
                }
            }
            $date = rs_function_to_get_expiry_date_in_unixtimestamp();
            RSPointExpiry::insert_earning_points($user_id, $currentregistrationpoints, '0', $date, 'RRP', '', '', '', '');
            $equearnamt = RSPointExpiry::earning_conversion_settings($currentregistrationpoints);
            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_id);
            RSPointExpiry::record_the_points($user_id, $currentregistrationpoints, '0', $date, 'RRP', $equearnamt, '0', '0', '0', '0', '0', '', $totalpoints, '', '0');
            add_user_meta($user_id, '_points_awarded', '1');
        }

        /* After First Purchase Registration Points */

        public static function rs_add_regpoints_to_user_after_first_purchase($user_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $banning_type = FPRewardSystem::check_banning_type($user_id);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                $registration_points = RSMemberFunction::user_role_based_reward_points($user_id, get_option('rs_reward_signup'));
                $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                $enabledisablepoints = get_option('rs_enable_disable_max_earning_points_for_user');
                $oldpoints = $wpdb->get_results("SELECT SUM((earnedpoints-usedpoints)) as availablepoints FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid=$user_id", ARRAY_A);
                $totaloldpoints = $oldpoints[0]['availablepoints'];
                $currentregistrationpoints = $totaloldpoints + $registration_points;
                if ($enabledisablepoints == 'yes') {
                    if (($currentregistrationpoints <= $restrictuserpoints) || ($restrictuserpoints == '')) {
                        $currentregistrationpoints = $currentregistrationpoints;
                    } else {
                        $currentregistrationpoints = $restrictuserpoints;
                    }
                }
                $mainpoints = array();
                $mainpoints[$user_id] = array('userid' => $user_id, 'points' => $registration_points, 'refuserid' => '', 'refpoints' => '');
                RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($user_id, 'rs_get_data_for_reward_points', $mainpoints);
                if (get_option('rs_signup_points_with_purchase_points') == 'yes') {
                    RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($user_id, 'rs_restrict_points_when_when_first_purchase', '1');
                }
            }
        }

        /* Instant Referral Registration Points */

        public static function rs_add_regpoints_to_refuser_instantly($user_id) {
            if (isset($_COOKIE['rsreferredusername'])) {
                $user_info = new WP_User($user_id);
                $registered_date = $user_info->user_registered;
                $limitation = false;
                $modified_registered_date = date('Y-m-d h:i:sa', strtotime($registered_date));
                $delay_days = get_option('_rs_select_referral_points_referee_time_content');
                $checking_date = date('Y-m-d h:i:sa', strtotime($modified_registered_date . ' + ' . $delay_days . ' days '));
                $modified_checking_date = strtotime($checking_date);
                $current_date = date('Y-m-d h:i:sa');
                $modified_current_date = strtotime($current_date);
                $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                //Is for Immediatly
                if (get_option('_rs_select_referral_points_referee_time') == '1') {
                    $limitation = true;
                } else {
                    // Is for Limited Time with Number of Days
                    if ($modified_current_date > $modified_checking_date) {
                        $limitation = true;
                    } else {
                        $limitation = false;
                    }
                }
                if ($limitation == true) {
                    $referreduser = get_user_by('login', $_COOKIE['rsreferredusername']);
                    if ($referreduser != false) {
                        $refuserid = $referreduser->ID;
                    } else {
                        $refuserid = $_COOKIE['rsreferredusername'];
                    }
                    $banning_type = FPRewardSystem::check_banning_type($refuserid);
                    if ($banning_type != 'earningonly' && $banning_type != 'both') {
                        if (RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($user_id, 'rs_referrer_regpoints_awarded') != '1') {
                            $referral_registration_points = RSMemberFunction::user_role_based_reward_points($refuserid, get_option('rs_referral_reward_signup'));
                            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
                            if ($enabledisablemaxpoints == 'yes') {
                                $new_obj->check_point_restriction($restrictuserpoints, $referral_registration_points, $pointsredeemed = 0, $event_slug = 'RRRP', $user_id, $nomineeid = '', $refuserid, $productid = '', $variationid = '', $reasonindetail = '');
                            } else {
                                $equearnamt = RSPointExpiry::earning_conversion_settings($referral_registration_points);
                                $valuestoinsert = array('pointstoinsert' => $referral_registration_points, 'pointsredeemed' => 0, 'event_slug' => 'RRRP', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $refuserid, 'referred_id' => $user_id, 'product_id' => '', 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $referral_registration_points, 'totalredeempoints' => 0);
                                $new_obj->total_points_management($valuestoinsert);
                                $previouslog = get_option('rs_referral_log');
                                RS_Referral_Log::main_referral_log_function($refuserid, $user_id, $referral_registration_points, array_filter((array) $previouslog));
                                update_user_meta($user_id, '_rs_i_referred_by', $refuserid);
                            }
                            add_user_meta($user_id, 'rs_referrer_regpoints_awarded', '1');
                        }
                    }
                }
            }
        }

        public static function reward_points_for_login() {
            $strtotime = array();
            if (is_user_logged_in()) {
                if (get_option('rs_enable_reward_points_for_login') == 'yes') {
                    $userid = get_current_user_id();
                    $date = date('y-m-d');
                    $strtotime = strtotime($date);
                    $getusermeta = (array) RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($userid, 'rs_login_date');
                    if (!in_array($strtotime, $getusermeta)) {
                        $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                        $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                        $pointsforlogin = get_option('rs_reward_points_for_login');
                        $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
                        if ($enabledisablemaxpoints == 'yes') {
                            $new_obj->check_point_restriction($restrictuserpoints, $pointsforlogin, $pointsredeemed = 0, $event_slug = 'LRP', $userid, $nomineeid = '', $referrer_id = '', $productid = '', $variationid = '', $reasonindetail = '');
                        } else {
                            $equearnamt = RSPointExpiry::earning_conversion_settings($pointsforlogin);
                            $valuestoinsert = array('pointstoinsert' => $pointsforlogin, 'pointsredeemed' => 0, 'event_slug' => 'LRP', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $userid, 'referred_id' => '', 'product_id' => '', 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $pointsforlogin, 'totalredeempoints' => 0);
                            $new_obj->total_points_management($valuestoinsert);
                        }
                        $oldlogindata = (array) RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($userid, 'rs_login_date');
                        $newdata = (array) $strtotime;
                        $mergedata = array_merge($oldlogindata, $newdata);
                        RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($userid, 'rs_login_date', $mergedata);
                    }
                }
            }
            do_action('fp_reward_point_for_login');
        }

    }

    RSRegistrationPoints::init();
}