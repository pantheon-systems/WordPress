jQuery(document).ready(function(){
	var filterSearch     = jQuery("#icl_st_filter_search");

    jQuery('.wpml-colorpicker').wpColorPicker();

    jQuery('a[href="#icl-st-toggle-translations"]').click(icl_st_toggler);
    var inlineTextArea = jQuery('.icl-st-inline textarea');
    inlineTextArea.focus(icl_st_monitor_ta);
    inlineTextArea.keyup(icl_st_monitor_ta_check_modifications);
    jQuery(".icl_st_form").submit(icl_st_submit_translation);
    jQuery('select[name="icl_st_filter_status"]').change(icl_st_filter_status);
    jQuery('select[name="icl_st_filter_context"]').change(icl_st_filter_context);
    jQuery('select[name="icl-st-filter-translation-priority"]').change(icl_st_filter_translation_priority);
    jQuery('#icl_st_filter_search_sb').click(icl_st_filter_search);

	if (filterSearch.length) {
		updateControls();

		filterSearch.keyup(function (event) {
			if (event.keyCode === 13) {
				icl_st_filter_search();
			}
			updateControls();
		});
	}

    jQuery('#icl_st_filter_search_remove').click(icl_st_filter_search_remove);
    jQuery('#icl_st_delete_selected').click(icl_st_delete_selected);

    jQuery('#icl_st_po_translations').click(function(){
        if(jQuery(this).attr('checked')){
            jQuery('#icl_st_po_language').removeAttr('disabled').fadeIn();
        }else{
            jQuery('#icl_st_po_language').attr('disabled','disabled').fadeOut();
        }
    });
    var iclTMLanguages = jQuery('#icl_tm_languages');
    iclTMLanguages.find(':checkbox').click(icl_st_update_languages);
    jQuery('.icl_st_row_cb, .check-column :checkbox').click(icl_st_update_checked_elements);
    iclTMLanguages.find('select').change(icl_st_change_service);
    jQuery('.icl_htmlpreview_link').click(icl_st_show_html_preview);
    jQuery('#icl_st_po_form').submit(icl_validate_po_upload);
    jQuery('#icl_st_send_strings').submit(icl_st_send_strings);

    jQuery('.handlediv').click(function () {
        jQuery(this).parent().toggleClass('closed');
    });

    jQuery('#icl_st_track_strings').submit(iclSaveForm);

    var iclSTOptionWriteForm = jQuery('#icl_st_option_write_form');
    iclSTOptionWriteForm.submit(icl_st_admin_options_form_submit);
    iclSTOptionWriteForm.submit(iclSaveForm);

    jQuery('.icl_stow_toggler').click(icl_st_admin_strings_toggle_strings);
    jQuery('#icl_st_ow_export').click(icl_st_ow_export_selected);
    jQuery('#icl_st_ow_export_close').click(icl_st_ow_export_close);

    // Picker align
    jQuery(".pick-show").click(function () {
        var set = jQuery(this).offset();
           jQuery("#colorPickerDiv").css({"top":set.top-180,"left":set.left, "z-index":99});
    });

	jQuery('input[name="wpml_st_theme_localization_type_wpml_td"]').on('click', function () {
		var checked = jQuery(this).prop('checked');
		jQuery('input[name="wpml_st_theme_localization_type_wpml_td"]').prop('checked', checked);
	});

    jQuery(document).delegate('.wpml_st_pop_download', 'click', icl_st_pop_download);

    var ICLSTMoreOptions = jQuery('#icl_st_more_options');
    ICLSTMoreOptions.submit(iclSaveForm);
    ICLSTMoreOptions.submit(
        function () {
            var iclSTTUser = jQuery('#icl_st_tusers');
            if (!iclSTTUser.find('label input:checked').length) {
                jQuery('#icl_st_tusers_list').html('-');
            } else {
                jQuery('#icl_st_tusers_list').html(iclSTTUser.find('label input:checked').next().map(
                    function () {
                        return jQuery(this).html();
                    }).get().join(', '))
            }
        }
    );

	function updateControls() {
		jQuery('#icl_st_filter_search_em').prop('disabled', filterSearch[0].value === '');
		jQuery('#search_translation').prop('disabled', filterSearch[0].value === '');
	}
});


