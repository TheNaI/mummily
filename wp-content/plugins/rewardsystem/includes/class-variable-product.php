<?php
/*
 * Simple Product Functionality
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFUnctinforVariableProduct')) {

    class RSFUnctinforVariableProduct {

        public static function init() {

            add_action('woocommerce_before_single_product', array(__CLASS__, 'display_msg_for_variable_product'));

            add_shortcode('variationrewardpoints', array(__CLASS__, 'add_variation_shortcode_div'));

            add_shortcode('variationpointprice', array(__CLASS__, 'add_variation_shortcode'));

            add_shortcode('variationpointsvalue', array(__CLASS__, 'add_variation_point_values_shortcode'));

            add_filter('woocommerce_ajax_variation_threshold', array(__CLASS__, 'rs_function_to_alert_the_variation_limit'), 999, 2);

            add_action('wp_ajax_nopriv_getvariationid', array(__CLASS__, 'add_shortcode_for_rewardpoints_of_variation'));

            add_action('wp_ajax_getvariationid', array(__CLASS__, 'add_shortcode_for_rewardpoints_of_variation'));
        }

        public static function display_msg_for_variable_product() {
            global $post;
            if (get_option('rs_enable_display_earn_message_for_variation_single_product') == 'yes') {
                $variableid = RSFunctionforSimpleProduct::get_variation_id($post->ID);
                $earnmessages = '';
                $earnmessage = '';
                if (is_array($variableid) && !empty($variableid)) {
                    $varpointss = RSFunctionforSimpleProduct::rewardpoints_of_variation($variableid[0], $post->ID);
                    if ($varpointss != '') {
                        $redeemingrspoints = $varpointss / wc_format_decimal(get_option('rs_redeem_point'));
                        $updatedredeemingpoints = $redeemingrspoints * wc_format_decimal(get_option('rs_redeem_point_value'));
                        $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                        $currency = round($updatedredeemingpoints, $roundofftype);
                        $message = get_option('rs_message_for_variation_products');
                        $earnmessage = str_replace('[variationrewardpoints]', $varpointss, $message);
                        if (get_option('woocommerce_currency_pos') == 'right' || get_option('woocommerce_currency_pos') == 'right_space') {
                            $currency = $currency . get_woocommerce_currency_symbol();
                        } elseif (get_option('woocommerce_currency_pos') == 'left' || get_option('woocommerce_currency_pos') == 'left_space') {
                            $currency = get_woocommerce_currency_symbol() . $currency;
                        }
                        $earnmessage = str_replace('[variationpointsvalue]', $currency, $earnmessage);
                        $messages = get_option("rs_message_for_single_product_variation");
                        $earnmessages = str_replace('[variationrewardpoints]', $varpointss, $messages);
                         if (get_option('_rs_enable_disable_gift_icon') == '1') {
                            if (get_option('rs_image_url_upload') != '') {
                                $image = "<img class = 'gifticon' src=" . get_option('rs_image_url_upload') . " style='width:16px;height:16px;display:inline;' />&nbsp;";
                            }
                        }
                        $earnmessages = $image . $earnmessages;
 
                    }
                }
            }
            ?>
            <div id='value_variable_product'></div>
            <?php if (get_option('rs_enable_display_earn_message_for_variation_single_product') == 'yes') { ?>
                <div id='value_variable_product1'></div>
                <?php
                if (get_option('rs_enable_display_earn_message_for_variation_single_product') == 'yes') {
                    if (($earnmessages != '' || $earnmessage != '')) {
                        ?>
                        <script type='text/javascript'>
                            jQuery(document).ready(function () {
                        <?php if (get_option('rs_show_hide_message_for_variable_product') == '1') { ?>
                                    jQuery('#value_variable_product1').addClass('woocommerce-info');
                                    jQuery('#value_variable_product1').show();
                                    jQuery('.gift_icon').hide();
                                    jQuery('#value_variable_product1').html("<?php echo $earnmessage; ?>");
                        <?php } ?>
                        <?php if (get_option('rs_show_hide_message_for_variable_in_single_product_page') == '1') { ?>
                                    jQuery('.variableshopmessage').show();
                                    jQuery('.variableshopmessage').html("<?php echo $earnmessages; ?>");
                        <?php } ?>
                            });
                        </script>
                        <?php
                    }
                }
            }
        }

        public static function add_variation_shortcode_div() {
            return "<span class='variationrewardpoints' style='display:inline-block'></span>";
        }

        public static function add_variation_shortcode() {
            return "<span class='variationpoint_price' style='display:inline-block'></span>";
        }

        public static function display_purchase_msg_for_variable_product() {
            if (is_product() || is_page()) {
                ?>                
                <script type='text/javascript'>
                    jQuery(document).ready(function () {
                        jQuery('#value_variable_product').hide();
                         jQuery('.gift_icon').hide();
                        jQuery(document).on('change', 'select', function () {
                            var variationid = jQuery('input:hidden[name=variation_id]').val();                           
                            if (variationid === '' || variationid === undefined) {
                                jQuery('#value_variable_product').hide();
                                // jQuery('.variableshopmessage').hide();
                                return false;
                            } else {                                
                                jQuery('#value_variable_product1').hide();
                                var dataparam = ({
                                    action: 'getvariationid',
                                    variationproductid: variationid,
                                    userid: "<?php echo get_current_user_id(); ?>",
                                });
                                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam, function (response) {
                                    if (response !== '') {
                <?php
                $banned_user_list = get_option('rs_banned-users_list');
                if (is_user_logged_in()) {
                    $userid = get_current_user_id();
                    $banning_type = FPRewardSystem::check_banning_type($userid);
                    if ($banning_type != 'earningonly' && $banning_type != 'both') {
                        ?>
                                                var splitresponse = response.split('_');
                                                if (splitresponse[0] > 0) {
                        <?php if (get_option('rs_show_hide_message_for_variable_in_single_product_page') == '1') { ?>
                                                        jQuery('.variableshopmessage').show();
                                                        jQuery('.gift_icon').show();
                                                        jQuery('.variableshopmessage').html("<?php echo do_shortcode(get_option('rs_message_for_single_product_variation')); ?>");
                        <?php } ?>
                        <?php if (get_option('rs_show_hide_message_for_variable_product') == '1') { ?>
                                                        jQuery('#value_variable_product').addClass('woocommerce-info');
                                                        jQuery('#value_variable_product').show();
                                                        jQuery('#value_variable_product').html("<?php echo do_shortcode(get_option('rs_message_for_variation_products')); ?>");
                        <?php } ?>
                                                    jQuery('.variationrewardpoints').html(splitresponse[0]);
                                                    jQuery('.variationrewardpointsamount').html(splitresponse[1]);
                                                    jQuery('.variationpoint_price').html(splitresponse[2]);
                                                } else {
                                                    jQuery('#value_variable_product').hide();
                                                    jQuery('.variableshopmessage').hide();
                                                }
                        <?php
                    }
                } else {
                    if (get_option('rs_show_hide_message_for_single_product_guest') == '1') {
                        ?>
                                                var splitresponse = response.split('_');
                                                if (splitresponse[0] > 0) {
                        <?php if (get_option('rs_show_hide_message_for_variable_in_single_product_page') == '1') { ?>
                                                        jQuery('.variableshopmessage').show();
                                                        jQuery('.gift_icon').show();
                                                        jQuery('.variableshopmessage').html("<?php echo do_shortcode(get_option('rs_message_for_single_product_variation')); ?>");
                        <?php } ?>
                        <?php if (get_option('rs_show_hide_message_for_variable_product') == '1') { ?>
                                                        jQuery('#value_variable_product').addClass('woocommerce-info');
                                                        jQuery('#value_variable_product').show();
                                                        jQuery('#value_variable_product').html("<?php echo do_shortcode(get_option('rs_message_for_variation_products')); ?>");
                        <?php } ?>
                                                    jQuery('.variationrewardpoints').html(splitresponse[0]);
                                                    jQuery('.variationrewardpointsamount').html(splitresponse[1]);
                                                    jQuery('.variationpoint_price').html(splitresponse[2]);
                                                } else {
                                                    jQuery('#value_variable_product').hide();
                                                    jQuery('.variableshopmessage').hide();
                                                }
                        <?php
                    }
                }
                ?>
                                    }
                                });
                            }
                        });
                        jQuery(document).on('change', '.wcva_attribute_radio', function (e) {
                            e.preventDefault();
                            var variationid = jQuery('input:hidden[name=variation_id]').val();
                            if (variationid === '' || variationid === undefined) {
                                jQuery('#value_variable_product').hide();
                                jQuery('.variableshopmessage').hide();
                                return false;
                            } else {
                                jQuery('#value_variable_product1').hide()
                                var dataparam = ({
                                    action: 'getvariationid',
                                    variationproductid: variationid,
                                    userid: "<?php echo get_current_user_id(); ?>",
                                });
                                jQuery.post("<?php echo admin_url('admin-ajax.php');
                ?>", dataparam,
                                        function (response) {                                            
                                            if (response !== '') {
                <?php
                $banned_user_list = get_option('rs_banned-users_list');
                if (is_user_logged_in()) {
                    $userid = get_current_user_id();
                    $banning_type = FPRewardSystem::check_banning_type($userid);
                    if ($banning_type != 'earningonly' && $banning_type != 'both') {
                        ?>
                                                        var splitresponse = response.split('_');
                                                        if (splitresponse[0] > 0) {
                        <?php if (get_option('rs_show_hide_message_for_variable_in_single_product_page') == '1') { ?>
                                                                jQuery('.variableshopmessage').show();
                                                                jQuery('.gift_icon').show();
                                                                jQuery('.variableshopmessage').html("<?php echo do_shortcode(get_option('rs_message_for_single_product_variation')); ?>");
                        <?php } ?>
                        <?php if (get_option('rs_show_hide_message_for_variable_product') == '1') { ?>
                                                                jQuery('#value_variable_product').show();
                                                                jQuery('#value_variable_product').html("<?php echo do_shortcode(get_option('rs_message_for_variation_products')); ?>");
                        <?php } ?>
                                                            jQuery('.variationrewardpoints').html(splitresponse[0]);
                                                            jQuery('.variationrewardpointsamount').html(splitresponse[1]);
                                                            jQuery('.variationpoint_price').html(splitresponse[2]);
                                                        } else {
                                                            jQuery('#value_variable_product').hide();
                                                            jQuery('.variableshopmessage').hide();
                                                        }
                        <?php
                    }
                } else {
                    ?>
                                                    var splitresponse = response.split('_');
                                                    if (splitresponse[0] > 0) {
                    <?php if (get_option('rs_show_hide_message_for_variable_in_single_product_page') == '1') { ?>
                                                            jQuery('.variableshopmessage').show();
                                                            jQuery('.gift_icon').show();
                                                            jQuery('.variableshopmessage').html("<?php echo do_shortcode(get_option('rs_message_for_single_product_variation')); ?>");
                    <?php } ?>
                    <?php if (get_option('rs_show_hide_message_for_variable_product') == '1') { ?>
                                                            jQuery('#value_variable_product').show();
                                                            jQuery('#value_variable_product').html("<?php echo do_shortcode(get_option('rs_message_for_variation_products')); ?>");
                    <?php } ?>
                                                        jQuery('.variationrewardpoints').html(splitresponse[0]);
                                                        jQuery('.variationrewardpointsamount').html(splitresponse[1]);
                                                        jQuery('.variationpoint_price').html(splitresponse[2]);
                                                    } else {
                                                        jQuery('#value_variable_product').hide();
                                                        jQuery('.variableshopmessage').hide();
                                                    }
                    <?php
                }
                ?>
                                            }
                                        });
                            }
                        });
                    });</script>
                <?php
            }
        }

        public static function add_variation_point_values_shortcode() {
            if (get_option('woocommerce_currency_pos') == 'right' || get_option('woocommerce_currency_pos') == 'right_space') {
                return "<div class='variationrewardpointsamount' style='display:inline-block'></div>" . get_woocommerce_currency_symbol();
            } elseif (get_option('woocommerce_currency_pos') == 'left' || get_option('woocommerce_currency_pos') == 'left_space') {
                return get_woocommerce_currency_symbol() . "<div class='variationrewardpointsamount' style='display:inline-block'></div>";
            }
        }

        public static function add_shortcode_for_rewardpoints_of_variation() {
            if (isset($_POST['variationproductid'])) {
                $variable_product1 = new WC_Product_Variation($_POST['variationproductid']);
                $restrictpoints = rs_function_to_restrict_points_for_product_which_has_saleprice($product_id = '', $_POST['variationproductid']);
                if ($restrictpoints != 'yes') {
                    update_option('variationproductids', $_POST['variationproductid']);
                    $item = array('qty' => '1');
                    $variable_product1 = new WC_Product_Variation($_POST['variationproductid']);
                    $newparentid = rs_get_parent_id($variable_product1);
                    $rewardpoints = check_level_of_enable_reward_point($newparentid, $_POST['variationproductid'], $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');                    
                    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                    $rewardpoints = round($rewardpoints, $roundofftype);
                    if ($rewardpoints > 0) {
                        if ($_POST['userid'] > 0) {
                            $rsoutput = RSMemberFunction::user_role_based_reward_points($_POST['userid'], $rewardpoints);
                        } else {
                            $rsoutput = $rewardpoints;
                        }
                        $redeemingrspoints = $rsoutput / wc_format_decimal(get_option('rs_redeem_point'));
                        $updatedredeemingpoints = $redeemingrspoints * wc_format_decimal(get_option('rs_redeem_point_value'));
                        $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                        echo round($rsoutput, $roundofftype) . '_' . round($updatedredeemingpoints, $roundofftype);
                        if (check_display_price_type($_POST['variationproductid']) == '2') {
                            echo "_" . '2';
                        } else {
                            echo "_" . '0';
                        }
                    } else {
                        echo "0_0_";
                        if (check_display_price_type($_POST['variationproductid']) == '2') {
                            echo "_" . '2';
                        } else {
                            echo "_" . '0';
                        }
                    }
                } else {
                    echo "0_0_";
                    if (check_display_price_type($_POST['variationproductid']) == '2') {
                        echo "_" . '2';
                    } else {
                        echo "_" . '0';
                    }
                }
            }
            exit();
        }

        public static function rs_function_to_alert_the_variation_limit($variation_limit, $product) {            
            $variation_limit = 1000;
            return $variation_limit;
        }

    }

    RSFUnctinforVariableProduct::init();
}