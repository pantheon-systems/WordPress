<?php
$sysinfo = OceanWP_Theme_Panel_System_Status::compile_system_status();
// $sysinfo_warnings = OceanWP_Theme_Panel_System_Status::compile_system_status_warnings();
?>
<a id="get-system-report-button" class="button blue oceanwp-button--get-system-report" href="#">
    <?php esc_html_e('Get System Report', 'ocean-extra'); ?>
</a>

<div id="oceanwp-textarea--get-system-report">
    <textarea readonly="readonly" onclick="this.focus();this.select()"></textarea>
</div>
<br>
<table class="table" cellspacing="0">
    <thead class="thead-light">
        <tr>
            <th colspan="3" data-export-label="WordPress Environment">
                <?php esc_html_e('WordPress Environment', 'ocean-extra'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td data-export-label="Home URL">
                <?php esc_html_e('Home URL', 'ocean-extra'); ?>:
            </td>


            <td><code><?php echo wp_kses_post($sysinfo['home_url']); ?></code></td>
        </tr>
        <tr>
            <td data-export-label="Site URL">
                <?php esc_html_e('Site URL', 'ocean-extra'); ?>:
            </td>

            <td>
                <code><?php echo esc_url($sysinfo['site_url']); ?></code>
            </td>
        </tr>

        <tr>
            <td data-export-label="WP Version">
                <?php esc_html_e('WP Version', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php bloginfo('version'); ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="WP Multisite">
                <?php esc_html_e('WP Multisite', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php if (false == $sysinfo['wp_multisite']) : ?>
                    <span class="status-invisible">False</span>
                    <span><?php echo esc_html_e('No', 'ocean-extra'); ?></span>
                <?php else : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                <?php endif; ?>
            </td>
        </tr>

        <?php $sof = $sysinfo['front_page_display']; ?>
        <tr>
            <td data-export-label="Front Page Display">
                <?php esc_html_e('Front Page Display', 'ocean-extra'); ?>:
            </td>

            <td><?php echo esc_html($sof); ?></td>
        </tr>

        <?php
        if ('page' == $sof) {
        ?>
            <tr>
                <td data-export-label="Front Page">
                    <?php esc_html_e('Front Page', 'ocean-extra'); ?>:
                </td>

                <td>
                    <?php echo esc_html($sysinfo['front_page']); ?>
                </td>
            </tr>
            <tr>
                <td data-export-label="Posts Page">
                    <?php esc_html_e('Posts Page', 'ocean-extra'); ?>:
                </td>

                <td>
                    <?php echo esc_html($sysinfo['posts_page']); ?>
                </td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td data-export-label="WP Memory Limit">
                <?php esc_html_e('WP Memory Limit', 'ocean-extra'); ?>:
            </td>

            <td>
                <span class="oceanwp-sysinfo-value">
                    <?php echo esc_html($sysinfo['wp_mem_limit']['size']); ?>
                </span>
            </td>
        </tr>
        <tr>
            <td data-export-label="WP Upload Limit">
                <?php esc_html_e('WP Upload Limit', 'ocean-extra'); ?>:
            </td>

            <td>
                <span class="oceanwp-sysinfo-value">
                    <?php echo esc_html($sysinfo['php_upload_max_filesize']); ?>
                </span>
            </td>
        </tr>
        <tr>
            <td data-export-label="WP Debug Mode">
                <?php esc_html_e('WP Debug Mode', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php if ('false' == $sysinfo['wp_debug']) : ?>
                    <span class="status-invisible">False</span>
                    <span><?php echo esc_html_e('Disabled', 'ocean-extra'); ?></span>
                <?php else : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="The Main WP Directory">
                <?php esc_html_e('The Main WP Directory', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php if (wp_is_writable($sysinfo['wp_writable'])) : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                    <span><?php esc_html_e('Writable', 'ocean-extra'); ?></span>
                <?php else : ?>
                    <span class="status-invisible">False</span><span class="status-state status-false"></span>
                    <span><?php printf(__('Make sure <code>%s</code> directory is writable.', 'ocean-extra'), $sysinfo['wp_writable']); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="The wp-content Directory">
                <?php esc_html_e('The wp-content Directory', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php if (wp_is_writable($sysinfo['wp_content_writable'])) : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                    <span><?php esc_html_e('Writable', 'ocean-extra'); ?></span>
                <?php else : ?>
                    <span class="status-invisible">False</span><span class="status-state status-false"></span>
                    <span><?php printf(__('Make sure <code>%s</code> directory is writable.', 'ocean-extra'), $sysinfo['wp_content_writable']); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="The uploads Directory">
                <?php esc_html_e('The uploads Directory', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php if (wp_is_writable($sysinfo['wp_uploads_writable'])) : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                    <span><?php esc_html_e('Writable', 'ocean-extra'); ?></span>
                <?php else : ?>
                    <span class="status-invisible">False</span><span class="status-state status-false"></span>
                    <span><?php printf(__('Make sure <code>%s</code> directory is writable.', 'ocean-extra'), $sysinfo['wp_uploads_writable']); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="The plugins Directory">
                <?php esc_html_e('The plugins Directory', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php if (wp_is_writable($sysinfo['wp_plugins_writable'])) : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                    <span><?php esc_html_e('Writable', 'ocean-extra'); ?></span>
                <?php else : ?>
                    <span class="status-invisible">False</span><span class="status-state status-false"></span>
                    <span><?php printf(__('Make sure <code>%s</code> directory is writable.', 'ocean-extra'), $sysinfo['wp_plugins_writable']); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="The themes Directory">
                <?php esc_html_e('The themes Directory', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php if (wp_is_writable($sysinfo['wp_themes_writable'])) : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                    <span><?php esc_html_e('Writable', 'ocean-extra'); ?></span>
                <?php else : ?>
                    <span class="status-invisible">False</span><span class="status-state status-false"></span>
                    <span><?php printf(__('Make sure <code>%s</code> directory is writable.', 'ocean-extra'), $sysinfo['wp_themes_writable']); ?></span>
                <?php endif; ?>
            </td>
        </tr>
    </tbody>
</table>
<br><br>
<table class="table" cellspacing="0">
    <thead class="thead-light">
        <tr>
            <th colspan="3" data-export-label="Theme"><?php esc_html_e('Theme', 'ocean-extra'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td data-export-label="Name"><?php esc_html_e('Name', 'ocean-extra'); ?>:</td>

            <td><?php echo esc_html($sysinfo['theme']['name']); ?></td>
        </tr>
        <tr>
            <td data-export-label="Version"><?php esc_html_e('Version', 'ocean-extra'); ?>:</td>

            <td>
                <?php echo esc_html($sysinfo['theme']['version']); ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="Author URL"><?php esc_html_e('Author URL', 'ocean-extra'); ?>:</td>

            <td><?php echo esc_url($sysinfo['theme']['author_uri']); ?></td>
        </tr>
        <tr>
            <td data-export-label="Child Theme"><?php esc_html_e('Child Theme', 'ocean-extra'); ?>:</td>

            <td>
                <?php if (is_child_theme()) : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                <?php else : ?>
                    <span class="status-invisible">False</span>
                    <span><?php echo esc_html_e('No', 'ocean-extra'); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <?php if (is_child_theme()) : ?>
            <tr>
                <td data-export-label="Parent Theme Name"><?php esc_html_e('Parent Theme Name', 'ocean-extra'); ?>:
                </td>

                <td><?php echo esc_html($sysinfo['theme']['parent_name']); ?></td>
            </tr>
            <tr>
                <td data-export-label="Parent Theme Version">
                    <?php esc_html_e('Parent Theme Version', 'ocean-extra'); ?>:
                </td>

                <td><?php echo esc_html($sysinfo['theme']['parent_version']); ?></td>
            </tr>
            <tr>
                <td data-export-label="Parent Theme Author URL">
                    <?php esc_html_e('Parent Theme Author URL', 'ocean-extra'); ?>:
                </td>

                <td><?php echo esc_url($sysinfo['theme']['parent_author_uri']); ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<br><br>

<table class="table" cellspacing="0">
    <thead class="thead-light">
        <tr>
            <th colspan="3" data-export-label="Server Environment">
                <?php esc_html_e('Server Environment', 'ocean-extra'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td data-export-label="Server Info">
                <?php esc_html_e('Server Info', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php echo esc_html($sysinfo['server_info']); ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="Localhost Environment">
                <?php esc_html_e('Localhost Environment', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php if ('true' == $sysinfo['localhost']) : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                <?php else : ?>
                    <span class="status-invisible">False</span>
                    <span><?php echo esc_html_e('No', 'ocean-extra'); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="PHP Version">
                <?php esc_html_e('PHP Version', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php echo esc_html($sysinfo['php_ver']); ?>
            </td>
        </tr>

        <?php
        if (function_exists('ini_get')) {
        ?>
            <tr class="<?php esc_attr_e(isset($sysinfo_warnings['php_mem_limit']) ? 'oceanwp-sysinfo-warning' : ''); ?>">
                <td data-export-label="PHP Memory Limit"><?php esc_html_e('PHP Memory Limit', 'ocean-extra'); ?>:</td>

                <td>
                    <span class="oceanwp-sysinfo-value">
                        <?php echo esc_html($sysinfo['php_mem_limit']['size']); ?>
                    </span>
                    <?php if (isset($sysinfo_warnings['php_mem_limit'])) : ?>
                        <span class="oceanwp-sysinfo-warning-msg">
                            <i class="oceanwp-icon-info-circle"></i>
                            <?php echo $sysinfo_warnings['php_mem_limit']['message']; ?>
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td data-export-label="PHP Post Max Size"><?php esc_html_e('PHP Post Max Size', 'ocean-extra'); ?>:</td>

                <td><?php echo esc_html($sysinfo['php_post_max_size']); ?></td>
            </tr>
            <tr>
                <td data-export-label="PHP Time Limit"><?php esc_html_e('PHP Time Limit', 'ocean-extra'); ?>:</td>
                </td>
                <td><?php echo esc_html($sysinfo['php_time_limit']); ?></td>
            </tr>

            <tr>
                <td data-export-label="PHP Max Input Vars"><?php esc_html_e('PHP Max Input Vars', 'ocean-extra'); ?>:</td>
                </a>
                </td>
                <td><?php echo esc_html($sysinfo['php_max_input_var']); ?></td>
            </tr>

        <?php
        }
        ?>
        <tr>
            <td data-export-label="PHP Display Errors"><?php esc_html_e('PHP Display Errors', 'ocean-extra'); ?>:</td>

            <td>
                <?php if ('false' == $sysinfo['php_display_errors']) : ?>
                    <span class="status-invisible">False</span>
                    <span><?php echo esc_html_e('Disabled', 'ocean-extra'); ?></span>
                <?php else : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <td data-export-label="MySQL Version"><?php esc_html_e('MySQL Version', 'ocean-extra'); ?>:</td>

            <td><?php echo esc_html($sysinfo['mysql_ver']); ?></td>
        </tr>
        <tr>
            <td data-export-label="Max Upload Size"><?php esc_html_e('Max Upload Size', 'ocean-extra'); ?>:</td>

            <td><?php echo esc_html($sysinfo['max_upload_size']); ?></td>
        </tr>
        <?php if (is_multisite()) : ?>
            <tr>
                <td data-export-label="Network Upload Limit"><?php esc_html_e('Network Upload Limit', 'ocean-extra'); ?>:</td>

                <td><?php echo esc_html($sysinfo['network_upload_limit']); ?></td>
            </tr>
        <?php endif; ?>

        <tr>

            <td data-export-label="PHP XML">
                <?php esc_html_e('PHP XML', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php if ('false' == $sysinfo['phpxml']) : ?>
                    <span class="status-invisible">False</span><span class="status-state status-false"></span>
                <?php else : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="MBString">
                <?php esc_html_e('MBString', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php if ('false' == $sysinfo['mbstring']) : ?>
                    <span class="status-invisible">False</span><span class="status-state status-false"></span>
                <?php else : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="SimpleXML">
                <?php esc_html_e('SimpleXML', 'ocean-extra'); ?>:
            </td>

            <td>
                <?php if ('false' == $sysinfo['simplexml']) : ?>
                    <span class="status-invisible">False</span><span class="status-state status-false"></span>
                <?php else : ?>
                    <span class="status-invisible">True</span><span class="status-state status-true"></span>
                <?php endif; ?>
            </td>
        </tr>
        <?php
        $posting = array();

        $posting['fsockopen_curl']['name'] = esc_html__('Fsockopen/cURL', 'ocean-extra');
        $posting['fsockopen_curl']['help'] = esc_attr__('Used when communicating with remote services with PHP.', 'ocean-extra');

        if ('true' == $sysinfo['fsockopen_curl']) {
            $posting['fsockopen_curl']['success'] = true;
        } else {
            $posting['fsockopen_curl']['success'] = false;
            $posting['fsockopen_curl']['note']    = esc_html__('Your server does not have fsockopen or cURL enabled - cURL is used to communicate with other servers. Please contact your hosting provider.', 'ocean-extra');
        }

        $posting['soap_client']['name'] = esc_html__('SoapClient', 'ocean-extra');
        $posting['soap_client']['help'] = esc_attr__('Some webservices like shipping use SOAP to get information from remote servers, for example, live shipping quotes from FedEx require SOAP to be installed.', 'ocean-extra');

        if (true == $sysinfo['soap_client']) {
            $posting['soap_client']['success'] = true;
        } else {
            $posting['soap_client']['success'] = false;
            $posting['soap_client']['note']    = sprintf(__('Your server does not have the <a href="%s">SOAP Client</a> class enabled - some gateway plugins which use SOAP may not work as expected.', 'ocean-extra'), 'http://php.net/manual/en/class.soapclient.php');
        }

        $posting['dom_document']['name'] = esc_html__('DOMDocument', 'ocean-extra');
        $posting['dom_document']['help'] = esc_attr__('HTML/Multipart emails use DOMDocument to generate inline CSS in templates.', 'ocean-extra');

        if (true == $sysinfo['dom_document']) {
            $posting['dom_document']['success'] = true;
        } else {
            $posting['dom_document']['success'] = false;
            $posting['dom_document']['note']    = sprintf(__('Your server does not have the <a href="%s">DOMDocument</a> class enabled - HTML/Multipart emails, and also some extensions, will not work without DOMDocument.', 'ocean-extra'), 'http://php.net/manual/en/class.domdocument.php');
        }


        $posting['gzip']['name'] = esc_html__('GZip', 'ocean-extra');
        $posting['gzip']['help'] = esc_attr__('GZip (gzopen) is used to open the GEOIP database from MaxMind.', 'ocean-extra');

        if (true == $sysinfo['gzip']) {
            $posting['gzip']['success'] = true;
        } else {
            $posting['gzip']['success'] = false;
            $posting['gzip']['note']    = sprintf(__('Your server does not support the <a href="%s">gzopen</a> function - this is required to use the GeoIP database from MaxMind. The API fallback will be used instead for geolocation.', 'ocean-extra'), 'http://php.net/manual/en/zlib.installation.php');
        }

        // Zip Archive.
        $posting['zip_archive']['name'] = esc_html__('Zip Archive', 'ocean-extra');
        $posting['zip_archive']['help'] = esc_attr__('Used to read or write ZIP compressed archives and the files inside them.', 'ocean-extra');

        if (class_exists('ZipArchive')) {
            $posting['zip_archive']['success'] = true;
        } else {
            $posting['zip_archive']['note']    = esc_html__('ZipArchive library is missing. Install the Zip extension. Contact your hosting provider.', 'ocean-extra');
            $posting['zip_archive']['success'] = false;
        }

        // Iconv.
        $posting['iconv']['name'] = esc_html__('Iconv', 'ocean-extra');
        $posting['iconv']['help'] = esc_attr__('Used in CSS parser to handle the character set conversion.', 'ocean-extra');

        if (extension_loaded('iconv')) {
            $posting['iconv']['success'] = true;
        } else {
            $posting['iconv']['note']    = esc_html__('Iconv library is missing. Install the iconv extension. Contact your hosting provider.', 'ocean-extra');
            $posting['iconv']['success'] = false;
        }

        // Echo the fields.
        foreach ($posting as $post) {
            $mark = !empty($post['success']) ? 'yes' : 'error';
        ?>
            <tr>
                <td data-export-label="<?php echo esc_html($post['name']); ?>">
                    <?php echo esc_html($post['name']); ?>:
                </td>

                <td>
                    <?php echo !empty($post['success']) ? '<span class="status-invisible">True</span><span class="status-state status-true"></span>' : '<span class="status-invisible">False</span><span class="status-state status-false"></span>'; ?>
                    <?php echo !empty($post['note']) ? wp_kses_data($post['note']) : ''; ?>
                </td>
            </tr>
        <?php
        }
        ?>
        <tr data-oceanwp-ajax="http_requests">
            <td data-export-label="HTTP Requests">
                <?php esc_html_e('HTTP Requests', 'ocean-extra'); ?>:
            </td>

            <td>
                <span class="status-state"><span class="spinner is-active"></span></span>
                <span class="status-text"></span>
            </td>
        </tr>
        <tr data-oceanwp-ajax="oceanwp_server">
            <td data-export-label="Communication with oceanwp.org">
                <?php esc_html_e('Communication with oceanwp.org', 'ocean-extra'); ?>:
            </td>

            <td>
                <span class="status-state"><span class="spinner is-active"></span></span>
                <span class="status-text"></span>
            </td>
        </tr>
    </tbody>
</table>
<br><br>

<table class="table" cellspacing="0">
    <thead class="thead-light">
        <tr>
            <th colspan="3" data-export-label="Active Plugins (<?php echo esc_html(count((array) get_option('active_plugins'))); ?>)">
                <?php esc_html_e('Active Plugins', 'ocean-extra'); ?> (<?php echo esc_html(count((array) get_option('active_plugins'))); ?>)
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($sysinfo['plugins'] as $name => $plugin_data) {

            if (!empty($plugin_data['Name'])) {
                $plugin_name = esc_html($plugin_data['Name']);

                if (!empty($plugin_data['PluginURI'])) {
                    $plugin_name = '<a href="' . esc_url($plugin_data['PluginURI']) . '" title="' . esc_attr__('Visit plugin homepage', 'ocean-extra') . '">' . esc_html($plugin_name) . '</a>';
                }
        ?>
                <tr>
                    <td><?php echo wp_kses_post($plugin_name); ?></td>

                    <td>
                        <?php echo sprintf(_x('by %s', 'by author', 'ocean-extra'), wp_kses_post($plugin_data['Author'])) . ' &ndash; ' . esc_html($plugin_data['Version']); ?>
                    </td>
                </tr>
        <?php
            }
        }
        ?>
    </tbody>
</table>