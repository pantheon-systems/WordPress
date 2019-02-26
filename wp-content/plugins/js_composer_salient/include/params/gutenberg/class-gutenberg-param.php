<?php

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class Gutenberg_Param {
	protected $postTypeSlug = 'wpb_gutenberg_param';

	public function __construct() {
		add_action( 'init', array(
			$this,
			'initialize',
		) );
	}

	public function initialize() {
		global $pagenow, $wp_version;
		if ( version_compare( $wp_version, '4.9.8', '>' ) && 'post-new.php' === $pagenow && vc_user_access()->wpAll( 'edit_posts' )
				->get() && vc_request_param( 'post_type' ) === $this->postTypeSlug ) {
			$this->registerGutenbergAttributeType();
			add_action( 'admin_print_styles', array(
				$this,
				'removeAdminUI',
			) );
		}
	}

	public function removeAdminUi() {
		$style = '
		<style>
            #adminmenumain, #wpadminbar {
                display: none;
            }

            html.wp-toolbar {
                padding: 0 !important;
            }

            .wp-toolbar #wpcontent {
                margin: 0;
            }

            .wp-toolbar #wpbody {
                padding-top: 0;
            }

            .gutenberg .gutenberg__editor .edit-post-layout .edit-post-header, html .block-editor-page .edit-post-header {
                top: 0;
                left: 0;
            }

            .gutenberg .gutenberg__editor .edit-post-layout.is-sidebar-opened .edit-post-layout__content, html .block-editor-page .edit-post-layout.is-sidebar-opened .edit-post-layout__content {
                margin-right: 0;
            }

            .gutenberg .gutenberg__editor .edit-post-layout .editor-post-publish-panel, html .block-editor-page .edit-post-layout .editor-post-publish-panel, html .block-editor-page .edit-post-header__settings {
                display: none;
            }
            .editor-post-title {
                display: none !important;
            }
        </style>
';
		echo $style;
	}

	protected function registerGutenbergAttributeType() {
		$labels = array(
			'name' => _x( 'Gutenberg attrs', 'Post type general name', 'js_composer' ),
			'singular_name' => _x( 'Gutenberg attr', 'Post type singular name', 'js_composer' ),
			'menu_name' => _x( 'Gutenberg attrs', 'Admin Menu text', 'js_composer' ),
			'name_admin_bar' => _x( 'Gutenberg attr', 'Add New on Toolbar', 'js_composer' ),
			'add_new' => __( 'Add New', 'js_composer' ),
			'add_new_item' => __( 'Add New Gutenberg attr', 'js_composer' ),
			'new_item' => __( 'New Gutenberg attr', 'js_composer' ),
			'edit_item' => __( 'Edit Gutenberg attr', 'js_composer' ),
			'view_item' => __( 'View Gutenberg attr', 'js_composer' ),
			'all_items' => __( 'All Gutenberg attrs', 'js_composer' ),
			'search_items' => __( 'Search Gutenberg attrs', 'js_composer' ),
			'parent_item_colon' => __( 'Parent Gutenberg attrs:', 'js_composer' ),
			'not_found' => __( 'No Gutenberg attrs found.', 'js_composer' ),
			'not_found_in_trash' => __( 'No Gutenberg attrs found in Trash.', 'js_composer' ),
			'featured_image' => _x( 'Gutenberg attr Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'js_composer' ),
			'set_featured_image' => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'js_composer' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'js_composer' ),
			'use_featured_image' => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'js_composer' ),
			'archives' => _x( 'Gutenberg attr archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'js_composer' ),
			'insert_into_item' => _x( 'Add into Gutenberg attr', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'js_composer' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this Gutenberg attr', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'js_composer' ),
			'filter_items_list' => _x( 'Filter Gutenberg attrs list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'js_composer' ),
			'items_list_navigation' => _x( 'Gutenberg attrs list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'js_composer' ),
			'items_list' => _x( 'Gutenberg attrs list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'js_composer' ),
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => false,
			'query_var' => false,
			'capability_type' => 'page',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => null,
			'show_in_rest' => true,
			'supports' => array( 'editor' ),
		);
		register_post_type( $this->postTypeSlug, $args );
	}
}
