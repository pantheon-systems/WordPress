<?php
/**
 * Helper class which defnes a namespace for some commonly used functions
 * 
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 */
class PMXI_Helper {
	const GLOB_MARK = 1;
	const GLOB_NOSORT = 2;
	const GLOB_ONLYDIR = 4;
	
	const GLOB_NODIR = 256;
	const GLOB_PATH = 512;
	const GLOB_NODOTS = 1024;
	const GLOB_RECURSE = 2048;

	const FNM_PATHNAME = 1;
	const FNM_NOESCAPE = 2;
	const FNM_PERIOD = 4;
	const FNM_CASEFOLD = 16;

	/**
	 * A safe empowered glob().
	 *
	 * Function glob() is prohibited on some server (probably in safe mode)
	 * (Message "Warning: glob() has been disabled for security reasons in
	 * (script) on line (line)") for security reasons as stated on:
	 * http://seclists.org/fulldisclosure/2005/Sep/0001.html
	 *
	 * safe_glob() intends to replace glob() using readdir() & fnmatch() instead.
	 * Supported flags: self::GLOB_MARK, self::GLOB_NOSORT, self::GLOB_ONLYDIR
	 * Additional flags: self::GLOB_NODIR, self::GLOB_PATH, self::GLOB_NODOTS, self::GLOB_RECURSE
	 * (not original glob() flags)
	 * @author BigueNique AT yahoo DOT ca
	 * @updates
	 * - 080324 Added support for additional flags: self::GLOB_NODIR, self::GLOB_PATH,
	 *   self::GLOB_NODOTS, self::GLOB_RECURSE
	 * - 100607 Recurse is_dir check fixed by Pavel Kulbakin <p.kulbakin@gmail.com>
	 */
	public static function safe_glob($pattern,  $flags=0) {
		$split = explode('/', str_replace('\\', '/', $pattern));
		$mask = array_pop($split);
		$path = implode('/', $split);			

		if (($dir = @opendir($path . '/')) !== false or ($dir = @opendir($path)) !== false) {
			$glob = array();
			while(($file = readdir($dir)) !== false) {
				// Recurse subdirectories (self::GLOB_RECURSE)
				if (($flags & self::GLOB_RECURSE) && is_dir($path . '/' . $file) && ( ! in_array($file, array('.', '..')))) {
					$glob = array_merge($glob, self::array_prepend(self::safe_glob($path . '/' . $file . '/' . $mask, $flags), ($flags & self::GLOB_PATH ? '' : $file . '/')));
				}
				// Match file mask				
				if (self::fnmatch($mask, $file, self::FNM_CASEFOLD)) {					
					if ((( ! ($flags & self::GLOB_ONLYDIR)) || is_dir("$path/$file"))
						&& (( ! ($flags & self::GLOB_NODIR)) || ( ! is_dir($path . '/' . $file)))
						&& (( ! ($flags & self::GLOB_NODOTS)) || ( ! in_array($file, array('.', '..'))))
					) {
						$glob[] = ($flags & self::GLOB_PATH ? $path . '/' : '') . $file . ($flags & self::GLOB_MARK ? '/' : '');
					}
				}				
			}
			closedir($dir);
			if ( ! ($flags & self::GLOB_NOSORT)) sort($glob);			
			return $glob;
		} else {
			return (strpos($pattern, "*") === false) ? array($pattern) : false;
		}
	}
	
	/**
	 * Prepends $string to each element of $array
	 * If $deep is true, will indeed also apply to sub-arrays
	 * @author BigueNique AT yahoo DOT ca
	 * @since 080324
	 */
	public static function array_prepend($array, $string, $deep=false) {
		if(empty($array)||empty($string)) {
			return $array;
		}
		foreach ($array as $key => $element) {
			if (is_array($element)) {
				if ($deep) {
					$array[$key] = self::array_prepend($element,$string,$deep);
				} else {
					trigger_error(__METHOD__ . ': array element', E_USER_WARNING);
				}
			} else {
				$array[$key] = $string.$element;
			}
		}
		return $array;

	}

	/**
	 * non-POSIX complient remplacement for the fnmatch
	 */
	public static function fnmatch($pattern, $string, $flags = 0) {
		
		$modifiers = null;
		$transforms = array(
			'\*'    => '.*',
			'\?'    => '.',
			'\[\!'  => '[^',
			'\['    => '[',
			'\]'    => ']',
			'\.'    => '\.',
			'\\'    => '\\\\',
			'\-'    => '-',
		);
		 
		// Forward slash in string must be in pattern:
		if ($flags & self::FNM_PATHNAME) {
			$transforms['\*'] = '[^/]*';
		}
		 
		// Back slash should not be escaped:
		if ($flags & self::FNM_NOESCAPE) {
			unset($transforms['\\']);
		}
		 
		// Perform case insensitive match:
		if ($flags & self::FNM_CASEFOLD) {
			$modifiers .= 'i';
		}
		 
		// Period at start must be the same as pattern:
		if ($flags & self::FNM_PERIOD) {
			if (strpos($string, '.') === 0 && strpos($pattern, '.') !== 0) return false;
		}
		 
		$pattern = '#^'
			.strtr(preg_quote($pattern, '#'), $transforms)
			.'$#'
			.$modifiers;
		
		return (boolean)preg_match($pattern, $string);
	}
}
