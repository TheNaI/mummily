<?php
/*
Plugin Name: Seed Confirm Pro
Plugin URI: https://www.seedthemes.com/plugin/seed-confirm-pro
Description: Creates confirmation form for bank transfer payment. If using with WooCommerce, this plugin will get bank information from WooCommerce.
Version: 1.0.5
Author: SeedThemes
Author URI: https://www.seedthemes.com
License: GPL2
Text Domain: seed-confirm
*/

/*
Copyright 2016 SeedThemes  (email : info@seedthemes.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once( dirname( __FILE__ ) . '/seed-confirm-pro-functions.php' );
require_once( dirname( __FILE__ ) . '/seed-confirm-pro-pending-to-cancelled.php' );

/**
 * Load text domain.
 */
load_plugin_textdomain('seed-confirm', false, basename( dirname( __FILE__ ) ) . '/languages' );

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'EDD_SEED_CONFIRM_STORE_URL', 'https://th.seedthemes.com' );

// the name of your product. This should match the download name in EDD exactly
define( 'EDD_SEED_CONFIRM_ITEM_NAME', 'Seed Confirm Pro: ปลั๊กอินแจ้งชำระเงิน' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/seed-confirm-pro-updater.php' );
}

/**
 * Updater.
 */
add_action( 'admin_init', 'edd_sl_seed_confirm_plugin_updater', 0 );

function edd_sl_seed_confirm_plugin_updater() {
	$status  = get_option( 'seed_confirm_license_status' );

	if($status == 'valid'){

		// retrieve our license key from the DB
		$license_key = trim( get_option( 'seed_confirm_license_key' ) );

		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater( EDD_SEED_CONFIRM_STORE_URL, __FILE__, array(
				'version'   => '1.0.5',                			// current version number
				'license'   => $license_key,         			// license key (used get_option above to retrieve from DB)
				'item_name' => EDD_SEED_CONFIRM_ITEM_NAME, 		// name of this plugin
				'author'    => 'SeedThemes'   					// author of this plugin
			)
		);

	}
}

if(!class_exists('Seed_Confirm'))
{
	class Seed_Confirm
	{
        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            // register actions
        } // END public function __construct

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            // Add Default payment-confirm page.
            $page = get_page_by_path('confirm-payment');

            if (!is_object($page)) {
                global $user_ID;

                $page = array(
                    'post_type'      => 'page',
                    'post_name'      => 'confirm-payment',
                    'post_parent'    => 0,
                    'post_author'    => $user_ID,
                    'post_status'    => 'publish',
                    'post_title'     => __('Confirm Payment', 'seed-confirm'),
                    'post_content'   => '[seed_confirm]',
                    'ping_status'    => 'closed',
                    'comment_status' => 'closed',
                );

                $page_id = wp_insert_post($page);
            }else{
                $page_id = $page->ID;
            }

            // Add default plugin's settings.
            add_option( 'seed_confirm_page', $page_id);
            add_option( 'seed_confirm_notification_text', __( 'Thank you for your payment. We will process your order shortly.', 'seed-confirm' ) );
            add_option( 'seed_confirm_notification_bg_color', '#57AD68' );
            add_option( 'seed_confirm_required', json_encode( array(
                'seed_confirm_name' => 'true',
                'seed_confirm_contact' => 'true',
                'seed_confirm_amount' => 'true',
            ) ) );
            add_option( 'seed_confirm_optional', json_encode( array(
                'optional_address' => '',
                'optional_information' => '',
            ) ) );

            // Add default schedule time for cancel order.
            update_option('seed_confirm_schedule_status', 'false');

            $default_time = 1140; // 1 day
            update_option('seed_confirm_time', $default_time);

            // Add default email template.
            update_option('seed_confirm_email_template', '');

        } // END public static function activate

        /**
         * Deactivate the plugin
         */     
        public static function deactivate()
        {
            // Clear schedule time for cancel order.
            delete_option('seed_confirm_time');
            wp_clear_scheduled_hook('seed_confirm_schedule_pending_to_cancelled_orders');

        } // END public static function deactivate
    } // END class Seed_Confirm
} // END if(!class_exists('Seed_Confirm'))

if(class_exists('Seed_Confirm'))
{
    // Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('Seed_Confirm', 'activate'));
	register_deactivation_hook(__FILE__, array('Seed_Confirm', 'deactivate'));

    // instantiate the plugin class
	$Seed_Confirm = new Seed_Confirm();
}

/**
 * Remove all woocommerce_thankyou_bacs hooks.
 * Cause we don't want to display all bacs from woocommerce.
 * Web show new one that is better.
 */
add_action( 'template_redirect', 'seed_confirm_remove_hook_thankyou_bacs' );

function seed_confirm_remove_hook_thankyou_bacs() {
	if(is_woocommerce_activated()){
		$gateways = WC()->payment_gateways()->payment_gateways();
		remove_action( 'woocommerce_thankyou_bacs', array( $gateways[ 'bacs' ], 'thankyou_page' ) );
	}
}

/**
 * Remove the original bank details
 * @link http://www.vanbodevelops.com/tutorials/remove-bank-details-from-woocommerce-order-emails
 */
add_action('init', 'seed_confirm_remove_bank_details', 100);

function seed_confirm_remove_bank_details()
{
	if (!is_woocommerce_activated()) {
		return;
	}

	// Get the gateways instance
	$gateways = WC_Payment_Gateways::instance();

	// Get all available gateways, [id] => Object
	$available_gateways = $gateways->get_available_payment_gateways();

	if (isset($available_gateways['bacs'])) {
		// If the gateway is available, remove the action hook
		remove_action('woocommerce_email_before_order_table', array($available_gateways['bacs'], 'email_instructions'), 10, 3);
	}
}

/**
 * Add bank lists to these pages.
 * Confirm page
 * Thankyou page
 * Thankyou email - only first email
 */
add_shortcode( 'seed_confirm_banks', 'seed_confirm_banks' );
add_action( 'woocommerce_thankyou_bacs', 'seed_confirm_banks', 10);

/**
 * Add bank lists to email only customer's first email.
 */
add_action( 'woocommerce_email_before_order_table', 'seed_confirm_banks_email', 10, 2);

function seed_confirm_banks_email($order, $sent_to_admin) {
    if(!$sent_to_admin && $order->has_status( 'on-hold' )) {
        // If user select payment method not bacs
        // Don't add bank list to email.
        $payment_method = get_post_meta( $order->id, '_payment_method', true );
        if($payment_method != 'bacs') return ;

        seed_confirm_banks($order->id);
    }
}

function seed_confirm_banks( $orderid ) {
	$thai_accounts = array();

	$gateways = WC()->payment_gateways->get_available_payment_gateways();

	$bacs_settings = $gateways['bacs'];

	$thai_accounts = seed_confirm_get_banks($bacs_settings->account_details);

    do_action('seed_confirm_before_banks', $orderid);
?>
<div id="seed-confirm-banks" class="seed-confirm-banks">
	<h2><?php esc_html_e( 'Our Bank Details', 'seed-confirm' ); ?></h2>
	<p><?php echo $bacs_settings->description;?></p>
	<div class="table-responsive _heading">
		<table class="table">
			<thead>
				<tr>
					<th class="seed-confirm-bank-logo">&nbsp;</th>
					<th class="seed-confirm-bank-name"><?php esc_html_e( 'Bank Name', 'seed-confirm' ); ?></th>
					<th class="seed-confirm-bank-sort-code"><?php esc_html_e( 'Sort Code', 'seed-confirm' ); ?></th>
					<th class="seed-confirm-bank-account-number"><?php esc_html_e( 'Account Number', 'seed-confirm' ); ?></th>
					<th class="seed-confirm-bank-account-name"><?php esc_html_e( 'Account Name', 'seed-confirm' ); ?></th>	
				</tr>
			</thead>
			<tbody>
				<?php foreach( $thai_accounts as $_account ): ?>
				<tr>
					<td class="seed-confirm-bank-logo"><?php if($_account['logo']) { echo '<img src="'. $_account['logo'] . '" width="32" height="32" style="border-radius:5px">';} ?></td>
					<td class="seed-confirm-bank-name"><?php echo $_account['bank_name']; ?></td>
					<td class="seed-confirm-bank-sort-code"><?php echo $_account['sort_code']; ?></td>
					<td class="seed-confirm-bank-account-number"><?php echo $_account['account_number'];?></td>
					<td class="seed-confirm-bank-account-name"><?php echo $_account['account_name'];?></td>		
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php
    do_action('seed_confirm_after_banks', $orderid);
}

