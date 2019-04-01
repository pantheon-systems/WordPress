/*global jQuery*/
/*localization global: wpml_tm_strings*/

var WPML_TM = WPML_TM || {};

(function () {
	"use strict";

jQuery(document).ready(function () {


	jQuery(document).delegate('.icl_tj_select_translator select', 'change', icl_tm_assign_translator);


	jQuery('#icl_side_by_site').find('a[href="#cancel"]').click(function () {
		var anchor = jQuery(this);
		jQuery.ajax({
			type: "POST", url: ajaxurl, data: 'action=dismiss_icl_side_by_site',
			success: function () {
				anchor.parent().parent().fadeOut();
			}
		});
		return false;
	});

	// Translator notes - translation dashboard - start
	jQuery('.icl_tn_link').click(function () {
		jQuery('.icl_post_note:visible').slideUp();
		var anchor = jQuery(this);
		var spl = anchor.attr('id').split('_');
		var doc_id = spl[3];
		var icl_post_note_doc_id = jQuery('#icl_post_note_' + doc_id);
		if (icl_post_note_doc_id.css('display') !== 'none') {
			icl_post_note_doc_id.slideUp();
		} else {
			icl_post_note_doc_id.slideDown();
			var text_area = icl_post_note_doc_id.find('textarea');
			text_area.focus();
			text_area.data('original_value', text_area.val());
		}
		return false;
	});

	jQuery('.icl_tn_cancel').click(function () {
		var note_div = jQuery(this).closest('.icl_post_note'),
			text_area = note_div.find('textarea');

		text_area.val( text_area.data('original_value' ) );
		note_div.slideUp();
	});

	jQuery('.icl_tn_save').click(function () {
		var anchor = jQuery(this);
		anchor.closest('table').find('input').attr('disabled', 'disabled');
		var tn_post_id = anchor.closest('table').find('.icl_tn_post_id').val();
		jQuery.ajax({
			type: "POST",
			url: icl_ajx_url,
			data: "icl_ajx_action=save_translator_note&note=" + anchor.closest('table').prev().val() + '&post_id=' + tn_post_id + '&_icl_nonce=' + jQuery('#_icl_nonce_stn_').val(),
			success: function () {
				anchor.closest('table').find('input').removeAttr('disabled');
				anchor.closest('table').parent().slideUp();
				var note_icon = jQuery('#icl_tn_link_' + tn_post_id).find('i');
				if (anchor.closest('table').prev().val()) {
					note_icon.removeClass('otgs-ico-note-add-o').addClass('otgs-ico-note-edit-o');
				} else {
					note_icon.removeClass('otgs-ico-note-edit-o').addClass('otgs-ico-note-add-o');
				}
			}
		});

	});
	// Translator notes - translation dashboard - end

	// MC Setup
	jQuery('#icl_doc_translation_method').submit(iclSaveForm);
	jQuery('#icl_page_sync_options').submit(iclSaveForm);
	jQuery('form[name="icl_custom_tax_sync_options"]').submit(iclSaveForm);
	jQuery('form[name="icl_custom_posts_sync_options"]').submit(iclSaveForm);
	jQuery('form[name="icl_cf_translation"]').submit(iclSaveForm);
	jQuery('form[name="icl_tcf_translation"]').submit(iclSaveForm);

	var icl_translation_jobs_basket = jQuery('#icl-translation-jobs-basket');
	icl_translation_jobs_basket.find('th :checkbox').change(iclTmSelectAllJobsBasket);
	icl_translation_jobs_basket.find('td :checkbox').change(iclTmUpdateJobsSelectionBasket);
	var icl_translation_jobs = jQuery('#icl-translation-jobs');
	icl_translation_jobs.find('td.js-check-all :checkbox').change(iclTmSelectAllJobsSelection);
	icl_translation_jobs.find('td :checkbox').change(update_translation_job_checkboxes);

	jQuery('#icl_tm_jobs_dup_submit').click(function () {
		return confirm(jQuery(this).next().html());
	});

	jQuery('#icl_hide_promo').click(function () {
		jQuery.ajax({type: "POST", url: ajaxurl, data: 'action=icl_tm_toggle_promo&value=1', success: function () {
			jQuery('.icl-translation-services').slideUp(function () {
				jQuery('#icl_show_promo').fadeIn();
			});
		}});
		return false;
	});

    jQuery('#icl_show_promo').click(function () {
        jQuery.ajax({type: "POST", url: ajaxurl, data: 'action=icl_tm_toggle_promo&value=0', success: function () {
            jQuery('#icl_show_promo').hide();
            jQuery('.icl-translation-services').slideDown();
        }});
        return false;
    });

    // --- Start: XLIFF form handler ---
	var icl_xliff_options_form = jQuery('#icl_xliff_options_form');
	if (icl_xliff_options_form !== undefined) {
        /** @namespace jQuery.browser.msie */
        if (jQuery.browser && jQuery.browser.msie) {
            icl_xliff_options_form.submit(icl_xliff_set_newlines);
        } else {
            jQuery(document).undelegate("#icl_xliff_options_form");
            jQuery(document).delegate('#icl_xliff_options_form', 'submit', icl_xliff_set_newlines);
        }
    }

    // --- End: XLIFF form handler ---

	// Make the number in the translation basket tab flash.
    var translation_basket_flash = function (count) {

        var basket_count = jQuery('#wpml-basket-items');
        var basket_tab = basket_count.parent();

        if (basket_count.length && count) {
            count--;

            var originalBackgroundColor = basket_tab.css('background-color');
            var originalColor = basket_tab.css('color');

            flash_animate_element(basket_tab, '#0085ba', '#ffffff');
            if (count) {
                flash_animate_element(basket_tab, originalBackgroundColor, originalColor);
            }

            translation_basket_flash(count);

        }
    };

    var flash_animate_element = function (element, backgroundColor, color) {
        element.animate({opacity: 1}, 500, function () {
                element.css({backgroundColor: backgroundColor, color: color});
            }
        );
    }

	if (location.href.indexOf("main.php&sm=basket") == -1 ) {
		translation_basket_flash (3);
	}
});

function icl_xliff_set_newlines(e) {
    e.preventDefault();

    var form = jQuery(this);
    var submitButton = form.find(':submit');

    submitButton.prop('disabled', true);
    var ajaxLoader = jQuery(icl_ajxloaderimg).insertBefore(submitButton);
    var icl_xliff_newlines = jQuery("input[name=icl_xliff_newlines]:checked").val();
    var icl_xliff_version = jQuery("select[name=icl_xliff_version]").val();

    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        dataType: 'json',
        data:  {
            action: 'set_xliff_options',
            security: wpml_xliff_ajax_nonce,
            icl_xliff_newlines: icl_xliff_newlines,
            icl_xliff_version: icl_xliff_version
        },
        success: function (msg) {
            if (!msg.error) {
                fadeInAjxResp('#icl_ajx_response', icl_ajx_saved);
            }
            else {
                alert(msg.error);
            }
        },
        error: function (msg) {
            fadeInAjxResp('#icl_ajx_response', icl_ajx_error);
        },
        complete: function () {
            ajaxLoader.remove();
            submitButton.prop('disabled', false);
        }
    });

    return false;
}

