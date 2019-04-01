<?php

class WPML_TM_Word_Count_Admin_Hooks implements IWPML_Action {

	/** @var WPML_WP_API $wp_api */
	private $wp_api;

	public function __construct( WPML_WP_API $wp_api ) {
		$this->wp_api = $wp_api;
	}

	public function add_hooks() {
		if ( $this->wp_api->is_dashboard_tab() ) {
			add_action( 'wpml_tm_dashboard_word_count_estimation', array( $this, 'display_dialog_open_link' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		add_filter( 'wpml_words_count_url', array( $this, 'words_count_url_filter' ) );
	}

	public function enqueue_scripts() {
		wp_enqueue_script(
			'word-count-report',
			WPML_TM_URL . '/dist/js/word-count/app.js',
			array( 'jquery-ui-dialog' ),
			WPML_TM_VERSION
		);
	}

	/**
	 * @param string $default_url
	 *
	 * @return string
	 */
	public function words_count_url_filter( $default_url ) {
		return $this->wp_api->get_tm_url( 'dashboard', '#words-count' );
	}

	public function display_dialog_open_link() {
		echo '<a href="#" class="js-word-count-dialog-open"
				 data-nonce="' . wp_create_nonce( WPML_TM_Word_Count_Hooks_Factory::NONCE_ACTION ) . '"
				 data-dialog-title="' . esc_html( 'Word count estimation', 'wpml-translation-management' ) . '"
				 data-cancel="' . esc_html( 'Cancel', 'wpml-translation-management' ) . '"
				 data-loading-text="' . esc_html( 'Initializing...', 'wpml-translation-management' ) . '">'
		     . esc_html__( 'Word count for the entire site', 'wpml-translation-management' ) . '</a>';
	}
}
