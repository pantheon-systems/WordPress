<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Access
 *
 * @package WPBakeryPageBuilder
 * @since 4.8
 */
abstract class Vc_Access {
	/**
	 * @var bool
	 */
	protected $validAccess = true;

	public function getValidAccess() {
		return $this->validAccess;
	}

	/**
	 * @param mixed $validAccess
	 *
	 * @return $this
	 */
	public function setValidAccess( $validAccess ) {
		$this->validAccess = $validAccess;

		return $this;
	}

	/**
	 * Check multi access settings by method inside class object.
	 *
	 * @param $method
	 * @param $valid
	 * @param $argsList
	 *
	 * @return $this
	 */
	public function checkMulti( $method, $valid, $argsList ) {
		if ( $this->getValidAccess() ) {
			$access = ! $valid;
			foreach ( $argsList as $args ) {
				if ( ! is_array( $args ) ) {
					$args = array( $args );
				}
				$this->setValidAccess( true );
				call_user_func_array( array( $this, $method ), $args );
				if ( $valid === $this->getValidAccess() ) {
					$access = $valid;
					break;
				}
			}
			$this->setValidAccess( $access );
		}

		return $this;
	}

	/**
	 * Get current validation state and reset it to true. ( should be never called twice )
	 * @return bool
	 */
	public function get() {
		$result = $this->getValidAccess();
		$this->setValidAccess( true );

		return $result;
	}

	/**
	 * Call die() function with message if access is invalid.
	 *
	 * @param string $message
	 * @return $this
	 * @throws \Exception
	 */
	public function validateDie( $message = '' ) {
		$result = $this->getValidAccess();
		$this->setValidAccess( true );
		if ( ! $result ) {
			if ( defined( 'VC_DIE_EXCEPTION' ) && VC_DIE_EXCEPTION ) {
				throw new Exception( $message );
			} else {
				die( $message );
			}
		}

		return $this;
	}


	/**
	 * @param $func
	 *
	 * @return $this
	 */
	public function check( $func ) {
		if ( $this->getValidAccess() ) {
			$args = func_get_args();
			$args = array_slice( $args, 1 );
			if ( ! empty( $func ) ) {
				$this->setValidAccess( call_user_func_array( $func, $args ) );
			}
		}

		return $this;
	}

	/**
	 * Any of provided rules should be valid.
	 * Usage: checkAny(
	 *      'vc_verify_admin_nonce',
	 *      array( 'current_user_can', 'edit_post', 12 ),
	 *      array( 'current_user_can', 'edit_posts' ),
	 * )
	 * @return $this
	 */
	public function checkAny() {
		if ( $this->getValidAccess() ) {
			$args = func_get_args();
			$this->checkMulti( 'check', true, $args );
		}

		return $this;
	}

	/**
	 * All provided rules should be valid.
	 * Usage: checkAll(
	 *      'vc_verify_admin_nonce',
	 *      array( 'current_user_can', 'edit_post', 12 ),
	 *      array( 'current_user_can', 'edit_posts' ),
	 * )
	 * @return $this
	 */
	public function checkAll() {
		if ( $this->getValidAccess() ) {
			$args = func_get_args();
			$this->checkMulti( 'check', false, $args );
		}

		return $this;
	}

	/**
	 * @param string $nonce
	 *
	 * @return Vc_Access
	 */
	public function checkAdminNonce( $nonce = '' ) {
		return $this->check( 'vc_verify_admin_nonce', $nonce );
	}

	/**
	 * @param string $nonce
	 *
	 * @return Vc_Access
	 */
	public function checkPublicNonce( $nonce = '' ) {
		return $this->check( 'vc_verify_public_nonce', $nonce );
	}
}
