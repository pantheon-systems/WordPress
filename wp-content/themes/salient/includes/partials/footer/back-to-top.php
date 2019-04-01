<?php
/**
 * Back to top button
 *
 * @package Salient WordPress Theme
 * @subpackage Partials
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$options = get_nectar_theme_options();

if ( ! empty( $options['back-to-top'] ) && $options['back-to-top'] == 1 ) { ?>
	<a id="to-top" class="
	<?php
	if ( ! empty( $options['back-to-top-mobile'] ) && $options['back-to-top-mobile'] == 1 ) {
		echo 'mobile-enabled';}
	?>
	"><i class="fa fa-angle-up"></i></a>
	<?php
}
