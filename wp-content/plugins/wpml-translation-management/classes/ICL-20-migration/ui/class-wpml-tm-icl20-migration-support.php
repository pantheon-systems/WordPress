<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Migration_Support {
	const PREFIX           = 'icl20-migration-reset-';
	const TIMESTAMP_FORMAT = 'Y-m-d H:i:s';

	private $can_rollback;
	private $progress;
	private $template_service;

	function __construct(
		IWPML_Template_Service $template_service,
		WPML_TM_ICL20_Migration_Progress $progress,
		$can_rollback = false
	) {
		$this->template_service = $template_service;
		$this->progress         = $progress;
		$this->can_rollback     = $can_rollback;
	}

	function add_hooks() {
		add_action( 'wpml_support_page_after', array( $this, 'show' ) );
	}

	public function parse_request() {
		$nonce_arg  = self::PREFIX . 'nonce';
		$action_arg = self::PREFIX . 'action';

		if ( array_key_exists( $nonce_arg, $_GET ) && array_key_exists( $action_arg, $_GET ) ) {
			$nonce  = filter_var( $_GET[ $nonce_arg ] );
			$action = filter_var( $_GET[ $action_arg ] );

			if ( wp_verify_nonce( $nonce, $action ) ) {
				if ( self::PREFIX . 'reset' === $action ) {
					update_option( WPML_TM_ICL20_Migration_Progress::OPTION_KEY_MIGRATION_LOCKED, false, false );
				}
				if ( self::PREFIX . 'rollback' === $action && $this->can_rollback ) {
					do_action( 'wpml_tm_icl20_migration_rollback' );
				}
			}

			$redirect_to = remove_query_arg( array( $nonce_arg, $action_arg ) );
			wp_safe_redirect( $redirect_to );
		}
	}

	public function show() {
		$model = $this->get_model();

		echo $this->template_service->show( $model, 'main.twig' );
	}

	private function get_model() {

		$reset_target_url = add_query_arg( array(
			                                   self::PREFIX . 'nonce'  => wp_create_nonce( self::PREFIX . 'reset' ),
			                                   self::PREFIX . 'action' => self::PREFIX . 'reset',
		                                   ) );
		$reset_target_url .= '#icl20-migration';

		$model = array(
			'title'   => __( 'Migration to ICL 2.0', 'wpml-translation-management' ),
			'data'    => array(
				__( 'Number of attempts made',
				    'wpml-translation-management' )               => $this->progress->get_current_attempts_count(),
				__( 'Last attempt was on',
				    'wpml-translation-management' )               => date( self::TIMESTAMP_FORMAT,
				                                                           $this->progress->get_last_attempt_timestamp() ),
				__( 'Last error', 'wpml-translation-management' ) => $this->progress->get_last_migration_error(),
			),
			'buttons' => array(
				'reset' => array(
					'type'  => 'primary',
					'label' => __( 'Try again', 'wpml-translation-management' ),
					'url'   => $reset_target_url,
				),
			),
		);

		if ( $this->can_rollback ) {
			$rollback_target_url          = add_query_arg( array(
				                                               self::PREFIX . 'nonce'  => wp_create_nonce( self::PREFIX
				                                                                                           . 'rollback' ),
				                                               self::PREFIX . 'action' => self::PREFIX . 'rollback',
			                                               ) );
			$model['buttons']['rollback'] = array(
				'type'  => 'secondary',
				'label' => __( 'Rollback migration (use at your own risk!)',
				               'wpml-translation-management' ),
				'url'   => $rollback_target_url,
			);
		}

		return $model;
	}
}