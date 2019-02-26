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


/*gallery js included below*/
/* global redux_change, wp */

/*global redux_change, redux*/



(function( $ ) {
    "use strict";



    $( document ).ready(
        function() {
            //redux.field_objects.gallery.init();
        }
    );

   


       // if ( !selector ) {
         var selector = $( document ).find( '.redux-container-gallery' );
         //console.log( selector);
       // }

        $( selector ).each(

            function() {

                var el = $( this );
                var parent = el;
               /* if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;

                }*/
                // When the user clicks on the Add/Edit gallery button, we need to display the gallery editing
                el.on(
                    {
                        click: function( event ) {

                            var current_gallery = $( this ).closest( 'fieldset' );

                            if ( event.currentTarget.id === 'clear-gallery' ) {
                                //remove value from input

                                var rmVal = current_gallery.find( '.gallery_values' ).val( '' );

                                //remove preview images
                                current_gallery.find( ".screenshot" ).html( "" );

                                return;

                            }
                         
                            // Make sure the media gallery API exists
                            if ( typeof wp === 'undefined' || !wp.media || !wp.media.gallery ) {
                                return;
                            }
                            event.preventDefault();

                            // Activate the media editor
                            var $$ = $( this );

                            var val = current_gallery.find( '.gallery_values' ).val();
                            var final;

                            if ( !val ) {
                                final = '[gallery ids="0"]';
                            } else {
                                final = '[gallery ids="' + val + '"]';
                            }

                            var frame = wp.media.gallery.edit( final );
                            
                            if($('body.particle-edit').length > 0) {
	                            //edit text
	                            $(frame.title.view.el).find('.media-frame-title h1').text('Edit Particle Shapes');
	                            $(frame.title.view.el).find('.media-frame-menu .media-menu a:contains(Add to Gallery)').text('Add to Particle Shapes');
	                            $(frame.title.view.el).find('.media-frame-menu .media-menu a:contains(Edit Gallery)').text('Edit');
	                            $(frame.title.view.el).find('.media-frame-menu .media-menu a:contains(Cancel Gallery)').text('Cancel');
	                            $(frame.title.view.el).find('.media-toolbar-primary a:contains(Update gallery)').text('Update Particle Shapes');
	                            setTimeout(function(){ $(frame.title.view.el).find('.media-toolbar-primary a:contains(Update gallery)').text('Update Particle Shapes'); },400);
	                            var $cssString = '.collection-settings, input[type="text"].describe, .attachment-details label[data-setting="alt"], .attachment-details label[data-setting="description"] { display: none!important;} .compat-item .label {max-width: 30%; } p.help { font-size: 12px; font-style: normal; color: #888; } .compat-item tr.compat-field-shape-bg-color, .compat-item tr.compat-field-shape-color-alpha, .compat-item tr.compat-field-shape-color-mapping, .compat-item tr.compat-field-shape-particle-color,  .compat-item tr.compat-field-shape-density, .compat-item tr.compat-field-shape-max-particle-size { display: block;} ';
	                            $('style#remove-gallery-els').remove();
	
	                            var head = document.head || document.getElementsByTagName('head')[0];
	                            var style = document.createElement('style');
	
	                            style.type = 'text/css';
	                            style.id = 'remove-gallery-els';
	                         
	                            if (style.styleSheet){
	                              style.styleSheet.cssText = $cssString;
	                            } else {
	                              style.appendChild(document.createTextNode($cssString));
	                            }
	                            head.appendChild(style);
								
						
	                           $('.media-menu-item:contains(Add to Particle Shapes)').on('click',function(){
	                             $ (frame.title.view.el).find('.media-frame-title h1, .media-frame-toolbar .media-button-insert').text('Add to Particle Shapes');
	                           });
	                           $('.media-menu-item:contains(Edit Particle Shapes)').on('click',function(){
	                             $ (frame.title.view.el).find('.media-frame-title h1').text('Edit Particle Shapes');
	                             $('.media-frame-toolbar .media-button-insert').text('Update Particle Shapes');
	                           });
	                           $('body').on('click','.media-frame:not(.hide-router) .attachments-browser li.attachment .attachment-preview',function(){
	                  
	                             $(frame.title.view.el).find('.media-frame-toolbar .media-button-insert').text('Add to Particle Shapes');
	                             $(frame.title.view.el).find('.media-frame-title h1').text('Add to Particle Shapes');
	                           });
	                           $('body').on('mousedown','.media-toolbar-primary .button:contains(Add to Particle Shapes)',function(){
	                                    setTimeout(function(){
	                                          $(frame.title.view.el).find('.media-frame-toolbar .media-button-insert').text('Update Particle Shapes');
	                                          $(frame.title.view.el).find('.media-frame-title h1').text('Edit Particle Shapes');
	                                    },200)
	                               
	                           });
                           }

                            // When the gallery-edit state is updated, copy the attachment ids across
                            frame.state( 'gallery-edit' ).on(
                                'update', function( selection ) {

                                    //clear screenshot div so we can append new selected images
                                    current_gallery.find( ".screenshot" ).html( "" );
									
									//remove temp stylesheet that shows extra fields
									 $('style#remove-gallery-els').remove();
									 $('body').removeClass('particle-edit');
                                    var element, preview_html = "", preview_img;
                                    var ids = selection.models.map(
                                        function( e ) {
                                            element = e.toJSON();
                                            preview_img = typeof element.sizes.thumbnail !== 'undefined' ? element.sizes.thumbnail.url : element.url;
                                            preview_html = "<img class='redux-option-image' src='" + preview_img + "'  />";
                                            current_gallery.find( ".screenshot" ).append( preview_html );

                                            return e.id;
                                        }
                                    );

                                    current_gallery.find( '.gallery_values' ).val( ids.join( ',' ) );
                                    //redux_change( current_gallery.find( '.gallery_values' ) );

                                }
                            );

                            return false;
                        }
                    }, '.gallery-attachments'
                );
            }
        );

    
})( jQuery );
