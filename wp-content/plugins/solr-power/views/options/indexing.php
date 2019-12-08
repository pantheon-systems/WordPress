<?php
/**
 * Renders the indexing tab
 *
 * @package Solr_Power
 */

?>
<div id="solr_indexing" class="solrtab">
	<div class="solr-power-subpage">
		<?php
		settings_fields( 'solr-power-index' );
		do_settings_sections( 'solr-power-index' );
		submit_button();
		?>
	</div>
</div>
