<?php
if ( !defined('ABSPATH') ) {
	die();
}

/** @var string $pluginFile Should be provided by the including file. */

$log = array();
$log[] = sprintf(
	'[OK] Main plugin file: %s',
	$pluginFile
);

$log[] = sprintf(
	'[Info] WordPress version: %s',
	$GLOBALS['wp_version']
);

$log[] = sprintf(
	'[Info] WP_PLUGIN_DIR: %s',
	WP_PLUGIN_DIR
);

$log[] = sprintf(
	'[Info] WP_PLUGIN_URL: %s',
	WP_PLUGIN_URL
);

$log[] = sprintf(
	'[Info] WPMU_PLUGIN_DIR: %s',
	WPMU_PLUGIN_DIR
);

$log[] = sprintf(
	'[Info] WPMU_PLUGIN_URL: %s',
	WPMU_PLUGIN_URL
);

$expectedPluginRoot = dirname(dirname(__FILE__));
$actualPluginRoot = dirname($pluginFile);

if ( $expectedPluginRoot === $actualPluginRoot ) {
	$log[] = sprintf(
		'[OK] Plugin root directory is "%s"',
		$actualPluginRoot
	);
} else {
	$log[] = sprintf(
		'[Error] Actual plugin directory: "%s", expected: "%s"',
		$actualPluginRoot,
		$expectedPluginRoot
	);
}

$requiredFiles = array(
	'css/menu-editor.css',
	'css/jquery.qtip.min.css',
	'js/menu-editor.js',
	'js/menu-highlight-fix.js',
	'js/jquery.sort.js',
	'js/jquery.qtip.min.js',
	'js/jquery.json.js',
	'images/cut.png',
	'images/delete.png',
	'images/page_white_add.png',
	'images/spinner.gif',
	'includes/editor-page.php',
	'includes/menu-editor-core.php',
	'modules/access-editor/access-editor-template.php',
	'includes/menu-item.php',
	'menu-editor.php',
	'uninstall.php',
);

foreach($requiredFiles as $filename) {
	$fullPath = dirname($pluginFile) . '/' . $filename;
	if ( is_readable($fullPath) ) {
		$log[] = sprintf(
			'[OK] File exists: %s',
			$fullPath
		);
	} else {
		$log[] = sprintf(
			'[Error] File does not exist: %s',
			$fullPath
		);
	}
}

foreach($requiredFiles as $filename) {
	if ( !preg_match('@\.(css|js|png)$@', $filename) ) {
		continue;
	}

	$url = plugins_url($filename, $pluginFile);
	$log[] = ame_test_url_access($url, $filename);
}

echo '<pre>';
$divider = str_repeat('-', 50);
echo "File consistency checks:\n", $divider, "\n";
foreach($log as $message) {
	echo $message, "\n";
}

//Test for buggy plugins_url filters.
echo $divider, "\nTesting for problems with the 'plugins_url' hook...\n";
add_filter('plugins_url', 'ame_plugins_url_test_first', -9999, 3);
add_filter('plugins_url', 'ame_plugins_url_test_last', 9999, 3);

$url = plugins_url('css/menu-editor.css', $pluginFile);

remove_filter('plugins_url', 'ame_plugins_url_test_first', -9999, 3);
remove_filter('plugins_url', 'ame_plugins_url_test_last', 9999, 3);

function ame_plugins_url_test_first($url, $path = '', $plugin = '') {
	printf(
		'[Info] plugins_url() output before plugin hooks: %s' . "\n",
		esc_html($url)
	);
	echo ame_test_url_access($url, 'css/menu-editor.css'), "\n";
	return $url;
}

function ame_plugins_url_test_last($url, $path = '', $plugin = '') {
	printf(
		'[Info] plugins_url() output after plugin hooks: %s' . "\n",
		esc_html($url)
	);
	echo ame_test_url_access($url, 'css/menu-editor.css'), "\n";
	return $url;
}

function ame_test_url_access($url, $filename) {
	$result = wp_remote_get($url);

	if ( is_wp_error($result) ) {
		return sprintf(
			'[Error] Can not load URL: %s (%s)',
			esc_html($url),
			$result->get_error_message()
		);
	} else if ( $result['response']['code'] == 200 ) {
		return sprintf(
			'[OK] URL is accessible: %s',
			esc_html($url)
		);
	} else {
		return sprintf(
			'[Error] Can no load "%s", URL : %s (%d %s)',
			esc_html($filename),
			esc_html($url),
			$result['response']['code'],
			$result['response']['message']
		);
	}
}

echo $divider;
echo '</pre>';