jQuery( window ).load( function() {

	if ( 'function' === typeof jQuery.fn.equalHeights ) {
		// Equal Heights Elements
		jQuery( '.fusion-events-shortcode' ).each( function() {
			jQuery( this ).find( '.fusion-events-meta' ).equalHeights();
		});

		jQuery( window ).on( 'resize', function() {
			jQuery( '.fusion-events-shortcode' ).each( function() {
				jQuery( this ).find( '.fusion-events-meta' ).equalHeights();
			});
		});
	}
});
