<?php

class ameAdminCss extends ameModule {
	protected $tabSlug = 'admin-css';
	protected $tabTitle = 'CSS';

	public function enqueueTabScripts() {
		parent::enqueueTabScripts();

		$menuConfig = $this->menuEditor->get_active_admin_menu();

		//We really only need a couple of menu properties for this feature, like the titles and URLs.
		$items = array_values(array_map(array($this, 'getRelevantMenuProperties'), $menuConfig['tree']));
	}

	private function getRelevantMenuProperties($menuItem) {
		$properties = array(
			'menu_title' => ameMenuItem::get($menuItem, 'menu_title', '(Untitled Item)'),
			'url'        => ameMenuItem::get($menuItem, 'url'),
		);

		if ( ameMenuItem::get($menuItem, 'separator', false) ) {
			$properties['separator'] = true;
		}

		if ( !empty($menuItem['items']) ) {
			$properties['items'] = array_values(array_map(
				array($this, 'getRelevantMenuProperties'),
				$menuItem['items']
			));
		}

		return $properties;
	}
}