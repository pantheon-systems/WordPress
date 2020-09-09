<?php
/**
 * Controller for sync behaviors
 *
 * @package Solr_Power
 */

/**
 * Controller for sync behaviors
 */
class SolrPower_Sync {

	/**
	 * Singleton instance
	 *
	 * @var SolrPower_Sync|Bool
	 */
	private static $instance = false;

	/**
	 * Last error message.
	 *
	 * @var string
	 */
	var $error_msg;

	/**
	 * Grab instance of object.
	 *
	 * @return SolrPower_Sync
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the object.
	 */
	function __construct() {
		add_action( 'publish_post', array( $this, 'handle_modified' ) );
		add_action( 'publish_page', array( $this, 'handle_modified' ) );
		add_action( 'save_post', array( $this, 'handle_modified' ) );
		add_action( 'delete_post', array( $this, 'handle_delete' ) );
		if ( is_multisite() ) {
			add_action( 'deactivate_blog', array( $this, 'delete_blog' ) );
			add_action( 'activate_blog', array( $this, 'handle_activate_blog' ) );
			add_action( 'archive_blog', array( $this, 'delete_blog' ) );
			add_action( 'unarchive_blog', array( $this, 'handle_activate_blog' ) );
			add_action( 'make_spam_blog', array( $this, 'delete_blog' ) );
			add_action( 'unspam_blog', array( $this, 'handle_activate_blog' ) );
			add_action( 'delete_blog', array( $this, 'delete_blog' ) );
			// WP 5.1 and higher.
			if ( function_exists( 'wp_insert_site' ) ) {
				add_action( 'wp_insert_site', array( $this, 'handle_activate_blog' ) );
			} else {
				add_action( 'wpmu_new_blog', array( $this, 'handle_activate_blog' ) );
			}
		}
	}

	/**
	 * Handle the post modification event.
	 *
	 * @param integer $post_id ID for the post.
	 */
	function handle_modified( $post_id ) {
		$post_info  = get_post( $post_id );
		$post_types = SolrPower::get_post_types();

		if ( ! in_array( $post_info->post_type, (array) $post_types, true ) ) {
			return;
		}

		$this->handle_status_change( $post_id, $post_info );
		$post_status = SolrPower::get_post_statuses();
		if ( 'revision' === $post_info->post_type || ! in_array( $post_info->post_status, $post_status, true ) ) {
			return;
		}
		// make sure this blog is not private or a spam if indexing on a multisite install.
		if ( is_multisite() && $this->is_private_blog() ) {
			return;
		}
		$docs   = array();
		$solr   = get_solr();
		$update = $solr->createUpdate();
		$doc    = $this->build_document( $update->createDocument(), $post_info );

		if ( $doc ) {
			$docs[] = $doc;
			$this->post( $docs );
		}

		return;
	}

	/**
	 * Handle the post delete event.
	 *
	 * @param integer $post_id ID for the post.
	 */
	function handle_delete( $post_id ) {
		$post_info            = get_post( $post_id );
		$plugin_s4wp_settings = solr_options();
		$delete_page          = $plugin_s4wp_settings['s4wp_delete_page'];
		$delete_post          = $plugin_s4wp_settings['s4wp_delete_post'];

		if ( is_multisite() ) {
			$this->delete( get_current_blog_id() . '_' . $post_info->ID );
		} else {
			$this->delete( $post_info->ID );
		}
	}

	/**
	 * Handle the blog activation event.
	 *
	 * @param object|integer $site Site object or simply its id.
	 */
	function handle_activate_blog( $site ) {
		$blogid = is_object( $site ) ? $site->id : $site;
		$this->load_blog_all( $blogid );
	}

	/**
	 * Delete the index for a blog
	 *
	 * @param integer $blogid ID for the blog.
	 */
	function delete_blog( $blogid ) {
		try {
			$solr = get_solr();
			if ( null !== $solr ) {
				$update = $solr->createUpdate();
				$update->addDeleteQuery( "blogid:{$blogid}" );
				$update->addCommit();
				$solr->update( $update );
			}
		} catch ( Exception $e ) {
			echo esc_html( $e->getMessage() );
		}
	}

