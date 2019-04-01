<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Manger for new post type for single grid item design with constructor
 *
 * @package WPBakeryPageBuilder
 * @since 4.4
 */
require_once vc_path_dir( 'EDITORS_DIR', 'class-vc-backend-editor.php' );

class Vc_Grid_Item_Editor extends Vc_Backend_Editor {
	protected static $post_type = 'vc_grid_item';
	protected $templates_editor = false;

	/**
	 * This method is called to add hooks.
	 *
	 * @since  4.8
	 * @access public
	 */
	public function addHooksSettings() {
		add_action( 'add_meta_boxes', array(
			$this,
			'render',
		) );
		add_action( 'vc_templates_render_backend_template', array(
			$this,
			'loadTemplate',
		), 10, 2 );
		/*		add_action( 'vc_ui-template-preview', array(
					$this,
					'replaceTemplatesPanelEditorJsAction',
				) );*/
	}

	public function addScripts() {
		$this->render( get_post_type() );
	}

	public function render( $post_type ) {
		if ( $this->isValidPostType( $post_type ) ) {
			$this->registerBackendJavascript();
			$this->registerBackendCss();
			// B.C:
			visual_composer()->registerAdminCss();
			visual_composer()->registerAdminJavascript();
			add_action( 'admin_print_scripts-post.php', array(
				$this,
				'printScriptsMessages',
			), 300 );
			add_action( 'admin_print_scripts-post-new.php', array(
				$this,
				'printScriptsMessages',
			), 300 );
		}
	}

	public function editorEnabled() {
		return vc_user_access()->part( 'grid_builder' )->can()->get();
	}

	public function replaceTemplatesPanelEditorJsAction() {
		wp_dequeue_script( 'vc-template-preview-script' );
		$this->templatesEditor()->addScriptsToTemplatePreview();
	}

	/**
	 * Create post type and new item in the admin menu.
	 * @return void
	 */
	public static function createPostType() {
		register_post_type( self::$post_type, array(
			'labels' => self::getPostTypesLabels(),
			'public' => false,
			'has_archive' => false,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_menu' => false,
			'query_var' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array(
				'title',
				'editor',
			),
		) );
	}

	public static function getPostTypesLabels() {
		return array(
			'add_new_item' => __( 'Add Grid template', 'js_composer' ),
			'name' => __( 'Grid Builder', 'js_composer' ),
			'singular_name' => __( 'Grid template', 'js_composer' ),
			'edit_item' => __( 'Edit Grid template', 'js_composer' ),
			'view_item' => __( 'View Grid template', 'js_composer' ),
			'search_items' => __( 'Search Grid templates', 'js_composer' ),
			'not_found' => __( 'No Grid templates found', 'js_composer' ),
			'not_found_in_trash' => __( 'No Grid templates found in Trash', 'js_composer' ),
		);
	}

	/**
	 * Rewrites validation for correct post_type of th post.
	 *
	 * @param string $type
	 *
	 * @return bool
	 */
	public function isValidPostType( $type = '' ) {
		$type = ! empty( $type ) ? $type : get_post_type();

		return $this->editorEnabled() && $this->postType() === $type;
	}

	/**
	 * Get post type for Vc grid element editor.
	 *
	 * @static
	 * @return string
	 */
	public static function postType() {
		return self::$post_type;
	}

	/**
	 * Calls add_meta_box to create Editor block.
	 *
	 * @access public
	 */
	public function addMetaBox() {
		add_meta_box( 'wpb_visual_composer', __( 'Grid Builder', 'js_composer' ), array(
			$this,
			'renderEditor',
		), $this->postType(), 'normal', 'high' );
	}

	/**
	 * Change order of the controls for shortcodes admin block.
	 *
	 * @return array
	 */
	public function shortcodesControls() {
		return array(
			'delete',
			'edit',
		);
	}

