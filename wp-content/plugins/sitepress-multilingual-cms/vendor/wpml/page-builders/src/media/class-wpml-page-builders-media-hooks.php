<?php

class WPML_Page_Builders_Media_Hooks implements IWPML_Action {

	/** @var IWPML_PB_Media_Update_Factory $media_update_factory */
	private $media_update_factory;

	/** @var string $page_builder_slug */
	private $page_builder_slug;

	/**
	 * WPML_Page_Builders_Media_Hooks constructor.
	 *
	 * @param IWPML_PB_Media_Update_Factory $media_update_factory
	 * @param string                        $page_builder_slug
	 */
	public function __construct( IWPML_PB_Media_Update_Factory $media_update_factory, $page_builder_slug ) {
		$this->media_update_factory = $media_update_factory;
		$this->page_builder_slug    = $page_builder_slug;
	}
	public function add_hooks() {
		add_filter( 'wmpl_pb_get_media_updaters', array( $this, 'add_media_updater' ) );
		add_filter( 'wpml_media_content_for_media_usage', array( $this, 'add_package_strings_content' ), 10, 2 );
	}
	/**
	 * @param IWPML_PB_Media_Update[] $updaters
	 *
	 * @return IWPML_PB_Media_Update[]
	 */
	public function add_media_updater( $updaters ) {
		if ( ! array_key_exists( $this->page_builder_slug, $updaters ) ) {
			$updaters[ $this->page_builder_slug ] = $this->media_update_factory->create();
		}
		return $updaters;
	}

	/**
	 * @param string  $content
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	public function add_package_strings_content( $content, $post ) {
		$packages = apply_filters( 'wpml_st_get_post_string_packages', array(), $post->ID );

		/** @var WPML_Package[] $packages */
		foreach ( $packages as $package ) {
			$strings = $package->get_package_strings();

			foreach ( $strings as $string ) {
				$content .= PHP_EOL . $string->value;
			}
		}

		return $content;
	}
}