	/**
	 * Check whether a blog is private
	 *
	 * @return boolean
	 */
	protected static function is_private_blog() {
		global $current_blog;

		return apply_filters( 'solr_is_private_blog', ( 1 != $current_blog->public || 1 == $current_blog->spam || 1 == $current_blog->archived ), $current_blog );
	}

	/**
	 * Index all blog content
	 *
	 * @param integer $blogid ID for the blog.
	 */
	function load_blog_all( $blogid ) {
		global $wpdb;
		$documents = array();
		$cnt       = 0;
		$batchsize = 10;

		$bloginfo = get_blog_details( $blogid, false );

		if ( $bloginfo->public && ! $bloginfo->archived && ! $bloginfo->spam && ! $bloginfo->deleted ) {
			$postids = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM %s WHERE post_type = 'post' and post_status = 'publish';", $wpdb->base_prefix . $blogid . '_posts' ) );

			$solr   = get_solr();
			$update = $solr->createUpdate();

			for ( $idx = 0; $idx < count( $postids ); $idx ++ ) {
				$postid      = $ids[ $idx ];
				$documents[] = $this->build_document( $update->createDocument(), get_blog_post( $blogid, $postid->ID ), $bloginfo->domain, $bloginfo->path );
				$cnt ++;
				if ( $cnt == $batchsize ) {
					$this->post( $documents );
					$cnt       = 0;
					$documents = array();
				}
			}

			if ( $documents ) {
				$this->post( $documents );
			}
		}
	}

	/**
	 * Handle the status change event.
	 *
	 * @param integer $post_id   Post ID.
	 * @param WP_Post $post_info Post object.
	 */
	function handle_status_change( $post_id, $post_info = null ) {
		if ( ! $post_info ) {
			$post_info = get_post( $post_id );
		}

		if ( is_multisite() ) {
			$this->delete( get_current_blog_id() . '_' . $post_info->ID );
		} else {
			$this->delete( $post_info->ID );
		}
	}

