<?php
/**
 * Function for managing the last sort order on the view list table per user.
 */

/**
 * Save
 */
function wpmtst_save_view_list_order() {
	$name  = $_REQUEST['name'];
	$order = $_REQUEST['order'];
	$success = '';
	if ( in_array( $name, array( 'name', 'id' ) ) ) {
		$success = update_user_meta( get_current_user_id(), 'strong_view_list_order', array( $name, $order ) );
	}
	echo $success;
	wp_die();
}
add_action( 'wp_ajax_wpmtst_save_view_list_order', 'wpmtst_save_view_list_order' );


/**
 * Fetch
 */
function wpmtst_fetch_view_list_order() {
	global $pagenow;

	if ( $pagenow == 'edit.php'
		 && isset( $_GET['post_type'] )
		 && 'wpm-testimonial' == $_GET['post_type']
		 && isset( $_GET['page'] )
		 && 'testimonial-views' == $_GET['page']
		 && ! isset( $_GET['orderby'] )
		 && ! isset( $_GET['action'] ) )
	{
		$order = get_user_meta( get_current_user_id(), 'strong_view_list_order', true );
		if ( $order ) {
			$url = admin_url( "edit.php?post_type=wpm-testimonial&page=testimonial-views&orderby={$order[0]}&order={$order[1]}" );
			wp_redirect( $url );
			exit;
		}
	}
}
add_action( 'admin_init', 'wpmtst_fetch_view_list_order' );


/**
 * Clear
 */
function wpmtst_clear_view_list_order() {
	delete_user_meta( get_current_user_id(), 'strong_view_list_order' );
	$url = 'edit.php?post_type=wpm-testimonial&page=testimonial-views';
	wp_redirect( $url );
}
add_action( 'admin_post_clear-view-sort', 'wpmtst_clear_view_list_order' );
