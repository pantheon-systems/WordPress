<?php
/**
 * WooCommerce Plugin Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Plugin/Gateway/Admin/Views
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
?>

<tr>

	<th><?php echo esc_html( $title ); ?></th>

	<td class="forminp">

		<table class="sv_wc_payment_gateway_token_editor widefat" data-gateway-id="<?php echo esc_attr( $id ); ?>">

			<thead>
				<tr>

					<?php // Display a column for each token field
					foreach ( $columns as $column_id => $column_title ) : ?>
						<th class="token-<?php echo esc_attr( $column_id ); ?>"><?php echo esc_html( $column_title ); ?></th>
					<?php endforeach; ?>

				</tr>
			</thead>

			<tbody class="tokens">

				<?php
				/** Fire inside the payment gateway token editor.
				 *
				 * @since 4.3.0
				 * @param int $user_id the current user ID
				 */
				do_action( 'wc_payment_gateway_' . $id . '_token_editor_tokens', $user_id ); ?>

			</tbody>

			<tbody class="meta">
				<tr class="no-tokens">
					<td colspan="<?php echo count( $columns ); ?>"><?php esc_html_e( 'No saved payment tokens', 'woocommerce-plugin-framework' ); ?></td>
				</tr>
			</tbody>

			<?php // Editor actions
			if ( ! empty( $actions ) ) : ?>

				<tfoot>
					<tr>
						<th class="actions" colspan="<?php echo count( $columns ); ?>">

							<?php foreach ( $actions as $action => $label ) : ?>

									<?php $button_class = 'save' === $action ? 'button-primary' : 'button'; ?>

									<button class="sv-wc-payment-gateway-token-editor-action-button <?php echo sanitize_html_class( $button_class ); ?>" data-action="<?php echo esc_attr( $action ); ?>" data-user-id="<?php echo esc_attr( $user_id ); ?>">
										<?php echo esc_attr( $label ); ?>
									</button>

							<?php endforeach; ?>

						</th>
					</tr>
				</tfoot>

			<?php endif; ?>

		</table>

	</td>

</tr>
