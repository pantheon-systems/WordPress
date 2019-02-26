<?php
/**
 * Header nav space
 *
 * @package    Salient WordPress Theme
 * @subpackage Partials
 * @version    9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$nectar_header_options = nectar_get_header_variables();

if ( $nectar_header_options['perm_trans'] != 1 || $nectar_header_options['perm_trans'] == 1 && $nectar_header_options['bg_header'] == 'false' || $nectar_header_options['page_full_screen_rows'] == 'on' ) { ?>
  
<div id="header-space" data-header-mobile-fixed='<?php echo esc_attr( $nectar_header_options['mobile_fixed'] ); ?>'></div> 

	<?php

}