/**
 * Enqueue css and javascript for confirmation payment page.
 * CSS for feel good.
 * javascript for validate data.
 */
add_action( 'wp_enqueue_scripts', 'seed_confirm_scripts' );

function seed_confirm_scripts() {
	if(!is_admin()) {
		wp_enqueue_style( 'seed-confirm', plugin_dir_url( __FILE__ ) . 'seed-confirm-pro.css' , array() );
		wp_enqueue_script( 'seed-confirm', plugin_dir_url( __FILE__ ) . 'seed-confirm-pro.js' , array('jquery'), '2016-1', true );
	}
}

/**
 * Enqueue javascript for settings on admin page.
 */
add_action( 'admin_enqueue_scripts', 'seed_confirm_admin_scripts' );

function seed_confirm_admin_scripts() {
    if(is_admin()){
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_style( 'seed-confirm-admin', plugin_dir_url( __FILE__ ) . 'seed-confirm-pro-admin.css', array());
        wp_enqueue_script( 'seed-confirm', plugin_dir_url( __FILE__ ) . 'seed-confirm-pro-admin.js' , array('wp-color-picker','jquery-ui-sortable'));
    }
}

add_filter( 'woocommerce_bacs_accounts', 'seed_confirm_bacs', 10 );

function seed_confirm_bacs( $accounts ) {

	$thai_accounts = seed_confirm_get_banks($accounts);

    return $thai_accounts;
}

/**
 * Register seed_confirm shortcode.
 * This shortcode display form for  payment confirmation.
 * [seed_confirm]
 */
add_shortcode( 'seed_confirm', 'seed_confirm_shortcode' );

