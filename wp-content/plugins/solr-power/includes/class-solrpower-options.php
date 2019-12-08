<?php
/**
 * Controller for options configuration
 *
 * @package SolrPower_Options
 */

/**
 * Controller for options configuration
 */
class SolrPower_Options {

	/**
	 * Singleton instance
	 *
	 * @var SolrPower_Options|Bool
	 */
	private static $instance = false;

	/**
	 * Admin message.
	 *
	 * @var null|string
	 */
	public $msg = null;

	/**
	 * Grab instance of object.
	 *
	 * @return SolrPower_Options
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the options object
	 */
	function __construct() {
		$menu_action = is_multisite() ? 'network_admin_menu' : 'admin_menu';
		add_action( $menu_action, array( $this, 'add_pages' ) );
		add_action( 'wp_ajax_solr_options', array( $this, 'options_load' ) );
		add_action( 'admin_init', array( $this, 'check_for_actions' ) );
		add_action( 'admin_init', array( $this, 'settings_api' ) );
		add_action( 'wpmuadminedit', array( $this, 'action_wpmuadminedit' ) );
	}

	/**
	 * Register the options page
	 */
	function add_pages() {
		add_menu_page(
			'Solr Power',
			'Solr Power',
			'manage_options',
			'solr-power',
			array(
				$this,
				'options_page',
			),
			'dashicons-search'
		);
	}

	/**
	 * Render the options page
	 */
	function options_page() {
		if ( file_exists( SOLR_POWER_PATH . '/solr-options-page.php' ) ) {
			include( SOLR_POWER_PATH . '/solr-options-page.php' );
		} else {
			esc_html_e( "Couldn't locate the options page.", 'solr-for-wordpress-on-pantheon' );
		}
	}

	/**
	 * Handles saving of options in the network admin
	 * because the WordPress settings API doesn't do this for us.
	 */
	public function action_wpmuadminedit() {
		if ( empty( $_POST['option_page'] )
			|| ! in_array( $_POST['option_page'], array( 'solr-power-facet', 'solr-power-index' ), true ) ) {
			return;
		}

		// Mostly cribbed from wp-admin/options.php.
		$option_page = sanitize_text_field( $_POST['option_page'] );
		check_admin_referer( $option_page . '-options' );
		$whitelist_options = apply_filters( 'whitelist_options', array() );
		$options           = $whitelist_options[ $option_page ];
		foreach ( $options as $option ) {
			$option = trim( $option );
			$value  = null;
			if ( isset( $_POST[ $option ] ) ) {
				$value = $_POST[ $option ];
				if ( ! is_array( $value ) ) {
					$value = trim( $value );
				}
				$value = wp_unslash( $value );
			}
			update_site_option( $option, $value );
		}
		$goback = add_query_arg( 'settings-updated', 'true', wp_get_referer() );
		wp_redirect( $goback );
		exit;
	}

