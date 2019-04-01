/* globals AmeCapabilityManager, jQuery, AmeActors */

window.AmeItemAccessEditor = (function ($) {
	'use strict';

	/**
	 * A group of related permissions that can be displayed in the extended permissions panel.
	 *
	 * @typedef {Object} ExtPermissionsGroup
	 * @property {string} title           A descriptive title, e.g. the name of the post type.
	 * @property {Object} capabilities    A dictionary of ["readable name" => "capability"].
	 * @property {string} type            What kind of group this is. Examples: "post_type", "taxonomy".
	 * @property {string|null} objectKey  Post type or taxonomy key. Examples: "page", "category".
	 */

	var _,
		api,
		isProVersion = false,
		actorSelector,
		postTypes,
		taxonomies,

		$editor,
		$actorTableRows = null,
		wasDialogCreated = false,
		saveCallback,

		menuItem,
		itemRequiredCap = '',
		containerNode = null,
		selectedActor = null,
		readableNamesEnabled = true,

		/**
		 * @type {ExtPermissionsGroup}
		 */
		extPermissions = null,
		/**
		 * @type {jQuery}
		 */
		$currentExtTable = null,

		hasExtendedPermissions = false,
		unsavedCapabilities = {};

	var defaultDialogWidth = 390,
		extendedDialogWidth = 755;

	function createEditorDialog() {
		$editor = $editor || $('#ws_menu_access_editor');
		$editor.dialog({
			autoOpen: false,
			closeText: ' ',
			modal: true,
			minHeight: 100,
			width: defaultDialogWidth,
			draggable: false
		});

		$editor.find('label.ws_ext_action_name')
			.wrapInner('<span class="ws_ext_readable_name"></span>')
			.append('<span class="ws_ext_capability"></span>');

		$editor.find('#ws_ext_permissions_container')
			.toggleClass('ws_ext_readable_names_enabled', readableNamesEnabled);

		wasDialogCreated = true;
	}

	function setSelectedActor(actor) {
		selectedActor = actor || null;

		//Deselect the previously selected actor.
		$actorTableRows.removeClass('ws_cpt_selected_role');

		//Select the new one.
		if (selectedActor) {
			$actorTableRows.filter(function() {
				return $(this).data('actor') === selectedActor;
			}).addClass('ws_cpt_selected_role');

			$editor.find('.ws_aed_selected_actor_name').text(
				actorSelector.getNiceName(AmeActors.getActor(selectedActor))
			);
		}

		if (hasExtendedPermissions) {
			refreshExtPermissionsTable();
		}
	}

	/**
	 * Get a row from the role/actor table by actor ID.
	 *
	 * @param {string} actor
	 * @returns {jQuery}
	 */
	function getActorRow(actor) {
		return $actorTableRows.filter(function() {
			return $(this).data('actor') === actor;
		});
	}

	function refreshExtPermissionsTable() {
		//Show what permissions the actor has for this CPT or taxonomy.
		if (!hasExtendedPermissions) {
			return;
		}

		var actions = $currentExtTable.find('tr td.ws_ext_action_check_column input[type="checkbox"]');
		actions.each(function() {
			var actionCheckbox = $(this),
				requiredCapability = extPermissions.capabilities[actionCheckbox.data('ext_action')],
				hasCap = AmeCapabilityManager.hasCap(selectedActor, requiredCapability, unsavedCapabilities),
				hasCapByDefault = AmeCapabilityManager.hasCapByDefault(selectedActor, requiredCapability);
			actionCheckbox.prop('checked', hasCap);

			//Flag settings that don't match the default. This can help find problems.
			actionCheckbox.closest('tr').toggleClass('ws_ext_has_custom_setting', hasCap !== hasCapByDefault);
		});
	}

	/**
	 * Get the available permission settings for the post type or taxonomy that a URL refers to.
	 *
	 * Taxonomy has precedence over post type because it's less common in admin menus, and thus more notable.
	 * If a URL mentions both, this function only returns the taxonomy. Returns null if the URL isn't related to
	 * any CPTs or taxonomies.
	 *
	 * @param {string} url
	 * @returns {ExtPermissionsGroup|null}
	 */
	function detectExtPermissions(url) {
		url = url || '';
		//To ease parsing, convert "something.php" to "/wp-admin/something.php". Otherwise the parser will think
		//"something.php" is a domain name.
		if (/^[\w\-]+?\.php/.test(url)) {
			url = '/wp-admin/' + url;
		}

		var parsed = parseUri(url);
		if (_.includes(['edit.php', 'post-new.php', 'edit-tags.php'], parsed.file)) {
			var taxonomy = _.get(parsed, 'queryKey.taxonomy', null),
				postType = _.get(parsed, 'queryKey.post_type', null);

			if (taxonomy && taxonomies.hasOwnProperty(taxonomy)) {
				return _.assign({}, taxonomies[taxonomy], {type: 'taxonomy', objectKey: taxonomy});
			} else if (postType && postTypes.hasOwnProperty(postType)) {
				return _.assign({}, postTypes[postType], {type: 'post_type', objectKey: postType});
			} else if ((parsed.file === 'edit-tags.php') && (taxonomies.hasOwnProperty('category'))) {
				return _.assign({}, _.get(taxonomies, 'category'), {type: 'taxonomy', objectKey: 'category'});
			} else if (postTypes.hasOwnProperty('post')) {
				return _.assign({}, _.get(postTypes, 'post'), {type: 'post_type', objectKey: 'post'});
			}
		}
		return null;
	}

	// parseUri 1.2.2
	// (c) Steven Levithan [http://stevenlevithan.com]
	// MIT License
	// Modified: Added partial URL-decoding support.

	function parseUri(str) {
		var	o   = parseUri.options,
			m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
			uri = {},
			i   = 14;

		while (i--) { uri[o.key[i]] = m[i] || ""; }

		uri[o.q.name] = {};
		uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
			if ($1) {
				//Decode percent-encoded query parameters.
				if (o.q.name === 'queryKey') {
					$1 = decodeURIComponent($1);
					$2 = decodeURIComponent($2);
				}
				uri[o.q.name][$1] = $2;
			}
		});

		return uri;
	}

	parseUri.options = {
		strictMode: false,
		key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
		q:   {
			name:   "queryKey",
			parser: /(?:^|&)([^&=]*)=?([^&]*)/g
		},
		parser: {
			strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
			loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
		}
	};

	// --- parseUri ends ---

	//Set up dialog event handlers.
	$(document).ready(function() {
		$editor = $('#ws_menu_access_editor');

		//Select a role or user on click.
		$editor.on('click', '.ws_role_table_body tr', function() {
			if (hasExtendedPermissions) {
				setSelectedActor($(this).closest('tr').data('actor'));
			}
		});

		//Toggle readable names vs capabilities.
		$editor.on('click', '#ws_ext_toggle_capability_names', function() {
			readableNamesEnabled = !readableNamesEnabled;
			$('#ws_ext_permissions_container').toggleClass('ws_ext_readable_names_enabled', readableNamesEnabled);

			//Remember the user's choice.
			$.cookie('ame-readable-capability-names', readableNamesEnabled ? '1' : '0', {expires: 90});
		});

		//Prevent the user from accidentally changing menu permissions when selecting a role.
		$editor.on('click', '.ws_column_role label', function(event) {
			if (hasExtendedPermissions) {
				//Usually, clicking the role label would toggle the access checkbox. This prevents that.
				event.preventDefault();
			}
		});

		//Store changes made by the user in a temporary location.
		$editor.find('.ws_ext_permissions_table').on(
			'change',
			'input.ws_ext_action_allowed',
			function() {
				if (!hasExtendedPermissions) {
					return;
				}

				var checkbox = $(this),
					isAllowed = checkbox.prop('checked'),
					capability = extPermissions.capabilities[checkbox.data('ext_action')],
					hasCapWhenReset;

				//Don't create custom settings unless necessary.
				AmeCapabilityManager.resetCapInContext(unsavedCapabilities, selectedActor, capability);
				hasCapWhenReset = AmeCapabilityManager.hasCap(selectedActor, capability, unsavedCapabilities);
				if (isAllowed !== hasCapWhenReset) {
					AmeCapabilityManager.setCapInContext(
						unsavedCapabilities,
						selectedActor,
						capability,
						isAllowed,
						extPermissions.type,
						extPermissions.objectKey
					);
				}

				//If this is also the cap that's required to access the menu item, update the actor checkbox.
				if (capability === itemRequiredCap) {
					getActorRow(selectedActor).find('input.ws_role_access').prop('checked', isAllowed);
				}

				refreshExtPermissionsTable();
			}
		);

		//Checking a role also gives it the required capability. However, that happens later, on the server side,
		//and we don't want to give the role access to other menus associated with that capability. That means we only
		//grant them that capability HERE if they already had it. Yep, that's not confusing at all.
		$editor.find('.ws_role_table_body').on('change', 'input.ws_role_access', function() {
			if (!hasExtendedPermissions || !itemRequiredCap) {
				return;
			}

			var isAllowed = $(this).prop('checked'),
				hasCap = AmeCapabilityManager.hasCap(selectedActor, itemRequiredCap, unsavedCapabilities),
				hasCapByDefault = AmeCapabilityManager.hasCapByDefault(selectedActor, itemRequiredCap);

			if (isAllowed && hasCapByDefault && !hasCap) {
				AmeCapabilityManager.setCapInContext(
					unsavedCapabilities,
					selectedActor,
					itemRequiredCap,
					true,
					extPermissions.type,
					extPermissions.objectKey
				);
				refreshExtPermissionsTable();
			}
		});

		//The "Save Changes" button.
		$editor.find('#ws_save_access_settings').click(function() {
			//Read the new settings from the form.
			var extraCapability, restrictAccessToItems, grantAccess;

			extraCapability = api.jsTrim($('#ws_extra_capability').val()) || null;
			restrictAccessToItems = $('#ws_restrict_access_to_items').prop('checked');

			grantAccess = $.extend({}, menuItem.grant_access);
			$actorTableRows.each(function() {
				var row = $(this);
				grantAccess[row.data('actor')] = row.find('input.ws_role_access').prop('checked');
			});

			//Notify the editor. It will then update the menu item with the new values and refresh the UI.
			if (saveCallback) {
				saveCallback(
					menuItem,
					containerNode,
					{
						extraCapability       : extraCapability,
						grantAccess           : grantAccess,
						restrictAccessToItems : restrictAccessToItems,
						grantedCapabilities   : unsavedCapabilities
					}
				);
			}

			$editor.dialog('close');
		});
	});

	return {
		/**
		 * @param {AmeEditorApi} config.api
		 * @param {Object}   config.actors
		 * @param {AmeActorSelector}   config.actorSelector
		 * @param {Object}   config.postTypes
		 * @param {Object}   config.taxonomies
		 * @param {lodash}   config.lodash
		 * @param {Function} config.save
		 * @param {boolean}  [config.isPro]
		 *
		 * @param config
		 */
		setup: function(config) {
			_ = config.lodash;
			api = config.api;
			actorSelector = config.actorSelector;

			postTypes = config.postTypes;
			taxonomies = config.taxonomies;

			saveCallback = config.save || null;
			isProVersion = _.get(config, 'isPro', false);

			//Read settings from cookies.
			readableNamesEnabled = $.cookie('ame-readable-capability-names');
			if (typeof readableNamesEnabled === 'undefined') {
				readableNamesEnabled = true;
			} else {
				readableNamesEnabled = (readableNamesEnabled === '1'); //Expected: "1" or "0".
			}
		},

		open: function(state) {
			menuItem = state.menuItem;
			containerNode = state.containerNode;
			unsavedCapabilities = {};
			if (!wasDialogCreated) {
				createEditorDialog();
			}

			//Write the values of this item to the editor fields.
			itemRequiredCap = api.getFieldValue(menuItem, 'access_level', 'Error: access_level is missing!');
			var requiredCapField = $editor.find('#ws_required_capability').empty();
			if (menuItem.template_id === '') {
				//Custom items have no required caps, only what users set.
				requiredCapField.append('<em>None</em>');
			} else {
				requiredCapField.text(itemRequiredCap);
			}

			$editor.find('#ws_extra_capability').val(api.getFieldValue(menuItem, 'extra_capability', ''));
			$editor.find('#ws_restrict_access_to_items').prop(
				'checked',
				api.getFieldValue(menuItem, 'restrict_access_to_items', false)
			);

			//Generate the actor list.
			var table = $editor.find('.ws_role_table_body tbody').empty(),
				alternate = '',
				visibleActors = actorSelector.getVisibleActors();
			for(var index = 0; index < visibleActors.length; index++) {
				var actor = visibleActors[index];

				var checkboxId = 'allow_' + actor.id.replace(/[^a-zA-Z0-9_]/g, '_');
				var checkbox = $('<input type="checkbox">').addClass('ws_role_access').attr('id', checkboxId);

				var actorHasAccess = api.actorCanAccessMenu(menuItem, actor.id);
				checkbox.prop('checked', actorHasAccess);

				alternate = (alternate === '') ? 'alternate' : '';

				var cell = '<td>';
				var row = $('<tr>').data('actor', actor.id).attr('class', alternate).append(
					$(cell).addClass('ws_column_access').append(checkbox),
					$(cell).addClass('ws_column_role post-title').append(
						$('<label>').attr('for', checkboxId).append(
							$('<span>').text(actorSelector.getNiceName(actor))
						)
					),
					$(cell).addClass('ws_column_selected_role_tip').append(
						$('<div></div>').addClass('ws_cpt_selected_role_tip')
					)
				);

				table.append(row);
			}

			//We'll need these rows when dealing with CPTs and taxonomies.
			$actorTableRows = table.find('tr');

			//Hide the submenu override checkbox when the item doesn't have submenus.
			$editor.find('#ws_item_restriction_container').toggle(state.itemHasSubmenus);

			//Warn the user if the required capability == role. Can't make it less restrictive.
			$('#ws_hardcoded_role_error').toggle(AmeCapabilityManager.roleExists(itemRequiredCap));
			$('#ws_hardcoded_role_name').text(itemRequiredCap);

			//Detect post type or taxonomy.
			extPermissions = isProVersion ? detectExtPermissions(api.getItemDisplayUrl(menuItem)) : null;
			hasExtendedPermissions = !!extPermissions;

			var panelNode = $('#ws_ext_permissions_container');
			if (hasExtendedPermissions) {
				//Display the extended panel.
				panelNode.show();

				//Display only one of the tables.
				$editor.find('.ws_ext_permissions_table').hide();
				$currentExtTable = $editor.find('#ws_' + extPermissions.type + '_permissions_table').show();

				//Show the group title.
				panelNode.find('#ws_ext_selected_object_type_name').text(extPermissions.title);

				//Often, the "create" option is redundant because it uses the same capability as "edit".
				if (extPermissions.type === 'post_type') {
					var createCap = _.get(extPermissions, 'capabilities.create_posts'),
						editCap   = _.get(extPermissions, 'capabilities.edit_posts');
					$('#ws_cpt_action-create_posts').closest('tr').toggle(createCap !== editCap);
				}

				//Set caps. This way the user can mouse over an action to see the corresponding capability.
				_.forEach(_.get(extPermissions, 'capabilities', []), function(capability, action) {
					var title = capability;
					if (capability === itemRequiredCap) {
						title = title + ' (The required capability for this menu item.)';
					}
					$currentExtTable
						.find('tr.ws_ext_action-' + action + ' label.ws_ext_action_name')
						.attr('title', title)
						.toggleClass('ws_same_as_required_cap', capability === itemRequiredCap)
						.find('.ws_ext_capability').text(capability);
				});
			} else {
				panelNode.hide();
				$currentExtTable = null;
			}

			$editor.dialog('open');

			//Make the dialog wider/narrower to accommodate the ext. permissions panel.
			var desiredWidth = hasExtendedPermissions ? extendedDialogWidth : defaultDialogWidth;
			if ($editor.dialog('option', 'width') !== desiredWidth) {
				$editor.dialog('option', 'width', desiredWidth);
			}

			if (hasExtendedPermissions) {
				//Select either the currently selected actor, or just the first one.
				setSelectedActor(state.selectedActor || (visibleActors[0].id) || null);

				//The permission table must be at least as tall as the actor list or the selected row won't look right.
				var roleTable = $editor.find('.ws_role_table_body'),
					heightDifference = roleTable.height() - $currentExtTable.height(),
					paddingCell = $currentExtTable.find('.ws_ext_padding_row td'),
					paddingCellHeight = paddingCell.height();

				if (Math.abs(heightDifference) >= 1) {
					paddingCell.height(Math.max(heightDifference + paddingCellHeight, 1));
				}
			} else {
				setSelectedActor(null);
			}

			//Enable role hover and selection effects if there is a CPT or taxonomy to display.
			$('#ws_role_access_container').toggleClass('ws_has_extended_permissions', hasExtendedPermissions);
		},

		getCurrentMenuItem: function() {
			return menuItem;
		},

		detectExtPermissions: detectExtPermissions
	};
})(jQuery);
