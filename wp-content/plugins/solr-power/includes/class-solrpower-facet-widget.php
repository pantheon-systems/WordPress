<?php
/**
 * Widget for displaying search faceting UI.
 *
 * @package Solr_Power
 */

/**
 * Widget for displaying search faceting UI.
 */
class SolrPower_Facet_Widget extends WP_Widget {

	/**
	 * Facets sent to WP_Query.
	 *
	 * @var array
	 */
	public $facets = array();

	/**
	 * Instantiate the widget object.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'solrpower_facet_widget',
			'description' => 'Facet your search results.',
		);
		parent::__construct( 'solrpower_facet_widget', 'Solr Search', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args     Arguments defined by the user.
	 * @param array $instance Instantiated widget instance.
	 */
	public function widget( $args, $instance ) {
		$this->dummy_query();
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		$this->facets = filter_input( INPUT_GET, 'facet', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
		echo '<form action="' . esc_url( home_url( '/' ) ) . '" method="get" id="solr_facet">';
		$this->render_searchbox();
		echo '<div id="solr_facets">';
		$this->fetch_facets();
		echo '</div>';
		echo '</form>';
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : 'Search';
		?>
		<p>
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'solr-for-wordpress-on-pantheon' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

	/**
	 * Fetches and displays returned facets.
	 *
	 * @param bool $echo Whether or not the facets should be echoed.
	 * @return bool|string
	 */
	function fetch_facets( $echo = true ) {
		$solr_options = solr_options();
		if ( ! $solr_options['s4wp_output_facets'] ) {
			return false;
		}

		$facets       = SolrPower_WP_Query::get_instance()->facets;
		$this->facets = $facets;
		$sent_facets  = filter_input( INPUT_GET, 'facet', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );

		$output = ''; // HTML Output.

		foreach ( $facets as $facet_name => $data ) {
			// Strict comparisons are used so this needs to be a string.
			$facet_name = strval( $facet_name );

			if ( false === $this->show_facet( $facet_name ) ) {
				continue;
			}
			/**
			 * Filter facet HTML
			 *
			 * Filter the HTML output of a facet.
			 *
			 * @param string $facet_name The facet name.
			 * @param object $data       The Solarium facet data object.
			 */
			$html = apply_filters( 'solr_facet_items', false, $facet_name, $data );
			if ( false !== $html ) {
				$output .= $html;
				continue;
			}

			/**
			 * Filter facet title
			 *
			 * Filter the facet title displayed in the widget.
			 *
			 * @param boolean|string $ret        The custom facet title, defaults to false.
			 * @param string         $facet_name The facet name.
			 */
			$facet_nice_name = apply_filters( 'solr_facet_title', false, $facet_name );
			if ( false === $facet_nice_name ) {
				$replace         = array( '/\_taxonomy/', '/\_str/', '/\_/' );
				$facet_nice_name = preg_replace( $replace, ' ', $facet_name );
			}
			$values = $data->getValues();
			if ( 0 === count( $values ) ) {
				continue;
			}
			$output .= '<h2>' . esc_html( $facet_nice_name ) . '</h2>';
			$output .= '<ul id="fn_' . esc_attr( md5( $facet_name ) ) . '">';

			$facets_facet_name = array();
			if ( isset( $this->facets[ $facet_name ] ) ) {
				$facets_facet_name = $this->facets[ $facet_name ]->getValues();
				if ( is_array( $this->facets[ $facet_name ] ) ) {
					// Decode special characters of facet and store in temporary array.
					$facets_facet_name = array_map(
						array(
							__CLASS__,
							'htmlspecialchars_decode',
						),
						$this->facets[ $facet_name ]
					);
				}
			}

			foreach ( $values as $name => $count ) :

				$nice_name = str_replace( '^^', '', $name );
				$checked   = '';
				$name      = strval( $name );

				if ( (
						isset( $this->facets[ $facet_name ] ) &&
						in_array( htmlspecialchars_decode( $name ), $facets_facet_name, true )
					) || (
						isset( $sent_facets[ $facet_name ] ) &&
						in_array( $name, $sent_facets[ $facet_name ], true )
					)
				) {
					$checked = checked( true, true, false );
				}
				$facet_id = 'f_' . md5( $facet_name . $name );
				$output  .= '<li>';
				$output  .= '<input type="checkbox" name="facet[' . esc_attr( $facet_name ) . '][]" value="' . esc_attr( $name ) . '" ' . $checked . ' class="facet_check" id="' . $facet_id . '"> ';
				if ( $solr_options['allow_ajax'] ) {
					$output .= '<a href="#" class="facet_link" data-for="' . $facet_id . '">';
				}
				$output .= esc_html( $nice_name );
				$output .= ' (' . esc_html( $count ) . ')';
				if ( $solr_options['allow_ajax'] ) {
					$output .= '</a>';
				}
				$output .= '</li>';
			endforeach;

			$output .= '<a href="' . $this->reset_url( $facet_name ) . '" class="solr_reset" data-for="fn_' . esc_attr( md5( $facet_name ) ) . '">Reset</a>';
			$output .= '</ul>';

		} // End foreach().
		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}

	}

	/**
	 * Reset link below facet list.
	 *
	 * @param string $facet_name Name of the facet.
	 * @return string|boolean
	 */
	public function reset_url( $facet_name ) {
		$solr_options = solr_options();
		if ( $solr_options['allow_ajax'] ) {
			return '#';
		}
		$facets = $this->facets;
		if ( ! isset( $facets[ $facet_name ] ) ) {
			return false;
		}
		unset( $facets[ $facet_name ] );

		return add_query_arg(
			array(
				'facet' => $facets,
			)
		);
	}

	/**
	 * Basic input textbox.
	 */
	public function render_searchbox() {
		$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
		$html  = '<input type="text" name="s" value="' . get_search_query() . '" id="solr_s"> <br/><br/>';
		$html .= '<input type="submit" value="Search"><br/><br/>';
		$html .= '<input type="hidden" name="paged" id="solr_paged" value="' . $paged . '">';
		/**
		 * Filter facet widget search box HTML
		 *
		 * Filter the HTML output of the facet widget search box.
		 *
		 * @param string $html the search box html.
		 */
		echo apply_filters( 'solr_facet_searchbox', $html );
	}

	/**
	 * Determine if a facet should be visible based on options set on admin page.
	 *
	 * @param string $facet Facet name.
	 * @return bool
	 */
	public function show_facet( $facet ) {

		if ( 0 < strpos( $facet, 'taxonomy' ) ) {
			$facet = 'taxonomy';
		}

		if ( 0 < strpos( $facet, 'author' ) ) {
			$facet = 'author';
		}

		if ( 'post_type' === $facet ) {
			$facet = 'type';
		}

		if ( 0 < strpos( $facet, '_str' ) ) {
			$facet = 'custom_fields';
		}

		$key = 's4wp_facet_on_' . $facet;

		$solr_options = solr_options();

		if ( array_key_exists( $key, $solr_options )
			&& false !== $solr_options[ $key ]
		) {
			return true;
		}

		return false;
	}

	/**
	 * Mock a dummy WP_Query
	 */
	function dummy_query() {
		global $wp_query;
		$query = new WP_Query();
		if ( ! $wp_query->get( 's' ) ) {
			$query->set( 's', '*:*' );
			$query->get_posts();
		}

	}

	/**
	 * Callback for array_map to decode html special characters
	 *
	 * @param string $facet Facet to decode.
	 * @return string
	 */
	function htmlspecialchars_decode( $facet ) {
		return htmlspecialchars_decode( $facet, ENT_QUOTES );
	}
}

/**
 * Display the search box outside of a widget.
 */
function solr_facet_search() {
	$facet = new SolrPower_Facet_Widget();
	$facet->dummy_query();
	$facet->facets = filter_input( INPUT_GET, 'facet', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
	echo '<form action="' . esc_url( home_url( '/' ) ) . '" method="get" id="solr_facet">';
	$facet->render_searchbox();
	$facet->fetch_facets();
	echo '</form>';
}
