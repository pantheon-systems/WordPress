<?php

class WPML_Page_Builders_Media_Shortcodes_Update implements IWPML_PB_Media_Update {

	/** @var WPML_Translation_Element_Factory $element_factory */
	private $element_factory;

	/** @var WPML_Page_Builders_Media_Shortcodes $media_shortcodes*/
	private $media_shortcodes;

	/** @var WPML_Page_Builders_Media_Usage $media_usage */
	private $media_usage;

	public function __construct(
		WPML_Translation_Element_Factory $element_factory,
		WPML_Page_Builders_Media_Shortcodes $media_shortcodes,
		WPML_Page_Builders_Media_Usage $media_usage
	) {
		$this->element_factory  = $element_factory;
		$this->media_shortcodes = $media_shortcodes;
		$this->media_usage      = $media_usage;
	}

	/**
	 * @param WP_Post $post
	 */
	public function translate( $post ) {
		if ( ! $this->media_shortcodes->has_media_shortcode( $post->post_content ) ) {
			return;
		}

		$element = $this->element_factory->create_post( $post->ID );

		if ( ! $element->get_source_language_code() ) {
			return;
		}

		$post->post_content = $this->media_shortcodes->set_target_lang( $element->get_language_code() )
		                                             ->set_source_lang( $element->get_source_language_code() )
		                                             ->translate( $post->post_content );

		$this->media_usage->update( $element->get_source_element()->get_id() );

		// wp_update_post() can modify post tag. Code below sends tags by IDs to prevent this.
		// wpmlcore-5947
		// https://core.trac.wordpress.org/ticket/45121
		$tag_ids = wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) );
		$postarr = array(
			'ID'           => $post->ID,
			'post_content' => $post->post_content,
			'tags_input'   => $tag_ids,
		);
		wp_update_post( $postarr );
	}
}
