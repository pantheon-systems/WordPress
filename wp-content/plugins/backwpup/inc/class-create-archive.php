<?php
/**
 * Class for creating File Archives
 */
class BackWPup_Create_Archive {

	/**
	 * Achieve file with full path
	 *
	 * @var string
	 */
	private $file = '';

	/**
	 * Compression method
	 *
	 * @var string Method off compression Methods are ZipArchive, PclZip, Tar, TarGz, TarBz2, gz, bz2
	 */
	private $method = '';

	/**
	 * Open handel for files.
	 */
	private $filehandel = NULL;

	/**
	 * class handel for ZipArchive.
	 *
	 * @var ZipArchive
	 */
	private $ziparchive = NULL;

	/**
	 * class handel for PclZip.
	 *
	 * @var PclZip
	 */
	private $pclzip = NULL;

	/**
	 * class handel for PclZip.
	 *
	 * @var array()
	 */
	private $pclzip_file_list = array();

	/**
	 * File cont off added files to handel somethings that depends on it
	 *
	 * @var int number of files added
	 */
	private $file_count = 0;

	/**
	 * Set archive Parameter
	 *
	 * @param $file string File with full path of the archive
	 * @throws BackWPup_Create_Archive_Exception
	 */
	public function __construct( $file ) {

		//check param
		if ( empty( $file ) ) {
			throw new BackWPup_Create_Archive_Exception(  __( 'The file name of an archive cannot be empty.', 'backwpup' ) );
		}

		//set file
		$this->file = trim( $file );

		//check folder can used
		if ( ! is_dir( dirname( $this->file ) ) ||! is_writable( dirname( $this->file ) ) ) {
			throw new BackWPup_Create_Archive_Exception( sprintf( _x( 'Folder %s for archive not found','%s = Folder name', 'backwpup' ), dirname( $this->file ) ) );
		}

		//set and check method and get open handle
		if ( strtolower( substr( $this->file, -7 ) ) == '.tar.gz' ) {
			if ( ! function_exists( 'gzencode' ) ) {
				throw new BackWPup_Create_Archive_Exception( __( 'Functions for gz compression not available', 'backwpup' ) );
			}
			$this->method = 'TarGz';
			$this->filehandel = fopen( $this->file, 'ab' );
		}
		elseif ( strtolower( substr( $this->file, -8 ) ) == '.tar.bz2' ) {
			if ( ! function_exists( 'bzcompress' ) ) {
				throw new BackWPup_Create_Archive_Exception( __( 'Functions for bz2 compression not available', 'backwpup' ) );
			}
			$this->method = 'TarBz2';
			$this->filehandel = fopen( $this->file, 'ab' );
		}
		elseif ( strtolower( substr( $this->file, -4 ) ) == '.tar' ) {
			$this->method = 'Tar';
			$this->filehandel = fopen( $this->file, 'ab');
		}
		elseif ( strtolower( substr( $this->file, -4 ) ) == '.zip' ) {
			$this->method = 'ZipArchive';
			//check and set method
			if ( ! class_exists( 'ZipArchive' ) ) {
				$this->method = 'PclZip';
			}
			//open classes
			if ( $this->get_method() == 'ZipArchive' ) {
				$this->ziparchive = new ZipArchive();
				$ziparchive_open = $this->ziparchive->open( $this->file, ZipArchive::CREATE );
				if ( $ziparchive_open !== TRUE ) {
					$this->ziparchive_status();
					throw new BackWPup_Create_Archive_Exception( sprintf( _x( 'Cannot create zip archive: %d','ZipArchive open() result', 'backwpup' ), $ziparchive_open ) );
				}
			}
			if ( $this->get_method() == 'PclZip' && ! function_exists( 'gzencode' ) ) {
				throw new BackWPup_Create_Archive_Exception( __( 'Functions for gz compression not available', 'backwpup' ) );
			}
			if( $this->get_method() == 'PclZip' ) {
				$this->method = 'PclZip';
				if ( ! defined('PCLZIP_TEMPORARY_DIR') ) {
					define( 'PCLZIP_TEMPORARY_DIR', BackWPup::get_plugin_data( 'TEMP' ) );
				}
				require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
				$this->pclzip = new PclZip( $this->file );
			}
		}
		elseif ( strtolower( substr( $this->file, -3 ) ) == '.gz' ) {
			if ( ! function_exists( 'gzencode' ) )
				throw new BackWPup_Create_Archive_Exception( __( 'Functions for gz compression not available', 'backwpup' ) );
			$this->method = 'gz';
			$this->filehandel = fopen( 'compress.zlib://' . $this->file, 'wb');
		}
		elseif ( strtolower( substr( $this->file, -4 ) ) == '.bz2' ) {
			if ( ! function_exists( 'bzcompress' ) ) {
				throw new BackWPup_Create_Archive_Exception( __( 'Functions for bz2 compression not available', 'backwpup' ) );
			}
			$this->method = 'bz2';
			$this->filehandel = fopen( 'compress.bzip2://' . $this->file, 'w');
		}
		else {
			throw new BackWPup_Create_Archive_Exception( sprintf( _x( 'Method to archive file %s not detected','%s = file name', 'backwpup' ), basename( $this->file ) ) );
		}

		//check file handle
		if ( isset( $this->filehandel ) && ! $this->filehandel ) {
			throw new BackWPup_Create_Archive_Exception( __( 'Cannot open archive file', 'backwpup' ) );
		}

	}


