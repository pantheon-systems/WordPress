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

// wp_editor is includes since 3.3 of wordpress
if ( !function_exists( 'wp_editor' ) ) :
    include 'textarea.php';
else:
    extract( $field );

    $editor_args = wp_parse_args( $field, array(
        'wpautop'       => true, // use wpautop?
        'media_buttons' => true, // show insert/upload button(s)
        'textarea_name' => $name, // set the textarea name to something different, square brackets [] can be used here
        'textarea_rows' => 20, // rows="..."
        'tabindex'      => '',
        'editor_css'    => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
        'editor_class'  => '', // add extra class(es) to the editor textarea
        'teeny'         => false, // output the minimal editor config used in Press This
        'dfw'           => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
        'tinymce'       => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
        'quicktags'     => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
    ) );
    ?>
    <div class="editor" <?php echo $custom_attributes ?>
        <?php if ( isset( $data ) ) echo yith_plugin_fw_html_data_to_string( $data ); ?>><?php wp_editor( $value, $id, $editor_args ); ?></div>

<?php endif; ?>