<?php

class WPML_ST_Scan_Dir {

	/**
	 * @param string $folder
	 * @param array $extensions
	 * @param bool $single_file
	 * @param array $ignore_folders
	 *
	 * @return array
	 */
	public function scan( $folder, array $extensions = array(), $single_file = false, $ignore_folders = array() ) {

		$files = array();
		$scanned_files = array();

		if ( is_dir( $folder ) ) {
			$scanned_files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $folder ) );
		} elseif ( $single_file ) {
			$scanned_files = array( new SplFileInfo( $folder ) );
		}

		foreach ( $scanned_files as $file ) {
			$ignore_file = false;

			if ( in_array( $file->getExtension(), $extensions, true ) ) {

				foreach( $ignore_folders as $ignore_folder ) {
					if ( false !== strpos( $file->getPathname(), $ignore_folder ) ) {
						$ignore_file = true;
					}
				}

				if ( $ignore_file ) {
					continue;
				}

				$files[] = $file->getPathname();
			}
		}

		return $files;
	}
}