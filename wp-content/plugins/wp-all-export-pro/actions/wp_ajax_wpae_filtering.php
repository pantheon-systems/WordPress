<?php

function pmxe_wp_ajax_wpae_filtering(){

	if ( ! check_ajax_referer( 'wp_all_export_secure', 'security', false )){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	if ( ! current_user_can( PMXE_Plugin::$capabilities ) ){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	$response = array(
		'html' => '',
		'btns' => ''
	);

	ob_start();

	$errors = new WP_Error();	

	$input = new PMXE_Input();
	
	$post = $input->post('data', array());

	if ( ! empty($post['cpt'])):		

		$engine = new XmlExportEngine($post, $errors);	

		$engine->init_available_data();	

		?>
		<div class="wpallexport-content-section">
			<div class="wpallexport-collapsed-header">
				<h3><?php _e('Add Filtering Options', 'wp_all_export_plugin'); ?></h3>	
			</div>		
			<div class="wpallexport-collapsed-content">			
				<?php include_once PMXE_ROOT_DIR . '/views/admin/export/blocks/filters.php'; ?>
			</div>	
		</div>

	<?php

	endif;

	$response['html'] = ob_get_clean();
	
	ob_start();

	if ( XmlExportEngine::$is_auto_generate_enabled ):
	?>
	<span class="wp_all_export_btn_with_note">
		<a href="javascript:void(0);" class="back rad3 auto-generate-template" style="float:none; background: #425f9a; padding: 0 50px; margin-right: 10px; color: #fff; font-weight: normal;"><?php printf(__('Migrate %s', 'wp_all_export_plugin'), wp_all_export_get_cpt_name(array($post['cpt']), 2, $post)); ?></a>
		<span class="auto-generate-template">&nbsp;</span>
	</span>
	<span class="wp_all_export_btn_with_note">
		<input type="submit" class="button button-primary button-hero wpallexport-large-button" value="<?php _e('Customize Export File', 'wp_all_export_plugin') ?>"/>
		<span class="auto-generate-template">&nbsp;</span>
	</span>
	<?php
	else:
	?>	
	<span class="wp_all_export_btn_with_note">
		<input type="submit" class="button button-primary button-hero wpallexport-large-button" value="<?php _e('Customize Export File', 'wp_all_export_plugin') ?>"/>		
	</span>
	<?php
	endif;
	$response['btns'] = ob_get_clean();
	
	exit(json_encode($response)); die;

}