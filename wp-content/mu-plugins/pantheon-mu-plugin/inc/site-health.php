<?php
/**
 * Pantheon Site Health Modifications
 *
 * @package pantheon
 */

namespace Pantheon\Site_Health;

use function __;
use function add_action;
use function add_filter;
use function array_filter;
use function defined;
use function esc_attr;
use function esc_html;
use function esc_html__;
use function esc_html_e;
use function esc_url;
use function get_option;
use function get_plugin_data;
use function in_array;
use function number_format_i18n;
use function printf;
use function sprintf;
use function ucfirst;
use function wp_kses_post;

use const WP_PLUGIN_DIR;

// If on Pantheon...
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
	add_filter( 'site_status_tests', __NAMESPACE__ . '\\site_health_mods' );
	add_filter( 'site_status_tests', __NAMESPACE__ . '\\object_cache_tests' );
	if ( ! defined( 'PANTHEON_COMPATIBILITY' ) || PANTHEON_COMPATIBILITY ) {
		add_filter( 'site_health_navigation_tabs', __NAMESPACE__ . '\\add_compatibility_tab' );
		add_action( 'site_health_tab_content', __NAMESPACE__ . '\\output_compatibility_content' );
		add_filter( 'site_status_tests', __NAMESPACE__ . '\\compatibility_checks' );
		add_action( 'wp_ajax_health-check-test-compatibility', __NAMESPACE__ . '\\test_compatibility_ajax' );
	}
}


/**
 * Output the Pantheon Compatibility tab content in Site Health.
 *
 * @param string $tab
 *
 * @return void
 */
