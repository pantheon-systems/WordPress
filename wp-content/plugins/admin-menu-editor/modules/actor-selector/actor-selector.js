/// <reference path="../../js/jquery.d.ts" />
/// <reference path="../../js/jquery-json.d.ts" />
/// <reference path="../../js/actor-manager.ts" />
var AmeActorSelector = /** @class */ (function () {
    function AmeActorSelector(actorManager, isProVersion, currentUserLogin, visibleUsers, ajaxParams) {
        var _this = this;
        this.selectedActor = null;
        this.selectedDisplayName = 'All';
        this.visibleUsers = [];
        this.subscribers = [];
        this.isProVersion = false;
        this.cachedVisibleActors = null;
        this.actorManager = actorManager;
        if (typeof currentUserLogin !== 'undefined') {
            this.currentUserLogin = currentUserLogin;
        }
        else {
            this.currentUserLogin = wsAmeActorSelectorData.currentUserLogin;
        }
        if (typeof visibleUsers !== 'undefined') {
            this.visibleUsers = visibleUsers;
        }
        else {
            this.visibleUsers = wsAmeActorSelectorData.visibleUsers;
        }
        if (typeof isProVersion !== 'undefined') {
            this.isProVersion = isProVersion;
        }
        if (ajaxParams) {
            this.ajaxParams = ajaxParams;
        }
        else {
            this.ajaxParams = wsAmeActorSelectorData;
        }
        //Discard any users that don't exist / were not loaded by the actor manager.
        var _ = AmeActorSelector._;
        this.visibleUsers = _.intersection(this.visibleUsers, _.keys(actorManager.getUsers()));
        if (jQuery.isReady) {
            this.initDOM();
        }
        else {
            jQuery(function () {
                _this.initDOM();
            });
        }
    }
    AmeActorSelector.prototype.initDOM = function () {
        var _this = this;
        this.selectorNode = jQuery('#ws_actor_selector');
        this.populateActorSelector();
        //Don't show the selector in the free version.
        if (!this.isProVersion) {
            this.selectorNode.hide();
            return;
        }
        //Select an actor on click.
        this.selectorNode.on('click', 'li a.ws_actor_option', function (event) {
            var actor = jQuery(event.target).attr('href').substring(1);
            if (actor === '') {
                actor = null;
            }
            _this.setSelectedActor(actor);
            event.preventDefault();
        });
        //Display the user selection dialog when the user clicks "Choose users".
        this.selectorNode.on('click', '#ws_show_more_users', function (event) {
            event.preventDefault();
            AmeVisibleUserDialog.open({
                currentUserLogin: _this.currentUserLogin,
                users: _this.actorManager.getUsers(),
                visibleUsers: _this.visibleUsers,
                save: function (userDetails, selectedUsers) {
                    _this.actorManager.addUsers(userDetails);
                    _this.visibleUsers = selectedUsers;
                    //The user list has changed, so clear the cache.
                    _this.cachedVisibleActors = null;
                    //Display the new actor list.
                    _this.populateActorSelector();
                    //Save the user list via AJAX.
                    _this.saveVisibleUsers();
                }
            });
        });
    };
    AmeActorSelector.prototype.setSelectedActor = function (actorId) {
        if ((actorId !== null) && !this.actorManager.actorExists(actorId)) {
            return;
        }
        var previousSelection = this.selectedActor;
        this.selectedActor = actorId;
        this.highlightSelectedActor();
        if (actorId !== null) {
            this.selectedDisplayName = this.actorManager.getActor(actorId).displayName;
        }
        else {
            this.selectedDisplayName = 'All';
        }
        //Notify subscribers that the selection has changed.
        if (this.selectedActor !== previousSelection) {
            for (var i = 0; i < this.subscribers.length; i++) {
                this.subscribers[i](this.selectedActor, previousSelection);
            }
        }
    };
    AmeActorSelector.prototype.onChange = function (callback) {
        this.subscribers.push(callback);
    };
    AmeActorSelector.prototype.highlightSelectedActor = function () {
        //Deselect the previous item.
        this.selectorNode.find('.current').removeClass('current');
        //Select the new one or "All".
        var selector;
        if (this.selectedActor === null) {
            selector = 'a.ws_no_actor';
        }
        else {
            selector = 'a[href$="#' + this.selectedActor + '"]';
        }
        this.selectorNode.find(selector).addClass('current');
    };
    AmeActorSelector.prototype.populateActorSelector = function () {
        var actorSelector = this.selectorNode, $ = jQuery;
        var isSelectedActorVisible = false;
        //Build the list of available actors.
        actorSelector.empty();
        actorSelector.append('<li><a href="#" class="current ws_actor_option ws_no_actor" data-text="All">All</a></li>');
        var visibleActors = this.getVisibleActors();
        for (var i = 0; i < visibleActors.length; i++) {
            var actor = visibleActors[i], name_1 = this.getNiceName(actor);
            actorSelector.append($('<li></li>').append($('<a></a>')
                .attr('href', '#' + actor.id)
                .attr('data-text', name_1)
                .text(name_1)
                .addClass('ws_actor_option')));
            isSelectedActorVisible = (actor.id === this.selectedActor) || isSelectedActorVisible;
        }
        if (this.isProVersion) {
            var moreUsersText = 'Choose users\u2026';
            actorSelector.append($('<li>').append($('<a></a>')
                .attr('id', 'ws_show_more_users')
                .attr('href', '#more-users')
                .attr('data-text', moreUsersText)
                .text(moreUsersText)));
        }
        if (this.isProVersion) {
            actorSelector.show();
        }
        //If the selected actor is no longer on the list, select "All" instead.
        if ((this.selectedActor !== null) && !isSelectedActorVisible) {
            this.setSelectedActor(null);
        }
        this.highlightSelectedActor();
    };
    AmeActorSelector.prototype.getVisibleActors = function () {
        var _this = this;
        if (this.cachedVisibleActors) {
            return this.cachedVisibleActors;
        }
        var _ = AmeActorSelector._;
        var actors = [];
        //Include all roles.
        //Idea: Sort roles either alphabetically or by typical privilege level (admin, editor, author, ...).
        _.forEach(this.actorManager.getRoles(), function (role) {
            actors.push(role);
        });
        //Include the Super Admin (multisite only).
        if (this.actorManager.getUser(this.currentUserLogin).isSuperAdmin) {
            actors.push(this.actorManager.getSuperAdmin());
        }
        //Include the current user.
        actors.push(this.actorManager.getUser(this.currentUserLogin));
        //Include other visible users.
        _(this.visibleUsers)
            .without(this.currentUserLogin)
            .sortBy()
            .forEach(function (login) {
            var user = _this.actorManager.getUser(login);
            actors.push(user);
        })
            .value();
        this.cachedVisibleActors = actors;
        return actors;
    };
    AmeActorSelector.prototype.saveVisibleUsers = function () {
        jQuery.post(this.ajaxParams.adminAjaxUrl, {
            'action': this.ajaxParams.ajaxUpdateAction,
            '_ajax_nonce': this.ajaxParams.ajaxUpdateNonce,
            'visible_users': jQuery.toJSON(this.visibleUsers)
        });
    };
    AmeActorSelector.prototype.getCurrentUserActor = function () {
        return this.actorManager.getUser(this.currentUserLogin);
    };
    AmeActorSelector.prototype.getNiceName = function (actor) {
        var name = actor.displayName;
        if (actor instanceof AmeUser) {
            if (actor.userLogin === this.currentUserLogin) {
                name = 'Current user (' + actor.userLogin + ')';
            }
            else {
                name = actor.displayName + ' (' + actor.userLogin + ')';
            }
        }
        return name;
    };
    AmeActorSelector._ = wsAmeLodash;
    return AmeActorSelector;
}());
//# sourceMappingURL=actor-selector.js.map