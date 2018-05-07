<?php
/**
 * Class: Utility Class
 *
 * Utility class to evaluate an expression.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WSAL_OPT_PREFIX' ) ) {
	exit( 'Invalid request' );
}

/**
 * Class WSAL_NP_Expression
 * Utility class to evaluate an expression that will replace the eval function
 *
 * @author wp.kytten
 * @package wp-security-audit-log
 */
class WSAL_NP_Expression {

	/**
	 * Instance of WSAL_NP_Notifier.
	 *
	 * @var object
	 */
	private $notif = null;

	/**
	 * Condition Select.
	 *
	 * @var array
	 */
	private $_s1_data = null;

	/**
	 * Trigger.
	 *
	 * @var array
	 */
	private $_s2_data = null;

	/**
	 * Condition.
	 *
	 * @var array
	 */
	private $_s3_data = null;

	/**
	 * Post Status.
	 *
	 * @var array
	 */
	private $_s4_data = null;

	/**
	 * Post Type.
	 *
	 * @var array
	 */
	private $_s5_data = null;

	/**
	 * User Roles.
	 *
	 * @var array
	 */
	private $_s6_data = null;

	/**
	 * Notification Title.
	 *
	 * @var string
	 */
	private $title = '';

	/**
	 * Expression.
	 *
	 * @var array
	 */
	private $_expr = null;

	/**
	 * Method: Constructor.
	 *
	 * @param WSAL_NP_Notifier $notif - Instance of notifier.
	 * @param array            $s1_data - Condition data.
	 * @param array            $s2_data - Trigger data.
	 * @param array            $s3_data - Condition data.
	 * @param string           $title - Notification title.
	 * @param array            $s4_data - Post Status data.
	 * @param array            $s5_data - Post Type data.
	 * @param array            $s6_data - User Roles data.
	 */
	final public function __construct( WSAL_NP_Notifier $notif, array $s1_data, array $s2_data, array $s3_data, $title, $s4_data = array(), $s5_data = array(), $s6_data = array() ) {
		$this->notif = $notif;
		$this->_s1_data = $s1_data;
		$this->_s2_data = $s2_data;
		$this->_s3_data = $s3_data;
		$this->_s4_data = $s4_data;
		$this->_s5_data = $s5_data;
		$this->_s6_data = $s6_data;
		$this->title = $title;
	}

	/**
	 * Method: Evaluate Conditions for notification.
	 *
	 * @param array $data - Notification data array.
	 */
	final public function EvaluateConditions( array $data ) {
		$result = false;
		if ( empty( $data ) ) {
			return $result;
		}

		$first_item = false;
		$prev_op = null;
		$exp_array = array();

		foreach ( $data as $i => $entry ) {
			if ( isset( $entry['select1'] ) ) { // Single.
				$r = $this->_evaluateTrigger( $entry );
				if ( $i < 1 ) {
					array_push( $exp_array, $r );
					$first_item = true;
				} else {
					if ( $entry['select1'] == 1 ) {
						$prev_op = '||';
					} else {
						$prev_op = '&&';
					}
					array_push( $exp_array, $prev_op, $r );
					$first_item = true;
				}
			} else { // Group.
				$ca = array();
				foreach ( $entry as $k => $item ) {
					$r = $this->_evaluateTrigger( $item );
					// First group - op before the array.
					if ( empty( $ca ) && $first_item ) {
						$prev_op = $this->_getOperator( $item['select1'] );
						array_push( $exp_array, $prev_op );
					}
					if ( isset( $entry[ $k + 1 ] ) ) { // Next is available.
						$prev_op = $this->_getOperator( $entry[ $k + 1 ]['select1'] );
						array_push( $ca, $r, $prev_op );
					} else {
						array_push( $ca, $r );
					}
				}
				array_push( $exp_array, $ca );
				$first_item = true;
			}
		}
		$this->_expr = $exp_array;
		return $this->__evaluateFinalArray( $exp_array );
	}

