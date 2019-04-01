jQuery(document).ready(function($){

    if( $('form>table').size() > 0 ){
        $('.wcml-is-translatable-attr-block tr').insertAfter('form>table tr:nth-child(3)');
    }else{
        $('.wcml-is-translatable-attr-block div').insertAfter('form div:nth-child(3)');
    }

    $('.wcml-is-translatable-attr-block').remove();


    $(document).on('click', '#wcml-is-translatable-attr', function(){

        if( !$(this).is(':checked') && $('#wcml-is-translatable-attr-notice').size()>0 ){

            if ( confirm( $('#wcml-is-translatable-attr-notice').val() ) == false ) {
                $(this).attr("checked", true);
            }
        }
    });

});

