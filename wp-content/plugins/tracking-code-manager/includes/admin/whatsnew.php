<?php
define('TCMP_WHATSNEW_VERSION', 8);
function tcmp_ui_whats_new() {
    global $tcmp;
    $tcmp->Options->setShowWhatsNew(FALSE);
    $tcmp->Options->setShowWhatsNewSeenVersion(TCMP_WHATSNEW_VERSION);
    ?>
    <style>
        .tcmp-grid {
            margin-left: auto;
            margin-right: auto;
            border-spacing: 10px;
            max-width: 1120px;
        }
        .tcmp-grid td, .tcmp-grid td p {
            font-size:16px;
            vertical-align: top;
        }
        .tcmp-grid td ul {
            list-style-type: disc;
            margin-left: 30px!important;
        }
        .tcmp-grid td {
            padding: 20px!important;
        }
        .tcmp-headline {
            font-size:40px;
            font-weight:bold;
            text-align:center;
            margin: 10px!important;
        }
        .tcmp-subheadline {
            font-size:25px!important;
            font-weight:bold;
            text-align:left;
            margin: 0px!important;
        }
    </style>

    <p class="tcmp-headline">What's new in Tracking Code Manager?</p>
    <table border="0" class="tcmp-grid">
        <tr valign="top">
            <td valign="top" width="50%">
                Now the Tracking Code Manager let you:
                <ul>
                    <li>Use tracking codes by device types</li>
                    <li>Sort tracking codes using drag & drop</li>
                    <li>Shortcode support</li>
                    <li>Fixed 6 small issues</li>
                    <li>Quick support links added</li>
                </ul>
                <br>

                <p class="tcmp-subheadline">Dynamic Conversion Values</p>
                <p>Finally, Dynamic Conversion Values are now available for WooCommerce and Easy Digital Download. Now you can track the values of your conversions on <b>Google Adwords</b> and <b>Facebook Ads</b> (with the <b>New Pixel</b> and relative events like "Purchase" and others), and many other channels.</p>
                <img src="<?php echo TCMP_PLUGIN_ASSETS_URI ?>landing/tcmp-fb.png" />
                <br>
                <br>
                <div style="float: right;">
                    <a class="button button-secondary" href="<?php echo TCMP_TAB_MANAGER_URI?>&hwb=1">CONTINUE USING FREE VERSION</a>
                    <a class="button button-primary" href="<?php echo TCMP_TAB_DOCS_DCV_URI?>?utm_campaign=whatsnew" target="_blank">SEE MORE ››</a>
                </div>
            </td>
            <td valign="top" width="50%" style="border-left: 1px solid #44444E;">
                <p class="tcmp-subheadline" style="margin-top: 0px!important;">Introducing the Tracking Code Manager brother!</p>
                <p>We are proud to introduce Posts' Footer Manager, a free plugin that let you clean and organize the stuff you have in the footer of your blogpost.</p>
                <p>If you are tired of the MESSY stuff that appears after the content of your pages and articles, you should give it a go.</p>
                <div style="float: right;">
                    <a class="button button-secondary" href="http://wordpress.org/plugins/intelly-posts-footer-manager" target="_blank">
                        Download Posts' Footer Manager from Wordpress.org ››
                    </a>
                </div>
                <br>
                <br>
                <br>

                <p class="tcmp-subheadline">Our new awesome Plugins:</p>
                <p>Built by Marketers, for Marketers.</p>
                <ul>
                    <li><a href="https://intellywp.com/custom-audiences-enhancer/?utm_campaign=whatsnew" target="_blank">Custom Audiences Enhancer</a></li>
                    <li><a href="https://wordpress.org/plugins/intelly-welcome-bar/" target="_blank">Welcome Bar</a></li>
                    <li><a href="https://wordpress.org/plugins/intelly-related-posts/" target="_blank">Inline Related Posts</a></li>
                    <li><a href="https://wordpress.org/plugins/intelly-countdown/" target="_blank">Evergreen Countdown Timer</a></li>
                    <li><a href="https://wordpress.org/plugins/intelly-posts-footer-manager/" target="_blank">Posts' Footer Manager</a></li>
                </ul>
            </td>
        </tr>
    </table>
<?php }