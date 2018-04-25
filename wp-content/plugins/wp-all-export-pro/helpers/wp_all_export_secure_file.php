<?php
if ( ! function_exists('wp_all_export_secure_file') ){

	function wp_all_export_secure_file( $targetDir, $ID = false){

		$is_secure_import = PMXE_Plugin::getInstance()->getOption('secure');

		if ( $is_secure_import ){			

			$dir = $targetDir . DIRECTORY_SEPARATOR . ( ( $ID ) ? md5( $ID . NONCE_SALT ) : md5( time() . NONCE_SALT ) );							

			if ( ! is_dir($dir) ) @mkdir($dir, 0755);

			if (@is_writable($dir) and @is_dir($dir)){
				$targetDir = $dir;	
				@touch( $dir . DIRECTORY_SEPARATOR . 'index.php' );
			}
			
		}

		return $targetDir;
	}
}	