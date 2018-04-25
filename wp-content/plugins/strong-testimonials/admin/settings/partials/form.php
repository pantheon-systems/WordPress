<?php
/**
 * Form Settings
 *
 * @package Strong_Testimonials
 * @since   1.13
 */
$pages_list   = wpmtst_get_pages();
$form_options = get_option( 'wpmtst_form_options' );
$plugins      = apply_filters( 'wpmtst_captcha_plugins', get_option( 'wpmtst_captcha_plugins', array() ) );

/**
 * If integration with selected Captcha plugin has been removed, disable Captcha.
 */
if ( ! is_array( $plugins ) || ! in_array( $form_options['captcha'], array_keys( $plugins ) ) ) {
	$form_options['captcha'] = '';
	update_option( 'wpmtst_form_options', $form_options );
}

foreach ( $plugins as $key => $plugin ) {

	if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin['file'] ) ) {
		$plugins[ $key ]['installed'] = true;
	}

	$plugins[ $key ]['active'] = is_plugin_active( $plugin['file'] );

	/**
	 * If current Captcha plugin has been deactivated, disable Captcha
	 * so corresponding div does not appear on front-end form.
	 */
	if ( $key == $form_options['captcha'] && ! $plugins[ $key ]['active'] ) {
		$form_options['captcha'] = '';
		update_option( 'wpmtst_form_options', $form_options );
	}

}
?>
<input type="hidden"
       name="wpmtst_form_options[default_recipient]"
       value="<?php echo htmlentities( serialize( $form_options['default_recipient'] ) ); ?>">

<?php
/**
 * ========================================
 * Labels & Messages
 * ========================================
 */
?>
<h2><?php _e( 'Form Labels & Messages', 'strong-testimonials' ); ?></h2>

<?php do_action( 'wpmtst_before_form_settings', 'form-messages' ); ?>

<table class="form-table compact" cellpadding="0" cellspacing="0">
	<?php
	$messages = $form_options['messages'];
	foreach ( $messages as $key => $message ):
		$elid = str_replace( '-', '_', $key );
		// $string, $context, $name
		$content = apply_filters( 'wpmtst_l10n', $message['text'], 'strong-testimonials-form-messages', $message['description'] );
		?>

        <tr>
            <th scope="row">
                <label for="<?php echo $elid; ?>">
					<?php _ex( $message['description'], 'description', 'strong-testimonials' ); ?>
                </label>
                <input type="hidden" name="wpmtst_form_options[messages][<?php echo $key; ?>][description]"
                       value="<?php esc_attr_e( $message['description'] ); ?>"/>
            </th>
            <td>
				<?php if ( 'submission_success' == $elid ): ?>
					<?php
					$settings = array(
						'textarea_name' => "wpmtst_form_options[messages][$key][text]",
						'textarea_rows' => 10,
					);
					wp_editor( $content, $elid, $settings );
					?>
				<?php else: ?>
					<?php if ( 'required_field' == $elid ): ?>
                        <fieldset>
                            <label>
                                <input type="checkbox"
                                       name="wpmtst_form_options[messages][<?php echo $key; ?>][enabled]"
                                       <?php checked( $message['enabled'] ); ?>">
								<?php _e( 'Display required notice at top of form', 'strong-testimonials' ); ?>
                            </label
                        </fieldset>
					<?php endif; ?>
                    <input type="text" id="<?php echo $elid; ?>"
                           name="wpmtst_form_options[messages][<?php echo $key; ?>][text]"
                           value="<?php esc_attr_e( $content ); ?>" required/>
				<?php endif; ?>
            </td>
            <td class="actions">
                <input type="button" class="button secondary restore-default-message"
                       value="<?php _ex( 'restore default', 'singular', 'strong-testimonials' ); ?>"
                       data-target-id="<?php esc_attr_e( $elid ); ?>"/>
            </td>
        </tr>

	<?php endforeach; ?>

    <tr>
        <td colspan="3">
            <input type="button" id="restore-default-messages" class="button"
                   name="restore-default-messages"
                   value="<?php _e( 'Restore Default Messages', 'strong-testimonials' ); ?>"/>
        </td>
    </tr>
</table>

<table class="form-table" cellpadding="0" cellspacing="0">
    <tr>
        <th scope="row" class="tall">
			<?php _e( 'Scroll', 'strong-testimonials' ); ?>
        </th>
        <td>
            <fieldset>
                <div>
                    <label>
                        <input type="checkbox"
                               name="wpmtst_form_options[scrolltop_error]" <?php checked( $form_options['scrolltop_error'] ); ?>/>
						<?php printf( __( 'If errors, scroll to the first error minus %s pixels. On by default.', 'strong-testimonials' ), '<input type="text" name="wpmtst_form_options[scrolltop_error_offset]" value="' . $form_options['scrolltop_error_offset'] . '" size="3">' ); ?>
                    </label>
                </div>
                <div>
                    <label class="block">
                        <input type="checkbox"
                               name="wpmtst_form_options[scrolltop_success]" <?php checked( $form_options['scrolltop_success'] ); ?>/>
						<?php printf( __( 'If success, scroll to the success message minus %s pixels. On by default.', 'strong-testimonials' ), '<input type="text" name="wpmtst_form_options[scrolltop_success_offset]" value="' . $form_options['scrolltop_success_offset'] . '" size="3">' ); ?>
                    </label>
                </div>
            </fieldset>
        </td>
    </tr>
