/**
 * MIMO
 * -
 *
 * Licensed under the GPLv2+ license.
 */

window.Mimo = window.Mimo || {};

( function( window, document, $, plugin ) {

	var input_date_shipped = $('#mimo_date_shipped')
			provider_sortable = $('#provider-sortable')
			provider_header = '#provider-sortable h3'
			button_add_provider	= $('#add-provider')
			button_close_provider = '.close-provider'
			add_tracking_url = '.add_tracking_url'
			button_update_provider = '.update-provider'
			button_delete_provider = '.delete-provider'
			input_provider_name = '.provider-name'
			shipment_tracking_input = $('.mimo-field')
			button_tracking = $('#mimo-shipment-tracking button')

	plugin.init = function() {

		if ( input_date_shipped.length ) {

			input_date_shipped.datepicker({
				showButtonPanel: true,
			});

		}

    $(document).on('click', provider_header, function(e) {

      $(this).next().slideToggle();
      $(this).toggleClass('is-active');

    });

    $(document).on('click', button_close_provider, function(e) {

      $(this).closest('.list-item-inner').slideUp(400);

    });

    $(document).on('keypress, keydown, keyup', input_provider_name, function(e) {

      $(this).closest('.list_item').find('h3').text($(this).val())

    });


    provider_sortable.sortable({
      axis: "y",
      handle: "h3",
      items: '.list_item',
      placeholder: 'ui-state-highlight',
      update: function( event, ui ) {
        $.post( MIMO.ajaxurl, $(this).sortable('serialize') + '&action=mimo_update_order_provider', function( data ) {
          //alert(data)
        });
      }
    });

    $(document).on('click', button_update_provider, function(e) {

      e.preventDefault();

		  var $item = $(this).closest('.list_item');
		  $(this).next().addClass('is-active');

		  $.ajax({
		    type: 'POST',
		    url: MIMO.ajaxurl,
		    data: $item.find('input').serialize() + '&action=mimo_update_provider',
		    success: function(data, textStatus, XMLHttpRequest) {
		      //alert(data);
		      $item.find('.is-active').removeClass('is-active');
		    }

		  });


    });

    $(document).on('click', button_delete_provider, function(e) {

      e.preventDefault();

		  var $item = $(this).closest('.list_item');
		  $item.find('.spinner').addClass('is-active');

		  $.ajax({
		    type: 'POST',
		    url: MIMO.ajaxurl,
		    data: $item.find('input').serialize() + '&action=mimo_delete_provider',
		    success: function(data, textStatus, XMLHttpRequest) {
		      $item.fadeOut(200, function() {
		        $(this).remove();
		      });
		      $item.find('.spinner').removeClass('is-active');
		    }
		  });

    });


    button_add_provider.click(function (e) {

      e.preventDefault();

      $(this).next().addClass('is-active');

      provider_sortable.append(
        '<div id="list_item_9999"  class="list_item">\
          <h3>' + MIMO.provider_name + '</h3>\
          <div class="list-item-inner" style="display:block">\
            <label for="">\
              ' + MIMO.provider_name + '\
              <input type="text" class="widefat provider-name" name="name" id="name" value="">\
            </label>\
            <label for="">\
              '+ MIMO.tracking_url +'\
              <input type="text" class="widefat" name="tracking_url" id="tracking_url" value="">\
            </label>\
            <label for="">\
              <input type="checkbox" class="add_tracking_url" name="add_tracking_url" value="1" checked="checked">\
              '+ MIMO.add_tracking_url +'\
            </label>\
            <input type="hidden" class="key" name="key" value="">\
            <div class="control-actions">\
              <div class="alignleft">\
                <a class="widget-control-remove delete-provider" href="#">'+ MIMO.delete +'</a> |\
                <a class="close-provider" href="#">'+ MIMO.close +'</a>\
              </div>\
              <div class="alignright">\
                <input type="submit" class="button button-primary right update-provider" value="'+ MIMO.update +'">\
                <span class="spinner"></span>\
              </div>\
              <br class="clear">\
            </div>\
          </div>\
        </div>'
      );

      provider_sortable.sortable('refresh');

      $.ajax({
        type: 'POST',
        url: MIMO.ajaxurl,
        data: provider_sortable.sortable('serialize') + '&action=mimo_add_provider',
        success: function(data, textStatus, XMLHttpRequest) {

          $('.list_item').last().find('.key').val(data);
          $('#list_item_9999').attr( 'id', 'list_item_'+ data );
          $('#add-provider').next().removeClass('is-active');

        }

      });

    });



    button_tracking.on('click', function(e) {

      e.preventDefault();

      var $this = $(this);

      $this.closest('.control-actions').find('.spinner').addClass('is-active');

      $.ajax({
        type: 'POST',
        url: MIMO.ajaxurl,
        data: shipment_tracking_input.serialize() +'&mimo_shipment_tracking_nonce=' + $('#mimo_shipment_tracking_nonce').val() + '&action=mimo_send_tracking',
        success: function(data, textStatus, XMLHttpRequest) {

          $this.closest('.control-actions').find('.spinner').removeClass('is-active');

          if ( data.errors == true ){
            alert( data.msg );
          }else{
            $('select#order_status').val("wc-completed").trigger('change');
          }

          $( '#mimo-shipment-tracking .tracking-link' ).html( data.tracking_link );

        },

      });

    });


	};



	$( plugin.init );



}( window, document, jQuery, window.Mimo ) );
