<?php
/*
 * Nominee Setting Tab
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSNominee')) {

    class RSNominee {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_nominee', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_nominee', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings')); // call the init function to update the default settings on page load

            add_action('woocommerce_admin_field_rs_select_nominee_for_user', array(__CLASS__, 'rs_select_user_as_nominee'));

            add_action('admin_head', array(__CLASS__, 'rs_chosen_for_nominee_tab'));

            add_action('woocommerce_admin_field_rs_select_nominee_for_user_in_checkout', array(__CLASS__, 'rs_select_user_as_nominee_in_checkout'));

            add_action('woocommerce_admin_field_rs_nominee_list_table', array(__CLASS__, 'rs_function_to_display_nominee_list_table'));

            add_action('admin_head', array(__CLASS__, 'rs_function_to_enable_disable_nominee'));

            add_action('wp_ajax_nopriv_rs_action_to_enable_disable_nominee', array(__CLASS__, 'rs_ajax_function_to_enable_disable'));

            add_action('wp_ajax_rs_action_to_enable_disable_nominee', array(__CLASS__, 'rs_ajax_function_to_enable_disable'));
            
            add_action('fp_action_to_reset_settings_rewardsystem_nominee', array(__CLASS__, 'rs_function_to_reset_nominee_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_nominee'] = __('Nominee', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;
            global $wp_roles;
            foreach ($wp_roles->roles as $values => $key) {
                $userroleslug[] = $values;
                $userrolename[] = $key['name'];
            }

            $newcombineduserrole = array_combine((array) $userroleslug, (array) $userrolename);
            return apply_filters('woocommerce_rewardsystem_nominee_settings', array(
                array(
                    'name' => __('Nominee Settings for Product Purchase in Checkout Page', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_nominee_setting_in_checkout'
                ),
                array(
                    'name' => __('Nominee Field', 'rewardsystem'),
                    'id' => 'rs_show_hide_nominee_field_in_checkout',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_nominee_field_in_checkout',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('My Nominee Label', 'rewardsystem'),
                    'desc' => __('Enter the My Nominee Label', 'rewardsystem'),
                    'id' => 'rs_my_nominee_title_in_checkout',
                    'css' => 'min-width:350px;',
                    'std' => 'My Nominee',
                    'default' => 'My Nominee',
                    'type' => 'text',
                    'newids' => 'rs_my_nominee_title_in_checkout',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Nominee User Selection', 'rewardsystem'),
                    'id' => 'rs_select_type_of_user_for_nominee_checkout',
                    'css' => 'min-width:100px;',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By User(s)', 'rewardsystem'),
                        '2' => __('By User Role(s)', 'rewardsystem'),
                    ),
                    'newids' => 'rs_select_type_of_user_for_nominee_checkout',
                ),
                array(                    
                    'type' => 'rs_select_nominee_for_user_in_checkout',                    
                ),
                array(
                    'name' => __('User Role Selection', 'rewardsystem'),
                    'id' => 'rs_select_users_role_for_nominee_checkout',
                    'css' => 'min-width:343px;',
                    'std' => '',
                    'default' => '',
                    'placeholder' => 'Search for a User Role',
                    'type' => 'multiselect',
                    'options' => $newcombineduserrole,
                    'newids' => 'rs_select_users_role_for_nominee_checkout',
                    'desc_tip' => false,
                ),
                array(
                    'name' => __('Checkout Page Nominee is identified based on', 'rewardsystem'),
                    'id' => 'rs_select_type_of_user_for_nominee_name_checkout',
                    'css' => 'min-width:100px;',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('User Email ', 'rewardsystem'),
                        '2' => __('Username', 'rewardsystem'),
                    ),
                ),
                array('type' => 'sectionend', 'id' => '_rs_nominee_setting_in_checkout'),
                array(
                    'name' => __('Nominee Settings for Product Purchase in My Account Page', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_nominee_setting'
                ),
                array(
                    'name' => __('Nominee Field', 'rewardsystem'),
                    'id' => 'rs_show_hide_nominee_field',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'default' => '1',
                    'newids' => 'rs_show_hide_nominee_field',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Show', 'rewardsystem'),
                        '2' => __('Hide', 'rewardsystem'),
                    ),
                ),
                array(
                    'name' => __('My Nominee Label', 'rewardsystem'),
                    'desc' => __('Enter the My Nominee Label', 'rewardsystem'),
                    'id' => 'rs_my_nominee_title',
                    'css' => 'min-width:350px;',
                    'std' => 'My Nominee',
                    'default' => 'My Nominee',
                    'type' => 'text',
                    'newids' => 'rs_my_nominee_title',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Nominee User Selection', 'rewardsystem'),
                    'id' => 'rs_select_type_of_user_for_nominee',
                    'css' => 'min-width:100px;',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('By User(s)', 'rewardsystem'),
                        '2' => __('By User Role(s)', 'rewardsystem'),
                    ),
                    'newids' => 'rs_select_type_of_user_for_nominee',
                ),
                array(                    
                    'type' => 'rs_select_nominee_for_user',                    
                ),
                array(
                    'name' => __('User Role Selection', 'rewardsystem'),
                    'id' => 'rs_select_users_role_for_nominee',
                    'css' => 'min-width:343px;',
                    'std' => '',
                    'default' => '',
                    'placeholder' => 'Search for a User Role',
                    'type' => 'multiselect',
                    'options' => $newcombineduserrole,
                    'newids' => 'rs_select_users_role_for_nominee',
                    'desc_tip' => false,
                ),
                array(
                    'name' => __('My Account Page Nominee is identified based on', 'rewardsystem'),
                    'id' => 'rs_select_type_of_user_for_nominee_name',
                    'css' => 'min-width:100px;',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'select',
                    'options' => array(
                        '1' => __('User Email ', 'rewardsystem'),
                        '2' => __('Username', 'rewardsystem'),
                    ),
                ),
                array('type' => 'sectionend', 'id' => '_rs_nominee_setting'),
                array(
                    'name' => __('Nominated User List', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_nominated_user_list'
                ),
                array(
                    'type' => 'rs_nominee_list_table'
                ),
                array('type' => 'sectionend', 'id' => '_rs_nominated_user_list'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSNominee::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSNominee::reward_system_admin_fields());
            update_option('rs_select_users_role_for_nominee', $_POST['rs_select_users_role_for_nominee']);
            update_option('rs_select_users_list_for_nominee', $_POST['rs_select_users_list_for_nominee']);            
            update_option('rs_select_users_role_for_nominee_checkout', $_POST['rs_select_users_role_for_nominee_checkout']);
            update_option('rs_select_users_list_for_nominee_in_checkout', $_POST['rs_select_users_list_for_nominee_in_checkout']);
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            foreach (RSNominee::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        /*
         * Function to Select user as Nominee
         */

        public static function rs_select_user_as_nominee() {
            ?>
            <style type="text/css">
                .chosen-container-single {
                    position:absolute;
                }

            </style>
            <?php
            $field_id = "rs_select_users_list_for_nominee";
            $field_label = "User Selection";
            $getuser = get_option('rs_select_users_list_for_nominee');
            echo rs_function_to_add_field_for_user_select($field_id, $field_label, $getuser);
        }

        /*
         * Function for choosen in Select user role as Nominee
         */

        public static function rs_chosen_for_nominee_tab() {
            global $woocommerce;
            if (isset($_GET['page'])) {
                if (isset($_GET['tab'])) {
                    if ($_GET['tab'] == 'rewardsystem_nominee') {
                        if ((float) $woocommerce->version > (float) ('2.2.0')) {
                            echo rs_common_select_function('#rs_select_users_role_for_nominee');
                            echo rs_common_select_function('#rs_select_users_role_for_nominee_checkout');
                        } else {
                            echo rs_common_chosen_function('#rs_select_users_role_for_nominee');
                            echo rs_common_chosen_function('#rs_select_users_role_for_nominee_checkout');
                        }
                    }
                }
            }
        }

        public static function rs_select_user_as_nominee_in_checkout() {
            ?>
            <style type="text/css">
                .chosen-container-single {
                    position:absolute;
                }

            </style>
            <?php
            $field_id = "rs_select_users_list_for_nominee_in_checkout";
            $field_label = "User Selection";
            $getuser = get_option('rs_select_users_list_for_nominee_in_checkout');
            echo rs_function_to_add_field_for_user_select($field_id, $field_label, $getuser);
        }

        public static function rs_function_to_display_nominee_list_table() {
            $newwp_list_table_for_users = new WP_List_Table_for_Nominee();
            $newwp_list_table_for_users->prepare_items();
            $plugin_url = WP_PLUGIN_URL;
            $newwp_list_table_for_users->display();
        }

        public static function rs_function_to_enable_disable_nominee() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('.rs_enable_disable').click(function () {
                        var userid = jQuery(this).attr('data-userid');
                        var checkboxvalue = jQuery(this).is(':checked') ? 'yes' : 'no';
                        var nomineeid = jQuery(this).attr('data-nomineeid');
                        var dataparam = ({
                            action: 'rs_action_to_enable_disable_nominee',
                            userid: userid,
                            checkboxvalue: checkboxvalue,
                            nomineeid: nomineeid
                        });
                        jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                function (response) {
                                    console.log(response);
                                }, 'json');
                    });
                });
            </script>
            <?php
        }

        public static function rs_ajax_function_to_enable_disable() {
            if (isset($_POST['userid']) && $_POST['userid'] != '') {
                $userid = $_POST['userid'];
                $nomineeid = $_POST['nomineeid'];
                if (isset($_POST['checkboxvalue'])) {
                    update_user_meta($userid, 'rs_enable_nominee', $_POST['checkboxvalue']);
                }
            }
        }
        
        public static function rs_function_to_reset_nominee_tab() {
            $settings = RSNominee::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);            
        }

    }

    RSNominee::init();
}