function icl_tm_assign_translator() {
	var this_translator = jQuery(this);
	var translator_id = this_translator.val();
	var icl_tj_select_translator = this_translator.closest('.icl_tj_select_translator');
	var translation_controls = icl_tj_select_translator.find('.icl_tj_select_translator_controls');
	var job_id = translation_controls.attr('id').replace(/^icl_tj_tc_/, '');
	translation_controls.show();
	translation_controls.find('.icl_tj_cancel').click(function () {
		this_translator.val(jQuery('#icl_tj_ov_' + job_id).val());
		translation_controls.hide();
	});
	var jobType = jQuery('#icl_tj_ty_' + job_id).val();
	translation_controls.find('.icl_tj_ok').unbind('click').click(function () {
		icl_tm_assign_translator_request(job_id, translator_id, this_translator, jobType);
	});

}

function icl_tm_assign_translator_request(job_id, translator_id, select, jobType) {
	var translation_controls = select.closest('.icl_tj_select_translator').find('.icl_tj_select_translator_controls');
	select.attr('disabled', 'disabled');
	translation_controls.find('.icl_tj_cancel, .icl_tj_ok').attr('disabled', 'disabled');
	var td_wrapper = select.parent().parent();

    var ajaxLoader = jQuery( icl_ajxloaderimg ).insertBefore( translation_controls.find( '.icl_tj_ok' ) );

	jQuery.ajax({
		type: "POST",
		url: icl_ajx_url,
		dataType: 'json',
		data: 'icl_ajx_action=assign_translator&job_id=' + job_id + '&translator_id=' + translator_id + '&job_type=' + jobType + '&_icl_nonce=' + jQuery('#_icl_nonce_at').val(),
		success: function (msg) {
			if (!msg.error) {
				translation_controls.hide();
				/** @namespace msg.service */
				if (msg.service !== 'local') {
					td_wrapper.html(msg.message);
				}
			}
			select.removeAttr('disabled');
			translation_controls.find('.icl_tj_cancel, .icl_tj_ok').removeAttr('disabled');
			ajaxLoader.remove();
			translation_controls.hide();


		}
	});

	return false;
}

    function icl_tm_set_pickup_method(e) {
        e.preventDefault();

        var form = jQuery(this);
        var submitButton = form.find(':submit');

        submitButton.prop('disabled', true);
        var ajaxLoader = jQuery(icl_ajxloaderimg).insertBefore(submitButton);

        jQuery.ajax({
            type: "POST",
            url: icl_ajx_url,
            dataType: 'json',
            data: 'icl_ajx_action=set_pickup_mode&' + form.serialize(),
            success: function (msg) {
                if ( msg.success ) {
                    icl_translations_pickup_box_populate();
                } else {
					fadeInAjxResp( '#icl_ajx_response_tpm', msg.data.message, true );
				}
            },
            complete: function () {
                ajaxLoader.remove();
                submitButton.prop('disabled', false);
            }
        });

        return false;
    }

    function iclTmSelectAllJobsBasket(caller) {
        if (jQuery(caller).attr('checked')) {
            jQuery('#icl-translation-jobs-basket').find(':checkbox').attr('checked', 'checked');
            jQuery('#icl-tm-jobs-cancel-but').removeAttr('disabled');
        } else {
            jQuery('#icl-translation-jobs-basket').find(':checkbox').removeAttr('checked');
            jQuery('#icl-tm-jobs-cancel-but').attr('disabled', 'disabled');
        }
    }

	function update_translation_job_checkboxes() {
		update_job_checkboxes('#icl-translation-jobs')
	}
    function update_job_checkboxes(table_selector) {
        var job_parent = jQuery(table_selector);
        if (job_parent.find(':checkbox:checked').length > 0) {
            jQuery('#icl-tm-jobs-cancel-but').removeAttr('disabled');
            var checked_items = job_parent.find('th :checkbox');
            if (job_parent.find('td :checkbox:checked').length === job_parent.find('td :checkbox').length) {
                checked_items.attr('checked', 'checked');
            } else {
                checked_items.removeAttr('checked');
            }
        } else {
            jQuery('#icl-tm-jobs-cancel-but').attr('disabled', 'disabled');
        }
    }

    function iclTmUpdateJobsSelectionBasket() {
        iclTmSelectAllJobsBasket(this);
        update_job_checkboxes('#icl-translation-jobs-basket');
    }

	function iclTmSelectAllJobsSelection() {
     if (jQuery(this).attr('checked')) {
         jQuery('#icl-translation-jobs').find(':checkbox').attr('checked', 'checked');
         jQuery('#icl-tm-jobs-cancel-but').removeAttr('disabled');
     } else {
         jQuery('#icl-translation-jobs').find(':checkbox').removeAttr('checked');
         jQuery('#icl-tm-jobs-cancel-but').attr('disabled', 'disabled');
     }
 }

if (typeof String.prototype.startsWith !== 'function') {
  // see below for better implementation!
  String.prototype.startsWith = function (str){
    return this.slice(0, str.length) === str;
  };
}
if (typeof String.prototype.endsWith !== 'function') {
  String.prototype.endsWith = function (str){
    return this.slice(-str.length) === str;
  };
}
}());

(function($) {
	$(document).ready(function(){
		$('#translation-notifications').on('change', 'input', function(e) {
			var input = $(e.target);
			var child = $('[name="' + input.data('child') + '"]');

			if ( child.length ) {
				child.prop('disabled', !input.is(":checked"));
			}

		});
	});
})(jQuery);