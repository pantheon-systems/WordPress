this["WPML_TM"] = this["WPML_TM"] || {};

this["WPML_TM"]["templates/translation-editor/footer.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class="wpml-translation-action-buttons-abort">\r\n\t<button class="cancel wpml-dialog-close-button js-dialog-cancel">' +
((__t = (cancel)) == null ? '' : __t) +
'</button>\r\n\t<button class="button-secondary wpml-resign-button js-resign">' +
((__t = (resign)) == null ? '' : __t) +
'</button>\r\n</div>\r\n<div class="wpml-translation-action-buttons-status">\r\n\t<div class="progress-bar js-progress-bar"><div class="progress-bar-text"></div></div>\r\n\t<label><input class="js-translation-complete" name="complete" type="checkbox"/>' +
((__t = (translation_complete)) == null ? '' : __t) +
'</label>\r\n\r\n\r\n\t<label class="otgs-toggle-group">\r\n\t\t<input type="checkbox" class="js-toggle-translated" id="wpml_tm_toggle_translated">\r\n\t\t<label for="wpml_tm_toggle_translated" class="otgs-on-off-switch">' +
((__t = (hide_translated)) == null ? '' : __t) +
'</label>\r\n\t\t<span class="otgs-switch__onoff">\r\n\t\t\t\t\t\t<span class="otgs-switch__onoff-label">\r\n\t\t\t\t\t\t\t<span class="otgs-switch__onoff-inner"></span>\r\n\t\t\t\t\t\t\t<span class="otgs-switch__onoff-switch"></span>\r\n\t\t\t\t\t\t</span>\r\n\t\t\t\t\t</span>\r\n\t</label>\r\n</div>\r\n<div class = "wpml-translation-action-buttons-apply">\r\n\t<span class = "js-saving-message" style = "display:none"><img src="' +
((__t = (loading_url)) == null ? '' : __t) +
'" alt="' +
((__t = (saving)) == null ? '' : __t) +
'" height="16" width="16"/>' +
((__t = (saving)) == null ? '' : __t) +
'</span>\r\n\t<button class = "button button-primary button-large wpml-dialog-close-button js-save-and-close">' +
((__t = (save_and_close)) == null ? '' : __t) +
'</button>\r\n\t<button class = "button button-primary button-large wpml-dialog-close-button js-save">' +
((__t = (save)) == null ? '' : __t) +
'</button>\r\n</div>\r\n\r\n\r\n';

}
return __p
};

this["WPML_TM"]["templates/translation-editor/group.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {

 if ( title ) { ;
__p +=
((__t = ( title )) == null ? '' : __t);
 } ;
__p += '\r\n<div class="inside">\r\n</div>\r\n\r\n';
 if ( divider ) { ;
__p += '\r\n<hr />\r\n';
 } ;
__p += '\r\n<button class="button-copy button-secondary js-button-copy-group">\r\n\t<i class="otgs-ico-copy"></i>\r\n</button>\r\n';

}
return __p
};

this["WPML_TM"]["templates/translation-editor/header.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p +=
((__t = ( title )) == null ? '' : __t) +
'\r\n<a href="' +
((__t = ( link_url )) == null ? '' : __t) +
'" class="view" target="_blank">' +
((__t = ( link_text )) == null ? '' : __t) +
'</a>\r\n';

}
return __p
};

this["WPML_TM"]["templates/translation-editor/image.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '\t<div class="inside">\r\n\t\t<img src="' +
((__t = ( image_src )) == null ? '' : __t) +
'">\r\n\t</div>\r\n\r\n\r\n';
 if ( divider ) { ;
__p += '\r\n<hr />\r\n';
 } ;
__p += '\r\n';

}
return __p
};

