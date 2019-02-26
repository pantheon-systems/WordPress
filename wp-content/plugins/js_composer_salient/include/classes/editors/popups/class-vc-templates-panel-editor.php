<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Templates_Panel_Editor
 * @since 4.4
 */
class Vc_Templates_Panel_Editor {
	/**
	 * @since 4.4
	 * @var string
	 */
	protected $option_name = 'wpb_js_templates';
	/**
	 * @since 4.4
	 * @var bool
	 */
	protected $default_templates = false;
	/**
	 * @since 4.4
	 * @var bool
	 */
	protected $initialized = false;
	
	/*nectar addition*/
	protected $template_count = 0;
	/*nectar addition end*/
	
	/**
	 * @since 4.4
	 * Add ajax hooks, filters.
	 */
	public function init() {
		if ( $this->initialized ) {
			return;
		}
		$this->initialized = true;
		add_filter( 'vc_load_default_templates_welcome_block', array(
			$this,
			'loadDefaultTemplatesLimit',
		) );

		add_filter( 'vc_templates_render_category', array(
			$this,
			'renderTemplateBlock',
		), 10 );
		add_filter( 'vc_templates_render_template', array(
			$this,
			'renderTemplateWindow',
		), 10, 2 );

		/**
		 * Ajax methods
		 *  'vc_save_template' -> saving content as template
		 *  'vc_backend_load_template' -> loading template content for backend
		 *  'vc_frontend_load_template' -> loading template content for frontend
		 *  'vc_delete_template' -> deleting template by index
		 */
		add_action( 'wp_ajax_vc_save_template', array(
			$this,
			'save',
		) );
		add_action( 'wp_ajax_vc_backend_load_template', array(
			$this,
			'renderBackendTemplate',
		) );
		add_action( 'wp_ajax_vc_frontend_load_template', array(
			$this,
			'renderFrontendTemplate',
		) );
		add_action( 'wp_ajax_vc_load_template_preview', array(
			$this,
			'renderTemplatePreview',
		) );
		add_action( 'wp_ajax_vc_delete_template', array(
			$this,
			'delete',
		) );

		/*		add_action( 'vc-render-templates-preview-template', array(
					$this,
					'addScriptsToTemplatePreview',
				) );*/

	}

	public function addBodyClassTemplatePreview() {
		return 'vc_general-template-preview';
	}

	public function renderTemplateBlock( $category ) {
		if ( 'my_templates' === $category['category'] ) {
			$category['output'] = '';

			if ( vc_user_access()->part( 'templates' )->checkStateAny( true, null )->get() ) {
				$category['output'] .= '
				<div class="vc_column vc_col-sm-12" data-vc-hide-on-search="true">
					<div class="vc_element_label">' . esc_html( __( 'Save current layout as a template', 'js_composer' ) ) . '</div>
					<div class="vc_input-group">
						<input name="padding" data-js-element="vc-templates-input" class="vc_form-control wpb-textinput vc_panel-templates-name" type="text" value="" placeholder="' . esc_attr__( 'Template name', 'js_composer' ) . '" data-vc-disable-empty="#vc_ui-save-template-btn">
						<span class="vc_input-group-btn">
							<button class="vc_general vc_ui-button vc_ui-button-size-sm vc_ui-button-action vc_ui-button-shape-rounded vc_template-save-btn" id="vc_ui-save-template-btn" disabled data-vc-ui-element="button-save">' . esc_html( __( 'Save Template', 'js_composer' ) ) . '</button>
						</span>
					</div>
					<span class="vc_description">' . esc_html( __( 'Save layout and reuse it on different sections of this site.', 'js_composer' ) ) . '</span>
				</div>';
			}

			$category['output'] .= '<div class="vc_column vc_col-sm-12">';
			if ( isset( $category['category_name'] ) ) {
				$category['output'] .= '<h3>' . esc_html( $category['category_name'] ) . '</h3>';
			}
			if ( isset( $category['category_description'] ) ) {
				$category['output'] .= '<p class="vc_description">' . esc_html( $category['category_description'] ) . '</p>';
			}

			$category['output'] .= '</div>';
			$category['output'] .= '
			<div class="vc_column vc_col-sm-12">
				<div class="vc_ui-template-list vc_templates-list-my_templates vc_ui-list-bar" data-vc-action="collapseAll">';
			if ( ! empty( $category['templates'] ) ) {
				foreach ( $category['templates'] as $template ) {
					$category['output'] .= $this->renderTemplateListItem( $template );
				}
			}
			$category['output'] .= '
				</div>
			</div>';
		} else {
			if ( 'default_templates' === $category['category'] ) {
				$category['output'] = '<div class="vc_col-md-12">';
				if ( isset( $category['category_name'] ) ) {
					$category['output'] .= '<h3>' . esc_html( $category['category_name'] ) . '</h3>';
				}
				if ( isset( $category['category_description'] ) ) {
					$category['output'] .= '<p class="vc_description">' . esc_html( $category['category_description'] ) . '</p>';
				}
				$category['output'] .= '</div>';
				$category['output'] .= '
				<div class="vc_column vc_col-sm-12">
					<div class="vc_ui-template-list vc_templates-list-default_templates vc_ui-list-bar" data-vc-action="collapseAll">';
				if ( ! empty( $category['templates'] ) ) {
					foreach ( $category['templates'] as $template ) {
						$category['output'] .= $this->renderTemplateListItem( $template );
					}
				}
				$category['output'] .= '
				</div>
			</div>';

			}
		}

		return $category;
	}

