<?php

function pmxe_pmxe_before_export($export_id)
{
	$export = new PMXE_Export_Record();
	$export->getById($export_id);	
	
	if ( ! $export->isEmpty() )
	{				
		if ( ! $export->options['export_only_new_stuff'] )
		{
			$postList = new PMXE_Post_List();
			$missingPosts = $postList->getBy(array('export_id' => $export_id, 'iteration !=' => --$export->iteration));
			$missing_ids = array();
			if ( ! $missingPosts->isEmpty() ): 
					
				foreach ($missingPosts as $missingPost) 
				{			
					$missing_ids[] = $missingPost['post_id'];												
				}

			endif;	

			if ( ! empty($missing_ids))
			{
				global $wpdb;
				// Delete records form pmxe_posts
				$sql = "DELETE FROM " . PMXE_Plugin::getInstance()->getTablePrefix() . "posts WHERE post_id IN (" . implode(',', $missing_ids) . ") AND export_id = %d";
				$wpdb->query( 
					$wpdb->prepare($sql, $export->id)
				);
			}
		}

		if ( empty($export->parent_id) )
		{
			delete_option( 'wp_all_export_queue_' . $export->id );		
		}		
	}		
}