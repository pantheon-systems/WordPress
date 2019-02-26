/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
(function ($) {

    $('.metaboxes-tab').each(function () {
        $('.tabs-panel', this).hide();

        var active_tab = wpCookies.get('active_metabox_tab');
        if (active_tab == null) {
            active_tab = $('ul.metaboxes-tabs li:first-child a', this).attr('href');
        } else {
            active_tab = '#' + active_tab;
        }

        $(active_tab).show();

        $('.metaboxes-tabs a', this).click(function (e) {
            if ($(this).parent().hasClass('tabs')) {
                e.preventDefault();
                return;
            }

            var t = $(this).attr('href');
            $(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
            $(this).closest('.metaboxes-tab').find('.tabs-panel').hide();
            $(t).show();

            return false;
        });
    });

    var act_page_option = $('#_active_page_options-container').parent().html();
    $('#_active_page_options-container').parent().remove();
    $(act_page_option).insertAfter('#yit-post-setting .handlediv');
    $(act_page_option).insertAfter('#yit-page-setting .handlediv');


    $('#_active_page_options-container').on('click', function(){
        if( $('#_active_page_options').is(":checked") ){
            $('#yit-page-setting .inside .metaboxes-tab, #yit-post-setting .inside .metaboxes-tab').css( { 'opacity' : 1 , 'pointer-events' : 'auto' } );
        }else{
            $('#yit-page-setting .inside .metaboxes-tab, #yit-post-setting .inside .metaboxes-tab').css( { 'opacity' : 0.5 , 'pointer-events' : 'none' } );
        }
    }).click();


    //dependencies handler
    $('.metaboxes-tab [data-dep-target]').each(function(){
        var t = $(this);

        var field = '#' + t.data('dep-target'),
            dep = '#' + t.data('dep-id'),
            value = t.data('dep-value'),
            type = t.data('dep-type');


        dependencies_handler( field, dep, value.toString(), type );

        $(dep).on('change', function(){
            dependencies_handler( field, dep, value.toString(), type );
        }).change();
    });

    //Handle dependencies.
    function dependencies_handler ( id, deps, values, type ) {
        var result = true;


        //Single dependency
        if( typeof( deps ) == 'string' ) {
            if( deps.substr( 0, 6 ) == ':radio' )
            {deps = deps + ':checked'; }

            var val = $( deps ).val();

            if( $(deps).attr('type') == 'checkbox'){
                var thisCheck = $(deps);
                if ( thisCheck.is ( ':checked' ) ) {
                    val = 'yes';
                }
                else {
                    val = 'no';
                }
            }

            values = values.split( ',' );

            for( var i = 0; i < values.length; i++ ) {
                if( val != values[i] )
                { result = false; }
                else
                { result = true; break; }
            }
        }

        var $current_field     = $( id ),
            $current_container = $( id + '-container' ).parent();

        var types = type.split( '-' ), j;
        for ( j in types ) {
            var current_type = types[ j ];

            if ( !result ) {
                switch ( current_type ) {
                    case 'disable':
                        $current_container.addClass( 'yith-disabled' );
                        $current_field.attr( 'disabled', true );
                        break;
                    case 'hideme':
                        $current_field.hide();
                        break;
                    default:
                        $current_container.hide();
                }

            } else {
                switch ( current_type ) {
                    case 'disable':
                        $current_container.removeClass( 'yith-disabled' );
                        $current_field.attr( 'disabled', false );
                        break;
                    case 'hideme':
                        $current_field.show();
                        break;
                    default:
                        $current_container.show();
                }
            }
        }
    }
    
})(jQuery);