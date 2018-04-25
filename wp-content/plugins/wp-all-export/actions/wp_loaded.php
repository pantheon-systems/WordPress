<?php

function pmxe_wp_loaded() {

	@ini_set("max_input_time", PMXE_Plugin::getInstance()->getOption('max_input_time'));

	$maxExecutionTime  = PMXE_Plugin::getInstance()->getOption('max_execution_time');
	if($maxExecutionTime == -1) {
		$maxExecutionTime = 0;
	}

	@ini_set("max_execution_time", $maxExecutionTime);

	$scheduledExport = new \Wpae\Scheduling\Export();

	if ( ! empty($_GET['zapier_subscribe']) and ! empty($_GET['api_key']) )
	{
		$zapier_api_key = PMXE_Plugin::getInstance()->getOption('zapier_api_key');

		if ( ! empty($zapier_api_key) and $zapier_api_key == $_GET['api_key'] )
		{
			$subscriptions = get_option('zapier_subscribe', array());

			$body = json_decode(file_get_contents("php://input"), true);

			if ( ! empty($body))
			{
				$subscriptions[basename($body['target_url'])] = $body;
			}

			update_option('zapier_subscribe', $subscriptions);

			exit(json_encode(array('status' => 200)));
		}
		else
		{
			http_response_code(401);
			exit(json_encode(array('status' => 'error')));
		}
	}

	if ( ! empty($_GET['zapier_unsubscribe']) and ! empty($_GET['api_key']) )
	{
		$zapier_api_key = PMXE_Plugin::getInstance()->getOption('zapier_api_key');

		if ( ! empty($zapier_api_key) and $zapier_api_key == $_GET['api_key'] )
		{
			$subscriptions = get_option('zapier_subscribe', array());

			$body = json_decode(file_get_contents("php://input"), true);

			if ( ! empty($subscriptions[basename($body['target_url'])]) ) unset($subscriptions[basename($body['target_url'])]);

			update_option('zapier_subscribe', $subscriptions);

			exit(json_encode(array('status' => 200)));
		}
		else
		{
			http_response_code(401);
			exit(json_encode(array('status' => 'error')));
		}
	}

	if ( ! empty($_GET['export_completed']) and ! empty($_GET['api_key']))
	{

		$zapier_api_key = PMXE_Plugin::getInstance()->getOption('zapier_api_key');

		if ( ! empty($zapier_api_key) and $zapier_api_key == $_GET['api_key'] )
		{

			global $wpdb;

			$table_prefix = PMXE_Plugin::getInstance()->getTablePrefix();

			$export = $wpdb->get_row("SELECT * FROM {$table_prefix}exports ORDER BY `id` DESC LIMIT 1");

			if ( ! empty($export) and ! is_wp_error($export) )
			{
				$child_export = new PMXE_Export_Record();
				$child_export->getById( $export->id );

				if ( ! $child_export->isEmpty())
				{
					$wp_uploads = wp_upload_dir();

					$is_secure_import = PMXE_Plugin::getInstance()->getOption('secure');

					if ( ! $is_secure_import)
					{
						$filepath = get_attached_file($child_export->attch_id);
					}
					else
					{
						$filepath = wp_all_export_get_absolute_path($child_export->options['filepath']);
					}

					$fileurl = str_replace($wp_uploads['basedir'], $wp_uploads['baseurl'], $filepath);

					$response = array(
						'website_url' => home_url(),
						'export_id' => $child_export->id,
						'export_name' => $child_export->friendly_name,
						'file_name' => basename($filepath),
						'file_type' => $child_export->options['export_to'],
						'post_types_exported' => empty($child_export->options['cpt']) ? $child_export->options['wp_query'] : implode($child_export->options['cpt'], ','),
						'export_created_date' => $child_export->registered_on,
						'export_last_run_date' => date('Y-m-d H:i:s'),
						'export_trigger_type' => 'manual',
						'records_exported' => $child_export->exported,
						'export_file' => ''
					);

					if (file_exists($filepath))
					{
						$response['export_file_url'] = $fileurl;
						$response['status'] = 200;
						$response['message'] = 'OK';
					}
					else
					{
						$response['export_file_url'] = '';
						$response['status'] = 300;
						$response['message'] = 'File doesn\'t exist';
					}

					$response = apply_filters('wp_all_export_zapier_response', $response);

					wp_send_json(array($response));
				}
			}

		}
		else
		{
			http_response_code(401);
			exit(json_encode(array('status' => 'error')));
		}
	}

	/* Check if cron is manualy, then execute export */
	$cron_job_key = PMXE_Plugin::getInstance()->getOption('cron_job_key');

	if ( ! empty($_GET['action']) && ! empty($_GET['export_id']) && (!empty($_GET['export_hash']) || !empty($_GET['security_token'])))
	{

		if(empty($_GET['export_hash'])) {
			$securityToken = $_GET['security_token'];
		} else {
			$securityToken = $_GET['export_hash'];
		}

		if ( $securityToken == substr(md5($cron_job_key . $_GET['export_id']), 0, 16) )
		{
			$export = new PMXE_Export_Record();

			$export->getById($_GET['export_id']);

			if ( ! $export->isEmpty())
			{
				switch ($_GET['action'])
				{
					case 'get_data':

						if ( ! empty($export->options['current_filepath']) and @file_exists($export->options['current_filepath']))
						{
							$filepath = $export->options['current_filepath'];
						}
						else
						{
							$is_secure_import = PMXE_Plugin::getInstance()->getOption('secure');

							if ( ! $is_secure_import)
							{
								$filepath = get_attached_file($export->attch_id);
							}
							else
							{
								$filepath = wp_all_export_get_absolute_path($export->options['filepath']);
							}
						}

						if ( ! empty($_GET['part']) and is_numeric($_GET['part'])) $filepath = str_replace(basename($filepath), str_replace('.' . $export->options['export_to'], '', basename($filepath)) . '-' . $_GET['part'] . '.' . $export->options['export_to'], $filepath);

						break;

					case 'get_bundle':

						$filepath = wp_all_export_get_absolute_path($export->options['bundlepath']);

						break;
				}

				if (file_exists($filepath))
				{
					$uploads  = wp_upload_dir();
					$fileurl = $uploads['baseurl'] . str_replace($uploads['basedir'], '', str_replace(basename($filepath), rawurlencode(basename($filepath)), $filepath));

					if($export['options']['export_to'] == XmlExportEngine::EXPORT_TYPE_XML && $export['options']['xml_template_type'] == XmlExportEngine::EXPORT_TYPE_GOOLE_MERCHANTS) {

						// If we are doing a google merchants export, send the file as a download.
						header("Content-type: text/plain");
						header("Content-Disposition: attachment; filename=".basename($filepath));
						readfile($filepath);

						die;
					}
					$fileurl = str_replace( "\\", "/", $fileurl );
					wp_redirect($fileurl);
				}
				else
				{
					wp_send_json(array(
						'status'     => 403,
						'message'    => __('File doesn\'t exist', 'wp_all_export_plugin')
					));
				}
			}
		}
		else
		{
			wp_send_json(array(
				'status'     => 403,
				'message'    => __('Export hash is not valid.', 'wp_all_export_plugin')
			));
		}
	}

    if(isset($_GET['action']) && $_GET['action'] == 'wpae_public_api') {
        $router = new \Wpae\Http\Router();
        $router->route($_GET['q'], false);
    }
}

