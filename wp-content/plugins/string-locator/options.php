<?php
$this_url = admin_url( ( is_multisite() ? 'network/admin.php' : 'tools.php' ) . '?page=string-locator' );

$search_string   = '';
$search_location = '';
$search_regex    = false;

if ( isset( $_POST['string-locator-string'] ) ) {
	$search_string = $_POST['string-locator-string'];
}
if ( isset( $_POST['string-locator-search'] ) ) {
	$search_location = $_POST['string-locator-search'];
}

if ( isset( $_GET['restore'] ) ) {
	$restore = unserialize( get_option( 'string-locator-search-overview' ) );

	$search_string   = $restore->search;
	$search_location = $restore->directory;
	$search_regex    = String_Locator::absbool( $restore->regex );
}
?>
<div class="wrap">
	<h2>
		<?php _e( 'String Locator', 'string-locator' ); ?>
	</h2>

	<form action="<?php echo esc_url( $this_url ); ?>" method="post" id="string-locator-search-form">
		<label for="string-locator-search"><?php _e( 'Search through', 'string-locator' ); ?></label>
		<select name="string-locator-search" id="string-locator-search">
			<optgroup label="<?php _e( 'Core', 'string-locator' ); ?>">
				<option value="core"><?php _e( 'The whole WordPress directory', 'string-locator' ); ?></option>
				<option value="wp-content"><?php _e( 'Everything under wp-content', 'string-locator' ); ?></option>
			</optgroup>
			<optgroup label="<?php _e( 'Themes', 'string-locator' ); ?>">
				<?php echo String_Locator::get_themes_options( $search_location ); ?>
			</optgroup>
			<?php if ( String_Locator::has_mu_plugins() ) : ?>
			<optgroup label="<?php _e( 'Must Use Plugins', 'string-locator' ); ?>">
				<?php echo String_Locator::get_mu_plugins_options( $search_location ); ?>
			</optgroup>
			<?php endif; ?>
			<optgroup label="<?php _e( 'Plugins', 'string-locator' ); ?>">
				<?php echo String_Locator::get_plugins_options( $search_location ); ?>
			</optgroup>
		</select>

		<label for="string-locator-string"><?php _e( 'Search string', 'string-locator' ); ?></label>
		<input type="text" name="string-locator-string" id="string-locator-string" value="<?php echo esc_attr( $search_string ); ?>" />

		<label><input type="checkbox" name="string-locator-regex" id="string-locator-regex"<?php echo ( $search_regex ? ' checked="checked"' : '' ); ?>'> RegEx search</label>

		<p>
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Search', 'string-locator' ); ?>">
			<a href="<?php echo esc_url( $this_url . '&restore=true' ); ?>" class="button button-primary"><?php _e( 'Restore last search', 'string-locator' ); ?></a>
		</p>
	</form>

	<div class="notices"></div>

	<div class="string-locator-feedback hide">
		<progress id="string-locator-search-progress" max="100"></progress>
		<span id="string-locator-feedback-text"><?php esc_html_e( 'Preparing search&hellip;', 'string-locator' ); ?></span>
	</div>

	<div class="table-wrapper">
		<?php
		if ( isset( $_GET['restore'] ) ) {
			$items = maybe_unserialize( get_option( 'string-locator-search-history', array() ) );

			echo String_Locator::prepare_full_table( $items, array( 'restore' ) );
		}
		else {
			echo String_Locator::prepare_full_table( array() );
		}
		?>
	</div>
</div>