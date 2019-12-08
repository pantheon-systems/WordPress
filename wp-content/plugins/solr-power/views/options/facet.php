<?php
/**
 * Renders the facet tab
 *
 * @package Solr_Power
 */

?>
<div id="solr_facet" class="solrtab">
	<div class="solr-power-subpage">
		<?php
		settings_fields( 'solr-power-facet' );
		do_settings_sections( 'solr-power-facet' );
		submit_button();
		?>
	</div>
</div>
