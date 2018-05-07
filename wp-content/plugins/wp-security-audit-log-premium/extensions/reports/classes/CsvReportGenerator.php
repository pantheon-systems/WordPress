<?php
/**
 * Class WSAL_Rep_CsvReportGenerator
 * Provides utility methods to generate a csv report
 *
 * @package wsal/report
 */

if ( ! class_exists( 'WSAL_Rep_Plugin' ) ) {
	exit( 'You are not allowed to view this page.' );
}

/**
 * Class WSAL_Rep_CsvReportGenerator
 * Provides utility methods to generate a csv report
 *
 * @package wsal/report
 */
class WSAL_Rep_CsvReportGenerator {

	/**
	 * Datetime format.
	 *
	 * @var string
	 */
	protected $datetime_format = null;

	/**
	 * Method: Constructor
	 *
	 * @param string $datetime_format - Datetime format.
	 */
	public function __construct( $datetime_format ) {
		$this->datetime_format = $datetime_format;
	}

	/**
	 * Generate the CSV of the Report.
	 *
	 * @param array  $data - Data.
	 * @param array  $filters - Filters.
	 * @param string $uploads_dir_path - Uploads Directory Path.
	 * @param string $delim - (Optional) Delimiter.
	 * @return int|string
	 */
	public function Generate( array $data, array $filters, $uploads_dir_path, $delim = ',' ) {
		if ( empty( $data ) ) {
			return 0;
		}
		// Split data by blog so we can display an organized report.
		$tempData = array();
		foreach ( $data as $k => $entry ) {
			$blogName   = $entry['blog_name'];
			$user       = get_user_by( 'login', $entry['user_name'] );
			if ( ! empty( $user ) ) {
				$entry['user_firstname']    = $user->first_name;
				$entry['user_lastname']     = $user->last_name;
			} else {
				$entry['user_firstname']    = '';
				$entry['user_lastname']     = '';
			}
			if ( ! isset( $tempData[ $blogName ] ) ) {
				$tempData[ $blogName ] = array();
			}
			array_push( $tempData[ $blogName ], $entry );
		}

		if ( empty( $tempData ) ) {
			return 0;
		}

		// Check directory once more.
		if ( ! is_dir( $uploads_dir_path ) || ! is_readable( $uploads_dir_path ) || ! is_writable( $uploads_dir_path ) ) {
			return 1;
		}

		$fn = 'wsal_report_' . WSAL_Rep_Util_S::GenerateRandomString() . '.csv';
		$fp = $uploads_dir_path . $fn;

		$file = fopen( $fp, 'w' );

		// Add columns.
		if ( ! empty( $filters['number_logins'] ) ) {
			$columns = array(
				array(
					'Username',
					'User',
					'Role',
					'Logins',
				),
			);
		} else {
			if ( isset( $filters['type_statistics'] ) ) {
				$columns = $this->_getColumns( $filters['type_statistics'] );
			} else {
				$columns = array(
					array(
						'Blog Name',
						'Code',
						'Type',
						'Date',
						'Username',
						'User',
						'Role',
						'Source IP',
						'Messsage',
					),
				);
			}
		}

		$out = '';
		foreach ( $columns as $row ) {
			$quoted_data = array_map( array( $this, 'quote' ), $row );
			$out .= sprintf( "%s\n", implode( $delim, $quoted_data ) );
		}
		fwrite( $file, $out );

		if ( ! empty( $filters['number_logins'] ) ) {
			$tempData = array();
			foreach ( $data as $entry ) {
				$user_name = $entry['user_name'];
				$user       = get_user_by( 'login', $user_name );
				if ( ! isset( $tempData[ $user_name ] ) ) {
					$tempData[ $user_name ] = array(
						'counter' => 1,
						'user_name' => $user_name, // Username of the user.
						'user'  => $user->first_name . ' ' . $user->last_name, // First and Last name of the user.
						'role' => $entry['user_name'],
					);
				} else {
					$tempData[ $user_name ]['counter']++;
				}
			}
			foreach ( $tempData as $element ) {
				$values = array(
					array(
						$element['user_name'],
						$element['user'],
						$element['role'],
						$element['counter'],
					),
				);
				$out = '';
				foreach ( $values as $row ) {
					$quoted_data = array_map( array( $this, 'quote' ), $row );
					$out .= sprintf( "%s\n", implode( $delim, $quoted_data ) );
				}
				fwrite( $file, $out );
			}
		} else {
			if ( isset( $filters['type_statistics'] ) ) {
				$this->_writeRows( $file, $data, $filters['type_statistics'], $delim );
			} else {
				foreach ( $tempData as $blogName => $entry ) {
					// Add rows.
					foreach ( $entry as $k => $alert ) {
						// Date Format compatible with Excel.
						$aDate = explode( '.', $alert['date'] );
						$date = DateTime::createFromFormat( $this->datetime_format, $aDate[0] );
						$newDateString = $date->format( 'd/m/Y h:i:s A' );
						$values = array(
							array(
								$alert['blog_name'],
								$alert['alert_id'],
								$alert['code'],
								$newDateString,
								$alert['user_name'],
								$alert['user_firstname'] . ' ' . $alert['user_lastname'],
								$alert['role'],
								$alert['user_ip'],
								$alert['message'],
							),
						);
						$out = '';
						foreach ( $values as $row ) {
							$quoted_data = array_map( array( $this, 'quote' ), $row );
							$out .= sprintf( "%s\n", implode( $delim, $quoted_data ) );
						}
						fwrite( $file, $out );
					}
				}
			}
		}
		fclose( $file );
		return $fn;
	}

