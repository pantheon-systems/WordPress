<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery Page Builder basic class.
 * @since 4.2
 */
class Vc_Base {
	/**
	 * Shortcode's edit form.
	 *
	 * @since  4.2
	 * @access protected
	 * @var bool|Vc_Shortcode_Edit_Form
	 */
	protected $shortcode_edit_form = false;

	/**
	 * Templates management panel editor.
	 * @since  4.4
	 * @access protected
	 * @var bool|Vc_Templates_Panel_Editor
	 */
	protected $templates_panel_editor = false;
	/**
	 * Presets management panel editor.
	 * @since  5.2
	 * @access protected
	 * @var bool|Vc_Preset_Panel_Editor
	 */
	protected $preset_panel_editor = false;
	/**
	 * Post object for VC in Admin.
	 *
	 * @since  4.4
	 * @access protected
	 * @var bool|Vc_Post_Admin
	 */
	protected $post_admin = false;
	/**
	 * Post object for VC.
	 *
	 * @since  4.4.3
	 * @access protected
	 * @var bool|Vc_Post_Admin
	 */
	protected $post = false;
	/**
	 * List of shortcodes map to VC.
	 *
	 * @since  4.2
	 * @access public
	 * @var array WPBakeryShortCodeFishBones
	 */
	protected $shortcodes = array();

	/** @var  Vc_Shared_Templates */
	public $shared_templates;

	/**
	 * Load default object like shortcode parsing.
	 *
	 * @since  4.2
	 * @access public
	 */
	public function init() {
		do_action( 'vc_before_init_base' );
		if ( is_admin() ) {
			$this->postAdmin()->init();
		}
		add_filter( 'body_class', array(
			$this,
			'bodyClass',
		) );
		add_filter( 'the_excerpt', array(
			$this,
			'excerptFilter',
		) );
		add_action( 'wp_head', array(
			$this,
			'addMetaData',
		) );
		add_action( 'wp_head', array(
			$this,
			'addIEMinimalSupport',
		) );
		if ( is_admin() ) {
			$this->initAdmin();
		} else {
			$this->initPage();
		}
		do_action( 'vc_after_init_base' );
	}

	/**
	 * Post object for interacting with Current post data.
	 * @since 4.4
	 * @return Vc_Post_Admin
	 */
	public function postAdmin() {
		if ( false === $this->post_admin ) {
			require_once vc_path_dir( 'CORE_DIR', 'class-vc-post-admin.php' );
			$this->post_admin = new Vc_Post_Admin();
		}

		return $this->post_admin;
	}

	/**
	 * Build VC for frontend pages.
	 *
	 * @since  4.2
	 * @access public
	 */
	public function initPage() {
		do_action( 'vc_build_page' );
		add_action( 'template_redirect', array(
			$this,
			'frontCss',
		) );
		add_action( 'template_redirect', array(
			'WPBMap',
			'addAllMappedShortcodes',
		) );
		add_action( 'wp_head', array(
			$this,
			'addFrontCss',
		), 1000 );
		add_action( 'wp_head', array(
			$this,
			'addNoScript',
		), 1000 );
		add_action( 'template_redirect', array(
			$this,
			'frontJsRegister',
		) );
		add_filter( 'the_content', array(
			$this,
			'fixPContent',
		), 11 );
	}

	/**
	 * Load admin required modules and elements
	 *
	 * @since  4.2
	 * @access public
	 */
	public function initAdmin() {
		do_action( 'vc_build_admin_page' );
		// Build settings for admin page;
		//$this->registerAdminJavascript();
		//$this->registerAdminCss();

		// editors actions:
		$this->editForm()->init();
		$this->templatesPanelEditor()->init();
		/* nectar addition
		$this->shared_templates->init();
		*/
		// ajax params/shortcode action
		add_action( 'wp_ajax_wpb_single_image_src', array(
			$this,
			'singleImageSrc',
		) ); // @todo move it
		add_action( 'wp_ajax_wpb_gallery_html', array(
			$this,
			'galleryHTML',
		) ); // @todo move it

		// plugins list page actions links
		add_filter( 'plugin_action_links', array(
			$this,
			'pluginActionLinks',
		), 10, 2 );
	}

	/**
	 * Setter for edit form.
	 * @since 4.2
	 *
	 * @param Vc_Shortcode_Edit_Form $form
	 */
	public function setEditForm( Vc_Shortcode_Edit_Form $form ) {
		$this->shortcode_edit_form = $form;
	}

