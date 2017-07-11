<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForSingleProductPage')) {

    class RSFunctionForSingleProductPage {

        public static function init() {
            
            add_action('wp_head',  array(__CLASS__, 'provide_custom_css_option_single_page'));
            
        }

        /*
         * Function to Use Custom CSS in Single Product Page
         *  
         */

        public static function provide_custom_css_option_single_page() {
            global $woocommerce;
            if (is_product()) {
                ?>
                <style type="text/css">
                <?php
                echo get_option('rs_single_product_page_custom_css');
                ?> 
                </style>

                <?php
            }
        }

    }

    RSFunctionForSingleProductPage::init();
}