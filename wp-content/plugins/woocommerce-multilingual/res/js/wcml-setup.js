
var WCML_Setup = WCML_Setup || {};

jQuery( function($){

    WCML_Setup.init = function(){

        $(document).ready(function() {

            $('.wcml-setup-form').on( 'click', 'a.submit', function(){

                var form = $(this).closest('form');
                form.attr('action', $(this).attr('href') );
                form.submit();
                return false;

            });

        });

    }

    WCML_Setup.init();

});






