<?php

/**
 * @author OnTheGo Systems
 */
class WPML_XML_Config_Log_UI {
	/** @var IWPML_Template_Service */
	private $template_service;

	function __construct( WPML_Config_Update_Log $log, IWPML_Template_Service $template_service ) {
		$this->log              = $log;
		$this->template_service = $template_service;
	}

	/**
	 * @return string
	 */
	public function show() {
		$model = $this->get_model();

		return $this->template_service->show( $model, 'main.twig' );
	}

	/** @return array */
	private function get_model() {
		$entries = $this->log->get();
		krsort( $entries );

		$table_data = array();
		$columns    = array();
		foreach ( $entries as $entry ) {
			$columns += array_keys( $entry );
		}
		$columns = array_unique( $columns );

		foreach ( $entries as $timestamp => $entry ) {
			$table_row = array( 'timestamp' => $this->getDateTimeFromMicroseconds( $timestamp ) );

			foreach ( $columns as $column ) {
				$table_row[ $column ] = null;
				if ( array_key_exists( $column, $entry ) ) {
					$table_row[ $column ] = $entry[ $column ];
				}
			}
			$table_data[] = $table_row;
		}

		array_unshift( $columns, 'timestamp' );

		$model = array(
			'strings' => array(
				'title'     => __( 'Remote XML Config Log', 'sitepress' ),
				'message'   => __( "WPML needs to load configuration files, which tell it how to translate your theme and the plugins that you use. If there's a problem, use the Retry button. If the problem continues, contact WPML support, show the error details and we'll help you resolve it.", 'sitepress' ),
				'details'   => __( 'Details', 'sitepress' ),
				'empty_log' => __( 'The remote XML Config Log is empty', 'sitepress' ),
			),
			'buttons' => array(
				'retry' => array(
					'label' => __( 'Retry', 'sitepress' ),
					'url'   => null,
					'type'  => 'primary',
				),
				'clear' => array(
					'label' => __( 'Clear log', 'sitepress' ),
					'url'   => null,
					'type'  => 'secondary',
				),
			),
			'columns' => $columns,
			'entries' => $table_data,
		);

		if ( $table_data ) {
			$clear_log_url                    = add_query_arg( array( WPML_XML_Config_Log_Notice::NOTICE_ERROR_GROUP . '-action' => 'wpml_xml_update_clear', WPML_XML_Config_Log_Notice::NOTICE_ERROR_GROUP . '-nonce' => wp_create_nonce( 'wpml_xml_update_clear' ) ), $this->log->get_log_url() );
			$model['buttons']['clear']['url'] = $clear_log_url;

			$retry_url                        = add_query_arg( array( WPML_XML_Config_Log_Notice::NOTICE_ERROR_GROUP . '-action' => 'wpml_xml_update_refresh', WPML_XML_Config_Log_Notice::NOTICE_ERROR_GROUP . '-nonce' => wp_create_nonce( 'wpml_xml_update_refresh' ) ), $this->log->get_log_url() );
			$model['buttons']['retry']['url'] = $retry_url;
		}

		return $model;
	}

	private function getDateTimeFromMicroseconds( $time ) {
		$dFormat = 'Y-m-d H:i:s';

		if ( strpos( $time, '.' ) === false ) {
			return $time;
		}

		list( $sec, $usec ) = explode( '.', $time ); //split the microtime on .

		return date( $dFormat, $sec ) . $usec;
	}
}
