var WCML_Pointer = WCML_Pointer || {};

( function( $ ) {
    WCML_Pointer.openPointer = function( trigger ) {
        var content = $( '#' + trigger.data( 'wcml-open-pointer' ) );
        $( '.wcml-information-active-pointer' ).pointer( 'close' );

        if( trigger.length ) {
            trigger.addClass( 'wcml-information-active-pointer' );
            trigger.pointer( {
                pointerClass : 'wcml-information-pointer',
                content: content.html(),
                position: {
                    edge: 'bottom',
                    align: 'right'
                },
                buttons: function( event, t ) {
                    var button_close = $( '<a href="javascript:void(0);" class="notice-dismiss alignright"></a>' );
                    button_close.bind( 'click.pointer', function( e ) {
                        e.preventDefault();
                        t.element.pointer( 'close' );
                    });
                    return button_close;
                },
                show: function( event, t ){
                    t.pointer.css( 'marginLeft', '115px' );
                    t.pointer.css( 'z-index', '99999' );
                },
                close: function( event, t ){
                    t.pointer.css( 'marginLeft', '0' );
                },
            } ).pointer( 'open' );
        }
    }

    $( 'body' ).on( 'click', '[data-wcml-open-pointer]', function() {
        WCML_Pointer.openPointer( $( this ) );
    } );

    $( 'body' ).on( 'click', 'a', function() {
        if( ! $(this).hasClass( 'wcml-pointer-link' ) ){
            $( '.wcml-information-active-pointer' ).pointer( 'close' );
        }
    } );

} )( jQuery );

jQuery( document ).ready( function($) {

    $('.wcml-pointer-block').each( function(){
        var selector = $(this).data('selector');
        if( selector ){
            var insert_method = $(this).data('insert-method');
            switch(insert_method){
                case 'prepend':
                    $(this).prependTo( $( '#'+selector ) ).show();
                    break;
                case 'append':
                    $(this).appendTo( $( '#'+selector ) ).show();
                    break;
                default:
                    $(this).insertAfter( $( '#'+selector ) ).show();
            }
        }else{
            $(this).show();
        }
    });

});