function seed_confirm_shortcode( $atts ) {
	global $post;

	$seed_confirm_name = '';
	$seed_confirm_contact = '';
	$seed_confirm_order = '';
	$seed_confirm_account_number = '';
	$seed_confirm_amount = '';
	$seed_confirm_date = '';
	$seed_confirm_hour = '';
	$seed_confirm_minute = '';

	$current_user = wp_get_current_user();

	$user_id = $current_user->ID;

	$seed_confirm_name = get_user_meta( $user_id, 'billing_first_name', true ) . ' ' . get_user_meta( $user_id, 'billing_last_name', true );
	$seed_confirm_contact = get_user_meta( $user_id, 'billing_phone', true );

	$seed_confirm_date = current_time('d-m-Y');
	$seed_confirm_hour = current_time('H');
	$seed_confirm_minute = current_time('i');

	ob_start();
	?>
	<?php if( $_SERVER['REQUEST_METHOD'] === 'POST' ): ?>
	<div class="seed-confirm-message" style="background-color: <?php echo get_option( 'seed_confirm_notification_bg_color' ); ?>">
		<?php echo get_option( 'seed_confirm_notification_text' ); ?>
	</div>
	<?php endif; ?>

	<form method="post" id="seed-confirm-form" class="seed-confirm-form _heading" enctype="multipart/form-data">
		<?php wp_nonce_field( 'seed-confirm-form-'.$post->ID ) ?>
        <?php
        $seed_confirm_required = json_decode( get_option( 'seed_confirm_required' ), true );
        $seed_confirm_optional = json_decode( get_option( 'seed_confirm_optional' ), true );

        do_action('seed_confirm_after_form_open');
        ?>
		<div class="form-group row">
			<div class="col-sm-6">
				<label for="seed-confirm-name"><?php esc_html_e( 'Name', 'seed-confirm' ); ?></label>
				<input class="form-control <?php if( isset( $seed_confirm_required['seed_confirm_name'] )){ echo 'required';} ?>" type="text" id="seed-confirm-name" name="seed-confirm-name" value="<?php echo esc_html( $seed_confirm_name ); ?>" />
			</div>	
			<div class="col-sm-6">
				<label for="seed-confirm-contact"><?php esc_html_e( 'Contact', 'seed-confirm' ); ?></label>
				<input class="form-control <?php if( isset( $seed_confirm_required['seed_confirm_contact'] )){ echo 'required';} ?>" type="text" id="seed-confirm-contact" name="seed-confirm-contact" value="<?php echo esc_html( $seed_confirm_contact ); ?>" />
			</div>
		</div>
        <?php
        if(isset($seed_confirm_optional['optional_address']) && $seed_confirm_optional['optional_address'] == 'true'){
        ?>
            <div class="form-group">
                <div class="seed-confirm-optional-address">
                    <label><?php esc_html_e( 'Address', 'seed-confirm' ); ?></label>
                    <textarea rows="7" class="form-control" id="seed-confirm-optional-address" name="seed-confirm-optional-address"></textarea>
                </div>
            </div>
        <?php }?>
		<div class="form-group row">
			<div class="col-sm-6">
				<label for="seed-confirm-order"><?php esc_html_e( 'Order', 'seed-confirm' ); ?></label>

				<?php
				$user_id = $current_user->ID;

				$customer_orders = array();

				if( $user_id !== 0 && is_woocommerce_activated()) {
					$customer_orders = get_posts( array(
						'numberposts' => -1,
						'meta_key'    => '_customer_user',
						'meta_value'  => $user_id,
						'post_type'   => wc_get_order_types(),
						'post_status' => array( 'wc-on-hold', 'wc-processing' ),
						)
					);
				}

				if( count( $customer_orders ) > 0 ) {
					?>

					<select id="seed-confirm-order" name="seed-confirm-order" class="form-control <?php if( isset( $seed_confirm_required['seed_confirm_order'] )){ echo 'required';} ?>">
						<?php
						foreach( $customer_orders as $_order ):
							$order = new WC_Order( $_order->ID );
						?>
						<option value="<?php echo $_order->ID ?>"<?php if($seed_confirm_order == $_order->ID): ?> selected="selected"<?php endif ?>>
							<?php 
								if( $_order->post_status == 'wc-processing' ) {esc_html_e( '[Noted] ', 'seed-confirm' ); };
								echo __('No. ', 'seed-confirm') . $_order->ID .__(' - Amount: ', 'seed-confirm') . $order->get_total() . ' '. get_woocommerce_currency_symbol(); 
							?>
						</option>
						<?php
							endforeach;
						?>
					</select>
					<?php } else { ?>
						<input type="text" class="form-control <?php if( isset( $seed_confirm_required['seed_confirm_order'] )){ echo 'required';} ?>" id="seed-confirm-order" name="seed-confirm-order" value="<?php echo esc_html( $seed_confirm_order ); ?>" />
						<?php } ?>
					</div>
					<div class="col-sm-6">
						<label for="seed-confirm-amount"><?php esc_html_e( 'Amount', 'seed-confirm' ); ?></label>
						<input type="text" class="form-control <?php if( isset( $seed_confirm_required['seed_confirm_amount'] )){ echo 'required';} ?>" name="seed-confirm-amount" id="seed-confirm-amount" value="<?php echo esc_html( $seed_confirm_amount ); ?>" />
					</div>
				</div>
				<?php
				$account_details = get_option('woocommerce_bacs_accounts', true);
				if( !is_null( $account_details ) ) {
                    $thai_accounts = seed_confirm_get_banks($account_details);
				}
			?>
			<div class="form-group seed-confirm-bank-info">
				<label><?php esc_html_e( 'Bank Account', 'seed-confirm' ); ?></label>
				<?php if( count( $thai_accounts ) > 0 ): ?>
				<?php foreach( $thai_accounts as $_account ): ?>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input <?php if( isset( $seed_confirm_required['seed_confirm_account_number'] )){ echo 'required';} ?>" type="radio" id="bank-<?php echo $_account['account_number']; ?>" name="seed-confirm-account-number" value='<?php echo $_account['bank_name']; ?>,<?php echo $_account['account_number']; ?>' <?php if( $seed_confirm_account_number == $_account['bank_name'].','.$_account['account_number']): ?> selected="selected"<?php endif; ?>>
                            <span class="seed-confirm-bank-info-logo"><?php if($_account['logo']) { echo '<img src="'. $_account['logo'] . '" width="32" height="32">';} ?></span>
                            <span class="seed-confirm-bank-info-bank"><?php echo $_account['bank_name']; ?></span>
                            <span class="seed-confirm-bank-info-account-number"><?php echo $_account['account_number']; ?></span>
                            <span class="seed-confirm-bank-info-account-name"><?php echo $_account['account_name']; ?></span>
                        </label>
                    </div>
			    <?php endforeach; ?>
			<?php endif; ?>
		</div>
		<?php wp_enqueue_script('jquery-ui-datepicker'); ?>
		<div class="form-group row">
			<div class="col-sm-6 seed-confirm-date">
				<label for="seed-confirm-date"><?php esc_html_e( 'Transfer Date', 'seed-confirm' ); ?></label>
				<input type="text" id="seed-confirm-date" name="seed-confirm-date" class="form-control <?php if( isset( $seed_confirm_required['seed_confirm_date'] )){ echo 'required';} ?>" value="<?php echo $seed_confirm_date ?>"/>
			</div>
			<div class="col-sm-6 seed-confirm-time">
				<label><?php esc_html_e( 'Time', 'seed-confirm' ); ?></label>
				<div class="form-inline">
					
					<select name="seed-confirm-hour" id="seed-confirm-hour" class="form-control">
						<option value="00"<?php if( $seed_confirm_hour == '00'): ?> selected='selected'<?php endif; ?>>00</option>
						<option value="01"<?php if( $seed_confirm_hour == '01'): ?> selected='selected'<?php endif; ?>>01</option>
						<option value="02"<?php if( $seed_confirm_hour == '02'): ?> selected='selected'<?php endif; ?>>02</option>
						<option value="03"<?php if( $seed_confirm_hour == '03'): ?> selected='selected'<?php endif; ?>>03</option>
						<option value="04"<?php if( $seed_confirm_hour == '04'): ?> selected='selected'<?php endif; ?>>04</option>
						<option value="05"<?php if( $seed_confirm_hour == '05'): ?> selected='selected'<?php endif; ?>>05</option>
						<option value="06"<?php if( $seed_confirm_hour == '06'): ?> selected='selected'<?php endif; ?>>06</option>
						<option value="07"<?php if( $seed_confirm_hour == '07'): ?> selected='selected'<?php endif; ?>>07</option>
						<option value="08"<?php if( $seed_confirm_hour == '08'): ?> selected='selected'<?php endif; ?>>08</option>
						<option value="09"<?php if( $seed_confirm_hour == '09'): ?> selected='selected'<?php endif; ?>>09</option>
						<option value="10"<?php if( $seed_confirm_hour == '10'): ?> selected='selected'<?php endif; ?>>10</option>
						<option value="11"<?php if( $seed_confirm_hour == '11'): ?> selected='selected'<?php endif; ?>>11</option>
						<option value="12"<?php if( $seed_confirm_hour == '12'): ?> selected='selected'<?php endif; ?>>12</option>
						<option value="13"<?php if( $seed_confirm_hour == '13'): ?> selected='selected'<?php endif; ?>>13</option>
						<option value="14"<?php if( $seed_confirm_hour == '14'): ?> selected='selected'<?php endif; ?>>14</option>
						<option value="15"<?php if( $seed_confirm_hour == '15'): ?> selected='selected'<?php endif; ?>>15</option>
						<option value="16"<?php if( $seed_confirm_hour == '16'): ?> selected='selected'<?php endif; ?>>16</option>
						<option value="17"<?php if( $seed_confirm_hour == '17'): ?> selected='selected'<?php endif; ?>>17</option>
						<option value="18"<?php if( $seed_confirm_hour == '18'): ?> selected='selected'<?php endif; ?>>18</option>
						<option value="19"<?php if( $seed_confirm_hour == '19'): ?> selected='selected'<?php endif; ?>>19</option>
						<option value="20"<?php if( $seed_confirm_hour == '20'): ?> selected='selected'<?php endif; ?>>20</option>
						<option value="21"<?php if( $seed_confirm_hour == '21'): ?> selected='selected'<?php endif; ?>>21</option>
						<option value="22"<?php if( $seed_confirm_hour == '22'): ?> selected='selected'<?php endif; ?>>22</option>
						<option value="23"<?php if( $seed_confirm_hour == '23'): ?> selected='selected'<?php endif; ?>>23</option>
					</select>
					
					
					<select name="seed-confirm-minute" id="seed-confirm-minute" class="form-control">
						<option value="00"<?php if( $seed_confirm_minute == '00'): ?> selected='selected'<?php endif; ?>>00</option>
						<option value="01"<?php if( $seed_confirm_minute == '01'): ?> selected='selected'<?php endif; ?>>01</option>
						<option value="02"<?php if( $seed_confirm_minute == '02'): ?> selected='selected'<?php endif; ?>>02</option>
						<option value="03"<?php if( $seed_confirm_minute == '03'): ?> selected='selected'<?php endif; ?>>03</option>
						<option value="04"<?php if( $seed_confirm_minute == '04'): ?> selected='selected'<?php endif; ?>>04</option>
						<option value="05"<?php if( $seed_confirm_minute == '05'): ?> selected='selected'<?php endif; ?>>05</option>
						<option value="06"<?php if( $seed_confirm_minute == '06'): ?> selected='selected'<?php endif; ?>>06</option>
						<option value="07"<?php if( $seed_confirm_minute == '07'): ?> selected='selected'<?php endif; ?>>07</option>
						<option value="08"<?php if( $seed_confirm_minute == '08'): ?> selected='selected'<?php endif; ?>>08</option>
						<option value="09"<?php if( $seed_confirm_minute == '09'): ?> selected='selected'<?php endif; ?>>09</option>
						<option value="10"<?php if( $seed_confirm_minute == '10'): ?> selected='selected'<?php endif; ?>>10</option>
						<option value="11"<?php if( $seed_confirm_minute == '11'): ?> selected='selected'<?php endif; ?>>11</option>
						<option value="12"<?php if( $seed_confirm_minute == '12'): ?> selected='selected'<?php endif; ?>>12</option>
						<option value="13"<?php if( $seed_confirm_minute == '13'): ?> selected='selected'<?php endif; ?>>13</option>
						<option value="14"<?php if( $seed_confirm_minute == '14'): ?> selected='selected'<?php endif; ?>>14</option>
						<option value="15"<?php if( $seed_confirm_minute == '15'): ?> selected='selected'<?php endif; ?>>15</option>
						<option value="16"<?php if( $seed_confirm_minute == '16'): ?> selected='selected'<?php endif; ?>>16</option>
						<option value="17"<?php if( $seed_confirm_minute == '17'): ?> selected='selected'<?php endif; ?>>17</option>
						<option value="18"<?php if( $seed_confirm_minute == '18'): ?> selected='selected'<?php endif; ?>>18</option>
						<option value="19"<?php if( $seed_confirm_minute == '19'): ?> selected='selected'<?php endif; ?>>19</option>
						<option value="20"<?php if( $seed_confirm_minute == '20'): ?> selected='selected'<?php endif; ?>>20</option>
						<option value="21"<?php if( $seed_confirm_minute == '21'): ?> selected='selected'<?php endif; ?>>21</option>
						<option value="22"<?php if( $seed_confirm_minute == '22'): ?> selected='selected'<?php endif; ?>>22</option>
						<option value="23"<?php if( $seed_confirm_minute == '23'): ?> selected='selected'<?php endif; ?>>23</option>
						<option value="24"<?php if( $seed_confirm_minute == '24'): ?> selected='selected'<?php endif; ?>>24</option>
						<option value="25"<?php if( $seed_confirm_minute == '25'): ?> selected='selected'<?php endif; ?>>25</option>
						<option value="26"<?php if( $seed_confirm_minute == '26'): ?> selected='selected'<?php endif; ?>>26</option>
						<option value="27"<?php if( $seed_confirm_minute == '27'): ?> selected='selected'<?php endif; ?>>27</option>
						<option value="28"<?php if( $seed_confirm_minute == '28'): ?> selected='selected'<?php endif; ?>>28</option>
						<option value="29"<?php if( $seed_confirm_minute == '29'): ?> selected='selected'<?php endif; ?>>29</option>
						<option value="30"<?php if( $seed_confirm_minute == '30'): ?> selected='selected'<?php endif; ?>>30</option>
						<option value="31"<?php if( $seed_confirm_minute == '31'): ?> selected='selected'<?php endif; ?>>31</option>
						<option value="32"<?php if( $seed_confirm_minute == '32'): ?> selected='selected'<?php endif; ?>>32</option>
						<option value="33"<?php if( $seed_confirm_minute == '33'): ?> selected='selected'<?php endif; ?>>33</option>
						<option value="34"<?php if( $seed_confirm_minute == '34'): ?> selected='selected'<?php endif; ?>>34</option>
						<option value="35"<?php if( $seed_confirm_minute == '35'): ?> selected='selected'<?php endif; ?>>35</option>
						<option value="36"<?php if( $seed_confirm_minute == '36'): ?> selected='selected'<?php endif; ?>>36</option>
						<option value="37"<?php if( $seed_confirm_minute == '37'): ?> selected='selected'<?php endif; ?>>37</option>
						<option value="38"<?php if( $seed_confirm_minute == '38'): ?> selected='selected'<?php endif; ?>>38</option>
						<option value="39"<?php if( $seed_confirm_minute == '39'): ?> selected='selected'<?php endif; ?>>39</option>
						<option value="40"<?php if( $seed_confirm_minute == '40'): ?> selected='selected'<?php endif; ?>>40</option>
						<option value="41"<?php if( $seed_confirm_minute == '41'): ?> selected='selected'<?php endif; ?>>41</option>
						<option value="42"<?php if( $seed_confirm_minute == '42'): ?> selected='selected'<?php endif; ?>>42</option>
						<option value="43"<?php if( $seed_confirm_minute == '43'): ?> selected='selected'<?php endif; ?>>43</option>
						<option value="44"<?php if( $seed_confirm_minute == '44'): ?> selected='selected'<?php endif; ?>>44</option>
						<option value="45"<?php if( $seed_confirm_minute == '45'): ?> selected='selected'<?php endif; ?>>45</option>
						<option value="46"<?php if( $seed_confirm_minute == '46'): ?> selected='selected'<?php endif; ?>>46</option>
						<option value="47"<?php if( $seed_confirm_minute == '47'): ?> selected='selected'<?php endif; ?>>47</option>
						<option value="48"<?php if( $seed_confirm_minute == '48'): ?> selected='selected'<?php endif; ?>>48</option>
						<option value="49"<?php if( $seed_confirm_minute == '49'): ?> selected='selected'<?php endif; ?>>49</option>
						<option value="50"<?php if( $seed_confirm_minute == '50'): ?> selected='selected'<?php endif; ?>>50</option>
						<option value="51"<?php if( $seed_confirm_minute == '51'): ?> selected='selected'<?php endif; ?>>51</option>
						<option value="52"<?php if( $seed_confirm_minute == '52'): ?> selected='selected'<?php endif; ?>>52</option>
						<option value="53"<?php if( $seed_confirm_minute == '53'): ?> selected='selected'<?php endif; ?>>53</option>
						<option value="54"<?php if( $seed_confirm_minute == '54'): ?> selected='selected'<?php endif; ?>>54</option>
						<option value="55"<?php if( $seed_confirm_minute == '55'): ?> selected='selected'<?php endif; ?>>55</option>
						<option value="56"<?php if( $seed_confirm_minute == '56'): ?> selected='selected'<?php endif; ?>>56</option>
						<option value="57"<?php if( $seed_confirm_minute == '57'): ?> selected='selected'<?php endif; ?>>57</option>
						<option value="58"<?php if( $seed_confirm_minute == '58'): ?> selected='selected'<?php endif; ?>>58</option>
						<option value="59"<?php if( $seed_confirm_minute == '59'): ?> selected='selected'<?php endif; ?>>59</option>
					</select>
					
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('#seed-confirm-date').datepicker({
								dateFormat : 'dd-mm-yy',
								maxDate: new Date
							});
						});

					</script>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="seed-confirm-slip">
				<label><?php esc_html_e( 'Payment Slip', 'seed-confirm' ); ?></label>
				<input type="file" id="seed-confirm-slip" name="seed-confirm-slip" class="<?php if( isset( $seed_confirm_required['seed_confirm_slip'] )){ echo 'required';} ?>" />
			</div>
		</div>
        <?php
        if(isset($seed_confirm_optional['optional_information']) && $seed_confirm_optional['optional_information'] == 'true'){?>
        <div class="form-group">
			<div class="seed-confirm-optional-information">
				<label><?php esc_html_e( 'Remark', 'seed-confirm' ); ?></label>
                <textarea rows="7" class="form-control" id="seed-confirm-optional-information" name="seed-confirm-optional-information"></textarea>
			</div>
		</div>
        <?php }?>
		<input type="hidden" name="postid" value="<?php echo $post->ID ?>" />
		<input type="submit" class="btn btn-primary" value="<?php esc_html_e( 'Submit Payment Detail', 'seed-confirm' ); ?>" />
	    <?php do_action('seed_confirm_before_form_close');?>
    </form>

