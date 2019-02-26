<?php
/**
 * Direct Link Tracking Admin
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

include AFFWP_DLT_PLUGIN_DIR . 'includes/screen-options.php';

function affwp_direct_links_admin() {

	$action = null;

	if ( isset( $_GET['action2'] ) && '-1' !== $_GET['action2'] ) {
		$action = $_GET['action2'];
	} elseif ( isset( $_GET['action'] ) && '-1' !== $_GET['action'] ) {
		$action = $_GET['action'];
	}

	if ( isset( $_GET['action'] ) && 'add_direct_link' == $_GET['action'] ) {

		include AFFWP_DLT_PLUGIN_DIR . 'includes/add-direct-link.php';

	} elseif ( 'edit_direct_link' === $action ) {

		include AFFWP_DLT_PLUGIN_DIR . 'includes/edit-direct-link.php';

	} else {

		$direct_links_table = new AffWP_Direct_Link_Tracking_Table();
		$direct_links_table->prepare_items();
?>
		<div class="wrap">

			<h1>
				<?php _e( 'Direct Links', 'affiliatewp-direct-link-tracking' ); ?>

				<a href="<?php echo esc_url( remove_query_arg( array( 'affwp_notice' ), add_query_arg( 'action', 'add_direct_link' ) ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'affiliatewp-direct-link-tracking' ); ?></a>
			</h1>

			<?php do_action( 'affwp_direct_link_tracking_direct_links_page_top' ); ?>

			<form id="affwp-direct-links-filter" method="get" action="<?php echo admin_url( 'admin.php?page=affiliate-wp' ); ?>">

				<input type="hidden" name="page" value="affiliate-wp-direct-links" />

				<?php $direct_links_table->views() ?>
				<?php $direct_links_table->display() ?>
			</form>

			<?php do_action( 'affwp_direct_link_tracking_direct_links_page_bottom' ); ?>
		</div>
<?php

	}

}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * AffWP_Direct_Link_Tracking_Table Class
 *
 * Renders the Direct Link Tracking table on the Direct Link Tracking page
 *
 * @since 1.0
 */
class AffWP_Direct_Link_Tracking_Table extends WP_List_Table {

	/**
	 * Default number of items to show per page
	 *
	 * @var string
	 * @since 1.0
	 */
	public $per_page = 30;

	/**
	 * Total number of direct links found
	 *
	 * @var int
	 * @since 1.0
	 */
	public $total_count;

	/**
	 * Number of active direct links found
	 *
	 * @var string
	 * @since 1.0
	 */
	public $active_count;

	/**
	 *  Number of inactive direct links found
	 *
	 * @var string
	 * @since 1.0
	 */
	public $inactive_count;

	/**
	 * Number of pending direct links found
	 *
	 * @var string
	 * @since 1.0
	 */
	public $pending_count;

	/**
	 * Number of rejected direct links found
	 *
	 * @var string
	 * @since 1.0
	 */
	public $rejected_count;

