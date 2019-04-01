<?php
/**
 * Creatives Admin
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Creatives
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/creatives/screen-options.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/creatives/class-list-table.php';

function affwp_creatives_admin() {

	if ( isset( $_GET['action'] ) && 'view_creative' == $_GET['action'] ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/creatives/view.php';

	} else if ( isset( $_GET['action'] ) && 'add_creative' == $_GET['action'] ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/creatives/new.php';

	} else if ( isset( $_GET['action'] ) && 'edit_creative' == $_GET['action'] ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/creatives/edit.php';

	} else if( isset( $_GET['action'] ) && 'delete' == $_GET['action'] ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/creatives/delete.php';

	} else {

		$creatives_table = new AffWP_Creatives_Table();
		$creatives_table->prepare_items();
	?>
	<div class="wrap">
			<h2><?php _e( 'Creatives', 'affiliate-wp' ); ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'affwp_notice' => false, 'action' => 'add_creative' ) ) ); ?>" class="add-new-h2"><?php _e( 'Add New', 'affiliate-wp' ); ?></a>
			</h2>
			<?php
			/**
			 * Fires at the top of the creatives admin screen.
			 */
			do_action( 'affwp_creatives_page_top' );
			?>

			<form id="affwp-creatives-filter" method="get" action="<?php echo esc_url( affwp_admin_url( 'creatives' ) ); ?>">

				<input type="hidden" name="page" value="affiliate-wp-creatives" />

				<?php $creatives_table->views() ?>
				<?php $creatives_table->display() ?>
			</form>

			<?php
			/**
			 * Fires at the bottom of the creatives admin screen.
			 */
			do_action( 'affwp_creatives_page_bottom' );
			?>
		</div>

<?php
	}
}
