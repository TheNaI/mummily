<?php

/*
 * Trigger this upon plugin install
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('RSInstall')) {

    class RSInstall {

        private static $dbversion = '1.2.3';

        public static function init() {

            add_filter('cron_schedules', array(__CLASS__, 'set_up_rs_cron'));

            add_action('rscronjob', array(__CLASS__, 'main_function_for_mail_sending'));
        }

        public static function get_charset_table() {
            global $wpdb;
            $charset_collate = '';
            if ($wpdb->has_cap('collation')) {
                $charset_collate = $wpdb->get_charset_collate();
            }
            return $charset_collate;
        }

        public static function create_table_for_point_expiry() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            if (self::rs_check_table_exists($table_name)) {
                $charset_collate = self::get_charset_table();
                $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		earnedpoints FLOAT,
                usedpoints FLOAT,
                expiredpoints FLOAT,
                userid INT(99),
                earneddate VARCHAR(999) NOT NULL,
                expirydate VARCHAR(999) NOT NULL,
                checkpoints VARCHAR(999) NOT NULL,
                orderid INT(99),
                totalearnedpoints INT(99),
                totalredeempoints INT(99),
                reasonindetail VARCHAR(999),
         	UNIQUE KEY id (id)
                ) $charset_collate;";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta($sql);
                add_option('rs_point_expiry', self::$dbversion);
            }
        }

        public static function rs_update_null_value_to_zero() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $querys = $wpdb->get_results("SELECT id,usedpoints FROM $table_name WHERE usedpoints IS NULL", ARRAY_A);
            foreach ($querys as $query) {
                $wpdb->update($table_name, array('usedpoints' => 0), array('id' => $query['id']));
            }
        }

        public static function create_table_to_record_earned_points_and_redeem_points() {

            global $wpdb;
            $getdbversiondata = get_option("rs_record_points") != 'false' ? get_option('rs_record_points') : "0";
            $table_name = $wpdb->prefix . 'rsrecordpoints';
            if (self::rs_check_table_exists($table_name)) {
                $charset_collate = self::get_charset_table();

                $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		earnedpoints FLOAT,
                redeempoints FLOAT,
                userid INT(99),
                earneddate VARCHAR(999) NOT NULL,
                expirydate VARCHAR(999) NOT NULL,
                checkpoints VARCHAR(999) NOT NULL,
                earnedequauivalentamount INT(99),
                redeemequauivalentamount INT(99),
                orderid INT(99),
                productid INT(99),
                variationid INT(99),
                refuserid INT(99),
                reasonindetail VARCHAR(999),
                totalpoints INT(99),
                showmasterlog VARCHAR(999),
                showuserlog VARCHAR(999),
                nomineeid INT(99),
                nomineepoints INT(99),
         	UNIQUE KEY id (id)
                ) $charset_collate;";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta($sql);
                add_option('rs_record_points', self::$dbversion);
                if (self::rs_check_column_exists($table_name, 'totalpoints')) {
                    $wpdb->query("ALTER TABLE $table_name MODIFY totalpoints FLOAT ");
                }
            }
        }

        public static function create_table_for_gift_voucher() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rsgiftvoucher';
            if (self::rs_check_table_exists($table_name)) {
                $charset_collate = self::get_charset_table();
                $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		vouchercode VARCHAR(999) NOT NULL   ,
                points FLOAT,
                vouchercreated VARCHAR(999) NOT NULL,
                voucherexpiry VARCHAR(999) NOT NULL,
                memberused VARCHAR(999) NOT NULL,
         	UNIQUE KEY id (id)
                ) $charset_collate;";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta($sql);
            }
        }

        public static function create_table_for_email_template() {
            global $wpdb;
            $getdbversiondata = get_option("rs_email_template_version") != 'false' ? get_option('rs_email_template_version') : "0";
            $table_name = $wpdb->prefix . 'rs_templates_email';
            if (self::rs_check_table_exists($table_name)) {
                $charset_collate = self::get_charset_table();
                $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                template_name LONGTEXT NOT NULL,
                sender_opt VARCHAR(10) NOT NULL DEFAULT 'woo',
                from_name LONGTEXT NOT NULL,
                from_email LONGTEXT NOT NULL,
                subject LONGTEXT NOT NULL,
                message LONGTEXT NOT NULL,
                earningpoints LONGTEXT NOT NULL,
                redeemingpoints LONGTEXT NOT NULL,
                mailsendingoptions LONGTEXT NOT NULL,
                rsmailsendingoptions LONGTEXT NOT NULL,
                minimum_userpoints LONGTEXT NOT NULL,
                sendmail_options VARCHAR(10) NOT NULL DEFAULT '1',
                sendmail_to LONGTEXT NOT NULL,
                sending_type VARCHAR(20) NOT NULL,
                rs_status VARCHAR(20) NOT NULL DEFAULT 'DEACTIVATE',
                UNIQUE KEY id (id)
                ) $charset_collate;";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta($sql);
                add_option('rs_email_template_version', self::$dbversion);
            }
        }

        public static function create_table_for_encash_reward_points() {
            global $wpdb;
            $getdbversiondata = get_option("rs_encash_version") != 'false' ? get_option('rs_encash_version') : "0";
            $table_name = $wpdb->prefix . 'sumo_reward_encashing_submitted_data';
            if (self::rs_check_table_exists($table_name)) {
                $charset_collate = self::get_charset_table();
                $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                userid INT(225),
                userloginname VARCHAR(200),
                pointstoencash VARCHAR(200),
                pointsconvertedvalue VARCHAR(200),
                encashercurrentpoints VARCHAR(200),
                reasonforencash LONGTEXT,
                encashpaymentmethod VARCHAR(200),
                paypalemailid VARCHAR(200),
                otherpaymentdetails LONGTEXT,
                status VARCHAR(200),
                date VARCHAR(300),
                UNIQUE KEY id (id)
                ) $charset_collate;";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta($sql);
                add_option('rs_encash_version', self::$dbversion);
            }
        }

        public static function create_table_for_send_points() {
            global $wpdb;
            $getdbversiondata = get_option("rs_send_points_version") != 'false' ? get_option('rs_send_points_version') : "0";
            $table_name = $wpdb->prefix . 'sumo_reward_send_point_submitted_data';
            if (self::rs_check_table_exists($table_name)) {
                $charset_collate = self::get_charset_table();
                $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                userid INT(225),
                userloginname VARCHAR(200),
                pointstosend VARCHAR(200),
                sendercurrentpoints VARCHAR(200),
                status VARCHAR(200),
                selecteduser LONGTEXT NOT NULL,
                date VARCHAR(300),
                UNIQUE KEY id (id)
                ) $charset_collate;";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta($sql);
                add_option('rs_send_points_version', self::$dbversion);
            }
        }

        public static function insert_data_for_email_template() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rs_templates_email';

            $email_temp_check = $wpdb->get_results("SELECT * FROM $table_name", OBJECT);
            if (empty($email_temp_check)) {
                return $wpdb->insert($table_name, array(
                            'template_name' => 'Default',
                            'sender_opt' => 'woo',
                            'from_name' => 'Admin',
                            'from_email' => get_option('admin_email'),
                            'subject' => 'SUMO Rewards Point',
                            'message' => 'Hi {rsfirstname} {rslastname}, <br><br> You have Earned Reward Points: {rspoints} on {rssitelink}  <br><br> You can use this Reward Points to make discounted purchases on {rssitelink} <br><br> Thanks',
                            'minimum_userpoints' => '0',
                            'mailsendingoptions' => '2',
                            'rsmailsendingoptions' => '3',
                ));
            }
            return;
        }

        public static function install() {
            self::create_table_for_point_expiry();
            self::create_table_to_record_earned_points_and_redeem_points();
            self::create_table_for_email_template();
            self::create_table_for_gift_voucher();
            self::create_table_for_encash_reward_points();
            self::create_table_for_send_points();
            self::rs_update_null_value_to_zero();
            self::insert_data_for_email_template();
            self::create_cron_job();
            self::default_value_for_earning_and_redeem_points();
        }

        public static function set_up_rs_cron($schedules) {
            $interval = get_option('rs_mail_cron_time');
            if (get_option('rs_mail_cron_type') == 'minutes') {
                $interval = $interval * 60;
            } else if (get_option('rs_mail_cron_type') == 'hours') {
                $interval = $interval * 3600;
            } else if (get_option('rs_mail_cron_type') == 'days') {
                $interval = $interval * 86400;
            }
            $schedules['rshourly'] = array(
                'interval' => $interval,
                'display' => 'RS Hourly'
            );
            return $schedules;
        }

        public static function create_cron_job() {
            wp_clear_scheduled_hook('rscronjob');
            delete_option('rscheckcronsafter');
            if (wp_next_scheduled('rscronjob') == false) {
                wp_schedule_event(time(), 'rshourly', 'rscronjob');
            }
        }

        /*
         * Function for send mail based on cron time
         */

        public static function main_function_for_mail_sending() {
            global $wpdb;
            global $woocommerce;
            $point_control = wc_format_decimal(get_option('rs_redeem_point'));
            $point_control_price = wc_format_decimal(get_option('rs_redeem_point_value'));
            $emailtemplate_table_name = $wpdb->prefix . 'rs_templates_email';
            $email_templates = $wpdb->get_results("SELECT * FROM $emailtemplate_table_name"); //all email templates
            if (is_array($email_templates)) {
                foreach ($email_templates as $emails) {
                    if ($emails->rs_status == "ACTIVE") {
                        if ($emails->mailsendingoptions == '1') {
                            if (get_option('rsemailtemplates' . $emails->id) != '1') {
                                if ($emails->sendmail_options == '1') {
                                    if ($emails->rsmailsendingoptions == '3') {
                                        $checksendingmailoptions = 1;
                                        $maindta = $checksendingmailoptions + get_option('rscheckcronsafter');
                                        $newdatavalues = update_option('rscheckcronsafter', $maindta);

                                        if (get_option('rscheckcronsafter') > 1) {
                                            //if()
                                            foreach (get_users() as $myuser) {
                                                $user = get_userdata($myuser->ID);
                                                $user_wmpl_lang = get_user_meta($myuser->ID, 'rs_wpml_lang', true);
                                                if (empty($user_wmpl_lang)) {
                                                    $user_wmpl_lang = 'en';
                                                }
                                                $to = $user->user_email;
                                                $subject = RSWPMLSupport::fp_rs_get_wpml_text('rs_template_' . $emails->id . '_subject', $user_wmpl_lang, $emails->subject);
                                                $firstname = $user->user_firstname;
                                                $lastname = $user->user_lastname;
                                                $url_to_click = "<a href=" . site_url() . ">" . site_url() . "</a>";
                                                if (get_user_meta($myuser->ID, 'unsub_value', true) != 'yes') {
                                                    $userpoint = RSPointExpiry::get_sum_of_total_earned_points($myuser->ID);
                                                    $revised_amount = $userpoint * $point_control_price;
                                                    $coupon_value_in_points = $revised_amount / $point_control;
                                                    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                                    $coupon_value_in_points = get_woocommerce_formatted_price(round($coupon_value_in_points, $roundofftype));
                                                    $userpoint = round($userpoint, $roundofftype);
                                                    $minimumuserpoints = $emails->minimum_userpoints;
                                                    if ($minimumuserpoints == '') {
                                                        $minimumuserpoints = 0;
                                                    } else {
                                                        $minimumuserpoints = $emails->minimum_userpoints;
                                                    }
                                                    if ($minimumuserpoints < $userpoint) {

                                                        $message = RSWPMLSupport::fp_rs_get_wpml_text('rs_template_' . $emails->id . '_message', $user_wmpl_lang, $emails->message);
                                                        $message = str_replace('{rssitelink}', $url_to_click, $message);
                                                        $message = str_replace('{rsfirstname}', $firstname, $message);
                                                        $message = str_replace('{rslastname}', $lastname, $message);
                                                        $message = str_replace('{rspoints}', $userpoint, $message);
                                                        if (strpos($message, '{rs_points_in_currency}') !== false) {
                                                            $message = str_replace('{rs_points_in_currency}', $userpoint . '(' . $coupon_value_in_points . ')', $message);
                                                        }
                                                        $message = do_shortcode($message); //shortcode feature
                                                        ob_start();
                                                        wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                                        echo $message;
                                                        wc_get_template('emails/email-footer.php');
                                                        $woo_temp_msg = ob_get_clean();
                                                        $headers = "MIME-Version: 1.0\r\n";
                                                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                                        if ($emails->sender_opt == 'local') {
                                                            $headers .= "From: " . $emails->from_name . " <" . $emails->from_email . ">\r\n";
                                                        } else {
                                                            $headers .= "From: " . get_option('woocommerce_email_from_name') . " <" . get_option('woocommerce_email_from_address') . ">\r\n";
                                                        }
                                                        //wp_mail($to, $subject, $woo_temp_msg, $headers='');
                                                        if ('2' == get_option('rs_select_mail_function')) {
                                                            $mailer = WC()->mailer();

                                                            if ($mailer->send($to, $subject, $woo_temp_msg, $headers)) {
                                                                
                                                            }
                                                        } elseif ('1' == get_option('rs_select_mail_function')) {
                                                            if (mail($to, $subject, $woo_temp_msg, $headers)) {
                                                                
                                                            }
                                                        } else {
                                                            $mailer = WC()->mailer();
                                                            if ($mailer->send($to, $subject, $woo_temp_msg, $headers = '')) {
                                                                
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if ($emails->rsmailsendingoptions == '3') {
                                        $emailusers = unserialize($emails->sendmail_to);
                                        $checksendingmailoptions = 1;
                                        $maindta = $checksendingmailoptions + get_option('rscheckcronsafter');
                                        $newdatavalues = update_option('rscheckcronsafter', $maindta);

                                        if (get_option('rscheckcronsafter') > 1) {
                                            foreach ($emailusers as $myuser) {
                                                $user = get_userdata($myuser);
                                                $user_wmpl_lang = get_user_meta($myuser, 'rs_wpml_lang', true);
                                                if (empty($user_wmpl_lang)) {
                                                    $user_wmpl_lang = 'en';
                                                }
                                                $to = $user->user_email;
                                                $subject = RSWPMLSupport::fp_rs_get_wpml_text('rs_template_' . $emails->id . '_subject', $user_wmpl_lang, $emails->subject);
                                                $firstname = $user->user_firstname;
                                                $lastname = $user->user_lastname;
                                                $url_to_click = site_url();
                                                $userpoint = RSPointExpiry::get_sum_of_total_earned_points($myuser);
                                                if (get_user_meta($myuser, 'unsub_value', true) != 'yes') {
                                                    $revised_amount = $userpoint * $point_control_price;
                                                    $coupon_value_in_points = $revised_amount / $point_control;
                                                    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                                    $coupon_value_in_points = get_woocommerce_formatted_price(round($coupon_value_in_points, $roundofftype));
                                                    $userpoint = round($userpoint, $roundofftype);
                                                    $minimumuserpoints = $emails->minimum_userpoints;
                                                    if ($minimumuserpoints == '') {
                                                        $minimumuserpoints = 0;
                                                    } else {
                                                        $minimumuserpoints = $emails->minimum_userpoints;
                                                    }
                                                    if ($minimumuserpoints < $userpoint) {
                                                        $message = RSWPMLSupport::fp_rs_get_wpml_text('rs_template_' . $emails->id . '_message', $user_wmpl_lang, $emails->message);
                                                        $message = str_replace('{rssitelink}', $url_to_click, $message);
                                                        $message = str_replace('{rsfirstname}', $firstname, $message);
                                                        $message = str_replace('{rslastname}', $lastname, $message);
                                                        $message = str_replace('{rspoints}', $userpoint, $message);
                                                        if (strpos($message, '{rs_points_in_currency}') !== false) {
                                                            $message = str_replace('{rs_points_in_currency}', $userpoint . '(' . $coupon_value_in_points . ')', $message);
                                                        }
                                                        $message = do_shortcode($message); //shortcode feature
                                                        ob_start();
                                                        wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                                        echo $message;
                                                        wc_get_template('emails/email-footer.php');
                                                        $woo_temp_msg = ob_get_clean();
                                                        $headers = "MIME-Version: 1.0\r\n";
                                                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                                        if ($emails->sender_opt == 'local') {
                                                            $headers .= "From: " . $emails->from_name . " <" . $emails->from_email . ">\r\n";
                                                        } else {
                                                            $headers .= "From: " . get_option('woocommerce_email_from_name') . " <" . get_option('woocommerce_email_from_address') . ">\r\n";
                                                        }
                                                        if ('2' == get_option('rs_select_mail_function')) {
                                                            if ($mailer->send($to, $subject, $woo_temp_msg, $headers)) {
                                                                
                                                            }
                                                        } elseif ('1' == get_option('rs_select_mail_function')) {
                                                            if (mail($to, $subject, $woo_temp_msg, $headers)) {
                                                                
                                                            }
                                                        } else {
                                                            $mailer = WC()->mailer();
                                                            if ($mailer->send($to, $subject, $woo_temp_msg, $headers = '')) {
                                                                
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                update_option('rsemailtemplates' . $emails->id, '1');
                            }
                        } else {
                            if ($emails->sendmail_options == '1') {
                                if ($emails->rsmailsendingoptions == '3') {
                                    $checksendingmailoptions = 1;
                                    $maindta = $checksendingmailoptions + get_option('rscheckcronsafter');
                                    $newdatavalues = update_option('rscheckcronsafter', $maindta);

                                    if (get_option('rscheckcronsafter') > 1) {
                                        foreach (get_users() as $myuser) {
                                            $user = get_userdata($myuser->ID);
                                            $user_wmpl_lang = get_user_meta($myuser->ID, 'rs_wpml_lang', true);
                                            if (empty($user_wmpl_lang)) {
                                                $user_wmpl_lang = 'en';
                                            }
                                            $to = $user->user_email;
                                            $subject = RSWPMLSupport::fp_rs_get_wpml_text('rs_template_' . $emails->id . '_subject', $user_wmpl_lang, $emails->subject);
                                            $firstname = $user->user_firstname;
                                            $lastname = $user->user_lastname;
                                            $url_to_click = "<a href=" . site_url() . ">" . site_url() . "</a>";
                                            if (get_user_meta($myuser->ID, 'unsub_value', true) != 'yes') {
                                                $userpoint = RSPointExpiry::get_sum_of_total_earned_points($myuser->ID);
                                                $revised_amount = $userpoint * $point_control_price;
                                                $coupon_value_in_points = $revised_amount / $point_control;
                                                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                                $coupon_value_in_points = get_woocommerce_formatted_price(round($coupon_value_in_points, $roundofftype));
                                                $userpoint = round($userpoint, $roundofftype);
                                                $minimumuserpoints = $emails->minimum_userpoints;
                                                if ($minimumuserpoints == '') {
                                                    $minimumuserpoints = 0;
                                                } else {
                                                    $minimumuserpoints = $emails->minimum_userpoints;
                                                }
                                                if ($minimumuserpoints < $userpoint) {


                                                    $message = RSWPMLSupport::fp_rs_get_wpml_text('rs_template_' . $emails->id . '_message', $user_wmpl_lang, $emails->message);
                                                    $message = str_replace('{rssitelink}', $url_to_click, $message);
                                                    $message = str_replace('{rsfirstname}', $firstname, $message);
                                                    $message = str_replace('{rslastname}', $lastname, $message);
                                                    $message = str_replace('{rspoints}', $userpoint, $message);
                                                    if (strpos($message, '{rs_points_in_currency}') !== false) {
                                                        $message = str_replace('{rs_points_in_currency}', $userpoint . '(' . $coupon_value_in_points . ')', $message);
                                                    }
                                                    $message = do_shortcode($message); //shortcode feature
                                                    ob_start();
                                                    wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                                    echo $message;
                                                    wc_get_template('emails/email-footer.php');
                                                    $woo_temp_msg = ob_get_clean();
                                                    $headers = "MIME-Version: 1.0\r\n";
                                                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                                    if ($emails->sender_opt == 'local') {
                                                        $headers .= "From: " . $emails->from_name . " <" . $emails->from_email . ">\r\n";
                                                    } else {
                                                        $headers .= "From: " . get_option('woocommerce_email_from_name') . " <" . get_option('woocommerce_email_from_address') . ">\r\n";
                                                    }
                                                    //wp_mail($to, $subject, $woo_temp_msg, $headers='');
                                                    if ('2' == get_option('rs_select_mail_function')) {
                                                        $mailer = WC()->mailer();
                                                        if ($mailer->send($to, $subject, $woo_temp_msg, $headers)) {
                                                            
                                                        }
                                                    } elseif ('1' == get_option('rs_select_mail_function')) {
                                                        if (mail($to, $subject, $woo_temp_msg, $headers)) {
                                                            
                                                        }
                                                    } else {
                                                        $mailer = WC()->mailer();
                                                        if ($mailer->send($to, $subject, $woo_temp_msg, $headers = '')) {
                                                            
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($emails->rsmailsendingoptions == '3') {
                                    $emailusers = unserialize($emails->sendmail_to);
                                    $checksendingmailoptions = 1;
                                    $maindta = $checksendingmailoptions + get_option('rscheckcronsafter');
                                    $newdatavalues = update_option('rscheckcronsafter', $maindta);

                                    if (get_option('rscheckcronsafter') > 1) {
                                        foreach ($emailusers as $myuser) {
                                            $user = get_userdata($myuser);
                                            $user_wmpl_lang = get_user_meta($myuser, 'rs_wpml_lang', true);
                                            if (empty($user_wmpl_lang)) {
                                                $user_wmpl_lang = 'en';
                                            }
                                            $to = $user->user_email;
                                            $subject = RSWPMLSupport::fp_rs_get_wpml_text('rs_template_' . $emails->id . '_subject', $user_wmpl_lang, $emails->subject);
                                            $firstname = $user->user_firstname;
                                            $lastname = $user->user_lastname;
                                            $url_to_click = site_url();
                                            $userpoint = RSPointExpiry::get_sum_of_total_earned_points($myuser);
                                            if (get_user_meta($myuser, 'unsub_value', true) != 'yes') {
                                                $revised_amount = $userpoint * $point_control_price;
                                                $coupon_value_in_points = $revised_amount / $point_control;
                                                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                                $coupon_value_in_points = get_woocommerce_formatted_price(round($coupon_value_in_points, $roundofftype));
                                                $userpoint = round($userpoint, $roundofftype);
                                                $minimumuserpoints = $emails->minimum_userpoints;
                                                if ($minimumuserpoints == '') {
                                                    $minimumuserpoints = 0;
                                                } else {
                                                    $minimumuserpoints = $emails->minimum_userpoints;
                                                }
                                                if ($minimumuserpoints < $userpoint) {
                                                    $message = RSWPMLSupport::fp_rs_get_wpml_text('rs_template_' . $emails->id . '_message', $user_wmpl_lang, $emails->message);
                                                    $message = str_replace('{rssitelink}', $url_to_click, $message);
                                                    $message = str_replace('{rsfirstname}', $firstname, $message);
                                                    $message = str_replace('{rslastname}', $lastname, $message);
                                                    $message = str_replace('{rspoints}', $userpoint, $message);
                                                    if (strpos($message, '{rs_points_in_currency}') !== false) {
                                                        $message = str_replace('{rs_points_in_currency}', $userpoint . '(' . $coupon_value_in_points . ')', $message);
                                                    }
                                                    $message = do_shortcode($message); //shortcode feature
                                                    ob_start();
                                                    wc_get_template('emails/email-header.php', array('email_heading' => $subject));
                                                    echo $message;
                                                    wc_get_template('emails/email-footer.php');
                                                    $woo_temp_msg = ob_get_clean();
                                                    $headers = "MIME-Version: 1.0\r\n";
                                                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                                                    if ($emails->sender_opt == 'local') {
                                                        $headers .= "From: " . $emails->from_name . " <" . $emails->from_email . ">\r\n";
                                                    } else {
                                                        $headers .= "From: " . get_option('woocommerce_email_from_name') . " <" . get_option('woocommerce_email_from_address') . ">\r\n";
                                                    }
                                                    if ('2' == get_option('rs_select_mail_function')) {
                                                        $mailer = WC()->mailer();
                                                        if ($mailer->send($to, $subject, $woo_temp_msg, $headers)) {
                                                            
                                                        }
                                                    } elseif ('1' == get_option('rs_select_mail_function')) {
                                                        if (mail($to, $subject, $woo_temp_msg, $headers)) {
                                                            
                                                        }
                                                    } else {
                                                        $mailer = WC()->mailer();
                                                        if ($mailer->send($to, $subject, $woo_temp_msg, $headers = '')) {
                                                            
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
public static function rs_check_column_exists($table_name, $column_name) {
    global $wpdb;
    $data_base = constant('DB_NAME');
    $column_exists = $wpdb->query("select * from information_schema.columns where table_schema='$data_base' and table_name = '$table_name' and column_name = '$column_name'");
    if ($column_exists <= 0) {
        return true;
    }
    return false;
}
public static function rs_check_table_exists($table_name) {
    global $wpdb;
    $data_base = constant('DB_NAME');
    $column_exists = $wpdb->query("select * from information_schema.columns where table_schema='$data_base' and table_name = '$table_name'");
    if ($column_exists <= 0) {
        return true;
    }
    return false;
}
        public static function default_value_for_earning_and_redeem_points() {
            add_option('rs_earn_point', '1');
            add_option('rs_earn_point_value', '1');
            add_option('rs_redeem_point', '1');
            add_option('rs_redeem_point_value', '1');
            add_option('rs_redeem_point_for_cash_back', '1');
            add_option('rs_redeem_point_value_for_cash_back', '1');
        }

    }

    RSInstall::init();
}