	/** Output rendered template in new panel dialog
	 * @since 4.4
	 *
	 * @param $template_name
	 * @param $template_data
	 *
	 * @return string
	 */
	function renderTemplateWindow( $template_name, $template_data ) {
		if ( 'my_templates' === $template_data['type'] ) {
			return $this->renderTemplateWindowMyTemplates( $template_name, $template_data );
		} else {
			if ( 'default_templates' === $template_data['type'] ) {
				return $this->renderTemplateWindowDefaultTemplates( $template_name, $template_data );
			}
		}

		return $template_name;
	}

	/**
	 * @since 4.4
	 *
	 * @param $template_name
	 * @param $template_data
	 *
	 * @return string
	 */
	public function renderTemplateWindowMyTemplates( $template_name, $template_data ) {
		ob_start();
		$template_id = esc_attr( $template_data['unique_id'] );
		$template_id_hash = md5( $template_id ); // needed for jquery target for TTA
		$template_name = esc_html( $template_name );
		$preview_template_title = esc_attr__( 'Preview template', 'js_composer' );
		$add_template_title = esc_attr__( 'Add template', 'js_composer' );
		$deleteControlRender = '';
		if ( vc_user_access()->part( 'templates' )->checkStateAny( true, null )->get() ) {
			$delete_template_title = esc_attr__( 'Delete template', 'js_composer' );
			$deleteControlRender = <<<DATA
				<button type="button" class="vc_general vc_ui-control-button" data-vc-ui-delete="template-title" title="$delete_template_title">
					<i class="vc-composer-icon vc-c-icon-delete_empty"></i>
				</button>
DATA;
		}
		echo <<<HTML
			<button type="button" class="vc_ui-list-bar-item-trigger" title="$add_template_title"
					 	data-template-handler=""
						data-vc-ui-element="template-title">$template_name</button>
			<div class="vc_ui-list-bar-item-actions">
				<button type="button" class="vc_general vc_ui-control-button" title="$add_template_title"
					 	data-template-handler="">
					<i class="vc-composer-icon vc-c-icon-add"></i>
				</button>
				$deleteControlRender
				<button type="button" class="vc_general vc_ui-control-button" title="$preview_template_title"
					data-vc-container=".vc_ui-list-bar" data-vc-preview-handler data-vc-target="[data-template_id_hash=$template_id_hash]">
					<i class="vc-composer-icon vc-c-icon-arrow_drop_down"></i>
				</button>
			</div>
HTML;

		return ob_get_clean();
	}

