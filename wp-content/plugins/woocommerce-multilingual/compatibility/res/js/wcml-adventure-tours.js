jQuery( document ).ready( function( $ ){


    $( '.wcml_custom_cost_field').each( function(){

        var tour_id = $(this).attr('data-tour');

        $('input[name="tour-booking-row['+tour_id+'][spec_price]"]').after( $(this) );

        if( $( '.wcml_custom_prices_input:checked' ).val() == 1 ){
            $(this).show();
        }

    });

    $(document).on( 'change', '.wcml_custom_prices_input', function(){

        if( $(this).val() == 1 ){

            $( '.wcml_custom_cost_field' ).show();

        }else{

            $( '.wcml_custom_cost_field' ).hide();

        }

    });

});

