<?php
/**
 * Meta Interface.
 *
 * Interface used by the Metadata.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface used by the Metadata.
 *
 * @package Wsal
 */
interface WSAL_Adapters_MetaInterface {

	/**
	 * Create a meta object
	 *
	 * @param array $occurenceIds - Array of meta data.
	 * @return int ID of the new meta data
	 */
	public function deleteByOccurenceIds( $occurenceIds );

	/**
	 * Load by name and occurrence id.
	 *
	 * @param string $metaName - Meta name.
	 * @param int    $occurenceId - Occurrence ID.
	 */
	public function loadByNameAndOccurenceId( $metaName, $occurenceId );
}
