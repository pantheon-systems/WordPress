<?php

function blocksy_print($value) {
	static $first_time = true;

	if ($first_time) {
		ob_start();
		echo '<style>
		div.ct_print_r {
			max-height: 500px;
			overflow-y: scroll;
			background: #23282d;
			margin: 10px 30px;
			padding: 0;
			border: 1px solid #F5F5F5;
			border-radius: 3px;
			position: relative;
			z-index: 11111;
		}

		div.ct_print_r pre {
			color: #78FF5B;
			background: #23282d;
			text-shadow: 1px 1px 0 #000;
			font-family: Consolas, monospace;
			font-size: 12px;
			margin: 0;
			padding: 5px;
			display: block;
			line-height: 16px;
			text-align: left;
		}

		div.ct_print_r_group {
			background: #f1f1f1;
			margin: 10px 30px;
			padding: 1px;
			border-radius: 5px;
			position: relative;
			z-index: 11110;
		}
		div.ct_print_r_group div.ct_print_r {
			margin: 9px;
			border-width: 0;
		}
		</style>';

		/**
		 * Note to code reviewers: This line doesn't need to be escaped.
		 * The variable used here has the value escaped properly.
		 */
		echo str_replace( array( '  ', "\n" ), '', ob_get_clean() );

		$first_time = false;
	}

	/**
	 * Note to code reviewers: This line doesn't need to be escaped.
	 * The variable used here has the value escaped properly.
	 */
	if (func_num_args() === 1) {
		echo '<div class="ct_print_r"><pre>';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo htmlspecialchars(Blocksy_FW_Dumper::dump($value));
		echo '</pre></div>';
	} else {
		echo '<div class="ct_print_r_group">';

		foreach (func_get_args() as $param) {
			blocksy_print($param);
		}

		echo '</div>';
	}
}

/**
 * TVar_dumper class.
 * original source: https://code.google.com/p/prado3/source/browse/trunk/framework/Util/TVar_dumper.php
 *
 * TVar_dumper is intended to replace the buggy PHP function var_dump and print_r.
 * It can correctly identify the recursively referenced objects in a complex
 * object structure. It also has a recursive depth control to avoid indefinite
 * recursive display of some peculiar variables.
 *
 * TVar_dumper can be used as follows,
 * <code>
 *   echo TVar_dumper::dump($var);
 * </code>
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package System.Util
 * @since 3.0
 */
class Blocksy_FW_Dumper {
	/**
	 * Object
	 *
	 * @var object objects boj
	 */
	private static $_objects;
	/**
	 * Output
	 *
	 * @var string Output output of the dumper.
	 */
	private static $_output;
	/**
	 * Depth
	 *
	 * @var int Depth depth
	 */
	private static $_depth;

	/**
	 * Converts a variable into a string representation.
	 * This method achieves the similar functionality as var_dump and print_r
	 * but is more robust when handling complex objects such as PRADO controls.
	 *
	 * @param mixed   $var     Variable to be dumped.
	 * @param integer $depth Maximum depth that the dumper should go into the variable. Defaults to 10.
	 * @return string the string representation of the variable
	 */
	public static function dump( $var, $depth = 10 ) {
		self::reset_internals();

		self::$_depth = $depth;
		self::dump_internal( $var, 0 );

		$output = self::$_output;

		self::reset_internals();

		return $output;
	}

	/**
	 * Reset internals.
	 */
	private static function reset_internals() {
		self::$_output = '';
		self::$_objects = array();
		self::$_depth = 10;
	}

	/**
	 * Dump
	 *
	 * @param object $var var.
	 * @param int    $level level.
	 */
	private static function dump_internal( $var, $level ) {
		switch ( gettype( $var ) ) {
			case 'boolean':
				self::$_output .= $var ? 'true' : 'false';
				break;
			case 'integer':
				self::$_output .= "$var";
				break;
			case 'double':
				self::$_output .= "$var";
				break;
			case 'string':
				self::$_output .= "'$var'";
				break;
			case 'resource':
				self::$_output .= '{resource}';
				break;
			case 'NULL':
				self::$_output .= 'null';
				break;
			case 'unknown type':
				self::$_output .= '{unknown}';
				break;
			case 'array':
				if ( self::$_depth <= $level ) {
					self::$_output .= 'array(...)';
				} elseif ( empty( $var ) ) {
					self::$_output .= 'array()';
				} else {
					$keys = array_keys( $var );
					$spaces = str_repeat( ' ', $level * 4 );
					self::$_output .= "array\n" . $spaces . '(';
					foreach ( $keys as $key ) {
						self::$_output .= "\n" . $spaces . "    [$key] => ";
						self::$_output .= self::dump_internal( $var[ $key ], $level + 1 );
					}
					self::$_output .= "\n" . $spaces . ')';
				}
				break;
			case 'object':
				$id = array_search( $var, self::$_objects, true );

				if ( false !== $id ) {
					self::$_output .= get_class( $var ) . '(...)';
				} elseif ( self::$_depth <= $level ) {
					self::$_output .= get_class( $var ) . '(...)';
				} else {
					$id = array_push( self::$_objects, $var );
					$class_name = get_class( $var );
					$members = (array) $var;
					$keys = array_keys( $members );
					$spaces = str_repeat( ' ', $level * 4 );
					self::$_output .= "$class_name\n" . $spaces . '(';
					foreach ( $keys as $key ) {
						$key_display = strtr(
							trim( $key ),
							array( "\0" => ':' )
						);
						self::$_output .= "\n" . $spaces . "    [$key_display] => ";
						self::$_output .= self::dump_internal(
							$members[ $key ],
							$level + 1
						);
					}
					self::$_output .= "\n" . $spaces . ')';
				}
				break;
		}
	}
}
