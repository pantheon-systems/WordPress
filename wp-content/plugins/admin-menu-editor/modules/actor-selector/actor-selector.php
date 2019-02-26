<?php
class ameActorSelector extends ameModule {
	const ajaxUpdateAction = 'ws_ame_set_visible_users';

	public function __construct($menuEditor) {
		parent::__construct($menuEditor);

		add_action('wp_ajax_' . self::ajaxUpdateAction, array($this, 'ajaxSetVisibleUsers'));
		add_action('admin_menu_editor-users_to_load', array($this, 'addVisibleUsersToLoginList'));
	}

	public function registerScripts() {
		parent::registerScripts();

		$isProVersion = apply_filters('admin_menu_editor_is_pro', false);
		$dependencies = array('ame-actor-manager', 'ame-lodash', 'jquery');
		if ( $isProVersion || wp_script_is('ame-visible-users', 'registered') ) {
			$dependencies[] = 'ame-visible-users';
		}

		wp_register_auto_versioned_script(
			'ame-actor-selector',
			plugins_url('modules/actor-selector/actor-selector.js', $this->menuEditor->plugin_file),
			$dependencies
		);

		$currentUser = wp_get_current_user();
		wp_localize_script(
			'ame-actor-selector',
			'wsAmeActorSelectorData',
			array(
				'visibleUsers' => $this->menuEditor->get_plugin_option('visible_users'),
				'currentUserLogin' => $currentUser->get('user_login'),
				'isProVersion' => apply_filters('admin_menu_editor_is_pro', false),

				'ajaxUpdateAction' => self::ajaxUpdateAction,
				'ajaxUpdateNonce' => wp_create_nonce(self::ajaxUpdateAction),
				'adminAjaxUrl' => admin_url('admin-ajax.php'),
			)
		);
	}

	public function ajaxSetVisibleUsers() {
		if ( !check_ajax_referer(self::ajaxUpdateAction, false, false) ){
			die(__("Access denied. Invalid nonce.", 'admin-menu-editor'));
		}
		if ( !$this->menuEditor->current_user_can_edit_menu() ) {
			die(__("You don't have permission to use Admin Menu Editor Pro.", 'admin-menu-editor'));
		}

		$post = $this->menuEditor->get_post_params();
		$visibleUsers = json_decode(strval($post['visible_users']));
		$visibleUsers = array_unique(array_map('strval', $visibleUsers));

		$this->menuEditor->set_plugin_option('visible_users', $visibleUsers);
		die('OK');
	}

	public function addVisibleUsersToLoginList($userLogins) {
		$visibleUsers = $this->menuEditor->get_plugin_option('visible_users');
		if ( is_array($visibleUsers) ) {
			$userLogins = array_merge($userLogins, $visibleUsers);
		}
		return $userLogins;
	}
}