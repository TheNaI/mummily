<?php
/*
 * Login form
 */

if ( ! defined( 'ABSPATH' ) ) exit;

?><div class="login-form woocommerce">

	<?php $woo_sp_forms_login_title = get_option('woo_sp_forms_login_title');?>

	<h2><?php echo __($woo_sp_forms_login_title, 'woocommerce'); ?></h2>

	<form class="woocomerce-form woocommerce-form-login login" method="post">

		<?php do_action( 'woocommerce_login_form_start' ); ?>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="username"><?php _e( 'Username or email address', 'woocommerce' ); ?> <span class="required">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
			<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" />
		</p>

		<?php do_action( 'woocommerce_login_form' ); ?>

		<p class="form-row">
			<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
			<input type="submit" class="woocommerce-Button button" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>" />
		</p>

		<?php do_action( 'woocommerce_login_form_end' ); ?>

	</form>
		<p class="woocommerce-LostPassword lost_password">
			<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>
		</p>
</div>
