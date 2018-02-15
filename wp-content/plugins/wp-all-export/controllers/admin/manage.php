<?php 
/**
 * Manage Imports
 * 
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 */
class PMXE_Admin_Manage extends PMXE_Controller_Admin {
	
	public function init() {
		parent::init();
		
		if ('update' == PMXE_Plugin::getInstance()->getAdminCurrentScreen()->action) {
			$this->isInline = true;			
		}
	}
	
	/**
	 * Previous Imports list
	 */
	public function index() {

		$get = $this->input->get(array(
			's' => '',
			'order_by' => 'id',
			'order' => 'DESC',
			'pagenum' => 1,
			'perPage' => 25,
		));
		$get['pagenum'] = absint($get['pagenum']);
		extract($get);
		$this->data += $get;

		if ( ! in_array($order_by, array('registered_on', 'id', 'friendly_name'))){
			$order_by = 'registered_on';
		}

		if ( ! in_array($order, array('DESC', 'ASC'))){
			$order = 'DESC';
		}
		
		$list = new PMXE_Export_List();		
		$by = array('parent_id' => 0);
		if ('' != $s) {
			$like = '%' . preg_replace('%\s+%', '%', preg_replace('/[%?]/', '\\\\$0', $s)) . '%';
			$by[] = array(array('friendly_name LIKE' => $like, 'registered_on LIKE' => $like), 'OR');
		}
		
		$this->data['list'] = $list->setColumns(
				$list->getTable() . '.*'				
			)->getBy($by, "$order_by $order", $pagenum, $perPage, $list->getTable() . '.id');
			
		$this->data['page_links'] = paginate_links(array(
			'base' => add_query_arg('pagenum', '%#%', $this->baseUrl),
			'add_args' => array('page' => 'pmxe-admin-manage'),
			'format' => '',
			'prev_text' => __('&laquo;', 'PMXE_plugin'),
			'next_text' => __('&raquo;', 'PMXE_plugin'),
			'total' => ceil($list->total() / $perPage),
			'current' => $pagenum,
		));
		
		PMXE_Plugin::$session->clean_session();		

		$this->render();
	}	
	
	/**
	 * Edit Options
	 */
	public function options() {
		
		// deligate operation to other controller
		$controller = new PMXE_Admin_Export();
		$controller->set('isTemplateEdit', true);
		$controller->options();
	}

	/**
	 * Edit Template
	 */
	public function template() {

		// deligate operation to other controller
		$controller = new PMXE_Admin_Export();
		$controller->set('isTemplateEdit', true);
		$controller->template();
	}	

	/**
	 * Cron Scheduling
	 */
	public function scheduling() {
		$this->data['id'] = $id = $this->input->get('id');
		$this->data['cron_job_key'] = PMXE_Plugin::getInstance()->getOption('cron_job_key');
		$this->data['item'] = $item = new PMXE_Export_Record();
		if ( ! $id or $item->getById($id)->isEmpty()) {
			wp_redirect($this->baseUrl); die();
		}

		$wp_uploads = wp_upload_dir();	

		$this->data['file_path'] = site_url() . '/wp-cron.php?export_hash=' . substr(md5($this->data['cron_job_key'] . $item['id']), 0, 16) . '&export_id=' . $item['id'] . '&action=get_data'; 

		$this->data['bundle_url'] = ''; 

		if ( ! empty($item['options']['bundlepath']) )
		{			
			$this->data['bundle_url'] = site_url() . '/wp-cron.php?export_hash=' . substr(md5($this->data['cron_job_key'] . $item['id']), 0, 16) . '&export_id=' . $item['id'] . '&action=get_bundle&t=zip';
		}		

		$this->render();
	}

	/**
	 * Google merchants info
	 */
	public function google_merchants_info() {

		$this->data['id'] = $id = $this->input->get('id');
		$this->data['cron_job_key'] = PMXE_Plugin::getInstance()->getOption('cron_job_key');
		$this->data['item'] = $item = new PMXE_Export_Record();
		if ( ! $id or $item->getById($id)->isEmpty()) {
			wp_redirect($this->baseUrl); die();
		}
		
		$this->data['file_path'] = site_url() . '/wp-cron.php?export_hash=' . substr(md5($this->data['cron_job_key'] . $item['id']), 0, 16) . '&export_id=' . $item['id'] . '&action=get_data';

		$this->render();
	}