	/**
	 * @since 4.4
	 *
	 * @param $template_name
	 * @param $template_data
	 *
	 * @return string
	 */
	public function renderTemplateWindowDefaultTemplates( $template_name, $template_data ) {
		ob_start();
		$template_id = esc_attr( $template_data['unique_id'] );
		$template_id_hash = md5( $template_id ); // needed for jquery target for TTA
		$template_name = esc_html( $template_name );
		$preview_template_title = esc_attr__( 'Preview template', 'js_composer' );
		$add_template_title = esc_attr__( 'Add template', 'js_composer' );

		echo <<<HTML
		<button type="button" class="vc_ui-list-bar-item-trigger" title="$add_template_title"
			data-template-handler=""
			data-vc-ui-element="template-title">$template_name</button>
		<div class="vc_ui-list-bar-item-actions">
			<button type="button" class="vc_general vc_ui-control-button" title="$add_template_title"
					data-template-handler="">
				<i class="vc-composer-icon vc-c-icon-add"></i>
			</button>
			<button type="button" class="vc_general vc_ui-control-button" title="$preview_template_title"
				data-vc-container=".vc_ui-list-bar" data-vc-preview-handler data-vc-target="[data-template_id_hash=$template_id_hash]">
				<i class="vc-composer-icon vc-c-icon-arrow_drop_down"></i>
			</button>
		</div>
HTML;

		return ob_get_clean();
	}

	/**
	 * @since 4.4
	 * vc_filter: vc_templates_render_frontend_template - called when unknown template received to render in frontend.
	 */
	function renderFrontendTemplate() {
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie()->part( 'templates' )->can()->validateDie();

		add_filter( 'vc_frontend_template_the_content', array(
			$this,
			'frontendDoTemplatesShortcodes',
		) );
		$template_id = vc_post_param( 'template_unique_id' );
		$template_type = vc_post_param( 'template_type' );
		add_action( 'wp_print_scripts', array(
			$this,
			'addFrontendTemplatesShortcodesCustomCss',
		) );

		if ( '' === $template_id ) {
			die( 'Error: Vc_Templates_Panel_Editor::renderFrontendTemplate:1' );
		}
		WPBMap::addAllMappedShortcodes();
		if ( 'my_templates' === $template_type ) {
			$saved_templates = get_option( $this->option_name );
			vc_frontend_editor()->setTemplateContent( $saved_templates[ $template_id ]['template'] );
			vc_frontend_editor()->enqueueRequired();
			vc_include_template( 'editors/frontend_template.tpl.php', array(
				'editor' => vc_frontend_editor(),
			) );
			die();
		} else {
			if ( 'default_templates' === $template_type ) {
				$this->renderFrontendDefaultTemplate();
			} else {
				echo apply_filters( 'vc_templates_render_frontend_template', $template_id, $template_type );
			}
		}
		die(); // no needs to do anything more. optimization.
	}

	/**
	 * Load frontend default template content by index
	 * @since 4.4
	 */
	public function renderFrontendDefaultTemplate() {
		$template_index = (int) vc_post_param( 'template_unique_id' );
		$data = $this->getDefaultTemplate( $template_index );
		if ( ! $data ) {
			die( 'Error: Vc_Templates_Panel_Editor::renderFrontendDefaultTemplate:1' );
		}
		vc_frontend_editor()->setTemplateContent( trim( $data['content'] ) );
		vc_frontend_editor()->enqueueRequired();
		vc_include_template( 'editors/frontend_template.tpl.php', array(
			'editor' => vc_frontend_editor(),
		) );
		die();
	}

	/**
	 * @since 4.7
	 */
	public function renderUITemplate() {
		vc_include_template( 'editors/popups/vc_ui-panel-templates.tpl.php', array(
			'box' => $this,
		) );

		return '';
	}

