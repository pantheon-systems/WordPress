<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @var array $field
 */

extract( $field );

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

wp_enqueue_style( 'font-awesome' );
extract( $field );

$filter_icons      = !empty( $field[ 'filter_icons' ] ) ? $field[ 'filter_icons' ] : '';
$default_icon_text = isset( $std ) ? $std : false;
$default_icon_data = YIT_Icons()->get_icon_data( $default_icon_text, $filter_icons );

$current_icon_data = YIT_Icons()->get_icon_data( $value, $filter_icons );
$current_icon_text = $value;

$yit_icons = YIT_Icons()->get_icons( $filter_icons );
?>

<div id="yit-icons-manager-wrapper-<?php echo $id ?>" class="yit-icons-manager-wrapper">

    <div class="yit-icons-manager-text">
        <div class="yit-icons-manager-icon-preview" <?php echo $current_icon_data ?>></div>
        <input class="yit-icons-manager-icon-text" type="text" id="<?php echo $id ?>" name="<?php echo $name ?>" value="<?php echo $current_icon_text; ?>"/>
        <div class="clear"></div>
    </div>


    <div class="yit-icons-manager-list-wrapper">
        <ul class="yit-icons-manager-list">
            <?php foreach ( $yit_icons as $font => $icons ):
                foreach ( $icons as $key => $icon_name ):
                    $icon_text = $font . ':' . $icon_name;
                    $icon_class = $icon_text == $current_icon_text ? 'active' : '';
                    $icon_class .= $icon_text == $default_icon_text ? ' default' : '';
                    $data_icon = str_replace( '\\', '&#x', $key );
                    ?>
                    <li class="<?php echo $icon_class ?>" data-font="<?php echo $font ?>" data-icon="<?php echo $data_icon; ?>" data-key="<?php echo $key ?>"
                        data-name="<?php echo $icon_name ?>"></li>
                    <?php
                endforeach;
            endforeach; ?>
        </ul>
    </div>

    <div class="yit-icons-manager-actions">
        <?php if ( $default_icon_text ): ?>
            <div class="yit-icons-manager-action-set-default button"><?php _e( 'Set Default', 'yith-plugin-fw' ) ?><i
                    class="yit-icons-manager-default-icon-preview" <?php echo $default_icon_data ?>></i></div>
        <?php endif ?>
    </div>

</div>
