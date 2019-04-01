<?php
namespace AffWP\REST\Admin;

use AffWP\Admin\List_Table;

if ( ! class_exists( 'AffWP\Admin\List_Table' ) ) {
	require_once AFFILIATEWP_PLUGIN_DIR . 'includes/abstracts/class-affwp-list-table.php';
}

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Core class used to display a list table of API records.
 *
 * @since 1.9
 */
class Consumers_Table extends List_Table  {

	/**
	 * Number of records to list per page.
	 *
	 * @access public
	 * @since  1.9
	 * @var    int
	 */
	public $per_page = 30;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @see WP_List_Table::__construct()
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through
	 *                    the list table. Default empty array.
	 */
	public function __construct( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'singular'     => __( 'API Key', 'affiliate-wp' ),
			'plural'       => __( 'API Keys', 'affiliate-wp' ),
			'display_args' => array(
				'hide_column_controls' => true
			)
		) );

		parent::__construct( $args );

		$this->query();
	}

	/**
	 * Message to be displayed when there are no consumers.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function no_items() {
		_e( 'No API consumers found.', 'affiliate-wp' );
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 2.5
	 * @access protected
	 *
	 * @return string Name of the primary column.
	 */
	protected function get_primary_column_name() {
		return 'username';
	}

	/**
	 * Renders most of the columns in the list table.
	 *
	 * @access  public
	 * @since   1.9
	 *
	 * @param array $item Contains all the data of the keys
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Renders the 'Public Key' column.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param \AffWP\REST\Consumer $item Current REST consumer.
	 * @return string Display information for the public key.
	 */
	public function column_public_key( $item ) {
		return '<input readonly="readonly" type="text" class="large-text" value="' . esc_attr( $item->public_key ) . '"/>';
	}

	/**
	 * Renders the 'Token' column.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param \AffWP\REST\Consumer $item Current REST consumer.
	 * @return string Display information for the token.
	 */
	public function column_token( $item ) {
		return '<input readonly="readonly" type="text" class="large-text" value="' . esc_attr( $item->token ) . '"/>';
	}

	/**
	 * Renders the 'Secret Key' column.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param \AffWP\REST\Consumer $item Current REST consumer.
	 * @return string Display information for the secret key.
	 */
	public function column_secret_key( $item ) {
		return '<input readonly="readonly" type="text" class="large-text" value="' . esc_attr( $item->secret_key ) . '"/>';
	}

	/**
	 * Renders the 'username' column.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @see row_actions()
	 *
	 * @param \AffWP\REST\Consumer $item Current REST consumer.
	 * @return string Display information for the user.
	 */
	public function column_username( $item ) {

		$row_actions = array();

		$base_query_args = array(
			'user_id'      => $item->user_id,
			'affwp_action' => 'process_api_key'
		);

		$row_actions['reissue'] = $this->get_row_action_link(
			__( 'Reissue', 'affiliate-wp' ),
			array_merge( $base_query_args, array(
				'affwp_api_process' => 'regenerate'
			) ),
			array(
				'nonce' => 'affwp-api-nonce',
				'class' => 'affwp-regenerate-api-key',
			)
		);

		$row_actions['revoke'] = $this->get_row_action_link(
			__( 'Revoke', 'affiliate-wp' ),
			array_merge( $base_query_args, array(
				'affwp_api_process' => 'revoke'
			) ),
			array(
				'nonce' => 'affwp-api-nonce',
				'class' => 'affwp-revoke-api-key affwp-delete',
			)
		);

		/**
		 * Filters the row actions for a given consumer.
		 *
		 * @since 1.9
		 *
		 * @param array                $row_actions Consumer row actions.
		 * @param \AffWP\REST\Consumer $item        Current REST consumer.
		 */
		$actions = apply_filters( 'affwp_api_row_actions', $row_actions, $item );

		$username = sprintf( '<a href="%1$s"><strong>%2$s</strong></a>',
			esc_url( add_query_arg( 'user_id', $item->user_id, admin_url( 'user-edit.php' ) ) ),
			affiliate_wp()->REST->consumers->get_consumer_username( $item->user_id )
		);

		return sprintf('%1$s %2$s', $username, $this->row_actions( $row_actions ) );
	}

	/**
	 * Retrieves the consumers table columns.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @return array $columns Array of all the consumer table columns.
	 */
	public function get_columns() {
		$columns = array(
			'username'   => __( 'Username', 'affiliate-wp' ),
			'public_key' => __( 'Public Key', 'affiliate-wp' ),
			'token'      => __( 'Token', 'affiliate-wp' ),
			'secret_key' => __( 'Secret Key', 'affiliate-wp' ),
		);

		return $this->prepare_columns( $columns );
	}

	/**
	 * Displays the key generation form.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param string $which Optional. Which location the builk actions are being rendered for.
	 *                      Will be 'top' or 'bottom'. Default empty.
	 */
	public function bulk_actions( $which = '' ) {
		// These aren't really bulk actions but this outputs the markup in the right place.
		if ( 'top' === $which ) :
			$action_url = affwp_admin_url( 'tools', array( 'tab' => 'api_keys' ) );
			?>
			<form id="api-key-generate-form" method="post" action="<?php echo esc_url( $action_url ); ?>">
				<input type="hidden" name="affwp_action" value="process_api_key" />
				<input type="hidden" name="affwp_api_process" value="generate" />
				<?php wp_nonce_field( 'affwp-api-nonce' ); ?>
				<span class="affwp-ajax-search-wrap">
					<input type="text" name="user_name" id="user_name" class="affwp-user-search" data-affwp-status="any" autocomplete="off" placeholder="<?php esc_attr_e( 'Enter username', 'affiliate-wp' ); ?>" />
				</span>
				<?php submit_button( __( 'Generate New API Keys', 'affiliate-wp' ), 'secondary', 'submit', false ); ?>
			</form>
		<?php endif;
	}

	/**
	 * Generates the table navigation above and below the table.
	 *
	 * @access protected
	 * @since  1.9
	 *
	 * @param string $which Which location the builk actions are being rendered for. Will be 'top'
	 *                      or 'bottom'.
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">

			<div class="alignleft actions bulkactions">
				<?php $this->bulk_actions( $which ); ?>
			</div>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>

			<br class="clear" />
		</div>
		<?php
	}

	/**
	 * Retrieves the current page number.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @return int Current page number.
	 */
	public function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Performs the key query for consumers.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @return array Array of consumer objects.
	 */
	public function consumers_data() {

		$order    = isset( $_GET['order'] )   ? $_GET['order']           : 'DESC';
		$orderby  = isset( $_GET['orderby'] ) ? $_GET['orderby']         : 'consumer_id';
		$per_page = $this->get_items_per_page( 'affwp_edit_affiliates_per_page', $this->per_page );
		$offset   = $per_page * ( $this->get_paged() - 1 );

		$consumers = affiliate_wp()->REST->consumers->get_consumers( array(
			'number'  => $per_page,
			'offset'  => $offset,
			'orderby' => $orderby,
			'order'   => $order
		) );

		return $consumers;
	}



	/**
	 * Retrieves the total consumers count.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return int Total consumers count.
	 */
	public function total_items() {
		return affiliate_wp()->REST->consumers->count();
	}

	/**
	 * Sets up the final data for the table.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @see consumers_data()
	 */
	public function prepare_items() {
		$this->get_column_info();

		$data = $this->consumers_data();

		$total_items = $this->total_items();

		$this->items = $data;

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $this->per_page,
				'total_pages' => ceil( $total_items / $this->per_page ),
			)
		);
	}
}
