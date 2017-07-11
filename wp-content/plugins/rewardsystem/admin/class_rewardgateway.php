<?php

function init_reward_gateway_class() {

    if (!class_exists('WC_Payment_Gateway'))
        return;

    class WC_Reward_Gateway extends WC_Payment_Gateway {

        public function __construct() {
            global $woocommerce;
            $this->id = 'reward_gateway';
            $this->method_title = __('SUMO Reward Points Payment Gateway', 'rewardasystem');
            $this->has_fields = false; //Load Form Fields
            $this->init_form_fields();
            $this->init_settings();
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->is_forced_automatic_subscription_payment = $this->get_option('rs_subscription_based_payment_option') == "yes" && $this->get_option('rs_force_auto_r_manual_subscription_payment') == "2";
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

            if (class_exists('SUMOSubscriptions')) {
                add_action('admin_head', array($this, 'perform_script'));
            }
        }

        function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'rewardsystem'),
                    'type' => 'checkbox',
                    'label' => __('Enable Rewards Point Gateway', 'rewardsystem'),
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => __('Title', 'rewardsystem'),
                    'type' => 'text',
                    'description' => __('This Controls the Title which the user sees during checkout', 'rewardsystem'),
                    'default' => __('SUMO Reward Points Payment Gateway', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => __('Description', 'rewardsystem'),
                    'type' => 'textarea',
                    'description' => __('This controls the description which the user sees during checkout.', 'rewardsystem'),
                    'default' => __('Pay with your SUMO Reward Points', 'rewardsystem'),
                    'desc_tip' => true,
                ),
                'error_payment_gateway' => array(
                    'title' => 'Error Message',
                    'type' => 'textarea',
                    'description' => __('This Controls the errror message which is displayed during Checkout', 'rewardsystem'),
                    'desc_tip' => true,
                    'default' => __('You need [needpoints] Points in your Account .But You have only [userpoints] Points.', 'rewardsystem'),
                ),
                'error_message_for_payment_gateway' => array(
                    'title' => 'Error Message for Payment Gateway',
                    'type' => 'textarea',
                    'description' => __('This Controls the error message which is displayed during Checkout', 'rewardsystem'),
                    'desc_tip' => true,
                    'default' => __('Maximum Cart Total has been Limited to [maximum_cart_total]'),
                ),
            );

            if (class_exists('SUMOSubscriptions')) {
                $this->form_fields['rs_subscription_based_payment_option'] = array(
                    'title' => __('Remove the option for the Subscriber to choose Automatic/Manual Payment when placed using SUMO Reward Points Payment Gateway', 'rewardsystem'),
                    'type' => 'checkbox',
                    'std' => 'no',
                    'default' => 'no',
                    'desc_tip' => __('This option controls whether the user should have an option for preapproving the future subscription renewals.', 'rewardsystem'),
                );

                $this->form_fields['rs_force_auto_r_manual_subscription_payment'] = array(
                    'title' => __('Force Automatic/Manual Payment', 'rewardsystem'),
                    'type' => 'select',
                    'css' => 'width:315px',
                    'std' => '2',
                    'options' => array(
                        '1' => __('Force Manual Reward Points Payment', 'rewardsystem'),
                        '2' => __('Force Automatic Reward Points Payment', 'rewardsystem'),
                    ),
                    'desc' => __('This option controls how the subscription renewals has to be managed when the user purchases using inbuilt reward points payment gateway.', 'rewardsystem'),
                    'desc_tip' => true
                );
            }
        }

        function perform_script() {
            if (isset($_GET['page']) && isset($_GET['tab']) && isset($_GET['section']) &&
                    $_GET['page'] == "wc-settings" && $_GET['tab'] == "checkout" && $_GET['section'] == "reward_gateway") {
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        if (jQuery("#woocommerce_reward_gateway_rs_subscription_based_payment_option").is(":checked")) {
                            jQuery("#woocommerce_reward_gateway_rs_force_auto_r_manual_subscription_payment").closest('tr').show();
                        } else {
                            jQuery("#woocommerce_reward_gateway_rs_force_auto_r_manual_subscription_payment").closest('tr').hide();
                        }

                        jQuery("#woocommerce_reward_gateway_rs_subscription_based_payment_option").change(function () {
                            if (this.checked) {
                                jQuery("#woocommerce_reward_gateway_rs_force_auto_r_manual_subscription_payment").closest('tr').show();
                            } else {
                                jQuery("#woocommerce_reward_gateway_rs_force_auto_r_manual_subscription_payment").closest('tr').hide();
                            }
                        });
                    });
                </script> 
                <?php
            }
        }

        function process_payment($order_id) {
            global $woocommerce;
            $redeemedpoints = gateway_points($order_id);
            $order = new WC_Order($order_id);
            $getmaxoption = get_option('rs_max_redeem_discount_for_sumo_reward_points');
            $getuserid = rs_get_order_obj($order);
            $payment_method = $getuserid['payment_method'];
            $getuserid = $getuserid['order_userid'];
            $couponcodeuserid = get_userdata($getuserid);
            $couponcodeuserlogin = is_object($couponcodeuserid) ? $couponcodeuserid->user_login : 'Guest';
            $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
            $current_conversion = wc_format_decimal(get_option('rs_redeem_point'));
            $point_amount = wc_format_decimal(get_option('rs_redeem_point_value'));
            $getmyrewardpoints = RSPointExpiry::get_sum_of_total_earned_points($getuserid);
            $ordertotal = $order->get_total();
            if (isset($woocommerce->cart->coupon_discount_amounts["$usernickname"])) {
                $total4 = $woocommerce->cart->coupon_discount_amounts[$usernickname];
                $total5 = $total4 * $current_conversion;
                $total6 = $total5 / $point_amount;
                $userpoints = $getmyrewardpoints - $total6;
            } else {
                $userpoints = $getmyrewardpoints != NULL ? $getmyrewardpoints : '0';
            }
            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
            update_post_meta($order_id, 'total_redeem_points_for_order_point_price', $redeemedpoints);
            update_post_meta($order_id, 'frontendorder', 1);
            if ($userpoints < $redeemedpoints) {
                $error_msg = $this->get_option('error_payment_gateway');
                $find = array('[userpoints]', '[needpoints]');
                $roundvalueuserpoint = round($userpoints, $roundofftype);
                $roundvalueredeempoint = round($redeemedpoints, $roundofftype);
                $replace = array($roundvalueuserpoint, $roundvalueredeempoint);
                $finalreplace = str_replace($find, $replace, $error_msg);
                wc_add_notice(__($finalreplace, 'woocommerce'), 'error');
                return;
            } else {
                if ($getmaxoption != '') {
                    if ($getmaxoption > $ordertotal) {
                        $error_msg = $this->get_option('error_message_for_payment_gateway');
                        $find = array('[maximum_cart_total]');
                        $roundvaluemaxoption = round($getmaxoption, $roundofftype);
                        $replace = $roundvaluemaxoption;
                        $finalreplace = str_replace($find, get_woocommerce_currency_symbol() . $replace, $error_msg);
                        wc_add_notice(__($finalreplace, 'woocommerce'), 'error');
                        return;
                    }
                }
            }
            //For SUMOSubscriptions, Automatic Subscription Payment Compatibility.
            $renewal_order_id = rs_get_post_parent($order) > 0 ? $order_id : 0;
            $parent_id = rs_get_post_parent($order) > 0 ? rs_get_post_parent($order) : $order_id;

            if ((isset($_POST['rs_reward_points_payment_selection']) && $_POST['rs_reward_points_payment_selection'] == '1') || $this->is_forced_automatic_subscription_payment) {
                update_post_meta($parent_id, 'sumo_parent_order_auto_manual', "auto");
            } else {
                update_post_meta($parent_id, 'sumo_parent_order_auto_manual', "manual");
            }
            update_post_meta($parent_id, 'sumo_order_payment_method', $payment_method);
            update_post_meta($parent_id, 'sumo_totalamount', $order->get_total());

            $order->payment_complete();
            $order_status = get_option('rs_order_status_after_gateway_purchase');

            $order->update_status($order_status);
            //Reduce Stock Levels
            //  $order->reduce_order_stock();
            //Remove Cart
            $woocommerce->cart->empty_cart();

            //Redirect the User
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
            wc_add_notice(__('Payment error:', 'woothemes') . $error_message, 'error');
            return;
        }

    }

    function gateway_points($order_id) {
        global $woocommerce;
        $couponamount1 = array();
        $array = array();
        $linetotal = array();
        $linetaxpoint=array();
        $order = new WC_Order($order_id);
        $linetax=array();
        update_post_meta($order_id, 'pointsvalue', '1');
        foreach ($order->get_items()as $item) {
            $product_id = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
            $enable = calculate_point_price_for_products($product_id);
            if ($enable[$product_id] != '') {
                $cart_object = $enable[$product_id] * $item['qty'];
                $array[] = $cart_object;
                 if (get_option('woocommerce_prices_include_tax') == 'no') {
                    $linetaxpoint[] = $item['line_subtotal_tax'];
                }
            } else {
                 $linetotal[] = $item['line_subtotal'];
                 $linetax[]=$item['line_subtotal_tax'];
            }
           
                $shipping_total = $order->get_total_shipping();
                $shiptax = $order->get_shipping_tax();
           
        }
       $totalrewardpointprice = array_sum($array);
        $totalbalancepoints = array_sum($linetotal);
        $linetax=array_sum($linetax);
        $pointpricetax=array_sum($linetaxpoint);
        $totalbalancepoints = $shipping_total +$shiptax+ $totalbalancepoints+$linetax+$pointpricetax;
        $newvalue = $totalbalancepoints / wc_format_decimal(get_option('rs_redeem_point_value'));
        $updatedvalue = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));

        $rewardpointscoupons = $order->get_items(array('coupon'));
        $order_user_id = rs_get_order_obj($order);
        $order_user_id = $order_user_id['order_userid'];
        $getuserdatabyid = get_user_by('id', $order_user_id);
        $getusernickname = is_object($getuserdatabyid) ? $getuserdatabyid->user_login : 'Guest';

        $auto_redeem_name = 'auto_redeem_' . strtolower($getusernickname);
        $maincouponchecker = 'sumo_' . strtolower($getusernickname);
        if (is_array($rewardpointscoupons) && !empty($rewardpointscoupons)) {
            foreach ($rewardpointscoupons as $coupon) {
                if ($auto_redeem_name == $coupon['name'] || $maincouponchecker == $coupon['name']) {
                    $couponamount1[] = $coupon['discount_amount'];
                }
            }
        }
        $couponamount = array_sum($couponamount1);
        $redeemedpoints = $totalrewardpointprice + $updatedvalue;
        $redeemedpoints = $redeemedpoints - $couponamount;
        return $redeemedpoints;
    }

    add_filter('woocommerce_available_payment_gateways', 'filter_gateway', 10, 1);

    add_filter('woocommerce_available_payment_gateways', 'filter_product', 10, 1);

    add_filter('woocommerce_available_payment_gateways', 'filter_product_point_price', 10, 1);

    function filter_product_point_price($gateways) {
        global $woocommerce;
        if (!empty($woocommerce->cart->cart_contents)) {
            foreach ($woocommerce->cart->cart_contents as $key => $values) {
                $productid = $values['variation_id'] != 0 ? $values['variation_id'] : $values['product_id'];
                $typeofprice = check_display_price_type($productid);
                if ($typeofprice == '2') {
                    foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                        if ($gateway->id != 'reward_gateway') {
                            unset($gateways[$gateway->id]);
                        }
                    }
                }
            }
        }

        return $gateways != 'NULL' ? $gateways : array();
    }

    function filter_product($gateways) {

        global $woocommerce;
        $enable = get_option('rs_exclude_products_for_redeeming');

        if ($enable == 'yes') {
            if (!empty($woocommerce->cart->cart_contents)) {
                foreach ($woocommerce->cart->cart_contents as $key => $values) {
                    $productid = $values['product_id'];

                    if (get_option('rs_exclude_products_to_enable_redeeming') != '') {
                        if (!is_array(get_option('rs_exclude_products_to_enable_redeeming'))) {
                            if ((get_option('rs_exclude_products_to_enable_redeeming') != '' && (get_option('rs_exclude_products_to_enable_redeeming') != NULL))) {
                                $product_id = explode(',', get_option('rs_exclude_products_to_enable_redeeming'));
                            }
                        } else {
                            $product_id = get_option('rs_exclude_products_to_enable_redeeming');
                        }
                        if (in_array($productid, (array) $product_id)) {
                            foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                if ($gateway->id == 'reward_gateway') {
                                    unset($gateways[$gateway->id]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $gateways != 'NULL' ? $gateways : array();
    }

    function filter_gateway($gateways) {
        if (get_option('rs_show_hide_reward_points_gatewy') == '1') {
            global $woocommerce;
            $includecategory = get_option('rs_select_category_for_purchase_using_points');
            if (is_array($includecategory)) {
                $include_category = (array) $includecategory; // Compatible for Old WooCommerce Version            
            } else {
                $include_category = (array) explode(',', $includecategory); // Compatible with Latest Version            
            }
            $enablecatagorypurchase = get_option('rs_enable_selected_category_for_purchase_using_points');
            $enableproductpurchase = get_option('rs_enable_selected_product_for_purchase_using_points');
            if (($enableproductpurchase == 'yes')) {                
                if (get_option('rs_select_product_for_purchase_using_points') != '') {
                    if (!is_array(get_option('rs_select_product_for_purchase_using_points'))) {
                        if ((get_option('rs_select_product_for_purchase_using_points') != '' && (get_option('rs_select_product_for_purchase_using_points') != NULL))) {
                            $product_id = explode(',', get_option('rs_select_product_for_purchase_using_points'));
                        }
                    } else {
                        $product_id = get_option('rs_select_product_for_purchase_using_points');
                    }
                    if (!empty($woocommerce->cart->cart_contents)) {
                        foreach ($woocommerce->cart->cart_contents as $key => $values) {
                            $productid = $values['product_id'];
                            if (in_array($productid, (array) $product_id)) {
                                foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                    if ($gateway->id != 'reward_gateway') {
                                        unset($gateways[$gateway->id]);
                                    }
                                }
                            } else {
                                if (($enablecatagorypurchase == 'yes')) {
                                    $productcategorys = get_the_terms($productid, 'product_cat');
                                    if ($productcategorys != false) {
                                        $condition = 'false';
                                        foreach ($productcategorys as $productcategory) {
                                            $termid = $productcategory->term_id;
                                            if (get_option('rs_select_category_for_purchase_using_points') != '') {
                                                if (in_array($termid, $include_category)) {
                                                    foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                                        if ($gateway->id != 'reward_gateway') {
                                                            unset($gateways[$gateway->id]);
                                                        }
                                                    }
                                                    $condition = 'true';
                                                }
                                            }
                                        }
                                        if ($condition == 'false') {
                                            if (get_option('rs_enable_gateway_visible_to_all_product') == 'no') {
                                                foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                                    if ($gateway->id == 'reward_gateway') {
                                                        unset($gateways[$gateway->id]);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if (get_option('rs_enable_gateway_visible_to_all_product') == 'no') {
                                            foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                                if ($gateway->id == 'reward_gateway') {
                                                    unset($gateways[$gateway->id]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if (($enablecatagorypurchase == 'yes')) {
                    foreach ($woocommerce->cart->cart_contents as $key => $values) {
                        $includecategory = get_option('rs_select_category_for_purchase_using_points');
                        if (is_array($includecategory)) {
                            $include_category = (array) $includecategory; // Compatible for Old WooCommerce Version            
                        } else {
                            $include_category = (array) explode(',', $includecategory); // Compatible with Latest Version            
                        }
                        $productid = $values['product_id'];
                        $productcategorys = get_the_terms($productid, 'product_cat');
                        if ($productcategorys != false) {
                            $getcount = count($productcategorys);
                            if ($getcount >= '1') {
                                $condition = 'false';
                                foreach ($productcategorys as $productcategory) {
                                    $termid = $productcategory->term_id;
                                    if (get_option('rs_select_category_for_purchase_using_points') != '') {

                                        if (in_array($termid, $include_category)) {
                                            foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                                if ($gateway->id != 'reward_gateway') {
                                                    unset($gateways[$gateway->id]);
                                                }
                                            }
                                            $condition = 'true';
                                        }
                                    }
                                }
                                if ($condition == 'false') {
                                    if (get_option('rs_enable_gateway_visible_to_all_product') == 'no') {
                                        foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                            if ($gateway->id == 'reward_gateway') {
                                                unset($gateways[$gateway->id]);
                                            }
                                        }
                                    }
                                }
                            } else {
                                @$termid = $productcategorys[0]->term_id;
                                if (get_option('rs_select_category_for_purchase_using_points') != '') {
                                    foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                        if ($gateway->id != 'reward_gateway') {
                                            unset($gateways[$gateway->id]);
                                        }
                                    }
                                }
                            }
                        } else {
                            if (get_option('rs_enable_gateway_visible_to_all_product') == 'no') {
                                foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                    if ($gateway->id == 'reward_gateway') {
                                        unset($gateways[$gateway->id]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            global $woocommerce;
            $type = array();
            $cart_object = array();
            $enableproductpurchase = get_option('rs_enable_selected_product_for_hide_gateway');
            if (($enableproductpurchase == 'yes')) {
                foreach ($woocommerce->cart->cart_contents as $key => $values) {
                    $productid = $values['product_id'];
                    if (get_option('rs_select_product_for_hide_gateway') != '') {
                        if (!is_array(get_option('rs_select_product_for_hide_gateway'))) {
                            if ((get_option('rs_select_product_for_hide_gateway') != '' && (get_option('rs_select_product_for_hide_gateway') != NULL))) {
                                $product_id = explode(',', get_option('rs_select_product_for_hide_gateway'));
                            }
                        } else {
                            $product_id = get_option('rs_select_product_for_hide_gateway');
                        }


                        if (in_array($productid, (array) $product_id)) {
                            foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                if ($gateway->id == 'reward_gateway') {
                                    unset($gateways[$gateway->id]);
                                }
                            }
                        } else {
                            if (get_option('rs_enable_gateway_visible_to_all_product') == 'no') {
                                foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                    if ($gateway->id == 'reward_gateway') {
                                        unset($gateways[$gateway->id]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $enablecatagorypurchase = get_option('rs_enable_selected_category_to_hide_gateway');
            if (($enablecatagorypurchase == 'yes')) {
                foreach ($woocommerce->cart->cart_contents as $key => $values) {
                    $includecategory = get_option('rs_select_category_to_hide_gateway');
                    if (is_array($includecategory)) {
                        $include_category = (array) $includecategory; // Compatible for Old WooCommerce Version            
                    } else {
                        $include_category = (array) explode(',', $includecategory); // Compatible with Latest Version            
                    }
                    $productid = $values['product_id'];
                    $productcategorys = get_the_terms($productid, 'product_cat');
                    if ($productcategorys != false) {
                        $getcount = count($productcategorys);
                        if ($getcount >= '1') {
                            foreach ($productcategorys as $productcategory) {
                                $termid = $productcategory->term_id;
                                if (get_option('rs_select_category_to_hide_gateway') != '') {
                                    if (in_array($termid, $include_category)) {
                                        foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                            if ($gateway->id == 'reward_gateway') {
                                                unset($gateways[$gateway->id]);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            @$termid = $productcategorys[0]->term_id;
                            if (get_option('rs_select_category_to_hide_gateway') != '') {
                                foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                    if ($gateway->id != 'reward_gateway') {
                                        unset($gateways[$gateway->id]);
                                    }
                                }
                            }
                        }
                    } else {
                        if (get_option('rs_enable_gateway_visible_to_all_product') == 'no') {
                            foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                                if ($gateway->id == 'reward_gateway') {
                                    unset($gateways[$gateway->id]);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $gateways != 'NULL' ? $gateways : array();
    }

    function rs_empty_cart() {
        global $woocommerce;
        $woocommerce->cart->empty_cart();
        $woocommerce->cart->remove_coupons();
        $passed = true;
    }

    add_filter('woocommerce_add_to_cart_validation', 'sell_individually_for_point_pricing', 10, 6);

    function sell_individually_for_point_pricing($passed, $product_id, $product_quantity, $variation_id = '', $variatins = array(), $cart_item_data = array()) {
        if (get_option('rs_show_hide_reward_points_gatewy') == '1') {
            global $woocommerce;
            $productnametodisplay = '';
            $sellindividuallyproducts = array();
            $msgtoreplace = array();
            $current_strtofind = "[productname]";
            $getstrtodisplay = get_option('rs_errmsg_when_other_products_added_to_cart_page');
            if (!is_array(get_option('rs_select_product_for_purchase_using_points'))) {
                $strtodisplay = explode(',', get_option('rs_select_product_for_purchase_using_points'));
            } else {
                $strtodisplay = get_option('rs_select_product_for_purchase_using_points');
            }
            if (!is_array(get_option('rs_select_category_for_purchase_using_points'))) {
                $category = (array) explode(',', get_option('rs_select_category_for_purchase_using_points'));
            } else {
                $category = (array) get_option('rs_select_category_for_purchase_using_points');
            }
            $enableproductpurchase = get_option('rs_enable_selected_product_for_purchase_using_points');
            $enablecatagorypurchase = get_option('rs_enable_selected_category_for_purchase_using_points');
            $varorproid = $variation_id == '' ? $product_id : $variation_id;
            if ($enableproductpurchase == 'yes') {
                if (in_array($varorproid, $strtodisplay)) {
                    if ($enablecatagorypurchase == 'yes') {
                        if (!empty($category)) {
                            $productcategorys = get_the_terms($varorproid, 'product_cat');
                            if ($productcategorys != false) {
                                $getcount = count($productcategorys);
                                $condition = 'false';
                                foreach ($productcategorys as $productcategory) {
                                    $termid = $productcategory->term_id;
                                    if (in_array($termid, $category)) {
                                        $condition = 'true';
                                        $passed = true;
                                    }
                                }
                                foreach ($woocommerce->cart->cart_contents as $key => $values) {
                                    $product = $values['variation_id'] > '0' ? $values['variation_id'] : $values['product_id'];
                                    $productcategorys = get_the_terms($product, 'product_cat');
                                    if ($productcategorys != false) {
                                        $condition = 'false';
                                        foreach ($productcategorys as $productcategory) {
                                            $termid = $productcategory->term_id;

                                            if (in_array($termid, $category)) {
                                                $condition = 'true';
                                            }
                                        }
                                        if ($condition == 'false') {
                                            if (!in_array($product, $strtodisplay)) {
                                                rs_empty_cart();
                                            }
                                        }
                                    } else {
                                        if (!in_array($product, $strtodisplay)) {
                                            rs_empty_cart();
                                        }
                                    }
                                }

                                if ($condition == 'false') {
                                    if (!in_array($varorproid, $strtodisplay)) {
                                        rs_empty_cart();
                                    }
                                }
                            } else {
                                foreach ($woocommerce->cart->cart_contents as $key => $values) {
                                    $product = $values['variation_id'] > '0' ? $values['variation_id'] : $values['product_id'];

                                    $productcategorys = get_the_terms($product, 'product_cat');
                                    if ($productcategorys != false) {
                                        $condition = 'false';
                                        foreach ($productcategorys as $productcategory) {
                                            $termid = $productcategory->term_id;

                                            if (in_array($termid, $category)) {
                                                $condition = 'true';
                                            }
                                        }
                                        if ($condition == 'false') {
                                            if (!in_array($product, $strtodisplay)) {
                                                rs_empty_cart();
                                            }
                                        }
                                    } else {
                                        if (!in_array($product, $strtodisplay)) {
                                            rs_empty_cart();
                                        }
                                    }
                                }
                            }
                        } else {
                            rs_empty_cart();
                        }
                    } else {
                        foreach ($woocommerce->cart->cart_contents as $key => $values) {
                            $product = $values['variation_id'] > '0' ? $values['variation_id'] : $values['product_id'];
                            if (!in_array($product, $strtodisplay)) {
                                rs_empty_cart();
                            }
                        }
                    }
                } else {
                    if ($enablecatagorypurchase == 'yes') {
                        if (!empty($category)) {
                            $productcategorys = get_the_terms($varorproid, 'product_cat');
                            if ($productcategorys != false) {
                                $getcount = count($productcategorys);
                                $category_check = 'false';
                                foreach ($productcategorys as $productcategory) {
                                    $termid = $productcategory->term_id;
                                    if (in_array($termid, $category)) {
                                        $category_check = 'true';
                                        foreach ($woocommerce->cart->cart_contents as $key => $values) {
                                            $product = $values['variation_id'] > '0' ? $values['variation_id'] : $values['product_id'];
                                            $productcategorys = get_the_terms($product, 'product_cat');
                                            if ($productcategorys != false) {
                                                $condition = 'false';
                                                foreach ($productcategorys as $productcategory) {
                                                    $termid = $productcategory->term_id;
                                                    if (in_array($termid, $category)) {
                                                        $condition = 'true';
                                                    }
                                                }
                                                if ($condition == 'false') {
                                                    if (!in_array($product, $strtodisplay)) {
                                                        rs_empty_cart();
                                                    }
                                                }
                                            }
                                        }
                                        $passed = true;
                                    }
                                }

                                if ($category_check == 'false') {
                                    foreach ($woocommerce->cart->cart_contents as $key => $values) {
                                        $product = $values['variation_id'] > '0' ? $values['variation_id'] : $values['product_id'];
                                        $productcategorys = get_the_terms($product, 'product_cat');
                                        if ($productcategorys != false) {
                                            foreach ($productcategorys as $productcategory) {
                                                $termid = $productcategory->term_id;
                                                if (in_array($termid, $category)) {
                                                    $productnametodisplay = get_the_title($product);
                                                    $msgtoreplace = str_replace($current_strtofind, $productnametodisplay, $getstrtodisplay);
                                                    wc_add_notice(__($msgtoreplace), 'error');
                                                    rs_empty_cart();
                                                }
                                            }
                                        } else {

                                            if (in_array($product, $strtodisplay)) {
                                                rs_empty_cart();
                                            }
                                        }
                                    }
                                }
                            } else {

                                foreach ($woocommerce->cart->cart_contents as $key => $values) {
                                    $product = $values['variation_id'] > '0' ? $values['variation_id'] : $values['product_id'];
                                    $productcategorys = get_the_terms($product, 'product_cat');

                                    if ($productcategorys != false) {
                                        foreach ($productcategorys as $productcategory) {
                                            $termid = $productcategory->term_id;
                                            if (in_array($termid, $category)) {
                                                $productnametodisplay = get_the_title($product);
                                                $msgtoreplace = str_replace($current_strtofind, $productnametodisplay, $getstrtodisplay);
                                                wc_add_notice(__($msgtoreplace), 'error');
                                                rs_empty_cart();
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            foreach ($woocommerce->cart->cart_contents as $key => $values) {
                                $product = $values['variation_id'] > '0' ? $values['variation_id'] : $values['product_id'];
                                $productnametodisplay = get_the_title($product);
                                $msgtoreplace = str_replace($current_strtofind, $productnametodisplay, $getstrtodisplay);
                                wc_add_notice(__($msgtoreplace), 'error');
                            }
                            rs_empty_cart();
                        }
                    } else {
                        foreach ($woocommerce->cart->cart_contents as $key => $values) {
                            $product = $values['variation_id'] > '0' ? $values['variation_id'] : $values['product_id'];
                            if (in_array($product, $strtodisplay)) {
                                $productnametodisplay = get_the_title($product);
                                $msgtoreplace = str_replace($current_strtofind, $productnametodisplay, $getstrtodisplay);
                                wc_add_notice(__($msgtoreplace), 'error');
                                rs_empty_cart();
                            }
                        }

                        $passed = true;
                    }
                }
            } else {
                if ($enablecatagorypurchase == 'yes') {
                    if (!empty($category)) {
                        $productcategorys = get_the_terms($varorproid, 'product_cat');
                        if ($productcategorys != false) {
                            $getcount = count($productcategorys);
                            $check_cat = 'false';
                            foreach ($productcategorys as $productcategory) {
                                $termid = $productcategory->term_id;
                                if (in_array($termid, $category)) {
                                    $check_cat = 'true';
                                    foreach ($woocommerce->cart->cart_contents as $key => $values) {
                                        $product = $values['variation_id'] > '0' ? $values['variation_id'] : $values['product_id'];
                                        $productcategorys = get_the_terms($product, 'product_cat');
                                        if ($productcategorys != false) {
                                            $condition = 'false';
                                            foreach ($productcategorys as $productcategory) {
                                                $termid = $productcategory->term_id;
                                                if (in_array($termid, $category)) {
                                                    $condition = 'true';
                                                }
                                            }
                                            if ($condition == 'false') {
                                                rs_empty_cart();
                                            }
                                        } else {
                                            rs_empty_cart();
                                        }
                                    }
                                    $passed = true;
                                }
                            }
                            if ($check_cat == 'false') {
                                foreach ($woocommerce->cart->cart_contents as $key => $values) {
                                    $product = $values['variation_id'] > '0' ? $values['variation_id'] : $values['product_id'];
                                    $productcategorys = get_the_terms($product, 'product_cat');
                                    if ($productcategorys != false) {
                                        foreach ($productcategorys as $productcategory) {
                                            $termid = $productcategory->term_id;
                                            if (in_array($termid, $category)) {
                                                $productnametodisplay = get_the_title($product);
                                                $msgtoreplace = str_replace($current_strtofind, $productnametodisplay, $getstrtodisplay);
                                                wc_add_notice(__($msgtoreplace), 'error');
                                                rs_empty_cart();
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            foreach ($woocommerce->cart->cart_contents as $key => $values) {
                                $product = $values['variation_id'] > '0' ? $values['variation_id'] : $values['product_id'];
                                $productcategorys = get_the_terms($product, 'product_cat');
                                if ($productcategorys != false) {
                                    foreach ($productcategorys as $productcategory) {
                                        $termid = $productcategory->term_id;
                                        if (in_array($termid, $category)) {
                                            $productnametodisplay = get_the_title($product);
                                            $msgtoreplace = str_replace($current_strtofind, $productnametodisplay, $getstrtodisplay);
                                            wc_add_notice(__($msgtoreplace), 'error');
                                            rs_empty_cart();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $passed;
    }

    function add_your_gateway_class($methods) {
        if (is_user_logged_in()) {
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
                $methods[] = 'WC_Reward_Gateway';
            }
        }
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'add_your_gateway_class');

    function rs_force_auto_r_manual_adaptive_payment($description, $gateway_id) {
        $reward_points_gateway = new WC_Reward_Gateway();

        if ($reward_points_gateway->get_option('rs_subscription_based_payment_option') == 'no' && $gateway_id == 'reward_gateway') {
            if (is_checkout_pay_page() && isset($_GET['key'])) {
                $order_id = wc_get_order_id_by_order_key($_GET['key']);

                if (function_exists('sumo_is_order_contains_subscriptions') && sumo_is_order_contains_subscriptions($order_id)) {
                    return $description . rs_display_adaptive_payment_selection_checkbox();
                }
            } else if (function_exists('sumo_is_cart_contains_subscription_items') && function_exists('sumo_is_order_subscription') &&
                    is_checkout() && (sumo_is_cart_contains_subscription_items() || sumo_is_order_subscription())) {
                return $description . rs_display_adaptive_payment_selection_checkbox();
            }
        }
        return $description;
    }

    function rs_display_adaptive_payment_selection_checkbox() {
        ob_start();
        ?>
        <div class = rs_reward_points_payment_selection >
            <br><br>
            <input type= checkbox id = rs_reward_points_payment_selection name = rs_reward_points_payment_selection value = "1" /><?php echo __('Enable Automatic Preapproval Payments', 'woocommerce') ?>
        </div>
        <?php
        return ob_get_clean();
    }

    function sumosubscription_is_preapproval_status_valid($subscription_post_id, $parent_order_id) {
        $payment_method = get_post_meta($parent_order_id, 'sumo_order_payment_method', true);
        $renewal_order_id = get_post_meta($subscription_post_id, 'sumo_get_renewal_id', true);
        $preapproval_status = false;

        if ($payment_method == "reward_gateway") {
            $order = new WC_Order($renewal_order_id);
            $redeemedpoints = gateway_points($renewal_order_id);
            $date = '999999999999';
            $getmaxoption = get_option('rs_max_redeem_discount_for_sumo_reward_points');
            $getuserid = rs_get_order_obj($order);
            $getuserid = $getuserid['order_userid'];
            $getmyrewardpoints = RSPointExpiry::get_sum_of_total_earned_points($getuserid);
            $equredeemamt = RSPointExpiry::redeeming_conversion_settings($redeemedpoints);
            if ($redeemedpoints < $getmyrewardpoints) {
                if ($getmaxoption != '') {
                    if ($order->get_total() > $getmaxoption) {
                        $preapproval_status = true;
                    }
                } else {
                    $preapproval_status = true;
                }
            }
        }

        return $preapproval_status;
    }

    function sumosubscription_get_preapproval_status($subscription_post_id, $parent_order_id) {
        if (sumosubscription_is_preapproval_status_valid($subscription_post_id, $parent_order_id)) {
            $preapproval_status = 'valid';
            update_post_meta($subscription_post_id, 'sumo_subscription_preapproval_status', $preapproval_status);
        }
    }

    function sumosubscription_preapproved_recurring_payment_transaction($subscription_post_id, $parent_order_id) {
        $renewal_order_id = get_post_meta($subscription_post_id, 'sumo_get_renewal_id', true);
        $order = new WC_Order($renewal_order_id);
        $redeemedpoints = gateway_points($renewal_order_id);
        $date = '999999999999';
        $getuserid = rs_get_order_obj($order);
        $getuserid = $getuserid['order_userid'];
        $couponcodeuserid = get_userdata($getuserid);
        $couponcodeuserlogin = is_object($couponcodeuserid) ? $couponcodeuserid->user_login : 'Guest';
        $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
        $current_conversion = wc_format_decimal(get_option('rs_redeem_point'));
        $point_amount = wc_format_decimal(get_option('rs_redeem_point_value'));
        $getmyrewardpoints = RSPointExpiry::get_sum_of_total_earned_points($getuserid);
        $equredeemamt = RSPointExpiry::redeeming_conversion_settings($redeemedpoints);

        if (sumosubscription_is_preapproval_status_valid($subscription_post_id, $parent_order_id)) {
            RSPointExpiry::perform_calculation_with_expiry($redeemedpoints, $getuserid);
            RSPointExpiry::record_the_points($getuserid, '0', $redeemedpoints, $date, 'RPFGWS', '0', $equredeemamt, $subscription_post_id, '0', '0', '0', '', $getmyrewardpoints, '', '0');
            update_post_meta($subscription_post_id, 'sumo_subscription_preapproved_payment_transaction_status', 'success');
        }
    }

    if (class_exists('SUMOSubscriptions')) {
        add_filter('woocommerce_gateway_description', 'rs_force_auto_r_manual_adaptive_payment', 10, 2);
        add_action('sumosubscriptions_process_preapproval_status', 'sumosubscription_get_preapproval_status', 10, 2);
        add_action('sumosubscriptions_process_preapproved_payment_transaction', 'sumosubscription_preapproved_recurring_payment_transaction', 10, 2);
    }
}
