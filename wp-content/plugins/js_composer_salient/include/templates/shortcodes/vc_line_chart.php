<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $title
 * @var $el_class
 * @var $el_id
 * @var $type
 * @var $style
 * @var $legend
 * @var $animation
 * @var $tooltips
 * @var $x_values
 * @var $values
 * @var $css
 * @var $css_animation
 * Shortcode class
 * @var $this WPBakeryShortCode_Vc_Line_Chart
 */
$el_class = $el_id = $title = $type = $legend = $style = $tooltips = $animation = $x_values = $values = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$base_colors = array(
	'normal' => array(
		'blue' => '#5472d2',
		'turquoise' => '#00c1cf',
		'pink' => '#fe6c61',
		'violet' => '#8d6dc4',
		'peacoc' => '#4cadc9',
		'chino' => '#cec2ab',
		'mulled-wine' => '#50485b',
		'vista-blue' => '#75d69c',
		'orange' => '#f7be68',
		'sky' => '#5aa1e3',
		'green' => '#6dab3c',
		'juicy-pink' => '#f4524d',
		'sandy-brown' => '#f79468',
		'purple' => '#b97ebb',
		'black' => '#2a2a2a',
		'grey' => '#ebebeb',
		'white' => '#ffffff',
		'default' => '#f7f7f7',
		'primary' => '#0088cc',
		'info' => '#58b9da',
		'success' => '#6ab165',
		'warning' => '#ff9900',
		'danger' => '#ff675b',
		'inverse' => '#555555',
	),
	'active' => array(
		'blue' => '#3c5ecc',
		'turquoise' => '#00a4b0',
		'pink' => '#fe5043',
		'violet' => '#7c57bb',
		'peacoc' => '#39a0bd',
		'chino' => '#c3b498',
		'mulled-wine' => '#413a4a',
		'vista-blue' => '#5dcf8b',
		'orange' => '#f5b14b',
		'sky' => '#4092df',
		'green' => '#5f9434',
		'juicy-pink' => '#f23630',
		'sandy-brown' => '#f57f4b',
		'purple' => '#ae6ab0',
		'black' => '#1b1b1b',
		'grey' => '#dcdcdc',
		'white' => '#f0f0f0',
		'default' => '#e8e8e8',
		'primary' => '#0074ad',
		'info' => '#3fafd4',
		'success' => '#59a453',
		'warning' => '#e08700',
		'danger' => '#ff4b3c',
		'inverse' => '#464646',
	),
);
$colors = array(
	'flat' => array(
		'normal' => $base_colors['normal'],
		'active' => $base_colors['active'],
	),
);
foreach ( $base_colors['normal'] as $name => $color ) {
	$colors['modern']['normal'][ $name ] = array( vc_colorCreator( $color, 7 ), $color );
}
foreach ( $base_colors['active'] as $name => $color ) {
	$colors['modern']['active'][ $name ] = array( vc_colorCreator( $color, 7 ), $color );
}

wp_enqueue_script( 'vc_line_chart' );

$class_to_filter = 'vc_chart vc_line-chart wpb_content_element';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$options = array();

if ( ! empty( $legend ) ) {
	$options[] = 'data-vc-legend="1"';
}

if ( ! empty( $tooltips ) ) {
	$options[] = 'data-vc-tooltips="1"';
}

if ( ! empty( $animation ) ) {
	$options[] = 'data-vc-animation="' . esc_attr( str_replace( 'easein', 'easeIn', $animation ) ) . '"';
}

$values = (array) vc_param_group_parse_atts( $values );
$data = array(
	'labels' => explode( ';', trim( $x_values, ';' ) ),
	'datasets' => array(),
);

foreach ( $values as $k => $v ) {

	if ( 'custom' === $style ) {
		if ( ! empty( $v['custom_color'] ) ) {
			$color = $v['custom_color'];
			$highlight = vc_colorCreator( $v['custom_color'], - 10 ); //10% darker
		} else {
			$color = 'grey';
			$highlight = 'grey';
		}
	} else {
		$color = isset( $colors[ $style ]['normal'][ $v['color'] ] ) ? $colors[ $style ]['normal'][ $v['color'] ] : $v['normal']['color'];
		$highlight = isset( $colors[ $style ]['active'][ $v['color'] ] ) ? $colors[ $style ]['active'][ $v['color'] ] : $v['active']['color'];
	}

	// don't use gradients for lines
	if ( 'line' === $type ) {
		$color = is_array( $color ) ? end( $color ) : $color;
		$highlight = is_array( $highlight ) ? end( $highlight ) : $highlight;
		$rgb = vc_hex2rgb( $color );
		$fill_color = 'rgba(' . $rgb[0] . ', ' . $rgb[1] . ', ' . $rgb[2] . ', 0.1)';
	} else {
		$fill_color = $color;
	}

	if ( 'modern' === $style ) {
		$stroke_color = vc_colorCreator( is_array( $color ) ? end( $color ) : $color, - 7 );
		$highlight_stroke_color = vc_colorCreator( $stroke_color, - 7 );
	} else {
		$stroke_color = $color;
		$highlight_stroke_color = $highlight;
	}

	$data['datasets'][] = array(
		'label' => isset( $v['title'] ) ? $v['title'] : '',
		'fillColor' => $fill_color,
		'strokeColor' => $stroke_color,
		'pointColor' => $color,
		'pointStrokeColor' => $color,
		'highlightFill' => $highlight,
		'highlightStroke' => $highlight_stroke_color,
		'pointHighlightFill' => $highlight_stroke_color,
		'pointHighlightStroke' => $highlight_stroke_color,
		'data' => explode( ';', isset( $v['y_values'] ) ? trim( $v['y_values'], ';' ) : '' ),
	);
}

$options[] = 'data-vc-type="' . esc_attr( $type ) . '"';
$options[] = 'data-vc-values="' . htmlentities( json_encode( $data ) ) . '"';

if ( '' !== $title ) {
	$title = '<h2 class="wpb_heading">' . $title . '</h4>';
}

$canvas_html = '<canvas class="vc_line-chart-canvas" width="1" height="1"></canvas>';
$legend_html = '';
if ( $legend ) {
	foreach ( $data['datasets'] as $v ) {
		$color = is_array( $v['pointColor'] ) ? current( $v['pointColor'] ) : $v['pointColor'];
		$legend_html .= '<li><span style="background-color:' . $color . '"></span>' . $v['label'] . '</li>';
	}
	$legend_html = '<ul class="vc_chart-legend">' . $legend_html . '</ul>';
	$canvas_html = '<div class="vc_chart-with-legend">' . $canvas_html . '</div>';
}
if ( ! empty( $el_id ) ) {
	$options[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output = '
<div class="' . esc_attr( $css_class ) . '" ' . implode( ' ', $options ) . '>
	' . $title . '
	<div class="wpb_wrapper">
		' . $canvas_html . $legend_html . '
	</div>' . '
</div>' . '
';

echo $output;
