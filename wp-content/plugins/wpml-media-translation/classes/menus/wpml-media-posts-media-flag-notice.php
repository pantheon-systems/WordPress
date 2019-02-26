<?php

class WPML_Media_Posts_Media_Flag_Notice implements IWPML_Action {

	const PREPARE_ACTION = 'wpml-media-has-media-flag-prepare';
	const PROCESS_ACTION = 'wpml-media-has-media-flag';

	const NOTICE_ID    = 'wpml-media-posts-media-flag';
	const NOTICE_GROUP = 'wpml-media';

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * WPML_Media_Has_Media_Notice constructor.
	 *
	 * @param SitePress $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {

		if ( $this->is_wpml_media_screen() ) {
			add_filter( 'wpml_media_menu_overrides', array( $this, 'override_default_menu' ) );
		} else {
			add_action( 'admin_head', array( $this, 'add_top_notice' ) );
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_js' ) );
	}

	public function override_default_menu( $menu_elements ) {
		$menu_elements[] = array( $this, 'render_menu' );

		return $menu_elements;
	}

	public function enqueue_js() {
		$wpml_media_url = $this->sitepress->get_wp_api()->constant( 'WPML_MEDIA_URL' );
		wp_enqueue_script( 'wpml-media-setup', $wpml_media_url . '/res/js/wpml-media-posts-media-flag.js', array( 'jquery' ), false, true );
	}

	private function is_wpml_media_screen() {
		return isset( $_GET['page'] ) && 'wpml-media' === $_GET['page'];
	}

	public function add_top_notice() {

		/* translators: name ot WPML-Media plugin */
		$wpml_media = '<strong>' . __( 'WPML Media Translation', 'wpml-media' ) . '</strong>';

		/* translators: used to build a link in the "Click here to finish the setup" */
		$here_text = _x( 'here', 'Used to build a link in the "Click here to finish the setup"', 'wpml-media' );
		$here_link = '<a href="' . admin_url( 'admin.php?page=wpml-media' ) . '">' . $here_text . '</a>';

		/* translators: %1$s will be replaced with a translation of "WPML Media Translation", while %2$s is a link with the translation of the word "here" */
		$text = vsprintf(
			esc_html__( 'The %1$s setup is almost complete. Click %2$s to finish the setup.', 'wpml-media' ),
			array(
				$wpml_media,
				$here_link
			)
		);

		$notice = new WPML_Notice( self::NOTICE_ID, $text, self::NOTICE_GROUP );
		$notice->set_css_class_types( 'notice-warning' );
		$notice->set_hideable( false );
		$notice->set_dismissible( false );
		$notice->set_collapsable( false );
		$notice->add_exclude_from_page( 'wpml-media' );
		$notice->add_capability_check( array( 'manage_options' ) );
		$wpml_admin_notices = wpml_get_admin_notices();
		$wpml_admin_notices->add_notice( $notice );

	}

	public function render_menu() {
		?>
		<div class="wrap wpml-media-setup">
			<h2><?php esc_html_e( 'Setup required', 'wpml-media' ) ?></h2>
			<div
				id="wpml-media-posts-media-flag"
				class="notice notice-warning"
				style="padding-bottom:8px"

				data-prepare-action="<?php echo esc_attr( self::PREPARE_ACTION ); ?>"
				data-prepare-nonce="<?php echo wp_create_nonce( self::PREPARE_ACTION ); ?>"

				data-process-action="<?php echo esc_attr( self::PROCESS_ACTION ); ?>"
				data-process-nonce="<?php echo wp_create_nonce( self::PROCESS_ACTION ); ?>"

			>
				<p>
					<?php esc_html_e( 'In order to get WPML Media Translation fully working, you need to run this set up which takes only a few moments depending on the total number of posts in your WordPress install.', 'wpml-media' ); ?>
				</p>
				<input type="button" class="button-primary alignright"
					   value="<?php esc_attr_e( 'Finish setup', 'wpml-media' ) ?>"/>

				<span class="spinner"> </span>
				<p class="alignleft status description"></p>
				<br clear="all"/>
			</div>
		</div>
		<?php
	}

}
