<?php
/**
 * View Manager.
 *
 * This Class load all the views, initialize them and shows the active one.
 * It creates also the menu items.
 *
 * @package Wsal
 */
class WSAL_ViewManager {

	/**
	 * Array of views.
	 *
	 * @var WSAL_AbstractView[]
	 */
	public $views = array();

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var object
	 */
	protected $_plugin;

	/**
	 * Active view.
	 *
	 * @var WSAL_AbstractView|null
	 */
	protected $_active_view = false;

	/**
	 * Method: Constructor.
	 *
	 * @param  WpSecurityAuditLog $plugin - Instance of WpSecurityAuditLog.
	 * @author Ashar Irfan
	 * @since  1.0.0
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		$this->_plugin = $plugin;

		$skip_views = array();

		// Array of views to skip for premium version.
		if ( wsal_freemius()->is_plan__premium_only( 'starter' ) ) {
			$skip_views[] = dirname( __FILE__ ) . '/Views/EmailNotifications.php';
			$skip_views[] = dirname( __FILE__ ) . '/Views/ExternalDB.php';
			$skip_views[] = dirname( __FILE__ ) . '/Views/Licensing.php';
			$skip_views[] = dirname( __FILE__ ) . '/Views/LogInUsers.php';
			$skip_views[] = dirname( __FILE__ ) . '/Views/Reports.php';
			$skip_views[] = dirname( __FILE__ ) . '/Views/Search.php';
		}

		// Load views.
		foreach ( glob( dirname( __FILE__ ) . '/Views/*.php' ) as $file ) {
			if ( empty( $skip_views ) || ! in_array( $file, $skip_views ) ) {
				$this->AddFromFile( $file );
			}
		}

		// Add menus.
		add_action( 'admin_menu', array( $this, 'AddAdminMenus' ) );
		add_action( 'network_admin_menu', array( $this, 'AddAdminMenus' ) );

		// Add plugin shortcut links.
		add_filter( 'plugin_action_links_' . $plugin->GetBaseName(), array( $this, 'AddPluginShortcuts' ) );

		// Render header.
		add_action( 'admin_enqueue_scripts', array( $this, 'RenderViewHeader' ) );

		// Render footer.
		add_action( 'admin_footer', array( $this, 'RenderViewFooter' ) );
	}

	/**
	 * Add new view from file inside autoloader path.
	 *
	 * @param string $file Path to file.
	 */
	public function AddFromFile( $file ) {
		$this->AddFromClass( $this->_plugin->GetClassFileClassName( $file ) );
	}

	/**
	 * Add new view given class name.
	 *
	 * @param string $class Class name.
	 */
	public function AddFromClass( $class ) {
		$this->AddInstance( new $class( $this->_plugin ) );
	}

	/**
	 * Add newly created view to list.
	 *
	 * @param WSAL_AbstractView $view The new view.
	 */
	public function AddInstance( WSAL_AbstractView $view ) {
		$this->views[] = $view;
	}

	/**
	 * Order views by their declared weight.
	 */
	public function ReorderViews() {
		usort( $this->views, array( $this, 'OrderByWeight' ) );
	}

	/**
	 * Get page order by its weight.
	 *
	 * @internal This has to be public for PHP to call it.
	 * @param WSAL_AbstractView $a - First view.
	 * @param WSAL_AbstractView $b - Second view.
	 * @return int
	 */
	public function OrderByWeight( WSAL_AbstractView $a, WSAL_AbstractView $b ) {
		$wa = $a->GetWeight();
		$wb = $b->GetWeight();
		switch ( true ) {
			case $wa < $wb:
				return -1;
			case $wa > $wb:
				return 1;
			default:
				return 0;
		}
	}

	/**
	 * WordPress Action
	 */
	public function AddAdminMenus() {
		$this->ReorderViews();

		if ( $this->_plugin->settings->CurrentUserCan( 'view' ) && count( $this->views ) ) {
			// Add main menu.
			$this->views[0]->hook_suffix = add_menu_page(
				'WP Security Audit Log',
				'Audit Log',
				'read', // No capability requirement.
				$this->views[0]->GetSafeViewName(),
				array( $this, 'RenderViewBody' ),
				$this->views[0]->GetIcon(),
				'2.5' // Right after dashboard.
			);

			// Add menu items.
			foreach ( $this->views as $view ) {
				if ( $view->IsAccessible() ) {
					if ( $this->GetClassNameByView( $view->GetSafeViewName() ) ) {
						continue;
					}

					if ( ( 'wsal-togglealerts' === $view->GetSafeViewName()
							|| 'wsal-settings' === $view->GetSafeViewName()
							|| 'wsal-ext-settings' === $view->GetSafeViewName()
						)
						&& ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
						continue;
					}

					$view->hook_suffix = add_submenu_page(
						$view->IsVisible() ? $this->views[0]->GetSafeViewName() : null,
						$view->GetTitle(),
						$view->GetName(),
						'read', // No capability requirement.
						$view->GetSafeViewName(),
						array( $this, 'RenderViewBody' ),
						$view->GetIcon()
					);
				}
			}
		}
	}

