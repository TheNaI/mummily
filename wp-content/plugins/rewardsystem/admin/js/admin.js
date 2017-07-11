/*
 * Keyup Validation in Product Settings
 */
jQuery(function () {

    jQuery('body').on('blur', rewardsystem.field_ids, function () {
        jQuery('.wc_error_tip').fadeOut('100', function () {
            jQuery(this).remove();
        });

        return this;
    });

    jQuery('body').on('keyup change', rewardsystem.field_ids, function () {
        var value = jQuery(this).val();
        console.log(woocommerce_admin.i18n_mon_decimal_error);
        var regex = new RegExp("[^\+0-9\%.\\" + woocommerce_admin.mon_decimal_point + "]+", "gi");
        var newvalue = value.replace(regex, '');

        if (value !== newvalue) {
            jQuery(this).val(newvalue);
            if (jQuery(this).parent().find('.wc_error_tip').size() == 0) {
                jQuery(this).after('<div class="wc_error_tip">' + woocommerce_admin.i18n_mon_decimal_error + " Negative Values are not allowed" + '</div>');
                jQuery('.wc_error_tip')
                        .css('left', offset.left + jQuery(this).width() - (jQuery(this).width() / 2) - (jQuery('.wc_error_tip').width() / 2))
                        .css('top', offset.top + jQuery(this).height())
                        .fadeIn('100');
            }
        }
        return this;
    });



    jQuery("body").click(function () {
        jQuery('.wc_error_tip').fadeOut('100', function () {
            jQuery(this).remove();
        });

    });

    if (jQuery('#rs_reward_signup_after_first_purchase').is(':checked') == true) {
        jQuery('#rs_signup_points_with_purchase_points').closest('tr').show();
    } else {
        jQuery('#rs_signup_points_with_purchase_points').closest('tr').hide();
    }

    jQuery('#rs_reward_signup_after_first_purchase').change(function () {
        if (jQuery('#rs_reward_signup_after_first_purchase').is(':checked') == true) {
            jQuery('#rs_signup_points_with_purchase_points').closest('tr').show();
        } else {
            jQuery('#rs_signup_points_with_purchase_points').closest('tr').hide();
        }
    });

    /* Show/Hide Product Fields - Start */

    jQuery('#_rewardsystem__points_based_on_conversion').attr('readonly', 'true');
    if (jQuery('#_rewardsystemcheckboxvalue').val() == 'no') {
        jQuery('.show_if_enable').parent().hide();
    } else {
        jQuery('.show_if_enable').parent().show();
        if (jQuery('.rewardsystem_options').val() === '') {
            jQuery('._rewardsystempercent_field').css('display', 'none');
            jQuery('._rewardsystempoints_field').css('display', 'none');
        } else if (jQuery('.rewardsystem_options').val() === '1') {
            jQuery('._rewardsystempercent_field').css('display', 'none');
            jQuery('._rewardsystempoints_field').css('display', 'block');
        } else {
            jQuery('._rewardsystempercent_field').css('display', 'block');
            jQuery('._rewardsystempoints_field').css('display', 'none');
        }

        jQuery('.rewardsystem_options').change(function () {
            if (jQuery(this).val() === '') {
                jQuery('._rewardsystempercent_field').css('display', 'none');
                jQuery('._rewardsystempoints_field').css('display', 'none');
            } else if (jQuery(this).val() === '1') {
                jQuery('._rewardsystempercent_field').css('display', 'none');
                jQuery('._rewardsystempoints_field').css('display', 'block');
            } else {
                jQuery('._rewardsystempercent_field').css('display', 'block');
                jQuery('._rewardsystempoints_field').css('display', 'none');
            }

        });
        if (jQuery('.referral_rewardsystem_options_get').val() === '') {

            jQuery('._referralrewardsystempoints_for_getting_referred_field').css('display', 'none');
            jQuery('._referralrewardsystempercent_for_getting_referred_field').css('display', 'none');


        } else if (jQuery('.referral_rewardsystem_options_get').val() === '1') {

            jQuery('._referralrewardsystempoints_for_getting_referred_field').css('display', 'block');
            jQuery('._referralrewardsystempercent_for_getting_referred_field').css('display', 'none');

        } else {

            jQuery('._referralrewardsystempoints_for_getting_referred_field').css('display', 'none');
            jQuery('._referralrewardsystempercent_for_getting_referred_field').css('display', 'block');

        }

        if (jQuery('.referral_rewardsystem_options').val() === '') {
            jQuery('._referralrewardsystempercent_field').css('display', 'none');
            jQuery('._referralrewardsystempoints_field').css('display', 'none');
        } else if (jQuery('.referral_rewardsystem_options').val() === '1') {
            jQuery('._referralrewardsystempercent_field').css('display', 'none');
            jQuery('._referralrewardsystempoints_field').css('display', 'block');
        } else {
            jQuery('._referralrewardsystempercent_field').css('display', 'block');
            jQuery('._referralrewardsystempoints_field').css('display', 'none');
        }


        jQuery('.referral_rewardsystem_options').change(function () {
            if (jQuery(this).val() === '') {
                jQuery('._referralrewardsystempercent_field').css('display', 'none');
                jQuery('._referralrewardsystempoints_field').css('display', 'none');
            } else if (jQuery(this).val() === '1') {
                jQuery('._referralrewardsystempercent_field').css('display', 'none');
                jQuery('._referralrewardsystempoints_field').css('display', 'block');
            } else {
                jQuery('._referralrewardsystempercent_field').css('display', 'block');
                jQuery('._referralrewardsystempoints_field').css('display', 'none');
            }


        });
        jQuery('.referral_rewardsystem_options_get').change(function () {
            if (jQuery(this).val() === '') {

                jQuery('._referralrewardsystempoints_for_getting_referred_field').css('display', 'none');
                jQuery('._referralrewardsystempercent_for_getting_referred_field').css('display', 'none');

            } else if (jQuery(this).val() === '1') {

                jQuery('._referralrewardsystempoints_for_getting_referred_field').css('display', 'block');
                jQuery('._referralrewardsystempercent_for_getting_referred_field').css('display', 'none');

            } else {

                jQuery('._referralrewardsystempoints_for_getting_referred_field').css('display', 'none');
                jQuery('._referralrewardsystempercent_for_getting_referred_field').css('display', 'block');

            }


        });
    }

    jQuery('#_rewardsystemcheckboxvalue').change(function () {
        if (jQuery(this).val() == 'no') {
            jQuery('.show_if_enable').parent().hide();
        } else {

            jQuery('.show_if_enable').parent().show();
            if (jQuery('.rewardsystem_options').val() === '') {
                jQuery('._rewardsystempercent_field').css('display', 'none');
                jQuery('._rewardsystempoints_field').css('display', 'none');
            } else if (jQuery('.rewardsystem_options').val() === '1') {
                jQuery('._rewardsystempercent_field').css('display', 'none');
                jQuery('._rewardsystempoints_field').css('display', 'block');
            } else {
                jQuery('._rewardsystempercent_field').css('display', 'block');
                jQuery('._rewardsystempoints_field').css('display', 'none');
            }


            jQuery('.rewardsystem_options').change(function () {
                if (jQuery(this).val() === '') {
                    jQuery('._rewardsystempercent_field').css('display', 'none');
                    jQuery('._rewardsystempoints_field').css('display', 'none');
                } else if (jQuery(this).val() === '1') {
                    jQuery('._rewardsystempercent_field').css('display', 'none');
                    jQuery('._rewardsystempoints_field').css('display', 'block');
                } else {
                    jQuery('._rewardsystempercent_field').css('display', 'block');
                    jQuery('._rewardsystempoints_field').css('display', 'none');
                }

            });
            if (jQuery('.referral_rewardsystem_options_get').val() === '') {

                jQuery('._referralrewardsystempoints_for_getting_referred_field').css('display', 'none');
                jQuery('._referralrewardsystempercent_for_getting_referred_field').css('display', 'none');

            } else if (jQuery('.referral_rewardsystem_options_get').val() === '1') {

                jQuery('._referralrewardsystempoints_for_getting_referred_field').css('display', 'block');
                jQuery('._referralrewardsystempercent_for_getting_referred_field').css('display', 'none');

            } else {

                jQuery('._referralrewardsystempoints_for_getting_referred_field').css('display', 'none');
                jQuery('._referralrewardsystempercent_for_getting_referred_field').css('display', 'block');

            }
            if (jQuery('.referral_rewardsystem_options').val() === '') {
                jQuery('._referralrewardsystempercent_field').css('display', 'none');
                jQuery('._referralrewardsystempoints_field').css('display', 'none');
            } else if (jQuery('.referral_rewardsystem_options').val() === '1') {
                jQuery('._referralrewardsystempercent_field').css('display', 'none');
                jQuery('._referralrewardsystempoints_field').css('display', 'block');
            } else {
                jQuery('._referralrewardsystempercent_field').css('display', 'block');
                jQuery('._referralrewardsystempoints_field').css('display', 'none');
            }

            jQuery('.referral_rewardsystem_options').change(function () {
                if (jQuery(this).val() === '') {
                    jQuery('._referralrewardsystempercent_field').css('display', 'none');
                    jQuery('._referralrewardsystempoints_field').css('display', 'none');
                } else if (jQuery(this).val() === '1') {
                    jQuery('._referralrewardsystempercent_field').css('display', 'none');
                    jQuery('._referralrewardsystempoints_field').css('display', 'block');
                } else {
                    jQuery('._referralrewardsystempercent_field').css('display', 'block');
                    jQuery('._referralrewardsystempoints_field').css('display', 'none');
                }

            });
            jQuery('.referral_rewardsystem_options_get').change(function () {
                if (jQuery(this).val() === '') {

                    jQuery('._referralrewardsystempoints_for_getting_referred_field').css('display', 'none');
                    jQuery('._referralrewardsystempercent_for_getting_referred_field').css('display', 'none');

                } else if (jQuery(this).val() === '1') {

                    jQuery('._referralrewardsystempoints_for_getting_referred_field').css('display', 'block');
                    jQuery('._referralrewardsystempercent_for_getting_referred_field').css('display', 'none');

                } else {

                    jQuery('._referralrewardsystempoints_for_getting_referred_field').css('display', 'none');
                    jQuery('._referralrewardsystempercent_for_getting_referred_field').css('display', 'block');

                }

            });

        }
    });

    /* Show/Hide Product Fields - End */

    /*Social Reward Points Show/Hide - Start*/

    if (jQuery('#_socialrewardsystemcheckboxvalue').val() === 'no') {
        jQuery('.show_if_social_enable').closest('p').hide();
    } else {
        jQuery('.show_if_social_enable').parent().show();

        /* Social Reward System for facebook */
        if (jQuery('.social_rewardsystem_options_facebook').val() === '') {
            jQuery('._socialrewardsystempoints_facebook_field').css('display', 'none');
            jQuery('._socialrewardsystempercent_facebook_field').css('display', 'none');
        } else if (jQuery('.social_rewardsystem_options_facebook').val() === '1') {
            jQuery('._socialrewardsystempercent_facebook_field').css('display', 'none');
            jQuery('._socialrewardsystempoints_facebook_field').css('display', 'block');
        } else {
            jQuery('._socialrewardsystempercent_facebook_field').css('display', 'block');
            jQuery('._socialrewardsystempoints_facebook_field').css('display', 'none');
        }

        /* On Change Event Triggering for Social Rewards Facebook */
        jQuery('.social_rewardsystem_options_facebook').change(function () {
            if (jQuery(this).val() === '') {
                jQuery('._socialrewardsystempoints_facebook_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_facebook_field').css('display', 'none');
            } else if (jQuery(this).val() === '1') {
                jQuery('._socialrewardsystempercent_facebook_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_facebook_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_facebook_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_facebook_field').css('display', 'none');
            }
        });


        /* Social Reward System for facebook */
        if (jQuery('._social_rewardsystem_options_facebook_share').val() === '') {
            jQuery('._socialrewardsystempoints_facebook_share_field').css('display', 'none');
            jQuery('._socialrewardsystempercent_facebook_share_field').css('display', 'none');
        } else if (jQuery('._social_rewardsystem_options_facebook_share').val() === '1') {
            jQuery('._socialrewardsystempercent_facebook_share_field').css('display', 'none');
            jQuery('._socialrewardsystempoints_facebook_share_field').css('display', 'block');
        } else {
            jQuery('._socialrewardsystempercent_facebook_share_field').css('display', 'block');
            jQuery('._socialrewardsystempoints_facebook_share_field').css('display', 'none');
        }

        /* On Change Event Triggering for Social Rewards Facebook */
        jQuery('._social_rewardsystem_options_facebook_share').change(function () {
            if (jQuery(this).val() === '') {
                jQuery('._socialrewardsystempoints_facebook_share_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_facebook_share_field').css('display', 'none');
            } else if (jQuery(this).val() === '1') {
                jQuery('._socialrewardsystempercent_facebook_share_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_facebook_share_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_facebook_share_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_facebook_share_field').css('display', 'none');
            }
        });


        /* Social Reward System for twitter */
        if (jQuery('.social_rewardsystem_options_twitter').val() === '') {
            jQuery('._socialrewardsystempoints_twitter_field').css('display', 'none');
            jQuery('._socialrewardsystempercent_twitter_field').css('display', 'none');
        } else if (jQuery('.social_rewardsystem_options_twitter').val() === '1') {
            jQuery('._socialrewardsystempercent_twitter_field').css('display', 'none');
            jQuery('._socialrewardsystempoints_twitter_field').css('display', 'block');
        } else {
            jQuery('._socialrewardsystempercent_twitter_field').css('display', 'block');
            jQuery('._socialrewardsystempoints_twitter_field').css('display', 'none');
        }

        /* On Change Event Triggering for Social Rewards twitter */
        jQuery('.social_rewardsystem_options_twitter').change(function () {
            if (jQuery(this).val() === '') {
                jQuery('._socialrewardsystempoints_twitter_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_twitter_field').css('display', 'none');
            } else if (jQuery(this).val() === '1') {
                jQuery('._socialrewardsystempercent_twitter_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_twitter_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_twitter_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_twitter_field').css('display', 'none');
            }
        });

        /* Social Reward System for Google+ */
        if (jQuery('.social_rewardsystem_options_google').val() === '') {
            jQuery('._socialrewardsystempoints_google_field').css('display', 'none');
            jQuery('._socialrewardsystempercent_google_field').css('display', 'none');
        } else if (jQuery('.social_rewardsystem_options_google').val() === '1') {
            jQuery('._socialrewardsystempercent_google_field').css('display', 'none');
            jQuery('._socialrewardsystempoints_google_field').css('display', 'block');
        } else {
            jQuery('._socialrewardsystempercent_google_field').css('display', 'block');
            jQuery('._socialrewardsystempoints_google_field').css('display', 'none');
        }

        /* On Change Event Triggering for Social Rewards Google+ */
        jQuery('.social_rewardsystem_options_google').change(function () {
            if (jQuery(this).val() === '') {
                jQuery('._socialrewardsystempoints_google_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_google_field').css('display', 'none');
            } else if (jQuery(this).val() === '1') {
                jQuery('._socialrewardsystempercent_google_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_google_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_google_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_google_field').css('display', 'none');
            }
        });

        /* Social Reward System for VK */
        if (jQuery('.social_rewardsystem_options_vk').val() === '') {
            jQuery('._socialrewardsystempoints_vk_field').css('display', 'none');
            jQuery('._socialrewardsystempercent_vk_field').css('display', 'none');
        } else if (jQuery('.social_rewardsystem_options_vk').val() === '1') {
            jQuery('._socialrewardsystempercent_vk_field').css('display', 'none');
            jQuery('._socialrewardsystempoints_vk_field').css('display', 'block');
        } else {
            jQuery('._socialrewardsystempercent_vk_field').css('display', 'block');
            jQuery('._socialrewardsystempoints_vk_field').css('display', 'none');
        }

        /* On Change Event Triggering for Social Rewards VK */
        jQuery('.social_rewardsystem_options_vk').change(function () {
            if (jQuery(this).val() === '') {
                jQuery('._socialrewardsystempoints_vk_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_vk_field').css('display', 'none');
            } else if (jQuery(this).val() === '1') {
                jQuery('._socialrewardsystempercent_vk_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_vk_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_vk_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_vk_field').css('display', 'none');
            }
        });
        if (jQuery('._social_rewardsystem_options_instagram').val() === '') {
            jQuery('._socialrewardsystempoints_instagram_field').css('display', 'none');
            jQuery('._socialrewardsystempercent_instagram_field').css('display', 'none');
        } else if (jQuery('._social_rewardsystem_options_instagram').val() === '1') {
            jQuery('._socialrewardsystempercent_instagram_field').css('display', 'none');
            jQuery('._socialrewardsystempoints_instagram_field').css('display', 'block');
        } else {
            jQuery('._socialrewardsystempercent_instagram_field').css('display', 'block');
            jQuery('._socialrewardsystempoints_instagram_field').css('display', 'none');
        }

        /* On Change Event Triggering for Social Rewards VK */
        jQuery('._social_rewardsystem_options_instagram').change(function () {
            if (jQuery(this).val() === '') {
                jQuery('._socialrewardsystempoints_instagram_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_instagram_field').css('display', 'none');
            } else if (jQuery(this).val() === '1') {
                jQuery('._socialrewardsystempercent_instagram_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_instagram_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_instagram_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_instagram_field').css('display', 'none');
            }
        });

        if (jQuery('._social_rewardsystem_options_ok_follow').val() === '') {
            jQuery('._socialrewardsystempoints_ok_follow_field').css('display', 'none');
            jQuery('._socialrewardsystempercent_ok_follow_field').css('display', 'none');
        } else if (jQuery('._social_rewardsystem_options_ok_follow').val() === '1') {
            jQuery('._socialrewardsystempercent_ok_follow_field').css('display', 'none');
            jQuery('._socialrewardsystempoints_ok_follow_field').css('display', 'block');
        } else {
            jQuery('._socialrewardsystempercent_ok_follow_field').css('display', 'block');
            jQuery('._socialrewardsystempoints_ok_follow_field').css('display', 'none');
        }

        /* On Change Event Triggering for Social Rewards VK */
        jQuery('._social_rewardsystem_options_ok_follow').change(function () {
            if (jQuery(this).val() === '') {
                jQuery('._socialrewardsystempoints_ok_follow_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_ok_follow_field').css('display', 'none');
            } else if (jQuery(this).val() === '1') {
                jQuery('._socialrewardsystempercent_ok_follow_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_ok_follow_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_ok_follow_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_ok_follow_field').css('display', 'none');
            }
        });

    }

    jQuery('#_socialrewardsystemcheckboxvalue').change(function () {
        if (jQuery(this).val() == 'no') {
            jQuery('.show_if_social_enable').parent().hide();
        } else {
            jQuery('.show_if_social_enable').parent().show();

            /* Social Reward System for facebook */
            if (jQuery('.social_rewardsystem_options_facebook').val() === '') {
                jQuery('._socialrewardsystempoints_facebook_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_facebook_field').css('display', 'none');
            } else if (jQuery('.social_rewardsystem_options_facebook').val() === '1') {
                jQuery('._socialrewardsystempercent_facebook_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_facebook_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_facebook_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_facebook_field').css('display', 'none');
            }

            /* On Change Event Triggering for Social Rewards Facebook */
            jQuery('.social_rewardsystem_options_facebook').change(function () {
                if (jQuery(this).val() === '') {
                    jQuery('._socialrewardsystempoints_facebook_field').css('display', 'none');
                    jQuery('._socialrewardsystempercent_facebook_field').css('display', 'none');
                } else if (jQuery(this).val() === '1') {
                    jQuery('._socialrewardsystempercent_facebook_field').css('display', 'none');
                    jQuery('._socialrewardsystempoints_facebook_field').css('display', 'block');
                } else {
                    jQuery('._socialrewardsystempercent_facebook_field').css('display', 'block');
                    jQuery('._socialrewardsystempoints_facebook_field').css('display', 'none');
                }
            });

            if (jQuery('._social_rewardsystem_options_facebook_share').val() === '') {

                jQuery('._socialrewardsystempoints_facebook_share_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_facebook_share_field').css('display', 'none');
            } else if (jQuery('._social_rewardsystem_options_facebook_share').val() === '1') {
                jQuery('._socialrewardsystempercent_facebook_share_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_facebook_share_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_facebook_share_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_facebook_share_field').css('display', 'none');
            }

            /* On Change Event Triggering for Social Rewards Facebook */
            jQuery('._social_rewardsystem_options_facebook_share').change(function () {
                if (jQuery(this).val() === '') {
                    jQuery('._socialrewardsystempoints_facebook_share_field').css('display', 'none');
                    jQuery('._socialrewardsystempercent_facebook_share_field').css('display', 'none');
                } else if (jQuery(this).val() === '1') {
                    jQuery('._socialrewardsystempercent_facebook_share_field').css('display', 'none');
                    jQuery('._socialrewardsystempoints_facebook_share_field').css('display', 'block');
                } else {
                    jQuery('._socialrewardsystempercent_facebook_share_field').css('display', 'block');
                    jQuery('._socialrewardsystempoints_facebook_share_field').css('display', 'none');
                }
            });

            /* Social Reward System for twitter */
            if (jQuery('.social_rewardsystem_options_twitter').val() === '') {
                jQuery('._socialrewardsystempoints_twitter_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_twitter_field').css('display', 'none');
            } else if (jQuery('.social_rewardsystem_options_twitter').val() === '1') {
                jQuery('._socialrewardsystempercent_twitter_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_twitter_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_twitter_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_twitter_field').css('display', 'none');
            }

            /* On Change Event Triggering for Social Rewards twitter */
            jQuery('.social_rewardsystem_options_twitter').change(function () {
                if (jQuery(this).val() === '') {
                    jQuery('._socialrewardsystempoints_twitter_field').css('display', 'none');
                    jQuery('._socialrewardsystempercent_twitter_field').css('display', 'none');
                } else if (jQuery(this).val() === '1') {
                    jQuery('._socialrewardsystempercent_twitter_field').css('display', 'none');
                    jQuery('._socialrewardsystempoints_twitter_field').css('display', 'block');
                } else {
                    jQuery('._socialrewardsystempercent_twitter_field').css('display', 'block');
                    jQuery('._socialrewardsystempoints_twitter_field').css('display', 'none');
                }
            });

            if (jQuery('._social_rewardsystem_options_twitter_follow').val() === '') {
                jQuery('._socialrewardsystempoints_twitter_follow_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_twitter_follow_field').css('display', 'none');
            } else if (jQuery('._social_rewardsystem_options_twitter_follow').val() === '1') {
                jQuery('._socialrewardsystempercent_twitter_follow_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_twitter_follow_field').css('display', 'block');
            } else if (jQuery('._social_rewardsystem_options_twitter_follow').val() === '2') {
                jQuery('._socialrewardsystempercent_twitter_follow_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_twitter_follow_field').css('display', 'none');
            }

            /* On Change Event Triggering for Social Rewards twitter */
            jQuery('._social_rewardsystem_options_twitter_follow').change(function () {
                if (jQuery(this).val() === '') {
                    jQuery('._socialrewardsystempoints_twitter_follow_field').css('display', 'none');
                    jQuery('._socialrewardsystempercent_twitter_follow_field').css('display', 'none');
                } else if (jQuery(this).val() === '1') {
                    jQuery('._socialrewardsystempercent_twitter_follow_field').css('display', 'none');
                    jQuery('._socialrewardsystempoints_twitter_follow_field').css('display', 'block');
                } else if (jQuery(this).val() === '2') {
                    jQuery('._socialrewardsystempercent_twitter_follow_field').css('display', 'block');
                    jQuery('._socialrewardsystempoints_twitter_follow_field').css('display', 'none');
                }
            });


            if (jQuery('._social_rewardsystem_options_ok_follow').val() === '') {
                jQuery('._socialrewardsystempoints_ok_follow_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_ok_follow_field').css('display', 'none');
            } else if (jQuery('._social_rewardsystem_options_ok_follow').val() === '1') {
                jQuery('._socialrewardsystempercent_ok_follow_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_ok_follow_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_ok_follow_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_ok_follow_field').css('display', 'none');
            }


            /* On Change Event Triggering for Social Rewards ok.ru */
            jQuery('._social_rewardsystem_options_ok_follow').change(function () {
                if (jQuery(this).val() === '') {
                    jQuery('._socialrewardsystempoints_ok_follow_field').css('display', 'none');
                    jQuery('._socialrewardsystempercent_ok_follow_field').css('display', 'none');
                } else if (jQuery(this).val() === '1') {
                    jQuery('._socialrewardsystempercent_ok_follow_field').css('display', 'none');
                    jQuery('._socialrewardsystempoints_ok_follow_field').css('display', 'block');
                } else if (jQuery(this).val() === '2') {
                    jQuery('._socialrewardsystempercent_ok_follow_field').css('display', 'block');
                    jQuery('._socialrewardsystempoints_ok_follow_field').css('display', 'none');
                }
            });

            /* Social Reward System for Google+ */
            if (jQuery('.social_rewardsystem_options_google').val() === '') {
                jQuery('._socialrewardsystempoints_google_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_google_field').css('display', 'none');
            } else if (jQuery('.social_rewardsystem_options_google').val() === '1') {
                jQuery('._socialrewardsystempercent_google_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_google_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_google_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_google_field').css('display', 'none');
            }

            /* On Change Event Triggering for Social Rewards Google+ */
            jQuery('.social_rewardsystem_options_google').change(function () {
                if (jQuery(this).val() === '') {
                    jQuery('._socialrewardsystempoints_google_field').css('display', 'none');
                    jQuery('._socialrewardsystempercent_google_field').css('display', 'none');
                } else if (jQuery(this).val() === '1') {
                    jQuery('._socialrewardsystempercent_google_field').css('display', 'none');
                    jQuery('._socialrewardsystempoints_google_field').css('display', 'block');
                } else {
                    jQuery('._socialrewardsystempercent_google_field').css('display', 'block');
                    jQuery('._socialrewardsystempoints_google_field').css('display', 'none');
                }
            });

            /* Social Reward System for VK */
            if (jQuery('.social_rewardsystem_options_vk').val() === '') {
                jQuery('._socialrewardsystempoints_vk_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_vk_field').css('display', 'none');
            } else if (jQuery('.social_rewardsystem_options_vk').val() === '1') {
                jQuery('._socialrewardsystempercent_vk_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_vk_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_vk_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_vk_field').css('display', 'none');
            }

            /* On Change Event Triggering for Social Rewards VK */
            jQuery('.social_rewardsystem_options_vk').change(function () {
                if (jQuery(this).val() === '') {
                    jQuery('._socialrewardsystempoints_vk_field').css('display', 'none');
                    jQuery('._socialrewardsystempercent_vk_field').css('display', 'none');
                } else if (jQuery(this).val() === '1') {
                    jQuery('._socialrewardsystempercent_vk_field').css('display', 'none');
                    jQuery('._socialrewardsystempoints_vk_field').css('display', 'block');
                } else {
                    jQuery('._socialrewardsystempercent_vk_field').css('display', 'block');
                    jQuery('._socialrewardsystempoints_vk_field').css('display', 'none');
                }
            });
            if (jQuery('._social_rewardsystem_options_instagram').val() === '') {
                jQuery('._socialrewardsystempoints_instagram_field').css('display', 'none');
                jQuery('._socialrewardsystempercent_instagram_field').css('display', 'none');
            } else if (jQuery('.social_rewardsystem_options_instagram').val() === '1') {
                jQuery('._socialrewardsystempercent_instagram_field').css('display', 'none');
                jQuery('._socialrewardsystempoints_instagram_field').css('display', 'block');
            } else {
                jQuery('._socialrewardsystempercent_instagram_field').css('display', 'block');
                jQuery('._socialrewardsystempoints_instagram_field').css('display', 'none');
            }

            /* On Change Event Triggering for Social Rewards VK */
            jQuery('._social_rewardsystem_options_instagram').change(function () {
                if (jQuery(this).val() === '') {
                    jQuery('._socialrewardsystempoints_instagram_field').css('display', 'none');
                    jQuery('._socialrewardsystempercent_instagram_field').css('display', 'none');
                } else if (jQuery(this).val() === '1') {
                    jQuery('._socialrewardsystempercent_instagram_field').css('display', 'none');
                    jQuery('._socialrewardsystempoints_instagram_field').css('display', 'block');
                } else {
                    jQuery('._socialrewardsystempercent_instagram_field').css('display', 'block');
                    jQuery('._socialrewardsystempoints_instagram_field').css('display', 'none');
                }
            });
        }
    });

    /* Social Reward Points Show/Hide - End*/

    // Confirm Dialog for Resetting the Tab

    jQuery('#resettab').click(function (e) {
        var status = confirm(rewardsystem.reset_confirm_msg);
        if (status === true) {
        } else {
            e.preventDefault();
        }
    });

    /*Show or Hide settings for General Tab - Start*/
    if (jQuery('#rs_enable_disable_point_priceing').val() == '2') {
        jQuery('#rs_sufix_prefix_point_price_label').parent().parent().hide();
        jQuery('#rs_local_price_points_for_product').parent().parent().hide();
        jQuery('#rs_label_for_point_value').parent().parent().hide();
        jQuery('#rs_local_enable_disable_point_price_for_product').parent().parent().hide();
        jQuery('#rs_global_point_price_type').parent().parent().hide();

    } else {
        jQuery('#rs_label_for_point_value').parent().parent().show();
        jQuery('#rs_sufix_prefix_point_price_label').parent().parent().show();
        jQuery('#rs_local_enable_disable_point_price_for_product').parent().parent().show();
    }

    jQuery('#rs_enable_disable_point_priceing').change(function () {
        if (jQuery('#rs_enable_disable_point_priceing').val() == '2') {
            jQuery('#rs_label_for_point_value').parent().parent().hide();
            jQuery('#rs_local_price_points_for_product').parent().parent().hide();
            jQuery('#rs_local_enable_disable_point_price_for_product').parent().parent().hide();
            jQuery('#rs_global_point_price_type').parent().parent().hide();
            jQuery('#rs_sufix_prefix_point_price_label').parent().parent().hide();

        } else {
            jQuery('#rs_label_for_point_value').parent().parent().show();
            jQuery('#rs_local_enable_disable_point_price_for_product').parent().parent().show();
            jQuery('#rs_sufix_prefix_point_price_label').parent().parent().show();
            if (jQuery('#rs_local_enable_disable_point_price_for_product').val() == '1') {
                jQuery('#rs_global_point_price_type').parent().parent().show();
            } else {
                jQuery('#rs_global_point_price_type').parent().parent().hide();
            }

        }
    });
    if (jQuery('#rs_local_enable_disable_point_price_for_product').val() == '2') {
        jQuery('#rs_global_point_price_type').parent().parent().hide();
        jQuery('#rs_local_price_points_for_product').parent().parent().hide();
    } else {
        jQuery('#rs_global_point_price_type').parent().parent().show();

    }
    jQuery('#rs_local_enable_disable_point_price_for_product').change(function () {
        if (jQuery('#rs_local_enable_disable_point_price_for_product').val() == '2') {
            jQuery('#rs_global_point_price_type').parent().parent().hide();
            jQuery('#rs_local_price_points_for_product').parent().parent().hide();
        } else {

            jQuery('#rs_global_point_price_type').parent().parent().show();
            if (jQuery('#rs_global_point_price_type').val() == '1') {
                jQuery('#rs_local_price_points_for_product').parent().parent().show();
            } else {
                jQuery('#rs_local_price_points_for_product').parent().parent().hide();
            }

        }
    });
    if (jQuery('#rs_global_point_price_type').val() == '2') {

        jQuery('#rs_local_price_points_for_product').parent().parent().hide();
    } else {

    }
    jQuery('#rs_global_point_price_type').change(function () {
        if (jQuery('#rs_global_point_price_type').val() == '2') {

            jQuery('#rs_local_price_points_for_product').parent().parent().hide();
        } else {


            jQuery('#rs_local_price_points_for_product').parent().parent().show();
        }
    });

    if (jQuery('#rs_global_enable_disable_sumo_reward').val() == '2') {
        jQuery('.show_if_enable_in_general').parent().parent().hide();
    } else {
        jQuery('.show_if_enable_in_general').parent().parent().show();

        if (jQuery('#rs_global_reward_type').val() == '1') {
            jQuery('#rs_global_reward_points').parent().parent().show();
            jQuery('#rs_global_reward_percent').parent().parent().hide();
        } else {
            jQuery('#rs_global_reward_points').parent().parent().hide();
            jQuery('#rs_global_reward_percent').parent().parent().show();
        }

        jQuery('#rs_global_reward_type').change(function () {
            if (jQuery('#rs_global_reward_type').val() == '1') {
                jQuery('#rs_global_reward_points').parent().parent().show();
                jQuery('#rs_global_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_reward_points').parent().parent().hide();
                jQuery('#rs_global_reward_percent').parent().parent().show();
            }
        });

        //To Show or hide Referral Points or Percentage for SUMO Reward.
        if (jQuery('#rs_global_referral_reward_type').val() == '1') {
            jQuery('#rs_global_referral_reward_point').parent().parent().show();
            jQuery('#rs_global_referral_reward_percent').parent().parent().hide();
        } else {
            jQuery('#rs_global_referral_reward_point').parent().parent().hide();
            jQuery('#rs_global_referral_reward_percent').parent().parent().show();
        }
        if (jQuery('#rs_global_referral_reward_type_refer').val() == '1') {

            jQuery('#rs_global_referral_reward_point_get_refer').parent().parent().show();
            jQuery('#rs_global_referral_reward_percent_get_refer').parent().parent().hide();

        } else {

            jQuery('#rs_global_referral_reward_point_get_refer').parent().parent().hide();
            jQuery('#rs_global_referral_reward_percent_get_refer').parent().parent().show();

        }
        jQuery('#rs_global_referral_reward_type').change(function () {
            if (jQuery('#rs_global_referral_reward_type').val() == '1') {
                jQuery('#rs_global_referral_reward_point').parent().parent().show();
                jQuery('#rs_global_referral_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_referral_reward_point').parent().parent().hide();
                jQuery('#rs_global_referral_reward_percent').parent().parent().show();
            }
        });
        jQuery('#rs_global_referral_reward_type').change(function () {
            if (jQuery('#rs_global_referral_reward_type').val() == '1') {
                jQuery('#rs_global_referral_reward_point').parent().parent().show();

                jQuery('#rs_global_referral_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_referral_reward_point').parent().parent().hide();

                jQuery('#rs_global_referral_reward_percent').parent().parent().show();
            }
        });
        jQuery('#rs_global_referral_reward_type_refer').change(function () {
            if (jQuery('#rs_global_referral_reward_type_refer').val() == '1') {

                jQuery('#rs_global_referral_reward_point_get_refer').parent().parent().show();
                jQuery('#rs_global_referral_reward_percent_get_refer').parent().parent().hide();

            } else {

                jQuery('#rs_global_referral_reward_point_get_refer').parent().parent().hide();
                jQuery('#rs_global_referral_reward_percent_get_refer').parent().parent().show();

            }
        });
    }


    jQuery('#rs_global_enable_disable_sumo_reward').change(function () {
        if (jQuery(this).val() == '2') {
            jQuery('.show_if_enable_in_general').parent().parent().hide();
        } else {
            jQuery('.show_if_enable_in_general').parent().parent().show();

            if (jQuery('#rs_global_reward_type').val() == '1') {
                jQuery('#rs_global_reward_points').parent().parent().show();
                jQuery('#rs_global_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_reward_points').parent().parent().hide();
                jQuery('#rs_global_reward_percent').parent().parent().show();
            }

            jQuery('#rs_global_reward_type').change(function () {
                if (jQuery('#rs_global_reward_type').val() == '1') {
                    jQuery('#rs_global_reward_points').parent().parent().show();
                    jQuery('#rs_global_reward_percent').parent().parent().hide();
                } else {
                    jQuery('#rs_global_reward_points').parent().parent().hide();
                    jQuery('#rs_global_reward_percent').parent().parent().show();
                }
            });

            //To Show or hide Referral Points or Percentage for SUMO Reward.
            if (jQuery('#rs_global_referral_reward_type').val() == '1') {
                jQuery('#rs_global_referral_reward_point').parent().parent().show();
                jQuery('#rs_global_referral_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_referral_reward_point').parent().parent().hide();
                jQuery('#rs_global_referral_reward_percent').parent().parent().show();
            }

            if (jQuery('#rs_global_referral_reward_type_refer').val() == '1') {


                jQuery('#rs_global_referral_reward_point_get_refer').parent().parent().show();
                jQuery('#rs_global_referral_reward_percent_get_refer').parent().parent().hide();

            } else {
                jQuery('#rs_global_referral_reward_percent_get_refer').parent().parent().show();

                jQuery('#rs_global_referral_reward_point_get_refer').parent().parent().hide();

            }

            jQuery('#rs_global_referral_reward_type').change(function () {
                if (jQuery('#rs_global_referral_reward_type').val() == '1') {
                    jQuery('#rs_global_referral_reward_point').parent().parent().show();
                    jQuery('#rs_global_referral_reward_percent').parent().parent().hide();
                } else {
                    jQuery('#rs_global_referral_reward_point').parent().parent().hide();
                    jQuery('#rs_global_referral_reward_percent').parent().parent().show();
                }
            });
            jQuery('#rs_global_referral_reward_type_refer').change(function () {
                if (jQuery('#rs_global_referral_reward_type_refer').val() == '1') {

                    jQuery('#rs_global_referral_reward_point_get_refer').parent().parent().show();
                    jQuery('#rs_global_referral_reward_percent_get_refer').parent().parent().hide();

                } else {


                    jQuery('#rs_global_referral_reward_point_get_refer').parent().parent().hide();
                    jQuery('#rs_global_referral_reward_percent_get_refer').parent().parent().show();
                }
            });
        }
    });

    //To Show or hide Maximum Discount Type as Fixed Value or Percentage.
    if (jQuery('#rs_max_redeem_discount').val() == '1') {
        jQuery('#rs_fixed_max_redeem_discount').parent().parent().show();
        jQuery('#rs_percent_max_redeem_discount').parent().parent().hide();
    } else {
        jQuery('#rs_fixed_max_redeem_discount').parent().parent().hide();
        jQuery('#rs_percent_max_redeem_discount').parent().parent().show();
    }

    jQuery('#rs_max_redeem_discount').change(function () {
        if (jQuery('#rs_max_redeem_discount').val() == '1') {
            jQuery('#rs_fixed_max_redeem_discount').parent().parent().show();
            jQuery('#rs_percent_max_redeem_discount').parent().parent().hide();
        } else {
            jQuery('#rs_fixed_max_redeem_discount').parent().parent().hide();
            jQuery('#rs_percent_max_redeem_discount').parent().parent().show();
        }
    });

    //To Show or hide Referral Cookie Expiry as Minutes,Hours or Days.
    if (jQuery('#rs_referral_cookies_expiry').val() == '1') {
        jQuery('#rs_referral_cookies_expiry_in_min').parent().parent().show();
        jQuery('#rs_referral_cookies_expiry_in_hours').parent().parent().hide();
        jQuery('#rs_referral_cookies_expiry_in_days').parent().parent().hide();
    } else if (jQuery('#rs_referral_cookies_expiry').val() == '2') {
        jQuery('#rs_referral_cookies_expiry_in_min').parent().parent().hide();
        jQuery('#rs_referral_cookies_expiry_in_hours').parent().parent().show();
        jQuery('#rs_referral_cookies_expiry_in_days').parent().parent().hide();
    } else {
        jQuery('#rs_referral_cookies_expiry_in_min').parent().parent().hide();
        jQuery('#rs_referral_cookies_expiry_in_hours').parent().parent().hide();
        jQuery('#rs_referral_cookies_expiry_in_days').parent().parent().show();
    }

    jQuery('#rs_referral_cookies_expiry').change(function () {
        if (jQuery('#rs_referral_cookies_expiry').val() == '1') {
            jQuery('#rs_referral_cookies_expiry_in_min').parent().parent().show();
            jQuery('#rs_referral_cookies_expiry_in_hours').parent().parent().hide();
            jQuery('#rs_referral_cookies_expiry_in_days').parent().parent().hide();
        } else if (jQuery('#rs_referral_cookies_expiry').val() == '2') {
            jQuery('#rs_referral_cookies_expiry_in_min').parent().parent().hide();
            jQuery('#rs_referral_cookies_expiry_in_hours').parent().parent().show();
            jQuery('#rs_referral_cookies_expiry_in_days').parent().parent().hide();
        } else {
            jQuery('#rs_referral_cookies_expiry_in_min').parent().parent().hide();
            jQuery('#rs_referral_cookies_expiry_in_hours').parent().parent().hide();
            jQuery('#rs_referral_cookies_expiry_in_days').parent().parent().show();
        }
    });

    //To Show or hide Referral Should be applied for Unlimited or Limited.
    if (jQuery('#_rs_select_referral_points_referee_time').val() == '2') {
        jQuery('#_rs_select_referral_points_referee_time_content').parent().parent().show();
    } else {
        jQuery('#_rs_select_referral_points_referee_time_content').parent().parent().hide();
    }

    jQuery('#_rs_select_referral_points_referee_time').change(function () {
        if (jQuery('#_rs_select_referral_points_referee_time').val() == '2') {
            jQuery('#_rs_select_referral_points_referee_time_content').parent().parent().show();
        } else {
            jQuery('#_rs_select_referral_points_referee_time_content').parent().parent().hide();
        }
    });

    //Revise Referral Points Enable/Disable
    if (jQuery('#_rs_reward_referal_point_user_deleted').val() == '1') {
        jQuery('#_rs_time_validity_to_redeem').closest('tr').show();
        if (jQuery('#_rs_time_validity_to_redeem').val() == '2') {
            jQuery('#_rs_days_for_redeeming_points').closest('tr').show();
        } else {
            jQuery('#_rs_days_for_redeeming_points').closest('tr').hide();
        }

        jQuery('#_rs_time_validity_to_redeem').change(function () {
            if (jQuery('#_rs_time_validity_to_redeem').val() == '2') {
                jQuery('#_rs_days_for_redeeming_points').closest('tr').show();
            } else {
                jQuery('#_rs_days_for_redeeming_points').closest('tr').hide();
            }
        });
    } else {
        jQuery('#_rs_time_validity_to_redeem').closest('tr').hide();
        jQuery('#_rs_days_for_redeeming_points').closest('tr').hide();
    }

    jQuery('#_rs_reward_referal_point_user_deleted').change(function () {
        if (jQuery('#_rs_reward_referal_point_user_deleted').val() == '1') {
            jQuery('#_rs_time_validity_to_redeem').closest('tr').show();
            if (jQuery('#_rs_time_validity_to_redeem').val() == '2') {
                jQuery('#_rs_days_for_redeeming_points').closest('tr').show();
            } else {
                jQuery('#_rs_days_for_redeeming_points').closest('tr').hide();
            }

            jQuery('#_rs_time_validity_to_redeem').change(function () {
                if (jQuery('#_rs_time_validity_to_redeem').val() == '2') {
                    jQuery('#_rs_days_for_redeeming_points').closest('tr').show();
                } else {
                    jQuery('#_rs_days_for_redeeming_points').closest('tr').hide();
                }
            });
        } else {
            jQuery('#_rs_time_validity_to_redeem').closest('tr').hide();
            jQuery('#_rs_days_for_redeeming_points').closest('tr').hide();
        }
    });

    jQuery('.button-primary').click(function (e) {
        if (jQuery('#_rs_select_referral_points_referee_time_content').val() != '' && jQuery('#_rs_days_for_redeeming_points').val() != '') {
            var referral_points_referee_time_content = Number(jQuery('#_rs_select_referral_points_referee_time_content').val());
            var days_for_redeeming_points = Number(jQuery('#_rs_days_for_redeeming_points').val());
            if (referral_points_referee_time_content > days_for_redeeming_points) {
                e.preventDefault();
                jQuery('#_rs_days_for_redeeming_points').focus();
                jQuery("#_rs_days_for_redeeming_points").after("<div class='validation' style='color:red;margin-bottom: 20px;'>Please enter</div>");
            }
        }
    });

    //To Show or Hide Maximum Earning Point for each User
    if (jQuery('#rs_enable_disable_max_earning_points_for_user').is(":checked") == false) {
        jQuery('#rs_max_earning_points_for_user').parent().parent().hide();
    } else {
        jQuery('#rs_max_earning_points_for_user').parent().parent().show();
    }

    jQuery('#rs_enable_disable_max_earning_points_for_user').change(function () {
        if (jQuery('#rs_enable_disable_max_earning_points_for_user').is(":checked") == false) {
            jQuery('#rs_max_earning_points_for_user').parent().parent().hide();
        } else {
            jQuery('#rs_max_earning_points_for_user').parent().parent().show();
        }
    });


    //To Show or Hide No of Purchase Field
    if (jQuery('#rs_enable_delete_referral_cookie_after_first_purchase').is(":checked") == false) {
        jQuery('#rs_no_of_purchase').parent().parent().hide();
    } else {
        jQuery('#rs_no_of_purchase').parent().parent().show();
    }

    jQuery('#rs_enable_delete_referral_cookie_after_first_purchase').change(function () {
        if (jQuery('#rs_enable_delete_referral_cookie_after_first_purchase').is(":checked") == false) {
            jQuery('#rs_no_of_purchase').parent().parent().hide();
        } else {
            jQuery('#rs_no_of_purchase').parent().parent().show();
        }
    });



    //To show or hide gift icon
    if (jQuery('#_rs_enable_disable_gift_icon').val() == '2') {
        jQuery('#rs_image_url_upload').parent().parent().hide();
    } else {
        jQuery('#rs_image_url_upload').parent().parent().show();
    }

    jQuery('#_rs_enable_disable_gift_icon').change(function () {
        if (jQuery('#_rs_enable_disable_gift_icon').val() == '2') {
            jQuery('#rs_image_url_upload').parent().parent().hide();
        } else {
            jQuery('#rs_image_url_upload').parent().parent().show();
        }
    });

    /*Show or hide Settings for General Tab - End*/

    /*Show or hide Settings for Reward Points for Action Tab - Start*/

    if (jQuery('#rs_select_referral_points_award').val() == '1') {
        jQuery('#rs_number_of_order_for_referral_points').parent().parent().hide();
        jQuery('#rs_amount_of_order_for_referral_points').parent().parent().hide();
        jQuery('#rs_referral_reward_signup_after_first_purchase').parent().parent().parent().parent().show();
    } else if (jQuery('#rs_select_referral_points_award').val() == '2') {
        jQuery('#rs_amount_of_order_for_referral_points').parent().parent().hide();
        jQuery('#rs_referral_reward_signup_after_first_purchase').parent().parent().parent().parent().hide();
        jQuery('#rs_number_of_order_for_referral_points').parent().parent().show();

    } else if (jQuery('#rs_select_referral_points_award').val() == '3') {
        jQuery('#rs_number_of_order_for_referral_points').parent().parent().hide();
        jQuery('#rs_referral_reward_signup_after_first_purchase').parent().parent().parent().parent().hide();
        jQuery('#rs_amount_of_order_for_referral_points').parent().parent().show();

    }
    if (jQuery('#rs_referral_reward_signup_getting_refer').val() == '1') {
        jQuery('#rs_referral_reward_getting_refer_after_first_purchase').parent().parent().parent().parent().show();
        jQuery('#rs_referral_reward_getting_refer').parent().parent().show();
    } else if (jQuery('#rs_referral_reward_signup_getting_refer').val() == '2') {
        jQuery('#rs_referral_reward_getting_refer').parent().parent().hide();
        jQuery('#rs_referral_reward_getting_refer_after_first_purchase').parent().parent().parent().parent().hide();
    }

    jQuery('#rs_referral_reward_signup_getting_refer').change(function () {
        if (jQuery('#rs_referral_reward_signup_getting_refer').val() == '1') {
            jQuery('#rs_referral_reward_getting_refer').parent().parent().show();
            jQuery('#rs_referral_reward_getting_refer_after_first_purchase').parent().parent().parent().parent().show();

        } else if (jQuery('#rs_referral_reward_signup_getting_refer').val() == '2') {
            jQuery('#rs_referral_reward_getting_refer').parent().parent().hide();
            jQuery('#rs_referral_reward_getting_refer_after_first_purchase').parent().parent().parent().parent().hide();


        }
    });
    jQuery('#rs_select_referral_points_award').change(function () {
        if (jQuery('#rs_select_referral_points_award').val() == '1') {
            jQuery('#rs_number_of_order_for_referral_points').parent().parent().hide();
            jQuery('#rs_amount_of_order_for_referral_points').parent().parent().hide();
            jQuery('#rs_referral_reward_signup_after_first_purchase').parent().parent().parent().parent().show();

        } else if (jQuery('#rs_select_referral_points_award').val() == '2') {
            jQuery('#rs_amount_of_order_for_referral_points').parent().parent().hide();
            jQuery('#rs_referral_reward_signup_after_first_purchase').parent().parent().parent().parent().hide();
            jQuery('#rs_number_of_order_for_referral_points').parent().parent().show();

        } else if (jQuery('#rs_select_referral_points_award').val() == '3') {
            jQuery('#rs_number_of_order_for_referral_points').parent().parent().hide();
            jQuery('#rs_referral_reward_signup_after_first_purchase').parent().parent().parent().parent().hide();
            jQuery('#rs_amount_of_order_for_referral_points').parent().parent().show();
        }
    });
    //To Show or Hide Reward Points for login
    if (jQuery('#rs_enable_reward_points_for_login').is(":checked") == false) {
        jQuery('#rs_reward_points_for_login').parent().parent().hide();
    } else {
        jQuery('#rs_reward_points_for_login').parent().parent().show();
    }

    jQuery('#rs_enable_reward_points_for_login').change(function () {
        if (jQuery('#rs_enable_reward_points_for_login').is(":checked") == false) {
            jQuery('#rs_reward_points_for_login').parent().parent().hide();
        } else {
            jQuery('#rs_reward_points_for_login').parent().parent().show();
        }
    });

    /*Show or hide Settings for Reward Points for Action Tab - End*/

    /*Show or hide Settings for Member Level - Start*/

    if (jQuery('#rs_enable_user_role_based_reward_points').is(':checked')) {
        jQuery('.rewardpoints_userrole').parent().parent().show();
    } else {
        jQuery('.rewardpoints_userrole').parent().parent().hide();
    }


    if (jQuery('#rs_enable_earned_level_based_reward_points').is(':checked')) {
        jQuery('.rsdynamicrulecreation').show();
        jQuery('#rs_select_earn_points_based_on').parent().parent().show();

    } else {
        jQuery('.rsdynamicrulecreation').hide();
        jQuery('#rs_select_earn_points_based_on').parent().parent().hide();
    }
    jQuery(document).on('click', '#rs_enable_user_role_based_reward_points', function () {
        if (jQuery(this).is(":checked")) {
            jQuery('.rewardpoints_userrole').parent().parent().show();
        } else {
            jQuery('.rewardpoints_userrole').parent().parent().hide();
        }

    });

    if (jQuery('#rs_enable_membership_plan_based_reward_points').is(':checked')) {
        jQuery('.rewardpoints_membership_plan').parent().parent().show();
    } else {
        jQuery('.rewardpoints_membership_plan').parent().parent().hide();
    }

    jQuery(document).on('click', '#rs_enable_membership_plan_based_reward_points', function () {
        if (jQuery(this).is(":checked")) {
            jQuery('.rewardpoints_membership_plan').parent().parent().show();
        } else {
            jQuery('.rewardpoints_membership_plan').parent().parent().hide();
        }

    });

    jQuery(document).on('click', '#rs_enable_earned_level_based_reward_points', function () {
        if (jQuery(this).is(":checked")) {
            jQuery('.rsdynamicrulecreation').show();
            jQuery('#rs_select_earn_points_based_on').parent().parent().show();
        } else {
            jQuery('.rsdynamicrulecreation').hide();
            jQuery('#rs_select_earn_points_based_on').parent().parent().hide();
        }
    });

    /*Show or hide Settings for Member Level - End*/

    /*Show or hide Settings for Add/Remove Tab - Start*/

    if (jQuery('#rs_select_user_type').val() == '1') {
        jQuery('#rs_select_to_include_customers').parent().parent().hide();
        jQuery('#rs_select_to_exclude_customers').parent().parent().hide();
    } else if (jQuery('#rs_select_user_type').val() == '2') {
        jQuery('#rs_select_to_include_customers').parent().parent().show();
        jQuery('#rs_select_to_exclude_customers').parent().parent().hide();
    } else {
        jQuery('#rs_select_to_include_customers').parent().parent().hide();
        jQuery('#rs_select_to_exclude_customers').parent().parent().show();
    }
    jQuery('#rs_select_user_type').change(function () {
        if (jQuery('#rs_select_user_type').val() == '1') {
            jQuery('#rs_select_to_include_customers').parent().parent().hide();
            jQuery('#rs_select_to_exclude_customers').parent().parent().hide();
            jQuery('#rs_reward_addremove_points').val("");
            jQuery('#rs_reward_addremove_reason').val("");
        } else if (jQuery('#rs_select_user_type').val() == '2') {
            jQuery('#rs_select_to_include_customers').parent().parent().show();
            jQuery('#rs_select_to_exclude_customers').parent().parent().hide();
            jQuery('#rs_reward_addremove_points').val("");
            jQuery('#rs_reward_addremove_reason').val("");
        } else {
            jQuery('#rs_select_to_include_customers').parent().parent().hide();
            jQuery('#rs_select_to_exclude_customers').parent().parent().show();
            jQuery('#rs_reward_addremove_points').val("");
            jQuery('#rs_reward_addremove_reason').val("");
        }
    });

    /*Show or hide Settings for Add/Remove Tab - End*/

    /*Show or hide Settings for Message Tab - Start*/

    if (jQuery('#rs_show_hide_message_for_single_product').val() == '1') {
        jQuery('#rs_message_for_single_product_point_rule').parent().parent().show();
    } else {
        jQuery('#rs_message_for_single_product_point_rule').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_single_product').change(function () {
        if (jQuery('#rs_show_hide_message_for_single_product').val() == '1') {
            jQuery('#rs_message_for_single_product_point_rule').parent().parent().show();
        } else {
            jQuery('#rs_message_for_single_product_point_rule').parent().parent().hide();
        }
    });

    //Show or Hide Earn Point Message in Shop Page
    if (jQuery('#rs_show_hide_message_for_simple_in_shop').val() == '1') {
        jQuery('#rs_message_in_shop_page_for_simple').parent().parent().show();
        jQuery('#rs_message_position_for_simple_products_in_shop_page').parent().parent().show();
    } else {
        jQuery('#rs_message_in_shop_page_for_simple').parent().parent().hide();
        jQuery('#rs_message_position_for_simple_products_in_shop_page').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_simple_in_shop').change(function () {
        if (jQuery('#rs_show_hide_message_for_simple_in_shop').val() == '1') {
            jQuery('#rs_message_in_shop_page_for_simple').parent().parent().show();
            jQuery('#rs_message_position_for_simple_products_in_shop_page').parent().parent().show();
        } else {
            jQuery('#rs_message_in_shop_page_for_simple').parent().parent().hide();
            jQuery('#rs_message_position_for_simple_products_in_shop_page').parent().parent().hide();
        }
    });

    //Show or Hide Earn Point Message in Single Product Page
    if (jQuery('#rs_show_hide_message_for_shop_archive_single').val() == '1') {
        jQuery('#rs_message_in_single_product_page').parent().parent().show();
    } else {
        jQuery('#rs_message_in_single_product_page').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_shop_archive_single').change(function () {
        if (jQuery('#rs_show_hide_message_for_shop_archive_single').val() == '1') {
            jQuery('#rs_message_in_single_product_page').parent().parent().show();
        } else {
            jQuery('#rs_message_in_single_product_page').parent().parent().hide();
        }
    });

    //Show or Hide Earn Point Message in Single Product Page for Variable Products
    if (jQuery('#rs_show_hide_message_for_variable_in_single_product_page').val() == '1') {
        jQuery('#rs_message_for_single_product_variation').parent().parent().show();
        jQuery('#rs_message_position_in_single_product_page_for_simple_products').parent().parent().show();
    } else {
        jQuery('#rs_message_for_single_product_variation').parent().parent().hide();
        jQuery('#rs_message_position_in_single_product_page_for_simple_products').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_variable_in_single_product_page').change(function () {
        if (jQuery('#rs_show_hide_message_for_variable_in_single_product_page').val() == '1') {
            jQuery('#rs_message_for_single_product_variation').parent().parent().show();
            jQuery('#rs_message_position_in_single_product_page_for_simple_products').parent().parent().show();
        } else {
            jQuery('#rs_message_for_single_product_variation').parent().parent().hide();
            jQuery('#rs_message_position_in_single_product_page_for_simple_products').parent().parent().hide();
        }
    });

    //Show or Hide Message for each Variant (Variable Product) in Single Product Page
    if (jQuery('#rs_show_hide_message_for_variable_product').val() == '1') {
        jQuery('#rs_message_for_variation_products').parent().parent().show();
    } else {
        jQuery('#rs_message_for_variation_products').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_variable_product').change(function () {
        if (jQuery('#rs_show_hide_message_for_variable_product').val() == '1') {
            jQuery('#rs_message_for_variation_products').parent().parent().show();
        } else {
            jQuery('#rs_message_for_variation_products').parent().parent().hide();
        }
    });


    //Show or Hide Message in Cart Page for each Products
    if (jQuery('#rs_show_hide_message_for_each_products').val() == '1') {
        jQuery('#rs_message_product_in_cart').parent().parent().show();
    } else {
        jQuery('#rs_message_product_in_cart').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_each_products').change(function () {
        if (jQuery('#rs_show_hide_message_for_each_products').val() == '1') {
            jQuery('#rs_message_product_in_cart').parent().parent().show();
        } else {
            jQuery('#rs_message_product_in_cart').parent().parent().hide();
        }
    });

    //Show or Hide Message in Cart Page for Completing the Total Purchase
    if (jQuery('#rs_show_hide_message_for_total_points').val() == '1') {
        jQuery('#rs_message_total_price_in_cart').parent().parent().show();
    } else {
        jQuery('#rs_message_total_price_in_cart').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_total_points').change(function () {
        if (jQuery('#rs_show_hide_message_for_total_points').val() == '1') {
            jQuery('#rs_message_total_price_in_cart').parent().parent().show();
        } else {
            jQuery('#rs_message_total_price_in_cart').parent().parent().hide();
        }
    });

    //Show or Hide Message in Cart Page that display Your Reward Points
    if (jQuery('#rs_show_hide_message_for_my_rewards').val() == '1') {
        jQuery('#rs_message_user_points_in_cart').parent().parent().show();
    } else {
        jQuery('#rs_message_user_points_in_cart').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_my_rewards').change(function () {
        if (jQuery('#rs_show_hide_message_for_my_rewards').val() == '1') {
            jQuery('#rs_message_user_points_in_cart').parent().parent().show();
        } else {
            jQuery('#rs_message_user_points_in_cart').parent().parent().hide();
        }
    });

    //Show or Hide Message in Cart Page that Display Redeeming Your Points
    if (jQuery('#rs_show_hide_message_for_redeem_points').val() == '1') {
        jQuery('#rs_message_user_points_redeemed_in_cart').parent().parent().show();
    } else {
        jQuery('#rs_message_user_points_redeemed_in_cart').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_redeem_points').change(function () {
        if (jQuery('#rs_show_hide_message_for_redeem_points').val() == '1') {
            jQuery('#rs_message_user_points_redeemed_in_cart').parent().parent().show();
        } else {
            jQuery('#rs_message_user_points_redeemed_in_cart').parent().parent().hide();
        }
    });


    //Show or Hide Message for Guest in Checkout Page
    if (jQuery('#rs_enable_acc_creation_for_guest_checkout_page').is(':checked')) {
        jQuery('#rs_message_for_guest_in_checkout').parent().parent().hide();
        jQuery('#rs_show_hide_message_for_guest_checkout_page').parent().parent().hide();
        jQuery('#rs_show_hide_message_for_guest').parent().parent().hide();
        jQuery('#rs_message_for_guest_in_cart').parent().parent().hide();
    } else {
        jQuery('#rs_show_hide_message_for_guest_checkout_page').parent().parent().show();
        if (jQuery('#rs_show_hide_message_for_guest_checkout_page').val() == '1') {
            jQuery('#rs_message_for_guest_in_checkout').parent().parent().show();
        } else {
            jQuery('#rs_message_for_guest_in_checkout').parent().parent().hide();
        }

        jQuery('#rs_show_hide_message_for_guest_checkout_page').change(function () {
            if (jQuery('#rs_show_hide_message_for_guest_checkout_page').val() == '1') {
                jQuery('#rs_message_for_guest_in_checkout').parent().parent().show();
            } else {
                jQuery('#rs_message_for_guest_in_checkout').parent().parent().hide();
            }
        });


        //Show or Hide Message for Guest in Cart Page
        jQuery('#rs_show_hide_message_for_guest').parent().parent().show();
        if (jQuery('#rs_show_hide_message_for_guest').val() == '1') {
            jQuery('#rs_message_for_guest_in_cart').parent().parent().show();
        } else {
            jQuery('#rs_message_for_guest_in_cart').parent().parent().hide();
        }

        jQuery('#rs_show_hide_message_for_guest').change(function () {
            if (jQuery('#rs_show_hide_message_for_guest').val() == '1') {
                jQuery('#rs_message_for_guest_in_cart').parent().parent().show();
            } else {
                jQuery('#rs_message_for_guest_in_cart').parent().parent().hide();
            }
        });
    }

    jQuery('#rs_enable_acc_creation_for_guest_checkout_page').change(function () {
        if (jQuery('#rs_enable_acc_creation_for_guest_checkout_page').is(':checked')) {
            jQuery('#rs_message_for_guest_in_checkout').parent().parent().hide();
            jQuery('#rs_show_hide_message_for_guest_checkout_page').parent().parent().hide();
            jQuery('#rs_show_hide_message_for_guest').parent().parent().hide();
            jQuery('#rs_message_for_guest_in_cart').parent().parent().hide();
        } else {
            jQuery('#rs_show_hide_message_for_guest_checkout_page').parent().parent().show();
            if (jQuery('#rs_show_hide_message_for_guest_checkout_page').val() == '1') {
                jQuery('#rs_message_for_guest_in_checkout').parent().parent().show();
            } else {
                jQuery('#rs_message_for_guest_in_checkout').parent().parent().hide();
            }

            jQuery('#rs_show_hide_message_for_guest_checkout_page').change(function () {
                if (jQuery('#rs_show_hide_message_for_guest_checkout_page').val() == '1') {
                    jQuery('#rs_message_for_guest_in_checkout').parent().parent().show();
                } else {
                    jQuery('#rs_message_for_guest_in_checkout').parent().parent().hide();
                }
            });

            //Show or Hide Message for Guest in Cart Page
            jQuery('#rs_show_hide_message_for_guest').parent().parent().show();
            if (jQuery('#rs_show_hide_message_for_guest').val() == '1') {
                jQuery('#rs_message_for_guest_in_cart').parent().parent().show();
            } else {
                jQuery('#rs_message_for_guest_in_cart').parent().parent().hide();
            }

            jQuery('#rs_show_hide_message_for_guest').change(function () {
                if (jQuery('#rs_show_hide_message_for_guest').val() == '1') {
                    jQuery('#rs_message_for_guest_in_cart').parent().parent().show();
                } else {
                    jQuery('#rs_message_for_guest_in_cart').parent().parent().hide();
                }
            });
        }
    });


    //Show or Hide Message in Checkout Page for each Products
    if (jQuery('#rs_show_hide_message_for_each_products_checkout_page').val() == '1') {
        jQuery('#rs_message_product_in_checkout').parent().parent().show();
    } else {
        jQuery('#rs_message_product_in_checkout').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_each_products_checkout_page').change(function () {
        if (jQuery('#rs_show_hide_message_for_each_products_checkout_page').val() == '1') {
            jQuery('#rs_message_product_in_checkout').parent().parent().show();
        } else {
            jQuery('#rs_message_product_in_checkout').parent().parent().hide();
        }
    });

    //Show or Hide Message in Checkout Page for Completing the Total Purchase
    if (jQuery('#rs_show_hide_message_for_total_points_checkout_page').val() == '1') {
        jQuery('#rs_message_total_price_in_checkout').parent().parent().show();
    } else {
        jQuery('#rs_message_total_price_in_checkout').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_total_points_checkout_page').change(function () {
        if (jQuery('#rs_show_hide_message_for_total_points_checkout_page').val() == '1') {
            jQuery('#rs_message_total_price_in_checkout').parent().parent().show();
        } else {
            jQuery('#rs_message_total_price_in_checkout').parent().parent().hide();
        }
    });

    //Show or Hide Message in Checkout Page that display Your Reward Points
    if (jQuery('#rs_show_hide_message_for_my_rewards_checkout_page').val() == '1') {
        jQuery('#rs_message_user_points_in_checkout').parent().parent().show();
    } else {
        jQuery('#rs_message_user_points_in_checkout').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_my_rewards_checkout_page').change(function () {
        if (jQuery('#rs_show_hide_message_for_my_rewards_checkout_page').val() == '1') {
            jQuery('#rs_message_user_points_in_checkout').parent().parent().show();
        } else {
            jQuery('#rs_message_user_points_in_checkout').parent().parent().hide();
        }
    });

    //Show or Hide Message in Checkout Page that Display Redeeming Your Points
    if (jQuery('#rs_show_hide_message_for_redeem_points_checkout_page').val() == '1') {
        jQuery('#rs_message_user_points_redeemed_in_checkout').parent().parent().show();
    } else {
        jQuery('#rs_message_user_points_redeemed_in_checkout').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_for_redeem_points_checkout_page').change(function () {
        if (jQuery('#rs_show_hide_message_for_redeem_points_checkout_page').val() == '1') {
            jQuery('#rs_message_user_points_redeemed_in_checkout').parent().parent().show();
        } else {
            jQuery('#rs_message_user_points_redeemed_in_checkout').parent().parent().hide();
        }
    });

    //Show or Hide Message for Payment Gateway Reward Points
    if (jQuery('#rs_show_hide_message_payment_gateway_reward_points').val() == '1') {
        jQuery('#rs_message_payment_gateway_reward_points').parent().parent().show();
    } else {
        jQuery('#rs_message_payment_gateway_reward_points').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_payment_gateway_reward_points').change(function () {
        if (jQuery('#rs_show_hide_message_payment_gateway_reward_points').val() == '1') {
            jQuery('#rs_message_payment_gateway_reward_points').parent().parent().show();
        } else {
            jQuery('#rs_message_payment_gateway_reward_points').parent().parent().hide();
        }
    });

    if (jQuery('#rs_disable_point_if_reward_points_gateway').is(':checked')) {
        jQuery('#rs_restriction_msg_for_reward_gatweway').closest('tr').show();
    } else {
        jQuery('#rs_restriction_msg_for_reward_gatweway').closest('tr').hide();
    }

    jQuery('#rs_disable_point_if_reward_points_gateway').change(function () {
        if (jQuery('#rs_disable_point_if_reward_points_gateway').is(':checked')) {
            jQuery('#rs_restriction_msg_for_reward_gatweway').closest('tr').show();
        } else {
            jQuery('#rs_restriction_msg_for_reward_gatweway').closest('tr').hide();
        }
    });

    if (jQuery('#rs_show_hide_message_errmsg_for_point_price_coupon').val() == '1') {
        jQuery('#rs_errmsg_for_redeem_in_point_price_prt').parent().parent().show();
    } else {
        jQuery('#rs_errmsg_for_redeem_in_point_price_prt').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_errmsg_for_point_price_coupon').change(function () {
        if (jQuery('#rs_show_hide_message_errmsg_for_point_price_coupon').val() == '1') {
            jQuery('#rs_errmsg_for_redeem_in_point_price_prt').parent().parent().show();
        } else {
            jQuery('#rs_errmsg_for_redeem_in_point_price_prt').parent().parent().hide();
        }
    });

    if (jQuery('#rs_show_hide_message_notice_for_redeeming').val() == '1') {
        jQuery('#rs_msg_for_redeem_when_tax_enabled').parent().parent().show();
    } else {
        jQuery('#rs_msg_for_redeem_when_tax_enabled').parent().parent().hide();
    }

    jQuery('#rs_show_hide_message_notice_for_redeeming').change(function () {
        if (jQuery('#rs_show_hide_message_notice_for_redeeming').val() == '1') {
            jQuery('#rs_msg_for_redeem_when_tax_enabled').parent().parent().show();
        } else {
            jQuery('#rs_msg_for_redeem_when_tax_enabled').parent().parent().hide();
        }
    });

    /*Show or hide Settings for Message Tab - End*/

    /*Show or hide Settings for Cart Tab - Start*/

    var enable_auto_redeem_checkbox = jQuery('#rs_enable_disable_auto_redeem_points').is(':checked') ? 'yes' : 'no';
    if (enable_auto_redeem_checkbox === 'yes') {
        jQuery('#rs_percentage_cart_total_auto_redeem').parent().parent().show();
    } else {
        jQuery('#rs_percentage_cart_total_auto_redeem').parent().parent().hide();
    }

    jQuery('#rs_enable_disable_auto_redeem_points').click(function () {
        var enable_auto_redeem_checkbox = jQuery('#rs_enable_disable_auto_redeem_points').is(':checked') ? 'yes' : 'no';
        if (enable_auto_redeem_checkbox == 'yes') {
            jQuery('#rs_percentage_cart_total_auto_redeem').parent().parent().show();
        } else {
            jQuery('#rs_percentage_cart_total_auto_redeem').parent().parent().hide();
        }
    });
    var currentvalue = jQuery('#rs_show_hide_redeem_field').val();
    if (currentvalue === '1') {
        jQuery('#rs_enable_redeem_for_selected_products').parent().parent().parent().parent().show();
        jQuery('#rs_exclude_products_for_redeeming').parent().parent().parent().parent().show();
        jQuery('#rs_enable_redeem_for_selected_category').parent().parent().parent().parent().show();
        jQuery('#rs_exclude_category_for_redeeming').parent().parent().parent().parent().show();
        jQuery('#rs_show_redeeming_field').parent().parent().show();
        var enable_selected_product_checkbox = jQuery('#rs_enable_redeem_for_selected_products').is(':checked') ? 'yes' : 'no';
        var enable_exclude_product_checkbox = jQuery('#rs_exclude_products_for_redeeming').is(':checked') ? 'yes' : 'no';
        var enable_selected_category_checkbox = jQuery('#rs_enable_redeem_for_selected_category').is(':checked') ? 'yes' : 'no';
        var enable_exclude_category_checkbox = jQuery('#rs_exclude_category_for_redeeming').is(':checked') ? 'yes' : 'no';
        if (enable_selected_product_checkbox === 'yes') {
            jQuery('#rs_select_products_to_enable_redeeming').parent().parent().show();
        } else {
            jQuery('#rs_select_products_to_enable_redeeming').parent().parent().hide();
        }
        if (enable_exclude_product_checkbox === 'yes') {
            jQuery('#rs_exclude_products_to_enable_redeeming').parent().parent().show();
        } else {
            jQuery('#rs_exclude_products_to_enable_redeeming').parent().parent().hide();
        }
        if (enable_selected_category_checkbox === 'yes') {
            jQuery('#rs_select_category_to_enable_redeeming').parent().parent().show();
        } else {
            jQuery('#rs_select_category_to_enable_redeeming').parent().parent().hide();
        }
        if (enable_exclude_category_checkbox === 'yes') {
            jQuery('#rs_exclude_category_to_enable_redeeming').parent().parent().show();
        } else {
            jQuery('#rs_exclude_category_to_enable_redeeming').parent().parent().hide();
        }


        //When enabling the product and category
        jQuery('#rs_enable_redeem_for_selected_products').click(function () {
            var enable_redeem_for_selected_product = jQuery('#rs_enable_redeem_for_selected_products').is(':checked') ? 'yes' : 'no';
            if (enable_redeem_for_selected_product == 'yes') {
                jQuery('#rs_select_products_to_enable_redeeming').parent().parent().show();
            } else {
                jQuery('#rs_select_products_to_enable_redeeming').parent().parent().hide();
            }
        });
        jQuery('#rs_exclude_products_for_redeeming').click(function () {
            var enable_exclude_product_checkbox = jQuery('#rs_exclude_products_for_redeeming').is(':checked') ? 'yes' : 'no';
            if (enable_exclude_product_checkbox == 'yes') {
                jQuery('#rs_exclude_products_to_enable_redeeming').parent().parent().show();
            } else {
                jQuery('#rs_exclude_products_to_enable_redeeming').parent().parent().hide();
            }
        });
        jQuery('#rs_enable_redeem_for_selected_category').click(function () {
            var enable_selected_category_checkbox = jQuery('#rs_enable_redeem_for_selected_category').is(':checked') ? 'yes' : 'no';
            if (enable_selected_category_checkbox == 'yes') {
                jQuery('#rs_select_category_to_enable_redeeming').parent().parent().show();
            } else {
                jQuery('#rs_select_category_to_enable_redeeming').parent().parent().hide();
            }
        });
        jQuery('#rs_exclude_category_for_redeeming').click(function () {
            var enable_exclude_category_checkbox = jQuery('#rs_exclude_category_for_redeeming').is(':checked') ? 'yes' : 'no';
            if (enable_exclude_category_checkbox == 'yes') {
                jQuery('#rs_exclude_category_to_enable_redeeming').parent().parent().show();
            } else {
                jQuery('#rs_exclude_category_to_enable_redeeming').parent().parent().hide();
            }
        });
    } else {
        jQuery('#rs_enable_redeem_for_selected_products').parent().parent().parent().parent().hide();
        jQuery('#rs_exclude_products_for_redeeming').parent().parent().parent().parent().hide();
        jQuery('#rs_enable_redeem_for_selected_category').parent().parent().parent().parent().hide();
        jQuery('#rs_exclude_category_for_redeeming').parent().parent().parent().parent().hide();
        jQuery('#rs_select_products_to_enable_redeeming').parent().parent().hide();
        jQuery('#rs_exclude_products_to_enable_redeeming').parent().parent().hide();
        jQuery('#rs_select_category_to_enable_redeeming').parent().parent().hide();
        jQuery('#rs_exclude_category_to_enable_redeeming').parent().parent().hide();
        jQuery('#rs_show_redeeming_field').parent().parent().hide();
        if (currentvalue === '2') {
            jQuery('#rs_show_redeeming_field').parent().parent().show();
        } else if (currentvalue === '5') {
            jQuery('#rs_show_redeeming_field').parent().parent().show();
        }
    }

    jQuery('#rs_show_hide_redeem_field').change(function () {
        var currentvalue = jQuery(this).val();
        if (currentvalue === '1') {
            jQuery('#rs_enable_redeem_for_selected_products').parent().parent().parent().parent().show();
            jQuery('#rs_exclude_products_for_redeeming').parent().parent().parent().parent().show();
            jQuery('#rs_enable_redeem_for_selected_category').parent().parent().parent().parent().show();
            jQuery('#rs_exclude_category_for_redeeming').parent().parent().parent().parent().show();
            jQuery('#rs_show_redeeming_field').parent().parent().show();
            var enable_selected_product_checkbox = jQuery('#rs_enable_redeem_for_selected_products').is(':checked') ? 'yes' : 'no';
            var enable_exclude_product_checkbox = jQuery('#rs_exclude_products_for_redeeming').is(':checked') ? 'yes' : 'no';
            var enable_selected_category_checkbox = jQuery('#rs_enable_redeem_for_selected_category').is(':checked') ? 'yes' : 'no';
            var enable_exclude_category_checkbox = jQuery('#rs_exclude_category_for_redeeming').is(':checked') ? 'yes' : 'no';
            if (enable_selected_product_checkbox === 'yes') {
                jQuery('#rs_select_products_to_enable_redeeming').parent().parent().show();
            } else {
                jQuery('#rs_select_products_to_enable_redeeming').parent().parent().hide();
            }
            if (enable_exclude_product_checkbox === 'yes') {
                jQuery('#rs_exclude_products_to_enable_redeeming').parent().parent().show();
            } else {
                jQuery('#rs_exclude_products_to_enable_redeeming').parent().parent().hide();
            }
            if (enable_selected_category_checkbox === 'yes') {
                jQuery('#rs_select_category_to_enable_redeeming').parent().parent().show();
            } else {
                jQuery('#rs_select_category_to_enable_redeeming').parent().parent().hide();
            }
            if (enable_exclude_category_checkbox === 'yes') {
                jQuery('#rs_exclude_category_to_enable_redeeming').parent().parent().show();
            } else {
                jQuery('#rs_exclude_category_to_enable_redeeming').parent().parent().hide();
            }


            //When enabling the product and category
            jQuery('#rs_enable_redeem_for_selected_products').click(function () {
                var enable_redeem_for_selected_product = jQuery('#rs_enable_redeem_for_selected_products').is(':checked') ? 'yes' : 'no';
                if (enable_redeem_for_selected_product == 'yes') {
                    jQuery('#rs_select_products_to_enable_redeeming').parent().parent().show();
                } else {
                    jQuery('#rs_select_products_to_enable_redeeming').parent().parent().hide();
                }
            });
            jQuery('#rs_exclude_products_for_redeeming').click(function () {
                var enable_exclude_product_checkbox = jQuery('#rs_exclude_products_for_redeeming').is(':checked') ? 'yes' : 'no';
                if (enable_exclude_product_checkbox == 'yes') {
                    jQuery('#rs_exclude_products_to_enable_redeeming').parent().parent().show();
                } else {
                    jQuery('#rs_exclude_products_to_enable_redeeming').parent().parent().hide();
                }
            });
            jQuery('#rs_enable_redeem_for_selected_category').click(function () {
                var enable_selected_category_checkbox = jQuery('#rs_enable_redeem_for_selected_category').is(':checked') ? 'yes' : 'no';
                if (enable_selected_category_checkbox == 'yes') {
                    jQuery('#rs_select_category_to_enable_redeeming').parent().parent().show();
                } else {
                    jQuery('#rs_select_category_to_enable_redeeming').parent().parent().hide();
                }
            });
            jQuery('#rs_exclude_category_for_redeeming').click(function () {
                var enable_exclude_category_checkbox = jQuery('#rs_exclude_category_for_redeeming').is(':checked') ? 'yes' : 'no';
                if (enable_exclude_category_checkbox == 'yes') {
                    jQuery('#rs_exclude_category_to_enable_redeeming').parent().parent().show();
                } else {
                    jQuery('#rs_exclude_category_to_enable_redeeming').parent().parent().hide();
                }
            });
        } else {
            jQuery('#rs_enable_redeem_for_selected_products').parent().parent().parent().parent().hide();
            jQuery('#rs_exclude_products_for_redeeming').parent().parent().parent().parent().hide();
            jQuery('#rs_enable_redeem_for_selected_category').parent().parent().parent().parent().hide();
            jQuery('#rs_exclude_category_for_redeeming').parent().parent().parent().parent().hide();
            jQuery('#rs_select_products_to_enable_redeeming').parent().parent().hide();
            jQuery('#rs_exclude_products_to_enable_redeeming').parent().parent().hide();
            jQuery('#rs_select_category_to_enable_redeeming').parent().parent().hide();
            jQuery('#rs_exclude_category_to_enable_redeeming').parent().parent().hide();
            jQuery('#rs_show_redeeming_field').parent().parent().hide();
            if (currentvalue === '2') {
                jQuery('#rs_show_redeeming_field').parent().parent().show();
            } else if (currentvalue === '5') {
                jQuery('#rs_show_redeeming_field').parent().parent().show();
            }
        }
    });
    //Show or Hide Redeeming Field Caption
    if (jQuery('#rs_show_hide_redeem_caption').val() == '1') {
        jQuery('#rs_redeem_field_caption').parent().parent().show();
    } else {
        jQuery('#rs_redeem_field_caption').parent().parent().hide();
    }

    jQuery('#rs_show_hide_redeem_caption').change(function () {
        if (jQuery('#rs_show_hide_redeem_caption').val() == '1') {
            jQuery('#rs_redeem_field_caption').parent().parent().show();
        } else {
            jQuery('#rs_redeem_field_caption').parent().parent().hide();
        }
    });
    //Show or Hide Redeeming Field Placeholder
    if (jQuery('#rs_show_hide_redeem_placeholder').val() == '1') {
        jQuery('#rs_redeem_field_placeholder').parent().parent().show();
    } else {
        jQuery('#rs_redeem_field_placeholder').parent().parent().hide();
    }

    jQuery('#rs_show_hide_redeem_placeholder').change(function () {
        if (jQuery('#rs_show_hide_redeem_placeholder').val() == '1') {
            jQuery('#rs_redeem_field_placeholder').parent().parent().show();
        } else {
            jQuery('#rs_redeem_field_placeholder').parent().parent().hide();
        }
    });
    //Show or Hide Current User Points is Empty Error Message
    if (jQuery('#rs_show_hide_points_empty_error_message').val() == '1') {
        jQuery('#rs_current_points_empty_error_message').parent().parent().show();
    } else {
        jQuery('#rs_current_points_empty_error_message').parent().parent().hide();
    }

    jQuery('#rs_show_hide_points_empty_error_message').change(function () {
        if (jQuery('#rs_show_hide_points_empty_error_message').val() == '1') {
            jQuery('#rs_current_points_empty_error_message').parent().parent().show();
        } else {
            jQuery('#rs_current_points_empty_error_message').parent().parent().hide();
        }
    });
    //Show or Hide Minimum Points for first time Redeeming Error Message
    if (jQuery('#rs_show_hide_first_redeem_error_message').val() == '1') {
        jQuery('#rs_min_points_first_redeem_error_message').parent().parent().show();
    } else {
        jQuery('#rs_min_points_first_redeem_error_message').parent().parent().hide();
    }

    jQuery('#rs_show_hide_first_redeem_error_message').change(function () {
        if (jQuery('#rs_show_hide_first_redeem_error_message').val() == '1') {
            jQuery('#rs_min_points_first_redeem_error_message').parent().parent().show();
        } else {
            jQuery('#rs_min_points_first_redeem_error_message').parent().parent().hide();
        }
    });
    //Show or Hide Minimum Points After first time Redeeming Error Message
    if (jQuery('#rs_show_hide_after_first_redeem_error_message').val() == '1') {
        jQuery('#rs_min_points_after_first_error').parent().parent().show();
    } else {
        jQuery('#rs_min_points_after_first_error').parent().parent().hide();
    }

    jQuery('#rs_show_hide_after_first_redeem_error_message').change(function () {
        if (jQuery('#rs_show_hide_after_first_redeem_error_message').val() == '1') {
            jQuery('#rs_min_points_after_first_error').parent().parent().show();
        } else {
            jQuery('#rs_min_points_after_first_error').parent().parent().hide();
        }
    });
    //Show or Hide Minimum Cart Total for Redeeming Error Message
    if (jQuery('#rs_show_hide_minimum_cart_total_error_message').val() == '1') {
        jQuery('#rs_min_cart_total_redeem_error').parent().parent().show();
    } else {
        jQuery('#rs_min_cart_total_redeem_error').parent().parent().hide();
    }

    jQuery('#rs_show_hide_minimum_cart_total_error_message').change(function () {
        if (jQuery('#rs_show_hide_minimum_cart_total_error_message').val() == '1') {
            jQuery('#rs_min_cart_total_redeem_error').parent().parent().show();
        } else {
            jQuery('#rs_min_cart_total_redeem_error').parent().parent().hide();
        }
    });
    //Show or Hide For Redeem Button Type
    if (jQuery('#rs_redeem_field_type_option').val() == '1') {
        jQuery('#rs_percentage_cart_total_redeem').parent().parent().hide();
    } else {
        jQuery('#rs_percentage_cart_total_redeem').parent().parent().show();
    }

    jQuery('#rs_redeem_field_type_option').change(function () {
        if (jQuery('#rs_redeem_field_type_option').val() == '1') {
            jQuery('#rs_percentage_cart_total_redeem').parent().parent().hide();
        } else {
            jQuery('#rs_percentage_cart_total_redeem').parent().parent().show();
        }
    });

    /*Show or hide Settings for Cart Tab - End*/

    /*Show or hide Settings for Checkout Tab - Start*/

    var enable_selected_product_for_purchase = jQuery('#rs_enable_selected_product_for_purchase_using_points').is(':checked') ? 'yes' : 'no';
    if (enable_selected_product_for_purchase == 'yes') {
        jQuery('#rs_select_product_for_purchase_using_points').parent().parent().show();
    } else {
        jQuery('#rs_select_product_for_purchase_using_points').parent().parent().hide();
    }

    jQuery('#rs_enable_selected_product_for_purchase_using_points').change(function () {
        jQuery('#rs_select_product_for_purchase_using_points').parent().parent().toggle();
    });

    var enable_selected_product_for_purchase1 = jQuery('#rs_enable_selected_product_for_hide_gateway').is(':checked') ? 'yes' : 'no';
    if (enable_selected_product_for_purchase1 == 'yes') {
        jQuery('#rs_select_product_for_hide_gateway').parent().parent().show();
    } else {
        jQuery('#rs_select_product_for_hide_gateway').parent().parent().hide();
    }

    jQuery('#rs_enable_selected_product_for_hide_gateway').change(function () {
        jQuery('#rs_select_product_for_hide_gateway').parent().parent().toggle();
    });



    var enable_selected_product_for_purchase11 = jQuery('#rs_enable_selected_category_to_hide_gateway').is(':checked') ? 'yes' : 'no';
    if (enable_selected_product_for_purchase11 == 'yes') {
        jQuery('#rs_select_category_to_hide_gateway').parent().parent().show();
    } else {
        jQuery('#rs_select_category_to_hide_gateway').parent().parent().hide();
    }

    jQuery('#rs_enable_selected_category_to_hide_gateway').change(function () {
        jQuery('#rs_select_category_to_hide_gateway').parent().parent().toggle();
    });

    var enable_selected_category_for_purchase1 = jQuery('#rs_enable_selected_category_for_purchase_using_points').is(':checked') ? 'yes' : 'no';
    if (enable_selected_category_for_purchase1 == 'yes') {
        jQuery('#rs_select_category_for_purchase_using_points').parent().parent().show();
    } else {
        jQuery('#rs_select_category_for_purchase_using_points').parent().parent().hide();
    }

    jQuery('#rs_enable_selected_category_for_purchase_using_points').change(function () {
        jQuery('#rs_select_category_for_purchase_using_points').parent().parent().toggle();
    });
    var gateway = jQuery('#rs_show_hide_reward_points_gatewy').val();
    if (gateway == '1') {
        jQuery('#rs_enable_selected_category_to_hide_gateway').parent().parent().parent().parent().hide();
        jQuery('#rs_enable_selected_product_for_hide_gateway').parent().parent().parent().parent().hide();
        jQuery('#rs_select_product_for_hide_gateway').parent().parent().hide();
        jQuery('#rs_select_category_to_hide_gateway').parent().parent().hide();
        jQuery('#rs_enable_gateway_visible_to_all_product').parent().parent().parent().parent().show();
    }
    if (gateway == '2') {
        jQuery('#rs_enable_selected_product_for_purchase_using_points').parent().parent().parent().parent().hide();
        jQuery('#rs_enable_selected_category_for_purchase_using_points').parent().parent().parent().parent().hide();
        jQuery('#rs_select_product_for_purchase_using_points').parent().parent().hide();
        jQuery('#rs_select_category_for_purchase_using_points').parent().parent().hide();
        jQuery('#rs_enable_gateway_visible_to_all_product').parent().parent().parent().parent().hide();
    }
    jQuery('#rs_show_hide_reward_points_gatewy').change(function () {
        if (jQuery('#rs_show_hide_reward_points_gatewy').val() == '1') {
            jQuery('#rs_enable_selected_category_to_hide_gateway').parent().parent().parent().parent().hide();
            jQuery('#rs_enable_selected_product_for_hide_gateway').parent().parent().parent().parent().hide();
            jQuery('#rs_select_product_for_hide_gateway').parent().parent().hide();
            jQuery('#rs_select_category_to_hide_gateway').parent().parent().hide();
            jQuery('#rs_enable_selected_product_for_purchase_using_points').parent().parent().parent().parent().show();
            jQuery('#rs_enable_selected_category_for_purchase_using_points').parent().parent().parent().parent().show();
            jQuery('#rs_enable_gateway_visible_to_all_product').parent().parent().parent().parent().show();
            if (enable_selected_category_for_purchase1 == 'yes') {
                jQuery('#rs_select_category_for_purchase_using_points').parent().parent().show();
            } else {
                jQuery('#rs_select_category_for_purchase_using_points').parent().parent().hide();
            }
            if (enable_selected_product_for_purchase == 'yes') {
                jQuery('#rs_select_product_for_purchase_using_points').parent().parent().show();
            } else {
                jQuery('#rs_select_product_for_purchase_using_points').parent().parent().hide();
            }
        } else {
            jQuery('#rs_enable_gateway_visible_to_all_product').parent().parent().parent().parent().hide();
            jQuery('#rs_enable_selected_product_for_purchase_using_points').parent().parent().parent().parent().hide();
            jQuery('#rs_enable_selected_category_for_purchase_using_points').parent().parent().parent().parent().hide();
            jQuery('#rs_select_product_for_purchase_using_points').parent().parent().hide();
            jQuery('#rs_select_category_for_purchase_using_points').parent().parent().hide();
            jQuery('#rs_enable_selected_category_to_hide_gateway').parent().parent().parent().parent().show();
            jQuery('#rs_enable_selected_product_for_hide_gateway').parent().parent().parent().parent().show();
            if (enable_selected_product_for_purchase11 == 'yes') {
                jQuery('#rs_select_category_to_hide_gateway').parent().parent().show();
            } else {
                jQuery('#rs_select_category_to_hide_gateway').parent().parent().hide();
            }
            if (enable_selected_product_for_purchase1 == 'yes') {
                jQuery('#rs_select_product_for_hide_gateway').parent().parent().show();
            } else {
                jQuery('#rs_select_product_for_hide_gateway').parent().parent().hide();
            }

        }
    });


    //Show or Hide For Redeem Button Type
    if (jQuery('#rs_redeem_field_type_option_checkout').val() == '1') {
        jQuery('#rs_percentage_cart_total_redeem_checkout').parent().parent().hide();
    } else {
        jQuery('#rs_percentage_cart_total_redeem_checkout').parent().parent().show();
    }

    jQuery('#rs_redeem_field_type_option_checkout').change(function () {
        if (jQuery('#rs_redeem_field_type_option_checkout').val() == '1') {
            jQuery('#rs_percentage_cart_total_redeem_checkout').parent().parent().hide();
        } else {
            jQuery('#rs_percentage_cart_total_redeem_checkout').parent().parent().show();
        }
    });

    /*Show or hide Settings for Checkout Tab - End*/

    /*Show or hide Settings for My Account Tab - Start*/

    if (jQuery('#rs_show_hide_generate_referral').val() == '1') {
        jQuery('#rs_show_hide_generate_referral_link_type').parent().parent().show();
        jQuery('#rs_generate_referral_link_based_on_user').parent().parent().show();
        jQuery('#rs_generate_link_label').closest('tr').show();
        jQuery('#rs_generate_link_sno_label').closest('tr').show();
        jQuery('#rs_generate_link_date_label').closest('tr').show();
        jQuery('#rs_generate_link_referrallink_label').closest('tr').show();
        jQuery('#rs_generate_link_social_label').closest('tr').show();
        jQuery('#rs_generate_link_action_label').closest('tr').show();
        jQuery('#rs_generate_link_button_label').closest('tr').show();

        if (jQuery('#rs_show_hide_generate_referral_link_type').val() == '1') {
            jQuery('#rs_prefill_generate_link').parent().parent().show();
            jQuery('#rs_static_generate_link').parent().parent().hide();
            jQuery('#rs_my_referral_link_button_label').parent().parent().hide();
        } else {
            jQuery('#rs_prefill_generate_link').parent().parent().hide();
            jQuery('#rs_static_generate_link').parent().parent().show();
            jQuery('#rs_my_referral_link_button_label').parent().parent().show();
        }

        jQuery('#rs_show_hide_generate_referral_link_type').change(function () {
            if (jQuery('#rs_show_hide_generate_referral_link_type').val() == '1') {
                jQuery('#rs_prefill_generate_link').parent().parent().show();
                jQuery('#rs_static_generate_link').parent().parent().hide();
                jQuery('#rs_my_referral_link_button_label').parent().parent().hide();
            } else {
                jQuery('#rs_prefill_generate_link').parent().parent().hide();
                jQuery('#rs_static_generate_link').parent().parent().show();
                jQuery('#rs_my_referral_link_button_label').parent().parent().show();
            }
        });

        jQuery('#rs_select_type_of_user_for_referral').closest('tr').show();
        if (jQuery('#rs_select_type_of_user_for_referral').val() === '1') {
            jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
            jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
            jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
            jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();
        } else if (jQuery('#rs_select_type_of_user_for_referral').val() === '2') {
            jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
            jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
            jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
            jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().show();
        } else if (jQuery('#rs_select_type_of_user_for_referral').val() === '3') {
            jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();
            jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().show();
            jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
            jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
        } else if (jQuery('#rs_select_type_of_user_for_referral').val() === '4') {
            jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
            jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().show();
            jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
            jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();
        } else {
            jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
            jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
            jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().show();
            jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();

        }

        jQuery('#rs_select_type_of_user_for_referral').change(function () {
            if (jQuery('#rs_select_type_of_user_for_referral').val() === '1') {
                jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();
            } else if (jQuery('#rs_select_type_of_user_for_referral').val() === '2') {
                jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().show();
            } else if (jQuery('#rs_select_type_of_user_for_referral').val() === '3') {
                jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().show();
                jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
            } else if (jQuery('#rs_select_type_of_user_for_referral').val() === '4') {
                jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().show();
                jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();
            } else {
                jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().show();
                jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();

            }
        });

        jQuery('#rs_display_msg_when_access_is_prevented').closest('tr').show();
        if (jQuery('#rs_display_msg_when_access_is_prevented').val() == '1') {
            jQuery('#rs_msg_for_restricted_user').closest('tr').show();
        } else {
            jQuery('#rs_msg_for_restricted_user').closest('tr').hide();
        }

        jQuery('#rs_display_msg_when_access_is_prevented').change(function () {
            if (jQuery('#rs_display_msg_when_access_is_prevented').val() == '1') {
                jQuery('#rs_msg_for_restricted_user').closest('tr').show();
            } else {
                jQuery('#rs_msg_for_restricted_user').closest('tr').hide();
            }
        });
    } else {
        jQuery('#rs_show_hide_generate_referral_link_type').parent().parent().hide();
        jQuery('#rs_prefill_generate_link').parent().parent().hide();
        jQuery('#rs_static_generate_link').parent().parent().hide();
        jQuery('#rs_my_referral_link_button_label').parent().parent().hide();
        jQuery('#rs_generate_referral_link_based_on_user').parent().parent().hide();
        jQuery('#rs_generate_link_label').closest('tr').hide();
        jQuery('#rs_generate_link_sno_label').closest('tr').hide();
        jQuery('#rs_generate_link_date_label').closest('tr').hide();
        jQuery('#rs_generate_link_referrallink_label').closest('tr').hide();
        jQuery('#rs_generate_link_social_label').closest('tr').hide();
        jQuery('#rs_generate_link_action_label').closest('tr').hide();
        jQuery('#rs_generate_link_button_label').closest('tr').hide();
        jQuery('#rs_select_type_of_user_for_referral').closest('tr').hide();
        jQuery('#rs_select_exclude_users_list_for_show_referral_link').closest('tr').hide();
        jQuery('#rs_select_include_users_for_show_referral_link').closest('tr').hide();
        jQuery('#rs_select_users_role_for_show_referral_link').closest('tr').hide();
        jQuery('#rs_select_exclude_users_role_for_show_referral_link').closest('tr').hide();
        jQuery('#rs_display_msg_when_access_is_prevented').closest('tr').hide();
        jQuery('#rs_msg_for_restricted_user').closest('tr').hide();
    }

    jQuery('#rs_show_hide_generate_referral').change(function () {
        if (jQuery('#rs_show_hide_generate_referral').val() == '1') {
            jQuery('#rs_show_hide_generate_referral_link_type').parent().parent().show();
            jQuery('#rs_generate_referral_link_based_on_user').parent().parent().show();
            jQuery('#rs_generate_link_label').closest('tr').show();
            jQuery('#rs_generate_link_sno_label').closest('tr').show();
            jQuery('#rs_generate_link_date_label').closest('tr').show();
            jQuery('#rs_generate_link_referrallink_label').closest('tr').show();
            jQuery('#rs_generate_link_social_label').closest('tr').show();
            jQuery('#rs_generate_link_action_label').closest('tr').show();
            jQuery('#rs_generate_link_button_label').closest('tr').show();
            if (jQuery('#rs_show_hide_generate_referral_link_type').val() == '1') {
                jQuery('#rs_prefill_generate_link').parent().parent().show();
                jQuery('#rs_static_generate_link').parent().parent().hide();
                jQuery('#rs_my_referral_link_button_label').parent().parent().hide();
            } else {
                jQuery('#rs_prefill_generate_link').parent().parent().hide();
                jQuery('#rs_static_generate_link').parent().parent().show();
                jQuery('#rs_my_referral_link_button_label').parent().parent().show();
            }

            jQuery('#rs_show_hide_generate_referral_link_type').change(function () {
                if (jQuery('#rs_show_hide_generate_referral_link_type').val() == '1') {
                    jQuery('#rs_prefill_generate_link').parent().parent().show();
                    jQuery('#rs_static_generate_link').parent().parent().hide();
                    jQuery('#rs_my_referral_link_button_label').parent().parent().hide();
                } else {
                    jQuery('#rs_prefill_generate_link').parent().parent().hide();
                    jQuery('#rs_static_generate_link').parent().parent().show();
                    jQuery('#rs_my_referral_link_button_label').parent().parent().show();
                }
            });

            jQuery('#rs_select_type_of_user_for_referral').closest('tr').show();

            if (jQuery('#rs_select_type_of_user_for_referral').val() === '1') {
                jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();
            } else if (jQuery('#rs_select_type_of_user_for_referral').val() === '2') {
                jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().show();
            } else if (jQuery('#rs_select_type_of_user_for_referral').val() === '3') {
                jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().show();
                jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
            } else if (jQuery('#rs_select_type_of_user_for_referral').val() === '4') {
                jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().show();
                jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();
            } else {
                jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
                jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().show();
                jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();

            }

            jQuery('#rs_select_type_of_user_for_referral').change(function () {
                if (jQuery('#rs_select_type_of_user_for_referral').val() === '1') {
                    jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
                    jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
                    jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
                    jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();
                } else if (jQuery('#rs_select_type_of_user_for_referral').val() === '2') {
                    jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
                    jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
                    jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
                    jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().show();
                } else if (jQuery('#rs_select_type_of_user_for_referral').val() === '3') {
                    jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();
                    jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().show();
                    jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
                    jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
                } else if (jQuery('#rs_select_type_of_user_for_referral').val() === '4') {
                    jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
                    jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().show();
                    jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().hide();
                    jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();
                } else {
                    jQuery('#rs_select_exclude_users_list_for_show_referral_link').parent().parent().hide();
                    jQuery('#rs_select_users_role_for_show_referral_link').parent().parent().hide();
                    jQuery('#rs_select_exclude_users_role_for_show_referral_link').parent().parent().show();
                    jQuery('#rs_select_include_users_for_show_referral_link').parent().parent().hide();

                }
            });
            jQuery('#rs_display_msg_when_access_is_prevented').closest('tr').show();
            if (jQuery('#rs_display_msg_when_access_is_prevented').val() == '1') {
                jQuery('#rs_msg_for_restricted_user').closest('tr').show();
            } else {
                jQuery('#rs_msg_for_restricted_user').closest('tr').hide();
            }

            jQuery('#rs_display_msg_when_access_is_prevented').change(function () {
                if (jQuery('#rs_display_msg_when_access_is_prevented').val() == '1') {
                    jQuery('#rs_msg_for_restricted_user').closest('tr').show();
                } else {
                    jQuery('#rs_msg_for_restricted_user').closest('tr').hide();
                }
            });
        } else {
            jQuery('#rs_show_hide_generate_referral_link_type').parent().parent().hide();
            jQuery('#rs_prefill_generate_link').parent().parent().hide();
            jQuery('#rs_static_generate_link').parent().parent().hide();
            jQuery('#rs_my_referral_link_button_label').parent().parent().hide();
            jQuery('#rs_generate_referral_link_based_on_user').parent().parent().hide();
            jQuery('#rs_generate_link_label').closest('tr').hide();
            jQuery('#rs_generate_link_sno_label').closest('tr').hide();
            jQuery('#rs_generate_link_date_label').closest('tr').hide();
            jQuery('#rs_generate_link_referrallink_label').closest('tr').hide();
            jQuery('#rs_generate_link_social_label').closest('tr').hide();
            jQuery('#rs_generate_link_action_label').closest('tr').hide();
            jQuery('#rs_generate_link_button_label').closest('tr').hide();
            jQuery('#rs_select_type_of_user_for_referral').closest('tr').hide();
            jQuery('#rs_select_exclude_users_list_for_show_referral_link').closest('tr').hide();
            jQuery('#rs_select_include_users_for_show_referral_link').closest('tr').hide();
            jQuery('#rs_select_users_role_for_show_referral_link').closest('tr').hide();
            jQuery('#rs_select_exclude_users_role_for_show_referral_link').closest('tr').hide();
            jQuery('#rs_display_msg_when_access_is_prevented').closest('tr').hide();
            jQuery('#rs_msg_for_restricted_user').closest('tr').hide();
        }
    });

    if (jQuery('#rs_show_hide_redeem_voucher').val() == '1') {
        jQuery('#rs_redeem_your_gift_voucher_label').closest('tr').show();
        jQuery('#rs_redeem_gift_voucher_button_label').closest('tr').show();
        jQuery('#rs_redeem_voucher_position').closest('tr').show();
    } else {
        jQuery('#rs_redeem_your_gift_voucher_label').closest('tr').hide();
        jQuery('#rs_redeem_gift_voucher_button_label').closest('tr').hide();
        jQuery('#rs_redeem_voucher_position').closest('tr').hide();
    }

    jQuery('#rs_show_hide_redeem_voucher').change(function () {
        if (jQuery('#rs_show_hide_redeem_voucher').val() == '1') {
            jQuery('#rs_redeem_your_gift_voucher_label').closest('tr').show();
            jQuery('#rs_redeem_gift_voucher_button_label').closest('tr').show();
            jQuery('#rs_redeem_voucher_position').closest('tr').show();
        } else {
            jQuery('#rs_redeem_your_gift_voucher_label').closest('tr').hide();
            jQuery('#rs_redeem_gift_voucher_button_label').closest('tr').hide();
            jQuery('#rs_redeem_voucher_position').closest('tr').hide();
        }
    });
    
    if (jQuery('#rs_account_show_hide_facebook_like_button').val() == '1') {
        jQuery('#rs_facebook_title').closest('tr').show();
        jQuery('#rs_facebook_description').closest('tr').show();
        jQuery('#rs_fbshare_image_url_upload').closest('tr').show();
    } else {
        jQuery('#rs_facebook_title').closest('tr').hide();
        jQuery('#rs_facebook_description').closest('tr').hide();
        jQuery('#rs_fbshare_image_url_upload').closest('tr').hide();
    }

    jQuery('#rs_account_show_hide_facebook_like_button').change(function () {
        if (jQuery('#rs_account_show_hide_facebook_like_button').val() == '1') {
            jQuery('#rs_facebook_title').closest('tr').show();
            jQuery('#rs_facebook_description').closest('tr').show();
            jQuery('#rs_fbshare_image_url_upload').closest('tr').show();
        } else {
            jQuery('#rs_facebook_title').closest('tr').hide();
            jQuery('#rs_facebook_description').closest('tr').hide();
            jQuery('#rs_fbshare_image_url_upload').closest('tr').hide();
        }
    });

    /*Show or hide Settings for My Account Tab - End*/

    /*To hide Loader Image in Bulk Update Tab - Start*/

    jQuery('.gif_rs_sumo_reward_button').css('display', 'none');
    jQuery('.gif_rs_sumo_reward_button_social').css('display', 'none');
    jQuery('.gif_rs_sumo_point_price_button').css('display', 'none');

    /*To hide Loader Image in Bulk Update Tab - End*/

    /*Show or hide Settings for Bulk Update Tab - Start*/
    if ((jQuery('.rs_which_point_precing_product_selection').val() === '1')) {
        jQuery('#rs_select_particular_products_for_point_price').parent().parent().hide();
        jQuery('#rs_select_particular_categories_for_point_price').parent().parent().hide();
    } else if (jQuery('.rs_which_point_precing_product_selection').val() === '2') {
        jQuery('#rs_select_particular_products_for_point_price').parent().parent().show();
        jQuery('#rs_select_particular_categories_for_point_price').parent().parent().hide();
    } else if (jQuery('.rs_which_point_precing_product_selection').val() === '3') {
        jQuery('#rs_select_particular_products_for_point_price').parent().parent().hide();
        jQuery('#rs_select_particular_categories_for_point_price').parent().parent().hide();
    } else {
        jQuery('#rs_select_particular_categories_for_point_price').parent().parent().show();
        jQuery('#rs_select_particular_products_for_point_price').parent().parent().hide();
    }


    jQuery('.rs_which_point_precing_product_selection').change(function () {
        if ((jQuery(this).val() === '1')) {
            jQuery('#rs_select_particular_categories_for_point_price').parent().parent().hide();
            jQuery('#rs_select_particular_products_for_point_price').parent().parent().hide();
        } else if (jQuery(this).val() === '2') {
            jQuery('#rs_select_particular_products_for_point_price').parent().parent().show();
            jQuery('#rs_select_particular_categories_for_point_price').parent().parent().hide();
        } else if (jQuery(this).val() === '3') {
            jQuery('#rs_select_particular_categories_for_point_price').parent().parent().hide();
            jQuery('#rs_select_particular_products_for_point_price').parent().parent().hide();
        } else {
            jQuery('#rs_select_particular_categories_for_point_price').parent().parent().show();
            jQuery('#rs_select_particular_products_for_point_price').parent().parent().hide();
        }
    });




    if ((jQuery('.rs_which_product_selection').val() === '1')) {
        jQuery('#rs_select_particular_products').parent().parent().hide();
        jQuery('#rs_select_particular_categories').parent().parent().hide();
    } else if (jQuery('.rs_which_product_selection').val() === '2') {
        jQuery('#rs_select_particular_products').parent().parent().show();
        jQuery('#rs_select_particular_categories').parent().parent().hide();
    } else if (jQuery('.rs_which_product_selection').val() === '3') {
        jQuery('#rs_select_particular_products').parent().parent().hide();
        jQuery('#rs_select_particular_categories').parent().parent().hide();
    } else {
        jQuery('#rs_select_particular_categories').parent().parent().show();
        jQuery('#rs_select_particular_products').parent().parent().hide();
    }

    if ((jQuery('.rs_which_social_product_selection').val() === '1')) {
        jQuery('#rs_select_particular_social_products').parent().parent().hide();
        jQuery('#rs_select_particular_social_categories').parent().parent().hide();
    } else if (jQuery('.rs_which_social_product_selection').val() === '2') {
        jQuery('#rs_select_particular_social_products').parent().parent().show();
        jQuery('#rs_select_particular_social_categories').parent().parent().hide();
    } else if (jQuery('.rs_which_social_product_selection').val() === '3') {
        jQuery('#rs_select_particular_social_products').parent().parent().hide();
        jQuery('#rs_select_particular_social_categories').parent().parent().hide();
    } else {
        jQuery('#rs_select_particular_social_categories').parent().parent().show();
        jQuery('#rs_select_particular_social_products').parent().parent().hide();
    }

    jQuery('.rs_which_product_selection').change(function () {
        if ((jQuery(this).val() === '1') || (jQuery(this).val() === '3')) {
            jQuery('#rs_select_particular_products').parent().parent().hide();
            jQuery('#rs_select_particular_categories').parent().parent().hide();
        } else if (jQuery(this).val() === '2') {
            jQuery('#rs_select_particular_products').parent().parent().show();
            jQuery('#rs_select_particular_categories').parent().parent().hide();
        } else {
            jQuery('#rs_select_particular_categories').parent().parent().show();
            jQuery('#rs_select_particular_products').parent().parent().hide();
        }
    });

    jQuery('.rs_which_social_product_selection').change(function () {
        if ((jQuery(this).val() === '1') || (jQuery(this).val() === '3')) {
            jQuery('#rs_select_particular_social_products').parent().parent().hide();
            jQuery('#rs_select_particular_social_categories').parent().parent().hide();
        } else if (jQuery(this).val() === '2') {
            jQuery('#rs_select_particular_social_products').parent().parent().show();
            jQuery('#rs_select_particular_social_categories').parent().parent().hide();
        } else {
            jQuery('#rs_select_particular_social_categories').parent().parent().show();
            jQuery('#rs_select_particular_social_products').parent().parent().hide();
        }
    });

    var selectrangevalue = jQuery('#rs_sumo_select_order_range').val();
    if (selectrangevalue === '1') {
        jQuery('#rs_from_date').parent().parent().hide();
    } else {
        jQuery('#rs_from_date').parent().parent().show();
    }
    jQuery('#rs_sumo_select_order_range').change(function () {
        if (jQuery(this).val() === '1') {
            jQuery('#rs_from_date').parent().parent().hide();
        } else {
            jQuery('#rs_from_date').parent().parent().show();
        }
    });

    /*
     * Show or Hide For Reward Points In Update
     */

    if (jQuery('#rs_local_enable_disable_point_price').val() == '2') {
        jQuery('.show_if_price_enable_in_update').parent().parent().hide();
        jQuery('#rs_local_point_price_type').parent().parent().hide();
        jQuery('#rs_local_point_pricing_type').parent().parent().hide();

    } else {
        jQuery('#rs_local_point_price_type').parent().parent().show();
        jQuery('#rs_local_point_pricing_type').parent().parent().show();
        if (jQuery('#rs_local_point_pricing_type').val() == '2') {
            jQuery('#rs_local_point_price_type').parent().parent().hide();

        } else {
            jQuery('#rs_local_point_price_type').parent().parent().show();
            if (jQuery('#rs_local_point_price_type').val() == '1') {
                jQuery('.show_if_price_enable_in_update').parent().parent().show();
            }
            if (jQuery('#rs_local_point_price_type').val() == '1') {
                jQuery('.show_if_price_enable_in_update').parent().parent().show();
            }

        }

    }


    jQuery('#rs_local_enable_disable_point_price').change(function () {
        if (jQuery(this).val() == '2') {
            jQuery('.show_if_price_enable_in_update').parent().parent().hide();
            jQuery('#rs_local_point_price_type').parent().parent().hide();
            jQuery('#rs_local_point_pricing_type').parent().parent().hide();
        } else {


            jQuery('#rs_local_point_pricing_type').parent().parent().show();
            if (jQuery('#rs_local_point_pricing_type').val() == '2') {
                jQuery('#rs_local_point_price_type').parent().parent().hide();
                jQuery('#rs_local_price_points').parent().parent().show();


            } else {
                jQuery('#rs_local_point_price_type').parent().parent().show();
                if (jQuery('#rs_local_point_price_type').val() == '1') {
                    jQuery('.show_if_price_enable_in_update').parent().parent().show();
                }
                if (jQuery('#rs_local_point_price_type').val() == '1') {
                    jQuery('.show_if_price_enable_in_update').parent().parent().show();
                }

            }
        }
    });

    jQuery('#rs_local_point_pricing_type').change(function () {
        if (jQuery(this).val() == '2') {
            jQuery('#rs_local_point_price_type').parent().parent().hide();

        } else {
            jQuery('#rs_local_point_price_type').parent().parent().show();
            if (jQuery('#rs_local_point_price_type').val() == '1') {
                jQuery('.show_if_price_enable_in_update').parent().parent().show();


            }
        }
    });
    if (jQuery('#rs_local_point_price_type').val() == '2') {
        jQuery('.show_if_price_enable_in_update').parent().parent().hide();


    }

    jQuery('#rs_local_point_price_type').change(function () {
        if (jQuery(this).val() == '2') {
            jQuery('.show_if_price_enable_in_update').parent().parent().hide();

        } else {

            jQuery('.show_if_price_enable_in_update').parent().parent().show();
        }
    });

    if (jQuery('#rs_local_enable_disable_reward').val() == '2') {
        jQuery('.show_if_enable_in_update').parent().parent().hide();
    } else {
        jQuery('.show_if_enable_in_update').parent().parent().show();

        if (jQuery('#rs_local_reward_type').val() === '1') {
            jQuery('#rs_local_reward_points').parent().parent().show();
            jQuery('#rs_local_reward_percent').parent().parent().hide();
        } else {
            jQuery('#rs_local_reward_points').parent().parent().hide();
            jQuery('#rs_local_reward_percent').parent().parent().show();
        }


        if (jQuery('#rs_local_referral_reward_type').val() === '1') {
            jQuery('#rs_local_referral_reward_point').parent().parent().show();
            jQuery('#rs_local_referral_reward_percent').parent().parent().hide();
        } else {
            jQuery('#rs_local_referral_reward_point').parent().parent().hide();
            jQuery('#rs_local_referral_reward_percent').parent().parent().show();
        }
        if (jQuery('#rs_local_referral_reward_type_get_refer').val() === '1') {

            jQuery('#rs_local_referral_reward_point_for_getting_referred').parent().parent().show();

            jQuery('#rs_local_referral_reward_percent_for_getting_referred').parent().parent().hide();
        } else {

            jQuery('#rs_local_referral_reward_point_for_getting_referred').parent().parent().hide();

            jQuery('#rs_local_referral_reward_percent_for_getting_referred').parent().parent().show();
        }

        jQuery('#rs_local_reward_type').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_local_reward_points').parent().parent().show();
                jQuery('#rs_local_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points').parent().parent().hide();
                jQuery('#rs_local_reward_percent').parent().parent().show();
            }
        });
        jQuery('#rs_local_referral_reward_type').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_local_referral_reward_point').parent().parent().show();
                jQuery('#rs_local_referral_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_local_referral_reward_point').parent().parent().hide();
                jQuery('#rs_local_referral_reward_percent').parent().parent().show();
            }
        });

        jQuery('#rs_local_referral_reward_type_get_refer').change(function () {
            if ((jQuery(this).val()) === '1') {

                jQuery('#rs_local_referral_reward_point_for_getting_referred').parent().parent().show();

                jQuery('#rs_local_referral_reward_percent_for_getting_referred').parent().parent().hide();
            } else {

                jQuery('#rs_local_referral_reward_point_for_getting_referred').parent().parent().hide();

                jQuery('#rs_local_referral_reward_percent_for_getting_referred').parent().parent().show();
            }
        });

    }

    jQuery('#rs_local_enable_disable_reward').change(function () {
        if (jQuery(this).val() == '2') {
            jQuery('.show_if_enable_in_update').parent().parent().hide();
        } else {
            jQuery('.show_if_enable_in_update').parent().parent().show();

            if (jQuery('#rs_local_reward_type').val() === '1') {
                jQuery('#rs_local_reward_points').parent().parent().show();
                jQuery('#rs_local_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points').parent().parent().hide();
                jQuery('#rs_local_reward_percent').parent().parent().show();
            }


            if (jQuery('#rs_local_referral_reward_type').val() === '1') {
                jQuery('#rs_local_referral_reward_point').parent().parent().show();
                jQuery('#rs_local_referral_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_local_referral_reward_point').parent().parent().hide();
                jQuery('#rs_local_referral_reward_percent').parent().parent().show();
            }
            if (jQuery('#rs_local_referral_reward_type_get_refer').val() === '1') {

                jQuery('#rs_local_referral_reward_point_for_getting_referred').parent().parent().show();

                jQuery('#rs_local_referral_reward_percent_for_getting_referred').parent().parent().hide();
            } else {

                jQuery('#rs_local_referral_reward_point_for_getting_referred').parent().parent().hide();

                jQuery('#rs_local_referral_reward_percent_for_getting_referred').parent().parent().show();
            }
            jQuery('#rs_local_reward_type').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_local_reward_points').parent().parent().show();
                    jQuery('#rs_local_reward_percent').parent().parent().hide();
                } else {
                    jQuery('#rs_local_reward_points').parent().parent().hide();
                    jQuery('#rs_local_reward_percent').parent().parent().show();
                }
            });
            jQuery('#rs_local_referral_reward_type').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_local_referral_reward_point').parent().parent().show();
                    jQuery('#rs_local_referral_reward_percent').parent().parent().hide();
                } else {
                    jQuery('#rs_local_referral_reward_point').parent().parent().hide();
                    jQuery('#rs_local_referral_reward_percent').parent().parent().show();
                }
            });
            jQuery('#rs_local_referral_reward_type_get_refer').change(function () {
                if ((jQuery(this).val()) === '1') {

                    jQuery('#rs_local_referral_reward_point_for_getting_referred').parent().parent().show();

                    jQuery('#rs_local_referral_reward_percent_for_getting_referred').parent().parent().hide();
                } else {

                    jQuery('#rs_local_referral_reward_point_for_getting_referred').parent().parent().hide();

                    jQuery('#rs_local_referral_reward_percent_for_getting_referred').parent().parent().show();
                }
            });
        }
    });

    /*
     * End of Show or Hide For Reward Points In Update
     */


    /*
     * Show or Hide For Social Reward Points In Update
     */
    if (jQuery('#rs_local_enable_disable_social_reward').val() == '2') {
        jQuery('.show_if_social_enable_in_update').parent().parent().hide();
    } else {
        jQuery('.show_if_social_enable_in_update').parent().parent().show();

        if (jQuery('#rs_local_reward_type_for_facebook').val() === '1') {
            jQuery('#rs_local_reward_points_facebook').parent().parent().show();
            jQuery('#rs_local_reward_percent_facebook').parent().parent().hide();
        } else {
            jQuery('#rs_local_reward_points_facebook').parent().parent().hide();
            jQuery('#rs_local_reward_percent_facebook').parent().parent().show();
        }

        jQuery('#rs_local_reward_type_for_facebook').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_local_reward_points_facebook').parent().parent().show();
                jQuery('#rs_local_reward_percent_facebook').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_facebook').parent().parent().hide();
                jQuery('#rs_local_reward_percent_facebook').parent().parent().show();
            }
        });

        if (jQuery('#rs_local_reward_type_for_facebook_share').val() === '1') {
            jQuery('#rs_local_reward_points_facebook_share').parent().parent().show();
            jQuery('#rs_local_reward_percent_facebook_share').parent().parent().hide();
        } else {
            jQuery('#rs_local_reward_points_facebook_share').parent().parent().hide();
            jQuery('#rs_local_reward_percent_facebook_share').parent().parent().show();
        }

        jQuery('#rs_local_reward_type_for_facebook_share').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_local_reward_points_facebook_share').parent().parent().show();
                jQuery('#rs_local_reward_percent_facebook_share').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_facebook_share').parent().parent().hide();
                jQuery('#rs_local_reward_percent_facebook_share').parent().parent().show();
            }
        });
        if (jQuery('#rs_local_reward_type_for_twitter').val() === '1') {
            jQuery('#rs_local_reward_points_twitter').parent().parent().show();
            jQuery('#rs_local_reward_percent_twitter').parent().parent().hide();
        } else {
            jQuery('#rs_local_reward_points_twitter').parent().parent().hide();
            jQuery('#rs_local_reward_percent_twitter').parent().parent().show();
        }

        jQuery('#rs_local_reward_type_for_twitter').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_local_reward_points_twitter').parent().parent().show();
                jQuery('#rs_local_reward_percent_twitter').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_twitter').parent().parent().hide();
                jQuery('#rs_local_reward_percent_twitter').parent().parent().show();
            }
        });
        if (jQuery('#rs_local_reward_type_for_twitter_follow').val() === '1') {
            jQuery('#rs_local_reward_points_twitter_follow').parent().parent().show();
            jQuery('#rs_local_reward_percent_twitter_follow').parent().parent().hide();
        } else {
            jQuery('#rs_local_reward_points_twitter_follow').parent().parent().hide();
            jQuery('#rs_local_reward_percent_twitter_follow').parent().parent().show();
        }

        jQuery('#rs_local_reward_type_for_twitter_follow').change(function () {

            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_local_reward_points_twitter_follow').parent().parent().show();
                jQuery('#rs_local_reward_percent_twitter_follow').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_twitter_follow').parent().parent().hide();
                jQuery('#rs_local_reward_percent_twitter_follow').parent().parent().show();
            }
        });
        if (jQuery('#rs_local_reward_type_for_vk').val() === '1') {
            jQuery('#rs_local_reward_points_vk').parent().parent().show();
            jQuery('#rs_local_reward_percent_vk').parent().parent().hide();
        } else {
            jQuery('#rs_local_reward_points_vk').parent().parent().hide();
            jQuery('#rs_local_reward_percent_vk').parent().parent().show();
        }

        jQuery('#rs_local_reward_type_for_vk').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_local_reward_points_vk').parent().parent().show();
                jQuery('#rs_local_reward_percent_vk').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_vk').parent().parent().hide();
                jQuery('#rs_local_reward_percent_vk').parent().parent().show();
            }
        });

        if (jQuery('#rs_local_reward_type_for_ok_follow').val() === '1') {
            jQuery('#rs_local_reward_points_ok_follow').parent().parent().show();
            jQuery('#rs_local_reward_percent_ok_follow').parent().parent().hide();
        } else {
            jQuery('#rs_local_reward_points_ok_follow').parent().parent().hide();
            jQuery('#rs_local_reward_percent_ok_follow').parent().parent().show();
        }

        jQuery('#rs_local_reward_type_for_ok_follow').change(function () {

            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_local_reward_points_ok_follow').parent().parent().show();
                jQuery('#rs_local_reward_percent_ok_follow').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_ok_follow').parent().parent().hide();
                jQuery('#rs_local_reward_percent_ok_follow').parent().parent().show();
            }
        });
        if (jQuery('#rs_local_reward_type_for_instagram').val() === '1') {
            jQuery('#rs_local_reward_points_instagram').parent().parent().show();
            jQuery('#rs_local_reward_percent_instagram').parent().parent().hide();
        } else {
            jQuery('#rs_local_reward_points_instagram').parent().parent().hide();
            jQuery('#rs_local_reward_percent_instagram').parent().parent().show();
        }

        jQuery('#rs_local_reward_type_for_instagram').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_local_reward_points_instagram').parent().parent().show();
                jQuery('#rs_local_reward_percent_instagram').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_instagram').parent().parent().hide();
                jQuery('#rs_local_reward_percent_instagram').parent().parent().show();
            }
        });
        if (jQuery('#rs_local_reward_type_for_google').val() === '1') {
            jQuery('#rs_local_reward_points_google').parent().parent().show();
            jQuery('#rs_local_reward_percent_google').parent().parent().hide();
        } else {
            jQuery('#rs_local_reward_points_google').parent().parent().hide();
            jQuery('#rs_local_reward_percent_google').parent().parent().show();
        }

        jQuery('#rs_local_reward_type_for_google').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_local_reward_points_google').parent().parent().show();
                jQuery('#rs_local_reward_percent_google').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_google').parent().parent().hide();
                jQuery('#rs_local_reward_percent_google').parent().parent().show();
            }
        });
    }

    jQuery('#rs_local_enable_disable_social_reward').change(function () {
        if (jQuery(this).val() == '2') {
            jQuery('.show_if_social_enable_in_update').parent().parent().hide();
        } else {
            jQuery('.show_if_social_enable_in_update').parent().parent().show();

            if (jQuery('#rs_local_reward_type_for_facebook').val() === '1') {
                jQuery('#rs_local_reward_points_facebook').parent().parent().show();
                jQuery('#rs_local_reward_percent_facebook').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_facebook').parent().parent().hide();
                jQuery('#rs_local_reward_percent_facebook').parent().parent().show();
            }

            jQuery('#rs_local_reward_type_for_facebook').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_local_reward_points_facebook').parent().parent().show();
                    jQuery('#rs_local_reward_percent_facebook').parent().parent().hide();
                } else {
                    jQuery('#rs_local_reward_points_facebook').parent().parent().hide();
                    jQuery('#rs_local_reward_percent_facebook').parent().parent().show();
                }
            });

            if (jQuery('#rs_local_reward_type_for_facebook_share').val() === '1') {
                jQuery('#rs_local_reward_points_facebook_share').parent().parent().show();
                jQuery('#rs_local_reward_percent_facebook_share').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_facebook_share').parent().parent().hide();
                jQuery('#rs_local_reward_percent_facebook_share').parent().parent().show();
            }

            jQuery('#rs_local_reward_type_for_facebook_share').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_local_reward_points_facebook_share').parent().parent().show();
                    jQuery('#rs_local_reward_percent_facebook_share').parent().parent().hide();
                } else {
                    jQuery('#rs_local_reward_points_facebook_share').parent().parent().hide();
                    jQuery('#rs_local_reward_percent_facebook_share').parent().parent().show();
                }
            });

            if (jQuery('#rs_local_reward_type_for_twitter').val() === '1') {
                jQuery('#rs_local_reward_points_twitter').parent().parent().show();
                jQuery('#rs_local_reward_percent_twitter').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_twitter').parent().parent().hide();
                jQuery('#rs_local_reward_percent_twitter').parent().parent().show();
            }

            jQuery('#rs_local_reward_type_for_twitter').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_local_reward_points_twitter').parent().parent().show();
                    jQuery('#rs_local_reward_percent_twitter').parent().parent().hide();
                } else {
                    jQuery('#rs_local_reward_points_twitter').parent().parent().hide();
                    jQuery('#rs_local_reward_percent_twitter').parent().parent().show();
                }
            });

            if (jQuery('#rs_local_reward_type_for_twitter_follow').val() === '1') {
                jQuery('#rs_local_reward_points_twitter_follow').parent().parent().show();
                jQuery('#rs_local_reward_percent_twitter_follow').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_twitter_follow').parent().parent().hide();
                jQuery('#rs_local_reward_percent_twitter_follow').parent().parent().show();
            }

            jQuery('#rs_local_reward_type_for_twitter_follow').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_local_reward_points_twitter_follow').parent().parent().show();
                    jQuery('#rs_local_reward_percent_twitter_follow').parent().parent().hide();
                } else {
                    jQuery('#rs_local_reward_points_twitter_follow').parent().parent().hide();
                    jQuery('#rs_local_reward_percent_twitter_follow').parent().parent().show();
                }
            });
            if (jQuery('#rs_local_reward_type_for_vk').val() === '1') {
                jQuery('#rs_local_reward_points_vk').parent().parent().show();
                jQuery('#rs_local_reward_percent_vk').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_vk').parent().parent().hide();
                jQuery('#rs_local_reward_percent_vk').parent().parent().show();
            }

            jQuery('#rs_local_reward_type_for_vk').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_local_reward_points_vk').parent().parent().show();
                    jQuery('#rs_local_reward_percent_vk').parent().parent().hide();
                } else {
                    jQuery('#rs_local_reward_points_vk').parent().parent().hide();
                    jQuery('#rs_local_reward_percent_vk').parent().parent().show();
                }
            });
            if (jQuery('#rs_local_reward_type_for_instagram').val() === '1') {
                jQuery('#rs_local_reward_points_instagram').parent().parent().show();
                jQuery('#rs_local_reward_percent_instagram').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_instagram').parent().parent().hide();
                jQuery('#rs_local_reward_percent_instagram').parent().parent().show();
            }

            jQuery('#rs_local_reward_type_for_instagram').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_local_reward_points_instagram').parent().parent().show();
                    jQuery('#rs_local_reward_percent_instagram').parent().parent().hide();
                } else {
                    jQuery('#rs_local_reward_points_instagram').parent().parent().hide();
                    jQuery('#rs_local_reward_percent_instagram').parent().parent().show();
                }
            });
            if (jQuery('#rs_local_reward_type_for_google').val() === '1') {
                jQuery('#rs_local_reward_points_google').parent().parent().show();
                jQuery('#rs_local_reward_percent_google').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_google').parent().parent().hide();
                jQuery('#rs_local_reward_percent_google').parent().parent().show();
            }

            jQuery('#rs_local_reward_type_for_google').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_local_reward_points_google').parent().parent().show();
                    jQuery('#rs_local_reward_percent_google').parent().parent().hide();
                } else {
                    jQuery('#rs_local_reward_points_google').parent().parent().hide();
                    jQuery('#rs_local_reward_percent_google').parent().parent().show();
                }
            });

            if (jQuery('#rs_local_reward_type_for_ok_follow').val() === '1') {
                jQuery('#rs_local_reward_points_ok_follow').parent().parent().show();
                jQuery('#rs_local_reward_percent_ok_follow').parent().parent().hide();
            } else {
                jQuery('#rs_local_reward_points_ok_follow').parent().parent().hide();
                jQuery('#rs_local_reward_percent_ok_follow').parent().parent().show();
            }

            jQuery('#rs_local_reward_type_for_ok_follow').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_local_reward_points_ok_follow').parent().parent().show();
                    jQuery('#rs_local_reward_percent_ok_follow').parent().parent().hide();
                } else {
                    jQuery('#rs_local_reward_points_ok_follow').parent().parent().hide();
                    jQuery('#rs_local_reward_percent_ok_follow').parent().parent().show();
                }
            });
        }
    });

    /*Show or hide Settings for Bulk Update Tab - End*/

    /*Show or hide Settings for Social Reward Tab - Start*/
    if ((jQuery('#rs_global_social_facebook_url').val()) === '2') {
        jQuery('#rs_global_social_facebook_url_custom').parent().parent().show();
    } else {
        jQuery('#rs_global_social_facebook_url_custom').parent().parent().hide();
    }
    if ((jQuery('#rs_global_social_twitter_url').val()) === '2') {
        jQuery('#rs_global_social_twitter_url_custom').parent().parent().show();
    } else {
        jQuery('#rs_global_social_twitter_url_custom').parent().parent().hide();
    }
    if ((jQuery('#rs_global_social_ok_url').val()) === '2') {
        jQuery('#rs_global_social_ok_url_custom').parent().parent().show();
    } else {
        jQuery('#rs_global_social_ok_url_custom').parent().parent().hide();
    }
    if ((jQuery('#rs_global_social_google_url').val()) === '2') {
        jQuery('#rs_global_social_google_url_custom').parent().parent().show();
    } else {
        jQuery('#rs_global_social_google_url_custom').parent().parent().hide();
    }
    jQuery('#rs_global_social_facebook_url').change(function () {
        jQuery('#rs_global_social_facebook_url_custom').parent().parent().toggle();
    });
    jQuery('#rs_global_social_ok_url').change(function () {
        jQuery('#rs_global_social_ok_url_custom').parent().parent().toggle();
    });
    jQuery('#rs_global_social_twitter_url').change(function () {
        jQuery('#rs_global_social_twitter_url_custom').parent().parent().toggle();
    });
    jQuery('#rs_global_social_google_url').change(function () {
        jQuery('#rs_global_social_google_url_custom').parent().parent().toggle();
    });

    if (jQuery('#rs_global_social_enable_disable_reward').val() == '2') {
        jQuery('.show_if_social_tab_enable').parent().parent().hide();
    } else {
        jQuery('.show_if_social_tab_enable').parent().parent().show();

        /*Facebook Reward Type Validation in jQuery Start*/
        if ((jQuery('#rs_global_social_reward_type_facebook').val()) === '1') {
            jQuery('#rs_global_social_facebook_reward_points').parent().parent().show();
            jQuery('#rs_global_social_facebook_reward_percent').parent().parent().hide();
        } else {
            jQuery('#rs_global_social_facebook_reward_points').parent().parent().hide();
            jQuery('#rs_global_social_facebook_reward_percent').parent().parent().show();
        }
        jQuery('#rs_global_social_reward_type_facebook').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_global_social_facebook_reward_points').parent().parent().show();
                jQuery('#rs_global_social_facebook_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_facebook_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_facebook_reward_percent').parent().parent().show();
            }
        });
        if ((jQuery('#rs_global_social_reward_type_facebook_share').val()) === '1') {
            jQuery('#rs_global_social_facebook_share_reward_points').parent().parent().show();
            jQuery('#rs_global_social_facebook_share_reward_percent').parent().parent().hide();
        } else {
            jQuery('#rs_global_social_facebook_share_reward_points').parent().parent().hide();
            jQuery('#rs_global_social_facebook_share_reward_percent').parent().parent().show();
        }
        jQuery('#rs_global_social_reward_type_facebook_share').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_global_social_facebook_share_reward_points').parent().parent().show();
                jQuery('#rs_global_social_facebook_share_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_facebook_share_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_facebook_share_reward_percent').parent().parent().show();
            }
        });
        /*Facebook Reward Type Validation in jQuery Ends*/

        /*Twitter Reward Type Validation in jQuery Start*/
        if ((jQuery('#rs_global_social_reward_type_twitter').val()) === '1') {
            jQuery('#rs_global_social_twitter_reward_points').parent().parent().show();
            jQuery('#rs_global_social_twitter_reward_percent').parent().parent().hide();
        } else {
            jQuery('#rs_global_social_twitter_reward_points').parent().parent().hide();
            jQuery('#rs_global_social_twitter_reward_percent').parent().parent().show();
        }
        jQuery('#rs_global_social_reward_type_twitter').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_global_social_twitter_reward_points').parent().parent().show();
                jQuery('#rs_global_social_twitter_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_twitter_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_twitter_reward_percent').parent().parent().show();
            }
        });

        /*Twitter Reward Type Validation in jQuery Ends*/
        if ((jQuery('#rs_global_social_reward_type_twitter_follow').val()) === '1') {
            jQuery('#rs_global_social_twitter_follow_reward_points').parent().parent().show();
            jQuery('#rs_global_social_twitter_follow_reward_percent').parent().parent().hide();
        } else {
            jQuery('#rs_global_social_twitter_follow_reward_points').parent().parent().hide();
            jQuery('#rs_global_social_twitter_follow_reward_percent').parent().parent().show();
        }
        jQuery('#rs_global_social_reward_type_twitter_follow').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_global_social_twitter_follow_reward_points').parent().parent().show();
                jQuery('#rs_global_social_twitter_follow_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_twitter_follow_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_twitter_follow_reward_percent').parent().parent().show();
            }
        });

        /*ok.ru Reward Type Validation in jQuery Ends*/
        if ((jQuery('#rs_global_social_reward_type_ok_follow').val()) === '1') {
            jQuery('#rs_global_social_ok_follow_reward_points').parent().parent().show();
            jQuery('#rs_global_social_ok_follow_reward_percent').parent().parent().hide();
        } else {
            jQuery('#rs_global_social_ok_follow_reward_points').parent().parent().hide();
            jQuery('#rs_global_social_ok_follow_reward_percent').parent().parent().show();
        }
        jQuery('#rs_global_social_reward_type_ok_follow').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_global_social_ok_follow_reward_points').parent().parent().show();
                jQuery('#rs_global_social_ok_follow_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_ok_follow_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_ok_follow_reward_percent').parent().parent().show();
            }
        });
        /*Google Reward Type Validation in jQuery Start*/
        if ((jQuery('#rs_global_social_reward_type_google').val()) === '1') {
            jQuery('#rs_global_social_google_reward_points').parent().parent().show();
            jQuery('#rs_global_social_google_reward_percent').parent().parent().hide();
        } else {
            jQuery('#rs_global_social_google_reward_points').parent().parent().hide();
            jQuery('#rs_global_social_google_reward_percent').parent().parent().show();
        }
        jQuery('#rs_global_social_reward_type_google').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_global_social_google_reward_points').parent().parent().show();
                jQuery('#rs_global_social_google_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_google_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_google_reward_percent').parent().parent().show();
            }
        });
        /*Google Reward Type Validation in jQuery Ends*/

        /*VK Reward Type Validation in jQuery Start*/
        if ((jQuery('#rs_global_social_reward_type_vk').val()) === '1') {
            jQuery('#rs_global_social_vk_reward_points').parent().parent().show();
            jQuery('#rs_global_social_vk_reward_percent').parent().parent().hide();
        } else {
            jQuery('#rs_global_social_vk_reward_points').parent().parent().hide();
            jQuery('#rs_global_social_vk_reward_percent').parent().parent().show();
        }
        jQuery('#rs_global_social_reward_type_vk').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_global_social_vk_reward_points').parent().parent().show();
                jQuery('#rs_global_social_vk_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_vk_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_vk_reward_percent').parent().parent().show();
            }
        });
        if ((jQuery('#rs_global_social_reward_type_instagram').val()) === '1') {
            jQuery('#rs_global_social_instagram_reward_points').parent().parent().show();
            jQuery('#rs_global_social_instagram_reward_percent').parent().parent().hide();
        } else {
            jQuery('#rs_global_social_instagram_reward_points').parent().parent().hide();
            jQuery('#rs_global_social_instagram_reward_percent').parent().parent().show();
        }
        jQuery('#rs_global_social_reward_type_instagram').change(function () {
            if ((jQuery(this).val()) === '1') {
                jQuery('#rs_global_social_instagram_reward_points').parent().parent().show();
                jQuery('#rs_global_social_instagram_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_instagram_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_instagram_reward_percent').parent().parent().show();
            }
        });
        /*VK Reward Type Validation in jQuery Ends*/

