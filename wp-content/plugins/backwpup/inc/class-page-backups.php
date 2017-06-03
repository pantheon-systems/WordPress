<?php
/**
 *
 */
class BackWPup_Page_Backups extends WP_List_Table {

	private static $listtable = NULL;
	private $destinations = array();

	/**
	 * @var int
	 */
	private $jobid = 1;
	/**
	 * @var string
	 */
	private $dest = 'FOLDER';

	/**
	 *
	 */
	function __construct() {

		parent::__construct( array(
								  'plural'   => 'backups',
								  'singular' => 'backup',
								  'ajax'     => TRUE
							 ) );

		$this->destinations = BackWPup::get_registered_destinations();

	}

	/**
	 * @return bool
	 */
	function ajax_user_can() {

		return current_user_can( 'backwpup_backups' );
	}

	/**
	 *
	 */
	function prepare_items() {

		$per_page = $this->get_items_per_page( 'backwpupbackups_per_page' );
		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = 20;
		}

		$jobdest = '';
		if ( isset( $_GET[ 'jobdets-button-top' ] ) ) {
			$jobdest = sanitize_text_field( $_GET[ 'jobdest-top' ] );
		}
		if( isset( $_GET[ 'jobdets-button-bottom' ] ) ) {
			$jobdest = sanitize_text_field( $_GET[ 'jobdest-bottom' ] );
		}

		if ( empty( $jobdest ) ) {
			$jobdests = $this->get_destinations_list();
			if ( empty( $jobdests ) ) {
				$jobdests = array( '_' );
			}
			$jobdest           	 	= $jobdests[ 0 ];
			$_GET[ 'jobdest-top' ] 	= $jobdests[ 0 ];
			$_GET[ 'jobdets-button-top' ] = 'empty';
		}

		list( $this->jobid, $this->dest ) = explode( '_', $jobdest );

		if ( ! empty( $this->destinations[ $this->dest ][ 'class' ] ) ) {
			/** @var BackWPup_Destinations $dest_object */
			$dest_object = BackWPup::get_destination( $this->dest );
			$this->items = $dest_object->file_get_list( $jobdest );
		}

		//if no items brake
		if ( ! $this->items ) {
			$this->items = '';

			return;
		}

		//Sorting
		$order   = isset( $_GET[ 'order' ] ) ? $_GET[ 'order' ] : 'desc';
		$orderby = isset( $_GET[ 'orderby' ] ) ? $_GET[ 'orderby' ] : 'time';
		$tmp     = Array();
		if ( $orderby === 'time' ) {
			if ( $order === 'asc' ) {
				foreach ( $this->items as &$ma ) {
					$tmp[ ] = & $ma[ "time" ];
				}
				array_multisort( $tmp, SORT_ASC, $this->items );
			}
			else {
				foreach ( $this->items as &$ma ) {
					$tmp[ ] = & $ma[ "time" ];
				}
				array_multisort( $tmp, SORT_DESC, $this->items );
			}
		}
		elseif ( $orderby === 'file' ) {
			if ( $order === 'asc' ) {
				foreach ( $this->items as &$ma ) {
					$tmp[ ] = & $ma[ "filename" ];
				}
				array_multisort( $tmp, SORT_ASC, $this->items );
			}
			else {
				foreach ( $this->items as &$ma ) {
					$tmp[ ] = & $ma[ "filename" ];
				}
				array_multisort( $tmp, SORT_DESC, $this->items );
			}
		}
		elseif ( $orderby === 'folder' ) {
			if ( $order === 'asc' ) {
				foreach ( $this->items as &$ma ) {
					$tmp[ ] = & $ma[ "folder" ];
				}
				array_multisort( $tmp, SORT_ASC, $this->items );
			}
			else {
				foreach ( $this->items as &$ma ) {
					$tmp[ ] = & $ma[ "folder" ];
				}
				array_multisort( $tmp, SORT_DESC, $this->items );
			}
		}
		elseif ( $orderby === 'size' ) {
			if ( $order === 'asc' ) {
				foreach ( $this->items as &$ma ) {
					$tmp[ ] = & $ma[ "filesize" ];
				}
				array_multisort( $tmp, SORT_ASC, $this->items );
			}
			else {
				foreach ( $this->items as &$ma ) {
					$tmp[ ] = & $ma[ "filesize" ];
				}
				array_multisort( $tmp, SORT_DESC, $this->items );
			}
		}