	/**
	 * Closes open archive on shutdown.
	 */
	public function __destruct() {

		//close PclZip Class
		if ( is_object( $this->pclzip ) ) {
			if ( count( $this->pclzip_file_list ) > 0 ) {
				if ( 0 == $this->pclzip->add( $this->pclzip_file_list ) ) {
					trigger_error( sprintf( __( 'PclZip archive add error: %s', 'backwpup' ), $this->pclzip->errorInfo( TRUE ) ), E_USER_ERROR );
				}
			}
			unset( $this->pclzip );
		}

		//close ZipArchive Class
		if ( is_object( $this->ziparchive ) ) {
			if ( ! $this->ziparchive->close() ) {
				$this->ziparchive_status();
				trigger_error( __( 'ZIP archive cannot be closed correctly.', 'backwpup' ), E_USER_ERROR );
				sleep( 1 );
			}
			$this->ziparchive = NULL;
		}

		//close file if open
		if ( is_resource( $this->filehandel ) ) {
			fclose( $this->filehandel );
		}
	}

	/*
	 * Closing the archive
	 */
	public function close() {

		//write tar file end
		if ( in_array( $this->get_method(), array( 'Tar', 'TarGz', 'TarBz2' ), true ) ) {
			$footer = pack( "a1024", "" );
			if ( $this->method === 'TarGz' ) {
				$footer = gzencode( $footer );
			}
			if ( $this->method === 'TarBz2' ) {
				$footer = bzcompress( $footer );
			}
			fwrite( $this->filehandel, $footer );
		}
	}

	/**
	 * Get method that the archive uses
	 *
	 * @return string of compression method
	 */
	public function get_method() {

		return $this->method;
	}


	/**
	 * Adds a file to Archive
	 *
	 * @param $file_name       string
	 * @param $name_in_archive string
	 * @return bool Add worked or not
	 */
	public function add_file( $file_name, $name_in_archive = '' ) {

		$file_name = trim( $file_name );

	    //check param
		if ( empty( $file_name ) ) {
			trigger_error( __( 'File name cannot be empty', 'backwpup' ), E_USER_WARNING );
			return FALSE;
		}

		if ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
			clearstatcache( TRUE, $file_name );
		}

		if ( ! is_readable( $file_name ) ) {
			trigger_error( sprintf( _x( 'File %s does not exist or is not readable', 'File to add to archive', 'backwpup' ), $file_name ), E_USER_WARNING );
			return TRUE;
		}

		if ( empty( $name_in_archive ) ) {
			$name_in_archive = $file_name;
		}

		//remove reserved chars
		$name_in_archive = str_replace( array( "?", "<", ">", ":", "%","\"", "*", "|", chr(0) ) , '', $name_in_archive );

