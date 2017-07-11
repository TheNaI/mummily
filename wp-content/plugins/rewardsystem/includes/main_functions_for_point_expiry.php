<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSPointExpiry')) {

    class RSPointExpiry {

        public static function init() {
            global $woocommerce;
            if (is_user_logged_in()) {
                $order_status = get_option('rs_order_status_after_gateway_purchase');
                add_action('woocommerce_order_status_' . $order_status, array(__CLASS__, 'redeem_for_reward_gateway'), 1);

                $orderstatuslist = get_option('rs_order_status_control');
                if (is_array($orderstatuslist)) {
                    foreach ($orderstatuslist as $value) {
                        add_action('woocommerce_order_status_' . $value, array(__CLASS__, 'update_earning_points_for_user'), 1);
//                        add_action('woocommerce_thankyou', array(__CLASS__, 'update_earning_points_for_user'), 1);
                    }
                }

                $orderstatuslistforredeem = get_option('rs_order_status_control_redeem');
                if (is_array($orderstatuslistforredeem)) {
                    foreach ($orderstatuslistforredeem as $value) {

                        add_action('woocommerce_thankyou', array(__CLASS__, 'update_redeem_point_for_user_third_party_sites'), 1);

                        add_action('woocommerce_order_status_' . $value, array(__CLASS__, 'update_redeem_point_for_user'), 1);
                    }
                }


                $order_status_control = get_option('rs_list_other_status_for_redeem');
                if (get_option('rs_list_other_status_for_redeem') != '') {
                    foreach ($order_status_control as $order_status) {
                        $orderstatuslist = get_option('rs_order_status_control_redeem');
                        if (is_array($orderstatuslist)) {
                            foreach ($orderstatuslist as $value) {
                                if ($value != 'pending') {
                                    add_action('woocommerce_order_status_' . $value . '_to_' . $order_status, array(__CLASS__, 'update_revised_redeem_points_for_user'));
                                }
                                if (in_array('pending', $orderstatuslist)) {
                                    if (is_admin()) {
                                        add_action('woocommerce_order_status_pending' . '_to_' . $order_status, array(__CLASS__, 'update_revised_redeem_points_for_user'));
                                    }
                                }
                            }
                        }
                    }
                }

                $order_status_control = get_option('rs_list_other_status');
                if (get_option('rs_list_other_status') != '') {
                    foreach ($order_status_control as $order_status) {
                        $orderstatuslist = get_option('rs_order_status_control');
                        if (is_array($orderstatuslist)) {
                            foreach ($orderstatuslist as $value) {
                                add_action('woocommerce_order_status_' . $value . '_to_' . $order_status, array(__CLASS__, 'update_revised_points_for_user'));
                            }
                        }
                    }
                }

                add_action('wp_head', array(__CLASS__, 'check_if_expiry'));

                add_action('wp_head', array(__CLASS__, 'get_sum_of_total_earned_points'));

                add_action('wp_head', array(__CLASS__, 'delete_if_used'));

                add_action('comment_post', array(__CLASS__, 'get_reviewed_user_list'), 10, 2);

                if (get_option('rs_review_reward_status') == '1') {
                    add_action('comment_unapproved_to_approved', array(__CLASS__, 'getcommentstatus'), 10, 1);
                }
                if (get_option('rs_review_reward_status') == '2') {
                    add_action('comment_unapproved', array(__CLASS__, 'getcommentstatus'), 10, 1);
                }

                add_action('woocommerce_update_options_rewardsystem_status', array(__CLASS__, 'rewards_rs_order_status_control'), 99);

                add_action('admin_init', array(__CLASS__, 'rewards_rs_order_status_control'), 9999);

                add_action('delete_user', array(__CLASS__, 'delete_referral_registered_people'));

                add_shortcode('rs_my_reward_points', array(__CLASS__, 'myrewardpoints_total_shortcode'));

                add_shortcode('rs_generate_static_referral', array(__CLASS__, 'shortcode_for_static_referral_link'));

                add_action('woocommerce_checkout_update_order_meta', array(__CLASS__, 'check_redeeming_in_order'), 10, 2);

                add_action('comment_unapproved_to_approved', array(__CLASS__, 'getcommentstatus_post'), 10, 1);

                add_action('comment_unapproved', array(__CLASS__, 'getcommentstatus_post'), 10, 1);

                add_action('comment_post', array(__CLASS__, 'get_post_comment_user_list'), 10, 2);

                add_action('comment_post', array(__CLASS__, 'get_page_comment_user_list'), 10, 2);

                add_action('woocommerce_checkout_update_order_meta', array(__CLASS__, 'reward_points_for_product_review_after_purchase'), 10, 2);
            }
            add_shortcode('rs_generate_referral', array(__CLASS__, 'rs_fp_rewardsystem'));

            add_action('woocommerce_process_shop_order_meta', array(__CLASS__, 'save_manuall_order'), 50, 2);
        }

        public static function save_manuall_order($order_id, $post) {
            if (get_post_meta($order_id, 'frontendorder', true) != '1') {
                global $woocommerce;
                $couponamount1 = array();
                $array = array();
                $linetotal = array();
                $order = new WC_Order($order_id);
                update_post_meta($order_id, 'pointsvalue', '1');
                foreach ($order->get_items()as $item) {
                    $product_id = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
                    $enable = calculate_point_price_for_products($product_id);
                    if ($enable[$product_id] != '') {
                        $cart_object = $enable[$product_id] * $item['qty'];
                        $array[] = $cart_object;
                    } else {
                        $linetotal[] = $item['line_subtotal'];
                    }
                    if (get_option('woocommerce_prices_include_tax') === 'yes') {
                        $shipping_total = $order->get_total_shipping();
                        $tax_total = 0;
                    } else {
                        $shipping_total = $order->get_total_shipping();
                        $tax_total = $order->get_total_tax();
                    }
                }
                $totalrewardpointprice = array_sum($array);
                $totalbalancepoints = array_sum($linetotal);
                $totalbalancepoints = $tax_total + $shipping_total + $totalbalancepoints;
                $newvalue = $totalbalancepoints / wc_format_decimal(get_option('rs_redeem_point_value'));
                $updatedvalue = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
                $rewardpointscoupons = $order->get_items(array('coupon'));
                $order_user_id = rs_get_order_obj($order);
                $order_user_id = $order_user_id['order_userid'];
                $getuserdatabyid = get_user_by('id', $order_user_id);
                $getusernickname = is_object($getuserdatabyid) ? $getuserdatabyid->user_login : 'Guest';
                $auto_redeem_name = 'auto_redeem_' . strtolower($getusernickname);
                $maincouponchecker = 'sumo_' . strtolower($getusernickname);
                foreach ($rewardpointscoupons as $coupon) {
                    if ($auto_redeem_name == $coupon['name'] || $maincouponchecker == $coupon['name']) {
                        $couponamount1[] = $coupon['discount_amount'];
                    }
                }
                $couponamount = array_sum($couponamount1);
                $redeemedpoints = $totalrewardpointprice + $updatedvalue;
                $redeemedpoints = $redeemedpoints - $couponamount;
                $getmaxoption = get_option('rs_max_redeem_discount_for_sumo_reward_points');
                $ordertotal = $order->get_total();
                foreach ($order->get_items() as $item) {
                    $productid = $productid = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
                    $checklevel = 'no';
                    $reward_pointsss[$productid] = check_level_of_enable_reward_point($item['product_id'], $item['variation_id'], $item, $checklevel, $referred_user = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
                }
                update_post_meta($order_id, 'points_for_current_order', $reward_pointsss);
                $points_for_current_order_in_value = array_sum($reward_pointsss);
                update_post_meta($order_id, 'rs_points_for_current_order_as_value', $points_for_current_order_in_value);
                if (get_option('rs_gateway_for_manual_order') == '1') {
                    if ($ordertotal >= $getmaxoption) {
                        $rewardgateway = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, '_payment_method', true);
                        update_post_meta($order_id, 'total_redeem_points_for_order_point_price', $redeemedpoints);
                        update_option('gateway', $rewardgateway);
                        if ($rewardgateway == 'reward_gateway') {
                            $gateway_used = get_post_meta($order_id, 'manuall_order', true);
                            $date = '999999999999';
                            if ($gateway_used != '1') {
                                self::perform_calculation_with_expiry($redeemedpoints, $order_user_id);
                                $equredeemamt = RSPointExpiry::redeeming_conversion_settings($redeemedpoints);
                                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($order_user_id);
                                if ($totalpoints >= 0 && $totalpoints >= $redeemedpoints) {
                                    RSPointExpiry::record_the_points($order_user_id, '0', $redeemedpoints, $date, 'RPFGW', '0', $equredeemamt, $order_id, '0', '0', '0', '', $totalpoints, '', '0');
                                    update_post_meta($order_id, 'manuall_order', 1);
                                    update_post_meta($order_id, 'refund_gateway', 2);
                                    update_post_meta($order_id, 'second_time_gateway', 2);
                                }
                            }
                        }
                    }
                }
            }
        }

        public static function redeem_for_reward_gateway($order_id) {
            $getmaxoption = get_option('rs_max_redeem_discount_for_sumo_reward_points');
            $order = new WC_Order($order_id);
            $order_user_id = rs_get_order_obj($order);
            $order_user_id = $order_user_id['order_userid'];
            $ordertotal = $order->get_total();
            if ($ordertotal > $getmaxoption) {
                $rewardgateway = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, '_payment_method', true);
                if ($rewardgateway == 'reward_gateway') {
                    $gateway_used = get_post_meta($order_id, 'sumo_gateway_used', true);
                    if ($gateway_used != '1') {
                        $date = '999999999999';
                        $total_redeem = get_post_meta($order_id, 'total_redeem_points_for_order_point_price', true);
                        $equredeemamt = RSPointExpiry::redeeming_conversion_settings($total_redeem);
                        $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($order_user_id);
                        if ($totalpoints >= 0) {
                            self::perform_calculation_with_expiry($total_redeem, $order_user_id);
                            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($order_user_id);
                            RSPointExpiry::record_the_points($order_user_id, '0', $total_redeem, $date, 'RPFGW', '0', $equredeemamt, $order_id, '0', '0', '0', '', $totalpoints, '', '0');
                            do_action('fp_redeem_reward_points_using_rewardgateway', $order_id, $total_redeem);
                            update_post_meta($order_id, 'sumo_gateway_used', 1);
                            update_post_meta($order_id, 'redeem_point_once', 1);
                        }
                    }
                }
            }
        }

        public static function reward_points_for_product_review_after_purchase($orderid, $order_user_id) {
            $order = new WC_Order($orderid);
            $userid = rs_get_order_obj($order);
            $userid = $userid['order_userid'];
            foreach ($order->get_items() as $eachitem) {
                $product_id = $eachitem['variation_id'] != '0' ? $eachitem['variation_id'] : $eachitem['product_id'];
                $getproductid = (array) get_post_meta($userid, 'product_id_for_product_review_meta1', true);
                if ($getproductid == '') {
                    update_post_meta($userid, 'product_id_for_product_review_meta1', $product_id);
                } else {
                    $arraymerge = array_merge((array) $getproductid, (array) $product_id);
                    update_post_meta($userid, 'product_id_for_product_review_meta1', $arraymerge);
                }
            }
        }

        public static function update_redeem_point_for_user($order_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $table_name2 = $wpdb->prefix . 'rsrecordpoints';
            global $woocommerce;
            $termid = '';
            $order = new WC_Order($order_id);
            $order_user_id = rs_get_order_obj($order);
            $order_status = $order_user_id['order_status'];
            $order_user_id = $order_user_id['order_userid'];
            $order_status = str_replace('wc-', '', $order_status);
            $selected_order_status = get_option('rs_order_status_control_redeem');
            $fp_earned_points_sms = false;
            $getredeemfororder = get_post_meta($order_id, 'redeem_point_once', true);
            $date = '999999999999';
            if ($getredeemfororder != 1) {
                $rewardgateway = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, '_payment_method', true);
                if ($rewardgateway == 'reward_gateway') {
                    $gateway_used = get_post_meta($order_id, 'sumo_gateway_used', true);
                    $total_redeem = get_post_meta($order_id, 'total_redeem_points_for_order_point_price', true);
                    $equredeemamt = RSPointExpiry::redeeming_conversion_settings($total_redeem);
                    if (get_post_meta($order_id, 'second_time_gateway', true) == '1') {
                        self::perform_calculation_with_expiry($total_redeem, $order_user_id);
                        $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($order_user_id);
                        self::record_the_points($order_user_id, '0', $total_redeem, $date, 'RPFGW', '0', $equredeemamt, $order_id, '', '', '', '', $totalpoints, '', '0');
                        update_post_meta($order_id, 'refund_gateway', 2);
                    }
                }
                $redeempoints = RSFunctionToApplyCoupon::update_redeem_reward_points_to_user($order_id, $order_user_id);
                if ($redeempoints != 0) {
                    $pointsredeemed = self::perform_calculation_with_expiry($redeempoints, $order_user_id);
                }
                /* Reward Points For Using Payment Gateway Method */
                //if ($points_awarded_for_this_order != 'yes') {
                $getuserdatabyid = get_user_by('id', $order_user_id);
                $getusernickname = is_object($getuserdatabyid) ? $getuserdatabyid->user_login : 'Guest';
                $auto_redeem_name = 'auto_redeem_' . strtolower($getusernickname);
                $maincouponchecker = 'sumo_' . strtolower($getusernickname);
                if ($redeempoints != 0) {
                    $equredeemamt = self::redeeming_conversion_settings($redeempoints);
                    $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($order_user_id);
                    if ($totalpoints >= 0) {
                        self::record_the_points($order_user_id, '0', $redeempoints, $date, 'RP', '0', $equredeemamt, $order_id, '', '', '', '', $totalpoints, '', '0');
                        if (in_array($maincouponchecker, $order->get_used_coupons())) {
                            do_action('fp_redeem_reward_points_manually', $order_id, $pointsredeemed);
                        }
                    }
                }
                if (in_array($auto_redeem_name, $order->get_used_coupons())) {
                    do_action('fp_redeem_reward_points_automatically', $order_id, $pointsredeemed);
                }
                update_post_meta($order_id, 'redeem_point_once', 1);
            }
            update_post_meta($order_id, 'second_time_gateway', 1);
        }

        /* Check Point is Valid to Redeeming
         * param1: $userid,
         * Function used for Redeemin when user uses third party payment gateways like PayPal
         * return: null, it just perform the query for mysql if the point is expired.
         */

        public static function update_redeem_point_for_user_third_party_sites($order_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $table_name2 = $wpdb->prefix . 'rsrecordpoints';
            global $woocommerce;
            $termid = '';
            $order = new WC_Order($order_id);
            $order_user_id = rs_get_order_obj($order);
            $payment_method = $order_user_id['payment_method'];
            $order_status = $order_user_id['order_status'];
            $order_user_id = $order_user_id['order_userid'];
            $order_status = 'pending';
            $selected_order_status = get_option('rs_order_status_control_redeem');
            $fp_earned_points_sms = false;
            if (in_array('pending', $selected_order_status)) {
                $getredeemfororder = get_post_meta($order_id, 'redeem_point_once', true);
                if ($getredeemfororder != 1) {
                    $redeempoints = RSFunctionToApplyCoupon::update_redeem_reward_points_to_user($order_id, $order_user_id);
                    if ($redeempoints != 0) {
                        $pointsredeemed = self::perform_calculation_with_expiry($redeempoints, $order_user_id);
                    }
                    $date = '999999999999';
                    /* Reward Points For Using Payment Gateway Method */
                    //if ($points_awarded_for_this_order != 'yes') {
                    if ($redeempoints != 0) {
                        $equredeemamt = self::redeeming_conversion_settings($redeempoints);
                        $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($order_user_id);
                        self::record_the_points($order_user_id, '0', $redeempoints, $date, 'RP', '0', $equredeemamt, $order_id, '', '', '', '', $totalpoints, '', '0');
                    }
                    update_post_meta($order_id, 'redeem_point_once', 1);
                }
            }
        }

        public static function update_revised_redeem_points_for_user($order_id) {
            global $woocommerce;
            $termid = '';
            $order = new WC_Order($order_id);
            $order_user_id = rs_get_order_obj($order);
            $order_user_id = $order_user_id['order_userid'];
            $redeempoints = self::update_revised_reward_points_to_user($order_id, $order_user_id);
            $date = rs_function_to_get_expiry_date_in_unixtimestamp();
            if ($redeempoints != 0) {
                $equredeemamt = self::redeeming_conversion_settings($redeempoints);
                self::insert_earning_points($order_user_id, $redeempoints, '0', $date, 'RVPFRP', $order_id, $redeempoints, '0', '');
                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($order_user_id);
                self::record_the_points($order_user_id, $redeempoints, '0', $date, 'RVPFRP', '0', $equredeemamt, $order_id, $productid = '', $variationid = '', $refuserid = '', '', $totalpoints, '', '0');
                update_post_meta($order_id, 'redeem_point_once', 2);
            }
            $rewardgateway = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, '_payment_method', true);
            $refund_gateway = get_post_meta($order_id, 'refund_gateway', true);
            if ($refund_gateway != '1') {
                if ($rewardgateway == 'reward_gateway') {
                    $total_redeem = get_post_meta($order_id, 'total_redeem_points_for_order_point_price', true);
                    if ($total_redeem != '' || $total_redeem != '0') {
                        self::insert_earning_points($order_user_id, $total_redeem, '0', $date, 'RVPFRPG', $order_id, $total_redeem, '0', '');
                        $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($order_user_id);
                        self::record_the_points($order_user_id, $total_redeem, '0', $date, 'RVPFRPG', '0', '0', $order_id, '0', '0', '0', '', $totalpoints, '', '0');
                        update_post_meta($order_id, 'refund_gateway', 1);
                        update_post_meta($order_id, 'second_time_gateway', 1);
                        update_post_meta($order_id, 'redeem_point_once', 2);
                    }
                }
            }
        }

        public static function getcommentstatus_post($id) {
            self::get_post_comment_user_list($id, true);
        }

        public static function get_page_comment_user_list($commentid, $approved) {
            if (get_option('rs_reward_for_comment_Page') == 'yes') {
                global $post;
                $mycomment = get_comment($commentid);
                $get_comment_post_type = get_post_type($mycomment->comment_post_ID);
                $postid = $mycomment->comment_post_ID;
                $orderuserid = $mycomment->user_id;
                if ($get_comment_post_type == 'page') {
                    $getuserreview = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($mycomment->user_id, 'usercommentpage' . $mycomment->comment_post_ID);
                    if ($getuserreview != '1') {
                        if (($approved == true)) {
                            $getreviewpoints = RSMemberFunction::user_role_based_reward_points($mycomment->user_id, get_option("rs_reward_page_review"));
                            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                            $currentregistrationpoints = $getreviewpoints;
                            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
                            if ($enabledisablemaxpoints == 'yes') {
                                $new_obj->check_point_restriction($restrictuserpoints, $currentregistrationpoints, $pointsredeemed = 0, $event_slug = 'RPCPAR', $mycomment->user_id, $nomineeid = '', $referrer_id = '', $mycomment->comment_post_ID, $variationid = '', $reasonindetail = '');
                            } else {
                                $equearnamt = RSPointExpiry::earning_conversion_settings($currentregistrationpoints);
                                $valuestoinsert = array('pointstoinsert' => $currentregistrationpoints, 'pointsredeemed' => 0, 'event_slug' => 'RPCPAR', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $mycomment->user_id, 'referred_id' => '', 'product_id' => $mycomment->comment_post_ID, 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $currentregistrationpoints, 'totalredeempoints' => 0);
                                $new_obj->total_points_management($valuestoinsert);
                                RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($mycomment->user_id, 'usercommentpage' . $mycomment->comment_post_ID, '1');
                            }
                        }
                    }
                }
            }
        }

        public static function get_post_comment_user_list($commentid, $approved) {
            if (get_option('rs_reward_for_comment_Post') == 'yes') {
                global $post;
                $mycomment = get_comment($commentid);
                $get_comment_post_type = get_post_type($mycomment->comment_post_ID);
                $postid = $mycomment->comment_post_ID;
                $orderuserid = $mycomment->user_id;
                if ($get_comment_post_type == 'post') {
                    $getuserreview = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($mycomment->user_id, 'usercommentpost' . $mycomment->comment_post_ID);
                    if ($getuserreview != '1') {
                        if (($approved == true)) {
                            $getreviewpoints = RSMemberFunction::user_role_based_reward_points($mycomment->user_id, get_option("rs_reward_post_review"));
                            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                            $currentregistrationpoints = $getreviewpoints;
                            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
                            if ($enabledisablemaxpoints == 'yes') {
                                $new_obj->check_point_restriction($restrictuserpoints, $currentregistrationpoints, $pointsredeemed = 0, $event_slug = 'RPCPAR', $mycomment->user_id, $nomineeid = '', $referrer_id = '', $mycomment->comment_post_ID, $variationid = '', $reasonindetail = '');
                            } else {
                                $equearnamt = RSPointExpiry::earning_conversion_settings($currentregistrationpoints);
                                $valuestoinsert = array('pointstoinsert' => $currentregistrationpoints, 'pointsredeemed' => 0, 'event_slug' => 'RPCPAR', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $mycomment->user_id, 'referred_id' => '', 'product_id' => $mycomment->comment_post_ID, 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $currentregistrationpoints, 'totalredeempoints' => 0);
                                $new_obj->total_points_management($valuestoinsert);
                                RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($mycomment->user_id, 'usercommentpost' . $mycomment->comment_post_ID, '1');
                            }
                        }
                    }
                }
            }
        }

        public static function check_if_expiry() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $userid = get_current_user_id();
            $currentdate = time();
            $getarraystructure = $wpdb->get_results("SELECT * FROM $table_name WHERE expirydate < $currentdate and expirydate NOT IN(999999999999) and expiredpoints IN(0) and userid=$userid", ARRAY_A);
            if (!empty($getarraystructure)) {
                foreach ($getarraystructure as $key => $eacharray) {
                    $wpdb->update($table_name, array('expiredpoints' => $eacharray['earnedpoints'] - $eacharray['usedpoints']), array('id' => $eacharray['id']));
                }
            }
        }

        public static function check_if_expiry_on_admin($user_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $userid = $user_id;
            $currentdate = time();
            $getarraystructure = $wpdb->get_results("SELECT * FROM $table_name WHERE expirydate < $currentdate and expirydate NOT IN(999999999999) and expiredpoints IN(0) and userid = $userid", ARRAY_A);
            if (!empty($getarraystructure)) {
                foreach ($getarraystructure as $key => $eacharray) {
                    $wpdb->update($table_name, array('expiredpoints' => $eacharray['earnedpoints'] - $eacharray['usedpoints']), array('id' => $eacharray['id']));
                }
            }
        }

        public static function delete_if_used() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $userid = get_current_user_id();
            $currentdate = time();
            $totalearnpoints = '0';
            $totalredeempoints = '0';
            $getarraystructure = $wpdb->get_results("SELECT * FROM $table_name WHERE earnedpoints=usedpoints and expiredpoints IN(0) and userid=$userid", ARRAY_A);
            if (!empty($getarraystructure)) {
                foreach ($getarraystructure as $eacharray) {
                    $totalearnpoints += (float) $eacharray['earnedpoints'];
                    $totalredeempoints += (float) $eacharray['usedpoints'];
                    update_user_meta($userid, 'rs_earned_points_before_delete', $totalearnpoints);
                    update_user_meta($userid, 'rs_redeem_points_before_delete', $totalredeempoints);
                    $wpdb->delete($table_name, array('id' => $eacharray['id']));
                }
            }
            $getdata = $wpdb->get_results("SELECT * FROM $table_name WHERE earnedpoints=(usedpoints+expiredpoints) and expiredpoints NOT IN(0) and userid=$userid", ARRAY_A);
            $totalexpiredpoints = '';
            if (!empty($getdata)) {
                foreach ($getdata as $array) {
                    $totalexpiredpoints += $array['expiredpoints'];
                    update_user_meta($userid, 'rs_expired_points_before_delete', $totalexpiredpoints);
                    $wpdb->delete($table_name, array('id' => $array['id']));
                }
            }
        }

        /* Get the SUM of available Points after performing few more audits */

        public static function get_sum_of_earned_points($userid) {
            $total_points_earned = "";
            if ($userid != '' && $userid != '0') {
                global $wpdb;
                $table_name = $wpdb->prefix . "rspointexpiry";
                $getcurrentuserid = $userid;
                $current_user_points_log = $wpdb->get_results("SELECT SUM(earnedpoints) as availablepoints FROM $table_name WHERE earnedpoints NOT IN(0) and userid=$getcurrentuserid", ARRAY_A);

                $totaloldearnedpoints = "";
                foreach ($current_user_points_log as $separate_points) {
                    $deletedearnedpoints = get_user_meta($getcurrentuserid, 'rs_earned_points_before_delete', true);
                    $total_earned_points = get_user_meta($getcurrentuserid, 'rs_user_total_earned_points', true);
                    $totalexpiredpoints = get_user_meta($getcurrentuserid, 'rs_expired_points_before_delete', true);
                    $oldearnedpoints = get_user_meta($getcurrentuserid, '_my_reward_points', true);
                    if ($total_earned_points > $oldearnedpoints) {
                        $totaloldearnedpoints = $total_earned_points - $oldearnedpoints;
                    }
                    $total_points_earned = $separate_points['availablepoints'] + (float) $deletedearnedpoints + (float) $totaloldearnedpoints + (float) $totalexpiredpoints;
                }
            }
            return $total_points_earned;
        }

        /* Get the SUM of available Points with order id */

        public static function get_sum_of_total_earned_points($userid) {
            if ($userid != '' && $userid != '0') {
                global $wpdb;
                $table_name = $wpdb->prefix . 'rspointexpiry';
                $getcurrentuserid = $userid;
                if ($getcurrentuserid != '') {
                    $usedpoints = $wpdb->get_results("SELECT usedpoints FROM $table_name WHERE usedpoints IS NULL", ARRAY_A);
                    $checkresults = $wpdb->get_results("SELECT SUM((earnedpoints-usedpoints)) as availablepoints FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid = $getcurrentuserid", ARRAY_A);
                    foreach ($checkresults as $checkresultss) {
                        $checkresult = $checkresultss['availablepoints'] != NULL ? $checkresultss['availablepoints'] : 0;
                    }
                    return $checkresult;
                }
            }
        }

        /* Insert the Data based on Point Expiry */

        public static function insert_earning_points($user_id, $earned_points, $usedpoints, $date, $checkpoints, $orderid, $totalearnedpoints, $totalredeempoints, $reasonindetail = '') {
            if ($user_id != '') {
                global $wpdb;
                $table_name = $wpdb->prefix . "rspointexpiry";
                $currentdate = time();
                $noofday = get_option('rs_point_to_be_expire');
                $expirydate = 999999999999;
                if (($noofday == '') || ($noofday == '0')) {
                    $query = $wpdb->get_row("SELECT * FROM $table_name WHERE userid = $user_id and expirydate = $expirydate", ARRAY_A);
                    if (!empty($query)) {
                        $id = $query['id'];
                        $oldearnedpoints = $query['earnedpoints'];
                        $oldearnedpoints = $oldearnedpoints + $earned_points;
                        $usedpoints = $usedpoints + $query['usedpoints'];
                        $wpdb->update($table_name, array('earnedpoints' => $oldearnedpoints, 'usedpoints' => $usedpoints), array('id' => $id));
                    } else {
                        $wpdb->insert(
                                $table_name, array(
                            'earnedpoints' => $earned_points,
                            'usedpoints' => $usedpoints,
                            'expiredpoints' => '0',
                            'userid' => $user_id,
                            'earneddate' => $currentdate,
                            'expirydate' => $date,
                            'checkpoints' => $checkpoints,
                            'orderid' => $orderid,
                            'totalearnedpoints' => $totalearnedpoints,
                            'totalredeempoints' => $totalredeempoints,
                            'reasonindetail' => $reasonindetail
                        ));
                    }
                } else {
                    $wpdb->insert(
                            $table_name, array(
                        'earnedpoints' => $earned_points,
                        'usedpoints' => $usedpoints,
                        'expiredpoints' => '0',
                        'userid' => $user_id,
                        'earneddate' => $currentdate,
                        'expirydate' => $date,
                        'checkpoints' => $checkpoints,
                        'orderid' => $orderid,
                        'totalearnedpoints' => $totalearnedpoints,
                        'totalredeempoints' => $totalredeempoints,
                        'reasonindetail' => $reasonindetail
                    ));
                }
            }
        }

        public static function record_the_points($user_id, $earned_points, $usedpoints, $date, $checkpoints, $equearnamt, $equredeemamt, $orderid, $productid, $variationid, $refuserid, $reasonindetail, $totalpoints, $nomineeid, $nomineepoints) {
            if ($user_id != '') {
                global $wpdb;
                $table_name = $wpdb->prefix . "rsrecordpoints";
                $currentdate = time();
                $wpdb->insert(
                        $table_name, array(
                    'earnedpoints' => $earned_points,
                    'redeempoints' => $usedpoints,
                    'userid' => $user_id,
                    'earneddate' => $currentdate,
                    'expirydate' => $date,
                    'checkpoints' => $checkpoints,
                    'earnedequauivalentamount' => $equearnamt,
                    'redeemequauivalentamount' => $equredeemamt,
                    'productid' => $productid,
                    'variationid' => $variationid,
                    'orderid' => $orderid,
                    'refuserid' => $refuserid,
                    'reasonindetail' => $reasonindetail,
                    'totalpoints' => $totalpoints,
                    'showmasterlog' => "false",
                    'showuserlog' => "false",
                    'nomineeid' => $nomineeid,
                    'nomineepoints' => $nomineepoints
                ));
            }
        }

        public static function perform_calculation_with_expiry($redeempoints, $getcurrentuserid) {
            if ($getcurrentuserid != '') {
                global $wpdb;
                $table_name = $wpdb->prefix . 'rspointexpiry';
                $getarraystructure = $wpdb->get_results("SELECT * FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and  expiredpoints IN(0) and userid=$getcurrentuserid ORDER BY expirydate ASC", ARRAY_A);
                if (is_array($getarraystructure)) {
                    foreach ($getarraystructure as $key => $eachrow) {
                        $getactualpoints = $eachrow['earnedpoints'] - $eachrow['usedpoints'];
                        if ($redeempoints >= $getactualpoints) {
                            $getusedpoints = $getactualpoints;
                            $usedpoints = $eachrow['usedpoints'] + $getusedpoints;
                            $id = $eachrow['id'];
                            $redeempoints = $redeempoints - $getactualpoints;

                            $wpdb->query("UPDATE $table_name SET usedpoints = $usedpoints WHERE id = $id");
                            if ($redeempoints == 0) {
                                break;
                            }
                        } else {
                            $getusedpoints = $redeempoints;
                            $usedpoints = $eachrow['usedpoints'] + $getusedpoints;
                            $id = $eachrow['id'];

                            $wpdb->query("UPDATE $table_name SET usedpoints = $usedpoints  WHERE id = $id");
                            break;
                        }
                    }
                }
            }
            return $redeempoints;
        }

        public static function update_revised_points_for_user($order_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $table_name2 = $wpdb->prefix . 'rsrecordpoints';
            global $woocommerce;
            $termid = '';
            $order = new WC_Order($order_id);
            $order_user_id = rs_get_order_obj($order);
            $payment_method = $order_user_id['payment_method'];
            $order_user_id = $order_user_id['order_userid'];
            $new_obj = new RewardPointsOrder($order_id, $apply_previous_order_points = 'no');
            $checkredeeming = $new_obj->check_redeeming_in_order();
            $date = rs_function_to_get_expiry_date_in_unixtimestamp();
            $enableoption = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, 'rs_check_enable_option_for_redeeming');
            if ($enableoption == 'yes' && $checkredeeming == false) {
                $getpaymentgatewayused = rs_function_to_get_gateway_point($order_id, $order_user_id, $payment_method);
                if ($getpaymentgatewayused != '') {
                    $valuestoinsert = array('pointstoinsert' => 0, 'pointsredeemed' => $getpaymentgatewayused, 'event_slug' => 'RVPFRPG', 'equalearnamnt' => 0, 'equalredeemamnt' => 0, 'user_id' => $order_user_id, 'referred_id' => '', 'product_id' => '', 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => 0, 'totalredeempoints' => $getpaymentgatewayused);
                    $new_obj->total_points_management($valuestoinsert);
                }
            } else {
                $getpaymentgatewayused = rs_function_to_get_gateway_point($order_id, $order_user_id, $payment_method);
                if ($getpaymentgatewayused != '') {
                    $valuestoinsert = array('pointstoinsert' => 0, 'pointsredeemed' => $getpaymentgatewayused, 'event_slug' => 'RVPFRPG', 'equalearnamnt' => 0, 'equalredeemamnt' => 0, 'user_id' => $order_user_id, 'referred_id' => '', 'product_id' => '', 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => 0, 'totalredeempoints' => $getpaymentgatewayused);
                    $new_obj->total_points_management($valuestoinsert);
                }
            }
            $product_ids = get_post_meta($order_id, 'points_for_current_order', true);
            if ($checkredeeming == false) {
                $value = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, 'rs_revised_points_once', true);
                if ($value != '1') {
                    $orderuserid = $order_user_id;
                    self::rs_insert_the_selected_level_revised_reward_points($redeempoints = 0, $orderuserid, $order_id, $product_ids, $date);
                    $referreduser = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, '_referrer_name');
                    if ($referreduser != '') {
                        $product_ids = get_post_meta($order_id, 'rsgetreferalpoints', true);
                        self::rs_insert_the_selected_level_revised_reward_points_get_refer($redeempoints = 0, $orderuserid, $order_id, $product_ids, $date);
                        self::rs_insert_the_selected_level_revised_referral_reward_points($redeempoints = 0, $referreduser, $orderuserid, $order_id, $new_obj, $order);
                    }
                }
                update_post_meta($order_id, 'earning_point_once', 2);
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($order_id, 'rs_revised_points_once', 1);
            }
        }

        public static function update_revised_reward_points_to_user($order_id, $orderuserid) {
            // Inside Loop
            $order = new WC_Order($order_id);
            $rewardpointscoupons = $order->get_items(array('coupon'));
            $getuserdatabyid = get_user_by('id', $orderuserid);
            $getusernickname = is_object($getuserdatabyid) ? $getuserdatabyid->user_login : 'Guest';
            $maincouponchecker = 'sumo_' . strtolower($getusernickname);
            $auto_redeem_name = 'auto_redeem_' . strtolower($getusernickname);
            foreach ($rewardpointscoupons as $couponcode => $value) {
                if ($maincouponchecker == $value['name']) {
                    if (get_option('rewardsystem_looped_over_coupon' . $order_id) != '1') {
                        $getcouponid = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($orderuserid, 'redeemcouponids', true);
                        $currentamount = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($getcouponid, 'coupon_amount');
                        if ($currentamount >= $value['discount_amount']) {
                            $current_conversion = wc_format_decimal(get_option('rs_redeem_point'));
                            $point_amount = wc_format_decimal(get_option('rs_redeem_point_value'));
                            $redeemedamount = $value['discount_amount'] * $current_conversion;
                            $redeemedpoints = $redeemedamount / $point_amount;
                        }
                        return $redeemedpoints;
                        update_option('rewardsystem_looped_over_coupon' . $order_id, '1');
                    }
                }
                if ($auto_redeem_name == $value['name']) {
                    if (get_option('rewardsystem_looped_over_coupon' . $order_id) != '1') {
                        $getuserdatabyid = get_user_by('id', $orderuserid);
                        $getusernickname = is_object($getuserdatabyid) ? $getuserdatabyid->user_login : 'Guest';
                        $getcouponid = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($orderuserid, 'auto_redeemcoupon_ids', true);
                        $currentamount = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($getcouponid, 'coupon_amount');
                        if ($currentamount >= $value['discount_amount']) {
                            $current_conversion = wc_format_decimal(get_option('rs_redeem_point'));
                            $point_amount = wc_format_decimal(get_option('rs_redeem_point_value'));
                            $redeemedamount = $value['discount_amount'] * $current_conversion;
                            $redeemedpoints = $redeemedamount / $point_amount;
                        }
                        return $redeemedpoints;
                        update_option('rewardsystem_looped_over_coupon' . $order_id, '1');
                    }
                }
            }
        }

        public static function rs_insert_the_selected_level_revised_reward_points_get_refer($pointsredeemed, $orderuserid, $order_id, $product_ids, $date) {
            if (!empty($product_ids)) {
                foreach ($product_ids as $key => $value) {
                    self::insert_earning_points($orderuserid, $pointsredeemed, $value, $date, 'RVPPRRPG', $order_id, $totalearnedpoints, $totalredeempoints, '');
                    $equearnamt = self::earning_conversion_settings($pointsredeemed);
                    $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($orderuserid);
                    self::record_the_points($orderuserid, '0', $value, $date, 'RVPPRRPG', $equearnamt, $equredeemamt = '0', $order_id, $key, $key, '', '', $totalpoints, '', '0');
                }
            }
        }

        public static function rs_insert_the_selected_level_revised_reward_points($pointsredeemed, $orderuserid, $order_id, $product_ids, $date) {
            if (!empty($product_ids)) {
                foreach ($product_ids as $key => $value) {
                    self::insert_earning_points($orderuserid, $pointsredeemed, $value, $date, 'RVPFPPRP', $order_id, $totalearnedpoints = '0', $totalredeempoints = '0', '');
                    $equearnamt = self::earning_conversion_settings($pointsredeemed);
                    $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($orderuserid);
                    if ($totalpoints > 0) {
                        self::record_the_points($orderuserid, '0', $value, $date, 'RVPFPPRP', $equearnamt, $equredeemamt = '0', $order_id, $key, $key, '', '', $totalpoints, '', '0');
                    }
                }
            }
        }

        public static function rs_insert_the_selected_level_revised_referral_reward_points($pointsredeemed, $referreduser, $orderuserid, $order_id, $new_obj, $order) {
            $refuser = get_user_by('login', $referreduser);
            if ($refuser != false) {
                $myid = $refuser->ID;
            } else {
                $myid = $referreduser;
            }
            $order_user_id = rs_get_order_obj($order);
            $order_user_id = $order_user_id['order_userid'];
            foreach ($order->get_items() as $item) {
                $checkproduct = rs_get_product_object($item['product_id']);
                if (is_object($checkproduct) && ($checkproduct->is_type('simple') || ($checkproduct->is_type('subscription')) || ($checkproduct->is_type('booking')))) {
                    $productid = $item['product_id'];
                    $variationid = '0';
                } else {
                    $productid = $item['product_id'];
                    $variationid = $item['variation_id'];
                }
                $checklevel = 'no';
                $pointstoinsert = check_level_of_enable_reward_point($productid, $variationid, $item, $checklevel, $myid, $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
                $valuestoinsert = array('pointstoinsert' => 0, 'pointsredeemed' => $pointstoinsert, 'event_slug' => 'RVPFPPRRP', 'equalearnamnt' => 0, 'equalredeemamnt' => 0, 'user_id' => $order_user_id, 'referred_id' => $myid, 'product_id' => $productid, 'variation_id' => $variationid, 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => 0, 'totalredeempoints' => $pointstoinsert);
                $new_obj->total_points_management($valuestoinsert);
            }
        }

        /*
         * @ updates earning points for user in db
         *
         */

        public static function update_earning_points_for_user($order_id) {
            $new_obj = new RewardPointsOrder($order_id, $apply_previous_order_points = 'no');
            $new_obj->update_earning_points_for_user();
        }

        public static function check_weather_the_points_is_awarded_for_order($order_id) {
            $order = new WC_Order($order_id);
            $array = array();
            foreach ($order->get_items() as $item) {
                $termid = '';
                $productid = $item['product_id'];
                $variationid = $item['variation_id'] == '' ? '0' : $item['variation_id'];
                $checklevel = 'yes';
                $checked_level_for_reward_points = check_level_of_enable_reward_point($productid, $variationid, $item, $checklevel, $referred_user = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
                $array[] = $checked_level_for_reward_points;
            }
            return $array;
        }

        public static function check_redeeming_in_order($order_id, $orderuserid) {
            $new_obj = new RewardPointsOrder($order_id, $apply_previous_order_points = 'no');
            $new_obj->check_redeeming_in_order();
        }

        public static function order_coupon_validator($order_id, $product_id) {
            $modified_point_list = get_post_meta($order_id, 'points_for_current_order', true);
            foreach ($modified_point_list as $key => $value) {
                if ($product_id == $key) {
                    $totalrewardpointsnew = $value;
                }
            }
            return $totalrewardpointsnew;
        }

        public static function delete_referral_registered_people($user_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $table_name2 = $wpdb->prefix . 'rsrecordpoints';
            $registration_points = get_option('rs_reward_signup');
            $referral_registration_points = RSMemberFunction::user_role_based_reward_points($user_id, get_option('rs_referral_reward_signup'));
            $getreferredusermeta = get_user_meta($user_id, '_rs_i_referred_by', true);
            $refuserid = $getreferredusermeta;
            $getregisteredcount = get_user_meta($refuserid, 'rsreferreduserregisteredcount', true);
            $currentregistration = $getregisteredcount - 1;
            update_user_meta($refuserid, 'rsreferreduserregisteredcount', $currentregistration);
            $date = rs_function_to_get_expiry_date_in_unixtimestamp();
            /* Below Code is for Removing Referral Point Registration when Deleting User */
            $user_info = new WP_User($user_id);
            $registered_date = $user_info->user_registered;
            $delay_days = get_option('_rs_days_for_redeeming_points');
            $modified_registered_date = date('Y-m-d h:i:sa', strtotime($registered_date));
            $checking_date = date('Y-m-d h:i:sa', strtotime($modified_registered_date . ' + ' . $delay_days . ' days '));
            $modified_checking_date = strtotime($checking_date);
            $current_date = date('Y-m-d h:i:sa');
            $modified_current_date = strtotime($current_date);
            $reward_referal_user_deleted = get_option('_rs_reward_referal_point_user_deleted');
            $no_of_days_to_reward = get_option('_rs_time_validity_to_redeem');
            if ($reward_referal_user_deleted == '1') {
                if ($no_of_days_to_reward == '1') {
                    $condition = true;
                } else {
                    $condition = $modified_current_date < $modified_checking_date;
                }
                if ($condition) {
                    if ($getreferredusermeta != '') {
                        $oldpointss = self::get_sum_of_total_earned_points($refuserid);
                        $currentregistrationpointss = $oldpointss - $referral_registration_points;
                        self::insert_earning_points($refuserid, '0', $referral_registration_points, $date, 'RVPFRRRP', '0', '0', '0', '');
                        $equredeemamt = self::redeeming_conversion_settings($referral_registration_points);
                        $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($refuserid);
                        self::record_the_points($refuserid, '0', $referral_registration_points, $date, 'RVPFRRRP', '0', $equredeemamt, '0', '0', '0', $user_id, '', $totalpoints, '', '0');
                        update_user_meta($user_id, '_rs_i_referred_by', $refuserid);
                    }
                    $getlistoforder = get_user_meta($user_id, '_update_user_order', true);
                    if (is_array($getlistoforder) && !empty($getlistoforder)) {
                        foreach ($getlistoforder as $order_id) {
                            $order = new WC_Order($order_id);
                            $order_user_id = rs_get_order_obj($order);
                            $order_user_id = $order_user_id['order_userid'];
                            if ($order->status == 'completed') {
                                $pointslog = array();
                                $usernickname = get_user_meta($order_user_id, 'nickname', true);
                                foreach ($order->get_items() as $item) {
                                    if (get_option('rs_set_price_to_calculate_rewardpoints_by_percentage') == '1') {
                                        $getregularprice = get_post_meta($item['product_id'], '_regular_price', true);

                                        if ($getregularprice == '') {
                                            $getregularprice = get_post_meta($item['product_id'], '_price', true);
                                        }
                                    } else {
                                        $getregularprice = get_post_meta($item['product_id'], '_price', true);
                                        if ($getregularprice == '') {
                                            $getregularprice = get_post_meta($item['product_id'], '_regular_price', true);
                                        }
                                    }
                                    $getregularprice = get_discount_price($item['product_id']);
                                    do_action_ref_array('rs_delete_points_for_referral_simple', array(&$getregularprice, &$item));
                                    $productid = $item['product_id'];
                                    $variationid = $item['variation_id'] == '' ? '0' : $item['variation_id'];
                                    $itemquantity = $item['qty'];
                                    $orderuserid = $order_user_id;
                                    $term = get_the_terms($productid, 'product_cat');
                                    if (is_array($term)) {
                                        foreach ($term as $terms) {
                                            $termid = $terms->term_id;
                                        }
                                    }
                                    $new_obj = new RewardPointsOrder($order_id, $apply_previous_order_points = 'no');
                                    $referreduser = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, '_referrer_name');
                                    if ($referreduser != '') {
                                        //For Inserting Referral Reward Points
                                        self::rs_insert_the_selected_level_revised_referral_reward_points($redeempoints = 0, $referreduser, $orderuserid, $order_id, $new_obj, $order);
                                        $gettotalearnedpoints = $wpdb->get_results("SELECT SUM((earnedpoints)) as availablepoints FROM $table_name WHERE orderid = $order_id", ARRAY_A);
                                        $totalearnedpoints = ($gettotalearnedpoints[0]['availablepoints'] != NULL) ? $gettotalearnedpoints[0]['availablepoints'] : 0;
                                        $totalredeempoints = '0';
                                        $equredeemamt = self::redeeming_conversion_settings($totalredeempoints);
                                        $wpdb->query("UPDATE $table_name SET totalearnedpoints = $totalearnedpoints,totalredeempoints = $totalredeempoints WHERE orderid = $order_id");
                                    }
                                    self::update_revised_reward_points_to_user($order_id, $orderuserid);
                                }
                            }
                        }
                    }
                }
            }
        }

        /*
         *
         * @ Redeeming Conversion settings
         * @returns equivalent currency  value for current points
         */

        public static function redeeming_conversion_settings($points_to_redeem) {
            $user_entered_points = $points_to_redeem; //Ex:10points
            $conversion_rate_points = wc_format_decimal(get_option('rs_redeem_point')); //Conversion Points
            $conversion_rate_points_value = wc_format_decimal(get_option('rs_redeem_point_value')); //Value for the Conversion Points (i.e)  1 points is equal to $.2
            $conversion_step1 = $user_entered_points / $conversion_rate_points; //Ex: 10/1=10
            $converted_value = $conversion_step1 * $conversion_rate_points_value; //Ex:10 * 2 = 20
            return $converted_value; // $.20
        }

        /*
         *
         * @ Earning Conversion settings
         * @returns equivalent currency  value for current points
         */

        public static function earning_conversion_settings($earnpoints) {
            $user_entered_points = $earnpoints; //Ex:10points
            $conversion_rate_points = earn_point_conversion(); //Conversion Points
            $conversion_rate_points_value = earn_point_conversion_value(); //Value for the Conversion Points (i.e)  1 points is equal to $.2
            $conversion_step1 = $user_entered_points / $conversion_rate_points; //Ex: 10/1=10
            $converted_value = $conversion_step1 * $conversion_rate_points_value; //Ex:10 * 2 = 20
            return $converted_value; // $.20
        }

        public static function check_if_the_customer_purchased_this_product_already($user_id, $emails, $product_id, $variation_id) {
            global $wpdb;
            $results = $wpdb->get_results(
                    $wpdb->prepare("
			SELECT DISTINCT order_items.order_item_id
			FROM {$wpdb->prefix}woocommerce_order_items as order_items
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta ON order_items.order_item_id = itemmeta.order_item_id
                        LEFT JOIN {$wpdb->postmeta} AS postmeta ON order_items.order_id = postmeta.post_id
			LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
			WHERE
				posts.post_status IN ( 'wc-completed', 'wc-processing' ) AND
				itemmeta.meta_value  = %s AND
				itemmeta.meta_key    IN ( '_variation_id', '_product_id' ) AND
				postmeta.meta_key    IN ( '_billing_email', '_customer_user' ) AND
				(
					postmeta.meta_value  IN ( '" . implode("','", array_map('esc_sql', array_unique((array) $emails))) . "' ) OR
					(
						postmeta.meta_value = %s
					)
				)
			", $variation_id == '' ? $product_id : $variation_id, $user_id
                    )
            );

            $array_results = array();
            if (!empty($results)) {
                foreach ($results as $each_results) {
                    $array_results[] = $each_results->order_item_id;
                }
                $new = $wpdb->get_results("SELECT SUM(meta_value) as totalqty FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id IN(" . implode(',', $array_results) . ") and meta_key='_qty'");

                return $new[0]->totalqty;
            } else {
                return 0;
            }
        }

        public static function get_reviewed_user_list($commentid, $approved) {
            global $post;
            $mycomment = get_comment($commentid);
            $get_comment_post_type = get_post_type($mycomment->comment_post_ID);
            $orderuserid = $mycomment->user_id;
            $get_status = $mycomment->comment_approved;
            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
            if ($get_comment_post_type == 'product') {
                if (get_option('rs_reward_for_comment_product_review') == 'yes') {
                    $userid = get_current_user_id();
                    $product_id = $mycomment->comment_post_ID;
                    $user_info = get_user_by('id', $userid);
                    $emails = $user_info->user_email;
                    $get_all_review_product_id = self::check_if_the_customer_purchased_this_product_already($mycomment->user_id, $emails, $product_id, '');
                    if ($get_all_review_product_id > 0) {
                        if (get_option('rs_restrict_reward_product_review') == 'yes') {
                            $getuserreview = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($mycomment->user_id, 'userreviewed' . $mycomment->comment_post_ID);
                            if ($getuserreview != '1') {
                                if (($get_status == '1')) {
                                    $getreviewpoints = RSMemberFunction::user_role_based_reward_points($mycomment->user_id, get_option("rs_reward_product_review"));
                                    $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                                    $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                                    $currentregistrationpoints = $getreviewpoints;
                                    if ($enabledisablemaxpoints == 'yes') {
                                        $new_obj->check_point_restriction($restrictuserpoints, $currentregistrationpoints, $pointsredeemed = 0, $event_slug = 'RPPR', $mycomment->user_id, $nomineeid = '', $referrer_id = '', $mycomment->comment_post_ID, $variationid = '', $reasonindetail = '');
                                    } else {
                                        $equearnamt = RSPointExpiry::earning_conversion_settings($currentregistrationpoints);
                                        $valuestoinsert = array('pointstoinsert' => $currentregistrationpoints, 'pointsredeemed' => 0, 'event_slug' => 'RPPR', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $mycomment->user_id, 'referred_id' => '', 'product_id' => $mycomment->comment_post_ID, 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $currentregistrationpoints, 'totalredeempoints' => 0);
                                        $new_obj->total_points_management($valuestoinsert);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($mycomment->user_id, 'userreviewed' . $mycomment->comment_post_ID, '1');
                                    }
                                }
                            }
                        } else {
                            if (($get_status == '1')) {
                                $getreviewpoints = RSMemberFunction::user_role_based_reward_points($mycomment->user_id, get_option("rs_reward_product_review"));
                                $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                                $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                                $currentregistrationpoints = $getreviewpoints;
                                if ($enabledisablemaxpoints == 'yes') {
                                    $new_obj->check_point_restriction($restrictuserpoints, $currentregistrationpoints, $pointsredeemed = 0, $event_slug = 'RPPR', $mycomment->user_id, $nomineeid = '', $referrer_id = '', $mycomment->comment_post_ID, $variationid = '', $reasonindetail = '');
                                } else {
                                    $equearnamt = RSPointExpiry::earning_conversion_settings($currentregistrationpoints);
                                    $valuestoinsert = array('pointstoinsert' => $currentregistrationpoints, 'pointsredeemed' => 0, 'event_slug' => 'RPPR', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $mycomment->user_id, 'referred_id' => '', 'product_id' => $mycomment->comment_post_ID, 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $currentregistrationpoints, 'totalredeempoints' => 0);
                                    $new_obj->total_points_management($valuestoinsert);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($mycomment->user_id, 'userreviewed' . $mycomment->comment_post_ID, '1');
                                }
                            }
                        }
                    }
                } else {
                    if (get_option('rs_restrict_reward_product_review') == 'yes') {
                        $getuserreview = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($mycomment->user_id, 'userreviewed' . $mycomment->comment_post_ID);
                        if ($getuserreview != '1') {
                            if (($get_status == '1')) {
                                $getreviewpoints = RSMemberFunction::user_role_based_reward_points($mycomment->user_id, get_option("rs_reward_product_review"));
                                $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                                $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                                $currentregistrationpoints = $getreviewpoints;
                                if ($enabledisablemaxpoints == 'yes') {
                                    $new_obj->check_point_restriction($restrictuserpoints, $currentregistrationpoints, $pointsredeemed = 0, $event_slug = 'RPPR', $mycomment->user_id, $nomineeid = '', $referrer_id = '', $mycomment->comment_post_ID, $variationid = '', $reasonindetail = '');
                                } else {
                                    $equearnamt = RSPointExpiry::earning_conversion_settings($currentregistrationpoints);
                                    $valuestoinsert = array('pointstoinsert' => $currentregistrationpoints, 'pointsredeemed' => 0, 'event_slug' => 'RPPR', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $mycomment->user_id, 'referred_id' => '', 'product_id' => $mycomment->comment_post_ID, 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $currentregistrationpoints, 'totalredeempoints' => 0);
                                    $new_obj->total_points_management($valuestoinsert);
                                    RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($mycomment->user_id, 'userreviewed' . $mycomment->comment_post_ID, '1');
                                }
                            }
                        }
                    } else {
                        if (($get_status == '1')) {
                            $getreviewpoints = RSMemberFunction::user_role_based_reward_points($mycomment->user_id, get_option("rs_reward_product_review"));
                            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                            $currentregistrationpoints = $getreviewpoints;
                            if ($enabledisablemaxpoints == 'yes') {
                                $new_obj->check_point_restriction($restrictuserpoints, $currentregistrationpoints, $pointsredeemed = 0, $event_slug = 'RPPR', $mycomment->user_id, $nomineeid = '', $referrer_id = '', $mycomment->comment_post_ID, $variationid = '', $reasonindetail = '');
                            } else {
                                $equearnamt = RSPointExpiry::earning_conversion_settings($currentregistrationpoints);
                                $valuestoinsert = array('pointstoinsert' => $currentregistrationpoints, 'pointsredeemed' => 0, 'event_slug' => 'RPPR', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $mycomment->user_id, 'referred_id' => '', 'product_id' => $mycomment->comment_post_ID, 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $currentregistrationpoints, 'totalredeempoints' => 0);
                                $new_obj->total_points_management($valuestoinsert);
                                RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($mycomment->user_id, 'userreviewed' . $mycomment->comment_post_ID, '1');
                            }
                        }
                    }
                }
            }
            do_action('fp_reward_point_for_product_review');
        }

        public static function getcommentstatus($id) {
            if (get_option('rs_review_reward_status') == '1') {
                self::get_reviewed_user_list($id, true);
            } else {
                self::get_reviewed_user_list($id, false);
            }
        }

        public static function rs_function_to_display_log($csvmasterlog, $user_deleted, $order_status_changed, $earnpoints, $order, $checkpoints, $productid, $orderid, $variationid, $userid, $refuserid, $reasonindetail, $redeempoints, $masterlog, $nomineeid, $usernickname, $nominatedpoints) {
            $getmsgrpg = '';
            $post_url = admin_url('post.php?post=' . $orderid) . '&action=edit';
            $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
            $vieworderlink = esc_url_raw(add_query_arg('view-order', $orderid, $myaccountlink));
            $vieworderlinkforfront = '<a href="' . $vieworderlink . '">#' . $orderid . '</a>';
            $view_product = '<a target="_blank" href="' . get_permalink($productid) . '">' . get_the_title($productid) . '</a>';
            $vieworderlink1 = esc_url_raw(add_query_arg('view-subscription', $orderid, $myaccountlink));
            $vieworderlinkforfront1 = '<a href="' . $vieworderlink1 . '">#' . $orderid . '</a>';
            $payment_method_title = rs_get_order_obj($order);
            $payment_method_title = $payment_method_title['payment_method_title'];
            switch ($checkpoints) {
                case 'RPG' :
                    $getmsgrpg = get_option('_rs_localize_reward_for_payment_gateway_message');
                    $replacepaymenttitle = str_replace('{payment_title}', $payment_method_title, $getmsgrpg);
                    return $replacepaymenttitle;
                    break;
                case 'PPRP':
                    if ($masterlog == false) {
                        $getmsgrpg = get_option('_rs_localize_points_earned_for_purchase_main');
                        $replaceorderid = str_replace('{currentorderid}', $vieworderlinkforfront, $getmsgrpg);
                        return $replaceorderid;
                        break;
                    } else {
                        if ($csvmasterlog == false) {
                            $getmsgrpg = get_option('_rs_localize_product_purchase_reward_points');
                            $replaceproductid = str_replace('{itemproductid}', $productid, $getmsgrpg);
                            if (strpos($getmsgrpg, '{productname}') !== false) {
                                $replaceproductid = str_replace('{productname}', $view_product, $getmsgrpg);
                            }
                            $replaceorderid = str_replace('{currentorderid}', $vieworderlinkforfront, $replaceproductid);
                            return $replaceorderid;
                        } else {
                            $getmsgrpg = get_option('_rs_localize_product_purchase_reward_points');
                            $replaceproductid = str_replace('{itemproductid}', $productid, $getmsgrpg);
                            if (strpos($getmsgrpg, '{productname}') !== false) {
                                $replaceproductid = str_replace('{productname}', $view_product, $getmsgrpg);
                            }
                            $replaceorderid = str_replace('{currentorderid}', '#' . $orderid, $replaceproductid);
                            return $replaceorderid;
                        }
                    }
                case 'PPRRPG':
                    $getmsgrpg = get_option('_rs_localize_referral_reward_points_for_purchase_gettin_referred');
                    $postname = get_the_title($productid);
                    $replaceproductid = str_replace('{itemproductid}', $postname, $getmsgrpg);
                    if (strpos($getmsgrpg, '{productname}') !== false) {
                        $replaceproductid = str_replace('{productname}', $view_product, $getmsgrpg);
                    }
                    return $replaceproductid;
                    break;
                case 'RRPGR':
                    $getmsgrpg = get_option('_rs_localize_referral_reward_points_gettin_referred');
                    return $getmsgrpg;
                    break;
                case 'PPRRP':
                    $getmsgrpg = get_option('_rs_localize_referral_reward_points_for_purchase');
                    $replaceproductid = str_replace('{itemproductid}', $productid, $getmsgrpg);
                    if (strpos($getmsgrpg, '{productname}') !== false) {
                        $replaceproductid = str_replace('{productname}', $view_product, $getmsgrpg);
                    }
                    $replaceusername = str_replace('{purchasedusername}', $refuserid != '' ? $refuserid : __('Guest', 'rewardsystem'), $replaceproductid);
                    return $replaceusername;
                    break;
                case 'RRP':
                    $getmsgrpg = get_option('_rs_localize_points_earned_for_registration');
                    return $getmsgrpg;
                    break;
                case 'RRRP':
                    $getmsgrpg = get_option('_rs_localize_points_earned_for_referral_registration');
                    $refuserid = $refuserid != '' ? $refuserid : '(User Deleted)';
                    $replaceusername = str_replace('{registereduser}', $refuserid, $getmsgrpg);
                    return $replaceusername;
                    break;
                case 'LRP':
                    $getmsgrpg = get_option('_rs_localize_reward_points_for_login');
                    return $getmsgrpg;
                    break;
                case 'RPC':
                    $getmsgrpg = get_option('_rs_localize_coupon_reward_points_log');
                    return $getmsgrpg;
                    break;
                case 'RPFL':
                    $getmsgrpg = get_option('_rs_localize_reward_for_facebook_like');
                    return $getmsgrpg;
                    break;
                case 'RPFS':
                    $getmsgrpg = get_option('_rs_localize_reward_for_facebook_share');
                    return $getmsgrpg;
                    break;

                case 'RPTT':
                    $getmsgrpg = get_option('_rs_localize_reward_for_twitter_tweet');
                    return $getmsgrpg;
                    break;
                case 'RPIF':
                    $getmsgrpg = get_option('_rs_localize_reward_for_instagram');
                    return $getmsgrpg;
                    break;
                case 'RPTF':
                    $getmsgrpg = get_option('_rs_localize_reward_for_twitter_follow');
                    return $getmsgrpg;
                    break;
                case 'RPOK':
                    $getmsgrpg = get_option('_rs_localize_reward_for_ok_follow');
                    return $getmsgrpg;
                    break;
                case 'RPGPOS':
                    $getmsgrpg = get_option('_rs_localize_reward_for_google_plus');
                    return $getmsgrpg;
                    break;
                case 'RPVL':
                    $getmsgrpg = get_option('_rs_localize_reward_for_vk');
                    return $getmsgrpg;
                    break;
                case 'RPPR':
                    $getmsgrpg = get_option('_rs_localize_points_earned_for_product_review');
                    $replaceproductid = str_replace('{reviewproductid}', $productid, $getmsgrpg);
                    if (strpos($getmsgrpg, '{productname}') != false) {
                        $replaceproductid = str_replace('{productname}', $view_product, $getmsgrpg);
                    }
                    return $replaceproductid;
                    break;
                case 'RP':
                    if ($csvmasterlog == false) {
                        $getmsgrpg = get_option('_rs_localize_points_redeemed_towards_purchase');
                        $replaceproductid = str_replace('{currentorderid}', $vieworderlinkforfront, $getmsgrpg);
                        return $replaceproductid;
                        break;
                    } else {
                        $getmsgrpg = get_option('_rs_localize_points_redeemed_towards_purchase');
                        $replaceproductid = str_replace('{currentorderid}', '#' . $orderid, $getmsgrpg);
                        return $replaceproductid;
                        break;
                    }
                case 'MAP':
                    $getmsgrpg = $reasonindetail;
                    return $getmsgrpg;
                    break;
                case 'MRP':
                    $getmsgrpg = $reasonindetail;
                    return $getmsgrpg;
                    break;
                case 'CBRP':
                    $getmsgrpg = get_option('_rs_localize_points_to_cash_log');
                    return $getmsgrpg;
                    break;
                case 'RCBRP':
                    $getmsgrpg = get_option('_rs_localize_points_to_cash_log_revised');
                    return $getmsgrpg;
                    break;
                case 'RPGV':
                    $getmsgrpg = get_option('_rs_localize_voucher_code_usage_log_message');
                    $replaceproductid = str_replace('{rsusedvouchercode}', $reasonindetail, $getmsgrpg);
                    return $replaceproductid;
                    break;
                case 'RPBSRP':
                    $getmsgrpg = get_option('_rs_localize_buying_reward_points_log');
                    $replaceproductid = str_replace('{rsbuyiedrewardpoints}', $earnpoints, $getmsgrpg);
                    return $replaceproductid;
                    break;
                case 'MAURP':
                    $getmsgrpg = $reasonindetail;
                    return $getmsgrpg;
                    break;
                case 'MRURP':
                    $getmsgrpg = $reasonindetail;
                    return $getmsgrpg;
                    break;
                case 'RPCPR':
                    $getmsgrpg = get_option('_rs_localize_points_earned_for_post_review');
                    $postname = get_the_title($productid);
                    $replaceproductid = str_replace('{postid}', $postname, $getmsgrpg);
                    return $replaceproductid;
                    return $getmsgrpg;
                    break;
                case 'RPCPRO':
                    $getmsgrpg = get_option('_rs_localize_points_earned_for_product_creation');
                    $postname = get_the_title($productid);
                    $replaceproductid = str_replace('{ProductName}', $postname, $getmsgrpg);
                    return $replaceproductid;
                    return $getmsgrpg;
                    break;
                case 'MREPFU':
                    $getmsgrpg = get_option('_rs_localize_max_earning_points_log');
                    $replacepoints = get_option('rs_max_earning_points_for_user');
                    $replace = str_replace('[rsmaxpoints]', $replacepoints, $getmsgrpg);
                    return $replace;
                    break;
                case 'RPFGW':
                    if ($csvmasterlog == false) {
                        $getmsgrpg = get_option('_rs_reward_points_gateway_log_localizaation');
                        $replaceproductid = str_replace('{currentorderid}', $vieworderlinkforfront, $getmsgrpg);
                        return $replaceproductid;
                        break;
                    } else {
                        $getmsgrpg = get_option('_rs_reward_points_gateway_log_localizaation');
                        $replaceproductid = str_replace('{currentorderid}', '#' . $orderid, $getmsgrpg);
                        return $replaceproductid;
                        break;
                    }
                case 'RPFGWS':
                    $getmsgrpg = get_option('_rs_localize_reward_for_using_subscription');
                    $replaceorderid = str_replace('{subscription_id}', $vieworderlinkforfront1, $getmsgrpg);
                    return $replaceorderid;
                    break;

                case 'RVPFRPG':
                    $getmsgrpg = get_option('_rs_localize_revise_reward_for_payment_gateway_message');
                    $replaceproductid = str_replace('{payment_title}', $payment_method_title, $getmsgrpg);
                    return $replaceproductid;
                    break;
                case 'RVPFPPRP':
                    if ($masterlog == false) {
                        if ($csvmasterlog == false) {
                            $getmsgrpg = get_option('_rs_log_revise_product_purchase_main');
                            $replaceproductid = str_replace('{currentorderid}', $vieworderlinkforfront, $getmsgrpg);
                            return $replaceproductid;
                            break;
                        } else {
                            $getmsgrpg = get_option('_rs_log_revise_product_purchase_main');
                            $replaceproductid = str_replace('{currentorderid}', '#' . $orderid, $getmsgrpg);
                            return $replaceproductid;
                            break;
                        }
                    } else {
                        $getmsgrpg = get_option('_rs_log_revise_product_purchase');
                        $replaceproductid = str_replace('{productid}', $productid, $getmsgrpg);
                        if (strpos($getmsgrpg, '{productname}') !== false) {
                            $replaceproductid = str_replace('{productname}', $view_product, $getmsgrpg);
                        }
                        return $replaceproductid;
                        break;
                    }
                case 'RVPFPPRRP':
                    if ($order_status_changed == true) {
                        $getmsgrpg = get_option('_rs_log_revise_referral_product_purchase');
                        $replaceproductid = str_replace('{productid}', $productid, $getmsgrpg);
                        return $replaceproductid;
                        break;
                    } elseif ($user_deleted == true) {
                        $getmsgrpg = get_option('_rs_localize_revise_points_for_referral_purchase');
                        $replaceproductid = str_replace('{productid}', $productid, $getmsgrpg);
                        if (strpos($getmsgrpg, '{productname}') !== false) {
                            $replaceproductid = str_replace('{productname}', $view_product, $getmsgrpg);
                        }
                        $replaceusername = str_replace('{usernickname}', $refuserid, $replaceproductid);
                        return $replaceusername;
                        break;
                    }
                case 'RVPPRRPG':
                    if ($order_status_changed == true) {
                        $getmsgrpg = get_option('_rs_log_revise_getting_referred_product_purchase');
                        $replaceproductid = str_replace('{productid}', $productid, $getmsgrpg);
                        return $replaceproductid;
                    } elseif ($user_deleted == true) {
                        $getmsgrpg = get_option('_rs_localize_revise_points_for_getting_referred_purchase');
                        $replaceproductid = str_replace('{productid}', $productid, $getmsgrpg);
                        if (strpos($getmsgrpg, '{productname}') !== false) {
                            $replaceproductid = str_replace('{productname}', $view_product, $getmsgrpg);
                        }
                        $replaceusername = str_replace('{usernickname}', $refuserid, $replaceproductid);
                        return $replaceusername;
                        break;
                    }
                    break;
                case 'RVPFRP':
                    $getmsgrpg = get_option('_rs_log_revise_points_redeemed_towards_purchase');
                    return $getmsgrpg;
                    break;
                case 'RVPFRRRP':
                    $getmsgrpg = get_option('_rs_localize_referral_account_signup_points_revised');
                    $replaceproductid = str_replace('{usernickname}', $refuserid, $getmsgrpg);
                    return $replaceproductid;
                    break;
                case 'RVPFRPVL':
                    $getmsgrpg = get_option('_rs_localize_reward_for_vk_like_revised');
                    return $getmsgrpg;
                    break;
                case 'RVPFRPGPOS':
                    $getmsgrpg = get_option('_rs_localize_reward_for_google_plus_revised');
                    return $getmsgrpg;
                    break;
                case 'RVPFRPFL':
                    $getmsgrpg = get_option('_rs_localize_reward_for_facebook_like_revised');
                    return $getmsgrpg;
                    break;
                case 'PPRPFN':
                    if ($masterlog == true) {
                        $getmsgrpg = get_option('_rs_localize_log_for_nominee');
                        $replaceproductid = str_replace('[points]', $earnpoints, $getmsgrpg);
                        $replaceproductid1 = str_replace('[user]', $nomineeid, $replaceproductid);
                        $replaceproductid2 = str_replace('[name]', $usernickname, $replaceproductid1);
                        return $replaceproductid2;
                        break;
                    } else {
                        $getmsgrpg = get_option('_rs_localize_log_for_nominee');
                        $replaceproductid = str_replace('[points]', $earnpoints, $getmsgrpg);
                        $replaceproductid1 = str_replace('[user]', $nomineeid, $replaceproductid);
                        $replaceproductid2 = str_replace('[name]', "You", $replaceproductid1);
                        return $replaceproductid2;
                        break;
                    }
                case 'PPRPFNP':
                    if ($masterlog == true) {
                        $getmsgrpg = get_option('_rs_localize_log_for_nominated_user');
                        $replaceproductid1 = str_replace('[user]', $nomineeid, $getmsgrpg);
                        $replaceproductid2 = str_replace('[points]', $nominatedpoints, $replaceproductid1);
                        $replaceproductid3 = str_replace('[name]', $usernickname, $replaceproductid2);
                        return $replaceproductid3;
                        break;
                    } else {
                        $getmsgrpg = get_option('_rs_localize_log_for_nominated_user');
                        $replaceproductid1 = str_replace('[user]', $nomineeid, $getmsgrpg);
                        $replaceproductid2 = str_replace('[points]', $nominatedpoints, $replaceproductid1);
                        $replaceproductid3 = str_replace('[name]', "Your", $replaceproductid2);
                        return $replaceproductid3;
                        break;
                    }
                case 'IMPADD':
                    $getmsgrpg = get_option('_rs_localize_log_for_import_add');
                    $replaceproductid2 = str_replace('[points]', $earnpoints, $getmsgrpg);
                    return $replaceproductid2;
                    break;
                case 'IMPOVR':
                    if ($masterlog == true) {
                        $getmsgrpg = get_option('_rs_localize_log_for_import_override');
                        $replaceproductid2 = str_replace('[points]', $earnpoints, $getmsgrpg);
                        return $replaceproductid2;
                        break;
                    } else {
                        $getmsgrpg = get_option('_rs_localize_log_for_import_override');
                        $replaceproductid2 = str_replace('[points]', "Your", $getmsgrpg);
                        return $replaceproductid2;
                        break;
                    }
                case 'RPFP':
                    $getmsgrpg = get_option('_rs_localize_points_earned_for_post');
                    $postname = get_the_title($productid);
                    $replacepostid = str_replace('{postid}', $postname, $getmsgrpg);
                    return $replacepostid;
                    break;
                case 'SP':
                    if ($masterlog == true) {
                        $getmsgrpg = get_option('_rs_localize_log_for_reciver');
                        $replaceproductid = str_replace('[points]', $earnpoints, $getmsgrpg);
                        $replaceproductid1 = str_replace('[user]', $nomineeid, $replaceproductid);
                        $replaceproductid2 = str_replace('[name]', $usernickname, $replaceproductid1);
                        return $replaceproductid2;
                        break;
                    } else {
                        $getmsgrpg = get_option('_rs_localize_log_for_reciver');
                        $replaceproductid = str_replace('[points]', $earnpoints, $getmsgrpg);
                        $replaceproductid1 = str_replace('[user]', $nomineeid, $replaceproductid);
                        $replaceproductid2 = str_replace('[name]', "You", $replaceproductid1);
                        return $replaceproductid2;
                        break;
                    }
                case 'RPCPAR':
                    $getmsg = get_option('_rs_localize_points_earned_for_page_review');
                    $postname = get_the_title($productid);
                    $replaceproductid = str_replace('{pagename}', $postname, $getmsg);
                    return $replaceproductid;
                    break;

                case 'SENPM':
                    if ($masterlog == true) {
                        $getmsgrpg = get_option('_rs_localize_log_for_sender');
                        $replaceproductid1 = str_replace('[user]', $nomineeid, $getmsgrpg);
                        $replaceproductid2 = str_replace('[points]', $redeempoints, $replaceproductid1);
                        $replaceproductid3 = str_replace('[name]', $usernickname, $replaceproductid2);
                        return $replaceproductid3;
                        break;
                    } else {
                        $getmsgrpg = get_option('_rs_localize_log_for_sender');
                        $replaceproductid1 = str_replace('[user]', $nomineeid, $getmsgrpg);
                        $replaceproductid2 = str_replace('[points]', $redeempoints, $replaceproductid1);
                        $replaceproductid3 = str_replace('[name]', "Your", $replaceproductid2);
                        return $replaceproductid3;
                        break;
                    }
                case 'SPB':
                    if ($masterlog == false) {
                        $getmsgrpg = get_option('_rs_localize_log_for_sender_after_submit');
                        return $getmsgrpg;
                        break;
                    }
                case 'SPA':
                    if ($masterlog == false) {
                        $getmsgrpg = get_option('_rs_localize_log_for_sender');
                        $replaceproductid1 = str_replace('[user]', $nomineeid, $getmsgrpg);
                        $replaceproductid2 = str_replace('[points]', $redeempoints, $replaceproductid1);
                        $replaceproductid3 = str_replace('[name]', "Your", $replaceproductid2);
                        return $replaceproductid3;
                        break;
                    }
                case 'SEP':
                    $getmsgrpg = get_option('_rs_localize_points_to_send_log_revised');
                    return $getmsgrpg;
                    break;
                case 'RPFURL':
                    $getmsgrpg = get_option('rs_message_for_pointurl');
                    $replacepoints = str_replace('[points]', $earnpoints, $getmsgrpg);
                    return $replacepoints;
                    break;
            }
        }

        public static function rewards_rs_order_status_control() {
            global $woocommerce;
            $orderslugs = array();
            $orderslugs1 = array();
            if (function_exists('wc_get_order_statuses')) {
                $orderslugss = str_replace('wc-', '', array_keys(wc_get_order_statuses()));
                foreach ($orderslugss as $value) {
                    if (is_array(get_option('rs_order_status_control'))) {
                        if (!in_array($value, get_option('rs_order_status_control'))) {
                            $orderslugs[] = $value;
                        }
                    }

                    if (is_array(get_option('rs_order_status_control_redeem'))) {
                        if (!in_array($value, get_option('rs_order_status_control_redeem'))) {
                            $orderslugs1[] = $value;
                        }
                    }
                }
            } else {
                $taxonomy = 'shop_order_status';
                $orderstatus = '';
                $term_args = array(
                    'hide_empty' => false,
                    'orderby' => 'date',
                );
                $tax_terms = get_terms($taxonomy, $term_args);
                foreach ($tax_terms as $getterms) {
                    if (is_array(get_option('rs_order_status_control'))) {
                        if (!in_array($getterms->slug, get_option('rs_order_status_control'))) {
                            $orderslugs[] = $getterms->slug;
                        }
                    }

                    if (is_array(get_option('rs_order_status_control_redeem'))) {
                        if (!in_array($getterms->slug, get_option('rs_order_status_control_redeem'))) {
                            $orderslugs1[] = $getterms->slug;
                        }
                    }
                }
            }
            update_option('rs_list_other_status_for_redeem', $orderslugs1);
            update_option('rs_list_other_status', $orderslugs);
        }

        public static function myrewardpoints_total_shortcode($content) {
            if (is_user_logged_in()) {
                ob_start();
                $userid = get_current_user_id();
                $getusermeta = self::get_sum_of_total_earned_points($userid);
                if ($getusermeta != '' && $getusermeta > 0) {
                    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                    echo get_option('rs_my_rewards_total') . " " . round(number_format((float) $getusermeta, 2, '.', ''), $roundofftype) . "</h4><br>";
                } else {
                    echo get_option('rs_my_rewards_total') . " " . " 0</h4><br>";
                }
                $content = ob_get_clean();
                return $content;
            } else {
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                $message = get_option('rs_message_shortcode_guest_display');
                $login = get_option('rs_message_shortcode_login_name');
                $msg = $message . ' <a href=' . $myaccountlink . '> ' . $login . '</a>';
                return '<br>' . $msg;
            }
        }

        public static function rs_fp_rewardsystem($atts) {
            if (is_user_logged_in()) {
                $get_user_type = get_option('rs_select_type_of_user_for_referral');
                $check_user_restriction = rs_function_to_check_the_restriction_for_referral($get_user_type);
                if ($check_user_restriction) {
                    ob_start();
                    extract(shortcode_atts(array(
                        'referralbutton' => 'show',
                        'referraltable' => 'show',
                                    ), $atts));
                    if ($referralbutton == 'show') {
                        RSFunctionForMyAccount::generate_referral_key();
                    }
                    if ($referraltable == 'show') {
                        RSFunctionForMyAccount::list_table_array();
                    }
                    $maincontent = ob_get_clean();
                    return $maincontent;
                } else {
                    $message = get_option('rs_msg_for_restricted_user');
                    if (get_option('rs_display_msg_when_access_is_prevented') === '1') {
                        echo '<br>' . $message;
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

        public static function shortcode_for_static_referral_link() {
            if (is_user_logged_in()) {
                $get_user_type = get_option('rs_select_type_of_user_for_referral');
                $check_user_restriction = rs_function_to_check_the_restriction_for_referral($get_user_type);
                if ($check_user_restriction) {
                    ob_start();
                    $currentuserid = get_current_user_id();
                    $objectcurrentuser = get_userdata($currentuserid);
                    if (get_option('rs_generate_referral_link_based_on_user') == '1') {
                        $referralperson = is_object($objectcurrentuser) ? $objectcurrentuser->user_login : 'Guest';
                    } else {
                        $referralperson = $currentuserid;
                    }

                    $refurl = add_query_arg('ref', $referralperson, get_option('rs_static_generate_link'));
                    ?><h3><?php echo get_option('rs_my_referral_link_button_label'); ?></h3><?php
                    echo $refurl;
                    $maincontent = ob_get_clean();
                    return $maincontent;
                } else {
                    $message = get_option('rs_msg_for_restricted_user');
                    if (get_option('rs_display_msg_when_access_is_prevented') === '1') {
                        echo '<br>' . $message;
                    }
                }
            } else {
                ob_start();
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                $linkforlogin = add_query_arg('redirect_to', get_permalink(), $myaccountlink);
                $message = get_option('rs_message_shortcode_guest_display');
                $login = get_option('rs_message_shortcode_login_name');
                echo '<br>' . $message . ' <a href=' . $linkforlogin . '> ' . $login . '</a>';
                $maincontent = ob_get_clean();
                return $maincontent;
            }
        }

        public static function delete_cookie_after_some_purchase($cookievalue) {
            $countnoofpurchase = '';
            $getnoofpurchase = get_user_meta(get_current_user_id(), 'rs_no_of_purchase_for_user', true);
            if ($getnoofpurchase != false) {
                $countnoofpurchase = count($getnoofpurchase);
            }
            $checkenable = get_option('rs_enable_delete_referral_cookie_after_first_purchase');
            $noofpurchase = get_option('rs_no_of_purchase');
            if ($checkenable == 'yes') {
                if (($noofpurchase != '') && ($noofpurchase != 0)) {
                    if ($countnoofpurchase >= $noofpurchase) {
                        setcookie('rsreferredusername', $cookievalue, time() - 3600, '/');
                    }
                }
            }
        }

    }

    RSPointExpiry::init();
}