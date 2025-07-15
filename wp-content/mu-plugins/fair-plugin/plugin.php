<?php
/**
 * Plugin Name: FAIR - Federated and Independent Repositories
 * Description: Make your site more FAIR.
 * Version: 0.1
 * Author: FAIR Contributors
 * License: GPLv2
 * Requires at least: 4.1
 * Requires PHP: 7.2.24
 * Text Domain: fair
 * Update URI: https://api.fair.pm
 */

namespace FAIR;

const VERSION = '0.1';
const PLUGIN_DIR = __DIR__;
const PLUGIN_FILE = __FILE__;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/avatars/namespace.php';
require_once __DIR__ . '/inc/credits/namespace.php';
require_once __DIR__ . '/inc/dashboard-widgets/namespace.php';
require_once __DIR__ . '/inc/default-repo/namespace.php';
require_once __DIR__ . '/inc/disable-openverse/namespace.php';
require_once __DIR__ . '/inc/importers/namespace.php';
require_once __DIR__ . '/inc/pings/namespace.php';
require_once __DIR__ . '/inc/salts/namespace.php';
require_once __DIR__ . '/inc/settings/namespace.php';
require_once __DIR__ . '/inc/user-notification/namespace.php';
require_once __DIR__ . '/inc/version-check/namespace.php';

// External dependencies.
require_once __DIR__ . '/inc/updater/class-lite.php';

bootstrap();