function output_compatibility_content( $tab ) {
	if ( 'compatibility' !== $tab ) {
		return;
	}
	?>

	<div class="health-check-body health-check-compatibility-tab hide-if-no-js">
		<h2>
			<?php esc_html_e( 'Pantheon Compatibility', 'pantheon' ); ?>
		</h2>

		<p>
			<?php
			printf(
				wp_kses_post(
					/* translators: %s: URL to Known Issues page. */
					__( 'This page lists active plugins that have known compatibility issues with Pantheon\'s infrastructure. For additional details, see the <a href="%s" target="_blank">Known Issues</a> page.', 'pantheon' )
				),
				esc_url( 'https://docs.pantheon.io/plugins-known-issues' )
			);
			?>
		</p>

		<div id="health-check-compatibility" class="health-check-accordion">

			<?php

			$info = [
				'automatic' => [
					'label' => esc_html__( 'Automatic Fixes', 'pantheon' ),
					'description' => esc_html__( 'Compatibility with the following plugins has been automatically added.', 'pantheon' ),
					'fields' => get_option( 'pantheon_applied_fixes' ),
					'show_count' => true,
				],
				'manual' => [
					'label' => esc_html__( 'Manual Fixes', 'pantheon' ),
					'description' => esc_html__( 'Compatibility with the following plugins needs to be manually applied.', 'pantheon' ),
					'fields' => get_compatibility_manual_fixes(),
					'show_count' => true,
				],
				'notes' => [
					'label' => esc_html__( 'Needs Review', 'pantheon' ),
					'description' => esc_html__( 'Compatibility with the following plugins needs to be reviewed.', 'pantheon' ),
					'fields' => get_compatibility_review_fixes(),
					'show_count' => true,
				],
			];
			foreach ( $info as $section => $details ) :
				if ( empty( $details['fields'] ) ) {
					continue;
				}

				?>
				<h3 class="health-check-accordion-heading">
					<button aria-expanded="false" class="health-check-accordion-trigger" aria-controls="health-check-accordion-block-<?php echo esc_attr( $section ); ?>" type="button">
					<span class="title">
						<?php
						echo esc_html( $details['label'] );
						if ( isset( $details['show_count'] ) && $details['show_count'] ) {
							printf( '(%s)', esc_html( number_format_i18n( count( $details['fields'] ) ) ) );
						}
						?>
					</span>
						<span class="icon"></span>
					</button>
				</h3>

				<div id="health-check-accordion-block-<?php echo esc_attr( $section ); ?>" class="health-check-accordion-panel" hidden="hidden">
					<?php
					if ( ! empty( $details['description'] ) ) {
						printf( '<p>%s</p>', esc_html( $details['description'] ) );
					}

					output_compatibility_status_table( $details['fields'] );
					?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Output the compatibility status table.
 *
 * @param array $plugins
 * @param bool $output
 * @param bool $incompatible True if only incompatible plugin issues should be displayed.
 */
function output_compatibility_status_table( $plugins, $output = true, $incompatible = false ) {
	ob_start();
	?>
	<table class='widefat striped health-check-table' role='presentation'>
		<thead>
		<tr>
			<th><?php esc_html_e( 'Plugin', 'pantheon' ); ?></th>
			<th><?php esc_html_e( 'Compatibility Status', 'pantheon' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php

		// Filter out incompatible plugins. This allows us to re-use the status table for different types of compatibility issues.
		if ( $incompatible && ( ! isset( $plugins['plugin_compatibility'] ) || $plugins['plugin_compatibility'] !== 'incompatible' ) ) {
			$plugins = array_filter( $plugins, function ( $plugins ) {
				return $plugins['plugin_compatibility'] === 'incompatible';
			} );
		}

		foreach ( $plugins as $field ) {
			/* translators: %s: A plugin's compatibility status. */
			$status = sprintf( __( '%s', 'pantheon' ), ucfirst( $field['plugin_status'] ) );
			/* translators: %s: A plugin's compatibility status message. */
			$message = sprintf( __( '%s', 'pantheon' ), ucfirst( $field['plugin_message'] ) );
			$values = '<ul>';
			$values .= '<li><b>' . $status . '</b></li>';
			$values .= '<li>' . $message . '</li>';
			$values .= '</ul>';
			printf(
				/* translators: %s: Plugin's name. */
				'<tr><td>' . esc_html__( '%s', 'pantheon' ) . '</td><td>%s</td></tr>',
				esc_html( $field['plugin_name'] ),
				wp_kses_post( $values )
			);
		}
		?>
		</tbody>
	</table>
	<?php
	$table = ob_get_clean();
	if ( $output ) {
		echo wp_kses_post( $table );
	}

	return $table;
}

/**
 * Register the Pantheon Compatibility tab in Site Health.
 *
 * @param array $tabs
 *
 * @return array
 */
function add_compatibility_tab( $tabs ) {
	$tabs['compatibility'] = esc_html__( 'Pantheon Compatibility', 'pantheon' );

	return $tabs;
}


/**
 * Get list of plugins that require manual fixes.
 *
 * @return array[]
 */
function get_compatibility_manual_fixes() {
	$plugins = [
		'big-file-uploads' => [
			'plugin_status' => esc_html__( 'Manual Fix Required', 'pantheon' ),
			'plugin_slug' => 'tuxedo-big-file-uploads/tuxedo_big_file_uploads.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#big-file-uploads'
				)
			),
		],
		'jetpack' => [
			'plugin_status' => esc_html__( 'Manual Fix Required', 'pantheon' ),
			'plugin_slug' => 'jetpack/jetpack.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#jetpack'
				)
			),
		],
		'wordfence' => [
			'plugin_status' => esc_html__( 'Manual Fix Required', 'pantheon' ),
			'plugin_slug' => 'wordfence/wordfence.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#wordfence'
				)
			),
		],
		'wpml' => [
			'plugin_status' => esc_html__( 'Manual Fix Required', 'pantheon' ),
			'plugin_slug' => 'sitepress-multilingual-cms/sitepress.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#wpml-the-wordpress-multilingual-plugin'
				)
			),
		],
		'wp-cerber' => [
			'plugin_status' => esc_html__( 'Manual Fix Required', 'pantheon' ),
			'plugin_slug' => 'wp-cerber/wp-cerber.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'WP Cerber conflicts with Pantheon\'s Global CDN caching. Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#wp-cerber'
				)
			),
		],
	];

	return add_plugin_names_to_known_issues(
		array_filter( $plugins, static function ( $plugin ) {
			return in_array( $plugin['plugin_slug'], get_option( 'active_plugins' ), true );
		} )
	);
}

/**
 * Query WP for plugin names to include in compatibility list.
 *
 * @param array $plugins
 *
 * @return array[]
 */
