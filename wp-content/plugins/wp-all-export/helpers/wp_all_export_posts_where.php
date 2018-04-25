<?php

function wp_all_export_posts_where($where)
{
	if ( ! empty(PMXE_Plugin::$session) and PMXE_Plugin::$session->has_session() ) 
	{				
		// manual export run
		$customWhere = PMXE_Plugin::$session->get('whereclause');		
		$where .= $customWhere;
	}
	else
	{
		// cron job execution
		if ( ! empty(XmlExportEngine::$exportOptions['whereclause']) ) $where .= XmlExportEngine::$exportOptions['whereclause'];		
	}

	return $where;
}