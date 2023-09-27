<?php
/**
 * Group title
 *
 * @package Blocksy
 */

/**
 * Implement group title
 */
class Blocksy_Group_Title extends WP_Customize_Section {
	/**
	 * Type of this section.
	 *
	 * @var string
	 */
	public $type = 'ct-group-title';

	/**
	 * Special categorization for the section.
	 *
	 * @var string
	 */
	public $kind = 'default';

	/**
	 * Output
	 */
	public function render() {
		$description = $this->description;
		$class = 'accordion-section ct-group-title';

		if ('divider' === $this->kind) {
			$class = 'accordion-section ct-group-divider';
		}

		if ('option' === $this->kind) {
			$class = 'accordion-section ct-option-title';
		}

		?>

		<li
			id="accordion-section-<?php echo esc_attr( $this->id ); ?>"
			class="<?php echo esc_attr( $class ); ?>">
			<?php if (! empty($this->title) && strpos($this->title, '</div>') === false || $this->kind === 'divider') { ?>
			<h3><?php echo $this->title; ?></h3>
			<?php } else { ?>
			<?php echo $this->title; ?>
			<?php } ?>

			<?php if ( ! empty( $description ) ) { ?>
				<span class="description"><?php echo esc_html( $description ); ?></span>
			<?php } ?>
		</li>
		<?php
	}
}

