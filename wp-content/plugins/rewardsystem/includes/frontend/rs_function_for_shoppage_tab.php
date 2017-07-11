<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForShopPage')) {

    class RSFunctionForShopPage {

        public static function init() {
            
            add_action('wp_head',  array(__CLASS__, 'provide_custom_css_option_shop_page'));
            
        }

        /*
         * Function to Use Custom CSS in Shop Page
         *  
         */

        public static function provide_custom_css_option_shop_page() {
            global $woocommerce;
            if (is_shop()) {
                ?>
                <style type="text/css">
                <?php
                echo get_option('rs_shop_page_custom_css');
                ?> 
                </style>

                <?php
            }
        }

    }

    RSFunctionForShopPage::init();
}