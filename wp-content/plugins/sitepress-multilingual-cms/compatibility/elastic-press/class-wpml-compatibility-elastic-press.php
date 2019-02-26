<?php

class WPML_Compatibility_ElasticPress {
	/** @var WPML_Compatibility_ElasticPress_Lang */
	private $lang_integration;

	/**
	 * @param WPML_Compatibility_ElasticPress_Lang $lang_integration
	 */
	public function __construct( WPML_Compatibility_ElasticPress_Lang $lang_integration ) {
		$this->lang_integration = $lang_integration;
	}

	public function register_feature() {
		ep_register_feature( 'wpml_integration', array(
			'title' => __( 'Integration WPML with ElasticPress', 'sitepress' ),
			'setup_cb' => array( $this, 'set_up' ),
			'feature_box_summary_cb' => array( $this, 'box_summary' ),
			'feature_box_long_cb' => array( $this, 'box_description_long' ),
			'requires_install_reindex' => false,
		) );
	}

	public function set_up() {
		add_filter( 'ep_post_sync_args', array( $this->lang_integration, 'add_lang_info' ), 10, 2 );
		add_filter( 'ep_search_args', array( $this->lang_integration, 'filter_by_lang' ), 10, 1 );
	}

	public function box_summary() {
		$content = esc_html__( 'Integration WPML with ElasticPress', 'sitepress' );
		echo '<p>' . $content . '</p>';
	}

	public function box_description_long() {
		$content = esc_html__( 'This allows to search for content in a specific language only.', 'sitepress' );
		echo '<p>' . $content . '</p>';
	}
}
