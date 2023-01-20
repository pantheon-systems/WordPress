<?php
/**
 * Quote widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package Blocksy
 */


// Widget title
$title = blocksy_default_akg( 'title', $atts, __( 'Quote', 'blocksy-companion' ) );

// Quote text
$quote_text = do_shortcode(blocksy_default_akg( 'quote_text', $atts, '' ));

// Quote author
$quote_author = blocksy_default_akg( 'quote_author', $atts, __( 'John Doe', 'blocksy-companion' ) );

// Quote avatar
$attachment_id = blocksy_default_akg(
	'quote_avatar/attachment_id',
	$atts,
	null
);

$image_output = '';

if ($attachment_id) {
	$image_output = blc_call_fn(['fn' => 'blocksy_image'], [
		'attachment_id' => $attachment_id,
		'ratio' => '1/1',
		'tag_name' => 'figure',
		'size' => 'thumbnail'
	]);
}

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $before_widget;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
if (! empty(trim($title))) {
	echo $before_title . wp_kses_post( $title ) . $after_title;
}

?>

<blockquote>
	<?php echo $quote_text; ?>
</blockquote>

<div class="ct-quote-author">
	<?php echo $image_output; ?>

	<?php if (blocksy_default_akg('quote_has_by_label', $atts, 'yes') === 'yes') { ?>
		<?php echo sprintf(
			// translators: %s here is the author name
			__('By %s', 'blocksy-companion'),
			$quote_author
		) ?>
	<?php } else { ?>
		<?php echo $quote_author; ?>
	<?php } ?>
</div>

<?php echo wp_kses_post( $after_widget ); ?>
