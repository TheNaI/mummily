jQuery(function ($) {

    var wc_checkout_coupons = {
        init: function () {
            $(document.body).on('click', '.woocommerce-remove-coupon', this.remove_coupon);
            $( 'form.checkout_coupon' ).hide().submit( this.submit );
        },
        submit: function () {            
            var $form = $(this);            

            var data = {
                security: wc_checkout_params.apply_coupon_nonce,
                coupon_code: $form.find('input[name="coupon_code"]').val()
            };

            $.ajax({
                type: 'POST',
                url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
                data: data,
                success: function (code) {
                    $('.woocommerce-error, .woocommerce-message').remove();
                    $form.removeClass('processing').unblock();

                    if (code) {
                        $form.before(code);
                        $form.slideUp();

                        $(document.body).trigger('update_checkout', {update_shipping_method: false});
                        location.reload();
                    }
                },
                dataType: 'html'
            });

            return false;
        },
        remove_coupon: function (e) {
            e.preventDefault();
            $form = $('table.shop_table.cart').closest('form');
            var $datacoupon = $(e.target).data('coupon');
            //console.log($datacoupon);
            var data = {
                action: 'sumo_remove_coupon',
                coupon: $datacoupon,
            };
            $.ajax({
                url: checkoutscript_variable_js.wp_ajax_url,
                data: data,
                dataType: 'html',
                type: 'post',
                success: function (response) {
                     $('.sumo_reward_points_auto_redeem_error_message').remove();
                    common_syntax(response);

                }
            });

        }
    };


    var common_syntax = function ($node) {
        console.log($node);
        var $html = $.parseHTML($node, true);
        var $load_script = $($html).filter('div.sumo_reward_points_checkout_apply_discount');
        var $apply_discount1 = $('div.sumo_reward_points_checkout_apply_discount', $html).closest('form');
        console.log($load_script);
        $('div.woocommerce').prepend($apply_discount1);
        jQuery(".redeeemit").show();
    };

    wc_checkout_coupons.init();
});


