<?php
// adapted from http://wordpress.org/extend/plugins/black-studio-wpml-javascript-redirect/
// thanks to Blank Studio - http://www.blackstudio.it/

class WPML_Browser_Redirect {

	/**
	 * @var SitePress
	 */
	private $sitepress;

	public function __construct( $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function init_hooks() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init(){
        if( ! isset( $_GET['redirect_to'] ) &&
            ! is_admin() &&
			( ! isset( $_SERVER['REQUEST_URI'] ) || ! preg_match( '#wp-login\.php$#', preg_replace( "@\?(.*)$@", '', $_SERVER['REQUEST_URI'] ) ) )
        ) {
	        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        }
    }

    public function enqueue_scripts(){
        wp_register_script( 'wpml-browser-redirect', ICL_PLUGIN_URL . '/dist/js/browser-redirect/app.js', array(), ICL_SITEPRESS_VERSION );

        $args['skip_missing'] = intval( $this->sitepress->get_setting( 'automatic_redirect' ) == 1 );

        // Build multi language urls array
        $languages      = $this->sitepress->get_ls_languages($args);
        $language_urls  = array();
        foreach($languages as $language) {
			if(isset($language['default_locale']) && $language['default_locale']) {
				$language_urls[$language['default_locale']] = $language['url'];
				$language_parts = explode('_', $language['default_locale']);
				if(count($language_parts)>1) {
					foreach($language_parts as $language_part) {
						if(!isset($language_urls[$language_part])) {
							$language_urls[$language_part] = $language['url'];
						}
					}
				}
			}
			$language_urls[$language['language_code']] = $language['url'];
        }
        // Cookie parameters
        $http_host = $_SERVER['HTTP_HOST'] == 'localhost' ? '' : $_SERVER['HTTP_HOST'];
        $cookie = array(
			'name'       => '_icl_visitor_lang_js',
			'domain'     => ( defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN ? COOKIE_DOMAIN : $http_host ),
			'path'       => ( defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/' ),
			'expiration' => $this->sitepress->get_setting( 'remember_language' ),
        );

		// Send params to javascript
        $params = array(
			'pageLanguage' => defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : get_bloginfo( 'language' ),
			'languageUrls' => $language_urls,
			'cookie'       => $cookie
        );

		/**
		 * Filters the data sent to the browser redirection script.
		 *
		 * If ´$param´ is empty or ´$params['pageLanguage']´ or ´$params['languageUrls']´ are not set, the script won't be enqueued.
		 *
		 * @since 4.0.6
		 *
		 * @param array $params {
		 *     Data sent to the script as `wpml_browser_redirect_params` object.
		 *
		 *     @type string $pageLanguage The language of the current page.
		 *     @type array  $languageUrls Associative array where the key is the language code and the value the translated URLs of the current page.
		 *     @type array  $cookie       Associative array containing information to use for creating the cookie.
		 * }
		 */
		$params = apply_filters( 'wpml_browser_redirect_language_params', $params );

		$enqueue = false;
		if ( $params && isset( $params['pageLanguage'], $params['languageUrls'] ) ) {
			wp_localize_script( 'wpml-browser-redirect', 'wpml_browser_redirect_params', $params );

			/**
			 * Prevents the `wpml-browser-redirect` from being enqueued.
			 *
			 * If the filter returns as falsy value, the script won't be enqueued.
			 *
			 * @since 4.0.6
			 *
			 * @param bool $enqueue Defaults to `true`
			 */
			$enqueue = apply_filters( 'wpml_enqueue_browser_redirect_language', true );
			if ( $enqueue ) {
				wp_enqueue_script( 'wpml-browser-redirect' );
			}
		}

		/**
		 * Fires after the browser redirection logic runs, even if the script is not enqueued.
		 *
		 * @since 4.0.6
		 *
		 * @param bool $enqueue Defaults to `true`
		 * @param bool $params  @see `wpml_browser_redirect_language_params`
		 */
		do_action( 'wpml_enqueued_browser_redirect_language', $enqueue, $params );
    }
}
