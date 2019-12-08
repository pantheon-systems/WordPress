<?php

/**
 * Manage configuration options.
 */
class WPCFM_CLI_Command extends WP_CLI_Command
{

    /**
     * Push a bundle to the filesystem
     *
     * ## OPTIONS
     *
     * <bundle_name>
     * : The bundle name to export (or use "all")
     *
     * ## EXAMPLES
     *
     * wp config push bundle_name
     *
     * @synopsis <bundle_name> [--network]
     *
     */
    function push( $args, $assoc_args ) {
        if ( isset( $assoc_args['network'] ) ) {
            WPCFM()->options->is_network = true;
        }

        WPCFM()->readwrite->push_bundle( $args[0] );
        WP_CLI::success( 'The bundle has been written to file.' );
    }


    /**
     * Pull a bundle into the database
     *
     * ## OPTIONS
     *
     * <bundle_name>
     * : The bundle name to import (or use "all")
     *
     * ## EXAMPLES
     *
     * wp config pull bundle_name
     *
     * @synopsis <bundle_name> [--network]
     *
     */
    function pull( $args, $assoc_args ) {
        if ( isset( $assoc_args['network'] ) ) {
            WPCFM()->options->is_network = true;
        }

        $bundle_name = $args[0] ?: 'all';

        if ( 'all' != $bundle_name ) {
            if ( ! in_array( $bundle_name, WPCFM()->helper->get_bundle_names() ) ) {
                WP_CLI::error( "Bundle file for `$bundle_name` cannot be found." );
            }
        }

        WPCFM()->readwrite->pull_bundle( $bundle_name );
        WP_CLI::success( 'The bundle has been pulled into the database.' );
    }

    /**
     * Compare bundle differences
     *
     * ## OPTIONS
     *
     * <bundle_name>
     * : The bundle name to compare (or use "all")
     *
     * ## EXAMPLES
     *
     * wp config diff bundle_name
     *
     * @synopsis <bundle_name>
     *
     */
    function diff( $args, $assoc_args ) {
        $compare = WPCFM()->readwrite->compare_bundle( $args[0] );
        if ($compare['error'] !== '') {
            WP_CLI::warning( $compare['error'] );
        }
        else {
            # Sort these things into stuff that's only in one place,
            # or where there's actually a diff.
            $only_db_rows = array();
            $only_file_rows = array();
            $diff_rows = array();
            foreach( $compare['db'] as $key => $value ) {
                if ( !isset( $compare['file'][$key] ) ) {
                    $only_db_rows[] = array($key, $value);
                }
                elseif ( $value !== $compare['file'][$key] ) {
                    $diff_rows[$key] = array( $key, $compare['file'][$key], $value );
                }
            }
            foreach( $compare['file'] as $key => $value ) {
                if ( !isset( $compare['db'][$key] ) ) {
                    $only_file_rows[] = array( $key, $value );
                }
                elseif ( $value !== $compare['db'][$key] ) {
                    $diff_rows[$key] = array( $key, $compare['db'][$key], $value );
                }
            }
            if ( count( $only_file_rows) > 0 ) {
                $file = new \cli\Table( array( 'Option', 'Value' ), $only_file_rows);
                WP_CLI::line( 'Options that are only in files (pull to load)' );
                $file->display();
            }
            if ( count( $only_db_rows) > 0 ) {
                $db = new \cli\Table( array( 'Option', 'Value' ), $only_db_rows);
                WP_CLI::line( 'Options that are only in DB (push to write to file)' );
                $db->display();
            }
            if ( count( $diff_rows ) > 0 ) {
                $diff = new \cli\Table( array( 'Option', 'DB value', 'File value' ), $diff_rows);
                WP_CLI::line( 'Options in both the database and in files.' );
                $diff->display();
            }
        }
    }

    /**
     * Get bundle names
     *
     * ## OPTIONS
     *
     * ## Examples
     *
     * wp config bundles
     *
     */
    function bundles() {
        $bundles = WPCFM()->helper->get_bundles();
        $header = array( 'Bundle', 'Label', 'In File', 'In DB', 'Configs' );
        $table = new \cli\Table( $header, array() );
        foreach( $bundles as $bundle ) {
            $row = array( $bundle['name'], $bundle['label'], $bundle['is_file'], $bundle['is_db'] );
            $row[] = implode( ', ', $bundle['config'] );
            $table->addrow( $row );
        }
        $table->display();
    }

    /**
     * Get bundle details
     *
     * ## OPTIONS
     *
     * <bundle_name>
     * : The bundle name to inspect.
     *
     * ## EXAMPLES
     *
     * wp config show_bundle <bundle_name>
     *
     * @synopsis <bundle_name>
     *
     */
    function show_bundle( $args, $assoc_args ) {
        $file_bundle = WPCFM()->readwrite->read_file( $args[0] );
        $db_bundle = WPCFM()->readwrite->read_db( $args[0] );
        $header = array( 'Config', 'File value', 'DB value' );
        $rows = array();
        foreach( $file_bundle as $key => $value ) {
            $rows[$key] = array( $key, $value );
            if ( isset( $db_bundle[$key] ) ) {
                $rows[$key][] = $db_bundle[$key];
            }
            else {
                $rows[$key][] = 'n/a';
            }
        }
        foreach( $file_bundle as $key => $value ) {
            if ( !isset( $db_bundle[$key] ) ) {
                $rows[$key] = array( $key, 'n/a', $db_bundle[$key] );
            }
        }
        unset( $rows['.label'] );
        ksort($rows);
        $table = new \cli\Table( $header, $rows );
        $table->display();
    }
}

WP_CLI::add_command( 'config', 'WPCFM_CLI_Command' );
