<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForGiftVoucher')) {

    class RSFunctionForGiftVoucher {

        public static function init() {

            // add_action('wp_enqueue_scripts', array(__CLASS__, 'wp_enqueqe_script_for_footable'));
            // add_action('admin_enqueue_scripts', array(__CLASS__, 'wp_enqueqe_script_for_footable'));

            add_action('wp_ajax_nopriv_rewardsystem_point_vouchers', array(__CLASS__, 'process_ajax_request_rs_point_vouchers'));

            add_action('wp_ajax_rewardsystem_point_vouchers', array(__CLASS__, 'process_ajax_request_rs_point_vouchers'));

            add_action('wp_ajax_nopriv_rewardsystem_point_bulk_vouchers', array(__CLASS__, 'process_ajax_request_for_rs_bulk_point_vouchers'));

            add_action('wp_ajax_rewardsystem_point_bulk_vouchers', array(__CLASS__, 'process_ajax_request_for_rs_bulk_point_vouchers'));

            add_action('wp_ajax_nopriv_rewardsystem_redeem_voucher_codes', array(__CLASS__, 'process_ajax_request_to_redeem_voucher_reward_system'));

            add_action('wp_ajax_rewardsystem_redeem_voucher_codes', array(__CLASS__, 'process_ajax_request_to_redeem_voucher_reward_system'));

         

            if (get_option('rs_show_hide_redeem_voucher') == '1') {

                if (get_option('rs_redeem_voucher_position') == '1') {

                    add_action('woocommerce_before_my_account', array(__CLASS__, 'reward_system_my_account_voucher_redeem'));
                } else {
                    add_action('woocommerce_after_my_account', array(__CLASS__, 'reward_system_my_account_voucher_redeem'));
                }
            }

            add_shortcode('rs_redeem_vouchercode', array(__CLASS__, 'rewardsystem_myaccount_voucher_redeem_shortcode'));

            add_filter('woocommerce_login_redirect', array(__CLASS__, 'rs_function_to_redirect_after_login_and_registration'));

            add_filter('woocommerce_registration_redirect', array(__CLASS__, 'rs_function_to_redirect_after_login_and_registration'));
        }

        public static function wp_enqueqe_script_for_footable() {
            wp_register_script('wp_reward_footable', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/footable.js");
            wp_register_script('wp_reward_footable_filter', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/footable.filter.js");
            wp_enqueue_script('wp_reward_footable');
            wp_enqueue_script('wp_reward_footable_filter');
        }

        public static function reward_system_my_account_voucher_redeem() {
            $placeholder = get_option('rs_redeem_your_gift_voucher_placeholder');
            ?>
            <h3><?php echo get_option('rs_redeem_your_gift_voucher_label'); ?></h3>
            <input type="text" size="50" name="rs_redeem_voucher" id="rs_redeem_voucher_code" value="" placeholder="<?php echo $placeholder; ?>">
            <input type="submit" style="margin-left:10px;" class="button <?php echo get_option('rs_extra_class_name_redeem_gift_voucher_button'); ?>" name="rs_submit_redeem_voucher" id="rs_submit_redeem_voucher" value="<?php echo get_option('rs_redeem_gift_voucher_button_label'); ?>"/>
            <div class="rs_redeem_voucher_error" style="color:red;"></div>
            <div class="rs_redeem_voucher_success" style="color:green"></div>
            <?php
        }

        public static function rs_script_to_redeem_voucher() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('#rs_submit_redeem_voucher').click(function () {
                        var redeemvouchercode = jQuery('#rs_redeem_voucher_code').val();
                        var new_redeemvouchercode = redeemvouchercode.replace(/\s/g, '');
                        if (new_redeemvouchercode === '') {
                            jQuery('.rs_redeem_voucher_error').html('<?php echo addslashes(get_option('rs_voucher_redeem_empty_error')); ?>').fadeIn().delay(5000).fadeOut();
                            return false;
                        } else {
                            jQuery('.rs_redeem_voucher_error').html('');
                            var dataparam = ({
                                action: 'rewardsystem_redeem_voucher_codes',
                                redeemvouchercode: new_redeemvouchercode,
                            });
                            jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                    function (response) {
                                        console.log(jQuery.parseHTML(response));
                                        jQuery('.rs_redeem_voucher_success').html(jQuery.parseHTML(response)).fadeIn().delay(5000).fadeOut();
                                        jQuery('#rs_redeem_voucher_code').val('');
                                    });
                            return false;
                        }
                    });
                });
            </script>
            <?php
        }

        public static function rewardsystem_myaccount_voucher_redeem_shortcode() {
            wp_enqueue_script('giftvoucher', false, array(), '', true);
            $messagess = get_option('rs_redeem_your_gift_voucher_label');
            $placeholder = get_option('rs_redeem_your_gift_voucher_placeholder');
            ob_start();
            if (is_user_logged_in()) {
                ?>
                <h3><?php echo $messagess; ?></h3>
                <input type="text" size="50" name="rs_redeem_voucher" id="rs_redeem_voucher_code" value="" placeholder="<?php echo $placeholder; ?>"><input type="submit" style="margin-left:10px;" class="button <?php echo get_option('rs_extra_class_name_redeem_gift_voucher_button'); ?>" name="rs_submit_redeem_voucher" id="rs_submit_redeem_voucher" value="<?php echo get_option('rs_redeem_gift_voucher_button_label'); ?>"/>
                <div class="rs_redeem_voucher_error" style="color:red;"></div>
                <div class="rs_redeem_voucher_success" style="color:green"></div>            
                <?php
            } else {
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                $linkforlogin = add_query_arg('redirect_to', get_permalink(), $myaccountlink);
                ?>
                <?php ob_start(); ?><a href="<?php echo $linkforlogin; ?>"><?php echo addslashes(get_option('rs_redeem_voucher_login_link_label')); ?></a>                
                <?php
                $message_for_guest = get_option("rs_voucher_redeem_guest_error_message");
                $redeem_voucher_guest_to_find = "[rs_login_link]";
                $redeem_voucher_guest_to_replace = ob_get_clean();
                $redeem_voucher_guest_replaced_content = str_replace($redeem_voucher_guest_to_find, $redeem_voucher_guest_to_replace, $message_for_guest);
                echo $redeem_voucher_guest_replaced_content;
            }
            $maincontent = ob_get_clean();
            return $maincontent;
        }

        public static function rs_function_to_redirect_after_login_and_registration($redirect) {

            if (isset($_REQUEST['redirect_to'])) {
                $redirect = $_REQUEST['redirect_to'];
            }
            return $redirect;
        }

        public static function process_ajax_request_to_redeem_voucher_reward_system() {
            $newone = array();
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                if (isset($_POST['redeemvouchercode'])) {
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'rsgiftvoucher';
                    $voucher_code_to_redeem = trim($_POST['redeemvouchercode']);
                    $findedarray = $wpdb->get_results("SELECT * FROM $table_name WHERE vouchercode = '$voucher_code_to_redeem'", ARRAY_A);
                    if (empty($findedarray)) {
                        echo addslashes(get_option('rs_invalid_voucher_code_error_message'));
                        exit();
                    } else {
                        $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                        $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                        $dateformat = get_option('date_format');
                        $todays_date = date_i18n($dateformat);
                        $today = strtotime($todays_date);
                        $exp_date = $findedarray[0]['voucherexpiry'];
                        $vouchercreated = $findedarray[0]['vouchercreated'];
                        $voucherused = isset($findedarray[0]['memberused']) != '' ? $findedarray[0]['memberused'] : '';                        
                        $voucherpoints = $findedarray[0]['points'];
                        $translatedstring = $_POST['redeemvouchercode'];
                        $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
                        if ($voucherused == '') {                            
                            if ($exp_date != '' && $exp_date != 'Never') {
                                $expiration_date = strtotime($exp_date);                                
                                if ($expiration_date >= $today) {
                                    if ($enabledisablemaxpoints == 'yes') {
                                        $new_obj->check_point_restriction($restrictuserpoints, $voucherpoints, $pointsredeemed = 0, $event_slug = 'RPGV', $userid, $nomineeid = '', $referrer_id = '', $product_id = '', $variationid = '', $translatedstring);
                                    } else {
                                        $equearnamt = RSPointExpiry::earning_conversion_settings($voucherpoints);
                                        $valuestoinsert = array('pointstoinsert' => $voucherpoints, 'pointsredeemed' => 0, 'event_slug' => 'RPGV', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $userid, 'referred_id' => '', 'product_id' => '', 'variation_id' => '', 'reasonindetail' => $translatedstring, 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $voucherpoints, 'totalredeempoints' => 0);
                                        $new_obj->total_points_management($valuestoinsert);
                                        $rs_voucher_redeem_success_to_find = "[giftvoucherpoints]";
                                        $rs_voucher_redeem_success_message = get_option('rs_voucher_redeem_success_message');
                                        $rs_voucher_redeem_success_message_replaced = str_replace($rs_voucher_redeem_success_to_find, $voucherpoints, $rs_voucher_redeem_success_message);
                                        echo addslashes($rs_voucher_redeem_success_message_replaced);
                                    }                                    
                                    $wpdb->update($table_name, array('points' => $voucherpoints, 'vouchercode' => $_POST['redeemvouchercode'], 'vouchercreated' => $vouchercreated, 'voucherexpiry' => $exp_date, 'memberused' => get_current_user_id()), array('id' => $findedarray[0]['id']));                                   
                                } else {
                                    echo addslashes(get_option('rs_voucher_code_expired_error_message'));
                                }
                            } else {
                                // Coupon Never Expired
                                if ($enabledisablemaxpoints == 'yes') {
                                    $new_obj->check_point_restriction($restrictuserpoints, $voucherpoints, $pointsredeemed = 0, $event_slug = 'RPGV', $userid, $nomineeid = '', $referrer_id = '', $product_id = '', $variationid = '', $translatedstring);
                                } else {
                                    $equearnamt = RSPointExpiry::earning_conversion_settings($voucherpoints);
                                    $valuestoinsert = array('pointstoinsert' => $voucherpoints, 'pointsredeemed' => 0, 'event_slug' => 'RPGV', 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $userid, 'referred_id' => '', 'product_id' => '', 'variation_id' => '', 'reasonindetail' => $translatedstring, 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $voucherpoints, 'totalredeempoints' => 0);
                                    $new_obj->total_points_management($valuestoinsert);
                                    $rs_voucher_redeem_success_to_find = "[giftvoucherpoints]";
                                    $rs_voucher_redeem_success_message = get_option('rs_voucher_redeem_success_message');
                                    $rs_voucher_redeem_success_message_replaced = str_replace($rs_voucher_redeem_success_to_find, $voucherpoints, $rs_voucher_redeem_success_message);
                                    echo addslashes($rs_voucher_redeem_success_message_replaced);
                                }
                                $wpdb->update($table_name, array('points' => $voucherpoints, 'vouchercode' => $_POST['redeemvouchercode'], 'vouchercreated' => $vouchercreated, 'voucherexpiry' => $exp_date, 'memberused' => get_current_user_id()), array('id' => $findedarray[0]['id']));
                            }                            
                        } else {
                            echo addslashes(get_option('rs_voucher_code_used_error_message'));
                        }
                    }
                }
            } else {
                echo addslashes(get_option('rs_banned_user_redeem_voucher_error'));
            }
            do_action('fp_reward_point_for_using_gift_voucher');
            exit();
        }

    }
    RSFunctionForGiftVoucher::init();
}