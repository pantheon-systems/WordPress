<?php
/* translators: On the Views admin screen. */
global $view;
?>
<th>
    <label for="view-divi_builder"><?php _e( 'Divi Builder', 'strong-testimonials-lucid-theme' ); ?></label>
</th>
<td>
    <div class="row">
        <div class="row-inner">
            <input type="checkbox" id="view-divi_builder" class="if toggle checkbox"
                   name="view[data][divi_builder]" value="1" <?php checked( $view['divi_builder'] ); ?>/>
            <label for="view-divi_builder">
                <?php _e( 'Check this if adding this view (via shortcode or widget) using the Visual Builder in <b>Divi Builder version 2</b>.', 'strong-testimonials' ); ?>
            </label>
            <p class="description short">
                <?php _e( 'Not required if simply adding this view in the default editor.', 'strong-testimonials' ); ?>
            </p>
            <p class="description short">
                <?php _e( 'Not required if simply adding this view in the <b>Divi theme</b> using either the default editor or Divi Builder.', 'strong-testimonials' ); ?>
            </p>
        </div>
    </div>
</td>
