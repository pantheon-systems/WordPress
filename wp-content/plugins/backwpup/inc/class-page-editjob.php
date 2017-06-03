<?php
/**
 *
 */
class BackWPup_Page_Editjob {

	/**
	 *
	 */
	public static function auth() {

		//check $_GET[ 'tab' ]
		if ( isset($_GET[ 'tab' ] ) ) {
			$_GET[ 'tab' ] = sanitize_title_with_dashes( $_GET[ 'tab' ] );
			if ( substr( $_GET[ 'tab' ], 0, 5 ) != 'dest-' && substr( $_GET[ 'tab' ], 0, 8 ) != 'jobtype-'  && ! in_array( $_GET[ 'tab' ], array( 'job','cron' ), true ) )
				$_GET[ 'tab' ] = 'job';
		} else {
			$_GET[ 'tab' ] = 'job';
		}

		if ( substr( $_GET[ 'tab' ], 0, 5 ) == 'dest-' ) {
			$jobid        = (int)$_GET[ 'jobid' ];
			$id = strtoupper( str_replace( 'dest-', '', $_GET[ 'tab' ] ) );
			$dest_class = BackWPup::get_destination( $id );
			$dest_class->edit_auth( $jobid );
		}
	}

	/**
	 * Save Form data
	 */
	public static function save_post_form($tab, $jobid) {

		if ( ! current_user_can( 'backwpup_jobs_edit' ) ) {
			return __( 'Sorry, you don\'t have permissions to do that.', 'backwpup' );
		}

		$job_types = BackWPup::get_job_types();

		switch ( $tab ) {
			case 'job':
				BackWPup_Option::update( $jobid, 'jobid', $jobid );

				//set type of backup
				$backuptype = 'archive';
				if ( class_exists( 'BackWPup_Pro', false ) && $_POST['backuptype'] === 'sync' ) {
					$backuptype = 'sync';
				}
				BackWPup_Option::update( $jobid, 'backuptype', $backuptype );

				$type_post = isset( $_POST['type'] ) ? (array) $_POST['type'] : array();
				//check existing type
				foreach ( $type_post as $key => $value ) {
					if ( ! isset( $job_types[ $value ] ) ) {
						unset( $type_post[ $key ] );
					}
				}
				sort( $type_post );
				BackWPup_Option::update( $jobid, 'type', $type_post );

				//test if job type makes backup
				$makes_file = false;
				/* @var BackWPup_JobTypes $job_type */
				foreach ( $job_types as $type_id => $job_type ) {
					if ( in_array( $type_id, $type_post, true ) ) {
						if ( $job_type->creates_file() ) {
							$makes_file = true;
							break;
						}
					}
				}

				if ( $makes_file ) {
					$destinations_post = isset( $_POST['destinations'] ) ? (array) $_POST['destinations'] : array();
				} else {
					$destinations_post = array();
				}

				$destinations = BackWPup::get_registered_destinations();
				foreach ( $destinations_post as $key => $dest_id ) {
					//remove all destinations that not exists
					if ( ! isset( $destinations[ $dest_id ] ) ) {
						unset( $destinations_post[ $key ] );
						continue;
					}
					//if sync remove all not sync destinations
					if ( $backuptype === 'sync' ) {
						if ( ! $destinations[ $dest_id ]['can_sync'] ) {
							unset( $destinations_post[ $key ] );
						}
					}
				}
				sort( $destinations_post );
				BackWPup_Option::update( $jobid, 'destinations', $destinations_post );

				$name = sanitize_text_field( trim( $_POST['name'] ) );
				if ( ! $name || $name === __( 'New Job', 'backwpup' ) ) {
					$name = sprintf( __( 'Job with ID %d', 'backwpup' ), $jobid );
				}
				BackWPup_Option::update( $jobid, 'name', $name );

				$emails = explode( ',', sanitize_text_field( $_POST['mailaddresslog'] ) );
				foreach ( $emails as $key => $email ) {
					$emails[ $key ] = sanitize_email( trim( $email ) );
					if ( ! is_email( $emails[ $key ] ) ) {
						unset( $emails[ $key ] );
					}
				}
				$mailaddresslog = implode( ', ', $emails );
				BackWPup_Option::update( $jobid, 'mailaddresslog', $mailaddresslog );

				$mailaddresssenderlog = trim( $_POST['mailaddresssenderlog'] );
				BackWPup_Option::update( $jobid, 'mailaddresssenderlog', $mailaddresssenderlog );

				BackWPup_Option::update( $jobid, 'mailerroronly', ! empty( $_POST['mailerroronly'] ) );

				$archiveformat = in_array( $_POST['archiveformat'], array(
					'.zip',
					'.tar',
					'.tar.gz',
					'.tar.bz2'
				), true ) ? $_POST['archiveformat'] : '.zip';
				BackWPup_Option::update( $jobid, 'archiveformat', $archiveformat );

				BackWPup_Option::update( $jobid, 'archivename', BackWPup_Job::sanitize_file_name( $_POST['archivename'] ) );
				break;
			case 'cron':
				$activetype = in_array( $_POST['activetype'], array(
					'',
					'wpcron',
					'easycron',
					'link'
				), true ) ? $_POST['activetype'] : '';
				BackWPup_Option::update( $jobid, 'activetype', $activetype );

				$cronselect = $_POST['cronselect'] === 'advanced' ? 'advanced' : 'basic';
				BackWPup_Option::update( $jobid, 'cronselect', $cronselect );

				//save advanced
				if ( $cronselect === 'advanced' ) {
					if ( empty( $_POST['cronminutes'] ) || $_POST['cronminutes'][0] === '*' ) {
						if ( ! empty( $_POST['cronminutes'][1] ) ) {
							$_POST['cronminutes'] = array( '*/' . $_POST['cronminutes'][1] );
						} else {
							$_POST['cronminutes'] = array( '*' );
						}
					}
					if ( empty( $_POST['cronhours'] ) || $_POST['cronhours'][0] === '*' ) {
						if ( ! empty( $_POST['cronhours'][1] ) ) {
							$_POST['cronhours'] = array( '*/' . $_POST['cronhours'][1] );
						} else {
							$_POST['cronhours'] = array( '*' );
						}
					}
					if ( empty( $_POST['cronmday'] ) || $_POST['cronmday'][0] === '*' ) {
						if ( ! empty( $_POST['cronmday'][1] ) ) {
							$_POST['cronmday'] = array( '*/' . $_POST['cronmday'][1] );
						} else {
							$_POST['cronmday'] = array( '*' );
						}
					}
					if ( empty( $_POST['cronmon'] ) || $_POST['cronmon'][0] === '*' ) {
						if ( ! empty( $_POST['cronmon'][1] ) ) {
							$_POST['cronmon'] = array( '*/' . $_POST['cronmon'][1] );
						} else {
							$_POST['cronmon'] = array( '*' );
						}
					}
					if ( empty( $_POST['cronwday'] ) || $_POST['cronwday'][0] === '*' ) {
						if ( ! empty( $_POST['cronwday'][1] ) ) {
							$_POST['cronwday'] = array( '*/' . $_POST['cronwday'][1] );
						} else {
							$_POST['cronwday'] = array( '*' );
						}
					}
					$cron = implode( ",", $_POST['cronminutes'] ) . ' ' . implode( ",", $_POST['cronhours'] ) . ' ' . implode( ",", $_POST['cronmday'] ) . ' ' . implode( ",", $_POST['cronmon'] ) . ' ' . implode( ",", $_POST['cronwday'] );
					BackWPup_Option::update( $jobid, 'cron', $cron );
				} else {
					//Save basic
					if ( $_POST['cronbtype'] === 'mon' ) {
						BackWPup_Option::update( $jobid, 'cron', absint( $_POST['moncronminutes'] ) . ' ' . absint( $_POST['moncronhours'] ) . ' ' . absint( $_POST['moncronmday'] ) . ' * *' );
					}
					if ( $_POST['cronbtype'] === 'week' ) {
						BackWPup_Option::update( $jobid, 'cron', absint( $_POST['weekcronminutes'] ) . ' ' . absint( $_POST['weekcronhours'] ) . ' * * ' . absint( $_POST['weekcronwday'] ) );
					}
					if ( $_POST['cronbtype'] === 'day' ) {
						BackWPup_Option::update( $jobid, 'cron', absint( $_POST['daycronminutes'] ) . ' ' . absint( $_POST['daycronhours'] ) . ' * * *' );
					}
					if ( $_POST['cronbtype'] === 'hour' ) {
						BackWPup_Option::update( $jobid, 'cron', absint( $_POST['hourcronminutes'] ) . ' * * * *' );
					}
				}
				//reschedule
				$activetype = BackWPup_Option::get( $jobid, 'activetype' );
				wp_clear_scheduled_hook( 'backwpup_cron', array( 'id' => $jobid ) );
				if ( $activetype === 'wpcron' ) {
					$cron_next = BackWPup_Cron::cron_next( BackWPup_Option::get( $jobid, 'cron' ) );
					wp_schedule_single_event( $cron_next, 'backwpup_cron', array( 'id' => $jobid ) );
				}
				$easy_cron_job_id = BackWPup_Option::get( $jobid, 'easycronjobid' );
				if ( $activetype === 'easycron' ) {
					BackWPup_EasyCron::update( $jobid );
				} elseif ( $easy_cron_job_id ) {
					BackWPup_EasyCron::delete( $jobid );
				}
				break;
			default:
				if ( strstr( $tab, 'dest-' ) ) {
					$dest_class = BackWPup::get_destination( str_replace( 'dest-', '', $tab ) );
					$dest_class->edit_form_post_save( $jobid );
				}
				if ( strstr( $tab, 'jobtype-' ) ) {
					$id = strtoupper( str_replace( 'jobtype-', '', $tab ) );
					$job_types[ $id ]->edit_form_post_save( $jobid );
				}
		}

		//saved message
		$messages = BackWPup_Admin::get_messages();
		if ( empty( $messages['error'] ) ) {
			$url = BackWPup_Job::get_jobrun_url( 'runnowlink', $jobid );
			BackWPup_Admin::message( sprintf( __( 'Changes for job <i>%s</i> saved.', 'backwpup' ), BackWPup_Option::get( $jobid, 'name' ) ) . ' <a href="' . network_admin_url( 'admin.php' ) . '?page=backwpupjobs">' . __( 'Jobs overview', 'backwpup' ) . '</a> | <a href="' . $url['url'] . '">' . __( 'Run now', 'backwpup' ) . '</a>' );
		}
	}

