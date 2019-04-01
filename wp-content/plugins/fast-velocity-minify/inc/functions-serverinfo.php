<?php

### Get General Information
function fvm_get_generalinfo() {
    global $is_IIS;
    if( is_rtl() ) : ?>
        <style type="text/css">
            #GeneralOverview table,
            #GeneralOverview th,
            #GeneralOverview td {
                direction: ltr;
                text-align: left;
            }
            #GeneralOverview h2 {
                padding: 0.5em 0 0;
            }
        </style>
    <?php endif;
?>
    <div class="wrap" id="GeneralOverview">
        <h2><?php _e('Server Info','fvm-serverinfo'); ?></h2>
        <br />
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e('Variable Name', 'fvm-serverinfo'); ?></th>
                    <th><?php _e('Value', 'fvm-serverinfo'); ?></th>
                    <th><?php _e('Variable Name', 'fvm-serverinfo'); ?></th>
                    <th><?php _e('Value', 'fvm-serverinfo'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php _e('OS', 'fvm-serverinfo'); ?></td>
                    <td><?php echo PHP_OS; ?></td>
                    <td><?php _e('Database Data Disk Usage', 'fvm-serverinfo'); ?></td>
                    <td><?php echo fvm_format_filesize(fvm_get_mysql_data_usage()); ?></td>
                </tr>
                <tr class="alternate">
                    <td><?php _e('Server', 'fvm-serverinfo'); ?></td>
                    <td><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></td>
                    <td><?php _e('Database Index Disk Usage', 'fvm-serverinfo'); ?></td>
                    <td><?php echo fvm_format_filesize(fvm_get_mysql_index_usage()); ?></td>
                </tr>
                <tr>
                    <td>PHP</td>
                    <td>v<?php echo PHP_VERSION; ?></td>
                    <td><?php _e('MYSQL Maximum Packet Size', 'fvm-serverinfo'); ?></td>
                    <td><?php echo fvm_format_filesize(fvm_get_mysql_max_allowed_packet()); ?></td>
                </tr>
                <tr class="alternate">
                    <td>MYSQL</td>
                    <td>v<?php echo fvm_get_mysql_version(); ?></td>
                    <td><?php _e('MYSQL Maximum No. Connection', 'fvm-serverinfo'); ?></td>
                    <td><?php echo number_format_i18n(fvm_get_mysql_max_allowed_connections()); ?></td>
                </tr>
                <tr>
                    <td><?php _e('Server Load', 'fvm-serverinfo'); ?></td>
                    <td><?php echo fvm_get_serverload(); ?></td>
                    <td><?php _e( 'MYSQL Query Cache Size', 'fvm-serverinfo' ); ?></td>
                    <td><?php echo fvm_format_filesize( fvm_get_mysql_query_cache_size() ); ?></td>
                </tr>
                <tr class="alternate">
                    <td><?php _e("Server CPU's", 'fvm-serverinfo'); ?></td>
                    <td><?php echo fvm_get_servercpu(); ?></td>
                    <td><?php _e('PHP Short Tag', 'fvm-serverinfo'); ?></td>
                    <td><?php echo fvm_get_php_short_tag(); ?></td>
                </tr>
                <tr>
                    <td><?php _e('Server Hostname', 'fvm-serverinfo'); ?></td>
                    <td><?php echo $_SERVER['SERVER_NAME']; ?></td>
                    <td><?php _e('PHP Magic Quotes GPC', 'fvm-serverinfo'); ?></td>
                    <td><?php echo fvm_get_php_magic_quotes_gpc(); ?></td>
                </tr>
                <tr class="alternate">
                    <td><?php _e('Server Document Root','fvm-serverinfo'); ?></td>
                    <td><?php echo $_SERVER['DOCUMENT_ROOT']; ?></td>
                    <td><?php _e('PHP Memory Limit', 'fvm-serverinfo'); ?></td>
                    <td><?php echo fvm_format_php_size(fvm_get_php_memory_limit()); ?></td>
                </tr>
                <tr>
                    <td><?php _e('Site Url', 'fvm-serverinfo'); ?></td>
                    <td><?php echo site_url(); ?></td>
                    <td><?php _e('PHP Max Upload Size', 'fvm-serverinfo'); ?></td>
                    <td><?php echo fvm_format_php_size(fvm_get_php_upload_max()); ?></td>
                </tr>
                <tr class="alternate">
                    <td><?php _e('Home Url', 'fvm-serverinfo'); ?></td>
                    <td><?php echo home_url(); ?></td>
                    <td><?php _e('PHP Max Post Size', 'fvm-serverinfo'); ?></td>
                    <td><?php echo fvm_format_php_size(fvm_get_php_post_max()); ?></td>
                </tr>
                <tr>
                    <td><?php _e('MySQL Date/Time', 'fvm-serverinfo'); ?></td>
                    <td><?php echo mysql2date(sprintf(__('%s @ %s', 'wp-postratings'), get_option('date_format'), get_option('time_format')), current_time('mysql', 1)). ' GMT'; ?></td>
                    <td><?php _e('PHP Max Script Execute Time', 'fvm-serverinfo'); ?></td>
                    <td><?php echo fvm_get_php_max_execution(); ?>s</td>
                </tr>
            </tbody>
        </table>
    </div>
<?php
}


