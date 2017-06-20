<?php



class Porto_Admin {



    public function __construct() {
		
        add_action( 'admin_init', array( $this, 'admin_init' ) );

        add_action( 'wp_before_admin_bar_render', array( $this, 'add_wp_toolbar_menu' ) );

        add_action( 'admin_menu', array( $this, 'admin_menu' ) );

        add_action( 'after_switch_theme', array( $this, 'activation_redirect' ) );
		

    }
	
	



    public function add_wp_toolbar_menu() {



        if ( current_user_can( 'edit_theme_options' ) ) {



            $porto_parent_menu_title = '<span class="ab-icon"></span><span class="ab-label">Porto</span>';



            $this->add_wp_toolbar_menu_item( $porto_parent_menu_title, false, admin_url( 'admin.php?page=porto' ), array( 'class' => 'porto-menu' ), 'porto' );
			
			$this->add_wp_toolbar_menu_item( __( 'Registration', 'Porto' ), 'porto', admin_url( 'admin.php?page=porto-registration' ) );

            $this->add_wp_toolbar_menu_item( __( 'System Status', 'Porto' ), 'porto', admin_url( 'admin.php?page=porto-system' ) );

            $this->add_wp_toolbar_menu_item( __( 'Plugins', 'Porto' ), 'porto', admin_url( 'admin.php?page=porto-plugins' ) );

            $this->add_wp_toolbar_menu_item( __( 'Install Demos', 'Porto' ), 'porto', admin_url( 'admin.php?page=porto-demos' ) );

            $this->add_wp_toolbar_menu_item( __( 'Theme Options', 'Porto' ), 'porto', admin_url( 'themes.php?page=porto_settings' ) );

        }

    }



    public function add_wp_toolbar_menu_item( $title, $parent = false, $href = '', $custom_meta = array(), $custom_id = '' ) {



        global $wp_admin_bar;



        if ( current_user_can( 'edit_theme_options' ) ) {

            if ( ! is_super_admin() || ! is_admin_bar_showing() ) {

                return;

            }



            // Set custom ID

            if ( $custom_id ) {

                $id = $custom_id;

            } else { // Generate ID based on $title

                $id = strtolower( str_replace( ' ', '-', $title ) );

            }



            // links from the current host will open in the current window

            $meta = strpos( $href, site_url() ) !== false ? array() : array( 'target' => '_blank' ); // external links open in new tab/window

            $meta = array_merge( $meta, $custom_meta );



            $wp_admin_bar->add_node( array(

                'parent' => $parent,

                'id'     => $id,

                'title'  => $title,

                'href'   => $href,

                'meta'   => $meta,

            ) );

        }



    }



    public function activation_redirect() {

        if ( current_user_can( 'edit_theme_options' ) ) {

            header( 'Location:' . admin_url() . 'admin.php?page=porto' );

        }

    }



    public function admin_init() {



        if ( current_user_can( 'edit_theme_options' ) ) {

            if ( isset( $_GET['porto-deactivate'] ) && 'deactivate-plugin' == $_GET['porto-deactivate'] ) {

                check_admin_referer( 'porto-deactivate', 'porto-deactivate-nonce' );



                $plugins = TGM_Plugin_Activation::$instance->plugins;



                foreach ( $plugins as $plugin ) {

                    if ( $plugin['slug'] == $_GET['plugin'] ) {

                        deactivate_plugins( $plugin['file_path'] );

                    }

                }

            } if ( isset( $_GET['porto-activate'] ) && 'activate-plugin' == $_GET['porto-activate'] ) {

                check_admin_referer( 'porto-activate', 'porto-activate-nonce' );



                $plugins = TGM_Plugin_Activation::$instance->plugins;



                foreach ( $plugins as $plugin ) {

                    if ( isset( $_GET['plugin'] ) && $plugin['slug'] == $_GET['plugin'] ) {

                        activate_plugin( $plugin['file_path'] );



                        wp_redirect( admin_url( 'admin.php?page=porto-plugins' ) );

                        exit;

                    }

                }

            }

        }

    }