	/**
	 * Build a Solarium document
	 *
	 * @param Solarium\QueryType\Update\Query\Document\Document $doc       Existing Solarium document.
	 * @param WP_Post                                           $post_info Post object.
	 * @param string                                            $domain    Domain context.
	 * @param string                                            $path      Path context.
	 * @return Solarium\QueryType\Update\Query\Document\Document Generated Solarium document.
	 */
	function build_document(
		Solarium\QueryType\Update\Query\Document\Document $doc, $post_info, $domain = null,
		$path = null
	) {
		$plugin_s4wp_settings = solr_options();
		$exclude_ids          = ( is_array( $plugin_s4wp_settings['s4wp_exclude_pages'] ) ) ? $plugin_s4wp_settings['s4wp_exclude_pages'] : explode( ',', $plugin_s4wp_settings['s4wp_exclude_pages'] );
		$categoy_as_taxonomy  = $plugin_s4wp_settings['s4wp_cat_as_taxo'];
		$index_comments       = $plugin_s4wp_settings['s4wp_index_comments'];
		/**
		 * Filter indexed custom fields
		 *
		 * Filter the list of custom field slugs available to index.
		 *
		 * @param array $index_custom_fields Array of custom field slugs for indexing.
		 */
		$index_custom_fields = apply_filters( 'solr_index_custom_fields', $plugin_s4wp_settings['s4wp_index_custom_fields'] );

		if ( $post_info ) {

			// check if we need to exclude this document.
			if ( is_multisite() && in_array( substr( site_url(), 7 ) . $post_info->ID, (array) $exclude_ids ) ) {
				return null;
			} elseif ( ! is_multisite() && in_array( $post_info->ID, (array) $exclude_ids ) ) {
				return null;
			}

			$auth_info = get_userdata( $post_info->post_author );

			if ( is_multisite() ) {
				$blogid = get_current_blog_id();
				$doc->setField( 'solr_id', $blogid . '_' . $post_info->ID );
				$doc->setField( 'blogid', $blogid );
				$doc->setField( 'blogdomain', $domain );
				$doc->setField( 'blogpath', $path );
				$doc->setField( 'wp', 'multisite' );
			} else {
				$doc->setField( 'solr_id', $post_info->ID );
			}
			$doc->setField( 'ID', $post_info->ID );
			$doc->setField( 'permalink', get_permalink( $post_info->ID ) );
			$doc->setField( 'wp', 'wp' );

			$numcomments = 0;
			if ( $index_comments ) {
				$comments = get_comments( "status=approve&post_id={$post_info->ID}" );
				foreach ( $comments as $comment ) {
					$doc->addField( 'comments', $comment->comment_content );
					$numcomments += 1;
				}
			}
			$doc->setField( 'post_name', $post_info->post_name );
			$doc->setField( 'post_title', $post_info->post_title );
			$doc->setField( 'post_content', strip_tags( $post_info->post_content ) );
			$doc->setField( 'comment_count', $numcomments );
			if ( isset( $auth_info->display_name ) ) {
				$doc->setField( 'post_author', $auth_info->display_name );
			}
			if ( isset( $auth_info->user_nicename ) ) {
				$doc->setField( 'author_s', get_author_posts_url( $auth_info->ID, $auth_info->user_nicename ) );
			}
			$doc->setField( 'post_type', $post_info->post_type );
			$doc->setField( 'post_date_gmt', $this->format_date( $post_info->post_date_gmt ) );
			$doc->setField( 'post_modified_gmt', $this->format_date( $post_info->post_modified_gmt ) );
			$doc->setField( 'post_date', $this->format_date( $post_info->post_date ) );

			$doc->setField( 'post_modified', $this->format_date( $post_info->post_modified ) );
			$doc->setField( 'displaydate', $post_info->post_date );
			$doc->setField( 'displaymodified', $post_info->post_modified );

			$post_time = strtotime( $post_info->post_date );
			$doc->setField( 'year_i', gmdate( 'Y', $post_time ) );
			$doc->setField( 'month_i', gmdate( 'm', $post_time ) );
			$doc->setField( 'day_i', gmdate( 'd', $post_time ) );
			$doc->setField( 'week_i', gmdate( 'W', $post_time ) );
			$doc->setField( 'dayofweek_i', ( gmdate( 'w', $post_time ) + 1 ) );
			$doc->setField( 'dayofweek_iso_i', gmdate( 'w', $post_time ) );
			$doc->setField( 'hour_i', gmdate( 'H', $post_time ) );
			$doc->setField( 'minute_i', gmdate( 'i', $post_time ) );
			$doc->setField( 'second_i', gmdate( 's', $post_time ) );

			$post_time = strtotime( $post_info->post_modified );
			$doc->setField( 'post_modified_year_i', gmdate( 'Y', $post_time ) );
			$doc->setField( 'post_modified_month_i', gmdate( 'm', $post_time ) );
			$doc->setField( 'post_modified_day_i', gmdate( 'd', $post_time ) );
			$doc->setField( 'post_modified_week_i', gmdate( 'W', $post_time ) );
			$doc->setField( 'post_modified_dayofweek_i', ( gmdate( 'w', $post_time ) + 1 ) );
			$doc->setField( 'post_modified_dayofweek_iso_i', gmdate( 'w', $post_time ) );
			$doc->setField( 'post_modified_hour_i', gmdate( 'H', $post_time ) );
			$doc->setField( 'post_modified_minute_i', gmdate( 'i', $post_time ) );
			$doc->setField( 'post_modified_second_i', gmdate( 's', $post_time ) );

			$doc->setField( 'post_status', $post_info->post_status );
			$doc->setField( 'post_parent', $post_info->post_parent );
			$doc->setField( 'post_excerpt', $post_info->post_excerpt );

			$categories = get_the_category( $post_info->ID );
			if ( $categories ) {
				foreach ( $categories as $category ) {
					if ( $categoy_as_taxonomy ) {
						$doc->addField( 'categories', get_category_parents( $category->cat_ID, false, '^^' ) );
					} else {
						$doc->addField( 'categories', $category->cat_name );
					}
					$doc->addField( 'categories_str', $category->cat_name );
					$doc->addField( 'categories_slug_str', $category->slug );
					$doc->addField( 'categories_id', $category->term_id );
					$doc->addField( 'term_taxonomy_id', $category->term_taxonomy_id );
					// Category Parents too.
					$the_cat = $category;
					while ( 0 !== $the_cat->parent ) {
						$the_cat = get_category( $the_cat->parent );
						$doc->addField( 'parent_categories_str', $the_cat->cat_name );
						$doc->addField( 'parent_categories_slug_str', $the_cat->slug );
						$doc->addField( 'parent_categories_id', $the_cat->term_id );
						$doc->addField( 'parent_term_taxonomy_id', $the_cat->term_taxonomy_id );

					}
				}
			}

			// get all the taxonomy names used by wp.
			$taxonomies = (array) get_taxonomies(
				array(
					'_builtin' => false,
				),
				'names'
			);
			foreach ( $taxonomies as $parent ) {
				$terms = get_the_terms( $post_info->ID, $parent );
				if ( (array) $terms === $terms ) {
					// we are creating *_taxonomy as dynamic fields using our schema
					// so lets set up all our taxonomies in that format.
					$parent = $parent . '_taxonomy';
					foreach ( $terms as $term ) {
						$doc->addField( $parent . '_str', $term->name );
						$doc->addField( $parent . '_slug_str', $term->slug );
						$doc->addField( $parent . '_id', $term->term_id );
						$doc->addField( 'term_taxonomy_id', $term->term_taxonomy_id );
					}
				}
			}

			$tags = get_the_tags( $post_info->ID );
			if ( $tags ) {
				foreach ( $tags as $tag ) {
					$doc->addField( 'tags', $tag->name );
					$doc->addField( 'tags_slug_str', $tag->slug );
					$doc->addField( 'tags_id', $tag->term_id );
					$doc->addField( 'term_taxonomy_id', $tag->term_taxonomy_id );
				}
			}

			$custom_fields = get_post_custom( $post_info->ID );
			if ( count( $index_custom_fields ) > 0 && count( $custom_fields ) ) {
				$used = array();
				foreach ( (array) $index_custom_fields as $field_name ) {
					// test a php error notice.
					if ( isset( $custom_fields[ $field_name ] ) ) {
						$field = (array) $custom_fields[ $field_name ];

						foreach ( $field as $key => $value ) {
							$doc->addField( $field_name . '_str', $value );
							if ( ! in_array( $field_name, $used ) ) {
								if ( is_numeric( $value ) ) {
									$doc->addField( $field_name . '_i', intval( $value ) );
									$doc->addField( $field_name . '_d', floatval( preg_replace( '/[^-0-9\.]/', '', $value ) ) );
									$doc->addField( $field_name . '_f', floatval( preg_replace( '/[^-0-9\.]/', '', $value ) ) );
								}
								$doc->addField( $field_name . '_s', $value );
							}
							$doc->addField( $field_name . '_srch', $value );
							$used[] = $field_name;
						}
					}
				}
			}
		} // End if().

		/**
		 * Filter the generated Solr document.
		 *
		 * @param Solarium\QueryType\Update\Query\Document\Document $doc       Generated Solr document.
		 * @param WP_Post                                           $post_info Original post object.
		 */
		return apply_filters( 'solr_build_document', $doc, $post_info );
	}

