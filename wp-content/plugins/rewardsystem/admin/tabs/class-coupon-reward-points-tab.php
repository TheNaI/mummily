<?php
/*
 * Coupon Reward Points
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSCouponRewardPoints')) {

    class RSCouponRewardPoints {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_coupon_reward_points', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_coupon_reward_points', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings')); // call the init function to update the default settings on page load

            add_action('woocommerce_admin_field_rs_coupon_usage_points_dynamics', array(__CLASS__, 'reward_add_coupon_usage_points_to_action'));
            
            add_action('fp_action_to_reset_settings_rewardsystem_coupon_reward_points', array(__CLASS__, 'rs_function_to_reset_coupon_reward_points_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_coupon_reward_points'] = __('Coupon Reward Points', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            return apply_filters('woocommerce_rewardsystem_coupon_reward_points_settings', array(
                array(
                    'name' => __('Coupon Reward Points Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_coupon_reward_points_setting'
                ),
                array(
                    'name' => __('When different Points is associated with the same Coupon Code then', 'rewardsystem'),
                    'desc' => __('If more than one type(level) is enabled then use the highest/lowest points for the Coupons ', 'rewardsystem'),
                    'id' => 'rs_choose_priority_level_selection_coupon_points',
                    'class' => 'rs_choose_priority_level_selection_coupon_points',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'newids' => 'rs_choose_priority_level_selection_coupon_points',
                    'options' => array(
                        '1' => __('Rule with the highest number of points will be awarded', 'rewardsystem'),
                        '2' => __('Rule with the lowest number of points will be awarded', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Award Points to User when they apply a WooCommerce Coupon in Cart', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_reward_point_coupon_message_settings'
                ),
                array(
                    'name' => __('Message displayed when Coupon Reward Points is Earned', 'rewardsystem'),
                    'desc' => __('This messgae will be displayed when The User applies the Selected coupons for Reward Points ', 'rewardsystem'),
                    'id' => 'rs_coupon_applied_reward_success',
                    'css' => 'min-width:550px;',
                    'std' => 'You have received [coupon_rewardpoints] Points for using the coupon [coupon_name]',
                    'type' => 'textarea',
                    'newids' => 'rs_coupon_applied_reward_success',
                    'default' => 'You have received [coupon_rewardpoints] Points for using the coupon [coupon_name]',
                    'desc_tip' => true,
                ),
                array(
                    'type' => 'rs_coupon_usage_points_dynamics',
                ),
                array('type' => 'sectionend', 'id' => '_rs_coupon_reward_points_setting'),
                array(
                    'name' => __('Shortcode used in Coupon Reward Points', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_shortcode_for_coupon'
                ),
                array(
                    'type' => 'title',
                    'desc' => '<b>[coupon_rewardpoints]</b> - To display points earned for using coupon code<br><br>'
                    . '<b>[coupon_name]</b> - To display coupon name<br><br>',                    
                ),
                array('type' => 'sectionend', 'id' => 'rs_shortcode_for_coupon'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields(RSCouponRewardPoints::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSCouponRewardPoints::reward_system_admin_fields());
            $rewards_dynamic_rulerule_couponpoints = array_values($_POST['rewards_dynamic_rule_coupon_usage']);
            update_option('rewards_dynamic_rule_couponpoints', $rewards_dynamic_rulerule_couponpoints);
            return false;
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSCouponRewardPoints::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function reward_add_coupon_usage_points_to_action() {
            wp_nonce_field(plugin_basename(__FILE__), 'rsdynamicrulecreation_coupon_usage');
            global $woocommerce;
            ?>
            <style type="text/css">
                .coupon_code_points_selected{
                    width: 60%!important;
                }
                .coupon_code_points{
                    width: 60%!important;
                }
                .chosen-container-multi {
                    position:absolute!important;
                }


            </style>
            <table class="widefat fixed rsdynamicrulecreation_coupon_usage" cellspacing="0">
                <thead>
                    <tr>

                        <th class="manage-column column-columnname" scope="col"><?php _e('Coupon Codes', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e('Reward Points', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e('Remove Rule', 'rewardsystem'); ?></th>
                    </tr>
                </thead>
                <tbody id="here">
                    <?php
                    $rewards_dynamic_rulerule_coupon_points = get_option('rewards_dynamic_rule_couponpoints');
                    $i = 0;
                    if (is_array($rewards_dynamic_rulerule_coupon_points)) {
                        foreach ($rewards_dynamic_rulerule_coupon_points as $rewards_dynamic_rule) {
                            ?>
                            <tr>
                                <td class="column-columnname">
                                    <p class="form-field">
                                        <select multiple="multiple" name="rewards_dynamic_rule_coupon_usage[<?php echo $i; ?>][coupon_codes][]" class="short coupon_code_points_selected">
                                            <?php
                                            if (isset($rewards_dynamic_rule["coupon_codes"]) && $rewards_dynamic_rule["coupon_codes"] != "") {
                                                $coupons_list = $rewards_dynamic_rule["coupon_codes"];
                                                foreach ($coupons_list as $separate_coupons) {
                                                    ?>
                                                    <option value="<?php echo $separate_coupons; ?>" selected><?php echo $separate_coupons; ?></option>
                                                    <?php
                                                }
                                                foreach (get_posts('post_type=shop_coupon') as $value) {
                                                    $coupon_title = $value->post_title;
                                                    $coupon_object = new WC_Coupon($coupon_title);
                                                    $couponcodeuserid = get_userdata($value->post_author);
                                                    $couponcodeuserlogin = $couponcodeuserid->user_login;
                                                    $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
                                                    if ($usernickname != $value->post_title) {
                                                        if (!in_array($coupon_title, $coupons_list)) {
                                                            ?>
                                                            <option value="<?php echo $coupon_title; ?>"><?php echo $coupon_title; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </p>
                                </td>


                                <td class="column-columnname">
                                    <p class="form-field">
                                        <input type="text" name="rewards_dynamic_rule_coupon_usage[<?php echo $i; ?>][reward_points]" value="<?php echo $rewards_dynamic_rule["reward_points"]; ?>" />
                                    </p>
                                </td>
                                <td class="column-columnname num">
                                    <span class="remove button-secondary"><?php _e('Remove Rule', 'rewardsystem'); ?></span>
                                </td>
                            </tr>


                            <?php
                            $i = $i + 1;
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>

                        <td class="manage-column column-columnname num" scope="col"> <span class="add button-primary"><?php _e('Add Rule', 'rewardsystem'); ?></span></td>
                    </tr>
                    <tr>

                        <th class="manage-column column-columnname" scope="col"><?php _e('Coupon Codes', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e('Reward Points', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e('Add Rule', 'rewardsystem'); ?></th>

                    </tr>
                </tfoot>
            </table>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('#afterclick').hide();
                    var countrewards_dynamic_rule = <?php echo $i; ?>;
                    jQuery(".add").click(function () {
                        jQuery('#afterclick').show();
                        countrewards_dynamic_rule = countrewards_dynamic_rule + 1;
                        jQuery('#here').append('<tr><td><p class="form-field"><select multiple="multiple" id = "coupon_code_points' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_coupon_usage[' + countrewards_dynamic_rule + '][coupon_codes][]" class="short coupon_points coupon_code_points"><?php
            foreach (get_posts('post_type=shop_coupon') as $value) {
                $coupon_title = $value->post_title;
                $coupon_object = new WC_Coupon($coupon_title);
                $couponcodeuserid = get_userdata($value->post_author);
                $couponcodeuserlogin = $couponcodeuserid->user_login;
                $usernickname = 'sumo_' . strtolower("$couponcodeuserlogin");
                if ($usernickname != $value->post_title) {
                    ?><option value="<?php echo $coupon_title; ?>"><?php echo $coupon_title; ?><?php
                }
            }
            ?></option></select></p></td>\n\
            \n\<td><p class="form-field"><input type = "text" name="rewards_dynamic_rule_coupon_usage[' + countrewards_dynamic_rule + '][reward_points]" class="short " /></p></td>\n\
            \n\<td class="num"><span class="remove button-secondary">Remove Rule</span></td></tr><hr>');
            <?php if ((float) $woocommerce->version > (float) ('2.2.0')) { ?>
                            jQuery('#coupon_code_points' + countrewards_dynamic_rule).select2();
            <?php } else { ?>
                            jQuery('#coupon_code_points' + countrewards_dynamic_rule).chosen();
            <?php } ?>
                    });

                    jQuery(document).on('click', '.remove', function () {
                        jQuery(this).parent().parent().remove();
                    });
            <?php if ((float) $woocommerce->version > (float) ('2.2.0')) { ?>
                        jQuery('.coupon_code_points_selected').select2();
            <?php } else { ?>
                        jQuery('.coupon_code_points_selected').chosen();
            <?php } ?>
                });



            </script>

            <?php
        }

        public static function save_data_for_dynamic_rule_coupon_points() {
            
        }
        
        public static function rs_function_to_reset_coupon_reward_points_tab() {
            $settings = RSCouponRewardPoints::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);  
            delete_option('rewards_dynamic_rule_couponpoints');
        }                

    }

    RSCouponRewardPoints::init();
}