	/**
	 *
	 * Output css
	 *
	 * @return void
	 */
	public static function admin_print_styles() {

		?>
		<style type="text/css" media="screen">
			#cron-min, #cron-hour, #cron-day, #cron-month, #cron-weekday {
				overflow: auto;
				white-space: nowrap;
				height: 7em;
			}
			#cron-min-box, #cron-hour-box, #cron-day-box, #cron-month-box, #cron-weekday-box {
				border: 1px solid gray;
				margin: 10px 0 10px 10px;
				padding: 2px 2px;
				width: 100px;
				float: left;
			}
			#wpcronbasic {
				border-collapse: collapse;
			}
			#wpcronbasic th, #wpcronbasic td {
				width:80px;
				border-bottom: 1px solid gray;
			}
		</style>
		<?php
		//add css for all other tabs
		if ( substr( $_GET[ 'tab' ], 0, 5 ) == 'dest-' ) {
			$dest_object = BackWPup::get_destination( str_replace( 'dest-', '', $_GET[ 'tab' ] ) );
			$dest_object->admin_print_styles();
		}
		elseif ( substr( $_GET[ 'tab' ], 0, 8 ) == 'jobtype-' ) {
			$job_type = BackWPup::get_job_types();
			$id       = strtoupper( str_replace( 'jobtype-', '', $_GET[ 'tab' ] ) );
			$job_type[ $id ]->admin_print_styles( );
		}
	}

	/**
	 *
	 * Output js
	 *
	 * @return void
	 */
	public static function admin_print_scripts() {

		wp_enqueue_script( 'backwpupgeneral' );

		//add js for the first tabs
		if ( $_GET[ 'tab' ] == 'job' ) {
			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				wp_enqueue_script( 'backwpuptabjob', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/page_edit_tab_job.js', array('jquery'), time(), TRUE );
			} else {
				wp_enqueue_script( 'backwpuptabjob', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/page_edit_tab_job.min.js', array('jquery'), BackWPup::get_plugin_data( 'Version' ), TRUE );
			}
		}
 		elseif ( $_GET[ 'tab' ] == 'cron' ) {
			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				wp_enqueue_script( 'backwpuptabcron', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/page_edit_tab_cron.js', array('jquery'), time(), TRUE );
			} else {
				wp_enqueue_script( 'backwpuptabcron', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/page_edit_tab_cron.min.js', array('jquery'), BackWPup::get_plugin_data( 'Version' ), TRUE );
			}
		}
		//add js for all other tabs
		elseif ( strstr( $_GET[ 'tab' ], 'dest-' ) ) {
			$dest_object = BackWPup::get_destination( str_replace( 'dest-', '', $_GET[ 'tab' ] ) );
			$dest_object->admin_print_scripts();
		}
		elseif (  strstr( $_GET[ 'tab' ], 'jobtype-' ) ) {
			$job_type = BackWPup::get_job_types();
			$id       = strtoupper( str_replace( 'jobtype-', '', $_GET[ 'tab' ] ) );
			$job_type[ $id ]->admin_print_scripts( );
		}
	}

	/**
	 * @static
	 *
	 * @param string $args
	 *
	 * @return mixed
	 */
	public static function ajax_cron_text( $args = '' ) {

		if ( is_array( $args ) ) {
			extract( $args );
			$ajax = FALSE;
		} else {
			if ( ! current_user_can( 'backwpup_jobs_edit' ) )
				wp_die( -1 );
			check_ajax_referer( 'backwpup_ajax_nonce' );
			if ( empty( $_POST[ 'cronminutes' ] ) || $_POST[ 'cronminutes' ][ 0 ] == '*' ) {
				if ( ! empty( $_POST[ 'cronminutes' ][ 1 ] ) )
					$_POST[ 'cronminutes' ] = array( '*/' . $_POST[ 'cronminutes' ][ 1 ] );
				else
					$_POST[ 'cronminutes' ] = array( '*' );
			}
			if ( empty( $_POST[ 'cronhours' ] ) || $_POST[ 'cronhours' ][ 0 ] == '*' ) {
				if ( ! empty( $_POST[ 'cronhours' ][ 1 ] ) )
					$_POST[ 'cronhours' ] = array( '*/' . $_POST[ 'cronhours' ][ 1 ] );
				else
					$_POST[ 'cronhours' ] = array( '*' );
			}
			if ( empty( $_POST[ 'cronmday' ] ) || $_POST[ 'cronmday' ][ 0 ] == '*' ) {
				if ( ! empty( $_POST[ 'cronmday' ][ 1 ] ) )
					$_POST[ 'cronmday' ] = array( '*/' . $_POST[ 'cronmday' ][ 1 ] );
				else
					$_POST[ 'cronmday' ] = array( '*' );
			}
			if ( empty( $_POST[ 'cronmon' ] ) || $_POST[ 'cronmon' ][ 0 ] == '*' ) {
				if ( ! empty( $_POST[ 'cronmon' ][ 1 ] ) )
					$_POST[ 'cronmon' ] = array( '*/' . $_POST[ 'cronmon' ][ 1 ] );
				else
					$_POST[ 'cronmon' ] = array( '*' );
			}
			if ( empty( $_POST[ 'cronwday' ] ) || $_POST[ 'cronwday' ][ 0 ] == '*' ) {
				if ( ! empty( $_POST[ 'cronwday' ][ 1 ] ) )
					$_POST[ 'cronwday' ] = array( '*/' . $_POST[ 'cronwday' ][ 1 ] );
				else
					$_POST[ 'cronwday' ] = array( '*' );
			}
			$crontype  = $_POST[ 'crontype' ];
			$cronstamp = implode( ",", $_POST[ 'cronminutes' ] ) . ' ' . implode( ",", $_POST[ 'cronhours' ] ) . ' ' . implode( ",", $_POST[ 'cronmday' ] ) . ' ' . implode( ",", $_POST[ 'cronmon' ] ) . ' ' . implode( ",", $_POST[ 'cronwday' ] );
			$ajax      = TRUE;
		}
		echo '<p class="wpcron" id="schedulecron">';

		if ( $crontype == 'advanced' ) {
			echo str_replace( '\"','"', __( 'Working as <a href="http://wikipedia.org/wiki/Cron">Cron</a> schedule:', 'backwpup' ) );
			echo ' <i><b>' . esc_attr( $cronstamp ). '</b></i><br />';
		}

		list( $cronstr[ 'minutes' ], $cronstr[ 'hours' ], $cronstr[ 'mday' ], $cronstr[ 'mon' ], $cronstr[ 'wday' ] ) = explode( ' ', $cronstamp, 5 );
		if ( FALSE !== strpos( $cronstr[ 'minutes' ], '*/' ) || $cronstr[ 'minutes' ] == '*' ) {
			$repeatmins = str_replace( '*/', '', $cronstr[ 'minutes' ] );
			if ( $repeatmins == '*' || empty( $repeatmins ) )
				$repeatmins = 5;
			echo '<span style="color:red;">' . sprintf( __( 'ATTENTION: Job runs every %d minutes!', 'backwpup' ), $repeatmins ) . '</span><br />';
		}
		if ( FALSE !== strpos( $cronstr[ 'hours' ], '*/' ) || $cronstr[ 'hours' ] == '*' ) {
			$repeathouer = str_replace( '*/', '', $cronstr[ 'hours' ] );
			if ( $repeathouer == '*' || empty( $repeathouer ) )
				$repeathouer = 1;
			echo '<span style="color:red;">' . sprintf( __( 'ATTENTION: Job runs every %d hours!', 'backwpup' ), $repeathouer ) . '</span><br />';
		}
		$cron_next = BackWPup_Cron::cron_next( $cronstamp ) + ( get_option( 'gmt_offset' ) * 3600 );
		if ( PHP_INT_MAX === $cron_next ) {
			echo '<span style="color:red;">' . __( 'ATTENTION: Can\'t calculate cron!', 'backwpup' ) . '</span><br />';
		}
		else {
			_e( 'Next runtime:', 'backwpup' );
			echo ' <b>' . date_i18n( 'D, j M Y, H:i', $cron_next, TRUE ) . '</b>';
		}
		echo "</p>";

		if ( $ajax )
			die();
		else
			return;
	}

	/**
	 *
	 */
	public static function page() {

		if ( ! empty( $_GET[ 'jobid' ] ) ) {
			$jobid = (int)$_GET[ 'jobid' ];
		}
		else {
			//generate jobid if not exists
			$newjobid = BackWPup_Option::get_job_ids();
			sort( $newjobid );
			$jobid = end( $newjobid ) + 1;
		}

		$destinations = BackWPup::get_registered_destinations();
		$job_types    = BackWPup::get_job_types();

		?>
    <div class="wrap" id="backwpup-page">
		<?php
		echo '<h1>' . sprintf( esc_html__( '%1$s &rsaquo; Job: %2$s', 'backwpup' ), BackWPup::get_plugin_data( 'name' ), '<span id="h2jobtitle">' . esc_html( BackWPup_Option::get( $jobid, 'name' ) ) . '</span>' ). '</h1>';

		//default tabs
		$tabs = array( 'job' => array( 'name' => esc_html__( 'General', 'backwpup' ), 'display' => TRUE ), 'cron' => array( 'name' => __( 'Schedule', 'backwpup' ), 'display' => TRUE ) );
		//add jobtypes to tabs
		$job_job_types = BackWPup_Option::get( $jobid, 'type' );
		foreach ( $job_types as $typeid => $typeclass ) {
			$tabid          = 'jobtype-' . strtolower( $typeid );
			$tabs[ $tabid ][ 'name' ] = $typeclass->info[ 'name' ];
			$tabs[ $tabid ][ 'display' ] = TRUE;
			if ( ! in_array( $typeid, $job_job_types, true ) )
				$tabs[ $tabid ][ 'display' ] = FALSE;

		}
		//add destinations to tabs
		$jobdests = BackWPup_Option::get( $jobid, 'destinations' );
		foreach ( $destinations as $destid => $dest ) {
			$tabid          = 'dest-' . strtolower( $destid );
			$tabs[ $tabid ][ 'name' ] = sprintf( __( 'To: %s', 'backwpup' ), $dest[ 'info' ][ 'name' ] );
			$tabs[ $tabid ][ 'display' ] = TRUE;
			if ( ! in_array( $destid, $jobdests, true ) )
				$tabs[ $tabid ][ 'display' ] = FALSE;
		}
		//display tabs
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $id => $tab ) {
			$addclass = '';
			if ( $id === $_GET[ 'tab' ] ) {
				$addclass = ' nav-tab-active';
			}
			$display = '';
			if ( ! $tab[ 'display' ] ) {
				$display = ' style="display:none;"';
			}
			echo '<a href="' . wp_nonce_url( network_admin_url( 'admin.php?page=backwpupeditjob&tab=' . $id . '&jobid=' . $jobid ), 'edit-job' )   . '" class="nav-tab' . $addclass . '" id="tab-' . esc_attr( $id ) . '" data-nexttab="' . esc_attr( $id ) . '"' . $display . '>' . esc_html( $tab[ 'name' ] ) . '</a>';
		}
		echo '</h2>';
		//display messages
		BackWPup_Admin::display_messages();
		echo '<form name="editjob" id="editjob" method="post" action="' . esc_attr(admin_url( 'admin-post.php' )) . '">';
		echo '<input type="hidden" id="jobid" name="jobid" value="' .  esc_attr( $jobid ) . '" />';
		echo '<input type="hidden" name="tab" value="' . esc_attr( $_GET[ 'tab' ] ) . '" />';
		echo '<input type="hidden" name="nexttab" value="' . esc_attr( $_GET[ 'tab' ] ) . '" />';
		echo '<input type="hidden" name="page" value="backwpupeditjob" />';
		echo '<input type="hidden" name="action" value="backwpup" />';
    	echo '<input type="hidden" name="anchor" value="" />';
		wp_nonce_field( 'backwpupeditjob_page' );
		wp_nonce_field( 'backwpup_ajax_nonce', 'backwpupajaxnonce', FALSE );

		switch ( $_GET[ 'tab' ] ) {
			case 'job':
				?>
				<div class="table" id="info-tab-job">
					<h3><?php esc_html_e( 'Job Name', 'backwpup' ) ?></h3>
					<table class="form-table">
						<tr>
							<th scope="row"><label for="name"><?php esc_html_e( 'Please name this job.', 'backwpup' ) ?></label></th>
							<td>
								<input name="name" type="text" id="name" placeholder="<?php esc_attr_e( 'Job Name', 'backwpup' ); ?>" data-empty="<?php esc_attr_e( 'New Job', 'backwpup' ); ?>" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'name' ) ); ?>" class="regular-text" />
							</td>
						</tr>
					</table>

					<h3><?php esc_html_e( 'Job Tasks', 'backwpup' ) ?></h3>
					<table class="form-table">
						<tr>
							<th scope="row"><?php esc_html_e( 'This job is a&#160;&hellip;', 'backwpup' ) ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e( 'Job tasks', 'backwpup' ) ?></span>
									</legend><?php
									foreach ( $job_types as $id => $typeclass ) {
										$addclass = '';
										if ( $typeclass->creates_file() ) {
											$addclass .= ' filetype';
										}
										echo '<p><label for="jobtype-select-' . strtolower( $id ) . '"><input class="jobtype-select checkbox' . $addclass . '" id="jobtype-select-' . strtolower( $id ) . '" type="checkbox" ' . checked( TRUE, in_array( $id, BackWPup_Option::get( $jobid, 'type' ), true ), FALSE ) . ' name="type[]" value="' .esc_attr( $id ) . '" /> ' . esc_attr( $typeclass->info[ 'description' ] ) . '</label>';
										if ( ! empty( $typeclass->info[ 'help' ] ) ) {
											echo '<br><span class="description">' . esc_attr( $typeclass->info[ 'help' ] ) . '</span>';
										}
										echo '</p>';
									}
									?></fieldset>
							</td>
						</tr>
					</table>

					<h3 class="title hasdests"><?php esc_html_e( 'Backup File Creation', 'backwpup' ) ?></h3>
					<p class="hasdests"></p>
					<table class="form-table hasdests">
						<?php if ( class_exists( 'BackWPup_Pro', FALSE ) ) { ?>
						<tr>
							<th scope="row"><?php esc_html_e( 'Backup type', 'backwpup' ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">	<span><?php esc_html_e( 'Backup type', 'backwpup' ) ?></span></legend>
									<p>
										<label for="idbackuptype-sync">
											<input class="radio" type="radio"<?php checked( 'sync', BackWPup_Option::get( $jobid, 'backuptype' ), TRUE ); ?> name="backuptype" id="idbackuptype-sync" value="sync" /> <?php esc_html_e( 'Synchronize file by file to destination', 'backwpup' ); ?>
										</label>
									</p>
									<p>
										<label for="idbackuptype-archive">
											<input class="radio" type="radio"<?php checked( 'archive', BackWPup_Option::get( $jobid, 'backuptype' ), TRUE ); ?> name="backuptype" id="idbackuptype-archive" value="archive" /> <?php esc_html_e( 'Create a backup archive', 'backwpup' ); ?>
										</label>
									</p>
								</fieldset>
							</td>
						</tr>
						<?php } ?>
						<tr class="nosync">
							<th scope="row"><label for="archivename"><?php esc_html_e( 'Archive name', 'backwpup' ) ?></label></th>
							<td>
								<input name="archivename" type="text" id="archivename" placeholder="backwpup_%Y-%m-%d_%H-%i-%s" value="<?php echo esc_attr(BackWPup_Option::get( $jobid, 'archivename' ));?>" class="regular-text code" />
								<?php
								$current_time = current_time( 'timestamp' );
								$datevars    = array( '%d', '%j', '%m', '%n', '%Y', '%y', '%a', '%A', '%B', '%g', '%G', '%h', '%H', '%i', '%s' );
								$datevalues  = array( date( 'd', $current_time ), date( 'j', $current_time ), date( 'm', $current_time ), date( 'n', $current_time ), date( 'Y', $current_time ), date( 'y', $current_time ), date( 'a', $current_time ), date( 'A', $current_time ), date( 'B', $current_time ), date( 'g', $current_time ), date( 'G', $current_time ), date( 'h', $current_time ), date( 'H', $current_time ), date( 'i', $current_time ), date( 's', $current_time ) );
								$archivename = str_replace( $datevars, $datevalues, BackWPup_Job::sanitize_file_name( BackWPup_Option::get( $jobid, 'archivename' ) ) );
								echo '<p>Preview: <code><span id="archivefilename">' . esc_attr( $archivename ) . '</span><span id="archiveformat">' . esc_attr( BackWPup_Option::get( $jobid, 'archiveformat' ) ) . '</span></code></p>';
								echo '<p class="description">';
								echo "<strong>" . esc_attr__( 'Replacement patterns:', 'backwpup' ) . "</strong><br />";
								echo esc_attr__( '%d = Two digit day of the month, with leading zeros', 'backwpup' ) . '<br />';
								echo esc_attr__( '%j = Day of the month, without leading zeros', 'backwpup' ) . '<br />';
								echo esc_attr__( '%m = Day of the month, with leading zeros', 'backwpup' ) . '<br />';
								echo esc_attr__( '%n = Representation of the month (without leading zeros)', 'backwpup' ) . '<br />';
								echo esc_attr__( '%Y = Four digit representation for the year', 'backwpup' ) . '<br />';
								echo esc_attr__( '%y = Two digit representation of the year', 'backwpup' ) . '<br />';
								echo esc_attr__( '%a = Lowercase ante meridiem (am) and post meridiem (pm)', 'backwpup' ) . '<br />';
								echo esc_attr__( '%A = Uppercase ante meridiem (AM) and post meridiem (PM)', 'backwpup' ) . '<br />';
								echo esc_attr__( '%B = Swatch Internet Time', 'backwpup' ) . '<br />';
								echo esc_attr__( '%g = Hour in 12-hour format, without leading zeros', 'backwpup' ) . '<br />';
								echo esc_attr__( '%G = Hour in 24-hour format, without leading zeros', 'backwpup' ) . '<br />';
								echo esc_attr__( '%h = Hour in 12-hour format, with leading zeros', 'backwpup' ) . '<br />';
								echo esc_attr__( '%H = Hour in 24-hour format, with leading zeros', 'backwpup' ) . '<br />';
								echo esc_attr__( '%i = Two digit representation of the minute', 'backwpup' ) . '<br />';
								echo esc_attr__( '%s = Two digit representation of the second', 'backwpup' ) . '<br />';
								echo '</p>';
								?>
							</td>
						</tr>
						<tr class="nosync">
							<th scope="row"><?php esc_html_e( 'Archive Format', 'backwpup' ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e( 'Archive Format', 'backwpup' ) ?></span></legend>
									<?php
									if ( class_exists( 'ZipArchive' ) ) {
										echo '<p><label for="idarchiveformat-zip"><input class="radio" type="radio"' . checked( '.zip', BackWPup_Option::get( $jobid, 'archiveformat' ), FALSE ) . ' name="archiveformat" id="idarchiveformat-zip" value=".zip" /> ' . esc_html__( 'Zip', 'backwpup' ) . '</label></p>';
									} else {
										echo '<p><label for="idarchiveformat-zip"><input class="radio" type="radio"' . checked( '.zip', BackWPup_Option::get( $jobid, 'archiveformat' ), FALSE ) . ' name="archiveformat" id="idarchiveformat-zip" value=".zip" disabled="disabled" /> ' . esc_html__( 'Zip', 'backwpup' ) . '</label>';
										echo '<br /><span class="description">' . esc_html(sprintf( __( 'Disabled due to missing %s PHP class.', 'backwpup' ), 'ZipArchive' )) . '</span></p>';
									}
									echo '<p><label for="idarchiveformat-tar"><input class="radio" type="radio"' . checked( '.tar', BackWPup_Option::get( $jobid, 'archiveformat' ), FALSE ) . ' name="archiveformat" id="idarchiveformat-tar" value=".tar" /> ' . esc_html__( 'Tar', 'backwpup' )  . '</label></p>';
									if ( function_exists( 'gzopen' ) ) {
										echo '<p><label for="idarchiveformat-targz"><input class="radio" type="radio"' . checked( '.tar.gz', BackWPup_Option::get( $jobid, 'archiveformat' ), FALSE ) . ' name="archiveformat" id="idarchiveformat-targz" value=".tar.gz" /> ' . esc_html__( 'Tar GZip', 'backwpup' ) .  '</label></p>';
									} else {
										echo '<p><label for="idarchiveformat-targz"><input class="radio" type="radio"' . checked( '.tar.gz', BackWPup_Option::get( $jobid, 'archiveformat' ), FALSE ) . ' name="archiveformat" id="idarchiveformat-targz" value=".tar.gz" disabled="disabled" /> ' . esc_html__( 'Tar GZip', 'backwpup' ) . '</label>';
										echo '<br /><span class="description">' . esc_html(sprintf( __( 'Disabled due to missing %s PHP function.', 'backwpup' ), 'gzopen()' )) . '</span></p>';
									}
									if ( function_exists( 'bzopen' ) ) {
										echo '<p><label for="idarchiveformat-tarbz2"><input class="radio" type="radio"' . checked( '.tar.bz2', BackWPup_Option::get( $jobid, 'archiveformat' ), FALSE ) . ' name="archiveformat" id="idarchiveformat-tarbz2" value=".tar.bz2" /> ' . esc_html__( 'Tar BZip2', 'backwpup' ) . '</label></p>';
									} else {
										echo '<p><label for="idarchiveformat-tarbz2"><input class="radio" type="radio"' . checked( '.tar.bz2', BackWPup_Option::get( $jobid, 'archiveformat' ), FALSE ) . ' name="archiveformat" id="idarchiveformat-tarbz2" value=".tar.bz2" disabled="disabled" /> ' . esc_html__( 'Tar BZip2', 'backwpup' ) . '</label>';
										echo '<br /><span class="description">' . esc_html(sprintf( __( 'Disabled due to missing %s PHP function.', 'backwpup' ), 'bzopen()' )) . '</span></p>';
									}
									?></fieldset>
							</td>
						</tr>
					</table>

					<h3 class="title hasdests"><?php esc_html_e( 'Job Destination', 'backwpup' ) ?></h3>
					<p class="hasdests"></p>
					<table class="form-table hasdests">
						<tr>
							<th scope="row"><?php esc_html_e( 'Where should your backup file be stored?', 'backwpup' ) ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e( 'Where should your backup file be stored?', 'backwpup' ) ?></span>
									</legend><?php
									foreach ( $destinations as $id => $dest ) {
										$syncclass = '';
										if ( ! $dest[ 'can_sync' ] ) {
											$syncclass = 'nosync';
										}
										echo '<p class="' . esc_attr($syncclass) . '"><label for="dest-select-' . strtolower( $id ) . '"><input class="checkbox" id="dest-select-' . strtolower(  esc_attr( $id ) ) . '" type="checkbox" ' . checked( TRUE, in_array( $id, BackWPup_Option::get( $jobid, 'destinations' ), true ), FALSE ) . ' name="destinations[]" value="' . esc_attr($id) . '" ' . disabled( ! empty( $dest[ 'error' ] ), TRUE, FALSE ) . ' /> ' . esc_attr( $dest[ 'info' ][ 'description' ] );
										if ( ! empty( $dest[ 'error' ] ) ) {
											echo '<br><span class="description">' . esc_attr( $dest[ 'error' ] ) . '</span>';
										}
										echo '</label></p>';
									}
									?></fieldset>
							</td>
						</tr>
					</table>

					<h3 class="title"><?php esc_html_e( 'Log Files', 'backwpup' ) ?></h3>
					<p></p>
					<table class="form-table">
						<tr>
							<th scope="row"><label for="mailaddresslog"><?php esc_html_e( 'Send log to email address', 'backwpup' ) ?></label></th>
							<td>
								<input name="mailaddresslog" type="text" id="mailaddresslog" value="<?php echo esc_html( BackWPup_Option::get( $jobid, 'mailaddresslog' ) );?>" class="regular-text" />
								<p class="description"><?php esc_attr_e( 'Leave empty to not have log sent. Or separate with , for more than one receiver.', 'backwpup' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="mailaddresssenderlog"><?php esc_html_e( 'Email FROM field', 'backwpup' ) ?></label></th>
							<td>
								<input name="mailaddresssenderlog" type="text" id="mailaddresssenderlog" value="<?php echo esc_html( BackWPup_Option::get( $jobid, 'mailaddresssenderlog' ) );?>" class="regular-text" placeholder="<?php esc_attr_e( 'Your Name &lt;mail@domain.tld&gt;', 'backwpup' ); ?>"/>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Errors only', 'backwpup' ); ?></th>
							<td>
	                            <label for="idmailerroronly">
								<input class="checkbox" value="1" id="idmailerroronly"
									   type="checkbox" <?php checked( BackWPup_Option::get( $jobid, 'mailerroronly' ), TRUE ); ?>
									   name="mailerroronly" /> <?php esc_html_e( 'Send email with log only when errors occur during job execution.', 'backwpup' ); ?>
								</label>
							</td>
						</tr>
					</table>
				</div>
				<?php
				break;
			case 'cron':
				?>
				<div class="table" id="info-tab-cron">
					<h3 class="title"><?php esc_html_e( 'Job Schedule', 'backwpup' ) ?></h3>
					<p></p>
					<table class="form-table">
						<tr>
	                        <th scope="row"><?php esc_html_e( 'Start job', 'backwpup' ); ?></th>
	                        <td>
	                            <fieldset>
	                                <legend class="screen-reader-text"><span><?php esc_html_e( 'Start job', 'backwpup' ) ?></span></legend>
	                                <label for="idactivetype"><input class="radio"
	                                       type="radio"<?php checked( '', BackWPup_Option::get( $jobid, 'activetype' ), TRUE ); ?>
	                                       name="activetype" id="idactivetype"
	                                       value="" /> <?php esc_html_e( 'manually only', 'backwpup' ); ?></label><br/>
	                                <label for="idactivetype-wpcron"><input class="radio"
	                                       type="radio"<?php checked( 'wpcron', BackWPup_Option::get( $jobid, 'activetype' ), TRUE ); ?>
	                                       name="activetype" id="idactivetype-wpcron"
	                                       value="wpcron" /> <?php esc_html_e( 'with WordPress cron', 'backwpup' ); ?></label><br/>
		                            <?php
		                            $disabled = '';
		                            $easycron_api = get_site_option( 'backwpup_cfg_easycronapikey' );
		                            if ( empty( $easycron_api ) ) {
			                            $disabled = ' disabled="disabled"';
		                            }
		                            ?>
		                            <label for="idactivetype-easycron"><input class="radio" type="radio"<?php checked( 'easycron', BackWPup_Option::get( $jobid, 'activetype' ), TRUE ); ?> name="activetype" id="idactivetype-easycron"<?php echo $disabled; ?> value="easycron" />
			                        <?php _e( 'with <a href="https://www.easycron.com?ref=36673" title="Affiliate Link!">EasyCron.com</a>', 'backwpup' );
		                            if ( empty( $easycron_api ) ) {
			                            echo '&nbsp;-&nbsp;<span class="description">' . sprintf( __( 'First setup <a href="%s">API Key</a>.', 'backwpup' ), network_admin_url( 'admin.php' ) . '?page=backwpupsettings#backwpup-tab-apikey' ) . '</span>';
		                            }
		                            ?>
		                            </label><br/>
		                            <?php
									$url = BackWPup_Job::get_jobrun_url( 'runext', BackWPup_Option::get( $jobid, 'jobid' ) );
									?>
	                                <label for="idactivetype-link">
		                                <input class="radio" type="radio"<?php checked( 'link', BackWPup_Option::get( $jobid, 'activetype' ), TRUE ); ?> name="activetype" id="idactivetype-link" value="link" />
		                                &nbsp;<?php esc_html_e( 'with a link', 'backwpup' ); ?> <code><a href="<?php echo $url[ 'url' ];?>" target="_blank"><?php echo esc_html($url[ 'url' ]);?></a></code><br>
		                                <span class="description"><?php esc_attr_e( 'Copy the link for an external start. This option has to be activated to make the link work.', 'backwpup' ); ?></span>
	                                </label>

	                            </fieldset>
	                        </td>
	                    </tr>
	                    <tr>
							<th scope="row"><?php esc_html_e( 'Start job with CLI', 'backwpup' ); ?></th>
							<td>
								<?php
								_e( 'Use <a href="http://wp-cli.org/">WP-CLI</a> to run jobs from commandline.', 'backwpup' );
								?>
							</td>
	                    </tr>
					</table>
					<h3 class="title wpcron"><?php esc_html_e( 'Schedule execution time', 'backwpup' ) ?></h3>
					<?php BackWPup_Page_Editjob::ajax_cron_text( array( 'cronstamp' => BackWPup_Option::get( $jobid, 'cron' ), 'crontype' => BackWPup_Option::get( $jobid, 'cronselect' ) ) ); ?>
					<table class="form-table wpcron">
						<tr>
							<th scope="row"><?php esc_html_e( 'Scheduler type', 'backwpup' ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e( 'Scheduler type', 'backwpup' ) ?></span></legend>
	                                <label for="idcronselect-basic"><input class="radio"
										   type="radio"<?php checked( 'basic', BackWPup_Option::get( $jobid, 'cronselect' ), TRUE ); ?>
										   name="cronselect" id="idcronselect-basic"
										   value="basic" /> <?php esc_html_e( 'basic', 'backwpup' ); ?></label><br/>
	                                <label for="idcronselect-advanced"><input class="radio"
										   type="radio"<?php checked( 'advanced', BackWPup_Option::get( $jobid, 'cronselect' ), TRUE ); ?>
										   name="cronselect" id="idcronselect-advanced"
										   value="advanced" /> <?php esc_html_e( 'advanced', 'backwpup' ); ?></label><br/>
								</fieldset>
							</td>
						</tr>
						<?php

						list( $cronstr[ 'minutes' ], $cronstr[ 'hours' ], $cronstr[ 'mday' ], $cronstr[ 'mon' ], $cronstr[ 'wday' ] ) = explode( ' ', BackWPup_Option::get( $jobid, 'cron' ), 5 );
						if ( strstr( $cronstr[ 'minutes' ], '*/' ) )
							$minutes = explode( '/', $cronstr[ 'minutes' ] );
						else
							$minutes = explode( ',', $cronstr[ 'minutes' ] );
						if ( strstr( $cronstr[ 'hours' ], '*/' ) )
							$hours = explode( '/', $cronstr[ 'hours' ] );
						else
							$hours = explode( ',', $cronstr[ 'hours' ] );
						if ( strstr( $cronstr[ 'mday' ], '*/' ) )
							$mday = explode( '/', $cronstr[ 'mday' ] );
						else
							$mday = explode( ',', $cronstr[ 'mday' ] );
						if ( strstr( $cronstr[ 'mon' ], '*/' ) )
							$mon = explode( '/', $cronstr[ 'mon' ] );
						else
							$mon = explode( ',', $cronstr[ 'mon' ] );
						if ( strstr( $cronstr[ 'wday' ], '*/' ) )
							$wday = explode( '/', $cronstr[ 'wday' ] );
						else
							$wday = explode( ',', $cronstr[ 'wday' ] );
						?>
	                    <tr class="wpcronbasic"<?php if ( BackWPup_Option::get( $jobid, 'cronselect' ) !== 'basic' ) echo ' style="display:none;"';?>>
	                        <th scope="row"><?php _e( 'Scheduler', 'backwpup' ); ?></th>
	                        <td>
	                            <table id="wpcronbasic">
	                                <tr>
	                                    <th>
											<?php _e( 'Type', 'backwpup' ); ?>
	                                    </th>
	                                    <th>
	                                    </th>
	                                    <th>
											<?php _e( 'Hour', 'backwpup' ); ?>
	                                    </th>
	                                    <th>
											<?php _e( 'Minute', 'backwpup' ); ?>
	                                    </th>
	                                </tr>
	                                <tr>
	                                    <td><label for="idcronbtype-mon"><?php echo '<input class="radio" type="radio"' . checked( TRUE, is_numeric( $mday[ 0 ] ), FALSE ) . ' name="cronbtype" id="idcronbtype-mon" value="mon" /> ' . esc_html__( 'monthly', 'backwpup' ); ?></label></td>
	                                    <td><select name="moncronmday"><?php for ( $i = 1; $i <= 31; $i ++ ) {
											echo '<option ' . selected( in_array( (string) $i, $mday, TRUE ), TRUE, FALSE ) . '  value="' . esc_attr($i) . '" />' . esc_html__( 'on', 'backwpup' ) . ' ' . esc_html($i) . '</option>';
										} ?></select></td>
	                                    <td><select name="moncronhours"><?php for ( $i = 0; $i < 24; $i ++ ) {
											echo '<option ' . selected( in_array( (string) $i, $hours, TRUE ), TRUE, FALSE ) . '  value="' . esc_attr($i) . '" />' . esc_html($i) . '</option>';
										} ?></select></td>
	                                    <td><select name="moncronminutes"><?php for ( $i = 0; $i < 60; $i = $i + 5 ) {
											echo '<option ' . selected( in_array( (string) $i, $minutes, TRUE ), TRUE, FALSE ) . '  value="' . esc_attr($i) . '" />' . esc_html($i) . '</option>';
										} ?></select></td>
	                                </tr>
	                                <tr>
	                                    <td><label for="idcronbtype-week"><?php echo '<input class="radio" type="radio"' . checked( TRUE, is_numeric( $wday[ 0 ] ), FALSE ) . ' name="cronbtype" id="idcronbtype-week" value="week" /> ' . esc_html__( 'weekly', 'backwpup' ); ?></label></td>
	                                    <td><select name="weekcronwday">
											<?php     echo '<option ' . selected( in_array( "0", $wday, TRUE ), TRUE, FALSE ) . '  value="0" />' . esc_html__( 'Sunday', 'backwpup' ) . '</option>';
											echo '<option ' . selected( in_array( "1", $wday, TRUE ), TRUE, FALSE ) . '  value="1" />' . esc_html__( 'Monday', 'backwpup' ) . '</option>';
											echo '<option ' . selected( in_array( "2", $wday, TRUE ), TRUE, FALSE ) . '  value="2" />' . esc_html__( 'Tuesday', 'backwpup' ) . '</option>';
											echo '<option ' . selected( in_array( "3", $wday, TRUE ), TRUE, FALSE ) . '  value="3" />' . esc_html__( 'Wednesday', 'backwpup' ) . '</option>';
											echo '<option ' . selected( in_array( "4", $wday, TRUE ), TRUE, FALSE ) . '  value="4" />' . esc_html__( 'Thursday', 'backwpup' ) . '</option>';
											echo '<option ' . selected( in_array( "5", $wday, TRUE ), TRUE, FALSE ) . '  value="5" />' . esc_html__( 'Friday', 'backwpup' ) . '</option>';
											echo '<option ' . selected( in_array( "6", $wday, TRUE ), TRUE, FALSE ) . '  value="6" />' . esc_html__( 'Saturday', 'backwpup' ) . '</option>'; ?>
	                                    </select></td>
	                                    <td><select name="weekcronhours"><?php for ( $i = 0; $i < 24; $i ++ ) {
											echo '<option ' . selected( in_array( (string) $i, $hours, TRUE ), TRUE, FALSE ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
										} ?></select></td>
	                                    <td><select name="weekcronminutes"><?php for ( $i = 0; $i < 60; $i = $i + 5 ) {
											echo '<option ' . selected( in_array( (string) $i, $minutes, TRUE ), TRUE, FALSE ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
										} ?></select></td>
	                                </tr>
	                                <tr>
	                                    <td><label for="idcronbtype-day"><?php echo '<input class="radio" type="radio"' . checked( "**", $mday[ 0 ] . $wday[ 0 ], FALSE ) . ' name="cronbtype" id="idcronbtype-day" value="day" /> ' . esc_html__( 'daily', 'backwpup' ); ?></label></td>
	                                    <td></td>
	                                    <td><select name="daycronhours"><?php for ( $i = 0; $i < 24; $i ++ ) {
											echo '<option ' . selected( in_array( (string) $i, $hours, TRUE ), TRUE, FALSE ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
										} ?></select></td>
	                                    <td><select name="daycronminutes"><?php for ( $i = 0; $i < 60; $i = $i + 5 ) {
											echo '<option ' . selected( in_array( (string) $i, $minutes, TRUE ), TRUE, FALSE ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
										} ?></select></td>
	                                </tr>
	                                <tr>
	                                    <td><label for="idcronbtype-hour"><?php echo '<input class="radio" type="radio"' . checked( "*", $hours[ 0 ], FALSE ) . ' name="cronbtype" id="idcronbtype-hour" value="hour" /> ' . esc_html__( 'hourly', 'backwpup' ); ?></label></td>
	                                    <td></td>
	                                    <td></td>
	                                    <td><select name="hourcronminutes"><?php for ( $i = 0; $i < 60; $i = $i + 5 ) {
											echo '<option ' . selected( in_array( (string) $i, $minutes, TRUE ), TRUE, FALSE ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
										} ?></select></td>
	                                </tr>
	                            </table>
	                        </td>
	                    </tr>
						<tr class="wpcronadvanced"<?php if ( BackWPup_Option::get( $jobid, 'cronselect' ) != 'advanced' ) echo ' style="display:none;"';?>>
							<th scope="row"><?php _e( 'Scheduler', 'backwpup' ); ?></th>
							<td>
	                            <div id="cron-min-box">
	                                <b><?php _e( 'Minutes:', 'backwpup' ); ?></b><br/>
									<?php
									echo '<label for="idcronminutes"><input class="checkbox" type="checkbox"' . checked( in_array( "*", $minutes, TRUE ), TRUE, FALSE ) . ' name="cronminutes[]" id="idcronminutes" value="*" /> ' . __( 'Any (*)', 'backwpup' ) . '</label><br />';
									?>
	                                <div id="cron-min"><?php
										for ( $i = 0; $i < 60; $i = $i + 5 ) {
											echo '<label for="idcronminutes-' . $i . '"><input class="checkbox" type="checkbox"' . checked( in_array( (string) $i, $minutes, TRUE ), TRUE, FALSE ) . ' name="cronminutes[]" id="idcronminutes-' . esc_attr( $i ) . '" value="' . esc_attr( $i ) . '" /> ' . esc_attr( $i ) . '</label><br />';
										}
										?>
	                                </div>
	                            </div>
	                            <div id="cron-hour-box">
	                                <b><?php _e( 'Hours:', 'backwpup' ); ?></b><br/>
									<?php

									echo '<label for="idcronhours"><input class="checkbox" type="checkbox"' . checked( in_array( "*", $hours, TRUE ), TRUE, FALSE ) . ' name="cronhours[]" id="idcronhours" value="*" /> ' . __( 'Any (*)', 'backwpup' ) . '</label><br />';
									?>
	                                <div id="cron-hour"><?php
										for ( $i = 0; $i < 24; $i ++ ) {
											echo '<label for="idcronhours-' . $i . '"><input class="checkbox" type="checkbox"' . checked( in_array( (string) $i, $hours, TRUE ), TRUE, FALSE ) . ' name="cronhours[]" id="idcronhours-' . esc_attr( $i ) . '" value="' . esc_attr( $i ) . '" /> ' . esc_html( $i ) . '</label><br />';
										}
										?>
	                                </div>
	                            </div>
	                            <div id="cron-day-box">
	                                <b><?php _e( 'Day of Month:', 'backwpup' ); ?></b><br/>
	                                <label for="idcronmday"><input class="checkbox" type="checkbox"<?php checked( in_array( "*", $mday, TRUE ), TRUE, TRUE ); ?>
	                                       name="cronmday[]" id="idcronmday" value="*"/> <?php _e( 'Any (*)', 'backwpup' ); ?></label>
	                                <br/>

	                                <div id="cron-day">
										<?php
										for ( $i = 1; $i <= 31; $i ++ ) {
											echo '<label for="idcronmday-' . $i . '"><input class="checkbox" type="checkbox"' . checked( in_array( (string) $i, $mday, TRUE ), TRUE, FALSE ) . ' name="cronmday[]" id="idcronmday-' . esc_attr( $i ) . '" value="' . esc_attr( $i ) . '" /> ' . esc_html( $i ) . '</label><br />';
										}
										?>
	                                </div>
	                            </div>
	                            <div id="cron-month-box">
	                                <b><?php _e( 'Month:', 'backwpup' ); ?></b><br/>
									<?php
									echo '<label for="idcronmon"><input class="checkbox" type="checkbox"' . checked( in_array( "*", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon" value="*" /> ' . esc_html__( 'Any (*)', 'backwpup' ) . '</label><br />';
									?>
	                                <div id="cron-month">
										<?php
										echo '<label for="idcronmon-1"><input class="checkbox" type="checkbox"' . checked( in_array( "1", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon-1" value="1" /> ' . esc_html__( 'January', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronmon-2"><input class="checkbox" type="checkbox"' . checked( in_array( "2", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon-2" value="2" /> ' . esc_html__( 'February', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronmon-3"><input class="checkbox" type="checkbox"' . checked( in_array( "3", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon-3" value="3" /> ' . esc_html__( 'March', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronmon-4"><input class="checkbox" type="checkbox"' . checked( in_array( "4", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon-4" value="4" /> ' . esc_html__( 'April', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronmon-5"><input class="checkbox" type="checkbox"' . checked( in_array( "5", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon-5" value="5" /> ' . esc_html__( 'May', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronmon-6"><input class="checkbox" type="checkbox"' . checked( in_array( "6", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon-6" value="6" /> ' . esc_html__( 'June', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronmon-7"><input class="checkbox" type="checkbox"' . checked( in_array( "7", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon-7" value="7" /> ' . esc_html__( 'July', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronmon-8"><input class="checkbox" type="checkbox"' . checked( in_array( "8", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon-8" value="8" /> ' . esc_html__( 'August', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronmon-9"><input class="checkbox" type="checkbox"' . checked( in_array( "9", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon-9" value="9" /> ' . esc_html__( 'September', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronmon-10"><input class="checkbox" type="checkbox"' . checked( in_array( "10", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon-10" value="10" /> ' . esc_html__( 'October', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronmon-11"><input class="checkbox" type="checkbox"' . checked( in_array( "11", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon-11" value="11" /> ' . esc_html__( 'November', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronmon-12"><input class="checkbox" type="checkbox"' . checked( in_array( "12", $mon, TRUE ), TRUE, FALSE ) . ' name="cronmon[]" id="idcronmon-12" value="12" /> ' . esc_html__( 'December', 'backwpup' ) . '</label><br />';
										?>
	                                </div>
	                            </div>
	                            <div id="cron-weekday-box">
	                                <b><?php esc_html_e( 'Day of Week:', 'backwpup' ); ?></b><br/>
									<?php
									echo '<label for="idcronwday"><input class="checkbox" type="checkbox"' . checked( in_array( "*", $wday, TRUE ), TRUE, FALSE ) . ' name="cronwday[]" id="idcronwday" value="*" /> ' . __( 'Any (*)', 'backwpup' ) . '</label><br />';
									?>
	                                <div id="cron-weekday">
										<?php
										echo '<label for="idcronwday-0"><input class="checkbox" type="checkbox"' . checked( in_array( "0", $wday, TRUE ), TRUE, FALSE ) . ' name="cronwday[]" id="idcronwday-0" value="0" /> ' . esc_html__( 'Sunday', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronwday-1"><input class="checkbox" type="checkbox"' . checked( in_array( "1", $wday, TRUE ), TRUE, FALSE ) . ' name="cronwday[]" id="idcronwday-1" value="1" /> ' . esc_html__( 'Monday', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronwday-2"><input class="checkbox" type="checkbox"' . checked( in_array( "2", $wday, TRUE ), TRUE, FALSE ) . ' name="cronwday[]" id="idcronwday-2" value="2" /> ' . esc_html__( 'Tuesday', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronwday-3"><input class="checkbox" type="checkbox"' . checked( in_array( "3", $wday, TRUE ), TRUE, FALSE ) . ' name="cronwday[]" id="idcronwday-3" value="3" /> ' . esc_html__( 'Wednesday', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronwday-4"><input class="checkbox" type="checkbox"' . checked( in_array( "4", $wday, TRUE ), TRUE, FALSE ) . ' name="cronwday[]" id="idcronwday-4" value="4" /> ' . esc_html__( 'Thursday', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronwday-5"><input class="checkbox" type="checkbox"' . checked( in_array( "5", $wday, TRUE ), TRUE, FALSE ) . ' name="cronwday[]" id="idcronwday-5" value="5" /> ' . esc_html__( 'Friday', 'backwpup' ) . '</label><br />';
										echo '<label for="idcronwday-6"><input class="checkbox" type="checkbox"' . checked( in_array( "6", $wday, TRUE ), TRUE, FALSE ) . ' name="cronwday[]" id="idcronwday-6" value="6" /> ' . esc_html__( 'Saturday', 'backwpup' ) . '</label><br />';
										?>
	                                </div>
	                            </div>
	                            <br class="clear"/>
							</td>
						</tr>
					</table>
				</div>
				<?php
				break;
			default:
				echo '<div class="table" id="info-tab-' . $_GET[ 'tab' ] . '">';
				if ( strstr( $_GET[ 'tab' ], 'dest-' ) ) {
					$dest_object = BackWPup::get_destination( str_replace( 'dest-', '', $_GET[ 'tab' ] ) );
					$dest_object->edit_tab( $jobid );
				}
				if ( strstr( $_GET[ 'tab' ], 'jobtype-' ) ) {
					$id = strtoupper( str_replace( 'jobtype-', '', $_GET[ 'tab' ] ) );
					$job_types[ $id ]->edit_tab( $jobid );
				}
				echo '</div>';
		}
		echo '<p class="submit">';
		submit_button( __( 'Save changes', 'backwpup' ), 'primary', 'save', FALSE, array( 'tabindex' => '2', 'accesskey' => 'p' ) );
		echo '</p></form>';
		?>
    </div>

    <script type="text/javascript">
	    jQuery(document).ready(function ($) {
	        // auto post if things changed
	        var changed = false;
	        $( '#editjob' ).change( function () {
	            changed = true;
	        });
			$( '.nav-tab' ).click( function () {
				if ( changed ) {
					$( 'input[name="nexttab"]' ).val( $(this).data( "nexttab" ) );
					$( '#editjob' ).submit();
					return false;
	            }
			});
	    });
    </script>
		<?php
		//add inline js
		if ( strstr( $_GET[ 'tab' ], 'dest-' ) ) {
			$dest_object = BackWPup::get_destination( str_replace( 'dest-', '', sanitize_text_field( $_GET[ 'tab' ] ) ) );
			$dest_object->edit_inline_js();
		}
		if ( strstr( $_GET[ 'tab' ], 'jobtype-' ) ) {
			$id = strtoupper( str_replace( 'jobtype-', '', sanitize_text_field( $_GET[ 'tab' ] ) ) );
			$job_types[ $id ]->edit_inline_js();
		}

	}
}

