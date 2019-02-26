<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_AMS_Synchronize_Actions implements IWPML_Action {

	const ENABLED_FOR_TRANSLATION_VIA_ATE = 'wpml_enabled_for_translation_via_ate';

	/**
	 * @var WPML_TM_AMS_API
	 */
	private $ams_api;
	/**
	 * @var WPML_TM_AMS_Users
	 */
	private $ams_user_records;
	/**
	 * @var WPML_WP_User_Factory $user_factory
	 */
	private $user_factory;

	/**
	 * @var WPML_TM_AMS_Translator_Activation_Records
	 */
	private $translator_activation_records;

	public function __construct(
		WPML_TM_AMS_API $ams_api,
		WPML_TM_AMS_Users $ams_user_records,
		WPML_WP_User_Factory $user_factory,
		WPML_TM_AMS_Translator_Activation_Records $translator_activation_records
	) {
		$this->ams_api                       = $ams_api;
		$this->ams_user_records              = $ams_user_records;
		$this->user_factory                  = $user_factory;
		$this->translator_activation_records = $translator_activation_records;
	}

	public function add_hooks() {
		add_action( 'wpml_tm_ate_synchronize_translators', array( $this, 'synchronize_translators' ) );
		add_action( 'wpml_tm_ate_synchronize_managers', array( $this, 'synchronize_managers' ) );
		add_action( 'wpml_tm_ate_enable_subscription', array( $this, 'enable_subscription' ) );
		add_action( 'deleted_user', array( $this, 'user_changed' ) );
		add_action( 'profile_update', array( $this,'user_changed' ) );
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	public function synchronize_translators() {
		$result = $this->ams_api->synchronize_translators( $this->ams_user_records->get_translators() );
		if( ! is_wp_error( $result ) ) {
			$this->translator_activation_records->update( $result['translators'] );
		}
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	public function synchronize_managers() {
		$this->ams_api->synchronize_managers( $this->ams_user_records->get_managers() );
	}

	public function enable_subscription( $user_id ) {
		$user = $this->user_factory->create( $user_id );
		if ( ! $user->get_meta( self::ENABLED_FOR_TRANSLATION_VIA_ATE ) ) {
			$this->ams_api->enable_subscription( $user->user_email );
			$user->update_meta( self::ENABLED_FOR_TRANSLATION_VIA_ATE, true );
		}
	}

	public function user_changed() {
		$this->synchronize_managers();
		$this->synchronize_translators();
	}

}
