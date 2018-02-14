<?php
/**
 * 
 */

require_once 'BngCustomTypes.php';
require_once 'BngShortcodes.php';
require_once 'BngMigration.php';

class BngLocationTool
{
	protected static $instance = null;
	
	public $location;
	public $custom;
	public $shortcodes;
	public $migration;
	

	public function __construct()
	{
		$prefix = 'bng_';


		$custom = new BngCustomTypes($prefix);
		$this->custom = $custom;

		$shortcodes = new BngShortcodes($prefix);
		$this->shortcodes = $shortcodes;

		$this->migration = new BngMigration($prefix);
		// Wordpress Hooks
	}

	public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}

?>