	/**
	 * Get Shortcodes Edit form object.
	 *
	 * @see    Vc_Shortcode_Edit_Form::__construct
	 * @since  4.2
	 * @access public
	 * @return Vc_Shortcode_Edit_Form
	 */
	public function editForm() {
		return $this->shortcode_edit_form;
	}

	/**
	 * Setter for Templates editor.
	 * @since 4.4
	 *
	 * @param Vc_Templates_Panel_Editor $editor
	 */
	public function setTemplatesPanelEditor( Vc_Templates_Panel_Editor $editor ) {
		$this->templates_panel_editor = $editor;
	}

	/**
	 * Setter for Preset editor.
	 * @since 5.2
	 *
	 * @param Vc_Preset_Panel_Editor $editor
	 */
	public function setPresetPanelEditor( Vc_Preset_Panel_Editor $editor ) {
		$this->preset_panel_editor = $editor;
	}

	/**
	 * Get templates manager.
	 * @see    Vc_Templates_Panel_Editor::__construct
	 * @since  4.4
	 * @access public
	 * @return bool|Vc_Templates_Panel_Editor
	 */
	public function templatesPanelEditor() {
		return $this->templates_panel_editor;
	}

	/**
	 * Get preset manager.
	 * @see    Vc_Preset_Panel_Editor::__construct
	 * @since  5.2
	 * @access public
	 * @return bool|Vc_Preset_Panel_Editor
	 */
	public function presetPanelEditor() {
		return $this->preset_panel_editor;
	}

	/**
	 * Get shortcode class instance.
	 *
	 * @see    WPBakeryShortCodeFishBones
	 * @since  4.2
	 * @access public
	 *
	 * @param string $tag
	 *
	 * @return Vc_Shortcodes_Manager|null
	 */
	public function getShortCode( $tag ) {
		return Vc_Shortcodes_Manager::getInstance()->setTag( $tag );
	}

	/**
	 * Remove shortcode from shortcodes list of VC.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param $tag - shortcode tag
	 */
	public function removeShortCode( $tag ) {
		remove_shortcode( $tag );
	}

	/**
	 * @todo move it
	 * @since 4.2
	 */
	public function singleImageSrc() {
		// @todo again, this method should be moved (comment added on 4.8)
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();

		$image_id = (int) vc_post_param( 'content' );
		$params = vc_post_param( 'params' );
		$post_id = vc_post_param( 'post_id' );
		$img_size = vc_post_param( 'size' );
		$img = '';

		if ( ! empty( $params['source'] ) ) {
			$source = $params['source'];
		} else {
			$source = 'media_library';
		}

		switch ( $source ) {
			case 'media_library':
			case 'featured_image':

				if ( 'featured_image' === $source ) {
					if ( $post_id && has_post_thumbnail( $post_id ) ) {
						$img_id = get_post_thumbnail_id( $post_id );
					} else {
						$img_id = 0;
					}
				} else {
					$img_id = preg_replace( '/[^\d]/', '', $image_id );
				}

				if ( ! $img_size ) {
					$img_size = 'thumbnail';
				}

				if ( $img_id ) {
					$img = wp_get_attachment_image_src( $img_id, $img_size );
					if ( $img ) {
						$img = $img[0];
					}
				}

				break;

			case 'external_link':
				if ( ! empty( $params['custom_src'] ) ) {
					$img = $params['custom_src'];
				}
				break;
		}

		die( $img );
	}

	/**
	 * @todo move it
	 * @since 4.2
	 */
	public function galleryHTML() {
		// @todo again, this method should be moved (comment added on 4.8)
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();

		$images = vc_post_param( 'content' );
		if ( ! empty( $images ) ) {
			echo fieldAttachedImages( explode( ',', $images ) );
		}
		die();
	}

	/**
	 * Set or modify new settings for shortcode.
	 *
	 * This function widely used by WPBMap class methods to modify shortcodes mapping
	 *
	 * @since 4.3
	 *
	 * @param $tag
	 * @param $name
	 * @param $value
	 */
	public function updateShortcodeSetting( $tag, $name, $value ) {
		Vc_Shortcodes_Manager::getInstance()->getElementClass( $tag )->setSettings( $name, $value );
	}

