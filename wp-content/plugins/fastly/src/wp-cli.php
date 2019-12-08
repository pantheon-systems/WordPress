<?php
if (!class_exists('Purgely_Command')) :

    /**
     * Define the "fastly" WP CLI command.
     */
    class Purgely_Command extends WP_CLI_Command
    {
        /**
         * Purge a URL, post ID(s), or purge-all.
         *
         * ## EXAMPLES
         *
         * Purge all
         *  wp fastly purge all
         *
         * Purge ID-s
         *  wp fastly purge id 56
         *
         * Purge URL
         *  wp fastly purge url http://www.wired.com/category/design/
         *
         * @param  array $args
         * @return void
         */
        public function purge($args)
        {

            // Collect arguments
            $type = !empty($args[0]) ? $args[0] : false;
            $thing = !empty($args[1]) ? $args[1] : false;

            if ($type === 'id') {
                $type = Purgely_Purge::KEY_COLLECTION;
            }

            // Check supported purge types, add ids to supported types
            if (!in_array($type, Purgely_Purge::get_purge_types())) {
                WP_CLI::error(__('Missing or invalid purge type.', 'purgely'));
                return;
            }

            if ($type === Purgely_Purge::ALL && !Purgely_Settings::get_setting('allow_purge_all')) {
                WP_CLI::error(__('Allow Full Cache Purges first.', 'purgely'));
                return;
            }

            // Check data to be purged
            if (!$thing && $type !== Purgely_Purge::ALL) {
                WP_CLI::error(__('Missing thing that needs to be purged.', 'purgely'));
                return;
            } elseif ($thing && $type === Purgely_Purge::URL) {
                if (!is_url($thing)) {
                    WP_CLI::error(__('Invalid URL.', 'purgely'));
                    return;
                }
            }

            // Set wp original certificate instead of wp-cli certificate
            Requests::set_certificate_path(ABSPATH . WPINC . '/certificates/ca-bundle.crt');

            $purgely = new Purgely_Purge();

            // Find related IDs for inputed ID
            if ($type === Purgely_Purge::KEY_COLLECTION) {
                $related_collection_object = new Purgely_Related_Surrogate_Keys($thing);
                $thing = $related_collection_object->locate_all();
                // Issue purge request
                foreach ($thing as $tg) {
                    $result = $purgely->purge($type, $tg);
                }
            } else {
                $result = $purgely->purge($type, $thing);
            }

            if ($type === Purgely_Purge::ALL) {
                $message = 'all';
            } elseif ($type === Purgely_Purge::KEY_COLLECTION) {
                $message = 'ID:' . $args[1];
            } elseif ($type === Purgely_Purge::URL) {
                $message = esc_url($thing);
            } else {
                $message = $thing;
            }

            if ($result) {
                WP_CLI::success(sprintf(__('Successfully purged - %s', 'purgely'), $message));
            } else {
                WP_CLI::error(sprintf(__('Purge failed - %s - (enable and check logging for more information)'), $message));
            }
        }

        /**
         * Sets wanted configuration or lists available configuration options
         * @param $args
         */
        public function configset($args)
        {
            $config_section = !empty($args[0]) ? $args[0] : false;
            $config_option = !empty($args[1]) ? $args[1] : false;
            $config_value = isset($args[2]) ? $args[2] : false;

            if($config_section === false || $config_option === false || $config_value === false) {
                $message = $this->_color('red', 'Missing section, option or value');
                $msg = $this->_color('green', 'wp fastly configset {section} {option} {value}');
                $message .= "\nUsage: {$msg}";
                $msg = $this->_color('gold', 'general, advanced, webhooks');
                $message .= "\nSections: $msg";
                $msg = $this->_color('green', 'wp fastly configlist {section}');
                $message .= "\nTo list options from certain section run: $msg";
                $msg = $this->_color('green', '{true|false}');
                $message .= "\nFor yes/no configuration options use : $msg";
                WP_CLI::error(sprintf(__($message, 'purgely')));
                return;
            }

            // Sanitize
            $registered_settings = Purgely_Settings::get_registered_settings();
            if(array_key_exists($config_option, $registered_settings)) {
                $callback = $registered_settings[$config_option]['sanitize_callback'];
                // False will return false when checking validity
                if(in_array($config_value, array('1', 1, 'true'), true)) {
                    $config_value = 'true';
                }

                if(in_array($config_value, array('0', 0, 'false'), true)) {
                    $config_value = 'false';
                } else {
                    $config_value = call_user_func($callback, $config_value);
                    // TODO standardization issue(visual)
                    if($config_value === true) {
                        $config_value = 'true';
                    }
                }
            }

            // List configuration options and determine if option and section exists
            if(array_key_exists($config_section, Purgely_Settings::$lists)) {
                $section = Purgely_Settings::$lists[$config_section];
                $settings_list = Purgely_Settings::get_database_section_settings($section);
                if(array_key_exists($config_option, $settings_list)) {
                    // Update value
                    if($config_value === false){
                        WP_CLI::error(sprintf(__('Invalid configuration option value')));
                        return;
                    }
                    $settings_list[$config_option] = $config_value;
                } else {
                    WP_CLI::error(sprintf(__('Invalid configuration option')));
                    return;
                }
            } else {
                WP_CLI::error(sprintf(__('Invalid configuration section')));
                return;
            }

            if(update_option($section, $settings_list)) {
                $config_option = $this->_color('gold', $config_option);
                $config_value = $this->_color('red', $config_value);
                WP_CLI::success(sprintf(__(
                "Successfully saved $config_option option in configuration with value $config_value", 'purgely'))
                );
            } else {
                WP_CLI::error(sprintf(__('Failed to save option. Please update value.', 'purgely')));
            }
        }

        /**
         * Lists configuration by sections
         * @param $args
         */
        public function configlist($args)
        {
            $conf_list = !empty($args[0]) ? $args[0] : false;

            if(!$conf_list) {
                $msg = $this->_color('gold', 'wp fastly configlist {general|advanced|webhooks}');

                WP_CLI::error(sprintf(__("Usage: {$msg}", 'purgely')));
                return;
            }

            // List configuration options
            if(array_key_exists($conf_list, Purgely_Settings::$lists)) {
                $section = Purgely_Settings::$lists[$conf_list];
                $settings_list = Purgely_Settings::get_database_section_settings($section);
                foreach($settings_list as $key => $value) {

                    // Output 1 and 0 as true/false TODO issue with standardization of admin and cli saving (only visual)
                    if(in_array($value, array('1', 1, 'true', true), true)) {
                        $value = 'true';
                    } elseif (in_array($value, array('0', 0, 'false', false), true)){
                        $value = 'false';
                    }

                    $key = $this->_color('gold', $key);
                    $value = $this->_color('red', $value);
                    echo "\n $key = $value\n";
                }
                return;
            }
            $msg = $this->_color('gold', 'wp fastly configlist {general|advanced|webhooks}');
            WP_CLI::error(sprintf(__("Invalid config list selected. Usage: {$msg}", 'purgely')));
            return;
        }

        /**
         * Set color on string
         * @param $color
         * @param $string
         * @return string
         */
        protected function _color($color, $string)
        {
            if(!$color || $string === false) {
                return $string;
            }
            switch ($color){
                case ('red'):
                    $string = "\e[31m" . $string . "\e[0m";
                    break;
                case ('gold'):
                    $string = "\e[33m" . $string . "\e[0m";
                    break;
                case ('green'):
                    $string = "\e[32m" . $string . "\e[0m";
                    break;
                default:
                    break;
            }
            return $string;
        }
    }
endif;

WP_CLI::add_command('fastly', 'Purgely_Command');
