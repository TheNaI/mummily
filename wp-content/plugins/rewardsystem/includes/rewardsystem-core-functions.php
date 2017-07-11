<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


/* @return list of user roles */

function fp_rs_get_user_roles() {
    global $wp_roles;
    foreach ($wp_roles->roles as $values => $key) {
        $userroleslug[] = $values;
        $userrolename[] = $key['name'];
    }
    $user_roles = array_combine((array) $userroleslug, (array) $userrolename);
    return $user_roles;
}

/*
 * @return list of product categories
 */

function fp_rs_get_product_category() {
    $categorylist = array();
    $categoryname = array();
    $categoryid = array();
    $particularcategory = get_terms('product_cat');
    if (!is_wp_error($particularcategory)) {
        if (!empty($particularcategory)) {
            if (is_array($particularcategory)) {
                foreach ($particularcategory as $category) {
                    $categoryname[] = $category->name;
                    $categoryid[] = $category->term_id;
                }
            }
            $categorylist = array_combine((array) $categoryid, (array) $categoryname);
        }
    }
    return $categorylist;
}

/*
 * Get all Order Statuses
 */

function fp_rs_get_all_order_status() {
    $order_statuses = '';
    if (function_exists('wc_get_order_statuses')) {
        $orderstatus = str_replace('wc-', '', array_keys(wc_get_order_statuses()));
        $orderslugs = array_values(wc_get_order_statuses());
        $order_statuses = array_combine((array) $orderstatus, (array) $orderslugs);
    } else {
        $taxonomy = 'shop_order_status';
        $orderstatus = '';
        $orderslugs = '';

        $term_args = array(
            'hide_empty' => false,
            'orderby' => 'date',
        );
        $tax_terms = get_terms($taxonomy, $term_args);
        if (is_array($tax_terms) && !empty($tax_terms)) {
            foreach ($tax_terms as $getterms) {
                if (is_object($getterms)) {
                    $orderstatus[] = $getterms->name;
                    $orderslugs[] = $getterms->slug;
                }
            }
        }
        $order_statuses = array_combine((array) $orderslugs, (array) $orderstatus);
    }
    return $order_statuses;
}

/*
 * @return nonce value for security purpose of ajax
 */

function rs_function_to_create_security() {
    $secure = wp_create_nonce('sumo-reward-points');
    return $secure;
}

/*
 * @return bool Value if Nonce is verified
 */

function rs_function_to_verify_secure($secure) {
    if (wp_verify_nonce($secure, 'sumo-reward-points')) {
        return true;
    } else {
        return false;
    }
}

function earn_point_conversion() {
    return wc_format_decimal(get_option('rs_earn_point'));
}

function earn_point_conversion_value() {
    return wc_format_decimal(get_option('rs_earn_point_value'));
}

function multi_dimensional_descending_sort_coupon_points($arr, $index) {
    $b = array();
    $c = array();
    if (is_array($arr)) {
        foreach ($arr as $key => $value) {
            $b[$key] = $value[$index];
        }
        arsort($b);
        foreach ($b as $key => $value) {
            $c[] = $arr[$key];
        }

        return $c;
    }
}

function rs_get_referer_id_linking_rule($linkarray, $field, $value) {
    if (is_array($linkarray)) {
        foreach ($linkarray as $key => $eachreferer) {
            if ($eachreferer[$field] == $value)
                return $eachreferer['referer'];
        }
    }
    return FALSE;
}

function rs_perform_manual_link_referer($buyer_id) {
    $data = get_option('rewards_dynamic_rule_manual');
    return rs_get_referer_id_linking_rule($data, "refferal", $buyer_id);
}

function apply_coupon_code_reward_points_user($order_id) {
    $order = new WC_order($order_id);
    $order_user_id = rs_get_order_obj($order);
    $order_user_id = $order_user_id['order_userid'];
    $coupons_applied_in_cart = array();
    if (get_option('rs_choose_priority_level_selection_coupon_points') == '1') {
        $coupons_for_points_rule_list = multi_dimensional_descending_sort_coupon_points(get_option('rewards_dynamic_rule_couponpoints'), 'reward_points');
    } else {
        $coupons_for_points_rule_list = RSMemberFunction::multi_dimensional_sort(get_option('rewards_dynamic_rule_couponpoints'), 'reward_points');
    }
    $getthedatas = array();
    if (is_array($coupons_for_points_rule_list)) {
        if (!empty($coupons_for_points_rule_list)) {
            foreach ($coupons_for_points_rule_list as $key => $value) {
                if (!in_array($value['coupon_codes'], $getthedatas)) {
                    $getthedatas[$key] = $value['coupon_codes'];
                }
            }
        }
    }
    $c = array();
    foreach ($getthedatas as $key => $mainvalue) {
        $c[] = $coupons_for_points_rule_list[$key];
    }

    $rewardpointscoupons = $order->get_items(array('coupon'));
    foreach ($rewardpointscoupons as $applied_coupons) {
        $coupons_applied_in_cart[] = $applied_coupons['name'];
    }
    foreach ($c as $coupons_for_points_each_rule) {
        global $woocommerce;
        $rule_created_coupons_list = $coupons_for_points_each_rule["coupon_codes"];
        $rule_created_coupons_points_list = $coupons_for_points_each_rule["reward_points"];
        foreach ($rule_created_coupons_list as $separate_rule_coupons) {
            $coupon_name_shortcode_to_find = "[coupon_name]";
            $coupon_name_shortcode_to_replace = $separate_rule_coupons;
            $coupon_reward_points_to_update = $rule_created_coupons_points_list;
            $newfunctionchecker = RSFunctionForCouponRewardPoints::find_coupon_values($separate_rule_coupons, $coupons_applied_in_cart);
            $new_obj = new RewardPointsOrder($order_id, $apply_previous_order_points = 'no');
            if ((int) $newfunctionchecker == 1) {
                $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                if ($enabledisablemaxpoints == 'yes') {
                    $new_obj->check_point_restriction($restrictuserpoints, $coupon_reward_points_to_update, $pointsredeemed = 0, $event_slug = 'RPC', $order_user_id, $nomineeid = '', $referrer_id = '', $product_id = '', $variationid = '', $reasonindetail = '');
                } else {
                    $equearnamt = RSPointExpiry::earning_conversion_settings($coupon_reward_points_to_update);
                    $valuestoinsert = array('pointstoinsert' => $coupon_reward_points_to_update, 'pointsredeemed' => 0, 'event_slug' => 'RPC', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $order_user_id, 'referred_id' => '', 'product_id' => '', 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $coupon_reward_points_to_update, 'totalredeempoints' => 0);
                    $new_obj->total_points_management($valuestoinsert);
                }
            }
        }
    }
    do_action('fp_reward_point_for_using_coupons');
}

