<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('RSFunctionForCart')) {

    class RSFunctionForCart {

        public static function init() {

            add_action('woocommerce_cart_totals_before_order_total', array(__CLASS__, 'display_total_earned_points'));

            add_action('wp_head', array(__CLASS__, 'show_hide_coupon_code'), 1);

            if (get_option('rs_reward_point_troubleshoot_after_cart') == '1') {
                add_action('woocommerce_after_cart_table', array(__CLASS__, 'reward_system_add_redeem_message_after_cart_table'));
            } else {
                add_action('woocommerce_cart_coupon', array(__CLASS__, 'reward_system_add_redeem_message_after_cart_table'));
            }

            if (get_option('rs_reward_point_troubleshoot_before_cart') == '1') {
                add_action('woocommerce_before_cart', array(__CLASS__, 'get_reward_points_to_display_msg_in_cart_and_checkout'));
            } else {
                add_action('woocommerce_before_cart_table', array(__CLASS__, 'get_reward_points_to_display_msg_in_cart_and_checkout'));
            }
            add_action('woocommerce_before_checkout_form', array(__CLASS__, 'get_reward_points_to_display_msg_in_cart_and_checkout'));

            if (get_option('rs_reward_point_troubleshoot_before_cart') == '1') {
                add_action('woocommerce_before_cart', array(__CLASS__, 'display_msg_in_cart_page'));
            } else {
                add_action('woocommerce_before_cart_table', array(__CLASS__, 'display_msg_in_cart_page'));
            }
            add_action('woocommerce_before_checkout_form', array(__CLASS__, 'display_msg_in_checkout_page'));

            if (get_option('rs_reward_point_troubleshoot_before_cart') == '1') {
                add_action('woocommerce_before_cart', array(__CLASS__, 'display_msg_in_cart_page_for_balance_reward_points'));
            } else {
                add_action('woocommerce_before_cart_table', array(__CLASS__, 'display_msg_in_cart_page_for_balance_reward_points'));
            }
            add_action('woocommerce_before_checkout_form', array(__CLASS__, 'display_msg_in_checkout_page_for_balance_reward_points'));

            add_shortcode('redeempoints', array(__CLASS__, 'get_redeem_point_to_display_in_msg'));

            add_shortcode('rspoint', array(__CLASS__, 'get_each_product_price_in_cart'));

            add_shortcode('titleofproduct', array(__CLASS__, 'get_each_producttitle_in_cart'));

            add_shortcode('carteachvalue', array(__CLASS__, 'get_each_product_points_value_in_cart'));

            add_shortcode('redeemeduserpoints', array(__CLASS__, 'get_balance_redeem_points_to_display_in_msg'));

            add_action('woocommerce_before_cart', array(__CLASS__, 'display_redeem_min_max_points_buttons_on_cart_page'));

            add_shortcode('rsminimumpoints', array(__CLASS__, 'get_minimum_redeeming_points_value'));

            add_shortcode('rsmaximumpoints', array(__CLASS__, 'get_maximum_redeeming_points_value'));

            add_shortcode('rsequalpoints', array(__CLASS__, 'get_minimum_and_maximum_redeeming_points_value'));

            add_filter('woocommerce_cart_totals_coupon_label', array(__CLASS__, 'change_coupon_label'), 1, 2);

            add_filter('woocommerce_add_to_cart_validation', array(__CLASS__, 'sell_individually_functionality'), 10, 5);

            add_filter('vartable_add_to_cart_validation', array(__CLASS__, 'sell_individually_functionality'), 10, 5); //compatability with woo-variations-table plugin

            add_filter('woocommerce_cart_item_price', array(__CLASS__, 'display_points_price'), 10, 3);

            add_filter('woocommerce_cart_item_subtotal', array(__CLASS__, 'display_points_total'), 10, 3);

            add_action('woocommerce_cart_total', array(__CLASS__, 'total_points_display_in_cart'));

            add_action('woocommerce_add_to_cart', array(__CLASS__, 'set_point_price_for_products_in_session'), 1, 5);

            add_action('woocommerce_checkout_update_order_meta', array(__CLASS__, 'save_point_price_info_in_order'));

            add_action('woocommerce_checkout_update_order_meta', array(__CLASS__, 'remove_session'));

            add_action('woocommerce_removed_coupon', array(__CLASS__, 'unset_session'));

            add_action('wp_head', array(__CLASS__, 'rs_apply_coupon_automatically'), 10);

            add_action('woocommerce_checkout_update_order_meta', array(__CLASS__, 'save_points_info_in_order'), 10, 2);

            add_filter('woocommerce_is_purchasable', array(__CLASS__, 'is_purchasable_product'), 10, 2);

            add_filter('woocommerce_get_variation_price_html', array(__CLASS__, 'check_variation_points'), 10, 2);

            add_filter('woocommerce_show_variation_price', array(__CLASS__, 'change_variation_point_price_display'), 10, 3);

            add_filter('woocommerce_variable_free_price_html', array(__CLASS__, 'hide_free_product_msg'), 10, 3);

            add_filter('woocommerce_calculated_total', array(__CLASS__, 'alter_free_product_price'), 10, 2);

            add_filter('woocommerce_cart_subtotal', array(__CLASS__, 'display_cart_subtotal'), 10, 3);

            add_filter('woocommerce_get_price_html', array(__CLASS__, 'display_variation_price'), 10, 2);

            add_filter('woocommerce_checkout_coupon_message', array(__CLASS__, 'hide_coupon'), 1);

            add_action('woocommerce_before_add_to_cart_button', array(__CLASS__, 'point_price_booking_alter_prie'), 10);

            add_action('save_post', array(__CLASS__, 'save_meta_boxes'), 1, 2);

            add_filter('woocommerce_cart_item_removed_title', array(__CLASS__, 'update_cart_coupon'), 10, 2);

            add_filter('woocommerce_update_cart_action_cart_updated', array(__CLASS__, 'update_coupon_in_cart'), 10, 1);

            add_action('bbp_new_topic_post_extras', array(__CLASS__, 'points_for_bbpress'), 10, 1);

            add_action('bbp_new_reply_post_extras', array(__CLASS__, 'points_for_bbpress'), 10, 1);

            add_action('woocommerce_after_cart_totals', array(__CLASS__, 'hide_coupon_and_redeem'));

            add_action('woocommerce_after_checkout_form', array(__CLASS__, 'hide_redeem_field_checkout'), 10, 1);

            add_action('woocommerce_removed_coupon', array(__CLASS__, 'display_message_in_cart'), 10, 1);

            add_filter('woocommerce_add_message', array(__CLASS__, 'clear_notices'), 10, 1);

            if (get_option('rs_reward_point_troubleshoot_before_cart') == '1') {
                add_action('woocommerce_before_cart', array(__CLASS__, 'display_complete_message_cart_page'));
            } else {

                add_action('woocommerce_before_cart_table', array(__CLASS__, 'display_complete_message_cart_page'));
            }
        }

        public static function display_message_in_cart($coupon_code) {
            $coupon = new WC_Coupon($coupon_code);
            if (is_object($coupon) && $coupon->is_valid()) {
                update_option('couponn', $coupon_code);
                if (is_cart()) {
                    if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.6.14', '<=')) {
                        RSFunctionForCart::get_reward_points_to_display_msg_in_cart_and_checkout();
                        RSFunctionForCart::display_complete_message_cart_page();
                        RSFunctionForCheckout::your_current_points_cart_page();
                    }
                    RSFunctionForCart::display_msg_in_cart_page();
                    RSFunctionForCart::display_msg_in_cart_page_for_balance_reward_points();
                    RSFunctionForCart::display_redeem_points_buttons_on_cart_page();
                }
                if (is_checkout()) {
                    if (get_option('rs_enable_redeem_for_order') == 'yes') {
                        RSFunctionForCart::get_reward_points_to_display_msg_in_cart_and_checkout();
                        RSFunctionForCart::display_msg_in_cart_page();
                        RSFunctionForCart::display_complete_message_cart_page();
                        RSFunctionForCart::display_msg_in_cart_page_for_balance_reward_points();
                        RSFunctionForCart::display_redeem_points_buttons_on_cart_page();
                    }
                }
            }
        }

        public static function display_complete_message_cart_page() {
            global $totalrewardpointsnew;
            if (is_user_logged_in()) {
                $checkenableoption = RSFunctionForCart::check_the_applied_coupons();
                if (get_option('rs_show_hide_message_for_total_points') == '1') {
                    if ($checkenableoption == false) {
                        if (is_array($totalrewardpointsnew)) {
                            if (array_sum($totalrewardpointsnew) > 0) {
                                $totalrewardpoints = do_shortcode('[totalrewards]');
                                if ($totalrewardpoints > 0) {
                                    ?>
                                    <div class="woocommerce-info sumo_reward_points_complete_message">
                                        <?php
                                        echo do_shortcode(get_option('rs_message_total_price_in_cart'));
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

        public static function clear_notices($message) {
            $couponcode = get_option('couponn');
            global $woocommerce;
            $user_ID = get_current_user_id();
            $getinfousernickname = get_user_by('id', $user_ID);
            $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
            $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
            $auto = 'auto_redeem_' . strtolower("$couponcodeuserlogin");
            $woo_msg = __('Coupon has been removed.', 'woocommerce');
            if ($message == $woo_msg) {
                if ($usernickname == $couponcode || $auto == $couponcode) {
                    $message = __(get_option('rs_remove_redeem_points_message'), 'rewardsystem');
                }
            }
            return $message;
        }

        public static function points_for_bbpress($topic_id) {
            $topic_ids = '';
            $post = get_post($topic_id);
            $post_parent = $post->post_parent;
            $post_type = $post->post_type;
            $user_id = get_current_user_id();
            if (is_user_logged_in()) {
                if ($post_type == 'topic') {
                    $event_slug = 'RPCT';
                    $productlevelrewardpointss = get_option('rs_reward_points_for_creatic_topic');
                }
                if ($post_type == 'reply') {
                    $event_slug = 'RPRT';
                    $topic_ids = $post->post_parent;
                    $reply_id = $topic_id;
                    $productlevelrewardpointss = get_option('rs_reward_points_for_reply_topic');
                }
                $checktopic = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($user_id, 'userreplytopic' . $post_parent, true);
                if ($checktopic != '1') {
                    $enabletopic = get_option('rs_enable_reward_points_for_create_topic');
                    $enablereply = get_option('rs_enable_reward_points_for_reply_topic');
                    if ($enabletopic == 'yes' || $enablereply == 'yes') {
                        if ($productlevelrewardpointss != '') {
                            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
                            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                            if ($enabledisablemaxpoints == 'yes') {
                                $new_obj->check_point_restriction($restrictuserpoints, $productlevelrewardpointss, $pointsredeemed = 0, $event_slug, $user_id, $nomineeid = '', $referrer_id = '', $productid = '', $variationid = '', $reasonindetail);
                            } else {
                                $equearnamt = RSPointExpiry::earning_conversion_settings($productlevelrewardpointss);
                                $valuestoinsert = array('pointstoinsert' => $productlevelrewardpointss, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $user_id, 'referred_id' => '', 'product_id' => '', 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $productlevelrewardpointss, 'totalredeempoints' => 0);
                                $new_obj->total_points_management($valuestoinsert);
                            }
                            if ($topic_ids != '') {
                                RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($user_id, 'userreplytopic' . $topic_ids, '1');
                            }
                        }
                    }
                }
            }
        }

        public static function hide_redeem_field_checkout($id) {
            global $woocommerce;
            $user_ID = get_current_user_id();
            $getinfousernickname = get_user_by('id', $user_ID);
            $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
            $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
            $array = $woocommerce->cart->get_applied_coupons();
            if (get_option('_rs_not_allow_earn_points_if_sumo_coupon') == 'yes') {
                if (is_array($woocommerce->cart->get_applied_coupons())) {
                    $getappliedcoupon = $woocommerce->cart->get_applied_coupons();
                    if (!empty($getappliedcoupon)) {
                        foreach ($woocommerce->cart->get_applied_coupons() as $coupons) {
                            $coupon_id_array = new WC_Coupon($coupons);
                            $coupon_id = rs_get_coupon_obj($coupon_id_array);
                            $coupon_id = $coupon_id['coupon_id'];
                            $check_sumo_coupon = get_post_meta($coupon_id, 'sumo_coupon_check', true);
                            if ($check_sumo_coupon == 'yes') {
                                ?>
                                <script type="text/javascript">
                                    jQuery(document).ready(function () {
                                        jQuery("#mainsubmi").parent().hide();
                                    });
                                </script>
                                <?php
                            }
                        }
                    }
                }
            }
            if (in_array($usernickname, $array)) {
                ?>                            
                <style type="text/css">
                    .redeeemit {
                        display:none;
                    }
                </style>                 
                <?php
            } else {
                ?>                            
                <style type="text/css">
                    .redeeemit {
                        display:block;
                    }
                </style>                 
                <?php
            }
        }

        public static function hide_coupon_and_redeem() {
            global $woocommerce;
            $user_ID = get_current_user_id();
            $getinfousernickname = get_user_by('id', $user_ID);
            $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
            $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
            $autoredeem = 'auto_redeem_' . strtolower("$couponcodeuserlogin");
            $array = $woocommerce->cart->get_applied_coupons();
            if (get_option('rs_show_hide_redeem_field') == '5' || get_option('rs_show_hide_redeem_field') == '1' || get_option('rs_show_hide_redeem_field') == '2') {
                if (empty($array)) {
                    ?>     
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery("#mainsubmi").parent().show();
                        });
                    </script>

                    <?php
                }
            }
            if (in_array($usernickname, $array) || in_array($autoredeem, $array)) {
                if (get_option('rs_show_hide_redeem_field') != '1') {
                    echo RSFunctionForCheckout::rs_script_and_style_to_hide_coupon_field();
                }
            } else {
                $array = $woocommerce->cart->get_applied_coupons();
                if (!empty($array)) {
                    if (get_option('rs_show_hide_redeem_field') != '5') {
                        echo RSFunctionForCheckout::rs_script_and_style_to_hide_coupon_field();
                    }
                    if (get_option('rs_show_hide_redeem_field') != '1') {
                        echo RSFunctionForCheckout::rs_style_to_hide_redeem_field_in_checkout();
                        echo RSFunctionForCheckout::rs_script_to_hide_redeem_field_in_cart();
                    }
                }
            }

            if (get_option('rs_dont_allow_redeem_if_sumo_coupon') == 'yes') {
                $array = $woocommerce->cart->get_applied_coupons();
                if (is_array($woocommerce->cart->get_applied_coupons())) {
                    $getappliedcoupon = $woocommerce->cart->get_applied_coupons();
                    if (!empty($getappliedcoupon)) {
                        foreach ($woocommerce->cart->get_applied_coupons() as $coupons) {
                            $coupon_id_array = new WC_Coupon($coupons);
                            $coupon_id = rs_get_coupon_obj($coupon_id_array);
                            $coupon_id = $coupon_id['coupon_id'];
                            $getd = get_post_meta($coupon_id, 'sumo_coupon_check', true);
                            if ($getd == 'yes') {
                                ?>
                                <script type="text/javascript">
                                    jQuery(document).ready(function () {
                                        jQuery("#mainsubmi").parent().hide();
                                    });
                                </script>

                                <?php
                            }
                        }
                    }
                }
            }
        }

        public static function update_coupon_in_cart($coupon) {
            self::update_cart_coupon($coupon, $item = '');
            return $coupon;
        }

        public static function update_cart_coupon($product, $item) {
            global $woocommerce;
            WC()->cart->calculate_totals();
            foreach ($woocommerce->cart->applied_coupons as $code) {
                if (get_option('woocommerce_prices_include_tax') == 'yes') {
                    if (get_option('woocommerce_tax_display_cart') == 'incl') {
                        $get_cart_total_for_redeem = $woocommerce->cart->subtotal;
                    } else {
                        $get_cart_total_for_redeem = $woocommerce->cart->subtotal;
                    }
                } else {
                    if (get_option('woocommerce_tax_display_cart') == 'incl') {
                        $get_cart_total_for_redeem = $woocommerce->cart->subtotal_ex_tax;
                    } else {
                        $get_cart_total_for_redeem = $woocommerce->cart->subtotal_ex_tax;
                    }
                }
                $coupon = new WC_Coupon($code);
                $coupon_obj = rs_get_coupon_obj($coupon);
                $couponamount = $coupon_obj['coupon_amount'];
                $coupon_code = $coupon_obj['coupon_code'];
                $coupon_id = $coupon_obj['coupon_id'];
                $user_ID = get_current_user_id();
                $getinfousernickname = get_user_by('id', $user_ID);
                $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                if ($coupon_code == 'sumo_' . strtolower($couponcodeuserlogin)) {
                    $getmaxruleoption = get_option('rs_max_redeem_discount');
                    $getpercentmaxoption = get_option('rs_percent_max_redeem_discount');
                    if (get_option('rs_apply_redeem_basedon_cart_or_product_total') == '1') {
                        if ($getmaxruleoption == '2') {
                            if ($getpercentmaxoption != '') {
                                $percentageproduct = $getpercentmaxoption / 100;
                                $getpricepercent = $percentageproduct * $get_cart_total_for_redeem;
                                if ($couponamount > $getpricepercent) {
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($coupon_id, 'coupon_amount', $getpricepercent);
                                    if (get_option('rs_redeem_field_type_option') == '2') {
                                        $limitation_percentage_for_redeeming_for_button = get_option('rs_percentage_cart_total_redeem');
                                        if ($limitation_percentage_for_redeeming_for_button != '') {
                                            $reddem_value_in_amount_percent = $limitation_percentage_for_redeeming_for_button / 100;
                                            $reddem_points_for_total = $reddem_value_in_amount_percent * $get_cart_total_for_redeem;
                                            if ($getpricepercent > $reddem_points_for_total) {
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($coupon_id, 'coupon_amount', $reddem_points_for_total);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $getsumofselectedproduct = RSFunctionToApplyCoupon::get_sum_of_selected_products('sumo', '', '');
                        if ($getmaxruleoption == '2') {
                            if ($getpercentmaxoption != '') {
                                $percentageproduct = $getpercentmaxoption / 100;
                                $getpricepercent = $percentageproduct * $getsumofselectedproduct;
                                if ($couponamount > $getpricepercent) {
                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($coupon_id, 'coupon_amount', $getpricepercent);
                                    if (get_option('rs_redeem_field_type_option') == '2') {
                                        $limitation_percentage_for_redeeming_for_button = get_option('rs_percentage_cart_total_redeem');
                                        if ($limitation_percentage_for_redeeming_for_button != '') {
                                            $reddem_value_in_amount_percent = $limitation_percentage_for_redeeming_for_button / 100;
                                            $reddem_points_for_total = $reddem_value_in_amount_percent * $getsumofselectedproduct;
                                            if ($getpricepercent > $reddem_points_for_total) {
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($coupon_id, 'coupon_amount', $reddem_points_for_total);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $product;
        }

        public static function save_meta_boxes($post_id, $post) {
            if ('product' == $post->post_type) {
                if (get_option('rs_reward_for_enable_product_create') == 'yes') {
                    $currentregistrationpoints = get_option('rs_reward_Product_create');
                    $get_option = get_post_meta($post->ID, 'productcretion_points', true);
                    $date = rs_function_to_get_expiry_date_in_unixtimestamp();
                    if ($get_option != '1') {
                        $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
                        $user_id = $post->post_author;
                        $equearnamt = RSPointExpiry::earning_conversion_settings($currentregistrationpoints);
                        $valuestoinsert = array('pointstoinsert' => $currentregistrationpoints, 'pointsredeemed' => 0, 'event_slug' => 'RPCPRO', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $user_id, 'referred_id' => '', 'product_id' => $productid, 'variation_id' => '', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $currentregistrationpoints, 'totalredeempoints' => 0);
                        $new_obj->total_points_management($valuestoinsert);
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post->ID, 'productcretion_points', '1');
                    }
                }
            }
        }

        public static function point_price_booking_alter_prie() {
            if (class_exists('WC_Bookings')) {
                ?>
                <div class="wc-bookings-booking-cost1"></div> 

                <?php
            }
        }

        public static function hide_coupon($message) {
            $type = array();

            global $woocommerce;
            foreach ($woocommerce->cart->cart_contents as $item) {
                $product_id = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
                $type[] = check_display_price_type($product_id);
                $enable = calculate_point_price_for_products($product_id);
                if ($enable[$product_id] != '') {
                    $cart_object[] = $enable[$product_id];
                }
            }

            if (in_array(2, $type)) {
                if (get_option('rs_show_hide_message_errmsg_for_point_price_coupon') == '1') {
                    $message = get_option('rs_errmsg_for_redeem_in_point_price_prt');
                } else {
                    return "<span class='displaymessage' ></span>";
                }
                return $message;
            }
            if (!empty($cart_object)) {
                if (get_option('rs_show_hide_message_errmsg_for_point_price_coupon') == '1') {
                    $message = get_option('rs_errmsg_for_redeem_in_point_price_prt');
                } else {
                    return "<span class='displaymessage' ></span>";
                }

                return $message;
            } else {
                return $message;
            }
        }

        public static function display_variation_price($cart_object, $compond) {
            if (is_product() || is_shop()) {
                $id = rs_get_id($compond);
                $gettheproducts = rs_get_product_object($id);
                if (is_object($gettheproducts) && rs_check_variable_product_type($gettheproducts)) {
                    $variation_idss = RSFunctionforSimpleProduct::get_variation_id($id);
                    foreach ($variation_idss as $eachvariation) {
                        $productid = $eachvariation;
                        if (check_display_price_type($productid) == '2') {
                            if (get_option('rs_enable_disable_point_priceing') == '1') {
                                $enable = calculate_point_price_for_products($productid);
                                if ($enable[$productid] != '') {
                                    $cart_object = '';
                                }
                            }
                        }
                    }
                }
                return $cart_object;
            } else {
                return $cart_object;
            }
        }

        public static function display_cart_subtotal($cart_object, $compond, $product) {
            if (get_option('rs_enable_disable_point_priceing') == '1') {
                $array = array();
                foreach ($product->cart_contents as $key => $value) {
                    $productid = $value['variation_id'] != '' ? $value['variation_id'] : $value['product_id'];

                    if (check_display_price_type($productid) == '2') {
                        if (get_option('rs_enable_disable_point_priceing') == '1') {
                            $label = get_option('rs_label_for_point_value');
                            $replace = str_replace("/", "", $label);
                            $enable = calculate_point_price_for_products($productid);
                            if ($enable[$productid] != '') {
                                $cart_object = $enable[$productid] * $value['quantity'];
                                $array[] = $cart_object;
                            }
                        }
                    } else {

                        return $cart_object;
                    }
                }
                $amount = array_sum($array);
                if ($amount != '' || $amount != '0') {
                    $labelposition = get_option('rs_sufix_prefix_point_price_label');
                    if ($labelposition == '1') {
                        $totalamount = $replace . $amount;
                    } else {
                        $totalamount = $amount . $replace;
                    }

                    return $totalamount;
                } else {
                    return $cart_object;
                }
            } else {
                return $cart_object;
            }
        }

        public static function alter_free_product_price($cart_object, $product) {
            if (get_option('rs_enable_disable_point_priceing') == '1') {
                foreach ($product->cart_contents as $key => $value) {
                    $productid = $value['variation_id'] != '' ? $value['variation_id'] : $value['product_id'];
                    if (check_display_price_type($productid) == '2') {
                        if (get_option('rs_enable_disable_point_priceing') == '1') {
                            $enable = calculate_point_price_for_products($productid);
                            if ($enable[$productid] != '') {
                                $cart_object = '1';
                            }
                        }
                    }
                }
                return $cart_object;
            } else {
                return $cart_object;
            }
        }

        public static function is_purchasable_product($purchaseable, $product) {
            $id = rs_get_id($product);
            if (get_option('rs_enable_disable_point_priceing') == '1') {
                if (check_display_price_type($id) == '2') {
                    $enable = calculate_point_price_for_products($id);
                    if ($enable[$id] != '') {
                        $purchaseable = true;
                        return $purchaseable;
                    } else {
                        return $purchaseable;
                    }
                } else {
                    return $purchaseable;
                }
            } else {
                return $purchaseable;
            }
        }

        public static function check_variation_points($product, $id) {
            if (get_option('rs_enable_disable_point_priceing') == '1') {
                $var_id = rs_get_id($id);
                if (check_display_price_type($var_id) == '2') {
                    $label = get_option('rs_label_for_point_value');
                    $replace = str_replace("/", "", $label);
                    $enable = calculate_point_price_for_products($var_id);
                    if ($enable[$var_id] != '') {
                        $product = $enable[$var_id];
                        $labelposition = get_option('rs_sufix_prefix_point_price_label');
                        if ($labelposition == '1') {
                            $totalamount = $replace . $product;
                        } else {
                            $totalamount = $product . $replace;
                        }
                        return $totalamount;
                    } else {
                        return $product;
                    }
                } else {
                    return $product;
                }
            } else {
                return $product;
            }
        }

        public static function change_variation_point_price_display($product, $obj, $id) {
            if (get_option('rs_enable_disable_point_priceing') == '1') {
                $var_id = rs_get_id($id);
                if (check_display_price_type($var_id) == '2') {
                    $enable = calculate_point_price_for_products($var_id);
                    if ($enable[$var_id] != '') {
                        $product = true;
                        return $product;
                    } else {
                        return $product;
                    }
                } else {
                    return $product;
                }
            } else {
                return $product;
            }
        }

        public static function hide_free_product_msg($product, $obj) {
            if (get_option('rs_enable_disable_point_priceing') == '1') {
                $product = '';
                return $product;
            } else {
                return $product;
            }
        }

        public static function save_points_info_in_order($order_id, $orderuserid) {
            if (get_option('rs_enable_disable_reward_point_based_coupon_amount') == 'yes') {
                $points_info = self::moified_points_for_products_in_cart();
                update_post_meta($order_id, 'points_for_current_order', $points_info);
                $points_for_current_order_in_value = array_sum($points_info);
                update_post_meta($order_id, 'rs_points_for_current_order_as_value', $points_for_current_order_in_value);
                update_post_meta($order_id, 'frontendorder', 1);
            } else {
                $points_info = self::original_points_for_product_in_cart();
                update_post_meta($order_id, 'points_for_current_order', $points_info);
                $points_for_current_order_in_value = array_sum($points_info);
                update_post_meta($order_id, 'rs_points_for_current_order_as_value', $points_for_current_order_in_value);
                update_post_meta($order_id, 'frontendorder', 1);
            }
        }

        public static function moified_points_for_products_in_cart() {
            global $woocommerce;
            $modified_points_updated = array();
            $original_points_array = self::original_points_for_product_in_cart();
            if (is_array($original_points_array) && !empty($original_points_array)) {
                foreach ($original_points_array as $product_id => $points) {
                    $modified_points = self::coupon_points_conversion($product_id, $points);
                    if ($modified_points != 0) {
                        $modified_points_updated[$product_id] = $modified_points;
                    }
                }
            }
            return $modified_points_updated;
        }

        public static function coupon_included_products($product_ids, $coupon_code) {
            global $woocommerce;
            $coupon_product_ids = array();
            foreach ($woocommerce->cart->cart_contents as $cart_details) {
                $product_id = $cart_details['variation_id'] != '' ? $cart_details['variation_id'] : $cart_details['product_id'];
                if (in_array($product_id, $product_ids)) {
                    $coupon_product_ids[] = $cart_details['line_subtotal'];
                }
            }
            $coupon_product_ids = array_sum($coupon_product_ids);

            return $coupon_product_ids;
        }

        public static function coupon_validator($product_id, $points) {
            global $woocommerce;
            $selected_products = array();
            $discount_coupon = $woocommerce->cart->coupon_discount_amounts;
            $newdiscount_amounts = $woocommerce->cart->coupon_discount_amounts;
            if ($newdiscount_amounts) {
                $discountss = array_sum(array_values($newdiscount_amounts));
                $c_amount = $discountss;
            }

            foreach ($woocommerce->cart->applied_coupons as $code) {
                $coupon = new WC_Coupon($code);
                $coupon_obj = rs_get_coupon_obj($coupon);
                $selectedproduct = $coupon_obj['product_ids'];
                $coupon_code = $coupon_obj['coupon_code'];
                $coupon_amount = $coupon_obj['coupon_amount'];
                $selectedcategories = $coupon_obj['product_categories'];
                $discount_type = $coupon_obj['discount_type'];
                $user_ID = get_current_user_id();
                $getinfousernickname = get_user_by('id', $user_ID);
                $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                if ($coupon_code == 'sumo_' . strtolower($couponcodeuserlogin)) {
                    if ($discount_type == 'fixed_cart') {
                        $selectedproduct = $coupon_obj['product_ids'];
                        if (!empty($selectedproduct)) {
                            if (in_array($product_id, $selectedproduct)) {
                                $coupon_product_ids["$code"][] = $product_id;
                                $count_of_products = 1;
                                $selected_products["$code"][$product_id] = $count_of_products > 1 ? $discount_coupon["$code"] / $count_of_products : $discount_coupon["$code"];
                            }
                        } else {
                            $coupon_product_ids["$code"][] = $product_id;
                            $count_of_products = 1;
                            $selected_products["$code"][$product_id] = $c_amount;
                        }
                    }
                } else {
                    if ($discount_type == 'fixed_cart') {
                        if (!empty($selectedproduct)) {
                            if (in_array($product_id, $selectedproduct)) {
                                $coupon_product_ids["$code"][] = $product_id;
                                $count_of_products = 1;
                                $selected_products["$code"][$product_id] = $count_of_products > 1 ? $discount_coupon["$code"] / $count_of_products : $discount_coupon["$code"];
                            }
                        } else {
                            $coupon_product_ids["$code"][] = $product_id;
                            $count_of_products = 1;
                            $selected_products["$code"][$product_id] = $c_amount;
                        }
                    } else if ($discount_type == 'percent_product') {
                        if (!empty($selectedproduct)) {
                            if (in_array($product_id, $selectedproduct)) {
                                $coupon_product_ids["$code"][] = $product_id;
                                $count_of_products = 1;
                                $selected_products["$code"][$product_id] = $count_of_products > 1 ? $discount_coupon["$code"] / $count_of_products : $discount_coupon["$code"];
                            }
                        } else {
                            $coupon_product_ids["$code"][] = $product_id;
                            $count_of_products = 1;

                            $selected_products["$code"][$product_id] = $c_amount;
                        }
                    } else if ($discount_type == 'fixed_product') {
                        if (!empty($selectedproduct)) {
                            if (in_array($product_id, $selectedproduct)) {
                                $coupon_product_ids["$code"][] = $product_id;
                                $count_of_products = 1;
                                $selected_products["$code"][$product_id] = $count_of_products > 1 ? $discount_coupon["$code"] / $count_of_products : $discount_coupon["$code"];
                            }
                        } else {
                            $coupon_product_ids["$code"][] = $product_id;
                            $count_of_products = 1;

                            $selected_products["$code"][$product_id] = $c_amount;
                        }
                    } else if ($discount_type = 'percent') {
                        if (!empty($selectedproduct)) {
                            if (in_array($product_id, $selectedproduct)) {
                                $coupon_product_ids["$code"][] = $product_id;
                                $count_of_products = 1;

                                $selected_products["$code"][$product_id] = $count_of_products > 1 ? $discount_coupon["$code"] / $count_of_products : $discount_coupon["$code"];
                            }
                        } else {
                            $coupon_product_ids["$code"][] = $product_id;
                            $count_of_products = 1;

                            $selected_products["$code"][$product_id] = $c_amount;
                        }
                    }
                }
            }

            return $selected_products;
        }

        public static function get_product_price_in_cart() {
            global $woocommerce;
            $price = array();
            foreach ($woocommerce->cart->cart_contents as $key => $value) {
                $checklevel = 'no';
                $points = check_level_of_enable_reward_point($value['product_id'], $value['variation_id'], $value, $checklevel, $referred_user = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
                update_post_meta($value['product_id'], 'linetotal1', $value['line_subtotal']);
                $totalrewardpoints = RSMemberFunction::user_role_based_reward_points(get_current_user_id(), $points);
                $totalrewardpointsnew[] = $totalrewardpoints;
                if ($totalrewardpointsnew > 0) {
                    $price[] = $value['line_subtotal'];
                }
            }
            $totalprice = array_sum($price);
            return $totalprice;
        }

        public static function coupon_points_conversion($product_id, $points) {
            global $woocommerce;
            $coupon_amounts = self::coupon_validator($product_id, $points);
            $newpoints = $points;
            $conversions = array();
            if (!empty($coupon_amounts) && is_array($coupon_amounts)) {
                foreach ($coupon_amounts as $key1 => $value) {
                    
                }
                if ($newpoints > 0) {
                    $c_amount1 = $value[$product_id];
                    $newdiscount_amounts = $woocommerce->cart->coupon_discount_amounts;
                    if ($newdiscount_amounts) {
                        $discountss = array_sum(array_values($newdiscount_amounts));
                        $c_amount = $discountss;
                    }
                    foreach ($woocommerce->cart->applied_coupons as $key1) {
                        $coupon = new WC_Coupon($key1);
                        $coupon_obj = rs_get_coupon_obj($coupon);
                        $selectedproduct = $coupon_obj['product_ids'];
                        $rp = self::coupon_included_products($selectedproduct, $coupon_obj['coupon_code']);
                        if (!empty($selectedproduct)) {
                            $conversion = $c_amount1 / $rp;
                        } else {
                            $conversion = $c_amount / self::get_product_price_in_cart();
                        }
                    }
                    $newpoints1 = $newpoints;
                    $conversion = $conversion * $newpoints1;
                    if ($newpoints1 > $conversion) {
                        $conversions[] = $newpoints1 - $conversion;
                    }
                    $newpoints = $newpoints1 - $conversion;
                }
                return end($conversions);
            }
            return $newpoints;
        }

        public static function coupon($key, $product_id) {
            global $woocommerce;
            $discount_coupon = $woocommerce->cart->applied_coupons;
            $coupon = new WC_Coupon($key);
            $coupon_obj = rs_get_coupon_obj($coupon);
            if (count($discount_coupon) > 1) {
                $couponcode = get_post_meta($product_id, 'couponcode');
                if ($couponcode != $coupon_obj['coupon_code']) {
                    $linetotal = get_post_meta($product_id, 'linetotal');
                    $vd = $linetotal[0];
                }
            } else {
                $vd = self::get_product_price_in_cart();
                $vd1 = self::get_product_price_in_cart() - $coupon_obj['coupon_amount'];
                update_post_meta($product_id, 'linetotal', $vd1);
                update_post_meta($product_id, 'couponcode', $coupon_obj['coupon_code']);
            }

            return $vd;
        }

        public static function get_reward_points_to_display_msg_in_cart_and_checkout() {
            global $woocommerce;
            global $messageglobal;
            global $totalrewardpoints;
            global $checkproduct;
            global $value;
            global $totalrewardpointsnew;
            ?>
            <style>
                .cart_total_minimum:before{
                    font-family:WooCommerce;content:"\e028";display:inline-block;position:absolute;top:1em;left:1.5em;color:#1e85be
                }
                .cart_total_minimum{
                    font-size: 10pt;font-family:WooCommerce;padding:1em 2em 1em 3.5em!important;margin:0 0 2em!important;position:relative;background-color:#f7f6f7;color:#515151;border-top:3px solid #a46497;list-style:none!important;width:auto;word-wrap:break-word;border-top-color:#1e85be
                }
            </style>
            <?php
            $minimum_cart_total = get_option('rs_minimum_cart_total_for_earning');
            $maximum_cart_total = get_option('rs_maximum_cart_total_for_earning');
            $cart_total = $woocommerce->cart->subtotal;
            $error_message = get_option('rs_min_cart_total_for_earning_error_message');
            $replace = '[carttotal]';
            $error_message = str_replace($replace, $minimum_cart_total, $error_message);

            $error_message_max = get_option('rs_max_cart_total_for_earning_error_message');
            $replace = '[carttotal]';
            $error_message_max = str_replace($replace, $maximum_cart_total, $error_message_max);


            if (get_option('rs_enable_disable_reward_point_based_coupon_amount') == 'yes') {
                $points_info = self::moified_points_for_products_in_cart();
                if (!empty($points_info)) {
                    if ($minimum_cart_total != '' && $maximum_cart_total != '') {
                        if ($cart_total >= $minimum_cart_total && $cart_total <= $maximum_cart_total) {
                            $totalrewardpointsnew = $points_info;
                        } elseif ($cart_total <= $minimum_cart_total) {
                            $totalrewardpointsnew = '';
                            if ($cart_total != '') {
                                if (get_option('rs_show_hide_maximum_cart_total_earn_error_message') == '1') {
                                    ?>
                                    <div class="woocommerce-error" >  <?php echo $error_message; ?>  </div>
                                    <?php
                                }
                            }
                        } elseif ($cart_total >= $maximum_cart_total) {
                            $totalrewardpointsnew = '';
                            if ($cart_total != '') {
                                if (get_option('rs_show_hide_maximum_cart_total_earn_error_message') == '1') {
                                    ?>
                                    <div class="woocommerce-error" >  <?php echo $error_message_max; ?>  </div>
                                    <?php
                                }
                            }
                        }
                    } else if ($minimum_cart_total != '' && $maximum_cart_total == '') {
                        if ($cart_total >= $minimum_cart_total) {
                            $totalrewardpointsnew = $points_info;
                        } else {
                            $totalrewardpointsnew = '';
                            if ($cart_total != '') {
                                if (get_option('rs_show_hide_minimum_cart_total_earn_error_message') == '1') {
                                    ?>
                                    <div class="woocommerce-error" >  <?php echo $error_message; ?>  </div>
                                    <?php
                                }
                            }
                        }
                    } else if ($minimum_cart_total == '' && $maximum_cart_total != '') {
                        if ($cart_total <= $maximum_cart_total) {
                            $totalrewardpointsnew = $points_info;
                        } else {
                            $totalrewardpointsnew = '';
                            if ($cart_total != '') {
                                if (get_option('rs_show_hide_maximum_cart_total_earn_error_message') == '1') {
                                    ?>
                                    <div class="woocommerce-error" >  <?php echo $error_message_max; ?>  </div>
                                    <?php
                                }
                            }
                        }
                    } else if ($minimum_cart_total == '' && $maximum_cart_total == '') {
                        $totalrewardpointsnew = $points_info;
                    }
                }
            } else {
                $points_info = self::original_points_for_product_in_cart();
                if (!empty($points_info)) {
                    if ($minimum_cart_total != '' && $maximum_cart_total != '') {
                        if ($cart_total >= $minimum_cart_total && $cart_total <= $maximum_cart_total) {
                            $totalrewardpointsnew = $points_info;
                        } elseif ($cart_total <= $minimum_cart_total) {
                            $totalrewardpointsnew = '';
                            if ($cart_total != '') {
                                if (get_option('rs_show_hide_maximum_cart_total_earn_error_message') == '1') {
                                    ?>
                                    <div class="woocommerce-error" >  <?php echo $error_message; ?>  </div>
                                    <?php
                                }
                            }
                        } elseif ($cart_total >= $maximum_cart_total) {
                            $totalrewardpointsnew = '';
                            if ($cart_total != '') {
                                if (get_option('rs_show_hide_maximum_cart_total_earn_error_message') == '1') {
                                    ?>
                                    <div class="woocommerce-error" >  <?php echo $error_message_max; ?>  </div>
                                    <?php
                                }
                            }
                        }
                    } else if ($minimum_cart_total != '' && $maximum_cart_total == '') {
                        if ($cart_total >= $minimum_cart_total) {
                            $totalrewardpointsnew = $points_info;
                        } else {
                            $totalrewardpointsnew = '';
                            if ($cart_total != '') {
                                if (get_option('rs_show_hide_minimum_cart_total_earn_error_message') == '1') {
                                    ?>

                                    <div class="woocommerce-error" >  <?php echo $error_message; ?>  </div>
                                    <?php
                                }
                            }
                        }
                    } else if ($minimum_cart_total == '' && $maximum_cart_total != '') {
                        if ($cart_total <= $maximum_cart_total) {
                            $totalrewardpointsnew = $points_info;
                        } else {
                            $totalrewardpointsnew = '';
                            if ($cart_total != '') {
                                if (get_option('rs_show_hide_maximum_cart_total_earn_error_message') == '1') {
                                    ?>
                                    <div class="woocommerce-error" >  <?php echo $error_message_max; ?>  </div>
                                    <?php
                                }
                            }
                        }
                    } else if ($minimum_cart_total == '' && $maximum_cart_total == '') {
                        $totalrewardpointsnew = $points_info;
                    }
                }
            }
            if (!empty($points_info)) {
                foreach ($points_info as $product_id => $points) {
                    if ($points != 0) {
                        $checkproduct = rs_get_product_object($product_id);
                        $value = $product_id;
                        $totalrewardpoints = $points;
                        if (is_object($checkproduct) && (!$checkproduct->is_type('booking'))) {
                            if (is_cart()) {
                                $messageglobal[$product_id] = do_shortcode(get_option('rs_message_product_in_cart')) . "<br>";
                            } elseif (is_checkout()) {
                                $messageglobal[$product_id] = do_shortcode(get_option('rs_message_product_in_checkout')) . "<br>";
                            }
                        }
                    }
                }
            } else {
                $totalrewardpointsnew = '';
            }
            $totalrewardpoints = do_shortcode('[totalrewards]');
            WC()->session->set('rewardpoints', $totalrewardpoints);
        }

        public static function test_coupon() {
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function () {
                    jQuery('.displaymessage').parent().hide();
                });
            </script>
            <?php
            if (is_cart()) {
                if (isset($_GET['remove_coupon'])) {
                    wp_redirect(wc_get_page_permalink('cart'));
                }
            }
        }

        public static function remove_session() {
            WC()->session->set('auto_redeemcoupon', 'yes');
        }

        public static function unset_session() {
            global $woocommerce;
            WC()->session->set('auto_redeemcoupon', 'no');
        }

        public static function rs_apply_coupon_automatically() {
            global $woocommerce;
            if (is_checkout()) {
                if (isset($_GET['remove_coupon'])) {
                    WC()->session->set('auto_redeemcoupon', 'no');
                }
            }
            $autoredeemenable = get_option('rs_enable_disable_auto_redeem_points');
            if ($autoredeemenable == 'yes') {
                // if (is_cart()||is_checkout()) {
                global $woocommerce;
                $user_ID = get_current_user_id();
                if (is_user_logged_in()) {
                    $current_carttotal_amount = $woocommerce->cart->subtotal;

                    $maximum_cart_total_redeem = get_option('rs_maximum_cart_total_points');
                    $minimum_cart_total_redeem = get_option('rs_minimum_cart_total_points');
                    if ($minimum_cart_total_redeem != '' && $maximum_cart_total_redeem != '') {
                        if ($current_carttotal_amount >= $minimum_cart_total_redeem && $current_carttotal_amount <= $maximum_cart_total_redeem) {
                            if (is_cart()) {
                                self::auto_redeeming();
                            }
                            if (is_checkout()) {
                                if (get_option('rs_enable_disable_auto_redeem_checkout') == 'yes') {
                                    self::auto_redeeming();
                                }
                            }
                        }
                    } else if ($minimum_cart_total_redeem != '' && $maximum_cart_total_redeem == '') {
                        if ($current_carttotal_amount >= $minimum_cart_total_redeem) {
                            if (is_cart()) {
                                self::auto_redeeming();
                            }
                            if (is_checkout()) {
                                if (get_option('rs_enable_disable_auto_redeem_checkout') == 'yes') {
                                    self::auto_redeeming();
                                }
                            }
                        }
                    } else if ($minimum_cart_total_redeem == '' && $maximum_cart_total_redeem != '') {
                        if ($current_carttotal_amount <= $maximum_cart_total_redeem) {
                            if (is_cart()) {
                                self::auto_redeeming();
                            }
                            if (is_checkout()) {
                                if (get_option('rs_enable_disable_auto_redeem_checkout') == 'yes') {
                                    self::auto_redeeming();
                                }
                            }
                        }
                    } else if ($minimum_cart_total_redeem == '' && $maximum_cart_total_redeem == '') {
                        if (is_cart()) {
                            self::auto_redeeming();
                        }
                        if (is_checkout()) {
                            if (get_option('rs_enable_disable_auto_redeem_checkout') == 'yes') {
                                self::auto_redeeming();
                            }
                        }
                    }
                }
            }
        }

        public static function auto_redeeming() {
            $type = array();
            global $woocommerce;
            $user_ID = get_current_user_id();
            $getinfousernickname = get_user_by('id', $user_ID);
            $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
            $autoredeemenable = get_option('rs_enable_disable_auto_redeem_points');
            $checkfirstimeredeem = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($user_ID, 'rsfirsttime_redeemed');
            if ($woocommerce->cart->get_cart_contents_count() == 0) {
                WC()->session->set('auto_redeemcoupon', 'yes');
            }
            if ($woocommerce->cart->get_cart_contents_count() == 0) {
                foreach ($woocommerce->cart->applied_coupons as $code) {
                    $coupon = new WC_Coupon($code);
                    $coupon_obj = rs_get_coupon_obj($coupon);
                    $couponcode = $coupon_obj['coupon_code'];
                    $woocommerce->cart->remove_coupon($couponcode);
                }
            }
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
                    if ($woocommerce->cart->get_cart_contents_count() != 0) {
                        if ($autoredeemenable == 'yes') {
                            if (WC()->session->get('auto_redeemcoupon') != 'no') {
                                global $woocommerce;
                                $getuserid = get_current_user_id();
                                $user_current_points = RSPointExpiry::get_sum_of_total_earned_points($getuserid);
                                if ($user_current_points > 0) {
                                    if ($user_current_points >= get_option("rs_first_time_minimum_user_points")) {
                                        $redeem_conversion = get_option('rs_redeem_point');
                                        if (get_option('woocommerce_prices_include_tax') == 'yes') {
                                            if (get_option('woocommerce_tax_display_cart') == 'incl') {
                                                $get_cart_total_for_redeem = $woocommerce->cart->subtotal;
                                            } else {
                                                $get_cart_total_for_redeem = $woocommerce->cart->subtotal;
                                            }
                                        } else {
                                            if (get_option('woocommerce_tax_display_cart') == 'incl') {
                                                $get_cart_total_for_redeem = $woocommerce->cart->subtotal_ex_tax;
                                            } else {
                                                $get_cart_total_for_redeem = $woocommerce->cart->subtotal_ex_tax;
                                            }
                                        }
                                        $point_control = wc_format_decimal(get_option('rs_redeem_point'));
                                        $point_control_price = wc_format_decimal(get_option('rs_redeem_point_value')); //i.e., 100 Points is equal to $1
                                        $cartpoints_string_to_replace = "[cartredeempoints]";
                                        $currency_symbol_string_to_find = "[currencysymbol]";
                                        $currency_value_string_to_find = "[pointsvalue]";
                                        $getmaxruleoption = get_option('rs_max_redeem_discount');
                                        $getfixedmaxoption = get_option('rs_fixed_max_redeem_discount');
                                        $getpercentmaxoption = get_option('rs_percent_max_redeem_discount');
                                        $errpercentagemsg = get_option('rs_errmsg_for_max_discount_type');
                                        $point_control = wc_format_decimal(get_option('rs_redeem_point'));
                                        $point_control_price = wc_format_decimal(get_option('rs_redeem_point_value'));
                                        $minimum_cart_total_redeem = get_option('rs_minimum_cart_total_points');
                                        if ($user_current_points >= get_option("rs_minimum_user_points_to_redeem")) {
                                            if ($get_cart_total_for_redeem >= $minimum_cart_total_redeem) {
                                                if (!is_array(get_option('rs_select_products_to_enable_redeeming'))) {
                                                    $allowproducts = explode(',', get_option('rs_select_products_to_enable_redeeming'));
                                                } else {
                                                    $allowproducts = get_option('rs_select_products_to_enable_redeeming');
                                                }

                                                if (!is_array(get_option('rs_exclude_products_to_enable_redeeming'))) {
                                                    $excludeproducts = explode(',', get_option('rs_exclude_products_to_enable_redeeming'));
                                                } else {
                                                    $excludeproducts = get_option('rs_exclude_products_to_enable_redeeming');
                                                }
                                                $allowcategory = get_option('rs_select_category_to_enable_redeeming');
                                                $excludecategory = get_option('rs_exclude_category_to_enable_redeeming');
                                                $coupon = array(
                                                    'post_title' => 'auto_redeem_' . strtolower($couponcodeuserlogin),
                                                    'post_content' => '',
                                                    'post_status' => 'publish',
                                                    'post_author' => get_current_user_id(),
                                                    'post_type' => 'shop_coupon',
                                                );
                                                $oldcouponid = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($user_ID, 'auto_redeemcoupon_ids', true);
                                                wp_delete_post($oldcouponid, true);
                                                $new_coupon_id = wp_insert_post($coupon);
                                                update_user_meta($user_ID, 'auto_redeemcoupon_ids', $new_coupon_id);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'carttotal', $woocommerce->cart->cart_contents_total);
                                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'cartcontenttotal', $woocommerce->cart->cart_contents_count);

                                                //Redeeming only for Selected Products option start
                                                $enableproductredeeming = get_option('rs_enable_redeem_for_selected_products');
                                                if ($enableproductredeeming == 'yes') {
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'product_ids', implode(',', array_filter(array_map('intval', $allowproducts))));
                                                }
                                                $excludeproductredeeming = get_option('rs_exclude_products_for_redeeming');
                                                if ($excludeproductredeeming == 'yes') {
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'exclude_product_ids', implode(',', array_filter(array_map('intval', $excludeproducts))));
                                                    $product = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($new_coupon_id, 'exclude_product_ids');

                                                    foreach ($woocommerce->cart->cart_contents as $key => $value) {
                                                        $product_idsss = $value['product_id'];
                                                        if ($product_idsss == $product) {
                                                            WC()->session->set('auto_redeemcoupon', 'no');
                                                        }
                                                    }
                                                }
                                                $enablecategoryredeeming = get_option('rs_enable_redeem_for_selected_category');
                                                if ($enablecategoryredeeming == 'yes') {
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'product_categories', implode(',', array_filter(array_map('intval', $allowcategory))));
                                                }
                                                $excludecategoryredeeming = get_option('rs_exclude_category_for_redeeming');
                                                if ($excludecategoryredeeming == 'yes') {
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'exclude_product_categories', implode(',', array_filter(array_map('intval', $excludecategory))));
                                                }

                                                //Redeeming only for Selected Products option End
                                                if (get_option('rs_apply_redeem_basedon_cart_or_product_total') == '1') {
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'carttotal', $woocommerce->cart->cart_contents_total);
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'cartcontenttotal', $woocommerce->cart->cart_contents_count);
                                                    $limitation_percentage_for_redeeming_for_button = get_option('rs_percentage_cart_total_auto_redeem');
                                                    $reddem_value_in_amount_percent = $limitation_percentage_for_redeeming_for_button / 100;
                                                    $reddem_points_for_total = $reddem_value_in_amount_percent * $get_cart_total_for_redeem;
                                                    $coupon_value_in_amount = $reddem_points_for_total;
                                                    if ($getmaxruleoption == '1') {
                                                        if ($getfixedmaxoption != '') {
                                                            if ($reddem_points_for_total > $getfixedmaxoption) {
                                                                $coupon_value_in_amount = $getfixedmaxoption;
                                                                $errpercentagemsg1 = str_replace('[percentage] %', $getfixedmaxoption, $errpercentagemsg);
                                                                wc_add_notice(__($errpercentagemsg1), 'error');
                                                            } else {
                                                                $coupon_value_in_amount = $reddem_points_for_total;
                                                            }
                                                        }
                                                    } else {
                                                        if ($getmaxruleoption == '2') {
                                                            if ($getpercentmaxoption != '') {
                                                                $percentageproduct = $getpercentmaxoption / 100;
                                                                $getpricepercent = $percentageproduct * $get_cart_total_for_redeem;

                                                                if ($getpricepercent > $reddem_points_for_total) {
                                                                    $coupon_value_in_amount = $reddem_points_for_total;
                                                                } else {
                                                                    $coupon_value_in_amount = $getpricepercent;
                                                                    $errpercentagemsg1 = str_replace('[percentage] ', $getpercentmaxoption, $errpercentagemsg);
                                                                    wc_add_notice(__($errpercentagemsg1), 'error');
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $getuserid = get_current_user_id();
                                                    $user_current_points = RSPointExpiry::get_sum_of_total_earned_points($getuserid);
                                                    $point_control = wc_format_decimal(get_option('rs_redeem_point'));
                                                    $point_control_price = wc_format_decimal(get_option('rs_redeem_point_value')); //i.e., 100 Points is equal to $1
                                                    $revised_amount = $coupon_value_in_amount * $point_control;
                                                    $coupon_value_in_points = $revised_amount / $point_control_price;
                                                    $user_current_points_in_value = $user_current_points / $point_control;
                                                    $user_current_points1 = $user_current_points_in_value * $point_control_price;
                                                    if ($coupon_value_in_points > $user_current_points) {
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'coupon_amount', $user_current_points1);
                                                    } else {
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'coupon_amount', $coupon_value_in_amount);
                                                    }
                                                } else {
                                                    $getsumofselectedproduct = RSFunctionToApplyCoupon::get_sum_of_selected_products('auto', '', $user_current_points);
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'carttotal', $getsumofselectedproduct);
                                                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'cartcontenttotal', $getsumofselectedproduct);
                                                    $limitation_percentage_for_redeeming_for_button = get_option('rs_percentage_cart_total_auto_redeem');
                                                    $reddem_value_in_amount_percent = $limitation_percentage_for_redeeming_for_button / 100;
                                                    $reddem_points_for_total = $reddem_value_in_amount_percent * $getsumofselectedproduct;
                                                    $coupon_value_in_amount = $reddem_points_for_total;
                                                    if ($reddem_points_for_total > $getsumofselectedproduct) {
                                                        $reddem_points_for_total = $getsumofselectedproduct;
                                                        $coupon_value_in_amount = $reddem_points_for_total;
                                                    }
                                                    if ($getmaxruleoption == '1') {
                                                        if ($getfixedmaxoption != '') {
                                                            if ($reddem_points_for_total > $getfixedmaxoption) {
                                                                $coupon_value_in_amount = $getfixedmaxoption;
                                                                $errpercentagemsg1 = str_replace('[percentage] %', $getfixedmaxoption, $errpercentagemsg);

                                                                wc_add_notice(__($errpercentagemsg1), 'error');
                                                            } else {
                                                                $coupon_value_in_amount = $reddem_points_for_total;
                                                            }
                                                        }
                                                    } else {
                                                        if ($getmaxruleoption == '2') {
                                                            if ($getpercentmaxoption != '') {
                                                                $percentageproduct = $getpercentmaxoption / 100;
                                                                $getpricepercent = $percentageproduct * $coupon_value_in_amount;

                                                                if ($getpricepercent > $reddem_points_for_total) {
                                                                    $coupon_value_in_amount = $reddem_points_for_total;
                                                                } else {
                                                                    $coupon_value_in_amount = $getpricepercent;
                                                                    $errpercentagemsg1 = str_replace('[percentage] ', $getpercentmaxoption, $errpercentagemsg);

                                                                    wc_add_notice(__($errpercentagemsg1), 'error');
                                                                }
                                                            }
                                                        }
                                                    }

                                                    $getuserid = get_current_user_id();
                                                    $user_current_points = RSPointExpiry::get_sum_of_total_earned_points($getuserid);
                                                    $point_control = wc_format_decimal(get_option('rs_redeem_point'));
                                                    $point_control_price = wc_format_decimal(get_option('rs_redeem_point_value')); //i.e., 100 Points is equal to $1
                                                    $revised_amount = $coupon_value_in_amount * $point_control;
                                                    $coupon_value_in_points = $revised_amount / $point_control_price;
                                                    $user_current_points_in_value = $user_current_points / $point_control;
                                                    $user_current_points1 = $user_current_points_in_value * $point_control_price;
                                                    if ($coupon_value_in_points > $user_current_points) {
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'coupon_amount', $user_current_points1);
                                                    } else {
                                                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($new_coupon_id, 'coupon_amount', $coupon_value_in_amount);
                                                    }
                                                }

                                                if ($woocommerce->cart->has_discount('auto_redeem_' . strtolower($couponcodeuserlogin)))
                                                    return;

                                                if (get_post_meta($new_coupon_id, 'coupon_amount', true) != 0) {
                                                    if (get_option('rs_minimum_redeeming_points') != '' && get_option('rs_maximum_redeeming_points') == '') {
                                                        if ($coupon_value_in_points > get_option('rs_minimum_redeeming_points')) {
                                                            $woocommerce->cart->add_discount('auto_redeem_' . strtolower($couponcodeuserlogin));
                                                        }
                                                    }

                                                    if (get_option('rs_maximum_redeeming_points') != '' && get_option('rs_minimum_redeeming_points') == '') {
                                                        if ($coupon_value_in_points < get_option('rs_maximum_redeeming_points')) {
                                                            $woocommerce->cart->add_discount('auto_redeem_' . strtolower($couponcodeuserlogin));
                                                        }
                                                    }

                                                    if (get_option('rs_minimum_redeeming_points') == get_option('rs_maximum_redeeming_points')) {
                                                        if (($coupon_value_in_points == get_option('rs_minimum_redeeming_points')) && ($coupon_value_in_points == get_option('rs_maximum_redeeming_points'))) {
                                                            $woocommerce->cart->add_discount('auto_redeem_' . strtolower($couponcodeuserlogin));
                                                        }
                                                    }

                                                    if (get_option('rs_minimum_redeeming_points') == '' && get_option('rs_maximum_redeeming_points') == '') {
                                                        $woocommerce->cart->add_discount('auto_redeem_' . strtolower($couponcodeuserlogin));
                                                    }

                                                    if (get_option('rs_minimum_redeeming_points') != '' && get_option('rs_maximum_redeeming_points') != '') {
                                                        if (($coupon_value_in_points >= get_option('rs_minimum_redeeming_points')) && ($coupon_value_in_points <= get_option('rs_maximum_redeeming_points'))) {
                                                            $woocommerce->cart->add_discount('auto_redeem_' . strtolower($couponcodeuserlogin));
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        public static function set_point_price_for_products_in_session($cart_item_key, $product_id = null, $quantity = null, $variation_id = null, $variation = null) {
            $product_id = $variation_id != null ? $variation_id : $product_id;
            $point_price_for_product = calculate_point_price_for_products($product_id);
            WC()->session->set($cart_item_key . 'point_price_for_product', $point_price_for_product);
        }

        public static function save_point_price_info_in_order($orderid) {
            global $woocommerce;
            $current_cart_contents = $woocommerce->cart->cart_contents;
            foreach ($current_cart_contents as $key => $value) {
                if (WC()->session->get($key . 'point_price_for_product')) {
                    $point_price_info[] = WC()->session->get($key . 'point_price_for_product');
                    update_post_meta($orderid, 'point_price_for_product_in_order', $point_price_info);
                }
            }
        }

        public static function total_points_display_in_cart($price) {
            global $woocommerce;
            $total1 = 0;
            $totalpoints1 = 0;
            $totalpoints2 = 0;
            $totalvariable = 0;
            $varpoints = array();
            $array = array();
            $points = array();
            $total = array();
            $linetotal = array();
            $linetaxforproduct = array();
            $linetaxforpointprice = array();
            $labelpoint = get_option('rs_label_for_point_value');

            if (get_option('rs_enable_disable_point_priceing') == '1') {
                $shippingcost = $woocommerce->shipping->shipping_total;
                $shipping_tax = $woocommerce->shipping->shipping_taxes;
                $shipping_tax_total = array_sum($shipping_tax);
                $coupon_amount = $woocommerce->cart->get_cart_discount_total();

                $ship = $shippingcost + $shipping_tax_total;
                $shippingcost_total = $ship;
                foreach ($woocommerce->cart->cart_contents as $key) {

                    $total22[] = $key['line_total'];

                    $product_id = $key['variation_id'] != 0 ? $key['variation_id'] : $key['product_id'];
                    $enablevariable = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_price');
                    $typeofprice1[] = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_pricing_type', true);

                    $typeofprice[] = check_display_price_type($product_id);
                    $points_array = calculate_point_price_for_products($product_id);

                    if ($points_array != NULL) {
                        $points = (float) implode(",", $points_array);
                        $opto1[] = $enablevariable;
                        $enable = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price');
                        $opto[] = $enable;
                    }


                    $enable = calculate_point_price_for_products($product_id);
                    if ($enable[$product_id] != '') {
                        $cart_object = $enable[$product_id] * $key['quantity'];
                        $array[] = $cart_object;
                        if (get_option('woocommerce_prices_include_tax') == 'no') {
                            $linetaxforpointprice[] = $key['line_subtotal_tax'];
                        }
                    } else {
                        $linetotal[] = $key['line_subtotal'];
                        $linetaxforproduct[] = $key['line_subtotal_tax'];
                    }
                }
                $current_conversion1 = wc_format_decimal(get_option('rs_redeem_point'));
                $point_amount1 = wc_format_decimal(get_option('rs_redeem_point_value'));
                $redeemedamount1 = $shippingcost_total * $current_conversion1;
                $redeemedpoints2 = $redeemedamount1 / $point_amount1;
                $totalvariable = array_sum($linetotal);
                $producttax = array_sum($linetaxforproduct);
                $pointpricetax = array_sum($linetaxforpointprice);
                $totalvariable = $totalvariable + $producttax + $pointpricetax;
                $newvalue = $totalvariable / wc_format_decimal(get_option('rs_redeem_point_value'));
                $updatedvalue = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
                $pointsproducttotal = array_sum($array) + $updatedvalue - $coupon_amount;
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                $totalwithtax = $pointsproducttotal + $redeemedpoints2;
                $pointsproducttotal = round($totalwithtax, $roundofftype);
                $labelposition = get_option('rs_sufix_prefix_point_price_label');
                if ($labelposition == '1') {
                    $update = $labelpoint . $pointsproducttotal;
                } else {
                    $replace = str_replace("/", "", $labelpoint);
                    $update = '/' . $pointsproducttotal . $replace;
                }
                if (in_array("yes", $opto) || in_array("1", $opto1)) {

                    if (in_array('2', $typeofprice)) {
                        $replace = str_replace("/", "", $update);
                        return $replace;
                    } else {
                        if (array_sum($array) > 0) {
                            if ($pointsproducttotal > 0) {
                                $update = $update;
                            } else {
                                $update = 0;
                            }
                            return $price . $update;
                        } else {
                            return $price;
                        }
                    }
                } else {
                    return $price;
                }
            } else {
                return $price;
            }
        }

        public static function display_points_price($product_price, $item, $item_key) {

            $points_array = array();
            $product_id = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
            if (get_option('rs_enable_disable_point_priceing') == '1') {
                $enablevariable = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_price');
                $enable = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price');
                if ($enablevariable == '1' || $enable == 'yes') {
                    $quantity = $item['quantity'];
                    $labelpoint = get_option('rs_label_for_point_value');
                    $labelposition = get_option('rs_sufix_prefix_point_price_label');

                    $points_array = calculate_point_price_for_products($product_id);

                    $points = implode(",", $points_array);

                    if ($points != '') {
                        $typeofprice = check_display_price_type($product_id);

                        if ($typeofprice == '2') {
                            $replace = str_replace("/", "", $labelpoint);
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $points = round($points, $roundofftype);
                            if ($labelposition == '1') {
                                $product_price = $replace . $points;
                            } else {
                                $product_price = $points . $replace;
                            }
                            return $product_price;
                        } else {
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $points = round($points, $roundofftype);
                            if ($labelposition == '1') {
                                $product_price1 = $labelpoint . $points;
                            } else {
                                $replace = str_replace("/", "", $labelpoint);
                                $product_price1 = '/' . $points . $replace;
                            }
                            $product_price = wc_price(rs_get_price($item['data'])) . $product_price1;

                            return $product_price;
                        }
                    } else {
                        return $product_price;
                    }
                } else {
                    return $product_price;
                }
            } else {
                return $product_price;
            }
        }

        public static function display_points_total($product_price, $item, $item_key) {
            $labelposition = get_option('rs_sufix_prefix_point_price_label');

            $product_id = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
            if (get_option('rs_enable_disable_point_priceing') == '1') {
                $enablevariable = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_price');
                $enable = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price');
                if ($enablevariable == '1' || $enable == 'yes') {
                    $quantity = $item['quantity'];
                    $labelpoint = get_option('rs_label_for_point_value');
                    $id = $item['product_id'];

                    $points_array = calculate_point_price_for_products($product_id);
                    $points = implode(",", $points_array);
                    if ($points != '') {
                        $typeofprice = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price_type') != '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price_type') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_pricing_type');
                        $typeofprice = check_display_price_type($product_id);
                        if ($typeofprice == '2') {
                            $replace = str_replace("/", "", $labelpoint);
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $points = round($points, $roundofftype);

                            if ($labelposition == '1') {
                                $product_price = $replace . $points * $quantity;
                            } else {
                                $product_price = $points * $quantity . $replace;
                            }
                            return $product_price;
                        } else {
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $points = round($points, $roundofftype);
                            $pointss = $points * $quantity;
                            if ($labelposition == '1') {
                                $product_price1 = $labelpoint . $pointss;
                            } else {
                                $replace = str_replace("/", "", $labelpoint);
                                $product_price1 = '/' . $pointss . $replace;
                            }
                            $product_price = wc_price($item['line_subtotal']) . $product_price1;
                            return $product_price;
                        }
                    } else {
                        return $product_price;
                    }
                } else {
                    return $product_price;
                }
            } else {
                return $product_price;
            }
        }

        public static function sell_individually_functionality($valid, $product_id, $quantity, $variation_id = NULL, $variations = NULL) {
            if (get_option('rs_enable_disable_point_priceing') == '1') {
                if (function_exists('WC')) {
                    global $woocommerce;
                    $cart_content = WC()->cart->get_cart();

                    if (!empty($cart_content)) {
                        $cart_contents_count = WC()->cart->cart_contents_count;
                        foreach ($cart_content as $key => $content) {
                            if ($cart_contents_count > 0 && 1 <= $cart_contents_count) {

                                global $woocommerce;
                                if ((float) $woocommerce->version >= (float) '3.0') {
                                    if (isset($content['data']->post_type) && $content['data']->post_type == 'product_variation') {
                                        $productid = $content['data']->get_parent_id();
                                        $parent_id = $content['data']->get_parent_id();
                                    } else {
                                        $productid = $content['data']->get_id();
                                        $product_ids = $content['data']->get_id();
                                    }
                                } else {
                                    if (isset($content['data']->variation_id)) {
                                        $productid = $content['data']->variation_id;
                                        $parent_id = $content['data']->variation_id;
                                    } else {
                                        $productid = $content['data']->id;
                                        $product_ids = $content['data']->id;
                                    }
                                }
                                if (self::check_is_point_pricing_enable($productid)) {

                                    if (isset($variation_id)) {
                                        $get_product_productid = $variation_id;
                                    } else {
                                        $get_product_productid = $product_id;
                                    }

                                    if (self::check_is_point_pricing_enable($get_product_productid)) {
                                        if (isset($variation_id)) {

                                            if ($variation_id == $content['data']->variation_id) {

                                                $valid = false;
                                                wc_add_notice(get_option('rs_errmsg_for_point_price_product_with_same'), 'error');
                                                return $valid;
                                            }
                                        } else {
                                            if ($product_id == $content['data']->id) {

                                                $valid = false;
                                                wc_add_notice(get_option('rs_errmsg_for_point_price_product_with_same'), 'error');
                                                return $valid;
                                            }
                                        }
                                    } else {
                                        $valid = false;
                                        wc_add_notice(get_option('rs_errmsg_for_normal_product_with_point_price'), 'error');
                                        return $valid;
                                    }
                                } else {
                                    if (isset($variation_id)) {
                                        $get_product_productid = $variation_id;
                                    } else {
                                        $get_product_productid = $product_id;
                                    }
                                    if (self::check_is_point_pricing_enable($get_product_productid)) {

                                        $valid = false;
                                        wc_add_notice(get_option('rs_errmsg_for_point_price_product_with_normal'), 'error');
                                        return $valid;
                                    } else {

                                        $valid = true;
                                        return $valid;
                                    }
                                }
                            } else {
                                if (isset($variation_id)) {
                                    $get_product_productid = $variation_id;
                                } else {
                                    $get_product_productid = $product_id;
                                }
                                if (self::check_is_point_pricing_enable($get_product_productid)) {
                                    if (self::check_cart_contain_subscription()) {
                                        $valid = false;
                                        wc_add_notice("You cannot add more than one product", 'error');
                                        return $valid;
                                    } else {
                                        WC()->cart->empty_cart();
                                        $valid = true;
                                        wc_add_notice("Cannot add normal product with point pricing product", 'error');
                                        return $valid;
                                    }
                                } else {

                                    $valid = true;
                                    return $valid;
                                }
                            }
                        }
                    }
                    if (!is_user_logged_in()) {
                        if (self::check_is_point_pricing_enable($product_id)) {
                            $valid = false;
                            wc_add_notice("Please signup to purchase this product", 'error');
                            return $valid;
                        }
                    }
                }
            }
            return $valid;
        }

        public static function check_is_point_pricing_enable($product_id) {
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
                RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price_type');
                $productlevel = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price') != '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_price');
                $productlevelrewardtype = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_point_price_type') != '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_point_price_type') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_price_type');
                $productlevelrewardpoints = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem__points') != '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem__points') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, 'price_points');
                $productdispalytype = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price_type') != '' ? RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_rewardsystem_enable_point_price_type') : RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($product_id, '_enable_reward_points_pricing_type');
                if (($productlevel == 'yes') || ($productlevel == '1')) {
                    if ($productdispalytype == '2') {
                        if ($productlevelrewardpoints != '') {

                            return true;
                        } else {

                            return false;
                        }
                    }
                } else {
                    return false;
                }
            }
        }

        /* Function for hiding the couon field */

        public static function show_hide_coupon_code() {

            global $woocommerce;
            $type = array();
            if (!is_user_logged_in()) {
                if (is_array($woocommerce->cart->get_applied_coupons())) {
                    foreach ($woocommerce->cart->get_applied_coupons() as $coupons) {
                        if (strpos($coupons, 'sumo_') !== false) {
                            WC()->cart->remove_coupon($coupons);
                        }
                        if (strpos($coupons, 'auto_redeem_') !== false) {
                            WC()->cart->remove_coupon($coupons);
                        }
                    }
                }
            }
            $type = array();
            foreach ($woocommerce->cart->cart_contents as $item) {
                $product_id = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
                $type[] = check_display_price_type($product_id);
                $enable = calculate_point_price_for_products($product_id);
                if ($enable[$product_id] != '') {
                    $cart_object[] = $enable[$product_id];
                }
            }
            if (!empty($cart_object)) {
                ?>
                <style type="text/css">
                    .coupon{
                        display: none;
                    }

                    .showcoupon {
                        display: none;
                    }

                </style>
                <?php
            }

            if (in_array(2, $type)) {
                ?>
                <style type="text/css">
                    .coupon{
                        display: none;
                    }
                    .showcoupon {
                        display: none;
                    }

                </style>
                <?php
            }
        }

        public static function display_total_earned_points($param) {
            if (is_user_logged_in()) {
                global $woocommerce;
                if (get_option('rs_show_hide_total_points_cart_field') == '1') {
                    if (get_option('rs_enable_disable_reward_point_based_coupon_amount') == 'yes') {
                        $points_info = self::moified_points_for_products_in_cart();
                    } else {
                        $points_info = self::original_points_for_product_in_cart();
                    }
                    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                    if (is_array($points_info)) {
                        if (array_sum($points_info) != 0) {
                            $total = $woocommerce->cart->discount_cart;
                            if ($total != 0) {
                                if (get_option('rs_enable_redeem_for_order') == 'no' && get_option('rs_disable_point_if_coupon') == 'no') {
                                    ?>
                                    <div class="points_total" >
                                        <tr class="points-totalvalue">
                                            <th><?php echo get_option('rs_total_earned_point_caption'); ?></th>
                                            <td><?php echo round(array_sum($points_info), $roundofftype); ?></td>
                                        </tr>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <div class="points_total" >
                                    <tr class="points-totalvalue">
                                        <th><?php echo get_option('rs_total_earned_point_caption'); ?></th>
                                        <td><?php echo round(array_sum($points_info), $roundofftype); ?></td>
                                    </tr>
                                </div>
                                <?php
                            }
                        }
                    }
                }
            }
        }

        public static function reward_system_add_redeem_message_after_cart_table() {
            if (is_user_logged_in()) {
                if (get_option('rs_redeem_field_type_option') == '1') {
                    global $woocommerce;
                    $minimum_cart_total_redeem = get_option('rs_minimum_cart_total_points');
                    $maximum_cart_total_redeem = get_option('rs_maximum_cart_total_points');
                    $current_carttotal_amount = $woocommerce->cart->subtotal;
                    if ($minimum_cart_total_redeem != '' && $maximum_cart_total_redeem != '') {
                        if ($current_carttotal_amount >= $minimum_cart_total_redeem && $current_carttotal_amount <= $maximum_cart_total_redeem) {
                            self::reward_system_add_message_after_cart_table();
                        } else {
                            if (get_option('rs_show_hide_maximum_cart_total_error_message') == '1') {
                                $userid = get_current_user_id();
                                $banning_type = FPRewardSystem::check_banning_type($userid);
                                if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
                                    $max_cart_total_redeeming = get_option('rs_min_cart_total_redeem_error');
                                    $max_cart_amount_to_find = "[carttotal]";
                                    $max_cart_total_currency_to_find = "[currencysymbol]";
                                    $max_cart_amount_to_replace = get_option('rs_minimum_cart_total_points');
                                    $max_cart_total_currency_to_replace = get_woocommerce_formatted_price($max_cart_amount_to_replace);
                                    $max_cart_total_msg1 = str_replace($max_cart_amount_to_find, $max_cart_total_currency_to_replace, $max_cart_total_redeeming);
                                    $max_cart_total_replaced = str_replace($max_cart_total_currency_to_find, "", $max_cart_total_msg1);
                                    ?>
                                    <div class="woocommerce-error"><?php echo $max_cart_total_replaced; ?></div>
                                    <?php
                                }
                            }
                        }
                    } else if ($minimum_cart_total_redeem != '' && $maximum_cart_total_redeem == '') {
                        if ($current_carttotal_amount >= $minimum_cart_total_redeem) {
                            self::reward_system_add_message_after_cart_table();
                        } else {
                            if (get_option('rs_show_hide_minimum_cart_total_error_message') == '1') {
                                $userid = get_current_user_id();
                                $banning_type = FPRewardSystem::check_banning_type($userid);
                                if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
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
                        }
                    } else if ($minimum_cart_total_redeem == '' && $maximum_cart_total_redeem != '') {
                        if ($current_carttotal_amount <= $maximum_cart_total_redeem) {
                            self::reward_system_add_message_after_cart_table();
                        } else {
                            if (get_option('rs_show_hide_maximum_cart_total_error_message') == '1') {
                                $userid = get_current_user_id();
                                $banning_type = FPRewardSystem::check_banning_type($userid);
                                if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
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
                        }
                    } else if ($minimum_cart_total_redeem == '' && $maximum_cart_total_redeem == '') {
                        self::reward_system_add_message_after_cart_table();
                    }
                }
            }
        }

        public static function reward_system_add_message_after_cart_table() {
            $type = array();
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
                ?>
                <style type="text/css">
                <?php echo get_option('rs_cart_page_custom_css'); ?>
                </style>
                <?php
                global $woocommerce;
                global $coupon_code;
                if (is_user_logged_in()) {
                    $user_ID = get_current_user_id();
                    $getinfousernickname = get_user_by('id', $user_ID);
                    $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                    $minimum_cart_total_redeem = get_option('rs_minimum_cart_total_points');
                    $cart_subtotal_for_redeem = $woocommerce->cart->subtotal;
                    $cart_subtotal_redeem_amount = $cart_subtotal_for_redeem;
                    $get_old_points = RSPointExpiry::get_sum_of_total_earned_points($user_ID);
                    if ($get_old_points > 0) {
                        $coupon_code = 'sumo_' . strtolower($couponcodeuserlogin); // Code
                        $coupon = new WC_Coupon($coupon_code);
                        $checkfirstimeredeem = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($user_ID, 'rsfirsttime_redeemed');
                        if ($checkfirstimeredeem != '1') {
                            if ($get_old_points >= get_option("rs_first_time_minimum_user_points")) {
                                if ($cart_subtotal_redeem_amount >= $minimum_cart_total_redeem) {
                                    $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
                                    $array = $woocommerce->cart->get_applied_coupons();
                                    if (!in_array($auto_redeem_name, $array)) {
                                        foreach ($woocommerce->cart->cart_contents as $item) {
                                            $product_id = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
                                            $type[] = check_display_price_type($product_id);
                                            $enable = calculate_point_price_for_products($product_id);
                                            if ($enable[$product_id] != '') {
                                                $cart_object[] = $enable[$product_id];
                                            }
                                        }
                                        if (empty($cart_object)) {
                                            if (!in_array('2', $type)) {
                                                if (get_option('rs_redeem_field_type_option') == '1') {
                                                    if (is_cart() && !is_checkout()) {
                                                        ?>
                                                        <div class="fp_apply_reward">
                                                            <?php if (get_option("rs_show_hide_redeem_caption") == '1') { ?>
                                                                <label for="rs_apply_coupon_code_field"><?php echo get_option('rs_redeem_field_caption'); ?></label>
                                                            <?php } ?>
                                                            <?php
                                                            if (get_option('rs_show_hide_redeem_placeholder') == '1') {
                                                                $placeholder = get_option('rs_redeem_field_placeholder');
                                                            }
                                                            ?>
                                                            <input id="rs_apply_coupon_code_field" class="input-text" type="text" placeholder="<?php echo $placeholder; ?>" value="" name="rs_apply_coupon_code_field">
                                                            <input class="button <?php echo get_option('rs_extra_class_name_apply_reward_points'); ?>" type="submit" id='mainsubmi' value="<?php echo get_option('rs_redeem_field_submit_button_caption'); ?>" name="rs_apply_coupon_code">
                                                        </div>
                                                        <div class='rs_warning_message' style='display:inline-block;color:red'></div>
                                                        <?php
                                                    }
                                                }
                                                if (get_option('rs_redeem_field_type_option_checkout') == '1') {
                                                    if (is_checkout()) {
                                                        ?>
                                                        <div class="fp_apply_reward">
                                                            <?php if (get_option("rs_show_hide_redeem_caption") == '1') { ?>
                                                                <label for="rs_apply_coupon_code_field"><?php echo get_option('rs_redeem_field_caption'); ?></label>
                                                            <?php } ?>
                                                            <?php
                                                            if (get_option('rs_show_hide_redeem_placeholder') == '1') {
                                                                $placeholder = get_option('rs_redeem_field_placeholder');
                                                            }
                                                            ?>
                                                            <input id="rs_apply_coupon_code_field" class="input-text" type="text" placeholder="<?php echo $placeholder; ?>" value="" name="rs_apply_coupon_code_field">
                                                            <input class="button <?php echo get_option('rs_extra_class_name_apply_reward_points'); ?>" type="submit" id='mainsubmi' value="<?php echo get_option('rs_redeem_field_submit_button_caption'); ?>" name="rs_apply_coupon_code">
                                                        </div>
                                                        <div class='rs_warning_message' style='display:inline-block;color:red'></div>
                                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if (get_option('rs_show_hide_minimum_cart_total_error_message') == '1') {
                                        $userid = get_current_user_id();
                                        $banning_type = FPRewardSystem::check_banning_type($userid);
                                        if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
                                            $min_cart_total_redeeming = get_option('rs_min_cart_total_redeem_error');
                                            $min_cart_amount_to_find = "[carttotal]";
                                            $min_cart_total_currency_to_find = "[currencysymbol]";
                                            $min_cart_amount_to_replace = get_option('rs_minimum_cart_total_points');
                                            $min_cart_total_currency_to_replace = get_woocommerce_formatted_price($min_cart_amount_to_replace);
                                            $min_cart_total_msg1 = str_replace($min_cart_amount_to_find, $min_cart_total_currency_to_replace, $min_cart_total_redeeming);
                                            $min_cart_total_replaced = str_replace($min_cart_total_currency_to_find, "", $min_cart_total_msg1);
                                            ?>
                                            <div class="woocommerce-info"><?php echo $min_cart_total_replaced; ?></div>
                                            <?php
                                        }
                                    }
                                }
                            } else {
                                if (get_option('rs_show_hide_first_redeem_error_message') == '1') {
                                    $userid = get_current_user_id();
                                    $banning_type = FPRewardSystem::check_banning_type($userid);
                                    if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
                                        $rs_first_redeem_message = get_option('rs_min_points_first_redeem_error_message');
                                        $rs_first_redeem_to_find = "[firstredeempoints]";
                                        $rs_first_redeem_to_replace = get_option('rs_first_time_minimum_user_points');
                                        $rs_first_redeem_replaced = str_replace($rs_first_redeem_to_find, $rs_first_redeem_to_replace, $rs_first_redeem_message);
                                        ?>
                                        <div class="woocommerce-info"><?php echo $rs_first_redeem_replaced; ?></div>
                                        <?php
                                    }
                                }
                            }
                        } else {
                            if ($get_old_points >= get_option("rs_minimum_user_points_to_redeem")) {
                                if ($cart_subtotal_redeem_amount >= $minimum_cart_total_redeem) {
                                    $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
                                    $array = $woocommerce->cart->get_applied_coupons();
                                    if (!in_array($auto_redeem_name, $array)) {
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
                                                if (get_option('rs_redeem_field_type_option') == '1') {
                                                    if (is_cart() && !is_checkout()) {
                                                        ?>
                                                        <div class="fp_apply_reward">
                                                            <?php if (get_option("rs_show_hide_redeem_caption") == '1') { ?>
                                                                <label for="rs_apply_coupon_code_field"><?php echo get_option('rs_redeem_field_caption'); ?></label>
                                                            <?php } ?>
                                                            <?php
                                                            if (get_option('rs_show_hide_redeem_placeholder') == '1') {
                                                                $placeholder = get_option('rs_redeem_field_placeholder');
                                                            }
                                                            ?>
                                                            <input id="rs_apply_coupon_code_field" class="input-text" type="text" placeholder="<?php echo $placeholder; ?>" value="" name="rs_apply_coupon_code_field">
                                                            <input class="button <?php echo get_option('rs_extra_class_name_apply_reward_points'); ?>" type="submit" id='mainsubmi' value="<?php echo get_option('rs_redeem_field_submit_button_caption'); ?>" name="rs_apply_coupon_code">
                                                        </div>
                                                        <div class='rs_warning_message' style='display:inline-block;color:red'></div>
                                                        <?php
                                                    }
                                                }
                                                if (get_option('rs_redeem_field_type_option_checkout') == '1') {
                                                    if (is_checkout()) {
                                                        ?>
                                                        <div class="fp_apply_reward">
                                                            <?php if (get_option("rs_show_hide_redeem_caption") == '1') { ?>
                                                                <label for="rs_apply_coupon_code_field"><?php echo get_option('rs_redeem_field_caption'); ?></label>
                                                            <?php } ?>
                                                            <?php
                                                            if (get_option('rs_show_hide_redeem_placeholder') == '1') {
                                                                $placeholder = get_option('rs_redeem_field_placeholder');
                                                            }
                                                            ?>
                                                            <input id="rs_apply_coupon_code_field" class="input-text" type="text" placeholder="<?php echo $placeholder; ?>" value="" name="rs_apply_coupon_code_field">
                                                            <input class="button <?php echo get_option('rs_extra_class_name_apply_reward_points'); ?>" type="submit" id='mainsubmi' value="<?php echo get_option('rs_redeem_field_submit_button_caption'); ?>" name="rs_apply_coupon_code">
                                                        </div>
                                                        <div class='rs_warning_message' style='display:inline-block;color:red'></div>
                                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if (get_option('rs_show_hide_minimum_cart_total_error_message') == '1') {
                                        $userid = get_current_user_id();
                                        $banning_type = FPRewardSystem::check_banning_type($userid);
                                        if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
                                            $min_cart_total_redeeming = get_option('rs_min_cart_total_redeem_error');
                                            $min_cart_amount_to_find = "[carttotal]";
                                            $min_cart_total_currency_to_find = "[currencysymbol]";
                                            $min_cart_amount_to_replace = get_option('rs_minimum_cart_total_points');
                                            $min_cart_total_currency_to_replace = get_woocommerce_formatted_price($min_cart_amount_to_replace);
                                            $min_cart_total_msg1 = str_replace($min_cart_amount_to_find, $min_cart_total_currency_to_replace, $min_cart_total_redeeming);
                                            $min_cart_total_replaced = str_replace($min_cart_total_currency_to_find, "", $min_cart_total_msg1);
                                            ?>
                                            <div class="woocommerce-info"><?php echo $min_cart_total_replaced; ?></div>
                                            <?php
                                        }
                                    }
                                }
                            } else {
                                if (get_option('rs_show_hide_after_first_redeem_error_message') == '1') {
                                    $userid = get_current_user_id();
                                    $banning_type = FPRewardSystem::check_banning_type($userid);
                                    if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
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

                            <?php
                        }
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
        }

        /* Function to get the reward points to be displayed in message in cart and checkout */

        public static function original_points_for_product_in_cart() {
            global $checkproduct;
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                global $messageglobal;
                global $totalrewardpointsnew;
                global $totalrewardpoints;
                $rewardpoints = array('0');
                $totalrewardpoints;
                global $woocommerce;
                global $value;
                foreach ($woocommerce->cart->cart_contents as $key => $value) {
                    $restrictpoints = rs_function_to_restrict_points_for_product_which_has_saleprice($value['product_id'], $value['variation_id']);
                    if ($restrictpoints == 'no') {
                        $checklevel = 'no';
                        $points = check_level_of_enable_reward_point($value['product_id'], $value['variation_id'], $value, $checklevel, $referred_user = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
                        $totalrewardpoints = RSMemberFunction::user_role_based_reward_points(get_current_user_id(), $points);
                        $product_id = $value['variation_id'] != (0 || '') ? $value['variation_id'] : $value['product_id'];
                        $totalrewardpointsnew[$product_id] = $totalrewardpoints;
                    }
                }
                return $totalrewardpointsnew;
            }
        }

        public static function check_the_applied_coupons() {
            global $woocommerce;
            if (get_option('_rs_not_allow_earn_points_if_sumo_coupon') == 'yes') {
                if (is_array($woocommerce->cart->get_applied_coupons())) {
                    $getappliedcoupon = $woocommerce->cart->get_applied_coupons();
                    if (!empty($getappliedcoupon)) {
                        foreach ($woocommerce->cart->get_applied_coupons() as $coupons) {
                            $coupon_id_array = new WC_Coupon($coupons);
                            $coupon_obj = rs_get_coupon_obj($coupon_id_array);
                            $coupon_id = $coupon_obj['coupon_id'];
                            $getd = get_post_meta($coupon_id, 'sumo_coupon_check', true);
                            if ($getd == 'yes') {
                                return true;
                            }
                        }
                    } else {
                        return false;
                    }
                }
            }
            if (get_option('rs_enable_redeem_for_order') == 'yes') {
                if (is_array($woocommerce->cart->get_applied_coupons())) {
                    $getappliedcoupon = $woocommerce->cart->get_applied_coupons();
                    if (!empty($getappliedcoupon)) {
                        $currentuserid = get_current_user_id();
                        $user_ID = get_current_user_id();
                        $getinfousernickname = get_user_by('id', $user_ID);
                        $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                        $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
                        $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
                        foreach ($woocommerce->cart->get_applied_coupons() as $coupons) {
                            if (strtolower($coupons) == $usernickname || strtolower($coupons) == $auto_redeem_name) {
                                return true;
                            } else {
                                if (get_option('rs_disable_point_if_coupon') == 'yes') {
                                    if (!empty($getappliedcoupon)) {
                                        return true;
                                    }
                                }
                                return false;
                            }
                        }
                    } else {
                        return false;
                    }
                }
            } else {
                if (get_option('rs_disable_point_if_coupon') == 'yes') {
                    if (is_array($woocommerce->cart->get_applied_coupons())) {
                        $getappliedcoupon = $woocommerce->cart->get_applied_coupons();
                        if (!empty($getappliedcoupon)) {
                            $currentuserid = get_current_user_id();
                            $user_ID = get_current_user_id();
                            $getinfousernickname = get_user_by('id', $user_ID);
                            $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                            $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
                            $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
                            foreach ($woocommerce->cart->get_applied_coupons() as $coupons) {
                                if ($coupons == $usernickname) {
                                    return false;
                                }
                                if ($coupons == $auto_redeem_name) {
                                    return false;
                                }
                                return true;
                            }
                        } else {
                            return false;
                        }
                    }
                }

                return false;
            }
        }

        public static function display_msg_in_cart_page() {
            global $woocommerce;
            global $value;
            global $totalrewardpointsnew;
            global $messageglobal;
            if (is_user_logged_in()) {
                $checkenableoption = self::check_the_applied_coupons();
                if (get_option('rs_show_hide_message_for_each_products') == '1') {
                    if ($checkenableoption == false) {
                        if (is_array($totalrewardpointsnew)) {
                            if (!empty($totalrewardpointsnew)) {
                                if (array_sum($totalrewardpointsnew) > 0) {
                                    if (is_array($messageglobal) && $messageglobal != NULL) {
                                        ?>
                                        <div class="woocommerce-info sumo_reward_points_info_message">
                                            <?php
                                            foreach ($messageglobal as $globalcommerce) {
                                                echo $globalcommerce;
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $checkenableoption = self::check_the_applied_coupons();
                if (get_option('rs_enable_acc_creation_for_guest_checkout_page') == 'yes' && get_option('rs_show_hide_message_for_each_products') == '1') {
                    if ($checkenableoption == false) {
                        if (is_array($messageglobal) && $messageglobal != NULL) {
                            ?>
                            <div class="woocommerce-info sumo_reward_points_info_message">
                                <?php
                                foreach ($messageglobal as $globalcommerce) {
                                    echo $globalcommerce;
                                }
                                ?>
                            </div>
                            <?php
                        }
                    }
                }
            }
        }

        public static function display_msg_in_cart_page_for_balance_reward_points() {
            global $woocommerce;
            if (is_user_logged_in()) {
                if (get_option('rs_disable_point_if_coupon') == 'yes') {
                    foreach ($woocommerce->cart->get_applied_coupons() as $coupons) {

                        $user_ID = get_current_user_id();
                        $getinfousernickname = get_user_by('id', $user_ID);
                        $couponcodeuserlogin = $getinfousernickname->user_login;
                        $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
                        $auto_redeem_name = 'auto_redeem_' . strtolower("$couponcodeuserlogin");
                        if (strtolower($coupons) != $auto_redeem_name && strtolower($coupons) != $usernickname) {
                            ?>
                            <div class="woocommerce-info sumo_reward_points_auto_redeem_message">
                                <?php echo get_option('rs_errmsg_for_coupon_in_order'); ?>
                            </div>
                            <?php
                        }
                    }
                }
                if (get_option('rs_show_hide_message_for_redeem_points') == '1') {
                    if (is_array($woocommerce->cart->get_applied_coupons())) {
                        $user_ID = get_current_user_id();
                        $getinfousernickname = get_user_by('id', $user_ID);
                        $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                        $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
                        $auto_redeem_name = 'auto_redeem_' . strtolower("$couponcodeuserlogin");

                        if (isset($woocommerce->cart->coupon_discount_amounts["$auto_redeem_name"])) {
                            $total = $woocommerce->cart->coupon_discount_amounts["$auto_redeem_name"];

                            if ($total != 0) {
                                foreach ($woocommerce->cart->get_applied_coupons() as $coupons) {
                                    if (strtolower($coupons) == $auto_redeem_name) {
                                        ?>
                                        <div class="woocommerce-message sumo_reward_points_auto_redeem_message">
                                            <?php echo do_shortcode(get_option('rs_message_user_points_redeemed_in_cart')); ?>
                                        </div>
                                        <?php
                                        if (get_option('rs_enable_redeem_for_order') == 'yes') {
                                            ?>
                                            <div class="woocommerce-info sumo_reward_points_auto_redeem_error_message">
                                                <?php echo get_option('rs_errmsg_for_redeeming_in_order'); ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                            }
                        }

                        if (isset($woocommerce->cart->coupon_discount_amounts["$usernickname"])) {
                            $total = $woocommerce->cart->coupon_discount_amounts["$usernickname"];
                            if ($total != 0) {

                                foreach ($woocommerce->cart->get_applied_coupons() as $coupons) {
                                    if (strtolower($coupons) == $usernickname || strtolower($coupons) == $auto_redeem_name) {
                                        $userid = get_current_user_id();
                                        $banning_type = FPRewardSystem::check_banning_type($userid);
                                        if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
                                            ?>
                                            <div class="woocommerce-message sumo_reward_points_manual_redeem_message">
                                                <?php echo do_shortcode(get_option('rs_message_user_points_redeemed_in_cart')); ?>
                                            </div>
                                            <?php
                                            /* Error Message to be Displayed When the order contain only redeeming */
                                            if (get_option('rs_enable_redeem_for_order') == 'yes') {
                                                ?>
                                                <div class="woocommerce-info sumo_reward_points_manual_redeem_error_message">
                                                    <?php echo get_option('rs_errmsg_for_redeeming_in_order'); ?>
                                                </div>
                                                <?php
                                            }
                                            //  if (get_option('rs_redeem_field_type_option') == '2') {
                                            ?>
                                            <div class="sumo_reward_point_hide_field_script" data-sumo_coupon="yes">
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function () {
                                                        jQuery("#mainsubmi").parent().hide();
                                                    });
                                                </script>
                                            </div>
                                            <?php
                                            // }

                                            if (get_option('rs_redeem_field_type_option_checkout') == '2') {
                                                ?>
                                                <div class="sumo_reward_point_hide_field_script" data-sumo_coupon="yes">
                                                    <script type="text/javascript">
                                                        jQuery(document).ready(function () {
                                                            jQuery("#mainsubmi").parent().hide();
                                                        });
                                                    </script>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <?php
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        public static function display_msg_in_checkout_page() {
            global $woocommerce;
            global $value;
            global $totalrewardpointsnew;
            global $messageglobal;
            if (is_user_logged_in()) {
                $checkenableoption = self::check_the_applied_coupons();
                if (get_option('rs_show_hide_message_for_each_products_checkout_page') == '1') {
                    if ($checkenableoption == false) {
                        if (is_array($totalrewardpointsnew)) {
                            if (array_sum($totalrewardpointsnew) > 0) {
                                if (is_array($messageglobal) && $messageglobal != NULL) {
                                    ?>
                                    <div class="woocommerce-info">
                                        <?php
                                        if (is_array($messageglobal)) {
                                            foreach ($messageglobal as $globalcommerce) {
                                                echo $globalcommerce;
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                }
            } else {
                $checkenableoption = self::check_the_applied_coupons();
                if (get_option('rs_enable_acc_creation_for_guest_checkout_page') == 'yes' && get_option('rs_show_hide_message_for_each_products_checkout_page') == '1') {
                    if ($checkenableoption == false) {
                        if (is_array($totalrewardpointsnew)) {
                            if (array_sum($totalrewardpointsnew) > 0) {
                                if (is_array($messageglobal) && $messageglobal != NULL) {
                                    ?>
                                    <div class="woocommerce-info">
                                        <?php
                                        if (is_array($messageglobal)) {
                                            foreach ($messageglobal as $globalcommerce) {
                                                echo $globalcommerce;
                                            }
                                        }
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

        public static function get_redeem_point_to_display_in_msg() {
            global $woocommerce;
            global $value;
            $user_ID = get_current_user_id();
            $getinfousernickname = get_user_by('id', $user_ID);
            $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
            $user_current_points = RSPointExpiry::get_sum_of_total_earned_points($user_ID);
            $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
            $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
            if (isset($woocommerce->cart->coupon_discount_amounts["$usernickname"])) {
                $total = $woocommerce->cart->coupon_discount_amounts[$usernickname];
                $tax = $woocommerce->cart->coupon_discount_tax_amounts[$usernickname];
                if (get_option('woocommerce_tax_display_cart') == 'incl' && get_option('woocommerce_prices_include_tax') == 'no') {
                    $total = $total + $tax;
                } elseif (get_option('woocommerce_tax_display_cart') == 'incl' && get_option('woocommerce_prices_include_tax') == 'yes') {
                    $total = $total + $tax;
                }
                $current_conversion = wc_format_decimal(get_option('rs_redeem_point'));
                $point_amount = wc_format_decimal(get_option('rs_redeem_point_value'));
                $newtotal = $total * $current_conversion;
                $newtotal = $newtotal / $point_amount;
                if ($newtotal > $user_current_points) {
                    $newtotal = $user_current_points;
                }

                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                return round($newtotal, $roundofftype);
            }

            if (isset($woocommerce->cart->coupon_discount_amounts["$auto_redeem_name"])) {
                $total = $woocommerce->cart->coupon_discount_amounts[$auto_redeem_name];
                $tax = $woocommerce->cart->coupon_discount_tax_amounts[$auto_redeem_name];
                if (get_option('woocommerce_tax_display_cart') == 'incl' && get_option('woocommerce_prices_include_tax') == 'no') {
                    $total = $total + $tax;
                } elseif (get_option('woocommerce_tax_display_cart') == 'incl' && get_option('woocommerce_prices_include_tax') == 'yes') {
                    $total = $total + $tax;
                }
                $current_conversion = wc_format_decimal(get_option('rs_redeem_point'));
                $point_amount = wc_format_decimal(get_option('rs_redeem_point_value'));
                $newtotal = $total * $current_conversion;
                $newtotal = $newtotal / $point_amount;

                if ($newtotal > $user_current_points) {
                    $newtotal = $user_current_points;
                }
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                return round($newtotal, $roundofftype);
            }
        }

        public static function display_msg_in_checkout_page_for_balance_reward_points() {
            global $woocommerce;
            if (get_option('rs_show_hide_message_for_redeem_points_checkout_page') == '1') {
                if (is_user_logged_in()) {
                    if (is_array($woocommerce->cart->get_applied_coupons())) {
                        $user_ID = get_current_user_id();
                        $getinfousernickname = get_user_by('id', $user_ID);
                        $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';

                        $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
                        $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
                        if (isset($woocommerce->cart->coupon_discount_amounts["$auto_redeem_name"])) {
                            $total = $woocommerce->cart->coupon_discount_amounts["$auto_redeem_name"];
                            if ($total != 0) {
                                foreach ($woocommerce->cart->get_applied_coupons() as $coupons) {
                                    if (strtolower($coupons) == $auto_redeem_name) {
                                        ?>
                                        <div class="woocommerce-message">
                                            <?php echo do_shortcode(get_option('rs_message_user_points_redeemed_in_checkout')); ?>
                                        </div>
                                        <?php
                                        if (get_option('rs_enable_redeem_for_order') == 'yes') {
                                            ?>
                                            <div class="woocommerce-info">
                                                <?php echo get_option('rs_errmsg_for_redeeming_in_order'); ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                            }
                        }
                        if (isset($woocommerce->cart->coupon_discount_amounts["$usernickname"])) {
                            $total = $woocommerce->cart->coupon_discount_amounts["$usernickname"];
                            if ($total != 0) {
                                foreach ($woocommerce->cart->get_applied_coupons() as $coupons) {
                                    if (strtolower($coupons) == $usernickname || strtolower($coupons) == $auto_redeem_name) {

                                        $userid = get_current_user_id();
                                        $banning_type = FPRewardSystem::check_banning_type($userid);
                                        if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
                                            ?>
                                            <div class="woocommerce-message">
                                                <?php echo do_shortcode(get_option('rs_message_user_points_redeemed_in_checkout')); ?>
                                            </div>
                                            <?php
                                            /* Error Message to be Displayed When the order contain only redeeming */
                                            if (get_option('rs_enable_redeem_for_order') == 'yes') {
                                                ?>
                                                <div class="woocommerce-info">
                                                    <?php echo get_option('rs_errmsg_for_redeeming_in_order'); ?>
                                                </div>
                                                <?php
                                            }

                                            // if (get_option('rs_redeem_field_type_option') == '2') {
                                            ?>
                                            <script type="text/javascript">
                                                jQuery(document).ready(function () {
                                                    jQuery("#mainsubmi").parent().hide();
                                                });</script>
                                            <?php
                                            // }

                                            if (get_option('rs_redeem_field_type_option_checkout') == '2') {
                                                ?>
                                                <div class="sumo_reward_point_hide_field_script" data-sumo_coupon="yes">
                                                    <script type="text/javascript">
                                                        jQuery(document).ready(function () {
                                                            jQuery("#mainsubmi").parent().hide();
                                                        });
                                                    </script>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <?php
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        public static function get_each_product_price_in_cart() {
            global $totalrewardpoints;
            global $checkproduct;
            global $value;
            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
            return round($totalrewardpoints, $roundofftype);
        }

        public static function get_each_producttitle_in_cart() {
            global $checkproduct;
            global $value;
            $variation = rs_get_product_object($value);
            if (is_object($variation) && ($variation->is_type('simple') || ($variation->is_type('subscription')))) {
                return "<strong>" . get_the_title($value) . "</strong>";
            } else {
                if (is_object($variation) && (rs_check_variable_product_type($variation))) {
                    $variation = $variation->get_variation_attributes();
                    foreach ($variation as $key) {
                        return "<strong>" . $checkproduct->get_title() . "\r" . $key . "</strong>";
                    }
                }
            }
        }

        public static function get_each_product_points_value_in_cart() {
            $getpoints = do_shortcode('[rspoint]');
            $redeemconver = $getpoints / wc_format_decimal(get_option('rs_redeem_point'));
            $updatedvalue = $redeemconver * wc_format_decimal(get_option('rs_redeem_point_value'));
            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
            return get_woocommerce_formatted_price(round($updatedvalue, $roundofftype));
        }

        public static function get_balance_redeem_points_to_display_in_msg() {
            global $woocommerce;
            $user_ID = get_current_user_id();
            $getinfousernickname = get_user_by('id', $user_ID);
            $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
            $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
            $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
            if (isset($woocommerce->cart->coupon_discount_amounts["$usernickname"])) {
                $total = $woocommerce->cart->coupon_discount_amounts[$usernickname];
                $tax = $woocommerce->cart->coupon_discount_tax_amounts[$usernickname];
                if (get_option('woocommerce_tax_display_cart') == 'incl' && get_option('woocommerce_prices_include_tax') == 'no') {
                    $total = $total + $tax;
                } elseif (get_option('woocommerce_tax_display_cart') == 'incl' && get_option('woocommerce_prices_include_tax') == 'yes') {
                    $total = $total + $tax;
                }
                $current_conversion = wc_format_decimal(get_option('rs_redeem_point'));
                $point_amount = wc_format_decimal(get_option('rs_redeem_point_value'));
                $total = $total * $current_conversion;
                $total = $total / $point_amount;
                $myrewardpoint = RSPointExpiry::get_sum_of_total_earned_points($user_ID);
                $majorpoint = $myrewardpoint - $total;
                if ($majorpoint < 0) {
                    $majorpoint = '0';
                }
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                return round($majorpoint, $roundofftype);
            }
            if (isset($woocommerce->cart->coupon_discount_amounts["$auto_redeem_name"])) {
                $total = $woocommerce->cart->coupon_discount_amounts[$auto_redeem_name];
                $tax = $woocommerce->cart->coupon_discount_tax_amounts[$auto_redeem_name];
                if (get_option('woocommerce_tax_display_cart') == 'incl' && get_option('woocommerce_prices_include_tax') == 'no') {
                    $total = $total + $tax;
                } elseif (get_option('woocommerce_tax_display_cart') == 'incl' && get_option('woocommerce_prices_include_tax') == 'yes') {
                    $total = $total + $tax;
                }
                $current_conversion = wc_format_decimal(get_option('rs_redeem_point'));
                $point_amount = wc_format_decimal(get_option('rs_redeem_point_value'));
                $total = $total * $current_conversion;
                $total = $total / $point_amount;
                $myrewardpoint = RSPointExpiry::get_sum_of_total_earned_points($user_ID);
                $majorpoint = $myrewardpoint - $total;
                if ($majorpoint < 0) {
                    $majorpoint = '0';
                }
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                return round($majorpoint, $roundofftype);
            }
        }

        public static function validation_in_my_cart() {
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function () {

                    jQuery('#mainsubmi').click(function () {

                        var float_value_current_points = parseFloat('<?php
            $currentuserpoints = RSPointExpiry::get_sum_of_total_earned_points(get_current_user_id());
            echo $currentuserpoints;
            ?>');
                        float_value_current_points = Math.round(float_value_current_points * 100) / 100;
                        var float_value_minimum_redeeming_points = parseFloat('<?php echo get_option("rs_minimum_redeeming_points"); ?>');
                        float_value_minimum_redeeming_points = Math.round(float_value_minimum_redeeming_points * 100) / 100;
                        var float_value_maximum_redeeming_points = parseFloat('<?php echo get_option("rs_maximum_redeeming_points"); ?>');
                        float_value_maximum_redeeming_points = Math.round(float_value_maximum_redeeming_points * 100) / 100;
            <?php if (get_option('rs_redeem_field_type_option') == '1' || get_option('rs_redeem_field_type_option_checkout') == '1') { ?>
                            var getvalue = jQuery('#rs_apply_coupon_code_field').val();
                            if (getvalue === '') {
                                jQuery('.rs_warning_message').html('<?php echo addslashes(get_option('rs_redeem_empty_error_message')); ?>');
                                return false;
                            } else if (jQuery.isNumeric(getvalue) == false) {
                                jQuery('.rs_warning_message').html('<?php echo addslashes(get_option('rs_redeem_character_error_message')); ?>');
                                return false;
                            } else if (getvalue > float_value_current_points) {
                                jQuery('.rs_warning_message').html('<?php echo addslashes(get_option('rs_redeem_max_error_message')); ?>');
                                return false;
                            } else if (jQuery.isNumeric(getvalue) == true) {
                                if (getvalue < 0) {
                                    jQuery('.rs_warning_message').html('<?php echo addslashes(get_option('rs_redeem_character_error_message')); ?>');
                                    return false;
                                }
                            }
                <?php if (get_option('rs_minimum_redeeming_points') == (get_option('rs_maximum_redeeming_points'))) { ?>
                                if (getvalue < float_value_minimum_redeeming_points) {
                                    jQuery('.rs_warning_message').html('<?php echo do_shortcode(addslashes(get_option("rs_minimum_and_maximum_redeem_point_error_message"))); ?>');
                                    return false;
                                } else if (getvalue > float_value_maximum_redeeming_points) {
                                    jQuery('.rs_warning_message').html('<?php echo do_shortcode(addslashes(get_option("rs_minimum_and_maximum_redeem_point_error_message"))); ?>');
                                    return false;
                                }
                <?php } ?>
                <?php if (get_option('rs_minimum_redeeming_points') != '') { ?>
                                if (getvalue < float_value_minimum_redeeming_points) {
                                    jQuery('.rs_warning_message').html('<?php echo do_shortcode(addslashes(get_option("rs_minimum_redeem_point_error_message"))); ?>');
                                    return false;
                                }
                <?php } ?>

                <?php if (get_option('rs_maximum_redeeming_points') != '') { ?>
                                if (getvalue > float_value_maximum_redeeming_points) {
                                    jQuery('.rs_warning_message').html('<?php echo do_shortcode(addslashes(get_option("rs_maximum_redeem_point_error_message"))); ?>');
                                    return false;
                                }
                <?php } ?>

            <?php } else { ?>


                            var getvalue = jQuery('#rs_apply_coupon_code_field').val();
                <?php if (get_option('rs_minimum_redeeming_points') == (get_option('rs_maximum_redeeming_points'))) { ?>
                                if (getvalue < float_value_minimum_redeeming_points) {
                                    jQuery('.rs_warning_message').html('<?php echo do_shortcode(addslashes(get_option("rs_minimum_and_maximum_redeem_point_error_message_for_buttontype"))); ?>');
                                    return false;
                                } else if (getvalue > float_value_maximum_redeeming_points) {
                                    jQuery('.rs_warning_message').html('<?php echo do_shortcode(addslashes(get_option("rs_minimum_and_maximum_redeem_point_error_message_for_buttontype"))); ?>');
                                    return false;
                                }
                <?php } ?>
                <?php if (get_option('rs_minimum_redeeming_points') != '') { ?>
                                if (getvalue < float_value_minimum_redeeming_points) {
                                    jQuery('.rs_warning_message').html('<?php echo do_shortcode(addslashes(get_option("rs_minimum_redeem_point_error_message_for_button_type"))); ?>');
                                    return false;
                                }
                <?php } ?>

                <?php if (get_option('rs_maximum_redeeming_points') != '') { ?>
                                if (getvalue > float_value_maximum_redeeming_points) {
                                    jQuery('.rs_warning_message').html('<?php echo do_shortcode(addslashes(get_option("rs_maximum_redeem_point_error_message_for_button_type"))); ?>');
                                    return false;
                                }
                <?php } ?>

            <?php } ?>
                    });
                });
            </script>
            <?php
        }

        public static function get_minimum_redeeming_points_value() {
            return get_option('rs_minimum_redeeming_points');
        }

        public static function get_maximum_redeeming_points_value() {
            return get_option('rs_maximum_redeeming_points');
        }

        public static function get_minimum_and_maximum_redeeming_points_value() {
            return get_option('rs_minimum_redeeming_points');
        }

        public static function display_redeem_min_max_points_buttons_on_cart_page() {
            if (is_user_logged_in()) {
                global $woocommerce;
                foreach ($woocommerce->cart->cart_contents as $item) {
                    $product_id = $item['variation_id'] != 0 ? $item['variation_id'] : $item['product_id'];
                    $type[] = check_display_price_type($product_id);
                    $enable = calculate_point_price_for_products($product_id);
                    if ($enable[$product_id] != '') {
                        $cart_object[] = $enable[$product_id];
                    }
                }

                if (!empty($cart_object)) {
                    if (get_option('rs_show_hide_message_errmsg_for_point_price_coupon') == '1') {
                        $message = get_option('rs_errmsg_for_redeem_in_point_price_prt');
                        ?>
                        <div class="woocommerce-info"><?php echo $message; ?></div>
                        <?php
                    }
                }
                if (get_option('rs_redeem_field_type_option') == '2') {
                    global $woocommerce;
                    $minimum_cart_total_redeem = get_option('rs_minimum_cart_total_points');
                    $maximum_cart_total_redeem = get_option('rs_maximum_cart_total_points');
                    $current_carttotal_amount = $woocommerce->cart->total;
                    if ($minimum_cart_total_redeem != '' && $maximum_cart_total_redeem != '') {
                        if ($current_carttotal_amount >= $minimum_cart_total_redeem && $current_carttotal_amount <= $maximum_cart_total_redeem) {
                            self::display_redeem_points_buttons_on_cart_page();
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
                            self::display_redeem_points_buttons_on_cart_page();
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
                            self::display_redeem_points_buttons_on_cart_page();
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
                        self::display_redeem_points_buttons_on_cart_page();
                    }
                }
            }
        }

        public static function display_redeem_points_buttons_on_cart_page() {
            $totalselectedvalue = array();
            global $woocommerce;
            if (is_user_logged_in()) {
                $type = array();
                $points_for_include_product = '';
                $userid = get_current_user_id();
                $banning_type = FPRewardSystem::check_banning_type($userid);
                if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
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
                                        if ($item['line_subtotal_tax']) {
                                            if (get_option('woocommerce_prices_include_tax') == 'yes' && get_option('woocommerce_tax_display_cart') == 'incl') {
                                                $totalselectedvalue[] = $item['line_subtotal'] + $item['line_subtotal_tax'];
                                            } elseif (get_option('woocommerce_prices_include_tax') == 'no' && get_option('woocommerce_tax_display_cart') == 'incl') {
                                                $totalselectedvalue[] = $item['line_subtotal'] + $item['line_subtotal_tax'];
                                            } else {
                                                $totalselectedvalue[] = $item['line_subtotal'];
                                            }
                                        } else {
                                            $totalselectedvalue[] = $item['line_subtotal'];
                                        }
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
                                                    if ($item['line_subtotal_tax']) {
                                                        if (get_option('woocommerce_prices_include_tax') == 'yes' && get_option('woocommerce_tax_display_cart') == 'incl') {
                                                            $totalselectedvalue[$productid] = $item['line_subtotal'] + $item['line_subtotal_tax'];
                                                        } elseif (get_option('woocommerce_prices_include_tax') == 'no' && get_option('woocommerce_tax_display_cart') == 'incl') {
                                                            $totalselectedvalue[$productid] = $item['line_subtotal'] + $item['line_subtotal_tax'];
                                                        }
                                                    } else {
                                                        $totalselectedvalue[$productid] = $item['line_subtotal'];
                                                    }
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
                                                if ($item['line_subtotal_tax']) {
                                                    if (get_option('woocommerce_prices_include_tax') == 'yes' && get_option('woocommerce_tax_display_cart') == 'incl') {
                                                        $totalselectedvalue[$productid] = $item['line_subtotal'] + $item['line_subtotal_tax'];
                                                    } elseif (get_option('woocommerce_prices_include_tax') == 'no' && get_option('woocommerce_tax_display_cart') == 'incl') {
                                                        $totalselectedvalue[$productid] = $item['line_subtotal'] + $item['line_subtotal_tax'];
                                                    }
                                                } else {
                                                    $totalselectedvalue[$productid] = $item['line_subtotal'];
                                                }
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
                    if ($user_current_points > 0) {
                        if (get_user_meta($getuserid, 'rsfirsttime_redeemed', true) != '1') {
                            if ($user_current_points >= get_option("rs_first_time_minimum_user_points")) {
                                if (get_option('rs_redeem_field_type_option') == '2') {
                                    $getuserid = get_current_user_id();
                                    $user_current_points = RSPointExpiry::get_sum_of_total_earned_points($getuserid);

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
                                    $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem');
                                    $cart_total_in_amount = $limitation_percentage_for_redeeming / 100;
                                    $updated_cart_total_in_amount = $cart_total_in_amount * $current_carttotal_amount;
                                    $redeem_conversion = wc_format_decimal(get_option('rs_redeem_point'));
                                    $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                                    $points_for_redeem_in_points = $updated_cart_total_in_amount * $redeem_conversion;
                                    $updated_points_for_redeeming = $points_for_redeem_in_points / $points_conversion_value;
                                    $cartpoints_string_to_replace = "[cartredeempoints]";
                                    $currency_symbol_string_to_find = "[currencysymbol]";
                                    $currency_value_string_to_find = "[pointsvalue]";
                                    if ($user_current_points >= $updated_points_for_redeeming) {
                                        $redeem_button_message_more = get_option('rs_redeeming_button_option_message');
                                        $percentage_string_to_replace = "[redeempercent]";
                                        $currency_value_string_to_find = "[pointsvalue]";
                                        $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                                        $points_currency_value = $updated_cart_total_in_amount;
                                        $points_currency_amount_to_replace = $updated_points_for_redeeming;
                                        $points_for_redeeming = $updated_points_for_redeeming;
                                        if (get_option('rs_apply_redeem_basedon_cart_or_product_total') == '2') {
                                            if ($points_for_include_product != '') {
                                                $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem');
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
                                        $redeem_button_message_replaced_first = str_replace($currency_value_string_to_find, $currency_symbol_string_to_replace, $redeem_button_message_more);
                                        $redeem_button_message_replaced_second = str_replace($currency_symbol_string_to_find, "", $redeem_button_message_replaced_first);
                                        $redeem_button_message_replaced_third = str_replace($cartpoints_string_to_replace, $points_for_redeeming, $redeem_button_message_replaced_second);
                                    } else {

                                        $points_for_redeeming = $user_current_points;

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
                                                    $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem');
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
                                        $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                                        $points_currency_value = $points_for_redeeming / $redeem_conversion;
                                        $points_currency_amount_to_replace = $points_currency_value * $points_conversion_value;
                                        $currency_symbol_string_to_replace = get_woocommerce_formatted_price($points_currency_amount_to_replace);
                                        $redeem_button_message_replaced_first = str_replace($currency_symbol_string_to_find, "", $redeem_button_message_more);
                                        $redeem_button_message_replaced_second = str_replace($currency_value_string_to_find, $currency_symbol_string_to_replace, $redeem_button_message_replaced_first);
                                        $redeem_button_message_replaced_third = str_replace($cartpoints_string_to_replace, $points_for_redeeming, $redeem_button_message_replaced_second);
                                    }
                                    $minimum_cart_total_redeem = get_option('rs_minimum_cart_total_points');

                                    if (get_option('woocommerce_prices_include_tax') === 'yes') {
                                        $cart_subtotal_redeem_amount = $woocommerce->cart->subtotal_ex_tax;
                                    } else {
                                        $cart_subtotal_redeem_amount = $woocommerce->cart->subtotal;
                                    }

                                    if ($cart_subtotal_redeem_amount >= $minimum_cart_total_redeem) {
                                        $user_ID = get_current_user_id();
                                        $getinfousernickname = get_user_by('id', $user_ID);
                                        $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                                        $array = $woocommerce->cart->get_applied_coupons();
                                        $auto_redeem_name = 'auto_redeem_' . strtolower($couponcodeuserlogin);
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
                                                            <input id="rs_apply_coupon_code_field" class="input-text" type="hidden"  value="<?php echo $points_for_redeeming; ?> " name="rs_apply_coupon_code_field">
                                                            <input class="<?php echo get_option('rs_extra_class_name_apply_reward_points'); ?>" type="submit" id='mainsubmi' value="<?php echo get_option('rs_redeem_field_submit_button_caption'); ?>" name="rs_apply_coupon_code1">
                                                        </div>
                                                    </form>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($user_current_points >= get_option("rs_minimum_user_points_to_redeem")) {
                                if (get_option('rs_redeem_field_type_option') == '2') {
                                    $getuserid = get_current_user_id();
                                    $user_current_points = RSPointExpiry::get_sum_of_total_earned_points($getuserid);
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
                                    $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem');
                                    $cart_total_in_amount = $limitation_percentage_for_redeeming / 100;
                                    $updated_cart_total_in_amount = $cart_total_in_amount * $current_carttotal_amount;
                                    $redeem_conversion = wc_format_decimal(get_option('rs_redeem_point'));
                                    $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                                    $points_for_redeem_in_points = $updated_cart_total_in_amount * $redeem_conversion;
                                    $updated_points_for_redeeming = $points_for_redeem_in_points / $points_conversion_value;
                                    $cartpoints_string_to_replace = "[cartredeempoints]";
                                    $currency_symbol_string_to_find = "[currencysymbol]";
                                    $currency_value_string_to_find = "[pointsvalue]";
                                    if ($user_current_points >= $updated_points_for_redeeming) {
                                        $redeem_button_message_more = get_option('rs_redeeming_button_option_message');
                                        $currency_value_string_to_find = "[pointsvalue]";
                                        $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                                        $points_currency_value = $updated_cart_total_in_amount;
                                        $points_currency_amount_to_replace = $updated_points_for_redeeming;
                                        $points_for_redeeming = $updated_points_for_redeeming;
                                        if (get_option('rs_apply_redeem_basedon_cart_or_product_total') == '2') {
                                            if ($points_for_include_product != '') {
                                                $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem');
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

                                        $redeem_button_message_replaced_first = str_replace($currency_value_string_to_find, $currency_symbol_string_to_replace, $redeem_button_message_more);
                                        $redeem_button_message_replaced_second = str_replace($currency_symbol_string_to_find, "", $redeem_button_message_replaced_first);
                                        $redeem_button_message_replaced_third = str_replace($cartpoints_string_to_replace, $points_for_redeeming, $redeem_button_message_replaced_second);
                                    } else {
                                        $points_for_redeeming = $user_current_points;
                                        if (get_option('rs_apply_redeem_basedon_cart_or_product_total') == '2') {
                                            if ($points_for_include_product != '') {
                                                $points_for_redeeming1 = $points_for_include_product / $points_conversion_value;
                                                if ($user_current_points > $points_for_redeeming1) {
                                                    $points_for_redeeming = $points_for_redeeming1;
                                                    $points_currency_value = $getsumofselectedproduct;
                                                    $limitation_percentage_for_redeeming = get_option('rs_percentage_cart_total_redeem');
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

                                        $redeem_button_message_more = get_option('rs_redeeming_button_option_message_checkout');
                                        $points_conversion_value = wc_format_decimal(get_option('rs_redeem_point_value'));
                                        $points_currency_value = $points_for_redeeming / $redeem_conversion;
                                        $points_currency_amount_to_replace = $points_currency_value * $points_conversion_value;
                                        $currency_symbol_string_to_replace = get_woocommerce_formatted_price($points_currency_amount_to_replace);
                                        $redeem_button_message_replaced_first = str_replace($currency_symbol_string_to_find, "", $redeem_button_message_more);
                                        $redeem_button_message_replaced_second = str_replace($currency_value_string_to_find, $currency_symbol_string_to_replace, $redeem_button_message_replaced_first);
                                        $redeem_button_message_replaced_third = str_replace($cartpoints_string_to_replace, $points_for_redeeming, $redeem_button_message_replaced_second);
                                    }
                                    $minimum_cart_total_redeem = get_option('rs_minimum_cart_total_points');
                                    if (get_option('woocommerce_prices_include_tax') === 'yes') {
                                        $cart_subtotal_redeem_amount = $woocommerce->cart->subtotal_ex_tax;
                                    } else {
                                        $cart_subtotal_redeem_amount = $woocommerce->cart->subtotal;
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
                                                        <div class="woocommerce-info sumo_reward_points_cart_apply_discount"><?php echo $redeem_button_message_replaced_third; ?>
                                                            <input id="rs_apply_coupon_code_field" class="input-text" type="hidden"  value="<?php echo $points_for_redeeming; ?> " name="rs_apply_coupon_code_field">
                                                            <input class="<?php echo get_option('rs_extra_class_name_apply_reward_points'); ?>" type="submit" id='mainsubmi' value="<?php echo get_option('rs_redeem_field_submit_button_caption'); ?>" name="rs_apply_coupon_code1" />
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
                }
            }
        }

        public static function change_coupon_label($link, $coupon) {
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                $coupon_obj = rs_get_coupon_obj($coupon);
                $couponcode = $coupon_obj['coupon_code'];
                if (is_string($coupon))
                    $coupon = new WC_Coupon($coupon);
                $user_ID = get_current_user_id();
                $getinfousernickname = get_user_by('id', $user_ID);
                $couponcodeuserlogin = is_object($getinfousernickname) ? $getinfousernickname->user_login : 'Guest';
                if (strtolower($couponcode) == ('sumo_' . strtolower($couponcodeuserlogin)) || strtolower($couponcode) == 'auto_redeem_' . strtolower($couponcodeuserlogin)) {
                    $newcoupon = get_option('rs_coupon_label_message');
                    $link = ' ' . $newcoupon;
                }
            }
            return $link;
        }

    }

    RSFunctionForCart::init();
}