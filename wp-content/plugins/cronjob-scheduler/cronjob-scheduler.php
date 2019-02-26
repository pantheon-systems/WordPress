<?php namespace chrispage1;
/**
 * Plugin Name: Cronjob Scheduler
 * Description: Plugin to manage, edit and remove WordPress cron job tasks
 * Version: 1.30
 * Author: chrispage1
 * Author URI: https://profiles.wordpress.org/chrispage1/
 *
 * @author chrispage1 <https://profiles.wordpress.org/chrispage1/>
 * @license http://www.gnu.org/licenses/gpl.html GNU v3
 *
 */

class Commons {

    protected

        /**
         * Array to store all plugin settings
         * @var array
         */
        $_options = array (),

        /**
         * Admin notice ID
         * @var string
         */

        $_adminNoticeID = 'session_notices',
        /**
         * Prefix for option parameters
         * @var string
         */
        $_optionPrefix = null,

        /**
         * Array to store magic getter/setter params
         * @var array
         */
        $__get = array ();




    /**
     * Supr3meBaseAbstract Plugin constructor class
     * @param string $plugin_slug  Plugin slug for settings saved to database
     * @param array  $options      Array of permittable options for this plugin
     */
    public function __construct ($plugin_slug, array $options) {

        // if the session has not been started, start it
        if(!isset($_SESSION)) session_start();

        // set our plugin slug and generate our option prefix
        $this->_optionPrefix = '_chrispage1_' . sanitize_title($plugin_slug);

        // populate our array of options
        $this->_options = $options;
        $this->_options = $this->get_option();

        // create filter for admin notices
        $this->action(array ('admin_notices', 'network_admin_notices'), 'notices_display', 10);

        // return current instance
        return $this;
    }



    /**
     * Magic __get() method to get undefined params
     * @param  string $param_name   Parameter name
     * @return mixed                Value of the parameter
     */
    public function __get($param_name) {

        // create $return param
        $return = false;

        // if we have already instantiated this param, return it
        if(array_key_exists($param_name, $this->__get)) {
            return $this->__get[$param_name];
        }

        // return our populated $return and store
        // it in our caching parameter
        return $this->__get[$param_name] = $return;
    }



    /**
     * Magic __set() method to set magic params
     * @param string $param_name    Parameter name
     * @param mixed $param_value    Parameter value
     * @return mixed
     */
    public function __set($param_name, $param_value) {
        // set the param against our $this->__get array
        return $this->__get[$param_name] = $param_value;
    }


    /**
     * Get timezone from WordPress installation
     * @return \DateTimeZone
     */
    protected function getTimezone () {

        // get our timezone string
        $sTimezone = $this->get_option('timezone_string');

        // return DateTimeZone object
        return new \DateTimeZone($sTimezone);
    }



    /**
     * Update plugin options in the database
     * @param  string $key          Key to store the option under
     * @param  mixed $value         Value to store against the option
     * @return bool                 Returns true on success, false on failure
     */
    public function update_option ($key, $value) {

        // check if the option exists
        if(array_key_exists($key, $this->_options)) {

            // attempt to update the option
            if(update_site_option($this->_optionPrefix . $key, $value)) {

                // set option on our options array
                $this->_options[$key] = $value;
                return true;
            }
        }

        // if we get to this point, update failed
        return false;
    }




    /**
     * Gets plugin option / options from the database
     *
     * @param string $key The key to get
     * @return array
     */
    public function get_option ($key = null) {

        // if the key is not null, attempt to return a single value
        if(!is_null($key)) {
            if(isset($this->_options[$key]) && !is_null($this->_options[$key])) {
                // return our cached option
                return $this->_options[$key];
            } else {
                // get it from the database and return it
                return get_site_option($this->_optionPrefix . $key);
            }
        }

        // loop through each option and get it from the database
        foreach($this->_options as $key => $value) {

            // try and get this option from the database
            $db_loaded = get_site_option($this->_optionPrefix . $key);

            // get option with a default
            $this->_options[$key] = ($db_loaded !== false ? $db_loaded : $value);
        }

        // return the resulting array
        return $this->_options;
    }



