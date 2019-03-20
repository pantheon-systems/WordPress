/// <reference path="lodash-3.10.d.ts" />
/// <reference path="common.d.ts" />
var __extends = (this && this.__extends) || (function () {
    var extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var AmeBaseActor = /** @class */ (function () {
    function AmeBaseActor(id, displayName, capabilities, metaCapabilities) {
        if (metaCapabilities === void 0) { metaCapabilities = {}; }
        this.displayName = '[Error: No displayName set]';
        this.groupActors = [];
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
    AmeBaseActor.prototype.hasOwnCap = function (capability) {
        if (this.capabilities.hasOwnProperty(capability)) {
            return this.capabilities[capability];
        }
        if (this.metaCapabilities.hasOwnProperty(capability)) {
            return this.metaCapabilities[capability];
        }
        return null;
    };
    AmeBaseActor.getActorSpecificity = function (actorId) {
        var actorType = actorId.substring(0, actorId.indexOf(':')), specificity = 0;
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
    };
    AmeBaseActor.prototype.toString = function () {
        return this.displayName + ' [' + this.id + ']';
    };
    return AmeBaseActor;
}());
var AmeRole = /** @class */ (function (_super) {
    __extends(AmeRole, _super);
    function AmeRole(roleId, displayName, capabilities, metaCapabilities) {
        if (metaCapabilities === void 0) { metaCapabilities = {}; }
        var _this = _super.call(this, 'role:' + roleId, displayName, capabilities, metaCapabilities) || this;
        _this.name = roleId;
        return _this;
    }
    AmeRole.prototype.hasOwnCap = function (capability) {
        //In WordPress, a role name is also a capability name. Users that have the role "foo" always
        //have the "foo" capability. It's debatable whether the role itself actually has that capability
        //(WP_Role says no), but it's convenient to treat it that way.
        if (capability === this.name) {
            return true;
        }
        return _super.prototype.hasOwnCap.call(this, capability);
    };
    return AmeRole;
}(AmeBaseActor));
var AmeUser = /** @class */ (function (_super) {
    __extends(AmeUser, _super);
    function AmeUser(userLogin, displayName, capabilities, roles, isSuperAdmin, userId, metaCapabilities) {
        if (isSuperAdmin === void 0) { isSuperAdmin = false; }
        if (metaCapabilities === void 0) { metaCapabilities = {}; }
        var _this = _super.call(this, 'user:' + userLogin, displayName, capabilities, metaCapabilities) || this;
        _this.userId = 0;
        _this.isSuperAdmin = false;
        _this.avatarHTML = '';
        _this.userLogin = userLogin;
        _this.roles = roles;
        _this.isSuperAdmin = isSuperAdmin;
        _this.userId = userId || 0;
        if (_this.isSuperAdmin) {
            _this.groupActors.push(AmeSuperAdmin.permanentActorId);
        }
        for (var i = 0; i < _this.roles.length; i++) {
            _this.groupActors.push('role:' + _this.roles[i]);
        }
        return _this;
    }
    AmeUser.createFromProperties = function (properties) {
        var user = new AmeUser(properties.user_login, properties.display_name, properties.capabilities, properties.roles, properties.is_super_admin, properties.hasOwnProperty('id') ? properties.id : null, properties.meta_capabilities);
        if (properties.avatar_html) {
            user.avatarHTML = properties.avatar_html;
        }
        return user;
    };
    return AmeUser;
}(AmeBaseActor));
var AmeSuperAdmin = /** @class */ (function (_super) {
    __extends(AmeSuperAdmin, _super);
    function AmeSuperAdmin() {
        return _super.call(this, AmeSuperAdmin.permanentActorId, 'Super Admin', {}) || this;
    }
    AmeSuperAdmin.prototype.hasOwnCap = function (capability) {
        //The Super Admin has all possible capabilities except the special "do_not_allow" flag.
        return (capability !== 'do_not_allow');
    };
    AmeSuperAdmin.permanentActorId = 'special:super_admin';
    return AmeSuperAdmin;
}(AmeBaseActor));
var AmeActorManager = /** @class */ (function () {
    function AmeActorManager(roles, users, isMultisite, suspectedMetaCaps) {
        if (isMultisite === void 0) { isMultisite = false; }
        if (suspectedMetaCaps === void 0) { suspectedMetaCaps = {}; }
        var _this = this;
        this.roles = {};
        this.users = {};
        this.grantedCapabilities = {};
        this.isMultisite = false;
        this.exclusiveSuperAdminCapabilities = {};
        this.tagMetaCaps = {};
        this.suggestedCapabilities = [];
        this.isMultisite = !!isMultisite;
        AmeActorManager._.forEach(roles, function (roleDetails, id) {
            var role = new AmeRole(id, roleDetails.name, roleDetails.capabilities, AmeActorManager._.get(roleDetails, 'meta_capabilities', {}));
            _this.roles[role.name] = role;
        });
        AmeActorManager._.forEach(users, function (userDetails) {
            var user = AmeUser.createFromProperties(userDetails);
            _this.users[user.userLogin] = user;
        });
        if (this.isMultisite) {
            this.superAdmin = new AmeSuperAdmin();
        }
        this.suspectedMetaCaps = suspectedMetaCaps;
        var exclusiveCaps = [
            'update_core', 'update_plugins', 'delete_plugins', 'install_plugins', 'upload_plugins', 'update_themes',
            'delete_themes', 'install_themes', 'upload_themes', 'update_core', 'edit_css', 'unfiltered_html',
            'edit_files', 'edit_plugins', 'edit_themes', 'delete_user', 'delete_users'
        ];
        for (var i = 0; i < exclusiveCaps.length; i++) {
            this.exclusiveSuperAdminCapabilities[exclusiveCaps[i]] = true;
        }
        var tagMetaCaps = [
            'manage_post_tags', 'edit_categories', 'edit_post_tags', 'delete_categories',
            'delete_post_tags'
        ];
        for (var i = 0; i < tagMetaCaps.length; i++) {
            this.tagMetaCaps[tagMetaCaps[i]] = true;
        }
    }
    AmeActorManager.prototype.actorCanAccess = function (actorId, grantAccess, defaultCapability) {
        if (defaultCapability === void 0) { defaultCapability = null; }
        if (grantAccess.hasOwnProperty(actorId)) {
            return grantAccess[actorId];
        }
        if (defaultCapability !== null) {
            return this.hasCap(actorId, defaultCapability, grantAccess);
        }
        return true;
    };
    AmeActorManager.prototype.getActor = function (actorId) {
        if (actorId === AmeSuperAdmin.permanentActorId) {
            return this.superAdmin;
        }
        var separator = actorId.indexOf(':'), actorType = actorId.substring(0, separator), actorKey = actorId.substring(separator + 1);
        if (actorType === 'role') {
            return this.roles.hasOwnProperty(actorKey) ? this.roles[actorKey] : null;
        }
        else if (actorType === 'user') {
            return this.users.hasOwnProperty(actorKey) ? this.users[actorKey] : null;
        }
        throw {
            name: 'InvalidActorException',
            message: "There is no actor with that ID, or the ID is invalid.",
            value: actorId
        };
    };
    AmeActorManager.prototype.actorExists = function (actorId) {
        try {
            return (this.getActor(actorId) !== null);
        }
        catch (exception) {
            if (exception.hasOwnProperty('name') && (exception.name === 'InvalidActorException')) {
                return false;
            }
            else {
                throw exception;
            }
        }
    };
    AmeActorManager.prototype.hasCap = function (actorId, capability, context) {
        context = context || {};
        return this.actorHasCap(actorId, capability, [context, this.grantedCapabilities]);
    };
    AmeActorManager.prototype.hasCapByDefault = function (actorId, capability) {
        return this.actorHasCap(actorId, capability);
    };
    AmeActorManager.prototype.actorHasCap = function (actorId, capability, contextList) {
        //It's like the chain-of-responsibility pattern.
        //Everybody has the "exist" cap and it can't be removed or overridden by plugins.
        if (capability === 'exist') {
            return true;
        }
        capability = this.mapMetaCap(capability);
        var result = null;
        //Step #1: Check temporary context - unsaved caps, etc. Optional.
        //Step #2: Check granted capabilities. Default on, but can be skipped.
        if (contextList) {
            //Check for explicit settings first.
            var actorValue = void 0, len = contextList.length;
            for (var i = 0; i < len; i++) {
                if (contextList[i].hasOwnProperty(actorId)) {
                    actorValue = contextList[i][actorId];
                    if (typeof actorValue === 'boolean') {
                        //Context: grant_access[actorId] = boolean. Necessary because enabling a menu item for a role
                        //should also enable it for all users who have that role (unless explicitly disabled for a user).
                        return actorValue;
                    }
                    else if (actorValue.hasOwnProperty(capability)) {
                        //Context: grantedCapabilities[actor][capability] = boolean|[boolean, ...]
                        result = actorValue[capability];
                        return (typeof result === 'boolean') ? result : result[0];
                    }
                }
            }
        }
        //Step #3: Check owned/default capabilities. Always checked.
        var actor = this.getActor(actorId);
        if (actor === null) {
            return false;
        }
        var hasOwnCap = actor.hasOwnCap(capability);
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
            for (var index = 0; index < actor.roles.length; index++) {
                var roleHasCap = this.actorHasCap('role:' + actor.roles[index], capability, contextList);
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
    };
    AmeActorManager.prototype.mapMetaCap = function (capability) {
        if (capability === 'customize') {
            return 'edit_theme_options';
        }
        else if (capability === 'delete_site') {
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
    };
    /* -------------------------------
     * Roles
     * ------------------------------- */
    AmeActorManager.prototype.getRoles = function () {
        return this.roles;
    };
    AmeActorManager.prototype.roleExists = function (roleId) {
        return this.roles.hasOwnProperty(roleId);
    };
    ;
    AmeActorManager.prototype.getSuperAdmin = function () {
        return this.superAdmin;
    };
    /* -------------------------------
     * Users
     * ------------------------------- */
    AmeActorManager.prototype.getUsers = function () {
        return this.users;
    };
    AmeActorManager.prototype.getUser = function (login) {
        return this.users.hasOwnProperty(login) ? this.users[login] : null;
    };
    AmeActorManager.prototype.addUsers = function (newUsers) {
        var _this = this;
        AmeActorManager._.forEach(newUsers, function (user) {
            _this.users[user.userLogin] = user;
        });
    };
    AmeActorManager.prototype.getGroupActorsFor = function (userLogin) {
        return this.users[userLogin].groupActors;
    };
    /* -------------------------------
     * Granted capability manipulation
     * ------------------------------- */
    AmeActorManager.prototype.setGrantedCapabilities = function (newGrants) {
        this.grantedCapabilities = AmeActorManager._.cloneDeep(newGrants);
    };
    AmeActorManager.prototype.getGrantedCapabilities = function () {
        return this.grantedCapabilities;
    };
    /**
     * Grant or deny a capability to an actor.
     */
    AmeActorManager.prototype.setCap = function (actor, capability, hasCap, sourceType, sourceName) {
        this.setCapInContext(this.grantedCapabilities, actor, capability, hasCap, sourceType, sourceName);
    };
    AmeActorManager.prototype.setCapInContext = function (context, actor, capability, hasCap, sourceType, sourceName) {
        capability = this.mapMetaCap(capability);
        var grant = sourceType ? [hasCap, sourceType, sourceName || null] : hasCap;
        AmeActorManager._.set(context, [actor, capability], grant);
    };
    AmeActorManager.prototype.resetCapInContext = function (context, actor, capability) {
        capability = this.mapMetaCap(capability);
        if (AmeActorManager._.has(context, [actor, capability])) {
            delete context[actor][capability];
        }
    };
    /**
     * Remove redundant granted capabilities.
     *
     * For example, if user "jane" has been granted the "edit_posts" capability both directly and via the Editor role,
     * the direct grant is redundant. We can remove it. Jane will still have "edit_posts" because she's an editor.
     */
    AmeActorManager.prototype.pruneGrantedUserCapabilities = function () {
        var _this = this;
        var _ = AmeActorManager._, pruned = _.cloneDeep(this.grantedCapabilities), context = [pruned];
        var actorKeys = _(pruned).keys().filter(function (actorId) {
            //Skip users that are not loaded.
            var actor = _this.getActor(actorId);
            if (actor === null) {
                return false;
            }
            return (actor instanceof AmeUser);
        }).value();
        _.forEach(actorKeys, function (actor) {
            _.forEach(_.keys(pruned[actor]), function (capability) {
                var grant = pruned[actor][capability];
                delete pruned[actor][capability];
                var hasCap = _.isArray(grant) ? grant[0] : grant, hasCapWhenPruned = !!_this.actorHasCap(actor, capability, context);
                if (hasCap !== hasCapWhenPruned) {
                    pruned[actor][capability] = grant; //Restore.
                }
            });
        });
        this.setGrantedCapabilities(pruned);
        return pruned;
    };
    ;
    /**
     * Compare the specificity of two actors.
     *
     * Returns 1 if the first actor is more specific than the second, 0 if they're both
     * equally specific, and -1 if the second actor is more specific.
     *
     * @return {Number}
     */
    AmeActorManager.compareActorSpecificity = function (actor1, actor2) {
        var delta = AmeBaseActor.getActorSpecificity(actor1) - AmeBaseActor.getActorSpecificity(actor2);
        if (delta !== 0) {
            delta = (delta > 0) ? 1 : -1;
        }
        return delta;
    };
    ;
    AmeActorManager.prototype.generateCapabilitySuggestions = function (capPower) {
        var _ = AmeActorManager._;
        var capsByPower = _.memoize(function (role) {
            var sortedCaps = _.reduce(role.capabilities, function (result, hasCap, capability) {
                if (hasCap) {
                    result.push({
                        capability: capability,
                        power: _.get(capPower, [capability], 0)
                    });
                }
                return result;
            }, []);
            sortedCaps = _.sortBy(sortedCaps, function (item) { return -item.power; });
            return sortedCaps;
        });
        var rolesByPower = _.values(this.getRoles()).sort(function (a, b) {
            var aCaps = capsByPower(a), bCaps = capsByPower(b);
            //Prioritise roles with the highest number of the most powerful capabilities.
            var i = 0, limit = Math.min(aCaps.length, bCaps.length);
            for (; i < limit; i++) {
                var delta_1 = bCaps[i].power - aCaps[i].power;
                if (delta_1 !== 0) {
                    return delta_1;
                }
            }
            //Give a tie to the role that has more capabilities.
            var delta = bCaps.length - aCaps.length;
            if (delta !== 0) {
                return delta;
            }
            //Failing that, just sort alphabetically.
            if (a.displayName > b.displayName) {
                return 1;
            }
            else if (a.displayName < b.displayName) {
                return -1;
            }
            return 0;
        });
        var preferredCaps = [
            'manage_network_options',
            'install_plugins', 'edit_plugins', 'delete_users',
            'manage_options', 'switch_themes',
            'edit_others_pages', 'edit_others_posts', 'edit_pages',
            'unfiltered_html',
            'publish_posts', 'edit_posts',
            'read'
        ];
        var deprecatedCaps = _(_.range(0, 10)).map(function (level) { return 'level_' + level; }).value();
        deprecatedCaps.push('edit_files');
        var findDiscriminant = function (caps, includeRoles, excludeRoles) {
            var getEnabledCaps = function (role) {
                return _.keys(_.pick(role.capabilities, _.identity));
            };
            //Find caps that all of the includeRoles have and excludeRoles don't.
            var includeCaps = _.intersection.apply(_, _.map(includeRoles, getEnabledCaps)), excludeCaps = _.union.apply(_, _.map(excludeRoles, getEnabledCaps)), possibleCaps = _.without.apply(_, [includeCaps].concat(excludeCaps).concat(deprecatedCaps));
            var bestCaps = _.intersection(preferredCaps, possibleCaps);
            if (bestCaps.length > 0) {
                return bestCaps[0];
            }
            else if (possibleCaps.length > 0) {
                return possibleCaps[0];
            }
            return null;
        };
        var suggestedCapabilities = [];
        for (var i = 0; i < rolesByPower.length; i++) {
            var role = rolesByPower[i];
            var cap = findDiscriminant(preferredCaps, _.slice(rolesByPower, 0, i + 1), _.slice(rolesByPower, i + 1, rolesByPower.length));
            suggestedCapabilities.push({ role: role, capability: cap });
        }
        var previousSuggestion = null;
        for (var i = suggestedCapabilities.length - 1; i >= 0; i--) {
            if (suggestedCapabilities[i].capability === null) {
                suggestedCapabilities[i].capability =
                    previousSuggestion ? previousSuggestion : 'exist';
            }
            else {
                previousSuggestion = suggestedCapabilities[i].capability;
            }
        }
        this.suggestedCapabilities = suggestedCapabilities;
    };
    AmeActorManager.prototype.getSuggestedCapabilities = function () {
        return this.suggestedCapabilities;
    };
    AmeActorManager._ = wsAmeLodash;
    return AmeActorManager;
}());
if (typeof wsAmeActorData !== 'undefined') {
    AmeActors = new AmeActorManager(wsAmeActorData.roles, wsAmeActorData.users, wsAmeActorData.isMultisite, wsAmeActorData.suspectedMetaCaps);
    if (typeof wsAmeActorData['capPower'] !== 'undefined') {
        AmeActors.generateCapabilitySuggestions(wsAmeActorData['capPower']);
    }
}
//# sourceMappingURL=actor-manager.js.map