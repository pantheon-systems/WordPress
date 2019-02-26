jQuery(document).ready(function(){
	jQuery( "#wcpgsk_accordion" ).accordion({
		icons: icons,
		heightStyle: "content",
		collapsible: true,
		active: false,
	});
	
	jQuery( "#wcpgsk_item_fieldset_accordion" ).accordion({
		icons: icons,
		heightStyle: "content",
		collapsible: true,
		active: false,
	});
	
});
/*
var icons = {
	header: "ui-icon-plusthick",
	activeHeader: "ui-icon-minusthick"
};
*/
var icons = {
	header: "ui-icon-circle-arrow-e",
	activeHeader: "ui-icon-circle-arrow-s"
};
