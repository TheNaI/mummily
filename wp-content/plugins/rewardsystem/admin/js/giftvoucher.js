jQuery(function ($) {
    var giftvoucher = {
        init: function () {
            $(document.body).on('click', '.button', this.giftvoucherclick);
        },     
        giftvoucherclick: function (evt) {
            evt.preventDefault();       
            var redeemvouchercode = jQuery('#rs_redeem_voucher_code').val();
            var new_redeemvouchercode = redeemvouchercode.replace(/\s/g, '');            
            if (new_redeemvouchercode === '') {
                jQuery('.rs_redeem_voucher_error').html(giftvoucher_variable_js.error).fadeIn().delay(5000).fadeOut();
                return false;
            } else {                
                var data = {
                    action: 'rewardsystem_redeem_voucher_codes',
                    redeemvouchercode: new_redeemvouchercode,
                };
                $.ajax({
                    type: 'POST',
                    url: giftvoucher_variable_js.wp_ajax_url,
                    data: data,
                    dataType: 'html',
                    success: function (response) {
                        console.log(jQuery.parseHTML(response));
                        jQuery('.rs_redeem_voucher_success').html(jQuery.parseHTML(response)).fadeIn().delay(5000).fadeOut();
                        jQuery('#rs_redeem_voucher_code').val('');
                    },
                });

            }



        },
    };
    giftvoucher.init();
});




