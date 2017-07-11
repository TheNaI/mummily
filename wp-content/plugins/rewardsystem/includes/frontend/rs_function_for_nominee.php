<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForNominee')) {

    class RSFunctionForNominee {

        public static function init() {

            if (get_option('rs_show_hide_nominee_field_in_checkout') == '1') {

                add_action('woocommerce_after_order_notes', array(__CLASS__, 'ajax_for_saving_nominee_in_checkout'));

                add_action('woocommerce_after_order_notes', array(__CLASS__, 'display_nominee_field_in_checkout'));
            }

            add_action('woocommerce_checkout_update_order_meta', array(__CLASS__, 'save_selected_nominee_in_checkout'), 10, 2);
        }

        public static function display_nominee_field_in_checkout() {
            global $woocommerce;
            global $wp_roles;
            ?>
            <style type="text/css">
                .chosen-container-single {
                    position:absolute;
                }

            </style>        
            <?php
            $getnomineetype = get_option('rs_select_type_of_user_for_nominee_checkout');
            if ($getnomineetype == '1') {

                $getusers = get_option('rs_select_users_list_for_nominee_in_checkout');
                echo "<h2>" . get_option('rs_my_nominee_title_in_checkout') . "</h2>";
                if ($getusers != '') {
                    ?>
                    <table class="form-table">
                        <tr valign="top">
                            <td style="width:150px;">
                                <label for="rs_select_nominee_in_checkout" style="font-size:16px;font-weight: bold;"><?php _e('Select Nominee for Product Purchase', 'rewardsystem'); ?></label>
                            </td>
                        </tr>
                        <tr valign="top">
                            <td style="width:300px;">
                                <select name="rs_select_nominee_in_checkout" style="width:300px;" id="rs_select_nominee_in_checkout" class="short rs_select_nominee_in_checkout">
                                    <option value=""><?php _e('Choose Nominee', 'rewardsystem'); ?></option>
                                    <?php
                                    $getusers = get_option('rs_select_users_list_for_nominee_in_checkout');
                                    $currentuserid = get_current_user_id();
                                    $usermeta = get_user_meta($currentuserid, 'rs_selected_nominee_in_checkout', true);
                                    if ($getusers != '') {
                                        if (!is_array($getusers)) {
                                            $userids = array_filter(array_map('absint', (array) explode(',', $getusers)));
                                            foreach ($userids as $userid) {
                                                $user = get_user_by('id', $userid);
                                                ?>
                                                <option value="<?php echo $userid; ?>" <?php echo $usermeta == $userid ? "selected=selected" : '' ?>>
                                                    <?php if (get_option('rs_select_type_of_user_for_nominee_name_checkout') == '1') { ?>
                                                        <?php echo esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email) . ')'; ?></option>
                                                        <?php
                                                } else {
                                                    echo esc_html($user->display_name);
                                                }
                                                ?>
                                                <?php
                                            }
                                        } else {
                                            $userids = $getusers;
                                            foreach ($userids as $userid) {
                                                $user = get_user_by('id', $userid);
                                                ?>
                                                <option value="<?php echo $userid; ?>" <?php echo $usermeta == $userid ? "selected=selected" : '' ?>>
                                                    <?php if (get_option('rs_select_type_of_user_for_nominee_name_checkout') == '1') { ?>
                                                        <?php echo esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email) . ')'; ?></option>
                                                        <?php
                                                } else {
                                                    echo esc_html($user->display_name);
                                                }
                                                ?>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </td> 

                        </tr>
                    </table>
                    <?php
                } else {
                    _e('You have no Nominee', 'rewardsystem');
                }
            } else {
                $getuserrole = get_option('rs_select_users_role_for_nominee_checkout');
                echo "<h2>" . get_option('rs_my_nominee_title_in_checkout') . "</h2>";
                if ($getuserrole != '') {
                    ?>
                    <table class="form-table">
                        <tr valign="top">
                            <td style="width:150px;">
                                <label for="rs_select_nominee_in_checkout" style="font-size:20px;font-weight:bold;"><?php _e('Select Nominee', 'rewardsystem'); ?></label>
                            </td>
                            <td style="width:300px;">
                                <select name="rs_select_nominee_in_checkout" style="width:300px;" id="rs_select_nominee_in_checkout" class="short rs_select_nominee_in_checkout">
                                    <option value=""><?php _e('Choose Nominee', 'rewardsystem'); ?></option>
                                    <?php
                                    $getusers = get_option('rs_select_users_role_for_nominee_checkout');
                                    $currentuserid = get_current_user_id();
                                    $usermeta = get_user_meta($currentuserid, 'rs_selected_nominee_in_checkout', true);
                                    if ($getusers != '') {
                                        if (is_array($getusers)) {
                                            foreach ($getusers as $userrole) {
                                                $args['role'] = $userrole;
                                                $users = get_users($args);
                                                foreach ($users as $user) {
                                                    $userid = $user->ID;
                                                    ?>
                                                    <option value="<?php echo $userid; ?>" <?php echo $usermeta == $userid ? "selected=selected" : '' ?>>
                                                        <?php if (get_option('rs_select_type_of_user_for_nominee_name_checkout') == '1') { ?>
                                                            <?php echo esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email) . ')'; ?></option> ?></option>
                                                            <?php
                                                    } else {
                                                        echo esc_html($user->display_name);
                                                    }
                                                    ?>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </td>

                        </tr>
                    </table>
                    <?php
                } else {
                    _e('You have no Nominee', 'rewardsystem');
                }
            }
        }

        public static function ajax_for_saving_nominee_in_checkout() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('#rs_select_nominee_in_checkout').change(function () {
                        var value = jQuery('#rs_select_nominee_in_checkout').val();
                        var Value = {
                            action: "rs_save_nominee_in_checkout",
                            selectedvalue: value,
                        };
                        jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", Value, function (response) {
                            console.log('Success');
                        });
                        return false;
                    });
                    return false;
                });
            </script>
            <?php
        }

        public static function save_selected_nominee_in_checkout($order_id, $user_id) {
            $getpostvalue = isset($_POST['rs_select_nominee_in_checkout']) ? $_POST['rs_select_nominee_in_checkout'] : '';
            update_post_meta($order_id, 'rs_selected_nominee_in_checkout', $getpostvalue);
        }

    }

    RSFunctionForNominee::init();
}