	/**
	 * Output html for backend editor meta box.
	 *
	 * @param null|int $post
	 *
	 * @return mixed|void
	 */
	public function renderEditor( $post = null ) {
		if ( ! vc_user_access()->part( 'grid_builder' )->can()->get() ) {
			return;
		}

		//		add_filter( 'vc_wpbakery_shortcode_get_controls_list', array(
		//			$this,
		//			'shortcodesControls',
		//		) );
		require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/class-vc-grid-item.php' );
		$this->post = $post;
		vc_include_template( 'params/vc_grid_item/editor/vc_grid_item_editor.tpl.php', array(
			'editor' => $this,
			'post' => $this->post,
		) );
		add_action( 'admin_footer', array(
			$this,
			'renderEditorFooter',
		) );
		do_action( 'vc_backend_editor_render' );
		do_action( 'vc_vc_grid_item_editor_render' );
		add_action( 'vc_user_access_check-shortcode_edit', array(
			$this,
			'accessCheckShortcodeEdit',
		), 10, 2 );
		add_action( 'vc_user_access_check-shortcode_all', array(
			$this,
			'accessCheckShortcodeAll',
		), 10, 2 );

		return;
	}

	public function accessCheckShortcodeEdit( $null, $shortcode ) {
		return vc_user_access()->part( 'grid_builder' )->can()->get();
	}

	public function accessCheckShortcodeAll( $null, $shortcode ) {
		return vc_user_access()->part( 'grid_builder' )->can()->get();
	}

	/**
	 * Output required html and js content for VC editor.
	 *
	 * Here comes panels, modals and js objects with data for mapped shortcodes.
	 */
	public function renderEditorFooter() {
		vc_include_template( 'params/vc_grid_item/editor/partials/vc_grid_item_editor_footer.tpl.php', array(
			'editor' => $this,
			'post' => $this->post,
		) );
		do_action( 'vc_backend_editor_footer_render' );
	}

	public function registerBackendJavascript() {
		parent::registerBackendJavascript();
		wp_register_script( 'vc_grid_item_editor', vc_asset_url( 'js/dist/grid-builder.min.js' ), array( 'vc-backend-min-js' ), WPB_VC_VERSION, true );
		wp_localize_script( 'vc_grid_item_editor', 'i18nLocaleGItem', array(
			'preview' => __( 'Preview', 'js_composer' ),
			'builder' => __( 'Builder', 'js_composer' ),
			'add_template_message' => __( 'If you add this template, all your current changes will be removed. Are you sure you want to add template?', 'js_composer' ),
		) );
	}

	public function enqueueJs() {
		parent::enqueueJs();
		wp_enqueue_script( 'vc_grid_item_editor' );
	}

	public function templatesEditor() {
		if ( false === $this->templates_editor ) {
			require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/editor/popups/class-vc-templates-editor-grid-item.php' );
			$this->templates_editor = new Vc_Templates_Editor_Grid_Item();
		}

		return $this->templates_editor;
	}

	public function loadPredefinedTemplate( $template_id, $template_type ) {
		ob_start();
		$this->templatesEditor()->load( $template_id );

		return ob_get_clean();

	}

	public function loadTemplate( $template_id, $template_type ) {
		if ( 'grid_templates' === $template_type ) {
			return $this->loadPredefinedTemplate( $template_id, $template_type );
		} else if ( 'grid_templates_custom' === $template_type ) {
			return $this->templatesEditor()->loadCustomTemplate( $template_id );
		}

		return $template_id;
	}

	public function templatePreviewPath( $path ) {
		return 'params/vc_grid_item/editor/vc_ui-template-preview.tpl.php';
	}

	public function renderTemplatePreview() {
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie()->part( 'grid_builder' )->can()->validateDie();

		add_action( 'vc_templates_render_backend_template_preview', array(
			$this,
			'loadTemplate',
		), 10, 2 );
		add_filter( 'vc_render_template_preview_include_template', array(
			$this,
			'templatePreviewPath',
		) );
		visual_composer()->templatesPanelEditor()->renderTemplatePreview();
	}
}
