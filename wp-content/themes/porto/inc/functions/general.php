<?php



if (!function_exists('array_insert_before')) :

    function array_insert_before($key, array &$array, $new_key, $new_value) {

        if (array_key_exists($key, $array)) {

            $new = array();

            foreach ($array as $k => $value) {

                if ($k === $key) {

                    $new[$new_key] = $new_value;

                }

                $new[$k] = $value;

            }

            return $new;

        }

        return false;

    }

endif;



if (!function_exists('array_insert_after')) :

    function array_insert_after($key, &$array, $new_key, $new_value) {

        if (array_key_exists($key, $array)) {

            $new = array();

            foreach ($array as $k => $value) {

                $new[$k] = $value;

                if ($k === $key) {

                    $new[$new_key] = $new_value;

                }

            }

            return $new;

        }

        return false;

    }

endif;



if (!function_exists('porto_add_url_parameters')):

    function porto_add_url_parameters($url, $name, $value) {

        $url_data = parse_url(str_replace('#038;', '&', $url));

        if (!isset($url_data["query"]))

            $url_data["query"]="";



        $params = array();

        parse_str($url_data['query'], $params);

        $params[$name] = $value;

        $url_data['query'] = http_build_query($params);

        return porto_build_url($url_data);

    }

endif;



if (!function_exists('porto_remove_url_parameters')):

    function porto_remove_url_parameters($url, $name) {

        $url_data = parse_url(str_replace('#038;', '&', $url));

        if (!isset($url_data["query"]))

            $url_data["query"]="";



        $params = array();

        parse_str($url_data['query'], $params);

        $params[$name] = "";

        $url_data['query'] = http_build_query($params);

        return porto_build_url($url_data);

    }

endif;



if (!function_exists('porto_build_url')):

    function porto_build_url($url_data) {

        $url="";

        if (isset($url_data['host'])) {

            $url .= $url_data['scheme'] . '://';

            if (isset($url_data['user'])) {

                $url .= $url_data['user'];

                if (isset($url_data['pass']))

                    $url .= ':' . $url_data['pass'];

                $url .= '@';

            }

            $url .= $url_data['host'];

            if (isset($url_data['port']))

                $url .= ':' . $url_data['port'];

        }

        if (isset($url_data['path']))

            $url .= $url_data['path'];

        if (isset($url_data['query']))

            $url .= '?' . $url_data['query'];

        if (isset($url_data['fragment']))

            $url .= '#' . $url_data['fragment'];



        return str_replace('#038;', '&', $url);

    }

endif;



function porto_get_blank_image() {

    return 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

}



if (!function_exists('array2json')):

    function array2json($arr) {

        if(function_exists('json_encode')) return json_encode($arr); //Lastest versions of PHP already has this functionality.

        $parts = array();

        $is_list = false;



        //Find out if the given array is a numerical array

        $keys = array_keys($arr);

        $max_length = count($arr)-1;

        if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1

            $is_list = true;

            for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position

                if($i != $keys[$i]) { //A key fails at position check.

                    $is_list = false; //It is an associative array.

                    break;

                }

            }

        }



        foreach($arr as $key=>$value) {

            if(is_array($value)) { //Custom handling for arrays

                if($is_list) $parts[] = array2json($value); /* :RECURSION: */

                else $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */

            } else {

                $str = '';

                if(!$is_list) $str = '"' . $key . '":';



                //Custom handling for multiple data types

                if(is_numeric($value)) $str .= $value; //Numbers

                elseif($value === false) $str .= 'false'; //The booleans

                elseif($value === true) $str .= 'true';

                else $str .= '"' . addslashes($value) . '"'; //All other things



                $parts[] = $str;

            }

        }

        $json = implode(',',$parts);



        if($is_list) return '[' . $json . ']';//Return numerical JSON

        return '{' . $json . '}';//Return associative JSON

    }

endif;



function porto_generate_rand() {

    $validCharacters = 'abcdefghijklmnopqrstuvwxyz0123456789';

    $rand = '';

    $length = 32;

    for ($n = 1; $n < $length; $n++) {

        $whichCharacter = rand(0, strlen($validCharacters)-1);

        $rand .= $validCharacters{$whichCharacter};

    }



    return $rand;

}



