<?php
namespace AffWP\Admin\Meta_Box;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface Base {

	/**
	 * Initializes the meta box.
	 *
	 * Define the meta box name,
	 * and the action on which to hook the meta box here.
	 *
	 * Example:
	 *
	 *    $this->action        = 'affwp_overview_meta_boxes';
	 *    $this->meta_box_name = __( 'Name of the meta box', 'affiliate-wp' );
	 *
	 * @access  public
	 * @return  void
	 * @since   1.9
	 */
	public function init();

	/**
	 * Defines the meta box content, as well as a
	 * filter by which the content may be adjusted.
	 *
	 * Use this method in your child class to define
	 * the content of your meta box.
	 *
	 * For example, given a $meta_box_id value of 'my-metabox-id',
	 * the filter would be: affwp_meta_box_my-meta-box-id.
	 *
	 * @return mixed string The content of the meta box
	 * @since  1.9
	 */
	public function content();
}
