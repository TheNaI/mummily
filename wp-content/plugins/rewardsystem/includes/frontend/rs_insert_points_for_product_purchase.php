<?php

if (($getnomineeidinmyaccount == '') && ($getnomineeidincheckout == '')) {    
    $event_slug = 'PPRP';
    if ($enabledisablemaxpoints == 'yes') {
        $this->check_point_restriction($restrictuserpoints, $productlevelrewardpointss, $pointsredeemed, $event_slug, $orderuserid, $nomineeid = '', $referrer_id = '', $productid, $variationid,$reasonindetail);
    } else {
        $equearnamt = RSPointExpiry::earning_conversion_settings($productlevelrewardpointss);        
        $valuestoinsert = array('pointstoinsert' => $productlevelrewardpointss,
            'pointsredeemed' => 0,
            'event_slug' => $event_slug,
            'equalearnamnt' => $equearnamt,
            'equalredeemamnt' => 0,
            'user_id' => $orderuserid,
            'referred_id' => '',
            'product_id' => $productid,
            'variation_id' => $variationid,
            'reasonindetail' => '',
            'nominee_id' => '',
            'nominee_points' => '',
            'totalearnedpoints' => $productlevelrewardpointss,
            'totalredeempoints' => 0);
        $this->total_points_management($valuestoinsert);
    }
} elseif (($getnomineeidinmyaccount != '' && $enablenomineeidinmyaccount == 'yes') && ($getnomineeidincheckout != '')) {
    $nomineeid = $orderuserid;
    $orderuserid = $getnomineeidincheckout;
    $this->insert_points_for_product($enabledisablemaxpoints, $order_id, $orderuserid, $nomineeid, $productlevelrewardpointss, $productid, $variationid);
} elseif (($getnomineeidinmyaccount != '' && $enablenomineeidinmyaccount == 'yes') && ($getnomineeidincheckout == '')) {
    $nomineeid = $orderuserid;
    $orderuserid = $getnomineeidinmyaccount;
    $this->insert_points_for_product($enabledisablemaxpoints, $order_id, $orderuserid, $nomineeid, $productlevelrewardpointss, $productid, $variationid);
} elseif (($getnomineeidinmyaccount != '' && $enablenomineeidinmyaccount == 'no') && ($getnomineeidincheckout != '')) {
    $nomineeid = $orderuserid;
    $orderuserid = $getnomineeidincheckout;
    $this->insert_points_for_product($enabledisablemaxpoints, $order_id, $orderuserid, $nomineeid, $productlevelrewardpointss, $productid, $variationid);
} elseif (($getnomineeidinmyaccount != '' && $enablenomineeidinmyaccount == 'no') && ($getnomineeidincheckout == '')) {
    $event_slug = 'PPRP';
    if ($enabledisablemaxpoints == 'yes') {
        $this->check_point_restriction($restrictuserpoints, $productlevelrewardpointss, $pointsredeemed, $event_slug, $orderuserid, $nomineeid = '', $referrer_id = '', $productid, $variationid,$reasonindetail);
    } else {
        $equearnamt = RSPointExpiry::earning_conversion_settings($productlevelrewardpointss);
        $valuestoinsert = array('pointstoinsert' => $productlevelrewardpointss,
            'pointsredeemed' => 0,
            'event_slug' => $event_slug,
            'equalearnamnt' => $equearnamt,
            'equalredeemamnt' => 0,
            'user_id' => $orderuserid,
            'referred_id' => '',
            'product_id' => $productid,
            'variation_id' => $variationid,
            'reasonindetail' => '',
            'nominee_id' => '',
            'nominee_points' => '',
            'totalearnedpoints' => $productlevelrewardpointss,
            'totalredeempoints' => 0);
        $this->total_points_management($valuestoinsert);
    }
} elseif (($getnomineeidinmyaccount == '') && ($getnomineeidincheckout != '')) {
    $nomineeid = $orderuserid;
    $orderuserid = $getnomineeidincheckout;
    $this->insert_points_for_product($enabledisablemaxpoints, $order_id, $orderuserid, $nomineeid, $productlevelrewardpointss, $productid, $variationid);
}