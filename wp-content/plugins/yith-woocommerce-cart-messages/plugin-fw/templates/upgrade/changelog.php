<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @var string $plugin_name
 * @var string $changelog
 *
 */
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="robots" content="noindex,follow">
    <title><?php echo $plugin_name ?> - Changelog</title>
    <style type="text/css">
        body {
            background  : #ffffff;
            color       : #444;
            font-family : -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            font-size   : 13px;
            line-height : 1.4em;
            padding     : 10px;
        }

        h2.yith-plugin-changelog-title {
            text-transform : uppercase;
            font-size      : 17px;
        }

        ul {
            list-style : none;
            padding    : 0;
        }

        li {
            display       : list-item;
            margin-bottom : 6px;
        }
    </style>
</head>
<body>
<h2 class='yith-plugin-changelog-title'><?php echo $plugin_name ?> - Changelog</h2>
<div class='yith-plugin-changelog'><?php echo $changelog ?></div>
</body>
</html>