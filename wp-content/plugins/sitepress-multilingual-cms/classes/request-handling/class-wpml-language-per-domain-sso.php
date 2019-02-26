<?php

/**
 * Class WPML_Language_Per_Domain_SSO
 */
class WPML_Language_Per_Domain_SSO {

	const SSO_NONCE_ACTION    = 'wpml_iframe_content';
	const SCRIPT_HANDLER      = 'wpml_lang_per_domain_sso';
	const TRANSIENT_DOING_SSO = 'wpml_doing_sso';
	const TRANSIENT_PREFIX    = 'wpml_sso_';
	const KEY_TOKEN           = 'wpml_sso_token';
	const AJAX_ACTION         = 'wpml_sign_user';
	const IFRAME_DOMAIN_HASH  = 'wpml_sso_iframe_hash';
	const DOING_SSO_TIMEOUT   = MINUTE_IN_SECONDS;

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var WPML_PHP_Functions $php_functions */
	private $php_functions;

	private $site_url;
	private $domains;

	/** @var int $current_user_id */
	private $current_user_id;

	public function __construct( SitePress $sitepress, WPML_PHP_Functions $php_functions ) {
		$this->sitepress        = $sitepress;
		$this->php_functions    = $php_functions;
		$this->site_url         = $this->sitepress->convert_url( get_home_url(), $this->sitepress->get_default_language() );
		$this->domains          = $this->get_domains();
	}

