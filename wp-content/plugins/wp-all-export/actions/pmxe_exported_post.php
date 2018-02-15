<?php

function pmxe_pmxe_exported_post( $pid, $exportRecord )
{
	// do not associate exported record with child export
	if ( ! empty($exportRecord->parent_id) ) return;

	$postRecord = new PMXE_Post_Record();	
	$postRecord->getBy(array(
		'post_id'   => $pid,
		'export_id' => XmlExportEngine::$exportID		
	));

	if ($postRecord->isEmpty())
	{
		$postRecord->set(array(
			'post_id'   => $pid,
			'export_id' => XmlExportEngine::$exportID,
			'iteration' => $exportRecord->iteration		
		))->insert();
	}	
	else
	{
		$postRecord->set(array('iteration' => $exportRecord->iteration))->update();
	}		
}