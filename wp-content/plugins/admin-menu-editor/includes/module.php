<?php
abstract class ameModule {
	protected $tabSlug = '';
	protected $tabTitle = '';
	protected $tabOrder = 10;

	protected $moduleId = '';
	protected $moduleDir = '';

	protected $settingsFormAction = '';

	/**
	 * @var WPMenuEditor
	 */
	protected $menuEditor;

	public function __construct($menuEditor) {
		$this->menuEditor = $menuEditor;

		if ( class_exists('ReflectionClass', false) ) {
			//This should never throw an exception since the current class must exist for this constructor to be run.
			/** @noinspection PhpUnhandledExceptionInspection */
			$reflector = new ReflectionClass(get_class($this));
			$this->moduleDir = dirname($reflector->getFileName());
			$this->moduleId = basename($this->moduleDir);
		}

		if ( !$this->isEnabledForRequest() ) {
			return;
		}

		add_action('admin_menu_editor-register_scripts', array($this, 'registerScripts'));

		//Register the module tab.
		if ( ($this->tabSlug !== '') && is_string($this->tabSlug) ) {
			add_action('admin_menu_editor-tabs', array($this, 'addTab'), $this->tabOrder);
			add_action('admin_menu_editor-section-' . $this->tabSlug, array($this, 'displaySettingsPage'));

			add_action('admin_menu_editor-enqueue_scripts-' . $this->tabSlug, array($this, 'enqueueTabScripts'));
			add_action('admin_menu_editor-enqueue_styles-' . $this->tabSlug, array($this, 'enqueueTabStyles'));

			//Optionally, handle settings form submission.
			if ( $this->settingsFormAction !== '' ) {
				add_action('admin_menu_editor-header', array($this, '_processAction'), 10, 2);
			}
		}
	}

	/**
	 * Does this module need to do anything for the current request?
	 *
	 * For example, some modules work in the normal dashboard but not in the network admin.
	 * Other modules don't need to run during AJAX requests or when WP is running Cron jobs.
	 */
	protected function isEnabledForRequest() {
		return true;
	}

	public function addTab($tabs) {
		$tabs[$this->tabSlug] = !empty($this->tabTitle) ? $this->tabTitle : $this->tabSlug;
		return $tabs;
	}

	public function displaySettingsPage() {
		$this->menuEditor->display_settings_page_header();

		if ( !$this->outputMainTemplate() ) {
			printf("[ %1\$s : Module \"%2\$s\" doesn't have a primary template. ]", __METHOD__, $this->moduleId);
		}

		$this->menuEditor->display_settings_page_footer();
	}

	protected function getTabUrl($queryParameters = array()) {
		$queryParameters = array_merge(
			array('sub_section' => $this->tabSlug),
			$queryParameters
		);
		return $this->menuEditor->get_plugin_page_url($queryParameters);
	}

	protected function outputMainTemplate() {
		return $this->outputTemplate($this->moduleId);
	}

	protected function outputTemplate($name) {
		$templateFile = $this->moduleDir . '/' . $name . '-template.php';
		if ( file_exists($templateFile) ) {
			/** @noinspection PhpUnusedLocalVariableInspection Used in some templates. */
			$moduleTabUrl = $this->getTabUrl();

			$templateVariables = $this->getTemplateVariables($name);
			if ( !empty($templateVariables) ) {
				extract($templateVariables, EXTR_SKIP);
			}

			/** @noinspection PhpIncludeInspection */
			require $templateFile;
			return true;
		}
		return false;
	}

	protected function getTemplateVariables(/** @noinspection PhpUnusedParameterInspection */ $templateName) {
		//Override this method to pass variables to a template.
		return array();
	}

	public function registerScripts() {
		//Override this method to register scripts.
	}

	public function enqueueTabScripts() {
		//Override this method to add scripts to the $this->tabSlug tab.
	}

	public function enqueueTabStyles() {
		//Override this method to add stylesheets to the $this->tabSlug tab.
	}

	/**
	 * @access private
	 * @param string $action
	 * @param array $post
	 */
	public function _processAction($action, $post = array()) {
		if ( $action === $this->settingsFormAction ) {
			check_admin_referer($action);
			$this->handleSettingsForm($post);
		}
	}

	public function handleSettingsForm($post = array()) {
		//Override this method to process a form submitted from the module's tab.
	}

	protected function getScopedOption($name, $defaultValue = null) {
		if ( $this->menuEditor->get_plugin_option('menu_config_scope') === 'site' ) {
			return get_option($name, $defaultValue);
		} else {
			return get_site_option($name, $defaultValue);
		}
	}

	protected function setScopedOption($name, $value, $autoload = null) {
		if ( $this->menuEditor->get_plugin_option('menu_config_scope') === 'site' ) {
			update_option($name, $value, $autoload);
		} else {
			WPMenuEditor::atomic_update_site_option($name, $value);
		}
	}

	public function getModuleId() {
		return $this->moduleId;
	}

	public function getTabTitle() {
		return $this->tabTitle;
	}
}