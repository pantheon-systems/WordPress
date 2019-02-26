//(c) W-Shadow

/*global wsEditorData, defaultMenu, customMenu, _:false */

/**
 * @property wsEditorData
 * @property {boolean} wsEditorData.wsMenuEditorPro
 *
 * @property {object} wsEditorData.blankMenuItem
 * @property {object} wsEditorData.itemTemplates
 * @property {object} wsEditorData.customItemTemplate
 *
 * @property {string} wsEditorData.adminAjaxUrl
 * @property {string} wsEditorData.imagesUrl
 *
 * @property {string} wsEditorData.menuFormatName
 * @property {string} wsEditorData.menuFormatVersion
 *
 * @property {boolean} wsEditorData.hideAdvancedSettings
 * @property {boolean} wsEditorData.showExtraIcons
 * @property {boolean} wsEditorData.dashiconsAvailable
 * @property {string}  wsEditorData.submenuIconsEnabled
 * @property {Object}  wsEditorData.showHints
 *
 * @property {string} wsEditorData.hideAdvancedSettingsNonce
 * @property {string} wsEditorData.getPagesNonce
 * @property {string} wsEditorData.getPageDetailsNonce
 * @property {string} wsEditorData.disableDashboardConfirmationNonce
 *
 * @property {string} wsEditorData.captionShowAdvanced
 * @property {string} wsEditorData.captionHideAdvanced
 *
 * @property {string} wsEditorData.unclickableTemplateId
 * @property {string} wsEditorData.unclickableTemplateClass
 * @property {string} wsEditorData.embeddedPageTemplateId
 *
 * @property {string} wsEditorData.currentUserLogin
 * @property {string|null} wsEditorData.selectedActor
 *
 * @property {object} wsEditorData.actors
 * @property {string[]} wsEditorData.visibleUsers
 *
 * @property {object} wsEditorData.postTypes
 * @property {object} wsEditorData.taxonomies
 *
 * @property {string|null} wsEditorData.selectedMenu
 * @property {string|null} wsEditorData.selectedSubmenu
 *
 * @property {string} wsEditorData.setTestConfigurationNonce
 * @property {string} wsEditorData.testAccessNonce
 *
 * @property {boolean} wsEditorData.isDemoMode
 * @property {boolean} wsEditorData.isMasterMode
 */

wsEditorData.wsMenuEditorPro = !!wsEditorData.wsMenuEditorPro; //Cast to boolean.
var wsIdCounter = 0;

//A bit of black magic/hack to convince my IDE that wsAmeLodash is an alias for lodash.
window.wsAmeLodash = (function() {
	'use strict';
	if (typeof wsAmeLodash !== 'undefined') {
		return wsAmeLodash;
	}
	return _.noConflict();
})();

//These two properties must be objects, not arrays.
jQuery.each(['grant_access', 'hidden_from_actor'], function(unused, key) {
	'use strict';
	if (wsEditorData.blankMenuItem.hasOwnProperty(key) && !jQuery.isPlainObject(wsEditorData.blankMenuItem[key])) {
		wsEditorData.blankMenuItem[key] = {};
	}
});

AmeCapabilityManager = AmeActors;

/**
 * A utility for retrieving post and page titles.
 */
var AmePageTitles = (function($) {
	'use strict';

	var me = {}, cache = {};

	function getCacheKey(pageId, blogId) {
		return blogId + '_' + pageId;
	}

	/**
	 * Add a page title to the cache.
	 *
	 * @param {Number} pageId Post or page ID.
	 * @param {Number} blogId Blog ID.
	 * @param {String} title The title of the post or page.
	 */
	me.add = function(pageId, blogId, title) {
		cache[getCacheKey(pageId, blogId)] = title;
	};

	/**
	 * Get page title.
	 *
	 * Note: This method does not return the title. Instead, it calls the provided callback with the title
	 * as the first argument. The callback will be executed asynchronously if the title hasn't been cached yet.
	 *
	 * @param {Number} pageId
	 * @param {Number} blogId
	 * @param {Function} callback
	 */
	me.get = function(pageId, blogId, callback) {
		var key = getCacheKey(pageId, blogId);
		if (typeof cache[key] !== 'undefined') {
			callback(cache[key], pageId, blogId);
			return;
		}

		$.getJSON(
			wsEditorData.adminAjaxUrl,
			{
				'action' : 'ws_ame_get_page_details',
				'_ajax_nonce' : wsEditorData.getPageDetailsNonce,
				'post_id' : pageId,
				'blog_id' : blogId
			},
			function(details) {
				var title;
				if (typeof details.error !== 'undefined'){
					title = details.error;
				} else if ((typeof details !== 'object') || (typeof details.post_title === 'undefined')) {
					title = '< Server error >';
				} else {
					title = details.post_title;
				}
				cache[key] = title;

				callback(cache[key], pageId, blogId);
			}
		);
	};

	return me;
})(jQuery);

var AmeEditorApi = {};
window.AmeEditorApi = AmeEditorApi;


