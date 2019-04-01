<?php

/**
 * Test if the user is allowed to copy posts
 */
function duplicate_post_is_current_user_allowed_to_copy() {
	return current_user_can('copy_posts');
}

/**
 * Test if post type is enable to be copied
 */
function duplicate_post_is_post_type_enabled($post_type){
	$duplicate_post_types_enabled = get_option('duplicate_post_types_enabled', array ('post', 'page'));
	if(!is_array($duplicate_post_types_enabled)) $duplicate_post_types_enabled = array($duplicate_post_types_enabled);
	return in_array($post_type, $duplicate_post_types_enabled);
}

/**
 * Wrapper for the option 'duplicate_post_create_user_level'
 */
function duplicate_post_get_copy_user_level() {
	return get_option( 'duplicate_post_copy_user_level' );
}

// Template tag
/**
 * Retrieve duplicate post link for post.
 *
 *
 * @param int $id Optional. Post ID.
 * @param string $context Optional, default to display. How to write the '&', defaults to '&amp;'.
 * @param string $draft Optional, default to true
 * @return string
 */
function duplicate_post_get_clone_post_link( $id = 0, $context = 'display', $draft = true ) {
	if ( !duplicate_post_is_current_user_allowed_to_copy() )
		return;

	if ( !$post = get_post( $id ) )
		return;
	
	if(!duplicate_post_is_post_type_enabled($post->post_type))
		return;

	if ($draft)
	$action_name = "duplicate_post_save_as_new_post_draft";
	else
	$action_name = "duplicate_post_save_as_new_post";

	if ( 'display' == $context )
	$action = '?action='.$action_name.'&amp;post='.$post->ID;
	else
	$action = '?action='.$action_name.'&post='.$post->ID;

	$post_type_object = get_post_type_object( $post->post_type );
	if ( !$post_type_object )
	return;

	return wp_nonce_url(apply_filters( 'duplicate_post_get_clone_post_link', admin_url( "admin.php". $action ), $post->ID, $context ), 'duplicate-post_' . $post->ID);
}
/**
 * Display duplicate post link for post.
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @param int $id Optional. Post ID.
 */
function duplicate_post_clone_post_link( $link = null, $before = '', $after = '', $id = 0 ) {
	if ( !$post = get_post( $id ) )
	return;

	if ( !$url = duplicate_post_get_clone_post_link( $post->ID ) )
	return;

	if ( null === $link )
	$link = esc_html__('Copy to a new draft', 'duplicate-post');

	$link = '<a class="post-clone-link" href="' . $url . '" title="'
	. esc_attr__("Copy to a new draft", 'duplicate-post')
	.'">' . $link . '</a>';
	echo $before . apply_filters( 'duplicate_post_clone_post_link', $link, $post->ID ) . $after;
}
/**
 * Get original post .
 *
 * @param int $post Optional. Post ID or Post object.
 * @param string $output Optional, default is Object. Either OBJECT, ARRAY_A, or ARRAY_N.
 * @return mixed Post data
 */
function duplicate_post_get_original($post = null , $output = OBJECT){
	if ( !$post = get_post( $post ) )
		return;
	$original_ID = get_post_meta( $post->ID, '_dp_original');
	if (empty($original_ID)) return null;
	$original_post = get_post($original_ID[0],  $output);
	return $original_post;
}

// Admin bar
function duplicate_post_admin_bar_render() {
	if(!is_admin_bar_showing()) return;
	global $wp_admin_bar;
	$current_object = get_queried_object();
	if ( !empty($current_object) ){
		if ( ! empty( $current_object->post_type )
			&& ( $post_type_object = get_post_type_object( $current_object->post_type ) )
			&& duplicate_post_is_current_user_allowed_to_copy()
			&& ( $post_type_object->show_ui || 'attachment' == $current_object->post_type )
			&& (duplicate_post_is_post_type_enabled($current_object->post_type) ) )
		{
			$wp_admin_bar->add_menu( array(
	        	'id' => 'new_draft',
	        	'title' => esc_attr__("Copy to a new draft", 'duplicate-post'),
	        	'href' => duplicate_post_get_clone_post_link( $current_object->ID )
			) );	
		}
	} else if ( is_admin() && isset( $_GET['post'] )){
		$id = $_GET['post'];
		$post = get_post($id);
		if( duplicate_post_is_current_user_allowed_to_copy()
				&& duplicate_post_is_post_type_enabled($post->post_type)) {
					$wp_admin_bar->add_menu( array(
						'id' => 'new_draft',
						'title' => esc_attr__("Copy to a new draft", 'duplicate-post'),
						'href' => duplicate_post_get_clone_post_link( $id )
					) );
		}
	}
}

function duplicate_post_add_css() {
	if(!is_admin_bar_showing()) return;
	$current_object = get_queried_object();
	if ( !empty($current_object) ){
		if ( ! empty( $current_object->post_type )
			&& ( $post_type_object = get_post_type_object( $current_object->post_type ) )
			&& duplicate_post_is_current_user_allowed_to_copy()
			&& ( $post_type_object->show_ui || 'attachment' == $current_object->post_type )
			&& (duplicate_post_is_post_type_enabled($current_object->post_type) ) )
		{
			wp_enqueue_style ( 'duplicate-post', plugins_url('/duplicate-post.css', __FILE__));
		}
	} else if ( is_admin() && isset( $_GET['post'] )){
		$id = $_GET['post'];
		$post = get_post($id);
		if( duplicate_post_is_current_user_allowed_to_copy()
				&& duplicate_post_is_post_type_enabled($post->post_type)) {
					wp_enqueue_style ( 'duplicate-post', plugins_url('/duplicate-post.css', __FILE__));
				}
	}
}


add_action('init', 'duplicate_post_init');

function duplicate_post_init(){
	if (get_option ( 'duplicate_post_show_adminbar' ) == 1) {
		add_action ( 'wp_before_admin_bar_render', 'duplicate_post_admin_bar_render' );
		add_action ( 'wp_enqueue_scripts', 'duplicate_post_add_css' );
		add_action ( 'admin_enqueue_scripts', 'duplicate_post_add_css' );
	}
}

/**
 * Sort taxonomy objects: first public, then private
 */
function duplicate_post_tax_obj_cmp($a, $b) {
	return ($a->public < $b->public);
}