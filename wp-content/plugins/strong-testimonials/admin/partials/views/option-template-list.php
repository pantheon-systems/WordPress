<?php /* translators: On the Views admin screen. */ ?>
<th>
	<?php _e( 'Template', 'strong-testimonials' ); ?>
</th>
<td colspan="2">
    <div id="view-template-list">
        <div class="radio-buttons">

			<?php include 'template-not-found.php'; ?>

			<?php
			// Source groups
					$current_source = '';
			foreach ( $templates[ $current_type ] as $source => $source_templates ) {
                        if ( $source != $current_source ) {
                            echo '<div class="template-optgroup">' . $source . '</div>';
                            $current_source = $source;
                        }
					?>
                <ul class="radio-list template-list">
					<?php foreach ( $source_templates as $key => $template ) : ?>
                        <li>
							<?php include 'template-input.php'; ?>
							<?php include 'template-options.php'; ?>
                        </li>
					<?php endforeach; ?>
				</ul>
				<?php
				}
				?>

		</div>

	</div>
</td>
