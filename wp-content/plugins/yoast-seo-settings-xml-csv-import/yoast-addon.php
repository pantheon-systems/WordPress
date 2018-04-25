<?php

/*
Plugin Name: WP All Import - Yoast WordPress SEO Add-On
Plugin URI: http://www.wpallimport.com/
Description: Import data into Yoast WordPress SEO with WP All Import.
Version: 1.1.7
Author: Soflyy
*/

include "rapid-addon.php";

include_once(ABSPATH.'wp-admin/includes/plugin.php');

add_action( 'pmxi_saved_post', 'yoast_addon_primary_category', 10, 1 );

$yoast_addon = new RapidAddon( 'Yoast WordPress SEO Add-On', 'yoast_addon' );

$custom_type = yoast_seo_addon_get_post_type();

switch($custom_type) {
	
	// When "Taxonomies" is chosen in Step 1
	case "taxonomies":
		$yoast_addon->add_field( 'wpseo_focuskw', 'Focus Keyword', 'text', null, "Pick the main keyword or keyphrase that this post/page is about." );

		$yoast_addon->add_field( 'wpseo_title', 'SEO Title', 'text', null, "The SEO title defaults to what is generated based on this site's title template for this post type." );

		$yoast_addon->add_field( 'wpseo_desc', 'Meta Description', 'text', null, "The meta description will be limited to 156 chars. It is often shown as the black text under the title in a search result. For this to work it has to contain the keyword that was searched for." );
		
		$yoast_addon->add_options(
			$yoast_addon->add_field( 'wpseo_opengraph-title', 'Facebook Title', 'text', null, "If you don't want to use the post title for sharing the post on Facebook but instead want another title there, import it here." ),
			'Facebook Options',
			array(
				$yoast_addon->add_field( 'wpseo_opengraph-description', 'Description', 'text', null, "If you don't want to use the meta description for sharing the post on Facebook but want another description there, write it here." ),
				$yoast_addon->add_field( 'wpseo_opengraph-image', 'Image', 'image', null, "If you want to override the image used on Facebook for this post, import one here. The recommended image size for Facebook is 1200 x 628px."),
			)
		);

		$yoast_addon->add_options(
			$yoast_addon->add_field( 'wpseo_twitter-title', 'Twitter Title', 'text', null, "If you don't want to use the post title for sharing the post on Twitter but instead want another title there, import it here." ),
			'Twitter Options',
			array(
				$yoast_addon->add_field( 'wpseo_twitter-description', 'Description', 'text', null, "If you don't want to use the meta description for sharing the post on Twitter but want another description there, import it here." ),
				$yoast_addon->add_field( 'wpseo_twitter-image', 'Image', 'image', null, "If you want to override the image used on Twitter for this post, import one here. The recommended image size for Twitter is 1024 x 512px."),
			)
		);
		
		$yoast_addon->add_options(
			null,
			'Advanced SEO Options',
			array(
				$yoast_addon->add_field( 'wpseo_noindex', 'Meta Robots Index', 'radio', 
					array(
						'' => 'default',
						'index' => 'index',
						'noindex' => 'noindex',
					),
					"This setting can be overwritten by Yoast WordPress SEO's sitewide privacy settings"
				),
				$yoast_addon->add_field( 'wpseo_sitemap_include', 'Include in Sitemap', 'radio', 
					array(
						'' => 'Auto detect',
						'always' => 'Always include',
						'never' => 'Never include'
					),
					'Should this page be in the XML Sitemap at all times, regardless of Robots Meta settings?'
				),
				$yoast_addon->add_field( 'wpseo_canonical', 'Canonical URL', 'text', null, 'The canonical URL that this page should point to, leave empty to default to permalink. Cross domain canonical supported too.' )

			)
		);
		
		$yoast_addon->set_import_function( 'yoast_seo_addon_import' );

		if (function_exists('is_plugin_active')) {

			if ( !is_plugin_active( "wordpress-seo/wp-seo.php" ) && !is_plugin_active( "wordpress-seo-premium/wp-seo-premium.php" ) ) {

				$yoast_addon->admin_notice(
					'The Yoast WordPress SEO Add-On requires WP All Import <a href="http://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=yoast" target="_blank">Pro</a> or <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a>, and the <a href="https://yoast.com/wordpress/plugins/seo/">Yoast WordPress SEO</a> plugin.',
					array(
						'plugins' => array('wordpress-seo/wp-seo.php')
					)
				);
			}

			if ( is_plugin_active( "wordpress-seo/wp-seo.php" ) || is_plugin_active( "wordpress-seo-premium/wp-seo-premium.php" ) ) {
				
				$yoast_addon->run();
				
			}
		}

		function yoast_seo_addon_import( $term_id, $data, $import_options, $article ) {

			global $yoast_addon;
			
			// all fields except for slider and image fields
			$fields = array(
				'wpseo_focuskw',
				'wpseo_title',
				'wpseo_desc',
				'wpseo_noindex',
				'wpseo_sitemap_include',
				'wpseo_canonical',
				'wpseo_opengraph-title',
				'wpseo_opengraph-description',
				'wpseo_twitter-title',
				'wpseo_twitter-description',
			);
			
			// image fields
			$image_fields = array(
				'wpseo_opengraph-image',
				'wpseo_twitter-image'
			);
			
			$fields = array_merge( $fields, $image_fields );
			
			// Gets the slug for the taxonomy currently being imported into
			$taxonomy_type = $import_options['options']['taxonomy_type'];
			
			// Get the current SEO data for taxonomies, and store it in an array
			$meta = get_option("wpseo_taxonomy_meta");
			
			foreach ( $fields as $field ) {
				
				if ( empty($article['ID']) or $yoast_addon->can_update_meta( $field, $import_options ) ) {

					if ( in_array( $field, $image_fields ) ) {

						if ( $yoast_addon->can_update_image( $import_options ) ) {

							$id = $data[$field]['attachment_id'];
						
							$url = wp_get_attachment_url( $id );

							$meta[$taxonomy_type][$term_id][$field] = $url;

						}

					} else {
						
						$meta[$taxonomy_type][$term_id][$field] = $data[$field];

					}
				}
			}
			// Update the option to include our newly imported SEO data for taxonomies
			update_option("wpseo_taxonomy_meta", $meta);

		}

		break;
		
	case "shop_order":
	case "import_users":
		// Don't show the "Yoast SEO" section for WooCommerce Order & User imports
		break;
		
	// When most anything else is being imported...
	default:
	
		$yoast_addon->add_field( '_yoast_wpseo_focuskw', 'Focus Keyword', 'text', null, 'Pick the main keyword or keyphrase that this post/page is about.' );
		
		$yoast_addon->add_field( '_yoast_wpseo_title', 'SEO Title', 'text', null, 'The SEO title defaults to what is generated based on this sites title template for this posttype.' );

		$yoast_addon->add_field( '_yoast_wpseo_metadesc', 'Meta Description', 'text', null, 'The meta description will be limited to 156 chars. It is often shown as the black text under the title in a search result. For this to work it has to contain the keyword that was searched for.' );

		$yoast_addon->add_options(
			$yoast_addon->add_field( '_yoast_wpseo_opengraph-title', 'Facebook Title', 'text', null, "If you don't want to use the post title for sharing the post on Facebook but instead want another title there, import it here." ),
			'Facebook Options',
			array(
				$yoast_addon->add_field( '_yoast_wpseo_opengraph-description', 'Description', 'text', null, "If you don't want to use the meta description for sharing the post on Facebook but want another description there, write it here." ),
				$yoast_addon->add_field( '_yoast_wpseo_opengraph-image', 'Image', 'image', null, "If you want to override the image used on Facebook for this post, import one here. The recommended image size for Facebook is 1200 x 628px."),
			)
		);

		$yoast_addon->add_options(
			$yoast_addon->add_field( '_yoast_wpseo_twitter-title', 'Twitter Title', 'text', null, "If you don't want to use the post title for sharing the post on Twitter but instead want another title there, import it here." ),
			'Twitter Options',
			array(
				$yoast_addon->add_field( '_yoast_wpseo_twitter-description', 'Description', 'text', null, "If you don't want to use the meta description for sharing the post on Twitter but want another description there, import it here." ),
				$yoast_addon->add_field( '_yoast_wpseo_twitter-image', 'Image', 'image', null, "If you want to override the image used on Twitter for this post, import one here. The recommended image size for Twitter is 1024 x 512px."),
			)
		);

		$yoast_addon->add_options(
			null,
			'Advanced SEO Options',
			array(
				$yoast_addon->add_field( '_yoast_wpseo_meta-robots-noindex', 'Meta Robots Index', 'radio', 
					array(
						'' => 'default',
						'2' => 'index',
						'1' => 'noindex',
					),
					"This setting can be overwritten by Yoast WordPress SEO's sitewide privacy settings"
				),
				$yoast_addon->add_field( '_yoast_wpseo_meta-robots-nofollow', 'Meta Robots Nofollow', 'radio', 
					array(
						'' => 'Follow',
						'1' => 'Nofollow'
					) ),
				$yoast_addon->add_field( '_yst_is_cornerstone', 'This article is cornerstone content', 'radio', 
					array(
						'' => 'No',
						'1' => 'Yes'
					) ),
				$yoast_addon->add_field( '_yoast_wpseo_meta-robots-adv', 'Meta Robots Advanced', 'radio', 
					array(
						'' => 'default',
						'none' => 'None',
						'noimageindex' => 'No Image Index',
						'noarchive' => 'No Archive',
						'nosnippet' => 'No Snippet'
					),
					'Advanced meta robots settings for this page.'
				),
				$yoast_addon->add_field( '_yoast_wpseo_canonical', 'Canonical URL', 'text', null, 'The canonical URL that this page should point to, leave empty to default to permalink. Cross domain canonical supported too.' ),
				$yoast_addon->add_field( '_yoast_wpseo_primary_category_addon', 'Primary Category', 'text', null, 'The name or slug of the primary category' )
			)
		);
		
		$yoast_addon->set_import_function( 'yoast_seo_addon_import' );

		if (function_exists('is_plugin_active')) {

			if ( !is_plugin_active( "wordpress-seo/wp-seo.php" ) && !is_plugin_active( "wordpress-seo-premium/wp-seo-premium.php" ) ) {

				$yoast_addon->admin_notice(
					'The Yoast WordPress SEO Add-On requires WP All Import <a href="http://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=yoast" target="_blank">Pro</a> or <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a>, and the <a href="https://yoast.com/wordpress/plugins/seo/">Yoast WordPress SEO</a> plugin.',
					array(
						'plugins' => array('wordpress-seo/wp-seo.php')
					)
				);
			}

			if ( is_plugin_active( "wordpress-seo/wp-seo.php" ) || is_plugin_active( "wordpress-seo-premium/wp-seo-premium.php" ) ) {
				
				$yoast_addon->run();
				
			}
		}

		function yoast_seo_addon_import( $post_id, $data, $import_options, $article ) {

			global $yoast_addon;

			// all fields except for slider and image fields
			$fields = array(
				'_yoast_wpseo_focuskw',
				'_yoast_wpseo_title',
				'_yoast_wpseo_metadesc',
				'_yoast_wpseo_meta-robots-noindex',
				'_yoast_wpseo_meta-robots-nofollow',
				'_yoast_wpseo_meta-robots-adv',
				'_yoast_wpseo_canonical',
				'_yoast_wpseo_redirect',
				'_yoast_wpseo_opengraph-title',
				'_yoast_wpseo_opengraph-description',
				'_yoast_wpseo_twitter-title',
				'_yoast_wpseo_twitter-description',
				'_yoast_wpseo_primary_category_addon',
				'_yst_is_cornerstone'
			);
			
			// image fields
			$image_fields = array(
				'_yoast_wpseo_opengraph-image',
				'_yoast_wpseo_twitter-image'
			);
			
			$fields = array_merge( $fields, $image_fields );
			
			// update everything in fields arrays
			foreach ( $fields as $field ) {
				if ( $field == '_yoast_wpseo_primary_category_addon' ) {

							$title = $data[$field];

							$cat_slug = sanitize_title( $title ); // Get the slug for the Primary Category so we can match it later

							update_post_meta( $post_id, '_yoast_wpseo_addon_category_slug', $cat_slug );

							// Set post metas for regular categories and product categories so we know if we can update them after pmxi_saved_post hook fires.

							if ( empty( $article['ID'] ) or $yoast_addon->can_update_meta( '_yoast_wpseo_primary_category', $import_options ) ) {

								update_post_meta( $post_id, '_yoast_wpseo_primary_category_can_update', 1 );
							
							} else {

								update_post_meta( $post_id, '_yoast_wpseo_primary_category_can_update', 0 );

							}

							if ( empty( $article['ID'] ) or $yoast_addon->can_update_meta( '_yoast_wpseo_primary_product_cat', $import_options ) ) {

								update_post_meta( $post_id, '_yoast_wpseo_primary_product_cat_can_update', 1 );

							} else {

								update_post_meta( $post_id, '_yoast_wpseo_primary_product_cat_can_update', 0 );

							}

				} else {

					if ( empty($article['ID']) or $yoast_addon->can_update_meta( $field, $import_options ) ) {

						if ( in_array( $field, $image_fields ) ) {

							if ( $yoast_addon->can_update_image( $import_options ) ) {

								$id = $data[$field]['attachment_id'];
							
								$url = wp_get_attachment_url( $id );

								update_post_meta( $post_id, $field, $url );

							}

						} else {

							if ( $field == '_yoast_wpseo_focuskw' ) {

								update_post_meta( $post_id, $field, $data[$field] );
								update_post_meta( $post_id, '_yoast_wpseo_focuskw_text_input', $data[$field] );

							} elseif ( $field == "_yst_is_cornerstone" && empty( $data[$field] ) ) {
								
								if ( empty($article['ID']) ) {
									
									// Do nothing
									
								} else {
									
									delete_post_meta( $post_id, "_yst_is_cornerstone");
									
								}
								
							} else {

								update_post_meta( $post_id, $field, $data[$field] );

							}
						}
					}
				}
			}
			
		}
}


