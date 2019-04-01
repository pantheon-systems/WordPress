<?php
/**
 * Class WPML_Media_Set_Initial_Language
 */
class WPML_Media_Set_Initial_Language implements IWPML_Action {
	/**
	 * @var wpdb
	 */
	private $wpdb;
	/**
	 * @var string
	 */
	private $language;

	/**
	 * WPML_Media_Set_Initial_Language constructor.
	 *
	 * @param wpdb $wpdb
	 * @param string $language
	 */
	public function __construct( wpdb $wpdb, $language ) {
		$this->wpdb      = $wpdb;
		$this->language = $language;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_wpml_media_set_initial_language', array( $this, 'set' ) );
	}

	public function set() {

		$this->update_db();

		$message = __( 'Setting language to media: done!', 'wpml-media' );

		wp_send_json_success( array(
			'message' => $message
		) );

	}

	public function update_db(){
		$maxtrid = $this->wpdb->get_var( "SELECT MAX(trid) FROM {$this->wpdb->prefix}icl_translations" );

		$this->wpdb->query(
			$this->wpdb->prepare( "
            INSERT INTO {$this->wpdb->prefix}icl_translations
                (element_type, element_id, trid, language_code, source_language_code)
            SELECT 'post_attachment', ID, %d + ID,  %s, NULL 
			FROM {$this->wpdb->posts} p
                LEFT JOIN {$this->wpdb->prefix}icl_translations t
                ON p.ID = t.element_id AND t.element_type = 'post_attachment' 
                WHERE post_type = 'attachment' AND t.translation_id IS NULL",
				$maxtrid, $this->language
			)
		);
	}
}