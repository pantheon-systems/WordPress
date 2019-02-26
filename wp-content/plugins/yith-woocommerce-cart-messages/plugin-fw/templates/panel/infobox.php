<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 *        'section_general_settings_boxinfo'         => array(
 *            'name' => __( 'General information', 'yith-plugin-fw' ),
 *            'type' => 'boxinfo',
 *            'default' => array(
 *                'plugin_name' => __( 'Plugin Name', 'yith-plugin-fw' ),
 *                'buy_url' => 'http://www.yithemes.com',
 *                'demo_url' => 'http://plugins.yithemes.com/demo-url/'
 *            ),
 *            'id'   => 'yith_wcas_general_boxinfo'
 *        ),
 */
?>
<div id="<?php echo $id ?>" class="meta-box-sortables">
    <div id="<?php echo $id ?>-content-panel" class="postbox " style="display: block;">
        <h3><?php echo $name ?></h3>
        <div class="inside">
            <p>Lorem ipsum ... </p>
            <p class="submit"><a href="<?php echo $default['buy_url'] ?>" class="button-primary">Buy Plugin</a></p>
        </div>
    </div>
</div>