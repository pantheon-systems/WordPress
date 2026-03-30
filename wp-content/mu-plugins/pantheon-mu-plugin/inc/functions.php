<?php
/**
 * Pantheon mu-plugin helper functions
 *
 * @package pantheon
 */

namespace Pantheon;

/**
 * Helper function that returns the current WordPress version.
 *
 * @return string
 */
function _pantheon_get_current_wordpress_version(): string {
	include ABSPATH . WPINC . '/version.php';
	return $wp_version; // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
}

/**
 * Helper function to get the request headers.
 *
 * @param array $headers Optional. An array of headers to process. Defaults to $_SERVER.
 * @return array Processed headers in standard HTTP header format.
 */
function _pantheon_get_request_headers( array $headers = [] ): array {
	$headers = ! empty( $headers ) ? $headers : ( ! empty( $_SERVER ) ? $_SERVER : [] );

	if ( empty( $headers ) ) {
		return [];
	}

	foreach ( $headers as $key => $value ) {
		if ( substr( $key, 0, 5 ) !== 'HTTP_' ) {
			continue;
		}

		/**
		 * Convert HTTP headers to standard HTTP header format.
		 *
		 * We use str_replace twice so that we can use ucwords to capitalize
		 * the first letter of each word, e.g. HTTP_USER_AGENT to User-Agent.
		 */
		$header = str_replace( ' ', '-', ucwords( str_replace( '_', ' ', strtolower( substr( $key, 5 ) ) ) ) );
		$headers[ $header ] = $value;
	}

	return $headers;
}

/**
 * Helper function to get a specific header value.
 *
 * @param string $key The header key to retrieve.
 * @return string The value of the specified header, or an empty string if not found.
 */
function _pantheon_get_header( string $key ): string {
	$headers = _pantheon_get_request_headers();
	return ! empty( $headers[ $key ] ) ? esc_textarea( $headers[ $key ] ) : '';
}

/**
 * Get the Pantheon dashboard URL for the current site.
 *
 * @param string $path Optional path to append to the dashboard URL (e.g., '#dev/redis').
 * @return string The dashboard URL or empty string if not on Pantheon or invalid site ID.
 */
function _pantheon_get_dashboard_url( $path = '' ) {
	if ( ! isset( $_ENV['PANTHEON_SITE'] ) ) {
		return '';
	}

	$site_id = $_ENV['PANTHEON_SITE'];

	// Validate that PANTHEON_SITE matches expected UUID format.
	if ( ! preg_match( '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $site_id ) ) {
		return '';
	}

	$url = 'https://dashboard.pantheon.io/sites/' . $site_id;

	if ( ! empty( $path ) ) {
		$url .= $path;
	}

	return $url;
}

/**
 * Render a Pantheon-styled notice
 *
 * @param array $args {
 *     Array of arguments for the notice.
 *     @type string $type           Notice type: 'warning', 'error', 'success', 'info'. Default 'warning'.
 *     @type string $heading        Notice heading text.
 *     @type string $message        Main notice message (can include HTML).
 *     @type string $button_text    Optional button text.
 *     @type string $button_url     Optional button URL.
 *     @type string $logo_url       Optional logo URL. Default uses Pantheon logo in plugin directory.
 *     @type bool   $dismissible    Whether notice can be dismissed. Default false.
 * }
 * @return void
 */
function _pantheon_render_notice( $args = [] ) {
	$defaults = [
		'type'        => 'warning',
		'heading'     => '',
		'message'     => '',
		'button_text' => '',
		'button_url'  => '',
		'logo_url'    => plugins_url( 'assets/images/logo-fist-black.webp', __FILE__ ),
		'dismissible' => false,
	];

	$args = wp_parse_args( $args, $defaults );

	$notice_classes = [
		'notice',
		'notice-' . esc_attr( $args['type'] ),
		'pantheon-notice',
	];

	if ( $args['dismissible'] ) {
		$notice_classes[] = 'is-dismissible';
	}
	?>
	<div class="<?php echo esc_attr( implode( ' ', $notice_classes ) ); ?>">
		<div class="pantheon-notice-aside">
			<div class="pantheon-notice-icon-wrapper">
				<img src="<?php echo esc_url( $args['logo_url'] ); ?>" alt="Pantheon" width="48" height="48">
			</div>
		</div>
		<div class="pantheon-notice-content">
			<?php if ( $args['heading'] ) : ?>
				<h3><?php echo esc_html( $args['heading'] ); ?></h3>
			<?php endif; ?>

			<?php if ( $args['message'] ) : ?>
				<p><?php echo wp_kses_post( $args['message'] ); ?></p>
			<?php endif; ?>

			<?php if ( $args['button_url'] && $args['button_text'] ) : ?>
				<div class="pantheon-notice-actions">
					<p>
						<a href="<?php echo esc_url( $args['button_url'] ); ?>"
							class="button"
							target="_blank"
							rel="noopener noreferrer">
							<?php echo esc_html( $args['button_text'] ); ?>
						</a>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Enqueue Pantheon notice styles.
 *
 * @return void
 */
function _pantheon_enqueue_notice_styles() {
	wp_enqueue_style(
		'pantheon-notice',
		plugin_dir_url( __FILE__ ) . 'assets/css/pantheon-notice.css',
		[],
		PANTHEON_MU_PLUGIN_VERSION
	);
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\_pantheon_enqueue_notice_styles' );
