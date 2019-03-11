<?php

class WCML_Sensei {

	/** @var SitePress */
	private $sitepress;
	/** @var wpdb */
	private $wpdb;
	/** WPML_Custom_Columns $wpml_custom_columns */
	private $custom_columns;

	/**
	 * WCML_Sensei constructor.
	 *
	 * @param Sitepress $sitepress
	 * @param wpdb $wpdb
	 * @param WPML_Custom_Columns $custom_columns
	 */
	public function __construct( SitePress $sitepress, wpdb $wpdb, WPML_Custom_Columns $custom_columns ) {
		$this->sitepress      = $sitepress;
		$this->wpdb           = $wpdb;
		$this->custom_columns = $custom_columns;
	}

	public function add_hooks() {

		add_filter( 'manage_edit-lesson_columns', array( $this->custom_columns, 'add_posts_management_column' ) );
		add_filter( 'manage_edit-course_columns', array( $this->custom_columns, 'add_posts_management_column' ) );
		add_filter( 'manage_edit-question_columns', array( $this->custom_columns, 'add_posts_management_column' ) );

		add_action( 'save_post', array( $this, 'save_post_actions' ), 100, 2 );
		add_action( 'sensei_log_activity_after', array( $this, 'log_activity_after' ), 10, 3 );
		add_filter( 'sensei_bought_product_id', array( $this, 'filter_bought_product_id' ), 10, 2 );
		add_action( 'delete_comment', array( $this, 'delete_user_activity' ) );

		add_action( 'pre_get_comments', array( $this, 'pre_get_comments' ) );

		if ( $this->is_sensei_admin_without_language_switcher() ) {
			remove_action( 'wp_before_admin_bar_render', array( $this->sitepress, 'admin_language_switcher' ) );
		}
	}

	private function is_sensei_admin_without_language_switcher() {

		$is_message_post_type = isset( $_GET['post_type'] ) && $_GET['post_type'] == 'sensei_message';
		$is_grading_page      = isset( $_GET['page'] ) && $_GET['page'] == 'sensei_grading';

		return is_admin() && ( $is_message_post_type || $is_grading_page );
	}

	public function save_post_actions( $post_id, $post ) {
		global $sitepress;

		// skip not related post types
		if ( ! in_array( $post->post_type, array( 'lesson', 'course', 'quiz' ) ) ) {
			return;
		}
		// skip auto-drafts
		if ( $post->post_status == 'auto-draft' ) {
			return;
		}
		// skip autosave
		if ( isset( $_POST['autosave'] ) ) {
			return;
		}

		if ( $post->post_type == 'quiz' && isset( $_POST['ID'] ) ) {
			$this->save_post_actions( $_POST['ID'], get_post( $_POST['ID'] ) );
		}


		// sync fields from original
		$trid         = $sitepress->get_element_trid( $post_id, 'post_' . $post->post_type );
		$translations = $sitepress->get_element_translations( $trid, 'post_' . $post->post_type );

		if ( ! empty( $translations ) ) {
			$original_post_id = false;
			foreach ( $translations as $t ) {
				if ( $t->original ) {
					$original_post_id = $t->element_id;
					break;
				}
			}

			if ( $post_id != $original_post_id ) {
				$this->sync_custom_fields( $original_post_id, $post_id, $post->post_type );
			} else {
				foreach ( $translations as $t ) {
					if ( $original_post_id != $t->element_id ) {
						$this->sync_custom_fields( $original_post_id, $t->element_id, $post->post_type );
					}
				}
			}
		}

	}


