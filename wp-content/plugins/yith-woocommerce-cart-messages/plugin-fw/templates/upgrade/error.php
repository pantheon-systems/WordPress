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
    <title>WordPress › Error</title>
    <style type="text/css">
        html {
            background : #f1f1f1;
        }

        .container {
            background         : #fff;
            color              : #444;
            font-family        : -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            margin             : 2em auto;
            padding            : 1em 2em;
            max-width          : 700px;
            -webkit-box-shadow : 0 1px 3px rgba(0, 0, 0, 0.13);
            box-shadow         : 0 1px 3px rgba(0, 0, 0, 0.13);
        }

        h1 {
            border-bottom  : 1px solid #dadada;
            clear          : both;
            color          : #666;
            font-size      : 24px;
            margin         : 30px 0 0 0;
            padding        : 0;
            padding-bottom : 7px;
        }

        #error {
            margin-top : 50px;
        }

        #error p {
            font-size   : 14px;
            line-height : 1.5;
            margin      : 25px 0 20px;
        }

        #error code {
            font-family : Consolas, Monaco, monospace;
        }

        ul li {
            margin-bottom : 10px;
            font-size     : 14px;
        }

        a {
            color : #0073aa;
        }

        a:hover,
        a:active {
            color : #00a0d2;
        }

        a:focus {
            color              : #124964;
            -webkit-box-shadow : 0 0 0 1px #5b9dd9,
            0 0 2px 1px rgba(30, 140, 190, .8);
            box-shadow         : 0 0 0 1px #5b9dd9,
            0 0 2px 1px rgba(30, 140, 190, .8);
            outline            : none;
        }

        .button {
            background            : #f7f7f7;
            border                : 1px solid #ccc;
            color                 : #555;
            display               : inline-block;
            text-decoration       : none;
            font-size             : 13px;
            line-height           : 26px;
            height                : 28px;
            margin                : 0;
            padding               : 0 10px 1px;
            cursor                : pointer;
            -webkit-border-radius : 3px;
            -webkit-appearance    : none;
            border-radius         : 3px;
            white-space           : nowrap;
            -webkit-box-sizing    : border-box;
            -moz-box-sizing       : border-box;
            box-sizing            : border-box;

            -webkit-box-shadow    : 0 1px 0 #ccc;
            box-shadow            : 0 1px 0 #ccc;
            vertical-align        : top;
        }

        .button.button-large {
            height      : 30px;
            line-height : 28px;
            padding     : 0 12px 2px;
        }

        .button:hover,
        .button:focus {
            background   : #fafafa;
            border-color : #999;
            color        : #23282d;
        }

        .button:focus {
            border-color       : #5b9dd9;
            -webkit-box-shadow : 0 0 3px rgba(0, 115, 170, .8);
            box-shadow         : 0 0 3px rgba(0, 115, 170, .8);
            outline            : none;
        }

        .button:active {
            background         : #eee;
            border-color       : #999;
            -webkit-box-shadow : inset 0 2px 5px -3px rgba(0, 0, 0, 0.5);
            box-shadow         : inset 0 2px 5px -3px rgba(0, 0, 0, 0.5);
            -webkit-transform  : translateY(1px);
            -ms-transform      : translateY(1px);
            transform          : translateY(1px);
        }

    </style>
</head>
<body>
<div id="error" class="container">
    <p><?php echo $error ?></p>
</div>

</body>
</html>