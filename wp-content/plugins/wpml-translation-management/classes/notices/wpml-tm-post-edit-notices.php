<?php

class WPML_TM_Post_Edit_Notices {

	const TEMPLATE_TRANSLATION_IN_PROGRESS     = 'translation-in-progress.twig';
	const TEMPLATE_USE_PREFERABLY_TM_DASHBOARD = 'use-preferably-tm-dashboard.twig';
	const TEMPLATE_USE_PREFERABLY_TE           = 'use-preferably-translation-editor.twig';
	const DO_NOT_SHOW_AGAIN_ACTION             = 'wpml_dismiss_post_edit_te_notice';

	/** @var WPML_Post_Status $post_status */
	private $post_status;

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var IWPML_Template_Service $template_render */
	private $template_render;

	/** @var WPML_Super_Globals_Validation $super_globals */
	private $super_globals;

	/** @var WPML_TM_Translation_Status_Display $status_display */
	private $status_display;

	/** @var WPML_Translation_Element_Factory $element_factory */
	private $element_factory;

	/** @var bool $use_translation_editor */
	private $use_translation_editor;

	/**
	 * @param WPML_Post_Status                   $post_status
	 * @param SitePress                          $sitepress
	 * @param IWPML_Template_Service             $template_render
	 * @param WPML_Super_Globals_Validation      $super_globals
	 * @param WPML_TM_Translation_Status_Display $status_display
	 * @param WPML_Translation_Element_Factory   $element_factory
	 * @param bool                               $use_translation_editor
	 */
	public function __construct(
		WPML_Post_Status $post_status,
		SitePress $sitepress,
		IWPML_Template_Service $template_render,
		WPML_Super_Globals_Validation $super_globals,
		WPML_TM_Translation_Status_Display $status_display,
		WPML_Translation_Element_Factory $element_factory,
		$use_translation_editor
	) {
		$this->post_status            = $post_status;
		$this->sitepress              = $sitepress;
		$this->template_render        = $template_render;
		$this->super_globals          = $super_globals;
		$this->status_display         = $status_display;
		$this->element_factory        = $element_factory;
		$this->use_translation_editor = $use_translation_editor;
	}

	public function add_hooks() {
		$request_get_trid = isset( $_GET['trid'] ) ?
			filter_var( $_GET['trid'], FILTER_SANITIZE_NUMBER_INT ) :
			'';

		$request_get_post = isset( $_GET['post'] ) ?
			filter_var( $_GET['post'], FILTER_SANITIZE_NUMBER_INT ) :
			'';

		$request_get_lang = isset( $_GET['lang'] ) ?
			filter_var( $_GET['lang'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ) :
			'';

		if ( ( $request_get_trid || $request_get_post ) && $request_get_lang ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'admin_notices', array( $this, 'display_notices' ) );
		}

		add_action( 'wp_ajax_' . self::DO_NOT_SHOW_AGAIN_ACTION, array( $this, 'do_not_display_it_again' ) );
	}

	public function enqueue_assets() {
		wp_enqueue_script(
			'wpml-tm-post-edit-alert',
			WPML_TM_URL . '/res/js/post-edit-alert.js',
			array( 'jquery', 'jquery-ui-dialog' ),
			WPML_TM_VERSION
		);
	}

	public function display_notices() {
		$trid    = $this->super_globals->get( 'trid', FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE );
		$post_id = $this->super_globals->get( 'post', FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE );
		$lang    = $this->super_globals->get( 'lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_NULL_ON_FAILURE );

		if ( ! $post_id ) {
			return;
		}

		$post_element = $this->element_factory->create( $post_id, 'post' );
		$is_original  = ! $post_element->get_source_language_code();

		if ( ! $trid ) {
			$trid = $post_element->get_trid();
		}

		if ( $trid ) {
			$translation_status = (int) $this->post_status->get_status( $post_id, $trid, $lang );

			if ( $this->is_translation_in_progress( $translation_status ) ) {
				$model = array(
					'warning' => sprintf(
						__( '%sWarning:%s You are trying to edit a translation that is currently in the process of being added using WPML.', 'wpml-translation-management' ),
						'<strong>',
						'</strong>'
					),
					'check_dashboard' => sprintf(
						__( 'Please refer to the <a href="%s">Translation Management dashboard</a> for the exact status of this translation.', 'wpml-translation-management' ),
						admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&' )
					),
				);

				echo $this->template_render->show( $model, self::TEMPLATE_TRANSLATION_IN_PROGRESS );

			} elseif (
				! $is_original &&
				$this->use_translation_editor &&
				apply_filters( 'wpml_tm_show_page_builders_translation_editor_warning', true, $post_id ) &&
			    $this->should_display_it()
			) {

				$model = array(
					'warning' => sprintf(
						__( '%sWarning:%s You are trying to edit a translation using the standard WordPress editor but your site is configured to use the WPML Translation Editor.', 'wpml-translation-management' ),
						'<strong>',
						'</strong>'
					),
				    'go_back_button'         => __( 'Go back', 'wpml-translation-management' ),
				    'edit_anyway_button'     => __( 'Edit anyway', 'wpml-translation-management' ),
				    'open_in_te_button'      => __( 'Open in Translation Editor', 'wpml-translation-management' ),
				    'translation_editor_url' => $this->get_translation_editor_link( $post_element ),
					'do_not_show_again'      => __( "Don't show this warning again", 'wpml-translation-management' ),
					'nonce'                  => wp_nonce_field(
						self::DO_NOT_SHOW_AGAIN_ACTION,
						self::DO_NOT_SHOW_AGAIN_ACTION,
						true,
						false
					),
				);

				echo $this->template_render->show( $model, self::TEMPLATE_USE_PREFERABLY_TE );
			}

		} elseif ( $post_element->is_translatable() && $this->use_translation_editor ){
			$model = array(
				'warning' => sprintf(
					__('%sWarning:%s You are trying to add a translation using the standard WordPress editor but your site is configured to use the WPML Translation Editor.' , 'wpml-translation-management'),
					'<strong>',
					'</strong>'
				),
				'use_tm_dashboard' => sprintf(
					__( 'You should use <a href="%s">Translation management dashboard</a> to send the original document to translation.' , 'wpml-translation-management' ),
					admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php' )
				),
			);

			echo $this->template_render->show( $model, self::TEMPLATE_USE_PREFERABLY_TM_DASHBOARD );
		}
	}

	public function do_not_display_it_again() {
		update_option( self::DO_NOT_SHOW_AGAIN_ACTION, 1, false );
	}

	/**
	 * @return bool
	 */
	private function should_display_it() {
		return false === get_option( self::DO_NOT_SHOW_AGAIN_ACTION );
	}

	/**
	 * @param int|null $translation_status
	 *
	 * @return bool
	 */
	private function is_translation_in_progress( $translation_status ) {
		return ! is_null( $translation_status )
		       && $translation_status > 0
		       && $translation_status != ICL_TM_DUPLICATE
		       && $translation_status < ICL_TM_COMPLETE;
	}

	/**
	 * @param WPML_Post_Element $post_element
	 *
	 * @return string
	 */
	private function get_translation_editor_link( WPML_Post_Element $post_element ) {
		$post_id             = $post_element->get_id();
		$source_post_element = $post_element->get_source_element();

		if ( $source_post_element ) {
			$post_id = $source_post_element->get_id();
		}

		$url = $this->status_display->filter_status_link(
			'#', $post_id, $post_element->get_language_code(), $post_element->get_trid()
		);

		return remove_query_arg( 'return_url', $url );
	}
}
