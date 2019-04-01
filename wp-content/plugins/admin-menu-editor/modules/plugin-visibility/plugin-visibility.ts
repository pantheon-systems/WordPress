/// <reference path="../../js/knockout.d.ts" />
/// <reference path="../../js/jquery.d.ts" />
/// <reference path="../../js/jqueryui.d.ts" />
/// <reference path="../../js/lodash-3.10.d.ts" />
/// <reference path="../../modules/actor-selector/actor-selector.ts" />
/// <reference path="../../ajax-wrapper/ajax-action-wrapper.d.ts" />

declare let amePluginVisibility: AmePluginVisibilityModule;
declare const wsPluginVisibilityData: PluginVisibilityScriptData;

interface PluginVisibilityScriptData {
	isMultisite: boolean,
	canManagePlugins: {[roleId : string] : boolean},
	selectedActor: string,
	installedPlugins: Array<PvPluginInfo>,
	settings: PluginVisibilitySettings,
	isProVersion: boolean
}

interface PluginVisibilitySettings {
	grantAccessByDefault: GrantAccessMap,
	plugins: {
		[fileName : string] : {
			isVisibleByDefault: boolean,
			grantAccess: GrantAccessMap,
			customName?: string,
			customDescription?: string;
			customAuthor?: string;
			customSiteUrl?: string;
			customVersion?: string;
		}
	}
}

interface GrantAccessMap {
	[actorId : string] : boolean
}

interface PvPluginInfo {
	name: string,
	description: string,
	author: string,
	version: string,
	siteUrl: string,

	fileName: string,
	isActive: boolean;
}

class AmePluginVisibilityModule {
	static _ = wsAmeLodash;

	plugins: Array<AmePlugin>;
	private readonly canRoleManagePlugins: {[roleId: string] : boolean};
	grantAccessByDefault: {[actorId: string] : KnockoutObservable<boolean>};
	private readonly isMultisite: boolean;

	actorSelector: AmeActorSelector;
	selectedActor: KnockoutComputed<string>;
	settingsData: KnockoutObservable<string>;

	areAllPluginsChecked: KnockoutComputed<boolean>;

	/**
	 * Actors that don't lose access to a plugin when you uncheck it in the "All" view.
	 * This is a convenience feature that lets the user quickly hide a bunch of plugins from everyone else.
	 */
	private readonly privilegedActors: Array<IAmeActor>;

	constructor(scriptData: PluginVisibilityScriptData) {
		const _ = AmePluginVisibilityModule._;

		this.actorSelector = new AmeActorSelector(AmeActors, scriptData.isProVersion);

		//Wrap the selected actor in a computed observable so that it can be used with Knockout.
		let _selectedActor = ko.observable(this.actorSelector.selectedActor);
		this.selectedActor = ko.computed<string>({
			read: function () {
				return _selectedActor();
			},
			write: (newActor: string) => {
				this.actorSelector.setSelectedActor(newActor);
			}
		});
		this.actorSelector.onChange((newSelectedActor: string) => {
			_selectedActor(newSelectedActor);
		});

		//Re-select the previously selected actor, or select "All" (null) by default.
		this.selectedActor(scriptData.selectedActor);

		this.canRoleManagePlugins = scriptData.canManagePlugins;
		this.isMultisite = scriptData.isMultisite;

		this.grantAccessByDefault = {};
		_.forEach(this.actorSelector.getVisibleActors(), (actor: AmeBaseActor) => {
			this.grantAccessByDefault[actor.id] = ko.observable<boolean>(
				_.get(scriptData.settings.grantAccessByDefault, actor.id, this.canManagePlugins(actor))
			);
		});

		this.plugins = _.map(scriptData.installedPlugins, (plugin) => {
			return new AmePlugin(plugin, _.get(scriptData.settings.plugins, plugin.fileName, {}), this);
		});
		//Normally, the plugin list is sorted by the (real) plugin name. Re-sort taking custom names into account.
		this.plugins.sort(function(a, b) {
			return a.name().localeCompare(b.name());
		});

		this.privilegedActors = [this.actorSelector.getCurrentUserActor()];
		if (this.isMultisite) {
			this.privilegedActors.push(AmeActors.getSuperAdmin());
		}

		this.areAllPluginsChecked = ko.computed({
			read: () => {
				return _.every(this.plugins, (plugin) => {
					return this.isPluginVisible(plugin);
				});
			},
			write: (isChecked) => {
				if (this.selectedActor() !== null) {
					let canSeePluginsByDefault = this.getGrantAccessByDefault(this.selectedActor());
					canSeePluginsByDefault(isChecked);
				}
				_.forEach(this.plugins, (plugin) => {
					this.setPluginVisibility(plugin, isChecked);
				});
			}
 		});

		//This observable will be populated when saving changes.
		this.settingsData = ko.observable('');
	}

