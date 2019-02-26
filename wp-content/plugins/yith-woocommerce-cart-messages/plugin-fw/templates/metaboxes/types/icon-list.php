<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Awesome Icon Admin View
 *
 * @package    YITH
 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
 * @since 1.0.0
 */

extract( $args );


if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$current_options = wp_parse_args( $args[ 'value' ], $args[ 'std' ] );
$current_icon    = YIT_Icon()->get_icon_data( $current_options[ 'icon' ] );
$std_icon        = YIT_Icon()->get_icon_data( $std[ 'icon' ] );

$options[ 'icon' ] = YIT_Plugin_Common::get_icon_list();

?>


<div id="<?php echo $id ?>-container" class="select_icon rm_option rm_input rm_text" <?php echo yith_field_deps_data( $args ); ?>>
    <div id="<?php echo $id ?>-container" <?php echo yith_field_deps_data( $args ); ?>>

        <label for="<?php echo $id ?>"><?php echo $label ?></label>

        <div class="option">
            <div class="select_wrapper icon_list_type clearfix">
                <select name="<?php echo $name ?>[select]" id="<?php echo $id ?>[select]" <?php if ( isset( $std[ 'select' ] ) ) : ?>data-std="<?php echo $std[ 'select' ]; ?>"<?php endif; ?>>
                    <?php foreach ( $options[ 'select' ] as $val => $option ) : ?>
                        <option value="<?php echo $val ?>" <?php selected( $current_options[ 'select' ], $val ); ?> ><?php echo $option ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="icon-manager-wrapper">
                <div class="icon-manager-text">
                    <div class="icon-preview" <?php echo $current_icon ?>></div>
                    <input type="text" id="<?php echo $id ?>[icon]" class="icon-text" name="<?php echo $name ?>[icon]" value="<?php echo $current_options[ 'icon' ]; ?>"/>
                </div>


                <div class="icon-manager">
                    <ul class="icon-list-wrapper">
                        <?php foreach ( $options[ 'icon' ] as $font => $icons ):
                            foreach ( $icons as $key => $icon ): ?>
                                <li data-font="<?php echo $font ?>" data-icon="<?php echo ( strpos( $key, '\\' ) === 0 ) ? '&#x' . substr( $key, 1 ) : $key ?>" data-key="<?php echo $key ?>" data-name="<?php echo $icon ?>"></li>
                                <?php
                            endforeach;
                        endforeach; ?>
                    </ul>
                </div>
            </div>


            <div class="input_wrapper custom_icon_wrapper upload" style="clear:both;">
                <input type="text" name="<?php echo $name ?>[custom]" id="<?php echo $id ?>[custom]" value="<?php echo $current_options[ 'custom' ] ?>" class="yith-plugin-fw-upload-img-url upload_custom_icon"/>
                <input type="button" value="<?php _e( 'Upload', 'yith-plugin-fw' ) ?>" id="<?php echo $id; ?>-custom-button" class="yith-plugin-fw-upload-button button"/>

                <div class="yith-plugin-fw-upload-img-preview" style="margin-top:10px;">
                    <?php
                    $file = $current_options[ 'custom' ];
                    if ( preg_match( '/(jpg|jpeg|png|gif|ico)$/', $file ) ) {
                        echo __( 'Image preview', 'yith-plugin-fw' ) . ': ' . "<img src=\"" . YIT_CORE_ASSETS_URL . "/images/sleep.png\" data-src=\"$file\" />";
                    }
                    ?>
                </div>
            </div>

        </div>

        <div class="clear"></div>


        <div class="description">
            <?php echo $desc ?>
            <?php if ( $std[ 'select' ] == 'custom' ) : ?>
                <?php printf( __( '(Default: %s <img src="%s"/>)', 'yith-plugin-fw' ), $options[ 'select' ][ 'custom' ], $std[ 'custom' ] ) ?>
            <?php else: ?>
                <?php printf( __( '(Default: <i %s></i> )', 'yith-plugin-fw' ), $std_icon ) ?>
            <?php endif; ?>
        </div>

        <div class="clear"></div>

    </div>
</div>

<script>

    jQuery( document ).ready( function ( $ ) {

        $( '.select_wrapper.icon_list_type' ).on( 'change', function () {

            var t       = $( this );
            var parents = $( '#' + t.parents( 'div.select_icon' ).attr( 'id' ) );
            var option  = $( 'option:selected', this ).val();
            var to_show = option == 'none' ? '' : option == 'icon' ? '.icon-manager-wrapper' : '.custom_icon_wrapper';

            parents.find( '.option > div:not(.icon_list_type)' ).removeClass( 'show' ).addClass( 'hidden' );
            parents.find( to_show ).removeClass( 'hidden' ).addClass( 'show' );
        } );

        $( '.select_wrapper.icon_list_type' ).trigger( 'change' );

        var $icon_list    = $( '.select_icon' ).find( 'ul.icon-list-wrapper' ),
            $preview      = $( '.icon-preview' ),
            $element_list = $icon_list.find( 'li' ),
            $icon_text    = $( '.icon-text' );

        $element_list.on( "click", function () {
            var $t = $( this );
            $element_list.removeClass( 'active' );
            $t.addClass( 'active' );
            $preview.attr( 'data-font', $t.data( 'font' ) );
            $preview.attr( 'data-icon', $t.data( 'icon' ) );
            $preview.attr( 'data-name', $t.data( 'name' ) );
            $preview.attr( 'data-key', $t.data( 'key' ) );

            $icon_text.val( $t.data( 'font' ) + ':' + $t.data( 'name' ) );

        } );
    } );

</script>