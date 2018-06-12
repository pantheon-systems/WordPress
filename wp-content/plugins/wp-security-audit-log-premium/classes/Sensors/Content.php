<?php
/**
 * Sensor: Content
 *
 * Content sensor class file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WordPress contents (posts, pages and custom posts).
 *
 * 2000 User created a new blog post and saved it as draft
 * 2001 User published a blog post
 * 2002 User modified a published blog post
 * 2003 User modified a draft blog post
 * 2008 User permanently deleted a blog post from the trash
 * 2012 User moved a blog post to the trash
 * 2014 User restored a blog post from trash
 * 2016 User changed blog post category
 * 2017 User changed blog post URL
 * 2019 User changed blog post author
 * 2021 User changed blog post status
 * 2023 User created new category
 * 2024 User deleted category
 * 2025 User changed the visibility of a blog post
 * 2027 User changed the date of a blog post
 * 2049 User set a post as sticky
 * 2050 User removed post from sticky
 * 2052 User changed generic tables
 * 2065 User modified content for a published post
 * 2068 User modified content for a draft post
 * 2072 User modified content of a post
 * 2073 User submitted a post for review
 * 2074 User scheduled a post
 * 2086 User changed title of a post
 * 2100 User opened a post in the editor
 * 2101 User viewed a post
 * 2111 User disabled Comments/Trackbacks and Pingbacks on a published post
 * 2112 User enabled Comments/Trackbacks and Pingbacks on a published post
 * 2113 User disabled Comments/Trackbacks and Pingbacks on a draft post
 * 2114 User enabled Comments/Trackbacks and Pingbacks on a draft post
 * 2004 User created a new WordPress page and saved it as draft
 * 2005 User published a WordPress page
 * 2006 User modified a published WordPress page
 * 2007 User modified a draft WordPress page
 * 2009 User permanently deleted a page from the trash
 * 2013 User moved WordPress page to the trash
 * 2015 User restored a WordPress page from trash
 * 2018 User changed page URL
 * 2020 User changed page author
 * 2022 User changed page status
 * 2026 User changed the visibility of a page post
 * 2028 User changed the date of a page post
 * 2047 User changed the parent of a page
 * 2048 User changed the template of a page
 * 2066 User modified content for a published page
 * 2069 User modified content for a draft page
 * 2075 User scheduled a page
 * 2087 User changed title of a page
 * 2102 User opened a page in the editor
 * 2103 User viewed a page
 * 2115 User disabled Comments/Trackbacks and Pingbacks on a published page
 * 2116 User enabled Comments/Trackbacks and Pingbacks on a published page
 * 2117 User disabled Comments/Trackbacks and Pingbacks on a draft page
 * 2118 User enabled Comments/Trackbacks and Pingbacks on a draft page
 * 2029 User created a new post with custom post type and saved it as draft
 * 2030 User published a post with custom post type
 * 2031 User modified a post with custom post type
 * 2032 User modified a draft post with custom post type
 * 2033 User permanently deleted post with custom post type
 * 2034 User moved post with custom post type to trash
 * 2035 User restored post with custom post type from trash
 * 2036 User changed the category of a post with custom post type
 * 2037 User changed the URL of a post with custom post type
 * 2038 User changed the author or post with custom post type
 * 2039 User changed the status of post with custom post type
 * 2040 User changed the visibility of a post with custom post type
 * 2041 User changed the date of post with custom post type
 * 2067 User modified content for a published custom post type
 * 2070 User modified content for a draft custom post type
 * 2076 User scheduled a custom post type
 * 2088 User changed title of a custom post type
 * 2104 User opened a custom post type in the editor
 * 2105 User viewed a custom post type
 * 2119 User added blog post tag
 * 2120 User removed blog post tag
 * 2121 User created new tag
 * 2122 User deleted tag
 * 2123 User renamed tag
 * 2124 User changed tag slug
 * 2125 User changed tag description
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_Content extends WSAL_AbstractSensor {

	/**
	 * Old post.
	 *
	 * @var stdClass
	 */
	protected $_old_post = null;

	/**
	 * Old permalink.
	 *
	 * @var string
	 */
	protected $_old_link = null;

	/**
	 * Old categories.
	 *
	 * @var array
	 */
	protected $_old_cats = null;

	/**
	 * Old tags.
	 *
	 * @var array
	 */
	protected $_old_tags = null;

	/**
	 * Old path to file.
	 *
	 * @var string
	 */
	protected $_old_tmpl = null;

	/**
	 * Old post is marked as sticky.
	 *
	 * @var boolean
	 */
	protected $_old_stky = null;

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {
		if ( current_user_can( 'edit_posts' ) ) {
			add_action( 'admin_init', array( $this, 'EventWordPressInit' ) );
		}
		add_action( 'transition_post_status', array( $this, 'EventPostChanged' ), 10, 3 );
		add_action( 'delete_post', array( $this, 'EventPostDeleted' ), 10, 1 );
		add_action( 'wp_trash_post', array( $this, 'EventPostTrashed' ), 10, 1 );
		add_action( 'untrash_post', array( $this, 'EventPostUntrashed' ) );
		add_action( 'edit_category', array( $this, 'EventChangedCategoryParent' ) );
		add_action( 'save_post', array( $this, 'SetRevisionLink' ), 10, 3 );
		add_action( 'publish_future_post', array( $this, 'EventPublishFuture' ), 10, 1 );

		add_action( 'create_category', array( $this, 'EventCategoryCreation' ), 10, 1 );
		add_action( 'create_post_tag', array( $this, 'EventTagCreation' ), 10, 1 );

		add_action( 'wp_head', array( $this, 'ViewingPost' ), 10 );
		add_filter( 'post_edit_form_tag', array( $this, 'EditingPost' ), 10, 1 );

		add_filter( 'wp_update_term_data', array( $this, 'event_terms_rename' ), 10, 4 );
	}

	/**
	 * Method: Triggered when terms are renamed.
	 *
	 * @param array  $data     Term data to be updated.
	 * @param int    $term_id  Term ID.
	 * @param string $taxonomy Taxonomy slug.
	 * @param array  $args     Arguments passed to wp_update_term().
	 * @since 2.6.9
	 */
	public function event_terms_rename( $data, $term_id, $taxonomy, $args ) {
		// Check if the taxonomy is term.
		if ( 'post_tag' !== $taxonomy ) {
			return $data;
		}

		// Get data.
		$new_name = ( isset( $data['name'] ) ) ? $data['name'] : false;
		$new_slug = ( isset( $data['slug'] ) ) ? $data['slug'] : false;
		$new_desc = ( isset( $args['description'] ) ) ? $args['description'] : false;

		// Get old data.
		$term = get_term( $term_id, $taxonomy );
		$old_name = $term->name;
		$old_slug = $term->slug;
		$old_desc = $term->description;
		$term_link = $this->get_tag_link( $term_id );

		// Update if both names are not same.
		if ( $old_name !== $new_name ) {
			$this->plugin->alerts->Trigger(
				2123, array(
					'old_name' => $old_name,
					'new_name' => $new_name,
					'TagLink' => $term_link,
				)
			);
		}

		// Update if both slugs are not same.
		if ( $old_slug !== $new_slug ) {
			$this->plugin->alerts->Trigger(
				2124, array(
					'tag' => $new_name,
					'old_slug' => $old_slug,
					'new_slug' => $new_slug,
					'TagLink' => $term_link,
				)
			);
		}

		// Update if both descriptions are not same.
		if ( $old_desc !== $new_desc ) {
			$this->plugin->alerts->Trigger(
				2125, array(
					'tag' => $new_name,
					'TagLink' => $term_link,
					'old_desc' => $old_desc,
					'new_desc' => $new_desc,
					'ReportText' => $old_desc . '|' . $new_desc,
				)
			);
		}
		return $data;

	}

	/**
	 * Gets the alert code based on the type of post.
	 *
	 * @param stdClass $post - The post.
	 * @param integer  $type_post - Alert code type post.
	 * @param integer  $type_page - Alert code type page.
	 * @param integer  $type_custom - Alert code type custom.
	 * @return integer - Alert code.
	 */
	protected function GetEventTypeForPostType( $post, $type_post, $type_page, $type_custom ) {
		switch ( $post->post_type ) {
			case 'page':
				return $type_page;
			case 'post':
				return $type_post;
			default:
				return $type_custom;
		}
	}

	/**
	 * Triggered when a user accesses the admin area.
	 */
	public function EventWordPressInit() {
		// Load old data, if applicable.
		$this->RetrieveOldData();

		// Check for category changes.
		$this->CheckCategoryDeletion();

		// Check for tag changes.
		$this->check_tag_deletion();
	}

	/**
	 * Retrieve Old data.
	 *
	 * @global mixed $_POST - Post data.
	 */
	protected function RetrieveOldData() {
		// Set filter input args.
		$filter_input_args = array(
			'post_ID' => FILTER_VALIDATE_INT,
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'action' => FILTER_SANITIZE_STRING,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		if ( isset( $post_array['_wpnonce'] )
			&& isset( $post_array['post_ID'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			if ( isset( $post_array ) && isset( $post_array['post_ID'] )
				&& ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				&& ! ( isset( $post_array['action'] ) && 'autosave' == $post_array['action'] )
			) {
				$post_id = intval( $post_array['post_ID'] );
				$this->_old_post = get_post( $post_id );
				$this->_old_link = get_permalink( $post_id );
				$this->_old_tmpl = $this->GetPostTemplate( $this->_old_post );
				$this->_old_cats = $this->GetPostCategories( $this->_old_post );
				$this->_old_tags = $this->get_post_tags( $this->_old_post );
				$this->_old_stky = in_array( $post_id, get_option( 'sticky_posts' ) );
			}
		} elseif ( isset( $post_array['post_ID'] ) && current_user_can( 'edit_post', $post_array['post_ID'] ) ) {
			$post_id = intval( $post_array['post_ID'] );
			$this->_old_post = get_post( $post_id );
			$this->_old_link = get_permalink( $post_id );
			$this->_old_tmpl = $this->GetPostTemplate( $this->_old_post );
			$this->_old_cats = $this->GetPostCategories( $this->_old_post );
			$this->_old_tags = $this->get_post_tags( $this->_old_post );
			$this->_old_stky = in_array( $post_id, get_option( 'sticky_posts' ) );
		}
	}

	/**
	 * Get the template path.
	 *
	 * @param stdClass $post - The post.
	 * @return string - Full path to file.
	 */
	protected function GetPostTemplate( $post ) {
		$id = $post->ID;
		$template = get_page_template_slug( $id );
		$pagename = $post->post_name;

		$templates = array();
		if ( $template && 0 === validate_file( $template ) ) {
			$templates[] = $template;
		}
		if ( $pagename ) {
			$templates[] = "page-$pagename.php";
		}
		if ( $id ) {
			$templates[] = "page-$id.php";
		}
		$templates[] = 'page.php';

		return get_query_template( 'page', $templates );
	}

	/**
	 * Get post categories (array of category names).
	 *
	 * @param stdClass $post - The post.
	 * @return array - List of categories.
	 */
	protected function GetPostCategories( $post ) {
		return wp_get_post_categories(
			$post->ID, array(
				'fields' => 'names',
			)
		);
	}

	/**
	 * Get post tags (array of tag names).
	 *
	 * @param stdClass $post - The post.
	 * @return array - List of tags.
	 */
	protected function get_post_tags( $post ) {
		return wp_get_post_tags(
			$post->ID, array(
				'fields' => 'names',
			)
		);
	}

	/**
	 * Check all the post changes.
	 *
	 * @param string   $new_status - New status.
	 * @param string   $old_status - Old status.
	 * @param stdClass $post - The post.
	 */
	public function EventPostChanged( $new_status, $old_status, $post ) {
		// Ignorable states.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( empty( $post->post_type ) ) {
			return;
		}
		if ( 'revision' == $post->post_type ) {
			return;
		}

		// Set filter input args.
		$filter_input_args = array(
			'post_ID' => FILTER_VALIDATE_INT,
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'original_post_status' => FILTER_SANITIZE_STRING,
			'sticky' => FILTER_SANITIZE_STRING,
			'action' => FILTER_SANITIZE_STRING,
			'_inline_edit' => FILTER_SANITIZE_STRING,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		// Verify nonce.
		if ( isset( $post_array['_wpnonce'] )
			&& isset( $post_array['post_ID'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			// Edit Post Screen.
			$original = isset( $post_array['original_post_status'] ) ? $post_array['original_post_status'] : '';
			$this->trigger_post_change_alerts( $old_status, $new_status, $post, $original, isset( $post_array['sticky'] ) );
		} elseif ( isset( $post_array['_inline_edit'] )
			&& 'inline-save' === $post_array['action']
			&& wp_verify_nonce( $post_array['_inline_edit'], 'inlineeditnonce' ) ) {
			// Quick Post Edit.
			$original = isset( $post_array['original_post_status'] ) ? $post_array['original_post_status'] : '';
			$this->trigger_post_change_alerts( $old_status, $new_status, $post, $original, isset( $post_array['sticky'] ) );
		}
	}

	/**
	 * Method: Trigger Post Change Alerts.
	 *
	 * @param string   $old_status - Old status.
	 * @param string   $new_status - New status.
	 * @param stdClass $post - The post.
	 * @param string   $original - Original Post Status.
	 * @param string   $sticky - Sticky post.
	 * @since 1.0.0
	 */
	public function trigger_post_change_alerts( $old_status, $new_status, $post, $original, $sticky ) {
		WSAL_Sensors_Request::SetVars(
			array(
				'$new_status' => $new_status,
				'$old_status' => $old_status,
				'$original' => $original,
			)
		);
		// Run checks.
		if ( $this->_old_post ) {
			if ( $this->CheckOtherSensors( $this->_old_post ) ) {
				return;
			}
			if ( 'auto-draft' == $old_status || 'auto-draft' == $original ) {
				// Handle create post events.
				$this->CheckPostCreation( $this->_old_post, $post );
			} else {
				// Handle update post events.
				$changes = 0;
				$changes = $this->CheckAuthorChange( $this->_old_post, $post )
					+ $this->CheckStatusChange( $this->_old_post, $post )
					+ $this->CheckParentChange( $this->_old_post, $post )
					+ $this->CheckStickyChange( $this->_old_stky, $sticky, $post )
					+ $this->CheckVisibilityChange( $this->_old_post, $post, $old_status, $new_status )
					+ $this->CheckTemplateChange( $this->_old_tmpl, $this->GetPostTemplate( $post ), $post )
					+ $this->CheckCategoriesChange( $this->_old_cats, $this->GetPostCategories( $post ), $post )
					+ $this->check_tags_change( $this->_old_tags, $this->get_post_tags( $post ), $post )
					+ $this->CheckDateChange( $this->_old_post, $post )
					+ $this->CheckPermalinkChange( $this->_old_link, get_permalink( $post->ID ), $post )
					+ $this->CheckCommentsPings( $this->_old_post, $post );

				$this->CheckModificationChange( $post->ID, $this->_old_post, $post, $changes );
			}
		}
	}

	/**
	 * Check post creation.
	 *
	 * @global array $_POST
	 * @param stdClass $old_post - Old post.
	 * @param stdClass $new_post - New post.
	 */
	protected function CheckPostCreation( $old_post, $new_post ) {
		// Set filter input args.
		$filter_input_args = array(
			'action' => FILTER_SANITIZE_STRING,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		/**
		 * Nonce is already verified at this point.
		 *
		 * @see $this->EventPostChanged();
		 */
		$wp_actions = array( 'editpost', 'heartbeat' );
		if ( isset( $post_array['action'] ) && in_array( $post_array['action'], $wp_actions ) ) {
			if ( ! in_array( $new_post->post_type, array( 'attachment', 'revision', 'nav_menu_item' ) ) ) {
				$event = 0;
				$is_scheduled = false;
				switch ( $new_post->post_status ) {
					case 'publish':
						$event = 2001;
						break;
					case 'draft':
						$event = 2000;
						break;
					case 'future':
						$event = 2074;
						$is_scheduled = true;
						break;
					case 'pending':
						$event = 2073;
						break;
				}
				if ( $event ) {
					$editor_link = $this->GetEditorLink( $new_post );
					if ( $is_scheduled ) {
						$this->plugin->alerts->Trigger(
							$event, array(
								'PostID' => $new_post->ID,
								'PostType' => $new_post->post_type,
								'PostTitle' => $new_post->post_title,
								'PostStatus' => $new_post->post_status,
								'PostDate' => $new_post->post_date,
								'PublishingDate' => $new_post->post_date,
								'PostUrl' => get_permalink( $new_post->ID ),
								$editor_link['name'] => $editor_link['value'],
							)
						);
					} else {
						$this->plugin->alerts->Trigger(
							$event, array(
								'PostID' => $new_post->ID,
								'PostType' => $new_post->post_type,
								'PostTitle' => $new_post->post_title,
								'PostStatus' => $new_post->post_status,
								'PostDate' => $new_post->post_date,
								'PostUrl' => get_permalink( $new_post->ID ),
								$editor_link['name'] => $editor_link['value'],
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Post future publishing.
	 *
	 * @param integer $post_id - Post ID.
	 */
	public function EventPublishFuture( $post_id ) {
		$post = get_post( $post_id );
		$editor_link = $this->GetEditorLink( $post );
		$this->plugin->alerts->Trigger(
			2001, array(
				'PostID' => $post->ID,
				'PostType' => $post->post_type,
				'PostTitle' => $post->post_title,
				'PostStatus' => $post->post_status,
				'PostDate' => $post->post_date,
				'PostUrl' => get_permalink( $post->ID ),
				$editor_link['name'] => $editor_link['value'],
			)
		);
	}

	/**
	 * Post permanently deleted.
	 *
	 * @param integer $post_id - Post ID.
	 */
	public function EventPostDeleted( $post_id ) {
		// Set filter input args.
		$filter_input_args = array(
			'action' => FILTER_SANITIZE_STRING,
			'_wpnonce' => FILTER_SANITIZE_STRING,
		);

		// Filter $_GET array for security.
		$get_array = filter_input_array( INPUT_GET, $filter_input_args );

		// Exclude CPTs from external plugins.
		$post = get_post( $post_id );
		if ( $this->CheckOtherSensors( $post ) ) {
			return;
		}

		// Verify nonce.
		if ( isset( $get_array['_wpnonce'] ) && wp_verify_nonce( $get_array['_wpnonce'], 'delete-post_' . $post_id ) ) {
			$wp_actions = array( 'delete' );
			if ( isset( $get_array['action'] ) && in_array( $get_array['action'], $wp_actions ) ) {
				if ( ! in_array( $post->post_type, array( 'attachment', 'revision', 'nav_menu_item' ) ) ) { // Ignore attachments, revisions and menu items.
					$event = 2008;
					// Check WordPress backend operations.
					if ( $this->CheckAutoDraft( $event, $post->post_title ) ) {
						return;
					}
					$editor_link = $this->GetEditorLink( $post );
					$this->plugin->alerts->Trigger(
						$event, array(
							'PostID' => $post->ID,
							'PostType' => $post->post_type,
							'PostTitle' => $post->post_title,
							'PostStatus' => $post->post_status,
							'PostDate' => $post->post_date,
							'PostUrl' => get_permalink( $post->ID ),
						)
					);
				}
			}
		}
	}

	/**
	 * Post moved to the trash.
	 *
	 * @param integer $post_id - Post ID.
	 */
	public function EventPostTrashed( $post_id ) {
		$post = get_post( $post_id );
		if ( $this->CheckOtherSensors( $post ) ) {
			return;
		}
		$editor_link = $this->GetEditorLink( $post );
		$this->plugin->alerts->Trigger(
			2012, array(
				'PostID' => $post->ID,
				'PostType' => $post->post_type,
				'PostTitle' => $post->post_title,
				'PostStatus' => $post->post_status,
				'PostDate' => $post->post_date,
				'PostUrl' => get_permalink( $post->ID ),
				$editor_link['name'] => $editor_link['value'],
			)
		);
	}

	/**
	 * Post restored from trash.
	 *
	 * @param integer $post_id - Post ID.
	 */
	public function EventPostUntrashed( $post_id ) {
		$post = get_post( $post_id );
		if ( $this->CheckOtherSensors( $post ) ) {
			return;
		}
		$editor_link = $this->GetEditorLink( $post );
		$this->plugin->alerts->Trigger(
			2014, array(
				'PostID' => $post->ID,
				'PostType' => $post->post_type,
				'PostTitle' => $post->post_title,
				'PostStatus' => $post->post_status,
				'PostDate' => $post->post_date,
				'PostUrl' => get_permalink( $post->ID ),
				$editor_link['name'] => $editor_link['value'],
			)
		);
	}

	/**
	 * Post date changed.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 */
	protected function CheckDateChange( $oldpost, $newpost ) {
		$from = strtotime( $oldpost->post_date );
		$to = strtotime( $newpost->post_date );

		if ( 'draft' == $oldpost->post_status ) {
			return 0;
		}

		if ( $from != $to ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				2027, array(
					'PostID' => $oldpost->ID,
					'PostType' => $oldpost->post_type,
					'PostTitle' => $oldpost->post_title,
					'PostStatus' => $oldpost->post_status,
					'PostDate' => $newpost->post_date,
					'PostUrl' => get_permalink( $oldpost->ID ),
					'OldDate' => $oldpost->post_date,
					'NewDate' => $newpost->post_date,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Categories changed.
	 *
	 * @param array    $old_cats - Old categories.
	 * @param array    $new_cats - New categories.
	 * @param stdClass $post - The post.
	 */
	protected function CheckCategoriesChange( $old_cats, $new_cats, $post ) {
		$old_cats = implode( ', ', $old_cats );
		$new_cats = implode( ', ', $new_cats );
		if ( $old_cats != $new_cats ) {
			$event = $this->GetEventTypeForPostType( $post, 2016, 0, 2016 );
			if ( $event ) {
				$editor_link = $this->GetEditorLink( $post );
				$this->plugin->alerts->Trigger(
					$event, array(
						'PostID' => $post->ID,
						'PostType' => $post->post_type,
						'PostTitle' => $post->post_title,
						'PostStatus' => $post->post_status,
						'PostDate' => $post->post_date,
						'PostUrl' => get_permalink( $post->ID ),
						'OldCategories' => $old_cats ? $old_cats : 'no categories',
						'NewCategories' => $new_cats ? $new_cats : 'no categories',
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
		}
	}

	/**
	 * Tags changed.
	 *
	 * @param array    $old_tags - Old tags.
	 * @param array    $new_tags - New tags.
	 * @param stdClass $post - The post.
	 */
	protected function check_tags_change( $old_tags, $new_tags, $post ) {
		// Check for added tags.
		$added_tags = array_diff( $new_tags, $old_tags );

		// Check for removed tags.
		$removed_tags = array_diff( $old_tags, $new_tags );

		// Convert tags arrays to string.
		$old_tags = implode( ', ', $old_tags );
		$new_tags = implode( ', ', $new_tags );
		$added_tags = implode( ', ', $added_tags );
		$removed_tags = implode( ', ', $removed_tags );

		// Declare event variables.
		$add_event = '';
		$remove_event = '';
		if ( $old_tags !== $new_tags && ! empty( $added_tags ) ) {
			$add_event = 2119;
			$editor_link = $this->GetEditorLink( $post );
			$post_status = ( 'publish' === $post->post_status ) ? 'published' : $post->post_status;
			$this->plugin->alerts->Trigger(
				$add_event, array(
					'PostID' => $post->ID,
					'PostType' => $post->post_type,
					'PostStatus' => $post_status,
					'PostTitle' => $post->post_title,
					'PostDate' => $post->post_date,
					'PostUrl' => get_permalink( $post->ID ),
					'tag' => $added_tags ? $added_tags : 'no tags',
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}

		if ( $old_tags !== $new_tags && ! empty( $removed_tags ) ) {
			$remove_event = 2120;
			$editor_link = $this->GetEditorLink( $post );
			$post_status = ( 'publish' === $post->post_status ) ? 'published' : $post->post_status;
			$this->plugin->alerts->Trigger(
				$remove_event, array(
					'PostID' => $post->ID,
					'PostType' => $post->post_type,
					'PostStatus' => $post_status,
					'PostTitle' => $post->post_title,
					'PostDate' => $post->post_date,
					'PostUrl' => get_permalink( $post->ID ),
					'tag' => $removed_tags ? $removed_tags : 'no tags',
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}

		if ( $add_event || $remove_event ) {
			return 1;
		}
	}

	/**
	 * Author changed.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 */
	protected function CheckAuthorChange( $oldpost, $newpost ) {
		if ( $oldpost->post_author != $newpost->post_author ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$old_author = get_userdata( $oldpost->post_author );
			$old_author = (is_object( $old_author )) ? $old_author->user_login : 'N/A';
			$new_author = get_userdata( $newpost->post_author );
			$new_author = (is_object( $new_author )) ? $new_author->user_login : 'N/A';
			$this->plugin->alerts->Trigger(
				2019, array(
					'PostID' => $oldpost->ID,
					'PostType' => $oldpost->post_type,
					'PostTitle' => $oldpost->post_title,
					'PostStatus' => $oldpost->post_status,
					'PostDate' => $oldpost->post_date,
					'PostUrl' => get_permalink( $oldpost->ID ),
					'OldAuthor' => $old_author,
					'NewAuthor' => $new_author,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
	}

	/**
	 * Status changed.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 */
	protected function CheckStatusChange( $oldpost, $newpost ) {
		// Set filter input args.
		$filter_input_args = array(
			'publish' => FILTER_SANITIZE_STRING,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		/**
		 * Nonce is already verified at this point.
		 *
		 * @see $this->EventPostChanged();
		 */
		if ( $oldpost->post_status != $newpost->post_status ) {
			if ( isset( $post_array['publish'] ) ) {
				// Special case (publishing a post).
				$editor_link = $this->GetEditorLink( $newpost );
				$this->plugin->alerts->Trigger(
					2001, array(
						'PostID' => $newpost->ID,
						'PostType' => $newpost->post_type,
						'PostTitle' => $newpost->post_title,
						'PostStatus' => $newpost->post_status,
						'PostDate' => $newpost->post_date,
						'PostUrl' => get_permalink( $newpost->ID ),
						$editor_link['name'] => $editor_link['value'],
					)
				);
			} else {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					2021, array(
						'PostID' => $oldpost->ID,
						'PostType' => $oldpost->post_type,
						'PostTitle' => $oldpost->post_title,
						'PostStatus' => $newpost->post_status,
						'PostDate' => $oldpost->post_date,
						'PostUrl' => get_permalink( $oldpost->ID ),
						'OldStatus' => $oldpost->post_status,
						'NewStatus' => $newpost->post_status,
						$editor_link['name'] => $editor_link['value'],
					)
				);
			}
			return 1;
		}
	}

	/**
	 * Post parent changed.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 */
	protected function CheckParentChange( $oldpost, $newpost ) {
		if ( $oldpost->post_parent != $newpost->post_parent ) {
			$event = $this->GetEventTypeForPostType( $oldpost, 0, 2047, 0 );
			if ( $event ) {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					$event, array(
						'PostID' => $oldpost->ID,
						'PostType' => $oldpost->post_type,
						'PostTitle' => $oldpost->post_title,
						'PostStatus' => $oldpost->post_status,
						'PostDate' => $oldpost->post_date,
						'OldParent' => $oldpost->post_parent,
						'NewParent' => $newpost->post_parent,
						'OldParentName' => $oldpost->post_parent ? get_the_title( $oldpost->post_parent ) : 'no parent',
						'NewParentName' => $newpost->post_parent ? get_the_title( $newpost->post_parent ) : 'no parent',
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
		}
	}

	/**
	 * Permalink changed.
	 *
	 * @param string   $old_link - Old permalink.
	 * @param string   $new_link - New permalink.
	 * @param stdClass $post - The post.
	 */
	protected function CheckPermalinkChange( $old_link, $new_link, $post ) {
		if ( $old_link !== $new_link ) {
			$editor_link = $this->GetEditorLink( $post );
			$this->plugin->alerts->Trigger(
				2017, array(
					'PostID' => $post->ID,
					'PostType' => $post->post_type,
					'PostTitle' => $post->post_title,
					'PostStatus' => $post->post_status,
					'PostDate' => $post->post_date,
					'OldUrl' => $old_link,
					'NewUrl' => $new_link,
					$editor_link['name'] => $editor_link['value'],
					'ReportText' => '"' . $old_link . '"|"' . $new_link . '"',
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Post visibility changed.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 * @param string   $old_status - Old status.
	 * @param string   $new_status - New status.
	 */
	protected function CheckVisibilityChange( $oldpost, $newpost, $old_status, $new_status ) {
		if ( 'draft' == $old_status || 'draft' == $new_status ) {
			return;
		}

		$old_visibility = '';
		$new_visibility = '';

		if ( $oldpost->post_password ) {
			$old_visibility = __( 'Password Protected', 'wp-security-audit-log' );
		} elseif ( 'publish' == $old_status ) {
			$old_visibility = __( 'Public', 'wp-security-audit-log' );
		} elseif ( 'private' == $old_status ) {
			$old_visibility = __( 'Private', 'wp-security-audit-log' );
		}

		if ( $newpost->post_password ) {
			$new_visibility = __( 'Password Protected', 'wp-security-audit-log' );
		} elseif ( 'publish' == $new_status ) {
			$new_visibility = __( 'Public', 'wp-security-audit-log' );
		} elseif ( 'private' == $new_status ) {
			$new_visibility = __( 'Private', 'wp-security-audit-log' );
		}

		if ( $old_visibility && $new_visibility && ($old_visibility != $new_visibility) ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				2025, array(
					'PostID' => $oldpost->ID,
					'PostType' => $oldpost->post_type,
					'PostTitle' => $oldpost->post_title,
					'PostStatus' => $newpost->post_status,
					'PostDate' => $oldpost->post_date,
					'PostUrl' => get_permalink( $oldpost->ID ),
					'OldVisibility' => $old_visibility,
					'NewVisibility' => $new_visibility,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
	}

	/**
	 * Post template changed.
	 *
	 * @param string   $old_tmpl - Old template path.
	 * @param string   $new_tmpl - New template path.
	 * @param stdClass $post - The post.
	 */
	protected function CheckTemplateChange( $old_tmpl, $new_tmpl, $post ) {
		if ( $old_tmpl != $new_tmpl ) {
			$event = $this->GetEventTypeForPostType( $post, 0, 2048, 0 );
			if ( $event ) {
				$editor_link = $this->GetEditorLink( $post );
				$this->plugin->alerts->Trigger(
					$event, array(
						'PostID' => $post->ID,
						'PostType' => $post->post_type,
						'PostTitle' => $post->post_title,
						'PostStatus' => $post->post_status,
						'PostDate' => $post->post_date,
						'OldTemplate' => ucwords( str_replace( array( '-', '_' ), ' ', basename( $old_tmpl, '.php' ) ) ),
						'NewTemplate' => ucwords( str_replace( array( '-', '_' ), ' ', basename( $new_tmpl, '.php' ) ) ),
						'OldTemplatePath' => $old_tmpl,
						'NewTemplatePath' => $new_tmpl,
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
		}
	}

	/**
	 * Post sets as sticky changes.
	 *
	 * @param string   $old_stky - Old template path.
	 * @param string   $new_stky - New template path.
	 * @param stdClass $post - The post.
	 */
	protected function CheckStickyChange( $old_stky, $new_stky, $post ) {
		if ( $old_stky != $new_stky ) {
			$event = $new_stky ? 2049 : 2050;
			$editor_link = $this->GetEditorLink( $post );
			$this->plugin->alerts->Trigger(
				$event, array(
					'PostID' => $post->ID,
					'PostType' => $post->post_type,
					'PostTitle' => $post->post_title,
					'PostStatus' => $post->post_status,
					'PostDate' => $post->post_date,
					'PostUrl' => get_permalink( $post->ID ),
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
	}

	/**
	 * Post modified content.
	 *
	 * @param integer  $post_id – Post ID.
	 * @param stdClass $oldpost – Old post.
	 * @param stdClass $newpost – New post.
	 * @param int      $modified – Set to 0 if no changes done to the post.
	 */
	public function CheckModificationChange( $post_id, $oldpost, $newpost, $modified ) {
		if ( $this->CheckOtherSensors( $oldpost ) ) {
			return;
		}
		$changes = $this->CheckTitleChange( $oldpost, $newpost );
		if ( ! $changes ) {
			$content_changed = $oldpost->post_content != $newpost->post_content; // TODO what about excerpts?

			if ( $oldpost->post_modified != $newpost->post_modified ) {
				$event = 0;

				// Check if content changed.
				if ( $content_changed ) {
					$event = 2065;
				} elseif ( ! $modified ) {
					$event = 2002;
				}
				if ( $event ) {
					if ( 2002 === $event ) {
						// Get Yoast alerts.
						$yoast_alerts = $this->plugin->alerts->get_alerts_by_sub_category( 'Yoast SEO' );

						// Check all alerts.
						foreach ( $yoast_alerts as $alert_code => $alert ) {
							if ( $this->plugin->alerts->WillOrHasTriggered( $alert_code ) ) {
								return 0; // Return if any Yoast alert has or will trigger.
							}
						}
					}

					$editor_link = $this->GetEditorLink( $oldpost );
					$this->plugin->alerts->Trigger(
						$event, array(
							'PostID' => $post_id,
							'PostType' => $oldpost->post_type,
							'PostTitle' => $oldpost->post_title,
							'PostStatus' => $oldpost->post_status,
							'PostDate' => $oldpost->post_date,
							'PostUrl' => get_permalink( $post_id ),
							$editor_link['name'] => $editor_link['value'],
						)
					);
					return 1;
				}
			}
		}
	}

	/**
	 * New category created.
	 *
	 * @param integer $category_id - Category ID.
	 */
	public function EventCategoryCreation( $category_id ) {
		$category = get_category( $category_id );
		$category_link = $this->getCategoryLink( $category_id );
		$this->plugin->alerts->Trigger(
			2023, array(
				'CategoryName' => $category->name,
				'Slug' => $category->slug,
				'CategoryLink' => $category_link,
			)
		);
	}

	/**
	 * New tag created.
	 *
	 * @param int $tag_id - Tag ID.
	 */
	public function EventTagCreation( $tag_id ) {
		$tag = get_tag( $tag_id );
		$tag_link = $this->get_tag_link( $tag_id );
		$this->plugin->alerts->Trigger(
			2121, array(
				'TagName' => $tag->name,
				'Slug' => $tag->slug,
				'TagLink' => $tag_link,
			)
		);
	}

	/**
	 * Category deleted.
	 *
	 * @global array $_POST - Post data.
	 */
	protected function CheckCategoryDeletion() {
		// Set filter input args.
		$filter_input_args = array(
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'action' => FILTER_SANITIZE_STRING,
			'action2' => FILTER_SANITIZE_STRING,
			'taxonomy' => FILTER_SANITIZE_STRING,
			'delete_tags' => array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags'  => FILTER_REQUIRE_ARRAY,
			),
			'tag_ID' => FILTER_VALIDATE_INT,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		if ( empty( $post_array ) ) {
			return;
		}
		$action = ! empty( $post_array['action'] ) ? $post_array['action']
			: ( ! empty( $post_array['action2'] ) ? $post_array['action2'] : '');
		if ( ! $action ) {
			return;
		}

		$category_ids = array();

		if ( isset( $post_array['taxonomy'] ) ) {
			if ( 'delete' == $action
				&& 'category' == $post_array['taxonomy']
				&& ! empty( $post_array['delete_tags'] )
				&& wp_verify_nonce( $post_array['_wpnonce'], 'bulk-tags' ) ) {
				// Bulk delete.
				foreach ( $post_array['delete_tags'] as $delete_tag ) {
					$category_ids[] = $delete_tag;
				}
			} elseif ( 'delete-tag' == $action
				&& 'category' == $post_array['taxonomy']
				&& ! empty( $post_array['tag_ID'] )
				&& wp_verify_nonce( $post_array['_wpnonce'], 'delete-tag_' . $post_array['tag_ID'] ) ) {
				// Single delete.
				$category_ids[] = $post_array['tag_ID'];
			}
		}

		foreach ( $category_ids as $category_id ) {
			$category = get_category( $category_id );
			$category_link = $this->getCategoryLink( $category_id );
			$this->plugin->alerts->Trigger(
				2024, array(
					'CategoryID' => $category_id,
					'CategoryName' => $category->cat_name,
					'Slug' => $category->slug,
					'CategoryLink' => $category_link,
				)
			);
		}
	}

	/**
	 * Tag deleted.
	 *
	 * @global array $_POST - Post data
	 */
	protected function check_tag_deletion() {
		// Set filter input args.
		$filter_input_args = array(
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'action' => FILTER_SANITIZE_STRING,
			'action2' => FILTER_SANITIZE_STRING,
			'taxonomy' => FILTER_SANITIZE_STRING,
			'delete_tags' => array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags'  => FILTER_REQUIRE_ARRAY,
			),
			'tag_ID' => FILTER_VALIDATE_INT,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		// If post array is empty then return.
		if ( empty( $post_array ) ) {
			return;
		}

		// Check for action.
		$action = ! empty( $post_array['action'] ) ? $post_array['action']
			: ( ! empty( $post_array['action2'] ) ? $post_array['action2'] : '' );
		if ( ! $action ) {
			return;
		}

		$tag_ids = array();

		if ( isset( $post_array['taxonomy'] ) ) {
			if ( 'delete' === $action
				&& 'post_tag' === $post_array['taxonomy']
				&& ! empty( $post_array['delete_tags'] )
				&& wp_verify_nonce( $post_array['_wpnonce'], 'bulk-tags' ) ) {
				// Bulk delete.
				foreach ( $post_array['delete_tags'] as $delete_tag ) {
					$tag_ids[] = $delete_tag;
				}
			} elseif ( 'delete-tag' === $action
				&& 'post_tag' === $post_array['taxonomy']
				&& ! empty( $post_array['tag_ID'] )
				&& wp_verify_nonce( $post_array['_wpnonce'], 'delete-tag_' . $post_array['tag_ID'] ) ) {
				// Single delete.
				$tag_ids[] = $post_array['tag_ID'];
			}
		}

		foreach ( $tag_ids as $tag_id ) {
			$tag = get_tag( $tag_id );
			$this->plugin->alerts->Trigger(
				2122, array(
					'TagID' => $tag_id,
					'TagName' => $tag->name,
					'Slug' => $tag->slug,
				)
			);
		}
	}

	/**
	 * Changed the parent of the category.
	 *
	 * @global array $_POST - Post data.
	 */
	public function EventChangedCategoryParent() {
		// Set filter input args.
		$filter_input_args = array(
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'name' => FILTER_SANITIZE_STRING,
			'parent' => FILTER_SANITIZE_STRING,
			'tag_ID' => FILTER_VALIDATE_INT,
		);

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		if ( empty( $post_array ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}
		if ( isset( $post_array['_wpnonce'] )
			&& isset( $post_array['name'] )
			&& isset( $post_array['tag_ID'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-tag_' . $post_array['tag_ID'] ) ) {
			$category = get_category( $post_array['tag_ID'] );
			$category_link = $this->getCategoryLink( $post_array['tag_ID'] );
			if ( 0 != $category->parent ) {
				$old_parent = get_category( $category->parent );
				$old_parent_name = (empty( $old_parent )) ? 'no parent' : $old_parent->name;
			} else {
				$old_parent_name = 'no parent';
			}
			if ( isset( $post_array['parent'] ) ) {
				$new_parent = get_category( $post_array['parent'] );
				$new_parent_name = (empty( $new_parent )) ? 'no parent' : $new_parent->name;
			}

			if ( $old_parent_name !== $new_parent_name ) {
				$this->plugin->alerts->Trigger(
					2052, array(
						'CategoryName' => $category->name,
						'OldParent' => $old_parent_name,
						'NewParent' => $new_parent_name,
						'CategoryLink' => $category_link,
					)
				);
			}
		}
	}

	/**
	 * Check auto draft and the setting: Hide Plugin in Plugins Page
	 *
	 * @param integer $code - Alert code.
	 * @param string  $title - Title.
	 * @return boolean
	 */
	private function CheckAutoDraft( $code, $title ) {
		if ( 2008 == $code && 'auto-draft' == $title ) {
			// To do: Check setting else return false.
			if ( $this->plugin->settings->IsWPBackend() == 1 ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Builds revision link.
	 *
	 * @param integer $revision_id - Revision ID.
	 * @return string|null - Link.
	 */
	private function getRevisionLink( $revision_id ) {
		if ( ! empty( $revision_id ) ) {
			return admin_url( 'revision.php?revision=' . $revision_id );
		} else {
			return null;
		}
	}

	/**
	 * Builds category link.
	 *
	 * @param integer $category_id - Category ID.
	 * @return string|null - Link.
	 */
	private function getCategoryLink( $category_id ) {
		if ( ! empty( $category_id ) ) {
			return admin_url( 'term.php?taxnomy=category&tag_ID=' . $category_id );
		} else {
			return null;
		}
	}

	/**
	 * Builds tag link.
	 *
	 * @param integer $tag_id - Tag ID.
	 * @return string|null - Link.
	 */
	private function get_tag_link( $tag_id ) {
		if ( ! empty( $tag_id ) ) {
			return admin_url( 'term.php?taxnomy=post_tag&tag_ID=' . $tag_id );
		} else {
			return null;
		}
	}

	/**
	 * Ignore post from BBPress, WooCommerce Plugin
	 * Triggered on the Sensors
	 *
	 * @param stdClass $post - The post.
	 */
	private function CheckOtherSensors( $post ) {
		if ( empty( $post ) || ! isset( $post->post_type ) ) {
			return false;
		}
		switch ( $post->post_type ) {
			case 'forum':
			case 'topic':
			case 'reply':
			case 'product':
				return true;
			default:
				return false;
		}
	}

	/**
	 * Triggered after save post for add revision link.
	 *
	 * @param integer  $post_id - Post ID.
	 * @param stdClass $post - Post.
	 * @param bool     $update - True if update.
	 */
	public function SetRevisionLink( $post_id, $post, $update ) {
		$revisions = wp_get_post_revisions( $post_id );
		if ( ! empty( $revisions ) ) {
			$revision = array_shift( $revisions );

			$obj_occ = new WSAL_Models_Occurrence();
			$occ = $obj_occ->GetByPostID( $post_id );
			$occ = count( $occ ) ? $occ[0] : null;
			if ( ! empty( $occ ) ) {
				$revision_link = $this->getRevisionLink( $revision->ID );
				if ( ! empty( $revision_link ) ) {
					$occ->SetMetaValue( 'RevisionLink', $revision_link );
				}
			}
		}
	}

	/**
	 * Alerts for Viewing of Posts, Pages and Custom Posts.
	 */
	public function ViewingPost() {
		// Retrieve the current post object.
		$post = get_queried_object();
		if ( is_user_logged_in() ) {
			if ( ! is_admin() ) {
				if ( $this->CheckOtherSensors( $post ) ) {
					return $post->post_title;
				}

				// Filter $_SERVER array for security.
				$server_array = filter_input_array( INPUT_SERVER );

				$current_path = isset( $server_array['REQUEST_URI'] ) ? $server_array['REQUEST_URI'] : false;
				if ( ! empty( $server_array['HTTP_REFERER'] )
					&& ! empty( $current_path )
					&& strpos( $server_array['HTTP_REFERER'], $current_path ) !== false ) {
					// Ignore this if we were on the same page so we avoid double audit entries.
					return;
				}
				if ( ! empty( $post->post_title ) ) {
					$editor_link = $this->GetEditorLink( $post );
					$this->plugin->alerts->Trigger(
						2101, array(
							'PostID'    => $post->ID,
							'PostType'  => $post->post_type,
							'PostTitle' => $post->post_title,
							'PostStatus' => $post->post_status,
							'PostDate' => $post->post_date,
							'PostUrl'   => get_permalink( $post->ID ),
							$editor_link['name'] => $editor_link['value'],
						)
					);
				}
			}
		}
	}

	/**
	 * Alerts for Editing of Posts, Pages and Custom Posts.
	 *
	 * @param stdClass $post - Post.
	 */
	public function EditingPost( $post ) {
		if ( is_user_logged_in() ) {
			if ( is_admin() ) {
				if ( $this->CheckOtherSensors( $post ) ) {
					return $post;
				}

				// Filter $_SERVER array for security.
				$server_array = filter_input_array( INPUT_SERVER );

				$current_path = isset( $server_array['SCRIPT_NAME'] ) ? $server_array['SCRIPT_NAME'] . '?post=' . $post->ID : false;
				if ( ! empty( $server_array['HTTP_REFERER'] )
					&& strpos( $server_array['HTTP_REFERER'], $current_path ) !== false ) {
					// Ignore this if we were on the same page so we avoid double audit entries.
					return $post;
				}
				if ( ! empty( $post->post_title ) ) {
					$event = 2100;
					if ( ! $this->WasTriggered( $event ) ) {
						$editor_link = $this->GetEditorLink( $post );
						$this->plugin->alerts->Trigger(
							$event, array(
								'PostID' => $post->ID,
								'PostType' => $post->post_type,
								'PostTitle' => $post->post_title,
								'PostStatus' => $post->post_status,
								'PostDate' => $post->post_date,
								'PostUrl' => get_permalink( $post->ID ),
								$editor_link['name'] => $editor_link['value'],
							)
						);
					}
				}
			}
		}
		return $post;
	}

	/**
	 * Check if the alert was triggered.
	 *
	 * @param integer $alert_id - Alert code.
	 * @return boolean
	 */
	private function WasTriggered( $alert_id ) {
		$query = new WSAL_Models_OccurrenceQuery();
		$query->addOrderBy( 'created_on', true );
		$query->setLimit( 1 );
		$last_occurence = $query->getAdapter()->Execute( $query );
		if ( ! empty( $last_occurence ) ) {
			if ( $last_occurence[0]->alert_id == $alert_id ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Changed title of a post.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 */
	private function CheckTitleChange( $oldpost, $newpost ) {
		if ( $oldpost->post_title != $newpost->post_title ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				2086, array(
					'PostID' => $newpost->ID,
					'PostType' => $newpost->post_type,
					'PostTitle' => $newpost->post_title,
					'PostStatus' => $newpost->post_status,
					'PostDate' => $newpost->post_date,
					'PostUrl' => get_permalink( $newpost->ID ),
					'OldTitle' => $oldpost->post_title,
					'NewTitle' => $newpost->post_title,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Comments/Trackbacks and Pingbacks check.
	 *
	 * @param stdClass $oldpost - Old post.
	 * @param stdClass $newpost - New post.
	 */
	private function CheckCommentsPings( $oldpost, $newpost ) {
		$result = 0;
		$editor_link = $this->GetEditorLink( $newpost );

		// Comments.
		if ( $oldpost->comment_status != $newpost->comment_status ) {
			$type = 'Comments';

			if ( 'open' == $newpost->comment_status ) {
				$event = $this->GetCommentsPingsEvent( $newpost, 'enable' );
			} else {
				$event = $this->GetCommentsPingsEvent( $newpost, 'disable' );
			}

			$this->plugin->alerts->Trigger(
				$event, array(
					'Type' => $type,
					'PostID' => $newpost->ID,
					'PostType' => $newpost->post_type,
					'PostStatus' => $newpost->post_status,
					'PostDate' => $newpost->post_date,
					'PostTitle' => $newpost->post_title,
					'PostStatus' => $newpost->post_status,
					'PostUrl' => get_permalink( $newpost->ID ),
					$editor_link['name'] => $editor_link['value'],
				)
			);
			$result = 1;
		}
		// Trackbacks and Pingbacks.
		if ( $oldpost->ping_status != $newpost->ping_status ) {
			$type = 'Trackbacks and Pingbacks';

			if ( 'open' == $newpost->ping_status ) {
				$event = $this->GetCommentsPingsEvent( $newpost, 'enable' );
			} else {
				$event = $this->GetCommentsPingsEvent( $newpost, 'disable' );
			}

			$this->plugin->alerts->Trigger(
				$event, array(
					'Type' => $type,
					'PostID' => $newpost->ID,
					'PostType' => $newpost->post_type,
					'PostTitle' => $newpost->post_title,
					'PostStatus' => $newpost->post_status,
					'PostDate' => $newpost->post_date,
					'PostUrl' => get_permalink( $newpost->ID ),
					$editor_link['name'] => $editor_link['value'],
				)
			);
			$result = 1;
		}
		return $result;
	}

	/**
	 * Comments/Trackbacks and Pingbacks event code.
	 *
	 * @param stdClass $post - The post.
	 * @param string   $status - The status.
	 */
	private function GetCommentsPingsEvent( $post, $status ) {
		if ( 'disable' == $status ) {
			$event = 2111;
		} else {
			$event = 2112;
		}
		return $event;
	}

	/**
	 * Get editor link.
	 *
	 * @param stdClass $post - The post.
	 * @return array $editor_link - Name and value link.
	 */
	private function GetEditorLink( $post ) {
		$name = 'EditorLinkPost';
		// $name .= ( 'page' == $post->post_type ) ? 'Page' : 'Post' ;
		$value = get_edit_post_link( $post->ID );
		$editor_link = array(
			'name' => $name,
			'value' => $value,
		);
		return $editor_link;
	}
}