function rsmail_sending_on_custom_rule($user_id, $order_id) {
    global $wpdb;
    global $woocommerce;
    $emailtemplate_table_name = $wpdb->prefix . 'rs_templates_email';
    $email_templates = $wpdb->get_results("SELECT * FROM $emailtemplate_table_name"); //all email templates        
    if (is_array($email_templates)) {
        foreach ($email_templates as $emails) {
            if ($emails->rs_status == "ACTIVE") {
                if ($emails->mailsendingoptions == '1') {
                    if ($emails->rsmailsendingoptions == '1') {
                        if (get_option('rsearningtemplates' . $emails->id) != '1') {
                            if ($emails->sendmail_options == '1') {
                                include'frontend/rsmailsendingearning.php';
                            } else {
                                include'frontend/rsmailsendingearning2.php';
                            }
                            update_option('rsearningtemplates' . $emails->id, '1');
                        }
                    }

                    if ($emails->rsmailsendingoptions == '2') {
                        if (get_option('rsredeemingtemplates' . $emails->id) != '1') {
                            if ($emails->sendmail_options == '1') {
                                include'frontend/rsmailsendingredeeming.php';
                            } else {
                                include'frontend/rsmailsendingredeeming2.php';
                            }
                            update_option('rsredeemingtemplates' . $emails->id, '1');
                        }
                    }
                } else {
                    if ($emails->rsmailsendingoptions == '1') {
                        if ($emails->sendmail_options == '1') {
                            include'frontend/rsmailsendingearning.php';
                        } else {
                            include'frontend/rsmailsendingearning2.php';
                        }
                    }
                    if ($emails->rsmailsendingoptions == '2') {
                        if ($emails->sendmail_options == '1') {
                            include 'frontend/rsmailsendingredeeming.php';
                        } else {
                            include 'frontend/rsmailsendingredeeming2.php';
                        }
                    }
                }
            }
        }
    }
}

function display_total_currency_value($getcurrentuserid) {
    $getoldpoints = RSPointExpiry::get_sum_of_total_earned_points($getcurrentuserid);
    $point_control = wc_format_decimal(get_option('rs_redeem_point'));
    $point_control_price = wc_format_decimal(get_option('rs_redeem_point_value')); //i.e., 100 Points is equal to $1
    $revised_amount = $getoldpoints * $point_control_price;
    $coupon_value_in_points = $revised_amount / $point_control;
    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
    return round($getoldpoints, $roundofftype) . '(' . get_woocommerce_formatted_price(round($coupon_value_in_points, $roundofftype)) . ')';
}

function calculate_point_price_for_products($product_id) {
    $checkproduct = rs_get_product_object($product_id);
    if (get_option('rs_enable_disable_point_priceing') == '1') {
        $global_enable = get_option('rs_local_enable_disable_point_price_for_product');
        $points = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem__points');
        $enable = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price');
        $checkenablevariation = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_price');
        $variablerewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, 'price_points');
        $point_price_type = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_price_type');
        $point_based_on_conversion = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_price_points_based_on_conversion');
        $simple_product_type = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_point_price_type');
        $simple_product_price = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem__points_based_on_conversion');
        $price_display_type = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price_type');
        $global_reward_type = get_option('rs_global_point_price_type');
        $global_reward_display_type = get_option('rs_global_point_priceing_type');
        $checkproduct = rs_get_product_object($product_id);
        if (is_object($checkproduct) && ($checkproduct->is_type('simple') || ($checkproduct->is_type('subscription')) || ($checkproduct->is_type('lottery')))) {
            if ($enable == 'yes') {
                if ($price_display_type == '2') {
                    $data[$product_id] = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem__points');
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                } else {
                    if ($simple_product_type == 1) {
                        if ($points == '') {
                            $term = get_the_terms($product_id, 'product_cat');
                            if (is_array($term)) {
                                foreach ($term as $term) {
                                    $enablevalue = get_woocommerce_term_meta($term->term_id, 'enable_point_price_category', true);
                                    $display_type_price = get_woocommerce_term_meta($term->term_id, 'point_priceing_category_type', true);
                                    if (($enablevalue == 'yes') && ($enablevalue != '')) {

                                        $display_type = get_woocommerce_term_meta($term->term_id, 'point_price_category_type', true);
                                        if ($display_type == '1') {
                                            $checktermpoints = get_woocommerce_term_meta($term->term_id, 'rs_category_points_price', true);
                                            if ($checktermpoints == '') {
                                                if ($global_enable == '1') {
                                                    if ($global_reward_type == '1') {
                                                        if (get_option('rs_local_price_points_for_product') != '') {
                                                            $data[$product_id] = get_option('rs_local_price_points_for_product');
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                                        } else {
                                                            $data[$product_id] = '';
                                                        }
                                                    } else {
                                                        $data[$product_id] = get_sale_and_regular_price($product_id);
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                                    }
                                                } else {
                                                    $data[$product_id] = '';
                                                }
                                            } else {
                                                $data[$product_id] = get_woocommerce_term_meta($term->term_id, 'rs_category_points_price', true);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                            }
                                        } else {
                                            $data[$product_id] = get_sale_and_regular_price($product_id);
                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                        }
                                    } else {
                                        if ($global_enable == '1') {

                                            if ($global_reward_type == '1') {
                                                if (get_option('rs_local_price_points_for_product') != '') {
                                                    $data[$product_id] = get_option('rs_local_price_points_for_product');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                                } else {
                                                    $data[$product_id] = '';
                                                }
                                            } else {
                                                $data[$product_id] = get_sale_and_regular_price($product_id);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                            }
                                        } else {
                                            $data[$product_id] = '';
                                        }
                                    }
                                }
                            } else {
                                $global_enable = get_option('rs_local_enable_disable_point_price_for_product');
                                $global_reward_type = get_option('rs_global_point_price_type');
                                if ($global_enable == '1') {
                                    if ($global_reward_type == '1') {
                                        if (get_option('rs_local_price_points_for_product') != '') {
                                            $data[$product_id] = get_option('rs_local_price_points_for_product');
                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                        } else {
                                            $data[$product_id] = '';
                                        }
                                    } else {
                                        $data[$product_id] = get_sale_and_regular_price($product_id);
                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                    }
                                } else {

                                    $data[$product_id] = '';
                                }
                            }
                        } else {
                            $data[$product_id] = $points;
                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                        }
                    } else {
                        $data[$product_id] = get_sale_and_regular_price($product_id);
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                    }
                }
            } else {
                $data[$product_id] = '';
            }
        } else {
            if ($checkenablevariation == '1') {
                $price_display_type = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_pricing_type', true);
                if ($price_display_type == '2') {
                    $data[$product_id] = $variablerewardpoints;
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                } else {
                    if ($point_price_type == 1) {
                        if (wp_get_post_parent_id($product_id) != '0') {
                            $parentvariationid = new WC_Product_Variation($product_id);
                            $newparentid = rs_get_parent_id($parentvariationid);
                        } else {
                            $newparentid = $product_id;
                        }
                        if ($variablerewardpoints == '') {
                            $term = get_the_terms($newparentid, 'product_cat');
                            if (is_array($term)) {

                                foreach ($term as $term) {
                                    $enablevalue = get_woocommerce_term_meta($term->term_id, 'enable_point_price_category', true);

                                    if (($enablevalue == 'yes') && ($enablevalue != '')) {

                                        $display_type = get_woocommerce_term_meta($term->term_id, 'point_price_category_type', true);
                                        if ($display_type == '1') {
                                            $checktermpoints = get_woocommerce_term_meta($term->term_id, 'rs_category_points_price', true);
                                            if ($checktermpoints == '') {
                                                if ($global_enable == '1') {
                                                    if ($global_reward_type == '1') {
                                                        if (get_option('rs_local_price_points_for_product') != '') {
                                                            $data[$product_id] = get_option('rs_local_price_points_for_product');
                                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                                        } else {
                                                            $data[$product_id] = '';
                                                        }
                                                    } else {
                                                        $data[$product_id] = get_sale_and_regular_price($product_id);
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                                    }
                                                } else {
                                                    $data[$product_id] = '';
                                                }
                                            } else {
                                                $data[$product_id] = get_woocommerce_term_meta($term->term_id, 'rs_category_points_price', true);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                            }
                                        } else {
                                            $data[$product_id] = get_sale_and_regular_price($product_id);
                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                        }
                                    } else {
                                        if ($global_enable == '1') {
                                            if ($global_reward_type == '1') {
                                                if (get_option('rs_local_price_points_for_product') != '') {
                                                    $data[$product_id] = get_option('rs_local_price_points_for_product');
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                                } else {
                                                    $data[$product_id] = '';
                                                }
                                            } else {
                                                $data[$product_id] = get_sale_and_regular_price($product_id);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                            }
                                        } else {
                                            $data[$product_id] = '';
                                        }
                                    }
                                }
                            } else {
                                $global_enable = get_option('rs_local_enable_disable_point_price_for_product');
                                $global_reward_type = get_option('rs_global_point_price_type');
                                if ($global_enable == '1') {
                                    if ($global_reward_type == '1') {
                                        if (get_option('rs_local_price_points_for_product') != '') {
                                            $data[$product_id] = get_option('rs_local_price_points_for_product');
                                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                                        } else {
                                            $data[$product_id] = '';
                                        }
                                    } else {
                                        $data[$product_id] = get_sale_and_regular_price($product_id);
                                    }
                                } else {

                                    $data[$product_id] = '';
                                }
                            }
                        } else {

                            $data[$product_id] = $variablerewardpoints;
                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                        }
                    } else {
                        $data[$product_id] = get_sale_and_regular_price($product_id);

                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($product_id, 'pointsvalueenable', '1');
                    }
                }
            } else {
                $data[$product_id] = '';
            }
        }
    } else {
        $data[$product_id] = '';
    }
    if (is_object($checkproduct) && ($checkproduct->is_type('booking'))) {
        $booking_points = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, 'booking_points');
        $data[$product_id] = $booking_points;
    }
    return $data;
}

