<?php
/*
 * Simple Product Settings
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSVariableProduct')) {

    class RSVariableProduct {

        public static function init() {

            add_action('woocommerce_product_after_variable_attributes_js', array(__CLASS__, 'rs_admin_option_for_variable_product_in_js'));

            add_action('woocommerce_product_after_variable_attributes', array(__CLASS__, 'rs_admin_option_for_variable_product'), 10, 3);

            add_action('woocommerce_process_product_meta_variable-subscription', array(__CLASS__, 'save_variable_product_fields_for_subscription'), 10, 1);

            add_action('woocommerce_save_product_variation', array(__CLASS__, 'save_variable_product_fields'), 10, 2);

            add_action('woocommerce_product_after_variable_attributes', array(__CLASS__, 'rs_validation_for_input_field_in_variable_product'), 10, 3);
        }

        public static function rs_admin_option_for_variable_product_in_js() {
            if (is_admin()) {
                ?>
                <table>
                    <tr>
                        <td>
                            <?php
                            // Select
                            woocommerce_wp_select(
                                    array(
                                        'id' => '_enable_reward_points_price[ + loop + ]',
                                        'label' => __('Enable SUMO Reward Points Price', 'rewardsystem'),
                                        'desc_tip' => 'true',
                                        'description' => __('Choose an Option.', 'rewardsystem'),
                                        'value' => $variation_data['_enable_reward_points_price'][0],
                                        'options' => array(
                                            '1' => __('Enable', 'rewardsystem'),
                                            '2' => __('Disable', 'rewardsystem'),
                                        )
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            // Select
                            woocommerce_wp_select(
                                    array(
                                        'id' => '_enable_reward_points_pricing_type[ + loop + ]',
                                        'label' => __(' Precing Display Type', 'rewardsystem'),
                                        'desc_tip' => 'true',
                                        'description' => __('Choose an Option.', 'rewardsystem'),
                                        'value' => $variation_data['_enable_reward_points_pricing_type'][0],
                                        'options' => array(
                                            '1' => __('Currency & Points', 'rewardsystem'),
                                            '2' => __('Only Points', 'rewardsystem'),
                                        )
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            // Select
                            woocommerce_wp_select(
                                    array(
                                        'id' => '_enable_reward_points_price_type[ + loop + ]',
                                        'label' => __(' Points Price Type', 'rewardsystem'),
                                        'desc_tip' => 'true',
                                        'description' => __('Choose an Option.', 'rewardsystem'),
                                        'value' => $variation_data['_enable_reward_points_price_type'][0],
                                        'options' => array(
                                            '1' => __('BY Fixed', 'rewardsystem'),
                                            '2' => __('Based on Conversion', 'rewardsystem'),
                                        )
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            // Text Field
                            woocommerce_wp_text_input(
                                    array(
                                        'id' => 'price_points[ + loop + ]',
                                        'label' => __('By Fixed Points Price:', 'rewardsystem'),
                                        'placeholder' => '',
                                        'size' => '5',
                                        'desc_tip' => 'true',
                                        'description' => __('By Fixed Point Price', 'rewardsystem'),
                                        'value' => ''
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            // Text Field
                            woocommerce_wp_text_input(
                                    array(
                                        'id' => '_price_points_based_on_conversion[ + loop + ]',
                                        'label' => __(' Points Price Based on Conversion:', 'rewardsystem'),
                                        'placeholder' => '',
                                        'size' => '5',
                                        'class' => 'fp_point_price',
                                        'desc_tip' => 'true',
                                        'description' => __('Point Price Based on Conversion', 'rewardsystem'),
                                        'value' => ''
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            // Select
                            woocommerce_wp_select(
                                    array(
                                        'id' => '_enable_reward_points[ + loop + ]',
                                        'label' => __('Enable SUMO Reward Points', 'rewardsystem'),
                                        'desc_tip' => 'true',
                                        'description' => __('Choose an Option.', 'rewardsystem'),
                                        'value' => $variation_data['_enable_reward_points'][0],
                                        'options' => array(
                                            '1' => __('Enable', 'rewardsystem'),
                                            '2' => __('Disable', 'rewardsystem'),
                                        )
                                    )
                            );
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <?php
                            // Select
                            woocommerce_wp_select(
                                    array(
                                        'id' => '_select_reward_rule[ + loop + ]',
                                        'label' => __('Reward Type', 'rewardsystem'),
                                        'class' => '_select_reward_rule',
                                        'description' => __('Select Reward Rule', 'rewardsystem'),
                                        'value' => '',
                                        'options' => array(
                                            '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                            '2' => __('By Percentage of Product Price', 'rewardsystem'),
                                        )
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            // Text Field
                            woocommerce_wp_text_input(
                                    array(
                                        'id' => '_reward_points[ + loop + ]',
                                        'label' => __('Reward Points', 'rewardsystem'),
                                        'placeholder' => '',
                                        'desc_tip' => 'true',
                                        'description' => __('This Value is applicable for "By Fixed Reward Points" Reward Type', 'rewardsystem'),
                                        'value' => ''
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            woocommerce_wp_text_input(
                                    array(
                                        'id' => '_reward_percent[ + loop + ]',
                                        'label' => __('Reward Percent', 'rewardsystem'),
                                        'placeholder' => '',
                                        'desc_tip' => 'true',
                                        'description' => __('This Value is applicable for "By Percentage of Product Price" Reward Type', 'rewardsystem'),
                                        'value' => ''
                                    )
                            );
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <?php
                            // Select
                            woocommerce_wp_select(
                                    array(
                                        'id' => '_select_referral_reward_rule[ + loop + ]',
                                        'label' => __('Referral Reward Type', 'rewardsystem'),
                                        'class' => '_select_referral_reward_rule',
                                        'description' => __('Select Referral Reward Rule', 'rewardsystem'),
                                        'value' => '',
                                        'options' => array(
                                            '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                            '2' => __('By Percentage of Product Price', 'rewardsystem'),
                                        )
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            // Text Field
                            woocommerce_wp_text_input(
                                    array(
                                        'id' => '_referral_reward_points[ + loop + ]',
                                        'label' => __('Referral Reward Points', 'rewardsystem'),
                                        'placeholder' => '',
                                        'desc_tip' => 'true',
                                        'description' => __('This Value is applicable for "By Fixed Reward Points" Referral Referral Reward Type', 'rewardsystem'),
                                        'value' => ''
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            woocommerce_wp_text_input(
                                    array(
                                        'id' => '_referral_reward_percent[ + loop + ]',
                                        'label' => __('Referral Reward Percent', 'rewardsystem'),
                                        'placeholder' => '',
                                        'desc_tip' => 'true',
                                        'description' => __('This Value is applicable for "By Percentage of Product Price" Referral Reward Type', 'rewardsystem'),
                                        'value' => ''
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            // Select
                            woocommerce_wp_select(
                                    array(
                                        'id' => '_select_referral_reward_rule_getrefer[ + loop + ]',
                                        'label' => __(' Reward Type for Getting Referred', 'rewardsystem'),
                                        'class' => '_select_referral_reward_rule_getrefer',
                                        'description' => __('Select Referral Reward Rule for Getting Refer', 'rewardsystem'),
                                        'value' => '',
                                        'options' => array(
                                            '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                            '2' => __('By Percentage of Product Price', 'rewardsystem'),
                                        )
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            // Text Field
                            woocommerce_wp_text_input(
                                    array(
                                        'id' => '_referral_reward_points_getting_refer[ + loop + ]',
                                        'label' => __(' Reward Points for Getting Referred', 'rewardsystem'),
                                        'placeholder' => '',
                                        'desc_tip' => 'true',
                                        'description' => __('This Value is applicable for "By Fixed Reward Points" Referral Referral Reward Type', 'rewardsystem'),
                                        'value' => ''
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            woocommerce_wp_text_input(
                                    array(
                                        'id' => '_referral_reward_percent_getting_refer[ + loop + ]',
                                        'label' => __(' Reward Percent for getting Referred', 'rewardsystem'),
                                        'placeholder' => '',
                                        'desc_tip' => 'true',
                                        'description' => __('This Value is applicable for "By Percentage of Product Price" Referral Reward Type', 'rewardsystem'),
                                        'value' => ''
                                    )
                            );
                            ?>
                        </td>
                    </tr>
                </table>
                <?php
            }
        }

        public static function rs_admin_option_for_variable_product($loop, $variation_data, $variations) {

            global $post;
            global $woocommerce;
            $enable_reward_point = '';
            $reward_type = '';
            $reward_points = '';
            $reward_points_in_percent = '';
            $referral_reward_type = '';
            $referral_reward_points = '';
            $referral_reward_points_in_percent = '';
            $pointprice = '';
            $enablepointprice = '';
            $point_price_type = '';
            $pointprice_text = '';

            $gettingrefer = '';
            $gettingreferpercent = '';
            $getrefettype = '';
            $variation_data = get_post_meta($variations->ID);
            if (is_admin()) {

                if (isset($variation_data['_enable_reward_points_price'][0]))
                    $enablepointprice = $variation_data['_enable_reward_points_price'][0];


                woocommerce_wp_select(
                        array(
                            'id' => '_enable_reward_points_price[' . $loop . ']',
                            'label' => __('Enable Point Price:', 'rewardsystem'),
                            'desc_tip' => true,
                            'class' => '_enable_reward_points_price_variation',
                            'description' => __('Enable Point Price ', 'rewardsystem'),
                            'value' => $enablepointprice,
                            'default' => '1',
                            'options' => array(
                                '1' => __('Enable', 'rewardsystem'),
                                '2' => __('Disable', 'rewardsystem'),
                            )
                        )
                );


                if (isset($variation_data['_enable_reward_points_pricing_type'][0]))
                    $precing_type = $variation_data['_enable_reward_points_pricing_type'][0];


                woocommerce_wp_select(
                        array(
                            'id' => '_enable_reward_points_pricing_type[' . $loop . ']',
                            'label' => __(' Pricing Display Type:', 'rewardsystem'),
                            'desc_tip' => true,
                            'description' => __(' Pricing Type ', 'rewardsystem'),
                            'value' => $precing_type,
                            'class' => 'fp_point_price',
                            'default' => '1',
                            'options' => array(
                                '1' => __('Currency & Points', 'rewardsystem'),
                                '2' => __('Points Only', 'rewardsystem'),
                            )
                        )
                );


                if (isset($variation_data['_enable_reward_points_price_type'][0]))
                    $point_price_type = $variation_data['_enable_reward_points_price_type'][0];


                woocommerce_wp_select(
                        array(
                            'id' => '_enable_reward_points_price_type[' . $loop . ']',
                            'label' => __('Point Price Type:', 'rewardsystem'),
                            'desc_tip' => true,
                            'description' => __(' Point Price Type ', 'rewardsystem'),
                            'value' => $point_price_type,
                            'class' => 'fp_point_price_currency',
                            'default' => '1',
                            'options' => array(
                                '1' => __('By Fixed', 'rewardsystem'),
                                '2' => __('Based On Conversion', 'rewardsystem'),
                            )
                        )
                );
                if (isset($variation_data['_price_points_based_on_conversion'][0]))
                    $pointprice_text = $variation_data['_price_points_based_on_conversion'][0];


                woocommerce_wp_text_input(
                        array(
                            'id' => '_price_points_based_on_conversion[' . $loop . ']',
                            'label' => __(' Point Price Based on Conversion:', 'rewardsystem'),
                            'class' => 'fp_variation_points_price',
                            'size' => '5',
                            'value' => $pointprice_text,
                        )
                );

                if (isset($variation_data['price_points'][0]))
                    $pointprice = $variation_data['price_points'][0];


                woocommerce_wp_text_input(
                        array(
                            'id' => 'price_points[' . $loop . ']',
                            'label' => __('By Fixed PointPrice:', 'rewardsystem'),
                            'size' => '5',
                            'class' => 'fp_variation_points_price_field',
                            'value' => $pointprice,
                        )
                );

                if (isset($variation_data['_enable_reward_points'][0]))
                    $enable_reward_point = $variation_data['_enable_reward_points'][0];

                woocommerce_wp_select(
                        array(
                            'id' => '_enable_reward_points[' . $loop . ']',
                            'label' => __('Enable SUMO Reward Points', 'rewardsystem'),
                            'default' => '2',
                            'desc_tip' => false,
                            'description' => __('Enable will Turn On Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
                                    . 'Disable will Turn Off Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. ', 'rewardsystem'),
                            'value' => $enable_reward_point,
                            'options' => array(
                                '1' => __('Enable', 'rewardsystem'),
                                '2' => __('Disable', 'rewardsystem'),
                            )
                        )
                );
                if (isset($variation_data['_select_reward_rule'][0]))
                    $reward_type = $variation_data['_select_reward_rule'][0];


                woocommerce_wp_select(
                        array(
                            'id' => '_select_reward_rule[' . $loop . ']',
                            'label' => __('Reward Type', 'rewardsystem'),
                            'default' => '2',
                            'value' => $reward_type,
                            'options' => array(
                                '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                '2' => __('By Percentage of Product Price', 'rewardsystem'),
                            )
                        )
                );

                if (isset($variation_data['_reward_points'][0]))
                    $reward_points = $variation_data['_reward_points'][0];

                woocommerce_wp_text_input(
                        array(
                            'id' => '_reward_points[' . $loop . ']',
                            'label' => __('Reward Points', 'rewardsystem'),
                            'description' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'desc_tip' => 'true',
                            'value' => $reward_points
                        )
                );

                if (isset($variation_data['_reward_percent'][0]))
                    $reward_points_in_percent = $variation_data['_reward_percent'][0];

                woocommerce_wp_text_input(
                        array(
                            'id' => '_reward_percent[' . $loop . ']',
                            'label' => __('Reward Points in Percent %', 'rewardsystem'),
                            'description' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'desc_tip' => 'true',
                            'value' => $reward_points_in_percent
                        )
                );

                if (isset($variation_data['_select_referral_reward_rule'][0]))
                    $referral_reward_type = $variation_data['_select_referral_reward_rule'][0];


                woocommerce_wp_select(
                        array(
                            'id' => '_select_referral_reward_rule[' . $loop . ']',
                            'label' => __('Referral Reward Type', 'rewardsystem'),
                            'default' => '2',
                            'value' => $referral_reward_type,
                            'options' => array(
                                '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                '2' => __('By Percentage of Product Price', 'rewardsystem'),
                            )
                        )
                );

                if (isset($variation_data['_referral_reward_points'][0]))
                    $referral_reward_points = $variation_data['_referral_reward_points'][0];

                woocommerce_wp_text_input(
                        array(
                            'id' => '_referral_reward_points[' . $loop . ']',
                            'label' => __('Referral Reward Points', 'rewardsystem'),
                            'description' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'desc_tip' => 'true',
                            'value' => $referral_reward_points
                        )
                );

                if (isset($variation_data['_referral_reward_percent'][0]))
                    $referral_reward_points_in_percent = $variation_data['_referral_reward_percent'][0];

                woocommerce_wp_text_input(
                        array(
                            'id' => '_referral_reward_percent[' . $loop . ']',
                            'label' => __('Referral Reward Points in Percent %', 'rewardsystem'),
                            'description' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'desc_tip' => 'true',
                            'value' => $referral_reward_points_in_percent
                        )
                );

                if (isset($variation_data['_select_referral_reward_rule_getrefer'][0]))
                    $getrefettype = $variation_data['_select_referral_reward_rule_getrefer'][0];


                woocommerce_wp_select(
                        array(
                            'id' => '_select_referral_reward_rule_getrefer[' . $loop . ']',
                            'label' => __(' Reward Type for Getting Referred', 'rewardsystem'),
                            'default' => '2',
                            'value' => $getrefettype,
                            'options' => array(
                                '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                '2' => __('By Percentage of Product Price', 'rewardsystem'),
                            )
                        )
                );

                if (isset($variation_data['_referral_reward_points_getting_refer'][0]))
                    $gettingrefer = $variation_data['_referral_reward_points_getting_refer'][0];

                woocommerce_wp_text_input(
                        array(
                            'id' => '_referral_reward_points_getting_refer[' . $loop . ']',
                            'label' => __('Reward Points for Getting Referred', 'rewardsystem'),
                            'description' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'desc_tip' => 'true',
                            'value' => $gettingrefer
                        )
                );





                if (isset($variation_data['_referral_reward_percent_getting_refer'][0]))
                    $gettingreferpercent = $variation_data['_referral_reward_percent_getting_refer'][0];

                woocommerce_wp_text_input(
                        array(
                            'id' => '_referral_reward_percent_getting_refer[' . $loop . ']',
                            'label' => __(' Reward Points in Percent % for Getting Referred', 'rewardsystem'),
                            'description' => __('When left empty, Category and Product Settings will be considered in the same order and Current Settings (Global Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Global Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'desc_tip' => 'true',
                            'value' => $gettingreferpercent
                        )
                );
            }
        }

        public static function save_variable_product_fields_for_subscription($post_id) {
            if (isset($_POST['variable_sku'])) :
                $variable_sku = $_POST['variable_sku'];
                $variable_post_id = $_POST['variable_post_id'];

// Text Field
                $_text_field = $_POST['_reward_points'];
                for ($i = 0; $i < sizeof($variable_sku); $i++) :
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($_text_field[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_reward_points', stripslashes($_text_field[$i]));
                    }
                endfor;
                $point_select = $_POST['_enable_reward_points_price'];
                for ($i = 0; $i < sizeof($variable_sku); $i++):
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($point_select[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_enable_reward_points_price', stripslashes($point_select[$i]));
                    }
                endfor;

                $_text_field1 = $_POST['$_enable_reward_points_pricing_type'];
                for ($i = 0; $i < sizeof($variable_sku); $i++) :
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($_text_field1[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '$_enable_reward_points_pricing_type', stripslashes($_text_field1[$i]));
                    }
                endfor;




                $point_text = $_POST['price_points'];
                for ($i = 0; $i < sizeof($variable_sku); $i++):
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($point_text[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, 'price_points', stripslashes($point_text[$i]));
                    }
                endfor;

                $points_based_on_conversion = $_POST['variable_sale_price'];

                $point_price_type = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variation_id, '_enable_reward_points_price_type');
                if ($point_price_type == 2) {
                    for ($i = 0; $i < sizeof($variable_sku); $i++):
                        $variation_id = (int) $variable_post_id[$i];

                        if (isset($points_based_on_conversion[$i])) {
                            $newvalue = $points_based_on_conversion[$i] / wc_format_decimal(get_option('rs_redeem_point_value'));
                            $points_based_on_conversion[$i] = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_price_points_based_on_conversion', stripslashes($points_based_on_conversion[$i]));
                        }
                    endfor;
                    for ($i = 0; $i < sizeof($variable_sku); $i++):
                        $variation_id = (int) $variable_post_id[$i];
                        $point_price_typeert = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variation_id, '_price_points_based_on_conversion', true);
                        if (empty($point_price_typeert)) {
                            $points_based_on_conversion = $_POST['variable_regular_price'];
                            if (isset($points_based_on_conversion[$i])) {
                                $newvalue = $points_based_on_conversion[$i] / wc_format_decimal(get_option('rs_redeem_point_value'));
                                $points_based_on_conversion[$i] = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
                                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_price_points_based_on_conversion', stripslashes($points_based_on_conversion[$i]));
                            }
                        }
                    endfor;
                } else {
                    for ($i = 0; $i < sizeof($variable_sku); $i++):
                        $variation_id = (int) $variable_post_id[$i];
                        if (isset($points_based_on_conversion[$i])) {
                            $points_based_on_conversion[$i] = '';
                            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_price_points_based_on_conversion', stripslashes($points_based_on_conversion[$i]));
                        }
                    endfor;
                }


                $percent_text_field = $_POST['_reward_percent'];
                for ($i = 0; $i < sizeof($variable_sku); $i++):
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($percent_text_field[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_reward_percent', stripslashes($percent_text_field[$i]));
                    }
                endfor;
//select
                $new_select = $_POST['_select_reward_rule'];
                for ($i = 0; $i < sizeof($variable_sku); $i++):
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($new_select[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_select_reward_rule', stripslashes($new_select[$i]));
                    }
                endfor;


                $_text_fields = $_POST['_referral_reward_points'];
                for ($i = 0; $i < sizeof($variable_sku); $i++) :
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($_text_field[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_referral_reward_points', stripslashes($_text_fields[$i]));
                    }
                endfor;

                $percent_text_fields = $_POST['_referral_reward_percent'];
                for ($i = 0; $i < sizeof($variable_sku); $i++):
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($percent_text_field[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_referral_reward_percent', stripslashes($percent_text_fields[$i]));
                    }
                endfor;

                $_text_fields = $_POST['_referral_reward_points_getting_refer'];
                for ($i = 0; $i < sizeof($variable_sku); $i++) :
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($_text_field[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_referral_reward_points_getting_refer', stripslashes($_text_fields[$i]));
                    }
                endfor;

                $percent_text_fields = $_POST['_referral_reward_percent_getting_refer'];
                for ($i = 0; $i < sizeof($variable_sku); $i++):
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($percent_text_field[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_referral_reward_percent_getting_refer', stripslashes($percent_text_fields[$i]));
                    }
                endfor;

                $new_selects = $_POST['_select_referral_reward_rule_getrefer'];
                for ($i = 0; $i < sizeof($variable_sku); $i++):
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($new_select[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_select_referral_reward_rule_getrefer', stripslashes($new_selects[$i]));
                    }
                endfor;

//select
                $new_selects = $_POST['_select_referral_reward_rule'];
                for ($i = 0; $i < sizeof($variable_sku); $i++):
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($new_select[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_select_referral_reward_rule', stripslashes($new_selects[$i]));
                    }
                endfor;


// Select
                $_select = $_POST['_enable_reward_points'];
                for ($i = 0; $i < sizeof($variable_sku); $i++) :
                    $variation_id = (int) $variable_post_id[$i];
                    if (isset($_select[$i])) {
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_enable_reward_points', stripslashes($_select[$i]));
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_enable_reward_points', stripslashes($_select[$i]));
                    }
                endfor;
            endif;
        }

        public static function save_variable_product_fields($variation_id, $i) {

            $variable_sku = $_POST['variable_sku'];
            $variable_post_id = $_POST['variable_post_id'];
            $fff = get_post_meta($variation_id, '_regular_price');
            $conversion_type = $_POST['_enable_reward_points_price_type'];
            $regular_price = get_post_meta($variation_id, '_regular_price', true);
            if ($regular_price == '') {
                update_post_meta($variation_id, '_regular_price', 0);
                update_post_meta($variation_id, '_price', 0);
            }



            if (isset($conversion_type[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_enable_reward_points_price_type', stripslashes($conversion_type[$i]));
            }
            $points_based_on_conversion = $_POST['variable_sale_price'];
            $point_price_type = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variation_id, '_enable_reward_points_price_type');
            if ($point_price_type == 2) {
                if (isset($points_based_on_conversion[$i])) {
                    $newvalue = $points_based_on_conversion[$i] / wc_format_decimal(get_option('rs_redeem_point_value'));
                    $points_based_on_conversion[$i] = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_price_points_based_on_conversion', stripslashes($points_based_on_conversion[$i]));
                    $point_price_typecv = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($variation_id, '_price_points_based_on_conversion');
                    if (empty($point_price_typecv)) {
                        $points_based_on_conversion = $_POST['variable_regular_price'];
                        $newvalue = $points_based_on_conversion[$i] / wc_format_decimal(get_option('rs_redeem_point_value'));
                        $points_based_on_conversion[$i] = $newvalue * wc_format_decimal(get_option('rs_redeem_point'));
                        RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_price_points_based_on_conversion', stripslashes($points_based_on_conversion[$i]));
                    }
                }
            } else {
                if (isset($points_based_on_conversion[$i])) {
                    $points_based_on_conversion[$i] = '';
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_price_points_based_on_conversion', stripslashes($points_based_on_conversion[$i]));
                }
            }

            // Text Field
            $_text_field = $_POST['_reward_points'];

            if (isset($_text_field[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_reward_points', stripslashes($_text_field[$i]));
            }

            $point_select = $_POST['_enable_reward_points_price'];

            if (isset($point_select[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_enable_reward_points_price', stripslashes($point_select[$i]));
            }

            $point_text = $_POST['price_points'];
            if (isset($point_text[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, 'price_points', stripslashes($point_text[$i]));
            }

            $_enable_reward_points_pricing_type = $_POST['_enable_reward_points_pricing_type'];

            if (isset($_enable_reward_points_pricing_type[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_enable_reward_points_pricing_type', stripslashes($_enable_reward_points_pricing_type[$i]));
            }


            $percent_text_field = $_POST['_reward_percent'];
            if (isset($percent_text_field[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_reward_percent', stripslashes($percent_text_field[$i]));
            }

            //select
            $new_select = $_POST['_select_reward_rule'];
            if (isset($new_select[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_select_reward_rule', stripslashes($new_select[$i]));
            }

            $_text_fields = $_POST['_referral_reward_points'];
            if (isset($_text_field[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_referral_reward_points', stripslashes($_text_fields[$i]));
            }

            $percent_text_fields = $_POST['_referral_reward_percent'];
            if (isset($percent_text_field[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_referral_reward_percent', stripslashes($percent_text_fields[$i]));
            }
            $percent_text_fields = $_POST['_referral_reward_percent_getting_refer'];
            if (isset($percent_text_field[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_referral_reward_percent_getting_refer', stripslashes($percent_text_fields[$i]));
            }
            $new_selects = $_POST['_select_referral_reward_rule_getrefer'];
            if (isset($new_select[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_select_referral_reward_rule_getrefer', stripslashes($new_selects[$i]));
            }

            $_text_fields = $_POST['_referral_reward_points_getting_refer'];
            if (isset($_text_field[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_referral_reward_points_getting_refer', stripslashes($_text_fields[$i]));
            }
            //select
            $new_selects = $_POST['_select_referral_reward_rule'];
            if (isset($new_select[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_select_referral_reward_rule', stripslashes($new_selects[$i]));
            }

            // Select
            $_select = $_POST['_enable_reward_points'];
            if (isset($_select[$i])) {
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_enable_reward_points', stripslashes($_select[$i]));
                RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($variation_id, '_enable_reward_points', stripslashes($_select[$i]));
            }
        }

        public static function rs_validation_for_input_field_in_variable_product($loop, $variation, $id) {
            ?>

            <script type="text/javascript">
                jQuery(document).ready(function () {

                    jQuery('.fp_variation_points_price').attr('readonly', 'true');
                    jQuery(document).on('change', '.fp_point_price', function () {

                        jQuery('.fp_variation_points_price').attr('readonly', 'true');

                    });
                    jQuery('#publish').click(function (e) {
                        if (jQuery("select[name='_enable_reward_points_pricing_type[<?php echo $loop ?>]']").val() == '2') {
                            if (jQuery("[name='price_points[<?php echo $loop ?>]']").val() == '') {
                                jQuery("[name='price_points[<?php echo $loop ?>]']").css({
                                    "border": "1px solid red",
                                    "background": "#FFCECE"
                                });

                                jQuery("[name='price_points[<?php echo $loop ?>]']").show();
                                jQuery("[name='price_points[<?php echo $loop ?>]']").focus();

                                e.preventDefault();
                            }
                        }
                    });
                });
                jQuery("select[name='_enable_reward_points_price[<?php echo $loop ?>]']").change(function () {
                    if (jQuery("select[name='_enable_reward_points_price[<?php echo $loop ?>]']").val() == '2') {
                        jQuery("select[name='_enable_reward_points_pricing_type[<?php echo $loop ?>]']").parent().hide();
                        jQuery("select[name='_enable_reward_points_price_type[<?php echo $loop ?>]']").parent().hide();
                        jQuery("[name='_price_points_based_on_conversion[<?php echo $loop ?>]']").parent().hide();
                        jQuery("[name='price_points[<?php echo $loop ?>]']").parent().hide();
                        jQuery("[name='variable_regular_price[<?php echo $loop ?>]']").parent().show();
                        jQuery("[name='variable_sale_price[<?php echo $loop ?>]']").parent().show();
                    } else {
                        jQuery("select[name='_enable_reward_points_pricing_type[<?php echo $loop ?>]']").parent().show();
                        if (jQuery("select[name='_enable_reward_points_pricing_type[<?php echo $loop ?>]']").val() == '2') {
                            jQuery("[name='price_points[<?php echo $loop ?>]']").parent().show();
                            jQuery("select[name='_enable_reward_points_price_type[<?php echo $loop ?>]']").parent().hide();
                            jQuery("[name='_price_points_based_on_conversion[<?php echo $loop ?>]']").parent().hide();
                            jQuery("[name='variable_regular_price[<?php echo $loop ?>]']").parent().hide();
                            jQuery("[name='variable_sale_price[<?php echo $loop ?>]']").parent().hide();
                        } else {
                            jQuery("select[name='_enable_reward_points_price_type[<?php echo $loop ?>]']").parent().show();
                            jQuery("[name='variable_regular_price[<?php echo $loop ?>]']").parent().show();
                            jQuery("[name='variable_sale_price[<?php echo $loop ?>]']").parent().show();
                            if (jQuery("select[name='_enable_reward_points_price_type[<?php echo $loop ?>]']").val() == '2') {
                                jQuery("[name='_price_points_based_on_conversion[<?php echo $loop ?>]']").parent().show();
                                jQuery("[name='price_points[<?php echo $loop ?>]']").parent().hide();
                            } else {
                                jQuery("[name='price_points[<?php echo $loop ?>]']").parent().show();
                                jQuery("[name='_price_points_based_on_conversion[<?php echo $loop ?>]']").parent().hide();
                            }
                        }

                    }
                });

                if (jQuery("select[name='_enable_reward_points_pricing_type[<?php echo $loop ?>]']").val() == '2') {
                    jQuery("[name='price_points[<?php echo $loop ?>]']").parent().show();
                    jQuery("select[name='_enable_reward_points_price_type[<?php echo $loop ?>]']").parent().hide();
                    jQuery("[name='_price_points_based_on_conversion[<?php echo $loop ?>]']").parent().hide();
                    jQuery("[name='variable_regular_price[<?php echo $loop ?>]']").parent().hide();
                    jQuery("[name='variable_sale_price[<?php echo $loop ?>]']").parent().hide();
                } else {
                    jQuery("select[name='_enable_reward_points_price_type[<?php echo $loop ?>]']").parent().show();
                    jQuery("[name='variable_regular_price[<?php echo $loop ?>]']").parent().show();
                    jQuery("[name='variable_sale_price[<?php echo $loop ?>]']").parent().show();
                    if (jQuery("select[name='_enable_reward_points_price_type[<?php echo $loop ?>]']").val() == '2') {
                        jQuery("[name='_price_points_based_on_conversion[<?php echo $loop ?>]']").parent().show();
                        jQuery("[name='price_points[<?php echo $loop ?>]']").parent().hide();
                    } else {
                        jQuery("[name='price_points[<?php echo $loop ?>]']").parent().show();
                        jQuery("[name='_price_points_based_on_conversion[<?php echo $loop ?>]']").parent().hide();
                    }
                }
                jQuery("select[name='_enable_reward_points_pricing_type[<?php echo $loop ?>]']").change(function () {
                    if (jQuery("select[name='_enable_reward_points_pricing_type[<?php echo $loop ?>]']").val() == '2') {
                        jQuery("[name='price_points[<?php echo $loop ?>]']").parent().show();
                        jQuery("select[name='_enable_reward_points_price_type[<?php echo $loop ?>]']").parent().hide();
                        jQuery("[name='_price_points_based_on_conversion[<?php echo $loop ?>]']").parent().hide();
                        jQuery("[name='variable_regular_price[<?php echo $loop ?>]']").parent().hide();
                        jQuery("[name='variable_sale_price[<?php echo $loop ?>]']").parent().hide();
                    } else {
                        jQuery("select[name='_enable_reward_points_price_type[<?php echo $loop ?>]']").parent().show();
                        jQuery("[name='variable_regular_price[<?php echo $loop ?>]']").parent().show();
                        jQuery("[name='variable_sale_price[<?php echo $loop ?>]']").parent().show();
                        if (jQuery("select[name='_enable_reward_points_price_type[<?php echo $loop ?>]']").val() == '2') {
                            jQuery("[name='_price_points_based_on_conversion[<?php echo $loop ?>]']").parent().show();
                            jQuery("[name='price_points[<?php echo $loop ?>]']").parent().hide();
                        } else {
                            jQuery("[name='price_points[<?php echo $loop ?>]']").parent().show();
                            jQuery("[name='_price_points_based_on_conversion[<?php echo $loop ?>]']").parent().hide();
                        }
                    }
                });
                jQuery("select[name='_enable_reward_points_price_type[<?php echo $loop ?>]']").change(function () {
                    if (jQuery("select[name='_enable_reward_points_price_type[<?php echo $loop ?>]']").val() == '2') {
                        jQuery("[name='_price_points_based_on_conversion[<?php echo $loop ?>]']").parent().show();
                        jQuery("[name='price_points[<?php echo $loop ?>]']").parent().hide();
                    } else {
                        jQuery("[name='price_points[<?php echo $loop ?>]']").parent().show();
                        jQuery("[name='_price_points_based_on_conversion[<?php echo $loop ?>]']").parent().hide();
                    }
                });
            </script>
            <?php
        }

    }

    RSVariableProduct::init();
}