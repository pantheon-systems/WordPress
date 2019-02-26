<?php

class WPML_Media_Selector implements IWPML_Action {

	/**
	 * @var SitePress
	 */
	private $sitepress;
	/**
	 * @var WPML_Twig_Template_Loader
	 */
	private $template_loader;
	/**
	 * @var WPML_Media_Post_With_Media_Files_Factory
	 */
	private $post_with_media_files_factory;
	/**
	 * @var WPML_Media_Post_With_Media_Files_Factory
	 */
	private $translation_element_factory;


	const USER_META_HIDE_POST_MEDIA_SELECTOR = '_wpml_media_hide_post_media_selector';

	public function __construct(
		SitePress $sitepress,
		WPML_Twig_Template_Loader $template_loader,
		WPML_Media_Post_With_Media_Files_Factory $post_with_media_files_factory,
		WPML_Translation_Element_Factory $translation_element_factory
	) {
		$this->sitepress                     = $sitepress;
		$this->template_loader               = $template_loader;
		$this->post_with_media_files_factory = $post_with_media_files_factory;
		$this->translation_element_factory   = $translation_element_factory;
	}

	public function add_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_res' ) );
		add_action( 'wp_ajax_wpml_media_load_image_selector', array( $this, 'load_images_selector' ) );
		add_action( 'wp_ajax_wpml_media_toogle_show_media_selector', array( $this, 'toggle_show_media_selector' ) );

		add_filter( 'wpml_translation_dashboard_row_data', array( $this, 'add_media_data_to_dashboard_row' ), 10, 2 );
		add_action( 'wpml_tm_after_translation_dashboard_documents', array( $this, 'add_media_selector_preloader' ) );
	}

	public function enqueue_res() {
		$current_screen = get_current_screen();

		if ( $current_screen->id === 'wpml_page_' . $this->sitepress->get_wp_api()->constant( 'WPML_TM_FOLDER' ) . '/menu/main' ) {
			$wpml_media_url = $this->sitepress->get_wp_api()->constant( 'WPML_MEDIA_URL' );
			wp_enqueue_script( 'wpml-media-selector', $wpml_media_url . '/res/js/media-selector.js', array( 'jquery' ), false, true );
			wp_enqueue_style( 'wpml-media-selector', $wpml_media_url . '/res/css/media-selector.css', array() );
		}
	}

	public function load_images_selector() {
		$post_id = (int) $_POST['post_id'];
		if ( isset( $_POST['languages'] ) && is_array( $_POST['languages'] ) ) {
			$languages = array_map( 'sanitize_text_field', $_POST['languages'] );
		} else {
			$languages = array();
		}

		$media_files_list  = $this->get_media_files_list( $post_id, $languages );
		$media_files_count = count( $media_files_list );

		$model = array(
			'files'   => $media_files_list,
			'post_id' => $post_id
		);

		$html = $this->template_loader->get_template()->show( $model, 'media-selector.twig' );

		wp_send_json_success( array( 'html' => $html, 'media_files_count' => $media_files_count ) );
	}

	/**
	 * @param int $post_id
	 * @param array $languages
	 *
	 * @return array
	 */
	private function get_media_files_list( $post_id, $languages ) {

		$media_files_list = array();

		$post_with_media = $this->post_with_media_files_factory->create( $post_id );

		$media_ids = $post_with_media->get_media_ids();

		foreach ( $media_ids as $attachment_id ) {

			$media_files_list[ $attachment_id ] = array(
				'thumbnail'  => wp_get_attachment_thumb_url( $attachment_id ),
				'name'       => get_post_field( 'post_title', $attachment_id ),
				'translated' => $this->media_file_is_translated( $attachment_id, $languages )
			);
		}

		return $media_files_list;
	}

	private function media_file_is_translated( $attachment_id, $languages ) {
		$post_element = $this->translation_element_factory->create( $attachment_id, 'post' );
		foreach ( $languages as $language ) {
			$translation = $post_element->get_translation( $language );
			if ( null === $translation || get_post_meta( $attachment_id, '_wp_attached_file', true )
			                              === get_post_meta( $translation->get_id(), '_wp_attached_file', true ) ) {
				return false;
			}
		}

		return true;
	}

	public function toggle_show_media_selector() {
		$current_value = get_user_meta( get_current_user_id(), self::USER_META_HIDE_POST_MEDIA_SELECTOR, true );
		update_user_meta( get_current_user_id(), self::USER_META_HIDE_POST_MEDIA_SELECTOR, ! $current_value );
		wp_send_json_success();
	}

	/**
	 * @param array $row_data
	 * @param stdClass $doc_data
	 *
	 * @return array
	 */
	public function add_media_data_to_dashboard_row( $row_data, $doc_data ) {
		if ( 0 !== strpos( $doc_data->translation_element_type, 'post_' ) ) {
			return $row_data;
		}

		$row_data = $this->add_post_has_media_flag( $row_data, $doc_data->ID );
		$row_data = $this->add_post_type_attribute_data( $row_data, $doc_data->ID );

		return $row_data;
	}

	/**
	 * @param array $data
	 * @param int $post_id
	 *
	 * @return array
	 */
	private function add_post_has_media_flag( array $data, $post_id ) {
		$data['has-media'] = get_post_meta( $post_id, WPML_Media_Set_Posts_Media_Flag::HAS_MEDIA_POST_FLAG, true );

		return $data;
	}

	/**
	 * @param array $data
	 * @param int $post_id
	 *
	 * @return array
	 */
	private function add_post_type_attribute_data( $data, $post_id ) {
		$post_type        = get_post_type( $post_id );
		$post_type_object = get_post_type_object( $post_type );

		$data['post-type'] = strtolower( $post_type_object->labels->singular_name );

		return $data;
	}

	public function add_media_selector_preloader() {
		$model = array(
			'strings'       => array(
				'has_posts' => sprintf(
					__(
						'Choose which media to translate with this %s',
						'wpml-media'
					), '%POST_TYPE%'
				),
				'loading'   => __( 'Loading...', 'wpml-media' )
			),
			'hide_selector' => get_user_meta( get_current_user_id(), self::USER_META_HIDE_POST_MEDIA_SELECTOR, true )
		);
		echo $this->template_loader->get_template()->show( $model, 'media-selector-preloader.twig' );
	}
}