<?php
/**
 * Login Form
 *
 * @version     2.6.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php wc_print_notices(); ?>
<?php do_action( 'woocommerce_before_customer_login_form' ); ?>
<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
<div class="u-columns row" id="customer_login">
	<div class="u-column1 col-md-6">
<?php else : ?>
<div class="u-columns row">
	<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) !== 'yes' ) : ?>	
    <div class="u-column1">
	<?php else: ?>
    <div class="u-column1 col-md-6 col-md-offset-3">
	<?php endif; ?>
<?php endif; ?>
		<?php 
		$action = ( isset( $_GET['action'] ) && $_GET['action'] ) ? $_GET['action'] : null; 
		if( ( ! $action && $action != 'register' ) || get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ): ?>
        <div class="featured-box align-left">
            <div class="box-content">
				<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) !== 'yes' ) : ?>
					<div class="row">
						<div class="col-sm-6">
							<h2><?php _e( 'New Customers', 'porto'); ?></h2>
							<p><?php _e( 'By creating an account with our store, you will be able to move through the checkout process faster, store multiple shipping addresses, view and track your orders in your account and more.', 'porto'); ?></p>
							<a class="btn btn-primary" href="<?php echo get_permalink( woocommerce_get_page_id( 'myaccount' ) ) . '?action=register'?>"><?php _e( 'Create an Account', 'porto' ); ?></a>
						</div>
						<div class="col-sm-6">
				<?php endif; ?>
							<h2><?php _e( 'Login', 'porto' ); ?></h2>
							<form method="post" class="login">
								<?php do_action( 'woocommerce_login_form_start' ); ?>
								<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
									<label for="username"><?php _e( 'Username or email address', 'porto' ); ?> <span class="required">*</span></label>
									<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
								</p>
								<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
									<label for="password" class="clearfix"><?php _e( 'Password', 'porto' ); ?> <span class="required">*</span>
										<span class="woocommerce-LostPassword lost_password pt-right">
											<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'porto' ); ?></a>
										</span>
									</label>
									<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" />
								</p>
								<?php do_action( 'woocommerce_login_form' ); ?>
								<p class="form-row clearfix">
									<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
									<label for="rememberme" class="inline pt-left">
										<input class="woocommerce-Input woocommerce-Input--checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'porto' ); ?>
									</label>
									<input type="submit" class="woocommerce-Button button btn-lg pt-right" name="login" value="<?php esc_attr_e( 'Login', 'porto' ); ?>" />
								</p>
								<?php do_action( 'woocommerce_login_form_end' ); ?>
							</form>
				<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) !== 'yes' ) : ?>
						</div>
					</div>
				<?php endif; ?>
            </div>
        </div>
		<?php endif; ?>
<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
	</div>
	<div class="u-column2 col-md-6">
        <?php wc_get_template( 'myaccount/form-register.php' ); ?>
	</div>
</div>
<?php else : ?>
	<?php if( isset( $_GET['action'] ) && $_GET['action'] == 'register' ): ?>
		<div class="col-sm-12">
			<?php get_template_part( 'woocommerce/myaccount/form-register-2' ); ?>
		</div>
	<?php endif; ?>
    </div>
</div>
<?php endif; ?>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>