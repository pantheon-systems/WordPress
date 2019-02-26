<?php
/**
 * Referrals Admin List Table
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Referrals
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.9
 */

use AffWP\Admin\List_Table;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AffWP_Referrals_Table Class
 *
 * Renders the Affiliates table on the Affiliates page
 *
 * @since 1.0
 *
 * @see \AffWP\Admin\List_Table
 */
class AffWP_Referrals_Table extends List_Table {

	/**
	 * Default number of items to show per page
	 *
	 * @var int
	 * @since 1.0
	 */
	public $per_page = 30;

	/**
	 * Total number of referrals found
	 *
	 * @var int
	 * @since 1.0
	 */
	public $total_count;

	/**
	 * Number of paid referrals found
	 *
	 * @var int
	 * @since 1.0
	 */
	public $paid_count;

	/**
	 * Number of unpaid referrals found
	 *
	 * @var int
	 * @since 1.0
	 */
	public $unpaid_count;

	/**
	 * Number of pending referrals found
	 *
	 * @var int
	 * @since 1.0
	 */
	public $pending_count;

	/**
	 * Number of rejected referrals found
	 *
	 * @var int
	 * @since 1.0
	 */
	public $rejected_count;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @see WP_List_Table::__construct()
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through
	 *                    the list table. Default empty array.
	 */
	public function __construct( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'singular' => 'referral',
			'plural'   => 'referrals',
		) );

		parent::__construct( $args );

		$this->get_referral_counts();
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
	 * @return svoid
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

		$affiliate_id   = isset( $_GET['affiliate_id'] ) ? absint( $_GET['affiliate_id'] ) : '';
		$base           = affwp_admin_url( 'referrals' );
		$base           = $affiliate_id ? add_query_arg( 'affiliate_id', $affiliate_id, $base ) : $base;
		$current        = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$total_count    = '&nbsp;<span class="count">(' . $this->total_count    . ')</span>';
		$paid_count     = '&nbsp;<span class="count">(' . $this->paid_count . ')</span>';
		$unpaid_count   = '&nbsp;<span class="count">(' . $this->unpaid_count . ')</span>';
		$pending_count  = '&nbsp;<span class="count">(' . $this->pending_count . ')</span>';
		$rejected_count = '&nbsp;<span class="count">(' . $this->rejected_count . ')</span>';

		$views = array(
			'all'      => sprintf( '<a href="%s"%s>%s</a>', esc_url( remove_query_arg( 'status', $base ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __( 'All', 'affiliate-wp' ) . $total_count ),
			'paid'     => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'paid', $base ) ), $current === 'paid' ? ' class="current"' : '', __( 'Paid', 'affiliate-wp' ) . $paid_count ),
			'unpaid'   => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'unpaid', $base ) ), $current === 'unpaid' ? ' class="current"' : '', __( 'Unpaid', 'affiliate-wp' ) . $unpaid_count ),
			'pending'  => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'pending', $base ) ), $current === 'pending' ? ' class="current"' : '', __( 'Pending', 'affiliate-wp' ) . $pending_count ),
			'rejected' => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'rejected', $base ) ), $current === 'rejected' ? ' class="current"' : '', __( 'Rejected', 'affiliate-wp' ) . $rejected_count ),
		);

		return $views;
	}

	/**
	 * Retrieve the table columns
	 *
	 * @access public
	 * @since 1.0
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'amount'      => __( 'Amount', 'affiliate-wp' ),
			'affiliate'   => __( 'Affiliate', 'affiliate-wp' ),
			'reference'   => __( 'Reference', 'affiliate-wp' ),
			'description' => __( 'Description', 'affiliate-wp' ),
			'date'        => __( 'Date', 'affiliate-wp' ),
			'actions'     => __( 'Actions', 'affiliate-wp' ),
			'status'      => __( 'Status', 'affiliate-wp' ),
		);

		/**
		 * Filters the referrals list table columns.
		 *
		 * @param function               $prepared_columns Prepared columns.
		 * @param array                  $columns          The columns for this list table.
		 * @param \AffWP_Referrals_Table $this             List table instance.
		 */
		return apply_filters( 'affwp_referral_table_columns', $this->prepare_columns( $columns ), $columns, $this );
	}

	/**
	 * Retrieve the table's sortable columns
	 *
	 * @access public
	 * @since 1.0
	 * @return array Array of all the sortable columns
	 */
	public function get_sortable_columns() {
		return array(
			'amount'    => array( 'amount', false ),
			'affiliate' => array( 'affiliate_id', false ),
			'date'      => array( 'date', false ),
			'status'    => array( 'status', false ),
		);
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param \AffWP\Referral $referral    Contains all the data of the affiliate
	 * @param string          $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $referral, $column_name ) {
		switch( $column_name ) {

			case 'date' :
				$value = $referral->date_i18n();
				break;

			case 'description' :
				$value = wp_trim_words( $referral->description, 10 );

				/**
				 * Filters the referral description column data in the referrals list table.
				 *
				 * @param string $value       Data shown in the Description column.
				 * @param array  $description The referral description.
				 */
				$value = (string) apply_filters( 'affwp_referral_description_column', $value, $referral->description );
				break;

			default:
				$value = isset( $referral->$column_name ) ? $referral->$column_name : '';
				break;
		}

		/**
		 * Filters the default value for each column in the referrals list table.
		 *
		 * This dynamic filter is appended with a suffix of the column name, for example:
		 *
		 *     `affwp_referral_table_description`
		 *
		 * @param string $value    Column data to show.
		 * @param array  $referral Referral data.
		 *
		 */
		return apply_filters( 'affwp_referral_table_' . $column_name, $value, $referral );
	}

	/**
	 * Render the checkbox column
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param \AffWP\Referral $referral Contains all the data for the checkbox column
	 * @return string Displays a checkbox
	 */
	public function column_cb( $referral ) {
		return '<input type="checkbox" name="referral_id[]" value="' . absint( $referral->referral_id ) . '" />';
	}

	/**
	 * Renders the amount column.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param \AffWP\Referral $referral Contains all the data for the checkbox column
	 * @return string Displays the referral amount
	 */
	public function column_amount( $referral ) {
		$value = affwp_currency_filter( affwp_format_amount( $referral->amount ) );

		/**
		 * The referral amount column data.
		 *
		 * @param string          $value    Data shown in the Amount column.
		 * @param \AffWP\Referral $referral The referral data.
		 */
		return apply_filters( 'affwp_referral_table_amount', $value, $referral );
	}

	/**
	 * Renders the status column.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param \AffWP\Referral $referral Contains all the data for the checkbox column
	 * @return string Displays the referral status
	 */
	public function column_status( $referral ) {
		$value ='<span class="affwp-status ' . $referral->status . '"><i></i>' . affwp_get_referral_status_label( $referral ) . '</span>';

		/**
		 * Filters the referral status column data in the referrals list table.
		 *
		 * @param string          $value    Data shown in the Status column.
		 * @param \AffWP\Referral $referral The referral data.
		 */
		return apply_filters( 'affwp_referral_table_status', $value, $referral );
	}

	/**
	 * Renders the affiliate column.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param \AffWP\Referral $referral Contains all the data for the checkbox column
	 * @return string The affiliate
	 */
	public function column_affiliate( $referral ) {

		$value = affwp_admin_link(
			'referrals',
			affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id ),
			array( 'affiliate_id' => $referral->affiliate_id )
		);

		/**
		 * Filters the referring affiliate column data in the referrals list table.
		 *
		 * You'll also need to specify the wrapping html for this value (defaults to
		 * an anchor to the referral admin screen for this referral).
		 *
		 * @param string          $value    Data shown in the Affiliate column.
		 * @param \AffWP\Referral $referral The referral data.
		 */
		$value = apply_filters( 'affwp_referral_affiliate_column', $value, $referral );

		/**
		 * Filters the referring affiliate column data in the referrals list table.
		 *
		 * @param string          $value    Data shown in the Affiliate column.
		 * @param \AffWP\Referral $referral The referral data.
		 */
		return apply_filters( 'affwp_referral_table_affiliate', $value, $referral );
	}

	/**
	 * Renders the reference column.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param \AffWP\Referral $referral Contains all the data for the checkbox column.
	 * @return string The reference.
	 */
	public function column_reference( $referral ) {

		/**
		 * Filters the referral reference column value in the referrals list table.
		 *
		 * @param string          $reference Data shown in the Reference column.
		 * @param \AffWP\Referral $referral  The current referral.
		 */
		$value = apply_filters( 'affwp_referral_reference_column', $referral->reference, $referral );

		/**
		 * Filters the referral reference column data in the referrals list table.
		 *
		 * @param string           $value    Data shown in the Reference column.
		 * @param \AffWP\Referral  $referral The referral data.
		 */
		return apply_filters( 'affwp_referral_table_reference', $value, $referral );
	}

	/**
	 * Render the actions column
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param \AffWP\Referral $referral Contains all the data for the actions column.
	 * @return string The actions HTML.
	 */
	public function column_actions( $referral ) {

		$row_actions = array();

		$base_query_args = array(
			'referral_id' => $referral->ID
		);

		if( 'paid' == $referral->status ) {

			// Mark as Unpaid.
			$row_actions['mark-as-unpaid'] = $this->get_row_action_link(
				__( 'Mark as Unpaid', 'affiliate-wp' ),
				array_merge( $base_query_args, array(
					'action' => 'mark_as_unpaid'
				) ),
				array(
					'nonce' => 'referral-nonce',
					'class' => 'mark-as-unpaid'
				)
			);

		} else {

			if( 'unpaid' == $referral->status ) {

				// Mark as Paid.
				$row_actions['mark-as-paid'] = $this->get_row_action_link(
					__( 'Mark as Paid', 'affiliate-wp' ),
					array_merge( $base_query_args, array(
						'action' => 'mark_as_paid'
					) ),
					array(
						'nonce' => 'referral-nonce',
						'class' => 'mark-as-paid'
					)
				);

			}

			if( 'rejected' == $referral->status || 'pending' == $referral->status ) {

				// Accept.
				$row_actions['accept'] = $this->get_row_action_link(
					__( 'Accept', 'affiliate-wp' ),
					array_merge( $base_query_args, array(
						'action' => 'accept'
					) ),
					array(
						'nonce' => 'referral-nonce',
						'class' => 'accept'
					)
				);

			}

			if( 'rejected' != $referral->status ) {

				// Reject.
				$row_actions['reject'] = $this->get_row_action_link(
					__( 'Reject', 'affiliate-wp' ),
					array_merge( $base_query_args, array(
						'action' => 'reject'
					) ),
					array(
						'nonce' => 'referral-nonce',
						'class' => 'reject'
					)
				);
			}

		}

		// Edit.
		$row_actions['edit'] = $this->get_row_action_link(
			__( 'Edit', 'affiliate-wp' ),
			array_merge( $base_query_args, array(
				'action' => 'edit_referral'
			) ),
			array( 'class' => 'edit' )
		);

		// Delete.
		$row_actions['delete'] = $this->get_row_action_link(
			__( 'Delete', 'affiliate-wp' ),
			array_merge( $base_query_args, array(
				'affwp_action' => 'process_delete_referral'
			) ),
			array(
				'nonce' => 'affwp_delete_referral_nonce',
				'class' => 'delete'
			)
		);
		$row_actions['delete'] = '<span class="trash">' . $row_actions['delete'] . '</span>';

		/**
		 * Filters the row actions array for the Referrals list table.
		 *
		 * Retained only for back-compat. Use {@see 'affwp_referral_row_actions'} instead.
		 *
		 * @since 1.2
		 *
		 * @param array           $row_actions Row actions array.
		 * @param \AffWP\Referral $referral    Current referral.
		 */
		$row_actions = apply_filters( 'affwp_referral_action_links', $row_actions, $referral );

		/**
		 * Filters the row actions array for the Referrals list table.
		 *
		 * @since 1.9
		 *
		 * @param array           $row_actions Row actions array.
		 * @param \AffWP\Referral $referral    Current referral.
		 */
		$row_actions = apply_filters( 'affwp_referral_row_actions', $row_actions, $referral );

		return $this->row_actions( $row_actions, true );
	}

	/**
	 * Renders the message to be displayed when there are no referrals.
	 *
	 * @since 1.7.2
	 * @access public
	 */
	public function no_items() {
		_e( 'No referrals found.', 'affiliate-wp' );
	}

	/**
	 * Outputs the reporting views.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param string $which Optional. Whether the bulk actions are being displayed at
	 *                      the top or bottom of the list table. Accepts either 'top'
	 *                      or bottom. Default empty.
	 */
	public function bulk_actions( $which = '' ) {

		if ( is_null( $this->_actions ) ) {
			$no_new_actions = $this->_actions = $this->get_bulk_actions();
			$this->_actions = array_intersect_assoc( $this->_actions, $no_new_actions );
			$two = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) )
			return;

		echo "<select name='action$two'>\n";
		echo "<option value='-1' selected='selected'>" . __( 'Bulk Actions', 'affiliate-wp' ) . "</option>\n";

		foreach ( $this->_actions as $name => $title ) {
			$class = 'edit' == $name ? ' class="hide-if-no-js"' : '';

			echo "\t<option value='$name'$class>$title</option>\n";
		}

		echo "</select>\n";

		/**
		 * Fires at the top and bottom of the referral bulk-actions admin screen
		 * (inside the form element).
		 *
		 * @param string $which Indicator for whether the bulk actions were rendered at the 'top'
		 *                      or 'bottom' of the referrals list table.
		 */
		do_action( 'affwp_referral_bulk_actions', $which );

		submit_button( __( 'Apply', 'affiliate-wp' ), 'action', false, false, array( 'id' => "doaction$two" ) );
		echo "\n";

		// Makes the filters only get output at the top of the page
		if( ! did_action( 'affwp_referral_filters' ) ) {
			$affiliate = isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : false;

			if ( $affiliate && $affiliate = affwp_get_affiliate( $affiliate ) ) {
				$affiliate_name = affwp_get_affiliate_username( $affiliate );
			} else {
				$affiliate_name = '';
			}
			?>
			<span class="affwp-ajax-search-wrap">
				<input type="text" name="affiliate_id" id="user_name" class="affwp-user-search" value="<?php echo esc_attr( $affiliate_name ); ?>" data-affwp-status="any" autocomplete="off" placeholder="<?php _e( 'Affiliate name', 'affiliate-wp' ); ?>" />
			</span>
			<?php
			$from = ! empty( $_REQUEST['filter_from'] ) ? $_REQUEST['filter_from'] : '';
			$to   = ! empty( $_REQUEST['filter_to'] )   ? $_REQUEST['filter_to']   : '';

			echo "<input type='text' class='affwp-datepicker' autocomplete='off' name='filter_from' placeholder='" . __( 'From - mm/dd/yyyy', 'affiliate-wp' ) . "' value='" . esc_attr( $from ) . "'/>";
			echo "<input type='text' class='affwp-datepicker' autocomplete='off' name='filter_to' placeholder='" . __( 'To - mm/dd/yyyy', 'affiliate-wp' ) . "' value='" . esc_attr( $to ) . "'/>&nbsp;";

			/**
			 * Fires in the admin referrals screen, inside the search filters form area, prior to the submit button.
			 */
			do_action( 'affwp_referral_filters' );

			submit_button( __( 'Filter', 'affiliate-wp' ), 'action', false, false );
			echo "\n";

		}
	}

	/**
	 * Retrieves the bulk actions.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return array $actions The array of bulk actions.
	 */
	public function get_bulk_actions() {
		$actions = array(
			'accept'         => __( 'Accept', 'affiliate-wp' ),
			'reject'         => __( 'Reject', 'affiliate-wp' ),
			'mark_as_paid'   => __( 'Mark as Paid', 'affiliate-wp' ),
			'mark_as_unpaid' => __( 'Mark as Unpaid', 'affiliate-wp' ),
			'delete'         => __( 'Delete', 'affiliate-wp' ),
		);

		/**
		 * Filters the bulk actions array for the referrals list table.
		 *
		 * @param array $actions List of bulk actions.
		 */
		return apply_filters( 'affwp_referrals_bulk_actions', $actions );
	}

	/**
	 * Processes bulk actions for the referrals list table.
	 *
	 * @access public
	 * @since  1.0
	 */
	public function process_bulk_action() {

		if( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-referrals' ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'referral-nonce' ) ) {
			return;
		}

		$ids = isset( $_GET['referral_id'] ) ? $_GET['referral_id'] : array();

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$ids    = array_map( 'absint', $ids );
		$action = ! empty( $_REQUEST['action'] ) ? $_REQUEST['action'] : false;

		if( empty( $ids ) || empty( $action ) ) {
			return;
		}

		foreach ( $ids as $id ) {

			if ( 'delete' === $this->current_action() ) {
				affwp_delete_referral( $id );
			}

			if ( 'reject' === $this->current_action() ) {
				affwp_set_referral_status( $id, 'rejected' );
			}

			if ( 'accept' === $this->current_action() ) {
				affwp_set_referral_status( $id, 'unpaid' );
			}

			if ( 'mark_as_paid' === $this->current_action() ) {
				if ( $referral = affwp_get_referral( $id ) ) {
					affwp_add_payout( array(
						'affiliate_id'  => $referral->affiliate_id,
						'referrals'     => $id,
						'payout_method' => 'manual'
					) );
				}
			}

			if ( 'mark_as_unpaid' === $this->current_action() ) {
				affwp_set_referral_status( $id, 'unpaid' );
			}

			/**
			 * Fires after a referral bulk action is performed.
			 *
			 * The dynamic portion of the hook name, `$this->current_action()` refers
			 * to the current bulk action being performed.
			 *
			 * @param int $id The ID of the object.
			 */
			do_action( 'affwp_referrals_do_bulk_action_' . $this->current_action(), $id );
		}

	}

	/**
	 * Retrieves the discount code counts.
	 *
	 * @access public
	 * @since  1.0
	 */
	public function get_referral_counts() {

		$affiliate_id = isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '';

		if( is_array( $affiliate_id ) ) {
			$affiliate_id = array_map( 'absint', $affiliate_id );
		} else {
			$affiliate_id = absint( $affiliate_id );
		}

		$this->paid_count = affiliate_wp()->referrals->count(
			array_merge( $this->query_args, array(
				'affiliate_id' => $affiliate_id,
				'status'       => 'paid'
			) )
		);

		$this->unpaid_count = affiliate_wp()->referrals->count(
			array_merge( $this->query_args, array(
				'affiliate_id' => $affiliate_id,
				'status'       => 'unpaid'
			) )
		);
		$this->pending_count = affiliate_wp()->referrals->count(
			array_merge( $this->query_args, array(
				'affiliate_id' => $affiliate_id,
				'status'       => 'pending'
			) )
		);

		$this->rejected_count = affiliate_wp()->referrals->count(
			array_merge( $this->query_args, array(
				'affiliate_id' => $affiliate_id,
				'status'       => 'rejected'
			) )
		);

		$this->total_count = $this->paid_count + $this->unpaid_count + $this->pending_count + $this->rejected_count;
	}

	/**
	 * Retrieve all the data for all the Affiliates
	 *
	 * @access public
	 * @since 1.0
	 * @return array $referrals_data Array of all the data for the Affiliates
	 */
	public function referrals_data() {

		$page        = isset( $_GET['paged'] )        ? absint( $_GET['paged'] ) : 1;
		$status      = isset( $_GET['status'] )       ? $_GET['status']          : '';
		$affiliate   = isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id']    : '';
		$reference   = isset( $_GET['reference'] )    ? $_GET['reference']       : '';
		$context     = isset( $_GET['context'] )      ? $_GET['context']         : '';
		$campaign    = isset( $_GET['campaign'] )     ? $_GET['campaign']        : '';
		$from        = isset( $_GET['filter_from'] )  ? $_GET['filter_from']     : '';
		$to          = isset( $_GET['filter_to'] )    ? $_GET['filter_to']       : '';
		$order       = isset( $_GET['order'] )        ? $_GET['order']           : 'DESC';
		$orderby     = isset( $_GET['orderby'] )      ? $_GET['orderby']         : 'referral_id';
		$referral    = '';
		$description = '';
		$is_search   = false;

		$amount = isset( $_GET['amount'] ) ? sanitize_text_field( $_GET['amount'] ) : 0;

		if ( $affiliate && $affiliate = affwp_get_affiliate( $affiliate ) ) {
			$affiliate = $affiliate->ID;
		} else {
			// Switch back to empty for the benefit of get_referrals().
			$affiliate = '';
		}

		$date = array();
		if( ! empty( $from ) ) {
			$date['start'] = $from;
		}
		if( ! empty( $to ) ) {
			$date['end']   = $to . ' 23:59:59';;
		}

		if( ! empty( $_GET['s'] ) ) {

			$is_search = true;

			$search = sanitize_text_field( $_GET['s'] );

			if( is_numeric( $search ) ) {
				// This is an referral ID search
				$referral = absint( $search );
			} elseif ( strpos( $search, 'ref:' ) !== false ) {
				$reference = trim( str_replace( 'ref:', '', $search ) );
			} elseif ( strpos( $search, 'context:' ) !== false ) {
				$context = trim( str_replace( 'context:', '', $search ) );
			} elseif ( strpos( $search, 'affiliate:' ) !== false ) {
				$affiliate = absint( trim( str_replace( 'affiliate:', '', $search ) ) );
			} elseif ( strpos( $search, 'campaign:' ) !== false ) {
				$campaign = trim( str_replace( 'campaign:', '', $search ) );
			} elseif ( strpos( $search, 'amount:' ) !== false ) {
				$amount = trim( str_replace( 'amount:', '', $search ) );
			} elseif ( strpos( $search, 'desc:' ) !== false ) {
				$description = trim( str_replace( 'desc:', '', $search ) );
			}

		}

		$per_page = $this->get_items_per_page( 'affwp_edit_referrals_per_page', $this->per_page );

		$args = wp_parse_args( $this->query_args, array(
			'number'       => $per_page,
			'offset'       => $per_page * ( $page - 1 ),
			'status'       => $status,
			'referral_id'  => $referral,
			'affiliate_id' => $affiliate,
			'reference'    => $reference,
			'context'      => $context,
			'campaign'     => $campaign,
			'amount'       => $amount,
			'description'  => $description,
			'date'         => $date,
			'search'       => $is_search,
			'orderby'      => sanitize_text_field( $orderby ),
			'order'        => sanitize_text_field( $order )
		) );

		$referrals = affiliate_wp()->referrals->get_referrals( $args );

		// Retrieve the "current" total count for pagination purposes.
		$args['number']      = -1;
		$this->current_count = affiliate_wp()->referrals->count( $args );

		return $referrals;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @since 1.0
	 * @uses AffWP_Referrals_Table::get_columns()
	 * @uses AffWP_Referrals_Table::get_sortable_columns()
	 * @uses AffWP_Referrals_Table::process_bulk_action()
	 * @uses AffWP_Referrals_Table::referrals_data()
	 * @uses WP_List_Table::get_pagenum()
	 * @uses WP_List_Table::set_pagination_args()
	 * @return void
	 */
	public function prepare_items() {
		$per_page = $this->get_items_per_page( 'affwp_edit_referrals_per_page', $this->per_page );

		$this->get_column_info();

		$this->process_bulk_action();

		$data = $this->referrals_data();

		$current_page = $this->get_pagenum();

		$status = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

		switch( $status ) {
			case 'paid':
				$total_items = $this->paid_count;
				break;
			case 'pending':
				$total_items = $this->pending_count;
				break;
			case 'unpaid':
				$total_items = $this->unpaid_count;
				break;
			case 'rejected':
				$total_items = $this->rejected_count;
				break;
			case 'any':
				$total_items = $this->current_count;
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
