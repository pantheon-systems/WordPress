jQuery(document).ready(function($){


    $(document).on('click','#wcml_file_path_option', function( e ){

        if($(this).is(':checked')){
            $(this).parent().find('ul').show();
        }else{
            $(this).parent().find('ul').hide();
        }
    });

});