	/**
	 * Build custom css styles for page from shortcodes attributes created by VC editors.
	 *
	 * Called by save method, which is hooked by edit_post action.
	 * Function creates meta data for post with the key '_wpb_shortcodes_custom_css'
	 * and value as css string, which will be added to the footer of the page.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param $post_id
	 */
	public function buildShortcodesCustomCss( $post_id ) {
		$post = get_post( $post_id );
		/**
		 * vc_filter: vc_base_build_shortcodes_custom_css
		 * @since 4.4
		 */
		 
		 /* nectar addition */ 
 		$portfolio_extra_content = (isset($post->ID)) ? get_post_meta($post->ID, '_nectar_portfolio_extra_content', true) : '';
 		
 		if(!empty($portfolio_extra_content)) {
 			$css = apply_filters( 'vc_base_build_shortcodes_custom_css', $this->parseShortcodesCustomCss( $portfolio_extra_content ) );
 		} else {
 			$css = apply_filters( 'vc_base_build_shortcodes_custom_css', $this->parseShortcodesCustomCss( $post->post_content ) );
 		}
 		/* nectar addition end */ 
		
		if ( empty( $css ) ) {
			delete_post_meta( $post_id, '_wpb_shortcodes_custom_css' );
		} else {
			update_post_meta( $post_id, '_wpb_shortcodes_custom_css', $css );
		}
	}

	/**
	 * Parse shortcodes custom css string.
	 *
	 * This function is used by self::buildShortcodesCustomCss and creates css string from shortcodes attributes
	 * like 'css_editor'.
	 *
	 * @see    WPBakeryVisualComposerCssEditor
	 * @since  4.2
	 * @access public
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function parseShortcodesCustomCss( $content ) {
		$css = '';
		if ( ! preg_match( '/\s*(\.[^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $content ) ) {
			return $css;
		}
		WPBMap::addAllMappedShortcodes();
		preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes );
		foreach ( $shortcodes[2] as $index => $tag ) {
			$shortcode = WPBMap::getShortCode( $tag );
			$attr_array = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
			if ( isset( $shortcode['params'] ) && ! empty( $shortcode['params'] ) ) {
				foreach ( $shortcode['params'] as $param ) {
					if ( isset( $param['type'] ) && 'css_editor' === $param['type'] && isset( $attr_array[ $param['param_name'] ] ) ) {
						$css .= $attr_array[ $param['param_name'] ];
					}
				}
			}
		}
		foreach ( $shortcodes[5] as $shortcode_content ) {
			$css .= $this->parseShortcodesCustomCss( $shortcode_content );
		}

		return $css;
	}

	/**
	 * Hooked class method by wp_head WP action to output post custom css.
	 *
	 * Method gets post meta value for page by key '_wpb_post_custom_css' and if it is not empty
	 * outputs css string wrapped into style tag.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param int $id
	 */
	public function addPageCustomCss( $id = null ) {
		if ( is_front_page() || is_home() ) {
			$id = get_queried_object_id();
		} else if ( is_singular() ) {
			if ( ! $id ) {
				$id = get_the_ID();
			}
		}

		if ( $id ) {
			$post_custom_css = get_post_meta( $id, '_wpb_post_custom_css', true );
			if ( ! empty( $post_custom_css ) ) {
				$post_custom_css = strip_tags( $post_custom_css );
				echo '<style type="text/css" data-type="vc_custom-css">';
				echo $post_custom_css;
				echo '</style>';
			}
		}
	}

	/**
	 * Hooked class method by wp_footer WP action to output shortcodes css editor settings from page meta data.
	 *
	 * Method gets post meta value for page by key '_wpb_shortcodes_custom_css' and if it is not empty
	 * outputs css string wrapped into style tag.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param int $id
	 *
	 */
	public function addShortcodesCustomCss( $id = null ) {
		if ( ! is_singular() ) {
			return;
		}
		if ( ! $id ) {
			$id = get_the_ID();
		}

		if ( $id ) {
			$shortcodes_custom_css = get_post_meta( $id, '_wpb_shortcodes_custom_css', true );
			if ( ! empty( $shortcodes_custom_css ) ) {
				$shortcodes_custom_css = strip_tags( $shortcodes_custom_css );
				echo '<style type="text/css" data-type="vc_shortcodes-custom-css">';
				echo $shortcodes_custom_css;
				echo '</style>';
			}
		}
	}