	/**
	 * @since 4.4
	 */
	public function save() {
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie()->part( 'templates' )->checkStateAny( true, null )->validateDie();

		$template_name = vc_post_param( 'template_name' );
		$template = vc_post_param( 'template' );
		if ( ! isset( $template_name ) || '' === trim( $template_name ) || ! isset( $template ) || '' === trim( $template ) ) {
			header( ':', true, 500 );
			throw new Exception( 'Error: Vc_Templates_Panel_Editor::save:1' ); // @todo set default function for reply
		}

		$template_arr = array(
			'name' => stripslashes( $template_name ),
			'template' => stripslashes( $template ),
		);

		$saved_templates = get_option( $this->option_name );

		$template_id = sanitize_title( $template_name ) . '_' . rand();
		if ( false === $saved_templates ) {
			$deprecated = '';
			$autoload = 'no';
			$new_template = array();
			$new_template[ $template_id ] = $template_arr;
			add_option( $this->option_name, $new_template, $deprecated, $autoload );
		} else {
			$saved_templates[ $template_id ] = $template_arr;
			update_option( $this->option_name, $saved_templates );
		}
		$template = array(
			'name' => $template_arr['name'],
			'type' => 'my_templates',
			'unique_id' => $template_id,
		);
		echo $this->renderTemplateListItem( $template );
		die();
	}

	/**
	 * Loading Any templates Shortcodes for backend by string $template_id from AJAX
	 * @since 4.4
	 * vc_filter: vc_templates_render_backend_template - called when unknown template requested to render in backend
	 */
	public function renderBackendTemplate() {
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie()->part( 'templates' )->can()->validateDie();

		$template_id = vc_post_param( 'template_unique_id' );
		$template_type = vc_post_param( 'template_type' );

		if ( ! isset( $template_id, $template_type ) || '' === $template_id || '' === $template_type ) {
			die( 'Error: Vc_Templates_Panel_Editor::renderBackendTemplate:1' );
		}
		WPBMap::addAllMappedShortcodes();
		if ( 'my_templates' === $template_type ) {
			$saved_templates = get_option( $this->option_name );

			$content = trim( $saved_templates[ $template_id ]['template'] );
			$content = str_replace( '\"', '"', $content );
			$pattern = get_shortcode_regex();
			$content = preg_replace_callback( "/{$pattern}/s", 'vc_convert_shortcode', $content );
			echo $content;
			die();
		} else {
			if ( 'default_templates' === $template_type ) {
				$this->getBackendDefaultTemplate();
				die();
			} else {
				echo apply_filters( 'vc_templates_render_backend_template', $template_id, $template_type );
				die();
			}
		}
	}

	/**
	 * Render new template view as backened editor content.
	 *
	 * @since 4.8
	 */
	public function renderTemplatePreview() {
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( array(
				'edit_post',
				(int) vc_request_param( 'post_id' ),
			) )->validateDie()->part( 'templates' )->can()->validateDie();

		$template_id = vc_request_param( 'template_unique_id' );
		$template_type = vc_request_param( 'template_type' );
		global $current_user;
		wp_get_current_user();

		if ( ! isset( $template_id, $template_type ) || '' === $template_id || '' === $template_type ) {
			die( __( 'Error: wrong template id.', 'js_composer' ) );
		}
		WPBMap::addAllMappedShortcodes();
		if ( 'my_templates' === $template_type ) {
			$saved_templates = get_option( $this->option_name );

			$content = trim( $saved_templates[ $template_id ]['template'] );
			$content = str_replace( '\"', '"', $content );
			$pattern = get_shortcode_regex();
			$content = preg_replace_callback( "/{$pattern}/s", 'vc_convert_shortcode', $content );
		} else {
			if ( 'default_templates' === $template_type ) {
				$content = $this->getBackendDefaultTemplate( true );
			} else {
				$content = apply_filters( 'vc_templates_render_backend_template_preview', $template_id, $template_type );
			}
		}

		vc_include_template( apply_filters( 'vc_render_template_preview_include_template', 'editors/vc_ui-template-preview.tpl.php' ), array(
			'content' => $content,
			'editorPost' => get_post( vc_request_param( 'post_id' ) ),
			'current_user' => $current_user,
		) );
		die();

	}