	/**
	 * Download import templates
	 */
	public function templates() {
		$this->data['id'] = $id = $this->input->get('id');		
		$this->data['item'] = $item = new PMXE_Export_Record();
		if ( ! $id or $item->getById($id)->isEmpty()) {
			wp_redirect($this->baseUrl); die();
		}

		$this->render();
	}

	/**
	 * Cancel import processing
	 */
	public function cancel(){
		
		$id = $this->input->get('id');
		
		PMXE_Plugin::$session->clean_session( $id );

		$item = new PMXE_Export_Record();
		if ( ! $id or $item->getById($id)->isEmpty()) {
			wp_redirect($this->baseUrl); die();
		}
		$item->set(array(
			'triggered'   => 0,
			'processing'  => 0,
			'executing'   => 0,
			'canceled'    => 1,
			'canceled_on' => date('Y-m-d H:i:s')
		))->update();		

		wp_redirect(add_query_arg('pmxe_nt', urlencode(__('Export canceled', 'wp_all_import_plugin')), $this->baseUrl)); die();
	}
	
	/**
	 * Reexport
	 */
	public function update() {
			
		$id = $this->input->get('id');
		
		PMXE_Plugin::$session->clean_session($id);			

		$action_type = $this->input->get('type');

		$this->data['item'] = $item = new PMXE_Export_Record();
		if ( ! $id or $item->getById($id)->isEmpty()) {
			wp_redirect($this->baseUrl); die();
		}			

		$item->fix_template_options();				

		$default = PMXE_Plugin::get_default_import_options();
		$DefaultOptions = $item->options + $default;
		if (empty($item->options['export_variations'])){
			$DefaultOptions['export_variations'] = XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_PARENT_AND_VARIATION;
		}
		if (empty($item->options['export_variations_title'])){
			$DefaultOptions['export_variations_title'] = XmlExportEngine::VARIATION_USE_DEFAULT_TITLE;
		}
		$this->data['post'] = $post = $this->input->post($DefaultOptions);	
		$this->data['iteration'] = $item->iteration;

		if ($this->input->post('is_confirmed')) {

			check_admin_referer('update-export', '_wpnonce_update-export');	
			
			$iteration = ( empty($item->options['creata_a_new_export_file']) && ! empty($post['creata_a_new_export_file'])) ? 0 : $item->iteration;			

			$item->set(array( 'options' => $post, 'iteration' => $iteration))->save();
			if ( ! empty($post['friendly_name']) ) {
				$item->set( array( 'friendly_name' => $post['friendly_name'], 'scheduled' => (($post['is_scheduled']) ? $post['scheduled_period'] : '') ) )->save();	
			}			

			// compose data to look like result of wizard steps
			$sesson_data = $post + array('update_previous' => $item->id ) + $default;
			
			foreach ($sesson_data as $key => $value) {
				PMXE_Plugin::$session->set($key, $value);
			}

			$this->data['engine'] = new XmlExportEngine($sesson_data, $this->errors);	
			$this->data['engine']->init_additional_data();
			$this->data['engine']->init_available_data();	

			PMXE_Plugin::$session->save_data();			

			if ( ! $this->errors->get_error_codes()) {		

				// deligate operation to other controller
				$controller = new PMXE_Admin_Export();
				$controller->data['update_previous'] = $item;
				$controller->process();
				return;
				
			}

			$this->errors->remove('count-validation');
			if ( ! $this->errors->get_error_codes()) {												
				?>
				<script type="text/javascript">
				window.location.href = "<?php echo add_query_arg('pmxe_nt', urlencode(__('Options updated', 'wp_all_export_plugin')), $this->baseUrl); ?>";
				</script>
				<?php
				die();	
			}

		}

		$this->data['isWizard'] = false;		
		$this->data['engine'] = new XmlExportEngine($post, $this->errors);	
		$this->data['engine']->init_available_data();	
		
		$this->render();
	}
	
