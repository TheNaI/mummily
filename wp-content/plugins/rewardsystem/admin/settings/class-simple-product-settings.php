<?php
/*
 * Simple Product Settings
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSSimpleProduct')) {

    class RSSimpleProduct {

        public static function init() {

            add_action('woocommerce_product_options_general_product_data', array(__CLASS__, 'rs_admin_option_for_simple_product'), 1);

            add_action('woocommerce_product_options_advanced', array(__CLASS__, 'rs_common_admin_options_for_social_input_field'));

            add_action('woocommerce_process_product_meta', array(__CLASS__, 'save_reward_points_admin_fields_to_product_meta'));

            add_action('woocommerce_process_product_meta', array(__CLASS__, 'save_social_reward_points_admin_fields_to_product_meta'));

            add_action('woocommerce_product_options_advanced', array(__CLASS__, 'rs_admin_option_for_point_price_setting'));

            add_action('woocommerce_process_product_meta', array(__CLASS__, 'save_admin_field_for_point_price_setting'));
        }

        public static function rs_admin_option_for_simple_product() {
            global $post;
            if (is_admin()) {
                ?>
                <div class="options_group show_if_simple show_if_subscription show_if_booking show_if_external">
                    <?php
                    woocommerce_wp_select(array(
                        'id' => '_rewardsystemcheckboxvalue',
                        'class' => 'rewardsystemcheckboxvalue',
                        'placeholder' => '',
                        'desc_tip' => 'true',
                        'description' => __('Enable will Turn On Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. '
                                . 'Disable will Turn Off Reward Points for Product Purchase and Category/Product Settings will be considered if it is available. ', 'rewardsystem'),
                        'label' => __('Enable SUMO Reward Points for Product Purchase', 'rewardsystem'),
                        'options' => array(
                            'no' => __('Disable', 'rewardsystem'),
                            'yes' => __('Enable', 'rewardsystem'),
                        )
                            )
                    );

                    woocommerce_wp_select(array(
                        'id' => '_rewardsystem_options',
                        'class' => 'rewardsystem_options show_if_enable',
                        'label' => __('Reward Type', 'rewardsystem'),
                        'options' => array(
                            '1' => __('By Fixed Reward Points', 'rewardsystem'),
                            '2' => __('By Percentage of Product Price', 'rewardsystem'),
                        )
                            )
                    );
                    woocommerce_wp_text_input(
                            array(
                                'id' => '_rewardsystempoints',
                                'class' => 'show_if_enable',
                                'name' => '_rewardsystempoints',
                                'placeholder' => '',
                                'desc_tip' => 'true',
                                'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                                'label' => __('Reward Points', 'rewardsystem'),
                            )
                    );
                    woocommerce_wp_text_input(
                            array(
                                'id' => '_rewardsystempercent',
                                'class' => 'show_if_enable',
                                'name' => '_rewardsystempercent',
                                'placeholder' => '',
                                'desc_tip' => 'true',
                                'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                                'label' => __('Reward Points in Percent %', 'rewardsystem')
                            )
                    );
                    woocommerce_wp_select(array(
                        'id' => '_referral_rewardsystem_options',
                        'class' => 'referral_rewardsystem_options show_if_enable',
                        'label' => __('Referral Reward Type', 'rewardsystem'),
                        'options' => array(
                            '1' => __('By Fixed Reward Points', 'rewardsystem'),
                            '2' => __('By Percentage of Product Price', 'rewardsystem'),
                        )
                            )
                    );
                    woocommerce_wp_text_input(
                            array(
                                'id' => '_referralrewardsystempoints',
                                'class' => 'show_if_enable',
                                'name' => '_referralrewardsystempoints',
                                'placeholder' => '',
                                'desc_tip' => 'true',
                                'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                                'label' => __('Referral Reward Points', 'rewardsystem')
                            )
                    );
                    woocommerce_wp_text_input(
                            array(
                                'id' => '_referralrewardsystempercent',
                                'class' => 'show_if_enable',
                                'name' => '_referralrewardsystempercent',
                                'placeholder' => '',
                                'desc_tip' => 'true',
                                'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                                'label' => __('Referral Reward Points in Percent %', 'rewardsystem')
                            )
                    );

                    woocommerce_wp_select(array(
                        'id' => '_referral_rewardsystem_options_getrefer',
                        'class' => 'referral_rewardsystem_options_get show_if_enable',
                        'label' => __('Getting Referred Reward Type', 'rewardsystem'),
                        'options' => array(
                            '1' => __('By Fixed Reward Points', 'rewardsystem'),
                            '2' => __('By Percentage of Product Price', 'rewardsystem'),
                        )
                            )
                    );

                    woocommerce_wp_text_input(
                            array(
                                'id' => '_referralrewardsystempoints_for_getting_referred',
                                'class' => 'show_if_enable',
                                'name' => '_referralrewardsystempoints_for_getting_referred',
                                'placeholder' => '',
                                'desc_tip' => 'true',
                                'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                                'label' => __('Reward Points for Getting Referred', 'rewardsystem')
                            )
                    );

                    woocommerce_wp_text_input(
                            array(
                                'id' => '_referralrewardsystempercent_for_getting_referred',
                                'class' => 'show_if_enable',
                                'name' => '_referralrewardsystempercent_for_getting_referred',
                                'placeholder' => '',
                                'desc_tip' => 'true',
                                'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                        . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                                'label' => __(' Reward Points in Percent % for Getting Referred', 'rewardsystem')
                            )
                    );
                    ?>
                </div>
                <?php
            }
        }

        public static function rs_common_admin_options_for_social_input_field() {
            if (is_admin()) {
                woocommerce_wp_select(array(
                    'id' => '_socialrewardsystemcheckboxvalue',
                    'class' => 'socialrewardsystemcheckboxvalue',
                    'placeholder' => '',
                    'desc_tip' => 'true',
                    'description' => __('Enable will Turn On Reward Points for Product Purchase and Category/Global Settings will be considered when applicable. '
                            . 'Disable will Turn Off Reward Points for Product Purchase and Category/Global Settings will not be considered. ', 'rewardsystem'),
                    'label' => __('Enable SUMO Reward Points for Social Promotion', 'rewardsystem'),
                    'options' => array(
                        'no' => __('Disable', 'rewardsystem'),
                        'yes' => __('Enable', 'rewardsystem'),
                    )
                        )
                );

                woocommerce_wp_select(
                        array(
                            'id' => '_social_rewardsystem_options_facebook',
                            'class' => 'social_rewardsystem_options_facebook show_if_social_enable',
                            'label' => __('Facebook Like Reward Type', 'rewardsystem'),
                            'options' => array(
                                '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                '2' => __('By Percentage of Product Price', 'rewardsystem')
                            )
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempoints_facebook',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempoints_facebook',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('Facebook Like Reward Points', 'rewardsystem')
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempercent_facebook',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempercent_facebook',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('Facebook Like Reward Points in Percent %', 'rewardsystem')
                        )
                );
                woocommerce_wp_select(
                        array(
                            'id' => '_social_rewardsystem_options_facebook_share',
                            'class' => ' _social_rewardsystem_options_facebook_share show_if_social_enable',
                            'label' => __('Facebook Share Reward Type', 'rewardsystem'),
                            'options' => array(
                                '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                '2' => __('By Percentage of Product Price', 'rewardsystem')
                            )
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempoints_facebook_share',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempoints_facebook_share',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('Facebook Share Reward Points', 'rewardsystem')
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempercent_facebook_share',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempercent_facebook_share',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('Facebook Share Reward Points in Percent %', 'rewardsystem')
                        )
                );
                woocommerce_wp_select(
                        array(
                            'id' => '_social_rewardsystem_options_twitter',
                            'class' => 'social_rewardsystem_options_twitter show_if_social_enable',
                            'label' => __('Twitter Tweet Reward Type', 'rewardsystem'),
                            'options' => array(
                                '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                '2' => __('By Percentage of Product Price', 'rewardsystem')
                            )
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempoints_twitter',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempoints_twitter',
                            'placeholder' => '',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('Twitter Tweet Reward Points', 'rewardsystem')
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempercent_twitter',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempercent_twitter',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('Twitter Tweet Reward Percent %', 'rewardsystem')
                        )
                );
                woocommerce_wp_select(
                        array(
                            'id' => '_social_rewardsystem_options_twitter_follow',
                            'class' => '_social_rewardsystem_options_twitter_follow show_if_social_enable',
                            'label' => __('Twitter Follow Reward Type', 'rewardsystem'),
                            'options' => array(
                                '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                '2' => __('By Percentage of Product Price', 'rewardsystem')
                            )
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempoints_twitter_follow',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempoints_twitter_follow',
                            'placeholder' => '',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('Twitter Follow Reward Points', 'rewardsystem')
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempercent_twitter_follow',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempercent_twitter_follow',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('Twitter Follow Reward Percent %', 'rewardsystem')
                        )
                );
                woocommerce_wp_select(
                        array(
                            'id' => '_social_rewardsystem_options_google',
                            'class' => 'social_rewardsystem_options_google show_if_social_enable',
                            'label' => __('Google+1 Reward Type', 'rewardsystem'),
                            'options' => array(
                                '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                '2' => __('By Percentage of Product Price', 'rewardsystem')
                            )
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempoints_google',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempoints_google',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('Google+1 Reward Points', 'rewardsystem')
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempercent_google',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempercent_google',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('Google+1 Reward Percent %', 'rewardsystem')
                        )
                );
                woocommerce_wp_select(
                        array(
                            'id' => '_social_rewardsystem_options_vk',
                            'class' => 'social_rewardsystem_options_vk show_if_social_enable',
                            'label' => __('VK.com Like Reward Type', 'rewardsystem'),
                            'options' => array(
                                '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                '2' => __('By Percentage of Product Price', 'rewardsystem')
                            )
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempoints_vk',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempoints_vk',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('VK.com Like Reward Points ', 'rewardsystem')
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempercent_vk',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempercent_vk',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('VK.com Like Reward Percent %', 'rewardsystem')
                        )
                );

                woocommerce_wp_select(
                        array(
                            'id' => '_social_rewardsystem_options_instagram',
                            'class' => '_social_rewardsystem_options_instagram show_if_social_enable',
                            'label' => __('Instagram Reward Type', 'rewardsystem'),
                            'options' => array(
                                '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                '2' => __('By Percentage of Product Price', 'rewardsystem')
                            )
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempoints_instagram',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempoints_instagram',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('Instagram Reward Points ', 'rewardsystem')
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempercent_instagram',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempercent_instagram',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('Instagram Reward Percent %', 'rewardsystem')
                        )
                );
                woocommerce_wp_select(
                        array(
                            'id' => '_social_rewardsystem_options_ok_follow',
                            'class' => '_social_rewardsystem_options_ok_follow show_if_social_enable',
                            'label' => __('OK.ru Share Reward Type', 'rewardsystem'),
                            'options' => array(
                                '1' => __('By Fixed Reward Points', 'rewardsystem'),
                                '2' => __('By Percentage of Product Price', 'rewardsystem')
                            )
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempoints_ok_follow',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempoints_ok_follow',
                            'placeholder' => '',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('OK.ru Share Reward Points', 'rewardsystem')
                        )
                );
                woocommerce_wp_text_input(
                        array(
                            'id' => '_socialrewardsystempercent_ok_follow',
                            'class' => 'show_if_social_enable',
                            'name' => '_socialrewardsystempercent_ok_follow',
                            'placeholder' => '',
                            'desc_tip' => 'true',
                            'description' => __('When left empty, Category and Global Settings will be considered in the same order and Current Settings (Product Settings) will be ignored. '
                                    . 'When value greater than or equal to 0 is entered then Current Settings (Product Settings) will be considered and Category/Global Settings will be ignored.  ', 'rewardsystem'),
                            'label' => __('OK.ru Share Reward Percent %', 'rewardsystem')
                        )
                );
            }
        }

        /* Add Admin Field Point Price Setting */

        public static function rs_admin_option_for_point_price_setting() {
            global $post;
            if (is_admin()) {
                $gettheproducts = rs_get_product_object($post->ID);                
                if (is_object($gettheproducts) && ($gettheproducts->is_type('simple') || $gettheproducts->is_type('booking') || $gettheproducts->is_type('lottery'))) {
                    if (get_option('rs_enable_disable_point_priceing') == '1') {
                        woocommerce_wp_select(array(
                            'id' => '_rewardsystem_enable_point_price',
                            'class' => '_rewardsystem_enable_point_price',
                            'label' => __('Enable Point Pricing', 'rewardsystem'),
                            'options' => array(
                                'no' => __('Disable', 'rewardsystem'),
                                'yes' => __('Enable', 'rewardsystem'),
                            )
                        ));


                        woocommerce_wp_select(array(
                            'id' => '_rewardsystem_enable_point_price_type',
                            'class' => '_rewardsystem_enable_point_price_type',
                            'label' => __(' Pricing Display Type', 'rewardsystem'),
                            'options' => array(
                                '1' => __('Currency & Point Price', 'rewardsystem'),
                                '2' => __('Only Point Price', 'rewardsystem'),
                            ),
                            'std' => '1'
                        ));
                        woocommerce_wp_select(array(
                            'id' => '_rewardsystem_point_price_type',
                            'class' => '_rewardsystem_point_price_type',
                            'label' => __('Point Price Type', 'rewardsystem'),
                            'options' => array(
                                '1' => __('By Fixed', 'rewardsystem'),
                                '2' => __('Based On Conversion', 'rewardsystem'),
                            ),
                            'std' => '1'
                        ));
                        woocommerce_wp_text_input(
                                array(
                                    'id' => '_rewardsystem__points',
                                    'class' => '_rewardsystem__points',
                                    'name' => '_rewardsystem__points',
                                    'label' => __('Points to product', 'rewardsystem')
                                )
                        );
                        woocommerce_wp_text_input(
                                array(
                                    'id' => '_rewardsystem__points_based_on_conversion',
                                    'class' => '_rewardsystem__points_based_on_conversion',
                                    'name' => '_rewardsystem__points_based_on_conversion',
                                    'readonly' => "readonly",
                                    'label' => __('Points Based On Conversion', 'rewardsystem')
                        ));
                    }
                }
            }
        }

        /* Save the Social Reward Points custom fields value in the product meta for Product Settings */

        public static function save_reward_points_admin_fields_to_product_meta($post_id) {

            $reward_system_enabled_value = $_POST['_rewardsystemcheckboxvalue'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_rewardsystemcheckboxvalue', $reward_system_enabled_value);

            /*
             * Saving Reward Points of Simple Product to prodcut meta
             */
            $reward_selection_type = $_POST['_rewardsystem_options'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_rewardsystem_options', $reward_selection_type);
            $fixed_reward_points = $_POST['_rewardsystempoints'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_rewardsystempoints', $fixed_reward_points);
            $percentage_reward_points = $_POST['_rewardsystempercent'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_rewardsystempercent', $percentage_reward_points);

            /*
             * Saving Referral Reward Points of Simple Product to prodcut meta
             */
            $referral_reward_selection_type = $_POST['_referral_rewardsystem_options'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_referral_rewardsystem_options', $referral_reward_selection_type);
            $fixed_referral_reward_points = $_POST['_referralrewardsystempoints'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_referralrewardsystempoints', $fixed_referral_reward_points);
            $percentage_referral_reward_points = $_POST['_referralrewardsystempercent'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_referralrewardsystempercent', $percentage_referral_reward_points);



            $referral_reward_points_for_getting_referred = $_POST['_referralrewardsystempoints_for_getting_referred'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_referralrewardsystempoints_for_getting_referred', $referral_reward_points_for_getting_referred);

            $referral_reward_selection_type = $_POST['_referral_rewardsystem_options_getrefer'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_referral_rewardsystem_options_getrefer', $referral_reward_selection_type);

            $referral_reward_points_percent_for_getting_referred = $_POST['_referralrewardsystempercent_for_getting_referred'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_referralrewardsystempercent_for_getting_referred', $referral_reward_points_percent_for_getting_referred);
        }

        /* Save the Social Reward Points custom fields value in the product meta for Social Settings */

        public static function save_social_reward_points_admin_fields_to_product_meta($post_id) {

            $social_reward_system_enabled_value = $_POST['_socialrewardsystemcheckboxvalue'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystemcheckboxvalue', $social_reward_system_enabled_value);

            /*
             * Saving Social Reward Points for Facebook to prodcut meta
             */
            $social_reward_selection_type_for_facebook = $_POST['_social_rewardsystem_options_facebook'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_social_rewardsystem_options_facebook', $social_reward_selection_type_for_facebook);
            $social_fixed_reward_points_for_facebook = $_POST['_socialrewardsystempoints_facebook'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempoints_facebook', $social_fixed_reward_points_for_facebook);
            $social_percentage_reward_points_for_facebook = $_POST['_socialrewardsystempercent_facebook'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempercent_facebook', $social_percentage_reward_points_for_facebook);


            $social_reward_selection_type_for_facebook_share = $_POST['_social_rewardsystem_options_facebook_share'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_social_rewardsystem_options_facebook_share', $social_reward_selection_type_for_facebook_share);
            $social_fixed_reward_points_for_facebookshare = $_POST['_socialrewardsystempoints_facebook_share'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempoints_facebook_share', $social_fixed_reward_points_for_facebookshare);
            $social_percentage_reward_points_for_facebook_share = $_POST['_socialrewardsystempercent_facebook_share'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempercent_facebook_share', $social_percentage_reward_points_for_facebook_share);


            /*
             * Saving Social Reward Points for Twitter to prodcut meta
             */
            $social_reward_selection_type_for_twitter = $_POST['_social_rewardsystem_options_twitter'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_social_rewardsystem_options_twitter', $social_reward_selection_type_for_twitter);
            $social_fixed_reward_points_for_twitter = $_POST['_socialrewardsystempoints_twitter'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempoints_twitter', $social_fixed_reward_points_for_twitter);
            $social_percentage_reward_points_for_twitter = $_POST['_socialrewardsystempercent_twitter'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempercent_twitter', $social_percentage_reward_points_for_twitter);

            $social_reward_selection_type_for_twitter_follow = $_POST['_social_rewardsystem_options_twitter_follow'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_social_rewardsystem_options_twitter_follow', $social_reward_selection_type_for_twitter_follow);
            $social_fixed_reward_points_for_twitter_follow = $_POST['_socialrewardsystempoints_twitter_follow'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempoints_twitter_follow', $social_fixed_reward_points_for_twitter_follow);
            $social_percentage_reward_points_for_twitter_follow = $_POST['_socialrewardsystempercent_twitter_follow'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempercent_twitter_follow', $social_percentage_reward_points_for_twitter_follow);


            /*
             * Saving Social Reward Points for Google+ to prodcut meta
             */
            $social_reward_selection_type_for_googleplus = $_POST['_social_rewardsystem_options_google'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_social_rewardsystem_options_google', $social_reward_selection_type_for_googleplus);
            $social_fixed_reward_points_for_googleplus = $_POST['_socialrewardsystempoints_google'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempoints_google', $social_fixed_reward_points_for_googleplus);
            $social_percentage_reward_points_for_googleplus = $_POST['_socialrewardsystempercent_google'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempercent_google', $social_percentage_reward_points_for_googleplus);

            /*
             * Saving Social Reward Points for VK to prodcut meta
             */
            $social_reward_selection_type_for_vk = $_POST['_social_rewardsystem_options_vk'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_social_rewardsystem_options_vk', $social_reward_selection_type_for_vk);
            $social_fixed_reward_points_for_vk = $_POST['_socialrewardsystempoints_vk'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempoints_vk', $social_fixed_reward_points_for_vk);
            $social_percentage_reward_points_for_vk = $_POST['_socialrewardsystempercent_vk'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempercent_vk', $social_percentage_reward_points_for_vk);
            $social_reward_selection_type_for_instagram = $_POST['_social_rewardsystem_options_instagram'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_social_rewardsystem_options_instagram', $social_reward_selection_type_for_instagram);
            $social_fixed_reward_points_for_instagram = $_POST['_socialrewardsystempoints_instagram'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempoints_instagram', $social_fixed_reward_points_for_instagram);
            $social_percentage_reward_points_for_instagram = $_POST['_socialrewardsystempercent_instagram'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempercent_instagram', $social_percentage_reward_points_for_instagram);

            /*
             * Saving Social Reward Points for OK.ru to prodcut meta
             */
            $social_reward_selection_type_for_ok_follow = $_POST['_social_rewardsystem_options_ok_follow'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_social_rewardsystem_options_ok_follow', $social_reward_selection_type_for_ok_follow);
            $social_fixed_reward_points_for_ok_follow = $_POST['_socialrewardsystempoints_ok_follow'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempoints_ok_follow', $social_fixed_reward_points_for_ok_follow);
            $social_percentage_reward_points_for_ok_follow = $_POST['_socialrewardsystempercent_ok_follow'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_socialrewardsystempercent_ok_follow', $social_percentage_reward_points_for_ok_follow);
        }

        /* Save Admin Field for Buying Reward Points */

        public static function save_admin_field_for_point_price_setting($post_id) {
            $woocommerce_rewardpoints_enable = $_POST['_rewardsystem_enable_point_price'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_rewardsystem_enable_point_price', $woocommerce_rewardpoints_enable);
            $woocommerce_points_reward_select = $_POST['_rewardsystem__points'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_rewardsystem__points', $woocommerce_points_reward_select);
            $woocommerce_points_reward_type_select = $_POST['_rewardsystem_point_price_type'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_rewardsystem_point_price_type', $woocommerce_points_reward_type_select);
            $woocommerce_rewardpoints_enable_type = $_POST['_rewardsystem_enable_point_price_type'];
            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_rewardsystem_enable_point_price_type', $woocommerce_rewardpoints_enable_type);

            $woocommerce_points_based_on_conversion = $_POST['_sale_price'];
            if ($woocommerce_points_based_on_conversion == '') {
                $woocommerce_points_based_on_conversion = $_POST['_regular_price'];
            }
            //this line has to be checked
            $newvalue = $woocommerce_points_based_on_conversion * wc_format_decimal(get_option('rs_redeem_point'));
            $points_based_on_conversion = $newvalue / wc_format_decimal(get_option('rs_redeem_point_value'));

            RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($post_id, '_rewardsystem__points_based_on_conversion', $points_based_on_conversion);
        }

    }

    RSSimpleProduct::init();
}