	public function registerPreviewScripts() {
		visual_composer()->registerAdminJavascript();
		visual_composer()->registerAdminCss();
		vc_backend_editor()->registerBackendJavascript();
		vc_backend_editor()->registerBackendCss();
		wp_register_script( 'vc_editors-templates-preview-js', vc_asset_url( 'js/editors/templates-preview.js' ), array(
			'vc-backend-min-js',
		), WPB_VC_VERSION, true );
	}

	/**
	 * Enqueue required scripts for template preview
	 * @since 4.8
	 */
	public function enqueuePreviewScripts() {
		vc_backend_editor()->enqueueCss();
		vc_backend_editor()->enqueueJs();
		wp_enqueue_script( 'vc_editors-templates-preview-js' );
	}

	/**
	 * @since 4.4
	 */
	public function delete() {
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie()->part( 'templates' )->checkStateAny( true, null )->validateDie();

		$template_id = vc_post_param( 'template_id' );
		$template_type = vc_post_param( 'template_type' );

		if ( ! isset( $template_id ) || '' === $template_id ) {
			die( 'Error: Vc_Templates_Panel_Editor::delete:1' );
		}
		if ( 'my_templates' === $template_type ) {

			$saved_templates = get_option( $this->option_name );
			unset( $saved_templates[ $template_id ] );
			if ( count( $saved_templates ) > 0 ) {
				update_option( $this->option_name, $saved_templates );
			} else {
				delete_option( $this->option_name );
			}
			wp_send_json_success();
		} else {
			do_action( 'vc_templates_delete_templates', $template_id, $template_type );
		}
		wp_send_json_error();
	}

	/**
	 * @since 4.4
	 *
	 * @param $templates
	 *
	 * vc_filter: vc_load_default_templates_limit_total - total items to show
	 *
	 * @return array
	 */
	public function loadDefaultTemplatesLimit( $templates ) {
		$start_index = 0;
		$total_templates_to_show = apply_filters( 'vc_load_default_templates_limit_total', 6 );

		return array_slice( $templates, $start_index, $total_templates_to_show );
	}

	/**
	 * Get user templates
	 *
	 * @since 4.12
	 * @return mixed
	 */
	public function getUserTemplates() {
		return apply_filters( 'vc_get_user_templates', get_option( $this->option_name ) );
	}

