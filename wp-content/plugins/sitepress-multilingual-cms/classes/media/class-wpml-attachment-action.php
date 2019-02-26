<?php

class WPML_Attachment_Action implements IWPML_Action {

	/** @var SitePress */
	private $sitepress;

	/** @var wpdb */
	private $wpdb;

	public function __construct( SitePress $sitepress, wpdb $wpdb ) {
		$this->sitepress = $sitepress;
		$this->wpdb      = $wpdb;
	}

	public function add_hooks() {
		if ( $this->is_admin_or_xmlrpc() && ! $this->is_uploading_plugin_or_theme() ) {

			$active_languages = $this->sitepress->get_active_languages();

			if ( count( $active_languages ) > 1 ) {
				add_filter( 'views_upload', array( $this, 'views_upload_actions' ) );
			}
		}

		add_filter( 'attachment_link', array( $this->sitepress, 'convert_url' ), 10, 1 );
		add_filter( 'wp_delete_file', array( $this, 'delete_file_actions' ) );
	}

	private function is_admin_or_xmlrpc() {
		$is_admin  = is_admin();
		$is_xmlrpc = ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST );

		return $is_admin || $is_xmlrpc;
	}

	private function is_uploading_plugin_or_theme() {
		global $action;

		return ( isset( $action ) && ( $action == 'upload-plugin' || $action == 'upload-theme' ) );
	}

	public function views_upload_actions( $views ) {
		global $pagenow;

		if ( $pagenow == 'upload.php' ) {
			//get current language
			$lang = $this->sitepress->get_current_language();

			foreach ( $views as $key => $view ) {
				// extract the base URL and query parameters
				$href_count = preg_match( '/(href=["\'])([\s\S]+?)\?([\s\S]+?)(["\'])/', $view, $href_matches );
				if ( $href_count && isset( $href_args ) ) {
					$href_base = $href_matches[2];
					wp_parse_str( $href_matches[3], $href_args );
				} else {
					$href_base = 'upload.php';
					$href_args = array();
				}

				if ( $lang != 'all' ) {
					$sql = $this->wpdb->prepare( "
						SELECT COUNT(p.id)
						FROM {$this->wpdb->posts} AS p
							INNER JOIN {$this->wpdb->prefix}icl_translations AS t
								ON p.id = t.element_id
						WHERE p.post_type = 'attachment'
						AND t.element_type='post_attachment'
						AND t.language_code = %s ", $lang );

					switch ( $key ) {
						case 'all';
							$and = " AND p.post_status != 'trash' ";
							break;
						case 'detached':
							$and = " AND p.post_status != 'trash' AND p.post_parent = 0 ";
							break;
						case 'trash':
							$and = " AND p.post_status = 'trash' ";
							break;
						default:
							if ( isset( $href_args['post_mime_type'] ) ) {
								$and = " AND p.post_status != 'trash' " . wp_post_mime_type_where( $href_args['post_mime_type'], 'p' );
							} else {
								$and = $this->wpdb->prepare( " AND p.post_status != 'trash' AND p.post_mime_type LIKE %s", $key . '%' );
							}
					}

					$and = apply_filters( 'wpml-media_view-upload-sql_and', $and, $key, $view, $lang );

					$sql_and = $sql . $and;
					$sql     = apply_filters( 'wpml-media_view-upload-sql', $sql_and, $key, $view, $lang );

					$res = apply_filters( 'wpml-media_view-upload-count', null, $key, $view, $lang );
					if ( null === $res ) {
						$res = $this->wpdb->get_col( $sql );
					}
					//replace count
					$view = preg_replace( '/\((\d+)\)/', '(' . $res[0] . ')', $view );
				}

				//replace href link, adding the 'lang' argument and the revised count
				$href_args['lang'] = $lang;
				$href_args         = array_map( 'urlencode', $href_args );
				$new_href          = add_query_arg( $href_args, $href_base );
				$views[ $key ]     = preg_replace( '/(href=["\'])([\s\S]+?)(["\'])/', '$1' . $new_href . '$3', $view );
			}
		}

		return $views;
	}

	//check if the image is not duplicated to another post before deleting it physically
	public function delete_file_actions( $file ) {
		static $saved_request = array();
		if ( $file ) {
			$file_name = $this->get_file_name_without_size_from_full_name( $file );
			if ( array_key_exists( $file_name, $saved_request ) ) {
				$attachment = $saved_request[$file_name];
			} else {
				$sql = "SELECT pm.meta_id, pm.post_id FROM {$this->wpdb->postmeta} AS pm 
						WHERE pm.meta_value LIKE %s AND pm.meta_key='_wp_attached_file'";
				$attachment_prepared = $this->wpdb->prepare( $sql, array( '%' . $file_name ) );
				$attachment = $this->wpdb->get_row( $attachment_prepared );

				$saved_request [$file_name] = $attachment;
			}
			if ( ! empty( $attachment ) ) {
				$file = null;
			}
		}

		return $file;
	}

	private function get_file_name_without_size_from_full_name( $file ) {
		$file_name = preg_replace( '/^(.+)\-\d+x\d+(\.\w+)$/', '$1$2', $file );
		$file_name = preg_replace( '/^[\s\S]+(\/.+)$/', '$1', $file_name );
		$file_name = str_replace( '/', '', $file_name );

		return $file_name;
	}

}