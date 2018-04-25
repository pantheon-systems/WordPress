<div class="email-option recipient">

    <div class="email-option-part">
        <div class="email-option-fieldset">
            <div class="controls">
				<?php if ( ! isset( $recipient['primary'] ) ) : ?>
                    <div class="delete-recipient dashicons dashicons-no"></div>
				<?php endif; ?>
            </div>
            <div class="fields">
                <input class="admin_name"
                       type="text"
                       name="wpmtst_form_options[recipients][<?php echo $key; ?>][admin_name]"
                       value="<?php echo esc_attr( $recipient['admin_name'] ); ?>"
                       placeholder="<?php _e( '(optional)', 'strong-testimonials' ); ?>"
                       size="30">
            </div>
        </div>
    </div>

    <div class="email-option-part">
		<?php if ( isset( $recipient['primary'] ) ) : ?>

            <div class="email-option-fieldset primary-1">
                <div class="controls">
                    <input type="hidden" name="wpmtst_form_options[recipients][<?php echo $key; ?>][primary]" value="1">
                    <input type="radio" id="wpmtst-options-admin-site-email-1"
                           name="wpmtst_form_options[recipients][<?php echo $key; ?>][admin_site_email]" <?php checked( $recipient['admin_site_email'], 1 ); ?>
                           value="1">
                </div>
                <div class="fields">
                    <label for="wpmtst-options-admin-site-email-1">
						<?php _e( 'admin:', 'strong-testimonials' ); ?>
                        &nbsp;<?php echo get_bloginfo( 'admin_email' ); ?>
                    </label>
                </div>
            </div>

            <div class="email-option-fieldset primary-2">
                <div class="controls">
                    <input class="focus-next-field" type="radio"
                           id="wpmtst-options-admin-site-email-0"
                           name="wpmtst_form_options[recipients][<?php echo $key; ?>][admin_site_email]" <?php checked( $recipient['admin_site_email'], 0 ); ?>
                           value="0">
                </div>
                <div class="fields">
                    <input type="email" id="wpmtst-options-admin-email"
                           name="wpmtst_form_options[recipients][<?php echo $key; ?>][admin_email]"
                           value="<?php echo esc_attr( $recipient['admin_email'] ); ?>"
                           placeholder="<?php _e( 'email address', 'strong-testimonials' ); ?>"
                           size="30">
                </div>
            </div>

		<?php else : ?>

            <div class="email-option-fieldset secondary">
                <div class="controls"></div>
                <div class="fields">
                    <input type="email"
                           name="wpmtst_form_options[recipients][<?php echo $key; ?>][admin_email]"
                           value="<?php echo esc_attr( $recipient['admin_email'] ); ?>"
                           placeholder="<?php _e( 'email address', 'strong-testimonials' ); ?>"
                           size="30">
                </div>
            </div>

		<?php endif; ?>
    </div>

</div>
