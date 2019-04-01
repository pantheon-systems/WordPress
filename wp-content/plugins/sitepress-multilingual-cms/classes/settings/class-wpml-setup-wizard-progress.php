<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Setup_Wizard_Progress {
	private $items;
	private $current_step;

	/**
	 * WPML_Setup_Wizard_Progress constructor.
	 *
	 * @param int   $current_step
	 * @param array $items
	 */
	public function __construct( $current_step, array $items ) {
		$this->current_step = $current_step ? $current_step : 1;
		$this->items        = $items;
	}

	public function render() {
		$this->items = apply_filters( 'wpml_setup_wizard_progress_items', $this->items );
		?>
			<div class="wpml-wizard">
				<ul class="wizard-steps-container js-wizard-steps-container">
			<?php
			foreach ( $this->items as $step => $text ) {
				?>
					<li class="<?php echo $this->get_step_classes( $step ); ?>">
				  <?php echo esc_html( $text ); ?>
					</li>
				<?php
			}
			?>
				</ul>
			</div>
		<?php
	}

	private function get_step_classes( $step ) {
		$step_classes = array( 'wizard-step', 'js-wizard-step' );
		if ( $step === $this->current_step ) {
			$step_classes[] = 'wizard-current-step';
		}
		if ( $step < $this->current_step ) {
			$step_classes[] = 'wizard-active-step';
		}

		return implode( ' ', $step_classes );
	}
}
