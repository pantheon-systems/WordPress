<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('EDD_SL_Plugin_Updater')) {
    require_once(plugin_dir_path(__FILE__) . "EDD_SL_Plugin_Updater.php");
}

if (!class_exists('WPFront_User_Role_Editor_Plugin_Updater')) {

    /**
     * Plugin Updater
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Plugin_Updater extends EDD_SL_Plugin_Updater {

        public function __construct($_api_url, $_plugin_file, $_api_data, $slug) {
            parent::__construct($_api_url, $_plugin_file, $_api_data);

            $this->slug = $slug;
        }

        protected function api_request($_action, $_data) {
            $time_key = $this->slug . '-plugin_updater_api_request_last_checked';
            $api_key = $this->slug . '-plugin_updater_api_request';

            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $time = $entity->get_option($time_key);

            if ($time === NULL || $time < time() - 6 * 3600) {
                $api = NULL;
            } else {
                $api = $entity->get_option($api_key);
            }

            if ($api === NULL) {
                $api = parent::api_request($_action, $_data);
                if ($api) {
                    $entity->update_option($api_key, serialize($api));
                    $entity->update_option($time_key, time());
                }
            } else {
                $api = unserialize($api);
            }

            return $api;
        }

        public function recheck() {
            $time_key = $this->slug . '-plugin_updater_api_request_last_checked';
            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $entity->update_option($time_key, 0);

            set_site_transient('update_plugins', null);
        }

    }

}