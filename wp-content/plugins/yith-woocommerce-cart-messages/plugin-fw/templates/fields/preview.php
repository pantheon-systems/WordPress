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
$class = !empty( $class ) ? $class : 'yith-plugin-fw-preview-field';

?>
<img src="<?php echo $value ?>" class="<?php echo $class ?>"
    <?php echo $custom_attributes ?>
    <?php if ( isset( $data ) ) echo yith_plugin_fw_html_data_to_string( $data ); ?>>