	public function init_hooks() {

		if ( $this->is_doing_sso() ) {
			add_action( 'init', array( $this, 'init_action' ) );

			// Add iframe
			add_action( 'wp_footer', array( $this, 'add_iframes_to_footer' ) );
			add_action( 'admin_footer', array( $this, 'add_iframes_to_footer' ) );
			add_action( 'login_footer', array( $this, 'add_iframes_to_footer' ) );

			// Enqueue scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_sso_script' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_sso_script' ) );
			add_action( 'login_enqueue_scripts', array( $this, 'enqueue_sso_script' ) );

			// Add AJAX actions
			add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'iframe_ajax_sign_user' ) );
			add_action( 'wp_ajax_nopriv_' . self::AJAX_ACTION, array( $this, 'iframe_ajax_sign_user' ) );
		}

		// Hooks to initiate an SSO process
		add_action( 'wp_login', array( $this, 'wp_login_action' ), 10, 2 );
		add_action( 'wp_logout', array( $this, 'wp_logout_action' ) );
	}

	private function get_domain_transient_name( $domain ) {
		return self::TRANSIENT_PREFIX . $this->current_user_id . '_' . $domain;
	}

	public function init_action() {
		$this->set_current_user_id();
		$this->output_iframe_content();
	}

	/** @param int $user_id */
	private function set_current_user_id( $user_id = 0 ) {
		if ( $user_id ) {
			$this->current_user_id = $user_id;
		}

		if ( ! $this->current_user_id ) {
			$this->current_user_id = $this->get_user_id_from_token();
		}

		if ( ! $this->current_user_id ) {
			$this->current_user_id = get_current_user_id();
		}
	}

	/**
	 * Add content of iframe.
	 * This function is hooked very early and exits to avoid loading all sites.
	 */
	private function output_iframe_content() {
		if ( $this->should_add_content_to_iframe() ) {
			$nonce = wp_create_nonce( self::SSO_NONCE_ACTION );
			?>
			<script>
				function sendXHRHttpRequest( params ) {
					var xhr = new XMLHttpRequest();
					xhr.open( 'POST', "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>", true );
					xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhr.send(params);
				}
                window.onmessage = function (e) {
                    var payload = JSON.parse(e.data),
						userId = parseInt(payload.userId),
						userStatus = payload.userStatus,
                        domains = <?php echo json_encode( array_values( $this->domains ) ); ?>;

                    if (-1 === domains.indexOf(e.origin)) {
                        return;
                    }

					var params = 'action=<?php echo self::AJAX_ACTION; ?>&nonce=<?php echo esc_attr( $nonce ); ?>&user_id=' + userId + '&user_status=' + userStatus;

                    sendXHRHttpRequest(params);
                };
			</script>
			<?php
			$this->php_functions->exit_php();
		}
	}

	/**
	 * Add iframe to footer.
	 */
	public function add_iframes_to_footer() {
		foreach ( $this->domains as $domain ) {

			if ( $domain !== $this->get_current_domain() && $this->is_domain_pending_sign_user( $domain ) ) {
				$domain_hash = $this->get_domain_hash( $domain );
				$iframe_url  = add_query_arg( self::IFRAME_DOMAIN_HASH, $domain_hash, trailingslashit( $domain ) );
				?>
				<iframe class="wpml_iframe" style="display:none" src="<?php echo esc_url( $iframe_url ); ?>"></iframe>
				<?php
			}
		}
	}

	public function enqueue_sso_script() {
		wp_enqueue_script(
			self::SCRIPT_HANDLER,
			ICL_PLUGIN_URL . '/res/js/wpml-language-per-domain-sso.js',
			array( 'jquery' )
		);

		wp_localize_script(
			self::SCRIPT_HANDLER,
			'wpml_sso',
			array(
				'ajaxurl'           => admin_url( 'admin-ajax.php' ),
				'is_user_logged_in' => is_user_logged_in(),
				'current_user_id'   => $this->current_user_id,
				'nonce'             => wp_create_nonce( self::SSO_NONCE_ACTION ),
			)
		);
	}

	public function iframe_ajax_sign_user() {
		$this->set_current_user_id( $this->get_user_id_from_post_var() );

		if ( $this->validate_ajax_sign_user() ) {
			$user_status = isset( $_POST['user_status'] )
				? filter_var( $_POST['user_status'], FILTER_SANITIZE_STRING ) : null;

			if ( 'wpml_is_user_signed_in' === $user_status ) {
				wp_set_auth_cookie( $this->current_user_id );
			} else {
				wp_clear_auth_cookie();
			}

			delete_transient( $this->get_domain_transient_name( $this->get_current_domain() ) );
		}
	}

	/** @return bool */
	private function validate_ajax_sign_user() {
		$current_domain = $this->get_current_domain();

		return $this->current_user_id
		       && $this->is_valid_ajax()
		       && $this->get_domain_hash( $current_domain ) === get_transient( $this->get_domain_transient_name( $current_domain ) );
	}

	/**
	 * @param string  $user_login
	 * @param WP_User $user
	 */
	public function wp_login_action( $user_login, WP_User $user ) {
		$this->set_current_user_id( (int) $user->ID );
		$this->set_doing_sso_transients();
	}

	public function wp_logout_action() {
		$this->set_current_user_id();
		$this->set_doing_sso_transients();
		add_filter( 'logout_redirect', array( $this, 'add_redirect_user_token' ), 10, 3 );
	}

	/** @return int */
	private function get_user_id_from_token() {
		$user_id = 0;

		if ( array_key_exists( self::KEY_TOKEN, $_GET ) ) {
			$token = filter_var( $_GET[ self::KEY_TOKEN ], FILTER_SANITIZE_STRING );
			$user_id = (int) get_transient( self::TRANSIENT_PREFIX . $token );
			delete_transient( self::TRANSIENT_PREFIX . $token );
		}

		return $user_id;
	}

	/** @return int */
	private function get_user_id_from_post_var() {
		$user_id = 0;

		if ( isset( $_POST['user_id'] ) ) {
			$user_id = (int) $_POST['user_id'];
		}

		return $user_id;
	}

	/**
	 * Store specific key to DB, to check later in other domains.
	 */
	public function set_doing_sso_transients() {
		set_transient( self::TRANSIENT_DOING_SSO, 1, self::DOING_SSO_TIMEOUT );

		foreach ( $this->domains as $domain ) {
			$domain_hash = $this->get_domain_hash( $domain );

			if ( $this->get_current_domain() !== $domain ) {
				set_transient( $this->get_domain_transient_name( $domain ), $domain_hash, self::DOING_SSO_TIMEOUT );
			}
		}
	}

	/**
	 * Create URL hash.
	 *
	 * @param string $domain
	 *
	 * @return string
	 */
	private function get_domain_hash( $domain ) {
		return hash( 'sha256', self::SSO_NONCE_ACTION . $domain );
	}

	private function get_current_domain() {
		$host = '';

		if ( array_key_exists( 'HTTP_HOST', $_SERVER ) ) {
			$host = (string) $_SERVER['HTTP_HOST'];
		}

		return $this->get_current_protocol() . $host;
	}

	private function get_current_protocol() {
		return is_ssl() ? 'https://' : 'http://';
	}

	/**
	 * @return array
	 */
	private function get_domains() {
		$domains = $this->sitepress->get_setting( 'language_domains', array() );

		$active_codes = array_keys( $this->sitepress->get_active_languages() );
		$sso_domains  = array( $this->site_url );

		foreach ( $domains as $language_code => $domain ) {

			if ( in_array( $language_code, $active_codes ) ) {
				$sso_domains[] = $this->get_current_protocol() . $domain;
			}
		}

		return $sso_domains;
	}

	private function is_valid_ajax() {
		return wpml_is_ajax()
		       && isset( $_POST['nonce'] )
		       && wp_verify_nonce( $_POST['nonce'], self::SSO_NONCE_ACTION );
	}

	private function should_add_content_to_iframe() {
		return isset( $_GET[ self::IFRAME_DOMAIN_HASH ] )
		       && ! wpml_is_ajax()
		       && $this->get_domain_hash( $this->get_current_domain() ) === $_GET[ self::IFRAME_DOMAIN_HASH ];
	}

	private function is_doing_sso() {
		return (bool) get_transient( self::TRANSIENT_DOING_SSO );
	}

	private function is_domain_pending_sign_user( $domain ) {
		return (bool) get_transient( $this->get_domain_transient_name( $domain ) );
	}

	/**
	 * @param string           $redirect_to           The redirect destination URL.
	 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User|WP_Error $user
	 *
	 * @return string
	 */
	public function add_redirect_user_token( $redirect_to, $requested_redirect_to, $user ) {
		if ( ! is_wp_error( $user ) ) {
			return add_query_arg( self::KEY_TOKEN, $this->create_user_token( $user->ID ), $redirect_to );
		}

		return $redirect_to;
	}

	/**
	 * @param int $user_id
	 *
	 * @return string
	 */
	private function create_user_token( $user_id ) {
		$token = wp_create_nonce( self::SSO_NONCE_ACTION );
		set_transient( self::TRANSIENT_PREFIX . $token, $user_id, self::DOING_SSO_TIMEOUT );
		return $token;
	}
}
