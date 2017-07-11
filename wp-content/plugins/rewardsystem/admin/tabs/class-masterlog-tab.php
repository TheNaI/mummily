<?php
/*
 * Master Log Tab
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSMasterLog')) {

    class RSMasterLog {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_masterlog', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_masterlog', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));

            add_action('woocommerce_admin_field_rs_select_users_master_log', array(__CLASS__, 'rs_select_user_to_export_master_log'));

            add_action('admin_head', array(__CLASS__, 'rs_add_chosen_to_masterlog_tab'));

            add_action('wp_ajax_rs_selected_user_type', array(__CLASS__, 'process_ajax_to_select_user_type'));

            add_action('wp_ajax_rssplitusertoexportlog', array(__CLASS__, 'process_ajax_to_export_log'));

            add_action('woocommerce_admin_field_rs_masterlog', array(__CLASS__, 'rs_list_all_points_log'));

            add_action('wp_ajax_rs_export_masterlog_option', array(__CLASS__, 'selected_option_masterlog_export_callback'));

            add_action('wp_ajax_rs_list_of_users_masterlog_export', array(__CLASS__, 'selected_users_for_export_masterlog_callback'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_masterlog'] = __('Master Log', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;
            return apply_filters('woocommerce_rewardsystem_myaccount_settings', array(
                array(
                    'name' => __('MasterLog Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_masterlog_setting',
                ),
                array(
                    'name' => __('Export Master Log Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_system_export_masterlog_csv'
                ),
                array(
                    'name' => __('Export Master Log for', 'rewardsystem'),
                    'desc' => __('Here you can set whether to Export Master Log for All Users or Selected Users', 'rewardsystem'),
                    'id' => 'rs_export_import_masterlog_option',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'options' => array('1' => 'All Users', '2' => 'Selected Users'),
                    'newids' => 'rs_export_import_masterlog_option',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Select the users that you wish to Export Master Log', 'rewardsystem'),
                    'desc' => __('Here you select the users to whom you wish to Export the Master Log', 'rewardsystem'),
                    'id' => 'rs_export_masterlog_users_list',
                    'css' => 'min-width:400px;',
                    'std' => '',
                    'default' => '',
                    'type' => 'rs_select_users_master_log',
                    'newids' => 'rs_export_masterlog_users_list',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_reward_system_export_masterlog_csv'),
                array(
                    'type' => 'rs_masterlog',
                ),
                array('type' => 'sectionend'),
                array('type' => 'sectionend', 'id' => 'rs_masterlog_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSMasterLog::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSMasterLog::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSMasterLog::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function rs_select_user_to_export_master_log() {
            $field_id = "rs_export_masterlog_users_list";
            $field_label = "Select the users that you wish to Export Master Log";
            $getuser = get_option('rs_export_masterlog_users_list');
            echo rs_function_to_add_field_for_user_select($field_id, $field_label, $getuser);
        }

        public static function rs_add_chosen_to_masterlog_tab() {
            global $woocommerce;
            global $wpdb;
            $table_name = $wpdb->prefix . 'rsrecordpoints';
            if (isset($_GET['page'])) {
                if ($_GET['page'] == 'rewardsystem_callback') {
                    if (isset($_GET['tab'])) {
                        if ($_GET['tab'] == 'rewardsystem_masterlog') {
                            ?>
                            <?php
                            if ((float) $woocommerce->version <= (float) ('2.2.0')) {
                                echo rs_common_chosen_function('#rs_export_masterlog_users_list');
                            }
                        }
                    }
                }
            }
            ?>
            <?php
            if (isset($_GET['tab'])) {
                if ($_GET['tab'] == 'rewardsystem_masterlog') {
                    echo rs_common_ajax_function_to_select_user('rs_export_masterlog_users_list');
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            if ((jQuery('input[name=rs_export_import_masterlog_option]:checked').val()) === '2') {
                                jQuery('#rs_export_masterlog_users_list').parent().parent().show();
                            } else {
                                jQuery('#rs_export_masterlog_users_list').parent().parent().hide();
                            }
                            jQuery('input[name=rs_export_import_masterlog_option]:radio').change(function () {
                                jQuery('#rs_export_masterlog_users_list').parent().parent().toggle();
                            });
                            jQuery(document).ready(function () {
                                var selected_masterlog_option = jQuery('input[name="rs_export_import_masterlog_option"]').val();
                                var masterlog_data = {
                                    action: "rs_export_masterlog_option",
                                    export_masterlog_type: selected_masterlog_option,
                                };
                                jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', masterlog_data, function (response) {
                                    console.log('Got this from the server: ' + response);
                                });
                                jQuery('input[name="rs_export_import_masterlog_option"]').change(function () {
                                    var selected_masterlog_option = jQuery(this).val();
                                    var masterlog_data = {
                                        action: "rs_export_masterlog_option",
                                        export_masterlog_type: selected_masterlog_option,
                                    };
                                    jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', masterlog_data, function (response) {
                                        console.log('Got this from the server: ' + response);
                                    });
                                });
                            });
                            jQuery(document).ready(function () {
                                jQuery('#rs_export_masterlog_users_list').change(function () {
                                    var selected_users_mastelog = jQuery(this).val();
                                    var selected_users_masterlog_param = {
                                        action: "rs_list_of_users_masterlog_export",
                                        selected_users_masterlog_export: selected_users_mastelog
                                    };
                                    jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', selected_users_masterlog_param, function (response) {
                                        console.log('Got this from the server: ' + response);
                                    });
                                });
                            });

                            jQuery('#rs_export_master_log_csv1').hide();
                            jQuery('.gif_rs_sumo_reward_button_for_masterlog').css('display', 'none');
                            jQuery('#rs_export_master_log_csv').click(function () {
                                jQuery('.gif_rs_sumo_reward_button_for_masterlog').css('display', 'inline-block');
                                var selectedoption = jQuery("input:radio[name=rs_export_import_masterlog_option]:checked").val();
                                var selected_users_type = ({
                                    action: "rs_selected_user_type",
                                    selectedoption: selectedoption
                                });
                                function getDatatoexport(id) {
                                    return jQuery.ajax({
                                        type: 'POST',
                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                        data: ({
                                            action: "rssplitusertoexportlog",
                                            ids: id
                                        }),
                                        success: function (response) {
                                            response = jQuery.trim(response);
                                            if (response === 'success') {
                                                jQuery('#rs_export_master_log_csv1').trigger('click');
                                                jQuery('.gif_rs_sumo_reward_button_for_masterlog').css('display', 'none');
                                            }
                                        },
                                        dataType: 'json',
                                        async: false
                                    });
                                }
                                jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', selected_users_type,
                                        function (response) {
                                            console.log(response);
                                            if (response != 'success') {
                                                var j = 1;
                                                var i, j, temparray, chunk = 10;
                                                for (i = 0, j = response.length; i < j; i += chunk) {
                                                    temparray = response.slice(i, i + chunk);
                                                    getDatatoexport(temparray);
                                                    console.log(temparray);
                                                }
                                                jQuery.when(getDatatoexport("")).done(function (a1) {
                                                    console.log('Ajax Done Successfully');
                                                });
                                            }
                                        }, 'json');
                            });

                        });
                    </script>

                    <?php
                }
            }
        }

        public static function process_ajax_to_select_user_type() {
            if (isset($_POST['selectedoption'])) {
                delete_option('rs_data_to_export');
                if ($_POST['selectedoption'] == '1') {
                    $alluser = get_users();
                    foreach ($alluser as $users) {
                        $userid[] = $users->ID;
                    }
                    echo json_encode($userid);
                } else if ($_POST['selectedoption'] == '2') {
                    $selecteduser = get_option('rs_selected_userlist_masterlog_export');
                    if (is_array($selecteduser)) {
                        $selecteduser = $selecteduser;
                    } else {
                        $selecteduser = explode(',', $selecteduser);
                    }
                    foreach ($selecteduser as $users) {
                        $userid[] = $users;
                    }
                    echo json_encode($userid);
                }
            }
            exit();
        }

        public static function process_ajax_to_export_log() {
            if (isset($_POST['ids']) && !empty($_POST['ids'])) {
                global $wpdb, $woocommerce;
                $i = 1;
                $table_name = $wpdb->prefix . 'rsrecordpoints';
                $userids = $_POST['ids'];
                $data = array();
                $userid = implode(',', $userids);
                $datas = $wpdb->get_results("SELECT * FROM $table_name WHERE userid in ($userid)", ARRAY_A);
                $datas = $datas + (array) get_option('rsoveralllog');
                if (is_array($datas) && !empty($datas)) {
                    foreach ($datas as $values) {
                        if ($i % 2 != 0) {
                            $name = 'alternate';
                        } else {
                            $name = '';
                        }
                        if ($values != '') {
                            if (isset($values['earnedpoints'])) {
                                $orderid = $values['orderid'];
                                if ((float) $woocommerce->version <= (float) ('2.2.0')) {
                                    $order = new WC_Order($orderid);
                                } else {
                                    $order = wc_get_order($orderid);
                                }
                                $checkpoints = $values['checkpoints'];
                                $productid = $values['productid'];
                                $variationid = $values['variationid'];
                                $userid = $values['userid'];
                                $username = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($values['userid'], 'nickname');
                                $refuserid = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($values['refuserid'], 'nickname');
                                $reasonindetail = $values['reasonindetail'];
                                $redeempoints = $values['redeempoints'];
                                $masterlog = true;
                                $earnpoints = $values['earnedpoints'];
                                $user_deleted = true;
                                $order_status_changed = true;
                                $csvmasterlog = true;
                                $nominatedpoints = $values['nomineepoints'];
                                $nomineeid = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($values['nomineeid'], 'nickname');
                                $usernickname = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($values['userid'], 'nickname');
                                $eventname = RSPointExpiry::rs_function_to_display_log($csvmasterlog, $user_deleted, $order_status_changed, $earnpoints, $order, $checkpoints, $productid, $orderid, $variationid, $userid, $refuserid, $reasonindetail, $redeempoints, $masterlog, $nomineeid, $usernickname, $nominatedpoints);
                            } else {
                                if (!empty($values['totalvalue'])) {
                                    if (get_option('rs_round_off_type') == '1') {
                                        $total = $values['totalvalue'];
                                    } else {
                                        $total = number_format($values['totalvalue']);
                                    }
                                } else {
                                    $total = $values['totalvalue'];
                                }

                                $getusernickname_masterlog_exp = get_user_meta($values['userid'], 'nickname', true);
                                if ($getusernickname_masterlog_exp == '') {
                                    $getusernickname_masterlog_exp = $values['userid'];
                                }

                                $username = $getusernickname_masterlog_exp;
                                $earnpoints = $total;
                                $redeempoints = $total;
                                $eventname = $values['eventname'];
                                $values['earneddate'] = $values['date'];
                            }
                            if (get_option('rs_dispaly_time_format') == '1') {
                                $dateformat = "d-m-Y h:i:s A";
                                $update_start_date = is_numeric($values['earneddate']) ? date_i18n($dateformat, $values['earneddate']) : $values['earneddate'];
                            } else {
                                $timeformat = get_option('time_format');
                                $dateformat = get_option('date_format') . ' ' . $timeformat;
                                $update_start_date = is_numeric($values['earneddate']) ? date_i18n($dateformat, $values['earneddate']) : $values['earneddate'];
                                $update_start_date = strftime($update_start_date);
                            }

                            $data[] = array(
                                'user_name' => $username,
                                'points' => $earnpoints != '0' ? $earnpoints : $redeempoints,
                                'event' => $eventname,
                                'date' => $update_start_date,
                            );
                        }
                    }
                    $olddata = (array) get_option('rs_data_to_export');
                    $currentdata = $data;
                    $mergedata = array_merge($olddata, $currentdata);
                    update_option('rs_data_to_export', $mergedata);
                } else {
                    $olddata = get_option('rs_data_to_export');
                    $currentdata = $data;
                    $mergedata = array_merge($olddata, $currentdata);
                    update_option('rs_data_to_export', $mergedata);
                    echo json_encode(array("success"));
                }
            } else {
                echo json_encode(array("success"));
            }
            exit();
        }

        public static function outputCSV($data) {
            $output = fopen("php://output", "w");
            if (is_array($data) && !empty($data)) {
                foreach ($data as $row) {
                    if ($row != false) {
                        fputcsv($output, $row); // here you can change delimiter/enclosure
                    }
                }
            }
            fclose($output);
        }

        public static function selected_option_masterlog_export_callback() {
            global $wpdb; // this is how you get access to the database
            if (isset($_POST['export_masterlog_type'])) {
                $export_masterloguser_type_value = $_POST['export_masterlog_type'];
                update_option('selected_user_type_masterlog', $export_masterloguser_type_value);
            }
            exit();
        }

        public static function selected_users_for_export_masterlog_callback() {
            global $wpdb; // this is how you get access to the database
            $rs_selected_users_export_masterlog = $_POST['selected_users_masterlog_export'];
            if (!is_array($rs_selected_users_export_masterlog)) {
                $rs_selected_users_export_masterlog = explode(',', $rs_selected_users_export_masterlog);
            }
            update_option('rs_selected_userlist_masterlog_export', $rs_selected_users_export_masterlog);
        }

        public static function rs_list_all_points_log() {
            ?>

            <style type="text/css">
                p.sumo_reward_points {
                    display:none;
                }
                #mainforms {
                    display:none;
                }
            </style>

            <?php
            if (isset($_POST['rs_export_master_log_csv1'])) {
                $export_masterlog_heading = "Username,Points,Event,Date" . "\n";
                ob_end_clean();
                header("Content-type: text/csv");
                $dateformat = get_option('date_format');
                header("Content-Disposition: attachment; filename=reward_points_masterlog " . date_i18n('Y-m-d') . ".csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                echo $export_masterlog_heading;
                $data = get_option('rs_data_to_export');
                self::outputCSV($data);
                exit();
            }
            $newwp_list_table_for_users = new WP_List_Table_for_Master_Log();
            $newwp_list_table_for_users->prepare_items();
            $plugin_url = WP_PLUGIN_URL;
            echo '<tr valign ="top">
            <td class="forminp forminp-select">
                <input type="button" id="rs_export_master_log_csv" name="rs_export_master_log_csv" value="Export Master Log as CSV"/>
                <img class="gif_rs_sumo_reward_button_for_masterlog" src="' . $plugin_url . '/rewardsystem/admin/images/update.gif" style="width:32px;height:32px;position:absolute"/>         
                <input type="submit" id="rs_export_master_log_csv1" name="rs_export_master_log_csv1" value="Export Master Log as CSV1"/>
            </td>
        </tr></p>';
            $newwp_list_table_for_users->search_box('Search', 'search_id');

            $newwp_list_table_for_users->display();
        }

    }

    RSMasterLog::init();
}