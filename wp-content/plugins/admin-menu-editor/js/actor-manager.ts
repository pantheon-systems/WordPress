/// <reference path="lodash-3.10.d.ts" />
/// <reference path="common.d.ts" />

declare let wsAmeActorData: any;
declare var wsAmeLodash: _.LoDashStatic;
declare let AmeActors: AmeActorManager;

type Falsy = false | null | '' | undefined | 0;
type Truthy = true | string | 1;

interface CapabilityMap {
	[capabilityName: string] : boolean;
}

abstract class AmeBaseActor {
	public id: string;
	public displayName: string = '[Error: No displayName set]';
	public capabilities: CapabilityMap;
	public metaCapabilities: CapabilityMap;

	groupActors: string[] = [];

	constructor(id: string, displayName: string, capabilities: CapabilityMap, metaCapabilities: CapabilityMap = {}) {
		this.id = id;
		this.displayName = displayName;
		this.capabilities = capabilities;
		this.metaCapabilities = metaCapabilities;
	}

	/**
	 * Get the capability setting directly from this actor, ignoring capabilities
	 * granted by roles, the Super Admin flag, or the grantedCapabilities feature.
	 *
	 * Returns NULL for capabilities that are neither explicitly granted nor denied.
	 *
	 * @param {string} capability
	 * @returns {boolean|null}
	 */
	hasOwnCap(capability: string): boolean {
		if (this.capabilities.hasOwnProperty(capability)) {
			return this.capabilities[capability];
		}
		if (this.metaCapabilities.hasOwnProperty(capability)) {
			return this.metaCapabilities[capability];
		}
		return null;
	}

	static getActorSpecificity(actorId: string) {
		let actorType = actorId.substring(0, actorId.indexOf(':')),
			specificity = 0;
		switch (actorType) {
			case 'role':
				specificity = 1;
				break;
			case 'special':
				specificity = 2;
				break;
			case 'user':
				specificity = 10;
				break;
			default:
				specificity = 0;
		}
		return specificity;
	}

	toString(): string {
		return this.displayName + ' [' + this.id + ']';
	}
}

class AmeRole extends AmeBaseActor {
	name: string;

	constructor(roleId: string, displayName: string, capabilities: CapabilityMap, metaCapabilities: CapabilityMap = {}) {
		super('role:' + roleId, displayName, capabilities, metaCapabilities);
		this.name = roleId;
	}

	hasOwnCap(capability: string): boolean {
		//In WordPress, a role name is also a capability name. Users that have the role "foo" always
		//have the "foo" capability. It's debatable whether the role itself actually has that capability
		//(WP_Role says no), but it's convenient to treat it that way.
		if (capability === this.name) {
			return true;
		}
		return super.hasOwnCap(capability);
	}
}

interface AmeUserPropertyMap {
	user_login: string;
	display_name: string;
	capabilities: CapabilityMap;
	meta_capabilities: CapabilityMap;
	roles : string[];
	is_super_admin: boolean;
	id?: number;
	avatar_html?: string;
}

class AmeUser extends AmeBaseActor {
	userLogin: string;
	userId: number = 0;
	roles: string[];
	isSuperAdmin: boolean = false;
	groupActors: string[];
	avatarHTML: string = '';

	constructor(
		userLogin: string,
		displayName: string,
		capabilities: CapabilityMap,
		roles: string[],
		isSuperAdmin: boolean = false,
	    userId?: number,
		metaCapabilities: CapabilityMap = {}
	) {
		super('user:' + userLogin, displayName, capabilities, metaCapabilities);

		this.userLogin = userLogin;
		this.roles = roles;
		this.isSuperAdmin = isSuperAdmin;
		this.userId = userId || 0;

		if (this.isSuperAdmin) {
			this.groupActors.push(AmeSuperAdmin.permanentActorId);
		}
		for (let i = 0; i < this.roles.length; i++) {
			this.groupActors.push('role:' + this.roles[i]);
		}
	}

