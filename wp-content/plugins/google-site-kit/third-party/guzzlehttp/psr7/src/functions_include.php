<?php

namespace Google\Site_Kit_Dependencies;

// Don't redefine the functions if included multiple times.
if (!\function_exists('Google\\Site_Kit_Dependencies\\GuzzleHttp\\Psr7\\str')) {
    require __DIR__ . '/functions.php';
}