	function sync_custom_fields( $original_post_id, $post_id, $post_type ) {
		global $sitepress;

		$language = $sitepress->get_language_for_element( $post_id, 'post_' . $post_type );
		if ( $post_type == 'quiz' ) {

			//sync quiz lesson
			$lesson_id = get_post_meta( $original_post_id, '_quiz_lesson', true );

			if ( $lesson_id ) {
				$tr_lesson_id = apply_filters( 'translate_object_id', $lesson_id, 'lesson', false, $language );

				if ( ! is_null( $tr_lesson_id ) ) {
					update_post_meta( $post_id, '_quiz_lesson', $tr_lesson_id );
				}
			} else {
				delete_post_meta( $post_id, '_quiz_lesson' );
			}

		} elseif ( $post_type == 'lesson' ) {
			//sync lesson course
			$course_id = get_post_meta( $original_post_id, '_lesson_course', true );

			if ( $course_id ) {
				$tr_course_id = apply_filters( 'translate_object_id', $course_id, 'course', false, $language );

				if ( ! is_null( $tr_course_id ) ) {
					update_post_meta( $post_id, '_lesson_course', $tr_course_id );
				}
			} else {
				delete_post_meta( $post_id, '_lesson_course' );
			}

			//sync lesson prerequisite
			$lesson_id = get_post_meta( $original_post_id, '_lesson_prerequisite', true );

			if ( $lesson_id ) {
				$tr_lesson_id = apply_filters( 'translate_object_id', $lesson_id, 'lesson', false, $language );

				if ( ! is_null( $tr_lesson_id ) ) {
					update_post_meta( $post_id, '_lesson_prerequisite', $tr_lesson_id );
				}
			} else {
				delete_post_meta( $post_id, '_lesson_prerequisite' );
			}

		} else {

			//sync course woocommerce_product
			$product_id = get_post_meta( $original_post_id, '_course_woocommerce_product', true );

			if ( $product_id ) {
				$tr_product_id = apply_filters( 'translate_object_id', $product_id, get_post_type( $product_id ), false, $language );

				if ( ! is_null( $tr_product_id ) ) {
					update_post_meta( $post_id, '_course_woocommerce_product', $tr_product_id );
				}
			} else {
				delete_post_meta( $post_id, '_course_woocommerce_product' );
			}

			//sync course prerequisite
			$course_id = get_post_meta( $original_post_id, '_course_prerequisite', true );

			if ( $course_id ) {
				$tr_course_id = apply_filters( 'translate_object_id', $course_id, 'course', false, $language );

				if ( ! is_null( $tr_course_id ) ) {
					update_post_meta( $post_id, '_course_prerequisite', $tr_course_id );
				}
			} else {
				delete_post_meta( $post_id, '_course_prerequisite' );
			}

		}

	}

	function log_activity_after( $args, $data, $comment_id = false ) {
		global $sitepress;

		if ( ! $comment_id ) {
			return false;
		}

		$comment_post_id = $data['comment_post_ID'];
		$trid            = $sitepress->get_element_trid( $comment_post_id, 'post_' . get_post_type( $comment_post_id ) );
		$translations    = $sitepress->get_element_translations( $trid, 'post_' . get_post_type( $comment_post_id ) );

		foreach ( $translations as $translation ) {

			if ( $comment_post_id != $translation->element_id ) {
				$data['comment_post_ID'] = $translation->element_id;

				$trid = $sitepress->get_element_trid( $comment_id, 'comment' );

				$tr_comment_id = apply_filters( 'translate_object_id', $comment_id, 'comment', false, $translation->language_code );
				if ( isset( $args['action'] ) && 'update' == $args['action'] && ! is_null( $tr_comment_id ) && get_comment( $tr_comment_id ) ) {
					$data['comment_ID'] = $tr_comment_id;
					$tr_comment_id      = wp_update_comment( $data );
				} else {
					$tr_comment_id = wp_insert_comment( $data );
					$sitepress->set_element_language_details( $tr_comment_id, 'comment', $trid, $translation->language_code );
				}
			}

		}

	}

	function filter_bought_product_id( $product_id, $order ) {

		$order_id       = method_exists( 'WC_Order', 'get_id' ) ? $order->get_id() : $order->id;
		$order_language = get_post_meta( $order_id, 'wpml_language', true );

		$tr_product_id = apply_filters( 'translate_object_id', $product_id, get_post_type( $product_id ), false, $order_language );

		if ( ! is_null( $tr_product_id ) ) {
			return $tr_product_id;
		} else {
			return $product_id;
		}
	}

	function delete_user_activity( $comment_id ) {
		global $sitepress;

		$comment_type = get_comment_type( $comment_id );

		if ( strstr( $comment_type, "sensei" ) ) {

			$trid         = $sitepress->get_element_trid( $comment_id, 'comment' );
			$translations = $sitepress->get_element_translations( $trid, 'comment' );

			remove_action( 'delete_comment', array( $this, 'delete_user_activity' ) );
			foreach ( $translations as $translation ) {
				if ( $comment_id != $translation->element_id ) {
					wp_delete_comment( $translation->element_id, true );
				}
			}

		}


	}

	function pre_get_comments( $obj ) {
		global $sitepress;

		if ( $obj->query_vars['type'] == 'sensei_course_start' ) {

			remove_filter( 'comments_clauses', array( $sitepress, 'comments_clauses' ), 10, 2 );

		}

	}

}
