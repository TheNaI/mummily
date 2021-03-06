<?php
/*
 * Import Export Tab Setting
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSImportExport')) {

    class RSImportExport {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_import_export', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_import_export', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));

            add_action('woocommerce_admin_field_rs_import_export_selected_user', array(__CLASS__, 'selected_users_list_import_export'));

            add_action('admin_head', array(__CLASS__, 'ajax_to_export_user_points'));

            add_action('woocommerce_admin_field_import_export', array(__CLASS__, 'reward_system_page_customization'));

            add_action('admin_footer', array(__CLASS__, 'select_custom_date'));

            add_action('admin_footer', array(__CLASS__, 'export_points_selection'));

            add_action('wp_ajax_rssplitusertoimpexp', array(__CLASS__, 'process_ajax_to_impexp_userpoint'));

            add_action('wp_ajax_rs_impexp_point_in_csv', array(__CLASS__, 'process_ajax_to_split_user_to_imp'));

            add_action('wp_ajax_rs_export_option', array(__CLASS__, 'export_option_selected_callback'));

            add_action('wp_ajax_rs_list_of_users_to_export', array(__CLASS__, 'selected_users_for_exporting_csv_callback'));

            add_action('wp_ajax_rs_select_csv_format', array(__CLASS__, 'select_csv_format'));

            add_action('wp_ajax_rs_selected_date_option', array(__CLASS__, 'export_option_selected_date_callback'));

            add_action('wp_ajax_rs_import_export_start_date', array(__CLASS__, 'export_start_date_callback'));

            add_action('wp_ajax_rs_import_export_end_date', array(__CLASS__, 'export_end_date_callback'));

            add_action('fp_action_to_reset_settings_rewardsystem_import_export', array(__CLASS__, 'rs_function_to_reset_import_export_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_import_export'] = __('Import/Export Points in CSV', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;

            return apply_filters('woocommerce_rewardsystem_gift_voucher_settings', array(
                array(
                    'name' => __('Import/Export User Points in CSV Format', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_import_export_setting'
                ),
                array(
                    'name' => __('Export available Points for', 'rewardsystem'),
                    'desc' => __('Here you can set whether to Export Reward Points for All Users or Selected Users', 'rewardsystem'),
                    'id' => 'rs_export_import_user_option',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'options' => array('1' => 'All Users', '2' => 'Selected Users'),
                    'newids' => 'rs_export_import_user_option',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Select the User(s) for whom you wish to Export Points', 'rewardsystem'),
                    'desc' => __('Here you select the users to whom you wish to Export Reward Points', 'rewardsystem'),
                    'id' => 'rs_import_export_users_list',
                    'std' => '',
                    'default' => '',
                    'type' => 'rs_import_export_selected_user',
                    'newids' => 'rs_import_export_users_list',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Users are identified based on', 'rewardsystem'),
                    'desc' => __('Here you can set whether to Export CSV Format with Username or Userid or Emailid', 'rewardsystem'),
                    'id' => 'rs_csv_format',
                    'class' => 'rs_csv_format',
                    'newids' => 'rs_csv_format',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'options' => array('1' => 'Username', '2' => 'Email-Id'),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Export User Points for', 'rewardsystem'),
                    'desc' => __('Here you can set whether to Export Reward Points for All Time or Selected Date', 'rewardsystem'),
                    'id' => 'rs_export_import_date_option',
                    'class' => 'rs_export_import_date_option',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'options' => array('1' => 'All Time', '2' => 'Selected Date'),
                    'newids' => 'rs_export_import_date_option',
                    'desc_tip' => true,
                ),
                array(
                    'type' => 'import_export',
                ),
                array('type' => 'sectionend', 'id' => '_rs_import_export_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSImportExport::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSImportExport::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSImportExport::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function selected_users_list_import_export() {
            $field_id = "rs_import_export_users_list";
            $field_label = "Select the Users that you wish to Export Reward Points";
            $getuser = get_option('rs_import_export_users_list');
            echo rs_function_to_add_field_for_user_select($field_id, $field_label, $getuser);
        }

        public static function ajax_to_export_user_points() {             
            if (isset($_GET['tab'])) {
                if ($_GET['tab'] == 'rewardsystem_import_export') {
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery('#rs_export_user_points_csv1').hide();
                            jQuery('.gif_rs_sumo_reward_button_for_impexp').css('display', 'none');
                            jQuery('#rs_export_user_points_csv').click(function () {
                                jQuery('.gif_rs_sumo_reward_button_for_impexp').css('display', 'inline-block');
                                var usertype = jQuery("input:radio[name=rs_export_import_user_option]:checked").val();
                                var selecteduser = jQuery("#rs_import_export_users_list").val();
                                var dataparam = ({
                                    action: 'rs_impexp_point_in_csv',
                                    usertype: usertype,
                                    selecteduser: selecteduser
                                });
                                function getDataforimp(id) {
                                    return jQuery.ajax({
                                        type: 'POST',
                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                        data: ({
                                            action: "rssplitusertoimpexp",
                                            ids: id,
                                            usertype: usertype,
                                        }),
                                        success: function (response) {
                                            response = jQuery.trim(response);
                                            if (response === 'success') {
                                                jQuery('#rs_export_user_points_csv1').trigger('click');
                                                jQuery('.gif_rs_sumo_reward_button_for_impexp').css('display', 'none');
                                            }
                                        },
                                        dataType: 'json',
                                        async: false
                                    })
                                }
                                jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', dataparam,
                                        function (response) {
                                            console.log(response);
                                            if (response != 'success') {
                                                var j = 1;
                                                var i, j, temparray, chunk = 10;
                                                for (i = 0, j = response.length; i < j; i += chunk) {
                                                    temparray = response.slice(i, i + chunk);
                                                    getDataforimp(temparray);
                                                    console.log(temparray);
                                                }
                                                jQuery.when(getDataforimp("")).done(function (a1) {
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

        public static function reward_system_page_customization() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            if (isset($_POST['rs_import_user_points'])) {
                if ($_FILES["file"]["error"] > 0) {
                    echo "Error: " . $_FILES["file"]["error"] . "<br>";
                } else {
                    $mimes = array('text/csv',
                        'text/plain',
                        'application/csv',
                        'text/comma-separated-values',
                        'application/excel',
                        'application/vnd.ms-excel',
                        'application/vnd.msexcel',
                        'text/anytext',
                        'application/octet-stream',
                        'application/txt');
                    if (in_array($_FILES['file']['type'], $mimes)) {
// do something
                        self::inputCSV($_FILES["file"]["tmp_name"]);
                    } else {
                        ?>
                        <style type="text/css">
                            div.error {
                                display:block;
                            }
                        </style>
                        <?php
                    }
                }
                $myurl = get_permalink();
            }

            if (isset($_POST['rs_import_user_points_old'])) {
                if ($_FILES["file"]["error"] > 0) {
                    echo "Error: " . $_FILES["file"]["error"] . "<br>";
                } else {
                    $mimes = array('text/csv',
                        'text/plain',
                        'application/csv',
                        'text/comma-separated-values',
                        'application/excel',
                        'application/vnd.ms-excel',
                        'application/vnd.msexcel',
                        'text/anytext',
                        'application/octet-stream',
                        'application/txt');
                    if (in_array($_FILES['file']['type'], $mimes)) {
// do something
                        self::inputCSVforold($_FILES["file"]["tmp_name"]);
                    } else {
                        ?>
                        <style type="text/css">
                            div.error {
                                display:block;
                            }
                        </style>
                        <?php
                    }
                }
                $myurl = get_permalink();
            }

            if (isset($_POST['rs_export_user_points_csv1'])) {
                ob_end_clean();
                header("Content-type: text/csv;charset=utf-8");
                $dateformat = get_option('date_format');
                header("Content-Disposition: attachment; filename=reward_points_" . date_i18n('Y-m-d') . ".csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                $data = get_option('rs_data_to_impexp');
                self::outputCSV($data);
                exit();
            }
            ?>


            <style type="text/css">
                p.sumo_reward_points {
                    display:none;
                }
                #mainforms {
                    display:none;
                }
            </style>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_point_export_start_date"><?php _e('Start Date', 'rewardsystem'); ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="text" class="rs_point_export_start_date" value="" name="rs_point_export_start_date" id="rs_point_export_start_date" />
                </td>
            </tr>

            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_point_export_end_date"><?php _e('End Date', 'rewardsystem'); ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="text" class="rs_point_export_end_date" value="" name="rs_point_export_end_date" id="rs_point_export_end_date" />
                </td>
            </tr>
            <tr valign ="top">
                <th class="titledesc" scope="row">
                    <label for="rs_export_user_points_csv"><?php _e('Export User Points to CSV', 'rewardsystem'); ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="button" id="rs_export_user_points_csv" name="rs_export_user_points_csv" value="Export User Points"/>
                    <img class="gif_rs_sumo_reward_button_for_impexp" src="<?php echo WP_PLUGIN_URL; ?>/rewardsystem/admin/images/update.gif" style="width:32px;height:32px;position:absolute"/>
                    <input type="submit" id="rs_export_user_points_csv1" name="rs_export_user_points_csv1" value="Export User Points1"/>
                </td>
            </tr>

            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rs_import_user_points_csv"><?php _e('Import User Points to CSV', 'rewardsystem'); ?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="file" id="rs_import_user_points_csv" name="file" />
                </td>
            </tr>


            <tr valign="top">
                <td class="forminp forminp-select">
                    <input type="submit" id="rs_import_user_points" name="rs_import_user_points" value="Import CSV for Version 10.0 (Above 10.0)"/>
                </td>
                <td class="forminp forminp-select">
                    <input type="submit" id="rs_import_user_points_old" name="rs_import_user_points_old" value="Import CSV for Older Version (Below 10.0)"/>

                </td>

            </tr>

            <?php if (get_option('rewardsystem_csv_array') != '') { ?>
                <table class="wp-list-table widefat fixed posts">
                    <thead>
                        <tr>
                            <th>
                                Username
                            </th>
                            <th>
                                User Reward Points
                            </th>
                            <th>
                                Expiry Date
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        foreach (get_option('rewardsystem_csv_array')as $newcsv) {
                            ?>
                            <tr>
                                <td><?php echo isset($newcsv[0]) && $newcsv[0] != '' ? $newcsv[0] : ''; ?></td><td><?php echo isset($newcsv[1]) && $newcsv[1] != '' ? $newcsv[1] : '0'; ?></td>
                                <td>
                                    <?php
                                    if(isset($newcsv[2])){
                                        $date = $newcsv[2] == '999999999999' ? '-' : date('m/d/Y h:i:s A T', $newcsv[2]);
                                        echo $date;
                                    }else{
                                        echo '-';
                                    }
                                    ?>
                                </td>
                            </tr>

                            <?php
                        }
                        ?>
                    </tbody>
                </table>

                <table>
                    <tr valign="top">
                        <td>
                            <input type="submit" id="rs_new_action_reward_points" name="rs_new_action_reward_points" value="Override Existing User Points"/>
                        </td>
                        <td>
                            <input type="submit" id="rs_exist_action_reward_points" name="rs_exist_action_reward_points" value="Add Points with Already Earned Points"/>
                        </td>
                    </tr>
                </table>
                <?php
            }

            if (isset($_POST['rs_new_action_reward_points'])) {
                $getvalues = get_option('rewardsystem_csv_array');
                if (is_array($getvalues)) {
                    $i = 0;
                    foreach ($getvalues as $newvalues) {
                        $newvalues[0] = isset($newvalues[0]) && $newvalues[0] != '' ? $newvalues[0] : '';                        
                        $datalogin = get_user_by('login', $newvalues[0]);
                        if (!empty($datalogin)) {
                            $usedid = $datalogin->ID;
                            $wpdb->delete($table_name, array('userid' => $usedid));
                        }
                    }
                }
                if (is_array($getvalues)) {
                    $i = 0;
                    foreach ($getvalues as $newvalues) {
                        $newvalues[0] = isset($newvalues[0]) && $newvalues[0] != '' ? $newvalues[0] : '';
                        $newvalues[1] = isset($newvalues[1]) && $newvalues[1] != '' ? $newvalues[1] : '0';                        
                        $newvalues[2] = isset($newvalues[2]) && $newvalues[2] != '' ? date('m/d/Y h:i:s A T', $newvalues[2]) : 999999999999;                        
                        $datalogin = get_user_by('login', $newvalues[0]);
                        if (!empty($datalogin)) {
                            $usedid = $datalogin->ID;
                            if ($i == 0) {
                                // $wpdb->delete($table_name, array('userid' => $usedid));
                            }
                            $pointsredeemedy = RSPointExpiry::get_sum_of_total_earned_points($usedid);
                            $earnpoint = $newvalues[1];
                            $date = $newvalues[2];
                            RSPointExpiry::insert_earning_points($usedid, $earnpoint, '0', $date, 'IMPOVR', $order_id, $totalearnedpoints, $totalredeempoints, '');
                            $equearnamt = RSPointExpiry::earning_conversion_settings($earnpoint);
                            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($usedid);
                            if ($earnpoint == '') {
                                $earnpoint = '0';
                            }
                            RSPointExpiry::record_the_points($usedid, $earnpoint, '0', $date, 'IMPOVR', $equearnamt, $equredeemamt, '0', '0', '0', '0', '', $totalpoints, '', '0');
                        } else {
                            $datalogin = get_user_by('email', $newvalues[0]);
                            if (!empty($datalogin)) {
                                $usedid = $datalogin->ID;
                                if ($i == 0) {
                                    //  $wpdb->delete($table_name, array('userid' => $usedid));
                                }
                                $previouspointss = RSPointExpiry::get_sum_of_total_earned_points($usedid);
                                $earnpoint = $newvalues[1];
                                $date = $newvalues[2];
                                RSPointExpiry::insert_earning_points($usedid, $earnpoint, '0', $date, 'IMPOVR', $order_id, $totalearnedpoints, $totalredeempoints, '');
                                $equearnamt = RSPointExpiry::earning_conversion_settings($earnpoint);
                                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($usedid);
                                if ($earnpoint == '') {
                                    $earnpoint = '0';
                                }
                                RSPointExpiry::record_the_points($usedid, $earnpoint, '0', $date, 'IMPOVR', $equearnamt, $equredeemamt, '0', '0', '0', '0', '', $totalpoints, '', '0');
                            }
                        }
                        $i++;
                    }
                }
                delete_option('rewardsystem_csv_array');
                $redirect = add_query_arg(array('saved' => 'true'));
                wp_safe_redirect($redirect);
                exit();
            }

            if (isset($_POST['rs_exist_action_reward_points'])) {
                $getvalues = get_option('rewardsystem_csv_array');                
                global $wpdb;
                $table_name = $wpdb->prefix . 'rspointexpiry';
                if (is_array($getvalues)) {
                    foreach ($getvalues as $newvalues) {
                        $newvalues[0] = isset($newvalues[0]) && $newvalues[0] != '' ? $newvalues[0] : '';
                        $newvalues[1] = isset($newvalues[1]) && $newvalues[1] != '' ? $newvalues[1] : '0';                        
                        $newvalues[2] = isset($newvalues[2]) && $newvalues[2] != '' ? date('m/d/Y h:i:s A T', $newvalues[2]) : 999999999999;
                        $datalogin = get_user_by('login', $newvalues[0]);
                        if (!empty($datalogin)) {
                            $oldpoints = RSPointExpiry::get_sum_of_total_earned_points($datalogin->ID);
                            $currentpoints = $oldpoints + $newvalues[1];
                            $usedid = $datalogin->ID;
                            $earnpoint = $newvalues[1];
                            $date = $newvalues[2];
                            RSPointExpiry::insert_earning_points($usedid, $earnpoint, '0', $date, 'IMPADD', $order_id, $totalearnedpoints, $totalredeempoints, '');
                            $equearnamt = RSPointExpiry::earning_conversion_settings($earnpoint);
                            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($usedid);
                            if ($earnpoint == '') {
                                $earnpoint = '0';
                            }
                            RSPointExpiry::record_the_points($usedid, $earnpoint, '0', $date, 'IMPADD', $equearnamt, $equredeemamt, '0', '0', '0', '0', '', $totalpoints, '', '0');
                        } else {
                            $datalogin = get_user_by('email', $newvalues[0]);
                            if (!empty($datalogin)) {
                                $oldpoints = RSPointExpiry::get_sum_of_total_earned_points($datalogin->ID);
                                $currentpoints = $oldpoints + $newvalues[1];
                                $usedid = $datalogin->ID;
                                $earnpoint = $newvalues[1];
                                $date = $newvalues[2];
                                RSPointExpiry::insert_earning_points($usedid, $earnpoint, '0', $date, 'IMPADD', $order_id, $totalearnedpoints, $totalredeempoints, '');
                                $equearnamt = RSPointExpiry::earning_conversion_settings($earnpoint);
                                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($usedid);
                                if ($earnpoint == '') {
                                    $earnpoint = '0';
                                }
                                RSPointExpiry::record_the_points($usedid, $earnpoint, '0', $date, 'IMPADD', $equearnamt, $equredeemamt, '0', '0', '0', '0', '', $totalpoints, '', '0');
                            }
                        }
                    }
                }
                delete_option('rewardsystem_csv_array');
                $myurl = get_permalink();
                $redirect = add_query_arg(array('saved' => 'true'));
                wp_safe_redirect($redirect);
                exit();
            }
        }

        public static function select_custom_date() {
            if (isset($_GET['tab'])) {
                if ($_GET['tab'] == 'rewardsystem_import_export') {
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery('#rs_point_export_start_date').datepicker({dateFormat: 'yy-mm-dd'});
                            jQuery('#rs_point_export_end_date').datepicker({dateFormat: 'yy-mm-dd'});
                            jQuery('#rs_point_export_start_date').change(function () {
                                var export_start_date = jQuery('#rs_point_export_start_date').val();
                                var export_param_start_date = {
                                    action: "rs_import_export_start_date",
                                    export_startdate: export_start_date,
                                };

                                jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', export_param_start_date, function (response) {
                                    console.log('Got this from the server: ' + response);
                                });
                            });
                            jQuery('#rs_point_export_end_date').change(function () {
                                var export_end_date = jQuery('#rs_point_export_end_date').val();
                                var export_param_end_date = {
                                    action: "rs_import_export_end_date",
                                    export_enddate: export_end_date,
                                };

                                jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', export_param_end_date, function (response) {
                                    console.log('Got this from the server: ' + response);
                                });
                            });
                        });
                    </script>
                    <?php
                }
            }
        }

        public static function export_points_selection() {
            if (isset($_GET['tab'])) {
                if ($_GET['tab'] == 'rewardsystem_import_export') {
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            var selected_option = jQuery('input[name="rs_export_import_user_option"]').val();
                            var data = {
                                action: "rs_export_option",
                                exporttype: selected_option,
                            };
                            jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', data, function (response) {
                                console.log('Got this from the server: ' + response);
                            });
                            jQuery('input[name="rs_export_import_user_option"]').change(function () {
                                var selected_option = jQuery(this).val();
                                var data = {
                                    action: "rs_export_option",
                                    exporttype: selected_option,
                                };
                                jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', data, function (response) {
                                    console.log('Got this from the server: ' + response);
                                });
                            });
                        });
                        jQuery(document).ready(function () {
                            var selected_users_for_export;
                            selected_users_for_export = jQuery('#rs_import_export_users_list').val();

                            var selected_users_data = {
                                action: "rs_list_of_users_to_export",
                                exportlist: selected_users_for_export
                            };
                            jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', selected_users_data, function (response) {
                                console.log('Got this from the server: ' + response);
                            });
                            jQuery('#rs_import_export_users_list').change(function () {
                                selected_users_for_export = jQuery('#rs_import_export_users_list').val();

                                var selected_users_data = {
                                    action: "rs_list_of_users_to_export",
                                    exportlist: selected_users_for_export
                                };
                                jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', selected_users_data, function (response) {
                                    console.log('Got this from the server: ' + response);
                                });
                            });
                        });
                        jQuery(document).ready(function () {
                            var selected_option_date = jQuery('input[name="rs_export_import_date_option"]').val();
                            var selected_date_option_param = {
                                action: "rs_selected_date_option",
                                dateoption: selected_option_date,
                            };
                            jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', selected_date_option_param, function (response) {
                                console.log('Got this from the server: ' + response);
                            });
                            jQuery('input[name="rs_export_import_date_option"]').change(function () {
                                var selected_option_date = jQuery(this).val();
                                var selected_date_option_param = {
                                    action: "rs_selected_date_option",
                                    dateoption: selected_option_date,
                                };
                                jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', selected_date_option_param, function (response) {
                                    console.log('Got this from the server: ' + response);
                                });
                            });
                        });
                        jQuery(document).ready(function () {
                            var selected_format = jQuery('input[name="rs_csv_format"]').val();
                            var selected_format = {
                                action: "rs_select_csv_format",
                                exportformat: selected_format,
                            };
                            jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', selected_format, function (response) {

                                console.log('Got this from the server: ' + response);
                            });
                            jQuery(document).on('change', '.rs_csv_format', function () {
                                var selected_format = jQuery(this).val();
                                var selected_format = {
                                    action: "rs_select_csv_format",
                                    exportformat: selected_format,
                                };
                                jQuery.post('<?php echo admin_url('admin-ajax.php') ?>', selected_format, function (response) {
                                    console.log('Got this from the server: ' + response);
                                });
                            });

                        });
                    </script>
                    <?php
                }
            }
        }

        public static function ajax_csvformat_common_function($export_csvformat_selection, $user_value, $points, $convertexpiry) {
            if ($export_csvformat_selection == '1') {
                $data = array(
                    'user_name' => $user_value->user_login,
                    'points' => $points != '' ? $points : 0,
                    'date' => $convertexpiry != '' ? $convertexpiry : '',
                );
            } else {
                $data = array(
                    'user_name' => $user_value->user_email,
                    'points' => $points != '' ? $points : 0,
                    'date' => $convertexpiry != '' ? $convertexpiry : '',
                );
            }
            return $data;
        }

        public static function ajax_common_function_with_date($total_points, $user_value, $export_csvformat_selection) {
            $date_from_log = "";
            $points_from_log = "";
            if (is_array($total_points) && !empty($total_points)) {
                foreach ($total_points as $new_total_points) {
                    if (is_array($new_total_points) && !empty($new_total_points)) {
                        if (isset($new_total_points['earneddate'])) {
                            $date_from_log = $new_total_points['earneddate'];
                        }
                        if (isset($new_total_points['totalpoints'])) {
                            $points_from_log = $new_total_points['totalpoints'];
                        }
                        $converted_time = $date_from_log;
                        $selected_start_date = get_option('selected_start_date');
                        $selected_start_time = '00:00:00';
                        $selected_start_date_time = $selected_start_date . ' ' . $selected_start_time;
                        $converted_start_time = strtotime($selected_start_date_time);
                        $selected_end_date = get_option('selected_end_date');
                        $selected_end_time = '23:59:00';
                        $selected_end_date_time = $selected_end_date . ' ' . $selected_end_time;
                        $converted_end_time = strtotime($selected_end_date_time);
                        if ($converted_start_time <= $converted_time && $converted_end_time >= $converted_time) {
                            $earnpoints = $new_total_points['earnedpoints'] - $new_total_points['usedpoints'];
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $points = round($earnpoints, $roundofftype);
                            $expirydate = $new_total_points['expirydate'];
                            if ($expirydate != 999999999999) {
                                $timeformat = get_option('time_format');
                                $dateformat = get_option('date_format') . ' ' . $timeformat;
                                $stringto_time = strtotime($expirydate);
                                 if($stringto_time==false){
                                   $stringto_time=$expirydate;
                               }
                                $convertexpiry = date_i18n($dateformat, $stringto_time);
                            } else {
                                $convertexpiry = '-';
                            }
                            $data = self::ajax_csvformat_common_function($export_csvformat_selection, $user_value, $points, $convertexpiry);
                        } else {
                            $data = self::ajax_csvformat_common_function($export_csvformat_selection, $user_value, $points = '', $convertexpiry = '');
                        }
                    }
                }
            }
            return $data;
        }

        public static function ajax_common_function($userid, $table_name, $export_csvformat_selection) {
            global $wpdb;
            if (is_array($userid) && !empty($userid)) {
                foreach ($userid as $users) {
                    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                    $total_points = $wpdb->get_results("SELECT * FROM $table_name WHERE userid = $users", ARRAY_A);
                    $users = get_user_by('id', $users);
                    if (!empty($total_points)) {
                        foreach ($total_points as $new_total_points) {
                            $earnpoints = $new_total_points['earnedpoints'] - $new_total_points['usedpoints'];
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $points = round($earnpoints, $roundofftype);
                            $expirydate = $new_total_points['expirydate'];
                            if ($expirydate != 999999999999) {
                                $timeformat = get_option('time_format');
                                $dateformat = get_option('date_format') . ' ' . $timeformat;
                                $stringto_time = strtotime($expirydate);
                                 if($stringto_time == false){
                                   $stringto_time = $expirydate;
                               }
                                $convertexpiry = date_i18n($dateformat, $stringto_time);
                            } else {
                                $convertexpiry = '-';
                            }
                            $data[] = self::ajax_csvformat_common_function($export_csvformat_selection, $users, $points, $convertexpiry);
                        }
                    } else {
                        $data[] = self::ajax_csvformat_common_function($export_csvformat_selection, $users, $points = '', $convertexpiry = '');
                    }
                }
            }
            $olddata = get_option('rs_data_to_impexp');
            $currentdata = $data;
            $mergedata = array_merge($olddata, $currentdata);
            update_option('rs_data_to_impexp', $mergedata);
        }

        public static function process_ajax_to_impexp_userpoint() {
            if (isset($_POST['ids']) && !empty($_POST['ids'])) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'rspointexpiry';
                $export_type_selection = $_POST['usertype'];
                $export_date_selection = get_option('selected_date_type');
                $export_csvformat_selection = get_option('selected_format');
                if ($export_type_selection == '1') {
                    if ($export_date_selection == '1') {
                        $userid = $_POST['ids'];
                        if (!is_array($userid)) {
                            $userid = explode(',', $_POST['ids']);
                        }
                        $data = self::ajax_common_function($userid, $table_name, $export_csvformat_selection);
                    } else {
                        $userid = $_POST['ids'];
                        if (!is_array($userid)) {
                            $userid = explode(',', $_POST['ids']);
                        }
                        if (is_array($userid) && !empty($userid)) {
                            foreach ($userid as $user_value) {
                                $date_from_log = "";
                                $points_from_log = "";
                                $updated_total_points = "";
                                $total_points = $wpdb->get_results("SELECT * FROM $table_name WHERE userid = $user_value", ARRAY_A);
                                $user_value = get_user_by('id', $user_value);
                                if ((get_option('selected_start_date') != NULL) && (get_option('selected_end_date') != NULL)) {
                                    if (is_array($total_points)) {
                                        if (!empty($total_points)) {
                                            $data[] = self::ajax_common_function_with_date($total_points, $user_value, $export_csvformat_selection);
                                        } else {
                                            $data[] = self::ajax_csvformat_common_function($export_csvformat_selection, $user_value, $points = '', $convertexpiry = '');
                                        }
                                    }
                                } else {
                                    $userid = $_POST['ids'];
                                    if (!is_array($userid)) {
                                        $userid = explode(',', $_POST['ids']);
                                    }
                                    $data = self::ajax_common_function($userid, $table_name, $export_csvformat_selection);
                                }
                            }
                            $olddata = get_option('rs_data_to_impexp');
                            $currentdata = $data;
                            $mergedata = array_merge($olddata, $currentdata);
                            update_option('rs_data_to_impexp', $mergedata);
                        }
                    }
                } else {
                    if ($export_date_selection == '1') {
                        if (get_option('rs_selected_user_list_export') != NULL) {
                            $userid = $_POST['ids'];
                            if (!is_array($userid)) {
                                $userid = explode(',', $_POST['ids']);
                            }
                            $data = self::ajax_common_function($userid, $table_name, $export_csvformat_selection);
                        } else {
                            $userid = $_POST['ids'];
                            if (!is_array($userid)) {
                                $userid = explode(',', $_POST['ids']);
                            }
                            $data = self::ajax_common_function($userid, $table_name, $export_csvformat_selection);
                        }
                    } else {
                        if (get_option('rs_selected_user_list_export') != NULL) {
                            $userid = $_POST['ids'];
                            if (!is_array($userid)) {
                                $userid = explode(',', $_POST['ids']);
                            }
                            if (is_array($userid) && !empty($userid)) {
                                foreach ($userid as $user_value) {
                                    $total_points = $wpdb->get_results("SELECT * FROM $table_name WHERE userid = $user_value", ARRAY_A);
                                    $user_value = get_user_by('id', $user_value);
                                    if (!empty($total_points)) {
                                        $data[] = self::ajax_common_function_with_date($total_points, $user_value, $export_csvformat_selection);
                                    } else {
                                        $data[] = self::ajax_csvformat_common_function($export_csvformat_selection, $user_value, $points = '', $convertexpiry = '');
                                    }
                                }
                            }
                            $olddata = get_option('rs_data_to_impexp');
                            $currentdata = $data;
                            $mergedata = array_merge($olddata, $currentdata);
                            update_option('rs_data_to_impexp', $mergedata);
                        } else {
                            $userid = $_POST['ids'];
                            if (!is_array($userid)) {
                                $userid = explode(',', $_POST['ids']);
                            }
                            $data = self::ajax_common_function($userid, $table_name, $export_csvformat_selection);
                        }
                    }
                }
            } else {
                echo json_encode(array("success"));
            }
            exit();
        }

        public static function process_ajax_to_split_user_to_imp() {
            if (isset($_POST['usertype'])) {
                delete_option('rs_data_to_impexp');
                update_option('rs_data_to_impexp', array());
                if ($_POST['usertype'] == '1') {
                    $alluser = get_users();
                    foreach ($alluser as $users) {
                        $userid[] = $users->ID;
                    }
                    echo json_encode($userid);
                } else if ($_POST['usertype'] == '2') {
                    $selecteduser = $_POST['selecteduser'];
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

        public static function export_option_selected_callback() {
            global $wpdb; // this is how you get access to the database
            if (isset($_POST['exporttype'])) {
                $export_user_type_value = $_POST['exporttype'];
                update_option('selected_user_type', $export_user_type_value);
            }
            exit();
        }

        public static function selected_users_for_exporting_csv_callback() {
            global $wpdb; // this is how you get access to the database
            if (isset($_POST['exportlist'])) {
                $rs_selected_list_export = $_POST['exportlist'];
                if (!is_array($rs_selected_list_export)) {
                    $rs_selected_list_export = explode(',', $rs_selected_list_export);
                }
                update_option('rs_selected_user_list_export', $rs_selected_list_export);
            }
        }

        public static function outputCSV($data) {
            $output = fopen("php://output", "w");

            foreach ($data as $row) {
                if ($row != false) {
                    fputcsv($output, $row); // here you can change delimiter/enclosure
                }
            }
            fclose($output);
        }

        public static function inputCSV($data_path) {
            $row = 1;
            if (($handle = fopen($data_path, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $row++;
                    $datas = strtotime($data[2]);
                    $datass = $data[2] != 0 ? $datas : '';
                    $collection[] = array_filter(array($data[0], $data[1], $datass));
                }
                update_option('rewardsystem_csv_array', array_merge(array_filter($collection)));
                fclose($handle);
            }
        }

        public static function inputCSVforold($data_path) {
            $row = 1;
            if (($handle = fopen($data_path, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $row++;
                    $datas = strtotime($data[2]);
                    $datass = $data[2] != 0 ? $datas : '';
                    $collection[] = array_filter(array($data[0], $data[1], $datass));
                }
                update_option('rewardsystem_csv_array', array_merge(array_filter($collection)));
                fclose($handle);
            }
        }

        public static function select_csv_format() {
            global $wpdb;
            if (isset($_POST['exportformat'])) {
                $select_export_format = $_POST['exportformat'];
                update_option('selected_format', $select_export_format);
            }
            exit();
        }

        public static function export_option_selected_date_callback() {
            global $wpdb; // this is how you get access to the database
            if (isset($_POST['dateoption'])) {
                $export_selected_date_option = $_POST['dateoption'];
                delete_option('selected_start_date');
                delete_option('selected_end_date');
                update_option('selected_date_type', $export_selected_date_option);
            }
            exit();
        }

        public static function export_start_date_callback() {
            global $wpdb; // this is how you get access to the database
            if (isset($_POST['export_startdate'])) {
                $export_start_date = $_POST['export_startdate'];
                delete_option('selected_start_date');

                update_option('selected_start_date', $export_start_date);
            }
            exit();
        }

        public static function export_end_date_callback() {
            global $wpdb; // this is how you get access to the database
            if (isset($_POST['export_enddate'])) {
                $export_end_date = $_POST['export_enddate'];
                delete_option('selected_end_date');
                update_option('selected_end_date', $export_end_date);
            }
            exit();
        }

        public static function rs_function_to_reset_import_export_tab() {
            $settings = RSImportExport::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

    }

    RSImportExport::init();
}