	/**
	 * WordPress Filter
	 *
	 * @param array $old_links - Array of old links.
	 */
	public function AddPluginShortcuts( $old_links ) {
		$this->ReorderViews();

		$new_links = array();
		foreach ( $this->views as $view ) {
			if ( $view->HasPluginShortcutLink() ) {
				$new_links[] =
					'<a href="'
						. admin_url( 'admin.php?page=' . $view->GetSafeViewName() )
						. '">'
						. $view->GetName()
					. '</a>';
			}
		}
		return array_merge( $new_links, $old_links );
	}

	/**
	 * Returns page id of current page (or false on error).
	 *
	 * @return int
	 */
	protected function GetBackendPageIndex() {
		// Get current view via $_GET array.
		$current_view = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

		if ( isset( $current_view ) ) {
			foreach ( $this->views as $i => $view ) {
				if ( $current_view === $view->GetSafeViewName() ) {
					return $i;
				}
			}
		}
		return false;
	}

	/**
	 * Returns the current active view or null if none.
	 *
	 * @return WSAL_AbstractView|null
	 */
	public function GetActiveView() {
		if ( false === $this->_active_view ) {
			$this->_active_view = null;

			// Get current view via $_GET array.
			$current_view = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

			if ( isset( $current_view ) ) {
				foreach ( $this->views as $view ) {
					if ( $current_view === $view->GetSafeViewName() ) {
						$this->_active_view = $view;
					}
				}
			}

			if ( $this->_active_view ) {
				$this->_active_view->is_active = true;
			}
		}
		return $this->_active_view;
	}

	/**
	 * Render header of the current view.
	 */
	public function RenderViewHeader() {
		if ( ! ! ($view = $this->GetActiveView()) ) {
			$view->Header();
		}
	}

	/**
	 * Render footer of the current view.
	 */
	public function RenderViewFooter() {
		if ( ! ! ($view = $this->GetActiveView()) ) {
			$view->Footer();
		}
	}

	/**
	 * Render content of the current view.
	 */
	public function RenderViewBody() {
		$view = $this->GetActiveView();
			?>
			<div class="wrap">
				<?php
					$view->RenderIcon();
					$view->RenderTitle();
					$view->RenderContent();
				?>
			</div>
		<?php
	}

	/**
	 * Returns view instance corresponding to its class name.
	 *
	 * @param string $class_name View class name.
	 * @return WSAL_AbstractView The view or false on failure.
	 */
	public function FindByClassName( $class_name ) {
		foreach ( $this->views as $view ) {
			if ( $view instanceof $class_name ) {
				return $view;
			}
		}
		return false;
	}

	/**
	 * Method: Returns class name of the view using view name.
	 *
	 * @param  string $view_slug - Slug of view.
	 * @since  1.0.0
	 */
	private function GetClassNameByView( $view_slug ) {
		$not_show = false;
		switch ( $view_slug ) {
			case 'wsal-emailnotifications':
				if ( class_exists( 'WSAL_NP_Plugin' ) ) {
					$not_show = true;
				}
				break;
			case 'wsal-loginusers':
				if ( class_exists( 'WSAL_User_Management_Plugin' ) ) {
					$not_show = true;
				}
				break;
			case 'wsal-reports':
				if ( class_exists( 'WSAL_Rep_Plugin' ) ) {
					$not_show = true;
				}
				break;
			case 'wsal-search':
				if ( class_exists( 'WSAL_SearchExtension' ) ) {
					$not_show = true;
				}
				break;
			case 'wsal-externaldb':
				if ( class_exists( 'WSAL_Ext_Plugin' ) ) {
					$not_show = true;
				}
				break;
			default:
				break;
		}
		return $not_show;
	}
}
