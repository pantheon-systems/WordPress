<div>
    <div class="email-option-row">

        <div class="email-option-desc">
			<?php _e( "To", 'strong-testimonials' ); ?>
        </div>

        <div class="email-option-inputs">

            <div class="email-option header">

                <div class="email-option-part">
                    <div class="email-option-label">
                        <?php _e( 'Name', 'strong-testimonials' ); ?>
                    </div>
                </div>

                <div class="email-option-part">
                    <div class="email-option-label">
						<?php _e( 'Email', 'strong-testimonials' ); ?>
                    </div>
                </div>

            </div>

			<?php
			if ( isset( $form_options['recipients'] ) && $form_options['recipients'] ) {
				foreach ( $form_options['recipients'] as $key => $recipient ) {
					include 'recipient.php';
				}
			}
			?>

            <div class="email-option footer">
                <input class="button" type="button" id="add-recipient"
                       value="<?php esc_attr_e( 'Add recipient', 'strong-testimonials' ); ?>">
            </div>

        </div>

    </div>

</div>