<?php

class WPML_Display_As_Translated_Attachments_Query {

	private $sitepress;
	private $post_translation;

	public function __construct( SitePress $sitepress, WPML_Post_Translation $post_translation ) {
		$this->sitepress        = $sitepress;
		$this->post_translation = $post_translation;
	}

	public function add_hooks() {
		add_filter( 'wpml_post_parse_query', array( $this, 'adjust_post_parent' ) );
	}

	public function adjust_post_parent( $q ) {
		if ( ! empty( $q->query_vars['post_parent'] ) && isset( $q->query['post_type'] ) &&
		     'attachment' === $q->query['post_type'] &&
		     $this->sitepress->is_display_as_translated_post_type( $q->query['post_type'] ) &&
		     $this->sitepress->get_current_language() !== $this->sitepress->get_default_language()
		) {

			$q->query_vars['post_parent__in'] = array(
				$q->query_vars['post_parent'],
				$this->post_translation->get_original_element( $q->query_vars['post_parent'] )
			);

			unset( $q->query_vars['post_parent'] );
		}

		return $q;
	}
}