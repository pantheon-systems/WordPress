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

$class      = isset( $class ) ? $class : 'yith-plugin-fw-select-images';
$wrapper_id = $id . '-wrapper';
?>
<div id="<?php echo $wrapper_id ?>" class="yith-plugin-fw-select-images__wrapper">

    <select id="<?php echo $id ?>"
            name="<?php echo $name ?>"
            class="<?php echo $class ?>"
            style="display: none"
        <?php echo $custom_attributes ?>
        <?php if ( isset( $data ) ) echo yith_plugin_fw_html_data_to_string( $data ); ?>>
        <?php foreach ( $options as $key => $item ) :
            $label = !empty( $item[ 'label' ] ) ? $item[ 'label' ] : $key;
            ?>
            <option value="<?php echo esc_attr( $key ) ?>" <?php selected( $key, $value ); ?> ><?php echo $label ?></option>
        <?php endforeach; ?>
    </select>

    <ul class="yith-plugin-fw-select-images__list">
        <?php foreach ( $options as $key => $item ) :
            $label = !empty( $item[ 'label' ] ) ? $item[ 'label' ] : $key;
            $image = !empty( $item[ 'image' ] ) ? $item[ 'image' ] : '';
            if ( $image ) :
                $selected_class = 'yith-plugin-fw-select-images__item--selected';
                $current_class = $key === $value ? $selected_class : '';
                ?>
                <li class="yith-plugin-fw-select-images__item <?php echo $current_class ?>" data-key="<?php echo $key ?>">
                    <?php if ( $label ) : ?>
                        <div class="yith-plugin-fw-select-images__item__label"><?php echo $label ?></div>
                    <?php endif; ?>
                    <img src="<?php echo $image ?>">
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>