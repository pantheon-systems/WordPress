<?php
/* @var array $args Data passed to the view */
/* @var string $title Block title */
/* @var string $button_title Start button title */

$tab              = ( isset( $tab ) ) ? $tab : '';
$show_button      = ( isset( $show_button ) ) ? $show_button : true;
$description      = ( isset( $description ) ) ? $description : false;
$progress_percent = ( isset( $progress_percent ) ) ? $progress_percent : 0;
?>
<div class="block-scope <?php echo $progress_percent === 100 ? 'completed' : '' ?>" data-state='<?php echo wp_json_encode( $args ) ?>'>
	<div class="block-title-wrap <?php echo false !== $description ? 'with-description' : '' ?>">

		<?php if ( ! empty( $pie_chart ) ) : ?>
		<div class="pie-chart" data-percentage="<?php echo $progress_percent; ?>">
			<svg viewBox="-100 -100 200 200">
				<path d="" />
			</svg>
		</div>
		<?php endif ?>

		<h4 class="block-title"><?php echo $title; ?></h4>

		<?php if ( false !== $description ) : ?>
			<a href="#" class="general-helper"></a>
			<div class="helper-message">
				<?php echo $description; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="button-wrapper">
		<a href="#" class="as3cf-pro-tool start button"><?php echo $button_title; ?></a>
	</div>
</div><!-- /.block-scope -->


