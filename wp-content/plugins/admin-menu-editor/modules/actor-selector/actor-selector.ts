/// <reference path="../../js/jquery.d.ts" />
/// <reference path="../../js/jquery-json.d.ts" />
/// <reference path="../../js/actor-manager.ts" />

declare var wsAmeLodash: _.LoDashStatic;
declare var AmeVisibleUserDialog: any;
//Created using wp_localize_script.
declare var wsAmeActorSelectorData: {
	visibleUsers: string[],
	currentUserLogin: string,

	ajaxUpdateAction: string,
	ajaxUpdateNonce: string,
	adminAjaxUrl: string,
};

interface SelectedActorChangedCallback {
	(newSelectedActor: string, oldSelectedActor: string): void
}
interface SaveVisibleActorAjaxParams {
	ajaxUpdateAction: string,
	ajaxUpdateNonce: string,
	adminAjaxUrl: string,
}

class AmeActorSelector {
	private static _ = wsAmeLodash;

	public selectedActor: string = null;
	public selectedDisplayName: string = 'All';

	private visibleUsers: string[] = [];
	private subscribers: SelectedActorChangedCallback[] = [];
	private readonly actorManager: AmeActorManagerInterface;
	private readonly currentUserLogin: string;
	private readonly isProVersion: boolean = false;
	private ajaxParams: SaveVisibleActorAjaxParams;
	private readonly allOptionEnabled: boolean = true;

	private cachedVisibleActors: IAmeActor[] = null;

	private selectorNode;

	constructor(
		actorManager: AmeActorManagerInterface,
		isProVersion?: boolean,
		allOptionEnabled: boolean = true
	) {
		this.actorManager = actorManager;

		if (typeof isProVersion !== 'undefined') {
			this.isProVersion = isProVersion;
		}
		this.allOptionEnabled = allOptionEnabled;

		this.currentUserLogin = wsAmeActorSelectorData.currentUserLogin;
		this.visibleUsers = wsAmeActorSelectorData.visibleUsers;
		this.ajaxParams = wsAmeActorSelectorData;

		//Discard any users that don't exist / were not loaded by the actor manager.
		const _ = AmeActorSelector._;
		this.visibleUsers = _.intersection(this.visibleUsers, _.keys(actorManager.getUsers()));

		if (jQuery.isReady) {
			this.initDOM();
		} else {
			jQuery(() => {
				this.initDOM();
			});
		}
	}

	private initDOM() {
		this.selectorNode = jQuery('#ws_actor_selector');
		this.populateActorSelector();

		//Don't show the selector in the free version.
		if (!this.isProVersion) {
			this.selectorNode.hide();
			return;
		}

		//Select an actor on click.
		this.selectorNode.on('click', 'li a.ws_actor_option', (event) => {
			let actor = jQuery(event.target).attr('href').substring(1);
			if (actor === '') {
				actor = null;
			}

			this.setSelectedActor(actor);
			event.preventDefault();
		});

		//Display the user selection dialog when the user clicks "Choose users".
		this.selectorNode.on('click', '#ws_show_more_users', (event) => {
			event.preventDefault();
			AmeVisibleUserDialog.open({
				currentUserLogin: this.currentUserLogin,
				users: this.actorManager.getUsers(),
				visibleUsers: this.visibleUsers,
				actorManager: this.actorManager,

				save: (userDetails, selectedUsers) => {
					this.actorManager.addUsers(userDetails);
					this.visibleUsers = selectedUsers;
					//The user list has changed, so clear the cache.
					this.cachedVisibleActors = null;
					//Display the new actor list.
					this.populateActorSelector();

					//Save the user list via AJAX.
					this.saveVisibleUsers();
				}
			});
		});
	}

