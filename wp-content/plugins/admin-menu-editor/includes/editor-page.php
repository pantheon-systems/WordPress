<?php
/**
 * @var array $editor_data Various pieces of data passed by the plugin.
 */
$current_user = wp_get_current_user();
$images_url = $editor_data['images_url'];
$is_pro_version = apply_filters('admin_menu_editor_is_pro', false);
$is_second_toolbar_visible = isset($_COOKIE['ame-show-second-toolbar']) && (intval($_COOKIE['ame-show-second-toolbar']) === 1);
$is_compact_layout_enabled = isset($_COOKIE['ame-compact-layout']) && (intval($_COOKIE['ame-compact-layout']) === 1);
$is_multisite = is_multisite();

$icons = array(
	'cut' => '/gnome-icon-theme/edit-cut-blue.png',
	'copy' => '/gion/edit-copy.png',
	'paste' => '/gnome-icon-theme/edit-paste.png',
	'hide'  =>  '/page-invisible.png',
	'hide-and-deny'  =>  '/font-awesome/eye-slash-color.png',
	'new' => '/page-add.png',
	'delete' => '/page-delete.png',
	'new-separator' => '/separator-add.png',
	'toggle-all' => '/check-all.png',
	'copy-permissions' => '/copy-permissions.png',
	'toggle-toolbar' => '/font-awesome/angle-double-down.png',
	'sort-ascending' => '/sort_ascending.png',
	'sort-descending' => '/sort_descending.png',
);
foreach($icons as $name => $url) {
	$icons[$name] = $images_url . $url;
}

$hide_button_extra_tooltip = 'When "All" is selected, this will hide the menu from everyone except the current user'
	. ($is_multisite ? ' and Super Admin' : '') . '.';

//Output the "Upgrade to Pro" message
if ( !apply_filters('admin_menu_editor_is_pro', false) ){
	//Pseudo-randomly decide whether to show the VAC link.
	$is_vac_link_visible = (hexdec( substr(md5(get_site_url() . 'vc3'), -3) ) % 100) <= 20; //20% of sites will see it.
	?>
	<script type="text/javascript">
	(function($){
		var screenLinks = $('#screen-meta-links'),
			showVacLink = (<?php echo $is_vac_link_visible ? 'true' : 'false' ?>);

		if (showVacLink) {
			screenLinks.append(
				$('<div>', {
					'class' : 'custom-screen-meta-link-wrap',
					'id'    : 'ws-visual-admin-customizer-ad'
				}).append($('<a>', {
					'href'  : 'https://wordpress.org/plugins/visual-admin-customizer/',
					'class' : 'show-settings custom-screen-meta-link',
					'title' : 'A free plugin for customizing the WordPress admin interface',
					'target': '_blank',
					'text'  : 'Visual Admin Customizer'
				}))
			);
		}

		screenLinks.append(
			'<div id="ws-pro-version-notice" class="custom-screen-meta-link-wrap">' +
				'<a href="http://adminmenueditor.com/upgrade-to-pro/?utm_source=Admin%2BMenu%2BEditor%2Bfree&utm_medium=text_link&utm_content=top_upgrade_link&utm_campaign=Plugins" id="ws-pro-version-notice-link" class="show-settings custom-screen-meta-link" target="_blank" title="View Pro version details">Upgrade to Pro</a>' +
			'</div>'
		);
	})(jQuery);
	</script>
	<?php
}

?>

<?php do_action('admin_menu_editor-display_header'); ?>

<?php
if ( !empty($_GET['message']) ){
	if ( intval($_GET['message']) == 2 ) {
		echo '<div id="message" class="error"><p><strong>Failed to decode input! The menu wasn\'t modified.</strong></p></div>';
	}
}

include dirname(__FILE__) . '/../modules/access-editor/access-editor-template.php';
$extrasDirectory = dirname(__FILE__) . '/../extras';
if ( $is_pro_version ) {
	include $extrasDirectory . '/menu-color-dialog.php';
	include $extrasDirectory . '/copy-permissions-dialog.php';
}

function ame_output_sort_buttons($icons) {
	?>
	<a class='ws_button ws_sort_menus_button' data-sort-direction="asc" href='javascript:void(0)' title='Sort ascending'>
		<img src='<?php echo $icons['sort-ascending']; ?>' alt="Sort ascending" />
	</a>
	<a class='ws_button ws_sort_menus_button' data-sort-direction="desc" href='javascript:void(0)' title='Sort descending'>
		<img src='<?php echo $icons['sort-descending']; ?>' alt="Sort descending" />
	</a>
	<?php
}