<?php
	return ob_get_clean();
}

/**
 * Grab POST from confirmation payment form and keep it in database.
 */
add_action( 'init', 'seed_confirm_init' , 10 );

function seed_confirm_init() {
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ):
		if( array_key_exists( 'postid' , $_POST )
			&& array_key_exists( '_wpnonce' , $_POST )
			&& wp_verify_nonce( $_POST['_wpnonce'], 'seed-confirm-form-'.$_POST['postid'] ) ):

			$name = $_POST[ 'seed-confirm-name' ];
			$contact = $_POST[ 'seed-confirm-contact' ];
			$order_id = $_POST[ 'seed-confirm-order' ];
			$bank = array_key_exists( 'seed-confirm-account-number', $_POST) ? $_POST[ 'seed-confirm-account-number' ] : '';
			$amount = $_POST['seed-confirm-amount' ];
			$date = $_POST[ 'seed-confirm-date' ];
			$hour = $_POST[ 'seed-confirm-hour' ];
			$minute = $_POST[ 'seed-confirm-minute' ];
			$optional_information =  array_key_exists( 'seed-confirm-optional-information', $_POST) ? $_POST[ 'seed-confirm-optional-information' ] : '';
			$optional_address =  array_key_exists( 'seed-confirm-optional-address', $_POST) ? $_POST[ 'seed-confirm-optional-address' ] : '';
			$the_content = '<div class="seed_confirm_log">';

			if( trim( $name ) != '' ) {
				$the_content .= '<strong>' . esc_html__( 'Name', 'seed-confirm' ) . ': </strong>';
				$the_content .= '<span>'. $name . '</span><br>';
			}

			if( trim( $contact ) != '' ) {
				$the_content .= '<strong>' . esc_html__( 'Contact', 'seed-confirm' ) . ': </strong>';
				$the_content .= '<span>'. $contact . '</span><br>';
			}

			if( trim( $optional_address ) != '' ) {
				$the_content .= '<strong>' . esc_html__( 'Address', 'seed-confirm' ) . ': </strong>';
				$the_content .= '<span>'. $optional_address . '</span><br>';
			}

			if( trim( $order_id ) != '' ) {
				$the_content .= '<strong>' . esc_html__( 'Order no', 'seed-confirm' ) . ': </strong>';
				$the_content .= '<span><a href="'. get_admin_url() .'post.php?post=' . $order_id . '&action=edit" target="_blank">'. $order_id . '</a></span><br>';
			}

            if( trim( $bank ) != '' ) {
			    list($bank_name, $account_number) = explode(',', $bank);

                $the_content .= '<strong>' . esc_html__( 'Bank name', 'seed-confirm' ) . ': </strong>';
                $the_content .= '<span>'. $bank_name . '</span><br>';
                $the_content .= '<strong>' . esc_html__( 'Account no', 'seed-confirm' ) . ': </strong>';
                $the_content .= '<span>'. $account_number . '</span><br>';
            }

		if( trim( $amount )  != '' ) {
			$the_content .= '<strong>' . esc_html__( 'Amount', 'seed-confirm' ) . ': </strong>';
			$the_content .= '<span>'. $amount . '</span><br>';
		}

		if( trim( $date )  != '' ) {
			$the_content .= '<strong>' . esc_html__( 'Date', 'seed-confirm' ) . ': </strong>';
			$the_content .= '<span>'. $date;

			if( trim( $hour )  != '' ) {
				$the_content .= ' ' . $hour;

				if( trim( $minute )  != '' ) {
					$the_content .= ':' . $minute;
				} else {
					$the_content .= ':00';
				}
			}
			$the_content .= '</span><br>';
		}

        if( trim( $optional_information )  != '' ) {
            $the_content .= '<strong>' . esc_html__( 'Remark', 'seed-confirm' ) . ': </strong>';
            $the_content .= '<span>'. $optional_information . '</span><br>';
        }

		$the_content .= '</div>';

		$symbol = get_option('seed_confirm_symbol', (function_exists('get_woocommerce_currency_symbol')?get_woocommerce_currency_symbol():'฿'));

		$transfer_notification_id = wp_insert_post( array	(
			'post_title' => __('Order no. ', 'seed-confirm') .  $order_id . __(' by ', 'seed-confirm') . $name . ' ('. $amount .' '. $symbol . ')',
			'post_content' => $the_content,
			'post_type' => 'seed_confirm_log',
			'post_status' => 'publish'
			)
		);

		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		// Random slip filename. 
        $overrides = array(
            'test_form' => false,
            'unique_filename_callback' => 'seed_unique_filename'
        );
		
		$slip_image = wp_handle_upload( $_FILES['seed-confirm-slip'], $overrides );

		// Append slip image to post content. 

		if( $slip_image && !isset( $slip_image['error'] ) ){

			$the_content .= '<br><img class="seed-confirm-img" src="'.$slip_image['url'].'" />';

			$attrs = array(
				'ID'           => $transfer_notification_id,
				'post_content' => $the_content,
			);

			wp_update_post( $attrs );
            update_post_meta( $transfer_notification_id, 'seed-confirm-image', $slip_image['url'] );
		}

        // Send email to admin.
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
		$headers .= 'From: Seed Confirm <'.get_option( 'admin_email' ).'>' . "\r\n" .
		$headers .= 'X-Mailer: PHP/' . phpversion();

		$mailsent = wp_mail( get_option('seed_confirm_email_notification', get_option( 'admin_email' )) , 'Bank transfer notification', $the_content, $headers );

		if ( ! add_post_meta( $transfer_notification_id, 'seed-confirm-name', $_POST['seed-confirm-name'] , true ) )
			update_post_meta( $transfer_notification_id, 'seed-confirm-name', $_POST['seed-confirm-name'] );

		if ( ! add_post_meta( $transfer_notification_id, 'seed-confirm-contact', $_POST['seed-confirm-contact'] , true ) )
			update_post_meta( $transfer_notification_id, 'seed-confirm-contact', $_POST['seed-confirm-contact'] );

        if( array_key_exists( 'seed-confirm-optional-address', $_POST) ) {
            if ( ! add_post_meta( $transfer_notification_id, 'seed-confirm-optional-address', $_POST['seed-confirm-optional-address'] , true ) )
                update_post_meta( $transfer_notification_id, 'seed-confirm-optional-address', $_POST['seed-confirm-optional-address'] );
        }

		if ( ! add_post_meta( $transfer_notification_id, 'seed-confirm-order', $_POST['seed-confirm-order'] , true ) )
			update_post_meta( $transfer_notification_id, 'seed-confirm-order', $_POST['seed-confirm-order'] );

		if( array_key_exists( 'seed-confirm-account-number', $_POST) ) {
            $bank = $_POST['seed-confirm-account-number'];
            list($bank_name, $account_number) = explode(',', $bank);

			if ( ! add_post_meta( $transfer_notification_id, 'seed-confirm-bank-name', $bank_name , true ) )
				update_post_meta( $transfer_notification_id, 'seed-confirm-bank-name', $bank_name );
			if ( ! add_post_meta( $transfer_notification_id, 'seed-confirm-account-number', $account_number , true ) )
				update_post_meta( $transfer_notification_id, 'seed-confirm-account-number', $account_number );
		}

		if ( ! add_post_meta( $transfer_notification_id, 'seed-confirm-amount', $_POST['seed-confirm-amount'] , true ) )
			update_post_meta( $transfer_notification_id, 'seed-confirm-amount', $_POST['seed-confirm-amount'] );

		if ( ! add_post_meta( $transfer_notification_id, 'seed-confirm-date', $_POST['seed-confirm-date'] , true ) )
			update_post_meta( $transfer_notification_id, 'seed-confirm-date', $_POST['seed-confirm-date'] );

		if ( ! add_post_meta( $transfer_notification_id, 'seed-confirm-hour', $_POST['seed-confirm-hour'] , true ) )
			update_post_meta( $transfer_notification_id, 'seed-confirm-hour', $_POST['seed-confirm-hour'] );

		if ( ! add_post_meta( $transfer_notification_id, 'seed-confirm-minute', $_POST['seed-confirm-minute'] , true ) )
			update_post_meta( $transfer_notification_id, 'seed-confirm-minute', $_POST['seed-confirm-minute'] );

        if( array_key_exists( 'seed-confirm-optional-information', $_POST) ) {
            if ( ! add_post_meta( $transfer_notification_id, 'seed-confirm-optional-information', $_POST['seed-confirm-optional-information'] , true ) )
                update_post_meta( $transfer_notification_id, 'seed-confirm-optional-information', $_POST['seed-confirm-optional-information'] );
        }

		// Automatic update woo order status if woocommerce is installed.
		if(is_woocommerce_activated()){
            $post = get_post($order_id);
            
			if( !empty($post) && $post->post_type == 'shop_order' ) {
                $order = new WC_Order($order_id);
				$order->update_status('processing', 'order_note');

                // Send email
                WC()->mailer()->emails['WC_Email_Customer_Processing_Order']->trigger($order_id);
			}
		}

		endif;
		endif;
	}

