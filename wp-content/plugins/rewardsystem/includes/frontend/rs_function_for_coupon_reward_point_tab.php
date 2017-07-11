<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForCouponRewardPoints')) {

    class RSFunctionForCouponRewardPoints {

        public static function init() {

            add_action('woocommerce_before_cart_table', array(__CLASS__, 'display_message_coupon_reward_points'), 999);

            add_action('woocommerce_before_checkout_form', array(__CLASS__, 'display_message_coupon_reward_points'), 999);
        }        

        public static function find_coupon_values($couponcode, $applied_coupons_cart) {

            if (is_array($applied_coupons_cart)) {
                if (in_array($couponcode, $applied_coupons_cart)) {
                    return "1";
                }
            }
        }        

        public static function display_message_coupon_reward_points() {
            global $woocommerce;
            if (get_option('rs_choose_priority_level_selection_coupon_points') == '1') {
                $coupons_for_points_rule_list = multi_dimensional_descending_sort_coupon_points(get_option('rewards_dynamic_rule_couponpoints'), 'reward_points');
            } else {
                $coupons_for_points_rule_list = RSMemberFunction::multi_dimensional_sort(get_option('rewards_dynamic_rule_couponpoints'), 'reward_points');
            }
            $getthedatas = array();
            if (is_array($coupons_for_points_rule_list) && !empty($coupons_for_points_rule_list)) {
                foreach ($coupons_for_points_rule_list as $key => $value) {
                    if (isset($value['coupon_codes'])) {
                        if (!in_array($value['coupon_codes'], $getthedatas)) {
                            $getthedatas[$key] = $value['coupon_codes'];
                        }
                    }
                }
            }
            $c = array();
            if (is_array($getthedatas) && !empty($getthedatas)) {
                foreach ($getthedatas as $key => $mainvalue) {
                    $c[] = $coupons_for_points_rule_list[$key];
                }
            }


            if (is_array($c) && !empty($c)) {
                foreach ($c as $coupons_for_points_each_rule) {
                    $rule_created_coupons_list = $coupons_for_points_each_rule["coupon_codes"];

                    $rule_created_coupons_points_list = $coupons_for_points_each_rule["reward_points"];
                    if (is_array($rule_created_coupons_list) && !empty($rule_created_coupons_list)) {
                        foreach ($rule_created_coupons_list as $separate_rule_coupons) {
                            $newfunctionchecker = self::find_coupon_values($separate_rule_coupons, $woocommerce->cart->applied_coupons);
                            if ($newfunctionchecker == '1') {
                                $coupon_name_shortcode_to_find = "[coupon_name]";
                                $coupon_name_shortcode_to_replace = $separate_rule_coupons;
                                $coupon_name_shortcode_replaced = str_replace($coupon_name_shortcode_to_find, $coupon_name_shortcode_to_replace, get_option('rs_coupon_applied_reward_success'));
                                $coupon_reward_points_shortcode_to_find = "[coupon_rewardpoints]";
                                $coupon_reward_points_shortcode_to_replace = $rule_created_coupons_points_list;
                                $coupon_reward_points_shortcode_replaced = str_replace($coupon_reward_points_shortcode_to_find, $coupon_reward_points_shortcode_to_replace, $coupon_name_shortcode_replaced);
                                ?>
                                <div class="woocommerce-message">
                                <?php echo $coupon_reward_points_shortcode_replaced; ?>
                                </div>
                                    <?php
                                }
                            }
                        }
                    }
                }
            }

        }

        RSFunctionForCouponRewardPoints::init();
    }