<?php

class WPML_Upgrade_Display_Mode_For_Posts implements IWPML_Upgrade_Command {

	const DISPLAY_MODE_SETTING = 'show_untranslated_blog_posts';

	/** @var SitePress */
	private $sitepress;

	/** @var WPML_Settings_Helper */
	private $settings;

	/** @var WPML_Notices */
	private $wpml_notices;

	public function __construct( array $args ) {
		$this->sitepress    = $args[0];
		$this->settings     = $args[1];
		$this->wpml_notices = $args[2];
	}

	/**
	 * @return bool
	 */
	public function run_admin() {

		if ( $this->sitepress->get_setting( self::DISPLAY_MODE_SETTING ) ) {

			$notice = $this->wpml_notices->create_notice( __CLASS__, $this->get_notice_content() );
			$notice->add_display_callback( array( 'WPML_Notice_Show_On_Dashboard_And_WPML_Pages', 'is_on_page' ) );
			$notice->set_css_class_types( 'info' );

			$this->wpml_notices->add_notice( $notice );

			return false;
		} else {
			return true;
		}
	}

	/**
	 * @return bool
	 */
	public function run_ajax() {
		if ( isset( $_POST['mode'] ) ) {
			if ( 'translate' === $_POST['mode'] ) {
				$this->settings->set_post_type_translatable( 'post' );
				$this->sitepress->set_setting( self::DISPLAY_MODE_SETTING, false, true );
				$this->wpml_notices->remove_notice( 'default', __CLASS__ );

				return true;
			}
			if ( 'display-as-translated' === $_POST['mode'] ) {
				$this->settings->set_post_type_display_as_translated( 'post' );
				$this->sitepress->set_setting( self::DISPLAY_MODE_SETTING, false, true );
				$this->wpml_notices->remove_notice( 'default', __CLASS__ );

				return true;
			}
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function run_frontend() {
		return false;
	}

	/**
	 * @return array
	 */
	public function get_results() {
		return array();
	}

	private function get_notice_content() {
		ob_start();

		$action    = str_replace( '_', '-', strtolower( __CLASS__ ) );
		$setup_url = WPML_Admin_URL::multilingual_setup( 7 );

		?>
		<div class="js-main-content">
			<h2><?php esc_html_e( 'Display mode for blog posts has changed', 'sitepress' ); ?></h2>

			<p><?php esc_html_e( 'Until now, your site was set to display "all blog posts", even if they are not translated. That feature is now replaced with a better and more complete translation mode.', 'sitepress' ); ?></p>

			<p><?php esc_html_e( "Which blog posts do you want to display on your site's translations?", 'sitepress' ); ?></p>

			<p><label><input type="radio" name="mode"
			                 value="display-as-translated"/> <?php esc_html_e( "Blog posts from the site's default language, or translations when they exist", 'sitepress' ); ?>
				</label></p>
			<p><label><input type="radio" name="mode"
			                 value="translate"/> <?php esc_html_e( "Only translated blog posts (never display posts from the default language on translation languages)", 'sitepress' ); ?>
				</label></p>

			<input type="button" class="button-primary" name="save" value="<?php esc_attr_e( 'Save' ); ?>"
			       disabled="disabled"/>
			<?php wp_nonce_field( $action . '-nonce', $action . '-nonce' ); ?>
		</div>
		<div class="js-thankyou-content" style="display: none">
			<p><?php echo sprintf( esc_html__( 'Thank you for choosing. You can always change your selection in %sPost Types Translation setup%s.', 'sitepress' ), '<a href="' . $setup_url . '">', '</a>' ); ?></p>
		</div>
		<script>
			jQuery( document ).ready( function ( $ ) {
				$( '.js-main-content' ).find( 'input[name=mode]' ).on( 'change', function ( e ) {
					$( '.js-main-content' ).find( 'input[name=save]' ).prop( 'disabled', false );
				} );
				$( '.js-main-content' ).find( 'input[name=save]' ).on( 'click', function ( e ) {
					$( this ).prop( 'disabled', true );
					$.ajax( {
						url: ajaxurl,
						type: "POST",
						data: {
							action: '<?php echo $action; ?>',
							nonce: $( '#<?php echo $action; ?>-nonce' ).val(),
							mode: $( 'input[name=mode]:checked', '.js-main-content' ).val()
						},
						success: function ( response ) {
							$( '.js-main-content' ).hide();
							$( '.js-thankyou-content' ).show();
						}
					} );
				} );
			} );
		</script>
		<?php

		return ob_get_clean();
	}
}