<?php
// @use WPML_Media::menu_content
$orphan_attachments_sql = "
		SELECT COUNT(*)
		FROM {$this->wpdb->posts}
		WHERE post_type = 'attachment'
			AND ID NOT IN (
				SELECT element_id
				FROM {$this->wpdb->prefix}icl_translations
				WHERE element_type='post_attachment'
			)
		";

$orphan_attachments = $this->wpdb->get_var( $orphan_attachments_sql );

?>
<div class="wpml-section" id="<?php echo esc_attr( WPML_Media_Settings::ID ); ?>">

	<div class="wpml-section-header">
		<h3><?php esc_html_e('Media Translation', 'sitepress'); ?></h3>
	</div>

	<div class="wpml-section-content">
		<?php if ( $orphan_attachments ): ?>

			<p><?php esc_html_e("The Media Translation plugin needs to add languages to your site's media. Without this language information, existing media files will not be displayed in the WordPress admin.", 'sitepress') ?></p>

		<?php else: ?>

			<p><?php esc_html_e('You can check if some attachments can be duplicated to translated content:', 'sitepress') ?></p>

		<?php endif ?>

		<form id="wpml_media_options_form">
			<input type="hidden" name="no_lang_attachments" value="<?php echo $orphan_attachments ?>"/>
			<input type="hidden" id="wpml_media_options_action"/>
			<table class="wpml-media-existing-content">

				<tr>
					<td colspan="2">
						<ul class="wpml_media_options_language">
							<li><label><input type="checkbox" id="set_language_info" name="set_language_info" value="1" <?php if (!empty($orphan_attachments)): ?>checked="checked"<?php endif; ?>
							                  <?php if (empty($orphan_attachments)): ?>disabled="disabled"<?php endif ?> />&nbsp;<?php esc_html_e('Set language information for existing media', 'sitepress') ?></label></li>
							<li><label><input type="checkbox" id="translate_media" name="translate_media" value="1" checked="checked"/>&nbsp;<?php esc_html_e('Translate existing media in all languages', 'sitepress') ?></label></li>
							<li><label><input type="checkbox" id="duplicate_media" name="duplicate_media" value="1" checked="checked"/>&nbsp;<?php esc_html_e('Duplicate existing media for translated content', 'sitepress') ?></label></li>
							<li><label><input type="checkbox" id="duplicate_featured" name="duplicate_featured" value="1" checked="checked"/>&nbsp;<?php esc_html_e('Duplicate the featured images for translated content', 'sitepress') ?></label></li>
						</ul>
					</td>
				</tr>

				<tr>
					<td><a href="https://wpml.org/documentation/getting-started-guide/media-translation/" target="_blank"><?php esc_html_e('Media Translation Documentation', 'sitepress') ?></a></td>
					<td align="right">
						<input class="button-primary" name="start" type="submit" value="<?php esc_attr_e('Start', 'sitepress'); ?> &raquo;"/>
					</td>

				</tr>

				<tr>
					<td colspan="2">
						<img class="progress" src="<?php echo ICL_PLUGIN_URL ?>/res/img/ajax-loader.gif" width="16" height="16" alt="loading" style="display: none;"/>
						&nbsp;<span class="status"> </span>
					</td>
				</tr>
            </table>


            <table class="wpml-media-new-content-settings">

				<tr>
					<td colspan="2">
						<h4><?php esc_html_e('How to handle media for new content:', 'sitepress'); ?></h4>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<ul class="wpml_media_options_language">
							<?php
							$settings = get_option( '_wpml_media' );
							$content_defaults = $settings[ 'new_content_settings' ];

							$always_translate_media_html_checked = $content_defaults['always_translate_media'] ? 'checked="checked"' : '';
							$duplicate_media_html_checked = $content_defaults['duplicate_media'] ? 'checked="checked"' : '';
							$duplicate_featured_html_checked = $content_defaults['duplicate_featured'] ? 'checked="checked"' : '';
							?>
							<li>
								<label><input type="checkbox" name="content_default_always_translate_media"
								              value="1" <?php echo $always_translate_media_html_checked; ?> />&nbsp;<?php esc_html_e('When uploading media to the Media library, make it available in all languages', 'sitepress') ?></label>
							</li>
							<li>
								<label><input type="checkbox" name="content_default_duplicate_media"
								              value="1" <?php echo $duplicate_media_html_checked; ?> />&nbsp;<?php esc_html_e('Duplicate media attachments for translations', 'sitepress') ?></label>
							</li>
							<li>
								<label><input type="checkbox" name="content_default_duplicate_featured"
								              value="1"  <?php echo $duplicate_featured_html_checked; ?> />&nbsp;<?php esc_html_e('Duplicate featured images for translations', 'sitepress') ?></label>
							</li>
						</ul>
					</td>
				</tr>

				<tr>
					<td colspan="2" align="right">
						<input class="button-secondary" name="set_defaults" type="submit" value="<?php esc_attr_e('Apply', 'sitepress'); ?>"/>
					</td>
				</tr>

				<tr>
					<td colspan="2">
						<img class="content_default_progress" src="<?php echo ICL_PLUGIN_URL ?>/res/img/ajax-loader.gif" width="16" height="16" alt="loading" style="display: none;"/>
						&nbsp;<span class="content_default_status"> </span>
					</td>
				</tr>

			</table>

			<div id="wpml_media_all_done" class="hidden updated">
				<p><?php esc_html_e("You're all done. From now on, all new media files that you upload to content will receive a language. You can automatically duplicate them to translations from the post-edit screen.", 'sitepress') ?></p>
			</div>

		</form>
	</div>

</div>
