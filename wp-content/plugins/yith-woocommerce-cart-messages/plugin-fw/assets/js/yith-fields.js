jQuery( function ( $ ) {

    var $datepicker   = $( '.yith-plugin-fw-datepicker' ),
        $colorpicker  = $( '.yith-plugin-fw-colorpicker' ),
        $upload       = {
            imgPreviewHandler  : '.yith-plugin-fw-upload-img-preview',
            uploadButtonHandler: '.yith-plugin-fw-upload-button',
            imgUrlHandler      : '.yith-plugin-fw-upload-img-url',
            resetButtonHandler : '.yith-plugin-fw-upload-button-reset',
            imgUrl             : $( '.yith-plugin-fw-upload-img-url' )
        },
        $wpAddMedia   = $( '.add_media' ),
        $imageGallery = {
            sliderWrapper: $( '.yith-plugin-fw .image-gallery ul.slides-wrapper' ),
            buttonHandler: '.yith-plugin-fw .image-gallery-button'
        },
        $onoff        = $( '.yith-plugin-fw-onoff-container span' ),
        $sidebars     = $( '.yith-plugin-fw-sidebar-layout' ),
        $slider       = $( '.yith-plugin-fw .yith-plugin-fw-slider-container .ui-slider-horizontal' ),
        $codemirror   = $( '.codemirror' ),
        $icons        = $( '.yit-icons-manager-wrapper' );

    /* Datepicker */
    $datepicker.each( function () {
        var args = $( this ).data();
        $( this ).datepicker( args );
    } );

    /* Colorpicker */
    $colorpicker.wpColorPicker( {
                                    clear: function () {
                                        var input = $( this );
                                        input.val( input.data( 'default-color' ) );
                                        input.change();
                                    }
                                } );
    $colorpicker.each( function () {
        var select_label = $( this ).data( 'variations-label' );
        $( this ).parent().parent().find( 'a.wp-color-result' ).attr( 'title', select_label );
    } );

    /* Upload */
    if ( typeof wp !== 'undefined' && typeof wp.media !== 'undefined' ) {
        var _custom_media = true;
        // preview
        $upload.imgUrl.change( function () {
            var url     = $( this ).val(),
                re      = new RegExp( "(http|ftp|https)://[a-zA-Z0-9@?^=%&amp;:/~+#-_.]*.(gif|jpg|jpeg|png|ico)" ),
                preview = $( this ).parent().find( $upload.imgPreviewHandler ).first();

            if ( preview.length < 1 )
                preview = $( this ).parent().parent().find( $upload.imgPreviewHandler ).first();

            if ( re.test( url ) ) {
                preview.html( '<img src="' + url + '" style="max-width:100px; max-height:100px;" />' );
            } else {
                preview.html( '' );
            }
        } ).trigger( 'change' );

        $( document ).on( 'click', $upload.uploadButtonHandler, function ( e ) {
            e.preventDefault();

            var t  = $( this ),
                custom_uploader,
                id = t.attr( 'id' ).replace( /-button$/, '' );

            //If the uploader object has already been created, reopen the dialog
            if ( custom_uploader ) {
                custom_uploader.open();
                return;
            }

            var custom_uploader_states = [
                // Main states.
                new wp.media.controller.Library( {
                                                     library   : wp.media.query(),
                                                     multiple  : false,
                                                     title     : 'Choose Image',
                                                     priority  : 20,
                                                     filterable: 'uploaded'
                                                 } )
            ];

            // Create the media frame.
            custom_uploader = wp.media.frames.downloadable_file = wp.media( {
                                                                                // Set the title of the modal.
                                                                                title   : 'Choose Image',
                                                                                library : {
                                                                                    type: ''
                                                                                },
                                                                                button  : {
                                                                                    text: 'Choose Image'
                                                                                },
                                                                                multiple: false,
                                                                                states  : custom_uploader_states
                                                                            } );

            //When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on( 'select', function () {
                var attachment = custom_uploader.state().get( 'selection' ).first().toJSON();

                $( "#" + id ).val( attachment.url );
                // Save the id of the selected element to an element which name is the same with a suffix "-yith-attachment-id"
                if ( $( "#" + id + "-yith-attachment-id" ) ) {
                    $( "#" + id + "-yith-attachment-id" ).val( attachment.id );
                }
                $upload.imgUrl.trigger( 'change' );
            } );

            //Open the uploader dialog
            custom_uploader.open();
        } );

        $( document ).on( 'click', $upload.resetButtonHandler, function ( e ) {
            var t             = $( this ),
                id            = t.attr( 'id' ),
                input_id      = t.attr( 'id' ).replace( /-button-reset$/, '' ),
                default_value = $( '#' + id ).data( 'default' );

            $( "#" + input_id ).val( default_value );
            $upload.imgUrl.trigger( 'change' );
        } );
    }

    $wpAddMedia.on( 'click', function () {
        _custom_media = false;
    } );

    /* Image Gallery */
    if ( typeof wp !== 'undefined' && typeof wp.media !== 'undefined' ) {
        $( document ).on( 'click', $imageGallery.buttonHandler, function ( e ) {
            var $t                      = $( this ),
                $container              = $t.closest( '.image-gallery' ),
                $image_gallery_ids      = $container.find( '.image_gallery_ids' ),
                attachment_ids          = $image_gallery_ids.val(),
                $gallery_images_wrapper = $container.find( 'ul.slides-wrapper' );

            // Create the media frame.
            var image_gallery_frame = wp.media.frames.image_gallery = wp.media( {
                                                                                    // Set the title of the modal.
                                                                                    title : $t.data( 'choose' ),
                                                                                    button: {
                                                                                        text: $t.data( 'update' )
                                                                                    },
                                                                                    states: [
                                                                                        new wp.media.controller.Library( {
                                                                                                                             title     : $t.data( 'choose' ),
                                                                                                                             filterable: 'all',
                                                                                                                             multiple  : true
                                                                                                                         } )
                                                                                    ]
                                                                                } );

            // When an image is selected, run a callback.
            image_gallery_frame.on( 'select', function () {
                var selection = image_gallery_frame.state().get( 'selection' );
                selection.map( function ( attachment ) {
                    attachment = attachment.toJSON();

                    if ( attachment.id ) {
                        attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;
                        $gallery_images_wrapper.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment.sizes.thumbnail.url + '"/><ul class="actions"><li><a href="#" class="delete" title="' + $t.data( 'delete' ) + '">x</a></li></ul></li>' );
                    }
                } );

                $image_gallery_ids.val( attachment_ids );
            } );

            image_gallery_frame.open();

        } );

        // Image ordering
        $imageGallery.sliderWrapper.each( function () {
            var $t = $( this );
            $t.sortable( {
                             items               : 'li.image',
                             cursor              : 'move',
                             scrollSensitivity   : 40,
                             forcePlaceholderSize: true,
                             forceHelperSize     : false,
                             helper              : 'clone',
                             opacity             : 0.65,
                             start               : function ( event, ui ) {
                                 ui.item.css( 'background-color', '#f6f6f6' );
                             },
                             stop                : function ( event, ui ) {
                                 ui.item.removeAttr( 'style' );
                             },
                             update              : function ( event, ui ) {
                                 var attachment_ids = '';

                                 $t.find( 'li.image' ).css( 'cursor', 'default' ).each( function () {
                                     var attachment_id = $( this ).attr( 'data-attachment_id' );
                                     attachment_ids = attachment_ids + attachment_id + ',';
                                 } );

                                 $t.closest( '.image-gallery' ).find( '.image_gallery_ids' ).val( attachment_ids );
                             }
                         } );
        } );

        // Remove images
        $imageGallery.sliderWrapper.on( 'click', 'a.delete', function () {
            var $wrapper           = $( this ).closest( '.image-gallery' ),
                $gallery           = $( this ).closest( '.image-gallery ul.slides-wrapper' ),
                $image_gallery_ids = $wrapper.find( '.image_gallery_ids' ),
                attachment_ids     = '';

            $( this ).closest( 'li.image' ).remove();

            $gallery.find( 'li.image' ).css( 'cursor', 'default' ).each( function () {
                var attachment_id = $( this ).attr( 'data-attachment_id' );
                attachment_ids = attachment_ids + attachment_id + ',';
            } );

            $image_gallery_ids.val( attachment_ids );
        } );
    }

    /* on-off */
    $onoff.on( 'click', function () {
        var input   = $( this ).prev( 'input' ),
            checked = input.prop( 'checked' );

        if ( checked ) {
            input.prop( 'checked', false ).attr( 'value', 'no' ).removeClass( 'onoffchecked' );
        } else {
            input.prop( 'checked', true ).attr( 'value', 'yes' ).addClass( 'onoffchecked' );
        }

        input.change();
    } );

    /* Sidebars */
    $sidebars.each( function () {
        var $images = $( this ).find( 'img' );
        $images.on( 'click', function () {
            var $container = $( this ).closest( '.yith-plugin-fw-sidebar-layout' ),
                $left      = $container.find( '.yith-plugin-fw-sidebar-layout-sidebar-left-container' ),
                $right     = $container.find( '.yith-plugin-fw-sidebar-layout-sidebar-right-container' ),
                type       = $( this ).data( 'type' );

            $( this ).parent().children( ':radio' ).attr( 'checked', false );
            $( this ).prev( ':radio' ).attr( 'checked', true );

            if ( typeof type != 'undefined' ) {
                switch ( type ) {
                    case 'left':
                        $left.show();
                        $right.hide();
                        break;
                    case 'right':
                        $right.show();
                        $left.hide();
                        break;
                    case 'double':
                        $left.show();
                        $right.show();
                        break;
                    default:
                        $left.hide();
                        $right.hide();
                        break;
                }
            }
        } );
    } );

    /* Slider */
    $slider.each( function () {
        var val      = $( this ).data( 'val' ),
            minValue = $( this ).data( 'min' ),
            maxValue = $( this ).data( 'max' ),
            step     = $( this ).data( 'step' ),
            labels   = $( this ).data( 'labels' );

        $( this ).slider( {
                              value: val,
                              min  : minValue,
                              max  : maxValue,
                              range: 'min',
                              step : step,

                              create: function () {
                                  $( this ).find( '.ui-slider-handle' ).text( $( this ).slider( "value" ) );
                              },


                              slide: function ( event, ui ) {
                                  $( this ).find( 'input' ).val( ui.value );
                                  $( this ).find( '.ui-slider-handle' ).text( ui.value );
                                  $( this ).siblings( '.feedback' ).find( 'strong' ).text( ui.value + labels );
                              }
                          } );
    } );

    /* codemirror */
    $codemirror.each( function ( i, v ) {
        var editor = CodeMirror.fromTextArea( v, {
            lineNumbers            : 1,
            mode                   : 'javascript',
            showCursorWhenSelecting: true
        } );

        $( v ).data( 'codemirrorInstance', editor );
    } );

    /* Select All - Deselect All */
    $( document ).on( 'click', '.yith-plugin-fw-select-all', function () {
        var $targetSelect = $( '#' + $( this ).data( 'select-id' ) );
        $targetSelect.find( 'option' ).prop( 'selected', true ).trigger( 'change' );
    } );

    $( document ).on( 'click', '.yith-plugin-fw-deselect-all', function () {
        var $targetSelect = $( '#' + $( this ).data( 'select-id' ) );
        $targetSelect.find( 'option' ).prop( 'selected', false ).trigger( 'change' );
    } );


    $icons.each( function () {
        var $container = $( this ),
            $preview   = $container.find( '.yit-icons-manager-icon-preview' ).first(),
            $text      = $container.find( '.yit-icons-manager-icon-text' );

        $container.on( 'click', '.yit-icons-manager-list li', function ( event ) {
            var $target = $( event.target ).closest( 'li' ),
                font    = $target.data( 'font' ),
                icon    = $target.data( 'icon' ),
                key     = $target.data( 'key' ),
                name    = $target.data( 'name' );

            $preview.attr( 'data-font', font );
            $preview.attr( 'data-icon', icon );
            $preview.attr( 'data-key', key );
            $preview.attr( 'data-name', name );

            $text.val( font + ':' + name );

            $container.find( '.yit-icons-manager-list li' ).removeClass( 'active' );
            $target.addClass( 'active' );
        } );

        $container.on( 'click', '.yit-icons-manager-action-set-default', function () {
            $container.find( '.yit-icons-manager-list li.default' ).trigger( 'click' );
        } );
    } );

    /** Select Images */
    $( document ).on( 'click', '.yith-plugin-fw-select-images__item', function () {
        var item    = $( this ),
            key     = item.data( 'key' ),
            wrapper = item.closest( '.yith-plugin-fw-select-images__wrapper' ),
            items   = wrapper.find( '.yith-plugin-fw-select-images__item' ),
            select  = wrapper.find( 'select' ).first();

        if ( select.length ) {
            select.val( key );
            items.removeClass( 'yith-plugin-fw-select-images__item--selected' );
            item.addClass( 'yith-plugin-fw-select-images__item--selected' );
        }
    } );
} );
