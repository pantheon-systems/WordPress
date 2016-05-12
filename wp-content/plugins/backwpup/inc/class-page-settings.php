<?php
/**
 * Class for BackWPup settings page
 */
class BackWPup_Page_Settings {

	/**
	 *
	 * Output js
	 *
	 * @return void
	 */
	public static function admin_print_scripts() {

		wp_enqueue_script( 'backwpupgeneral' );

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			wp_enqueue_script( 'backwpuppagesettings', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/page_settings.js', array( 'jquery' ), time(), TRUE );
		} else {
			wp_enqueue_script( 'backwpuppagesettings', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/page_settings.min.js', array( 'jquery' ), BackWPup::get_plugin_data( 'Version' ), TRUE );
		}
	}


	/**
	 * Save settings form data
	 */
	public static function save_post_form() {

		if ( ! current_user_can( 'backwpup_settings' ) )
			return;

		//set default options if button clicked
		if ( isset( $_POST[ 'default_settings' ] ) && $_POST[ 'default_settings' ] ) {

			delete_site_option( 'backwpup_cfg_showadminbar' );
			delete_site_option( 'backwpup_cfg_showfoldersize' );
			delete_site_option( 'backwpup_cfg_jobstepretry' );
			delete_site_option( 'backwpup_cfg_jobmaxexecutiontime' );
			delete_site_option( 'backwpup_cfg_loglevel' );
			delete_site_option( 'backwpup_cfg_jobwaittimems' );
			delete_site_option( 'backwpup_cfg_jobrunauthkey' );
			delete_site_option( 'backwpup_cfg_jobdooutput' );
			delete_site_option( 'backwpup_cfg_maxlogs' );
			delete_site_option( 'backwpup_cfg_gzlogs' );
			delete_site_option( 'backwpup_cfg_protectfolders' );
			delete_site_option( 'backwpup_cfg_authentication' );
			delete_site_option( 'backwpup_cfg_logfolder' );
			delete_site_option( 'backwpup_cfg_dropboxappkey' );
			delete_site_option( 'backwpup_cfg_dropboxappsecret' );
			delete_site_option( 'backwpup_cfg_dropboxsandboxappkey' );
			delete_site_option( 'backwpup_cfg_dropboxsandboxappsecret' );
			delete_site_option( 'backwpup_cfg_sugarsynckey' );
			delete_site_option( 'backwpup_cfg_sugarsyncsecret' );
			delete_site_option( 'backwpup_cfg_sugarsyncappid' );
			delete_site_option( 'backwpup_cfg_hash' );

			BackWPup_Option::default_site_options();

			BackWPup_Admin::message( __( 'Settings reset to default', 'backwpup' ) );
			return;
		}

		update_site_option( 'backwpup_cfg_showadminbar', ! empty( $_POST[ 'showadminbar' ] ) );
		update_site_option( 'backwpup_cfg_showfoldersize', ! empty( $_POST[ 'showfoldersize' ] ) );
		if ( empty( $_POST[ 'jobstepretry' ] ) || 100 < $_POST[ 'jobstepretry' ] || 1 > $_POST[ 'jobstepretry' ] ) {
			$_POST[ 'jobstepretry' ] = 3;
		}
		update_site_option( 'backwpup_cfg_jobstepretry', absint( $_POST[ 'jobstepretry' ] ) );
		if ( (int) $_POST[ 'jobmaxexecutiontime' ] > 300 ) {
			$_POST[ 'jobmaxexecutiontime' ] = 300;
		}
		update_site_option( 'backwpup_cfg_jobmaxexecutiontime', absint( $_POST[ 'jobmaxexecutiontime' ] ) );
		update_site_option( 'backwpup_cfg_loglevel', in_array( $_POST[ 'loglevel' ], array( 'normal_translated', 'normal', 'debug_translated', 'debug' ), true ) ? $_POST[ 'loglevel' ] : 'normal_translated' );
		update_site_option( 'backwpup_cfg_jobwaittimems', absint( $_POST[ 'jobwaittimems' ] ) );
		update_site_option( 'backwpup_cfg_jobdooutput', ! empty( $_POST[ 'jobdooutput' ] ) );
		update_site_option( 'backwpup_cfg_maxlogs', absint( $_POST[ 'maxlogs' ] ) );
		update_site_option( 'backwpup_cfg_gzlogs', ! empty( $_POST[ 'gzlogs' ] ) );
		update_site_option( 'backwpup_cfg_protectfolders', ! empty( $_POST[ 'protectfolders' ] ) );
		$_POST[ 'jobrunauthkey' ] = preg_replace( '/[^a-zA-Z0-9]/', '', trim( $_POST[ 'jobrunauthkey' ] ) );
		update_site_option( 'backwpup_cfg_jobrunauthkey', $_POST[ 'jobrunauthkey' ] );
		$_POST[ 'logfolder' ] = trailingslashit( str_replace( '\\', '/', trim( stripslashes( sanitize_text_field( $_POST[ 'logfolder' ] ) ) ) ) );
		//set def. folders
		if ( empty( $_POST[ 'logfolder' ] ) || $_POST[ 'logfolder' ] === '/' ) {
			delete_site_option( 'backwpup_cfg_logfolder' );
			BackWPup_Option::default_site_options();
		} else {
			update_site_option( 'backwpup_cfg_logfolder', $_POST[ 'logfolder' ] );
		}

		$authentication = get_site_option( 'backwpup_cfg_authentication', array( 'method' => '', 'basic_user' => '', 'basic_password' => '', 'user_id' => 0, 'query_arg' => '' ) );
		$authentication[ 'method' ] = ( in_array( $_POST[ 'authentication_method' ], array( 'user', 'basic', 'query_arg' ), true ) ) ? $_POST[ 'authentication_method' ] : '';
		$authentication[ 'basic_user' ] = sanitize_text_field( $_POST[ 'authentication_basic_user' ] );
		$authentication[ 'basic_password' ] = BackWPup_Encryption::encrypt( (string) $_POST[ 'authentication_basic_password' ] );
		$authentication[ 'query_arg' ] =  sanitize_text_field( $_POST[ 'authentication_query_arg' ] );
		$authentication[ 'user_id' ] = absint( $_POST[ 'authentication_user_id' ] );
		update_site_option( 'backwpup_cfg_authentication', $authentication );
		delete_site_transient( 'backwpup_cookies' );

		do_action( 'backwpup_page_settings_save' );

		BackWPup_Admin::message( __( 'Settings saved', 'backwpup' ) );
	}

	/**
	 * Page Output
	 */
	public static function page() {
		global $wpdb;

		?>
    <div class="wrap" id="backwpup-page">
		<h1><?php echo sprintf( __( '%s &rsaquo; Settings', 'backwpup' ), BackWPup::get_plugin_data( 'name' ) ); ?></h1>
		<?php
		$tabs = array( 'general' => __( 'General', 'backwpup' ), 'job' => __( 'Jobs', 'backwpup' ), 'log' => __( 'Logs', 'backwpup' ), 'net' => __( 'Network', 'backwpup' ), 'apikey' => __( 'API Keys', 'backwpup' ), 'information' => __( 'Information', 'backwpup' ) );
		$tabs = apply_filters( 'backwpup_page_settings_tab', $tabs );
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $id => $name ) {
			echo '<a href="#backwpup-tab-' . esc_attr( $id ) . '" class="nav-tab">' . esc_attr( $name ). '</a>';
		}
		echo '</h2>';
		BackWPup_Admin::display_messages();
		?>

    <form id="settingsform" action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
		<?php wp_nonce_field( 'backwpupsettings_page' ); ?>
        <input type="hidden" name="page" value="backwpupsettings" />
	    <input type="hidden" name="action" value="backwpup" />
    	<input type="hidden" name="anchor" value="#backwpup-tab-general" />

		<div class="table ui-tabs-hide" id="backwpup-tab-general">

			<h3 class="title"><?php _e( 'Display Settings', 'backwpup' ); ?></h3>
            <p><?php _e( 'Do you want to see BackWPup in the WordPress admin bar?', 'backwpup' ); ?></p>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e( 'Admin bar', 'backwpup' ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Admin Bar', 'backwpup' ); ?></span></legend>
                            <label for="showadminbar">
                                <input name="showadminbar" type="checkbox" id="showadminbar" value="1" <?php checked( get_site_option( 'backwpup_cfg_showadminbar' ), TRUE ); ?> />
								<?php _e( 'Show BackWPup links in admin bar.', 'backwpup' ); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e( 'Folder sizes', 'backwpup' ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Folder sizes', 'backwpup' ); ?></span></legend>
                            <label for="showfoldersize">
                                <input name="showfoldersize" type="checkbox" id="showfoldersize" value="1" <?php checked( get_site_option( 'backwpup_cfg_showfoldersize' ), TRUE ); ?> />
								<?php _e( 'Display folder sizes in the files tab when editing a job. (Might increase loading time of files tab.)', 'backwpup' ); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </table>
			<h3 class="title"><?php _e( 'Security', 'backwpup' ); ?></h3>
			<p><?php _e( 'Security option for BackWPup', 'backwpup' ); ?></p>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e( 'Protect folders', 'backwpup' ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Protect folders', 'backwpup' ); ?></span></legend>
                            <label for="protectfolders">
                                <input name="protectfolders" type="checkbox" id="protectfolders" value="1" <?php checked( get_site_option( 'backwpup_cfg_protectfolders' ), TRUE ); ?> />
								<?php _e( 'Protect BackWPup folders ( Temp, Log and Backups ) with <code>.htaccess</code> and <code>index.php</code>', 'backwpup' ); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </table>

			<?php do_action('backwpup_page_settings_tab_generel'); ?>

		</div>

        <div class="table ui-tabs-hide" id="backwpup-tab-log">

            <p><?php _e( 'Every time BackWPup runs a backup job, a log file is being generated. Choose where to store your log files and how many of them.', 'backwpup' ); ?></p>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="logfolder"><?php _e( 'Log file folder', 'backwpup' ); ?></label></th>
                    <td>
                        <input name="logfolder" type="text" id="logfolder" value="<?php echo esc_attr( get_site_option( 'backwpup_cfg_logfolder' ) );?>" class="regular-text code"/>
	                    <p class="description"><?php echo sprintf( __( 'You can use absolute or relative path! Relative path is relative to %s.', 'backwpup' ), '<code>' . trailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) ) .'</code>' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="maxlogs"><?php _e( 'Maximum log files', 'backwpup' ); ?></label></th>
                    <td>
                        <input name="maxlogs" type="number" min="0" step="1" id="maxlogs" value="<?php echo absint( get_site_option( 'backwpup_cfg_maxlogs' ) );?>" class="small-text"/>
	                    <?php _e( 'Maximum log files in folder.', 'backwpup' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e( 'Compression', 'backwpup' ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Compression', 'backwpup' ); ?></span></legend>
                            <label for="gzlogs">
                                <input name="gzlogs" type="checkbox" id="gzlogs" value="1" <?php checked( get_site_option( 'backwpup_cfg_gzlogs' ), TRUE ); ?><?php if ( ! function_exists( 'gzopen' ) ) echo ' disabled="disabled"'; ?> />
								<?php _e( 'Compress log files with GZip.', 'backwpup' ); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
	            <tr>
		            <th scope="row"><?php _e( 'Logging Level', 'backwpup' ); ?></th>
		            <td>
			            <fieldset>
				            <legend class="screen-reader-text"><span><?php _e( 'Logging Level', 'backwpup' ); ?></span></legend>
				            <label for="loglevel">
					            <select name="loglevel" size="1">
						            <option value="normal_translated" <?php selected( get_site_option( 'backwpup_cfg_loglevel', 'normal_translated' ), 'normal_translated' ); ?>><?php _e( 'Normal (translated)', 'backwpup' ); ?></option>
						            <option value="normal" <?php selected( get_site_option( 'backwpup_cfg_loglevel' ), 'normal' ); ?>><?php _e( 'Normal (not translated)', 'backwpup' ); ?></option>
						            <option value="debug_translated" <?php selected( get_site_option( 'backwpup_cfg_loglevel' ), 'debug_translated' ); ?>><?php _e( 'Debug (translated)', 'backwpup' ); ?></option>
						            <option value="debug" <?php selected( get_site_option( 'backwpup_cfg_loglevel' ), 'debug' ); ?>><?php _e( 'Debug (not translated)', 'backwpup' ); ?></option>
					            </select>
				            </label>
				            <p class="description"><?php esc_attr_e( 'Debug log has much more informations than normal logs. It is for support and should be handled carefully. For support is the best to use a not translated log file. Usage of not translated logs can reduce the PHP memory usage too.', 'backwpup' ); ?></p>
			            </fieldset>
		            </td>
	            </tr>
            </table>

        </div>
        <div class="table ui-tabs-hide" id="backwpup-tab-job">

            <p><?php _e( 'There are a couple of general options for backup jobs. Set them here.', 'backwpup' ); ?></p>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="jobstepretry"><?php _e( "Maximum number of retries for job steps", 'backwpup' ); ?></label></th>
                    <td>
                        <input name="jobstepretry" type="number" min="1" step="1" max="99" id="jobstepretry" value="<?php echo absint( get_site_option( 'backwpup_cfg_jobstepretry' ) );?>" class="small-text" />
                    </td>
                </tr>
				<tr>
                    <th scope="row"><?php _e( 'Maximum script execution time', 'backwpup' ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Maximum PHP Script execution time', 'backwpup' ); ?></span></legend>
                            <label for="jobmaxexecutiontime">
                                <input name="jobmaxexecutiontime" type="number" min="0" step="1" max="300" id="jobmaxexecutiontime" value="<?php echo absint( get_site_option( 'backwpup_cfg_jobmaxexecutiontime' ) ); ?>" class="small-text" />
								<?php _e( 'seconds.', 'backwpup' ); ?>
	                            <p class="description"><?php _e( 'Job will restart before hitting maximum execution time. Restarts will be disabled on CLI usage. If <code>ALTERNATE_WP_CRON</code> has been defined, WordPress Cron will be used for restarts, so it can take a while. 0 means no maximum.', 'backwpup' ); ?></p>
							</label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jobrunauthkey"><?php _e( 'Key to start jobs externally with an URL', 'backwpup' ); ?></label>
                    </th>
                    <td>
                        <input name="jobrunauthkey" type="text" id="jobrunauthkey" value="<?php echo esc_attr( get_site_option( 'backwpup_cfg_jobrunauthkey' ) );?>" class="text code"/>
	                    <p class="description"><?php _e( 'Will be used to protect job starts from unauthorized person.', 'backwpup' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e( 'Reduce server load', 'backwpup' ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Reduce server load', 'backwpup' ); ?></span></legend>
                            <label for="jobwaittimems">
								<select name="jobwaittimems" size="1">
									<option value="0" <?php selected( get_site_option( 'backwpup_cfg_jobwaittimems' ), 0 ); ?>><?php _e( 'disabled', 'backwpup' ); ?></option>
                                    <option value="10000" <?php selected( get_site_option( 'backwpup_cfg_jobwaittimems' ), 10000 ); ?>><?php _e( 'minimum', 'backwpup' ); ?></option>
                                    <option value="30000" <?php selected( get_site_option( 'backwpup_cfg_jobwaittimems' ), 30000 ); ?>><?php _e( 'medium', 'backwpup' ); ?></option>
                                    <option value="90000" <?php selected( get_site_option( 'backwpup_cfg_jobwaittimems' ), 90000 ); ?>><?php _e( 'maximum', 'backwpup' ); ?></option>
                                </select>
                            </label>
	                        <p class="description"><?php _e( 'This adds short pauses to the process. Can be used to reduce the CPU load.', 'backwpup' ); ?></p>
                        </fieldset>
                    </td>
                </tr>
	            <tr>
		            <th scope="row"><?php _e( 'Empty output on working', 'backwpup' ); ?></th>
		            <td>
			            <fieldset>
				            <legend class="screen-reader-text"><span><?php _e( 'Enable an empty Output on backup working.', 'backwpup' ); ?></span></legend>
				            <label for="jobdooutput">
					            <input name="jobdooutput" type="checkbox" id="jobdooutput" value="1" <?php checked( get_site_option( 'backwpup_cfg_jobdooutput' ), TRUE ); ?> />
					            <?php _e( 'Enable an empty Output on backup working.', 'backwpup' ); ?>
				            </label>
				            <p class="description"><?php _e( 'This do an empty output on job working. This can help in some situations or can brake the working. You must test it.', 'backwpup' ); ?></p>
			            </fieldset>
		            </td>
	            </tr>
            </table>

        </div>

        <div class="table ui-tabs-hide" id="backwpup-tab-net">

			<h3><?php echo sprintf( __( 'Authentication for <code>%s</code>', 'backwpup' ), site_url( 'wp-cron.php' ) ); ?></h3>
            <p><?php _e( 'If you protected your blog with HTTP basic authentication (.htaccess), or you use a Plugin to secure wp-cron.php, than use the authentication methods below.', 'backwpup' ); ?></p>
            <?php
                $authentication = get_site_option( 'backwpup_cfg_authentication', array( 'method' => '', 'basic_user' => '', 'basic_password' => '', 'user_id' => 0, 'query_arg' => '' ) );
            ?>
	        <table class="form-table">
	            <tr>
		            <th scope="row"><?php _e( 'Authentication method', 'backwpup' ); ?></th>
		            <td>
			            <fieldset>
				            <legend class="screen-reader-text"><span><?php _e( 'Authentication method', 'backwpup' ); ?></span></legend>
				            <label for="authentication_method">
					            <select name="authentication_method" id="authentication_method" size="1" >
						            <option value="" <?php selected( $authentication[ 'method' ], '' ); ?>><?php _e( 'none', 'backwpup' ); ?></option>
						            <option value="basic" <?php selected( $authentication[ 'method' ], 'basic' ); ?>><?php _e( 'Basic auth', 'backwpup' ); ?></option>
						            <option value="user" <?php selected( $authentication[ 'method' ], 'user' ); ?>><?php _e( 'WordPress User', 'backwpup' ); ?></option>
						            <option value="query_arg" <?php selected( $authentication[ 'method' ], 'query_arg' ); ?>><?php _e( 'Query argument', 'backwpup' ); ?></option>
					            </select>
				            </label>
			            </fieldset>
		            </td>
	            </tr>
                <tr class="authentication_basic" <?php if ( $authentication[ 'method' ] !== 'basic' ) echo 'style="display:none"'; ?>>
                    <th scope="row"><label for="authentication_basic_user"><?php _e( 'Basic Auth Username:', 'backwpup' ); ?></label></th>
                    <td>
                        <input name="authentication_basic_user" type="text" id="authentication_basic_user" value="<?php echo esc_attr( $authentication[ 'basic_user' ] );?>" class="regular-text" autocomplete="off" />
                    </td>
                </tr>
                <tr class="authentication_basic" <?php if ( $authentication[ 'method' ] !== 'basic' ) echo 'style="display:none"'; ?>>
			        <th scope="row"><label for="authentication_basic_password"><?php _e( 'Basic Auth Password:', 'backwpup' ); ?></label></th>
			        <td>
				        <input name="authentication_basic_password" type="password" id="authentication_basic_password" value="<?php echo esc_attr( BackWPup_Encryption::decrypt( $authentication[ 'basic_password' ] ) );?>" class="regular-text" autocomplete="off" />
		        </tr>
		        <tr class="authentication_user" <?php if ( $authentication[ 'method' ] !== 'user' ) echo 'style="display:none"'; ?>>
			        <th scope="row"><?php _e( 'Select WordPress User', 'backwpup' ); ?></th>
			        <td>
				        <fieldset>
					        <legend class="screen-reader-text"><span><?php _e( 'Select WordPress User', 'backwpup' ); ?></span>
					        </legend>
					        <label for="authentication_user_id">
						        <select name="authentication_user_id" size="1" >
							        <?php
							        $users = get_users( array( 'who' => 'administrators', 'number' => 99, 'orderby' => 'display_name' ) );
							        foreach ( $users as $user ) {
								        echo '<option value="' . $user->ID . '" '. selected( $authentication[ 'user_id' ], $user->ID, FALSE ) .'>'. esc_attr( $user->display_name ) .'</option>';
							        }
							        ?>
						        </select>
					        </label>
				        </fieldset>
			        </td>
		        </tr>
		        <tr class="authentication_query_arg" <?php if ( $authentication[ 'method' ] != 'query_arg' ) echo 'style="display:none"'; ?>>
			        <th scope="row"><label for="authentication_query_arg"><?php _e( 'Query arg key=value:', 'backwpup' ); ?></label></th>
			        <td>
				        ?<input name="authentication_query_arg" type="text" id="authentication_query_arg" value="<?php echo esc_attr( $authentication[ 'query_arg' ] );?>" class="regular-text" />
			        </td>
		        </tr>
            </table>

        </div>

        <div class="table ui-tabs-hide" id="backwpup-tab-apikey">

			<?php do_action( 'backwpup_page_settings_tab_apikey' ); ?>

        </div>

        <div class="table ui-tabs-hide" id="backwpup-tab-information">
			<br />
			<?php
			echo '<table class="wp-list-table widefat fixed" cellspacing="0" style="width:85%;margin-left:auto;margin-right:auto;">';
			echo '<thead><tr><th width="35%">' . __( 'Setting', 'backwpup' ) . '</th><th>' . __( 'Value', 'backwpup' ) . '</th></tr></thead>';
			echo '<tfoot><tr><th>' . __( 'Setting', 'backwpup' ) . '</th><th>' . __( 'Value', 'backwpup' ) . '</th></tr></tfoot>';
			echo '<tr title="&gt;=3.2"><td>' . __( 'WordPress version', 'backwpup' ) . '</td><td>' . esc_html( BackWPup::get_plugin_data( 'wp_version' ) ) . '</td></tr>';
			if ( ! class_exists( 'BackWPup_Pro', FALSE ) )
				echo '<tr title=""><td>' . __( 'BackWPup version', 'backwpup' ) . '</td><td>' . esc_html( BackWPup::get_plugin_data( 'Version' ) ) . ' <a href="' . __( 'http://backwpup.com', 'backwpup' ) . '">' . __( 'Get pro.', 'backwpup' ) . '</a></td></tr>';
			else
				echo '<tr title=""><td>' . __( 'BackWPup Pro version', 'backwpup' ) . '</td><td>' . esc_html( BackWPup::get_plugin_data( 'Version' ) ) . '</td></tr>';
			$bit = '';
			if ( PHP_INT_SIZE === 4 ) {
				$bit = ' (32bit)';
			}
			if ( PHP_INT_SIZE === 8 ) {
				$bit = ' (64bit)';
			}
			echo '<tr title="&gt;=5.3.3"><td>' . __( 'PHP version', 'backwpup' ) . '</td><td>' . esc_html( PHP_VERSION . ' ' . $bit ) . '</td></tr>';
			echo '<tr title="&gt;=5.0.7"><td>' . __( 'MySQL version', 'backwpup' ) . '</td><td>' . esc_html( $wpdb->get_var( "SELECT VERSION() AS version" ) ) . '</td></tr>';
			if ( function_exists( 'curl_version' ) ) {
				$curlversion = curl_version();
				echo '<tr title=""><td>' . __( 'cURL version', 'backwpup' ) . '</td><td>' . esc_html( $curlversion[ 'version' ] ) . '</td></tr>';
				echo '<tr title=""><td>' . __( 'cURL SSL version', 'backwpup' ) . '</td><td>' . esc_html( $curlversion[ 'ssl_version' ] ) . '</td></tr>';
			}
			else {
				echo '<tr title=""><td>' . __( 'cURL version', 'backwpup' ) . '</td><td>' . __( 'unavailable', 'backwpup' ) . '</td></tr>';
			}
			echo '<tr title=""><td>' . __( 'WP-Cron url:', 'backwpup' ) . '</td><td>' . site_url( 'wp-cron.php' ) . '</td></tr>';
			//response test
			echo '<tr><td>' . __( 'Server self connect:', 'backwpup' ) . '</td><td>';
			$raw_response = BackWPup_Job::get_jobrun_url( 'test' );
			$response_code = wp_remote_retrieve_response_code( $raw_response );
			$response_body = wp_remote_retrieve_body( $raw_response );
			if ( strstr( $response_body, 'BackWPup test request' ) === false ) {
				$test_result = __( '<strong>Not expected HTTP response:</strong><br>','backwpup' );
				if ( ! $response_code ) {
					$test_result .= sprintf( __( 'WP Http Error: <code>%s</code>', 'backwpup' ), esc_html( $raw_response->get_error_message() ) ) . '<br>';
				} else {
					$test_result .= sprintf( __( 'Status-Code: <code>%d</code>', 'backwpup' ), esc_html( $response_code ) ) . '<br>';
				}
				$response_headers = wp_remote_retrieve_headers( $raw_response );
				foreach( $response_headers as $key => $value ) {
					$test_result .= esc_html( ucfirst( $key ) ) . ': <code>' . esc_html( $value ) . '</code><br>';
				}
				$content = esc_html( wp_remote_retrieve_body( $raw_response ) );
				if ( $content ) {
					$test_result .= sprintf( __( 'Content: <code>%s</code>', 'backwpup' ), $content );
				}
				echo $test_result;
			} else {
				_e( 'Response Test O.K.', 'backwpup' );
			}
			echo '</td></tr>';
			//folder test
			echo '<tr><td>' . __( 'Temp folder:', 'backwpup' ) . '</td><td>';
			if ( ! is_dir( BackWPup::get_plugin_data( 'TEMP' ) ) ) {
				echo sprintf( __( 'Temp folder %s doesn\'t exist.', 'backwpup' ), esc_html( BackWPup::get_plugin_data( 'TEMP' ) ) );
			} elseif ( ! is_writable( BackWPup::get_plugin_data( 'TEMP' ) ) ) {
				echo sprintf( __( 'Temporary folder %s is not writable.', 'backwpup' ), esc_html( BackWPup::get_plugin_data( 'TEMP' ) ) );
			} else {
				echo esc_html( BackWPup::get_plugin_data( 'TEMP' ) );
			}
			echo '</td></tr>';
			$log_folder = esc_html( get_site_option( 'backwpup_cfg_logfolder' ) );
			$log_folder = BackWPup_File::get_absolute_path( $log_folder );
			echo '<tr><td>' . __( 'Log folder:', 'backwpup' ) . '</td><td>';
			if ( ! is_dir( $log_folder ) ) {
				echo sprintf( __( 'Logs folder %s not exist.','backwpup' ), $log_folder );
			} elseif ( ! is_writable( $log_folder ) ) {
				echo sprintf( __( 'Log folder %s is not writable.','backwpup' ), $log_folder );
			} else {
				echo $log_folder;
			}
			echo '</td></tr>';
			echo '<tr title=""><td>' . __( 'Server', 'backwpup' ) . '</td><td>' . esc_html( $_SERVER[ 'SERVER_SOFTWARE' ] ) . '</td></tr>';
			echo '<tr title=""><td>' . __( 'Operating System', 'backwpup' ) . '</td><td>' . esc_html( PHP_OS ) . '</td></tr>';
			echo '<tr title=""><td>' . __( 'PHP SAPI', 'backwpup' ) . '</td><td>' . esc_html( PHP_SAPI ) . '</td></tr>';
			$php_user = __( 'Function Disabled', 'backwpup' );
			if ( function_exists( 'get_current_user' ) ) {
				$php_user = get_current_user();
			}
			echo '<tr title=""><td>' . __( 'Current PHP user', 'backwpup' ) . '</td><td>' . esc_html( $php_user )  . '</td></tr>';
			$text  = (bool) ini_get( 'safe_mode' ) ? __( 'On', 'backwpup' ) : __( 'Off', 'backwpup' );
			echo '<tr title=""><td>' . __( 'Safe Mode', 'backwpup' ) . '</td><td>' . $text . '</td></tr>';
			echo '<tr title="&gt;=30"><td>' . __( 'Maximum execution time', 'backwpup' ) . '</td><td>' . esc_html( ini_get( 'max_execution_time' ) ) . ' ' . __( 'seconds', 'backwpup' ) . '</td></tr>';
			if ( defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON )
				echo '<tr title="ALTERNATE_WP_CRON"><td>' . __( 'Alternative WP Cron', 'backwpup' ) . '</td><td>' . __( 'On', 'backwpup' ) . '</td></tr>';
			else
				echo '<tr title="ALTERNATE_WP_CRON"><td>' . __( 'Alternative WP Cron', 'backwpup' ) . '</td><td>' . __( 'Off', 'backwpup' ) . '</td></tr>';
			if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON )
				echo '<tr title="DISABLE_WP_CRON"><td>' . __( 'Disabled WP Cron', 'backwpup' ) . '</td><td>' . __( 'On', 'backwpup' ) . '</td></tr>';
			else
				echo '<tr title="DISABLE_WP_CRON"><td>' . __( 'Disabled WP Cron', 'backwpup' ) . '</td><td>' . __( 'Off', 'backwpup' ) . '</td></tr>';
			if ( defined( 'FS_CHMOD_DIR' ) )
				echo '<tr title="FS_CHMOD_DIR"><td>' . __( 'CHMOD Dir', 'backwpup' ) . '</td><td>' . esc_html( FS_CHMOD_DIR ) . '</td></tr>';
			else
				echo '<tr title="FS_CHMOD_DIR"><td>' . __( 'CHMOD Dir', 'backwpup' ) . '</td><td>0755</td></tr>';
			$now = localtime( time(), TRUE );
			echo '<tr title=""><td>' . __( 'Server Time', 'backwpup' ) . '</td><td>' . esc_html( $now[ 'tm_hour' ] . ':' . $now[ 'tm_min' ] ) . '</td></tr>';
			echo '<tr title=""><td>' . __( 'Blog Time', 'backwpup' ) . '</td><td>' . date( 'H:i', current_time( 'timestamp' ) ) . '</td></tr>';
			echo '<tr title=""><td>' . __( 'Blog Timezone', 'backwpup' ) . '</td><td>' . esc_html( get_option( 'timezone_string' ) ) . '</td></tr>';
			echo '<tr title=""><td>' . __( 'Blog Time offset', 'backwpup' ) . '</td><td>' . sprintf( __( '%s hours', 'backwpup' ), (int) get_option( 'gmt_offset' ) ) . '</td></tr>';
			echo '<tr title="WPLANG"><td>' . __( 'Blog language', 'backwpup' ) . '</td><td>' . get_bloginfo( 'language' ) . '</td></tr>';
			echo '<tr title="utf8"><td>' . __( 'MySQL Client encoding', 'backwpup' ) . '</td><td>';
			echo defined( 'DB_CHARSET' ) ? DB_CHARSET : '';
			echo '</td></tr>';
			echo '<tr title="URF-8"><td>' . __( 'Blog charset', 'backwpup' ) . '</td><td>' . get_bloginfo( 'charset' ) . '</td></tr>';
			echo '<tr title="&gt;=128M"><td>' . __( 'PHP Memory limit', 'backwpup' ) . '</td><td>' . esc_html( ini_get( 'memory_limit' ) ) . '</td></tr>';
			echo '<tr title="WP_MEMORY_LIMIT"><td>' . __( 'WP memory limit', 'backwpup' ) . '</td><td>' . esc_html( WP_MEMORY_LIMIT ) . '</td></tr>';
			echo '<tr title="WP_MAX_MEMORY_LIMIT"><td>' . __( 'WP maximum memory limit', 'backwpup' ) . '</td><td>' . esc_html( WP_MAX_MEMORY_LIMIT ) . '</td></tr>';
			echo '<tr title=""><td>' . __( 'Memory in use', 'backwpup' ) . '</td><td>' . size_format( @memory_get_usage( TRUE ), 2 ) . '</td></tr>';
			//disabled PHP functions
			$disabled = esc_html( ini_get( 'disable_functions' ) );
			if ( ! empty( $disabled ) ) {
				$disabledarry = explode( ',', $disabled );
				echo '<tr title=""><td>' . __( 'Disabled PHP Functions:', 'backwpup' ) . '</td><td>';
				echo implode( ', ', $disabledarry );
				echo '</td></tr>';
			}
			//Loaded PHP Extensions
			echo '<tr title=""><td>' . __( 'Loaded PHP Extensions:', 'backwpup' ) . '</td><td>';
			$extensions = get_loaded_extensions();
			sort( $extensions );
			echo  esc_html( implode( ', ', $extensions ) );
			echo '</td></tr>';
			echo '</table>'
			?>
        </div>

		<?php do_action( 'backwpup_page_settings_tab_content' ); ?>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e( 'Save Changes', 'backwpup' ); ?>" />
			&nbsp;
			<input type="submit" name="default_settings" id="default_settings" class="button-secondary" value="<?php _e( 'Reset all settings to default', 'backwpup' ); ?>" />
        </p>
    </form>
    </div>
	<?php
	}

}