    public function admin_menu(){



        if ( current_user_can( 'edit_theme_options' ) ) {

            $welcome_screen = add_menu_page( 'Porto', 'Porto', 'administrator', 'porto', array( $this, 'welcome_screen' ), 'dashicons-porto-logo', 59 );

			$welcome       = add_submenu_page( 'porto', __( 'Welcome', 'Porto' ), __( 'Welcome', 'Porto' ), 'administrator', 'porto', array( $this, 'welcome_screen' ) );

			$porto_registration = add_submenu_page( 'porto', __( 'Registration', 'porto' ), __( 'Registration', 'porto' ), 'administrator', 'porto-registration', array( $this, 'porto_registration_tab' ) );

            $system_status = add_submenu_page( 'porto', __( 'System Status', 'Porto' ), __( 'System Status', 'Porto' ), 'administrator', 'porto-system', array( $this, 'system_tab' ) );

            $plugins       = add_submenu_page( 'porto', __( 'Plugins', 'Porto' ), __( 'Plugins', 'Porto' ), 'administrator', 'porto-plugins', array( $this, 'plugins_tab' ) );

            $demos         = add_submenu_page( 'porto', __( 'Install Demos', 'Porto' ), __( 'Install Demos', 'Porto' ), 'administrator', 'porto-demos', array( $this, 'demos_tab' ) );

            $theme_options = add_submenu_page( 'porto', __( 'Theme Options', 'Porto' ), __( 'Theme Options', 'Porto' ), 'administrator', 'themes.php?page=porto_settings' );

        }

    }



    public function welcome_screen() {

        require_once( porto_admin . '/admin_pages/welcome.php' );

    }

	public function porto_registration_tab() {


        require_once( porto_admin . '/admin_pages/porto-registration.php' );


    }

    public function system_tab() {

        require_once( porto_admin . '/admin_pages/system-status.php' );

    }



    public function demos_tab() {

        require_once( porto_admin . '/admin_pages/install-demos.php' );

    }



    public function plugins_tab() {

        require_once( porto_admin . '/admin_pages/porto-plugins.php' );

    }



