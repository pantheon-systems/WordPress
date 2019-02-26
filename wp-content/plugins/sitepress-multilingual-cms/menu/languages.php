<?php
	/* var WPML_Language_Switcher $wpml_language_switcher */
	global $sitepress, $sitepress_settings, $wpdb, $wpml_language_switcher;

	if ( ! is_plugin_active( WPML_PLUGIN_BASENAME ) ) {
		?>
		<h2><?php esc_html_e( 'Setup WPML', 'sitepress' ) ?></h2>
		<div class="updated fade">
			<p style="line-height:1.5"><?php esc_html_e( 'The WPML Multilingual CMS plugin is not currently enabled.', 'sitepress' ) ?></p>
            <p style="line-height:1.5">
				<?php echo sprintf(
					esc_html__( 'Please go to the %sPlugins%s page and enable the WPML Multilingual CMS plugin before trying to configure the plugin.', 'sitepress' ),
					'<a href="plugins.php">', '</a>'
				) ?>
            </p>
		</div>
		<?php
		return;
	}

	if ( isset( $_GET[ 'trop' ] ) ) {
		require_once dirname( __FILE__ ) . '/edit-languages.php';
		global $icl_edit_languages;
		$flags_factory = new WPML_Flags_Factory( $wpdb );
		$icl_edit_languages = new SitePress_EditLanguages( $flags_factory->create() );
		$icl_edit_languages->render();
		return;
		}

	$sitepress_settings                 = get_option( 'icl_sitepress_settings' );
	$setup_wizard_step                  = (int) $sitepress->get_setting( 'setup_wizard_step' );
	$setup_complete                     = $sitepress->get_setting( 'setup_complete' );
	$active_languages                   = $sitepress->get_active_languages();
	$hidden_languages                   = $sitepress->get_setting( 'hidden_languages' );
	$show_untranslated_blog_posts       = $sitepress->get_setting( 'show_untranslated_blog_posts' );
	$automatic_redirect                 = $sitepress->get_setting( 'automatic_redirect' );
	$setting_urls                       = $sitepress->get_setting( 'urls' );
	$existing_content_language_verified = $sitepress->get_setting( 'existing_content_language_verified' );
	$language_negotiation_type          = $sitepress->get_setting( 'language_negotiation_type' );
	$seo                                = $sitepress->get_setting( 'seo' );
	$default_language                   = $sitepress->get_default_language();
	$all_languages                      = $sitepress->get_languages( $sitepress->get_admin_language() );
	$sample_lang                        = false;
	$default_language_details           = false;
	$wp_api                             = $sitepress->get_wp_api();
	$should_hide_admin_language         = $wp_api->version_compare_naked( get_bloginfo( 'version' ), '4.7', '>=' );
	$encryptor                          = new WPML_Data_Encryptor();
	$inactive_content                   = null;

	if(!$existing_content_language_verified ){
		// try to determine the blog language
		$blog_current_lang = 0;
		if($blog_lang = get_option('WPLANG')){
			$exp = explode('_',$blog_lang);
			$blog_current_lang = $wpdb->get_var(
									$wpdb->prepare("SELECT code FROM {$wpdb->prefix}icl_languages WHERE code= %s",
												   $exp[0]));
		}
		if(!$blog_current_lang && defined('WPLANG') && WPLANG != ''){
			$blog_current_lang = $wpdb->get_var($wpdb->prepare("SELECT code FROM {$wpdb->prefix}icl_languages WHERE default_locale=%s", WPLANG));
			if(!$blog_current_lang){
				$blog_lang = WPLANG;
				$exp = explode('_',$blog_lang);
				$blog_current_lang = $wpdb->get_var(
					$wpdb->prepare("SELECT code FROM {$wpdb->prefix}icl_languages WHERE code= %s",
								   $exp[0]));
			}
		}
		if(!$blog_current_lang){
			$blog_current_lang = 'en';
		}
		$languages = $sitepress->get_languages( $blog_current_lang, false, true, false, 'display_name' );
	}else{
		$languages = $sitepress->get_languages( $sitepress->get_admin_language(), false, true, false, 'display_name' );
		foreach($active_languages as $lang){
			if($lang['code'] != $default_language ){
				$sample_lang = $lang;
				break;
			}
		}
		$default_language_details = $sitepress->get_language_details( $default_language );
		$inactive_content         = new WPML_Inactive_Content( $wpdb, $sitepress->get_current_language() );
	}
global $language_switcher_defaults, $language_switcher_defaults_alt;

$theme_wpml_config_file = WPML_Config::get_theme_wpml_config_file();


?>
<?php $sitepress->noscript_notice() ?>

