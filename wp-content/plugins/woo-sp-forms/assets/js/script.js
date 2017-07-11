jQuery(document).ready(function($) {
	$('.woo_sp_close_window, .woo_sp_overlay').click(function (){
		$('.woo_sp_popup_login, .woo_sp_overlay').fadeOut("slow");
		return false;
	});

	$('li #woo_sp_login, #woo_sp_login').click(function(){
		$('.woo_sp_popup_login, .woo_sp_overlay').fadeIn("slow");
		return false;
	});
	
	$('.woo_sp_close_window, .woo_sp_overlay').click(function (){
		$('.woo_sp_popup_sign_up, .woo_sp_overlay').fadeOut("slow");
		return false;
	});

	$('li #woo_sp_sign_up, #woo_sp_sign_up').click(function(){
		$('.woo_sp_popup_sign_up, .woo_sp_overlay').fadeIn("slow");
		return false;
	});

	if($('*').is('.woo_sp_msg')) {
		 setTimeout(function(){
            $('.woo_sp_msg').fadeOut("slow");
        }, 2000);
	}
});		