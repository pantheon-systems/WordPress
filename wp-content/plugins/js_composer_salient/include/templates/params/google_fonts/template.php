<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="vc_row-fluid vc_column">
	<div class="wpb_element_label"><?php _e( 'Font Family', 'js_composer' ); ?></div>
	<div class="vc_google_fonts_form_field-font_family-container">
		<select class="vc_google_fonts_form_field-font_family-select"
			default[font_style]="<?php echo $values['font_style']; ?>">
			<?php
			/** @var $this Vc_Google_Fonts */
			$fonts = $this->_vc_google_fonts_get_fonts();
			foreach ( $fonts as $font_data ) : ?>
				<option value="<?php echo $font_data->font_family . ':' . $font_data->font_styles; ?>"
					data[font_types]="<?php echo $font_data->font_types; ?>"
					data[font_family]="<?php echo $font_data->font_family; ?>"
					data[font_styles]="<?php echo $font_data->font_styles; ?>"
					class="<?php echo vc_build_safe_css_class( $font_data->font_family ); ?>" <?php echo( strtolower( $values['font_family'] ) == strtolower( $font_data->font_family ) || strtolower( $values['font_family'] ) == strtolower( $font_data->font_family ) . ':' . $font_data->font_styles ? 'selected' : '' ); ?> ><?php echo $font_data->font_family ?></option>
			<?php endforeach ?>
		</select>
	</div>
	<?php if ( isset( $fields['font_family_description'] ) && strlen( $fields['font_family_description'] ) > 0 ) : ?>
		<span class="vc_description clear"><?php echo $fields['font_family_description']; ?></span>
	<?php endif ?>
</div>

<?php if ( isset( $fields['no_font_style'] ) && false === $fields['no_font_style'] || ! isset( $fields['no_font_style'] ) ) : ?>
	<div class="vc_row-fluid vc_column">
		<div class="wpb_element_label"><?php _e( 'Font style', 'js_composer' ); ?></div>
		<div class="vc_google_fonts_form_field-font_style-container">
			<select class="vc_google_fonts_form_field-font_style-select"></select>
		</div>
	</div>
	<?php if ( isset( $fields['font_style_description'] ) && strlen( $fields['font_style_description'] ) > 0 ) : ?>
		<span class="vc_description clear"><?php echo $fields['font_style_description']; ?></span>
	<?php endif ?>
<?php endif ?>

<div class="vc_row-fluid vc_column vc_google_fonts_form_field-preview-wrapper">
	<div class="wpb_element_label"><?php _e( 'Google Fonts preview', 'js_composer' ); ?>:</div>
	<div class="vc_google_fonts_form_field-preview-container">
		<span><?php _e( 'Grumpy wizards make toxic brew for the evil Queen and Jack.', 'js_composer' ); ?></span>
	</div>
	<div class="vc_google_fonts_form_field-status-container"><span></span></div>
</div>

<input name="<?php echo $settings['param_name']; ?>"
	class="wpb_vc_param_value  <?php echo $settings['param_name'] . ' ' . $settings['type']; ?>_field" type="hidden"
	value="<?php echo $value; ?>"/>
