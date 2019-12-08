<?php
/**
 * Class Google\Site_Kit_Dev_Settings\Admin\Settings_Screen
 *
 * @package   Google\Site_Kit_Dev_Settings
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://sitekit.withgoogle.com
 */

namespace Google\Site_Kit_Dev_Settings\Admin;

use Google\Site_Kit_Dev_Settings\Setting;

/**
 * Class for the admin screen to control Site Kit developer settings.
 *
 * @since 0.1.0
 */
class Settings_Screen {

	/**
	 * The admin page slug.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	const SLUG = 'googlesitekit-dev-settings';

	/**
	 * The admin page parent slug.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	const PARENT_SLUG = 'googlesitekit-dashboard';

	/**
	 * Setting instance.
	 *
	 * @since 0.1.0
	 * @var Setting
	 */
	protected $setting;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Setting $setting Setting instance.
	 */
	public function __construct( Setting $setting ) {
		$this->setting = $setting;
	}

	/**
	 * Registers the menu item for the admin screen.
	 *
	 * @since 0.1.0
	 */
	public function register_menu() {
		if ( class_exists( 'Google\Site_Kit\Permissions' ) ) {
			if ( defined( 'Google\Site_Kit\Permissions::SETUP' ) ) {
				$capability = \Google\Site_Kit\Permissions::SETUP;
			} else {
				$capability = \Google\Site_Kit\Permissions::MANAGE_OPTIONS;
			}
		} else {
			$capability = \Google\Site_Kit\Core\Permissions\Permissions::SETUP;
		}
		$hook_suffix = add_submenu_page(
			self::PARENT_SLUG,
			__( 'Site Kit Developer Settings', 'google-site-kit-dev-settings' ),
			__( 'Developer Settings', 'google-site-kit-dev-settings' ),
			$capability,
			self::SLUG,
			array( $this, 'render' )
		);
		add_action(
			"load-{$hook_suffix}",
			function() {
				$this->add_settings_ui();
			}
		);
	}

	/**
	 * Renders the admin screen.
	 *
	 * @since 0.1.0
	 */
	public function render() {
		?>
		<style type="text/css">
			.external-link > .dashicons {
				font-size: 16px;
				text-decoration: none;
			}

			.external-link:hover > .dashicons,
			.external-link:focus > .dashicons {
				text-decoration: none;
			}
		</style>
		<div class="wrap">
			<h1>
				<?php esc_html_e( 'Site Kit Developer Settings', 'google-site-kit-dev-settings' ); ?>
			</h1>

			<form action="options.php" method="post" novalidate="novalidate">
				<?php settings_fields( self::SLUG ); ?>
				<?php do_settings_sections( self::SLUG ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Adds settings sections and fields.
	 *
	 * @since 0.1.0
	 */
	protected function add_settings_ui() {
		add_settings_section(
			'general',
			__( 'General', 'google-site-kit-dev-settings' ),
			null,
			self::SLUG
		);
		add_settings_section(
			'authentication',
			__( 'Authentication', 'google-site-kit-dev-settings' ),
			function() {
				$gcp_helper_url = $this->get_gcp_helper_url();
				?>
				<p class="description">
					<?php esc_html_e( 'Providing each of these values will override the default authentication mechanism, using your own Google Cloud Platform credentials instead.', 'google-site-kit-dev-settings' ); ?>
					<?php
					printf(
						'<a class="external-link" href="%1$s" target="_blank">%2$s<span class="screen-reader-text"> %3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>',
						$gcp_helper_url,
						esc_html__( 'Create your credentials', 'google-site-kit-dev-settings' ),
						/* translators: accessibility text */
						esc_html__( '(opens in a new tab)', 'google-site-kit-dev-settings' )
					);
					?>
				</p>
				<?php
			},
			self::SLUG
		);

		$option       = $this->setting->get();
		$sub_settings = $this->setting->get_sub_settings();

		foreach ( $sub_settings as $sub_setting ) {
			add_settings_field(
				$sub_setting['id'],
				$sub_setting['title'],
				function( $args ) use ( $sub_setting, $option ) {
					$value = ! empty( $option[ $sub_setting['id'] ] ) ? $option[ $sub_setting['id'] ] : '';

					$class       = ! empty( $sub_setting['class'] ) ? ' class="' . esc_attr( $sub_setting['class'] ) . '"' : '';
					$describedby = ! empty( $sub_setting['description'] ) ? ' aria-describedby="' . esc_attr( Setting::OPTION_NAME . '-' . $sub_setting['id'] . '-description' ) . '"' : '';
					?>
					<input
						type="text"
						id="<?php echo esc_attr( Setting::OPTION_NAME . '-' . $sub_setting['id'] ); ?>"
						name="<?php echo esc_attr( Setting::OPTION_NAME . '[' . $sub_setting['id'] . ']' ); ?>"
						value="<?php echo esc_attr( $value ); ?>"
						<?php echo $class . $describedby; /* phpcs:ignore WordPress.Security.EscapeOutput */ ?>
					/>
					<?php
					if ( ! empty( $sub_setting['description'] ) ) {
						?>
						<p id="<?php echo esc_attr( Setting::OPTION_NAME . '-' . $sub_setting['id'] . '-description' ); ?>" class="description">
							<?php echo esc_html( $sub_setting['description'] ); ?>
						</p>
						<?php
					}
				},
				self::SLUG,
				$sub_setting['section'],
				array( 'label_for' => Setting::OPTION_NAME . '-' . $sub_setting['id'] )
			);
		}
	}

	/**
	 * Gets the URL to a helper page on developers.google.com to simplify creating credentials on Google Cloud Platform.
	 *
	 * @since 0.3.0
	 *
	 * @return string GCP helper URL.
	 */
	protected function get_gcp_helper_url() {
		$external_sitename = html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$external_sitename = str_replace( '&', 'and', $external_sitename );
		$external_sitename = trim( preg_replace( '/([^A-Za-z0-9 ]+|google)/i', '', $external_sitename ) );
		if ( strlen( $external_sitename ) < 4 ) {
			$external_sitename .= ' Site Kit';
		}

		$external_page_params = array(
			'sitename' => substr( $external_sitename, 0, 30 ), // limit to 30 chars.
			'siteurl'  => untrailingslashit( home_url() ),
		);

		return add_query_arg( $external_page_params, 'https://developers.google.com/web/site-kit' );
	}

	/**
	 * Gets the URL to generate credentials on Google Cloud Platform.
	 *
	 * @since 0.3.0
	 *
	 * @return string GCP URL.
	 */
	protected function get_gcp_url() {
		return 'https://console.cloud.google.com/apis/credentials';
	}
}
