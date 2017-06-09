jQuery( document ).ready( function($) {

    jQuery('#fusion-builder-import-file').on('change', FusionPrepareUpload);

    jQuery('.fusion-builder-import-data').on('click', FusionUploadFiles);

    function FusionPrepareUpload(event) {
        if ( jQuery(this).val() !== '' ) {
            jQuery('.fusion-builder-import-data').prop('disabled', false);
        } else {
            jQuery('.fusion-builder-import-data').prop('disabled', true);
        }
        files = event.target.files;
    }

    function FusionUploadFiles(event) {
        if ( event ) {
            event.stopPropagation();
            event.preventDefault();
        }

        var data = new FormData();
        var input_field = jQuery('#fusion-builder-import-file');

        jQuery.each(files, function(key, value) {
            data.append(key, value);
        } );

        data.append('action', 'fusion_builder_importer');
        data.append('fusion_import_nonce', fusionBuilderConfig.fusion_import_nonce);

        jQuery.ajax( {
            type: "POST",
            url: fusionBuilderConfig.ajaxurl,
            dataType: 'json',
            contentType: false,
            processData: false,
            data: data,
            cache: false,
            complete : function( data ) {
                input_field.val('');
                jQuery('.fusion-builder-import-success').show();
            }
        } );
    }

} );
