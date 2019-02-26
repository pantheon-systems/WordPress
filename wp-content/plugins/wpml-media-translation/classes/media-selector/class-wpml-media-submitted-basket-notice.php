<?php

class WPML_Media_Submitted_Basket_Notice implements IWPML_Action {
	/**
	 * @var WPML_Twig_Template_Loader
	 */
	private $template_loader;

	public function __construct( WPML_Twig_Template_Loader $template_loader ) {
		$this->template_loader = $template_loader;
	}

	public function add_hooks() {
		add_action( 'wpml_tm_scripts_enqueued', array( $this, 'load_js' ) );
		add_action( 'wpml_translation_basket_page_after', array( $this, 'load_dialog_template' ) );
	}

	public function load_js() {
		$script_handle = 'submitted-basket-notice';
		wp_enqueue_script(
			$script_handle,
			WPML_MEDIA_URL . '/res/js/submitted-basket-notice.js',
			array( 'jquery-ui-dialog' ), WPML_MEDIA_VERSION, false );

		$wpml_media_basket_notice_data = array(
			'button_label' => __( 'Continue', 'wpml_media' ),
		);
		wp_localize_script( $script_handle, 'wpml_media_basket_notice_data', $wpml_media_basket_notice_data );
	}

	public function load_dialog_template() {

		/* translators: WPML plugin name */
		$wpml_plugin_name = __( 'WPML', 'wpml-media' );
		/* translators: WPML Media Translation saddon/section name */
		$media_translation_name = __( 'Media Translation', 'wpml-media' );

		$media_translation_url  = admin_url( 'admin.php?page=wpml-media' );
		$media_translation_link = sprintf(
			'<a href="%s" target="_blank" rel="noopener" class="wpml-external-link">%s &raquo; %s</a>',
			$media_translation_url,
			$wpml_plugin_name,
			$media_translation_name
		);

		/* translators: media file string used in "if you want to use a different media file for each language..." */
		$media_file_string = __( 'media file', 'wpml-media' );
		$redirect_url = '#';

		if ( defined( 'WPML_TM_FOLDER' ) ) {
			$redirect_url = add_query_arg( 'page', WPML_TM_FOLDER . '/menu/main.php', admin_url( 'admin.php' ) );
		}

		$model = array(
			'strings' => array(
				'dialog_title'            => __( 'Media sent to translation', 'wpml-media' ),
				'content_with_media_sent' => __( 'You have sent content which contains media attachments for translation.', 'wpml-media' ),
				'media_texts_translated'  => sprintf( __( 'Translators will translate all your %smedia texts%s.', 'wpml-media' ), '<strong>', '</strong>' ),
				'use_different_media'     => sprintf( __( 'If you want to use a different %s for each language, you can set them in: %s.', 'wpml-media' ),
					'<strong>' . $media_file_string . '</strong>', $media_translation_link ),
				'learn_more'              => __( 'Learn more about Media Translation', 'wpml-media' ),
				'wpml'                    => _x( 'WPML', 'plugin name', 'wpml-media' ),
				'media_translation'       => _x( 'Media Translation', 'wpml addon name', 'wpml-media' )
			),

			'learn_more_url' => 'https://wpml.org/?page_id=113610',
			'redirect_url' => $redirect_url,
		);

		echo $this->template_loader->get_template()->show( $model, 'submitted-basket-notice.twig' );

	}

}