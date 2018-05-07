<?php
/**
 * Reports Utility Class
 *
 * Provides utility methods to generate reports.
 *
 * @since 1.0.0
 * @package report-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WSAL_Rep_Plugin' ) ) {
	exit( 'You are not allowed to view this page.' );
}

/**
 * Class WSAL_Rep_Common
 * Provides utility methods to generate reports.
 *
 * @package report-wsal
 */
class WSAL_Rep_Common {

	const REPORT_HTML = 0;
	const REPORT_CSV = 1;
	const REPORT_DAILY = 'Daily';
	const REPORT_WEEKLY = 'Weekly';
	const REPORT_MONTHLY = 'Monthly';
	const REPORT_QUARTERLY = 'Quarterly';
	const WSAL_PR_PREFIX = 'periodic-report-';

	// Statistics reports criteria.
	const LOGIN_BY_USER = 1;
	const LOGIN_BY_ROLE = 2;
	const VIEWS_BY_USER = 3;
	const VIEWS_BY_ROLE = 4;
	const PUBLISHED_BY_USER = 5;
	const PUBLISHED_BY_ROLE = 6;
	const DIFFERENT_IP = 7;

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var object
	 */
	protected $wsal = null;

	/**
	 * Instance of WSAL_Rep_Util_O.
	 *
	 * @var object
	 */
	protected $ko = null;

	/**
	 * Instance of WSAL_Rep_Util_M.
	 *
	 * @var object
	 */
	protected $km = null;

	/**
	 * GMT Offset.
	 *
	 * @var integer
	 */
	protected $_gmt_offset_sec = 0;

	/**
	 * DateTime format.
	 *
	 * @var string
	 */
	protected $_datetimeFormat = null;

	/**
	 * Date format.
	 *
	 * @var string
	 */
	protected $_dateFormat = null;

	/**
	 * Time format.
	 *
	 * @var string
	 */
	protected $_timeFormat = null;

	/**
	 * Upload directory path.
	 *
	 * @var string
	 * @see CheckDirectory()
	 */
	protected $_uploadsDirPath = null;

	/**
	 * Attachments.
	 *
	 * @var null
	 */
	protected $_attachments = null;

	/**
	 * Holds the alert groups
	 *
	 * @var array
	 */
	private $_catAlertGroups = array();

	/**
	 * Is multisite?
	 *
	 * @var boolean
	 */
	private static $_iswpmu = false;

	/**
	 * Frequency daily hour
	 * For testing change hour here [01 to 23]
	 *
	 * @var string
	 */
	private static $_daily_hour = '08';

	/**
	 * Frequency montly date
	 * For testing change date here [01 to 31]
	 *
	 * @var string
	 */
	private static $_monthly_day = '01';

	/**
	 * Frequency weekly date
	 * For testing change date here [1 (for Monday) through 7 (for Sunday)]
	 *
	 * @var string
	 */
	private static $_weekly_day = '1';

	/**
	 * Schedule hook name
	 * For testing change the name
	 *
	 * @var string
	 */
	private static $_schedule_hook = 'summary_email_reports';

	/**
	 * Errors array.
	 *
	 * @var array
	 */
	private $_errors = array();

	/**
	 * Extension directory path.
	 *
	 * @var string
	 */
	public $_base_dir;

	/**
	 * Extension directory url.
	 *
	 * @var string
	 */
	public $_base_url;

	public function __construct( WpSecurityAuditLog $wsal ) {

		@ini_set( 'max_execution_time', '300' );

		$this->wsal = $wsal;
		$this->ko = new WSAL_Rep_Util_O();
		$this->km = new WSAL_Rep_Util_M();

		// Get DateTime Format from WordPress General Settings.
		$this->_datetimeFormat = $this->wsal->settings->GetDatetimeFormat( false );
		$this->_dateFormat = $this->wsal->settings->GetDateFormat();
		$this->_timeFormat = $this->wsal->settings->GetTimeFormat();

		$timezone = $this->wsal->settings->GetTimezone();
		if ( $timezone ) {
			$this->_gmt_offset_sec = get_option( 'gmt_offset' ) * ( 60 * 60 );
		} else {
			$this->_gmt_offset_sec = date( 'Z' );
		}

		self::$_iswpmu = $this->wsal->IsMultisite();

		// Cron job WordPress.
		add_action( self::$_schedule_hook, array( $this, 'cronJob' ) );
		if ( ! wp_next_scheduled( self::$_schedule_hook ) ) {
			wp_schedule_event( time(), 'hourly', self::$_schedule_hook );
		}
		// Cron job Reports Directory Pruning.
		add_action( 'reports_pruning', array( $this, 'reportsPruning' ) );
		if ( ! wp_next_scheduled( 'reports_pruning' ) ) {
			wp_schedule_event( time(), 'daily', 'reports_pruning' );
		}

		// Set paths.
		$this->_base_dir = WSAL_BASE_DIR . 'extensions/reports';
		$this->_base_url = WSAL_BASE_URL . 'extensions/reports';
	}

	/**
	 * Set the option by name with the given value.
	 *
	 * @param string $option - Option name.
	 * @param mixed  $value - Value.
	 */
	public function AddGlobalOption( $option, $value ) {
		$this->DeleteCacheNotif();
		$this->wsal->SetGlobalOption( $option, $value );
	}

	/**
	 * Delete the option by name.
	 *
	 * @param string $option - Option name.
	 * @return boolean result
	 */
	public function DeleteGlobalOption( $option ) {
		$this->DeleteCacheNotif();
		return $this->wsal->DeleteByName( $option );
	}

	/**
	 * Get the option by name.
	 *
	 * @param string $option - Option name.
	 * @return mixed value
	 */
	public function GetOptionByName( $option ) {
		return $this->wsal->GetGlobalOption( $option );
	}

	/**
	 * Delete cache.
	 */
	public function DeleteCacheNotif() {
		if ( function_exists( 'wp_cache_delete' ) ) {
			wp_cache_delete( WSAL_CACHE_KEY_2 );
		}
	}

	/**
	 * Retrieve list of role names.
	 *
	 * @return array List of role names.
	 */
	public function GetRoles() {
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		return $wp_roles->get_names();
	}

	/**
	 * Date Format from WordPress General Settings.
	 * Used in the form help text.
	 *
	 * @return string
	 */
	public function GetDateFormat() {
		$search = array( 'Y', 'm', 'd' );
		$replace = array( 'yyyy', 'mm', 'dd' );
		return str_replace( $search, $replace, $this->_dateFormat );
	}

	/**
	 * Method: Return Sites.
	 *
	 * @param int|null $limit Maximum number of sites to return (null = no limit).
	 * @return object Object with keys: blog_id, blogname, domain
	 */
	final public static function GetSites( $limit = null ) {
		global $wpdb;
		if ( self::$_iswpmu ) {
			$sql = 'SELECT blog_id, domain FROM ' . $wpdb->blogs;
			if ( ! is_null( $limit ) ) {
				$sql .= ' LIMIT ' . $limit;
			}
			$res = $wpdb->get_results( $sql );
			foreach ( $res as $row ) {
				$row->blogname = get_blog_option( $row->blog_id, 'blogname' );
			}
		} else {
			$res = new stdClass();
			$res->blog_id = get_current_blog_id();
			$res->blogname = esc_html( get_bloginfo( 'name' ) );
			$res = array( $res );
		}
		return $res;
	}

	/**
	 * Retrieve the information about the current blog.
	 *
	 * @return mixed
	 */
	final public static function GetCurrentBlogInfo() {
		global $wpdb;
		$blogId = get_current_blog_id();
		$t = new stdClass();
		$t->blog_id = $blogId;
		$t->blogname = get_blog_option( $blogId, 'blogname' );
		$t->domain = $wpdb->get_var( 'SELECT domain FROM ' . $wpdb->blogs . ' WHERE blog_id=' . $blogId );
		return $t;
	}

