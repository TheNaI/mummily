<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('RSFunctionForCheckout')) {

    class RSFunctionForCheckout {

        public static function init() {

            add_action('woocommerce_review_order_after_order_total', array(__CLASS__, 'display_earned_points_checkout'));

            add_action('woocommerce_before_checkout_form', array(__CLASS__, 'display_redeem_min_max_points_buttons_on_cart_page'));

            add_action('woocommerce_removed_coupon', array(__CLASS__, 'testing_checkout_coupon'));


            if (get_option('rs_reward_point_troubleshoot_before_cart') == '1') {

                add_action('woocommerce_before_cart', array(__CLASS__, 'your_current_points_cart_page'));

                add_action('woocommerce_before_cart', array(__CLASS__, 'rs_notice_in_cart_for_tax'), 1);
            } else {

                add_action('woocommerce_before_cart_table', array(__CLASS__, 'your_current_points_cart_page'));

                add_action('woocommerce_before_cart_table', array(__CLASS__, 'rs_notice_in_cart_for_tax'), 1);
            }

            add_action('woocommerce_before_checkout_form', array(__CLASS__, 'your_current_points_checkout_page'));

            add_action('woocommerce_before_checkout_form', array(__CLASS__, 'rs_notice_in_checkout_for_tax'), 1);

            add_shortcode('userpoints', array(__CLASS__, 'add_shortcode_for_user_points'));

            add_shortcode('userpoints_value', array(__CLASS__, 'add_shortcode_for_user_points_value'));

            add_action('woocommerce_before_checkout_form', array(__CLASS__, 'display_complete_message_checkout_page'));

            add_shortcode('totalrewards', array(__CLASS__, 'getshortcodetotal_rewards'));

            add_shortcode('totalrewardsvalue', array(__CLASS__, 'getvalueshortcodetotal_rewards'));

            if (get_option('rs_reward_point_troubleshoot_before_cart') == '1') {

                add_action('woocommerce_before_cart', array(__CLASS__, 'show_message_for_guest_cart_page'));
            } else {

                add_action('woocommerce_before_cart_table', array(__CLASS__, 'show_message_for_guest_cart_page'));
            }

            add_action('woocommerce_before_checkout_form', array(__CLASS__, 'show_message_for_guest_checkout_page'));

            add_shortcode('loginlink', array(__CLASS__, 'get_my_account_url_link'));

            add_action('woocommerce_after_checkout_form', array(__CLASS__, 'add_custom_message_to_payment_gateway_on_checkout'));

            add_action('wp_ajax_rs_order_payment_gateway_reward', array(__CLASS__, 'payment_gateway_reward_points_process_ajax_request'));

            //Force Signup if Guests placing the Subscription Order.
            add_action('woocommerce_before_checkout_form', array(__CLASS__, 'rs_function_to_enable_guest_signup_on_checkout'), 10, 1);

            add_action('woocommerce_checkout_process', array(__CLASS__, 'rs_function_to_create_account_for_guest'));

            add_action('wp_head', array(__CLASS__, 'rs_hide_redeeming_field_in_both_cart_and_checkout'));
            
            add_action('woocommerce_checkout_update_order_meta','rs_function_to_update_cart_subtotal');
        }
        public static function testing_checkout_coupon($couponcode) {
            if (get_option('rs_enable_disable_reward_point_based_coupon_amount') == 'yes') {
                if (is_checkout()) {
                    echo "<script type='text/javascript'> window.location.href=window.location.href;</script>";
                }
            }
        }

        public static function display_earned_points_checkout() {
            global $woocommerce;
            if (get_option('rs_show_hide_total_points_checkout_field') == '1') {
                $total_points = WC()->session->get('rewardpoints');
                if ($total_points != 0) {
                    $total = $woocommerce->cart->discount_cart;
                    if ($total != 0) {
                        if (get_option('rs_enable_redeem_for_order') == 'no') {
                            ?>
                            <tr class="tax-total">
                                <th><?php echo get_option('rs_total_earned_point_caption_checkout'); ?></th>
                                <td><?php echo $total_points; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr class="tax-total">
                            <th><?php echo get_option('rs_total_earned_point_caption_checkout'); ?></th>
                            <td><?php echo $total_points; ?></td>
                        </tr>
                        <?php
                    }
                }
            }
        }

        public static function display_redeem_min_max_points_buttons_on_cart_page() {
            if (is_user_logged_in()) {

                global $woocommerce;
                $minimum_cart_total_redeem = get_option('rs_minimum_cart_total_points');
                $maximum_cart_total_redeem = get_option('rs_maximum_cart_total_points');
                $current_carttotal_amount = $woocommerce->cart->subtotal;
                if ($minimum_cart_total_redeem != '' && $maximum_cart_total_redeem != '') {
                    if ($current_carttotal_amount >= $minimum_cart_total_redeem && $current_carttotal_amount <= $maximum_cart_total_redeem) {
                        self::reward_system_checkout_page_redeeming();
                    } else {
                        if (get_option('rs_show_hide_maximum_cart_total_error_message') == '1') {
                            $max_cart_total_redeeming = get_option('rs_max_cart_total_redeem_error');
                            $max_cart_amount_to_find = "[carttotal]";
                            $max_cart_total_currency_to_find = "[currencysymbol]";
                            $max_cart_amount_to_replace = get_option('rs_maximum_cart_total_points');
                            $max_cart_total_currency_to_replace = get_woocommerce_formatted_price($max_cart_amount_to_replace);
                            $max_cart_total_msg1 = str_replace($max_cart_amount_to_find, $max_cart_total_currency_to_replace, $max_cart_total_redeeming);
                            $max_cart_total_replaced = str_replace($max_cart_total_currency_to_find, "", $max_cart_total_msg1);
                            ?>
                            <div class="woocommerce-error"><?php echo $max_cart_total_replaced; ?></div>
                            <?php
                        }
                    }
                } else if ($minimum_cart_total_redeem != '' && $maximum_cart_total_redeem == '') {
                    if ($current_carttotal_amount >= $minimum_cart_total_redeem) {
                        self::reward_system_checkout_page_redeeming();
                    } else {
                        if (get_option('rs_show_hide_minimum_cart_total_error_message') == '1') {
                            $min_cart_total_redeeming = get_option('rs_min_cart_total_redeem_error');
                            $min_cart_amount_to_find = "[carttotal]";
                            $min_cart_total_currency_to_find = "[currencysymbol]";
                            $min_cart_amount_to_replace = get_option('rs_minimum_cart_total_points');
                            $min_cart_total_currency_to_replace = get_woocommerce_formatted_price($min_cart_amount_to_replace);
                            $min_cart_total_msg1 = str_replace($min_cart_amount_to_find, $min_cart_total_currency_to_replace, $min_cart_total_redeeming);
                            $min_cart_total_replaced = str_replace($min_cart_total_currency_to_find, "", $min_cart_total_msg1);
                            ?>
                            <div class="woocommerce-error"><?php echo $min_cart_total_replaced; ?></div>
                            <?php
                        }
                    }
                } else if ($minimum_cart_total_redeem == '' && $maximum_cart_total_redeem != '') {
                    if ($current_carttotal_amount <= $maximum_cart_total_redeem) {
                        self::reward_system_checkout_page_redeeming();
                    } else {
                        if (get_option('rs_show_hide_maximum_cart_total_error_message') == '1') {
                            $max_cart_total_redeeming = get_option('rs_max_cart_total_redeem_error');
                            $max_cart_amount_to_find = "[carttotal]";
                            $max_cart_total_currency_to_find = "[currencysymbol]";
                            $max_cart_amount_to_replace = get_option('rs_maximum_cart_total_points');
                            $max_cart_total_currency_to_replace = get_woocommerce_formatted_price($max_cart_amount_to_replace);
                            $max_cart_total_msg1 = str_replace($max_cart_amount_to_find, $max_cart_total_currency_to_replace, $max_cart_total_redeeming);
                            $max_cart_total_replaced = str_replace($max_cart_total_currency_to_find, "", $max_cart_total_msg1);
                            ?>
                            <div class="woocommerce-error"><?php echo $max_cart_total_replaced; ?></div>
                            <?php
                        }
                    }
                } else if ($minimum_cart_total_redeem == '' && $maximum_cart_total_redeem == '') {
                    self::reward_system_checkout_page_redeeming();
                }
            }
        }

        public static function reward_system_checkout_page_redeeming() {
            $totalselectedvalue = array();
            ?>
            <style type="text/css">
            <?php echo get_option('rs_checkout_page_custom_css'); ?>
            </style>
            <?php
            if (is_user_logged_in()) {
                global $woocommerce;
                $getuserid = get_current_user_id();
                $user_current_points = RSPointExpiry::get_sum_of_total_earned_points($getuserid);
                $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                $redeem_conversion = wc_format_decimal(get_option('rs_redeem_point'));
                if (get_option('rs_apply_redeem_basedon_cart_or_product_total') == '2') {
                    $getsumofselectedproduct = RSFunctionToApplyCoupon::get_sum_of_selected_products('auto', '', $user_current_points);
                    foreach ($woocommerce->cart->cart_contents as $item) {
                        $productid = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
                        $includeproductid = get_option('rs_select_products_to_enable_redeeming');
                        if (is_array($includeproductid)) {
                            $include_productid = (array) $includeproductid;
                        } else {
                            $include_productid = (array) explode(',', $includeproductid);
                        }
                        if (get_option('rs_enable_redeem_for_selected_products') == 'yes') {
                            if (get_option('rs_select_products_to_enable_redeeming') != '') {
                                if (in_array($productid, $include_productid)) {
                                    $totalselectedvalue[] = $item['line_subtotal'];
                                }
                            }
                        }
                        $includecategory = get_option('rs_select_category_to_enable_redeeming');
                        if (is_array($includecategory)) {
                            $include_category = (array) $includecategory; // Compatible for Old WooCommerce Version            
                        } else {
                            $include_category = (array) explode(',', $includecategory); // Compatible with Latest Version            
                        }
                        $productcategorys = get_the_terms($productid, 'product_cat');
                        if ($productcategorys != false) {
                            $getcount = count($productcategorys);
                            if ($getcount >= '1') {
                                foreach ($productcategorys as $productcategory) {
                                    $termid = $productcategory->term_id;
                                    if (get_option('rs_enable_redeem_for_selected_category') == 'yes') {
                                        if (get_option('rs_select_category_to_enable_redeeming') != '') {
                                            if (in_array($termid, $include_category)) {
                                                $totalselectedvalue[$productid] = $item['line_subtotal'];
                                            }
                                        } else {
                                            $totalselectedvalue[] = $woocommerce->cart->subtotal;
                                        }
                                    }
                                }
                            } else {
                                @$termid = $productcategorys[0]->term_id;
                                if (get_option('rs_enable_redeem_for_selected_category') == 'yes') {
                                    if (get_option('rs_select_category_to_enable_redeeming') != '') {
                                        if (in_array($termid, $include_category)) {
                                            $totalselectedvalue[$productid] = $item['line_subtotal'];
                                        }
                                    } else {
                                        $totalselectedvalue[] = $woocommerce->cart->subtotal;
                                    }
                                }
                            }
                        }
                    }
                    $points_for_include_product_sum = array_sum($totalselectedvalue);
                    $points_for_include_product = $redeem_conversion * $points_for_include_product_sum;
                    $points_for_redeeming = $points_for_include_product / $points_conversion_value;
                }
                $getinfousernickname = get_user_by('id', get_current_user_id());
                $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                $minimum_cart_total_redeem = get_option('rs_minimum_cart_total_points');

                $cart_subtotal_redeem_amount = $woocommerce->cart->subtotal;                
                $user_ID = get_current_user_id();
                $current_points_user = RSPointExpiry::get_sum_of_total_earned_points($user_ID);
                if ($current_points_user > '0') {
                    $minimum_cart_total_redeem_checkout = get_option('rs_minimum_cart_total_points');
                    if (get_option('woocommerce_prices_include_tax') == 'yes') {
                        if (get_option('woocommerce_tax_display_cart') == 'incl') {
                            $cart_subtotal_redeem_amount_checkout = $woocommerce->cart->subtotal;
                        } else {
                            $cart_subtotal_redeem_amount_checkout = $woocommerce->cart->subtotal;
                        }
                    } else {
                        if (get_option('woocommerce_tax_display_cart') == 'incl') {
                            $cart_subtotal_redeem_amount_checkout = $woocommerce->cart->subtotal_ex_tax;
                        } else {
                            $cart_subtotal_redeem_amount_checkout = $woocommerce->cart->subtotal_ex_tax;
                        }
                    }
                    if (get_option('rs_show_hide_redeem_field_checkout') == '1') {
                        $user_ID = get_current_user_id();
                        $checkfirstimeredeem = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($user_ID, 'rsfirsttime_redeemed');
                        if ($checkfirstimeredeem != '1') {
                            $userid = get_current_user_id();
                            $banning_type = FPRewardSystem::check_banning_type($userid);
                            if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
                                if (get_option('rs_show_hide_redeem_it_field_checkout') == '1') {
                                    if (get_option('rs_redeem_field_type_option_checkout') == '1') {
                                        if ($current_points_user >= get_option("rs_first_time_minimum_user_points")) {
                                            foreach ($woocommerce->cart->cart_contents as $key) {
                                                $product_id = $key['product_id'];
                                                $type[] = check_display_price_type($product_id);
                                                $enable = calculate_point_price_for_products($product_id);
                                                if ($enable[$product_id] != '') {
                                                    $cart_object[] = $enable[$product_id];
                                                }
                                            }
                                            if (empty($cart_object)) {
                                                if (!in_array(2, $type)) {
                                                    if (get_option('rs_redeem_field_type_option_checkout') == '1') {
                                                        $user_ID = get_current_user_id();
                                                        $getinfousernickname = get_user_by('id', $user_ID);
                                                        $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                                                        $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
                                                        $array = $woocommerce->cart->get_applied_coupons();

                                                        if (!in_array($auto_redeem_name, $array)) {
                                                            ?>
                                                            <div class="redeeemit">
                                                                <div class="woocommerce-info"><?php echo get_option('rs_reedming_field_label_checkout'); ?> <a href="javascript:void(0)" class="redeemit"> <?php echo get_option('rs_reedming_field_link_label_checkout'); ?></a></div>
                                                            </div>
                                                            <?php
                                                        }
                                                    } else {

                                                        self::reward_checkout_redeeming_type_button($cart_subtotal_redeem_amount_checkout, $minimum_cart_total_redeem_checkout);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $getuserid = get_current_user_id();
                                        $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem_checkout');
                                        $cart_total_in_amount = $limitation_percentage_for_redeeming / 100;
                                        $updated_cart_total_in_amount = $cart_total_in_amount * $cart_subtotal_redeem_amount_checkout;
                                        $redeem_conversion = wc_format_decimal(get_option('rs_redeem_point'));
                                        $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                                        $points_for_redeem_in_points = $updated_cart_total_in_amount * $redeem_conversion;
                                        $updated_points_for_redeeming = $points_for_redeem_in_points / $points_conversion_value;
                                        $currency_symbol_string_to_find = "[currencysymbol]";
                                        $cartpoints_string_to_replace = "[cartredeempoints]";
                                        $currency_symbol_string_to_find = "[currencysymbol]";
                                        $cuurency_value_string_to_find = "[pointsvalue]";
                                        if ($current_points_user >= $updated_points_for_redeeming) {
                                            $redeem_button_message_more = get_option('rs_redeeming_button_option_message');
                                            $percentage_string_to_replace = "[redeempercent]";
                                            $cuurency_value_string_to_find = "[pointsvalue]";
                                            $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                                            $points_currency_value = $updated_cart_total_in_amount;
                                            $points_currency_amount_to_replace = $updated_points_for_redeeming;
                                            $points_for_redeeming = $updated_points_for_redeeming;
                                            if (get_option('rs_apply_redeem_basedon_cart_or_product_total') == '2') {
                                                if ($points_for_include_product != '') {
                                                    $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem_checkout');
                                                    $cart_total_in_amount = $limitation_percentage_for_redeeming / 100;
                                                    $updated_cart_total_in_amount = $cart_total_in_amount * $points_for_include_product_sum;
                                                    $points_for_include_product = $redeem_conversion * $updated_cart_total_in_amount;
                                                    $points_for_redeeming = $points_for_include_product / $points_conversion_value;
                                                    $points_currency_value = $updated_cart_total_in_amount;
                                                }
                                            }
                                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                            $points_for_redeeming = round($points_for_redeeming, $roundofftype);

                                            $redeem_button_message_more = get_option('rs_redeeming_button_option_message');
                                            $currency_symbol_string_to_replace = get_woocommerce_formatted_price($points_currency_value);
                                            $redeem_button_message_replaced_first = str_replace($cuurency_value_string_to_find, $currency_symbol_string_to_replace, $redeem_button_message_more);
                                            $redeem_button_message_replaced_second = str_replace($currency_symbol_string_to_find, "", $redeem_button_message_replaced_first);
                                            $redeem_button_message_replaced_third = str_replace($cartpoints_string_to_replace, $points_for_redeeming, $redeem_button_message_replaced_second);
                                        } else {

                                            $points_for_redeeming = $current_points_user;
                                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                            $points_for_redeeming = round($points_for_redeeming, $roundofftype);
                                            $redeem_button_message_more = get_option('rs_redeeming_button_option_message');
                                            $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                                            $points_currency_value = $points_for_redeeming / $redeem_conversion;
                                            $points_currency_amount_to_replace = $points_currency_value * $points_conversion_value;
                                            if (get_option('rs_apply_redeem_basedon_cart_or_product_total') == '2') {
                                                if ($points_for_include_product != '') {
                                                    $points_for_redeeming1 = $points_for_include_product / $points_conversion_value;
                                                    if ($user_current_points > $points_for_redeeming1) {
                                                        $points_for_redeeming = $points_for_redeeming1;
                                                        $points_currency_value = $getsumofselectedproduct;
                                                        $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem_checkout');
                                                        $cart_total_in_amount = $limitation_percentage_for_redeeming / 100;
                                                        $updated_cart_total_in_amount = $cart_total_in_amount * $points_for_include_product_sum;
                                                        $points_for_include_product = $redeem_conversion * $updated_cart_total_in_amount;
                                                        $points_for_redeeming = $points_for_include_product / $points_conversion_value;
                                                        $points_currency_value = $updated_cart_total_in_amount;
                                                    }
                                                }
                                            }
                                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                            $points_for_redeeming = round($points_for_redeeming, $roundofftype);

                                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                            $points_currency_amount_to_replace = round($points_currency_amount_to_replace, $roundofftype);
                                            $currency_symbol_string_to_replace = get_woocommerce_formatted_price($points_currency_value);
                                            $redeem_button_message_replaced_first = str_replace($currency_symbol_string_to_find, "", $redeem_button_message_more);
                                            $redeem_button_message_replaced_second = str_replace($cuurency_value_string_to_find, $currency_symbol_string_to_replace, $redeem_button_message_replaced_first);
                                            $redeem_button_message_replaced_third = str_replace($cartpoints_string_to_replace, $points_for_redeeming, $redeem_button_message_replaced_second);
                                        }


                                        if ($current_points_user >= get_option("rs_first_time_minimum_user_points")) {
                                            if ($cart_subtotal_redeem_amount_checkout >= $minimum_cart_total_redeem_checkout) {
                                                foreach ($woocommerce->cart->cart_contents as $item) {
                                                    $product_id = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
                                                    $type[] = check_display_price_type($product_id);
                                                    $enable = calculate_point_price_for_products($product_id);
                                                    if ($enable[$product_id] != '') {
                                                        $cart_object[] = $enable[$product_id];
                                                    }
                                                }
                                                if (empty($cart_object)) {
                                                    if (!in_array(2, $type)) {
                                                        $user_ID = get_current_user_id();
                                                        $getinfousernickname = get_user_by('id', $user_ID);
                                                        $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                                                        $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
                                                        $array = $woocommerce->cart->get_applied_coupons();
                                                        if (!in_array($auto_redeem_name, $array)) {
                                                            ?>
                                                            <form method="post">
                                                                <div class="woocommerce-info sumo_reward_points_checkout_apply_discount"><?php echo $redeem_button_message_replaced_third; ?>
                                                                    <input id="rs_apply_coupon_code_field" class="input-text" type="hidden"  value="<?php echo $points_for_redeeming; ?> " name="rs_apply_coupon_code_field">
                                                                    <input class="button <?php echo get_option('rs_extra_class_name_apply_reward_points'); ?>" type="submit" id='mainsubmi' value="<?php echo get_option('rs_redeem_field_submit_button_caption'); ?>" name="rs_apply_coupon_code2">
                                                                    <div class='rs_warning_message' style='display:inline-block;color:red'></div>
                                                                </div>
                                                            </form>
                                                            <?php
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                            <form name="checkout_redeeming" class="checkout_redeeming" method="post">
                                <?php
                                RSFunctionForCart::reward_system_add_message_after_cart_table();
                                ?>
                            </form>
                            <?php
                        } else {

                            if ($current_points_user >= get_option("rs_minimum_user_points_to_redeem")) {
                                $userid = get_current_user_id();
                                $banning_type = FPRewardSystem::check_banning_type($userid);
                                if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
                                    if (get_option('rs_show_hide_redeem_it_field_checkout') == '1') {
                                        if (get_option('rs_redeem_field_type_option_checkout') == '1') {
                                            if ($current_points_user >= get_option("rs_first_time_minimum_user_points")) {

                                                foreach ($woocommerce->cart->cart_contents as $key) {
                                                    $product_id = $key['variation_id'] != 0 ? $key['variation_id'] : $key['product_id'];
                                                    $type[] = check_display_price_type($product_id);
                                                    $enable = calculate_point_price_for_products($product_id);
                                                    if ($enable[$product_id] != '') {
                                                        $cart_object[] = $enable[$product_id];
                                                    }
                                                }
                                                if (empty($cart_object)) {
                                                    if (!in_array(2, $type)) {
                                                        if (get_option('rs_redeem_field_type_option_checkout') == '1') {

                                                            $user_ID = get_current_user_id();
                                                            $getinfousernickname = get_user_by('id', $user_ID);
                                                            $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                                                            $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
                                                            $array = $woocommerce->cart->get_applied_coupons();
                                                            if (!in_array($auto_redeem_name, $array)) {
                                                                ?>
                                                                <div class="redeeemit">
                                                                    <div class="woocommerce-info"><?php echo get_option('rs_reedming_field_label_checkout'); ?> <a href="javascript:void(0)" class="redeemit"> <?php echo get_option('rs_reedming_field_link_label_checkout'); ?></a></div>
                                                                </div>
                                                                <?php
                                                            }
                                                        } else {
                                                            self::reward_checkout_redeeming_type_button($cart_subtotal_redeem_amount_checkout, $minimum_cart_total_redeem_checkout);
                                                        }
                                                    }
                                                }
                                            }
                                        } else {

                                            if (get_option('woocommerce_prices_include_tax') == 'yes') {
                                                if (get_option('woocommerce_tax_display_cart') == 'incl') {
                                                    $current_carttotal_amount = $woocommerce->cart->subtotal;
                                                } else {
                                                    $current_carttotal_amount = $woocommerce->cart->subtotal;
                                                }
                                            } else {
                                                if (get_option('woocommerce_tax_display_cart') == 'incl') {
                                                    $current_carttotal_amount = $woocommerce->cart->subtotal_ex_tax;
                                                } else {
                                                    $current_carttotal_amount = $woocommerce->cart->subtotal_ex_tax;
                                                }
                                            }
                                            $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem_checkout');
                                            $cart_total_in_amount = $limitation_percentage_for_redeeming / 100;
                                            $updated_cart_total_in_amount = $cart_total_in_amount * $current_carttotal_amount;
                                            $redeem_conversion = wc_format_decimal(get_option('rs_redeem_point'));
                                            $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                                            $points_for_redeem_in_points = $updated_cart_total_in_amount * $redeem_conversion;
                                            $updated_points_for_redeeming = $points_for_redeem_in_points / $points_conversion_value;
                                            $cartpoints_string_to_replace = "[cartredeempoints]";
                                            $currency_symbol_string_to_find = "[currencysymbol]";
                                            $cuurency_value_string_to_find = "[pointsvalue]";
                                            if ($current_points_user >= $updated_points_for_redeeming) {
                                                $redeem_button_message_more = get_option('rs_redeeming_button_option_message');
                                                $cuurency_value_string_to_find = "[pointsvalue]";
                                                $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                                                $points_currency_value = $updated_cart_total_in_amount;
                                                $points_currency_amount_to_replace = $updated_points_for_redeeming;
                                                $points_for_redeeming = $updated_points_for_redeeming;
                                                if (get_option('rs_apply_redeem_basedon_cart_or_product_total') == '2') {
                                                    if ($points_for_include_product != '') {
                                                        $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem_checkout');
                                                        $cart_total_in_amount = $limitation_percentage_for_redeeming / 100;
                                                        $updated_cart_total_in_amount = $cart_total_in_amount * $points_for_include_product_sum;
                                                        $points_for_include_product = $redeem_conversion * $updated_cart_total_in_amount;
                                                        $points_for_redeeming = $points_for_include_product / $points_conversion_value;
                                                        $points_currency_value = $updated_cart_total_in_amount;
                                                    }
                                                }
                                                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                                $points_for_redeeming = round($points_for_redeeming, $roundofftype);
                                                $redeem_button_message_more = get_option('rs_redeeming_button_option_message');
                                                $currency_symbol_string_to_replace = get_woocommerce_formatted_price($points_currency_value);

                                                $redeem_button_message_replaced_first = str_replace($cuurency_value_string_to_find, $currency_symbol_string_to_replace, $redeem_button_message_more);
                                                $redeem_button_message_replaced_second = str_replace($currency_symbol_string_to_find, "", $redeem_button_message_replaced_first);
                                                $redeem_button_message_replaced_third = str_replace($cartpoints_string_to_replace, $points_for_redeeming, $redeem_button_message_replaced_second);
                                            } else {

                                                $points_for_redeeming = $current_points_user;
                                                if (get_option('rs_apply_redeem_basedon_cart_or_product_total') == '2') {
                                                    if ($points_for_include_product != '') {
                                                        $points_for_redeeming1 = $points_for_include_product / $points_conversion_value;
                                                        if ($user_current_points > $points_for_redeeming1) {
                                                            $points_for_redeeming = $points_for_redeeming1;
                                                            $points_currency_value = $getsumofselectedproduct;
                                                            $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem_checkout');
                                                            $cart_total_in_amount = $limitation_percentage_for_redeeming / 100;
                                                            $updated_cart_total_in_amount = $cart_total_in_amount * $points_for_include_product_sum;

                                                            $points_for_include_product = $redeem_conversion * $updated_cart_total_in_amount;
                                                            $points_for_redeeming = $points_for_include_product / $points_conversion_value;
                                                            $points_currency_value = $updated_cart_total_in_amount;
                                                        } else {
                                                            
                                                        }
                                                    }
                                                }
                                                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                                $points_for_redeeming = round($points_for_redeeming, $roundofftype);

                                                $redeem_button_message_more = get_option('rs_redeeming_button_option_message_checkout');
                                                $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                                                $points_currency_value = $points_for_redeeming / $redeem_conversion;
                                                $points_currency_amount_to_replace = $points_currency_value * $points_conversion_value;
                                                $currency_symbol_string_to_replace = get_woocommerce_formatted_price($points_currency_value);
                                                $redeem_button_message_replaced_first = str_replace($currency_symbol_string_to_find, "", $redeem_button_message_more);
                                                $redeem_button_message_replaced_second = str_replace($cuurency_value_string_to_find, $currency_symbol_string_to_replace, $redeem_button_message_replaced_first);
                                                $redeem_button_message_replaced_third = str_replace($cartpoints_string_to_replace, $points_for_redeeming, $redeem_button_message_replaced_second);
                                            }
                                            if ($cart_subtotal_redeem_amount >= $minimum_cart_total_redeem) {
                                                $user_ID = get_current_user_id();
                                                $getinfousernickname = get_user_by('id', $user_ID);
                                                $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                                                $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
                                                $array = $woocommerce->cart->get_applied_coupons();
                                                foreach ($woocommerce->cart->cart_contents as $item) {
                                                    $product_id = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
                                                    $type[] = check_display_price_type($product_id);
                                                    $enable = calculate_point_price_for_products($product_id);
                                                    if ($enable[$product_id] != '') {
                                                        $cart_object[] = $enable[$product_id];
                                                    }
                                                }
                                                if (empty($cart_object)) {

                                                    if (!in_array(2, $type)) {
                                                        if (!in_array($auto_redeem_name, $array)) {
                                                            ?>
                                                            <form method="post">
                                                                <div class="woocommerce-info sumo_reward_points_checkout_apply_discount"><?php echo $redeem_button_message_replaced_third; ?>
                                                                    <input id="rs_apply_coupon_code_field" clasiss="input-text" type="hidden"  value="<?php echo $points_for_redeeming; ?> " name="rs_apply_coupon_code_field">
                                                                    <input class="button <?php echo get_option('rs_extra_class_name_apply_reward_points'); ?>" type="submit" id='mainsubmi' value="<?php echo get_option('rs_redeem_field_submit_button_caption'); ?>" name="rs_apply_coupon_code2">
                                                                    <div class='rs_warning_message' style='display:inline-block;color:red'></div>
                                                                </div>
                                                            </form>
                                                            <?php
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>
                                <form name="checkout_redeeming" class="checkout_redeeming" method="post">
                                    <?php
                                    RSFunctionForCart::reward_system_add_message_after_cart_table();
                                    ?>
                                </form>
                                <?php
                            } else {
                                $rs_minpoints_after_first_redeem = get_option('rs_min_points_after_first_error');
                                $min_points_to_replace = get_option('rs_minimum_user_points_to_redeem');
                                $min_points_to_find = "[points_after_first_redeem]";
                                $min_points_after_first_replaced = str_replace($min_points_to_find, $min_points_to_replace, $rs_minpoints_after_first_redeem);
                                ?>
                                <div class="woocommerce-info"><?php echo $min_points_after_first_replaced; ?></div>
                                <?php
                            }
                        }
                    }
                    ?>

                    <script type = "text/javascript">
                    <?php if (get_option('rs_show_hide_redeem_it_field_checkout') == '1') { ?>
                            jQuery('.fp_apply_reward').css("display", "none");
                            jQuery('.woocommerce-info a.redeemit').click(function () {
                                jQuery('.fp_apply_reward').toggle();
                            });
                    <?php } ?>
                    </script>
                    <?php
                } else {
                    if (get_option('rs_show_hide_points_empty_error_message') == '1') {
                        $userid = get_current_user_id();
                        $banning_type = FPRewardSystem::check_banning_type($userid);
                        if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
                            $user_points_empty_message = get_option('rs_current_points_empty_error_message');
                            ?>
                            <div class="woocommerce-info"><?php echo $user_points_empty_message; ?></div>
                            <?php
                        }
                    }
                }
            }
        }

        public static function reward_checkout_redeeming_type_button($cart_subtotal_redeem_amount_checkout, $minimum_cart_total_redeem_checkout) {
            global $woocommerce;
            $getuserid = get_current_user_id();
            $current_points_user = RSPointExpiry::get_sum_of_total_earned_points($getuserid);
            $current_carttotal_amount = $woocommerce->cart->subtotal;
            $redeem_conversion = get_option('rs_redeem_point');
            $current_carttotal_in_points = $current_carttotal_amount * $redeem_conversion;
            $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem');
            $updated_points_step1 = $current_carttotal_in_points / 100;
            $updated_points_for_redeeming = $updated_points_step1 * $limitation_percentage_for_redeeming;
            $currency_symbol_string_to_find = "[currencysymbol]";
            $cartpoints_string_to_replace = "[cartredeempoints]";
            $currency_symbol_string_to_find = "[currencysymbol]";
            $cuurency_value_string_to_find = "[pointsvalue]";
            if ($current_points_user >= $updated_points_for_redeeming) {
                $points_for_redeeming = $updated_points_for_redeeming;
                $cuurency_value_string_to_find = "[pointsvalue]";
                $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                $points_currency_value = $updated_points_for_redeeming / $redeem_conversion;
                $points_currency_amount_to_replace = $updated_points_for_redeeming;
                $points_for_redeeming = $updated_points_for_redeeming / $points_conversion_value;
                $redeem_button_message_more = get_option('rs_redeeming_button_option_message_checkout');

                $currency_symbol_string_to_replace = get_woocommerce_formatted_price($points_currency_value);
                $redeem_button_message_replaced_first = str_replace($cuurency_value_string_to_find, $currency_symbol_string_to_replace, $redeem_button_message_more);
                $redeem_button_message_replaced_second = str_replace($currency_symbol_string_to_find, "", $redeem_button_message_replaced_first);
                $redeem_button_message_replaced_third = str_replace($cartpoints_string_to_replace, $points_for_redeeming, $redeem_button_message_replaced_second);
            } else {
                $points_for_redeeming = $current_points_user;
                $redeem_button_message_more = get_option('rs_redeeming_button_option_message_checkout');
                $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                $points_currency_value = $points_for_redeeming / $redeem_conversion;
                $points_currency_amount_to_replace = $points_currency_value * $points_conversion_value;
                $currency_symbol_string_to_replace = get_woocommerce_formatted_price($points_currency_value);
                $redeem_button_message_replaced_first = str_replace($currency_symbol_string_to_find, "", $redeem_button_message_more);
                $redeem_button_message_replaced_second = str_replace($cuurency_value_string_to_find, $currency_symbol_string_to_replace, $redeem_button_message_replaced_first);
                $redeem_button_message_replaced_third = str_replace($cartpoints_string_to_replace, $points_for_redeeming, $redeem_button_message_replaced_second);
            }
            if ($current_points_user >= get_option("rs_first_time_minimum_user_points")) {
                if ($cart_subtotal_redeem_amount_checkout >= $minimum_cart_total_redeem_checkout) {
                    $user_ID = get_current_user_id();
                    $getinfousernickname = get_user_by('id', $user_ID);
                    $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                    $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
                    $array = $woocommerce->cart->get_applied_coupons();
                    foreach ($woocommerce->cart->cart_contents as $item) {
                        $product_id = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
                        $type[] = check_display_price_type($product_id);
                        $enable = calculate_point_price_for_products($product_id);
                        if ($enable[$product_id] != '') {
                            $cart_object[] = $enable[$product_id];
                        }
                    }
                    if (empty($cart_object)) {
                        if (!in_array(2, $type)) {
                            if (!in_array($auto_redeem_name, $array)) {
                                ?>
                                <form method="post">
                                    <div class="woocommerce-info"><?php echo $redeem_button_message_replaced_third; ?>
                                        <input id="rs_apply_coupon_code_field" class="input-text" type="hidden" placeholder="<?php echo $placeholder; ?>" value="<?php echo $points_for_redeeming; ?> " name="rs_apply_coupon_code_field">                                            <input class="button <?php echo get_option('rs_extra_class_name_apply_reward_points'); ?>" type="submit" id='mainsubmi' value="<?php echo get_option('rs_redeem_field_submit_button_caption'); ?>" name="rs_apply_coupon_code">
                                    </div>
                                </form>
                                <?php
                            }
                        }
                    }
                }
            }
        }

        public static function your_current_points_cart_page() {
            if (get_option('rs_show_hide_message_for_my_rewards') == '1') {
                if (is_user_logged_in()) {
                    $user_ID = get_current_user_id();
                    $current_user_points = RSPointExpiry::get_sum_of_total_earned_points($user_ID);
                    if ($current_user_points > 0) {
                        $userid = get_current_user_id();
                        $banning_type = FPRewardSystem::check_banning_type($userid);
                        if ($banning_type != 'earningonly' && $banning_type != 'both') {
                            ?>
                            <div class="woocommerce-info sumo_reward_points_current_points_message">
                                <?php
                                $user_ID = get_current_user_id();
                                echo do_shortcode(get_option('rs_message_user_points_in_cart'));
                                ?>
                            </div>
                            <?php
                        }
                    }
                }
            }
        }

        public static function rs_notice_in_cart_for_tax() {
            if (get_option('woocommerce_calc_taxes') == 'yes' && get_option('rs_show_hide_message_notice_for_redeeming') == '1') {
                ?>
                <div class="woocommerce-error sumo_reward_points_notice">
                    <?php
                    echo get_option('rs_msg_for_redeem_when_tax_enabled');
                    ?>
                </div>
                <?php
            }
        }

        public static function rs_notice_in_checkout_for_tax() {
            if (get_option('woocommerce_calc_taxes') == 'yes' && get_option('rs_show_hide_message_notice_for_redeeming') == '1') {
                ?>
                <div class="woocommerce-error sumo_reward_points_notice">
                    <?php
                    echo get_option('rs_msg_for_redeem_when_tax_enabled');
                    ?>
                </div>
                <?php
            }
        }

        public static function your_current_points_checkout_page() {
            if (get_option('rs_show_hide_message_for_my_rewards_checkout_page') == '1') {
                if (is_user_logged_in()) {
                    $user_ID = get_current_user_id();
                    $current_user_points = RSPointExpiry::get_sum_of_total_earned_points($user_ID);
                    if ($current_user_points > 0) {
                        $userid = get_current_user_id();
                        $banning_type = FPRewardSystem::check_banning_type($userid);
                        if ($banning_type != 'earningonly' && $banning_type != 'both') {
                            ?>
                            <div class="woocommerce-info">
                                <?php
                                $user_ID = get_current_user_id();
                                echo do_shortcode(get_option('rs_message_user_points_in_checkout'));
                                ?>
                            </div>
                            <?php
                        }
                    }
                }
            }
        }

        public static function add_shortcode_for_user_points() {
            if (is_user_logged_in()) {
                $user_ID = get_current_user_id();
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                return "<strong>" . round(RSPointExpiry::get_sum_of_total_earned_points($user_ID), $roundofftype) . "</strong>";
            }
        }

        public static function add_shortcode_for_user_points_value() {
            if (is_user_logged_in()) {
                $user_ID = get_current_user_id();
                $current_user_points = RSPointExpiry::get_sum_of_total_earned_points($user_ID);
                $pointconversion = wc_format_decimal(get_option('rs_redeem_point'));
                $pointconversionvalue = wc_format_decimal(get_option('rs_redeem_point_value'));
                $pointswithvalue = $current_user_points / $pointconversion;
                $rewardpoints_amount = $pointswithvalue * $pointconversionvalue;
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                return get_woocommerce_formatted_price(round($rewardpoints_amount, $roundofftype));
            } else {
                $rewardpoints_amount = 0;
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                return get_woocommerce_formatted_price(round($rewardpoints_amount, $roundofftype));
            }
        }

        public static function display_complete_message_checkout_page() {
            global $totalrewardpointsnew;
            if (is_user_logged_in()) {
                $checkenableoption = RSFunctionForCart::check_the_applied_coupons();
                if (get_option('rs_show_hide_message_for_total_points_checkout_page') == '1') {
                    if ($checkenableoption == false) {
                        if (is_array($totalrewardpointsnew)) {
                            if (array_sum($totalrewardpointsnew) > 0) {
                                $totalrewardpoints = do_shortcode('[totalrewards]');
                                if ($totalrewardpoints > 0) {
                                    ?>
                                    <div class="woocommerce-info">
                                        <?php
                                        echo do_shortcode(get_option('rs_message_total_price_in_checkout'));
                                        ?>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                }
            }
        }

        public static function getshortcodetotal_rewards() {
            global $totalrewardpointsnew;
            //var_dump($totalrewardpointsnew);
            if (is_array($totalrewardpointsnew)) {
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                return round(array_sum($totalrewardpointsnew), $roundofftype);
            } else {
                return "<strong>  </strong>";
            }
        }

        public static function getvalueshortcodetotal_rewards() {
            $getrstotal = do_shortcode('[totalrewards]');
            $getcals = $getrstotal / wc_format_decimal(get_option('rs_redeem_point'));
            $updatedvalue = $getcals * wc_format_decimal(get_option('rs_redeem_point_value'));
            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
            return get_woocommerce_formatted_price(round($updatedvalue, $roundofftype));
        }

        public static function add_custom_message_to_payment_gateway_on_checkout($checkout) {
            if (get_option('rs_show_hide_message_payment_gateway_reward_points') == '1') {
                if (is_user_logged_in()) {
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery('.subinfogateway').parent().hide();
                            jQuery('#order_review').on('click', '.payment_methods input.input-radio', function () {
                                var orderpaymentgateway = jQuery(this).val();
                                var paymentgatewaytitle = jQuery('.payment_method_' + orderpaymentgateway).find('label').html();
                                var dataparam = ({
                                    action: 'rs_order_payment_gateway_reward',
                                    getpaymentgatewayid: orderpaymentgateway,
                                    getpaymenttitle: paymentgatewaytitle,
                                    userid: "<?php echo get_current_user_id(); ?>",
                                });
                                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                        function (response) {
                                            console.log(response);
                                            jQuery('.subinfogateway').parent().show();
                                            if(response.restrictedmsg != '' && orderpaymentgateway == 'reward_gateway'){                                                  
                                                jQuery('.rewardgatewayrestriction').css('display', 'inline-block');
                                                jQuery('.rewardgatewayrestriction').html(response.restrictedmsg);
                                            }else{                                                
                                                jQuery('.rewardgatewayrestriction').css('display', 'none');
                                            }
                                            jQuery('.rspgpoints').html(response.rewardpoints);                                            
                                            var responsepoints = jQuery('.rspgpoints').html(response.rewardpoints);
                                            if ((response.rewardpoints == null) || (response.rewardpoints == '')) {
                                                jQuery('.rspgpoints').parent().css('display', 'none');
                                            } else {
                                                jQuery('.rspgpoints').parent().css('display', 'inline-block');
                                            }
                                            if (response.title !== null) {
                                                jQuery('.subinfogateway').html(response.title.replace(/\\/g, ''));
                                            }
                                        }, 'json');
                            });
                        });
                    </script>
                    <?php
                    $getmessage = get_option('rs_message_payment_gateway_reward_points');
                    $findarray = array('[paymentgatewaytitle]', '[paymentgatewaypoints]');
                    $replacearray = array('<label class="subinfogateway">  </label>', '<span class="rspgpoints"></span>');
                    $output = str_replace($findarray, $replacearray, $getmessage);
                    ?>
                    <div class="woocommerce-info rewardgatewayrestriction"><?php echo $output; ?></div>
                    <?php
                }
            }
        }

        public static function payment_gateway_reward_points_process_ajax_request() {                       
            if (isset($_POST['getpaymentgatewayid'])) {
                $gatewayid = $_POST['getpaymentgatewayid'];                
                $getthevalue = rs_function_to_get_gateway_point($order_id = '',$_POST['userid'],$gatewayid);
                $getthetitle = $_POST['getpaymenttitle'];
                $restrictedmsg = '';
                if(get_option('rs_disable_point_if_reward_points_gateway') == 'yes'){
                    $getrestrictedmsg = get_option('rs_restriction_msg_for_reward_gatweway');
                    $restrictedmsg = str_replace('[paymentgatewaytitle]',$getthetitle,$getrestrictedmsg);
                }
                echo json_encode(array('rewardpoints' => $getthevalue, 'title' => $getthetitle, 'restrictedmsg' => $restrictedmsg));
            }
            exit();
        }

        public static function show_message_for_guest_cart_page() {
            global $totalrewardpointsnew;

            if (!is_user_logged_in()) {
                $totalrewardpoints = do_shortcode('[totalrewards]');
                if (get_option('rs_enable_acc_creation_for_guest_checkout_page') == 'no' && get_option('rs_show_hide_message_for_guest') == '1') {
                    ?>
                    <div class="woocommerce-info"><?php echo do_shortcode(get_option('rs_message_for_guest_in_cart')); ?></div>
                    <?php
                }
            }
        }

        public static function show_message_for_guest_checkout_page() {
            if (!is_user_logged_in()) {
                if (get_option('rs_enable_acc_creation_for_guest_checkout_page') == 'no' && get_option('rs_show_hide_message_for_guest_checkout_page') == '1') {
                    ?>
                    <div class="woocommerce-info"><?php echo do_shortcode(get_option('rs_message_for_guest_in_checkout')); ?></div>
                    <?php
                }
            }
        }

        /**
         * Force Display Signup on Checkout for Guest. 
         * Since Guest can't have the permission to buy Subscriptions.
         * @param object $checkout
         */
        public static function rs_function_to_enable_guest_signup_on_checkout($checkout) {
            global $messageglobal;
            if (!is_user_logged_in() && is_checkout() && isset($checkout->enable_signup) && isset($checkout->enable_guest_checkout) && get_option('rs_enable_acc_creation_for_guest_checkout_page') == 'yes' && is_array($messageglobal) && $messageglobal != NULL) {
                $checkout->enable_signup = true;
                $checkout->enable_guest_checkout = false;
            }
        }

        /**
         * To Create account for Guest. 
         */
        public static function rs_function_to_create_account_for_guest() {
            $checkrewardenable = self::rs_function_to_return_reward_points_enabled_product();
            if (!is_user_logged_in() && is_checkout() && get_option('rs_enable_acc_creation_for_guest_checkout_page') == 'yes' && $checkrewardenable) {
                $_POST['createaccount'] = 1;
            }
        }

        public static function rs_function_to_return_reward_points_enabled_product() {
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                if (isset($cart_item['product_id'])) {
                    $productid = $cart_item['product_id'];
                    $variationid = isset($cart_item['variation_id']) ? $cart_item['variation_id'] : '0';                    
                    $checklevel = 'yes';
                    $checked_level_for_reward_points = check_level_of_enable_reward_point($productid, $variationid,$cart_item, $checklevel, $referred_user = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
                    if ($checked_level_for_reward_points != 0) {
                        return true;
                    }
                }
            }
            return false;
        }

        public static function get_my_account_url_link() {
            global $woocommerce;
            $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
            $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
            return '<a href=' . $myaccountlink . '>' . $myaccounttitle . '</a>';
        }

        public static function rs_hide_redeeming_field_in_both_cart_and_checkout() {
            global $woocommerce;
            $user_ID = get_current_user_id();
            $getinfousernickname = get_user_by('id', $user_ID);
            $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
            $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
            $autoredeem = 'auto_redeem_' . strtolower("$couponcodeuserlogin");
            $array = $woocommerce->cart->get_applied_coupons();
            $show_hide_coupon_redeem_field = get_option('rs_show_hide_redeem_field');
            $show_hide_redeem_field = get_option('rs_show_redeeming_field');
            $check_discount_is_applied = function_exists('check_sumo_discounts_are_applied_in_cart') ? check_sumo_discounts_are_applied_in_cart() : 'false';
            if ($show_hide_coupon_redeem_field == '1') {
                echo self::rs_hide_redeem_field($show_hide_redeem_field, $check_discount_is_applied);
            } else if ($show_hide_coupon_redeem_field == '2') {
                echo self::rs_script_and_style_to_hide_coupon_field();
                echo self::rs_hide_redeem_field($show_hide_redeem_field, $check_discount_is_applied);
            } else if ($show_hide_coupon_redeem_field == '3') {
                echo self::rs_script_to_hide_redeem_field_in_cart();
            } else if ($show_hide_coupon_redeem_field == '4') {
                echo self::rs_script_to_hide_redeem_field_in_cart();
                echo self::rs_script_and_style_to_hide_coupon_field();
            } else {
                echo self::rs_hide_redeem_field($show_hide_redeem_field, $check_discount_is_applied);
            }
        }

        public static function rs_hide_redeem_field($show_hide_redeem_field, $check_discount_is_applied) {
            if ($show_hide_redeem_field == '2' && $check_discount_is_applied) {
                echo self::rs_script_to_hide_redeem_field_in_cart();
                echo self::rs_style_to_hide_redeem_field_in_checkout();
            }
        }

        public static function rs_script_to_hide_redeem_field_in_cart() {
            ?>     
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery("#mainsubmi").parent().hide();
                });
            </script>
            <?php
        }

        public static function rs_style_to_hide_redeem_field_in_checkout() {
            ?>
            <style type="text/css">
                .redeeemit{
                    display: none !important;
                }
            </style>
            <?php
        }

        public static function rs_script_and_style_to_hide_coupon_field() {
            ?>
            <style type="text/css">
            <?php if (is_cart()) { ?>
                    .coupon{
                        display: none;
                    }
            <?php } ?>
            </style>
            <?php if (is_checkout()) { ?>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        jQuery(".showcoupon").parent().hide();
                    });
                </script>
                <?php
            }
        }

    }

    RSFunctionForCheckout::init();
}