<?php

/**
 * Check woo-commerce plugin is installed and activated or not.
 * @return bool
 */
if ( ! function_exists( 'is_woocommerce_activated' ) ) {
    function is_woocommerce_activated() {
        if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
    }
}

/**
 * Get array of banks.
 * @param array $accounts
 * @return array
 */
if( !function_exists('seed_confirm_get_banks') ){
    function seed_confirm_get_banks($accounts){
        $thai_accounts = array();

        foreach( $accounts as $_account ) {
            $is_thaibank = false;
            $logo = '';

            if( ( false !==  mb_strpos( trim( $_account['bank_name'] ) , 'กสิกร' ) )
                || false !== stripos( trim( $_account['bank_name'] ) , 'kbank' )
                || false !== stripos( trim( $_account['bank_name'] ) , 'kasikorn' ) ) {
                $is_thaibank = true;
                $logo = 'kbank';
            } else if( ( false !== mb_strpos( trim( $_account['bank_name'] ) , 'กรุงเทพ' ) )
                || false !== stripos( trim( $_account['bank_name'] ) , 'bbl' )
                || false !== stripos( trim( $_account['bank_name'] ) , 'bualuang' ) ) {
                $is_thaibank = true;
                $logo = 'bbl';
            } else if( ( false !== mb_strpos( trim( $_account['bank_name'] ) , 'กรุงไทย' ) )
                || false !== stripos( trim( $_account['bank_name'] ) , 'ktb' )
                || false !== stripos( trim( $_account['bank_name'] ) , 'krungthai' ) ) {
                $is_thaibank = true;
                $logo = 'ktb';
            } else if( ( false !== mb_strpos( trim( $_account['bank_name'] ) , 'ทหารไทย' ) )
                || false !== stripos( trim( $_account['bank_name'] ) , 'tmb' )
                || false !== stripos( trim( $_account['bank_name'] ) , 'thai military' ) ) {
                $is_thaibank = true;
                $logo = 'tmb';
            } else if( ( false !== mb_strpos( trim( $_account['bank_name'] ) , 'ไทยพาณิชย์' ) )
                || false !== stripos( trim( $_account['bank_name'] ) , 'scb' )
                || false !== stripos( trim( $_account['bank_name'] ) , 'siam commercial' ) ) {
                $is_thaibank = true;
                $logo = 'scb';
            } else if( ( false !== mb_strpos( trim( $_account['bank_name'] ) , 'กรุงศรี' ) )
                || false !== stripos( trim( $_account['bank_name'] ) , 'krungsri' ) ) {
                $is_thaibank = true;
                $logo = 'krungsri';
            } else if( ( false !== mb_strpos( trim( $_account['bank_name'] ) , 'ออมสิน' ) )
                || false !== stripos( trim( $_account['bank_name'] ) , 'gsb' ) ) {
                $is_thaibank = true;
                $logo = 'gsb';
            } else if( ( false !== mb_strpos( trim( $_account['bank_name'] ) , 'ธนชาต' ) )
                || false !== stripos( trim( $_account['bank_name'] ) , 'tbank' ) ) {
                $is_thaibank = true;
                $logo = 'tbank';
            } else if( ( false !== mb_strpos( trim( $_account['bank_name'] ) , 'ยูโอบี' ) )
                || false !== stripos( trim( $_account['bank_name'] ) , 'uob' ) ) {
                $is_thaibank = true;
                $logo = 'uob';
            } else if( ( false !== mb_strpos( trim( $_account['bank_name'] ) , 'อิสลาม' ) )
                || false !== stripos( trim( $_account['bank_name'] ) , 'islamic' )
                || false !== stripos( trim( $_account['bank_name'] ) , 'ibank' ) ) {
                $is_thaibank = true;
                $logo = 'ibank';
            } else if( ( false !== mb_strpos( trim( $_account['bank_name'] ) , 'อาคารสงเคราะห์' ) )
                || false !== mb_strpos( trim( $_account['bank_name'] ) , 'ธอส' )
                || false !== stripos( trim( $_account['bank_name'] ) , 'ghb' ) ) {
                $is_thaibank = true;
                $logo = 'ghb';
            }

            $_account['is_thaibank'] = $is_thaibank;

            if( $logo !== '' )
                $_account['logo'] = plugins_url( 'img/'.$logo.'.png', __FILE__ );

            $thai_accounts[] = $_account;
        }

        return $thai_accounts;
    }
}

/**
 * Use for generate unique file name.
 * Difficult to predict.
 * Only slip image that upload through seed-confirm.
 * @param $dir
 * @param $name
 * @param $ext
 * @return (string) uniq name
 */
if( !function_exists('seed_unique_filename') ){
    function seed_unique_filename($dir, $name, $ext ) {
        return 'slip-'.md5( $dir.$name.time() ).$ext;
    }
}