	static createFromProperties(properties: AmeUserPropertyMap): AmeUser {
		let user = new AmeUser(
			properties.user_login,
			properties.display_name,
			properties.capabilities,
			properties.roles,
			properties.is_super_admin,
			properties.hasOwnProperty('id') ? properties.id : null,
			properties.meta_capabilities
		);

		if (properties.avatar_html) {
			user.avatarHTML = properties.avatar_html;
		}

		return user;
	}
}

class AmeSuperAdmin extends AmeBaseActor {
	static permanentActorId = 'special:super_admin';

	constructor() {
		super(AmeSuperAdmin.permanentActorId, 'Super Admin', {});
	}

	hasOwnCap(capability: string): boolean {
		//The Super Admin has all possible capabilities except the special "do_not_allow" flag.
		return (capability !== 'do_not_allow');
	}
}

interface AmeGrantedCapabilityMap {
	[actorId: string]: {
		[capability: string] : any
	}
}

interface AmeCapabilitySuggestion {
	role: AmeRole;
	capability: string;
}

class AmeActorManager {
	private static _ = wsAmeLodash;

	private roles: {[roleId: string] : AmeRole} = {};
	private users: {[userLogin: string] : AmeUser} = {};
	private grantedCapabilities: AmeGrantedCapabilityMap = {};

	public readonly isMultisite: boolean = false;
	private superAdmin: AmeSuperAdmin;
	private exclusiveSuperAdminCapabilities = {};

	private tagMetaCaps = {};
	private suspectedMetaCaps: CapabilityMap;

	private suggestedCapabilities: AmeCapabilitySuggestion[] = [];

	constructor(roles, users, isMultisite: Truthy | Falsy = false, suspectedMetaCaps: CapabilityMap = {}) {
		this.isMultisite = !!isMultisite;

		AmeActorManager._.forEach(roles, (roleDetails, id) => {
			const role = new AmeRole(
				id,
				roleDetails.name,
				roleDetails.capabilities,
				AmeActorManager._.get(roleDetails, 'meta_capabilities', {})
			);
			this.roles[role.name] = role;
		});

		AmeActorManager._.forEach(users, (userDetails: AmeUserPropertyMap) => {
			const user = AmeUser.createFromProperties(userDetails);
			this.users[user.userLogin] = user;
		});

		if (this.isMultisite) {
			this.superAdmin = new AmeSuperAdmin();
		}

		this.suspectedMetaCaps = suspectedMetaCaps;

		const exclusiveCaps: string[] = [
			'update_core', 'update_plugins', 'delete_plugins', 'install_plugins', 'upload_plugins', 'update_themes',
			'delete_themes', 'install_themes', 'upload_themes', 'update_core', 'edit_css', 'unfiltered_html',
			'edit_files', 'edit_plugins', 'edit_themes', 'delete_user', 'delete_users'
		];
		for (let i = 0; i < exclusiveCaps.length; i++) {
			this.exclusiveSuperAdminCapabilities[exclusiveCaps[i]] = true;
		}

		const tagMetaCaps = [
			'manage_post_tags', 'edit_categories', 'edit_post_tags', 'delete_categories',
			'delete_post_tags'
		];
		for (let i = 0; i < tagMetaCaps.length; i++) {
			this.tagMetaCaps[tagMetaCaps[i]] = true;
		}
	}

	actorCanAccess(
		actorId: string,
		grantAccess: {[actorId: string] : boolean},
		defaultCapability: string = null
	): boolean {
		if (grantAccess.hasOwnProperty(actorId)) {
			return grantAccess[actorId];
		}
		if (defaultCapability !== null) {
			return this.hasCap(actorId, defaultCapability, grantAccess);
		}
		return true;
	}

	getActor(actorId): AmeBaseActor {
		if (actorId === AmeSuperAdmin.permanentActorId) {
			return this.superAdmin;
		}

		const separator = actorId.indexOf(':'),
			actorType = actorId.substring(0, separator),
			actorKey = actorId.substring(separator + 1);

		if (actorType === 'role') {
			return this.roles.hasOwnProperty(actorKey) ? this.roles[actorKey] : null;
		} else if (actorType === 'user') {
			return this.users.hasOwnProperty(actorKey) ? this.users[actorKey] : null;
		}

		throw {
			name: 'InvalidActorException',
			message: "There is no actor with that ID, or the ID is invalid.",
			value: actorId
		};
	}

