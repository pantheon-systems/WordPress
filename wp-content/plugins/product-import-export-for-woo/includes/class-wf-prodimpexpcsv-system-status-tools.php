<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WF_ProdImpExpCsv_System_Status_Tools {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_debug_tools', array( $this, 'tools' ) );
	}

	/**
	 * Tools we add to WC
	 * @param  array $tools
	 * @return array
	 */
	public function tools( $tools ) {
		$tools['delete_products'] = array(
			'name'		=> __( 'Delete Products','wf_csv_import_export'),
			'button'	=> __( 'Delete ALL products','wf_csv_import_export' ),
			'desc'		=> __( 'This tool will delete all products allowing you to start fresh.', 'wf_csv_import_export' ),
			'callback'  => array( $this, 'delete_products' )
		);
		$tools['delete_variations'] = array(
			'name'		=> __( 'Delete Variations','wf_csv_import_export'),
			'button'	=> __( 'Delete ALL variations','wf_csv_import_export' ),
			'desc'		=> __( 'This tool will delete all variations allowing you to start fresh.', 'wf_csv_import_export' ),
			'callback'  => array( $this, 'delete_variations' )
		);
		$tools['delete_orphaned_variations'] = array(
			'name'		=> __( 'Delete Orphans','wf_csv_import_export'),
			'button'	=> __( 'Delete orphaned variations','wf_csv_import_export' ),
			'desc'		=> __( 'This tool will delete variations which have no parent.', 'wf_csv_import_export' ),
			'callback'  => array( $this, 'delete_orphaned_variations' )
		);
		return $tools;
	}

	/**
	 * Delete products
	 */
	public function delete_products() {
		global $wpdb;

		// Delete products
		$result  = absint( $wpdb->delete( $wpdb->posts, array( 'post_type' => 'product' ) ) );
		$result2 = absint( $wpdb->delete( $wpdb->posts, array( 'post_type' => 'product_variation' ) ) );

		// Delete meta and term relationships with no post
		$wpdb->query( "DELETE pm
			FROM {$wpdb->postmeta} pm
			LEFT JOIN {$wpdb->posts} wp ON wp.ID = pm.post_id
			WHERE wp.ID IS NULL" );
		$wpdb->query( "DELETE tr
			FROM {$wpdb->term_relationships} tr
			LEFT JOIN {$wpdb->posts} wp ON wp.ID = tr.object_id
			WHERE wp.ID IS NULL" );

		echo '<div class="updated"><p>' . sprintf( __( '%d Products Deleted', 'wf_csv_import_export' ), ( $result + $result2 ) ) . '</p></div>';
	}

	/**
	 * Delete variations
	 */
	public function delete_variations() {
		global $wpdb;

		// Delete products
		$result = absint( $wpdb->delete( $wpdb->posts, array( 'post_type' => 'product_variation' ) ) );

		// Delete meta and term relationships with no post
		$wpdb->query( "DELETE pm
			FROM {$wpdb->postmeta} pm
			LEFT JOIN {$wpdb->posts} wp ON wp.ID = pm.post_id
			WHERE wp.ID IS NULL" );
		$wpdb->query( "DELETE tr
			FROM {$wpdb->term_relationships} tr
			LEFT JOIN {$wpdb->posts} wp ON wp.ID = tr.object_id
			WHERE wp.ID IS NULL" );

		echo '<div class="updated"><p>' . sprintf( __( '%d Variations Deleted', 'wf_csv_import_export' ), $result ) . '</p></div>';
	}

	/**
	 * Delete orphans
	 */
	public function delete_orphaned_variations() {
		global $wpdb;

		// Delete meta and term relationships with no post
		$result = absint( $wpdb->query( "DELETE products
			FROM {$wpdb->posts} products
			LEFT JOIN {$wpdb->posts} wp ON wp.ID = products.post_parent
			WHERE wp.ID IS NULL AND products.post_type = 'product_variation';" ) );

		echo '<div class="updated"><p>' . sprintf( __( '%d Variations Deleted', 'wf_csv_import_export' ), $result ) . '</p></div>';
	}	
}

new WF_ProdImpExpCsv_System_Status_Tools();