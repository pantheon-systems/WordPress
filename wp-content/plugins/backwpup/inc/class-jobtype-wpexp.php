<?php
/**
 *
 */
class BackWPup_JobType_WPEXP extends BackWPup_JobTypes {

	/**
	 *
	 */
	public function __construct() {

		$this->info[ 'ID' ]        	 = 'WPEXP';
		$this->info[ 'name' ]        = __( 'XML export', 'backwpup' );
		$this->info[ 'description' ] = __( 'WordPress XML export', 'backwpup' );
		$this->info[ 'URI' ]         = __( 'http://backwpup.com', 'backwpup' );
		$this->info[ 'author' ]      = 'Inpsyde GmbH';
		$this->info[ 'authorURI' ]   = __( 'http://inpsyde.com', 'backwpup' );
		$this->info[ 'version' ]     = BackWPup::get_plugin_data( 'Version' );

	}

	/**
	 * @return bool
	 */
	public function creates_file() {

		return TRUE;
	}

	/**
	 * @return array
	 */
	public function option_defaults() {
		return array( 'wpexportcontent' => 'all', 'wpexportfilecompression' => '', 'wpexportfile' => sanitize_text_field( get_bloginfo( 'name' ) ) . '.wordpress.%Y-%m-%d' );
	}


	/**
	 * @param $jobid
	 * @internal param $main
	 */
	public function edit_tab( $jobid ) {
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Items to export', 'backwpup' ) ?></th>
				<td>
					<fieldset>
						<label for="idwpexportcontent-all"><input type="radio" name="wpexportcontent" id="idwpexportcontent-all" value="all" <?php checked( BackWPup_Option::get( $jobid, 'wpexportcontent' ), 'all' ); ?> /> <?php _e( 'All content', 'backwpup' ); ?></label><br />
						<label for="idwpexportcontent-post"><input type="radio" name="wpexportcontent" id="idwpexportcontent-post" value="post" <?php checked( BackWPup_Option::get( $jobid, 'wpexportcontent' ), 'post' ); ?> /> <?php _e( 'Posts', 'backwpup' ); ?></label><br />
						<label for="idwpexportcontent-page"><input type="radio" name="wpexportcontent" id="idwpexportcontent-page" value="page" <?php checked( BackWPup_Option::get( $jobid, 'wpexportcontent' ), 'page' ); ?> /> <?php _e( 'Pages', 'backwpup' ); ?></label><br />
						<?php
						foreach ( get_post_types( array( '_builtin' => FALSE, 'can_export' => TRUE ), 'objects' ) as $post_type ) {
							?>
							<label for="idwpexportcontent-<?php echo esc_attr( $post_type->name ); ?>"><input type="radio" name="wpexportcontent" id="idwpexportcontent-<?php echo esc_attr( $post_type->name ); ?>" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( BackWPup_Option::get( $jobid, 'wpexportcontent' ), esc_attr( $post_type->name ) ); ?> /> <?php echo esc_html( $post_type->label ); ?></label><br />
						<?php } ?>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="idwpexportfile"><?php esc_html_e( 'XML Export file name', 'backwpup' ) ?></label></th>
				<td>
					<input name="wpexportfile" type="text" id="idwpexportfile"
						   value="<?php echo esc_attr(BackWPup_Option::get( $jobid, 'wpexportfile' ));?>"
						   class="medium-text code"/>.xml
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'File compression', 'backwpup' ) ?></th>
				<td>
					<fieldset>
						<?php
						echo '<label for="idwpexportfilecompression"><input class="radio" type="radio"' . checked( '', BackWPup_Option::get( $jobid, 'wpexportfilecompression' ), FALSE ) . ' name="wpexportfilecompression" id="idwpexportfilecompression" value="" /> ' . esc_html__( 'none', 'backwpup' ). '</label><br />';
						if ( function_exists( 'gzopen' ) )
							echo '<label for="idwpexportfilecompression-gz"><input class="radio" type="radio"' . checked( '.gz', BackWPup_Option::get( $jobid, 'wpexportfilecompression' ), FALSE ) . ' name="wpexportfilecompression" id="idwpexportfilecompression-gz" value=".gz" /> ' . esc_html__( 'GZip', 'backwpup' ). '</label><br />';
						else
							echo '<label for="idwpexportfilecompression-gz"><input class="radio" type="radio"' . checked( '.gz', BackWPup_Option::get( $jobid, 'wpexportfilecompression' ), FALSE ) . ' name="wpexportfilecompression" id="idwpexportfilecompression-gz" value=".gz" disabled="disabled" /> ' . esc_html__( 'GZip', 'backwpup' ). '</label><br />';
						if ( function_exists( 'bzopen' ) )
							echo '<label for="idwpexportfilecompression-bz2"><input class="radio" type="radio"' . checked( '.bz2', BackWPup_Option::get( $jobid, 'wpexportfilecompression' ), FALSE ) . ' name="wpexportfilecompression" id="idwpexportfilecompression-bz2" value=".bz2" /> ' . esc_html__( 'BZip2', 'backwpup' ). '</label><br />';
						else
							echo '<label for="idwpexportfilecompression-bz2"><input class="radio" type="radio"' . checked( '.bz2', BackWPup_Option::get( $jobid, 'wpexportfilecompression' ), FALSE ) . ' name="wpexportfilecompression" id="idwpexportfilecompression-bz2" value=".bz2" disabled="disabled" /> ' . esc_html__( 'BZip2', 'backwpup' ). '</label><br />';
						?>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * @param $id
	 */
	public function edit_form_post_save( $id ) {

		BackWPup_Option::update( $id, 'wpexportcontent', sanitize_text_field( $_POST[ 'wpexportcontent' ] ) );
		BackWPup_Option::update( $id, 'wpexportfile', sanitize_file_name( $_POST[ 'wpexportfile' ] ) );
		if ( $_POST[ 'wpexportfilecompression' ] === '' || $_POST[ 'wpexportfilecompression' ] === '.gz' || $_POST[ 'wpexportfilecompression' ] === '.bz2' ) {
			BackWPup_Option::update( $id, 'wpexportfilecompression', $_POST[ 'wpexportfilecompression' ] );
		}
	}

