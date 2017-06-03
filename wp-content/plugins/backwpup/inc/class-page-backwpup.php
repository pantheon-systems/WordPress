<?php
/**
 * Render plugin dashboard.
 *
 */
class BackWPup_Page_BackWPup {


	/**
	 * Called on load action.
	 *
	 * @return void
	 */
	public static function load() {
		global $wpdb;

		if ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'dbdumpdl' ) {

			//check permissions
			check_admin_referer( 'backwpupdbdumpdl' );

			if ( ! current_user_can( 'backwpup_jobs_edit' ) )
				die();

			//doing dump
			header( "Pragma: public" );
			header( "Expires: 0" );
			header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
			header( "Content-Type: application/octet-stream; charset=". get_bloginfo( 'charset' ) );
			header( "Content-Disposition: attachment; filename=" . DB_NAME . ".sql;" );
			try {
				$sql_dump = new BackWPup_MySQLDump();
				foreach ( $sql_dump->tables_to_dump as $key => $table ) {
					if ( $wpdb->prefix != substr( $table,0 , strlen( $wpdb->prefix ) ) )
						unset( $sql_dump->tables_to_dump[ $key ] );
				}
				$sql_dump->execute();
				unset( $sql_dump );
			} catch ( Exception $e ) {
				die( $e->getMessage() );
			}
			die();
		}
	}

	/**
	 * Enqueue script.
	 *
	 * @return void
	 */
	public static function admin_print_scripts() {

		wp_enqueue_script( 'backwpupgeneral' );

	}

	/**
	 * Print the markup.
	 *
	 * @return void
	 */
	public static function page() {
		// get wizards
		$wizards = BackWPup::get_wizards();
		?>
        <div class="wrap" id="backwpup-page">
            <h1><?php echo sprintf( __( '%s &rsaquo; Dashboard', 'backwpup' ), BackWPup::get_plugin_data( 'name') ); ?></h1>
			<?php

			BackWPup_Admin::display_messages();

			if ( class_exists( 'BackWPup_Pro', FALSE ) ) { ?>
				<div class="backwpup-welcome backwpup-max-width">
					<h3><?php _ex( 'Planning backups', 'Dashboard heading', 'backwpup' ); ?></h3>
					<p><?php _e('BackWPup’s job wizards make planning and scheduling your backup jobs a breeze.','backwpup' ); echo ' '; _e('Use your backup archives to save your entire WordPress installation including <code>/wp-content/</code>. Push them to an external storage service if you don’t want to save the backups on the same server.','backwpup'); ?></p>
					<h3><?php _ex( 'Restoring backups', 'Dashboard heading', 'backwpup' ); ?></h3>
					<p><?php _e( 'With a single backup archive you are able to restore an installation. Use a tool like phpMyAdmin or a plugin like <a href="http://wordpress.org/plugins/adminer/" target="_blank">Adminer</a> to restore your database backup files.', 'backwpup' ) ?></p>
					<h3><?php _ex( 'Ready to set up a backup job?', 'Dashboard heading','backwpup' ); ?></h3>
					<p><?php printf( __('Use one of the wizards to plan a backup, or use <a href="%s">expert mode</a> for full control over all options.','backwpup'), network_admin_url( 'admin.php') . '?page=backwpupeditjob' ); echo ' '; _e( '<strong>Please note: You are solely responsible for the security of your data; the authors of this plugin are not.</strong>', 'backwpup' ); ?></p>
				</div>
			<?php } else {?>
				<div class="backwpup-welcome backwpup-max-width">
					<h3><?php _ex( 'Planning backups', 'Dashboard heading', 'backwpup' ); ?></h3>
					<p><?php _e('Use the short links in the <strong>First steps</strong> box to plan and schedule backup jobs.','backwpup' ); echo ' '; _e('Use your backup archives to save your entire WordPress installation including <code>/wp-content/</code>. Push them to an external storage service if you don’t want to save the backups on the same server.','backwpup'); ?></p>
					<h3><?php _ex( 'Restoring backups', 'Dashboard heading', 'backwpup' ); ?></h3>
					<p><?php _e( 'With a single backup archive you are able to restore an installation. Use a tool like phpMyAdmin or a plugin like <a href="http://wordpress.org/plugins/adminer/" target="_blank">Adminer</a> to restore your database backup files.', 'backwpup' ) ?></p>
					<h3><?php _ex( 'Ready to set up a backup job?', 'Dashboard heading','backwpup' ); ?></h3>
					<p><?php printf( __('<a href="%s">Add a new backup job</a> and plan what you want to save.','backwpup'), network_admin_url( 'admin.php') . '?page=backwpupeditjob' ); ?>
					<br /><?php _e( '<strong>Please note: You are solely responsible for the security of your data; the authors of this plugin are not.</strong>', 'backwpup' ); ?></p>
				</div>
			<?php }

			if ( current_user_can( 'backwpup_jobs_edit' ) && current_user_can( 'backwpup_logs' ) && current_user_can( 'backwpup_jobs_start' ) ) {
			?>
				<div  id="backwpup-first-steps" class="metabox-holder postbox backwpup-floated-postbox">
					<h3 class="hndle"><span><?php  _e( 'First Steps', 'backwpup' ); ?></span></h3>
					<div class="inside">
						<ul>
							<?php if ( class_exists( 'BackWPup_Pro', FALSE ) ) { ?>
								<li type="1"><a href="<?php echo wp_nonce_url( network_admin_url( 'admin.php' ) . '?page=backwpupwizard&wizard_start=SYSTEMTEST', 'wizard' ); ?>"><?php  _e( 'Test the installation', 'backwpup' ); ?></a></li>
								<li type="1"><a href="<?php echo wp_nonce_url( network_admin_url( 'admin.php' ) . '?page=backwpupwizard&wizard_start=JOB', 'wizard' ); ?>"><?php  _e( 'Create a Job', 'backwpup' ); ?></a></li>
							<?php } else { ?>
                           		<li type="1"><a href="<?php echo network_admin_url( 'admin.php' ) . '?page=backwpupsettings#backwpup-tab-information'; ?>"><?php  _e( 'Check the installation', 'backwpup' ); ?></a></li>
                            	<li type="1"><a href="<?php echo network_admin_url( 'admin.php' ) . '?page=backwpupeditjob'; ?>"><?php  _e( 'Create a Job', 'backwpup' ); ?></a></li>
							<?php } ?>
							<li type="1"><a href="<?php echo network_admin_url( 'admin.php' ) . '?page=backwpupjobs'; ?>"><?php  _e( 'Run the created job', 'backwpup' ); ?></a></li>
							<li type="1"><a href="<?php echo network_admin_url( 'admin.php' ) . '?page=backwpuplogs'; ?>"><?php  _e( 'Check the job log', 'backwpup' ); ?></a></li>
						</ul>
					</div>
				</div>
			<?php }

			if ( current_user_can( 'backwpup_jobs_start' ) ) {?>
				<div id="backwpup-one-click-backup" class="metabox-holder postbox backwpup-floated-postbox">
					<h3 class="hndle"><span><?php esc_html_e( 'One click backup', 'backwpup' ); ?></span></h3>
					<div class="inside">
						<a href="<?php echo wp_nonce_url( network_admin_url( 'admin.php?page=backwpup&action=dbdumpdl' ), 'backwpupdbdumpdl' ); ?>" class="button button-primary button-primary-bwp" title="<?php esc_attr_e( 'Generate a database backup of WordPress tables and download it right away!', 'backwpup' ); ?>"><?php esc_html_e( 'Download database backup', 'backwpup' ); ?></a><br />
					</div>
				</div>
			<?php } ?>

			<div id="backwpup-rss-feed" class="metabox-holder postbox backwpup-cleared-postbox backwpup-max-width">
				<h3 class="hndle"><span><?php esc_attr_e( 'BackWPup News', 'backwpup' ); ?></span></h3>
				<div class="inside">
					<?php

						$rss = fetch_feed( _x( 'http://marketpress.com/news/tag/backwpup/feed/', 'BackWPup News RSS Feed URL', 'backwpup' ) );

						if ( is_wp_error( $rss ) ) {
							echo '<p>' . sprintf( __('<strong>RSS Error</strong>: %s', 'backwpup' ), $rss->get_error_message() ) . '</p>';
						} elseif ( ! $rss->get_item_quantity() ) {
							echo '<ul><li>' . esc_html__( 'An error has occurred, which probably means the feed is down. Try again later.', 'backwpup' ) . '</li></ul>';
							$rss->__destruct();
							unset( $rss );
						} else {
							echo '<ul>';
							$first = TRUE;
							foreach ( $rss->get_items( 0, 4 ) as $item ) {
								$link = $item->get_link();
								while ( stristr($link, 'http') != $link ) {
									$link = substr($link, 1);
								}
								$link = esc_url(strip_tags($link));
								$title = esc_attr(strip_tags($item->get_title()));
								if ( empty($title) ) {
									$title = __( 'Untitled', 'backwpup' );
								}

								$desc = str_replace( array("\n", "\r"), ' ', esc_attr( strip_tags( @html_entity_decode( $item->get_description(), ENT_QUOTES, get_option( 'blog_charset' ) ) ) ) );
								$excerpt = wp_html_excerpt( $desc, 360 );

								// Append ellipsis. Change existing [...] to [&hellip;].
								if ( '[...]' == substr( $excerpt, -5 ) )
									$excerpt = substr( $excerpt, 0, -5 ) . '[&hellip;]';
								elseif ( '[&hellip;]' != substr( $excerpt, -10 ) && $desc != $excerpt )
									$excerpt .= ' [&hellip;]';

								$excerpt = esc_html( $excerpt );

								if ( $first ) {
									$summary = "<div class='rssSummary'>$excerpt</div>";
								} else {
									$summary = '';
								}

								$date = '';
								if ( $first ) {
									$date = $item->get_date( 'U' );

									if ( $date ) {
										$date = ' <span class="rss-date">' . date_i18n( get_option( 'date_format' ), $date ) . '</span>';
									}
								}

								echo "<li><a href=\"$link\" title=\"$desc\">$title</a>{$date}{$summary}</li>";
								$first = FALSE;
							}
							echo '</ul>';
							$rss->__destruct();
							unset($rss);
						}
					?>
				</div>
			</div>

			<?php
			if ( class_exists( 'BackWPup_Pro', FALSE ) ) {
				/* @var BackWPup_Pro_Wizards $wizard_class */

				foreach ( $wizards as $wizard_class ) {
					//check permissions
					if ( ! current_user_can( $wizard_class->info[ 'cap' ] ) )
						continue;
					//get info of wizard
					echo '<div id="wizard-' . strtolower( $wizard_class->info[ 'ID' ] ) . '" class="wizardbox post-box backwpup-floated-postbox"><form method="get" action="' . network_admin_url( 'admin.php' ) . '">';
					echo '<h3 class="wizardbox_name">' . $wizard_class->info[ 'name' ] . '</h3>';
					echo '<p class="wizardbox_description">' . $wizard_class->info[ 'description' ] . '</p>';
					$conf_names = $wizard_class->get_pre_configurations();
					if ( ! empty ( $conf_names ) ) {
						echo '<select id="wizardbox_pre_conf" name="pre_conf" size="1">';
						foreach( $conf_names as $conf_key => $conf_name) {
							echo '<option value="' . esc_attr( $conf_key ) . '">' . esc_attr( $conf_name ) . '</option>';
						}
						echo '</select>';
					} else {
						echo '<input type="hidden" name="pre_conf" value="" />';
					}
					wp_nonce_field( 'wizard' );
					echo '<input type="hidden" name="page" value="backwpupwizard" />';
					echo '<input type="hidden" name="wizard_start" value="' . esc_attr( $wizard_class->info[ 'ID' ] ) . '" />';
					echo '<div class="wizardbox_start"><input type="submit" name="submit" class="button button-primary button-primary-bwp" value="' . esc_attr( __( 'Start wizard', 'backwpup' ) ) . '" /></div>';
					echo '</form></div>';
				}
			} ?>

	        <div class="metabox-holder postbox backwpup-cleared-postbox backwpup-floated-postbox">
		        <h3 class="hndle"><span><a href="https://www.ostraining.com/">OSTraining</a> <?php  esc_html_e( 'Video: Introduction', 'backwpup' ); ?></span></h3>
		        <iframe class="inside" width="340" height="190" src="https://www.youtube.com/embed/pECMkLE27QQ?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
	        </div>

	        <div class="metabox-holder postbox backwpup-floated-postbox">
		        <h3 class="hndle"><span><a href="https://www.ostraining.com/">OSTraining</a> <?php  esc_html_e( 'Video: Settings', 'backwpup' ); ?></span></h3>
		        <iframe class="inside" width="340" height="190" src="https://www.youtube.com/embed/F55xEoDnS0U?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
	        </div>

	        <div class="metabox-holder postbox backwpup-cleared-postbox backwpup-floated-postbox">
		        <h3 class="hndle"><span><a href="https://www.ostraining.com/">OSTraining</a> <?php  esc_html_e( 'Video: Daily Backups', 'backwpup' ); ?></span></h3>
		        <iframe class="inside" width="340" height="190" src="https://www.youtube.com/embed/staZo0DS5m4?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
	        </div>

	        <div class="metabox-holder postbox backwpup-floated-postbox">
		        <h3 class="hndle"><span><a href="https://www.ostraining.com/">OSTraining</a> <?php  esc_html_e( 'Video: Creating Full Backups', 'backwpup' ); ?></span></h3>
		        <iframe class="inside" width="340" height="190" src="https://www.youtube.com/embed/3N9FbmBuaac?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
	        </div>

	        <div class="metabox-holder postbox backwpup-cleared-postbox backwpup-floated-postbox">
		        <h3 class="hndle"><span><a href="https://www.ostraining.com/">OSTraining</a> <?php  esc_html_e( 'Video: Restoring Backups', 'backwpup' ); ?></span></h3>
		        <iframe class="inside" width="340" height="190" src="https://www.youtube.com/embed/VIwDp87vYZY?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
	        </div>

			<div id="backwpup-stats" class="metabox-holder postbox backwpup-cleared-postbox backwpup-max-width">
				<div class="backwpup-table-wrap">
				<?php
					self::mb_next_jobs();
					self::mb_last_logs();
				?>
				</div>
			</div>

			<?php if ( ! class_exists( 'BackWPup_Pro', FALSE ) ) { ?>
			<div id="backwpup-thank-you" class="metabox-holder postbox backwpup-cleared-postbox backwpup-max-width">
				<h3 class="hndle"><span><?php  _ex( 'Thank you for using BackWPup!', 'Pro teaser box', 'backwpup' ); ?></span></h3>
				<div class="inside">
					<p><img class="backwpup-banner-img" src="<?php echo BackWPup::get_plugin_data( 'URL' ) . '/assets/images/backwpupbanner-pro.png'; ?>" alt="BackWPup Banner" /></p>
					<h3 class="backwpup-text-center"><?php _ex( 'Get access to:', 'Pro teaser box', 'backwpup' ); ?></h3>
					<ul class="backwpup-text-center">
						<li><?php _ex( 'First-class <strong>dedicated support</strong> at MarketPress Helpdesk.', 'Pro teaser box', 'backwpup' ); ?></li>
						<li><?php echo esc_html_x( 'Differential backups to Google Drive and other cloud storage service.', 'Pro teaser box', 'backwpup' ); ?></li>
						<li><?php echo esc_html_x( 'Easy-peasy wizards to create and schedule backup jobs.', 'Pro teaser box', 'backwpup' ); ?></li>
						<li><?php printf( '<a href="' . esc_html__( 'http://backwpup.com', 'backwpup' ) .'">%s</a>', _x( 'And more…', 'Pro teaser box, link text', 'backwpup' ) ); ?></li>
					</ul>
					<p class="backwpup-text-center"><a href="<?php esc_html_e( 'http://backwpup.com', 'backwpup' ); ?>" class="button button-primary button-primary-bwp" title="<?php _ex( 'Get BackWPup Pro now', 'Pro teaser box, link title', 'backwpup' ); ?>"><?php _ex( 'Get BackWPup Pro now', 'Pro teaser box, link text', 'backwpup' ); ?></a></p>
				</div>
			</div>
			<?php } ?>

        </div>
	<?php
	}

	/**
	 * Displaying last logs
	 */
	private static function mb_last_logs() {

		if ( ! current_user_can( 'backwpup_logs' ) )
			return;
		?>
		<table class="wp-list-table widefat" cellspacing="0">
			<caption><?php esc_html_e( 'Last logs', 'backwpup' ); ?></caption>
			<thead>
			<tr><th style="width:30%"><?php esc_html_e( 'Time', 'backwpup' ); ?></th><th style="width:55%"><?php  esc_html_e( 'Job', 'backwpup' ); ?></th><th style="width:20%"><?php  esc_html_e( 'Result', 'backwpup' ); ?></th></tr>
			</thead>
			<?php
			//get log files
			$logfiles = array();
			$log_folder = get_site_option( 'backwpup_cfg_logfolder' );
			$log_folder = BackWPup_File::get_absolute_path( $log_folder );
			if ( is_readable( $log_folder ) && $dir = opendir( $log_folder ) ) {
				while ( ( $file = readdir( $dir ) ) !== FALSE ) {
					if ( is_readable( $log_folder . $file ) && is_file( $log_folder . $file ) && FALSE !== strpos( $file, 'backwpup_log_' ) && FALSE !== strpos( $file, '.html' ) ) {
						$logfiles[ filemtime( $log_folder . $file ) ] = $file;
					}
				}
				closedir( $dir );
				krsort( $logfiles, SORT_NUMERIC );
			}

			if ( count( $logfiles ) > 0 ) {
				$count = 0;
				$alternate = TRUE;
				foreach ( $logfiles as $logfile ) {
					$logdata = BackWPup_Job::read_logheader( $log_folder . $logfile );
					if ( ! $alternate ) {
						echo '<tr>';
						$alternate = TRUE;
					} else {
						echo '<tr class="alternate">';
						$alternate = FALSE;
					}
					echo '<td>' . sprintf( __( '%1$s at %2$s', 'backwpup' ), date_i18n( get_option( 'date_format' ) , $logdata[ 'logtime' ] ), date_i18n( get_option( 'time_format' ), $logdata[ 'logtime' ] ) ) . '</td>';
					$log_name = str_replace( array( '.html', '.gz' ), '', basename( $logfile ) );
					echo '<td><a class="thickbox" href="' . admin_url( 'admin-ajax.php' ) . '?&action=backwpup_view_log&log=' . $log_name .'&_ajax_nonce=' . wp_create_nonce( 'view-log_' . $log_name ) . '&amp;TB_iframe=true&amp;width=640&amp;height=440" title="' . esc_attr( basename( $logfile ) ) . '">' . esc_html( $logdata[ 'name' ] ) . '</i></a></td>';
					echo '<td>';
					if ( $logdata[ 'errors' ] ) {
						printf( '<span style="color:red;font-weight:bold;">' . _n( "%d ERROR", "%d ERRORS", $logdata[ 'errors' ], 'backwpup' ) . '</span><br />', $logdata[ 'errors' ] );
					}
					if ( $logdata[ 'warnings' ] ) {
						printf( '<span style="color:#e66f00;font-weight:bold;">' . _n( "%d WARNING", "%d WARNINGS", $logdata[ 'warnings' ], 'backwpup' ) . '</span><br />', $logdata[ 'warnings' ] );
					}
					if ( ! $logdata[ 'errors' ] && ! $logdata[ 'warnings' ] ) {
						echo '<span style="color:green;font-weight:bold;">' . __( 'OK', 'backwpup' ) . '</span>';
					}
					echo '</td></tr>';
					$count ++;
					if ( $count >= 5 )
						break;
				}
			}
			else {
				echo '<tr><td colspan="3">' . __( 'none', 'backwpup' ) . '</td></tr>';
			}
			?>
		</table>
		<?php
	}

	/**
	 * Displaying next jobs
	 */
	private static function mb_next_jobs() {

		if ( ! current_user_can( 'backwpup_jobs' ) )
			return;
		?>
		<table class="wp-list-table widefat" cellspacing="0">
			<caption><?php _e( 'Next scheduled jobs', 'backwpup' ); ?></caption>
			<thead>
			<tr>
				<th style="width: 30%"><?php  esc_html_e( 'Time', 'backwpup' ); ?></th>
				<th style="width: 70%"><?php  esc_html_e( 'Job', 'backwpup' ); ?></th>
			</tr>
			</thead>
			<?php
			//get next jobs
			$mainsactive = BackWPup_Option::get_job_ids( 'activetype', 'wpcron' );
			sort( $mainsactive );
			$alternate = TRUE;
			// add working job if it not in active jobs
			$job_object = BackWPup_Job::get_working_data();
			if ( ! empty( $job_object ) && ! empty( $job_object->job[ 'jobid' ] ) && ! in_array($job_object->job[ 'jobid' ], $mainsactive, true ) )
				$mainsactive[ ] = $job_object->job[ 'jobid' ];
			foreach ( $mainsactive as $jobid ) {
				$name = BackWPup_Option::get( $jobid, 'name' );
				if ( ! empty( $job_object ) && $job_object->job[ 'jobid' ] == $jobid ) {
					$runtime  = current_time( 'timestamp' ) -  $job_object->job[ 'lastrun' ];
					if ( ! $alternate ) {
						echo '<tr>';
						$alternate = TRUE;
					} else {
						echo '<tr class="alternate">';
						$alternate = FALSE;
					}
					echo '<td>' . sprintf( '<span style="color:#e66f00;">' . esc_html__( 'working since %d seconds', 'backwpup' ) . '</span>', $runtime ) . '</td>';
					echo '<td><span style="font-weight:bold;">' . esc_html ( $job_object->job[ 'name' ] ) . '</span><br />';
					echo "<a style=\"color:red;\" href=\"" . wp_nonce_url( network_admin_url( 'admin.php?page=backwpupjobs&action=abort'), 'abort-job' ) . "\">" . esc_html__( 'Abort', 'backwpup' ) . "</a>";
					echo "</td></tr>";
				}
				else {
					if ( ! $alternate ) {
						echo '<tr>';
						$alternate = TRUE;
					} else {
						echo '<tr class="alternate">';
						$alternate = FALSE;
					}
					if ( $nextrun = wp_next_scheduled( 'backwpup_cron', array( 'id' => $jobid ) ) + ( get_option( 'gmt_offset' ) * 3600 ) )
						echo '<td>' . sprintf( __( '%1$s at %2$s', 'backwpup' ), date_i18n( get_option( 'date_format' ), $nextrun, TRUE ), date_i18n( get_option( 'time_format' ), $nextrun, TRUE ) ) . '</td>';
					else
						echo '<td><em>' . esc_html__( 'Not scheduled!', 'backwpup' ) . '</em></td>';

					echo '<td><a href="' . wp_nonce_url( network_admin_url( 'admin.php' ) . '?page=backwpupeditjob&jobid=' . $jobid, 'edit-job' ) . '" title="' . esc_attr( __( 'Edit Job', 'backwpup' ) ) . '">' . esc_html($name) . '</a></td></tr>';
				}
			}
			if ( empty( $mainsactive ) and ! empty( $job_object ) ) {
				echo '<tr><td colspan="2"><i>' . esc_html__( 'none', 'backwpup' ) . '</i></td></tr>';
			}
			?>
		</table>
		<?php
	}

}