		switch ( $this->get_method() ) {
			case 'gz':
				if ( $this->file_count > 0 ) {
					trigger_error( __( 'This archive method can only add one file', 'backwpup' ), E_USER_WARNING );
					return FALSE;
				}
				//add file to archive
				if ( ! ( $fd = fopen( $file_name, 'rb' ) ) ) {
					trigger_error( sprintf( __( 'Cannot open source file %s to archive', 'backwpup' ), $file_name ), E_USER_WARNING );
					return FALSE;
				}
				while ( ! feof( $fd ) ) {
					fwrite( $this->filehandel, fread( $fd, 8192 ) );
				}
				fclose( $fd );
				$this->file_count++;
				break;
			case 'bz':
				if ( $this->file_count > 0 ) {
					trigger_error( __( 'This archive method can only add one file', 'backwpup' ), E_USER_WARNING );
					return FALSE;
				}
				//add file to archive
				if ( ! ( $fd = fopen( $file_name, 'rb' ) ) ) {
					trigger_error( sprintf( __( 'Cannot open source file %s to archive', 'backwpup' ), $file_name ), E_USER_WARNING );
					return FALSE;
				}
				while ( ! feof( $fd ) ) {
					fwrite( $this->filehandel, bzcompress( fread( $fd, 8192 ) ) );
				}
				fclose( $fd );
				$this->file_count++;
				break;
			case 'Tar':
			case 'TarGz':
			case 'TarBz2':
				//convert chars for archives file names
				if ( function_exists( 'iconv' ) && stristr( PHP_OS, 'win' ) !== false ) {
					$test = @iconv( 'ISO-8859-1', 'UTF-8', $name_in_archive );
					if ( $test ) {
						$name_in_archive = $test;
					}
				}
				return $this->tar_file( $file_name, $name_in_archive );
				break;
			case 'ZipArchive':
				//convert chars for archives file names
				if ( function_exists( 'iconv' ) && stristr( PHP_OS, 'win' ) === false ) {
					$test = @iconv( 'UTF-8', 'CP437', $name_in_archive );
					if ( $test ) {
						$name_in_archive = $test;
					}
				}
				$file_size = filesize( $file_name );
				if ( $file_size === FALSE ) {
					return FALSE;
				}
				//check if entry already in archive and delete it if it not in full size
				if ( $zip_file_stat = $this->ziparchive->statName( $name_in_archive ) ) {
					if ( $zip_file_stat[ 'size' ] != $file_size ) {
						$this->ziparchive->deleteName( $name_in_archive );
						//reopen on deletion
						$this->file_count = 21;
					} else {
						//file already complete in archive
						return TRUE;
					}
				}
				//close and reopen, all added files are open on fs
				if ( $this->file_count > 20 ) { //35 works with PHP 5.2.4 on win
					if ( ! $this->ziparchive->close() ) {
						$this->ziparchive_status();
						trigger_error(__( 'ZIP archive cannot be closed correctly', 'backwpup'	), E_USER_ERROR	);
						sleep( 1 );
					}
					$this->ziparchive = NULL;
					if ( ! $this->check_archive_filesize() ) {
						return FALSE;
					}
					$this->ziparchive = new ZipArchive();
					$ziparchive_open = $this->ziparchive->open( $this->file, ZipArchive::CREATE );
					if ( $ziparchive_open !== TRUE ) {
						$this->ziparchive_status();
						return FALSE;
					}
					$this->file_count = 0;
				}
				if ( $file_size < ( 1024 * 1024 * 2 ) ) {
					if ( ! $this->ziparchive->addFromString( $name_in_archive, file_get_contents( $file_name ) ) ) {
						$this->ziparchive_status();
						trigger_error( sprintf( __( 'Cannot add "%s" to zip archive!', 'backwpup' ), $name_in_archive ), E_USER_ERROR );
						return FALSE;
					} else {
						$file_factor = round( $file_size / ( 1024 * 1024 ), 4 ) * 2;
						$this->file_count = $this->file_count + $file_factor;
					}
				} else {
					if ( ! $this->ziparchive->addFile( $file_name, $name_in_archive ) ) {
						$this->ziparchive_status();
						trigger_error( sprintf( __( 'Cannot add "%s" to zip archive!', 'backwpup' ), $name_in_archive ), E_USER_ERROR );
						return FALSE;
					} else {
						$this->file_count++;
					}
				}
				break;
			case 'PclZip':
				$this->pclzip_file_list[] = array( PCLZIP_ATT_FILE_NAME => $file_name, PCLZIP_ATT_FILE_NEW_FULL_NAME => $name_in_archive );
				if ( count( $this->pclzip_file_list ) >= 100 ) {
					if ( 0 == $this->pclzip->add( $this->pclzip_file_list ) ) {
						trigger_error( sprintf( __( 'PclZip archive add error: %s', 'backwpup' ), $this->pclzip->errorInfo( TRUE ) ), E_USER_ERROR );
						return FALSE;
					}
					$this->pclzip_file_list = array();
				}
				break;
		}

