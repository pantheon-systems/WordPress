<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Author: Arlo Carreon <http://arlocarreon.com>
 * Info: http://mexitek.github.io/phpColors/
 * License: http://arlo.mit-license.org/
 *
 * @modified by js_composer
 * @since 4.8
 */
class Vc_Color_Helper {

	/**
	 * A color utility that helps manipulate HEX colors
	 */

	private $_hex;
	private $_hsl;
	private $_rgb;

	/**
	 * Auto darkens/lightens by 10% for sexily-subtle gradients.
	 * Set this to FALSE to adjust automatic shade to be between given color
	 * and black (for darken) or white (for lighten)
	 */
	const DEFAULT_ADJUST = 10;

	/**
	 * Instantiates the class with a HEX value
	 *
	 * @param string $hex
	 *
	 * @throws Exception "Bad color format"
	 */
	function __construct( $hex ) {
		// Strip # sign is present
		$color = str_replace( '#', '', $hex );

		// Make sure it's 6 digits
		if ( strlen( $color ) === 3 ) {
			$color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
		} else if ( strlen( $color ) != 6 ) {
			throw new Exception( 'HEX color needs to be 6 or 3 digits long' );
		}

		$this->_hsl = self::hexToHsl( $color );
		$this->_hex = $color;
		$this->_rgb = self::hexToRgb( $color );
	}

	public static function clamp( $val, $max = 1 ) {
		return min( max( $val, 0 ), $max );
	}

	// ====================
	// = Public Interface =
	// ====================

	/**
	 * Given a HEX string returns a HSL array equivalent.
	 *
	 * @param string $color
	 *
	 * @return array HSL associative array
	 */
	public static function hexToHsl( $color ) {

		// Sanity check
		$color = self::_checkHex( $color );

		// Convert HEX to DEC
		$R = hexdec( $color[0] . $color[1] );
		$G = hexdec( $color[2] . $color[3] );
		$B = hexdec( $color[4] . $color[5] );

		$HSL = array();

		$var_R = ( $R / 255.0 );
		$var_G = ( $G / 255.0 );
		$var_B = ( $B / 255.0 );

		$var_Min = min( $var_R, $var_G, $var_B );
		$var_Max = max( $var_R, $var_G, $var_B );
		$del_Max = floatval( $var_Max - $var_Min );

		$L = ( $var_Max + $var_Min ) / 2.0;

		$H = 0.0;
		$S = 0.0;

		if ( 0 != $del_Max ) {
			if ( $L < 0.5 ) {
				$S = $del_Max / ( $var_Max + $var_Min );
			} else {
				$S = $del_Max / ( 2 - $var_Max - $var_Min );
			}

			switch ( $var_Max ) {
				case $var_R:
					$H = ( $var_G - $var_B ) / $del_Max + ( $var_G < $var_B ? 6 : 0 );
					break;
				case $var_G:
					$H = ( $var_B - $var_R ) / $del_Max + 2;
					break;
				case $var_B:
					$H = ( $var_R - $var_G ) / $del_Max + 4;
					break;
			}

			$H /= 6;
		}

		$HSL['H'] = ( $H * 360.0 );
		$HSL['S'] = $S;
		$HSL['L'] = $L;

		return $HSL;
	}

	/**
	 *  Given a HSL associative array returns the equivalent HEX string
	 *
	 * @param array $hsl
	 *
	 * @return string HEX string
	 * @throws Exception "Bad HSL Array"
	 */
	public static function hslToHex( $hsl = array() ) {
		// Make sure it's HSL
		if ( empty( $hsl ) || ! isset( $hsl['H'] ) || ! isset( $hsl['S'] ) || ! isset( $hsl['L'] ) ) {
			throw new Exception( 'Param was not an HSL array' );
		}

		list( $H, $S, $L ) = array(
			fmod( $hsl['H'], 360 ) / 360.0,
			$hsl['S'],
			$hsl['L'],
		);

		if ( 0 == $S ) {
			$r = $L * 255.0;
			$g = $L * 255.0;
			$b = $L * 255.0;
		} else {

			if ( $L < 0.5 ) {
				$var_2 = $L * ( 1.0 + $S );
			} else {
				$var_2 = ( $L + $S ) - ( $S * $L );
			}

			$var_1 = 2.0 * $L - $var_2;

			$r = self::clamp( round( 255.0 * self::_huetorgb( $var_1, $var_2, $H + ( 1 / 3 ) ) ), 255 );
			$g = self::clamp( round( 255.0 * self::_huetorgb( $var_1, $var_2, $H ) ), 255 );
			$b = self::clamp( round( 255.0 * self::_huetorgb( $var_1, $var_2, $H - ( 1 / 3 ) ) ), 255 );

		}

		// Convert to hex
		$r = dechex( $r );
		$g = dechex( $g );
		$b = dechex( $b );

		// Make sure we get 2 digits for decimals
		$r = ( strlen( '' . $r ) === 1 ) ? '0' . $r : $r;
		$g = ( strlen( '' . $g ) === 1 ) ? '0' . $g : $g;
		$b = ( strlen( '' . $b ) === 1 ) ? '0' . $b : $b;

		return $r . $g . $b;
	}

