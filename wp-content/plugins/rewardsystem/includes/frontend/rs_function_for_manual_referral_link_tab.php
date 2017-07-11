<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForManualReferralLink')) {

    class RSFunctionForManualReferralLink {

        public static function init() {

            add_action('wp_head', array(__CLASS__, 'life_time_referral_link'));
        }

        public static function life_time_referral_link() {

            $userid = get_current_user_id();
            $once_time = get_post_meta($userid, 'reward_manuall_referaal_link', true);
            if ($once_time != '1') {
                if (isset($_COOKIE['rsreferredusername'])) {
                    if (is_user_logged_in()) {
                        if (get_option('rs_enable_referral_link_for_life_time') == 'yes') {
                            if (get_option('rs_generate_referral_link_based_on_user') == '1') {
                                $referredusername = get_user_by('login', $_COOKIE['rsreferredusername']);
                                $refereduserid = $referredusername->ID;
                            } else {
                                $referredusername = get_userdata($_COOKIE['rsreferredusername']);
                                $refereduserid = $referredusername->ID;
                            }
                            $userid = get_current_user_id();
                            $getoveralllog = get_option('rewards_dynamic_rule_manual');
                            if ($userid != $refereduserid) {
                                if (!empty($getoveralllog)) {
                                    $boolvalue = self::life_time_bool_value($refereduserid, $userid);
                                    if ($boolvalue != 'false') {
                                        $merge[] = array('referer' => esc_html($referredusername->ID), 'refferal' => esc_html($userid), 'type' => 'Automatic');
                                        $logmerge = array_merge((array) $getoveralllog, $merge);
                                        update_option('rewards_dynamic_rule_manual', $logmerge);
                                    }
                                } else {
                                    $merge[] = array('referer' => esc_html($referredusername->ID), 'refferal' => esc_html($userid), 'type' => 'Automatic');
                                    update_option('rewards_dynamic_rule_manual', $merge);
                                }
                            }
                        }
                    }
                    update_post_meta($userid, 'reward_manuall_referaal_link', 1);
                }
            }
        }

        public static function life_time_bool_value($refuserid, $userid) {
            $getoveralllog = get_option('rewards_dynamic_rule_manual');
            foreach ($getoveralllog as $value) {
                if (($value['referer'] == $refuserid) && ($value['refferal'] == $userid)) {
                    $userid = get_current_user_id();
                    if ($value['referer'] != $userid) {
                        return true;
                    }
                } else {
                    return false;
                }
            }
        }

    }

    RSFunctionForManualReferralLink::init();
}