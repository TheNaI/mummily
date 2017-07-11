<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionofGeneralTab')) {

    class RSFunctionofGeneralTab {

        public static function init() {
            add_action('wp_head', array(__CLASS__, 'provide_custom_css_option'));
        }

        public static function provide_custom_css_option() {
            if (!is_cart() && !is_checkout() && !is_account_page() && !is_shop() && !is_product()) {
                ?>
                <style type="text/css">
                <?php
                echo get_option('rs_general_custom_css');
                ?> 
                </style>
                <?php
            }
        }        
    }

    RSFunctionofGeneralTab::init();
}