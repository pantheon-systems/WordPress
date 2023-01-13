<?php
/**
 * RGBA Color control class.
 *
 * @package     OceanWP WordPress theme
 * @subpackage  Controls
 * @see   		https://github.com/justintadlock/butterbean
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RGBA Color control
 *
 * @since  1.0.0
 * @access public
 */
class OceanWP_ButterBean_Control_RGBA_Color extends ButterBean_Control {

	/**
	 * The type of control.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'rgba-color';

	/**
	 * Custom options to pass to the color picker.  Mostly, this is a wrapper for
	 * `iris()`, which is bundled with core WP.  However, if they change pickers
	 * in the future, it may correspond to a different script.
	 *
	 * @link   http://automattic.github.io/Iris/#options
	 * @link   https://make.wordpress.org/core/2012/11/30/new-color-picker-in-wp-3-5/
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $options = array();

	/**
	 * Gets the attributes for the control.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_attr() {
		$attr = parent::get_attr();

		$setting = $this->get_setting();

		$attr['type']               = 'text';
		$attr['maxlength']          = 7;
		$attr['data-default'] 		= $setting ? $setting->default : '';

		return $attr;
	}

	/**
	 * Get the value for the setting.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $setting
	 * @return mixed
	 */
	public function get_value( $setting = 'default' ) {

		$value = parent::get_value( $setting );

		return $value;
	}

	/**
	 * Adds custom data to the json array. This data is passed to the Underscore template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		$this->json['options'] = $this->options;
	}

}
