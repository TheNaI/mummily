jQuery(function ($) {
    var formforsendpoints = {
        init: function () {
            $(document.body).on('click', '#rs_send_points_submit_button', this.formforsendpointsclick);
            jQuery(".error").hide();
            jQuery(".success_info").hide();
        },
        formforsendpointsclick: function (evt) {
            evt.preventDefault();
            var $form = $(evt.target);
            var sendpoints_current_user_points = formforsendpoints_variable_js.currentuserpoint;
            var value = jQuery('#select_user_ids').val();
            var selecttype = formforsendpoints_variable_js.selecttype;
            if (selecttype === '2') {
                var value = jQuery('#select_user_ids').val();
            }
            var send_points = jQuery("#rs_total_reward_value_send").val();
            var restrictpoint = formforsendpoints_variable_js.limittosendreq;
            var restrict_point_enable = formforsendpoints_variable_js.sendpointlimit;
            if (restrict_point_enable == '1') {
                if (restrictpoint != '' && restrictpoint != '0') {
                    if (send_points > restrictpoint) {
                        jQuery('.error_greater_than_limit').css('color', 'red');
                        jQuery('.error_greater_than_limit').html(formforsendpoints_variable_js.limit_err);
                        jQuery(".error_greater_than_limit").fadeIn().delay(5000).fadeOut();
                        return false;
                    }
                }
            }
            if (value == '' || value == null) {
                jQuery('.error_empty_user').css('color', 'red');
                jQuery('.error_empty_user').html(formforsendpoints_variable_js.user_emty_err);
                jQuery(".error_empty_user").fadeIn().delay(5000).fadeOut();
                return false;
            }
            if (send_points == "") {
                jQuery('.error_point_empty').css('color', 'red');
                jQuery('.error_point_empty').html(formforsendpoints_variable_js.point_emp_err);
                jQuery(".error_point_empty").fadeIn().delay(5000).fadeOut();
                return false;
            } 
            if (isNaN(send_points)) {
                jQuery('.error_points_not_number').css('color', 'red');
                jQuery('.error_points_not_number').html(formforsendpoints_variable_js.point_not_num);
                jQuery(".error_points_not_number").fadeIn().delay(5000).fadeOut();
                return false;
            }
            if (send_points > sendpoints_current_user_points) {
                jQuery('.points_more_than_current_points').css('color', 'red');
                jQuery('.points_more_than_current_points').html(formforsendpoints_variable_js.errorforgreaterpoints);
                jQuery(".points_more_than_current_points").fadeIn().delay(5000).fadeOut();
                return false;
            }
            if (send_points < 0) {
                jQuery('.points_less_than_current_points').css('color', 'red');
                jQuery('.points_less_than_current_points').html(formforsendpoints_variable_js.errorforlesserpoints);
                jQuery(".points_less_than_current_points").fadeIn().delay(5000).fadeOut();
                return false;
            }
            var send_request_user_id = formforsendpoints_variable_js.user_id;
            var send_request_user_name = formforsendpoints_variable_js.username;
            var send_default_status = "Due";
            var send_form_params = ({
                action: "rs_send_form_value",
                points_to_send: send_points,
                selecteduserforsend: value,
                userid_of_send_request: send_request_user_id,
                username_of_send_request: send_request_user_name,
                sender_current_points: sendpoints_current_user_points,
                send_default_status: send_default_status,
            });
            jQuery.post(formforsendpoints_variable_js.wp_ajax_url, send_form_params, function (response) {
                jQuery('.error_empty_user').css('color', 'green');
                jQuery("#rs_total_reward_value_send").val('');
                jQuery(".success_info").fadeIn();
                jQuery(".success_info").html(formforsendpoints_variable_js.success_info);
                jQuery(".success_info").fadeOut(3000,function(){
                    location.reload(true);
                });
                console.log('Got this from the server: ' + response);
            });
        },
    };
    formforsendpoints.init();
});




