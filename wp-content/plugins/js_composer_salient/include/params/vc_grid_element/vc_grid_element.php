<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


class Vc_Grid_Element {
	protected $template = '';
	protected $html_template = false;
	protected $post = false;
	protected $attributes = array();
	protected $grid_atts = array();
	protected $is_end = false;
	protected static $templates_added = false;
	protected $shortcodes = array(
		'vc_gitem_row',
		'vc_gitem_col',
		'vc_gitem_post_title',
		'vc_gitem_icon',
	);

	public function shortcodes() {
		return $this->shortcodes;
	}

	function setTemplate( $template ) {
		$this->template = $template;
		$this->parseTemplate( $template );
	}

	function template() {
		return $this->template;
	}

	public function parseTemplate( $template ) {
		$this->setShortcodes();
		$this->html_template = do_shortcode( $template );
	}

	function renderItem( WP_Post $post ) {
		$attributes = $this->attributes();
		$pattern = array();
		$replacement = array();
		foreach ( $attributes as $attr ) {
			$pattern[] = '/\{\{' . preg_quote( $attr, '' ) . '\}\}/';
			$replacement[] = $this->attribute( $attr, $post );
		}
		$css_class_items = 'vc_grid-item ' . ( $this->isEnd() ? ' vc_grid-last-item ' : '' )
		                   . ' vc_grid-thumb vc_theme-thumb-full-overlay vc_animation-slide-left vc_col-sm-'
		                   . $this->gridAttribute( 'element_width', 12 );
		foreach ( $post->filter_terms as $t ) {
			$css_class_items .= ' vc_grid-term-' . $t;
		}

		return '<div class="'
		       . $css_class_items
		       . '">' . "\n" . preg_replace( $pattern, $replacement, $this->html_template )
		       . "\n" . '</div>' . "\n";
	}

	public function renderParam() {
		$output = '<div class="vc_grid-element-constructor" data-vc-grid-element="builder"></div>'
		          . '<a href="#" data-vc-control="add-row">' . __( 'Add row', 'js_composer' ) . '</a>';
		if ( false === self::$templates_added ) {
			foreach ( $this->shortcodes as $tag ) {
				$method = vc_camel_case( $tag . '_template' );
				if ( method_exists( $this, $method ) ) {
					$content = $this->$method();
				} else {
					$content = $this->vcDefaultTemplate( $tag );
				}
				$output .= '<script type="text/template" data-vc-grid-element-template="'
				           . esc_attr( $tag ) . '">' . $content . '</script>';
				$output .= '<script type="text/template" data-vc-grid-element-template="modal">'
				           . '<div class="vc_grid-element-modal-title"><# title #></div>'
				           . '<div class="vc_grid-element-modal-controls"><# controls #></div>'
				           . '<div class="vc_grid-element-modal-body"><# body #></div>'
				           . '</script>';
			}
			self::$templates_added = true;
		}

		return $output;
	}

	public function setGridAttributes( $grid_atts ) {
		$this->grid_atts = $grid_atts;
	}

	public function gridAttribute( $name, $default = '' ) {
		return isset( $this->grid_atts[ $name ] ) ? $this->grid_atts[ $name ] : $default;
	}

	public function setAttribute( $name ) {
		$this->attributes[] = $name;
	}

	public function attributes() {
		return $this->attributes;
	}

	public function attribute( $name, $post ) {
		if ( method_exists( $this, 'attribute' . ucfirst( $name ) ) ) {
			$method_name = 'attribute' . ucfirst( $name );

			return $this->$method_name( $post );
		}
		if ( isset( $post->$name ) ) {
			return $post->$name;
		}

		return '';
	}

	public function setIsEnd( $is_end = true ) {
		$this->is_end = $is_end;
	}

	public function isEnd() {
		return $this->is_end;
	}

	/**
	 * Set elements templates.
	 */
	protected function setShortcodes() {
		foreach ( $this->shortcodes as $tag ) {
			add_shortcode( $tag, array( $this, vc_camel_case( $tag . '_shortcode' ) ) );
		}
	}

	// Templates {{
	public function vcGitemRowShortcode( $atts, $content = '' ) {
		return '<div class="vc_row vc_gitem-row'
		       . $this->gridAttribute( 'element_width' ) . '">'
		       . "\n" . do_shortcode( $content ) . "\n" . '</div>';
	}

