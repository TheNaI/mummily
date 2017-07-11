<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForMail')) {

    class RSFunctionForMail {

        public static function init() {

            add_action('update_option_rs_mail_cron_type', array(__CLASS__, 'rs_cron_job_setting'));

            add_action('update_option_rs_mail_cron_time', array(__CLASS__, 'rs_cron_job_setting'));
        }

        public static function rs_cron_job_setting() {
            wp_clear_scheduled_hook('rscronjob');
            delete_option('rscheckcronsafter');
            if (wp_next_scheduled('rscronjob') == false) {
                wp_schedule_event(time(), 'rshourly', 'rscronjob');
            }
        }

    }

    RSFunctionForMail::init();
}