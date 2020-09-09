<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('PTNWPAdmin')) :
class PTNWPAdmin {
	public $settings;
	public $siteinfo;
	public $bvinfo;
	public $bvapi;

	function __construct($settings, $siteinfo, $bvapi = null) {
		$this->settings = $settings;
		$this->siteinfo = $siteinfo;
		$this->bvapi = $bvapi;
		$this->bvinfo = new PTNInfo($this->settings);
	}

	public function mainUrl($_params = '') {
		if (function_exists('network_admin_url')) {
			return network_admin_url('admin.php?page='.$this->bvinfo->plugname.$_params);
		} else {
			return admin_url('admin.php?page='.$this->bvinfo->plugname.$_params);
		}
	}

	function removeAdminNotices() {
		if (array_key_exists('page', $_REQUEST) && $_REQUEST['page'] == $this->bvinfo->plugname) {
			remove_all_actions('admin_notices');
			remove_all_actions('all_admin_notices');
		}
	}

	public function initHandler() {
		if (!current_user_can('activate_plugins'))
			return;

		if (array_key_exists('bvnonce', $_REQUEST) &&
				wp_verify_nonce($_REQUEST['bvnonce'], "bvnonce") &&
				array_key_exists('blogvaultkey', $_REQUEST) &&
				(strlen($_REQUEST['blogvaultkey']) == 64) &&
				(array_key_exists('page', $_REQUEST) &&
				$_REQUEST['page'] == $this->bvinfo->plugname)) {
			$keys = str_split($_REQUEST['blogvaultkey'], 32);
			PTNAccount::addAccount($this->settings, $keys[0], $keys[1]);
			if (array_key_exists('redirect', $_REQUEST)) {
				$location = $_REQUEST['redirect'];
				wp_redirect($this->bvinfo->appUrl().'/migration/'.$location);
				exit();
			}
		}
		if ($this->bvinfo->isActivateRedirectSet()) {
			$this->settings->updateOption($this->bvinfo->plug_redirect, 'no');
			wp_redirect($this->mainUrl());
		}
	}

	public function menu() {
		$brand = $this->bvinfo->getBrandInfo();
		if (!$brand || (!array_key_exists('hide', $brand) && !array_key_exists('hide_from_menu', $brand))) {
			$bname = $this->bvinfo->getBrandName();
			$icon = $this->bvinfo->getBrandIcon();
			add_menu_page($bname, $bname, 'manage_options', $this->bvinfo->plugname,
					array($this, 'adminPage'), plugins_url($icon,  __FILE__ ));
		}
	}

	public function hidePluginDetails($plugin_metas, $slug) {
		$brand = $this->bvinfo->getBrandInfo();
		$bvslug = $this->bvinfo->slug;

		if ($slug === $bvslug && $brand && array_key_exists('hide_plugin_details', $brand)){
			foreach ($plugin_metas as $pluginKey => $pluginValue) {
				if (strpos($pluginValue, sprintf('>%s<', translate('View details')))) {
					unset($plugin_metas[$pluginKey]);
					break;
				}
			}
		}
		return $plugin_metas;
	}

	public function settingsLink($links, $file) {
		if ( $file == plugin_basename( dirname(__FILE__).'/pantheon.php' ) ) {
			$links[] = '<a href="'.$this->mainUrl().'">'.__( 'Settings' ).'</a>';
		}
		return $links;
	}