	/**
	 * Post documents to the Solr index
	 *
	 * @param array   $documents Array of Solr documents.
	 * @param boolean $commit    Whether or not to commit the documents.
	 * @param boolean $optimize  Whether or not to optimize the index.
	 */
	function post( $documents, $commit = true, $optimize = false ) {
		try {
			$solr = get_solr();
			if ( null !== $solr ) {

				$update = $solr->createUpdate();

				if ( $documents ) {
					syslog( LOG_INFO, 'posting ' . count( $documents ) . ' documents for blog:' . get_bloginfo( 'wpurl' ) );
					$update->addDocuments( $documents );
				} else {
					syslog( LOG_INFO, 'posting failed documents for blog:' . get_bloginfo( 'wpurl' ) );
				}

				if ( $commit ) {
					syslog( LOG_INFO, 'telling Solr to commit' );
					$update->addCommit();
					$solr->update( $update );
				}

				if ( $optimize ) {
					$update = $solr->createUpdate();
					$update->addOptimize();
					$solr->update( $update );
					syslog( LOG_INFO, 'Optimizing: ' . get_bloginfo( 'wpurl' ) );
				}
				wp_cache_delete( 'solr_index_stats', 'solr' );
			} else {
				syslog( LOG_ERR, 'failed to get a solr instance created' );
				$this->error_msg = esc_html( 'failed to get a solr instance created' );

				return false;
			}
		} catch ( Exception $e ) {
			$this->error_msg = esc_html( $e->getMessage() );

			return false;
		} // End try().
	}

