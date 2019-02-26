<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @var array  $args
 * @var string $custom_attributes
 *
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly
?>

<input
        type="hidden"
        id="<?php echo $args[ 'id' ] ?>"
        class="<?php echo $args[ 'class' ] ?>"
        name="<?php echo $args[ 'name' ] ?>"
        data-placeholder="<?php echo $args[ 'data-placeholder' ] ?>"
        data-allow_clear="<?php echo $args[ 'data-allow_clear' ] ?>"
        data-selected="<?php echo is_array( $args[ 'data-selected' ] ) ? esc_attr( json_encode( $args[ 'data-selected' ] ) ) : esc_attr( $args[ 'data-selected' ] ) ?>"
        data-multiple="<?php echo $args[ 'data-multiple' ] === true ? 'true' : 'false' ?>"
    <?php echo( !empty( $args[ 'data-action' ] ) ? 'data-action="' . $args[ 'data-action' ] . '"' : '' ) ?>
        value="<?php echo $args[ 'value' ] ?>"
        style="<?php echo $args[ 'style' ] ?>"
    <?php echo $custom_attributes ?>
/>
