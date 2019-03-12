<?php
abstract class ameMenu {
	const format_name = 'Admin Menu Editor menu';
	const format_version = '7.0';

	/**
	 * Load an admin menu from a JSON string.
	 *
	 * @static
	 *
	 * @param string $json A JSON-encoded menu structure.
	 * @param bool $assume_correct_format Skip the format header check and assume everything is fine. Defaults to false.
	 * @param bool $always_normalize Always normalize the menu structure, even if format[is_normalized] is true.
	 * @throws InvalidMenuException
	 * @return array
	 */
	public static function load_json($json, $assume_correct_format = false, $always_normalize = false) {
		$arr = json_decode($json, true);
		if ( !is_array($arr) ) {
			throw new InvalidMenuException('The input is not a valid JSON-encoded admin menu.');
		}
		return self::load_array($arr, $assume_correct_format, $always_normalize);
	}

	/**
	 * Load an admin menu structure from an associative array.
	 *
	 * @static
	 *
	 * @param array $arr
	 * @param bool $assume_correct_format
	 * @param bool $always_normalize
	 * @throws InvalidMenuException
	 * @return array
	 */
	public static function load_array($arr, $assume_correct_format = false, $always_normalize = false){
		$is_normalized = false;
		if ( !$assume_correct_format ) {
			if ( isset($arr['format']) && ($arr['format']['name'] == self::format_name) ) {
				$compared = version_compare($arr['format']['version'], self::format_version);
				if ( $compared > 0 ) {
					throw new InvalidMenuException(sprintf(
						"Can't load a menu created by a newer version of the plugin. Menu format: '%s', newest supported format: '%s'.",
						$arr['format']['version'],
						self::format_version
					));
				}
				//We can skip normalization if the version number matches exactly and the menu is already normalized.
				if ( ($compared === 0) && isset($arr['format']['is_normalized']) ) {
					$is_normalized = $arr['format']['is_normalized'];
				}
			} else {
				return self::load_menu_40($arr);
			}
		}

		if ( !(isset($arr['tree']) && is_array($arr['tree'])) ) {
			throw new InvalidMenuException("Failed to load a menu - the menu tree is missing.");
		}

		if ( isset($arr['format']) && !empty($arr['format']['compressed']) ) {
			$arr = self::decompress($arr);
		}

		$menu = array('tree' => array());
		$menu = self::add_format_header($menu);

		if ( $is_normalized && !$always_normalize ) {
			$menu['tree'] = $arr['tree'];
		} else {
			foreach($arr['tree'] as $file => $item) {
				$menu['tree'][$file] = ameMenuItem::normalize($item);
			}
			$menu['format']['is_normalized'] = true;
		}

		if ( isset($arr['color_css']) && is_string($arr['color_css']) ) {
			$menu['color_css'] = $arr['color_css'];
			$menu['color_css_modified'] = isset($arr['color_css_modified']) ? intval($arr['color_css_modified']) : 0;
		}

		//Sanitize color presets.
		if ( isset($arr['color_presets']) && is_array($arr['color_presets']) ) {
			$color_presets = array();

			foreach($arr['color_presets'] as $name => $preset) {
				$name = substr(trim(strip_tags(strval($name))), 0, 250);
				if ( empty($name) || !is_array($preset) ) {
					continue;
				}

				//Each color must be a hexadecimal HTML color code. For example: "#12456"
				$is_valid_preset = true;
				foreach($preset as $property => $color) {
					//Note: It would good to check $property against a list of known color names.
					if ( !is_string($property) || !is_string($color) || !preg_match('/^\#[0-9a-f]{6}$/i', $color) ) {
						$is_valid_preset = false;
						break;
					}
				}

				if ( $is_valid_preset ) {
					$color_presets[$name] = $preset;
				}
			}

			$menu['color_presets'] = $color_presets;
		}

		//Copy directly granted capabilities.
		if ( isset($arr['granted_capabilities']) && is_array($arr['granted_capabilities']) ) {
			$granted_capabilities = array();
			foreach($arr['granted_capabilities'] as $actor => $capabilities) {
				//Skip empty lists to avoid problems with {} => [] and to save space.
				if ( !empty($capabilities) ) {
					$granted_capabilities[strval($actor)] = $capabilities;
				}
			}
			if (!empty($granted_capabilities)) {
				$menu['granted_capabilities'] = $granted_capabilities;
			}
		}

		//Copy component visibility.
		if ( isset($arr['component_visibility']) ) {
			$visibility = array();

			foreach(array('toolbar', 'adminMenu') as $component) {
				if (
					isset($arr['component_visibility'][$component])
					&& is_array($arr['component_visibility'][$component])
					&& !empty($arr['component_visibility'][$component])
				) {
					//Expected: actorId => boolean.
					$visibility[$component] = array();
					foreach($arr['component_visibility'][$component] as $actorId => $allow) {
						$visibility[$component][strval($actorId)] = (bool)($allow);
					}
				}
			}

			$menu['component_visibility'] = $visibility;
		}

		//Copy the "modified icons" flag.
		if ( isset($arr['has_modified_dashicons']) ) {
			$menu['has_modified_dashicons'] = (bool)$arr['has_modified_dashicons'];
		}

		//Copy the pre-generated list of virtual capabilities.
		if ( isset($arr['prebuilt_virtual_caps']) ) {
			$menu['prebuilt_virtual_caps'] = $arr['prebuilt_virtual_caps'];
		}

		return $menu;
	}

