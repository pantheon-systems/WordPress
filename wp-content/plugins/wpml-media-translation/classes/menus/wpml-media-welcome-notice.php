<?php

/**
 * Class WPML_Media_Welcome_Notice
 */
class WPML_Media_Welcome_Notice implements IWPML_Action {
	const USER_META = WPML_Media_Welcome_Notice_Factory::USER_META;
	const ACKNOWLEDGE_ACTION = 'acknowledge-wpml-media-welcome-message';
	const ACKNOWLEDGED = 'acknowledged';
	const DISMISS_ACTION = 'dismiss_wpml_media_welcome_message';
	const DISMISSED = 'dismissed';
	const TOGGLE_ACTION = 'toggle-wpml-media-welcome-message';

	const NONCE_KEY = 'wpml-media-welcome-message';

	const DOC_URL = 'https://wpml.org/?page_id=113610';

	/**
	 * @var bool
	 */
	private $is_tm_dashboard = false;

	/**
	 * WPML_Media_Welcome_Notice constructor.
	 *
	 * @param bool $is_tm_dashboard
	 */
	public function __construct( $is_tm_dashboard = false ) {
		$this->is_tm_dashboard = $is_tm_dashboard;
	}

	public function add_hooks() {
		add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
		add_action( 'init', array( $this, 'track_documentation_visit' ) );

		add_action( 'admin_head', array( $this, 'enqueue_res' ) );
		add_action( 'wp_ajax_' . self::DISMISS_ACTION, array( $this, 'dismiss' ) );
		add_action( 'wp_ajax_' . self::TOGGLE_ACTION, array( $this, 'toggle' ) );
	}

	public function enqueue_res() {
		wp_enqueue_script( 'wpml-media-welcome-notice', WPML_MEDIA_URL . '/res/js/menu/welcome-notice.js', array( 'jquery' ), false, true );
		wp_localize_script( 'wpml-media-welcome-notice', 'wpmlMediaWelcomeNotice', array(
			'dismissAjaxAction' => self::DISMISS_ACTION,
			'toggleAjaxAction'  => self::TOGGLE_ACTION,
			'nonce'             => wp_create_nonce( self::NONCE_KEY ),
		) );
		wp_enqueue_style( 'wpml-media-welcome-notice', WPML_MEDIA_URL . '/res/css/welcome-notice.css' );
	}

	public function track_documentation_visit() {
		if ( isset( $_GET['action'] ) && self::ACKNOWLEDGE_ACTION === $_GET['action'] ) {
			if ( isset( $_GET['nonce'] ) && wp_create_nonce( self::NONCE_KEY ) === $_GET['nonce'] ) {
				$this->acknowledge();
			}
			wp_redirect( $_GET['redirect_to'] );
		}
	}

