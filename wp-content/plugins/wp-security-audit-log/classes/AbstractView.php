<?php
/**
 * Abstract class used in all the views.
 *
 * @see Views/*.php
 * @package Wsal
 */
abstract class WSAL_AbstractView {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var object
	 */
	protected $_plugin;

	/**
	 * WordPress version.
	 *
	 * @var string
	 */
	protected $_wpversion;

	/**
	 * Contains the result to a call to add_submenu_page().
	 *
	 * @var string
	 */
	public $hook_suffix = '';

	/**
	 * Tells us whether this view is currently being displayed or not.
	 *
	 * @var boolean
	 */
	public $is_active = false;

	/**
	 * Allowed notice names.
	 *
	 * @var array
	 */
	public static $AllowedNoticeNames = array();

	/**
	 * Method: Constructor.
	 *
	 * @param  object $plugin - Instance of WpSecurityAuditLog.
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		$this->_plugin = $plugin;

		// Get and store WordPress version.
		global $wp_version;
		if ( ! isset( $wp_version ) ) {
			$wp_version = get_bloginfo( 'version' );
		}
		$this->_wpversion = floatval( $wp_version );

		// Handle admin notices.
		add_action( 'wp_ajax_AjaxDismissNotice', array( $this, 'AjaxDismissNotice' ) );
	}

	/**
	 * Dismiss an admin notice through ajax.
	 *
	 * @internal
	 */
	public function AjaxDismissNotice() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'view' ) ) {
			die( 'Access Denied.' );
		}

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( ! isset( $post_array['notice'] ) ) {
			die( 'Notice name expected as "notice" parameter.' );
		}

		$this->DismissNotice( $post_array['notice'] );
	}

	/**
	 * Method: Check if notice is dismissed.
	 *
	 * @param string $name — Name of notice.
	 * @return boolean — Whether notice got dismissed or not.
	 */
	public function IsNoticeDismissed( $name ) {
		$user_id = get_current_user_id();
		$meta_key = 'wsal-notice-' . $name;
		self::$AllowedNoticeNames[] = $name;
		return ! ! get_user_meta( $user_id, $meta_key, true );
	}

	/**
	 * Method: Dismiss notice.
	 *
	 * @param string $name — Name of notice to dismiss.
	 */
	public function DismissNotice( $name ) {
		$user_id = get_current_user_id();
		$meta_key = 'wsal-notice-' . $name;
		$old_value = get_user_meta( $user_id, $meta_key, true );
		if ( in_array( $name, self::$AllowedNoticeNames ) || false === $old_value ) {
			update_user_meta( $user_id, $meta_key, '1' );
		}
	}

	/**
	 * Method: Register notice.
	 *
	 * @param string $name — Makes this notice available.
	 */
	public function RegisterNotice( $name ) {
		self::$AllowedNoticeNames[] = $name;
	}

	/**
	 * Method: Return page name (for menu etc).
	 *
	 * @return string
	 */
	abstract public function GetName();

	/**
	 * Method: Return page title.
	 *
	 * @return string
	 */
	abstract public function GetTitle();

	/**
	 * Method: Page icon name.
	 *
	 * @return string
	 */
	abstract public function GetIcon();

	/**
	 * Method: Menu weight, the higher this is, the lower it goes.
	 *
	 * @return int
	 */
	abstract public function GetWeight();

	/**
	 * Renders and outputs the view directly.
	 */
	abstract public function Render();

	/**
	 * Renders the view icon (this has been deprecated in newwer WP versions).
	 */
	public function RenderIcon() {
		?>
		<div id="icon-plugins" class="icon32"><br></div>
		<?php
	}

	/**
	 * Renders the view title.
	 */
	public function RenderTitle() {
		?>
		<h2><?php echo esc_html( $this->GetTitle() ); ?></h2>
		<?php
	}

	/**
	 * Method: Render content of the view.
	 *
	 * @link self::Render()
	 */
	public function RenderContent() {
		$this->Render();
	}

	/**
	 * Method: Whether page should appear in menu or not.
	 *
	 * @return boolean
	 */
	public function IsVisible() {
		return true;
	}

	/**
	 * Method: Whether page should be accessible or not.
	 *
	 * @return boolean
	 */
	public function IsAccessible() {
		return true;
	}

	/**
	 * Used for rendering stuff into head tag.
	 */
	public function Header() {
	}

	/**
	 * Used for rendering stuff in page fotoer.
	 */
	public function Footer() {
	}

	/**
	 * Method: Safe view menu name.
	 *
	 * @return string
	 */
	public function GetSafeViewName() {
		return 'wsal-' . preg_replace( '/[^A-Za-z0-9\-]/', '-', $this->GetViewName() );
	}

	/**
	 * Override this and make it return true to create a shortcut link in plugin page to the view.
	 *
	 * @return boolean
	 */
	public function HasPluginShortcutLink() {
		return false;
	}

	/**
	 * Method: URL to backend page for displaying view.
	 *
	 * @return string
	 */
	public function GetUrl() {
		$fn = function_exists( 'network_admin_url' ) ? 'network_admin_url' : 'admin_url';
		return $fn( 'admin.php?page=' . $this->GetSafeViewName() );
	}

	/**
	 * Method: Generates view name out of class name.
	 *
	 * @return string
	 */
	public function GetViewName() {
		return strtolower( str_replace( array( 'WSAL_Views_', 'WSAL_' ), '', get_class( $this ) ) );
	}
}
