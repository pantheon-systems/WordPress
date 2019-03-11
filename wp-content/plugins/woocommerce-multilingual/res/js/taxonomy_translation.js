jQuery(document).ready(function ($) {

    if (typeof TaxonomyTranslation != 'undefined') {

        TaxonomyTranslation.views.TermView = TaxonomyTranslation.views.TermView.extend({
            initialize: function () {
                TaxonomyTranslation.views.TermView.__super__.initialize.apply(this, arguments);
                this.listenTo(this.model, 'translationSaved', this.render_overlay);
            },
            render_overlay: function () {
                var taxonomy = TaxonomyTranslation.classes.taxonomy.get("taxonomy");
                $.ajax({
                    type: "post",
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        action: "wcml_update_term_translated_warnings",
                        taxonomy: taxonomy,
                        wcml_nonce: $('#wcml_update_term_translated_warnings_nonce').val()
                    },
                    success: function (response) {
                        if (response.hide) {
                            if (response.is_attribute) {
                                $('.tax-product-attributes').removeAttr('title');
                                $('.tax-product-attributes i.otgs-ico-warning').remove();
                            } else {
                                $('.js-tax-tab-' + taxonomy).removeAttr('title');
                                $('.js-tax-tab-' + taxonomy + ' i.otgs-ico-warning').remove();
                            }
                        }
                    }
                })
            }
        });

    }

    function disable_tax_translation_toggling() {
        $('.wcml-tax-translation-list .actions a')
            .bind('click', tax_translation_toggling_return_false)
            .css({cursor: 'wait'});
    }

    function enable_tax_translation_toggling() {
        $('.wcml-tax-translation-list .actions a')
            .unbind('click', tax_translation_toggling_return_false)
            .css({cursor: 'pointer'});
    }

    function tax_translation_toggling_return_false(event) {
        event.preventDefault();
        return false;
    }

    $(document).on('submit', '#wcml_tt_sync_variations', function () {

        var this_form = $('#wcml_tt_sync_variations');
        var data = this_form.serialize();
        this_form.find('.wcml_tt_spinner').fadeIn();
        this_form.find('input[type=submit]').attr('disabled', 'disabled');

        $.ajax({
            type: "post",
            url: ajaxurl,
            dataType: 'json',
            data: data,
            success: function (response) {
                this_form.find('.wcml_tt_sycn_preview').html(response.progress);
                if (response.go) {
                    this_form.find('input[name=last_post_id]').val(response.last_post_id);
                    this_form.find('input[name=languages_processed]').val(response.languages_processed);
                    this_form.trigger('submit');
                } else {
                    this_form.find('input[name=last_post_id]').val(0);
                    this_form.find('.wcml_tt_spinner').fadeOut();
                    this_form.find('input').removeAttr('disabled');
                    jQuery('#wcml_tt_sync_assignment').fadeOut();
                    jQuery('#wcml_tt_sync_desc').fadeOut();
                }

            }
        });

        return false;

    });


    $(document).on('submit', '#wcml_tt_sync_assignment', function () {

        var this_form = $('#wcml_tt_sync_assignment');
        var parameters = this_form.serialize();

        this_form.find('.wcml_tt_spinner').fadeIn();
        this_form.find('input').attr('disabled', 'disabled');

        $('.wcml_tt_sync_row').remove();

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: ajaxurl,
            data: 'action=wcml_tt_sync_taxonomies_in_content_preview&wcml_nonce=' + $('#wcml_sync_taxonomies_in_content_preview_nonce').val() + '&' + parameters,
            success: function (ret) {

                this_form.find('.wcml_tt_spinner').fadeOut();
                this_form.find('input').removeAttr('disabled');

                if (ret.errors) {
                    this_form.find('.errors').html(ret.errors);
                } else {
                    jQuery('#wcml_tt_sync_preview').html(ret.html);
                    jQuery('#wcml_tt_sync_assignment').fadeOut();
                    jQuery('#wcml_tt_sync_desc').fadeOut();
                }

            }

        });

        return false;

    });

    $(document).on('click', 'form.wcml_tt_do_sync a.submit', function () {

        var this_form = $('form.wcml_tt_do_sync');
        var parameters = this_form.serialize();

        this_form.find('.wcml_tt_spinner').fadeIn();
        this_form.find('input').attr('disabled', 'disabled');

        jQuery.ajax({
            type: "POST",
            dataType: 'json',
            url: ajaxurl,
            data: 'action=wcml_tt_sync_taxonomies_in_content&wcml_nonce=' + $('#wcml_sync_taxonomies_in_content_nonce').val() + '&' + parameters,
            success: function (ret) {

                this_form.find('.wcml_tt_spinner').fadeOut();
                this_form.find('input').removeAttr('disabled');

                if (ret.errors) {
                    this_form.find('.errors').html(ret.errors);
                } else {
                    this_form.closest('.wcml_tt_sync_row').html(ret.html);
                }

            }

        });

        return false;


    });

    $(document).on('click', '#term-table-sync-header', function () {
        $('#wcml_tt_sync_assignment').hide();
        $('#wcml_tt_sync_desc').hide();
    });

    $(document).on('click', '#term-table-header', function () {
        if( $('#wcml_tt_sync_assignment').data('sync') ) {
            $('#wcml_tt_sync_assignment').show();
            $('#wcml_tt_sync_desc').show();
        }
    });


});