function yoast_addon_primary_category( $post_id ) {

	$product_update = get_post_meta( $post_id, '_yoast_wpseo_primary_product_cat_can_update', true ); // Can we update product primary categories?

	$post_update = get_post_meta( $post_id, '_yoast_wpseo_primary_category_can_update', true ); // Can we update post primary categories?

	// Only proceed if we have permission to update one of them.

	if ( $post_update == 1 or $product_update == 1 ) {
	
		$cat_slug = get_post_meta( $post_id, '_yoast_wpseo_addon_category_slug', true );

		if ( !empty( $cat_slug ) ) {

			$post_type = get_post_type( $post_id );

			if ( !empty( $cat_slug ) and !empty( $post_type ) ) {

				if ( $post_type == 'product' and $product_update == 1 ) { // Products use 'product_cat' instead of 'categories'.

		    		$cat = get_term_by( 'slug', $cat_slug, 'product_cat' ); 

		  			$cat_id = $cat->term_id;

		  			if ( !empty( $cat_id ) ) {

		  				update_post_meta( $post_id, '_yoast_wpseo_primary_product_cat', $cat_id );


	  				}

				} else {

					if ( $post_update == 1 ) {

						$cat = get_term_by( 'slug', $cat_slug, 'category' );
					
						$cat_id = $cat->term_id;

						if ( !empty( $cat_id ) ) {

							update_post_meta( $post_id, '_yoast_wpseo_primary_category', $cat_id );

						}
					}
				}
			}
		}
	}
	delete_post_meta( $post_id, '_yoast_wpseo_primary_category_can_update' );
	delete_post_meta( $post_id, '_yoast_wpseo_primary_product_cat_can_update' );
	delete_post_meta( $post_id, '_yoast_wpseo_addon_category_slug' );
}