if ( ! function_exists( 'porto_is_ajax' ) ) {

    function porto_is_ajax() {



        if ( defined( 'DOING_AJAX' ) ) {

            return true;

        }



        if (function_exists('mb_strtolower')) {

            return ( isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && mb_strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest' ) ? true : false;

        } else {

            return ( isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest' ) ? true : false;

        }

    }

}



function porto_stringify_attributes( $attributes ) {

    $atts = array();

    foreach ( $attributes as $name => $value ) {

        $atts[] = $name . '="' . esc_attr( $value ) . '"';

    }



    return implode( ' ', $atts );

}



function porto_has_class( $class, $classes ) {

    return in_array( $class, explode( ' ', strtolower( $classes ) ) );

}



function porto_strip_tags( $content ) {

    $content = str_replace( ']]>', ']]&gt;', $content );

    $content = preg_replace("/<script.*?\/script>/s", "", $content) ? : $content;

    $content = preg_replace("/<style.*?\/style>/s", "", $content) ? : $content;

    $content = strip_tags( $content );

    return $content;

}


/**
 * Modifies WordPress's built-in comments_popup_link() function to return a string instead of echo comment results
 */
function get_comments_popup_link( $zero = false, $one = false, $more = false, $css_class = '', $none = false ) {
    global $wpcommentspopupfile, $wpcommentsjavascript;
 
    $id = get_the_ID();
 
    if ( false === $zero ) $zero = __( 'No Comments' );
    if ( false === $one ) $one = __( '1 Comment' );
    if ( false === $more ) $more = __( '% Comments' );
    if ( false === $none ) $none = __( 'Comments Off' );
 
    $number = get_comments_number( $id );
 
    $str = '';
 
    if ( 0 == $number && !comments_open() && !pings_open() ) {
        $str = '<span' . ((!empty($css_class)) ? ' class="' . esc_attr( $css_class ) . '"' : '') . '>' . $none . '</span>';
        return $str;
    }
 
    if ( post_password_required() ) {
        $str = __('Enter your password to view comments.');
        return $str;
    }
 
    $str = '<a href="';
    if ( $wpcommentsjavascript ) {
        if ( empty( $wpcommentspopupfile ) )
            $home = home_url();
        else
            $home = get_option('siteurl');
        $str .= $home . '/' . $wpcommentspopupfile . '?comments_popup=' . $id;
        $str .= '" onclick="wpopen(this.href); return false"';
    } else { // if comments_popup_script() is not in the template, display simple comment link
        if ( 0 == $number )
            $str .= get_permalink() . '#respond';
        else
            $str .= get_comments_link();
        $str .= '"';
    }
 
    if ( !empty( $css_class ) ) {
        $str .= ' class="'.$css_class.'" ';
    }
    $title = the_title_attribute( array('echo' => 0 ) );
 
    $str .= apply_filters( 'comments_popup_link_attributes', '' );
 
    $str .= ' title="' . esc_attr( sprintf( __('Comment on %s'), $title ) ) . '">';
    $str .= get_comments_number_str( $zero, $one, $more );
    $str .= '</a>';
     
    return $str;
}
 
/**
 * Modifies WordPress's built-in comments_number() function to return string instead of echo
 */
function get_comments_number_str( $zero = false, $one = false, $more = false, $deprecated = '' ) {
    if ( !empty( $deprecated ) )
        _deprecated_argument( __FUNCTION__, '1.3' );
 
    $number = get_comments_number();
 
    if ( $number > 1 )
        $output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments') : $more);
    elseif ( $number == 0 )
        $output = ( false === $zero ) ? __('No Comments') : $zero;
    else // must be one
        $output = ( false === $one ) ? __('1 Comment') : $one;
 
    return apply_filters('comments_number', $output, $number);
}


// Woocommerce Vendor Start
if(class_exists('WC_Vendors') ){
function porto_wc_vendor_header(){
		
global $porto_settings, $post, $wp_query,$vendor_shop;
			$vendor_id     = WCV_Vendors::get_vendor_id( $vendor_shop ); 
			$shop_name  =  get_user_meta( $vendor_id  , 'pv_shop_name', true );
			$description  = do_shortcode( get_user_meta( $vendor_id, 'pv_shop_description', true ) );
			if($vendor_shop){
				if($porto_settings['porto_wcvendors_shop_description']){
				?>
				 <?php 
					 $product_id = get_the_ID();
					 $author = WCV_Vendors::get_vendor_from_product( $product_id );
					 $link=WCV_Vendors::get_vendor_shop_page( $author);
					 $author = WCV_Vendors::get_vendor_from_product(get_the_ID()); 
					 $user = get_userdata( $author );
					 ?>
					 
						<?php 
					

						$r = get_user_meta( $user->ID, 'picture', true );
						if($r){		
						$r = $r['url'];
									
						?>
						<div class="vendor-profile-bg" style="background:url('<?php echo $r?>') ;background-size:cover">
						<?php }else{ ?>
						<div class="vendor-profile-bg">
						<?php }	?>
						<div class="overlay-vendor-effect">
					<?php if($porto_settings['porto_wcvendors_shop_avatar']){ ;?>
					<div class="vendor_userimg">
				
					<div class="profile-img">
					  	<a href="<?php echo $link?>"> <?php echo get_avatar( $vendor_id, 80 ); ?></a>
					</div>
					
					</div>
					<?php } ?>
					  <h1><a href="<?php echo $link?>"><?php echo $shop_name; ?></a></h1>
					  <div class="custom_shop_description">
					  <?php echo wpautop( $description ); ?>
					  </div>
				<?php 	  
				 $author = WCV_Vendors::get_vendor_from_product(get_the_ID()); 
				 $user = get_userdata( $author );
					
				 if($porto_settings['porto_wcvendors_shop_profile']){
				  ?>
				  
					 <?php if($porto_settings['porto_wcvendors_phone']){
					   if($user->phone_number) { ?>
				      <span class="vendorcustom-mail"><i class="fa fa-phone aligmentvendor"></i> &nbsp;<?php echo  $user->phone_number; ?></span>
				      <?php } } ?>  &nbsp;&nbsp;
				    <?php if($porto_settings['porto_wcvendors_email']){
					   if($user->user_email) { ?>
				    <span class="vendorcustom-mail"><i class="fa fa-envelope aligmentvendor"></i> &nbsp;<?php echo  $user->user_email; ?></span>
				   <?php } } ?>
				   &nbsp;&nbsp;
					 <?php 
					 if($porto_settings['porto_wcvendors_url']){
					 if($user->user_url) { ?>
				    <span class="vendorcustom-mail"><i class="fa fa-globe aligmentvendor"></i> &nbsp; <?php echo $user->user_url ;?></span>
				    <?php } } 
				    ?>

					<p class="vendor-user-social">
						<?php if( $user->facebook_url ) : ?>
							<span class="user-facebook"><a rel="nofollow" href="<?php echo esc_url( $user->facebook_url ); ?>"><i class="fa fa-facebook-square"></i></a></span>
						<?php endif; ?>

						<?php if( $user->twitter_url ) : ?>
							<span class="user-twitter"><a rel="nofollow" href="<?php echo esc_url( $user->twitter_url ); ?>"><i class="fa fa-twitter-square"></i></a></span>
						<?php endif; ?>

						<?php if( $user->gplus_url ) : ?>
							<span class="user-googleplus"><a rel="nofollow" href="<?php echo esc_url( $user->gplus_url ); ?>"><i class="fa fa-google-plus-square"></i></a></span>
						<?php endif; ?>
						
						 <?php if( $user->youtube_url ) : ?>
							<span class="user-youtube"><a rel="nofollow" href="<?php echo esc_url( $user->youtube_url ); ?>"><i class="fa fa-youtube-square"></i></a></span>
						<?php endif; ?>
						
							
						 <?php if( $user->linkedin_url ) : ?>
							<span class="user-linkedin"><a rel="nofollow" href="<?php echo esc_url( $user->linkedin_url ); ?>"><i class="fa fa-linkedin-square"></i></a></span>
						<?php endif; ?>
						
						 <?php if( $user->flickr_url ) : ?>
							<span class="user-flicker"><a rel="nofollow" href="<?php echo esc_url( $user->flickr_url ); ?>"><i class="fa fa-flickr-square"></i></a></span>
						<?php endif; ?>
						 
					</p>

				 <?php }?>
			 
				 </div>

				 </div>
				<?php }
			
			
			}
			if(is_product()){

		   $shop_name  =  get_user_meta( $post->post_author  , 'pv_shop_name', true );
			$Shop_description  =  get_user_meta( $post->post_author  , 'pv_shop_description', true );

		  ?>

			 <?php if($porto_settings['porto_single_wcvendors_product_description']){ ?>
			   <?php 
				  $product_id = get_the_ID();
				 $author = WCV_Vendors::get_vendor_from_product( $product_id );
				 $link=WCV_Vendors::get_vendor_shop_page( $author);
				 $author = WCV_Vendors::get_vendor_from_product(get_the_ID()); 
					 $user = get_userdata( $author );
					 ?>
					 
						<?php 
						$r = get_user_meta( $user->ID, 'picture', true );
						if($r){
						$r = $r['url'];
											
						?>
						<div class="vendor-profile-bg" style="background:url('<?php echo $r?>') ;background-size:cover">
						<?php }else{ ?>
						<div class="vendor-profile-bg">
						<?php }	?>
						<div class="overlay-vendor-effect">
				 <?php if($porto_settings['porto_wcvendors_product_avatar']){ ?>
					<div class="vendor_userimg">
						
							<div class="profile-img">
						  <a href="<?php echo $link?>"> <?php echo get_avatar( $author, 80 ); ?>	</a>
							</div>
					
					</div>
				 <?php } ?>
				
			     <h1>
				  <a href="<?php echo $link?>"><?php echo $shop_name; ?></a>
				 </h1>
				  <div class="custom_shop_description">
					<?php echo wpautop( $Shop_description ); ?>
				  </div>
				  </div>
				  
				 
				<?php 	  
				 $author = WCV_Vendors::get_vendor_from_product(get_the_ID()); 
				 $user = get_userdata( $author );

			 
				if($porto_settings['porto_wcvendors_product_profile']){
				?>
			  
				 <?php if($porto_settings['porto_wcvendors_phone']){
				   if($user->phone_number) { ?>
					<span class="vendorcustom-mail"><i class="fa fa-phone aligmentvendor"></i> &nbsp;<?php echo  $user->phone_number; ?></span>
				<?php } } ?>  
			    <?php if($porto_settings['porto_wcvendors_email']){
				   if($user->user_email) { ?>
			    <span class="vendorcustom-mail"><i class="fa fa-envelope aligmentvendor"></i> &nbsp;<?php echo  $user->user_email; ?></span>
			    <?php } } ?>
			  
				 <?php 
				 if($porto_settings['porto_wcvendors_url']){
				 if($user->user_url) { ?>
			     <span class="vendorcustom-mail"><i class="fa fa-globe  aligmentvendor"></i> &nbsp; <?php echo $user->user_url ;?></span>
			    <?php } } ?>


	      	<p class="vendor-user-social">
				<?php if( $user->facebook_url ) : ?>
					<span class="user-facebook"><a rel="nofollow" href="<?php echo esc_url( $user->facebook_url ); ?>"><i class="fa fa-facebook-square"></i></a></span>
				<?php endif; ?>

				<?php if( $user->twitter_url ) : ?>
					<span class="user-twitter"><a rel="nofollow" href="<?php echo esc_url( $user->twitter_url ); ?>"><i class="fa fa-twitter-square"></i></a></span>
				<?php endif; ?>

				<?php if( $user->gplus_url ) : ?>
					<span class="user-googleplus"><a rel="nofollow" href="<?php echo esc_url( $user->gplus_url ); ?>"><i class="fa fa-google-plus-square"></i></a></span>
				<?php endif; ?>
				
				 <?php if( $user->youtube_url ) : ?>
					<span class="user-youtube"><a rel="nofollow" href="<?php echo esc_url( $user->youtube_url ); ?>"><i class="fa fa-youtube-square"></i></a></span>
				<?php endif; ?>
				
					
				 <?php if( $user->linkedin_url ) : ?>
					<span class="user-linkedin"><a rel="nofollow" href="<?php echo esc_url( $user->linkedin_url ); ?>"><i class="fa fa-linkedin-square"></i></a></span>
				<?php endif; ?>
				
				 <?php if( $user->flickr_url ) : ?>
					<span class="user-flicker"><a rel="nofollow" href="<?php echo esc_url( $user->flickr_url ); ?>"><i class="fa fa-flickr-square"></i></a></span>
				<?php endif; ?>
				 
				</p>	 
			 
				<?php  }?>
			 
				 </div>
				 <?php } ?>
					
				<?php 
			
			}
	} 
	
	
}


// Woocommerce Vendor End

// Enable font size in the editor
if ( ! function_exists( 'porto_mce_buttons' ) ) {
	function porto_mce_buttons( $buttons ) {
		array_unshift( $buttons, 'fontsizeselect' ); // Add Font Size Select
		return $buttons;
	}
}
add_filter( 'mce_buttons_2', 'porto_mce_buttons' );

// Customize mce editor font sizes
if ( ! function_exists( 'porto_mce_text_sizes' ) ) {
	function porto_mce_text_sizes( $initArray ){
		$initArray['fontsize_formats'] = "9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 22px 23px 24px 25px 26px 27px 28px 29px 30px 31px 32px 33px 34px 35px 36px 37px 38px 39px 40px 41px 42px 43px 44px 45px 46px 47px 48px 49px 50px 51px 52px 53px 54px 55px 56px 57px 58px 59px 60px 61px 62px 63px 64px 65px 66px 67px 68px 69px 70px 71px 72px";
		return $initArray;
	}
}
add_filter( 'tiny_mce_before_init', 'porto_mce_text_sizes' );
