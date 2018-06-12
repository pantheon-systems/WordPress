<?php
/**
 * Class: Utility Class
 *
 * Utility class for common functions.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WSAL_OPT_PREFIX' ) ) {
	exit( 'Invalid request' );
}

/**
 * Class WSAL_NP_Common
 *
 * Utility class, used for all the common functions used in the plugin.
 *
 * @package wp-security-audit-log
 */
class WSAL_NP_Common {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var WpSecurityAuditLog
	 */
	public $wsal = null;

	const TRANSIENT_FAILED_COUNT = 'wsal-notifications-failed-known-count';
	const TRANSIENT_FAILED_UNKNOWN_COUNT = 'wsal-notifications-failed-unknown-count';
	const TRANSIENT_404_COUNT = 'wsal-notifications-404-count';
	const TRANSIENT_404_VISITOR_COUNT = 'wsal-notifications-404-visitor-count';

	/**
	 * Method: Constructor.
	 *
	 * @param WpSecurityAuditLog $wsal - Instance of WpSecurityAuditLog.
	 */
	public function __construct( WpSecurityAuditLog $wsal ) {
		$this->wsal = $wsal;
	}

	/**
	 * Creates an unique random number.
	 *
	 * @param int $size The length of the number to generate.
	 * @return string
	 */
	public function UniqueNumber( $size = 20 ) {
		$numbers = range( 0, 100 );
		shuffle( $numbers );
		$n = join( '', array_slice( $numbers, 0, $size ) );
		return substr( $n, 0, $size );
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
	 * Update the option by name with the given value.
	 *
	 * @param string $option - Option name.
	 * @param mixed  $value - Value.
	 * @return boolean result
	 */
	public function UpdateGlobalOption( $option, $value ) {
		$this->DeleteCacheNotif();
		return $this->wsal->UpdateGlobalOption( $option, $value );
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
			wp_cache_delete( WSAL_CACHE_KEY );
		}
	}

	/**
	 * Retrieve the appropriate posts table name.
	 *
	 * @param wpdb $wpdb
	 * @return string
	 */
	public function GetPostsTableName( $wpdb ) {
		$pfx = $this->GetDbPrefix( $wpdb );
		if ( $this->wsal->IsMultisite() ) {
			global $blog_id;
			$bid = ($blog_id == 1 ? '' : $blog_id . '_');
			return $pfx . $bid . 'posts';
		}
		return $pfx . 'posts';
	}

	/**
	 * Retrieve the appropriate db prefix.
	 *
	 * @param wpdb $wpdb
	 * @return mixed
	 */
	public function GetDbPrefix( $wpdb ) {
		if ( $this->wsal->IsMultisite() ) {
			return $wpdb->base_prefix;
		}
		return $wpdb->prefix;
	}

	/**
	 * Validate the input from a condition.
	 *
	 * @param string $string
	 * @return mixed
	 */
	public function ValidateInput( $string ) {
		$string = preg_replace( '/<script[^>]*?>.*?<\/script>/i', '', $string );
		$string = preg_replace( '/<[\/\!]*?[^<>]*?>/i', '', $string );
		$string = preg_replace( '/<style[^>]*?>.*?<\/style>/i', '', $string );
		$string = preg_replace( '/<![\s\S]*?--[ \t\n\r]*>/i', '', $string );
		return preg_replace( "/[^a-z0-9.':\-_]/i", '', $string );
	}