	actorExists(actorId: string): boolean {
		try {
			return (this.getActor(actorId) !== null);
		} catch (exception) {
			if (exception.hasOwnProperty('name') && (exception.name === 'InvalidActorException')) {
				return false;
			} else {
				throw exception;
			}
		}
	}

	hasCap(actorId: string, capability, context?: {[actor: string] : any}): boolean {
		context = context || {};
		return this.actorHasCap(actorId, capability, [context, this.grantedCapabilities]);
	}

	hasCapByDefault(actorId, capability) {
		return this.actorHasCap(actorId, capability);
	}

	private actorHasCap(actorId: string, capability: string, contextList?: Array<Object>): (boolean | null) {
		//It's like the chain-of-responsibility pattern.

		//Everybody has the "exist" cap and it can't be removed or overridden by plugins.
		if (capability === 'exist') {
			return true;
		}

		capability = this.mapMetaCap(capability);
		let result = null;

		//Step #1: Check temporary context - unsaved caps, etc. Optional.
		//Step #2: Check granted capabilities. Default on, but can be skipped.
		if (contextList) {
			//Check for explicit settings first.
			let actorValue, len = contextList.length;
			for (let i = 0; i < len; i++) {
				if (contextList[i].hasOwnProperty(actorId)) {
					actorValue = contextList[i][actorId];
					if (typeof actorValue === 'boolean') {
						//Context: grant_access[actorId] = boolean. Necessary because enabling a menu item for a role
						//should also enable it for all users who have that role (unless explicitly disabled for a user).
						return actorValue;
					} else if (actorValue.hasOwnProperty(capability)) {
						//Context: grantedCapabilities[actor][capability] = boolean|[boolean, ...]
						result = actorValue[capability];
						return (typeof result === 'boolean') ? result : result[0];
					}
				}
			}
		}

		//Step #3: Check owned/default capabilities. Always checked.
		let actor = this.getActor(actorId);
		if (actor === null) {
			return false;
		}
		let hasOwnCap = actor.hasOwnCap(capability);
		if (hasOwnCap !== null) {
			return hasOwnCap;
		}

		//Step #4: Users can get a capability through their roles or the "super admin" flag.
		//Only users can have inherited capabilities, so if this actor is not a user, we're done.
		if (actor instanceof AmeUser) {
			//Note that Super Admin has priority. If the user is a super admin, their roles are ignored.
			if (actor.isSuperAdmin) {
				return this.actorHasCap('special:super_admin', capability, contextList);
			}

			//Check if any of the user's roles have the capability.
			result = null;
			for (let index = 0; index < actor.roles.length; index++) {
				let roleHasCap = this.actorHasCap('role:' + actor.roles[index], capability, contextList);
				if (roleHasCap !== null) {
					result = result || roleHasCap;
				}
			}
			if (result !== null) {
				return result;
			}
		}

		if (this.suspectedMetaCaps.hasOwnProperty(capability)) {
			return null;
		}
		return false;
	}

	private mapMetaCap(capability: string): string {
		if (capability === 'customize') {
			return 'edit_theme_options';
		} else if (capability === 'delete_site') {
			return 'manage_options';
		}
		//In Multisite, some capabilities are only available to Super Admins.
		if (this.isMultisite && this.exclusiveSuperAdminCapabilities.hasOwnProperty(capability)) {
			return AmeSuperAdmin.permanentActorId;
		}
		if (this.tagMetaCaps.hasOwnProperty(capability)) {
			return 'manage_categories';
		}
		if ((capability === 'assign_categories') || (capability === 'assign_post_tags')) {
			return 'edit_posts';
		}
		return capability;
	}

	/* -------------------------------
	 * Roles
	 * ------------------------------- */

