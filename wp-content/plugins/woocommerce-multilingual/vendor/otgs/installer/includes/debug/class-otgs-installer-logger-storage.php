<?php

class OTGS_Installer_Logger_Storage {

	const MAX_SIZE = 50;

	const OPTION_KEY                    = 'otgs-installer-log';
	const COMPONENT_SUBSCRIPTION        = 'subscription-fetching';
	const COMPONENT_DOWNLOAD            = 'download';
	const COMPONENT_REPOSITORIES        = 'repositories-fetching';
	const API_CONNECTION_TEST           = 'api-connection-test';
	const PRODUCTS_FILE_CONNECTION_TEST = 'products-connection-test';

	private $log_entries;
	private $log_factory;
	private $max_size;

	public function __construct( OTGS_Installer_Log_Factory $log_factory, $max_size = self::MAX_SIZE ) {
		$this->max_size    = $max_size;
		$this->log_factory = $log_factory;
	}

	/**
	 * @return array|OTGS_Installer_Log[]
	 */
	public function get() {
		if ( ! $this->log_entries ) {
			$this->log_entries = get_option( self::OPTION_KEY );
		}

		return $this->convert_to_object( $this->log_entries ? $this->log_entries : array() );
	}

	public function add( OTGS_Installer_Log $log ) {
		$log->set_time( date( 'Y-d-m h:m:s' ) );
		$log_entries = $this->get();
		array_unshift( $log_entries, $log );
		$log_entries = array_slice( $log_entries, 0, $this->max_size );
		$log_entries_arr = $this->convert_to_array( $log_entries );
		update_option( self::OPTION_KEY, $log_entries_arr );
		$this->log_entries = $log_entries_arr;
	}

	/**
	 * @param array $log_entries
	 *
	 * @return array
	 */
	private function convert_to_object( $log_entries ) {
		$log_converted = array();

		foreach ( $log_entries as $log_data ) {
			$log = $this->log_factory->create();
			$log->set_request_args( $log_data['request_args'] )
			    ->set_request_url( $log_data['request_url'] )
			    ->set_response( $log_data['response'] )
			    ->set_time( $log_data['time'] )
			    ->set_component( $log_data['component'] );
			$log_converted[] = $log;
		}

		return $log_converted;
	}

	/**
	 * @param OTGS_Installer_Log[] $log_entries
	 *
	 * @return array
	 */
	private function convert_to_array( $log_entries ) {
		$log_converted = array();

		foreach ( $log_entries as $log_data ) {
			$log_converted[] = array(
				'request_args' => $log_data->get_request_args(),
				'request_url'  => $log_data->get_request_url(),
				'response'     => $log_data->get_response(),
				'component'    => $log_data->get_component(),
				'time'         => $log_data->get_time(),
			);
		}

		return $log_converted;
	}
}