<?php
/*
 * Member Level Tab Setting
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSMemberLevel')) {

    class RSMemberLevel {

        public static function init() {

            add_action('admin_init', array(__CLASS__, 'reward_system_default_settings'), 999);

            add_filter('woocommerce_rs_settings_tabs_array', array(__CLASS__, 'reward_system_tab_setting')); // Register a New Tab in a WooCommerce Reward System Settings        

            add_action('woocommerce_rs_settings_tabs_rewardsystem_member_level', array(__CLASS__, 'reward_system_register_admin_settings')); // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action('woocommerce_update_options_rewardsystem_member_level', array(__CLASS__, 'reward_system_update_settings')); // call the woocommerce_update_options_{slugname} to update the reward system                               

            if (class_exists('SUMOMemberships')) {
                add_filter('woocommerce_rewardsystem_member_level_settings', array(__CLASS__, 'add_field_for_membership'));
            }

            add_action('woocommerce_admin_field_rs_user_role_dynamics', array(__CLASS__, 'reward_system_add_table_to_action'));

            add_filter("woocommerce_rewardsystem_member_level_settings", array(__CLASS__, 'reward_system_add_settings_to_action'));

            add_action('fp_action_to_reset_settings_rewardsystem_member_level', array(__CLASS__, 'rs_function_to_reset_memberlevel_tab'));
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function reward_system_tab_setting($setting_tabs) {
            if (!is_array($setting_tabs))
                $setting_tabs = (array) $setting_tabs;
            $setting_tabs['rewardsystem_member_level'] = __('Member Level', 'rewardsystem');
            return $setting_tabs;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce;
            return apply_filters('woocommerce_rewardsystem_member_level_settings', array(
                array(
                    'name' => __('Member Level Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_member_level_setting',
                ),
                array(
                    'name' => __('Priority Level Selection', 'rewardsystem'),
                    'desc' => __('If more than one type(level) is enabled then use the highest/lowest percentage', 'rewardsystem'),
                    'id' => 'rs_choose_priority_level_selection',
                    'class' => 'rs_choose_priority_level_selection',
                    'std' => '1',
                    'default' => '1',
                    'type' => 'radio',
                    'newids' => 'rs_choose_priority_level_selection',
                    'options' => array(
                        '1' => __('Use the level that gives highest percentage', 'rewardsystem'),
                        '2' => __('Use the level that gives lowest percentage', 'rewardsystem'),
                    ),
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => 'rs_member_level_setting', 'class' => 'rs_member_level_setting'),
                array(
                    'name' => __('Reward Points Earning Percentage based on User Role', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_user_role_reward_points',
                ),
                array(
                    'name' => __('User Role based Earning Level', 'rewardsystem'),
                    'desc' => __('Enable this option to modify earning points based on user role', 'rewardsystem'),
                    'id' => 'rs_enable_user_role_based_reward_points',
                    'css' => 'min-width:150px;',
                    'std' => 'yes',
                    'default' => 'yes',
                    'type' => 'checkbox',
                    'newids' => 'rs_enable_user_role_based_reward_points',
                ),
                array('type' => 'sectionend', 'id' => '_rs_user_role_reward_points'),
                array(
                    'name' => __('Reward Points Earning Percentage based on Earned Points', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_member_level_earning_points',
                ),
                array(
                    'name' => __('Earned Points based Earning Level', 'rewardsystem'),
                    'desc' => __('Enable this option to modify earning points based on earned points', 'rewardsystem'),
                    'id' => 'rs_enable_earned_level_based_reward_points',
                    'css' => 'min-width:150px;',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rs_enable_earned_level_based_reward_points',
                ),
                array(
                    'name' => __('Earned Points is decided', 'rewardsystem'),
                    'id' => 'rs_select_earn_points_based_on',
                    'css' => 'min-width:150px;',
                    'std' => '1',
                    'type' => 'select',
                    'newids' => 'rs_select_earn_points_based_on',
                    'options' => array(
                        '1' => __('Based on Total Earned Points', 'rewardsystem'),
                        '2' => __('Based on Current Points', 'rewardsystem')),
                ),
                array(
                    'type' => 'rs_user_role_dynamics',
                ),
                array('type' => 'sectionend', 'id' => '_rs_member_level_earning_points'),
                array(
                    'name' => __('Member Level Message Settings', 'rewardsystem'),
                    'type' => 'title',
                    'id' => '_rs_member_level_message_settings',
                ),
                array(
                    'name' => __('Message displayed for Free Products in Cart', 'rewardsystem'),
                    'desc' => __('Enter the Message which will be displayed for the Free Products in Cart', 'rewardsystem'),
                    'id' => 'rs_free_product_message_info',
                    'css' => 'min-width:550px;',
                    'std' => 'You have got this product for reaching [current_level_points] Reward Points',
                    'default' => 'You have got this product for reaching [current_level_points] Reward Points',
                    'type' => 'textarea',
                    'newids' => 'rs_free_product_message_info',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Free Product Label in Cart', 'rewardsystem'),
                    'desc' => __('Enter the Caption which will be displayed when after Free Product is removed from cart', 'rewardsystem'),
                    'id' => 'rs_free_product_msg_caption',
                    'css' => 'min-width:550px;',
                    'std' => 'Free Product',
                    'default' => 'Free Product',
                    'type' => 'textarea',
                    'newids' => 'rs_free_product_msg_caption',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Display Free Product Message in Cart and Order Details Page', 'rewardsystem'),
                    'desc' => __('Enable this option to display free product message in cart/order', 'rewardsystem'),
                    'id' => 'rs_remove_msg_from_cart_order',
                    'css' => 'min-width:150px;',
                    'std' => 'yes',
                    'default' => 'yes',
                    'type' => 'checkbox',
                    'newids' => 'rs_remove_msg_from_cart_order',
                ),
                array(
                    'name' => __('Label for Balance Points to reach next Member Level', 'rewardsystem'),
                    'desc' => __('Enable Display Balance Points to reach next level in member level', 'rewardsystem'),
                    'id' => 'rs_point_to_reach_next_level',
                    'css' => 'min-width:550px;',
                    'std' => '[balancepoint] more Points to reach [next_level_name] Earning Level ',
                    'default' => '[balancepoint] more Points to reach [next_level_name] Earning Level',
                    'type' => 'textarea',
                    'newids' => 'rs_point_to_reach_next_level',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => '_rs_member_level_message_settings'),
                array(
                    'name' => __('Shortcodes used in Member Level', 'rewardsystem'),
                    'type' => 'title',
                    'id' => 'rs_shortcode_in_member_level',
                ),
                array(
                    'type' => 'title',
                    'desc' => '<b>[current_level_points]</b> - To display current level points<br><br>'
                    . '<b>[balancepoint]</b> - To display balance points to reach next earning level<br><br>'
                    . '<b>[next_level_name]</b> - To display next earning level name',
                ),
                array('type' => 'sectionend', 'id' => 'rs_shortcode_in_member_level'),
            ));
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {

            woocommerce_admin_fields(RSMemberLevel::reward_system_admin_fields());
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options(RSMemberLevel::reward_system_admin_fields());
            update_option('rewards_dynamic_rule', $_POST['rewards_dynamic_rule']);
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function reward_system_default_settings() {
            global $woocommerce;
            foreach (RSMemberLevel::reward_system_admin_fields() as $setting)
                if (isset($setting['newids']) && isset($setting['std'])) {
                    add_option($setting['newids'], $setting['std']);
                }
        }

        public static function add_field_for_membership($settings) {
            $updated_settings = array();
            $membership_level = sumo_get_membership_levels();
            foreach ($settings as $section) {
                $updated_settings[] = $section;
                if (isset($section['id']) && '_rs_user_role_reward_points' == $section['id'] &&
                        isset($section['type']) && 'sectionend' == $section['type']) {
                    $updated_settings[] = array(
                        'name' => __('Reward Points Earning Percentage based on Membership Plan', 'rewardsystem'),
                        'type' => 'title',
                        'id' => '_rs_membership_plan_reward_points',
                    );
                    $updated_settings[] = array(
                        'name' => __('Membership Plan based Earning Level', 'rewardsystem'),
                        'desc' => __('Enable this option to modify earning points based on membership plan', 'rewardsystem'),
                        'id' => 'rs_enable_membership_plan_based_reward_points',
                        'css' => 'min-width:150px;',
                        'std' => 'yes',
                        'default' => 'yes',
                        'type' => 'checkbox',
                        'newids' => 'rs_enable_membership_plan_based_reward_points',
                    );
                    foreach ($membership_level as $key => $value) {
                        $updated_settings[] = array(
                            'name' => __('Reward Points Earning Percentage for ' . $value, 'rewardsystem'),
                            'desc' => __('Please Enter Percentage of Reward Points for ' . $value, 'rewardsystem'),
                            'class' => 'rewardpoints_membership_plan',
                            'id' => 'rs_reward_membership_plan_' . $key,
                            'css' => 'min-width:150px;',
                            'std' => '100',
                            'type' => 'text',
                            'newids' => 'rs_reward_membership_plan_' . $key,
                            'desc_tip' => true,
                        );
                    }
                    $updated_settings[] = array(
                        'type' => 'sectionend',
                        'id' => '_rs_membership_plan_reward_points'
                    );
                }
            }
            return $updated_settings;
        }

        /*
         * Function to add table for Earning Level in Member Level Tab
         */

        public static function reward_system_add_table_to_action() {
            global $woocommerce;
            wp_nonce_field(plugin_basename(__FILE__), 'rsdynamicrulecreation');
            ?>
            <style type="text/css">
                .rs_add_free_product_user_levels{
                    width:100%;
                }
                .chosen-container-active{
                    position: absolute;
                }
            </style>
            <?php
            echo rs_common_ajax_function_to_select_products('rs_add_free_product_user_levels');
            ?>
            <table class="widefat fixed rsdynamicrulecreation" cellspacing="0">
                <thead>
                    <tr>
                        <th class="manage-column column-columnname" scope="col"><?php _e('Level Name', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e('Reward Points', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e('Reward Points Earning Percentage', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e('Free Product(s)', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e('Remove Level', 'rewardsystem'); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="manage-column column-columnname num" scope="col"> <span class="add button-primary"><?php _e('Add New Level', 'rewardsystem'); ?></span></td>
                    </tr>
                    <tr>
                        <th class="manage-column column-columnname" scope="col"><?php _e('Level Name', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e('Reward Points', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e('Reward Points Earning Percentage', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e('Free Product(s)', 'rewardsystem'); ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e('Remove Level', 'rewardsystem'); ?></th>

                    </tr>
                </tfoot>
                <tbody id="here">
                    <?php
                    $rewards_dynamic_rulerule = get_option('rewards_dynamic_rule');
                    if (!empty($rewards_dynamic_rulerule)) {
                        if (is_array($rewards_dynamic_rulerule)) {
                            foreach ($rewards_dynamic_rulerule as $i => $rewards_dynamic_rule) {
                                ?>
                                <tr>
                                    <td class="column-columnname">
                                        <p class="form-field">
                                            <input type="text" name="rewards_dynamic_rule[<?php echo $i; ?>][name]" class="short" value="<?php echo $rewards_dynamic_rule['name']; ?>"/>
                                        </p>
                                    </td>
                                    <td class="column-columnname">
                                        <p class="form-field">
                                            <input type="number" step="any" min="0" name="rewards_dynamic_rule[<?php echo $i; ?>][rewardpoints]" id="rewards_dynamic_rewardpoints<?php echo $i; ?>" class="short" value="<?php echo $rewards_dynamic_rule['rewardpoints']; ?>"/>
                                        </p>
                                    </td>
                                    <td class="column-columnname">
                                        <p class="form-field">
                                            <input type ="number" name="rewards_dynamic_rule[<?php echo $i; ?>][percentage]" id="rewards_dynamic_rule_percentage<?php echo $i; ?>" class="short test" value="<?php echo $rewards_dynamic_rule['percentage']; ?>"/>
                                        </p>
                                    </td>
                                    <td class="column-columnname">
                                        <p class="form-field">
                                            <?php
                                            if ((float) $woocommerce->version > (float) ('2.2.0')) {
                                                if ($woocommerce->version >= (float) ('3.0.0')) {
                                                    ?>                                                    
                                                    <select class="wc-product-search" multiple="multiple" style="width: 100%;" id="rewards_dynamic_rule[<?php echo $i; ?>]['product_list'][]" name="rewards_dynamic_rule[<?php echo $i; ?>][product_list][]" data-placeholder="<?php _e('Search for a product&hellip;', 'woocommerce'); ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true">
                                                        <?php
                                                        $json_ids = array();
                                                        if (isset($rewards_dynamic_rule['product_list']) && $rewards_dynamic_rule['product_list'] != "") {
                                                            $list_of_produts = $rewards_dynamic_rule['product_list'];
                                                            foreach ($list_of_produts as $product_id) {
                                                                $product = rs_get_product_object($product_id);
                                                                if (is_object($product)) {
                                                                    $json_ids = wp_kses_post($product->get_formatted_name());
                                                                    ?> <option value="<?php echo $product_id; ?>" selected="selected"><?php echo esc_html($json_ids); ?></option><?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <!-- For Latest -->
                                                    <input type="hidden" class="wc-product-search" style="width: 100%;" id="rewards_dynamic_rule[<?php echo $i; ?>]['product_list'][]" name="rewards_dynamic_rule[<?php echo $i; ?>][product_list][]" data-placeholder="<?php _e('Search for a product&hellip;', 'woocommerce'); ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" data-selected="<?php
                                                    $json_ids = array();
                                                    if ($rewards_dynamic_rule['product_list'] != "") {
                                                        $list_of_produts = $rewards_dynamic_rule['product_list']['0'];
                                                        $product_ids = array_filter(array_map('absint', (array) explode(',', $list_of_produts)));
                                                        foreach ($product_ids as $product_id) {
                                                            $product = rs_get_product_object($product_id);
                                                            if (is_object($product)) {
                                                                $json_ids[$product_id] = wp_kses_post($product->get_formatted_name());
                                                            }
                                                        } echo esc_attr(json_encode($json_ids));
                                                    }
                                                    ?>" value="<?php echo implode(',', array_keys($json_ids)); ?>" /><?php
                                                       }
                                                   } else {
                                                       ?>
                                                <!-- For Old Version -->
                                                <select multiple name="rewards_dynamic_rule[<?php echo $i; ?>][product_list][]" class="rs_add_free_product_user_levels">
                                                    <?php
                                                    if ($rewards_dynamic_rule['product_list'] != "") {
                                                        $list_of_produts = $rewards_dynamic_rule['product_list'];
                                                        foreach ($list_of_produts as $rs_free_id) {
                                                            echo '<option value="' . $rs_free_id . '" ';
                                                            selected(1, 1);
                                                            echo '>' . ' #' . $rs_free_id . ' &ndash; ' . get_the_title($rs_free_id);
                                                            ?>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <option value=""></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                <?php
                                            }
                                            ?>
                                        </p>
                                    </td>
                                    <td class="column-columnname num">
                                        <span class="remove button-secondary"><?php _e('Remove Level', 'rewardsystem'); ?></span>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(".add").on('click', function () {
                        jQuery('#afterclick').show();
                        var countrewards_dynamic_rule = Math.round(new Date().getTime() + (Math.random() * 100));
            <?php
            if ((float) $woocommerce->version > (float) ('2.2.0')) {
                if ($woocommerce->version >= (float) ('3.0.0')) {
                    ?>
                                jQuery('#here').append('<tr><td><p class="form-field"><input type="text" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][name]" class="short" value=""/></p></td>\n\
                                                                                                                                                                                                   \n\<td><p class="form-field"><input type="number" step="any" min="0" id="rewards_dynamic_ruleamount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][rewardpoints]" class="short" value=""/></p></td>\n\
                                                                                                                                                                                          \n\\n\
                                                                                                                                                                                        <td><p class="form-field"><input type ="number" id="rewards_dynamic_rule_claimcount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][percentage]" class="short test"  value=""/></p></td>\n\\n\
                                                                                                                                                                                        \n\<td><p class="form-field">\n\
                                                                                                                                                                                        \n\
                                                                                                                                                                                        <select style="width:100%;" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][product_list][]" class="wc-product-search" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="true"></select></p></td>n\
                                                                                                                                                                                        <td class="num"><span class="remove button-secondary">Remove Rule</span></td></tr><hr>');
                                jQuery('body').trigger('wc-enhanced-select-init');
                <?php } else {
                    ?>
                                jQuery('#here').append('<tr><td><p class="form-field"><input type="text" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][name]" class="short" value=""/></p></td>\n\
                                                                                                                                                                                                           \n\<td><p class="form-field"><input type="number" step="any" min="0" id="rewards_dynamic_ruleamount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][rewardpoints]" class="short" value=""/></p></td>\n\
                                                                                                                                                                                                  \n\\n\
                                                                                                                                                                                                <td><p class="form-field"><input type ="number" id="rewards_dynamic_rule_claimcount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][percentage]" class="short test"  value=""/></p></td>\n\\n\
                                                                                                                                                                                                \n\<td><p class="form-field">\n\
                                                                                                                                                                                                \n\
                                                                                                                                                                                                <input type=hidden style="width:100%;" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][product_list][]" class="wc-product-search" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="true"/></p></td>n\
                                                                                                                                                                                                <td class="num"><span class="remove button-secondary">Remove Rule</span></td></tr><hr>');
                                jQuery('body').trigger('wc-enhanced-select-init');
                <?php } ?>
            <?php } else { ?>
                            jQuery('#here').append('<tr><td><p class="form-field"><input type="text" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][name]" class="short" value=""/></p></td>\n\
                                                                                                                                                    \n\<td><p class="form-field"><input type="number" step="any" min="0" id="rewards_dynamic_ruleamount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][rewardpoints]" class="short" value=""/></p></td>\n\
                                                                                                                                                    \n\\n\
                                                                                                                                                    <td><p class="form-field"><input type ="number" id="rewards_dynamic_rule_claimcount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][percentage]" class="short test"  value=""/></p></td>\n\\n\
                                                                                                                                                    \n\<td><p class="form-field">\n\
                                                                                                                                                    \n\
                                                                                                                                                    <select multiple name="rewards_dynamic_rule[' + countrewards_dynamic_rule + '][product_list][]" class="rs_add_free_product_user_levels"><option value=""></option></select></p></td>n\
                                                                                                                                                    <td class="num"><span class="remove button-secondary">Remove Rule</span></td></tr><hr>');

            <?php } if ((float) $woocommerce->version <= (float) ('2.2.0')) { ?>
                            jQuery(function () {
                                jQuery("select.rs_add_free_product_user_levels").ajaxChosen({
                                    method: 'GET',
                                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                    dataType: 'json',
                                    afterTypeDelay: 100,
                                    data: {
                                        action: 'woocommerce_json_search_products_and_variations',
                                        security: '<?php echo wp_create_nonce("search-products"); ?>'
                                    }
                                }, function (data) {
                                    var terms = {};

                                    jQuery.each(data, function (i, val) {
                                        terms[i] = val;
                                    });
                                    return terms;
                                });
                            });
            <?php } ?>
                        return false;
                    });
                    jQuery(document).on('click', '.remove', function () {
                        jQuery(this).parent().parent().remove();
                    });
                    jQuery('#rs_enable_user_role_based_reward_points').addClass('rs_enable_user_role_based_reward_points');
                    jQuery('#rs_enable_earned_level_based_reward_points').addClass('rs_enable_user_role_based_reward_points');
                });
            </script>
            <?php
        }

        /*
         * Function to add settings for Member Level in Member Level Tab
         */

        public static function reward_system_add_settings_to_action($settings) {
            global $wp_roles;
            $updated_settings = array();
            $mainvariable = array();
            global $woocommerce;
            foreach ($settings as $section) {
                if (isset($section['id']) && '_rs_user_role_reward_points' == $section['id'] &&
                        isset($section['type']) && 'sectionend' == $section['type']) {
                    foreach ($wp_roles->role_names as $value => $key) {
                        $updated_settings[] = array(
                            'name' => __('Reward Points Earning Percentage for ' . $value . ' User Role', 'rewardsystem'),
                            'desc' => __('Please Enter Percentage of Reward Points for ' . $value, 'rewardsystem'),
                            'class' => 'rewardpoints_userrole',
                            'id' => 'rs_reward_user_role_' . $value,
                            'css' => 'min-width:150px;',
                            'std' => '100',
                            'type' => 'text',
                            'newids' => 'rs_reward_user_role_' . $value,
                            'desc_tip' => true,
                        );
                    }

                    $updated_settings[] = array(
                        'type' => 'sectionend', 'id' => '_rs_user_role_reward_points',
                    );
                }

                $updated_settings[] = $section;
            }

            return $updated_settings;
        }

        public static function rs_function_to_reset_memberlevel_tab() {
            $settings = RSMemberLevel::reward_system_admin_fields();
            RSTabManagement::rs_function_to_reset_setting($settings);
        }

    }

    RSMemberLevel::init();
}