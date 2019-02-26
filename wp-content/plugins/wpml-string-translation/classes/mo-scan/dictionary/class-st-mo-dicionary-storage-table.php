<?php

class WPML_ST_MO_Dictionary_Storage_Table implements WPML_ST_MO_Dictionary_Storage {
	/** @var wpdb */
	private $wpdb;

	/** @var string */
	private $table;

	/** @var array */
	private $data = array();

	/** @var WPML_ST_MO_File[] */
	private $new_data = array();

	/** @var WPML_ST_MO_File[] */
	private $updated_data = array();

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb  = $wpdb;
		$this->table = $this->wpdb->prefix . 'icl_mo_files_domains';
		$this->load_data();
	}

	public function add_hooks() {
		add_action( 'shutdown', array( $this, 'persist' ), 11, 0 );
	}

	public function save( WPML_ST_MO_File $file ) {
		$is_new                          = ! isset( $this->data[ $file->get_path() ] );
		$this->data[ $file->get_path() ] = $file;

		if ( $is_new ) {
			$this->new_data[] = $file;
		} else {
			$this->updated_data[] = $file;
		}
	}

	/**
	 * We have to postpone saving of real data because target table may not be created yet by migration process
	 */
	public function persist() {
		foreach ( $this->new_data as $file ) {
			$sql = "INSERT IGNORE INTO $this->table ( file_path, file_path_md5, domain, status, num_of_strings, last_modified, component_type, component_id ) VALUES ( %s, %s, %s, %s, %d, %d, %s, %s )";
			$this->wpdb->query(
				$this->wpdb->prepare(
					$sql,
					array(
						$file->get_path(),
						$file->get_path_hash(),
						$file->get_domain(),
						$file->get_status(),
						$file->get_imported_strings_count(),
						$file->get_last_modified(),
						$file->get_component_type(),
						$file->get_component_id(),
					)
				)
			);
		}

		foreach ( $this->updated_data as $file ) {
			$this->wpdb->update(
				$this->table,
				$this->file_to_array( $file ),
				array(
					'file_path_md5' => $file->get_path_hash(),
				),
				array( '%s', '%s', '%d', '%d' )
			);
		}
	}

	/**
	 * @param WPML_ST_MO_File $file
	 * @param array $data
	 *
	 * @return array
	 */
	private function file_to_array( WPML_ST_MO_File $file, array $data = array() ) {
		$data['domain']         = $file->get_domain();
		$data['status']         = $file->get_status();
		$data['num_of_strings'] = $file->get_imported_strings_count();
		$data['last_modified']  = $file->get_last_modified();

		return $data;
	}

	public function find( $path = null, $status = null ) {
		if ( null !== $path ) {
			return isset( $this->data[ $path ] ) ? array( $this->data[ $path ] ) : array();
		}

		if ( null === $status ) {
			return array_values( $this->data );
		}

		if ( ! is_array( $status ) ) {
			$status = array( $status );
		}

		$result = array();
		foreach ( $this->data as $file ) {
			if ( in_array( $file->get_status(), $status, true ) ) {
				$result[] = $file;
			}
		}

		return $result;
	}

	private function load_data() {
		if ( $this->wpdb->get_var( "SHOW TABLES LIKE '$this->table'" ) !== $this->table ) {
			return;
		}

		$sql    = "SELECT * FROM {$this->table}";
		$rowset = $this->wpdb->get_results( $sql );

		foreach ( $rowset as $row ) {
			$file = new WPML_ST_MO_File( $row->file_path, $row->domain, $row->status );
			$file->set_imported_strings_count( $row->num_of_strings );
			$file->set_last_modified( $row->last_modified );
			$file->set_component_type( $row->component_type );
			$file->set_component_id( $row->component_id );

			$this->data[ $file->get_path() ] = $file;
		}
	}
}
