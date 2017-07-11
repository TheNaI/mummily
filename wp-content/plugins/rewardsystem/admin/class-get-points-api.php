<?php

/*
 * API for Points
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class RSAPI {

    public function __construct($product_id) {
        $this->id = $product_id;
    }

    public function get_term_id($product_id) {
        $termid = array();
        $productid = $this->get_parent_id($product_id);
        $term = get_the_terms($productid, 'product_cat');
        if (is_array($term) && !empty($term)) {
            foreach ($term as $terms) {
                $termid = $terms->term_id;
            }
        }
        return $termid;
    }

    public function get_product_type($product_id) {
        $product_obj = new WC_Product_Variation($product_id);
        if (is_object($product_obj) && $product_obj != false) {
            return 'variable';
        } else {
            return 'simple';
        }
    }

    public function get_parent_id($product_id) {
        $product_obj = new WC_Product_Variation($product_id);
        if (is_object($product_obj) && $product_obj != false) {
            return $product_obj->parent->get_id();
        }else{
            return $product_id;
        }
    }

    public function get_points() {

        $product_id = $this->id;

        $product_type = $this->get_product_type($product_id);

        //Product Level
        $product_level = $product_type == 'variable' ? get_post_meta($product_id, '_enable_reward_points') : get_post_meta($product_id, '_rewardsystemcheckboxvalue');
        $product_level_rewardtype = $product_type == 'variable' ? get_post_meta($product_id, '_select_reward_rule') : get_post_meta($product_id, '_rewardsystem_options');
        $product_level_rewardpoints = $product_type == 'variable' ? get_post_meta($product_id, '_reward_points') : get_post_meta($product_id, '_rewardsystempoints');
        $product_level_rewardpercent = $product_type == 'variable' ? get_post_meta($product_id, '_reward_percent') : get_post_meta($product_id, '_rewardsystempercent');

        if (($product_level == 'yes') || ($product_level == '1')) {
            if (($product_level_rewardtype == '1') && ($product_level_rewardpoints != '')) {
                return $product_level_rewardpoints;
            } else {
                if ($product_level_rewardpercent != '') {
                    return $product_level_rewardpercent;
                }
            }
        } else {
            $term_id = $this->get_term_id($product_id);
            $category_level_value = $this->get_points_from_category_level($term_id) != '' ? $this->get_points_from_category_level() : $this->get_points_from_global_level();
            return $category_level_value;
        }
    }

    public function get_points_from_category_level($termid) {

        $product_id = $this->id;
        $term_id = $termid['term_id'];
        
        //Category Level
        $category_list = wp_get_post_terms($product_id, 'product_cat');
        $getcount = count($category_list);
        $category_level = get_woocommerce_term_meta($term_id, 'enable_reward_system_category');
        $category_level_rewardtype = get_woocommerce_term_meta($term_id, 'enable_rs_rule');
        $category_level_rewardpoints = get_woocommerce_term_meta($term_id, 'rs_category_points');
        $category_level_rewardpercent = get_woocommerce_term_meta($term_id, 'rs_category_percent');

        if ($getcount >= '1') {
            if (($categorylevel == 'yes') || ($categorylevel != '')) {
                if (($categorylevelrewardtype == '1') && ($categorylevelrewardpoints != '')) {
                    return $categorylevelrewardpoints;
                } else {
                    if ($categorylevelrewardpercent != '') {
                        return $categorylevelrewardpercent;
                    }
                }
            }
        }
        return '';
    }

    public function get_points_from_global_level() {
        //Global Level
        $global_enable = get_option('rs_global_enable_disable_sumo_reward');
        $global_reward_type = get_option('rs_global_reward_type');
        $global_rewardpoints = get_option('rs_global_reward_points');
        $global_rewardpercent = get_option('rs_global_reward_percent');

        if ($global_enable == '1') {
            if (($global_reward_type == '1') && ($global_rewardpoints != '')) {
                return $global_rewardpoints;
            } else {
                if ($global_rewardpercent != '') {
                    return $global_rewardpercent;
                }
            }
        }
        return '';
    }

}