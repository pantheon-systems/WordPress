<?php

function pmxi_wp_loaded_99() {

	$table = PMXI_Plugin::getInstance()->getTablePrefix() . 'imports';
	global $wpdb;
	$imports = $wpdb->get_results("SELECT `id`, `name`, `path` FROM $table WHERE `path` IS NULL", ARRAY_A);

	if ( ! empty($imports) ){

		$importRecord = new PMXI_Import_Record();
		$importRecord->clear();
		foreach ($imports as $imp) {
			$importRecord->getById($imp['id']);
			if ( ! $importRecord->isEmpty()){
				$importRecord->delete( true );
			}
			$importRecord->clear();
		}

	}
	
	/* Check if cron is manualy, then execute import */
	$cron_job_key = PMXI_Plugin::getInstance()->getOption('cron_job_key');
	
	if (!empty($cron_job_key) and !empty($_GET['import_key']) and $_GET['import_key'] == $cron_job_key and !empty($_GET['action']) and in_array($_GET['action'], array('processing','trigger','pipe','cancel','cleanup'))) {		
		
		$logger = create_function('$m', 'echo "<p>$m</p>\\n";');								

		if (empty($_GET['import_id']))
		{
			if ($_GET['action'] == 'cleanup')
			{
				$settings = new PMXI_Admin_Settings();				
				$settings->cleanup( true );
				wp_send_json(array(
					'status'     => 200,
					'message'    => __('Cleanup completed.', 'wp_all_import_plugin')
				));				
				return;
			}		
			wp_send_json(array(
				'status'     => 403,
				'message'    => __('Missing import ID.', 'wp_all_import_plugin')
			));				
			return;
		}

		$import = new PMXI_Import_Record();
		
		$ids = explode(',', $_GET['import_id']);

		if (!empty($ids) and is_array($ids)){						

			foreach ($ids as $id) { if (empty($id)) continue;

				$import->getById($id);	

				if ( ! $import->isEmpty() ){			

					if ( ! empty($_GET['sync']) )
					{
						$imports = $wpdb->get_results("SELECT `id`, `name`, `path` FROM $table WHERE `processing` = 1", ARRAY_A);
						if ( ! empty($imports) )
						{
							$processing_ids = array();
							foreach ($imports as $imp) {
								$processing_ids[] = $imp['id'];
							}
							wp_send_json(array(
								'status'     => 403,
								'message'    => sprintf(__('Other imports are currently in process [%s].', 'wp_all_import_plugin'), implode(",", $processing_ids))
							));
							//$logger and call_user_func($logger, sprintf(__('Other imports are currently in process [%s].', 'wp_all_import_plugin'), implode(",", $processing_ids)));
							break;
						}
					}		

					if ( ! in_array($import->type, array('url', 'ftp', 'file')) ) {
						wp_send_json(array(
							'status'     => 500,
							'message'    => sprintf(__('Scheduling update is not working with "upload" import type. Import #%s.', 'wp_all_import_plugin'), $id)
						));
						//$logger and call_user_func($logger, sprintf(__('Scheduling update is not working with "upload" import type. Import #%s.', 'wp_all_import_plugin'), $id));
					}

					switch ($_GET['action']) {

						case 'trigger':							

							if ( (int) $import->executing ){

								wp_send_json(array(
									'status'     => 403,
									'message'    => sprintf(__('Import #%s is currently in manually process. Request skipped.', 'wp_all_import_plugin'), $id)
								));

								//$logger and call_user_func($logger, sprintf(__('Import #%s is currently in manually process. Request skipped.', 'wp_all_import_plugin'), $id));	
							}
							elseif ( ! $import->processing and ! $import->triggered ){
								
								$import->set(array(
									'triggered' => 1,						
									'imported' => 0,
									'created' => 0,
									'updated' => 0,
									'skipped' => 0,
									'deleted' => 0,																		
									'queue_chunk_number' => 0,
									'last_activity' => date('Y-m-d H:i:s')									
								))->update();
									
								$history_log = new PMXI_History_Record();						
								$history_log->set(array(
									'import_id' => $import->id,
									'date' => date('Y-m-d H:i:s'),
									'type' => 'trigger',
									'summary' => __("triggered by cron", "wp_all_import_plugin")
								))->save();	

								//$logger and call_user_func($logger, sprintf(__('#%s Cron job triggered.', 'wp_all_import_plugin'), $id));
								wp_send_json(array(
									'status'     => 200,
									'message'    => sprintf(__('#%s Cron job triggered.', 'wp_all_import_plugin'), $id)
								));
							
							}
							elseif( $import->processing and ! $import->triggered) {
								wp_send_json(array(
									'status'     => 403,
									'message'    => sprintf(__('Import #%s currently in process. Request skipped.', 'wp_all_import_plugin'), $id)
								));

								//$logger and call_user_func($logger, sprintf(__('Import #%s currently in process. Request skipped.', 'wp_all_import_plugin'), $id));	
							}													
							elseif( ! $import->processing and $import->triggered){
								
								wp_send_json(array(
									'status'     => 403,
									'message'    => sprintf(__('Import #%s already triggered. Request skipped.', 'wp_all_import_plugin'), $id)
								));

								//$logger and call_user_func($logger, sprintf(__('Import #%s already triggered. Request skipped.', 'wp_all_import_plugin'), $id));	
							}

							break;

						case 'processing':

							if ( $import->processing == 1 and (time() - strtotime($import->registered_on)) > ((PMXI_Plugin::getInstance()->getOption('cron_processing_time_limit')) ? PMXI_Plugin::getInstance()->getOption('cron_processing_time_limit') : 120)){ // it means processor crashed, so it will reset processing to false, and terminate. Then next run it will work normally.
								$import->set(array(
									'processing' => 0									
								))->update();
							}
							
							// start execution imports that is in the cron process												
							if ( ! (int) $import->triggered ){
								wp_send_json(array(
									'status'     => 403,
									'message'    => sprintf(__('Import #%s is not triggered. Request skipped.', 'wp_all_import_plugin'), $id)
								));
								//$logger and call_user_func($logger, sprintf(__('Import #%s is not triggered. Request skipped.', 'wp_all_import_plugin'), $id));	
							}
							elseif ( (int) $import->executing ){
								wp_send_json(array(
									'status'     => 403,
									'message'    => sprintf(__('Import #%s is currently in manually process. Request skipped.', 'wp_all_import_plugin'), $id)
								));
								//$logger and call_user_func($logger, sprintf(__('Import #%s is currently in manually process. Request skipped.', 'wp_all_import_plugin'), $id));	
							}
							elseif ( (int) $import->triggered and ! (int) $import->processing ){								
								
								$log_storage = (int) PMXI_Plugin::getInstance()->getOption('log_storage');

								// unlink previous logs
								if ( (int) $import->queue_chunk_number < (int) $import->count )
								{
									$by = array();
									$by[] = array(array('import_id' => $id, 'type NOT LIKE' => 'trigger'), 'AND');
									$historyLogs = new PMXI_History_List();
									$historyLogs->setColumns('id', 'import_id', 'type', 'date')->getBy($by, 'id ASC');
									if ($historyLogs->count() and $historyLogs->count() >= $log_storage ){
										$logsToRemove = $historyLogs->count() - $log_storage;
										foreach ($historyLogs as $i => $file){																					
											$historyRecord = new PMXI_History_Record();
											$historyRecord->getBy('id', $file['id']);
											if ( ! $historyRecord->isEmpty()) $historyRecord->delete(); // unlink history file only
											if ($i == $logsToRemove)
												break;
										}
									}	

									$history_log = new PMXI_History_Record();						
									$history_log->set(array(
										'import_id' => $import->id,
										'date' => date('Y-m-d H:i:s'),
										'type' => 'processing',
										'summary' => __("cron processing", "wp_all_import_plugin")
									))->save();	

									if ($log_storage){
										$wp_uploads = wp_upload_dir();	
										$log_file = wp_all_import_secure_file( $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::LOGS_DIRECTORY, $history_log->id ) . DIRECTORY_SEPARATOR . $history_log->id . '.html';
										if ( @file_exists($log_file) ) wp_all_import_remove_source($log_file, false);	

										//@file_put_contents($log_file, sprintf(__('<p>Source path `%s`</p>', 'wp_all_import_plugin'), $import->path));
										
									}
								}

								ob_start();

								$response = $import->set(array('canceled' => 0, 'failed' => 0))->execute($logger, true, $history_log->id);

								$log_data = ob_get_clean();
								
								if ($log_storage){									
									$log = @fopen($log_file, 'a+');				
									@fwrite($log, $log_data);
									@fclose($log);
								}

								if ( ! empty($response) and is_array($response)){
									wp_send_json($response);
								}
								elseif ( ! (int) $import->queue_chunk_number ){

									wp_send_json(array(
										'status'     => 200,
										'message'    => sprintf(__('Import #%s complete', 'wp_all_import_plugin'), $import->id)
									));
									//$logger and call_user_func($logger, sprintf(__('Import #%s complete', 'wp_all_import_plugin'), $import->id));	

								}
								else{

									wp_send_json(array(
										'status'     => 200,
										'message'    => sprintf(__('Records Processed %s. Records Count %s.', 'wp_all_import_plugin'), (int) $import->queue_chunk_number, (int) $import->count)
									));

									// $logger and call_user_func($logger, sprintf(__('Records Count %s', 'wp_all_import_plugin'), (int) $import->count));
									// $logger and call_user_func($logger, sprintf(__('Records Processed %s', 'wp_all_import_plugin'), (int) $import->queue_chunk_number));

								}

							}
							else {
								wp_send_json(array(
									'status'     => 403,
									'message'    => sprintf(__('Import #%s already processing. Request skipped.', 'wp_all_import_plugin'), $id)
								));
								//$logger and call_user_func($logger, sprintf(__('Import #%s already processing. Request skipped.', 'wp_all_import_plugin'), $id));								
							}

							break;					
						case 'pipe':					

							$import->execute($logger);

							break;

						case 'cancel':

							$import->set(array(
								'triggered'   => 0,
								'processing'  => 0,
								'executing'   => 0,
								'canceled'    => 1,
								'canceled_on' => date('Y-m-d H:i:s')
							))->update();

							wp_send_json(array(
								'status'     => 200,
								'message'    => sprintf(__('Import #%s canceled', 'wp_all_import_plugin'), $import->id)
							));

							break;
					}								
				}					
			}
		}		
	}			
}