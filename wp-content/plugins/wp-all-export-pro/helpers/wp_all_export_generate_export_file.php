<?php
if ( ! function_exists('wp_all_export_generate_export_file') )
{
	function wp_all_export_generate_export_file( $export_id )
	{
		$is_secure_import = PMXE_Plugin::getInstance()->getOption('secure');

		$wp_uploads  = wp_upload_dir();

		$target = $is_secure_import ? wp_all_export_secure_file($wp_uploads['basedir'] . DIRECTORY_SEPARATOR . PMXE_Plugin::UPLOADS_DIRECTORY, $export_id ) : $wp_uploads['path'];									

		$export = new PMXE_Export_Record();

		$export->getById( $export_id );
		
		if ( ! $export->isEmpty())
		{
			if($export->options['export_to'] == XmlExportEngine::EXPORT_TYPE_XML &&
			$export->options['xml_template_type'] == XmlExportEngine::EXPORT_TYPE_GOOLE_MERCHANTS) {
				$extension = \Wpae\App\Service\ExportGoogleMerchants::GOOGLE_MERCHANTS_EXTENSION;
			} else {
				$extension = $export->options['export_to'];
			}

			if ( $export->options['creata_a_new_export_file'] )
			{
				$export_file_name =  sanitize_file_name($export->friendly_name) . '-' . ($export->iteration + 1) . '.' . $extension;
			}
			else
			{
				$export_file_name = sanitize_file_name($export->friendly_name) . '.' . $extension;
			}					
		}		

		return apply_filters('wp_all_export_export_file_name', $target . DIRECTORY_SEPARATOR . $export_file_name, $export_id );		
	}
}