?>

<div id='ws_menu_editor' style="visibility: hidden;" class="<?php
	if ( $is_compact_layout_enabled ) {
		echo 'ws_compact_layout';
	} else {
		echo 'ws_large_layout';
	}
?>">

	<?php include dirname(__FILE__) . '/../modules/actor-selector/actor-selector-template.php'; ?>

    <div>

	<div class='ws_main_container'>
		<div class='ws_toolbar'>
			<div class="ws_button_container">
				<a id='ws_cut_menu' class='ws_button' href='javascript:void(0)' title='Cut'><img src='<?php echo $icons['cut']; ?>' alt="Cut" /></a>
				<a id='ws_copy_menu' class='ws_button' href='javascript:void(0)' title='Copy'><img src='<?php echo $icons['copy']; ?>' alt="Copy" /></a>
				<a id='ws_paste_menu' class='ws_button' href='javascript:void(0)' title='Paste'><img src='<?php echo $icons['paste']; ?>' alt="Paste" /></a>

				<div class="ws_separator">&nbsp;</div>

				<a id='ws_new_menu' class='ws_button' href='javascript:void(0)' title='New menu'><img src='<?php echo $icons['new']; ?>' alt="New menu" /></a>
				<a id='ws_new_separator' class='ws_button' href='javascript:void(0)' title='New separator'><img src='<?php echo $icons['new-separator']; ?>' alt="New separator" /></a>

				<?php if ( $is_pro_version ): ?>
					<div class="ws_separator">&nbsp;</div>

					<a id='ws_hide_and_deny_menu' class='ws_button ws_hide_and_deny_button' href='javascript:void(0)'
					   title='Hide and prevent access. &#10;<?php echo esc_attr($hide_button_extra_tooltip); ?>'>
						<img src='<?php echo $icons['hide-and-deny']; ?>' alt="Hide" />
					</a>
				<?php endif; ?>

				<?php if ( $editor_data['show_deprecated_hide_button'] ): ?>
					<a id='ws_hide_menu' class='ws_button' href='javascript:void(0)' title='Hide without preventing access (cosmetic)'><img src='<?php echo $icons['hide']; ?>' alt="Hide (cosmetic)" /></a>
				<?php endif; ?>

				<a id='ws_delete_menu' class='ws_button ws_delete_menu_button' href='javascript:void(0)' title='Delete menu'><img src='<?php echo $icons['delete']; ?>' alt="Delete menu" /></a>

				<div class="ws_separator">&nbsp;</div>

				<?php
				if ( !$is_pro_version ) {
					ame_output_sort_buttons($icons);
				}
				?>

				<?php if ( $is_pro_version ): ?>
					<a id='ws_toggle_toolbar' class='ws_button' href='javascript:void(0)' title='Toggle second toolbar'>
						<img src='<?php echo $icons['toggle-toolbar']; ?>' alt="Toolbar toggle" />
					</a>
				<?php endif; ?>

				<div class="clear"></div>
			</div>

			<div class="ws_button_container ws_second_toolbar_row <?php
					if (!$is_second_toolbar_visible) { echo ' hidden'; }
				?>">

				<?php
				if ( $is_pro_version ) {
					ame_output_sort_buttons($icons);
				}
				?>

				<?php  if ( $is_pro_version ): ?>
					<div class="ws_separator">&nbsp;</div>

					<a id='ws_toggle_all_menus' class='ws_button' href='javascript:void(0)'
					   title='Toggle all menus for the selected role'><img src='<?php echo $icons['toggle-all']; ?>' alt="Toggle all" /></a>

					<a id='ws_copy_role_permissions' class='ws_button' href='javascript:void(0)'
					   title='Copy all menu permissions from one role to another'><img src='<?php echo $icons['copy-permissions']; ?>' alt="Copy permissions" /></a>

					<div class="ws_separator">&nbsp;</div>
				<?php endif; ?>

				<div class="clear"></div>
			</div>
		</div>

		<div id='ws_menu_box' class="ws_box">
		</div>

		<?php do_action('admin_menu_editor-container', 'menu'); ?>
	</div>

	<div class='ws_main_container'>
		<div class='ws_toolbar'>
			<div class="ws_button_container">
				<a id='ws_cut_item' class='ws_button' href='javascript:void(0)' title='Cut'><img src='<?php echo $icons['cut']; ?>' alt="Cut" /></a>
				<a id='ws_copy_item' class='ws_button' href='javascript:void(0)' title='Copy'><img src='<?php echo $icons['copy']; ?>' alt="Copy" /></a>
				<a id='ws_paste_item' class='ws_button' href='javascript:void(0)' title='Paste'><img src='<?php echo $icons['paste']; ?>' alt="Paste" /></a>

				<div class="ws_separator">&nbsp;</div>

				<a id='ws_new_item' class='ws_button' href='javascript:void(0)' title='New menu item'><img src='<?php echo $icons['new']; ?>' alt="New menu item" /></a>
				<?php if ( $is_pro_version ): ?>
					<a id='ws_new_submenu_separator' class='ws_button' href='javascript:void(0)' title='New separator'><img src='<?php echo $icons['new-separator']; ?>' alt="New separator" /></a>
				<?php endif; ?>

				<?php if ( $is_pro_version ): ?>
					<div class="ws_separator">&nbsp;</div>

					<a id='ws_hide_and_deny_item' class='ws_button ws_hide_and_deny_button' href='javascript:void(0)'
					   title='Hide and prevent access. &#10;<?php echo esc_attr($hide_button_extra_tooltip); ?>'>
						<img src='<?php echo $icons['hide-and-deny']; ?>' alt="Hide" />
					</a>
				<?php endif; ?>

				<?php if ( $editor_data['show_deprecated_hide_button'] ): ?>
					<a id='ws_hide_item' class='ws_button' href='javascript:void(0)' title='Hide without preventing access (cosmetic)'><img src='<?php echo $icons['hide']; ?>' alt="Show/Hide" /></a>
				<?php endif; ?>

				<a id='ws_delete_item' class='ws_button ws_delete_menu_button' href='javascript:void(0)' title='Delete menu item'><img src='<?php echo $icons['delete']; ?>' alt="Delete menu item" /></a>

				<div class="ws_separator">&nbsp;</div>

				<?php
				if ( !$is_pro_version ) {
					ame_output_sort_buttons($icons);
				}
				?>

				<div class="clear"></div>
			</div>

			<div class="ws_button_container ws_second_toolbar_row <?php
					if (!$is_second_toolbar_visible) { echo ' hidden'; }
				?>">

				<?php
				if ( $is_pro_version ) {
					ame_output_sort_buttons($icons);
				}
				?>

				<div class="clear"></div>
			</div>
		</div>

		<div id='ws_submenu_box' class="ws_box">
		</div>

		<?php do_action('admin_menu_editor-container', 'submenu'); ?>
	</div>

	<div class="ws_basic_container">

		<div class="ws_main_container" id="ws_editor_sidebar">
		<form method="post" action="<?php echo esc_attr(add_query_arg('noheader', '1', $editor_data['current_tab_url'])); ?>" id='ws_main_form' name='ws_main_form'>
			<?php wp_nonce_field('menu-editor-form'); ?>
			<input type="hidden" name="action" value="save_menu">
			<?php
			printf('<input type="hidden" name="config_id" value="%s">', esc_attr($editor_data['menu_config_id']));
			?>
			<input type="hidden" name="data" id="ws_data" value="">
			<input type="hidden" name="data_length" id="ws_data_length" value="">
			<input type="hidden" name="selected_actor" id="ws_selected_actor" value="">

			<input type="hidden" name="selected_menu_url" id="ws_selected_menu_url" value="">
			<input type="hidden" name="selected_submenu_url" id="ws_selected_submenu_url" value="">

			<input type="hidden" name="expand_menu" id="ws_expand_selected_menu" value="">
			<input type="hidden" name="expand_submenu" id="ws_expand_selected_submenu" value="">

			<input type="button" id='ws_save_menu' class="button-primary ws_main_button" value="Save Changes" />
		</form>

			<input type="button" id='ws_reset_menu' value="Undo changes" class="button ws_main_button" />
			<input type="button" id='ws_load_menu' value="Load default menu" class="button ws_main_button" />

			<!--
			<input type="button" id='ws_test_access' value="Test access..." class="button ws_main_button" />
			-->

			<?php
			$compact_layout_title = 'Compact layout';
			if ( $is_compact_layout_enabled ) {
				$compact_layout_title = '&#x2713; ' . $compact_layout_title;
			}
			?>
			<input type="button"
			       id='ws_toggle_editor_layout'
			       value="<?php echo $compact_layout_title; ?>"
			       class="button ws_main_button" />

			<?php
				do_action('admin_menu_editor-sidebar');
			?>
		</div>

		<div class="clear"></div>
		<div class="metabox-holder">
		<?php
		if ( apply_filters('admin_menu_editor-show_general_box', false) ) :
			$is_general_box_open = true;
			if ( isset($_COOKIE['ame_vis_box_open']) ) {
				$is_general_box_open = ($_COOKIE['ame_vis_box_open'] === '1');
			}
			$box_class = $is_general_box_open ? '' : 'closed';

			?>
				<div class="postbox ws_ame_custom_postbox <?php echo $box_class; ?>" id="ws_ame_general_vis_box">
					<button type="button" class="handlediv button-link">
						<span class="toggle-indicator"></span>
					</button>
					<h2 class="hndle">General</h2>
					<div class="inside">
						<?php do_action('admin_menu_editor-general_box'); ?>
					</div>
				</div>
			<?php
		endif;

		$is_how_to_box_open = true;
		if ( isset($_COOKIE['ame_how_to_box_open']) ) {
			$is_how_to_box_open = ($_COOKIE['ame_how_to_box_open'] === '1');
		}
		$box_class = $is_how_to_box_open ? '' : 'closed';

		if ( $is_pro_version ) {
			$tutorial_base_url = 'https://adminmenueditor.com/documentation/';
		} else {
			$tutorial_base_url = 'https://adminmenueditor.com/free-version-docs/';
		}

		/** @noinspection HtmlUnknownTarget */
		$how_to_link_template = '<a href="' . htmlspecialchars($tutorial_base_url) . '%1$s" target="_blank" title="Opens in a new tab">%2$s</a>';
		$how_to_item_template = '<li>' . $how_to_link_template . '</li>';

		?>
			<div class="postbox ws_ame_custom_postbox <?php echo $box_class; ?>" id="ws_ame_how_to_box">
				<button type="button" class="handlediv button-link">
					<span class="toggle-indicator"></span>
				</button>
				<h2 class="hndle">How To</h2>
				<div class="inside">
					<ul class="ame-tutorial-list">
						<?php
						if ( $is_pro_version ):
							//Pro version tutorials.
							?>
							<li><?php
								printf($how_to_link_template, 'how-to-hide-a-menu-item/', 'Hide a Menu...');
								?>
								<ul class="ame-tutorial-list">
									<?php
									foreach (
										array(
											'how-to-hide-a-menu-item/#how-to-hide-a-menu-from-a-role'                   => 'From a Role',
											'how-to-hide-a-menu-item/#how-to-hide-a-menu-from-a-user'                   => 'From a User',
											'how-to-hide-a-menu-item/#how-to-hide-a-menu-from-everyone-except-yourself' => 'From Everyone Except You',
											'how-to-hide-menu-without-preventing-access/'                               => 'Without Preventing Access',
										)
										as $how_to_url => $how_to_title
									) {
										printf($how_to_item_template, esc_attr($how_to_url), $how_to_title);
									}
									?>
								</ul>
							</li>
							<?php
							foreach (
								array(
									'how-to-give-access-to-menu/' => 'Show a Menu',
									'how-to-move-and-sort-menus/' => 'Move and Sort Menus',
									'how-to-add-a-new-menu-item/' => 'Add a New Menu',
								)
								as $how_to_url => $how_to_title
							) {
								printf($how_to_item_template, esc_attr($how_to_url), $how_to_title);
							}

						else:
							//Free version tutorials.
							foreach (
								array(
									'how-to-hide-menus/'          => 'Hide a Menu Item',
									'how-to-hide-menus-cosmetic/' => 'Hide Without Blocking Access',
									'how-to-add-new-menu/'        => 'Add a New Menu',
								)
								as $how_to_url => $how_to_title
							) {
								printf($how_to_item_template, esc_attr($how_to_url), $how_to_title);
							}
						endif;
						?>
					</ul>
				</div>
			</div>
		</div> <!-- / .metabox-holder -->

		<?php
		$hint_id = 'ws_sidebar_pro_ad';
		$show_pro_benefits = !apply_filters('admin_menu_editor_is_pro', false) && (!isset($editor_data['show_hints'][$hint_id]) || $editor_data['show_hints'][$hint_id]);

		if ( $show_pro_benefits ):
			$benefit_variations = array(
				'Hide dashboard widgets.',
				'More menu icons.',
				'Make menus open in a new tab or an iframe.',
				'Prevent users from deleting a specific user.',
			);
			//Pseudo-randomly select one phrase based on the site URL.
			$variation_index = hexdec( substr(md5(get_site_url() . 'ab'), -2) ) % count($benefit_variations);
			$selected_variation = $benefit_variations[$variation_index];

			$pro_version_link = 'http://adminmenueditor.com/upgrade-to-pro/?utm_source=Admin%2BMenu%2BEditor%2Bfree&utm_medium=text_link&utm_content=sidebar_link_nv' . $variation_index . '&utm_campaign=Plugins';
			?>
			<div class="clear"></div>

			<div class="ws_hint" id="<?php echo esc_attr($hint_id); ?>">
				<div class="ws_hint_close" title="Close">x</div>
				<div class="ws_hint_content">
					<strong>Upgrade to Pro:</strong>
					<ul>
						<li>Role-based menu permissions.</li>
						<li>Hide items from specific users.</li>
						<li>Menu import and export.</li>
						<li>Change menu colors.</li>
						<li><?php echo $selected_variation; ?></li>
					</ul>
					<a href="<?php echo esc_attr($pro_version_link); ?>" target="_blank">Learn more</a>
					|
					<a href="http://amedemo.com/" target="_blank">Try online demo</a>
				</div>
			</div>
		<?php
		endif;
		?>

	</div> <!-- / .ws_basic_container -->

    </div>

	<div class="clear"></div>