    /**
     * Basic method to load view file
     * @param  string $name         Name of the view to load
     * @param  array $variables     Array of variables to make available to view
     * @return void
     */
    public function load_view ($name, $variables = array()) {

        // set options as an accesible variable
        $options = $this->_options;
        $options['plugin_dir'] = dirname(__FILE__);

        // extract variables as parameters and then remove $variables
        extract($variables);
        unset($variables);

        // load in our view file
        require_once(dirname(__FILE__) . '/views/' . $name . '.php');

        // unset our options
        unset($options);
    }



    /**
     * Helper function for WordPress actions
     * @param  string|array $hook   Name of the filter to use
     * @param  string|null $method  The method to call, defaults to $hook
     * @param  int $priority        Action priority
     * @return void
     */
    public function action ($hook, $method = null, $priority = 10) {

        // convert the $hook string into an array
        if(!is_array($hook))
            $hook = array($hook);

        // return private hook method with action flag
        foreach($hook as $hook_single)
            $this->_hook('action', $hook_single, $method, $priority);
    }



    /**
     * Helper function for WordPress filters
     * @param  string $hook         Name of the filter to use
     * @param  string|null $method  The method to call, defaults to $hook
     * @param  int $priority        Filter priority
     * @return void
     */
    public function filter ($hook, $method = null, $priority = 10) {

        // convert the $hook string into an array
        if(!is_array($hook))
            $hook = array($hook);

        // return private hook method with filter flag
        foreach($hook as $hook_single)
            $this->_hook('filter', $hook_single, $method, $priority);
    }



    /**
     * Registers WordPress hooks and actions
     * @param  string $type         Either action or filter type
     * @param  string $hook         The WordPress filter/action
     * @param  string|null $method  The method to call
     * @param  int $priority        Action/filter priority
     * @return mixed
     */
    private function _hook ($type, $hook, $method = null, $priority = 10) {

        // check if we are trying to run a valid type (default to action)
        if(!in_array($type, array('filter', 'action'))) $type = 'action';

        // prefix the type
        $type = 'add_' . $type;

        // set the method to be the hook if not defined
        if(is_null($method)) $method = $hook;

        // return the result of the WordPress filter/action
        return $type($hook, is_object($method) ? $method :  array($this, $method), $priority, 999);
    }



    /**
     * Sets a notice for the current session
     * @param string $message       Message to output
     * @param string $class         Class of the message. Should be one of updated, error or updated-nag
     * @return void
     */
    public function notice_set ($message, $class = 'updated') {

        // @see http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices

        // set the session parameter
        $_SESSION[$this->_adminNoticeID][] = array (
            'message'   => $message,
            'class'     => $class,
        );
    }



    /**
     * Outputs admin notices using the admin_notices hook
     * @return void
     */
    public function notices_display () {

        // @see http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices

        // dont continue if our notices are not an array
        if(!isset($_SESSION[$this->_adminNoticeID]) || !is_array($_SESSION[$this->_adminNoticeID])) return;

        // if our session is an array, loop through each message
        foreach($_SESSION[$this->_adminNoticeID] as $key => $notice) {

            // output our message
            echo "<div class='{$notice['class']}'><p>{$notice['message']}</p></div>";

            // unset it as its been shown
            unset($_SESSION[$this->_adminNoticeID][$key]);
        }
    }

}

class CronjobScheduler extends Commons {

    public
        $_schedules = array (),
        $_cronjobs = array (),
        $_notifications = array (),
        $_admin_page = null;

    const
        NOTIFICATION_UPDATED    = 'updated',
        NOTIFICATION_ERROR      = 'error';


