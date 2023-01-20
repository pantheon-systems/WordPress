<?php

$attr = apply_filters('blocksy:hero:wrapper-attr', $attr);

$prefix = blocksy_manager()->screen->get_prefix();

?>

<div <?php echo blocksy_attr_to_html($attr) ?>>
	<?php if ($prefix === 'courses_single' && function_exists('tutor')) { ?>
		<?php echo $elements ?>
	<?php } else { ?>
		<header class="entry-header">
			<?php echo $elements ?>
		</header>
	<?php } ?>
</div>