<?php if($setup_complete || SitePress_Setup::languages_table_is_complete()) { ?>
<div class="wrap wpml-settings-container<?php if( empty( $setup_complete ) ): ?> wpml-wizard<?php endif; ?>">
	<h2><?php esc_html_e( 'Setup WPML', 'sitepress' ) ?></h2>

	<?php
  $compatibility_reports_args = array(
	  'plugin_name'        => 'WPML',
	  'plugin_uri'         => 'http://wpml.org',
	  'plugin_site'        => 'wpml.org',
	  'use_styles'         => true,
	  'privacy_policy_url' => 'https://wpml.org/documentation/privacy-policy-and-gdpr-compliance/?utm_source=wpmlplugin&utm_campaign=compatibility-reporting&utm_medium=wpml-setup&utm_term=privacy-policy-and-gdpr-compliance',
	  'plugin_repository'  => 'wpml',
  );

  if ( ! $sitepress->is_setup_complete() ) {
	  $wizard_progress = new WPML_Setup_Wizard_Progress( $setup_wizard_step, array(
		  1 => __( '1. Content language', 'sitepress' ),
		  2 => __( '2. Translation languages', 'sitepress' ),
		  3 => __( '3. Language switcher', 'sitepress' ),
		  4 => __( '4. Compatibility', 'sitepress' ),
		  5 => __( '5. Registration', 'sitepress' ),
	  ) );
	  $wizard_progress->render();
  }

	if ( ! $existing_content_language_verified || $setup_wizard_step <= 1 ): ?>
		<?php
		$setup_step = new WPML_Setup_Step_One_Menu( $sitepress );
		echo $setup_step->render();
		?>
	<?php else: ?>
		<?php
		if(!empty( $setup_complete ) || $setup_wizard_step == 2): ?>
		<?php if ( count( $active_languages ) > 1 ): ?>
				<p>
					<strong><?php esc_html_e( 'This screen contains the language settings for your site.', 'sitepress' ) ?></strong>
				</p>
				<ul class="wpml-navigation-links js-wpml-navigation-links">
					<?php
					$navigation_items = array(
						'#lang-sec-11'  => __( 'Theme and plugins reporting', 'sitepress' ),
						'#lang-sec-1'   => __( 'Site Languages', 'sitepress' ),
						'#lang-sec-2'   => __( 'Language URL format', 'sitepress' ),
						'#lang-sec-4'   => __( 'Admin language', 'sitepress' ),
						'#lang-sec-7'   => __( 'Hide languages', 'sitepress' ),
						'#lang-sec-8'   => __( 'Make themes work multilingual', 'sitepress' ),
						'#lang-sec-9'   => __( 'Browser language redirect', 'sitepress' ),
						'#lang-sec-9-5' => __( 'SEO Options', 'sitepress' ),
						'#cookie'       => __( 'Language filtering for AJAX operations', 'sitepress' ),
						'#lang-sec-10'  => __( 'WPML love', 'sitepress' ),
					);

					if( $should_hide_admin_language && array_key_exists( '#lang-sec-4', $navigation_items ) ) {
						unset( $navigation_items['#lang-sec-4'] );
					}

					/**
					 * @param array $navigation_items
					 */
					$navigation_items = apply_filters( 'wpml_admin_languages_navigation_items', $navigation_items );

					foreach ( $navigation_items  as $link => $text ) {
						echo '<li><a href="' . esc_attr( $link ) . '">' . esc_html( $text ) . '</a></li>';
					}
					?>
				</ul>
			<?php endif; ?>

		<?php
		if ( $setup_complete ) {
			?>
					<div class="wpml-section wpml-section-wpml-theme-and-plugins-reporting" id="lang-sec-11">
						<div class="wpml-section-header">
							<h3><?php esc_html_e( 'Reporting to wpml.org', 'sitepress' ) ?></h3>
						</div>
						<div class="wpml-section-content">
				<?php
				$compatibility_reports_after_setup_args                   = $compatibility_reports_args;
				$compatibility_reports_after_setup_args['custom_heading'] = '';
				$compatibility_reports_after_setup_args['use_radio']      = false;

				do_action( 'otgs_installer_render_local_components_setting', $compatibility_reports_after_setup_args );
				?>
						</div>
					</div>
			<?php
		}
		?>

			<div id="lang-sec-1" class="wpml-section wpml-section-languages">
				<div class="wpml-section-header">
					<h3><?php _e('Site Languages', 'sitepress') ?></h3>
				</div>

				<div class="wpml-section-content">
					<div class="wpml-section-content-inner">
						<?php if(!empty( $setup_complete )): ?>
							<h4><?php _e('These languages are enabled for this site:','sitepress'); ?></h4>

							<?php do_action( 'wpml_before_active_languages_display' ); ?>

							<ul id="icl_enabled_languages" class="enabled-languages">
									<?php foreach($active_languages as $lang): $is_default = ( $default_language ==$lang['code']); ?>
									<?php
									if(!empty( $hidden_languages ) && in_array($lang['code'], $hidden_languages )){
										$hidden = '&nbsp<strong style="color:#f00">(' . esc_html__( 'hidden', 'sitepress' ) . ')</strong>';
									}else{
										$hidden = '';
									}
									?>

								<li <?php if($is_default):?>class="selected"<?php endif;?>>
									<input id="default_language_<?php echo esc_attr( $lang['code'] ) ?>"
                                           name="default_language" type="radio"
                                           value="<?php echo esc_attr( $lang['code'] ) ?>" <?php checked( $is_default ) ?> />
									<label for="default_language_<?php echo esc_attr( $lang['code'] ) ?>">
                                        <?php echo esc_html( $lang['display_name'] ) . $hidden ?>
										<?php if ( $is_default ): ?>(<?php esc_html_e( 'default', 'sitepress' ) ?>)<?php endif ?>
                                    </label>
								</li>
								<?php endforeach ?>
							</ul>
						<?php do_action( 'wpml_after_active_languages_display' ); ?>
						<?php else: ?>
							<p class="wpml-wizard-instruction">
                                <?php esc_html_e( 'Select the languages to enable for your site (you can also add and remove languages later).', 'sitepress' ) ?>
                            </p>
						<?php endif; ?>
						<?php wp_nonce_field('wpml_set_default_language', 'set_default_language_nonce'); ?>
						<p class="buttons-wrap">
							<button id="icl_cancel_default_button" class="button-secondary action"><?php esc_html_e( 'Cancel', 'sitepress' ) ?></button>
							<button id="icl_save_default_button" class="button-primary action"><?php esc_html_e( 'Save', 'sitepress' ) ?></button>
						</p>
						<?php if(!empty( $setup_complete )): ?>
							<p>
								<button id="icl_change_default_button" class="button-secondary action <?php if(count($active_languages) < 2): ?>hidden<?php endif ?>">
                                    <?php esc_html_e( 'Change default language', 'sitepress' ) ?>
                                </button>
								<button id="icl_add_remove_button" class="button-secondary action">
                                    <?php esc_html_e( 'Add / Remove languages', 'sitepress' ) ?>
                                </button>
							</p>
							<p class="icl_ajx_response" id="icl_ajx_response"></p>
						<?php endif; ?>
						<div id="icl_avail_languages_picker" class="<?php if( !empty( $setup_complete ) ) echo 'hidden'; ?>">
							<ul class="available-languages">
								<?php
								foreach ( $languages as $lang ) {
									$checked  = checked( '1', $lang['active'], false );
									$disabled = disabled( $default_language, $lang['code'], false );

									$language_item_classes = array();
									if ( (bool) $lang['active'] ) {
										$language_item_classes[] = 'wpml-selected';
									}
									?>
									<li class="<?php echo implode( ' ', $language_item_classes ); ?>">
										<label for="wpml-language-<?php echo $lang['code'] ?>">
											<input type="checkbox" id="wpml-language-<?php echo esc_attr( $lang['code'] ) ?>"
                                                   value="<?php echo esc_attr( $lang['code'] ) ?>" <?php echo $checked . ' ' . $disabled; ?>/>
											<img src="<?php echo $sitepress->get_flag_url( $lang['code'] ) ?>" width="18" height="12">
											<?php echo esc_html( $lang['display_name'] ) ?>
										</label>
									</li>
									<?php
								}
								?>
							</ul>
							<?php if(!empty( $setup_complete )): ?>
							<p class="buttons-wrap">
								<input id="icl_cancel_language_selection" type="button" class="button-secondary action"
                                       value="<?php esc_attr_e( 'Cancel', 'sitepress' ) ?>" />
								<input id="icl_save_language_selection" type="button" class="button-primary action"
                                       value="<?php esc_attr_e( 'Save', 'sitepress' ) ?>" />
							</p>
							<?php endif; ?>
							<?php wp_nonce_field('wpml_set_active_languages', 'set_active_languages_nonce'); ?>
						</div>

						<?php if (!empty( $setup_complete )): ?>
							<p>
								<a href="admin.php?page=<?php echo WPML_PLUGIN_FOLDER ?>/menu/languages.php&amp;trop=1">
                                    <?php esc_html_e( 'Edit Languages', 'sitepress' ) ?>
                                </a>
							</p>
						<?php endif; ?>
					</div> <!-- wpml-section-content-inner -->

					<?php if ( $inactive_content && $inactive_content->has_entries() ) : ?>
						<div class="wpml-section-content-inner">
							<?php
							$render_inactive_content = new WPML_Inactive_Content_Render(
								$inactive_content,
								array( WPML_PLUGIN_PATH . '/templates/languages/' )
							);

							echo $render_inactive_content->render();
							?>
						</div> <!-- wpml-section-content-inner -->
					<?php endif; ?>

					<?php if ( 2 === (int) $setup_wizard_step ): ?>
						<footer class="clearfix text-right">
							<input id="icl_setup_back_1" class="button-secondary alignleft" name="save" value="<?php esc_attr_e( 'Back', 'sitepress' ) ?>" type="button" />
							<?php wp_nonce_field('setup_got_to_step1_nonce', '_icl_nonce_gts1'); ?>
							<input id="icl_setup_next_1" class="button-primary alignright" name="save"
                                   value="<?php esc_attr_e( 'Next', 'sitepress' ) ?>" type="button"
                                <?php disabled( count( $active_languages ) < 2 ) ?> />
						</footer>
					<?php endif; ?>

				</div> <!-- .wcml-section-content -->
			</div> <!-- .wpml-section-languages -->

	<?php elseif ( $setup_wizard_step === 4 ): ?>

			<div class="wpml-section wpml-section-wpml-theme-and-plugins-reporting" id="lang-sec-compatibility">
				<div class="wpml-section-header">
					<h3>
			  <?php esc_html_e( 'Compatibility reporting', 'sitepress' ); ?>
					</h3>
				</div>
				<div class="wpml-section-content">
					<p class="wpml-wizard-instruction">
			  <?php esc_html_e( 'The WPML plugin can send a list of active plugins and theme used in your site to wpml.org. This allows our support team to help you much faster and to contact you in advance about potential compatibility problems and their solutions.',
			                    'sitepress' ); ?>
					</p>
			<?php
			$compatibility_reports_before_setup_args              = $compatibility_reports_args;
			$compatibility_reports_before_setup_args['use_radio'] = true;
			do_action( 'otgs_installer_render_local_components_setting', $compatibility_reports_before_setup_args );
			?>
				</div>

				<footer class="clearfix text-right">
					<input id="icl_setup_back_3"
					       class="button-secondary alignleft"
					       name="save"
					       value="<?php esc_attr_e( 'Back', 'sitepress' ) ?>"
					       type="button"/>
			<?php wp_nonce_field( 'setup_got_to_step3_nonce', '_icl_nonce_gts3' ); ?>
					<input id="icl_setup_next_5" class="button-primary alignright" name="save"
					       value="<?php esc_attr_e( 'Next', 'sitepress' ) ?>" type="button"
					/>
			<?php wp_nonce_field( 'setup_got_to_step5_nonce', '_icl_nonce_gts5' ); ?>
				</footer>
			</div>

	<?php
		elseif ( $setup_wizard_step === 5 ): ?>
		<?php $site_key = WP_Installer()->get_site_key('wpml'); ?>
		<div class="wpml-section" id="lang-sec-0">
			<div class="wpml-section-header">
				<h3><?php esc_html_e( 'Registration', 'sitepress' ) ?></h3>
			</div>
			<div class="wpml-section-content">

				<?php if(is_multisite() && !empty($site_key)): ?>

				<p class="wpml-wizard-instruction"><?php esc_html_e( 'WPML is already registered network-wide.', 'sitepress' ) ?></p>
				<footer class="clearfix text-right">
					<form id="installer_registration_form">
						<input type="hidden" name="action" value="installer_save_key" />
						<input type="hidden" name="button_action" value="installer_save_key" />
						<input <?php if(empty($site_key)): ?>style="display: none;"<?php endif; ?> class="button-primary button-large"
                               name="finish" value="<?php esc_attr_e( 'Finish', 'sitepress' ) ?>" type="submit" />
						<?php wp_nonce_field('registration_form_submit_nonce', '_icl_nonce'); ?>
					</form>
				</footer>

				<?php else: ?>

				<p class="wpml-wizard-instruction">
                    <?php esc_html_e( 'Enter the site key, from your wpml.org account, to receive automatic updates for WPML on this site.', 'sitepress' ) ?>
                </p>
				<form id="installer_registration_form">
					<input type="hidden" name="action" value="installer_save_key" />
					<input type="hidden" name="button_action" value="installer_save_key" />
					<label for="installer_site_key">
						<?php esc_html_e( 'Site key:', 'sitepress' ) ?>
						<input type="text" name="installer_site_key" value="<?php echo esc_attr( $site_key ) ?>" <?php disabled( ! empty( $site_key ) ) ?> />
					</label>

					<div class="status_msg<?php if ( ! empty( $site_key ) ): ?> icl_valid_text<?php endif; ?>">
						<?php
						if ( $site_key ) {
                            esc_html_e( 'Thank you for registering WPML on this site. You will receive automatic updates when new versions are available.', 'sitepress' );
                        }
                        ?>
					</div>

					<div class="text-right">
						<?php if ( empty( $site_key ) ): ?>
						<input class="button-primary" name="register" value="<?php esc_attr_e( 'Register', 'sitepress' ) ?>" type="submit" />
						<input class="button-secondary" name="later" value="<?php esc_attr_e( 'Remind me later', 'sitepress' ) ?>" type="submit" />
						<?php endif; ?>
						<input <?php if(empty($site_key)): ?>style="display: none;"<?php endif; ?> class="button-primary button-large button" name="finish"
                               value="<?php esc_attr_e( 'Finish', 'sitepress' ) ?>" type="submit" />

						<?php wp_nonce_field('registration_form_submit_nonce', '_icl_nonce'); ?>
					</div>

				</form>

				<?php endif; ?>

				<?php if(empty($site_key)): ?>
					<hr class="wpml-margin-top-base">
				<p>
                    <?php
                    printf(
                        esc_html__( "Don't have a key for this site? %sGenerate a key for this site%s", 'sitepress' ),
                        '<a class="button-primary" href="https://wpml.org/my-account/sites/?add=' . urlencode( get_site_url() ) . '" target="_blank">', '</a>'
                    )
                    ?>
                </p>
				<p>
                    <?php
                    printf(
                        esc_html__( "If you don't have a WPML.org account or a valid subscription, you can %spurchase%s one and get later upgrades, full support and 30 days money-back guarantee.", 'sitepress' ),
                        '<a href="http://wpml.org/purchase/" target="_blank">', '</a>'
                    )
                    ?>
                </p>
				<?php endif; ?>
			</div>
		</div>

		<?php endif; ?>


		<?php if(!empty( $setup_complete )): ?>
			<?php
			if ( !class_exists ( 'WP_Http' ) ) {
				include_once ABSPATH . WPINC . '/class-http.php';
			}
			/**
			 * @var WPML_URL_Converter $wpml_url_converter
			 * @var WPML_Request $wpml_request_handler
			 */
			global $wpml_url_converter, $wpml_request_handler;

			$validator = wpml_get_langs_in_dirs_val ( new WP_Http(), $wpml_url_converter );
			?>
			<?php if(count($active_languages) > 1): ?>
				<div class="wpml-section wpml-section-url-format" id="lang-sec-2">
					<div class="wpml-section-header">
						<h3><?php esc_html_e( 'Language URL format', 'sitepress' ) ?></h3>
					</div>
					<div class="wpml-section-content">
						<h4><?php esc_html_e( 'Choose how to determine which language visitors see contents in', 'sitepress' ) ?></h4>
						<form id="icl_save_language_negotiation_type" name="icl_save_language_negotiation_type" action="">
							<?php wp_nonce_field('save_language_negotiation_type', 'save_language_negotiation_type_nonce') ?>
							<ul>
								<?php

								$abs_home = $wpml_url_converter->get_abs_home();
								$icl_folder_url_disabled = $validator->validate_langs_in_dirs ( $sample_lang['code'] );
								?>
								<li>
									<label>
										<input type="radio" name="icl_language_negotiation_type" value="1" <?php checked( 1 == $language_negotiation_type ) ?> />
										<?php esc_html_e( 'Different languages in directories', 'sitepress' ) ?>
										<span class="explanation-text">
										(<?php
											$root = !empty( $setting_urls['directory_for_default_language']);
											echo $validator->print_explanation( $sample_lang['code'], $root );?>)
										</span>
									</label>

									<div id="icl_use_directory_wrap" style="<?php if( $language_negotiation_type != 1): ?>display:none;<?php endif; ?>" >
										<p class="sub-section">
											<label>
												<input type="checkbox" name="use_directory" id="icl_use_directory" value="1"
                                                    <?php checked( ! empty( $setting_urls['directory_for_default_language'] ) ) ?> />
												<?php esc_html_e( 'Use directory for default language', 'sitepress' ) ?>
											</label>
										</p>

										<div id="icl_use_directory_details" class="sub-section" <?php if(empty( $setting_urls['directory_for_default_language'])) echo ' style="display:none"'  ?> >

											<p><?php esc_html_e( 'What to show for the root url:', 'sitepress' ) ?></p>

											<ul>
												<li>
													<label for="wpml_show_on_root_html_file">
														<input id="wpml_show_on_root_html_file" type="radio" name="show_on_root"
                                                               value="html_file" <?php checked( 'html_file' === $setting_urls['show_on_root'] ) ?> />
														<?php esc_html_e( 'HTML file', 'sitepress' ) ?> &ndash;
                                                        <span class="explanation-text">
                                                            <?php esc_html_e( 'please enter path: absolute or relative to the WordPress installation folder', 'sitepress' ) ?>
                                                        </span>
													</label>
													<p>
														<input type="text" id="root_html_file_path" name="root_html_file_path" value="<?php echo esc_attr( $setting_urls['root_html_file_path'] ) ?>" />
														<label class="icl_error_text icl_error_1" for="root_html_file_path" style="display: none;">
                                                            <?php esc_html_e( 'Please select what to show for the root url.', 'sitepress' ) ?>
                                                        </label>
													</p>
												</li>

												<li>
													<label>
														<input id="wpml_show_on_root_page" type="radio" name="show_on_root" value="page"
                                                           <?php checked( 'page' === $setting_urls['show_on_root'] ) ?>
                                                           <?php if($setting_urls['show_on_root'] === 'page'):?>class="active"<?php endif; ?>
                                                        />
														<?php esc_html_e( 'A page', 'sitepress' ) ?>

														<span style="display: none;" id="wpml_show_page_on_root_x"><?php esc_html_e( 'Please save the settings first by clicking Save.', 'sitepress' ) ?></span>

														<span id="wpml_show_page_on_root_details" <?php if($setting_urls['show_on_root'] !== 'page'):
														?>style="display:none"<?php endif; ?>>
														<?php
														$rp_exists = false;
														if(!empty( $setting_urls['root_page'])){
															$rp = get_post( $setting_urls['root_page']);
															if($rp && $rp->post_status !== 'trash'){
																$rp_exists = true;
															}
														}
														?>
														<?php if($rp_exists): ?>
															<a href="<?php echo get_edit_post_link( $setting_urls['root_page']) ?>">
                                                                <?php esc_html_e( 'Edit root page.', 'sitepress' ) ?>
                                                            </a>
														<?php else: ?>
															<a href="<?php echo admin_url('post-new.php?post_type=page&wpml_root_page=1') ?>">
                                                                <?php esc_html_e( 'Create root page.', 'sitepress' ) ?>
                                                            </a>
														<?php endif; ?>
														</span>
														<p id="icl_hide_language_switchers" class="sub-section" <?php if($setting_urls['show_on_root'] !== 'page'): ?>style="display:none"<?php endif; ?>>
														  <label>
															  <input type="checkbox" name="hide_language_switchers" id="icl_hide_language_switchers"
                                                                     value="1" <?php checked( $setting_urls['hide_language_switchers']) ?> />
															  <?php esc_html_e( 'Hide language switchers on the root page', 'sitepress' ) ?>
														  </label>
													  </p>

													</label>
												</li>


											</ul>

										</div>
									</div>

									<?php if ( $icl_folder_url_disabled ): ?>
									<div class="icl_error_text" style="margin:10px;">
										<p>
											<?php esc_html_e( 'It looks like languages per directories will not function.', 'sitepress' ) ?>
											<a href="#" onClick="jQuery(this).parent().parent().next().toggle();return false">
                                                <?php esc_html_e( 'Details', 'sitepress' ) ?>
                                            </a>
										</p>
									</div>
									<div class="icl_error_text" style="display:none;margin:10px;">
										<p><?php esc_html_e( 'This can be a result of either:', 'sitepress' ) ?></p>
										<ul>
											<li><?php esc_html_e( "WordPress is installed in a directory (not root) and you're using default links.", 'sitepress' ) ?></li>
											<li><?php esc_html_e( 'URL rewriting is not enabled in your web server.', 'sitepress' ) ?></li>
											<li><?php esc_html_e( 'The web server cannot write to the .htaccess file', 'sitepress' ) ?></li>
										</ul>
										<a href="https://wpml.org/?page_id=1010"><?php esc_html_e( 'How to fix', 'sitepress' ) ?></a>
											<p>
												<?php printf(__('When WPML accesses <a target="_blank" href="%s">%s</a> it gets:', 'sitepress'), $__url = $validator->get_validation_url($sample_lang['code']), $__url); ?>
												<br/>
												<?php
												echo $validator->print_error_response ();
												?>
											</p>
											<p>
												<?php printf( esc_html__( 'The expected value is: %s', 'sitepress' ), '<br /><strong>&lt;!--' . get_home_url() . '--&gt;</strong>' ) ?>
											</p>
										</div>
									<?php endif; ?>
								</li>
								<?php
								global $wpmu_version;
								if ( isset( $wpmu_version ) || ( function_exists( 'is_multisite' ) && is_multisite() && ( ! defined( 'WPML_SUNRISE_MULTISITE_DOMAINS' ) || ! WPML_SUNRISE_MULTISITE_DOMAINS ) ) ) {
									$icl_lnt_disabled = 'disabled="disabled" ';
								}else{
									$icl_lnt_disabled = '';
								}
								?>
								<li>
									<label>
										<input <?php echo $icl_lnt_disabled ?>id="icl_lnt_domains" type="radio" name="icl_language_negotiation_type"
                                               value="2" <?php checked( 2 == $language_negotiation_type ) ?> />
										<?php esc_html_e( 'A different domain per language', 'sitepress' ) ?>
										<?php if($icl_lnt_disabled): ?>
											<span class="icl_error_text"><?php esc_html_e( 'This option is not yet available for Multisite installs', 'sitepress' ) ?></span>
										<?php endif; ?>
										<?php if(defined('WPML_SUNRISE_MULTISITE_DOMAINS') && WPML_SUNRISE_MULTISITE_DOMAINS): ?>
											<span class="icl_error_text"><?php esc_html_e( 'Experimental', 'sitepress' ) ?></span>
										<?php endif; ?>
									</label>
									<?php wp_nonce_field('language_domains_nonce', '_icl_nonce_ldom', false); ?>
									<?php wp_nonce_field('validate_language_domain', 'validate_language_domain_nonce', false); ?>
									<div id="icl_lnt_domains_box">
										<?php if ( (int) $language_negotiation_type === 2 ):
											$domains_box = new WPML_Lang_Domains_Box( $sitepress );
											echo $domains_box->render();
                                        endif; ?>
									</div>
									<div id="language_domain_xdomain_options" class="sub-section" <?php if( $language_negotiation_type != 2 ) echo ' style="display:none"'  ?>>
										<p><?php esc_html_e( 'Pass session arguments between domains through the language switcher', 'sitepress' ) ?></p>
										<p>
											<label>
												<input type="radio" name="icl_xdomain_data"
													   value="<?php echo WPML_XDOMAIN_DATA_GET ?>"
													   <?php checked( WPML_XDOMAIN_DATA_GET === (int) $sitepress_settings['xdomain_data'] ) ?>/>
												<?php esc_html_e( 'Pass arguments via GET (the url)', 'sitepress' ) ?>
											</label>
										</p>
										<p>
											<label>
												<input type="radio" name="icl_xdomain_data"
													   value="<?php echo WPML_XDOMAIN_DATA_POST ?>"
													   <?php checked( WPML_XDOMAIN_DATA_POST === (int) $sitepress_settings['xdomain_data'] )  ?>/>
												<?php esc_html_e( 'Pass arguments via POST', 'sitepress' ) ?>
											</label>
										</p>
										<p>
											<label>
												<input type="radio" name="icl_xdomain_data"
													   value="<?php echo WPML_XDOMAIN_DATA_OFF ?>"
													   <?php checked( WPML_XDOMAIN_DATA_OFF === (int) $sitepress_settings['xdomain_data'] ) ?>/>
												<?php esc_html_e( 'Disable this feature', 'sitepress' ) ?>
											</label>
										</p>

                                        <?php $encryptor_library = $encryptor->get_crypt_library(); ?>
										<?php if( $encryptor_library === 'mcrypt' || $encryptor_library === 'openssl' ): ?>
											<p><?php printf( esc_html__( 'The data will be encrypted with the %s algorithm.', 'sitepress' ),
													$encryptor_library === 'mcrypt' ? 'MCRYPT_RIJNDAEL_256' : 'AES-256-CTR' ) ?></p>
										<?php else: ?>
											<p><?php esc_html_e( 'Because encryption is not supported on your host, the data will only have a basic encoding with the bse64 algorithm.', 'sitepress' ) ?></p>
										<?php endif; ?>

										<p><a href="https://wpml.org/?page_id=693147" target="_blank"><?php esc_html_e( 'Learn more about passing data between domains', 'sitepress' ) ?></a></p>


									</div>

								</li>
								<li>
									<label>
										<input type="radio" name="icl_language_negotiation_type" value="3" <?php checked( 3 == $language_negotiation_type ) ?> />
										<?php esc_html_e( 'Language name added as a parameter', 'sitepress' ) ?>
										<span class="explanation-text">
                                            <?php echo sprintf( '(%s?lang=%s - %s)', get_home_url(), esc_html( $sample_lang['code'] ), esc_html( $sample_lang['display_name'] ) ) ?>
                                        </span>
									</label>
								</li>
							</ul>
							<div class="wpml-form-message" style="display:none;"></div>
							<div class="wpml-form-errors icl_form_errors" style="display: none;"></div>
							<p class="buttons-wrap">
								<span class="icl_ajx_response" id="icl_ajx_response2"></span>
								<input class="button button-primary" name="save" value="<?php esc_html_e( 'Save', 'sitepress' ) ?>" type="submit" />
							</p>
						</form>
					</div>
				</div> <!-- .wpml-section-url-format -->
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'wpml_admin_after_languages_url_format' ); ?>

		<?php if(!empty( $setup_complete ) && count($all_languages) > 1 && ! $should_hide_admin_language): ?>
			<div class="wpml-section wpml-section-admin-language" id="lang-sec-4">
				<div class="wpml-section-header">
					<h3><?php esc_html_e( 'Admin language', 'sitepress' ) ?></h3>
				</div>
				<div class="wpml-section-content">
					<form id="icl_admin_language_options" name="icl_admin_language_options" action="">
						<?php wp_nonce_field('icl_admin_language_options_nonce', '_icl_nonce'); ?>
						<?php if(is_admin()): ?>
						<p>
							<label>
								<?php _e('Default admin language: ', 'sitepress'); ?>
								<?php $default_language_details = $sitepress->get_language_details( $default_language ); ?>
								<select name="icl_admin_default_language">
									<option value="_default_"><?php printf( esc_html__( 'Default language (currently %s)', 'sitepress' ), $default_language_details['display_name'] ) ?></option>
									<?php foreach($all_languages as $al):?>
										<?php if ( $al['active'] ): ?>
											<option value="<?php echo esc_attr( $al['code'] ) ?>" <?php selected( $sitepress->get_setting( 'admin_default_language' ) == $al['code'] ) ?>>
                                            <?php
                                            echo esc_html( $al['display_name'] );
                                            if ( $sitepress->get_admin_language() != $al['code'] ) {
	                                            echo ' (' . esc_html( $al['native_name'] ) . ')';
                                            }
                                            ?>&nbsp;
                                            </option>
										<?php endif; ?>
									<?php endforeach; ?>
									<?php foreach($all_languages as $al):?>
										<?php if(!$al['active']): ?>
											<option value="<?php echo esc_attr( $al['code'] ) ?>" <?php selected( $sitepress->get_setting( 'admin_default_language' ) == $al['code'] ) ?>>
                                                <?php
                                                echo esc_html( $al['display_name'] );
                                                if ( $sitepress->get_admin_language() != $al['code'] ) {
	                                                echo ' (' . esc_html( $al['native_name'] ) . ')';
                                                }
                                                ?>&nbsp;
                                            </option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							</label>
						</p>
						<?php endif; ?>
						<p><?php printf( __( 'Each user can choose the admin language. You can edit your language preferences by visiting your <a href="%s">profile page</a>.', 'sitepress' ), 'profile.php#wpml' ) ?></p>
						<p class="buttons-wrap">
							<span class="icl_ajx_response" id="icl_ajx_response_al"></span>
							<input class="button button-primary" name="save" value="<?php esc_html_e( 'Save', 'sitepress' ) ?>" type="submit" />
						</p>
					</form>
				</div>
			</div>
		<?php endif; ?>

		<?php if(!empty( $setup_complete ) && count($active_languages) > 1): ?>

			<div class="wpml-section wpml-section-hide-languages" id="lang-sec-7">
				<div class="wpml-section-header">
					<h3><?php esc_html_e( 'Hide languages', 'sitepress' ) ?></h3>
				</div>
				<div class="wpml-section-content">
					<p><?php esc_html_e( 'You can completely hide content in specific languages from visitors and search engines, but still view it yourself. This allows reviewing translations that are in progress.', 'sitepress' ) ?></p>
					<form id="icl_hide_languages" name="icl_hide_languages" action="">
						<?php wp_nonce_field('icl_hide_languages_nonce', '_icl_nonce') ?>
						<?php foreach($active_languages as $l): ?>
						<?php if($l['code'] == $default_language_details['code']) continue; ?>
							<p>
								<label>
									<input type="checkbox" name="icl_hidden_languages[]" value="<?php echo esc_attr( $l['code'] ) ?>"
                                        <?php checked( ! empty( $hidden_languages ) && in_array( $l['code'], $hidden_languages ) ) ?>  />
                                    <?php echo esc_html( $l['display_name'] ) ?>
								</label>
							</p>
						<?php endforeach; ?>
						<p id="icl_hidden_languages_status">
							<?php
								if (!empty( $hidden_languages )){
                                    //While checking for hidden languages, it cleans any possible leftover from inactive or deleted languages
                                    if ( 1 == count( $hidden_languages ) ) {
                                        if ( isset( $active_languages[ $hidden_languages[ 0 ] ] ) ) {
	                                        printf( esc_html__( '%s is currently hidden to visitors.', 'sitepress' ), esc_html( $active_languages[ $hidden_languages[0] ]['display_name'] ) );
                                            $hidden_languages[] = $hidden_languages[ 0 ];
                                        }
                                    } else {
                                        $_hlngs = array();
                                        foreach ( $hidden_languages as $l ) {
                                            if ( isset( $active_languages[ $l ] ) ) {
                                                $_hlngs[ ] = $active_languages[ $l ][ 'display_name' ];
                                            }
                                        }
                                        $hlangs = implode( ', ', $_hlngs );
	                                    printf( esc_html__( '%s are currently hidden to visitors.', 'sitepress' ), esc_html( $hlangs ) );
                                    }

                                    $hidden_languages = array_unique($hidden_languages);
                                    $sitepress->set_setting('hidden_languages', $hidden_languages);

									 echo '<p>';
									printf( __( 'You can enable its/their display for yourself, in your <a href="%s">profile page</a>.', 'sitepress' ), 'profile.php#wpml' );
									 echo '</p>';
								}
								else {
									esc_html_e( 'All languages are currently displayed.', 'sitepress' );
								}
							?>
						</p>
						<p class="buttons-wrap">
							<span class="icl_ajx_response" id="icl_ajx_response_hl"></span>
							<input class="button button-primary" name="save" value="<?php esc_attr_e( 'Save', 'sitepress' ) ?>" type="submit" />
						</p>
					</form>
				</div>
			</div>

			<div class="wpml-section wpml-section-ml-themes" id="lang-sec-8">
				<div class="wpml-section-header">
					<h3><?php esc_html_e( 'Make themes work multilingual', 'sitepress' ) ?></h3>
				</div>
				<div class="wpml-section-content">
						<form id="icl_adjust_ids" name="icl_adjust_ids" action="">
							<?php wp_nonce_field('icl_adjust_ids_nonce', '_icl_nonce'); ?>
							<p><?php esc_html_e( 'This feature turns themes into multilingual, without having to edit their PHP files.', 'sitepress' ) ?></p>
							<p>
								<label>
									<input type="checkbox" value="1" name="icl_adjust_ids" <?php checked( $sitepress->get_setting( 'auto_adjust_ids' ) ) ?> />
									<?php esc_html_e( 'Adjust IDs for multilingual functionality', 'sitepress' ) ?>
								</label>
							</p>
							<p class="explanation-text"><?php esc_html_e( 'Note: auto-adjust IDs will increase the number of database queries for your site.', 'sitepress' ) ?></p>
							<p class="buttons-wrap">
								<span class="icl_ajx_response" id="icl_ajx_response_ai"></span>
								<input class="button button-primary" name="save" value="<?php esc_attr_e( 'Save', 'sitepress' ) ?>" type="submit" />
							</p>
						</form>
				</div>
			</div>

			<div class="wpml-section wpml-section-redirect" id="lang-sec-9">
				<div class="wpml-section-header">
					<h3><?php esc_html_e( 'Browser language redirect', 'sitepress' ) ?></h3>
				</div>
				<div class="wpml-section-content">
					<p><?php esc_html_e( 'WPML can automatically redirect visitors according to browser language.', 'sitepress' ) ?></p>
					<p class="explanation-text"><?php esc_html_e( "This feature uses Javascript. Make sure that your site doesn't have JS errors.", 'sitepress' ) ?></p>
					<form id="icl_automatic_redirect" name="icl_automatic_redirect" action="">
						<?php wp_nonce_field('icl_automatic_redirect_nonce', '_icl_nonce') ?>
						<ul>
							<li><label>
								<input type="radio" value="0" name="icl_automatic_redirect" <?php checked( empty( $automatic_redirect ) ) ?> />
								<?php esc_html_e( 'Disable browser language redirect', 'sitepress' ) ?>
							</label></li>
							<li><label>
									<input type="radio" value="1" name="icl_automatic_redirect" <?php checked( 1, $automatic_redirect ) ?> />
								<?php esc_html_e( 'Redirect visitors based on browser language only if translations exist', 'sitepress' ) ?>
							</label></li>
							<li><label>
									<input type="radio" value="2" name="icl_automatic_redirect" <?php checked( 2, $automatic_redirect ) ?> />
								<?php esc_html_e( 'Always redirect visitors based on browser language (redirect to home page if translations are missing)', 'sitepress' ) ?>
							</label></li>
						</ul>
						<ul>
							<li>
								<label>
                                    <?php
                                        printf(
	                                        esc_html__( "Remember visitors' language preference for %s hours (please enter 24 or multiples of it).", 'sitepress' ),
                                            '<input size="2" type="number" min="24" value="' . (int) $sitepress->get_setting( 'remember_language' ) . '" name="icl_remember_language" /> '
                                        );
								    ?>
								<?php if(!$wpml_request_handler->get_cookie_lang()): ?>
								<span class="icl_error_text"><?php esc_html_e( "Your browser doesn't seem to be allowing cookies to be set.", 'sitepress' ) ?></span>
								<?php endif; ?>
							</label></li>
						</ul>
						<div class="wpml-form-message update-nag js-redirect-warning"<?php if( empty( $automatic_redirect ) ) : ?> style="display: none;"<?php endif; ?>>
							<?php
							$redirect_warning_1 = esc_html__( "Browser language redirect may affect your site's indexing", 'sitepress' );
							$redirect_warning_2 = esc_html__( 'learn more', 'sitepress' );
                            $url = 'https://wpml.org/documentation/getting-started-guide/language-setup/automatic-redirect-based-on-browser-language/how-browser-language-redirect-affects-google-indexing/';
                            echo $redirect_warning_1 . '- <a href="' . $url . '" target="_blank">' . $redirect_warning_2 . '</a>';
							?>
						</div>
						<p class="buttons-wrap">
							<span class="icl_ajx_response" id="icl_ajx_response_ar"></span>
							<input class="button button-primary" name="save" value="<?php esc_attr_e( 'Save', 'sitepress' ) ?>" type="submit" />
						</p>
					</form>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>


	<?php if(!empty( $setup_complete )): ?>

		<?php
        $request_get_page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_NULL_ON_FAILURE);
        do_action('icl_extra_options_' . $request_get_page);

		$seo_ui = new WPML_SEO_HeadLangs( $sitepress, new WPML_Queried_Object_Factory() );
		$seo_ui->render_menu();
		?>
		<div class="wpml-section wpml-section-wpml-love" id="lang-sec-10">
			<div class="wpml-section-header">
				<h3><?php esc_html_e( 'WPML love', 'sitepress' ) ?></h3>
			</div>
			<div class="wpml-section-content">
				<form id="icl_promote_form" name="icl_promote_form" action="">
					<?php wp_nonce_field('icl_promote_form_nonce', '_icl_nonce'); ?>
					<p>
						<label><input type="checkbox" name="icl_promote" <?php checked( $sitepress->get_setting( 'promote_wpml' ) ) ?> value="1" />
						<?php printf(__("Tell the world your site is running multilingual with WPML (places a message in your site's footer) - <a href=\"%s\">read more</a>", 'sitepress'),'https://wpml.org/?page_id=4560'); ?></label>
					</p>
					<p class="buttons-wrap">
						<span class="icl_ajx_response" id="icl_ajx_response_lv"></span>
						<input class="button button-primary" name="save" value="<?php esc_attr_e( 'Save', 'sitepress' ) ?>" type="submit" />
					</p>
				</form>
			</div>
		</div>

	<?php endif; ?>

	<?php
	do_action( 'wpml_after_settings', $theme_wpml_config_file );

	/**
	 * @deprecated use `wpml_after_settings` instead
	 */
	do_action( 'wpml_admin_after_wpml_love', $theme_wpml_config_file );

	/**
	 * @deprecated use `wpml_menu_footer` instead
	 */
	do_action( 'icl_menu_footer' );
	do_action( 'wpml_menu_footer' );
	?>

</div> <!-- .wrap -->
<?php
}
//Save any changed setting
$sitepress->save_settings();
