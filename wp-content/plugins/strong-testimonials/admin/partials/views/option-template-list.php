<?php /* translators: On the Views admin screen. */ ?>
<th>
	<?php _e( 'Template', 'strong-testimonials' ); ?>
</th>
<td colspan="2">
    <div id="view-template-list">
        <div class="radio-buttons">

			<?php include 'template-not-found.php'; ?>

            <ul class="radio-list template-list">
                <?php foreach ( $templates[ $current_type ] as $key => $template ) : ?>
                <li>
                    <?php include 'template-input.php'; ?>
                    <?php include 'template-options.php'; ?>
                </li>
                <?php endforeach; ?>
            </ul>

        </div>
    </div>
</td>
