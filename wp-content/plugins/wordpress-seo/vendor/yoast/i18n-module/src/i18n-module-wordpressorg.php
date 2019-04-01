<?php

/**
 * The Yoast i18n module with a connection to WordPress.org.
 */
class Yoast_I18n_WordPressOrg_v3 {

	/**
	 * The i18n object that presents the user with the notification.
	 *
	 * @var yoast_i18n_v3
	 */
	protected $i18n;

	/**
	 * Constructs the i18n module for wordpress.org. Required fields are the 'textdomain', 'plugin_name' and 'hook'
	 *
	 * @param array $args                   The settings for the i18n module.
	 * @param bool $show_translation_box    Whether the translation box should be shown.
	 */
	public function __construct( $args, $show_translation_box = true ) {
		$args = $this->set_defaults( $args );

		$this->i18n = new Yoast_I18n_v3( $args, $show_translation_box );
		$this->set_api_url( $args['textdomain'] );
	}

	/**
	 * Returns the i18n_promo message from the i18n_module. Returns en empty string if the promo shouldn't be shown.
	 *
	 * @access public
	 *
	 * @return string The i18n promo message.
	 */
	public function get_promo_message() {
		return $this->i18n->get_promo_message();
	}

	/**
	 * Returns a button that can be used to dismiss the i18n-message.
	 *
	 * @access private
	 *
	 * @return string
	 */
	public function get_dismiss_i18n_message_button() {
		return $this->i18n->get_dismiss_i18n_message_button();
	}

	/**
	 * Sets the default values for wordpress.org
	 *
	 * @param array $args The arguments to set defaults for.
	 *
	 * @return array The arguments with the arguments set.
	 */
	private function set_defaults( $args ) {

		if ( ! isset( $args['glotpress_logo'] ) ) {
			$args['glotpress_logo'] = 'https://plugins.svn.wordpress.org/' . $args['textdomain'] . '/assets/icon-128x128.png';
		}

		if ( ! isset( $args['register_url'] ) ) {
			$args['register_url'] = 'https://translate.wordpress.org/projects/wp-plugins/' . $args['textdomain'] . '/';
		}

		if ( ! isset( $args['glotpress_name'] ) ) {
			$args['glotpress_name'] = 'Translating WordPress';
		}

		if ( ! isset( $args['project_slug'] ) ) {
			$args['project_slug'] = $args['textdomain'];
		}

		return $args;
	}

	/**
	 * Set the API URL on the i18n object.
	 *
	 * @param string $textdomain The textdomain to use for the API URL.
	 */
	private function set_api_url( $textdomain ) {
		$this->i18n->set_api_url( 'https://translate.wordpress.org/api/projects/wp-plugins/' . $textdomain . '/stable/' );
	}
}
