<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://aum.im
 * @since      1.0.0
 *
 * @package    Designil_Woo_Thai_Address
 * @subpackage Designil_Woo_Thai_Address/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Designil_Woo_Thai_Address
 * @subpackage Designil_Woo_Thai_Address/includes
 * @author     Watcharapon Charoenwongjongdee <aum_kub@hotmail.com>
 */
class Designil_Woo_Thai_Address_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'designil-woo-thai-address',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
