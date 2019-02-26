<?php

/**
 * Class WPML_TF_Translation_Service_Change_Hooks
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Translation_Service_Change_Hooks implements IWPML_Action {

	/** @var WPML_TF_Settings_Read $settings_read */
	private $settings_read;

	/** @var WPML_TF_Settings_Write $settings_write */
	private $settings_write;

	/** @var WPML_TF_TP_Ratings_Synchronize_Factory $tp_ratings_synchronize_factory */
	private $tp_ratings_synchronize_factory;

	public function __construct(
		WPML_TF_Settings_Read $settings_read,
		WPML_TF_Settings_Write $settings_write,
		WPML_TF_TP_Ratings_Synchronize_Factory $tp_ratings_synchronize_factory
	) {
		$this->settings_read                  = $settings_read;
		$this->settings_write                 = $settings_write;
		$this->tp_ratings_synchronize_factory = $tp_ratings_synchronize_factory;
	}

	public function add_hooks() {
		add_action(
			'wpml_tm_before_set_translation_service',
			array( $this, 'before_set_translation_service_callback' )
		);
	}

	public function before_set_translation_service_callback( stdClass $service ) {
		$this->cleanup_pending_ratings_queue();
		$this->disable_tf_if_not_allowed_by_ts( $service );
	}

	private function cleanup_pending_ratings_queue() {
		$tp_ratings_sync = $this->tp_ratings_synchronize_factory->create();
		$tp_ratings_sync->run( true );
	}

	private function disable_tf_if_not_allowed_by_ts( stdClass $service ) {
		if ( isset( $service->translation_feedback ) && ! $service->translation_feedback ) {
			/** @var WPML_TF_Settings $settings */
			$settings = $this->settings_read->get( 'WPML_TF_Settings' );
			$settings->set_enabled( false );
			$this->settings_write->save( $settings );
		}
	}
}
