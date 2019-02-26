<?php

class WPML_TM_Parent_Filter_Ajax implements IWPML_Action {

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var array $wp_post_types */
	private $wp_post_types;

	public function __construct( SitePress $sitepress, array $wp_post_types ) {
		$this->sitepress     = $sitepress;
		$this->wp_post_types = $wp_post_types;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_icl_tm_parent_filter', array( $this, 'get_parents_dropdown' ) );
	}

	public function get_parents_dropdown() {
		$current_language       = $this->sitepress->get_current_language();
		$request_post_type      = filter_var( $_POST['type'], FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_NULL_ON_FAILURE );
		$request_post_lang      = filter_var( $_POST['from_lang'], FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_NULL_ON_FAILURE );
		$request_post_parent_id = filter_var( $_POST['parent_id'], FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE );

		$this->sitepress->switch_lang( $request_post_lang );

		if ( in_array( $request_post_type, $this->wp_post_types ) ) {
			$html = wp_dropdown_pages(
				array(
					'echo'     => 0,
					'name'     => 'filter[parent_id]',
					'selected' => $request_post_parent_id,
				)
			);
		} else {
			$html = wp_dropdown_categories(
				array(
					'echo'          => 0,
					'orderby'       => 'name',
					'name'          => 'filter[parent_id]',
					'selected'      => $request_post_parent_id,
					'taxonomy'      => $request_post_type,
					'hide_if_empty' => true,
				)
			);
		}

		$this->sitepress->switch_lang( $current_language );

		if ( ! $html ) {
			$html = esc_html__( 'None found', 'wpml-translation-management' );
		}

		wp_send_json_success( array( 'html' => $html ) );
	}
}