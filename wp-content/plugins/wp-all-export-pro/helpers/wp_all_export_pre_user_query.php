<?php

function wp_all_export_pre_user_query($obj)
{
	if ( ! empty(PMXE_Plugin::$session) and PMXE_Plugin::$session->has_session() ) 
	{
		// manual export run
		$customWhere = PMXE_Plugin::$session->get('whereclause');
		$obj->query_where .= $customWhere;

		$customJoin = PMXE_Plugin::$session->get('joinclause');

        if ( ! empty( $customJoin ) ) {
            foreach ($customJoin as $join) {
                $obj->query_from = str_replace(trim($join), "", $obj->query_from);
            }
            $obj->query_from .= implode( ' ', array_unique( $customJoin ) );
        }
	}
	else
	{
		// cron job execution
		if ( ! empty(XmlExportEngine::$exportOptions['whereclause']) ) $obj->query_where .= XmlExportEngine::$exportOptions['whereclause'];
		if ( ! empty(XmlExportEngine::$exportOptions['joinclause']) ) {
			$obj->query_from .= implode( ' ', array_unique( XmlExportEngine::$exportOptions['joinclause'] ) );		
		}
	}	
	return $obj;
}