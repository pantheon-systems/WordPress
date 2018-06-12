<?php

function wp_all_export_terms_clauses($clauses, $taxonomies, $args)
{
	if ( ! empty(PMXE_Plugin::$session) and PMXE_Plugin::$session->has_session() )
	{
		// manual export run
		$customWhere = PMXE_Plugin::$session->get('whereclause');
        $clauses['where'] .= $customWhere;

		$customJoin = PMXE_Plugin::$session->get('joinclause');

		if ( ! empty( $customJoin ) ) {
            $clauses['join'] .= implode( ' ', array_unique( $customJoin ) );
		}
	}
	else
	{
		// cron job execution
		if ( ! empty(XmlExportEngine::$exportOptions['whereclause']) ) $clauses['where'] .= XmlExportEngine::$exportOptions['whereclause'];
		if ( ! empty(XmlExportEngine::$exportOptions['joinclause']) ) {
            $clauses['join'] .= implode( ' ', array_unique( XmlExportEngine::$exportOptions['joinclause'] ) );
		}
	}

	return $clauses;
}