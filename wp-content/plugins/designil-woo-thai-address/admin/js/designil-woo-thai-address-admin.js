(function( $ ) {
	
	$(document).ready(function(){
		if ( $(".wta--debug").length > 0 ) {
			$(".wta--debug").click(function(){
				$(this).select();
			});
		}
	});	

})( jQuery );