	/**
	 * Given a HEX string returns a RGB array equivalent.
	 *
	 * @param string $color
	 *
	 * @return array RGB associative array
	 */
	public static function hexToRgb( $color ) {

		// Sanity check
		$color = self::_checkHex( $color );

		// Convert HEX to DEC
		$R = hexdec( $color[0] . $color[1] );
		$G = hexdec( $color[2] . $color[3] );
		$B = hexdec( $color[4] . $color[5] );

		$RGB['R'] = $R;
		$RGB['G'] = $G;
		$RGB['B'] = $B;

		return $RGB;
	}

	/**
	 *  Given an RGB associative array returns the equivalent HEX string
	 *
	 * @param array $rgb
	 *
	 * @return string RGB string
	 * @throws Exception "Bad RGB Array"
	 */
	public static function rgbToHex( $rgb = array() ) {
		// Make sure it's RGB
		if ( empty( $rgb ) || ! isset( $rgb['R'] ) || ! isset( $rgb['G'] ) || ! isset( $rgb['B'] ) ) {
			throw new Exception( 'Param was not an RGB array' );
		}

		// Convert RGB to HEX
		$hex[0] = dechex( $rgb['R'] );
		if ( 1 === strlen( $hex[0] ) ) {
			$hex[0] .= $hex[0];
		}
		$hex[1] = dechex( $rgb['G'] );

		if ( 1 === strlen( $hex[1] ) ) {
			$hex[1] .= $hex[1];
		}
		$hex[2] = dechex( $rgb['B'] );

		if ( 1 === strlen( $hex[2] ) ) {
			$hex[2] .= $hex[2];
		}

		return implode( '', $hex );

	}

	/**
	 * Given a HEX value, returns a darker color. If no desired amount provided, then the color halfway between
	 * given HEX and black will be returned.
	 *
	 * @param int $amount
	 *
	 * @return string Darker HEX value
	 */
	public function darken( $amount = self::DEFAULT_ADJUST ) {
		// Darken
		$darkerHSL = $this->_darken( $this->_hsl, $amount );

		// Return as HEX
		return self::hslToHex( $darkerHSL );
	}

	/**
	 * Given a HEX value, returns a lighter color. If no desired amount provided, then the color halfway between
	 * given HEX and white will be returned.
	 *
	 * @param int $amount
	 *
	 * @return string Lighter HEX value
	 */
	public function lighten( $amount = self::DEFAULT_ADJUST ) {
		// Lighten
		$lighterHSL = $this->_lighten( $this->_hsl, $amount );

		// Return as HEX
		return self::hslToHex( $lighterHSL );
	}

	/**
	 * Given a HEX value, returns a mixed color. If no desired amount provided, then the color mixed by this ratio
	 *
	 * @param string $hex2 Secondary HEX value to mix with
	 * @param int $amount = -100..0..+100
	 *
	 * @return string mixed HEX value
	 */
	public function mix( $hex2, $amount = 0 ) {
		$rgb2 = self::hexToRgb( $hex2 );
		$mixed = $this->_mix( $this->_rgb, $rgb2, $amount );

		// Return as HEX
		return self::rgbToHex( $mixed );
	}

	/**
	 * Creates an array with two shades that can be used to make a gradient
	 *
	 * @param int $amount Optional percentage amount you want your contrast color
	 *
	 * @return array An array with a 'light' and 'dark' index
	 */
	public function makeGradient( $amount = self::DEFAULT_ADJUST ) {
		// Decide which color needs to be made
		if ( $this->isLight() ) {
			$lightColor = $this->_hex;
			$darkColor = $this->darken( $amount );
		} else {
			$lightColor = $this->lighten( $amount );
			$darkColor = $this->_hex;
		}

		// Return our gradient array
		return array( 'light' => $lightColor, 'dark' => $darkColor );
	}


	/**
	 * Returns whether or not given color is considered "light"
	 *
	 * @param string|Boolean $color
	 *
	 * @return boolean
	 */
	public function isLight( $color = false ) {
		// Get our color
		$color = ( $color ) ? $color : $this->_hex;

		// Calculate straight from rbg
		$r = hexdec( $color[0] . $color[1] );
		$g = hexdec( $color[2] . $color[3] );
		$b = hexdec( $color[4] . $color[5] );

		return ( ( $r * 299 + $g * 587 + $b * 114 ) / 1000 > 130 );
	}

