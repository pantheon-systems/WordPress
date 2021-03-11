/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 11);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

/* global menuIcons:false */

__webpack_require__(2);

(function ($) {
	var miPicker;

	if (!menuIcons.activeTypes || _.isEmpty(menuIcons.activeTypes)) {
		return;
	}

	/**
  * @namespace
  * @property {object} templates - Cached templates for the item previews on the fields
  * @property {string} wrapClass - Field wrapper's class
  * @property {object} frame     - Menu Icons' media frame instance
  * @property {object} target    - Frame's target model
  */
	miPicker = {
		templates: {},
		wrapClass: 'div.oe-icons-wrap',
		frame: null,
		target: new wp.media.model.IconPickerTarget(),

		/**
   * Callback function to filter active icon types
   *
   * TODO: Maybe move to frame view?
   *
   * @param {string} type - Icon type.
   */
		typesFilter: function typesFilter(type) {
			return $.inArray(type.id, menuIcons.activeTypes) >= 0;
		},

		/**
   * Create Menu Icons' media frame
   */
		createFrame: function createFrame() {
			miPicker.frame = new wp.media.view.MediaFrame.MenuIcons({
				target: miPicker.target,
				ipTypes: _.filter(iconPicker.types, miPicker.typesFilter),
				SidebarView: wp.media.view.MenuIconsSidebar
			});
		},

		/**
   * Pick icon for a menu item and open the frame
   *
   * @param {object} model - Menu item model.
   */
		pickIcon: function pickIcon(model) {
			miPicker.frame.target.set(model, { silent: true });
			miPicker.frame.open();
		},

		/**
   * Set or unset icon
   *
   * @param {object} e - jQuery click event.
   */
		setUnset: function setUnset(e) {
			var $el = $(e.currentTarget),
			    $clicked = $(e.target);

			e.preventDefault();

			if ($clicked.hasClass('_select') || $clicked.hasClass('_icon')) {
				miPicker.setIcon($el);
			} else if ($clicked.hasClass('_remove')) {
				miPicker.unsetIcon($el);
			}
		},

		/**
   * Set Icon
   *
   * @param {object} $el - jQuery object.
   */
		setIcon: function setIcon($el) {
			var id = $el.data('id'),
			    frame = miPicker.frame,
			    items = frame.menuItems,
			    model = items.get(id);

			if (model) {
				miPicker.pickIcon(model.toJSON());
				return;
			}

			model = {
				id: id,
				$el: $el,
				$title: $('#edit-menu-item-title-' + id),
				$inputs: {}
			};

			// Collect menu item's settings fields and use them
			// as the model's attributes.
			$el.find('div._settings input').each(function () {
				var $input = $(this),
				    key = $input.attr('class').replace('_oe-', ''),
				    value = $input.val();

				if (!value) {
					if (_.has(menuIcons.menuSettings, key)) {
						value = menuIcons.menuSettings[key];
					} else if (_.has(menuIcons.settingsFields, key)) {
						value = menuIcons.settingsFields[key]['default'];
					}
				}

				model[key] = value;
				model.$inputs[key] = $input;
			});

			items.add(model);
			miPicker.pickIcon(model);
		},

		/**
   * Unset icon
   *
   * @param {object} $el - jQuery object.
   */
		unsetIcon: function unsetIcon($el) {
			var id = $el.data('id');

			$el.find('div._settings input').val('');
			$el.trigger('mi:update');
			miPicker.frame.menuItems.remove(id);
		},

		/**
   * Update valeus of menu item's setting fields
   *
   * When the type and icon is set, this will (re)generate the icon
   * preview on the menu item field.
   *
   * @param {object} e - jQuery event.
   */
		updateField: function updateField(e) {
			var $el = $(e.currentTarget),
			    $set = $el.find('a._select'),
			    $unset = $el.find('a._remove'),
			    type = $el.find('input._oe-type').val(),
			    icon = $el.find('input._oe-icon').val(),
			    url = $el.find('input._oe-url').val(),
			    template;

			if (type === '' || icon === '' || _.indexOf(menuIcons.activeTypes, type) < 0) {
				$set.text(menuIcons.text.select).attr('title', '');
				$unset.addClass('hidden');

				return;
			}

			if (miPicker.templates[type]) {
				template = miPicker.templates[type];
			} else {
				template = miPicker.templates[type] = wp.template('oe-icons-item-field-preview-' + iconPicker.types[type].templateId);
			}

			$unset.removeClass('hidden');
			$set.attr('title', menuIcons.text.change);
			$set.html(template({
				type: type,
				icon: icon,
				url: url
			}));
		},

		/**
   * Initialize picker functionality
   *
   * #fires mi:update
   */
		init: function init() {
			miPicker.createFrame();
			$(document).on('click', miPicker.wrapClass, miPicker.setUnset).on('mi:update', miPicker.wrapClass, miPicker.updateField);

			// Trigger 'mi:update' event to generate the icons on the item fields.
			$(miPicker.wrapClass).trigger('mi:update');
		}
	};

	miPicker.init();
})(jQuery);

