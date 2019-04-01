<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Renders navigation bar for Editors.
 */
class Vc_Navbar implements Vc_Render {
	/**
	 * @var array
	 */
	protected $controls = array(
		'add_element',
		'templates',
		'save_backend',
		'preview',
		'frontend',
		'custom_css',
		'fullscreen',
		'windowed',
	);
	/**
	 * @var string
	 */
	protected $brand_url = 'https://wpbakery.com/?utm_campaign=VCplugin&utm_source=vc_user&utm_medium=backend_editor';
	/**
	 * @var string
	 */
	protected $css_class = 'vc_navbar';
	/**
	 * @var string
	 */
	protected $controls_filter_name = 'vc_nav_controls';
	/**
	 * @var bool|WP_Post
	 */
	protected $post = false;

	/**
	 * @param WP_Post $post
	 */
	public function __construct( WP_Post $post ) {
		$this->post = $post;
	}

	/**
	 * Generate array of controls by iterating property $controls list.
	 * vc_filter: vc_nav_controls - hook to override list of controls
	 * @return array - list of arrays witch contains key name and html output for button.
	 */
	public function getControls() {
		$list = array();
		foreach ( $this->controls as $control ) {
			$method = vc_camel_case( 'get_control_' . $control );
			if ( method_exists( $this, $method ) ) {
				$list[] = array( $control, $this->$method() . "\n" );
			}
		}

		return apply_filters( $this->controls_filter_name, $list );
	}

	/**
	 * Get current post.
	 * @return null|WP_Post
	 */
	public function post() {
		if ( $this->post ) {
			return $this->post;
		}

		return get_post();
	}

	/**
	 * Render template.
	 */
	public function render() {
		vc_include_template( 'editors/navbar/navbar.tpl.php', array(
			'css_class' => $this->css_class,
			'controls' => $this->getControls(),
			'nav_bar' => $this,
			'post' => $this->post(),
		) );
	}

	/**
	 * vc_filter: vc_nav_front_logo - hook to override WPBakery Page Builder logo
	 * @return mixed|void
	 */
	public function getLogo() {
		$output = '<a id="vc_logo" class="vc_navbar-brand" title="' . __( 'WPBakery Page Builder', 'js_composer' )
		          . '" href="' . esc_attr( $this->brand_url ) . '" target="_blank">'
		          . __( 'WPBakery Page Builder', 'js_composer' ) . '</a>';

		return apply_filters( 'vc_nav_front_logo', $output );
	}

	/**
	 * @return string
	 */
	public function getControlCustomCss() {
		if ( ! vc_user_access()->part( 'post_settings' )->can()->get() ) {
			return '';
		}

		return '<li class="vc_pull-right"><a id="vc_post-settings-button" href="javascript:;" class="vc_icon-btn vc_post-settings" title="'
		       . __( 'Page settings', 'js_composer' ) . '">'
		       . '<span id="vc_post-css-badge" class="vc_badge vc_badge-custom-css" style="display: none;">' . __( 'CSS', 'js_composer' )
			   . '</span><i class="vc-composer-icon vc-c-icon-cog"></i></a>'
		       . '</li>';
	}

	/**
	 * @return string
	 */
	public function getControlFullscreen() {
		return '<li class="vc_show-mobile vc_pull-right">'
		       . '<a id="vc_fullscreen-button" class="vc_icon-btn vc_fullscreen-button" title="'. __( 'Full screen', 'js_composer' ) . '"><i class="vc-composer-icon vc-c-icon-fullscreen"></i></a>'
		       . '</li>';
	}

	/**
	 * @return string
	 */
	public function getControlWindowed() {
		return '<li class="vc_show-mobile vc_pull-right">'
		       . '<a id="vc_windowed-button" class="vc_icon-btn vc_windowed-button" title="'. __( 'Exit full screen', 'js_composer' ) . '"><i class="vc-composer-icon vc-c-icon-fullscreen_exit"></i></a>'
		       . '</li>';
	}

	/**
	 * @return string
	 */
	public function getControlAddElement() {
		if ( vc_user_access()
			     ->part( 'shortcodes' )
			     ->checkStateAny( true, 'custom', null )
			     ->get() &&
		     vc_user_access_check_shortcode_all( 'vc_row' ) && vc_user_access_check_shortcode_all( 'vc_column' )
		) {
			return '<li class="vc_show-mobile">'
			       . '	<a href="javascript:;" class="vc_icon-btn vc_element-button" data-model-id="vc_element" id="vc_add-new-element" title="'
			       . '' . __( 'Add new element', 'js_composer' ) . '">'
				   . '    <i class="vc-composer-icon vc-c-icon-add_element"></i>'
			       . '	</a>'
			       . '</li>';
		}

		return '';
	}

	/**
	 * @return string
	 */
	public function getControlTemplates() {
		if ( ! vc_user_access()->part( 'templates' )->can()->get() ) {
			return '';
		}

		return '<li><a href="javascript:;" class="vc_icon-btn vc_templates-button"  id="vc_templates-editor-button" title="'
		       . __( 'Templates', 'js_composer' ) . '"><i class="vc-composer-icon vc-c-icon-add_template"></i></a></li>';
	}

	/**
	 * @return string
	 */
	public function getControlFrontend() {
		if ( ! vc_enabled_frontend() ) {
			return '';
		}

		return '<li class="vc_pull-right" style="display: none;">'
		       . '<a href="' . vc_frontend_editor()->getInlineUrl() . '" class="vc_btn vc_btn-primary vc_btn-sm vc_navbar-btn" id="wpb-edit-inline">' . __( 'Frontend', 'js_composer' ) . '</a>'
		       . '</li>';
	}

	/**
	 * @return string
	 */
	public function getControlPreview() {
		return '';
	}

	/**
	 * @return string
	 */
	public function getControlSaveBackend() {
		return '<li class="vc_pull-right vc_save-backend">'
		       . '<a href="javascript:;" class="vc_btn vc_btn-grey vc_btn-sm vc_navbar-btn vc_control-preview">' . __( 'Preview', 'js_composer' ) . '</a>'
		       . '<a class="vc_btn vc_btn-sm vc_navbar-btn vc_btn-primary vc_control-save" id="wpb-save-post">' . __( 'Update', 'js_composer' ) . '</a>'
		       . '</li>';
	}
}
