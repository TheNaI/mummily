<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class FPRewardSystemSendpointTabList extends WP_List_Table {

    function __construct() {
        global $status, $page;
        parent::__construct(array(
            'singular' => 'send_application',
            'plural' => 'send_applications',
            'ajax' => true
        ));
    }

    function column_default($item, $column_name) {
        return $item[$column_name];
    }

    function column_userloginname($item) {


        if ($item['status'] == 'Paid') {
            //Build row actions
            $actions = array(
                'delete' => sprintf('<a href="?page=%s&tab=%s&action=%s&id=%s">Delete</a>', $_REQUEST['page'], $_REQUEST['tab'], 'send_application_delete', $item['id']),
            );

            //Return the title contents
            return sprintf('%1$s %3$s',
                    /* $1%s */ $item['userloginname'],
                    /* $2%s */ $item['id'],
                    /* $3%s */ $this->row_actions($actions)
            );
        } elseif ($item['status'] == 'Rejected') {
            //Build row actions
             $actions = array(
                'delete' => sprintf('<a href="?page=%s&tab=%s&action=%s&id=%s">Delete</a>', $_REQUEST['page'], $_REQUEST['tab'], 'delete', $item['id']),
            );
            return sprintf('%1$s %3$s',
                    /* $1%s */ $item['userloginname'],
                    /* $2%s */ $item['id'],
                    /* $3%s */ $this->row_actions($actions)
            );
        } else {
            $actions = array(
                'accept' => sprintf('<a href="?page=%s&tab=%s&action=%s&id=%s">Approve</a>', $_REQUEST['page'], $_REQUEST['tab'], 'accept', $item['id']),
                'reject' => sprintf('<a href="?page=%s&tab=%s&action=%s&id=%s">Reject</a>', $_REQUEST['page'], $_REQUEST['tab'], 'reject', $item['id']),
            );
            //Return the title contents
            return sprintf('%1$s %3$s',
                    /* $1%s */ $item['userloginname'],
                    /* $2%s */ $item['id'],
                    /* $3%s */ $this->row_actions($actions)
            );
        }
    }

    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="id[]" value="%s" />', $item['id']
        );
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text            
            'userloginname' => __('Sent by', 'rewardsystem'),
            'selecteduser' => __('Received by', 'rewardsystem'),
            'pointstosend' => __('Points', 'rewardsystem'),
            'sendercurrentpoints' => __('Current user Points', 'rewardsystem'),
            'status' => __('Request Status', 'rewardsystem'),
            'date' => __('Requested date', 'rewardsystem')
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'userloginname' => array('userloginname', false), //true means it's already sorted            
            'selecteduser' => array('selecteduser', false),
            'pointstosend' => array('pointstosend', false),
            'sendercurrentpoints' => array('sendercurrentpoints', false),
            'status' => array('status', false),
            'date' => array('date', false)
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => __('Delete', 'rewardsystem'),
            'rspaid' => __('Mark as Paid', 'rewardsystem'),
            'rsdue' => __('Mark as Due', 'rewardsystem'),
        );
        return $actions;
    }

    function process_bulk_action() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sumo_reward_send_point_submitted_data'; // do not forget about tables prefix
        $table_name1 = $wpdb->prefix . "rspointexpiry";
        $table_name2 = $wpdb->prefix . "rsrecordpoints";
        $table_name3 = $wpdb->prefix . "users";
        $date = rs_function_to_get_expiry_date_in_unixtimestamp();
        if ('send_application_delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) {
                $ids = implode(',', $ids);
            } else {
                $ids = $ids;
            }
            
            if (!empty($ids)) {
                $ids = explode(',', $ids);
                $countids = count($ids);
                foreach ($ids as $eachid) {
                    $user_id = $wpdb->get_results("SELECT id,userid,pointstosend FROM $table_name WHERE id = $eachid", ARRAY_A);
                    if (is_array($user_id) && !empty($user_id)) {
                        foreach ($user_id as $value) {
                            $user_idss = $value['userid'];
                            $returnedpointsss = $value['pointstosend'];
                            $updatoption = $value['id'] . 'sendpointupadate';
                            $get_user = get_user_meta($user_idss, $updatoption, true);
                            if ($get_user != '1') {
                                $equearnamt = RSPointExpiry::earning_conversion_settings($returnedpointsss);
                                RSPointExpiry::insert_earning_points($user_idss, $returnedpointsss, '0', $date, 'SEP', '0', $returnedpointsss, '0');
                                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_idss);
                                RSPointExpiry::record_the_points($user_idss, $returnedpointsss, '0', $date, 'SEP', '0', '0', $equearnamt, '0', '0', '0', '', $totalpoints, '', '0');
                                update_user_meta($user_idss, $updatoption, '1');
                            }
                        }
                    }
                }
            }
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) {
                $ids = implode(',', $ids);
            } else {
                $ids = $ids;
            }
            $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            $redirect = remove_query_arg(array('action', 'id'), get_permalink());
            wp_safe_redirect($redirect);
        } elseif ('rspaid' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids) && !empty($ids)) {
                $ids = explode(',', $ids);
                $countids = count($ids);
                foreach ($ids as $eachid) {
                    $wpdb->update($table_name, array('status' => 'Paid'), array('id' => $eachid));
                    $message = __($countids . ' Status Changed to Paid', 'rewardsystem');
                    $selecteduserid = $wpdb->get_results("SELECT selecteduser,userid FROM $table_name WHERE id = $eachid", ARRAY_A);
                    if (is_array($selecteduserid) && !empty($selecteduserid)) {
                        foreach ($selecteduserid as $value) {
                            $useridsss = $value['selecteduser'];
                            $senduser = $value['userid'];
                            $sendpoints = $wpdb->get_results("SELECT pointstosend FROM $table_name WHERE id = $eachid", ARRAY_A);
                            foreach ($sendpoints as $value) {
                                $returnedpointssss = $value['pointstosend'];
                            }
                            $equearnamt = RSPointExpiry::earning_conversion_settings($returnedpointssss);
                            RSPointExpiry::insert_earning_points($useridsss, $returnedpointssss, '0', $date, 'SP', '0', $returnedpointssss, '0', '');
                            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($useridsss);
                            RSPointExpiry::record_the_points($useridsss, $returnedpointssss, '0', $date, 'SP', '0', '0', $equearnamt, '0', '0', '0', '', $totalpoints, $senduser, '0');
                        }
                    }
                }

                if (!empty($message)):
                    ?>
                    <div id="message" class="updated"><p><?php echo $message ?></p></div>
                    <?php
                endif;
                $redirect = remove_query_arg(array('action', 'id'), get_permalink());
                wp_safe_redirect($redirect);
                exit;
            }
        }elseif ('accept' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (!is_array($ids)) {
                $ids = explode(',', $ids);
                $countids = count($ids);
                foreach ($ids as $eachid) {
                    $wpdb->update($table_name, array('status' => 'Paid'), array('id' => $eachid));
                    $message = __($countids . ' Status Changed to Paid', 'rewardsystem');
                    $selecteduserid = $wpdb->get_results("SELECT selecteduser,userid FROM $table_name WHERE id = $eachid", ARRAY_A);
                    if (is_array($selecteduserid) && !empty($selecteduserid)) {
                        foreach ($selecteduserid as $value) {
                            $useridsss = $value['selecteduser'];
                            $returnedpointssss = $value['pointstosend'];
                            $senduser = $value['userid'];
                            $equearnamt = RSPointExpiry::earning_conversion_settings($returnedpointssss);
                            RSPointExpiry::insert_earning_points($useridsss, $returnedpointssss, '0', $date, 'SP', '0', $returnedpointssss, '0', '');
                            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($useridsss);
                            RSPointExpiry::record_the_points($useridsss, $returnedpointssss, '0', $date, 'SP', '0', '0', $equearnamt, '0', '0', '0', '', $totalpoints, $senduser, '0');

                            //Log to be record for Sender after Admin Approval
                            $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($senduser);
                            RSPointExpiry::record_the_points($senduser, '0', $returnedpointssss, $date, 'SPA', '0', '0', '0', '0', '0', '0', '', $totalpoints, $useridsss, '0');
                        }
                    }
                }
                if (!empty($message)):
                    ?>
                    <div id="message" class="updated"><p><?php echo $message ?></p></div>
                    <?php
                endif;
                $redirect = remove_query_arg(array('action', 'id'), get_permalink());
                wp_safe_redirect($redirect);
                exit;
            }
        }elseif ('reject' === $this->current_action()) {
            $user_idss = '';
            $returnedpointsss = '';
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (!is_array($ids)) {
                $ids = explode(',', $ids);
                $countids = count($ids);
                foreach ($ids as $eachid) {
                    $wpdb->update($table_name, array('status' => 'Rejected'), array('id' => $eachid));
                    $message = __($countids . ' Status Changed to Rejected', 'rewardsystem');
                    $user_id = $wpdb->get_results("SELECT id,userid,pointstosend FROM $table_name WHERE id = $eachid", ARRAY_A);
                    if (is_array($user_id) && !empty($user_id)) {
                        foreach ($user_id as $value) {
                            $user_idss = $value['userid'];
                            $returnedpointsss = $value['pointstosend'];
                            $updatoption = $value['id'] . 'sendpointupadate';
                            $get_user = get_user_meta($user_idss, $updatoption, true);
                            if ($get_user != '1') {
                                $equearnamt = RSPointExpiry::earning_conversion_settings($returnedpointsss);
                                RSPointExpiry::insert_earning_points($user_idss, $returnedpointsss, '0', $date, 'SEP', '0', $returnedpointsss, '0');
                                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_idss);
                                RSPointExpiry::record_the_points($user_idss, $returnedpointsss, '0', $date, 'SEP', '0', '0', $equearnamt, '0', '0', '0', '', $totalpoints, '', '0');
                                update_user_meta($user_idss, $updatoption, '1');
                            }
                        }
                    }
                }
                if (!empty($message)):
                    ?>
                    <div id="message" class="updated"><p><?php echo $message ?></p></div>
                    <?php
                endif;
                $redirect = remove_query_arg(array('action', 'id'), get_permalink());
                wp_safe_redirect($redirect);
                exit;
            }
        }elseif ('delete' === $this->current_action()) {
            $user_idss = '';
            $returnedpointsss = '';
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) {
                $ids = implode(',', $ids);
            } else {
                $ids = $ids;
            }
            
            if (!empty($ids)) {
                $ids = explode(',', $ids);
                $countids = count($ids);
                foreach ($ids as $eachid) {
                    $user_id = $wpdb->get_results("SELECT id,userid,pointstosend FROM $table_name WHERE id = $eachid", ARRAY_A);
                    if (is_array($user_id) && !empty($user_id)) {
                        foreach ($user_id as $value) {
                            $user_idss = $value['userid'];
                            $returnedpointsss = $value['pointstosend'];
                            $updatoption = $value['id'] . 'sendpointupadate';
                            $get_user = get_user_meta($user_idss, $updatoption, true);
                            if ($get_user != '1') {
                                $equearnamt = RSPointExpiry::earning_conversion_settings($returnedpointsss);
                                RSPointExpiry::insert_earning_points($user_idss, $returnedpointsss, '0', $date, 'SEP', '0', $returnedpointsss, '0');
                                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_idss);
                                RSPointExpiry::record_the_points($user_idss, $returnedpointsss, '0', $date, 'SEP', '0', '0', $equearnamt, '0', '0', '0', '', $totalpoints, '', '0');
                                update_user_meta($user_idss, $updatoption, '1');
                            }
                        }
                    }
                }
            }            
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) {
                $ids = implode(',', $ids);
            } else {
                $ids = $ids;
            }            
            $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            if (!empty($message)):
                ?>
                <div id="message" class="updated"><p><?php echo $message ?></p></div>
                <?php
            endif;
            $redirect = remove_query_arg(array('action', 'id'), get_permalink());
            wp_safe_redirect($redirect);
            exit;
        }else {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids))
                $ids = implode(',', $ids);
            if (!empty($ids)) {
                $ids = explode(',', $ids);
                $countids = count($ids);
                foreach ($ids as $eachid) {
                    $wpdb->update($table_name, array('status' => 'Due'), array('id' => $eachid));
                    $message = __($countids . ' Status Changed to Due', 'rewardsystem');
                }
                if (!empty($message)):
                    ?>
                    <div id="message" class="updated"><p><?php echo $message ?></p></div>
                    <?php
                endif;
                $redirect = remove_query_arg(array('action', 'id'), get_permalink());
                wp_safe_redirect($redirect);
                exit;
            }
        }
    }

    private function table_data($startpoint, $perPage) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sumo_reward_send_point_submitted_data'; // do not forget about tables prefix
        $data = array();
        $query_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name  LIMIT %d OFFSET %d", $perPage, $startpoint), ARRAY_A);
        $i = 1;
        foreach ($query_data as $user) {
            $sender = $user['userid'];
            $sender_info = get_user_by('id', $sender);
            $customer_info = $sender_info->display_name . ' (#' . $sender_info->ID . ' - ' . sanitize_email($sender_info->user_email) . ')';
            $receiver_user_id = $user['selecteduser'];
            $customer = get_user_by('id', $receiver_user_id);
            if (is_object($customer)) {
                $reciver_info = $customer->display_name . ' (#' . $customer->ID . ' - ' . sanitize_email($customer->user_email) . ')';
                $data[] = array(
                    'id' => $user['id'],
                    'userloginname' => $customer_info,
                    'selecteduser' => $reciver_info,
                    'pointstosend' => $user['pointstosend'],
                    'sendercurrentpoints' => $user['sendercurrentpoints'],
                    'status' => $user['status'],
                    'date' => $user['date'],
                );
                $i++;
            }
        }
        return $data;
    }

    function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sumo_reward_send_point_submitted_data'; // do not forget about tables prefix
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $user = get_current_user_id();
        $screen = get_current_screen();
        $per_page = RSTabManagement::rs_get_value_for_no_of_item_perpage($user, $screen);
        $currentPage = $this->get_pagenum();
        $startpoint = ($currentPage - 1) * $per_page;
        $data = $this->table_data($startpoint, $per_page);
        $this->items = $data;
        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }

}
