<?php
namespace AffWP\Admin;

// Load WP_List_Table if not loaded
if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List Table base class for use in the admin.
 *
 * @since 1.9
 *
 * @see \WP_List_Table
 */
abstract class List_Table extends \WP_List_Table {

	/**
	 * Optional arguments to pass when preparing items.
	 *
	 * @access public
	 * @since  1.9
	 * @var    array
	 */
	public $query_args = array();

	/**
	 * Optional arguments to pass when preparing items for display.
	 *
	 * @access public
	 * @since  1.9
	 * @var    array
	 */
	public $display_args = array();

	/**
	 * Current screen object.
	 *
	 * @access public
	 * @since  1.9
	 * @var    \WP_Screen
	 */
	public $screen;

	/**
	 * Total item count for the current query
	 *
	 * Used for the pagination controls with non-status filtered results.
	 *
	 * @access public
	 * @since  2.1
	 * @var    int
	 */
	public $current_count;

	/**
	 * Sets up the list table instance.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @see WP_List_Table::__construct()
	 *
	 * @param array $args {
	 *     Optional. Arbitrary display and query arguments to pass through to the list table.
	 *     Default empty array.
	 *
	 *     @type string $singular    Singular version of the list table item.
	 *     @type string $plural      Plural version of the list table item.
	 *     @type array $query_args   Optional. Arguments to pass through to the query used for preparing items.
	 *                               Accepts any valid arguments accepted by the given query methods.
	 *     @type array $display_args {
	 *         Optional. Arguments to pass through for use when displaying queried items.
	 *
	 *         @type string $pre_table_callback   Callback to fire at the top of the list table, just before the list
	 *                                            table navigation is displayed. Default empty (disabled).
	 *         @type bool   $hide_table_nav       Whether to hide the entire table navigation at the top and bottom
	 *                                            of the list table. Will hide the bulk actions, extra tablenav, and
	 *                                            pagination. Use `$hide_bulk_options`, or `$hide_pagination` for more
	 *                                            fine-grained control. Default false.
	 *         @type bool   $hide_bulk_options    Whether to hide the bulk options controls at the top and bottom of
	 *                                            the list table. Default false.
	 *         @type array  $hide_pagination      Whether to hide the pagination controls at the top and bottom of the
	 *                                            list table. Default false.
	 *         @type bool   $columns_to_hide      An array of column IDs to hide for the current instance of the list
	 *                                            table. Note: other columns may be already hidden depending on current
	 *                                            user settings determined by screen options column controls. Default
	 *                                            empty array.
	 *         @type bool   $hide_column_controls Whether to hide the screen options column controls for the list table.
	 *                                            This should always be enabled when instantiating a standalone list
	 *                                            table in sub-views such as view_affiliate or view_payout due to
	 *                                            conflicts introduced in column controls generated for list tables
	 *                                            instantiated at the primary-view level. Default false.
	 *     }
	 * }
	 */
	public function __construct( $args = array() ) {
		$this->screen = get_current_screen();

		$display_args = array(
			'pre_table_callback'   => '',
			'hide_table_nav'       => false,
			'hide_bulk_options'    => false,
			'hide_pagination'      => false,
			'columns_to_hide'      => array(),
			'hide_column_controls' => false,
		);

		if ( ! empty( $args['query_args'] ) ) {
			$this->query_args = $args['query_args'];

			unset( $args['query_args'] );
		}

		if ( ! empty( $args['display_args'] ) ) {
			$this->display_args = wp_parse_args( $args['display_args'], $display_args );

			unset( $args['display_args'] );
		} else {
			$this->display_args = $display_args;
		}

		$args = wp_parse_args( $args, array(
			'ajax' => false,
		) );

		parent::__construct( $args );
	}

	/**
	 * Builds and retrieves the HTML markup for a row action link.
	 *
	 * @access public
	 * @since  1.9
	 * @since  1.9.3 Added an optional 'base_uri' argument to `$args` for use when adding query args.
	 *
	 * @param string $label      Row action link label.
	 * @param array  $query_args Query arguments.
	 * @param array  $args {
	 *     Optional. Additional arguments for building a row action link.
	 *
	 *     @type false|string $nonce    Whether to nonce the URL. Accepts false (disabled) or a nonce name
	 *                                  to use. Default false.
	 *     @type string       $class    Class attribute value for the link.
	 *     @type string       $base_uri Base URI to add query args to. Default is the current screen.
	 *
	 * }
	 * @return string Row action link markup.
	 */
	public function get_row_action_link( $label, $query_args, $args = array() ) {

		$base_uri = empty( $args['base_uri'] ) ? false : $args['base_uri'];

		if ( empty( $args['nonce'] ) ) {
			$url = esc_url( add_query_arg( $query_args, $base_uri ) );
		} else {
			$url = wp_nonce_url( add_query_arg( $query_args, $base_uri ), $args['nonce'] );
		}

		$class = empty( $args['class'] ) ? '' : sprintf( ' class="%s"', esc_attr( $args['class'] ) );

		return sprintf( '<a href="%1$s"%2$s>%3$s</a>', $url, $class, esc_html( $label ) );
	}

	/**
	 * Generates the table navigation above or below the table.
	 *
	 * @access protected
	 * @since  1.9
	 *
	 * @param string $which Which location the builk actions are being rendered for.
	 *                      Will be 'top' or 'bottom'.
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}

		if ( ! empty( $this->display_args['pre_table_callback'] )
			&& is_callable( $this->display_args['pre_table_callback'] )
			&& 'top' === $which
		) {

			echo call_user_func( $this->display_args['pre_table_callback'] );
		}

		if ( true !== $this->display_args['hide_table_nav'] ) : ?>
			<div class="tablenav <?php echo esc_attr( $which ); ?>">

				<?php if ( $this->has_items() && true !== $this->display_args['hide_bulk_options'] ) : ?>
					<div class="alignleft actions bulkactions">
						<?php $this->bulk_actions( $which ); ?>
					</div>
				<?php endif;
				$this->extra_tablenav( $which );

				if ( true !== $this->display_args['hide_pagination'] ) :
					$this->pagination( $which );
				endif;
				?>

				<br class="clear" />
			</div>
		<?php endif;
	}

	/**
	 * Prepares columns for display.
	 *
	 * Applies display arguments passed in the constructor to the list of columns.
	 *
	 * @access protected
	 * @since  1.9
	 *
	 * @param array $columns List of columns.
	 * @return array (Maybe) filtered list of columns.
	 */
	public function prepare_columns( $columns ) {
		if ( ! empty( $this->display_args['columns_to_hide'] ) ) {
			$columns_to_hide = $this->display_args['columns_to_hide'];

			foreach ( $columns_to_hide as $column ) {
				if ( array_key_exists( $column, $columns ) ) {
					unset( $columns[ $column ] );
				}
			}
		}
		return $columns;
	}

	/**
	 * Retrieves a list of all, hidden,sortable, and primary columns, with filters applied.
	 *
	 * Also sets up column show/hide controls.
	 *
	 * @access protected
	 * @since  1.9
	 *
	 * @return array Column headers.
	 */
	protected function get_column_info() {
		if ( true === $this->display_args['hide_column_controls'] ) {
			$columns = $this->get_columns();

			$hidden = array();

			$sortable = $this->get_sortable_columns();

			$this->_column_headers = array( $columns, $hidden, $sortable, $this->get_primary_column_name() );
		} else {
			$this->_column_headers = parent::get_column_info();
		}

		return $this->_column_headers;
	}
}
