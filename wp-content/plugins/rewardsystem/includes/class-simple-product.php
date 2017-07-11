<?php
/*
 * Simple Product Functionality
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionforSimpleProduct')) {

    class RSFunctionforSimpleProduct {

        public static function init() {
            add_filter('woocommerce_get_price_html', array(__CLASS__, 'display_reward_point_msg_for_product'), 10, 2);
            add_shortcode('rewardpoints', array(__CLASS__, 'add_shortcode_function_for_rewardpoints_of_simple'));

            add_action('woocommerce_before_single_product', array(__CLASS__, 'display_purchase_message_for_simple_in_single_product_page'));
            add_shortcode('equalamount', array(__CLASS__, 'get_redeem_conversion_value'));

            add_filter('woocommerce_variation_sale_price_html', array(__CLASS__, 'display_point_price_in_variable_product'), 99, 2);
            add_filter('woocommerce_variation_price_html', array(__CLASS__, 'display_point_price_in_variable_product'), 99, 2);
        }

        public static function display_reward_point_msg_for_product($price, $product) {
            global $post;
            global $woocommerce_loop;
            $related_product = false;
            $userid = get_current_user_id();
            $point_price_info = get_option('rs_label_for_point_value');
            $labelposition = get_option('rs_sufix_prefix_point_price_label');
            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
            $displaymax = '';
            $point_price_display = '';
            $banning_type = FPRewardSystem::check_banning_type($userid);
            $id = rs_get_id($product);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                $gettheproducts = rs_get_product_object($id);
                if ((isset($woocommerce_loop['name']) && $woocommerce_loop['name'] == 'related')) {
                    $related_product = true;
                }
                if (is_object($gettheproducts) && rs_check_variable_product_type($gettheproducts) && $related_product == false) {
                    $pointmin = self::get_point_price($id);
                    $point_price_label = str_replace("/", "", $point_price_info);
                    if (!empty($pointmin)) {
                        $displaymin = min($pointmin);
                        $displaymax = max($pointmin);
                        if ($price != '') {
                            $display = '/';
                        } else {
                            $display = '';
                        }
                        if ($labelposition == '1') {
                            $point_price_display = $display . $point_price_label . $displaymin . '-' . $point_price_label . $displaymax;
                        } else {
                            $point_price_display = $display . $displaymin . $point_price_label . '-' . $displaymax . $point_price_label;
                        }
                    }
                    if (is_shop() || is_product_category()) {                                                                        //Shop and Category Page Message for Variable Product                            
                        if (get_option('rs_enable_display_earn_message_for_variation') == 'yes') {
                            $variation_id = self::get_variation_id($id);
                            $varpointss = self::rewardpoints_of_variation($variation_id[0], $id);
                            if ($varpointss != '') {
                                $redeemingrspoints = $varpointss / wc_format_decimal(get_option('rs_redeem_point'));
                                $updatedredeemingpoints = $redeemingrspoints * wc_format_decimal(get_option('rs_redeem_point_value'));
                                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                $currency = round($varpointss, $roundofftype);
                                $message = get_option("rs_message_for_single_product_variation");
                                $earnmessage = str_replace('[variationrewardpoints]', $currency, $message);
                                // Shop Page Message for Variable Product with Gift Icon
                                return self::rs_function_to_get_msg_with_gift_icon_for_variable($earnmessage, $price, $point_price_display);
                            }
                        } else {
                            return self::rs_function_to_get_msg_with_gift_icon_for_variable($earnmessage = '', $price, $point_price_display);
                        }
                    }

                    if (is_product() || is_page()) {                                                                                   //Single Product and Custom Page Message for Variable Product                            
                        if (get_option('rs_show_hide_message_for_variable_in_single_product_page') == '1') {
                            // Shop Page Message for Variable Product with Gift Icon
                            if (get_option('rs_enable_display_earn_message_for_variation_single_product') == 'yes') {
                                $variation_id = self::get_variation_id($id);
                                $varpointss = self::rewardpoints_of_variation($variation_id[0], $id);                                
                                if ($varpointss != '') {
                                   
                                    return self::rs_function_to_get_msg_with_gift_icon_for_variable($earnmessage='', $price, $point_price_display);
                                }
                            } else {
                                return self::rs_function_to_get_msg_with_gift_icon_for_variable($earnmessage = '', $price, $point_price_display);
                            }
                        }
                    }
                } else {
                    $getshortcodevalues = do_shortcode('[rewardpoints]');
                    $enabledpoints = calculate_point_price_for_products($id);
                    $point_price = $enabledpoints[$id];
                    $point_price = round($point_price, $roundofftype);
                    $point_price_type = check_display_price_type($id);
                    $enable_reward_points = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($id, '_rewardsystemcheckboxvalue');
                    $replace = str_replace("/", "", $point_price_info);
                    if ($labelposition == '1') {
                        $point_price_info = $point_price_info . $point_price;
                    } else {
                        $point_price_info = '/' . $point_price . $replace;
                    }
                    if ($enable_reward_points == 'yes' && $getshortcodevalues > 0) {
                        if (is_shop() || is_product_category()) {                                        //Shop and Category Page Message for Simple Product
                            $earnmessage = do_shortcode(get_option("rs_message_in_shop_page_for_simple"));

                            // Shop Page Message for Simple Product with Gift Icon
                            $earnpoint_msg_in_shop = self::rs_function_to_get_msg_with_gift_icon($earnmessage);
                            $msg_position = get_option('rs_message_position_for_simple_products_in_shop_page');
                            //Shop Page Message for Simple Product for User                            
                            if (is_user_logged_in()) {
                                if (get_option('rs_show_hide_message_for_simple_in_shop') == '1') {

                                    //Function to Return Shop Page Message
                                    return self::rs_function_to_get_earnpoint_msg($point_price_type, $point_price, $price, $point_price_info, $earnpoint_msg_in_shop, $msg_position);
                                }
                            } else {
                                //Shop Page Message for Simple Product for Guest
                                if (get_option('rs_show_hide_message_for_simple_in_shop_guest') == '1') {

                                    //Function to Return Shop Page Message
                                    return self::rs_function_to_get_earnpoint_msg($point_price_type, $point_price, $price, $point_price_info, $earnpoint_msg_in_shop, $msg_position);
                                }
                            }
                        }

                        if (is_product() || is_page()) {                              //Single Product and Custom Page Message for Simple Product
                            if (get_option('rs_show_hide_message_for_shop_archive_single') == '1') {
                                $earnmessage = do_shortcode(get_option("rs_message_in_single_product_page"));
                                // Single Product and Custom Page Message for Simple Product with Gift Icon
                                $earnpoint_msg_in_shop = self::rs_function_to_get_msg_with_gift_icon($earnmessage);
                                $msg_position = get_option('rs_message_position_in_single_product_page_for_simple_products');
                                //Function to Return Single Product and Custom Page Message
                                return self::rs_function_to_get_earnpoint_msg($point_price_type, $point_price, $price, $point_price_info, $earnpoint_msg_in_shop, $msg_position);
                            }
                        }
                    }

                    if (get_option('rs_enable_disable_point_priceing') == '1') {                                                   //Altered Price when Point Price
                        if ($point_price_type == '2') {
                            $point_price_info = str_replace("/", "", $point_price_info);
                            return $point_price_info;
                        } else {
                            if ($point_price != '') {
                                return $price . '<span class="point_price_label">' . $point_price_info;
                            }
                        }
                    }
                }
            }
            return $price;
        }

        public static function rs_function_to_get_earnpoint_msg($point_price_type, $point_price, $price, $point_price_info, $earnpoint_msg_in_shop, $msg_position) {
            if (get_option('rs_enable_disable_point_priceing') == '1') {                          //Shop Page Message for Simple Product when Points Price is Enabled
                if ($point_price_type == '2') {
                    $point_price_info = str_replace("/", "", $point_price_info);
                    if ($msg_position == '1') {    //Position of Shop Page Message for Simple Product - Before
                        return "<small>" . $earnpoint_msg_in_shop . "</small> <br>" . $point_price_info;
                    } else {                                                                            //Position of Shop Page Message for Simple Product - After
                        return "<small>" . $point_price_info . "<br>" . $earnpoint_msg_in_shop . "</small><br>";
                    }
                } else {
                    if ($point_price != '') {
                        if ($msg_position == '1') {    //Position of Shop Page Message for Simple Product - Before
                            return $earnpoint_msg_in_shop . "<br>" . $price . '<span class="point_price_label">' . $point_price_info;
                        } else {                                                                              //Position of Shop Page Message for Simple Product - After
                            return $price . '<span class="point_price_label">' . $point_price_info . "<br><small>" . $earnpoint_msg_in_shop . "</small>";
                        }
                    } else {
                        if ($msg_position == '1') {    //Position of Shop Page Message for Simple Product - Before
                            return $earnpoint_msg_in_shop . "<br>" . $price;
                        } else {                                                                              //Position of Shop Page Message for Simple Product - After
                            return $price . "<br>" . $earnpoint_msg_in_shop;
                        }
                    }
                }
            } else {                                                                              //Shop Page Message for Simple Product when Points Price is Disabled
                if ($msg_position == '1') {    //Position of Shop Page Message for Simple Product - Before
                    return $earnpoint_msg_in_shop . "<br>" . $price;
                } else {                                                                              //Position of Shop Page Message for Simple Product - After
                    return $price . "<br>" . $earnpoint_msg_in_shop;
                }
            }
        }

        public static function rs_function_to_get_msg_with_gift_icon($earnmessage) {
            if (get_option('_rs_enable_disable_gift_icon') == '1') {
                if (get_option('rs_image_url_upload') != '') {
                    $earnpoint_msg_in_shop = "<span class='simpleshopmessage'><img src=" . get_option('rs_image_url_upload') . " style='width:16px;height:16px;display:inline;' />&nbsp; " . $earnmessage . "</span>";
                } else {
                    $earnpoint_msg_in_shop = "<span class='simpleshopmessage'>" . $earnmessage . "</span>";
                }
            } else {
                $earnpoint_msg_in_shop = "<span class='simpleshopmessage'>" . $earnmessage . "</span>";
            }
            return $earnpoint_msg_in_shop;
        }

         public static function rs_function_to_get_msg_with_gift_icon_for_variable($earnmessage, $price, $point_price_display) {
               $image = "<img class='gift_icon' src=" . get_option('rs_image_url_upload') . " style='width:16px;height:16px;display:inline;' />&nbsp;";
            if ($earnmessage != '') {              
                $break = '<br>';
            } else {         
                $break = '';
                if (is_shop()|| is_product_category()) {
                    $image = '';
                }
                if (is_product() || is_page()) {
                    $break = '<br>';
                }
            }
            if (get_option('_rs_enable_disable_gift_icon') == '1') {
                if (get_option('rs_image_url_upload') != '') {
                    if (get_option('rs_message_position_in_single_product_page_for_variable_products') == '1') {
                        return $image. "<span class='variableshopmessage'>" . $earnmessage . "</span>".$break . $price . $point_price_display;
                    } else {
                        return $price . $point_price_display . '<br>'.$image . "<span class='variableshopmessage'>" . $earnmessage . "</span>";
                    }
                } else {
                    if (get_option('rs_message_position_in_single_product_page_for_variable_products') == '1') {
                        return "<span class='variableshopmessage'>" . $earnmessage . "</span>" . $break . $price . $point_price_display;
                    } else {
                        return $price . $point_price_display . $break . "<span class='variableshopmessage'>" . $earnmessage . "</span>";
                    }
                }
            } else {
                if (get_option('rs_message_position_in_single_product_page_for_variable_products') == '1') {
                    return "<span class='variableshopmessage'>" . $earnmessage . "</span>" . $break . $price . $point_price_display;
                } else {
                    return $price . $point_price_display . $break . "<span class='variableshopmessage'>" . $earnmessage . "</span>";
                }
            }
        }

        public static function get_point_price($post_id) {
            $enabledpoints1 = array();
            $args = array(
                'post_parent' => $post_id,
                'post_type' => 'product_variation',
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'fields' => 'ids',
                'post_status' => 'publish',
                'numberposts' => -1
            );
            $variation_id = get_posts($args);
            foreach ($variation_id as $key) {
                $enabledpoints = calculate_point_price_for_products($key);
                if ($enabledpoints[$key] != '') {
                    $enabledpoints1[$key] = $enabledpoints[$key];
                }
            }
            return $enabledpoints1;
        }

        public static function add_shortcode_function_for_rewardpoints_of_simple() {
            global $post;
            $restrictpoints = rs_function_to_restrict_points_for_product_which_has_saleprice($post->ID, $variationid = '');
            if ($restrictpoints == 'no') {
                $checkproducttype = rs_get_product_object($post->ID);
                if (is_shop() || is_product() || is_page() || is_product_category()) {
                    if (is_object($checkproducttype) && ($checkproducttype->is_type('simple') || ($checkproducttype->is_type('subscription')))) {
                        $item = array('qty' => '1');
                        $checklevel = 'no';
                        $reward_points = check_level_of_enable_reward_point($post->ID, $variationid = '0', $item, $checklevel, $referred_user = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
                        $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                        if (get_current_user_id() > 0) {
                            $reward_points = RSMemberFunction::user_role_based_reward_points(get_current_user_id(), $reward_points);
                        }
                        $reward_points = round($reward_points, $roundofftype);
                        return $reward_points;
                    }
                }
            }
        }

        public static function display_purchase_message_for_simple_in_single_product_page() {
            global $post;
            $order = '';
            if (is_user_logged_in()) {
                $userid = get_current_user_id();
                $banning_type = FPRewardSystem::check_banning_type($userid);
                if ($banning_type != 'earningonly' && $banning_type != 'both') {
                    $checkproducttype = rs_get_product_object($post->ID);
                    if (get_option('rs_show_hide_message_for_single_product') == '1') {
                        if (is_object($checkproducttype) && ($checkproducttype->is_type('simple') || ($checkproducttype->is_type('subscription')))) {
                            if (RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($post->ID, '_rewardsystemcheckboxvalue') == 'yes') {
                                if (RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($post->ID, '_rewardsystem_options') == '1') {
                                    $rewardpoints = do_shortcode('[rewardpoints]');
                                    if ($rewardpoints > 0) {
                                        ?>
                                        <div class="woocommerce-info"><?php echo do_shortcode(get_option('rs_message_for_single_product_point_rule')); ?></div>
                                        <?php
                                    }
                                } else {
                                    $rewardpoints = do_shortcode('[rewardpoints]');
                                    if ($rewardpoints > 0) {
                                        ?>
                                        <div class="woocommerce-info"><?php echo do_shortcode(get_option('rs_message_for_single_product_point_rule')); ?></div>
                                        <?php
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $earnmessage = do_shortcode(get_option("rs_message_for_single_product_point_rule"));
                if (get_option('rs_show_hide_message_for_single_product_guest') == '1') {
                    $checkproducttype = rs_get_product_object($post->ID);
                    if (is_object($checkproducttype) && ($checkproducttype->is_type('simple') || ($checkproducttype->is_type('subscription')))) {
                        if (RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($post->ID, '_rewardsystemcheckboxvalue') == 'yes') {
                            if (RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($post->ID, '_rewardsystem_options') == '1') {
                                $rewardpoints = do_shortcode('[rewardpoints]');
                                if ($rewardpoints > 0) {
                                    if ($earnmessage != '') {
                                        ?>
                                        <div class="woocommerce-info"><?php echo do_shortcode(get_option('rs_message_for_single_product_point_rule')); ?></div>
                                        <?php
                                    }
                                }
                            } else {
                                $rewardpoints = do_shortcode('[rewardpoints]');
                                if ($earnmessage != '') {
                                    if ($rewardpoints > 0) {
                                        ?>
                                        <div class="woocommerce-info"><?php echo do_shortcode(get_option('rs_message_for_single_product_point_rule')); ?></div>
                                        <?php
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        public static function get_redeem_conversion_value() {
            if (get_current_user_id() > 0) {
                $singleproductvalue = do_shortcode('[rewardpoints]');
            } else {
                $singleproductvalue = do_shortcode('[rewardpoints]');
            }
            $newvalue = $singleproductvalue / wc_format_decimal(get_option('rs_redeem_point'));
            $updatedvalue = $newvalue * wc_format_decimal(get_option('rs_redeem_point_value'));
            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
            $price = round($updatedvalue, $roundofftype);
            return get_woocommerce_formatted_price($price);
        }

        public static function display_point_price_in_variable_product($price, $object) {
            global $post;
            if (get_option('rs_enable_disable_point_priceing') == '1') {
                if (get_option('rs_enable_disable_point_priceing') == '1') {
                    $enabledpoints = calculate_point_price_for_products($object->variation_id);
                    $point_price = $enabledpoints[$object->variation_id];
                    $point_price_info = get_option('rs_label_for_point_value');
                    $typeofprice = check_display_price_type($object->variation_id);
                    $labelposition = get_option('rs_sufix_prefix_point_price_label');
                    if ($typeofprice == '2') {
                        $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                        $point_price = round($point_price, $roundofftype);
                        $replace = str_replace("/", "", $point_price_info);

                        if ($labelposition == '1') {
                            $totalamount = $replace . $point_price;
                        } else {
                            $totalamount = $point_price . $replace;
                        }
                        return $totalamount;
                    } else {
                        if ($point_price != '') {
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $point_price = round($point_price, $roundofftype);
                            if ($labelposition == '1') {
                                $totalamount = $point_price_info . $point_price;
                            } else {
                                $replace = str_replace("/", "", $point_price_info);
                                $totalamount = '/' . $point_price . $replace;
                            }
                            return $price . '<span class="point_price_label">' . $totalamount;
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

        public static function get_variation_id($post_id) {
            $args = array(
                'post_parent' => $post_id,
                'post_type' => 'product_variation',
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'fields' => 'ids',
                'post_status' => 'publish',
                'numberposts' => -1
            );
            $variation_id = get_posts($args);

            return $variation_id;
        }

        public static function rewardpoints_of_variation($variation_id, $newparentid) {
            $varpoints = '';
            $item = array('qty' => '1');
            $rewardpoints = check_level_of_enable_reward_point($newparentid, $variation_id, $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
            if (is_user_logged_in()) {
                $getpoints = RSMemberFunction::user_role_based_reward_points(get_current_user_id(), $rewardpoints);
            } else {
                $getpoints = $rewardpoints;
            }
            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
            $varpoints = round($getpoints, $roundofftype);
            return $varpoints;
        }

    }

    RSFunctionforSimpleProduct::init();
}