function add_plugin_names_to_known_issues( $plugins ) {
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	foreach ( $plugins as $key => $plugin ) {
		$file = WP_PLUGIN_DIR . '/' . $plugin['plugin_slug'];
		if ( ! file_exists( $file ) ) {
			$plugins[ $key ]['plugin_name'] = ucfirst( $key );
		} else {
			$plugin_data = get_plugin_data( $file, false, false );
			$plugins[ $key ]['plugin_name'] = $plugin_data['Name'];
		}
	}

	/**
	 * Allow the list of plugins with known issues to be filtered.
	 *
	 * @param array $plugins The list of plugins with known issues.
	 */
	return apply_filters( 'pantheon_compatibility_known_issues_plugins', $plugins );
}

/**
 * Get list of plugins that require review.
 *
 * @return array[]
 */
function get_compatibility_review_fixes() {
	$plugins = [
		'raptive-ads' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'raptive-ads/adthrive-ads.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#adthrive-ads'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'all-in-one-wp-migration' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'all-in-one-wp-migration/all-in-one-wp-migration.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#all-in-one-wp-migration'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'bookly' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'bookly-responsive-appointment-booking-tool/main.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#bookly'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'coming-soon' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'coming-soon/coming-soon.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#coming-soon'
				)
			),
		],
		'disable-json-api' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'disable-json-api/disable-json-api.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#disable-rest-api-and-require-jwt--oauth-authentication'
				)
			),
		],
		'divi-builder' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'divi-builder/divi-builder.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#divi-wordpress-theme--visual-page-builder'
				)
			),
		],
		'elementor' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'elementor/elementor.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#elementor'
				)
			),
		],
		'facetwp' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'facetwp/index.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#facetwp'
				)
			),
		],
		'cookie-law-info' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'cookie-law-info/cookie-law-info.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#gdpr-cookie-consent'
				)
			),
		],
		'h5p' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'h5p/h5p.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#h5p'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'hm-require-login' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'hm-require-login/hm-require-login.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#hm-require-login'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'hummingbird-performance' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'hummingbird-performance/wp-hummingbird.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#hummingbird'
				)
			),
		],
		'hyperdb' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'hyperdb/db.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#hyperdb'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'iwp-client' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'iwp-client/init.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#infinitewp'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'instashow' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'instashow/instashow.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#instashow'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'wp-maintenance-mode' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'wp-maintenance-mode/wp-maintenance-mode.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#maintenance-mode'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'worker' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'worker/init.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#managewp-worker'
				)
			),
		],
		'monarch' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'monarch/monarch.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#monarch-social-sharing'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'new-relic' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'new-relic/new-relic.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#new-relic-reporting-for-wordpress'
				)
			),
		],
		'object-sync-for-salesforce' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'object-sync-for-salesforce/object-sync-for-salesforce.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#object-sync-for-salesforce'
				)
			),
		],
		'one-click-demo-import' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'one-click-demo-import/one-click-demo-import.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#one-click-demo-import'
				)
			),
		],
		'posts-to-posts' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'posts-to-posts/posts-to-posts.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#posts-2-posts'
				)
			),
		],
		'query-monitor' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'query-monitor/query-monitor.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#query-monitor'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'site24x7' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'site24x7/site24x7.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#site24x7'
				)
			),
		],
		'wp-smush-pro' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'wp-smush-pro/wp-smush-pro.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#smush-pro'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'better-wp-security' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'better-wp-security/better-wp-security.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#solid-security-previously-ithemes-security'
				)
			),
		],
		'unbounce' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'unbounce/unbounce.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#unbounce-landing-pages'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'unyson' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'unyson/unyson.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#unyson-theme-framework'
				)
			),
		],
		'updraftplus' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'updraftplus/updraftplus.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#updraft--updraft-plus-backup'
				)
			),
		],
		'weather-station' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'weather-station/weather-station.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#weather-station'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'webp-express' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'webp-express/webp-express.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#webp-express'
				)
			),
		],
		'woocommerce' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'woocommerce/woocommerce.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#woocommerce'
				)
			),
		],
		'download-manager' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'download-manager/download-manager.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#wordpress-download-manager'
				)
			),
		],
		'wp-all-import' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'wp-all-import/wp-all-import.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#wp-all-import--export'
				)
			),
		],
		'wp-migrate-db' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'wp-migrate-db/wp-migrate-db.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#wp-migrate-db'
				)
			),
		],
		'wp-phpmyadmin' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'wp-phpmyadmin/wp-phpmyadmin.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#wp-phpmyadmin'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'wp-reset' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'wp-reset/wp-reset.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#wp-reset'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
		'wp-ban' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'wp-ban/wp-ban.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#wp-ban'
				)
			),
		],
		'wpfront-notification-bar' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'wpfront-notification-bar/wpfront-notification-bar.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#wpfront-notification-bar'
				)
			),
		],
		'yoast-seo' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'wordpress-seo/wp-seo.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#yoast-seo'
				)
			),
		],
		'yoast-indexables' => [
			'plugin_status' => esc_html__( 'Partial Compatibility', 'pantheon' ),
			'plugin_slug' => 'yoast-seo/wp-seo.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#yoast-indexables'
				)
			),
		],
		'phastpress' => [
			'plugin_status' => esc_html__( 'Incompatible', 'pantheon' ),
			'plugin_slug' => 'phastpress/phastpress.php',
			'plugin_message' => wp_kses_post(
				sprintf(
					/* translators: %s: the link to relevant documentation. */
					__( 'Read more about the issue <a href="%s" target="_blank">here</a>.', 'pantheon' ),
					'https://docs.pantheon.io/plugins-known-issues#phastpress'
				)
			),
			'plugin_compatibility' => 'incompatible',
		],
	];

	return add_plugin_names_to_known_issues(
		array_filter( $plugins, static function ( $plugin ) {
			return in_array( $plugin['plugin_slug'], get_option( 'active_plugins' ), true );
		} )
	);
}

