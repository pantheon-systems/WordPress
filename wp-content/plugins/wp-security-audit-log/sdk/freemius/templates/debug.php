<?php
	/**
	 * @package     Freemius
	 * @copyright   Copyright (c) 2015, Freemius, Inc.
	 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License Version 3
	 * @since       1.1.1
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	global $fs_active_plugins;

	$fs_options = FS_Option_Manager::get_manager( WP_FS__ACCOUNTS_OPTION_NAME, true );

    $off_text = fs_text_x_inline( 'Off', 'as turned off' );
    $on_text  = fs_text_x_inline( 'On', 'as turned on' );
?>
<h1><?php echo fs_text_inline( 'Freemius Debug' ) . ' - ' . fs_text_inline( 'SDK' ) . ' v.' . $fs_active_plugins->newest->version ?></h1>
<div>
	<!-- Debugging Switch -->
	<?php //$debug_mode = get_option( 'fs_debug_mode', null ) ?>
	<span class="switch-label"><?php fs_esc_html_echo_x_inline( 'Debugging', 'as code debugging' ) ?></span>

	<div class="switch <?php echo WP_FS__DEBUG_SDK ? 'off' : 'on' ?>">
		<div class="toggle"></div>
		<span class="on"><?php echo esc_html( $on_text ) ?></span>
		<span class="off"><?php echo esc_html( $off_text ) ?></span>
	</div>
	<script type="text/javascript">
		(function ($) {
			$(document).ready(function () {
				// Switch toggle
				$('.switch').click(function () {
					$(this)
						.toggleClass('on')
						.toggleClass('off');

					$.post(ajaxurl, {
						action: 'fs_toggle_debug_mode',
						is_on : ($(this).hasClass('off') ? 1 : 0)
					}, function (response) {
						if (1 == response) {
							// Refresh page on success.
							location.reload();
						}
					});
				});
			});
		}(jQuery));
	</script>
</div>
<h2><?php fs_esc_html_echo_inline( 'Actions', 'actions' ) ?></h2>
<table>
	<tbody>
	<tr>
		<td>
			<!-- Delete All Accounts -->
			<form action="" method="POST">
				<input type="hidden" name="fs_action" value="restart_freemius">
				<?php wp_nonce_field( 'restart_freemius' ) ?>
				<button class="button button-primary"
				        onclick="if (confirm('<?php fs_esc_attr_echo_inline( 'Are you sure you want to delete all Freemius data?', 'delete-all-confirm' ) ?>')) this.parentNode.submit(); return false;"><?php fs_esc_html_echo_inline( 'Delete All Accounts' ) ?></button>
			</form>
		</td>
		<td>
			<!-- Clear API Cache -->
			<form action="" method="POST">
				<input type="hidden" name="fs_clear_api_cache" value="true">
				<button class="button button-primary"><?php fs_esc_html_echo_inline( 'Clear API Cache' ) ?></button>
			</form>
		</td>
		<td>
			<!-- Sync Data with Server -->
			<form action="" method="POST">
				<input type="hidden" name="background_sync" value="true">
				<button class="button button-primary"><?php fs_esc_html_echo_inline( 'Sync Data From Server' ) ?></button>
			</form>
		</td>
		<td>
			<button id="fs_load_db_option" class="button"><?php fs_esc_html_echo_inline( 'Load DB Option' ) ?></button>
		</td>
		<td>
			<button id="fs_set_db_option" class="button"><?php fs_esc_html_echo_inline( 'Set DB Option' ) ?></button>
		</td>
	</tr>
	</tbody>
</table>
<script type="text/javascript">
	(function ($) {
		$('#fs_load_db_option').click(function () {
			var optionName = prompt('Please enter the option name:');

			if (optionName) {
				$.post(ajaxurl, {
					action     : 'fs_get_db_option',
					option_name: optionName
				}, function (response) {
					if (response.data.value)
						prompt('The option value is:', response.data.value);
					else
						alert('Oops... Option does not exist in the DB.');
				});
			}
		});

		$('#fs_set_db_option').click(function () {
			var optionName = prompt('Please enter the option name:');

			if (optionName) {
				var optionValue = prompt('Please enter the option value:');

				if (optionValue) {
					$.post(ajaxurl, {
						action      : 'fs_set_db_option',
						option_name : optionName,
						option_value: optionValue
					}, function () {
						alert('Option was successfully set.');
					});
				}
			}
		});
	})(jQuery);
</script>
<?php
	if ( ! defined( 'FS_API__ADDRESS' ) ) {
		define( 'FS_API__ADDRESS', '://api.freemius.com' );
	}
	if ( ! defined( 'FS_API__SANDBOX_ADDRESS' ) ) {
		define( 'FS_API__SANDBOX_ADDRESS', '://sandbox-api.freemius.com' );
	}

	$defines = array(
		array(
			'key' => 'WP_FS__REMOTE_ADDR',
			'val' => WP_FS__REMOTE_ADDR,
		),
		array(
			'key' => 'WP_FS__ADDRESS_PRODUCTION',
			'val' => WP_FS__ADDRESS_PRODUCTION,
		),
		array(
			'key' => 'FS_API__ADDRESS',
			'val' => FS_API__ADDRESS,
		),
		array(
			'key' => 'FS_API__SANDBOX_ADDRESS',
			'val' => FS_API__SANDBOX_ADDRESS,
		),
		array(
			'key' => 'WP_FS__DIR',
			'val' => WP_FS__DIR,
		),
	)
?>
<br>
<table class="widefat">
	<thead>
	<tr>
		<th><?php fs_esc_html_echo_inline( 'Key', 'key' ) ?></th>
		<th><?php fs_esc_html_echo_inline( 'Value', 'value' ) ?></th>
	</tr>
	</thead>
	<tbody>
	<?php $alternate = false;
		foreach ( $defines as $p ) : ?>
			<tr<?php if ( $alternate ) {
				echo ' class="alternate"';
			} ?>>
				<td><?php echo $p['key'] ?></td>
				<td><?php echo $p['val'] ?></td>
			</tr>
			<?php $alternate = ! $alternate ?>
		<?php endforeach ?>
	</tbody>
</table>
<h2><?php fs_esc_html_echo_x_inline( 'SDK Versions', 'as software development kit versions', 'sdk-versions' ) ?></h2>
<table id="fs_sdks" class="widefat">
	<thead>
	<tr>
		<th><?php fs_esc_html_echo_x_inline( 'Version', 'product version' ) ?></th>
		<th><?php fs_esc_html_echo_inline( 'SDK Path' ) ?></th>
		<th><?php fs_esc_html_echo_inline( 'Module Path' ) ?></th>
		<th><?php fs_esc_html_echo_inline( 'Is Active' ) ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $fs_active_plugins->plugins as $sdk_path => &$data ) : ?>
		<?php $is_active = ( WP_FS__SDK_VERSION == $data->version ) ?>
		<tr<?php if ( $is_active ) {
			echo ' style="background: #E6FFE6; font-weight: bold"';
		} ?>>
			<td><?php echo $data->version ?></td>
			<td><?php echo $sdk_path ?></td>
			<td><?php echo $data->plugin_path ?></td>
			<td><?php echo ( $is_active ) ? 'Active' : 'Inactive' ?></td>
		</tr>
	<?php endforeach ?>
	</tbody>
</table>

<?php
	$module_types = array(
		WP_FS__MODULE_TYPE_PLUGIN,
		WP_FS__MODULE_TYPE_THEME
	);
?>

<?php foreach ( $module_types as $module_type ) : ?>
	<?php $modules = $fs_options->get_option( $module_type . 's' ) ?>
	<?php if ( is_array( $modules ) && count( $modules ) > 0 ) : ?>
		<h2><?php echo esc_html( ( WP_FS__MODULE_TYPE_PLUGIN == $module_type ) ? fs_text_inline( 'Plugins', 'plugins' ) : fs_text_inline( 'Themes', 'themes' ) ) ?></h2>
		<table id="fs_<?php echo $module_type ?>" class="widefat">
			<thead>
			<tr>
				<th><?php fs_esc_html_echo_inline( 'ID', 'id' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Slug' ) ?></th>
                <th><?php fs_esc_html_echo_x_inline( 'Version', 'product version' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Title' ) ?></th>
				<th><?php fs_esc_html_echo_x_inline( 'API', 'as application program interface' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Freemius State' ) ?></th>
                <th><?php fs_esc_html_echo_inline( 'Module Path' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Public Key' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Actions' ) ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $modules as $slug => $data ) : ?>
				<?php
				if ( WP_FS__MODULE_TYPE_THEME === $module_type ) {
					$current_theme = wp_get_theme();
					$is_active     = ( $current_theme->stylesheet === $data->file );
				} else {
					$is_active = is_plugin_active( $data->file );
				}
				?>
				<?php $fs = $is_active ? freemius( $data->id ) : null ?>
				<tr<?php if ( $is_active ) {
					if ( $fs->has_api_connectivity() && $fs->is_on() ) {
						echo ' style="background: #E6FFE6; font-weight: bold"';
					} else {
						echo ' style="background: #ffd0d0; font-weight: bold"';
					}
				} ?>>
					<td><?php echo $data->id ?></td>
					<td><?php echo $slug ?></td>
					<td><?php echo $data->version ?></td>
					<td><?php echo $data->title ?></td>
					<td<?php if ( $is_active && ! $fs->has_api_connectivity() ) {
						echo ' style="color: red; text-transform: uppercase;"';
					} ?>><?php if ( $is_active ) {
							echo esc_html( $fs->has_api_connectivity() ?
								fs_text_x_inline( 'Connected', 'as connection was successful' ) :
                                fs_text_x_inline( 'Blocked', 'as connection blocked' )
                            );
						} ?></td>
					<td<?php if ( $is_active && ! $fs->is_on() ) {
						echo ' style="color: red; text-transform: uppercase;"';
					} ?>><?php if ( $is_active ) {
							echo esc_html( $fs->is_on() ?
                                $on_text :
                                $off_text
                            );
						} ?></td>
					<td><?php echo $data->file ?></td>
					<td><?php echo $data->public_key ?></td>
					<td>
						<?php if ( $is_active ) : ?>
							<?php if ( $fs->has_trial_plan() ) : ?>
								<form action="" method="POST">
									<input type="hidden" name="fs_action" value="simulate_trial">
									<input type="hidden" name="module_id" value="<?php echo $fs->get_id() ?>">
									<?php wp_nonce_field( 'simulate_trial' ) ?>

								<button type="submit" class="button button-primary simulate-trial"><?php fs_esc_html_echo_inline( 'Simulate Trial' ) ?></button>
								</form>
							<?php endif ?>
							<?php if ( $fs->is_registered() ) : ?>
								<a class="button" href="<?php echo $fs->get_account_url() ?>"><?php fs_esc_html_echo_inline( 'Account', 'account' ) ?></a>
							<?php endif ?>
						<?php endif ?>
					</td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	<?php endif ?>
<?php endforeach ?>
<?php foreach ( $module_types as $module_type ) : ?>
	<?php
	/**
	 * @var array     $VARS
	 * @var FS_Site[] $sites
	 */
	$sites = $VARS[ $module_type . '_sites' ];
	?>
	<?php if ( is_array( $sites ) && count( $sites ) > 0 ) : ?>
		<h2><?php echo esc_html( sprintf(
				/* translators: %s: 'plugin' or 'theme' */
				fs_text_inline( '%s Installs', 'module-installs' ),
				( WP_FS__MODULE_TYPE_PLUGIN === $module_type ? fs_text_inline( 'Plugin', 'plugin' ) : fs_text_inline( 'Theme', 'theme' ) )
			) ) ?> / <?php fs_esc_html_echo_x_inline( 'Sites', 'like websites', 'sites' ) ?></h2>
		<table id="fs_<?php echo $module_type ?>_installs" class="widefat">
			<thead>
			<tr>
				<th><?php fs_esc_html_echo_inline( 'ID', 'id' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Slug' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'User ID' ) ?></th>
				<th><?php fs_esc_html_echo_x_inline( 'Plan', 'as product pricing plan', 'plan' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Public Key' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Secret Key' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Actions' ) ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $sites as $slug => $site ) : ?>
				<tr>
					<td><?php echo $site->id ?></td>
					<td><?php echo $slug ?></td>
					<td><?php echo $site->user_id ?></td>
					<td><?php
							echo is_object( $site->plan ) ?
								Freemius::_decrypt( $site->plan->name ) :
								''
						?></td>
					<td><?php echo $site->public_key ?></td>
					<td><?php echo $site->secret_key ?></td>
					<td><form action="" method="POST">
							<input type="hidden" name="fs_action" value="delete_install">
							<?php wp_nonce_field( 'delete_install' ) ?>
							<input type="hidden" name="module_id" value="<?php echo $site->plugin_id ?>">
							<input type="hidden" name="module_type" value="<?php echo $module_type ?>">
							<input type="hidden" name="slug" value="<?php echo $slug ?>">
							<button type="submit" class="button"><?php fs_esc_html_echo_x_inline( 'Delete', 'verb', 'delete' ) ?></button></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	<?php endif ?>
<?php endforeach ?>
<?php
	$addons = $VARS['addons'];
?>
<?php foreach ( $addons as $plugin_id => $plugin_addons ) : ?>
	<h2><?php echo esc_html( sprintf( fs_text_inline( 'Add Ons of module %s', 'addons-of-x' ), $plugin_id ) ) ?></h2>
	<table id="fs_addons" class="widefat">
		<thead>
		<tr>
			<th><?php fs_esc_html_echo_inline( 'ID', 'id' ) ?></th>
			<th><?php fs_esc_html_echo_inline( 'Title' ) ?></th>
			<th><?php fs_esc_html_echo_inline( 'Slug' ) ?></th>
            <th><?php fs_esc_html_echo_x_inline( 'Version', 'product version' ) ?></th>
            <th><?php fs_esc_html_echo_inline( 'Public Key' ) ?></th>
            <th><?php fs_esc_html_echo_inline( 'Secret Key' ) ?></th>
        </tr>
		</thead>
		<tbody>
		<?php
			/**
			 * @var FS_Plugin[] $plugin_addons
			 */
			foreach ( $plugin_addons as $addon ) : ?>
				<tr>
					<td><?php echo $addon->id ?></td>
					<td><?php echo $addon->title ?></td>
					<td><?php echo $addon->slug ?></td>
					<td><?php echo $addon->version ?></td>
					<td><?php echo $addon->public_key ?></td>
					<td><?php echo $addon->secret_key ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endforeach ?>
<?php
	/**
	 * @var FS_User[] $users
	 */
	$users = $VARS['users'];
?>
<?php if ( is_array( $users ) && 0 < count( $users ) ) : ?>
	<h2><?php fs_esc_html_echo_inline( 'Users' ) ?></h2>
	<table id="fs_users" class="widefat">
		<thead>
		<tr>
			<th><?php fs_esc_html_echo_inline( 'ID', 'id' ) ?></th>
			<th><?php fs_esc_html_echo_inline( 'Name' ) ?></th>
			<th><?php fs_esc_html_echo_inline( 'Email' ) ?></th>
			<th><?php fs_esc_html_echo_inline( 'Verified' ) ?></th>
			<th><?php fs_esc_html_echo_inline( 'Public Key' ) ?></th>
			<th><?php fs_esc_html_echo_inline( 'Secret Key' ) ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $users as $user_id => $user ) : ?>
			<tr>
				<td><?php echo $user->id ?></td>
				<td><?php echo $user->get_name() ?></td>
				<td><a href="mailto:<?php echo esc_attr( $user->email ) ?>"><?php echo $user->email ?></a></td>
				<td><?php echo json_encode( $user->is_verified ) ?></td>
				<td><?php echo $user->public_key ?></td>
				<td><?php echo $user->secret_key ?></td>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
<?php foreach ( $module_types as $module_type ) : ?>
	<?php $licenses = $VARS[ $module_type . '_licenses' ] ?>
	<?php if ( is_array( $licenses ) && count( $licenses ) > 0 ) : ?>
		<h2><?php echo esc_html( sprintf( fs_text_inline( '%s Licenses', 'module-licenses' ), ( WP_FS__MODULE_TYPE_PLUGIN === $module_type ? fs_text_inline( 'Plugin', 'plugin' ) : fs_text_inline( 'Theme', 'theme' ) ) ) ) ?></h2>
		<table id="fs_<?php echo $module_type ?>_licenses" class="widefat">
			<thead>
			<tr>
				<th><?php fs_esc_html_echo_inline( 'ID', 'id' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Plugin ID' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'User ID' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Plan ID' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Quota' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Activated' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Blocking' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'License Key' ) ?></th>
				<th><?php fs_esc_html_echo_x_inline( 'Expiration', 'as expiration date' ) ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $licenses as $slug => $module_licenses ) : ?>
				<?php foreach ( $module_licenses as $id => $licenses ) : ?>
					<?php if ( is_array( $licenses ) && 0 < count( $licenses ) ) : ?>
						<?php foreach ( $licenses as $license ) : ?>
							<tr>
								<td><?php echo $license->id ?></td>
								<td><?php echo $license->plugin_id ?></td>
								<td><?php echo $license->user_id ?></td>
								<td><?php echo $license->plan_id ?></td>
								<td><?php echo $license->is_unlimited() ? 'Unlimited' : ( $license->is_single_site() ? 'Single Site' : $license->quota ) ?></td>
								<td><?php echo $license->activated ?></td>
								<td><?php echo $license->is_block_features ? 'Blocking' : 'Flexible' ?></td>
								<td><?php echo htmlentities( $license->secret_key ) ?></td>
								<td><?php echo $license->expiration ?></td>
							</tr>
						<?php endforeach ?>
					<?php endif ?>
				<?php endforeach ?>
			<?php endforeach ?>
			</tbody>
		</table>
	<?php endif ?>
<?php endforeach ?>
<?php if ( FS_Logger::is_storage_logging_on() ) : ?>

	<h2><?php fs_esc_html_echo_inline( 'Debug Log', 'debug-log' ) ?></h2>

	<div id="fs_debug_filters">
		<select name="type">
			<option value="" selected="selected"><?php fs_esc_html_echo_inline( 'All Types', 'all-types' ) ?></option>
			<option value="warn_error">Warnings & Errors</option>
			<option value="error">Errors</option>
			<option value="warn">Warnings</option>
			<option value="info">Info</option>
		</select>
		<select name="request_type">
			<option value="" selected="selected"><?php fs_esc_html_echo_inline( 'All Requests', 'all-requests' ) ?></option>
			<option value="call">Sync</option>
			<option value="ajax">AJAX</option>
			<option value="cron">WP Cron</option>
		</select>
		<input name="file" type="text" placeholder="<?php fs_esc_attr_echo_inline( 'File' ) ?>"/>
		<input name="function" type="text" placeholder="<?php fs_esc_attr_echo_inline( 'Function' ) ?>"/>
		<input name="process_id" type="text" placeholder="<?php fs_esc_attr_echo_inline( 'Process ID' ) ?>"/>
		<input name="logger" type="text" placeholder="<?php fs_esc_attr_echo_inline( 'Logger' ) ?>"/>
		<input name="message" type="text" placeholder="<?php fs_esc_attr_echo_inline( 'Message' ) ?>"/>
		<div style="margin: 10px 0">
			<button id="fs_filter" class="button" style="float: left"><i class="dashicons dashicons-filter"></i> <?php fs_esc_html_echo_inline( 'Filter', 'filter' ) ?>
			</button>

			<form action="" method="POST" style="float: left; margin-left: 10px;">
				<input type="hidden" name="fs_action" value="download_logs">
				<?php wp_nonce_field( 'download_logs' ) ?>
				<div class="fs-filters"></div>
				<button id="fs_download" class="button" type="submit"><i
						class="dashicons dashicons-download"></i> <?php fs_esc_html_echo_inline( 'Download' ) ?></button>
			</form>
			<div style="clear: both"></div>
		</div>
	</div>

	<div id="fs_log_book" style="height: 300px; overflow: auto;">
		<table class="widefat">
			<thead>
			<tr>
				<th>#</th>
				<th><?php fs_esc_html_echo_inline( 'Type' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'ID', 'id' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Function' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Message' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'File' ) ?></th>
				<th><?php fs_esc_html_echo_inline( 'Timestamp' ) ?></th>
			</tr>
			</thead>
			<tbody>
			<tr style="display: none">
				<td>{$log.log_order}.</td>
				<td class="fs-col--type">{$log.type}</td>
				<td class="fs-col--logger">{$log.logger}</td>
				<td class="fs-col--function">{$log.function}</td>
				<td class="fs-col--message">
					<a href="#" onclick="jQuery(this).parent().find('div').toggle(); return false;">
						<nobr>{$log.message_short}</nobr>
					</a>
					<div style="display: none;">{$log.message}</div>
				</td>
				<td class="fs-col--file">{$log.file}:{$log.line}</td>
				<td class="fs-col--timestamp">{$log.created}</td>
			</tr>

			</tbody>
		</table>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			var filtersChanged       = false,
			    offset               = 0,
			    limit                = 200,
			    prevFiltersSignature = null;

			var getFilters = function () {
				var filters   = {},
				    signature = '';

				$('#fs_debug_filters').find('select, input').each(function (i, e) {
					var $element = $(e);

					if ('hidden' === $element.attr('type'))
						return;

					var val = $element.val();
					if ('' !== val.trim()) {
						var name = $(e).attr('name');
						filters[name] = val;
						signature += name + '=' + val + '~';
					}
				});

				if (signature != prevFiltersSignature) {
					filtersChanged = true;
					prevFiltersSignature = signature;
				} else {
					filtersChanged = false;
				}

				return filters;
			};

			$('#fs_download').parent().submit(function () {
				var filters      = getFilters(),
				    hiddenFields = '';

				for (var f in filters) {
					if (filters.hasOwnProperty(f)) {
						hiddenFields += '<input type="hidden" name="filters[' + f + ']" value="' + filters[f] + '" />';
					}
				}

				$(this).find('.fs-filters').html(hiddenFields);
			});

			var loadLogs = function () {
				var $tbody   = $('#fs_log_book tbody'),
				    template = $tbody.find('tr:first-child').html(),
				    filters  = getFilters();

				if (!filtersChanged) {
					offset += limit;
				} else {
					// Cleanup table for new filter (only keep template row).
					$tbody.find('tr').each(function (i, e) {
						if (0 == i)
							return;

						$(e).remove();
					});

					offset = 0;
				}

				$.post(ajaxurl, {
					action : 'fs_get_debug_log',
					filters: filters,
					offset : offset,
					limit  : limit
				}, function (response) {

					for (var i = 0; i < response.data.length; i++) {
						var templateCopy = template;

						response.data[i].message_short = (response.data[i].message.length > 32) ?
						response.data[i].message.substr(0, 32) + '...' :
							response.data[i].message;

						for (var p in response.data[i]) {
							if (response.data[i].hasOwnProperty(p)) {
								templateCopy = templateCopy.replace('{$log.' + p + '}', response.data[i][p]);
							}
						}

						$tbody.append('<tr' + (i % 2 ? ' class="alternate"' : '') + '>' + templateCopy + '</tr>');
					}
				});
			};

			$('#fs_filter').click(function () {
				loadLogs();

				return false;
			});

			loadLogs();
		});
	</script>
<?php endif ?>