</table>

<?php
/**
 * ========================================
 * Actions
 * ========================================
 */
?>
<hr>
<h3><?php _e( 'Form Actions', 'strong-testimonials' ); ?></h3>

<table class="form-table" cellpadding="0" cellspacing="0">
    <tr>
        <th scope="row">
            <label for="redirect-page">
				<?php _e( 'Upon Successful Submission', 'strong-testimonials' ); ?>
            </label>
        </th>
        <td>
            <div>
                <label class="success-action">
                    <input type="radio"
                           name="wpmtst_form_options[success_action]"
                           value="message" <?php checked( 'message', $form_options['success_action'] ); ?>/> <?php _e( 'display message', 'strong-testimonials' ); ?>
                </label>
            </div>

            <div>
                <label class="success-action">
                    <input type="radio"
                           name="wpmtst_form_options[success_action]"
                           value="id" <?php checked( 'id', $form_options['success_action'] ); ?>/> <?php _e( 'redirect to a page', 'strong-testimonials' ); ?>
                </label>

                <select id="redirect-page" name="wpmtst_form_options[success_redirect_id]">

                    <option value=""><?php _e( '&mdash; select a page &mdash;' ); ?></option>

					<?php foreach ( $pages_list as $pages ) : ?>

                        <option value="<?php echo $pages->ID; ?>" <?php selected( isset( $form_options['success_redirect_id'] ) ? $form_options['success_redirect_id'] : 0, $pages->ID ); ?>>
							<?php echo $pages->post_title; ?>
                        </option>

					<?php endforeach; ?>

                </select>

                <div style="display: inline-block; text-indent: 20px;">
                    <label>
						<?php _ex( 'or enter its ID or slug', 'to select a redirect page', 'strong-testimonials' ); ?>
                        &nbsp;
                        <input type="text"
                               id="redirect-page-2"
                               name="wpmtst_form_options[success_redirect_2]"
                               size="30">
                    </label>
                </div>
            </div>

            <div>
                <label class="success-action">
                    <input type="radio"
                           name="wpmtst_form_options[success_action]"
                           value="url" <?php checked( 'url', $form_options['success_action'] ); ?>/> <?php _e( 'redirect to a URL', 'strong-testimonials' ); ?>
                </label>
                <label>
                    <input type="text" id="redirect-page-3"
                           name="wpmtst_form_options[success_redirect_url]"
                           value="<?php echo $form_options['success_redirect_url']; ?>" size="75"/>
                </label>
            </div>

        </td>
    </tr>

    <tr>
        <th scope="row">
            <label>
				<?php _e( 'Post Status', 'strong-testimonials' ); ?>
            </label>
        </th>
        <td>
            <ul class="compact">
                <li>
                    <label>
                        <input type="radio" name="wpmtst_form_options[post_status]" value="pending"
							<?php checked( 'pending', $form_options['post_status'] ); ?>/>
						<?php _e( 'Pending', 'strong-testimonials' ); ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" name="wpmtst_form_options[post_status]" value="publish"
							<?php checked( 'publish', $form_options['post_status'] ); ?>/>
						<?php _e( 'Published' ); ?>
                    </label>
                </li>
            </ul>
        </td>
    </tr>

    <tr>
        <th scope="row">
            <label for="wpmtst-options-admin-notify">
				<?php _e( 'Notification Email', 'strong-testimonials' ); ?>
            </label>
        </th>

        <td>
            <div class="match-height">
                <fieldset>
                    <label for="wpmtst-options-admin-notify">
                        <input id="wpmtst-options-admin-notify" type="checkbox" name="wpmtst_form_options[admin_notify]"
							<?php checked( $form_options['admin_notify'] ); ?>/>
						<?php _e( 'Send an email upon new testimonial submission.', 'strong-testimonials' ); ?>
                    </label>
                </fieldset>
            </div>
            <div class="email-container"
                 id="admin-notify-fields" <?php echo ( $form_options['admin_notify'] ) ? '' : 'style="display: none;"'; ?>>
				<?php
				include 'email-from.php';
				include 'email-to.php';
				include 'email.php';
				do_action( 'wpmtst_after_notification_fields', 'notification' );
				?>
            </div>
        </td>
    </tr>
</table>

<?php
/**
 * ========================================
 * Spam Control
 * ========================================
 */
?>
<hr>
<h3><?php _e( 'Form Spam Control', 'strong-testimonials' ); ?></h3>

