<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSfunctionformforSendPoints')) {

    class RSfunctionformforSendPoints {

        public static function init() {
            add_action('wp_ajax_rs_send_form_value', array(__CLASS__, 'save_selected_user'));

            add_action('wp_ajax_nopriv_rs_send_form_value', array(__CLASS__, 'save_selected_user'));

            add_shortcode('rssendpoints', array(__CLASS__, 'frontendformforsendpoints'));

            add_action('wp_ajax_rs_send_form_value', array(__CLASS__, 'process_send_points_to_users'));

            add_action('wp_ajax_rs_sumo_search_users', array(__CLASS__, 'rs_sumo_search_wp_users'));
        }

        public static function rs_sumo_search_wp_users() {
            $json_ids = array();
            $customers_query = new WP_User_Query(array(
                'fields' => 'all',
                'orderby' => 'display_name',
                'search' => '*' . $_REQUEST['term'] . '*',
                'search_columns' => array('ID', 'user_login', 'user_email', 'user_nicename')
            ));
            $customers = $customers_query->get_results();
            $current_user_id = get_current_user_id();
            if (get_option('rs_select_send_points_user_type') == '1') {
                if (!empty($customers)) {
                    foreach ($customers as $customer) {
                        if ($current_user_id != $customer->ID) {
                            $display_user[$customer->ID] = $customer->display_name . ' (#' . $customer->ID . ' - ' . sanitize_email($customer->user_email) . ')';
                        }
                    }
                }
            }
            if (get_option('rs_select_send_points_user_type') == '2') {
                $getusers = get_option('rs_select_users_list_for_send_point');
                if ($getusers != '') {
                    if (!is_array($getusers)) {
                        $userids = array_filter(array_map('absint', (array) explode(',', $getusers)));
                        $display_user = self::rs_function_to_display_select_field($userids, $customers, $current_user_id);
                    } else {
                        $userids = $getusers;
                        $display_user = self::rs_function_to_display_select_field($userids, $customers, $current_user_id);
                    }
                }
            }
            wp_send_json($display_user);
            exit();
        }

        public static function rs_function_to_display_select_field($userids, $customers, $current_user_id) {
            foreach ($userids as $userid) {
                if (!empty($customers)) {
                    foreach ($customers as $customer) {
                        if ($current_user_id != $customer->ID) {
                            if (in_array($customer->ID, $userids)) {
                                $found_customers[$customer->ID] = $customer->display_name . ' (#' . $customer->ID . ' - ' . sanitize_email($customer->user_email) . ')';
                            }
                        }
                    }
                }
            }
            return $found_customers;
        }

        public static function frontendformforsendpoints() {
            if (is_user_logged_in()) {
                wp_enqueue_script('formforsendpoints', false, array(), '', true);
                global $woocommerce;
                global $wp_roles;
                if (get_option('rs_enable_msg_for_send_point') == '1') {
                    if (is_user_logged_in()) {
                        $user_ID = get_current_user_id();
                        if (RSPointExpiry::get_sum_of_total_earned_points($user_ID) > 0) {
                            ob_start();
                            $currentuserpoints = RSPointExpiry::get_sum_of_total_earned_points($user_ID);
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $currentuserpoints = round($currentuserpoints, $roundofftype);
                            $getusers = get_option('rs_select_users_list_for_send_point');
                            ?>
                            <form id="sendpoint_form" method="post" enctype="multipart/form-data">
                                <table>
                                    <tr>
                                        <th>
                                            <label><?php echo get_option("rs_total_send_points_request"); ?></label>
                                        </th>
                                        <td style="border:none;padding: 6px 10px 6px 10px;">
                                            <input style="min-width:250px !important;height:30px !important;" type = "text" id = "rs_total_send_points_request" name = "rs_total_send_points_request" readonly="readonly" value="<?php echo $currentuserpoints; ?>">
                                            <div class = "points_more_than_current_points"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <label><?php echo addslashes(get_option("rs_select_user_label")); ?></label>
                                        </th>
                                        <td style="border:none;padding: 6px 10px 6px 10px;">
                                            <?php
                                            global $woocommerce;
                                            if ((float) $woocommerce->version < (float) '3.0') {
                                                ?>
                                                <input id="select_user_ids" type="text" placeholder="<?php _e('Select User to Send Points', 'rewardsystem') ?>" style="font-size:14px;min-width:250px !important;height:30px !important;"/>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function () {
                                                        jQuery("#select_user_ids").select2({
                                                            allowClear: true,
                                                            enable: false,
                                                            readonly: false,
                                                            multiple: false,
                                                            initSelection: function (data, callback) {
                                                                var data_show = {
                                                                    id: data.val(),
                                                                    text: data.attr('data-selected')
                                                                };
                                                                if (data.val() > 0) {
                                                                    return callback(data_show);
                                                                }
                                                            },
                                                            minimumInputLength: 3,
                                                            ajax: {
                                                                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                                                dataType: 'json',
                                                                type: "GET",
                                                                quietMillis: 250,
                                                                data: function (term) {
                                                                    return {
                                                                        term: term,
                                                                        action: "rs_sumo_search_users"
                                                                    };
                                                                },
                                                                results: function (data) {
                                                                    var terms = [];
                                                                    if (data) {
                                                                        jQuery.each(data, function (id, text) {
                                                                            terms.push({
                                                                                id: id,
                                                                                text: text
                                                                            });
                                                                        });
                                                                    }
                                                                    return {results: terms};
                                                                }
                                                            }

                                                        });

                                                    });
                                                </script> 
                                            <?php } else { ?>
                                                <select id="select_user_ids" name="select_user_ids"  data-placeholder="<?php _e('Select User to Send Points', 'rewardsystem') ?>" style="min-width:250px !important;height:30px !important;" data-allow_clear="true" ></select>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function () {
                                                        jQuery("#select_user_ids").select2({
                                                            allowClear: true,
                                                            minimumInputLength: 3,
                                                            escapeMarkup: function (m) {
                                                                return m;
                                                            },
                                                            ajax: {
                                                                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                                                dataType: 'json',
                                                                quietMillis: 250,
                                                                data: function (params) {
                                                                    return {
                                                                        term: params.term,
                                                                        action: 'rs_sumo_search_users'
                                                                    };
                                                                },
                                                                processResults: function (data) {
                                                                    var terms = [];
                                                                    if (data) {
                                                                        jQuery.each(data, function (id, text) {
                                                                            terms.push({
                                                                                id: id,
                                                                                text: text
                                                                            });
                                                                        });
                                                                    }
                                                                    return {
                                                                        results: terms
                                                                    };
                                                                },
                                                                cache: true
                                                            }
                                                        });
                                                    });
                                                </script>
                                            <?php } ?>
                                            <div class = "error_empty_user" ></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <label><?php echo get_option("rs_points_to_send_request"); ?></label>
                                        </th>
                                        <td style="border:none;padding: 6px 10px 6px 10px;">
                                            <input style="min-width:250px !important;height:30px !important;" type = "text" id = "rs_total_reward_value_send" name = "rs_total_reward_value_send" value=""/>
                                            <div class = "error_points_not_number" ></div>
                                            <div class = "error_greater_than_limit"> </div>
                                            <div class = "error_point_empty"></div>
                                            <div class = "points_less_than_current_points"> </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="border:none;padding: 6px 10px 6px 10px;">
                                            <div class = "success_info"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td  style="border:none;padding: 6px 10px 6px 10px;">
                                            <input type = "submit" name= "rs_send_points_submit_button" value="<?php echo addslashes(get_option("rs_select_points_submit_label")); ?>" id="rs_send_points_submit_button"/>
                                        </td>
                                    </tr>
                                </table>                                                                                                                                                                                               
                            </form>
                            <?php
                            $getcontent = ob_get_clean();
                            return $getcontent;
                        } else {
                            $msg = get_option('rs_msg_when_user_have_no_points');
                            echo $msg;
                        }
                    }
                }
            } else {
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                $linkforlogin = add_query_arg('redirect_to', get_permalink(), $myaccountlink);
                $message = get_option('rs_message_shortcode_guest_display');
                $login = get_option('rs_message_shortcode_login_name');
                echo '<br>' . $message . ' <a href=' . $linkforlogin . '> ' . $login . '</a>';
            }
        }

        public static function save_selected_user() {
            $getpostvalue = $_POST['selecteduserforsend'];
            $currentuserid = get_current_user_id();
            update_user_meta($currentuserid, 'rs_selected_user', $getpostvalue);
        }

        public static function process_send_points_to_users() {
            global $wpdb;
            if (isset($_POST['points_to_send']) && isset($_POST['selecteduserforsend']) && ($_POST['selecteduserforsend'] != '')) {
                $sender_userid = $_POST['userid_of_send_request'];
                $sender_username = $_POST['username_of_send_request'];
                $points_to_be_send = $_POST['points_to_send'];
                $current_points_for_user = $_POST['sender_current_points'];
                $selected_user = $_POST['selecteduserforsend'];
                $table_name = $wpdb->prefix . "sumo_reward_send_point_submitted_data";
                $user_id = get_current_user_id();
                $date = rs_function_to_get_expiry_date_in_unixtimestamp();
                $default_status_of_send_request = $_POST['send_default_status'];
                $approval_type = get_option('rs_request_approval_type');
                update_user_meta($user_id, 'rs_request_approval_type', $approval_type);
                if ($approval_type == '2') {
                    $default_status_of_send_request = 'Paid';
                    $equearnamt = RSPointExpiry::earning_conversion_settings($points_to_be_send);
                    RSPointExpiry::insert_earning_points($selected_user, $points_to_be_send, '0', $date, 'SP', '0', $points_to_be_send, '0', '');
                    $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($selected_user);
                    RSPointExpiry::record_the_points($selected_user, $points_to_be_send, '0', $date, 'SP', '0', '0', $equearnamt, '0', '0', '0', '', $totalpoints, $sender_userid, '0');
                }
                $wpdb->insert($table_name, array('userid' => $sender_userid, 'userloginname' => $sender_username, 'pointstosend' => $points_to_be_send, 'sendercurrentpoints' => $current_points_for_user, 'status' => $default_status_of_send_request, 'selecteduser' => $selected_user, 'date' => date_i18n('Y-m-d H:i:s')));
                $redeempoints = RSPointExpiry::perform_calculation_with_expiry($points_to_be_send, $user_id);
                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_id);
                $equredeemamt = RSPointExpiry::redeeming_conversion_settings($points_to_be_send);
                if ($approval_type == '1') {
                    RSPointExpiry::record_the_points($user_id, '0', $points_to_be_send, $date, 'SPB', '0', $equredeemamt, '0', '0', '0', '0', '', $totalpoints, $selected_user, '0');
                } else {
                    RSPointExpiry::record_the_points($user_id, '0', $points_to_be_send, $date, 'SENPM', '0', $equredeemamt, '0', '0', '0', '0', '', $totalpoints, $selected_user, '0');
                }
            }
            exit();
        }

    }

    RSfunctionformforSendPoints::init();
}