### Function: Format Bytes Into TiB/GiB/MiB/KiB/Bytes
if(!function_exists('fvm_format_filesize')) {
    function fvm_format_filesize($rawSize) {
		if(is_numeric($rawSize)) {
			if($rawSize / 1099511627776 > 1) {
				return number_format_i18n($rawSize/1099511627776, 1).' '.__('TiB', 'fvm-serverinfo');
			} elseif($rawSize / 1073741824 > 1) {
				return number_format_i18n($rawSize/1073741824, 1).' '.__('GiB', 'fvm-serverinfo');
			} elseif($rawSize / 1048576 > 1) {
				return number_format_i18n($rawSize/1048576, 1).' '.__('MiB', 'fvm-serverinfo');
			} elseif($rawSize / 1024 > 1) {
				return number_format_i18n($rawSize/1024, 1).' '.__('KiB', 'fvm-serverinfo');
			} elseif($rawSize > 1) {
				return number_format_i18n($rawSize, 0).' '.__('bytes', 'fvm-serverinfo');
			} else {
				return __('N/A', 'fvm-serverinfo');
			}
		} else {
            return __('N/A', 'fvm-serverinfo');
        }
    }
}

### Function: Convert PHP Size Format to Localized
function fvm_format_php_size($size) {
    if (!is_numeric($size)) {
        if (strpos($size, 'M') !== false) {
            $size = intval($size)*1024*1024;
        } elseif (strpos($size, 'K') !== false) {
            $size = intval($size)*1024;
        } elseif (strpos($size, 'G') !== false) {
            $size = intval($size)*1024*1024*1024;
        }
    }
    return is_numeric($size) ? fvm_format_filesize($size) : $size;
}

### Function: Get PHP Short Tag
if(!function_exists('fvm_get_php_short_tag')) {
    function fvm_get_php_short_tag() {
        if(ini_get('short_open_tag')) {
            $short_tag = __('On', 'fvm-serverinfo');
        } else {
            $short_tag = __('Off', 'fvm-serverinfo');
        }
        return $short_tag;
    }
}


### Function: Get PHP Magic Quotes GPC
if(!function_exists('fvm_get_php_magic_quotes_gpc')) {
    function fvm_get_php_magic_quotes_gpc() {
        if(get_magic_quotes_gpc()) {
            $magic_quotes_gpc = __('On', 'fvm-serverinfo');
        } else {
            $magic_quotes_gpc = __('Off', 'fvm-serverinfo');
        }
        return $magic_quotes_gpc;
    }
}


### Function: Get PHP Max Upload Size
if(!function_exists('fvm_get_php_upload_max')) {
    function fvm_get_php_upload_max() {
        if(ini_get('upload_max_filesize')) {
            $upload_max = ini_get('upload_max_filesize');
        } else {
            $upload_max = __('N/A', 'fvm-serverinfo');
        }
        return $upload_max;
    }
}


### Function: Get PHP Max Post Size
if(!function_exists('fvm_get_php_post_max')) {
    function fvm_get_php_post_max() {
        if(ini_get('post_max_size')) {
            $post_max = ini_get('post_max_size');
        } else {
            $post_max = __('N/A', 'fvm-serverinfo');
        }
        return $post_max;
    }
}


### Function: PHP Maximum Execution Time
if(!function_exists('fvm_get_php_max_execution')) {
    function fvm_get_php_max_execution() {
        if(ini_get('max_execution_time')) {
            $max_execute = ini_get('max_execution_time');
        } else {
            $max_execute = __('N/A', 'fvm-serverinfo');
        }
        return $max_execute;
    }
}


### Function: PHP Memory Limit
if(!function_exists('fvm_get_php_memory_limit')) {
    function fvm_get_php_memory_limit() {
        if(ini_get('memory_limit')) {
            $memory_limit = ini_get('memory_limit');
        } else {
            $memory_limit = __('N/A', 'fvm-serverinfo');
        }
        return $memory_limit;
    }
}


### Function: Get MYSQL Version
if(!function_exists('fvm_get_mysql_version')) {
    function fvm_get_mysql_version() {
        global $wpdb;
        return $wpdb->get_var("SELECT VERSION() AS version");
    }
}


### Function: Get MYSQL Data Usage
if(!function_exists('fvm_get_mysql_data_usage')) {
    function fvm_get_mysql_data_usage() {
        global $wpdb;
        $data_usage = 0;
        $tablesstatus = $wpdb->get_results("SHOW TABLE STATUS");
        foreach($tablesstatus as  $tablestatus) {
			if(is_numeric($tablestatus->Data_length)) { $data_usage += $tablestatus->Data_length; } else { $data_usage += 0; }
        }
        if (!$data_usage) {
            $data_usage = __('N/A', 'fvm-serverinfo');
        }
        return $data_usage;
    }
}