/**
 * Register seed_confirm_log PostType.
 * Store confirmation payment.
 */
add_action('init', 'seed_confirm_register_transfer_notifications_logs');

function seed_confirm_register_transfer_notifications_logs() {
	register_post_type('seed_confirm_log', array(
		'labels'	=> array(
			'name'		=> __('Confirm Logs', 'seed-confirm'),
			'singular_name' => __('Log'),
			'menu_name'	=> __('Confirm Logs','seed-confirm')
			),
		'capabilities' => array(
			'create_posts' => 'do_not_allow',
			),
		'map_meta_cap'	=> true,
		'supports' => array( 'title', 'editor', 'custom-fields', 'thumbnail' ),
		'has_archive'	=> false,
		'menu_icon'   => 'dashicons-paperclip',
		'public'	=> true,
		'publicly_queryable'	=> false
		)
	);
}

/**
 * Adds a submenu page under a seed_confirm_log posttype.
 */
add_action('admin_menu', 'seed_register_confirm_log_settings_page');

function seed_register_confirm_log_settings_page() {
	add_submenu_page(
		'edit.php?post_type=seed_confirm_log',
		__( 'Settings', 'seed-confirm' ),
		__( 'Settings', 'seed-confirm' ),
		'manage_options',
		'seed-confirm-log-settings',
		'seed_confirm_log_settings_form'
	);
}