	public function vcGitemRowTemplate() {
		$output = '<div class="vc_gitem-wrapper">';
		$output .= '<div class="vc_t-grid-controls vc_t-grid-controls-row" data-vc-element-shortcode="controls">';
		// Move control
		$output .= '<a class="vc_t-grid-control vc_t-grid-control-move" href="#" title="'
		           . __( 'Drag row to reorder', 'js_composer' ) . '" data-vc-element-control="move"><i class="vc_t-grid-icon vc_t-grid-icon-move"></i></a>';
		// Layout control
		$output .= '<span class="vc_t-grid-control vc_t-grid-control-layouts" style="display: none;">'
		           // vc_col-sm-12
		           . '<a class="vc_t-grid-control vc_t-grid-control-layout" data-cells="12" title="'
		           . '1/1' . '" data-vc-element-control="layouts">'
		           . '<i class="vc_t-grid-icon vc_t-grid-icon-layout-12"></i></a>'
		           // vc_col-sm-6 + vc_col-sm-6
		           . '<a class="vc_t-grid-control vc_t-grid-control-layout" data-cells="6_6" title="'
		           . '1/2 + 1/2' . '" data-vc-element-control="layouts">'
		           . '<i class="vc_t-grid-icon vc_t-grid-icon-layout-6-6"></i></a>'
		           // vc_col-sm-4 + vc_col-sm-4 + vc_col-sm-4
		           . '<a class="vc_t-grid-control vc_t-grid-control-layout" data-cells="4_4_4" title="'
		           . '1/3 + 1/3 + 1/3' . '" data-vc-element-control="layouts">'
		           . '<i class="vc_t-grid-icon vc_t-grid-icon-layout-4-4-4"></i></a>'
		           . '</span>'
		           . '<span class="vc_pull-right">'
		           // Destroy control
		           . '<a class="vc_t-grid-control vc_t-grid-control-destroy" href="#" title="'
		           . __( 'Delete this row', 'js_composer' ) . '" data-vc-element-control="destroy">'
		           . '<i class="vc_t-grid-icon vc_t-grid-icon-destroy"></i>'
		           . '</a>'
		           . '</span>';
		$output .= '</div>';
		$output .= '<div data-vc-element-shortcode="content" class="vc_row vc_gitem-content"></div>';
		$output .= '</div>';

		return $output;
	}

	public function vcGitemColShortcode( $atts, $content = '' ) {
		$width = '12';
		$atts = shortcode_atts( array(
			'width' => '12',
		), $atts );
		extract( $atts );

		return '<div class="vc_col-sm-' . $width . ' vc_gitem-col">'
		       . "\n" . do_shortcode( $content ) . "\n" . '</div>';
	}

	public function vcGitemColTemplate() {
		$output = '<div class="vc_gitem-wrapper">';
		// Controls
		// Control "Add"
		$controls = '<a class="vc_t-grid-control vc_t-grid-control-add" href="#" title="'
		            . __( 'Prepend to this column', 'js_composer' ) . '" data-vc-element-control="add">'
		            . '<i class="vc_t-grid-icon vc_t-grid-icon-add"></i>'
		            . '</a>';
		$output .= '<div class="vc_t-grid-controls vc_t-grid-controls-col" data-vc-element-shortcode="controls">'
		           . $controls
		           . '</div>';
		// Content
		$output .= '<div data-vc-element-shortcode="content" class="vc_gitem-content">'
		           . '</div>';
		/*
				$output .= '<div class="vc_t-grid-controls vc_t-grid-controls-col vc_t-grid-controls-bottom">'
							.$controls
						. '</div>';
		*/
		$output .= '</div>';

		return $output;
	}

	public function vcGitemPostTitleShortcode( $atts, $content = '' ) {
		$atts = shortcode_atts( array(), $atts );
		extract( $atts );
		$this->setAttribute( 'post_title' );

		return '<h3 data-vc-element-shortcode="content" class="vc_ptitle">{{post_title}}</h3>';
	}

	public function vcDefaultTemplate( $tag ) {
		$name = preg_replace( '/^vc_gitem_/', '', $tag );
		$title = ucfirst( preg_replace( '/\_/', ' ', $name ) );

		return '<div class="vc_gitem-wrapper">'
		       . $this->elementControls( $title, preg_match( '/^post/', $name ) ? 'orange' : 'green' )
		       . '</div>';
	}

	protected function elementControls( $title, $theme = null ) {
		return '<div class="vc_t-grid-controls vc_t-grid-controls-element'
		       . ( is_string( $theme ) ? ' vc_th-controls-element-' . $theme : '' )
		       . '" data-vc-element-shortcode="controls">'
		       // Move control
		       . '<a class="vc_t-grid-control vc_t-grid-control-move" href="#" title="'
		       . __( 'Drag to reorder', 'js_composer' ) . '" data-vc-element-control="move">'
		       . '<i class="vc_t-grid-icon vc_t-grid-icon-move"></i>'
		       . '</a>'
		       // Label
		       . '<span class="vc_t-grid-control vc_t-grid-control-name" data-vc-element-control="name">
					' . $title
		       . '</span>'
		       // Edit control
		       . '<a class="vc_t-grid-control vc_t-grid-control-edit" data-vc-element-control="edit">'
		       . '<i class="vc_t-grid-icon vc_t-grid-icon-edit"></i>'
		       . '</a>'
		       // Delete control
		       . '<a class="vc_t-grid-control vc_t-grid-control-destroy" data-vc-element-control="destroy">'
		       . '<i class="vc_t-grid-icon vc_t-grid-icon-destroy"></i>'
		       . '</a>'
		       . '</div>';
	}
	// }}
}

function vc_vc_grid_element_form_field( $settings, $value ) {
	$grid_element = new Vc_Grid_Element();

	return '<div data-vc-grid-element="container" data-vc-grid-tags-list="'
	       . esc_attr( json_encode( $grid_element->shortcodes() ) ) . '">'
	       . '<input data-vc-grid-element="value" type="hidden" name="' . $settings['param_name']
	       . '" class="wpb_vc_param_value wpb-textinput '
	       . $settings['param_name'] . ' ' . $settings['type'] . '_field" '
	       . ' value="'
	       . esc_attr( $value ) . '">'
	       . $grid_element->renderParam()
	       . '</div>';
}

function vc_load_vc_grid_element_param() {
	vc_add_shortcode_param(
		'vc_grid_element',
		'vc_vc_grid_element_form_field'
	);
}

add_action( 'vc_load_default_params', 'vc_load_vc_grid_element_param' );
