<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Attachment_Permissions_Pro')) {

    /**
     * Attachment Permissions
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Attachment_Permissions_Pro extends WPFront_User_Role_Editor_Attachment_Permissions {

        public function user_has_cap($allcaps, $caps, $args) {
            if (isset($args[2])) {
                $post_id = $args[2];
                $post = get_post($post_id);

                if (!empty($post) && !empty($post->post_type) && $post->post_type === 'attachment') {
                    $deny_permission = FALSE;

                    $cap_attachments = NULL;
                    $cap_others_attachments = NULL;


                    if ($args[0] === 'edit_post') {
                        $cap_attachments = 'edit_attachments';
                        $cap_others_attachments = 'edit_others_attachments';
                    } elseif ($args[0] === 'delete_post') {
                        $cap_attachments = 'delete_attachments';
                        $cap_others_attachments = 'delete_others_attachments';
                    }

                    if (!empty($cap_attachments)) {
                        if (!current_user_can($cap_attachments)) {
                            $deny_permission = TRUE;
                        } elseif (!current_user_can($cap_others_attachments)) {
                            if (intval($args[1]) !== intval($post->post_author)) {
                                $deny_permission = TRUE;
                            }
                        }
                    }

                    if ($deny_permission) {
                        foreach ($caps as $cap) {
                            unset($allcaps[$cap]);
                        }
                    }
                }
            }

            return $allcaps;
        }

        public function posts_where($where, $query) {
            if (!is_user_logged_in())
                return $where;

            if (current_user_can('read_others_attachments'))
                return $where;

            $post_type = '';
            if (isset($query->query_vars['post_type']))
                $post_type = $query->query_vars['post_type'];

            if ($post_type !== 'attachment')
                return $where;

            $user = wp_get_current_user();

            global $wpdb;
            return $where . " AND $wpdb->posts.post_author = $user->ID ";
        }

    }

}

