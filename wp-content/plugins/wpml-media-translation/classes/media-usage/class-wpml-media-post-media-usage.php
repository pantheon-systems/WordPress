<?php

class WPML_Media_Post_Media_Usage implements IWPML_Action {

	/** @see WPML_Post_Translation::save_post_actions() */
	const PRIORITY_AFTER_CORE_SAVE_POST_ACTIONS = 200;

	/**
	 * @var SitePress
	 */
	private $sitepress;
	/**
	 * @var WPML_Media_Post_With_Media_Files_Factory
	 */
	private $post_with_media_files_factory;
	/**
	 * @var WPML_Media_Usage_Factory
	 */
	private $media_usage_factory;


	public function __construct(
		SitePress $sitepress,
		WPML_Media_Post_With_Media_Files_Factory $post_with_media_files_factory,
		WPML_Media_Usage_Factory $media_usage_factory
	) {
		$this->sitepress                     = $sitepress;
		$this->post_with_media_files_factory = $post_with_media_files_factory;
		$this->media_usage_factory           = $media_usage_factory;
	}

	public function add_hooks() {
		add_action( 'save_post', array( $this, 'update_media_usage' ), self::PRIORITY_AFTER_CORE_SAVE_POST_ACTIONS, 2 );
	}

	/**
	 * @param int $post_id
	 * @param WP_Post|null $post
	 */
	public function update_media_usage( $post_id, $post = null ) {

		if ( null === $post ) {
			$post = get_post( $post_id );
		}

		if ( $this->sitepress->get_wp_api()->constant( 'DOING_AUTOSAVE' )
		     || ! $this->sitepress->is_translated_post_type( $post->post_type )
		     || $post_id !== (int) $this->sitepress->get_original_element_id( $post_id, 'post_' . $post->post_type )
		) {
			return;
		}

		$media_ids = $this->post_with_media_files_factory->create( $post_id )->get_media_ids();
		foreach ( $media_ids as $media_id ) {
			$this->media_usage_factory->create( $media_id )->add_post( $post->ID );
		}

	}


}