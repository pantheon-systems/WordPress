<?php

use Symfony\Component\Yaml\Yaml;
class WPCFM_Readwrite
{

    public $folder;
    public $error;


    function __construct() {

        // Create the "config" folder
        $this->folder = WPCFM_CONFIG_DIR;

        if ( ! is_dir( $this->folder ) ) {
            if ( ! wp_mkdir_p( $this->folder ) ) {
                $this->error = __( 'Create ' . $this->folder .  ' and grant write access', 'wpcfm' );
            }
        }
        elseif ( ! is_writable( $this->folder ) ) {
            $this->error = __( 'The ' . $this->folder .  ' folder is not writable', 'wpcfm' );
        }
    }


    /**
     * Move the file bundle to DB
     * @param string $bundle_name The bundle name (or "all")
     */
    function pull_bundle( $bundle_name ) {
        $bundles = ( 'all' == $bundle_name ) ? WPCFM()->helper->get_bundle_names() : array( $bundle_name );

        // Retrieve the settings
        $settings = WPCFM()->options->get( 'wpcfm_settings' );
        $settings = json_decode( $settings, true );

        // Is this really needed (and is it a good place?)
        if ( ! is_array( $settings ) || ! isset( $settings['bundles'] ) ) {
            $settings = array( 'bundles' => array() );
        }

        $dontUpdateSettings = false;
        // Import each bundle into DB
        foreach ( $bundles as $bundle_name ) {
            $data = $this->read_file( $bundle_name );
            $bundle_label = $data['.label'];
            unset( $data['.label'] );

            $this->write_db( $bundle_name, $data );

            // If we import wpcfm settings don't update it
            if (!$dontUpdateSettings) {
              $dontUpdateSettings = $bundle_name == 'wpcfm';
          }
          if ($dontUpdateSettings) {
              continue;
          }

            // Update the bundle's config options (using the pull file)
          $exists = false;
          foreach ( $settings['bundles'] as $key => $bundle_settings ) {
            if ( $bundle_name == $bundle_settings['name'] ) {
                $settings['bundles'][ $key ]['label'] = $bundle_label;
                $settings['bundles'][ $key ]['config'] = array_keys( $data );
                $exists = true;
                break;
            }
        }

        if ( ! $exists ) {
            $settings['bundles'][] = array(
                'label'     => $bundle_label,
                'name'      => $bundle_name,
                'config'    => array_keys( $data ),
            );
        }
    }

        // Write the settings
    if (!$dontUpdateSettings) {
      WPCFM()->options->update( 'wpcfm_settings', json_encode( $settings ) );
  }
}


    /**
     * Move the DB bundle to file
     * @param string $bundle_name The bundle name (or "all")
     */
    function push_bundle( $bundle_name ) {
        $bundles = ( 'all' == $bundle_name ) ? WPCFM()->helper->get_bundle_names() : array( $bundle_name );

        foreach ( $bundles as $bundle_name ) {
            $data = $this->read_db( $bundle_name );

            // Append the bundle label
            $bundle_meta = WPCFM()->helper->get_bundle_by_name( $bundle_name );
            $data['.label'] = $bundle_meta['label'];

            if (WPCFM_CONFIG_FORMAT == 'json') {
            // JSON_PRETTY_PRINT for PHP 5.4+
                $data = version_compare( PHP_VERSION, '5.4.0', '>=' ) ?
                json_encode( $data, JSON_PRETTY_PRINT ) :
                json_encode( $data );
            }
            elseif (in_array(WPCFM_CONFIG_FORMAT, array('yaml', 'yml'))) {
                $data = WPCFM_Helper::convert_to_yaml($data);
            }
            $this->write_file( $bundle_name, $data );
        }
    }


    /**
     * Compare the DB vs file versions
     */
    function compare_bundle( $bundle_name ) {

        $return = array();
        $db_bundle = array();
        $file_bundle = array();

        // Diff all bundles
        if ( 'all' == $bundle_name ) {
            $bundle_names = WPCFM()->helper->get_bundle_names();
            foreach ( $bundle_names as $bundle_name ) {

                // Retrieve each bundle
                $temp_file = $this->read_file( $bundle_name );
                $temp_db = $this->read_db( $bundle_name );

                // Merge the bundle values
                $file_bundle = array_merge( $file_bundle, $temp_file );
                $db_bundle = array_merge( $db_bundle, $temp_db );
            }
        }
        // Diff a single bundle
        else {
            $file_bundle = $this->read_file( $bundle_name );
            $db_bundle = $this->read_db( $bundle_name );
        }

        // Remove the .label
        unset( $file_bundle['.label'] );

        // Convert to YAML for better readability if PHP version is compatible
        if (PHP_VERSION_ID >= 50604 && WPCFM_CONFIG_USE_YAML_DIFF) {
            $file_bundle = WPCFM_Helper::convert_to_yaml($file_bundle, false);
            $db_bundle   = WPCFM_Helper::convert_to_yaml($db_bundle, false);
        }

        if ( $file_bundle == $db_bundle ) {
            $return['error'] = __( 'Both versions are identical', 'wpcfm' );
        }
        else {
            $return['error'] = '';
            $return['file'] = $file_bundle;
            $return['db'] = $db_bundle;
        }

        return $return;
    }


