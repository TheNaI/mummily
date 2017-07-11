<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('RSFunctionForSocialRewards')) {

    class RSFunctionForSocialRewards {

        public static function init() {

            add_action('wp_enqueue_scripts', array(__CLASS__, 'add_enqueue_for_social_messages'));

            if (get_option('rs_global_position_sumo_social_buttons') == '1') {

                add_action('woocommerce_before_single_product', array(__CLASS__, 'reward_system_social_likes_buttons'));
            } elseif (get_option('rs_global_position_sumo_social_buttons') == '2') {

                add_action('woocommerce_before_single_product_summary', array(__CLASS__, 'reward_system_social_likes_buttons'));
            } elseif (get_option('rs_global_position_sumo_social_buttons') == '3') {

                add_action('woocommerce_single_product_summary', array(__CLASS__, 'reward_system_social_likes_buttons'));
            } elseif (get_option('rs_global_position_sumo_social_buttons') == '4') {

                add_action('woocommerce_after_single_product', array(__CLASS__, 'reward_system_social_likes_buttons'));
            } elseif (get_option('rs_global_position_sumo_social_buttons') == '6') {

                add_action('woocommerce_product_meta_end', array(__CLASS__, 'reward_system_social_likes_buttons'));
            } else {

                add_action('woocommerce_after_single_product_summary', array(__CLASS__, 'reward_system_social_likes_buttons'));
            }

            add_action('wp_ajax_nopriv_rssocialfacebookcallback', array(__CLASS__, 'update_reward_points_for_facebook_like'));

            add_action('wp_ajax_rssocialfacebookcallback', array(__CLASS__, 'update_reward_points_for_facebook_like'));

            add_action('wp_ajax_nopriv_rssocialinstagram', array(__CLASS__, 'update_reward_points_for_instagram'));

            add_action('wp_ajax_rssocialinstagram', array(__CLASS__, 'update_reward_points_for_instagram'));

            add_action('wp_ajax_nopriv_rsok', array(__CLASS__, 'update_reward_points_for_ok'));

            add_action('wp_ajax_rsok', array(__CLASS__, 'update_reward_points_for_ok'));

            add_action('wp_ajax_nopriv_rssocialfacebooksharecallback', array(__CLASS__, 'update_reward_points_for_facebook_share'));

            add_action('wp_ajax_rssocialfacebooksharecallback', array(__CLASS__, 'update_reward_points_for_facebook_share'));

            add_action('wp_ajax_nopriv_rssocialtwittercallback', array(__CLASS__, 'update_reward_points_for_twitter_tweet'));

            add_action('wp_ajax_rssocialtwittercallback', array(__CLASS__, 'update_reward_points_for_twitter_tweet'));

            add_action('wp_ajax_nopriv_rssocialtwitterfollowcallback', array(__CLASS__, 'update_reward_points_for_twitter_follow'));

            add_action('wp_ajax_rssocialtwitterfollowcallback', array(__CLASS__, 'update_reward_points_for_twitter_follow'));

            add_action('wp_ajax_nopriv_rssocialgooglecallback', array(__CLASS__, 'update_reward_points_for_google_plus_share'));

            add_action('wp_ajax_rssocialgooglecallback', array(__CLASS__, 'update_reward_points_for_google_plus_share'));

            add_action('wp_ajax_nopriv_rsvkcallback', array(__CLASS__, 'update_reward_points_for_vk_like'));

            add_action('wp_ajax_rsvkcallback', array(__CLASS__, 'update_reward_points_for_vk_like'));

            add_shortcode('google_share_reward_points', array(__CLASS__, 'add_shortcode_for_social_google_share'));

            add_shortcode('vk_reward_points', array(__CLASS__, 'add_shortcode_for_social_vk_like'));

            add_shortcode('twitter_tweet_reward_points', array(__CLASS__, 'add_shortcode_for_social_twitter_tweet'));

            add_shortcode('facebook_like_reward_points', array(__CLASS__, 'add_shortcode_for_social_facebook_like'));

            add_shortcode('facebook_share_reward_points', array(__CLASS__, 'add_shortcode_for_social_facebook_share'));

            add_shortcode('twitter_follow_reward_points', array(__CLASS__, 'add_shortcode_for_social_twitter_follow'));

            add_shortcode('ok_share_reward_points', array(__CLASS__, 'add_shortcode_for_social_ok_share'));

            add_shortcode('instagram_reward_points', array(__CLASS__, 'add_shortcode_for_social_instagram'));

        }

        public static function reward_system_social_likes_buttons() {
            global $post;
            global $woocommerce;
            if (is_user_logged_in()) {
                if (get_post_meta($post->ID, '_socialrewardsystemcheckboxvalue', true) == 'yes') {
                    if (get_option('rs_global_show_hide_facebook_like_button') == '1') {
                        $array_social['fb_like'] = "show";
                    }
                    if (get_option('rs_global_show_hide_facebook_share_button') == '1') {
                        $array_social['fb_share'] = "show";
                    }
                    if (get_option('rs_global_show_hide_twitter_tweet_button') == '1') {
                        $array_social['twitter'] = "show";
                    }
                    if (get_option('rs_global_show_hide_twitter_follow_tweet_button') == '1') {
                        if (get_option('rs_global_social_twitter_profile_name') != '') {
                            $array_social['twitter_follow'] = "show";
                        }
                    }
                    if (get_option('rs_global_show_hide_google_plus_button') == '1') {
                        $array_social['google_share'] = "show";
                    }
                    if (get_option('rs_global_show_hide_vk_button') == '1') {
                        $array_social['vk_like'] = "show";
                    }
                    if (get_option('rs_global_show_hide_instagram_button') == '1') {
                        if (get_option('rs_instagram_profile_name') != '') {
                            $array_social['instagram'] = "show";
                        }
                    }
                    if (get_option('rs_global_show_hide_ok_button') == '1') {
                        $array_social['ok_share'] = "show";
                    }
                    $post_title = $post->post_title;
                    $post_description = $post->post_content;
                    $product_url = $post->guid;
                    $product_caption = $post->post_excerpt;
                    $gallery = get_post_gallery_images($post);
                    $plugins_url = plugins_url();
                    $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID));
                    $language = get_option('rs_language_selection_for_button');
                    $wplang = get_option('WPLANG');
                    $message = do_shortcode(get_option(('rs_succcess_message_for_facebook_share')));
                    $unsucess = get_option('rs_unsucccess_message_for_facebook_share');
                    $global_variable_for_js = array('wp_ajax_url' => admin_url('admin-ajax.php'), 'user_id' => get_current_user_id(), 'product_title' => $post_title, 'url' => get_permalink(), 'caption' => $product_caption, 'images' => $image[0], 'product_id' => $post->ID, 'facebook_id' => get_option('rs_facebook_application_id'), 'language' => $language, 'wplang' => $wplang, 'msg_social' => $message, 'unsucess_msg' => $unsucess);
                    wp_localize_script('socialbutton', 'socialbutton_variable_js', $global_variable_for_js);
                    wp_enqueue_script('socialbutton', false, array(), '', true);

                    if (get_option('rs_facebook_application_id') != '') {
                        ?>
                        <style>.ig-b- { display: inline-block; }
                            .ig-b- img { visibility: hidden; }
                            .ig-b-:hover { background-position: 0 -60px; } .ig-b-:active { background-position: 0 -120px; }
                            .ig-b-32 { width: 32px; height: 32px; background: url(//badges.instagram.com/static/images/ig-badge-sprite-32.png) no-repeat 0 0; }
                            @media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min--moz-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-device-pixel-ratio: 2), only screen and (min-resolution: 192dpi), only screen and (min-resolution: 2dppx) {
                                .ig-b-32 { background-image: url(//badges.instagram.com/static/images/ig-badge-sprite-32@2x.png); background-size: 60px 178px; } }</style>
                        <div id="fb-root"></div>
                        <script type="text/javascript">
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
                                if (d.getElementById(id)) {
                                    return;
                                }
                                js = d.createElement(s);
                                js.id = id;
                        <?php if (get_option('rs_language_selection_for_button') == 1) { ?>
                                    js.src = "https://connect.facebook.net/en_US/sdk.js";
                            <?php
                        } else {
                            if (get_option('WPLANG') == '') {
                                ?>
                                        js.src = "https://connect.facebook.net/en_US/sdk.js";
                            <?php } else { ?>
                                        js.src = "https://connect.facebook.net/<?php echo get_option('WPLANG'); ?>/sdk.js";
                                <?php
                            }
                        }
                        ?>
                                fjs.parentNode.insertBefore(js, fjs);
                            }(document, 'script', 'facebook-jssdk'));
                            console.log('script loaded');
                        </script>
                    <?php } ?>
                    <script>!function (d, s, id) {
                            var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                            if (!d.getElementById(id)) {
                                js = d.createElement(s);
                                js.id = id;
                                js.src = p + '://platform.twitter.com/widgets.js';
                                fjs.parentNode.insertBefore(js, fjs);
                            }
                        }(document, 'script', 'twitter-wjs');</script>
                    <script>
                        var originalCallback = function (o) {
                            console.log(o);
                            console.log('original callback - ' + o.state);
                            var state = o.state;
                    <?php
                    $google_success_mesage = do_shortcode(get_option('rs_succcess_message_for_google_share'));
                    $google_unsuccess_mesage = get_option('rs_unsucccess_message_for_google_unshare');
                    ?>
                            var dataparam = ({
                                action: 'rssocialgooglecallback',
                                state: state,
                                postid: '<?php echo $post->ID; ?>',
                                currentuserid: '<?php echo get_current_user_id(); ?>',
                            });
                            jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                    function (response) {
                                        if (response == "You already Shared this post on Goole+1Ajax Call Successfully Triggered") {
                                            jQuery('<p><?php echo addslashes($google_unsuccess_mesage); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                        } else {
                                            jQuery('<p><?php echo addslashes($google_success_mesage); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                        }
                                    });
                            return false;
                        };</script>
                    <script>
                        window.___gcfg = {
                            lang: '<?php echo get_option('WPLANG') == '' ? 'en_US' : get_option('WPLANG'); ?>',
                            parsetags: 'onload'
                        }
                    </script>
                    <style type="text/css">
                        .gc-bubbleDefault, .pls-container {
                            display: none;
                        }
                    </style>
                    <script type="text/javascript" src="https://apis.google.com/js/plusone.js">

                    </script>
                    <?php
                    if (get_option('rs_vk_application_id') != '') {
                        ?>
                        <script type="text/javascript" src="//vk.com/js/api/openapi.js?116"></script>

                        <script type="text/javascript">
                            VK.init({
                                apiId: "<?php echo get_option('rs_vk_application_id') ?>",
                                onlyWidgets: true
                            });
                        </script>

                        <script type="text/javascript">
                            jQuery(window).load(function () {
                                VK.Widgets.Like("vk_like", {type: "button"});

                                VK.Observer.subscribe("widgets.like.liked", function f() {

                                    var vklikecallback = ({
                                        action: 'rsvkcallback',
                                        state: 'on',
                                        postid: '<?php echo $post->ID; ?>',
                                        currentuserid: '<?php echo get_current_user_id(); ?>',
                                    });
                        <?php
                        $vk_success_mesage = do_shortcode(get_option('rs_succcess_message_for_vk'));
                        $vk_unlike_mesage = get_option('rs_unsucccess_message_for_vk');
                        ?>


                                    jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", vklikecallback,
                                            function (response) {
                                                if (response == "You have already liked this post on VK.ComAjax Call Successfully Triggered") {
                                                    jQuery('<p><?php echo addslashes($vk_unlike_mesage); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                                } else {
                                                    jQuery('<p><?php echo addslashes($vk_success_mesage); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                                }
                                            });
                                    return false;

                                });

                                VK.Observer.subscribe("widgets.like.unliked", function f1() {

                                    var vkunlikecallback = ({
                                        action: 'rsvkcallback',
                                        state: 'off',
                                        postid: '<?php echo $post->ID; ?>',
                                        currentuserid: '<?php echo get_current_user_id(); ?>',
                                    });
                                    jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", vkunlikecallback,
                                            function (response) {

                                            });
                                    return false;
                                });
                            });

                        </script>
                        <style type="text/css">
                            .vk-like{
                                width:88px !important;
                            }
                        </style>
                        <?php
                    }
                    ?>
                    <style type="text/css">
                        .fb_iframe_widget {
                            display:inline-flex !important;
                        }
                        .twitter-share-button {
                            width:88px !important;
                        }

                    </style>

                    <!--<div id='ok_shareWidget'> </div>-->
                    <script>
                        !function (d, id, did, st, title, description, image) {
                            var js = d.createElement("script");
                            js.src = "https://connect.ok.ru/connect.js";
                            js.onload = js.onreadystatechange = function () {
                                if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
                                    if (!this.executed) {
                                        this.executed = true;
                                        setTimeout(function () {
                                            OK.CONNECT.insertShareWidget(id, did, st, title, description, image);
                                        }, 0);
                                    }
                                }
                            };
                            d.documentElement.appendChild(js);
                        }(document, "ok_shareWidget", "<?php echo get_option('rs_global_social_ok_url') == '1' ? get_permalink() : get_option('rs_global_social_ok_url_custom'); ?>", '{"sz":30,"st":"oval","nc":1,"nt":1}', "", "", "");
                        !function (d, id, did, st) {
                            var js = d.createElement("script");
                            js.src = "https://connect.ok.ru/connect.js";
                            js.onload = js.onreadystatechange = function () {
                                if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
                                    if (!this.executed) {
                                        this.executed = true;
                                        setTimeout(function () {
                                            onOkConnectReady()
                                        }, 0);
                                    }
                                }
                            }
                            d.documentElement.appendChild(js);
                        }(document);
                        function onOkConnectReady() {
                            OK.CONNECT.insertGroupWidget("mineGroupWidgetDivId", "50582132228315", "{width:250,height:335}");
                            OK.CONNECT.insertShareWidget("mineShareWidgetDivId", "https://apiok.ru", "{width:125,height:25,st:'oval',sz:12,ck:1}");
                        }
                    </script>
                    <script type="text/javascript">
                        jQuery(window).load(function () {
                            <?php $ok_success_mesage_follow = do_shortcode(get_option('rs_succcess_message_for_ok_follow')); ?>
                            function listenForShare() {
                                if (window.addEventListener) {
                                    window.addEventListener('message', onShare, false);
                                } else {
                                    window.attachEvent('onmessage', onShare);
                                }
                            }

                            function onShare(e) {
                                var args = e.data.split("$");
                                if (args[0] == "ok_shared") {
                                    var dataparam = ({
                                        action: 'rsok',
                                        state: 'on',
                                        postid: '<?php echo $post->ID; ?>',
                                        currentuserid: '<?php echo get_current_user_id(); ?>',
                                    });
                                    jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                            function (response) {
                                                console.log(response);
                                                jQuery('<p><?php echo addslashes($ok_success_mesage_follow); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                            });

                                    return false;
                                }
                            }
                            listenForShare();
                        });
                    </script>
                    <?php
                    $enablerewards = RSFunctionForSavingMetaValues::rewardsystem_get_post_meta($post->ID, '_socialrewardsystemcheckboxvalue');
                    if ($enablerewards == 'yes') {
                        ?>
                        <style type="text/css">
                        <?php echo get_option('rs_social_custom_css'); ?>
                        </style>
                        <table class="rs_social_sharing_buttons"   style="display:<?php echo get_option('rs_social_button_position_troubleshoot'); ?>">
                            <tr>
                                <?php
                                if (get_option('rs_global_show_hide_facebook_like_button') == '1') {
                                    if (get_option('rs_facebook_application_id') != '') {
                                        ?>
                                        <td><div class="fb-like" data-href="<?php echo get_option('rs_global_social_facebook_url') == '1' ? get_permalink() : get_option('rs_global_social_facebook_url_custom'); ?>" data-layout="button_count" data-action="like" data-show-faces="true" data-share="false"></div></td>
                                        <?php
                                    }
                                }
                                ?>
                                <?php
                                if (get_option('rs_global_show_hide_facebook_share_button') == '1') {
                                    if (get_option('rs_facebook_application_id') != '') {
                                        ?>
                                        <td><div class="share_wrapper1">
                                                <img class='fb_share_img' src="<?php echo $plugins_url ?>/rewardsystem/admin/images/icon1.png"> <span class="label"><?php echo get_option('rs_fbshare_button_label'); ?> </span>
                                            </div>
                                        </td>

                                        <?php
                                    }
                                }
                                ?>
                                <?php if (get_option('rs_global_show_hide_twitter_tweet_button') == '1') { ?>
                                    <td><div class="rstwitter-button-msg"><a href="https://twitter.com/share" class="twitter-share-button" id="twitter-share-button" data-url="<?php echo get_option('rs_global_social_twitter_url') == '1' ? get_permalink() : get_option('rs_global_social_twitter_url_custom'); ?>"></a></div></td>
                                <?php } ?>
                                <?php
                                if (get_option('rs_global_show_hide_twitter_follow_tweet_button') == '1') {
                                    if (get_option('rs_global_social_twitter_profile_name') != '') {
                                        $profile = get_option('rs_global_social_twitter_profile_name')
                                        ?>
                                        <td class="twitter_follow_btn"><div class="rstwitterfollow-button-msg"><a href='https://twitter.com/<?php echo $profile; ?>'   class="twitter-follow-button" data-show-count="false">Follow @twitter</a></td></div>
                                        <?php
                                    }
                                }
                                ?>
                                <?php if (get_option('rs_global_show_hide_google_plus_button') == '1') { ?>
                                    <td>
                                        <div id="google-plus-one"><g:plusone annotation="bubble" callback="originalCallback" class="google-plus-one" href='<?php echo get_option('rs_global_social_google_url') == '1' ? get_permalink() : get_option('rs_global_social_google_url_custom'); ?>'></g:plusone></div>
                                    </td>


                                <?php } ?>
                                <?php if (count($array_social) > 6) { ?>
                                </tr>
                                <tr>
                                    <?php if (get_option('rs_global_show_hide_vk_button') == '1') { ?>
                                        <td ><div id="vk_like" class='vk-like' ></div></td>
                                    <?php } ?>
                                    <?php
                                    if (get_option('rs_global_show_hide_instagram_button') == '1') {
                                        if (get_option('rs_instagram_profile_name') != '') {
                                            $instagram_profile_name = get_option('rs_instagram_profile_name');
                                            ?>
                                            <td>
                                                <div class ="instagram_button"><a href="https://www.instagram.com/<?php echo $instagram_profile_name ?>/?ref=badge" class="ig-b- ig-b-32" target="_blank"><img src="//badges.instagram.com/static/images/ig-badge-32.png" alt="Instagram" /></a></div>

                                            </td>
                                            <?php
                                        }
                                    }
                                    if (get_option('rs_global_show_hide_ok_button') == '1') {
                                        ?>
                                        <td><div class="ok-share-button" id="ok_shareWidget" style="width:30px;"><a href="https://ok.ru/" class="ok-share-button" id="ok-share-button" data-url="<?php echo get_option('rs_global_social_ok_url') == '1' ? get_permalink() : get_option('rs_global_social_ok_url_custom'); ?>"></a></div> </td>
                                    <?php } ?>
                                </tr>
                            <?php } else { ?>

                                <?php if (get_option('rs_global_show_hide_vk_button') == '1') { ?>
                                    <td ><div id="vk_like" class='vk-like' ></div></td>
                                <?php } ?>
                                <?php
                                if (get_option('rs_global_show_hide_instagram_button') == '1') {
                                    if (get_option('rs_instagram_profile_name') != '') {
                                        $instagram_profile_name = get_option('rs_instagram_profile_name');
                                        ?>
                                        <td>
                                            <div class ="instagram_button"><a href="https://www.instagram.com/<?php echo $instagram_profile_name ?>/?ref=badge" class="ig-b- ig-b-32" target="_blank"><img src="//badges.instagram.com/static/images/ig-badge-32.png" alt="Instagram" /></a></div>
                                        </td>
                                        <?php
                                    }
                                }
                                if (get_option('rs_global_show_hide_ok_button') == '1') {
                                    ?>
                                    <td><div class="ok-share-button" id="ok_shareWidget" style="width:30px;"><a href="https://ok.ru/" class="ok-share-button" id="ok-share-button" data-url="<?php echo get_option('rs_global_social_ok_url') == '1' ? get_permalink() : get_option('rs_global_social_ok_url_custom'); ?>"></a></div> </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        </table>
                        <?php if (get_option('rs_global_show_hide_facebook_share_button') == '1') { ?>
                            <style>
                                .share_wrapper1{
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
                        <?php } ?>
                        <div class="social_promotion_success_message"></div>
                    <?php } ?>
                    <?php
                    ?>
                    <?php
                    if (get_option('rs_instagram_profile_name') != '') {
                        $instagram_unsuccess_mesage = get_option('rs_unsucccess_message_for_instagram');
                        $instagram_success_message = do_shortcode(get_option('rs_succcess_message_for_instagram'));
                        ?>
                        <script type='text/javascript'>
                            jQuery(window).load(function () {
                                var instagram_button = document.querySelector('.instagram_button');
                                instagram_button.addEventListener('click', function (e) {
                                    var dataparam = ({
                                        action: 'rssocialinstagram',
                                        state: 'on',
                                        postid: '<?php echo $post->ID; ?>',
                                        currentuserid: '<?php echo get_current_user_id(); ?>',
                                    });
                                    jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                            function (response) {

                                                if (response == "You already follow this profileAjax Call Successfully Triggered") {
                                                    jQuery('<p><?php echo addslashes($instagram_unsuccess_mesage); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                                } else {
                                                    jQuery('<p><?php echo addslashes($instagram_success_message); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                                }

                                            });

                                    return false;
                                });
                            });
                    <?php } ?>
                    </script>

                    <script type='text/javascript'>
                        jQuery(window).load(function () {
                            /* This is for facebook which is been like or not */
                    <?php if (get_option('rs_facebook_application_id') != '') { ?>
                                var page_like_callback = function (url, html_element) {
                        <?php
                        $facebook_success_mesage = do_shortcode(get_option('rs_succcess_message_for_facebook_like'));
                        $facebook_unsuccess_mesage = get_option('rs_unsucccess_message_for_facebook_unlike');
                        ?>

                                    console.log("page_like");
                                    console.log(url);
                                    console.log(html_element);
                                    var dataparam = ({
                                        action: 'rssocialfacebookcallback',
                                        state: 'on',
                                        postid: '<?php echo $post->ID; ?>',
                                        currentuserid: '<?php echo get_current_user_id(); ?>',
                                    });
                                    jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                            function (response) {

                                                if (response == "You already liked this postAjax Call Successfully Triggered") {
                                                    jQuery('<p><?php echo addslashes($facebook_unsuccess_mesage); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                                } else {
                                                    jQuery('<p><?php echo addslashes($facebook_success_mesage); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                                }

                                            });
                                    return false;
                                }

                                var page_unlike_callback = function (url, html_element) {
                        <?php
                        $facebook_success_mesage = do_shortcode(get_option('rs_succcess_message_for_facebook_like'));
                        $facebook_unsuccess_mesage = get_option('rs_unsucccess_message_for_facebook_unlike');
                        ?>
                                    console.log('page_unlike');
                                    console.log(url);
                                    console.log(html_element);
                                    var dataparam = ({
                                        action: 'rssocialfacebookcallback',
                                        state: 'off',
                                        postid: '<?php echo $post->ID; ?>',
                                        currentuserid: '<?php echo get_current_user_id(); ?>'
                                    });
                                    jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                            function (response) {

                                                if (response == "You already liked this postAjax Call Successfully Triggered") {
                                                    jQuery('<p><?php echo addslashes($facebook_unsuccess_mesage); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                                } else {
                                                    jQuery('<p><?php echo addslashes($facebook_success_mesage); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                                }
                                            });
                                    return false;
                                }
                                // Detect Like or Unlike using Event Subscribe of Facebook
                                FB.Event.subscribe('edge.create', page_like_callback);
                                FB.Event.subscribe('edge.remove', page_unlike_callback);
                    <?php } ?>
                            twttr.events.bind('follow', function (event) {

                    <?php
                    $twitter_success_mesage_follow = do_shortcode(get_option('rs_succcess_message_for_twitter_follow'));
                    $twitter_unsuccess_mesage_follow = get_option('rs_unsucccess_message_for_twitter_unfollow');
                    ?>
                                console.log('You follow Successfully');
                                var dataparam = ({
                                    action: 'rssocialtwitterfollowcallback',
                                    state: 'on',
                                    postid: '<?php echo $post->ID; ?>',
                                    currentuserid: '<?php echo get_current_user_id(); ?>',
                                });
                                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                        function (response) {

                                            if (response == "You already Follow this postAjax Call Successfully Triggered") {
                                                jQuery('<p><?php echo addslashes($twitter_unsuccess_mesage_follow); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                            } else {
                                                jQuery('<p><?php echo addslashes($twitter_success_mesage_follow); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                            }

                                        });
                                return false;
                            });
                            // This below code is for Twitter Tweet
                            twttr.events.bind('tweet', function (ev) {
                    <?php
                    $twitter_success_mesage = do_shortcode(get_option('rs_succcess_message_for_twitter_share'));
                    $twitter_unsuccess_mesage = get_option('rs_unsucccess_message_for_twitter_unshare');
                    ?>
                                console.log('You Tweet Successfully');
                                var dataparam = ({
                                    action: 'rssocialtwittercallback',
                                    state: 'on',
                                    postid: '<?php echo $post->ID; ?>',
                                    currentuserid: '<?php echo get_current_user_id(); ?>',
                                });
                                jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", dataparam,
                                        function (response) {

                                            if (response == "You already Tweet this postAjax Call Successfully Triggered") {
                                                jQuery('<p><?php echo addslashes($twitter_unsuccess_mesage); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                            } else {
                                                jQuery('<p><?php echo addslashes($twitter_success_mesage); ?></p>').appendTo('.social_promotion_success_message').fadeIn().delay(5000).fadeOut();
                                            }

                                        });
                                return false;
                            });
                        });</script>
                    <?php
                }
            }
        }

        public static function add_fb_style_hide_comment_box() {
            global $post;

            if (get_post_meta(@$post->ID, '_socialrewardsystemcheckboxvalue', true) == 'yes') {
                ?>
                <style type="text/css">
                    .fb_edge_widget_with_comment span.fb_edge_comment_widget iframe.fb_ltr {
                        display: none !important;
                    }
                    .fb-like{
                        height: 20px !important;
                        overflow: hidden !important;
                    }
                    .tipsy-inner {
                        background-color:#<?php echo get_option('rs_social_tooltip_bg_color'); ?>;

                        color:#<?php echo get_option('rs_social_tooltip_text_color'); ?>;
                    }
                    .tipsy-arrow-s { border-top-color: #<?php echo get_option('rs_social_tooltip_bg_color'); ?>; }
                </style>

                <?php
                if (get_option('rs_reward_point_enable_tipsy_social_rewards') == '1') {
                    if (get_option('rs_global_show_hide_social_tooltip_for_facebook') == '1') {
                        ?>
                        <script type="text/javascript">
                            jQuery(window).load(function () {
                        <?php
                        $userid = get_current_user_id();
                        $banning_type = FPRewardSystem::check_banning_type($userid);
                        if ($banning_type != 'earningonly' && $banning_type != 'both') {
                            $fb_info = get_user_meta($userid, '_rsfacebooklikes', true);
                            $postid = $post->ID;
                            if (!in_array($postid, (array) $fb_info)) {
                                ?>
                                        jQuery('.fb-like').tipsy({gravity: 's', live: 'true', fallback: '<?php echo do_shortcode(get_option('rs_social_message_for_facebook')); ?>'});
                                <?php
                            }
                        }
                        ?>
                            });
                        </script>
                        <?php
                    }
                }
                if (get_option('rs_reward_point_enable_tipsy_social_rewards') == '1') {
                    if (get_option('rs_global_show_hide_social_tooltip_for_facebook_share') == '1') {
                        ?>
                        <script type="text/javascript">
                            jQuery(window).load(function () {
                        <?php
                        $userid = get_current_user_id();
                        $banning_type = FPRewardSystem::check_banning_type($userid);
                        if ($banning_type != 'earningonly' && $banning_type != 'both') {
                            $fb_info = get_user_meta($userid, '_rsfacebookshare', true);
                            $postid = $post->ID;
                            if (!in_array($postid, (array) $fb_info)) {
                                ?>
                                        jQuery('.share_wrapper1').tipsy({gravity: 's', live: 'true', fallback: '<?php echo do_shortcode(get_option('rs_social_message_for_facebook_share')); ?>'});
                                <?php
                            }
                        }
                        ?>
                            });
                        </script>
                        <?php
                    }
                }
                if (get_option('rs_reward_point_enable_tipsy_social_rewards') == '1') {
                    if (get_option('rs_global_show_hide_social_tooltip_for_twitter') == '1') {
                        ?>
                        <script type="text/javascript">
                            jQuery(window).load(function () {
                        <?php
                        $userid = get_current_user_id();
                        $banning_type = FPRewardSystem::check_banning_type($userid);
                        if ($banning_type != 'earningonly' && $banning_type != 'both') {
                            $twitter_info = get_user_meta($userid, '_rstwittertweet', true);
                            $postid = $post->ID;
                            if (!in_array($postid, (array) $twitter_info)) {
                                ?>
                                        jQuery('.rstwitter-button-msg').tipsy({gravity: 's', live: 'true', fallback: '<?php echo do_shortcode(get_option('rs_social_message_for_twitter')); ?>'});
                                <?php
                            }
                        }
                        ?>
                            });
                        </script>
                        <?php
                    }
                }
                if (get_option('rs_reward_point_enable_tipsy_social_rewards') == '1') {
                    if (get_option('rs_global_show_hide_social_tooltip_for_twitter_follow') == '1') {
                        ?>
                        <script type="text/javascript">
                            jQuery(window).load(function () {
                        <?php
                        $userid = get_current_user_id();
                        $banning_type = FPRewardSystem::check_banning_type($userid);
                        if ($banning_type != 'earningonly' && $banning_type != 'both') {
                            $twitter_info = get_user_meta($userid, '_rstwitterfollow', true);
                            $postid = $post->ID;
                            if (!in_array($postid, (array) $twitter_info)) {
                                ?>
                                        jQuery('.rstwitterfollow-button-msg').tipsy({gravity: 's', live: 'true', fallback: '<?php echo do_shortcode(get_option('rs_social_message_for_twitter_follow')); ?>'});
                                <?php
                            }
                        }
                        ?>
                            });
                        </script>
                        <?php
                    }
                }
                if (get_option('rs_reward_point_enable_tipsy_social_rewards') == '1') {
                    if (get_option('rs_global_show_hide_social_tooltip_for_ok_follow') == '1') {
                        ?>
                        <script type="text/javascript">
                            jQuery(window).load(function () {
                        <?php
                        $userid = get_current_user_id();
                        $banning_type = FPRewardSystem::check_banning_type($userid);
                        if ($banning_type != 'earningonly' && $banning_type != 'both') {
                            $twitter_info = get_user_meta($userid, '_rsokfollow', true);
                            $postid = $post->ID;
                            if (!in_array($postid, (array) $twitter_info)) {
                                ?>
                                        jQuery('.ok-share-button').tipsy({gravity: 's', live: 'true', fallback: '<?php echo do_shortcode(get_option('rs_social_message_for_ok_follow')); ?>'});
                                <?php
                            } else {
                                $ok_success_mesage_follow = do_shortcode(get_option('rs_unsucccess_message_for_ok_unfollow'));
                                ?>
                                        jQuery('.ok-share-button').tipsy({gravity: 's', live: 'true', fallback: '<?php echo $ok_success_mesage_follow; ?>'});
                                <?php
                            }
                        }
                        ?>
                            });
                        </script>
                        <?php
                    }
                }
                if (get_option('rs_reward_point_enable_tipsy_social_rewards') == '1') {
                    if (get_option('rs_global_show_hide_social_tooltip_for_google') == '1') {
                        ?>
                        <script type="text/javascript">
                            jQuery(window).load(function () {
                        <?php
                        $userid = get_current_user_id();
                        $banning_type = FPRewardSystem::check_banning_type($userid);
                        if ($banning_type != 'earningonly' && $banning_type != 'both') {
                            $google_info = get_user_meta($userid, '_rsgoogleshares', true);
                            $postid = $post->ID;
                            if (!in_array($postid, (array) $google_info)) {
                                ?>
                                        jQuery('#google-plus-one').tipsy({gravity: 's', live: 'true', fallback: '<?php echo do_shortcode(get_option('rs_social_message_for_google_plus')); ?>'});
                                <?php
                            }
                        }
                        ?>
                            });
                        </script>
                        <?php
                    }
                }
                if (get_option('rs_reward_point_enable_tipsy_social_rewards') == '1') {
                    if (get_option('rs_global_show_hide_social_tooltip_for_vk') == '1') {
                        ?>
                        <script type="text/javascript">
                            jQuery(window).load(function () {
                        <?php
                        $userid = get_current_user_id();
                        $banning_type = FPRewardSystem::check_banning_type($userid);
                        if ($banning_type != 'earningonly' && $banning_type != 'both') {
                            $vk_info = get_user_meta($userid, '_rsvklike', true);
                            $postid = $post->ID;
                            if (!in_array($postid, (array) $vk_info)) {
                                ?>
                                        jQuery('.vk-like').tipsy({gravity: 's', live: 'true', fallback: '<?php echo do_shortcode(get_option('rs_social_message_for_vk')); ?>'});
                                <?php
                            }
                        }
                        ?>
                            });
                        </script>
                        <?php
                    }
                }
                if (get_option('rs_reward_point_enable_tipsy_social_rewards') == '1') {
                    if (get_option('rs_global_show_hide_social_tooltip_for_instagram') == '1') {
                        ?>
                        <script type="text/javascript">
                            jQuery(window).load(function () {
                        <?php
                        $userid = get_current_user_id();
                        $banning_type = FPRewardSystem::check_banning_type($userid);
                        if ($banning_type != 'earningonly' && $banning_type != 'both') {
                            $vk_info = get_user_meta($userid, '_rsinstagram', true);
                            $postid = $post->ID;
                            if (!in_array($postid, (array) $vk_info)) {
                                ?>
                                        jQuery('.instagram_button').tipsy({gravity: 's', live: 'true', fallback: '<?php echo do_shortcode(get_option('rs_social_message_for_instagram')); ?>'});
                                <?php
                            }
                        }
                        ?>
                            });
                        </script>
                        <?php
                    }
                }
            }
        }

        /* Function to insert FB Share Reward Points - Start */

        public static function update_reward_points_for_facebook_share() {
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                if (isset($_POST['state']) && ($_POST['postid']) && ($_POST['currentuserid'])) {
                    $postid = $_POST['postid'];
                    $currentuserid = $_POST['currentuserid'];
                    $getarrayids[] = $_POST['postid'];
                    $oldoption = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($_POST['currentuserid'], '_rsfacebookshare');
                    if (!empty($oldoption)) {
                        if (!in_array($_POST['postid'], $oldoption)) {
                            $mergedata = array_merge((array) $oldoption, $getarrayids);
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsfacebookshare', $mergedata);
                            if ($_POST['state'] == 'on') {
                                self::rs_insert_facebook_share_points($postid, $currentuserid);
                            }
                        } else {
                            _e('You already share this post', 'rewardsystem');
                        }
                    } else {
                        update_user_meta($_POST['currentuserid'], '_rsfacebookshare', $getarrayids);
                        if ($_POST['state'] == 'on') {
                            self::rs_insert_facebook_share_points($postid, $currentuserid);
                        }
                    }
                    echo "Ajax Call Successfully Triggered";
                }
                exit();
            }
        }

        public static function rs_insert_facebook_share_points($postid, $currentuserid) {
            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
            $item = array('qty' => '1');
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'fb_share');
            $event_slug = 'RPFS';
            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
            if ($enabledisablemaxpoints == 'yes') {
                $new_obj->check_point_restriction($restrictuserpoints, $rewardpoints, $pointsredeemed = 0, $event_slug, $currentuserid, $nomineeid = '', $referrer_id = '', $postid, $variationid = '0', $reasonindetail = '');
            } else {
                $equearnamt = RSPointExpiry::earning_conversion_settings($rewardpoints);
                $valuestoinsert = array('pointstoinsert' => $rewardpoints, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $currentuserid, 'referred_id' => '', 'product_id' => $postid, 'variation_id' => '0', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $rewardpoints, 'totalredeempoints' => 0);
                $new_obj->total_points_management($valuestoinsert);
            }
        }

        public static function add_shortcode_for_social_facebook_share($contents) {
            ob_start();
            global $post;
            $item = array('qty' => '1');
            $postid = $post->ID;
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'fb_share');
            echo $rewardpoints;
            $newcontentss = ob_get_clean();
            return $newcontentss;
        }

        /* Function to insert FB Share Reward Points - End */

        /* Function to insert OK.ru Reward Points - Start */

        public static function update_reward_points_for_ok() {
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                if (isset($_POST['state']) && ($_POST['postid']) && ($_POST['currentuserid'])) {
                    $postid = $_POST['postid'];
                    $currentuserid = $_POST['currentuserid'];
                    $getarrayids[] = $_POST['postid'];
                    $oldoption = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($_POST['currentuserid'], '_rsokfollow');
                    if (!empty($oldoption)) {
                        if (!in_array($_POST['postid'], $oldoption)) {
                            $mergedata = array_merge((array) $oldoption, $getarrayids);
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsokfollow', $mergedata);
                            if ($_POST['state'] == 'on') {
                                self::rs_insert_ok_follow_points($postid, $currentuserid);
                            }
                        } else {
                            _e('You already Shared this post', 'rewardsystem');
                        }
                    } else {
                        RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsokfollow', $getarrayids);
                        if ($_POST['state'] == 'on') {
                            self::rs_insert_ok_follow_points($postid, $currentuserid);
                        }
                    }
                    echo "Ajax Call Successfully Triggered";
                }
                do_action('fp_reward_point_for_ok_follow');
                exit();
            }
        }

        public static function rs_insert_ok_follow_points($postid, $currentuserid) {
            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
            $item = array('qty' => '1');
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'ok_follow');
            $event_slug = 'RPOK';
            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
            if ($enabledisablemaxpoints == 'yes') {
                $new_obj->check_point_restriction($restrictuserpoints, $rewardpoints, $pointsredeemed = 0, $event_slug, $currentuserid, $nomineeid = '', $referrer_id = '', $postid, $variationid = '0', $reasonindetail = '');
            } else {
                $equearnamt = RSPointExpiry::earning_conversion_settings($rewardpoints);
                $valuestoinsert = array('pointstoinsert' => $rewardpoints, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $currentuserid, 'referred_id' => '', 'product_id' => $postid, 'variation_id' => '0', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $rewardpoints, 'totalredeempoints' => 0);
                $new_obj->total_points_management($valuestoinsert);
            }
        }

        /* Function to insert Instagram Reward Points - Start */

        public static function update_reward_points_for_instagram() {
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                if (isset($_POST['state']) && ($_POST['postid']) && ($_POST['currentuserid'])) {
                    $postid = $_POST['postid'];
                    $currentuserid = $_POST['currentuserid'];
                    $getarrayids[] = $_POST['postid'];
                    $oldoption = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($_POST['currentuserid'], '_rsinstagram');
                    if (!empty($oldoption)) {
                        if (!in_array($_POST['postid'], $oldoption)) {
                            $mergedata = array_merge((array) $oldoption, $getarrayids);
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsinstagram', $mergedata);
                            if ($_POST['state'] == 'on') {
                                self::rs_insert_instagram_share_points($postid, $currentuserid);
                            }
                        } else {
                            _e('You already follow this profile', 'rewardsystem');
                        }
                    } else {
                        update_user_meta($_POST['currentuserid'], '_rsinstagram', $getarrayids);
                        if ($_POST['state'] == 'on') {
                            self::rs_insert_instagram_share_points($postid, $currentuserid);
                        }
                    }
                    echo "Ajax Call Successfully Triggered";
                }
                exit();
            }
        }

        public static function rs_insert_instagram_share_points($postid, $currentuserid) {
            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
            $item = array('qty' => '1');
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'instagram');
            $event_slug = 'RPIF';
            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
            if ($enabledisablemaxpoints == 'yes') {
                $new_obj->check_point_restriction($restrictuserpoints, $rewardpoints, $pointsredeemed = 0, $event_slug, $currentuserid, $nomineeid = '', $referrer_id = '', $postid, $variationid = '0', $reasonindetail = '');
            } else {
                $equearnamt = RSPointExpiry::earning_conversion_settings($rewardpoints);
                $valuestoinsert = array('pointstoinsert' => $rewardpoints, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $currentuserid, 'referred_id' => '', 'product_id' => $postid, 'variation_id' => '0', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $rewardpoints, 'totalredeempoints' => 0);
                $new_obj->total_points_management($valuestoinsert);
            }
        }

        public static function add_shortcode_for_social_instagram($contents) {
            ob_start();
            global $post;
            $item = array('qty' => '1');
            $postid = $post->ID;
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'instagram');
            echo $rewardpoints;
            $newcontentss = ob_get_clean();
            return $newcontentss;
        }

        /* Function to insert Instagram Reward Points - End */

        /* Function to insert FB Like Reward Points - Start */

        public static function update_reward_points_for_facebook_like() {
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                if (isset($_POST['state']) && ($_POST['postid']) && ($_POST['currentuserid'])) {
                    $postid = $_POST['postid'];
                    $currentuserid = $_POST['currentuserid'];
                    $getarrayids[] = $_POST['postid'];
                    $oldoption = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($_POST['currentuserid'], '_rsfacebooklikes');
                    if (!empty($oldoption)) {
                        if (!in_array($_POST['postid'], $oldoption)) {
                            $mergedata = array_merge((array) $oldoption, $getarrayids);
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsfacebooklikes', $mergedata);
                            if ($_POST['state'] == 'on') {
                                self::rs_insert_facebook_like_points($postid, $currentuserid);
                            }
                        } else {
                            _e('You already liked this post', 'rewardsystem');
                        }
                    } else {
                        update_user_meta($_POST['currentuserid'], '_rsfacebooklikes', $getarrayids);
                        if ($_POST['state'] == 'on') {
                            self::rs_insert_facebook_like_points($postid, $currentuserid);
                        }
                    }
                    if ($_POST['state'] == 'off') {
                        $getarrayunlikeids[] = $_POST['postid'];
                        $oldunlikeoption = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($_POST['currentuserid'], '_rsfacebookunlikes');
                        if (!empty($oldunlikeoption)) {
                            if (!in_array($_POST['postid'], $oldunlikeoption)) {
                                $mergedunlikedata = array_merge((array) $oldunlikeoption, $getarrayunlikeids);
                                RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsfacebookunlikes', $mergedunlikedata);
                                self::rs_insert_facebook_like_revised_points($postid, $currentuserid);
                            }
                        } else {
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsfacebookunlikes', $getarrayunlikeids);
                            self::rs_insert_facebook_like_revised_points($postid, $currentuserid);
                        }
                    }
                    echo "Ajax Call Successfully Triggered";
                }
                do_action('fp_reward_point_for_facebook_like');
                exit();
            }
        }

        public static function rs_insert_facebook_like_points($postid, $currentuserid) {
            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
            $item = array('qty' => '1');
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'fb_like');
            $event_slug = 'RPFL';
            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
            if ($enabledisablemaxpoints == 'yes') {
                $new_obj->check_point_restriction($restrictuserpoints, $rewardpoints, $pointsredeemed = 0, $event_slug, $currentuserid, $nomineeid = '', $referrer_id = '', $postid, $variationid = '0', $reasonindetail = '');
            } else {
                $equearnamt = RSPointExpiry::earning_conversion_settings($rewardpoints);
                $valuestoinsert = array('pointstoinsert' => $rewardpoints, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $currentuserid, 'referred_id' => '', 'product_id' => $postid, 'variation_id' => '0', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $rewardpoints, 'totalredeempoints' => 0);
                $new_obj->total_points_management($valuestoinsert);
            }
        }

        public static function rs_insert_facebook_like_revised_points($postid, $currentuserid) {
            $item = array('qty' => '1');
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'fb_like');
            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
            $event_slug = 'RVPFRPFL';
            $valuestoinsert = array('pointstoinsert' => 0, 'pointsredeemed' => $rewardpoints, 'event_slug' => $event_slug, 'equalearnamnt' => 0, 'equalredeemamnt' => 0, 'user_id' => $currentuserid, 'referred_id' => '', 'product_id' => $postid, 'variation_id' => '0', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => 0, 'totalredeempoints' => $rewardpoints);
            $new_obj->total_points_management($valuestoinsert);
        }

        public static function add_shortcode_for_social_facebook_like($contents) {
            ob_start();
            global $post;
            $item = array('qty' => '1');
            $postid = $post->ID;
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'fb_like');
            echo $rewardpoints;
            $newcontentss = ob_get_clean();
            return $newcontentss;
        }

        /* Function to insert FB Like Reward Points - End */

        /* Function to insert Twitter Follow Reward Points - Start */

        public static function update_reward_points_for_twitter_follow() {
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                if (isset($_POST['state']) && ($_POST['postid']) && ($_POST['currentuserid'])) {
                    $postid = $_POST['postid'];
                    $currentuserid = $_POST['currentuserid'];
                    $getarrayids[] = $_POST['postid'];
                    $oldoption = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($_POST['currentuserid'], '_rstwitterfollow');
                    if (!empty($oldoption)) {
                        if (!in_array($_POST['postid'], $oldoption)) {
                            $mergedata = array_merge((array) $oldoption, $getarrayids);
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rstwitterfollow', $mergedata);
                            if ($_POST['state'] == 'on') {
                                self::rs_insert_twitter_follow_points($postid, $currentuserid);
                            }
                        } else {
                            _e('You already Follow this post', 'rewardsystem');
                        }
                    } else {
                        RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rstwitterfollow', $getarrayids);
                        if ($_POST['state'] == 'on') {
                            self::rs_insert_twitter_follow_points($postid, $currentuserid);
                        }
                    }
                    echo "Ajax Call Successfully Triggered";
                }
                do_action('fp_reward_point_for_twitter_follow');
                exit();
            }
        }

        public static function rs_insert_twitter_follow_points($postid, $currentuserid) {
            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
            $item = array('qty' => '1');
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'twitter_follow');
            $event_slug = 'RPTF';
            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
            if ($enabledisablemaxpoints == 'yes') {
                $new_obj->check_point_restriction($restrictuserpoints, $rewardpoints, $pointsredeemed = 0, $event_slug, $currentuserid, $nomineeid = '', $referrer_id = '', $postid, $variationid = '0', $reasonindetail = '');
            } else {
                $equearnamt = RSPointExpiry::earning_conversion_settings($rewardpoints);
                $valuestoinsert = array('pointstoinsert' => $rewardpoints, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $currentuserid, 'referred_id' => '', 'product_id' => $postid, 'variation_id' => '0', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $rewardpoints, 'totalredeempoints' => 0);
                $new_obj->total_points_management($valuestoinsert);
            }
        }

        public static function add_shortcode_for_social_twitter_follow() {
            ob_start();
            global $post;
            $item = array('qty' => '1');
            $postid = $post->ID;
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'twitter_follow');
            echo $rewardpoints;
            $newcontentss = ob_get_clean();
            return $newcontentss;
        }

        public static function add_shortcode_for_social_ok_share() {
            ob_start();
            global $post;
            $item = array('qty' => '1');
            $postid = $post->ID;
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'ok_follow');
            echo $rewardpoints;
            $newcontentss = ob_get_clean();
            return $newcontentss;
        }

        /* Function to insert Twitter Follow Reward Points - End */

        /* Function to insert Twitter Tweet Reward Points - Start */

        public static function update_reward_points_for_twitter_tweet() {
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                if (isset($_POST['state']) && ($_POST['postid']) && ($_POST['currentuserid'])) {
                    $postid = $_POST['postid'];
                    $currentuserid = $_POST['currentuserid'];
                    $getarrayids[] = $_POST['postid'];
                    $oldoption = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($_POST['currentuserid'], '_rstwittertweet');
                    if (!empty($oldoption)) {
                        if (!in_array($_POST['postid'], $oldoption)) {
                            $mergedata = array_merge((array) $oldoption, $getarrayids);
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rstwittertweet', $mergedata);
                            if ($_POST['state'] == 'on') {
                                self::rs_insert_twitter_tweet_points($postid, $currentuserid);
                            }
                        } else {
                            _e('You already Tweet this post', 'rewardsystem');
                        }
                    } else {
                        RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rstwittertweet', $getarrayids);
                        if ($_POST['state'] == 'on') {
                            self::rs_insert_twitter_tweet_points($postid, $currentuserid);
                        }
                    }
                    echo "Ajax Call Successfully Triggered";
                }
                do_action('fp_reward_point_for_twitter_tweet');
                exit();
            }
        }

        public static function rs_insert_twitter_tweet_points($postid, $currentuserid) {
            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
            $item = array('qty' => '1');
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'twitter_tweet');
            $event_slug = 'RPTT';
            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
            if ($enabledisablemaxpoints == 'yes') {
                $new_obj->check_point_restriction($restrictuserpoints, $rewardpoints, $pointsredeemed = 0, $event_slug, $currentuserid, $nomineeid = '', $referrer_id = '', $postid, $variationid = '0', $reasonindetail = '');
            } else {
                $equearnamt = RSPointExpiry::earning_conversion_settings($rewardpoints);
                $valuestoinsert = array('pointstoinsert' => $rewardpoints, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $currentuserid, 'referred_id' => '', 'product_id' => $postid, 'variation_id' => '0', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $rewardpoints, 'totalredeempoints' => 0);
                $new_obj->total_points_management($valuestoinsert);
            }
        }

        public static function add_shortcode_for_social_twitter_tweet() {
            ob_start();
            global $post;
            $item = array('qty' => '1');
            $postid = $post->ID;
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'twitter_tweet');
            echo $rewardpoints;
            $newcontentss = ob_get_clean();
            return $newcontentss;
        }

        /* Function to insert Twitter Tweet Reward Points - End */

        /* Function to insert Google+1 Share Reward Points - Start */

        public static function update_reward_points_for_google_plus_share() {
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                if (isset($_POST['state']) && ($_POST['postid']) && ($_POST['currentuserid'])) {
                    $postid = $_POST['postid'];
                    $currentuserid = $_POST['currentuserid'];
                    $getarrayids[] = $_POST['postid'];
                    $oldoption = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($_POST['currentuserid'], '_rsgoogleshares');
                    if (!empty($oldoption)) {
                        if (!in_array($_POST['postid'], $oldoption)) {
                            $mergedata = array_merge((array) $oldoption, $getarrayids);
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsgoogleshares', $mergedata);
                            if ($_POST['state'] == 'on') {
                                self::rs_insert_google_plus_share_points($postid, $currentuserid);
                            }
                        } else {
                            _e('You already Shared this post on Goole+1', 'rewardsystem');
                        }
                    } else {
                        RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsgoogleshares', $getarrayids);
                        if ($_POST['state'] == 'on') {
                            self::rs_insert_google_plus_share_points($postid, $currentuserid);
                        }
                    }

                    if ($_POST['state'] == 'off') {
                        $getarrayunlikeids[] = $_POST['postid'];
                        $oldunlikeoption = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($_POST['currentuserid'], '_rsgoogleplusunlikes');
                        if (!empty($oldunlikeoption)) {
                            if (!in_array($_POST['postid'], $oldunlikeoption)) {
                                $mergedunlikedata = array_merge((array) $oldunlikeoption, $getarrayunlikeids);
                                RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsgoogleplusunlikes', $mergedunlikedata);
                                self::rs_insert_google_plus_share_revised_points($postid, $currentuserid);
                            }
                        } else {
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsgoogleplusunlikes', $getarrayunlikeids);
                            self::rs_insert_google_plus_share_revised_points($postid, $currentuserid);
                        }
                    }
                    echo "Ajax Call Successfully Triggered";
                }
                do_action('fp_reward_point_for_google_plus_share');
                exit();
            }
        }

        public static function rs_insert_google_plus_share_points($postid, $currentuserid) {
            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
            $item = array('qty' => '1');
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'g_plus');
            $event_slug = 'RPGPOS';
            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
            if ($enabledisablemaxpoints == 'yes') {
                $new_obj->check_point_restriction($restrictuserpoints, $rewardpoints, $pointsredeemed = 0, $event_slug, $currentuserid, $nomineeid = '', $referrer_id = '', $postid, $variationid = '0', $reasonindetail = '');
            } else {
                $equearnamt = RSPointExpiry::earning_conversion_settings($rewardpoints);
                $valuestoinsert = array('pointstoinsert' => $rewardpoints, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $currentuserid, 'referred_id' => '', 'product_id' => $postid, 'variation_id' => '0', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $rewardpoints, 'totalredeempoints' => 0);
                $new_obj->total_points_management($valuestoinsert);
            }
        }

        public static function rs_insert_google_plus_share_revised_points($postid, $currentuserid) {
            $item = array('qty' => '1');
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'g_plus');
            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
            $event_slug = 'RVPFRPGPOS';
            $valuestoinsert = array('pointstoinsert' => 0, 'pointsredeemed' => $rewardpoints, 'event_slug' => $event_slug, 'equalearnamnt' => 0, 'equalredeemamnt' => 0, 'user_id' => $currentuserid, 'referred_id' => '', 'product_id' => $postid, 'variation_id' => '0', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => 0, 'totalredeempoints' => $rewardpoints);
            $new_obj->total_points_management($valuestoinsert);
        }

        public static function add_shortcode_for_social_google_share($contents) {
            ob_start();
            global $post;
            $item = array('qty' => '1');
            $postid = $post->ID;
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'g_plus');
            echo $rewardpoints;
            $newcontentss = ob_get_clean();
            return $newcontentss;
        }

        /* Function to insert Google+1 Share Reward Points - End */

        /* Function to insert VK.Com like Reward Points - Start */

        public static function update_reward_points_for_vk_like() {
            $userid = get_current_user_id();
            $banning_type = FPRewardSystem::check_banning_type($userid);
            if ($banning_type != 'earningonly' && $banning_type != 'both') {
                if (isset($_POST['state']) && ($_POST['postid']) && ($_POST['currentuserid'])) {
                    $postid = $_POST['postid'];
                    $currentuserid = $_POST['currentuserid'];
                    $getarrayids[] = $_POST['postid'];
                    $oldoption = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($_POST['currentuserid'], '_rsvklike');
                    if (!empty($oldoption)) {
                        if (!in_array($_POST['postid'], $oldoption)) {
                            $mergedata = array_merge((array) $oldoption, $getarrayids);
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsvklike', $mergedata);
                            if ($_POST['state'] == 'on') {
                                self::rs_insert_vk_like_points($postid, $currentuserid);
                            }
                        } else {
                            _e("You have already liked this post on VK.Com", 'rewardsystem');
                        }
                    } else {
                        RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsvklike', $getarrayids);
                        if ($_POST['state'] == 'on') {
                            self::rs_insert_vk_like_points($postid, $currentuserid);
                        }
                    }

                    if ($_POST['state'] == 'off') {
                        $getarrayunlikeids[] = $_POST['postid'];
                        $oldunlikeoption = RSFunctionForSavingMetaValues::rewardsystem_get_user_meta($_POST['currentuserid'], '_rsvkunlikes');
                        if (!empty($oldunlikeoption)) {
                            if (!in_array($_POST['postid'], $oldunlikeoption)) {
                                $mergedunlikedata = array_merge((array) $oldunlikeoption, $getarrayunlikeids);
                                RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsvkunlikes', $mergedunlikedata);
                                self::rs_insert_vk_like_revised_points($postid, $currentuserid);
                            }
                        } else {
                            RSFunctionForSavingMetaValues::rewardsystem_update_user_meta($_POST['currentuserid'], '_rsvkunlikes', $getarrayunlikeids);
                            self::rs_insert_vk_like_revised_points($postid, $currentuserid);
                        }
                    }
                    echo "Ajax Call Successfully Triggered";
                }
                do_action('fp_reward_point_for_vk_like');
                exit();
            }
        }

        public static function rs_insert_vk_like_points($postid, $currentuserid) {
            $restrictuserpoints = get_option('rs_max_earning_points_for_user');
            $enabledisablemaxpoints = get_option('rs_enable_disable_max_earning_points_for_user');
            $item = array('qty' => '1');
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'vk_like');
            $event_slug = 'RPVL';
            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
            if ($enabledisablemaxpoints == 'yes') {
                $new_obj->check_point_restriction($restrictuserpoints, $rewardpoints, $pointsredeemed = 0, $event_slug, $currentuserid, $nomineeid = '', $referrer_id = '', $postid, $variationid = '0', $reasonindetail = '');
            } else {
                $equearnamt = RSPointExpiry::earning_conversion_settings($rewardpoints);
                $valuestoinsert = array('pointstoinsert' => $rewardpoints, 'pointsredeemed' => 0, 'event_slug' => $event_slug, 'equalearnamnt' => $equearnamt, 'equalredeemamnt' => 0, 'user_id' => $currentuserid, 'referred_id' => '', 'product_id' => $postid, 'variation_id' => '0', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => $rewardpoints, 'totalredeempoints' => 0);
                $new_obj->total_points_management($valuestoinsert);
            }
        }

        public static function rs_insert_vk_like_revised_points($postid, $currentuserid) {
            $item = array('qty' => '1');
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'vk_like');
            $new_obj = new RewardPointsOrder($order_id = 0, $apply_previous_order_points = 'no');
            $event_slug = 'RVPFRPVL';
            $valuestoinsert = array('pointstoinsert' => 0, 'pointsredeemed' => $rewardpoints, 'event_slug' => $event_slug, 'equalearnamnt' => 0, 'equalredeemamnt' => 0, 'user_id' => $currentuserid, 'referred_id' => '', 'product_id' => $postid, 'variation_id' => '0', 'reasonindetail' => '', 'nominee_id' => '', 'nominee_points' => '', 'totalearnedpoints' => 0, 'totalredeempoints' => $rewardpoints);
            $new_obj->total_points_management($valuestoinsert);
        }

        public static function add_shortcode_for_social_vk_like($contents) {
            ob_start();
            global $post;
            $item = array('qty' => '1');
            $postid = $post->ID;
            $rewardpoints = check_level_of_enable_reward_point($postid, $variationid = '0', $item, $checklevel = 'no', $referred_user = '', $getting_referrer = 'no', $socialreward = 'yes', $rewardfor = 'vk_like');
            echo $rewardpoints;
            $newcontentss = ob_get_clean();
            return $newcontentss;
        }

        /* Function to insert VK.Com like Reward Points - End */

        public static function add_enqueue_for_social_messages() {
            wp_register_script('wp_reward_tooltip', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/jquery.tipsy.js");
            wp_register_style('wp_reward_tooltip_style', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/css/tipsy.css");
            wp_register_script('wp_jscolor_rewards', REWARDSYSTEM_PLUGIN_DIR_URL . "admin/js/jscolor/jscolor.js");
            if (get_option('rs_reward_point_enable_tipsy_social_rewards') == '1') {
                wp_enqueue_script('wp_reward_tooltip');
            }
            wp_enqueue_script('wp_jscolor_rewards');
            wp_enqueue_style('wp_reward_tooltip_style');
        }

    }

    RSFunctionForSocialRewards::init();
}