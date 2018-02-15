<?php
// Indicate if template not found; for example, after switching themes or deactivating add-ons.
/* translators: On the Views admin screen. */
?>
<?php if ( ! $template_found ) : ?>
    <ul class="radio-list template-list">
        <li>
            <div>
                <input class="error" type="radio" id="<?php esc_attr_e( $view['template'] ); ?>"
                       name="view[data][<?php esc_attr_e( $current_mode ); ?>]"
                       value="<?php esc_attr_e( $view['template'] ); ?>" checked>
                <label for="<?php esc_attr_e( $view['template'] ); ?>">
					<?php echo $view['template']; ?>
                </label>
            </div>
            <div class="template-description">
                <p>
                    <span class="dashicons dashicons-warning error"></span>&nbsp;
                    <span class="error"><?php _e( 'not found', 'strong-testimonials' ); ?></span>
                </p>
				<?php // TODO Explain: Add template key and explain this could be due to switching themes or deactivating a template plugin. It's OK to leave this setting as is if testing or troubleshooting. ?>
            </div>
        </li>
    </ul>
<?php endif;