(function ($, _){
'use strict';

var actorSelectorWidget = new AmeActorSelector(AmeActors, wsEditorData.wsMenuEditorPro);

var itemTemplates = {
	templates: wsEditorData.itemTemplates,

	getTemplateById: function(templateId) {
		if (wsEditorData.itemTemplates.hasOwnProperty(templateId)) {
			return wsEditorData.itemTemplates[templateId];
		} else if ((templateId === '') || (templateId === 'custom')) {
			return wsEditorData.customItemTemplate;
		}
		return null;
	},

	getDefaults: function (templateId) {
		var template = this.getTemplateById(templateId);
		if (template) {
			return template.defaults;
		} else {
			return null;
		}
	},

	getDefaultValue: function (templateId, fieldName) {
		if (fieldName === 'template_id') {
			return null;
		}

		var defaults = this.getDefaults(templateId);
		if (defaults && (typeof defaults[fieldName] !== 'undefined')) {
			return defaults[fieldName];
		}
		return null;
	},

	hasDefaultValue: function(templateId, fieldName) {
		return (this.getDefaultValue(templateId, fieldName) !== null);
	}
};

/**
 * Set an input field to a value. The only difference from jQuery.val() is that
 * setting a checkbox to true/false will check/clear it.
 *
 * @param input
 * @param value
 */
function setInputValue(input, value) {
	if (input.attr('type') === 'checkbox'){
		input.prop('checked', value);
    } else {
        input.val(value);
    }
}

/**
 * Get the value of an input field. The only difference from jQuery.val() is that
 * checked/unchecked checkboxes will return true/false.
 *
 * @param input
 * @return {*}
 */
function getInputValue(input) {
	if (input.attr('type') === 'checkbox'){
		return input.is(':checked');
	}
	return input.val();
}


/*
 * Utility function for generating pseudo-random alphanumeric menu IDs.
 * Rationale: Simpler than atomically auto-incrementing or globally unique IDs.
 */
function randomMenuId(prefix, size){
	prefix = (typeof prefix === 'undefined') ? 'custom_item_' : prefix;
	size = (typeof size === 'undefined') ? 5 : size;

    var suffix = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < size; i++ ) {
        suffix += possible.charAt(Math.floor(Math.random() * possible.length));
    }

    return prefix + suffix;
}

function outputWpMenu(menu){
	var menuCopy = $.extend(true, {}, menu);
	var menuBox = $('#ws_menu_box');

	//Remove the current menu data
	menuBox.empty();
	$('#ws_submenu_box').empty();

	//Display the new menu
	var i = 0;
	for (var filename in menuCopy){
		if (!menuCopy.hasOwnProperty(filename)){
			continue;
		}
		outputTopMenu(menuCopy[filename]);
		i++;
	}

	//Automatically select the first top-level menu
	menuBox.find('.ws_menu:first').click();
}

/**
 * Load a menu configuration in the editor.
 * Note: All previous settings will be discarded without warning. Unsaved changes will be lost.
 *
 * @param {Object} adminMenu The menu structure to load.
 */
function loadMenuConfiguration(adminMenu) {
	//There are some menu properties that need to be objects, but PHP JSON-encodes empty associative
	//arrays as numeric arrays. We want them to be empty objects instead.
	if (adminMenu.hasOwnProperty('color_presets') && !$.isPlainObject(adminMenu.color_presets)) {
		adminMenu.color_presets = {};
	}

	var objectProperties = ['grant_access', 'hidden_from_actor'];
	//noinspection JSUnusedLocalSymbols
	function fixEmptyObjects(unused, menuItem) {
		for (var i = 0; i < objectProperties.length; i++) {
			var key = objectProperties[i];
			if (menuItem.hasOwnProperty(key) && !$.isPlainObject(menuItem[key])) {
				menuItem[key] = {};
			}
		}
		if (menuItem.hasOwnProperty('items')) {
			$.each(menuItem.items, fixEmptyObjects);
		}
	}
	$.each(adminMenu.tree, fixEmptyObjects);

	//Load color presets from the new configuration.
	if (typeof adminMenu.color_presets === 'object') {
		colorPresets = $.extend(true, {}, adminMenu.color_presets);
	} else {
		colorPresets = {};
	}
	wasPresetDropdownPopulated = false;

	//Load capabilities.
	AmeCapabilityManager.setGrantedCapabilities(_.get(adminMenu, 'granted_capabilities', {}));

	//Load general menu visibility.
	generalComponentVisibility = _.get(adminMenu, 'component_visibility', {});
	AmeEditorApi.refreshComponentVisibility();

	//Display the new admin menu.
	outputWpMenu(adminMenu.tree);
}

/*
 * Create edit widgets for a top-level menu and its submenus and append them all to the DOM.
 *
 * Inputs :
 *	menu - an object containing menu data
 *	afterNode - if specified, the new menu widget will be inserted after this node. Otherwise,
 *	            it will be added to the end of the list.
 * Outputs :
 *	Object with two fields - 'menu' and 'submenu' - containing the DOM nodes of the created widgets.
 */
function outputTopMenu(menu, afterNode){
	//Create the menu widget
	var menu_obj = buildMenuItem(menu, true);

	if ( (typeof afterNode !== 'undefined') && (afterNode !== null) ){
		$(afterNode).after(menu_obj);
	} else {
		menu_obj.appendTo('#ws_menu_box');
	}

	//Create a container for menu items, even if there are none
	var submenu = buildSubmenu(menu.items, menu_obj.attr('id'));
	submenu.appendTo('#ws_submenu_box');
	menu_obj.data('submenu_id', submenu.attr('id'));

	//Note: Update the menu only after its children are ready. It needs the submenu items to decide whether to display
	//the access checkbox as checked or indeterminate.
	updateItemEditor(menu_obj);

	return {
		'menu' : menu_obj,
		'submenu' : submenu
	};
}

/*
 * Create and populate a submenu container.
 */
function buildSubmenu(items, parentMenuId){
	//Create a container for menu items, even if there are none
	var submenu = $('<div class="ws_submenu" style="display:none;"></div>');
	submenu.attr('id', 'ws-submenu-'+(wsIdCounter++));

	if (parentMenuId) {
		submenu.data('parent_menu_id', parentMenuId);
	}

	//Only show menus that have items.
	//Skip arrays (with a length) because filled menus are encoded as custom objects.
	var entry = null;
	if (items) {
		$.each(items, function(index, item) {
			entry = buildMenuItem(item, false);
			if ( entry ){
				submenu.append(entry);
				updateItemEditor(entry);
			}
		});
	}

	//Make the submenu sortable
	makeBoxSortable(submenu);

	return submenu;
}

/**
 * Create an edit widget for a menu item.
 *
 * @param {Object} itemData
 * @param {Boolean} [isTopLevel] Specify if this is a top-level menu or a sub-menu item. Defaults to false (= sub-item).
 * @return {*} The created widget as a jQuery object.
 */
function buildMenuItem(itemData, isTopLevel) {
	isTopLevel = (typeof isTopLevel === 'undefined') ? false : isTopLevel;

	//Create the menu HTML
	var item = $('<div></div>')
		.attr('class', "ws_container")
		.attr('id', 'ws-menu-item-' + (wsIdCounter++))
		.data('menu_item', itemData)
		.data('field_editors_created', false);

	item.addClass(isTopLevel ? 'ws_menu' : 'ws_item');
	if ( itemData.separator ) {
		item.addClass('ws_menu_separator');
	}

	//Add a header and a container for property editors (to improve performance
	//the editors themselves are created later, when the user tries to access them
	//for the first time).
	var contents = [];
	var menuTitle = getFieldValue(itemData, 'menu_title', '');
	if (menuTitle === '') {
		menuTitle = '&nbsp;';
	}

	contents.push(
		'<div class="ws_item_head">',
			itemData.separator ? '' : '<a class="ws_edit_link"> </a><div class="ws_flag_container"> </div>',
			'<input type="checkbox" class="ws_actor_access_checkbox">',
			'<span class="ws_item_title">',
				formatMenuTitle(menuTitle),
			'&nbsp;</span>',

		'</div>',
		'<div class="ws_editbox" style="display: none;"></div>'
	);
	item.append(contents.join(''));

	//Apply flags based on the item's state
	var flags = ['hidden', 'unused', 'custom'];
	for (var i = 0; i < flags.length; i++) {
		setMenuFlag(item, flags[i], getFieldValue(itemData, flags[i], false));
	}

	if ( isTopLevel && !itemData.separator ){
		//Allow the user to drag menu items to top-level menus
		item.droppable({
			'hoverClass' : 'ws_menu_drop_hover',

			'accept' : (function(thing){
				return thing.hasClass('ws_item');
			}),

			'drop' : (function(event, ui){
				var droppedItemData = readItemState(ui.draggable);
				var new_item = buildMenuItem(droppedItemData, false);

				var sourceSubmenu = ui.draggable.parent();
				var submenu = $('#' + item.data('submenu_id'));
				submenu.append(new_item);

				if ( !event.ctrlKey ) {
					ui.draggable.remove();
				}

				updateItemEditor(new_item);

				//Moving an item can change aggregate menu permissions. Update the UI accordingly.
				updateParentAccessUi(submenu);
				updateParentAccessUi(sourceSubmenu);
			})
		});
	}

	return item;
}

function jsTrim(str){
	return str.replace(/^\s+|\s+$/g, "");
}

//Expose this handy tool to our other scripts.
AmeEditorApi.jsTrim = jsTrim;

function stripAllTags(input) {
	//Based on: http://phpjs.org/functions/strip_tags/
	var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
		commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
	return input.replace(commentsAndPhpTags, '').replace(tags, '');
}

function truncateString(input, maxLength, padding) {
	if (typeof padding === 'undefined') {
		padding = '';
	}

	if (input.length > maxLength) {
		input = input.substring(0, maxLength - 1) + padding;
	}

	return input;
}

/**
 * Format menu title for display in HTML.
 * Strips tags and truncates long titles.
 *
 * @param {String} title
 * @returns {String}
 */
function formatMenuTitle(title) {
	title = stripAllTags(title);

	//Compact whitespace.
	title = title.replace(/[\s\t\r\n]+/g, ' ');
	title = jsTrim(title);

	//The max. length was chosen empirically.
	title = truncateString(title, 34, '\u2026');
	return title;
}

//Editor field spec template.
var baseField = {
	caption : '[No caption]',
    standardCaption : true,
	advanced : false,
	type : 'text',
	defaultValue: '',
	onlyForTopMenus: false,
	addDropdown : false,
	visible: true,

	write: null,
	display: null,

	tooltip: null
};

/*
 * List of all menu fields that have an associated editor
 */
var knownMenuFields = {
	'menu_title' : $.extend({}, baseField, {
		caption : 'Menu title',
		display: function(menuItem, displayValue, input, containerNode) {
			//Update the header as well.
			containerNode.find('.ws_item_title').html(formatMenuTitle(displayValue) + '&nbsp;');
			return displayValue;
		},
		write: function(menuItem, value, input, containerNode) {
			menuItem.menu_title = value;
			containerNode.find('.ws_item_title').html(stripAllTags(input.val()) + '&nbsp;');
		}
	}),

	'template_id' : $.extend({}, baseField, {
		caption : 'Target page',
		type : 'select',
		options : (function(){
			//Generate name => id mappings for all item templates + the special "Custom" template.
			var itemTemplateIds = [];
			itemTemplateIds.push([wsEditorData.customItemTemplate.name, '']);

			for (var template_id in wsEditorData.itemTemplates) {
				if (wsEditorData.itemTemplates.hasOwnProperty(template_id)) {
					itemTemplateIds.push([wsEditorData.itemTemplates[template_id].name, template_id]);
				}
			}

			itemTemplateIds.sort(function(a, b) {
				if (a[1] === b[1]) {
					return 0;
				}

				//The "Custom" item is always first.
				if (a[1] === '') {
					return -1;
				} else if (b[1] === '') {
					return 1;
				}

				//Top-level items go before submenus.
				var aIsTop = (a[1].charAt(0) === '>') ? 1 : 0;
				var bIsTop = (b[1].charAt(0) === '>') ? 1 : 0;
				if (aIsTop !== bIsTop) {
					return bIsTop - aIsTop;
				}

				//Everything else is sorted by name, in alphabetical order.
				if (a[0] > b[0]) {
					return 1;
				} else if (a[0] < b[0]) {
					return -1;
				}
				return 0;
			});

			return itemTemplateIds;
		})(),

		write: function(menuItem, value, input, containerNode) {
			var oldTemplateId = menuItem.template_id;

			menuItem.template_id = value;
			menuItem.defaults = itemTemplates.getDefaults(menuItem.template_id);
		    menuItem.custom = (menuItem.template_id === '');

		    // The file/URL of non-custom items is read-only and equal to the default
		    // value. Rationale: simplifies menu generation, prevents some user mistakes.
		    if (menuItem.template_id !== '') {
			    menuItem.file = null;
		    }

		    // The new template might not have default values for some of the fields
		    // currently set to null (= "default"). In those cases, we need to make
		    // the current values explicit.
		    containerNode.find('.ws_edit_field').each(function(index, field){
			    field = $(field);
			    var fieldName = field.data('field_name');
			    var isSetToDefault = (menuItem[fieldName] === null);
			    var hasDefaultValue = itemTemplates.hasDefaultValue(menuItem.template_id, fieldName);

			    if (isSetToDefault && !hasDefaultValue) {
					var oldDefaultValue = itemTemplates.getDefaultValue(oldTemplateId, fieldName);
					if (oldDefaultValue !== null) {
						menuItem[fieldName] = oldDefaultValue;
					}
			    }
		    });
		}
	}),

	'embedded_page_id' : $.extend({}, baseField, {
		caption: 'Embedded page ID',
		defaultValue: 'Select page to display',
		type: 'text',
		visible: false, //Displayed on-demand.
		addDropdown: 'ws_embedded_page_selector',

		display: function(menuItem, displayValue, input) {
			//Only show this field if the "Embed WP page" template is selected.
			input.closest('.ws_edit_field').toggle(menuItem.template_id === wsEditorData.embeddedPageTemplateId);

			input.prop('readonly', true);
			var pageId = parseInt(getFieldValue(menuItem, 'embedded_page_id', 0), 10),
				blogId = parseInt(getFieldValue(menuItem, 'embedded_page_blog_id', 1), 10),
				formattedId = 'ID: ' + pageId;

			if (pageId <= 0) {
				return 'Select page =>';
			}

			if (blogId !== 1) {
				formattedId = formattedId + ', blog ID: ' + blogId;
			}
			displayValue = formattedId;

			AmePageTitles.get(pageId, blogId, function(title) {
				//If we retrieved the title via AJAX, the user might have selected a different page in the meantime.
				//Make sure it's still the same page before displaying the title.
				var currentPageId = parseInt(getFieldValue(menuItem, 'embedded_page_id', 0), 10),
					currentBlogId = parseInt(getFieldValue(menuItem, 'embedded_page_blog_id', 1), 10);
				if ((currentPageId !== pageId) || (currentBlogId !== blogId)) {
					return;
				}

				displayValue = title + ' (' + formattedId + ')';
				input.val(displayValue);
			});

			return displayValue;
		},

		write: function() {
			//The user cannot directly edit this field. We deliberately ignore writes.
		}
	}),

	'file' : $.extend({}, baseField, {
		caption: 'URL',
		display: function(menuItem, displayValue, input) {
			// The URL/file field is read-only for default menus. Also, since the "file"
			// field is usually set to a page slug or plugin filename for plugin/hook pages,
			// we display the dynamically generated "url" field here (i.e. the actual URL) instead.
			if (menuItem.template_id !== '') {
				input.attr('readonly', 'readonly');
				displayValue = itemTemplates.getDefaultValue(menuItem.template_id, 'url');
			} else {
				input.removeAttr('readonly');
			}
			return displayValue;
		},

		write: function(menuItem, value) {
			// A menu must always have a non-empty URL. If the user deletes the current value,
			// reset it to the old value.
			if (value === '') {
				value = menuItem.file;
			}
			// Default menus always point to the default file/URL.
			if (menuItem.template_id !== '') {
				value = null;
			}
			menuItem.file = value;
		}
	}),

	'access_level' : $.extend({}, baseField, {
		caption: 'Permissions',
		defaultValue: 'read',
		type: 'access_editor',
		visible: false, //Will be set to visible only in Pro version.

		display: function(menuItem) {
			//Permissions display is a little complicated and could use improvement.
			var requiredCap = getFieldValue(menuItem, 'access_level', '');
			var extraCap = getFieldValue(menuItem, 'extra_capability', '');

			var displayValue = (menuItem.template_id === '') ? '< Custom >' : requiredCap;
			if (extraCap !== '') {
				if (menuItem.template_id === '') {
					displayValue = extraCap;
				} else {
					displayValue = displayValue + '+' + extraCap;
				}
			}

			return displayValue;
		},

		write: function(menuItem) {
			//The required capability can't be directly edited and always equals the default.
			menuItem.access_level = null;
		}
	}),

	'required_capability_read_only' : $.extend({}, baseField, {
		caption: 'Required capability',
		defaultValue: 'none',
		type: 'text',
		tooltip: "Only users who have this capability can see the menu. "+
			"The capability can't be changed because it's usually hard-coded in WordPress or the plugin that created the menu."+
			"<br><br>Use the \"Extra capability\" field to restrict access to this menu.",

		visible: function(menuItem) {
			//Show only in the free version, on non-custom menus.
			return !wsEditorData.wsMenuEditorPro && (menuItem.template_id !== '');
		},

		display: function(menuItem, displayValue, input) {
			input.prop('readonly', true);
			return getFieldValue(menuItem, 'access_level', '');
		},

		write: function(menuItem, value) {
			//The required capability is read-only. Ignore writes.
		}
	}),

	'extra_capability' : $.extend({}, baseField, {
		caption: 'Extra capability',
		defaultValue: 'read',
		type: 'text',
		addDropdown: 'ws_cap_selector',
		tooltip: function(menuItem) {
			if (menuItem.template_id === '') {
				return 'Only users who have this capability can see the menu.';
			}
			return 'An additional capability check that is applied on top of the required capability.';
		},

		display: function(menuItem) {
			var requiredCap = getFieldValue(menuItem, 'access_level', '');
			var extraCap = getFieldValue(menuItem, 'extra_capability', '');

			//On custom menus, show the default required cap when no extra cap is selected.
			//Otherwise there would be no visible capability requirements at all.
			var displayValue = extraCap;
			if ((menuItem.template_id === '') && (extraCap === '')) {
				displayValue = requiredCap;
			}

			return displayValue;
		},

		write: function(menuItem, value) {
			value = jsTrim(value);

			//Reset to default if the user clears the input.
			if (value === '') {
				menuItem.extra_capability = null;
				return;
			}

			menuItem.extra_capability = value;
		}
	}),

	'appearance_heading' : $.extend({}, baseField, {
		caption: 'Appearance',
		advanced : true,
		onlyForTopMenus: false,
		type: 'heading',
		standardCaption: false,
		visible: false //Only visible in the Pro version.
	}),

	'icon_url' : $.extend({}, baseField, {
		caption: 'Icon URL',
		type : 'icon_selector',
		advanced : true,
		defaultValue: 'div',
		onlyForTopMenus: true,

		display: function(menuItem, displayValue, input, containerNode) {
			//Display the current icon in the selector.
			var cssClass = getFieldValue(menuItem, 'css_class', '');
			var iconUrl = getFieldValue(menuItem, 'icon_url', '', containerNode);
			displayValue = iconUrl;

			//When submenu icon visibility is set to "only if manually selected",
			//don't show the default submenu icons.
			var isDefault = (typeof menuItem.icon_url === 'undefined') || (menuItem.icon_url === null);
			if (isDefault && (wsEditorData.submenuIconsEnabled === 'if_custom') && containerNode.hasClass('ws_item')) {
				iconUrl = 'none';
				cssClass = '';
			}

			var selectButton = input.closest('.ws_edit_field').find('.ws_select_icon');
			var cssIcon = selectButton.find('.icon16');
			var imageIcon = selectButton.find('img');

			var matches = cssClass.match(/\b(ame-)?menu-icon-([^\s]+)\b/);
			var iconFontMatches = iconUrl && iconUrl.match(/^\s*((dashicons|ame-fa)-[a-z0-9\-]+)/);

			//Icon URL takes precedence over icon class.
			if ( iconUrl && iconUrl !== 'none' && iconUrl !== 'div' && !iconFontMatches ) {
				//Regular image icon.
				cssIcon.hide();
				imageIcon.prop('src', iconUrl).show();
			} else if ( iconFontMatches ) {
				cssIcon.removeClass().addClass('icon16');
				if ( iconFontMatches[2] === 'dashicons' ) {
					//Dashicon.
					cssIcon.addClass('dashicons ' + iconFontMatches[1]);
				} else if ( iconFontMatches[2] === 'ame-fa' ) {
					//FontAwesome icon.
					cssIcon.addClass('ame-fa ' + iconFontMatches[1]);
				}
				imageIcon.hide();
				cssIcon.show();
			} else if ( matches ) {
				//Other CSS-based icon.
				imageIcon.hide();
				var iconClass = (matches[1] ? matches[1] : '') + 'icon-' + matches[2];
				cssIcon.removeClass().addClass('icon16 ' + iconClass).show();
			} else {
				//This menu has no icon at all. This is actually a valid state
				//and WordPress will display a menu like that correctly.
				imageIcon.hide();
				cssIcon.removeClass().addClass('icon16').show();
			}

			return displayValue;
		}
	}),

	'colors' : $.extend({}, baseField, {
		caption: 'Color scheme',
		defaultValue: 'Default',
		type: 'color_scheme_editor',
		onlyForTopMenus: true,
		visible: false,
		advanced : true,

		display: function(menuItem, displayValue, input, containerNode) {
			var colors = getFieldValue(menuItem, 'colors', {}) || {};
			var colorList = containerNode.find('.ws_color_scheme_display');

			colorList.empty();
			var count = 0, maxColorsToShow = 7;

			$.each(colors, function(name, value) {
				if ( !value || (count >= maxColorsToShow) ) {
					return;
				}

				colorList.append(
					$('<span></span>').addClass('ws_color_display_item').css('background-color', value)
				);
				count++;
			});

			if (count === 0) {
				colorList.append('Default');
			}

			return 'Placeholder. You should never see this.';
		},

		write: function(menuItem) {
			//Menu colors can't be directly edited.
		}
	}),

	'html_heading' : $.extend({}, baseField, {
		caption: 'HTML',
		advanced : true,
		onlyForTopMenus: true,
		type: 'heading',
		standardCaption: false
	}),

	'open_in' : $.extend({}, baseField, {
		caption: 'Open in',
		advanced : true,
		type : 'select',
		options : [
			['Same window or tab', 'same_window'],
			['New window', 'new_window'],
			['Frame', 'iframe']
		],
		defaultValue: 'same_window',
		visible: false
	}),

	'iframe_height' : $.extend({}, baseField, {
		caption: 'Frame height (pixels)',
		advanced : true,
		visible: function(menuItem) {
			return wsEditorData.wsMenuEditorPro && (getFieldValue(menuItem, 'open_in') === 'iframe');
		},

		display: function(menuItem, displayValue, input) {
			input.prop('placeholder', 'Auto');
			if (displayValue === 0 || displayValue === '0') {
				displayValue = '';
			}
			return displayValue;
		},

		write: function(menuItem, value) {
			value = parseInt(value, 10);
			if (isNaN(value) || (value < 0)) {
				value = 0;
			}
			value = Math.round(value);

			if (value > 10000) {
				value = 10000;
			}

			if (value === 0) {
				menuItem.iframe_height = null;
			} else {
				menuItem.iframe_height = value;
			}

		}
	}),

	'css_class' : $.extend({}, baseField, {
		caption: 'CSS classes',
		advanced : true,
		onlyForTopMenus: true
	}),

	'hookname' : $.extend({}, baseField, {
		caption: 'ID attribute',
		advanced : true,
		onlyForTopMenus: true
	}),

	'page_properties_heading' : $.extend({}, baseField, {
		caption: 'Page',
		advanced : true,
		onlyForTopMenus: true,
		type: 'heading',
		standardCaption: false
	}),

	'page_heading' : $.extend({}, baseField, {
		caption: 'Page heading',
		advanced : true,
		onlyForTopMenus: false,
		visible: false
	}),

	'page_title' : $.extend({}, baseField, {
		caption: "Window title",
		standardCaption : true,
		advanced : true
	}),

	'is_always_open' : $.extend({}, baseField, {
		caption: 'Keep this menu expanded',
		advanced : true,
		onlyForTopMenus: true,
		type: 'checkbox',
		standardCaption: false
	})
};

AmeEditorApi.getItemDisplayUrl = function(menuItem) {
	var url = getFieldValue(menuItem, 'file', '');
	if (menuItem.template_id !== '') {
		//Use the template URL. It's a preset that can't be overridden.
		var defaultUrl = itemTemplates.getDefaultValue(menuItem.template_id, 'url');
		if (defaultUrl) {
			url = defaultUrl;
		}
	}
	return url;
};

/*
 * Create editors for the visible fields of a menu entry and append them to the specified node.
 */
function buildEditboxFields(fieldContainer, entry, isTopLevel){
	isTopLevel = (typeof isTopLevel === 'undefined') ? false : isTopLevel;

	var basicFields = $('<div class="ws_edit_panel ws_basic"></div>').appendTo(fieldContainer);
    var advancedFields = $('<div class="ws_edit_panel ws_advanced"></div>').appendTo(fieldContainer);

    if ( wsEditorData.hideAdvancedSettings ){
    	advancedFields.css('display', 'none');
    }

	for (var field_name in knownMenuFields){
		if (!knownMenuFields.hasOwnProperty(field_name)) {
			continue;
		}

		var fieldSpec = knownMenuFields[field_name];
		if (fieldSpec.onlyForTopMenus && !isTopLevel) {
			continue;
		}

		var field = buildEditboxField(entry, field_name, fieldSpec);
		if (field){
            if (fieldSpec.advanced){
                advancedFields.append(field);
            } else {
                basicFields.append(field);
            }
		}
	}

	//Add a link that shows/hides advanced fields
	fieldContainer.append(
		'<div class="ws_toggle_container"><a href="#" class="ws_toggle_advanced_fields"'+
		(wsEditorData.hideAdvancedSettings ? '' : ' style="display:none;" ' )+'>'+
		(wsEditorData.hideAdvancedSettings ? wsEditorData.captionShowAdvanced : wsEditorData.captionHideAdvanced)
		+'</a></div>'
	);
}

/*
 * Create an editor for a specified field.
 */
//noinspection JSUnusedLocalSymbols
function buildEditboxField(entry, field_name, field_settings){
	//Build a form field of the appropriate type
	var inputBox = null;
	var basicTextField = '<input type="text" class="ws_field_value">';
	//noinspection FallthroughInSwitchStatementJS
	switch(field_settings.type){
		case 'select':
			inputBox = $('<select class="ws_field_value">');
			var option = null;
			for( var index = 0; index < field_settings.options.length; index++ ){
				var optionTitle = field_settings.options[index][0];
				var optionValue = field_settings.options[index][1];

				option = $('<option>')
					.val(optionValue)
					.text(optionTitle);
				option.appendTo(inputBox);
			}
			break;

        case 'checkbox':
            inputBox = $('<label><input type="checkbox" class="ws_field_value"> <span class="ws_field_label_text">'+
                field_settings.caption + '</span></label>'
            );
            break;

		case 'access_editor':
			inputBox = $('<input type="text" class="ws_field_value" readonly="readonly">')
                .add('<input type="button" class="button ws_launch_access_editor" value="Edit...">');
			break;

		case 'icon_selector':
			//noinspection HtmlUnknownTag
			inputBox = $(basicTextField)
                .add('<button class="button ws_select_icon" title="Select icon"><div class="icon16 icon-settings"></div><img src="" style="display:none;"></button>');
			break;

		case 'color_scheme_editor':
			inputBox = $('<span class="ws_color_scheme_display">Placeholder</span>')
				.add('<input type="button" class="button ws_open_color_editor" value="Edit...">');
			break;

		case 'heading':
			inputBox = $('<span>' + field_settings.caption + '</span>');
			break;

		case 'text':
			/* falls through */
		default:
			inputBox = $(basicTextField);
	}


	var className = "ws_edit_field ws_edit_field-"+field_name;
	if (field_settings.addDropdown){
		className += ' ws_has_dropdown';
	}
	if (!field_settings.standardCaption) {
		className += ' ws_no_field_caption';
	}
	if (field_settings.type === 'heading') {
		className += ' ws_field_group_heading';
	}

	var caption = '';
	if (field_settings.standardCaption) {
		var tooltip = '';
		if (field_settings.tooltip !== null) {
			tooltip = ' <a class="ws_field_tooltip_trigger"><div class="dashicons dashicons-info"></div></a>';
		}
		caption = '<span class="ws_field_label_text">' + field_settings.caption + tooltip + '</span><br>';
	}
	var editField = $('<div>' + caption + '</div>')
		.attr('class', className)
		.append(inputBox);

	if (field_settings.addDropdown) {
		//Add a dropdown button
		var dropdownId = field_settings.addDropdown;
		editField.append(
			$('<input type="button" value="&#9660;">')
				.addClass('button ws_dropdown_button ' + dropdownId + '_trigger')
				.attr('tabindex', '-1')
				.data('dropdownId', dropdownId)
		);
	}

	editField
		.append(
			$('<img class="ws_reset_button" title="Reset to default value" src="">')
				.attr('src', wsEditorData.imagesUrl + '/transparent16.png')
		).data('field_name', field_name);

	var visible = true;
	if (typeof field_settings.visible === 'function') {
		visible = field_settings.visible(entry, field_name);
	} else {
		visible = field_settings.visible;
	}
	if (!visible) {
		editField.css('display', 'none');
	}

	return editField;
}

/**
 * Get the parent menu of a menu item.
 *
 * @param containerNode A DOM element as a jQuery object.
 * @return {JQuery} Parent container node, or an empty jQuery set.
 */
function getParentMenuNode(containerNode) {
	var submenu = containerNode.closest('.ws_submenu', '#ws_menu_editor'),
		parentId = submenu.data('parent_menu_id');
	if (parentId) {
		return $('#' + parentId);
	} else {
		return $([]);
	}
}

/**
 * Get all submenu items of a menu item.
 *
 * @param {JQuery} containerNode
 * @return {JQuery} A list of submenu item container nodes, or an empty set.
 */
function getSubmenuItemNodes(containerNode) {
	var subMenuId = containerNode.data('submenu_id');
	if (subMenuId) {
		return $('#' + subMenuId).find('.ws_container');
	} else {
		return $([]);
	}
}

/**
 * Apply a callback recursively to a menu item and all of its children, in depth-first order.
 * The callback will be invoked with two arguments: (containerNode, menuItem).
 *
 * @param containerNode
 * @param {Function} callback
 */
function walkMenuTree(containerNode, callback) {
	getSubmenuItemNodes(containerNode).each(function() {
		walkMenuTree($(this), callback);
	});
	callback(containerNode, containerNode.data('menu_item'));
}

/**
 * Update the UI elements that that indicate whether the currently selected
 * actor can access a menu item.
 *
 * @param containerNode
 */
function updateActorAccessUi(containerNode) {
	//Update the permissions checkbox & UI
	var menuItem = containerNode.data('menu_item');
	if (actorSelectorWidget.selectedActor !== null) {
		var hasAccess = actorCanAccessMenu(menuItem, actorSelectorWidget.selectedActor);
		var hasCustomPermissions = actorHasCustomPermissions(menuItem, actorSelectorWidget.selectedActor);

		var isOverrideActive = !hasAccess && getFieldValue(menuItem, 'restrict_access_to_items', false);

		//Check if the parent menu has the "hide all submenus if this is hidden" override in effect.
		var currentChild = containerNode, parentNode, parentItem;
		do {
			parentNode = getParentMenuNode(currentChild);
			parentItem = parentNode.data('menu_item');
			if (
				parentItem
				&& getFieldValue(parentItem, 'restrict_access_to_items', false)
				&& !actorCanAccessMenu(parentItem, actorSelectorWidget.selectedActor)
			) {
				hasAccess = false;
				isOverrideActive = true;
				break;
			}
			currentChild = parentNode;
		} while (parentNode.length > 0);

		var checkbox = containerNode.find('.ws_actor_access_checkbox');
		checkbox.prop('checked', hasAccess);

		//Display the checkbox in an indeterminate state if the actual menu permissions are unknown
		//because it uses meta capabilities.
		var isIndeterminate = (hasAccess === null);
		//Also show it as indeterminate if some items of this menu are hidden and some are visible,
		//or if their permissions don't match this menu's permissions.
		var submenuItems = getSubmenuItemNodes(containerNode);
		if ((submenuItems.length > 0) && !isOverrideActive)  {
			var differentPermissions = false;
			submenuItems.each(function() {
				var item = $(this).data('menu_item');
				if ( !item ) { //Skip placeholder items created by drag & drop operations.
					return true;
				}
				var hasSubmenuAccess = actorCanAccessMenu(item, actorSelectorWidget.selectedActor);
				if (hasSubmenuAccess !== hasAccess) {
					differentPermissions = true;
					return false;
				}
				return true;
			});

			if (differentPermissions) {
				isIndeterminate = true;
			}
		}
		checkbox.prop('indeterminate', isIndeterminate);

		if (isIndeterminate && (hasAccess === null)) {
			setMenuFlag(
				containerNode,
				'uncertain_meta_cap',
				true,
				"This item might be visible.\n"
				+ "The plugin cannot reliably detect if \"" + actorSelectorWidget.selectedDisplayName
				+ "\" has the \"" + getFieldValue(menuItem, 'access_level', '[No capability]')
				+ "\" capability. If you need to hide the item, try checking and then unchecking it."
			);
		} else {
			setMenuFlag(containerNode, 'uncertain_meta_cap', false);
		}

		containerNode.toggleClass('ws_is_hidden_for_actor', !hasAccess);
		containerNode.toggleClass('ws_has_custom_permissions_for_actor', hasCustomPermissions);
		setMenuFlag(containerNode, 'custom_actor_permissions', hasCustomPermissions);
		setMenuFlag(containerNode, 'hidden_from_others', false);
	} else {
		containerNode.removeClass('ws_is_hidden_for_actor ws_has_custom_permissions_for_actor');
		setMenuFlag(containerNode, 'custom_actor_permissions', false);
		setMenuFlag(containerNode, 'uncertain_meta_cap', false);

		var currentUserActor = 'user:' + wsEditorData.currentUserLogin;
		var otherActors = _(wsEditorData.actors).keys().without(currentUserActor, 'special:super_admin').value(),
			hiddenFromCurrentUser = ! actorCanAccessMenu(menuItem, currentUserActor),
			hasAccessToThisItem = _.curry(actorCanAccessMenu, 2)(menuItem),
			hiddenFromOthers = _.every(otherActors, function(actorId) {
				return (hasAccessToThisItem(actorId) === false);
			}),
			visibleForSuperAdmin = AmeActors.isMultisite && actorCanAccessMenu(menuItem, 'special:super_admin');

		setMenuFlag(
			containerNode,
			'hidden_from_others',
			hiddenFromOthers,
			hiddenFromCurrentUser
				? 'Hidden from everyone'
				: ('Hidden from everyone except you' + (visibleForSuperAdmin ? ' and Super Admins' : ''))
		);
	}

	//Update the "hidden" flag.
	setMenuFlag(containerNode, 'hidden', itemHasHiddenFlag(menuItem, actorSelectorWidget.selectedActor));
}

/**
 * Like updateActorAccessUi() except it updates the specified menu's parent, not the menu itself.
 * If the menu has no parent (i.e. it's a top-level menu), this function does nothing.
 *
 * @param containerNode Either a menu item or a submenu container.
 */
function updateParentAccessUi(containerNode) {
	var submenu;
	if ( containerNode.is('.ws_submenu') ) {
		submenu = containerNode;
	} else {
		submenu = containerNode.parent();
	}

	var parentId = submenu.data('parent_menu_id');
	if (parentId) {
		updateActorAccessUi($('#' + parentId));
	}
}

/**
 * Update an edit widget with the current menu item settings.
 *
 * @param {JQuery} containerNode
 */
function updateItemEditor(containerNode) {
	var menuItem = containerNode.data('menu_item');

	//Apply flags based on the item's state.
	var flags = ['hidden', 'unused', 'custom'];
	for (var i = 0; i < flags.length; i++) {
		setMenuFlag(containerNode, flags[i], getFieldValue(menuItem, flags[i], false));
	}

	//Update the permissions checkbox & other actor-specific UI
	updateActorAccessUi(containerNode);

	//Update all input fields with the current values.
	containerNode.find('.ws_edit_field').each(function(index, field) {
		field = $(field);
		var fieldName = field.data('field_name');
		var input = field.find('.ws_field_value').first();

		var hasADefaultValue = itemTemplates.hasDefaultValue(menuItem.template_id, fieldName);
		var defaultValue = getDefaultValue(menuItem, fieldName, null, containerNode);
		var isDefault = hasADefaultValue && ((typeof menuItem[fieldName] === 'undefined') || (menuItem[fieldName] === null));

        if (fieldName === 'access_level') {
            isDefault = (getFieldValue(menuItem, 'extra_capability', '') === '')
				&& isEmptyObject(menuItem.grant_access)
				&& (!getFieldValue(menuItem, 'restrict_access_to_items', false));
        } else if (fieldName === 'required_capability_read_only') {
        	isDefault = true;
	        hasADefaultValue = true;
        }

		field.toggleClass('ws_has_no_default', !hasADefaultValue);
		field.toggleClass('ws_input_default', isDefault);

		var displayValue = isDefault ? defaultValue : menuItem[fieldName];
		if (knownMenuFields[fieldName].display !== null) {
			displayValue = knownMenuFields[fieldName].display(menuItem, displayValue, input, containerNode);
		}

        setInputValue(input, displayValue);

		//Store the value to help with change detection.
		if (input.length > 0) {
			$.data(input.get(0), 'ame_last_display_value', displayValue);
		}

		if (typeof (knownMenuFields[fieldName].visible) === 'function') {
			var isFieldVisible = knownMenuFields[fieldName].visible(menuItem, fieldName);
			if (isFieldVisible) {
				field.css('display', '');
			} else {
				field.css('display', 'none');
			}
		}
    });
}

function isEmptyObject(obj) {
    for (var prop in obj) {
        if (obj.hasOwnProperty(prop)) {
            return false;
        }
    }
    return true;
}

/**
 * Get the current value of a single menu field.
 *
 * If the specified field is not set, this function will attempt to retrieve it
 * from the "defaults" property of the menu object. If *that* fails, it will return
 * the value of the optional third argument defaultValue.
 *
 * @param {Object} entry
 * @param {string} fieldName
 * @param {*} [defaultValue]
 * @param {JQuery} [containerNode]
 * @return {*}
 */
function getFieldValue(entry, fieldName, defaultValue, containerNode){
	if ( (typeof entry[fieldName] === 'undefined') || (entry[fieldName] === null) ) {
		return getDefaultValue(entry, fieldName, defaultValue, containerNode);
	} else {
		return entry[fieldName];
	}
}

AmeEditorApi.getFieldValue = getFieldValue;

/**
 * Get the default value of a menu field.
 *
 * @param {Object} entry
 * @param {String} fieldName
 * @param {*} [defaultValue]
 * @param {JQuery} [containerNode]
 * @returns {*}
 */
function getDefaultValue(entry, fieldName, defaultValue, containerNode) {
	//By default, a submenu item has the same icon as its parent.
	if ((fieldName === 'icon_url') && containerNode && (wsEditorData.submenuIconsEnabled !== 'never')) {
		var parentContainerNode = getParentMenuNode(containerNode),
			parentMenuItem = parentContainerNode.data('menu_item');
		if (parentMenuItem) {
			return getFieldValue(parentMenuItem, fieldName, defaultValue, parentContainerNode);
		}
	}

	//Use the custom menu title as the page title if the default page title matches the default menu title.
	//Note that if the page title is an empty string (''), WP automatically uses the menu title. So we do the same.
	if ((fieldName === 'page_title') && (entry.template_id !== '')) {
		var defaultPageTitle = itemTemplates.getDefaultValue(entry.template_id, 'page_title'),
			defaultMenuTitle = itemTemplates.getDefaultValue(entry.template_id, 'menu_title'),
			customMenuTitle = entry['menu_title'];

		if (
			(customMenuTitle !== null)
			&& (customMenuTitle !== '')
			&& ((defaultPageTitle === '') || (defaultMenuTitle === defaultPageTitle))
		) {
			return customMenuTitle;
		}
	}

	if (typeof defaultValue === 'undefined') {
		defaultValue = null;
	}

	//Known templates take precedence.
	if ((entry.template_id === '') || (typeof itemTemplates.templates[entry.template_id] !== 'undefined')) {
		var templateDefault = itemTemplates.getDefaultValue(entry.template_id, fieldName);
		return (templateDefault !== null) ? templateDefault : defaultValue;
	}

	if (fieldName === 'template_id') {
		return null;
	}

	//Separators can have their own defaults, independent of templates.
	var hasDefault = (typeof entry.defaults !== 'undefined') && (typeof entry.defaults[fieldName] !== 'undefined');
	if (hasDefault){
		return entry.defaults[fieldName];
	}

	return defaultValue;
}

/*
 * Make a menu container sortable
 */
function makeBoxSortable(menuBox){
	//Make the submenu sortable
	menuBox.sortable({
		items: '> .ws_container',
		cursor: 'move',
		dropOnEmpty: true,
		cancel : '.ws_editbox, .ws_edit_link',

		placeholder: 'ws_container ws_sortable_placeholder',
		forcePlaceholderSize: true,

		stop: function(even, ui) {
			//Fix incorrect item overlap caused by jQuery.sortable applying the initial z-index as an inline style.
			ui.item.css('z-index', '');

			//Fix submenu container height. It should be tall enough to reach the selected parent menu.
			if (ui.item.hasClass('ws_menu') && ui.item.hasClass('ws_active')) {
				AmeEditorApi.updateSubmenuBoxHeight(ui.item);
			}
		}
	});
}

/**
 * Iterates over all menu items invoking a callback for each item.
 *
 * The callback will be passed two arguments: the menu item and its UI container node (a jQuery object).
 * You can stop iteration by returning false from the callback.
 *
 * @param {Function} callback
 * @param {boolean} [skipSeparators] Defaults to true. Set to false to include separators in the iteration.
 */
AmeEditorApi.forEachMenuItem = function(callback, skipSeparators) {
	if (typeof skipSeparators === 'undefined') {
		skipSeparators = true;
	}

	$('#ws_menu_editor').find('.ws_container').each(function() {
		var containerNode = $(this);
		if ( !(skipSeparators && containerNode.hasClass('ws_menu_separator')) ) {
			return callback(containerNode.data('menu_item'), containerNode);
		}
	});
};

/**
 * Select the first menu item that has the specified URL.
 *
 * @param {string} boxSelector
 * @param {string} url
 * @param {boolean|null} [expandProperties]
 * @returns {JQuery}
 */
AmeEditorApi.selectMenuItemByUrl = function(boxSelector, url, expandProperties) {
	if (typeof expandProperties === 'undefined') {
		expandProperties = null;
	}

	var box = $(boxSelector);
	if (box.is('#ws_submenu_box')) {
		box = box.find('.ws_submenu:visible').first();
	}

	var containerNode =
		box.find('.ws_container')
		.filter(function() {
			var itemUrl = AmeEditorApi.getItemDisplayUrl($(this).data('menu_item'));
			return (itemUrl === url);
		})
		.first();

	if (containerNode.length > 0) {
		AmeEditorApi.selectItem(containerNode);

		if (expandProperties !== null) {
			var expandLink = containerNode.find('.ws_edit_link').first();
			if (expandLink.hasClass('ws_edit_link_expanded') !== expandProperties) {
				expandLink.click();
			}
		}
	}
	return containerNode;
};

/***************************************************************************
                       Parsing & encoding menu inputs
 ***************************************************************************/

/**
 * Encode the current menu structure as JSON
 *
 * @return {String} A JSON-encoded string representing the current menu tree loaded in the editor.
 */
function encodeMenuAsJSON(tree){
	if (typeof tree === 'undefined' || !tree) {
		tree = readMenuTreeState();
	}
	tree.format = {
		name: wsEditorData.menuFormatName,
		version: wsEditorData.menuFormatVersion
	};

	//Compress the admin menu.
	tree = compressMenu(tree);

	return $.toJSON(tree);
}

function readMenuTreeState(){
	var tree = {};
	var menuPosition = 0;
	var itemsByFilename = {};

	//Gather all menus and their items
	$('#ws_menu_box').find('.ws_menu').each(function() {
		var containerNode = this;
		var menu = readItemState(containerNode, menuPosition++);

		//Attach the current menu to the main structure.
		var filename = getFieldValue(menu, 'file');

		//Give unclickable items unique keys.
		if (menu.template_id === wsEditorData.unclickableTemplateId) {
			ws_paste_count++;
			filename = '#' + wsEditorData.unclickableTemplateClass + '-' + ws_paste_count;
		} else if (menu.template_id === wsEditorData.embeddedPageTemplateId) {
			ws_paste_count++;
			filename = '#embedded-page-' + ws_paste_count;
		}

		//Prevent the user from saving top level items with duplicate URLs.
		//WordPress indexes the submenu array by parent URL and AME uses a {url : menu_data} hashtable internally.
		//Duplicate URLs would cause problems for both.
		if (itemsByFilename.hasOwnProperty(filename)) {
			throw {
				code: 'duplicate_top_level_url',
				message: 'Error: Found a duplicate URL! All top level menus must have unique URLs.',
				duplicates: [itemsByFilename[filename], containerNode]
			};
		}

		tree[filename] = menu;
		itemsByFilename[filename] = containerNode;
	});

	AmeCapabilityManager.pruneGrantedUserCapabilities();

	return {
		tree: tree,
		color_presets: $.extend(true, {}, colorPresets),
		granted_capabilities: AmeCapabilityManager.getGrantedCapabilities(),
		component_visibility: $.extend(true, {}, generalComponentVisibility)
	};
}

/**
 * Losslessly compress the admin menu configuration.
 * 
 * This is a JS port of the ameMenu::compress() function defined in /includes/menu.php.
 * 
 * @param {Object} adminMenu
 * @returns {Object}
 */
function compressMenu(adminMenu) {
	var common = {
		properties: _.omit(wsEditorData.blankMenuItem, ['defaults']),
		basic_defaults: _.clone(_.get(wsEditorData.blankMenuItem, 'defaults', {})),
		custom_item_defaults: _.clone(itemTemplates.getTemplateById('').defaults)
	};

	adminMenu.format.compressed = true;
	adminMenu.format.common = common;

	function compressItem(item) {
		//These empty arrays can be dropped.
		if ( _.isEmpty(item['grant_access']) ) {
			delete item['grant_access'];
		}
		if ( _.isEmpty(item['items']) ) {
			delete item['items'];
		}

		//Normal and custom menu items have different defaults.
		//Remove defaults that are the same for all items of that type.
		var defaults = _.get(item, 'custom', false) ? common['custom_item_defaults'] : common['basic_defaults'];
		if ( _.has(item, 'defaults') ) {
			_.forEach(defaults, function(value, key) {
				if (_.has(item['defaults'], key) && (item['defaults'][key] === value)) {
					delete item['defaults'][key];
				}
			});
		}

		//Remove properties that match the common values.
		_.forEach(common['properties'], function(value, key) {
			if (_.has(item, key) && (item[key] === value)) {
				delete item[key];
			}
		});

		return item;
	}

	adminMenu.tree = _.mapValues(adminMenu.tree, function(topMenu) {
		topMenu = compressItem(topMenu);
		if (typeof topMenu.items !== 'undefined') {
			topMenu.items = _.map(topMenu.items, compressItem);
		}
		return topMenu;
	});

	return adminMenu;
}

AmeEditorApi.readMenuTreeState = readMenuTreeState;
AmeEditorApi.encodeMenuAsJson = encodeMenuAsJSON;

/**
 * Extract the current menu item settings from its editor widget.
 *
 * @param itemDiv DOM node containing the editor widget, usually with the .ws_item or .ws_menu class.
 * @param {Number} [position] Menu item position among its sibling menu items. Defaults to zero.
 * @return {Object} A menu object in the tree format.
 */
function readItemState(itemDiv, position){
	position = (typeof position === 'undefined') ? 0 : position;

	itemDiv = $(itemDiv);
	var item = $.extend(true, {}, wsEditorData.blankMenuItem, itemDiv.data('menu_item'), readAllFields(itemDiv));

	item.defaults = itemDiv.data('menu_item').defaults;

	//Save the position data
	item.position = position;
	item.defaults.position = position; //The real default value will later overwrite this

	item.separator = itemDiv.hasClass('ws_menu_separator');
	item.custom = menuHasFlag(itemDiv, 'custom');

	//Gather the menu's sub-items, if any
	item.items = [];
	var subMenuId = itemDiv.data('submenu_id');
	if (subMenuId) {
		var itemPosition = 0;
		$('#' + subMenuId).find('.ws_item').each(function () {
			var sub_item = readItemState(this, itemPosition++);
			item.items.push(sub_item);
		});
	}

	return item;
}

/*
 * Extract the values of all menu/item fields present in a container node
 *
 * Inputs:
 *	container - a jQuery collection representing the node to read.
 */
function readAllFields(container){
	if ( !container.hasClass('ws_container') ){
		container = container.closest('.ws_container');
	}

	if ( !container.data('field_editors_created') ){
		return container.data('menu_item');
	}

	var state = {};

	//Iterate over all fields of the item
	container.find('.ws_edit_field').each(function() {
		var field = $(this);

		//Get the name of this field
		var field_name = field.data('field_name');
		//Skip if unnamed
		if (!field_name) {
			return true;
		}

		//Hackety-hack. The "Page" input is for display purposes and contains more than just the ID. Skip it.
		//Eventually we'll need a better way to handle this.
		if (field_name === 'embedded_page_id') {
			return true;
		}
		//Headings contain no useful data.
		if (field.hasClass('ws_field_group_heading')) {
			return true;
		}

		//Find the field (usually an input or select element).
		var input_box = field.find('.ws_field_value');

		//Save null if default used, custom value otherwise
		if (field.hasClass('ws_input_default')){
			state[field_name] = null;
		} else {
			state[field_name] = getInputValue(input_box);
		}
		return true;
	});

    //Permission settings are not stored in the visible access_level field (that's just for show),
    //so do not attempt to read them from there.
    state.access_level = null;

	return state;
}


/***************************************************************************
 Flag manipulation
 ***************************************************************************/

var item_flags = {
	'custom': 'This is a custom menu item',
	'unused': 'This item was added since the last time you saved menu settings.',
	'hidden': 'Cosmetically hidden',
	'custom_actor_permissions': "The selected role has custom permissions for this item.",
	'hidden_from_others': 'Hidden from everyone except you.',
	'uncertain_meta_cap': 'The plugin cannot detect if this item is visible by default.'
};

function setMenuFlag(item, flag, state, title) {
	title = title || item_flags[flag];
	item = $(item);

	var item_class = 'ws_' + flag;
	var img_class = 'ws_' + flag + '_flag';

	item.toggleClass(item_class, state);
	if (state) {
		//Add the flag image.
		var flag_container = item.find('.ws_flag_container');
		var image = flag_container.find('.' + img_class);
		if (image.length === 0) {
			image = $('<div></div>').addClass('ws_flag').addClass(img_class);
			flag_container.append(image);
		}
		image.attr('title', title);
	} else {
		//Remove the flag image.
		item.find('.' + img_class).remove();
	}
}

function menuHasFlag(item, flag){
	return $(item).hasClass('ws_'+flag);
}

//The "hidden" flag is special. There's both a global version and one that's actor-specific.

/**
 * Check if a menu item is hidden from an actor.
 * This function only checks the "hidden" and "hidden_from_actor" flags, not permissions.
 *
 * @param {Object} menuItem
 * @param {string|null} actor
 * @returns {boolean}
 */
function itemHasHiddenFlag(menuItem, actor) {
	var isHidden = false,
		userActors,
		userPrefix = 'user:',
		userLogin;

	//(Only) A globally hidden item is hidden from everyone.
	if ((actor === null) || menuItem.hidden) {
		return menuItem.hidden;
	}

	if (actor.substr(0, userPrefix.length) === userPrefix) {
		//You can set an exception for a specific user. It takes precedence.
		if (menuItem.hidden_from_actor.hasOwnProperty(actor)) {
			isHidden = menuItem.hidden_from_actor[actor];
		} else {
			//Otherwise the item is hidden only if it is hidden from all of the user's roles.
			userLogin = actorSelectorWidget.selectedActor.substr(userPrefix.length);
			userActors = AmeCapabilityManager.getGroupActorsFor(userLogin);
			for (var i = 0; i < userActors.length; i++) {
				if (menuItem.hidden_from_actor.hasOwnProperty(userActors[i]) && menuItem.hidden_from_actor[userActors[i]]) {
					isHidden = true;
				} else {
					isHidden = false;
					break;
				}
			}
		}
	} else {
		//Roles and the super admin are straightforward.
		isHidden = menuItem.hidden_from_actor.hasOwnProperty(actor) && menuItem.hidden_from_actor[actor];
	}

	return isHidden;
}

/**
 * Toggle menu visibility without changing its permissions.
 *
 * Applies to the selected actor, or all actors if no actor is selected.
 *
 * @param {JQuery} selection A menu container node.
 * @param {boolean} [isHidden] Optional. True = hide the menu, false = show the menu.
 */
function toggleItemHiddenFlag(selection, isHidden) {
	var menuItem = selection.data('menu_item');

	//By default, invert the current state.
	if (typeof isHidden === 'undefined') {
		isHidden = !itemHasHiddenFlag(menuItem, actorSelectorWidget.selectedActor);
	}

	//Mark the menu as hidden/visible
	if (actorSelectorWidget.selectedActor === null) {
		//For ALL roles and users.
		menuItem.hidden = isHidden;
		menuItem.hidden_from_actor = {};
	} else {
		//Just for the current role.
		if (isHidden) {
			menuItem.hidden_from_actor[actorSelectorWidget.selectedActor] = true;
		} else {
			if (actorSelectorWidget.selectedActor.indexOf('user:') === 0) {
				//User-specific exception. Lets you can hide a menu from all admins but leave it visible to yourself.
				menuItem.hidden_from_actor[actorSelectorWidget.selectedActor] = false;
			} else {
				delete menuItem.hidden_from_actor[actorSelectorWidget.selectedActor];
			}
		}

		//When the user un-hides a menu that was globally hidden via the "hidden" flag, we must remove
		//that flag but also make sure the menu stays hidden from other roles.
		if (!isHidden && menuItem.hidden) {
			menuItem.hidden = false;
			$.each(wsEditorData.actors, function(otherActor) {
				if (otherActor !== actorSelectorWidget.selectedActor) {
					menuItem.hidden_from_actor[otherActor] = true;
				}
			});
		}
	}
	setMenuFlag(selection, 'hidden', isHidden);

	//Also mark all of it's submenus as hidden/visible
	var submenuId = selection.data('submenu_id');
	if (submenuId) {
		$('#' + submenuId + ' .ws_item').each(function(){
			toggleItemHiddenFlag($(this), isHidden);
		});
	}
}

/***********************************************************
                  Capability manipulation
 ************************************************************/

function actorCanAccessMenu(menuItem, actor) {
	if (!$.isPlainObject(menuItem.grant_access)) {
		menuItem.grant_access = {};
	}

	//By default, any actor that has the required cap has access to the menu.
	//Users can override this on a per-menu basis.
	var requiredCap = getFieldValue(menuItem, 'access_level', '< Error: access_level is missing! >');
	var actorHasAccess = false;
	if (menuItem.grant_access.hasOwnProperty(actor)) {
		actorHasAccess = menuItem.grant_access[actor];
	} else {
		actorHasAccess = AmeCapabilityManager.hasCap(actor, requiredCap, menuItem.grant_access);
	}
	return actorHasAccess;
}

AmeEditorApi.actorCanAccessMenu = actorCanAccessMenu;

function actorHasCustomPermissions(menuItem, actor) {
	if (menuItem.grant_access && menuItem.grant_access.hasOwnProperty && menuItem.grant_access.hasOwnProperty(actor)) {
		return (menuItem.grant_access[actor] !== null);
	}
	return false;
}

/**
 * @param containerNode
 * @param {string|Object.<string, boolean>} actor
 * @param {boolean} [allowAccess]
 */
function setActorAccess(containerNode, actor, allowAccess) {
	var menuItem = containerNode.data('menu_item');

	//grant_access comes from PHP, which JSON-encodes empty assoc. arrays as arrays.
	//However, we want it to be a dictionary.
	if (!$.isPlainObject(menuItem.grant_access)) {
		menuItem.grant_access = {};
	}

	if (typeof actor === 'string') {
		menuItem.grant_access[actor] = Boolean(allowAccess);
	} else {
		_.assign(menuItem.grant_access, actor);
	}
}

/**
 * Make a menu item inaccessible to everyone except a particular actor.
 *
 * Will not change access settings for actors that are more specific than the input actor.
 * For example, if the input actor is a "role:", this function will only disable other roles,
 * but will leave "user:" actors untouched.
 *
 * @param {Object} menuItem
 * @param {String} actor
 * @return {Object}
 */
function denyAccessForAllExcept(menuItem, actor) {
	//grant_access comes from PHP, which JSON-encodes empty assoc. arrays as arrays.
	//However, we want it to be a dictionary.
	if (!$.isPlainObject(menuItem.grant_access)) {
		menuItem.grant_access = {};
	}

	$.each(wsEditorData.actors, function(otherActor) {
		//If the input actor is more or equally specific...
		if ((actor === null) || (AmeActorManager.compareActorSpecificity(actor, otherActor) >= 0)) {
			menuItem.grant_access[otherActor] = false;
		}
	});

	if (actor !== null) {
		menuItem.grant_access[actor] = true;
	}
	return menuItem;
}

/***************************************************************************
 Event handlers
 ***************************************************************************/

//Cut & paste stuff
var menu_in_clipboard = null;
var ws_paste_count = 0;

//Color preset stuff.
var colorPresets = {},
	wasPresetDropdownPopulated = false;

//General admin menu visibility.
var generalComponentVisibility = {};

//Combined DOM-ready event handler.
var isDomReadyDone = false;

function ameOnDomReady() {
	isDomReadyDone = true;

	//Some editor elements are only available in the Pro version.
	if (wsEditorData.wsMenuEditorPro) {
		knownMenuFields.open_in.visible = true;
		knownMenuFields.access_level.visible = true;
		knownMenuFields.page_heading.visible = true;
		knownMenuFields.colors.visible = true;
		knownMenuFields.appearance_heading.visible = true;
		knownMenuFields.appearance_heading.onlyForTopMenus = false;
		knownMenuFields.extra_capability.visible = false; //Superseded by the "access_level" field.

		//The Pro version supports submenu icons, but they can be disabled by the user.
		knownMenuFields.icon_url.onlyForTopMenus = (wsEditorData.submenuIconsEnabled === 'never');

		$('.ws_hide_if_pro').hide();
	}

	//Let other plugins filter knownMenuFields.
	$(document).trigger('filterMenuFields.adminMenuEditor', [knownMenuFields, baseField]);

	//Make the top menu box sortable (we only need to do this once)
    var mainMenuBox = $('#ws_menu_box');
    makeBoxSortable(mainMenuBox);

	/***************************************************************************
	                  Event handlers for editor widgets
	 ***************************************************************************/
	var menuEditorNode = $('#ws_menu_editor'),
		submenuBox = $('#ws_submenu_box'),
		submenuDropZone = submenuBox.closest('.ws_main_container').find('.ws_dropzone');

	var currentVisibleSubmenu = null;

	/**
	 * Select a menu item and show its submenu.
	 *
	 * @param {JQuery|HTMLElement} container Menu container node.
	 */
	function selectItem(container) {
		if (container.hasClass('ws_active')) {
			//The menu item is already selected.
			return;
		}

		//Highlight the active item and un-highlight the previous one
		container.addClass('ws_active');
		container.siblings('.ws_active').removeClass('ws_active');
		if (container.hasClass('ws_menu')) {
			//Show/hide the appropriate submenu
			if ( currentVisibleSubmenu ){
				currentVisibleSubmenu.hide();
			}
			currentVisibleSubmenu = $('#' + container.data('submenu_id')).show();

			updateSubmenuBoxHeight(container);

			currentVisibleSubmenu.closest('.ws_main_container')
				.find('.ws_toolbar .ws_delete_menu_button')
				.toggleClass('ws_button_disabled', !canDeleteItem(getSelectedSubmenuItem()));
		}

		//Make the "delete" button appear disabled if you can't delete this item.
		container.closest('.ws_main_container')
			.find('.ws_toolbar .ws_delete_menu_button')
			.toggleClass('ws_button_disabled', !canDeleteItem(container));
	}
	AmeEditorApi.selectItem = selectItem;

	//Select the clicked menu item and show its submenu
	menuEditorNode.on('click', '.ws_container', (function () {
		selectItem($(this));
    }));

	function updateSubmenuBoxHeight(selectedMenu) {
		//Make the submenu box tall enough to reach the selected item.
		//This prevents the menu tip (if any) from floating in empty space.
		if (selectedMenu.hasClass('ws_menu_separator')) {
			submenuBox.css('min-height', '');
		} else {
			var menuTipHeight = 30,
				empiricalExtraHeight = 4,
				verticalBoxOffset = (submenuBox.offset().top - mainMenuBox.offset().top),
				minSubmenuHeight = (selectedMenu.offset().top - mainMenuBox.offset().top)
					- verticalBoxOffset
					+ menuTipHeight - submenuDropZone.outerHeight() + empiricalExtraHeight;
			minSubmenuHeight = Math.max(minSubmenuHeight, 0);
			submenuBox.css('min-height', minSubmenuHeight);
		}
	}

	AmeEditorApi.updateSubmenuBoxHeight = updateSubmenuBoxHeight;

	//Show a notification icon next to the "Permissions" field when the menu item supports extended permissions.
	function updateExtPermissionsIndicator(container, menuItem) {
		var extPermissions = AmeItemAccessEditor.detectExtPermissions(AmeEditorApi.getItemDisplayUrl(menuItem)),
			fieldTitle = container.find('.ws_edit_field-access_level .ws_field_label_text'),
			indicator = fieldTitle.find('.ws_ext_permissions_indicator');

		if (wsEditorData.wsMenuEditorPro && (extPermissions !== null)) {
			if (indicator.length < 1) {
				indicator = $('<div class="dashicons dashicons-info ws_ext_permissions_indicator"></div>');
				fieldTitle.append(" ").append(indicator);
			}
			//Idea: Change the icon based on the kind of permissions available (post type, tags, etc).
			indicator.show().data('ext_permissions', extPermissions);
		} else {
			indicator.hide();
		}
	}

	menuEditorNode.on('adminMenuEditor:fieldChange', function(event, menuItem, fieldName) {
		if ((fieldName === 'template_id') || (fieldName === 'file')) {
			updateExtPermissionsIndicator($(event.target), menuItem);
		}
	});

	//Show/hide a menu's properties
	menuEditorNode.on('click', '.ws_edit_link', (function (event) {
		event.preventDefault();

		var container = $(this).parents('.ws_container').first();
		var box = container.find('.ws_editbox');

		//For performance, the property editors for each menu are only created
		//when the user tries to access access them for the first time.
		if ( !container.data('field_editors_created') ){
			var menuItem = container.data('menu_item');
			buildEditboxFields(box, menuItem, container.hasClass('ws_menu'));
			container.data('field_editors_created', true);
			updateItemEditor(container);
			updateExtPermissionsIndicator(container, menuItem);
		}

		$(this).toggleClass('ws_edit_link_expanded');
		//show/hide the editbox
		if ($(this).hasClass('ws_edit_link_expanded')){
			box.show();
		} else {
			//Make sure changes are applied before the menu is collapsed
			box.find('input').change();
			box.hide();
		}
    }));

    //The "Default" button : Reset to default value when clicked
    menuEditorNode.on('click', '.ws_reset_button', (function () {
        //Find the field div (it holds the field name)
        var field = $(this).parents('.ws_edit_field');
	    var fieldName = field.data('field_name');

		if ( (field.length > 0) && fieldName ) {
			//Extract the default value from the menu item.
            var containerNode = field.closest('.ws_container');
			var menuItem = containerNode.data('menu_item');

			if (fieldName === 'access_level') {
	            //This is a pretty nasty hack.
	            menuItem.grant_access = {};
	            menuItem.extra_capability = null;
				menuItem.restrict_access_to_items = false;
				delete menuItem.had_access_before_hiding;
            }

			if (itemTemplates.hasDefaultValue(menuItem.template_id, fieldName)) {
				menuItem[fieldName] = null;
				updateItemEditor(containerNode);
				updateParentAccessUi(containerNode);
			}
		}
	}));

	//When a field is edited, change it's appearance if it's contents don't match the default value.
    function fieldValueChange(){
	    /* jshint validthis:true */
        var input = $(this);
		var field = input.parents('.ws_edit_field').first();
	    var fieldName = field.data('field_name');

        if ((fieldName === 'access_level') || (fieldName === 'embedded_page_id')) {
            //These fields are read-only and can never be directly edited by the user.
            //Ignore spurious change events.
            return;
        }

	    var containerNode = field.parents('.ws_container').first();
	    var menuItem = containerNode.data('menu_item');

	    var oldValue = menuItem[fieldName];
	    var oldDisplayValue = $.data(this, 'ame_last_display_value');
	    var value = getInputValue(input);
	    var defaultValue = getDefaultValue(menuItem, fieldName, null, containerNode);
        var hasADefaultValue = (defaultValue !== null);

	    //Some fields/templates have no default values.
        field.toggleClass('ws_has_no_default', !hasADefaultValue);
        if (!hasADefaultValue) {
            field.removeClass('ws_input_default');
        }

        if (field.hasClass('ws_input_default') && (value == defaultValue)) {
            value = null; //null = use default.
        }

	    //Ignore changes where the new value is the same as the old one.
	    if ((value === oldValue) || (value === oldDisplayValue)) {
		    return;
	    }

	    //Update the item.
	    if (knownMenuFields[fieldName].write !== null) {
		    knownMenuFields[fieldName].write(menuItem, value, input, containerNode);
	    } else {
		    menuItem[fieldName] = value;
	    }

	    updateItemEditor(containerNode);
	    updateParentAccessUi(containerNode);

	    containerNode.trigger('adminMenuEditor:fieldChange', [menuItem, fieldName]);
    }
	menuEditorNode.on('click change', '.ws_field_value', fieldValueChange);

	//Show/hide advanced fields
	menuEditorNode.on('click', '.ws_toggle_advanced_fields', function(){
		var self = $(this);
		var advancedFields = self.parents('.ws_container').first().find('.ws_advanced');

		if ( advancedFields.is(':visible') ){
			advancedFields.hide();
			self.text(wsEditorData.captionShowAdvanced);
		} else {
			advancedFields.show();
			self.text(wsEditorData.captionHideAdvanced);
		}

		return false;
	});

	//Allow/forbid items in actor-specific views
	menuEditorNode.on('click', 'input.ws_actor_access_checkbox', function() {
		if (actorSelectorWidget.selectedActor === null) {
			return;
		}

		var checked = $(this).is(':checked');
		var containerNode = $(this).closest('.ws_container');

		var menu = containerNode.data('menu_item');
		//Ask for confirmation if the user tries to hide Dashboard -> Home.
		if ( !checked && ((menu.template_id === 'index.php>index.php') || (menu.template_id === '>index.php')) ) {
			updateItemEditor(containerNode); //Resets the checkbox back to the old value.
			confirmDashboardHiding(function(ok) {
				if (ok) {
					setActorAccessForTreeAndUpdateUi(containerNode, actorSelectorWidget.selectedActor, checked);
				}
			});
		} else {
			setActorAccessForTreeAndUpdateUi(containerNode, actorSelectorWidget.selectedActor, checked);
		}
	});

	/**
	 * This confusingly named function sets actor access for the specified menu item
	 * and all of its children (if any). It also updates the UI with the new settings.
	 *
	 * (And it violates SRP in a particularly egregious manner.)
	 *
	 * @param containerNode
	 * @param {String|Object.<String, Boolean>} actor
	 * @param {Boolean} [allowAccess]
	 */
	function setActorAccessForTreeAndUpdateUi(containerNode, actor, allowAccess) {
		setActorAccess(containerNode, actor, allowAccess);

		//Apply the same permissions to sub-menus.
		var subMenuId = containerNode.data('submenu_id');
		if (subMenuId && containerNode.hasClass('ws_menu')) {
			$('.ws_item', '#' + subMenuId).each(function() {
				var node = $(this);
				setActorAccess(node, actor, allowAccess);
				updateItemEditor(node);
			});
		}

		updateItemEditor(containerNode);
		updateParentAccessUi(containerNode);
	}

	/**
	 * Confirm with the user that they want to hide "Dashboard -> Home".
	 *
	 * This particular menu is important because hiding it can cause an "insufficient permissions" error
	 * to be displayed right when someone logs in, making it look like login failed.
	 */
	var permissionConfirmationDialog = $('#ws-ame-dashboard-hide-confirmation').dialog({
		autoOpen: false,
		modal: true,
		closeText: ' ',
		width: 380,
		title: 'Warning'
	});
	var currentConfirmationCallback = function(ok) {};

	/**
	 * Confirm hiding "Dashboard -> Home".
	 *
	 * @param callback Called when the user selects an option. True = confirmed.
	 */
	function confirmDashboardHiding(callback) {
		//The user can disable the confirmation dialog.
		if (!wsEditorData.dashboardHidingConfirmationEnabled) {
			callback(true);
			return;
		}

		currentConfirmationCallback = callback;
		permissionConfirmationDialog.dialog('open');
	}

	$('#ws_confirm_menu_hiding, #ws_cancel_menu_hiding').click(function() {
		var confirmed = $(this).is('#ws_confirm_menu_hiding');
		var dontShowAgain = permissionConfirmationDialog.find('.ws_dont_show_again input[type="checkbox"]').is(':checked');

		currentConfirmationCallback(confirmed);
		permissionConfirmationDialog.dialog('close');

		if (dontShowAgain) {
			wsEditorData.dashboardHidingConfirmationEnabled = false;
			//Run an AJAX request to disable the dialog for this user.
			$.post(
				wsEditorData.adminAjaxUrl,
				{
					'action' : 'ws_ame_disable_dashboard_hiding_confirmation',
					'_ajax_nonce' : wsEditorData.disableDashboardConfirmationNonce
				}
			);
		}
	});


	/*************************************************************************
	                  Access editor dialog
	 *************************************************************************/

	AmeItemAccessEditor.setup({
		api: AmeEditorApi,
		actorSelector: actorSelectorWidget,
		postTypes: wsEditorData.postTypes,
		taxonomies: wsEditorData.taxonomies,
		lodash: _,
		isPro: wsEditorData.wsMenuEditorPro,

		save: function(menuItem, containerNode, settings) {
			//Save the new settings.
			menuItem.extra_capability         = settings.extraCapability;
			menuItem.grant_access             = settings.grantAccess;
			menuItem.restrict_access_to_items = settings.restrictAccessToItems;

			//Save granted capabilities.
			var newlyDisabledCaps = {};
			_.forEach(settings.grantedCapabilities, function(capabilities, actor) {
				_.forEach(capabilities, function(grant, capability) {
					if (!_.isArray(grant)) {
						grant = [grant, null, null];
					}

					AmeCapabilityManager.setCap(actor, capability, grant[0], grant[1], grant[2]);

					if (!grant[0]) {
						if (!newlyDisabledCaps.hasOwnProperty(capability)) {
							newlyDisabledCaps[capability] = [];
						}
						newlyDisabledCaps[capability].push(actor);
					}
				});
			});

			AmeEditorApi.forEachMenuItem(function(menuItem, containerNode) {
				//When the user unchecks a capability, uncheck ALL menu items associated with that capability.
				//Anything less won't actually get rid of the capability as enabled menus auto-grant req. caps.
				var requiredCap = getFieldValue(menuItem, 'access_level');
				if (newlyDisabledCaps.hasOwnProperty(requiredCap)) {
					//It's enough to remove custom "allow" settings. The rest happens automatically - items that
					//have no custom per-role settings use capability checks.
					_.forEach(newlyDisabledCaps[requiredCap], function(actor) {
						if (_.get(menuItem.grant_access, actor) === true) {
							delete menuItem.grant_access[actor];
						}
					});
				}

				//Due to changed caps and cascading submenu overrides, changes to one item's permissions
				//can affect other items. Lets just update all items.
				updateActorAccessUi(containerNode);
			});

			//Refresh the UI.
			updateItemEditor(containerNode);
		}
	});

	menuEditorNode.on('click', '.ws_launch_access_editor', function() {
		var containerNode = $(this).parents('.ws_container').first();
		var menuItem = containerNode.data('menu_item');

		AmeItemAccessEditor.open({
			menuItem: menuItem,
			containerNode: containerNode,
			selectedActor: actorSelectorWidget.selectedActor,
			itemHasSubmenus: (!!(containerNode.data('submenu_id')) &&
				$('#' + containerNode.data('submenu_id')).find('.ws_item').length > 0)
		});
	});

	/***************************************************************************
		              General dialog handlers
	 ***************************************************************************/

	$(document).on('click', '.ws_close_dialog', function() {
		$(this).parents('.ui-dialog-content').dialog('close');
	});


	/***************************************************************************
	              Drop-down list for combo-box fields
	 ***************************************************************************/

	var capSelectorDropdown = $('#ws_cap_selector');
	var currentDropdownOwner = null; //The input element that the dropdown is currently associated with.
	var currentDropdownOwnerMenu = null; //The menu item that the above input belongs to.

	var isDropdownBeingHidden = false, isSuggestionClick = false;

	//Show/hide the capability drop-down list when the trigger button is clicked
	$('#ws_trigger_capability_dropdown').on('mousedown click', onDropdownTriggerClicked);
	menuEditorNode.on('mousedown click', '.ws_cap_selector_trigger', onDropdownTriggerClicked);

	function onDropdownTriggerClicked(event){
		/* jshint validthis:true */
		var inputBox = null;
		var button = $(this);

		var isInAccessEditor = false;
		isSuggestionClick = false;

		//Find the input associated with the button that was clicked.
		if ( button.attr('id') === 'ws_trigger_capability_dropdown' ) {
			inputBox = $('#ws_extra_capability');
			isInAccessEditor = true;
		} else {
			inputBox = button.closest('.ws_edit_field').find('.ws_field_value').first();
		}

		//If the user clicks the same button again while the dropdown is already visible,
		//ignore the click. The dropdown will be hidden by its "blur" handler.
		if (event.type === 'mousedown') {
			if ( capSelectorDropdown.is(':visible') && inputBox.is(currentDropdownOwner) ) {
				isDropdownBeingHidden = true;
			}
			return;
		} else if (isDropdownBeingHidden) {
			isDropdownBeingHidden = false; //Ignore the click event.
			return;
		}

		//A jQuery UI dialog widget will prevent focus from leaving the dialog. So if we want
		//the dropdown to be properly focused when displaying it in a dialog, we must make it
		//a child of the dialog's DOM node (and vice versa when it's not in a dialog).
		var parentContainer = $(this).closest('.ui-dialog, #ws_menu_editor');
		if ((parentContainer.length > 0) && (capSelectorDropdown.closest(parentContainer).length === 0)) {
			var oldHeight = capSelectorDropdown.height(); //Height seems to reset when moving to a new parent.
			capSelectorDropdown.detach().appendTo(parentContainer).height(oldHeight);
		}

		//Pre-select the current capability (will clear selection if there's no match).
		capSelectorDropdown.val(inputBox.val()).show();

		//Move the drop-down near the input box.
		var inputPos = inputBox.offset();
		capSelectorDropdown
			.css({
				position: 'absolute',
				zIndex: 1010 //Must be higher than the permissions dialog overlay.
			})
			.offset({
				left: inputPos.left,
				top : inputPos.top + inputBox.outerHeight()
			}).
			width(inputBox.outerWidth());

		currentDropdownOwner = inputBox;

		currentDropdownOwnerMenu = null;
		if (isInAccessEditor) {
			currentDropdownOwnerMenu = AmeItemAccessEditor.getCurrentMenuItem();
		} else {
			currentDropdownOwnerMenu = currentDropdownOwner.closest('.ws_container').data('menu_item');
		}
		
		capSelectorDropdown.focus();

		capSuggestionFeature.show();
	}

	//Also show it when the user presses the down arrow in the input field (doesn't work in Opera).
	$('#ws_extra_capability').bind('keyup', function(event){
		if ( event.which === 40 ){
			$('#ws_trigger_capability_dropdown').click();
		}
	});

	function hideCapSelector() {
		capSelectorDropdown.hide();
		capSuggestionFeature.hide();
		isSuggestionClick = false;
	}

	//Event handlers for the drop-down lists themselves
	var dropdownNodes = $('.ws_dropdown');

	// Hide capability drop-down when it loses focus.
	dropdownNodes.blur(function(){
		if (!isSuggestionClick) {
			hideCapSelector();
		}
	});

	dropdownNodes.keydown(function(event){

		//Hide it when the user presses Esc
		if ( event.which === 27 ){
			hideCapSelector();
			if (currentDropdownOwner) {
				currentDropdownOwner.focus();
			}

		//Select an item & hide the list when the user presses Enter or Tab
		} else if ( (event.which === 13) || (event.which === 9) ){
			hideCapSelector();

			if (currentDropdownOwner) {
				if ( capSelectorDropdown.val() ){
					currentDropdownOwner.val(capSelectorDropdown.val()).change();
				}
				currentDropdownOwner.focus();
			}

			event.preventDefault();
		}
	});

	//Eat Tab keys to prevent focus theft. Required to make the "select item on Tab" thing work.
	dropdownNodes.keyup(function(event){
		if ( event.which === 9 ){
			event.preventDefault();
		}
	});


	//Update the input & hide the list when an option is clicked
	dropdownNodes.click(function(){
		if (capSelectorDropdown.val()){
			hideCapSelector();
			if (currentDropdownOwner) {
				currentDropdownOwner.val(capSelectorDropdown.val()).change().focus();
			}
		}
	});

	//Highlight an option when the user mouses over it (doesn't work in IE)
	dropdownNodes.mousemove(function(event){
		if ( !event.target ){
			return;
		}

		var option = event.target;
		if ( (typeof option.selected !== 'undefined') && !option.selected && option.value ){
			option.selected = true;

			//Preview which roles have this capability and the required cap.
			capSuggestionFeature.previewAccessForItem(currentDropdownOwnerMenu, option.value);
		}
	});

	/************************************************************************
	 *                     Capability suggestions
	 *************************************************************************/

	var capSuggestionFeature = (function() {
		//This feature is not used in the Pro version because it has a different permission UI.
		if (wsEditorData.wsMenuEditorPro) {
			return {
				previewAccessForItem: function () {},
				show: function () {},
				hide: function () {}
			}
		}

		var capabilitySuggestions = $('#ws_capability_suggestions'),
			suggestionBody = capabilitySuggestions.find('table tbody').first().empty(),
			suggestedCapabilities = AmeActors.getSuggestedCapabilities();

		for (var i = 0; i < suggestedCapabilities.length; i++) {
			var role = suggestedCapabilities[i].role, capability = suggestedCapabilities[i].capability;
			$('<tr>')
				.data('role', role)
				.data('capability', capability)
				.append(
					$('<th>', {text: role.displayName, scope: 'row'}).addClass('ws_ame_role_name')
				)
				.append(
					$('<td>', {text: capability}).addClass('ws_ame_suggested_capability')
				)
				.appendTo(suggestionBody);
		}

		var currentPreviewedCaps = null;

		/**
		 * Update the access preview.
		 * @param {string|string[]|null} capabilities
		 */
		function previewAccess(capabilities) {
			if (typeof capabilities === 'string') {
				capabilities = [capabilities];
			}

			if (_.isEqual(capabilities, currentPreviewedCaps)) {
				return;
			}
			currentPreviewedCaps = capabilities;
			capabilitySuggestions.find('#ws_previewed_caps').text(currentPreviewedCaps.join(' + '));

			//Short-circuit the no-caps case.
			if (capabilities === null || capabilities.length === 0) {
				suggestionBody.find('tr').removeClass('ws_preview_has_access');
				return;
			}

			suggestionBody.find('tr').each(function() {
				var $row = $(this),
					role = $row.data('role');

				var hasCaps = true;
				for (var i = 0; i < capabilities.length; i++) {
					hasCaps = hasCaps && AmeActors.hasCap(role.id, capabilities[i]);
				}
				$row.toggleClass('ws_preview_has_access', hasCaps);
			});
		}

		function previewAccessForItem(menuItem, selectedExtraCap) {
			var requiredCap = '', extraCap = '';

			if (menuItem) {
				requiredCap = getFieldValue(menuItem, 'access_level', '');
				extraCap = getFieldValue(menuItem, 'extra_capability', '');
			}
			if (typeof selectedExtraCap !== 'undefined') {
				extraCap = selectedExtraCap;
			}

			var caps = [];
			if (menuItem && (menuItem.template_id !== '') || (extraCap === '')) {
				caps.push(requiredCap);
			}
			if (extraCap !== '') {
				caps.push(extraCap);
			}

			previewAccess(caps);
		}

		suggestionBody.on('mouseenter', 'td.ws_ame_suggested_capability', function() {
			var row = $(this).closest('tr');
			previewAccessForItem(currentDropdownOwnerMenu, row.data('capability'));
		});

		capSelectorDropdown.on('keydown keyup', function() {
			previewAccessForItem(currentDropdownOwnerMenu, capSelectorDropdown.val());
		});

		suggestionBody.on('mousedown', 'td.ws_ame_suggested_capability', function() {
			//Don't immediately hide the list when the user tries to click a suggestion.
			//It would prevent the click from registering.
			isSuggestionClick = true;
		});

		suggestionBody.on('click', 'td.ws_ame_suggested_capability', function() {
			var capability = $(this).closest('tr').data('capability');

			//Change the input to the selected capability.
			if (currentDropdownOwner) {
				currentDropdownOwner.val(capability).change();
			}

			hideCapSelector();
		});

		//Workaround for pressing LMB on a suggestion, then moving the mouse outside the suggestion box and releasing the button.
		$(document).on('click', function(event) {
			if (
				isSuggestionClick
				&& capabilitySuggestions.is(':visible')
				&& ( $(event.target).closest(capabilitySuggestions).length < 1 )
			) {
				hideCapSelector();
			}
		});

		return {
			previewAccessForItem: previewAccessForItem,
			show: function() {
				//Position the capability suggestion table next to the selector and match heights.
				capabilitySuggestions
					.css({
						position: 'absolute',
						zIndex: 1009
					})
					.show()
					.position({
						my: 'left top',
						at: 'right top',
						of: capSelectorDropdown,
						collision: 'none'
					});

				var selectorHeight = capSelectorDropdown.height(),
					suggestionsHeight = capabilitySuggestions.height(),
					desiredHeight = Math.max(selectorHeight, suggestionsHeight);
				if (selectorHeight < desiredHeight) {
					capSelectorDropdown.height(desiredHeight);
				}
				if (suggestionsHeight < desiredHeight) {
					capabilitySuggestions.height(desiredHeight);
				}

				if (currentDropdownOwnerMenu) {
					previewAccessForItem(currentDropdownOwnerMenu);
				}
			},
			hide: function() {
				capabilitySuggestions.hide();
			}
		};
	})();


	/*************************************************************************
	                           Icon selector
	 *************************************************************************/
	var iconSelector = $('#ws_icon_selector');
	var currentIconButton = null; //Keep track of the last clicked icon button.

	var iconSelectorTabs = iconSelector.find('#ws_icon_source_tabs');
	iconSelectorTabs.tabs();

	//When the user clicks one of the available icons, update the menu item.
	iconSelector.on('click', '.ws_icon_option', function() {
		var selectedIcon = $(this).addClass('ws_selected_icon');
		iconSelector.hide();

		//Assign the selected icon to the menu.
		if (currentIconButton) {
			var container = currentIconButton.closest('.ws_container');
			var item = container.data('menu_item');

			//Remove the existing icon class, if any.
			var cssClass = getFieldValue(item, 'css_class', '');
			cssClass = jsTrim( cssClass.replace(/\b(ame-)?menu-icon-[^\s]+\b/, '') );

			if (selectedIcon.data('icon-class')) {
				//Add the new class.
				cssClass = selectedIcon.data('icon-class') + ' ' + cssClass;
				//Can't have both a class and an image or we'll get two overlapping icons.
				item.icon_url = '';
			} else if (selectedIcon.data('icon-url')) {
				item.icon_url = selectedIcon.data('icon-url');
			}
			item.css_class = cssClass;

			updateItemEditor(container);
		}

		currentIconButton = null;
	});

	//Show/hide the icon selector when the user clicks the icon button.
	menuEditorNode.on('click', '.ws_select_icon', function() {
		var button = $(this);
		//Clicking the same button a second time hides the icon list.
		if ( currentIconButton && button.is(currentIconButton) ) {
			iconSelector.hide();
			//noinspection JSUnusedAssignment
			currentIconButton = null;
			return;
		}

		currentIconButton = button;

		var containerNode = currentIconButton.closest('.ws_container');
		var menuItem = containerNode.data('menu_item');
		var cssClass = getFieldValue(menuItem, 'css_class', '');
		var iconUrl = getFieldValue(menuItem, 'icon_url', '', containerNode);

		var customImageOption = iconSelector.find('.ws_custom_image_icon').hide();

		//Highlight the currently selected icon.
		iconSelector.find('.ws_selected_icon').removeClass('ws_selected_icon');

		var selectedIcon = null;
		var classMatches = cssClass.match(/\b(ame-)?menu-icon-([^\s]+)\b/);
		//Dashicons and FontAwesome icons are set via the icon URL field, but they are actually CSS-based.
		var iconFontMatches = iconUrl && iconUrl.match('^\s*((?:dashicons|ame-fa)-[a-z0-9\-]+)\s*$');

		if ( iconUrl && iconUrl !== 'none' && iconUrl !== 'div' && !iconFontMatches ) {
			var currentIcon = iconSelector.find('.ws_icon_option img[src="' + iconUrl + '"]').first().closest('.ws_icon_option');
			if ( currentIcon.length > 0 ) {
				selectedIcon = currentIcon.addClass('ws_selected_icon').show();
			} else {
				//Display and highlight the custom image.
				customImageOption.find('img').prop('src', iconUrl);
				customImageOption.addClass('ws_selected_icon').show().data('icon-url', iconUrl);
				selectedIcon = customImageOption;
			}
		} else if ( classMatches || iconFontMatches ) {
			//Highlight the icon that corresponds to the current CSS class or Dashicon/FontAwesome icon.
			var iconClass = iconFontMatches ? iconFontMatches[1] : ((classMatches[1] ? classMatches[1] : '') + 'icon-' + classMatches[2]);
			selectedIcon = iconSelector.find('.' + iconClass).closest('.ws_icon_option').addClass('ws_selected_icon');
		}

		//Activate the tab that contains the icon.
		var activeTabId = selectedIcon.closest('.ws_tool_tab').prop('id'),
			activeTabItem = iconSelectorTabs.find('a[href="#' + activeTabId + '"]').closest('li');
		if (activeTabItem.length > 0) {
			iconSelectorTabs.tabs('option', 'active', activeTabItem.index());
		}

		iconSelector.show();
		iconSelector.position({ //Requires jQuery UI.
			my: 'left top',
			at: 'left bottom',
			of: button
		});
	});

	//Alternatively, use the WordPress media uploader to select a custom icon.
	//This code is based on the header selection script in /wp-admin/js/custom-header.js.
	$('#ws_choose_icon_from_media').click(function(event) {
		event.preventDefault();
		var frame = null;

		//This option is not usable on the demo site since the filesystem is usually read-only.
		if (wsEditorData.isDemoMode) {
			alert('Sorry, image upload is disabled in demo mode!');
			return;
		}

        //If the media frame already exists, reopen it.
        if ( frame ) {
            frame.open();
            return;
        }

        //Create a custom media frame.
        frame = wp.media.frames.customAdminMenuIcon = wp.media({
            //Set the title of the modal.
            title: 'Choose a Custom Icon (20x20)',

            //Tell it to show only images.
            library: {
                type: 'image'
            },

            //Customize the submit button.
            button: {
                text: 'Set as icon', //Button text.
                close: true //Clicking the button closes the frame.
            }
        });

        //When an image is selected, set it as the menu icon.
        frame.on( 'select', function() {
            //Grab the selected attachment.
            var attachment = frame.state().get('selection').first();
            //TODO: Warn the user if the image exceeds 20x20 pixels.

	        //Set the menu icon to the attachment URL.
            if (currentIconButton) {
                var container = currentIconButton.closest('.ws_container');
                var item = container.data('menu_item');

                //Remove the existing icon class, if any.
                var cssClass = getFieldValue(item, 'css_class', '');
	            item.css_class = jsTrim( cssClass.replace(/\b(ame-)?menu-icon-[^\s]+\b/, '') );

	            //Set the new icon URL.
	            item.icon_url = attachment.attributes.url;

                updateItemEditor(container);
            }

            currentIconButton = null;
        });

		//If the user closes the frame by via Esc or the "X" button, clear up state.
		frame.on('escape', function(){
			currentIconButton = null;
		});

        frame.open();
		iconSelector.hide();
	});

	//Hide the icon selector if the user clicks outside of it.
	//Exception: Clicks on "Select icon" buttons are handled above.
	$(document).on('mouseup', function(event) {
		if ( !iconSelector.is(':visible') ) {
			return;
		}

		if (
			!iconSelector.is(event.target)
			&& iconSelector.has(event.target).length === 0
			&& $(event.target).closest('.ws_select_icon').length === 0
		) {
			iconSelector.hide();
			currentIconButton = null;
		}
	});


	/*************************************************************************
	                        Embedded page selector
	 *************************************************************************/

	var pageSelector = $('#ws_embedded_page_selector'),
		pageListBox = pageSelector.find('#ws_current_site_pages'),
		currentPageSelectorButton = null, //The last page dropdown button that was clicked.
		isPageListPopulated = false,
		isPageRequestInProgress = false;

	pageSelector.tabs({
		heightStyle: 'auto',
		hide: false,
		show: false
	});
	//Hack. The selector needs to be hidden by default, but it can't start out as "display: none" because that makes
	//jQuery miscalculate tab heights. So we put it in a hidden container, then hide it on load and move it elsewhere.
	pageSelector.hide().appendTo(menuEditorNode);

	/**
	 * Update the page selector with the current menu item's settings.
	 */
	function updatePageSelector() {
		var menuItem, selectedPageId = 0, selectedBlogId = 1;
		if ( currentPageSelectorButton ) {
			menuItem = currentPageSelectorButton.closest('.ws_container').data('menu_item');
			selectedPageId = parseInt(getFieldValue(menuItem, 'embedded_page_id', 0), 10);
			selectedBlogId = parseInt(getFieldValue(menuItem, 'embedded_page_blog_id', 1), 10);
		}

		if (selectedPageId === 0) {
			pageListBox.val(null);
		} else {
			var optionValue = selectedBlogId + '_' + selectedPageId;
			pageListBox.val(optionValue);
			if ( pageListBox.val() !== optionValue ) {
				pageListBox.val('custom');
			}
		}

		pageSelector.find('#ws_embedded_page_id').val(selectedPageId);
		pageSelector.find('#ws_embedded_page_blog_id').val(selectedBlogId);
	}

	menuEditorNode.on('click', '.ws_embedded_page_selector_trigger', function(event) {
		var thisButton = $(this),
			thisInput = thisButton.closest('.ws_edit_field').find('input.ws_field_value:first');

		//Clicking the same button a second time hides the page selector.
		if (thisButton.is(currentPageSelectorButton) && pageSelector.is(':visible')) {
			pageSelector.hide();
			//noinspection JSUnusedAssignment
			currentPageSelectorButton = null;
			return;
		}

		currentPageSelectorButton = thisButton;
		pageSelector.show();
		pageSelector.position({
			my: 'left top',
			at: 'left bottom',
			of: thisInput
		});

		event.stopPropagation();

		if (!isPageListPopulated && !isPageRequestInProgress) {
			isPageRequestInProgress = true;

			var pageList = pageSelector.find('#ws_current_site_pages');
			pageList.prop('readonly', true);

			$.getJSON(
				wsEditorData.adminAjaxUrl,
				{
					'action' : 'ws_ame_get_pages',
					'_ajax_nonce' : wsEditorData.getPagesNonce
				},
				function(data){
					isPageRequestInProgress = false;
					pageList.prop('readonly', false);

					if (typeof data.error !== 'undefined'){
						alert(data.error);
						return;
					} else if ((typeof data !== 'object') || (typeof data.length === 'undefined')) {
						alert('Error: Could not retrieve a list of pages. Unexpected response from the server.');
						return;
					}

					//An alphabetised list is easier to scan visually.
					var pages = data.sort(function(a, b) {
						return a.post_title.localeCompare(b.post_title);
					});

					//Populate the select box.
					pageList.empty();
					$.each(pages, function(index, page) {
						pageList.append($('<option>', {
							val: page.blog_id + '_' + page.post_id,
							text: page.post_title
						}));
					});

					//Add a "custom" option. Select it when the current setting doesn't match any of the listed pages.
					pageList.prepend($('<option>', {
						val: 'custom',
						text: '< Custom >'
					}));

					updatePageSelector();
					isPageListPopulated = true;
				},
				'json'
			);

		}

		updatePageSelector();

		//Open the "Pages" tab by default, or the "Custom" tab if that's what's selected in the list box.
		//The updatePageSelector call above sets the pageListBox value.
		pageSelector.tabs('option', 'active', (pageListBox.val() === 'custom') ? 1 : 0);
	});

	//Hide the page selector if the user clicks outside of it and outside the current button.
	$(document).on('mouseup', function(event) {
		if ( !pageSelector.is(':visible') ) {
			return;
		}

		var target = $(event.target);
		var isOutsideSelector = target.closest(pageSelector).length === 0;
		var isOutsideButton = currentPageSelectorButton && (target.closest(currentPageSelectorButton).length === 0);

		if (isOutsideSelector && isOutsideButton) {
			pageSelector.hide();
			currentPageSelectorButton = null;
		}
	});

	function setEmbeddedPageForCurrentItem(newPageId, newBlogId, title) {
		if ( currentPageSelectorButton ) {
			var containerNode = currentPageSelectorButton.closest('.ws_container'),
				menuItem = containerNode.data('menu_item');

			menuItem.embedded_page_id = newPageId;
			menuItem.embedded_page_blog_id = newBlogId;

			if (typeof title === 'string') {
				//Store the page title for later. It will be displayed in the text box.
				AmePageTitles.add(newPageId, newBlogId, title);
			}

			updateItemEditor(containerNode);
		}
	}

	//When the user chooses a page from the list, update the menu item and hide the dropdown.
	pageListBox.on('change', function() {
		var selection = pageListBox.val();
		if (selection === 'custom') { // jshint ignore:line
			//Do nothing. Presumably, the user will now switch to the "Custom" tab and enter new settings.
			//If they don't do that and just close the dropdown, we keep the previous settings.
		} else if ( currentPageSelectorButton ) {
			//Set the new page and blog IDs. The expected value format is "blogid_postid".
			var parts = selection.split('_'),
				newBlogId = parseInt(parts[0], 10),
				newPageId = parseInt(parts[1], 10);

			pageSelector.hide();
			setEmbeddedPageForCurrentItem(newPageId, newBlogId, pageListBox.children(':selected').text());
		}
	});

	pageSelector.find('#ws_custom_embedded_page_tab form').on('submit', function(event) {
		event.preventDefault();

		var newPageId = parseInt(pageSelector.find('#ws_embedded_page_id').val(), 10),
			newBlogId = parseInt(pageSelector.find('#ws_embedded_page_blog_id').val(), 10);

		if (isNaN(newPageId) || (newPageId < 0)) {
			alert('Error: Invalid post ID');
		} else if (isNaN(newBlogId) || (newBlogId < 0)) {
			alert('Error: Invalid blog ID');
		} else if ( currentPageSelectorButton ) {
			pageSelector.hide();
			setEmbeddedPageForCurrentItem(newPageId, newBlogId);
		}
	});


	/*************************************************************************
	                             Color picker
	 *************************************************************************/

	var menuColorDialog = $('#ws-ame-menu-color-settings');
	if (menuColorDialog.length > 0) {
		menuColorDialog.dialog({
			autoOpen: false,
			closeText: ' ',
			draggable: false,
			modal: true,
			minHeight: 400,
			minWidth: 520
		});
	}

	var colorDialogState = {
		menuItem: null,
		editingGlobalColors: false
	};

	var menuColorVariables = [
		'base-color',
		'text-color',
		'highlight-color',
		'icon-color',

		'menu-highlight-text',
		'menu-highlight-icon',
		'menu-highlight-background',

		'menu-current-text',
		'menu-current-icon',
		'menu-current-background',

		'menu-submenu-text',
		'menu-submenu-background',
		'menu-submenu-focus-text',
		'menu-submenu-current-text',

		'menu-bubble-text',
		'menu-bubble-background',
		'menu-bubble-current-text',
		'menu-bubble-current-background'
	];

	var colorPresetDropdown = $('#ame-menu-color-presets'),
		colorPresetDeleteButton = $("#ws-ame-delete-color-preset"),
		areColorChangesIgnored = false;

	//Show only the primary color settings by default.
	var showAdvancedColors = false;
	$('#ws-ame-show-advanced-colors').click(function() {
		showAdvancedColors = !showAdvancedColors;
		$('#ws-ame-menu-color-settings').find('.ame-advanced-menu-color').toggle(showAdvancedColors);
		$(this).text(showAdvancedColors ? 'Hide advanced options' : 'Show advanced options');
	});

	var colorPickersInitialized = false;
	function setUpColorDialog(dialogTitle) {
		//Initializing the color pickers takes a while, so we only do it when needed instead of on document ready.
		if ( !colorPickersInitialized ) {
			menuColorDialog.find('.ame-color-picker').wpColorPicker({
				//Deselect the current preset when the user changes any of the color options.
				change: deselectPresetOnColorChange,
				clear: deselectPresetOnColorChange
			});
			colorPickersInitialized = true;
		}

		//Populate presets and deselect the previously selected option.
		colorPresetDropdown.val('');
		if (!wasPresetDropdownPopulated) {
			populatePresetDropdown();
			wasPresetDropdownPopulated = true;
		}

		//Update the dialog title.
		menuColorDialog.dialog('option', 'title', dialogTitle);
	}

	//"Edit.." color schemes.
	menuEditorNode.on('click', '.ws_open_color_editor, .ws_color_scheme_display', function() {
		var containerNode = $(this).parents('.ws_container').first();
		var menuItem = containerNode.data('menu_item');

		colorDialogState.containerNode = containerNode;
		colorDialogState.menuItem = menuItem;
		colorDialogState.editingGlobalColors = false;

		//Add menu title to the dialog caption.
		var title = getFieldValue(menuItem, 'menu_title', null);
		setUpColorDialog(title ? ('Colors: ' + formatMenuTitle(title)) : 'Colors');

		//Show the [global] preset only if the user has set it up.
		var globalPresetExists = colorPresets.hasOwnProperty('[global]');
		menuColorDialog.find('#ame-global-colors-preset').toggle(globalPresetExists);

		var colors = getFieldValue(menuItem, 'colors', {}),
			colorsToDisplay = colors || {};
		if (_.isEmpty(colors)) {
			//Normalization. No custom colors = use default colors, and null is used to indicate default settings.
			menuItem.colors = null;
			//If no custom colors, select and display the global preset.
			if (globalPresetExists) {
				colorsToDisplay = colorPresets['[global]'];
				colorPresetDropdown.val('[global]');
				colorPresetDeleteButton.hide();
			}
		}
		displayColorSettingsInDialog(colorsToDisplay);

		menuColorDialog.dialog('open');
	});

	//The "Colors" button in the main sidebar.
	$('#ws_edit_global_colors').click(function() {
		colorDialogState.editingGlobalColors = true;
		colorDialogState.menuItem = null;
		colorDialogState.containerNode = null;

		setUpColorDialog('Default menu colors');
		displayColorSettingsInDialog(_.get(colorPresets, '[global]', {}));

		//Hide the [global] preset. We'll be editing it.
		menuColorDialog.find('#ame-global-colors-preset').hide();

		menuColorDialog.dialog('open');
	});

	function getColorSettingsFromDialog() {
		var colors = {}, colorCount = 0;

		for (var i = 0; i < menuColorVariables.length; i++) {
			var name = menuColorVariables[i];
			var value = $('#ame-color-' + name).val();
			if (value) {
				colors[name] = value;
				colorCount++;
			}
		}

		if (colorCount > 0) {
			return colors;
		} else {
			return null;
		}
	}

	function displayColorSettingsInDialog(colors) {
		//noinspection JSUnusedAssignment
		areColorChangesIgnored = true;
		var customColorCount = 0;

		for (var i = 0; i < menuColorVariables.length; i++) {
			var name = menuColorVariables[i];
			var value = colors.hasOwnProperty(name) ? colors[name] : false;

			if ( value ) {
				$('#ame-color-' + name).wpColorPicker('color', value);
				customColorCount++;
			} else {
				$('#ame-color-' + name).closest('.wp-picker-container').find('.wp-picker-clear').click();
			}
		}

		areColorChangesIgnored = false;
		return customColorCount;
	}

	//The "Save Changes" button in the color dialog.
	$('#ws-ame-save-menu-colors').click(function() {
		menuColorDialog.dialog('close');
		var colors = getColorSettingsFromDialog();

		if ( colorDialogState.editingGlobalColors ) {
			if (colors === null) {
				delete colorPresets['[global]'];
			} else {
				colorPresets['[global]'] = colors;
			}
		} else if ( colorDialogState.menuItem ) {
			var menuItem = colorDialogState.menuItem;
			//If colors match the global settings, reset them to null. Using the [global] preset is the default.
			if (_.has(colorPresets, '[global]') && _.isEqual(colors, colorPresets['[global]'])) {
				menuItem.colors = null;
			} else {
				menuItem.colors = colors;
			}
			updateItemEditor(colorDialogState.containerNode);
		}

		colorDialogState.containerNode = null;
		colorDialogState.menuItem = null;
		colorDialogState.editingGlobalColors = false;
	});

	//The "Apply to All" button in the same dialog.
	$('#ws-ame-apply-colors-to-all').click(function() {
		if (!confirm('Apply these color settings to ALL top level menus?')) {
			return;
		}

		//Set this as the global preset and remove custom settings from all items.
		var newColors = getColorSettingsFromDialog();
		if (newColors === null) {
			delete colorPresets['[global]'];
		} else {
			colorPresets['[global]'] = newColors;
		}
		$('#ws_menu_box').find('.ws_menu').each(function() {
			var containerNode = $(this),
				menuItem = containerNode.data('menu_item');
			if (!menuItem.separator) {
				menuItem.colors = null;
				updateItemEditor(containerNode);
			}
		});

		menuColorDialog.dialog('close');
		colorDialogState.containerNode = null;
		colorDialogState.menuItem = null;
	});

	function addColorPreset(name, colors) {
		colorPresets[name] = colors;
		populatePresetDropdown();
		colorPresetDropdown.val(name);
		colorPresetDeleteButton.removeClass('hidden');
	}

	function deleteColorPreset(name) {
		delete colorPresets[name];
		populatePresetDropdown();
		colorPresetDropdown.val('');
		colorPresetDeleteButton.addClass('hidden');
	}

	function populatePresetDropdown() {
		var separator = colorPresetDropdown.find('#ame-color-preset-separator');

		//Delete the old options, but keep the "save preset" option and so on.
		colorPresetDropdown.find('option').not('.ame-meta-option').remove();

		//Sort presets alphabetically.
		var presetNames = $.map(colorPresets, function(unused, name) {
			return name;
		}).sort(function(a, b) {
			return a.localeCompare(b);
		});

		//Add them all to the dropdown.
		var newOptions = jQuery([]);
		$.each(presetNames, function(unused, name) {
			if (name === '[global]') {
				return;
			}

			newOptions = newOptions.add($('<option>', {
				val: name,
				text: name
			}));
		});
		newOptions.insertBefore(separator);
	}

	function deselectPresetOnColorChange() {
		//Most jQuery widgets don't trigger change events when you update them via JavaScript,
		//but apparently wpColorPicker does. We want to ignore those superfluous events.
		if (!areColorChangesIgnored && (colorPresetDropdown.val() !== '')) {
			colorPresetDropdown.val('');
		}
	}

	colorPresetDropdown.change(function() {
		var dropdown = $(this),
			presetName = dropdown.val();

		colorPresetDeleteButton.toggleClass('hidden', _.includes(['[save_preset]', '[global]', '', null], presetName));

		if ((presetName === '[save_preset]') && menuColorDialog.dialog('isOpen')) {
			//Create a new preset.
			var colors = getColorSettingsFromDialog();
			if (colors === null) {
				dropdown.val('');
				alert('Error: No colors selected');
				return;
			}

			var newPresetName = window.prompt('New preset name:', '');
			if ((newPresetName === null) || (jsTrim(newPresetName) === '')) {
				dropdown.val('');
				return;
			}

			addColorPreset(newPresetName, colors);
		} else if (presetName !== '') {
			//Apply the selected preset.
			var preset = colorPresets[presetName];
			displayColorSettingsInDialog(preset);
		}
	});

	colorPresetDeleteButton.click(function() {
		var presetName = $('#ame-menu-color-presets').val();
		if ( _.includes(['[save_preset]', '[global]', '', null], presetName) ) {
			return false;
		}
		if (!confirm('Are you sure you want to delete the preset "' + presetName + '"?')) {
			return false;
		}

		deleteColorPreset(presetName);
		return false;
	});

    /*************************************************************************
	                           Menu toolbar buttons
	 *************************************************************************/
    function getSelectedMenu() {
	    return $('#ws_menu_box').find('.ws_active');
    }

	//Show/Hide menu
	$('#ws_hide_menu').click(function (event) {
		event.preventDefault();

		//Get the selected menu
		var selection = getSelectedMenu();
		if (!selection.length) {
			return;
		}

		toggleItemHiddenFlag(selection);
	});

	//Hide a menu and deny access.
	menuEditorNode.find('.ws_toolbar').on('click', '.ws_hide_and_deny_button', function() {
		var $box = $(this).closest('.ws_main_container').find('.ws_box'),
			selection = $box.is('#ws_menu_box') ? getSelectedMenu() : getSelectedSubmenuItem();
		if (selection.length < 1) {
			return;
		}

		function objectFillKeys(keys, value) {
			var result = {};
			_.forEach(keys, function(key) {
				result[key] = value;
			});
			return result;
		}

		if (actorSelectorWidget.selectedActor === null) {
			//Hide from everyone except Super Admin and the current user.
			var menuItem = selection.data('menu_item'),
				validActors = _.keys(wsEditorData.actors),
				alwaysAllowedActors = _.intersection(
					['special:super_admin', 'user:' + wsEditorData.currentUserLogin],
					validActors
				),
				victims = _.difference(validActors, alwaysAllowedActors),
				shouldHide;

			//First, lets check who has access. Maybe this item is already hidden from the victims.
			shouldHide = _.some(victims, _.curry(actorCanAccessMenu, 2)(menuItem));

			var keepEnabled = objectFillKeys(alwaysAllowedActors, true),
				hideAllExceptAllowed = _.assign(objectFillKeys(victims, false), keepEnabled);

			walkMenuTree(selection, function(container, item) {
				var newAccess;
				if (shouldHide) {
					//Yay, hide it now!
					newAccess = hideAllExceptAllowed;
					//Only update had_access_before_hiding if this item isn't hidden yet or the field is missing.
					//We don't want to double-hide an item.
					var actorsWithAccess = _.filter(victims, function(actor) {
						return actorCanAccessMenu(item, actor);
					});
					if ((actorsWithAccess.length) > 0 || _.isEmpty(_.get(item, 'had_access_before_hiding', null))) {
						item.had_access_before_hiding = actorsWithAccess;
					}
				} else {
					//Give back access to the roles and users who previously had access.
					//Careful, don't give access to roles that no longer exist.
					var actorsWhoHadAccess = _.get(item, 'had_access_before_hiding', []) || [];
					actorsWhoHadAccess = _.intersection(actorsWhoHadAccess, validActors);

					newAccess = _.assign(objectFillKeys(actorsWhoHadAccess, true), keepEnabled);
					delete item.had_access_before_hiding;
				}

				setActorAccess(container, newAccess);
				updateItemEditor(container);
			});

		} else {
			//Just toggle the checkbox.
			selection.find('input.ws_actor_access_checkbox').click();
		}
	});

	//Delete error dialog. It shows up when the user tries to delete one of the default menus.
	var menuDeletionDialog = $('#ws-ame-menu-deletion-error').dialog({
		autoOpen: false,
		modal: true,
		closeText: ' ',
		title: 'Error',
		draggable: false
	});
	var menuDeletionCallback = function(hide) {
		menuDeletionDialog.dialog('close');
		var selection = menuDeletionDialog.data('selected_menu');

		function applyCallbackRecursively(containerNode, callback) {
			callback(containerNode.data('menu_item'));

			var subMenuId = containerNode.data('submenu_id');
			if (subMenuId && containerNode.hasClass('ws_menu')) {
				$('.ws_item', '#' + subMenuId).each(function() {
					var node = $(this);
					callback(node.data('menu_item'));
					updateItemEditor(node);
				});
			}

			updateItemEditor(containerNode);
		}

		function hideRecursively(containerNode, exceptActor) {
			var otherActors = _(actorSelectorWidget.getVisibleActors())
				.pluck('id')
				.without(exceptActor)
				.value();

			applyCallbackRecursively(containerNode, function(menuItem) {
				//Remember which actors had access to this item so that it
				//can be un-hidden by the toolbar button.
				var actorsWithAccess = _.filter(otherActors, function(actor) {
					return actorCanAccessMenu(menuItem, actor);
				});
				if ((actorsWithAccess.length) > 0) {
					menuItem.had_access_before_hiding = actorsWithAccess;
				}

				denyAccessForAllExcept(menuItem, exceptActor);
			});
			updateParentAccessUi(containerNode);
		}

		//TODO: Write had_access_before_hiding so that it can be un-hidden using the toolbar button.
		if (hide === 'all') {
			if (wsEditorData.wsMenuEditorPro) {
				hideRecursively(selection, null);
			} else {
				//The free version doesn't have role permissions, so use the global "hidden" flag.
				applyCallbackRecursively(selection, function(menuItem) {
					menuItem.hidden = true;
				});
			}
		} else if (hide === 'except_current_user') {
			hideRecursively(selection, 'user:' + wsEditorData.currentUserLogin);
		} else if (hide === 'except_administrator' && !wsEditorData.wsMenuEditorPro) {
			//Set "required capability" to something only the Administrator role would have.
			var adminOnlyCap = 'manage_options';
			applyCallbackRecursively(selection, function(menuItem) {
				menuItem.extra_capability = adminOnlyCap;
			});
			alert('The "required capability" field was set to "' + adminOnlyCap + '".');
		}
	};

	//Callbacks for each of the dialog buttons.
	$('#ws_cancel_menu_deletion').click(function() {
		menuDeletionCallback(false);
	});
	$('#ws_hide_menu_from_everyone').click(function() {
		menuDeletionCallback('all');
	});
	$('#ws_hide_menu_except_current_user').click(function() {
		menuDeletionCallback('except_current_user');
	});
	$('#ws_hide_menu_except_administrator').click(function() {
		menuDeletionCallback('except_administrator');
	});

	/**
	 * Check if it's possible to delete a menu item.
	 *
	 * @param {JQuery} containerNode
	 * @returns {boolean}
	 */
	function canDeleteItem(containerNode) {
		if (!containerNode || (containerNode.length < 1)) {
			return false;
		}

		var menuItem = containerNode.data('menu_item');
		var isDefaultItem =
			( menuItem.template_id !== '')
			&& ( menuItem.template_id !== wsEditorData.unclickableTemplateId)
			&& ( menuItem.template_id !== wsEditorData.embeddedPageTemplateId)
			&& (!menuItem.separator);

		var otherCopiesExist = false;
		if (isDefaultItem) {
			//Check if there are any other menus with the same template ID.
			$('#ws_menu_editor').find('.ws_container').each(function() {
				var otherItem = $(this).data('menu_item');
				if ((menuItem !== otherItem) && (menuItem.template_id === otherItem.template_id)) {
					otherCopiesExist = true;
					return false;
				}
				return true;
			});
		}

		return (!isDefaultItem || otherCopiesExist);
	}

	/**
	 * Attempt to delete a menu item. Will check if the item can actually be deleted and ask the user for confirmation.
	 * UI callback.
	 *
	 * @param {JQuery} selection The selected menu item (DOM node).
	 */
	function tryDeleteItem(selection) {
		var menuItem = selection.data('menu_item');
		var shouldDelete = false;

		if (canDeleteItem(selection)) {
			//Custom and duplicate items can be deleted normally.
			shouldDelete = confirm('Delete this menu?');
		} else {
			//Non-custom items can not be deleted, but they can be hidden. Ask the user if they want to do that.
			menuDeletionDialog.find('#ws-ame-menu-type-desc').text(
				getDefaultValue(menuItem, 'is_plugin_page') ? 'an item added by another plugin' : 'a built-in menu item'
			);
			menuDeletionDialog.data('selected_menu', selection);

			//Different versions get slightly different options because only the Pro version has
			//role-specific permissions.
			$('#ws_hide_menu_except_current_user').toggleClass('hidden', !wsEditorData.wsMenuEditorPro);
			$('#ws_hide_menu_except_administrator').toggleClass('hidden', wsEditorData.wsMenuEditorPro);

			menuDeletionDialog.dialog('open');

			//Select "Cancel" as the default button.
			menuDeletionDialog.find('#ws_cancel_menu_deletion').focus();
		}

		if (shouldDelete) {
			//Delete this menu's submenu first, if any.
			var submenuId = selection.data('submenu_id');
			if (submenuId) {
				$('#' + submenuId).remove();
			}
			var parentSubmenu = selection.closest('.ws_submenu');

			//Delete the menu.
			selection.remove();

			if (parentSubmenu) {
				//Refresh permissions UI for this menu's parent (if any).
				updateParentAccessUi(parentSubmenu);
			}
		}
	}

	//Delete menu
	$('#ws_delete_menu').click(function (event) {
		event.preventDefault();

		//Get the selected menu
		var selection = getSelectedMenu();
		if (!selection.length) {
			return;
		}

		tryDeleteItem(selection);
	});

	//Copy menu
	$('#ws_copy_menu').click(function (event) {
		event.preventDefault();

		//Get the selected menu
		var selection = $('#ws_menu_box').find('.ws_active');
		if (!selection.length) {
			return;
		}

		//Store a copy of the current menu state in clipboard
		menu_in_clipboard = readItemState(selection);
	});

	//Cut menu
	$('#ws_cut_menu').click(function (event) {
		event.preventDefault();

		//Get the selected menu
		var selection = $('#ws_menu_box').find('.ws_active');
		if (!selection.length) {
			return;
		}

		//Store a copy of the current menu state in clipboard
		menu_in_clipboard = readItemState(selection);

		//Remove the original menu and submenu
		$('#'+selection.data('submenu_id')).remove();
		selection.remove();
	});

	//Paste menu
	function pasteMenu(menu, afterMenu) {
		//The user shouldn't need to worry about giving separators a unique filename.
		if (menu.separator) {
			menu.defaults.file = randomMenuId('separator_');
		}

		//If we're pasting from a sub-menu, we may need to fix some properties
		//that are blank for sub-menu items but required for top-level menus.
		if (getFieldValue(menu, 'css_class', '') == '') {
			menu.css_class = 'menu-top';
		}
		if (getFieldValue(menu, 'icon_url', '') == '') {
			menu.icon_url = 'dashicons-admin-generic';
		}
		if (getFieldValue(menu, 'hookname', '') == '') {
			menu.hookname = randomMenuId();
		}

		//Paste the menu after the specified one, or at the end of the list.
		if (afterMenu) {
			return outputTopMenu(menu, afterMenu);
		} else {
			return outputTopMenu(menu);
		}
	}

	$('#ws_paste_menu').click(function (event) {
		event.preventDefault();

		//Check if anything has been copied/cut
		if (!menu_in_clipboard) {
			return;
		}

		var menu = $.extend(true, {}, menu_in_clipboard);

		//Get the selected menu
		var selection = $('#ws_menu_box').find('.ws_active');
		//Paste the menu after the selection.
		pasteMenu(menu, (selection.length > 0) ? selection : null);
	});

	//New menu
	$('#ws_new_menu').click(function (event) {
		event.preventDefault();

		ws_paste_count++;

		//The new menu starts out rather bare
		var randomId = randomMenuId();
		var menu = $.extend(true, {}, wsEditorData.blankMenuItem, {
			custom: true, //Important : flag the new menu as custom, or it won't show up after saving.
			template_id : '',
			menu_title : 'Custom Menu ' + ws_paste_count,
			file : randomId,
			items: []
		});
		menu.defaults = $.extend(true, {}, itemTemplates.getDefaults(''));

		//Make it accessible only to the current actor if one is selected.
		if (actorSelectorWidget.selectedActor !== null) {
			denyAccessForAllExcept(menu, actorSelectorWidget.selectedActor);
		}

		//Insert the new menu
		var selection = $('#ws_menu_box').find('.ws_active');
		var result = outputTopMenu(menu, (selection.length > 0) ? selection : null);

		//The menus's editbox is always open
		result.menu.find('.ws_edit_link').click();
	});

	//New separator
	$('#ws_new_separator, #ws_new_submenu_separator').click(function (event) {
		event.preventDefault();

		ws_paste_count++;

		//The new menu starts out rather bare
		var randomId = randomMenuId('separator_');
		var menu = $.extend(true, {}, wsEditorData.blankMenuItem, {
			separator: true, //Flag as a separator
			custom: false,   //Separators don't need to flagged as custom to be retained.
			items: [],
			defaults: {
				separator: true,
				css_class : 'wp-menu-separator',
				access_level : 'read',
				file : randomId,
				hookname : randomId
			}
		});

		if ( $(this).attr('id').indexOf('submenu') === -1 ) {
			//Insert in the top-level menu.
			var selection = $('#ws_menu_box').find('.ws_active');
			outputTopMenu(menu, (selection.length > 0) ? selection : null);
		} else {
			//Insert in the currently visible submenu.
			pasteItem(menu);
		}
	});

	//Toggle all menus for the currently selected actor
	$('#ws_toggle_all_menus').click(function(event) {
		event.preventDefault();

		if ( actorSelectorWidget.selectedActor === null ) {
			alert("This button enables/disables all menus for the selected role. To use it, click a role and then click this button again.");
			return;
		}

		var topMenuNodes = $('.ws_menu', '#ws_menu_box');
		//Look at the first menu's permissions and set everything to the opposite.
		var allow = ! actorCanAccessMenu(topMenuNodes.eq(0).data('menu_item'), actorSelectorWidget.selectedActor);

		topMenuNodes.each(function() {
			var containerNode = $(this);
			setActorAccessForTreeAndUpdateUi(containerNode, actorSelectorWidget.selectedActor, allow);
		});
	});

	//Copy all menu permissions from one role to another.
	var copyPermissionsDialog = $('#ws-ame-copy-permissions-dialog').dialog({
		autoOpen: false,
		modal: true,
		closeText: ' ',
		draggable: false
	});

	var sourceActorList = $('#ame-copy-source-actor'), destinationActorList = $('#ame-copy-destination-actor');

	//The "Copy permissions" toolbar button.
	$('#ws_copy_role_permissions').click(function(event) {
		event.preventDefault();

		var previousSource = sourceActorList.val();

		//Populate source/destination lists.
		sourceActorList.find('option').not('[disabled]').remove();
		destinationActorList.find('option').not('[disabled]').remove();
		$.each(actorSelectorWidget.getVisibleActors(), function(index, actor) {
			var option = $('<option>', {
				val: actor.id,
				text: actorSelectorWidget.getNiceName(actor)
			});
			sourceActorList.append(option);
			destinationActorList.append(option.clone());
		});

		//Pre-select the current actor as the destination.
		if (actorSelectorWidget.selectedActor !== null) {
			destinationActorList.val(actorSelectorWidget.selectedActor);
		}

		//Restore the previous source selection.
		if (previousSource) {
			sourceActorList.val(previousSource);
		}
		if (!sourceActorList.val()) {
			sourceActorList.find('option').first().prop('selected', true); //Fallback.
		}

		copyPermissionsDialog.dialog('open');
	});

	//Actually copy the permissions when the user click the confirmation button.
	var copyConfirmationButton = $('#ws-ame-confirm-copy-permissions');
	copyConfirmationButton.click(function() {
		var sourceActor = sourceActorList.val();
		var destinationActor = destinationActorList.val();

		if (sourceActor === null || destinationActor === null) {
			alert('Select a source and a destination first.');
			return;
		}

		//Iterate over all menu items and copy the permissions from one actor to the other.
		var allMenuNodes = $('.ws_menu', '#ws_menu_box').add('.ws_item', submenuBox);
		allMenuNodes.each(function() {
			var node = $(this);
			var menuItem = node.data('menu_item');

			//Only change permissions when they don't match. This ensures we won't unnecessarily overwrite default
			//permissions and bloat the configuration with extra grant_access entries.
			var sourceAccess      = actorCanAccessMenu(menuItem, sourceActor);
			var destinationAccess = actorCanAccessMenu(menuItem, destinationActor);
			if (sourceAccess !== destinationAccess) {
				setActorAccess(node, destinationActor, sourceAccess);
				//Note: In theory, we could also look at the default permissions for destinationActor and
				//revert to default instead of overwriting if that would make the two actors' permissions match.
			}
		});

		//todo: copy granted permissions like CPTs.

		//If the user is currently looking at the destination actor, force the UI to refresh
		//so that they can see the new permissions.
		if (actorSelectorWidget.selectedActor === destinationActor) {
			//This is a bit of a hack, but right now there's no better way to refresh all items at once.
			actorSelectorWidget.setSelectedActor(null);
			actorSelectorWidget.setSelectedActor(destinationActor);
		}

		//All done.
		copyPermissionsDialog.dialog('close');
	});

	//Only enable the copy button when the user selects a valid source and destination.
	copyConfirmationButton.prop('disabled', true);
	sourceActorList.add(destinationActorList).click(function() {
		var sourceActor = sourceActorList.val();
		var destinationActor = destinationActorList.val();

		var validInputs = (sourceActor !== null) && (destinationActor !== null) && (sourceActor !== destinationActor);
		copyConfirmationButton.prop('disabled', !validInputs);
	});

	//Sort menus in ascending or descending order.
	menuEditorNode.find('.ws_toolbar').on('click', '.ws_sort_menus_button', function(event) {
		event.preventDefault();

		var button = $(this),
			direction = button.data('sort-direction') || 'asc',
			menuBox = $(this).closest('.ws_main_container').find('.ws_box').first();

		if (menuBox.is('#ws_submenu_box')) {
			menuBox = menuBox.find('.ws_submenu:visible').first();
		}

		if (menuBox.length > 0) {
			sortMenuItems(menuBox, direction);
		}

		//When sorting the top level menu also sort submenus, but leave the first item unmoved.
		//Moving the first item would change the parent menu URL (WP always links it to the first item),
		//which can be unexpected and confusing. The user can always move the first item manually.
		if (menuBox.is('#ws_menu_box')) {
			$('#ws_submenu_box').find('.ws_submenu').each(function() {
				sortMenuItems($(this), direction, true);
			});
		}
	});

	/**
	 * Sort menu items by title.
	 *
	 * @param $menuBox A DOM node that contains multiple menu items.
	 * @param {string} direction 'asc' or 'desc'
	 * @param {boolean} [leaveFirstItem] Leave the first item in its original position. Defaults to false.
	 */
	function sortMenuItems($menuBox, direction, leaveFirstItem) {
		var multiplier = (direction === 'desc') ? -1 : 1,
			items = $menuBox.find('.ws_container'),
			firstItem = items.first();

		//Separators don't have a title, but we don't want them to end up at the top of the list.
		//Instead, lets keep their position the same relative to the previous item.
		var prevItemTitle = '';
		items.each((function(){
			var item = $(this), sortValue;
			if (item.is('.ws_menu_separator')) {
				sortValue = prevItemTitle;
			} else {
				sortValue = jsTrim(item.find('.ws_item_title').text());
				prevItemTitle = sortValue;
			}
			item.data('ame-sort-value', sortValue);
		}));

		function compareMenus(a, b){
			var aTitle = $(a).data('ame-sort-value'),
				bTitle = $(b).data('ame-sort-value');

			aTitle = aTitle.toLowerCase();
			bTitle = bTitle.toLowerCase();

			if (aTitle > bTitle) {
				return multiplier;
			} else if (aTitle < bTitle) {
				return -multiplier;
			}
			return 0;
		}

		items.sort(compareMenus);

		if (leaveFirstItem) {
			//Move the first item back to the top.
			firstItem.prependTo($menuBox);
		}
	}

	//Toggle the second row of toolbar buttons.
	$('#ws_toggle_toolbar').click(function() {
		var visible = menuEditorNode.find('.ws_second_toolbar_row').toggle().is(':visible');
		$.cookie('ame-show-second-toolbar', visible ? '1' : '0', {expires: 90});
	});


	/*************************************************************************
	                          Item toolbar buttons
	 *************************************************************************/
	function getSelectedSubmenuItem() {
		return $('#ws_submenu_box').find('.ws_submenu:visible .ws_active');
	}

	//Show/Hide item
	$('#ws_hide_item').click(function (event) {
		event.preventDefault();

		//Get the selected item
		var selection = getSelectedSubmenuItem();
		if (!selection.length) {
			return;
		}

		//Mark the item as hidden/visible
		toggleItemHiddenFlag(selection);
	});

	//Delete item
	$('#ws_delete_item').click(function (event) {
		event.preventDefault();

		var selection = getSelectedSubmenuItem();
		if (!selection.length) {
			return;
		}

		tryDeleteItem(selection);
	});

	//Copy item
	$('#ws_copy_item').click(function (event) {
		event.preventDefault();

		//Get the selected item
		var selection = getSelectedSubmenuItem();
		if (!selection.length) {
			return;
		}

		//Store a copy of item state in the clipboard
		menu_in_clipboard = readItemState(selection);
	});

	//Cut item
	$('#ws_cut_item').click(function (event) {
		event.preventDefault();

		//Get the selected item
		var selection = getSelectedSubmenuItem();
		if (!selection.length) {
			return;
		}

		//Store a copy of item state in the clipboard
		menu_in_clipboard = readItemState(selection);

		var submenu = selection.parent();
		//Remove the original item
		selection.remove();
		updateParentAccessUi(submenu);
	});

	//Paste item
	function pasteItem(item, targetSubmenu) {
		//We're pasting this item into a sub-menu, so it can't have a sub-menu of its own.
		//Instead, any sub-menu items belonging to this item will be pasted after the item.
		var newItems = [];
		for (var file in item.items) {
			if (item.items.hasOwnProperty(file)) {
				newItems.push(buildMenuItem(item.items[file], false));
			}
		}
		item.items = [];

		newItems.unshift(buildMenuItem(item, false));

		//Paste into the currently visible submenu by default.
		targetSubmenu = targetSubmenu || $('#ws_submenu_box').find('.ws_submenu:visible');
		//Get the selected menu
		var selection = targetSubmenu.find('.ws_active');
		for(var i = 0; i < newItems.length; i++) {
			if (selection.length > 0) {
				//If an item is selected add the pasted items after it
				selection.after(newItems[i]);
			} else {
				//Otherwise add the pasted items at the end
				targetSubmenu.append(newItems[i]);
			}

			updateItemEditor(newItems[i]);
			newItems[i].show();
		}

		updateParentAccessUi(targetSubmenu);
	}

	$('#ws_paste_item').click(function (event) {
		event.preventDefault();

		//Check if anything has been copied/cut
		if (!menu_in_clipboard) {
			return;
		}

		//You can only add separators to submenus in the Pro version.
		if ( menu_in_clipboard.separator && !wsEditorData.wsMenuEditorPro ) {
			return;
		}

		//Paste it.
		var item = $.extend(true, {}, menu_in_clipboard);
		pasteItem(item);
	});

	//New item
	$('#ws_new_item').click(function (event) {
		event.preventDefault();

		if ($('.ws_submenu:visible').length < 1) {
			return; //Abort if no submenu visible
		}

		ws_paste_count++;

		var entry = $.extend(true, {}, wsEditorData.blankMenuItem, {
			custom: true,
			template_id : '',
			menu_title : 'Custom Item ' + ws_paste_count,
			file : randomMenuId(),
			items: []
		});
		entry.defaults = $.extend(true, {}, itemTemplates.getDefaults(''));

		//Make it accessible to only the currently selected actor.
		if (actorSelectorWidget.selectedActor !== null) {
			denyAccessForAllExcept(entry, actorSelectorWidget.selectedActor);
		}

		var menu = buildMenuItem(entry);

		//Insert the item into the currently open submenu.
		var visibleSubmenu = $('#ws_submenu_box').find('.ws_submenu:visible');
		var selection = visibleSubmenu.find('.ws_active');
		if (selection.length > 0) {
			selection.after(menu);
		} else {
			visibleSubmenu.append(menu);
		}
		updateItemEditor(menu);

		//The items's editbox is always open
		menu.find('.ws_edit_link').click();

		updateParentAccessUi(menu);
	});

	//==============================================
	//				Main buttons
	//==============================================

	//Save Changes - encode the current menu as JSON and save
	$('#ws_save_menu').click(function () {
		try {
			var tree = readMenuTreeState();
		} catch (error) {
			//Right now the only known error condition is duplicate top level URLs.
			if (error.hasOwnProperty('code') && (error.code === 'duplicate_top_level_url')) {
				var message = 'Error: Duplicate menu URLs. The following top level menus have the same URL:\n\n' ;
				for (var i = 0; i < error.duplicates.length; i++) {
					var containerNode = $(error.duplicates[i]);
					message += (i + 1) + '. ' + containerNode.find('.ws_item_title').first().text() + '\n';
				}
				message += '\nPlease change the URLs to be unique or delete the duplicates.';
				alert(message);
			} else {
				alert(error.message);
			}
			return;
		}

		function findItemByTemplateId(items, templateId) {
			var foundItem = null;

			$.each(items, function(index, item) {
				if (item.template_id === templateId) {
					foundItem = item;
					return false;
				}
				if (item.hasOwnProperty('items') && (item.items.length > 0)) {
					foundItem = findItemByTemplateId(item.items, templateId);
					if (foundItem !== null) {
						return false;
					}
				}
				return true;
			});

			return foundItem;
		}

		//Abort the save if it would make the editor inaccessible.
        if (wsEditorData.wsMenuEditorPro) {
            var myMenuItem = findItemByTemplateId(tree.tree, 'options-general.php>menu_editor');
            if (myMenuItem === null) { // jshint ignore:line
                //This is OK - the missing menu item will be re-inserted automatically.
            } else if (!actorCanAccessMenu(myMenuItem, 'user:' + wsEditorData.currentUserLogin)) {
                alert(
	                "Error: This configuration would make you unable to access the menu editor!\n\n" +
	                "Please click either your role name or \"Current user (" + wsEditorData.currentUserLogin + ")\" "+
	                "and enable the \"Menu Editor Pro\" menu item."
                );
                return;
            }
        }

		var data = encodeMenuAsJSON(tree);
		$('#ws_data').val(data);
		$('#ws_data_length').val(data.length);
		$('#ws_selected_actor').val(actorSelectorWidget.selectedActor === null ? '' : actorSelectorWidget.selectedActor);

		var selectedMenu = getSelectedMenu();
		if (selectedMenu.length > 0) {
			$('#ws_selected_menu_url').val(AmeEditorApi.getItemDisplayUrl(selectedMenu.data('menu_item')));
			$('#ws_expand_selected_menu').val(selectedMenu.find('.ws_editbox').is(':visible') ? '1' : '');

			var selectedSubmenu = getSelectedSubmenuItem();
			if (selectedSubmenu.length > 0) {
				$('#ws_selected_submenu_url').val(AmeEditorApi.getItemDisplayUrl(selectedSubmenu.data('menu_item')));
				$('#ws_expand_selected_submenu').val(selectedSubmenu.find('.ws_editbox').is(':visible') ? '1' : '');
			}
		}

		$('#ws_main_form').submit();
	});

	//Load default menu - load the default WordPress menu
	$('#ws_load_menu').click(function () {
		if (confirm('Are you sure you want to load the default WordPress menu?')){
			loadMenuConfiguration(defaultMenu);
		}
	});

	//Reset menu - re-load the custom menu. Discards any changes made by user.
	$('#ws_reset_menu').click(function () {
		if (confirm('Undo all changes made in the current editing session?')){
			loadMenuConfiguration(customMenu);
		}
	});

	$('#ws_toggle_editor_layout').click(function () {
		var isCompactLayoutEnabled = menuEditorNode.toggleClass('ws_compact_layout').hasClass('ws_compact_layout');
		$.cookie('ame-compact-layout', isCompactLayoutEnabled ? '1' : '0', {expires: 90});

		var button = $(this);
		if (button.is('input')) {
			var checkMark = '\u2713';
			button.val(button.val().replace(checkMark, ''));
			if (isCompactLayoutEnabled) {
				button.val(checkMark + ' ' + button.val());
			}
		}
	});

	//Export menu - download the current menu as a file
	$('#export_dialog').dialog({
		autoOpen: false,
		closeText: ' ',
		modal: true,
		minHeight: 100
	});

	$('#ws_export_menu').click(function(){
		var button = $(this);
		button.prop('disabled', true);
		button.val('Exporting...');

		$('#export_complete_notice, #download_menu_button').hide();
		$('#export_progress_notice').show();
		var exportDialog = $('#export_dialog');
		exportDialog.dialog('open');

		//Encode the menu.
		try {
			var exportData = encodeMenuAsJSON();
		} catch (error) {
			exportDialog.dialog('close');
			alert(error.message);

			button.val('Export');
			button.prop('disabled', false);
			return;
		}

		//Store the menu for download.
		$.post(
			wsEditorData.adminAjaxUrl,
			{
				'data' : exportData,
				'action' : 'export_custom_menu',
				'_ajax_nonce' : wsEditorData.exportMenuNonce
			},
			/**
			 * @param {Object} data
			 */
			function(data){
				button.val('Export');
				button.prop('disabled', false);

				if ( typeof data.error !== 'undefined' ){
					exportDialog.dialog('close');
					alert(data.error);
				}

				if ( _.has(data, 'download_url') ){
					//window.location = data.download_url;
					$('#download_menu_button').attr('href', _.get(data, 'download_url')).data('filesize', _.get(data, 'filesize'));
					$('#export_progress_notice').hide();
					$('#export_complete_notice, #download_menu_button').show();
				}
			},
			'json'
		);
	});

	$('#ws_cancel_export').click(function(){
		$('#export_dialog').dialog('close');
	});

	$('#download_menu_button').click(function(){
		$('#export_dialog').dialog('close');
	});

	//Import menu - upload an exported menu and show it in the editor
	$('#import_dialog').dialog({
		autoOpen: false,
		closeText: ' ',
		modal: true
	});

	$('#ws_cancel_import').click(function(){
		$('#import_dialog').dialog('close');
	});

	$('#ws_import_menu').click(function(){
		$('#import_progress_notice, #import_progress_notice2, #import_complete_notice, #ws_import_error').hide();
		$('#ws_import_panel').show();
		$('#import_menu_form').resetForm();
		//The "Upload" button is disabled until the user selects a file
		$('#ws_start_import').attr('disabled', 'disabled');

		var importDialog = $('#import_dialog');
		importDialog.find('.hide-when-uploading').show();
		importDialog.dialog('open');
	});

	$('#import_file_selector').change(function(){
		$('#ws_start_import').prop('disabled', ! $(this).val() );
	});

	//This function displays unhandled server side errors. In theory, our upload handler always returns a well-formed
	//response even if there's an error. In practice, stuff can go wrong in unexpected ways (e.g. plugin conflicts).
	function handleUnexpectedImportError(xhr, errorMessage) {
		//The server-side code didn't catch this error, so it's probably something serious
		//and retrying won't work.
		$('#import_menu_form').resetForm();
		$('#ws_import_panel').hide();

		//Display error information.
		$('#ws_import_error_message').text(errorMessage);
		$('#ws_import_error_http_code').text(xhr.status);
		$('#ws_import_error_response').text((xhr.responseText !== '') ? xhr.responseText : '[Empty response]');
		$('#ws_import_error').show();
	}

	//AJAXify the upload form
	$('#import_menu_form').ajaxForm({
		dataType : 'json',
		beforeSubmit: function(formData) {

			//Check if the user has selected a file
			for(var i = 0; i < formData.length; i++){
				if ( formData[i].name === 'menu' ){
					if ( (typeof formData[i].value === 'undefined') || !formData[i].value){
						alert('Select a file first!');
						return false;
					}
				}
			}

			$('#import_dialog').find('.hide-when-uploading').hide();
			$('#import_progress_notice').show();

			$('#ws_start_import').attr('disabled', 'disabled');
			return true;
		},
		success: function(data, status, xhr) {
			$('#import_progress_notice').hide();

			var importDialog = $('#import_dialog');
			if ( !importDialog.dialog('isOpen') ){
				//Whoops, the user closed the dialog while the upload was in progress.
				//Discard the response silently.
				return;
			}

			if ( data === null ) {
				handleUnexpectedImportError(xhr, 'Invalid response from server. Please check your PHP error log.');
				return;
			}

			if ( typeof data.error !== 'undefined' ){
				alert(data.error);
				//Let the user try again
				$('#import_menu_form').resetForm();
				importDialog.find('.hide-when-uploading').show();
			}

			if ( (typeof data.tree !== 'undefined') && data.tree ){
				//Whee, we got back a (seemingly) valid menu. A veritable miracle!
				//Lets load it into the editor.
				var progressNotice = $('#import_progress_notice2').show();
				loadMenuConfiguration(data);
				progressNotice.hide();
				//Display a success notice, then automatically close the window after a few moments
				$('#import_complete_notice').show();
				setTimeout((function(){
					//Close the import dialog
					$('#import_dialog').dialog('close');
				}), 500);
			}

		},
		error: function(xhr, status, errorMessage) {
			handleUnexpectedImportError(xhr, errorMessage);
		}
	});

	/*************************************************************************
	                 Drag & drop items between menu levels
	 *************************************************************************/

	if (wsEditorData.wsMenuEditorPro) {
		//Allow the user to drag sub-menu items to the top level.
		$('#ws_top_menu_dropzone').droppable({
			'hoverClass' : 'ws_dropzone_hover',
			'activeClass' : 'ws_dropzone_active',

			'accept' : (function(thing){
				return thing.hasClass('ws_item');
			}),

			'drop' : (function(event, ui){
				var droppedItemData = readItemState(ui.draggable);
				var newItemNodes = pasteMenu(droppedItemData);

				//If the item was originally a top level menu, also move its original submenu items.
				if (getFieldValue(droppedItemData, 'parent') === null) {
					var droppedItemFile = getFieldValue(droppedItemData, 'file');
					var nearbyItems = $(ui.draggable).siblings('.ws_item');
					nearbyItems.each(function() {
						var containerNode = $(this),
							submenuItem = containerNode.data('menu_item');

						//Was this item originally a child of the dragged menu?
						if (getFieldValue(submenuItem, 'parent') === droppedItemFile) {
							pasteItem(submenuItem, newItemNodes.submenu);
							if ( !event.ctrlKey ) {
								containerNode.remove();
							}
						}
					});
				}

				if ( !event.ctrlKey ) {
					ui.draggable.remove();
				}
			})
		});

		//...and to drag top level menus to a sub-menu.
		submenuBox.closest('.ws_main_container').droppable({
			'hoverClass' : 'ws_top_to_submenu_drop_hover',

			'accept' : (function(thing){
				var visibleSubmenu = $('#ws_submenu_box').find('.ws_submenu:visible');
				return (
					//Accept top-level menus
					thing.hasClass('ws_menu') &&

					//Prevent users from dropping a menu on its own sub-menu.
					(visibleSubmenu.attr('id') !== thing.data('submenu_id'))
				);
			}),

			'drop' : (function(event, ui){
				var droppedItemData = readItemState(ui.draggable);
				pasteItem(droppedItemData);
				if ( !event.ctrlKey ) {
					ui.draggable.remove();
				}
			})
		});
	}

	/******************************************************************
	                 Component visibility settings
	 ******************************************************************/

	var $generalVisBox = $('#ws_ame_general_vis_box'),
		$showAdminMenu = $('#ws_ame_show_admin_menu'),
		$showWpToolbar = $('#ws_ame_show_toolbar');

	AmeEditorApi.actorCanSeeComponent = function(component, actorId) {
		if (actorId === null) {
			return _.some(actorSelectorWidget.getVisibleActors(), function(actor) {
				return AmeEditorApi.actorCanSeeComponent(component, actor.id);
			});
		}

		var actorSpecificSetting = _.get(generalComponentVisibility, [component, actorId], null);
		if (actorSpecificSetting !== null) {
			return actorSpecificSetting;
		}

		//Super Admin can see everything by default.
		if (actorId === AmeSuperAdmin.permanentActorId) {
			return _.get(generalComponentVisibility, [component, AmeSuperAdmin.permanentActorId], true);
		}

		var actor = AmeActors.getActor(actorId);
		if (actor instanceof AmeUser) {
			var grants = _.get(generalComponentVisibility, component, {});

			//Super Admin has priority.
			if (actor.isSuperAdmin) {
				return AmeEditorApi.actorCanSeeComponent(component, AmeSuperAdmin.permanentActorId);
			}

			//The user can see the admin menu/Toolbar if at least one of their roles can see it.
			var result = null;
			_.forEach(actor.roles, function(roleName) {
				var allow = _.get(grants, 'role:' + roleName, true);
				if (result === null) {
					result = allow;
				} else {
					result = result || allow;
				}
			});

			if (result !== null) {
				return result;
			}
		}

		//Everyone can see the admin menu and the Toolbar by default.
		return true;
	};

	AmeEditorApi.refreshComponentVisibility = function() {
		if ($generalVisBox.length < 1) {
			return;
		}

		var actorId = actorSelectorWidget.selectedActor;
		$showAdminMenu.prop('checked', AmeEditorApi.actorCanSeeComponent('adminMenu', actorId));
		$showWpToolbar.prop('checked', AmeEditorApi.actorCanSeeComponent('toolbar', actorId));
	};

	AmeEditorApi.setComponentVisibility = function(section, actorId, enabled) {
		if (actorId === null) {
			_.forEach(actorSelectorWidget.getVisibleActors(), function(actor) {
				_.set(generalComponentVisibility, [section, actor.id], enabled);
			});
		} else {
			_.set(generalComponentVisibility, [section, actorId], enabled);
		}
	};

	if ($generalVisBox.length > 0) {
		$showAdminMenu.click(function() {
			AmeEditorApi.setComponentVisibility(
				'adminMenu',
				actorSelectorWidget.selectedActor,
				$(this).is(':checked')
			);
		});
		$showWpToolbar.click(function () {
			AmeEditorApi.setComponentVisibility(
				'toolbar',
				actorSelectorWidget.selectedActor,
				$(this).is(':checked')
			);
		});

		$generalVisBox.find('.handlediv').click(function() {
			$generalVisBox.toggleClass('closed');
			$.cookie(
				'ame_vis_box_open',
				($generalVisBox.hasClass('closed') ? '0' : '1'),
				{ expires: 90 }
			);
		});

		actorSelectorWidget.onChange(function() {
			AmeEditorApi.refreshComponentVisibility();
		});
	}

	/******************************************************************
	                      Tooltips and hints
	 ******************************************************************/


	//Set up tooltips
	$('.ws_tooltip_trigger').qtip({
		style: {
			classes: 'qtip qtip-rounded ws_tooltip_node'
		},
		hide: {
			fixed: true,
			delay: 300
		}
	});

	//Set up menu field toltips.
	menuEditorNode.on('mouseenter click', '.ws_edit_field .ws_field_tooltip_trigger', function(event) {
		var $trigger = $(this),
			fieldName = $trigger.closest('.ws_edit_field').data('field_name');

		if (knownMenuFields[fieldName].tooltip === null) {
			return;
		}

		var tooltipText = 'Invalid tooltip';
		if (typeof knownMenuFields[fieldName].tooltip === 'string') {
			tooltipText = knownMenuFields[fieldName].tooltip;
		} else if (typeof knownMenuFields[fieldName].tooltip === 'function') {
			tooltipText = function() {
				var $theTrigger = $(this),
					menuItem = $theTrigger.closest('.ws_container').data('menu_item');
				return knownMenuFields[fieldName].tooltip(menuItem);
			}
		}

		$trigger.qtip({
			overwrite: false,
			content: {
				text: tooltipText
			},
			show: {
				event: event.type,
				ready: true //Show immediately.
			},
			style: {
				classes: 'qtip qtip-rounded ws_tooltip_node'
			},
			hide: {
				fixed: true,
				delay: 300
			},
			position: {
				my: 'bottom center',
				at: 'top center'
			}
		}, event);
	});

	//Set up the "additional permissions are available" tooltips.
	menuEditorNode.on('mouseenter click', '.ws_ext_permissions_indicator', function() {
		var $indicator = $(this);
		$indicator.qtip({
			overwrite: false,
			content: {
				text: function() {
					var indicator = $(this),
						extPermissions = indicator.data('ext_permissions'),
						text = 'Additional permission settings are available. Click "Edit..." to change them.',
						heading = '';

					if (extPermissions && extPermissions.hasOwnProperty('title')) {
						heading = extPermissions.title;
						if (extPermissions.hasOwnProperty('type')) {
							heading = _.capitalize(_.startCase(extPermissions.type).toLowerCase()) + ': ' + heading;
						}
						text = '<strong>' + heading + '</strong><br>' + text;
					}

					return text;
				}
			},
			show: {
				ready: true //Show immediately.
			},
			style: {
				classes: 'qtip qtip-rounded ws_tooltip_node'
			},
			hide: {
				fixed: true,
				delay: 300
			},
			position: {
				my: 'bottom center',
				at: 'top center'
			}
		});
	});

	//Flag closed hints as hidden by sending the appropriate AJAX request to the backend.
	$('.ws_hint_close').click(function() {
		var hint = $(this).parents('.ws_hint').first();
		hint.hide();
		wsEditorData.showHints[hint.attr('id')] = false;
		$.post(
			wsEditorData.adminAjaxUrl,
			{
				'action' : 'ws_ame_hide_hint',
				'hint' : hint.attr('id')
			}
		);
	});

	//Expand/collapse the "How To" box.
	var $howToBox = $("#ws_ame_how_to_box");
	$howToBox.find(".handlediv").click(function() {
		$howToBox.toggleClass('closed');
		$.cookie(
			'ame_how_to_box_open',
			($howToBox.hasClass('closed') ? '0' : '1'),
			{ expires: 180 }
		);
	});


	/******************************************************************
	                           Actor views
	 ******************************************************************/

	if (wsEditorData.wsMenuEditorPro) {
		actorSelectorWidget.onChange(function() {
			//There are some UI elements that can be visible or hidden depending on whether an actor is selected.
			var editorNode = $('#ws_menu_editor');
			editorNode.toggleClass('ws_is_actor_view', (actorSelectorWidget.selectedActor !== null));

			//Update the menu item states to indicate whether they're accessible.
			editorNode.find('.ws_container').each(function() {
				updateActorAccessUi($(this));
			});
		});

		if (wsEditorData.hasOwnProperty('selectedActor') && wsEditorData.selectedActor) {
			actorSelectorWidget.setSelectedActor(wsEditorData.selectedActor);
		} else {
			actorSelectorWidget.setSelectedActor(null);
		}
	}

	/******************************************************************
	                        "Test Access" feature
	 ******************************************************************/
	var testAccessDialog = $('#ws_ame_test_access_screen').dialog({
			autoOpen: false,
			modal: true,
			closeText: ' ',
			title: 'Test access',
			width: 900
			//draggable: false
		}),
		testMenuItemList = $('#ws_ame_test_menu_item'),
		testActorList = $('#ws_ame_test_relevant_actor'),
		testAccessButton = $('#ws_ame_start_access_test'),
		testAccessFrame = $('#ws_ame_test_access_frame'),
		testConfig = null,

		testProgress = $('#ws_ame_test_progress'),
		testProgressText = $('#ws_ame_test_progress_text');

	$('#ws_test_access').click(function () {
		testConfig = readMenuTreeState();

		var selectedMenuContainer = getSelectedMenu(),
			selectedItemContainer = getSelectedSubmenuItem(),
			selectedMenu = null,
			selectedItem = null,
			selectedUrl = null;
		if (selectedMenuContainer.length > 0) {
			selectedMenu = selectedMenuContainer.data('menu_item');
			selectedUrl = getFieldValue(selectedMenu, 'url');
		}
		if (selectedItemContainer.length > 0) {
			selectedItem = selectedItemContainer.data('menu_item');
			selectedUrl = getFieldValue(selectedItem, 'url');
		}

		function addMenuItems(collection, parentTitle, parentFile) {
			_.each(collection, function (menuItem) {
				if (menuItem.separator) {
					return;
				}

				var title = formatMenuTitle(getFieldValue(menuItem, 'menu_title', '[Untitled menu]'));
				if (parentTitle) {
					title = parentTitle + ' -> ' + title;
				}
				var url = getFieldValue(menuItem, 'url', '[no-url]');

				var option = $(
					'<option>', {
						val: url,
						text: title
					}
				);
				option.data('menu_item', menuItem);
				option.data('parent_file', parentFile || '');
				option.prop('selected', (url === selectedUrl));

				testMenuItemList.append(option);

				if (menuItem.items) {
					addMenuItems(menuItem.items, title, getFieldValue(menuItem, 'file', ''));
				}
			});
		}

		//Populate the list of menu items.
		testMenuItemList.empty();
		addMenuItems(testConfig.tree);

		//Populate the actor list.
		testActorList.empty();
		testActorList.append($('<option>', {text: 'Not selected', val: ''}));
		_.each(actorSelectorWidget.getVisibleActors(), function (actor) {
			//TODO: Skip anything that isn't a role
			var option = $('<option>', {
				val: actor.id,
				text: actorSelectorWidget.getNiceName(actor)
			});
			testActorList.append(option);
		});

		//Pre-select the current actor.
		if (actorSelectorWidget.selectedActor !== null) {
			testActorList.val(actorSelectorWidget.selectedActor);
		}

		testAccessDialog.dialog('open');
	});

	testAccessButton.click(function () {
		testAccessButton.prop('disabled', true);
		testProgress.show();
		testProgressText.text('Sending menu settings...');

		var selectedOption = testMenuItemList.find('option:selected').first(),
			selectedMenu = selectedOption.data('menu_item'),
			menuUrl = selectedOption.val();

		$.ajax(
			wsEditorData.adminAjaxUrl,
			{
				data: {
					'action': 'ws_ame_set_test_configuration',
					'data': encodeMenuAsJSON(testConfig),
					'_ajax_nonce': wsEditorData.setTestConfigurationNonce
				},
				method: 'post',
				dataType: 'json',
				success: function(response, textStatus) {
					if (!response) {
						alert('Error: Could not parse the server response.');
						testAccessButton.prop('disabled', false);
						return;
					}
					if (response.error) {
						alert(response.error);
						testAccessButton.prop('disabled', false);
						return;
					}
					if (!response.success) {
						alert('Error: The request failed, but there is no error information available.');
						testAccessButton.prop('disabled', false);
						return;
					}

					//Caution: Won't work in IE. Needs compat checks.
					var testPageUrl = new URL(menuUrl, window.location.href);
					testPageUrl.searchParams.append('ame-test-menu-access-as', $('#ws_ame_test_access_username').val());
					testPageUrl.searchParams.append('_wpnonce', wsEditorData.testAccessNonce);
					testPageUrl.searchParams.append('ame-test-relevant-role', testActorList.val());

					testPageUrl.searchParams.append('ame-test-target-item', getFieldValue(selectedMenu, 'file', ''));
					testPageUrl.searchParams.append('ame-test-target-parent', selectedOption.data('parent_file'));

					testProgressText.text('Loading the test page....');
					$('#ws_ame_test_frame_placeholder').hide();

					$(window).on('message', receiveTestAccessResults);
					testAccessFrame
						.show()
						.on('load', onAccessTestLoaded)
						.prop('src', testPageUrl.href);
				},
				error: function(jqXHR, textStatus) {
					alert('HTTP Error: ' + textStatus);
					testAccessButton.prop('disabled', false);
				}
			}
		);
	});

	function onAccessTestLoaded() {
		testAccessFrame.off('load', onAccessTestLoaded);
		testProgress.hide();

		testAccessButton.prop('disabled', false);
	}

	function receiveTestAccessResults(event) {
		if (event.originalEvent.source !== testAccessFrame.get(0).contentWindow) {
			if (console && console.warn) {
				console.warn('AME: Received a message from an unexpected source. Message ignored.');
			}
			return;
		}
		var message = event.originalEvent.data || event.originalEvent.message;
		console.log('message received', message);

		$(window).off('message', receiveTestAccessResults);
	}


	//Finally, show the menu
	loadMenuConfiguration(customMenu);

	//Select the previous selected menu, if any.
	if (wsEditorData.selectedMenu) {
		AmeEditorApi.selectMenuItemByUrl(
			'#ws_menu_box',
			wsEditorData.selectedMenu,
			_.get(wsEditorData, 'expandSelectedMenu') === '1'
		);

		if (wsEditorData.selectedSubmenu) {
			AmeEditorApi.selectMenuItemByUrl(
				'#ws_submenu_box',
				wsEditorData.selectedSubmenu,
				_.get(wsEditorData, 'expandSelectedSubmenu') === '1'
			);
		}
	}

	//... and make the UI visible now that it's fully rendered.
	menuEditorNode.css('visibility', 'visible');
}

$(document).ready(ameOnDomReady);

//Compatibility workaround: If another plugin or theme throws an exception in its jQuery.ready() handler,
//our callback might never get run. As a backup, set a timer and manually check if the DOM is ready.
var domCheckAttempts = 0,
	maxDomCheckAttempts = 30;
var domCheckIntervalId = window.setInterval(function () {
	if (isDomReadyDone || (domCheckAttempts >= maxDomCheckAttempts)) {
		window.clearInterval(domCheckIntervalId);
		return;
	}
	domCheckAttempts++;

	if ($ && $.isReady) {
		isDomReadyDone = true;
		ameOnDomReady();
	}
}, 1000);

})(jQuery, wsAmeLodash);

