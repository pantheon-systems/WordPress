<?php
/**
 * Pantheon Site Health Modifications
 *
 * @package pantheon
 */

namespace Pantheon\Site_Health;

// If on Pantheon...
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
	add_filter( 'site_status_tests', __NAMESPACE__ . '\\site_health_mods' );
	add_filter( 'site_status_tests', __NAMESPACE__ . '\\object_cache_tests' );
}

/**
 * Modify the Site Health tests.
 *
 * @param array $tests The Site Health tests.
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
 * Add object cache tests.
 *
 * @param array $tests The Site Health tests.
 * @return array
 */
function object_cache_tests( $tests ) {
	$tests['direct']['object_cache'] = [
		'label' => __( 'Object Cache', 'pantheon' ),
		'test'  => 'test_object_cache',
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
