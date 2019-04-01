<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-custom-heading.php' );

class WPBakeryShortCode_VC_Gitem_Post_Data extends WPBakeryShortCode_VC_Custom_heading {
	/**
	 * Get data_source attribute value
	 *
	 * @param array $atts - list of shortcode attributes
	 *
	 * @return string
	 */
	public function getDataSource( array $atts ) {
		return isset( $atts['data_source'] ) ? $atts['data_source'] : 'post_title';
	}

	public function getAttributes( $atts ) {
		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		if ( isset( $atts['block_container'] ) && strlen( $atts['block_container'] ) > 0 ) {
			if ( ! isset( $atts['font_container'] ) ) {
				$atts['font_container'] = $atts['block_container'];
			} else {
				// merging two params into font_container
				$atts['font_container'] .= '|' . $atts['block_container'];
			}
		}
		$atts = parent::getAttributes( $atts );
		if ( ! isset( $this->atts['use_custom_fonts'] ) || 'yes' !== $this->atts['use_custom_fonts'] ) {
			$atts['google_fonts_data'] = array();
		}

		return $atts;
	}
}