</div> <!-- / .ws_menu_editor -->

<?php do_action('admin_menu_editor-display_footer'); ?>



<?php
	//Create a pop-up capability selector
	$capSelector = array('<select id="ws_cap_selector" class="ws_dropdown" size="10">');

	$capSelector[] = '<optgroup label="Roles">';
 	foreach($editor_data['all_roles'] as $role_id => $role_name){
 		$capSelector[] = sprintf(
		 	'<option value="%s">%s</option>',
		 	esc_attr($role_id),
		 	$role_name
	 	);
 	}
 	$capSelector[] = '</optgroup>';

 	$capSelector[] = '<optgroup label="Capabilities">';
 	foreach($editor_data['all_capabilities'] as $cap){
 		$capSelector[] = sprintf(
		 	'<option value="%s">%s</option>',
		 	esc_attr($cap),
		 	$cap
	 	);
 	}
 	$capSelector[] = '</optgroup>';
 	$capSelector[] = '</select>';

 	echo implode("\n", $capSelector);
?>

<!-- Menu icon selector widget -->
<div id="ws_icon_selector" class="ws_with_more_icons" style="display: none;">

	<div id="ws_icon_source_tabs">
	<ul class="ws_tool_tab_nav">
		<?php
		$iconSelectorTabs = apply_filters(
			'admin_menu_editor-icon_selector_tabs',
			array('ws_core_icons_tab' => 'Dashicons')
		);
		foreach($iconSelectorTabs as $id => $caption) {
			printf('<li><a href="#%s">%s</a></li>', esc_attr($id), $caption);
		}
		?>
	</ul>

	<?php
	//Let the user select a custom icon via the media uploader.
	//We only support the new WP 3.5+ media API. Hence the function_exists() check.
	if ( function_exists('wp_enqueue_media') ):
		?>
		<input type="button" class="button"
		       id="ws_choose_icon_from_media"
		       title="Upload an image or choose one from your media library"
		       value="Media Library">
		<div class="clear"></div>
		<?php
	endif;
	?>

	<div class="ws_tool_tab" id="ws_core_icons_tab">

	<?php
	//The old "menu-icon-something" icons are only available in WP 3.8.x and below. Newer versions use Dashicons.
	//Plugins can change $wp_version to something useless for security, so lets check if Dashicons are available
	//before we throw away the old icons.
	$oldMenuIconsAvailable = ( !$editor_data['dashicons_available'] )
		|| version_compare($GLOBALS['wp_version'], '3.9-beta', '<');

	if ($oldMenuIconsAvailable) {
		$defaultWpIcons = array(
			'generic', 'dashboard', 'post', 'media', 'links', 'page', 'comments',
			'appearance', 'plugins', 'users', 'tools', 'settings', 'site',
		);
		foreach($defaultWpIcons as $icon) {
			printf(
				'<div class="ws_icon_option" title="%1$s" data-icon-class="menu-icon-%2$s">
					<div class="ws_icon_image icon16 icon-%2$s"><br></div>
				</div>',
				esc_attr(ucwords($icon)),
				$icon
			);
		}
	}

	//These dashicons are used in the default admin menu.
	$defaultDashicons = array(
		'admin-generic', 'dashboard', 'admin-post', 'admin-media', 'admin-links', 'admin-page', 'admin-comments',
		'admin-appearance', 'admin-plugins', 'admin-users', 'admin-tools', 'admin-settings', 'admin-network',
	);

	//The rest of Dashicons. Some icons were manually removed as they wouldn't look good as menu icons.
	$dashicons = array(
		'admin-site', 'admin-home',
		'album', 'align-center', 'align-left', 'align-none', 'align-right',
		'analytics', 'archive', 'art', 'awards', 'backup', 'book', 'book-alt',
		'building', 'businessman', 'calendar', 'calendar-alt', 'camera', 'carrot',
		'cart', 'category', 'chart-area', 'chart-bar', 'chart-line', 'chart-pie',
		'clipboard', 'clock', 'cloud', 'desktop', 'dismiss', 'download', 'edit', 'editor-code', 'editor-contract', 'editor-customchar',
		'editor-distractionfree', 'editor-help', 'editor-insertmore',
		'editor-justify', 'editor-kitchensink', 'editor-ol', 'editor-paste-text',
		'editor-paste-word', 'editor-quote', 'editor-removeformatting', 'editor-rtl', 'editor-spellcheck',
		'editor-ul', 'editor-unlink', 'editor-video',
		'email', 'email-alt', 'exerpt-view', 'external', 'facebook',
		'facebook-alt', 'feedback', 'filter', 'flag', 'format-aside',
		'format-audio', 'format-chat', 'format-gallery', 'format-image', 'format-quote', 'format-status',
		'format-video', 'forms', 'googleplus', 'grid-view', 'groups',
		'hammer', 'heart', 'hidden', 'id', 'id-alt', 'image-crop', 'image-filter',
		'image-flip-horizontal', 'image-flip-vertical', 'image-rotate',
		'image-rotate-left', 'image-rotate-right', 'images-alt',
		'images-alt2', 'index-card', 'info', 'leftright', 'lightbulb', 'list-view',
		'location', 'location-alt', 'lock', 'marker',
		'media-archive', 'media-audio',	'media-code', 'media-default', 'media-video', 'megaphone',
		'menu', 'microphone', 'migrate', 'minus', 'money', 'nametag', 'networking', 'no',
		'no-alt', 'palmtree', 'performance', 'phone', 'playlist-audio',
		'playlist-video', 'plus', 'plus-alt', 'portfolio', 'post-status', 'post-trash',
		'pressthis', 'products', 'redo', 'rss', 'schedule',
		'screenoptions', 'search', 'share', 'share-alt',
		'share-alt2', 'share1', 'shield', 'shield-alt', 'slides', 'smartphone', 'smiley', 'sort', 'sos', 'star-empty',
		'star-filled', 'star-half', 'sticky', 'store', 'tablet', 'tag',
		'tagcloud', 'testimonial', 'text', 'thumbs-down', 'thumbs-up', 'translation', 'twitter', 'undo',
		'universal-access',	'universal-access-alt', 'unlock',
		'update', 'upload', 'vault', 'video-alt', 'video-alt2', 'video-alt3', 'visibility', 'warning', 'welcome-add-page',
		'welcome-comments', 'welcome-learn-more', 'welcome-view-site', 'welcome-widgets-menus', 'welcome-write-blog',
		'wordpress', 'wordpress-alt', 'yes'
	);

	if ($editor_data['dashicons_available']) {
		function ws_ame_print_dashicon_option($icon, $isExtraIcon = false) {
			printf(
				'<div class="ws_icon_option%3$s" title="%1$s" data-icon-url="dashicons-%2$s">
					<div class="ws_icon_image icon16 dashicons dashicons-%2$s"></div>
				</div>',
				esc_attr(ucwords(str_replace('-', ' ', $icon))),
				$icon,
				$isExtraIcon ? ' ws_icon_extra' : ''
			);
		}

		if ( !$oldMenuIconsAvailable ) {
			foreach($defaultDashicons as $icon) {
				ws_ame_print_dashicon_option($icon);
			}
		}
		foreach($dashicons as $icon) {
			ws_ame_print_dashicon_option($icon, true);
		}
	}

	$defaultIconImages = array(
		admin_url('images/generic.png'),
	);
	foreach($defaultIconImages as $icon) {
		printf(
			'<div class="ws_icon_option" data-icon-url="%1$s">
				<img src="%1$s">
			</div>',
			esc_attr($icon)
		);
	}

	?>
	<div class="ws_icon_option ws_custom_image_icon" title="Custom image" style="display: none;">
		<img src="<?php echo esc_attr(admin_url('images/loading.gif')); ?>">
	</div>

		<div class="clear"></div>
	</div>

		<?php do_action('admin_menu_editor-icon_selector'); ?>

	</div><!-- tab container -->

