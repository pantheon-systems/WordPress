<?php

class WPML_Media_Add_To_Basket implements IWPML_Action {

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * WPML_Media_Add_To_Basket constructor.
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		add_filter(
			'pre_update_option_' . $this->sitepress->get_wp_api()->constant( 'TranslationProxy_Basket::ICL_TRANSLATION_JOBS_BASKET' ),
			array( $this, 'add_media' )
		);
	}

	public function add_media( $data ) {

		if ( ! empty( $data['post'] ) ) {
			foreach ( $data['post'] as $post_id => $post ) {
				if ( $media = $this->get_post_media( $post_id ) ) {
					$data['post'][ $post_id ]['media-translation'] = $media;
				}
			}
		}

		return $data;
	}

	private function get_post_media( $post_id ) {
		return isset( $_POST['post'][ $post_id ]['media-translation'] ) ?
			array_map( 'intval', $_POST['post'][ $post_id ]['media-translation'] ) :
			array();
	}
}