//==============================================
//				Screen options
//==============================================

jQuery(function($){
	'use strict';

	var screenOptions = $('#ws-ame-screen-meta-contents');
	var hideSettingsCheckbox = screenOptions.find('#ws-hide-advanced-settings');
	hideSettingsCheckbox.prop('checked', wsEditorData.hideAdvancedSettings);

	//Update editor state when settings change
	$('#ws-hide-advanced-settings').click(function(){
		wsEditorData.hideAdvancedSettings = hideSettingsCheckbox.prop('checked');

		//Show/hide advanced settings dynamically as the user changes the setting.
		if ($(this).is(hideSettingsCheckbox)) {
			var menuEditorNode = $('#ws_menu_editor');
			if ( wsEditorData.hideAdvancedSettings ){
				menuEditorNode.find('div.ws_advanced').hide();
				menuEditorNode.find('a.ws_toggle_advanced_fields').text(wsEditorData.captionShowAdvanced).show();
			} else {
				menuEditorNode.find('div.ws_advanced').show();
				menuEditorNode.find('a.ws_toggle_advanced_fields').text(wsEditorData.captionHideAdvanced).hide();
			}
		}

		$.post(
			wsEditorData.adminAjaxUrl,
			{
				'action' : 'ws_ame_save_screen_options',
				'hide_advanced_settings' : wsEditorData.hideAdvancedSettings ? 1 : 0,
				'show_extra_icons' : wsEditorData.showExtraIcons ? 1 : 0,
				'_ajax_nonce' : wsEditorData.hideAdvancedSettingsNonce
			}
		);
	});

	//Move our options into the screen meta panel
	var advSettings = $('#adv-settings');
	if (advSettings.length > 0) {
		advSettings.empty().append(screenOptions.show());
	}
});