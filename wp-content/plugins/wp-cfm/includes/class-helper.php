<?php

use Symfony\Component\Yaml\Yaml;
class WPCFM_Helper
{

    /**
     * Load all bundles (DB + file)
     */
    function get_bundles() {
        $output = array();

        // Get DB bundles first
        $opts = WPCFM()->options->get( 'wpcfm_settings' );
        $opts = json_decode( $opts, true );

        if ( isset( $opts['bundles'] ) ) {
            foreach ( $opts['bundles'] as $bundle ) {
                $bundle['is_db'] = true;
                $bundle['is_file'] = false;
                $output[ $bundle['name'] ] = $bundle;
            }
        }

        // Then merge file bundles
        $file_bundles = $this->get_file_bundles();
        foreach ( $file_bundles as $bundle_name => $bundle ) {
            if ( isset( $output[ $bundle_name ] ) ) {
                $output[ $bundle_name ]['is_file'] = true;
                $output[ $bundle_name ]['url'] = $this->get_bundle_url( $bundle_name );
            }
            else {
                $bundle['is_db'] = false;
                $bundle['is_file'] = true;
                $bundle['url'] = $this->get_bundle_url( $bundle_name );
                $bundle['config'] = array_keys($bundle['config']);
                $output[ $bundle_name ] = $bundle;
            }
        }

        return $output;
    }


    /**
     * Get bundle URL
     */

    function get_bundle_url( $bundle_name ) {
        return WPCFM_CONFIG_URL . '/' . basename( WPCFM()->readwrite->bundle_filename( $bundle_name ) );
    }


    /**
     * Get file bundles
     */
    function get_file_bundles() {

        $output = array();
        $filenames = array_filter( scandir( WPCFM_CONFIG_DIR ), function( $item ) {
            return !is_dir( WPCFM_CONFIG_DIR . '/' . $item );
        });

        foreach ( $filenames as $filename ) {

            // Ignore dot files
            if ( '.' == substr( $filename, 0, 1 ) ) {
                continue;
            }

            // Default to single site bundle
            $bundle_name = str_replace( '.' . WPCFM_CONFIG_FORMAT, '', $filename );

            if ( is_multisite() ) {
                $filename_parts = explode( '-', $filename, 2 );

                // Only accept multi-site bundles
                if ( 2 > count( $filename_parts ) ) {
                    continue;
                }

                $bundle_name = str_replace( '.json', '', $filename_parts[1] );

                if ( WPCFM()->options->is_network ) {
                    if ( 'network' != $filename_parts[0] ) {
                        continue;
                    }
                }
                elseif ( $filename_parts[0] != 'blog' . get_current_blog_id() ) {
                    continue;
                }

            }

            $bundle_data = WPCFM()->readwrite->read_file( $bundle_name );
            $bundle_label = $bundle_data['.label'];
            unset( $bundle_data['.label'] );

            $output[ $bundle_name ] = array(
                'label'     => $bundle_label,
                'name'      => $bundle_name,
                'config'    => $bundle_data,
            );
        }
        return $output;
    }


    /**
     * Load all bundle names
     */
    function get_bundle_names() {
        return array_keys( $this->get_bundles() );
    }


    /**
     * Get bundle by name
     */
    function get_bundle_by_name( $bundle_name ) {
        $bundles = $this->get_bundles();
        return isset( $bundles[ $bundle_name ] ) ?
        $bundles[ $bundle_name ] :
        array();
    }


    /**
     * Put configuration items into groups
     */
    function group_items( $items ) {
        $output = array();

        // Sort by array key
        ksort( $items );

        foreach ( $items as $key => $item ) {
            $group = isset( $item['group'] ) ? $item['group'] : __( 'WP Options', 'wpcfm' );
            $output[ $group ][ $key ] = $item;
        }

        return $output;
    }

    /**
     * Convert array to yaml
     *
     * @param $data array
     * @param $saveFormat boolean
     *
     * @return mixed
     */
    static function convert_to_yaml($data, $saveFormat = true) {
        foreach ($data as $key => &$value) {
            $jsonDecoded = json_decode($value, true);
            if (is_array($jsonDecoded)) {
                $value = $jsonDecoded;
                if ($saveFormat) {
                    $data['.' . $key . '_format'] = 'json';
                }
            }
            elseif (is_serialized($value)) {
                $value = unserialize($value);
                if ($saveFormat) {
                    $data['.' . $key . '_format'] = 'serialized';
                }
            }
        }
        return Yaml::dump($data, 10);
    }

}