//        if (jQuery('#product-type').val() === 'variable') {
//            jQuery('._social_rewardsystem_options_facebook_field').css('display', 'none');
//            jQuery('._social_rewardsystem_options_facebook_share_field').css('display', 'none');
//            jQuery('._social_rewardsystem_options_twitter_field').css('display', 'none');
//            jQuery('._social_rewardsystem_options_google_field').css('display', 'none');
//        } else {
//            jQuery('._social_rewardsystem_options_facebook_field').css('display', 'block');
//            jQuery('._social_rewardsystem_options_facebook_share_field').css('display', 'block');
//            jQuery('._social_rewardsystem_options_twitter_field').css('display', 'block');
//            jQuery('._social_rewardsystem_options_google_field').css('display', 'block');
//        }
//        jQuery('#product-type').change(function () {
//            if (jQuery(this).val() === 'variable') {
//                jQuery('._social_rewardsystem_options_facebook_field').css('display', 'none');
//                jQuery('._social_rewardsystem_options_facebook_share_field').css('display', 'none');
//
//                jQuery('._social_rewardsystem_options_twitter_field').css('display', 'none');
//                jQuery('._social_rewardsystem_options_google_field').css('display', 'none');
//            } else {
//                jQuery('._social_rewardsystem_options_facebook_field').css('display', 'block');
//                jQuery('._social_rewardsystem_options_facebook_share_field').css('display', 'block');
//                jQuery('._social_rewardsystem_options_twitter_field').css('display', 'block');
//                jQuery('._social_rewardsystem_options_google_field').css('display', 'block');
//            }
//        });
    }

    jQuery('#rs_global_social_enable_disable_reward').change(function () {
        if (jQuery('#rs_global_social_enable_disable_reward').val() == '2') {
            jQuery('.show_if_social_tab_enable').parent().parent().hide();
        } else {
            jQuery('.show_if_social_tab_enable').parent().parent().show();

            /*Facebook Reward Type Validation in jQuery Start*/
            if ((jQuery('#rs_global_social_reward_type_facebook').val()) === '1') {
                jQuery('#rs_global_social_facebook_reward_points').parent().parent().show();
                jQuery('#rs_global_social_facebook_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_facebook_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_facebook_reward_percent').parent().parent().show();
            }
            jQuery('#rs_global_social_reward_type_facebook').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_global_social_facebook_reward_points').parent().parent().show();
                    jQuery('#rs_global_social_facebook_reward_percent').parent().parent().hide();
                } else {
                    jQuery('#rs_global_social_facebook_reward_points').parent().parent().hide();
                    jQuery('#rs_global_social_facebook_reward_percent').parent().parent().show();
                }
            });
            /*Facebook Reward Type Validation in jQuery Ends*/

            /*Twitter Reward Type Validation in jQuery Start*/
            if ((jQuery('#rs_global_social_reward_type_twitter').val()) === '1') {
                jQuery('#rs_global_social_twitter_reward_points').parent().parent().show();
                jQuery('#rs_global_social_twitter_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_twitter_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_twitter_reward_percent').parent().parent().show();
            }
            jQuery('#rs_global_social_reward_type_twitter').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_global_social_twitter_reward_points').parent().parent().show();
                    jQuery('#rs_global_social_twitter_reward_percent').parent().parent().hide();
                } else {
                    jQuery('#rs_global_social_twitter_reward_points').parent().parent().hide();
                    jQuery('#rs_global_social_twitter_reward_percent').parent().parent().show();
                }
            });
            /*Twitter Reward Type Validation in jQuery Ends*/
            if ((jQuery('#rs_global_social_reward_type_twitter_follow').val()) === '1') {
                jQuery('#rs_global_social_twitter_follow_reward_points').parent().parent().show();
                jQuery('#rs_global_social_twitter_follow_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_twitter_follow_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_twitter_follow_reward_percent').parent().parent().show();
            }
            jQuery('#rs_global_social_reward_type_twitter_follow').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_global_social_twitte_follow_reward_points').parent().parent().show();
                    jQuery('#rs_global_social_twitter_follow_reward_percent').parent().parent().hide();
                } else {
                    jQuery('#rs_global_social_twitter_follow_reward_points').parent().parent().hide();
                    jQuery('#rs_global_social_twitter_follow_reward_percent').parent().parent().show();
                }
            });

            /*OK.ru Reward Type Validation in jQuery Ends*/
            if ((jQuery('#rs_global_social_reward_type_ok_follow').val()) === '1') {
                jQuery('#rs_global_social_ok_follow_reward_points').parent().parent().show();
                jQuery('#rs_global_social_ok_follow_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_ok_follow_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_ok_follow_reward_percent').parent().parent().show();
            }
            jQuery('#rs_global_social_reward_type_ok_follow').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_global_social_ok_follow_reward_points').parent().parent().show();
                    jQuery('#rs_global_social_ok_follow_reward_percent').parent().parent().hide();
                } else {
                    jQuery('#rs_global_social_ok_follow_reward_points').parent().parent().hide();
                    jQuery('#rs_global_social_ok_follow_reward_percent').parent().parent().show();
                }
            });
            /*Google Reward Type Validation in jQuery Start*/
            if ((jQuery('#rs_global_social_reward_type_google').val()) === '1') {
                jQuery('#rs_global_social_google_reward_points').parent().parent().show();
                jQuery('#rs_global_social_google_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_google_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_google_reward_percent').parent().parent().show();
            }
            jQuery('#rs_global_social_reward_type_google').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_global_social_google_reward_points').parent().parent().show();
                    jQuery('#rs_global_social_google_reward_percent').parent().parent().hide();
                } else {
                    jQuery('#rs_global_social_google_reward_points').parent().parent().hide();
                    jQuery('#rs_global_social_google_reward_percent').parent().parent().show();
                }
            });
            /*Google Reward Type Validation in jQuery Ends*/

            /*VK Reward Type Validation in jQuery Start*/
            if ((jQuery('#rs_global_social_reward_type_vk').val()) === '1') {
                jQuery('#rs_global_social_vk_reward_points').parent().parent().show();
                jQuery('#rs_global_social_vk_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_vk_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_vk_reward_percent').parent().parent().show();
            }
            jQuery('#rs_global_social_reward_type_vk').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_global_social_vk_reward_points').parent().parent().show();
                    jQuery('#rs_global_social_vk_reward_percent').parent().parent().hide();
                } else {
                    jQuery('#rs_global_social_vk_reward_points').parent().parent().hide();
                    jQuery('#rs_global_social_vk_reward_percent').parent().parent().show();
                }
            });
            /*VK Reward Type Validation in jQuery Ends*/
            if ((jQuery('#rs_global_social_reward_type_instagram').val()) === '1') {
                jQuery('#rs_global_social_instagram_reward_points').parent().parent().show();
                jQuery('#rs_global_social_instagram_reward_percent').parent().parent().hide();
            } else {
                jQuery('#rs_global_social_instagram_reward_points').parent().parent().hide();
                jQuery('#rs_global_social_instagram_reward_percent').parent().parent().show();
            }
            jQuery('#rs_global_social_reward_type_instagram').change(function () {
                if ((jQuery(this).val()) === '1') {
                    jQuery('#rs_global_social_instagram_reward_points').parent().parent().show();
                    jQuery('#rs_global_social_instagram_reward_percent').parent().parent().hide();
                } else {
                    jQuery('#rs_global_social_instagram_reward_points').parent().parent().hide();
                    jQuery('#rs_global_social_instagram_reward_percent').parent().parent().show();
                }
            });

            if (jQuery('#product-type').val() === 'variable') {
                jQuery('._social_rewardsystem_options_facebook_field').css('display', 'none');
                jQuery('._social_rewardsystem_options_facebook_share_field').css('display', 'none');
                jQuery('._social_rewardsystem_options_twitter_field').css('display', 'none');
                jQuery('._social_rewardsystem_options_twitter_field_follow').css('display', 'none');


                jQuery('._social_rewardsystem_options_google_field').css('display', 'none');
            } else {
                jQuery('._social_rewardsystem_options_facebook_field').css('display', 'block');
                jQuery('._social_rewardsystem_options_facebook_share_field').css('display', 'block');
                jQuery('._social_rewardsystem_options_twitter_field').css('display', 'block');
                jQuery('._social_rewardsystem_options_twitter_field_follow').css('display', 'block');

                jQuery('._social_rewardsystem_options_google_field').css('display', 'block');
            }
            jQuery('#product-type').change(function () {
                if (jQuery(this).val() === 'variable') {
                    jQuery('._social_rewardsystem_options_facebook_field').css('display', 'none');
                    jQuery('._social_rewardsystem_options_facebook_share_field').css('display', 'none');
                    jQuery('._social_rewardsystem_options_twitter_field').css('display', 'none');
                    jQuery('._social_rewardsystem_options_twitter_field_follow').css('display', 'none');

                    jQuery('._social_rewardsystem_options_google_field').css('display', 'none');
                } else {
                    jQuery('._social_rewardsystem_options_facebook_field').css('display', 'block');
                    jQuery('._social_rewardsystem_options_facebook_share_field').css('display', 'block');
                    jQuery('._social_rewardsystem_options_twitter_field').css('display', 'block');
                    jQuery('._social_rewardsystem_options_twitter_field_follow').css('display', 'block');

                    jQuery('._social_rewardsystem_options_google_field').css('display', 'block');
                }

            });
        }
    });
    /*Show or hide Settings for Social Reward Tab - End*/

    /*Show or hide Settings for SMS Tab - Start*/

    if ((jQuery('input[name=rs_sms_sending_api_option]:checked').val()) === '1') {
        jQuery('#rs_nexmo_key').parent().parent().hide();
        jQuery('#rs_nexmo_secret').parent().parent().hide();
        jQuery('#rs_twilio_secret_account_id').parent().parent().show();
        jQuery('#rs_twilio_auth_token_id').parent().parent().show();
        jQuery('#rs_twilio_from_number').parent().parent().show();
    } else {
        jQuery('#rs_nexmo_key').parent().parent().show();
        jQuery('#rs_nexmo_secret').parent().parent().show();
        jQuery('#rs_twilio_secret_account_id').parent().parent().hide();
        jQuery('#rs_twilio_auth_token_id').parent().parent().hide();
        jQuery('#rs_twilio_from_number').parent().parent().hide();
    }
    jQuery('input[name=rs_sms_sending_api_option]:radio').change(function () {
        if ((jQuery('input[name=rs_sms_sending_api_option]:checked').val()) === '1') {
            jQuery('#rs_nexmo_key').parent().parent().hide();
            jQuery('#rs_nexmo_secret').parent().parent().hide();
            jQuery('#rs_twilio_secret_account_id').parent().parent().show();
            jQuery('#rs_twilio_auth_token_id').parent().parent().show();
            jQuery('#rs_twilio_from_number').parent().parent().show();
        } else {
            jQuery('#rs_nexmo_key').parent().parent().show();
            jQuery('#rs_nexmo_secret').parent().parent().show();
            jQuery('#rs_twilio_secret_account_id').parent().parent().toggle();
            jQuery('#rs_twilio_auth_token_id').parent().parent().toggle();
            jQuery('#rs_twilio_from_number').parent().parent().toggle();
        }
    });

    /*Show or hide Settings for SMS Tab - End*/

    /*Show or hide Settings for Order Tab - Start*/

    if (jQuery('#rs_enable_msg_for_earned_points').is(":checked")) {
        jQuery('#rs_msg_for_earned_points').parent().parent().show();
    } else {
        jQuery('#rs_msg_for_earned_points').parent().parent().hide();
    }

    jQuery('#rs_enable_msg_for_earned_points').change(function () {
        if (jQuery('#rs_enable_msg_for_earned_points').is(":checked")) {
            jQuery('#rs_msg_for_earned_points').parent().parent().show();
        } else {
            jQuery('#rs_msg_for_earned_points').parent().parent().hide();
        }
    });

    if (jQuery('#rs_enable_msg_for_redeem_points').is(":checked")) {
        jQuery('#rs_msg_for_redeem_points').parent().parent().show();
    } else {
        jQuery('#rs_msg_for_redeem_points').parent().parent().hide();
    }

    jQuery('#rs_enable_msg_for_redeem_points').change(function () {
        if (jQuery('#rs_enable_msg_for_redeem_points').is(":checked")) {
            jQuery('#rs_msg_for_redeem_points').parent().parent().show();
        } else {
            jQuery('#rs_msg_for_redeem_points').parent().parent().hide();
        }
    });

    /*Show or hide Settings for Order Tab - End*/

    /*Show or hide Settings for Import/Export Tab - Start*/

    if ((jQuery('input[name=rs_export_import_user_option]:checked').val()) === '2') {
        jQuery('#rs_import_export_users_list').parent().parent().show();
    } else {
        jQuery('#rs_import_export_users_list').parent().parent().hide();
    }
    jQuery('input[name=rs_export_import_user_option]:radio').change(function () {
        jQuery('#rs_import_export_users_list').parent().parent().toggle();
    });

    if ((jQuery('input[name=rs_export_import_date_option]:checked').val()) === '2') {
        jQuery('#rs_point_export_start_date').parent().parent().show();
        jQuery('#rs_point_export_end_date').parent().parent().show();
    } else {
        jQuery('#rs_point_export_start_date').parent().parent().hide();
        jQuery('#rs_point_export_end_date').parent().parent().hide();
    }
    jQuery('input[name=rs_export_import_date_option]:radio').change(function () {
        jQuery('#rs_point_export_start_date').parent().parent().toggle();
        jQuery('#rs_point_export_end_date').parent().parent().toggle();
    });

    /*Show or hide Settings for Import/Export Tab - End*/

    /*Show or hide Settings for Report in CSV Tab - Start*/

    if ((jQuery('input[name=rs_export_user_report_option]:checked').val()) === '2') {
        jQuery('#rs_export_users_report_list').parent().parent().show();
    } else {
        jQuery('#rs_export_users_report_list').parent().parent().hide();
    }
    jQuery('input[name=rs_export_user_report_option]:radio').change(function () {
        jQuery('#rs_export_users_report_list').parent().parent().toggle();
    });

    if ((jQuery('input[name=rs_export_report_date_option]:checked').val()) === '2') {
        jQuery('#rs_point_export_report_start_date').parent().parent().show();
        jQuery('#rs_point_export_report_end_date').parent().parent().show();
    } else {
        jQuery('#rs_point_export_report_start_date').parent().parent().hide();
        jQuery('#rs_point_export_report_end_date').parent().parent().hide();
    }
    jQuery('input[name=rs_export_report_date_option]:radio').change(function () {
        jQuery('#rs_point_export_report_start_date').parent().parent().toggle();
        jQuery('#rs_point_export_report_end_date').parent().parent().toggle();
    });

    /*Show or hide Settings for Report in CSV Tab - End*/

    /*Show or hide Settings for Form for Send Points Tab - Start*/

    if (jQuery('#rs_select_send_points_user_type').val() == '1') {
        jQuery('#rs_select_users_list_for_send_point').parent().parent().hide();

    }
    jQuery('#rs_select_send_points_user_type').change(function () {
        var currentvalue = jQuery(this).val();
        if (currentvalue == '2') {
            jQuery('#rs_select_users_list_for_send_point').parent().parent().show();
        } else {
            jQuery('#rs_select_users_list_for_send_point').parent().parent().hide();
        }
    });
    var restrict_point_enable = "<?php echo get_option('rs_limit_for_send_point'); ?>";
    if (restrict_point_enable == '1') {

        jQuery('#rs_limit_send_points_request').parent().parent().hide();
    } else {
        jQuery('#rs_limit_send_points_request').parent().parent().hide();
    }
    jQuery('#rs_limit_for_send_point').change(function () {
        var currentvalue = jQuery(this).val();
        if (currentvalue == '1') {
            jQuery('#rs_limit_send_points_request').parent().parent().show();
        } else {
            jQuery('#rs_limit_send_points_request').parent().parent().hide();
        }
    });

    /*Show or hide Settings for Form for Send Points Tab - End*/

    /*Show or hide Settings for Nominee Tab - Start*/

    var currentvalue = jQuery('#rs_show_hide_nominee_field').val();
    if (currentvalue == '1') {
        jQuery('#rs_my_nominee_title').parent().parent().show();
        jQuery('#rs_select_type_of_user_for_nominee').parent().parent().show();

        if (jQuery('#rs_select_type_of_user_for_nominee').val() == '1') {
            jQuery('#rs_select_users_list_for_nominee').parent().parent().show();
            jQuery('#rs_select_users_role_for_nominee').parent().parent().hide();
        } else {
            jQuery('#rs_select_users_list_for_nominee').parent().parent().hide();
            jQuery('#rs_select_users_role_for_nominee').parent().parent().show();
        }

        jQuery('#rs_select_type_of_user_for_nominee').change(function () {
            if (jQuery('#rs_select_type_of_user_for_nominee').val() == '1') {
                jQuery('#rs_select_users_list_for_nominee').parent().parent().show();
                jQuery('#rs_select_users_role_for_nominee').parent().parent().hide();
            } else {
                jQuery('#rs_select_users_list_for_nominee').parent().parent().hide();
                jQuery('#rs_select_users_role_for_nominee').parent().parent().show();
            }
        });
    } else {
        jQuery('#rs_my_nominee_title').parent().parent().hide();
        jQuery('#rs_select_type_of_user_for_nominee').parent().parent().hide();
        jQuery('#rs_select_users_list_for_nominee').parent().parent().hide();
        jQuery('#rs_select_users_role_for_nominee').parent().parent().hide();
    }

    jQuery('#rs_show_hide_nominee_field').change(function () {
        var currentvalue = jQuery('#rs_show_hide_nominee_field').val();
        if (currentvalue == '1') {
            jQuery('#rs_my_nominee_title').parent().parent().show();
            jQuery('#rs_select_type_of_user_for_nominee').parent().parent().show();

            if (jQuery('#rs_select_type_of_user_for_nominee').val() == '1') {
                jQuery('#rs_select_users_list_for_nominee').parent().parent().show();
                jQuery('#rs_select_users_role_for_nominee').parent().parent().hide();
            } else {
                jQuery('#rs_select_users_list_for_nominee').parent().parent().hide();
                jQuery('#rs_select_users_role_for_nominee').parent().parent().show();
            }

            jQuery('#rs_select_type_of_user_for_nominee').change(function () {
                if (jQuery('#rs_select_type_of_user_for_nominee').val() == '1') {
                    jQuery('#rs_select_users_list_for_nominee').parent().parent().show();
                    jQuery('#rs_select_users_role_for_nominee').parent().parent().hide();
                } else {
                    jQuery('#rs_select_users_list_for_nominee').parent().parent().hide();
                    jQuery('#rs_select_users_role_for_nominee').parent().parent().show();
                }
            });
        } else {
            jQuery('#rs_my_nominee_title').parent().parent().hide();
            jQuery('#rs_select_type_of_user_for_nominee').parent().parent().hide();
            jQuery('#rs_select_users_list_for_nominee').parent().parent().hide();
            jQuery('#rs_select_users_role_for_nominee').parent().parent().hide();
        }
    });


    var currentvalues = jQuery('#rs_show_hide_nominee_field_in_checkout').val();
    if (currentvalues == '1') {
        jQuery('#rs_my_nominee_title_in_checkout').parent().parent().show();
        jQuery('#rs_select_type_of_user_for_nominee_checkout').parent().parent().show();

        if (jQuery('#rs_select_type_of_user_for_nominee_checkout').val() == '1') {
            jQuery('#rs_select_users_list_for_nominee_in_checkout').parent().parent().show();
            jQuery('#rs_select_users_role_for_nominee_checkout').parent().parent().hide();
        } else {
            jQuery('#rs_select_users_list_for_nominee_in_checkout').parent().parent().hide();
            jQuery('#rs_select_users_role_for_nominee_checkout').parent().parent().show();
        }

        jQuery('#rs_select_type_of_user_for_nominee_checkout').change(function () {
            if (jQuery('#rs_select_type_of_user_for_nominee_checkout').val() == '1') {
                jQuery('#rs_select_users_list_for_nominee_in_checkout').parent().parent().show();
                jQuery('#rs_select_users_role_for_nominee_checkout').parent().parent().hide();
            } else {
                jQuery('#rs_select_users_list_for_nominee_in_checkout').parent().parent().hide();
                jQuery('#rs_select_users_role_for_nominee_checkout').parent().parent().show();
            }
        });
    } else {
        jQuery('#rs_my_nominee_title_in_checkout').parent().parent().hide();
        jQuery('#rs_select_users_list_for_nominee_in_checkout').parent().parent().hide();
        jQuery('#rs_select_users_role_for_nominee_checkout').parent().parent().hide();
        jQuery('#rs_select_type_of_user_for_nominee_checkout').parent().parent().hide();
    }

    jQuery('#rs_show_hide_nominee_field_in_checkout').change(function () {
        var currentvalues = jQuery('#rs_show_hide_nominee_field_in_checkout').val();
        if (currentvalues == '1') {
            jQuery('#rs_my_nominee_title_in_checkout').parent().parent().show();
            jQuery('#rs_select_type_of_user_for_nominee_checkout').parent().parent().show();

            if (jQuery('#rs_select_type_of_user_for_nominee_checkout').val() == '1') {
                jQuery('#rs_select_users_list_for_nominee_in_checkout').parent().parent().show();
                jQuery('#rs_select_users_role_for_nominee_checkout').parent().parent().hide();
            } else {
                jQuery('#rs_select_users_list_for_nominee_in_checkout').parent().parent().hide();
                jQuery('#rs_select_users_role_for_nominee_checkout').parent().parent().show();
            }

            jQuery('#rs_select_type_of_user_for_nominee_checkout').change(function () {
                if (jQuery('#rs_select_type_of_user_for_nominee_checkout').val() == '1') {
                    jQuery('#rs_select_users_list_for_nominee_in_checkout').parent().parent().show();
                    jQuery('#rs_select_users_role_for_nominee_checkout').parent().parent().hide();
                } else {
                    jQuery('#rs_select_users_list_for_nominee_in_checkout').parent().parent().hide();
                    jQuery('#rs_select_users_role_for_nominee_checkout').parent().parent().show();
                }
            });
        } else {
            jQuery('#rs_my_nominee_title_in_checkout').parent().parent().hide();
            jQuery('#rs_select_users_list_for_nominee_in_checkout').parent().parent().hide();
            jQuery('#rs_select_users_role_for_nominee_checkout').parent().parent().hide();
            jQuery('#rs_select_type_of_user_for_nominee_checkout').parent().parent().hide();
        }
    });

    /*Show or hide Settings for Nominee Tab - End*/

    /*Show or hide Settings for Point URL Tab - Start*/

    jQuery('#rs_expiry_time_for_pointurl').datepicker({dateFormat: 'yy-mm-dd', minDate: 0});
    if (jQuery('#rs_time_limit_for_pointurl').val() == '1') {
        jQuery('#rs_expiry_time_for_pointurl').parent().parent().hide();
    } else {
        jQuery('#rs_expiry_time_for_pointurl').parent().parent().show();
    }

    jQuery('#rs_time_limit_for_pointurl').change(function () {
        if (jQuery('#rs_time_limit_for_pointurl').val() == '1') {
            jQuery('#rs_expiry_time_for_pointurl').parent().parent().hide();
        } else {
            jQuery('#rs_expiry_time_for_pointurl').parent().parent().show();
        }
    });

    if (jQuery('#rs_count_limit_for_pointurl').val() == '1') {
        jQuery('#rs_count_for_pointurl').parent().parent().hide();
    } else {
        jQuery('#rs_count_for_pointurl').parent().parent().show();
    }

    jQuery('#rs_count_limit_for_pointurl').change(function () {
        if (jQuery('#rs_count_limit_for_pointurl').val() == '1') {
            jQuery('#rs_count_for_pointurl').parent().parent().hide();
        } else {
            jQuery('#rs_count_for_pointurl').parent().parent().show();
        }
    });

    /*Show or hide Settings for Point URL Tab - End*/

    /*Show or hide Settings for Buying Reward Point - Start*/

    if (jQuery('#_rewardsystem_buying_reward_points').val() == 'no') {
        jQuery('.show_if_buy_reward_points_enable').parent().hide();
    } else {
        jQuery('.show_if_buy_reward_points_enable').parent().show();
    }

    jQuery('#_rewardsystem_buying_reward_points').change(function () {
        if (jQuery(this).val() == 'no') {
            jQuery('.show_if_buy_reward_points_enable').parent().hide();
        } else {
            jQuery('.show_if_buy_reward_points_enable').parent().show();
        }
    });

    /*Show or hide Settings for Buying Reward Point - End*/

    /*Show or hide Settings for Point Price in Product Page - Start*/

    if (jQuery('#_rewardsystem_enable_point_price').val() == 'no') {
        jQuery('#_rewardsystem__points').parent().hide();
        jQuery('#_rewardsystem_point_price_type').parent().hide();
        jQuery('#_rewardsystem__points_based_on_conversion').parent().hide();
        jQuery('#_rewardsystem_enable_point_price_type').parent().hide();
        jQuery('#_regular_price').parent().show();
        jQuery('#_sale_price').parent().show();
    } else {
        jQuery('._rewardsystem_enable_point_price_type').parent().show();
        if (jQuery('._rewardsystem_enable_point_price_type').val() == '2') {
            jQuery('#_rewardsystem__points_based_on_conversion').parent().hide();
            jQuery('#_rewardsystem_point_price_type').parent().hide();
            jQuery('#_regular_price').parent().hide();
            jQuery('#_sale_price').parent().hide();

        } else {
            jQuery('#_rewardsystem_point_price_type').parent().show();
            jQuery('#_regular_price').parent().show();
            jQuery('#_sale_price').parent().show();
        }
        if (jQuery('#_rewardsystem_point_price_type').val() == '1') {
            jQuery('#_rewardsystem__points').parent().show();
        } else {

            jQuery('._rewardsystem__points_based_on_conversion').parent().show();
        }
    }
    jQuery('#_rewardsystem_enable_point_price').change(function () {
        if (jQuery(this).val() == 'no') {
            jQuery('#_rewardsystem__points').parent().hide();
            jQuery('#_rewardsystem_point_price_type').parent().hide();
            jQuery('#_rewardsystem__points_based_on_conversion').parent().hide();
            jQuery('#_rewardsystem_enable_point_price_type').parent().hide();
            jQuery('#_regular_price').parent().show();
            jQuery('#_sale_price').parent().show();

        } else {
            jQuery('#_rewardsystem_enable_point_price_type').parent().show();
            if (jQuery('#_rewardsystem_enable_point_price_type').val() == '2') {
                jQuery('#_rewardsystem__points_based_on_conversion').parent().hide();
                jQuery('#_rewardsystem_point_price_type').parent().hide();
                jQuery('#_regular_price').parent().hide();
                jQuery('#_sale_price').parent().hide();
                jQuery('#_rewardsystem__points').parent().show();

            } else {
                jQuery('#_regular_price').parent().show();
                jQuery('#_sale_price').parent().show();
                jQuery('#_rewardsystem_point_price_type').parent().show();
                if (jQuery('#_rewardsystem_point_price_type').val() == '1') {
                    jQuery('#_rewardsystem__points').parent().show();

                } else {
                    jQuery('#_rewardsystem__points_based_on_conversion').parent().show();
                }
            }
        }
    });

    if (jQuery('._rewardsystem_enable_point_price_type').val() == '2') {
        jQuery('#_rewardsystem__points_based_on_conversion').parent().hide();
        jQuery('#_rewardsystem_point_price_type').parent().hide();
    }
    jQuery('._rewardsystem_enable_point_price_type').change(function () {
        if (jQuery('._rewardsystem_enable_point_price_type').val() == '2') {
            jQuery('#_rewardsystem__points').parent().show();
            jQuery('#_rewardsystem_point_price_type').parent().hide();
            jQuery('#_rewardsystem__points_based_on_conversion').parent().hide();
            jQuery('#_rewardsystem_point_price_type').parent().hide();
            jQuery('#_regular_price').parent().hide();
            jQuery('#_sale_price').parent().hide();
        } else {
            jQuery('#_rewardsystem_point_price_type').parent().show();
            jQuery('#_regular_price').parent().show();
            jQuery('#_sale_price').parent().show();
            if (jQuery('#_rewardsystem_point_price_type').val() == '1') {
                jQuery('#_rewardsystem__points').parent().show();
                jQuery('#_rewardsystem__points_based_on_conversion').parent().hide();
            } else {
                jQuery('#_rewardsystem__points_based_on_conversion').parent().show();
                jQuery('#_rewardsystem__points').parent().hide();
            }
        }
    });


    if (jQuery('#_rewardsystem_point_price_type').val() == '1') {

        jQuery('#_rewardsystem__points_based_on_conversion').parent().hide();

    } else {
        jQuery('#_rewardsystem__points').parent().hide();

    }
    jQuery('#_rewardsystem_point_price_type').change(function () {
        if (jQuery(this).val() == '1') {
            jQuery('#_rewardsystem__points').parent().show();
            jQuery('#_rewardsystem__points_based_on_conversion').parent().hide();

        } else {
            jQuery('#_rewardsystem__points_based_on_conversion').parent().show();
            jQuery('#_rewardsystem__points').parent().hide();
        }
    });

    /*Show or hide Settings for Point Price in Product Page - End*/

    /*Show or hide Settings for Auto Redeeming in Checkout - Start*/

    var enable_auto_redeem_checkbox = jQuery('#rs_enable_disable_auto_redeem_points').is(':checked') ? 'yes' : 'no';
    if (enable_auto_redeem_checkbox === 'yes') {
        jQuery('#rs_percentage_cart_total_auto_redeem').parent().parent().show();
        jQuery('#rs_enable_disable_auto_redeem_checkout').parent().parent().parent().parent().show();
    } else {
        jQuery('#rs_percentage_cart_total_auto_redeem').parent().parent().hide();
        jQuery('#rs_enable_disable_auto_redeem_checkout').parent().parent().parent().parent().hide();
    }

    jQuery('#rs_enable_disable_auto_redeem_points').click(function () {
        var enable_auto_redeem_checkbox = jQuery('#rs_enable_disable_auto_redeem_points').is(':checked') ? 'yes' : 'no';
        if (enable_auto_redeem_checkbox == 'yes') {
            jQuery('#rs_percentage_cart_total_auto_redeem').parent().parent().show();
            jQuery('#rs_enable_disable_auto_redeem_checkout').parent().parent().parent().parent().show();
        } else {
            jQuery('#rs_percentage_cart_total_auto_redeem').parent().parent().hide();
            jQuery('#rs_enable_disable_auto_redeem_checkout').parent().parent().parent().parent().hide();
        }
    });

    /*Show or hide Settings for Auto Redeeming in Checkout - End*/

});