	/**
	 * Function to get all templates for display
	 *  - with image (optional preview image)
	 *  - with unique_id (required for do something for rendering.. )
	 *  - with name (required for display? )
	 *  - with type (required for requesting data in server)
	 *  - with category key (optional/required for filtering), if no category provided it will be displayed only in
	 * "All" category type vc_filter: vc_get_user_templates - hook to override "user My Templates" vc_filter:
	 * vc_get_all_templates - hook for override return array(all templates), hook to add/modify/remove more templates,
	 *  - this depends only to displaying in panel window (more layouts)
	 * @since 4.4
	 * @return array - all templates with name/unique_id/category_key(optional)/image
	 */
	public function getAllTemplates() {
		$data = array();
		// Here we go..
		if ( apply_filters( 'vc_show_user_templates', true ) ) {
			// We need to get all "My Templates"
			$user_templates = $this->getUserTemplates();
			// this has only 'name' and 'template' key  and index 'key' is template id.
			$arr_category = array(
				'category' => 'my_templates',
				/*nectar addition*/
				'category_name' => __( 'My Templates', 'js_composer' ),
				'category_description' => __( 'Append previously saved templates to the current layout.', 'js_composer' ),
				/*nectar addition end*/
				'category_weight' => 10,
			);
			$category_templates = array();
			if ( ! empty( $user_templates ) ) {
				foreach ( $user_templates as $template_id => $template_data ) {
					$category_templates[] = array(
						'unique_id' => $template_id,
						'name' => $template_data['name'],
						'type' => 'my_templates',
						// for rendering in backend/frontend with ajax
						/*nectar addition*/
						'image' => isset( $template_data['image_path'] ) ? $template_data['image_path'] : false,
						'cat_display_name' => isset( $template_data['cat_display_name'] ) ? $template_data['cat_display_name'] : '',
						// preview image
						'custom_class' => isset( $template_data['custom_class'] ) ? $template_data['custom_class'] : false,
						/*nectar addition end*/
					);
				}
			}
			$arr_category['templates'] = $category_templates;
			$data[] = $arr_category;
		}

		// To get all "Default Templates"
		$default_templates = $this->getDefaultTemplates();
		// this has 'name', 'image_path', 'custom_class', and 'content' as template data
		if ( ! empty( $default_templates ) ) {
			$arr_category = array(
				/*nectar addition*/
				'category' => 'default_templates',
				'category_name' => __( 'Salient Studio', 'js_composer' ),
				'category_description' => __( 'Append predefined Salient templates to the current layout', 'js_composer' ),
				'category_weight' => 11,
				/*nectar addition end*/
			);
			$category_templates = array();
			foreach ( $default_templates as $template_id => $template_data ) {
				if ( isset( $template_data['disabled'] ) && $template_data['disabled'] ) {
					continue;
				}
				$category_templates[] = array(
					'unique_id' => $template_id,
					'name' => $template_data['name'],
					'type' => 'default_templates',
					// for rendering in backend/frontend with ajax
					'image' => isset( $template_data['image_path'] ) ? $template_data['image_path'] : false,
					/*nectar addition*/
					'cat_display_name' => isset( $template_data['cat_display_name'] ) ? $template_data['cat_display_name'] : '',
					/*nectar addition end*/
					// preview image
					'custom_class' => isset( $template_data['custom_class'] ) ? $template_data['custom_class'] : false,
				);
			}
			if ( ! empty( $category_templates ) ) {
				$arr_category['templates'] = $category_templates;
				$data[] = $arr_category;
			}
		}

		// To get any other 3rd "Custom template" - do this by hook filter 'vc_get_all_templates'
		return apply_filters( 'vc_get_all_templates', $data );
	}

	/**
	 * Load default templates list and initialize variable
	 * To modify you should use add_filter('vc_load_default_templates','your_custom_function');
	 * Argument is array of templates data like:
	 *      array(
	 *          array(
	 *              'name'=>__('My custom template','my_plugin'),
	 *              'image_path'=> preg_replace( '/\s/', '%20', plugins_url( 'images/my_image.png', __FILE__ ) ), //
	 * always use preg replace to be sure that "space" will not break logic
	 *              'custom_class'=>'my_custom_class', // if needed
	 *              'content'=>'[my_shortcode]yeah[/my_shortcode]', // Use HEREDoc better to escape all single-quotes
	 * and double quotes
	 *          ),
	 *          ...
	 *      );
	 * Also see filters 'vc_load_default_templates_panels' and 'vc_load_default_templates_welcome_block' to modify
	 * templates in panels tab and/or in welcome block. vc_filter: vc_load_default_templates - filter to override
	 * default templates array
	 * @since 4.4
	 * @return array
	 */
	public function loadDefaultTemplates() {
		if ( ! $this->initialized ) {
			$this->init(); // add hooks if not added already (fix for in frontend)
		}

		if ( ! is_array( $this->default_templates ) ) {
			/*nectar addition*/
			//require_once vc_path_dir( 'CONFIG_DIR', 'templates.php' );
			require_once locate_template('/nectar/nectar-vc-addons/salient-studio-templates.php');
			/*nectar addition end*/
			$templates = apply_filters( 'vc_load_default_templates', $this->default_templates );
			$this->default_templates = $templates;
			do_action( 'vc_load_default_templates_action' );
		}

		return $this->default_templates;
	}

	/**
	 * Alias for loadDefaultTemplates
	 * @since 4.4
	 * @return array - list of default templates
	 */
	public function getDefaultTemplates() {
		return $this->loadDefaultTemplates();
	}

