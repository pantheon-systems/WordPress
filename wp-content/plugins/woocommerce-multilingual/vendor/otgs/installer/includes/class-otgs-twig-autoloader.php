<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class OTGS_Twig_Autoloader {

	/**
	 * @param bool $prepend
	 */
	public static function register( $prepend = false ) {
		if ( PHP_VERSION_ID < 50300 ) {
			spl_autoload_register( array( __CLASS__, 'autoload' ) );
		} else {
			spl_autoload_register( array( __CLASS__, 'autoload' ), true, $prepend );
		}

	}

	/**
	 * @param string $class
	 */
	public static function autoload( $class ) {
		if ( 0 !== strpos( $class, 'Twig' ) ) {
			return;
		}

		$file = WP_Installer()->plugin_path() . '/../../twig/twig/lib/' . str_replace( array( '_', "\0" ), array( '/', '' ), $class . '.php' );

		if ( is_file( $file ) ) {
			require $file;
		}
	}
}