jQuery(function($) {
	//On AME pages, move settings tabs after the heading. This is necessary to make them appear on the right side,
	//and WordPress breaks that by moving notices like "Settings saved" after the first H1 (see common.js).
	var menuEditorHeading = $('#ws_ame_editor_heading').first(),
		menuEditorTabs = $('.nav-tab-wrapper').first();
	menuEditorTabs = menuEditorTabs.add(menuEditorTabs.next('.clear'));
	if ((menuEditorHeading.length > 0) && (menuEditorTabs.length > 0)) {
		menuEditorTabs.insertAfter(menuEditorHeading);
	}
});