/**
 * Callback for submenu page under a seed_confirm_log.
 */
function seed_confirm_log_settings_form() {

	// Set default setting's tab
	if(!isset($_GET['tab']) || $_GET['tab'] == '' || $_GET['tab'] == 'settings'){
		$nav_tab_active = 'settings';
	}elseif($_GET['tab'] == 'bacs'){
		$nav_tab_active = 'bacs';
	}elseif($_GET['tab'] == 'schedule'){
		$nav_tab_active = 'schedule';
	}elseif($_GET['tab'] == 'license'){
		$nav_tab_active = 'license';
	}else{
		$nav_tab_active = 'settings';
	}
?>
	<form method="post" action="" name="form">
		<h2 class="nav-tab-wrapper seed-confirm-tab-wrapper">
			<a href="<?php echo admin_url('edit.php?post_type=seed_confirm_log&page=seed-confirm-log-settings&tab=settings'); ?>" class="nav-tab <?php if($nav_tab_active == 'settings') echo 'nav-tab-active'; ?>"><?php _e( 'Seed Confirm Settings', 'seed-confirm' ); ?></a>
			<?php if(!is_woocommerce_activated()){ ?>
			<a href="<?php echo admin_url('edit.php?post_type=seed_confirm_log&page=seed-confirm-log-settings&tab=bacs'); ?>" class="nav-tab <?php if($nav_tab_active == 'bacs') echo 'nav-tab-active'; ?>"><?php _e( 'Bank Accounts', 'seed-confirm' ); ?></a>
			<?php } ?>
            <?php if(is_woocommerce_activated()){ ?>
			<a href="<?php echo admin_url('edit.php?post_type=seed_confirm_log&page=seed-confirm-log-settings&tab=schedule'); ?>" class="nav-tab <?php if($nav_tab_active == 'schedule') echo 'nav-tab-active'; ?>"><?php _e( 'Auto Cancel Unpaid Orders', 'seed-confirm' ); ?></a>
			<?php } ?>
			<a href="<?php echo admin_url('edit.php?post_type=seed_confirm_log&page=seed-confirm-log-settings&tab=license'); ?>" class="nav-tab <?php if($nav_tab_active == 'license') echo 'nav-tab-active'; ?>"><?php _e( 'License', 'seed-confirm' ); ?></a>
		</h2>
		<?php if( isset($_SESSION['saved']) && $_SESSION['saved'] == 'true' ){ ?>
		<div class="updated inline">
			<p><strong><?php _e('Your settings have been saved.', 'seed-confirm'); ?></strong></p>
		</div>
		<?php unset($_SESSION['saved']); ?>
		<?php } ?>
		<!-- Settings tab -->
		<?php if($nav_tab_active == 'settings'){?>

			<h2 class="title"><?php _e('Confirm Payment Page', 'seed-confirm');?></h2>
			<table class="form-table">
				<tbody>
				<tr>
					<th><label for="seed_notification_text"><?php _e( 'Page', 'seed-confirm' ) ?></label></th>
					<td>
						<select name="seed_confirm_page" id="seed_confirm_page">
							<?php
							$pages = get_pages();
							foreach ( $pages as $page ) {
							?>
							<option value="<?php echo $page->ID;?>" <?php if( get_option('seed_confirm_page') == $page->ID){ echo 'selected="selected"';} ?> ><?php echo $page->post_title;?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				</tbody>
			</table>
			<h2 class="title"><?php _e('Notice Message', 'seed-confirm');?></h2>
			<p><?php _e('Display after received payment information.', 'seed-confirm'); ?></p>
			<table class="form-table">
				<tbody>
				<tr>
					<th><label for="seed_notification_text"><?php _e( 'Message', 'seed-confirm' ) ?></label></th>
					<td><input type="text" class="large-text" value="<?php echo get_option( 'seed_confirm_notification_text' ); ?>" id="seed_confirm_notification_text" name="seed_confirm_notification_text"></td>
				</tr>
				<tr>
					<th><label for="seed_notification_bg_color"><?php _e( 'Background Color', 'seed-confirm' ); ?></label></th>
					<td><input type="text" class="color-picker" value="<?php echo get_option( 'seed_confirm_notification_bg_color' ); ?>" id="seed_confirm_notification_bg_color" name="seed_confirm_notification_bg_color"></td>
				</tr>
				</tbody>
			</table>

			<h2 class="title"><?php _e('Confirm Payment Form', 'seed-confirm'); ?></h2>
			<p><?php _e('Display all fields, each field can be set to be required.', 'seed-confirm'); ?></p>
			<table class="form-table">
				<tbody>
				<tr>
					<th><?php _e('Required fields', 'seed-confirm'); ?></th>
					<td>
						<?php $seed_confirm_required = json_decode( get_option( 'seed_confirm_required' ), true ); ?>
						<label><input <?php if( isset( $seed_confirm_required['seed_confirm_name'] ) ){ ?> checked="checked" <?php } ?> type="checkbox" value="true" name="seed_confirm_required[seed_confirm_name]"> <?php _e( 'Name', 'seed-confirm' ); ?></label>
						<br/>
						<label><input <?php if( isset( $seed_confirm_required['seed_confirm_contact'] ) ){ ?> checked="checked" <?php } ?>  type="checkbox" value="true" name="seed_confirm_required[seed_confirm_contact]"> <?php _e( 'Contact', 'seed-confirm' ); ?></label>
						<br/>
						<label><input <?php if( isset( $seed_confirm_required['seed_confirm_order'] ) ){ ?> checked="checked" <?php } ?>  type="checkbox" value="true" name="seed_confirm_required[seed_confirm_order]"> <?php _e( 'Order', 'seed-confirm' ); ?></label>
						<br/>
						<label><input <?php if( isset( $seed_confirm_required['seed_confirm_amount'] ) ){ ?> checked="checked" <?php } ?>  type="checkbox" value="true" name="seed_confirm_required[seed_confirm_amount]"> <?php _e( 'Amount', 'seed-confirm' ); ?></label>
						<br/>
						<label><input <?php if( isset( $seed_confirm_required['seed_confirm_account_number'] ) ){ ?> checked="checked" <?php } ?>  type="checkbox" value="true" name="seed_confirm_required[seed_confirm_account_number]"> <?php _e( 'Bank Account', 'seed-confirm' ); ?></label>
						<br/>
						<label><input <?php if( isset( $seed_confirm_required['seed_confirm_date'] ) ){ ?> checked="checked" <?php } ?>  type="checkbox" value="true" name="seed_confirm_required[seed_confirm_date]"> <?php _e( 'Transfer Date', 'seed-confirm' ); ?></label>
						<br/>
						<label><input <?php if( isset( $seed_confirm_required['seed_confirm_slip'] ) ){ ?> checked="checked" <?php } ?>  type="checkbox" value="true" name="seed_confirm_required[seed_confirm_slip]"> <?php _e( 'Payment Slip', 'seed-confirm' ); ?></label>
					</td>
				</tr>
				</tbody>
			</table>

            <h2 class="title"><?php _e('Optional fields', 'seed-confirm'); ?></h2>
            <p><?php _e('Enable or disable optional fields on payment confirmation form', 'seed-confirm'); ?></p>
            <table class="form-table">
                <tbody>
                <tr>
                    <th><?php _e('Enable?', 'seed-confirm'); ?></th>
                    <td>
                        <?php $seed_confirm_optional = json_decode( get_option( 'seed_confirm_optional' ), true ); ?>
                        <?php
                        // Not necessary to display if the woocommerce is installed.
                        $disabled = '';
                        $disabled_note = '';
                        if(is_woocommerce_activated()){
                            $disabled = ' disabled="disabled" ';
                            $disabled_note = __(' <i>(Disable when WooCommerce is activated.)</i>', 'seed-confirm');
                        }
                        ?>
                        <label><input <?php echo $disabled ;?> <?php if( isset( $seed_confirm_optional['optional_address'] ) ){ ?> checked="checked" <?php } ?> type="checkbox" value="true" name="seed_confirm_optional[optional_address]"> <?php _e( 'Address', 'seed-confirm' ); ?><?php echo $disabled_note ;?></label>
                        <br/>
                        <label><input <?php if( isset( $seed_confirm_optional['optional_information'] ) ){ ?> checked="checked" <?php } ?> type="checkbox" value="true" name="seed_confirm_optional[optional_information]"> <?php _e( 'Remark', 'seed-confirm' ); ?></label>
                        <br/>
                    </td>
                </tr>
                </tbody>
            </table>

			<h2 class="title"><?php _e('Currency', 'seed-confirm'); ?></h2>
			<p><?php _e('Display in confirmation logs.', 'seed-confirm'); ?></p>
			<table class="form-table">
				<tbody>
				<tr>
					<th><?php _e('Currency symbol', 'seed-confirm'); ?></th>
					<td><input type="text" value="<?php echo get_option( 'seed_confirm_symbol' ); ?>" id="seed_confirm_symbol" name="seed_confirm_symbol" class="small-text"></td>
				</tr>
				</tbody>
			</table>

            <h2 class="title"><?php _e('E-Mail Notification', 'seed-confirm'); ?></h2>
            <p><?php _e('User can set multiple emails, separate each email account by comma (,).', 'seed-confirm'); ?></p>
            <p><?php _e('Example', 'seed-confirm');?></p>
            <p>
                <ol>
                    <li>user@example.com</li>
                    <li>user@example.com, anotheruser@example.com</li>
                    <li>User &lt;user@example.com&gt;</li>
                    <li>User &lt;user@example.com&gt;, &lt;Another User &gt;anotheruser@example.com</li>
                </ol>
            </p>
            <table class="form-table">
                <tbody>
                <tr>
                    <th><?php _e('E-Mail', 'seed-confirm'); ?></th>
                    <td><input type="text" value="<?php echo get_option( 'seed_confirm_email_notification', get_option('admin_email') ); ?>" id="seed_confirm_email_notification" name="seed_confirm_email_notification" class="large-text"></td>
                </tr>
                </tbody>
            </table>

		<?php } ?>
		<!-- Bacs tab - hide if woocommerce is activated. -->
		<?php if(!is_woocommerce_activated()){ ?>
			<?php if($nav_tab_active == 'bacs'){ ?>

				<?php $account_details = get_option( 'woocommerce_bacs_accounts'); ?>
				<h2><?php _e( 'Bank Accounts', 'seed-confirm' ); ?></h2>
				<p><?php _e('Direct bank/wire transfer account information.', 'seed-confirm'); ?></p>
				<table class="form-table">
					<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc"><?php _e( 'Account Details', 'seed-confirm' ); ?>:</th>
						<td id="bacs_accounts" class="forminp">
							<table class="widefat seed-confirm-table sortable" cellspacing="0">
								<thead>
								<tr>
									<th class="sort">&nbsp;</th>
									<th><?php _e( 'Account Name', 'seed-confirm' ); ?></th>
									<th><?php _e( 'Account Number', 'seed-confirm' ); ?></th>
									<th><?php _e( 'Bank Name', 'seed-confirm' ); ?></th>
									<th><?php _e( 'Branch', 'seed-confirm' ); ?></th>
									<th><?php _e( 'IBAN', 'seed-confirm' ); ?></th>
									<th><?php _e( 'BIC / Swift', 'seed-confirm' ); ?></th>
								</tr>
								</thead>
								<tbody class="accounts">
								<?php
								$i = -1;
								if ( isset($account_details) && is_array($account_details) ) {
									foreach ( $account_details as $account ) {
										$i++;

										echo '
									<tr class="account">
										<td class="sort"></td>
										<td><input type="text" value="' . esc_attr( wp_unslash( $account['account_name'] ) ) . '" name="bacs_account_name[' . $i . ']" /></td>
										<td><input type="text" value="' . esc_attr( $account['account_number'] ) . '" name="bacs_account_number[' . $i . ']" /></td>
										<td><input type="text" value="' . esc_attr( wp_unslash( $account['bank_name'] ) ) . '" name="bacs_bank_name[' . $i . ']" /></td>
										<td><input type="text" value="' . esc_attr( $account['sort_code'] ) . '" name="bacs_sort_code[' . $i . ']" /></td>
										<td><input type="text" value="' . esc_attr( $account['iban'] ) . '" name="bacs_iban[' . $i . ']" /></td>
										<td><input type="text" value="' . esc_attr( $account['bic'] ) . '" name="bacs_bic[' . $i . ']" /></td>
									</tr>';
									}
								}
								?>
								</tbody>
								<tfoot>
								<tr>
									<th colspan="7"><a href="#" class="add button"><?php _e( '+ Add Account', 'seed-confirm' ); ?></a> <a href="#" class="remove_rows button"><?php _e( 'Remove selected account(s)', 'seed-confirm' ); ?></a></th>
								</tr>
								</tfoot>
							</table>
						</td>
					</tr>
					</tbody>
				</table>

			<?php } ?>
		<?php } ?>
		<!-- Schedule tab - show if woocommerce is activated. -->
		<?php if(is_woocommerce_activated()){ ?>
			<?php if($nav_tab_active == 'schedule'){ ?>

				<h2><?php _e( 'Auto Cancel Unpaid Orders', 'seed-confirm' ); ?></h2>
				<p><?php _e('Change order status from on-hold to cancelled automatically after x minutes.', 'seed-confirm'); ?></p>
                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php _e('Enable?', 'seed-confirm'); ?>
                        </th>
                        <td>
                            <input id="seed_confirm_schedule_status" name="seed_confirm_schedule_status" type="checkbox" value="true" <?php if(get_option('seed_confirm_schedule_status') == 'true'){ ?> checked="checked" <?php } ?> />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php _e('Pending time', 'seed-confirm'); ?>
                        </th>
                        <td>
                            <input id="seed_confirm_time" name="seed_confirm_time" type="text" class="small-text <?php if(get_option('seed_confirm_schedule_status') != 'true'){ ?> disabled <?php } ?>" value="<?php echo get_option('seed_confirm_time', 1440);?>" <?php if(get_option('seed_confirm_schedule_status') != 'true'){ ?> readonly="readonly" <?php } ?> />
                            <label class="description" for="seed_confirm_time"> <?php _e('Minutes (60 minutes = 1 hour, 1440 minutes = 1 day)', 'seed-confirm'); ?></label>
                        </td>
                    </tr>
                    </tbody>
                </table>

			<?php } ?>
		<?php } ?>
		<!-- License tab -->
		<?php 
		if($nav_tab_active == 'license'){ 
			$license = get_option( 'seed_confirm_license_key' );
			$status  = get_option( 'seed_confirm_license_status' );
		?>
			<h2 class="title"><?php _e('License', 'seed-confirm');?></h2>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e('License Key', 'seed-confirm'); ?>
						</th>
						<td>
							<input id="seed_confirm_license_key" name="seed_confirm_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
							<label class="description" for="seed_confirm_license_key"><?php _e('Enter your license key', 'seed-confirm'); ?></label>
						</td>
					</tr>
					<?php if( false !== $license ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('Activate License', 'seed-confirm'); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<span style="color:green;"><?php _e('active', 'seed-confirm'); ?></span>
									<input type="submit" class="button-secondary" name="seed_confirm_license_deactivate" value="<?php _e('Deactivate License', 'see-confirm'); ?>"/>
								<?php } else { ?>
									<input type="submit" class="button-secondary" name="seed_confirm_license_activate" value="<?php _e('Activate License', 'see-confirm'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } ?>

		<!-- Submit form -->
		<p class="submit">
			<?php wp_nonce_field( 'seed-confirm' ) ?>
			<?php submit_button(); ?>
		</p>
	</form>
<?php
}

/**
 * Save settings and bacs into database.
 * Bacs use wp_options.woocommerce_bacs_accounts to keep bacs values.
 * Thus this plugin can share datas with woocommerce plugin.
 * I copy this code from class-wc-gateway-bacs.php
 * @copy wp-content/plugins/woocommerce/includes/gateways/bacs/class-wc-gateway-bacs.php
 */
add_action('init', 'seed_confirm_save_settings');

function seed_confirm_save_settings(){

	if(isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'seed-confirm')){

		// Settings tab activate.
		if(!isset($_GET['tab']) || $_GET['tab'] == '' || $_GET['tab'] == 'settings'){

			update_option( 'seed_confirm_page', $_POST['seed_confirm_page'] );
			update_option( 'seed_confirm_notification_text', $_POST['seed_confirm_notification_text'] );
			update_option( 'seed_confirm_notification_bg_color', $_POST['seed_confirm_notification_bg_color'] );
			update_option( 'seed_confirm_required', json_encode( isset($_POST['seed_confirm_required'])? $_POST['seed_confirm_required']: array() ) );
			update_option( 'seed_confirm_optional', json_encode( isset($_POST['seed_confirm_optional'])? $_POST['seed_confirm_optional']: array() ) );
			update_option( 'seed_confirm_symbol', $_POST['seed_confirm_symbol'] );
			update_option( 'seed_confirm_email_notification', $_POST['seed_confirm_email_notification'] );

			$_SESSION['saved'] = 'true';
		}

		// Bacs tab activate.
		if(isset($_GET['tab']) && $_GET['tab'] == 'bacs'){
			$accounts = array();

			if ( isset( $_POST['bacs_account_name'] ) ) {

				$account_names   = array_map( 'seed_confirm_clean', $_POST['bacs_account_name'] );
				$account_numbers = array_map( 'seed_confirm_clean', $_POST['bacs_account_number'] );
				$bank_names      = array_map( 'seed_confirm_clean', $_POST['bacs_bank_name'] );
				$sort_codes      = array_map( 'seed_confirm_clean', $_POST['bacs_sort_code'] );
				$ibans           = array_map( 'seed_confirm_clean', $_POST['bacs_iban'] );
				$bics            = array_map( 'seed_confirm_clean', $_POST['bacs_bic'] );

				foreach ( $account_names as $i => $name ) {
					if ( ! isset( $account_names[ $i ] ) ) {
						continue;
					}

					$accounts[] = array(
						'account_name'   => $account_names[ $i ],
						'account_number' => $account_numbers[ $i ],
						'bank_name'      => $bank_names[ $i ],
						'sort_code'      => $sort_codes[ $i ],
						'iban'           => $ibans[ $i ],
						'bic'            => $bics[ $i ]
					);
				}

				update_option( 'woocommerce_bacs_accounts', $accounts );

				$_SESSION['saved'] = 'true';
			}
		}

		// Schedule tab activate
        if(isset($_GET['tab']) && $_GET['tab'] == 'schedule'){

            $seed_confirm_schedule_status = (array_key_exists('seed_confirm_schedule_status', $_POST))? $_POST['seed_confirm_schedule_status']:'false';
		    update_option( 'seed_confirm_schedule_status', $seed_confirm_schedule_status);

		    $seed_confirm_time = absint($_POST['seed_confirm_time']);
            update_option( 'seed_confirm_time', $seed_confirm_time);

            // Clear old schedule and add new one.
            // If user set time to 0, remove schedule and not add it (meaning disable).
            wp_clear_scheduled_hook('seed_confirm_schedule_pending_to_cancelled_orders');

            if ($seed_confirm_schedule_status == 'true' && $seed_confirm_time > 0) {
                wp_schedule_single_event(time() + ( $seed_confirm_time * 60 ), 'seed_confirm_schedule_pending_to_cancelled_orders');
            }

            $_SESSION['saved'] = 'true';
        }

        // License tab activate
		if(isset($_GET['tab']) && $_GET['tab'] == 'license'){
			// Check to see if user change new license.
			$old = get_option( 'seed_confirm_license_key' );

			if( $old && $old != $_POST['seed_confirm_license_key'] ) {
				// new license has been entered, so must reactivate
				delete_option( 'seed_confirm_license_status' );
			}

			update_option( 'seed_confirm_license_key', $_POST['seed_confirm_license_key'] );

			$_SESSION['saved'] = 'true';
		}
	}
}

/**
 ************************************
 * Activate license key
 ************************************
 */

add_action('admin_init', 'seed_confirm_activate_license');

function seed_confirm_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['seed_confirm_license_activate'] ) ) {

		// run a quick security check
		if( !isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'seed-confirm') )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'seed_confirm_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_SEED_CONFIRM_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_SEED_CONFIRM_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'seed-confirm' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.', 'seed-confirm' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'revoked' :

						$message = __( 'Your license key has been disabled.', 'seed-confirm' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.', 'seed-confirm' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.', 'seed-confirm' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'seed-confirm' ), EDD_SEED_CONFIRM_ITEM_NAME );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.', 'seed-confirm' );
						break;

					default :

						$message = __( 'An error occurred, please try again.', 'seed-confirm' );
						break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'edit.php?post_type=seed_confirm_log&page=seed-confirm-log-settings&tab=license' );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'seed_confirm_license_status', $license_data->license );
		wp_redirect( admin_url( 'edit.php?post_type=seed_confirm_log&page=seed-confirm-log-settings&tab=license' ) );
		exit();
	}
}

