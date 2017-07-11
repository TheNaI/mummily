<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForOrder')) {

    class RSFunctionForOrder {

        public static function init() {

            add_action('woocommerce_order_items_table', array(__CLASS__, 'display_total_redem_points_order'));

            add_action('woocommerce_email_after_order_table', array(__CLASS__, 'get_the_total_earned_points_for_order'));

            add_action('woocommerce_email_after_order_table', array(__CLASS__, 'get_the_total_redeem_points_for_order'));            

            add_filter('woocommerce_get_formatted_order_total', array(__CLASS__, 'display_total_point_price'), 10, 2);

            add_filter('woocommerce_order_formatted_line_subtotal', array(__CLASS__, 'display_line_total'), 8, 3);

            add_filter('woocommerce_order_subtotal_to_display', array(__CLASS__, 'display_line_total1'), 8, 3);

            add_filter('woocommerce_get_order_item_totals', array(__CLASS__, 'display_message_total_earn_point_for_order'), 8, 2);
        }

        public static function display_message_total_earn_point_for_order($total_rows, $order) {
            $orderid = rs_get_order_obj($order);
            $order_id = $orderid['order_id'];
            if ($order_id != '') {
                $order_user_id = $orderid['order_userid'];
                $redeempoints = RSFunctionToApplyCoupon::update_redeem_reward_points_to_user($order_id, $order_user_id);
                if ($redeempoints > 0) {
                    $tax_display = '';
                    $total_rows['discount'] = array(
                        'label' => __(get_option('rs_coupon_label_message'), 'woocommerce'),
                        'value' => '-' . $order->get_discount_to_display($tax_display)
                    );
                    return $total_rows;
                } else {
                    return $total_rows;
                }
            }
            return $total_rows;
        }

        public static function display_line_total1($line_total1, $id, $order) {
            $orderid = rs_get_order_obj($order);
            $order_id = $orderid['order_id'];
            $rewardgateway = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, '_payment_method');
            $array = array();
            $linetotal = array();
            $updatedvalue = array();
            if ($rewardgateway == 'reward_gateway') {
                foreach ($order->get_items()as $item) {
                    $productid = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
                    if (check_display_price_type($productid) == '2') {
                        if (get_option('rs_enable_disable_point_priceing') == '1') {
                            $label = get_option('rs_label_for_point_value');

                            $replace = str_replace("/", "", $label);
                            $enable = calculate_point_price_for_products($productid);
                            if ($enable[$productid] != '') {
                                $cart_object = $enable[$productid] * $item['qty'];
                                $array[] = $cart_object;
                            }
                        }
                    } else {
                        $label = get_option('rs_label_for_point_value');
                        $replace = str_replace("/", "", $label);
                        $enable = calculate_point_price_for_products($productid);
                        if ($enable[$productid] != '') {
                            $cart_object = $enable[$productid] * $item['qty'];
                            $array[] = $cart_object;
                        } else {
                            $linetotal = $item['line_subtotal'];
                            $newvalue = $linetotal / wc_format_decimal(get_option('rs_redeem_point_value'));
                            $updatedvalue[] = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
                        }
                    }
                }

                $amount = array_sum($array) + array_sum($updatedvalue);
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                $amount = round($amount, $roundofftype);
                $labelposition = get_option('rs_sufix_prefix_point_price_label');
                if ($labelposition == '1') {
                    $product_price = $replace . $amount;
                } else {
                    $product_price = $amount . $replace;
                }
                return $product_price;
            } else {
                return $line_total1;
            }
        }

        public static function display_line_total($line_total1, $id, $order) {
            $orderid = rs_get_order_obj($order);
            $order_id = $orderid['order_id'];
            $rewardgateway = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, '_payment_method');
            if ($rewardgateway == 'reward_gateway') {
                $labelpoint = get_option('rs_label_for_point_value');
                $product_id = $id['variation_id'] != 0 ? $id['variation_id'] : $id['product_id'];
                $label = get_option('rs_label_for_point_value');
                $replace = str_replace("/", "", $label);
                $enable = calculate_point_price_for_products($product_id);
                if ($enable[$product_id] != '') {
                    $cart_object = $enable[$product_id] * $id['qty'];
                    $array = $cart_object;
                    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                    $array = round($array, $roundofftype);
                    $labelposition = get_option('rs_sufix_prefix_point_price_label');
                    if ($labelposition == '1') {
                        $product_price = $replace . $array;
                    } else {
                        $product_price = $array . $replace;
                    }
                    return $product_price;
                } else {
                    $linetotal = $id['line_subtotal'];
                    $newvalue = $linetotal / wc_format_decimal(get_option('rs_redeem_point_value'));
                    $updatedvalue = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
                    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                    $updatedvalue = round($updatedvalue, $roundofftype);
                    $labelposition = get_option('rs_sufix_prefix_point_price_label');
                    if ($labelposition == '1') {
                        $product_price = $replace . $updatedvalue;
                    } else {
                        $product_price = $updatedvalue . $replace;
                    }

                    return $product_price;
                }
            } else {
                return $line_total1;
            }
        }

        public static function display_total_point_price($order1, $order) {
            $orderid = rs_get_order_obj($order);
            $order_id = $orderid['order_id'];
            $rewardgateway = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($order_id, '_payment_method');
            if ($rewardgateway == 'reward_gateway') {
                $updatedvalue = array();
                $replace = '';
                global $woocommerce;
                $shipping_total = '';
                $updatedvalue1 = '';
                $couponamount = '';
                $couponamount1 = array();
                $updatedvalue = array();
                $array = array();
                $rewardpointscoupons = $order->get_items(array('coupon'));
                foreach ($rewardpointscoupons as $coupon) {
                    $couponamount1[] = $coupon['discount_amount'];
                }
                $couponamount = array_sum($couponamount1);

                foreach ($order->get_items()as $item) {

                    if (get_option('woocommerce_prices_include_tax') === 'yes') {
                        $shipping_total = $order->get_total_shipping();
                        $tax_total = 0;
                    } else {
                        $shipping_total = $order->get_total_shipping();
                        $tax_total = $order->get_total_tax();
                    }
                    $shipping_total = $shipping_total + $tax_total;
                    $newvalue1 = $shipping_total / wc_format_decimal(get_option('rs_redeem_point_value'));
                    $updatedvalue1 = $newvalue1 * wc_format_decimal(get_option('rs_redeem_point'));
                    $product_id = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
                    $label = get_option('rs_label_for_point_value');
                    $replace = str_replace("/", "", $label);
                    $enable = calculate_point_price_for_products($product_id);
                    if ($enable[$product_id] != '') {
                        $cart_object = $enable[$product_id] * $item['qty'];
                        $array[] = $cart_object;
                    } else {
                        $linetotal = $item['line_subtotal'];
                        $newvalue = $linetotal / wc_format_decimal(get_option('rs_redeem_point_value'));
                        $updatedvalue[] = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
                    }
                }


                $amount = array_sum($array) + array_sum($updatedvalue) - $couponamount;
                $amount = $amount + $updatedvalue1;
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                $amount = round($amount, $roundofftype);
                $labelposition = get_option('rs_sufix_prefix_point_price_label');
                if ($labelposition == '1') {
                    $product_price = $replace . $amount;
                } else {
                    $product_price = $amount . $replace;
                }
                return $product_price;
            } else {
                return $order1;
            }
        }

        public static function display_total_redem_points_order($order) {
            $totalpoints = array(0);
            $orderid = rs_get_order_obj($order);
            $order_id = $orderid['order_id'];
            if (get_option('rs_show_hide_total_points_order_field') == '1') {
                $totalpoints = get_post_meta($order_id, 'points_for_current_order', true);
                $redeem_check = get_post_meta($order_id, 'rs_check_enable_option_for_redeeming',true);
                if ($redeem_check == 'no') {
                    if ($totalpoints != '') {
                        $total_points = array_sum($totalpoints);
                        if ($total_points != 0) {
                            ?>
                            <tfoot>
                                <tr class="cart-total">
                                    <th><?php echo get_option('rs_total_earned_point_caption_checkout'); ?></th>
                                    <td><?php echo $total_points; ?></td>
                                </tr>
                            </tfoot>
                            <?php
                        }
                    }
                }
            }
        }

        /* To get the total earned for order */

        public static function get_the_total_earned_points_for_order($order) {
            $status = get_option('rs_order_status_control');
            global $wpdb;
            $table_name = $wpdb->prefix . 'rsrecordpoints';
            $orderid = rs_get_order_obj($order);
            $orderstatus = $orderid['order_status'];
            $orderid = $orderid['order_id'];
            $gettotalearnpoints = $wpdb->get_results("SELECT earnedpoints FROM $table_name WHERE orderid = $orderid", ARRAY_A);            
            if (is_array($status)) {
                foreach ($status as $statuses) {
                    $statusstr = $statuses;
                }
            }
            $replacestatus = str_replace('wc-completed', $statusstr, $orderstatus);
            if (get_option('rs_enable_msg_for_earned_points') == 'yes') {
                if (in_array($replacestatus, $status)) {
                    $totalearnedvalue = "";
                    $earned_total = $gettotalearnpoints;
                    if (is_array($earned_total)) {
                        foreach ($earned_total as $key => $value) {
                            $totalearnedvalue += (float)$value['earnedpoints'];
                        }
                        $msgforearnedpoints = get_option('rs_msg_for_earned_points');
                        $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                        $replacemsgforearnedpoints = str_replace('[earnedpoints]', $totalearnedvalue != "" ? round($totalearnedvalue, $roundofftype) : "0", $msgforearnedpoints);
                        echo '<br><br>' . '<b>' . $replacemsgforearnedpoints . '<b>' . '<br><br>';
                    }
                }
            }
        }

        /* To get the total redeem for order */

        public static function get_the_total_redeem_points_for_order($order) {
            $status = get_option('rs_order_status_control');
            global $wpdb;
            $table_name = $wpdb->prefix . 'rsrecordpoints';
            $orderid = rs_get_order_obj($order);
            $orderstatus = $orderid['order_status'];
            $orderid = $orderid['order_id'];
            $gettotalredeempoints = $wpdb->get_results("SELECT redeempoints FROM $table_name WHERE orderid=$orderid", ARRAY_A);            
            if (is_array($status)) {
                foreach ($status as $statuses) {
                    $statusstr = $statuses;
                }
            }
            $replacestatus = str_replace('wc-completed', $statusstr, $orderstatus);            
            if (get_option('rs_enable_msg_for_redeem_points') == 'yes') {
                if (in_array($replacestatus, $status)) {
                    $totalredeemvalue = "";
                    $redeem_total = $gettotalredeempoints;
                    if (is_array($redeem_total)) {
                        foreach ($redeem_total as $key => $value) {
                            $totalredeemvalue += (float)$value['redeempoints'];
                        }
                        $msgforredeempoints = get_option('rs_msg_for_redeem_points');
                        $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                        $replacemsgforredeempoints = str_replace('[redeempoints]', $totalredeemvalue != "" ? round($totalredeemvalue, $roundofftype) : "0", $msgforredeempoints);
                        echo '<b>' . $replacemsgforredeempoints . '</b>';
                    }
                }
            }
        }

    }

    RSFunctionForOrder::init();
}