function yoast_seo_addon_get_post_type() {
	/**
	* Show fields based on post type
	**/

	$custom_type = false;

	// Get import ID from URL or set to 'new'
	if ( isset( $_GET['import_id'] ) ) {
		$import_id = $_GET['import_id'];
	} elseif ( isset( $_GET['id'] ) ) {
		$import_id = $_GET['id'];
	}

	if ( empty( $import_id ) ) {
		$import_id = 'new';
	}

	// Declaring $wpdb as global to access database
	global $wpdb;

	// Get values from import data table
	$imports_table = $wpdb->prefix . 'pmxi_imports';

	// Get import session from database based on import ID or 'new'
	$import_options = $wpdb->get_row( $wpdb->prepare("SELECT options FROM $imports_table WHERE id = %d", $import_id), ARRAY_A );

	// If this is an existing import load the custom post type from the array
	if ( ! empty($import_options) )	{
		$import_options_arr = unserialize($import_options['options']);
		$custom_type = $import_options_arr['custom_type'];
	} else {
		// If this is a new import get the custom post type data from the current session
		$import_options = $wpdb->get_row( $wpdb->prepare("SELECT option_name, option_value FROM $wpdb->options WHERE option_name = %s", '_wpallimport_session_' . $import_id . '_'), ARRAY_A );				
		$import_options_arr = empty($import_options) ? array() : unserialize($import_options['option_value']);
		$custom_type = empty($import_options_arr['custom_type']) ? '' : $import_options_arr['custom_type'];		
	}
	return $custom_type;
}