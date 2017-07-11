<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSBookingCompatibility')) {

    class RSBookingCompatibility {

        public static function init() {
//        add_action('wp_head', array(__CLASS__, 'booking_compatible'));
            add_action('wp_ajax_woocommerce_booking_sumo_reward_system', array(__CLASS__, 'sumo_compatible_with_booking'));
            add_shortcode('sumobookingpoints', array(__CLASS__, 'sumo_fixed_points_compatible_with_booking'));
            add_action('woocommerce_before_single_product', array(__CLASS__, 'add_woocommerce_notice'));
            add_action('woocommerce_before_cart', array(__CLASS__, 'reward_points_in_top_of_content'));
            add_action('woocommerce_before_checkout_form', array(__CLASS__, 'reward_points_in_top_of_content'));
            add_shortcode('bookingrspoint', array(__CLASS__, 'get_each_product_price'));
            add_action('woocommerce_before_cart', array(__CLASS__, 'rewardmessage_in_cart'));
            add_action('woocommerce_before_checkout_form', array(__CLASS__, 'rewardmessage_in_cart'));
            if (class_exists('WC_Bookings')) {
                add_filter('woocommerce_rewardsystem_message_settings', array(__CLASS__, 'add_custom_field_to_message_tab'));
            }
            add_shortcode('bookingproducttitle', array(__CLASS__, 'get_woocommerce_booking_product_title'));
            add_shortcode('equalbookingamount', array(__CLASS__, 'get_each_product_points_value_in_cart_for_booking'));
            include_once('rs_update_booking_points.php');
            add_filter('woocommerce_available_payment_gateways', array(__CLASS__, 'remove_payment_methods'));
        }

        public static function remove_payment_methods($available_gateways) {

            if (class_exists('WC_Bookings')) {
                foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                    if ($gateway->id == 'reward_gateway' && $gateway->enabled == 'yes') {
                        $available_gateways[$gateway->id] = $gateway;
                    }
                }
            }
            return $available_gateways != 'NULL' ? $available_gateways : array();
        }

        public static function booking_compatible() {
            if (class_exists('WC_Bookings')) {
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        var xhr;
                        jQuery('.woocommerce_booking_variations').hide();
                        jQuery('#wc-bookings-booking-form').on('change', 'input, select', function () {
                            if (xhr)
                                xhr.abort();
                            var form = jQuery(this).closest('form');
                            var dataparam = ({
                                action: 'woocommerce_booking_sumo_reward_system',
                                form: form.serialize(),
                            });
                            jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam, function (response) {

                                if ((response.sumorewardpoints !== 0) && (response.sumorewardpoints !== '')) {
                                    jQuery('.woocommerce_booking_variations').addClass('woocommerce-info');
                                    jQuery('.woocommerce_booking_variations').show();
                                    jQuery('.sumobookingpoints').html(response.sumorewardpoints);
                                } else {
                                    jQuery('.woocommerce_booking_variations').hide();
                                }
                            }, 'json');
                        });
                    });
                </script>

                <?php
            }
        }

        public static function sumo_fixed_points_compatible_with_booking() {
            global $post;
            $booking_id = $post->ID;
            $getproducttype = rs_get_product_object($booking_id);
            $global_enable = get_option('rs_global_enable_disable_sumo_reward');
            $global_reward_type = get_option('rs_global_reward_type');
            if (is_object($getproducttype) && $getproducttype->is_type('booking')) {
                $checklevel = 'no';
                $value = array('qty' => '1');
                $points = check_level_of_enable_reward_point($post->ID, $post->ID, $value, $checklevel, $referred_user = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                $reward_points = round($points, $roundofftype);
                return $reward_points;
            }
        }

        public static function sumo_compatible_with_booking() {
            $posted = array();
            parse_str($_POST['form'], $posted);
            $booking_id = $posted['add-to-cart'];
            $product = rs_get_product_object($booking_id);
            if (!$product) {
                die(json_encode(array('sumorewardpoints' => 0)));
            }
            $booking_form = new WC_Booking_Form($product);
            $cost = $booking_form->calculate_booking_cost($posted);
            $args = array('qty' => 1,'price' => $cost);
            if (is_wp_error($cost)) {
                die(json_encode(array('sumorewardpoints' => 0)));
            }
            $tax_display_mode = get_option('woocommerce_tax_display_shop');
            if (function_exists('wc_get_price_including_tax')) {
                $price_to_display_inc = wc_get_price_including_tax($product, $args);
            } else {
                $price_to_display_inc = $product->get_price_including_tax(1, $cost);
            }
            if (function_exists('wc_get_price_excluding_tax')) {
                $price_to_display_exc = wc_get_price_excluding_tax($product, $args);
            } else {
                $price_to_display_exc = $product->get_price_excluding_tax(1, $cost);
            }
            $display_price = $tax_display_mode == 'incl' ? $price_to_display_inc : $price_to_display_exc;
            global $post;
            $checkproducttype = rs_get_product_object($booking_id);
            if (is_object($checkproducttype) && $checkproducttype->is_type('booking')) {
                $checklevel = 'no';
                $value = array('qty' => '1');
                $getpoints = check_level_of_enable_reward_point($booking_id, $booking_id, $value, $checklevel, $referred_user = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                $finalpoints = round($getpoints, $roundofftype);
                die(json_encode(array('sumorewardpoints' => $finalpoints, 'booking_price' => $display_price, 'booking_formatted_price' => get_woocommerce_formatted_price($display_price) . $product->get_price_suffix())));
            }
        }

        public static function add_woocommerce_notice() {
            global $post;
            $order = '';
            if (is_user_logged_in()) {
                $userid = get_current_user_id();
                $banning_type = FPRewardSystem::check_banning_type($userid);
                if ($banning_type != 'earningonly' && $banning_type != 'both') {
                    $checkproducttype = rs_get_product_object($post->ID);
                    if (is_object($checkproducttype) && $checkproducttype->is_type('booking')) {
                        if (get_post_meta($post->ID, '_rewardsystemcheckboxvalue', true) == 'yes') {
                            if (get_post_meta($post->ID, '_rewardsystem_options', true) == '1') {
                                $rewardpoints = do_shortcode('[sumobookingpoints]');
                                if ($rewardpoints > 0) {
                                    ?>
                                    <div class="woocommerce-info"><?php _e("Book this Product and Earn <span class='sumobookingpoints'>$rewardpoints</span> Points"); ?></div>
                                    <?php
                                }
                            } else {
                                ?>
                                <div class="woocommerce_booking_variations"><?php _e("Book this Product and Earn <span class='sumobookingpoints'></span> Points"); ?></div>
                                <?php
                            }
                        }
                    }
                }
            } else {
                $checkproducttype = rs_get_product_object($post->ID);
                if (is_object($checkproducttype) && $checkproducttype->is_type('booking')) {
                    if (get_post_meta($post->ID, '_rewardsystemcheckboxvalue', true) == 'yes') {
                        if (get_post_meta($post->ID, '_rewardsystem_options', true) == '1') {
                            $rewardpoints = do_shortcode('[sumobookingpoints]');
                            if ($rewardpoints > 0) {
                                ?>
                                <div class="woocommerce-info"><?php _e("Book this Product and Earn <span class='sumobookingpoints'>$rewardpoints</span> Points"); ?></div>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="woocommerce_booking_variations"><?php _e("Book this Product and Earn <span class='sumobookingpoints'></span> Points"); ?></div>
                            <?php
                        }
                    }
                }
            }
        }

        public static function reward_points_in_top_of_content() {
            global $checkproduct;
            if (is_user_logged_in()) {
                $userid = get_current_user_id();
                $banning_type = FPRewardSystem::check_banning_type($userid);
                if ($banning_type != 'earningonly' && $banning_type != 'both') {
                    global $messageglobalbooking;
                    global $totalrewardpointsnewbooking;
                    global $totalrewardpoints;
                    $totalrewardpoints;
                    global $woocommerce;
                    global $bookingvalue;
                    foreach ($woocommerce->cart->cart_contents as $key => $bookingvalue) {
                        $checkproduct = rs_get_product_object($bookingvalue['product_id']);
                        if (is_object($checkproduct) && $checkproduct->is_type('booking')) {
                            $checklevel = 'no';
                            $points = check_level_of_enable_reward_point($bookingvalue['product_id'], $bookingvalue['product_id'], $bookingvalue, $checklevel, $referred_user = '', $getting_referrer = 'no', $socialreward = 'no', $rewardfor = '');
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $reward_points = round($points, $roundofftype);
                            $totalrewardpointsnewbooking[$bookingvalue['product_id']] = $reward_points;
                        }
                        if (is_object($checkproduct) && $checkproduct->is_type('booking')) {
                            if ($checkenable == 'yes') {
                                $validrewardpoints = do_shortcode('[bookingrspoint]');
                                if ($validrewardpoints > 0) {
                                    if (is_cart()) {
                                        $messageglobalbooking[] = do_shortcode(get_option('rs_woocommerce_booking_product_cart_message')) . "<br>";
                                    }
                                    if (is_checkout()) {
                                        $messageglobalbooking[] = do_shortcode(get_option('rs_woocommerce_booking_product_checkout_message')) . "<br>";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        public static function get_each_product_price() {
            global $totalrewardpoints;
            global $checkproduct;
            global $bookingvalue;
            if (is_object($checkproduct) && $checkproduct->is_type('booking')) {
                if (get_post_meta($bookingvalue['product_id'], '_rewardsystemcheckboxvalue', true) != 'yes') {
                    return "<strong>0</strong>";
                } else {
                    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                    return round($totalrewardpoints, $roundofftype);
                }
            }
        }

        public static function get_each_product_points_value_in_cart_for_booking() {
            $getpoints = do_shortcode('[bookingrspoint]');
            $redeemconver = $getpoints / wc_format_decimal(get_option('rs_redeem_point'));
            $updatedvalue = $redeemconver * wc_format_decimal(get_option('rs_redeem_point_value'));
            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
            return get_woocommerce_formatted_price(round($updatedvalue, $roundofftype));
        }

        public static function rewardmessage_in_cart() {
            global $woocommerce;
            global $bookingvalue;
            global $totalrewardpointsnewbooking;
            global $messageglobalbooking;
            if (get_option('rs_show_hide_message_for_each_products') == '1') {
                if (is_array($totalrewardpointsnewbooking)) {
                    if (array_sum($totalrewardpointsnewbooking) > 0) {
                        ?>
                        <div class="woocommerce-info">
                            <?php
                            if (is_array($messageglobalbooking)) {
                                foreach ($messageglobalbooking as $globalcommerce) {
                                    echo $globalcommerce;
                                }
                            }
                            ?>
                        </div>
                        <?php
                    }
                }
            }
        }

        public static function get_data_from_cart_item() {
            global $woocommerce;
            $cart_item = $woocommerce->cart->cart_contents;
            if (class_exists('WC_Bookings_Cart')) {
                ?>
                <div class="woocommerce-info">
                    <?php
                    foreach ($cart_item as $bookingvalue) {
                        
                    }
                    ?>

                </div>
                <?php
            }
        }

        public static function add_custom_field_to_message_tab($settings) {
            $updated_settings = array();
            foreach ($settings as $section) {
                if (isset($section['id']) && '_rs_reward_messages' == $section['id'] &&
                        isset($section['type']) && 'sectionend' == $section['type']) {
                    $updated_settings[] = array(
                        'name' => __('Message in Cart Page for each WooCommerce Booking Product', 'rewardsystem'),
                        'desc' => __('Enter the Message which will be displayed in each WooCommerce Booking Products added in the Cart', 'rewardsystem'),
                        'tip' => '',
                        'id' => 'rs_woocommerce_booking_product_cart_message',
                        'css' => 'min-width:550px;',
                        'std' => 'Purchase this [bookingproducttitle] and Earn <strong>[bookingrspoint]</strong> Reward Point ([equalbookingamount])',
                        'type' => 'textarea',
                        'newids' => 'rs_woocommerce_booking_product_cart_message',
                        'desc_tip' => true,
                    );
                    $updated_settings[] = array(
                        'name' => __('Message in Checkout Page for each WooCommerce Booking Product', 'rewardsystem'),
                        'desc' => __('Enter the Message which will be displayed in each WooCommerce Booking Products in the Checkout', 'rewardsystem'),
                        'tip' => '',
                        'id' => 'rs_woocommerce_booking_product_checkout_message',
                        'css' => 'min-width:550px;',
                        'std' => 'Purchase this [bookingproducttitle] and Earn <strong>[bookingrspoint]</strong> Reward Point ([equalbookingamount])',
                        'type' => 'textarea',
                        'newids' => 'rs_woocommerce_booking_product_checkout_message',
                        'desc_tip' => true,
                    );
                }
                $updated_settings[] = $section;
            }

            return $updated_settings;
        }

        public static function get_woocommerce_booking_product_title() {
            global $checkproduct;
            global $bookingvalue;
            if (is_object($checkproduct) && $checkproduct->is_type('booking')) {
                return "<strong>" . get_the_title($bookingvalue['product_id']) . "</strong>";
            }
        }

    }

    RSBookingCompatibility::init();
}