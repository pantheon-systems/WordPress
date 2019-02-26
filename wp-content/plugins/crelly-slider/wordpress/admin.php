<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CrellySliderAdmin {

	private static $mceSettings = array();

	// Creates the menu and the admin panel
	public static function showSettings() {
		add_action('admin_menu', 'CrellySliderAdmin::pluginMenus');
	}

	public static function pluginMenus() {
		add_menu_page('Crelly Slider', 'Crelly Slider', 'manage_options', 'crellyslider', 'CrellySliderAdmin::displayPage', 'div');
	}

	// Go to the correct page
	public static function displayPage() {
		if(!isset($_GET['view'])) {
			$index = 'home';
		}
		else {
			$index = esc_sql($_GET['view']);
			if($index != 'add' && $index != 'edit') {
				die();
			}
		}

		global $wpdb;

		// Check what the user is doing: is it adding or modifying a slider?
		$edit = true;
		$id = NULL;
		if(isset($_GET['view']) && esc_sql($_GET['view']) == 'add') {
			$edit = false;
			$id = NULL;
		}
		else if(isset($_GET['view']) && esc_sql($_GET['view']) == 'edit') {
			$edit = true;
			$id = isset($_GET['id']) ? (int)$_GET['id'] : NULL;
			if($id == NULL || ($id != NULL && !CrellySliderCommon::sliderExists($id))) {
				die();
			}

			$slider = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'crellyslider_sliders WHERE id = %d', $id));
		}

		?>
		<div
		class="wrap cs-admin"
		<?php if($edit && isset($slider)): ?>
			style="width: <?php echo esc_attr($slider->startWidth); ?>px;"
		<?php else: ?>
			style="width: 1140px;"
		<?php endif; ?>
		>

			<noscript class="cs-no-js">
				<div class="cs-message cs-message-error" style="display: block;"><?php _e('JavaScript must be enabled to view this page correctly.', 'crelly-slider'); ?></div>
			</noscript>

			<div class="cs-message cs-message-ok" style="display: none;"><?php _e('Operation completed successfully.', 'crelly-slider'); ?></div>
			<div class="cs-message cs-message-error" style="display: none;"><?php _e('Something went wrong.', 'crelly-slider'); ?></div>
			<div class="cs-message cs-message-wait" style="display: none;"><?php _e('Please wait', 'crelly-slider'); ?></div>
			<?php if(! $edit): ?>
				<div class="cs-message cs-message-warning"><?php _e('When you\'ll click "Save Settings", you\'ll be able to add slides and elements.', 'crelly-slider'); ?></div>
			<?php endif; ?>

			<h2 class="cs-logo" title="Crelly Slider">
				<a href="?page=crellyslider">
					<img src="<?php echo CS_PLUGIN_URL . '/wordpress/images/logo2.png' ?>" alt="Crelly Slider" />
				</a>
			</h2>

			<br />
			<br />

			<?php

			switch($index) {
				case 'home':
					self::displayHome();
				break;

				case 'add':
				case 'edit':
					self::displaySlider();
				break;
			}

			?>

		</div>
		<?php
	}

	// Displays the main plugin page
	public static function displayHome() {
		?>
		<div class="cs-home">
			<?php require_once CS_PATH . 'wordpress/home.php'; ?>
		</div>
		<?php
	}

	// Displays the slider page in wich you can add or modify sliders, slides and elements
	public static function displaySlider() {
		global $wpdb;

		$edit = true;
		$id = NULL;
		if(isset($_GET['view']) && esc_sql($_GET['view']) == 'add') {
			$edit = false;
			$id = NULL;
		}
		else if(isset($_GET['view']) && esc_sql($_GET['view']) == 'edit') {
			$edit = true;
			$id = isset($_GET['id']) ? (int)$_GET['id'] : NULL;
			if($id == NULL || ($id != NULL && !CrellySliderCommon::sliderExists($id))) {
				die();
			}

			$slider = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'crellyslider_sliders WHERE id = %d', $id));
			$slides = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'crellyslider_slides WHERE slider_parent = %d ORDER BY position', $id));
			// The elements variable are updated in the foreach() loop directly in the "slides.php" file
		}
		?>

		<div class="cs-useless-wp_editor" style="display: none;">
			<?php
			// I need to call wp_editor just to include TinyMCE's javascript
			wp_editor('', 'cs-element-editor-useless');
			self::setTinyMCEOptions();
			?>
		</div>

		<div class="cs-slider <?php echo $edit ? 'cs-edit-slider' : 'cs-add-slider' ?>">
			<div class="cs-tabs cs-tabs-fade cs-tabs-switch-interface">
				<?php if($edit): ?>
					<ul>

						<li>
							<span class="cs-icon icon-settings"></span>
							<a id="cs-show-slider-settings"><?php _e('Slider Settings', 'crelly-slider'); ?></a>
						</li>
						<li>
							<span class="cs-icon icon-edit"></span>
							<a id="cs-show-slides"><?php _e('Edit Slides', 'crelly-slider'); ?></a>
						</li>
					</ul>

					<br />
					<br />
					<br />
				<?php endif; ?>

				<?php require_once CS_PATH . 'wordpress/slider.php'; ?>
				<?php
				if($edit) {
					require_once CS_PATH . 'wordpress/elements.php';
					require_once CS_PATH . 'wordpress/slides.php';
				}
				?>
			</div>

			<br />

			<a class="cs-button cs-is-primary cs-save-settings" data-id="<?php echo $id; ?>" href="#"><?php _e('Save Settings', 'crelly-slider'); ?></a>

		</div>

		<?php
	}

	// Avoid incompatibility issues
	public static function isAdminJs() {
		?>
		<script type="text/javascript">
			var crellyslider_is_wordpress_admin = true;
			var crellyslider_locale = '<?php echo get_locale(); ?>';
		</script>
		<?php
	}

	public static function setIsAdminJs() {
		add_action('admin_enqueue_scripts', 'CrellySliderAdmin::isAdminJs');
	}

	// Include CSS and JavaScript
	public static function enqueues() {
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-datepicker');		
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_media();

		wp_register_script('datetimepicker', CS_PLUGIN_URL . '/wordpress/js/jquery.datetimepicker.js', array('jquery'), "2.5.17", true);
		wp_enqueue_script('datetimepicker');
		wp_enqueue_style('datetimepicker', CS_PLUGIN_URL . '/wordpress/css/jquery.datetimepicker.css', array(), "2.5.17");

		add_action('admin_print_footer_scripts', array( __CLASS__, 'printTinyMCEOptions'), 1);
		wp_register_script('crellyslider-admin', CS_PLUGIN_URL . '/wordpress/js/admin.js', array('wp-color-picker', 'datetimepicker'), CS_VERSION, true);

		self::localization();

		wp_enqueue_style('crellyslider-admin', CS_PLUGIN_URL . '/wordpress/css/admin.css', array(), CS_VERSION);
		wp_enqueue_script('crellyslider-admin');

		$wp_version = get_bloginfo('version');
		$menu_icon_url = CS_PLUGIN_URL . '/wordpress/images/menu-icon.png';
		if($wp_version < 3.8) {
			?>
			<style type="text/css">
				#adminmenu .toplevel_page_crellyslider div.wp-menu-image {
					background-image: url('<?php echo $menu_icon_url; ?>');
					background-repeat: no-repeat;
					background-position: -20px center;
				}

				#adminmenu .toplevel_page_crellyslider:hover div.wp-menu-image {
					background-position: -20px center;
				}

				#adminmenu .toplevel_page_crellyslider.current div.wp-menu-image {
					background-position: 8px center;
				}

				#adminmenu .current.toplevel_page_crellyslider:hover div.wp-menu-image {
					background-position: 8px center;
				}
			</style>
			<?php
		}
		else {
			?>
			<style type="text/css">
				#adminmenu .toplevel_page_crellyslider div.wp-menu-image {
					background-image: url('<?php echo $menu_icon_url; ?>');
					background-repeat: no-repeat;
					background-position: 8px center;
					opacity: .6;
					filter: alpha(opacity=60);
				}

				#adminmenu .toplevel_page_crellyslider:hover div.wp-menu-image {
					background-position: -20px center;
					opacity: 1;
					filter: alpha(opacity=100);
				}

				#adminmenu .toplevel_page_crellyslider.current div.wp-menu-image {
					opacity: 1;
					filter: alpha(opacity=100);
				}

				#adminmenu .current.toplevel_page_crellyslider:hover div.wp-menu-image {
					background-position: 8px center;
					opacity: 1;
					filter: alpha(opacity=100);
				}
			</style>
			<?php
		}
	}

	public static function setEnqueues() {
		add_action('admin_enqueue_scripts', 'CrellySliderAdmin::enqueues');
	}

	public static function localization() {
		// Here the translations for the admin.js file
		$crellyslider_translations = array(
			'slide' => __('Slide', 'crelly-slider'),
			'slide_delete_confirm' => __('The slide will be deleted. Are you sure?', 'crelly-slider'),
			'slide_delete_just_one' => __('You can\'t delete this. You must have at least one slide.', 'crelly-slider'),
			'slider_delete_confirm' => __('The slider will be deleted. Are you sure?', 'crelly-slider'),
			'text_element_default_html' => __('Text element', 'crelly-slider'),
			'slide_live_preview' => __('Live preview', 'crelly-slider'),
			'slide_stop_preview' => __('Stop preview', 'crelly-slider'),
			'duplicate_slide' => __('Duplicate slide', 'crelly-slider'),
			'remove_slide' => __('Delete slide', 'crelly-slider'),
			'exit_without_saving' => __('All unsaved changes will be lost. Are you sure you want to leave this page?', 'crelly-slider'),
			'switch_editor' => __('Switch editor', 'crelly-slider'),
		);
		wp_localize_script('crellyslider-admin', 'crellyslider_translations', $crellyslider_translations);
	}

	/**
	 * Invokes the filter for the tinyMCE init options
	 */
	public static function setTinyMCEOptions() {
		self::$mceSettings = apply_filters( 'crellyslider_tiny_mce_before_init', self::tinyMCEDefaultOptions());
	}

	/**
	 * Generates the default tinyMCE options
	 *
	 * @return array Default options for the tinyMCE editor
	 */
	private static function tinyMCEDefaultOptions() {
		$fontSizes = '';
		for($i = 1; $i <= 200; $i++) {
			$fontSizes .= $i . 'px ';
		}
		$fontSizes = trim($fontSizes);

		$language = explode('_', get_locale());
		if($language == false || count($language) == 0) {
			$language = 'en';
		}
		else {
			$language = $language[0];
		}
		
		return array(
			'toolbar1' => 'bold,italic,strikethrough,alignleft,aligncenter,alignright,link,unlink,underline,forecolor,backcolor',
			'toolbar2' => 'fontselect,fontsizeselect',
			'toolbar3' => '',
			'toolbar4' => '',
			'height' => 200,
			'forced_root_block' => false,
			'wpautop' => false,
			'fontsize_formats' => $fontSizes,
			'wp_lang_attr' => get_locale(),
			'content_css' => '',
			'cache_suffix' => '',
			'language' => $language,
			'theme' => 'modern',
			'skin' => 'lightgray',
			'formats' => '{' .
				'alignleft: [' .
					'{'.
						'selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", ' .
						'styles: {textAlign:"left", lineHeight:"inherit"}, ' .
						'inline: "div"'.
					'},' .
					'{' .
						'selector: "img,table,dl.wp-caption", ' .
						'classes: "alignleft"' .
					'}' .
				'],' .
				'aligncenter: [' .
					'{' .
						'selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", ' .
						'styles: {textAlign:"center", lineHeight:"inherit"}, ' .
						'inline: "div"' .
					'},' .
					'{' .
						'selector: "img,table,dl.wp-caption", ' .
						'classes: "aligncenter"' .
					'}' .
				'],' .
				'alignright: [' .
					'{' .
						'selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", ' .
						'styles: {textAlign:"right", lineHeight:"inherit"}, ' .
						'inline: "div"' .
					'},' .
					'{' .
						'selector: "img,table,dl.wp-caption", ' .
						'classes: "alignright"' .
					'}' .
				'],' .
				'strikethrough: {inline: "del"}' .
			'}',
			'relative_urls' => false,
			'remove_script_host' => false,
			'convert_urls' => false,
			'browser_spellcheck' => true,
			'fix_list_elements' => true,
			'entities' => '38,amp,60,lt,62,gt',
			'entity_encoding' => 'raw',
			'keep_styles' => false,
			'preview_styles' => 'font-family font-size font-weight font-style text-decoration text-transform',
			'end_container_on_empty_block' => true,
			'wpeditimage_disable_captions' => false,
			'wpeditimage_html5_captions' => true,
			'plugins' => 'charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview,image',
			'resize' => 'vertical',
			'menubar' => false,
			'indent' => true,
			'tabfocus_elements' => ':prev,:next',
		);
	}

	/**
	 * Outputs the previously set tinyMCE options (init Options)
	 *
	 * A <script> tag containing the global variable crellyslider_tinyMCEInit is printed
	 */
	public static function printTinyMCEOptions() {
		$options = self::$mceSettings;

		$serialized = '';
		foreach($options as $key => $value) {
			if (is_bool($value)) {
				$serialized .= $key . ':' . ($value ? 'true' : 'false') . ',';
			} else {
				$length = !empty($value) && is_string($value) ? strlen($value) : 0;

				// don't wrap objects, arrays or functions in quotes
				if ($length > 0
					&& (('{' == $value{0} && '}' == $value{$length - 1}) ||
						('[' == $value{0} && ']' == $value{$length - 1}) ||
						preg_match('/^\(?function ?\(/', $value))) {

					$serialized .= $key . ':' . $value . ',';
				} else {
					$serialized .= $key . ':"' . $value . '",';
				}
			}
		}

		// remove trailing spaces and commas and enclose in JSON { }
		$serialized = '{' . trim($serialized, ' ,') . '}';
		?>
		<script type="text/javascript">
			crellyslider_tinyMCEInit = <?php echo $serialized; ?>;
		</script>
		<?php
	}
}

?>