	/**
	 * AJAX Callback
	 */
	function options_load() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'solr_security', 'security' );
		$method = filter_input( INPUT_POST, 'method', FILTER_SANITIZE_STRING );
		if ( in_array( $method, array( 'start-index', 'resume-index' ), true ) ) {
			$query_args = array();
			if ( 'start-index' === $method ) {
				$query_args['batch'] = 1;
			}
			$batch_index   = new SolrPower_Batch_Index( $query_args );
			$success_posts = 0;
			$failed_posts  = 0;
			while ( $batch_index->have_posts() ) {
				$result = $batch_index->index_post();
				if ( 'success' === $result['status'] ) {
					$success_posts++;
				} elseif ( 'failed' === $result['status'] ) {
					$failed_posts++;
				}
			}
			// Iterate to the next set, but don't start it.
			$batch_index->fetch_next_posts();
			if ( 0 == $batch_index->get_remaining_posts() ) {
				do_action( 'solr_power_index_all_finished' );
			}
			header( 'Content-Type: application/json' );
			echo json_encode(
				array(
					'currentBatch'   => $batch_index->get_current_batch(),
					'successPosts'   => $success_posts,
					'failedPosts'    => $failed_posts,
					'remainingPosts' => $batch_index->get_remaining_posts(),
				)
			);
		}
		die();
	}

	/**
	 * Get the Solr Power option, depending on whether it's multisite or not
	 *
	 * @return array
	 */
	function get_option() {
		$option = 'plugin_s4wp_settings';
		if ( is_multisite() ) {
			return get_site_option( $option );
		} else {
			return get_option( $option );
		}
	}

	/**
	 * Update the Solr Power option, depending on whether it's multisite or not
	 *
	 * @param array $options Full options array.
	 */
	function update_option( $options ) {
		$optval = $this->sanitise_options( $options );
		$option = 'plugin_s4wp_settings';
		if ( is_multisite() ) {
			update_site_option( $option, $optval );
		} else {
			update_option( $option, $optval );

		}
	}

	/**
	 * Sanitises the options values
	 *
	 * @param array $options Array of s4w settings options.
	 *
	 * @return array $options sanitised values
	 */
	function sanitise_options( $options ) {
		$clean = array();

		$clean['s4wp_index_pages']         = 1;
		$clean['s4wp_index_posts']         = 1;
		$clean['s4wp_index_comments']      = absint( $options['s4wp_index_comments'] );
		$clean['s4wp_delete_page']         = absint( $options['s4wp_delete_page'] );
		$clean['s4wp_delete_post']         = absint( $options['s4wp_delete_post'] );
		$clean['s4wp_private_page']        = absint( $options['s4wp_private_page'] );
		$clean['s4wp_private_post']        = absint( $options['s4wp_private_post'] );
		$clean['s4wp_output_info']         = 1;
		$clean['s4wp_output_pager']        = 1;
		$clean['s4wp_output_facets']       = 1;
		$clean['s4wp_exclude_pages']       = $this->filter_str2list_numeric( $options['s4wp_exclude_pages'] );
		$clean['s4wp_cat_as_taxo']         = absint( $options['s4wp_cat_as_taxo'] );
		$clean['s4wp_max_display_tags']    = 10;
		$clean['s4wp_facet_on_categories'] = absint( $options['s4wp_facet_on_categories'] );
		$clean['s4wp_facet_on_tags']       = absint( $options['s4wp_facet_on_tags'] );
		$clean['s4wp_facet_on_author']     = absint( $options['s4wp_facet_on_author'] );
		$clean['s4wp_facet_on_type']       = absint( $options['s4wp_facet_on_type'] );
		$clean['s4wp_facet_on_taxonomy']   = absint( $options['s4wp_facet_on_taxonomy'] );
		if ( is_multisite() ) {
			$clean['s4wp_index_all_sites'] = absint( $options['s4wp_index_all_sites'] );
		}
		$clean['s4wp_index_custom_fields']    = $this->filter_str2list( $options['s4wp_index_custom_fields'] );
		$clean['s4wp_facet_on_custom_fields'] = $this->filter_str2list( $options['s4wp_facet_on_custom_fields'] );
		$clean['s4wp_default_operator']       = sanitize_text_field( $options['s4wp_default_operator'] );
		$clean['s4wp_default_sort']           = sanitize_text_field( $options['s4wp_default_sort'] );
		$clean['s4wp_solr_initialized']       = 1;
		$clean['allow_ajax']                  = absint( $options['allow_ajax'] );
		$clean['ajax_div_id']                 = sanitize_text_field( $options['ajax_div_id'] );

		return $clean;
	}

	/**
	 * Converts comma separated string of integers to array.
	 *
	 * @param string $input String to convert.
	 *
	 * @return array
	 */
	function filter_str2list_numeric( $input ) {
		$final = array();
		if ( '' !== $input ) {
			foreach ( explode( ',', $input ) as $val ) {
				$val = sanitize_text_field( trim( $val ) );
				if ( is_numeric( $val ) ) {
					$final[] = $val;
				}
			}
		}

		return $final;
	}

	/**
	 * Converts comma separated string to array.
	 *
	 * @param string $input String to convert.
	 *
	 * @return array
	 */
	function filter_str2list( $input ) {
		$final = array();
		if ( '' !== $input ) {
			foreach ( explode( ',', $input ) as $val ) {
				$final[] = sanitize_text_field( trim( $val ) );
			}
		}

		return $final;
	}

	/**
	 * Converts array to a comma separated string.
	 *
	 * @param array $input Array to convert.
	 * @return string
	 */
	function filter_list2str( $input ) {
		if ( ! is_array( $input ) ) {
			return '';
		}

		$outval = implode( ',', $input );
		if ( ! $outval ) {
			$outval = '';
		}

		return $outval;
	}

	/**
	 * Sets the default options.
	 */
	function initalize_options() {
		$options = array();

		$options['s4wp_index_pages']            = 1;
		$options['s4wp_index_posts']            = 1;
		$options['s4wp_delete_page']            = 1;
		$options['s4wp_delete_post']            = 1;
		$options['s4wp_private_page']           = 1;
		$options['s4wp_private_post']           = 1;
		$options['s4wp_output_info']            = 1;
		$options['s4wp_output_pager']           = 1;
		$options['s4wp_output_facets']          = 1;
		$options['s4wp_exclude_pages']          = '';
		$options['s4wp_cat_as_taxo']            = 1;
		$options['s4wp_max_display_tags']       = 10;
		$options['s4wp_facet_on_categories']    = 1;
		$options['s4wp_facet_on_taxonomy']      = 1;
		$options['s4wp_facet_on_tags']          = 1;
		$options['s4wp_facet_on_author']        = 1;
		$options['s4wp_facet_on_type']          = 1;
		$options['s4wp_index_comments']         = 1;
		$options['s4wp_index_custom_fields']    = '';
		$options['s4wp_facet_on_custom_fields'] = '';
		$options['s4wp_default_operator']       = 'OR';
		$options['s4wp_default_sort']           = 'score';
		$options['allow_ajax']                  = 0;
		$options['ajax_div_id']                 = '';
		$options['s4wp_solr_initialized']       = 1;
		$options['s4wp_index_all_sites']        = 1;
		$this->update_option( $options );
	}


	/**
	 * Checks to see if any actions were taken on the settings page.
	 */
	function check_for_actions() {

		if ( ! isset( $_POST['action'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$action = sanitize_text_field( $_POST['action'] );
		switch ( $action ) {
			case 'ping':
				if ( ! $this->check_nonce( 'solr_ping' ) ) {
					return;
				}
				if ( SolrPower_Api::get_instance()->ping_server() ) {
					$this->msg = esc_html__( 'Ping Success!', 'solr-for-wordpress-on-pantheon' );
				} else {
					$this->msg = esc_html__( 'Ping Failed!', 'solr-for-wordpress-on-pantheon' );
				}
				break;
			case 'init_blogs':
				if ( ! $this->check_nonce( 'solr_init_blogs' ) ) {
					return;
				}
				SolrPower_Sync::get_instance()->copy_config_to_all_blogs();
				$this->msg = esc_html__( 'Configuration has been copied to all blogs!', 'solr-for-wordpress-on-pantheon' );
				break;
			case 'optimize':
				if ( ! $this->check_nonce( 'solr_optimize' ) ) {
					return;
				}
				SolrPower_Api::get_instance()->optimize();
				$this->msg = esc_html__( 'Index Optimized!', 'solr-for-wordpress-on-pantheon' );
				break;
			case 'delete_all':
				if ( ! $this->check_nonce( 'solr_delete_all' ) ) {
					return;
				}
				SolrPower_Sync::get_instance()->delete_all();
				$this->msg = esc_html__( 'All Indexed Pages Deleted!', 'solr-for-wordpress-on-pantheon' );
				break;
			case 'repost_schema':
				if ( ! $this->check_nonce( 'solr_repost_schema' ) ) {
					return;
				}
				if ( ! getenv( 'PANTHEON_ENVIRONMENT' ) || ! getenv( 'FILEMOUNT' ) ) {
					$this->msg = esc_html__( 'Schema reposting only works in a Pantheon environment.', 'solr-for-wordpress-on-pantheon' );
					break;
				}
				SolrPower_Sync::get_instance()->delete_all();
				$output    = SolrPower_Api::get_instance()->submit_schema();
				$this->msg = esc_html__( 'All Indexed Pages Deleted!', 'solr-for-wordpress-on-pantheon' ) . '<br />' . esc_html( $output );
				break;
		} // End switch().

	}

	/**
	 * Verifies if nonce is valid for action taken.
	 *
	 * @param string $field Field to check the nonce for.
	 *
	 * @return bool
	 */
	private function check_nonce( $field ) {
		if ( ! isset( $_POST[ $field ] )
			|| ! wp_verify_nonce( $_POST[ $field ], 'solr_action' )
		) {
			$this->msg = esc_html__( 'Action failed. Please try again.', 'solr-for-wordpress-on-pantheon' );

			return false;
		}

		return true;
	}

	/**
	 * Register setting and sections for Settings API.
	 */
	function settings_api() {
		register_setting( 'solr-power-index', 'plugin_s4wp_settings', array( $this, 'sanitise_options' ) );
		register_setting( 'solr-power-facet', 'plugin_s4wp_settings', array( $this, 'sanitise_options' ) );
		$this->indexing_section();
		$this->results_section();
		$this->facet_section();
	}

	/**
	 * Generates HTML form fields based upon array of arguments sent (field, filter, type, choices).
	 *
	 * @param array $args Arguments for rendering the field.
	 */
	function render_field( $args ) {

		$field_name    = $args['field'];
		$s4wp_settings = solr_options();
		$value         = $this->render_value( $s4wp_settings[ $field_name ], $args['filter'] );
		switch ( $args['type'] ) {
			case 'input':
				echo '<input type="text" name="plugin_s4wp_settings[' . esc_attr( $field_name ) . ']" value="' . esc_attr( $value ) . '"/>';
				echo PHP_EOL; // XSS ok.
				break;
			case 'checkbox':
				echo '<input type="checkbox" name="plugin_s4wp_settings[' . esc_attr( $field_name ) . ']" value="1" ' . checked( $value, 1, false ) . '/>';
				echo PHP_EOL; // XSS ok.
				break;
			case 'radio':
				if ( ! isset( $args['choices'] ) || ! is_array( $args['choices'] ) ) {
					return;
				}
				if ( empty( $value ) ) {
					$value = $args['choices'][0];
				}
				foreach ( $args['choices'] as $choice ) {
					echo esc_html( $choice ) . ' ';
					echo '<input type="radio" name="plugin_s4wp_settings[' . esc_attr( $field_name ) . ']" value="' . esc_attr( $choice ) . '" ' . checked( $value, $choice, false ) . '/>';
					echo PHP_EOL; // XSS ok.
				}

				break;
			case 'select':
				if ( ! isset( $args['choices'] ) || ! is_array( $args['choices'] ) ) {
					return;
				}
				if ( empty( $value ) ) {
					$value = $args['choices'][0];
				}
				echo '<select name="plugin_s4wp_settings[' . esc_attr( $field_name ) . ']">';
				echo PHP_EOL; // XSS ok.
				foreach ( $args['choices'] as $choice ) {
					echo '<option value="' . esc_attr( $choice ) . '" ' . selected( $value, $choice, false ) . '>' . esc_attr( $choice ) . '</option>';
					echo PHP_EOL; // XSS ok.
				}
				echo '</select>';
				echo PHP_EOL; // XSS ok.
				break;
		} // End switch().
	}

	/**
	 * Type adjustments for value.
	 *
	 * @param string $value  Value saved in option.
	 * @param string $filter Type change needed.
	 *
	 * @return string Filtered (or unfiltered) value.
	 */
	private function render_value( $value, $filter ) {
		if ( is_null( $filter ) ) {
			return $value;
		}
		switch ( $filter ) {
			case 'list2str':
				return SolrPower_Options::get_instance()->filter_list2str( $value );
				break;
			case 'bool':
				return absint( $value );
				break;
		}

		return $value;
	}

	/**
	 * Indexing Options Section
	 * Creates the Indexing Options section and the fields in that section.
	 */
	private function indexing_section() {
		$section = 'solr_indexing';
		$page    = 'solr-power-index';

		add_settings_section(
			$section,
			'Indexing Options',
			'__return_empty_string',
			'solr-power-index'
		);

		$this->add_field( 's4wp_delete_page', 'Remove Page on Delete', $page, $section, 'checkbox', 'bool' );
		$this->add_field( 's4wp_delete_post', 'Remove Post on Delete', $page, $section, 'checkbox', 'bool' );
		$this->add_field( 's4wp_private_page', 'Remove Page on Status Change', $page, $section, 'checkbox', 'bool' );
		$this->add_field( 's4wp_private_post', 'Remove Post on Status Change', $page, $section, 'checkbox', 'bool' );
		$this->add_field( 's4wp_index_comments', 'Index Comments', $page, $section, 'checkbox', 'bool' );
		$this->add_field( 's4wp_index_custom_fields', 'Index custom fields (comma separated names list)', $page, $section, 'input', 'list2str' );
		$this->add_field( 's4wp_exclude_pages', 'Excludes Posts or Pages (comma separated ids list)', $page, $section, 'input', 'list2str' );

	}

	/**
	 * Results Section
	 * Adds fields in section.
	 */
	private function results_section() {
		$section = 'solr_results';
		$page    = 'solr-power-index';

		add_settings_section(
			$section,
			'Result Options',
			'__return_empty_string',
			'solr-power-index'
		);

		$this->add_field(
			's4wp_default_operator',
			'Default Search Operator',
			$page,
			$section,
			'radio',
			null,
			array(
				'OR',
				'AND',
			)
		);

		$this->add_field(
			's4wp_default_sort',
			'Default Sort',
			$page,
			$section,
			'select',
			null,
			array(
				'score',
				'displaydate',
			)
		);
	}

	/**
	 * Facet Section
	 * Adds fields in section.
	 */
	private function facet_section() {
		$section = 'solr_facet';
		$page    = 'solr-power-facet';

		add_settings_section(
			$section,
			'Facet Options',
			'__return_empty_string',
			'solr-power-facet'
		);

		$this->add_field( 'allow_ajax', 'AJAX Facet Search Support', $page, $section, 'checkbox', 'bool' );
		$this->add_field( 'ajax_div_id', 'AJAX Div ID (displays search results)', $page, $section, 'input' );
		$this->add_field( 's4wp_cat_as_taxo', 'Category Facet as Taxonomy', $page, $section, 'checkbox', 'bool' );
		$this->add_field( 's4wp_facet_on_categories', 'Categories as Facet', $page, $section, 'checkbox', 'bool' );
		$this->add_field( 's4wp_facet_on_tags', 'Tags as Facet', $page, $section, 'checkbox', 'bool' );
		$this->add_field( 's4wp_facet_on_author', 'Author as Facet', $page, $section, 'checkbox', 'bool' );
		$this->add_field( 's4wp_facet_on_type', 'Post Type as Facet', $page, $section, 'checkbox', 'bool' );
		$this->add_field( 's4wp_facet_on_taxonomy', 'Taxonomy as Facet', $page, $section, 'checkbox', 'bool' );
		$this->add_field( 's4wp_facet_on_custom_fields', 'Custom fields as Facet (comma separated ordered names list)', $page, $section, 'input', 'list2str' );

	}

	/**
	 * Adds the settings field with custom arguments.
	 *
	 * @param string      $name    Option Key.
	 * @param string      $title   Name of field / label.
	 * @param string      $page    Settings page slug.
	 * @param string      $section The section the field is going to be registered to.
	 * @param string      $type    Type of form element (input, checkbox, radio, select).
	 * @param null|string $filter  Any value typesetting that needs to be done.
	 * @param null|string $choices Any default choices for select or radio options.
	 */
	private function add_field( $name, $title, $page = 'solr-power', $section, $type, $filter = null, $choices = null ) {
		$args = array(
			'field'   => $name,
			'type'    => $type,
			'filter'  => $filter,
			'choices' => $choices,
		);
		add_settings_field(
			$name,
			$title,
			array(
				$this,
				'render_field',
			),
			$page,
			$section,
			$args
		);
	}

}
