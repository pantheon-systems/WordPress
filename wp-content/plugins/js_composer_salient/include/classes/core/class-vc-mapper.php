<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder Main manager.
 *
 * @package WPBakeryPageBuilder
 * @since   4.2
 */

/**
 * Vc mapper new class. On maintenance
 * Allows to bind hooks for shortcodes.
 * @since 4.2
 */
class Vc_Mapper {
	/**
	 * @since 4.2
	 * Stores mapping activities list which where called before initialization
	 * @var array
	 */
	protected $init_activity = array();
	/**
	 *
	 *
	 * @since 4.9
	 *
	 * @var array
	 */
	protected $element_activities = array();

	protected $hasAccess = array();

	// @todo fix_roles and maybe remove/@deprecate this
	protected $checkForAccess = true;

	/**
	 * @since 4.2
	 */
	function __construct() {
	}

	/**
	 * Include params list objects and calls all stored activity methods.
	 *
	 * @since  4.2
	 * @access public
	 */
	public function init() {
		do_action( 'vc_mapper_init_before' );
		require_once vc_path_dir( 'PARAMS_DIR', 'load.php' );
		WPBMap::setInit();
		require_once vc_path_dir( 'CONFIG_DIR', 'lean-map.php' );
		$this->callActivities();
		do_action( 'vc_mapper_init_after' );
	}

	/**
	 * This method is called by VC objects methods if it is called before VC initialization.
	 *
	 * @see WPBMAP
	 * @since  4.2
	 * @access public
	 *
	 * @param $object - mame of class object
	 * @param $method - method name
	 * @param array $params - list of attributes for object method
	 */
	public function addActivity( $object, $method, $params = array() ) {
		$this->init_activity[] = array(
			$object,
			$method,
			$params,
		);
	}

	/**
	 * This method is called by VC objects methods if it is called before VC initialization.
	 *
	 * @see WPBMAP
	 * @since  4.9
	 * @access public
	 *
	 * @param $tag - shortcode tag of element
	 * @param $method - method name
	 * @param array $params - list of attributes for object method
	 */
	public function addElementActivity( $tag, $method, $params = array() ) {
		if ( ! isset( $this->element_activities[ $tag ] ) ) {
			$this->element_activities[ $tag ] = array();
		}
		$this->element_activities[ $tag ][] = array(
			$method,
			$params,
		);
	}

	/**
	 * Call all stored activities.
	 *
	 * Called by init method. List of activities stored by $init_activity are created by other objects called after
	 * initialization.
	 *
	 * @since  4.2
	 * @access public
	 */
	protected function callActivities() {
		do_action( 'vc_mapper_call_activities_before' );
		foreach ( $this->init_activity as $activity ) {
			list( $object, $method, $params ) = $activity;
			if ( 'mapper' === $object ) {
				switch ( $method ) {
					case 'map':
						$currentScope = WPBMap::getScope();
						if ( isset( $params['scope'] ) ) {
							WPBMap::setScope( $params['scope'] );
						}
						WPBMap::map( $params['tag'], $params['attributes'] );
						WPBMap::setScope( $currentScope );
						break;
					case 'drop_param':
						WPBMap::dropParam( $params['name'], $params['attribute_name'] );
						break;
					case 'add_param':
						WPBMap::addParam( $params['name'], $params['attribute'] );
						break;
					case 'mutate_param':
						WPBMap::mutateParam( $params['name'], $params['attribute'] );
						break;
					case 'drop_all_shortcodes':
						WPBMap::dropAllShortcodes();
						break;
					case 'drop_shortcode':
						WPBMap::dropShortcode( $params['name'] );
						break;
					case 'modify':
						WPBMap::modify( $params['name'], $params['setting_name'], $params['value'] );
						break;
				}
			}
		}
	}

	/**
	 * Does user has access to modify/clone/delete/add shortcode
	 *
	 * @param $shortcode
	 *
	 * @todo fix_roles and maybe remove/@deprecate this
	 * @since 4.5
	 * @return bool
	 */
	public function userHasAccess( $shortcode ) {
		if ( $this->isCheckForAccess() ) {
			if ( isset( $this->hasAccess[ $shortcode ] ) ) {
				return $this->hasAccess[ $shortcode ];
			} else {
				$this->hasAccess[ $shortcode ] = vc_user_access_check_shortcode_edit( $shortcode );
			}

			return $this->hasAccess[ $shortcode ];
		}

		return true;
	}

	/**
	 * @todo fix_roles and maybe remove/@deprecate this
	 * @since 4.5
	 * @return bool
	 */
	public function isCheckForAccess() {
		return $this->checkForAccess;
	}

	/**
	 * @todo fix_roles and maybe remove/@deprecate this
	 * @since 4.5
	 *
	 * @param bool $checkForAccess
	 */
	public function setCheckForAccess( $checkForAccess ) {
		$this->checkForAccess = $checkForAccess;
	}

	public function callElementActivities( $tag ) {
		do_action( 'vc_mapper_call_activities_before' );
		if ( isset( $this->element_activities[ $tag ] ) ) {
			foreach ( $this->element_activities[ $tag ] as $activity ) {
				list( $method, $params ) = $activity;
				switch ( $method ) {
					case 'drop_param':
						WPBMap::dropParam( $params['name'], $params['attribute_name'] );
						break;
					case 'add_param':
						WPBMap::addParam( $params['name'], $params['attribute'] );
						break;
					case 'mutate_param':
						WPBMap::mutateParam( $params['name'], $params['attribute'] );
						break;
					case 'drop_shortcode':
						WPBMap::dropShortcode( $params['name'] );
						break;
					case 'modify':
						WPBMap::modify( $params['name'], $params['setting_name'], $params['value'] );
						break;
				}
			}
		}
	}
}
