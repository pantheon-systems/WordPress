<?php

class WPML_Custom_Types_Translation_UI {

	/** @var array */
	private $translation_option_class_names;

	/** @var WPML_Translation_Modes $translation_modes */
	private $translation_modes;

	/** @var WPML_UI_Unlock_Button $unlock_button_ui */
	private $unlock_button_ui;

	public function __construct( WPML_Translation_Modes $translation_modes, WPML_UI_Unlock_Button $unlock_button_ui ) {
		$this->translation_modes              = $translation_modes;
		$this->unlock_button_ui               = $unlock_button_ui;
		$this->translation_option_class_names = array(
			WPML_CONTENT_TYPE_TRANSLATE                => 'translate',
			WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED => 'display-as-translated',
			WPML_CONTENT_TYPE_DONT_TRANSLATE           => 'dont-translate'
		);
	}

	public function render_custom_types_header_ui( $type_label ) {
		?>
		<header class="wpml-flex-table-header wpml-flex-table-sticky">
			<div class="wpml-flex-table-row">
				<div class="wpml-flex-table-cell name">
					<?php echo $type_label ?>
				</div>
				<?php foreach ( $this->translation_modes->get_options() as $value => $label ) { ?>
					<div
						class="wpml-flex-table-cell text-center <?php echo $this->translation_option_class_names[ $value ]; ?>">
						<?php echo esc_html( $label ) ?>
					</div>
				<?php } ?>
			</div>
		</header>
		<?php
	}

	public function render_row( $content_label, $name, $content_slug, $disabled, $current_translation_mode, $unlocked ) {
		$radio_name    = esc_attr( $name . '[' . $content_slug . ']' );
		$unlocked_name = esc_attr( $name . '_unlocked[' . $content_slug . ']' );
		?>
		<div class="wpml-flex-table-cell name">
			<?php
			$this->unlock_button_ui->render( $disabled, $unlocked, $radio_name, $unlocked_name );
			echo $content_label;
			?>
			(<i><?php echo esc_html( $content_slug ); ?></i>)
		</div>
		<?php foreach ( $this->translation_modes->get_options() as $value => $label ) {
			$disabled_state_for_mode = self::get_disabled_state_for_mode( $unlocked, $disabled, $value, $content_slug )
			?>
			<div
				class="wpml-flex-table-cell text-center <?php echo $this->translation_option_class_names[ $value ]; ?>"
				data-header="<?php esc_attr( $label ) ?>">
				<input type="radio" name="<?php echo $radio_name; ?>"
				       value="<?php echo esc_attr( $value ); ?>" <?php echo $disabled_state_for_mode['html_attribute'] ?>
					<?php checked( $value, $current_translation_mode ) ?> />
				<?php if ( $disabled_state_for_mode['reason_message'] ) { ?>
					<p class="otgs-notice warning js-disabled-externally"><?php echo wp_kses_post( $disabled_state_for_mode['reason_message'] ); ?></p>
				<?php } ?>
			</div>
			<?php
		}
	}

	/**
	 * @param bool $unlocked
	 * @param bool $disabled
	 * @param int $mode
	 * @param string $content_slug
	 *
	 * @return array
	 */
	public static function get_disabled_state_for_mode( $unlocked, $disabled, $mode, $content_slug ) {
		$disabled_state_for_mode = array(
			'state' => ! $unlocked && $disabled,
			'reason_message' => ''
		);
		$disabled_state_for_mode = apply_filters( 'wpml_disable_translation_mode_radio', $disabled_state_for_mode, $mode, $content_slug );
		$disabled_state_for_mode['html_attribute'] = $disabled_state_for_mode['state'] ? 'disabled="disabled"' : '';
		return $disabled_state_for_mode;
	}

}
