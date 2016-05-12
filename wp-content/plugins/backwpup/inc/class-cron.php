<?php
/**
 * Class for BackWPup cron methods
 */
class BackWPup_Cron {

	/**
	 * @static
	 *
	 * @param $arg
	 * @internal param $args
	 */
	public static function run( $arg = 'restart' ) {

		if ( $arg === 'restart' ) {
			//reschedule restart
			wp_schedule_single_event( time() + 60, 'backwpup_cron', array( 'id' => 'restart' ) );
			//restart job if not working or a restart imitated
			self::cron_active( array( 'run' => 'restart' ) );

			return;
		}

		$arg = abs( $arg );
		if ( ! $arg ) {
			return;
		}

		//check that job exits
		$jobids = BackWPup_Option::get_job_ids( 'activetype', 'wpcron' );
		if ( ! in_array( $arg, $jobids, true ) ) {
			return;
		}

		//delay other job start for 5 minutes if already one is running
		$job_object = BackWPup_Job::get_working_data();
		if ( $job_object ) {
			wp_schedule_single_event( time() + 300, 'backwpup_cron', array( 'id' => $arg ) );

			return;
		}

		//reschedule next job run
		$cron_next = self::cron_next( BackWPup_Option::get( $arg, 'cron' ) );
		wp_schedule_single_event( $cron_next, 'backwpup_cron', array( 'id' => $arg ) );

		//start job
		self::cron_active( array(
			'run'   => 'cronrun',
			'jobid' => $arg
		) );

	}


	/**
	 * Check Jobs worked and Cleanup logs and so on
	 */
	public static function check_cleanup() {

		$job_object = BackWPup_Job::get_working_data();
		$log_folder = get_site_option( 'backwpup_cfg_logfolder' );
		$log_folder = BackWPup_File::get_absolute_path( $log_folder );

		// check aborted jobs for longer than a tow hours, abort them courtly and send mail
		if ( is_object( $job_object ) && ! empty( $job_object->logfile ) ) {
			$not_worked_time = microtime( TRUE ) - $job_object->timestamp_last_update;
			if ( $not_worked_time > 3600 ) {
				$job_object->log( E_USER_ERROR, __( 'Aborted, because no progress for one hour!', 'backwpup' ), __FILE__, __LINE__ );
				unlink( BackWPup::get_plugin_data( 'running_file' ) );
				$job_object->update_working_data();
			}
		}

		//Compress not compressed logs
		if ( is_readable( $log_folder ) && function_exists( 'gzopen' ) && get_site_option( 'backwpup_cfg_gzlogs' ) && ! is_object( $job_object ) ) {
			//Compress old not compressed logs
			if ( $dir = opendir( $log_folder ) ) {
				$jobids = BackWPup_Option::get_job_ids();
				while ( FALSE !== ( $file = readdir( $dir ) ) ) {
					if ( is_writeable( $log_folder . $file ) && '.html' == substr( $file, -5 ) ) {
						$compress = new BackWPup_Create_Archive( $log_folder . $file . '.gz' );
						if ( $compress->add_file( $log_folder . $file ) ) {
							unlink( $log_folder . $file );
							//change last logfile in jobs
							foreach( $jobids as $jobid ) {
								$job_logfile = BackWPup_Option::get( $jobid, 'logfile' );
								if ( ! empty( $job_logfile ) && $job_logfile === $log_folder . $file ) {
									BackWPup_Option::update( $jobid, 'logfile', $log_folder . $file . '.gz' );
								}
							}
						}
						unset( $compress );
					}
				}
				closedir( $dir );
			}
		}

		//Jobs cleanings
		if ( ! $job_object ) {
			//remove restart cron
			wp_clear_scheduled_hook( 'backwpup_cron', array( 'id' => 'restart' ) );
			//temp cleanup
			BackWPup_Job::clean_temp_folder();
		}

		//check scheduling jobs that not found will removed because there are single scheduled
		$activejobs = BackWPup_Option::get_job_ids( 'activetype', 'wpcron' );
		foreach ( $activejobs as $jobid ) {
			$cron_next = wp_next_scheduled( 'backwpup_cron', array( 'id' => $jobid ) );
			if ( ! $cron_next || $cron_next < time() ) {
				wp_unschedule_event( $cron_next, 'backwpup_cron', array( 'id' => $jobid ) );
				$cron_next = BackWPup_Cron::cron_next( BackWPup_Option::get( $jobid, 'cron' ) );
				wp_schedule_single_event( $cron_next, 'backwpup_cron', array( 'id' => $jobid ) );
			}
		}

	}


