<?php

class WPML_REST_Language_Middleware_Hooks implements IWPML_Action {

	const SCRIPT_HANDLER = 'wpml-rest-language';

	/** @var IWPML_Current_Language $current_language */
	private $current_language;

	public function __construct( IWPML_Current_Language $current_language ) {
		$this->current_language = $current_language;
	}

	public function add_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function admin_enqueue_scripts( $hook ) {
		if ( $this->is_editing_post( $hook ) ) {
			wp_enqueue_script(
				self::SCRIPT_HANDLER,
				ICL_PLUGIN_URL . '/dist/js/rest/app.js',
				array( 'wp-api-fetch' ),
				ICL_SITEPRESS_VERSION
			);

			wp_add_inline_script(
				self::SCRIPT_HANDLER,
				'wp.apiFetch.use && wp.apiFetch.use( wpmlRest.wpmlFetchApiLanguageMiddlewareFactory( "' . $this->current_language->get_current_language() . '" ) );',
				'after'
			);
		}
	}

	private function is_editing_post( $hook ) {
		return in_array( $hook, array( 'post.php', 'post-new.php' ), true );
	}
}
