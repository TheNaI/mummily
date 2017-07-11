<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_Reward_Points_WC_2P6')) {

    /*
     * Reward Points Compatible with 2.6 of WooCommerce
     */

    class FP_Reward_Points_WC_2P6 {

        public static function init() {
            add_action('wp_ajax_apply_sumo_reward_points', array(__CLASS__, 'apply_redeeming_points'), 999);
            add_action('wp_ajax_sumo_updated_cart_total', array(__CLASS__, 'recalculate_totals'), 999);
            add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'), 999);
            add_action('wp_ajax_sumo_remove_coupon', array(__CLASS__, 'remove_coupon_from_cart'), 999);
            add_action('wp_ajax_sumo_apply_coupon', array(__CLASS__, 'apply_coupon_from_cart'), 999);
            add_action('wp_ajax_rs_point_price_compatability', array(__CLASS__, 'combatibility_point_price'), 10);
        }

        public static function apply_coupon_from_cart() {
            $coupon = wc_clean($_POST['coupon']);
            WC()->cart->remove_coupon($coupon);
            RSFunctionForCart::get_reward_points_to_display_msg_in_cart_and_checkout();
            RSFunctionForCart::display_msg_in_cart_page();
            RSFunctionForCart::display_complete_message_cart_page();
            RSFunctionForCheckout::your_current_points_cart_page();
            RSFunctionForCart::display_msg_in_cart_page_for_balance_reward_points();
            RSFunctionForCart::display_redeem_points_buttons_on_cart_page();
            RSFunctionForCheckout::display_redeem_min_max_points_buttons_on_cart_page();
            woocommerce_cart_totals();
            die();
        }

        public static function combatibility_point_price() {
            if (get_option('rs_enable_disable_point_priceing') == 'yes') {
                global $post;
                $posted = array();
                parse_str($_POST['form'], $posted);
                if (isset($posted['add-to-cart'])) {
                    $booking_id = $posted['add-to-cart'];
                    $product = rs_get_product_object($booking_id);
                    $booking_form = new WC_Booking_Form($product);
                    $cost = $booking_form->calculate_booking_cost($posted);
                    $args = array('qty' => 1, 'price' => $cost);
                    if (is_wp_error($cost)) {
                        die(json_encode(array('sumorewardpoints' => 0)));
                    }
                    $tax_display_mode = get_option('woocommerce_tax_display_shop');
                    if (function_exists('wc_get_price_including_tax')) {
                        $price_to_display_inc = wc_get_price_including_tax($product, $args);
                    } else {
                        $price_to_display_inc = $product->get_price_including_tax(1, $cost);
                    }
                    if (function_exists('wc_get_price_excluding_tax')) {
                        $price_to_display_exc = wc_get_price_excluding_tax($product, $args);
                    } else {
                        $price_to_display_exc = $product->get_price_excluding_tax(1, $cost);
                    }
                    $display_price = $tax_display_mode == 'incl' ? $price_to_display_inc : $price_to_display_exc;
                    $product_price = $display_price;
                    $checkproducttype = rs_get_product_object($booking_id);
                    $product_id = $booking_id;
                    if (is_object($checkproducttype) && $checkproducttype->is_type('booking')) {
                        $product_level_fixed_price = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem__points');
                        $product_level_enable = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price');
                        $point_price_type = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_price_type');
                        $point_based_on_conversion = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_price_points_based_on_conversion');
                        $product_level_price_type = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_point_price_type');
                        $product_level_price_display_type = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price_type');
                        $global_reward_display_type = get_option('rs_global_point_priceing_type');
                        $data = array('0');
                        if ($product_level_enable == 'yes') {
                            if ($product_level_price_display_type == '2') {
                                $data[] = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem__points');
                            } else {
                                if ($product_level_price_type == '1') {
                                    if ($product_level_fixed_price == '') {
                                        $term = get_the_terms($product_id, 'product_cat');
                                        if (is_array($term)) {
                                            foreach ($term as $term) {
                                                $cat_level_enable = get_woocommerce_term_meta($term->term_id, 'enable_point_price_category', true);
                                                if (($cat_level_enable == 'yes') && ($cat_level_enable != '')) {
                                                    $cat_level_price_type = get_woocommerce_term_meta($term->term_id, 'point_price_category_type', true);
                                                    if ($cat_level_price_type == '1') {
                                                        $cat_level_fixed_price = get_woocommerce_term_meta($term->term_id, 'rs_category_points_price', true);
                                                        if ($cat_level_fixed_price == '') {
                                                            $data[] = self::rs_function_get_global_vlaue($product_price);
                                                        } else {
                                                            $data[] = $cat_level_fixed_price;
                                                        }
                                                    } else {
                                                        $newvalue = $product_price / wc_format_decimal(get_option('rs_redeem_point_value'));
                                                        $updatedvalue = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
                                                        $data[] = $updatedvalue;
                                                    }
                                                } else {
                                                    $data[] = self::rs_function_get_global_vlaue($product_price);
                                                }
                                            }
                                        } else {
                                            $data[] = self::rs_function_get_global_vlaue($product_price);
                                        }
                                    } else {
                                        $data[] = $product_level_fixed_price;
                                    }
                                } else {
                                    $newvalue = $product_price / wc_format_decimal(get_option('rs_redeem_point_value'));
                                    $updatedvalue = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
                                    $data[] = $updatedvalue;
                                }
                            }
                        } else {
                            $data[] = '';
                        }
                        if (!empty($data)) {
                            $getpointprice = max($data);
                        }
                    }
                    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                    $finalpointprice = round($getpointprice, $roundofftype);
                    $label = get_option('rs_label_for_point_value');
                    $labelposition = get_option('rs_sufix_prefix_point_price_label');
                    $replace = str_replace("/", "", $label);
                    if ($labelposition == '1') {
                        $pointpricemessage = $replace . $getpointprice;
                    } else {
                        $pointpricemessage = $getpointprice . $replace;
                    }
                    $label1 = '/';
                    if ($finalpointprice == '0' || $finalpointprice == '') {
                        $label = '';
                        $finalpointprice = '';
                        $label1 = '';
                    }
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'booking_points', $finalpointprice);
                    $type[] = check_display_price_type($product_id);
                    if (in_array(2, $type)) {
                        die(json_encode(array(
                            'result' => 'SUCCESS',
                            'html' => __('Booking cost', 'woocommerce-bookings') . ': <strong>' . $pointpricemessage . '</strong>'
                        )));
                    } else {
                        die(json_encode(array(
                            'result' => 'SUCCESS',
                            'html' => __('Booking cost', 'woocommerce-bookings') . ': <strong>' . wc_price($display_price) . $label1 . $pointpricemessage . '</strong>'
                        )));
                    }
                }
            }
        }

        public static function rs_function_get_global_vlaue($product_price) {
            $data = array();
            $global_enable = get_option('rs_local_enable_disable_point_price_for_product');
            $global_reward_type = get_option('rs_global_point_price_type');
            if ($global_enable == '1') {
                if ($global_reward_type == '1') {
                    if (get_option('rs_local_price_points_for_product') != '') {
                        $data[] = get_option('rs_local_price_points_for_product');
                    }
                } else {
                    $newvalue = $product_price / wc_format_decimal(get_option('rs_redeem_point_value'));
                    $updatedvalue = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
                    $data[] = $updatedvalue;
                }
            }
            return $data;
        }

        // Reward Points Compatible with Version 2.6 of WooCommerce
        public static function apply_redeeming_points() {
            global $woocommerce;
            if ($woocommerce->version === (float) ('2.6.0')) {
                RSFunctionToApplyCoupon::apply_matched_coupons();
                wc_print_notices();
                die();
            }
        }

        // Recalculate Totals
        public static function recalculate_totals() {
            if (!defined('WOOCOMMERCE_CART')) {
                define('WOOCOMMERCE_CART', true);
            }
            WC()->cart->calculate_totals();
            RSFunctionForCart::get_reward_points_to_display_msg_in_cart_and_checkout();
            RSFunctionForCart::display_msg_in_cart_page();
            RSFunctionForCart::display_complete_message_cart_page();
            RSFunctionForCheckout::your_current_points_cart_page();
            RSFunctionForCart::display_msg_in_cart_page_for_balance_reward_points();
            RSFunctionForCart::display_redeem_points_buttons_on_cart_page();
            RSFunctionForCheckout::display_redeem_min_max_points_buttons_on_cart_page();
            woocommerce_cart_totals();

            die();
        }

        // Remove Coupon from Cart

        public static function remove_coupon_from_cart() {
            if (isset($_POST['coupon'])) {
                $coupon = wc_clean($_POST['coupon']);
                WC()->cart->remove_coupon($coupon);
                RSFunctionForCart::get_reward_points_to_display_msg_in_cart_and_checkout();
                RSFunctionForCart::display_msg_in_cart_page();
                RSFunctionForCart::display_complete_message_cart_page();
                RSFunctionForCheckout::your_current_points_cart_page();
                RSFunctionForCart::display_msg_in_cart_page_for_balance_reward_points();
//                RSFunctionForCart::display_redeem_points_buttons_on_cart_page();
                RSFunctionForCheckout::display_redeem_min_max_points_buttons_on_cart_page();
                woocommerce_cart_totals();
            }
            die();
        }

        //register enqueue script for to perform redeeming on cart FP_Reward_Points_Main_Path
        public static function enqueue_scripts() {
            global $woocommerce;
            if ((float) $woocommerce->version >= (float) ('2.6.0')) {
                $minimum_points = get_option("rs_minimum_redeeming_points");
                $maximum_points = get_option("rs_maximum_redeeming_points");
                $error_msg_min_max = do_shortcode(addslashes(get_option("rs_minimum_and_maximum_redeem_point_error_message")));
                $error_msg_min = do_shortcode(addslashes(get_option("rs_minimum_redeem_point_error_message")));
                $error_msg_max = do_shortcode(addslashes(get_option("rs_maximum_redeem_point_error_message")));
                if (class_exists('WC_Bookings')) {
                    if (is_shop()) {
                        wp_enqueue_script('jquery');
                        wp_register_script('pointpricecompatibility', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/pointpricecompatibility.js");
                        $global_variable_for_js = array('wp_ajax_url' => admin_url('admin-ajax.php'), 'user_id' => get_current_user_id());
                        wp_localize_script('pointpricecompatibility', 'pointpricecompatibility_variable_js', $global_variable_for_js);
                        wp_enqueue_script('pointpricecompatibility', false, array(), '', true);
                    }
                }

                if (is_cart() && is_user_logged_in()) {
                    wp_enqueue_script('jquery');
                    wp_register_script('sumo_reward_points_wc2p6', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/sumorewardpoints_wc2p6.js");
                    $global_variable_for_js = array('wp_ajax_url' => admin_url('admin-ajax.php'), 'user_id' => get_current_user_id(), 'minimum_points' => $minimum_points, 'maximum_points' => $maximum_points, 'min_max_error' => $error_msg_min_max, 'min_error' => $error_msg_min, 'max_error' => $error_msg_max);
                    wp_localize_script('sumo_reward_points_wc2p6', 'sumo_global_variable_js', $global_variable_for_js);
                    wp_enqueue_script('sumo_reward_points_wc2p6', false, array(), '', true);
                }


                if (is_checkout() && is_user_logged_in()) {
                    wp_enqueue_script('jquery');
                    wp_register_script('checkoutscript', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/checkoutscript.js");
                    $global_variable_for_js = array('wp_ajax_url' => admin_url('admin-ajax.php'), 'user_id' => get_current_user_id());
                    wp_localize_script('checkoutscript', 'checkoutscript_variable_js', $global_variable_for_js);
                    wp_enqueue_script('checkoutscript', false, array(), '', true);
                }

                //Form For Refer a Friend
                $refername = addslashes(get_option('rs_my_rewards_friend_name_error_message'));
                $referemail = addslashes(get_option('rs_my_rewards_friend_email_error_message'));
                $invalidemail = addslashes(get_option('rs_my_rewards_friend_email_is_not_valid'));
                $subject = addslashes(get_option('rs_my_rewards_email_subject_error_message'));
                $message = addslashes(get_option('rs_my_rewards_email_message_error_message'));
                $termandcondition = get_option('rs_show_hide_iagree_termsandcondition_field');

                wp_enqueue_script('jquery');
                wp_register_script('referfriend', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/referfriend.js");
                $global_variable_for_js = array('wp_ajax_url' => admin_url('admin-ajax.php'), 'user_id' => get_current_user_id(), 'refnameerrormsg' => $refername, 'refmailiderrormsg' => $referemail, 'invalidemail' => $invalidemail, 'subjecterror' => $subject, 'messageerror' => $message, 'enableterms' => $termandcondition);
                wp_localize_script('referfriend', 'referfriend_variable_js', $global_variable_for_js);

                //Form For Cashback Request
                $currentuserpoints = RSPointExpiry::get_sum_of_total_earned_points(get_current_user_id());
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                $currentuserpoints = round($currentuserpoints, $roundofftype);
                $rs_minimum_points_for_encash = get_option('rs_minimum_points_encashing_request') != '' ? get_option('rs_minimum_points_encashing_request') : 0;
                $rs_maximum_points_for_encash = get_option('rs_maximum_points_encashing_request') != '' ? get_option('rs_maximum_points_encashing_request') : $currentuserpoints;
                $select_payment_method = get_option('rs_select_payment_method');
                $redeempoint_for_cashback = get_option('rs_redeem_point_for_cash_back');
                $redeempoint_value_for_cashback = get_option('rs_redeem_point_value_for_cash_back');
                if (is_user_logged_in()) {
                    $user_details = get_user_by('id', get_current_user_id());
                    $username = $user_details->user_login;
                } else {
                    $username = 'Guest';
                }
                wp_enqueue_script('jquery');
                wp_register_script('encashform', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/encashformscript.js");
                $encash_global_variable_for_js = array('wp_ajax_url' => admin_url('admin-ajax.php'), 'user_id' => get_current_user_id(), 'currentuserpoint' => $currentuserpoints, 'minimumpointforencash' => $rs_minimum_points_for_encash, 'maximumpointforencash' => $rs_maximum_points_for_encash, 'selectpaymentmethod' => $select_payment_method, 'redeempointforcashback' => $redeempoint_for_cashback, 'redeempointvalueforcashback' => $redeempoint_value_for_cashback, 'username' => $username);
                wp_localize_script('encashform', 'encashform_variable_js', $encash_global_variable_for_js);

                //Form For Send Points
                $currentuserpoints = RSPointExpiry::get_sum_of_total_earned_points(get_current_user_id());
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                $currentuserpoints = round($currentuserpoints, $roundofftype);
                if (get_option('rs_limit_send_points_request') != '') {
                    $limitotsendpointsreq = get_option('rs_limit_send_points_request');
                } else {
                    $limitotsendpointsreq = '0';
                }
                $limitotsendpoints = get_option('rs_limit_for_send_point');
                if (is_user_logged_in()) {
                    $user_details = get_user_by('id', get_current_user_id());
                    $username = $user_details->user_login;
                } else {
                    $username = 'Guest';
                }
                $pointsemptyerror = get_option('rs_err_when_point_field_empty');
                $errorforgreaterpoints = get_option('rs_error_msg_when_points_is_more');
                $errorforlesserpoints = get_option('rs_error_msg_when_points_is_less');                
                $limit_error = get_option('rs_err_when_point_greater_than_limit');
                $user_empty = get_option('rs_err_for_empty_user');
                $point_not_number = get_option('rs_err_when_point_is_not_number');
                $selectusertype = get_option('rs_select_send_points_user_type');
                $limitmessage = get_option("rs_err_when_point_greater_than_limit");
                $value = get_option('rs_limit_send_points_request');
                $replace = str_replace('{limitpoints}', $value, $limitmessage);
                if (get_option('rs_request_approval_type') == '1') {
                    $success_info = get_option('rs_message_send_point_request_submitted');
                } else {
                    $success_info = get_option('rs_message_send_point_request_submitted_for_auto');
                }
                wp_enqueue_script('jquery');
                wp_register_script('formforsendpoints', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/formforsendpointsscript.js");
                $sendpoints_global_variable_for_js = array('wp_ajax_url' => admin_url('admin-ajax.php'), 'success_info' => $success_info, 'point_emp_err' => $pointsemptyerror, 'point_not_num' => $point_not_number, 'user_emty_err' => $user_empty, 'limit_err' => $replace, 'user_id' => get_current_user_id(), 'currentuserpoint' => $currentuserpoints, 'limittosendreq' => $limitotsendpointsreq, 'sendpointlimit' => $limitotsendpoints, 'username' => $username, 'selecttype' => $selectusertype, 'errorforgreaterpoints' => $errorforgreaterpoints,'errorforlesserpoints' => $errorforlesserpoints);
                wp_localize_script('formforsendpoints', 'formforsendpoints_variable_js', $sendpoints_global_variable_for_js);

                //For Gift Voucher Redeem Field Shortcode 
                $error_msg = addslashes(get_option('rs_voucher_redeem_empty_error'));
                wp_enqueue_script('jquery');
                wp_register_script('giftvoucher', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/giftvoucher.js");
                $global_variable_for_js = array('wp_ajax_url' => admin_url('admin-ajax.php'), 'user_id' => get_current_user_id(), 'error' => $error_msg);
                wp_localize_script('giftvoucher', 'giftvoucher_variable_js', $global_variable_for_js);

                //For Cashback Table Shortcode 
                wp_enqueue_script('jquery');
                wp_register_script('encashform1', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/encashform.js");
                $global_variable_for_js = array('wp_ajax_url' => admin_url('admin-ajax.php'), 'user_id' => get_current_user_id());
                wp_localize_script('encashform1', 'encashform1_variable_js', $global_variable_for_js);

                //social button
                wp_enqueue_script('jquery');
                wp_register_script('socialbutton', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/socialbutton.js");
            }
        }

    }

    FP_Reward_Points_WC_2P6::init();
}