	/**
	 * "Pre-load" an old menu structure.
	 *
	 * In older versions of the plugin, the entire menu consisted of
	 * just the menu tree and nothing else. This was internally known as
	 * menu format "4".
	 *
	 * To improve portability and forward-compatibility, newer versions
	 * use a simple dictionary-based container instead, with the menu tree
	 * being one of the possible entries.
	 *
	 * @static
	 * @param array $arr
	 * @return array
	 */
	private static function load_menu_40($arr) {
		//This is *very* basic and might need to be improved.
		$menu = array('tree' => $arr);
		return self::load_array($menu, true);
	}

	private static function add_format_header($menu) {
		if ( !isset($menu['format']) || !is_array($menu['format']) ) {
			$menu['format'] = array();
		}
		$menu['format'] = array_merge(
			$menu['format'],
			array(
				'name' => self::format_name,
				'version' => self::format_version,
			)
		);
		return $menu;
	}

	/**
	 * Serialize an admin menu as JSON.
	 *
	 * @static
	 * @param array $menu
	 * @return string
	 */
	public static function to_json($menu) {
		$menu = self::add_format_header($menu);
		return json_encode($menu);
	}

  /**
   * Sort the menus and menu items of a given menu according to their positions
   *
   * @param array $tree A menu structure in the internal format (just the tree).
   * @return array Sorted menu in the internal format
   */
	public static function sort_menu_tree($tree){
		//Resort the tree to ensure the found items are in the right spots
		uasort($tree, 'ameMenuItem::compare_position');
		//Resort all submenus as well
		foreach ($tree as &$topmenu){
			if (!empty($topmenu['items'])){
				usort($topmenu['items'], 'ameMenuItem::compare_position');
			}
		}

		return $tree;
	}

	/**
	 * Convert the WP menu structure to the internal representation. All properties set as defaults.
	 *
	 * @param array $menu
	 * @param array $submenu
	 * @param array $blacklist
	 * @return array Menu in the internal tree format.
	 */
	public static function wp2tree($menu, $submenu, $blacklist = array()){
		$tree = array();
		foreach ($menu as $pos => $item){

			$tree_item = ameMenuItem::blank_menu();
			$tree_item['defaults'] = ameMenuItem::fromWpItem($item, $pos);
			$tree_item['separator'] = $tree_item['defaults']['separator'];

			//Attach sub-menu items
			$parent = $tree_item['defaults']['file'];
			if ( isset($submenu[$parent]) ){
				foreach($submenu[$parent] as $position => $subitem){
					$defaults = ameMenuItem::fromWpItem($subitem, $position, $parent);

					//Skip blacklisted items.
					if ( isset($defaults['url'], $blacklist[$defaults['url']]) ) {
						continue;
					}

					$tree_item['items'][] = array_merge(
						ameMenuItem::blank_menu(),
						array('defaults' => $defaults)
					);
				}
			}

			//Skip blacklisted top level menus (only if they have no submenus).
			if (
				empty($tree_item['items'])
				&& isset($tree_item['defaults']['url'], $blacklist[$tree_item['defaults']['url']])
			) {
				continue;
			}

			$tree[$parent] = $tree_item;
		}

		$tree = self::sort_menu_tree($tree);

		return $tree;
	}

