<?php
namespace AffWP\Admin\Reports;

/**
 * Core abstract class extended to implement Reports screen tabs.
 *
 * @since 1.9
 * @abstract
 */
abstract class Tab {

	/**
	 * Tab ID.
	 *
	 * Used when registering the tab element and hooking the display callback.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $tab_id = '';

	/**
	 * Tab label.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $label = '';

	/**
	 * Priority to register the tab.
	 *
	 * @access public
	 * @since  1.9
	 * @var    int
	 */
	public $priority = 0;

	/**
	 * Reports tiles.
	 *
	 * @access private
	 * @since  1.9
	 * @var    array
	 */
	private $tiles = array();

	/**
	 * Graph instance.
	 *
	 * @access public
	 * @since  1.9
	 * @var    \Affiliate_WP_Graph
	 */
	public $graph;

	/**
	 * Reports date filter values.
	 *
	 * @access public
	 * @since  1.9
	 * @var    array
	 */
	public $dates = array();

	/**
	 * Date query start and end values for filtering data.
	 *
	 * @access public
	 * @since  1.9
	 * @var    array
	 */
	public $date_query = array();

	/**
	 * Affiliate to filter for (if set).
	 *
	 * @access public
	 * @since  2.1
	 * @var    int
	 */
	public $affiliate_id = 0;

	/**
	 * Registry instance.
	 *
	 * @access public
	 * @since  2.1
	 * @var    \AffWP\Admin\Reports\Registry
	 */
	public $registry;

	/**
	 * Sets up Reports tabs.
	 *
	 * @access private
	 * @since  1.9
	 */
	public function __construct() {
		$this->registry = new Registry;

		// Deliberately hooked with an anonymous function. Use the 'affwp_reports_tabs' hook to remove tabs.
		$inst = $this;
		add_filter( 'affwp_reports_tabs', function( $tabs ) use ( $inst ) {
			$tabs[ $inst->tab_id ] = $inst->label;
			return $tabs;
		} );

		$this->dates      = affwp_get_report_dates();
		$this->date_query = array(
			'start' => $this->dates['year'] . '-' . $this->dates['m_start'] . '-' . $this->dates['day'] . ' 00:00:00',
			'end'   => $this->dates['year_end'] . '-' . $this->dates['m_end'] . '-' . $this->dates['day_end'] . ' 23:59:59',
		);

		add_action( "affwp_reports_{$this->tab_id}_nav",        array( $this->graph, 'graph_controls' ), 0 );
		add_action( "affwp_reports_tab_{$this->tab_id}",        array( $this, 'display'               )    );
		add_action( "affwp_reports_tab_{$this->tab_id}_trends", array( $this, 'display_trends'        )    );
	}

	/**
	 * Displays the tab contents.
	 *
	 * Can be overridden in extending sub-classes. Hooked to {@see 'affwp_reports_tab_$tab'}.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function display() {

		$this->set_up_tiles();

		if ( has_action( "affwp_reports_{$this->tab_id}_nav" ) ) : ?>
			<h3><?php _e( 'Filters', 'affiliate-wp' ); ?></h3>
			<div id="reports-nav">
				<form id="affwp-graphs-filter" method="get">
					<div class="tablenav top">
						<?php
						/**
						 * Fires inside the inner Reports nav in the given tab.
						 *
						 * The dynamic portion of the hook name, `$this->tab_id`, refers to the ID of the current tab.
						 *
						 * @since 1.9
						 *
						 * @param \AffWP\Admin\Reports\Tab $this Tab instance.
						 */
						do_action( "affwp_reports_{$this->tab_id}_nav", $this );
						?>
						<?php submit_button( __( 'Filter', 'affiliate-wp' ), 'secondary', 'submit', false ); ?>
					</div>
				</form>
			</div>
		<?php endif; ?>

		<?php
		/**
		 * Fires at the top of the given reports tab page.
		 *
		 * The dynamic portion of the hook name, `$this->tab_id`, refers to the tab ID.
		 * Fires after the Filters section at the top of the reports tab has already
		 * been rendered.
		 *
		 * @since 2.1
		 *
		 * @param \AffWP\Admin\Reports\Tab $this Tab instance.
		 */
		do_action( "affwp_reports_{$this->tab_id}_top", $this );
		?>

		<h3><?php _e( 'Quick Stats', 'affiliate-wp' ); ?></h3>

		<?php
		/**
		 * Fires before the given Reports tab meta boxes are rendered.
		 *
		 * Use this hook to register standalone meta boxes against. See set_up_tiles() for core usage.
		 *
		 * @since 1.9
		 *
		 * @param \AffWP\Admin\Reports\Tab $this Tab instance.
		 */
		do_action( "affwp_reports_{$this->tab_id}_meta_boxes", $this );
		?>

		<div id="affwp-reports-widgets-wrap">

			<div id="dashboard-widgets" class="metabox-holder">

				<div class="postbox-container">
					<?php do_meta_boxes( 'affiliates_page_affiliate-wp-reports', 'primary', null ); ?>
				</div>

				<div class="postbox-container">
					<?php do_meta_boxes( 'affiliates_page_affiliate-wp-reports', 'secondary', null ); ?>
				</div>

