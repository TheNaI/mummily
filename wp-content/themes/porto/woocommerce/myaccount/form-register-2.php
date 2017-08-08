<?php
/**
 * Register Form 2
 *
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $porto_settings;
?>

		<h1 class="heading-primary font-weight-normal">Create an Account</h1>
		
        <div class="featured-box align-left">
            <div class="box-content">

                <h2><?php _e( 'PERSONAL INFORMATION', 'porto' ); ?></h2>

                <form method="post" class="register">

                    <?php do_action( 'woocommerce_register_form_start' ); ?>
					
                    <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

                        <p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
                            <label for="reg_username"><?php _e( 'Username', 'porto' ); ?> <span class="required">*</span></label>
                            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
                        </p>

                    <?php endif; ?>

                    <p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
                        <label for="reg_email"><?php _e( 'Email address', 'porto' ); ?> <span class="required">*</span></label>
                        <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
                    </p>
					
					<h2><?php _e( 'LOGIN INFORMATION', 'porto' ); ?></h2>
					
                    <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

                        <p class="woocommerce-FormRow woocommerce-FormRow--wide form-row <?php if( $porto_settings['reg-form-info'] == 'full' ){ echo "form-row-first"; } else { "form-row-wide"; } ?>">
                            <label for="reg_password"><?php _e( 'Password', 'porto' ); ?> <span class="required">*</span></label>
                            <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" />
                        </p>
						<?php if( isset( $porto_settings['reg-form-info'] ) && $porto_settings['reg-form-info'] == 'full' ): ?>
							<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-last">
								<label for="reg_password2"><?php _e( 'Confirm Password', 'porto' ); ?> <span class="required">*</span></label>
								<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="confirm_password" id="reg_password2" />
							</p>
						<?php endif; ?>

                    <?php endif; ?>

                    <!-- Spam Trap -->
                    <div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'porto' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" autocomplete="off" /></div>

                    <?php do_action( 'woocommerce_register_form' ); ?>
                    <?php do_action( 'register_form' ); ?>

                    <div class="woocommerce-FormRow form-row clearfix">
						<p class="required pull-right"><?php _e( '* Required Fields', 'porto' ); ?></p>
                    </div>
					
					<div class="woocommerce-FormRow form-row clearfix">
                        <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
						<?php if( isset( $_SERVER['HTTP_REFERER'] ) && $_SERVER['HTTP_REFERER'] ): ?>
							<a href="<?php echo get_permalink( woocommerce_get_page_id( 'myaccount' ) ); ?>" class="pull-left"><i class="fa fa-angle-double-left"></i> <?php _e( 'Back', 'porto' ); ?></a>
						<?php endif; ?>
                        <input type="submit" class="woocommerce-Button button pull-right" name="register" value="<?php esc_attr_e( 'Submit', 'porto' ); ?>" />
                    </div>
					
					<div class="clear"></div>
                    <?php do_action( 'woocommerce_register_form_end' ); ?>

                </form>
            </div>
        </div>