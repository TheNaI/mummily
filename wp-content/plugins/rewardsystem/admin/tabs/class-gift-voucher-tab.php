<?php
/*
 * Gift Voucher Tab Setting
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSOfflineOnlineRewards')) {

    class RSOfflineOnlineRewards {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_settings')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_offline_online_rewards', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_offline_online_rewards', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'));

            add_action('woocommerce_admin_field_rs_offline_online_rewards_voucher_settings', array(__CLASS__, 'settings_for_voucher_code_offline_online_rewards'));

            add_action('woocommerce_admin_field_rs_offline_online_rewards_display_table_settings', array(__CLASS__, 'table_to_display_created_voucher_codes'));

            add_action('admin_head', array(__CLASS__, 'import_giftvoucher'));

            add_action('fp_action_to_reset_settings_rewardsystem_offline_online_rewards', array(__CLASS__, 'rs_function_to_reset_gift_voucher_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_settings($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_offline_online_rewards'] = __('Gift Voucher', 'rewardsystem');
            return array_filter($setting_tabs);
        }

        /*
         * Function for Admin Settings
         * 
         */

        public static function reward_system_admin_fields() {

            return apply_filters('woocommerce_rewardsystem_offline_online_rewards_settings', array(
                array(
                    'name' => __('Gift Voucher Reward Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_offline_to_online_rewards_settings'
                ),
                array(
                    'type' => 'rs_offline_online_rewards_voucher_settings',
                ),
                array(
                    'type' => 'rs_offline_online_rewards_display_table_settings',
                ),
                array(
                    'name' => __('Gift Voucher Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_gift_voucher_message_settings',
                ),
                array(
                    'name' => __('Error Message displayed when a Gift Voucher field is left empty', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when Redeem Voucher Button is clicked without entering the voucher code ', 'rewardsystem'),
                    'id' => 'rs_voucher_redeem_empty_error',
                    'css' => 'min-width:550px;',
                    'std' => 'Please Enter your Voucher Code',
                    'default' => 'Please Enter your Voucher Code',
                    'type' => 'text',
                    'newids' => 'rs_voucher_redeem_empty_error',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Success Message displayed when a Gift Voucher is Redeemed', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when the Gift Voucher has been Successfully Redeemed', 'rewardsystem'),
                    'id' => 'rs_voucher_redeem_success_message',
                    'css' => 'min-width:550px;',
                    'std' => '[giftvoucherpoints] Reward points has been added to your Account',
                    'default' => '[giftvoucherpoints] Reward points has been added to your Account',
                    'type' => 'text',
                    'newids' => 'rs_voucher_redeem_success_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message displayed when a User tries to Redeem Expired Voucher Code', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when the Gift Voucher has been Successfully Redeemed', 'rewardsystem'),
                    'id' => 'rs_voucher_code_expired_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Voucher has been Expired',
                    'default' => 'Voucher has been Expired',
                    'type' => 'text',
                    'newids' => 'rs_voucher_code_expired_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message displayed when a User tries to Redeem an Invalid Voucher Code', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed when a Invalid Voucher is used for Redeeming', 'rewardsystem'),
                    'id' => 'rs_invalid_voucher_code_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Sorry, Voucher not found in list',
                    'default' => 'Sorry, Voucher not found in list',
                    'type' => 'text',
                    'newids' => 'rs_invalid_voucher_code_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Login Link Label for Guests', 'rewardsystem'),
                    'desc' => __('Please Enter Login link for Guest Label', 'rewardsystem'),
                    'id' => 'rs_redeem_voucher_login_link_label',
                    'css' => 'min-width:200px;',
                    'std' => 'Login',
                    'default' => 'Login',
                    'type' => 'text',
                    'newids' => 'rs_redeem_voucher_login_link_label',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Message displayed for Guests', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed for Guest when Gift Voucher Shortcode is used', 'rewardsystem'),
                    'id' => 'rs_voucher_redeem_guest_error_message',
                    'css' => 'min-width:550px;',
                    'std' => 'Please [rs_login_link] to View this Page',
                    'default' => 'Please [rs_login_link] to View this Page',
                    'type' => 'text',
                    'newids' => 'rs_voucher_redeem_guest_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Error Message displayed when a User tries to Redeem a used Voucher Code', 'rewardsystem'),
                    'desc' => __('Enter the Message that will be displayed when User tries to Redeem a Voucher code that has already been Used', 'rewardsystem'),
                    'id' => 'rs_voucher_code_used_error_message',
                    'css' => 'min-width:200px;',
                    'std' => 'Voucher has been used',
                    'default' => 'Voucher has been used',
                    'type' => 'text',
                    'newids' => 'rs_voucher_code_used_error_message',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Message displayed for Banned Users', 'rewardsystem'),
                    'desc' => __('Enter the Message that will be displayed when a Banned User tries to Redeem the Gift Voucher', 'rewardsystem'),
                    'id' => 'rs_banned_user_redeem_voucher_error',
                    'css' => 'min-width:400px;',
                    'std' => 'You have Earned 0 Points',
                    'default' => 'You have Earned 0 Points',
                    'type' => 'textarea',
                    'newids' => 'rs_banned_user_redeem_voucher_error',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_gift_voucher_message_settings'),
                array(
                    'name' => __('Voucher Code Form Customization', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_offline_to_online_form_customize_settings'
                ),
                array(
                    'name' => __('Voucher Code field Label', 'rewardsystem'),
                    'id' => 'rs_reward_code_field_caption',
                    'css' => 'min-width:350px;',
                    'std' => 'Enter your Voucher Code below to Claim',
                    'default' => 'Enter your Voucher Code below to Claim',
                    'type' => 'text',
                    'newids' => 'rs_reward_code_field_caption',
                ),
                array(
                    'name' => __('Placeholder for Voucher Code Field', 'rewardsystem'),
                    'id' => 'rs_reward_code_field_placeholder',
                    'css' => 'min-width:350px;',
                    'std' => 'Voucher Code',
                    'default' => 'Voucher Code',
                    'type' => 'text',
                    'newids' => 'rs_reward_code_field_placeholder',
                ),
                array(
                    'name' => __('Submit Button Field Caption', 'rewardsystem'),
                    'id' => 'rs_reward_code_submit_field_caption',
                    'css' => 'min-width:350px;',
                    'std' => 'Submit',
                    'type' => 'text',
                    'newids' => 'rs_reward_code_submit_field_caption',
                ),
                array('type' => 'sectionend', 'id' => '_rs_offline_to_online_form_customize_settings'),
                array(
                    'name' => __('Current Balance Message Customization', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_current_balance_shortcode_customization'
                ),
                array(
                    'name' => __('Current Available Points Label', 'rewardsystem'),
                    'id' => 'rs_current_available_balance_caption',
                    'css' => 'min-width:350px;',
                    'std' => 'Current Balance:',
                    'default' => 'Current Balance:',
                    'type' => 'text',
                    'newids' => 'rs_current_available_balance_caption',
                ),
                array('type' => 'sectionend', 'id' => '_rs_current_balance_shortcode_customization'),
                array(
                    'name' => __('Shortcode used in Gift Vocuher', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_shortcode_for_gift_voucher'
                ),
                array(
                    'type' => 'title',
                    'desc' => '<b>[giftvoucherpoints]</b> - To display points earned for using voucher code<br><br>'
                    . '<b>[rs_login_link]</b> - To display login link for guests',
                ),
                array('type' => 'sectionend', 'id' => 'rs_shortcode_for_gift_voucher'),
                array('type' => 'sectionend', 'id' => '_rs_offline_to_online_rewards_settings'),
            ));
        }

        /*
         * Register  the Admin Field Settings
         * 
         */

        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSOfflineOnlineRewards::reward_system_admin_fields());
        }

        /*
         * Update Settings for Offline Online Rewards tab    
         * 
         */

        public static function reward_system_update_settings() {
            woocommerce_update_options(RSOfflineOnlineRewards::reward_system_admin_fields());
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSOfflineOnlineRewards::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function settings_for_voucher_code_offline_online_rewards() {
            $security = rs_function_to_create_security();
            $isadmin = is_admin() ? 'yes' : 'no';
            ?>
            <h3><?php _e('Voucher Code Settings', 'rewardsystem'); ?></h3>
            <table class="form-table">
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label><?php _e('Prefix/Suffix', 'rewardsystem'); ?></label>
                    </th>
                    <td class="forminp forminp-select">
                        <input type="checkbox" name="rs_enable_prefix_offline_online_rewards" class="rs_enable_prefix_offline_online_rewards"><span><?php _e('Prefix', 'rewardsystem'); ?></span>
                        <input type="text" name="rs_voucher_prefix_offline_online" class="rs_voucher_prefix_offline_online" />
                        <span class="rs_voucher_code_creation_error_for_prefix"></span><br><br>
                        <input type="checkbox" name="rs_enable_suffix_offline_online_rewards" class="rs_enable_suffix_offline_online_rewards"><span><?php _e('Suffix', 'rewardsystem'); ?></span>
                        <input type="text" name="rs_voucher_suffix_offline_online" class="rs_voucher_suffix_offline_online" />
                        <span class="rs_voucher_code_creation_error_for_suffix"></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label><?php _e('Voucher Code Type', 'rewardsystem'); ?></label><br/><br/>
                    </th>
                    <td class="forminp forminp-select">
                        <select name="rs_reward_code_type" id="rs_reward_code_type">
                            <option value="numeric"><?php _e('Numeric', 'rewardsystem'); ?></option>
                            <option value="alphanumeric"><?php _e('Alphanumeric', 'rewardsystem'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label class="rs_exclude_characters_code_generation_label"><?php _e('Excluded Alphabets from Voucher Code creation', 'rewardsystem'); ?></label>                    
                    </th>
                    <td class="forminp forminp-select">
                        <input type="text" name="rs_exclude_characters_code_generation" class="rs_exclude_characters_code_generation" />
                        <label class="exclude_caption">Alphabets are comma separated(For eg: i,l,o)</label>
                    </td>
                </tr>            
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label><?php _e('Voucher Code Length', 'rewardsystem'); ?></label>                    
                    </th>
                    <td class="forminp forminp-select">
                        <input type="number" step="1" min="0" name="rs_voucher_code_length_offline_online" class="rs_voucher_code_length_offline_online" />
                        <span class="rs_voucher_code_creation_error_for_character"></span>
                    </td>
                </tr>            
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label><?php _e('Reward Points per Voucher Code', 'rewardsystem'); ?></label>                    
                    </th>
                    <td class="forminp forminp-select">
                        <input type="number" step="any" min="0" name="rs_voucher_code_points_value_offline_online" class="rs_voucher_code_points_value_offline_online" />
                        <span class="rs_voucher_code_creation_error_for_rpv"></span>
                    </td>
                </tr>            
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label><?php _e('Number of Voucher Codes to generate', 'rewardsystem'); ?></label>                    
                    </th>
                    <td class="forminp forminp-select">
                        <input type="number" step="any" min="0" name="rs_voucher_code_count_offline_online" class="rs_voucher_code_count_offline_online" />
                        <span class="rs_voucher_code_creation_error_for_noofrc"></span>
                    </td>
                </tr>            
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label><?php _e('Expiry Date of Voucher Code(s)', 'rewardsystem'); ?></label>                    
                    </th>
                    <td class="forminp forminp-select">
                        <input type="text" class="rs_gift_voucher_expiry" value="" name="rs_gift_voucher_expiry" id="rs_gift_voucher_expiry" />
                        <span class="rs_voucher_code_creation_error_for_expdate"></span>
                    </td>
                </tr>            
            </table>
            <div id="dialog1" hidden="hidden" ></div>
            <button class="button-primary rs_create_voucher_codes_offline_online" ><?php _e('Create Voucher Codes', 'rewardsystem'); ?></button>            
            <span class="preloader_image_online_offline_rewards"><img src="<?php echo WP_PLUGIN_URL; ?>/rewardsystem/admin/images/update.gif" style="width:32px;height:32px; position: absolute;"/></span><br/><br/>        
            <style type="text/css">
                .rs_voucher_code_creation_error_for_character{
                    font-size: 16px;
                    color: red;
                }
                .rs_voucher_code_creation_error_for_rpv{
                    font-size: 16px;
                    color: red;
                }

                .rs_voucher_code_creation_error_for_noofrc{
                    font-size: 16px;
                    color: red;
                }

                .rs_voucher_code_creation_error_for_expdate{
                    font-size: 16px;
                    color: red;
                }

                .rs_voucher_code_creation_error_for_prefix{
                    font-size: 16px;
                    color: red;
                }

                .rs_voucher_code_creation_error_for_suffix{
                    font-size: 16px;
                    color: red;
                }
            </style>    
            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
            <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery("#opener").click(function () {

                    });
                    jQuery('#rs_gift_voucher_expiry').datepicker({dateFormat: 'yy-mm-dd', minDate: 0});
                    jQuery('.preloader_image_online_offline_rewards').css("display", "none");
                    var reward_code_type = jQuery('#rs_reward_code_type').val();
                    if (reward_code_type == 'numeric') {
                        jQuery('.rs_exclude_characters_code_generation_label').closest('tr').hide();
                    } else {
                        jQuery('.rs_exclude_characters_code_generation_label').closest('tr').show();
                    }
                    jQuery('#rs_reward_code_type').change(function () {
                        if (jQuery(this).val() == 'numeric') {
                            jQuery('.rs_exclude_characters_code_generation_label').closest('tr').hide();
                        } else {
                            jQuery('.rs_exclude_characters_code_generation_label').closest('tr').show();
                        }
                    });
                    jQuery('.rs_create_voucher_codes_offline_online').click(function () {
                        var prefix_enabled_value = jQuery('.rs_enable_prefix_offline_online_rewards').is(":checked") ? 'yes' : 'no';
                        var prefix_content = jQuery('.rs_voucher_prefix_offline_online').val();
                        var suffix_enabled_value = jQuery('.rs_enable_suffix_offline_online_rewards').is(":checked") ? 'yes' : 'no';
                        var sufffix_content = jQuery('.rs_voucher_suffix_offline_online').val();
                        var reward_code_type = jQuery('#rs_reward_code_type').val();
                        var exclude_content_code = jQuery('.rs_exclude_characters_code_generation').val();
                        var length_of_voucher_code = jQuery('.rs_voucher_code_length_offline_online').val();
                        var points_value_of_voucher_code = jQuery('.rs_voucher_code_points_value_offline_online').val();
                        var number_of_vouchers_to_be_created = jQuery('.rs_voucher_code_count_offline_online').val();
                        var gift_expired_date = jQuery('#rs_gift_voucher_expiry').val();
                        jQuery(this).attr('data-clicked', '1');
                        var dataclicked = jQuery(this).attr('data-clicked');
                        var dataparam = ({
                            action: 'rs_create_voucher_code',
                            proceedanyway: dataclicked,
                            prefix_enabled_value: prefix_enabled_value,
                            prefix_content: prefix_content,
                            suffix_enabled_value: suffix_enabled_value,
                            number_of_vouchers_to_be_created: number_of_vouchers_to_be_created,
                            sufffix_content: sufffix_content,
                            reward_code_type: reward_code_type,
                            length_of_voucher_code: length_of_voucher_code,
                            points_value_of_voucher_code: points_value_of_voucher_code,
                            exclude_content_code: exclude_content_code,
                            vouchercreated: '<?php echo date('Y-m-d'); ?>',
                            gift_expired_date: gift_expired_date,
                            security: "<?php echo $security; ?>",
                            state: "<?php echo $isadmin; ?>"
                        });
                        if (prefix_enabled_value === 'yes' && suffix_enabled_value === 'yes') {
                            if (prefix_content === '') {
                                jQuery('.rs_voucher_code_creation_error_for_prefix').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_prefix').html('<?php _e('Prefix value Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_prefix').fadeOut(5000);
                                return false;
                            }

                            if (sufffix_content === '') {
                                jQuery('.rs_voucher_code_creation_error_for_suffix').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_suffix').html('<?php _e('Suffix value Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_suffix').fadeOut(5000);
                                return false;
                            }
                            if (length_of_voucher_code === '') {
                                jQuery('.rs_voucher_code_creation_error_for_character').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_character').html('<?php _e('Number of Characters for Voucher Code Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_character').fadeOut(5000);
                                return false;
                            }
                            if (points_value_of_voucher_code === '') {
                                jQuery('.rs_voucher_code_creation_error_for_rpv').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_rpv').html('<?php _e('Reward Points Value per Voucher Code Generated Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_rpv').fadeOut(5000);
                                return false;
                            }
                            if (number_of_vouchers_to_be_created === '') {
                                jQuery('.rs_voucher_code_creation_error_for_noofrc').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_noofrc').html('<?php _e('Number of Voucher Codes to be Generated Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_noofrc').fadeOut(5000);
                                return false;
                            }
                        } else if (prefix_enabled_value != 'yes' && suffix_enabled_value != 'yes') {
                            if (length_of_voucher_code === '') {
                                jQuery('.rs_voucher_code_creation_error_for_character').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_character').html('<?php _e('Number of Characters for Voucher Code Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_character').fadeOut(5000);
                                return false;
                            }
                            if (points_value_of_voucher_code === '') {
                                jQuery('.rs_voucher_code_creation_error_for_rpv').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_rpv').html('<?php _e('Reward Points Value per Voucher Code Generated Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_rpv').fadeOut(5000);
                                return false;
                            }
                            if (number_of_vouchers_to_be_created === '') {
                                jQuery('.rs_voucher_code_creation_error_for_noofrc').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_noofrc').html('<?php _e('Number of Voucher Codes to be Generated Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_noofrc').fadeOut(5000);
                                return false;
                            }
                        } else if (prefix_enabled_value == 'yes' && suffix_enabled_value != 'yes') {
                            if (prefix_content == '') {
                                jQuery('.rs_voucher_code_creation_error_for_prefix').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_prefix').html('<?php _e('Prefix value Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_prefix').fadeOut(5000);
                                return false;
                            }
                            if (length_of_voucher_code === '') {
                                jQuery('.rs_voucher_code_creation_error_for_character').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_character').html('<?php _e('Number of Characters for Voucher Code Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_character').fadeOut(5000);
                                return false;
                            }
                            if (points_value_of_voucher_code === '') {
                                jQuery('.rs_voucher_code_creation_error_for_rpv').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_rpv').html('<?php _e('Reward Points Value per Voucher Code Generated Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_rpv').fadeOut(5000);
                                return false;
                            }
                            if (number_of_vouchers_to_be_created === '') {
                                jQuery('.rs_voucher_code_creation_error_for_noofrc').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_noofrc').html('<?php _e('Number of Voucher Codes to be Generated Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_noofrc').fadeOut(5000);
                                return false;
                            }
                        } else if (prefix_enabled_value != 'yes' && suffix_enabled_value == 'yes') {
                            if (sufffix_content == '') {
                                jQuery('.rs_voucher_code_creation_error_for_suffix').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_suffix').html('<?php _e('Suffix value Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_suffix').fadeOut(5000);
                                return false;
                            }
                            if (length_of_voucher_code === '') {
                                jQuery('.rs_voucher_code_creation_error_for_character').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_character').html('<?php _e('Number of Characters for Voucher Code Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_character').fadeOut(5000);
                                return false;
                            }
                            if (points_value_of_voucher_code === '') {
                                jQuery('.rs_voucher_code_creation_error_for_rpv').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_rpv').html('<?php _e('Reward Points Value per Voucher Code Generated Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_rpv').fadeOut(5000);
                                return false;
                            }
                            if (number_of_vouchers_to_be_created === '') {
                                jQuery('.rs_voucher_code_creation_error_for_noofrc').fadeIn();
                                jQuery('.rs_voucher_code_creation_error_for_noofrc').html('<?php _e('Number of Voucher Codes to be Generated Should not be Empty', 'rewardsystem'); ?>');
                                jQuery('.rs_voucher_code_creation_error_for_noofrc').fadeOut(5000);
                                return false;
                            }
                        }
                        jQuery('.preloader_image_online_offline_rewards').css("display", "inline");
                        function getvouchercountofflinerewards(id) {
                            return jQuery.ajax({
                                type: 'POST',
                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                data: ({
                                    action: 'rssplitvouchercode',
                                    ids: id,
                                    prefix_enabled_value: prefix_enabled_value,
                                    prefix_content: prefix_content,
                                    suffix_enabled_value: suffix_enabled_value,
                                    number_of_vouchers_to_be_created: number_of_vouchers_to_be_created,
                                    sufffix_content: sufffix_content,
                                    reward_code_type: reward_code_type,
                                    length_of_voucher_code: length_of_voucher_code,
                                    points_value_of_voucher_code: points_value_of_voucher_code,
                                    exclude_content_code: exclude_content_code,
                                    vouchercreated: '<?php echo date('Y-m-d'); ?>',
                                    gift_expired_date: gift_expired_date,
                                    security: "<?php echo $security; ?>",
                                    state: "<?php echo $isadmin; ?>"
                                }),
                                success: function (response) {
                                    console.log(response);
                                },
                                dataType: 'json',
                                async: false
                            });

                        }
                        jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                function (response) {
                                    console.log(response);

                                    if (response != 'success') {
                                        var j = 1;
                                        var i, j, temparray, chunk = 10;
                                        for (i = 0, j = response.length; i < j; i += chunk) {
                                            temparray = response.slice(i, i + chunk);
                                            getvouchercountofflinerewards(temparray);
                                        }
                                        jQuery.when(getvouchercountofflinerewards()).done(function (a1) {
                                            var uniquekey = [];
                                            jQuery.each(response, function (i, el) {
                                                if (jQuery.inArray(el, uniquekey) === -1) {
                                                    uniquekey.push(el);
                                                }
                                            });
                                            if (number_of_vouchers_to_be_created > uniquekey.length + 1) {

                                                jQuery("#dialog1").dialog({
                                                    buttons: [
                                                        {
                                                            text: "Ok",
                                                            icons: {
                                                                primary: "ui-icon-heart"
                                                            },
                                                            click: function () {
                                                                jQuery(this).dialog("close");
                                                                location.reload();
                                                            }

                                                        }
                                                    ]

                                                });
                                                jQuery('div#dialog1').on('dialogclose', function () {
                                                    location.reload();
                                                });
                                                jQuery("#dialog1").html(+uniquekey.length + 'Unique code is Generated Please Increase number of Character to Create More Voucher');
                                            } else {
                                                location.reload();
                                            }
                                            jQuery('.rs_voucher_prefix_offline_online').val('');
                                            jQuery('.rs_voucher_suffix_offline_online').val('');
                                            jQuery('#rs_reward_code_type').val('');
                                            jQuery('.rs_exclude_characters_code_generation').val('');
                                            jQuery('.rs_voucher_code_length_offline_online').val('');
                                            jQuery('.rs_voucher_code_points_value_offline_online').val('');
                                            jQuery('.rs_voucher_code_count_offline_online').val('');
                                            jQuery('#rs_gift_voucher_expiry').val('');
                                            console.log('Ajax Done Successfully');
                                            jQuery('.preloader_image_online_offline_rewards').css("display", "none");
                                        });
                                    }
                                }, 'json');
                        return false;
                    });
                });
            </script>    
            <?php
        }

        public static function table_to_display_created_voucher_codes() {
            ?>
            <style type="text/css">
                .rs_reward_code_vouchers_click {
                    border: 2px solid #a1a1a1;
                    padding: 3px 9px;
                    background: #dddddd;
                    width: 5px;
                    border-radius: 25px;
                }

                .rs_reward_code_vouchers_click:hover {
                    cursor: pointer;
                    background:red;
                    color:#fff;
                    border: 2px solid #fff;
                }
            </style>
            <table class="form-table">
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label for="rs_import_gift_voucher_csv"><?php _e('Export Voucher Codes as CSV', 'rewardsystem'); ?></label>
                    </th>
                    <td class="forminp forminp-select">
                        <input type="submit" id="rs_export_reward_codes_csv" name="rs_export_reward_codes_csv" value="Export Voucher Codes"/>
                    </td>

                </tr>

                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label for="rs_import_gift_voucher_csv"><?php _e('Import Gift Voucher to CSV', 'rewardsystem'); ?></label>
                    </th>
                    <td class="forminp forminp-select">
                        <input type="file" id="rs_import_gift_voucher_csv" name="file" />
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label for="rs_voucher_code_import_type"><?php _e('If Voucher Code already exists', 'rewardsystem'); ?></label>
                    </th>
                    <td class="forminp forminp-select">                
                        <select id ="rs_voucher_code_import_type" class="rs_voucher_code_import_type" name="rs_voucher_code_import_type">
                            <option value="1"><?php _e('Ignore Voucher Code', 'rewardsystem'); ?>  </option>
                            <option value="2"><?php _e('Replace Voucher Code', 'rewardsystem'); ?>  </option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <td class="forminp forminp-select">
                        <input type="submit" id="rs_import_reward_codes_csv_from_old" name="rs_import_reward_codes_csv_from_old" value="Import Voucher Codes as CSV "/>
                    </td>

                </tr>            
            </table>
            <?php
            if (isset($_POST['rs_export_reward_codes_csv'])) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'rsgiftvoucher';
                $each_reward_code = $wpdb->get_results("SELECT * FROM $table_name ", ARRAY_A);
                foreach ($each_reward_code as $code_info) {
                    $voucher_code = $code_info['vouchercode'];
                    $voucher_amount = $code_info['points'];
                    $voucher_created_date = $code_info['vouchercreated'];
                    $voucher_used_count = $code_info['memberused'] != "" ? get_user_by("id", $code_info['memberused'])->user_login : "Notyet";

                    if ($code_info['voucherexpiry'] != '') {
                        $voucher_expired_date = $code_info['voucherexpiry'];
                    } else {
                        $voucher_expired_date = 'Never';
                    }
                    $voucher_info_array[] = array($voucher_code, $voucher_amount, $voucher_created_date, $voucher_expired_date, $voucher_used_count);
                }
                ob_end_clean();
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=list_of_reward_codes" . date("Y-m-d") . ".csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                RSMasterLog::outputCSV($voucher_info_array);
                exit();
            }
            $newwp_list_table_for_users = new WP_List_Table_for_NewGiftVoucher();
            $newwp_list_table_for_users->prepare_items();
            $plugin_url = WP_PLUGIN_URL;
            $newwp_list_table_for_users->search_box('Search', 'search_id');
            $newwp_list_table_for_users->display();
        }

        public static function import_giftvoucher() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rsgiftvoucher';
            $get_gift_voucher = '';
            $get_option = get_option('once_create_gift');
            if ($get_option != '2') {
                $get_gift_voucher = get_option('rsvoucherlists');
                if (is_array($get_gift_voucher)) {
                    if (!empty($get_gift_voucher)) {
                        foreach ($get_gift_voucher as $array) {
                            foreach ($array as $variable1) {
                                if ($variable1['voucherexpiry'] != '') {
                                    $voucherexpiry = $variable1['voucherexpiry'];
                                } else {
                                    $voucherexpiry = '';
                                }
                                if ($variable1['memberused'] != '') {
                                    $username = $variable1['memberused'];
                                } else {
                                    $username = '';
                                }
                                $newupdates[$variable1['vouchercode']] = array(
                                    $variable1['vouchercode'] => array('points' => $variable1['points'], 'vouchercode' => $variable1['vouchercode'], 'vouchercreated' => $variable1['vouchercreated'], 'voucherexpiry' => $voucherexpiry, 'memberused' => $username)
                                );
                            }
                        }
                        $array3 = array_merge((array) get_option('rs_list_of_gift_vouchers_created'), $newupdates);
                        $array3 = array_map("unserialize", array_unique(array_map("serialize", $array3)));
                        update_option('rs_list_of_gift_vouchers_created', array_filter($array3));
                        update_option('once_create_gift', '2');
                    }
                }
            }
            $get_gift_voucher = get_option('rs_list_of_gift_vouchers_created');
            if (is_array($get_gift_voucher)) {
                foreach ($get_gift_voucher as $array) {
                    foreach ($array as $variable1) {
                        if ($variable1['voucherexpiry'] != '') {
                            $voucherexpiry = $variable1['voucherexpiry'];
                        } else {
                            $voucherexpiry = '';
                        }
                        if ($variable1['memberused'] != '') {
                            $username = $variable1['memberused'];
                        } else {
                            $username = '';
                        }
                        $wpdb->insert(
                                $table_name, array(
                            'points' => $variable1['points'],
                            'vouchercode' => $variable1['vouchercode'],
                            'vouchercreated' => $variable1['vouchercreated'],
                            'voucherexpiry' => $voucherexpiry,
                            'memberused' => $username)
                        );
                    }
                }
            }
            delete_option('rs_list_of_gift_vouchers_created');
            if (isset($_POST['rs_import_reward_codes_csv_from_old'])) {
                if (isset($_POST['rs_voucher_code_import_type'])) {
                    if ($_POST['rs_voucher_code_import_type'] == '1') {
                        if ($_FILES["file"]["error"] > 0) {
                            echo "Error: " . $_FILES["file"]["error"] . "<br>";
                        } else {
                            $mimes = array('text/csv',
                                'text/plain',
                                'application/csv',
                                'text/comma-separated-values',
                                'application/excel',
                                'application/vnd.ms-excel',
                                'application/vnd.msexcel',
                                'text/anytext',
                                'application/octet-stream',
                                'application/txt');
                            if (in_array($_FILES['file']['type'], $mimes)) {
                                self::inputCSVforIgnore($_FILES["file"]["tmp_name"]);
                            } else {
                                ?>
                                <style type="text/css">
                                    div.error {
                                        display:block;
                                    }
                                </style>
                                <?php
                            }
                        }
                        $myurl = get_permalink();
                    } else {
                        if ($_POST['rs_voucher_code_import_type'] == '2') {
                            if ($_FILES["file"]["error"] > 0) {
                                echo "Error: " . $_FILES["file"]["error"] . "<br>";
                            } else {
                                $mimes = array('text/csv',
                                    'text/plain',
                                    'application/csv',
                                    'text/comma-separated-values',
                                    'application/excel',
                                    'application/vnd.ms-excel',
                                    'application/vnd.msexcel',
                                    'text/anytext',
                                    'application/octet-stream',
                                    'application/txt');
                                if (in_array($_FILES['file']['type'], $mimes)) {
                                    self::inputCSVforReplace($_FILES["file"]["tmp_name"]);
                                } else {
                                    ?>
                                    <style type="text/css">
                                        div.error {
                                            display:block;
                                        }
                                    </style>
                                    <?php
                                }
                            }
                            $myurl = get_permalink();
                        }
                    }
                }
            }
        }

        public static function inputCSVforReplace($data_path) {
            global $wpdb;
            $row = 1;
            if (($handle = fopen($data_path, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $row++;
                    $datas = strtotime($data[2]);
                    $datass = isset($data[2]) ? $datas : 999999999999;
                    if ($data[3] != 'Never') {
                        if ($data[3] != '') {
                            $datasss = strtotime($data[3]);
                        } else {
                            $datasss = '';
                        }
                    } else {
                        $datasss = '';
                    }
                    $expired = $datasss;
                    $usedby = isset($data[4]) ? $data[4] : '';

                    $collection[] = array($data[0], $data[1], $datass, $expired, $usedby);
                }
                $table_name = $wpdb->prefix . "rsgiftvoucher";
                foreach ($collection as $variable1) {
                    if ($variable1[0] != '') {
                        $create_date = date_i18n('Y-m-d', $variable1[2]);
                        if (isset($variable1[3])) {
                            if ($variable1[3] != '') {
                                $expired_date = date_i18n('Y-m-d', $variable1[3]);
                            } else {
                                $expired_date = '';
                            }
                        } else {
                            $expired_date = '';
                        }
                        if ($variable1[4] != 'Notyet') {
                            if ($variable1[4] != '') {
                                $getusermeta1 = $wpdb->get_results("SELECT `ID` FROM `wp_users` WHERE `user_login`='$variable1[4]' ", ARRAY_A);
                                if (!empty($getusermeta1)) {
                                    foreach ($getusermeta1 as $userid) {
                                        $user = $userid['ID'];
                                    }
                                } else {
                                    $user = '';
                                }
                            } else {
                                $user = '';
                            }
                        } else {
                            $user = '';
                        }
                        $voucher_codes = $wpdb->get_col("SELECT vouchercode FROM $table_name");                                                
                        if (!in_array($variable1[0], $voucher_codes)) {
                            $wpdb->insert(
                                    $table_name, array(
                                'points' => $variable1[1],
                                'vouchercode' => $variable1[0],
                                'vouchercreated' => $create_date,
                                'voucherexpiry' => $expired_date,
                                'memberused' => $user)
                            );
                        }else{
                            $query = $wpdb->get_row("SELECT * FROM $table_name WHERE vouchercode = '$variable1[0]'", ARRAY_A);
                            $id = $query['id'];
                            $wpdb->update($table_name, 
                                    array('points' => $variable1[1],'vouchercreated' => $create_date,'voucherexpiry' => $expired_date,'memberused' => $user),
                                    array('id'=>$id)
                            );
                        }
                    }
                }
            }
        }

        public static function inputCSVforIgnore($data_path) {
            global $wpdb;
            $table_name = $wpdb->prefix . "rsgiftvoucher";
            $row = 1;
            if (($handle = fopen($data_path, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $row++;
                    $datas = strtotime($data[2]);
                    $datass = isset($data[2]) ? $datas : 999999999999;
                    if ($data[3] != 'Never') {
                        if ($data[3] != '') {
                            $datasss = strtotime($data[3]);
                        } else {
                            $datasss = '';
                        }
                    } else {
                        $datasss = '';
                    }
                    $expired = $datasss;
                    $usedby = isset($data[4]) ? $data[4] : '';
                    $collection[] = array($data[0], $data[1], $datass, $expired, $usedby);
                }

                $newupdates = array();
                foreach ($collection as $variable1) {
                    $get_voucher_codes = $wpdb->get_col("SELECT vouchercode FROM $table_name");
                    if (!in_array($variable1[0], $get_voucher_codes)) {
                        $create_date = date_i18n('Y-m-d', $variable1[2]);
                        if (isset($variable1[3])) {
                            if ($variable1[3] != '') {
                                $expired_date = date_i18n('Y-m-d', $variable1[3]);
                            } else {
                                $expired_date = '';
                            }
                        } else {
                            $expired_date = '';
                        }
                        if ($variable1[4] != 'Notyet') {
                            if ($variable1[4] != '') {
                                $getusermeta1 = $wpdb->get_results("SELECT ID FROM wp_users WHERE user_login='$variable1[4]' ", ARRAY_A);
                                if (!empty($getusermeta1)) {
                                    foreach ($getusermeta1 as $userid) {
                                        $user = $userid['ID'];
                                    }
                                } else {
                                    $user = '';
                                }
                            } else {
                                $user = '';
                            }
                        } else {
                            $user = '';
                        }
                        $wpdb->insert(
                                $table_name, array(
                            'points' => $variable1[1],
                            'vouchercode' => $variable1[0],
                            'vouchercreated' => $create_date,
                            'voucherexpiry' => $expired_date,
                            'memberused' => $user)
                        );
                    }
                }
                fclose($handle);
            }
        }

        public static function rs_function_to_reset_gift_voucher_tab() {
            $settings = RSOfflineOnlineRewards::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

    }

    RSOfflineOnlineRewards::init();
}