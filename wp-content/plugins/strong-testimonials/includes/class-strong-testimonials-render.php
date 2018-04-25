<?php
/**
 * Class Strong_Testimonials_Render
 *
 * @since 2.28.0
 */
class Strong_Testimonials_Render {

	public $styles = array();

	public $scripts = array();

	public $script_vars = array();

	public $css = array();

	public $shortcode;

	public $view_defaults = array();

	public $view_atts = array();

	public $query;

	/**
	 * Strong_Testimonials_Render constructor.
	 */
	public function __construct() {
		$this->set_view_defaults();
		$this->set_shortcodes();
		$this->add_enqueue_actions();
	}

	/**
	 * Set the defaults for a view.
	 */
	public function set_view_defaults() {
		$this->view_defaults = apply_filters( 'wpmtst_view_default', get_option( 'wpmtst_view_default' ) );
	}

	/**
	 * Get the shortcode defaults.
	 *
	 * @return array
	 */
	public function get_view_defaults() {
		return $this->view_defaults;
	}

	/**
	 * Set shortcode.
	 */
	public function set_shortcodes() {
		$this->shortcode = WPMST()->shortcodes->get_shortcode();
	}

	/**
	 * Store view attributes.
	 *
	 * @param $atts
	 */
	public function set_atts( $atts ) {
		$this->view_atts = $atts;
	}

	/**
	 * Load scripts and styles.
	 *
	 * @since 2.28.0
	 */
	private function add_enqueue_actions() {
		$options = get_option( 'wpmtst_compat_options' );

		/**
		 * Fallback.
		 * Provision each view when the shortcode is rendered.
		 * Enqueue both stylesheets and scripts in footer.
		 * _!_ Required for template function. _!_
		 */
		add_action( 'wpmtst_view_rendered', array( $this, 'view_rendered' ) );
		add_action( 'wpmtst_form_rendered', array( $this, 'view_rendered' ) );
		add_action( 'wpmtst_form_success', array( $this, 'view_rendered' ) );

		switch ( $options['prerender'] ) {
			case 'none':
				/**
				 * Use fallback.
				 */
				break;

			case 'all':
				/**
				 * Provision all views.
				 * Enqueue stylesheets in head, scripts in footer.
				 */
				add_action( 'wp_enqueue_scripts', array( $this, 'provision_all_views' ), 1 );
				add_action( 'wp_enqueue_scripts', array( $this, 'view_rendered' ) );
				break;

			default:
				/**
				 * Provision views in current page only.
				 * Enqueue stylesheets in head, scripts in footer.
				 */
				$this->provision_current_page();
				add_action( 'wp_enqueue_scripts', array( $this, 'view_rendered' ) );
		}
	}

