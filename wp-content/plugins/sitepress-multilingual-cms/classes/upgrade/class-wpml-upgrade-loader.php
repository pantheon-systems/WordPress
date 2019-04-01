<?php

class WPML_Upgrade_Loader implements IWPML_Action {

	const TRANSIENT_UPGRADE_IN_PROGRESS = 'wpml_core_update_in_progress';

	/** @var SitePress */
	private $sitepress;

	/** @var wpdb */
	private $upgrade_schema;

	/** @var WPML_Settings_Helper */
	private $settings;

	/** @var WPML_Upgrade_Command_Factory */
	private $factory;

	/** @var WPML_Notices */
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
		add_action( 'wpml_loaded', array( $this, 'wpml_upgrade' ) );
	}

	public function wpml_upgrade() {
		if ( get_transient( self::TRANSIENT_UPGRADE_IN_PROGRESS ) ) {
			return;
		}

		$commands = array(
			$this->factory->create_command_definition( 'WPML_Upgrade_Localization_Files', array( $this->sitepress ), array( 'admin' ) ),
			$this->factory->create_command_definition( 'WPML_Upgrade_Fix_Non_Admin_With_Admin_Cap', array(), array( 'admin' ) ),
			$this->factory->create_command_definition( 'WPML_Upgrade_Table_Translate_Job_For_3_9_0', array( $this->upgrade_schema ), array( 'admin', 'ajax', 'front-end' ) ),
			$this->factory->create_command_definition( 'WPML_Upgrade_Remove_Translation_Services_Transient', array(), array( 'admin' ) ),
			$this->factory->create_command_definition( 'WPML_Upgrade_Display_Mode_For_Posts', array( $this->sitepress, $this->settings, $this->notices ), array( 'admin', 'ajax' ) ),
			$this->factory->create_command_definition( 'WPML_Add_UUID_Column_To_Translation_Status', array( $this->upgrade_schema ), array( 'admin', 'ajax', 'front-end' ) ),
			$this->factory->create_command_definition( 'WPML_Upgrade_Element_Type_Length_And_Collation', array( $this->upgrade_schema ), array( 'admin', 'ajax', 'front-end' ) ),
			$this->factory->create_command_definition( 'WPML_Upgrade_Add_Word_Count_Column_To_Strings', array( $this->upgrade_schema ), array( 'admin', 'ajax', 'front-end' ) ),
			$this->factory->create_command_definition( 'WPML_Upgrade_Media_Without_Language', array( $this->upgrade_schema->get_wpdb(), $this->sitepress->get_default_language() ), array( 'admin', 'ajax' ) ),
			$this->factory->create_command_definition( 'WPML_Upgrade_Media_Duplication_In_Core', array( $this->sitepress, $this->upgrade_schema, $this->notices ), array( 'admin', 'ajax' ) ),
			$this->factory->create_command_definition( 'WPML_Upgrade_Chinese_Flags', array( 'wpdb' => $this->sitepress->wpdb() ), array( 'admin' ) ),
		);

		$upgrade = new WPML_Upgrade( $commands, $this->sitepress, $this->factory );
		$upgrade->run();
	}
}
