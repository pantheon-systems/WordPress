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
         var selector = $( document ).find( '.redux-container-gallery:visible' );
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
                                            preview_html = "<img class='redux-option-image' src='" + preview_img + "' />";
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