	/**
	 * Add css styles for current page and elements design options added w\ editor.
	 */
	public function addFrontCss() {
		$this->addPageCustomCss();
		$this->addShortcodesCustomCss();
	}

	public function addNoScript() {
		echo '<noscript>';
		echo '<style type="text/css">';
		echo ' .wpb_animate_when_almost_visible { opacity: 1; }';
		echo '</style>';
		echo '</noscript>';
	}

	/**
	 * Register front css styles.
	 *
	 * Calls wp_register_style for required css libraries files.
	 *
	 * @since  3.1
	 * @access public
	 */
	public function frontCss() {
		wp_register_style( 'flexslider', vc_asset_url( 'lib/bower/flexslider/flexslider.min.css' ), array(), WPB_VC_VERSION );
		wp_register_style( 'nivo-slider-css', vc_asset_url( 'lib/bower/nivoslider/nivo-slider.min.css' ), array(), WPB_VC_VERSION );
		wp_register_style( 'nivo-slider-theme', vc_asset_url( 'lib/bower/nivoslider/themes/default/default.min.css' ), array( 'nivo-slider-css' ), WPB_VC_VERSION );
		wp_register_style( 'prettyphoto', vc_asset_url( 'lib/prettyphoto/css/prettyPhoto.min.css' ), array(), WPB_VC_VERSION );
		wp_register_style( 'isotope-css', vc_asset_url( 'css/lib/isotope.min.css' ), array(), WPB_VC_VERSION );
		/* nectar addition */  
		//wp_register_style( 'font-awesome', vc_asset_url( 'lib/bower/font-awesome/css/font-awesome.min.css' ), array(), WPB_VC_VERSION );
		/* nectar addition end */
		wp_register_style( 'animate-css', vc_asset_url( 'lib/bower/animate-css/animate.min.css' ), array(), WPB_VC_VERSION );

		$front_css_file = vc_asset_url( 'css/js_composer.min.css' );
		$upload_dir = wp_upload_dir();
		$vc_upload_dir = vc_upload_dir();
		/* nectar addition
		if ( '1' === vc_settings()->get( 'use_custom' ) && is_file( $upload_dir['basedir'] . '/' . $vc_upload_dir . '/js_composer_front_custom.css' ) ) {
			$front_css_file = $upload_dir['baseurl'] . '/' . $vc_upload_dir . '/js_composer_front_custom.css';
			$front_css_file = vc_str_remove_protocol( $front_css_file );
		} nectar addition end */
		wp_register_style( 'js_composer_front', $front_css_file, array(), WPB_VC_VERSION );

		$custom_css_path = $upload_dir['basedir'] . '/' . $vc_upload_dir . '/custom.css';
		if ( is_file( $upload_dir['basedir'] . '/' . $vc_upload_dir . '/custom.css' ) && filesize( $custom_css_path ) > 0 ) {
			$custom_css_url = $upload_dir['baseurl'] . '/' . $vc_upload_dir . '/custom.css';
			$custom_css_url = vc_str_remove_protocol( $custom_css_url );
			wp_register_style( 'js_composer_custom_css', $custom_css_url, array(), WPB_VC_VERSION );
		}
		add_action( 'wp_enqueue_scripts', array(
			$this,
			'enqueueStyle',
		) );

		/**
		 * @since 4.4
		 */
		do_action( 'vc_base_register_front_css' );
	}

	/**
	 * Enqueue base css class for VC elements and enqueue custom css if exists.
	 */
	public function enqueueStyle() {
		$post = get_post();
		if ( $post && strpos( $post->post_content, '[vc_row' ) !== false ) {
			wp_enqueue_style( 'js_composer_front' );
		}
		wp_enqueue_style( 'js_composer_custom_css' );
	}

