(function( $ ) {
  $(document).ready(function() {

    /* BILLING */
    if ( $('#billing_sub_city, #billing_city, #billing_postcode').length > 0 ) {

      var billing_country = $("#billing_country").val();
      if ( billing_country == 'TH' ) {
        $.Thailand({
          database: wta.url + 'db/db.json', // path หรือ url ไปยัง database
          type: 'billing',
          $district: $('#billing_sub_city'), // input ของตำบล
          $amphoe: $('#billing_city'), // input ของอำเภอ
          $zipcode: $('#billing_postcode'), // input ของรหัสไปรษณีย์
        });
      }

    }

    $("#billing_country").on('select2:select', function(e){
      var billing_country = $('#billing_country').val();

      if ( billing_country == 'TH' ) {

          $.Thailand({
            database: wta.url + 'db/db.json', // path หรือ url ไปยัง database
            type: 'billing',
            $district: $('#billing_sub_city'), // input ของตำบล
            $amphoe: $('#billing_city'), // input ของอำเภอ
            $zipcode: $('#billing_postcode'), // input ของรหัสไปรษณีย์
          });
        
      } else {
        $('#billing_sub_city, #billing_city, #billing_postcode').typeahead('destroy');
        $('#billing_sub_city, #billing_city, #billing_postcode').val('');
      }
    });


    /* SHIPPING */
    if ( $('#shipping_sub_city, #shipping_city, #shipping_postcode').length > 0 ) {
      var shipping_country = $("#shipping_country").val();
      if ( shipping_country == 'TH' ) {
        $.Thailand({
          database: wta.url + 'db/db.json', // path หรือ url ไปยัง database
          type: 'shipping',
          $district: $('#shipping_sub_city'), // input ของตำบล
          $amphoe: $('#shipping_city'), // input ของอำเภอ
          $zipcode: $('#shipping_postcode'), // input ของรหัสไปรษณีย์
        });
      }
    }

    $("#shipping_country").on('select2:select', function(e){
      var shipping_country = $('#shipping_country').val();

      if ( shipping_country == 'TH' ) {

        $.Thailand({
          database: wta.url + 'db/db.json', // path หรือ url ไปยัง database
          type: 'shipping',
          $district: $('#shipping_sub_city'), // input ของตำบล
          $amphoe: $('#shipping_city'), // input ของอำเภอ
          $zipcode: $('#shipping_postcode'), // input ของรหัสไปรษณีย์
        });

      } else {
        $('#shipping_sub_city, #shipping_city, #shipping_postcode').typeahead('destroy');
        $('#shipping_sub_city, #shipping_city, #shipping_postcode').val('');
      }
    });

    if ( $('.woocommerce-Address').length ) {

      $('.woocommerce-Address').each(function(k, v){
        var address = $(this).find('address')
        var txt = address.html().split('}}{{');
        if ( k == 0 ) {
          // billing
          text_1 = txt[0].split('{{');
          text_1 = text_1[0] + text_1[1];
          text_2 = txt[1].split('}}');
          text_2 = text_2[1];
          address.html(text_1 + text_2);
        } else {
          // shipping
          text_1 = txt[0].split('{{');
          text_1 = text_1[0];
          text_2 = txt[1].split('}}');
          text_2 = text_2[0] + text_2[1];
          address.html(text_1 + text_2);
        }
        // console.log(address);
      });

    }

  }); // End document.ready
})( jQuery );
