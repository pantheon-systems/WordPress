<?php
require dirname( __FILE__ ) . '/wpml-menu-sync-display.class.php';

/** @var $sitepress SitePress */
/** @var $icl_menus_sync ICLMenusSync */
$active_languages = $sitepress->get_active_languages();
$def_lang_code = $sitepress->get_default_language();
$def_lang = $sitepress->get_language_details( $def_lang_code );
$secondary_languages = array();

foreach ( $active_languages as $code => $lang ) {
	if ( $code !== $def_lang_code ) {
		$secondary_languages[] = $lang;
	}
}
?>
<!--suppress HtmlFormInputWithoutLabel --><!--suppress HtmlUnknownAttribute -->
<div class="wrap">
<h2><?php esc_html_e( 'WP Menus Sync', 'sitepress' ) ?></h2>
<p><?php printf( esc_html__( 'Menu synchronization will sync the menu structure from the default language of %s to the secondary languages.', 'sitepress' ), $def_lang['display_name'] ) ?></p>

<br/>
<?php
if ( $icl_menus_sync->is_preview ) {
	?>

	<form id="icl_msync_confirm_form" method="post">
	<input type="hidden" name="action" value="icl_msync_confirm"/>

	<table id="icl_msync_confirm" class="widefat icl_msync">
	<thead>
	<tr>
		<th scope="row" class="menu-check-all"><input type="checkbox"/></th>
		<th><?php esc_html_e( 'Language', 'sitepress' ) ?></th>
		<th><?php esc_html_e( 'Action', 'sitepress' ) ?></th>
	</tr>
	</thead>
	<tbody>

	<?php
	if ( empty( $icl_menus_sync->sync_data ) ) {
		?>
		<tr>
			<td align="center" colspan="3"><?php esc_html_e( 'Nothing to sync.', 'sitepress' ) ?></td>
		</tr>
	<?php
	} else {
		//Menus
		foreach ( $icl_menus_sync->menus as $menu_id => $menu ) {
			$menu_sync_display = new WPML_Menu_Sync_Display( $menu_id, $icl_menus_sync );
			?>
			<tr class="icl_msync_menu_title">
				<td colspan="3"><?php echo esc_html( $menu['name'] ) ?></td>
			</tr>

			<?php
			// Display actions per menu
			// menu translations
			if ( isset( $icl_menus_sync->sync_data['menu_translations'], $icl_menus_sync->sync_data['menu_translations'][ $menu_id ] ) ) {
				foreach ( $icl_menus_sync->sync_data['menu_translations'][ $menu_id ] as $language => $name ) {
					$lang_details = $sitepress->get_language_details( $language );
					?>
					<tr>
						<th scope="row" class="check-column">
							<input type="checkbox"
								   name="sync[menu_translation][<?php echo esc_attr( $menu_id ) ?>][<?php echo esc_attr( $language ) ?>]"
								   value="<?php echo esc_attr( $name ) ?>"/>
						</th>
						<td><?php echo esc_html( $lang_details['display_name'] ) ?></td>
						<td><?php printf( esc_html__( 'Add menu translation:  %s', 'sitepress' ), '<strong>' . esc_html( $name ) . '</strong>' ) ?> </td>
					</tr>
				<?php
				}
			}

			foreach (
				array(
					'add',
					'mov',
					'del',
					'label_changed',
					'url_changed',
					'label_missing',
					'url_missing',
					'options_changed',
				) as $sync_type
			) {
				$menu_sync_display->print_sync_field( $sync_type );
			}
		}
	}
	?>
	</tbody>
	</table>
	<p class="submit">
		<?php
		$icl_menu_sync_submit_disabled = '';
		if ( empty( $icl_menus_sync->sync_data ) || ( empty( $icl_menus_sync->sync_data['mov'] ) && empty( $icl_menus_sync->sync_data['mov'][ $menu_id ] ) ) ) {
			$icl_menu_sync_submit_disabled = 'disabled="disabled"';
		}
		?>
		<input id="icl_msync_submit"
			   class="button-primary"
			   type="button"
			   value="<?php esc_attr_e( 'Apply changes' ) ?>"
			   data-message="<?php esc_attr_e( 'Syncing menus %1 of %2', 'sitepress' ) ?>"
			   data-message-complete="<?php esc_attr_e( 'The selected menus have been synchonized.', 'sitepress' ) ?>"
			<?php echo $icl_menu_sync_submit_disabled; ?> />&nbsp;
		<input id="icl_msync_cancel" class="button-secondary" type="button" value="<?php _e( 'Cancel' ) ?>"/>
		<span id="icl_msync_message"></span>
	</p>
		<?php wp_nonce_field( '_icl_nonce_menu_sync', '_icl_nonce_menu_sync' ); ?>
	</form>
<?php
} else {
	$need_sync = 0;
	?>
	<form method="post" action="">
		<input type="hidden" name="action" value="icl_msync_preview"/>
		<table class="widefat icl_msync">
			<thead>
			<tr>
				<th><?php echo esc_html( $def_lang['display_name'] ) ?></th>
				<?php
				if ( ! empty( $secondary_languages ) ) {
					foreach ( $secondary_languages as $lang ) {
						?>
						<th><?php echo esc_html( $lang['display_name'] ) ?></th>
					<?php
					}
				}
				?>
			</tr>
			</thead>
			<tbody>
			<?php
			if ( empty( $icl_menus_sync->menus ) ) {
				?>
				<tr>
					<td align="center" colspan="<?php echo count( $active_languages ) ?>"><?php esc_html_e( 'No menus found', 'sitepress' ) ?></td>
				</tr>
			<?php
			} else {
				foreach ( $icl_menus_sync->menus as $menu_id => $menu ) {
					?>

					<tr class="icl_msync_menu_title">
						<td><strong><?php echo esc_html( $menu['name'] ) ?></strong></td>
						<?php
						foreach ( $secondary_languages as $l ) {
							$input_name = sprintf( 'sync[menu_options][%s][%s][auto_add]', esc_attr( $menu_id ), esc_attr( $l['code'] ) );
							?>
							<td>
								<?php
								if ( isset( $menu['translations'][ $l['code'] ]['name'] ) ) {
									echo esc_html( $menu['translations'][ $l['code'] ]['name'] );
								} else { // menu is translated in $l[code]
									$need_sync++;
									?>
									<input type="text" class="icl_msync_add"
										   name="sync[menu_translations][<?php echo esc_attr( $menu_id ) ?>][<?php echo esc_attr( $l['code'] ) ?>]"
										   value="<?php echo esc_attr( $menu['name'] ) . ' - ' . esc_attr( $l['display_name'] ) ?>"
									/>
									<small><?php esc_html_e( 'Auto-generated title. Click to edit.', 'sitepress' ) ?></small>
									<input type="hidden" value=""
										   name="<?php echo $input_name ?>"
									/>
								<?php
								}
								if ( isset( $menu['translations'][ $l['code'] ]['auto_add'] ) ) {
									?>
									<input type="hidden" name="<?php echo $input_name ?>" value="<?php echo esc_attr( $menu['translations'][ $l['code'] ]['auto_add'] ) ?>"/>
								<?php
								}
								?>
							</td>
						<?php
						} //foreach($secondary_languages as $l):
						?>
					</tr>
					<?php
					$need_sync += $icl_menus_sync->render_items_tree_default( $menu_id );

				} //foreach( $icl_menus_sync->menus as  $menu_id => $menu):
			}
			?>
			</tbody>
		</table>
		<p class="submit">
			<?php
			if ( $need_sync ) {
				?>
				<input id="icl_msync_sync" type="submit" class="button-primary"
					   value="<?php esc_attr_e( 'Sync', 'sitepress' ) ?>"
					<?php disabled( ! $need_sync ) ?>
				/>
				&nbsp;&nbsp;
				<span id="icl_msync_max_input_vars"
					  style="display:none"
					  class="icl-admin-message-warning"
					  data-max_input_vars="<?php echo ini_get( 'max_input_vars' ); ?>">
					<?php
					printf(
						esc_html__( 'The menus on this page may not sync because it requires more input variables. Please modify the %s setting in your php.ini or .htaccess files to %s or more.', 'sitepress' ),
						'<strong>max_input_vars</strong>',
						'<strong>!NUM!</strong>'
					)
					?>
				</span>
			<?php
			} else {
				?>
				<input id="icl_msync_sync" type="submit" class="button-primary"
					   value="<?php esc_attr_e( 'Nothing Sync', 'sitepress' ) ?>"<?php disabled( ! $need_sync ) ?>
				/>
			<?php
			}
			?>
		</p>
		<?php wp_nonce_field( '_icl_nonce_menu_sync', '_icl_nonce_menu_sync' ); ?>
	</form>

	<?php
	if ( ! empty( $icl_menus_sync->operations ) ) {
		$show_string_translation_link = false;
		foreach ( $icl_menus_sync->operations as $op => $c ) {
			if ( $op == 'add' ) {
				?>
				<span class="icl_msync_item icl_msync_add"><?php esc_html_e( 'Item will be added', 'sitepress' ) ?></span>
			<?php
			} elseif ( $op == 'del' ) {
				?>
				<span class="icl_msync_item icl_msync_del"><?php esc_html_e( 'Item will be removed', 'sitepress' ) ?></span>
			<?php
			} elseif ( $op == 'not' ) {
				?>
				<span class="icl_msync_item icl_msync_not"><?php esc_html_e( 'Item cannot be added (parent not translated)', 'sitepress' ) ?></span>
			<?php
			} elseif ( $op == 'mov' ) {
				?>
				<span class="icl_msync_item icl_msync_mov"><?php esc_html_e( 'Item changed position', 'sitepress' ) ?></span>
			<?php
			} elseif ( $op == 'copy' ) {
				?>
				<span class="icl_msync_item icl_msync_copy"><?php esc_html_e( 'Item will be copied', 'sitepress' ) ?></span>
			<?php
			} elseif ( $op == 'label_changed' ) {
				?>
				<span class="icl_msync_item icl_msync_label_changed"><?php esc_html_e( 'Strings for menus will be updated', 'sitepress' ) ?></span>
			<?php
			} elseif ( $op == 'url_changed' ) {
				?>
				<span class="icl_msync_item icl_msync_url_changed"><?php esc_html_e( 'URLs for menus will be updated', 'sitepress' ) ?></span>
			<?php
			} elseif ( $op == 'options_changed' ) {
				?>
				<span class="icl_msync_item icl_msync_options_changed"><?php esc_html_e( 'Menu Options will be updated', 'sitepress' ) ?></span>
			<?php
			} elseif ( $op == 'label_missing' ) {
				?>
				<span class="icl_msync_item icl_msync_label_missing">
					<?php esc_html_e( 'Untranslated strings for menus', 'sitepress' ) ?>
				</span>
			<?php
			} elseif ( $op == 'url_missing' ) {
				?>
				<span class="icl_msync_item icl_msync_url_missing">
					<?php esc_html_e( 'Untranslated URLs for menus', 'sitepress' ) ?>
				</span>
			<?php
			}
		}
	}

	$icl_menus_sync->display_menu_links_to_string_translation();
}
do_action( 'icl_menu_footer' );
?>
</div>
