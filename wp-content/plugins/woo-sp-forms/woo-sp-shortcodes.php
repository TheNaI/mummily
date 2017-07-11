<?php
//add Shortcode [wf_login_form]
function wf_login_form($atts) {
  return wc_get_template('login-form.php', array(), 'woo-sp-forms/', plugin_dir_path( __FILE__ ) . 'templates/');
}
add_shortcode('wf_login_form', 'wf_login_form');

//add Shortcode [wf_registration_form]
function wf_registration($atts) {
  return wc_get_template('registration-form.php', array(), 'woo-sp-forms/', plugin_dir_path( __FILE__ ) . 'templates/');
}
add_shortcode('wf_registration_form', 'wf_registration');

//add Shortcode [wf_login_link]
function wf_login_link($atts) {
	
  extract( shortcode_atts(array(
      "img_login" => '',
      "img_logout" => '',
  ), $atts ) );

  $woo_sp_forms_login_title = get_option('woo_sp_forms_login_title');
  $woo_sp_forms_logout_title = get_option('woo_sp_forms_logout_title');

  if(!empty($img_login) && !empty($img_logout)){
    $woo_sp_forms_img_login = '<img src='.$img_login.'>';
    $woo_sp_forms_img_logout = '<img src='.$img_logout.'>';
    $woo_sp_forms_login_title='';
    $woo_sp_forms_logout_title='';
  } else {
    $woo_sp_forms_img_login = '';
    $woo_sp_forms_img_logout = '';
  }
    
  if (is_user_logged_in()) {
    $wf_login_link = '<a href="'.wp_logout_url(home_url()).'">'.$woo_sp_forms_img_logout.''. __($woo_sp_forms_logout_title,'woocommerce') .'</a>';
  } else {
    $wf_login_link = '<a href="#" id="woo_sp_login">'.$woo_sp_forms_img_login.''.__($woo_sp_forms_login_title,'woocommerce').'</a>';
  }

  return $wf_login_link;	
}
add_shortcode('wf_login_link', 'wf_login_link');

//add Shortcode [wf_signup_link]
function wf_signup_link($atts) {

  extract( shortcode_atts(array(
      "img_signup" => '',
  ), $atts ) );

  $woo_sp_forms_reg_title = get_option('woo_sp_forms_reg_title');

  if(!empty($img_signup)){
    $woo_sp_forms_img_signup = '<img src='.$img_signup.'>';
    $woo_sp_forms_reg_title = '';
  } else {
    $woo_sp_forms_img_signup = '';
  }

  if (!is_user_logged_in()) {
    $wf_registration_link = '<a href="#" id="woo_sp_sign_up">'.$woo_sp_forms_img_signup.''.__($woo_sp_forms_reg_title,'woocommerce').'</a>';
  } else {
    $wf_registration_link = '';
  }	
  		
  return $wf_registration_link;
  
}
add_shortcode('wf_signup_link', 'wf_signup_link');

?>