	getRoles() {
		return this.roles;
	}

	roleExists(roleId: string): boolean {
		return this.roles.hasOwnProperty(roleId);
	};

	getSuperAdmin() : AmeSuperAdmin {
		return this.superAdmin;
	}

	/* -------------------------------
	 * Users
	 * ------------------------------- */

	getUsers() {
		return this.users;
	}

	getUser(login: string) {
		return this.users.hasOwnProperty(login) ? this.users[login] : null;
	}

	addUsers(newUsers: AmeUser[]) {
		AmeActorManager._.forEach(newUsers, (user) => {
			this.users[user.userLogin] = user;
		});
	}

	getGroupActorsFor(userLogin: string) {
		return this.users[userLogin].groupActors;
	}

	/* -------------------------------
	 * Granted capability manipulation
	 * ------------------------------- */

	setGrantedCapabilities(newGrants) {
		this.grantedCapabilities = AmeActorManager._.cloneDeep(newGrants);
	}

	getGrantedCapabilities(): AmeGrantedCapabilityMap {
		return this.grantedCapabilities;
	}

	/**
	 * Grant or deny a capability to an actor.
	 */
	setCap(actor: string, capability: string, hasCap: boolean, sourceType?, sourceName?) {
		this.setCapInContext(this.grantedCapabilities, actor, capability, hasCap, sourceType, sourceName);
	}

	public setCapInContext(
		context: AmeGrantedCapabilityMap,
		actor: string,
		capability: string,
		hasCap: boolean,
		sourceType?: string,
		sourceName?: string
	) {
		capability = this.mapMetaCap(capability);

		const grant = sourceType ? [hasCap, sourceType, sourceName || null] : hasCap;
		AmeActorManager._.set(context, [actor, capability], grant);
	}

	public resetCapInContext(context: AmeGrantedCapabilityMap, actor: string, capability: string) {
		capability = this.mapMetaCap(capability);

		if (AmeActorManager._.has(context, [actor, capability])) {
			delete context[actor][capability];
		}
	}

	/**
	 * Remove redundant granted capabilities.
	 *
	 * For example, if user "jane" has been granted the "edit_posts" capability both directly and via the Editor role,
	 * the direct grant is redundant. We can remove it. Jane will still have "edit_posts" because she's an editor.
	 */
	pruneGrantedUserCapabilities(): AmeGrantedCapabilityMap {
		let _ = AmeActorManager._,
			pruned = _.cloneDeep(this.grantedCapabilities),
			context = [pruned];

		let actorKeys = _(pruned).keys().filter((actorId) => {
			//Skip users that are not loaded.
			const actor = this.getActor(actorId);
			if (actor === null) {
				return false;
			}
			return (actor instanceof AmeUser);
		}).value();

		_.forEach(actorKeys, (actor) => {
			_.forEach(_.keys(pruned[actor]), (capability) => {
				const grant = pruned[actor][capability];
				delete pruned[actor][capability];

				const hasCap = _.isArray(grant) ? grant[0] : grant,
					hasCapWhenPruned = !!this.actorHasCap(actor, capability, context);

				if (hasCap !== hasCapWhenPruned) {
					pruned[actor][capability] = grant; //Restore.
				}
			});
		});

		this.setGrantedCapabilities(pruned);
		return pruned;
	};


	/**
	 * Compare the specificity of two actors.
	 *
	 * Returns 1 if the first actor is more specific than the second, 0 if they're both
	 * equally specific, and -1 if the second actor is more specific.
	 *
	 * @return {Number}
	 */
	static compareActorSpecificity(actor1: string, actor2: string): Number {
		let delta = AmeBaseActor.getActorSpecificity(actor1) - AmeBaseActor.getActorSpecificity(actor2);
		if (delta !== 0) {
			delta = (delta > 0) ? 1 : -1;
		}
		return delta;
	};

