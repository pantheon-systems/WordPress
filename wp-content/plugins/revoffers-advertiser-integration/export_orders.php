<?php
declare(ticks = 1);

// protect from web invocation
if (php_sapi_name() !== 'cli') die('Invalid invocation');

pcntl_signal(SIGINT,  "revoffers_sighandle");
pcntl_signal(SIGTERM, "revoffers_sighandle");
pcntl_signal(SIGHUP,  "revoffers_sighandle");

revoffers_export();

function revoffers_export() {
    // require WP bootstrap
    require_once dirname(__FILE__) . '/../../../wp-load.php';
    require_once dirname(__FILE__) . '/admin_func.php';

    ini_set('display_errors', 1);

    $tempFile = null;
    if (function_exists('posix_isatty') && posix_isatty(STDOUT)) {
        $tempFile = '/tmp/revoffers-orders-export.json.gz';
        fwrite(STDERR, "Printing results to $tempFile\n");
        $fp = fopen("compress.zlib://$tempFile", 'w');
    } else {
        fwrite(STDERR, "Printing all results to STDOUT\n");
        $fp = STDOUT;
    }
    if (!$fp) throw new RuntimeException("Failed to open file");

    $clearLine = `tput el`;
    $lines = 0;
    $tickFunc = function($row, $total) use (&$lines, $clearLine) {
        global $revoffers_break;
        if ($revoffers_break) return false;
        $lines++;
        if ($lines % 5 === 0) {
            wp_cache_flush(); // clear memory
            fprintf(STDERR, "\r$clearLine %02.2f%%  %d / %d  (%2.1fM)", $lines*100/$total, $lines, $total, memory_get_usage()/1024/1024);
        }
    };

    try {
        revoffers_print_orders($fp, null, $tickFunc);
    } catch (\Exception $e) {
        fwrite(STDERR, "EXCEPTION: $e\n");
    }

    fclose($fp);
    fwrite(STDERR, "\nWrote $lines lines to " . ($tempFile ?: 'STDOUT') . "\n");
}

function revoffers_sighandle() {
    global $revoffers_break;
    fwrite(STDERR, " [EXITING...] ");
    $revoffers_break = true;
}