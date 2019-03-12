<?php

class OTGS_Installer_WP_Components_Setting_Ajax {

	const AJAX_ACTION           = 'otgs_save_setting_share_local_components';
	const SAVE_SETTING_PRIORITY = 1;

	/**
	 * @var OTGS_Installer_WP_Share_Local_Components_Setting
	 */
	private $setting;

	/**
	 * @var WP_Installer
	 */
	private $installer;

	public function __construct( OTGS_Installer_WP_Share_Local_Components_Setting $setting, WP_Installer $installer ) {
		$this->setting   = $setting;
		$this->installer = $installer;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'save' ), self::SAVE_SETTING_PRIORITY );
	}

	public function save() {
		if ( $this->is_valid_request() ) {
			$user_agree   = (int) filter_var( $_POST['agree'], FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$repo_request = filter_var( $_POST['repo'], FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			if ( $repo_request ) {
				$repos = array();
				foreach ( $this->installer->get_repositories() as $repo_id => $repository ) {
					if ( $repo_id === $repo_request ) {
						$repos[ $repo_id ] = $user_agree;
					}
				}
				$this->setting->save( $repos );
			}
		}
	}

	/**
	 * @return bool
	 */
	private function is_valid_request() {
		return isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], self::AJAX_ACTION );
	}
}
