<div><!-- row -->
    <div class="email-option-row">

        <div class="email-option-desc"><!-- left box -->
			<?php _e( "To", 'strong-testimonials' ); ?>
        </div>

        <div class="email-option-inputs"><!-- middle box -->

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

			<?php
			if ( isset( $form_options['recipients'] ) && $form_options['recipients'] ) {
				foreach ( $form_options['recipients'] as $key => $recipient ) {
					include 'recipient.php';
				}
			}
			?>

            <div class="email-option">
                <input class="button" type="button" id="add-recipient" value="Add recipient">
            </div>

        </div>

    </div>

</div>