<div class="email-option-row">

    <div class="email-option-desc">
        <label for="wpmtst-option-email-subject">
			<?php _e( "Subject", 'strong-testimonials' ); ?>
        </label>
    </div>

    <div class="email-option-inputs">
        <div class="email-option">
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

    <div class="email-option-inputs">
        <div class="email-option">
            <textarea id="wpmtst-option-email-message"
                      name="wpmtst_form_options[email_message]"
                      placeholder="<?php _e( 'message text', 'strong-testimonials' ); ?>"
                      rows="6"><?php echo esc_attr( $form_options['email_message'] ); ?></textarea>
        </div>
    </div>

</div>

<div class="email-option-row column">

    <div class="template-tags-help">
        <div class="title"><?php _e( "Tags for Subject and Message", 'strong-testimonials' ); ?></div>
        <div class="content">
            <ul><li>%BLOGNAME%</li><li>%TITLE%</li><li>%CONTENT%</li><li>%STATUS%</li><?php
				$custom_fields = wpmtst_get_custom_fields();
				foreach ( $custom_fields as $field ) {
					echo '<li>%' . strtoupper( $field['name'] ) . '%</li>';
				}
				?></ul>
        </div>
    </div>

</div>