	public function ptnsecAdminMenu($hook) {
		if ($hook === 'toplevel_page_pantheon') {
			wp_enqueue_style( 'ptnsurface', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_style('ptnsurface');
		}
	}

	public function showErrors() {
		$error = NULL;
		if (array_key_exists('error', $_REQUEST)) {
			$error = $_REQUEST['error'];
		}
		if ($error == "email") {
			echo '<div style="padding-bottom:0.5px; color:red;"><p>Incorrect Email.</p></div>';
		}
		else if (($error == "custom") && isset($_REQUEST['bvnonce']) && wp_verify_nonce($_REQUEST['bvnonce'], "bvnonce")) {
			echo '<div style="padding-bottom:0.5px;color: red;"><p>'.base64_decode($_REQUEST['message']).'</p></div>';
		}
	}

	public function getPluginLogo() {
		$brand = $this->bvinfo->getBrandInfo();
		if ($brand && array_key_exists('logo', $brand)) {
			return $brand['logo'];
		}
		return $this->bvinfo->logo;
	}

	public function getWebPage() {
		$brand = $this->bvinfo->getBrandInfo();
		if ($brand && array_key_exists('webpage', $brand)) {
			return $brand['webpage'];
		}
		return $this->bvinfo->webpage;
	}

	public function siteInfoTags() {
		require_once dirname( __FILE__ ) . '/recover.php';
		$bvnonce = wp_create_nonce("bvnonce");
		$secret = PTNRecover::defaultSecret($this->settings);
		$public = PTNAccount::getApiPublicKey($this->settings);
		$tags = "<input type='hidden' name='url' value='".$this->siteinfo->wpurl()."'/>\n".
				"<input type='hidden' name='homeurl' value='".$this->siteinfo->homeurl()."'/>\n".
				"<input type='hidden' name='siteurl' value='".$this->siteinfo->siteurl()."'/>\n".
				"<input type='hidden' name='dbsig' value='".$this->siteinfo->dbsig(false)."'/>\n".
				"<input type='hidden' name='plug' value='".$this->bvinfo->plugname."'/>\n".
				"<input type='hidden' name='adminurl' value='".$this->mainUrl()."'/>\n".
				"<input type='hidden' name='bvversion' value='".$this->bvinfo->version."'/>\n".
	 			"<input type='hidden' name='serverip' value='".$_SERVER["SERVER_ADDR"]."'/>\n".
				"<input type='hidden' name='abspath' value='".ABSPATH."'/>\n".
				"<input type='hidden' name='secret' value='".$secret."'/>\n".
				"<input type='hidden' name='public' value='".$public."'/>\n".
				"<input type='hidden' name='bvnonce' value='".$bvnonce."'/>\n";
		return $tags;
	}

	public function activateWarning() {
		global $hook_suffix;
		if (!PTNAccount::isConfigured($this->settings) && $hook_suffix == 'index.php' ) {
?>
			<div id="message" class="updated" style="padding: 8px; font-size: 16px; background-color: #dff0d8">
						<a class="button-primary" href="<?php echo $this->mainUrl(); ?>">Activate Pantheon</a>
						&nbsp;&nbsp;&nbsp;<b>Almost Done:</b> Activate your Pantheon account to migrate your site.
			</div>
<?php
		}
	}

	public function adminPage() {
		require_once dirname( __FILE__ ) . '/admin/main_page.php';
	}

	public function initBranding($plugins) {
		$slug = $this->bvinfo->slug;

		if (!is_array($plugins) || !isset($slug, $plugins)) {
			return $plugins;
		}

		$brand = $this->bvinfo->getBrandInfo();
		if ($brand) {
			if (array_key_exists('hide', $brand)) {
				unset($plugins[$slug]);
			} else {
				if (array_key_exists('name', $brand)) {
					$plugins[$slug]['Name'] = $brand['name'];
				}
				if (array_key_exists('title', $brand)) {
					$plugins[$slug]['Title'] = $brand['title'];
				}
				if (array_key_exists('description', $brand)) {
					$plugins[$slug]['Description'] = $brand['description'];
				}
				if (array_key_exists('authoruri', $brand)) {
					$plugins[$slug]['AuthorURI'] = $brand['authoruri'];
				}
				if (array_key_exists('author', $brand)) {
					$plugins[$slug]['Author'] = $brand['author'];
				}
				if (array_key_exists('authorname', $brand)) {
					$plugins[$slug]['AuthorName'] = $brand['authorname'];
				}
				if (array_key_exists('pluginuri', $brand)) {
					$plugins[$slug]['PluginURI'] = $brand['pluginuri'];
				}
			}
		}
		return $plugins;
	}
}
endif;