/**
 * Modify the Site Health tests.
 *
 * @param array $tests The Site Health tests.
 *
 * @return array
 */
function site_health_mods( $tests ) {
	// Remove checks that aren't relevant to Pantheon environments.
	unset( $tests['direct']['update_temp_backup_writable'] );
	unset( $tests['direct']['available_updates_disk_space'] );
	unset( $tests['async']['background_updates'] );

	return $tests;
}

/**
 * Add compatibility checks.
 *
 * @param array $tests The Site Health tests.
 *
 * @return array
 */
function compatibility_checks( $tests ) {
	$tests['async']['compatibility'] = [
		'label' => __( 'Pantheon Compatibility', 'pantheon' ),
		'test' => 'test-compatibility',
		'has_rest' => false,
		'async_direct_test' => __NAMESPACE__ . '\\test_compatibility',
	];

	return $tests;
}

/**
 * Check for compatibility issues and return it as a JSON response.
 */
function test_compatibility_ajax() {
	wp_send_json_success( test_compatibility() );
}

/**
 * Check for compatibility issues.
 *
 * @return array
 */
function test_compatibility() {
	$manual_fixes = get_compatibility_manual_fixes();
	$review_fixes = get_compatibility_review_fixes();

	if ( empty( $manual_fixes ) && empty( $review_fixes ) ) {
		return [
			'label' => __( 'Your site is stable', 'pantheon' ),
			'status' => 'good',
			'badge' => [
				'label' => __( 'Pantheon', 'pantheon' ),
				'color' => 'green',
			],
			'description' => __( 'There are no known compatibility issues with your active plugins.', 'pantheon' ),
			'test' => 'compatibility',
		];
	}

	if ( ! empty( $manual_fixes ) ) {
		$manual_table = output_compatibility_status_table( $manual_fixes, false );

		$description = sprintf(
			'<p>%s</p>%s',
			__( 'There are known compatibility issues with your active plugins that require manual fixes.', 'pantheon' ),
			$manual_table
		);

		if ( ! empty( $review_fixes ) ) {
			$review_table = output_compatibility_status_table( $review_fixes, false, true );
			$description = sprintf(
				'<p>%1$s</p>%2$s<p>%3$s</p>%4$s',
				__( 'There are known compatibility issues with your active plugins that require manual fixes.', 'pantheon' ),
				$manual_table,
				__( 'There are known incompatibility issues with the following plugins.', 'pantheon' ),
				$review_table
			);
		}

		return [
			'label' => __( 'One or more active plugins require a manual compatibility fix', 'pantheon' ),
			'status' => 'critical',
			'badge' => [
				'label' => __( 'Pantheon', 'pantheon' ),
				'color' => 'red',
			],
			'description' => $description,
			'test' => 'compatibility',
		];
	}

	return [
		'label' => __( 'You should review active plugins for compatibility issues', 'pantheon' ),
		'status' => 'recommended',
		'badge' => [
			'label' => __( 'Pantheon', 'pantheon' ),
			'color' => 'orange',
		],
		'description' => sprintf(
			'<p>%s</p>%s',
			__( 'One or more active plugins may be incompatible or only partially compatible with Pantheon.', 'pantheon' ),
			output_compatibility_status_table( $review_fixes, false )
		),
		'test' => 'compatibility',
	];
}