function get_sale_and_regular_price($product_id) {
    $product = rs_get_product_object($product_id);
    $product_price = rs_get_sale_or_regular_price($product);
    $newvalue = $product_price / wc_format_decimal(get_option('rs_redeem_point_value'));
    $updatedvalue = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
    return $updatedvalue;
}

function check_display_price_type($product_id) {
    if (get_option('rs_enable_disable_point_priceing') == '1') {
        $termid = '';
        //Product Level
        $points = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem__points');
        $enable = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price');
        $checkenablevariation = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_price');
        $variablerewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, 'price_points');
        $point_price_type = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_price_type');
        $point_based_on_conversion = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_price_points_based_on_conversion');
        $simple_product_type = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_point_price_type');
        $simple_product_price = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem__points_based_on_conversion');
        $typeofprice = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price_type');
        $productlevel = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price') != '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_price');
        $productlevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_point_price_type') != '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_point_price_type') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_price_type');
        $productlevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem__points') != '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem__points') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, 'price_points');
        $productdispalytype = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price_type') != '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price_type') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_pricing_type');
        //Global Level
        $global_enable = get_option('rs_local_enable_disable_point_price_for_product');
        $global_reward_type = get_option('rs_global_point_price_type');
        $global_rewardpoints = get_option('rs_local_price_points_for_product');
        $global_display_typt = get_option('rs_global_point_priceing_type');
        if (($productlevel == 'yes') || ($productlevel == '1')) {
            if ($productlevelrewardpoints != '') {
                if ($productdispalytype == '1') {
                    return '1';
                } else {
                    return '2';
                }
            }
        } else {
            return '0';
        }
    }
}

function get_discount_price($product_id) {
    $product = new WC_Product($product_id);
    return $product->get_price();
}

function rs_common_function_to_get_earned_points_for_order($orderid) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'rsrecordpoints';
    $order_obj = new WC_Order($orderid);
    $overall_earned_totals = array();
    $revised_earned_totals = array();
    $order_status = rs_get_order_obj($order_obj);
    $order_status = $order_status['order_status'];
    $order_status = str_replace('wc-', '', $order_status);
    $getoverallearnpoints = $wpdb->get_results("SELECT earnedpoints FROM $table_name WHERE orderid=$orderid and checkpoints != 'RVPFRP'", ARRAY_A);
    foreach ($getoverallearnpoints as $getoverallearnpointss) {
        $overall_earned_totals[] = $getoverallearnpointss['earnedpoints'];
    }
    $getoverallredeempoints = $wpdb->get_results("SELECT redeempoints FROM $table_name WHERE orderid=$orderid and checkpoints != 'RVPFPPRP'", ARRAY_A);
    foreach ($getoverallredeempoints as $getoverallredeempointss) {
        $overall_redeem_totals[] = $getoverallredeempointss['redeempoints'];
    }
    $gettotalearnpoints = $wpdb->get_results("SELECT earnedpoints FROM $table_name WHERE checkpoints = 'PPRP' and orderid=$orderid", ARRAY_A);
    foreach ($gettotalearnpoints as $gettotalearnpointss) {
        $earned_totals[] = $gettotalearnpointss['earnedpoints'];
    }
    $getrevisedearnedpoint = $wpdb->get_results("SELECT redeempoints FROM $table_name WHERE checkpoints = 'RVPFPPRP' and orderid=$orderid", ARRAY_A);
    foreach ($getrevisedearnedpoint as $getrevisedearnedpoints) {
        $revised_earned_totals[] = $getrevisedearnedpoints['redeempoints'];
    }
    $gettotalredeempoints = $wpdb->get_results("SELECT redeempoints FROM $table_name WHERE checkpoints = 'RP' and orderid=$orderid", ARRAY_A);
    foreach ($gettotalredeempoints as $gettotalredeempointss) {
        $redeem_totals[] = $gettotalredeempointss['redeempoints'];
    }
    $getrevisedredeempoints = $wpdb->get_results("SELECT earnedpoints FROM $table_name WHERE checkpoints = 'RVPFRP' and orderid=$orderid", ARRAY_A);
    foreach ($getrevisedredeempoints as $getrevisedredeempointss) {
        $revised_redeem_totals[] = $getrevisedredeempointss['earnedpoints'];
    }
    $totalearnedvalue = array_sum($overall_earned_totals) - array_sum($revised_earned_totals);
    return $totalearnedvalue;
}

