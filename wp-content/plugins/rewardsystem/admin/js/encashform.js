jQuery(function ($) {
    var encashform = {
        init: function () {
            $(document.body).on('click', '.cancelbutton', this.encashformclick);
        },
        encashformclick: function (evt) {
            evt.preventDefault();
            var status = jQuery(this).attr('data-status');
            var current_user_id = encashform1_variable_js.user_id;
            var id = jQuery(this).attr('data-id');
            var data = {
                action: "cancel_request_for_cash_back",
                status: status,
                current_user_id: current_user_id,
                id: id,
            };
            $.ajax({
                type: 'POST',
                url: encashform1_variable_js.wp_ajax_url,
                data: data,
                dataType: 'html',
                success: function (response) {
                    location.reload();
                    console.log('Success');
                    return false;
                },
            });


        },
    };
    encashform.init();
});