	public function welcome_notice() {
		$url    = admin_url( add_query_arg( array(
			'action'      => self::ACKNOWLEDGE_ACTION,
			'nonce'       => wp_create_nonce( self::NONCE_KEY ),
			'redirect_to' => self::DOC_URL
		), 'index.php' ) );
		$link   = '<a href="' . $url . '" target="_blank" class="wpml-external-link">' .
		          esc_html__( 'Media Translation documentation', 'wpml-media' ) . '</a>';
		$button = '<a href="' . $url . '" target="_blank" class="button button-primary button-wpml button-lg">' .
		          esc_html__( 'Learn more', 'wpml-media' ) . '</a>';

		if ( $this->is_new_install() ) {
			$title = esc_html__( 'Learn how to translate media', 'wpml-media' );
			$body1 = esc_html__( 'WPML allows you to use different media (images, etc.) for translated content. The Media Translation process is integrated with WPML’s content translation, but requires additional steps.', 'wpml-media' );
			$body2 = sprintf( esc_html__( 'Before you start, we highly recommend that you review the %s. Then, you’ll be able to close this message.', 'wpml-media' ), $link );
		} else {
			$title = esc_html__( 'Media Translation is completely different now', 'wpml-media' );
			$body1 = esc_html__( 'This release of Media Translation includes a complete new workflow, with complete new features and possibilities.', 'wpml-media' );
			$body2 = sprintf( esc_html__( 'Even if you’re already familiar with WPML, you should read the new %s. Then, you’ll be able to close this message.', 'wpml-media' ), $link );
		}
		$minimize_label = esc_html__( 'Minimize', 'wpml-media' );
		$maximize_label = esc_html__( 'Maximize', 'wpml-media' );
		$is_minimized   = $this->is_minimized();
		?>

		<div id="wpml-media-welcome-notice" style="<?php echo $this->is_tm_dashboard ? 'display:none' : ''; ?>"
		     class="notice otgs-notice otgs-is-dismissible wpml-media-welcome-notice <?php echo $is_minimized && ! $this->is_tm_dashboard ? 'minimized' : 'expanded'; ?>">
			<a class="js-dismiss notice-dismiss<?php if ( ! $this->can_dismiss() ): ?> hidden<?php endif ?>">
				<span class="screen-reader-text"><?php esc_html_e( 'Dismiss', 'wpml-media' ) ?></span></a>
			<div class="wpml-media-welcome-notice-bg"><i class="otgs-ico-wpml"></i></div>
			<div class="wpml-media-welcome-notice-content">
				<h3 class="wpml-media-welcome-notice-header">
					<?php echo $title ?>
				</h3>
				<div class="js-body wpml-media-welcome-notice-body">
					<p><?php echo $body1 ?></p>
					<p><?php echo $body2 ?></p>
				</div>
			</div>
			<div class="wpml-media-welcome-notice-action">
				<?php echo $button ?>
			</div>
			<?php if ( ! $this->is_tm_dashboard ): ?>
				<a class="js-toggle wpml-media-welcome-notice-toggle" data-alt-text="<?php
				echo $is_minimized ? esc_attr( $minimize_label ) : esc_attr( $maximize_label ); ?>">
					<?php echo $is_minimized ? $maximize_label : $minimize_label; ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}

	private function can_dismiss() {
		$meta = get_user_meta( get_current_user_id(), self::USER_META, true );

		return isset ( $meta['status'] ) && self::ACKNOWLEDGED === $meta['status'];
	}

	private function acknowledge() {
		$user_id = get_current_user_id();
		$meta    = get_user_meta( $user_id, self::USER_META, true );
		if ( empty( $meta ) ) {
			$meta = array( 'status' => self::ACKNOWLEDGED );
		} else {
			$meta['status'] = self::ACKNOWLEDGED;
		}
		update_user_meta( $user_id, self::USER_META, $meta );
	}

	public function dismiss() {
		if ( isset( $_POST['nonce'] ) && $_POST['nonce'] === wp_create_nonce( self::NONCE_KEY ) ) {
			$user_id        = get_current_user_id();
			$meta           = get_user_meta( $user_id, self::USER_META, true );
			$meta['status'] = self::DISMISSED;
			update_user_meta( $user_id, self::USER_META, $meta );
		}
	}

	private function is_new_install() {
		return ! get_option( 'wpml_media_upgraded_from_prior_2_3_0' );
	}

	private function is_minimized() {
		$meta = get_user_meta( get_current_user_id(), self::USER_META, true );

		return isset( $meta ) && ! empty( $meta['minimized'] );
	}

	public function toggle() {
		if ( isset( $_POST['nonce'] ) && $_POST['nonce'] === wp_create_nonce( self::NONCE_KEY ) ) {
			$user_id = get_current_user_id();
			$meta    = get_user_meta( $user_id, self::USER_META, true );
			if ( empty( $meta ) ) {
				$meta = array( 'minimized' => 1 );
			} else {
				$meta['minimized'] = (int) ( empty( $meta['minimized'] ) && 1 );
			}
			update_user_meta( $user_id, self::USER_META, $meta );
		}
	}

}