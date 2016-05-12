<?php


/* Sicherheitsabfrage */
if ( ! class_exists('Antispam_Bee') ) {
	die();
}


/**
* Antispam_Bee_GUI
*
* @since  2.4
*/

class Antispam_Bee_GUI extends Antispam_Bee {


	/**
	* Speicherung der GUI
	*
	* @since   0.1
	* @change  2.5.2
	*/

	public static function save_changes()
	{
		/* Kein POST? */
		if ( empty($_POST) ) {
			wp_die(__('Cheatin&#8217; uh?', 'antispam-bee'));
		}

		/* Capability check */
		if ( ! current_user_can('manage_options') ) {
			wp_die(__('Cheatin&#8217; uh?', 'antispam-bee'));
		}

		/* Referer prüfen */
		check_admin_referer('_antispam_bee__settings_nonce');

		/* Optionen ermitteln */
		$options = array(
			'flag_spam' 		=> (int)(!empty($_POST['ab_flag_spam'])),
			'email_notify' 		=> (int)(!empty($_POST['ab_email_notify'])),
			'cronjob_enable' 	=> (int)(!empty($_POST['ab_cronjob_enable'])),
			'cronjob_interval'	=> (int)self::get_key($_POST, 'ab_cronjob_interval'),

			'no_notice' 		=> (int)(!empty($_POST['ab_no_notice'])),

			'dashboard_count' 	=> (int)(!empty($_POST['ab_dashboard_count'])),
			'dashboard_chart' 	=> (int)(!empty($_POST['ab_dashboard_chart'])),
			'advanced_check' 	=> (int)(!empty($_POST['ab_advanced_check'])),
			'regexp_check' 		=> (int)(!empty($_POST['ab_regexp_check'])),
			'spam_ip' 			=> (int)(!empty($_POST['ab_spam_ip'])),
			'already_commented'	=> (int)(!empty($_POST['ab_already_commented'])),
			'time_check'		=> (int)(!empty($_POST['ab_time_check'])),
			'always_allowed' 	=> (int)(!empty($_POST['ab_always_allowed'])),

			'ignore_pings' 		=> (int)(!empty($_POST['ab_ignore_pings'])),
			'ignore_filter' 	=> (int)(!empty($_POST['ab_ignore_filter'])),
			'ignore_type' 		=> (int)self::get_key($_POST, 'ab_ignore_type'),

			'reasons_enable' 	=> (int)(!empty($_POST['ab_reasons_enable'])),
			'ignore_reasons' 	=> (array)self::get_key($_POST, 'ab_ignore_reasons'),

			'bbcode_check'		=> (int)(!empty($_POST['ab_bbcode_check'])),
			'gravatar_check'	=> (int)(!empty($_POST['ab_gravatar_check'])),
			'dnsbl_check'		=> (int)(!empty($_POST['ab_dnsbl_check']))
		);

		/* Keine Tagmenge eingetragen? */
		if ( empty($options['cronjob_interval']) ) {
			$options['cronjob_enable'] = 0;
		}

		/* Liste der Spamgründe */
		if ( empty($options['reasons_enable']) ) {
			$options['ignore_reasons'] = array();
		}

		/* Cron stoppen? */
		if ( $options['cronjob_enable'] && !self::get_option('cronjob_enable') ) {
			self::init_scheduled_hook();
		} else if ( !$options['cronjob_enable'] && self::get_option('cronjob_enable') ) {
			self::clear_scheduled_hook();
		}

		/* Optionen speichern */
		self::update_options($options);

		/* Redirect */
		wp_safe_redirect(
			add_query_arg(
				array(
					'updated' => 'true'
				),
				wp_get_referer()
			)
		);

		die();
	}


	/**
	* Generierung eine Selectbox
	*
	* @since   2.4.5
	* @change  2.4.5
	*
	* @param   string  $name      Name der Selectbox
	* @param   array   $data      Array mit Werten
	* @param   string  $selected  Selektierter Wert
	* @return  string  $html      Erzeugtes HTML
	*/

	private static function _build_select($name, $data, $selected)
	{
		/* Start HTML */
		$html = '<select name="' .$name. '">';

		/* Loop options */
		foreach( $data as $k => $v) {
			$html .= '<option value="' .esc_attr($k). '" ' .selected($selected, $k, false). '>' .esc_html__($v, 'antispam-bee'). '</option>';
		}

		/* Close HTML */
		$html .= '</select>';

		return $html;
	}


	/**
	* Anzeige der GUI
	*
	* @since   0.1
	* @change  2.6.4
	*/

