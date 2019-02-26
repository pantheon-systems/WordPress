<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @var array $args
 * @var string $custom_attributes
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly
?>

<select
        id="<?php echo $args[ 'id' ] ?>"
        class="<?php echo $args[ 'class' ] ?>"
        name="<?php echo $args[ 'name' ] ?>"
        data-placeholder="<?php echo $args[ 'data-placeholder' ] ?>"
        data-allow_clear="<?php echo $args[ 'data-allow_clear' ] ?>"
    <?php echo !empty( $args[ 'data-action' ] ) ? 'data-action="' . $args[ 'data-action' ] . '"' : ''; ?>
    <?php echo !empty( $args[ 'data-multiple' ] ) ? 'multiple="multiple"' : ''; ?>
        style="<?php echo $args[ 'style' ] ?>"
    <?php echo $custom_attributes ?>
>

    <?php if ( !empty( $args[ 'value' ] ) ) {
        $values = $args[ 'value' ];

        if ( !is_array( $values ) ) {
            $values = explode( ',', $values );
        }

        foreach ( $values as $value ): ?>
            <option value="<?php echo $value; ?>" <?php selected( true, true, true ) ?> >
                <?php echo $args[ 'data-selected' ][ $value ]; ?>
            </option>
        <?php endforeach;
    }
    ?>
</select>
