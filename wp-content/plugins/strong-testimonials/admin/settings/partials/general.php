<?php
/**
 * Settings
 *
 * @package Strong_Testimonials
 * @since 1.13
 */

$options = get_option( 'wpmtst_options' );
?>
<h2><?php _e( 'Admin', 'strong-testimonials' ); ?></h2>

<table class="form-table" cellpadding="0" cellspacing="0">

	<tr valign="top">
		<th scope="row">
			<?php _e( 'Pending Indicator', 'strong-testimonials' ); ?>
		</th>
		<td>
			<fieldset>
				<label>
					<input type="checkbox" name="wpmtst_options[pending_indicator]" <?php checked( $options['pending_indicator'] ); ?>>
					<?php _e( 'Show indicator bubble when new submissions are awaiting moderation.', 'strong-testimonials' ); ?>
                    <?php _e( 'On by default.', 'strong-testimonials' ); ?>
				</label>
			</fieldset>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<?php _e( 'Reordering', 'strong-testimonials' ); ?>
		</th>
		<td>
			<fieldset>
			<label>
				<input type="checkbox" name="wpmtst_options[reorder]" <?php checked( $options['reorder'] ); ?>>
				<?php _e( 'Enable drag-and-drop reordering in the testimonial list.', 'strong-testimonials' ); ?>
				<?php _e( 'Off by default.', 'strong-testimonials' ); ?>
			</label>
            <p class="description"><?php _e( 'Then set <b>Order</b> to "menu order" in the View.', 'strong-testimonials' ); ?></p>
			</fieldset>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<?php _e( 'Custom Fields Meta Box', 'strong-testimonials' ); ?>
		</th>
		<td>
			<fieldset>
			<label>
				<input type="checkbox" name="wpmtst_options[support_custom_fields]" <?php checked( $options['support_custom_fields'] ); ?>>
				<?php _e( 'Show the <strong>Custom Fields</strong> meta box in the testimonial post editor. This does not affect the <strong>Client Details</strong> meta box.', 'strong-testimonials' ); ?>
				<?php _e( 'Off by default.', 'strong-testimonials' ); ?>
			</label>
            <p class="description"><?php _e( 'For advanced users.', 'strong-testimonials' ); ?></p>
			</fieldset>
		</td>
	</tr>

</table>

<hr/>
<h2><?php _e( 'Output', 'strong-testimonials' ); ?></h2>

