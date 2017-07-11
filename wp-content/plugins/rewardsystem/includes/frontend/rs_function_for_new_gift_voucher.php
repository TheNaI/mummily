<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForOfflineOnlineRewards')) {

    class RSFunctionForOfflineOnlineRewards {

        public static function init() {

            add_action('wp_ajax_rs_create_voucher_code', array(__CLASS__, 'process_ajax_to_create_voucher_code'));

            add_action('wp_ajax_rssplitvouchercode', array(__CLASS__, 'process_to_split_voucher_codes'));

            add_shortcode('sumo_code_field', array(__CLASS__, 'rs_form_for_redeeming_voucher_codes'));

            add_shortcode('sumo_current_balance', array(__CLASS__, 'rs_function_for_current_available_points'));            
        }      

        /*
         * Voucher  code creation settings
         * 
         */

        public static function function_to_generate_random_code($array) {
            $reward_code_type = $array['reward_code_type'];
            $number_of_charaters_for_voucher_code = $array['characters_for_code'];
            $number_of_points_for_voucher_code = $array['point_for_code'];
            $number_of_vouchers_to_be_generated = $array['no_of_vouchercodes'];
            $gift_expired_date = $array['expiry_date'];
            $prefix_coupon_value = isset($array['prefix_coupon_value']) ? $array['prefix_coupon_value'] : '';
            $suffix_coupon_value = isset($array['suffix_coupon_value']) ? $array['suffix_coupon_value'] : '';
            $exclude_content_code = $array['exclude_content'];
            if ($reward_code_type == 'numeric') {
                if ($number_of_charaters_for_voucher_code != "" && $number_of_points_for_voucher_code != '' && $number_of_vouchers_to_be_generated != '') {
                    $number_of_times = (int) $number_of_vouchers_to_be_generated;
                    for ($k = 0; $k < $number_of_times; $k++) {
                        $random_code = '';
                        $check_duplicate_random_codes = array();
                        for ($j = 1; $j <= $number_of_charaters_for_voucher_code; $j++) {
                            $random_code .= rand(0, 9);
                        }
                        $random_codes[] = $prefix_coupon_value . $random_code . $suffix_coupon_value;
                    }
                }
            } else {
                if ($number_of_charaters_for_voucher_code != "" && $number_of_points_for_voucher_code != '' && $number_of_vouchers_to_be_generated != '') {
                    $list_of_characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $character_length = strlen($list_of_characters);
                    $number_of_times = (int) $number_of_vouchers_to_be_generated;
                    for ($k = 0; $k < $number_of_times; $k++) {
                        $randomstring = '';
                        for ($j = 1; $j <= $number_of_charaters_for_voucher_code; $j++) {
                            $randomstring .= $list_of_characters[rand(0, $character_length - 1)];
                        }
                        if ($exclude_content_code != "") {
                            $exclude_string = explode(",", $exclude_content_code);
                            $new_array = array();
                            foreach ($exclude_string as $value) {
                                $new_array[$value] = rand(0, 9);
                            }

                            $randomstring = strtr($randomstring, $new_array);
                            $random_codes[] = $prefix_coupon_value . $randomstring . $suffix_coupon_value;
                        } else {
                            $random_codes[] = $prefix_coupon_value . $randomstring . $suffix_coupon_value;
                        }
                    }
                }
            }
            return $random_codes;
        }

        public static function process_ajax_to_create_voucher_code() {
            $verify_security = isset($_POST['security']) ? rs_function_to_verify_secure($_POST['security']) : false;
            if (isset($_POST['proceedanyway']) && $verify_security && isset($_POST['state']) && $_POST['state'] == 'yes') {
                if ($_POST['proceedanyway'] == '1') {

                    $updated_voucher_code_new = array();

                    $prefix_enabled_for_voucher = $_POST['prefix_enabled_value'];

                    $prefix_coupon_value = $_POST['prefix_content'];

                    $suffix_enabled_for_voucher = $_POST['suffix_enabled_value'];

                    $suffix_coupon_value = $_POST['sufffix_content'];

                    $reward_code_type = $_POST['reward_code_type'];

                    $exclude_content_code = $_POST['exclude_content_code'];

                    $number_of_charaters_for_voucher_code = $_POST['length_of_voucher_code'];

                    $number_of_points_for_voucher_code = $_POST['points_value_of_voucher_code'];

                    $number_of_vouchers_to_be_generated = $_POST['number_of_vouchers_to_be_created'] ? $_POST['number_of_vouchers_to_be_created'] : '';

                    $gift_expired_date = $_POST['gift_expired_date'];

                    $voucher_created_date = $_POST['vouchercreated'];

                    if ($prefix_enabled_for_voucher == 'yes' && $suffix_enabled_for_voucher == 'yes') {
                        if ($prefix_coupon_value != '' && $suffix_coupon_value != '') {
                            $value_to_generate_randomcode = array(
                                'reward_code_type' => $reward_code_type,
                                'characters_for_code' => $number_of_charaters_for_voucher_code,
                                'point_for_code' => $number_of_points_for_voucher_code,
                                'no_of_vouchercodes' => $number_of_vouchers_to_be_generated,
                                'expiry_date' => $gift_expired_date,
                                'exclude_content' => $exclude_content_code,
                                'prefix_coupon_value' => $prefix_coupon_value,
                                'suffix_coupon_value' => $suffix_coupon_value,
                            );
                            $random_codes = self::function_to_generate_random_code($value_to_generate_randomcode);
                        } elseif ($prefix_coupon_value == '' && $suffix_coupon_value == '') {
                            $value_to_generate_randomcode = array(
                                'reward_code_type' => $reward_code_type,
                                'characters_for_code' => $number_of_charaters_for_voucher_code,
                                'point_for_code' => $number_of_points_for_voucher_code,
                                'no_of_vouchercodes' => $number_of_vouchers_to_be_generated,
                                'expiry_date' => $gift_expired_date,
                                'exclude_content' => $exclude_content_code,
                            );
                            $random_codes = self::function_to_generate_random_code($value_to_generate_randomcode);
                        } elseif ($prefix_coupon_value != '' && $suffix_coupon_value == '') {
                            $value_to_generate_randomcode = array(
                                'reward_code_type' => $reward_code_type,
                                'characters_for_code' => $number_of_charaters_for_voucher_code,
                                'point_for_code' => $number_of_points_for_voucher_code,
                                'no_of_vouchercodes' => $number_of_vouchers_to_be_generated,
                                'expiry_date' => $gift_expired_date,
                                'exclude_content' => $exclude_content_code,
                                'prefix_coupon_value' => $prefix_coupon_value,
                            );
                            $random_codes = self::function_to_generate_random_code($value_to_generate_randomcode);
                        } elseif ($prefix_coupon_value == '' && $suffix_coupon_value != '') {
                            $value_to_generate_randomcode = array(
                                'reward_code_type' => $reward_code_type,
                                'characters_for_code' => $number_of_charaters_for_voucher_code,
                                'point_for_code' => $number_of_points_for_voucher_code,
                                'no_of_vouchercodes' => $number_of_vouchers_to_be_generated,
                                'expiry_date' => $gift_expired_date,
                                'exclude_content' => $exclude_content_code,
                                'suffix_coupon_value' => $suffix_coupon_value,
                            );
                            $random_codes = self::function_to_generate_random_code($value_to_generate_randomcode);
                        }
                    } elseif ($prefix_enabled_for_voucher != 'yes' && $suffix_enabled_for_voucher != 'yes') {
                        $value_to_generate_randomcode = array(
                            'reward_code_type' => $reward_code_type,
                            'characters_for_code' => $number_of_charaters_for_voucher_code,
                            'point_for_code' => $number_of_points_for_voucher_code,
                            'no_of_vouchercodes' => $number_of_vouchers_to_be_generated,
                            'expiry_date' => $gift_expired_date,
                            'exclude_content' => $exclude_content_code,
                        );
                        $random_codes = self::function_to_generate_random_code($value_to_generate_randomcode);
                    } elseif ($prefix_enabled_for_voucher == 'yes' && $suffix_enabled_for_voucher != 'yes') {
                        if ($prefix_coupon_value != '') {
                            $value_to_generate_randomcode = array(
                                'reward_code_type' => $reward_code_type,
                                'characters_for_code' => $number_of_charaters_for_voucher_code,
                                'point_for_code' => $number_of_points_for_voucher_code,
                                'no_of_vouchercodes' => $number_of_vouchers_to_be_generated,
                                'expiry_date' => $gift_expired_date,
                                'exclude_content' => $exclude_content_code,
                                'prefix_coupon_value' => $prefix_coupon_value,
                            );
                            $random_codes = self::function_to_generate_random_code($value_to_generate_randomcode);
                        } else {
                            $value_to_generate_randomcode = array(
                                'reward_code_type' => $reward_code_type,
                                'characters_for_code' => $number_of_charaters_for_voucher_code,
                                'point_for_code' => $number_of_points_for_voucher_code,
                                'no_of_vouchercodes' => $number_of_vouchers_to_be_generated,
                                'expiry_date' => $gift_expired_date,
                                'exclude_content' => $exclude_content_code,
                            );
                            $random_codes = self::function_to_generate_random_code($value_to_generate_randomcode);
                        }
                    } elseif ($prefix_enabled_for_voucher != 'yes' && $suffix_enabled_for_voucher == 'yes') {
                        if ($suffix_coupon_value != '') {
                            $value_to_generate_randomcode = array(
                                'reward_code_type' => $reward_code_type,
                                'characters_for_code' => $number_of_charaters_for_voucher_code,
                                'point_for_code' => $number_of_points_for_voucher_code,
                                'no_of_vouchercodes' => $number_of_vouchers_to_be_generated,
                                'expiry_date' => $gift_expired_date,
                                'exclude_content' => $exclude_content_code,
                                'suffix_coupon_value' => $suffix_coupon_value,
                            );
                            $random_codes = self::function_to_generate_random_code($value_to_generate_randomcode);
                        } else {
                            $value_to_generate_randomcode = array(
                                'reward_code_type' => $reward_code_type,
                                'characters_for_code' => $number_of_charaters_for_voucher_code,
                                'point_for_code' => $number_of_points_for_voucher_code,
                                'no_of_vouchercodes' => $number_of_vouchers_to_be_generated,
                                'expiry_date' => $gift_expired_date,
                                'exclude_content' => $exclude_content_code,
                            );
                            $random_codes = self::function_to_generate_random_code($value_to_generate_randomcode);
                        }
                    }
                    echo json_encode($random_codes);
                }
                exit();
            }
        }

       public static function process_to_split_voucher_codes() {
            global $wpdb;
            $table_name = $wpdb->prefix . "rsgiftvoucher";
            $verify_security = isset($_POST['security']) ? rs_function_to_verify_secure($_POST['security']) : false;
            if (isset($_POST['ids']) && !empty($_POST['ids']) && $verify_security && isset($_POST['state']) && $_POST['state'] == 'yes') {
                $voucher_code = $_POST['ids'];
                $gift_expired_date = $_POST['gift_expired_date'];
                $voucher_created_date = $_POST['vouchercreated'];
                $number_of_points_for_voucher_code = $_POST['points_value_of_voucher_code'];
                foreach ($voucher_code as $each_code) {
                    $wpdb->insert(
                            $table_name, array(
                        'points' => $number_of_points_for_voucher_code,
                        'vouchercode' => $each_code,
                        'vouchercreated' => $voucher_created_date,
                        'voucherexpiry' => $gift_expired_date,
                        'memberused' => '')
                    );
                }
                echo json_encode('sucess');
            }
            exit();
        }      

        public static function get_keys_for_duplicate_values($my_arr) {
            $dups = $new_arr = array();
            if (is_array($my_arr) && !empty($my_arr)) {
                foreach ($my_arr as $key) {
                    if (is_array($key) && !empty($key)) {
                        foreach ($key as $val) {
                            if (!isset($new_arr[$val['vouchercode']])) {
                                $new_arr[$val['vouchercode']] = $val['vouchercode'];
                            } else {
                                if (isset($dups[$val['vouchercode']])) {
                                    $dups[$val['vouchercode']] = $val['vouchercode'];
                                } else {
                                    $dups[$val['vouchercode']] = $val['vouchercode'];
                                }
                            }
                        }
                    }
                }
            }

            return $dups;
        }

        public static function rs_form_for_redeeming_voucher_codes() {
            ob_start();
            if (is_user_logged_in()) {
                ?>
                <style>
                    .rs_form_for_claiming_offline_rewards{
                        margin-left: auto;
                        margin-right: auto;
                        width: 50%;
                    }
                    .rs_redeem_offline_rewards{
                        margin-left: auto;
                        margin-right: auto;
                        width: 50%;
                    }
                </style>
                <form class="rs_form_for_claiming_offline_rewards" method="post">
                    <label><?php _e('Enter your Voucher Code below to claim', 'rewardsystem'); ?></label><br/><br/>
                    <input type="text" name="rs_redeem_offline_online_rewards" class="rs_redeem_field_offline_online_rewards" placeholder="<?php _e('Voucher Code','rewardsystem'); ?>"/><br/><br/>
                    <input type="submit" name="rs_offline_rewards_redeem" class="rs_offline_rewards_redeem" value="<?php _e('Submit','rewardsystem');?>"/>  
                </form>
                <?php
                if (isset($_POST['rs_offline_rewards_redeem'])) {
                    $voucher_code_to_redeem = trim($_POST['rs_redeem_offline_online_rewards']);
                    $newone[] = '';
                    $userid = get_current_user_id();
                    $banning_type = FPRewardSystem::check_banning_type($userid);
                    if ($banning_type != 'earningonly' && $banning_type != 'both') {
                        if (isset($voucher_code_to_redeem)) {                          
                            global $wpdb;
                            $table_name = $wpdb->prefix . 'rsgiftvoucher';
                            $findedarray = $wpdb->get_results("SELECT * FROM $table_name WHERE vouchercode = '$voucher_code_to_redeem'", ARRAY_A);
                            if (empty($findedarray)) {
                                echo addslashes(get_option('rs_invalid_voucher_code_error_message'));
                                exit();
                            } else {
                                $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                                $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                                $todays_date = date_i18n("Y-m-d");
                                $today = strtotime($todays_date);
                                $vouchercreated = $findedarray[0]['vouchercreated'];
                                $memberused = isset($findedarray[0]['memberused']) != '' ? $findedarray[0]['memberused'] : '';
                                $voucherpoints = $findedarray[0]['points'];
                                $exp_date = $findedarray[0]['voucherexpiry'];
                                $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
                                $translatedstring = $voucher_code_to_redeem;
                                if ($memberused == '') {
                                    if ($exp_date != '') {
                                        $expiration_date = strtotime($exp_date);
                                        if ($expiration_date > $today) {
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
                                            $wpdb->update($table_name, array('points' => $voucherpoints, 'vouchercode' => $voucher_code_to_redeem, 'vouchercreated' => $vouchercreated, 'voucherexpiry' => $exp_date, 'memberused' => get_current_user_id()), array('id' => $findedarray[0]['id']));                                           
                                        } else {
                                            echo addslashes(get_option('rs_voucher_code_expired_error_message'));
                                        }
                                    } else {
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
                                             $wpdb->update($table_name, array('points' => $voucherpoints, 'vouchercode' => $voucher_code_to_redeem, 'vouchercreated' => $vouchercreated, 'voucherexpiry' => $exp_date, 'memberused' => get_current_user_id()), array('id' => $findedarray[0]['id']));
                                        }
                                } else {
                                    echo addslashes(get_option('rs_voucher_code_used_error_message'));
                                }
                            }
                        }
                    }
                }
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

        public static function rs_function_for_current_available_points() {
            if (is_user_logged_in()) {
                ?>
                <style type="text/css">
                    #current_points_caption{
                        font-size: 20px;
                        margin-left: auto;
                        margin-right: auto;
                        width: 50%;
                    }
                </style>    
                <?php
                $userid = get_current_user_id();
                $getusermeta = RSPointExpiry::get_sum_of_total_earned_points($userid) + get_user_meta($userid, '_my_reward_points', true);
                if ($getusermeta != '') {
                    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                    echo "<br/><br/><br/><div id='current_points_caption'><b>" . get_option('rs_current_available_balance_caption') . "</b>" . " " . round(number_format((float) $getusermeta, 2, '.', ''), $roundofftype) . "</div>";
                } else {
                    echo "<br/><br/><br/><div id='current_points_caption'><b>" . get_option('rs_current_available_balance_caption') . "</b>" . " " . "0" . "</div>";
                }
            }
        }      

    }

    RSFunctionForOfflineOnlineRewards::init();
}