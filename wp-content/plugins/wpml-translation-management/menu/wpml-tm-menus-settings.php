<?php

class WPML_TM_Menus_Settings extends WPML_TM_Menus {

	/** @var WPML_Translate_Link_Targets_UI $translate_link_targets_ui */
	private $translate_link_targets_ui;

	/** @var bool $end_user_feature_enabled */
	private $end_user_feature_enabled;

	private $mcsetup_sections = array();

	public function init() {
		$this->init_navigation_links();
	}

	private function init_navigation_links() {
		/**
		 * @var SitePress             $sitepress
		 * @var TranslationManagement $iclTranslationManagement
		 */
		global $sitepress, $iclTranslationManagement;
		$is_admin = current_user_can( 'manage_options' );

		if ( $this->is_end_user_feature_enabled() && $is_admin ) {
			$this->mcsetup_sections['ml-content-setup-sec-0'] = esc_html__( 'General settings', 'wpml-translation-management' );
		}

		$this->mcsetup_sections['ml-content-setup-sec-1'] = esc_html__( 'How to translate posts and pages', 'wpml-translation-management' );

		if ( $is_admin ) {
			$this->mcsetup_sections['ml-content-setup-sec-2'] = esc_html__( 'Posts and pages synchronization', 'wpml-translation-management' );
			$this->mcsetup_sections['ml-content-setup-sec-3'] = esc_html__( 'Translated documents options', 'wpml-translation-management' );

			if ( defined( 'WPML_ST_VERSION' ) ) {
				$this->mcsetup_sections['ml-content-setup-sec-4'] = esc_html__( 'Custom posts slug translation options', 'wpml-translation-management' );
			}

			$this->mcsetup_sections['ml-content-setup-sec-5']   = esc_html__( 'Translation pickup mode', 'wpml-translation-management' );
		}

		$this->mcsetup_sections['ml-content-setup-sec-5-1'] = esc_html__( 'XLIFF file options', 'wpml-translation-management' );

		if ( $is_admin ) {
			$this->mcsetup_sections['ml-content-setup-sec-cf']  = esc_html__( 'Custom Fields Translation', 'wpml-translation-management' );
			$this->mcsetup_sections['ml-content-setup-sec-tcf'] = esc_html__( 'Custom Term Meta Translation', 'wpml-translation-management' );

			$custom_posts = array();
			$this->post_types = $sitepress->get_translatable_documents(true);

			foreach ($this->post_types as $k => $v) {
				$custom_posts[$k] = $v;
			}

			global $wp_taxonomies;
			$custom_taxonomies = array_diff(array_keys((array)$wp_taxonomies), array('post_tag', 'category', 'nav_menu', 'link_category', 'post_format'));

			if ( $custom_posts ) {
				$this->mcsetup_sections['ml-content-setup-sec-7'] = esc_html__( 'Post Types Translation', 'wpml-translation-management' );
			}

			if ( $custom_taxonomies ) {
				$this->mcsetup_sections['ml-content-setup-sec-8'] = esc_html__( 'Taxonomies Translation', 'wpml-translation-management' );
			}

			if ( ! empty( $iclTranslationManagement->admin_texts_to_translate ) && function_exists( 'icl_register_string' ) ) {
				$this->mcsetup_sections['ml-content-setup-sec-9'] = esc_html__( 'Admin Strings to Translate', 'wpml-translation-management' );
			}
		}

		$this->get_translate_link_targets_ui()->add_hooks();

		$this->mcsetup_sections = apply_filters( 'wpml_mcsetup_navigation_links', $this->mcsetup_sections, $sitepress, $iclTranslationManagement );
	}

	protected function render_main() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Settings', 'wpml-translation-management' ) ?></h1>

