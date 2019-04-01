<?php

/**
 * Miscellaneous utility functions.
 */
class ameUtils {

	/**
	 * Get a value from a nested array or object based on a path.
	 *
	 * @param array|object $array Get an entry from this array.
	 * @param array|string $path A list of array keys in hierarchy order, or a string path like "foo.bar.baz".
	 * @param mixed $default The value to return if the specified path is not found.
	 * @param string $separator Path element separator. Only applies to string paths.
	 * @return mixed
	 */
	public static function get($array, $path, $default = null, $separator = '.') {
		if (is_string($path)) {
			$path = explode($separator, $path);
		}
		if (empty($path)) {
			return $default;
		}

		//Follow the $path into $input as far as possible.
		$currentValue = $array;
		$pathExists = true;
		foreach($path as $node) {
			if (is_array($currentValue) && array_key_exists($node, $currentValue)) {
				$currentValue = $currentValue[$node];
			} else if (is_object($currentValue) && property_exists($currentValue, $node)) {
				$currentValue = $currentValue->$node;
			} else {
				$pathExists = false;
				break;
			}
		}

		if ($pathExists) {
			return $currentValue;
		}
		return $default;
	}

	/**
	 * Get the first non-root directory from a path.
	 *
	 * Examples:
	 *  "foo/bar"          => "foo"
	 *  "/foo/bar/baz.txt" => "foo"
	 *  "bar"              => null
	 *  "baz/"             => "baz"
	 *  "/"                => null
	 *
	 * @param string $fileName
	 * @return string|null
	 */
	public static function getFirstDirectory($fileName) {
		$fileName = ltrim($fileName, '/');

		$segments = explode('/', $fileName, 2);
		if ((count($segments) > 1) && ($segments[0] !== '')) {
			return $segments[0];
		}
		return null;
	}
}