	/**
	 * Generate the CSV file of the Unique IP Report.
	 *
	 * @param array  $data - Data.
	 * @param array  $filters - Filters.
	 * @param string $uploads_dir_path - Uploads Directory Path.
	 * @param string $delim - (Optional) Delimiter.
	 */
	public function GenerateUniqueIPS( array $data, $uploads_dir_path, $delim = ',' ) {
		if ( empty( $data ) ) {
			return 0;
		}

		// Check directory once more
		if ( ! is_dir( $uploads_dir_path ) || ! is_readable( $uploads_dir_path ) || ! is_writable( $uploads_dir_path ) ) {
			return 1;
		}

		$fn = 'wsal_report_' . WSAL_Rep_Util_S::GenerateRandomString() . '.csv';
		$fp = $uploads_dir_path . $fn;

		$file = fopen( $fp, 'w' );

		// Add columns
		$columns = array(
			array(
				'Username',
				'Display name',
				'Unique IP',
				'List of IP adresses',
			),
		);
		$out = '';
		foreach ( $columns as $row ) {
			$quoted_data = array_map( array( $this, 'quote' ), $row );
			$out .= sprintf( "%s\n", implode( $delim, $quoted_data ) );
		}
		fwrite( $file, $out );

		foreach ( $data as $k => $element ) {
			$values = array(
				array(
					$element['user_login'],
					$element['display_name'],
					count( $element['ips'] ),
					join( ', ', $element['ips'] ),
				),
			);
			$out = '';
			foreach ( $values as $row ) {
				$quoted_data = array_map( array( $this, 'quote' ), $row );
				$out .= sprintf( "%s\n", implode( $delim, $quoted_data ) );
			}
			fwrite( $file, $out );
		}
		fclose( $file );
		return $fn;
	}

	/**
	 * Utility method to quote the given item
	 *
	 * @internal
	 * @param mixed $data - Data.
	 * @return string
	 */
	final public function quote( $data ) {
		$data = preg_replace( '/"(.+)"/', '""$1""', $data );
		return sprintf( '"%s"', $data );
	}

	/**
	 * Get the columns by type of report.
	 */
	private function _getColumns( $typeStatistics ) {
		// Logins Report
		if ( $typeStatistics == WSAL_Rep_Common::LOGIN_BY_USER || $typeStatistics == WSAL_Rep_Common::LOGIN_BY_ROLE ) {
			$columns = array(
				array(
					'Date',
					'Number of Logins',
				),
			);
		}
		// Views Report
		if ( $typeStatistics == WSAL_Rep_Common::VIEWS_BY_USER || $typeStatistics == WSAL_Rep_Common::VIEWS_BY_ROLE ) {
			$columns = array(
				array(
					'Date',
					'Views',
				),
			);
		}
		// Published content Report
		if ( $typeStatistics == WSAL_Rep_Common::PUBLISHED_BY_USER || $typeStatistics == WSAL_Rep_Common::PUBLISHED_BY_ROLE ) {
			$columns = array(
				array(
					'Date',
					'Published',
				),
			);
		}
		return $columns;
	}