			<?php
			do_action( 'icl_tm_messages' );
			$this->build_tab_items();
			$this->render_items();
			?>
		</div>
		<?php
	}

	protected function build_tab_items() {
		$this->build_mcs_item();
		$this->build_translation_notifications_item();

		$this->tab_items = apply_filters( 'wpml_tm_tab_items', $this->tab_items );
	}

	private function build_mcs_item() {
		global $sitepress;

		$this->tab_items['mcsetup']['caption'] = esc_html__('Multilingual Content Setup', 'wpml-translation-management');
		$translate_link_targets = new WPML_Translate_Link_Target_Global_State( $sitepress );
		if ( $translate_link_targets->is_rescan_required() ) {
			$this->tab_items['mcsetup']['caption'] = '<i class="otgs-ico-warning"></i>' . esc_html( $this->tab_items['mcsetup']['caption'] );
		}
		$this->tab_items['mcsetup']['callback'] = array($this, 'build_content_mcs');
		$this->tab_items['mcsetup']['current_user_can'] = array( 'manage_options',  WPML_Manage_Translations_Role::CAPABILITY );
	}

	private function build_translation_notifications_item() {
		$this->tab_items['notifications'] = array(
			'caption'          => esc_html__( 'Translation Notifications', 'wpml-translation-management' ),
			'current_user_can' => WPML_Manage_Translations_Role::CAPABILITY,
			'callback'         => array( $this, 'build_content_translation_notifications' ),
		);
	}

	public function build_content_mcs() {
		/**
		 * included by menu translation-management.php
		 *
		 * @var TranslationManagement $iclTranslationManagement
		 */
		global $sitepress, $sitepress_settings, $iclTranslationManagement;

		$translate_link_targets = new WPML_Translate_Link_Target_Global_State( $sitepress );
		if ( $translate_link_targets->is_rescan_required() ) {
			?>
			<div class="update-nag">
				<p>
					<i class="otgs-ico-warning"></i> <?php echo esc_html__( 'There is new translated content on this site. You can scan posts and strings to adjust links to point to translated content.',
					                                                        'wpml-translation-management' ); ?></p>
				<p><?php echo $this->get_navigation_link( $this->get_translate_link_targets_ui()->get_id() ); ?></p>
			</div>
			<?php
		}

		$this->render_mcsetup_navigation_links();
		?>

		<div class="wpml-section wpml-section-notice">
			<div class="updated below-h2">
				<p>
					<?php echo esc_html__( "WPML can read a configuration file that tells it what needs translation in themes and plugins. The file is named wpml-config.xml and it's placed in the root folder of the plugin or theme.",
					                       'wpml-translation-management' ); ?>
				</p>

				<p>
					<a href="https://wpml.org/?page_id=5526"><?php echo esc_html__( 'Learn more',
					                                                                'wpml-translation-management' ) ?></a>
				</p>
			</div>
		</div>

		<?php if ( $this->should_show_mcsetup_section( 'ml-content-setup-sec-0' ) ) : ?>
			<div class="wpml-section" id="ml-content-setup-sec-0">

				<div class="wpml-section-header">
					<h3><?php echo esc_html__( 'General settings', 'wpml-translation-management' ); ?></h3>
				</div>

				<div class="wpml-section-content">
					<form id="wpml-tm-general-settings" action="">
						<ul>
							<li>
								<label>
									<?php
									$disabling_option = new WPML_End_User_Account_Creation_Disabled_Option();
									$is_disabled      = $disabling_option->is_disabled();
									?>
									<input type="checkbox" name="wpml-disabling" value="1"
										<?php checked( true, $is_disabled ) ?>
										   data-nonce="<?php echo wp_create_nonce( WPML_End_User_Account_Creation_Disabled::NONCE ) ?>"
									/>
									<?php echo esc_html__( 'Disable the possibility of creating accounts for users on wpml.org',
									                       'wpml-translation-management' ) ?>
									<span class="spinner" style="float:none;"> </span>
								</label>
							</li>
						</ul>

					</form>
				</div>
			</div><!-- #ml-content-setup-sec-0 -->
		<?php endif; ?>

		<?php if ( $this->should_show_mcsetup_section( 'ml-content-setup-sec-1' ) ) : ?>
			<div class="wpml-section" id="ml-content-setup-sec-1">
				<?php
				$doc_translation_method = ICL_TM_TMETHOD_MANUAL;
				if ( isset( $iclTranslationManagement->settings['doc_translation_method'] ) ) {
					$doc_translation_method = $iclTranslationManagement->settings['doc_translation_method'];
				}
				$isClassicEditor = (string) ICL_TM_TMETHOD_EDITOR === (string) $doc_translation_method;
				$isATEEditor     = (string) ICL_TM_TMETHOD_ATE === (string) $doc_translation_method;
				$isManualEditor  = ( (string) ICL_TM_TMETHOD_MANUAL === (string) $doc_translation_method )
				                   || ( ! $isClassicEditor && ! $isATEEditor );
				?>

				<div class="wpml-section-header">
					<h3><?php echo esc_html__( 'How to translate posts and pages',
					                           'wpml-translation-management' ); ?></h3>
				</div>

				<div class="wpml-section-content">

					<form id="icl_doc_translation_method" name="icl_doc_translation_method" action="">
						<?php wp_nonce_field( 'icl_doc_translation_method_nonce', '_icl_nonce' ); ?>
						<div class="wpml-section-content-inner">
							<h4>
								<?php echo esc_html__( 'Translation editor options', 'wpml-translation-management' ) ?>
							</h4>

							<ul class="t_method">
								<li>
									<label>
										<input type="radio" name="t_method" value="<?php echo ICL_TM_TMETHOD_EDITOR ?>"
										       <?php if ( $isClassicEditor ): ?>checked="checked"<?php endif; ?> />
										<?php echo esc_html__( 'Use the Classic Translation Editor',
										                       'wpml-translation-management' ) ?>
									</label>
								</li>
								<li>
									<label>
										<input type="radio" name="t_method" value="<?php echo ICL_TM_TMETHOD_ATE ?>"
										       <?php if ( $isATEEditor ): ?>checked="checked"<?php endif; ?> />
										<?php echo esc_html__( 'Use the Advanced Translation Editor',
										                       'wpml-translation-management' ) ?>
									</label>
									<?php do_action( 'wpml_tm_mcs_' . ICL_TM_TMETHOD_ATE ); ?>
								</li>
								<li>
									<label>
										<input type="radio" name="t_method" value="<?php echo ICL_TM_TMETHOD_MANUAL ?>"
										       <?php if ( $isManualEditor ): ?>checked="checked"<?php endif; ?> />
										<?php echo esc_html__( 'Create translations manually',
										                       'wpml-translation-management' ) ?>
									</label>
								</li>
							</ul>
						</div>
						<?php do_action( 'wpml_doc_translation_method_below' ); ?>
						<div class="wpml-section-content-inner">
							<h4>
								<?php echo esc_html__( 'Taxonomies', 'wpml-translation-management' ) ?>
							</h4>

							<p id="tm_block_retranslating_terms"><label>
									<input name="tm_block_retranslating_terms"
										   value="1" <?php checked( icl_get_setting( 'tm_block_retranslating_terms' ),
									                                "1" ) ?>
										   type="checkbox" />
									<?php echo esc_html__( "Don't include already translated terms in the translation editor",
									                       'wpml-translation-management' ) ?>
								</label>
							</p>
						</div>

						<div class="wpml-section-content-inner">
							<h4>
								<?php echo esc_html__( 'Translators help', 'wpml-translation-management' ) ?>
							</h4>
							<p>
								<label>
									<input name="how_to_translate"
										   value="1" <?php checked( icl_get_setting( 'hide_how_to_translate' ),
									                                false ) ?>
										   type="checkbox" />
									<?php echo esc_html__( 'Show translation instructions in the list of pages',
									                       'wpml-translation-management' ) ?>
								</label>
							</p>
						</div>
						
						<?php do_action( 'wpml_how_to_translate_posts_and_pages' ); ?>

						<?php do_action( 'wpml_how_to_translate_posts_and_pages_below' ); ?>

						<p>
							<a href="https://wpml.org/?page_id=3416"
							   target="_blank"><?php echo esc_html__( 'Learn more about the different translation options',
							                                          'wpml-translation-management' ) ?></a>
						</p>

						<p class="buttons-wrap">
							<span class="icl_ajx_response" id="icl_ajx_response_dtm"> </span>
							<input type="submit" class="button-primary"
								   value="<?php echo esc_html__( 'Save', 'wpml-translation-management' ) ?>" />
						</p>

					</form>
				</div>
				<!-- .wpml-section-content -->

			</div><!-- #ml-content-setup-sec-1 -->
		<?php endif; ?>

		<?php if ( $this->should_show_mcsetup_section( 'ml-content-setup-sec-2' ) ) : ?>
			<?php include ICL_PLUGIN_PATH . '/menu/_posts_sync_options.php'; ?>
		<?php endif; ?><!-- #ml-content-setup-sec-2 -->

		<?php if ( $this->should_show_mcsetup_section( 'ml-content-setup-sec-3' ) ) : ?>
			<div class="wpml-section" id="ml-content-setup-sec-3">

				<div class="wpml-section-header">
					<h3><?php echo esc_html__( 'Translated documents options', 'wpml-translation-management' ) ?></h3>
				</div>

				<div class="wpml-section-content">

					<form name="icl_tdo_options" id="icl_tdo_options" action="">
						<?php wp_nonce_field( 'wpml-translated-document-options-nonce',
						                      WPML_TM_Options_Ajax::NONCE_TRANSLATED_DOCUMENT ); ?>

						<div class="wpml-section-content-inner">
							<h4>
								<?php echo esc_html__( 'Document status', 'wpml-translation-management' ) ?>
							</h4>
							<ul>
								<li>
									<label>
										<input type="radio" name="icl_translated_document_status" value="0"
											<?php checked( (bool) icl_get_setting( 'translated_document_status' ),
											               false ); ?> />
										<?php echo esc_html__( 'Draft', 'wpml-translation-management' ) ?>
									</label>
								</li>
								<li>
									<label>
										<input type="radio" name="icl_translated_document_status" value="1"
											<?php checked( (bool) icl_get_setting( 'translated_document_status' ),
											               true ); ?> />
										<?php echo esc_html__( 'Same as the original document',
										                       'wpml-translation-management' ) ?>
									</label>
								</li>
							</ul>
							<p class="explanation-text">
								<?php echo esc_html__( 'Choose if translations should be published when received. Note: If Publish is selected, the translation will only be published if the original document is published when the translation is received.',
								                       'wpml-translation-management' ) ?>
							</p>
						</div>

						<div class="wpml-section-content-inner">
							<h4>
								<?php echo esc_html__( 'Page URL', 'wpml-translation-management' ) ?>
							</h4>
							<ul>
								<li>
									<label><input type="radio" name="icl_translated_document_page_url" value="auto-generate"
									              <?php if ( empty( $sitepress_settings['translated_document_page_url'] )
									                         || $sitepress_settings['translated_document_page_url']
									                            === 'auto-generate' ): ?>checked="checked"<?php endif; ?> />
										<?php echo esc_html__( 'Auto-generate from title (default)',
										                       'wpml-translation-management' ) ?>
									</label>
								</li>
								<li>
									<label><input type="radio" name="icl_translated_document_page_url" value="translate"
									              <?php if ( $sitepress_settings['translated_document_page_url']
									                         === 'translate' ): ?>checked="checked"<?php endif; ?> />
										<?php echo esc_html__( 'Translate (this will include the slug in the translation and not create it automatically from the title)',
										                       'wpml-translation-management' ) ?>
									</label>
								</li>
								<li>
									<label><input type="radio" name="icl_translated_document_page_url" value="copy-encoded"
									              <?php if ( $sitepress_settings['translated_document_page_url']
									                         === 'copy-encoded' ): ?>checked="checked"<?php endif; ?> />
										<?php echo esc_html__( 'Copy from original language if translation language uses encoded URLs',
										                       'wpml-translation-management' ) ?>
									</label>
								</li>
							</ul>
						</div>

						<div class="wpml-section-content-inner">
							<p class="buttons-wrap">
								<span class="icl_ajx_response" id="icl_ajx_response_tdo"> </span>
								<input id="js-translated_document-options-btn" type="button" class="button-primary" value="<?php echo esc_attr__( 'Save',
								                                                                                                                  'wpml-translation-management' ) ?>" />
							</p>
						</div>

					</form>
				</div>
				<!-- .wpml-section-content -->
			</div><!-- #ml-content-setup-sec-3 -->
		<?php endif; ?>

		<?php if ( $this->should_show_mcsetup_section( 'ml-content-setup-sec-4' ) ) : ?>
			<?php include WPML_ST_PATH . '/menu/_slug-translation-options.php'; ?><!-- #ml-content-setup-sec-4 -->
		<?php endif; ?>

		<?php if ( $this->should_show_mcsetup_section( 'ml-content-setup-sec-5' ) ) : ?>
			<div class="wpml-section" id="ml-content-setup-sec-5">

				<div class="wpml-section-header">
					<h3><?php echo esc_html__( 'Translation pickup mode', 'wpml-translation-management' ); ?></h3>
				</div>

				<div class="wpml-section-content">

					<form id="icl_translation_pickup_mode" name="icl_translation_pickup_mode" action="">
						<?php wp_nonce_field( 'wpml_save_translation_pickup_mode',
						                      WPML_TM_Pickup_Mode_Ajax::NONCE_PICKUP_MODE ) ?>

						<p>
							<?php echo esc_html__( 'How should the site receive completed translations from Translation Service?',
							                       'wpml-translation-management' ); ?>
						</p>

						<p>
							<label>
								<input type="radio" name="icl_translation_pickup_method"
									   value="<?php echo ICL_PRO_TRANSLATION_PICKUP_XMLRPC ?>"
								       <?php if ( $sitepress_settings['translation_pickup_method']
								                  === ICL_PRO_TRANSLATION_PICKUP_XMLRPC ): ?>checked="checked"<?php endif ?>/>
								<?php echo esc_html__( 'Translation Service will deliver translations automatically using XML-RPC',
								                       'wpml-translation-management' ); ?>
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="icl_translation_pickup_method"
									   value="<?php echo ICL_PRO_TRANSLATION_PICKUP_POLLING ?>"
								       <?php if ( $sitepress_settings['translation_pickup_method']
								                  === ICL_PRO_TRANSLATION_PICKUP_POLLING ): ?>checked="checked"<?php endif; ?> />
								<?php echo esc_html__( 'The site will fetch translations manually',
								                       'wpml-translation-management' ); ?>
							</label>
						</p>


						<p class="buttons-wrap">
							<span class="icl_ajx_response" id="icl_ajx_response_tpm"> </span>
							<input id="translation-pickup-mode" class="button-primary" name="save"
								   value="<?php echo esc_attr__( 'Save',
							                                     'wpml-translation-management' ) ?>" type="button" />
						</p>

						<?php
						$this->build_content_dashboard_fetch_translations_box();
						?>
					</form>

					<?php do_action( 'wpml_tm_mcs_translation_pickup_mode' ); ?>

				</div>
				<!-- .wpml-section-content -->
			</div><!-- #ml-content-setup-sec-5 -->
		<?php endif; ?>

		<?php if ( $this->should_show_mcsetup_section( 'ml-content-setup-sec-5-1' ) ) : ?>
			<?php include WPML_TM_PATH . '/menu/xliff-options.php'; ?><!-- #ml-content-setup-sec-5-1 -->
		<?php endif; ?>

		<?php $this->build_content_mcs_custom_fields(); ?>

		<?php if ( $this->should_show_mcsetup_section( 'ml-content-setup-sec-7' ) ) : ?>
			<?php include ICL_PLUGIN_PATH . '/menu/_custom_types_translation.php'; ?><!-- #ml-content-setup-sec-7 -->
		<?php endif; ?>

		<?php if ( $this->should_show_mcsetup_section( 'ml-content-setup-sec-9' ) ) : ?>
			<div class="wpml-section" id="ml-content-setup-sec-9">

				<div class="wpml-section-header">
					<h3><?php echo esc_html__( 'Admin Strings to Translate', 'wpml-translation-management' ); ?></h3>
				</div>

				<div class="wpml-section-content">
					<table class="widefat">
						<thead>
						<tr>
							<th colspan="3">
								<?php echo esc_html__( 'Admin Strings', 'wpml-translation-management' ); ?>
							</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td>
								<?php
								foreach (
									$iclTranslationManagement->admin_texts_to_translate as $option_name =>
									$option_value
								) {
									$iclTranslationManagement->render_option_writes( $option_name, $option_value );
								}
								?>
								<br />

								<p><a class="button-secondary"
									  href="<?php echo admin_url( 'admin.php?page='
								                                  . WPML_ST_FOLDER
								                                  . '/menu/string-translation.php' ) ?>"><?php echo esc_html__( 'Edit translatable strings',
								                                                                                                'wpml-translation-management' ) ?></a>
								</p>
							</td>
						</tr>
						</tbody>
					</table>

				</div>
				<!-- .wpml-section-content -->

			</div><!-- #ml-content-setup-sec-9 -->
		<?php endif; ?>

		<?php if ( $this->should_show_mcsetup_section( $this->get_translate_link_targets_ui()->get_id() ) ) : ?>
			<?php echo $this->get_translate_link_targets_ui()->render(); ?><!-- #ml-content-setup-sec-links-target -->
		<?php endif; ?>

		<?php
		wp_enqueue_script( 'wpml-tm-mcs' );
		wp_enqueue_script( 'wpml-tm-mcs-translate-link-targets' );
	}

	private function build_content_mcs_custom_fields() {
		global $iclTranslationManagement, $wpdb;

		$settings_factory = new WPML_Custom_Field_Setting_Factory( $iclTranslationManagement );
		$settings_factory->show_system_fields = array_key_exists( 'show_system_fields', $_GET ) ? (bool) $_GET['show_system_fields'] : false;
		$unlock_button_ui = new WPML_UI_Unlock_Button();

		if ( $this->should_show_mcsetup_section( 'ml-content-setup-sec-cf' ) ) {
			$menu_item = new WPML_TM_MCS_Post_Custom_Field_Settings_Menu( $settings_factory, $unlock_button_ui );
			echo $menu_item->render();
		}

		if ( ! empty( $wpdb->termmeta ) && $this->should_show_mcsetup_section( 'ml-content-setup-sec-tcf' ) ) {
			$menu_item_terms = new WPML_TM_MCS_Term_Custom_Field_Settings_Menu( $settings_factory, $unlock_button_ui );
			echo $menu_item_terms->render();
		}
	}

	public function build_content_translation_notifications() {
		?>
		<form method="post" name="translation-notifications" id="translation-notifications"
			  action="admin.php?page=<?php echo WPML_TM_FOLDER . $this->get_page_slug() ?>&amp;sm=notifications">
			<input type="hidden" name="icl_tm_action" value="save_notification_settings"/>

			<?php do_action( 'wpml_tm_translation_notification_setting_after' ); ?>

			<div class="wpml-section" id="translation-notifications-sec-3">
				<p class="submit">
					<input type="submit" class="button-primary"
						   value="<?php echo esc_html__('Save', 'wpml-translation-management') ?>"/>
				</p>
			</div>

			<?php wp_nonce_field( 'save_notification_settings_nonce', 'save_notification_settings_nonce' ); ?>
		</form>

		<?php
	}

	protected function get_page_slug() {
		return WPML_Translation_Management::PAGE_SLUG_SETTINGS;
	}

	protected function get_default_tab() {
		return 'mcsetup';
	}

	private function render_mcsetup_navigation_links() {
		echo '<ul class="wpml-navigation-links js-wpml-navigation-links">';

		foreach ( $this->mcsetup_sections as $anchor => $title ) {
			echo '<li>' . $this->get_navigation_link( $anchor ) . '</li>';
		}

		echo '</ul>';
	}

	private function get_navigation_link( $anchor ) {
		if ( array_key_exists( $anchor, $this->mcsetup_sections ) ) {
			return '<a href="#' . $anchor . '">' . $this->mcsetup_sections[ $anchor ] . '</a>';
		}
	}

	/** @return bool */
	private function should_show_mcsetup_section( $anchor ) {
		return array_key_exists( $anchor, $this->mcsetup_sections );
	}

	/** @return WPML_Translate_Link_Targets_UI */
	private function get_translate_link_targets_ui() {
		/**
		 * @var SitePress $sitepress
		 * @var wpdb $wpdb
		 * @var $ICL_Pro_Translation
		 */
		global $sitepress, $wpdb, $ICL_Pro_Translation;

		if ( ! $this->translate_link_targets_ui ) {
			$this->translate_link_targets_ui = new WPML_Translate_Link_Targets_UI(
				__( 'Translate Link Targets', 'wpml-translation-management' ),
				$wpdb,
				$sitepress,
				$ICL_Pro_Translation
			);
		}

		return $this->translate_link_targets_ui;
	}

	private function is_end_user_feature_enabled() {
		if ( null === $this->end_user_feature_enabled ) {
			$end_user_factory = new WPML_End_User_Loader_Factory();
			$this->end_user_feature_enabled =  $end_user_factory->is_end_user_feature_enabled();
		}

		return $this->end_user_feature_enabled;
	}
}