</div>

<span id="ws-ame-screen-meta-contents" style="display:none;">
	<label for="ws-hide-advanced-settings">
		<input type="checkbox" id="ws-hide-advanced-settings"<?php
			if ( $this->options['hide_advanced_settings'] ){
				echo ' checked="checked"';
			}
		?> /> Hide advanced options
	</label><br>
</span>


<!-- Confirmation dialog when hiding "Dashboard -> Home" -->
<div id="ws-ame-dashboard-hide-confirmation" style="display: none;">
	<span>
		Hiding <em>Dashboard -> Home</em> may prevent users with the selected role from logging in!
		Are you sure you want to do it?
	</span>

	<h4>Explanation</h4>
	<p>
		WordPress automatically redirects users to the <em>Dashboard -> Home</em> page upon successful login.
		If you hide this page, users will get an "insufficient permissions" error when they log in
		due to being redirected to a hidden page. As a result, it will look like their login failed.
	</p>

	<h4>Recommendations</h4>
	<p>
		You can use a plugin like <a href="http://wordpress.org/plugins/peters-login-redirect/">Peter's Login Redirect</a>
		to redirect specific roles to different pages.
	</p>

	<div class="ws_dialog_buttons">
		<?php
		submit_button('Hide the menu', 'primary', 'ws_confirm_menu_hiding', false);
		submit_button('Leave it visible', 'secondary', 'ws_cancel_menu_hiding', false);
		?>
	</div>

	<label class="ws_dont_show_again">
		<input type="checkbox" id="ws-ame-disable-dashboard-hide-confirmation">
		Don't show this message again
	</label>
