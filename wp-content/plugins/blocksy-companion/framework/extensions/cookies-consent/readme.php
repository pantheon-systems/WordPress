<h2><?php echo __('Instructions', 'blocksy-companion'); ?></h2>

<p>
	<?php echo __('After installing and activating the Cookies Consent extension you will be able to configure it from this location:', 'blocksy-companion') ?>
</p>

<ul class="ct-modal-list">
	<li>
		<h4><?php echo __('Customizer', 'blocksy-companion') ?></h4>
		<i>
		<?php
			echo sprintf(
				__('Navigate to %s and customize the notification to meet your needs.', 'blocksy-companion'),
				sprintf(
					'<code>%s</code>',
					__('Customizer ➝ Cookie Consent', 'blocksy-companion')
				)
			);
		?>
		</i>
	</li>
</ul>
