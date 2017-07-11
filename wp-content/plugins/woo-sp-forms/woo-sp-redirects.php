<?php
//redirects afrer login
add_filter('woocommerce_login_redirect', 'wf_afrer_login_redirect');
function wf_afrer_login_redirect($redirect) {
	$page_id = get_option('woo_sp_forms_redirect_after_login');
	$redirect = get_page_link($page_id);
	if(empty($redirect)){
		$redirect = get_site_url();
	}
	return $redirect ;
}

//redirects afrer registration
add_action('woocommerce_registration_redirect', 'wf_afrer_registration_redirect');
function wf_afrer_registration_redirect($redirect) {
	$page_id = get_option('woo_sp_forms_redirect_after_reg');
	$redirect = get_page_link($page_id);
	if(empty($redirect)){
		$redirect = get_site_url();
	}
	return $redirect ;
}

?>