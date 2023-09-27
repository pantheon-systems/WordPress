<?php
/**
 * Facebook widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

$options = [
	'title' => [
		'type' => 'text',
		'label' => __( 'Title', 'blocksy-companion' ),
		'field_attr' => [ 'id' => 'widget-title' ],
		'design' => 'inline',
		'value' => __( 'Facebook', 'blocksy-companion' ),
		'disableRevertButton' => true,
	],

	'facebook_page_url' => [
		'type' => 'text',
		'label' => __( 'Page URL', 'blocksy-companion' ),
		'field_attr' => [ 'id' => 'widget-title' ],
		'design' => 'inline',
		'disableRevertButton' => true,
	],

	'facebook_faces' => [
		'type'  => 'ct-switch',
		'label' => __( 'Profile Photos', 'blocksy-companion' ),
		'value' => 'yes',
		'design' => 'inline-full',
	],

	'facebook_timeline' => [
		'type'  => 'ct-switch',
		'label' => __( 'Timeline', 'blocksy-companion' ),
		'value' => 'no',
		'design' => 'inline-full',
	],

	'facebook_cover' => [
		'type'  => 'ct-switch',
		'label' => __( 'Cover Photo', 'blocksy-companion' ),
		'value' => 'no',
		'design' => 'inline-full',
	],

	'facebook_small_header' => [
		'type'  => 'ct-switch',
		'label' => __( 'Small Header', 'blocksy-companion' ),
		'value' => 'no',
		'design' => 'inline-full',
	],

];
