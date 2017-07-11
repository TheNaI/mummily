<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForEmailTemplate')) {

    class RSFunctionForEmailTemplate {

        public static function init() {

            // add_action('wp_enqueue_scripts', array(__CLASS__, 'wp_enqueqe_script_for_email_footable'));
            // add_action('admin_enqueue_scripts', array(__CLASS__, 'wp_enqueqe_script_for_email_footable'));        

            add_action('wp_ajax_subscribevalue', array(__CLASS__, 'get_sub_value'));

            add_action('woocommerce_before_my_account', array(__CLASS__, 'sub_option_in_my_account_page'));

            add_shortcode('rs_unsubscribe_email', array(__CLASS__, 'shortcode_for_unsubscribe_email'));

            add_action('wp_head', array(__CLASS__, 'getting_value_to_unsubscribe'));

            add_action('wp_ajax_rs_email_template_status', array(__CLASS__, 'email_template_status'));

            add_action('wp_ajax_nopriv_rs_email_template_status', array(__CLASS__, 'email_template_status'));
        }

        public static function email_template_status() {
            if (isset($_POST['row_id'])) {
                global $wpdb;
                $table_name_email = $wpdb->prefix . 'rs_templates_email';
                $requesting_state = $_POST['status'] == 'ACTIVE' ? 'NOTACTIVE' : 'ACTIVE';
                $wpdb->update($table_name_email, array('rs_status' => $requesting_state), array('id' => $_POST['row_id']));
                echo $requesting_state;
            }
            exit();
        }

        public static function wp_enqueqe_script_for_email_footable() {
            wp_register_script('wp_reward_footable', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/footable.js", array('jquery'));
            wp_register_script('wp_reward_footable_filter', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/footable.filter.js", array('jquery'));
            wp_enqueue_script('wp_reward_footable');
            wp_enqueue_script('wp_reward_footable_filter');
        }

        public static function get_sub_value() {
            if ($_POST['getcurrentuser'] && $_POST['subscribe'] == 'no') {
                update_user_meta($_POST['getcurrentuser'], 'unsub_value', 'no');
                echo "1";
            } else {
                update_user_meta($_POST['getcurrentuser'], 'unsub_value', 'yes');
                echo "2";
            }

            exit();
        }

        /* For Unsubscribe option in My account Page */

        public static function sub_option_in_my_account_page() {
            if ((get_option('rs_show_hide_your_subscribe_link')) == '1') {
                ?>
                <br><h3><input type="checkbox" name="subscribeoption" id="subscribeoption" value="yes" <?php checked("yes", get_user_meta(get_current_user_id(), 'unsub_value', true)); ?>/>    <?php echo get_option('rs_unsub_field_caption'); ?></h3>
                <?php
            }
        }

        public static function shortcode_for_unsubscribe_email() {
            if (is_user_logged_in()) {
                ?>
                <br><h3><input type="checkbox" name="subscribeoption" id="subscribeoption" value="yes" <?php checked("yes", get_user_meta(get_current_user_id(), 'unsub_value', true)); ?>/><?php echo get_option('rs_unsub_field_caption'); ?></h3>
                    <?php
                } else {
                    $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                    $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                    $linkforlogin = add_query_arg('redirect_to', get_permalink(), $myaccountlink);
                    $message = get_option('rs_message_shortcode_guest_display');
                    $login = get_option('rs_message_shortcode_login_name');
                    echo '<br>' . $message . ' <a href=' . $linkforlogin . '> ' . $login . '</a>';
                }
            }

            public static function get_the_checkboxvalue_from_myaccount_page() {
                ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {

                    jQuery('#subscribeoption').click(function () {
                        var subscribe = jQuery('#subscribeoption').is(':checked') ? 'yes' : 'no';
                        var getcurrentuser = '<?php echo get_current_user_id(); ?>';
                        var data = {
                            action: 'subscribevalue',
                            subscribe: subscribe,
                            getcurrentuser: getcurrentuser,
                        };
                        jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data,
                                function (response) {
                                    if (response === '2') {
                                        alert("Successfully Unsubscribed...");
                                    } else {
                                        alert("Successfully Subscribed...");
                                    }
                                });
                    });
                });
            </script>

            <?php
        }

        public static function getting_value_to_unsubscribe() {
            if (isset($_GET['userid']) && isset($_REQUEST['nonce'])) {
                $user_id = $_GET['userid'];


                if (($_GET['userid']) && ($_GET['unsub'] == 'yes')) {
                    update_user_meta($_GET['userid'], 'unsub_value', 'yes');
                    wp_safe_redirect(site_url());
                    exit();
                }
            }
        }

    }

    RSFunctionForEmailTemplate::init();
}