	isPluginVisible(plugin: AmePlugin): boolean {
		let actorId = this.selectedActor();
		if (actorId === null) {
			return plugin.isVisibleByDefault();
		} else {
			let canSeePluginsByDefault = this.getGrantAccessByDefault(actorId),
				isVisible = plugin.getGrantObservable(actorId, plugin.isVisibleByDefault() && canSeePluginsByDefault());
			return isVisible();
		}
	}

	setPluginVisibility(plugin: AmePlugin, isVisible: boolean) {
		const selectedActor = this.selectedActor();
		if (selectedActor === null) {
			plugin.isVisibleByDefault(isVisible);

			//Show/hide from everyone except the current user and Super Admin.
			//However, don't enable plugins for roles that can't access the "Plugins" page in the first place.
			const _ = AmePluginVisibilityModule._;
			_.forEach(this.actorSelector.getVisibleActors(), (actor: AmeBaseActor) => {
				let allowAccess = plugin.getGrantObservable(actor.id, isVisible);
				if (!this.canManagePlugins(actor)) {
					allowAccess(false);
				} else if (_.includes(this.privilegedActors, actor)) {
					allowAccess(true);
				} else {
					allowAccess(isVisible);
				}
			});
		} else {
			//Show/hide from the selected role or user.
			let allowAccess = plugin.getGrantObservable(selectedActor, isVisible);
			allowAccess(isVisible);
		}
	}

	private canManagePlugins(actor: AmeBaseActor) {
		const _ = AmePluginVisibilityModule._;
		if ((actor instanceof AmeRole) && _.has(this.canRoleManagePlugins, actor.name)) {
			return this.canRoleManagePlugins[actor.name];
		}
		if (actor instanceof AmeSuperAdmin) {
			return true;
		}

		if (actor instanceof AmeUser) {
			//Can any of the user's roles manage plugins?
			let result = false;
			_.forEach(actor.roles, (roleId) => {
				if (_.get(this.canRoleManagePlugins, roleId, false)) {
					result = true;
					return false;
				}
			});
			return (result || AmeActors.hasCap(actor.id, 'activate_plugins'));
		}

		return false;
	}

	private getGrantAccessByDefault(actorId: string): KnockoutObservable<boolean> {
		if (!this.grantAccessByDefault.hasOwnProperty(actorId)) {
			this.grantAccessByDefault[actorId] = ko.observable(this.canManagePlugins(AmeActors.getActor(actorId)));
		}
		return this.grantAccessByDefault[actorId];
	}

	private getSettings(): PluginVisibilitySettings {
		const _ = AmePluginVisibilityModule._;
		let result: PluginVisibilitySettings = <PluginVisibilitySettings>{};

		result.grantAccessByDefault = _.mapValues(this.grantAccessByDefault, (allow): boolean => {
			return allow();
		});
		result.plugins = {};
		_.forEach(this.plugins, (plugin: AmePlugin) => {
			result.plugins[plugin.fileName] = {
				isVisibleByDefault: plugin.isVisibleByDefault(),
				grantAccess: _.mapValues(plugin.grantAccess, (allow): boolean => {
					return allow();
				})
			};

			for (let i = 0; i < AmePlugin.editablePropertyNames.length; i++) {
				let key = AmePlugin.editablePropertyNames[i],
					upperKey = key.substring(0, 1).toUpperCase() + key.substring(1);
				result.plugins[plugin.fileName]['custom' + upperKey] = plugin.customProperties[key]();
			}
		});

		return result;
	}

	//noinspection JSUnusedGlobalSymbols Used in KO template.
	saveChanges() {
		const settings = this.getSettings();

		//Remove settings associated with roles and users that no longer exist or are not visible.
		const _ = AmePluginVisibilityModule._,
			visibleActorIds = _.pluck(this.actorSelector.getVisibleActors(), 'id');
		_.forEach(settings.plugins, (plugin) => {
			plugin.grantAccess = _.pick<GrantAccessMap, GrantAccessMap>(plugin.grantAccess, visibleActorIds);
		});

		//Populate form field(s).
		this.settingsData(jQuery.toJSON(settings));

		return true;
	}
}

interface AmeStringObservableMap {
	[key: string]: KnockoutObservable<string>;
}

class AmePlugin {
	name: KnockoutComputed<string>;
	fileName: string;
	description: KnockoutComputed<string>;
	isActive: boolean;

