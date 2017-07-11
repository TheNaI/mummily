<?php

// Integrate WP List Table for Users

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WP_List_Table_for_Users extends WP_List_Table {

    // Prepare Items
    public function prepare_items() {
        global $wpdb;
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $userprefix = $wpdb->base_prefix . "users";
        $num_rows = $wpdb->get_var("SELECT COUNT(*) FROM $userprefix");

        $user = get_current_user_id();
        $screen = get_current_screen();
        $perPage = RSTabManagement::rs_get_value_for_no_of_item_perpage($user, $screen);
        $currentPage = $this->get_pagenum();
        $startpoint = ($currentPage - 1) * $perPage;
        $data = $this->table_data($startpoint, $perPage);
        if (isset($_REQUEST['s'])) {

            $searchvalue = $_REQUEST['s'];
            $keyword = "$searchvalue";

            $newdata = array();
            $args = array(
                'search' => $keyword,
            );

            $mydata = get_users($args);

            if (is_array($mydata) && !empty($mydata)) {
                $sr = 1;
                foreach ($mydata as $eacharray => $value) {
                    $newdata[] = $this->get_data_of_users($value->ID, $sr);
                    $sr++;
                }
            }

            $perPage = RSTabManagement::rs_get_value_for_no_of_item_perpage($user, $screen);
            $currentPage = $this->get_pagenum();
            $totalItems = count($newdata);

            $this->_column_headers = array($columns, $hidden, $sortable);

            $this->items = $newdata;
        } elseif (isset($_REQUEST['orderby']) && ($_REQUEST['orderby'] == 'total_points') && $_REQUEST['order']) {
            $perPage = RSTabManagement::rs_get_value_for_no_of_item_perpage($user, $screen);
            $paged = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;
            $order = $_REQUEST['order'];
            $startpoint = ($paged - 1) * $perPage;
            $data = $this->get_sorting_data($order, $startpoint, $perPage);
            $this->_column_headers = array($columns, $hidden, $sortable);
            $totalItems = $num_rows;

            $this->set_pagination_args(array(
                'total_items' => $totalItems,
                'per_page' => $perPage
            ));
            $this->items = $data;
        } else if (isset($_REQUEST['orderby']) && ($_REQUEST['orderby'] == 'total_earned_points') && $_REQUEST['order']) {
            $perPage = RSTabManagement::rs_get_value_for_no_of_item_perpage($user, $screen);
            $paged = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;
            $order = $_REQUEST['order'];
            $startpoint = ($paged - 1) * $perPage;
            $data = $this->get_sorting_datas($order, $startpoint, $perPage);
            $this->_column_headers = array($columns, $hidden, $sortable);
            $totalItems = $num_rows;

            $this->set_pagination_args(array(
                'total_items' => $totalItems,
                'per_page' => $perPage
            ));
            $this->items = $data;
        } else {
            usort($data, array(&$this, 'sort_data'));

            $totalItems = $num_rows;

            $this->set_pagination_args(array(
                'total_items' => $totalItems,
                'per_page' => $perPage
            ));

            //  $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

            $this->_column_headers = array($columns, $hidden, $sortable);

            $this->items = $data;
        }
    }

    private function get_data_of_users($user_id, $i) {
        $getuserbyid = get_user_by('id', $user_id);
        $arrayvalue = self::total_redeem_and_expired_points($user_id);
        $totalcurrentpoints = $arrayvalue['currentpoints'];
        $totalearnedpoints = $arrayvalue['totalpoints'];
        $totalexpiredpoints = $arrayvalue['expiredpoints'];
        $totalredeempoints = $arrayvalue['redeempoints'];

        $data = array(
            'sno' => $i,
            'user_name' => $getuserbyid->user_login,
            'user_email' => $getuserbyid->user_email,
            'total_points' => $totalcurrentpoints,
            'total_earned_points' => $totalearnedpoints,
            'total_redeem_points' => $totalredeempoints,
            'total_expired_points' => $totalexpiredpoints,
            'view' => "<a href=" . add_query_arg('view', $user_id, admin_url('admin.php?page=rewardsystem_callback&tab=rewardsystem_user_reward_points')) . ">View Log</a>",
            'edit' => "<a href=" . add_query_arg('edit', $user_id, admin_url('admin.php?page=rewardsystem_callback&tab=rewardsystem_user_reward_points')) . ">Edit Total Points</a>",
        );
        return $data;
    }

    public function get_columns() {
        $columns = array(
            'sno' => __('S.No', 'rewardsystem'),
            'user_name' => __('Username', 'rewardsystem'),
            'user_email' => __('User Email', 'rewardsystem'),
            'total_points' => __('Total Points', 'rewardsystem'),
            'total_earned_points' => __('Total Earned Points', 'rewardsystem'),
            'total_redeem_points' => __('Total Redeemed Points', 'rewardsystem'),
            'total_expired_points' => __('Total Expired Points', 'rewardsystem'),
            'view' => __('View', 'rewardsystem'),
            'edit' => __('Edit', 'rewardsystem'),
        );

        return $columns;
    }

    public function get_hidden_columns() {
        return array();
    }

    public function get_sortable_columns() {
        return array('user_name' => array('user_name', false),
            'sno' => array('sno', false),
            'total_points' => array('total_points', false),
            'total_earned_points' => array('total_earned_points', false),
            'total_redeem_points' => array('total_redeem_points', false),
            'total_expired_points' => array('total_expired_points', false),
        );
    }

    private function get_sorting_datas($sort_type, $startpoint, $perpage) {
        global $wpdb;
        $data = array();
        if (is_multisite()) {
            $usertable = $wpdb->base_prefix . 'users';
        } else {
            $usertable = $wpdb->prefix . 'users';
        }
        $points_table = $wpdb->prefix . 'rspointexpiry';
        if ($sort_type == 'asc') {
            // for ascending
            $get_datas = $wpdb->get_results("SELECT distinct t1.*, ( SELECT if(t2.earnedpoints is null, 0, SUM(t2.earnedpoints)) FROM $points_table AS t2 WHERE t1.ID=t2.userid AND t2.expiredpoints IN(0)) AS available FROM  $usertable AS t1 LEFT JOIN $points_table AS t2 ON t1.ID=t2.userid ORDER BY available ASC LIMIT $startpoint , $perpage");
        } else {
            // for descending
            $get_datas = $wpdb->get_results("SELECT distinct t1.*, ( SELECT if(t2.earnedpoints is null, 0, SUM(t2.earnedpoints)) FROM $points_table AS t2 WHERE t1.ID=t2.userid AND t2.expiredpoints IN(0)) AS available FROM  $usertable AS t1 LEFT JOIN $points_table AS t2 ON t1.ID=t2.userid ORDER BY available DESC LIMIT $startpoint , $perpage");
        }
        $i = 1;
        if (is_array($get_datas) && !empty($get_datas)) {
            foreach ($get_datas as $each_data) {
                $getuserbyid = get_user_by('id', $each_data->ID);
                $arrayvalue = self::total_redeem_and_expired_points($each_data->ID);
                $totalcurrentpoints = $arrayvalue['currentpoints'];
                $totalearnedpoints = $arrayvalue['totalpoints'];
                $totalexpiredpoints = $arrayvalue['expiredpoints'];
                $totalredeempoints = $arrayvalue['redeempoints'];

                $data[] = array(
                    'sno' => $startpoint + $i,
                    'user_name' => $getuserbyid->user_login,
                    'user_email' => $getuserbyid->user_email,
                    'total_points' => $totalcurrentpoints,
                    'total_earned_points' => $totalearnedpoints,
                    'total_redeem_points' => $totalredeempoints,
                    'total_expired_points' => $totalexpiredpoints,
                    'view' => "<a href=" . add_query_arg('view', $each_data->ID, admin_url('admin.php?page=rewardsystem_callback&tab=rewardsystem_user_reward_points')) . ">View Log</a>",
                    'edit' => "<a href=" . add_query_arg('edit', $each_data->ID, admin_url('admin.php?page=rewardsystem_callback&tab=rewardsystem_user_reward_points')) . ">Edit Total Points</a>",
                );
                $i++;
            }
        }
        return $data;
    }

    private function get_sorting_data($sort_type, $startpoint, $perpage) {
        global $wpdb;
        $data = array();
        $total_points_expired = 0;       
        if (is_multisite()) {
            $usertable = $wpdb->base_prefix . 'users';
        } else {
            $usertable = $wpdb->prefix . 'users';
        }
        $points_table = $wpdb->prefix . 'rspointexpiry';
        if ($sort_type == 'asc') {
            // for ascending
            $get_datas = $wpdb->get_results("SELECT distinct t1.*, ( SELECT if(t2.earnedpoints-t2.usedpoints is null, 0, SUM(t2.earnedpoints-t2.usedpoints)) FROM $points_table AS t2 WHERE t1.ID=t2.userid AND t2.expiredpoints IN(0)) AS available FROM  $usertable AS t1 LEFT JOIN $points_table AS t2 ON t1.ID=t2.userid ORDER BY available ASC LIMIT $startpoint , $perpage");
        } else {
            // for descending
            $get_datas = $wpdb->get_results("SELECT distinct t1.*, ( SELECT if(t2.earnedpoints-t2.usedpoints is null, 0, SUM(t2.earnedpoints-t2.usedpoints)) FROM $points_table AS t2 WHERE t1.ID=t2.userid AND t2.expiredpoints IN(0)) AS available FROM  $usertable AS t1 LEFT JOIN $points_table AS t2 ON t1.ID=t2.userid ORDER BY available DESC LIMIT $startpoint , $perpage");
        }
        $i = 1;
        if (is_array($get_datas) && !empty($get_datas)) {
            foreach ($get_datas as $each_data) {
                $getuserbyid = get_user_by('id', $each_data->ID);
                $arrayvalue = self::total_redeem_and_expired_points($each_data->ID);
                $totalcurrentpoints = $arrayvalue['currentpoints'];
                $totalearnedpoints = $arrayvalue['totalpoints'];
                $totalexpiredpoints = $arrayvalue['expiredpoints'];
                $totalredeempoints = $arrayvalue['redeempoints'];
                $data[] = array(
                    'sno' => $startpoint + $i,
                    'user_name' => $getuserbyid->user_login,
                    'user_email' => $getuserbyid->user_email,
                    'total_points' => $totalcurrentpoints,
                    'total_earned_points' => $totalearnedpoints,
                    'total_redeem_points' => $totalredeempoints,
                    'total_expired_points' => $totalexpiredpoints,
                    'view' => "<a href=" . add_query_arg('view', $each_data->ID, admin_url('admin.php?page=rewardsystem_callback&tab=rewardsystem_user_reward_points')) . ">View Log</a>",
                    'edit' => "<a href=" . add_query_arg('edit', $each_data->ID, admin_url('admin.php?page=rewardsystem_callback&tab=rewardsystem_user_reward_points')) . ">Edit Total Points</a>",
                );
                $i++;
            }
        }


        return $data;
    }

    private function table_data($startpoint, $perpage) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'rspointexpiry';
        $data = array();
        $i = 1;
        $table_user = $wpdb->base_prefix . 'users';
        $table_usermeta = $wpdb->base_prefix . 'usermeta';
        $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
        if (is_multisite()) {
            $id = get_current_blog_id();
            $blog_prefix = $wpdb->get_blog_prefix($id);
            $blog_prefix = $blog_prefix . 'capabilities';
            $getusermeta1 = $wpdb->get_results("SELECT $table_user.ID FROM $table_user INNER JOIN  $table_usermeta ON ( $table_user.ID = $table_usermeta.user_id ) WHERE  1=1 AND ($table_usermeta.meta_key = '$blog_prefix')LIMIT $startpoint, $perpage");
            foreach ($getusermeta1 as $user) {
                $getuserbyid = get_user_by('id', $user->ID);
                $arrayvalue = self::total_redeem_and_expired_points($user->ID);
                $totalcurrentpoints = $arrayvalue['currentpoints'];
                $totalearnedpoints = $arrayvalue['totalpoints'];
                $totalexpiredpoints = $arrayvalue['expiredpoints'];
                $totalredeempoints = $arrayvalue['redeempoints'];                
                $data[] = array(
                    'sno' => $startpoint + $i,
                    'user_name' => $getuserbyid->user_login,
                    'user_email' => $getuserbyid->user_email,
                    'total_points' => $totalcurrentpoints,
                    'total_earned_points' => $totalearnedpoints,
                    'total_redeem_points' => $totalredeempoints,
                    'total_expired_points' => $totalexpiredpoints,
                    'view' => "<a href=" . add_query_arg('view', $user->ID, admin_url('admin.php?page=rewardsystem_callback&tab=rewardsystem_user_reward_points')) . ">View Log</a>",
                    'edit' => "<a href=" . add_query_arg('edit', $user->ID, admin_url('admin.php?page=rewardsystem_callback&tab=rewardsystem_user_reward_points')) . ">Edit Total Points</a>",
                );
                $i++;
            }

            return $data;
        } else {
            $query_data = $wpdb->get_results("SELECT * FROM $table_user LIMIT $startpoint, $perpage");
            foreach ($query_data as $user) {
                $getuserbyid = get_user_by('id', $user->ID);
                $arrayvalue = self::total_redeem_and_expired_points($user->ID);
                $totalcurrentpoints = $arrayvalue['currentpoints'];
                $totalearnedpoints = $arrayvalue['totalpoints'];
                $totalexpiredpoints = $arrayvalue['expiredpoints'];
                $totalredeempoints = $arrayvalue['redeempoints'];                
                $data[] = array(
                    'sno' => $startpoint + $i,
                    'user_name' => $getuserbyid->user_login,
                    'user_email' => $getuserbyid->user_email,
                    'total_points' => $totalcurrentpoints,
                    'total_earned_points' => $totalearnedpoints,
                    'total_redeem_points' => $totalredeempoints,
                    'total_expired_points' => $totalexpiredpoints,
                    'view' => "<a href=" . add_query_arg('view', $user->ID, admin_url('admin.php?page=rewardsystem_callback&tab=rewardsystem_user_reward_points')) . ">View Log</a>",
                    'edit' => "<a href=" . add_query_arg('edit', $user->ID, admin_url('admin.php?page=rewardsystem_callback&tab=rewardsystem_user_reward_points')) . ">Edit Total Points</a>",
                );
                $i++;
            }

            return $data;
        }
    }

    public function total_redeem_and_expired_points($userid) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'rspointexpiry';
        $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
        $getuserbyid = get_user_by('id', $userid);
        $getusermeta = $wpdb->get_results("SELECT SUM((earnedpoints-usedpoints)) as availablepoints FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid=$userid", ARRAY_A);
        $currentpoints = $getusermeta[0]['availablepoints'] != '' ? round($getusermeta[0]['availablepoints'], $roundofftype) : '0';
        $getuser = $wpdb->get_results("SELECT SUM(earnedpoints) as availablepoints FROM $table_name WHERE earnedpoints NOT IN(0) and expiredpoints IN(0) and userid=$userid", ARRAY_A);
        $redeem_points = $wpdb->get_results("SELECT SUM(usedpoints) as redeempoints FROM $table_name WHERE usedpoints NOT IN(0) and userid=$userid", ARRAY_A);
        $expired_points = $wpdb->get_results("SELECT SUM(expiredpoints) as expiredpoints FROM $table_name WHERE expiredpoints NOT IN(0) and userid=$userid", ARRAY_A);
        $deletedearnedpoints = get_user_meta($userid, 'rs_earned_points_before_delete', true);
        $earnedpoints = $getuser[0]['availablepoints'] != '' ? round($getuser[0]['availablepoints'], $roundofftype) : '0';
        $totalearnedpoints = $earnedpoints + (float) $deletedearnedpoints;
        $totalearnedpoint = round($totalearnedpoints, $roundofftype);
        $deletedexpiredpoints = get_user_meta($userid, 'rs_expired_points_before_delete', true);
        $remain_expired_points = $expired_points[0]['expiredpoints'] != '' ? $expired_points[0]['expiredpoints'] : '0';
        $total_expired_points = $remain_expired_points + (float) $deletedexpiredpoints;
        $expired_points = round($total_expired_points, $roundofftype);
        $redeemmetavalue = get_user_meta($userid, 'rs_redeem_points_before_delete', true);
        $remain_redeem_points = $redeem_points[0]['redeempoints'] != '' ? round($redeem_points[0]['redeempoints'], $roundofftype) : '0';
        $total_redeem_points = $remain_redeem_points + (float) $redeemmetavalue;
        $redeem_points = round($total_redeem_points, $roundofftype);        
        $array = array('currentpoints' => $currentpoints, 'totalpoints' => $totalearnedpoint, 'redeempoints' => $redeem_points, 'expiredpoints' => $expired_points);
        return $array;
    }

    public function column_id($item) {
        return $item['sno'];
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'sno':
            case 'user_name':
            case 'user_email':
            case 'total_points':
            case 'total_earned_points':
            case 'total_redeem_points':
            case 'total_expired_points':
            case 'view':
            case 'edit':
                return $item[$column_name];

            default:
                return print_r($item, true);
        }
    }

    private function sort_data($a, $b) {

        $orderby = 'sno';
        $order = 'asc';

        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }

        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }

        $result = strnatcmp($a[$orderby], $b[$orderby]);

        if ($order === 'asc') {
            return $result;
        }

        return -$result;
    }

}