    /**
     * Constructor class that provides basic setup information
     *
     * @return bool Returns true on completion
     */
    public function __construct () {

        // run parent constructor
        parent::__construct('cronjob-scheduler', array ());

        // determine cronjobs path
        $cronjobs_include = dirname(__FILE__) . '/cronjobs.php';

        // create the $cronjobs_include file if it doesnt exist
        if(!file_exists($cronjobs_include)) {
            @file_put_contents(
                $cronjobs_include,
                "<?php \r\n/**\r\n * Cronjob Scheduler Include File\r\n *\r\n" .
                " * This page allows you to create Cronjob Scheduler custom actions\r\n" .
                " * through the WordPress admin. Actions can also be created within\r\n" .
                " * theme files or other PHP files throughout your WordPress install.\r\n *\r\n" .
                " * Do not edit this file unless you have a good understanding of PHP, \r\n" .
                " * saving this with invalid code can render your WordPress install inoperable\r\n" .
                " * until you can fix the issue using FTP or another file editing utility.\r\n" .
                "**/\r\n\r\n" .
                $this->sample_action_template('my_cronjob_action')
            );
        }

        // load in cronjobs.php file but its not essential
        if(file_exists($cronjobs_include)) {
            include($cronjobs_include);
        }

        // get schedules from database
        $this->_schedules = get_option('cjs_schedules', array ());

        // create new schedules filter
        $this->filter('cron_schedules', 'add_schedules_to_filter');

        // add filter so we can determine what page we are on
        $this->filter('current_screen', 'current_screen');

        // setup the admin menu
        $this->filter('admin_menu', 'plugin_admin_menu');

        // return true
        return true;
    }

    /**
     * Determine the current screen and
     * @return bool Returns true on completion
     */
    public function current_screen() {

        // get current screen info
        $current_screen = get_current_screen();

        // check to ensure we are on the settings page before handling certain things
        if($current_screen->id == 'settings_page_cronjob_scheduler') {

            // handle post if possible
            if(count($_POST)) $this->handle_post();

            // check if we can handle 'd'
            if(isset($_GET['d']) && $_GET['d'] == 'true') {
                $message = 'Great, thanks for the donation! We appreciate you supporting our work!';
                $this->notice_set($message, self::NOTIFICATION_UPDATED);
            }
        }

        // return true on completion
        return true;
    }

    /**
     * Gets WordPress schedules
     * @return array Array of schedules sorted in correct order
     */
    public function get_schedules () {

        $schedules = wp_get_schedules();

        uasort($schedules, array ($this, 'get_schedules_sort'));

        return $schedules;
    }

    /**
     * Sorting method for get_schedules()
     * @param  array $a Schedule array $a
     * @param  array $b Schedule array $b to compare
     * @return int      Returns sorting integer
     */
    public function get_schedules_sort ($a, $b) {

        // if schedules are the same return 0
        if($a['interval'] == $b['interval']) return 0;

        // return comparison int
        return $a['interval'] < $b['interval'] ? -1 : 1;
    }

    /**
     * Sets up the admin menu interface
     *
     * @return bool Returns result of add_options_page() method
     */
    public function plugin_admin_menu () {
        $this->_admin_page = add_options_page (
            'Cronjob Scheduler',
            'Cronjob Scheduler',
            'manage_options',
            'cronjob_scheduler',
            array ($this, 'admin_settings_page')
        );

        // run admin_head action for this page
        add_action('admin_head-' . $this->_admin_page, array($this, 'plugin_header'));

        return $this->_admin_page;
    }

    /**
     * Outputs information into the plugin header.
     * Styles, scripts, etc.
     */
    public function plugin_header() {
        echo '<style type="text/css">
            .wp-core-ui .button-red {
                background-color: #AA2C2A;
                border-color: #972725;
                box-shadow: inset 0 1px 0 rgba(198,70,68,.5),0 1px 0 rgba(0,0,0,.15);
                -o-box-shadow: inset 0 1px 0 rgba(198,70,68,.5),0 1px 0 rgba(0,0,0,.15);
                -ms-box-shadow: inset 0 1px 0 rgba(198,70,68,.5),0 1px 0 rgba(0,0,0,.15);
                -moz-box-shadow: inset 0 1px 0 rgba(198,70,68,.5),0 1px 0 rgba(0,0,0,.15);
                -webkit-box-shadow: inset 0 1px 0 rgba(198,70,68,.5),0 1px 0 rgba(0,0,0,.15);
            }
            .wp-core-ui .button-red:hover, .wp-core-ui .button-red:focus {
                background-color: #BD302E;
                border-color: #972725;
                box-shadow: inset 0 1px 0 rgba(198,70,68,.5),0 1px 0 rgba(0,0,0,.15);
                -o-box-shadow: inset 0 1px 0 rgba(198,70,68,.5),0 1px 0 rgba(0,0,0,.15);
                -ms-box-shadow: inset 0 1px 0 rgba(198,70,68,.5),0 1px 0 rgba(0,0,0,.15);
                -moz-box-shadow: inset 0 1px 0 rgba(198,70,68,.5),0 1px 0 rgba(0,0,0,.15);
                -webkit-box-shadow: inset 0 1px 0 rgba(198,70,68,.5),0 1px 0 rgba(0,0,0,.15);
            }
            .responsive-table {
                width: 100%;
                overflow-y: hidden;
                overflow-x: scroll;
                -ms-overflow-style: -ms-autohiding-scrollbar;
                -webkit-overflow-scrolling: touch;
            }
          </style>';
    }