	static readonly editablePropertyNames = ['name', 'description', 'author', 'siteUrl', 'version'];

	defaultProperties: AmeStringObservableMap = {};
	customProperties: AmeStringObservableMap = {};
	editableProperties: AmeStringObservableMap = {};

	isBeingEdited: KnockoutObservable<boolean>;
	isChecked: KnockoutComputed<boolean>;

	isVisibleByDefault: KnockoutObservable<boolean>;
	grantAccess: {[actorId : string] : KnockoutObservable<boolean>};

	constructor(details: PvPluginInfo, settings: Object, module: AmePluginVisibilityModule) {
		const _ = AmePluginVisibilityModule._;

		for (let i = 0; i < AmePlugin.editablePropertyNames.length; i++) {
			let key = AmePlugin.editablePropertyNames[i],
				upperKey = key.substring(0, 1).toUpperCase() + key.substring(1);
			this.defaultProperties[key] = ko.observable(_.get(details, key, ''));
			this.customProperties[key] = ko.observable(_.get(settings, 'custom' + upperKey, ''));
			this.editableProperties[key] = ko.observable(this.defaultProperties[key]());
		}

		this.name = ko.computed(() => {
			let value = this.customProperties['name']();
			if (value === '') {
				value = this.defaultProperties['name']();
			}
			return AmePlugin.stripAllTags(value);
		});
		this.description = ko.computed(() => {
			let value = this.customProperties['description']();
			if (value === '') {
				value = this.defaultProperties['description']();
			}
			return AmePlugin.stripAllTags(value);
		});

		this.fileName = details.fileName;
		this.isActive = details.isActive;

		this.isBeingEdited = ko.observable(false);

		this.isVisibleByDefault = ko.observable(_.get(settings, 'isVisibleByDefault', true));

		const emptyGrant: { [actorId: string]: boolean } = {};
		this.grantAccess = _.mapValues(_.get(settings, 'grantAccess', emptyGrant), (hasAccess) => {
			return ko.observable<boolean>(hasAccess);
		});

		this.isChecked = ko.computed<boolean>({
			read: () => {
				return module.isPluginVisible(this);
			},
			write: (isVisible: boolean) => {
				return module.setPluginVisibility(this, isVisible);
			}
		});
	}

	getGrantObservable(actorId: string, defaultValue: boolean = true): KnockoutObservable<boolean> {
		if (!this.grantAccess.hasOwnProperty(actorId)) {
			this.grantAccess[actorId] = ko.observable<boolean>(defaultValue);
		}
		return this.grantAccess[actorId];
	}

	//noinspection JSUnusedGlobalSymbols Used in KO template.
	openInlineEditor() {
		for (let i = 0; i < AmePlugin.editablePropertyNames.length; i++) {
			let key = AmePlugin.editablePropertyNames[i],
				customValue = this.customProperties[key]();
			this.editableProperties[key](customValue === '' ? this.defaultProperties[key]() : customValue);
		}

		this.isBeingEdited(true);
	}

	//noinspection JSUnusedGlobalSymbols Used in KO template.
	cancelEdit() {
		this.isBeingEdited(false);
	}

	//noinspection JSUnusedGlobalSymbols Used in KO template.
	confirmEdit() {
		for (let i = 0; i < AmePlugin.editablePropertyNames.length; i++) {
			let key = AmePlugin.editablePropertyNames[i],
				customValue = this.editableProperties[key]();
			if (customValue === this.defaultProperties[key]()) {
				customValue = '';
			}
			this.customProperties[key](customValue);
		}

		this.isBeingEdited(false);
	}

	//noinspection JSUnusedGlobalSymbols Used in KO template.
	resetNameAndDescription() {
		for (let i = 0; i < AmePlugin.editablePropertyNames.length; i++) {
			let key = AmePlugin.editablePropertyNames[i];
			this.customProperties[key]('');
		}
		this.isBeingEdited(false);
	}

	static stripAllTags(input): string {
		//Based on: http://phpjs.org/functions/strip_tags/
		const tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
			commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
		return input.replace(commentsAndPhpTags, '').replace(tags, '');
	}
}

jQuery(function ($) {
	amePluginVisibility = new AmePluginVisibilityModule(wsPluginVisibilityData);
	ko.applyBindings(amePluginVisibility, document.getElementById('ame-plugin-visibility-editor'));

	//Permanently dismiss the usage hint via AJAX.
	$('#ame-pv-usage-notice').on('click', '.notice-dismiss', function() {
		AjawV1.getAction('ws_ame_dismiss_pv_usage_notice').request();
	});
});