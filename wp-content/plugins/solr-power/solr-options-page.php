<?php
/**
 * Solr Power options page
 *
 * @package Solr_Power
 */

?>
<div id="solr_header">
	<h2>Solr Power <span>Version <?php echo esc_html( SOLR_POWER_VERSION ); ?></span></h2>
</div>
<?php
// Load up options.
$s4wp_settings = solr_options();

// Display a message if one is set.
if ( ! is_null( SolrPower_Options::get_instance()->msg ) ) {
	?>
	<div id="message">
		<p>
			<strong><?php echo wp_kses_post( SolrPower_Options::get_instance()->msg ); ?></strong>
		</p>
	</div>
	<?php
}
?>

<div class="wrap">
	<div class="solr-power-subpage">

		<h2 class="nav-tab-wrapper" id="solr-tabs">

			<a class="nav-tab <?php echo ( ! isset( $_GET['settings-updated'] ) ) ? 'nav-tab-active' : ''; ?>" id="solr_info-tab" href="#top#solr_info"><span class="dashicons dashicons-info"></span>
				<?php esc_html_e( 'Info', 'solr-for-wordpress-on-pantheon' ); ?>
			</a>
			<a class="nav-tab" id="solr_action-tab" href="#top#solr_action"><span class="dashicons dashicons-performance"></span>
				<?php esc_html_e( 'Actions', 'solr-for-wordpress-on-pantheon' ); ?>
			</a>
			<a class="nav-tab" id="solr_indexing-tab" href="#top#solr_indexing"><span class="dashicons dashicons-admin-page"></span>
				<?php esc_html_e( 'Indexing Options', 'solr-for-wordpress-on-pantheon' ); ?>
			</a>
			<a class="nav-tab" id="solr_facet-tab" href="#top#solr_facet"><span class="dashicons dashicons-forms"></span>
				<?php esc_html_e( 'Facet Options', 'solr-for-wordpress-on-pantheon' ); ?>
			</a>
		</h2>


		<?php
		$action = self_admin_url( 'admin.php?page=solr-power' );
		include 'views/options/info.php';
		include 'views/options/action.php';

		$options_action = is_multisite() ? network_admin_url( 'settings.php' ) : admin_url( 'options.php' );
		?>
		<form method="post" action="<?php echo esc_url( $options_action ); ?>">
		<?php
		include 'views/options/indexing.php';
		include 'views/options/facet.php';
		?>
		</form>
	</div>
</div>
