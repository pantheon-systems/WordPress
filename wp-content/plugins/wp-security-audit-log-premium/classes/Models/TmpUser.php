<?php
/**
 * Class: TmpUser Model Class
 *
 * Model used for the Temporary WP_users table.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Model tmp_users
 *
 * Model used for the Temporary WP_users table.
 *
 * @package Wsal
 */
class WSAL_Models_TmpUser extends WSAL_Models_ActiveRecord {

	/**
	 * User ID.
	 *
	 * @var integer
	 */
	public $id = 0;

	/**
	 * Username.
	 *
	 * @var string
	 */
	public $user_login = '';

	/**
	 * Model Name.
	 *
	 * @var string
	 */
	protected $adapterName = 'TmpUser';
}
