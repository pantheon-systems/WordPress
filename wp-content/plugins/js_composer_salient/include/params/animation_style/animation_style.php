<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_ParamAnimation
 *
 * For working with animations
 * array(
 *        'type' => 'animation_style',
 *        'heading' => __( 'Animation', 'js_composer' ),
 *        'param_name' => 'animation',
 * ),
 * Preview in http://daneden.github.io/animate.css/
 * @since 4.4
 */
class Vc_ParamAnimation {
	/**
	 * @since 4.4
	 * @var array $settings parameter settings from vc_map
	 */
	protected $settings;
	/**
	 * @since 4.4
	 * @var string $value parameter value
	 */
	protected $value;

	/**
	 * Define available animation effects
	 * @since 4.4
	 * vc_filter: vc_param_animation_style_list - to override animation styles
	 *     array
	 * @return array
	 */
	protected function animationStyles() {
		$styles = array(
			array(
				'values' => array(
					__( 'None', 'js_composer' ) => 'none',
				),
			),
			array(
				'label' => __( 'Attention Seekers', 'js_composer' ),
				'values' => array(
					// text to display => value
					__( 'bounce', 'js_composer' ) => array(
						'value' => 'bounce',
						'type' => 'other',
					),
					__( 'flash', 'js_composer' ) => array(
						'value' => 'flash',
						'type' => 'other',
					),
					__( 'pulse', 'js_composer' ) => array(
						'value' => 'pulse',
						'type' => 'other',
					),
					__( 'rubberBand', 'js_composer' ) => array(
						'value' => 'rubberBand',
						'type' => 'other',
					),
					__( 'shake', 'js_composer' ) => array(
						'value' => 'shake',
						'type' => 'other',
					),
					__( 'swing', 'js_composer' ) => array(
						'value' => 'swing',
						'type' => 'other',
					),
					__( 'tada', 'js_composer' ) => array(
						'value' => 'tada',
						'type' => 'other',
					),
					__( 'wobble', 'js_composer' ) => array(
						'value' => 'wobble',
						'type' => 'other',
					),
				),
			),
			array(
				'label' => __( 'Bouncing Entrances', 'js_composer' ),
				'values' => array(
					// text to display => value
					__( 'bounceIn', 'js_composer' ) => array(
						'value' => 'bounceIn',
						'type' => 'in',
					),
					__( 'bounceInDown', 'js_composer' ) => array(
						'value' => 'bounceInDown',
						'type' => 'in',
					),
					__( 'bounceInLeft', 'js_composer' ) => array(
						'value' => 'bounceInLeft',
						'type' => 'in',
					),
					__( 'bounceInRight', 'js_composer' ) => array(
						'value' => 'bounceInRight',
						'type' => 'in',
					),
					__( 'bounceInUp', 'js_composer' ) => array(
						'value' => 'bounceInUp',
						'type' => 'in',
					),
				),
			),
			array(
				'label' => __( 'Bouncing Exits', 'js_composer' ),
				'values' => array(
					// text to display => value
					__( 'bounceOut', 'js_composer' ) => array(
						'value' => 'bounceOut',
						'type' => 'out',
					),
					__( 'bounceOutDown', 'js_composer' ) => array(
						'value' => 'bounceOutDown',
						'type' => 'out',
					),
					__( 'bounceOutLeft', 'js_composer' ) => array(
						'value' => 'bounceOutLeft',
						'type' => 'out',
					),
					__( 'bounceOutRight', 'js_composer' ) => array(
						'value' => 'bounceOutRight',
						'type' => 'out',
					),
					__( 'bounceOutUp', 'js_composer' ) => array(
						'value' => 'bounceOutUp',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => __( 'Fading Entrances', 'js_composer' ),
				'values' => array(
					// text to display => value
					__( 'fadeIn', 'js_composer' ) => array(
						'value' => 'fadeIn',
						'type' => 'in',
					),
					__( 'fadeInDown', 'js_composer' ) => array(
						'value' => 'fadeInDown',
						'type' => 'in',
					),
					__( 'fadeInDownBig', 'js_composer' ) => array(
						'value' => 'fadeInDownBig',
						'type' => 'in',
					),
					__( 'fadeInLeft', 'js_composer' ) => array(
						'value' => 'fadeInLeft',
						'type' => 'in',
					),
					__( 'fadeInLeftBig', 'js_composer' ) => array(
						'value' => 'fadeInLeftBig',
						'type' => 'in',
					),
					__( 'fadeInRight', 'js_composer' ) => array(
						'value' => 'fadeInRight',
						'type' => 'in',
					),
					__( 'fadeInRightBig', 'js_composer' ) => array(
						'value' => 'fadeInRightBig',
						'type' => 'in',
					),
					__( 'fadeInUp', 'js_composer' ) => array(
						'value' => 'fadeInUp',
						'type' => 'in',
					),
					__( 'fadeInUpBig', 'js_composer' ) => array(
						'value' => 'fadeInUpBig',
						'type' => 'in',
					),
				),
			),
			array(
				'label' => __( 'Fading Exits', 'js_composer' ),
				'values' => array(
					__( 'fadeOut', 'js_composer' ) => array(
						'value' => 'fadeOut',
						'type' => 'out',
					),
					__( 'fadeOutDown', 'js_composer' ) => array(
						'value' => 'fadeOutDown',
						'type' => 'out',
					),
					__( 'fadeOutDownBig', 'js_composer' ) => array(
						'value' => 'fadeOutDownBig',
						'type' => 'out',
					),
					__( 'fadeOutLeft', 'js_composer' ) => array(
						'value' => 'fadeOutLeft',
						'type' => 'out',
					),
					__( 'fadeOutLeftBig', 'js_composer' ) => array(
						'value' => 'fadeOutLeftBig',
						'type' => 'out',
					),
					__( 'fadeOutRight', 'js_composer' ) => array(
						'value' => 'fadeOutRight',
						'type' => 'out',
					),
					__( 'fadeOutRightBig', 'js_composer' ) => array(
						'value' => 'fadeOutRightBig',
						'type' => 'out',
					),
					__( 'fadeOutUp', 'js_composer' ) => array(
						'value' => 'fadeOutUp',
						'type' => 'out',
					),
					__( 'fadeOutUpBig', 'js_composer' ) => array(
						'value' => 'fadeOutUpBig',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => __( 'Flippers', 'js_composer' ),
				'values' => array(
					__( 'flip', 'js_composer' ) => array(
						'value' => 'flip',
						'type' => 'other',
					),
					__( 'flipInX', 'js_composer' ) => array(
						'value' => 'flipInX',
						'type' => 'in',
					),
					__( 'flipInY', 'js_composer' ) => array(
						'value' => 'flipInY',
						'type' => 'in',
					),
					__( 'flipOutX', 'js_composer' ) => array(
						'value' => 'flipOutX',
						'type' => 'out',
					),
					__( 'flipOutY', 'js_composer' ) => array(
						'value' => 'flipOutY',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => __( 'Lightspeed', 'js_composer' ),
				'values' => array(
					__( 'lightSpeedIn', 'js_composer' ) => array(
						'value' => 'lightSpeedIn',
						'type' => 'in',
					),
					__( 'lightSpeedOut', 'js_composer' ) => array(
						'value' => 'lightSpeedOut',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => __( 'Rotating Entrances', 'js_composer' ),
				'values' => array(
					__( 'rotateIn', 'js_composer' ) => array(
						'value' => 'rotateIn',
						'type' => 'in',
					),
					__( 'rotateInDownLeft', 'js_composer' ) => array(
						'value' => 'rotateInDownLeft',
						'type' => 'in',
					),
					__( 'rotateInDownRight', 'js_composer' ) => array(
						'value' => 'rotateInDownRight',
						'type' => 'in',
					),
					__( 'rotateInUpLeft', 'js_composer' ) => array(
						'value' => 'rotateInUpLeft',
						'type' => 'in',
					),
					__( 'rotateInUpRight', 'js_composer' ) => array(
						'value' => 'rotateInUpRight',
						'type' => 'in',
					),
				),
			),
			array(
				'label' => __( 'Rotating Exits', 'js_composer' ),
				'values' => array(
					__( 'rotateOut', 'js_composer' ) => array(
						'value' => 'rotateOut',
						'type' => 'out',
					),
					__( 'rotateOutDownLeft', 'js_composer' ) => array(
						'value' => 'rotateOutDownLeft',
						'type' => 'out',
					),
					__( 'rotateOutDownRight', 'js_composer' ) => array(
						'value' => 'rotateOutDownRight',
						'type' => 'out',
					),
					__( 'rotateOutUpLeft', 'js_composer' ) => array(
						'value' => 'rotateOutUpLeft',
						'type' => 'out',
					),
					__( 'rotateOutUpRight', 'js_composer' ) => array(
						'value' => 'rotateOutUpRight',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => __( 'Specials', 'js_composer' ),
				'values' => array(
					__( 'hinge', 'js_composer' ) => array(
						'value' => 'hinge',
						'type' => 'out',
					),
					__( 'rollIn', 'js_composer' ) => array(
						'value' => 'rollIn',
						'type' => 'in',
					),
					__( 'rollOut', 'js_composer' ) => array(
						'value' => 'rollOut',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => __( 'Zoom Entrances', 'js_composer' ),
				'values' => array(
					__( 'zoomIn', 'js_composer' ) => array(
						'value' => 'zoomIn',
						'type' => 'in',
					),
					__( 'zoomInDown', 'js_composer' ) => array(
						'value' => 'zoomInDown',
						'type' => 'in',
					),
					__( 'zoomInLeft', 'js_composer' ) => array(
						'value' => 'zoomInLeft',
						'type' => 'in',
					),
					__( 'zoomInRight', 'js_composer' ) => array(
						'value' => 'zoomInRight',
						'type' => 'in',
					),
					__( 'zoomInUp', 'js_composer' ) => array(
						'value' => 'zoomInUp',
						'type' => 'in',
					),
				),
			),
			array(
				'label' => __( 'Zoom Exits', 'js_composer' ),
				'values' => array(
					__( 'zoomOut', 'js_composer' ) => array(
						'value' => 'zoomOut',
						'type' => 'out',
					),
					__( 'zoomOutDown', 'js_composer' ) => array(
						'value' => 'zoomOutDown',
						'type' => 'out',
					),
					__( 'zoomOutLeft', 'js_composer' ) => array(
						'value' => 'zoomOutLeft',
						'type' => 'out',
					),
					__( 'zoomOutRight', 'js_composer' ) => array(
						'value' => 'zoomOutRight',
						'type' => 'out',
					),
					__( 'zoomOutUp', 'js_composer' ) => array(
						'value' => 'zoomOutUp',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => __( 'Slide Entrances', 'js_composer' ),
				'values' => array(
					__( 'slideInDown', 'js_composer' ) => array(
						'value' => 'slideInDown',
						'type' => 'in',
					),
					__( 'slideInLeft', 'js_composer' ) => array(
						'value' => 'slideInLeft',
						'type' => 'in',
					),
					__( 'slideInRight', 'js_composer' ) => array(
						'value' => 'slideInRight',
						'type' => 'in',
					),
					__( 'slideInUp', 'js_composer' ) => array(
						'value' => 'slideInUp',
						'type' => 'in',
					),
				),
			),
			array(
				'label' => __( 'Slide Exits', 'js_composer' ),
				'values' => array(
					__( 'slideOutDown', 'js_composer' ) => array(
						'value' => 'slideOutDown',
						'type' => 'out',
					),
					__( 'slideOutLeft', 'js_composer' ) => array(
						'value' => 'slideOutLeft',
						'type' => 'out',
					),
					__( 'slideOutRight', 'js_composer' ) => array(
						'value' => 'slideOutRight',
						'type' => 'out',
					),
					__( 'slideOutUp', 'js_composer' ) => array(
						'value' => 'slideOutUp',
						'type' => 'out',
					),
				),
			),
		);

		/**
		 * Used to override animation style list
		 * @since 4.4
		 */

		return apply_filters( 'vc_param_animation_style_list', $styles );
	}

	/**
	 * @param array $styles - array of styles to group
	 * @param string|array $type - what type to return
	 *
	 * @since 4.4
	 * @return array
	 */
	public function groupStyleByType( $styles, $type ) {
		$grouped = array();
		foreach ( $styles as $group ) {
			$inner_group = array( 'values' => array() );
			if ( isset( $group['label'] ) ) {
				$inner_group['label'] = $group['label'];
			}
			foreach ( $group['values'] as $key => $value ) {
				if ( ( is_array( $value ) && isset( $value['type'] ) && ( ( is_string( $type ) && $value['type'] == $type ) || is_array( $type ) && in_array( $value['type'], $type ) ) ) || ! is_array( $value ) || ! isset( $value['type'] ) ) {
					$inner_group['values'][ $key ] = $value;
				}
			}
			if ( ! empty( $inner_group['values'] ) ) {
				$grouped[] = $inner_group;
			}
		}

		return $grouped;
	}

	/**
	 * Set variables and register animate-css asset
	 * @since 4.4
	 *
	 * @param $settings
	 * @param $value
	 */
	public function __construct( $settings, $value ) {
		$this->settings = $settings;
		$this->value = $value;
		wp_register_style( 'animate-css', vc_asset_url( 'lib/bower/animate-css/animate.min.css' ), array(), WPB_VC_VERSION );
	}

	/**
	 * Render edit form output
	 * @since 4.4
	 * @return string
	 */
	public function render() {
		$output = '<div class="vc_row">';
		wp_enqueue_style( 'animate-css' );

		$styles = $this->animationStyles();
		if ( isset( $this->settings['settings']['type'] ) ) {
			$styles = $this->groupStyleByType( $styles, $this->settings['settings']['type'] );
		}
		if ( isset( $this->settings['settings']['custom'] ) && is_array( $this->settings['settings']['custom'] ) ) {
			$styles = array_merge( $styles, $this->settings['settings']['custom'] );
		}

		if ( is_array( $styles ) && ! empty( $styles ) ) {
			$left_side = '<div class="vc_col-sm-6">';
			$build_style_select = "\n" . '<select class="vc_param-animation-style">' . "\n";
			foreach ( $styles as $style ) {
				$build_style_select .= "\t\t" . '<optgroup ' . ( isset( $style['label'] ) ? 'label="' . $style['label'] . '"' : '' ) . '>' . "\n";
				if ( is_array( $style['values'] ) && ! empty( $style['values'] ) ) {
					foreach ( $style['values'] as $key => $value ) {
						$build_style_select .= "\t\t\t" . '<option value="' . ( is_array( $value ) ? $value['value'] : $value ) . '">' . $key . '</option>' . "\n";
					}
				}
				$build_style_select .= "\t\t" . '</optgroup>' . "\n";
			}
			$build_style_select .= '</select>' . "\n";
			$left_side .= $build_style_select;
			$left_side .= '</div>'; // Close left_side div
			$output .= $left_side;

			$right_side = '<div class="vc_col-sm-6">';
			$right_side .= '<div class="vc_param-animation-style-preview"><button class="vc_btn vc_btn-grey vc_btn-sm vc_param-animation-style-trigger">' . __( 'Animate it', 'js_composer' ) . '</button></div>';
			$right_side .= '</div>'; // Close right_side div
			$output .= $right_side;
		}

		$output .= '</div>'; // Close Row
		$output .= '<input name="' . $this->settings['param_name'] . '" class="wpb_vc_param_value  ' . $this->settings['param_name'] . ' ' . $this->settings['type'] . '_field" type="hidden" value="' . $this->value . '" ' . ' />';

		return $output;
	}
}

/**
 * Function for rendering param in edit form (add element)
 * Parse settings from vc_map and entered 'values'.
 *
 * @param array $settings - parameter settings in vc_map
 * @param string $value - parameter value
 * @param string $tag - shortcode tag
 *
 * vc_filter: vc_animation_style_render_filter - filter to override editor form
 *     field output
 *
 * @since 4.4
 * @return mixed|void rendered template for params in edit form
 *
 */
function vc_animation_style_form_field( $settings, $value, $tag ) {

	$field = new Vc_ParamAnimation( $settings, $value, $tag );

	/**
	 * Filter used to override full output of edit form field animation style
	 * @since 4.4
	 */

	return apply_filters( 'vc_animation_style_render_filter', $field->render(), $settings, $value, $tag );
}