    /**
     * Returns the bundle filename.
     * @return string
     */
    function bundle_filename( $bundle_name ) {
        $filename = "$this->folder/$bundle_name." . WPCFM_CONFIG_FORMAT;

        if ( is_multisite() ) {
            if ( WPCFM()->options->is_network ) {
                $filename = "$this->folder/network-$bundle_name." . WPCFM_CONFIG_FORMAT;
            }
            else {
                $filename = "$this->folder/blog" . get_current_blog_id() . "-$bundle_name." . WPCFM_CONFIG_FORMAT;
            }
        }

        return $filename;
    }


    /**
     * Load the file bundle
     * @return array
     */
    function read_file( $bundle_name ) {
        $filename = $this->bundle_filename( $bundle_name );
        if ( is_readable( $filename ) ) {
            $contents = file_get_contents( $filename );
            if (WPCFM_CONFIG_FORMAT == 'json') {
                return json_decode( $contents, true );
            }
            elseif (in_array(WPCFM_CONFIG_FORMAT, array('yaml', 'yml'))) {
              $array = Yaml::parse($contents);
              foreach ($array as $key => $value) {
                $format = array();
                if (preg_match('/\.(.*)_format/i', $key, $format)) {
                  switch ($array[$format[0]]) {
                    case 'serialized':
                    $array[$format[1]] = serialize($array[$format[1]]);
                    break;
                    case 'json':
                    $array[$format[1]] = json_encode($array[$format[1]]);
                    break;
                }
                unset($array[$format[0]]);
            }
        }
        return $array;
    }
}
return array();
}


    /**
     * Write the bundle to file
     */
    function write_file( $bundle_name, $data ) {
        $filename = $this->bundle_filename( $bundle_name );
        if ( file_exists( $filename ) ) {
            if ( is_writable( $filename ) ) {
                return file_put_contents( $filename, $data );
            }
        }
        elseif ( is_writable( $this->folder ) ) {
            return file_put_contents( $filename, $data );
        }
        return false;
    }


    /**
     * Delete a bundle file
     */
    function delete_file( $bundle_name ) {
        $filename = $this->bundle_filename( $bundle_name );
        if ( is_writable( $filename ) ) {
            return unlink( $filename );
        }
        return false;
    }


    /**
     * Load the bundle (from database)
     * @return array
     */
    function read_db( $bundle_name ) {

        $output = array();
        $all_config = WPCFM()->registry->get_configuration_items();

        $opts = WPCFM()->options->get( 'wpcfm_settings' );
        $opts = json_decode( $opts, true );
        foreach ( $opts['bundles'] as $bundle ) {
            if ( $bundle['name'] == $bundle_name ) {
                $bundle_config = $bundle['config'];
                break;
            }
        }

        if ( isset( $bundle_config ) ) {
            foreach ( $all_config as $key => $config ) {
                if ( in_array( $key, $bundle_config ) ) {
                    $output[ $key ] = $config['value'];
                }
            }
        }

        return $output;
    }


    /**
     * Save the bundle configuration data (to database)
     * @param string $bundle_name
     * @param array $file_data Array of configuration items
     */
    function write_db( $bundle_name, $file_data ) {

        $success = false;
        $db_data = WPCFM()->registry->get_configuration_items();

        foreach ( $file_data as $key => $val ) {

            // Set a default group if needed
            $group = isset( $db_data[ $key ]['group'] ) ? $db_data[ $key ]['group'] : __( 'WP Options', 'wpcfm' );

            // Make sure "old_value" exists
            if ( empty( $db_data[ $key ]['value'] ) ) {
                $db_data[ $key ]['value'] = '';
            }

            // Create the callback params
            $callback_params = array(
                'name'          => $key,
                'group'         => $group,
                'old_value'     => $db_data[ $key ]['value'],
                'new_value'     => $val,
            );

            // If no callback is defined, default to the "callback_wp_options" method
            $callback = array( $this, 'callback_wp_options' );
            if ( ! empty( $db_data[ $key ]['callback'] ) ) {
                $callback = $db_data[ $key ]['callback'];
            }

            // Allow for callback override
            $callback = apply_filters( 'wpcfm_pull_callback', $callback, $callback_params );

            if ( is_callable( $callback ) ) {
                if ( is_array( $callback ) ) {
                    $function = $callback[1];
                    $success = $callback[0]->$function( $callback_params );
                }
                else {
                    $success = $callback( $callback_params );
                }
            }
        }

        return $success;
    }


    /**
     * Default callback - write to wp_options table
     */
    function callback_wp_options( $params ) {
        $option_name = $params['name'];
        $option_value = maybe_unserialize( $params['new_value'] );
        WPCFM()->options->update( $option_name, $option_value );
    }
}
