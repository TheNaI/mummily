<?php
//menu triger
add_filter( 'wp_nav_menu_items', 'woo_sp_menu_link', 10, 2 );
function woo_sp_menu_link($items, $args) {

  $woo_sp_menu_list_id = get_option('woo_sp_forms_menu');
  $woo_sp_forms_login_title = get_option('woo_sp_forms_login_title');
  $woo_sp_forms_logout_title = get_option('woo_sp_forms_logout_title');
  $woo_sp_forms_reg_title = get_option('woo_sp_forms_reg_title');
  $woo_sp_menu_list_id_array = explode(';', $woo_sp_menu_list_id);

  for ($i=0; $i < count($woo_sp_menu_list_id_array); $i++) { 
    if ($args->menu->term_id== $woo_sp_menu_list_id_array[$i]) {
      if (is_user_logged_in()) {
        $items .= '<li class="menu-item"><a href="'.wp_logout_url(home_url()).'">'. __($woo_sp_forms_logout_title, 'woocommerce') .'</a></li>';
      } else {
        $items .= '<li class="menu-item"><a href="#" id="woo_sp_login">'.__($woo_sp_forms_login_title, 'woocommerce').'</a></li>';
        $items .= '<li class="menu-item"><a href="#" id="woo_sp_sign_up">'.__($woo_sp_forms_reg_title, 'woocommerce').'</a></li>';
      }
    }
  }
  
  return $items;
}

//forms
add_action('wp_footer', 'woo_sp_main');
function woo_sp_main(){
    $woo_sp_forms_main_color = get_option('woo_sp_forms_main_color');
    $woo_sp_forms_text_color = get_option('woo_sp_forms_text_color');
    $woo_sp_forms_bg_color = get_option('woo_sp_forms_bg_color');

  ?>

  <style type="text/css">
    .woo_sp_popup_style{
      background: <?php echo $woo_sp_forms_bg_color; ?>!important;
      border-color: <?php echo $woo_sp_forms_main_color; ?>!important;
      font-family: 'Open Sans',sans-serif;
    }

    .woo_sp_close_window{
      color: <?php echo $woo_sp_forms_main_color; ?>!important;
    }  

    .woo_sp_popup_style .login-form h2, .registration-form h2{ 
      color: <?php echo $woo_sp_forms_main_color; ?>!important;
    }

    .woo_sp_popup_style .woocommerce form .form-row label{
      color: <?php echo $woo_sp_forms_text_color; ?>!important;
    }

    .woo_sp_popup_style input[type="text"]:focus,input[type="password"]:focus,input[type="password"]:focus { 
      border: 1px solid <?php echo $woo_sp_forms_main_color; ?>!important;
      color: #333333!important;
    }

    .woo_sp_popup_style input[type="submit"]{ 
      cursor: pointer!important;
      background: <?php echo $woo_sp_forms_main_color; ?>!important;
      color: #ffffff!important;
      font-family: 'Open Sans',sans-serif;
    }

    .woo_sp_popup_style input[type="submit"]:hover{ 
      color: #ffffff!important;
    }

    .woo_sp_popup_style a{
      color: <?php echo $woo_sp_forms_main_color; ?>!important;
    }  
  </style>

  <div class="woo_sp_overlay" id="woo_sp_overlay"></div>

	<div class="woo_sp_popup_form woo_sp_popup_login woo_sp_popup_style">
		<?php echo do_shortcode('[wf_login_form]');?>
	</div>

	<div class="woo_sp_popup_form woo_sp_popup_sign_up woo_sp_popup_style">
		<?php echo do_shortcode('[wf_registration_form]');?>
	</div>

<?php }

//notices
add_action('wp_head', 'woo_sp_notices');
function woo_sp_notices(){
  echo'<div class="woo_sp_msg">';
  echo wc_print_notices();
  echo'</div>';
}
?>