<div class="match-height gutter"><!-- row -->
    <fieldset>
        <label for="wpmtst-options-mail-queue">
            <input id="wpmtst-options-mail-queue"
                   type="checkbox"
                   name="wpmtst_form_options[mail_queue]" <?php checked( $form_options['mail_queue'] ); ?>>
			<?php _e( 'Use mail queue. For services like Mandrill. Off by default.', 'strong-testimonials' ); ?>
        </label>
    </fieldset>
</div>

<div class="email-option-first"><!-- row -->
    <div class="email-option-row">

        <div class="email-option-desc"><!-- left -->
			<?php _e( "From", 'strong-testimonials' ); ?>
        </div>

        <div class="email-option-inputs"><!-- middle -->

            <div class="email-option">

                <div class="email-option-part">
                    <div class="email-option-label">
                        <label for="wpmtst-options-sender-name">
							<?php _e( "Name", 'strong-testimonials' ); ?>
                        </label>
                    </div>
                </div>

                <div class="email-option-part">
                    <div class="email-option-label">
						<?php _e( "Email", 'strong-testimonials' ); ?>
                    </div>
                </div>

            </div>

            <div class="email-option recipient">

                <div class="email-option-part">
                    <div class="email-option-fieldset">
                        <div class="controls"></div>
                        <div class="fields">
                            <input type="text"
                                   id="wpmtst-options-sender-name"
                                   name="wpmtst_form_options[sender_name]"
                                   value="<?php echo esc_attr( $form_options['sender_name'] ); ?>"
                                   placeholder="<?php _e( '(optional)', 'strong-testimonials' ); ?>"
                                   size="30">
                        </div>
                    </div>
                </div>

                <div class="email-option-part">

                    <div class="email-option-fieldset">

                        <div class="controls">
                            <input type="radio"
                                   id="wpmtst-options-sender-site-email-1"
                                   name="wpmtst_form_options[sender_site_email]" <?php checked( $form_options['sender_site_email'], 1 ); ?>
                                   value="1">
                        </div>
                        <div class="fields">
							<?php _e( 'admin:', 'strong-testimonials' ); ?>
                            &nbsp;<?php echo get_bloginfo( 'admin_email' ); ?>
                        </div>

                    </div>

                    <div class="email-option-fieldset">

                        <div class="controls">
                            <input class="focus-next-field" type="radio"
                                   id="wpmtst-options-sender-site-email-0"
                                   name="wpmtst_form_options[sender_site_email]" <?php checked( $form_options['sender_site_email'], 0 ); ?>
                                   value="0">
                        </div>
                        <div class="fields">
                            <input type="email"
                                   id="wpmtst-options-sender-email"
                                   name="wpmtst_form_options[sender_email]"
                                   value="<?php echo esc_attr( $form_options['sender_email'] ); ?>"
                                   placeholder="<?php _e( 'email address', 'strong-testimonials' ); ?>"
                                   size="30">
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
