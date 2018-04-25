<?php
function pmxi_pmxi_before_xml_import( $import_id )
{
	delete_option('wp_all_import_taxonomies_hierarchy_' . $import_id);
}