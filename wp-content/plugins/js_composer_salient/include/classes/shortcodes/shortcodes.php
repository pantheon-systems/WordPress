<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder Shortcodes main
 *
 * @package WPBakeryPageBuilder
 *
 */

/* abstract VisualComposer class to create structural object of any type */
if ( ! class_exists( 'WPBakeryVisualComposerAbstract' ) ) {
	/**
	 * Class WPBakeryVisualComposerAbstract
	 */
	abstract class WPBakeryVisualComposerAbstract {
		/**
		 * @var
		 */
		public static $config;
		/**
		 * @var bool
		 */
		protected $is_plugin = true;
		/**
		 * @var bool
		 */
		protected $is_theme = false;
		/**
		 * @var bool
		 */
		protected $disable_updater = false;
		/**
		 * @var bool
		 */
		protected $settings_as_theme = false;
		/**
		 * @var bool
		 */
		protected $as_network_plugin = false;
		/**
		 * @var bool
		 */
		protected $is_init = false;
		/**
		 * @var string
		 */
		protected $controls_css_settings = 'cc';
		/**
		 * @var array
		 */
		protected $controls_list = array(
			'edit',
			'clone',
			'delete',
		);

		/**
		 * @var string
		 */
		protected $shortcode_content = '';

		/**
		 *
		 */
		public function __construct() {
		}

		/**
		 * @param $settings
		 */
		public function init( $settings ) {
			self::$config = (array) $settings;
		}

		/**
		 * @param $action
		 * @param $method
		 * @param int $priority
		 */
		public function addAction( $action, $method, $priority = 10 ) {
			add_action( $action, array(
				$this,
				$method,
			), $priority );
		}

		/**
		 * @param $action
		 * @param $method
		 * @param int $priority
		 *
		 * @return bool
		 */
		public function removeAction( $action, $method, $priority = 10 ) {
			return remove_action( $action, array(
				$this,
				$method,
			), $priority );
		}

		/**
		 * @param $filter
		 * @param $method
		 * @param int $priority
		 *
		 * @return bool|void
		 */
		public function addFilter( $filter, $method, $priority = 10 ) {
			return add_filter( $filter, array(
				$this,
				$method,
			), $priority );
		}

		/**
		 * @param $filter
		 * @param $method
		 * @param int $priority
		 */
		public function removeFilter( $filter, $method, $priority = 10 ) {
			remove_filter( $filter, array(
				$this,
				$method,
			), $priority );
		}

		/* Shortcode methods */
		/**
		 * @param $tag
		 * @param $func
		 */
		public function addShortCode( $tag, $func ) {
			add_shortcode( $tag, $func );
		}

		/**
		 * @param $content
		 */
		public function doShortCode( $content ) {
			do_shortcode( $content );
		}

		/**
		 * @param $tag
		 */
		public function removeShortCode( $tag ) {
			remove_shortcode( $tag );
		}

		/**
		 * @param $param
		 *
		 * @return null
		 */
		public function post( $param ) {
			return isset( $_POST[ $param ] ) ? $_POST[ $param ] : null;
		}

		/**
		 * @param $param
		 *
		 * @return null
		 */
		public function get( $param ) {
			return isset( $_GET[ $param ] ) ? $_GET[ $param ] : null;
		}

		/**
		 * @deprecated 4.5
		 *
		 * @param $asset
		 *
		 * @return string
		 */
		public function assetURL( $asset ) {
			// _deprecated_function( 'WPBakeryShortCode::assetURL', '4.5 (will be removed in 4.10)', 'vc_asset_url' );

			return vc_asset_url( $asset );
		}

		/**
		 * @param $asset
		 *
		 * @return string
		 */
		public function assetPath( $asset ) {
			return self::$config['APP_ROOT'] . self::$config['ASSETS_DIR'] . $asset;
		}

		/**
		 * @param $name
		 *
		 * @return null
		 */
		public static function config( $name ) {
			return isset( self::$config[ $name ] ) ? self::$config[ $name ] : null;
		}
	}
}

/**
 *
 */
define( 'VC_SHORTCODE_CUSTOMIZE_PREFIX', 'vc_theme_' );
/**
 *
 */
define( 'VC_SHORTCODE_BEFORE_CUSTOMIZE_PREFIX', 'vc_theme_before_' );
/**
 *
 */
define( 'VC_SHORTCODE_AFTER_CUSTOMIZE_PREFIX', 'vc_theme_after_' );
/**
 *
 */
define( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG', 'vc_shortcodes_css_class' );
if ( ! class_exists( 'WPBakeryShortCode' ) ) {
	/**
	 * Class WPBakeryShortCode
	 */
	abstract class WPBakeryShortCode extends WPBakeryVisualComposerAbstract {

		/**
		 * @var string - shortcode tag
		 */
		protected $shortcode;
		/**
		 * @var
		 */
		protected $html_template;

		/**
		 * @var
		 */
		/**
		 * @var
		 */
		protected $atts, $settings;
		/**
		 * @var int
		 */
		protected static $enqueue_index = 0;
		/**
		 * @var array
		 */
		protected static $js_scripts = array();
		/**
		 * @var array
		 */
		protected static $css_scripts = array();
		/**
		 * default scripts like scripts
		 * @var bool
		 * @since 4.4.3
		 */
		protected static $default_scripts_enqueued = false;
		/**
		 * @var string
		 */
		protected $shortcode_string = '';
		/**
		 * @var string
		 */
		protected $controls_template_file = 'editors/partials/backend_controls.tpl.php';

		public $nonDraggableClass = 'vc-non-draggable';

		/**
		 * @param $settings
		 */
		public function __construct( $settings ) {
			$this->settings = $settings;
			$this->shortcode = $this->settings['base'];
		}

		/**
		 * @param $content
		 *
		 * @return string
		 */
		public function addInlineAnchors( $content ) {
			return ( $this->isInline() || $this->isEditor() && true === $this->settings( 'is_container' ) ? '<span class="vc_container-anchor"></span>' : '' ) . $content;
		}

		/**
		 *
		 */
		public function enqueueAssets() {
			if ( ! empty( $this->settings['admin_enqueue_js'] ) ) {
				$this->registerJs( $this->settings['admin_enqueue_js'] );
			}
			if ( ! empty( $this->settings['admin_enqueue_css'] ) ) {
				$this->registerCss( $this->settings['admin_enqueue_css'] );
			}
		}

		/**
		 * Prints out the styles needed to render the element icon for the back end interface.
		 * Only performed if the 'icon' setting is a valid URL.
		 *
		 * @return void
		 * @since  4.2
		 * @modified 4.4
		 * @author Benjamin Intal
		 */
		public function printIconStyles() {
			if ( ! filter_var( $this->settings( 'icon' ), FILTER_VALIDATE_URL ) ) {
				return;
			}
			echo '
            <style>
                .vc_el-container #' . esc_attr( $this->settings['base'] ) . ' .vc_element-icon,
                .wpb_' . esc_attr( $this->settings['base'] ) . ' > .wpb_element_wrapper > .wpb_element_title > .vc_element-icon,
                .vc_el-container > #' . esc_attr( $this->settings['base'] ) . ' > .vc_element-icon,
                .vc_el-container > #' . esc_attr( $this->settings['base'] ) . ' > .vc_element-icon[data-is-container="true"],
                .compose_mode .vc_helper.vc_helper-' . esc_attr( $this->settings['base'] ) . ' > .vc_element-icon,
                .vc_helper.vc_helper-' . esc_attr( $this->settings['base'] ) . ' > .vc_element-icon,
                .compose_mode .vc_helper.vc_helper-' . esc_attr( $this->settings['base'] ) . ' > .vc_element-icon[data-is-container="true"],
                .vc_helper.vc_helper-' . esc_attr( $this->settings['base'] ) . ' > .vc_element-icon[data-is-container="true"],
                .wpb_' . esc_attr( $this->settings['base'] ) . ' > .wpb_element_wrapper > .wpb_element_title > .vc_element-icon,
                .wpb_' . esc_attr( $this->settings['base'] ) . ' > .wpb_element_wrapper > .wpb_element_title > .vc_element-icon[data-is-container="true"] {
                    background-position: 0 0;
                    background-image: url(' . esc_url( $this->settings['icon'] ) . ');
                    -webkit-background-size: contain;
                    -moz-background-size: contain;
                    -ms-background-size: contain;
                    -o-background-size: contain;
                    background-size: contain;
                }
            </style>';
		}

		/**
		 * @param $param
		 */
		protected function registerJs( $param ) {
			if ( is_array( $param ) && ! empty( $param ) ) {
				foreach ( $param as $value ) {
					$this->registerJs( $value );
				}
			} elseif ( is_string( $param ) && ! empty( $param ) ) {
				$name = 'admin_enqueue_js_' . md5( $param );
				self::$js_scripts[] = $name;
				wp_register_script( $name, $param, array( 'jquery' ), WPB_VC_VERSION, true );
			}
		}

		/**
		 * @param $param
		 */
		protected function registerCss( $param ) {
			if ( is_array( $param ) && ! empty( $param ) ) {
				foreach ( $param as $value ) {
					$this->registerCss( $value );
				}
			} elseif ( is_string( $param ) && ! empty( $param ) ) {
				$name = 'admin_enqueue_css_' . md5( $param );
				self::$css_scripts[] = $name;
				wp_register_style( $name, $param, array( 'js_composer' ), WPB_VC_VERSION );
			}
		}

		/**
		 *
		 */
		public static function enqueueCss() {
			if ( ! empty( self::$css_scripts ) ) {
				foreach ( self::$css_scripts as $stylesheet ) {
					wp_enqueue_style( $stylesheet );
				}
			}
		}

		/**
		 *
		 */
		public static function enqueueJs() {
			if ( ! empty( self::$js_scripts ) ) {
				foreach ( self::$js_scripts as $script ) {
					wp_enqueue_script( $script );
				}
			}
		}

		/**
		 * @param $shortcode
		 */
		public function shortcode( $shortcode ) {

		}

		/**
		 * @param $template
		 *
		 * @return string
		 */
		protected function setTemplate( $template ) {
			return $this->html_template = apply_filters( 'vc_shortcode_set_template_' . $this->shortcode, $template );
		}

		/**
		 * @return bool
		 */
		protected function getTemplate() {
			if ( isset( $this->html_template ) ) {
				return $this->html_template;
			}

			return false;
		}

		/**
		 * @return mixed
		 */
		protected function getFileName() {
			return $this->shortcode;
		}

		/**
		 * Find html template for shortcode output.
		 */
		protected function findShortcodeTemplate() {
			// Check template path in shortcode's mapping settings
			if ( ! empty( $this->settings['html_template'] ) && is_file( $this->settings( 'html_template' ) ) ) {
				return $this->setTemplate( $this->settings['html_template'] );
			}

			// Check template in theme directory
			$user_template = vc_shortcodes_theme_templates_dir( $this->getFileName() . '.php' );
			if ( is_file( $user_template ) ) {
				return $this->setTemplate( $user_template );
			}

			// Check default place
			$default_dir = vc_manager()->getDefaultShortcodesTemplatesDir() . '/';
			if ( is_file( $default_dir . $this->getFileName() . '.php' ) ) {
				return $this->setTemplate( $default_dir . $this->getFileName() . '.php' );
			}
			$template = apply_filters( 'vc_shortcode_set_template_' . $this->shortcode, '' );

			if ( ! empty( $template ) ? $template : '' ) {
				return $this->setTemplate( $template );
			}

			return '';
		}

		/**
		 * @param $atts
		 * @param null $content
		 *
		 * @return mixed|void
		 */
		protected function content( $atts, $content = null ) {
			return $this->loadTemplate( $atts, $content );
		}

		/**
		 * @param $atts
		 * @param null $content
		 *
		 * vc_filter: vc_shortcode_content_filter - hook to edit template content
		 * vc_filter: vc_shortcode_content_filter_after - hook after template is loaded to override output
		 *
		 * @return mixed|void
		 */
		protected function loadTemplate( $atts, $content = null ) {
			$output = '';
			if ( ! is_null( $content ) ) {
				$content = apply_filters( 'vc_shortcode_content_filter', $content, $this->shortcode );
			}
			$this->findShortcodeTemplate();
			if ( $this->html_template ) {
				ob_start();
				include( $this->html_template );
				$output = ob_get_contents();
				ob_end_clean();
			} else {
				trigger_error( sprintf( __( 'Template file is missing for `%s` shortcode. Make sure you have `%s` file in your theme folder.', 'js_composer' ), $this->shortcode, 'wp-content/themes/your_theme/vc_templates/' . $this->shortcode . '.php' ) );
			}

			return apply_filters( 'vc_shortcode_content_filter_after', $output, $this->shortcode );
		}

		/**
		 * @param $atts
		 * @param $content
		 *
		 * @return string
		 */
		public function contentAdmin( $atts, $content = null ) {
			$output = $custom_markup = $width = $el_position = '';
			if ( null !== $content ) {
				$content = wpautop( stripslashes( $content ) );
			}
			$shortcode_attributes = array( 'width' => '1/1' );
			$atts = vc_map_get_attributes( $this->shortcode, $atts ) + $shortcode_attributes;
			$this->atts = $atts;
			$elem = $this->getElementHolder( $width );
			if ( isset( $this->settings['custom_markup'] ) && '' !== $this->settings['custom_markup'] ) {
				$markup = $this->settings['custom_markup'];
				$elem = str_ireplace( '%wpb_element_content%', $this->customMarkup( $markup, $content ), $elem );
				$output .= $elem;
			} else {
				$inner = $this->outputTitle( $this->settings['name'] );
				$inner .= $this->paramsHtmlHolders( $atts );
				$elem = str_ireplace( '%wpb_element_content%', $inner, $elem );
				$output .= $elem;
			}

			return $output;
		}

		/**
		 * @return bool
		 */
		public function isAdmin() {
			return apply_filters( 'vc_shortcodes_is_admin', is_admin() );
		}

		/**
		 * @return bool
		 */
		public function isInline() {
			return vc_is_inline();
		}

		/**
		 * @return bool
		 */
		public function isEditor() {
			return vc_is_editor();
		}

		/**
		 * @param $atts
		 * @param null $content
		 * @param string $base
		 *
		 * vc_filter: vc_shortcode_output - hook to override output of shortcode
		 *
		 * @return string
		 */
		public function output( $atts, $content = null, $base = '' ) {
			$this->atts = $prepared_atts = $this->prepareAtts( $atts );
			$this->shortcode_content = $content;
			$output = '';
			$content = empty( $content ) && ! empty( $atts['content'] ) ? $atts['content'] : $content;
			if ( ( $this->isInline() || vc_is_page_editable() ) && method_exists( $this, 'contentInline' ) ) {
				$output .= $this->contentInline( $this->atts, $content );
			} else {
				$this->enqueueDefaultScripts();
				$custom_output = VC_SHORTCODE_CUSTOMIZE_PREFIX . $this->shortcode;
				$custom_output_before = VC_SHORTCODE_BEFORE_CUSTOMIZE_PREFIX . $this->shortcode; // before shortcode function hook
				$custom_output_after = VC_SHORTCODE_AFTER_CUSTOMIZE_PREFIX . $this->shortcode; // after shortcode function hook

				// Before shortcode
				if ( function_exists( $custom_output_before ) ) {
					$output .= $custom_output_before( $this->atts, $content );
				} else {
					$output .= $this->beforeShortcode( $this->atts, $content );
				}
				// Shortcode content
				if ( function_exists( $custom_output ) ) {
					$output .= $custom_output( $this->atts, $content );
				} else {
					$output .= $this->content( $this->atts, $content );
				}
				// After shortcode
				if ( function_exists( $custom_output_after ) ) {
					$output .= $custom_output_after( $this->atts, $content );
				} else {
					$output .= $this->afterShortcode( $this->atts, $content );
				}
			}
			// Filter for overriding outputs
			$output = apply_filters( 'vc_shortcode_output', $output, $this, $prepared_atts, $this->shortcode );

			return $output;
		}

		public function enqueueDefaultScripts() {
			if ( false === self::$default_scripts_enqueued ) {
				wp_enqueue_script( 'wpb_composer_front_js' );
				wp_enqueue_style( 'js_composer_front' );
				self::$default_scripts_enqueued = true;
			}
		}

		/**
		 * Return shortcode attributes, see \WPBakeryShortCode::output
		 * @since 4.4
		 * @return array
		 */
		public function getAtts() {
			return $this->atts;
		}

		/**
		 * Creates html before shortcode html.
		 *
		 * @param $atts - shortcode attributes list
		 * @param $content - shortcode content
		 *
		 * @return string - html which will be displayed before shortcode html.
		 */
		public function beforeShortcode( $atts, $content ) {
			return '';
		}

		/**
		 * Creates html before shortcode html.
		 *
		 * @param $atts - shortcode attributes list
		 * @param $content - shortcode content
		 *
		 * @return string - html which will be displayed after shortcode html.
		 */
		public function afterShortcode( $atts, $content ) {
			return '';
		}

		/**
		 * @param $el_class
		 *
		 * @return string
		 */
		public function getExtraClass( $el_class ) {
			$output = '';
			if ( '' !== $el_class ) {
				$output = ' ' . str_replace( '.', '', $el_class );
			}

			return $output;
		}

		/**
		 * @param $css_animation
		 *
		 * @return string
		 */
		public function getCSSAnimation( $css_animation ) {
			$output = '';
			if ( '' !== $css_animation && 'none' !== $css_animation ) {
				/* nectar addition */ 
				//wp_enqueue_script( 'waypoints' );
				/* nectar addition end*/ 
				wp_enqueue_style( 'animate-css' );
				$output = ' wpb_animate_when_almost_visible wpb_' . $css_animation . ' ' . $css_animation;
			}

			return $output;
		}

		/**
		 * Create HTML comment for blocks only if wpb_debug=true
		 *
		 * @deprecated 4.7 For debug type html comments use more generic debugComment function.
		 *
		 * @param $string
		 *
		 * @return string
		 */
		public function endBlockComment( $string ) {
			// _deprecated_function( 'WPBakeryShortCode::endBlockComment', '4.7 (will be removed in 4.10)', 'vc_asset_url' );
			return '';
		}

		/**
		 * if wpb_debug=true return HTML comment
		 *
		 * @since 4.7
		 * @deprecated 5.5 no need for extra info in output, use xdebug
		 * @param string $comment
		 *
		 * @return string
		 */
		public function debugComment( $comment ) {
			return '';
		}

		/**
		 * @param $name
		 *
		 * @return null
		 */
		public function settings( $name ) {
			return isset( $this->settings[ $name ] ) ? $this->settings[ $name ] : null;
		}

		/**
		 * @param $name
		 * @param $value
		 */
		public function setSettings( $name, $value ) {
			$this->settings[ $name ] = $value;
		}

		/**
		 * @since 5.5
		 * @return mixed
		 */
		public function getSettings() {
			return $this->settings;
		}

		/**
		 * @param $width
		 *
		 * @return string
		 */
		public function getElementHolder( $width ) {
			$output = '';
			$column_controls = $this->getColumnControlsModular();
			$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? 'wpb_sortable' : $this->nonDraggableClass );
			$css_class = 'wpb_' . $this->settings['base'] . ' wpb_content_element ' . $sortable . '' . ( ! empty( $this->settings['class'] ) ? ' ' . $this->settings['class'] : '' );
			$output .= '<div data-element_type="' . $this->settings['base'] . '" class="' . $css_class . '">';
			$output .= str_replace( '%column_size%', wpb_translateColumnWidthToFractional( $width ), $column_controls );
			$output .= $this->getCallbacks( $this->shortcode );
			$output .= '<div class="wpb_element_wrapper ' . $this->settings( 'wrapper_class' ) . '">';
			$output .= '%wpb_element_content%';
			$output .= '</div>';
			$output .= '</div>';

			return $output;
		}

		// Return block controls

		/**
		 * @param $controls
		 * @param string $extended_css
		 *
		 * @return string
		 */
		public function getColumnControls( $controls, $extended_css = '' ) {
			$controls_start = '<div class="vc_controls controls_element' . ( ! empty( $extended_css ) ? " {$extended_css}" : '' ) . '">';

			$controls_end = '</div>';

			$controls_add = '';
			$controls_edit = ' <a class="vc_control column_edit" href="#" title="' . sprintf( __( 'Edit %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><span class="vc_icon"></span></a>';
			$controls_delete = ' <a class="vc_control column_clone" href="#" title="' . sprintf( __( 'Clone %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><span class="vc_icon"></span></a> <a class="column_delete" href="#" title="' . sprintf( __( 'Delete %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><span class="vc_icon"></span></a>';

			$column_controls_full = $controls_start . $controls_add . $controls_edit . $controls_delete . $controls_end;
			$column_controls_size_delete = $controls_start . $controls_delete . $controls_end;
			$column_controls_popup_delete = $controls_start . $controls_delete . $controls_end;
			$column_controls_edit_popup_delete = $controls_start . $controls_edit . $controls_delete . $controls_end;
			$column_controls_edit = $controls_start . $controls_edit . $controls_end;

			$editAccess = vc_user_access_check_shortcode_edit( $this->shortcode );
			$allAccess = vc_user_access_check_shortcode_all( $this->shortcode );

			if ( 'popup_delete' === $controls ) {
				return $allAccess ? $column_controls_popup_delete : '';
			} elseif ( 'edit_popup_delete' === $controls ) {
				return $allAccess ? $column_controls_edit_popup_delete : ( $editAccess ? $column_controls_edit : '' );
			} elseif ( 'size_delete' === $controls ) {
				return $allAccess ? $column_controls_size_delete : '';
			} elseif ( 'add' === $controls ) {
				return $allAccess ? ( $controls_start . $controls_add . $controls_end ) : '';
			} else {
				return $allAccess ? $column_controls_full : ( $editAccess ? $column_controls_edit : '' );
			}
		}

		/**
		 * Return list of controls
		 * @return array
		 */
		public function getControlsList() {
			$editAccess = vc_user_access_check_shortcode_edit( $this->shortcode );
			$allAccess = vc_user_access_check_shortcode_all( $this->shortcode );
			if ( $allAccess ) {
				return apply_filters( 'vc_wpbakery_shortcode_get_controls_list', $this->controls_list, $this->shortcode );
			} else {
				$controls = apply_filters( 'vc_wpbakery_shortcode_get_controls_list', $this->controls_list, $this->shortcode );
				if ( $editAccess ) {
					foreach ( $controls as $key => $value ) {
						if ( 'edit' !== $value && 'add' !== $value ) {
							unset( $controls[ $key ] );
						}
					}

					return $controls;
				} else {
					return in_array( 'add', $controls ) ? array( 'add' ) : array();
				}
			}
		}

		/**
		 * Build new modern controls for shortcode.
		 *
		 * @param string $extended_css
		 *
		 * @return string
		 */
		public function getColumnControlsModular( $extended_css = '' ) {
			ob_start();
			vc_include_template( apply_filters( 'vc_wpbakery_shortcode_get_column_controls_modular_template', $this->controls_template_file ), array(
				'shortcode' => $this->shortcode,
				'position' => $this->controls_css_settings,
				'extended_css' => $extended_css,
				'name' => $this->settings( 'name' ),
				'controls' => $this->getControlsList(),
				'name_css_class' => $this->getBackendEditorControlsElementCssClass(),
				'add_allowed' => $this->getAddAllowed(),
			) );

			return ob_get_clean();
		}

		public function getBackendEditorControlsElementCssClass() {
			$moveAccess = vc_user_access()->part( 'dragndrop' )->checkStateAny( true, null )->get();

			$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) && $moveAccess ? ' vc_element-move' : ' ' . $this->nonDraggableClass );

			return 'vc_control-btn vc_element-name' . $sortable;
		}

		/**
		 * This will fire callbacks if they are defined in map.php
		 *
		 * @param $id
		 *
		 * @return string
		 */
		public function getCallbacks( $id ) {
			$output = '';

			if ( isset( $this->settings['js_callback'] ) ) {
				foreach ( $this->settings['js_callback'] as $text_val => $val ) {
					// TODO: name explain
					$output .= '<input type="hidden" class="wpb_vc_callback wpb_vc_' . $text_val . '_callback " name="' . $text_val . '" value="' . $val . '" />';
				}
			}

			return $output;
		}

		/**
		 * @param $param
		 * @param $value
		 *
		 * vc_filter: vc_wpbakeryshortcode_single_param_html_holder_value - hook to override param value (param type and etc is available in args)
		 *
		 * @return string
		 */
		public function singleParamHtmlHolder( $param, $value ) {
			$value = apply_filters( 'vc_wpbakeryshortcode_single_param_html_holder_value', $value, $param, $this->settings, $this->atts );
			$output = '';
			// Compatibility fixes
			$old_names = array(
				'yellow_message',
				'blue_message',
				'green_message',
				'button_green',
				'button_grey',
				'button_yellow',
				'button_blue',
				'button_red',
				'button_orange',
			);
			$new_names = array(
				'alert-block',
				'alert-info',
				'alert-success',
				'btn-success',
				'btn',
				'btn-info',
				'btn-primary',
				'btn-danger',
				'btn-warning',
			);
			$value = str_ireplace( $old_names, $new_names, $value );
			$param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
			$type = isset( $param['type'] ) ? $param['type'] : '';
			$class = isset( $param['class'] ) ? $param['class'] : '';
			if ( ! empty( $param['holder'] ) ) {
				if ( 'input' === $param['holder'] ) {
					$output .= '<' . $param['holder'] . ' readonly="true" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '">';
				} elseif ( in_array( $param['holder'], array(
					'img',
					'iframe',
				) ) ) {
					$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" src="' . $value . '">';
				} elseif ( 'hidden' !== $param['holder'] ) {
					$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
				}
			}
			if ( ! empty( $param['admin_label'] ) && true === $param['admin_label'] ) {
				$output .= '<span class="vc_admin_label admin_label_' . $param['param_name'] . ( empty( $value ) ? ' hidden-label' : '' ) . '"><label>' . $param['heading'] . '</label>: ' . $value . '</span>';
			}

			return $output;
		}

		/**
		 * @param $params
		 *
		 * @return string
		 */
		protected function getIcon( $params ) {
			$data = '';
			if ( isset( $params['is_container'] ) && true === $params['is_container'] ) {
				$data = ' data-is-container="true"';
			}
			$title = '';
			if ( isset( $params['title'] ) ) {
				$title = 'title="' . $params['title'] . '" ';
			}

			return '<i ' . $title . 'class="vc_general vc_element-icon' . ( ! empty( $params['icon'] ) ? ' ' . sanitize_text_field( $params['icon'] ) : '' ) . '"' . $data . '></i> ';
		}

		/**
		 * @param $title
		 *
		 * @return string
		 */
		protected function outputTitle( $title ) {
			$icon = $this->settings( 'icon' );
			if ( filter_var( $icon, FILTER_VALIDATE_URL ) ) {
				$icon = '';
			}
			$params = array(
				'icon' => $icon,
				'is_container' => $this->settings( 'is_container' ),
			);

			return '<h4 class="wpb_element_title"> ' . $this->getIcon( $params ) . esc_attr( $title ) . '</h4>';
		}

		/**
		 * @param string $content
		 *
		 * @return string
		 */
		public function template( $content = '' ) {
			return $this->contentAdmin( $this->atts, $content );
		}

		/**
		 * This functions prepares attributes to use in template
		 * Converts back escaped characters
		 *
		 * @param $atts
		 *
		 * @return array
		 */
		protected function prepareAtts( $atts ) {
			$returnAttributes = array();
			if ( is_array( $atts ) ) {
				foreach ( $atts as $key => $val ) {
					$returnAttributes[ $key ] = str_replace( array(
						'`{`',
						'`}`',
						'``',
					), array(
						'[',
						']',
						'"',
					), $val );
				}
			}

			return apply_filters( 'vc_shortcode_prepare_atts', $returnAttributes, $this->shortcode, $this->settings );
		}

		/**
		 * @return string
		 */
		public function getShortcode() {
			return $this->shortcode;
		}

		/**
		 * Since 4.5
		 * Possible placeholders:
		 *      {{ content }}
		 *      {{ title }}
		 *      {{ container-class }}
		 *
		 * Possible keys:
		 *  {{
		 *  <%
		 *  %
		 * @since 4.5
		 *
		 * @param $markup
		 * @param string $content
		 *
		 * @return string
		 */
		protected function customMarkup( $markup, $content = '' ) {
			$pattern = '/\{\{([\s\S][^\n]+?)\}\}|<%([\s\S][^\n]+?)%>|%([\s\S][^\n]+?)%/';
			preg_match_all( $pattern, $markup, $matches, PREG_SET_ORDER );
			if ( is_array( $matches ) && ! empty( $matches ) ) {
				foreach ( $matches as $match ) {
					switch ( strtolower( trim( $match[1] ) ) ) {
						// TODO: remove maybe create an wrappers as classes
						case 'content': {
							if ( '' !== $content ) {
								$markup = str_replace( $match[0], $content, $markup );
							} elseif ( isset( $this->settings['default_content_in_template'] ) && '' !== $this->settings['default_content_in_template'] ) {
								$markup = str_replace( $match[0], $this->settings['default_content_in_template'], $markup );
							} else {
								$markup = str_replace( $match[0], '', $markup );
							}
							break;
						}
						case 'title': {
							$markup = str_replace( $match[0], $this->outputTitle( $this->settings['name'] ), $markup );
							break;
						}
						case 'container-class': {
							if ( method_exists( $this, 'containerContentClass' ) ) {
								$markup = str_replace( $match[0], $this->containerContentClass(), $markup );
							} else {
								$markup = str_replace( $match[0], '', $markup );
							}
							break;
						}
						/*
						 * not really work. there is not $atts and no dynamically updated.
						 case 'params': {
							$inner = '';
							if ( isset( $this->settings['params'] ) && is_array( $this->settings['params'] ) ) {
								foreach ( $this->settings['params'] as $param ) {
									$param_value = isset( ${$param['param_name']} ) ? ${$param['param_name']} : '';
									if ( is_array( $param_value ) ) {
										// Get first element from the array
										reset( $param_value );
										$first_key = key( $param_value );
										$param_value = is_null( $first_key ) ? '' : $param_value[ $first_key ];
									}
									$inner .= $this->singleParamHtmlHolder( $param, $param_value );
								}
							}
							$markup = str_replace( $match[0], $inner, $markup );
							break;
						}*/
						case 'editor_controls': {
							$markup = str_replace( $match[0], $this->getColumnControls( $this->settings( 'controls' ) ), $markup );
							break;
						}
						case 'editor_controls_bottom_add': {
							$markup = str_replace( $match[0], $this->getColumnControls( 'add', 'bottom-controls' ), $markup );
							break;
						}
					}
				}
			}

			return do_shortcode( $markup );
		}

		/**
		 * @param $atts
		 *
		 * @return string
		 */
		protected function paramsHtmlHolders( $atts ) {
			$inner = '';
			if ( isset( $this->settings['params'] ) && is_array( $this->settings['params'] ) ) {
				foreach ( $this->settings['params'] as $param ) {
					$param_value = isset( $atts[ $param['param_name'] ] ) ? $atts[ $param['param_name'] ] : '';
					$inner .= $this->singleParamHtmlHolder( $param, $param_value );
				}
			}

			return $inner;
		}

		/**
		 * Check is allowed to add another element inside current element.
		 *
		 * @since 4.8
		 *
		 * @return bool
		 */
		public function getAddAllowed() {
			return true;
		}
	}
}
if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
	/**
	 * Class WPBakeryShortCodesContainer
	 */
	abstract class WPBakeryShortCodesContainer extends WPBakeryShortCode {
		/**
		 * @var array
		 */
		protected $predefined_atts = array();
		protected $backened_editor_prepend_controls = true;

		/**
		 * @return string
		 */
		public function customAdminBlockParams() {
			return '';
		}

		/**
		 * @param $width
		 * @param $i
		 *
		 * @return string
		 */
		public function mainHtmlBlockParams( $width, $i ) {
			$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? 'wpb_sortable' : $this->nonDraggableClass );

			return 'data-element_type="' . $this->settings['base'] . '" class="wpb_' . $this->settings['base'] . ' ' . $sortable . '' . ( ! empty( $this->settings['class'] ) ? ' ' . $this->settings['class'] : '' ) . ' wpb_content_holder vc_shortcodes_container"' . $this->customAdminBlockParams();
		}

		/**
		 * @param $width
		 * @param $i
		 *
		 * @return string
		 */
		public function containerHtmlBlockParams( $width, $i ) {
			return 'class="' . $this->containerContentClass() . '"';
		}

		/**
		 *
		 * @return string
		 */
		public function containerContentClass() {
			return 'wpb_column_container vc_container_for_children vc_clearfix';
		}

		/**
		 * @param $controls
		 * @param string $extended_css
		 *
		 * @return string
		 */
		public function getColumnControls( $controls = 'full', $extended_css = '' ) {
			$controls_start = '<div class="vc_controls vc_controls-visible controls_column' . ( ! empty( $extended_css ) ? " {$extended_css}" : '' ) . '">';
			$controls_end = '</div>';

			if ( 'bottom-controls' === $extended_css ) {
				$control_title = sprintf( __( 'Append to this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) );
			} else {
				$control_title = sprintf( __( 'Prepend to this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) );
			}

			$controls_move = '<a class="vc_control column_move vc_column-move" data-vc-control="move" href="#" title="' . sprintf( __( 'Move this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc-composer-icon vc-c-icon-dragndrop"></i></a>';
			$moveAccess = vc_user_access()->part( 'dragndrop' )->checkStateAny( true, null )->get();
			if ( ! $moveAccess ) {
				$controls_move = '';
			}
			$controls_add = '<a class="vc_control column_add" data-vc-control="add" href="#" title="' . $control_title . '"><i class="vc-composer-icon vc-c-icon-add"></i></a>';
			$controls_edit = '<a class="vc_control column_edit" data-vc-control="edit" href="#" title="' . sprintf( __( 'Edit this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc-composer-icon vc-c-icon-mode_edit"></i></a>';
			$controls_clone = '<a class="vc_control column_clone" data-vc-control="clone" href="#" title="' . sprintf( __( 'Clone this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc-composer-icon vc-c-icon-content_copy"></i></a>';
			$controls_delete = '<a class="vc_control column_delete" data-vc-control="delete" href="#" title="' . sprintf( __( 'Delete this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc-composer-icon vc-c-icon-delete_empty"></i></a>';
			$controls_full = $controls_move . $controls_add . $controls_edit . $controls_clone . $controls_delete;

			$editAccess = vc_user_access_check_shortcode_edit( $this->shortcode );
			$allAccess = vc_user_access_check_shortcode_all( $this->shortcode );

			if ( ! empty( $controls ) ) {
				if ( is_string( $controls ) ) {
					$controls = array( $controls );
				}
				$controls_string = $controls_start;
				foreach ( $controls as $control ) {
					$control_var = 'controls_' . $control;
					if ( ( $editAccess && 'edit' == $control ) || $allAccess ) {
						if ( isset( ${$control_var} ) ) {
							$controls_string .= ${$control_var};
						}
					}
				}

				return $controls_string . $controls_end;
			}

			if ( $allAccess ) {
				return $controls_start . $controls_full . $controls_end;
			} elseif ( $editAccess ) {
				return $controls_start . $controls_edit . $controls_end;
			}

			return $controls_start . $controls_end;
		}

		/**
		 * @param $atts
		 * @param null $content
		 *
		 * @return string
		 */
		public function contentAdmin( $atts, $content = null ) {
			$width = '';

			$atts = shortcode_atts( $this->predefined_atts, $atts );
			extract( $atts );
			$this->atts = $atts;
			$output = '';

			$output .= '<div ' . $this->mainHtmlBlockParams( $width, 1 ) . '>';
			if ( $this->backened_editor_prepend_controls ) {
				$output .= $this->getColumnControls( $this->settings( 'controls' ) );
			}
			$output .= '<div class="wpb_element_wrapper">';

			if ( isset( $this->settings['custom_markup'] ) && '' !== $this->settings['custom_markup'] ) {
				$markup = $this->settings['custom_markup'];
				$output .= $this->customMarkup( $markup );
			} else {
				$output .= $this->outputTitle( $this->settings['name'] );
				$output .= '<div ' . $this->containerHtmlBlockParams( $width, 1 ) . '>';
				$output .= do_shortcode( shortcode_unautop( $content ) );
				$output .= '</div>';
				$output .= $this->paramsHtmlHolders( $atts );
			}

			$output .= '</div>';
			if ( $this->backened_editor_prepend_controls ) {
				$output .= $this->getColumnControls( 'add', 'bottom-controls' );

			}
			$output .= '</div>';

			return $output;
		}

		/**
		 * @param $title
		 *
		 * @return string
		 */
		protected function outputTitle( $title ) {
			$icon = $this->settings( 'icon' );
			if ( filter_var( $icon, FILTER_VALIDATE_URL ) ) {
				$icon = '';
			}
			$params = array(
				'icon' => $icon,
				'is_container' => $this->settings( 'is_container' ),
				'title' => $title,
			);

			return '<h4 class="wpb_element_title"> ' . $this->getIcon( $params ) . '</h4>';
		}

		public function getBackendEditorChildControlsElementCssClass() {
			return 'vc_element-name';
		}
	}
}
if ( ! class_exists( 'WPBakeryShortCodeFishBones' ) ) {
	/**
	 * Class WPBakeryShortCodeFishBones
	 */
	class WPBakeryShortCodeFishBones extends WPBakeryShortCode {
		/**
		 * @var bool
		 */
		protected $shortcode_class = false;

		/**
		 * @param $settings
		 */
		public function __construct( $settings ) {
			if ( ! $settings ) {
				throw new Exception( 'Element must have settings to register' );
			}
			$this->settings = $settings;
			$this->shortcode = $this->settings['base'];
			$this->addAction( 'admin_init', 'hookAdmin' );
			if ( ! shortcode_exists( $this->shortcode ) ) {
				add_shortcode( $this->shortcode, array(
					$this,
					'render',
				) );
			}
		}

		public function hookAdmin() {
			$this->enqueueAssets();
			$this->addAction( 'admin_init', 'enqueueAssets' );
			if ( vc_is_page_editable() ) {
				// fix for page editable
				$this->addAction( 'wp_head', 'printIconStyles' );
			}

			$this->addAction( 'admin_head', 'printIconStyles' ); // fe+be
			$this->addAction( 'admin_print_scripts-post.php', 'enqueueAssets' );
			$this->addAction( 'admin_print_scripts-post-new.php', 'enqueueAssets' );
		}

		/**
		 * @return WPBakeryShortCodeFishBones
		 */
		public function shortcodeClass() {
			// _deprecated_function( '\WPBakeryShortCodeFishBones::shortcodeClass', '4.9 (will be removed in 4.11)' );
			if ( false !== $this->shortcode_class ) {
				return $this->shortcode_class;
			}

			require_once vc_path_dir( 'SHORTCODES_DIR', 'wordpress-widgets.php' );

			$class_name = $this->settings( 'php_class_name' ) ? $this->settings( 'php_class_name' ) : 'WPBakeryShortCode_' . $this->settings( 'base' );

			$autoloaded_dependencies = VcShortcodeAutoloader::getInstance()->includeClass( $class_name );

			if ( ! $autoloaded_dependencies ) {
				$file = vc_path_dir( 'SHORTCODES_DIR', str_replace( '_', '-', $this->settings( 'base' ) ) . '.php' );
				if ( is_file( $file ) ) {
					require_once( $file );
				}
			}

			if ( class_exists( $class_name ) && is_subclass_of( $class_name, 'WPBakeryShortCode' ) ) {
				$this->shortcode_class = new $class_name( $this->settings );
			} else {
				$this->shortcode_class = new WPBakeryShortCodeFishBones( $this->settings );
			}

			return $this->shortcode_class;
		}

		/**
		 *
		 *
		 * @since 4.9
		 *
		 * @param $tag
		 *
		 * @return \WPBakeryShortCodeFishBones
		 * @throws \Exception
		 */
		public static function getElementClass( $tag ) {
			$settings = WPBMap::getShortCode( $tag );
			if ( empty( $settings ) ) {
				throw new Exception( 'Element must be mapped in system' );
			}
			require_once vc_path_dir( 'SHORTCODES_DIR', 'wordpress-widgets.php' );

			$class_name = ! empty( $settings['php_class_name'] ) ? $settings['php_class_name'] : 'WPBakeryShortCode_' . $settings['base'];

			$autoloaded_dependencies = VcShortcodeAutoloader::getInstance()->includeClass( $class_name );

			if ( ! $autoloaded_dependencies ) {
				$file = vc_path_dir( 'SHORTCODES_DIR', str_replace( '_', '-', $settings['base'] ) . '.php' );
				if ( is_file( $file ) ) {
					require_once( $file );
				}
			}

			if ( class_exists( $class_name ) && is_subclass_of( $class_name, 'WPBakeryShortCode' ) ) {
				$shortcode_class = new $class_name( $settings );
			} else {
				$shortcode_class = new WPBakeryShortCodeFishBones( $settings );
			}

			return $shortcode_class;
		}

		/**
		 * @param $atts
		 * @param null $content
		 * @param null $tag
		 *
		 * @return string
		 */
		public function render( $atts, $content = null, $tag = null ) {

			return $this->getElementClass( $tag )->output( $atts, $content );
		}

		/**
		 * This method is not used
		 *
		 * @param $atts
		 * @param null $content
		 *
		 * @return string
		 */
		protected function content( $atts, $content = null ) {
			return '';
		}

		/**
		 * @param string $content
		 *
		 * @return string
		 */
		public function template( $content = '' ) {
			return $this->shortcodeClass()->contentAdmin( array(), $content );
		}
	}
}

/**
 * @since 4.9
 *
 * Class Vc_Shortcodes_Manager
 */
final class Vc_Shortcodes_Manager {
	private $shortcode_classes = array(
		'default' => array(),
	);

	private $tag;
	/**
	 * Core singleton class
	 * @var self - pattern realization
	 */
	private static $_instance;

	/**
	 * Get the instance of Vc_Shortcodes_Manager
	 *
	 * @return self
	 */
	public static function getInstance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function getTag() {
		return $this->tag;
	}

	public function setTag( $tag ) {
		$this->tag = $tag;

		return $this;
	}

	public function getElementClass( $tag ) {
		$currentScope = WPBMap::getScope();
		if ( isset( $this->shortcode_classes[ $currentScope ], $this->shortcode_classes[ $currentScope ][ $tag ] ) ) {
			return $this->shortcode_classes[ $currentScope ][ $tag ];
		}
		if ( ! isset( $this->shortcode_classes[ $currentScope ] ) ) {
			$this->shortcode_classes[ $currentScope ] = array();
		}
		$settings = WPBMap::getShortCode( $tag );
		if ( empty( $settings ) ) {
			throw new Exception( 'Element must be mapped in system' );
		}
		require_once vc_path_dir( 'SHORTCODES_DIR', 'wordpress-widgets.php' );

		$class_name = ! empty( $settings['php_class_name'] ) ? $settings['php_class_name'] : 'WPBakeryShortCode_' . $settings['base'];

		$autoloaded_dependencies = VcShortcodeAutoloader::getInstance()->includeClass( $class_name );

		if ( ! $autoloaded_dependencies ) {
			$file = vc_path_dir( 'SHORTCODES_DIR', str_replace( '_', '-', $settings['base'] ) . '.php' );
			if ( is_file( $file ) ) {
				require_once( $file );
			}
		}

		if ( class_exists( $class_name ) && is_subclass_of( $class_name, 'WPBakeryShortCode' ) ) {
			$shortcode_class = new $class_name( $settings );
		} else {
			$shortcode_class = new WPBakeryShortCodeFishBones( $settings );
		}
		$this->shortcode_classes[ $currentScope ][ $tag ] = $shortcode_class;

		return $shortcode_class;
	}

	public function shortcodeClass() {
		return $this->getElementClass( $this->tag );
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	public function template( $content = '' ) {
		return $this->getElementClass( $this->tag )->contentAdmin( array(), $content );
	}

	/**
	 * @param $name
	 *
	 * @return null
	 */
	public function settings( $name ) {
		$settings = WPBMap::getShortCode( $this->tag );

		return isset( $settings[ $name ] ) ? $settings[ $name ] : null;
	}

	/**
	 * @param $atts
	 * @param null $content
	 * @param null $tag
	 *
	 * @return string
	 */
	public function render( $atts, $content = null, $tag = null ) {

		return $this->getElementClass( $this->tag )->output( $atts, $content );
	}

	public function buildShortcodesAssets() {
		$elements = WPBMap::getAllShortCodes();
		foreach ( $elements as $tag => $settings ) {
			$element_class = $this->getElementClass( $tag );
			$element_class->enqueueAssets();
			$element_class->printIconStyles();
		}
	}

	public function buildShortcodesAssetsForEditable() {
		$elements = WPBMap::getAllShortCodes(); // @todo create pull to use only where it is set inside function. BC problem
		foreach ( $elements as $tag => $settings ) {
			$element_class = $this->getElementClass( $tag );
			$element_class->printIconStyles();
		}
	}

	public function isShortcodeClassInitialized( $tag ) {
		$currentScope = WPBMap::getScope();

		return isset( $this->shortcode_classes[ $currentScope ], $this->shortcode_classes[ $currentScope ][ $tag ] );
	}

	public function unsetElementClass( $tag ) {
		$currentScope = WPBMap::getScope();
		unset( $this->shortcode_classes[ $currentScope ][ $tag ] );

		return true;
	}
}
