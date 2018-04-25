<div class="email-option-row">

    <div class="email-option-desc">
        <label for="wpmtst-option-email-subject">
			<?php _e( "Subject", 'strong-testimonials' ); ?>
        </label>
    </div>

    <div class="email-option-inputs">
        <div class="email-option body">
            <input class="wide"
                   type="text"
                   id="wpmtst-option-email-subject"
                   name="wpmtst_form_options[email_subject]"
                   value="<?php echo esc_attr( $form_options['email_subject'] ); ?>"
                   placeholder="<?php _e( 'subject line', 'strong-testimonials' ); ?>"
                   size="50">
        </div>
    </div>

</div>

<div class="email-option-row">

    <div class="email-option-desc">
        <label for="wpmtst-option-email-message">
			<?php _e( "Message", 'strong-testimonials' ); ?>
        </label>
    </div>

	<?php $line_count = max( count( explode( "\n", $form_options['email_message'] ) ), 3 ); ?>
    <div class="email-option-inputs">
        <div class="email-option body">
            <textarea class="autosize"
                      id="wpmtst-option-email-message" name="wpmtst_form_options[email_message]"
                      rows="<?php echo esc_attr( $line_count ); ?>"
                      placeholder="<?php _e( 'message text', 'strong-testimonials' ); ?>"><?php echo esc_attr( $form_options['email_message'] ); ?></textarea>
        </div>
    </div>

</div>

<div class="email-option-row column no-padding">

    <div class="template-tags-help">
        <div class="title"><?php _e( "Tags for Subject and Message", 'strong-testimonials' ); ?></div>
        <div class="content">
            <ul id="wpmtst-tags-list">
                <li>%BLOGNAME%</li>
                <li>%TITLE%</li>
                <li>%CONTENT%</li>
                <li>%STATUS%</li>
                <li>%SUBMIT_DATE%</li>
                <?php
				$custom_fields = wpmtst_get_custom_fields();
				foreach ( $custom_fields as $field ) {
					echo '<li>%' . strtoupper( $field['name'] ) . '%</li>' . "\n";
				}
				?>
            </ul>
        </div>
    </div>

</div>