	/**
	 * Start job if in cron and run query args are set.
	 */
	public static function cron_active( $args = array() ) {

		//only if cron active
		if ( ! defined( 'DOING_CRON' ) || ! DOING_CRON ) {
			return;
		}

		if ( isset( $_GET[ 'backwpup_run' ] ) ) {
			$args[ 'run' ] = sanitize_text_field( $_GET[ 'backwpup_run' ] );
		}

		if ( isset( $_GET[ '_nonce' ] ) ) {
			$args[ 'nonce' ] = sanitize_text_field( $_GET[ '_nonce' ] );
		}

		if ( isset( $_GET[ 'jobid' ] ) ) {
			$args[ 'jobid' ] = absint( $_GET[ 'jobid' ] );
		}

		$args = array_merge( array(
			'run' => '',
			'nonce' => '',
			'jobid' => 0,
		), $args );

		if ( ! in_array( $args[ 'run' ], array( 'test','restart', 'runnow', 'runnowalt', 'runext', 'cronrun' ), true ) ) {
			return;
		}

		//special header
		@session_write_close();
		@header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ), true );
		@header( 'X-Robots-Tag: noindex, nofollow', true );
		nocache_headers();

		//on test die for fast feedback
		if ( $args['run'] === 'test' ) {
			die( 'BackWPup test request' );
		}

		if ( $args['run'] === 'restart' ) {
			$job_object = BackWPup_Job::get_working_data();
			//restart job if not working or a restart wished
			$not_worked_time = microtime( TRUE ) - $job_object->timestamp_last_update;
			if ( ! $job_object->pid || $not_worked_time > 300 ) {
				BackWPup_Job::start_http( 'restart' );
				return;
			}
		}

		// generate normal nonce
		$nonce = substr( wp_hash( wp_nonce_tick() . 'backwpup_job_run-' . $args['run'], 'nonce' ), - 12, 10 );
		//special nonce on external start
		if ( $args['run'] === 'runext' ) {
			$nonce = get_site_option( 'backwpup_cfg_jobrunauthkey' );
		}
		if ( $args['run'] === 'cronrun' ) {
			$nonce = '';
		}
		// check nonce
		if ( $nonce !== $args['nonce'] ) {
			return;
		}

		//check runext is allowed for job
		if ( $args['run'] === 'runext' ) {
			$jobids_link = BackWPup_Option::get_job_ids( 'activetype', 'link' );
			$jobids_easycron = BackWPup_Option::get_job_ids( 'activetype', 'easycron' );
			$jobids_external = array_merge( $jobids_link, $jobids_easycron );
			if ( ! in_array( $args['jobid'], $jobids_external, true ) ) {
				return;
			}
		}

		//run BackWPup job
		BackWPup_Job::start_http( $args['run'], $args['jobid'] );
	}


	/**
	 *
	 * Get the local time timestamp of the next cron execution
	 *
	 * @param string $cronstring  cron (* * * * *)
	 * @return int timestamp
	 */
	public static function cron_next( $cronstring ) {

		$cron      = array();
		$cronarray = array();
		//Cron string
		list( $cronstr[ 'minutes' ], $cronstr[ 'hours' ], $cronstr[ 'mday' ], $cronstr[ 'mon' ], $cronstr[ 'wday' ] ) = explode( ' ', trim( $cronstring ), 5 );

		//make arrays form string
		foreach ( $cronstr as $key => $value ) {
			if ( strstr( $value, ',' ) ) {
				$cronarray[ $key ] = explode( ',', $value );
			} else {
				$cronarray[ $key ] = array( 0 => $value );
			}
		}

		//make arrays complete with ranges and steps
		foreach ( $cronarray as $cronarraykey => $cronarrayvalue ) {
			$cron[ $cronarraykey ] = array();
			foreach ( $cronarrayvalue as $value ) {
				//steps
				$step = 1;
				if ( strstr( $value, '/' ) ) {
					list( $value, $step ) = explode( '/', $value, 2 );
				}
				//replace weekday 7 with 0 for sundays
				if ( $cronarraykey === 'wday' ) {
					$value = str_replace( '7', '0', $value );
				}
				//ranges
				if ( strstr( $value, '-' ) ) {
					list( $first, $last ) = explode( '-', $value, 2 );
					if ( ! is_numeric( $first ) || ! is_numeric( $last ) || $last > 60 || $first > 60 ) { //check
						return PHP_INT_MAX;
					}
					if ( $cronarraykey === 'minutes' && $step < 5 ) { //set step minimum to 5 min.
						$step = 5;
					}
					$range = array();
					for ( $i = $first; $i <= $last; $i = $i + $step ) {
						$range[ ] = $i;
					}
					$cron[ $cronarraykey ] = array_merge( $cron[ $cronarraykey ], $range );
				}
				elseif ( $value === '*' ) {
					$range = array();
					if ( $cronarraykey === 'minutes' ) {
						if ( $step < 10 ) { //set step minimum to 5 min.
							$step = 10;
						}
						for ( $i = 0; $i <= 59; $i = $i + $step ) {
							$range[ ] = $i;
						}
					}
					if ( $cronarraykey === 'hours' ) {
						for ( $i = 0; $i <= 23; $i = $i + $step ) {
							$range[ ] = $i;
						}
					}
					if ( $cronarraykey === 'mday' ) {
						for ( $i = $step; $i <= 31; $i = $i + $step ) {
							$range[ ] = $i;
						}
					}
					if ( $cronarraykey === 'mon' ) {
						for ( $i = $step; $i <= 12; $i = $i + $step ) {
							$range[ ] = $i;
						}
					}
					if ( $cronarraykey === 'wday' ) {
						for ( $i = 0; $i <= 6; $i = $i + $step ) {
							$range[ ] = $i;
						}
					}
					$cron[ $cronarraykey ] = array_merge( $cron[ $cronarraykey ], $range );
				}
				else {
					if ( ! is_numeric( $value ) || (int) $value > 60 ) {
						return PHP_INT_MAX;
					}
					$cron[ $cronarraykey ] = array_merge( $cron[ $cronarraykey ], array( 0 => absint( $value ) ) );
				}
			}
		}

		//generate years
		$year = (int) gmdate( 'Y' );
		for ( $i = $year; $i < $year + 100; $i ++ ) {
			$cron[ 'year' ][ ] = $i;
		}

		//calc next timestamp
		$current_timestamp = (int) current_time( 'timestamp' );
		foreach ( $cron[ 'year' ] as $year ) {
			foreach ( $cron[ 'mon' ] as $mon ) {
				foreach ( $cron[ 'mday' ] as $mday ) {
					if ( ! checkdate( $mon, $mday, $year ) ) {
						continue;
					}
					foreach ( $cron[ 'hours' ] as $hours ) {
						foreach ( $cron[ 'minutes' ] as $minutes ) {
							$timestamp = gmmktime( $hours, $minutes, 0, $mon, $mday, $year );
							if ( $timestamp && in_array( (int) gmdate( 'j', $timestamp ), $cron[ 'mday' ], true ) && in_array( (int) gmdate( 'w', $timestamp ), $cron[ 'wday' ], true ) && $timestamp > $current_timestamp ) {
								return $timestamp - ( (int) get_option( 'gmt_offset' ) * 3600 );
							}
						}
					}
				}
			}
		}

		return PHP_INT_MAX;
	}

}