		return TRUE;
	}

	/**
	 * Add a empty Folder to archive
	 *
	 * @param        $folder_name string Name of folder to add to archive
	 * @param string $name_in_archive
	 * @throws BackWPup_Create_Archive_Exception
	 * @return bool
	 */
	public function add_empty_folder( $folder_name, $name_in_archive = '' ) {

		$folder_name = trim( $folder_name );

		//check param
		if ( empty( $folder_name ) ) {
			trigger_error( __( 'Folder name cannot be empty', 'backwpup' ), E_USER_WARNING );
			return FALSE;
		}

		if ( ! is_dir( $folder_name ) || ! is_readable( $folder_name ) ) {
			trigger_error( sprintf( _x( 'Folder %s does not exist or is not readable', 'Folder path to add to archive', 'backwpup' ), $folder_name ), E_USER_WARNING );
			return TRUE;
		}

		if ( empty( $name_in_archive ) ) {
			return FALSE;
		}

		//remove reserved chars
		$name_in_archive = str_replace( array("?", "[", "]", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0)) , '', $name_in_archive );

		switch ( $this->get_method() ) {
			case 'gz':
				trigger_error( __( 'This archive method can only add one file', 'backwpup' ), E_USER_ERROR );
				return FALSE;
				break;
			case 'bz':
				trigger_error( __( 'This archive method can only add one file', 'backwpup' ), E_USER_ERROR );
				return FALSE;
				break;
			case 'Tar':
			case 'TarGz':
			case 'TarBz2':
				if ( ! $this->tar_empty_folder( $folder_name, $name_in_archive ) );
					return FALSE;
				break;
			case 'ZipArchive':
				if ( ! $this->ziparchive->addEmptyDir( $name_in_archive ) ) {
					trigger_error( sprintf( __( 'Cannot add "%s" to zip archive!', 'backwpup' ), $name_in_archive ), E_USER_WARNING );
					return FALSE;
				}
				break;
			case 'PclZip':
				return TRUE;
				break;
		}

		return TRUE;
	}

	/**
	 * Output status of ZipArchive
	 *
	 * @return bool
	 */
	private function ziparchive_status() {

		if ( $this->ziparchive->status == 0 )
			return TRUE;

		trigger_error( sprintf( _x( 'ZipArchive returns status: %s','Text of ZipArchive status Message', 'backwpup' ), $this->ziparchive->getStatusString() ), E_USER_ERROR );
		return FALSE;
	}

	/**
	 * Tar a file to archive
	 */
	private function tar_file( $file_name, $name_in_archive ) {

		if ( ! $this->check_archive_filesize( $file_name ) ) {
			return FALSE;
		}

		$chunk_size = 1024 * 1024 * 4;

		//split filename larger than 100 chars
		if ( strlen( $name_in_archive ) <= 100 ) {
			$filename        = $name_in_archive;
			$filename_prefix = "";
		}
		else {
			$filename_offset = strlen( $name_in_archive ) - 100;
			$split_pos       = strpos( $name_in_archive, '/', $filename_offset );
			if ( $split_pos === FALSE ) {
				$split_pos = strrpos( $name_in_archive, '/' );
			}
			$filename        = substr( $name_in_archive, $split_pos + 1 );
			$filename_prefix = substr( $name_in_archive, 0, $split_pos );
			if ( strlen( $filename ) > 100 ) {
				$filename = substr( $filename, -100 );
				trigger_error( sprintf( __( 'File name "%1$s" is too long to be saved correctly in %2$s archive!', 'backwpup' ), $name_in_archive, $this->get_method() ), E_USER_WARNING );
			}
			if ( strlen( $filename_prefix ) > 155 ) {
				trigger_error( sprintf( __( 'File path "%1$s" is too long to be saved correctly in %2$s archive!', 'backwpup' ), $name_in_archive, $this->get_method() ), E_USER_WARNING );
			}
		}
		//get file stat
		$file_stat = stat( $file_name );
		if ( ! $file_stat ) {
			return TRUE;
		}
		$file_stat[ 'size' ] = abs( (int) $file_stat[ 'size' ] );
		//open file
		if ( $file_stat[ 'size' ] > 0 ) {
			if ( ! ( $fd = fopen( $file_name, 'rb' ) ) ) {
				trigger_error( sprintf( __( 'Cannot open source file %s for archiving', 'backwpup' ), $file_name ), E_USER_WARNING );
				return TRUE;
			}
		}
		//Set file user/group name if linux
		$fileowner = __( "Unknown", "backwpup" );
		$filegroup = __( "Unknown", "backwpup" );
		if ( function_exists( 'posix_getpwuid' ) ) {
			$info      = posix_getpwuid( $file_stat[ 'uid' ] );
			$fileowner = $info[ 'name' ];
			$info      = posix_getgrgid( $file_stat[ 'gid' ] );
			$filegroup = $info[ 'name' ];
		}
		// Generate the TAR header for this file
		$chunk = pack( "a100a8a8a8a12a12a8a1a100a6a2a32a32a8a8a155a12",
			$filename, //name of file  100
			sprintf( "%07o", $file_stat[ 'mode' ] ), //file mode  8
			sprintf( "%07o", $file_stat[ 'uid' ] ), //owner user ID  8
			sprintf( "%07o", $file_stat[ 'gid' ] ), //owner group ID  8
			sprintf( "%011o", $file_stat[ 'size' ] ), //length of file in bytes  12
			sprintf( "%011o", $file_stat[ 'mtime' ] ), //modify time of file  12
			"        ", //checksum for header  8
			0, //type of file  0 or null = File, 5=Dir
			"", //name of linked file  100
			"ustar", //USTAR indicator  6
			"00", //USTAR version  2
			$fileowner, //owner user name 32
			$filegroup, //owner group name 32
			"", //device major number 8
			"", //device minor number 8
			$filename_prefix, //prefix for file name 155
			"" ); //fill block 12

		// Computes the unsigned Checksum of a file's header
		$checksum = 0;
		for ( $i = 0; $i < 512; $i ++ ) {
			$checksum += ord( substr( $chunk, $i, 1 ) );
		}

		$checksum = pack( "a8", sprintf( "%07o", $checksum ) );
		$chunk    = substr_replace( $chunk, $checksum, 148, 8 );

		if ( isset( $fd ) && is_resource( $fd ) ) {
			// read/write files in 512 bite Blocks
			while ( ( $content = fread( $fd, 512 ) ) != '' ) {
				$chunk .= pack( "a512", $content );
				if ( strlen( $chunk ) >= $chunk_size ) {
					if ( $this->method == 'TarGz' ) {
						$chunk = gzencode( $chunk );
					}
					if ( $this->method == 'TarBz2' ) {
						$chunk = bzcompress( $chunk );
					}
					fwrite( $this->filehandel, $chunk );
					$chunk = '';
				}
			}
			fclose( $fd );
		}

		if ( ! empty( $chunk ) ) {
			if ( $this->method == 'TarGz' ) {
				$chunk = gzencode( $chunk );
			}
			if ( $this->method == 'TarBz2' ) {
				$chunk = bzcompress( $chunk );
			}
			fwrite( $this->filehandel, $chunk );
		}

		return TRUE;
	}


	/**
	 * Tar a empty Folder to archive
	 */
	private function tar_empty_folder( $folder_name, $name_in_archive ) {

		$name_in_archive = trailingslashit( $name_in_archive );

		//split filename larger than 100 chars
		if ( strlen( $name_in_archive ) <= 100 ) {
			$tar_filename        = $name_in_archive;
			$tar_filename_prefix = "";
		}
		else {
			$filename_offset = strlen( $name_in_archive ) - 100;
			$split_pos       = strpos( $name_in_archive, '/', $filename_offset );
			if ( $split_pos === FALSE ) {
				$split_pos = strrpos( untrailingslashit( $name_in_archive ), '/' );
			}
			$tar_filename        = substr( $name_in_archive, $split_pos + 1 );
			$tar_filename_prefix = substr( $name_in_archive, 0, $split_pos );
			if ( strlen( $tar_filename ) > 100 ) {
				$tar_filename = substr( $tar_filename, - 100 );
				trigger_error( sprintf( __( 'Folder name "%1$s" is too long to be saved correctly in %2$s archive!', 'backwpup' ), $name_in_archive, $this->get_method() ), E_USER_WARNING );
			}
			if ( strlen( $tar_filename_prefix ) > 155 ) {
				trigger_error( sprintf( __( 'Folder path "%1$s" is too long to be saved correctly in %2$s archive!', 'backwpup' ), $name_in_archive, $this->get_method() ), E_USER_WARNING );
			}
		}
		//get file stat
		$file_stat = @stat( $folder_name );
		//Set file user/group name if linux
		$fileowner = __( "Unknown", "backwpup" );
		$filegroup = __( "Unknown", "backwpup" );
		if ( function_exists( 'posix_getpwuid' ) ) {
			$info      = posix_getpwuid( $file_stat[ 'uid' ] );
			$fileowner = $info[ 'name' ];
			$info      = posix_getgrgid( $file_stat[ 'gid' ] );
			$filegroup = $info[ 'name' ];
		}
		// Generate the TAR header for this file
		$header = pack( "a100a8a8a8a12a12a8a1a100a6a2a32a32a8a8a155a12",
			$tar_filename, //name of file  100
			sprintf( "%07o", $file_stat[ 'mode' ] ), //file mode  8
			sprintf( "%07o", $file_stat[ 'uid' ] ), //owner user ID  8
			sprintf( "%07o", $file_stat[ 'gid' ] ), //owner group ID  8
			sprintf( "%011o", 0 ), //length of file in bytes  12
			sprintf( "%011o", $file_stat[ 'mtime' ] ), //modify time of file  12
			"        ", //checksum for header  8
			5, //type of file  0 or null = File, 5=Dir
			"", //name of linked file  100
			"ustar", //USTAR indicator  6
			"00", //USTAR version  2
			$fileowner, //owner user name 32
			$filegroup, //owner group name 32
			"", //device major number 8
			"", //device minor number 8
			$tar_filename_prefix, //prefix for file name 155
			"" ); //fill block 12

		// Computes the unsigned Checksum of a file's header
		$checksum = 0;
		for ( $i = 0; $i < 512; $i ++ ) {
			$checksum += ord( substr( $header, $i, 1 ) );
		}

		$checksum = pack( "a8", sprintf( "%07o", $checksum ) );
		$header   = substr_replace( $header, $checksum, 148, 8 );
		//write header
		if ( $this->method == 'TarGz' ) {
			$header = gzencode( $header );
		}
		if ( $this->method == 'TarBz2' ) {
			$header = bzcompress( $header );
		}
		fwrite( $this->filehandel, $header );

		return TRUE;
	}

	/**
	 * @param string $file_to_add
	 *
	 * @return bool
	 */
	private function check_archive_filesize( $file_to_add = '' ) {

		$file_to_add_size = 0;
		if ( ! empty( $file_to_add ) ) {
			$file_to_add_size = filesize( $file_to_add );
			if ( $file_to_add_size === FALSE ) {
				$file_to_add_size = 0;
			}
		}

		if ( is_resource( $this->filehandel ) ) {
			$stats = fstat( $this->filehandel );
			$archive_size = $stats[ 'size' ];
		} else {
			$archive_size = filesize( $this->file );
			if ( $archive_size === FALSE ) {
				$archive_size = PHP_INT_MAX;
			}
		}

		$archive_size = $archive_size + $file_to_add_size;
		if ( $archive_size >= PHP_INT_MAX ) {
			trigger_error( sprintf( __( 'If %s will be added to your backup archive, the archive will be too large for operations with this PHP Version. You might want to consider splitting the backup job in multiple jobs with less files each.', 'backwpup' ), $file_to_add ), E_USER_ERROR );

			return FALSE;
		}

		return TRUE;
	}

}

/**
 * Exception Handler
 */
class BackWPup_Create_Archive_Exception extends Exception { }
