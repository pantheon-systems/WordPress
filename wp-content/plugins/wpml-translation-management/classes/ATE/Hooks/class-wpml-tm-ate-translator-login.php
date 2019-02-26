<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ATE_Translator_Login implements IWPML_Action {

	/** @var WPML_TM_AMS_Translator_Activation_Records */
	private $translator_activation_records;

	/** @var WPML_Translator_Records */
	private $translator_records;

	/** @var WPML_TM_AMS_API */
	private $ams_api;

	public function __construct(
		WPML_TM_AMS_Translator_Activation_Records $translator_activation_records,
		WPML_Translator_Records $translator_records,
		WPML_TM_AMS_API $ams_api
	) {
		$this->translator_activation_records = $translator_activation_records;
		$this->translator_records            = $translator_records;
		$this->ams_api                       = $ams_api;
	}

	public function add_hooks() {
		add_action( 'wp_login', array( $this, 'wp_login' ), 10, 2 );
	}

	public function wp_login( $user_login, $user ) {
		if ( $this->translator_records->does_user_have_capability( $user->ID ) ) {
			$result = $this->ams_api->is_subscription_activated( $user->user_email );
			if ( ! is_wp_error( $result ) ) {
				$this->translator_activation_records->set_activated(
					$user->user_email,
					$result
				);
			}
		}
	}

}