	/**
	 * Register front javascript libs.
	 *
	 * Calls wp_register_script for required css libraries files.
	 *
	 * @since  3.1
	 * @access public
	 */
	public function frontJsRegister() {
		wp_register_script( 'prettyphoto', vc_asset_url( 'lib/prettyphoto/js/jquery.prettyPhoto.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'waypoints', vc_asset_url( 'lib/waypoints/waypoints.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );

		// @deprecated used in old tabs
		wp_register_script( 'jquery_ui_tabs_rotate', vc_asset_url( 'lib/bower/jquery-ui-tabs-rotate/jquery-ui-tabs-rotate.min.js' ), array(
			'jquery',
			'jquery-ui-tabs',
		), WPB_VC_VERSION, true );

		// used in vc_gallery, old grid
		/* nectar addition */
		/*
		wp_register_script( 'isotope', vc_asset_url( 'lib/bower/isotope/dist/isotope.pkgd.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		*/

		wp_register_script( 'twbs-pagination', vc_asset_url( 'lib/bower/twbs-pagination/jquery.twbsPagination.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'nivo-slider', vc_asset_url( 'lib/bower/nivoslider/jquery.nivo.slider.pack.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'flexslider', vc_asset_url( 'lib/bower/flexslider/jquery.flexslider-min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'wpb_composer_front_js', vc_asset_url( 'js/dist/js_composer_front.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );

		/**
		 * @since 4.4
		 */
		do_action( 'vc_base_register_front_js' );
	}

	/**
	 * Register admin javascript libs.
	 *
	 * Calls wp_register_script for required css libraries files for Admin dashboard.
	 *
	 * @since  3.1
	 * vc_filter: vc_i18n_locale_composer_js_view, since 4.4 - override localization for js
	 * @access public
	 */
	public function registerAdminJavascript() {
		/**
		 * @since 4.4
		 */
		do_action( 'vc_base_register_admin_js' );

	}

	/**
	 * Register admin css styles.
	 *
	 * Calls wp_register_style for required css libraries files for admin dashboard.
	 *
	 * @since  3.1
	 * @access public
	 */
	public function registerAdminCss() {
		/**
		 * @since 4.4
		 */
		do_action( 'vc_base_register_admin_css' );
	}

	/**
	 * Add Settings link in plugin's page
	 * @since 4.2
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return array
	 */
	public function pluginActionLinks( $links, $file ) {
		if ( plugin_basename( vc_path_dir( 'APP_DIR', '/js_composer.php' ) ) == $file ) {
			$title = __( 'WPBakery Page Builder Settings', 'js_composer' );
			$html = esc_html__( 'Settings', 'js_composer' );
			if ( ! vc_user_access()->part( 'settings' )->can( 'vc-general-tab' )->get() ) {
				$title = __( 'About WPBakery Page Builder', 'js_composer' );
				$html = esc_html__( 'About', 'js_composer' );
			}
			$link = '<a title="' . esc_attr( $title ) . '" href="' . esc_url( $this->getSettingsPageLink() ) . '">' . $html . '</a>';
			array_unshift( $links, $link ); // Add to top
		}

		return $links;
	}

	/**
	 * Get settings page link
	 * @since 4.2
	 * @return string url to settings page
	 */
	public function getSettingsPageLink() {
		$page = 'vc-general';
		if ( ! vc_user_access()->part( 'settings' )->can( 'vc-general-tab' )->get() ) {
			$page = 'vc-welcome';
		}

		return add_query_arg( array( 'page' => $page ), admin_url( 'admin.php' ) );
	}

	/**
	 * Hooked class method by wp_head WP action.
	 * @since  4.2
	 * @access public
	 */
	public function addMetaData() {
		echo '<meta name="generator" content="Powered by WPBakery Page Builder - drag and drop page builder for WordPress."/>' . "\n";
	}

	/**
	 * Also add fix for IE8 bootstrap styles from WPExplorer
	 * @since  4.9
	 * @access public
	 */
	public function addIEMinimalSupport() {
		echo '<!--[if lte IE 9]><link rel="stylesheet" type="text/css" href="' . vc_asset_url( 'css/vc_lte_ie9.min.css' ) . '" media="screen"><![endif]-->';
	}

	/**
	 * Method adds css class to body tag.
	 *
	 * Hooked class method by body_class WP filter. Method adds custom css class to body tag of the page to help
	 * identify and build design specially for VC shortcodes.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	public function bodyClass( $classes ) {
		return js_composer_body_class( $classes );
	}

	/**
	 * Builds excerpt for post from content.
	 *
	 * Hooked class method by the_excerpt WP filter. When user creates content with VC all content is always wrapped by
	 * shortcodes. This methods calls do_shortcode for post's content and then creates a new excerpt.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param $output
	 *
	 * @return string
	 */
	public function excerptFilter( $output ) {
		global $post;
		/* nectar addition */ 
		if ( empty( $output ) && ! empty( $post->post_content ) ) {
			$post_content =  preg_replace ('/\[recent_posts[^\]]*\]/', ' ', $post->post_content);
			$text = strip_tags( do_shortcode($post_content ));
			$options = get_option('salient');
			$the_excerpt_length = (!empty($options['blog_excerpt_length'])) ? intval($options['blog_excerpt_length']) : 30; 
			$excerpt_length = apply_filters('excerpt_length', $the_excerpt_length);
			$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[...]' );
			$text = wp_trim_words( $text, $the_excerpt_length, $excerpt_more );
			return $text;
		}
		/* nectar addition end */ 
		return $output;
	}

	/**
	 * Remove unwanted wraping with p for content.
	 *
	 * Hooked by 'the_content' filter.
	 * @since 4.2
	 *
	 * @param null $content
	 *
	 * @return string|null
	 */
	public function fixPContent( $content = null ) {
		if ( $content ) {
			$s = array(
				'/' . preg_quote( '</div>', '/' ) . '[\s\n\f]*' . preg_quote( '</p>', '/' ) . '/i',
				'/' . preg_quote( '<p>', '/' ) . '[\s\n\f]*' . preg_quote( '<div ', '/' ) . '/i',
				'/' . preg_quote( '<p>', '/' ) . '[\s\n\f]*' . preg_quote( '<section ', '/' ) . '/i',
				'/' . preg_quote( '</section>', '/' ) . '[\s\n\f]*' . preg_quote( '</p>', '/' ) . '/i',
			);
			$r = array(
				'</div>',
				'<div ',
				'<section ',
				'</section>',
			);
			$content = preg_replace( $s, $r, $content );

			return $content;
		}

		return null;
	}

	/**
	 * Get array of string for locale.
	 *
	 * @since 4.7
	 *
	 * @return array
	 */
	public function getEditorsLocale() {
		return array(
			'add_remove_picture' => __( 'Add/remove picture', 'js_composer' ),
			'finish_adding_text' => __( 'Finish Adding Images', 'js_composer' ),
			'add_image' => __( 'Add Image', 'js_composer' ),
			'add_images' => __( 'Add Images', 'js_composer' ),
			'settings' => __( 'Settings', 'js_composer' ),
			'main_button_title' => __( 'WPBakery Page Builder', 'js_composer' ),
			'main_button_title_backend_editor' => __( 'Backend Editor', 'js_composer' ),
			'main_button_title_frontend_editor' => __( 'Frontend Editor', 'js_composer' ),
			'main_button_title_revert' => __( 'Classic Mode', 'js_composer' ),
			'main_button_title_gutenberg' => __( 'Gutenberg Editor', 'js_composer' ),
			'please_enter_templates_name' => __( 'Enter template name you want to save.', 'js_composer' ),
			'confirm_deleting_template' => __( 'Confirm deleting "{template_name}" template, press Cancel to leave. This action cannot be undone.', 'js_composer' ),
			'press_ok_to_delete_section' => __( 'Press OK to delete section, Cancel to leave', 'js_composer' ),
			'drag_drop_me_in_column' => __( 'Drag and drop me in the column', 'js_composer' ),
			'press_ok_to_delete_tab' => __( 'Press OK to delete "{tab_name}" tab, Cancel to leave', 'js_composer' ),
			'slide' => __( 'Slide', 'js_composer' ),
			/* nectar addition */ 
			'tab' => __( 'Tab', 'js_composer' ),
			'item' => __( 'Item', 'js_composer' ),
			'nectar_icon_list_item' => __( 'List Item', 'js_composer' ),
			'client' => __( 'Client', 'js_composer' ),
			'page_link' => __( 'Link', 'js_composer' ),
			'column' => __( 'Column', 'js_composer' ),
			'testimonial' => __( 'Testimonial', 'js_composer' ),
			/* nectar addition end */ 
			'section' => __( 'Section', 'js_composer' ),
			'please_enter_new_tab_title' => __( 'Please enter new tab title', 'js_composer' ),
			'press_ok_delete_section' => __( 'Press OK to delete "{tab_name}" section, Cancel to leave', 'js_composer' ),
			'section_default_title' => __( 'Section', 'js_composer' ),
			'please_enter_section_title' => __( 'Please enter new section title', 'js_composer' ),
			'error_please_try_again' => __( 'Error. Please try again.', 'js_composer' ),
			'if_close_data_lost' => __( 'If you close this window all shortcode settings will be lost. Close this window?', 'js_composer' ),
			'header_select_element_type' => __( 'Select element type', 'js_composer' ),
			'header_media_gallery' => __( 'Media gallery', 'js_composer' ),
			'header_element_settings' => __( 'Element settings', 'js_composer' ),
			'add_tab' => __( 'Add tab', 'js_composer' ),
			'are_you_sure_convert_to_new_version' => __( 'Are you sure you want to convert to new version?', 'js_composer' ),
			'loading' => __( 'Loading...', 'js_composer' ),
			// Media editor
			'set_image' => __( 'Set Image', 'js_composer' ),
			'are_you_sure_reset_css_classes' => __( 'Are you sure that you want to remove all your data?', 'js_composer' ),
			'loop_frame_title' => __( 'Loop settings', 'js_composer' ),
			'enter_custom_layout' => __( 'Custom row layout', 'js_composer' ),
			'wrong_cells_layout' => __( 'Wrong row layout format! Example: 1/2 + 1/2 or span6 + span6.', 'js_composer' ),
			'row_background_color' => __( 'Row background color', 'js_composer' ),
			'row_background_image' => __( 'Row background image', 'js_composer' ),
			'column_background_color' => __( 'Column background color', 'js_composer' ),
			'column_background_image' => __( 'Column background image', 'js_composer' ),
			'guides_on' => __( 'Guides ON', 'js_composer' ),
			'guides_off' => __( 'Guides OFF', 'js_composer' ),
			'template_save' => __( 'New template successfully saved.', 'js_composer' ),
			'template_added' => __( 'Template added to the page.', 'js_composer' ),
			'template_added_with_id' => __( 'Template added to the page. Template has ID attributes, make sure that they are not used more than once on the same page.', 'js_composer' ),
			'template_removed' => __( 'Template successfully removed.', 'js_composer' ),
			'template_is_empty' => __( 'Template is empty: There is no content to be saved as a template.', 'js_composer' ),
			'template_save_error' => __( 'Error while saving template.', 'js_composer' ),
			'css_updated' => __( 'Page settings updated!', 'js_composer' ),
			'update_all' => __( 'Update all', 'js_composer' ),
			'confirm_to_leave' => __( 'The changes you made will be lost if you navigate away from this page.', 'js_composer' ),
			'inline_element_saved' => __( '%s saved!', 'js_composer' ),
			'inline_element_deleted' => __( '%s deleted!', 'js_composer' ),
			'inline_element_cloned' => __( '%s cloned. <a href="#" class="vc_edit-cloned" data-model-id="%s">Edit now?</a>', 'js_composer' ),
			'gfonts_loading_google_font_failed' => __( 'Loading Google Font failed', 'js_composer' ),
			'gfonts_loading_google_font' => __( 'Loading Font...', 'js_composer' ),
			'gfonts_unable_to_load_google_fonts' => __( 'Unable to load Google Fonts', 'js_composer' ),
			'no_title_parenthesis' => sprintf( '(%s)', __( 'no title', 'js_composer' ) ),
			'error_while_saving_image_filtered' => __( 'Error while applying filter to the image. Check your server and memory settings.', 'js_composer' ),
			'ui_saved' => sprintf( '<i class="vc-composer-icon vc-c-icon-check"></i> %s', __( 'Saved!', 'js_composer' ) ),
			'ui_danger' => sprintf( '<i class="vc-composer-icon vc-c-icon-close"></i> %s', __( 'Failed to Save!', 'js_composer' ) ),
			'delete_preset_confirmation' => __( 'You are about to delete this preset. This action can not be undone.', 'js_composer' ),
			'ui_template_downloaded' => __( 'Downloaded', 'js_composer' ),
			'ui_template_update' => __( 'Update', 'js_composer' ),
			'ui_templates_failed_to_download' => __( 'Failed to download template', 'js_composer' ),
			'preset_removed' => __( 'Element successfully removed.', 'js_composer' ),
			'vc_successfully_updated' => __( 'Successfully updated!', 'js_composer' ),
			'gutenbergDoesntWorkProperly' => __( 'Gutenberg plugin doesn\'t work properly. Please check Gutenberg plugin.', 'js_composer' ),
		);
	}
}
