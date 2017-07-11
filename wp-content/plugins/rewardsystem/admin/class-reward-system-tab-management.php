<?php
/*
 * Reward System Tab Management
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('RSTabManagement')) {

    class RSTabManagement {

        public static function init() {
            add_action('admin_menu', array(__CLASS__, 'add_submenu_woocommerce'));
            if (isset($_GET['page']) && $_GET['page'] == 'rewardsystem_callback') {
                add_filter('set-screen-option', array(__CLASS__, 'rs_set_screen_option_value'), 10, 3);
            }
            add_filter('plugin_action_links_' . REWARDSYSTEM_PLUGIN_BASENAME, array(__CLASS__, 'rs_plugin_action'));
            add_filter('plugin_row_meta', array(__CLASS__, 'rs_plugin_row_meta'), 10, 2);
        }

        public static function add_submenu_woocommerce() {
            global $my_admin_page;
            $name = get_option('rs_brand_name');
            if ($name == '') {
                $name = 'SUMO Reward Points';
            }
            $my_admin_page = add_submenu_page('woocommerce', $name, $name, 'manage_woocommerce', 'rewardsystem_callback', array('RSTabManagement', 'rewardsystem_tab_management'));
            add_action('load-' . $my_admin_page, array('RSTabManagement', 'rs_function_to_display_screen_option'));
        }

        public static function rewardsystem_tab_management() {
            global $woocommerce, $woocommerce_settings, $current_section, $current_tab;
            do_action('woocommerce_rs_settings_start');
            $current_tab = ( empty($_GET['tab']) ) ? 'rewardsystem_general' : sanitize_text_field(urldecode($_GET['tab']));
            $current_section = ( empty($_REQUEST['section']) ) ? '' : sanitize_text_field(urldecode($_REQUEST['section']));

            if (!empty($_POST['save'])) {
                if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'woocommerce-settings'))
                    die(__('Action failed. Please refresh the page and retry.', 'rewardsystem'));

                if (!$current_section) {
                    switch ($current_tab) {
                        default :
                            if (isset($woocommerce_settings[$current_tab]))
                                woocommerce_update_options($woocommerce_settings[$current_tab]);
// Trigger action for tab
                            do_action('woocommerce_update_options_' . $current_tab);
                            break;
                    }
                    do_action('woocommerce_update_options');
                } else {
// Save section onlys
                    do_action('woocommerce_update_options_' . $current_tab . '_' . $current_section);
                }

// Clear any unwanted data
                delete_transient('woocommerce_cache_excluded_uris');
// Redirect back to the settings page
                $redirect = add_query_arg(array('saved' => 'true'));

                if (isset($_POST['subtab'])) {
                    wp_safe_redirect($redirect);
                    exit;
                }
            }
// Get any returned messages
            if (!empty($_POST['reset'])) {
                do_action('fp_action_to_reset_settings_' . $current_tab);
            }
            $error = ( empty($_GET['wc_error']) ) ? '' : urldecode(stripslashes($_GET['wc_error']));
            $message = ( empty($_GET['wc_message']) ) ? '' : urldecode(stripslashes($_GET['wc_message']));

            if ($error || $message) {

                if ($error) {
                    echo '<div id="message" class="error fade"><p><strong>' . esc_html($error) . '</strong></p></div>';
                } else {
                    echo '<div id="message" class="updated fade"><p><strong>' . esc_html($message) . '</strong></p></div>';
                }
            } elseif (!empty($_GET['saved'])) {

                echo '<div id="message" class="updated fade"><p><strong>' . __('Your settings have been saved.', 'rewardsystem') . '</strong></p></div>';
            }
            ?>
            <div class="wrap woocommerce">
                <form method="post" id="mainform" action="" enctype="multipart/form-data">
                    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div><h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
                        <?php
                        $tabs = '';
                        $tabs = apply_filters('woocommerce_rs_settings_tabs_array', $tabs);

                        foreach ($tabs as $name => $label) {
                            // echo $current_tab;
                            echo '<a href="' . admin_url('admin.php?page=rewardsystem_callback&tab=' . $name) . '" class="nav-tab ';
                            if ($current_tab == $name)
                                echo 'nav-tab-active';
                            echo '">' . $label . '</a>';
                        }
                        do_action('woocommerce_rs_settings_tabs');
                        ?>
                    </h2>

                    <?php
                    switch ($current_tab) :
                        default :
                            do_action('woocommerce_rs_settings_tabs_' . $current_tab);
                            break;
                    endswitch;
                    ?>
                    <p class="submit sumo_reward_points">
                        <?php if (!isset($GLOBALS['hide_save_button'])) : ?>
                            <input name="save" class="button-primary" type="submit" value="<?php _e('Save changes', 'rewardsystem'); ?>" />
                        <?php endif; ?>
                        <input type="hidden" name="subtab" id="last_tab" />
                        <?php wp_nonce_field('woocommerce-settings', '_wpnonce', true, true); ?>
                    </p>
                </form>
                <?php
                if (get_option('rs_show_hide_reset_all') == '1') {
                    if ($current_tab != 'rewardsystem_advanced') {
                        ?>
                        <form method="post" id="mainforms" action="" enctype="multipart/form-data" style="float: left; margin-top: -52px; margin-left: 159px;">
                            <input id="resettab" name="reset" class="button-secondary" type="submit" value="<?php _e('Reset', 'rewardsystem'); ?>"/>
                            <?php wp_nonce_field('woocommerce-reset_settings', '_wpnonce', true, true); ?>             
                        </form>
                    </div>   
                    <?php
                }
            }
        }

        public static function rs_function_to_display_screen_option() {
            if (isset($_GET['tab'])) {
                $array = array(
                    'rewardsystem_offline_online_rewards' => $_GET['tab'] == 'rewardsystem_offline_online_rewards',
                    'rewardsystem_masterlog' => $_GET['tab'] == 'rewardsystem_masterlog',
                    'rewardsystem_nominee' => $_GET['tab'] == 'rewardsystem_nominee',
                    'rewardsystem_referrallog' => $_GET['tab'] == 'rewardsystem_referrallog',
                    'rewardsystem_user_reward_points' => $_GET['tab'] == 'rewardsystem_user_reward_points',
                    'rewardsystem_request_for_cash_back' => $_GET['tab'] == 'rewardsystem_request_for_cash_back',
                    'rewardsystem_request_for_send_points' => $_GET['tab'] == 'rewardsystem_request_for_send_points',
                    'rs_points_url' => $_GET['tab'] == 'rs_points_url',
                );
                if (is_array($array) && !empty($array)) {
                    foreach ($array as $option_name => $tab_name) {
                        if ($tab_name) {
                            $screen = get_current_screen();
                            $args = array(
                                'label' => __('Number Of Items Per Page', 'rewardsystem'),
                                'default' => 10,
                                'option' => $option_name
                            );
                            add_screen_option('per_page', $args);
                        }
                    }
                }
            }
        }

        public static function rs_set_screen_option_value($status, $option, $value) {
            if ('rewardsystem_offline_online_rewards' == $option)
                return $value;

            if ('rewardsystem_masterlog' == $option)
                return $value;

            if ('rewardsystem_nominee' == $option)
                return $value;

            if ('rewardsystem_referrallog' == $option)
                return $value;

            if ('rewardsystem_user_reward_points' == $option)
                return $value;

            if ('rewardsystem_request_for_cash_back' == $option)
                return $value;

            if ('rewardsystem_request_for_send_points' == $option)
                return $value;

            if ('rs_points_url' == $option)
                return $value;
        }

        public static function rs_get_value_for_no_of_item_perpage($user, $screen) {
            $screen_option = $screen->get_option('per_page', 'option');
            $per_page = get_user_meta($user, $screen_option, true);
            if (empty($per_page) || $per_page < 1) {
                $per_page = $screen->get_option('per_page', 'default');
            }
            return $per_page;
        }

        public static function rs_function_to_reset_setting($settings) {
            foreach ($settings as $setting) {
                if (isset($setting['newids']) && isset($setting['std'])) {
                    delete_option($setting['newids']);
                    add_option($setting['newids'], $setting['std']);
                }
            }
        }

        /**
         * Show action links on the plugin screen.
         *
         * @param	mixed $links Plugin Action links
         * @return	array
         */
        public static function rs_plugin_action($links) {
            $action_links = array(
                'rsaboutpage' => '<a href="' . admin_url('admin.php?page=rewardsystem_callback') . '" aria-label="' . esc_attr__('Settings', 'rewardsystem') . '">' . esc_attr__('Settings', 'rewardsystem') . '</a>',
            );
            return array_merge($action_links, $links);
        }

        /**
         * Show row meta on the plugin screen.
         *
         * @param	mixed $links Plugin Row Meta
         * @param	mixed $file  Plugin Base file
         * @return	array
         */
        public static function rs_plugin_row_meta($links, $file) {
            if (REWARDSYSTEM_PLUGIN_BASENAME == $file) {
                $redirect_url = add_query_arg(array('page' => 'sumo-reward-points-welcome-page'), admin_url('admin.php'));
                $row_meta = array(
                    'rs_about' => '<a href="' . $redirect_url . '" aria-label="' . esc_attr__('About', 'rewardsystem') . '">' . esc_html__('About', 'rewardsystem') . '</a>',
                    'rs_support' => '<a href="http://fantasticplugins.com/support/" aria-label="' . esc_attr__('Support', 'rewardsystem') . '">' . esc_html__('Support', 'rewardsystem') . '</a>',
                );

                return array_merge($links, $row_meta);
            }
            return (array) $links;
        }

    }

    RSTabManagement::init();
}