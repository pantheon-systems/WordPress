<?php

class WPCFM_Custom_Field_Suite
{

    function __construct() {
        add_filter( 'wpcfm_configuration_items', array( $this, 'configuration_items' ) );
        add_filter( 'wpcfm_pull_callback', array( $this, 'pull_callback' ), 10, 2 );
    }


    /**
     * Register the field groups in WP-CFM
     */
    function configuration_items( $items ) {
        $field_groups = $this->get_field_groups();

        foreach ( $field_groups as $name => $group ) {
            $items[ "cfs_field_group_$name" ] = array(
                'value' => json_encode( $group ),
                'label' => $group['post_title'],
                'group' => 'Custom Field Suite',
            );
        }

        // Exclude some CFS options
        unset( $items['cfs_next_field_id'] );
        unset( $items['cfs_version'] );
        return $items;
    }


    /**
     * Tell WP-CFM to use cfs_pull() for field groups
     */
    function pull_callback( $callback, $callback_params ) {
        if ( 0 === strpos( $callback_params['name'], 'cfs_field_group_' ) ) {
            return array( $this, 'cfs_pull' );
        }
        return $callback;
    }


    /**
     * Import (overwrite) field groups into DB
     * @param string $params['name']
     * @param string $params['group']
     * @param string $params['old_value'] The previous settings data
     * @param string $params['new_value'] The new settings data
     */
    function cfs_pull( $params ) {

        $import_code = array();
        $old_value = json_decode( $params['old_value'], true );
        $new_value = json_decode( $params['new_value'], true );
        $next_field_id = (int) get_option( 'cfs_next_field_id' );

        // Store old field names & IDs
        $old_field_ids = array();
        $old_parent_ids = array();

        // key = old ID, value = new ID
        $new_field_lookup = array();

        if ( ! empty( $old_value ) ) {
            foreach ( $old_value['cfs_fields'] as $field ) {
                $old_field_ids[ $field['name'] ] = $field['id'];

                // Track parent IDs for loop sub-fields
                if ( 0 < (int) $field['parent_id'] ) {
                    $old_parent_ids[ $field['id'] ] = $field['parent_id'];
                }
            }
        }

        // Change the field IDs (if needed), then save
        $field_group = $new_value;

        foreach ( $field_group['cfs_fields'] as $key => $field ) {
            $field_name = $field['name'];

            // Preserve the old field ID
            if ( isset( $old_field_ids[ $field_name ] ) ) {
                $field_id = $old_field_ids[ $field_name ];
                $field_group['cfs_fields'][ $key ]['id'] = $field_id;

                // Use the existing parent_id
                if ( isset( $old_parent_ids[ $field_id ] ) ) {
                    $field_group['cfs_fields'][ $key ]['parent_id'] = $old_parent_ids[ $field_id ];
                }
            }
            // Otherwise, increment the field ID counter
            else {
                $prev_id = $field_group['cfs_fields'][ $key ]['id'];
                $new_field_lookup[ $prev_id ] = $next_field_id;
                $field_group['cfs_fields'][ $key ]['id'] = $next_field_id;
                $next_field_id++;

                // Use the new parent_id
                $parent_id = (int) $field_group['cfs_fields'][ $key ]['parent_id'];
                if ( 0 < $parent_id ) {
                    $field_group['cfs_fields'][ $key ]['parent_id'] = $new_field_lookup[ $parent_id ];
                }
            }
        }

        $this->save_field_group( $field_group );

        update_option( 'cfs_next_field_id', $next_field_id );
    }


    /**
     * Return an array of CFS field groups
     */
    function get_field_groups() {
        global $wpdb;

        $field_groups = array();
        $field_group_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'cfs' AND post_status = 'publish'" );
        $export = CFS()->field_group->export( array( 'field_groups' => $field_group_ids ) );

        foreach ( $export as $field_group ) {
            $field_groups[ $field_group['post_name'] ] = $field_group;
        }

        return $field_groups;
    }


    /**
     * Save a CFS field group
     */
    function save_field_group( $field_group ) {
        $args = array(
            'name'              => $field_group['post_name'],
            'post_type'         => 'cfs',
            'posts_per_page'    => 1,
            'fields'            => 'ids',
        );

        $posts = get_posts( $args );

        if ( ! empty( $posts ) ) {
            $post_id = $posts[0];
        }
        else {
            $post_id = wp_insert_post( array(
                'post_title'    => $field_group['post_title'],
                'post_name'     => $field_group['post_name'],
                'post_type'     => 'cfs',
                'post_status'   => 'publish',
                'post_content'  => '',
            ) );
        }

        // Insert the postmeta settings
        update_post_meta( $post_id, 'cfs_fields', $field_group['cfs_fields'] );
        update_post_meta( $post_id, 'cfs_rules', $field_group['cfs_rules'] );
        update_post_meta( $post_id, 'cfs_extras', $field_group['cfs_extras'] );
    }
}

// Requires CFS
if ( class_exists( 'Custom_Field_Suite' ) ) {
    new WPCFM_Custom_Field_Suite();
}