function icl_st_toggler(){
    jQuery(".icl-st-inline").slideUp();
    var inl = jQuery(this).parent().next().next();
    if(inl.css('display') == 'none'){
		inl.trigger('wpml-open-string-translations', inl);
        inl.slideDown();
    }else{
        inl.slideUp();
    }
    icl_st_show_html_preview_close();
}

var icl_st_ta_cache = [];
var icl_st_cb_cache = [];
function icl_st_monitor_ta(){
    if(jQuery(this).attr('id') !== undefined){
        var id = jQuery(this).attr('id').replace(/^icl_st_ta_/,'');
        if(icl_st_ta_cache[id] == undefined){
            icl_st_ta_cache[id] = jQuery(this).val();
            icl_st_cb_cache[id] = jQuery('#icl_st_cb_'+id).attr('checked');
        }
    }
}

function icl_st_monitor_ta_check_modifications(){
    if(jQuery(this).attr('id') !== undefined){
        var id = jQuery(this).attr('id').replace(/^icl_st_ta_/,'');
        if(icl_st_ta_cache[id] != jQuery(this).val()){
            jQuery('#icl_st_cb_'+id).removeAttr('checked');
        }else{
            if(icl_st_cb_cache[id]){
                jQuery('#icl_st_cb_'+id).attr('checked','checked');
            }
        }
    }
    icl_st_show_html_preview_close();
}

function icl_st_submit_translation(){
    var thisf = jQuery(this);
    var postvars = thisf.serialize();
    postvars += '&icl_ajx_action=icl_st_save_translation';
    thisf.contents().find('textarea, input').attr('disabled','disabled');
    thisf.contents().find('.icl_ajx_loader').fadeIn();
    var string_id = thisf.find('input[name="icl_st_string_id"]').val();
    jQuery.post(icl_ajx_url, postvars, function(msg){
        thisf.contents().find('textarea, input').removeAttr('disabled');
        thisf.contents().find('.icl_ajx_loader').fadeOut();
        var spl = msg.split('|');
        jQuery('#icl_st_string_status_'+string_id).html(spl[1]);
    });
    return false;
}