	generateCapabilitySuggestions(capPower): void {
		let _ = AmeActorManager._;

		let capsByPower = _.memoize((role: AmeRole): {capability: string, power: number}[] => {
			let sortedCaps = _.reduce(role.capabilities, (result, hasCap, capability) => {
				if (hasCap) {
					result.push({
						capability: capability,
						power: _.get(capPower, [capability], 0)
					});
				}
				return result;
			}, []);

			sortedCaps = _.sortBy(sortedCaps, (item) => -item.power);
			return sortedCaps;
		});

		let rolesByPower: AmeRole[] = _.values<AmeRole>(this.getRoles()).sort(function(a: AmeRole, b: AmeRole) {
			let aCaps = capsByPower(a),
				bCaps = capsByPower(b);

			//Prioritise roles with the highest number of the most powerful capabilities.
			let i = 0, limit = Math.min(aCaps.length, bCaps.length);
			for (; i < limit; i++) {
				let delta = bCaps[i].power - aCaps[i].power;
				if (delta !== 0) {
					return delta;
				}
			}

			//Give a tie to the role that has more capabilities.
			let delta = bCaps.length - aCaps.length;
			if (delta !== 0) {
				return delta;
			}

			//Failing that, just sort alphabetically.
			if (a.displayName > b.displayName) {
				return 1;
			} else if (a.displayName < b.displayName) {
				return -1;
			}
			return 0;
		});

		let preferredCaps = [
			'manage_network_options',
			'install_plugins', 'edit_plugins', 'delete_users',
			'manage_options', 'switch_themes',
			'edit_others_pages', 'edit_others_posts', 'edit_pages',
			'unfiltered_html',
			'publish_posts', 'edit_posts',
			'read'
		];

		let deprecatedCaps = _(_.range(0, 10)).map((level) => 'level_' + level).value();
		deprecatedCaps.push('edit_files');

		let findDiscriminant = (caps: string[], includeRoles: AmeRole[], excludeRoles): string => {
			let getEnabledCaps = (role: AmeRole): string[] => {
				return _.keys(_.pick(role.capabilities, _.identity));
			};

			//Find caps that all of the includeRoles have and excludeRoles don't.
			let includeCaps = _.intersection.apply(_, _.map(includeRoles, getEnabledCaps)),
				excludeCaps = _.union.apply(_, _.map(excludeRoles, getEnabledCaps)),
				possibleCaps = _.without.apply(_, [includeCaps].concat(excludeCaps).concat(deprecatedCaps));

			let bestCaps = _.intersection(preferredCaps, possibleCaps);

			if (bestCaps.length > 0) {
				return bestCaps[0];
			} else if (possibleCaps.length > 0) {
				return possibleCaps[0];
			}
			return null;
		};

		let suggestedCapabilities = [];
		for (let i = 0; i < rolesByPower.length; i++) {
			let role = rolesByPower[i];

			let cap = findDiscriminant(
				preferredCaps,
				_.slice(rolesByPower, 0, i + 1),
				_.slice(rolesByPower, i + 1, rolesByPower.length)
			);
			suggestedCapabilities.push({role: role, capability: cap});
		}

		let previousSuggestion = null;
		for (let i = suggestedCapabilities.length - 1; i >= 0; i--) {
			if (suggestedCapabilities[i].capability === null) {
				suggestedCapabilities[i].capability =
					previousSuggestion ? previousSuggestion : 'exist';
			} else {
				previousSuggestion = suggestedCapabilities[i].capability;
			}
		}

		this.suggestedCapabilities = suggestedCapabilities;
	}

	public getSuggestedCapabilities(): AmeCapabilitySuggestion[] {
		return this.suggestedCapabilities;
	}
}

if (typeof wsAmeActorData !== 'undefined') {
	AmeActors = new AmeActorManager(
		wsAmeActorData.roles,
		wsAmeActorData.users,
		wsAmeActorData.isMultisite,
		wsAmeActorData.suspectedMetaCaps
	);

	if (typeof wsAmeActorData['capPower'] !== 'undefined') {
		AmeActors.generateCapabilitySuggestions(wsAmeActorData['capPower']);
	}
}
