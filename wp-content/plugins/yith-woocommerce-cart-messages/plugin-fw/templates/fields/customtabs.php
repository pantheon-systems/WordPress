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

$field[ 'labels' ] = array(
    'plural_name'    => 'Tabs',
    'singular_name'  => 'Tab',
    'item_name_sing' => 'Tab',
    'item_name_plur' => 'Tabs',
);
$value             = is_array( $value ) ? $value : array();
?>
<div id="yit_custom_tabs" class="panel wc-metaboxes-wrapper" style="display: block;">
    <p class="toolbar">
        <a href="#" class="close_all"><?php _e( 'Close all', 'yith-plugin-fw' ) ?></a><a href="#" class="expand_all"><?php _e( 'Expand all', 'yith-plugin-fw' ) ?></a>
    </p>

    <div class="yit_custom_tabs wc-metaboxes ui-sortable" style="">

        <?php if ( !empty( $value ) ): ?>
            <?php foreach ( $value as $i => $tab ): ?>
                <div class="yit_custom_tab wc-metabox closed" rel="0">
                    <h3>
                        <button type="button" class="remove_row button"><?php _e( 'Remove', 'yith-plugin-fw' ) ?></button>
                        <div class="handlediv" title="Click to toggle"></div>
                        <strong class="attribute_name"><?php echo $tab[ 'name' ] ?></strong>
                    </h3>

                    <table cellpadding="0" cellspacing="0" class="woocommerce_attribute_data wc-metabox-content" style="display: table;">
                        <tbody>
                        <tr>
                            <td class="attribute_name">
                                <label><?php _e( 'Name', 'yith-plugin-fw' ) ?>:</label>
                                <input type="text" class="attribute_name" name="<?php echo $name ?>[<?php echo $i ?>][name]" value="<?php echo esc_attr( $tab[ 'name' ] ) ?>">
                                <input type="hidden" name="<?php echo $name ?>[<?php echo $i ?>][position]" class="attribute_position" value="<?php echo $i ?>">
                            </td>

                            <td rowspan="3">
                                <label><?php _e( 'Value', 'yith-plugin-fw' ) ?>:</label>
                                <textarea name="<?php echo $name ?>[<?php echo $i ?>][value]" cols="5" rows="5" placeholder="<?php _e( 'Content of the tab. (HTML is supported)', 'yith-plugin-fw' ) ?>"><?php echo $tab[ 'value' ] ?></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            <?php endforeach ?>
        <?php endif ?>
    </div>

    <p class="toolbar">
        <button type="button" class="button button-primary add_custom_tab"><?php _e( 'Add custom product tab', 'yith-plugin-fw' ) ?></button>
    </p>

    <div class="clear"></div>
</div>

<script>
    jQuery( document ).ready( function ( $ ) {
        // Add rows
        $( 'button.add_custom_tab' ).on( 'click', function () {

            var size = $( '.yit_custom_tabs .yit_custom_tab' ).size() + 1;

            // Add custom attribute row
            $( '.yit_custom_tabs' ).append( '<div class="yit_custom_tab wc-metabox">\
						<h3>\
							<button type="button" class="remove_row button"><?php _e( 'Remove', 'yith-plugin-fw' ) ?></button>\
							<div class="handlediv" title="Click to toggle"></div>\
							<strong class="attribute_name"></strong>\
						</h3>\
						<table cellpadding="0" cellspacing="0" class="woocommerce_attribute_data">\
							<tbody>\
								<tr>\
									<td class="attribute_name">\
										<label><?php _e( 'Name', 'yith-plugin-fw' ) ?>:</label>\
										<input type="text" class="attribute_name" name="<?php echo $name ?>[' + size + '][name]" />\
										<input type="hidden" name="<?php echo $name ?>[' + size + '][position]" class="attribute_position" value="' + size + '" />\
									</td>\
									<td rowspan="3">\
										<label><?php _e( 'Value', 'yith-plugin-fw' ) ?>:</label>\
										<textarea name="<?php echo $name ?>[' + size + '][value]" cols="5" rows="5" placeholder="<?php echo addslashes( __( 'Content of the tab. (HTML is supported)', 'yith-plugin-fw' ) ) ?>"></textarea>\
									</td>\
								</tr>\
							</tbody>\
						</table>\
					</div>' );

        } );


        $( '.yit_custom_tabs' ).on( 'click', 'button.remove_row', function () {
            var answer = confirm( "<?php _e( 'Do you want to remove the custom tab?', 'yith-plugin-fw' ) ?>" );
            if ( answer ) {
                var $parent = $( this ).parent().parent();

                $parent.remove();
                attribute_row_indexes();
            }
            return false;
        } );

        // Attribute ordering
        $( '.yit_custom_tabs' ).sortable( {
                                              items               : '.yit_custom_tab',
                                              cursor              : 'move',
                                              axis                : 'y',
                                              handle              : 'h3',
                                              scrollSensitivity   : 40,
                                              forcePlaceholderSize: true,
                                              helper              : 'clone',
                                              opacity             : 0.65,
                                              placeholder         : 'wc-metabox-sortable-placeholder',
                                              start               : function ( event, ui ) {
                                                  ui.item.css( 'background-color', '#f6f6f6' );
                                              },
                                              stop                : function ( event, ui ) {
                                                  ui.item.removeAttr( 'style' );
                                                  attribute_row_indexes();
                                              }
                                          } );

        function attribute_row_indexes() {
            $( '.yit_custom_tabs .yit_custom_tab' ).each( function ( index, el ) {
                var newVal = '[' + $( el ).index( '.yit_custom_tabs .yit_custom_tab' ) + ']';
                var oldVal = '[' + $( '.attribute_position', el ).val() + ']';

                $( ':input:not(button)', el ).each( function () {
                    var name = $( this ).attr( 'name' );
                    $( this ).attr( 'name', name.replace( oldVal, newVal ) );
                } );

                $( '.attribute_position', el ).val( $( el ).index( '.yit_custom_tabs .yit_custom_tab' ) );
            } );
        }

    } );
</script>