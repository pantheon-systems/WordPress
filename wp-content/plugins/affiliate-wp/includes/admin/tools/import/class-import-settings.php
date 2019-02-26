<?php
/**
 * Export Settings Class.
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Export
 * @copyright   Copyright (c) 2016, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */
namespace AffWP\Utils\Importer;

use AffWP\Utils\Importer;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements an importer for AffiliateWP settings.
 *
 * @since 2.0
 */
class Settings implements Importer\Base {

	/**
	 * File contents to import.
	 *
	 * @access public
	 * @since  2.0
	 * @var    null|string
	 */
	public $file = null;

	/**
	 * Import type.
	 *
	 * Used for import-type specific filters/actions
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $import_type = 'settings';

	/**
	 * Instantiates the settings importer.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param null|string $file Settings file data to import.
	 */
	public function __construct( $file = null ) {
		$this->file = $file;
	}

	/**
	 * Determines whether the current user can initiate an import of settings.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool True if the user can import, otherwise false.
	 */
	public function can_import() {
		return current_user_can( 'manage_affiliate_options' );
	}

	/**
	 * Retrieves the settings data to import.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return string Settings import file contents.
	 */
	public function get_data() {
		return affwp_object_to_array( json_decode( file_get_contents( $this->file ) ) );
	}

	/**
	 * Import the settings data.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function import() {
		affiliate_wp()->settings->set( $this->get_data(), $save = true );
	}
}