function rs_common_function_to_get_redeem_points_for_order($orderid) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'rsrecordpoints';
    $order_obj = new WC_Order($orderid);
    $order_status = rs_get_order_obj($order_obj);
    $order_status = $order_status['order_status'];
    $order_status = str_replace('wc-', '', $order_status);
    $overall_redeem_totals = array(0);
    $revised_redeem_totals = array(0);
    $getoverallearnpoints = $wpdb->get_results("SELECT earnedpoints FROM $table_name WHERE orderid=$orderid and checkpoints != 'RVPFRP'", ARRAY_A);
    foreach ($getoverallearnpoints as $getoverallearnpointss) {
        $overall_earned_totals[] = $getoverallearnpointss['earnedpoints'];
    }
    $getoverallredeempoints = $wpdb->get_results("SELECT redeempoints FROM $table_name WHERE orderid=$orderid and checkpoints != 'RVPFPPRP'", ARRAY_A);
    foreach ($getoverallredeempoints as $getoverallredeempointss) {
        $overall_redeem_totals[] = $getoverallredeempointss['redeempoints'];
    }
    $gettotalearnpoints = $wpdb->get_results("SELECT earnedpoints FROM $table_name WHERE checkpoints = 'PPRP' and orderid=$orderid", ARRAY_A);
    foreach ($gettotalearnpoints as $gettotalearnpointss) {
        $earned_totals[] = $gettotalearnpointss['earnedpoints'];
    }
    $getrevisedearnedpoint = $wpdb->get_results("SELECT redeempoints FROM $table_name WHERE checkpoints = 'RVPFPPRP' and orderid=$orderid", ARRAY_A);
    foreach ($getrevisedearnedpoint as $getrevisedearnedpoints) {
        $revised_earned_totals[] = $getrevisedearnedpoints['redeempoints'];
    }
    $gettotalredeempoints = $wpdb->get_results("SELECT redeempoints FROM $table_name WHERE checkpoints = 'RP' and orderid=$orderid", ARRAY_A);
    foreach ($gettotalredeempoints as $gettotalredeempointss) {
        $redeem_totals[] = $gettotalredeempointss['redeempoints'];
    }
    $getrevisedredeempoints = $wpdb->get_results("SELECT earnedpoints FROM $table_name WHERE checkpoints = 'RVPFRP' and orderid=$orderid", ARRAY_A);
    foreach ($getrevisedredeempoints as $getrevisedredeempointss) {
        $revised_redeem_totals[] = $getrevisedredeempointss['earnedpoints'];
    }
    $totalredeemvalue = array_sum($overall_redeem_totals) - array_sum($revised_redeem_totals);
    return $totalredeemvalue;
}

function footer_link() {
    global $unsublink2;

    return $unsublink2;
}

/* Function to return Payment Gateway Reward Points */

function rs_function_to_get_gateway_point($order_id, $userid, $gatewayid) {
    if (get_option('rs_reward_type_for_payment_gateways_' . $gatewayid) == '1') {
        $gatewaypoints = get_option('rs_reward_payment_gateways_' . $gatewayid);
    } else {
        $percentpoints = get_option('rs_reward_points_for_payment_gateways_in_percent_' . $gatewayid);
        $pointforconversion = earn_point_conversion();
        $pointforconversionvalue = earn_point_conversion_value();
        $cart_subtotal = get_post_meta($order_id, 'rs_cart_subtotal', true) == '' ? rs_function_to_get_cart_subtotal() : get_post_meta($order_id, 'rs_cart_subtotal', true);
        $reward_percent = (float) $percentpoints / 100;
        $cart_average_value = $reward_percent * $cart_subtotal;
        $point_coversion = $cart_average_value * $pointforconversion;
        $gatewaypoints = $point_coversion / $pointforconversionvalue;
    }
    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
    $gatewaypointss = round($gatewaypoints, $roundofftype);
    $getthevalue = RSMemberFunction::user_role_based_reward_points($userid, $gatewaypointss);
    return $getthevalue;
}

function rs_function_to_update_cart_subtotal($order_id) {
    $cart_subtotal = rs_function_to_get_cart_subtotal();
    update_post_meta($order_id, 'rs_cart_subtotal', $cart_subtotal);
}

/* Function to return Cart Subtotal */

function rs_function_to_get_cart_subtotal() {
    global $woocommerce;
    if (get_option('woocommerce_prices_include_tax') == 'yes' && get_option('woocommerce_tax_display_cart') == 'incl') {
        $cart_subtotal = $woocommerce->cart->subtotal;
    } elseif (get_option('woocommerce_prices_include_tax') == 'yes' && get_option('woocommerce_tax_display_cart') == 'excl') {
        $cart_subtotal = $woocommerce->cart->subtotal_ex_tax;
    } elseif (get_option('woocommerce_prices_include_tax') == 'no' && get_option('woocommerce_tax_display_cart') == 'incl') {
        $cart_subtotal = $woocommerce->cart->subtotal;
    } else {
        $cart_subtotal = $woocommerce->cart->subtotal_ex_tax;
    }
    return $cart_subtotal;
}

/* Function to return whether to award Product Purchase Reward Points for Renewal Orders */

function rs_function_to_provide_points_for_renewal_order($order_id) {
    if (get_option('rs_award_point_for_renewal_order') == 'yes' && get_post_meta($order_id, 'sumo_renewal_order_date', true) != '') {
        return false;
    } else {
        return true;
    }
}

/* Function to return whether to award Referral Product Purchase Reward Points for Renewal Orders */

function rs_function_to_provide_referral_points_for_renewal_order($order_id) {
    if (get_option('rs_award_referral_point_for_renewal_order') == 'yes' && get_post_meta($order_id, 'sumo_renewal_order_date', true) != '') {
        return false;
    } else {
        return true;
    }
}

/* Function For Checking in Which level Reward points is Awarded */

function check_level_of_enable_reward_point($productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor) {
    return is_product_level($productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor);
}

function is_product_level($productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor) {
    //Product Level    
    $itemquantity = isset($item['qty']) ? $item['qty'] : $item['quantity'];
    if ($referred_user != '') {
        $productlevel = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_rewardsystemcheckboxvalue') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_enable_reward_points');
        $productlevelrewardtype = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_referral_rewardsystem_options') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_select_referral_reward_rule');
        $productlevelrewardpoints = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_referralrewardsystempoints') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_referral_reward_points');
        $productlevelrewardpercent = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_referralrewardsystempercent') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_referral_reward_percent');
        if ($getting_referrer == 'yes') {
            $productlevel = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_rewardsystemcheckboxvalue') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_enable_reward_points');
            $productlevelrewardtype = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_referral_rewardsystem_options_getrefer') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_select_referral_reward_rule_getrefer');
            $productlevelrewardpoints = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_referralrewardsystempoints_for_getting_referred') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_referral_reward_points_getting_refer');
            $productlevelrewardpercent = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_referralrewardsystempercent_for_getting_referred') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_referral_reward_percent_getting_refer');
        }
    } elseif ($getting_referrer == 'yes') {
        $productlevel = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_rewardsystemcheckboxvalue') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_enable_reward_points');
        $productlevelrewardtype = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_referral_rewardsystem_options_getrefer') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_select_referral_reward_rule_getrefer');
        $productlevelrewardpoints = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_referralrewardsystempoints_for_getting_referred') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_referral_reward_points_getting_refer');
        $productlevelrewardpercent = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_referralrewardsystempercent_for_getting_referred') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_referral_reward_percent_getting_refer');
    } elseif ($socialreward == 'yes') {
        $newarray = rs_function_to_get_social_rewardspoints($productid, $rewardfor, $level = '1', $termid = '');
        $productlevel = $newarray['enable_level'];
        $productlevelrewardtype = $newarray['rewardtype'];
        $productlevelrewardpoints = $newarray['rewardpoints'];
        $productlevelrewardpercent = $newarray['rewardpercent'];
    } else {
        $productlevel = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_rewardsystemcheckboxvalue') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_enable_reward_points');
        $productlevelrewardtype = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_rewardsystem_options') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_select_reward_rule');
        $productlevelrewardpoints = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_rewardsystempoints') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_reward_points');
        $productlevelrewardpercent = $variationid == '0' || '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_rewardsystempercent') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variationid, '_reward_percent');
    }
    if (($productlevel == 'yes') || ($productlevel == '1')) {
        if ($productlevelrewardtype == '1' && $productlevelrewardpoints != '') {
            if ($checklevel == 'yes') {
                return '1';
            } else {
                return $productlevelrewardpoints * $itemquantity;
            }
        } elseif ($productlevelrewardtype == '2' && $productlevelrewardpercent != '') {
            if ($checklevel == 'yes') {
                return '1';
            } else {
                $regularprice = rs_function_to_get_regular_price($productid, $variationid, $item, $itemquantity);
                $convertedpoints = rs_convert_reward_percent_value($productlevelrewardpercent, $regularprice);
                return $convertedpoints;
            }
        }
        return is_category_level($productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor);
    } else {
        return '0';
    }
}