	/**
	 * Check if a menu contains any items with the "hidden" flag set to true.
	 *
	 * @param array $menu
	 * @return bool
	 */
	public static function has_hidden_items($menu) {
		if ( !is_array($menu) || empty($menu) || empty($menu['tree']) ) {
			return false;
		}

		foreach($menu['tree'] as $item) {
			if ( ameMenuItem::get($item, 'hidden') ) {
				return true;
			}
			if ( !empty($item['items']) ) {
				foreach($item['items'] as $child) {
					if ( ameMenuItem::get($child, 'hidden') ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Sanitize a list of menu items. Array indexes will be preserved.
	 *
	 * @param array $treeItems A list of menu items.
	 * @param bool $unfiltered_html Whether the current user has the unfiltered_html capability.
	 * @return array List of sanitized items.
	 */
	public static function sanitize($treeItems, $unfiltered_html = null) {
		if ( $unfiltered_html === null ) {
			$unfiltered_html = current_user_can('unfiltered_html');
		}

		$result = array();
		foreach($treeItems as $key => $item) {
			$item = ameMenuItem::sanitize($item, $unfiltered_html);

			if ( !empty($item['items']) ) {
				$item['items'] = self::sanitize($item['items'], $unfiltered_html);
			}
			$result[$key] = $item;
		}

		return $result;
	}

	/**
	 * Recursively filter a list of menu items and remove items flagged as missing.
	 *
	 * @param array $items An array of menu items to filter.
	 * @return array
	 */
	public static function remove_missing_items($items) {
		$items = array_filter($items, array(__CLASS__, 'is_not_missing'));

		foreach($items as &$item) {
			if ( !empty($item['items']) ) {
				$item['items'] = self::remove_missing_items($item['items']);
			}
		}

		return $items;
	}

	protected static function is_not_missing($item) {
		return empty($item['missing']);
	}

	/**
	 * Compress menu configuration (lossless).
	 *
	 * Reduces data size by storing commonly used properties and defaults in one place
	 * instead of in every menu item.
	 *
	 * @param array $menu
	 * @return array
	 */
	public static function compress($menu) {
		$property_dict = ameMenuItem::blank_menu();
		unset($property_dict['defaults']);

		$common = array(
			'properties' => $property_dict,
			'basic_defaults' => ameMenuItem::basic_defaults(),
			'custom_item_defaults' => ameMenuItem::custom_item_defaults(),
		);

		$menu['tree'] = self::map_items(
			$menu['tree'],
			array(__CLASS__, 'compress_item'),
			array($common)
		);

		$menu = self::add_format_header($menu);
		$menu['format']['compressed'] = true;
		$menu['format']['common'] = $common;

		return $menu;
	}

	protected static function compress_item($item, $common) {
		//These empty arrays can be dropped. They'll be restored either by merging common properties,
		//or by ameMenuItem::normalize().
		if ( empty($item['grant_access']) ) {
			unset($item['grant_access']);
		}
		if ( empty($item['items']) ) {
			unset($item['items']);
		}

		//Normal and custom menu items have different defaults.
		//Remove defaults that are the same for all items of that type.
		$defaults = !empty($item['custom']) ? $common['custom_item_defaults'] : $common['basic_defaults'];
		if ( isset($item['defaults']) ) {
			foreach($defaults as $key => $value) {
				if ( array_key_exists($key, $item['defaults']) && $item['defaults'][$key] === $value ) {
					unset($item['defaults'][$key]);
				}
			}
		}

		//Remove properties that match the common values.
		foreach($common['properties'] as $key => $value) {
			if ( array_key_exists($key, $item) && $item[$key] === $value ) {
				unset($item[$key]);
			}
		}

		return $item;
	}

	/**
	 * Decompress menu configuration that was previously compressed by ameMenu::compress().
	 *
	 * If the input $menu is not compressed, this method will return it unchanged.
	 *
	 * @param array $menu
	 * @return array
	 */
	public static function decompress($menu) {
		if ( !isset($menu['format']) || empty($menu['format']['compressed']) ) {
			return $menu;
		}

		$common = $menu['format']['common'];
		$menu['tree'] = self::decompress_list($menu['tree'], $common);

		unset($menu['format']['compressed'], $menu['format']['common']);
		return $menu;
	}

	protected static function decompress_list($list, $common) {
		//Optimization: Direct iteration is about 40% faster than map_items.
		$result = array();
		foreach($list as $key => $item) {
			$item = self::decompress_item($item, $common);
			if ( !empty($item['items']) ) {
				$item['items'] = self::decompress_list($item['items'], $common);
			}
			$result[$key] = $item;
		}
		return $result;
	}

	protected static function decompress_item($item, $common) {
		$item = array_merge($common['properties'], $item);

		$defaults = !empty($item['custom']) ? $common['custom_item_defaults'] : $common['basic_defaults'];
		$item['defaults'] = array_merge($defaults, $item['defaults']);

		return $item;
	}

	/**
	 * Recursively apply a callback to every menu item in an array and return the results.
	 * Array keys are preserved.
	 *
	 * @param array $items
	 * @param callable $callback
	 * @param array|null $extra_params Optional. An array of additional parameters to pass to the callback.
	 * @return array
	 */
	protected static function map_items($items, $callback, $extra_params = null) {
		if ( $extra_params === null ) {
			$extra_params = array();
		}
		$args = array_merge(array(null), $extra_params);

		$result = array();
		foreach($items as $key => $item) {
			$args[0] = $item;
			$item = call_user_func_array($callback, $args);

			if ( !empty($item['items']) ) {
				$item['items'] = self::map_items($item['items'], $callback, $extra_params);
			}
			$result[$key] = $item;
		}
		return $result;
	}

	/**
	 * @param array $items
	 * @param callable $callback
	 */
	public static function for_each($items, $callback) {
		foreach($items as $key => $item) {
			call_user_func($callback, $item);
			if ( !empty($item['items']) ) {
				self::for_each($item['items'], $callback);
			}
		}
	}
}

class ameGrantedCapabilityFilter {
	private $post_types = array();
	private $taxonomies = array();

	public function __construct() {
		$this->post_types = get_post_types(array('public' => true, 'show_ui' => true), 'names', 'or');
		$this->taxonomies = get_taxonomies(array('public' => true, 'show_ui' => true), 'names', 'or');
	}

	/**
	 * Remove capabilities that refer to unregistered post types or taxonomies.
	 *
	 * @param array $granted_capabilities
	 * @return array
	 */
	public function clean_up($granted_capabilities) {
		$clean = array();
		foreach($granted_capabilities as $actor => $capabilities) {
			$clean[$actor] = array_filter($capabilities, array($this, 'is_registered_source'));
		}
		return $clean;
	}

	private function is_registered_source($grant) {
		if ( !is_array($grant) || !isset($grant[1]) ) {
			return true;
		}

		if ( isset($grant[2]) ) {
			if ( $grant[1] === 'post_type' ) {
				return array_key_exists($grant[2], $this->post_types);
			} else if ( $grant[1] === 'taxonomy' ) {
				return array_key_exists($grant[2], $this->taxonomies);
			}
		}
		return false;
	}
}

/**
 * This could just be a closure, but we want to support PHP 5.2.
 */
class ameModifiedIconDetector {
	private $result = false;

	public static function detect($menu) {
		$detector = new self();
		ameMenu::for_each($menu['tree'], array($detector, 'checkItem'));
		return $detector->getResult();
	}

	public function checkItem($item) {
		$this->result = $this->result || $this->hasModifiedDashicon($item);
	}

	private function hasModifiedDashicon($item) {
		return !ameMenuItem::is_default($item, 'icon_url')
			&& (strpos(ameMenuItem::get($item, 'icon_url'), 'dashicons-') === 0);
	}

	private function getResult() {
		return $this->result;
	}
}


class InvalidMenuException extends Exception {}

class ameInvalidJsonException extends RuntimeException {};