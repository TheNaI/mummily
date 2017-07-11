<?php
/*
 * Add/Remove Points
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSAddorRemovePoints')) {

    class RSAddorRemovePoints {

        public static function init() {
            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_add_remove_points', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_add_remove_points', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_head', array(__CLASS__, 'ajax_function_for_add'));

            add_action('wp_ajax_no_priv_rsaddpointforuser', array(__CLASS__, 'process_ajax_to_split_users_for_add'));

            add_action('wp_ajax_rsaddpointforuser', array(__CLASS__, 'process_ajax_to_split_users_for_add'));

            add_action('wp_ajax_no_priv_rssplitusertoaddpoints', array(__CLASS__, 'process_ajax_to_add'));

            add_action('wp_ajax_rssplitusertoaddpoints', array(__CLASS__, 'process_ajax_to_add'));

            add_action('admin_head', array(__CLASS__, 'ajax_function_for_remove'));

            add_action('wp_ajax_no_priv_rsremovepointforuser', array(__CLASS__, 'process_ajax_to_split_users_for_remove'));

            add_action('wp_ajax_rsremovepointforuser', array(__CLASS__, 'process_ajax_to_split_users_for_remove'));

            add_action('wp_ajax_no_priv_rssplitusertoremovepoints', array(__CLASS__, 'process_ajax_to_remove'));

            add_action('wp_ajax_rssplitusertoremovepoints', array(__CLASS__, 'process_ajax_to_remove'));

            add_action('woocommerce_admin_field_rs_add_remove_remove_reward_points', array(__CLASS__, 'rs_getting_list_for_add_remove_option'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_add_remove_points'] = __('Add/Remove Reward Points', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            return apply_filters('woocommerce_rewardsystem_add_remove_points_settings', array(
                array(
                    'name' => __('Add/Remove Reward Points Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_add_remove_points_setting',
                ),
                array(
                    'type' => 'rs_add_remove_remove_reward_points',
                ),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSAddorRemovePoints::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSAddorRemovePoints::reward_system_admin_fields());
        }

        public static function ajax_function_for_add() {
            $security = rs_function_to_create_security();
            $isadmin = is_admin() ? 'yes' : 'no';
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('.gif_rs_sumo_reward_button_for_add').css('display', 'none');
                    jQuery('#rs_add_points').click(function () {
                        var enteredpoints = jQuery('#rs_reward_addremove_points').val();
                        var reason = jQuery('#rs_reward_addremove_reason').val();
                        var usertype = jQuery('#rs_select_user_type').val();
                        var includeuser = jQuery('#rs_select_to_include_customers').val();
                        var excludeuser = jQuery('#rs_select_to_exclude_customers').val();
                        var expireddate= jQuery('#rs_expired_date').val();
                        if (enteredpoints == '' && reason == '') {
                            jQuery('.rs_add_remove_points_error').fadeIn();
                            jQuery('.rs_add_remove_points_error').html('Please Enter Points');
                            jQuery('.rs_add_remove_points_error').fadeOut(5000);
                            jQuery('.rs_add_remove_points_reason_error').fadeIn();
                            jQuery('.rs_add_remove_points_reason_error').html('Please Enter Reason');
                            jQuery('.rs_add_remove_points_reason_error').fadeOut(5000);
                            jQuery('.gif_rs_sumo_reward_button_for_add').css('display', 'none');
                            return false;
                        } else if (enteredpoints == '') {
                            jQuery('.rs_add_remove_points_error').fadeIn();
                            jQuery('.rs_add_remove_points_error').html('Please Enter Points');
                            jQuery('.rs_add_remove_points_error').fadeOut(5000);
                            jQuery('.gif_rs_sumo_reward_button_for_add').css('display', 'none');
                            return false;
                        } else if (reason == '') {
                            jQuery('.rs_add_remove_points_reason_error').fadeIn();
                            jQuery('.rs_add_remove_points_reason_error').html('Please Enter Reason');
                            jQuery('.rs_add_remove_points_reason_error').fadeOut(5000);
                            jQuery('.gif_rs_sumo_reward_button_for_add').css('display', 'none');
                            return false;
                        } else {
                            jQuery('.gif_rs_sumo_reward_button_for_add').css('display', 'inline-block');
                            if (jQuery('#rs_select_user_type').val() === '1') {
                                jQuery(this).attr('data-clicked', '1');
                                var dataclicked = jQuery(this).attr('data-clicked');
                                var data = ({
                                    action: 'rsaddpointforuser',
                                    proceed: dataclicked,
                                    usertype: usertype,
                                });
                                function getData(id) {
                                    return jQuery.ajax({
                                        type: 'POST',
                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                        data: ({
                                            action: 'rssplitusertoaddpoints',
                                            ids: id,
                                            points: enteredpoints,
                                            reason: reason,
                                            expireddate:expireddate,
                                            secure: "<?php echo $security; ?>",
                                            state: "<?php echo $isadmin; ?>"
                                                    //proceedanyway: dataclicked
                                        }),
                                        success: function (response) {
                                            if (response) {
                                                if (response.success) {
                                                    jQuery('.rs_add_remove_points').fadeIn();
                                                    jQuery('.rs_add_remove_points').html('Points Successfully added to ' + response.success + ' users');
                                                    jQuery('.rs_add_remove_points').fadeOut(15000);
                                                    jQuery('#rs_reward_addremove_points').val("");
                                                    jQuery('#rs_reward_addremove_reason').val("");
                                                }
                                                if (response.failure) {
                                                    if (response.failure > 0) {
                                                        jQuery('.rs_add_remove_points').fadeIn();
                                                        jQuery('.rs_add_remove_points').html('Points failed to add ' + response.failure + ' user');
                                                        jQuery('.rs_add_remove_points').fadeOut(15000);
                                                    }
                                                }
                                                jQuery('.gif_rs_sumo_reward_button_for_add').css('display', 'none');
                                            }
                                        },
                                        dataType: 'json',
                                        async: false
                                    });
                                }
                                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data,
                                        function (response) {
                                            //console.log(response);
                                            if (response != 'success') {
                                                var j = 1;
                                                var i, j, temparray, chunk = 10;
                                                for (i = 0, j = response.length; i < j; i += chunk) {
                                                    temparray = response.slice(i, i + chunk);
                                                    getData(temparray);
                                                }
                                                jQuery.when(getData()).done(function (a1) {
                                                    console.log('Ajax Done Successfully');
                                                });
                                            }
                                        }, 'json');
                            } else if (jQuery('#rs_select_user_type').val() === '2') {
                                jQuery(this).attr('data-clicked', '1');
                                var dataclicked = jQuery(this).attr('data-clicked');
                                var data = ({
                                    action: 'rsaddpointforuser',
                                    proceed: dataclicked,
                                    usertype: usertype,
                                    includeuser: includeuser,
                                });
                                function getDataforinclude(id) {
                                    return jQuery.ajax({
                                        type: 'POST',
                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                        data: ({
                                            action: 'rssplitusertoaddpoints',
                                            ids: id,
                                            points: enteredpoints,
                                            reason: reason,
                                            expireddate:expireddate,
                                            secure: "<?php echo $security; ?>",
                                            state: "<?php echo $isadmin; ?>"
                                                    //proceedanyway: dataclicked
                                        }),
                                        success: function (response) {
                                            console.log(response);
                                            if (response) {
                                                if (response.success) {
                                                    jQuery('.rs_add_remove_points').fadeIn();
                                                    jQuery('.rs_add_remove_points').html('Points Successfully added to ' + response.success + ' users');
                                                    jQuery('.rs_add_remove_points').fadeOut(15000);
                                                    jQuery('#rs_reward_addremove_points').val("");
                                                    jQuery('#rs_reward_addremove_reason').val("");
                                                }
                                                if (response.failure) {
                                                    if (response.failure > 0) {
                                                        jQuery('.rs_add_remove_points').fadeIn();
                                                        jQuery('.rs_add_remove_points').html('Points failed to add ' + response.failure + ' user');
                                                        jQuery('.rs_add_remove_points').fadeOut(15000);
                                                    }
                                                }
                                                jQuery('.gif_rs_sumo_reward_button_for_add').css('display', 'none');
                                            }
                                        },
                                        dataType: 'json',
                                        async: false
                                    });
                                }
                                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data,
                                        function (response) {
                                            if (response != 'success') {
                                                var j = 1;
                                                var i, j, temparray, chunk = 10;
                                                for (i = 0, j = response.length; i < j; i += chunk) {
                                                    temparray = response.slice(i, i + chunk);
                                                    getDataforinclude(temparray);
                                                    //console.log(temparray);
                                                }
                                                jQuery.when(getDataforinclude()).done(function (a1) {
                                                    console.log('Ajax Done Successfully');
                                                });
                                            }
                                        }, 'json');
                            } else {
                                jQuery(this).attr('data-clicked', '1');
                                var dataclicked = jQuery(this).attr('data-clicked');
                                var data = ({
                                    action: 'rsaddpointforuser',
                                    proceed: dataclicked,
                                    usertype: usertype,
                                    excludeuser: excludeuser,
                                });
                                function getDataforexclude(id) {
                                    return jQuery.ajax({
                                        type: 'POST',
                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                        data: ({
                                            action: 'rssplitusertoaddpoints',
                                            ids: id,
                                            points: enteredpoints,
                                            expireddate:expireddate,
                                            reason: reason,
                                            secure: "<?php echo $security; ?>",
                                            state: "<?php echo $isadmin; ?>"
                                                    //proceedanyway: dataclicked
                                        }),
                                        success: function (response) {
                                            if (response) {
                                                if (response.success) {
                                                    jQuery('.rs_add_remove_points').fadeIn();
                                                    jQuery('.rs_add_remove_points').html('Points Successfully added to ' + response.success + ' users');
                                                    jQuery('.rs_add_remove_points').fadeOut(15000);
                                                    jQuery('#rs_reward_addremove_points').val("");
                                                    jQuery('#rs_reward_addremove_reason').val("");
                                                }
                                                if (response.failure) {
                                                    if (response.failure > 0) {
                                                        jQuery('.rs_add_remove_points').fadeIn();
                                                        jQuery('.rs_add_remove_points').html('Points failed to add ' + response.failure + ' user');
                                                        jQuery('.rs_add_remove_points').fadeOut(15000);
                                                    }
                                                }
                                                jQuery('.gif_rs_sumo_reward_button_for_add').css('display', 'none');
                                            }
                                        },
                                        dataType: 'json',
                                        async: false
                                    });
                                }
                                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data,
                                        function (response) {
                                            //console.log(response);
                                            if (response != 'success') {
                                                var j = 1;
                                                var i, j, temparray, chunk = 10;
                                                for (i = 0, j = response.length; i < j; i += chunk) {
                                                    temparray = response.slice(i, i + chunk);
                                                    getDataforexclude(temparray);
                                                    //console.log(temparray);
                                                }
                                                jQuery.when(getDataforexclude()).done(function (a1) {
                                                    console.log('Ajax Done Successfully');
                                                });
                                            }
                                        }, 'json');
                            }

                        }
                    });
                });
            </script>
            <?php
        }

        public static function process_ajax_to_split_users_for_add() {
            delete_option('fp_successfull_users_to_add');
            delete_option('fp_failed_users_to_add');
            if (isset($_POST['proceed'])) {
                if ($_POST['proceed'] == '1') {
                    if ($_POST['usertype'] == '1') {
                        $array = get_users();
                        foreach ($array as $arrays) {
                            $userid[] = $arrays->ID;
                        }
                        echo json_encode($userid);
                    } else if ($_POST['usertype'] == '2') { 
                        if(is_array($_POST['includeuser'])){
                            $array = $_POST['includeuser'];
                        }else{
                            $array = explode(',', $_POST['includeuser']);
                        }                        
                        foreach ($array as $arrays) {
                            $userid[] = $arrays;
                        }
                        echo json_encode($userid);
                    } else if ($_POST['usertype'] == '3') {
                        if(is_array($_POST['excludeuser'])){
                            $array = $_POST['excludeuser'];
                        }else{
                            $array = explode(',', $_POST['excludeuser']);
                        }
                        $array = explode(',', $_POST['excludeuser']);
                        $alluser = get_users();
                        foreach ($alluser as $users) {
                            $id = $users->ID;
                            if (!in_array($id, $array)) {
                                $userid[] = $id;
                            }
                        }
                        echo json_encode($userid);
                    }
                }
            }
            exit();
        }

        public static function process_ajax_to_add() {
            $verify_security = isset($_POST['secure']) ? rs_function_to_verify_secure($_POST['secure']) : false;
            if (isset($_POST['ids']) && $verify_security && isset($_POST['state']) && $_POST['state'] == 'yes') {
                $array = $_POST['ids'];
                $expired=$_POST['expireddate'];
                $expireddate=strtotime($expired);
                global $woocommerce;
                global $wpdb;
                $table_name = $wpdb->prefix . 'rspointexpiry';
                $enablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                foreach ($array as $arrays) {                    
                    $restrictuserpoints = get_option('rs_max_earning_points_for_user');
                    $user_id = $arrays;                    
                    $rs_new_points_to_add = $_POST['points'];
                    $reasonindetail = $_POST['reason'];
                    $event_slug = 'MAP';
                    $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
                    if ($enablemaxpoints == 'yes') {                        
                        $new_obj->check_point_restriction($restrictuserpoints, $rs_new_points_to_add, $pointsredeemed = '0', $event_slug, $user_id, $nomineeid = '', $referrer_id = '', $productid = '', $variationid = '', $reasonindetail);
                        $list_user_id[] = $user_id;
                    } else {
                        $equearnamt = RSPointExpiry::earning_conversion_settings($rs_new_points_to_add);
                        $valuestoinsert = array('expireddate'=>$expireddate,'manualaddpoints'=>'yes','pointstoinsert' => $rs_new_points_to_add, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $user_id, 'referred_id' => '', 'product_id' => '', 'variation_id' => '', 'reasonindetail' => $reasonindetail, 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $rs_new_points_to_add, 'totalredeempoints' => 0);
                        $new_obj->total_points_management($valuestoinsert);
                        $list_user_id[] = $user_id;
                    }
                }
                $countuser = count($list_user_id);
                $oldcount = get_option('fp_successfull_users_to_add');
                $countusers = $countuser + $oldcount;
                update_option('fp_successfull_users_to_add', $countusers);
            } else {
                $array_response = array('success' => get_option('fp_successfull_users_to_add'), 'failure' => get_option('fp_failed_users_to_add'));
                echo json_encode($array_response);
            }
            exit();
        }

        public static function ajax_function_for_remove() {
            $security = rs_function_to_create_security();
            $isadmin = is_admin() ? 'yes' : 'no';
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('#rs_expired_date').datepicker({dateFormat: 'yy-mm-dd'});                                                      
                    if (jQuery('#rs_reward_select_type').val() == '2') {
                        jQuery('#rs_remove_points').show();
                        jQuery('#rs_add_points').hide();
                        jQuery('#rs_expired_date').parent().parent().hide();

                    } else {
                        jQuery('#rs_remove_points').hide();
                        jQuery('#rs_add_points').show();
                        jQuery('#rs_expired_date').parent().parent().show();

                    }
                    jQuery('#rs_reward_select_type').change(function () {
                        if (jQuery('#rs_reward_select_type').val() == '2') {
                            jQuery('#rs_remove_points').show();
                            jQuery('#rs_add_points').hide();
                            jQuery('#rs_expired_date').parent().parent().hide();

                        } else {
                            jQuery('#rs_remove_points').hide();
                            jQuery('#rs_add_points').show();
                            jQuery('#rs_expired_date').parent().parent().show();

                        }
                    });

                    jQuery('.gif_rs_sumo_reward_button_for_remove').css('display', 'none');
                    jQuery('#rs_remove_points').click(function () {

                        var enteredpoints = jQuery('#rs_reward_addremove_points').val();
                        var reason = jQuery('#rs_reward_addremove_reason').val();
                        var usertype = jQuery('#rs_select_user_type').val();
                        var includeuser = jQuery('#rs_select_to_include_customers').val();
                        var excludeuser = jQuery('#rs_select_to_exclude_customers').val();
                        if (enteredpoints == '' && reason == '') {
                            jQuery('.rs_add_remove_points_error').fadeIn();
                            jQuery('.rs_add_remove_points_error').html('Please Enter Points');
                            jQuery('.rs_add_remove_points_error').fadeOut(5000);
                            jQuery('.rs_add_remove_points_reason_error').fadeIn();
                            jQuery('.rs_add_remove_points_reason_error').html('Please Enter Reason');
                            jQuery('.rs_add_remove_points_reason_error').fadeOut(5000);
                            jQuery('.gif_rs_sumo_reward_button_for_remove').css('display', 'none');
                            return false;
                        } else if (enteredpoints == '') {
                            jQuery('.rs_add_remove_points_error').fadeIn();
                            jQuery('.rs_add_remove_points_error').html('Please Enter Points');
                            jQuery('.rs_add_remove_points_error').fadeOut(5000);
                            jQuery('.gif_rs_sumo_reward_button_for_remove').css('display', 'none');
                            return false;
                        } else if (reason == '') {
                            jQuery('.rs_add_remove_points_reason_error').fadeIn();
                            jQuery('.rs_add_remove_points_reason_error').html('Please Enter Reason');
                            jQuery('.rs_add_remove_points_reason_error').fadeOut(5000);
                            jQuery('.gif_rs_sumo_reward_button_for_remove').css('display', 'none');
                            return false;
                        } else {
                            jQuery('.gif_rs_sumo_reward_button_for_remove').css('display', 'inline-block');
                            if (jQuery('#rs_select_user_type').val() === '1') {
                                jQuery(this).attr('data-clicked', '1');
                                var dataclicked = jQuery(this).attr('data-clicked');
                                var data = ({
                                    action: 'rsremovepointforuser',
                                    proceed: dataclicked,
                                    usertype: usertype
                                });
                                function getData(id) {
                                    return jQuery.ajax({
                                        type: 'POST',
                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                        data: ({
                                            action: 'rssplitusertoremovepoints',
                                            ids: id,
                                            points: enteredpoints,
                                            usertype: usertype,
                                            reason: reason,
                                            secure: "<?php echo $security; ?>",
                                            state: "<?php echo $isadmin; ?>"
                                                    //proceedanyway: dataclicked
                                        }),
                                        success: function (response) {
                                            console.log(response);
                                            if (response) {
                                                if ((response.success > 0) && (response.failure == 0)) {
                                                    jQuery('.rs_add_remove_points').fadeIn();
                                                    if (response.success > 1) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' users');
                                                    } else {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' user');
                                                    }
                                                    jQuery('.rs_add_remove_points').fadeOut(15000);
                                                    jQuery('#rs_reward_addremove_points').val("");
                                                    jQuery('#rs_reward_addremove_reason').val("");
                                                }
                                                if ((response.success > 0) && (response.failure > 0)) {
                                                    jQuery('.rs_add_remove_points').fadeIn();
                                                    if ((response.success > 1) && (response.failure > 1)) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' users.Points failed to remove ' + response.failure + ' users');
                                                    } else if ((response.success == 1) && (response.failure == 1)) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' user.Points failed to remove ' + response.failure + ' user');
                                                    } else if ((response.success == 1) && (response.failure > 1)) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' user.Points failed to remove ' + response.failure + ' users');
                                                    } else if ((response.success > 1) && (response.failure == 1)) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' users.Points failed to remove ' + response.failure + ' user');
                                                    }
                                                    jQuery('.rs_add_remove_points').fadeOut(15000);
                                                }
                                                if ((response.success == 0) && (response.failure > 0)) {
                                                    jQuery('.rs_add_remove_points').fadeIn();
                                                    if (response.failure > 1) {
                                                        jQuery('.rs_add_remove_points').html('Points failed to remove ' + response.failure + ' users');
                                                    } else {
                                                        jQuery('.rs_add_remove_points').html('Points failed to remove ' + response.failure + ' user');
                                                    }
                                                    jQuery('.rs_add_remove_points').fadeOut(15000);
                                                    jQuery('#rs_reward_addremove_points').val("");
                                                    jQuery('#rs_reward_addremove_reason').val("");
                                                }
                                                jQuery('.gif_rs_sumo_reward_button_for_remove').css('display', 'none');
                                            }
                                        },
                                        dataType: 'json',
                                        async: false
                                    });
                                }
                                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data,
                                        function (response) {
                                            console.log(response);
                                            if (response != 'success') {
                                                var j = 1;
                                                var i, j, temparray, chunk = 10;
                                                for (i = 0, j = response.length; i < j; i += chunk) {
                                                    temparray = response.slice(i, i + chunk);
                                                    getData(temparray);
                                                    console.log(temparray);
                                                }
                                                jQuery.when(getData()).done(function (a1) {
                                                    console.log('Ajax Done Successfully');
                                                    //location.reload();
                                                });
                                            }
                                        }, 'json');
                            } else if (jQuery('#rs_select_user_type').val() === '2') {
                                jQuery(this).attr('data-clicked', '1');
                                var dataclicked = jQuery(this).attr('data-clicked');
                                var data = ({
                                    action: 'rsremovepointforuser',
                                    proceed: dataclicked,
                                    usertype: usertype,
                                    includeuser: includeuser
                                });
                                function getDataforinclude(id) {
                                    return jQuery.ajax({
                                        type: 'POST',
                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                        data: ({
                                            action: 'rssplitusertoremovepoints',
                                            ids: id,
                                            points: enteredpoints,
                                            reason: reason,
                                            usertype: usertype,
                                            secure: "<?php echo $security; ?>",
                                            state: "<?php echo $isadmin; ?>"
                                                    //proceedanyway: dataclicked
                                        }),
                                        success: function (response) {
                                            console.log(response);
                                            if (response) {
                                                if ((response.success > 0) && (response.failure == 0)) {
                                                    jQuery('.rs_add_remove_points').fadeIn();
                                                    if (response.success > 1) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' users');
                                                    } else {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' user');
                                                    }
                                                    jQuery('.rs_add_remove_points').fadeOut(15000);
                                                    jQuery('#rs_reward_addremove_points').val("");
                                                    jQuery('#rs_reward_addremove_reason').val("");
                                                }
                                                if ((response.success > 0) && (response.failure > 0)) {
                                                    jQuery('.rs_add_remove_points').fadeIn();
                                                    if ((response.success > 1) && (response.failure > 1)) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' users.Points failed to remove ' + response.failure + ' users');
                                                    } else if ((response.success == 1) && (response.failure == 1)) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' user.Points failed to remove ' + response.failure + ' user');
                                                    } else if ((response.success == 1) && (response.failure > 1)) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' user.Points failed to remove ' + response.failure + ' users');
                                                    } else if ((response.success > 1) && (response.failure == 1)) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' users.Points failed to remove ' + response.failure + ' user');
                                                    }
                                                    jQuery('.rs_add_remove_points').fadeOut(15000);
                                                }
                                                if ((response.success == 0) && (response.failure > 0)) {
                                                    jQuery('.rs_add_remove_points').fadeIn();
                                                    if (response.failure > 1) {
                                                        jQuery('.rs_add_remove_points').html('Points failed to remove ' + response.failure + ' users');
                                                    } else {
                                                        jQuery('.rs_add_remove_points').html('Points failed to remove ' + response.failure + ' user');
                                                    }
                                                    jQuery('.rs_add_remove_points').fadeOut(15000);
                                                    jQuery('#rs_reward_addremove_points').val("");
                                                    jQuery('#rs_reward_addremove_reason').val("");
                                                }
                                                jQuery('.gif_rs_sumo_reward_button_for_remove').css('display', 'none');
                                            }
                                        },
                                        dataType: 'json',
                                        async: false
                                    });
                                }
                                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data,
                                        function (response) {
                                            console.log(response);
                                            if (response != 'success') {
                                                var j = 1;
                                                var i, j, temparray, chunk = 10;
                                                for (i = 0, j = response.length; i < j; i += chunk) {
                                                    temparray = response.slice(i, i + chunk);
                                                    getDataforinclude(temparray);
                                                    console.log(temparray);
                                                }
                                                jQuery.when(getDataforinclude()).done(function (a1) {
                                                    console.log('Ajax Done Successfully');
                                                    //location.reload();
                                                });
                                            }
                                        }, 'json');
                            } else {
                                jQuery(this).attr('data-clicked', '1');
                                var dataclicked = jQuery(this).attr('data-clicked');
                                var data = ({
                                    action: 'rsremovepointforuser',
                                    proceed: dataclicked,
                                    usertype: usertype,
                                    excludeuser: excludeuser
                                });
                                function getDataforexclude(id) {
                                    return jQuery.ajax({
                                        type: 'POST',
                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                        data: ({
                                            action: 'rssplitusertoremovepoints',
                                            ids: id,
                                            points: enteredpoints,
                                            reason: reason,
                                            usertype: usertype,
                                            secure: "<?php echo $security; ?>",
                                            state: "<?php echo $isadmin; ?>"
                                                    //proceedanyway: dataclicked
                                        }),
                                        success: function (response) {
                                            console.log(response);
                                            if (response) {
                                                if ((response.success > 0) && (response.failure == 0)) {
                                                    jQuery('.rs_add_remove_points').fadeIn();
                                                    if (response.success > 1) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' users');
                                                    } else {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' user');
                                                    }
                                                    jQuery('.rs_add_remove_points').fadeOut(15000);
                                                    jQuery('#rs_reward_addremove_points').val("");
                                                    jQuery('#rs_reward_addremove_reason').val("");
                                                }
                                                if ((response.success > 0) && (response.failure > 0)) {
                                                    jQuery('.rs_add_remove_points').fadeIn();
                                                    if ((response.success > 1) && (response.failure > 1)) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' users.Points failed to remove ' + response.failure + ' users');
                                                    } else if ((response.success == 1) && (response.failure == 1)) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' user.Points failed to remove ' + response.failure + ' user');
                                                    } else if ((response.success == 1) && (response.failure > 1)) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' user.Points failed to remove ' + response.failure + ' users');
                                                    } else if ((response.success > 1) && (response.failure == 1)) {
                                                        jQuery('.rs_add_remove_points').html('Points Successfully removed to ' + response.success + ' users.Points failed to remove ' + response.failure + ' user');
                                                    }
                                                    jQuery('.rs_add_remove_points').fadeOut(15000);
                                                }
                                                if ((response.success == 0) && (response.failure > 0)) {
                                                    jQuery('.rs_add_remove_points').fadeIn();
                                                    if (response.failure > 1) {
                                                        jQuery('.rs_add_remove_points').html('Points failed to remove ' + response.failure + ' users');
                                                    } else {
                                                        jQuery('.rs_add_remove_points').html('Points failed to remove ' + response.failure + ' user');
                                                    }
                                                    jQuery('.rs_add_remove_points').fadeOut(15000);
                                                    jQuery('#rs_reward_addremove_points').val("");
                                                    jQuery('#rs_reward_addremove_reason').val("");
                                                }
                                                jQuery('.gif_rs_sumo_reward_button_for_remove').css('display', 'none');
                                            }
                                        },
                                        dataType: 'json',
                                        async: false
                                    });
                                }
                                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data,
                                        function (response) {
                                            console.log(response);
                                            if (response != 'success') {
                                                var j = 1;
                                                var i, j, temparray, chunk = 10;
                                                for (i = 0, j = response.length; i < j; i += chunk) {
                                                    temparray = response.slice(i, i + chunk);
                                                    getDataforexclude(temparray);
                                                    console.log(temparray);
                                                }
                                                jQuery.when(getDataforexclude()).done(function (a1) {
                                                    console.log('Ajax Done Successfully');
                                                    //location.reload();
                                                });
                                            }
                                        }, 'json');
                            }
                        }
                    });
                });
            </script>
            <?php
        }

        public static function process_ajax_to_split_users_for_remove() {
            delete_option('fp_successfull_users_to_remove');
            delete_option('fp_failed_users_to_add_to_remove');
            if (isset($_POST['proceed'])) {
                if ($_POST['proceed'] == '1') {
                    if ($_POST['usertype'] == '1') {
                        $array = get_users();
                        foreach ($array as $arrays) {
                            $userid[] = $arrays->ID;
                        }
                        echo json_encode($userid);
                    } else if ($_POST['usertype'] == '2') {
                         if (is_array($_POST['includeuser'])) {
                            $array = $_POST['includeuser'];
                        } else {
                            $array = explode(',', $_POST['includeuser']);
                        }                     
                        foreach ($array as $arrays) {
                            $userid[] = $arrays;
                        }
                        echo json_encode($userid);
                    } else if ($_POST['usertype'] == '3') {
                         if (is_array($_POST['excludeuser'])) {
                            $array = $_POST['excludeuser'];
                        } else {
                            $array = explode(',', $_POST['excludeuser']);
                        }
                        $alluser = get_users();
                        foreach ($alluser as $users) {
                            $id = $users->ID;
                            if (!in_array($id, $array)) {
                                $userid[] = $id;
                            }
                        }
                        echo json_encode($userid);
                    }
                }
            }
            exit();
        }

        public static function process_ajax_to_remove() {
            $verify_security = isset($_POST['secure']) ? rs_function_to_verify_secure($_POST['secure']) : false;
            if (isset($_POST['ids']) && $verify_security && isset($_POST['state']) && $_POST['state'] == 'yes') {
                if (isset($_POST['usertype'])) {
                    $array = $_POST['ids'];
                    global $woocommerce;
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'rspointexpiry';
                    $enablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
                    foreach ($array as $arrays) {
                        $date = '999999999999';
                        $user_id = $arrays;
                        $my_rewards = $wpdb->get_results("SELECT SUM((earnedpoints-usedpoints)) as availablepoints FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid = $user_id", ARRAY_A);
                        $userpoints = $my_rewards[0]['availablepoints'];
                        $updatedpoints = $userpoints - $_POST['points'];
                        $reasonindetail = $_POST['reason'];
                        $removedpoints = $_POST['points'];
                        if ($removedpoints <= $userpoints) {
                            $pointsredeemed = RSPointExpiry::perform_calculation_with_expiry($removedpoints, $user_id);
                            $equredeemamt = RSPointExpiry::earning_conversion_settings($removedpoints);
                            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_id);
                            RSPointExpiry::record_the_points($user_id, '0', $removedpoints, $date, 'MRP', '', $equredeemamt, '', '', '', '', $reasonindetail, $totalpoints, '', '0');
                            $list_user_id[] = $user_id;
                        }
                    }
                    if ($_POST['usertype'] == '1') {
                        $alluser = count_users();
                        $countuser = $alluser['total_users'];
                        $successcount = isset($list_user_id) ? count($list_user_id) : 0;
                        $failurecount = $countuser - $successcount;
                        $oldcount = get_option('fp_successfull_users_to_remove');
                        $countusers = $countuser + $oldcount;
                        update_option('fp_successfull_users_to_remove', $successcount);
                        update_option('fp_failed_users_to_add_to_remove', $failurecount);
                    } else if ($_POST['usertype'] == '2') {
                        $countuser = count($array);
                        $successcount = isset($list_user_id) ? count($list_user_id) : 0;
                        $failurecount = $countuser - $successcount;
                        $oldcount = get_option('fp_successfull_users_to_remove');
                        $countusers = $countuser + $oldcount;
                        update_option('fp_successfull_users_to_remove', $successcount);
                        update_option('fp_failed_users_to_add_to_remove', $failurecount);
                    } else if ($_POST['usertype'] == '3') {
                        $alluser = count_users();
                        $countuser = $alluser['total_users'];
                        $exccountuser = count($array);
                        $updatedcount = $countuser - $exccountuser;
                        $successcount = isset($list_user_id) ? count($list_user_id) : 0;
                        $failurecount = $updatedcount - $successcount;
                        $oldcount = get_option('fp_successfull_users_to_remove');
                        $countusers = $countuser + $oldcount;
                        update_option('fp_successfull_users_to_remove', $successcount);
                        update_option('fp_failed_users_to_add_to_remove', $failurecount);
                    }
                }
            } else {
                $array_response = array('success' => get_option('fp_successfull_users_to_remove'), 'failure' => get_option('fp_failed_users_to_add_to_remove'));
                echo json_encode($array_response);
            }
            exit();
        }

        public static function rs_getting_list_for_add_remove_option() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            global $woocommerce;
            $enablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
            echo rs_common_ajax_function_to_select_user('rs_select_to_include_customers');
            echo rs_common_ajax_function_to_select_user('rs_select_to_exclude_customers');
            ?>
            <style type="text/css">
                p.sumo_reward_points {
                    display:none;
                }
                #mainforms {
                    display:none;
                }

            </style>        
            <form name="rs_addremove" method="post">
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="rs_select_user_type"><?php _e('User Selection Type', 'rewardsystem'); ?> </label>
                            </th>
                            <td>
                                <select name="rs_select_user_type"  id="rs_select_user_type" class="short rs_select_user_type">
                                    <option value="1"><?php echo __('All User', 'rewardsystem'); ?></option>
                                    <option value="2"><?php echo __('Include User', 'rewardsystem'); ?></option>
                                    <option value="3"><?php echo __('Exclude User', 'rewardsystem'); ?></option>
                                </select>  
                            </td>
                        </tr>
                        <?php
                        $incfield_id = "rs_select_to_include_customers";
                        $incfield_label = "Select to Include Username/Email";
                        $getincuser = get_option('rs_select_to_include_customers');
                        echo rs_function_to_add_field_for_user_select($incfield_id, $incfield_label, $getincuser);

                        $excfield_id = "rs_select_to_exclude_customers";
                        $excfield_label = "Select to Exclude Username/Email";
                        $getexcuser = get_option('rs_select_to_exclude_customers');
                        echo rs_function_to_add_field_for_user_select($excfield_id, $excfield_label, $getexcuser);
                        ?>

                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="rs_reward_addremove_points"><?php _e('Points to Update', 'rewardsystem'); ?></label>
                            </th>
                            <td class="forminp forminp-text">
                                <input type="text" class="" value="" style="min-width:150px;" required='required' id="rs_reward_addremove_points" name="rs_reward_addremove_points"> 	                    
                                <div class='rs_add_remove_points_error' style="color: red;font-size:14px;"></div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="rs_reward_addremove_reason"><?php _e('Reason in Detail'); ?></label>
                            </th>
                            <td class="forminp forminp-text">                          
                                <textarea cols='40' rows='5' name='rs_reward_addremove_reason' id="rs_reward_addremove_reason" required='required'></textarea>
                                <div class='rs_add_remove_points_reason_error' style="color: red;font-size:14px;"></div>
                            </td>
                        </tr>                        
                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="rs_reward_select_reward_point"><?php _e('Selection Type'); ?></label>
                            </th>
                            <td class="forminp forminp-text">
                                <select class="rs_reward_select_type"id="rs_reward_select_type" name="rs_reward_select_type"> 	
                                    <option value="1"><?php _e('Add Points', 'rewardsystem'); ?></option>
                                    <option value="2"><?php _e('Remove Points', 'rewardsystem'); ?></option>
                                </select>                               
                            </td>    
                            </td>
                        </tr>
                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="rs_expired_date"><?php _e('Expires On', 'rewardsystem'); ?></label>
                            </th>
                            <td class="forminp forminp-select">
                                <input type="text" class="rs_expired_date" value="" name="rs_expired_date" id="rs_expired_date" />
                                <span><?php _e('day(s) from the date of adding','rewardsystem');?></span>
                            </td>
                        </tr>
                        <tr valign='top'>
                            <td>
                                <input type='button' name='rs_remove_points' id='rs_remove_points'  class='button-primary' value='Remove Points'/>                            
                                <img class="gif_rs_sumo_reward_button_for_remove" src="<?php echo WP_PLUGIN_URL; ?>/rewardsystem/admin/images/update.gif" style="width:32px;height:32px;position:absolute"/>
                            </td>
                            <td>
                                <input type='button' name='rs_add_points' id='rs_add_points' class='button-primary' value='Add Points'/>
                                <img class="gif_rs_sumo_reward_button_for_add" src="<?php echo WP_PLUGIN_URL; ?>/rewardsystem/admin/images/update.gif" style="width:32px;height:32px;position:absolute"/><br>
                            </td>
                        </tr>
                        <tr valign='top'>
                            <td colspan="2">
                                <div class='rs_add_remove_points' style="color: green;font-size:18px;"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>   
            <?php
        }

    }

    RSAddorRemovePoints::init();
}