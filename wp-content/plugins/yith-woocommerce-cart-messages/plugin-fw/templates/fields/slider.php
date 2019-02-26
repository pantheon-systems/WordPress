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

$min = isset( $option[ 'min' ] ) ? $option[ 'min' ] : 0;
$max = isset( $option[ 'max' ] ) ? $option[ 'max' ] : 100;
?>
<div class="yith-plugin-fw-slider-container">
    <div class="ui-slider">
        <span class="minCaption"><?php echo $min ?></span>
        <div id="<?php echo $id ?>-div" data-step="<?php echo isset( $step ) ? $step : 1 ?>" data-min="<?php echo $min ?>" data-max="<?php echo $max ?>" data-val="<?php echo $value; ?>" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
            <input id="<?php echo $id ?>" type="hidden" name="<?php echo $name ?>" value="<?php echo esc_attr( $value ); ?>"/>
        </div>
        <span class="maxCaption"><?php echo $max ?></span>
    </div>
</div>