	/**
	 * Delete a specific document from the index
	 *
	 * @param integer $doc_id Document id.
	 */
	function delete( $doc_id ) {
		try {
			$solr = get_solr();
			if ( null !== $solr ) {
				$update = $solr->createUpdate();
				$update->addDeleteById( $doc_id );
				$update->addCommit();
				$solr->update( $update );
			}

			return true;
		} catch ( Exception $e ) {
			$this->error_msg = esc_html( $e->getMessage() );

			return false;
		}

	}

	/**
	 * Delete all documents in the index.
	 */
	function delete_all() {
		global $wpdb;
		try {
			$solr = get_solr();

			if ( null !== $solr ) {
				$update = $solr->createUpdate();
				$update->addDeleteQuery( '*:*' );
				$update->addCommit();
				$solr->update( $update );
			}
			wp_cache_delete( 'solr_index_stats', 'solr' );

			$option_names = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'solr_power_batch_%';" );
			foreach ( $option_names as $option_name ) {
				delete_option( $option_name );
			}

			return true;
		} catch ( Exception $e ) {
			$this->error_msg = esc_html( $e->getMessage() );

			return false;
		}
	}

	/**
	 * Load all posts into the index
	 *
	 * @param integer $prev      Offset to begin at.
	 * @param string  $post_type Post type to index.
	 * @param integer $limit     Number of posts to index.
	 * @param boolean $echo      Whether or not to echo output.
	 */
	function load_all_posts( $prev, $post_type = 'post', $limit = 5, $echo = true ) {
		global $wpdb, $current_site;

		$documents = array();
		$cnt       = 0;
		$batchsize = 500;
		$last      = '';
		$found     = false;
		$end       = false;
		$percent   = 0;
		// multisite logic is decided s4wp_get_option.
		$plugin_s4wp_settings = solr_options();
		if ( isset( $blog ) ) {
			$blog_id = $blog->blog_id;
		}
		if ( is_multisite() ) {

			// there is potential for this to run for an extended period of time, depending on the # of blogs.
			syslog( LOG_ERR, 'starting batch import, setting max execution time to unlimited' );
			ini_set( 'memory_limit', '1024M' );
			set_time_limit( 0 );

			// get a list of blog ids.
			$bloglist = $wpdb->get_col( "SELECT * FROM {$wpdb->base_prefix}blogs WHERE spam = 0 AND deleted = 0", 0 );
			syslog( LOG_INFO, 'pushing posts from ' . count( $bloglist ) . ' blogs into Solr' );
			foreach ( $bloglist as $bloginfo ) {

				// for each blog we need to import we get their id
				// and tell wordpress to switch to that blog.
				$blog_id = trim( $bloginfo );
				syslog( LOG_INFO, "switching to blogid $blog_id" );

				// attempt to save some memory by flushing wordpress's cache.
				wp_cache_flush();

				// everything just works better if we tell wordpress
				// to switch to the blog we're using, this is a multi-site
				// specific function.
				switch_to_blog( $blog_id );

				// now we actually gather the blog posts.
				$args    = array(

					/**
					 * Filter indexed post types
					 *
					 * Filter the list of post types available to index.
					 *
					 * @param array $post_types Array of post type names for indexing.
					 */
					'post_type'      => SolrPower::get_post_types(),
					'post_status'    => SolrPower::get_post_statuses(),
					'fields'         => 'ids',
					'posts_per_page' => absint( $limit ),
					'offset'         => absint( $prev ),
				);
				$query   = new WP_Query( $args );
				$postids = $query->posts;

				$postcount = count( $postids );
				syslog( LOG_INFO, "building $postcount documents for " . substr( get_bloginfo( 'wpurl' ), 7 ) );
				for ( $idx = 0; $idx < $postcount; $idx ++ ) {

					$postid  = $postids[ $idx ];
					$last    = $postid;
					$percent = ( floatval( $idx ) / floatval( $postcount ) ) * 100;
					if ( $prev && ! $found ) {
						if ( $postid === $prev ) {
							$found = true;
						}

						continue;
					}

					if ( $idx === $postcount - 1 ) {
						$end = true;
					}

					// using wpurl is better because it will return the proper
					// URL for the blog whether it is a subdomain install or otherwise.
					$solr        = get_solr();
					$update      = $solr->createUpdate();
					$documents[] = $this->build_document( $update->createDocument(), get_blog_post( $blog_id, $postid ), substr( get_bloginfo( 'wpurl' ), 7 ), $current_site->path );
					$cnt ++;
					if ( $cnt == $batchsize ) {
						$this->post( $documents, true, false );
						$this->post( false, true, false );
						wp_cache_flush();
						$cnt       = 0;
						$documents = array();
					}
				}
				// post the documents to Solr
				// and reset the batch counters.
				$this->post( $documents, true, false );
				$this->post( false, true, false );
				$cnt       = 0;
				$documents = array();
				syslog( LOG_INFO, "finished building $postcount documents for " . substr( get_bloginfo( 'wpurl' ), 7 ) );
				wp_cache_flush();
			} // End foreach().

			// done importing so lets switch back to the proper blog id.
			restore_current_blog();
		} else {
			$args      = array(

				/**
				 * Filter indexed post types
				 *
				 * Filter the list of post types available to index.
				 *
				 * @param array $post_types Array of post type names for indexing.
				 */
				'post_type'      => SolrPower::get_post_types(),
				'post_status'    => SolrPower::get_post_statuses(),
				'fields'         => 'ids',
				'posts_per_page' => absint( $limit ),
				'offset'         => absint( $prev ),
			);
			$query     = new WP_Query( $args );
			$posts     = $query->posts;
			$postcount = count( $posts );
			if ( 0 == $postcount ) {
				$end     = true;
				$results = sprintf( '{"type": "' . $post_type . '", "last": "%s", "end": true, "percent": "%.2f"}', $last, 100 );
				if ( $echo ) {
					echo $results;
				}
				die();
			}
			$last    = absint( $prev ) + 5;
			$percent = absint( ( floatval( $last ) / floatval( $query->found_posts ) ) * 100 );
			for ( $idx = 0; $idx < $postcount; $idx ++ ) {
				$postid = $posts[ $idx ];

				$solr        = get_solr();
				$update      = $solr->createUpdate();
				$documents[] = $this->build_document( $update->createDocument(), get_post( $postid ) );
				$cnt ++;
				if ( $cnt == $batchsize ) {
					$this->post( $documents, true, false );
					$cnt       = 0;
					$documents = array();
					wp_cache_flush();
					break;
				}
			}
		} // End if().

		if ( $documents ) {
			$this->post( $documents, true, false );
		}

		if ( 100 <= $percent ) {
			$results = sprintf( '{"type": "' . $post_type . '", "last": "%s", "end": true, "percent": "%.2f"}', $last, 100 );
			do_action( 'solr_power_index_all_finished' );
		} else {
			$results = sprintf( '{"type\": "' . $post_type . '", "last": "%s", "end": false, "percent": "%.2f"}', $last, $percent );
		}
		if ( $echo ) {
			echo $results;

			return;
		}

		return $results;
	}

	/**
	 * Format a date string
	 *
	 * @param string $thedate Date string.
	 * @return string
	 */
	function format_date( $thedate ) {
		$datere  = '/(\d{4}-\d{2}-\d{2})\s(\d{2}:\d{2}:\d{2})/';
		$replstr = '${1}T${2}Z';

		return preg_replace( $datere, $replstr, $thedate );
	}

	/**
	 * Copy config settings from the main blog to all other blogs
	 */
	function copy_config_to_all_blogs() {
		global $wpdb;

		$blogs = $wpdb->get_results( "SELECT blog_id FROM $wpdb->blogs WHERE spam = 0 AND deleted = 0" );

		$plugin_s4wp_settings = solr_options();
		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog->blog_id );
			wp_cache_flush();
			syslog( LOG_INFO, "pushing config to {$blog->blog_id}" );
			SolrPower_Options::get_instance()->update_option( $plugin_s4wp_settings );
		}

		wp_cache_flush();
		restore_current_blog();
	}

}
