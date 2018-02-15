<?php
/**
 * Sensor: BBPress
 *
 * BBPress sensor class file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Support for BBPress Forum Plugin.
 *
 * 8000 User created new forum
 * 8001 User changed status of a forum
 * 8002 User changed visibility of a forum
 * 8003 User changed the URL of a forum
 * 8004 User changed order of a forum
 * 8005 User moved forum to trash
 * 8006 User permanently deleted forum
 * 8007 User restored forum from trash
 * 8008 User changed the parent of a forum
 * 8011 User changed type of a forum
 * 8014 User created new topic
 * 8015 User changed status of a topic
 * 8016 User changed type of a topic
 * 8017 User changed URL of a topic
 * 8018 User changed the forum of a topic
 * 8019 User moved topic to trash
 * 8020 User permanently deleted topic
 * 8021 User restored topic from trash
 * 8022 User changed visibility of a topic
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_BBPress extends WSAL_AbstractSensor {

	/**
	 * Old permalink
	 *
	 * @var string
	 */
	protected $_OldLink = null;

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {
		if ( current_user_can( 'edit_posts' ) ) {
			add_action( 'admin_init', array( $this, 'EventAdminInit' ) );
		}
		add_action( 'post_updated', array( $this, 'CheckForumChange' ), 10, 3 );
		add_action( 'delete_post', array( $this, 'EventForumDeleted' ), 10, 1 );
		add_action( 'wp_trash_post', array( $this, 'EventForumTrashed' ), 10, 1 );
		add_action( 'untrash_post', array( $this, 'EventForumUntrashed' ) );
	}

	/**
	 * Triggered when a user accesses the admin area.
	 */
	public function EventAdminInit() {
		// Load old data, if applicable.
		$this->RetrieveOldData();
		// Check for Ajax changes.
		$this->TriggerAjaxChange();
	}

	/**
	 * Retrieve Old data.
	 *
	 * @global mixed $_POST post data
	 */
	protected function RetrieveOldData() {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		if ( isset( $post_array ) && isset( $post_array['post_ID'] )
			&& ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			&& ! ( isset( $post_array['action'] ) && 'autosave' === $post_array['action'] )
		) {
			$post_id = intval( $post_array['post_ID'] );
			$this->_OldLink = get_permalink( $post_id );
		}
	}

	/**
	 * Calls event forum changes.
	 *
	 * @param integer  $post_id - Post ID.
	 * @param stdClass $newpost - The new post.
	 * @param stdClass $oldpost - The old post.
	 */
	public function CheckForumChange( $post_id, $newpost, $oldpost ) {
		if ( $this->CheckBBPress( $oldpost ) ) {
			$changes = 0 + $this->EventForumCreation( $oldpost, $newpost );
			// Change Visibility.
			if ( ! $changes ) {
				$changes = $this->EventForumChangedVisibility( $oldpost );
			}
			// Change Type.
			if ( ! $changes ) {
				$changes = $this->EventForumChangedType( $oldpost );
			}
			// Change status.
			if ( ! $changes ) {
				$changes = $this->EventForumChangedStatus( $oldpost );
			}
			// Change Order, Parent or URL.
			if ( ! $changes ) {
				$changes = $this->EventForumChanged( $oldpost, $newpost );
			}
		}
	}

	/**
	 * Permanently deleted.
	 *
	 * @param integer $post_id Post ID.
	 */
	public function EventForumDeleted( $post_id ) {
		$post = get_post( $post_id );
		if ( $this->CheckBBPress( $post ) ) {
			switch ( $post->post_type ) {
				case 'forum':
					$this->EventForumByCode( $post, 8006 );
					break;
				case 'topic':
					$this->EventTopicByCode( $post, 8020 );
					break;
			}
		}
	}

	/**
	 * Moved to Trash.
	 *
	 * @param integer $post_id Post ID.
	 */
	public function EventForumTrashed( $post_id ) {
		$post = get_post( $post_id );
		if ( $this->CheckBBPress( $post ) ) {
			switch ( $post->post_type ) {
				case 'forum':
					$this->EventForumByCode( $post, 8005 );
					break;
				case 'topic':
					$this->EventTopicByCode( $post, 8019 );
					break;
			}
		}
	}

	/**
	 * Restored from Trash.
	 *
	 * @param integer $post_id Post ID.
	 */
	public function EventForumUntrashed( $post_id ) {
		$post = get_post( $post_id );
		if ( $this->CheckBBPress( $post ) ) {
			switch ( $post->post_type ) {
				case 'forum':
					$this->EventForumByCode( $post, 8007 );
					break;
				case 'topic':
					$this->EventTopicByCode( $post, 8021 );
					break;
			}
		}
	}

	/**
	 * Check post type.
	 *
	 * @param stdClass $post Post.
	 * @return boolean
	 */
	private function CheckBBPress( $post ) {
		switch ( $post->post_type ) {
			case 'forum':
			case 'topic':
			case 'reply':
				return true;
			default:
				return false;
		}
	}

	/**
	 * Event post creation.
	 *
	 * @param stdClass $old_post - The old post.
	 * @param stdClass $new_post - The new post.
	 * @return boolean
	 */
	private function EventForumCreation( $old_post, $new_post ) {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$original = isset( $post_array['original_post_status'] ) ? $post_array['original_post_status'] : '';
		if ( 'draft' === $old_post->post_status || 'auto-draft' === $original ) {
			$editor_link = $this->GetEditorLink( $new_post );
			if ( 'publish' === $new_post->post_status ) {
				switch ( $old_post->post_type ) {
					case 'forum':
						$this->plugin->alerts->Trigger(
							8000, array(
								'ForumName' => $new_post->post_title,
								'ForumURL' => get_permalink( $new_post->ID ),
								$editor_link['name'] => $editor_link['value'],
							)
						);
						break;
					case 'topic':
						$this->plugin->alerts->Trigger(
							8014, array(
								'TopicName' => $new_post->post_title,
								'TopicURL' => get_permalink( $new_post->ID ),
								$editor_link['name'] => $editor_link['value'],
							)
						);
						break;
				}
				return 1;
			}
		}
		return 0;
	}

	/**
	 * Event post changed visibility.
	 *
	 * @param stdClass $post The post.
	 * @return boolean $result
	 */
	private function EventForumChangedVisibility( $post ) {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$result = 0;
		$editor_link = $this->GetEditorLink( $post );
		switch ( $post->post_type ) {
			case 'forum':
				$old_visibility = ! empty( $post_array['visibility'] ) ? $post_array['visibility'] : '';
				$new_visibility = ! empty( $post_array['bbp_forum_visibility'] ) ? $post_array['bbp_forum_visibility'] : '';
				$new_visibility = ( 'publish' === $new_visibility ) ? 'public' : $new_visibility;

				if ( ! empty( $new_visibility ) && 'auto-draft' != $old_visibility && $old_visibility != $new_visibility ) {
					$this->plugin->alerts->Trigger(
						8002, array(
							'ForumName' => $post->post_title,
							'OldVisibility' => $old_visibility,
							'NewVisibility' => $new_visibility,
							$editor_link['name'] => $editor_link['value'],
						)
					);
					$result = 1;
				}
				break;
			case 'topic':
				$old_visibility = ! empty( $post_array['hidden_post_visibility'] ) ? $post_array['hidden_post_visibility'] : '';
				$new_visibility = ! empty( $post_array['visibility'] ) ? $post_array['visibility'] : '';
				$new_visibility = ( 'password' === $new_visibility ) ? 'password protected' : $new_visibility;

				if ( ! empty( $new_visibility ) && 'auto-draft' != $old_visibility && $old_visibility != $new_visibility ) {
					$this->plugin->alerts->Trigger(
						8022, array(
							'TopicName' => $post->post_title,
							'OldVisibility' => $old_visibility,
							'NewVisibility' => $new_visibility,
							$editor_link['name'] => $editor_link['value'],
						)
					);
					$result = 1;
				}
				break;
		}
		return $result;
	}

	/**
	 * Event post changed type.
	 *
	 * @param stdClass $post The post.
	 * @return boolean $result
	 */
	private function EventForumChangedType( $post ) {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$result = 0;
		$editor_link = $this->GetEditorLink( $post );
		switch ( $post->post_type ) {
			case 'forum':
				$bbp_forum_type = get_post_meta( $post->ID, '_bbp_forum_type', true );
				$old_type = ! empty( $bbp_forum_type ) ? $bbp_forum_type : 'forum';
				$new_type = ! empty( $post_array['bbp_forum_type'] ) ? $post_array['bbp_forum_type'] : '';
				if ( ! empty( $new_type ) && $old_type != $new_type ) {
					$this->plugin->alerts->Trigger(
						8011, array(
							'ForumName' => $post->post_title,
							'OldType' => $old_type,
							'NewType' => $new_type,
							$editor_link['name'] => $editor_link['value'],
						)
					);
					$result = 1;
				}
				break;
			case 'topic':
				if ( ! empty( $post_array['parent_id'] ) ) {
					$post_id = $post_array['parent_id'];
				} else {
					$post_id = $post->ID;
				}
				$bbp_sticky_topics = maybe_unserialize( get_post_meta( $post_id, '_bbp_sticky_topics', true ) );
				$fn = $this->IsMultisite() ? 'get_site_option' : 'get_option';
				$bbp_super_sticky_topics = maybe_unserialize( $fn( '_bbp_super_sticky_topics' ) );
				if ( ! empty( $bbp_sticky_topics ) && in_array( $post->ID, $bbp_sticky_topics ) ) {
					$old_type = 'sticky';
				} elseif ( ! empty( $bbp_super_sticky_topics ) && in_array( $post->ID, $bbp_super_sticky_topics ) ) {
					$old_type = 'super';
				} else {
					$old_type = 'unstick';
				}
				$new_type = ! empty( $post_array['bbp_stick_topic'] ) ? $post_array['bbp_stick_topic'] : '';
				if ( ! empty( $new_type ) && $old_type != $new_type ) {
					$this->plugin->alerts->Trigger(
						8016, array(
							'TopicName' => $post->post_title,
							'OldType' => ( 'unstick' == $old_type ) ? 'normal' : (( 'super' == $old_type ) ? 'super sticky' : $old_type),
							'NewType' => ( 'unstick' == $new_type ) ? 'normal' : (( 'super' == $new_type ) ? 'super sticky' : $new_type),
							$editor_link['name'] => $editor_link['value'],
						)
					);
					$result = 1;
				}
				break;
		}
		return $result;
	}

	/**
	 * Event post changed status.
	 *
	 * @param stdClass $post The post.
	 * @return boolean $result
	 */
	private function EventForumChangedStatus( $post ) {
		// Filter $_POST and $_GET array for security.
		$post_array = filter_input_array( INPUT_POST );
		$get_array = filter_input_array( INPUT_GET );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$result = 0;
		$editor_link = $this->GetEditorLink( $post );
		switch ( $post->post_type ) {
			case 'forum':
				$bbp_status = get_post_meta( $post->ID, '_bbp_status', true );
				$old_status = ! empty( $bbp_status ) ? $bbp_status : 'open';
				$new_status = ! empty( $post_array['bbp_forum_status'] ) ? $post_array['bbp_forum_status'] : '';
				if ( ! empty( $new_status ) && $old_status !== $new_status ) {
					$this->plugin->alerts->Trigger(
						8001, array(
							'ForumName' => $post->post_title,
							'OldStatus' => $old_status,
							'NewStatus' => $new_status,
							$editor_link['name'] => $editor_link['value'],
						)
					);
					$result = 1;
				}
				break;
			case 'topic':
				$old_status = ! empty( $post_array['original_post_status'] ) ? $post_array['original_post_status'] : '';
				$new_status = ! empty( $post_array['post_status'] ) ? $post_array['post_status'] : '';
				// In case of Ajax request Spam/Not spam.
				if ( isset( $get_array['action'] ) && 'bbp_toggle_topic_spam' === $get_array['action'] ) {
					$old_status = $post->post_status;
					$new_status = 'spam';
					if ( isset( $get_array['post_status'] ) && 'spam' === $get_array['post_status'] ) {
						$new_status = 'publish';
					}
				}
				// In case of Ajax request Close/Open.
				if ( isset( $get_array['action'] ) && 'bbp_toggle_topic_close' === $get_array['action'] ) {
					$old_status = $post->post_status;
					$new_status = 'closed';
					if ( isset( $get_array['post_status'] ) && 'closed' === $get_array['post_status'] ) {
						$new_status = 'publish';
					}
				}
				if ( ! empty( $new_status ) && $old_status !== $new_status ) {
					$this->plugin->alerts->Trigger(
						8015, array(
							'TopicName' => $post->post_title,
							'OldStatus' => ( 'publish' === $old_status ) ? 'open' : $old_status,
							'NewStatus' => ( 'publish' === $new_status ) ? 'open' : $new_status,
							$editor_link['name'] => $editor_link['value'],
						)
					);
					$result = 1;
				}
				break;
		}
		return $result;
	}

	/**
	 * Event post changed (order, parent, URL).
	 *
	 * @param stdClass $old_post - The old post.
	 * @param stdClass $new_post - The new post.
	 * @return boolean $result
	 */
	private function EventForumChanged( $old_post, $new_post ) {
		$editor_link = $this->GetEditorLink( $new_post );
		// Changed Order.
		if ( $old_post->menu_order != $new_post->menu_order ) {
			$this->plugin->alerts->Trigger(
				8004, array(
					'ForumName' => $new_post->post_title,
					'OldOrder' => $old_post->menu_order,
					'NewOrder' => $new_post->menu_order,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		// Changed Parent.
		if ( $old_post->post_parent != $new_post->post_parent ) {
			switch ( $old_post->post_type ) {
				case 'forum':
					$this->plugin->alerts->Trigger(
						8008, array(
							'ForumName' => $new_post->post_title,
							'OldParent' => $old_post->post_parent ? get_the_title( $old_post->post_parent ) : 'no parent',
							'NewParent' => $new_post->post_parent ? get_the_title( $new_post->post_parent ) : 'no parent',
							$editor_link['name'] => $editor_link['value'],
						)
					);
					break;
				case 'topic':
					$this->plugin->alerts->Trigger(
						8018, array(
							'TopicName' => $new_post->post_title,
							'OldForum' => $old_post->post_parent ? get_the_title( $old_post->post_parent ) : 'no parent',
							'NewForum' => $new_post->post_parent ? get_the_title( $new_post->post_parent ) : 'no parent',
							$editor_link['name'] => $editor_link['value'],
						)
					);
					break;
			}
			return 1;
		}
		// Changed URL.
		$old_link = $this->_OldLink;
		$new_link = get_permalink( $new_post->ID );
		if ( ! empty( $old_link ) && $old_link != $new_link ) {
			switch ( $old_post->post_type ) {
				case 'forum':
					$this->plugin->alerts->Trigger(
						8003, array(
							'ForumName' => $new_post->post_title,
							'OldUrl' => $old_link,
							'NewUrl' => $new_link,
							$editor_link['name'] => $editor_link['value'],
						)
					);
					break;
				case 'topic':
					$this->plugin->alerts->Trigger(
						8017, array(
							'TopicName' => $new_post->post_title,
							'OldUrl' => $old_link,
							'NewUrl' => $new_link,
							$editor_link['name'] => $editor_link['value'],
						)
					);
					break;
			}
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger Event (Forum).
	 *
	 * @param stdClass $post - The post.
	 * @param integer  $event - Event code.
	 */
	private function EventForumByCode( $post, $event ) {
		$editor_link = $this->GetEditorLink( $post );
		$this->plugin->alerts->Trigger(
			$event, array(
				'ForumID' => $post->ID,
				'ForumName' => $post->post_title,
				$editor_link['name'] => $editor_link['value'],
			)
		);
	}

	/**
	 * Trigger Event (Topic).
	 *
	 * @param stdClass $post - The post.
	 * @param integer  $event - Event code.
	 */
	private function EventTopicByCode( $post, $event ) {
		$editor_link = $this->GetEditorLink( $post );
		$this->plugin->alerts->Trigger(
			$event, array(
				'TopicID' => $post->ID,
				'TopicName' => $post->post_title,
				$editor_link['name'] => $editor_link['value'],
			)
		);
	}

	/**
	 * Trigger of ajax events generated in the Topic Grid
	 *
	 * @global mixed $_GET Get data
	 */
	public function TriggerAjaxChange() {
		// Filter $_GET array for security.
		$get_array = filter_input_array( INPUT_GET );

		if ( ! empty( $get_array['post_type'] ) && ! empty( $get_array['topic_id'] ) ) {
			if ( 'topic' == $get_array['post_type'] ) {
				$post = get_post( $get_array['topic_id'] );

				// Topic type.
				if ( isset( $get_array['action'] ) && 'bbp_toggle_topic_stick' == $get_array['action'] ) {
					if ( ! empty( $post->post_parent ) ) {
						$post_id = $post->post_parent;
					} else {
						$post_id = $get_array['topic_id'];
					}

					$bbp_sticky_topics = maybe_unserialize( get_post_meta( $post_id, '_bbp_sticky_topics', true ) );
					$fn = $this->IsMultisite() ? 'get_site_option' : 'get_option';
					$bbp_super_sticky_topics = maybe_unserialize( $fn( '_bbp_super_sticky_topics' ) );
					if ( ! empty( $bbp_sticky_topics ) && in_array( $get_array['topic_id'], $bbp_sticky_topics ) ) {
						$old_type = 'sticky';
					} elseif ( ! empty( $bbp_super_sticky_topics ) && in_array( $get_array['topic_id'], $bbp_super_sticky_topics ) ) {
						$old_type = 'super sticky';
					} else {
						$old_type = 'normal';
					}

					switch ( $old_type ) {
						case 'sticky':
						case 'super sticky':
							$new_type = 'normal';
							break;
						case 'normal':
							if ( isset( $get_array['super'] ) && 1 == $get_array['super'] ) {
								$new_type = 'super sticky';
							} else {
								$new_type = 'sticky';
							}
							break;
					}
					$editor_link = $this->GetEditorLink( $post );

					if ( ! empty( $new_type ) && $old_type != $new_type ) {
						$this->plugin->alerts->Trigger(
							8016, array(
								'TopicName' => $post->post_title,
								'OldType' => $old_type,
								'NewType' => $new_type,
								$editor_link['name'] => $editor_link['value'],
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Get editor link.
	 *
	 * @param stdClass $post - The post.
	 * @return array $editor_link_array - Name and value link.
	 */
	private function GetEditorLink( $post ) {
		$name = 'EditorLink';
		switch ( $post->post_type ) {
			case 'forum':
				$name .= 'Forum' ;
				break;
			case 'topic':
				$name .= 'Topic' ;
				break;
		}
		$value = get_edit_post_link( $post->ID );
		$editor_link_array = array(
			'name' => $name,
			'value' => $value,
		);
		return $editor_link_array;
	}
}