	/**
	 * Write the rows of the file.
	 */
	private function _writeRows( $file, $data, $typeStatistics, $delim ) {
		$tempData = array();
		// Logins Report
		if ( $typeStatistics == WSAL_Rep_Common::LOGIN_BY_USER || $typeStatistics == WSAL_Rep_Common::LOGIN_BY_ROLE ) {
			foreach ( $data as $entry ) {
				$aDate = explode( ' ', $entry['date'] );
				$entry_date = $aDate[0];
				if ( ! isset( $tempData[ $entry_date ] ) ) {
					$tempData[ $entry_date ] = array(
						'count' => 1,
					);
				} else {
					$tempData[ $entry_date ]['count']++;
				}
			}
			foreach ( $tempData as $date => $element ) {
				$values = array(
					array( $date, $element['count'] ),
				);
				$out = '';
				foreach ( $values as $row ) {
					$quoted_data = array_map( array( $this, 'quote' ), $row );
					$out .= sprintf( "%s\n", implode( $delim, $quoted_data ) );
				}
				fwrite( $file, $out );
			}
		}
		// Views Report
		if ( $typeStatistics == WSAL_Rep_Common::VIEWS_BY_USER || $typeStatistics == WSAL_Rep_Common::VIEWS_BY_ROLE ) {
			foreach ( $data as $entry ) {
				$aDate = explode( ' ', $entry['date'] );
				$entry_date = $aDate[0];
				switch ( $entry['alert_id'] ) {
					case '2101':
						if ( ! empty( $tempData[ $entry_date ]['posts'] ) ) {
							$tempData[ $entry_date ]['posts'] += 1;
						} else {
							$tempData[ $entry_date ]['posts'] = 1;
						}
						break;
					case '2103':
						if ( ! empty( $tempData[ $entry_date ]['pages'] ) ) {
							$tempData[ $entry_date ]['pages'] += 1;
						} else {
							$tempData[ $entry_date ]['pages'] = 1;
						}
						break;
					case '2105':
						if ( ! empty( $tempData[ $entry_date ]['custom'] ) ) {
							$tempData[ $entry_date ]['custom'] += 1;
						} else {
							$tempData[ $entry_date ]['custom'] = 1;
						}
						break;
				}
			}
			foreach ( $tempData as $date => $element ) {
				$values = array(
					array( $date, '' ),
					array( 'posts', ! empty( $element['posts'] ) ? $element['posts'] : 0 ),
					array( 'pages', ! empty( $element['pages'] ) ? $element['pages'] : 0 ),
					array( 'custom', ! empty( $element['custom'] ) ? $element['custom'] : 0 ),
				);
				$out = '';
				foreach ( $values as $row ) {
					$quoted_data = array_map( array( $this, 'quote' ), $row );
					$out .= sprintf( "%s\n", implode( $delim, $quoted_data ) );
				}
				fwrite( $file, $out );
			}
		}
		// Published content Report
		if ( $typeStatistics == WSAL_Rep_Common::PUBLISHED_BY_USER || $typeStatistics == WSAL_Rep_Common::PUBLISHED_BY_ROLE ) {
			foreach ( $data as $entry ) {
				$aDate = explode( ' ', $entry['date'] );
				$entry_date = $aDate[0];
				switch ( $entry['alert_id'] ) {
					case '2001':
						if ( ! empty( $tempData[ $entry_date ]['posts'] ) ) {
							$tempData[ $entry_date ]['posts'] += 1;
						} else {
							$tempData[ $entry_date ]['posts'] = 1;
						}
						break;
					case '2005':
						if ( ! empty( $tempData[ $entry_date ]['pages'] ) ) {
							$tempData[ $entry_date ]['pages'] += 1;
						} else {
							$tempData[ $entry_date ]['pages'] = 1;
						}
						break;
					case '2030':
						if ( ! empty( $tempData[ $entry_date ]['custom'] ) ) {
							$tempData[ $entry_date ]['custom'] += 1;
						} else {
							$tempData[ $entry_date ]['custom'] = 1;
						}
						break;
					case '9001':
						if ( ! empty( $tempData[ $entry_date ]['woocommerce'] ) ) {
							$tempData[ $entry_date ]['woocommerce'] += 1;
						} else {
							$tempData[ $entry_date ]['woocommerce'] = 1;
						}
						break;
				}
			}
			foreach ( $tempData as $date => $element ) {
				$values = array(
					array( $date, '' ),
					array( 'posts', ! empty( $element['posts'] ) ? $element['posts'] : 0 ),
					array( 'pages', ! empty( $element['pages'] ) ? $element['pages'] : 0 ),
					array( 'custom', ! empty( $element['custom'] ) ? $element['custom'] : 0 ),
					array( 'woocommerce', ! empty( $element['woocommerce'] ) ? $element['woocommerce'] : 0 ),
				);
				$out = '';
				foreach ( $values as $row ) {
					$quoted_data = array_map( array( $this, 'quote' ), $row );
					$out .= sprintf( "%s\n", implode( $delim, $quoted_data ) );
				}
				fwrite( $file, $out );
			}
		}
	}
}
