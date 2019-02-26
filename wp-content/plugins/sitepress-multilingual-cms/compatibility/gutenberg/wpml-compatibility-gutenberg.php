<?php

class WPML_Compatibility_Gutenberg implements IWPML_Action {

	/**
	 * We need to load the filter after `wpml_before_init` where ST loads the blocking filter
	 * and before `plugins_loaded` (priority 10) where Gutenberg loads the text domain.
	 */
	const PRIORITY_ON_PLUGINS_LOADED = 5;

	/** @var WPML_PHP_Functions $php_functions */
	private $php_functions;

	public function __construct( WPML_PHP_Functions $php_functions = null ) {
		$this->php_functions = $php_functions;
	}

	public function add_hooks() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain_filter' ), self::PRIORITY_ON_PLUGINS_LOADED );
	}

	public function load_textdomain_filter() {
		if ( $this->php_functions->defined( 'WPML_ST_VERSION' )
			&& $this->php_functions->function_exists( 'gutenberg_load_plugin_textdomain' )
		) {
			add_filter( 'override_load_textdomain', array( $this, 'unblock_gutenberg_domain' ), PHP_INT_MAX, 2 );
		}
	}

	/**
	 * @param bool   $override
	 * @param string $domain
	 *
	 * @return bool
	 */
	public function unblock_gutenberg_domain( $override, $domain ) {
		if ( 'gutenberg' === $domain ) {
			return false;
		}

		return $override;
	}
}