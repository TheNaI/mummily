<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForSms')) {

    class RSFunctionForSms {

        public static function send_sms_twilio_api($order_id) {
            global $woocommerce;
            require_once "Twilio.php";
            $order_id = new WC_order($order_id);
            $ord_obj = rs_get_order_obj($order_id);
            $user_id = $ord_obj['order_userid'];
            $phone_number = get_user_meta($user_id, 'billing_phone', true);
            if (strpos($phone_number, '+') !== false) {
                $banning_type = FPRewardSystem::check_banning_type($user_id);
                if ($banning_type != 'earningonly' && $banning_type != 'both') {
                    $AccountSid = get_option('rs_twilio_secret_account_id');
                    $AuthToken = get_option('rs_twilio_auth_token_id');
                    $client = new Services_Twilio($AccountSid, $AuthToken);
                    $people = array(
                        $phone_number => $user_login,
                    );
                    $message_replaced_link = self::common_function_for_message($user_id);
                    foreach ($people as $number => $name) {
                        $sms = $client->account->messages->sendMessage(
                                get_option('rs_twilio_from_number'),
                                // the number we are sending to - Any phone number
                                $phone_number,
                                // the sms body
                                $message_replaced_link
                        );
                    }
                }
            }
        }

        public static function send_sms_nexmo_api($order_id) {
            global $woocommerce;
            include_once ( "NexmoMessage.php" );
            $order_id = new WC_order($order_id);
            $ord_obj = rs_get_order_obj($order_id);
            $user_id = $ord_obj['order_userid'];
            $phone_number = get_user_meta($user_id, 'billing_phone', true);
            if (strpos($phone_number, '+') !== false) {
                echo "valid Phone number";
                $banning_type = FPRewardSystem::check_banning_type($user_id);
                if ($banning_type != 'earningonly' && $banning_type != 'both') {
                    $nexmo_sms = new NexmoMessage(get_option('rs_nexmo_key'), get_option('rs_nexmo_secret'));
                    $message_replaced_link = self::common_function_for_message($user_id);
                    $info = $nexmo_sms->sendText($phone_number, 'SUMO Rewards', $message_replaced_link);
                }
            }
        }

        public static function common_function_for_message($user_id) {
            $user_details = get_user_by('id', $user_id);
            $user_login = is_object($user_details) ? $user_details->user_login : 'Guest';
            $user_points = RSPointExpiry::get_sum_of_total_earned_points($user_id);
            $url_to_click = site_url();
            $message_content = get_option('rs_points_sms_content');
            $message_content_name_to_find = "{username}";
            $message_content_name_to_replace = $user_login;
            $message_content_points_to_find = "{rewardpoints}";
            $message_content_points_to_replace = round($user_points);
            $message_content_link_to_find = "{sitelink}";
            $message_content_link_to_replace = $url_to_click;
            $message_replaced_name = str_replace($message_content_name_to_find, $message_content_name_to_replace, $message_content);
            $message_replaced_points = str_replace($message_content_points_to_find, $message_content_points_to_replace, $message_replaced_name);
            $message_replaced_link = str_replace($message_content_link_to_find, $message_content_link_to_replace, $message_replaced_points);
            return $message_replaced_link;
        }

    }

}