				<div class="postbox-container">
					<?php do_meta_boxes( 'affiliates_page_affiliate-wp-reports', 'tertiary', null ); ?>
				</div>

			</div>

		</div>

		<h3><?php _e( 'Trends', 'affiliate-wp' ); ?></h3>

		<div class="reports-graph">
			<?php
			/**
			 * Fires inside the 'Trends' section of the given reports tab.
			 *
			 * The dynamic portion of the hook name, `$this->tab_id`, refers to the tab ID.
			 *
			 * @since 1.9
			 *
			 * @param \AffWP\Admin\Reports\Tab $this Tab instance.
			 */
			do_action( "affwp_reports_tab_{$this->tab_id}_trends", $this );
			?>
		</div>
		<?php
		/**
		 * Fires at the bottom of the given reports tab.
		 *
		 * The dynamic portion of the hook name, `$this->tab_id`, refers to the tab ID.
		 *
		 * @since 2.1
		 *
		 * @param \AffWP\Admin\Reports\Tab $this Tab instance.
		 */
		do_action( "affwp_reports_tab_{$this->tab_id}_bottom", $this );
	}

	/**
	 * Handles display for the 'Trends' section.
	 *
	 * Must be overridden by extending sub-classes.
	 *
	 * @access public
	 * @since  1.9
	 * @abstract
	 */
	abstract public function display_trends();

	/**
	 * Used to register tiles for the current Reports tab.
	 *
	 * Must be defined by extending sub-classes.
	 *
	 * @access public
	 * @since  1.9
	 * @abstract
	 */
	abstract public function register_tiles();

	/**
	 * Registers a new tile for display in the tab.
	 *
	 * Uses the core meta box API.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param int      $tile_id  Tile ID.
	 * @param array    $args {
	 *     Optional. Arguments for registering a new Reports tile tied to the tab ID.
	 *
	 *     @type string   $label            Tile label. Default 'Meta Box'.
	 *     @type string   $context          Tile context. Maps to the corresponding meta box `$context` value.
	 *                                      Accepts 'primary', 'secondary', and 'tertiary'. Default 'primary'.
	 *     @type string   $type             Tile type (used for formatting purposes). Accepts 'number', 'amount',
	 *                                      'rate', or empty. Default 'number'.
	 *     @type mixed    $data             The data value to supply to the tile. Default empty.
	 *     @type mixed    $comparison_data  Comparison data to pair with `$data`. Default empty.
	 *     @type callable $display_callback Display callback to use for the tile. Default is 'default_tile',
	 *                                      which leverages `$type`.
	 * }
	 */
	public function register_tile( $tile_id, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'label'             => 'Meta Box',
			'context'           => 'primary',
			'type'              => '',
			'data'              => '',
			'comparison_data'   => '',
			'display_callback'  => array( $this, 'default_tile' )
		) );

		$this->registry->add_tile( $this->tab_id, $tile_id, $args );
	}

	/**
	 * Unregisters a tile.
	 *
	 * @access protected
	 * @since  1.9
	 *
	 * @param string $tile_id ID for the tile to unregister.
	 */
	protected function unregister_tile( $tile_id ) {
		$this->registry->remove_tile( $this->tab_id, $tile_id );
	}

	/**
	 * Sets up tiles by registering them as standalone meta boxes.
	 *
	 * @access private
	 * @since  1.9
	 */
	private function set_up_tiles() {
		if ( ! class_exists( 'AffWP\Admin\Meta_Box' ) ) {
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/class-meta-box-base.php';
		}

		$this->register_tiles();

		/**
		 * Fires immediately after a given tab's tiles have been registered, but before their
		 * corresponding meta boxes are registered.
		 *
		 * The dynamic portion of the hook name, `$this->tab_id`, refers to the ID of the current tab.
		 *
		 * @since 1.9
		 *
		 * @param object $this Tab instance.
		 */
		do_action( "affwp_reports_{$this->tab_id}_register_tiles", $this );

		foreach ( $this->get_tiles() as $tile_id => $atts ) {
			$args = array(
				'meta_box_id'      => "{$this->tab_id}-{$tile_id}",
				'meta_box_name'    => empty( $atts['label'] ) ? $this->label : $atts['label'],
				'context'          => $atts['context'],
				'action'           => "affwp_reports_{$this->tab_id}_meta_boxes",
				'display_callback' => $atts['display_callback'],
				'extra_args'       => $atts,
			);

			new \AffWP\Admin\Meta_Box( $args );
		}
	}

	/**
	 * Retrieves the list of tiles for the current tab ID.
	 *
	 * @access protected
	 * @since  1.9
	 *
	 * @return array Filterable list of tiles for the current tab.
	 */
	protected function get_tiles() {
		/**
		 * Filters the Reports tiles for a given tab.
		 *
		 * The dynamic portion of the hook name, `$this->tab_id` refers to the tab ID.
		 *
		 * @since 1.9
		 *
		 * @param array                    $tiles Registered tiles.
		 * @param \AffWP\Admin\Reports\Tab $this  Tab instance.
		 */
		 return (array) apply_filters( "affwp_reports_{$this->tab_id}_tiles", $this->registry->get_tiles( $this->tab_id ), $this );
	}

	/**
	 * Handles default display of a tile.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param array $tile Tile data.
	 */
	public function default_tile( $tile ) {
		if ( ! empty( $tile['display_callback'] ) && array( $this, 'default_tile' ) !== $tile['display_callback'] ) {
			call_user_func( $tile['display_callback'], $tile );
		} else {

			if ( empty( $tile['data'] ) ) {
				echo '<span class="tile-no-data tile-value">' . __( 'No data for the current date range.', 'affiliate-wp' ) . '</span>';
			} else {
				switch( $tile['type'] ) {
					case 'number':
						echo '<span class="tile-number tile-value">' . affwp_format_amount( $tile['data'], false ) . '</span>';
						break;

					case 'split-number':
						printf( '<span class="tile-amount tile-value">%1$d / %2$d</span>',
							affwp_format_amount( $tile['data']['first_value'] ),
							affwp_format_amount( $tile['data']['second_value'] )
						);
						break;

					case 'amount':
						echo '<span class="tile-amount tile-value">' . affwp_currency_filter( affwp_format_amount( $tile['data'] ) ) . '</span>';
						break;

					case 'rate':
						echo '<span class="tile-rate tile-value">' . affwp_format_rate( $tile['data'] ) . '</span>';
						break;

					case 'url':
						echo '<span class="tile-url tile-value">' . $tile['data'] . '</span>';
						break;

					default:
						echo '<span class="tile-value">' . $tile['data'] . '</span>';
						break;
				}
			}

			if ( ! empty( $tile['comparison_data'] ) ) {
				echo '<span class="tile-compare">' . $tile['comparison_data'] . '</span>';
			}
		}
	}

	/**
	 * Retrieves the comparison data string for date ranges.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param false|string $override Optional. Value to override the date filter string with. Accepts false
	 *                               (disabled), or a string. Default false.
	 * @return string Date range string for the current date filter.
	 */
	public function get_date_comparison_label( $override = false ) {
		$label = '';

		$string_ranges = array(
			'today'        => __( 'Today', 'affiliate-wp' ),
			'yesterday'    => __( 'Yesterday', 'affiliate-wp' ),
			'this_week'    => __( 'This Week', 'affiliate-wp' ),
			'last_week'    => __( 'Last Week', 'affiliate-wp' ),
			'this_month'   => __( 'This Month', 'affiliate-wp' ),
			'last_month'   => __( 'Last Month', 'affiliate-wp' ),
			'this_quarter' => __( 'This Quarter', 'affiliate-wp' ),
			'last_quarter' => __( 'Last Quarter', 'affiliate-wp' ),
			'this_year'    => __( 'This Year', 'affiliate-wp' ),
			'last_year'    => __( 'Last Year', 'affiliate-wp' ),
		);

		if ( array_key_exists( $this->dates['range'], $string_ranges ) ) {
			$label = $string_ranges[ $this->dates['range'] ];
		} elseif ( 'other' === $this->dates['range'] ) {
			if ( false !== $override ) {
				$label = (string) $override;
			} else {
				/* translators: 1: Starting date, 2: Ending date */
				$label = sprintf( __( '%1$s to %2$s', 'affiliate-wp' ),
					$this->dates['date_from'],
					$this->dates['date_to']
				);
			}
		}
		return $label;
	}

	/**
	 * Sets up additional graph filters for the Affiliates tab in Reports.
	 *
	 * @access public
	 * @since  2.1
	 */
	public function set_up_additional_filters() {

		// Retrieve the affiliate ID if the filter is set.
		if ( ! empty( $_GET['affiliate_login'] ) ) {
			$username = sanitize_text_field( $_GET['affiliate_login'] );

			if ( $affiliate = affwp_get_affiliate( $username ) ) {
				$this->affiliate_id = $affiliate->ID;
			}
		}

		// Allow extra filters to be added by letting the Tab class render the form wrapper itself.
		$this->graph->set( 'form_wrapper', false );

		// Register the single affiliate filter.
		add_action( "affwp_reports_{$this->tab_id}_nav", array( $this, 'affiliate_filter' ), 10 );
	}

	/**
	 * Adds a single affiliate filter field to the Affiliates tab in Reports.
	 *
	 * @since 2.1
	 */
	public function affiliate_filter() {
		$affiliate_login = ! empty( $_GET['affiliate_login'] ) ? sanitize_text_field( $_GET['affiliate_login'] ) : '';

		// Only keep the currently-filtered affiliate if it's valid.
		if ( false === affwp_get_affiliate( $affiliate_login ) ) {
			$affiliate_login = '';
		}
		?>
		<span class="affwp-ajax-search-wrap">
			<input type="text" name="affiliate_login" id="user_name" class="affwp-user-search" value="<?php echo esc_attr( $affiliate_login ); ?>" data-affwp-status="any" autocomplete="off" placeholder="<?php _e( 'Affiliate name', 'affiliate-wp' ); ?>" />
		</span>
		<?php
	}

}
