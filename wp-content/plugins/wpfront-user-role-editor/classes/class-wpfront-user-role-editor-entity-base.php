<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once(plugin_dir_path(__FILE__) . "base/class-wpfront-entity-base.php");

if (!class_exists('WPFront_User_Role_Editor_Entity_Base')) {

    /**
     * User Role Editor Entity Base
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    abstract class WPFront_User_Role_Editor_Entity_Base extends WPFront_Entity_Base {

        public function __construct($table_name) {
            parent::__construct(
                    WPFront_User_Role_Editor::PLUGIN_SLUG, 
                    WPFront_User_Role_Editor::VERSION, 
                    $this instanceof WPFront_User_Role_Editor_Entity_Options ? $this : new WPFront_User_Role_Editor_Entity_Options(), 
                    'ure_' . $table_name
            );
        }

    }

}