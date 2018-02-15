<?php
/**
 * Class Strong_Testimonials_Settings
 */
class Strong_Testimonials_Settings {

    const DEFAULT_TAB = 'general';

    public static $callbacks;

	/**
	 * Strong_Testimonials_Settings constructor.
	 */
	public function __construct() {}

	/**
	 * Initialize.
	 */
	public static function init() {
		self::add_actions();
	}

	/**
	 * Add actions and filters.
	 */
	public static function add_actions() {
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Register settings
	 */
	public static function register_settings() {
		self::$callbacks = apply_filters( 'wpmtst_settings_callbacks', array() );
        do_action( 'wpmtst_register_settings' );
	}

	/**
	 * Settings page
	 */
	public static function settings_page() {
		if ( ! current_user_can( 'strong_testimonials_options' ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : self::DEFAULT_TAB;
		$url        = admin_url( 'edit.php?post_type=wpm-testimonial&page=testimonial-settings' );
		?>
		<div class="wrap wpmtst">

			<h1><?php echo apply_filters( 'wpmtst_cpt_singular_name', __( 'Testimonial', 'strong-testimonials' ) ); ?> <?php _e( 'Settings' ); ?></h1>

			<?php if( isset( $_GET['settings-updated'] ) ) : ?>
				<div id="message" class="updated notice is-dismissible">
					<p><?php _e( 'Settings saved.' ) ?></p>
				</div>
			<?php endif; ?>

			<h2 class="nav-tab-wrapper">
				<?php do_action( 'wpmtst_settings_tabs', $active_tab, $url ); ?>
			</h2>

			<form id="<?php esc_attr_e( $active_tab ); ?>-form" method="post" action="options.php">
				<?php
				if ( isset( self::$callbacks[ $active_tab ] ) && wpmtst_callback_exists( self::$callbacks[ $active_tab ] ) ) {
					call_user_func( self::$callbacks[ $active_tab ] );
				} else {
					call_user_func( self::$callbacks[ self::DEFAULT_TAB ] );
				}
				?>
				<p class="submit-row">
					<?php submit_button( '', 'primary', 'submit-form', false ); ?>
					<?php do_action( 'wpmtst_settings_submit_row'); ?>
				</p>
			</form>

		</div><!-- .wrap -->
		<?php
	}

}

Strong_Testimonials_Settings::init();