<table class="form-table" cellpadding="0" cellspacing="0">

	<tr valign="top">
		<th scope="row">
			<?php _e( 'Enable Touch', 'strong-testimonials' ); ?>
		</th>
		<td>
			<fieldset>
			<label>
				<input type="checkbox" name="wpmtst_options[touch_enabled]" <?php checked( $options['touch_enabled'] ); ?>>
				<?php _e( 'Enable touch swipe navigation in slideshows.', 'strong-testimonials' ); ?>
                <?php _e( 'On by default.', 'strong-testimonials' ); ?>
			</label>
            <p class="description"><?php _e( 'If you are having trouble scrolling long testimonials on a small screen, try disabling this.', 'strong-testimonials' ); ?></p>
			</fieldset>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<?php _e( 'Scroll Top', 'strong-testimonials' ); ?>
		</th>
		<td>
			<fieldset>
			<label>
				<input type="checkbox" name="wpmtst_options[scrolltop]" <?php checked( $options['scrolltop'] ); ?>>
				<?php printf( __( 'When a new page is selected in paginated Views, scroll to the top of the container minus %s pixels.', 'strong-testimonials' ), '<input type="text" name="wpmtst_options[scrolltop_offset]" value="' . $options['scrolltop_offset'] . '" size="3">' ); ?>
                <?php _e( 'On by default.', 'strong-testimonials' ); ?>
			</label>
			</fieldset>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<?php _e( 'Remove Whitespace', 'strong-testimonials' ); ?>
		</th>
		<td>
			<fieldset>
			<label>
				<input type="checkbox" name="wpmtst_options[remove_whitespace]" <?php checked( $options['remove_whitespace'] ); ?>>
				<?php _e( 'Remove space between HTML tags in View output to prevent double paragraphs <em>(wpautop)</em>.', 'strong-testimonials' ); ?>
                <?php _e( 'On by default.', 'strong-testimonials' ); ?>
			</label>
			</fieldset>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<?php _e( 'Comments', 'strong-testimonials' ); ?>
		</th>
		<td>
			<fieldset>
                <label>
                    <input type="checkbox" name="wpmtst_options[support_comments]" <?php checked( $options['support_comments'] ); ?>>
                    <?php _e( 'Allow comments on testimonials. Requires using your theme\'s single post template.', 'strong-testimonials' ); ?>
                    <?php _e( 'Off by default.', 'strong-testimonials' ); ?>
                </label>
			</fieldset>
			<p class="description"><?php _e( 'To enable comments:', 'strong-testimonials' ); ?></p>
			<ul class="description">
				<li><?php _e( 'For individual testimonials, use the <strong>Discussion</strong> meta box in the post editor or <strong>Quick Edit</strong> in the testimonial list.', 'strong-testimonials' ); ?></li>
				<li><?php _e( 'For multiple testimonials, use <strong>Bulk Edit</strong> in the testimonial list.', 'strong-testimonials' ); ?></li>
			</ul>
			<p class="description"><?php printf( '<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://strongplugins.com/document/strong-testimonials/enable-comments/' ),
				__( 'Tutorial', 'strong-testimonials' ) ); ?></p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<?php _e( 'Embed Width', 'strong-testimonials' ); ?>
		</th>
		<td>
			<fieldset>
                <?php printf(
                    /* Translators: %s is an input field. */
                    __( 'For embedded links (YouTube, Twitter, etc.) set the frame width to %s pixels.', 'strong-testimonials' ),
                    '<input type="text" name="wpmtst_options[embed_width]" value="' . $options['embed_width'] . '" size="3">' ); ?>
                <p class="description"><?php _e( 'Leave empty for default width (usually 100% for videos). Height will be calculated automatically. This setting only applies to Views.', 'strong-testimonials' ); ?></p>
                <p class="description">
                    <?php printf( '<a href="%s" target="_blank">%s</a>',
                        esc_url( 'https://codex.wordpress.org/Embeds' ),
                        __( 'More on embeds', 'strong-testimonials' ) ); ?> |
                    <?php printf( '<a href="%s" target="_blank">%s</a>',
                        esc_url( 'https://strongplugins.com/document/strong-testimonials/embed-social-media/' ),
                        __( 'Tutorial', 'strong-testimonials' ) ); ?>
                </p>
			</fieldset>
		</td>
	</tr>

    <tr valign="top">
        <th scope="row">
			<?php _e( 'Load Font Awesome', 'strong-testimonials' ); ?>
        </th>
        <td>
            <fieldset>
                <label>
                    <input type="checkbox" name="wpmtst_options[load_font_awesome]" <?php checked( $options['load_font_awesome'] ); ?>>
					<?php printf( __( 'Load the icon font necessary for star ratings %s, slideshow controls %s, and some template quotation marks %s. ','strong-testimonials' ),
                        '<i class="fa fa-star example" aria-hidden="true"></i>',
                        '<i class="fa fa-play example" aria-hidden="true"></i>',
                        '<i class="fa fa-quote-left example" aria-hidden="true"></i>' ); ?>
                    <?php _e( 'On by default.', 'strong-testimonials' ); ?>
                </label>
                <p class="description">
                    <?php _e( 'Some reasons to disable this:', 'strong-testimonials' ); ?>
                </p>
                <ul class="description">
                    <li>
                        <?php _e( 'Your theme or another plugin also loads Font Awesome and you want to make your site more efficient by only loading one copy.', 'strong-testimonials' ); ?>
                        <?php printf( 'Try <a href="%s" target="_blank">%s</a> for even more control.',
							esc_url( 'https://wordpress.org/plugins/better-font-awesome/' ),
							__( 'Better Font Awesome', 'strong-testimonials' ) ); ?></li>
                    <li><?php _e( 'You are overriding the icon CSS with images or another icon font.', 'strong-testimonials' ); ?></li>
                    <li><?php _e( 'You have no need for stars, slideshow controls, or quotation mark icons.', 'strong-testimonials' ); ?></li>
                    <li><?php _e( 'You know what you\'re doing.', 'strong-testimonials' ); ?></li>
                </ul>
            </fieldset>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
			<?php _e( 'Nofollow Links', 'strong-testimonials' ); ?>
        </th>
        <td>
            <fieldset>
                <label>
                    <input type="checkbox" name="wpmtst_options[nofollow]" <?php checked( $options['nofollow'] ); ?>>
					<?php _e( 'Add <code>rel="nofollow"</code> to URL custom fields.', 'strong-testimonials' ); ?>
                    <?php _e( 'Off by default.', 'strong-testimonials' ); ?>
                </label>
                <p class="description">
	                <?php printf( 'To edit this value on your existing testimonials in bulk, try <a href="%s" target="_blank">%s</a> and set <code>nofollow</code> to <b>default</b>, <b>yes</b> or <b>no</b>.',
		                esc_url( 'https://wordpress.org/plugins/custom-field-bulk-editor/' ),
		                __( 'Custom Field Bulk Editor', 'strong-testimonials' ) ); ?>
                </p>
            </fieldset>
        </td>
    </tr>

    <?php if ( wpmtst_is_plugin_active( 'lazy-loading-responsive-images' ) ) : ?>
    <tr valign="top">
        <th scope="row">
			<?php _e( 'No Lazy Loading', 'strong-testimonials' ); ?>
        </th>
        <td>
            <fieldset>
                <label>
                    <input type="checkbox" name="wpmtst_options[no_lazyload]" <?php checked( $options['no_lazyload'] ); ?>>
                    <?php printf( __( 'Exclude from <a href="%s" target="_blank">Lazy Loading Responsive Images</a> plugin.', 'strong-testimonials' ), esc_url( 'https://wordpress.org/plugins/lazy-loading-responsive-images/' ) ); ?>
                    <?php _e( 'On by default.', 'strong-testimonials' ); ?>
                </label>
            </fieldset>
        </td>
    </tr>
    <?php else : ?>
        <input type="hidden" name="wpmtst_options[no_lazyload]" value="<?php echo $options['no_lazyload']; ?>">
    <?php endif; ?>

</table>
