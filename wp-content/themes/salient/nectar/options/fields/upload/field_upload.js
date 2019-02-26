/*global jQuery, document, redux_upload, formfield:true, preview:true, tb_show, window, imgurl:true, tb_remove, $relid:true*/
/*
This is the uploader for wordpress starting from version 3.5
*/
jQuery(document).ready(function(){

            jQuery(".redux-opts-upload").click( function( event ) {
                var activeFileUploadContext = jQuery(this).parent();
                var relid = jQuery(this).attr('rel-id');
				var $that = jQuery(this);
				var elementID = jQuery(this).attr('id');
				
                event.preventDefault();

                // If the media frame already exists, reopen it.
                /*if ( typeof(custom_file_frame)!=="undefined" ) {
                    custom_file_frame.open();
                    return;
                }*/

                // if its not null, its broking custom_file_frame's onselect "activeFileUploadContext"
                custom_file_frame = null;

                // Create the media frame.
                custom_file_frame = wp.media.frames.customHeader = wp.media({
                    // Set the title of the modal.
                    title: jQuery(this).data("choose"),

                    // Tell the modal to show only images. Ignore if want ALL
                    library: {
                        type: 'image'
                    },
                    // Customize the submit button.
                    button: {
                        // Set the text of the button.
                        text: jQuery(this).data("update")
                    }
                });

                custom_file_frame.on( "select", function() {
                    // Grab the selected attachment.
                    var attachment = custom_file_frame.state().get("selection").first();

                    // Update value of the targetfield input with the attachment url.
                    jQuery('.redux-opts-screenshot',activeFileUploadContext).attr('src', attachment.attributes.url);
                    jQuery('#' + relid ).val(attachment.attributes.url).trigger('change');

                    jQuery('.redux-opts-upload',activeFileUploadContext).hide();
                    jQuery('.redux-opts-screenshot',activeFileUploadContext).show();
                    jQuery('.redux-opts-upload-remove',activeFileUploadContext).show();
                    
                    toggleParallaxOption();
                   
            });

            custom_file_frame.open();
        });

    jQuery(".redux-opts-upload-remove").click( function( event ) {
        var activeFileUploadContext = jQuery(this).parent();
        var relid = jQuery(this).attr('rel-id');

        event.preventDefault();

        jQuery('#' + relid).val('');
        jQuery(this).prev().fadeIn('slow');
        jQuery('.redux-opts-screenshot',activeFileUploadContext).fadeOut('slow');
        jQuery(this).fadeOut('slow');
        
         toggleParallaxOption();
    });
	
	
	 
	
	
	//media upload
	jQuery(".redux-opts-media-upload").click( function( event ) {
                var activeFileUploadContext = jQuery(this).parent();
                var relid = jQuery(this).attr('rel-id');

                event.preventDefault();

                // If the media frame already exists, reopen it.
                /*if ( typeof(custom_file_frame)!=="undefined" ) {
                    custom_file_frame.open();
                    return;
                }*/

                // if its not null, its broking custom_file_frame's onselect "activeFileUploadContext"
                custom_file_frame = null;

                // Create the media frame.
                custom_file_frame = wp.media.frames.customHeader = wp.media({
                    // Set the title of the modal.
                    title: jQuery(this).data("choose"),

                    // Tell the modal to show only images. Ignore if want ALL
                    library: {
                        type: 'video'
                    },
                    // Customize the submit button.
                    button: {
                        // Set the text of the button.
                        text: jQuery(this).data("update")
                    }
                });

                custom_file_frame.on( "select", function() {
                    // Grab the selected attachment.
                    var attachment = custom_file_frame.state().get("selection").first();

                    // Update value of the targetfield input with the attachment url.
                    jQuery('#' + relid ).val(attachment.attributes.url).trigger('change');
                    

				    jQuery('#_nectar_video_embed').trigger('keyup');
				    
                    jQuery('.redux-opts-media-upload',activeFileUploadContext).hide();
                    jQuery('.redux-opts-upload-media-remove',activeFileUploadContext).show();
            });

            custom_file_frame.open();
        });

    jQuery(".redux-opts-upload-media-remove").click( function( event ) {
        var activeFileUploadContext = jQuery(this).parent();
        var relid = jQuery(this).attr('rel-id');

        event.preventDefault();

        jQuery('#' + relid).val('');
        jQuery(this).prev().fadeIn('slow');
        jQuery('.redux-opts-screenshot',activeFileUploadContext).fadeOut('slow');
        jQuery(this).fadeOut('slow');
    });
    
    
    //only show parallax when using bg image
    function toggleParallaxOption(){
    	if(jQuery('#_nectar_header_bg').length > 0){
	    	if(jQuery('#_nectar_header_bg').attr('value').length > 0 || jQuery('#_nectar_header_bg_color').length > 0 && jQuery('#_nectar_header_bg_color').attr('value').length > 0){
	    		jQuery('#_nectar_header_parallax').parents('tr').show();
	    	} else {
	    		jQuery('#_nectar_header_parallax').parents('tr').hide();
	    		jQuery('#_nectar_header_parallax').prop('checked', false);
	    	}
    	}
    }
	
});