	public static function options_page() { ?>
		<div class="wrap" id="ab_main">
			<h2>
				Antispam Bee
			</h2>

			<form action="<?php echo admin_url('admin-post.php') ?>" method="post">
				<input type="hidden" name="action" value="ab_save_changes" />

				<?php wp_nonce_field('_antispam_bee__settings_nonce') ?>

				<?php $options = self::get_options() ?>

				<div class="ab-wrap">
					<!--[if lt IE 9]>
						<p class="browsehappy">
							<a href="http://browsehappy.com">Browse Happy</a>
						</p>
					<![endif]-->

					<div class="ab-column ab-arrow">
						<h3 class="icon">
							<?php esc_html_e('Antispam filter', 'antispam-bee') ?>
						</h3>
						<h6>
							<?php esc_html_e('Filter in the execution order', 'antispam-bee') ?>
						</h6>

						<ul>
							<li>
								<input type="checkbox" name="ab_already_commented" id="ab_already_commented" value="1" <?php checked($options['already_commented'], 1) ?> />
								<label for="ab_already_commented">
									<?php esc_html_e('Trust approved commenters', 'antispam-bee') ?>
									<span><?php esc_html_e('Always approve previously approved users', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_gravatar_check" id="ab_gravatar_check" value="1" <?php checked($options['gravatar_check'], 1) ?> />
								<label for="ab_gravatar_check">
									<?php esc_html_e('Trust commenters with a Gravatar', 'antispam-bee') ?>
									<span><?php _e('Check if commenter has a Gravatar image', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_time_check" id="ab_time_check" value="1" <?php checked($options['time_check'], 1) ?> />
								<label for="ab_time_check">
									<?php esc_html_e('Consider the comment time', 'antispam-bee') ?>
									<span><?php esc_html_e('Not recommended when using page caching', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_bbcode_check" id="ab_bbcode_check" value="1" <?php checked($options['bbcode_check'], 1) ?> />
								<label for="ab_bbcode_check">
									<?php esc_html_e('BBCode is spam', 'antispam-bee') ?>
									<span><?php esc_html_e('Review the comment contents for BBCode links', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_advanced_check" id="ab_advanced_check" value="1" <?php checked($options['advanced_check'], 1) ?> />
								<label for="ab_advanced_check">
									<?php esc_html_e('Validate the ip address of commenters', 'antispam-bee') ?>
									<span><?php esc_html_e('Validity check for used ip address', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_regexp_check" id="ab_regexp_check" value="1" <?php checked($options['regexp_check'], 1) ?> />
								<label for="ab_regexp_check">
									<?php esc_html_e('Use regular expressions', 'antispam-bee') ?>
									<span><?php _e('Predefined and custom patterns by <a href="https://gist.github.com/4242142" target="_blank">plugin hook</a>', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_spam_ip" id="ab_spam_ip" value="1" <?php checked($options['spam_ip'], 1) ?> />
								<label for="ab_spam_ip">
									<?php esc_html_e('Look in the local spam database', 'antispam-bee') ?>
									<span><?php esc_html_e('Already marked as spam? Yes? No?', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_dnsbl_check" id="ab_dnsbl_check" value="1" <?php checked($options['dnsbl_check'], 1) ?> />
								<label for="ab_dnsbl_check">
									<?php esc_html_e('Use a public antispam database', 'antispam-bee') ?>
									<span><?php _e('Matching the ip address with <a href="https://dnsbl.tornevall.org" target="_blank">Tornevall</a>', 'antispam-bee') ?></span>
								</label>
							</li>
						</ul>
					</div>


					<div class="ab-column ab-join">
						<h3 class="icon advanced">
							<?php esc_html_e('Advanced', 'antispam-bee') ?>
						</h3>
						<h6>
							<?php esc_html_e('Other antispam tools', 'antispam-bee') ?>
						</h6>

						<ul>
							<li>
								<input type="checkbox" name="ab_flag_spam" id="ab_flag_spam" value="1" <?php checked($options['flag_spam'], 1) ?> />
								<label for="ab_flag_spam">
									<?php esc_html_e('Mark as spam, do not delete', 'antispam-bee') ?>
									<span><?php esc_html_e('Keep the spam in my blog.', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_email_notify" id="ab_email_notify" value="1" <?php checked($options['email_notify'], 1) ?> />
								<label for="ab_email_notify">
									<?php esc_html_e('Notification by email', 'antispam-bee') ?>
									<span><?php esc_html_e('Notify admins by e-mail about incoming spam', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_no_notice" id="ab_no_notice" value="1" <?php checked($options['no_notice'], 1) ?> />
								<label for="ab_no_notice">
									<?php esc_html_e('Not save the spam reason', 'antispam-bee') ?>
									<span><?php esc_html_e('Spam reason as table column in the spam overview', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_cronjob_enable" id="ab_cronjob_enable" value="1" <?php checked($options['cronjob_enable'], 1) ?> />
								<label>
									<?php echo sprintf(
										esc_html__('Delete existing spam after %s days', 'antispam-bee'),
										'<input type="text" name="ab_cronjob_interval" value="' .esc_attr($options['cronjob_interval']). '" class="ab-mini-field" />'
									) ?>
									<span><?php esc_html_e('Cleaning up the database from old entries', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_ignore_filter" id="ab_ignore_filter" value="1" <?php checked($options['ignore_filter'], 1) ?> />
								<label>
									<?php echo sprintf(
										esc_html__('Limit approval to %s', 'antispam-bee'),
										self::_build_select(
											'ab_ignore_type',
											array(
												1 => 'Comments',
												2 => 'Pings'
											),
											$options['ignore_type']
										)
									); ?>
									<span><?php esc_html_e('Other types of spam will be deleted immediately', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_reasons_enable" id="ab_reasons_enable" value="1" <?php checked($options['reasons_enable'], 1) ?> />
								<label for="ab_reasons_enable">
									<?php esc_html_e('Delete comments by spam reasons', 'antispam-bee') ?>
									<span><?php esc_html_e('For multiple selections press Ctrl/CMD', 'antispam-bee') ?></span>
								</label>

								<ul>
									<li>
										<select name="ab_ignore_reasons[]" id="ab_ignore_reasons" size="2" multiple>
											<?php foreach ( self::$defaults['reasons'] as $k => $v ) { ?>
												<option <?php selected(in_array($k, $options['ignore_reasons']), true); ?> value="<?php echo $k ?>"><?php esc_html_e($v, 'antispam-bee') ?></option>
											<?php } ?>
										</select>
										<label for="ab_ignore_reasons">
											<?php esc_html_e('Spam Reason', 'antispam-bee') ?>
										</label>
									</li>
								</ul>
							</li>
						</ul>
					</div>


					<div class="ab-column ab-diff">
						<h3 class="icon more">
							<?php esc_html_e('More', 'antispam-bee') ?>
						</h3>
						<h6>
							<?php esc_html_e('A few little things', 'antispam-bee') ?>
						</h6>

						<ul>
							<li>
								<input type="checkbox" name="ab_dashboard_chart" id="ab_dashboard_chart" value="1" <?php checked($options['dashboard_chart'], 1) ?> />
								<label for="ab_dashboard_chart">
									<?php esc_html_e('Statistics on the dashboard', 'antispam-bee') ?>
									<span><?php esc_html_e('Daily updates of spam detection rate', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_dashboard_count" id="ab_dashboard_count" value="1" <?php checked($options['dashboard_count'], 1) ?> />
								<label for="ab_dashboard_count">
									<?php esc_html_e('Spam counter on the dashboard', 'antispam-bee') ?>
									<span><?php esc_html_e('Amount of identified spam comments', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_ignore_pings" id="ab_ignore_pings" value="1" <?php checked($options['ignore_pings'], 1) ?> />
								<label for="ab_ignore_pings">
									<?php esc_html_e('Do not check trackbacks / pingbacks', 'antispam-bee') ?>
									<span><?php esc_html_e('No spam check for trackback notifications', 'antispam-bee') ?></span>
								</label>
							</li>

							<li>
								<input type="checkbox" name="ab_always_allowed" id="ab_always_allowed" value="1" <?php checked($options['always_allowed'], 1) ?> />
								<label for="ab_always_allowed">
									<?php esc_html_e('Comment form used outside of posts', 'antispam-bee') ?>
									<span><?php esc_html_e('Check for comment forms on archive pages', 'antispam-bee') ?></span>
								</label>
							</li>
						</ul>
					</div>

					<div class="ab-column ab-column--service">
						<?php if ( substr( get_locale(), 0, 3 ) === 'de_' ) { ?>
							<p>
								<a href="https://github.com/pluginkollektiv/antispam-bee/wiki/" target="_blank">Dokumentation</a>
							</p>
						<?php } ?>

						<p>
							<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LG5VC9KXMAYXJ" target="_blank">PayPal</a>
						</p>
					</div>

					<div class="ab-column ab-column--submit">
						<input type="submit" class="button button-primary" value="<?php _e('Save Changes', 'antispam-bee') ?>" />
					</div>
				</div>
			</form>
		</div>
	<?php }
}