/**
 * Add object cache tests.
 *
 * @param array $tests The Site Health tests.
 *
 * @return array
 */
function object_cache_tests( $tests ) {
	$tests['direct']['object_cache'] = [
		'label' => __( 'Object Cache', 'pantheon' ),
		'test' => __NAMESPACE__ . '\\test_object_cache',
	];

	return $tests;
}

/**
 * Check for object cache and object cache plugins.
 *
 * @return array
 */
function test_object_cache() {
	if ( ! isset( $_ENV['CACHE_HOST'] ) ) {
		$result = [
			'label' => __( 'Redis Object Cache', 'pantheon' ),
			'status' => 'critical',
			'badge' => [
				'label' => __( 'Performance', 'pantheon' ),
				'color' => 'red',
			],
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Redis object cache is not active for your site.', 'pantheon' )
			),
			'test' => 'object_cache',
		];

		return $result;
	}

	$wp_redis_active = is_plugin_active( 'wp-redis/wp-redis.php' );
	$ocp_active = is_plugin_active( 'object-cache-pro/object-cache-pro.php' );

	if ( $wp_redis_active ) {
		$result = [
			'label' => __( 'WP Redis Active', 'pantheon' ),
			'status' => 'recommended',
			'badge' => [
				'label' => __( 'Performance', 'pantheon' ),
				'color' => 'orange',
			],
			'description' => sprintf(
				'<p>%s</p><p>%s</p>',
				__( 'WP Redis is active for your site. We recommend using Object Cache Pro.', 'pantheon' ),
				// Translators: %s is a URL to the Pantheon documentation to install Object Cache Pro.
				sprintf( __( 'Visit our <a href="%s">documentation site</a> to learn how.', 'pantheon' ), 'https://docs.pantheon.io/object-cache/wordpress' )
			),
			'test' => 'object_cache',
		];

		return $result;
	}

	if ( $ocp_active ) {
		$result = [
			'label' => __( 'Object Cache Pro Active', 'pantheon' ),
			'status' => 'good',
			'badge' => [
				'label' => __( 'Performance', 'pantheon' ),
				'color' => 'green',
			],
			'description' => sprintf(
				'<p>%s</p><p>%s</p>',
				__( 'Object Cache Pro is active for your site.', 'pantheon' ),
				// Translators: %s is a URL to the Object Cache Pro documentation.
				sprintf( __( 'Visit the <a href="%s">Object Cache Pro</a> documentation to learn more.', 'pantheon' ), 'https://objectcache.pro/docs' )
			),
			'test' => 'object_cache',
		];

		return $result;
	}

	$result = [
		'label' => __( 'No Object Cache Plugin Active', 'pantheon' ),
		'status' => 'critical',
		'badge' => [
			'label' => __( 'Performance', 'pantheon' ),
			'color' => 'red',
		],
		'description' => sprintf(
			'<p>%s</p><p>%s</p>',
			__( 'Redis object cache is active for your site but you have no object cache plugin installed. We recommend using Object Cache Pro.', 'pantheon' ),
			// Translators: %s is a URL to the Pantheon documentation to install Object Cache Pro.
			sprintf( __( 'Visit our <a href="%s">documentation site</a> to learn how to install it.', 'pantheon' ), 'https://docs.pantheon.io/object-cache/wordpress' )
		),
		'test' => 'object_cache',
	];

	return $result;
}
