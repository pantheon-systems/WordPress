<?php

class WPML_TM_Upgrade_Loader implements IWPML_Action {

	/** @var SitePress */
	private $sitepress;

	/** @var wpdb */
	private $upgrade_schema;

	/** @var WPML_Settings_Helper  */
	private $settings;

	/** @var WPML_Upgrade_Command_Factory */
	private $factory;

	/** @var WPML_Notices  */
	private $notices;

	public function __construct(
		SitePress $sitepress,
		WPML_Upgrade_Schema $upgrade_schema,
		WPML_Settings_Helper $settings,
		WPML_Notices $wpml_notices,
		WPML_Upgrade_Command_Factory $factory
	) {
		$this->sitepress      = $sitepress;
		$this->upgrade_schema = $upgrade_schema;
		$this->settings       = $settings;
		$this->notices        = $wpml_notices;
		$this->factory        = $factory;
	}

	public function add_hooks() {
		add_action( 'init', array( $this, 'wpml_tm_upgrade' ) );
	}

	public function wpml_tm_upgrade() {

		$commands = array(
			$this->factory->create_command_definition( 'WPML_TM_Upgrade_Translation_Priorities_For_Posts', array(), array( 'admin', 'ajax', 'front-end' ) ),
		);

		$upgrade = new WPML_Upgrade( $commands, $this->sitepress, $this->factory );
		$upgrade->run();
	}
}