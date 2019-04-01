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

/** @since 3.0.13 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

extract( $field );

$class = isset( $class ) ? $class : '';
$class = 'yith-plugin-fw-radio ' . $class;
?>
<div class="<?php echo $class ?>" id="<?php echo $id ?>"
    <?php echo $custom_attributes ?>
    <?php if ( isset( $data ) ) echo yith_plugin_fw_html_data_to_string( $data ); ?> value="<?php echo $value ?>">
    <?php foreach ( $options as $key => $label ) :
        $radio_id = sanitize_key( $id . '-' . $key );
        ?>
        <div class="yith-plugin-fw-radio__row">
            <input type="radio" id="<?php echo $radio_id ?>" name="<?php echo $name ?>" value="<?php echo esc_attr( $key ) ?>" <?php checked( $key, $value ); ?> />
            <label for="<?php echo $radio_id ?>"><?php echo $label ?></label>
        </div>
    <?php endforeach; ?>
</div>