</div>

<!-- Confirmation dialog when trying to delete a non-custom item. -->
<div id="ws-ame-menu-deletion-error" title="Error" style="display: none;">
	<div class="ws_dialog_panel">
		Sorry, it's not possible to permanently delete
		<span id="ws-ame-menu-type-desc">{a built-in menu item|an item added by another plugin}</span>.
		Would you like to hide it instead?
	</div>

	<div class="ws_dialog_buttons ame-vertical-button-list">
		<?php
		submit_button('Hide it from all users', 'secondary', 'ws_hide_menu_from_everyone', false);
		submit_button(
			sprintf('Hide it from everyone except "%s"', $current_user->get('user_login')),
			'secondary',
			'ws_hide_menu_except_current_user',
			false
		);
		submit_button(
			'Hide it from everyone except Administrator',
			'secondary',
			'ws_hide_menu_except_administrator',
			false
		);
		submit_button('Cancel', 'secondary', 'ws_cancel_menu_deletion', false);
		?>
	</div>
</div>

<?php include dirname(__FILE__) . '/cap-suggestion-box.php'; ?>

<?php include dirname(__FILE__) . '/test-access-screen.php'; ?>

<?php
if ( $is_pro_version ) {
	include $extrasDirectory . '/page-dropdown.php';
}
?>


<!--suppress JSUnusedLocalSymbols These variables are actually used by menu-editor.js -->
<script type='text/javascript'>
var defaultMenu = <?php echo $editor_data['default_menu_js']; ?>;
var customMenu = <?php echo $editor_data['custom_menu_js']; ?>;
</script>
