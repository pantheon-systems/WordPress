jQuery( document ).ready( function( $ ){

    wcml_lock_bookings_fields();

    if( $( '.wcml_custom_costs_input:checked' ).val() == 1 ){

        $( '.wcml_custom_cost_field' ).show();

    }

    $(document).on( 'change', '.wcml_custom_costs_input', function(){

        if( $(this).val() == 1 ){

            $( '.wcml_custom_cost_field' ).show();

        }else{

            $( '.wcml_custom_cost_field' ).hide();

        }

    });

    $(document).on( 'mouseout', '.add_row', function(){

        if( $( '.wcml_custom_costs_input:checked' ).val() == 1 ) {

            $( '.wcml_custom_cost_field' ).show();

        }

    });

    $(document).on( 'mouseout', '.add_person', function(){

        if( $( '.wcml_custom_costs_input:checked' ).val() == 1 ) {

            setTimeout(
                function() {
                    $( '.wcml_custom_cost_field' ).show();
                }, 3000);

        }

    });

    function wcml_lock_bookings_fields(){
        //lock fields
        if( typeof lock_settings != 'undefined'  && typeof lock_settings.lock_fields != 'undefined' && lock_settings.lock_fields == 1 ){

            $('#bookings_pricing input[type="number"],' +
                '#accommodation_bookings_rates input[type="number"], ' +
                '#bookings_resources input[type="number"], ' +
                '#bookings_availability input[type="number"], ' +
                '#bookings_availability input[type="time"], ' +

                '#bookings_persons input[type="number"]').each(function(){
                $(this).attr('readonly','readonly');
                $(this).after($('.wcml_lock_img').clone().removeClass('wcml_lock_img').show());
            });

            var buttons = [ 'add_resource', 'add_row','remove_booking_resource','remove_booking_person','add_person' ];

            for (i = 0; i < buttons.length; i++) {
                $('.'+buttons[i]).attr('disabled','disabled');
                $('.'+buttons[i]).unbind('click');
                $('.'+buttons[i]).after($('.wcml_lock_img').clone().removeClass('wcml_lock_img').show());
            }
            $('.add_row').removeAttr('data-row');

            $('form#post input[type="submit"]').click(function(){

                for (i = 0; i < non_standard_fields.ids.length; i++) {
                    $('#'+non_standard_fields.ids[i]).removeAttr('disabled');
                }

                $('#bookings_pricing select, #bookings_resources select, #bookings_availability select,#bookings_persons input[type="checkbox"]').each(function(){
                    $(this).removeAttr('disabled');
                });

            });


        }
    }

});



