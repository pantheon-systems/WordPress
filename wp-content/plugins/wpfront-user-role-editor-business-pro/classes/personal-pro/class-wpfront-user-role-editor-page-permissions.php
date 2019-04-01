<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "class-wpfront-user-role-editor-post-type-permissions.php");

if (!class_exists('WPFront_User_Role_Editor_Page_Permissions')) {

    /**
     * Post Permissions
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Page_Permissions extends WPFront_User_Role_Editor_Post_Type_Permissions {

        public function __construct($main) {
            parent::__construct('page', $main);
        }

    }

}

