jQuery(function ($) {
    var language = socialbutton_variable_js.language;
    var wplang = socialbutton_variable_js.wplang;

    window.fbAsyncInit = function () {
        FB.init({
            appId: socialbutton_variable_js.facebook_id,
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
        if (language == '1') {
            js.src = "https://connect.facebook.net/en_US/sdk.js";
        } else {
            if (wplang == '') {
                js.src = "https://connect.facebook.net/en_US/sdk.js";
            } else {
                js.src = "https://connect.facebook.net/" + wplang + "/sdk.js";
            }
        }
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    console.log('script loaded');
    function postToFeed() {
        var product_name = socialbutton_variable_js.product_title;
        var description = socialbutton_variable_js.product_title;
        var share_image = socialbutton_variable_js.images;
        var share_url = socialbutton_variable_js.url;
        var share_capt = socialbutton_variable_js.caption;
        var obj = {
            method: 'feed',
            name: product_name,
            link: share_url,
            picture: share_image,
            caption: share_capt,
            description: description

        };
        function callback(response) {
            if (response != null) {
                alert('sucessfully posted');
                var dataparam = ({
                    action: 'rssocialfacebooksharecallback',
                    state: 'on',
                    postid: socialbutton_variable_js.product_id,
                    currentuserid: socialbutton_variable_js.user_id,
                });
                $.ajax({
                    type: 'POST',
                    url: socialbutton_variable_js.wp_ajax_url,
                    data: dataparam,
                    dataType: 'html',
                    success: function (response) {
                        if (response == "You already share this postAjax Call Successfully Triggered") {
                            jQuery('.social_promotion_success_message').html(socialbutton_variable_js.unsucess_msg);
                        } else {
                            jQuery('.social_promotion_success_message').html(socialbutton_variable_js.msg_social);
                        }

                        jQuery('.social_promotion_success_message').fadeOut(6000);
                    }
                });

            } else {
                alert('cancel');
            }

        }
        FB.ui(obj, callback);
    }
    var socialbutton = {
        init: function () {
            $(document.body).on('click', '.share_wrapper1', this.socialbuttonclick);
        },
        socialbuttonclick: function (evt) {
            evt.preventDefault();
            postToFeed();
            return false;
        }
    };
    socialbutton.init();
});




