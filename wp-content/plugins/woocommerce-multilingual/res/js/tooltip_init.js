var WCML_Tooltip = {

    default_args : {
        attribute : 'data-tip',
        fadeIn    : 50,
        fadeOut   : 50,
        delay     : 200
    },

    setup: function(){
        jQuery(document).ready(function () {
            WCML_Tooltip.init();
        });
    },

    init: function(){
        jQuery('.wcml-tip:visible').tipTip( WCML_Tooltip.default_args ); //jquery hover won't work on hidden elements
    },

    create_tip: function( text, style, args ){

        if( typeof args == 'undefined' ){
            args = {};
        }

        var tip = jQuery( '<i class="otgs-ico-help wcml-tip" data-tip="'+text+'" style="'+style+'"></i>' );

        for(var i in WCML_Tooltip.default_args ){
            if( typeof args[i] == 'undefined' ){
                args[i] = WCML_Tooltip.default_args[i];
            }
        }

        tip.tipTip( args );

        return tip;
    },

    add_before: function ( elem_class, text, style, args ){

        tip = WCML_Tooltip.create_tip( text, style, args );
        tip.insertBefore( elem_class );

        return tip;
    },

    add_after: function ( elem_class, text, style, args ){

        tip = WCML_Tooltip.create_tip( text, style, args );
        tip.insertAfter( elem_class );


        return tip;
    }

}

WCML_Tooltip.setup();