		$this->set_pagination_args( array(
										 'total_items' => count( $this->items ),
										 'per_page'    => $per_page,
										 'jobdest'     => $jobdest,
										 'orderby'     => $orderby,
										 'order'       => $order
									) );

		//only display items on page
		$start = intval( ( $this->get_pagenum() - 1 ) * $per_page );
		$end   = $start + $per_page;
		if ( $end > count( $this->items ) ) {
			$end = count( $this->items );
		}

		$i = -1;
		$paged_items = array();
		foreach ( $this->items as $item ) {
			$i++;
			if ( $i < $start ) {
				continue;
			}
			if ( $i >= $end ) {
				break;
			}
			$paged_items[] = $item;
		}

		$this->items = $paged_items;

	}

	/**
	 *
	 */
	function no_items() {
		_e( 'No files could be found. (List will be generated during next backup.)', 'backwpup' );
	}

	/**
	 * @return array
	 */
	function get_bulk_actions() {

		if ( ! $this->has_items() ) {
			return array ();
		}

		$actions             = array();
		$actions[ 'delete' ] = __( 'Delete', 'backwpup' );

		return $actions;
	}

	/**
	 * @param $which
	 *
	 * @return mixed
	 */
	function extra_tablenav( $which ) {

		$destinations_list = $this->get_destinations_list();

		if (  count( $destinations_list ) < 1)
			return;

		if ( count( $destinations_list ) === 1 ) {
			echo '<input type="hidden" name="jobdest-' . $which . '" value="' . $destinations_list[0] . '">';
			return;
		}

		?>
		<div class="alignleft actions">
			<label for="jobdest-<?php echo esc_attr($which);?>">
				<select name="jobdest-<?php echo esc_html($which);?>" class="postform" id="jobdest-<?php echo esc_attr($which);?>">
					<?php
					foreach ( $destinations_list as $jobdest ) {
						list( $jobid, $dest ) = explode( '_', $jobdest );
						echo "\t<option value=\"" . $jobdest . "\" " . selected( $this->jobid . '_' . $this->dest, $jobdest, false ) . ">" . $dest . ": " . esc_html( BackWPup_Option::get( $jobid, 'name' ) ) . "</option>" . PHP_EOL;
					}
					?>
				</select>
			</label>
			<?php submit_button( __( 'Change destination', 'backwpup' ), 'secondary', 'jobdets-button-' . $which, FALSE, array( 'id' => 'query-submit-'.$which ) ); ?>
		</div>
		<?php
	}

	/**
	 * @return array
	 */
	function get_destinations_list() {

		$jobdest      = array();
		$jobids       = BackWPup_Option::get_job_ids();

		foreach ( $jobids as $jobid ) {
			if ( BackWPup_Option::get( $jobid, 'backuptype' ) === 'sync' ) {
				continue;
			}
			$dests = BackWPup_Option::get( $jobid, 'destinations' );
			foreach ( $dests as $dest ) {
				if ( ! $this->destinations[ $dest ][ 'class' ] ) {
					continue;
				}
				$dest_class = BackWPup::get_destination( $dest );
				$can_do_dest = $dest_class->file_get_list( $jobid . '_' . $dest );
				if ( ! empty( $can_do_dest ) ) {
					$jobdest[ ] = $jobid . '_' . $dest;
				}
			}
		}

		return $jobdest;
	}

	/**
	 * @return array
	 */
	function get_columns() {

		$posts_columns             = array();
		$posts_columns[ 'cb' ]     = '<input type="checkbox" />';
		$posts_columns[ 'time' ]   = __( 'Time', 'backwpup' );
		$posts_columns[ 'file' ]   = __( 'File', 'backwpup' );
		$posts_columns[ 'folder' ] = __( 'Folder', 'backwpup' );
		$posts_columns[ 'size' ]   = __( 'Size', 'backwpup' );

		return $posts_columns;
	}

	/**
	 * @return array
	 */
	function get_sortable_columns() {

		return array(
			'file'   => array( 'file', FALSE ),
			'folder' => 'folder',
			'size'   => 'size',
			'time'   => array( 'time', FALSE )
		);
	}

	/**
	 * The cb Column
	 *
	 * @param $item
	 * @return string
	 */
	function column_cb( $item ) {

		return '<input type="checkbox" name="backupfiles[]" value="' . esc_attr( $item[ 'file' ] ) . '" />';
	}


	/**
	 * The file Column
	 *
	 * @param $item
	 * @return string
	 */
	function column_file( $item ) {

		$r = '<strong>' . esc_attr( $item[ 'filename' ] ) . '</strong><br />';
		if ( ! empty( $item[ 'info' ] ) )
			$r .= esc_attr( $item[ 'info' ] ) . '<br />';
		$actions               = array();
		if ( current_user_can( 'backwpup_backups_delete' ) ) {
			$actions[ 'delete' ]   = "<a class=\"submitdelete\" href=\"" . wp_nonce_url( network_admin_url( 'admin.php' ) . '?page=backwpupbackups&action=delete&jobdest-top=' . $this->jobid . '_' . $this->dest . '&paged=' . $this->get_pagenum() . '&backupfiles[]=' . esc_attr( $item[ 'file' ] ), 'bulk-backups' ) . "\" onclick=\"if ( confirm('" . esc_js( __( "You are about to delete this backup archive. \n  'Cancel' to stop, 'OK' to delete.", "backwpup" ) ) . "') ) { return true;}return false;\">" . __( 'Delete', 'backwpup' ) . "</a>";
		}
		if ( current_user_can( 'backwpup_backups_download' ) && ! empty( $item[ 'downloadurl' ] ) ) {
			$actions[ 'download' ] = "<a href=\"" . wp_nonce_url( $item[ 'downloadurl' ], 'download-backup_' . $this->jobid ) . "\">" . __( 'Download', 'backwpup' ) . "</a>";
		}
		$r .= $this->row_actions( $actions );

		return $r;
	}

	/**
	 * The folder Column
	 *
	 * @param $item
	 * @return string
	 */
	function column_folder( $item ) {

		return esc_attr( $item[ 'folder' ] );
	}

	/**
	 * The size Column
	 *
	 * @param $item
	 * @return string
	 */
	function column_size( $item ) {

		if ( ! empty( $item[ 'filesize' ] ) && $item[ 'filesize' ] != - 1 ) {
			return size_format( $item[ 'filesize' ], 2 );
		}
		else {
			return __( '?', 'backwpup' );
		}
	}

	/**
	 * The time Column
	 *
	 * @param $item
	 * @return string
	 */
	function column_time( $item ) {

		$item[ 'time' ] = $item[ 'time' ] + get_option( 'gmt_offset' ) * 3600;
		return sprintf( __( '%1$s at %2$s', 'backwpup' ), date_i18n( get_option( 'date_format' ), $item[ 'time' ], TRUE ), date_i18n( get_option( 'time_format' ), $item[ 'time' ], TRUE ) );
	}


	/**
	 *
	 */
	public static function load() {

		//Create Table
		self::$listtable = new BackWPup_Page_Backups;

		switch ( self::$listtable->current_action() ) {
			case 'delete': //Delete Backup archives
				check_admin_referer( 'bulk-backups' );
				if ( ! current_user_can( 'backwpup_backups_delete' ) ) {
					wp_die( __( 'Sorry, you don\'t have permissions to do that.', 'backwpup' ) );
				}

				$jobdest = '';
				if ( isset( $_GET[ 'jobdest' ] ) ) {
					$jobdest = sanitize_text_field( $_GET[ 'jobdest' ] );
				}
				if ( isset( $_GET[ 'jobdest-top' ] ) ) {
					$jobdest = sanitize_text_field( $_GET[ 'jobdest-top' ] );
				}

				$_GET[ 'jobdest-top' ] = $jobdest;
				$_GET[ 'jobdets-button-top' ] = 'submit';

				if ( $jobdest === '' ) {
					return;
				}

				list( $jobid, $dest ) = explode( '_', $jobdest );
				/** @var BackWPup_Destinations $dest_class */
				$dest_class = BackWPup::get_destination( $dest );
				$files = $dest_class->file_get_list( $jobdest );
				foreach ( $_GET[ 'backupfiles' ] as $backupfile ) {
					foreach ( $files as $file ) {
						if ( is_array( $file ) && $file[ 'file' ] == $backupfile ) {
							$dest_class->file_delete( $jobdest, $backupfile );
						}
					}
				}
				$files = $dest_class->file_get_list( $jobdest );
				if ( empty ( $files ) ) {
					$_GET[ 'jobdest-top' ] = '';
				}
				break;
			default:
				if ( isset( $_GET[ 'jobid' ] ) ) {
					$jobid = absint( $_GET[ 'jobid' ] );
					$dest = strtoupper( str_replace( 'download', '', self::$listtable->current_action() ) );
					if ( ! empty( $dest ) && strstr( self::$listtable->current_action(), 'download' ) ) {
						if ( ! current_user_can( 'backwpup_backups_download' ) ) {
							wp_die( __( 'Sorry, you don\'t have permissions to do that.', 'backwpup') );
						}
						check_admin_referer( 'download-backup_' . $jobid );
						/** @var BackWPup_Destinations $dest_class */
						$dest_class = BackWPup::get_destination( $dest );
						$dest_class->file_download( $jobid, trim( sanitize_text_field( $_GET[ 'file' ] ) ) );
						die();
					}
				}
				break;
		}

		//Save per page
		if ( isset( $_POST[ 'screen-options-apply' ] ) && isset( $_POST[ 'wp_screen_options' ][ 'option' ] ) && isset( $_POST[ 'wp_screen_options' ][ 'value' ] ) && $_POST[ 'wp_screen_options' ][ 'option' ] == 'backwpupbackups_per_page' ) {
			check_admin_referer( 'screen-options-nonce', 'screenoptionnonce' );
			global $current_user;
			if ( $_POST[ 'wp_screen_options' ][ 'value' ] > 0 && $_POST[ 'wp_screen_options' ][ 'value' ] < 1000 ) {
				update_user_option( $current_user->ID, 'backwpupbackups_per_page', (int)$_POST[ 'wp_screen_options' ][ 'value' ] );
				wp_redirect( remove_query_arg( array( 'pagenum', 'apage', 'paged' ), wp_get_referer() ) );
				exit;
			}
		}

		add_screen_option( 'per_page', array(
											'label'   => __( 'Backup Files', 'backwpup' ),
											'default' => 20,
											'option'  => 'backwpupbackups_per_page'
									   ) );

		self::$listtable->prepare_items();
	}

	/**
	 * Output css
	 */
	public static function admin_print_styles() {

		?>
		<style type="text/css" media="screen">
			.column-size, .column-time {
				width: 10%;
			}
			@media screen and (max-width: 782px) {
				.column-size, .column-runtime, .column-size, .column-folder {
					display: none;
				}
				.column-time {
					width: 18%;
				}
			}
		</style>
		<?php
	}

	/**
	 *
	 * Output js
	 *
	 * @return void
	 */
	public static function admin_print_scripts() {

		wp_enqueue_script( 'backwpupgeneral' );
	}

	/**
	 * Display the page content
	 */
	public static function page() {

		?>
		<div class="wrap" id="backwpup-page">
			<h1><?php echo esc_html( sprintf( __( '%s &rsaquo; Manage Backup Archives', 'backwpup' ), BackWPup::get_plugin_data( 'name' ) ) ); ?></h1>
			<?php BackWPup_Admin::display_messages(); ?>
            <form id="posts-filter" action="" method="get">
            	<input type="hidden" name="page" value="backwpupbackups" />
				<?php self::$listtable->display(); ?>
                <div id="ajax-response"></div>
            </form>
        </div>
		<?php
	}
}

