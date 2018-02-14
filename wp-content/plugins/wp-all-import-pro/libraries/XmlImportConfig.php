<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 * @package General
 */

/**
 * This class is used for different XmlImport settings
 */
if (!class_exists('XmlImportConfig'))
{
	final class XmlImportConfig
	{
		/**
		 * Singleton instance
		 * @var XmlImportConfig
		 */
		private static $instance = null;
		/**
		 * Path to cache directory
		 * @var string
		 */
		private $cache_dir;
		/**
		 * String to use when concatenating result of xpath corresponding to multiple elements
		 * @var string
		 */
		private $multi_glue;

		/**
		 * Initial settings
		 */
		private function init()
		{
			$this->setCacheDirectory(dirname(__FILE__) . '/cache');
			$this->setMultiGlue( apply_filters('wp_all_import_multi_glue', ', ') );
		}

		/**
		 * Gets instance of a singleton class
		 * @return XmlImportConfig
		 */
		public static function getInstance()
		{
			//if (is_null(self::$instance)) {
				self::$instance = new self;
				self::$instance->init();
			//}
			return self::$instance;
		}

		/**
		 * Returns path to cache directory
		 * @return string
		 */
		public function getCacheDirectory()
		{
			return $this->cache_dir;
		}

		/**
		 * Sets path to cache directory
		 * @param string $cacheDirectoryPath
		 */
		public function setCacheDirectory($cacheDirectoryPath)
		{
			$this->cache_dir = $cacheDirectoryPath;
		}
		
		/**
		 * Returns string glue to use when concatenating multiple elements
		 * @return string
		 */
		public function getMultiGlue()
		{
			return $this->multi_glue;
		}
		/**
		 * Sets string glue to use when concatenating multiple element
		 * @param unknown_type $glue
		 */
		public function setMultiGlue($glue)
		{
			$this->multi_glue = $glue;
		}
	}
}