	/**
	 * Get default template data by template index in array.
	 * @since 4.4
	 *
	 * @param number $template_index
	 *
	 * @return array|bool
	 */
	public function getDefaultTemplate( $template_index ) {
		$this->loadDefaultTemplates();
		if ( ! is_numeric( $template_index ) || ! is_array( $this->default_templates ) || ! isset( $this->default_templates[ $template_index ] ) ) {
			return false;
		}

		return $this->default_templates[ $template_index ];
	}

	/**
	 * Add custom template to default templates list ( at end of list )
	 * $data = array( 'name'=>'', 'image'=>'', 'content'=>'' )
	 * @since 4.4
	 *
	 * @param $data
	 *
	 * @return bool true if added, false if failed
	 */
	public function addDefaultTemplates( $data ) {
		if ( is_array( $data ) && ! empty( $data ) && isset( $data['name'], $data['content'] ) ) {
			if ( ! is_array( $this->default_templates ) ) {
				$this->default_templates = array();
			}
			$this->default_templates[] = $data;

			return true;
		}

		return false;
	}

	/**
	 * Load default template content by index from ajax
	 * @since 4.4
	 *
	 * @param bool $return | should function return data or not
	 *
	 * @return string
	 */
	public function getBackendDefaultTemplate( $return = false ) {
		$template_index = (int) vc_request_param( 'template_unique_id' );
		$data = $this->getDefaultTemplate( $template_index );
		if ( ! $data ) {
			die( 'Error: Vc_Templates_Panel_Editor::getBackendDefaultTemplate:1' );
		}
		if ( $return ) {
			return trim( $data['content'] );
		} else {
			echo trim( $data['content'] );
			die();
		}
	}

	/**
	 * @since 4.4
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function sortTemplatesByCategories( array $data ) {
		$buffer = $data;
		uasort( $buffer, array(
			$this,
			'cmpCategory',
		) );

		return $buffer;
	}

	/**
	 * @since 4.4
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function sortTemplatesByNameWeight( array $data ) {
		$buffer = $data;
		uasort( $buffer, array(
			$this,
			'cmpNameWeight',
		) );

		return $buffer;
	}

	/**
	 * Function should return array of templates categories
	 * @since 4.4
	 *
	 * @param array $categories
	 *
	 * @return array - associative array of category key => and visible Name
	 */
	public function getAllCategoriesNames( array $categories ) {
		$categories_names = array();

		foreach ( $categories as $category ) {
			if ( isset( $category['category'] ) ) {
				$categories_names[ $category['category'] ] = isset( $category['category_name'] ) ? $category['category_name'] : $category['category'];
			}
		}

		return $categories_names;
	}

	/**
	 * @since 4.4
	 * @return array
	 */
	public function getAllTemplatesSorted() {
		$data = $this->getAllTemplates();
		// firstly we need to sort by categories
		$data = $this->sortTemplatesByCategories( $data );
		// secondly we need to sort templates by their weight or name
		foreach ( $data as $key => $category ) {
			$data[ $key ]['templates'] = $this->sortTemplatesByNameWeight( $category['templates'] );
		}

		return $data;
	}

	/**
	 * Used to compare two templates by category, category_weight
	 * If category weight is less template will appear in first positions
	 * @since 4.4
	 *
	 * @param array $a - template one
	 * @param array $b - second template to compare
	 *
	 * @return int
	 */
	protected function cmpCategory( $a, $b ) {
		$a_k = isset( $a['category'] ) ? $a['category'] : '*';
		$b_k = isset( $b['category'] ) ? $b['category'] : '*';
		$a_category_weight = isset( $a['category_weight'] ) ? $a['category_weight'] : 0;
		$b_category_weight = isset( $b['category_weight'] ) ? $b['category_weight'] : 0;

		return $a_category_weight == $b_category_weight ? strcmp( $a_k, $b_k ) : $a_category_weight - $b_category_weight;
	}