	setSelectedActor(actorId: string) {
		if ((actorId !== null) && !this.actorManager.actorExists(actorId)) {
			return;
		}

		const previousSelection = this.selectedActor;
		this.selectedActor = actorId;
		this.highlightSelectedActor();

		if (actorId !== null) {
			this.selectedDisplayName = this.actorManager.getActor(actorId).getDisplayName();
		} else {
			this.selectedDisplayName = 'All';
		}

		//Notify subscribers that the selection has changed.
		if (this.selectedActor !== previousSelection) {
			for (let i = 0; i < this.subscribers.length; i++) {
				this.subscribers[i](this.selectedActor, previousSelection);
			}
		}
	}

	onChange(callback: SelectedActorChangedCallback) {
		this.subscribers.push(callback);
	}

	private highlightSelectedActor() {
		//Deselect the previous item.
		this.selectorNode.find('.current').removeClass('current');

		//Select the new one or "All".
		let selector;
		if (this.selectedActor === null) {
			selector = 'a.ws_no_actor';
		} else {
			selector = 'a[href$="#' + this.selectedActor + '"]';
		}
		this.selectorNode.find(selector).addClass('current');
	}

	private populateActorSelector() {
		const actorSelector = this.selectorNode,
			$ = jQuery;
		let isSelectedActorVisible = false;

		//Build the list of available actors.
		actorSelector.empty();
		if (this.allOptionEnabled) {
			actorSelector.append('<li><a href="#" class="current ws_actor_option ws_no_actor" data-text="All">All</a></li>');
		}

		const visibleActors = this.getVisibleActors();
		for (let i = 0; i < visibleActors.length; i++) {
			const actor = visibleActors[i],
				name = this.getNiceName(actor);

			actorSelector.append(
				$('<li></li>').append(
					$('<a></a>')
						.attr('href', '#' + actor.getId())
						.attr('data-text', name)
						.text(name)
						.addClass('ws_actor_option')
				)
			);
			isSelectedActorVisible = (actor.getId() === this.selectedActor) || isSelectedActorVisible;
		}

		if (this.isProVersion) {
			const moreUsersText = 'Choose users\u2026';
			actorSelector.append(
				$('<li>').append(
					$('<a></a>')
						.attr('id', 'ws_show_more_users')
						.attr('href', '#more-users')
						.attr('data-text', moreUsersText)
						.text(moreUsersText)
				)
			);
		}

		if (this.isProVersion) {
			actorSelector.show();
		}

		//If the selected actor is no longer on the list, select the first available option instead.
		if ((this.selectedActor !== null) && !isSelectedActorVisible) {
			if (this.allOptionEnabled) {
				this.setSelectedActor(null);
			} else {
				const availableActors = this.getVisibleActors();
				this.setSelectedActor(AmeActorSelector._.first(availableActors).getId());
			}
		}

		this.highlightSelectedActor();
	}

	repopulate() {
		this.cachedVisibleActors = null;
		this.populateActorSelector();
	}

	getVisibleActors(): IAmeActor[] {
		if (this.cachedVisibleActors) {
			return this.cachedVisibleActors;
		}

		const _ = AmeActorSelector._;
		let actors = [];

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
			.forEach((login) => {
				const user = this.actorManager.getUser(login);
				actors.push(user);
			})
			.value();

		this.cachedVisibleActors = actors;
		return actors;
	}

	private saveVisibleUsers() {
		jQuery.post(
			this.ajaxParams.adminAjaxUrl,
			{
				'action' : this.ajaxParams.ajaxUpdateAction,
				'_ajax_nonce' : this.ajaxParams.ajaxUpdateNonce,
				'visible_users' : jQuery.toJSON(this.visibleUsers)
			}
		);
	}

	getCurrentUserActor(): IAmeUser {
		return this.actorManager.getUser(this.currentUserLogin);
	}

	getNiceName(actor: IAmeActor): string {
		let name = actor.getDisplayName();
		if (actor.hasOwnProperty('userLogin')) {
			const user = actor as IAmeUser;
			if (user.userLogin === this.currentUserLogin) {
				name = 'Current user (' + user.userLogin + ')';
			} else {
				name = user.getDisplayName() + ' (' + user.userLogin + ')';
			}
		}
		return name;
	}
}