<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.designilcode.com
 * @since             1.0.0
 * @package           Designil_Woo_Thai_Address
 *
 * @wordpress-plugin
 * Plugin Name:       Designil - Woo Thai Address
 * Plugin URI:        https://www.designilcode.com
 * Description:       เพิ่มความสะดวกในการกรอกที่อยู่ ในประเทศไทย และแสดง field ที่ตรงกับ UX สำหรับคนไทยโดยเฉพาะ
 * Version:           1.1.0
 * Author:            Designil
 * Author URI:        https://www.designilcode.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       designil-woo-thai-address
 * Domain Path:       /languages
 */

define( 'WTA_VERSION', "1.1.0" );

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'WTA_STORE_URL', 'https://www.designilcode.com/' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'WTA_ITEM_NAME', 'Designil - Woo Thai Address' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of the settings page for the license input to be displayed
define( 'WTA_LICENSE_PAGE', 'designil-woo-thai-address' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-designil-woo-thai-address-activator.php
 */
function activate_designil_woo_thai_address() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-designil-woo-thai-address-activator.php';
	Designil_Woo_Thai_Address_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-designil-woo-thai-address-deactivator.php
 */
function deactivate_designil_woo_thai_address() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-designil-woo-thai-address-deactivator.php';
	Designil_Woo_Thai_Address_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_designil_woo_thai_address' );
register_deactivation_hook( __FILE__, 'deactivate_designil_woo_thai_address' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-designil-woo-thai-address.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_designil_woo_thai_address() {

	$plugin = new Designil_Woo_Thai_Address();
	$plugin->run();

}
run_designil_woo_thai_address();

function wta_add_plugin_action_links( $links ) {
  return array_merge(
    array(
      'settings' => '<a href="'. admin_url( 'options-general.php?page=' . WTA_LICENSE_PAGE ) .'">'.__('Settings', 'designil-woo-thai-address').'</a>'
    ),
    $links
  );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wta_add_plugin_action_links' );

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
  // load our custom updater
  include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

function wta_plugin_updater() {

  // retrieve our license key from the DB
  $license_key = trim( get_option( 'wta_license_key' ) );

  // setup the updater
  $edd_updater = new EDD_SL_Plugin_Updater( WTA_STORE_URL, __FILE__, array(
      'version'   => WTA_VERSION,                // current version number
      'license'   => $license_key,         // license key (used get_option above to retrieve from DB)
      'item_name' => WTA_ITEM_NAME, // name of this plugin
      'author'    => 'Watcharapon Charoenwongjongdee'   // author of this plugin
    )
  );

  // print_r($edd_updater);

}
add_action( 'admin_init', 'wta_plugin_updater', 0 );

/************************************
* the code below is just a standard
* options page. Substitute with
* your own.
*************************************/

function designil_wta_license_menu() {

  add_options_page( 
    'Designil - Woo Thai Address',
    'Designil - Woo Thai Address',
    'manage_woocommerce',
    WTA_LICENSE_PAGE,  
    'designil_wta_page'
    );
}
add_action('admin_menu', 'designil_wta_license_menu');

function designil_wta_page() {
  global $wp_filter;
  $license = get_option( 'wta_license_key' );
  $status  = get_option( 'wta_license_key_status' );
  $original_field  = get_option( 'wta_original_field' );
  $original_field = $original_field == "true" ? "checked" : "";
  ?>
  <div class="wrap">
    <h2><?php _e('Designil - Woo Thai Address'); ?></h2>
    <form method="post" action="options.php">

      <table class="form-table wta">
        <tbody>
          <tr valign="top">
            <th scope="row" valign="top">
              <?php _e('License Key'); ?>
            </th>
            <td>
              <input id="wta_license_key" name="wta_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" /><label class="description" for="wta_license_key"></label>
              <div class="wta_register_here">
                <a href="https://www.designilcode.com/myaccount/" target="_blank">Get License Key</a>
              </div>
              
            </td>
          </tr>
          <?php if( false !== $license ) { ?>
            <tr valign="top">
              <th scope="row" valign="top">
                <?php _e('Activate License'); ?>
              </th>
              <td>
                <?php if( $status !== false && $status == 'valid' ) { ?>
                  <span style="color: green; display: block; text-transform: capitalize; padding: 0 0 10px;" ><?php _e('active'); ?></span>
                  <?php wp_nonce_field( 'wta_nonce', 'wta_nonce' ); ?>
                  <input type="submit" class="button-secondary" name="wta_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
                <?php } else {
                  wp_nonce_field( 'wta_nonce', 'wta_nonce' ); ?>
                  <input type="submit" class="button-secondary" name="wta_license_activate" value="<?php _e('Activate License'); ?>"/>
                <?php } ?>


              </td>
            </tr>
          <?php } ?>

          <tr>
            <td colspan="2"><hr></td>
          </tr>

          <tr>
            <th scope="row" valign="top">
              <?php _e('Default WooCommerce Field', 'designil-woo-thai-address'); ?>
            </th>
            <td>
              <input id="wta_original_field" name="wta_original_field" type="checkbox" <?php echo $original_field; ?> class="regular-text" value="true" /><label class="description" for="wta_original_field"></label>

            </td>
          </tr>

          <tr>
            <td colspan="2"><hr></td>
          </tr>

          <tr>            
            <th scope="row" valign="top">
              <?php _e('Debug', 'designil-woo-thai-address'); ?>
            </th>
            <td>
              <a href="options-general.php?page=designil-woo-thai-address&debug=true" class="debug--link">Debug Here</a>
              <?php if ( isset($_GET["debug"]) ) : ?>
                <textarea class="wta--debug">
                  <?php
                    print_r($wp_filter);
                  ?>
                </textarea>
              <?php endif; ?>
            </td>
          </tr>

          <?php         
            settings_fields("wta-section");
            do_settings_sections("theme-option");     
          ?>

          <tr>
            <th></th>
            <td><?php submit_button(); ?></td>
          </tr>

        </tbody>
      </table>

    </form>
  <?php
}

function wta_register_option() {
  if ( $_GET["page"] = 'designil-woo-thai-address' ) {
    register_setting("wta-section", "wta_license_key", "wta_sanitize_license");
    register_setting("wta-section", "wta_original_field");
  }
}
add_action('admin_init', 'wta_register_option');

function wta_sanitize_license( $new ) {
  $old = get_option( 'wta_license_key' );
  if( $old && $old != $new ) {
    delete_option( 'wta_license_key_status' ); // new license has been entered, so must reactivate
  }
  return $new;
}

/************************************
* this illustrates how to activate
* a license key
*************************************/

function wta_activate_license() {

  // listen for our activate button to be clicked
  if( isset( $_POST['wta_license_activate'] ) ) {

    if ( isset( $_POST['wta_license_key'] ) ) {
      $wta_license_key = $_POST['wta_license_key'];
      update_option( 'wta_license_key', $wta_license_key );
    }

    // run a quick security check
    if( ! check_admin_referer( 'wta_nonce', 'wta_nonce' ) )
      return; // get out if we didn't click the Activate button

    // retrieve the license from the database
    $license = trim( get_option( 'wta_license_key' ) );


    // data to send in our API request
    $api_params = array(
      'edd_action' => 'activate_license',
      'license'    => $license,
      'item_name'  => urlencode( WTA_ITEM_NAME ), // the name of our product in EDD
      'url'        => home_url()
    );

    // Call the custom API.
    $response = wp_remote_post( WTA_STORE_URL, array( 'timeout' => 15, 'sslverify' => true, 'body' => $api_params ) );

    // make sure the response came back okay
    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

      if ( is_wp_error( $response ) ) {
        $message = $response->get_error_message();
      } else {
        $message = __( 'An error occurred, please try again.' );
      }

    } else {

      $license_data = json_decode( wp_remote_retrieve_body( $response ) );

      if ( false === $license_data->success ) {

        switch( $license_data->error ) {

          case 'expired' :

            $message = sprintf(
              __( 'Your license key expired on %s.' ),
              date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
            );
            break;

          case 'revoked' :

            $message = __( 'Your license key has been disabled.' );
            break;

          case 'missing' :

            $message = __( 'Invalid license.' );
            break;

          case 'invalid' :
          case 'site_inactive' :

            $message = __( 'Your license is not active for this URL.' );
            break;

          case 'item_name_mismatch' :

            $message = sprintf( __( 'This appears to be an invalid license key for %s.' ), WTA_ITEM_NAME );
            break;

          case 'no_activations_left':

            $message = __( 'Your license key has reached its activation limit.' );
            break;

          default :

            $message = __( 'An error occurred, please try again.' );
            break;
        }

      }

    }

    // Check if anything passed on a message constituting a failure
    if ( ! empty( $message ) ) {
      $base_url = admin_url( 'options-general.php?page=' . WTA_LICENSE_PAGE );
      $redirect = add_query_arg( array( 'wta_sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

      wp_redirect( $redirect );
      exit();
    }

    // $license_data->license will be either "valid" or "invalid"

    update_option( 'wta_license_key_status', $license_data->license );
    wp_redirect( admin_url( 'options-general.php?page=' . WTA_LICENSE_PAGE ) );
    exit();
  }
}
add_action('admin_init', 'wta_activate_license');


/***********************************************
* Illustrates how to deactivate a license key.
* This will decrease the site count
***********************************************/

function wta_deactivate_license() {

  // listen for our activate button to be clicked
  if( isset( $_POST['wta_license_deactivate'] ) ) {

    // run a quick security check
    if( ! check_admin_referer( 'wta_nonce', 'wta_nonce' ) )
      return; // get out if we didn't click the Activate button

    // retrieve the license from the database
    $license = trim( get_option( 'wta_license_key' ) );


    // data to send in our API request
    $api_params = array(
      'edd_action' => 'deactivate_license',
      'license'    => $license,
      'item_name'  => urlencode( WTA_ITEM_NAME ), // the name of our product in EDD
      'url'        => home_url()
    );

    // Call the custom API.
    $response = wp_remote_post( WTA_STORE_URL, array( 'timeout' => 15, 'sslverify' => true, 'body' => $api_params ) );

    // make sure the response came back okay
    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

      if ( is_wp_error( $response ) ) {
        $message = $response->get_error_message();
      } else {
        $message = __( 'An error occurred, please try again.' );
      }

      $base_url = admin_url( 'options-general.php?page=' . WTA_LICENSE_PAGE );
      $redirect = add_query_arg( array( 'wta_sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

      wp_redirect( $redirect );
      exit();
    }

    // decode the license data
    $license_data = json_decode( wp_remote_retrieve_body( $response ) );

    // $license_data->license will be either "deactivated" or "failed"
    if( $license_data->license == 'deactivated' ) {
      delete_option( 'wta_license_key_status' );
    }

    wp_redirect( admin_url( 'options-general.php?page=' . WTA_LICENSE_PAGE ) );
    exit();

  }
}
add_action('admin_init', 'wta_deactivate_license');


/************************************
* this illustrates how to check if
* a license key is still valid
* the updater does this for you,
* so this is only needed if you
* want to do something custom
*************************************/

function wta_check_license() {

  global $wp_version;

  $license = trim( get_option( 'wta_license_key' ) );

  $api_params = array(
    'edd_action' => 'check_license',
    'license' => $license,
    'item_name' => urlencode( WTA_ITEM_NAME ),
    'url'       => home_url()
  );

  // Call the custom API.
  $response = wp_remote_post( WTA_STORE_URL, array( 'timeout' => 15, 'sslverify' => true, 'body' => $api_params ) );

  if ( is_wp_error( $response ) )
    return false;

  $license_data = json_decode( wp_remote_retrieve_body( $response ) );

  if( $license_data->license == 'valid' ) {
    echo 'valid'; exit;
    // this license is still valid
  } else {
    echo 'invalid'; exit;
    // this license is no longer valid
  }
}

/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function wta_admin_notices() {
  if ( isset( $_GET['wta_sl_activation'] ) && ! empty( $_GET['message'] ) ) {

    switch( $_GET['wta_sl_activation'] ) {

      case 'false':
        $message = urldecode( $_GET['message'] );
        ?>
        <div class="error">
          <p><?php echo $message; ?></p>
        </div>
        <?php
        break;

      case 'true':
      default:
        // Developers can put a custom success message here for when activation is successful if they way.
        break;

    }
  }

  $status = get_option( 'wta_license_key_status' );
  if( $status != 'valid' ) {
    $url = WTA_LICENSE_PAGE;
    $url = "options-general.php?page=" . $url;
    ?>
      <div class="error">
        <p><?php printf( __('Please <a href="%s">Activate License</a> Designil - Woo Thai Adress for keeping update and your own safety', 'designil-woo-thai-address'), $url); ?></p>
      </div>
    <?php
  }
}
add_action( 'admin_notices', 'wta_admin_notices' );