	/**
	 * Get things started
	 *
	 * @since 1.0
	 * @uses AffWP_Direct_Link_Tracking_Table::get_direct_link_counts()
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;

		parent::__construct( array(
			'singular'  => 'direct-link',
			'plural'    => 'direct-links',
			'ajax'      => false
		) );

		$this->get_direct_link_counts();
	}

	/**
	 * Show the search field
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param string $text Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @return void
	 */
	public function search_box( $text, $input_id ) {

		if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
			return;

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
			<?php submit_button( $text, 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
		</p>

	<?php
	}

	/**
	 * Retrieve the view types
	 *
	 * @access public
	 * @since 1.0
	 * @return array $views All the views available
	 */
	public function get_views() {
		$base           = admin_url( 'admin.php?page=affiliate-wp-direct-links' );

		$current        = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$total_count    = '&nbsp;<span class="count">(' . $this->total_count    . ')</span>';
		$active_count   = '&nbsp;<span class="count">(' . $this->active_count . ')</span>';
		$inactive_count = '&nbsp;<span class="count">(' . $this->inactive_count  . ')</span>';
		$pending_count  = '&nbsp;<span class="count">(' . $this->pending_count  . ')</span>';
		$rejected_count = '&nbsp;<span class="count">(' . $this->rejected_count  . ')</span>';

		$views = array(
			'all'		=> sprintf( '<a href="%s"%s>%s</a>', esc_url( remove_query_arg( 'status', $base ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __( 'All', 'affiliatewp-direct-link-tracking' ) . $total_count ),
			'active'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'active', $base ) ), $current === 'active' ? ' class="current"' : '', __( 'Active', 'affiliatewp-direct-link-tracking' ) . $active_count ),
			'inactive'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'inactive', $base ) ), $current === 'inactive' ? ' class="current"' : '', __( 'Inactive', 'affiliatewp-direct-link-tracking' ) . $inactive_count ),
			'pending'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'pending', $base ) ), $current === 'pending' ? ' class="current"' : '', __( 'Pending', 'affiliatewp-direct-link-tracking' ) . $pending_count ),
			'rejected'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'rejected', $base ) ), $current === 'rejected' ? ' class="current"' : '', __( 'Rejected', 'affiliatewp-direct-link-tracking' ) . $rejected_count ),
		);

		return $views;
	}

	/**
	 * Retrieve the table columns
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {

		$columns = array(
			'cb'           => '<input type="checkbox" />',
            'url'          => __( 'Domain', 'affiliatewp-direct-link-tracking' ),
            'affiliate'    => __( 'Affiliate', 'affiliatewp-direct-link-tracking' ),
			'visits'       => __( 'Visits', 'affiliatewp-direct-link-tracking' ),
			'status'       => __( 'Status', 'affiliatewp-direct-link-tracking' ),
			'date'         => __( 'Date Added', 'affiliatewp-direct-link-tracking' ),
			'actions'      => __( 'Actions', 'affiliatewp-direct-link-tracking' ),
		);

		return apply_filters( 'affwp_direct_link_tracking_table_columns', $columns );

	}

	/**
	 * Retrieve the table's sortable columns
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array Array of all the sortable columns
	 */
	public function get_sortable_columns() {

		return array(
			'url'       => array( 'url', false ),
			'affiliate' => array( 'name', false ),
			'visits'    => array( 'visits', false ),
			'status'    => array( 'status', false ),
			'date'      => array( 'date', false ),
		);

	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param array $direct_link Contains all the data of the direct link
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	function column_default( $direct_link, $column_name ) {

		switch ( $column_name ) {

			case 'date':
				$value = date_i18n( get_option( 'date_format' ), strtotime( $direct_link->date ) );
				break;

			default:
				$value = isset( $direct_link->$column_name ) ? $direct_link->$column_name : '';
				break;
		}

		return apply_filters( 'affwp_direct_link_tracking_table_' . $column_name, $value );
	}

	/**
	 * Render the Affiliate Column
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $direct_link Contains all the data of the direct link
	 * @return string Data shown in the Name column
	 */
    function column_affiliate( $direct_link ) {

		$base         = admin_url( 'admin.php?page=affiliate-wp-affiliates&action=edit_affiliate&affiliate_id=' . absint( $direct_link->affiliate_id ) );
		$name         = affiliate_wp()->affiliates->get_affiliate_name( $direct_link->affiliate_id );
		$value        = sprintf( '<a href="%s">%s</a>', $base, $name );
		$value       .= ' (ID: ' . $direct_link->affiliate_id . ')';

		return apply_filters( 'affwp_direct_link_tracking_table_affiliate', $value, $direct_link );

	}

	/**
	 * Render the Visits Column
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $direct_link Contains all the data of the direct link
	 * @return string Data shown in the Name column
	 */
    function column_visits( $direct_link ) {

		$base         = admin_url( 'admin.php?page=affiliate-wp-visits&s=' . urlencode( $direct_link->url ) . '&context=direct-link' );
		$visit_count  = affiliate_wp()->visits->count( array( 'search' => $direct_link->url, 'context' => 'direct-link' ) );
		$value        = sprintf( '<a href="%s">%s</a>', $base, __( 'Visits', 'affiliatewp-direct-link-tracking' ) );
		$value       .= ' (' . $visit_count . ')';

		return apply_filters( 'affwp_direct_link_tracking_table_visits', $value, $direct_link );

	}

	/**
	 * Render the checkbox column
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $direct_link Contains all the data for the checkbox column
	 * @return string Displays a checkbox
	 */
	function column_cb( $direct_link ) {
        return '<input type="checkbox" name="url_id[]" value="' . absint( $direct_link->url_id ) . '" />';
	}

	/**
	 * Render the actions column
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $direct_link Contains all the data for the actions column
	 * @return string action links
	 */
	function column_actions( $direct_link ) {

		$row_actions['edit'] = '<a href="' . esc_url( add_query_arg( array( 'affwp_notice' => false, 'action' => 'edit_direct_link', 'url_id' => $direct_link->url_id ) ) ) . '">' . __( 'Edit', 'affiliatewp-direct-link-tracking' ) . '</a>';

		if ( strtolower( $direct_link->status ) == 'active' ) {
			$row_actions['deactivate'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'affwp_notice' => 'direct_link_deactivated', 'action' => 'deactivate', 'url_id' => $direct_link->url_id ) ), 'direct-link-nonce' ) . '">' . __( 'Deactivate', 'affiliatewp-direct-link-tracking' ) . '</a>';

		} elseif ( strtolower( $direct_link->status ) == 'pending' ) {
			$row_actions['accept'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'affwp_notice' => 'direct_link_accepted', 'action' => 'accept', 'url_id' => $direct_link->url_id ) ), 'direct-link-nonce' ) . '">' . __( 'Accept', 'affiliatewp-direct-link-tracking' ) . '</a>';
			$row_actions['reject'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'affwp_notice' => 'direct_link_rejected', 'action' => 'reject', 'url_id' => $direct_link->url_id ) ), 'direct-link-nonce' ) . '">' . __( 'Reject', 'affiliatewp-direct-link-tracking' ) . '</a>';
		} elseif ( strtolower( $direct_link->status ) == 'rejected' ) {
			$row_actions['accept'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'affwp_notice' => 'direct_link_accepted', 'action' => 'accept', 'url_id' => $direct_link->url_id ) ), 'direct-link-nonce' ) . '">' . __( 'Accept', 'affiliatewp-direct-link-tracking' ) . '</a>';
		} else {
			$row_actions['activate'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'affwp_notice' => 'direct_link_activated', 'action' => 'activate', 'url_id' => $direct_link->url_id ) ), 'direct-link-nonce' ) . '">' . __( 'Activate', 'affiliatewp-direct-link-tracking' ) . '</a>';
		}

		// delete direct link
		$row_actions['delete'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'affwp_notice' => 'direct_link_deleted', 'action' => 'delete', 'url_id' => $direct_link->url_id ) ), 'direct-link-nonce' ) . '">' . __( 'Delete', 'affiliatewp-direct-link-tracking' ) . '</a>';

		$row_actions = apply_filters( 'affwp_direct_link_tracking_row_actions', $row_actions, $direct_link );

		return $this->row_actions( $row_actions, true );

	}


	/**
	 * Message to be displayed when there are no direct links
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function no_items() {
		_e( 'No direct links found.', 'affiliatewp-direct-link-tracking' );
	}

	/**
	 * Retrieve the bulk actions
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array $actions Array of the bulk actions
	 */
	public function get_bulk_actions() {

		$actions = array(
			'accept'     => __( 'Accept', 'affiliatewp-direct-link-tracking' ),
			'reject'     => __( 'Reject', 'affiliatewp-direct-link-tracking' ),
			'activate'   => __( 'Activate', 'affiliatewp-direct-link-tracking' ),
			'deactivate' => __( 'Deactivate', 'affiliatewp-direct-link-tracking' ),
			'delete'     => __( 'Delete', 'affiliatewp-direct-link-tracking' )
		);

		return apply_filters( 'affwp_direct_link_tracking_bulk_actions', $actions );

	}

	/**
	 * Process the bulk actions
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function process_bulk_action() {

		if ( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-direct-links' ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'direct-link-nonce' ) ) {
			return;
		}

		$ids = isset( $_GET['url_id'] ) ? $_GET['url_id'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$ids = array_map( 'absint', $ids );

		if ( empty( $ids ) ) {
			return;
		}

		foreach ( $ids as $id ) {

			// Accept the direct link.
			if ( 'accept' === $this->current_action() ) {
				affwp_dlt_set_direct_link_status( $id, 'active' );

				// Make sure to clear out old URL.
				affwp_dlt_update_direct_link( $id, array( 'url_old' => '' ) );
			}

			// Reject the direct link.
			if ( 'reject' === $this->current_action() ) {
				affwp_dlt_set_direct_link_status( $id, 'rejected' );
			}

			// Make direct link active (from inactive).
			if ( 'activate' === $this->current_action() ) {
				affwp_dlt_set_direct_link_status( $id, 'active' );
			}

			// Make direct link inactive.
			if ( 'deactivate' === $this->current_action() ) {
				affwp_dlt_set_direct_link_status( $id, 'inactive' );
			}

			// Delete the direct link.
			if ( 'delete' === $this->current_action() ) {
				affwp_dlt_delete_direct_link( $id );
			}

		}

	}

	/**
	 * Retrieve the direct link URL counts
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function get_direct_link_counts() {

		$search = isset( $_GET['s'] ) ? $_GET['s'] : '';

		$this->active_count   = affiliatewp_direct_link_tracking()->direct_links->count( array( 'status' => 'active', 'search' => $search ) );
		$this->inactive_count = affiliatewp_direct_link_tracking()->direct_links->count( array( 'status' => 'inactive', 'search' => $search ) );
		$this->pending_count  = affiliatewp_direct_link_tracking()->direct_links->count( array( 'status' => 'pending', 'search' => $search ) );
		$this->rejected_count = affiliatewp_direct_link_tracking()->direct_links->count( array( 'status' => 'rejected', 'search' => $search ) );

		$this->total_count    = $this->active_count + $this->inactive_count + $this->pending_count + $this->rejected_count;
	}

    /**
	 * Retrieve all the data for all the Direct Links
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array $direct_links Array of all the data for the Direct Links
	 */
	public function direct_link_data() {

		$page    = isset( $_GET['paged'] )    ? absint( $_GET['paged'] ) : 1;
		$status  = isset( $_GET['status'] )   ? $_GET['status']          : '';
		$search  = isset( $_GET['s'] )        ? $_GET['s']               : '';
		$order   = isset( $_GET['order'] )    ? $_GET['order']           : 'DESC';
		$orderby = isset( $_GET['orderby'] )  ? $_GET['orderby']         : 'url_id';

		$per_page = $this->get_items_per_page( 'affwp_edit_direct_links_per_page', $this->per_page );

		$direct_links = affwp_dlt_get_direct_links( array(
			'number'  => $per_page,
			'offset'  => $per_page * ( $page - 1 ),
			'status'  => $status,
			'search'  => $search,
			'orderby' => sanitize_text_field( $orderby ),
			'order'   => sanitize_text_field( $order )
		) );

		return $direct_links;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @since 1.0.0
	 * @uses AffWP_Direct_Link_Tracking_Table::get_columns()
	 * @uses AffWP_Direct_Link_Tracking_Table::get_sortable_columns()
	 * @uses AffWP_Direct_Link_Tracking_Table::process_bulk_action()
	 * @uses AffWP_Direct_Link_Tracking_Table::url_data()
	 * @uses WP_List_Table::get_pagenum()
	 * @uses WP_List_Table::set_pagination_args()
	 * @return void
	 */
	public function prepare_items() {

		$per_page = $this->get_items_per_page( 'affwp_edit_direct_links_per_page', $this->per_page );

		$columns = $this->get_columns();

		$hidden = array();

		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

        $data = $this->direct_link_data();

		$current_page = $this->get_pagenum();

		$status = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

		switch ( $status ) {
			case 'active':
				$total_items = $this->active_count;
				break;
			case 'inactive':
				$total_items = $this->inactive_count;
				break;
			case 'pending':
				$total_items = $this->pending_count;
				break;
			case 'rejected':
				$total_items = $this->rejected_count;
				break;
			case 'any':
				$total_items = $this->total_count;
				break;
		}

		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );
	}
}