    /**
     * Register all schedules
     * @param array $schedules Array of WordPress schedules
     * @return array
     */
    public function add_schedules_to_filter($schedules) {
        // loop through each schedule and add to $schedules
        foreach($this->_schedules as $name => $schedule) {
            $schedules[$name] = $schedule;
        }

        // return resulting array of $schedules
        return $schedules;
    }

    /**
     * Checks if the cron job setup has actually been completed or not
     *
     * @return bool Returns true if the wp cron appears to be configured
     */
    public function cron_configured () {
        return (defined('DISABLE_WP_CRON'));
    }

    /**
     * Determines the friendly schedule name
     *
     * @return string The friendly name for a schedule
     */
    public function get_friendly_schedule_name($item_key) {

        // loop through all schedules and get the display name
        foreach(wp_get_schedules() as $key => $schedule) {
            if($item_key === $key) return $schedule['display'];
        }

        // we couldnt get it!
        return $item_key;
    }

    /**
     * Parses the crons into a format we can loop through
     * easily
     * @return array returns an array of cron jobs
     */
    public function parse_crons () {

        // create our array of crons
        $aCrons = _get_cron_array();

        // loop through each cron job that WordPress has
        foreach($aCrons as $timestamp => $crons) {

            // loop through all of our crons
            foreach($crons as $hook => $cron_args) {
                foreach($cron_args as $key => $cron) {

                    // skip any that dont have a unique ID
                    if(!isset($cron['args']['uniqid'])) {

                        // skip WordPress reserved
                        if(substr($hook, 0, 3) == 'wp_') continue;

                        // support old events from previous versions by rescheduling
                        {

                            // create uniqid
                            $uniqid = md5($hook . $cron['schedule']);

                            // check we haven't already got an equivalent
                            if( !wp_next_scheduled($hook, array ('uniqid' => $uniqid) ) ) {

                                // unschedule existing event
                                wp_unschedule_event($timestamp, $hook, $cron['args']);

                                // create new uniqid
                                $uniqid = md5($hook . $cron['schedule']);
                                $cron['args']['uniqid'] = $uniqid;

                                // schedule our new event
                                wp_schedule_event($timestamp, $cron['schedule'], $hook, array (
                                    'uniqid'        => $cron['args']['uniqid'],
                                ));
                            } else {

                                // already scheduled
                                continue;

                            }
                        }
                    }

                    // add our cronjob to our array
                    $this->_cronjobs[$cron['args']['uniqid']] = array (
                        'last_run'      => $timestamp,
                        'hook'          => $hook,
                        'schedule'      => $cron['schedule'],
                        'display_name'  => $this->get_friendly_schedule_name($cron['schedule']),
                        'interval'      => $cron['interval'],
                        'uniqid'        => $cron['args']['uniqid'],
                    );
                }
            }
        }

        // sort cron jobs by key and return them
        uasort($this->_cronjobs, array($this, '_sort_crons'));
        return $this->_cronjobs;
    }

    /**
     * Sorts the crons into a particular order
     * @param  array $a Cron array 1
     * @param  array $b Cron array 2
     * @return int    Ordering integer
     */
    private function _sort_crons($a, $b) {
        // if they are the same, return 0
        if($a['hook'] == $b['hook']) return 0;

        // return 1 or -1
        return ($a['hook'] > $b['hook'] ? 1 : -1);
    }