	/**
	 * Returns whether or not a given color is considered "dark"
	 *
	 * @param string|Boolean $color
	 *
	 * @return boolean
	 */
	public function isDark( $color = false ) {
		// Get our color
		$color = ( $color ) ? $color : $this->_hex;

		// Calculate straight from rbg
		$r = hexdec( $color[0] . $color[1] );
		$g = hexdec( $color[2] . $color[3] );
		$b = hexdec( $color[4] . $color[5] );

		return ( ( $r * 299 + $g * 587 + $b * 114 ) / 1000 <= 130 );
	}

	/**
	 * Returns the complimentary color
	 * @return string Complementary hex color
	 *
	 */
	public function complementary() {
		// Get our HSL
		$hsl = $this->_hsl;

		// Adjust Hue 180 degrees
		$hsl['H'] += ( $hsl['H'] > 180 ) ? - 180 : 180;

		// Return the new value in HEX
		return self::hslToHex( $hsl );
	}

	/**
	 * Returns your color's HSL array
	 */
	public function getHsl() {
		return $this->_hsl;
	}

	/**
	 * Returns your original color
	 */
	public function getHex() {
		return $this->_hex;
	}

	/**
	 * Returns your color's RGB array
	 */
	public function getRgb() {
		return $this->_rgb;
	}

	// ===========================
	// = Private Functions Below =
	// ===========================


	/**
	 * Darkens a given HSL array
	 *
	 * @param array $hsl
	 * @param int $amount
	 *
	 * @return array $hsl
	 */
	private function _darken( $hsl, $amount = self::DEFAULT_ADJUST ) {
		// Check if we were provided a number
		if ( $amount ) {
			$hsl['L'] = ( $hsl['L'] * 100 ) - $amount;
			$hsl['L'] = ( $hsl['L'] < 0 ) ? 0 : $hsl['L'] / 100;
		} else {
			// We need to find out how much to darken
			$hsl['L'] = $hsl['L'] / 2;
		}

		return $hsl;
	}

	/**
	 * Lightens a given HSL array
	 *
	 * @param array $hsl
	 * @param int $amount
	 *
	 * @return array $hsl
	 */
	private function _lighten( $hsl, $amount = self::DEFAULT_ADJUST ) {
		// Check if we were provided a number
		if ( $amount ) {
			$hsl['L'] = ( $hsl['L'] * 100.0 ) + $amount;
			$hsl['L'] = ( $hsl['L'] > 100.0 ) ? 1.0 : $hsl['L'] / 100.0;
		} else {
			// We need to find out how much to lighten
			$hsl['L'] += ( 1.0 - $hsl['L'] ) / 2.0;
		}

		return $hsl;
	}

	/**
	 * Mix 2 rgb colors and return an rgb color
	 *
	 * @param array $rgb1
	 * @param array $rgb2
	 * @param int $amount ranged -100..0..+100
	 *
	 * @return array $rgb
	 *
	 *    ported from http://phpxref.pagelines.com/nav.html?includes/class.colors.php.source.html
	 */
	private function _mix( $rgb1, $rgb2, $amount = 0 ) {

		$r1 = ( $amount + 100 ) / 100;
		$r2 = 2 - $r1;

		$rmix = ( ( $rgb1['R'] * $r1 ) + ( $rgb2['R'] * $r2 ) ) / 2;
		$gmix = ( ( $rgb1['G'] * $r1 ) + ( $rgb2['G'] * $r2 ) ) / 2;
		$bmix = ( ( $rgb1['B'] * $r1 ) + ( $rgb2['B'] * $r2 ) ) / 2;

		return array( 'R' => $rmix, 'G' => $gmix, 'B' => $bmix );
	}

	/**
	 * Given a Hue, returns corresponding RGB value
	 *
	 * @param int $v1
	 * @param int $v2
	 * @param int $vH
	 *
	 * @return int
	 */
	private static function _huetorgb( $v1, $v2, $vH ) {
		if ( $vH < 0 ) {
			$vH += 1;
		}

		if ( $vH > 1 ) {
			$vH -= 1;
		}

		if ( ( 6 * $vH ) < 1 ) {
			return ( $v1 + ( $v2 - $v1 ) * 6 * $vH );
		}

		if ( ( 2 * $vH ) < 1 ) {
			return $v2;
		}

		if ( ( 3 * $vH ) < 2 ) {
			return ( $v1 + ( $v2 - $v1 ) * ( ( 2 / 3 ) - $vH ) * 6 );
		}

		return $v1;

	}

	/**
	 * You need to check if you were given a good hex string
	 *
	 * @param string $hex
	 *
	 * @return string Color
	 * @throws Exception "Bad color format"
	 */
	private static function _checkHex( $hex ) {
		// Strip # sign is present
		$color = str_replace( '#', '', $hex );

		// Make sure it's 6 digits
		if ( strlen( $color ) == 3 ) {
			$color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
		} else if ( strlen( $color ) != 6 ) {
			throw new Exception( 'HEX color needs to be 6 or 3 digits long' );
		}

		return $color;
	}
}