/**
 **********************************************
 * Deactivate license.
 **********************************************
 */
add_action('admin_init', 'seed_confirm_deactivate_license');

function seed_confirm_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['seed_confirm_license_deactivate'] ) ) {

		// run a quick security check
		if( !isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'seed-confirm') )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'seed_confirm_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_SEED_CONFIRM_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_SEED_CONFIRM_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'seed-confirm' );
			}

			$base_url = admin_url( 'edit.php?post_type=seed_confirm_log&page=seed-confirm-log-settings&tab=license' );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {
			delete_option( 'seed_confirm_license_status' );
		}

		wp_redirect( admin_url( 'edit.php?post_type=seed_confirm_log&page=seed-confirm-log-settings&tab=license' ) );
		exit();
	}
}

/**
 * Show admin notice if activate/deactivate license is fail.
 */
add_action( 'admin_notices', 'seed_confirm_admin_notices' );

function seed_confirm_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

		switch( $_GET['sl_activation'] ) {

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
}

/**
 * Copy this function from woocommerce.
 * @copy wp-content/plugins/woocommerce/includes/wc-formatting-functions.php
 */
function seed_confirm_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'wc_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}
/**
 * Add confirm payment button into my oder page.
 * For woocommerce only
 */
add_filter('woocommerce_my_account_my_orders_actions', 'seed_add_confirm_button', 10, 2);

function seed_add_confirm_button($actions, $post){

	$page = get_page_by_path( 'confirm-payment' );
	if(!$page){
		return $actions;
	}

	$url = get_page_link($page->ID);

	// Want to check this order has confirm-payment
	$params = array(
		'post_type' => 'seed_confirm_log',
		'meta_key' => 'seed-confirm-order',
		'meta_value' => $post->id
	);

	$seed_confirm_log = get_posts( $params );

	// Want to check this order already send product to user.
    $order = new WC_Order( $post->id );

	if($order->status == 'completed'){
		$actions['-completed'] = array(
			'url'   => $url,
			'name'  => __('Confirm Payment', 'seed-confirm'),
		);
	}elseif(count($seed_confirm_log)>0){
        $actions['-noted'] = array(
            'url'   => $url,
            'name'  => __('Confirm Payment', 'seed-confirm'),
        );
    }else{
		$actions['confirm-payment'] = array(
			'url'   => $url,
			'name'  => __('Confirm Payment', 'seed-confirm'),
		);
	}

	return $actions;
}