    /**
     * Converts a string to slug format
     * @param  string $string String to convert
     * @param bool $allow_underscore Set to true to replace invalid chars with underscore
     * @return string         String converted to slug format
     */
    public function to_slug($string, $allow_underscore = false) {
        return rtrim(strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', ($allow_underscore ? '_' : ''), $string))),'_');
    }

    /**
     * Returns the ideal cronjob string
     * @return string string
     */
    public function get_cron_string() {
        return
            '* * * * * wget -qO- &quot;' . esc_attr(get_bloginfo('wpurl')) .
            '/wp-cron.php?doing_wp_cron&quot; &>/dev/null';
    }

    /**
     * Returns an example cron action
     * @param  [type] $action_name [description]
     * @return [type]              [description]
     */
    public function sample_action_template($action_name) {

        // convert action to slug
        $action_name = $this->to_slug($action_name, true);

        // return created string
        return
            'function ' . $action_name .' () {' . "\r\n" .
            '    // code to execute on cron run' . "\r\n" .
            '} add_action(\'' . $action_name .'\', \'' . $action_name . '\');' . "\r\n";
    }

    /**
     * Handle plugin post requests
     * @return bool returns true on request
     */
    public function handle_post () {

        // verify the nonce field
        if(!wp_verify_nonce($_POST['scheduler_nonce'])) {
            // the nonce is invalid, return false
            $this->notice_set('Request could not be handled as it was invalid.', self::NOTIFICATION_ERROR);
            return false;
        }

        // attempt to delete an existing cronjob
        if(isset($_POST['delete'])) {

            // determine the $uniqid
            $uniqid = array_keys($_POST['delete']);
            if(is_array($uniqid) && array_key_exists(0, $uniqid)) {
                $uniqid = $uniqid[0];
            }

            // set our deleted flag to false
            $deleted = false;

            // loop through each cron and find the match!
            if($attributes = $this->find_cron_by_uniqid($uniqid)) {

                $args = array (
                    'uniqid'    => $uniqid,
                );

                // get the next scheduled event so we can delete it
                $next_scheduled = wp_next_scheduled($attributes['hook'], $args);

                // unschedule our event
                wp_unschedule_event($next_scheduled, $attributes['hook'], $args);

                // set our deleted flag to true
                $deleted = true;
            }

            // set the cronjobs array back to blank to avoid issues further down
            // the script
            $this->_cronjobs = array ();

            // output message and return true
            if($deleted) {
                $message = 'The action has been successfully unscheduled!';
                $this->notice_set($message, self::NOTIFICATION_UPDATED);
            } else {
                $message = 'Unable to find the scheduled action. Please try again.';
                $this->notice_set($message, self::NOTIFICATION_ERROR);
            }

            // return $deleted status
            return $deleted;

        } elseif(isset($_POST['trigger'])) {

            // get parameters
            $params = $_POST['trigger'];
            $uniqid = current(array_keys($params));

            // find cron by the unique ID
            if($attributes = $this->find_cron_by_uniqid($uniqid)) {
                if(has_action($attributes['hook'])) {
                    // run the action
                    do_action($attributes['hook']);

                    // success, action run
                    $message = 'The action was successfully triggered.';
                    $this->notice_set($message, self::NOTIFICATION_UPDATED);
                    return true;

                } else {
                    // the action doesnt exist
                    $message = 'The action \'' . $attributes['hook'] . '\' doesn\'t exist for this cronjob.';
                    $this->notice_set($message, self::NOTIFICATION_ERROR);
                    return false;
                }
            } else {
                // the cron doesnt exist
                $message = 'Something went wrong, we couldn\'t find cronjob.';
                $this->notice_set($message, self::NOTIFICATION_ERROR);
                return false;
            }

        } elseif(isset($_POST['cron'])) {

            $params = $_POST['cron'];

            if(!isset($params['method']) || !has_action($params['method'])) {

                // output error message because the method isnt valid
                $message = 'The cron action was invalid or does not exist. It is important that
                the action already exists before creating a new schedule.';
                $this->notice_set($message, self::NOTIFICATION_ERROR);
                return false;

            } elseif(substr($params['method'], 0, 3) == 'wp_') {

                // check if this is a system reserved cron job
                $message = 'You cannot create cron jobs that have a function prefixed with
                <span style="font-family: monospace">wp_</span> as these are system reserved.';
                $this->notice_set($message, self::NOTIFICATION_ERROR);
                return false;

            } elseif(!isset($params['schedule']) || !$this->get_friendly_schedule_name($params['schedule'])) {

                // output error message because the schedule isnt valid
                $message = 'The specified cron schedule was invalid. Please choose a valid schedule.';
                $this->notice_set($message, self::NOTIFICATION_ERROR);
                return false;

            } else {

                // check to ensure this cron doesnt already exist
                $uniqid = md5($params['method'] . $params['schedule']);
                if($this->find_cron_by_uniqid($uniqid)) {
                    $message = 'Failed to create because a cronjob with this schedule already exists.';
                    $this->notice_set($message, self::NOTIFICATION_ERROR);
                    return false;
                }
            }

            // validation passed, schedule the event to 60 seconds from now
            wp_schedule_event(time() - 60, $params['schedule'], $params['method'], array (
                'uniqid'    => $uniqid,
            ));

            $message = 'The cron has been scheduled successfully.';
            $this->notice_set($message, self::NOTIFICATION_UPDATED);


            unset($_POST['cron']);
            return true;

        } elseif(isset($_POST['deleteschedule'])) {

            // delete the custom schedule
            $schedule_id = array_keys($_POST['deleteschedule']);
            $schedule_id = $schedule_id[0];

            // see if the schedule actually exists
            if(array_key_exists($schedule_id, $this->_schedules)) {
                // delete the schedule
                unset($this->_schedules[$schedule_id]);
                update_option('cjs_schedules', $this->_schedules);

                $message = 'The schedule was deleted.';
                $this->notice_set($message, self::NOTIFICATION_UPDATED);
                return true;

            } else {
                // invalid schedule
                $message = 'The schedule could not be delete because we couldn\'t find it!';
                $this->notice_set($message, self::NOTIFICATION_ERROR);
                return false;
            }


        } elseif(isset($_POST['schedule'])) {

            // create a new post schedule

            $params = $_POST['schedule'];

            // check to ensure the interval is numeric
            if(!is_numeric($params['interval']) || $params['interval'] == 0) {
                $message = 'The interval should be numeric and be the number of minutes between each run. It cannot be 0!';
                $this->notice_set($message, self::NOTIFICATION_ERROR);
                return false;
            } else {
                $params['interval'] = $params['interval'] * 60;
            }

            // add to the schedules array
            $this->_schedules[$this->to_slug($params['display'] . '_' . $params['interval'])] = array (
                'interval' => $params['interval'],  // value was provided in seconds
                'display'  => $params['display']
            );

            // add the new schedules to WordPress option
            update_option('cjs_schedules', $this->_schedules);

            // if we get to this point, success!
            $message = 'A new schedule has been added.';
            $this->notice_set($message, self::NOTIFICATION_UPDATED);
            return true;

        }
    }

    /**
     * Finds cron based on uniqid
     * @param  string $uniqid Unique ID of the cronjob
     * @return array|bool         Array on success, false on failure
     */
    public function find_cron_by_uniqid($uniqid) {
        foreach($this->parse_crons() as $attributes) {
            if($uniqid == $attributes['uniqid']) {
                return $attributes;
            }
        }
        return false;
    }

    /**
     * Determines the location of the wp-config.php file
     * @return string returns absolute path to wp-config.php
     */
    public function get_wp_config_path() {

        // determine the current path
        $base = dirname(__FILE__);

        $path = dirname(dirname($base)) . '/wp-config.php';
        if(file_exists($path)) {
            // we have found the wp-config.php file
            return $path;
        } elseif(file_exists(dirname($path))) {
            // we have found the wp-config.php file
            return dirname($path) . '/wp-config.php';
        } else {
            return false;
        }
    }


    /**
     * Outputs the admin settings page container
     */
    public function admin_settings_page () {
        $this->load_view('admin_page');
    }
}

// create a new instance
new CronjobScheduler();