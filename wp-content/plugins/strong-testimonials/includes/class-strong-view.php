<?php
/**
 * View class.
 *
 * @since 2.3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Strong_View' ) ) :

class Strong_View {

	/**
	 * The view settings.
	 *
	 * @var array
	 */
	public $atts;

	/**
	 * The query.
	 */
	public $query;

	/**
	 * The template file.
	 */
	public $template_file;

	/**
	 * The view output.
	 *
	 * @var string
	 */
	public $html;

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	public $plugin_version;

	/**
	 * Strong_View constructor.
	 *
	 * @param array $atts
	 */
	function __construct( $atts = array() ) {
		$this->atts = apply_filters( 'wpmtst_view_atts', $atts );
		$this->plugin_version = get_option( 'wpmtst_plugin_version' );
	}

	/**
	 * Return our rendered template.
	 *
	 * @return string
	 */
	public function output() {
		return $this->html;
	}

	/**
	 * Process the view.
	 *
	 * Used by main class to load the scripts and styles for this View.
	 */
	public function process() {}

	/**
	 * Build the view.
	 */
	public function build() {}

	/**
	 * Add content filters.
	 */
	public function add_content_filters() {
		if ( isset( $this->atts['content'] ) && 'truncated' == $this->atts['content'] ) {

			// Force use of content instead of manual excerpt.
			add_filter( 'wpmtst_get_the_excerpt', 'wpmtst_bypass_excerpt', 1 );

		} elseif ( isset( $this->atts['content'] ) && 'excerpt' == $this->atts['content'] ) {

			// Maybe add read-more to manual excerpts.
			add_filter( 'wpmtst_get_the_excerpt', 'wpmtst_custom_excerpt_more', 20 );

		}
		// else no filters
	}

	/**
	 * Add content filters.
	 */
	public function remove_content_filters() {
		if ( isset( $this->atts['content'] ) && 'truncated' == $this->atts['content'] ) {

			remove_filter( 'wpmtst_get_the_excerpt', 'wpmtst_bypass_excerpt', 1 );

		} elseif ( isset( $this->atts['content'] ) && 'excerpt' == $this->atts['content'] ) {

			remove_filter( 'wpmtst_get_the_excerpt', 'wpmtst_custom_excerpt_more', 20 );

		}
		// else no filters
	}

	/**
	 * Build our query based on view attributes.
	 */
	public function build_query() {}

	/**
	 * Build class list based on view attributes.
	 *
	 * This must happen after the query.
	 */
	public function build_classes() {}

	/**
	 * Load template's extra stylesheets.
	 *
	 * @since 2.11.12
	 * @since 2.16.0 In Strong_View class.
	 */
	public function load_extra_stylesheets() {
		$styles = WPMST()->templates->get_template_config( $this->atts, 'styles', false );
		if ( $styles ) {
			$styles_array = explode( ',', str_replace( ' ', '', $styles ) );
			foreach ( $styles_array as $handle ) {
				WPMST()->render->add_style( $handle );
			}
		}
	}

	/**
	 * Load template's script and/or dependencies.
	 *
	 * @since 1.25.0
	 * @since 2.16.0 In Strong_View class.
	 */
	public function load_dependent_scripts() {
		// Scripts that are already registered.
		$deps = WPMST()->templates->get_template_config( $this->atts, 'scripts', false );
		$deps_array = $deps ? explode( ',', str_replace( ' ', '', $deps ) ) : array();

		// A single script included in directory.
		$script = WPMST()->templates->get_template_config( $this->atts, 'script', false );

		if ( $script ) {
			$handle = 'testimonials-' . $this->atts['template'];
			wp_register_script( $handle, $script, $deps_array );
			WPMST()->render->add_script( $handle );
		}
		else {
			foreach ( $deps_array as $handle ) {
				WPMST()->render->add_script( $handle );
			}
		}
	}

	/**
	 * Find a template's associated stylesheet.
	 *
	 * @since 1.23.0
	 * @since 2.16.0 In Strong_View class.
	 *
	 * @param bool  $enqueue   True = enqueue the stylesheet, @since 2.3
	 *
	 * @return bool|string
	 */
	public function find_stylesheet( $enqueue = true ) {
		// In case of deactivated widgets still referencing deleted Views
		if ( ! isset( $this->atts['template'] ) || ! $this->atts['template'] ) {
			return false;
		}

		$stylesheet = WPMST()->templates->get_template_attr( $this->atts, 'stylesheet', false );
		if ( $stylesheet ) {
			$handle = 'testimonials-' . str_replace( ':', '-', $this->atts['template'] );
			wp_register_style( $handle, $stylesheet, array(), $this->plugin_version );
			if ( $enqueue ) {
				WPMST()->render->add_style( $handle );
			} else {
				return $handle;
			}
		}

		return false;
	}

	/**
	 * Assemble list of CSS classes.
	 *
	 * @since 2.11.0
	 * @since 2.30.0 Adding template option classes.
	 *
	 * @return array
	 */
	public function get_template_css_class() {
		$template_name = $this->atts['template'];

		// Maintain back-compat with template format version 1.0.
		$class = str_replace( ':content', '', $template_name );
		$class = str_replace( ':', '-', $class );
		$class = str_replace( '-form-form', '-form', $class );
		$class_list = array( $class );

		$template_object = WPMST()->templates->get_template_by_name( $template_name );

		if ( isset( $template_object['config']['options'] ) && is_array( ( $template_object['config']['options'] ) ) ) {

			foreach ( $template_object['config']['options'] as $option ) {

				if ( isset( $this->atts['template_settings'][ $template_name ][ $option->name ] ) ) {

					foreach ( $option->values as $value ) {
						if ( $value->value == $this->atts['template_settings'][ $template_name ][ $option->name ] ) {
							if ( isset( $value->class_name ) ) {
								$class_list[] = $value->class_name;
							}
						}
					}

				}

			}

		}

		return $class_list;
	}

	/**
	 * Print our custom style.
	 *
	 * @since 2.22.0
	 */
	public function add_custom_style() {
		$this->custom_background();
		$this->custom_font_color();

		/**
		 * Hook to add more inline style to `wpmtst-custom-style` handle.
		 * @since 2.22.0
		 */
		do_action( 'wpmtst_view_custom_style', $this );
	}

	/**
	 * Is this a form view?
	 *
	 * @since 2.30.0
	 *
	 * @return bool
	 */
	public function is_form() {
		return ( isset( $this->atts['mode'] ) && 'form' == $this->atts['mode'] );
	}

	/**
	 * Build CSS for custom font color.
	 *
	 * @since 2.30.0
	 */
	public function custom_font_color() {
		$font_color = $this->atts['font-color'];
		if ( ! isset( $font_color['type'] ) || 'custom' != $font_color['type'] ) {
			return;
		}

		$c1 = isset( $font_color['color'] ) ? $font_color['color'] : '';

		if ( $c1 ) {
			$view_el = ".strong-view-id-{$this->atts['view']}";

			if ( $this->is_form() ) {
				wp_add_inline_style( 'wpmtst-custom-style',
				                     "$view_el .strong-form-inner { color: $c1; }" );
			}
			else {
				wp_add_inline_style( 'wpmtst-custom-style',
				                     "$view_el .testimonial-heading, " .
				                     "$view_el .testimonial-content p, " .
				                     "$view_el .testimonial-content a.readmore, " .
				                     "$view_el .testimonial-client div, " .
				                     "$view_el .testimonial-client a { color: $c1; }" );
			}
		}
	}

	/**
	 * Build CSS for custom background.
	 */
	public function custom_background() {
		$background = $this->atts['background'];
		if ( ! isset( $background['type'] ) ) {
			return;
		}

		$c1 = '';
		$c2 = '';

		switch ( $background['type'] ) {
			case 'preset':
				$preset = wpmtst_get_background_presets( $background['preset'] );
				$c1     = $preset['color'];
				if ( isset( $preset['color2'] ) ) {
					$c2 = $preset['color2'];
				}
				break;
			case 'gradient':
				$c1 = $background['gradient1'];
				$c2 = $background['gradient2'];
				break;
			case 'single':
				$c1 = $background['color'];
				break;
			default:
		}

		// Special handling for Divi Builder
		$prefix = '';
		if ( isset( $this->atts['divi_builder'] ) && $this->atts['divi_builder'] && wpmtst_divi_builder_active() ) {
			$prefix = '#et_builder_outer_content ';
		}

		$view_el = "$prefix.strong-view-id-{$this->atts['view']}";

		// Includes special handling for Bold template.
		if ( $c1 && $c2 ) {

			$gradient = self::gradient_rules( $c1, $c2 );

			if ( $this->is_form() ) {
				wp_add_inline_style( 'wpmtst-custom-style',
				                     "$view_el .strong-form-inner { $gradient }" );
			}
			else {
				wp_add_inline_style( 'wpmtst-custom-style',
				                     "$view_el .testimonial-inner { $gradient }" );

				if ( 'bold' == WPMST()->atts( 'template' ) ) {
					wp_add_inline_style( 'wpmtst-custom-style',
					                     "$view_el .readmore-page { background: $c2 }" );
				}
			}

		}
		elseif ( $c1 ) {

			if ( $this->is_form() ) {
				wp_add_inline_style( 'wpmtst-custom-style',
				                     "$view_el .strong-form-inner { background: $c1; }" );
			}
			else {
				wp_add_inline_style( 'wpmtst-custom-style',
				                     "$view_el .testimonial-inner { background: $c1; }" );

				if ( 'bold' == WPMST()->atts( 'template' ) ) {
					wp_add_inline_style( 'wpmtst-custom-style',
					                     "$view_el .readmore-page { background: $c1 }" );
				}
			}

		}
	}

	/**
	 * Print gradient rules.
	 *
	 * @param $c1
	 * @param $c2
	 *
	 * @return string
	 */
	public function gradient_rules( $c1, $c2 ) {
		return "background: {$c1};
	background: -moz-linear-gradient(top, {$c1} 0%, {$c2} 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, {$c1}), color-stop(100%, {$c2}));
	background: -webkit-linear-gradient(top,  {$c1} 0%, {$c2} 100%);
	background: -o-linear-gradient(top, {$c1} 0%, {$c2} 100%);
	background: -ms-linear-gradient(top, {$c1} 0%, {$c2} 100%);
	background: linear-gradient(to bottom, {$c1} 0%, {$c2} 100%);
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='{$c1}', endColorstr='{$c2}', GradientType=0);";
	}

	/**
	 * Stars
	 *
	 * @since 2.16.0 In Strong_View class.
	 */
	public function has_stars() {
		if ( isset( $this->atts['client_section'] ) ) {
			foreach ( $this->atts['client_section'] as $field ) {
				if ( 'rating' == $field['type'] ) {
					WPMST()->render->add_style( 'wpmtst-rating-display' );
					break;
				}
			}
		}
	}

}

endif;
