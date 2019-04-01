<?php
/**
 * WPML Sunrise Script - START
 * @author OnTheGoSystems
 * @version 3.7.0
 *
 * Place this script in the wp-content folder and add "define('SUNRISE', 'on');" in wp-config.php
 * in order to enable using different domains for different languages in multisite mode
 *
 * Experimental feature
 */

/**
 * Class WPML_Sunrise_Lang_In_Domains
 * @author OnTheGoSystems
 */
class WPML_Sunrise_Lang_In_Domains {

	/** @var  wpdb $wpdb */
	private $wpdb;

	/** @var  string $table_prefix */
	private $table_prefix;

	/** @var  string $current_blog */
	private $current_blog;

	/** @var  bool $no_recursion */
	private $no_recursion;

	/**
	 * Method init
	 */
	public function init() {
		if ( ! defined( 'WPML_SUNRISE_MULTISITE_DOMAINS' ) ) {
			define( 'WPML_SUNRISE_MULTISITE_DOMAINS', true );
		}

		add_filter( 'query', array( $this, 'query_filter' ) );
	}

	/**
	 * @param string $q
	 *
	 * @return string
	 */
	public function query_filter( $q ) {
		$this->set_private_properties();

		if ( ! $this->current_blog && ! $this->no_recursion ) {

			$this->no_recursion = true;

			$domains = $this->extract_variables_from_query( $q, 'domain' );

			if ( $domains && $this->query_has_no_result( $q ) ) {
				$q = $this->transpose_query_if_one_domain_is_matching( $q, $domains );
			}

			$this->no_recursion = false;
		}

		return $q;
	}

	/**
	 * method set_private_properties
	 */
	private function set_private_properties() {
		global $wpdb, $table_prefix, $current_blog;

		$this->wpdb = $wpdb;
		$this->table_prefix = $table_prefix;
		$this->current_blog = $current_blog;

	}

	/**
	 * @param string $query
	 *
	 * @return array
	 */
	private function extract_variables_from_query( $query, $field ) {
		$variables  = array();
		$patterns = array(
			'#WHERE\s+' . $field . '\s+IN\s*\(([^\)]+)\)#',
			'#WHERE\s+' . $field . '\s*=\s*([^\s]+)#',
			'#AND\s+' . $field . '\s+IN\s*\(([^\)]+)\)#',
			'#AND\s+' . $field . '\s*=\s*([^\s]+)#',
		);

		foreach ( $patterns as $pattern ) {
			$found = preg_match( $pattern, $query, $matches );
			if ( $found && array_key_exists( 1, $matches ) ) {
				$variables = $matches[1];
				$variables = preg_replace( '/\s+/', '', $variables );
				$variables = preg_replace( '/[\'"]/', '', $variables );
				$variables = explode( ',', $variables );
				break;
			}
		}

		return $variables;
	}

	/**
	 * @param string $q
	 *
	 * @return bool
	 */
	private function query_has_no_result( $q ) {
		return ! (bool) $this->wpdb->get_row( $q );
	}

	/**
	 * @param string $q
	 * @param array  $domains
	 *
	 * @return string
	 */
	private function transpose_query_if_one_domain_is_matching( $q, $domains ) {
		$paths        = $this->extract_variables_from_query( $q, 'path' );

		// Create as many placeholders as $paths we have.
		$placeholders = implode( ',', array_fill( 0, sizeof( $paths ), '%s' ) );

		// Array with all the parameters for preparing the SQL.
		$parameters   = $paths;
		$parameters[] = BLOG_ID_CURRENT_SITE;

		// The ORDER is there to get the default site at the end of the results.
		$blogs        = $this->wpdb->get_col( $this->wpdb->prepare(
			"SELECT blog_id FROM {$this->wpdb->blogs} WHERE path IN ($placeholders) ORDER BY blog_id = %d",
			$parameters
		) );

		$found_blog_id = null;
		foreach ( (array) $blogs as $blog_id ) {
			$prefix = $this->table_prefix;

			if ( $blog_id > 1 ) {
				$prefix .= $blog_id . '_';
			}

			$icl_settings = $this->wpdb->get_var( "SELECT option_value FROM {$prefix}options WHERE option_name = 'icl_sitepress_settings'" );

			if ( $icl_settings ) {
				$icl_settings = unserialize( $icl_settings );

				if ( $icl_settings && 2 === (int) $icl_settings['language_negotiation_type'] ) {
					$found_blog_id = $this->get_blog_id_from_domain( $domains, $icl_settings, $blog_id );
					if ( $found_blog_id ) {
						$q = $this->wpdb->prepare( "SELECT blog_id FROM {$this->wpdb->blogs} WHERE blog_id = %d", $found_blog_id );
						break;
					}
				}
			}
		}

		return $q;
	}

	/**
	 * @param array $domains
	 * @param array $wpml_settings
	 * @param       $blog_id
	 *
	 * @return mixed
	 */
	private function get_blog_id_from_domain( array $domains, array $wpml_settings, $blog_id ) {
		foreach ( $domains as $domain ) {
			if ( in_array( 'http://' . $domain, $wpml_settings['language_domains'], true ) ) {
				return $blog_id;
			} elseif ( in_array( $domain, $wpml_settings['language_domains'], true ) ) {
				return $blog_id;
			}
		}

		return null;
	}
}

$wpml_sunrise_lang_in_domains = new WPML_Sunrise_Lang_In_Domains();
$wpml_sunrise_lang_in_domains->init();

/**
 * WPML Sunrise Script - END
 */