	/**
	 * @since 4.4
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	protected function cmpNameWeight( $a, $b ) {
		$a_k = isset( $a['name'] ) ? $a['name'] : '*';
		$b_k = isset( $b['name'] ) ? $b['name'] : '*';
		$a_weight = isset( $a['weight'] ) ? $a['weight'] : 0;
		$b_weight = isset( $b['weight'] ) ? $b['weight'] : 0;

		return $a_weight == $b_weight ? strcmp( $a_k, $b_k ) : $a_weight - $b_weight;
	}

	/**
	 * Calls do_shortcode for templates.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function frontendDoTemplatesShortcodes( $content ) {
		return do_shortcode( $content );
	}

	/**
	 * Add custom css from shortcodes from template for template editor.
	 *
	 * Used by action 'wp_print_scripts'.
	 *
	 * @todo move to autoload or else some where.
	 * @since 4.4.3
	 *
	 */
	public function addFrontendTemplatesShortcodesCustomCss() {
		$output = $shortcodes_custom_css = '';
		$shortcodes_custom_css = visual_composer()->parseShortcodesCustomCss( vc_frontend_editor()->getTemplateContent() );
		if ( ! empty( $shortcodes_custom_css ) ) {
			$shortcodes_custom_css = strip_tags( $shortcodes_custom_css );
			$output .= '<style type="text/css" data-type="vc_shortcodes-custom-css">';
			$output .= $shortcodes_custom_css;
			$output .= '</style>';
		}
		echo $output;
	}

	public function addScriptsToTemplatePreview() {
		// wp_enqueue_script( 'vc-template-preview-script', vc_asset_url( 'js/editors/vc_ui-panel-templates-preview-be.js' ), array( 'vc-backend-min-js' ), WPB_VC_VERSION, true );
	}

	public function renderTemplateListItem( $template ) {
		$name = isset( $template['name'] ) ? esc_html( $template['name'] ) : esc_html( __( 'No title', 'js_composer' ) );
		$template_id = esc_attr( $template['unique_id'] );
		$template_id_hash = md5( $template_id ); // needed for jquery target for TTA
		$template_name = esc_html( $name );
		$template_name_lower = esc_attr( vc_slugify( $template_name ) );
		$template_type = esc_attr( isset( $template['type'] ) ? $template['type'] : 'custom' );
		$custom_class = esc_attr( isset( $template['custom_class'] ) ? $template['custom_class'] : '' );

		/*nectar addition - adding in preview img*/
		$preview_img = esc_attr( isset( $template['image'] ) &&  $template['image'] != '' ? $template['image'] : get_template_directory_uri() .'/nectar/nectar-vc-addons/img/templates/no-img.jpg'  );
		$cat_display_name = esc_attr( isset( $template['cat_display_name'] ) ? $template['cat_display_name'] : '' );

		$output = <<<HTML
					<div class="vc_ui-template vc_templates-template-type-$template_type $custom_class"
						data-template_id="$template_id"
						data-template_id_hash="$template_id_hash"
						data-category="$template_type"
						data-template_unique_id="$template_id"
						data-template_name="$template_name_lower"
						data-template_type="$template_type"
						data-vc-content=".vc_ui-template-content">
						<div class="vc_ui-list-bar-item">
HTML;
	
	if(!empty($preview_img) && $template_type == 'default_templates') { 
		//lazy load images out of view
		if($this->template_count > 6) {
			$output .= '<div class="img-wrap"><img data-src="'.$preview_img.'" alt="'.$name.'" width="300" height="200" /></div><div class="display_cat">'.$cat_display_name .'</div>';
		} else {
			$output .= '<div class="img-wrap"><img src="'.$preview_img.'" alt="'.$name.'" width="300" height="200" /></div><div class="display_cat">'.$cat_display_name .'</div>';
		}
		
	}
		$output .= apply_filters( 'vc_templates_render_template', $name, $template );
		$output .= <<<HTML
						</div>
						<div class="vc_ui-template-content" data-js-content>
						</div>
					</div>
HTML;

	$this->template_count++;

	/*nectar addition end*/

		return $output;
	}

	public function getOptionName() {
		return $this->option_name;
	}
}