this["WPML_TM"]["templates/translation-editor/languages.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '\t<input type="hidden" name="source_lang" value="' +
((__t = ( language.source )) == null ? '' : __t) +
'" />\r\n\t<input type="hidden" name="target_lang" value="' +
((__t = ( language.target )) == null ? '' : __t) +
'" />\r\n\t<h3 class="wpml-header-original">' +
((__t = ( labels.source_lang )) == null ? '' : __t) +
':\r\n\t\t<span class="wpml-title-flag"><img src="' +
((__t = ( language.img.source_url )) == null ? '' : __t) +
'" alt="' +
((__t = ( language.source_lang )) == null ? '' : __t) +
'"/></span>\r\n\t\t<strong>' +
((__t = ( language.source_lang )) == null ? '' : __t) +
'</strong>\r\n\t</h3>\r\n\r\n\t<h3 class="wpml-header-translation">' +
((__t = ( labels.target_lang )) == null ? '' : __t) +
':\r\n\t\t<span class="wpml-title-flag"><img src="' +
((__t = ( language.img.target_url )) == null ? '' : __t) +
'" alt="' +
((__t = ( language.target_lang )) == null ? '' : __t) +
'"/></span>\r\n\t\t<strong>' +
((__t = ( language.target_lang )) == null ? '' : __t) +
'</strong>\r\n\t</h3>\r\n\r\n\t<div class="wpml-copy-container">\r\n\t\t<button class="button-secondary button-copy-all js-button-copy-all" title="' +
((__t = ( labels.copy_from_original )) == null ? '' : __t) +
'">\r\n\t\t\t<i class="otgs-ico-copy"></i> ' +
((__t = ( labels.copy_all )) == null ? '' : __t) +
'\r\n\t\t</button>\r\n\t</div>\r\n';

}
return __p
};

this["WPML_TM"]["templates/translation-editor/note.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<p>' +
((__t = ( note )) == null ? '' : __t) +
'</p>\r\n\r\n';

}
return __p
};

this["WPML_TM"]["templates/translation-editor/section.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div class="handlediv button-link"><br></div>\r\n<h3 class="hndle">\r\n\t<span>' +
((__t = ( section.title )) == null ? '' : __t) +
' ';
 if ( section.empty ) { ;
__p += '&nbsp;<i>' +
((__t = ( section.empty_message )) == null ? '' : __t);
 } ;
__p += '</span>\r\n\t';
 if ( section.sub_title ) { ;
__p += '\r\n\t<span class="subtitle"><i class="otgs-ico-warning"></i>' +
((__t = ( section.sub_title )) == null ? '' : __t) +
'</span>\r\n\t';
 } ;
__p += '\r\n</h3>\r\n\r\n<div class="inside">\r\n</div>\r\n';

}
return __p
};

this["WPML_TM"]["templates/translation-editor/single-line.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<label>' +
((__t = (field.title)) == null ? '' : __t) +
'</label>\r\n<input readonly class="original_value js-original-value" value="' +
__e( field.field_data ) +
'" type="text" ' +
((__t = (field.original_direction)) == null ? '' : __t) +
'/>\r\n<button class="button-copy button-secondary js-button-copy icl_tm_copy_link otgs-ico-copy" id="icl_tm_copy_link_' +
((__t = (field.field_type)) == null ? '' : __t) +
'" title="' +
((__t = ( labels.copy_from_original )) == null ? '' : __t) +
'"/>\r\n<input class="translated_value js-translated-value" name="fields[' +
((__t = (field.field_type)) == null ? '' : __t) +
'][data]" value="' +
__e( field.field_data_translated ) +
'" type="text" ' +
((__t = (field.translation_direction)) == null ? '' : __t) +
'/>\r\n\r\n<div class="field_translation_complete">\r\n\t<label><input class="icl_tm_finished js-field-translation-complete" name="fields[' +
((__t = (field.field_type)) == null ? '' : __t) +
'][finished]" type="checkbox" ';
 if (field.field_finished) { ;
__p += ' checked="checked" ';
 } ;
__p += ' />' +
((__t = (labels.translation_complete)) == null ? '' : __t) +
'</label>\r\n</div>\r\n\r\n';
 if (field.diff) { ;
__p += '\r\n<a class="js-toggle-diff toggle-diff">' +
((__t = (labels.show_diff)) == null ? '' : __t) +
'</a>\r\n' +
((__t = (field.diff)) == null ? '' : __t) +
'\r\n';
 } ;
__p += '\r\n\r\n\r\n<input type="hidden" name="fields[' +
((__t = (field.field_type)) == null ? '' : __t) +
'][tid]" value="' +
((__t = (field.tid)) == null ? '' : __t) +
'">\r\n<input type="hidden" name="fields[' +
((__t = (field.field_type)) == null ? '' : __t) +
'][format]" value="base64">\r\n';

}
return __p
};

