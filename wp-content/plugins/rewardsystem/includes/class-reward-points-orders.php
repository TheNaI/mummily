<?php

if (!class_exists('RewardPointsOrder')) {

    class RewardPointsOrder {

        public function __construct($order_id = 0, $apply_previous_order_points) {
            $this->order_id = $order_id;
            $this->order = new WC_Order($order_id);
            $this->apply_previous_order_points = $apply_previous_order_points;
        }

        public function check_point_restriction($restrictuserpoints, $getpaymentgatewayused, $pointsredeemed, $event_slug, $orderuserid, $nomineeid, $referrer_id, $productid, $variationid, $reasonindetail) {
            return self::check_point_restriction_of_user($restrictuserpoints, $getpaymentgatewayused, $pointsredeemed, $event_slug, $orderuserid, $nomineeid, $referrer_id, $productid, $variationid, $reasonindetail);
        }

        private function check_point_restriction_of_user($restrictuserpoints, $getpaymentgatewayused, $pointsredeemed, $event_slug, $orderuserid, $nomineeid, $referrer_id, $productid, $variationid, $reasonindetail) {
            $order = $this->order;
            $total_earned_points = $this->get_total_earned_points();
            $total_redeemed_points = $this->get_total_redeemed_points();
            $date = rs_function_to_get_expiry_date_in_unixtimestamp();
            if (($restrictuserpoints != '') && ($restrictuserpoints != '0')) {
                $getoldpoints = RSPointExpiry::get_sum_of_total_earned_points($orderuserid);
                if ($getoldpoints <= $restrictuserpoints) {
                    $totalpointss = $getoldpoints + $getpaymentgatewayused;
                    if ($totalpointss <= $restrictuserpoints) {
                        $equearnamt = RSPointExpiry::earning_conversion_settings($getpaymentgatewayused);
                        $valuestoinsert = array('pointstoinsert' => $getpaymentgatewayused, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $orderuserid, 'referred_id' => $referrer_id, 'product_id' => $productid, 'variation_id' => $variationid, 'reasonindetail' => $reasonindetail, 'nominee_id' => $nomineeid, 'nominee_points' => '', 'totalearnedpoints' => $getpaymentgatewayused, 'totalredeempoints' => 0);
                        $this->total_points_management($valuestoinsert);
                        if ($nomineeid != '') {
                            $totalpointss = RSPointExpiry::get_sum_of_total_earned_points($orderuserid);
                            RSPointExpiry::record_the_points($nomineeid, '0', '0', $date, 'PPRPFNP', '0', '0', '0', '0', '0', '', '', $totalpointss, $orderuserid, $getpaymentgatewayused);
                        }
                        if ($referrer_id != '' && $event_slug != 'PPRRPG') {
                            $previouslog = get_option('rs_referral_log');
                            RS_Referral_Log::main_referral_log_function($referrer_id, $orderuserid, $getpaymentgatewayused, array_filter((array) $previouslog));
                        }
                        if ($event_slug == 'RRRP') {
                            $previouslog = get_option('rs_referral_log');
                            RS_Referral_Log::main_referral_log_function($refuserid, $user_id, $getpaymentgatewayused, array_filter((array) $previouslog));
                            update_user_meta($user_id, '_rs_i_referred_by', $refuserid);
                        }
                        if ($event_slug == 'RPCPAR') {
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($orderuserid, 'usercommentpage' . $productid, '1');
                        }
                        if ($event_slug == 'RPCPR') {
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($orderuserid, 'usercommentpost' . $productid, '1');
                        }
                        if ($event_slug == 'RPPR') {
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($orderuserid, 'userreviewed' . $productid, '1');
                        }
                        if ($event_slug == 'RPGV') {
                            $rs_voucher_redeem_success_to_find = "[giftvoucherpoints]";
                            $rs_voucher_redeem_success_message = get_option('rs_voucher_redeem_success_message');
                            $rs_voucher_redeem_success_message_replaced = str_replace($rs_voucher_redeem_success_to_find, $getpaymentgatewayused, $rs_voucher_redeem_success_message);
                            echo addslashes($rs_voucher_redeem_success_message_replaced);
                        }
                    } else {
                        $insertpoints = $restrictuserpoints - $getoldpoints;
                        $event_slug = 'MREPFU';
                        $this->points_management($insertpoints, $pointsredeemed, $event_slug, $total_earned_points, $total_redeemed_points, $orderuserid);
                    }
                } else {
                    $earned_points = 0;
                    $event_slug = 'MREPFU';
                    $this->points_management($earned_points, $pointsredeemed, $event_slug, $total_earned_points, $total_redeemed_points, $orderuserid);
                }
            } else {
                $equearnamt = RSPointExpiry::earning_conversion_settings($getpaymentgatewayused);
                $valuestoinsert = array('pointstoinsert' => $getpaymentgatewayused, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $orderuserid, 'referred_id' => $referrer_id, 'product_id' => $productid, 'variation_id' => $variationid, 'reasonindetail' => '', 'nominee_id' => $nomineeid, 'nominee_points' => '', 'totalearnedpoints' => $getpaymentgatewayused, 'totalredeempoints' => 0);
                $this->total_points_management($valuestoinsert);
                if ($nomineeid != '') {
                    $totalpointss = RSPointExpiry::get_sum_of_total_earned_points($orderuserid);
                    RSPointExpiry::record_the_points($nomineeid, '0', '0', $date, 'PPRPFNP', '0', '0', '0', '0', '0', '', '', $totalpointss, $orderuserid, $getpaymentgatewayused);
                }
                if ($referrer_id != '' && $event_slug != 'PPRRPG') {
                    $previouslog = get_option('rs_referral_log');
                    RS_Referral_Log::main_referral_log_function($referrer_id, $orderuserid, $getpaymentgatewayused, array_filter((array) $previouslog));
                }
                if ($event_slug == 'RRRP') {
                    $previouslog = get_option('rs_referral_log');
                    RS_Referral_Log::main_referral_log_function($referrer_id, $orderuserid, $getpaymentgatewayused, array_filter((array) $previouslog));
                    update_user_meta($orderuserid, '_rs_i_referred_by', $referrer_id);
                }
                if ($event_slug == 'RPCPAR') {
                    RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($orderuserid, 'usercommentpage' . $productid, '1');
                }
                if ($event_slug == 'RPCPR') {
                    RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($orderuserid, 'usercommentpost' . $productid, '1');
                }
                if ($event_slug == 'RPPR') {
                    RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($orderuserid, 'userreviewed' . $productid, '1');
                }
                if ($event_slug == 'RPGV') {
                    $rs_voucher_redeem_success_to_find = "[giftvoucherpoints]";
                    $rs_voucher_redeem_success_message = get_option('rs_voucher_redeem_success_message');
                    $rs_voucher_redeem_success_message_replaced = str_replace($rs_voucher_redeem_success_to_find, $getpaymentgatewayused, $rs_voucher_redeem_success_message);
                    echo addslashes($rs_voucher_redeem_success_message_replaced);
                }
            }
        }

        private function get_total_earned_points() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $gettotalearnedpoints = $wpdb->get_results("SELECT SUM((earnedpoints)) as availablepoints FROM $table_name WHERE orderid = $this->order_id", ARRAY_A);
            $totalearnedpoints = ($gettotalearnedpoints[0]['availablepoints'] != NULL) ? $gettotalearnedpoints[0]['availablepoints'] : 0;
            return $totalearnedpoints;
        }

        private function get_total_redeemed_points() {
            $total_redeemed_points = 0;
            return $total_redeemed_points;
        }

        public function update_earning_points_for_user() {
            global $wpdb;
            global $woocommerce;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $table_name2 = $wpdb->prefix . 'rsrecordpoints';
            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
            $order = $this->order;
            $orderuserid = rs_get_order_obj($order);
            $payment_method = $orderuserid['payment_method'];
            $orderuserid = $orderuserid['order_userid'];
            if ($this->check_restriction() && $this->award_earning_point_only_once() && $this->is_user_banned()) {
                $fp_earned_points_sms = false;
                $order_id = $this->order_id;
                do_action('rs_perform_action_for_order', $order_id);
                $redeempoints = '0';
                $pointsredeemed = '0';
                $checkredeeming = $this->check_redeeming_in_order();
                $enableoption = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, 'rs_check_enable_option_for_redeeming');
                /* Reward Points For Using Payment Gateway Method - Start */
                $getpaymentgatewayused = rs_function_to_get_gateway_point($order_id, $orderuserid, $payment_method);
                if ($enableoption == 'yes' && $checkredeeming == false) {
                    if ($getpaymentgatewayused != '') {
                        $event_slug = 'RPG';
                        if ($enabledisablemaxpoints == 'yes') {
                            $this->check_point_restriction($restrictuserpoints, $getpaymentgatewayused, $pointsredeemed, $event_slug, $orderuserid, $nomineeid = '', $referrer_id = '', $productid = '', $variationid = '', $reasonindetail);
                        } else {
                            $equearnamt = RSPointExpiry::earning_conversion_settings($getpaymentgatewayused);
                            $valuestoinsert = array('pointstoinsert' => $getpaymentgatewayused, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $orderuserid, 'referred_id' => '', 'product_id' => '', 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $getpaymentgatewayused, 'totalredeempoints' => 0);
                            $this->total_points_management($valuestoinsert);
                        }
                    }
                } else {
                    if ($getpaymentgatewayused != '') {
                        $event_slug = 'RPG';
                        if ($enabledisablemaxpoints == 'yes') {
                            $this->check_point_restriction($restrictuserpoints, $getpaymentgatewayused, $pointsredeemed, $event_slug, $orderuserid, $nomineeid = '', $referrer_id = '', $productid = '', $variationid = '', $reasonindetail);
                        } else {
                            $equearnamt = RSPointExpiry::earning_conversion_settings($getpaymentgatewayused);
                            $valuestoinsert = array('pointstoinsert' => $getpaymentgatewayused, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $orderuserid, 'referred_id' => '', 'product_id' => '', 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $getpaymentgatewayused, 'totalredeempoints' => 0);
                            $this->total_points_management($valuestoinsert);
                        }
                    }
                }
                do_action('fp_reward_point_for_using_gateways');
                /* Reward Points For Using Payment Gateway Method - End */

                /* Reward Points For Purchasing the Product - Start */
                $award_points_for_renewal_order = rs_function_to_provide_points_for_renewal_order($order_id);
                if ($award_points_for_renewal_order == true && $checkredeeming == false) {
                    $points_refer = array();
                    foreach ($order->get_items() as $item) {
                        $checkproduct = rs_get_product_object($item['product_id']);
                        if (is_object($checkproduct) && ($checkproduct->is_type('simple') || ($checkproduct->is_type('subscription')) || ($checkproduct->is_type('booking')))) {
                            $productid = $item['product_id'];
                            $variationid = '0';
                        } else {
                            $productid = $item['product_id'];
                            $variationid = $item['variation_id'];
                        }
                        $itemquantity = $item['qty'];
                        $this->rs_insert_the_selected_level_in_reward_points($restrictuserpoints, $enabledisablemaxpoints, $pointsredeemed, $productid, $variationid, $itemquantity, $orderuserid, $equearnamt = '', $equredeemamt = '', $order_id, $item, $reasonindetail = '');
                        $gettotalearnedpoints = $wpdb->get_results("SELECT SUM((earnedpoints)) as availablepoints FROM $table_name WHERE orderid = $order_id", ARRAY_A);
                        $totalearnedpoints = ($gettotalearnedpoints[0]['availablepoints'] != NULL) ? $gettotalearnedpoints[0]['availablepoints'] : 0;
                        $totalredeempoints = ($redeempoints != null) ? $redeempoints : 0;
                        $wpdb->query("UPDATE $table_name SET totalearnedpoints = $totalearnedpoints WHERE orderid = $order_id");
                        $wpdb->query("UPDATE $table_name SET totalredeempoints = $totalredeempoints WHERE orderid = $order_id");

                        /* Referral Reward Points For Purchasing the Product - Start */
                        $award_referral_points_for_renewal_order = rs_function_to_provide_referral_points_for_renewal_order($order_id);
                        if ($award_referral_points_for_renewal_order == true) {
                            $referreduser = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, '_referrer_name');
                            if ($referreduser != '') {
                                $points_refer[$productid] = $this->rs_insert_the_selected_level_in_referral_reward_points($enabledisablemaxpoints, $referreduser, $orderuserid, $restrictuserpoints, $productid, $variationid, $item, $getting_referrer = 'yes');
                                $this->rs_insert_the_selected_level_in_referral_reward_points($enabledisablemaxpoints, $referreduser, $orderuserid, $restrictuserpoints, $productid, $variationid, $item, $getting_referrer = 'no');
                            } else {
                                $referrer_id = rs_perform_manual_link_referer($orderuserid);
                                if ($referrer_id != false) {
                                    $this->rs_insert_the_selected_level_in_referral_reward_points($enabledisablemaxpoints, $referrer_id, $orderuserid, $restrictuserpoints, $productid, $variationid, $item, $getting_referrer = 'no');
                                }
                            }
                        }
                        /* Referral Reward Points For Purchasing the Product - End */
                    }
                    if ($this->apply_previous_order_points == 'no') {
                        apply_coupon_code_reward_points_user($order_id);
                        RSFunctionToApplyCoupon::update_redeem_reward_points_to_user($order_id, $orderuserid);
                        $gettotalearnedpoints = $wpdb->get_results("SELECT SUM((earnedpoints)) as availablepoints FROM $table_name WHERE orderid = $order_id", ARRAY_A);
                        $totalearnedpoints = ($gettotalearnedpoints[0]['availablepoints'] != NULL) ? $gettotalearnedpoints[0]['availablepoints'] : 0;
                        if ($totalearnedpoints != 0 && $totalearnedpoints != '') {
                            if (get_option('rs_send_sms_earning_points') == 'yes') {
                                $fp_earned_points_sms = true;
                            }
                        }
                        if ($fp_earned_points_sms == true) {
                            if (get_option('rs_enable_send_sms_to_user') == 'yes') {
                                if (get_option('rs_send_sms_earning_points') == 'yes') {
                                    if (get_option('rs_sms_sending_api_option') == '1') {
                                        RSFunctionForSms::send_sms_twilio_api($order_id);
                                    } else {
                                        RSFunctionForSms::send_sms_nexmo_api($order_id);
                                    }
                                }
                            }
                        }

                        update_post_meta($order_id, 'rsgetreferalpoints', $points_refer);
                        update_user_meta($orderuserid, 'rsfirsttime_redeemed', 1);
                        $return = RSPointExpiry::check_weather_the_points_is_awarded_for_order($order_id);
                        $return = array();
                        if (is_array($return)) {
                            if (in_array(1, $return)) {
                                add_post_meta($order_id, 'reward_points_awarded', 'yes');
                            }
                        }
                        rsmail_sending_on_custom_rule($orderuserid, $order_id);
                        do_action('fp_reward_point_for_product_purchase');
                    }
                }
                if ($this->apply_previous_order_points == 'no') {
                    $oldorderid = get_user_meta($orderuserid, 'rs_no_of_purchase_for_user', true);
                    $getorderid = (array) $order_id;
                    if ($oldorderid == '') {
                        update_user_meta($orderuserid, 'rs_no_of_purchase_for_user', $getorderid);
                    } else {
                        $mergearray = array_merge($oldorderid, (array) $getorderid);
                        update_user_meta($orderuserid, 'rs_no_of_purchase_for_user', $mergearray);
                    }
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($order_id, 'rs_revised_points_once', 2);
                    update_post_meta($order_id, 'earning_point_once', 1);
                }
                /* Reward Points For Purchasing the Product - End */
            }
            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($orderuserid, 'rs_restrict_points_when_when_first_purchase', '2');
        }

        public function rs_insert_the_selected_level_in_reward_points($restrictuserpoints, $enabledisablemaxpoints, $pointsredeemed, $productid, $variationid, $itemquantity, $orderuserid, $equearnamt, $equredeemamt, $order_id, $item, $reasonindetail = '') {
            $totalearnedpoints = '0';
            $totalredeempoints = '0';
            $enable = get_option('rs_enable_disable_reward_point_based_coupon_amount');
            $getnomineeidinmyaccount = get_user_meta($orderuserid, 'rs_selected_nominee', true);
            $enablenomineeidinmyaccount = get_user_meta($orderuserid, 'rs_enable_nominee', true);
            $getnomineeidincheckout = get_post_meta($order_id, 'rs_selected_nominee_in_checkout', true);
            $restrictpoints = rs_function_to_restrict_points_for_product_which_has_saleprice($productid, $variationid);
            if ($restrictpoints == 'no') {
                if ($enable == 'yes') {
                    $coupon_used = array();
                    $order_object = $this->order;
                    $coupon_used = $order_object->get_used_coupons();
                    if (is_array($coupon_used)) {
                        if (!empty($coupon_used)) {
                            $productidss = $variationid == '0' || '' ? $productid : $variationid;
                            $modified_point_list = get_post_meta($order_id, 'points_for_current_order', true);
                            $productlevelrewardpointss = $modified_point_list[$productidss];
                            $order = $this->order;
                            $order_total = $order->get_total();
                            $minimum_cart_total = get_option('rs_minimum_cart_total_for_earning');
                            $maximum_cart_total = get_option('rs_maximum_cart_total_for_earning');
                            if ($minimum_cart_total != '' && $minimum_cart_total != 0) {
                                if ($order_total < $minimum_cart_total) {
                                    $productlevelrewardpointss = 0;
                                }
                            }
                            if ($maximum_cart_total != '' && $maximum_cart_total != 0) {
                                if ($order_total > $maximum_cart_total) {
                                    $productlevelrewardpointss = 0;
                                }
                            }
                            if ($productlevelrewardpointss != '0') {
                                include ('frontend/rs_insert_points_for_product_purchase.php');
                            }
                        } else {
                            $order = $this->order;
                            $order_total = $order->get_total();
                            $minimum_cart_total = get_option('rs_minimum_cart_total_for_earning');
                            $checklevel = "no";
                            $productlevelrewardpointss = check_level_of_enable_reward_point($productid, $variationid, $item, $checklevel, $referrer_id = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
                            if ($minimum_cart_total != '' && $minimum_cart_total != 0) {
                                if ($order_total < $minimum_cart_total) {

                                    $productlevelrewardpoints = 0;
                                    $global_rewardpercent = 0;
                                    $global_rewardpoints = 0;
                                    $categorylevelrewardpercent = 0;
                                    $categorylevelrewardpoints = 0;
                                    $productlevelrewardpercent = 0;
                                }
                            }
                            $maximum_cart_total = get_option('rs_maximum_cart_total_for_earning');
                            if ($maximum_cart_total != '' && $maximum_cart_total != 0) {
                                if ($order_total > $maximum_cart_total) {

                                    $productlevelrewardpoints = 0;
                                    $global_rewardpercent = 0;
                                    $global_rewardpoints = 0;
                                    $categorylevelrewardpercent = 0;
                                    $categorylevelrewardpoints = 0;
                                    $productlevelrewardpercent = 0;
                                }
                            }
                            include ('frontend/rs_insert_points_for_product_purchase.php');
                        }
                    }
                } else {
                    $order = new $this->order;
                    $order_total = $order->get_total();
                    $minimum_cart_total = get_option('rs_minimum_cart_total_for_earning');
                    $checklevel = "no";
                    $productlevelrewardpointss = check_level_of_enable_reward_point($productid, $variationid, $item, $checklevel, $referrer_id = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
                    if ($minimum_cart_total != '' && $minimum_cart_total != 0) {
                        if ($order_total < $minimum_cart_total) {
                            $productlevelrewardpoints = 0;
                            $global_rewardpercent = 0;
                            $global_rewardpoints = 0;
                            $categorylevelrewardpercent = 0;
                            $categorylevelrewardpoints = 0;
                            $productlevelrewardpercent = 0;
                        }
                    }

                    $maximum_cart_total = get_option('rs_maximum_cart_total_for_earning');
                    if ($maximum_cart_total != '' && $maximum_cart_total != 0) {
                        if ($order_total > $maximum_cart_total) {
                            $productlevelrewardpoints = 0;
                            $global_rewardpercent = 0;
                            $global_rewardpoints = 0;
                            $categorylevelrewardpercent = 0;
                            $categorylevelrewardpoints = 0;
                            $productlevelrewardpercent = 0;
                        }
                    }

                    include ('frontend/rs_insert_points_for_product_purchase.php');
                }
            }
        }

        public function rs_insert_the_selected_level_in_referral_reward_points($enabledisablemaxpoints, $referrer_id, $orderuserid, $restrictuserpoints, $productid, $variationid, $item, $getting_referrer) {

            //User Info who placed the order
            $user_info = new WP_User($orderuserid);
            $registered_date = $user_info->user_registered;
            $strtotimeregdate = strtotime($registered_date);
            $limitation = false;
            //User Info who referred the user to place the order
            $refuser_info = new WP_User($referrer_id);
            $refregistered_date = $refuser_info->user_registered;
            $strtotimerefregdate = strtotime($refregistered_date);
            $modified_registered_date = date('Y-m-d h:i:sa', strtotime($registered_date));
            $delay_days = get_option('_rs_select_referral_points_referee_time_content');
            $checking_date = date('Y-m-d h:i:sa', strtotime($modified_registered_date . ' + ' . $delay_days . ' days '));
            $modified_checking_date = strtotime($checking_date);
            $current_date = date('Y-m-d h:i:sa');
            $modified_current_date = strtotime($current_date);
            $pointsredeemed = 0;
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
            if ($limitation == true && ($strtotimeregdate > $strtotimerefregdate)) {
                $refuser = get_user_by('login', $referrer_id);
                if ($refuser != false) {
                    $myid = $refuser->ID;
                } else {
                    $myid = $referrer_id;
                }
                $checklevel = "no";
                if ($getting_referrer == 'no') {
                    $event_slug = 'PPRRP';
                    $pointstoinsert = check_level_of_enable_reward_point($productid, $variationid, $item, $checklevel, $myid, $getting_referrer, $socialreward = 'no', $rewardfor = '');
                } else {
                    $event_slug = 'PPRRPG';
                    $pointstoinsert = check_level_of_enable_reward_point($productid, $variationid, $item, $checklevel, $myid, $getting_referrer, $socialreward = 'no', $rewardfor = '');
                }
                if ($enabledisablemaxpoints == 'yes') {
                    $this->check_point_restriction($restrictuserpoints, $pointstoinsert, $pointsredeemed, $event_slug, $orderuserid, $nomineeid = '', $myid, $productid, $variationid, $reasonindetail);
                } else {
                    $equearnamt = RSPointExpiry::earning_conversion_settings($pointstoinsert);
                    if ($event_slug == 'PPRRP') {
                        $valuestoinsert = array('pointstoinsert' => $pointstoinsert, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $myid, 'referred_id' => $orderuserid, 'product_id' => $productid, 'variation_id' => $variationid, 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $pointstoinsert, 'totalredeempoints' => 0);
                    } else if ($event_slug == 'PPRRPG') {
                        $valuestoinsert = array('pointstoinsert' => $pointstoinsert, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $orderuserid, 'referred_id' => $myid, 'product_id' => $productid, 'variation_id' => $variationid, 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $pointstoinsert, 'totalredeempoints' => 0);
                    }
                    $this->total_points_management($valuestoinsert);
                    if ($event_slug != 'PPRRPG') {
                        $previouslog = get_option('rs_referral_log');
                        RS_Referral_Log::main_referral_log_function($myid, $orderuserid, $pointstoinsert, array_filter((array) $previouslog));
                    }
                }
                if ($getting_referrer == 'yes') {
                    return $pointstoinsert;
                }
            }
        }

        public function insert_points_for_product($enabledisablemaxpoints, $order_id, $orderuserid, $nomineeid, $productlevelrewardpointss, $productid, $variationid) {
            if ($enabledisablemaxpoints == 'yes') {
                $event_slug = 'PPRPFN';
                $this->check_point_restriction($restrictuserpoints, $productlevelrewardpointss, $pointsredeemed = 0, $event_slug, $orderuserid, $nomineeid, $referrer_id = '', $reasonindetail);
            } else {
                $event_slug = 'PPRPFN';
                $equearnamt = RSPointExpiry::earning_conversion_settings($productlevelrewardpointss);
                $date = rs_function_to_get_expiry_date_in_unixtimestamp();
                $valuestoinsert = array('pointstoinsert' => $productlevelrewardpointss, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $orderuserid, 'referred_id' => '', 'product_id' => $productid, 'variation_id' => $variationid, 'reasonindetail' => '', 'nominee_id' => $nomineeid, 'nominee_points' => $productlevelrewardpointss, 'totalearnedpoints' => $productlevelrewardpointss, 'totalredeempoints' => 0);
                $this->total_points_management($valuestoinsert);
                $totalpointss = RSPointExpiry::get_sum_of_total_earned_points($nomineeid);
                RSPointExpiry::record_the_points($nomineeid, '0', '0', $date, 'PPRPFNP', '0', '0', '0', '0', '0', '', '', $totalpointss, $orderuserid, $productlevelrewardpointss);
            }
        }

        public function check_restriction() {
            $order = $this->order;
            $order_user_id = rs_get_order_obj($order);
            $order_user_id = $order_user_id['order_userid'];
            $get_restrict = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($order_user_id, 'rs_restrict_points_when_when_first_purchase', true);
            $get_restrict = $get_restrict != '1' ? true : false;
            return $get_restrict;
        }

        public function award_earning_point_only_once() {
            $earningpointonce = get_post_meta($this->order_id, 'earning_point_once', true);
            $earningpointonce = $earningpointonce != '1' ? true : false;
            return $earningpointonce;
        }

        public function is_user_banned() {
            $order = $this->order;
            $order_user_id = rs_get_order_obj($order);
            $order_user_id = $order_user_id['order_userid'];
            $banning_type = FPRewardSystem::check_banning_type($order_user_id);
            $ban = ($banning_type != 'earningonly' && $banning_type != 'both') ? true : false;
            return $ban;
        }

        public function check_redeeming_in_order() {
            $order_id = $this->order_id;
            $order = $this->order;
            $user_id = rs_get_order_obj($order);
            $user_id = $user_id['order_userid'];
            $rewardpointscoupons = $order->get_items(array('coupon'));
            $getuserdatabyid = get_user_by('id', $user_id);
            $getusernickname = isset($getuserdatabyid->user_login) ? $getuserdatabyid->user_login : "";
            $maincouponchecker = 'sumo_' . strtolower($getusernickname);
            $auto_redeem_name = 'auto_redeem_' . strtolower($getusernickname);
            $getcouponoption = get_option('rs_disable_point_if_coupon');
            $getredeemoption = get_option('rs_enable_redeem_for_order');
            $reward_gateway_check = get_option('rs_disable_point_if_reward_points_gateway');
            $payment_method = get_post_meta($order_id, '_payment_method', true);
            if ($reward_gateway_check == 'yes' && $payment_method == 'reward_gateway') {
                return true;
            }
            if ($getcouponoption == 'yes') {
                if (!empty($rewardpointscoupons)) {
                    foreach ($rewardpointscoupons as $array) {
                        if (!in_array($maincouponchecker, $array) || !in_array($auto_redeem_name, $array)) {
                            return true;
                        }
                    }
                }
            }
            if ($getredeemoption == 'yes') {
                if (!empty($rewardpointscoupons)) {
                    foreach ($rewardpointscoupons as $array) {
                        if (in_array($maincouponchecker, $array) || in_array($auto_redeem_name, $array)) {
                            return true;
                        }
                    }
                }
            }
            if (get_option('_rs_not_allow_earn_points_if_sumo_coupon') == 'yes') {
                foreach ($rewardpointscoupons as $couponcode => $value) {
                    $coupon_id_array = new WC_Coupon($value['name']);
                    $coupon_id = rs_get_coupon_obj($coupon_id_array);
                    $coupon_id = $coupon_id['coupon_id'];
                    $sumo_coupon_check = get_post_meta($coupon_id, 'sumo_coupon_check', true);
                    if ($sumo_coupon_check == 'yes') {
                        return true;
                    }
                }
            }
            return false;
        }

        public function total_points_management($valuestoinsert) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $date = rs_function_to_get_expiry_date_in_unixtimestamp();
            $order_id = $this->order_id;
            $user_id = $valuestoinsert['user_id'];
            if (isset($valuestoinsert['manualaddpoints'])) {
                if ($valuestoinsert['expireddate'] != '') {
                    $date = $valuestoinsert['expireddate'];
                } else {
                    $date = '999999999999';
                }
            }
            $pointstonsert = RSMemberFunction::user_role_based_reward_points($user_id, $valuestoinsert['pointstoinsert']);
            RSPointExpiry::insert_earning_points($user_id, $pointstonsert, $valuestoinsert['pointsredeemed'], $date, $valuestoinsert['event_slug'], $order_id, $valuestoinsert['totalearnedpoints'], $valuestoinsert['totalredeempoints'], $valuestoinsert['reasonindetail']);
            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_id);
            RSPointExpiry::record_the_points($user_id, $pointstonsert, $valuestoinsert['pointsredeemed'], $date, $valuestoinsert['event_slug'], $valuestoinsert['equalearnamnt'], $valuestoinsert['equalredeemamnt'], $order_id, $valuestoinsert['product_id'], $valuestoinsert['variation_id'], $valuestoinsert['referred_id'], $valuestoinsert['reasonindetail'], $totalpoints, $valuestoinsert['nominee_id'], $valuestoinsert['nominee_points']);
            $totalearnedpoints = $this->get_total_earned_points();
            $totalredeempoints = $this->get_total_redeemed_points();
            $wpdb->query("UPDATE $table_name SET totalearnedpoints = $totalearnedpoints,totalredeempoints = $totalredeempoints WHERE orderid = $order_id");
        }

        public function points_management($earned_points, $redeemed_points, $event_slug, $total_earned_points = 0, $total_redeemed_points = 0, $user_id) {
            $order_id = $this->order_id;
            $date = rs_function_to_get_expiry_date_in_unixtimestamp();
            $earned_points = RSMemberFunction::user_role_based_reward_points($user_id, $earned_points);
            RSPointExpiry::insert_earning_points($user_id, $earned_points, $redeemed_points, $date, $event_slug, $order_id, $total_earned_points, $total_redeemed_points, '');
            $equearnamt = $earned_points != 0 ? RSPointExpiry::earning_conversion_settings($earned_points) : 0;
            $equredeemamt = $redeemed_points != 0 ? RSPointExpiry::redeeming_conversion_settings($redeemed_points) : 0;
            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_id);
            RSPointExpiry::record_the_points($user_id, $earned_points, $redeemed_points, $date, $event_slug, $equearnamt, $equredeemamt, $order_id, '0', '0', '0', '', $totalpoints, '', '0');
        }

    }

}