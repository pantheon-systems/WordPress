jQuery(document).ready(function($){
   

	//The chosen one
	$('.vc_ui-panel:not([data-vc-shortcode="vc_text_separator"]):not([data-vc-shortcode="vc_pie"]) .wpb_edit_form_elements select[name="color"], .wpb_edit_form_elements select[name="nectar_marker_color"], .wpb_edit_form_elements select[name="star_rating_color"], .wpb_edit_form_elements select[name="cta_button_style"], .wpb_edit_form_elements select[name="divider_color"], .vc_ui-panel[data-vc-shortcode="nectar_horizontal_list_item"] .wpb_edit_form_elements select[name="color"], .vc_ui-panel[data-vc-shortcode="nectar_video_lightbox"] .wpb_edit_form_elements select[name*="color"], .wpb_edit_form_elements select[name="hover_color"], .wpb_edit_form_elements select[name="icon_color"], .wpb_edit_form_elements select[name="color_1"], .wpb_edit_form_elements select[name="button_color_2"], .wpb_edit_form_elements select[name="button_color"]').chosen();

	$('.vc_ui-panel:not([data-vc-shortcode="vc_text_separator"]):not([data-vc-shortcode="vc_pie"]) .wpb_edit_form_elements select[name="color"], .wpb_edit_form_elements select[name="nectar_marker_color"], .wpb_edit_form_elements select[name="star_rating_color"], .wpb_edit_form_elements select[name="cta_button_style"], .wpb_edit_form_elements select[name="divider_color"], .vc_ui-panel[data-vc-shortcode="nectar_horizontal_list_item"] .wpb_edit_form_elements select[name="color"], .vc_ui-panel[data-vc-shortcode="nectar_video_lightbox"] .wpb_edit_form_elements select[name*="color"], .wpb_edit_form_elements select[name="hover_color"], .wpb_edit_form_elements select[name="icon_color"], .wpb_edit_form_elements select[name="color_1"], .wpb_edit_form_elements select[name="button_color_2"], .wpb_edit_form_elements select[name="button_color"]').on('chosen:showing_dropdown', function(evt, params) {

	   if(params && params.chosen && params.chosen.container) {
	   		params.chosen.container.find('ul.chosen-results').first().unbind('mousewheel.chosen DOMMouseScroll.chosen');
		}

	 });
});