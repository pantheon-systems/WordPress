<?php

namespace Qiigo\Plugin\Integration {
	class Autoloader {
		private static $_inst = null;
		public static function Register($ns, $libPath) {
			if( !isset(static::$_inst) )
				static::$_inst = new Autoloader();
			
			static::$_inst->RegisterNS($ns, $libPath);
		}
		
		private $paths;
		public function __construct() {
			$this->paths = array();
			
			\spl_autoload_register(array($this,'Autoload'), true, true);
		}
		
		public function RegisterNS($ns, $libPath) {
			if( substr($ns, strlen($ns)-1) != '\\' )
				$ns .= '\\';
			
			if( substr($ns, 0, 1) == '\\' )
				$ns = substr($ns,1);
			
			if( substr($libPath, strlen($libPath)-1) != DS )
				$libPath .= DS;
			
			$this->paths[$ns] = $libPath;
		}
		
		public function Autoload($class_name) {
			
			foreach($this->paths as $ns => $libPath) {
				if( strlen($class_name) > strlen($ns) && substr($class_name, 0, strlen($ns)) == $ns ) {
					$cn = substr($class_name, strlen($ns));
					
					if( '\\' != DS )
						$cn = str_replace('\\', DS, $cn);
					
					$p = $libPath.$cn.".php";
					
					if( !is_file($p) )
						continue;
					
					require($p);
					break;
				}
			}
		}
	}
}