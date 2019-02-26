<?php

add_action('admin_init', 'revoffers_admin_options_init');
add_action('admin_menu', 'revoffers_admin_menu');
add_action('wp_ajax_revoffers_orders_debug', 'revoffers_api_get_orders');

//
// Static symbols
//

function revoffers_admin_settings_link($links)
{
    // Add links to the plugin card on the Plugins List admin page
    $pluginName = explode(DIRECTORY_SEPARATOR, plugin_basename(__FILE__), 2)[0];
    $settingsPage = 'options-general.php?page=' . $pluginName;
    $settingsUrl = esc_url( get_admin_url(null, $settingsPage) );
    array_unshift($links, '<a href="' . $settingsUrl . '">Settings</a>');
    //TODO $links[] = '<a href="https://manage.revoffers.com/" target="_blank">My Account</a>';
    return $links;
}

function revoffers_admin_options_init()
{
    // registers the company key as an options setting if user is admin
    register_setting('revoffers', 'revoffers_site_id', [
        'type' => 'string',
        'description' => 'Site ID used internally to RevOffers for audience correlation',
        'sanitize_callback' => 'revoffers_sanitize_options',
    ]);
}

function revoffers_admin_menu()
{
    // will create the admin menu if user has correct access
    $pageTitle = 'RevOffers Network Settings';
    $menuTitle = 'RevOffers Network';
    $urlSlug = explode(DIRECTORY_SEPARATOR, plugin_basename(__FILE__), 2)[0];
    add_options_page($pageTitle, $menuTitle, 'manage_options', $urlSlug, 'revoffers_admin_options_page');
    // icon, if we need it later: plugins_url('/images/logo.png', __FILE__)
}

function revoffers_sanitize_options($input)
{
    foreach ($input as $k => $v) {
        if ($v !== null) {
            if (preg_match('#\s#', $v)) $v = null;
            else $v = trim( strtolower( strip_tags( stripslashes($v) ) ) );
            $input[$k] = $v;
        }
    }
    return $input;
}

function revoffers_admin_options_page()
{
    $defaultHost = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
    if (preg_match('#\s#', $defaultHost)) $defaultHost = null;
    else {
        $defaultHost = strtolower($defaultHost);
        if (strpos($defaultHost, 'www.') === 0) $defaultHost = substr($defaultHost, 4);
    }

    include dirname(__FILE__) . '/admin_page.php';
}

/**
 * Called with /wp-admin/admin-ajax.php?action=revoffers_orders_debug
 * Optional params:
 * - timeout = number of seconds to allow iteration to continue
 * - asText = whether to print directly to the screen to attempt partial results
 */
function revoffers_api_get_orders()
{
    $fp = $tempFile = null;
    try {
        $timeout = isset($_GET['timeout']) ? (int) $_GET['timeout']
            : ((int) ini_get('max_execution_time')) ?: 55;
        $timeout = microtime(true) + $timeout;
        $isGz = isset($_GET['asText']) ? !$_GET['asText'] : true;

        // Prep file handles
        if ($isGz) {
            $tempFile = tempnam('/tmp', 'revoffers-export-');
            if ($tempFile) $fp = fopen("compress.zlib://$tempFile", 'w');
            $isGz = !!$fp;
        }
//        THIS DOESN'T WORK!!!
//        if (!$isGz) {
//            // fallback to direct CSV output as text
//            $fp = fopen('php://stdout', 'w');
//        }

        header('Content-Type: application/' . ($isGz ? 'gzip' : 'json') . '; charset=utf-8', true);
        header('Content-Disposition: attachment; filename=revoffers_orders_export.json'
            . ($isGz ? '.gz' : null), true);

        revoffers_print_orders($fp, $timeout);

        if ($isGz) {
            fclose($fp);// write GZ header info
            $fp = null;
            // If you want Chrome to auto de-compress:
            //header('Content-Encoding: gzip', true);
            readfile($tempFile);// send to STDOUT
        }

    } catch (\Exception $e) {
        @header('Content-Type: text/plain; charset=utf-8', true);
        echo "Failed: $e";
    } finally {
        if ($fp) @fclose($fp);
        if ($tempFile && file_exists($tempFile)) unlink($tempFile);
    }

    wp_die();
}

/**
 * @param resource $fp
 * @param int $timeout
 * @param callable $tick
 */
function revoffers_print_orders($fp, $timeout = null, $tick = null)
{
    $limit = 200;
    $maxOffset = 20000;
    $offset = $jsonOpt = $lines = $numTotal = 0;
    if (defined('JSON_UNESCAPED_SLASHES')) $jsonOpt |= JSON_UNESCAPED_SLASHES;
    if (defined('JSON_UNESCAPED_UNICODE')) $jsonOpt |= JSON_UNESCAPED_UNICODE;
    if (defined('JSON_PARTIAL_OUTPUT_ON_FAILURE')) $jsonOpt |= JSON_PARTIAL_OUTPUT_ON_FAILURE;

    print_more:
    $orderIds = wc_get_orders([
        'type' => 'shop_order',
        'date_created' => '>' . date('Y-m-d', time() - 60*60*24*365),// only 1 year
        'status' => 'completed',// only show successes
        'limit' => $limit,
        'offset' => $offset,
        'orderby' => 'date',
        'order' => 'DESC',
        'paginate' => $offset === 0,
        'return' => 'ids',
    ]);
    if ($offset === 0) {
        $numTotal = $orderIds->total;
        $orderIds = $orderIds->orders;
    }
    $offset += $limit;

    foreach ($orderIds as $orderId) {
        $lines++;
        $params = revoffers_get_order_safe($orderId, true);
        if (!$params) continue;
        $str = json_encode($params, $jsonOpt);

        if ($fp) {
            fwrite($fp, $str);
            fwrite($fp, "\n");
        } else {
            echo $str, "\n";
        }

        if ($timeout && microtime(true) >= $timeout) {
            $str = "TIMED OUT, lines=$lines total=$numTotal offset=$offset limit=$limit\n";
            if ($fp) fwrite($fp, $str);
            else echo $str;
            $offset = $maxOffset;
            break;
        }

        if ($tick && false === $tick($params, $numTotal)) {
            return;
        }
    }
    if ($offset < $maxOffset && count($orderIds) >= $limit) {
        goto print_more;
    }
}