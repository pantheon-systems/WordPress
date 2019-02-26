<?php
/**
 * @package     WBC_Importer - Extension for Importing demo content
 * @author      Webcreations907
 * @version     1.0.2
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if ( !class_exists( 'Wbc_Importer_Progress' ) ) {

	class Wbc_Importer_Progress {

		public static $instance;

		protected $parent;

		public $total_posts;

		/**
		 * Class Constructor
		 *
		 * @since       1.0
		 * @access      public
		 * @return      void
		 */
		public function __construct( $redparent ) {
			$this->parent   = $redparent;

			self::$instance = $this;

			add_action( 'wp_ajax_redux_wbc_importer_progress', array( $this, 'ajax_importer_progress' ) );

			add_action( 'wp_import_posts' , array( $this, 'progress_init' ) );

			add_action( 'add_attachment', array( $this, 'update_count' ) );
			add_action( 'edit_attachment', array( $this, 'update_count' ) );
			add_action( 'wp_insert_post', array( $this, 'update_count' ) );


			add_filter( 'wp_import_post_data_raw', array( $this, 'check_post' ) );

		}

		/**
		 * Checks if posts already exists or post types missing
		 *
		 * @param array   $post post to be imported
		 * @return array       retrun for WP_Importer
		 */
		public function check_post( $post ) {

			if ( ! post_type_exists( $post['post_type'] ) ) {
				$this->update_count();
				return $post;
			}

			if ( $post['status'] == 'auto-draft' ) {
				$this->update_count();
				return $post;
			}

			if ( 'nav_menu_item' == $post['post_type'] ) {
				$this->update_count();
				return $post;
			}

			$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );
			if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
				$this->update_count();
				return $post;
			}

			return $post;
		}

		/**	
		 * Sets post count option
		 * @param  array $posts Post array
		 * @return array        return array for WP_Importer
		 */
		public function progress_init( $posts ) {

			$progress_array = array(
				'total_post'     => count( $posts ),
				'imported_count' => 0,
				'remaining'      => count( $posts )
			);


			// $this->total_posts = $count_post;

			update_option( 'wbc_import_progress',  $progress_array );

			return $posts;
		}

		// Ajax Request
		public function ajax_importer_progress() {
			if ( !isset( $_REQUEST['nonce'] ) || !wp_verify_nonce( $_REQUEST['nonce'], "redux_{$this->parent->args['opt_name']}_wbc_importer" ) ) {
				die( 0 );
			}

			if ( is_array( $this->get_count() ) ) {
				echo json_encode( $this->get_count() );
			}

			die( 0 );
		}


		//Update post count totals
		public function update_count() {
			$post_count = get_option( 'wbc_import_progress' );

			if ( is_array( $post_count ) ) {
				if ( $post_count['remaining'] > 0 ) {
					$post_count['remaining']      = $post_count['remaining'] - 1;
					$post_count['imported_count'] = $post_count['imported_count'] + 1;
					update_option( 'wbc_import_progress', $post_count );
				}else {
					$post_count['remaining']      = 0;
					$post_count['imported_count'] = $post_count['total_post'];
					update_option( 'wbc_import_progress', $post_count );
				}
			}
		}

		// Returns post count array
		public function get_count() {
			return get_option( 'wbc_import_progress' );
		}


		public static function get_instance() {
			return self::$instance;
		}

	}//end class

} //end class exists