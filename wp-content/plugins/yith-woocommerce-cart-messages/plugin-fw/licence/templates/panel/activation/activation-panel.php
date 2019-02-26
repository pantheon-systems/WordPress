<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$to_active_products            = $this->get_to_active_products();
$activated_products            = $this->get_activated_products();
$no_active_products            = $this->get_no_active_licence_key();
$expired_products              = isset( $no_active_products[ '106' ] ) ? $no_active_products[ '106' ] : array();
$banned_products               = isset( $no_active_products[ '107' ] ) ? $no_active_products[ '107' ] : array();
$notice                        = isset( $notice ) ? $notice : '';
$notice_class                  = !empty( $notice ) ? 'notice notice-success visible' : 'notice notice-success';
$to_activate_check             = $this instanceof YIT_Theme_Licence ? 1 : 2;
$num_members_products_activate = $this->get_number_of_membership_products();
$debug                         = isset( $_REQUEST[ 'yith-license-debug' ] ) ? $_REQUEST[ 'yith-license-debug' ] : false;
?>

<div class="yit-container product-licence-activation">
    <h2><?php _e( 'YITH License Activation', 'yith-plugin-fw' ) ?></h2>

    <div class="licence-check-section">
        <form method="post" id="licence-check-update" action="<?php echo admin_url( 'admin-ajax.php' ) ?>">
            <span class="licence-label" style="display: block;"><?php _e( 'Have you updated your licenses? Have you asked for an extension? Update information concerning your products.', 'yith-plugin-fw' ); ?></span>
            <input type="hidden" name="action" value="yith_update_licence_information-<?php echo $this->get_product_type(); ?>"/>
            <input type="submit" name="submit" value="<?php _e( 'Update license information', 'yith-plugin-fw' ) ?>" class="button-licence licence-check"/>
            <div class="spinner"></div>
        </form>
    </div>

    <div id="yith-licence-notice" class="<?php echo $notice_class ?>">
        <p class="yith-licence-notice-message"><?php echo $notice ?></p>
    </div>

    <!-- To Active Products -->

    <?php if ( !empty( $to_active_products ) ) : ?>
        <h3 id="products-to-active" class="to-active">
            <?php echo _n( 'Product to activate', 'Products to activate', $to_activate_check, 'yith-plugin-fw' ) ?>
            <span class="spinner"></span>
        </h3>
        <div class="to-active-wrapper">
            <?php foreach ( $to_active_products as $init => $info ) : ?>
                <form class="to-active-form" method="post" id="<?php echo $info[ 'product_id' ] ?>" action="<?php echo admin_url( 'admin-ajax.php' ) ?>">
                    <?php if ( $debug ): ?>
                        <input type="hidden" name="debug" value="<?php echo $debug ?>"/>
                    <?php endif ?>
                    <table class="to-active-table">
                        <tbody>
                        <tr class="product-row">
                            <td class="product-name">
                                <?php echo $this->display_product_name( $info[ 'Name' ] ) ?>
                            </td>
                            <td>
                                <input type="email" name="email" placeholder="Your email on yithemes.com" value="" class="user-email"/>
                            </td>
                            <td>
                                <input type="text" name="licence_key" placeholder="License Key" value="" class="licence-key"/>
                            </td>
                            <td class="activate-button">
                                <input type="submit" name="submit" value="<?php _e( 'Activate', 'yith-plugin-fw' ) ?>" class="button-licence licence-activation" data-formid="<?php echo $info[ 'product_id' ] ?>"/>
                            </td>
                        </tr>
                        <input type="hidden" name="action" value="yith_activate-<?php echo $this->get_product_type(); ?>"/>
                        <input type="hidden" name="product_init" value="<?php echo $init ?>"/>
                        </tbody>
                    </table>
                    <div class="message-wrapper">
                        <span class="message arrow-left"></span>
                    </div>
                </form>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Activated Products -->

    <?php if ( !empty( $activated_products ) ) : ?>
        <h3 id="activated-products">
            <?php _e( 'Activated', 'yith-plugin-fw' ) ?>
            <span class="spinner"></span>
        </h3>
        <table class="activated-table">
            <thead>
            <tr>
                <th><?php _e( 'Product Name', 'yith-plugin-fw' ) ?></th>
                <?php if ( $this->show_extra_info ) : ?>
                    <th><?php _e( 'Email', 'yith-plugin-fw' ) ?></th>
                    <th><?php _e( 'License Key', 'yith-plugin-fw' ) ?></th>
                <?php endif; ?>

                <th><?php _e( 'Expires', 'yith-plugin-fw' ) ?></th>

                <?php if ( $this->show_extra_info ) : ?>
                    <th><?php _e( 'Remaining', 'yith-plugin-fw' ) ?></th>
                    <?php if ( $num_members_products_activate ) : ?>
                        <th><?php _e( 'Club Subscription', 'yith-plugin-fw' ) ?></th>
                    <?php endif; ?>
                <?php endif; ?>

                <th><?php _e( 'License Actions', 'yith-plugin-fw' ) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ( $activated_products as $init => $info ) : ?>
                <tr>
                    <td class="product-name">
                        <?php echo $this->display_product_name( $info[ 'Name' ] ) ?>
                    </td>

                    <?php if ( $this->show_extra_info ) : ?>
                        <td class="product-licence-email">
                            <?php echo $info[ 'licence' ][ 'email' ] ?>
                        </td>
                        <td class="product-licence-key">
                            <?php echo $info[ 'licence' ][ 'licence_key' ] ?>
                        </td>
                    <?php endif; ?>

                    <td class="product-licence-expires">
                        <?php echo date( "F j, Y", $info[ 'licence' ][ 'licence_expires' ] ); ?>
                    </td>

                    <?php if ( $this->show_extra_info ) : ?>
                        <td class="product-licence-remaining">
                            <?php printf( __( '%1s out of %2s', 'yith-plugin-fw' ), $info[ 'licence' ][ 'activation_remaining' ], $info[ 'licence' ][ 'activation_limit' ] ); ?>
                        </td>
                        <?php if ( $num_members_products_activate ) : ?>
                            <td class="product-licence-membership">
                                <span class="dashicons dashicons-<?php echo $info[ 'licence' ][ 'is_membership' ] ? 'yes' : 'no-alt' ?>"></span>
                            </td>
                        <?php endif; ?>
                    <?php endif; ?>

                    <td>
                        <a class="button-licence licence-deactive"
                           href="#"
                           data-licence-email="<?php echo $info[ 'licence' ][ 'email' ] ?>"
                           data-licence-key="<?php echo $info[ 'licence' ][ 'licence_key' ] ?>"
                           data-product-init="<?php echo $init ?>"
                           data-action="yith_deactivate-<?php echo $this->get_product_type(); ?>">
                            <?php _e( 'Deactivate', 'yith-plugin-fw' ) ?>
                        </a>

                        <?php if ( !$info[ 'licence' ][ 'is_membership' ] && $this->show_renew_button ) : ?>
                            <a class="button-licence licence-renew" href="<?php echo esc_url( $this->get_renewing_uri( $info[ 'licence' ][ 'licence_key' ] ) ) ?>" target="_blank">
                                <?php _e( 'Renew', 'yith-plugin-fw' ) ?>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Banned Products -->

    <?php if ( !empty( $banned_products ) ) : ?>
        <h3><?php _e( 'Banned', 'yith-plugin-fw' ) ?></h3>
        <table class="expired-table">
            <thead>
            <tr>
                <th><?php _e( 'Product Name', 'yith-plugin-fw' ) ?></th>
                <?php if ( $this->show_extra_info ) : ?>
                    <th><?php _e( 'Email', 'yith-plugin-fw' ) ?></th>
                    <th><?php _e( 'License Key', 'yith-plugin-fw' ) ?></th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ( $banned_products as $init => $info ) : ?>
                <tr>
                    <td class="product-name">
                        <?php echo $this->display_product_name( $info[ 'Name' ] ) ?>
                    </td>
                    <?php if ( $this->show_extra_info ) : ?>
                        <td class="product-licence-email"><?php echo $info[ 'licence' ][ 'email' ] ?></td>
                        <td class="product-licence-key"><?php echo $info[ 'licence' ][ 'licence_key' ] ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Expired Products -->

    <?php if ( !empty( $expired_products ) ) : ?>
        <h3><?php _e( 'Expired', 'yith-plugin-fw' ) ?></h3>
        <table class="expired-table">
            <thead>
            <tr>
                <th><?php _e( 'Product Name', 'yith-plugin-fw' ) ?></th>

                <?php if ( $this->show_extra_info ) : ?>
                    <th><?php _e( 'Email', 'yith-plugin-fw' ) ?></th>
                    <th><?php _e( 'License Key', 'yith-plugin-fw' ) ?></th>
                <?php endif; ?>

                <th><?php _e( 'Expires', 'yith-plugin-fw' ) ?></th>

                <?php if ( $this->show_renew_button ) : ?>
                    <th><?php _e( 'Renew', 'yith-plugin-fw' ) ?></th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ( $expired_products as $init => $info ) : ?>
                <tr>
                    <td class="product-name">
                        <?php echo $this->display_product_name( $info[ 'Name' ] ) ?>
                    </td>

                    <?php if ( $this->show_extra_info ) : ?>
                        <td class="product-licence-email"><?php echo $info[ 'licence' ][ 'email' ] ?></td>
                        <td class="product-licence-key"><?php echo $info[ 'licence' ][ 'licence_key' ] ?></td>
                    <?php endif; ?>

                    <td class="product-licence-expires"><?php echo date( "F j, Y", $info[ 'licence' ][ 'licence_expires' ] ); ?></td>

                    <?php if ( $this->show_renew_button ) : ?>
                        <td>
                            <a class="button-licence licence-renew" href="<?php echo $this->get_renewing_uri( $info[ 'licence' ][ 'licence_key' ] ) ?>" target="_blank">
                                <?php if ( $info[ 'licence' ][ 'is_membership' ] ) : ?>
                                    <?php _e( 'Order again', 'yith-plugin-fw' ) ?>
                                <?php else : ?>
                                    <?php __( 'Renew license', 'yith-plugin-fw' ) ?>
                                <?php endif; ?>
                            </a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>