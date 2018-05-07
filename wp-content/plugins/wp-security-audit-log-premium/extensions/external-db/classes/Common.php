<?php
/**
 * Class: Utility Class
 *
 * Utility class for common function.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_Ext_Common
 *
 * Utility class, used for all the common functions used in the plugin.
 *
 * @package external-db-wsal
 */
class WSAL_Ext_Common {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var object
	 */
	public $wsal = null;
	protected static $_archiveDb = null;
	protected static $_mirrorDb = null;

	/**
	 * Method: Constructor
	 *
	 * @param object $wsal - Instance of WpSecurityAuditLog.
	 * @since  1.0.0
	 */
	public function __construct( WpSecurityAuditLog $wsal ) {
		$this->wsal = $wsal;
	}

	/**
	 * Set the option by name with the given value.
	 *
	 * @param string $option - Option name.
	 * @param mixed  $value - value.
	 */
	public function AddGlobalOption( $option, $value ) {
		$this->wsal->SetGlobalOption( $option, $value );
	}

	/**
	 * Update the option by name with the given value.
	 *
	 * @param string $option - Option name.
	 * @param mixed  $value - Value.
	 * @return boolean
	 */
	public function UpdateGlobalOption( $option, $value ) {
		return $this->wsal->UpdateGlobalOption( $option, $value );
	}

	/**
	 * Delete the option by name.
	 *
	 * @param string $option - Option name.
	 * @return boolean result
	 */
	public function DeleteGlobalOption( $option ) {
		return $this->wsal->DeleteByName( $option );
	}

	/**
	 * Get the option by name.
	 *
	 * @param string $option - Option name.
	 * @param mix    $default - Default value.
	 * @return mixed value
	 */
	public function GetOptionByName( $option, $default = false ) {
		return $this->wsal->GetGlobalOption( $option, $default );
	}

	/**
	 * Encrypt password, before saves it to the DB.
	 *
	 * @param string $data - Original text.
	 * @return string - Encrypted text
	 */
	public function EncryptPassword( $data ) {
		return $this->wsal->getConnector()->encryptString( $data );
	}

	/**
	 * Decrypt password, after reads it from the DB.
	 *
	 * @param string $ciphertext_base64 - Encrypted text.
	 * @return string - Original text.
	 */
	public function DecryptPassword( $ciphertext_base64 ) {
		return $this->wsal->getConnector()->decryptString( $ciphertext_base64 );
	}

	/**
	 * Get timezone from the settings.
	 *
	 * @return int $gmt_offset_sec
	 */
	public function GetTimezone() {
		$gmt_offset_sec = 0;
		$timezone = $this->wsal->settings->GetTimezone();
		if ( $timezone ) {
			$gmt_offset_sec = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
		} else {
			$gmt_offset_sec = date( 'Z' );
		}
		return $gmt_offset_sec;
	}