	/**
	 * Validate a partial IP address.
	 *
	 * @param string $ip
	 * @return bool
	 */
	public function IsValidPartialIP( $ip ) {
		if ( ! $ip or strlen( trim( $ip ) ) == 0 ) {
			return false;
		}
		$ip = trim( $ip );
		$parts = explode( '.', $ip );
		if ( count( $parts ) <= 4 ) {
			foreach ( $parts as $part ) {
				if ( $part > 255 || $part < 0 ) {
					return false;
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Retrieve list of role names.
	 *
	 * @return array List of role names.
	 */
	public function GetRoleNames() {
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		return $wp_roles->get_names();
	}

	/**
	 * @internal
	 * @param string $key The key to pad
	 * @return string
	 */
	public function PadKey( $key ) {
		if ( strlen( $key ) == 1 ) {
			$key = str_pad( $key, 4, '0', STR_PAD_LEFT );
		}
		return $key;
	}

	/**
	 * Datetime used in the Notifications.
	 *
	 * @return string
	 */
	public function GetDatetimeFormat() {
		$date_format = $this->GetDateFormat();
		$time_format = $this->GetTimeFormat();
		return $date_format . ' ' . $time_format;
	}

	/**
	 * Date Format from WordPress General Settings.
	 *
	 * @return string
	 */
	public function GetDateFormat() {
		return $this->wsal->settings->GetDateFormat();
	}

	/**
	 * Used in the form validation.
	 *
	 * @return string
	 */
	public function DateValidFormat() {
		$search = array( 'Y', 'm', 'd' );
		$replace = array( 'yyyy', 'mm', 'dd' );
		return str_replace( $search, $replace, $this->GetDateFormat() );
	}

	/**
	 * Time Format from WordPress General Settings.
	 *
	 * @return string
	 */
	public function GetTimeFormat() {
		return $this->wsal->settings->GetTimeFormat();
	}

	/**
	 * Check time 24 hours.
	 *
	 * @return bool true/false
	 */
	public function Show24Hours() {
		$format = $this->GetTimeFormat();
		if ( strpos( $format, 'g' ) !== false ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Validate a condition.
	 *
	 * @param object $select2 - Select 2.
	 * @param object $select3 - Select 3 / Comparison.
	 * @param object $select4 - Select 4 / Post Status.
	 * @param object $select5 - Select 5 / Post Type.
	 * @param object $select6 - Select 6 / User Role.
	 * @param string $input_value - Input text box.
	 * @return bool|int|mixed
	 */
	public function ValidateCondition( $select2, $select3, $select4, $select5, $select6, $input_value ) {
		$values = $select2->data;
		$selected = $select2->selected;

		if ( ! isset( $values[ $selected ] ) ) {
			return array(
				'error' => __( 'The form is not valid. Please reload the page and try again.', 'wp-security-audit-log' ),
			);
		}

		// Get what's selected.
		$what = strtoupper( $values[ $selected ] );

		if ( 'ALERT CODE' == $what ) { // if ALERT CODE.
			$length = strlen( $input_value );
			if ( $length <> 4 ) {
				return array(
					'error' => __( 'The ALERT CODE is not valid.', 'wp-security-audit-log' ),
				);
			}
			$alerts = $this->wsal->alerts->GetAlerts();
			if ( empty( $alerts ) ) {
				return array(
					'error' => __( 'Internal Error. Please reload the page and try again.', 'wp-security-audit-log' ),
				);
			}
			// Ensure this is a valid Alert Code.
			$keys = array_keys( $alerts );
			$keys = array_map( array( $this, 'PadKey' ), $keys );
			if ( ! in_array( $input_value, $keys ) ) {
				return array(
					'error' => __( 'The ALERT CODE is not valid.', 'wp-security-audit-log' ),
				);
			}
		} elseif ( 'USERNAME' == $what ) { // IF USERNAME.
			$length = strlen( $input_value );
			if ( $length > 50 ) {
				return array(
					'error' => __( 'The USERNAME is not valid. Maximum of 50 characters allowed.', 'wp-security-audit-log' ),
				);
			}
			// Make sure this is a valid username.
			if ( ! username_exists( $input_value ) ) {
				return array(
					'error' => __( 'The USERNAME does not exist.', 'wp-security-audit-log' ),
				);
			}
		} elseif ( 'USER ROLE' == $what ) { // IF USER ROLE.
			$e = sprintf( __( '%s is not valid', 'wp-security-audit-log' ), $what );
			if ( '0' !== $input_value && empty( $input_value ) ) {
				return array(
					'error' => $e,
				);
			}

			if ( ! isset( $select6->data[ $select6->selected ] ) ) {
				return array(
					'error' => __( 'Selected USER ROLE is not valid.', 'wp-security-audit-log' ),
				);
			}
		} elseif ( 'SOURCE IP' == $what ) { // IF SOURCE IP.
			$length = strlen( $input_value );
			if ( $length > 15 ) {
				return array(
					'error' => __( 'The SOURCE IP is not valid. Maximum of 15 characters allowed.', 'wp-security-audit-log' ),
				);
			}
			$val_s3 = $select3->data[ $select3->selected ];
			if ( ! $val_s3 ) {
				return array(
					'error' => __( 'The form is not valid. Please reload the page and try again.', 'wp-security-audit-log' ),
				);
			}
			if ( 'IS EQUAL' == $val_s3 ) {
				$r = filter_var( $input_value, FILTER_VALIDATE_IP );
				if ( $r ) {
					return true;
				} else {
					return array(
						'error' => __( 'The SOURCE IP is not valid.', 'wp-security-audit-log' ),
					);
				}
			}
			$r = $this->IsValidPartialIP( $input_value );
			if ( $r ) {
				return true;
			} else {
				return array(
					'error' => __( 'The SOURCE IP fragment is not valid.', 'wp-security-audit-log' ),
				);
			}
		} elseif ( 'DATE' == $what ) { // DATE.
			$date_format = $this->DateValidFormat();
			if ( 'mm-dd-yyyy' == $date_format || 'dd-mm-yyyy' == $date_format ) {
				// Regular expression to match date format mm-dd-yyyy or dd-mm-yyyy.
				$reg_ex = '/^\d{1,2}-\d{1,2}-\d{4}$/';
			} else {
				// Regular expression to match date format yyyy-mm-dd.
				$reg_ex = '/^\d{4}-\d{1,2}-\d{1,2}$/';
			}
			$r = preg_match( $reg_ex, $input_value );
			if ( $r ) {
				return true;
			} else {
				return array(
					'error' => __( 'DATE is not valid.', 'wp-security-audit-log' ),
				);
			}
		} elseif ( 'TIME' == $what ) { // TIME.
			$time_array = explode( ':', $input_value );
			if ( count( $time_array ) == 2 ) {
				$p1 = intval( $time_array[0] );
				if ( $p1 < 0 || $p1 > 23 ) {
					return array(
						'error' => __( 'TIME is not valid.', 'wp-security-audit-log' ),
					);
				}
				$p2 = intval( $time_array[1] );
				if ( $p2 < 0 || $p2 > 59 ) {
					return array(
						'error' => __( 'TIME is not valid.', 'wp-security-audit-log' ),
					);
				}
				return true;
			}
			return false;
		} elseif ( 'POST ID' == $what || 'PAGE ID' == $what || 'CUSTOM POST ID' == $what ) { // POST ID, PAGE ID, CUSTOM POST ID.
			$e = sprintf( __( '%s is not valid', 'wp-security-audit-log' ), $what );
			$input_value = intval( $input_value );
			if ( ! $input_value ) {
				return array(
					'error' => $e,
				);
			}
			global $wpdb;
			$t = $this->GetPostsTableName( $wpdb );
			$result = $wpdb->get_var( sprintf( 'SELECT COUNT(ID) FROM ' . $t . ' WHERE ID = %d', $input_value ) );

			if ( $result >= 1 ) {
				return true;
			} else {
				$e = sprintf( __( '%s was not found', 'wp-security-audit-log' ), $what );
				return array(
					'error' => $e,
				);
			}
		} elseif ( 'SITE DOMAIN' == $what ) { // SITE ID.
			$e = sprintf( __( '%s is not valid', 'wp-security-audit-log' ), $what );
			if ( ! $input_value ) {
				return array(
					'error' => $e,
				);
			}
			if ( $this->wsal->IsMultisite() ) {
				global $wpdb;
				$result = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $wpdb->blogs . ' WHERE blog_id = %s', $input_value ) );
			} else {
				return array(
					'error' => __( 'The enviroment is not multisite.', 'wp-security-audit-log' ),
				);
			}
			if ( ! empty( $result ) && $result >= 1 ) {
				return true;
			} else {
				$e = sprintf( __( '%s was not found', 'wp-security-audit-log' ), $what );
				return array(
					'error' => $e,
				);
			}
		} elseif ( 'POST TYPE' == $what ) { // POST TYPE.
			$e = sprintf( __( '%s is not valid', 'wp-security-audit-log' ), $what );
			if ( '0' !== $input_value && empty( $input_value ) ) {
				return array(
					'error' => $e,
				);
			}

			if ( ! $this->wsal->IsMultisite() && ! isset( $select5->data[ $select5->selected ] ) ) {
				return array(
					'error' => __( 'Selected POST TYPE is not valid.', 'wp-security-audit-log' ),
				);
			}
		} elseif ( 'POST STATUS' == $what ) {
			$e = sprintf( __( '%s is not valid', 'wp-security-audit-log' ), $what );
			if ( '0' !== $input_value && empty( $input_value ) ) {
				return array(
					'error' => $e,
				);
			}

			if ( ! isset( $select4->data[ $select4->selected ] ) ) {
				return array(
					'error' => __( 'Selected POST STATUS is not valid.', 'wp-security-audit-log' ),
				);
			}
		}
		return true;
	}

	/**
	 * Retrieve a notification from the database.
	 *
	 * @param int $id
	 * @return mixed
	 */
	public function GetNotification( $id ) {
		$result = $this->wsal->GetNotification( $id );
		return $result;
	}

	/**
	 * Retrieve all notifications from the database.
	 *
	 * @param string $how
	 * @return mixed
	 */
	public function GetNotifications() {
		$result = $this->wsal->GetNotificationsSetting( WSAL_OPT_PREFIX );
		return $result;
	}

	/**
	 * Check to see whether or not we can add a new notification.
	 *
	 * @return bool
	 */
	public function CanAddNotification() {
		$num = $this->wsal->CountNotifications( WSAL_OPT_PREFIX );
		return $num < WSAL_MAX_NOTIFICATIONS ? true : false;
	}

	/**
	 * Get notifications disabled.
	 *
	 * @return stdClass[] notifications
	 */
	public function GetDisabledNotifications() {
		$notifications = $this->GetNotifications();

		foreach ( $notifications as $i => &$entry ) {
			$item = unserialize( $entry->option_value );

			if ( $item->status == 1 ) {
				unset( $notifications[ $i ] );
				continue;
			}
		}
		$notifications = array_values( $notifications );
		return $notifications;
	}

	/**
	 * Get notifications Not built-in.
	 *
	 * @return stdClass[] notifications
	 */
	public function GetNotBuiltInNotifications() {
		$notifications = $this->GetNotifications();

		foreach ( $notifications as $i => &$entry ) {
			$item = unserialize( $entry->option_value );

			if ( isset( $item->built_in ) ) {
				unset( $notifications[ $i ] );
				continue;
			}
		}
		$notifications = ($notifications) ? array_values( $notifications ) : null;
		return $notifications;
	}

	/**
	 * Get notifications built-in.
	 *
	 * @return stdClass[] notifications
	 */
	public function GetBuiltIn() {
		$notifications = $this->GetNotifications();
		$aBuilt_in = array();
		foreach ( $notifications as $i => &$entry ) {
			$item = unserialize( $entry->option_value );

			if ( isset( $item->built_in ) ) {
				$aBuilt_in[] = $notifications[ $i ];
			}
		}
		return $aBuilt_in;
	}

	/**
	 * Check built-in by name.
	 *
	 * @param string $name
	 * @return array|null
	 */
	public function CheckBuiltInByName( $name ) {
		$name = 'wsal-notification-built-in-' . $name;
		$aBuilt_in = $this->GetBuiltIn();
		if ( ! empty( $aBuilt_in ) ) {
			foreach ( $aBuilt_in as $element ) {
				if ( $element->option_name == $name ) {
					$item = unserialize( $element->option_value );
					$checked = array();
					foreach ( $item->triggers as $value ) {
						array_push( $checked, $value['input1'] );
					}
					return array(
						'title' => $item->title,
						'email' => $item->email,
						'checked' => $checked,
					);
				}
			}
		}
		return null;
	}

	/**
	 * Check built-in by type.
	 *
	 * @param string $type
	 * @return boolean
	 */
	public function CheckBuiltInByType( $type ) {
		$type = 'wsal-notification-built-in-' . $type;
		$aBuilt_in = $this->GetBuiltIn();
		if ( ! empty( $aBuilt_in ) ) {
			foreach ( $aBuilt_in as $element ) {
				if ( $element->option_name == $type ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Retrieve all notifications to display in the search view.
	 *
	 * @param wpdb   $wpdb
	 * @param $search
	 * @return array
	 */
	public function GetSearchResults( $search ) {
		if ( empty( $search ) ) {
			return array();
		}
		$notifications = $this->GetNotifications();
		$tmp = array();
		foreach ( $notifications as $entry ) {
			$item = unserialize( $entry->option_value );
			if ( false !== ($r = stristr( $item->title, $search )) ) {
				array_push( $tmp, $entry );
				continue;
			}
		}
		return $tmp;
	}


	/**
	 * JSON encode and display the Notification object in the Edit Notification view.
	 *
	 * @param WSAL_NP_NotificationBuilder $notif_builder
	 */
	public function CreateJsOutputEdit( WSAL_NP_NotificationBuilder $notif_builder ) {
		echo '<script type="text/javascript" id="wsalModelWp">';
		echo "var wsalModelWp = '" . json_encode( $notif_builder->get() ) . "';";
		echo '</script>';
	}


	/**
	 * Build the js script the view will use to rebuild the form in case of an error.
	 *
	 * @param $notif_builder
	 */
	public function CreateJsObjOutput( WSAL_NP_NotificationBuilder $notif_builder ) {
		echo '<script type="text/javascript" id="wsalModelWp">';
		echo "var wsalModelWp = '" . json_encode( $notif_builder->get() ) . "';";
		echo '</script>';
	}

	/**
	 * Get notifications page URL.
	 *
	 * @return string URL
	 */
	public function GetNotificationsPageUrl() {
		$class = $this->wsal->views->FindByClassName( 'WSAL_NP_Notifications' );
		if ( false === $class ) {
			$class = new WSAL_NP_Notifications( $this->wsal );
		}
		return esc_attr( $class->GetUrl() );
	}

	/**
	 * Save or update a notification into the database. This method will also validate the notification.
	 *
	 * @param WSAL_NP_NotificationBuilder $notif_builder - Instance of WSAL_NP_NotificationBuilder.
	 * @param object                      $notification - Instance of stdClass.
	 * @param bool                        $update - True for update | False for add operation.
	 * @return null|void
	 */
	public function SaveNotification( WSAL_NP_NotificationBuilder $notif_builder, $notification, $update = false ) {
		if ( ! $update ) {
			if ( ! $this->CanAddNotification() ) {
				?>
				<div class="error">
					<p><?php esc_html_e( 'Title is required.', 'wp-security-audit-log' ); ?></p>
				</div>
				<?php
				return $this->CreateJsObjOutput( $notif_builder );
			}
		}

		// Sanitize Title & Email.
		$title = trim( $notification->info->title );
		$title = str_replace( array( '\\', '/' ), '', $title );
		$title = sanitize_text_field( $title );
		$email = trim( $notification->info->email );

		// If there is Email Template.
		if ( ! empty( $notification->info->subject ) && ! empty( $notification->info->body ) ) {
			// Sanitize subject and body.
			$subject = trim( $notification->info->subject );
			$subject = str_replace( array( '\\', '/' ), '', $subject );
			$subject = sanitize_text_field( $subject );
			$body = $notification->info->body;
		}

		$notif_builder->clearTriggersErrors();

		// Validate title.
		if ( empty( $title ) ) {
			?>
			<div class="error"><p><?php esc_html_e( 'Title is required.', 'wp-security-audit-log' ); ?></p></div>
			<?php
			$notif_builder->update( 'errors', 'titleMissing', __( 'Title is required.', 'wp-security-audit-log' ) );
			return $this->CreateJsObjOutput( $notif_builder );
		} else {
			$regex_title = '/[A-Z0-9\,\.\+\-\_\?\!\@\#\$\%\^\&\*\=]/si';
			if ( ! preg_match( $regex_title, $title ) ) {
				$notif_builder->update( 'errors', 'titleMissing', __( 'Title is not valid.', 'wp-security-audit-log' ) );
				return $this->CreateJsObjOutput( $notif_builder );
			}
		}

		// Set triggers.
		$triggers = $notification->triggers;

		// Validate triggers.
		if ( empty( $triggers ) ) {
			$notif_builder->update( 'errors', 'triggersMissing', __( 'Please add at least one condition.', 'wp-security-audit-log' ) );
			return $this->CreateJsObjOutput( $notif_builder );
		}

		// ---------------------------------------------
		// Validate conditions
		// ---------------------------------------------
		$has_errors = false; // just a flag so we won't have to count notifObj->errors->triggers
		$conditions = array(); // will hold the trigger entries that will be saved into DB, so we won't have to parse the obj again
		foreach ( $triggers as $i => $entry ) {
			// flag
			$j = $i + 1; // To help us identify the right trigger in the DOM.

			// Simple obj mapping.
			$select1 = $entry->select1;
			$select2 = $entry->select2;
			$select3 = $entry->select3;
			$select4 = $entry->select4;
			$select5 = $entry->select5;
			$select6 = $entry->select6;
			$input1 = $entry->input1;

			/**
			 * PAGE ID and CUSTOM POST ID is deprecated
			 * since version 3.1.
			 *
			 * @deprecated PAGE ID, CUSTOM POST ID in select2.
			 * @since 3.1
			 */
			if ( 7 === $select2->selected || 8 === $select2->selected ) {
				$select2->selected = 6; // Assigning the value to POST ID.
			}

			// Checking if selected SITE DOMAIN(9).
			if ( 9 == $select2->selected ) {
				global $wpdb;
				$input1 = $wpdb->get_var( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE domain = %s", $input1 ) );
			}
			// Validate each trigger/condition.
			if ( $i ) {
				// Ignore the first trigger's select1 - because it's not used
				// so we start with the second one
				// make sure the provided selected index exists in the correspondent data array.
				if ( ! isset( $select1->data[ $select1->selected ] ) ) {
					$has_errors = true;
					$notif_builder->updateTriggerError( $j, __( 'The form is not valid. Please refresh the page and try again.', 'wp-security-audit-log' ) );
					continue;
				}
			}
			if ( ! isset( $select2->data[ $select2->selected ] ) ) {
				$has_errors = true;
				$notif_builder->updateTriggerError( $j, __( 'The form is not valid. Please refresh the page and try again.', 'wp-security-audit-log' ) );
				continue;
			}
			if ( ! isset( $select3->data[ $select3->selected ] ) ) {
				$has_errors = true;
				$notif_builder->updateTriggerError( $j, __( 'The form is not valid. Please refresh the page and try again.', 'wp-security-audit-log' ) );
				continue;
			}

			// Sanitize and validate input.
			$input1 = $this->ValidateInput( $input1 );
			$size = strlen( $input1 );
			if ( $size > 50 ) {
				$has_errors = true;
				$notif_builder->updateTriggerError( $j, __( "A trigger's condition must not be longer than 50 characters.", 'wp-security-audit-log' ) );
				continue;
			}

			$vm = $this->ValidateCondition( $select2, $select3, $select4, $select5, $select6, $input1 );
			if ( is_array( $vm ) ) {
				$has_errors = true;
				$notif_builder->updateTriggerError( $j, $vm['error'] );
				continue;
			}

			// Add condition.
			array_push(
				$conditions, array(
					'select1' => intval( $select1->selected ),
					'select2' => intval( $select2->selected ),
					'select3' => intval( $select3->selected ),
					'select4' => intval( $select4->selected ),
					'select5' => intval( $select5->selected ),
					'select6' => intval( $select6->selected ),
					'input1'  => strtolower( $input1 ),
				)
			);
		}

		// Validate email.
		if ( empty( $email ) ) {
			$notif_builder->update( 'errors', 'emailMissing', __( 'Email or Username is required.', 'wp-security-audit-log' ) );
			return $this->CreateJsObjOutput( $notif_builder );
		} else {
			if ( ! $this->CheckEmailOrUsername( $email ) ) {
				$notif_builder->update( 'errors', 'emailMissing', __( 'Email or Username is not valid.', 'wp-security-audit-log' ) );
				return $this->CreateJsObjOutput( $notif_builder );
			}
		}

		if ( $has_errors ) {
			return $this->CreateJsObjOutput( $notif_builder );
		} else {
			// save notification
			// Build the object that will be saved into DB.
			if ( $update ) {
				$optName = $notification->special->optName;
				// Holds the notification data that will be saved into the db.
				$data = new stdClass();
				$data->title = $notification->info->title;
				$data->email = $notification->info->email;
				$data->owner = $notification->special->owner;
				$data->dateAdded = $notification->special->dateAdded;
				$data->status = $notification->special->status;
				$data->viewState = $notification->viewState;
			} else {
				$optName = WSAL_OPT_PREFIX . $this->UniqueNumber();
				// Holds the notification data that will be saved into the db.
				$data = new stdClass();
				$data->title = $title;
				$data->email = $email;
				$data->owner = get_current_user_id();
				$data->dateAdded = time();
				$data->status = 1;
				$data->viewState = $notification->viewState;
			}

			// If there is Email Template.
			if ( ! empty( $subject ) && ! empty( $body ) ) {
				$data->subject = $subject;
				$data->body = $body;
			}

			$data->triggers = $conditions; // This will be serialized by WP.

			$result = $update ? $this->UpdateGlobalOption( $optName, $data ) : $this->AddGlobalOption( $optName, $data );
			if ( false === $result ) {
				// catchy... update_option && update_site_option will both return false if one will use them to update an option
				// with the same value(s)
				// so we need to check the last error.
				?>
				<div class="error"><p><?php esc_html_e( 'Notification could not be saved.', 'wp-security-audit-log' ); ?></p></div>
				<?php
				return $this->CreateJsObjOutput( $notif_builder );
			}
			// ALL GOOD.
			?>
			<div class="updated"><p><?php esc_html_e( 'Notification successfully saved.', 'wp-security-audit-log' ); ?></p></div>
			<?php
			// send to Notifications page.
			echo '<script type="text/javascript" id="wsalModelReset">';
			echo 'window.setTimeout(function(){location.href="' . $this->GetNotificationsPageUrl() . '";}, 700);';
			echo '</script>';
		}
		return null;
	}

	/**
	 * Email Template by name.
	 *
	 * @return array $email_body Body of the email
	 */
	public function GetEmailTemplate( $name, $force_default = false ) {
		$template = array();
		$opt_name = 'email-template-' . $name;
		$oTemplate = $this->GetOptionByName( $opt_name );
		if ( ! empty( $oTemplate ) && ! $force_default ) {
			$template = json_decode( json_encode( $oTemplate ), true );
		} else {
			$builtInSubject = ($name == 'built-in') ? __( 'Built-in Notification', 'wp-security-audit-log' ) : '';
			$default_email_subject = __( ' {title} on website {site} triggered', 'wp-security-audit-log' );
			$template['subject'] = $builtInSubject . $default_email_subject;

			$builtInBody = ($name == 'built-in') ? 'Built-in email notification' : 'Email Notification';
			$default_email_body = '<p>' . $builtInBody . __( ' <strong>{title}</strong> was triggered. Below are the notification details:', 'wp-security-audit-log' ) . '</p>';
			$default_email_body .= '<ul>';
			$default_email_body .= '<li>' . __( 'Alert ID', 'wp-security-audit-log' ) . ': {alert_id}</li>';
			$default_email_body .= '<li>' . __( 'Username', 'wp-security-audit-log' ) . ': {username}</li>';
			$default_email_body .= '<li>' . __( 'User role', 'wp-security-audit-log' ) . ': {user_role}</li>';
			$default_email_body .= '<li>' . __( 'IP address', 'wp-security-audit-log' ) . ': {source_ip}</li>';
			$default_email_body .= '<li>' . __( 'Alert Message', 'wp-security-audit-log' ) . ': {message}</li>';
			$default_email_body .= '<li>' . __( 'Alert generated on', 'wp-security-audit-log' ) . ': {date_time}</li>';
			$default_email_body .= '</ul>';
			$default_email_body .= '<p>' . __( 'Monitoring of WordPress and Email Notifications provided by <a href="http://www.wpsecurityauditlog.com">WP Security Audit Log, WordPress most comprehensive audit trail plugin</a>.', 'wp-security-audit-log' ) . '</p>';
			$template['body'] = $default_email_body;
		}

		return $template;
	}

	/**
	 * Form fields for the email template.
	 *
	 * @param array $data subject and body
	 */
	public function SpecificTemplate( $data = null ) {
		?>
		<table id="specific-template">
			<tbody class="widefat" id="email-template">
				<tr>
					<td class="left-column">
						<label for="columns"><h4><?php esc_html_e( 'Subject ', 'wp-security-audit-log' ); ?></h4></label>
					</td>
					<td>
						<fieldset>
							<input class="field" type="text" name="subject" placeholder="Subject *" value="<?php echo( ! empty( $data['subject'] ) ? $data['subject'] : null); ?>">
						</fieldset>
					</td>
				</tr>
				<tr>
					<td class="left-column">
						<label for="columns"><h4><?php esc_html_e( 'Body ', 'wp-security-audit-log' ); ?></h4></label>
						<span class="tags">HTML is accepted. Available template tags:<br>
						{title} - Notification title<br>
						{source_ip} - Client IP address<br>
						{alert_id} - The alert code<br>
						{date_time} - Alert generated on Date and time<br>
						{message} - The alert message<br>
						{username} - User login name<br>
						{user_role} - Role(s) of the user<br>
						{site} - Website name<br></span>
					</td>
					<td>
						<fieldset>
							<?php
							$content = ( ! empty( $data['body'] ) ? stripslashes( $data['body'] ) : '');
							$editor_id = 'body';
							$settings = array(
								'media_buttons' => false,
								'editor_height' => 400,
							);

							wp_editor( $content, $editor_id, $settings );
							?>
						</fieldset>
						<br>

											</td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Count login failure and update the transient.
	 *
	 * @param string $ip - IP address.
	 * @param int    $site_id - Site ID.
	 * @param string $user - WPUser object.
	 */
	public function CounterLoginFailure( $ip, $site_id, $user ) {
		// Valid 12 hours.
		$expiration = 12 * 60 * 60;

		$get_fn = $this->wsal->IsMultisite() ? 'get_site_transient' : 'get_transient';
		$set_fn = $this->wsal->IsMultisite() ? 'set_site_transient' : 'set_transient';
		if ( $user ) {
			$data_known = $get_fn( self::TRANSIENT_FAILED_COUNT );
			if ( ! $data_known ) {
				$data_known = array();
			}
			if ( ! isset( $data_known[ $site_id . ':' . $user->ID . ':' . $ip ] ) ) {
				$data_known[ $site_id . ':' . $user->ID . ':' . $ip ] = 1;
			}
			$data_known[ $site_id . ':' . $user->ID . ':' . $ip ]++;
			$set_fn( self::TRANSIENT_FAILED_COUNT, $data_known, $expiration );
		} else {
			$data_unknown = $get_fn( self::TRANSIENT_FAILED_UNKNOWN_COUNT );
			if ( ! $data_unknown ) {
				$data_unknown = array();
			}
			if ( ! isset( $data_unknown[ $site_id . ':' . $ip ] ) ) {
				$data_unknown[ $site_id . ':' . $ip ] = 1;
			}
			$data_unknown[ $site_id . ':' . $ip ]++;
			$set_fn( self::TRANSIENT_FAILED_UNKNOWN_COUNT, $data_unknown, $expiration );
		}
	}

	/**
	 * Check login failure limit.
	 *
	 * @param int    $limit - Limit for the alert.
	 * @param string $ip - IP address.
	 * @param int    $site_id - Site ID.
	 * @param string $user - WPUser object.
	 * @param bool   $exceed - True if exceeded, otherwise false.
	 * @return boolean passed limit true|false
	 */
	public function IsLoginFailureLimit( $limit, $ip, $site_id, $user, $exceed = false ) {
		$get_fn = $this->wsal->IsMultisite() ? 'get_site_transient' : 'get_transient';
		$limit = ( $limit + 1 );
		if ( $user ) {
			$data_known = $get_fn( self::TRANSIENT_FAILED_COUNT );
			if ( $exceed ) {
				return ( false !== $data_known ) && isset( $data_known[ $site_id . ':' . $user->ID . ':' . $ip ] ) && ($data_known[ $site_id . ':' . $user->ID . ':' . $ip ] > $limit);
			}
			return ( false !== $data_known ) && isset( $data_known[ $site_id . ':' . $user->ID . ':' . $ip ] ) && ($data_known[ $site_id . ':' . $user->ID . ':' . $ip ] == $limit);
		} else {
			$data_unknown = $get_fn( self::TRANSIENT_FAILED_UNKNOWN_COUNT );
			if ( $exceed ) {
				return ( false !== $data_unknown ) && isset( $data_unknown[ $site_id . ':' . $ip ] ) && ($data_unknown[ $site_id . ':' . $ip ] > $limit);
			}
			return ( false !== $data_unknown ) && isset( $data_unknown[ $site_id . ':' . $ip ] ) && ($data_unknown[ $site_id . ':' . $ip ] == $limit);
		}
	}

	/**
	 * Count 404 (Not found) and update the transient.
	 *
	 * @param int    $site_id - Site ID.
	 * @param string $username - Username.
	 * @param string $ip - IP address.
	 * @param int    $alert_code - 404 alert code.
	 */
	public function Counter404( $site_id, $username, $ip, $alert_code ) {
		// Valid 24 hours.
		$expiration = 24 * 60 * 60;
		$get_fn = $this->wsal->IsMultisite() ? 'get_site_transient' : 'get_transient';
		$set_fn = $this->wsal->IsMultisite() ? 'set_site_transient' : 'set_transient';

		if ( 6007 === $alert_code ) {
			$data = $get_fn( self::TRANSIENT_404_COUNT );
			if ( ! $data ) {
				$data = array();
			}
			if ( ! isset( $data[ $site_id . ':' . $username . ':' . $ip ] ) ) {
				$data[ $site_id . ':' . $username . ':' . $ip ] = 1;
			}
			$data[ $site_id . ':' . $username . ':' . $ip ]++;
			$set_fn( self::TRANSIENT_404_COUNT, $data, $expiration );
		} elseif ( 6023 === $alert_code ) {
			$data_visitor = $get_fn( self::TRANSIENT_404_VISITOR_COUNT );
			if ( ! $data_visitor ) {
				$data_visitor = array();
			}
			if ( ! isset( $data_visitor[ $site_id . ':' . $username . ':' . $ip ] ) ) {
				$data_visitor[ $site_id . ':' . $username . ':' . $ip ] = 1;
			}
			$data_visitor[ $site_id . ':' . $username . ':' . $ip ]++;
			$set_fn( self::TRANSIENT_404_VISITOR_COUNT, $data_visitor, $expiration );
		}
	}

	/**
	 * Check 404 (Not found) limit.
	 *
	 * @param int    $limit - Limit for the alert.
	 * @param int    $site_id - Site ID.
	 * @param string $username - Username.
	 * @param string $ip - IP address.
	 * @param bool   $exceed - True if exceeded, otherwise false.
	 * @param int    $alert_code - 404 alert code.
	 * @return boolean passed limit true|false
	 */
	public function Is404Limit( $limit, $site_id, $username, $ip, $exceed = false, $alert_code ) {
		$get_fn = $this->wsal->IsMultisite() ? 'get_site_transient' : 'get_transient';
		// More than limit.
		$limit = ($limit + 2);

		if ( 6007 === $alert_code ) {
			$data = $get_fn( self::TRANSIENT_404_COUNT );
			if ( $exceed ) {
				return ( false !== $data ) && isset( $data[ $site_id . ':' . $username . ':' . $ip ] ) && ($data[ $site_id . ':' . $username . ':' . $ip ] > $limit);
			}
			return ( false !== $data ) && isset( $data[ $site_id . ':' . $username . ':' . $ip ] ) && ($data[ $site_id . ':' . $username . ':' . $ip ] == $limit);
		} elseif ( 6023 === $alert_code ) {
			$data_visitor = $get_fn( self::TRANSIENT_404_VISITOR_COUNT );
			if ( $exceed ) {
				return ( false !== $data_visitor ) && isset( $data_visitor[ $site_id . ':' . $username . ':' . $ip ] ) && ($data_visitor[ $site_id . ':' . $username . ':' . $ip ] > $limit);
			}
			return ( false !== $data_visitor ) && isset( $data_visitor[ $site_id . ':' . $username . ':' . $ip ] ) && ($data_visitor[ $site_id . ':' . $username . ':' . $ip ] == $limit);
		}
	}

	/**
	 * Send notifications email.
	 *
	 * @return bool $result
	 */
	public function SendNotificationEmail( $email_address, $subject, $content, $alert_id ) {
		// Get email adresses even when there is the Username
		$email_address = $this->GetEmails( $email_address );
		if ( WSAL_DEBUG_NOTIFICATIONS ) {
			error_log( 'WP Security Audit Log Notification' );
			error_log( 'Email address: ' . $email_address );
			error_log( 'Alert ID: ' . $alert_id );
		}
		$headers = "MIME-Version: 1.0\r\n";

		// @see: http://codex.wordpress.org/Function_Reference/wp_mail
		add_filter( 'wp_mail_content_type', array( $this, '_set_html_content_type' ) );
		add_filter( 'wp_mail_from', array( $this, 'custom_wp_mail_from' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'custom_wp_mail_from_name' ) );

		$result = wp_mail( $email_address, $subject, $content, $headers );
		// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
		remove_filter( 'wp_mail_content_type', array( $this, '_set_html_content_type' ) );
		remove_filter( 'wp_mail_from', array( $this, 'custom_wp_mail_from' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'custom_wp_mail_from_name' ) );

		if ( WSAL_DEBUG_NOTIFICATIONS ) {
			error_log( 'Email success: ' . print_r( $result, true ) );
		}
		return $result;
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
	 * Get datetime formatted for the email.
	 *
	 * @return string $date
	 */
	public function GetEmailDatetime() {
		$date_format = $this->wsal->settings->GetDateFormat();
		$wp_time_format = get_option( 'time_format' );
		$search = array( 'a', 'T', ' ' );
		$replace = array( 'A', '', '' );
		$time_format = str_replace( $search, $replace, $wp_time_format );
		$gmt_offset_sec = $this->GetTimezone();

		$date_time_format = $date_format . ' @' . $time_format;
		$date = date( $date_time_format, microtime( true ) + $gmt_offset_sec );
		return $date;
	}

	/**
	 * Get the blog name.
	 *
	 * @return string $blogname
	 */
	public function GetBlogname() {
		if ( is_multisite() ) {
			$blog_id = (function_exists( 'get_current_blog_id' ) ? get_current_blog_id() : 0);
			$blogname = get_blog_option( $blog_id, 'blogname' );
		} else {
			$blogname = get_option( 'blogname' );
		}
		return $blogname;
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
	 * @param string $original_email_from original passed
	 * @return string from email_address
	 */
	final public function custom_wp_mail_from( $original_email_from ) {
		$email_from = $this->wsal->GetGlobalOption( 'from-email' );
		if ( ! empty( $email_from ) ) {
			return $email_from;
		} else {
			return $original_email_from;
		}
	}

	/**
	 * Return if there is a display-name in the setting or the original passed.
	 *
	 * @param string $original_email_from_name original passed
	 * @return string name
	 */
	final public function custom_wp_mail_from_name( $original_email_from_name ) {
		$email_from_name = $this->wsal->GetGlobalOption( 'display-name' );
		if ( ! empty( $email_from_name ) ) {
			return $email_from_name;
		} else {
			return $original_email_from_name;
		}
	}

	/**
	 * Validation email or username field.
	 *
	 * @return boolean
	 */
	public function CheckEmailOrUsername( $inputString ) {
		$inputString = trim( $inputString );
		$aEmailOrUsername = explode( ',', $inputString );
		foreach ( $aEmailOrUsername as $value ) {
			$value = htmlspecialchars( stripslashes( trim( $value ) ) );
			// check if e-mail address is well-formed
			if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
				$user = get_user_by( 'login', $value );
				if ( empty( $user ) ) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Get email adresses by usernames.
	 *
	 * @return String $emails comma separated email
	 */
	public function GetEmails( $inputString ) {
		$aEmails = array();
		$inputString = trim( $inputString );
		$aEmailOrUsername = explode( ',', $inputString );
		foreach ( $aEmailOrUsername as $value ) {
			$value = htmlspecialchars( stripslashes( trim( $value ) ) );
			// check if e-mail address is well-formed
			if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
				$user = get_user_by( 'login', $value );
				if ( ! empty( $user ) ) {
					array_push( $aEmails, $user->user_email );
				}
			} else {
				array_push( $aEmails, $value );
			}
		}
		return implode( ',', $aEmails );
	}
}