	/**
	 * Find shortcodes in content and prerender the view.
	 *
	 * In order to load stylesheets in normal sequence to prevent FOUC.
	 */
	private function provision_current_page() {
		// Look for our shortcodes in post content and widgets.
		add_action( 'wp_enqueue_scripts', array( $this, 'find_views' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'find_views_in_postmeta' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'find_views_in_postexcerpt' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'find_widgets' ), 1 );

		// Page Builder by Site Origin
		if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'find_pagebuilder_widgets' ), 1 );
		}

		// Beaver Builder
		if ( defined( 'FL_BUILDER_VERSION' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'find_beaverbuilder_widgets' ), 1 );
		}

		// Black Studio TinyMCE Widget
		if ( class_exists( 'Black_Studio_TinyMCE_Plugin' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'find_blackstudio_widgets' ), 1 );
		}
	}

	/**
	 * Provision all views.
	 *
	 * @since 2.28.0
	 */
	public function provision_all_views() {
		$views = wpmtst_get_views();
		foreach ( $views as $view ) {
			// Array( [id] => 1, [name] => TEST, [value] => {serialized_array} )
			$view_data = maybe_unserialize( $view['value'] );
			if ( isset( $view_data['mode'] ) && 'single_template' != $view_data['mode'] ) {
				$atts = array( 'view' => $view['id'] );
				$this->prerender( $atts );
			}
		}
	}

	/**
	 * Load stylesheet and scripts.
	 *
	 * Refer to add_enqueue_actions() for sequence.
	 *
	 * For compatibility with
	 * (1) page builders,
	 * (2) plugins like [Posts For Page] and [Custom Content Shortcode]
	 *     that pull in other posts so this plugin cannot prerender them,
	 * (3) using the form in popup makers.
	 */
	public function view_rendered() {
		$this->load_styles();
		$this->load_scripts();

		/**
		 * Print script variables on footer hook.
		 * @since 2.25.2
		 */
		add_action( 'wp_footer', array( $this, 'view_rendered_after' ) );
	}

	/**
	 * Add script variables only after view is rendered.
	 * To prevent duplicate variables.
	 *
	 * @since 2.24.1
	 */
	public function view_rendered_after() {
		$this->localize_scripts();
	}

	/**
	 * Add a stylesheet handle for enqueueing.
	 *
	 * @access private
	 *
	 * @param string $style_name The stylesheet handle.
	 *
	 * @since 2.27.0 Load FontAwesome conditionally. Check filter in one place
	 *               instead of each place where FontAwesome is needed.
	 */
	public function add_style( $style_name ) {
		if ( 'wpmtst-font-awesome' == $style_name && ! apply_filters( 'wpmtst_load_font_awesome', true ) ) {
			return;
		}

		if ( ! in_array( $style_name, $this->styles ) ) {
			$this->styles[] = $style_name;
		}
	}

	/**
	 * Add a script handle for enqueueing.
	 *
	 * @param string $script_name The script handle.
	 *
	 * @since 2.17.4 Using script handle as key.
	 */
	public function add_script( $script_name ) {
		$this->scripts[ $script_name ] = $script_name;
	}

	/**
	 * Add a script variable for localizing.
	 *
	 * @param string $script_name The script handle.
	 * @param string $var_name The script variable name.
	 * @param array $var The script variable.
	 *
	 * @since 2.17.5 Replace using variable name as key.
	 */
	public function add_script_var( $script_name, $var_name, $var ) {
		unset( $this->script_vars[ $var_name ] );
		$this->script_vars[ $var_name ] = array(
			'script_name' => $script_name,
			'var_name'    => $var_name,
			'var'         => $var,
		);
	}

	/**
	 * Enqueue stylesheets for the view being processed.
	 *
	 * @since 2.22.3
	 */
	public function load_styles() {
		$styles = apply_filters( 'wpmtst_styles', $this->styles );
		if ( $styles ) {
			foreach ( $styles as $key => $style ) {
				if ( ! wp_style_is( $style ) ) {
					wp_enqueue_style( $style );
				}
			}
		}
		wp_enqueue_style( 'wpmtst-custom-style' );
	}

	/**
	 * Enqueue scripts for the view being processed.
	 *
	 * @since 2.22.3
	 */
	public function load_scripts() {
		$scripts = apply_filters( 'wpmtst_scripts', $this->scripts );
		if ( $scripts ) {
			foreach ( $scripts as $key => $script ) {
				if ( ! wp_script_is( $script ) ) {
					wp_enqueue_script( $script );
				}
			}
		}
	}

	/**
	 * Print script variables for the view being processed.
	 *
	 * @since 2.22.3
	 */
	public function localize_scripts() {
		$vars = apply_filters( 'wpmtst_script_vars', $this->script_vars );
		if ( $vars ) {
			foreach ( $vars as $key => $var ) {
				wp_localize_script( $var['script_name'], $var['var_name'], $var['var'] );
			}
		}
	}

	/**
	 * Check the content for our shortcode.
	 *
	 * @param $content
	 *
	 * @return bool
	 */
	private function check_content( $content ) {
		if ( false === strpos( $content, '[' . $this->shortcode ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check and process a widget.
	 *
	 * @since 2.28.0
	 *
	 * @param $widget
	 */
	private function check_widget( $widget ) {
		if ( isset( $widget['view'] ) && $widget['view'] ) {
			$atts = array( 'view' => $widget['view'] );
			$this->prerender( $atts );
		}
	}

	/**
	 * Build list of all shortcode views on a page.
	 *
	 * @access public
	 */
	public function find_views() {
		global $post;
		if ( empty( $post ) ) {
			return;
		}

		$content = $post->post_content;
		if ( ! $this->check_content( $content ) ) {
			return;
		}

		$this->process_content( $content );
	}

	/**
	 * Build list of all shortcode views in a page's meta fields.
	 *
	 * To handle page builders that store shortcodes and widgets in post meta.
	 *
	 * @access public
	 * @since 1.15.11
	 */
	public function find_views_in_postmeta() {
		global $post;
		if ( empty( $post ) ) {
			return;
		}

		$meta_content            = get_post_meta( $post->ID );
		$meta_content_serialized = maybe_serialize( $meta_content );
		if ( ! $this->check_content( $meta_content_serialized ) ) {
			return;
		}

		$this->process_content( $meta_content_serialized );
	}

	/**
	 * Build list of all shortcode views in a page's excerpt.
	 *
	 * WooCommerce stores product short description in post_excerpt field.
	 *
	 * @access public
	 * @since 1.15.12
	 */
	public function find_views_in_postexcerpt() {
		global $post;
		if ( empty( $post ) ) {
			return;
		}

		if ( ! $this->check_content( $post->post_excerpt ) ) {
			return;
		}

		$this->process_content( $post->post_excerpt );
	}

	/**
	 * Find widgets in a page to gather styles, scripts and script vars.
	 *
	 * For standard widgets NOT in [Page Builder by SiteOrigin] panels.
	 *
	 * Thanks to Matthew Harris for catching strict pass-by-reference error
	 * on $id = array_pop( explode( '-', $widget_name ) ).
	 * @link https://github.com/cdillon/strong-testimonials/issues/3
	 *
	 * @access public
	 */
	public function find_widgets() {
		// Get all widgets
		$all_widgets = get_option( 'sidebars_widgets' );
		if ( ! $all_widgets ) {
			return;
		}

		// Get active strong widgets
		$strong_widgets = get_option( 'widget_strong-testimonials-view-widget' );

		foreach ( $all_widgets as $sidebar => $widgets ) {
			// active widget areas only
			if ( ! $widgets || 'wp_inactive_widgets' == $sidebar || 'array_version' == $sidebar ) {
				continue;
			}

			foreach ( $widgets as $key => $widget_name ) {
				// Is our widget active?
				if ( 0 === strpos( $widget_name, 'strong-testimonials-view-widget-' ) ) {

					if ( $strong_widgets ) {
						$name_parts = explode( '-', $widget_name );
						$id         = array_pop( $name_parts );

						if ( isset( $strong_widgets[ $id ] ) ) {
							$widget = $strong_widgets[ $id ];
							$this->check_widget( $widget );
						}

					}

				} elseif ( 0 === strpos( $widget_name, 'text-' ) ) {

					// Get text widget content to scan for shortcodes.
					$text_widgets = get_option( 'widget_text' );

					if ( $text_widgets ) {
						$name_parts = explode( '-', $widget_name );
						$id         = array_pop( $name_parts );

						if ( isset( $text_widgets[ $id ] ) ) {
							$widget = $text_widgets[ $id ];
							$this->process_content( $widget['text'] );
						}
					}

				}
			} // foreach $widgets
		} // foreach $all_widgets
	}

	/**
	 * Find widgets in a page to gather styles, scripts and script vars.
	 *
	 * For widgets in Page Builder by SiteOrigin.
	 */
	public function find_pagebuilder_widgets() {
		// Get all widgets
		$panels_data = get_post_meta( get_the_ID(), 'panels_data', true );
		if ( ! $panels_data ) {
			return;
		}

		$all_widgets = $panels_data['widgets'];
		if ( ! $all_widgets ) {
			return;
		}

		// Need to group by cell to replicate Page Builder rendering order,
		// whether these are Strong widgets or not.
		$cells = array();
		foreach ( $all_widgets as $key => $widget ) {
			$cell_id             = $widget['panels_info']['cell'];
			$cells[ $cell_id ][] = $widget;
		}

		foreach ( $cells as $cell_widgets ) {
			foreach ( $cell_widgets as $key => $widget ) {
				if ( 'Strong_Testimonials_View_Widget' == $widget['panels_info']['class'] ) {
					$this->check_widget( $widget );
				} elseif ( 'WP_Widget_Text' == $widget['panels_info']['class'] ) {
					// Is a Text widget?
					$this->process_content( $widget['text'] );
				}
			}
		}
	}

	/**
	 * Find widgets in a page to gather styles, scripts and script vars.
	 *
	 * For widgets in Beaver Builder.
	 */
	public function find_beaverbuilder_widgets() {
		$nodes = get_post_meta( get_the_ID(), '_fl_builder_data', true );
		if ( ! $nodes ) {
			return;
		}

		foreach ( $nodes as $key => $node ) {
			if ( 'module' != $node->type ) {
				continue;
			}

			if ( 'widget' != $node->settings->type ) {
				continue;
			}

			if ( 'Strong_Testimonials_View_Widget' == $node->settings->widget ) {
				$settings = (array) $node->settings;
				$widget   = (array) $settings['widget-strong-testimonials-view-widget'];
				$this->check_widget( $widget );
			}
		}
	}


	/**
	 * Build list of all shortcode views in Black Studio TinyMCE Widget.
	 *
	 * @access public
	 * @since 1.16.14
	 */
	public function find_blackstudio_widgets() {
		global $post;
		if ( empty( $post ) ) {
			return;
		}

		$widget_content = get_option( 'widget_black-studio-tinymce' );
		if ( ! $widget_content ) {
			return;
		}

		$widget_content_serialized = maybe_serialize( $widget_content );
		if ( ! $this->check_content( $widget_content_serialized ) ) {
			return;
		}

		$this->process_content( $widget_content_serialized );
	}

	/**
	 * @param $atts
	 *
	 * @return bool
	 */
	private function view_not_found( $atts ) {
		return ( isset( $atts['view_not_found'] ) && $atts['view_not_found'] );
	}

	/**
	 * Process content for shortcodes.
	 *
	 * A combination of has_shortcode and shortcode_parse_atts.
	 * This seems to solve the unenclosed shortcode issue too.
	 *
	 * @access private
	 *
	 * @param string $content Post content or widget content.
	 */
	private function process_content( $content ) {
		preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );
		if ( empty( $matches ) ) {
			return;
		}

		foreach ( $matches as $key => $shortcode ) {
			if ( $this->shortcode === $shortcode[2] ) {
				/**
				 * Retrieve all attributes from the shortcode.
				 *
				 * @since 1.16.13 Adding html_entity_decode.
				 */
				$atts = shortcode_parse_atts( html_entity_decode( $shortcode[3] ) );
				$this->prerender( $atts );
			} else {
				/**
				 * Recursively process nested shortcodes.
				 *
				 * Handles:
				 * Elegant Themes page builder.
				 *
				 * @since 1.15.5
				 */
				$this->process_content( $shortcode[5] );
			}
		}
	}

	/**
	 * Prerender a view to gather styles, scripts, and script vars.
	 *
	 * Similar to Strong_Testimonials_Shortcodes::render_view().
	 *
	 * @param $atts
	 *
	 * @since 1.25.0
	 * @since 2.16.0 Move all processing to Strong_View class.
	 */
	public function prerender( $atts ) {
		// Just like the shortcode function:
		$atts = shortcode_atts(
			array(),
			$atts,
			$this->shortcode
		);
		if ( $this->view_not_found( $atts ) ) {
			return;
		}

		$this->set_atts( $atts );

		switch ( $atts['mode'] ) {
			case 'form' :
				$view = new Strong_View_Form( $atts );
				break;
			case 'slideshow' :
				$view = new Strong_View_Slideshow( $atts );
				break;
			default :
				$view = new Strong_View_Display( $atts );
		}
		$view->process();

		/**
		 * Allow themes and plugins to do stuff like add extra stylesheets.
		 *
		 * @since 2.22.0
		 */
		do_action( 'wpmtst_view_found', $atts );
	}

	/**
	 * Parse view attributes.
	 *
	 * This is used by the shortcode filter and prerendering to assemble
	 * the view attributes.
	 *
	 * @param array $out   The output array of shortcode attributes.
	 * @param array $pairs The supported attributes and their defaults.
	 * @param array $atts  The user defined shortcode attributes.
	 *
	 * @return array
	 */
	public function parse_view( $out, $pairs, $atts ) {
		// Convert "id" to "view"
		if ( isset( $atts['id'] ) && $atts['id'] ) {
			$atts['view'] = $atts['id'];
			unset( $atts['id'] );
		}

		// Fetch the view
		$view = wpmtst_get_view( $atts['view'] );

		/**
		 * Add error attribute for shortcode handler.
		 *
		 * @since 1.21.0
		 */
		if ( ! $view ) {
			return array_merge( array( 'view_not_found' => 1 ), $out );
		}

		$view_data = unserialize( $view['value'] );

		/**
		 * Adjust for defaults.
		 *
		 * @since 2.30.0
		 */
		if ( isset( $view_data['category'] ) && 'all' == $view_data['category'] ) {
			$view_data['category'] = '';
		}
		if ( 'slideshow' == $view_data['mode'] ) {
			unset( $view_data['id'] );
		}

		/**
		 * Saner approach.
		 *
		 * @since 2.30.0
		 */

		// Post ID's override single ID, category, and count
		if ( isset( $atts['post_ids'] ) ) {
			$atts['id']       = $atts['post_ids'];
			$atts['category'] = '';
			$atts['count']    = -1;
		}

		// Override category slugs. This can handle a combination of slugs and ID's
		if ( isset( $atts['category'] ) ) {
			$cats  = array();
			$items = explode( ',', $atts['category'] );
			foreach ( $items as $item ) {
				if ( is_numeric( $item ) ) {
					$cats[] = $item;
				}
				else {
					$term = get_term_by( 'slug', $item, 'wpm-testimonial-category' );
					if ( $term ) {
						$cats[] = $term->term_id;
					}
				}
			}
			if ( $cats ) {
				$atts['category'] = implode( ',', $cats );
			}
		}

		$out = array_merge( $this->get_view_defaults(), $view_data, $atts );

		return $out;
	}

}
