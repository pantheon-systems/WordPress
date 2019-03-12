<?php
/**
 * Third party plugin compatibility.
 *
 * @link       http://cookielawinfo.com/
 * @since      1.7.2
 *
 * @package    Cookie_Law_Info
 * @subpackage Cookie_Law_Info/third-party
 */

/**
 * Third party plugin compatibility.
 *
 *
 * @package    Cookie_Law_Info
 * @subpackage Cookie_Law_Info/third-party
 * @author     WebToffee <info@webtoffee.com>
 */
class Cookie_Law_Info_Third_Party {

	/*
	 * scripts list, Script folder and main file must be same as that of script name
	 * Please check the `register_scripts` method for more details
	 */
	private $scripts=array(
		'pixelyoursite',
	);

	public static $existing_scripts=array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.7.2
	 */
	public function __construct() {
		
	}

	/**
	 * Registering third party scripts 
	 *
	 */
	public function register_scripts()
	{
		foreach ($this->scripts as $script) //loop through script list and include its file
		{
			$script_file=plugin_dir_path( __FILE__ )."scripts/$script/$script.php";
			if(file_exists($script_file))
			{
				self::$existing_scripts[]=$script; //this is for module_exits checking
				require_once $script_file;
			}
		}
	}
}