function is_category_level($productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor) {
    //Category Level
    $itemquantity = isset($item['qty']) ? $item['qty'] : $item['quantity'];
    $categorylist = wp_get_post_terms($productid, 'product_cat');
    $getcount = count($categorylist);
    $term = get_the_terms($productid, 'product_cat');
    $cat_level_enabled = array();
    $cat_level_point = array();
    $cat_level_percent = array();
    if (is_array($term)) {
        foreach ($term as $terms) {
            $termid = $terms->term_id;
            if ($referred_user != '') {
                $categorylevel = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'enable_reward_system_category');
                $categorylevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'referral_enable_rs_rule');
                $categorylevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'referral_rs_category_points');
                $categorylevelrewardpercent = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'referral_rs_category_percent');
                if ($getting_referrer == 'yes') {
                    $categorylevel = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'enable_reward_system_category');
                    $categorylevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'referral_enable_rs_rule_refer');
                    $categorylevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'referral_rs_category_points_get_refered');
                    $categorylevelrewardpercent = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'referral_rs_category_percent_get_refer');
                }
            } elseif ($getting_referrer == 'yes') {
                $categorylevel = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'enable_reward_system_category');
                $categorylevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'referral_enable_rs_rule_refer');
                $categorylevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'referral_rs_category_points_get_refered');
                $categorylevelrewardpercent = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'referral_rs_category_percent_get_refer');
            } elseif ($socialreward == 'yes') {
                $newarray = rs_function_to_get_social_rewardspoints($productid, $rewardfor, $level = '2', $termid);
                $categorylevel = $newarray['enable_level'];
                $categorylevelrewardtype = $newarray['rewardtype'];
                $categorylevelrewardpoints = $newarray['rewardpoints'];
                $categorylevelrewardpercent = $newarray['rewardpercent'];
            } else {
                $categorylevel = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'enable_reward_system_category');
                $categorylevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'enable_rs_rule');
                $categorylevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'rs_category_points');
                $categorylevelrewardpercent = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'rs_category_percent');
            }            
            if ($getcount >= 1) {
                if (($categorylevel == 'yes') && ($categorylevel != '')) {
                    if (($categorylevelrewardtype == '1') && $categorylevelrewardpoints != '') {
                        if ($checklevel == 'yes') {
                            $cat_level_enabled[] = '2';
                        } else {
                            $cat_level_point[] = $categorylevelrewardpoints * $itemquantity;
                        }
                    } else if (($categorylevelrewardtype == '2') && $categorylevelrewardpercent != '') {
                        if ($checklevel == 'yes') {
                            $cat_level_enabled[] = '2';
                        } else {
                            $regularprice = rs_function_to_get_regular_price($productid, $variationid, $item, $itemquantity);
                            $convertedpoints = rs_convert_reward_percent_value($categorylevelrewardpercent, $regularprice);
                            $cat_level_point[] = $convertedpoints;
                        }
                    }
                }
            }
        }
        if (!empty($cat_level_point)) {
            $category_points = max($cat_level_point);
            return $category_points;
        } elseif (!empty($cat_level_enabled)) {
            return '2';
        }
    }
    return is_global_level($productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor);
}

function is_global_level($productid, $variationid, $item, $checklevel, $referred_user, $getting_referrer, $socialreward, $rewardfor) {
    //Global Level
    $itemquantity = isset($item['qty']) ? $item['qty'] : $item['quantity'];
    $global_enable = get_option('rs_global_enable_disable_sumo_reward');
    if ($referred_user != '') {
        $global_reward_type = get_option('rs_global_referral_reward_type');
        $global_rewardpoints = get_option('rs_global_referral_reward_point');
        $global_rewardpercent = get_option('rs_global_referral_reward_percent');
        if ($getting_referrer == 'yes') {
            $global_reward_type = get_option('rs_global_referral_reward_type_refer');
            $global_rewardpoints = get_option('rs_global_referral_reward_point_get_refer');
            $global_rewardpercent = get_option('rs_global_referral_reward_percent_get_refer');
        }
    } elseif ($getting_referrer == 'yes') {
        $global_reward_type = get_option('rs_global_referral_reward_type_refer');
        $global_rewardpoints = get_option('rs_global_referral_reward_point_get_refer');
        $global_rewardpercent = get_option('rs_global_referral_reward_percent_get_refer');
    } elseif ($socialreward == 'yes') {
        $newarray = rs_function_to_get_social_rewardspoints($productid, $rewardfor, $level = '3', $termid = '');
        $global_enable = $newarray['enable_level'];
        $global_reward_type = $newarray['rewardtype'];
        $global_rewardpoints = $newarray['rewardpoints'];
        $global_rewardpercent = $newarray['rewardpercent'];
    } else {
        $global_reward_type = get_option('rs_global_reward_type');
        $global_rewardpoints = get_option('rs_global_reward_points');
        $global_rewardpercent = get_option('rs_global_reward_percent');
    }

    if ($global_enable == '1') {
        if ($global_reward_type == '1') {
            if ($global_rewardpoints != '') {
                if ($checklevel == 'yes') {
                    return '3';
                } else {
                    return $global_rewardpoints * $itemquantity;
                }
            }
        } else {
            if ($global_rewardpercent != '') {
                if ($checklevel == 'yes') {
                    return '3';
                } else {
                    $regularprice = rs_function_to_get_regular_price($productid, $variationid, $item, $itemquantity);
                    $convertedpoints = rs_convert_reward_percent_value($global_rewardpercent, $regularprice);
                    return $convertedpoints;
                }
            }
        }
    } else {
        return '0';
    }
}

/* Function to Convert Reward Percent into Points */

function rs_convert_reward_percent_value($rewardpercent, $regularprice) {
    $pointforconversion = earn_point_conversion();
    $pointforconversionvalue = earn_point_conversion_value();
    $get_rewardpercent = $rewardpercent / 100;
    $getaveragepoints = $get_rewardpercent * $regularprice;
    $pointswithvalue = $getaveragepoints * $pointforconversion;
    $rewardpoints = $pointswithvalue / $pointforconversionvalue;
    return $rewardpoints;
}