/***/ }),
/* 1 */
/***/ (function(module, exports) {

(function ($) {
	/**
  * Settings box tabs
  *
  * We can't use core's tabs script here because it will clear the
  * checkboxes upon tab switching
  */
	$('#oe-icons-settings-tabs').on('click', 'a.oe-settings-nav-tab', function (e) {
		var $el = $(this).blur(),
		    $target = $('#' + $el.data('type'));

		e.preventDefault();
		e.stopPropagation();

		$el.parent().addClass('tabs').siblings().removeClass('tabs');
		$target.removeClass('tabs-panel-inactive').addClass('tabs-panel-active').show().siblings('div.tabs-panel').hide().addClass('tabs-panel-inactive').removeClass('tabs-panel-active');
	}).find('a.oe-settings-nav-tab').first().click();

	// Settings meta box
	$('#oe-icons-settings-save').on('click', function (e) {

		var $button = $(this).prop('disabled', true),
		    $spinner = $button.siblings('span.spinner');

		e.preventDefault();
		e.stopPropagation();

		$spinner.css({
			display: 'inline-block',
			visibility: 'visible'
		});

		$.ajax({
			type: 'POST',
			url: window.menuIcons.ajaxUrls.update,
			data: $('#oe-icons-settings :input').serialize(),

			success: function success(response) {
				if (true === response.success && response.data.redirectUrl) {
					window.location = response.data.redirectUrl;
				} else {
					$button.prop('disabled', false);
				}
			},

			always: function always() {
				$spinner.hide();
			}
		});
	});
})(jQuery);

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

wp.media.model.MenuIconsItemSettingField = __webpack_require__(3);
wp.media.model.MenuIconsItemSettings = __webpack_require__(4);
wp.media.model.MenuIconsItem = __webpack_require__(5);

wp.media.view.MenuIconsItemSettingField = __webpack_require__(8);
wp.media.view.MenuIconsItemSettings = __webpack_require__(9);
wp.media.view.MenuIconsItemPreview = __webpack_require__(7);
wp.media.view.MenuIconsSidebar = __webpack_require__(10);
wp.media.view.MediaFrame.MenuIcons = __webpack_require__(6);

/***/ }),
/* 3 */
/***/ (function(module, exports) {

/**
 * wp.media.model.MenuIconsItemSettingField
 *
 * @class
 * @augments Backbone.Model
 */
var MenuIconsItemSettingField = Backbone.Model.extend({
	defaults: {
		id: '',
		label: '',
		value: '',
		type: 'text'
	}
});

module.exports = MenuIconsItemSettingField;

/***/ }),
/* 4 */
/***/ (function(module, exports) {

/**
 * wp.media.model.MenuIconsItemSettings
 *
 * @class
 * @augments Backbone.Collection
 */
var MenuIconsItemSettings = Backbone.Collection.extend({
  model: wp.media.model.MenuIconsItemSettingField
});

module.exports = MenuIconsItemSettings;

/***/ }),
/* 5 */
/***/ (function(module, exports) {

/**
 * wp.media.model.MenuIconsItem
 *
 * @class
 * @augments Backbone.Model
 */
var Item = Backbone.Model.extend({
	initialize: function initialize() {
		this.on('change', this.updateValues, this);
	},

	/**
  * Update the values of menu item's settings fields
  *
  * #fires mi:update
  */
	updateValues: function updateValues() {
		_.each(this.get('$inputs'), function ($input, key) {
			$input.val(this.get(key));
		}, this);

		// Trigger the 'mi:update' event to regenerate the icon on the field.
		this.get('$el').trigger('mi:update');
	}
});

module.exports = Item;

/***/ }),
/* 6 */
/***/ (function(module, exports) {

/**
 * wp.media.view.MediaFrame.MenuIcons
 *
 * @class
 * @augments wp.media.view.MediaFrame.IconPicker
 * @augments wp.media.view.MediaFrame.Select
 * @augments wp.media.view.MediaFrame
 * @augments wp.media.view.Frame
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MenuIcons = wp.media.view.MediaFrame.IconPicker.extend({
	initialize: function initialize() {
		this.menuItems = new Backbone.Collection([], {
			model: wp.media.model.MenuIconsItem
		});

		wp.media.view.MediaFrame.IconPicker.prototype.initialize.apply(this, arguments);
		if (this.setMenuTabPanelAriaAttributes) {
			// Set the menu ARIA tab panel attributes when the modal opens.
			this.off( 'open', this.setMenuTabPanelAriaAttributes, this );
			// Set the router ARIA tab panel attributes when the modal opens.
			this.off( 'open', this.setRouterTabPanelAriaAttributes, this );

			// Update the menu ARIA tab panel attributes when the content updates.
			this.off( 'content:render', this.setMenuTabPanelAriaAttributes, this );
			// Update the router ARIA tab panel attributes when the content updates.
			this.off( 'content:render', this.setRouterTabPanelAriaAttributes, this );
		}

		this.listenTo(this.target, 'change', this.miUpdateItemProps);
		this.on('select', this.miClearTarget, this);
	},

	miUpdateItemProps: function miUpdateItemProps(props) {
		var model = this.menuItems.get(props.id);

		model.set(props.changed);
	},

	miClearTarget: function miClearTarget() {
		this.target.clear({ silent: true });
	}
});

module.exports = MenuIcons;

/***/ }),
/* 7 */
/***/ (function(module, exports) {

/**
 * wp.media.view.MenuIconsItemPreview
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MenuIconsItemPreview = wp.media.View.extend({
	tagName: 'p',
	className: 'oe-preview menu-item attachment-info',
	events: {
		'click a': 'preventDefault'
	},

	initialize: function initialize() {
		wp.media.View.prototype.initialize.apply(this, arguments);
		this.model.on('change', this.render, this);
	},

	render: function render() {
		var frame = this.controller,
		    state = frame.state(),
		    selected = state.get('selection').single(),
		    props = this.model.toJSON(),
		    data = _.extend(props, {
			type: state.id,
			icon: selected.id,
			title: this.model.get('$title').val(),
			url: state.ipGetIconUrl(selected, props.image_size)
		}),
		    template = 'oe-icons-item-sidebar-preview-' + iconPicker.types[state.id].templateId + '-';

		if (data.hide_label) {
			template += 'hide_label';
		} else {
			template += data.position;
		}

		this.template = wp.media.template(template);
		this.$el.html(this.template(data));

		return this;
	},

	preventDefault: function preventDefault(e) {
		e.preventDefault();
	}
});

module.exports = MenuIconsItemPreview;

/***/ }),
/* 8 */
/***/ (function(module, exports) {

var $ = jQuery,
    MenuIconsItemSettingField;

/**
 * wp.media.view.MenuIconsItemSettingField
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
MenuIconsItemSettingField = wp.media.View.extend({
	tagName: 'label',
	className: 'setting',
	events: {
		'change :input': '_update'
	},

	initialize: function initialize() {
		wp.media.View.prototype.initialize.apply(this, arguments);

		this.template = wp.media.template('oe-icons-settings-field-' + this.model.get('type'));
		this.model.on('change', this.render, this);
	},

	prepare: function prepare() {
		return this.model.toJSON();
	},

	_update: function _update(e) {
		var value = $(e.currentTarget).val();

		this.model.set('value', value);
		this.options.item.set(this.model.id, value);
	}
});

module.exports = MenuIconsItemSettingField;

/***/ }),
/* 9 */
/***/ (function(module, exports) {

/**
 * wp.media.view.MenuIconsItemSettings
 *
 * @class
 * @augments wp.media.view.PriorityList
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MenuIconsItemSettings = wp.media.view.PriorityList.extend({
	className: 'oe-settings attachment-info',

	prepare: function prepare() {
		_.each(this.collection.map(this.createField, this), function (view) {
			this.set(view.model.id, view);
		}, this);
	},

	createField: function createField(model) {
		var field = new wp.media.view.MenuIconsItemSettingField({
			item: this.model,
			model: model,
			collection: this.collection
		});

		return field;
	}
});

module.exports = MenuIconsItemSettings;

/***/ }),
/* 10 */
/***/ (function(module, exports) {

/**
 * wp.media.view.MenuIconsSidebar
 *
 * @class
 * @augments wp.media.view.IconPickerSidebar
 * @augments wp.media.view.Sidebar
 * @augments wp.media.view.PriorityList
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MenuIconsSidebar = wp.media.view.IconPickerSidebar.extend({
	initialize: function initialize() {
		var title = new wp.media.View({
			tagName: 'h3',
			priority: -10
		});

		var info = new wp.media.View({
			tagName: 'p',
			className: '_info',
			priority: 1000
		});

		wp.media.view.IconPickerSidebar.prototype.initialize.apply(this, arguments);

		title.$el.text(window.menuIcons.text.preview);
		this.set('title', title);
	},

	createSingle: function createSingle() {
		this.createPreview();
		this.createSettings();
	},

	disposeSingle: function disposeSingle() {
		this.unset('preview');
		this.unset('settings');
	},

	createPreview: function createPreview() {
		var self = this,
		    frame = self.controller,
		    state = frame.state();

		// If the selected icon is still being downloaded (image or svg type),
		// wait for it to complete before creating the preview.
		if (state.dfd && state.dfd.state() === 'pending') {
			state.dfd.done(function () {
				self.createPreview();
			});

			return;
		}

		self.set('preview', new wp.media.view.MenuIconsItemPreview({
			controller: frame,
			model: frame.target,
			priority: 80
		}));
	},

	createSettings: function createSettings() {
		var frame = this.controller,
		    state = frame.state(),
		    fieldIds = state.get('data').settingsFields,
		    fields = [];

		_.each(fieldIds, function (fieldId) {
			var field = window.menuIcons.settingsFields[fieldId],
			    model;

			if (!field) {
				return;
			}

			model = _.defaults({
				value: frame.target.get(fieldId) || field['default']
			}, field);

			fields.push(model);
		});

		if (!fields.length) {
			return;
		}

		this.set('settings', new wp.media.view.MenuIconsItemSettings({
			controller: this.controller,
			collection: new wp.media.model.MenuIconsItemSettings(fields),
			model: frame.target,
			type: this.options.type,
			priority: 120
		}));
	}
});

module.exports = MenuIconsSidebar;

/***/ }),
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
__webpack_require__(0);

/***/ })
/******/ ]);