	/**
	 * Delete an import
	 */
	public function delete() {
		$id = $this->input->get('id');
		$this->data['item'] = $item = new PMXE_Export_Record();
		if ( ! $id or $item->getById($id)->isEmpty()) {
			wp_redirect($this->baseUrl); die();
		}
		
		if ($this->input->post('is_confirmed')) {
			check_admin_referer('delete-export', '_wpnonce_delete-export');					
			$item->delete();
			wp_redirect(add_query_arg('pmxe_nt', urlencode(__('Export deleted', 'wp_all_export_plugin')), $this->baseUrl)); die();
		}
		
		$this->render();
	}
	
	/**
	 * Bulk actions
	 */
	public function bulk() {
		check_admin_referer('bulk-exports', '_wpnonce_bulk-exports');
		if ($this->input->post('doaction2')) {
			$this->data['action'] = $action = $this->input->post('bulk-action2');
		} else {
			$this->data['action'] = $action = $this->input->post('bulk-action');
		}		
		$this->data['ids'] = $ids = $this->input->post('items');
		$this->data['items'] = $items = new PMXE_Export_List();
		if (empty($action) or ! in_array($action, array('delete')) or empty($ids) or $items->getBy('id', $ids)->isEmpty()) {
			wp_redirect($this->baseUrl); die();
		}		
		if ($this->input->post('is_confirmed')) {			
			foreach($items->convertRecords() as $item) {
				
				if ($item->attch_id) wp_delete_attachment($item->attch_id, true);

				$item->delete();
			}			
			wp_redirect(add_query_arg('pmxe_nt', urlencode(sprintf(__('%d %s deleted', 'wp_all_export_plugin'), $items->count(), _n('export', 'exports', $items->count(), 'wp_all_export_plugin'))), $this->baseUrl)); die();
		}		
		$this->render();
	}

	public function get_template(){
		$nonce = (!empty($_REQUEST['_wpnonce'])) ? $_REQUEST['_wpnonce'] : '';
		if ( ! wp_verify_nonce( $nonce, '_wpnonce-download_template' ) ) {		    
		    die( __('Security check', 'wp_all_export_plugin') ); 
		} else {	
			
			$id = $this->input->get('id');

			$export = new PMXE_Export_Record();

			$filepath = '';

			$export_data = array();
			
			if ( ! $export->getById($id)->isEmpty()){
				
				$export_data[] = $export->options['tpl_data'];
				$uploads   = wp_upload_dir();
				$targetDir = $uploads['basedir'] . DIRECTORY_SEPARATOR . PMXE_Plugin::TEMP_DIRECTORY;
				
				$export_file_name = "WP All Import Template - " . sanitize_file_name($export->friendly_name) . ".txt";

				file_put_contents($targetDir . DIRECTORY_SEPARATOR . $export_file_name, json_encode($export_data));						
				
				PMXE_download::csv($targetDir . DIRECTORY_SEPARATOR . $export_file_name);		

			}
		}
	}

	/*
	 * Download bundle for WP All Import
	 *
	 */
	public function bundle()
	{				
		$nonce = (!empty($_REQUEST['_wpnonce'])) ? $_REQUEST['_wpnonce'] : '';
		if ( ! wp_verify_nonce( $nonce, '_wpnonce-download_bundle' ) ) {
		    die( __('Security check', 'wp_all_export_plugin') ); 
		} else {

			$uploads  = wp_upload_dir();
						
			$id = $this->input->get('id');

			$export = new PMXE_Export_Record();		

			if ( ! $export->getById($id)->isEmpty())
			{				
				if ( ! empty($export->options['bundlepath']) )
				{
					$bundle_path = wp_all_export_get_absolute_path($export->options['bundlepath']);

					if ( @file_exists($bundle_path) )
					{						
						$bundle_url = $uploads['baseurl'] . str_replace($uploads['basedir'], '', $bundle_path);

						PMXE_download::zip($bundle_path);	
					}					
				}
				else
				{
					wp_redirect(add_query_arg('pmxe_nt', urlencode(__('The exported bundle is missing and can\'t be downloaded. Please re-run your export to re-generate it.', 'wp_all_export_plugin')), $this->baseUrl)); die();
				}
			}
			else
			{
				wp_redirect(add_query_arg('pmxe_nt', urlencode(__('This export doesn\'t exist.', 'wp_all_export_plugin')), $this->baseUrl)); die();
			}			
		}
	}	

