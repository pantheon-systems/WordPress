<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Setup_Compatibility {
	protected function get_content() {
		?>
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
				do_action( 'otgs_installer_render_local_components_setting',
				           array(
					           'plugin_name'        => 'WPML',
					           'plugin_uri'         => 'http://wpml.org',
					           'plugin_site'        => 'wpml.org',
					           'use_styles'         => true,
					           'use_radio'          => true,
					           'privacy_policy_url' => 'https://wpml.org/documentation/privacy-policy-and-gdpr-compliance/?utm_source=wpmlplugin&utm_campaign=compatibility-reporting&utm_medium=wpml-setup&utm_term=privacy-policy-and-gdpr-compliance',
				           ) );
		  ?>

			</div>

			<footer class="clearfix text-right">
				<input id="icl_setup_back_3" class="button-secondary alignleft" name="save" value="<?php esc_attr_e( 'Back', 'sitepress' ) ?>" type="button" />
		  <?php wp_nonce_field('setup_got_to_step3_nonce', '_icl_nonce_gts3'); ?>
				<input id="icl_setup_next_5" class="button-primary alignright" name="save"
				       value="<?php esc_attr_e( 'Next', 'sitepress' ) ?>" type="button"
				/>
		  <?php wp_nonce_field('setup_got_to_step5_nonce', '_icl_nonce_gts5'); ?>
			</footer>
		</div>
		<?php
	}
}