	/**
	 * Time Format from WordPress General Settings.
	 *
	 * @return boolean true if time is 24 hours false otherwise
	 */
	public function IsTime24Hours() {
		$wp_time_format = get_option( 'time_format' );
		if ( stripos( $wp_time_format, 'g' ) !== false ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Creates a connection and returns it
	 *
	 * @param array $connectionConfig - Array of connection configurations.
	 * @return wpdb Instance of WPDB
	 */
	private function CreateConnection( $connectionConfig ) {
		$password = $this->DecryptPassword( $connectionConfig['password'] );
		$newWpdb = new wpdb( $connectionConfig['user'], $password, $connectionConfig['name'], $connectionConfig['hostname'] );
		$newWpdb->set_prefix( $connectionConfig['base_prefix'] );
		return $newWpdb;
	}

	/*============================== External Database functions ==============================*/

	/**
	 * Migrate to external database (Metadata table)
	 */
	public function MigrateMeta( $index, $limit ) {
		return $this->wsal->getConnector()->MigrateMeta( $index, $limit );
	}

	/**
	 * Migrate to external database (Occurrences table)
	 */
	public function MigrateOccurrence( $index, $limit ) {
		return $this->wsal->getConnector()->MigrateOccurrence( $index, $limit );
	}

	/**
	 * Migrate back to WP database (Metadata table)
	 */
	public function MigrateBackMeta( $index, $limit ) {
		if ( $index == 0 ) {
			$this->RecreateTables();
		}
		return $this->wsal->getConnector()->MigrateBackMeta( $index, $limit );
	}

	/**
	 * Migrate back to WP database (Occurrences table)
	 */
	public function MigrateBackOccurrence( $index, $limit ) {
		$response = $this->wsal->getConnector()->MigrateBackOccurrence( $index, $limit );
		if ( ( ! empty( $response['complete'] ) && $response['complete']) || ( ! empty( $response['empty'] ) && $response['empty']) ) {
			$this->RemoveConfig();
		}
		return $response;
	}

	/**
	 * Checks if the necessary tables are available.
	 *
	 * @return bool true|false
	 */
	public function IsInstalled() {
		return $this->wsal->getConnector()->isInstalled();
	}

	/**
	 * Remove External DB config.
	 */
	public function RemoveConfig() {
		$this->DeleteGlobalOption( 'wsal-adapter-type' );
		$this->DeleteGlobalOption( 'wsal-adapter-user' );
		$this->DeleteGlobalOption( 'wsal-adapter-password' );
		$this->DeleteGlobalOption( 'wsal-adapter-name' );
		$this->DeleteGlobalOption( 'wsal-adapter-hostname' );
		$this->DeleteGlobalOption( 'wsal-adapter-base-prefix' );
	}

	/**
	 * Recreate DB tables on WP.
	 */
	public function RecreateTables() {
		$occurrence = new WSAL_Models_Occurrence();
		$occurrence->getAdapter()->InstallOriginal();
		$meta = new WSAL_Models_Meta();
		$meta->getAdapter()->InstallOriginal();
	}

	/*============================== Mirroring functions ==============================*/

		/**
		 * Checks if mirroring is enabled.
		 *
		 * @return bool true|false
		 */
	public function IsMirroringEnabled() {
		return $this->GetOptionByName( 'mirroring-e' );
	}

	/**
	 * Enable/Disable mirroring.
	 * if is marked as disabled remove the related config.
	 *
	 * @param bool $enabled true|false
	 */
	public function SetMirroringEnabled( $enabled ) {
		$this->AddGlobalOption( 'mirroring-e', $enabled );
		if ( empty( $enabled ) ) {
			$this->RemoveMirroringDBConfig();
			$this->RemovePapertrailConfig();
			$this->DeleteGlobalOption( 'wsal-mirroring-run-every' );
			$this->DeleteGlobalOption( 'wsal-mirroring-last-created' );
		}
	}

	/**
	 * Get mirroring type (database, papertrail, syslog).
	 *
	 * @return string value
	 */
	public function GetMirroringType() {
		return $this->GetOptionByName( 'mirroring-type' );
	}

	/**
	 * Set mirroring type (database, papertrail, syslog).
	 *
	 * @param string value
	 */
	public function SetMirroringType( $newvalue ) {
		$this->AddGlobalOption( 'mirroring-type', $newvalue );
		if ( $newvalue == 'database' ) {
			$this->RemovePapertrailConfig();
		} elseif ( $newvalue == 'papertrail' ) {
			$this->RemoveMirroringDBConfig();
		} elseif ( $newvalue == 'syslog' ) {
			$this->RemoveMirroringDBConfig();
			$this->RemovePapertrailConfig();
		}
	}

	/**
	 * Get mirroring frequency.
	 *
	 * @return string frequency
	 */
	public function GetMirroringRunEvery() {
		return $this->GetOptionByName( 'mirroring-run-every', 'hourly' );
	}

	/**
	 * Set mirroring frequency.
	 *
	 * @param string frequency
	 */
	public function SetMirroringRunEvery( $newvalue ) {
		$this->AddGlobalOption( 'mirroring-run-every', $newvalue );
	}

	/**
	 * Check if mirroring stops.
	 *
	 * @return bool value
	 */
	public function IsMirroringStop() {
		return $this->GetOptionByName( 'mirroring-stop' );
	}

	/**
	 * Enable/Disable mirroring stop.
	 *
	 * @param string value
	 */
	public function SetMirroringStop( $enabled ) {
		$this->AddGlobalOption( 'mirroring-stop', $enabled );
	}

	/**
	 * Get Papertrail destination.
	 *
	 * @return string value
	 */
	public function GetPapertrailDestination() {
		return trim( $this->GetOptionByName( 'papertrail-destination' ) );
	}

	/**
	 * Set Papertrail destination.
	 *
	 * @param string value
	 */
	public function SetPapertrailDestination( $newvalue ) {
		$this->AddGlobalOption( 'papertrail-destination', $newvalue );
	}

	/**
	 * Check if Papertrail colorization is enabled.
	 *
	 * @return bool value
	 */
	public function IsPapertrailColorizationEnabled() {
		return $this->GetOptionByName( 'papertrail-colorization-e' );
	}

	/**
	 * Enable/Disable Papertrail colorization.
	 *
	 * @param bool value
	 */
	public function SetPapertrailColorization( $enabled ) {
		if ( ! empty( $enabled ) ) {
			$this->AddGlobalOption( 'papertrail-colorization-e', $enabled );
		}
	}

	/**
	 * Remove the mirroring DB config.
	 */
	public function RemoveMirroringDBConfig() {
		$this->DeleteGlobalOption( 'wsal-mirror-type' );
		$this->DeleteGlobalOption( 'wsal-mirror-user' );
		$this->DeleteGlobalOption( 'wsal-mirror-password' );
		$this->DeleteGlobalOption( 'wsal-mirror-name' );
		$this->DeleteGlobalOption( 'wsal-mirror-hostname' );
		$this->DeleteGlobalOption( 'wsal-mirror-base-prefix' );
	}

	/**
	 * Remove the Papertrail config.
	 */
	public function RemovePapertrailConfig() {
		$this->DeleteGlobalOption( 'wsal-papertrail-destination' );
		$this->DeleteGlobalOption( 'wsal-papertrail-colorization-e' );
	}

	/**
	 * Copy to the mirror Database today alerts
	 * and update last_created date.
	 */
	public function MirroringAlertsToDB() {
		$args['mirroring_db'] = $this->MirrorDatabaseConnection();
		$last_created_on = $this->GetOptionByName( 'mirroring-last-created' );
		if ( ! empty( $last_created_on ) ) {
			$args['last_created_on'] = $last_created_on;
		} else {
			$args['last_created_on'] = strtotime( date( 'Y-m-d' ) . ' 00:00:00' );
		}
		$last_created_update = $this->wsal->getConnector()->MirroringAlertsToDB( $args );
		if ( ! empty( $last_created_update ) ) {
			// update last_created
			$this->AddGlobalOption( 'mirroring-last-created', $last_created_update );
		}
	}

	/**
	 * Get last_created alerts
	 * used in send_remote_syslog and send_local_syslog
	 */
	public function GetTodayAlerts() {
		$query = new WSAL_Models_OccurrenceQuery();
		$last_created_on = $this->GetOptionByName( 'mirroring-last-created' );
		if ( ! empty( $last_created_on ) ) {
			$start_from = $last_created_on;
		} else {
			$start_from = strtotime( date( 'Y-m-d' ) . ' 00:00:00' );
		}
		$query->addCondition( 'created_on > %s ', $start_from );
		$items = $query->getAdapter()->Execute( $query );
		if ( ! empty( $items ) ) {
			$last = end( $items );
			// update last_created
			$this->AddGlobalOption( 'mirroring-last-created', $last->created_on );
		}
		return $items;
	}

	/**
	 * Use syslog to a remote destination.
	 */
	public function send_remote_syslog( $site_id, $alert_code, $created_on, $username, $user_roles, $source_ip, $alert_message ) {
		$papertrailDestination = $this->GetPapertrailDestination();
		$destination = array_combine( array( 'hostname', 'port' ), explode( ':', $papertrailDestination ) );

		if ( $this->wsal->IsMultisite() ) {
			$info = get_blog_details( $site_id, true );
			$website = ( ! $info) ? 'Unknown_site_' . $site_id : str_replace( ' ', '_', $info->blogname );
		} else {
			$website = str_replace( ' ', '_', get_bloginfo( 'name' ) );
		}
		$component = 'Security_Audit_Log';
		$date = date( 'M d H:i:s', $created_on + $this->GetTimezone() );
		if ( is_string( $source_ip ) ) {
			$source_ip = str_replace( array( '"', '[', ']' ), '', $source_ip );
		}
		$message = ' ,"' . $source_ip . '", ';
		if ( ! empty( $username ) ) {
			if ( is_array( $user_roles ) && count( $user_roles ) ) {
				$user_roles = ucwords( implode( ', ', $user_roles ) );
			} elseif ( is_string( $user_roles ) && $user_roles != '' ) {
				$user_roles = ucwords( str_replace( array( '"', '[', ']' ), ' ', $user_roles ) );
			} else {
				$user_roles = 'Unknown';
			}
			$message .= $username . '(' . $user_roles . ') ';
		}
		$message .= $alert_message;
		if ( $this->IsPapertrailColorizationEnabled() ) {
			$message = $this->colorise_json( $message );
		}

		$sock = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP );
		foreach ( explode( "\n", $message ) as $line ) {
			$syslog_message = '<22>' . $date . ' ' . $website . ' ' . $component . ':' . $line;
			socket_sendto( $sock, $syslog_message, strlen( $syslog_message ), 0, $destination['hostname'], $destination['port'] );
		}
		socket_close( $sock );
	}

	/**
	 * Use syslog to the local machine.
	 */
	public function send_local_syslog( $site_id, $alert_code, $username, $alert_message, $code ) {
		$code = $code ? $code->code : E_NOTICE;
		$aPriority = array(
			E_CRITICAL => LOG_CRIT,
			E_ERROR => LOG_ERR,
			E_WARNING => LOG_WARNING,
			E_NOTICE => LOG_ALERT,
		);
		$website = ' on website ';
		if ( $this->wsal->IsMultisite() ) {
			$info = get_blog_details( $site_id, true );
			$website .= ( ! $info) ? 'Unknown Site ' . $site_id : $info->blogname;
		} else {
			$website .= str_replace( ' ', '_', get_bloginfo( 'name' ) );
		}
		$alert_message = 'Alert ' . $alert_code . $website . ': ' . $username . ' has ' . $alert_message;

		openlog( 'Security_Audit_Log', LOG_NDELAY, LOG_USER );
		syslog( $aPriority[ $code ], $alert_message );
		closelog();
	}

	/**
	 * Mirroring alerts.
	 */
	public function mirroring_alerts() {
		$type = $this->GetMirroringType();
		if ( $type == 'database' ) {
			$this->MirroringAlertsToDB();
		} elseif ( $type == 'papertrail' ) {
			$alerts = $this->GetTodayAlerts();
			if ( ! empty( $alerts ) ) {
				foreach ( $alerts as $item ) {
					$this->send_remote_syslog(
						$item->site_id,
						$item->alert_id,
						$item->created_on,
						$item->GetUsername(),
						$item->GetUserRoles(),
						$item->GetSourceIP(),
						$item->GetMessage()
					);
				}
			}
		} elseif ( $type == 'syslog' ) {
			$alerts = $this->GetTodayAlerts();
			if ( ! empty( $alerts ) ) {
				foreach ( $alerts as $item ) {
					$this->send_local_syslog(
						$item->site_id,
						$item->alert_id,
						$item->GetUsername(),
						$item->GetMessage(),
						$this->wsal->alerts->GetAlert( $item->alert_id )
					);
				}
			}
		}
	}

	private function colorise_json( $json ) {
		$seq = array(
			'reset' => "\033[0m",
			'color' => "\033[1;%dm",
			'bold'  => "\033[1m",
		);
		$fcolor = array(
			'black'   => "\033[30m",
			'red'     => "\033[31m",
			'green'   => "\033[32m",
			'yellow'  => "\033[33m",
			'blue'    => "\033[34m",
			'magenta' => "\033[35m",
			'cyan'    => "\033[36m",
			'white'   => "\033[37m",
		);
		$bcolor = array(
			'black'   => "\033[40m",
			'red'     => "\033[41m",
			'green'   => "\033[42m",
			'yellow'  => "\033[43m",
			'blue'    => "\033[44m",
			'magenta' => "\033[45m",
			'cyan'    => "\033[46m",
			'white'   => "\033[47m",
		);
		$output = $json;
		$output = preg_replace( '/(":)([0-9]+)/', '$1' . $fcolor['magenta'] . '$2' . $seq['reset'], $output );
		$output = preg_replace( '/(":)(true|false)/', '$1' . $fcolor['magenta'] . '$2' . $seq['reset'], $output );
		$output = str_replace( '{"', '{' . $fcolor['green'] . '"', $output );
		$output = str_replace( ',"', ',' . $fcolor['green'] . '"', $output );
		$output = str_replace( '":', '"' . $seq['reset'] . ':', $output );
		$output = str_replace( ':"', ':' . $fcolor['green'] . '"', $output );
		$output = str_replace( '",', '"' . $seq['reset'] . ',', $output );
		$output = str_replace( '",', '"' . $seq['reset'] . ',', $output );
		$output = $seq['reset'] . $output . $seq['reset'];
		return $output;
	}

	/**
	 * Get the Mirror connection.
	 *
	 * @return wpdb Instance of WPDB
	 */
	private function MirrorDatabaseConnection() {
		if ( ! empty( self::$_mirrorDb ) ) {
			return self::$_mirrorDb;
		} else {
			$connectionConfig = $this->GetMirrorConfig();
			if ( empty( $connectionConfig ) ) {
				return null;
			} else {
				self::$_mirrorDb = $this->CreateConnection( $connectionConfig );
				return self::$_mirrorDb;
			}
		}
	}

	/**
	 * Get the Mirror current config.
	 *
	 * @return array|null config
	 */
	private function GetMirrorConfig() {
		$type = $this->GetOptionByName( 'mirror-type' );
		if ( empty( $type ) ) {
			return null;
		} else {
			return array(
				'type' => $this->GetOptionByName( 'mirror-type' ),
				'user' => $this->GetOptionByName( 'mirror-user' ),
				'password' => $this->GetOptionByName( 'mirror-password' ),
				'name' => $this->GetOptionByName( 'mirror-name' ),
				'hostname' => $this->GetOptionByName( 'mirror-hostname' ),
				'base_prefix' => $this->GetOptionByName( 'mirror-base-prefix' ),
			);
		}
	}

	/*============================== Archiving functions ==============================*/

	/**
	 * Check if archiving is enabled.
	 *
	 * @return bool value
	 */
	public function IsArchivingEnabled() {
		return $this->GetOptionByName( 'archiving-e' );
	}

	/**
	 * Enable/Disable archiving.
	 *
	 * @param bool value
	 */
	public function SetArchivingEnabled( $enabled ) {
		$this->AddGlobalOption( 'archiving-e', $enabled );
		if ( empty( $enabled ) ) {
			$this->RemoveArchivingConfig();
			$this->DeleteGlobalOption( 'wsal-archiving-last-created' );
		}
	}

	/**
	 * Check if archiving by date is enabled.
	 *
	 * @return bool value
	 */
	public function IsArchivingDateEnabled() {
		return $this->GetOptionByName( 'archiving-date-e', 1 );
	}

	/**
	 * Enable/Disable archiving by date.
	 *
	 * @param bool value
	 */
	public function SetArchivingDateEnabled( $enabled ) {
		if ( ! empty( $enabled ) ) {
			$this->AddGlobalOption( 'archiving-date-e', $enabled );
			$this->DeleteGlobalOption( 'wsal-archiving-limit-e' );
			$this->DeleteGlobalOption( 'wsal-archiving-limit' );
		}
	}

	/**
	 * Check if archiving by limit is enabled.
	 *
	 * @return bool value
	 */
	public function IsArchivingLimitEnabled() {
		return $this->GetOptionByName( 'archiving-limit-e' );
	}

	/**
	 * Enable/Disable archiving by limit.
	 *
	 * @param bool value
	 */
	public function SetArchivingLimitEnabled( $enabled ) {
		if ( ! empty( $enabled ) ) {
			$this->AddGlobalOption( 'archiving-limit-e', $enabled );
			$this->DeleteGlobalOption( 'wsal-archiving-date-e' );
			$this->DeleteGlobalOption( 'wsal-archiving-date' );
			$this->DeleteGlobalOption( 'wsal-archiving-date-type' );
			// disable pruning if archiving is enable
			$this->DisablePruning();
		}
	}

	/**
	 * Get archiving date.
	 *
	 * @return int value
	 */
	public function GetArchivingDate() {
		return (int) $this->GetOptionByName( 'archiving-date', 1 );
	}

	/**
	 * Set archiving date.
	 *
	 * @param int value
	 */
	public function SetArchivingDate( $newvalue ) {
		$this->AddGlobalOption( 'archiving-date', (int) $newvalue );
	}

	/**
	 * Get archiving date type.
	 *
	 * @return string value
	 */
	public function GetArchivingDateType() {
		return $this->GetOptionByName( 'archiving-date-type', 'months' );
	}

	/**
	 * Set archiving date type.
	 *
	 * @param string value
	 */
	public function SetArchivingDateType( $newvalue ) {
		$this->AddGlobalOption( 'archiving-date-type', $newvalue );
	}

	/**
	 * Get archiving limit.
	 *
	 * @return int value
	 */
	public function GetArchivingLimit() {
		return (int) $this->GetOptionByName( 'archiving-limit', 1000 );
	}

	/**
	 * Set archiving date limit.
	 *
	 * @param string value
	 */
	public function SetArchivingLimit( $newvalue ) {
		$this->AddGlobalOption( 'archiving-limit', (int) $newvalue );
	}

	/**
	 * Get archiving frequency.
	 *
	 * @return string frequency
	 */
	public function GetArchivingRunEvery() {
		return $this->GetOptionByName( 'archiving-run-every', 'hourly' );
	}

	/**
	 * Set archiving frequency.
	 *
	 * @param string value
	 */
	public function SetArchivingRunEvery( $newvalue ) {
		$this->AddGlobalOption( 'archiving-run-every', $newvalue );
	}

	/**
	 * Check if archiving stop.
	 *
	 * @return bool value
	 */
	public function IsArchivingStop() {
		return $this->GetOptionByName( 'archiving-stop' );
	}

	/**
	 * Enable/Disable archiving stop.
	 *
	 * @param bool value
	 */
	public function SetArchivingStop( $enabled ) {
		$this->AddGlobalOption( 'archiving-stop', $enabled );
	}

	/**
	 * Remove the archiving config.
	 */
	public function RemoveArchivingConfig() {
		$this->DeleteGlobalOption( 'wsal-archiving-date-e' );
		$this->DeleteGlobalOption( 'wsal-archiving-date' );
		$this->DeleteGlobalOption( 'wsal-archiving-date-type' );
		$this->DeleteGlobalOption( 'wsal-archiving-limit-e' );
		$this->DeleteGlobalOption( 'wsal-archiving-limit' );

		$this->DeleteGlobalOption( 'wsal-archive-type' );
		$this->DeleteGlobalOption( 'wsal-archive-user' );
		$this->DeleteGlobalOption( 'wsal-archive-password' );
		$this->DeleteGlobalOption( 'wsal-archive-name' );
		$this->DeleteGlobalOption( 'wsal-archive-hostname' );
		$this->DeleteGlobalOption( 'wsal-archive-base-prefix' );

		$this->DeleteGlobalOption( 'wsal-archiving-daily-e' );
		$this->DeleteGlobalOption( 'wsal-archiving-weekly-e' );
		$this->DeleteGlobalOption( 'wsal-archiving-week-day' );
		$this->DeleteGlobalOption( 'wsal-archiving-time' );
	}

	/**
	 * Disable the pruning config.
	 */
	public function DisablePruning() {
		$this->AddGlobalOption( 'pruning-date-e', false );
		$this->AddGlobalOption( 'pruning-limit-e', false );
	}

	/**
	 * Archive alerts (Occurrences table)
	 */
	public function ArchiveOccurrence( $args ) {
		$args['archive_db'] = $this->ArchiveDatabaseConnection();
		if ( empty( $args['archive_db'] ) ) {
			return false;
		}
		$last_created_on = $this->GetOptionByName( 'archiving-last-created' );
		if ( ! empty( $last_created_on ) ) {
			$args['last_created_on'] = $last_created_on;
		}
		return $this->wsal->getConnector()->ArchiveOccurrence( $args );
	}

	/**
	 * Archive alerts (Metadata table)
	 */
	public function ArchiveMeta( $args ) {
		$args['archive_db'] = $this->ArchiveDatabaseConnection();
		return $this->wsal->getConnector()->ArchiveMeta( $args );
	}

	/**
	 * Delete alerts from the source tables
	 * after archiving them.
	 */
	public function DeleteAfterArchive( $args ) {
		$args['archive_db'] = $this->ArchiveDatabaseConnection();
		$this->wsal->getConnector()->DeleteAfterArchive( $args );
		if ( ! empty( $args['last_created_on'] ) ) {
			// update last_created
			$this->AddGlobalOption( 'archiving-last-created', $args['last_created_on'] );
		}
	}

	/**
	 * Check if archiving cron job started.
	 *
	 * @return bool
	 */
	public function IsArchivingCronStarted() {
		return $this->GetOptionByName( 'archiving-cron-started' );
	}

	/**
	 * Enable/Disable archiving cron job started option.
	 *
	 * @param bool value
	 */
	public function SetArchivingCronStarted( $value ) {
		if ( ! empty( $value ) ) {
			$this->AddGlobalOption( 'archiving-cron-started', 1 );
		} else {
			$this->DeleteGlobalOption( 'wsal-archiving-cron-started' );
		}
	}

	/**
	 * Archiving alerts.
	 */
	public function archiving_alerts() {
		if ( ! $this->IsArchivingCronStarted() ) {
			set_time_limit( 0 );
			// Start archiving
			$this->SetArchivingCronStarted( true );

			$args = array();
			$args['limit'] = 100;
			$argsResult = false;

			do {
				if ( $this->IsArchivingDateEnabled() ) {
					$num = $this->GetArchivingDate();
					$type = $this->GetArchivingDateType();
					$now = current_time( 'timestamp' );
					$args['by_date'] = $now - (strtotime( $num . ' ' . $type ) - $now);
				}
				if ( $this->IsArchivingLimitEnabled() ) {
					$args['by_limit'] = $this->GetArchivingLimit();
				}
				$argsResult = $this->ArchiveOccurrence( $args );
				if ( ! empty( $argsResult ) ) {
					$argsResult = $this->ArchiveMeta( $argsResult );
				}
				if ( ! empty( $argsResult ) ) {
					$this->DeleteAfterArchive( $argsResult );
				}
			} while ( $argsResult != false );
			// End archiving
			$this->SetArchivingCronStarted( false );
		}
	}

	/**
	 * Get the Archive connection
	 *
	 * @return wpdb Instance of WPDB
	 */
	private function ArchiveDatabaseConnection() {
		if ( ! empty( self::$_archiveDb ) ) {
			return self::$_archiveDb;
		} else {
			$connectionConfig = $this->GetArchiveConfig();
			if ( empty( $connectionConfig ) ) {
				return null;
			} else {
				self::$_archiveDb = $this->CreateConnection( $connectionConfig );
				return self::$_archiveDb;
			}
		}
	}

	/**
	 * Get the Archive config
	 *
	 * @return array|null config
	 */
	private function GetArchiveConfig() {
		$type = $this->GetOptionByName( 'archive-type' );
		if ( empty( $type ) ) {
			return null;
		} else {
			return array(
				'type' => $this->GetOptionByName( 'archive-type' ),
				'user' => $this->GetOptionByName( 'archive-user' ),
				'password' => $this->GetOptionByName( 'archive-password' ),
				'name' => $this->GetOptionByName( 'archive-name' ),
				'hostname' => $this->GetOptionByName( 'archive-hostname' ),
				'base_prefix' => $this->GetOptionByName( 'archive-base-prefix' ),
			);
		}
	}
}