	/**
	 * @param $job_object
	 * @return bool
	 */
	public function job_run( BackWPup_Job $job_object ) {
		global $wpdb, $post, $wp_query;

		$wxr_version = '1.2';

		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ) {
			$job_object->log( sprintf( __( '%d. Trying to create a WordPress export to XML file&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ) );
			$job_object->steps_data[ $job_object->step_working ]['wpexportfile'] = BackWPup::get_plugin_data( 'TEMP' ) . $job_object->generate_filename( $job_object->job[ 'wpexportfile' ], 'xml', TRUE );
			$job_object->steps_data[ $job_object->step_working ]['substep'] = 'header';
			$job_object->steps_data[ $job_object->step_working ]['post_ids'] = array();
			$job_object->substeps_todo = 10;
			$job_object->substeps_done = 0;
		}

		add_filter( 'wxr_export_skip_postmeta', array( $this, 'wxr_filter_postmeta' ), 10, 2 );

		if ( $job_object->steps_data[ $job_object->step_working ]['substep'] == 'header' ) {

			if ( 'all' != $job_object->job[ 'wpexportcontent' ] && post_type_exists( $job_object->job[ 'wpexportcontent' ] ) ) {
				$ptype = get_post_type_object( $job_object->job[ 'wpexportcontent' ] );
				if ( ! $ptype->can_export ) {
					$job_object->log( sprintf( __( 'WP Export: Post type “%s” does not allow export.', 'backwpup' ), $job_object->job[ 'wpexportcontent' ] ), E_USER_ERROR );
					return FALSE;
				}
				$where = $wpdb->prepare( "{$wpdb->posts}.post_type = %s", $job_object->job[ 'wpexportcontent' ] );
			} else {
				$post_types = get_post_types( array( 'can_export' => true ) );
				$esses = array_fill( 0, count($post_types), '%s' );
				$where = $wpdb->prepare( "{$wpdb->posts}.post_type IN (" . implode( ',', $esses ) . ')', $post_types );
				$job_object->job[ 'wpexportcontent' ] = 'all';
			}
			$where .= " AND {$wpdb->posts}.post_status != 'auto-draft'";

			// grab a snapshot of post IDs, just in case it changes during the export
			$job_object->steps_data[ $job_object->step_working ]['post_ids'] = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE $where" );
			$job_object->substeps_todo = $job_object->substeps_todo + count( $job_object->steps_data[ $job_object->step_working ]['post_ids'] );

			$header  = '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
			$header .= "<!-- This is a WordPress eXtended RSS file generated by the WordPress plugin BackWPup as an export of your site. -->\n";
			$header .= "<!-- It contains information about your site's posts, pages, comments, categories, and other content. -->\n";
			$header .= "<!-- You may use this file to transfer that content from one site to another. -->\n";
			$header .= "<!-- This file is not intended to serve as a complete backup of your site. -->\n\n";
			$header .= "<!-- To import this information into a WordPress site follow these steps: -->\n";
			$header .= "<!-- 1. Log in to that site as an administrator. -->\n";
			$header .= "<!-- 2. Go to Tools: Import in the WordPress admin panel. -->\n";
			$header .= "<!-- 3. Install the \"WordPress\" importer from the list. -->\n";
			$header .= "<!-- 4. Activate & Run Importer. -->\n";
			$header .= "<!-- 5. Upload this file using the form provided on that page. -->\n";
			$header .= "<!-- 6. You will first be asked to map the authors in this export file to users -->\n";
			$header .= "<!--    on the site. For each author, you may choose to map to an -->\n";
			$header .= "<!--    existing user on the site or to create a new user. -->\n";
			$header .= "<!-- 7. WordPress will then import each of the posts, pages, comments, categories, etc. -->\n";
			$header .= "<!--    contained in this file into your site. -->\n\n";
			$header .= "<!-- generator=\"WordPress/" . get_bloginfo_rss('version') . "\" created=\"". date('Y-m-d H:i') . "\" -->\n";
			$header .= "<rss version=\"2.0\" xmlns:excerpt=\"http://wordpress.org/export/$wxr_version/excerpt/\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:wp=\"http://wordpress.org/export/$wxr_version/\">\n";
			$header .= "<channel>\n";
			$header .= "\t<title>" . get_bloginfo_rss( 'name' ) ."</title>\n";
			$header .= "\t<link>" . get_bloginfo_rss( 'url' ) ."</link>\n";
			$header .= "\t<description>" . get_bloginfo_rss( 'description' ) ."</description>\n";
			$header .= "\t<pubDate>" . date( 'D, d M Y H:i:s +0000' ) ."</pubDate>\n";
			$header .= "\t<language>" . get_bloginfo_rss( 'language' ) ."</language>\n";
			$header .= "\t<wp:wxr_version>" . $wxr_version ."</wp:wxr_version>\n";
			$header .= "\t<wp:base_site_url>" . $this->wxr_site_url() ."</wp:base_site_url>\n";
			$header .= "\t<wp:base_blog_url>" . get_bloginfo_rss( 'url' ) ."</wp:base_blog_url>\n";
			$written = file_put_contents( $job_object->steps_data[ $job_object->step_working ][ 'wpexportfile' ], $header, FILE_APPEND );
			if ( $written === FALSE ) {
				$job_object->log( __( 'WP Export file could not written.', 'backwpup' ), E_USER_ERROR );

				return FALSE;
			}
			unset( $header );
			$job_object->steps_data[ $job_object->step_working ]['substep'] = 'authors';
			$job_object->substeps_done ++;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		if ( $job_object->steps_data[ $job_object->step_working ]['substep'] == 'authors' ) {
			$written = file_put_contents( $job_object->steps_data[ $job_object->step_working ][ 'wpexportfile' ], $this->wxr_authors_list(), FILE_APPEND );
			if ( $written === FALSE ) {
				$job_object->log( __( 'WP Export file could not written.', 'backwpup' ), E_USER_ERROR );

				return FALSE;
			}
			$job_object->steps_data[ $job_object->step_working ]['substep'] = 'cats';
			$job_object->substeps_done ++;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}


		if ( $job_object->steps_data[ $job_object->step_working ]['substep'] == 'cats' ) {
			if ( 'all' == $job_object->job[ 'wpexportcontent' ] ) {
				$cats = array();
				$categories = (array) get_categories( array( 'get' => 'all' ) );
				// put categories in order with no child going before its parent
				while ( $cat = array_shift( $categories ) ) {
					if ( $cat->parent == 0 || isset( $cats[$cat->parent] ) )
						$cats[$cat->term_id] = $cat;
					else
						$categories[] = $cat;
				}
				$cats_xml = '';
				foreach ( $cats as $c ) {
					$parent_slug = $c->parent ? $cats[$c->parent]->slug : '';
					$cats_xml .= "\t<wp:category><wp:term_id>" . $c->term_id ."</wp:term_id><wp:category_nicename>" . $c->slug . "</wp:category_nicename><wp:category_parent>" . $parent_slug . "</wp:category_parent>" . $this->wxr_cat_name( $c ) . $this->wxr_category_description( $c ) ."</wp:category>\n";
				}
				$written = file_put_contents( $job_object->steps_data[ $job_object->step_working ][ 'wpexportfile' ], $cats_xml, FILE_APPEND );
				if ( $written === FALSE ) {
					$job_object->log( __( 'WP Export file could not written.', 'backwpup' ), E_USER_ERROR );

					return FALSE;
				}
				unset( $cats_xml );
			}
			$job_object->steps_data[ $job_object->step_working ]['substep'] = 'tags';
			$job_object->substeps_done ++;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		if ( $job_object->steps_data[ $job_object->step_working ]['substep'] == 'tags' ) {
			if ( 'all' == $job_object->job[ 'wpexportcontent' ] ) {
				$tags = (array) get_tags( array( 'get' => 'all' ) );
				$tags_xml = '';
				foreach ( $tags as $t ) {
					$tags_xml .= "\t<wp:tag><wp:term_id>" . $t->term_id ."</wp:term_id><wp:tag_slug>" . $t->slug ."</wp:tag_slug>" . $this->wxr_tag_name( $t ) . $this->wxr_tag_description( $t ) ."</wp:tag>\n";
				}
				$written = file_put_contents( $job_object->steps_data[ $job_object->step_working ][ 'wpexportfile' ], $tags_xml, FILE_APPEND );
				if ( $written === FALSE ) {
					$job_object->log( __( 'WP Export file could not written.', 'backwpup' ), E_USER_ERROR );

					return FALSE;
				}
				unset( $tags_xml );
			}
			$job_object->steps_data[ $job_object->step_working ]['substep'] = 'terms';
			$job_object->substeps_done ++;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		if ( $job_object->steps_data[ $job_object->step_working ]['substep'] == 'terms' ) {
			if ( 'all' == $job_object->job[ 'wpexportcontent' ] ) {

				$terms = array();
				$custom_taxonomies = get_taxonomies( array( '_builtin' => false ) );
				$custom_terms = (array) get_terms( $custom_taxonomies, array( 'get' => 'all' ) );

				// put terms in order with no child going before its parent
				while ( $t = array_shift( $custom_terms ) ) {
					if ( $t->parent == 0 || isset( $terms[$t->parent] ) )
						$terms[$t->term_id] = $t;
					else
						$custom_terms[] = $t;
				}
				$terms_xml = '';
				foreach ( $terms as $t ) {
					$parent_slug =  $t->parent ? $terms[$t->parent]->slug : '';
					$terms_xml .= "\t<wp:term><wp:term_id>" . $t->term_id ."</wp:term_id><wp:term_taxonomy>" . $t->taxonomy . "</wp:term_taxonomy><wp:term_slug>" . $t->slug ."</wp:term_slug><wp:term_parent>" . $parent_slug ."</wp:term_parent>" . $this->wxr_term_name( $t ) . $this->wxr_term_description( $t ) ."</wp:term>\n";
				}
				$written = file_put_contents( $job_object->steps_data[ $job_object->step_working ][ 'wpexportfile' ], $terms_xml, FILE_APPEND );
				if ( $written === FALSE ) {
					$job_object->log( __( 'WP Export file could not written.', 'backwpup' ), E_USER_ERROR );

					return FALSE;
				}
				unset( $terms_xml );
			}
			$job_object->steps_data[ $job_object->step_working ]['substep'] = 'menus';
			$job_object->substeps_done ++;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		if ( $job_object->steps_data[ $job_object->step_working ]['substep'] == 'menus' ) {
			$menu_xml = '';
			if ( 'all' == $job_object->job[ 'wpexportcontent' ] ) {
				$menu_xml .= $this->wxr_nav_menu_terms();
			}
			$menu_xml .= "\t<generator>http://wordpress.org/?v=" . get_bloginfo_rss( 'version' ) . "</generator>\n";
			$written = file_put_contents( $job_object->steps_data[ $job_object->step_working ][ 'wpexportfile' ], $menu_xml, FILE_APPEND );
			if ( $written === FALSE ) {
				$job_object->log( __( 'WP Export file could not written.', 'backwpup' ), E_USER_ERROR );

				return FALSE;
			}
			unset( $menu_xml );

			$job_object->steps_data[ $job_object->step_working ]['substep'] = 'posts';
			$job_object->substeps_done ++;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}


		if ( $job_object->steps_data[ $job_object->step_working ]['substep'] == 'posts' ) {

			if ( ! empty( $job_object->steps_data[ $job_object->step_working ]['post_ids'] ) ) {
				$wp_query->in_the_loop = true; // Fake being in the loop.

				// fetch 20 posts at a time rather than loading the entire table into memory
				while ( $next_posts = array_splice( $job_object->steps_data[ $job_object->step_working ]['post_ids'], 0, 20 ) ) {
					$where = 'WHERE ID IN (' . join( ',', $next_posts ) . ')';
					$posts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} $where" );
					$wxr_post = '';
					// Begin Loop
					foreach ( $posts as $post ) {
						/* @var WP_Post $post */

						$is_sticky = is_sticky( $post->ID ) ? 1 : 0;

						$wxr_post .= "\t<item>\n";
						$wxr_post .= "\t\t<title>" . apply_filters( 'the_title_rss', $post->post_title ) ."</title>\n";
						$wxr_post .= "\t\t<link>" . esc_url( apply_filters( 'the_permalink_rss', get_permalink( $post ) ) ) ."</link>\n";
						$wxr_post .= "\t\t<pubDate>" . mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true, $post ), false ) ."</pubDate>\n";
						$wxr_post .= "\t\t<dc:creator>" . $this->wxr_cdata( get_the_author_meta( 'login', $post->post_author ) ) ."</dc:creator>\n";
						$wxr_post .= "\t\t<guid isPermaLink=\"false\">" . esc_url( get_the_guid( $post->ID ) ) ."</guid>\n";
						$wxr_post .= "\t\t<description></description>\n";
						$wxr_post .= "\t\t<content:encoded>" . $this->wxr_cdata( apply_filters( 'the_content_export', $post->post_content ) ) . "</content:encoded>\n";
						$wxr_post .= "\t\t<excerpt:encoded>" . $this->wxr_cdata( apply_filters( 'the_excerpt_export', $post->post_excerpt ) ) . "</excerpt:encoded>\n";
						$wxr_post .= "\t\t<wp:post_id>" . $post->ID . "</wp:post_id>\n";
						$wxr_post .= "\t\t<wp:post_date>" . $post->post_date . "</wp:post_date>\n";
						$wxr_post .= "\t\t<wp:post_date_gmt>" . $post->post_date_gmt . "</wp:post_date_gmt>\n";
						$wxr_post .= "\t\t<wp:comment_status>" . $post->comment_status . "</wp:comment_status>\n";
						$wxr_post .= "\t\t<wp:ping_status>" . $post->ping_status . "</wp:ping_status>\n";
						$wxr_post .= "\t\t<wp:post_name>" . $post->post_name . "</wp:post_name>\n";
						$wxr_post .= "\t\t<wp:status>" . $post->post_status . "</wp:status>\n";
						$wxr_post .= "\t\t<wp:post_parent>" . $post->post_parent . "</wp:post_parent>\n";
						$wxr_post .= "\t\t<wp:menu_order>" . $post->menu_order . "</wp:menu_order>\n";
						$wxr_post .= "\t\t<wp:post_type>" . $post->post_type . "</wp:post_type>\n";
						$wxr_post .= "\t\t<wp:post_password>" . $post->post_password . "</wp:post_password>\n";
						$wxr_post .= "\t\t<wp:is_sticky>" . $is_sticky . "</wp:is_sticky>\n";
						if ( $post->post_type == 'attachment' ) {
							$wxr_post .= "\t\t<wp:attachment_url>" . wp_get_attachment_url( $post->ID ) . "</wp:attachment_url>\n";
						}
						$wxr_post .= $this->wxr_post_taxonomy();

						$postmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $post->ID ) );
						foreach ( $postmeta as $meta ) {
							if ( apply_filters( 'wxr_export_skip_postmeta', false, $meta->meta_key, $meta ) ) {
								continue;
							}
							$wxr_post .= "\t\t<wp:postmeta>\n\t\t\t<wp:meta_key>" . $meta->meta_key ."</wp:meta_key>\n\t\t\t<wp:meta_value>" .$this->wxr_cdata( $meta->meta_value ) ."</wp:meta_value>\n\t\t</wp:postmeta>\n";
						}

						$comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved <> 'spam'", $post->ID ) );
						foreach ( $comments as $c ) {
							$wxr_post .= "\t\t<wp:comment>\n";
							$wxr_post .= "\t\t\t<wp:comment_id>" . $c->comment_ID . "</wp:comment_id>\n";
							$wxr_post .= "\t\t\t<wp:comment_author>" . $this->wxr_cdata( $c->comment_author ) . "</wp:comment_author>\n";
							$wxr_post .= "\t\t\t<wp:comment_author_email>" . $c->comment_author_email . "</wp:comment_author_email>\n";
							$wxr_post .= "\t\t\t<wp:comment_author_url>" . esc_url_raw( $c->comment_author_url ) . "</wp:comment_author_url>\n";
							$wxr_post .= "\t\t\t<wp:comment_author_IP>" . $c->comment_author_IP . "</wp:comment_author_IP>\n";
							$wxr_post .= "\t\t\t<wp:comment_date>" . $c->comment_date . "</wp:comment_date>\n";
							$wxr_post .= "\t\t\t<wp:comment_date_gmt>" . $c->comment_date_gmt . "</wp:comment_date_gmt>\n";
							$wxr_post .= "\t\t\t<wp:comment_content>" . $this->wxr_cdata( $c->comment_content ) . "</wp:comment_content>\n";
							$wxr_post .= "\t\t\t<wp:comment_approved>" . $c->comment_approved . "</wp:comment_approved>\n";
							$wxr_post .= "\t\t\t<wp:comment_type>" . $c->comment_type . "</wp:comment_type>\n";
							$wxr_post .= "\t\t\t<wp:comment_parent>" . $c->comment_parent . "</wp:comment_parent>\n";
							$wxr_post .= "\t\t\t<wp:comment_user_id>" . $c->user_id . "</wp:comment_user_id>\n";
							$c_meta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->commentmeta WHERE comment_id = %d", $c->comment_ID ) );
							foreach ( $c_meta as $meta ) {
								$wxr_post .= "\t\t\t<wp:commentmeta>\n\t\t\t\t<wp:meta_key>" . $meta->meta_key ."</wp:meta_key>\n\t\t\t\t<wp:meta_value>" .$this->wxr_cdata( $meta->meta_value ) ."</wp:meta_value>\n\t\t\t</wp:commentmeta>\n";
							}
							$wxr_post .= "\t\t</wp:comment>\n";
						}
						$wxr_post .= "\t</item>\n";
						$job_object->substeps_done ++;
					}
					$written = file_put_contents( $job_object->steps_data[ $job_object->step_working ][ 'wpexportfile' ], $wxr_post, FILE_APPEND );
					if ( $written === FALSE ) {
						$job_object->log( __( 'WP Export file could not written.', 'backwpup' ), E_USER_ERROR );

						return FALSE;
					}
					$job_object->do_restart_time();
				}
			}
			$written = file_put_contents( $job_object->steps_data[ $job_object->step_working ][ 'wpexportfile' ], "</channel>\n</rss>", FILE_APPEND );
			if ( $written === FALSE ) {
				$job_object->log( __( 'WP Export file could not written.', 'backwpup' ), E_USER_ERROR );

				return FALSE;
			}
			$job_object->steps_data[ $job_object->step_working ]['substep'] = 'check';
			$job_object->substeps_done ++;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		remove_filter( 'wxr_export_skip_postmeta', array( $this, 'wxr_filter_postmeta' ), 10 );

		if ( $job_object->steps_data[ $job_object->step_working ]['substep'] == 'check' ) {

			if ( extension_loaded( 'simplexml' ) && class_exists( 'DOMDocument' ) ) {
				$job_object->log( __( 'Check WP Export file&#160;&hellip;', 'backwpup' ) );
				$job_object->need_free_memory( filesize( $job_object->steps_data[ $job_object->step_working ]['wpexportfile'] ) * 2 );
				$valid = TRUE;

				$internal_errors = libxml_use_internal_errors( TRUE );
				$dom = new DOMDocument;
				$old_value = NULL;
				if ( function_exists( 'libxml_disable_entity_loader' ) )
					$old_value = libxml_disable_entity_loader( TRUE );
				$success = $dom->loadXML( file_get_contents( $job_object->steps_data[ $job_object->step_working ]['wpexportfile'] ) );
				if ( ! is_null( $old_value ) )
					libxml_disable_entity_loader( $old_value );

				if ( ! $success || isset( $dom->doctype ) ) {
					$errors = libxml_get_errors();
					$valid = FALSE;

					foreach ( $errors as $error ) {
						switch ( $error->level ) {
							case LIBXML_ERR_WARNING:
								$job_object->log( E_USER_WARNING, sprintf( __( 'XML WARNING (%s): %s', 'backwpup' ), $error->code, trim( $error->message ) ), $job_object->steps_data[ $job_object->step_working ]['wpexportfile'], $error->line );
								break;
							case LIBXML_ERR_ERROR:
								$job_object->log( E_USER_WARNING, sprintf( __( 'XML RECOVERABLE (%s): %s', 'backwpup' ), $error->code,  trim( $error->message ) ), $job_object->steps_data[ $job_object->step_working ]['wpexportfile'], $error->line  );
								break;
							case LIBXML_ERR_FATAL:
								$job_object->log( E_USER_WARNING, sprintf( __( 'XML ERROR (%s): %s', 'backwpup' ),$error->code,  trim( $error->message ) ), $job_object->steps_data[ $job_object->step_working ]['wpexportfile'], $error->line );
								break;
						}
					}
				} else {
					$xml = simplexml_import_dom( $dom );
					unset( $dom );

					// halt if loading produces an error
					if ( ! $xml ) {
						$job_object->log( __( 'There was an error when reading this WXR file', 'backwpup' ), E_USER_ERROR );
						$valid = FALSE;
					} else {

						$wxr_version = $xml->xpath('/rss/channel/wp:wxr_version');
						if ( ! $wxr_version ) {
							$job_object->log( __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'backwpup' ), E_USER_ERROR );
							$valid = FALSE;
						}

						$wxr_version = (string) trim( $wxr_version[0] );
						// confirm that we are dealing with the correct file format
						if ( ! preg_match( '/^\d+\.\d+$/', $wxr_version ) ) {
							$job_object->log( __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'backwpup' ), E_USER_ERROR );
							$valid = FALSE;
						}
					}
				}

				libxml_use_internal_errors( $internal_errors );

				if ( $valid )
					$job_object->log( __( 'WP Export file is a valid WXR file.', 'backwpup' ) );
			} else {
				$job_object->log( __( 'WP Export file can not be checked, because no XML extension is loaded, to ensure the file verification.', 'backwpup' ) );
			}

			$job_object->steps_data[ $job_object->step_working ]['substep'] = 'compress';
			$job_object->substeps_done ++;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		//Compress file
		if ( $job_object->steps_data[ $job_object->step_working ]['substep'] == 'compress' ) {
			if ( ! empty( $job_object->job[ 'wpexportfilecompression' ] ) ) {
				$job_object->log( __( 'Compressing file&#160;&hellip;', 'backwpup' ) );
				try {
					$compress = new BackWPup_Create_Archive( $job_object->steps_data[ $job_object->step_working ]['wpexportfile'] . $job_object->job[ 'wpexportfilecompression' ] );
					if ( $compress->add_file( $job_object->steps_data[ $job_object->step_working ]['wpexportfile'] ) ) {
						unset( $compress );
						unlink( $job_object->steps_data[ $job_object->step_working ]['wpexportfile'] );
						$job_object->steps_data[ $job_object->step_working ]['wpexportfile'] .= $job_object->job[ 'wpexportfilecompression' ];
						$job_object->log( __( 'Compressing done.', 'backwpup' ) );
					}
				} catch ( Exception $e ) {
					$job_object->log( $e->getMessage(), E_USER_ERROR, $e->getFile(), $e->getLine() );
					unset( $compress );
					return FALSE;
				}
			}
			$job_object->steps_data[ $job_object->step_working ]['substep'] = 'addfile';
			$job_object->substeps_done ++;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		if ( $job_object->steps_data[ $job_object->step_working ]['substep'] == 'addfile' ) {
			//add XML file to backup files
			if ( is_readable( $job_object->steps_data[ $job_object->step_working ]['wpexportfile'] ) ) {
				$job_object->additional_files_to_backup[ ] = $job_object->steps_data[ $job_object->step_working ]['wpexportfile'];
				$filesize = filesize( $job_object->steps_data[ $job_object->step_working ][ 'wpexportfile' ] );
				$job_object->log( sprintf( __( 'Added XML export "%1$s" with %2$s to backup file list.', 'backwpup' ), basename( $job_object->steps_data[ $job_object->step_working ]['wpexportfile'] ), size_format( $filesize, 2 ) ) );
			}
			$job_object->substeps_done ++;
			$job_object->update_working_data();
		}

		return TRUE;
	}

	/**
	 * Wrap given string in XML CDATA tag.
	 *
	 * @since WordPress 2.1.0
	 *
	 * @param string $str String to wrap in XML CDATA tag.
	 * @return string
	 */
	private function wxr_cdata( $str ) {
		if ( ! seems_utf8( $str ) ) {
			$str = utf8_encode( $str );
		}

		// not allowed UTF-8 chars in XML
		$str = preg_replace( '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $str );

		// $str = ent2ncr(esc_html($str));
		$str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

		return $str;
	}

	/**
	 * Return the URL of the site
	 *
	 * @since WordPress 2.5.0
	 *
	 * @return string Site URL.
	 */
	private function wxr_site_url() {
		// ms: the base url
		if ( is_multisite() )
			return network_home_url();
		// wp: the blog url
		else
			return get_bloginfo_rss( 'url' );
	}

	/**
	 * Output a cat_name XML tag from a given category object
	 *
	 * @since WordPress 2.1.0
	 *
	 * @param object $category Category Object
	 * @return string
	 */
	private function wxr_cat_name( $category ) {
		if ( empty( $category->name ) )
			return '';

		return '<wp:cat_name>' . $this->wxr_cdata( $category->name ) . '</wp:cat_name>';
	}

	/**
	 * Output a category_description XML tag from a given category object
	 *
	 * @since WordPress 2.1.0
	 *
	 * @param object $category Category Object
	 * @return string
	 */
	private function wxr_category_description( $category ) {
		if ( empty( $category->description ) )
			return '';

		return '<wp:category_description>' . $this->wxr_cdata( $category->description ) . '</wp:category_description>';
	}

	/**
	 * Output a tag_name XML tag from a given tag object
	 *
	 * @since WordPress 2.3.0
	 *
	 * @param object $tag Tag Object
	 * @return string
	 */
	private function wxr_tag_name( $tag ) {
		if ( empty( $tag->name ) )
			return '';

		return '<wp:tag_name>' . $this->wxr_cdata( $tag->name ) . '</wp:tag_name>';
	}

	/**
	 * Output a tag_description XML tag from a given tag object
	 *
	 * @since WordPress 2.3.0
	 *
	 * @param object $tag Tag Object
	 * @return string
	 */
	private function wxr_tag_description( $tag ) {
		if ( empty( $tag->description ) )
			return '';

		return '<wp:tag_description>' . $this->wxr_cdata( $tag->description ) . '</wp:tag_description>';
	}

	/**
	 * Output a term_name XML tag from a given term object
	 *
	 * @since WordPress 2.9.0
	 *
	 * @param object $term Term Object
	 * @return string
	 */
	private function wxr_term_name( $term ) {
		if ( empty( $term->name ) )
			return '';

		return '<wp:term_name>' . $this->wxr_cdata( $term->name ) . '</wp:term_name>';
	}

	/**
	 * Output a term_description XML tag from a given term object
	 *
	 * @since WordPress 2.9.0
	 *
	 * @param object $term Term Object
	 * @return string
	 */
	private function wxr_term_description( $term ) {
		if ( empty( $term->description ) )
			return '';

		return '<wp:term_description>' . $this->wxr_cdata( $term->description ) . '</wp:term_description>';
	}

	/**
	 * Output list of authors with posts
	 *
	 * @since WordPress 3.1.0
	 *
	 * @return string
	 */
	private function wxr_authors_list( ) {
		global $wpdb;

		$authors = array();
		$results = $wpdb->get_results( "SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_status != 'auto-draft'" );
		foreach ( (array) $results as $result )
			$authors[] = get_userdata( $result->post_author );

		$authors = array_filter( $authors );

		$wxr_authors = '';

		foreach ( $authors as $author ) {
			$wxr_authors .= "\t<wp:author>";
			$wxr_authors .= '<wp:author_id>' . $author->ID . '</wp:author_id>';
			$wxr_authors .= '<wp:author_login>' . $author->user_login . '</wp:author_login>';
			$wxr_authors .= '<wp:author_email>' . $author->user_email . '</wp:author_email>';
			$wxr_authors .= '<wp:author_display_name>' . $this->wxr_cdata( $author->display_name ) . '</wp:author_display_name>';
			$wxr_authors .= '<wp:author_first_name>' . $this->wxr_cdata( $author->user_firstname ) . '</wp:author_first_name>';
			$wxr_authors .= '<wp:author_last_name>' . $this->wxr_cdata( $author->user_lastname ) . '</wp:author_last_name>';
			$wxr_authors .= "</wp:author>\n";
		}

		return $wxr_authors;
	}

	/**
	 * Ouput all navigation menu terms
	 *
	 * @since WordPress 3.1.0
	 */
	private function wxr_nav_menu_terms() {
		$nav_menus = wp_get_nav_menus();
		if ( empty( $nav_menus ) || ! is_array( $nav_menus ) )
			return '';

		$wxr_nav_meuns = '';

		foreach ( $nav_menus as $menu ) {
			$wxr_nav_meuns .= "\t<wp:term><wp:term_id>{$menu->term_id}</wp:term_id><wp:term_taxonomy>nav_menu</wp:term_taxonomy><wp:term_slug>{$menu->slug}</wp:term_slug>";
			$wxr_nav_meuns .= $this->wxr_term_name( $menu );
			$wxr_nav_meuns .= "</wp:term>\n";
		}

		return $wxr_nav_meuns;
	}

	/**
	 * Output list of taxonomy terms, in XML tag format, associated with a post
	 *
	 * @since WordPress 2.3.0
	 */
	private function wxr_post_taxonomy() {
		$post = get_post();

		$taxonomies = get_object_taxonomies( $post->post_type );
		if ( empty( $taxonomies ) )
			return '';
		$terms = wp_get_object_terms( $post->ID, $taxonomies );

		$wxr_post_tags = '';

		foreach ( (array) $terms as $term ) {
			$wxr_post_tags .= "\t\t<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\">" . $this->wxr_cdata( $term->name ) . "</category>\n";
		}

		return $wxr_post_tags;
	}

	public function wxr_filter_postmeta( $return_me, $meta_key ) {
		if ( '_edit_lock' == $meta_key )
			$return_me = true;
		return $return_me;
	}



}
