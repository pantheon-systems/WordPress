<?php

class WPML_String_Functions {

	public static function is_css_color( $string ) {
		return (bool) preg_match( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/im', $string );
	}

	public static function is_css_length( $string ) {
		$parts = explode( ' ', $string );
		foreach ( $parts as $part ) {
			if ( ! (bool) preg_match( '/^[+-]?[0-9]+.?([0-9]+)?(px|em|ex|ch|rem|vw|vh|vmin|vmax|%|in|cm|mm|pt|pc)$/im', $part ) &&
		       '0' !== $part ) {
				return false;
			}
		}

		return true;
	}

	public static function is_numeric( $string ) {
		return (bool) is_numeric( $string );
	}

	public static function is_not_translatable( $string ) {
		return self::is_css_color( $string ) ||
		       self::is_css_length( $string ) ||
		       self::is_numeric( $string );
	}
}