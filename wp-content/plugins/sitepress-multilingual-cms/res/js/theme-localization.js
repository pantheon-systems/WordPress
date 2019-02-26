var WPML_Core = WPML_Core || {};
WPML_Core.theme_localization = {};

addLoadEvent(function(){     
    jQuery('#icl_theme_localization').submit(iclSaveThemeLocalization);
    jQuery('#icl_theme_localization_type').submit(iclSaveThemeLocalizationType);

    jQuery(document).delegate('.check-column-plugin :checkbox', 'change', function () {
        WPML_Core.theme_localization.check_column( 'plugins', jQuery(this).prop('checked') );
    });
    jQuery(document).delegate('.check-column-theme :checkbox', 'change', function () {
        WPML_Core.theme_localization.check_column( 'themes', jQuery(this).prop('checked') );
    });
});

function iclSaveThemeLocalization(){
    var spl = jQuery(this).serialize().split('&');
    var parameters = {};
    for(var i=0; i< spl.length; i++){
        var par = spl[i].split('=');
        parameters[par[0]] = par[1];
    }
    jQuery('#icl_theme_localization_wrap').load(location.href + ' #icl_theme_localization_subwrap', parameters, function(){
        fadeInAjxResp('#icl_ajx_response_fn', icl_ajx_saved);
    });
    return false;
}

function iclSaveThemeLocalizationType(){
    jQuery(this).find('.icl_form_errors').fadeOut();
    var val         = jQuery(this).find('[name="icl_theme_localization_type"]:checked').val();
    var td_on       = jQuery(this).find('[name="icl_theme_localization_load_td"]').attr('checked');
    var td_value    = jQuery(this).find('[name="textdomain_value"]').val();

    if(val == 2 && td_on && !jQuery.trim(td_value)){
        jQuery(this).find('.icl_form_errors_1').fadeIn();
        return false;
    }

    var data = jQuery(this).serializeArray();
    data.push({
        'name': 'action',
        'value' : 'WPML_Theme_Localization_Type'
    });

    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: data,
        success: function(){
            location.href=location.href.replace(/#(.*)$/,'');
        }
    });
    return false;
}

WPML_Core.theme_localization.check_column = function(type, checked){
    var visible_rows = jQuery('#wpml_strings_in_' + type).find(':checkbox:visible');
    visible_rows.prop('checked', checked);
};
