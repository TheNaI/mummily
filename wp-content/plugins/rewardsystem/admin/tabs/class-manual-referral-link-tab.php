<?php
/*
 * Manual Referral Link Tab
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('RSManualReferralLink')) {

    class RSManualReferralLink {

        public static function init() {

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_manual', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_manual', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action('woocommerce_admin_field_rs_user_role_dynamics_manual', array(__CLASS__, 'reward_system_add_manual_table_to_action'));

            add_action('fp_action_to_reset_settings_rewardsystem_manual', array(__CLASS__, 'rs_function_to_reset_manual_referral_link_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_manual'] = __('Manual Referral Link', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;

            return apply_filters('woocommerce_rewardsystem_manual_settings', array(
                array(
                    'name' => __('Manual Referral Link Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_manual_setting'
                ),
                array(
                    'type' => 'rs_user_role_dynamics_manual',
                ),
                array('type' => 'sectionend', 'id' => '_rs_manual_setting'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSManualReferralLink::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSManualReferralLink::reward_system_admin_fields());
            $rewards_dynamic_rulerule_manual = array_values($_POST['rewards_dynamic_rule_manual']);
            update_option('rewards_dynamic_rule_manual', $rewards_dynamic_rulerule_manual);
            return false;
        }

        public static function reward_system_add_manual_table_to_action() {
            global $woocommerce;
            wp_nonce_field(plugin_basename(__FILE__), 'rsdynamicrulecreation_manual');
            global $woocommerce;
            ?>
            <style type="text/css">
                .rs_manual_linking_referral{
                    width:60%;
                }
                .rs_manual_linking_referer{
                    width:60%;
                }
                .chosen-container-single {
                    position:absolute;
                }
                .column-columnname-link{
                    width:10%;               
                }            

            </style>
            <?php
            echo rs_common_ajax_function_to_select_user('rs_manual_linking_referer');
            echo rs_common_ajax_function_to_select_user('rs_manual_linking_referral');
            ?>
            <table class="widefat fixed rsdynamicrulecreation_manual" cellspacing="0">
                <thead>
                    <tr>

                        <th class="manage-column column-columnname" scope="col"><?php _e('Referrer Username', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e('Buyer Username', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname-link" scope="col"><?php _e('Linking Type', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e('Remove Linking', 'rewardsystem'); ?></th>
                    </tr>
                </thead>

                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="manage-column column-columnname num" scope="col"> <span class="add button-primary"><?php _e('Add Linking', 'rewardsystem'); ?></span></td>
                    </tr>
                    <tr>

                        <th class="manage-column column-columnname" scope="col"><?php _e('Referrer Username', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e('Buyer Username', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname-link" scope="col"><?php _e('Linking Type', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e('Add Linking', 'rewardsystem'); ?></th>

                    </tr>
                </tfoot>

                <tbody id="here">
                    <?php
                    $rewards_dynamic_rulerule_manual = get_option('rewards_dynamic_rule_manual');
                    $i = 0;
                    if (is_array($rewards_dynamic_rulerule_manual)) {
                        foreach ($rewards_dynamic_rulerule_manual as $rewards_dynamic_rule) {
                            if ($rewards_dynamic_rule['referer'] != '' && $rewards_dynamic_rule['refferal'] != '') {
                                ?>
                                <tr>
                                    <td class="column-columnname">
                                        <p class="form-field">
                                            <?php if ((float) $woocommerce->version <= (float) ('2.2.0')) { ?>
                                            <select name="rewards_dynamic_rule_manual[<?php echo $i; ?>][referer]" class="short rs_manual_linking_referer">
                                                    <?php
                                                    $user = get_user_by('id', absint($rewards_dynamic_rule['referer']));
                                                    echo '<option value="' . absint($user->ID) . '" ';
                                                    selected(1, 1);
                                                    echo '>' . esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email) . ')</option>';
                                                    ?>
                                                </select>
                                                <?php
                                            } else {
                                                $user_id = absint($rewards_dynamic_rule['referer']);
                                                $user = get_user_by('id', $user_id);
                                                $user_string = esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email) . ')';
                                                if ((float) $woocommerce->version >= (float) ('3.0.0')) {
                                                    ?>
                                                    <select multiple="multiple"  class="wc-customer-search" name="rewards_dynamic_rule_manual[<?php echo $i; ?>][referer]" data-placeholder="<?php _e('Search Users', 'rewardsystem'); ?>" >
                                                        <option value="<?php echo $user_id; ?>" selected="selected"><?php echo esc_attr($user_string); ?><option>
                                                    </select>
                                                <?php } else {
                                                    ?>
                                                    <input type="hidden" class="wc-customer-search" name="rewards_dynamic_rule_manual[<?php echo $i; ?>][referer]" data-placeholder="<?php _e('Search for a customer&hellip;', 'rewardsystem'); ?>" data-selected="<?php echo esc_attr($user_string); ?>" value="<?php echo $user_id; ?>" data-allow_clear="true" />
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </p>
                                    </td>
                                    <td class="column-columnname">
                                        <p class="form-field">
                                            <?php if ((float) $woocommerce->version <= (float) ('2.2.0')) { ?>
                                                <select name="rewards_dynamic_rule_manual[<?php echo $i; ?>][refferal]" class="short rs_manual_linking_referral">
                                                    <?php
                                                    $user = get_user_by('id', absint($rewards_dynamic_rule['refferal']));
                                                    echo '<option value="' . absint($user->ID) . '" ';
                                                    selected(1, 1);
                                                    echo '>' . esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email) . ')</option>';
                                                    ?>
                                                </select>
                                            <?php } else { ?>
                                                <?php
                                                $user_id = absint($rewards_dynamic_rule['refferal']);
                                                $user = get_user_by('id', $user_id);
                                                $user_string = esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email) . ')';
                                                if ((float) $woocommerce->version >= (float) ('3.0.0')) {
                                                    ?>
                                                    <select multiple="multiple"  class="wc-customer-search" name="rewards_dynamic_rule_manual[<?php echo $i; ?>][refferal]" data-placeholder="<?php _e('Search Users', 'rewardsystem'); ?>" >
                                                        <option value="<?php echo $user_id; ?>" selected="selected"><?php echo esc_attr($user_string); ?><option>
                                                    </select>
                                                <?php } else { ?>
                                                    <input type="hidden" class="wc-customer-search" name="rewards_dynamic_rule_manual[<?php echo $i; ?>][refferal]" data-placeholder="<?php _e('Search for a customer&hellip;', 'rewardsystem'); ?>" data-selected="<?php echo esc_attr($user_string); ?>" value="<?php echo $user_id; ?>" data-allow_clear="true" />
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </p>
                                    </td>
                                    <td class="column-columnname-link">    <?php
                                        if (@$rewards_dynamic_rule['type'] != '') {
                                            ?>
                                            <span> <b>Automatic</b></span>
                                            <?php
                                        } else {
                                            ?>
                                            <span> <b>Manual</b></span>
                                            <?php
                                        }
                                        ?>
                                        <input type="hidden" value="<?php echo @$rewards_dynamic_rule['type']; ?>" name="rewards_dynamic_rule_manual[<?php echo $i; ?>][type]"/>
                                    </td>
                                    <td class="column-columnname num">
                                        <span class="remove button-secondary"><?php _e('Remove Linking', 'rewardsystem'); ?></span>
                                    </td>
                                </tr>
                                <?php
                                $i = $i + 1;
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            <script>
                jQuery(document).ready(function () {
                    jQuery('#afterclick').hide();
                    var countrewards_dynamic_rule = <?php echo $i; ?>;
                    jQuery(".add").click(function () {
                        jQuery('#afterclick').show();
                        countrewards_dynamic_rule = countrewards_dynamic_rule + 1;
            <?php if ((float) $woocommerce->version <= (float) ('2.2.0')) { ?>

                            jQuery('#here').append('<tr><td><p class="form-field"><select name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][referer]" class="short rs_manual_linking_referer"><option value=""></option></select></p></td>\n\
                                                                                                            \n\<td><p class="form-field"><select name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][refferal]" class="short rs_manual_linking_referral"><option value=""></option></select></p></td>\n\
                                                                                                            \n\<td class="column-columnname-link" ><span><input type="hidden" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][type]"  value="" class="short "/><b>Manual</b></span></td>\n\
                                                                                                        \n\
                                                                                                        <td class="num"><span class="remove button-secondary">Remove Linking</span></td></tr><hr>');
                            jQuery(function () {
                                // Ajax Chosen Product Selectors
                                jQuery("select.rs_manual_linking_referer").ajaxChosen({
                                    method: 'GET',
                                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                    dataType: 'json',
                                    afterTypeDelay: 100,
                                    data: {
                                        action: 'woocommerce_json_search_customers',
                                        security: '<?php echo wp_create_nonce("search-customers"); ?>'
                                    }
                                }, function (data) {
                                    var terms = {};

                                    jQuery.each(data, function (i, val) {
                                        terms[i] = val;
                                    });
                                    return terms;
                                });
                            });
                            jQuery(function () {
                                // Ajax Chosen Product Selectors
                                jQuery("select.rs_manual_linking_referral").ajaxChosen({
                                    method: 'GET',
                                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                    dataType: 'json',
                                    afterTypeDelay: 100,
                                    data: {
                                        action: 'woocommerce_json_search_customers',
                                        security: '<?php echo wp_create_nonce("search-customers"); ?>'
                                    }
                                }, function (data) {
                                    var terms = {};

                                    jQuery.each(data, function (i, val) {
                                        terms[i] = val;
                                    });
                                    return terms;
                                });
                            });
            <?php
            } else {
                if ((float) $woocommerce->version >= (float) ('3.0.0')) { ?>
                            jQuery('#here').append('<tr><td><p class="form-field"><select class="wc-customer-search" style="width:300px;" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][referer]" data-placeholder="<?php _e("Search for a customer&hellip;", "rewardsystem"); ?>" data-allow_clear="true"><option value=""></option></select> </p></td>\n\
                                                                                                                    \n\<td><p class="form-field"><select class="wc-customer-search" style="width:300px;" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][refferal]" data-placeholder="<?php _e("Search for a customer&hellip;", "rewardsystem"); ?>" data-allow_clear="true"><option value=""></option></select></p></td>\n\
                                                                                                                  \n\<td class="column-columnname-link" ><span><input type="hidden" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][type]"  value="" class="short "/><b>Manual</b></span></td>\n\
                                                                                                                \n\
                                                                                                                <td class="num"><span class="remove button-secondary">Remove Linking</span></td></tr><hr>');
                            jQuery('body').trigger('wc-enhanced-select-init');
                <?php } else { ?>
                                jQuery('#here').append('<tr><td><p class="form-field"><input type="hidden" class="wc-customer-search" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][referer]" data-placeholder="<?php _e("Search for a customer&hellip;", "rewardsystem"); ?>" data-selected="" value="" data-allow_clear="true"/> </p></td>\n\
                                                                                                                    \n\<td><p class="form-field"><input type="hidden" class="wc-customer-search" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][refferal]" data-placeholder="<?php _e("Search for a customer&hellip;", "rewardsystem"); ?>" data-selected="" value="" data-allow_clear="true"/></p></td>\n\
                                                                                                                  \n\<td class="column-columnname-link" ><span><input type="hidden" name="rewards_dynamic_rule_manual[' + countrewards_dynamic_rule + '][type]"  value="" class="short "/><b>Manual</b></span></td>\n\
                                                                                                                \n\
                                                                                                                <td class="num"><span class="remove button-secondary">Remove Linking</span></td></tr><hr>');
                                jQuery('body').trigger('wc-enhanced-select-init');
                <?php }
            }
            ?>
                        return false;
                    });
                    jQuery(document).on('click', '.remove', function () {
                        jQuery(this).parent().parent().remove();
                    });
                });</script>

            <?php
        }
        
        public static function rs_function_to_reset_manual_referral_link_tab() {
            delete_option('rewards_dynamic_rule_manual');
        }

    }

    RSManualReferralLink::init();
}