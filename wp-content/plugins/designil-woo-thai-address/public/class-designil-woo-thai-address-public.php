<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://aum.im
 * @since      1.0.0
 *
 * @package    Designil_Woo_Thai_Address
 * @subpackage Designil_Woo_Thai_Address/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Designil_Woo_Thai_Address
 * @subpackage Designil_Woo_Thai_Address/public
 * @author     Watcharapon Charoenwongjongdee <aum_kub@hotmail.com>
 */
class Designil_Woo_Thai_Address_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->original = get_option('wta_original_field');
  	$status  = get_option( 'wta_license_key_status' );

  	if ( $status == 'valid' ) {
  		$this->init_hook();
  	}
  	
		$this->orderID = '';

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Thai_Address_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Thai_Address_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( is_checkout() || is_wc_endpoint_url( 'edit-address' ) ) {

			wp_enqueue_style( $this->plugin_name . '-jquery-thailand', plugin_dir_url( __FILE__ ) . 'css/jquery.Thailand.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/designil-woo-thai-address-public.css', array(), $this->version, 'all' );

		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Thai_Address_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Thai_Address_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( is_checkout() || is_wc_endpoint_url( 'edit-address' ) ) {

			wp_enqueue_script( $this->plugin_name . '-jquery-thailand', plugin_dir_url( __FILE__ ) . 'js/jquery.Thailand.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-typeahead', plugin_dir_url( __FILE__ ) . 'js/typeahead.bundle.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-JQL', plugin_dir_url( __FILE__ ) . 'js/JQL.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/designil-woo-thai-address-public.js', array( 'jquery' ), $this->version, false );

			wp_localize_script( 
			$this->plugin_name, 
			'wta', 
			array(
				'url' => plugin_dir_url( __FILE__ ),
				)
			);			

		}


	}

	public function woo_thai_init() {
		global $order_id;
	}

	public function init_hook() {
		// Hook in		
		add_action( 'init' , array( $this, 'woo_thai_init' ), 999);
		add_filter( 'woocommerce_default_address_fields' , array( $this, 'woo_thai_override_default_address_fields' ), 999);
		add_filter( 'woocommerce_checkout_fields' , array( $this, 'woo_thai_override_checkout_fields' ), 999);
		add_filter( 'woocommerce_billing_fields' , array( $this, 'woo_thai_override_billing_fields' ), 999);
		add_filter( 'woocommerce_shipping_fields' , array( $this, 'woo_thai_override_shipping_fields' ), 999);
		add_filter( 'gettext' , array( $this, 'woo_thai_change_text' ), 999, 3 );

		if ( empty($this->original) ) :
			// FrontEnd - My Address 
			add_filter( 'woocommerce_my_account_my_address_formatted_address', function( $args, $customer, $name='' ){
				$billing_sub_city = get_user_meta($customer, 'billing_sub_city', true);
				$shipping_sub_city = get_user_meta($customer, 'shipping_sub_city', true);
				$args['sub_city'] = '{{'.$billing_sub_city.'}}{{'.$shipping_sub_city.'}}';
				return $args;
			}, 999, 2 );

			// FrontEnd | BackEnd - Override Address Format
			add_filter( 'woocommerce_localisation_address_formats', function( $formats ){
				foreach ( $formats as $key => &$format ) {
					$formats['default'] = "{name}\n{company}\n{address_1}\n{sub_city} {city}\n{state} {postcode}\n{country}";
				}
				return $formats;
			}, 999 );

			// FrontEnd | BackEnd - Replace Value
			add_filter( 'woocommerce_formatted_address_replacements', function( $replacements, $args ){
				$replacements['{sub_city}'] = $args['sub_city'];
				return $replacements;
			}, 999, 2 );

			// FrontEnd | BackEnd - Pull Order ID for EMAIL
			add_filter( 'woocommerce_structured_data_order', function( $args ){
				$this->orderID = $args['orderNumber'];
				return $args;
			}, 999 );

			// FrontEnd | BackEnd - Get value for billing
			add_filter( 'woocommerce_order_formatted_billing_address', function( $args ){
				
				global $wp_query, $order, $post;

				if ( !empty($wp_query->query_vars['order-received']) ) 
					$id = $wp_query->query_vars['order-received'];

				if ( !empty($wp_query->query_vars['view-order']) ) 
					$id = $wp_query->query_vars['view-order'];

				if ( empty($id) && !empty($_GET['post']) )
					$id = $_GET['post'];

				if ( empty($id)) 
					$id = $this->orderID;

				if ( empty($id) && !empty($post->ID) )
					$id = $post->ID;

				if ( empty($id) && !empty($_GET['order_ids']) ) 
					$id = $_GET['order_ids'];
				
				if ( !empty($id)) 
					$sub_city = get_post_meta($id, '_billing_sub_city', true);

				if ( empty($sub_city) ) {
					$sub_city = '';
				}

				$args['sub_city'] = $sub_city;
				return $args;
			}, 999 );

			// FrontEnd | BackEnd - Get value for shipping
			add_filter( 'woocommerce_order_formatted_shipping_address', function( $args ){
				
				global $wp_query, $post;

				if ( !empty($wp_query->query_vars['order-received']) ) 
					$id = $wp_query->query_vars['order-received'];
				
				if ( !empty($wp_query->query_vars['view-order']) ) 
					$id = $wp_query->query_vars['view-order'];

				if ( empty($id) && !empty($_GET['post']) )
					$id = $_GET['post'];

				if ( empty($id)) 
					$id = $this->orderID;

				if ( empty($id) && !empty($post->ID) )
					$id = $post->ID;

				if ( empty($id) && !empty($_GET['order_ids']) ) 
					$id = $_GET['order_ids'];
				
				if ( !empty($id)) 
					$sub_city = get_post_meta($id, '_shipping_sub_city', true);

				if ( empty($sub_city) ) {
					$sub_city = '';
				}

				$args['sub_city'] = $sub_city;
				return $args;
			}, 999 );

			// BackEnd - Reformat Field 
			add_filter( 'woocommerce_admin_billing_fields', function ( $args ) {

				unset($args['address_2']);
				unset($args['company']);

				$args['address_1'] = array(
					'label' => __('Address', 'woocommerce'),
					'show' => false,
					);

				$args['city'] = array(
					'label' => __('District', 'designil-woo-thai-address'),
					'show' => false,
					);

				$this->array_insert(
					$args,
					"city",
						[
							"sub_city" => [
							'label' => __('Sub District', 'designil-woo-thai-address'),
							'show' => false,
							],
						]
					);			

				return $args;
			}, 999);

			// BackEnd - Reformat Field 
			add_filter( 'woocommerce_admin_shipping_fields', function ( $args ) {
				
				unset($args['address_2']);
				unset($args['company']);

				$args['address_1'] = array(
					'label' => __('Address', 'woocommerce'),
					'show' => false,
					);

				$args['city'] = array(
					'label' => __('District', 'designil-woo-thai-address'),
					'show' => false,
					);

				$this->array_insert(
					$args,
					"city",
						[
							"sub_city" => [
							'label' => __('Sub District', 'designil-woo-thai-address'),
							'show' => false,
							],
						]
					);

				return $args;
			}, 999);
		endif;

	}

	public function array_insert(&$array, $position, $insert)
	{
		if (is_int($position)) {
			array_splice($array, $position, 0, $insert);
		} else {
			$pos   = array_search($position, array_keys($array));
			$array = array_merge(
				array_slice($array, 0, $pos),
				$insert,
				array_slice($array, $pos)
				);
		}
	}

	// Our hooked in function - $fields is passed via the filter!
	public function woo_thai_override_default_address_fields( $fields ) {

		if ( empty($this->original) ) {
			// Priority
	    $fields['first_name']['priority'] = 0;
	    $fields['last_name']['priority'] = 10;
	    $fields['address_1']['priority'] = 30;
	    $fields['city']['priority'] = 60;
	    $fields['state']['priority'] = 70;
	    $fields['postcode']['priority'] = 80;
	    $fields['country']['priority'] = 100;

	    // Type
	    $fields['address_1']['type'] = 'textarea';

	    // Remove
	    unset($fields['company']);
	    unset($fields['address_2']);

	    // Order Sub City
	    $sub_city_order = 50;
	  } else {
	  	// Order Sub City
	  	$sub_city_order = 60;
	    $fields['address_2']['placeholder'] = __('Apartment, suite, unit etc. (optional)', 'designil-woo-thai-address');
	  }
	  // End Original


    // Translate 
    $fields['city']['label'] = __('District', 'designil-woo-thai-address');  
    $fields['address_1']['placeholder'] = __('Street Address', 'designil-woo-thai-address'); 

    // Add field
    $fields['sub_city'] = array (       
      'label' => __('Sub District', 'designil-woo-thai-address'),
      'type' => 'text',
      'required' => 1,
      'class' => array ('form-row-wide', 'address-field'),
      'autocomplete' => 'sub-city',
      'priority' => $sub_city_order,
      );

		return $fields;
	}

	public function woo_thai_override_checkout_fields( $fields ) {

		if ( empty($this->original) ) {
			// Reorder
			$fields['billing']['billing_email']['priority'] = 20;
			$fields['billing']['billing_phone']['priority'] = 110;

			// Class
			$fields['billing']['billing_email']['class'] = array('form-row-wide');
			$fields['billing']['billing_phone']['class'] = array('form-row-wide');
		} else {
    	$fields['billing']['billing_company']['label'] = __('Company name', 'designil-woo-thai-address'); 
		}

		// Label 
		$fields['billing']['billing_email']['label'] = __('Email Address', 'designil-woo-thai-address'); 
		$fields['order']['order_comments']['label'] = __('Order notes', 'designil-woo-thai-address'); 		
		$fields['order']['order_comments']['placeholder'] = __(' ', 'designil-woo-thai-address'); 

    return $fields;
	}

	public function woo_thai_override_billing_fields( $fields ) {

		if ( empty($this->original) ) {
			// Reorder
			$fields['billing_email']['priority'] = 20;
			$fields['billing_phone']['priority'] = 110;

			// Class
			$fields['billing_email']['class'] = array('form-row-wide');
			$fields['billing_phone']['class'] = array('form-row-wide');
		}

		// Label 
		$fields['billing_email']['label'] = __('Email Address', 'designil-woo-thai-address'); 

    return $fields;
	}

	public function woo_thai_override_shipping_fields( $fields ) {

		if ( !empty($this->original) ) {
			// Label
    	$fields['shipping_company']['label'] = __('Company name', 'designil-woo-thai-address');  
		}

    return $fields;
	}


	public function woo_thai_change_text( $translated_text, $text, $domain ) {

		if ( $domain == 'woocommerce' ) {

			switch ( $translated_text ) {
				case 'Billing details' :
				$translated_text = __( 'Billing details', 'designil-woo-thai-address' );
				break;
				case 'Additional information' :
				$translated_text = __( 'Additional information', 'designil-woo-thai-address' );
				break;
			}

		}

		return $translated_text;
	}
}
// Class

