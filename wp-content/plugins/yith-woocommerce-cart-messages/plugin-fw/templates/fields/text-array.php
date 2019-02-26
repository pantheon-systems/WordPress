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
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

extract( $field );

$size = isset( $size ) ? " style=\"width:{$size}px;\"" : '';
?>
<table class="yith-plugin-fw-text-array-table">
    <?php foreach ( $fields as $field_name => $field_label ) : ?>
        <tr>
            <td><?php echo $field_label ?></td>
            <td>
                <input type="text" name="<?php echo $name ?>[<?php echo $field_name ?>]" id="<?php echo $id ?>_<?php echo $field_name ?>" value="<?php echo isset( $value[ $field_name ] ) ? esc_attr( $value[ $field_name ] ) : '' ?>"<?php echo $size ?> />
            </td>
        </tr>
    <?php endforeach ?>
</table>
