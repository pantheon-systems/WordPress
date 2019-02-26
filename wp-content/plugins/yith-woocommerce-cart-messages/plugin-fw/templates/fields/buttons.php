<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

extract( $field );

if ( !empty( $buttons ) && is_array( $buttons ) ):
    foreach ( $buttons as $button ) :
        $button_default_args = array(
            'name'  => '',
            'class' => '',
            'data'  => array(),
        );
        $button = wp_parse_args( $button, $button_default_args );
        ?>
        <input type="button" class="<?php echo $button[ 'class' ]; ?> button button-secondary"
               value="<?php echo esc_attr( $button[ 'name' ] ) ?>" <?php echo yith_plugin_fw_html_data_to_string( $button[ 'data' ] ) ?>/>
    <?php endforeach;
endif; ?>
