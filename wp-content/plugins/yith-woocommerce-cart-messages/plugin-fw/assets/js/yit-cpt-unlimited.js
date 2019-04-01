/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

(function($) {
    "use strict";
    // Author code here

    // open media box
    $('.wrap h1, .wrap h2').on( 'click', 'a.multi-uploader', function(event){
        event.preventDefault();

        var file_frame,
            button = $(this),
            selected = false;

        // spinner
        button.next('span.spinner').css( 'display', 'inline-block' );

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: button.data( 'uploader_title' ),
            button: {
                text: button.data( 'uploader_button_text' )
            },
            library: {
                type: 'image'
            },
            multiple: true  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            var selection = file_frame.state().get('selection'),
                images = [];

            selection.map( function( attachment ) {
                attachment = attachment.toJSON();

                // Do something with attachment.id and/or attachment.url here
                images.push( { id: attachment.id, url: attachment.url, title: attachment.title } );
            });

            // make AJAX request
            $.post( ajaxurl, {
                images: images,
                post_type: typenow,
                action: 'yit_cptu_multiuploader'
            }, function( data ){
                location.reload();
            });

            button.next('span.spinner').css( 'display', 'inline-block' );

            // flag
            selected = true;
        });

        // when close
        file_frame.on( 'close', function() {
            if ( ! selected ) button.next('span.spinner').hide();
        });

        // Finally, open the modal
        file_frame.open();
    });

})(jQuery);