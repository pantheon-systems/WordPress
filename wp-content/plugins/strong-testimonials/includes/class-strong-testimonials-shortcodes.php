<?php
/**
 * Class Strong_Testimonials_Shortcodes
 *
 * @since 2.28.0
 */
class Strong_Testimonials_Shortcodes {

	public $shortcode = 'testimonial_view';

	/**
	 * Strong_Testimonials_Shortcodes constructor.
	 */
	public function __construct() {
		add_shortcode( $this->shortcode, array( $this, 'testimonial_view_shortcode' ) );
		add_filter( 'shortcode_atts_' . $this->shortcode, array( $this, 'testimonial_view_filter' ), 10, 3 );

		add_shortcode( 'testimonial_count', array( $this, 'testimonial_count_shortcode' ) );

		add_filter( 'widget_text', 'do_shortcode' );
		add_filter( 'no_texturize_shortcodes', array( $this, 'no_texturize_shortcodes' ) );

		add_filter( 'strong_view_html', array( $this, 'remove_whitespace' ) );
		add_filter( 'strong_view_form_html', array( $this, 'remove_whitespace' ) );
	}

	public function get_shortcode() {
		return $this->shortcode;
	}

	/**
	 * Our primary shortcode.
	 *
	 * @param      $atts
	 * @param null $content
	 *
	 * @return mixed|string
	 */
	public function testimonial_view_shortcode( $atts, $content = null ) {
		$out = shortcode_atts(
			array(),
			$atts,
			$this->shortcode
		);

		return $this->render_view( $out );
	}

	/**
	 * Shortcode attribute filter
	 *
	 * @since 1.21.0
	 *
	 * @param array $out The output array of shortcode attributes.
	 * @param array $pairs The supported attributes and their defaults.
	 * @param array $atts The user defined shortcode attributes.
	 *
	 * @return array
	 */
	public function testimonial_view_filter( $out, $pairs, $atts ) {
		return WPMST()->render->parse_view( $out, $pairs, $atts );
	}

	/**
	 * Render the View.
	 *
	 * @param $out
	 *
	 * @return mixed|string
	 */
	public function render_view( $out ) {
		// Did we find this view?
		if ( isset( $out['view_not_found'] ) && $out['view_not_found'] ) {
			if ( current_user_can( 'strong_testimonials_views' ) ) {
				return '<p style="color: red;">' . sprintf( __( 'Strong Testimonials View %s not found', 'strong-testimonials' ), $out['view'] ) . '</p>';
			}
		}

		switch ( $out['mode'] ) {
			case 'form' :
				$view = new Strong_View_Form( $out );
				break;
			case 'slideshow' :
				$view = new Strong_View_Slideshow( $out );
				break;
			default :
				$view = new Strong_View_Display( $out );
		}
		$view->build();

		return $view->output();
	}

	/**
	 * Remove whitespace between tags. Helps prevent double wpautop in plugins
	 * like Posts For Pages and Custom Content Shortcode.
	 *
	 * @param $html
	 *
	 * @since 2.3
	 *
	 * @return mixed
	 */
	public function remove_whitespace( $html ) {
		$options = get_option( 'wpmtst_options' );
		if ( $options['remove_whitespace'] ) {
			$html = preg_replace( '~>\s+<~', '><', $html );
		}

		return $html;
	}

	/**
	 * A shortcode to display the number of testimonials.
	 *
	 * For all: [testimonial_count]
	 * For a specific category (by slug): [testimonial_count category="abc"]
	 * Unformatted: [testimonial_count unformatted]
	 *
	 * @param      $atts
	 * @param null $content
	 *
	 * @since 2.19.0
	 * @since 2.30.0 unformatted attribute
	 *
	 * @return int
	 */
	public function testimonial_count_shortcode( $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'category'    => '',
				'unformatted' => 0,
			),
			normalize_empty_atts( $atts )
		);

		$args = array(
			'posts_per_page'           => -1,
			'post_type'                => 'wpm-testimonial',
			'post_status'              => 'publish',
			'wpm-testimonial-category' => $atts['category'],
			'suppress_filters'         => true,
		);
		$posts_array = get_posts( $args );

		if ( $atts['unformatted'] ) {
			return count( $posts_array );
		}

		return number_format_i18n( count( $posts_array ) );
	}

	/**
	 * Do not texturize shortcode.
	 *
	 * @since 1.11.5
	 *
	 * @param $shortcodes
	 *
	 * @return array
	 */
	public function no_texturize_shortcodes( $shortcodes ) {
		$shortcodes[] = $this->shortcode;

		return $shortcodes;
	}

}
