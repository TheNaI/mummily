<?php

class RSDiscountedPointsCalculation {

    public function __construct() {
        add_action('wp_footer', array($this, 'modified_count'));
    }

    public static function coupon_validator($product_id) {
        global $woocommerce;
        $selected_products = '';
        $discount_coupon = $woocommerce->cart->coupon_discount_amounts;
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
                    if (!empty($selectedproduct)) {
                        if (in_array($product_id, $selectedproduct)) {
                            $coupon_product_ids[$code][] = $product_id;
                            $count_of_products = 1;
                            $selected_products[$code][$product_id] = $count_of_products > 1 ? $discount_coupon[$code] / $count_of_products : $discount_coupon[$code];
                        }
                    } else {
                        $coupon_product_ids[$code][] = $product_id;
                        $count_of_products = 1;
                        $selected_products[$code][$product_id] = $count_of_products > 1 ? $discount_coupon[$code] / $count_of_products : $discount_coupon[$code];
                    }
                }
            } else {               
                if ($discount_type == 'fixed_cart') {
                    if (!empty($selectedproduct)) {
                        if (in_array($product_id, $selectedproduct)) {
                            $coupon_product_ids[$code][] = $product_id;
                            $count_of_products = 1;
                            $selected_products[$code][$product_id] = $count_of_products > 1 ? $discount_coupon[$code] / $count_of_products : $discount_coupon[$code];
                        }
                    } else {
                        $coupon_product_ids[$code][] = $product_id;
                        $count_of_products = 1;
                        $selected_products[$code][$product_id] = $count_of_products > 1 ? $discount_coupon[$code] / $count_of_products : $discount_coupon[$code];
                        WC()->session->set('current_count', $count_of_products);
                    }
                } else if ($discount_type == 'percent_product') {
                    if (!empty($selectedproduct)) {
                        if (in_array($product_id, $selectedproduct)) {
                            $coupon_product_ids[$code][] = $product_id;
                            $count_of_products = 1;
                            $selected_products[$code][$product_id] = $count_of_products > 1 ? $discount_coupon[$code] / $count_of_products : $discount_coupon[$code];
                        }
                    } else {
                        $coupon_product_ids[$code][] = $product_id;
                        $count_of_products = 1;

                        $selected_products[$code][$product_id] = $count_of_products > 1 ? $discount_coupon[$code] / $count_of_products : $discount_coupon[$code];
                    }
                } else if ($discount_type == 'fixed_product') {
                    if (!empty($selectedproduct)) {
                        if (in_array($product_id, $selectedproduct)) {
                            $coupon_product_ids[$code][] = $product_id;
                            $count_of_products = 1;
                            $selected_products[$code][$product_id] = $count_of_products > 1 ? $discount_coupon[$code] / $count_of_products : $discount_coupon[$code];
                        }
                    } else {
                        $coupon_product_ids[$code][] = $product_id;
                        $count_of_products = 1;

                        $selected_products[$code][$product_id] = $count_of_products > 1 ? $discount_coupon[$code] / $count_of_products : $discount_coupon[$code];
                    }
                } else if ($discount_type = 'percent') {
                    if (!empty($selectedproduct)) {
                        if (in_array($product_id, $selectedproduct)) {
                            $coupon_product_ids[$code][] = $product_id;
                            $count_of_products = 1;

                            $selected_products[$code][$product_id] = $count_of_products > 1 ? $discount_coupon[$code] / $count_of_products : $discount_coupon[$code];
                        }
                    } else {
                        $coupon_product_ids[$code][] = $product_id;
                        $count_of_products = 1;

                        $selected_products[$code][$product_id] = $count_of_products > 1 ? $discount_coupon[$code] / $count_of_products : $discount_coupon[$code];
                    }
                }
            }
        }
        return $selected_products;
    }

    public static function coupon_included_products($product_ids, $coupon_code) {
        global $woocommerce;
        foreach ($woocommerce->cart->cart_contents as $cart_details) {
            $product_id = $cart_details['product_id'] != '' ? $cart_details['product_id'] : $cart_details['variation_id'];
            if (in_array($product_id, $product_ids)) {

                $coupon_product_ids[] = $cart_details['line_subtotal'];
            }
        }
        $coupon_product_ids = array_sum($coupon_product_ids);

        return $coupon_product_ids;
    }

    public static function coupon_points_conversion($product_id, $points) {
        $coupon_amounts = self::coupon_validator($product_id);
        $newpoints = $points;
        $conversions = array();
        if (!empty($coupon_amounts) && is_array($coupon_amounts)) {
            foreach ($coupon_amounts as $key => $value) {
                if ($newpoints > 0) {
                    $c_amount = $value[$product_id];
                    $coupon = new WC_Coupon($key);
                    $coupon_obj = rs_get_coupon_obj($coupon);
                    $selectedproduct = $coupon_obj['product_ids'];
                    if (!empty($selectedproduct)) {
                        $conversion = $c_amount / self::coupon_included_products($selectedproduct, $key);
                    } else {
                        $conversion = $c_amount / RSFunctionForCart::get_product_price_in_cart();
                    }
                    $conversion = $conversion * $newpoints;
                    if ($newpoints > $conversion) {
                        $conversions[] = $newpoints - $conversion;
                    } else {
                        $conversions[] = 0;
                    }
                    if ($newpoints > $conversion) {
                        $newpoints = $newpoints - $conversion;
                    } else {
                        $newpoints = 0;
                    }
                }
            }
            return end($conversions);
        }
        return $newpoints;
    }

    public static function moified_points_for_products_in_cart() {
        global $woocommerce;

        $modified_points_updated = array();
        $original_points_array = RSFunctionForCart::original_points_for_product_in_cart();
        if (!empty($original_points_array)) {
            foreach ($original_points_array as $product_id => $points) {
                $modified_points = self::coupon_points_conversion($product_id, $points);

                if ($modified_points != 0) {
                    $modified_points_updated[$product_id] = $modified_points;
                }
            }
        }

        return $original_points_array;
    }

    public static function moified_points_count_in_cart() {
        $count = count(self::moified_points_for_products_in_cart());
        WC()->session->__unset('modified_count');

        return $count;
    }

    public static function modified_count() {
        global $woocommerce;
        $modified_count = self::moified_points_count_in_cart();

        if ($modified_count > 0) {
            $previous_value = WC()->session->get('modified_count');
            $original_points_count = count(RSFunctionForCart::original_points_for_product_in_cart());
            $updated_count = $previous_value != null ? $previous_value : $original_points_count;
            WC()->session->set('modified_count', $updated_count);
        }
    }

}

new RSDiscountedPointsCalculation();
