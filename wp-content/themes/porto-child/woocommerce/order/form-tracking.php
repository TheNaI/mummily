<?php
/**
 * Order tracking form
 *
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

?>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <form action="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" method="post" class="track_order featured-box align-left m-t-none">
            <div class="box-content">
                <p><?php _e( 'หากต้องการติดตามคำสั่งซื้อโปรดป้อนรหัสคำสั่งซื้อของคุณในช่องด้านล่างและกดปุ่ม "ติดตาม" ข้อมูลนี้ได้รับจากคุณในใบเสร็จรับเงินและในอีเมลยืนยันที่คุณควรได้รับ', 'porto' ); ?></p>

                <p class="form-row"><label for="orderid"><?php _e( 'เลข Order ID', 'porto' ); ?></label> <input class="input-text" type="text" name="orderid" id="orderid" placeholder="<?php esc_attr_e( 'คุณสามารถหาเลข Order ID ได้ในอีเมลของคุณ', 'porto' ); ?>" /></p>
                <p class="form-row"><label for="order_email"><?php _e( 'Email ชำระเงิน', 'porto' ); ?></label> <input class="input-text" type="text" name="order_email" id="order_email" placeholder="<?php esc_attr_e( 'อีเมลที่คุณใช้ในการสั่งซื้อสินค้า', 'porto' ); ?>" /></p>
                <div class="clear"></div>

                <p class="form-row clearfix"><input type="submit" class="button btn-lg pt-right" name="track" value="<?php esc_attr_e( 'Track', 'woocommerce' ); ?>" /></p>
                <?php wp_nonce_field( 'woocommerce-order_tracking' ); ?>
            </div>
        </form>
    </div>
</div>
