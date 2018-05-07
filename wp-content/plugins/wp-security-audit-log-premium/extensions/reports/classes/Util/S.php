<?php
/**
 * Class: Utility Class
 *
 * @since 1.0.0
 * @package report-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WSAL_Rep_Plugin' ) ) {
	exit( 'You are not allowed to view this page.' );
}

/**
 * Class WSAL_Rep_Util_S
 *
 * @package report-wsal
 */
class WSAL_Rep_Util_S {

	/**
	 * Creates an unique random number
	 *
	 * @param int $size - The length of the number to generate. Defaults to 25.
	 * @return string
	 */
	final static function GenerateRandomString( $size = 25 ) {
		$str = 'n0pqN865_3OUVristu47D_vwx012F_GH34_PQRST569_abcde753lm_yzAB109s_CfghD_E9h8sIJYZ_ijkKLM78WX';
		$str = str_shuffle( str_shuffle( str_shuffle( $str ) ) );
		$str = date( 'mdYHis' ) . '_' . substr( str_shuffle( $str ), 0, $size );
		return $str;
	}
}