this["WPML_TM"]["templates/translation-editor/textarea.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<label>' +
((__t = (field.title)) == null ? '' : __t) +
'</label>\r\n<textarea class="original_value js-original-value" readonly cols="22" rows="10" ' +
((__t = (field.original_direction)) == null ? '' : __t) +
'>' +
((__t = ( field.field_data )) == null ? '' : __t) +
'</textarea>\r\n<button class="button-copy button-secondary js-button-copy icl_tm_copy_link otgs-ico-copy" id="icl_tm_copy_link_' +
((__t = (field.field_type)) == null ? '' : __t) +
'" title="' +
((__t = ( labels.copy_from_original )) == null ? '' : __t) +
'"/>\r\n<textarea class="translated_value js-translated-value cols="22" rows="10" name="fields[' +
((__t = (field.field_type)) == null ? '' : __t) +
'][data]" ' +
((__t = (field.translation_direction)) == null ? '' : __t) +
'>' +
((__t = ( field.field_data_translated )) == null ? '' : __t) +
'</textarea>\r\n\r\n<div class="field_translation_complete">\r\n\t<label><input class="icl_tm_finished js-field-translation-complete" name="fields[' +
((__t = (field.field_type)) == null ? '' : __t) +
'][finished]" type="checkbox" ';
 if (field.field_finished) { ;
__p += ' checked="checked" ';
 } ;
__p += ' />' +
((__t = (labels.translation_complete)) == null ? '' : __t) +
'</label>\r\n</div>\r\n\r\n';
 if (field.diff) { ;
__p += '\r\n<a class="js-toggle-diff toggle-diff">' +
((__t = (labels.show_diff)) == null ? '' : __t) +
'</a>\r\n' +
((__t = (field.diff)) == null ? '' : __t) +
'\r\n';
 } ;
__p += '\r\n\r\n\r\n<input type="hidden" name="fields[' +
((__t = (field.field_type)) == null ? '' : __t) +
'][tid]" value="' +
((__t = (field.tid)) == null ? '' : __t) +
'">\r\n<input type="hidden" name="fields[' +
((__t = (field.field_type)) == null ? '' : __t) +
'][format]" value="base64">\r\n';

}
return __p
};

this["WPML_TM"]["templates/translation-editor/wysiwyg.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<label>' +
((__t = (field.title)) == null ? '' : __t) +
'</label>\r\n<div id="original_' +
((__t = (field.field_type)) == null ? '' : __t) +
'_placeholder"></div>\r\n<button class="button-copy button-secondary js-button-copy icl_tm_copy_link otgs-ico-copy" id="icl_tm_copy_link_' +
((__t = (field.field_type)) == null ? '' : __t) +
'" title="' +
((__t = ( labels.copy_from_original )) == null ? '' : __t) +
'"/>\r\n<div id="translated_' +
((__t = (field.field_type)) == null ? '' : __t) +
'_placeholder"></div>\r\n<input type="hidden" name="fields[' +
((__t = (field.field_type)) == null ? '' : __t) +
'][tid]" value="' +
((__t = (field.tid)) == null ? '' : __t) +
'">\r\n<input type="hidden" name="fields[' +
((__t = (field.field_type)) == null ? '' : __t) +
'][format]" value="base64">\r\n\r\n<div class="field_translation_complete">\r\n  <label><input class="icl_tm_finished js-field-translation-complete" name="fields[' +
((__t = (field.field_type)) == null ? '' : __t) +
'][finished]" type="checkbox" ';
 if (field.field_finished) { ;
__p += ' checked="checked" ';
 } ;
__p += ' />' +
((__t = (labels.translation_complete)) == null ? '' : __t) +
'</label>\r\n</div>\r\n\r\n';
 if (field.diff) { ;
__p += '\r\n<a class="js-toggle-diff toggle-diff">' +
((__t = (labels.show_diff)) == null ? '' : __t) +
'</a>\r\n' +
((__t = (field.diff)) == null ? '' : __t) +
'\r\n';
 } ;
__p += '\r\n\r\n\r\n';

}
return __p
};