<?php

/**
 * Class WPML_Cookie_Scripts
 */
class WPML_Cookie_Scripts {

	/**
	 * @var string
	 */
	private $language_cookie_name;

	/**
	 * @var string
	 */
	private $current_language;

	/**
	 * WPML_Cookie_Scripts constructor.
	 *
	 * @param string $language_cookie_name
	 * @param string $current_language
	 */
	public function __construct( $language_cookie_name, $current_language ) {
		$this->language_cookie_name = $language_cookie_name;
		$this->current_language = $current_language;
	}

	public function add_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), - PHP_INT_MAX );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery.cookie', ICL_PLUGIN_URL . '/res/js/jquery.cookie.js', array( 'jquery' ), ICL_SITEPRESS_VERSION );
		wp_enqueue_script( 'wpml-cookie', ICL_PLUGIN_URL . '/res/js/cookies/language-cookie.js', array( 'jquery', 'jquery.cookie' ), ICL_SITEPRESS_VERSION );

		$cookies = array(
			$this->language_cookie_name => array(
				'value'   => $this->current_language,
				'expires' => 1,
				'path'    => '/',
			),
		);

		wp_localize_script( 'wpml-cookie', 'wpml_cookies', $cookies );
	}
}