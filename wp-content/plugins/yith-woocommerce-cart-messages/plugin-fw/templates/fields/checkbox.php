<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @var array $field
 *
 */
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

extract( $field );
?>
<input type="checkbox" id="<?php echo $id ?>"
       name="<?php echo $name ?>" value="1"
    <?php echo !empty( $class ) ? "class='$class'" : ''; ?>
       <?php if ( isset( $std ) ) : ?>data-std="<?php echo $std ?>" <?php endif; ?>
    <?php checked( true, yith_plugin_fw_is_true( $value ) ) ?>
    <?php echo $custom_attributes ?>
    <?php if ( isset( $data ) ) echo yith_plugin_fw_html_data_to_string( $data ); ?>/>
<?php
if ( isset( $field[ 'desc-inline' ] ) )
    echo "<span class='description inline'>" . $field[ 'desc-inline' ] . "</span>";

?>
