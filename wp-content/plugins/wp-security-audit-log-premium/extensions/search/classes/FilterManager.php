<?php
/**
 * Class: Filter Manager
 *
 * Filter Manager for search extension.
 *
 * @since 1.0.0
 * @package search-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_AS_FilterManager
 *
 * @package search-wsal
 */
class WSAL_AS_FilterManager {

	/**
	 * Array of filters - WSAL_AS_Filters_AbstractFilter[]
	 *
	 * @var array
	 */
	protected $filters = array();

	/**
	 * Widget cache.
	 *
	 * @var WSAL_AS_Filters_AbstractWidget[]
	 */
	protected $widgets = null;

	/**
	 * Instance of WSAL_SearchExtension.
	 *
	 * @var object
	 */
	protected $_plugin;

	/**
	 * Method: Constructor.
	 *
	 * @param object $plugin - Instance of WSAL_SearchExtension.
	 * @since 1.0.0
	 */
	public function __construct( WSAL_SearchExtension $plugin ) {
		$this->_plugin = $plugin;
		// Load filters.
		foreach ( glob( dirname( __FILE__ ) . '/Filters/*.php' ) as $file ) {
			$this->AddFromFile( $file );
		}
	}

	/**
	 * Add new filter from file inside autoloader path.
	 *
	 * @param string $file Path to file.
	 */
	public function AddFromFile( $file ) {
		$this->AddFromClass( $this->_plugin->wsal->GetClassFileClassName( $file ) );
	}

	/**
	 * Add new filter given class name.
	 *
	 * @param string $class Class name.
	 */
	public function AddFromClass( $class ) {
		if ( is_subclass_of( $class, 'WSAL_AS_Filters_AbstractFilter' ) ) {
			$this->AddInstance( new $class( $this->_plugin ) );
		}
	}

	/**
	 * Add newly created filter to list.
	 *
	 * @param WSAL_AS_Filters_AbstractFilter $filter The new view.
	 */
	public function AddInstance( WSAL_AS_Filters_AbstractFilter $filter ) {
		$this->filters[] = $filter;
		// Reset widget cache.
		if ( $this->widgets == null ) {
			$this->widgets = null;
		}
	}

	/**
	 * Get filters.
	 *
	 * @return WSAL_AS_Filters_AbstractFilter[]
	 */
	public function GetFilters() {
		return $this->filters;
	}

	/**
	 * Gets widgets grouped in arrays with widget class as key.
	 *
	 * @return WSAL_AS_Filters_AbstractWidget[][]
	 */
	public function GetWidgets() {
		if ( $this->widgets == null ) {
			$this->widgets = array();
			foreach ( $this->filters as $filter ) {
				foreach ( $filter->GetWidgets() as $widget ) {
					$class = get_class( $widget );
					if ( ! isset( $this->widgets[ $class ] ) ) {
						$this->widgets[ $class ] = array();
					}
					$this->widgets[ $class ][] = $widget;
				}
			}
		}
		return $this->widgets;
	}

	/**
	 * Find widget given filter and widget name.
	 *
	 * @param string $filter_name - Filter name.
	 * @param string $widget_name - Widget name.
	 * @return WSAL_AS_Filters_AbstractWidget|null
	 */
	public function FindWidget( $filter_name, $widget_name ) {
		foreach ( $this->filters as $filter ) {
			if ( $filter->GetSafeName() == $filter_name ) {
				foreach ( $filter->GetWidgets() as $widget ) {
					if ( $widget->GetSafeName() == $widget_name ) {
						return $widget;
					}
				}
			}
		}
		return null;
	}

	/**
	 * Find a filter given a supported prefix.
	 *
	 * @param string $prefix Filter prefix.
	 * @return WSAL_AS_Filters_AbstractFilter|null
	 */
	public function FindFilterByPrefix( $prefix ) {
		foreach ( $this->filters as $filter ) {
			if ( in_array( $prefix, $filter->GetPrefixes() ) ) {
				return $filter;
			}
		}
		return null;
	}
}
