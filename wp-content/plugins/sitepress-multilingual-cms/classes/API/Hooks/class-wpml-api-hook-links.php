<?php

/**
 * Class WPML_API_Hook_Links
 *
 * This class provides various links by hooks
 */
class WPML_API_Hook_Links implements IWPML_Action {

	const POST_TRANSLATION_SETTINGS_PRIORITY = 10;
	const LINK_TO_TRANSLATION_PRIORITY = 9;

	/** @var WPML_Post_Status_Display_Factory */
	private $post_status_display_factory;

	public function __construct(
		WPML_Post_Status_Display_Factory $post_status_display_factory
	) {
		$this->post_status_display_factory = $post_status_display_factory;
	}

	public function add_hooks() {
		add_filter( 'wpml_get_post_translation_settings_link', array(
			$this,
			'get_post_translation_settings_link'
		), self::POST_TRANSLATION_SETTINGS_PRIORITY, 1 );

		add_filter( 'wpml_get_link_to_edit_translation', array(
			$this,
			'get_link_to_edit_translation'
		), self::LINK_TO_TRANSLATION_PRIORITY, 3 );
	}

	public function get_post_translation_settings_link( $link ) {
		return admin_url( 'admin.php?page=' . WPML_PLUGIN_FOLDER . '/menu/translation-options.php#icl_custom_posts_sync_options' );
	}

	public function get_link_to_edit_translation( $link, $post_id, $lang ) {
		$status_display = $this->post_status_display_factory->create();
		$status_data    = $status_display->get_status_data( $post_id, $lang );

		$status_link = $status_data[2];
		$trid        = $status_data[3];
		$css_class   = $status_data[4];

		return apply_filters( 'wpml_link_to_translation', $status_link, $post_id, $lang, $trid, $css_class );
	}

}