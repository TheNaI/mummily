jQuery(function ($) {
    var encashform = {
        init: function () {
            $(document.body).on('click', '#rs_encash_submit_button', this.encashformsubmit);
            jQuery(".error").hide();
            jQuery(".success_info").hide();            
            var name = encashform_variable_js.selectpaymentmethod;
            if (name == '1') {
                jQuery(".rs_encash_payment_method").hide();
                jQuery(".rs_encash_custom_payment_option_value").hide();
            }
            if (name == '2') {
                jQuery(".rs_encash_paypal_address").hide();
                jQuery(".rs_encash_payment_method").hide();
            }
            if (name == '3') {
                jQuery(".rs_encash_custom_payment_option_value").hide();
                jQuery(".rs_encash_payment_method").change(function () {
                    jQuery(".rs_encash_paypal_address").toggle();
                    jQuery(".rs_encash_custom_payment_option_value").toggle();
                    jQuery("#paypal_email_empty_error").hide();
                    jQuery("#paypal_custom_option_empty_error").hide();
                });
            }
        },
        encashformsubmit: function (evt) {
            evt.preventDefault();
            var $form = $(evt.target);         
            var encash_current_user_points = encashform_variable_js.currentuserpoint;
            var minimum_points_to_encash = encashform_variable_js.minimumpointforencash;
            var maximum_points_to_encash = encashform_variable_js.maximumpointforencash;
            var name=encashform_variable_js.selectpaymentmethod;
            var encash_points = jQuery("#rs_encash_points_value").val();
            var encash_points_validated = /^[0-9\b]+$/.test(encash_points);
            if (encash_points == "") {
                jQuery("#points_empty_error").fadeIn().delay(5000).fadeOut();
                return false;
            } else {

                jQuery("#points_empty_error").hide();
                if (encash_points_validated == false) {
                    jQuery("#points_number_error").fadeIn().delay(5000).fadeOut();
                    return false;
                } else {
                    jQuery("#points_number_error").hide();
                    if (Number(encash_points) > Number(encash_current_user_points)) {
                        jQuery("#points_greater_than_earnpoints_error").fadeIn().delay(5000).fadeOut();
                        return false;
                    } else {
                            if ((Number(encash_points) >= Number(minimum_points_to_encash)) && (Number(encash_points) <= Number(maximum_points_to_encash))) {
                                jQuery("#points_greater_than_earnpoints_error").hide();
                                jQuery("#currentpoints_lesser_than_minimumpoints_error").hide();
                                jQuery("#points_lesser_than_minpoints_error").hide();
                                jQuery("#rs_error_message_points_lesser_than_minimum_points").hide();
                                jQuery("#points_greater_than_maxpoints_error").hide();
                                var points_value = encashform_variable_js.redeempointforcashback;
                                var amount_value = encashform_variable_js.redeempointvalueforcashback;
                                var conversion_step1 = encash_points / points_value;
                                var currency_converted_value = conversion_step1 * amount_value;
                            } else {
                                jQuery("#points_lesser_than_minpoints_error").fadeIn().delay(5000).fadeOut();
                                return false;
                            }
                        }
                }
            }
            var reason_to_encash = jQuery("#rs_encash_points_reason").val();
            if (reason_to_encash == "") {
                jQuery("#reason_empty_error").fadeIn().delay(5000).fadeOut();
                return false;
            } else {
                jQuery("#reason_empty_error").hide();
            }

            if (name === '2') {
                var encash_selected_option = 'encash_through_custom_payment';
            } else {
                var encash_selected_option = jQuery("#rs_encash_payment_method").val();
            }
            if (encash_selected_option == "encash_through_paypal_method") {
                if (name == '1' || name == '3') {
                    var encash_paypal_email = jQuery("#rs_encash_paypal_address").val();
                    var encash_paypal_email_validated = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(encash_paypal_email);
                    if (encash_paypal_email == "") {
                        jQuery("#paypal_email_empty_error").fadeIn().delay(5000).fadeOut();
                        return false;
                    } else {
                        jQuery("#paypal_email_empty_error").hide();
                        if (encash_paypal_email_validated == false) {
                            jQuery("#paypal_email_format_error").fadeIn().delay(5000).fadeOut();
                            return false;
                        } else {
                            jQuery("#paypal_email_format_error").hide();
                        }
                    }
                }
            } else {
                var encash_custom_option = jQuery("#rs_encash_custom_payment_option_value").val();
                if (name == '2' || name == '3') {
                    if (encash_custom_option == "") {
                        jQuery("#paypal_custom_option_empty_error").fadeIn().delay(5000).fadeOut();
                        return false;
                    } else {
                        jQuery("#paypal_custom_option_empty_error").hide();
                    }
                }
            }
            jQuery(".success_info").show();
            jQuery(".success_info").fadeOut(3000);
            jQuery("#encashing_form")[0].reset();
            jQuery(".rs_encash_custom_payment_option_value").hide();
            if (name == '1') {
                jQuery(".rs_encash_paypal_address").show();
            }
            if (name == '2') {
                jQuery(".rs_encash_custom_payment_option_value").show();
            }
            if (name == '3') {
                jQuery(".rs_encash_paypal_address").show();
            }

            var encash_request_user_id = encashform_variable_js.user_id;
            var encash_request_user_name = encashform_variable_js.username;
            var encash_default_status = "Due";
            var encash_form_params = ({
                action: "rs_encash_form_value",
                points_to_encash: encash_points,
                reason_to_encash: reason_to_encash,
                payment_method: encash_selected_option,
                paypal_email_id: encash_paypal_email,
                custom_payment_details: encash_custom_option,
                userid_of_encash_request: encash_request_user_id,
                username_of_encash_request: encash_request_user_name,
                encasher_current_points: encash_current_user_points,
                converted_value_of_points: currency_converted_value,
                encash_default_status: encash_default_status,
            });
            jQuery.post(encashform_variable_js.wp_ajax_url, encash_form_params, function (response) {
                console.log('Got this from the server: ' + response);
            });
            return false;
        },
    };
    encashform.init();
});




