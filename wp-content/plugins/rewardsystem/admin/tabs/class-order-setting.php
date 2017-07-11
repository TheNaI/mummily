<?php
/*
 * Order Tab Setting
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSOrder')) {

    class RSOrder {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_order', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_order', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings')); // call the init function to update the default settings on page load

            add_action('add_meta_boxes', array(__CLASS__, 'add_meta_box_for_earned'));

            add_action('fp_action_to_reset_settings_rewardsystem_order', array(__CLASS__, 'rs_function_to_reset_order_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_order'] = __('Order', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            return apply_filters('woocommerce_rewardsystem_order_settings', array(
                array(
                    'name' => __('Order Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_order_setting'
                ),
                array(
                    'name' => __('Points Earned in Current Order in Order Details page', 'rewardsystem'),
                    'id' => 'rs_show_hide_total_points_order_field',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'newids' => 'rs_show_hide_total_points_order_field',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Points Earned in Order Label', 'rewardsystem'),
                    'id' => 'rs_total_earned_point_caption_checkout',
                    'css' => 'min-width:150px;',
                    'std' => 'Points that can be earned:',
                    'default' => 'Points that can be earned:',
                    'type' => 'text',
                    'newids' => 'rs_total_earned_point_caption_checkout',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Display Earned Points', 'rewardsystem'),
                    'desc' => __('Enable Message for Earned Points', 'rewardsystem'),
                    'id' => 'rs_enable_msg_for_earned_points',
                    'newids' => 'rs_enable_msg_for_earned_points',
                    'class' => 'rs_enable_msg_for_earned_points',
                    'type' => 'checkbox',
                ),
                array(
                    'name' => __('Message to display Earned Points', 'rewardsystem'),
                    'desc' => __('Message to display Earned Points', 'rewardsystem'),
                    'id' => 'rs_msg_for_earned_points',
                    'newids' => 'rs_msg_for_earned_points',
                    'class' => 'rs_msg_for_earned_points',
                    'css' => 'min-width:550px;',
                    'std' => 'Points Earned in this Order [earnedpoints]',
                    'default' => 'Points Earned in this Order [earnedpoints]',
                    'type' => 'textarea',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Display Redeemed Points', 'rewardsystem'),
                    'desc' => __('Enable Message for Redeem Points', 'rewardsystem'),
                    'id' => 'rs_enable_msg_for_redeem_points',
                    'newids' => 'rs_enable_msg_for_redeem_points',
                    'class' => 'rs_enable_msg_for_redeem_points',
                    'type' => 'checkbox',
                ),
                array(
                    'name' => __('Message to Redeemed Points', 'rewardsystem'),
                    'desc' => __('Message to Redeemed Points', 'rewardsystem'),
                    'id' => 'rs_msg_for_redeem_points',
                    'newids' => 'rs_msg_for_redeem_points',
                    'class' => 'rs_msg_for_redeem_points',
                    'css' => 'min-width:550px;',
                    'std' => 'Points Redeemed in this Order [redeempoints]',
                    'default' => 'Points Redeemed in this Order [redeempoints]',
                    'type' => 'textarea',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_order_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSOrder::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSOrder::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSOrder::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function add_meta_box_for_earned() {
            add_meta_box('order_earned_points', 'Earned Point and Redeem Points For Current Order', array('RSOrder', 'add_meta_box_to_earned_and_redeem_points'), 'shop_order', 'normal', 'low');
        }

        public static function add_meta_box_to_earned_and_redeem_points($order) {
            if (isset($_GET['post'])) {
                $order = $_GET['post'];
                global $wpdb;
                $overall_earned_totals = array();
                $overall_redeem_totals = array();
                $revised_earned_totals = array();
                $revised_redeem_totals = array();
                $orderstatuslistforredeem = array();
                $totalearnedvalue = "";
                $totalredeemvalue = '';
                $table_name = $wpdb->prefix . 'rsrecordpoints';
                $orderid = $order;
                $order_obj = new WC_Order($orderid);
                $ord_obj = rs_get_order_obj($order_obj);
                $user_id = $ord_obj['order_userid'];
                if ($user_id != '' && $user_id != '0') {
                    $orderstatus = $ord_obj['order_status'];
                    $order_status = str_replace('wc-', '', $orderstatus);
                    $getoverallearnpoints = $wpdb->get_results("SELECT earnedpoints FROM $table_name WHERE orderid = $orderid and userid=$user_id and checkpoints != 'RVPFRP'and  checkpoints != 'RVPFRPG'", ARRAY_A);
                    foreach ($getoverallearnpoints as $getoverallearnpointss) {
                        $overall_earned_totals[] = $getoverallearnpointss['earnedpoints'];
                    }
                    $getoverallredeempoints = $wpdb->get_results("SELECT redeempoints FROM $table_name WHERE orderid = $orderid and userid=$user_id and checkpoints != 'RVPFPPRP'", ARRAY_A);
                    foreach ($getoverallredeempoints as $getoverallredeempointss) {
                        $overall_redeem_totals[] = $getoverallredeempointss['redeempoints'];
                    }
                    $getrevisedearnedpoint = $wpdb->get_results("SELECT redeempoints FROM $table_name WHERE checkpoints = 'RVPFPPRP' and userid=$user_id and orderid = $orderid", ARRAY_A);
                    foreach ($getrevisedearnedpoint as $getrevisedearnedpoints) {
                        $revised_earned_totals[] = $getrevisedearnedpoints['redeempoints'];
                    }
                    $getrevisedredeempoints = $wpdb->get_results("SELECT earnedpoints FROM $table_name WHERE orderid = $orderid and userid=$user_id and checkpoints != 'PPRP' and checkpoints != 'PPRRPG' and checkpoints != 'RRP' and checkpoints != 'RPG' and checkpoints != 'RPBSRP'", ARRAY_A);
                    foreach ($getrevisedredeempoints as $getrevisedredeempointss) {
                        $revised_redeem_totals[] = $getrevisedredeempointss['earnedpoints'];
                    }
                    $orderstatuslistforredeem = get_option('rs_order_status_control_redeem');
                    if (in_array($order_status, $orderstatuslistforredeem)) {
                        RSPointExpiry::update_redeem_point_for_user($orderid);
                    }
                    if (get_option('rs_enable_msg_for_earned_points') == 'yes') {
                        if (get_option('rs_enable_msg_for_redeem_points') == 'yes') {
                            $totalearnedvalue = array_sum($overall_earned_totals) - array_sum($revised_earned_totals);
                            $totalredeemvalue = array_sum($overall_redeem_totals) - array_sum($revised_redeem_totals);

                            $msgforearnedpoints = get_option('rs_msg_for_earned_points');
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $replacemsgforearnedpoints = str_replace('[earnedpoints]', $totalearnedvalue != "" ? round($totalearnedvalue, $roundofftype) : "0", $msgforearnedpoints);

                            $msgforredeempoints = get_option('rs_msg_for_redeem_points');
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $replacemsgforredeempoints = str_replace('[redeempoints]', $totalredeemvalue != "" ? round($totalredeemvalue, $roundofftype) : "0", $msgforredeempoints);
                        } else {
                            $totalearnedvalue = array_sum($overall_earned_totals) - array_sum($revised_earned_totals);

                            $msgforearnedpoints = get_option('rs_msg_for_earned_points');
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $replacemsgforearnedpoints = str_replace('[earnedpoints]', $totalearnedvalue != "" ? round($totalearnedvalue, $roundofftype) : "0", $msgforearnedpoints);

                            $msgforredeempoints = get_option('rs_msg_for_redeem_points');
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $replacemsgforredeempoints = str_replace('[redeempoints]', $totalredeemvalue != "" ? round($totalredeemvalue, $roundofftype) : "0", $msgforredeempoints);
                        }
                    } else {

                        $msgforearnedpoints = get_option('rs_msg_for_earned_points');
                        $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                        $replacemsgforearnedpoints = str_replace('[earnedpoints]', $totalearnedvalue != "" ? round($totalearnedvalue, $roundofftype) : "0", $msgforearnedpoints);

                        if (get_option('rs_enable_msg_for_redeem_points') == 'yes') {
                            $totalredeemvalue = array_sum($overall_redeem_totals) - array_sum($revised_redeem_totals);

                            $msgforredeempoints = get_option('rs_msg_for_redeem_points');
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $replacemsgforredeempoints = str_replace('[redeempoints]', $totalredeemvalue != "" ? round($totalredeemvalue, $roundofftype) : "0", $msgforredeempoints);
                        } else {
                            $msgforredeempoints = get_option('rs_msg_for_redeem_points');
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $replacemsgforredeempoints = str_replace('[redeempoints]', $totalredeemvalue != "" ? round($totalredeemvalue, $roundofftype) : "0", $msgforredeempoints);
                        }
                    }
                    if (get_option('rs_enable_msg_for_earned_points') == 'yes') {
                        if (get_option('rs_enable_msg_for_redeem_points') == 'yes') {
                            ?>
                            <table width="100%" style=" border-radius: 10px; border-style: solid; border-color: #dfdfdf;">
                                <tr><td style="text-align:center; background-color:#F1F1F1"><h3>Earned Points</h3></td><td style="text-align:center;background-color:#F1F1F1"><h3>Redeem Points</h3></td></tr>
                                <tr><td style="text-align:center"><?php echo $replacemsgforearnedpoints; ?></td><td style="text-align:center"><?php echo $replacemsgforredeempoints; ?></td></tr>
                            </table>

                            <?php
                        } else {
                            ?>
                            <table width="100%" style=" border-radius: 10px; border-style: solid; border-color: #dfdfdf;">
                                <tr><td style="text-align:center; background-color:#F1F1F1"><h3>Earned Points</h3></td></tr>
                                <tr><td style="text-align:center"><?php echo $replacemsgforearnedpoints; ?></td></tr>
                            </table>

                            <?php
                        }
                    } else {
                        if (get_option('rs_enable_msg_for_redeem_points') == 'yes') {
                            ?>
                            <table width="100%" style=" border-radius: 10px; border-style: solid; border-color: #dfdfdf;">
                                <tr><td style="text-align:center;background-color:#F1F1F1"><h3>Redeem Points</h3></td></tr>
                                <tr><td style="text-align:center"><?php echo $replacemsgforredeempoints; ?></td></tr>
                            </table>

                            <?php
                        }
                    }
                }
            }
        }

        public static function rs_function_to_reset_order_tab() {
            $settings = RSOrder::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

    }

    RSOrder::init();
}