<?php

/**
 * Class WPML_Gutenberg_Config_Option
 */
class WPML_Gutenberg_Config_Option {

	const OPTION = 'wpml-gutenberg-config';

	/**
	 * @param array $config_data
	 */
	public function update_from_config( $config_data ) {
		$blocks = array();

		if ( isset( $config_data['wpml-config']['gutenberg-blocks']['gutenberg-block'] ) ) {
			foreach ( $config_data['wpml-config']['gutenberg-blocks']['gutenberg-block'] as $block_config ) {
				$blocks[ $block_config['attr']['type'] ] = array();
				if( '1' === $block_config['attr']['translate'] && isset( $block_config['xpath'] ) ) {
					foreach ( $block_config['xpath'] as $xpaths ) {
						if ( is_array( $xpaths ) ) {
							$blocks[ $block_config['attr']['type'] ] = array_merge( $blocks[ $block_config['attr']['type'] ], array_values( $xpaths ) );
						} else {
							$blocks[ $block_config['attr']['type'] ][] = $xpaths;
						}
					}
				}
			}
		}

		update_option( self::OPTION, $blocks );
	}

	public function get() {
		return get_option( self::OPTION, array() );
	}
}
