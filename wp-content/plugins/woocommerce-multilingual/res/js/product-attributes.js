/*globals jQuery, document, TaxonomyTranslation, window, ajaxurl */

(function () {
    "use strict";

    var attributeSelector = jQuery('#wcml_product_attributes');


    jQuery(document).ready(function () {

        attributeSelector.on('change', switchAttribute);

    });

    function switchAttribute(){
        var attributeName = jQuery(this).val();

        var wrap        = jQuery('#taxonomy-translation');
        var spinner     = jQuery('.wpml-loading-taxonomy:first').clone();

		wrap.html('');
        wrap.append(spinner);
		wrap.find('.wpml-loading-taxonomy:first').show();

        updateAttributeInfo( attributeName );

        TaxonomyTranslation.classes.taxonomy = new TaxonomyTranslation.models.Taxonomy({taxonomy: attributeName});
        TaxonomyTranslation.mainView = new TaxonomyTranslation.views.TaxonomyView({model: TaxonomyTranslation.classes.taxonomy}, {sync: isSyncTab()});


    }

    function isSyncTab(){

        return  window.location.search.substring(1).indexOf('&sync=1') > -1;
    }

    function updateAttributeInfo( taxonomy ){
        jQuery('.wrap .icl_tt_main_bottom').remove();

        jQuery.ajax({
            type: "post",
            url: ajaxurl,
            dataType: 'json',
            data: {
                action: "wcml_update_term_translated_warnings",
                taxonomy: taxonomy,
                show_sync: true
            },
            success: function (response) {
                jQuery('.tax-product-attributes').removeAttr('title');
                jQuery('.tax-product-attributes i.otgs-ico-warning').remove();
                if ( !response.hide ) {
                    jQuery('.tax-product-attributes').attr('title', jQuery('#warn_title').val() );
                    jQuery('.tax-product-attributes').append('<i class="otgs-ico-warning"></i>');
                }

                if( response.bottom_html ){
                    jQuery('.wcml-wrap .wrap').append( '<div class="icl_tt_main_bottom">'+response.bottom_html+'</div>' );
                }
            }
        });

    }



})();