### Function: Get MYSQL Index Usage
if(!function_exists('fvm_get_mysql_index_usage')) {
    function fvm_get_mysql_index_usage() {
        global $wpdb;
        $index_usage = 0;
        $tablesstatus = $wpdb->get_results("SHOW TABLE STATUS");
        foreach($tablesstatus as  $tablestatus) {
            if(is_numeric($tablestatus->Index_length)) { $index_usage +=  $tablestatus->Index_length; } else { $index_usage += 0; }
        }
        if (!$index_usage){
            $index_usage = __('N/A', 'fvm-serverinfo');
        }
        return $index_usage;
    }
}


### Function: Get MYSQL Max Allowed Packet
if(!function_exists('fvm_get_mysql_max_allowed_packet')) {
    function fvm_get_mysql_max_allowed_packet() {
        global $wpdb;
        $packet_max_query = $wpdb->get_row("SHOW VARIABLES LIKE 'max_allowed_packet'");
        $packet_max = $packet_max_query->Value;
        if(!$packet_max) {
            $packet_max = __('N/A', 'fvm-serverinfo');
        }
        return $packet_max;
    }
}


### Function:Get MYSQL Max Allowed Connections
if(!function_exists('fvm_get_mysql_max_allowed_connections')) {
    function fvm_get_mysql_max_allowed_connections() {
        global $wpdb;
        $connection_max_query = $wpdb->get_row("SHOW VARIABLES LIKE 'max_connections'");
        $connection_max = $connection_max_query->Value;
        if(!$connection_max) {
            $connection_max = __('N/A', 'fvm-serverinfo');
        }
        return $connection_max;
    }
}

### Function:Get MYSQL Query Cache Size
if(!function_exists('fvm_get_mysql_query_cache_size')) {
    function fvm_get_mysql_query_cache_size() {
        global $wpdb;
        $query_cache_size_query = $wpdb->get_row( "SHOW VARIABLES LIKE 'query_cache_size'" );
        $query_cache_size = $query_cache_size_query->Value;
        if ( empty( $query_cache_size ) ) {
            $query_cache_size = __( 'N/A', 'fvm-serverinfo' );
        }
        return $query_cache_size;
    }
}


### Function: Get The Server Load
if(!function_exists('fvm_get_serverload')) {
    function fvm_get_serverload() {
        $server_load = 0;
		$numCpus = 'N/A';
        if(PHP_OS != 'WINNT' && PHP_OS != 'WIN32') {
			clearstatcache();
            if(@file_exists('/proc/loadavg') ) {
                if ($fh = @fopen( '/proc/loadavg', 'r' )) {
                    $data = @fread( $fh, 6 );
                    @fclose( $fh );
                    $load_avg = explode( " ", $data );
                    $server_load = trim($load_avg[0]);
                }
			} else if ('WIN' == strtoupper(substr(PHP_OS, 0, 3)) && function_exists('popen') && fvm_function_available('popen')) {
				$process = @popen('wmic cpu get NumberOfCores', 'rb');
				if (false !== $process && null !== $process) {
					fgets($process);
					$numCpus = intval(fgets($process));
					pclose($process);
				}
			} else if (function_exists('system') && fvm_function_available('system')){
                $data = @system('uptime');
                preg_match('/(.*):{1}(.*)/', $data, $matches);
				if(isset($matches[2])) {
					$load_arr = explode(',', $matches[2]);
					$server_load = trim($load_arr[0]);
				} else {
					$server_load = __('N/A', 'fvm-serverinfo');
				}
            } else {
				$server_load = __('N/A', 'fvm-serverinfo');
			}
        }
        if(empty($server_load)) {
            $server_load = __('N/A', 'fvm-serverinfo');
        }
        return $server_load;
    }
}


### Function: Get The Server CPU's
if(!function_exists('fvm_get_servercpu')) {
    function fvm_get_servercpu() {
		$numCpus = 0;
        if(PHP_OS != 'WINNT' && PHP_OS != 'WIN32') {
			clearstatcache();
			if (is_file('/proc/cpuinfo')) {
				$cpuinfo = file_get_contents('/proc/cpuinfo');
				preg_match_all('/^processor/m', $cpuinfo, $matches);
				$numCpus = count($matches[0]);
			} else if (function_exists('popen') && fvm_function_available('popen')) {
				$process = @popen('sysctl -a', 'rb');
				if (false !== $process && null !== $process) {
					$output = stream_get_contents($process);
					preg_match('/hw.ncpu: (\d+)/', $output, $matches);
					if ($matches) { $numCpus = intval($matches[1][0]); }
					pclose($process);
				}
			} else {
					$numCpus = __('N/A', 'fvm-serverinfo');
			}
		}
        if(empty($numCpus)) {
            $numCpus = __('N/A', 'fvm-serverinfo');
        }
        return $numCpus;
    }
}