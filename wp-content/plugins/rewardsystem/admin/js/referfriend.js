jQuery(function ($) {

    function checkemail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }
    
    function ajaxforreferfrnd() {
        var firstname = jQuery('#rs_friend_name').val();
        var friendemail = jQuery('#rs_friend_email').val();
        var friendmessage = jQuery('#rs_your_message').val();
        var friendsubject = jQuery('#rs_friend_subject').val();

        var data = {
            action: 'rs_refer_a_friend_ajax',
            friendname: firstname,
            friendemail: friendemail,
            friendsubject: friendsubject,
            friendmessage: friendmessage
        };

        $.ajax({
            type: 'POST',
            url: referfriend_variable_js.wp_ajax_url,
            data: data,
            dataType: 'html',
            success: function (response) {
                jQuery('.rs_notification_final').css('color', 'green');
                document.getElementById("rs_refer_a_friend_form").reset();
                jQuery('.rs_notification_final').html('Mail Sent Successfully');
                jQuery('.rs_notification_final').fadeOut(6000);
            },
        });
    }
    var referfriend = {
        init: function () {
            $(document.body).on('click', '.button-primary', this.referfriendclick);
        },
        referfriendclick: function (evt) {
            evt.preventDefault();
            var $form = $(evt.target);
            var firstname = jQuery('#rs_friend_name').val();
            var friendemail = jQuery('#rs_friend_email').val();
            var friendmessage = jQuery('#rs_your_message').val();
            var friendsubject = jQuery('#rs_friend_subject').val();

            if (firstname === '') {
                jQuery('#rs_friend_name').css('border', '2px solid red');
                jQuery('#rs_friend_name').parent().find('.rs_notification').css('color', 'red');
                jQuery('#rs_friend_name').parent().find('.rs_notification').html(referfriend_variable_js.refnameerrormsg).css('color', 'red');
                return false;
            } else {
                jQuery('#rs_friend_name').css('border', '');
                jQuery('#rs_friend_name').parent().find('.rs_notification').html('');
            }
            if (friendemail === '') {
                jQuery('#rs_friend_email').css('border', '2px solid red');
                jQuery('#rs_friend_email').parent().find('.rs_notification').css('color', 'red');
                jQuery('#rs_friend_email').parent().find('.rs_notification').html(referfriend_variable_js.refmailiderrormsg).css('color', 'red');
                return false;
            } else {
                jQuery('#rs_friend_email').css('border', '');
                jQuery('#rs_friend_email').parent().find('.rs_notification').html('');
            }
            var emailArray = friendemail.split(",");
            for (i = 0; i <= (emailArray.length - 1); i++) {
                if (checkemail(emailArray[i])) {
                    //Do what ever with the email.
                    jQuery('#rs_friend_email').css('border', '');
                    jQuery('#rs_friend_email').parent().find('.rs_notification').html('');
                } else {
                    jQuery('#rs_friend_email').css('border', '2px solid red');
                    jQuery('#rs_friend_email').parent().find('.rs_notification').css('color', 'red');
                    jQuery('#rs_friend_email').parent().find('.rs_notification').html(referfriend_variable_js.invalidemail);
                    return false;

                }
            }

            if (friendsubject === '') {
                jQuery('#rs_friend_subject').css('border', '2px solid red');
                jQuery('#rs_friend_subject').parent().find('.rs_notification').css('color', 'red');
                jQuery('#rs_friend_subject').parent().find('.rs_notification').html(referfriend_variable_js.subjecterror);
                return false;
            } else {
                jQuery('#rs_friend_subject').css('border', '');
                jQuery('#rs_friend_subject').parent().find('.rs_notification').html('');
            }
            if (friendmessage === '') {
                jQuery('#rs_your_message').css('border', '2px solid red');
                jQuery('#rs_your_message').parent().find('.rs_notification').css('color', 'red');
                jQuery('#rs_your_message').parent().find('.rs_notification').html(referfriend_variable_js.messageerror);
                return false;
            } else {
                jQuery('#rs_your_message').css('border', '');
                jQuery('#rs_your_message').parent().find('.rs_notification').html('');
            }
            var enableterms = referfriend_variable_js.enableterms;
            if (enableterms == '2') {
                var terms = jQuery('#rs_terms').is(':checked') ? 'yes' : 'no';
                if (terms == 'no') {
                    //jQuery('#rs_terms').parent().find('.rs_notification').css('color', 'red');
                    jQuery(".iagreeerror").css("display", "block");
                    jQuery(".iagreeerror").css("color", "red");
                    return false;
                }
            }
            ajaxforreferfrnd();
        },
    };
    referfriend.init();
});




