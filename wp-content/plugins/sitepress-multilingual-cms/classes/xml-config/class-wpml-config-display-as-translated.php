<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 24/10/17
 * Time: 11:02 AM
 */

class WPML_Config_Display_As_Translated {

	/**
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/wpmlcore-4859
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/wpmlcore-4941
	 *
	 * @param array $config
	 *
	 * @return array
	 */
	public static function merge_to_translate_mode( $config ) {
		$config = self::merge_to_translate_mode_for_key( $config, 'custom-types', 'custom-type' );
		$config = self::merge_to_translate_mode_for_key( $config, 'taxonomies', 'taxonomy' );

		return $config;
	}

	private static function merge_to_translate_mode_for_key( $config, $key_plural, $key_singular ) {
		if ( isset( $config['wpml-config'][ $key_plural ][ $key_singular ] ) ) {
			foreach ( $config['wpml-config'][ $key_plural ][ $key_singular ] as $index => $settings ) {
				if ( isset( $settings['attr'] ) &&
				     array_key_exists( 'translate', $settings['attr'] ) &&
				     array_key_exists( 'display-as-translated', $settings['attr'] )
				) {
					if ( WPML_CONTENT_TYPE_TRANSLATE == $settings['attr']['translate'] &&
					     1 == $settings['attr']['display-as-translated']
					) {
						$settings['attr']['translate'] = WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED;
						unset( $settings['attr']['display-as-translated'] );
						$config['wpml-config'][ $key_plural ][ $key_singular ][ $index ] = $settings;
					}
				}
			}
		}
		return $config;
	}

}