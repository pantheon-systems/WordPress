<?php
/**
 * Mailchimp widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package Blocksy
 */


// Widget title
$title = blocksy_default_akg( 'title', $atts, __( 'Contact Info', 'blocksy-companion' ) );

// Text
$text = do_shortcode(blocksy_default_akg( 'contact_text', $atts, '' ));

$contact_information = blocksy_default_akg(
	'contact_information',
	$atts,
	[
		[
			'id' => 'address',
			'enabled' => true,
			'title' => __('Address:', 'blocksy-companion'),
			'content' => 'Street Name, NY 38954',
		],

		[
			'id' => 'phone',
			'enabled' => true,
			'title' => __('Phone:', 'blocksy-companion'),
			'content' => '578-393-4937',
			'link' => 'tel:578-393-4937',
		],

		[
			'id' => 'mobile',
			'enabled' => true,
			'title' => __('Mobile:', 'blocksy-companion'),
			'content' => '578-393-4937',
			'link' => 'tel:578-393-4937',
		],
	]
);

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $before_widget;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
if (! empty(trim($title))) {
	echo $before_title . wp_kses_post($title) . $after_title;
}

?>

<?php if (! empty($text)) { ?>
	<div class="ct-contact-info-text">
		<?php echo wp_kses_post($text) ?>
	</div>
<?php }

echo blc_call_fn(['fn' => 'blc_get_contacts_output'], [
	'data' => $contact_information,
	'link_target' => blocksy_default_akg('contact_link_target', $atts, 'no'),
	'type' => blocksy_akg('contacts_icon_shape', $atts, 'rounded'),
	'fill' => blocksy_akg('contacts_icon_fill_type', $atts, 'outline'),
	'size' => blocksy_akg('contacts_icons_size', $atts, 'medium'),
	'direction' => 'vertical'
]);

echo wp_kses_post($after_widget);