<table class="form-table" cellpadding="0" cellspacing="0">
    <tr>
        <th scope="row">
            <label>
				<?php _ex( 'Honeypot', 'spam control techniques', 'strong-testimonials' ); ?>
            </label>
        </th>
        <td>
            <p>
				<?php _e( 'These methods for trapping spambots are both time-tested and widely used. May be used simultaneously for more protection.', 'strong-testimonials' ); ?>
            </p>
            <p>
				<?php _e( 'However, honeypots may not be compatible with WP-SpamShield, Ajax page loading, caching or minification.', 'strong-testimonials' ); ?>
            </p>
            <p>
				<?php _e( 'If your form is not working properly, try disabling these.', 'strong-testimonials' ); ?>
            </p>
			<?php // TODO Add link to article that explains Ajax page loading. ?>
            <ul>
                <li class="checkbox">
                    <label>
                        <input type="checkbox"
                               name="wpmtst_form_options[honeypot_before]" <?php checked( $form_options['honeypot_before'] ); ?>/>
						<?php _e( 'Before', 'strong-testimonials' ); ?>
                    </label>
                    <p class="description"><?php _e( 'Adds a new empty field that is invisible to humans. Spambots tend to fill in every field they find in the form. Empty field = human. Not empty = spambot.', 'strong-testimonials' ); ?></p>
                </li>
                <li class="checkbox">
                    <label>
                        <input type="checkbox"
                               name="wpmtst_form_options[honeypot_after]" <?php checked( $form_options['honeypot_after'] ); ?>/>
						<?php _e( 'After', 'strong-testimonials' ); ?>
                    </label>
                    <p class="description"><?php _e( 'Adds a new field as soon as the form is submitted. Spambots cannot run JavaScript so the new field never gets added. New field = human. Missing = spambot.', 'strong-testimonials' ); ?></p>
                </li>
            </ul>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <label>
                <a name="captcha-section"></a><?php _e( 'Captcha', 'strong-testimonials' ); ?>
            </label>
        </th>
        <td class="stackem">
            <p>
				<?php _e( 'Enable Captcha using one of these plugins. Be sure to configure any plugins first, if necessary.', 'strong-testimonials' ); ?>
				<?php _e( 'May be used alongside honeypot methods.', 'strong-testimonials' ); ?>
            </p>
            <p>
				<?php _e( 'May not be compatible with Ajax page loading.', 'strong-testimonials' ); ?>
            </p>
            <ul>
                <li>
                    <label>
                        <input type="radio"
                               name="wpmtst_form_options[captcha]" <?php checked( '', $form_options['captcha'] ); ?>
                               value=""/> none
                    </label>
                </li>

				<?php foreach ( $plugins as $key => $plugin ) : ?>
                    <li>
                        <label class="inline <?php if ( ! $plugin['active'] ) echo 'disabled'; ?>">
                            <input type="radio"
                                   name="wpmtst_form_options[captcha]" <?php disabled( ! $plugin['active'] ); ?><?php checked( $key, $form_options['captcha'] ); ?>
                                   value="<?php echo $key; ?>"/>
							<?php echo $plugin['name']; ?>
                        </label>

						<?php if ( isset( $plugin['installed'] ) && $plugin['installed'] ) : // installed ?>

							<?php if ( $plugin['active'] ) : // active ?>

								<?php if ( isset( $plugin['settings'] ) && $plugin['settings'] ) : ?>
                                    <span class="link"><a href="<?php echo $plugin['settings']; ?>"><?php _ex( 'settings', 'link', 'strong-testimonials' ); ?></a></span>
								<?php else : ?>
                                    <span class="notice"><?php _e( 'no settings', 'strong-testimonials' ); ?></span>
								<?php endif; ?>

							<?php else : // inactive ?>

                                <span class="notice disabled"><?php _ex( 'inactive', 'adjective', 'strong-testimonials' ); ?></span>

							<?php endif; ?>
                            |

						<?php else : // not installed ?>

                            <span class="notice disabled">(<?php _e( 'not installed', 'strong-testimonials' ); ?>)</span>

                            <?php if ( isset( $plugin['search'] ) && $plugin['search'] ) : ?>
                                <span class="link"><a href="<?php echo $plugin['search']; ?>"><?php _ex( 'install plugin', 'link', 'strong-testimonials' ); ?></a></span>
                                |
                            <?php endif; ?>

						<?php endif; // whether installed ?>

                        <span class="link"><a href="<?php echo $plugin['url']; ?>" target="_blank"><?php _ex( 'plugin page', 'link', 'strong-testimonials' ); ?></a></span>

						<?php if ( isset( $plugin['desc'] ) && $plugin['desc'] ) : ?>
                            <p class="description <?php if ( isset( $plugin['style'] ) ) echo $plugin['style']; ?>"><?php echo $plugin['desc']; ?></p>
						<?php endif; ?>
                    </li>
				<?php endforeach; ?>
            </ul>
        </td>
    </tr>
</table>