function rs_function_to_get_regular_price($productid, $variationid, $item, $itemquantity) {
    $mainproductdatabooking = rs_get_product_object($productid);
    if (is_cart() || is_checkout()) {
        $getregularprice = $item['line_subtotal'];
        if (get_option('woocommerce_tax_display_cart') == 'incl' && get_option('woocommerce_prices_include_tax') == 'no') {
            $getregularprice = $item['line_subtotal'] + $item['line_subtotal_tax'];
        } elseif (get_option('woocommerce_tax_display_cart') == 'incl' && get_option('woocommerce_prices_include_tax') == 'yes') {
            $getregularprice = $item['line_subtotal'] + $item['line_subtotal_tax'];
        } elseif (get_option('woocommerce_tax_display_cart') == 'excl' && get_option('woocommerce_prices_include_tax') == 'yes') {
            $getregularprice = $item['line_subtotal'];
        }
    } elseif (is_shop() || is_product()) {
        if ($mainproductdatabooking->is_type('variation')) {
            $variable_product = new WC_Product_Variation($variationid);
            if (get_option('woocommerce_tax_display_shop') == 'incl' && get_option('woocommerce_prices_include_tax') == 'no') {
                $getregularprice = rs_get_price_including_tax($variable_product);
            } elseif (get_option('woocommerce_tax_display_shop') == 'incl' && get_option('woocommerce_prices_include_tax') == 'yes') {
                $getregularprice = rs_get_price_including_tax($variable_product);
            } elseif (get_option('woocommerce_tax_display_shop') == 'excl' && get_option('woocommerce_prices_include_tax') == 'yes') {
                $getregularprice = rs_get_price_excluding_tax($variable_product);
            } else {
                $getregularprice = $variable_product->get_price() * $itemquantity;
            }
        } else {
            $getregularprice = $mainproductdatabooking->get_price() * $itemquantity;
            if (get_option('woocommerce_tax_display_shop') == 'incl' && get_option('woocommerce_prices_include_tax') == 'no') {
                $getregularprice = rs_get_price_including_tax($mainproductdatabooking);
            } elseif (get_option('woocommerce_tax_display_shop') == 'incl' && get_option('woocommerce_prices_include_tax') == 'yes') {
                $getregularprice = rs_get_price_including_tax($mainproductdatabooking);
            } elseif (get_option('woocommerce_tax_display_shop') == 'excl' && get_option('woocommerce_prices_include_tax') == 'yes') {
                $getregularprice = rs_get_price_excluding_tax($mainproductdatabooking);
            }
        }
    } else {
        if ((is_object($item)) || (is_array($item)) && !empty($item)) {
            if (isset($item['qty']) && !isset($item['line_subtotal'])) {
                if ($variationid != '' && $variationid != 0) {
                    $variable_product = new WC_Product_Variation($variationid);
                    if (get_option('woocommerce_tax_display_shop') == 'incl' && get_option('woocommerce_prices_include_tax') == 'no') {
                        $getregularprice = rs_get_price_including_tax($variable_product);
                    } elseif (get_option('woocommerce_tax_display_shop') == 'incl' && get_option('woocommerce_prices_include_tax') == 'yes') {
                        $getregularprice = rs_get_price_including_tax($variable_product);
                    } elseif (get_option('woocommerce_tax_display_shop') == 'excl' && get_option('woocommerce_prices_include_tax') == 'yes') {
                        $getregularprice = rs_get_price_excluding_tax($variable_product);
                    } else {
                        $getregularprice = $variable_product->get_price() * $itemquantity;
                    }
                } else {
                    $getregularprice = $mainproductdatabooking->get_price() * $itemquantity;
                }
            } else {
                $getregularprice = $item['line_subtotal'];
                if (get_option('woocommerce_tax_display_cart') == 'incl' && get_option('woocommerce_prices_include_tax') == 'no') {
                    $getregularprice = $item['line_subtotal'] + $item['line_subtotal_tax'];
                } elseif (get_option('woocommerce_tax_display_cart') == 'incl' && get_option('woocommerce_prices_include_tax') == 'yes') {
                    $getregularprice = $item['line_subtotal'] + $item['line_subtotal_tax'];
                } elseif (get_option('woocommerce_tax_display_cart') == 'excl' && get_option('woocommerce_prices_include_tax') == 'yes') {
                    $getregularprice = $item['line_subtotal'];
                }
            }
        } else {
            $getregularprice = $mainproductdatabooking->get_price() * $itemquantity;
        }
    }
    return $getregularprice;
}

