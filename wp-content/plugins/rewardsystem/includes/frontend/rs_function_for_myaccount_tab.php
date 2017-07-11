<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('RSFunctionForMyAccount')) {

    class RSFunctionForMyAccount {

        public static function init() {

            add_action('wp_head', array(__CLASS__, 'check_cokkies_for_referal'));

            if (get_option('rs_display_generate_referral') == '1') {

                if (get_option('rs_show_hide_generate_referral') == '1') {

                    if (get_option('rs_show_hide_generate_referral_link_type') == '1') {

                        add_action('woocommerce_before_my_account', array(__CLASS__, 'function_for_referal_link'));
                    } else {
                        add_action('woocommerce_before_my_account', array(__CLASS__, 'static_referral_function'));
                    }
                }
            }

            add_action('wp_ajax_nopriv_unset_referral', array(__CLASS__, 'unset_array_referral_key'));

            add_action('wp_ajax_unset_referral', array(__CLASS__, 'unset_array_referral_key'));

            add_action('wp_ajax_nopriv_ajaxify_referral', array(__CLASS__, 'ajaxify_referral_key'));

            add_action('wp_ajax_ajaxify_referral', array(__CLASS__, 'ajaxify_referral_key'));

            add_action('woocommerce_checkout_update_order_meta', array(__CLASS__, 'checkout_cookies_referral_meta'), 10, 2);

            if (get_option('rs_show_hide_nominee_field') == '1') {

                add_action('woocommerce_after_my_account', array(__CLASS__, 'display_nominee_field_in_my_account'));
            }
            add_shortcode('rs_nominee_table', array(__CLASS__, 'display_nominee_field_in_my_account'));

            add_action('wp_ajax_nopriv_rs_save_nominee', array(__CLASS__, 'save_selected_nominee'));

            add_action('wp_ajax_rs_save_nominee', array(__CLASS__, 'save_selected_nominee'));

            if (get_option('rs_my_cashback_table') == '1') {

                add_action('woocommerce_after_my_account', array(__CLASS__, 'view_cash_back_table_in_myaccount'));
            }

            add_shortcode('rs_my_cashback_log', array(__CLASS__, 'view_cash_back_table_in_myaccount_shortcode'));

            add_action('woocommerce_before_my_account', array(__CLASS__, 'view_list_referal_table'));

            if (get_option('rs_my_reward_table') == '1') {
                if (get_option('rs_reward_table_position') == '1') {
                    add_action('woocommerce_after_my_account', array(__CLASS__, 'view_list_table_in_myaccount'));
                } else {
                    add_action('woocommerce_before_my_account', array(__CLASS__, 'view_list_table_in_myaccount'));
                }
            }
            if (get_option('rs_display_generate_referral') == '2') {
                if (get_option('rs_show_hide_generate_referral') == '1') {

                    if (get_option('rs_show_hide_generate_referral_link_type') == '1') {

                        add_action('woocommerce_after_my_account', array(__CLASS__, 'generate_referral_key'));

                        add_action('woocommerce_after_my_account', array(__CLASS__, 'list_table_array'));
                    } else {
                        add_action('woocommerce_after_my_account', array(__CLASS__, 'function_to_display_static_url'));
                    }
                }
            }
            add_shortcode('rs_my_rewards_log', array(__CLASS__, 'viewchangelog_shortcode'));

            add_shortcode('rs_user_total_redeemed_points', array(__CLASS__, 'add_shortcode_to_display_total_redeem_points'));

            add_shortcode('rs_user_total_earned_points', array(__CLASS__, 'add_shortcode_to_display_total_earned_points'));

            add_shortcode('rs_user_total_expired_points', array(__CLASS__, 'add_shortcode_to_display_total_expired_points'));

            add_action('wp_ajax_nopriv_cancel_request_for_cash_back', array(__CLASS__, 'ajax_request_for_cash_back'));

            add_action('wp_ajax_cancel_request_for_cash_back', array(__CLASS__, 'ajax_request_for_cash_back'));

            if (get_option('rs_show_hide_generate_referral_message') == '1') {

                 add_action('wp', array(__CLASS__, 'rs_show_referrer_name_in_home_page'));
                //add_filter('template_include', array(__CLASS__, 'rs_show_referrer_name_in_home_page'), 10, 1);
            }
            add_shortcode('rs_referred_user_name', array(__CLASS__, 'addshort_code_username'));

            add_shortcode('rs_view_referral_table', array(__CLASS__, 'viewreferraltable_shortcode'));

            add_shortcode('rs_user_total_points_in_value', array(__CLASS__, 'add_shortcode_to_display_user_total_points_in_value'));

            add_shortcode('rs_rank_based_total_earned_points', array(__CLASS__, 'view_total_user_points'));

            add_shortcode('rs_rank_based_current_reward_points', array(__CLASS__, 'view_total_current_user_points'));
        }

        public static function function_for_referal_link() {
            $get_user_type = get_option('rs_select_type_of_user_for_referral');
            $check_user_restriction = rs_function_to_check_the_restriction_for_referral($get_user_type);
            if ($check_user_restriction) {
                self::generate_referral_key();
                self::list_table_array();
            }
        }

        public static function static_referral_function() {
            $get_user_type = get_option('rs_select_type_of_user_for_referral');
            $check_user_restriction = rs_function_to_check_the_restriction_for_referral($get_user_type);
            if ($check_user_restriction) {
                self::function_to_display_static_url();
            }
        }

        public static function view_total_current_user_points() {
            if (is_user_logged_in()) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'rspointexpiry';
                $table_name2 = $wpdb->prefix . 'rsrecordpoints';
                $outputtablefields = '<p> ';
                $outputtablefields .= __('Page Size:', 'rewardsystem') . '<select id="change-page-sizesss"><option value="5">5</option><option value="10">10</option><option value="50">50</option>
                    <option value="100">100</option>
                </select>';
                $outputtablefields .= '</p>';
                echo $outputtablefields;
                ?>
                <table class = "totaluser_current demo shop_table my_account_orders table-bordered" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next">
                    <thead><tr><th ><?php echo get_option('rs_my_rewards_sno_label'); ?></th>                   
                            <th data-sortable="false" ><?php echo get_option('rs_my_rewards_userid_label'); ?></th>                  
                            <th data-type="numeric" data-sort-initial="true"><?php echo get_option('rs_my_rewards_points_earned_label'); ?></th>

                    <tbody>
                        <?php
                        $getusermeta = $wpdb->get_results("SELECT userid ,(earnedpoints-usedpoints) as availablepoints FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) ORDER BY availablepoints DESC", ARRAY_A);
                        $i = 1;
                        foreach ($getusermeta as $user) {
                            $author_obj = get_user_by('id', $user['userid']);
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $points = round($user['availablepoints'], $roundofftype)
                            ?>
                            <tr>
                                <td data-value="<?php echo $i; ?>"><?php echo $i; ?></td>                                     
                                <td><?php echo is_object($author_obj) ? $author_obj->user_login : 'Guest'; ?> </td>                                     
                                <td><?php echo $points; ?></td>
                            </tr>
                            <?php
                            $i++;
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="clear:both;">
                            <td colspan="3">
                                <div class="pagination pagination-centered"></div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <?php ?>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        jQuery('.totaluser_current').footable();
                        jQuery('#change-page-sizesss').change(function (e) {
                            e.preventDefault();
                            var pageSize = jQuery(this).val();
                            jQuery('.footable').data('page-size', pageSize);
                            jQuery('.footable').trigger('footable_initialized');
                        });

                    });</script>
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

        public static function view_total_user_points() {
            if (is_user_logged_in()) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'rspointexpiry';
                $table_name2 = $wpdb->prefix . 'rsrecordpoints';
                $outputtablefieldss = '<p> ';
                $outputtablefieldss .= __('Page Size:', 'rewardsystem') . '<select id="change-page-sizess"><option value="5">5</option><option value="10">10</option><option value="50">50</option>
                    <option value="100">100</option>
                </select>';

                $outputtablefieldss .= '</p>';
                echo $outputtablefieldss;
                ?>

                <table class = "totaluser demo shop_table my_account_orders table-bordered" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next">
                    <thead><tr><th ><?php echo get_option('rs_my_rewards_sno_label'); ?></th>                   
                            <th data-sortable="false"><?php echo get_option('rs_my_rewards_userid_label'); ?></th>                  
                            <th data-type="numeric" data-sort-initial="true"><?php echo get_option('rs_my_rewards_points_earned_label'); ?></th>

                    <tbody>
                        <?php
                        $getusermeta = $wpdb->get_results("SELECT userid ,earnedpoints  FROM $table_name WHERE earnedpoints NOT IN(0) and expiredpoints IN(0) ORDER BY earnedpoints DESC ", ARRAY_A);
                        $i = 1;
                        foreach ($getusermeta as $user) {
                            $author_obj = get_user_by('id', $user['userid']);
                            $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                            $points = round($user['earnedpoints'], $roundofftype)
                            ?>
                            <tr>
                                <td data-value="<?php echo $i; ?>"><?php echo $i; ?></td>                                     
                                <td><?php echo is_object($author_obj) ? $author_obj->user_login : 'Guest'; ?> </td>                                     
                                <td><?php echo $points; ?></td>
                            </tr>
                            <?php
                            $i++;
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="clear:both;">
                            <td colspan="3">
                                <div class="pagination pagination-centered"></div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <?php ?>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        jQuery('.totaluser').footable();
                        jQuery('#change-page-sizess').change(function (e) {
                            e.preventDefault();
                            var pageSize = jQuery(this).val();
                            jQuery('.footable').data('page-size', pageSize);
                            jQuery('.footable').trigger('footable_initialized');
                        });

                    });</script>
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

        public static function add_shortcode_to_display_user_total_points_in_value() {
            if (is_user_logged_in()) {
                $getcurrentuserid = get_current_user_id();
                echo '<b>' . display_total_currency_value($getcurrentuserid) . '</b>';
            } else {
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                $linkforlogin = add_query_arg('redirect_to', get_permalink(), $myaccountlink);
                $message = get_option('rs_message_shortcode_guest_display');
                $login = get_option('rs_message_shortcode_login_name');
                echo '<br>' . $message . ' <a href=' . $linkforlogin . '> ' . $login . '</a>';
            }
        }

        /* Function to display the input field and button for Generate Referral Link */

        public static function generate_referral_key() {
            if (is_user_logged_in()) {
                ?>
                <div class="referral_field1" style="margin-top:10px;">
                    <input type="text" size="50" name="generate_referral_field" id="generate_referral_field" required="required" value="<?php echo get_option('rs_prefill_generate_link'); ?>"><input type="submit" style="margin-left:10px;" class="button <?php echo get_option('rs_extra_class_name_generate_referral_link'); ?>" name="refgeneratenow" id="refgeneratenow" value="<?php echo get_option('rs_generate_link_button_label'); ?>"/>
                </div>                
                <?php
            } else {
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                echo 'Please Login to View the Content of  this Page <a href=' . $myaccountlink . '> Login </a>';
            }
        }

        public static function rs_script_to_generate_referral_link() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('#refgeneratenow').click(function () {
                        var referral_generate = jQuery('#generate_referral_field').val();
                        if (referral_generate === '') {
                            jQuery('#generate_referral_field').css('outline', 'red solid');
                            return false;
                        } else {
                            jQuery('#generate_referral_field').css('outline', '');
                            var urlstring = jQuery('#generate_referral_field').val();
                            var dataparam = ({
                                action: 'ajaxify_referral',
                                url: urlstring,
                                userid: '<?php echo get_current_user_id(); ?>',
                            });
                            jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                    function (response) {
                                        jQuery(".my_account_referral_link").load(window.location + " .my_account_referral_link");
                                        jQuery(document).ajaxComplete(function () {
                                            try {
                                                twttr.widgets.load();
                                                FB.XFBML.parse();
                                                gapi.plusone.go();
                                            } catch (ex) {
                                            }


                                            jQuery('.referralclick').click(function () {
                                                var getarraykey = jQuery(this).attr('data-array');
                                                jQuery(this).parent().parent().hide();
                                                var dataparam = ({
                                                    action: 'unset_referral',
                                                    unsetarray: getarraykey,
                                                    userid: '<?php echo get_current_user_id(); ?>',
                                                });
                                                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                                        function (response) {
                                                            var newresponse = response.replace(/\s/g, '');
                                                            if (newresponse === "success") {

                                                            }
                                                        });
                                                return false;
                                            });
                                        });
                                        if (response === "success") {
                                            location.reload();
                                        }
                                    });
                            return false;
                        }
                    });

                    jQuery('.referralclick').click(function () {
                        var getarraykey = jQuery(this).attr('data-array');
                        console.log(jQuery(this).parent().parent().hide());
                        var dataparam = ({
                            action: 'unset_referral',
                            unsetarray: getarraykey,
                            userid: '<?php echo get_current_user_id(); ?>'
                        });
                        jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                function (response) {
                                    var newresponse = response.replace(/\s/g, '');
                                    if (newresponse === "success") {

                                    }
                                });
                        return false;
                    });
                });
            </script>
            <?php
        }

        /* Function to display the List Table for Generated Referral Link */

        public static function list_table_array() {
            if (is_user_logged_in()) {
                ?>
                <style type="text/css">
                    .referralclick {
                        border: 2px solid #a1a1a1;
                        padding: 3px 9px;
                        background: #dddddd;
                        width: 5px;
                        border-radius: 25px;
                    }
                    .referralclick:hover {
                        cursor: pointer;
                        background:red;
                        color:#fff;
                        border: 2px solid #fff;
                    }
                </style>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        jQuery('.referrals .footable-toggle').click(function () {
                            gapi.plusone.go();
                            jQuery('.rs_social_buttons .fb-share-button span').css("width", "60px");
                            jQuery('.rs_social_buttons .fb-share-button span iframe').css({"width": "59px", "height": "29px", "visibility": "visible"});
                            jQuery('.rs_social_buttons iframe.twitter-share-button').css({"width": "59px", "height": "29px", "visibility": "visible"});
                        });
                        jQuery('.referrals').click(function () {
                            gapi.plusone.go();
                            jQuery('.rs_social_buttons .fb-share-button span').css("width", "60px");
                            jQuery('.rs_social_buttons .fb-share-button span iframe').css({"width": "59px", "height": "29px", "visibility": "visible"});
                            jQuery('.rs_social_buttons iframe.twitter-share-button').css({"width": "59px", "height": "29px", "visibility": "visible"});
                        });
                    });
                </script>
                <div id="fb-root"></div>
                <script>(function (d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id))
                            return;
                        js = d.createElement(s);
                        js.id = id;
                <?php if ((get_option('rs_language_selection_for_button') == 1)) { ?>
                            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
                    <?php
                } else {
                    if (get_option('WPLANG') == '') {
                        ?>
                                js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
                    <?php } else { ?>
                                js.src = "//connect.facebook.net/<?php echo get_option('WPLANG'); ?>/sdk.js#xfbml=1&version=v2.0";
                    <?php } ?>
                <?php } ?>
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));</script>
                <script>!function (d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                        if (!d.getElementById(id)) {
                            js = d.createElement(s);
                            js.id = id;
                            js.src = p + '://platform.twitter.com/widgets.js';
                            fjs.parentNode.insertBefore(js, fjs);
                        }
                    }(document, 'script', 'twitter-wjs');</script>

                <!-- Place this tag where you want the share button to render. -->


                <!-- Place this tag after the last share tag. -->
                <script>
                    window.___gcfg = {
                        lang: '<?php echo get_option('WPLANG') == '' ? 'en_US' : get_option('WPLANG'); ?>',
                        parsetags: 'onload'
                    }
                </script>                
                <script type="text/javascript" src="https://apis.google.com/js/plusone.js">
                    {
                        parsetags: 'explicit'
                    }
                </script>
                <h3><?php echo get_option('rs_generate_link_label'); ?></h3>
                <table class="referral_link shop_table my_account_referral_link" id="my_account_referral_link">
                    <thead>
                        <tr>
                            <th class="referral-number"><span class="nobr"><?php echo get_option('rs_generate_link_sno_label'); ?></span></th>
                            <th class="referral-date"><span class="nobr"><?php echo get_option('rs_generate_link_date_label'); ?></span></th>
                            <th class="referral-link"><span class="nobr"><?php echo get_option('rs_generate_link_referrallink_label'); ?></span></th>
                            <th data-hide='phone,tablet' class="referral-social"><span class="nobr"><?php echo get_option('rs_generate_link_social_label'); ?></span></th>
                            <th data-hide='phone,tablet' class="referral-actions"><span class="nobr"><?php echo get_option('rs_generate_link_action_label'); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $currentuserid = get_current_user_id();
                        if (is_array(get_option('arrayref' . $currentuserid))) {
                            $i = 1;
                            $j = 0;
                            foreach (get_option('arrayref' . $currentuserid) as $array => $key) {
                                $mainkey = explode(',', $key);
                                ?>
                                <tr class="referrals" data-url="<?php echo $mainkey[0]; ?>">
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $mainkey[1]; ?></td>
                                    <td><?php echo $mainkey[0]; ?></td>
                                    <td>
                                        <div class="rs_social_buttons">      
                                            <?php if (get_option('rs_account_show_hide_facebook_like_button') == '1') { ?>
                                                <div class="share_wrapper" id="share_wrapper" href="<?php echo $mainkey[0]; ?>" data-image="<?php echo get_option('rs_fbshare_image_url_upload') ?>" data-title="<?php echo get_option('rs_facebook_title') ?>" data-description="<?php echo get_option('rs_facebook_description') ?>">
                                                    <img class='fb_share_img' src="<?php echo REWARDSYSTEM_PLUGIN_DIR_URL; ?>/admin/images/icon1.png"> <span class="label"><?php echo get_option('rs_fbshare_button_label'); ?> </span>
                                                </div> 
                                            <?php } ?>
                                            <?php if (get_option('rs_account_show_hide_twitter_tweet_button') == '1') { ?>
                                                <a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-url="<?php echo $mainkey[0]; ?>">Tweet</a>
                                            <?php } ?><br>

                                            <?php if (get_option('rs_acount_show_hide_google_plus_button') == '1') { ?>
                                                <div class="g-plusone" data-action="share" data-annotation="none" data-href="<?php echo $mainkey[0]; ?>"><g:plusone></g:plusone></div>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td><span data-array="<?php echo $array; ?>" class="referralclick">x</span></td>
                                </tr>
                            <style>
                                .share_wrapper{
                                    margin-top: -12px;
                                    background-color:#3b5998;
                                    /*padding:2px;*/
                                    color:#fff;
                                    cursor:pointer;
                                    font-size:12px;
                                    font-weight:bold;
                                    border: 1px solid transparent;
                                    border-radius: 2px ;
                                    width:auto;
                                    height:23px;
                                }
                                .fb_share_img{
                                    margin-top: -3px;
                                    margin-left: 3px;
                                    margin-right: 3px;
                                }
                            </style>
                            <?php
                            $i++;
                            $j++;
                        }
                    }
                    ?>
                </tbody>
                </table>
                <?php
            }
        }

        public static function function_to_display_static_url() {
            if (is_user_logged_in()) {
                $currentuserid = get_current_user_id();
                $objectcurrentuser = get_userdata($currentuserid);
                if (get_option('rs_generate_referral_link_based_on_user') == '1') {
                    $referralperson = is_object($objectcurrentuser) ? $objectcurrentuser->user_login : 'Guest';
                } else {
                    $referralperson = $currentuserid;
                }
                if (get_option('rs_show_hide_generate_referral_link_type') == '2') {
                    $refurl = add_query_arg('ref', $referralperson, get_option('rs_static_generate_link'));
                    ?>              
                    <script>!function (d, s, id) {
                            var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                            if (!d.getElementById(id)) {
                                js = d.createElement(s);
                                js.id = id;
                                js.src = p + '://platform.twitter.com/widgets.js';
                                fjs.parentNode.insertBefore(js, fjs);
                            }
                        }(document, 'script', 'twitter-wjs');</script>

                    <!-- Place this tag where you want the share button to render. -->


                    <!-- Place this tag after the last share tag. -->
                    <script>
                        window.___gcfg = {
                            lang: '<?php echo get_option('WPLANG') == '' ? 'en_US' : get_option('WPLANG'); ?>',
                            parsetags: 'onload'
                        }
                    </script>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            gapi.plusone.go();
                        });
                    </script>
                    <script type="text/javascript" src="https://apis.google.com/js/plusone.js">
                        {
                            parsetags: 'explicit'
                        }
                    </script>
                    <h3><?php echo get_option('rs_my_referral_link_button_label'); ?></h3>
                    <table class="shop_table my_account_referral_link_static" id="my_account_referral_link_static">
                        <thead>
                            <tr>
                                <th class="referral-number_static"><span class="nobr"><?php echo get_option('rs_generate_link_sno_label'); ?></span></th>                        
                                <th class="referral-link_static"><span class="nobr"><?php echo get_option('rs_generate_link_referrallink_label'); ?></span></th>
                                <th  class="referral-social_static"><span class="nobr"><?php echo get_option('rs_generate_link_social_label'); ?></span></th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $j = 0;
                            ?>
                            <tr class="referrals_static">
                                <td><?php echo 1; ?></td>

                                <td><?php
                                    echo $refurl;
                                    ?></td>
                                <td>
                                    <?php if (get_option('rs_account_show_hide_facebook_like_button') == '1') { ?>
                                        <div class="share_wrapper" id="share_wrapper" href="<?php echo $refurl; ?>" data-image="<?php echo get_option('rs_fbshare_image_url_upload') ?>" data-title="<?php echo get_option('rs_facebook_title') ?>" data-description="<?php echo get_option('rs_facebook_description') ?>">
                                            <img class='fb_share_img' src="<?php echo REWARDSYSTEM_PLUGIN_DIR_URL; ?>/admin/images/icon1.png"> <span class="label"><?php echo get_option('rs_fbshare_button_label'); ?> </span>
                                        </div>
                                    <?php } ?>
                                    <?php if (get_option('rs_account_show_hide_twitter_tweet_button') == '1') { ?>
                                        <a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-url="<?php echo $refurl; ?>">Tweet</a>
                                    <?php } ?><br>
                                    <?php if (get_option('rs_acount_show_hide_google_plus_button') == '1') { ?>
                                        <div class="g-plusone" data-action="share" data-annotation="none" data-href="<?php echo $refurl; ?>"><g:plusone></g:plusone></div>
                                    <?php } ?>
                                </td>
                        <style>
                            .share_wrapper{
                                margin-top: -12px;
                                background-color:#3b5998;
                                /*padding:2px;*/
                                color:#fff;
                                cursor:pointer;
                                font-size:12px;
                                font-weight:bold;
                                border: 1px solid transparent;
                                border-radius: 2px ;
                                width:auto;
                                height:23px;
                            }
                            .fb_share_img{
                                margin-top: -3px;
                                margin-left: 3px;
                                margin-right: 3px;
                            }
                        </style>
                        <script type="text/javascript">
                            jQuery(document).ready(function () {
                                window.fbAsyncInit = function () {
                                    FB.init({
                                        appId: "<?php echo get_option('rs_facebook_application_id'); ?>",
                                        xfbml: true,
                                        version: 'v2.6'
                                    });
                                };
                                console.log('loaded script . . . . . ');
                                (function (d, s, id) {
                                    var js, fjs = d.getElementsByTagName(s)[0];
                                    if (d.getElementById(id))
                                        return;
                                    js = d.createElement(s);
                                    js.id = id;
                    <?php if ((get_option('rs_language_selection_for_button') == 1)) { ?>
                                        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
                        <?php
                    } else {
                        if (get_option('WPLANG') == '') {
                            ?>
                                            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
                        <?php } else { ?>
                                            js.src = "//connect.facebook.net/<?php echo get_option('WPLANG'); ?>/sdk.js#xfbml=1&version=v2.0";
                        <?php } ?>
                    <?php } ?>
                                    fjs.parentNode.insertBefore(js, fjs);
                                }(document, 'script', 'facebook-jssdk'));
                                function postToFeed(url, image, description, title) {
                                    var obj = {
                                        method: 'feed',
                                        name: title,
                                        link: url,
                                        picture: image,
                                        description: description
                                    };
                                    function callback(response) {
                                        if (response != null) {
                                            alert('sucessfully posted');
                                        } else {
                                            alert('cancel');
                                        }

                                    }
                                    FB.ui(obj, callback);
                                }
                                jQuery('.share_wrapper').click(function (evt) {
                                    evt.preventDefault();
                                    var a = document.getElementById('share_wrapper')
                                    var url = a.getAttribute('href');
                                    var image = a.getAttribute('data-image');
                                    var title = a.getAttribute('data-title');
                                    var description = a.getAttribute('data-description');
                                    postToFeed(url, image, description, title);
                                    return false;
                                });
                            });</script>
                    </tr>                    
                    </tbody>
                    </table>
                    <?php
                }
            }
        }

        public static function unset_array_referral_key() {
            $currentuserid = $_POST['userid'];
            if (isset($_POST['unsetarray'])) {
                $listarray = get_option('arrayref' . $currentuserid);
                unset($listarray[$_POST['unsetarray']]);
                update_option('arrayref' . $currentuserid, $listarray);
                echo "success";
            }
            exit();
        }

        public static function ajaxify_referral_key() {
            $currentuserid = $_POST['userid'];
            $objectcurrentuser = get_userdata($currentuserid);
            if (get_option('rs_generate_referral_link_based_on_user') == '1') {
                $referralperson = is_object($objectcurrentuser) ? $objectcurrentuser->user_login : 'Guest';
            } else {
                $referralperson = $currentuserid;
            }

            if (isset($_POST['url'])) {
                $refurl = add_query_arg('ref', $referralperson, $_POST['url']);
                $previousref = get_option('arrayref' . $currentuserid);
                $dateformat = get_option('date_format');
                $arrayref[] = $refurl . ',' . date_i18n($dateformat);
                if (is_array($previousref)) {
                    $arrayref = array_unique(array_merge($previousref, $arrayref), SORT_REGULAR);
                }
                update_option('arrayref' . $currentuserid, $arrayref);
                echo "success";
            }
            exit();
        }

        public static function check_cokkies_for_referal() {
            $get_user_type = get_option('rs_select_type_of_user_for_referral');
            $check_user_restriction = rs_function_to_check_the_restriction_for_referral($get_user_type);
            if ($check_user_restriction) {
                self::count_statistics_referral();
            }
        }

        public static function count_statistics_referral() {
            if (isset($_GET['ref']) && !is_user_logged_in()) {
                if (get_option('rs_referral_cookies_expiry') == '1') {
                    $min = get_option('rs_referral_cookies_expiry_in_min') == '' ? '1' : get_option('rs_referral_cookies_expiry_in_min');
                    setcookie('rsreferredusername', $_GET['ref'], time() + 60 * $min, '/');
                } elseif (get_option('rs_referral_cookies_expiry') == '2') {
                    $hour = get_option('rs_referral_cookies_expiry_in_hours') == '' ? '1' : get_option('rs_referral_cookies_expiry_in_hours');
                    $hours = 60 * $hour;
                    setcookie('rsreferredusername', $_GET['ref'], time() + 60 * $hours, '/');
                } else {
                    $day = get_option('rs_referral_cookies_expiry_in_days') == '' ? '1' : get_option('rs_referral_cookies_expiry_in_days');
                    $days = 24 * $day;
                    setcookie('rsreferredusername', $_GET['ref'], time() + 60 * 60 * $days, '/');
                }
                $user = get_user_by('login', $_GET['ref']);
                if ($user != false) {
                    $currentuserid = $user->ID;
                } else {
                    $currentuserid = $_GET['ref'];
                }
                if (isset($_COOKIE['rsreferredusername'])) {
                    $mycookies = $_COOKIE['rsreferredusername'];
                } else {
                    $mycookies = '';
                }
                if ($mycookies == '') {
                    $previouscount = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($currentuserid, 'rsreferredusernameclickthrough');
                    $updatedcount = $previouscount + 1;
                    update_user_meta($currentuserid, 'rsreferredusernameclickthrough', $updatedcount);
                }
            }
            if (isset($_COOKIE['rsreferredusername'])) {
                $mycookies = $_COOKIE['rsreferredusername'];
                RSPointExpiry::delete_cookie_after_some_purchase($mycookies);
            }
        }

        public static function checkout_cookies_referral_meta($order_id, $order_posted) {
            if (isset($_COOKIE['rsreferredusername'])) {
                if (get_option('rs_generate_referral_link_based_on_user') == '1') {
                    $user = get_user_by('login', $_COOKIE['rsreferredusername']);
                    $myid = $user->ID;
                } else {
                    $refuser = get_userdata($_COOKIE['rsreferredusername']);
                    $myid = $refuser->ID;
                }

                if (get_current_user_id() != $myid) {
                    $getcurrentuserid = get_current_user_id();
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($order_id, '_referrer_name', $_COOKIE['rsreferredusername']);
                    $referral_data = array(
                        'referred_user_name' => $_COOKIE['rsreferredusername'],
                        'award_referral_points_for_renewal' => get_option('rs_award_referral_point_for_renewal_order'),
                    );
                    RSFunctionForSavingMetaValues::rewardsystem_update_post_meta($order_id, 'rs_referral_data_for_renewal_order', $referral_data);
                    $getmetafromuser = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($getcurrentuserid, '_update_user_order');
                    $getorderlist[] = $order_id;
                    if (is_array($getmetafromuser)) {
                        $mainmerge = array_merge($getmetafromuser, $getorderlist);
                    } else {
                        $mainmerge = $getorderlist;
                    }
                    update_user_meta($getcurrentuserid, '_update_user_order', $mainmerge);
                }
            }
        }

        public static function view_cash_back_table_in_myaccount() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rspointexpiry';
            $table_name2 = $wpdb->prefix . 'rsrecordpoints';
            $table_name3 = $wpdb->prefix . 'sumo_reward_encashing_submitted_data';
            ?>
            <style type="text/css">
            <?php echo get_option('rs_myaccount_custom_css'); ?>
            </style>
            <?php
            $userid = get_current_user_id();
            $fetcharray = $wpdb->get_results("SELECT * FROM $table_name3 WHERE userid = $userid", ARRAY_A);
            if (!empty($fetcharray)) {
                echo "<h2 class=my_cashback_title>" . get_option('rs_my_cashback_title') . "</h2>";
                ?>

                <table class = "examples demo shop_table my_account_orders table-bordered" data-filter = "#filters" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next">

                    <thead><tr><th data-toggle="true" data-sort-initial = "true"><?php echo get_option('rs_my_cashback_sno_label'); ?></th>
                            <th><?php echo get_option('rs_my_cashback_userid_label'); ?></th>
                            <th><?php echo get_option('rs_my_cashback_requested_label'); ?></th>
                            <th><?php echo get_option('rs_my_cashback_status_label'); ?></th>
                            <th><?php echo get_option('rs_my_cashback_action_label'); ?></th>
                    <tbody>
                        <?php
                        $userid = get_current_user_id();
                        $fetcharray = $wpdb->get_results("SELECT * FROM $table_name3 WHERE userid = $userid", ARRAY_A);
                        if (is_array($fetcharray)) {
                            if (get_option('rs_points_log_sorting') == '1') {
                                krsort($fetcharray, SORT_NUMERIC);
                            }
                        }
                        $i = 1;
                        if (is_array($fetcharray)) {
                            foreach ($fetcharray as $newarray) {
                                if (is_array($newarray)) {
                                    $usernickname = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($newarray['userid'], 'nickname');
                                    $getoption = get_option('_rs_localize_points_to_cash_log_in_my_cashback_table');
                                    $pointstocashback = $newarray['pointstoencash'];
                                    $strreplace = str_replace('[pointstocashback]', $pointstocashback, $getoption);
                                    $cashbackamount = $newarray['pointsconvertedvalue'];
                                    $strreplace1 = str_replace('[cashbackamount]', get_woocommerce_currency_symbol() . $cashbackamount, $strreplace);
                                    $rewarderforfrontend = $strreplace1;
                                    $status = $newarray['status'];
                                    $id = $newarray['id'];
                                    ?>
                                    <tr>
                                        <td data-value="<?php echo $i; ?>"><?php echo $i; ?></td>
                                        <td><?php echo $usernickname; ?> </td>
                                        <td><?php echo $rewarderforfrontend; ?></td>
                                        <td><?php echo $status; ?></td>
                                        <?php if ($status != 'Cancelled' && $status != 'Paid') { ?>
                                            <td><input type="button" class = "cancelbutton" value="Cancel" data-id="<?php echo $id; ?>" data-status="<?php echo $status; ?>"/></td>
                                        <?php } else { ?>
                                            <td><?php
                                                if ($status == 'Paid') {
                                                    echo '-';
                                                }
                                                ?></td>
                                        <?php } ?>
                                    </tr>
                                    <?php
                                    $i++;
                                    ?>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="clear:both;">
                            <td colspan="7">
                                <div class="pagination pagination-centered"></div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        jQuery('.cancelbutton').click(function () {
                            var status = jQuery(this).attr('data-status');
                            var current_user_id = '<?php echo get_current_user_id(); ?>';
                            var id = jQuery(this).attr('data-id');
                            var removed_key_param = {
                                action: "cancel_request_for_cash_back",
                                status: status,
                                current_user_id: current_user_id,
                                id: id,
                            };
                            jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", removed_key_param, function (response) {
                                location.reload();
                                console.log('Success');
                            });
                            return false;
                        });
                        return false;
                    });
                </script>
                <?php
            }
        }

        public static function ajax_request_for_cash_back() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'sumo_reward_encashing_submitted_data';
            $table_name1 = $wpdb->prefix . "rspointexpiry";
            $table_name2 = $wpdb->prefix . "rsrecordpoints";
            if ($_POST['status'] != 'Cancelled') {
                $ids = $_POST['id'];
                $wpdb->update($table_name, array('status' => 'Cancelled'), array('id' => $ids));
                $message = __($countids . ' Status Changed to Cancelled', 'rewardsystem');
                $user_id = $wpdb->get_results("SELECT userid FROM $table_name WHERE id = $ids", ARRAY_A);
                foreach ($user_id as $value) {
                    $user_idss = $value['userid'];
                }
                $returnedpoints = $wpdb->get_results("SELECT pointstoencash FROM $table_name WHERE id = $ids", ARRAY_A);
                foreach ($returnedpoints as $value) {
                    $returnedpointsss = $value['pointstoencash'];
                }
                $date = rs_function_to_get_expiry_date_in_unixtimestamp();
                $equearnamt = RSPointExpiry::earning_conversion_settings($returnedpointsss);
                RSPointExpiry::insert_earning_points($user_idss, $returnedpointsss, '0', $date, 'RCBRP', '0', $returnedpointsss, '0', '');
                $totalpoints = RSPointExpiry::get_sum_of_total_earned_points($user_idss);
                RSPointExpiry::record_the_points($user_idss, $returnedpointsss, '0', $date, 'RCBRP', '0', '0', $equearnamt, '0', '0', '0', '', $totalpoints, '', '0');
            }
        }

        public static function view_cash_back_table_in_myaccount_shortcode() {
            wp_enqueue_script('encashform', false, array(), '', true);
            if (is_user_logged_in()) {
                echo self::view_cash_back_table_in_myaccount();
            } else {
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                $linkforlogin = add_query_arg('redirect_to', get_permalink(), $myaccountlink);
                $message = get_option('rs_message_shortcode_guest_display');
                $login = get_option('rs_message_shortcode_login_name');
                echo '<br>' . $message . ' <a href=' . $linkforlogin . '> ' . $login . '</a>';
            }
        }

        public static function view_list_referal_table() {
            $get_user_type = get_option('rs_select_type_of_user_for_referral');
            $check_user_restriction = rs_function_to_check_the_restriction_for_referral($get_user_type);
            if (get_option('rs_show_hide_referal_table') != '2' && $check_user_restriction) {
                echo "<h2  class=my_rewards_title>" . get_option('rs_referal_table_title') . "</h2>";
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                $userid = get_current_user_id();
                ?>
                <table class = "referrallog demo shop_table my_account_referal table-bordered"  data-page-size="5" data-page-previous-text = "prev" >
                    <thead><th ><?php echo get_option('rs_my_referal_sno_label'); ?> </th >                                     
                    <th ><?php echo get_option('rs_my_referal_userid_label'); ?></th>             
                    <th ><?php echo get_option('rs_my_total_referal_points_label'); ?></th>
                    <tbody>
                        <?php
                        $user_ID = get_current_user_id();
                        $fetcharray = RS_Referral_Log::get_corresponding_users_log($userid);
                        if (is_array($fetcharray)) {
                            if (get_option('rs_points_log_sorting') == '1') {
                                krsort($fetcharray, SORT_NUMERIC);
                            }
                        }
                        $i = 1;
                        if (is_array($fetcharray)) {
                            foreach ($fetcharray as $newarray => $values) {
                                $getuserbyid = get_user_by('id', $newarray);
                                if (is_object($getuserbyid)) {
                                    ?>
                                    <tr>
                                        <td data-value="<?php echo $i; ?>"><?php echo $i; ?></td>
                                        <td><?php echo is_object($getuserbyid) ? $getuserbyid->user_login : 'Guest'; ?></td>
                                        <td><?php echo $values; ?></td>

                                    </tr>
                                    <?php
                                }
                                $i++;
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="clear:both;">
                            <td colspan="7">
                                <div class="pagination pagination-centered"></div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <?php
            }
        }

        public static function viewreferraltable_shortcode($content) {
            if (is_user_logged_in()) {
                $get_user_type = get_option('rs_select_type_of_user_for_referral');
                $check_user_restriction = rs_function_to_check_the_restriction_for_referral($get_user_type);
                if ($check_user_restriction) {
                    ob_start();
                    echo self::view_list_referal_table();
                    $content = ob_get_clean();
                    return $content;
                } else {
                    $message = get_option('rs_msg_for_restricted_user');
                    if (get_option('rs_display_msg_when_access_is_prevented') === '1') {
                        echo '<br>' . $message;
                    }
                }
            } else {
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                $linkforlogin = add_query_arg('redirect_to', get_permalink(), $myaccountlink);
                $message = get_option('rs_message_shortcode_guest_display');
                $login = get_option('rs_message_shortcode_login_name');
                echo '<br>' . $message . ' <a href=' . $linkforlogin . '> ' . $login . '</a>';
            }
        }

        public static function view_list_table_in_myaccount() {
            global $woocommerce;
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'redeemingonly' && $banning_type != 'both') {
                global $wpdb;
                $table_name = $wpdb->prefix . 'rspointexpiry';
                $table_name2 = $wpdb->prefix . 'rsrecordpoints';
                ?>
                <style type="text/css">
                <?php echo get_option('rs_myaccount_custom_css'); ?>
                </style>
                <?php
                echo "<h2  class=my_rewards_title>" . get_option('rs_my_rewards_title') . "</h2>";
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                $userid = get_current_user_id();
                $getusermeta = $wpdb->get_results("SELECT SUM((earnedpoints-usedpoints)) as availablepoints FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid=$userid", ARRAY_A);
                $totaloldpoints = $getusermeta[0]['availablepoints'];
                $display_currency = get_option('rs_reward_currency_value');
                if ($display_currency == '1') {
                    $point_control = wc_format_decimal(get_option('rs_redeem_point'));
                    $point_control_price = wc_format_decimal(get_option('rs_redeem_point_value')); //i.e., 100 Points is equal to $1
                    $revised_amount = $totaloldpoints * $point_control_price;
                    $coupon_value_in_points = $revised_amount / $point_control;
                    $msg = '(' . get_woocommerce_formatted_price(round($coupon_value_in_points, $roundofftype)) . ')';
                } else {
                    $msg = '';
                }
                if ($totaloldpoints != '' && $totaloldpoints > 0) {
                    if (get_option('rs_reward_point_label_position') == '1') {
                        echo "<h4 class=my_reward_total> " . get_option('rs_my_rewards_total') . " " . round(number_format((float) $totaloldpoints, 2, '.', ''), $roundofftype) . $msg . "</h4><br>";
                    } else {
                        echo "<h4 class=my_reward_total> " . round(number_format((float) $totaloldpoints, 2, '.', ''), $roundofftype) . " " . $msg . get_option('rs_my_rewards_total') . "</h4><br>";
                    }
                } else {
                    if (get_option('rs_reward_point_label_position') == '1') {
                        echo "<h4 class=my_reward_total> " . get_option('rs_my_rewards_total') . " 0</h4><br>";
                    } else {
                        echo "<h4 class=my_reward_total> " . "0 " . get_option('rs_my_rewards_total') . " </h4><br>";
                    }
                }

                $outputtablefields = '<p> ';
                if (get_option('rs_show_hide_search_box_in_my_rewards_table') == '1') {
                    $outputtablefields .= __('Search:', 'rewardsystem') . '<input id="filters" type="text"/> ';
                }
                if (get_option('rs_show_hide_page_size_my_rewards') == '1') {
                    $outputtablefields .= __('Page Size:', 'rewardsystem') . '<select id="change-page-sizes"><option value="5">5</option><option value="10">10</option><option value="50">50</option>
                    <option value="100">100</option>
                </select>';
                }
                $outputtablefields .= '</p>';
                echo $outputtablefields;
                ?>

                <table class = "examples demo shop_table my_account_orders table-bordered" data-filter = "#filters" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next">

                    <thead><tr><th data-toggle="true" data-sort-initial = "true"><?php echo get_option('rs_my_rewards_sno_label'); ?></th>
                            <?php if (get_option('rs_my_reward_points_user_name_hide') == '1') { ?>
                                <th><?php echo get_option('rs_my_rewards_userid_label'); ?></th>
                            <?php } ?>
                            <th><?php echo get_option('rs_my_rewards_rewarder_label'); ?></th>

                            <th data-hide='phone' ><?php echo get_option('rs_my_rewards_points_earned_label'); ?></th>
                            <?php if (get_option('rs_my_reward_points_expire') == '1') { ?>
                                <th data-hide='phone'><?php echo get_option('rs_my_rewards_points_expired_label'); ?></th>
                            <?php } ?>
                            <th data-hide='phone,tablet'><?php echo get_option('rs_my_rewards_redeem_points_label'); ?></th>
                            <th data-hide="phone,tablet"><?php echo get_option('rs_my_rewards_total_points_label'); ?></th>
                            <th data-hide="phone,tablet"><?php echo get_option('rs_my_rewards_date_label'); ?></th></tr></thead>
                    <tbody>
                        <?php
                        $user_ID = get_current_user_id();
                        $fetcharray = $wpdb->get_results("SELECT * FROM $table_name2 WHERE userid = $user_ID AND showuserlog = false", ARRAY_A);
                        $fetcharray = $fetcharray + (array) get_user_meta($user_ID, '_my_points_log', true);
                        if (is_array($fetcharray)) {
                            if (get_option('rs_points_log_sorting') == '1') {
                                krsort($fetcharray, SORT_NUMERIC);
                            }
                        }
                        $i = 1;
                        if (is_array($fetcharray)) {
                            foreach ($fetcharray as $newarray) {
                                if (is_array($newarray)) {
                                    $orderid = $newarray['orderid'];
                                    if (isset($newarray['earnedpoints'])) {
                                        if (!empty($newarray['earnedpoints'])) {
                                            $pointsearned = round($newarray['earnedpoints'], $roundofftype);
                                        } else {
                                            $pointsearned = 0;
                                        }

                                        if (!empty($newarray['redeempoints'])) {
                                            $redeemedpoints = round($newarray['redeempoints'], $roundofftype);
                                        } else {
                                            $redeemedpoints = 0;
                                        }

                                        if (!empty($newarray['totalpoints'])) {
                                            $totalpoints = round($newarray['totalpoints'], $roundofftype);
                                        } else {
                                            $totalpoints = 0;
                                        }
                                        $usernickname = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($newarray['userid'], 'nickname');
                                        if (!empty($newarray['checkpoints'])) {
                                            if ((float) $woocommerce->version <= (float) ('2.2.0')) {
                                                $order = new WC_Order($orderid);
                                            } else {
                                                $order = wc_get_order($orderid);
                                            }
                                            $checkpoints = $newarray['checkpoints'];
                                            $productid = $newarray['productid'];
                                            $variationid = $newarray['variationid'];
                                            $userid = $newarray['userid'];                                            
                                            $reasonindetail = $newarray['reasonindetail'];
                                            $redeempoints = $newarray['redeempoints'];
                                            $referuserid=$newarray['refuserid'];
                                            $refuserid = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($referuserid, 'nickname',true);
                                            $masterlog = false;
                                            $earnpoints = $newarray['earnedpoints'];
                                            $nomineeid = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($newarray['nomineeid'], 'nickname');
                                            $user_deleted = true;
                                            $order_status_changed = true;
                                            $csvmasterlog = false;
                                            $usernickname = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($newarray['userid'], 'nickname');
                                            $nominatedpoints = $newarray['nomineepoints'];
                                            $reason = RSPointExpiry::rs_function_to_display_log($csvmasterlog, $user_deleted, $order_status_changed, $earnpoints, $order, $checkpoints, $productid, $orderid, $variationid, $userid, $refuserid, $reasonindetail, $redeempoints, $masterlog, $nomineeid, $usernickname, $nominatedpoints);
                                            $rewarderforfrontend = $reason;
                                            $timeformat = get_option('time_format');
                                            $dateformat = get_option('date_format') . ' ' . $timeformat;
                                            $gmtdate = $newarray['expirydate'] + get_option('gmt_offset') * HOUR_IN_SECONDS;
                                            $pointsexpireddates = $newarray['expirydate'] != 999999999999 ? date_i18n($dateformat, $gmtdate) : '-';
                                        } else {
                                            $rewarderforfrontend = '';
                                        }
                                    } else {
                                        if (!empty($newarray['points_earned_order'])) {
                                            $pointsearned = round($newarray['points_earned_order'], $roundofftype);
                                        } else {
                                            $pointsearned = 0;
                                        }

                                        if (!empty($newarray['before_order_points'])) {
                                            if (is_float($newarray['before_order_points'])) {
                                                $beforepoints = round($newarray['before_order_points'], $roundofftype);
                                            } else {
                                                $beforepoints = number_format($newarray['before_order_points']);
                                            }
                                        } else {
                                            $beforepoints = 0;
                                        }

                                        if (!empty($newarray['points_redeemed'])) {
                                            $redeemedpoints = round($newarray['points_redeemed'], $roundofftype);
                                        } else {
                                            $redeemedpoints = 0;
                                        }

                                        if (!empty($newarray['totalpoints'])) {
                                            $totalpoints = round($newarray['totalpoints'], $roundofftype);
                                        } else {
                                            $totalpoints = 0;
                                        }
                                        $usernickname = get_user_meta($newarray['userid'], 'nickname', true);

                                        if (!empty($newarray['rewarder_for_frontend'])) {
                                            $rewarderforfrontend = $newarray['rewarder_for_frontend'];
                                        } else {
                                            $rewarderforfrontend = '';
                                        }
                                        if (get_option('rs_my_reward_points_expire') == '1') {
                                            $newarray['earneddate'] = $newarray['earneddate'];
                                        }
                                        if (get_option('rs_my_reward_points_expire') == '1') {
                                            $newarray['expirydate'] = '999999999999';
                                            $timeformat = get_option('time_format');
                                            $dateformat = get_option('date_format') . ' ' . $timeformat;

                                            $pointsexpireddates = $newarray['expirydate'] != 999999999999 ? date_i18n($dateformat, $newarray['expirydate']) : '-';
                                        }
                                    }

                                    if ($pointsexpireddates != '-') {
                                        if (get_option('rs_dispaly_time_format') == '1') {
                                            $pointsexpireddates = $newarray['expirydate'] != 999999999999 ? date("d-m-Y h:i:s A", $newarray['expirydate']) : '-';
                                        } else {
                                            $stringto_time = strftime($pointsexpireddates);
                                            $pointsexpireddates = $stringto_time;
                                        }
                                    }
                                    if ((($pointsearned != 0) && ($redeemedpoints != 0)) || ((($pointsearned != 0) && ($redeemedpoints == 0)) || ($pointsearned == 0) && ($redeemedpoints != 0)) || ($rewarderforfrontend != '')) {
                                        if (get_option('rs_dispaly_time_format') == '1') {
                                            $dateformat = "d-m-Y h:i:s A";
                                            $gmtdate = $newarray['earneddate'] + get_option('gmt_offset') * HOUR_IN_SECONDS;
                                            $update_start_date = is_numeric($newarray['earneddate']) ? date_i18n($dateformat, $gmtdate) : $newarray['earneddate'];
                                            $update_start_date = strftime($update_start_date);
                                        } else {
                                            $timeformat = get_option('time_format');
                                            $dateformat = get_option('date_format') . ' ' . $timeformat;
                                            $gmtdate = $newarray['earneddate'] + get_option('gmt_offset') * HOUR_IN_SECONDS;
                                            $update_start_date = is_numeric($newarray['earneddate']) ? date_i18n($dateformat, $gmtdate) : $newarray['earneddate'];
                                            $update_start_date = strftime($update_start_date);
                                        }
                                        $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                                        $pointsearned = round($pointsearned, $roundofftype);
                                        $redeemedpoints = round($redeemedpoints, $roundofftype);
                                        if ($pointsearned != '0' || $redeemedpoints != '0' || $checkpoints == 'PPRPFNP' || $checkpoints == 'SPA') {
                                            ?>
                                            <tr>
                                                <td data-value="<?php echo $i; ?>"><?php echo $i; ?></td>
                                                <?php if (get_option('rs_my_reward_points_user_name_hide') == '1') { ?>
                                                    <td><?php echo $usernickname; ?> </td>
                                                <?php } ?>
                                                <td><?php echo $rewarderforfrontend; ?></td>
                                                <td><?php echo $pointsearned; ?> </td>
                                                <?php if (get_option('rs_my_reward_points_expire') == '1') { ?>
                                                    <td><?php echo $pointsexpireddates; ?></td>
                                                <?php } ?>
                                                <td><?php echo $redeemedpoints; ?></td>
                                                <td><?php echo $totalpoints; ?> </td>
                                                <td><?php echo $update_start_date; ?></td>

                                            </tr>
                                            <?php
                                        }
                                    }
                                    $i++;
                                }
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="clear:both;">
                            <td colspan="7">
                                <div class="pagination pagination-centered"></div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <?php
            }
        }

        public static function viewchangelog_shortcode($content) {
            if (is_user_logged_in()) {
                ob_start();
                echo self::view_list_table_in_myaccount();
                $content = ob_get_clean();
                return $content;
            } else {
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                $message = get_option('rs_message_shortcode_guest_display');
                $login = get_option('rs_message_shortcode_login_name');
                $msg = $message . ' <a href=' . $myaccountlink . '> ' . $login . '</a>';
                return '<br>' . $msg;
            }
        }

        public static function add_script_to_my_account() {

            if (!is_product() && !is_checkout() && !is_shop()) {
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        window.fbAsyncInit = function () {
                            FB.init({
                                appId: "<?php echo get_option('rs_facebook_application_id'); ?>",
                                xfbml: true,
                                version: 'v2.6'
                            });
                        };
                        console.log('loaded script . . . . . ');
                        (function (d, s, id) {
                            var js, fjs = d.getElementsByTagName(s)[0];
                            if (d.getElementById(id))
                                return;
                            js = d.createElement(s);
                            js.id = id;
                <?php if ((get_option('rs_language_selection_for_button') == 1)) { ?>
                                js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
                    <?php
                } else {
                    if (get_option('WPLANG') == '') {
                        ?>
                                    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
                    <?php } else { ?>
                                    js.src = "//connect.facebook.net/<?php echo get_option('WPLANG'); ?>/sdk.js#xfbml=1&version=v2.0";
                    <?php } ?>
                <?php } ?>
                            fjs.parentNode.insertBefore(js, fjs);
                        }(document, 'script', 'facebook-jssdk'));

                        jQuery('.examples').footable().bind('footable_filtering', function (e) {
                            var selected = jQuery('.filter-status').find(':selected').text();
                            if (selected && selected.length > 0) {
                                e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected : selected;
                                e.clear = !e.filter;
                            }
                        });
                        jQuery('.referrallog').footable().bind('footable_filtering', function (e) {
                            var selected = jQuery('.filter-status').find(':selected').text();
                            if (selected && selected.length > 0) {
                                e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected : selected;
                                e.clear = !e.filter;
                            }
                        });

                        function postToFeed(url, image, description, title) {
                            var obj = {
                                method: 'feed',
                                name: title,
                                link: url,
                                picture: image,
                                description: description
                            };
                            function callback(response) {
                                if (response != null) {
                                    alert('sucessfully posted');
                                } else {
                                    alert('cancel');
                                }
                            }
                            FB.ui(obj, callback);
                        }
                        jQuery('.referral_link').footable().bind({
                            'footable_row_expanded': function (e) {
                                jQuery('.referralclick').click(function () {
                                    var getarraykey = jQuery(this).attr('data-array');
                                    console.log(jQuery(this).parent().parent().hide());
                                    var dataparam = ({
                                        action: 'unset_referral',
                                        unsetarray: getarraykey,
                                        userid: '<?php echo get_current_user_id(); ?>'
                                    });
                                    jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                            function (response) {
                                                var newresponse = response.replace(/\s/g, '');
                                                if (newresponse === "success") {
                                                    location.reload();
                                                }
                                            });
                                    return false;
                                });
                                jQuery('.share_wrapper').click(function (evt) {
                                    evt.preventDefault();
                                    var a = document.getElementById('share_wrapper')
                                    var url = a.getAttribute('href');
                                    var image = a.getAttribute('data-image');
                                    var title = a.getAttribute('data-title');
                                    var description = a.getAttribute('data-description');
                                    postToFeed(url, image, description, title);
                                    return false;
                                });
                            },
                        });
                        jQuery('#change-page-sizes').change(function (e) {
                            e.preventDefault();
                            var pageSize = jQuery(this).val();
                            jQuery('.footable').data('page-size', pageSize);
                            jQuery('.footable').trigger('footable_initialized');
                        });


                    });</script>
                <?php
            }
        }

        /* Shortcode For Total Redeem Points */

        public static function add_shortcode_to_display_total_redeem_points() {
            if (is_user_logged_in()) {
                global $wpdb;
                $table_name = $wpdb->prefix . "rspointexpiry";
                $getcurrentuserid = get_current_user_id();
                $current_user_points_log = $wpdb->get_results("SELECT SUM(usedpoints) as availablepoints FROM $table_name WHERE usedpoints NOT IN(0) and userid=$getcurrentuserid", ARRAY_A);
                $total_points_redemed = '0';
                foreach ($current_user_points_log as $separate_points) {
                    $totalredeempoints = self::function_to_get_total_redeemed_points_for_user();
                    $total_points_redemed = $separate_points['availablepoints'] + $totalredeempoints;
                }
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                return round($total_points_redemed, $roundofftype);
            } else {
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                $message = get_option('rs_message_shortcode_guest_display');
                $login = get_option('rs_message_shortcode_login_name');
                $msg = $message . ' <a href=' . $myaccountlink . '> ' . $login . '</a>';
                return '<br>' . $msg;
            }
        }

        /* Shortcode for total Redeemed Points for User */

        public static function function_to_get_total_redeemed_points_for_user() {
            if (is_user_logged_in()) {
                $current_user_points_log = get_user_meta(get_current_user_id(), '_my_points_log', true);
                $total_points_redemed = '0';
                if ($current_user_points_log != '') {
                    foreach ($current_user_points_log as $separate_points) {
                        if (isset($separate_points['points_redeemed'])) {
                            if ($separate_points['points_redeemed'] != "") {
                                $total_points_redemed += $separate_points['points_redeemed'];
                            } else {
                                $total_points_redemed = "0";
                            }
                        }
                    }
                    $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';

                    return round($total_points_redemed, $roundofftype);
                }
            }
        }

        /* Shortcode For Total Earned Points */

        public static function add_shortcode_to_display_total_earned_points() {
            if (is_user_logged_in()) {
                $totaloldearnedpoints = '';
                global $wpdb;
                $table_name = $wpdb->prefix . "rspointexpiry";
                $getcurrentuserid = get_current_user_id();
                $current_user_points_log = $wpdb->get_results("SELECT SUM(earnedpoints) as availablepoints FROM $table_name WHERE earnedpoints NOT IN(0) and userid=$getcurrentuserid", ARRAY_A);
                $total_points_earned = '0';
                foreach ($current_user_points_log as $separate_points) {
                    $deletedearnedpoints = get_user_meta($getcurrentuserid, 'rs_earned_points_before_delete', true);
                    $total_earned_points = get_user_meta($getcurrentuserid, 'rs_user_total_earned_points', true);
                    $oldearnedpoints = get_user_meta($getcurrentuserid, '_my_reward_points', true);
                    if ($total_earned_points > $oldearnedpoints) {
                        $totaloldearnedpoints = $total_earned_points - $oldearnedpoints;
                    }
                    $total_points_earned = $separate_points['availablepoints'] + $deletedearnedpoints + $totaloldearnedpoints;
                }
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';
                return round($total_points_earned, $roundofftype);
            } else {
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                $linkforlogin = add_query_arg('redirect_to', get_permalink(), $myaccountlink);
                $message = get_option('rs_message_shortcode_guest_display');
                $login = get_option('rs_message_shortcode_login_name');
                echo '<br>' . $message . ' <a href=' . $linkforlogin . '> ' . $login . '</a>';
            }
        }

        /* Shortcode For Total Expired Points */

        public static function add_shortcode_to_display_total_expired_points() {
            if (is_user_logged_in()) {
                global $wpdb;
                $table_name = $wpdb->prefix . "rspointexpiry";
                $getcurrentuserid = get_current_user_id();
                $current_user_points_log = $wpdb->get_results("SELECT SUM(expiredpoints) as availablepoints FROM $table_name WHERE expiredpoints NOT IN(0) and userid=$getcurrentuserid", ARRAY_A);
                $total_points_expired = '0';
                foreach ($current_user_points_log as $separate_points) {
                    $deletedexpiredpoints = get_user_meta($getcurrentuserid, 'rs_expired_points_before_delete', true);
                    $total_points_expired = $separate_points['availablepoints'] + $deletedexpiredpoints;
                }
                $roundofftype = get_option('rs_round_off_type') == '1' ? '2' : '0';

                return round($total_points_expired, $roundofftype);
            } else {
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                $linkforlogin = add_query_arg('redirect_to', get_permalink(), $myaccountlink);
                $message = get_option('rs_message_shortcode_guest_display');
                $login = get_option('rs_message_shortcode_login_name');
                echo '<br>' . $message . ' <a href=' . $linkforlogin . '> ' . $login . '</a>';
            }
        }

        /*
         * Function for choosen in Select user role as Nominee
         */

        public static function rs_chosen_for_nominee_in_my_account_tab() {
            if (is_account_page()) {
                global $woocommerce;
                if ((float) $woocommerce->version > (float) ('2.2.0')) {
                    echo rs_common_select_function('.rs_select_nominee');
                } else {
                    echo rs_common_chosen_function('.rs_select_nominee');
                }
            }
        }

        public static function display_nominee_field_in_my_account() {
            if (is_user_logged_in()) {
                global $woocommerce;
                global $wp_roles;
                ?>
                <style type="text/css">
                    .chosen-container-single {
                        position:absolute;
                    }
                </style>
                <?php
                $getnomineetype = get_option('rs_select_type_of_user_for_nominee');
                if ($getnomineetype == '1') {
                    $getusers = get_option('rs_select_users_list_for_nominee');
                    echo "<h2>" . get_option('rs_my_nominee_title') . "</h2>";
                    if ($getusers != '') {
                        ?>
                        <table class="form-table">
                            <tr valign="top">
                                <td style="width:150px;">
                                    <label for="rs_select_nominee" style="font-size:20px;font-weight: bold;"><?php _e('Select Nominee', 'rewardsystem'); ?></label>
                                </td>
                                <td style="width:300px;">
                                    <select name="rs_select_nominee" style="width:300px;" id="rs_select_nominee" class="short rs_select_nominee">
                                        <option value=""><?php _e('Choose Nominee', 'rewardsystem'); ?></option>
                                        <?php
                                        $getusers = get_option('rs_select_users_list_for_nominee');
                                        $currentuserid = get_current_user_id();
                                        $usermeta = get_user_meta($currentuserid, 'rs_selected_nominee', true);
                                        if ($getusers != '') {
                                            if (!is_array($getusers)) {
                                                $userids = array_filter(array_map('absint', (array) explode(',', $getusers)));
                                                foreach ($userids as $userid) {
                                                    $user = get_user_by('id', $userid);
                                                    ?>
                                                    <option value="<?php echo $userid; ?>" <?php echo $usermeta == $userid ? "selected=selected" : '' ?>>
                                                        <?php if (get_option('rs_select_type_of_user_for_nominee_name') == '1') { ?>
                                                            <?php echo esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email) . ')'; ?></option>
                                                        <?php
                                                    } else {
                                                        echo esc_html($user->display_name);
                                                    }
                                                }
                                            } else {
                                                $userids = $getusers;
                                                foreach ($userids as $userid) {
                                                    $user = get_user_by('id', $userid);
                                                    ?>
                                                    <option value="<?php echo $userid; ?>" <?php echo $usermeta == $userid ? "selected=selected" : '' ?>>
                                                        <?php if (get_option('rs_select_type_of_user_for_nominee_name') == '1') { ?>
                                                            <?php echo esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email) . ')'; ?></option>
                                                        <?php
                                                    } else {
                                                        echo esc_html($user->display_name);
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td style="width:150px;">
                                    <input type="button" value="Add" class="rs_add_nominee"/>
                                </td>
                            </tr>
                        </table>
                        <?php
                    } else {
                        _e('You have no Nominee', 'rewardsystem');
                    }
                } else {
                    $getuserrole = get_option('rs_select_users_role_for_nominee');
                    echo "<h2>" . get_option('rs_my_nominee_title') . "</h2>";
                    if ($getuserrole != '') {
                        ?>
                        <table class="form-table">
                            <tr valign="top">
                                <td style="width:150px;">
                                    <label for="rs_select_nominee" style="font-size:20px;font-weight:bold;"><?php _e('Select Nominee', 'rewardsystem'); ?></label>
                                </td>
                                <td style="width:300px;">
                                    <select name="rs_select_nominee" style="width:300px;" id="rs_select_nominee" class="short rs_select_nominee">
                                        <option value=""><?php _e('Choose Nominee', 'rewardsystem'); ?></option>
                                        <?php
                                        $getusers = get_option('rs_select_users_role_for_nominee');
                                        $currentuserid = get_current_user_id();
                                        $usermeta = get_user_meta($currentuserid, 'rs_selected_nominee', true);
                                        if ($getusers != '') {
                                            if (is_array($getusers)) {
                                                foreach ($getusers as $userrole) {
                                                    $args['role'] = $userrole;
                                                    $users = get_users($args);
                                                    foreach ($users as $user) {
                                                        $userid = $user->ID;
                                                        ?>
                                                        <option value="<?php echo $userid; ?>" <?php echo $usermeta == $userid ? "selected=selected" : '' ?>>
                                                            <?php if (get_option('rs_select_type_of_user_for_nominee_name') == '1') { ?>
                                                                <?php echo esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email) . ')'; ?></option>
                                                            <?php
                                                        } else {
                                                            echo esc_html($user->display_name);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td style="width:150px;">
                                    <input type="button" value="Add" class="rs_add_nominee"/>
                                </td>
                            </tr>
                        </table>
                        <?php
                    } else {
                        _e('You have no Nominee', 'rewardsystem');
                    }
                }
            } else {
                $myaccountlink = get_permalink(get_option('woocommerce_myaccount_page_id'));
                $myaccounttitle = get_the_title(get_option('woocommerce_myaccount_page_id'));
                $linkforlogin = add_query_arg('redirect_to', get_permalink(), $myaccountlink);
                $message = get_option('rs_message_shortcode_guest_display');
                $login = get_option('rs_message_shortcode_login_name');
                echo '<br>' . $message . ' <a href=' . $linkforlogin . '> ' . $login . '</a>';
            }
        }

        public static function ajax_for_saving_nominee() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('.rs_add_nominee').click(function () {
                        var value = jQuery('#rs_select_nominee').val();
                        var Value = {
                            action: "rs_save_nominee",
                            selectedvalue: value,
                        };
                        jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", Value, function (response) {
                            alert("Nominee Saved");
                            console.log('Success');
                        });
                        return false;
                    });
                    return false;
                });
            </script>
            <?php
        }

        public static function save_selected_nominee() {
            $getpostvalue = $_POST['selectedvalue'];
            $currentuserid = get_current_user_id();
            update_user_meta($currentuserid, 'rs_selected_nominee', $getpostvalue);
            update_user_meta($currentuserid, 'rs_enable_nominee', 'yes');
        }

        public static function rs_show_referrer_name_in_home_page($query) {
            $get_user_type = get_option('rs_select_type_of_user_for_referral');
            $check_user_restriction = rs_function_to_check_the_restriction_for_referral($get_user_type);
            if (isset($_GET['ref']) && !is_user_logged_in() && $check_user_restriction) {
                get_header();
                ?>
                <div class="referral_field" style="margin-top:40px;">
                    <h4><?php echo do_shortcode(get_option('rs_show_hide_generate_referral_message_text')); ?></h4>
                </div>
                <style>
                    h4 {text-align:center;}
                </style>
                <?php
            }
            return $query;
        }

        public static function addshort_code_username() {
            if (get_option('rs_generate_referral_link_based_on_user') == '1') {
                $user = get_user_by('login', $_GET['ref']);
                $currentuserid = $user->user_nicename;
                return $currentuserid;
            } else {
                $user_info = get_userdata($_GET['ref']);
                $currentuserid = is_object($user_info) ? $user_info->user_login : 'Guest';
                return $currentuserid;
            }
        }

    }

    RSFunctionForMyAccount::init();
}