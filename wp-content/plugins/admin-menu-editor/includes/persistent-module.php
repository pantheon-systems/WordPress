<?php

abstract class amePersistentModule extends ameModule {
	/**
	 * @var string Database option where module settings are stored.
	 */
	protected $optionName = '';

	/**
	 * @var array|null Module settings. NULL when settings haven't been loaded yet.
	 */
	protected $settings = null;

	/**
	 * @var array Default module settings.
	 */
	protected $defaultSettings = array();

	public function __construct($menuEditor) {
		if ( $this->optionName === '' ) {
			throw new LogicException(__CLASS__ . '::$optionName is an empty string. You must set it to a valid option name.');
		}

		parent::__construct($menuEditor);
	}

	public function loadSettings() {
		if ( isset($this->settings) ) {
			return $this->settings;
		}

		$json = $this->getScopedOption($this->optionName, null);
		if ( is_string($json) && !empty($json) ) {
			$settings = json_decode($json, true);
		} else {
			$settings = array();
		}

		$this->settings = array_merge($this->defaultSettings, $settings);

		return $this->settings;
	}

	public function saveSettings() {
		$settings = json_encode($this->settings);
		//Save per site or site-wide based on plugin configuration.
		$this->setScopedOption($this->optionName, $settings);
	}

	public function mergeSettingsWith($newSettings) {
		$this->settings = array_merge($this->loadSettings(), $newSettings);
		return $this->settings;
	}

	protected function getTemplateVariables($templateName) {
		$variables = parent::getTemplateVariables($templateName);
		if ( $templateName === $this->moduleId ) {
			$variables = array_merge(
				$variables,
				array(
					'settings' => $this->loadSettings(),
				)
			);
		}
		return $variables;
	}
}