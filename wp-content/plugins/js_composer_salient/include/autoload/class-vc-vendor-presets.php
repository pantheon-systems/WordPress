<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Singleton to hold all vendor presets
 *
 * @since 4.8
 */
class Vc_Vendor_Preset {

	private static $_instance;
	private static $presets = array();

	public static function getInstance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	protected function __construct() {
	}

	/**
	 * Add vendor preset to collection
	 *
	 * @since 4.8
	 *
	 * @param string $title
	 * @param string $shortcode
	 * @param array $params
	 * @param bool $default
	 *
	 * @return bool
	 */
	public function add( $title, $shortcode, $params, $default = false ) {
		if ( ! $title || ! is_string( $title ) || ! $shortcode || ! is_string( $shortcode ) || ! $params || ! is_array( $params ) ) {
			return false;
		}

		$preset = array(
			'shortcode' => $shortcode,
			'default' => $default,
			'params' => $params,
			'title' => $title,
		);

		$id = md5( serialize( $preset ) );

		self::$presets[ $id ] = $preset;

		return true;
	}

	/**
	 * Get specific vendor preset
	 *
	 * @since 4.8
	 *
	 * @param string $id
	 *
	 * @return mixed array|false
	 */
	public function get( $id ) {
		if ( isset( self::$presets[ $id ] ) ) {
			return self::$presets[ $id ];
		}

		return false;
	}

	/**
	 * Get all vendor presets for specific shortcode
	 *
	 * @since 4.8
	 *
	 * @param string $shortcode
	 *
	 * @return array
	 */
	public function getAll( $shortcode ) {
		$list = array();

		foreach ( self::$presets as $id => $preset ) {
			if ( $shortcode === $preset['shortcode'] ) {
				$list[ $id ] = $preset;
			}
		}

		return $list;
	}

	/**
	 * Get all default vendor presets
	 *
	 * Include only one default preset per shortcode
	 *
	 * @since 4.8
	 *
	 * @return array
	 */
	public function getDefaults() {
		$list = array();

		$added = array();

		foreach ( self::$presets as $id => $preset ) {
			if ( $preset['default'] && ! in_array( $preset['shortcode'], $added ) ) {
				$added[] = $preset['shortcode'];
				$list[ $id ] = $preset;
			}
		}

		return $list;
	}

	/**
	 * Get ID of default preset for specific shortcode
	 *
	 * If multiple presets are default, return first
	 *
	 * @since 4.8
	 *
	 * @param string $shortcode
	 *
	 * @return string|null
	 */
	public function getDefaultId( $shortcode ) {
		foreach ( self::$presets as $id => $preset ) {
			if ( $shortcode === $preset['shortcode'] && $preset['default'] ) {
				return $id;
			}
		}

		return null;
	}
}
