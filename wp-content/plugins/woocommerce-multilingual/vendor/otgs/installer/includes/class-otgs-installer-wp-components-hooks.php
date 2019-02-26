<?php

class OTGS_Installer_WP_Components_Hooks {

	const EVENT_SEND_COMPONENTS_MONTHLY = 'otgs_send_components_data';
	const EVENT_SEND_COMPONENTS_AFTER_REGISTRATION = 'otgs_send_components_data_on_product_registration';
	const REPORT_SCHEDULING_PERIOD = '+1 month';

	/**
	 * @var OTGS_Installer_WP_Components_Storage
	 */
	private $storage;

	/**
	 * @var OTGS_Installer_WP_Components_Sender
	 */
	private $sender;

	/**
	 * @var OTGS_Installer_WP_Share_Local_Components_Setting
	 */
	private $setting;

	/**
	 * @var OTGS_Installer_PHP_Functions
	 */
	private $php_functions;

	public function __construct(
		OTGS_Installer_WP_Components_Storage $storage,
		OTGS_Installer_WP_Components_Sender $sender,
		OTGS_Installer_WP_Share_Local_Components_Setting $setting,
		OTGS_Installer_PHP_Functions $php_functions
	) {
		$this->storage       = $storage;
		$this->sender        = $sender;
		$this->setting       = $setting;
		$this->php_functions = $php_functions;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_end_user_get_info', array( $this, 'process_report_instantly' ) );
		add_action( 'wp_ajax_' . OTGS_Installer_WP_Components_Setting_Ajax::AJAX_ACTION, array( $this, 'force_send_components_data' ), OTGS_Installer_WP_Components_Setting_Ajax::SAVE_SETTING_PRIORITY + 1 );
		add_action( self::EVENT_SEND_COMPONENTS_MONTHLY, array( $this, 'send_components_data' ) );
		add_action( self::EVENT_SEND_COMPONENTS_AFTER_REGISTRATION, array( $this, 'send_components_data' ) );
		add_action( 'init', array( $this, 'schedule_components_report' ) );
		add_action( 'wp_ajax_save_site_key', array( $this, 'schedule_components_report_when_product_is_registered' ) );
	}

	public function schedule_components_report() {
		if ( ! wp_next_scheduled( self::EVENT_SEND_COMPONENTS_MONTHLY ) ) {
			wp_schedule_single_event( strtotime( self::REPORT_SCHEDULING_PERIOD ), self::EVENT_SEND_COMPONENTS_MONTHLY );
		}
	}

	public function schedule_components_report_when_product_is_registered() {
		if ( ! wp_next_scheduled( self::EVENT_SEND_COMPONENTS_AFTER_REGISTRATION ) ) {
			wp_schedule_single_event( time() + 60, self::EVENT_SEND_COMPONENTS_AFTER_REGISTRATION );
		}
	}

	public function process_report_instantly() {
		$this->storage->refresh_cache();
		$this->sender->send( $this->storage->get(), true );
	}

	public function force_send_components_data() {
		$this->storage->refresh_cache();
		$this->sender->send( $this->storage->get() );
	}

	public function send_components_data() {
		if ( $this->storage->is_outdated() ) {
			$this->storage->refresh_cache();
			$this->sender->send( $this->storage->get() );
		}
	}
}