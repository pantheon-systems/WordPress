<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-post-type-permissions.php");

if (!class_exists('WPFront_User_Role_Editor_Custom_Post_Permissions')) {

    /**
     * Custom Post Type Extended Permissions
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Custom_Post_Permissions extends WPFront_User_Role_Editor_Post_Type_Permissions {

        public function __construct($post_type, $main, $permission_cap, $caps_mapping) {
            parent::__construct($post_type, $main, $permission_cap, $caps_mapping);
        }

    }

}

