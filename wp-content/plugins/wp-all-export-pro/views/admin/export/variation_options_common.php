<script type="text/javascript">
    (function ($) {
        $(function () {

            function updateCheckSelect() {
                var exportVariations = $('#export_variations').val();
                var exportVariationsTitle = $('#export_variations_title').val();

                $('.sub-options-' + exportVariations).css('display', 'block');
            }

            $('.export_variations').change(function () {

                var inputName = $(this).attr('name');
                $('.sub-options').slideUp('fast');
                var value = $('input[name='+inputName+']:checked').val();
                var $thisInput = $('input.export_variations[value='+value +']');
                $thisInput.prop('checked', 'checked');
                $('#export_variations').val(value);
                if (value <= 2) {
                    $thisInput.parent().parent().find('.sub-options').slideDown('fast');
                    $thisInput.parent().parent().find('.sub-options').find('input').eq(0).attr('checked', 'checked');
                }
            });
            
            $('.export_variations_title').change(function(event){

                var inputName = $(this).attr('name');
                var value = $('input[name='+inputName +']:checked').val();
                var $thisInput = $('.export_variations_title[value='+value +']');
                $thisInput.prop('checked', 'checked');

                $('#export_variations_title').val(value);
            });

            updateCheckSelect();
        });
    })(jQuery);
</script>

<input type="hidden" id="export_variations" name="export_variations" value="<?php echo XmlExportEngine::getProductVariationMode();?>" />
<input type="hidden" id="export_variations_title" name="export_variations_title" value="<?php echo XmlExportEngine::getProductVariationTitleMode();?>" />