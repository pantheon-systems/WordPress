<?php

/*
 * Idea: Show tweaks as options in menu properties, e.g. in a "Tweaks" section styled like the collapsible
 * property sheets in Delphi.
 */

class ameTweakManager extends amePersistentModule {
	protected $tabSlug = 'tweaks';
	protected $tabTitle = 'Tweaks';
	protected $optionName = 'ws_ame_tweak_settings';

	private $tweaks = array();

	private $postponedTweaks = array();
	private $pendingSelectorTweaks = array();

	public function __construct($menuEditor) {
		parent::__construct($menuEditor);

		add_action('init', array($this, 'processTweaks'), 200);
		add_action('admin_head', array($this, 'outputSelectors'));
	}

	private function registerTweaks() {
		$this->tweaks = require (__DIR__ . '/default-tweaks.php');
		do_action('admin-menu-editor-register_tweaks', $this);
	}

	public function processTweaks() {
		$settings = $this->loadSettings();
		$isTweakEnabled = ameUtils::get($settings, 'isTweakEnabled');

		$this->registerTweaks();

		$currentUser = wp_get_current_user();
		$roles = $this->menuEditor->get_user_roles($currentUser);
		$isSuperAdmin = is_multisite() && is_super_admin($currentUser->ID);

		foreach ($this->tweaks as $id => $tweak) {
			if ( empty($isTweakEnabled[$id]) ) {
				continue; //This tweak is not enabled for anyone.
			}

			if ( !$this->appliesToUser($isTweakEnabled[$id], $currentUser, $roles, $isSuperAdmin) ) {
				continue;
			}

			if ( isset($tweak['initFilter']) && !call_user_func($tweak['initFilter']) ) {
				continue;
			}

			if ( !empty($tweak['screens']) || !empty($tweak['screenFilter']) ) {
				$this->postponedTweaks[$id] = $tweak;
				continue;
			}

			$this->applyTweak($id, $tweak);
		}

		if ( !empty($this->postponedTweaks) ) {
			add_action('current_screen', array($this, 'processPostponedTweaks'), 10, 1);
		}
	}

	/**
	 * @param array $enabledForActor
	 * @param WP_User $user
	 * @param array $roles
	 * @param bool $isSuperAdmin
	 * @return bool
	 */
	private function appliesToUser($enabledForActor, $user, $roles, $isSuperAdmin = false) {
		//User-specific settings have priority over everything else.
		$userActor = 'user:' . $user->user_login;
		if ( isset($enabledForActor[$userActor]) ) {
			return $enabledForActor[$userActor];
		}

		//The "Super Admin" flag has priority over regular roles.
		if ( $isSuperAdmin && isset($enabledForActor['special:super_admin']) ) {
			return $enabledForActor['special:super_admin'];
		}

		//If it's enabled for any role, it's enabled for the user.
		foreach($roles as $role) {
			if ( !empty($enabledForActor['role:' . $role]) ) {
				return true;
			}
		}

		//By default, all tweaks are disabled.
		return false;
	}

	private function applyTweak($id, $tweak) {
		//Run callbacks immediately.
		if ( isset($tweak['callback']) ) {
			call_user_func($tweak['callback']);
		}

		//Queue selectors for later.
		if ( !empty($tweak['selector']) ) {
			$this->pendingSelectorTweaks[$id] = $tweak;
		}
	}

	/**
	 * @param WP_Screen $screen
	 */
	public function processPostponedTweaks($screen = null) {
		if ( empty($screen) && function_exists('get_current_screen') ) {
			$screen = get_current_screen();
		}
		$screenId = isset($screen, $screen->id) ? $screen->id : null;

		foreach($this->postponedTweaks as $id => $tweak) {
			if ( !empty($tweak['screens']) && !in_array($screenId, $tweak['screens']) ) {
				continue;
			}

			if ( !empty($tweak['screenFilter']) && !call_user_func($tweak['screenFilter'], $screen) ) {
				continue;
			}

			$this->applyTweak($id, $tweak);
		}

		$this->postponedTweaks = array();
	}

	public function outputSelectors() {
		if ( empty($this->pendingSelectorTweaks) ) {
			return;
		}

		$selectors = array();
		foreach($this->pendingSelectorTweaks as $tweak) {
			$selectors[] = $tweak['selector'];
		}
		$css = sprintf(
			'<style type="text/css">%s { display: none; }</style>',
			implode(',', $selectors)
		);

		echo '<!-- AME selector tweaks -->', "\n", $css, "\n";

		$this->pendingSelectorTweaks = array();
	}

	protected function getTemplateVariables($templateName) {
		$variables = parent::getTemplateVariables($templateName);
		$variables['tweaks'] = $this->tweaks;
		return $variables;
	}


}