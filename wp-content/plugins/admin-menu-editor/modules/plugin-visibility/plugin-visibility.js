/// <reference path="../../js/knockout.d.ts" />
/// <reference path="../../js/jquery.d.ts" />
/// <reference path="../../js/jqueryui.d.ts" />
/// <reference path="../../js/lodash-3.10.d.ts" />
/// <reference path="../../modules/actor-selector/actor-selector.ts" />
/// <reference path="../../ajax-wrapper/ajax-action-wrapper.d.ts" />
var AmePluginVisibilityModule = /** @class */ (function () {
    function AmePluginVisibilityModule(scriptData) {
        var _this = this;
        var _ = AmePluginVisibilityModule._;
        this.actorSelector = new AmeActorSelector(AmeActors, scriptData.isProVersion);
        //Wrap the selected actor in a computed observable so that it can be used with Knockout.
        var _selectedActor = ko.observable(this.actorSelector.selectedActor);
        this.selectedActor = ko.computed({
            read: function () {
                return _selectedActor();
            },
            write: function (newActor) {
                _this.actorSelector.setSelectedActor(newActor);
            }
        });
        this.actorSelector.onChange(function (newSelectedActor) {
            _selectedActor(newSelectedActor);
        });
        //Re-select the previously selected actor, or select "All" (null) by default.
        this.selectedActor(scriptData.selectedActor);
        this.canRoleManagePlugins = scriptData.canManagePlugins;
        this.isMultisite = scriptData.isMultisite;
        this.grantAccessByDefault = {};
        _.forEach(this.actorSelector.getVisibleActors(), function (actor) {
            _this.grantAccessByDefault[actor.id] = ko.observable(_.get(scriptData.settings.grantAccessByDefault, actor.id, _this.canManagePlugins(actor)));
        });
        this.plugins = _.map(scriptData.installedPlugins, function (plugin) {
            return new AmePlugin(plugin, _.get(scriptData.settings.plugins, plugin.fileName, {}), _this);
        });
        //Normally, the plugin list is sorted by the (real) plugin name. Re-sort taking custom names into account.
        this.plugins.sort(function (a, b) {
            return a.name().localeCompare(b.name());
        });
        this.privilegedActors = [this.actorSelector.getCurrentUserActor()];
        if (this.isMultisite) {
            this.privilegedActors.push(AmeActors.getSuperAdmin());
        }
        this.areAllPluginsChecked = ko.computed({
            read: function () {
                return _.every(_this.plugins, function (plugin) {
                    return _this.isPluginVisible(plugin);
                });
            },
            write: function (isChecked) {
                if (_this.selectedActor() !== null) {
                    var canSeePluginsByDefault = _this.getGrantAccessByDefault(_this.selectedActor());
                    canSeePluginsByDefault(isChecked);
                }
                _.forEach(_this.plugins, function (plugin) {
                    _this.setPluginVisibility(plugin, isChecked);
                });
            }
        });
        //This observable will be populated when saving changes.
        this.settingsData = ko.observable('');
    }
    AmePluginVisibilityModule.prototype.isPluginVisible = function (plugin) {
        var actorId = this.selectedActor();
        if (actorId === null) {
            return plugin.isVisibleByDefault();
        }
        else {
            var canSeePluginsByDefault = this.getGrantAccessByDefault(actorId), isVisible = plugin.getGrantObservable(actorId, plugin.isVisibleByDefault() && canSeePluginsByDefault());
            return isVisible();
        }
    };
    AmePluginVisibilityModule.prototype.setPluginVisibility = function (plugin, isVisible) {
        var _this = this;
        var selectedActor = this.selectedActor();
        if (selectedActor === null) {
            plugin.isVisibleByDefault(isVisible);
            //Show/hide from everyone except the current user and Super Admin.
            //However, don't enable plugins for roles that can't access the "Plugins" page in the first place.
            var _1 = AmePluginVisibilityModule._;
            _1.forEach(this.actorSelector.getVisibleActors(), function (actor) {
                var allowAccess = plugin.getGrantObservable(actor.id, isVisible);
                if (!_this.canManagePlugins(actor)) {
                    allowAccess(false);
                }
                else if (_1.includes(_this.privilegedActors, actor)) {
                    allowAccess(true);
                }
                else {
                    allowAccess(isVisible);
                }
            });
        }
        else {
            //Show/hide from the selected role or user.
            var allowAccess = plugin.getGrantObservable(selectedActor, isVisible);
            allowAccess(isVisible);
        }
    };
    AmePluginVisibilityModule.prototype.canManagePlugins = function (actor) {
        var _this = this;
        var _ = AmePluginVisibilityModule._;
        if ((actor instanceof AmeRole) && _.has(this.canRoleManagePlugins, actor.name)) {
            return this.canRoleManagePlugins[actor.name];
        }
        if (actor instanceof AmeSuperAdmin) {
            return true;
        }
        if (actor instanceof AmeUser) {
            //Can any of the user's roles manage plugins?
            var result_1 = false;
            _.forEach(actor.roles, function (roleId) {
                if (_.get(_this.canRoleManagePlugins, roleId, false)) {
                    result_1 = true;
                    return false;
                }
            });
            return (result_1 || AmeActors.hasCap(actor.id, 'activate_plugins'));
        }
        return false;
    };
    AmePluginVisibilityModule.prototype.getGrantAccessByDefault = function (actorId) {
        if (!this.grantAccessByDefault.hasOwnProperty(actorId)) {
            this.grantAccessByDefault[actorId] = ko.observable(this.canManagePlugins(AmeActors.getActor(actorId)));
        }
        return this.grantAccessByDefault[actorId];
    };
    AmePluginVisibilityModule.prototype.getSettings = function () {
        var _ = AmePluginVisibilityModule._;
        var result = {};
        result.grantAccessByDefault = _.mapValues(this.grantAccessByDefault, function (allow) {
            return allow();
        });
        result.plugins = {};
        _.forEach(this.plugins, function (plugin) {
            result.plugins[plugin.fileName] = {
                isVisibleByDefault: plugin.isVisibleByDefault(),
                grantAccess: _.mapValues(plugin.grantAccess, function (allow) {
                    return allow();
                }),
                customName: plugin.customName(),
                customDescription: plugin.customDescription()
            };
        });
        return result;
    };
    //noinspection JSUnusedGlobalSymbols Used in KO template.
    AmePluginVisibilityModule.prototype.saveChanges = function () {
        var settings = this.getSettings();
        //Remove settings associated with roles and users that no longer exist or are not visible.
        var _ = AmePluginVisibilityModule._, visibleActorIds = _.pluck(this.actorSelector.getVisibleActors(), 'id');
        _.forEach(settings.plugins, function (plugin) {
            plugin.grantAccess = _.pick(plugin.grantAccess, visibleActorIds);
        });
        //Populate form field(s).
        this.settingsData(jQuery.toJSON(settings));
        return true;
    };
    AmePluginVisibilityModule._ = wsAmeLodash;
    return AmePluginVisibilityModule;
}());
var AmePlugin = /** @class */ (function () {
    function AmePlugin(details, settings, module) {
        var _this = this;
        var _ = AmePluginVisibilityModule._;
        this.defaultName = ko.observable(details.name);
        this.defaultDescription = ko.observable(details.description);
        this.customName = ko.observable(_.get(settings, 'customName', ''));
        this.customDescription = ko.observable(_.get(settings, 'customDescription', ''));
        this.name = ko.computed(function () {
            var value = _this.customName();
            if (value === '') {
                value = _this.defaultName();
            }
            return AmePlugin.stripAllTags(value);
        });
        this.description = ko.computed(function () {
            var value = _this.customDescription();
            if (value === '') {
                value = _this.defaultDescription();
            }
            return AmePlugin.stripAllTags(value);
        });
        this.fileName = details.fileName;
        this.isActive = details.isActive;
        this.isBeingEdited = ko.observable(false);
        this.editableName = ko.observable(this.defaultName());
        this.editableDescription = ko.observable(this.defaultDescription());
        this.isVisibleByDefault = ko.observable(_.get(settings, 'isVisibleByDefault', true));
        var emptyGrant = {};
        this.grantAccess = _.mapValues(_.get(settings, 'grantAccess', emptyGrant), function (hasAccess) {
            return ko.observable(hasAccess);
        });
        this.isChecked = ko.computed({
            read: function () {
                return module.isPluginVisible(_this);
            },
            write: function (isVisible) {
                return module.setPluginVisibility(_this, isVisible);
            }
        });
    }
    AmePlugin.prototype.getGrantObservable = function (actorId, defaultValue) {
        if (defaultValue === void 0) { defaultValue = true; }
        if (!this.grantAccess.hasOwnProperty(actorId)) {
            this.grantAccess[actorId] = ko.observable(defaultValue);
        }
        return this.grantAccess[actorId];
    };
    //noinspection JSUnusedGlobalSymbols Used in KO template.
    AmePlugin.prototype.openInlineEditor = function () {
        this.editableName(this.customName() === '' ? this.defaultName() : this.customName());
        this.editableDescription(this.customDescription() === '' ? this.defaultDescription() : this.customDescription());
        this.isBeingEdited(true);
    };
    //noinspection JSUnusedGlobalSymbols Used in KO template.
    AmePlugin.prototype.cancelEdit = function () {
        this.isBeingEdited(false);
    };
    //noinspection JSUnusedGlobalSymbols Used in KO template.
    AmePlugin.prototype.confirmEdit = function () {
        this.customName(this.editableName());
        this.customDescription(this.editableDescription());
        if (this.customName() === this.defaultName()) {
            this.customName('');
        }
        if (this.customDescription() === this.defaultDescription()) {
            this.customDescription('');
        }
        this.isBeingEdited(false);
    };
    //noinspection JSUnusedGlobalSymbols Used in KO template.
    AmePlugin.prototype.resetNameAndDescription = function () {
        this.customName('');
        this.customDescription('');
        this.isBeingEdited(false);
    };
    AmePlugin.stripAllTags = function (input) {
        //Based on: http://phpjs.org/functions/strip_tags/
        var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
        return input.replace(commentsAndPhpTags, '').replace(tags, '');
    };
    return AmePlugin;
}());
jQuery(function ($) {
    amePluginVisibility = new AmePluginVisibilityModule(wsPluginVisibilityData);
    ko.applyBindings(amePluginVisibility, document.getElementById('ame-plugin-visibility-editor'));
    //Permanently dismiss the usage hint via AJAX.
    $('#ame-pv-usage-notice').on('click', '.notice-dismiss', function () {
        AjawV1.getAction('ws_ame_dismiss_pv_usage_notice').request();
    });
});
//# sourceMappingURL=plugin-visibility.js.map