<?php

class OTGS_Installer_Buy_URL_Hooks {

	private $embedded_at;

	public function __construct( $embedded_at ) {
		$this->embedded_at = $embedded_at;
	}

	public function add_hooks() {
		add_filter( 'wp_installer_buy_url', array( $this, 'append_installer_source' ) );
	}

	/**
	 * @param string $url
	 *
	 * @return string
	 */
	public function append_installer_source( $url ) {
		$url = add_query_arg( 'embedded_at', $this->embedded_at, $url );

		return $url;
	}
}