function icl_st_filter_status(){
    var qs = jQuery(this).val() != '' ? '&status=' + jQuery(this).val() : '';
    location.href=location.href.replace(/#(.*)$/,'').replace(/&paged=([0-9]+)/,'').replace(/&updated=true/,'').replace(/&status=([0-9a-z-]+)/g,'') + qs;
}

function icl_st_filter_context(){
    var qs = jQuery(this).val() != '' ? '&context=' + encodeURIComponent(jQuery(this).val()) : '';
    location.href=location.href.replace(/#(.*)$/,'').replace(/&paged=([0-9]+)/,'').replace(/&updated=true/,'').replace(/&context=(.*)/g,'') + qs;
}

function icl_st_filter_translation_priority(){
    var qs = jQuery(this).val() != '' ? '&translation-priority=' + encodeURIComponent(jQuery(this).val()) : '';
    location.href=location.href.replace(/#(.*)$/,'').replace(/&paged=([0-9]+)/,'').replace(/&updated=true/,'').replace(/&translation-priority=(.*)/g,'') + qs;
}

function icl_st_filter_search(){
	var context            = jQuery('select[name="icl_st_filter_context"]').val(),
		search_text        = jQuery('#icl_st_filter_search').val(),
		exact_match        = jQuery('#icl_st_filter_search_em').attr('checked'),
		search_translation = jQuery('#search_translation').attr('checked'),
		query_string       = search_text !== '' ? '&search=' + encodeURIComponent(search_text) : '',
		url                = location.href;

	query_string = query_string.replace(/&em=1/g,'');

	if (icl_st_getUrlParameter('show_results') === 'all') {
		search_translation = false;
	}

	if(exact_match){
		query_string += '&em=1';
	}
	if (search_translation) {
		query_string += '&search_translation=1';
	}

	url = url.replace(/#(.*)$/,'')
			.replace(/&paged=([0-9]+)/,'')
			.replace(/&updated=true/,'')
			.replace(/&search=(.*)/g,'') + query_string;

	if ('' === context) {
		url = url.replace(/&context=(.*)/g,'');
	}

	location.href = url;
}

function icl_st_getUrlParameter(name) {
	name        = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
	var regex   = new RegExp('[\\?&]' + name + '=([^&#]*)');
	var results = regex.exec(location.search);
	return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

function icl_st_filter_search_remove(){
    location.href=location.href.replace(/#(.*)$/,'').replace(/&search=(.*)/g,'').replace(/&em=1/g,'');
}

function icl_st_delete_selected() {
    var postVars;
    var delids;
    var proceed;
    var confirmMessage;
    var errorMessage;
    var checkedRows = jQuery('.icl_st_row_cb:checked');
    if (checkedRows.length) {
        confirmMessage = jQuery(this).data('confirm');
        errorMessage = jQuery(this).data('error');
        proceed = confirm(confirmMessage);

        if (proceed) {
            delids = [];
            checkedRows.each(function () {
                var item = jQuery(this).val();
                delids.push(item);
                jQuery(this).trigger('click');
            });
            if (delids) {
                postVars = 'icl_ajx_action=icl_st_delete_strings&value=' + delids.join(',') + '&_icl_nonce=' + jQuery('#_icl_nonce_dstr').val();
                jQuery.post(icl_ajx_url, postVars, function () {
                    var i = 0;
                    for (; i < delids.length; i++) {
                        jQuery('.icl_st_row_cb[value="' + delids[i] + '"]').parent().parent().fadeOut('fast', function () {
                            jQuery(this).remove();
                        });
                    }
                }).success(function (msg) {
                    if ('1' !== msg) {
                        alert(errorMessage);
                    }
                }).error(function () {
                    alert(errorMessage);
                });
            }
        }
    }

    return false;
}

function icl_st_send_strings(){
    var checkedRows = jQuery('.icl_st_row_cb:checked');
    if(!checkedRows.length){
        return false;
    }
    var sendids = [];
    checkedRows.each(function(){
        sendids.push(jQuery(this).val());
    });

    if(!sendids.length){
        return false;
    }
    jQuery('#icl_st_send_strings').find('input[name="strings"]').val(sendids.join(','));

    return true;
}

function icl_st_update_languages() {
    if (!jQuery('#icl_tm_languages').find('input:checked').length) {
        jQuery('#icl_send_strings').attr('disabled', 'disabled');
    } else if (jQuery('.icl_st_row_cb:checked, .check-column input:checked').length && jQuery('.js-lang-not-active:checked').length === 0) {
        jQuery('#icl_send_strings').removeAttr('disabled');
    }
    var self = jQuery(this);
    var lang = self.attr('name').replace(/translate_to\[(.+)]/, '$1');
    if (self.attr('checked') == true && jQuery('#icl_st_service_' + lang).val() === 'icanlocalize') {
        icl_st_show_estimated_cost(lang);
    } else {
        icl_st_hide_estimated_cost(lang);
    }
}

function show_package_incomplete_notice(hide) {
    var notice = jQuery('#wpml-st-package-incomplete');
    if (hide) {
        notice.hide();
    } else {
        notice.show();
    }
}

function get_checked_cbs() {
    var package_counts = {};
    var context_select_options = jQuery('select[name="icl_st_filter_context"]').find('option') || [];
    jQuery.each(context_select_options, function (i, option) {
        option = jQuery(option);
        package_counts[option.val()] = option.data('unfiltered-count');
    });
    var st_table = jQuery('#icl_string_translations');
    var package_cbs = st_table.find('.icl_st_row_package:checked') || [];
    var affected_package_counts = {};
    jQuery.each(package_cbs, function (i, cb) {
        var domain = jQuery(jQuery(cb).closest('tr')).find('.wpml-st-col-domain').text();
        affected_package_counts[domain] = affected_package_counts.hasOwnProperty(domain) ? affected_package_counts[domain] : package_counts[domain];
        affected_package_counts[domain] = affected_package_counts[domain] - 1;
        jQuery(cb).data('package-domain', domain);
    });
    var checked_cbs = st_table.find('.icl_st_row_cb:checked');
    checked_cbs = checked_cbs.length > 0 ? checked_cbs : [];
    var incomplete_packages = false;
    var domain;
    for(domain in affected_package_counts){
        if(affected_package_counts.hasOwnProperty(domain) && affected_package_counts[domain] > 0){
            incomplete_packages = true;
        }
    }
    show_package_incomplete_notice( !incomplete_packages || checked_cbs.length === 0);

    return incomplete_packages ? [] : checked_cbs;
}

function icl_st_update_checked_elements() {
    if (jQuery(this).closest('th').hasClass('manage-column')) {
        jQuery('.icl_st_row_cb').prop('checked', jQuery(this).prop('checked'));
    }

    jQuery('#icl_st_change_lang_selected').prop('disabled', get_checked_cbs().length === 0);
    jQuery('#icl-st-change-translation-priority-selected').prop('disabled', get_checked_cbs().length === 0);

    jQuery('.js-change-translation-priority .select2-choice, .js-simple-lang-selector-flags .select2-choice').attr('disabled', get_checked_cbs().length === 0);


    if (!jQuery('.icl_st_row_cb:checked').length) {
        jQuery('#icl_st_delete_selected, #icl_send_strings').prop('disabled', true);
        WPML_String_Translation.translation_basket.clear_message();
    } else {
        jQuery('#icl_st_delete_selected').prop('disabled', false);
        var iclTROpt = jQuery('#icl-tr-opt');
        if (!iclTROpt.length || iclTROpt.find('input:checked').length) {
            if (WPML_String_Translation.translation_basket.maybe_enable_button()) {
                WPML_String_Translation.translation_basket.show_target_languages();
            }
        }
    }
    jQuery('.icl_st_estimate_wrap:visible').each(function () {
        var lang = jQuery(this).attr('id').replace(/icl_st_estimate_(.+)_wrap/, '$1');
        icl_st_show_estimated_cost(lang);
    });

    if (jQuery(this).hasClass('icl_st_row_cb')) {
        set_bulk_selects(jQuery('.icl_st_row_cb:not(:checked)').length === 0);
    }
}

function set_bulk_selects(bulk_cb_checked) {
    jQuery('.check-column input').attr('checked', (bulk_cb_checked ? 'checked' : false ));
}

function icl_st_show_html_preview(){
    var parent = jQuery(this).parent();
    var textarea = parent.parent().prev().find('textarea[name="icl_st_translation"]');
    if(parent.find('.icl_html_preview').css('display')=='none'){
        parent.find('.icl_html_preview').html(textarea.val().replace(/(\n|\r)/g,'<br>')).slideDown();
    }else{
        parent.find('.icl_html_preview').slideUp();
    }

    return false;
}

function icl_st_show_html_preview_close(){
    jQuery('.icl_html_preview').slideUp();
}

function icl_validate_po_upload(){
    var cont = jQuery(this).contents();
    cont.find('.icl_error_text').hide();
    if(!jQuery('#icl_po_file').val()){
        cont.find('#icl_st_err_po').fadeIn();
        return false;
    }
    if(!cont.find('select[name="icl_st_i_context"]').val() && !cont.find('input[name="icl_st_i_context_new"]').val()){
        cont.find('#icl_st_err_domain').fadeIn();
        return false;
    }
}

var icl_show_in_source_scroll_once = false;
jQuery(document).delegate('#icl_show_source_wrap', 'mouseover', function(){
    if(icl_show_in_source_scroll_once){
        icl_show_in_source(0, icl_show_in_source_scroll_once);
        icl_show_in_source_scroll_once = false;
    }
});

function icl_show_in_source(tabfile, line){

    if(icl_show_in_source_scroll_once){
        if(line > 40){
            line = line - 10;
            location.href=location.protocol+'//'+location.host+location.pathname+location.search+'#icl_source_line_'+tabfile+'_'+line;
        }
    }else{
        jQuery('.icl_string_track_source').fadeOut(
            function(){
                jQuery('#icl_string_track_source_'+tabfile).fadeIn(
                    function(){
                        if(line > 40){
                            line = line - 10;
                            location.href=location.protocol+'//'+location.host+location.pathname+location.search+'#icl_source_line_'+tabfile+'_'+line;
                        }
                    }
                );
            }
        );
    }
    return false;
}

function iclResizeIframe() {
    var frame = jQuery('#icl_string_track_frame_wrap').find('iframe');
    var tbAjaxContent = jQuery('#TB_ajaxContent');
    frame.attr('height', tbAjaxContent.height() - 20);
    frame.attr('width', tbAjaxContent.width());
}

function icl_st_admin_options_form_submit(){
    if(jQuery('input:checkbox.icl_st_has_translations:not(:checked)').length){
        iclHaltSave = !confirm(jQuery('#icl_st_options_write_confirm').html());
    }
    iclSaveForm_success_cb.push(function(){
        jQuery('#icl_st_options_write_success').fadeIn();
    });
}

function icl_st_admin_strings_toggle_strings(){
    var thisa = jQuery(this);
    jQuery(this).parent().next().slideToggle(function(){
        if(thisa.html().charAt(0)=='+'){
            thisa.html(thisa.html().replace(/^\+/,'-'));
        }else{
            thisa.html(thisa.html().replace(/^-/,'+'));
        }
    });
    return false;
}

function icl_st_ow_export_selected(){
    jQuery('#icl_st_ow_export').attr('disabled','disabled');
    jQuery('#icl_st_option_writes').find('.ajax_loader').fadeIn();
    jQuery.ajax({
        type: "POST",
        dataType: 'json',
        url: icl_ajx_url,
        data: "icl_ajx_action=icl_st_ow_export&"+jQuery('#icl_st_option_write_form').serialize() + '&_icl_nonce=' + jQuery('#_icl_nonce_owe').val(),
        success: function(res){
            jQuery('#icl_st_ow_export_out').html(res.message).slideDown();
            jQuery('#icl_st_option_writes').find('.ajax_loader').fadeOut(
                function(){
                    jQuery('#icl_st_ow_export_close').fadeIn();
                }
            );

        }
    });
}

function icl_st_ow_export_close(){
    jQuery('#icl_st_ow_export_out').slideUp(function(){jQuery('#icl_st_ow_export_close').fadeOut()});
    jQuery('#icl_st_ow_export').removeAttr('disabled');
}

function icl_st_pop_download(){

    location.href = ajaxurl + "?action=icl_st_pop_download&file="+jQuery(this).attr('href').substr(1);

    return false;
}

function icl_st_selected_word_count() {
    var word_count = 0;
    jQuery('.icl_st_row_cb:checked').each(function () {
        var string_id = jQuery(this).val();
        word_count += parseInt(jQuery('#icl_st_wc_' + string_id).val())
    });
    return word_count;
}

function icl_st_show_estimated_cost(lang){
    var estimate = icl_st_selected_word_count() * jQuery('#icl_st_max_rate_'+lang).html();
    jQuery('#icl_st_estimate_'+lang).html(Math.round(estimate*100)/100);
    jQuery('#icl_st_estimate_'+lang+'_wrap').show();
}

function icl_st_hide_estimated_cost(lang){
    jQuery('#icl_st_estimate_'+lang+'_wrap').hide();
}

function icl_st_change_service(){

    var lang = jQuery(this).attr('name').replace(/service\[(.+)]/ , '$1');
    if(jQuery(this).val()=='icanlocalize'){
        if(jQuery('#icl_st_translate_to_'+lang).attr('checked')){
            icl_st_show_estimated_cost(lang);
        }
    }else{
        icl_st_hide_estimated_cost(lang);
    }

}