    public function plugin_link( $item ) {

        $installed_plugins = get_plugins();



        $item['sanitized_plugin'] = $item['name'];



        $actions = array();



        // We have a repo plugin

        if ( ! $item['version'] ) {

            $item['version'] = TGM_Plugin_Activation::$instance->does_plugin_have_update( $item['slug'] );

        }
	
	
		$disable_class = '';
		if ( ( 'js_composer' == $item['slug'] || 'Ultimate_VC_Addons' == $item['slug'] || 'revslider' == $item['slug'] ) && ( $this->is_registered() != '1' || $this->is_unregistered() ) ) {
			$disable_class = ' disabled';
		}
		

        /** We need to display the 'Install' hover link */

        if ( ! isset( $installed_plugins[$item['file_path']] ) ) {
			
			if ( ! $disable_class ) {
				$url = esc_url( wp_nonce_url(
					add_query_arg(
						array(

                                'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),

                                'plugin'        => urlencode( $item['slug'] ),

                                'plugin_name'   => urlencode( $item['sanitized_plugin'] ),

                                'plugin_source' => urlencode( $item['source'] ),

                                'tgmpa-install' => 'install-plugin',

                                'return_url'    => 'porto-plugins',

                            ),

						TGM_Plugin_Activation::$instance->get_tgmpa_url()
					),
					'tgmpa-install',
					'tgmpa-nonce'
				) );
			} else {
				$url = '#';
			}
			
			$actions = array(
				'install' => '<a href="' . $url . '" class="button button-primary' . $disable_class . '" title="' . sprintf( esc_attr__( 'Install %s', 'Porto' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Install', 'Porto' ) . '</a>',
			);
			
        }

        /** We need to display the 'Activate' hover link */

        elseif ( is_plugin_inactive( $item['file_path'] ) ) {
			
			$disable_class = '';
				if ( ( 'js_composer' == $item['slug'] || 'Ultimate_VC_Addons' == $item['slug'] || 'revslider' == $item['slug'] ) && ( $this->is_registered() != '1' || $this->is_unregistered() ) ) {
				$disable_class = ' disabled';
			}
			
			if ( ! $disable_class ) {
				$url = esc_url( add_query_arg(
					array(

                            'plugin'               => urlencode( $item['slug'] ),

                            'plugin_name'          => urlencode( $item['sanitized_plugin'] ),

                            'plugin_source'        => urlencode( $item['source'] ),

                            'porto-activate'       => 'activate-plugin',

                            'porto-activate-nonce' => wp_create_nonce( 'porto-activate' ),

                        ),
					admin_url( 'admin.php?page=porto-plugins' )
				) );
			} else {
				$url = '#';
			}

			$actions = array(
				'activate' => '<a href="' . $url . '" class="button button-primary' . $disable_class . '" title="' . sprintf( esc_attr__( 'Activate %s', 'Porto' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Activate' , 'Porto' ) . '</a>',
			);
			
        }

        /** We need to display the 'Update' hover link */

        elseif ( version_compare( $installed_plugins[$item['file_path']]['Version'], $item['version'], '<' ) ) {
			
			$disable_class = '';
			if ( ( 'js_composer' == $item['slug'] || 'Ultimate_VC_Addons' == $item['slug'] || 'revslider' == $item['slug'] ) && ( $this->is_registered() != '1' || $this->is_unregistered() ) ) {
				$disable_class = ' disabled';
			}
			
			// We need to display the 'Update' hover link.
			$url = wp_nonce_url(
				add_query_arg(
					array(

						'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),

						'plugin'        => urlencode( $item['slug'] ),
						
						'tgmpa-update'  => 'update-plugin',

						'plugin_source' => urlencode( $item['source'] ),

						'version'       => urlencode( $item['version'] ),

						'return_url'    => 'porto-plugins',

					),
					TGM_Plugin_Activation::$instance->get_tgmpa_url()
				),
				'tgmpa-update',
				'tgmpa-nonce'
			);
			
			$actions = array(
				'update' => '<a href="' . $url . '" class="button button-primary' . $disable_class . '" title="' . sprintf( esc_attr__( 'Update %s', 'Porto' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Update', 'Porto' ) . '</a>',
			);


        } elseif ( is_plugin_active( $item['file_path'] ) ) {

            $actions = array(

                'deactivate' => sprintf(

                    '<a href="%1$s" class="button button-primary" title="Deactivate %2$s">Deactivate</a>',

                    esc_url( add_query_arg(

                        array(

                            'plugin'                 => urlencode( $item['slug'] ),

                            'plugin_name'            => urlencode( $item['sanitized_plugin'] ),

                            'plugin_source'          => urlencode( $item['source'] ),

                            'porto-deactivate'       => 'deactivate-plugin',

                            'porto-deactivate-nonce' => wp_create_nonce( 'porto-deactivate' ),

                        ),

                        admin_url( 'admin.php?page=porto-plugins' )

                    ) ),

                    $item['sanitized_plugin']

                ),

            );

        }



        return $actions;

    }



    public function let_to_num( $size ) {

        $l   = substr( $size, -1 );

        $ret = substr( $size, 0, -1 );

        switch ( strtoupper( $l ) ) {

            case 'P':

                $ret *= 1024;

            case 'T':

                $ret *= 1024;

            case 'G':

                $ret *= 1024;

            case 'M':

                $ret *= 1024;

            case 'K':

                $ret *= 1024;

        }

        return $ret;

    }
	
	public function verify_purchase($purchase_code, $username = 'SW-THEMES', $api_key = '34ege4a3agxosuxhi47g85zr19j1avvk', $item_id = '9207399') {
		
        // Open cURL channel
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "http://marketplace.envato.com/api/edge/$username/$api_key/verify-purchase:$purchase_code.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ENVATO-PURCHASE-VERIFY'); //api requires any user agent to be set

        // Decode returned JSON
        $result = json_decode( curl_exec($ch) , true );
				
        //check if purchase code is correct
        if ( !empty($result['verify-purchase']['item_id']) && $result['verify-purchase']['item_id'] ) {
			
            // check if purchased item is given item to check
            return $result['verify-purchase']['item_id'] == $item_id;
        }

        //invalid purchase code
        return false;

    }
	
	public function get_purchase_code(){
		return get_option('porto_product_registration_code');		
	}
	
	public function is_registered(){
		return get_option('porto_registered');
	}
	
	public function is_unregistered(){
		return get_option('porto_unregistered');
	}
	
	public function set_purchase_code($code){
		update_option('porto_product_registration_code',$code);
	}

}

new Porto_Admin();
function Porto(){
	return new Porto_Admin();
}

add_action('admin_init',function(){
	if( isset($_POST['porto_registration']) ){
		$code = isset($_POST['code']) ? esc_attr($_POST['code']) : Porto()->get_purchase_code();
		Porto()->set_purchase_code( $code );
		if( Porto()->verify_purchase($code) ){
			if( in_array( $_SERVER['REMOTE_ADDR'], array( '127.0.0.1', '::1' ) ) ){
				update_option('porto_registered','1');
			}else{
				$siteurl = preg_replace("(^https?://)", "", site_url() );
				$action = isset( $_POST['action'] ) ? $_POST['action'] : '';
				if( $action == 'unregister' ){
					deactivate_plugins( plugin_basename( 'js_composer/js_composer.php' ) );
					deactivate_plugins( plugin_basename( 'revslider/revslider.php' ) );
					deactivate_plugins( plugin_basename( 'Ultimate_VC_Addons/Ultimate_VC_Addons.php' ) );
				}
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://newsmartwave.net/wordpress/porto_verification/index.php?code=$code&url=$siteurl&action=$action");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				$result = curl_exec($ch);
				if( $result_json = json_decode($result) ){
					$result = $result_json->action;
					update_option( 'porto_code_used_site', $result_json->url);
				}
				switch( $result ){
					case 'successful':
						update_option('porto_registered','1');
						if( Porto()->is_unregistered() ){
							activate_plugins( plugin_basename( 'js_composer/js_composer.php' ) );
							activate_plugins( plugin_basename( 'revslider/revslider.php' ) );
							activate_plugins( plugin_basename( 'Ultimate_VC_Addons/Ultimate_VC_Addons.php' ) );
							update_option('porto_unregistered',false);
						}
						break;
					case 'used':
						update_option('porto_registered','2');
						break;
					case 'unregister':
						update_option('porto_registered','3');
						update_option('porto_unregistered',true);
						break;
				}
			}
		}else{
			update_option('porto_registered',false);
		}
	}
});

require_once(porto_admin . '/theme_options.php');

add_action("admin_init", "porto_compile_css_on_activation");
function porto_compile_css_on_activation(){
	if( ! get_option('porto_theme_style') ){
		porto_compile_css(true, 'theme');
	}
	if( ! get_option('porto_plugins_style') ){
		porto_compile_css(true, 'plugins');
	}
	if( ! get_option('porto_skin_style') ){
		porto_save_theme_settings();	
	}
	if( ! get_option('porto_theme_rtl_style') ){
		porto_compile_css(true, 'theme_rtl');
	}
	if( ! get_option('porto_plugins_rtl_style') ){
		porto_compile_css(true, 'plugins_rtl');
	}
}

add_action('admin_init', function(){
	if( Porto()->is_unregistered() ){
		add_filter( 'plugin_action_links', 'disable_porto_plugin_links', 10, 4 );
		function disable_porto_plugin_links( $actions, $plugin_file, $plugin_data, $context ) {
			$plugins = array( 'revslider/revslider.php', 'js_composer/js_composer.php', 'Ultimate_VC_Addons/Ultimate_VC_Addons.php' );
			if ( in_array( $plugin_file, $plugins)){
				unset( $actions['activate'] );
				unset( $actions['deactivate'] );
				unset( $actions['edit'] );
			}
			return $actions;
		}
		add_action( 'admin_notices', function(){ ?>
			<div class="notice notice-error">
				<h3 style="color: #dc3232;font-weight:normal"><?php _e( 'The Following Pre-bundled Premium Plugins are Deactivated <strong>"WPBakery Visual Composer, Ultimate Addons for Visual Composer, Slider Revolution"</strong> Please register your theme to activate them.', 'porto' ); ?></h3>
			</div>
		<?php });
	}
});