	public function split_bundle(){
		$nonce = (!empty($_REQUEST['_wpnonce'])) ? $_REQUEST['_wpnonce'] : '';
		if ( ! wp_verify_nonce( $nonce, '_wpnonce-download_split_bundle' ) ) {		    
		    die( __('Security check', 'wp_all_export_plugin') ); 
		} else {

			$uploads  = wp_upload_dir();
			
			$id = PMXE_Plugin::$session->update_previous;

			if (empty($id))
				$id = $this->input->get('id');

			$export = new PMXE_Export_Record();					
		
			if ( ! $export->getById($id)->isEmpty())
			{	
				if ( ! empty($export->options['split_files_list']))
				{					
					$tmp_dir    = $uploads['basedir'] . DIRECTORY_SEPARATOR . PMXE_Plugin::TEMP_DIRECTORY . DIRECTORY_SEPARATOR . md5($export->id) . DIRECTORY_SEPARATOR;
					$bundle_dir = $tmp_dir . 'split_files' . DIRECTORY_SEPARATOR;

					wp_all_export_rrmdir($tmp_dir);

					@mkdir($tmp_dir);	
					@mkdir($bundle_dir);

					foreach ($export->options['split_files_list'] as $file) {
						@copy( $file, $bundle_dir . basename($file) );							
					}

					$friendly_name = sanitize_file_name($export->friendly_name);

					$bundle_path = $tmp_dir . $friendly_name . '-split-files.zip';

					PMXE_Zip::zipDir($bundle_dir, $bundle_path);

					if (file_exists($bundle_path))
					{
						$bundle_url = $uploads['baseurl'] . str_replace($uploads['basedir'], '', $bundle_path);

						PMXE_download::zip($bundle_path);						
					}	
				}						
			}
		}
	}

	/*
	 * Download import log file
	 *
	 */
	public function get_file(){

		$nonce = (!empty($_REQUEST['_wpnonce'])) ? $_REQUEST['_wpnonce'] : '';
		if ( ! wp_verify_nonce( $nonce, '_wpnonce-download_feed' ) ) {		    
		    die( __('Security check', 'wp_all_export_plugin') ); 
		} else {

			$is_secure_import = PMXE_Plugin::getInstance()->getOption('secure');

			$id = $this->input->get('id');

			$export = new PMXE_Export_Record();

			$filepath = '';

			if ( ! $export->getById($id)->isEmpty())
			{
				if ( ! $is_secure_import)
				{
					$filepath = get_attached_file($export->attch_id);					
				}
				else
				{
					$filepath = wp_all_export_get_absolute_path($export->options['filepath']);
				}				

				if ( @file_exists($filepath) )
				{					
					switch ($export->options['export_to']) 
					{
						case 'xml':
							if($export['options']['xml_template_type'] == XmlExportEngine::EXPORT_TYPE_GOOLE_MERCHANTS) {
								PMXE_Download::txt($filepath);
							} else {
								PMXE_download::xml($filepath);
							}

							break;
						case 'csv':							
							if (empty($export->options['export_to_sheet']) or $export->options['export_to_sheet'] == 'csv')
							{
								PMXE_download::csv($filepath);		
							}							
							else 
							{													
								PMXE_download::xls($filepath);		
							}
							break;
						default:
							wp_redirect(add_query_arg('pmxe_nt', urlencode(__('File format not supported', 'wp_all_export_plugin')), $this->baseUrl)); die();
							break;
					}
				}	
				else
				{
					wp_redirect(add_query_arg('pmxe_nt', urlencode(__('The exported file is missing and can\'t be downloaded. Please re-run your export to re-generate it.', 'wp_all_export_plugin')), $this->baseUrl)); die();
				}
			}
			else 
			{
				wp_redirect(add_query_arg('pmxe_nt', urlencode(__('The exported file is missing and can\'t be downloaded. Please re-run your export to re-generate it.', 'wp_all_export_plugin')), $this->baseUrl)); die();
			}		
		}
	}

}