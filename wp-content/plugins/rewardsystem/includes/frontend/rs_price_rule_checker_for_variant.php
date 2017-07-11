<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RewardSystem_Price_Rule')) {

    class RewardSystem_Price_Rule {

        public static function init() {
            add_action('rs_price_rule_checker_variant', array(__CLASS__, 'rewardsystem_price_rule_variant'), 10, 2);
            add_action('rs_price_rule_checker_simple', array(__CLASS__, 'rewardsystem_price_rule_simple'), 10, 2);

            add_action('rs_update_points_for_simple', array(__CLASS__, 'rewardsystem_price_rule_for_order_simple_product'), 10, 2);
            add_action('rs_update_points_for_variable', array(__CLASS__, 'rewardsystem_price_rule_for_order_variable_product'), 10, 2);

            add_action('rs_update_points_for_referral_simple', array(__CLASS__, 'rewardsystem_price_rule_for_order_referral_simple'), 10, 2);
            add_action('rs_update_points_for_referral_simple', array(__CLASS__, 'rewardsystem_price_rule_for_order_referral_variable'), 10, 2);

            add_action('rs_delete_points_for_referral_simple', array(__CLASS__, 'rewardsystem_price_rule_for_deleting_referral_points_simple'), 10, 2);


            add_action('rs_redo_points_for_referral_simple', array(__CLASS__, 'rewardsystem_price_rule_for_redo_referral_points_simple'), 10, 2);
            add_action('rs_redo_points_for_referral_simples', array(__CLASS__, 'rewardsystem_price_rule_for_redo_referral_points_simples'), 10, 2);

            add_action('rs_redo_points_for_referral_variables', array(__CLASS__, 'rewardsystem_price_rule_for_redo_referral_points_variables'), 10, 2);

            add_action('rs_redo_points_for_simple', array(__CLASS__, 'rewardsystem_price_rule_for_redo_simple_products'), 10, 2);
            add_action('rs_redo_points_for_variable', array(__CLASS__, 'rewardsystem_price_rule_for_redo_variable_products'), 10, 2);
        }

        /* Show Message for Variable Product */

        public static function rewardsystem_price_rule_variant(&$variationregularprice, &$value) {
            if (class_exists('WC_Dynamic_Pricing')) {
                $variationregularprice = rs_get_price($value['data']);
            }
        }

        /* Show Message for Simple Product */

        public static function rewardsystem_price_rule_simple(&$getregularprice, &$value) {
            if (class_exists('WC_Dynamic_Pricing')) {
                $getregularprice = rs_get_price($value['data']);
            }
        }

        /* Update Points for Simple Product Type on Order Status */

        public static function rewardsystem_price_rule_for_order_simple_product(&$getregularprice, &$item) {
            if (class_exists('WC_Dynamic_Pricing')) {
                $getregularprice_with_quantity = $item['line_subtotal'];
                $quantity_checker = $item['qty'];
                $getregularprice = $getregularprice_with_quantity / $quantity_checker;
            }
        }

        /* Update Points for Variable Product Type on Order Status */

        public static function rewardsystem_price_rule_for_order_variable_product(&$variationregularprice, &$item) {
            if (class_exists('WC_Dynamic_Pricing')) {
                $variationregularprice_with_quantity = $item['line_subtotal'];
                $quantity_checker = $item['qty'];
                $variationregularprice = $variationregularprice_with_quantity / $quantity_checker;
            }
        }

        /* Update Points for Referral Points for Simple Products */

        public static function rewardsystem_price_rule_for_order_referral_simple(&$getregularprice, &$item) {
            if (class_exists('WC_Dynamic_Pricing')) {
                $getregularprice_with_quantity = $item['line_subtotal'];
                $quantity_checker = $item['qty'];
                $getregularprice = $getregularprice_with_quantity / $quantity_checker;
            }
        }

        public static function rewardsystem_price_rule_for_order_referral_simples(&$getregularprices, &$item) {
            if (class_exists('WC_Dynamic_Pricing') || class_exists('RP_WCDPD')) {
                $getregularprices_with_quantity = $item['line_subtotal'];
                $quantity_checker = $item['qty'];
                $getregularprices = $getregularprices_with_quantity / $quantity_checker;
            }
        }

        /* Update Points for Referral Points for Variable Products */

        public static function rewardsystem_price_rule_for_order_referral_variable(&$variationregularprice, &$item) {
            if (class_exists('WC_Dynamic_Pricing') || class_exists('RP_WCDPD')) {
                $variationregularprice_with_quantity = $item['line_total'];
                $quantity_checker = $item['qty'];
                $variationregularprice = $variationregularprice_with_quantity / $quantity_checker;
            }
        }

        /* Delete Points for Referral Rewards Simple Product */

        public static function rewardsystem_price_rule_for_deleting_referral_points_simple(&$getregularprice, &$item) {
            if (class_exists('WC_Dynamic_Pricing') || class_exists('RP_WCDPD')) {
                $getregularprice_with_quantity = $item['line_total'];
                $quantity_checker = $item['qty'];
                $getregularprice = $getregularprice_with_quantity / $quantity_checker;
            }
        }

        public static function rewardsystem_price_rule_for_deleting_referral_points_simples(&$getregularprices, &$item) {
            if (class_exists('WC_Dynamic_Pricing') || class_exists('RP_WCDPD')) {
                $getregularprices_with_quantity = $item['line_total'];
                $quantity_checker = $item['qty'];
                $getregularprice = $getregularprices_with_quantity / $quantity_checker;
            }
        }

        public static function rewardsystem_price_rule_for_redo_referral_points_simple(&$getregularprice, &$item) {
            if (class_exists('WC_Dynamic_Pricing') || class_exists('RP_WCDPD')) {
                $getregularprice_with_quantity = $item['line_total'];
                $quantity_checker = $item['qty'];
                $getregularprice = $getregularprice_with_quantity / $quantity_checker;
            }
        }

        public static function rewardsystem_price_rule_for_redo_referral_points_simples(&$getregularprices, &$item) {
            if (class_exists('WC_Dynamic_Pricing') || class_exists('RP_WCDPD')) {
                $getregularprices_with_quantity = $item['line_total'];
                $quantity_checker = $item['qty'];
                $getregularprices = $getregularprices_with_quantity / $quantity_checker;
            }
        }

        public static function rewardsystem_price_rule_for_redo_referral_points_variables(&$variationregularprice, &$item) {
            if (class_exists('WC_Dynamic_Pricing') || class_exists('RP_WCDPD')) {
                $variationregularprice_with_quantity = $item['line_total'];
                $quantity_checker = $item['qty'];
                $variationregularprice = $variationregularprice_with_quantity / $quantity_checker;
            }
        }

        public static function rewardsystem_price_rule_for_redo_simple_products(&$getregularprice, &$item) {
            if (class_exists('WC_Dynamic_Pricing') || class_exists('RP_WCDPD')) {
                $getregularprice_with_quantity = $item['line_total'];
                $quantity_checker = $item['qty'];
                $getregularprice = $getregularprice_with_quantity / $quantity_checker;
            }
        }

        public static function rewardsystem_price_rule_for_redo_variable_products(&$variationregularprice, &$item) {
            if (class_exists('WC_Dynamic_Pricing') || class_exists('RP_WCDPD')) {
                $variationregularprice_with_quantity = $item['line_total'];
                $quantity_checker = $item['qty'];
                $variationregularprice = $variationregularprice_with_quantity / $quantity_checker;
            }
        }

    }

    RewardSystem_Price_Rule::init();
}