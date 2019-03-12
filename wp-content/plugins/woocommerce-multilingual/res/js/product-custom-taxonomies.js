/*globals jQuery, document, TaxonomyTranslation, window, ajaxurl */

(function () {
    "use strict";

    var taxonomySelector = jQuery('#wcml_product_custom_taxonomies');


    jQuery(document).ready(function () {

        taxonomySelector.on('change', switchTaxonomy);

    });

    function switchTaxonomy(){
        var taxonomyName = jQuery(this).val();

        var wrap        = jQuery('#taxonomy-translation');
        var spinner     = jQuery('.wpml-loading-taxonomy:first').clone();

		wrap.html('');
        wrap.append(spinner);
		wrap.find('.wpml-loading-taxonomy:first').show();

        updateTaxonomyInfo( taxonomyName );

        TaxonomyTranslation.classes.taxonomy = new TaxonomyTranslation.models.Taxonomy({taxonomy: taxonomyName});
        TaxonomyTranslation.mainView = new TaxonomyTranslation.views.TaxonomyView({model: TaxonomyTranslation.classes.taxonomy}, {sync: isSyncTab()});


    }

    function isSyncTab(){

        return  window.location.search.substring(1).indexOf('&sync=1') > -1;
    }

    function updateTaxonomyInfo( taxonomy ){
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
                jQuery('.tax-custom-taxonomies').removeAttr('title');
                jQuery('.tax-custom-taxonomies i.otgs-ico-warning').remove();
                if ( !response.hide ) {
                    jQuery('.tax-custom-taxonomies').attr('title', jQuery('#warn_title').val() );
                    jQuery('.tax-custom-taxonomies').append('<i class="otgs-ico-warning"></i>');
                }

                if( response.bottom_html ){
                    jQuery('.wcml-wrap .wrap').append( '<div class="icl_tt_main_bottom">'+response.bottom_html+'</div>' );
                }
            }
        });

    }



})();
