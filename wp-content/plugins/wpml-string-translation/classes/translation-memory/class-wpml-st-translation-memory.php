<?php

class WPML_ST_Translation_Memory implements IWPML_Action {

	const NONCE         = 'wpml_translation_memory_nonce';
	const SCRIPT_HANDLE = 'wpml-st-translation-memory';

	/** @var WPML_ST_Translation_Memory_Records $records */
	private $records;

	public function __construct( WPML_ST_Translation_Memory_Records $records ) {
		$this->records = $records;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_wpml_st_fetch_translations', array( $this, 'fetch_translations' ) );
		add_filter( 'wpml_st_translation_memory_nonce', array( $this, 'get_nonce' ) );
		add_filter( 'wpml_st_get_translation_memory', array( $this, 'get_translation_memory' ), 10, 4 );

		if ( isset( $_GET['page'] ) && WPML_ST_FOLDER . '/menu/string-translation.php' === $_GET['page'] ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_script' ) );
		}
	}

	public function fetch_translations() {
		$results = array();

		if (
			wp_verify_nonce( $_POST['nonce'], self::NONCE ) &&
			isset( $_POST['strings'], $_POST['languages']['source'], $_POST['languages']['target'] )
		) {
			$args = array(
				'strings'     => $_POST['strings'],
				'source_lang' => $_POST['languages']['source'],
				'target_lang' => $_POST['languages']['target'],
			);

			$results = $this->records->get( $args );
		}

		wp_send_json_success( $results );
	}

	public function get_nonce( $nonce = null ) {
		return wp_create_nonce( self::NONCE );
	}

	/**
	 * @param array $empty_array
	 * @param array $args with keys
	 *                  - `strings` an array of strings
	 *                  - `source_lang`
	 *                  - `target_lang`
	 *
	 * @return stdClass[]
	 */
	public function get_translation_memory( $empty_array, $args ) {
		return $this->records->get( $args );
	}

	public function enqueue_script() {
		wp_register_script(
			self::SCRIPT_HANDLE,
			WPML_ST_URL . '/res/js/string-translation-memory.js',
			array( 'jquery' ),
			WPML_ST_VERSION
		);

		wp_enqueue_script( self::SCRIPT_HANDLE );
		wp_localize_script( self::SCRIPT_HANDLE, self::NONCE, array( 'value' => $this->get_nonce() ) );
	}
}