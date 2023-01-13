<?php
$oe_library_active_status = get_option( 'oe_library_active_status', 'yes' );
?>

<div id="ocean-library-disable-bulk" class="oceanwp-tp-switcher column-wrap">
	<label for="oceanwp-switch-library-disable" class="column-name">
		<input type="checkbox" role="checkbox" name="library-disable-bulk" value="true" id="oceanwp-switch-library-disable" <?php checked( $oe_library_active_status == 'yes' ); ?> />
		<span class="slider round"></span>
	</label>
</div>