function rs_function_to_get_social_rewardspoints($productid, $rewardfor, $level, $termid) {
    $productlevel = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystemcheckboxvalue');
    $categorylevel = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'enable_social_reward_system_category');
    $global_enable = get_option('rs_global_social_enable_disable_reward');
    if ($rewardfor == 'instagram') {
        if ($level == '1') {
            $productlevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_social_rewardsystem_options_instagram');
            $productlevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempoints_instagram');
            $productlevelrewardpercent = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempercent_instagram');
            $array = array('enable_level' => $productlevel, 'rewardtype' => $productlevelrewardtype, 'rewardpoints' => $productlevelrewardpoints, 'rewardpercent' => $productlevelrewardpercent);
            return $array;
        } elseif ($level == '2') {
            $categorylevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_instagram_enable_rs_rule');
            $categorylevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_instagram_rs_category_points');
            $categorylevelrewardpercents = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_instagram_rs_category_percent');
            $array = array('enable_level' => $categorylevel, 'rewardtype' => $categorylevelrewardtype, 'rewardpoints' => $categorylevelrewardpoints, 'rewardpercent' => $categorylevelrewardpercents);
            return $array;
        } else {
            $global_reward_type = get_option('rs_global_social_reward_type_instagram');
            $global_reward_points = get_option('rs_global_social_instagram_reward_points');
            $global_reward_percent = get_option('rs_global_social_instagram_reward_percent');
            $array = array('enable_level' => $global_enable, 'rewardtype' => $global_reward_type, 'rewardpoints' => $global_reward_points, 'rewardpercent' => $global_reward_percent);
            return $array;
        }
    } elseif ($rewardfor == 'twitter_follow') {
        if ($level == '1') {
            $gettype = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_social_rewardsystem_options_twitter_follow');
            $getpoints = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempoints_twitter_follow');
            $getpercent = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempercent_twitter_follow');
            $array = array('enable_level' => $productlevel, 'rewardtype' => $gettype, 'rewardpoints' => $getpoints, 'rewardpercent' => $getpercent);
            return $array;
        } elseif ($level == '2') {
            $categorylevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_twitter_follow_enable_rs_rule');
            $categorylevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_twitter_follow_rs_category_points');
            $categorylevelrewardpercents = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_twitter_follow_rs_category_percent');
            $array = array('enable_level' => $categorylevel, 'rewardtype' => $categorylevelrewardtype, 'rewardpoints' => $categorylevelrewardpoints, 'rewardpercent' => $categorylevelrewardpercents);
            return $array;
        } else {
            $global_reward_type = get_option('rs_global_social_reward_type_twitter_follow');
            $global_reward_points = get_option('rs_global_social_twitter_follow_reward_points');
            $global_reward_percent = get_option('rs_global_social_twitter_follow_reward_percent');
            $array = array('enable_level' => $global_enable, 'rewardtype' => $global_reward_type, 'rewardpoints' => $global_reward_points, 'rewardpercent' => $global_reward_percent);
            return $array;
        }
    } elseif ($rewardfor == 'fb_like') {
        if ($level == '1') {
            $gettype = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_social_rewardsystem_options_facebook');
            $getpoints = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempoints_facebook');
            $getpercent = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempercent_facebook');
            $array = array('enable_level' => $productlevel, 'rewardtype' => $gettype, 'rewardpoints' => $getpoints, 'rewardpercent' => $getpercent);
            return $array;
        } elseif ($level == '2') {
            $categorylevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_facebook_enable_rs_rule');
            $categorylevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_facebook_rs_category_points');
            $categorylevelrewardpercents = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_facebook_rs_category_percent');
            $array = array('enable_level' => $categorylevel, 'rewardtype' => $categorylevelrewardtype, 'rewardpoints' => $categorylevelrewardpoints, 'rewardpercent' => $categorylevelrewardpercents);
            return $array;
        } else {
            $global_reward_type = get_option('rs_global_social_reward_type_facebook');
            $global_reward_points = get_option('rs_global_social_facebook_reward_points');
            $global_reward_percent = get_option('rs_global_social_facebook_reward_percent');
            $array = array('enable_level' => $global_enable, 'rewardtype' => $global_reward_type, 'rewardpoints' => $global_reward_points, 'rewardpercent' => $global_reward_percent);
            return $array;
        }
    } elseif ($rewardfor == 'fb_share') {
        if ($level == '1') {
            $gettype = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_social_rewardsystem_options_facebook_share');
            $getpoints = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempoints_facebook_share');
            $getpercent = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempercent_facebook_share');
            $array = array('enable_level' => $productlevel, 'rewardtype' => $gettype, 'rewardpoints' => $getpoints, 'rewardpercent' => $getpercent);
            return $array;
        } elseif ($level == '2') {
            $categorylevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_facebook_share_enable_rs_rule');
            $categorylevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_facebook_share_rs_category_points');
            $categorylevelrewardpercents = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_facebook_share_rs_category_percent');
            $array = array('enable_level' => $categorylevel, 'rewardtype' => $categorylevelrewardtype, 'rewardpoints' => $categorylevelrewardpoints, 'rewardpercent' => $categorylevelrewardpercents);
            return $array;
        } else {
            $global_reward_type = get_option('rs_global_social_reward_type_facebook_share');
            $global_reward_points = get_option('rs_global_social_facebook_share_reward_points');
            $global_reward_percent = get_option('rs_global_social_facebook_share_reward_percent');
            $array = array('enable_level' => $global_enable, 'rewardtype' => $global_reward_type, 'rewardpoints' => $global_reward_points, 'rewardpercent' => $global_reward_percent);
            return $array;
        }
    } elseif ($rewardfor == 'twitter_tweet') {
        if ($level == '1') {
            $gettype = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_social_rewardsystem_options_twitter');
            $getpoints = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempoints_twitter');
            $getpercent = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempercent_twitter');
            $array = array('enable_level' => $productlevel, 'rewardtype' => $gettype, 'rewardpoints' => $getpoints, 'rewardpercent' => $getpercent);
            return $array;
        } elseif ($level == '2') {
            $categorylevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_twitter_enable_rs_rule');
            $categorylevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_twitter_rs_category_points');
            $categorylevelrewardpercents = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_twitter_rs_category_percent');
            $array = array('enable_level' => $categorylevel, 'rewardtype' => $categorylevelrewardtype, 'rewardpoints' => $categorylevelrewardpoints, 'rewardpercent' => $categorylevelrewardpercents);
            return $array;
        } else {
            $global_reward_type = get_option('rs_global_social_reward_type_twitter');
            $global_reward_points = get_option('rs_global_social_twitter_reward_points');
            $global_reward_percent = get_option('rs_global_social_twitter_reward_percent');
            $array = array('enable_level' => $global_enable, 'rewardtype' => $global_reward_type, 'rewardpoints' => $global_reward_points, 'rewardpercent' => $global_reward_percent);
            return $array;
        }
    } elseif ($rewardfor == 'g_plus') {
        if ($level == '1') {
            $gettype = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_social_rewardsystem_options_google');
            $getpoints = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempoints_google');
            $getpercent = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempercent_google');
            $array = array('enable_level' => $productlevel, 'rewardtype' => $gettype, 'rewardpoints' => $getpoints, 'rewardpercent' => $getpercent);
            return $array;
        } elseif ($level == '2') {
            $categorylevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_google_enable_rs_rule');
            $categorylevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_google_rs_category_points');
            $categorylevelrewardpercents = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_google_rs_category_percent');
            $array = array('enable_level' => $categorylevel, 'rewardtype' => $categorylevelrewardtype, 'rewardpoints' => $categorylevelrewardpoints, 'rewardpercent' => $categorylevelrewardpercents);
            return $array;
        } else {
            $global_reward_type = get_option('rs_global_social_reward_type_google');
            $global_reward_points = get_option('rs_global_social_google_reward_points');
            $global_reward_percent = get_option('rs_global_social_google_reward_percent');
            $array = array('enable_level' => $global_enable, 'rewardtype' => $global_reward_type, 'rewardpoints' => $global_reward_points, 'rewardpercent' => $global_reward_percent);
            return $array;
        }
    } elseif ($rewardfor == 'vk_like') {
        if ($level == '1') {
            $gettype = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_social_rewardsystem_options_vk');
            $getpoints = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempoints_vk');
            $getpercent = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempercent_vk');
            $array = array('enable_level' => $productlevel, 'rewardtype' => $gettype, 'rewardpoints' => $getpoints, 'rewardpercent' => $getpercent);
            return $array;
        } elseif ($level == '2') {
            $categorylevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_vk_enable_rs_rule');
            $categorylevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_vk_rs_category_points');
            $categorylevelrewardpercents = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_vk_rs_category_percent');
            $array = array('enable_level' => $categorylevel, 'rewardtype' => $categorylevelrewardtype, 'rewardpoints' => $categorylevelrewardpoints, 'rewardpercent' => $categorylevelrewardpercents);
            return $array;
        } else {
            $global_reward_type = get_option('rs_global_social_reward_type_vk');
            $global_reward_points = get_option('rs_global_social_vk_reward_points');
            $global_reward_percent = get_option('rs_global_social_vk_reward_percent');
            $array = array('enable_level' => $global_enable, 'rewardtype' => $global_reward_type, 'rewardpoints' => $global_reward_points, 'rewardpercent' => $global_reward_percent);
            return $array;
        }
    } elseif ($rewardfor == 'ok_follow') {
        if ($level == '1') {
            $gettype = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_social_rewardsystem_options_ok_follow');
            $getpoints = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempoints_ok_follow');
            $getpercent = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($productid, '_socialrewardsystempercent_ok_follow');
            $array = array('enable_level' => $productlevel, 'rewardtype' => $gettype, 'rewardpoints' => $getpoints, 'rewardpercent' => $getpercent);
            return $array;
        } elseif ($level == '2') {
            $categorylevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_ok_follow_enable_rs_rule');
            $categorylevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_ok_follow_rs_category_points');
            $categorylevelrewardpercents = RSFunctionForSavingMetaValues::rewardsystem_get_woocommerce_term_meta($termid, 'social_ok_follow_rs_category_percent');
            $array = array('enable_level' => $categorylevel, 'rewardtype' => $categorylevelrewardtype, 'rewardpoints' => $categorylevelrewardpoints, 'rewardpercent' => $categorylevelrewardpercents);
            return $array;
        } else {
            $global_reward_type = get_option('rs_global_social_reward_type_ok_follow');
            $global_reward_points = get_option('rs_global_social_ok_follow_reward_points');
            $global_reward_percent = get_option('rs_global_social_ok_follow_reward_percent');
            $array = array('enable_level' => $global_enable, 'rewardtype' => $global_reward_type, 'rewardpoints' => $global_reward_points, 'rewardpercent' => $global_reward_percent);
            return $array;
        }
    }
}

function get_woocommerce_formatted_price($price) {
    if (function_exists('wc_price')) {
        return wc_price($price);
    } else {
        return woocommerce_price($price);
    }
}

function rs_function_to_restrict_points_for_product_which_has_saleprice($product_id, $variation_id) {
    if (get_option('rs_pointx_not_award_when_sale_price') == 'yes') {
        if ($variation_id != '' && $variation_id != '0') {
            if (wp_get_post_parent_id($variation_id) != '0') {
                $variable_product1 = new WC_Product_Variation($variation_id);
            } else {
                $variable_product1 = rs_get_product_object($variation_id);
            }
            global $woocommerce;
            if ((float) $woocommerce->version >= (float) '3.0') {
                $getsaleprice = $variable_product1->get_sale_price();
            } else {
                $getsaleprice = $variable_product1->sale_price;
            }
            if ($getsaleprice != '') {
                return 'yes';
            }
        } else {
            $getsaleprice = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_sale_price');
            if ($getsaleprice != '') {
                return 'yes';
            }
        }
    }
    return 'no';
}

