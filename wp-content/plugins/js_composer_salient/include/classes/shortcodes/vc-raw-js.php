<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-raw-html.php' );

class WPBakeryShortCode_VC_Raw_js extends WPBakeryShortCode_VC_Raw_html {
	protected function getFileName() {
		return 'vc_raw_html';
	}

	protected function contentInline( $atts, $content = null ) {
		$el_class = $width = $el_position = '';
		extract( shortcode_atts( array(
			'el_class' => '',
			'el_position' => '',
			'width' => '1/2',
		), $atts ) );

		$el_class = $this->getExtraClass( $el_class );
		$el_class .= ' wpb_raw_js';
		$content = rawurldecode( base64_decode( strip_tags( $content ) ) );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_raw_code' . $el_class, $this->settings['base'], $atts );

		$output = '
			<div class="' . $css_class . '">
				<div class="wpb_wrapper">
					<textarea style="display: none;" class="vc_js_inline_holder">' . esc_attr( $content ) . '</textarea>
				</div>
			</div>
		';

		return $output;
	}
}
