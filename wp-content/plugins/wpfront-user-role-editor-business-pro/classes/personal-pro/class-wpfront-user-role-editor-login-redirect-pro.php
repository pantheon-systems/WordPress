<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Login_Redirect_Pro')) {

    /**
     * Login Redirect Controller
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Login_Redirect_Pro extends WPFront_User_Role_Editor_Login_Redirect {

        protected function role_supported($role, $extend = FALSE) {
            return parent::role_supported($role, TRUE);
        }

    }

}