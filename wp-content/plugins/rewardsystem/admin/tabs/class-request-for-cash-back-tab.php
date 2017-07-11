<?php
/*
 * Request for Cashback Tab
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSRequestForCashBack')) {

    class RSRequestForCashBack {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_request_for_cash_back', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_request_for_cash_back', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               
            
            add_action('woocommerce_admin_field_rs_encash_applications_edit_lists', array(__CLASS__, 'encash_applications_list_table'));

            add_action('woocommerce_admin_field_rs_encash_applications_list', array(__CLASS__, 'encash_list_overall_applications'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_request_for_cash_back'] = __('Request for Cashback', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {

            return apply_filters('woocommerce_rewardsystem_request_for_cash_back_settings', array(
                array(
                    'name' => __('Cashback Request', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_request_for_cash_back_setting'
                ),
                array(
                    'type' => 'rs_encash_applications_list',
                ),
                array(
                    'type' => 'rs_encash_applications_edit_lists',
                ),
                array('type' => 'sectionend', 'id' => '_rs_request_for_cash_back_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSRequestForCashBack::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSRequestForCashBack::reward_system_admin_fields());
        }

        public static function encash_validation($item) {
            $messages = array();

            if (empty($messages))
                return true;
            return implode('<br />', $messages);
        }

        public static function encash_applications_list_table($item) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'sumo_reward_encashing_submitted_data';
            $message = '';
            $notice = '';
            $default = array(
                'id' => 0,
                'userid' => '',
                'pointstoencash' => '',
                'encashercurrentpoints' => '',
                'reasonforencash' => '',
                'encashpaymentmethod' => '',
                'paypalemailid' => '',
                'otherpaymentdetails' => '',
                'status' => '',
            );

            if (isset($_REQUEST['nonce'])) {
                if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
                    $item = shortcode_atts($default, $_REQUEST);
                    $item_valid = self::encash_validation($item);
                    if ($item_valid === true) {
                        if ($item['id'] == 0) {
                            $result = $wpdb->insert($table_name, $item);
                            $item['id'] = $wpdb->insert_id;
                            if ($result) {
                                $message = __('Item was successfully saved');
                            } else {
                                $notice = __('There was an error while saving item');
                            }
                        } else {
                            $result = $wpdb->update($table_name, $item, array('id' => $item['id']));



                            if ($result) {
                                $message = __('Item was successfully updated');
                            } else {
                                $notice = __('There was an error while updating item');
                            }
                        }
                    } else {
                        // if $item_valid not true it contains error message(s)
                        $notice = $item_valid;
                    }
                }
            } else {
                // if this is not post back we load item to edit or give new one to create
                $item = $default;

                if (isset($_REQUEST['encash_application_id'])) {
                    $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['encash_application_id']), ARRAY_A);

                    if (!$item) {
                        $item = $default;
                        $notice = __('Item not found');
                    }
                }
            }
            ?>
            <?php
            if (isset($_REQUEST['encash_application_id'])) {
                ?>
                <style type="text/css">
                    p.sumo_reward_points {
                        display:none;
                    }
                    #mainforms {
                        display:none;
                    }
                </style>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        var currentvalue = jQuery('#encashpaymentmethod').val();
                        if (currentvalue === '1') {
                            jQuery('.paypalemailid').parent().parent().css('display', 'table-row');
                            jQuery('.otherpaymentdetails').parent().parent().css('display', 'none');
                        } else {
                            jQuery('.otherpaymentdetails').parent().parent().css('display', 'table-row');
                            jQuery('.paypalemailid').parent().parent().css('display', 'none');
                        }
                        jQuery('#encashpaymentmethod').change(function () {
                            var thisvalue = jQuery(this).val();
                            if (thisvalue === '1') {
                                jQuery('.paypalemailid').parent().parent().css('display', 'table-row');
                                jQuery('.otherpaymentdetails').parent().parent().css('display', 'none');
                            } else {
                                if (thisvalue === '2') {
                                    jQuery('.paypalemailid').parent().parent().css('display', 'none');
                                    jQuery('.otherpaymentdetails').parent().parent().css('display', 'table-row');
                                }
                            }
                        });
                    });
                </script>
                <?php
                $timeformat = get_option('time_format');
                $dateformat = get_option('date_format') . ' ' . $timeformat;
                $expired_date = date_i18n($dateformat);
                ?>
                <div class="wrap">
                    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
                    <h3><?php _e('Edit Cashback Status', 'rewardsystem'); ?><a class="add-new-h2"
                                                                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=rewardsystem_callback&tab=encash_applications'); ?>"><?php _e('Back to list') ?></a>
                    </h3>
                    <?php if (!empty($notice)): ?>
                        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
                    <?php endif; ?>
                    <?php if (!empty($message)): ?>
                        <div id="message" class="updated"><p><?php echo $message ?></p></div>
                    <?php endif; ?>
                    <form id="form" method="POST">
                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>"/>
                        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>
                        <input type="hidden" name="userid" value="<?php echo $item['userid']; ?>"/>
                        <input type="hidden" value="<?php echo $item['setvendoradmins']; ?>" name="setvendoradmins"/>
                        <input type="hidden" value="<?php echo $item['setusernickname']; ?>" name="setusernickname"/>
                        <input type="hidden" value="<?php echo $expired_date; ?>" name="date"/>
                        <div class="metabox-holder" id="poststuff">
                            <div id="post-body">
                                <div id="post-body-content">
                                    <table class="form-table">
                                        <tbody>                                        
                                            <tr>
                                                <th scope="row"><?php _e('Points for Cashback', 'rewardsystem'); ?></th>
                                                <td>
                                                    <input type="text" name="pointstoencash" id="setvendorname" value="<?php echo $item['pointstoencash']; ?>"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e('Reason for Cashback', 'rewardsystem'); ?></th>
                                                <td>
                                                    <textarea name="reasonforencash" rows="3" cols="30"><?php echo $item['reasonforencash']; ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e('Application Status', 'rewardsystem'); ?></th>
                                                <td>
                                                    <?php
                                                    $selected_approved = $item['status'] == 'Paid' ? "selected=selected" : '';
                                                    $selected_rejected = $item['status'] == 'Due' ? "selected=selected" : '';
                                                    ?>
                                                    <select name = "status">                                                    
                                                        <option value = "Paid" <?php echo $selected_approved; ?>><?php _e('Paid', 'rewardsystem'); ?></option>
                                                        <option value = "Due" <?php echo $selected_rejected; ?>><?php _e('Due', 'rewardsystem'); ?></option>
                                                    </select>
                                                </td>
                                            </tr>                                                                                
                                            <tr>
                                                <th scope="row"><?php _e('Cashback Payment Option', 'rewardsystem'); ?></th>
                                                <td>                                             
                                                    <?php
                                                    $selectedpaymentoption = $item['encashpaymentmethod'] == 'encash_through_paypal_method' ? "selected=selected" : "";
                                                    $mainselectedpaymentoption = $item['encashpaymentmethod'] == 'encash_through_custom_payment' ? "selected=selected" : "";
                                                    ?>
                                                    <select id="encashpaymentmethod" name="encashpaymentmethod">
                                                        <option value="1" <?php echo $selectedpaymentoption; ?>><?php _e('Paypal Address', 'rewardsystem'); ?></option>
                                                        <option value="2" <?php echo $mainselectedpaymentoption; ?>><?php _e('Custom Payment', 'rewardsystem'); ?></option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e('User Paypal Email', 'rewardsystem'); ?></th>
                                                <td>
                                                    <input type="text" name="paypalemailid" class="paypalemailid" value="<?php echo $item['paypalemailid']; ?>"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e('User Custom Payment Details', 'rewardsystem'); ?></th>
                                                <td>
                                                    <textarea name='otherpaymentdetails' rows='3' cols='30' id='otherpaymentdetails' class='otherpaymentdetails'><?php echo $item['otherpaymentdetails']; ?></textarea>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <input type="submit" value="<?php _e('Save Changes', 'rewardsystem') ?>" id="submit" class="button-primary" name="submit">
                                </div>
                            </div>
                        </div>                    
                    </form>

                </div>
            <?php } ?>

            <?php
        }

        public static function encash_list_overall_applications() {
            global $wpdb;
            global $current_section;
            global $current_tab;

            $testListTable = new FPRewardSystemEncashTabList();            
            $testListTable->prepare_items();            
            if (!isset($_REQUEST['encash_application_id'])) {
                $array_list = array();
                $message = '';
                if ('encash_application_delete' === $testListTable->current_action()) {
                    $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d'), count($_REQUEST['id'])) . '</p></div>';
                }
                echo $message;
                $testListTable->display();
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
            }
        }

    }

    RSRequestForCashBack::init();
}