	/**
	 * Method: Evaluate array.
	 *
	 * @param array $array - Notification data array.
	 */
	final protected function __evaluateArray( $array ) {
		$prev_result = null;
		$op = null;
		// Evaluate as we move forward into the array.
		foreach ( $array as $k => $value ) {
			if ( is_bool( $value ) ) {
				if ( is_null( $prev_result ) ) {
					$prev_result = $value;
				} else {
					if ( $op == '||' ) {
						$prev_result = $prev_result || $value;
					} else {
						$prev_result = $prev_result && $value;
					}
				}
			} elseif ( is_string( $value ) ) {
				$op = $value;
			}
		}
		return $prev_result;
	}

	/**
	 * Method: Evaluate final expression.
	 *
	 * @param array $array - Expression array.
	 */
	final protected function __evaluateFinalArray( $array ) {
		$prev_result = null;
		$op = null;
		foreach ( $array as $k => $value ) {
			if ( is_bool( $value ) ) {
				if ( is_null( $prev_result ) ) {
					$prev_result = $value;
				} else {
					if ( $op == '||' ) {
						$prev_result = $prev_result || $value;
					} else {
						$prev_result = $prev_result && $value;
					}
				}
			} elseif ( is_string( $value ) ) {
				$op = $value;
			} elseif ( is_array( $value ) ) {
				$t = $this->__evaluateArray( $value );
				if ( is_null( $prev_result ) ) {
					$prev_result = $t;
				} else {
					if ( $op == '||' ) {
						$prev_result = $prev_result || $t;
					} else {
						$prev_result = $prev_result && $t;
					}
				}
			}
		}
		return $prev_result;
	}

	/**
	 * Method: Return operator for condition.
	 *
	 * @param integer $s1 - Value of condition.
	 * @return string
	 */
	final protected function _getOperator( $s1 ) {
		if ( $s1 == 1 ) {
			return '||';
		}
		return '&&';
	}

	/**
	 * Method: Return expression array.
	 */
	final public function GetLastExpressionArray() {
		return $this->_expr;
	}

	/**
	 * Get the expression in string format.
	 *
	 * @param array $expression - Expression.
	 * @return string $expr_string
	 */
	final public function GetExpressionAsString( array $expression ) {
		$expr_string = '';
		foreach ( $expression as $item ) {
			if ( is_bool( $item ) ) {
				if ( $item ) {
					$expr_string .= 'TRUE';
				} else {
					$expr_string .= 'FALSE';
				}
			} elseif ( is_string( $item ) ) {
				$expr_string .= ' ' . $item . ' ';
			} elseif ( is_array( $item ) ) {
				$expr_string .= '(';
				foreach ( $item as $entry ) {
					if ( is_bool( $entry ) ) {
						if ( $entry ) {
							$expr_string .= 'TRUE';
						} else {
							$expr_string .= 'FALSE';
						}
					} elseif ( is_string( $entry ) ) {
						$expr_string .= ' ' . $entry . ' ';
					}
				}
				$expr_string .= ')';
			}
		}
		return $expr_string;
	}

	/**
	 * Evaluate a trigger.
	 *
	 * @param array $condition - Array of conditions.
	 * @return bool
	 */
	final protected function _evaluateTrigger( array $condition ) {
		if ( empty( $condition ) ) {
			return false;
		}
		$s1 = $this->_s1_data[ $condition['select1'] ];
		$s2 = $this->_s2_data[ $condition['select2'] ];
		$s3 = $this->_s3_data[ $condition['select3'] ];
		$s4 = $this->_s4_data[ $condition['select4'] ];
		$s5 = $this->_s5_data[ $condition['select5'] ];
		$s6 = $this->_s6_data[ $condition['select6'] ];
		$i1 = $condition['input1'];
		return $this->notif->_checkIfConditionMatch( $s1, $s2, $s3, $s4, $s5, $s6, $i1, $this->title );
	}
}