	/**
	 * Method: Get site users.
	 *
	 * @param int|null $limit Maximum number of sites to return (null = no limit).
	 */
	final public static function GetUsers( $limit = null ) {
		global $wpdb;
		$t = $wpdb->users;
		$sql = "SELECT ID, user_login FROM {$t}";
		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . $limit;
		}
		return $wpdb->get_results( $sql );
	}

	/**
	 * Get alerts code.
	 *
	 * @return array
	 */
	final public function GetAlertCodes() {
		$data = $this->wsal->alerts->GetAlerts();
		$keys = array();
		if ( ! empty( $data ) ) {
			$keys = array_keys( $data );
			$keys = array_map( array( $this, 'PadKey' ), $keys );
		}
		return $keys;
	}

	/**
	 * Method: Key padding.
	 *
	 * @internal
	 * @param string $key - The key to pad.
	 * @return string
	 */
	final public function PadKey( $key ) {
		if ( strlen( $key ) == 1 ) {
			$key = str_pad( $key, 4, '0', STR_PAD_LEFT );
		}
		return $key;
	}

	/**
	 * Check to see whether or not the specified directory is accessible.
	 *
	 * @param string $dirPath - Directory Path.
	 * @return bool
	 */
	final public function CheckDirectory( $dirPath ) {
		if ( ! is_dir( $dirPath ) ) {
			return false;
		}
		if ( ! is_readable( $dirPath ) ) {
			return false;
		}
		if ( ! is_writable( $dirPath ) ) {
			return false;
		}
		// Create the index.php file if not already there.
		$this->CreateIndexFile( $dirPath );
		$this->_uploadsDirPath = $dirPath;
		return true;
	}

	/**
	 * Create an index.php file, if none exists, in order to avoid directory listing in the specified directory
	 *
	 * @param string $dirPath - Directory Path.
	 * @return bool
	 */
	final public function CreateIndexFile( $dirPath ) {
		// Check if index.php file exists.
		$dirPath = trailingslashit( $dirPath );
		$result = 0;
		if ( ! is_file( $dirPath . 'index.php' ) ) {
			$result = @file_put_contents( $dirPath . 'index.php', '<?php /*[WP Security Audit Log Reporter plugin: This file was auto-generated to prevent directory listing ]*/ exit;' );
		}
		return ($result > 0);
	}

	/**
	 * Formatter for the alert message by name.
	 *
	 * @param string $name - Name of the meta.
	 * @param string $value - Value of the meta.
	 */
	final public function meta_formatter( $name, $value ) {
		switch ( true ) {
			case '%Message%' == $name:
				return esc_html( $value );

			case '%RevisionLink%' == $name:
				if ( ! empty( $value ) && $value != 'NULL' ) {
					return esc_html( ' Navigate to this URL to view the changes ' . $value );
				} else {
					return '';
				}

			case '%CommentLink%' == $name:
			case '%CommentMsg%' == $name:
				return strip_tags( $value );

			case in_array( $name, array( '%MetaValue%', '%MetaValueOld%', '%MetaValueNew%' ) ):
				return (
				strlen( $value ) > 50 ? (esc_html( substr( $value, 0, 50 ) ) . '&hellip;') : esc_html( $value )
				);

			case '%RevisionLink%' == $name:
				return ' Browse this URL to view the changes: ' . esc_html( $value );

			case '%EditorLinkPost%' == $name:
				return '<br>View the post: ' . esc_html( $value );

			case '%EditorLinkPage%' == $name:
				return '<br>View the page: ' . esc_html( $value );

			case '%CategoryLink%' == $name:
				return '<br>View the category: ' . esc_html( $value );

			case '%EditorLinkForum%' == $name:
				return '<br>View the forum: ' . esc_html( $value );

			case '%EditorLinkTopic%' == $name:
				return '<br>View the topic: ' . esc_html( $value );

			case '%LinkFile%' == $name:
				return '<br>To view the requests open the log file ' . esc_url( $value );

			case strncmp( $value, 'http://', 7 ) === 0:
			case strncmp( $value, 'https://', 7 ) === 0:
				return esc_html( $value );

			case '%multisite_text%' === $name:
				if ( $this->wsal->IsMultisite() && $value ) {
					$site_info = get_blog_details( $value, true );
					if ( $site_info ) {
						return ' on site ' . esc_html( $site_info->siteurl );
					}
					return;
				}
				return;

			case '%ReportText%' === $name:
				if ( ! empty( $value ) && 'NULL' != $value ) {
					$report_text = explode( '|', $value );
					if ( isset( $report_text[0] ) && isset( $report_text[1] ) ) {
						$report_text[0] = str_replace( '"', '', $report_text[0] );
						$report_text[1] = str_replace( '"', '', $report_text[1] );
						$report_str = ' from ';
						$report_str .= '<strong>';
						$report_str .= ( ! empty( $report_text[0] ) ) ? esc_html( $report_text[0] ) : 'NULL';
						$report_str .= '</strong>';
						$report_str .= ' to ';
						$report_str .= '<strong>';
						$report_str .= ( ! empty( $report_text[1] ) ) ? esc_html( $report_text[1] ) : 'NULL';
						$report_str .= '</strong>';
						return $report_str;
					}
				}
				return '';

			case '%ChangeText%' === $name:
				return '';

			default:
				return esc_html( $value );
		}
	}

	private function _addError( $error ) {
		array_push( $this->_errors, $error );
	}

	final public function HasErrors() {
		return ( ! empty( $this->_errors ));
	}

	final public function GetErrors() {
		return $this->_errors;
	}

	/**
	 * Get distinct values of IPs.
	 *
	 * @param int $limit - (Optional) Limit.
	 * @return array distinct values of IPs
	 */
	final public static function GetIPAddresses( $limit = null ) {
		$tmp = new WSAL_Models_Meta();
		$ips = $tmp->getAdapter()->GetMatchingIPs( $limit );
		return $ips;
	}

	/**
	 * Get alert details.
	 *
	 * @param int          $entryId - Entry ID.
	 * @param int          $alertId - Alert ID.
	 * @param int          $siteId - Site ID.
	 * @param string       $createdOn - Alert generation time.
	 * @param int          $userId - User ID.
	 * @param string|array $roles - User roles.
	 * @param string       $ip - IP address of the user.
	 * @param string       $ua - User agent.
	 * @return array details
	 */
	private function _getAlertDetails( $entryId, $alertId, $siteId, $createdOn, $userId = null, $roles = null, $ip = '', $ua = '' ) {
		// Must be a new instance every time, otherwise the alert message is not retrieved properly.
		$this->ko = new WSAL_Rep_Util_O();
		// #! Get alert details
		$code = $this->wsal->alerts->GetAlert( $alertId );
		$code = $code ? $code->code : 0;
		$const = (object) array(
			'name' => 'E_UNKNOWN',
			'value' => 0,
			'description' => __( 'Unknown error code.', 'wp-security-audit-log' ),
		);
		$const = $this->wsal->constants->GetConstantBy( 'value', $code, $const );

		// Blog details.
		if ( $this->wsal->IsMultisite() ) {
			$blogInfo = get_blog_details( $siteId, true );
			$blogName = esc_html__( 'Unknown Site', 'wp-security-audit-log' );
			$blogUrl = '';
			if ( $blogInfo ) {
				$blogName = esc_html( $blogInfo->blogname );
				$blogUrl = esc_attr( $blogInfo->siteurl );
			}
		} else {
			$blogName = get_bloginfo( 'name' );
			$blogUrl = '';
			if ( empty( $blogName ) ) {
				$blogName = __( 'Unknown Site', 'wp-security-audit-log' );
			} else {
				$blogName = esc_html( $blogName );
				$blogUrl = esc_attr( get_bloginfo( 'url' ) );
			}
		}

		// Get the alert message - properly.
		$this->ko->id = $entryId;
		$this->ko->site_id = $siteId;
		$this->ko->alert_id = $alertId;
		$this->ko->created_on = $createdOn;
		if ( $this->ko->is_migrated ) {
			$this->ko->_cachedmessage = $this->ko->GetMetaValue( 'MigratedMesg', false );
		}
		if ( ! $this->ko->is_migrated || ! $this->ko->_cachedmessage ) {
			$this->ko->_cachedmessage = $this->ko->GetAlert()->mesg;
		}

		if ( empty( $userId ) ) {
			$username = __( 'System', 'wp-security-audit-log' );
			$role = '';
		} else {
			$user = new WP_User( $userId );
			$username = $user->user_login;
			$role = (is_array( $roles ) ? implode( ', ', $roles ) : $roles);
		}
		if ( empty( $role ) ) {
			$role = '';
		}

		// Meta details.
		$out = array(
			'blog_name' => $blogName,
			'blog_url' => $blogUrl,
			'alert_id' => $alertId,
			'date' => str_replace(
				'$$$',
				substr( number_format( fmod( $createdOn + $this->_gmt_offset_sec, 1 ), 3 ), 2 ),
				date( $this->_datetimeFormat, $createdOn + $this->_gmt_offset_sec )
			),
			'code' => $const->name,
			// Fill variables in message.
			'message' => $this->ko->GetAlert()->GetMessage( $this->ko->GetMetaArray(), array( $this, 'meta_formatter' ), $this->ko->_cachedmessage ),
			'user_id' => $userId,
			'user_name' => $username,
			'role' => $role,
			'user_ip' => $ip,
			'user_agent' => $ua,
		);
		return $out;
	}

	/**
	 * Generate report mathing the filter passed.
	 *
	 * @param array $filters - Filters.
	 * @param bool  $validate - (Optional) Validation.
	 * @return array $dataAndFilters
	 */
	public function GenerateReport( array $filters, $validate = true ) {
		// Region >>> FILTERS VALIDATION.
		if ( $validate ) {
			if ( ! isset( $filters['sites'] ) ) {
				$this->_addError( sprintf( __( 'Internal error. <code>%s</code> key was not found.', 'wp-security-audit-log' ), 'sites' ) );
				return false;
			}
			if ( ! isset( $filters['users'] ) ) {
				$this->_addError( sprintf( __( 'Internal error. <code>%s</code> key was not found.', 'wp-security-audit-log' ), 'users' ) );
				return false;
			}
			if ( ! isset( $filters['roles'] ) ) {
				$this->_addError( sprintf( __( 'Internal error. <code>%s</code> key was not found.', 'wp-security-audit-log' ), 'roles' ) );
				return false;
			}
			if ( ! isset( $filters['ip-addresses'] ) ) {
				$this->_addError( sprintf( __( 'Internal error. <code>%s</code> key was not found.', 'wp-security-audit-log' ), 'ip-addresses' ) );
				return false;
			}
			if ( ! isset( $filters['alert_codes'] ) ) {
				$this->_addError( sprintf( __( 'Internal error. <code>%s</code> key was not found.', 'wp-security-audit-log' ), 'alert_codes' ) );
				return false;
			}
			if ( ! isset( $filters['alert_codes']['groups'] ) ) {
				$this->_addError( sprintf( __( 'Internal error. <code>%s</code> key was not found.', 'wp-security-audit-log' ), 'alert_codes["groups"]' ) );
				return false;
			}
			if ( ! isset( $filters['alert_codes']['alerts'] ) ) {
				$this->_addError( sprintf( __( 'Internal error. <code>%s</code> key was not found.', 'wp-security-audit-log' ), 'alert_codes["alerts"]' ) );
				return false;
			}
			if ( ! isset( $filters['date_range'] ) ) {
				$this->_addError( sprintf( __( 'Internal error. <code>%s</code> key was not found.', 'wp-security-audit-log' ), 'date_range' ) );
				return false;
			}
			if ( ! isset( $filters['date_range']['start'] ) ) {
				$this->_addError( sprintf( __( 'Internal error. <code>%s</code> key was not found.', 'wp-security-audit-log' ), 'date_range["start"]' ) );
				return false;
			}
			if ( ! isset( $filters['date_range']['end'] ) ) {
				$this->_addError( sprintf( __( 'Internal error. <code>%s</code> key was not found.', 'wp-security-audit-log' ), 'date_range["end"]' ) );
				return false;
			}
			if ( ! isset( $filters['report_format'] ) ) {
				$this->_addError( sprintf( __( 'Internal error. <code>%s</code> key was not found.', 'wp-security-audit-log' ), 'report_format' ) );
				return false;
			}
		}
		// endregion >>> FILTERS VALIDATION
		// Filters.
		$sites = ( empty( $filters['sites'] ) ? null : $filters['sites'] );
		$users = ( empty( $filters['users'] ) ? null : $filters['users'] );
		$roles = ( empty( $filters['roles'] ) ? null : $filters['roles'] );
		$ipAddresses = ( empty( $filters['ip-addresses'] ) ? null : $filters['ip-addresses'] );
		$alertGroups = ( empty( $filters['alert_codes']['groups'] ) ? null : $filters['alert_codes']['groups'] );
		$alertCodes = ( empty( $filters['alert_codes']['alerts'] ) ? null : $filters['alert_codes']['alerts'] );
		$post_types = ( empty( $filters['alert_codes']['post_types'] ) ? null : $filters['alert_codes']['post_types'] );
		$post_statuses = ( empty( $filters['alert_codes']['post_statuses'] ) ? null : $filters['alert_codes']['post_statuses'] );
		$dateStart = ( empty( $filters['date_range']['start'] ) ? null : $filters['date_range']['start'] );
		$dateEnd = ( empty( $filters['date_range']['end'] ) ? null : $filters['date_range']['end'] );
		$reportFormat = ( empty( $filters['report_format'] ) ? self::REPORT_HTML : self::REPORT_CSV );

		$_nextDate = (empty( $filters['nextDate'] ) ? null : $filters['nextDate']);
		$_limit = (empty( $filters['limit'] ) ? 0 : $filters['limit']);

		if ( empty( $alertGroups ) && empty( $alertCodes ) ) {
			$this->_addError( __( 'Please specify at least one Alert Group or specify an Alert Code.', 'wp-security-audit-log' ) );
			return false;
		}

		if ( $reportFormat <> self::REPORT_CSV && $reportFormat <> self::REPORT_HTML ) {
			$this->_addError( __( 'Internal Error: Could not detect the type of the report to generate.', 'wp-security-audit-log' ) );
			return false;
		}

		// Check alert codes and post types.
		$_codes = $this->GetCodesByGroups( $alertGroups, $alertCodes );
		if ( ! $_codes ) {
			return false;
		}

		/**
		 * -- @userId: COMMA-SEPARATED-LIST WordPress user id
		 * -- @siteId: COMMA-SEPARATED-LIST WordPress site id
		 * -- @postType: COMMA-SEPARATED-LIST WordPress post types
		 * -- @postStatus: COMMA-SEPARATED-LIST WordPress post statuses
		 * -- @roleName: REGEXP (must be quoted from PHP)
		 * -- @alertCode: COMMA-SEPARATED-LIST of numeric alert codes
		 * -- @startTimestamp: UNIX_TIMESTAMP
		 * -- @endTimestamp: UNIX_TIMESTAMP
		 *
		 * Usage:
		 * --------------------------
		 * set @siteId = null; -- '1,2,3,4....';
		 * set @userId = null;
		 * set @postType = null; -- 'post,page';
		 * set @postStatus = null; -- 'publish,draft';
		 * set @roleName = null; -- '(administrator)|(editor)';
		 * set @alertCode = null; -- '1000,1002';
		 * set @startTimestamp = null;
		 * set @endTimestamp = null;
		 */
		$_siteId = $sites ? "'" . implode( ',', $sites ) . "'" : 'null';
		$_userId = $users ? "'" . implode( ',', $users ) . "'" : 'null';

		$_post_types = 'null';
		if ( $post_types ) {
			$_post_types = array();
			foreach ( $post_types as $post_type ) {
				array_push( $_post_types, preg_quote( $post_type ) );
			}
			$_post_types = "'" . implode( ',', $_post_types ) . "'";
		}

		$_post_statuses = 'null';
		if ( $post_statuses ) {
			$_post_statuses = array();
			foreach ( $post_statuses as $post_status ) {
				array_push( $_post_statuses, preg_quote( $post_status ) );
			}
			$_post_statuses = "'" . implode( ',', $_post_statuses ) . "'";
		}

		$_roleName = 'null';
		if ( $roles ) {
			$_roleName = array();
			foreach ( $roles as $k => $role ) {
				array_push( $_roleName, esc_sql( '(' . preg_quote( $role ) . ')' ) );
			}
			$_roleName = "'" . implode( '|', $_roleName ) . "'";
		}
		$_ipAddress = $ipAddresses ? "'" . implode( ',', $ipAddresses ) . "'" : 'null';

		$_alertCode = ! empty( $_codes ) ? "'" . implode( ',', $_codes ) . "'" : 'null';

		$_startTimestamp = 'null';
		$_endTimestamp = 'null';

		if ( $dateStart ) {
			$dt = new DateTime();
			$df = $dt->createFromFormat( $this->_dateFormat . ' H:i:s', $dateStart . ' 00:00:00' );
			$_startTimestamp = $df->format( 'U' );
		}
		if ( $dateEnd ) {
			$dt = new DateTime();
			$df = $dt->createFromFormat( $this->_dateFormat . ' H:i:s', $dateEnd . ' 23:59:59' );
			$_endTimestamp = $df->format( 'U' );
		}

		$lastDate = null;

		if ( ! empty( $filters['unique_ip'] ) ) {
			$results = $this->wsal->getConnector()->getAdapter( 'Occurrence' )->GetReportGrouped( $_siteId, $_startTimestamp, $_endTimestamp, $_userId, $_roleName, $_ipAddress );
		} else {
			$results = $this->wsal->getConnector()->getAdapter( 'Occurrence' )->GetReporting( $_siteId, $_userId, $_roleName, $_alertCode, $_startTimestamp, $_endTimestamp, $_nextDate, $_limit, $_post_types, $_post_statuses );
		}

		if ( ! empty( $results['lastDate'] ) ) {
			$lastDate = $results['lastDate'];
			unset( $results['lastDate'] );
		}

		if ( empty( $results ) ) {
			$this->_addError( __( 'There are no alerts that match your filtering criteria. Please try a different set of rules.', 'wp-security-audit-log' ) );
			return false;
		}

		$data = array();
		$dataAndFilters = array();

		if ( ! empty( $filters['unique_ip'] ) ) {
			$data = array_values( $results );
		} else {
			// #! Get Alert details
			foreach ( $results as $i => $entry ) {
				$ip = esc_html( $entry->ip );
				$ua = esc_html( $entry->ua );
				$roles = maybe_unserialize( $entry->roles );

				if ( $entry->alert_id == '9999' ) {
					continue;
				}
				if ( is_string( $roles ) ) {
					$roles = str_replace( array( '"', '[', ']' ), ' ', $roles );
				}
				$t = $this->_getAlertDetails( $entry->id, $entry->alert_id, $entry->site_id, $entry->created_on, $entry->user_id, $roles, $ip, $ua );
				if ( ! empty( $ipAddresses ) ) {
					if ( in_array( $entry->ip, $ipAddresses ) ) {
						array_push( $data, $t );
					}
				} else {
					array_push( $data, $t );
				}
			}
		}

		if ( empty( $data ) ) {
			$this->_addError( __( 'There are no alerts that match your filtering criteria. Please try a different set of rules.', 'wp-security-audit-log' ) );
			return false;
		}
		$dataAndFilters['data'] = $data;
		$dataAndFilters['filters'] = $filters;
		$dataAndFilters['lastDate'] = $lastDate;

		return $dataAndFilters;
	}

	/**
	 * Generate the file of the report (HTML or CSV).
	 *
	 * @param data  $data - Data.
	 * @param array $filters - Filters.
	 * @return string|bool - Filename or false.
	 */
	private function FileGenerator( $data, $filters ) {
		$reportFormat = (empty( $filters['report_format'] ) ? self::REPORT_HTML : self::REPORT_CSV);
		$dateStart = ! empty( $filters['date_range']['start'] ) ? $filters['date_range']['start'] : null;
		$dateEnd = ! empty( $filters['date_range']['end'] ) ? $filters['date_range']['end'] : null;
		if ( $reportFormat == self::REPORT_HTML ) {
			$htmlReport = new WSAL_Rep_HtmlReportGenerator( $this->_dateFormat, $this->_gmt_offset_sec );

			if ( isset( $filters['alert_codes']['alerts'] ) ) {
				$criteria = null;
				if ( ! empty( $filters['unique_ip'] ) ) {
					$criteria = 'Number & List of unique IP addresses per user';
				}
				if ( ! empty( $filters['number_logins'] ) ) {
					$criteria = 'Number of Logins per user';
				}
				if ( ! empty( $criteria ) ) {
					unset( $filters['alert_codes']['alerts'] );
					$filters['alert_codes']['alerts'][0] = $criteria;
				}
			}
			// Report Number and list of unique IP.
			if ( ! empty( $filters['unique_ip'] ) ) {
				$result = $htmlReport->GenerateUniqueIPS( $data, $this->_uploadsDirPath, $dateStart, $dateEnd );
			} else {
				$result = $htmlReport->Generate( $data, $filters, $this->_uploadsDirPath, $this->_catAlertGroups );
			}

			if ( $result === 0 ) {
				$this->_addError( __( 'There are no alerts that match your filtering criteria. Please try a different set of rules.', 'wp-security-audit-log' ) );
				$result = false;
			} elseif ( $result === 1 ) {
				$this->_addError( sprintf( __( 'Error: The <strong>%s</strong> path is not accessible.', 'wp-security-audit-log' ), $this->_uploadsDirPath ) );
				$result = false;
			}
			return $result;
		}

		$csvReport = new WSAL_Rep_CsvReportGenerator( $this->_dateFormat . ' ' . $this->_timeFormat );
		// Report Number and list of unique IP.
		if ( ! empty( $filters['unique_ip'] ) ) {
			$result = $csvReport->GenerateUniqueIPS( $data, $this->_uploadsDirPath );
		} else {
			$result = $csvReport->Generate( $data, $filters, $this->_uploadsDirPath );
		}

		if ( $result === 0 ) {
			$this->_addError( __( 'There are no alerts that match your filtering criteria. Please try a different set of rules.', 'wp-security-audit-log' ) );
			$result = false;
		} elseif ( $result === 1 ) {
			$this->_addError( sprintf( __( 'Error: The <strong>%s</strong> path is not accessible.', 'wp-security-audit-log' ), $this->_uploadsDirPath ) );
			$result = false;
		}
		return $result;
	}

	/**
	 * Erase the reports older than 1 week.
	 */
	public function reportsPruning() {
		$uploadsDirObj = wp_upload_dir();
		$wpsalRepUploadsDir = trailingslashit( $uploadsDirObj['basedir'] ) . 'wp-security-audit-log/reports/';
		if ( file_exists( $wpsalRepUploadsDir ) ) {
			if ( $handle = opendir( $wpsalRepUploadsDir ) ) {
				while ( false !== ($entry = readdir( $handle )) ) {
					if ( $entry != '.' && $entry != '..' ) {
						$aFileName = explode( '_', $entry );
						if ( ! empty( $aFileName[2] ) ) {
							if ( $aFileName[2] <= date( 'mdYHis', strtotime( '-1 week' ) ) ) {
								@unlink( $wpsalRepUploadsDir . '/' . $entry );
							}
						}
					}
				}
				closedir( $handle );
			}
		}
	}

	/**
	 * Check the cron job frequency.
	 *
	 * @param string $frequency - Frequency.
	 * @return bool - Send email or Not.
	 */
	private function checkCronJobDate( $frequency ) {
		$send = false;
		switch ( $frequency ) {
			case self::REPORT_DAILY:
				$send = ( self::$_daily_hour === $this->calculate_daily_hour() ) ? true : false;
				break;
			case self::REPORT_WEEKLY:
				$weekly_day = $this->calculate_weekly_day();
				if ( empty( $weekly_day ) ) {
					$send = false;
				} else {
					$send = ( $weekly_day === self::$_weekly_day ) ? true : false;
				}
				break;
			case self::REPORT_MONTHLY:
				$str_date = $this->calculate_monthly_day();
				if ( empty( $str_date ) ) {
					$send = false;
				} else {
					$send = ( date( 'Y-m-d' ) == $str_date ) ? true : false;
				}
				break;
			case self::REPORT_QUARTERLY:
				$send = $this->CheckQuarter();
				break;
		}
		return $send;
	}

	/**
	 * Method: Calculate and return hour of the day
	 * based on WordPress timezone.
	 *
	 * @return string - Hour of the day.
	 * @since 2.1.1
	 */
	private function calculate_daily_hour() {
		return date( 'H', time() + ( get_option( 'gmt_offset' ) * ( 60 * 60 ) ) );
	}

	/**
	 * Method: Calculate and return day of the week
	 * based on WordPress timezone.
	 *
	 * @return string|bool - Day of the week or false.
	 * @since 2.1.1
	 */
	private function calculate_weekly_day() {
		if ( self::$_daily_hour === $this->calculate_daily_hour() ) {
			return date( 'w' );
		}
		return false;
	}

	/**
	 * Method: Calculate and return day of the month
	 * based on WordPress timezone.
	 *
	 * @return string|bool - Day of the week or false.
	 * @since 2.1.1
	 */
	private function calculate_monthly_day() {
		if ( self::$_daily_hour === $this->calculate_daily_hour() ) {
			return date( 'Y-m-' ) . self::$_monthly_day;
		}
		return false;
	}

	/**
	 * Execute cron job.
	 *
	 * @param bool $testSend - (Optional) Send now.
	 */
	public function cronJob( $testSend = false ) {
		$limit = 100;
		$periodicReports = $this->GetPeriodicReports();
		if ( ! empty( $periodicReports ) ) {
			foreach ( $periodicReports as $name => $report ) {
				$sites = $report->sites;
				$type = $report->type;
				$frequency = $report->frequency;
				$send = $this->checkCronJobDate( $frequency );
				if ( $send || $testSend ) {
					if ( ! empty( $report ) ) {
						$nextDate = null;
						$aAlerts = array();
						$post_types = array();
						$post_statuses = array();
						// Unique IP report
						if ( ! empty( $report->enableUniqueIps ) ) {
							$this->SummaryReportUniqueIPS( $name );
						} else {
							$users = ( ! empty( $report->users ) ? $report->users : array());
							$roles = ( ! empty( $report->roles ) ? $report->roles : array());
							$ipAddresses = ( ! empty( $report->ipAddresses ) ? $report->ipAddresses : array());

							if ( ! empty( $report->triggers ) ) {
								foreach ( $report->triggers as $key => $value ) {
									if ( isset( $value['alert_id'] ) && is_array( $value['alert_id'] ) ) {
										foreach ( $value['alert_id'] as $alert_id ) {
											array_push( $aAlerts, $alert_id );
										}
									} elseif ( isset( $value['alert_id'] ) ) {
										array_push( $aAlerts, $value['alert_id'] );
									}

									if ( isset( $value['post_types'] ) && is_array( $value['post_types'] ) ) {
										foreach ( $value['post_types'] as $post_type ) {
											array_push( $post_types, $post_type );
										}
									} elseif ( isset( $value['post_types'] ) ) {
										array_push( $post_types, $value['post_types'] );
									}

									if ( isset( $value['post_statuses'] ) && is_array( $value['post_statuses'] ) ) {
										foreach ( $value['post_statuses'] as $post_status ) {
											array_push( $post_statuses, $post_status );
										}
									} elseif ( isset( $value['post_statuses'] ) ) {
										array_push( $post_statuses, $value['post_statuses'] );
									}
								}
								$aAlerts = array_unique( $aAlerts );

								do {
									$nextDate = $this->BuildAttachment( $name, $aAlerts, $type, $frequency, $sites, $users, $roles, $ipAddresses, $nextDate, $limit, $post_types, $post_statuses );
									$lastDate = $nextDate;
								} while ( $lastDate != null );

								if ( $lastDate == null ) {
									$this->sendSummaryEmail( $name, $aAlerts );
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Send periodic report.
	 *
	 * @param string $report_name - Report name.
	 * @param string $next_date - Next date of report.
	 * @param int    $limit - Limit.
	 * @return string
	 */
	public function sendNowPeriodic( $report_name, $next_date = null, $limit = 100 ) {
		$report = $this->GetOptionByName( $report_name );
		if ( ! empty( $report ) ) {
			$aAlerts = array();
			$post_types = array();
			$post_statuses = array();
			$sites = $report->sites;
			$type = $report->type;
			$frequency = $report->frequency;
			// Unique IP report.
			if ( ! empty( $report->enableUniqueIps ) ) {
				$this->SummaryReportUniqueIPS( $report_name );
				$lastDate = null;
			} else {
				$users = ( ! empty( $report->users ) ? $report->users : array());
				$roles = ( ! empty( $report->roles ) ? $report->roles : array());
				$ipAddresses = ( ! empty( $report->ipAddresses ) ? $report->ipAddresses : array());

				if ( ! empty( $report->triggers ) ) {
					foreach ( $report->triggers as $key => $value ) {
						if ( isset( $value['alert_id'] ) && is_array( $value['alert_id'] ) ) {
							foreach ( $value['alert_id'] as $alert_id ) {
								array_push( $aAlerts, $alert_id );
							}
						} elseif ( isset( $value['alert_id'] ) ) {
							array_push( $aAlerts, $value['alert_id'] );
						}

						if ( isset( $value['post_types'] ) && is_array( $value['post_types'] ) ) {
							foreach ( $value['post_types'] as $post_type ) {
								array_push( $post_types, $post_type );
							}
						} elseif ( isset( $value['post_types'] ) ) {
							array_push( $post_types, $value['post_types'] );
						}

						if ( isset( $value['post_statuses'] ) && is_array( $value['post_statuses'] ) ) {
							foreach ( $value['post_statuses'] as $post_status ) {
								array_push( $post_statuses, $post_status );
							}
						} elseif ( isset( $value['post_statuses'] ) ) {
							array_push( $post_statuses, $value['post_statuses'] );
						}
					}
					$aAlerts = array_unique( $aAlerts );
					$next_date = $this->BuildAttachment( $report_name, $aAlerts, $type, $frequency, $sites, $users, $roles, $ipAddresses, $next_date, $limit, $post_types, $post_statuses );
					$lastDate = $next_date;

					if ( $lastDate == null ) {
						$this->sendSummaryEmail( $report_name, $aAlerts );
					}
				}
			}
			return $lastDate;
		}
	}

	/**
	 * Send the summary email.
	 *
	 * @param string $name - Report name.
	 * @param array  $alertCodes - Array of alert codes.
	 * @return bool $result
	 */
	public function sendSummaryEmail( $name, $alertCodes ) {
		$result = null;
		$report_name = str_replace( 'wsal-', '', $name );
		$notifications = $this->GetOptionByName( $report_name );

		if ( ! empty( $notifications ) ) {
			$email = $notifications->email;
			$frequency = $notifications->frequency;
			$sites = $notifications->sites;
			$title = $notifications->title;

			switch ( $frequency ) {
				case self::REPORT_DAILY:
					$pre_subject = sprintf( __( '%1$s - Website %2$s', 'wp-security-audit-log' ), date( $this->_dateFormat, time() ), get_bloginfo( 'name' ) );
					break;
				case self::REPORT_WEEKLY:
					$pre_subject = sprintf( __( 'Week number %1$s - Website %2$s', 'wp-security-audit-log' ), date( 'W', strtotime( '-1 week' ) ), get_bloginfo( 'name' ) );
					break;
				case self::REPORT_MONTHLY:
					$pre_subject = sprintf( __( 'Month %1$s %2$s- Website %3$s', 'wp-security-audit-log' ), date( 'F', strtotime( '-1 month' ) ), date( 'Y', strtotime( '-1 month' ) ), get_bloginfo( 'name' ) );
					break;
				case self::REPORT_QUARTERLY:
					$pre_subject = sprintf( __( 'Quarter %1$s - Website %2$s', 'wp-security-audit-log' ), $this->WhichQuarter(), get_bloginfo( 'name' ) );
					break;
			}

			// Number logins report.
			$isNumberLogins = false;
			if ( ! empty( $notifications->enableNumberLogins ) ) {
				$isNumberLogins = true;
			}

			$attachments = $this->GetAttachment( $name, $isNumberLogins );
			if ( ! empty( $attachments ) ) {
				$subject = $pre_subject . sprintf( __( ' - %s Email Report', 'wp-security-audit-log' ), $title );
				$content = '<p>The report ' . $title . ' from website ' . get_bloginfo( 'name' ) . ' for';
				switch ( $frequency ) {
					case self::REPORT_DAILY:
						$content .= ' ' . date( $this->_dateFormat, time() );
						break;
					case self::REPORT_WEEKLY:
						$content .= ' week ' . date( 'W', strtotime( '-1 week' ) );
						break;
					case self::REPORT_MONTHLY:
						$content .= ' the month of ' . date( 'F', strtotime( '-1 month' ) ) . ' ' . date( 'Y', strtotime( '-1 month' ) );
						break;
					case self::REPORT_QUARTERLY:
						$content .= ' the quarter ' . $this->WhichQuarter();
						break;
				}
				$content .= ' is attached.</p>';
				$content .= '<p>The report was automatically generated with the <a href="http://www.wpsecurityauditlog.com/extensions/compliance-reports-add-on-for-wordpress/">Reports Add-On</a> for the plugin <a href="http://www.wpsecurityauditlog.com">WP Security Audit Log</a>.</p>';
				$headers = "MIME-Version: 1.0\r\n";

				add_filter( 'wp_mail_content_type', array( $this, '_set_html_content_type' ) );
				add_filter( 'wp_mail_from', array( $this, 'custom_wp_mail_from' ) );
				add_filter( 'wp_mail_from_name', array( $this, 'custom_wp_mail_from_name' ) );
				$result = wp_mail( $email, $subject, $content, $headers, $attachments );

				remove_filter( 'wp_mail_content_type', array( $this, '_set_html_content_type' ) );
				remove_filter( 'wp_mail_from', array( $this, 'custom_wp_mail_from' ) );
				remove_filter( 'wp_mail_from_name', array( $this, 'custom_wp_mail_from_name' ) );
			}
			return $result;
		}
		return $result;
	}

	/**
	 * Get an array with all the Configured Periodic Reports.
	 */
	public function GetPeriodicReports() {
		$aReports = array();
		$reports = $this->wsal->GetNotificationsSetting( self::WSAL_PR_PREFIX );
		if ( ! empty( $reports ) ) {
			foreach ( $reports as $report ) {
				$aReports[ $report->option_name ] = unserialize( $report->option_value );
			}
		}
		return $aReports;
	}

	/**
	 * Create the report appending in a json file.
	 *
	 * @return string $lastDate last_date
	 */
	public function BuildAttachment( $attachKey, $aAlerts, $type, $frequency, $sites, $users, $roles, $ipAddresses, $nextDate, $limit, $post_types = '', $post_statuses = '' ) {
		$lastDate = null;
		$result = $this->GetListEvents( $aAlerts, $type, $frequency, $sites, $users, $roles, $ipAddresses, $nextDate, $limit, $post_types, $post_statuses );

		if ( ! empty( $result['lastDate'] ) ) {
			$lastDate = $result['lastDate'];
			// unset($result['lastDate']);
		}
		$filename = $this->_uploadsDirPath . 'result_' . $attachKey . '-user' . get_current_user_id() . '.json';
		if ( file_exists( $filename ) ) {
			$data = json_decode( file_get_contents( $filename ), true );
			if ( ! empty( $data ) ) {
				if ( ! empty( $result ) ) {
					foreach ( $result['data'] as $value ) {
						array_push( $data['data'], $value );
					}
				}
				$data['lastDate'] = $lastDate;
				file_put_contents( $filename, json_encode( $data ) );
			}
		} else {
			if ( ! empty( $result ) ) {
				file_put_contents( $filename, json_encode( $result ) );
			}
		}
		return $lastDate;
	}

	/**
	 * Generate the file (HTML or CSV) from the json file.
	 *
	 * @return string $result path of the file
	 */
	private function GetAttachment( $attachKey, $isNumberLogins ) {
		$result = null;
		$upload_dir = wp_upload_dir();
		$this->_uploadsDirPath = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/reports/';
		$filename = $this->_uploadsDirPath . 'result_' . $attachKey . '-user' . get_current_user_id() . '.json';
		if ( file_exists( $filename ) ) {
			$data = json_decode( file_get_contents( $filename ), true );
			if ( $isNumberLogins ) {
				$data['filters']['number_logins'] = true;
			}
			$result = $this->FileGenerator( $data['data'], $data['filters'] );
			$result = $this->_uploadsDirPath . $result;
		}
		@unlink( $filename );
		return $result;
	}

	/**
	 * Appending the report data to the content of the json file.
	 *
	 * @param string $report - Report data.
	 */
	public function generateReportJsonFile( $report ) {
		$upload_dir = wp_upload_dir();
		$this->_uploadsDirPath = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/reports/';
		$filename = $this->_uploadsDirPath . 'report-user' . get_current_user_id() . '.json';
		if ( file_exists( $filename ) ) {
			$data = json_decode( file_get_contents( $filename ), true );
			if ( ! empty( $data ) ) {
				if ( ! empty( $report ) ) {
					foreach ( $report['data'] as $value ) {
						array_push( $data['data'], $value );
					}
				}
				file_put_contents( $filename, json_encode( $data ) );
			}
		} else {
			if ( ! empty( $report ) ) {
				file_put_contents( $filename, json_encode( $report ) );
			}
		}
	}

	/**
	 * Generate the file on download it.
	 *
	 * @return string $download_page_url file URL
	 */
	public function downloadReportFile() {
		$download_page_url = null;
		$upload_dir = wp_upload_dir();
		$this->_uploadsDirPath = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/reports/';
		$filename = $this->_uploadsDirPath . 'report-user' . get_current_user_id() . '.json';
		if ( file_exists( $filename ) ) {
			$data = json_decode( file_get_contents( $filename ), true );
			$result = $this->FileGenerator( $data['data'], $data['filters'] );
			if ( ! empty( $result ) ) {
				$e = '&f=' . base64_encode( $result ) . '&ctype=' . $data['filters']['report_format'];
				$download_page_url = wp_nonce_url( $this->_base_url . '/download.php', 'wpsal_reporting_nonce_action', 'wpsal_reporting_nonce_name' ) . $e;
			}
		}
		@unlink( $filename );
		return $download_page_url;
	}

	/**
	 * Generate the file of the report (HTML or CSV).
	 *
	 * @return string|bool filename or false
	 */
	private function GetListEvents( $aAlerts, $type, $frequency, $sites, $users, $roles, $ipAddresses, $nextDate, $limit, $post_types, $post_statuses ) {
		switch ( $frequency ) {
			case self::REPORT_DAILY:
				$start_date = date( $this->_dateFormat, strtotime( '00:00:00' ) );
				break;
			case self::REPORT_WEEKLY:
				$start_date = date( $this->_dateFormat, strtotime( '-1 week' ) );
				break;
			case self::REPORT_MONTHLY:
				$start_date = date( $this->_dateFormat, strtotime( '-1 month' ) );
				break;
			case self::REPORT_QUARTERLY:
				$start_date = $this->StartQuarter();
				break;
		}
		$filters['sites'] = $sites;
		$filters['users'] = $users;
		$filters['roles'] = $roles;
		$filters['ip-addresses'] = $ipAddresses;
		$filters['alert_codes']['groups'] = array();
		$filters['alert_codes']['alerts'] = $aAlerts;
		$filters['alert_codes']['post_types'] = $post_types;
		$filters['alert_codes']['post_statuses'] = $post_statuses;
		$filters['date_range']['start'] = $start_date;
		$filters['date_range']['end'] = date( $this->_dateFormat, time() );
		$filters['report_format'] = $type;
		$filters['nextDate'] = $nextDate;
		$filters['limit'] = $limit;
		$upload_dir = wp_upload_dir();
		$this->_uploadsDirPath = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/reports/';
		$result = $this->GenerateReport( $filters, false );
		return $result;
	}

	/**
	 * Check Quarter of the year
	 * in the cron job.
	 *
	 * @return bool true|false
	 */
	private function CheckQuarter() {
		$hour   = date( 'H', time() + ( get_option( 'gmt_offset' ) * ( 60 * 60 ) ) );
		$month  = date( 'n', time() + ( get_option( 'gmt_offset' ) * ( 60 * 60 ) ) );
		$day    = date( 'j', time() + ( get_option( 'gmt_offset' ) * ( 60 * 60 ) ) );
		if ( '1' == $day && self::$_daily_hour === $hour ) {
			switch ( $month ) {
				case '1':
				case '4':
				case '7':
				case '10':
					return true;
					break;
				default:
					return false;
					break;
			}
		}
		return false;
	}

	/**
	 * Get Quarter of the year.
	 *
	 * @return string N. quarter
	 */
	private function WhichQuarter() {
		$month = date( 'n', time() );
		if ( $month >= 1 && $month <= 3 ) {
			return 'Q1';
		} elseif ( $month >= 4 && $month <= 6 ) {
			return 'Q2';
		} elseif ( $month >= 7 && $month <= 9 ) {
			return 'Q3';
		} elseif ( $month >= 10 && $month <= 12 ) {
			return 'Q4';
		}
	}

	/**
	 * Get Start Quarter of the year.
	 *
	 * @return string $start_date
	 */
	private function StartQuarter() {
		$month = date( 'n', time() );
		$year = date( 'Y', time() );
		if ( $month >= 1 && $month <= 3 ) {
			$start_date = date( $this->_dateFormat, strtotime( $year . '-01-01' ) );
		} elseif ( $month >= 4 && $month <= 6 ) {
			$start_date = date( $this->_dateFormat, strtotime( $year . '-04-01' ) );
		} elseif ( $month >= 7 && $month <= 9 ) {
			$start_date = date( $this->_dateFormat, strtotime( $year . '-07-01' ) );
		} elseif ( $month >= 10 && $month <= 12 ) {
			$start_date = date( $this->_dateFormat, strtotime( $year . '-10-01' ) );
		}
		return $start_date;
	}

	/**
	 * Alert Groups
	 * if we have alert groups, we need to retrieve all alert codes for those groups
	 * and add them to a final alert of alert codes that will be sent to db in the select query
	 * the same goes for individual alert codes
	 */
	private function GetCodesByGroups( $alertGroups, $alertCodes, $showError = true ) {
		$_codes = array();
		$hasAlertGroups = (empty( $alertGroups ) ? false : true);
		$hasAlertCodes = (empty( $alertCodes ) ? false : true);
		if ( $hasAlertCodes ) {
			// Add the specified alerts to the final array.
			$_codes = $alertCodes;
		}
		if ( $hasAlertGroups ) {
			// Get categorized alerts.
			$alerts = $this->wsal->alerts->GetCategorizedAlerts();
			$catAlerts = array();
			foreach ( $alerts as $cname => $group ) {
				foreach ( $group as $subname => $_entries ) {
					$catAlerts[ $subname ] = $_entries;
				}
			}
			$this->_catAlertGroups = array_keys( $catAlerts );
			if ( empty( $catAlerts ) ) {
				if ( $showError ) {
					$this->_addError( __( 'Internal Error. Could not retrieve the alerts from the main plugin.', 'wp-security-audit-log' ) );
				}
				return false;
			}
			// Make sure that all specified alert categories are valid.
			foreach ( $alertGroups as $k => $category ) {
				// get alerts from the category and add them to the final array
				// #! only if the specified category is valid, otherwise skip it.
				if ( isset( $catAlerts[ $category ] ) ) {
					// If this is the "System Activity" category...some of those alert needs to be padded.
					if ( $category == __( 'System Activity', 'wp-security-audit-log' ) ) {
						foreach ( $catAlerts[ $category ] as $i => $alert ) {
							$aid = $alert->type;
							if ( strlen( $aid ) == 1 ) {
								$aid = $this->PadKey( $aid );
							}
							array_push( $_codes, $aid );
						}
					} else {
						foreach ( $catAlerts[ $category ] as $i => $alert ) {
							array_push( $_codes, $alert->type );
						}
					}
				}
			}
		}
		if ( empty( $_codes ) ) {
			if ( $showError ) {
				$this->_addError( __( 'Please specify at least one Alert Group or specify an Alert Code.', 'wp-security-audit-log' ) );
			}
			return false;
		}
		return $_codes;
	}

	/**
	 * Get alerts codes by a SINGLE group name.
	 *
	 * @param string $alertGroup - Group name.
	 * @return array codes
	 */
	public function GetCodesByGroup( $alertGroup ) {
		$_codes = array();
		$alerts = $this->wsal->alerts->GetCategorizedAlerts();
		foreach ( $alerts as $cname => $group ) {
			foreach ( $group as $subname => $_entries ) {
				if ( $subname == $alertGroup ) {
					foreach ( $_entries as $alert ) {
						array_push( $_codes, $alert->type );
					}
					break;
				}
			}
		}
		if ( empty( $_codes ) ) {
			return false;
		}
		return $_codes;
	}

	/**
	 * Create and send the report unique IP by email.
	 *
	 * @param string $name - Group name.
	 */
	public function SummaryReportUniqueIPS( $name ) {
		$report_name = str_replace( 'wsal-', '', $name );
		$notifications = $this->GetOptionByName( $report_name );
		if ( ! empty( $notifications ) ) {
			if ( ! empty( $notifications->enableUniqueIps ) ) {
				$reportFormat = $notifications->type;
				$frequency = $notifications->frequency;
				$email = $notifications->email;
				$sites = (empty( $notifications->sites ) ? null : $notifications->sites);
				$_siteId = ($sites) ? "'" . implode( ',', $sites ) . "'" : 'null';

				$users = (empty( $notifications->users ) ? null : $notifications->users);
				$_userId = ($users) ? "'" . implode( ',', $users ) . "'" : 'null';

				$roles = (empty( $notifications->roles ) ? null : $notifications->roles);
				$_roleName = ($roles) ? "'" . implode( ',', $roles ) . "'" : 'null';

				$ipAddresses = (empty( $notifications->ipAddresses ) ? null : $notifications->ipAddresses);
				$_ipAddress = ($ipAddresses) ? "'" . implode( ',', $ipAddresses ) . "'" : 'null';

				switch ( $frequency ) {
					case self::REPORT_DAILY:
						$dateStart = date( $this->_dateFormat, strtotime( '00:00:00' ) );
						break;
					case self::REPORT_WEEKLY:
						$dateStart = date( $this->_dateFormat, strtotime( '-1 week' ) );
						break;
					case self::REPORT_MONTHLY:
						$dateStart = date( $this->_dateFormat, strtotime( '-1 month' ) );
						break;
					case self::REPORT_QUARTERLY:
						$dateStart = date( $this->_dateFormat, strtotime( '-3 month' ) );
						break;
				}
				$dateEnd = date( $this->_dateFormat, time() );

				if ( $dateStart ) {
					$dt = new DateTime();
					$df = $dt->createFromFormat( $this->_dateFormat . ' H:i:s', $dateStart . ' 00:00:00' );
					$_startTimestamp = $df->format( 'U' );
				}
				if ( $dateEnd ) {
					$dt = new DateTime();
					$df = $dt->createFromFormat( $this->_dateFormat . ' H:i:s', $dateEnd . ' 23:59:59' );
					$_endTimestamp = $df->format( 'U' );
				}

				$results = $this->wsal->getConnector()->getAdapter( 'Occurrence' )->GetReportGrouped( $_siteId, $_startTimestamp, $_endTimestamp, $_userId, $_roleName, $_ipAddress );
				$results = array_values( $results );
				$upload_dir = wp_upload_dir();
				$this->_uploadsDirPath = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/reports/';

				if ( $reportFormat == self::REPORT_HTML ) {
					$htmlReport = new WSAL_Rep_HtmlReportGenerator( $this->_dateFormat, $this->_gmt_offset_sec );
					$attachments = $htmlReport->GenerateUniqueIPS( $results, $this->_uploadsDirPath, $dateStart, $dateEnd );
				} else {
					$csvReport = new WSAL_Rep_CsvReportGenerator( $this->_dateFormat . ' ' . $this->_timeFormat );
					$attachments = $csvReport->GenerateUniqueIPS( $results, $this->_uploadsDirPath );
				}

				switch ( $frequency ) {
					case self::REPORT_DAILY:
						$pre_subject = sprintf( __( '%1$s - Website %2$s', 'wp-security-audit-log' ), date( $this->_dateFormat, time() ), get_bloginfo( 'name' ) );
						break;
					case self::REPORT_WEEKLY:
						$pre_subject = sprintf( __( 'Week number %1$s - Website %2$s', 'wp-security-audit-log' ), date( 'W', strtotime( '-1 week' ) ), get_bloginfo( 'name' ) );
						break;
					case self::REPORT_MONTHLY:
						$pre_subject = sprintf( __( 'Month %1$s %2$s- Website %3$s', 'wp-security-audit-log' ), date( 'F', strtotime( '-1 month' ) ), date( 'Y', strtotime( '-1 month' ) ), get_bloginfo( 'name' ) );
						break;
					case self::REPORT_QUARTERLY:
						$pre_subject = sprintf( __( 'Quarter %1$s - Website %2$s', 'wp-security-audit-log' ), $this->WhichQuarter(), get_bloginfo( 'name' ) );
						break;
				}

				if ( ! empty( $attachments ) ) {
					$attachments = $this->_uploadsDirPath . $attachments;
					$subject = $pre_subject . sprintf( __( ' - %s Email Report', 'wp-security-audit-log' ), 'List of unique IP addresses used by the same user' );
					$content = '<p>The report with the list of unique IP addresses used by the same user on website ' . get_bloginfo( 'name' ) . ' for';
					switch ( $frequency ) {
						case self::REPORT_DAILY:
							$content .= ' ' . date( $this->_dateFormat, time() );
							break;
						case self::REPORT_WEEKLY:
							$content .= ' week ' . date( 'W', strtotime( '-1 week' ) );
							break;
						case self::REPORT_MONTHLY:
							$content .= ' the month of ' . date( 'F', strtotime( '-1 month' ) ) . ' ' . date( 'Y', strtotime( '-1 month' ) );
							break;
						case self::REPORT_QUARTERLY:
							$content .= ' the quarter ' . $this->WhichQuarter();
							break;
					}
					$content .= ' is attached.</p>';
					$content .= '<p>The report was automatically generated with the <a href="http://www.wpsecurityauditlog.com/extensions/compliance-reports-add-on-for-wordpress/">Reports Add-On</a> for the plugin <a href="http://www.wpsecurityauditlog.com">WP Security Audit Log</a>.</p>';
					$headers = "MIME-Version: 1.0\r\n";

					add_filter( 'wp_mail_content_type', array( $this, '_set_html_content_type' ) );
					add_filter( 'wp_mail_from', array( $this, 'custom_wp_mail_from' ) );
					add_filter( 'wp_mail_from_name', array( $this, 'custom_wp_mail_from_name' ) );
					$result = wp_mail( $email, $subject, $content, $headers, $attachments );

					remove_filter( 'wp_mail_content_type', array( $this, '_set_html_content_type' ) );
					remove_filter( 'wp_mail_from', array( $this, 'custom_wp_mail_from' ) );
					remove_filter( 'wp_mail_from_name', array( $this, 'custom_wp_mail_from_name' ) );
				}
			}
		}
	}

	/**
	 * Create and send the report return the URL.
	 *
	 * @param array $filters - Filters.
	 * @return string $download_page_url - Group name.
	 */
	public function StatisticsUniqueIPS( $filters ) {
		$reportFormat = (empty( $filters['report_format'] ) ? self::REPORT_HTML : self::REPORT_CSV);
		$dateStart = ( ! empty( $filters['date_range']['start'] ) ? $filters['date_range']['start'] : null);
		$dateEnd = ( ! empty( $filters['date_range']['end'] ) ? $filters['date_range']['end'] : null);
		$sites = ( ! empty( $filters['sites'] ) ? $filters['sites'] : null);

		$_userId = ( ! empty( $filters['users'] ) ? $filters['users'] : 'null');
		$_roleName = ( ! empty( $filters['roles'] ) ? $filters['roles'] : 'null');
		$_ipAddress = ( ! empty( $filters['ipAddresses'] ) ? $filters['ipAddresses'] : 'null');

		$alertGroups = ( ! empty( $filters['alert_codes']['groups'] ) ? $filters['alert_codes']['groups'] : null);
		$alertCodes = ( ! empty( $filters['alert_codes']['alerts'] ) ? $filters['alert_codes']['alerts'] : null);

		// Alert Groups
		$_codes = $this->GetCodesByGroups( $alertGroups, $alertCodes );
		if ( ! $_codes ) {
			return false;
		}

		$_siteId = $sites ? "'" . implode( ',', $sites ) . "'" : 'null';
		$_startTimestamp = 'null';
		$_endTimestamp = 'null';
		$_alertCode = "'" . implode( ',', $_codes ) . "'";

		if ( $dateStart ) {
			$dt = new DateTime();
			$df = $dt->createFromFormat( $this->_dateFormat . ' H:i:s', $dateStart . ' 00:00:00' );
			$_startTimestamp = $df->format( 'U' );
		}
		if ( $dateEnd ) {
			$dt = new DateTime();
			$df = $dt->createFromFormat( $this->_dateFormat . ' H:i:s', $dateEnd . ' 23:59:59' );
			$_endTimestamp = $df->format( 'U' );
		}

		$results = $this->wsal->getConnector()->getAdapter( 'Occurrence' )->GetReportGrouped( $_siteId, $_startTimestamp, $_endTimestamp, $_userId, $_roleName, $_ipAddress, $_alertCode );
		$results = array_values( $results );
		$upload_dir = wp_upload_dir();
		$this->_uploadsDirPath = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/reports/';

		if ( $reportFormat == self::REPORT_HTML ) {
			$htmlReport = new WSAL_Rep_HtmlReportGenerator( $this->_dateFormat, $this->_gmt_offset_sec );
			$result = $htmlReport->GenerateUniqueIPS( $results, $this->_uploadsDirPath, $dateStart, $dateEnd );
		} else {
			$csvReport = new WSAL_Rep_CsvReportGenerator( $this->_dateFormat . ' ' . $this->_timeFormat );
			$result = $csvReport->GenerateUniqueIPS( $results, $this->_uploadsDirPath );
		}

		if ( $result === 0 ) {
			$this->_addError( __( 'There are no alerts that match your filtering criteria. Please try a different set of rules.', 'wp-security-audit-log' ) );
			$result = false;
		} elseif ( $result === 1 ) {
			$this->_addError( sprintf( __( 'Error: The <strong>%s</strong> path is not accessible.', 'wp-security-audit-log' ), $this->_uploadsDirPath ) );
			$result = false;
		}
		$download_page_url = null;
		if ( ! empty( $result ) ) {
			$e = '&f=' . base64_encode( $result ) . '&ctype=' . $reportFormat;
			$download_page_url = wp_nonce_url( $this->_base_url . '/download.php', 'wpsal_reporting_nonce_action', 'wpsal_reporting_nonce_name' ) . $e;
		}
		return $download_page_url;
	}

	/**
	 * Filter the mail content type.
	 */
	final public function _set_html_content_type() {
		return 'text/html';
	}

	/**
	 * Return if there is a from-email in the setting or the original passed.
	 *
	 * @param string $original_email_from - Original passed.
	 * @return string from email_address
	 */
	final public function custom_wp_mail_from( $original_email_from ) {
		$email_from = $this->GetOptionByName( 'from-email' );
		if ( ! empty( $email_from ) ) {
			return $email_from;
		} else {
			return $original_email_from;
		}
	}

	/**
	 * Return if there is a display-name in the setting or the original passed.
	 *
	 * @param string $original_email_from_name - Original passed.
	 * @return string name
	 */
	final public function custom_wp_mail_from_name( $original_email_from_name ) {
		$email_from_name = $this->GetOptionByName( 'display-name' );
		if ( ! empty( $email_from_name ) ) {
			return $email_from_name;
		} else {
			return $original_email_from_name;
		}
	}

	/*============================== Support Archive Database ==============================*/

	/**
	 * Check if archiving is enabled.
	 *
	 * @return bool value
	 */
	public function IsArchivingEnabled() {
		return $this->GetOptionByName( 'archiving-e' );
	}

	/**
	 * Switch to Archive DB if is enabled
	 */
	public function SwitchToArchiveDB() {
		if ( $this->IsArchivingEnabled() ) {
			$archiveType = $this->GetOptionByName( 'archive-type' );
			$archiveUser = $this->GetOptionByName( 'archive-user' );
			$password = $this->GetOptionByName( 'archive-password' );
			$archiveName = $this->GetOptionByName( 'archive-name' );
			$archiveHostname = $this->GetOptionByName( 'archive-hostname' );
			$archiveBasePrefix = $this->GetOptionByName( 'archive-base-prefix' );
			$config = WSAL_Connector_ConnectorFactory::GetConfigArray( $archiveType, $archiveUser, $password, $archiveName, $archiveHostname, $archiveBasePrefix );
			$this->wsal->getConnector( $config )->getAdapter( 'Occurrence' );
		}
	}

	/**
	 * Close Archive DB
	 */
	public function CloseArchiveDB() {
		if ( $this->IsArchivingEnabled() ) {
			$archiveType = $this->GetOptionByName( 'archive-type' );
			$archiveUser = $this->GetOptionByName( 'archive-user' );
			$password = $this->GetOptionByName( 'archive-password' );
			$archiveName = $this->GetOptionByName( 'archive-name' );
			$archiveHostname = $this->GetOptionByName( 'archive-hostname' );
			$archiveBasePrefix = $this->GetOptionByName( 'archive-base-prefix' );
			$config = WSAL_Connector_ConnectorFactory::GetConfigArray( $archiveType, $archiveUser, $password, $archiveName, $archiveHostname, $archiveBasePrefix );
			$result = $this->wsal->getConnector( $config )->closeConnection();
			$this->wsal->getConnector( null, true )->getAdapter( 'Occurrence' );
		}
	}

	/**
	 * Check if tyhere is match on the peport criteria.
	 *
	 * @param array $filters - Filters.
	 * @return bool value
	 */
	public function IsMatchingReportCriteria( $filters ) {
		// Filters.
		$sites         = ( empty( $filters['sites'] ) ? null : $filters['sites'] );
		$users         = ( empty( $filters['users'] ) ? null : $filters['users'] );
		$roles         = ( empty( $filters['roles'] ) ? null : $filters['roles'] );
		$ipAddresses   = ( empty( $filters['ip-addresses'] ) ? null : $filters['ip-addresses'] );
		$alertGroups   = ( empty( $filters['alert_codes']['groups'] ) ? null : $filters['alert_codes']['groups'] );
		$alertCodes    = ( empty( $filters['alert_codes']['alerts'] ) ? null : $filters['alert_codes']['alerts'] );
		$post_types    = ( empty( $filters['alert_codes']['post_types'] ) ? null : $filters['alert_codes']['post_types'] );
		$post_statuses = ( empty( $filters['alert_codes']['post_statuses'] ) ? null : $filters['alert_codes']['post_statuses'] );
		$dateStart     = ( empty( $filters['date_range']['start'] ) ? null : $filters['date_range']['start'] );
		$dateEnd       = ( empty( $filters['date_range']['end'] ) ? null : $filters['date_range']['end'] );

		$_codes = $this->GetCodesByGroups( $alertGroups, $alertCodes, false );

		$criteria['siteId'] = $sites ? "'" . implode( ',', $sites ) . "'" : 'null';
		$criteria['userId'] = $users ? "'" . implode( ',', $users ) . "'" : 'null';
		$criteria['roleName'] = 'null';
		$criteria['ipAddress'] = ! empty( $ipAddresses ) ? "'" . implode( ',', $ipAddresses ) . "'" : 'null';
		$criteria['alertCode'] = ! empty( $_codes ) ? "'" . implode( ',', $_codes ) . "'" : 'null';
		$criteria['startTimestamp'] = 'null';
		$criteria['endTimestamp'] = 'null';

		$criteria['post_types'] = 'null';
		if ( $post_types ) {
			$_post_types = array();
			foreach( $post_types as $post_type ) {
				array_push( $_post_types, esc_sql( '(' . preg_quote( $post_type ) . ')' ) );
			}
			$criteria['post_types'] = "'" . implode( '|', $_post_types ) . "'";
		}

		$criteria['post_statuses'] = 'null';
		if ( $post_statuses ) {
			$_post_statuses = array();
			foreach( $post_statuses as $post_status ) {
				array_push( $_post_statuses, esc_sql( '(' . preg_quote( $post_status ) . ')' ) );
			}
			$criteria['post_statuses'] = "'" . implode( '|', $_post_statuses ) . "'";
		}

		if ( $roles ) {
			$criteria['roleName'] = array();
			foreach ( $roles as $k => $role ) {
				array_push( $_roleName, esc_sql( '(' . preg_quote( $role ) . ')' ) );
			}
			$criteria['roleName'] = "'" . implode( '|', $_roleName ) . "'";
		}

		if ( $dateStart ) {
			$dt = new DateTime();
			$df = $dt->createFromFormat( $this->_dateFormat . ' H:i:s', $dateStart . ' 00:00:00' );
			$criteria['startTimestamp'] = $df->format( 'U' );
		}

		if ( $dateEnd ) {
			$dt = new DateTime();
			$df = $dt->createFromFormat( $this->_dateFormat . ' H:i:s', $dateEnd . ' 23:59:59' );
			$criteria['endTimestamp'] = $df->format( 'U' );
		}

		$count = $this->wsal->getConnector()->getAdapter( 'Occurrence' )->CheckMatchReportCriteria( $criteria );
		if ( $count > 0 ) {
			return true;
		} else {
			return false;
		}
	}
}
