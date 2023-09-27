<h2><?php echo __('Instructions', 'blocksy-companion'); ?></h2>

<p>
	<?php echo __('After installing and activating the Newsletter Subscribe
 extension you will have two possibilities to show your subscribe form:', 'blocksy-companion') ?>
</p>

<ol class="ct-modal-list">
	<li>
		<h4><?php echo __('Widget', 'blocksy-companion') ?></h4>
		<i>
		<?php
			echo sprintf(
				__('Navigate to %s and place the widget in any widget area you want.', 'blocksy-companion'),
				sprintf(
					'<code>%s</code>',
					__('Appearance ➝ Widgets', 'blocksy-companion')
				)
			);
		?>
		</i>
	</li>

	<li>
		<h4><?php echo __('Single Page Block', 'blocksy-companion') ?></h4>
		<i>
		<?php
			echo sprintf(
				__('Navigate to %s and customize the form and more.', 'blocksy-companion'),
				sprintf(
					'<code>%s</code>',
					__('Customizer ➝ Single Posts', 'blocksy-companion')
				)
			);
		?>
		</i>
	</li>
</ol>
