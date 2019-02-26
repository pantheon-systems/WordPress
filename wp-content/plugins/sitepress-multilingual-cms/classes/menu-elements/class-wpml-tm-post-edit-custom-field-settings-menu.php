<?php

/**
 * Class WPML_TM_Post_Edit_Custom_Field_Settings_Menu
 */
class WPML_TM_Post_Edit_Custom_Field_Settings_Menu {

	/** @var  WPML_Custom_Field_Setting_Factory $setting_factory */
	private $setting_factory;

	/** @var WP_Post $post */
	private $post;

	private $rendered = false;

	/**
	 * WPML_TM_Post_Edit_Custom_Field_Settings_Menu constructor.
	 *
	 * @param WPML_Custom_Field_Setting_Factory $settings_factory
	 * @param WP_Post                           $post
	 */
	public function __construct( &$settings_factory, $post ) {
		$this->setting_factory = &$settings_factory;
		$this->post            = $post;
	}

	/**
	 * @return string
	 */
	public function render() {
		$custom_keys = get_post_custom_keys( $this->post->ID );
		$custom_keys = $this->setting_factory->filter_custom_field_keys( $custom_keys );
		ob_start();
		if ( 0 !== count( $custom_keys ) ) {
			?>
			<table class="widefat">
				<thead>
				<tr>
					<th colspan="2"><?php esc_html_e( 'Custom fields', 'sitepress' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $custom_keys as $cfield ) {
					$field_setting = $this->setting_factory->post_meta_setting( $cfield );
					if ( $field_setting->excluded() ) {
						continue;
					}
					$this->rendered = true;
					$radio_disabled = ( $field_setting->is_read_only() && ! $field_setting->is_unlocked() ) ? 'disabled="disabled"' : '';
					$status         = (int) $field_setting->status();
					$states         = array(
						array( 'value' => WPML_IGNORE_CUSTOM_FIELD, 'text' => __( "Don't translate", 'sitepress' ) ),
						array( 'value' => WPML_COPY_CUSTOM_FIELD, 'text' => __( "Copy", 'sitepress' ) ),
						array( 'value' => WPML_COPY_ONCE_CUSTOM_FIELD, 'text' => __( "Copy once", 'sitepress' ) ),
						array( 'value' => WPML_TRANSLATE_CUSTOM_FIELD, 'text' => __( "Translate", 'sitepress' ) ),
					);
					?>
					<tr>
						<td id="icl_mcs_cf_<?php echo esc_attr( base64_encode( $cfield ) ); ?>">
							<div class="icl_mcs_cf_name">
								<?php echo esc_html( $cfield ); ?>
							</div>
							<?php
							/**
							 * Filter for custom field description in multilingual content setup metabox on post edit screen
							 *
							 * @param string $custom_field_description  custom field description
							 * @param string $custom_field_name         custom field name
							 * @param int    $post_id                   current post ID
							 */
							$cfield_description = apply_filters( 'wpml_post_edit_settings_custom_field_description', "", $cfield, $this->post->ID );
							if ( !empty( $cfield_description ) ) {
								printf( '<div class="icl_mcs_cf_description">%s</div>', esc_html( $cfield_description ) );
							}
							?>
						</td>
						<td align="right">
							<?php
							foreach ( $states as $state ) {
								$checked = $status == $state['value'] ? ' checked="checked"' : '';
								?>
									<label>
										<input class="icl_mcs_cfs"
									              name="icl_mcs_cf_<?php echo esc_attr( base64_encode( $cfield ) ); ?>"
									              type="radio"
									              value="<?php echo esc_attr( $state['value'] ); ?>" <?php echo esc_attr( $radio_disabled . $checked ); ?>
										/>
										<?php echo esc_html( $state['text'] ) ?>
									</label>&nbsp;
								<?php
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<br/>
			<?php
		}
		return ob_get_clean();
	}

	/**
	 * @return bool true if there were actual custom fields to display options for
	 */
	public function is_rendered() {

		return $this->rendered;
	}
}