function rs_function_to_check_the_restriction_for_referral($get_user_type) {
    $userids = array();
    if (is_user_logged_in()) {
        $current_user_id = get_current_user_id();
        $current_user_role = wp_get_current_user();
        $user_role = $current_user_role->roles[0];
    } elseif (isset($_GET['ref'])) {
        $user = get_user_by('login', $_GET['ref']);
        if ($user != false) {
            $current_user_id = $user->ID;
        } else {
            $current_user_id = $_GET['ref'];
        }
        $user_info = get_userdata($current_user_id);
        $user_role = $user_info->roles[0];
    } else {
        $current_user_id = '';
        $user_role = '';
    }
    if ($get_user_type == '1') {
        return true;
    } elseif ($get_user_type == '2') {
        $getuser = get_option('rs_select_include_users_for_show_referral_link');
        if ($getuser != "") {
            if (!is_array($getuser)) {
                $userids = array_filter(array_map('absint', (array) explode(',', $getuser)));
            } else {
                $userids = $getuser;
            }
            if (in_array($current_user_id, $userids)) {
                return true;
            }
        } else {
            return true;
        }
    } elseif ($get_user_type == '3') {
        $getuser = get_option('rs_select_exclude_users_list_for_show_referral_link');
        if ($getuser != "") {
            if (!is_array($getuser)) {
                $userids = array_filter(array_map('absint', (array) explode(',', $getuser)));
            } else {
                $userids = $getuser;
            }
            if (!in_array($current_user_id, $userids)) {
                return true;
            }
        } else {
            return true;
        }
    } elseif ($get_user_type == '4') {
        $getuser = get_option('rs_select_users_role_for_show_referral_link');
        if (is_array($getuser) && !empty($getuser)) {
            if (in_array($user_role, $getuser)) {
                return true;
            }
        } else {
            return true;
        }
    } else {
        $getuser = get_option('rs_select_exclude_users_role_for_show_referral_link');
        if (is_array($getuser) && !empty($getuser)) {
            if (!in_array($user_role, $getuser)) {
                return true;
            }
        } else {
            return true;
        }
    }
    return false;
}

function rs_function_to_get_expiry_date_in_unixtimestamp() {
    $noofdays = get_option('rs_point_to_be_expire');
    $date = (($noofdays != 0) && ($noofdays != '')) ? time() + ($noofdays * 24 * 60 * 60) : '999999999999';
    return $date;
}

function rs_get_product_object($product_id) {
    if (function_exists('wc_get_product')) {
        $product_object = wc_get_product($product_id);
    } else {
        $product_object = get_product($product_id);
    }
    return $product_object;
}

function rs_get_price_excluding_tax($product_object) {
    if (function_exists('wc_get_price_excluding_tax')) {
        $get_excluded_tax = wc_get_price_excluding_tax($product_object);
    } else {
        $get_excluded_tax = $product_object->get_price_excluding_tax();
    }
    return $get_excluded_tax;
}

function rs_get_price_including_tax($product_object) {
    if (function_exists('wc_get_price_including_tax')) {
        $get_included_tax = wc_get_price_including_tax($product_object);
    } else {
        $get_included_tax = $product_object->get_price_including_tax();
    }
    return $get_included_tax;
}

function rs_get_id($product) {
    global $woocommerce;
    if ((float) $woocommerce->version >= (float) '3.0') {
        $id = $product->get_id();
    } else {
        if ($product->is_type('variation')) {
            $id = $product->variation_id;
        } else {
            $id = $product->id;
        }
    }
    return $id;
}

function rs_get_parent_id($variable_product_obj) {
    global $woocommerce;
    if ((float) $woocommerce->version >= (float) '3.0') {
        $id = $variable_product_obj->get_parent_id();
    } else {
        $id = $variable_product_obj->parent->id;
    }
    return $id;
}

function rs_get_price($object) {
    global $woocommerce;
    if ((float) $woocommerce->version >= (float) '3.0') {
        $price = $object->get_price();
    } else {
        $price = $object->price;
    }
    return $price;
}

function rs_get_product_type($object) {
    global $woocommerce;
    if ((float) $woocommerce->version >= (float) '3.0') {
        $product_type = $object->get_type();
    } else {
        $product_type = $object->product_type;
    }
    return $product_type;
}

function rs_get_order_obj($order) {
    if (is_object($order) && !empty($order)) {
        global $woocommerce;
        if ((float) $woocommerce->version >= (float) '3.0') {
            $order_id = $order->get_id();
            $post_status = $order->get_status();
            $order_user_id = $order->get_user_id();
            $payment_method = $order->get_payment_method();
            $payment_method_title = $order->get_payment_method_title();
        } else {
            $order_id = $order->id;
            $post_status = $order->post_status;
            $order_user_id = $order->user_id;
            $payment_method = $order->payment_method;
            $payment_method_title = $order->payment_method_title;
        }
        $new_array = array(
            'order_id' => $order_id,
            'order_status' => $post_status,
            'order_userid' => $order_user_id,
            'payment_method' => $payment_method,
            'payment_method_title' => $payment_method_title
        );
        return $new_array;
    }
}

function rs_get_coupon_obj($object) {
    if (is_object($object) && !empty($object)) {
        global $woocommerce;
        if ((float) $woocommerce->version >= (float) '3.0') {
            $coupon_id = $object->get_id();
            $coupon_code = $object->get_code();
            $coupon_amnt = $object->get_amount();
            $coupon_product_ids = $object->get_product_ids();
            $discount_type = $object->get_discount_type();
            $product_cat = $object->get_product_categories();
        } else {
            $coupon_id = $object->id;
            $coupon_code = $object->code;
            $coupon_amnt = $object->coupon_amount;
            $coupon_product_ids = $object->product_ids;
            $discount_type = $object->discount_type;
            $product_cat = $object->product_categories;
        }
        $new_array = array(
            'coupon_id' => $coupon_id,
            'coupon_code' => $coupon_code,
            'coupon_amount' => $coupon_amnt,
            'product_ids' => $coupon_product_ids,
            'discount_type' => $discount_type,
            'product_categories' => $product_cat
        );
        return $new_array;
    }
}

function rs_get_post_parent($object) {
    global $woocommerce;
    if ((float) $woocommerce->version >= (float) '3.0') {
        $parent_id = $object->get_parent_id();
    } else {
        $parent_id = $object->post->post_parent;
    }
    return $parent_id;
}

function rs_get_sale_or_regular_price($product) {
    global $woocommerce;
    if ((float) $woocommerce->version >= (float) '3.0') {
        $price = $product->get_sale_price() != '' ? $product->get_sale_price() : $product->get_regular_price();
    } else {
        $price = $product->sale_price != '' ? $product->sale_price : $product->regular_price;
    }
    return $price;
}

function rs_check_variable_product_type($object) {
    global $woocommerce;
    if ((float) $woocommerce->version >= (float) '3.0') {
        if (is_object($object) && ($object->is_type('variable'))) {
            return true;
        }
    } else {
        if (is_object($object) && ($object->is_type('variation'))) {
            return true;
        }
    }
    return false;
}
