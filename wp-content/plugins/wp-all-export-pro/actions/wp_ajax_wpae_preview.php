<?php
/**
*	AJAX action for preview export row
*/

function pmxe_wp_ajax_wpae_preview(){

	if ( ! check_ajax_referer( 'wp_all_export_secure', 'security', false )){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	if ( ! current_user_can( PMXE_Plugin::$capabilities ) ){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	XmlExportEngine::$is_preview = true;

	$custom_xml_valid = true;

	ob_start();

	$values = array();

	parse_str($_POST['data'], $values);

	if(is_array($values['cc_options'])) {

		foreach ($values['cc_options'] as &$value) {
			$value = stripslashes($value);
		}
	}

	$export_id = (isset($_GET['id'])) ? stripcslashes($_GET['id']) : 0;

	$exportOptions = $values + (PMXE_Plugin::$session->has_session() ? PMXE_Plugin::$session->get_clear_session_data() : array()) + PMXE_Plugin::get_default_import_options();

	$exportOptions['custom_xml_template'] = (isset($_POST['custom_xml'])) ? stripcslashes($_POST['custom_xml']) : '';
	$exportOptions['custom_xml_template'] = str_replace('<ID>','<id>', $exportOptions['custom_xml_template'] );
	$exportOptions['custom_xml_template'] = str_replace('</ID>','</id>', $exportOptions['custom_xml_template'] );

	if ( ! empty($exportOptions['custom_xml_template'])) {
		$custom_xml_template_line_count = substr_count($exportOptions['custom_xml_template'], "\n");
    }

    $errors = new WP_Error();

	$engine = new XmlExportEngine($exportOptions, $errors);

	XmlExportEngine::$exportOptions      = $exportOptions;
	XmlExportEngine::$is_user_export     = $exportOptions['is_user_export'];
	XmlExportEngine::$is_comment_export  = $exportOptions['is_comment_export'];
	XmlExportEngine::$is_taxonomy_export = $exportOptions['is_taxonomy_export'];
	XmlExportEngine::$exportID 			 = $export_id;

	if ( class_exists('SitePress') && ! empty(XmlExportEngine::$exportOptions['wpml_lang'])){
		do_action( 'wpml_switch_language', XmlExportEngine::$exportOptions['wpml_lang'] );
	}

	if (XmlExportEngine::$exportOptions['export_to'] == XmlExportEngine::EXPORT_TYPE_XML && in_array(XmlExportEngine::$exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants')) ){

		if ( empty(XmlExportEngine::$exportOptions['custom_xml_template']) )
		{
			$errors->add('form-validation', __('XML template is empty.', 'wp_all_export_plugin'));
		}

		if ( ! empty(XmlExportEngine::$exportOptions['custom_xml_template'])){

			$engine->init_additional_data();

			$engine->init_available_data();

			$result = $engine->parse_custom_xml_template();		
			$line_numbers = $result['line_numbers'];
			if ( ! $errors->get_error_codes()) {
				XmlExportEngine::$exportOptions = array_merge(XmlExportEngine::$exportOptions, $result);
			}

			$originalXmlTemplate = $exportOptions['custom_xml_template'];
			libxml_use_internal_errors(true);
			libxml_clear_errors();

			//Add root se we make sure there is a root tag
			$result['original_post_loop'] = '<root>'.$result['original_post_loop'].'</root>';

			$custom_xml_template = simplexml_load_string($result['original_post_loop']);

			if ($custom_xml_template === false) {
				$custom_xml_template_errors = libxml_get_errors();
				libxml_clear_errors();
				$custom_xml_valid = false;
				// Remove one line because we added root
				$line_difference = $custom_xml_template_line_count - $line_numbers - 1;
			}
			$exportOptions['custom_xml_template'] = str_replace("<!-- BEGIN POST LOOP -->", "<!-- BEGIN LOOP -->", $exportOptions['custom_xml_template']);
			$exportOptions['custom_xml_template'] = str_replace("<!-- END POST LOOP -->", "<!-- END LOOP -->", $exportOptions['custom_xml_template']);

		}
	}

	if(isset($_GET['show_cdata'])) {
		XmlExportEngine::$exportOptions['show_cdata_in_preview'] = (bool)$_GET['show_cdata'];
	} else {
		XmlExportEngine::$exportOptions['show_cdata_in_preview'] = false;
	}

	if ( $errors->get_error_codes()) {
		$msgs = $errors->get_error_messages();
		if ( ! is_array($msgs)) {
			$msgs = array($msgs);
		}
		foreach ($msgs as $msg): ?>
			<div class="error"><p><?php echo $msg ?></p></div>
		<?php endforeach;
		exit( json_encode(array('html' => ob_get_clean())) );
	}

	if ( 'advanced' == $exportOptions['export_type'] )
	{
		if ( XmlExportEngine::$is_user_export ) {
			$exportQuery = eval('return new WP_User_Query(array(' . $exportOptions['wp_query'] . ', \'offset\' => 0, \'number\' => 10));');
		}
		elseif ( XmlExportEngine::$is_comment_export ) {
			$exportQuery = eval('return new WP_Comment_Query(array(' . $exportOptions['wp_query'] . ', \'offset\' => 0, \'number\' => 10));');
		}
		else {
			remove_all_actions('parse_query');
			remove_all_actions('pre_get_posts');
			remove_all_filters('posts_clauses');
			
			$exportQuery = eval('return new WP_Query(array(' . $exportOptions['wp_query'] . ', \'offset\' => 0, \'posts_per_page\' => 10));');
		}
	}
	else
	{
		XmlExportEngine::$post_types = $exportOptions['cpt'];

		if ( in_array('users', $exportOptions['cpt']) or in_array('shop_customer', $exportOptions['cpt']))
		{
			add_action('pre_user_query', 'wp_all_export_pre_user_query', 10, 1);
			$exportQuery = new WP_User_Query( array( 'orderby' => 'ID', 'order' => 'ASC', 'number' => 10 ));
			remove_action('pre_user_query', 'wp_all_export_pre_user_query');
		}
		elseif ( in_array('taxonomies', $exportOptions['cpt']))
		{
			add_filter('terms_clauses', 'wp_all_export_terms_clauses', 10, 3);
			$exportQuery = new WP_Term_Query( array( 'taxonomy' => $exportOptions['taxonomy_to_export'], 'orderby' => 'term_id', 'order' => 'ASC', 'number' => 10, 'hide_empty' => false ));
			remove_filter('terms_clauses', 'wp_all_export_terms_clauses');
		}
		elseif( in_array('comments', $exportOptions['cpt']))
		{
			add_action('comments_clauses', 'wp_all_export_comments_clauses', 10, 1);

			global $wp_version;

			if ( version_compare($wp_version, '4.2.0', '>=') )
			{
				$exportQuery = new WP_Comment_Query( array( 'orderby' => 'comment_ID', 'order' => 'ASC', 'number' => 10 ));
			}
			else
			{
				$exportQuery = get_comments( array( 'orderby' => 'comment_ID', 'order' => 'ASC', 'number' => 10 ));
			}
			remove_action('comments_clauses', 'wp_all_export_comments_clauses');
		}
		else
		{
			remove_all_actions('parse_query');
			remove_all_actions('pre_get_posts');
			remove_all_filters('posts_clauses');

			add_filter('posts_join', 'wp_all_export_posts_join', 10, 1);
			add_filter('posts_where', 'wp_all_export_posts_where', 10, 1);
			$exportQuery = new WP_Query( array( 'post_type' => $exportOptions['cpt'], 'post_status' => 'any', 'orderby' => 'title', 'order' => 'ASC', 'posts_per_page' => 10 ));
			
			remove_filter('posts_where', 'wp_all_export_posts_where');
			remove_filter('posts_join', 'wp_all_export_posts_join');
		}
	}

	XmlExportEngine::$exportQuery = $exportQuery;

    $engine->init_additional_data();

	?>

	<div id="post-preview" class="wpallexport-preview">
		
		<p class="wpallexport-preview-title"><?php echo sprintf("Preview first 10 %s", wp_all_export_get_cpt_name($exportOptions['cpt'], 10, $exportOptions)); ?></p>

		<div class="wpallexport-preview-content">

		<?php

		if(!$custom_xml_valid) {
			$error_msg = '<strong class="error">' . __('Invalid XML', 'wp_all_import_plugin') . '</strong><ul  class="error">';
			foreach($custom_xml_template_errors as $error) {
				$error_msg .= '<li>';
				$error_msg .= __('Line', 'wp_all_import_plugin') . ' ' . ($error->line + $line_difference) . ', ';
				$error_msg .= __('Column', 'wp_all_import_plugin') . ' ' . $error->column . ', ';
				$error_msg .= __('Code', 'wp_all_import_plugin') . ' ' . $error->code . ': ';
				$error_msg .= '<em>' . trim(esc_html($error->message)) . '</em>';
				$error_msg .= '</li>';
			}
			$error_msg .= '</ul>';
			echo $error_msg;
			exit( json_encode(array('html' => ob_get_clean())) );
		}

		$wp_uploads = wp_upload_dir();

		$functions = $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'functions.php';
		if ( @file_exists($functions) ) {
			require_once $functions;
		}

		switch ($exportOptions['export_to']) {

			case 'xml':

				$dom = new DOMDocument('1.0', $exportOptions['encoding']);
				libxml_use_internal_errors(true);
				try{
					$xml = XmlCsvExport::export_xml(true);
				} catch (WpaeMethodNotFoundException $e) {
					// Find the line where the function is
					$errorMessage = '';
					$functionName = $e->getMessage();
					$txtParts = explode("\n",$originalXmlTemplate);
					for ($i=0, $length = count($txtParts);$i<$length;$i++)
					{
						$tmp = strstr($txtParts[$i], $functionName);
						if ($tmp) {
							$errorMessage .= 'Error parsing XML feed: Call to undefined function <em>"'.$functionName.'"</em> on Line '.($i+1);
						}
					}

					$error_msg = '<span class="error">'.__($errorMessage, 'wp_all_import_plugin').'</span>';
					echo $error_msg;
					exit( json_encode(array('html' => ob_get_clean())) );
				} catch (WpaeInvalidStringException $e) {
					// Find the line where the function is
					$errorMessage = '';
					$functionName = $e->getMessage();
					$txtParts = explode("\n",$originalXmlTemplate);
					for ($i=0, $length = count($txtParts);$i<$length;$i++)
					{
						$tmp = strstr($txtParts[$i], $functionName);
						if ($tmp) {
							$errorMessage .= 'Error parsing XML feed: Unterminated string on line '.($i+1);
						}
					}

					$error_msg = '<span class="error">'.__($errorMessage, 'wp_all_import_plugin').'</span>';
					echo $error_msg;
					exit( json_encode(array('html' => ob_get_clean())) );
				} catch (WpaeTooMuchRecursionException $e) {
					$errorMessage = __('There was a problem parsing the custom XML template');
					$error_msg = '<span class="error">'.__($errorMessage, 'wp_all_import_plugin').'</span>';
					echo $error_msg;
					exit( json_encode(array('html' => ob_get_clean())) );
				}

                $xml_errors = false;

                $main_xml_tag = '';

                switch ( XmlExportEngine::$exportOptions['xml_template_type'] ){

                    case 'custom':
                        require_once PMXE_ROOT_DIR . '/classes/XMLWriter.php';

                        $preview_xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . "\n<Preview>\n" . $xml . "\n</Preview>";

                        $preview_xml = str_replace('<![CDATA[', 'CDATABEGIN', $preview_xml);
                        $preview_xml = str_replace(']]>', 'CDATACLOSE', $preview_xml);
                        $preview_xml = str_replace('&amp;', '&', $preview_xml);
                        $preview_xml = str_replace('&', '&amp;', $preview_xml);

                        $xml = PMXE_XMLWriter::preprocess_xml( XmlExportEngine::$exportOptions['custom_xml_template_header'] ) . "\n" . $xml . "\n" . PMXE_XMLWriter::preprocess_xml( XmlExportEngine::$exportOptions['custom_xml_template_footer'] );

                        $xml = str_replace('<![CDATA[', 'CDATABEGIN', $xml);
                        $xml = str_replace(']]>', 'CDATACLOSE', $xml);
                        $xml = str_replace('&amp;', '&', $xml);
                        $xml = str_replace('&', '&amp;', $xml);

                        // Determine XML root element
                        preg_match_all("%<[\w]+[\s|>]{1}%", XmlExportEngine::$exportOptions['custom_xml_template_header'], $matches);

                        if ( ! empty($matches[0]) ){
                          $main_xml_tag = preg_replace("%[\s|<|>]%","",array_shift($matches[0]));
                        }

                        libxml_clear_errors();
                        $dom->loadXML($xml);
                        $xml_errors = libxml_get_errors();
                        libxml_clear_errors();
                        if (! $xml_errors ){
                          $xpath = new DOMXPath($dom);
                          if (($elements = @$xpath->query('/' . $main_xml_tag)) and $elements->length){
                            pmxe_render_xml_element($elements->item( 0 ), true);
                          }
                          else{
                            $xml_errors = true;
                          }
                        }

                    break;

                    default:

                        libxml_clear_errors();
                        $dom->loadXML($xml);
                        $xml_errors = libxml_get_errors();
                        libxml_clear_errors();

                        $xpath = new DOMXPath($dom);

                        // Determine XML root element
                        $main_xml_tag = apply_filters('wp_all_export_main_xml_tag', $exportOptions['main_xml_tag'], XmlExportEngine::$exportID);
						$elements = @$xpath->query('/' . $main_xml_tag);
                        if ($elements->length){
                          pmxe_render_xml_element($elements->item( 0 ), true);
                          $xml_errors = false;
                        }
                        else{
                          $error_msg = '<strong>' . __('Can\'t preview the document.', 'wp_all_import_plugin') . '</strong><ul>';
                          $error_msg .= '<li>';
                          $error_msg .= __('You can continue export or try to use &lt;data&gt; tag as root element.', 'wp_all_import_plugin');
                          $error_msg .= '</li>';
                          $error_msg .= '</ul>';
                          echo $error_msg;
                          exit( json_encode(array('html' => ob_get_clean())) );
                        }
                    break;

                }

				if ( $xml_errors ){

					$preview_dom = new DOMDocument('1.0', $exportOptions['encoding']);
					libxml_clear_errors();
					$preview_dom->loadXML($preview_xml);
					$preview_xml_errors = libxml_get_errors();
					libxml_clear_errors();

					if ($preview_xml_errors){
						$error_msg = '<strong class="error">' . __('Invalid XML', 'wp_all_import_plugin') . '</strong><ul  class="error">';
						foreach($preview_xml_errors as $error) {
							$error_msg .= '<li>';
							$error_msg .= __('Line', 'wp_all_import_plugin') . ' ' . $error->line . ', ';
							$error_msg .= __('Column', 'wp_all_import_plugin') . ' ' . $error->column . ', ';
							$error_msg .= __('Code', 'wp_all_import_plugin') . ' ' . $error->code . ': ';
							$error_msg .= '<em>' . trim(esc_html($error->message)) . '</em>';
							$error_msg .= '</li>';
						}
						$error_msg .= '</ul>';
						echo $error_msg;
						exit( json_encode(array('html' => ob_get_clean())) );
					}
					else{
						$xpath = new DOMXPath($preview_dom);
						if (($elements = @$xpath->query('/Preview')) and $elements->length){
							pmxe_render_xml_element($elements->item( 0 ), true);
						}
						else{
							$error_msg = '<strong>' . __('Can\'t preview the document. Root element is not detected.', 'wp_all_import_plugin') . '</strong><ul>';
							$error_msg .= '<li>';
							$error_msg .= __('You can continue export or try to use &lt;data&gt; tag as root element.', 'wp_all_import_plugin');
							$error_msg .= '</li>';
							$error_msg .= '</ul>';
							echo $error_msg;
							exit( json_encode(array('html' => ob_get_clean())) );
						}
					}
				}

				break;

			case 'csv':
				?>
				<small>
				<?php

					$csv = XmlCsvExport::export_csv( true );

					if (!empty($csv)){
						$csv_rows = array_filter(explode("\n", $csv));
						if ($csv_rows){
							?>
							<table class="pmxe_preview" cellpadding="0" cellspacing="0">
							<?php
							foreach ($csv_rows as $rkey => $row) {
								$cells = str_getcsv($row, $exportOptions['delimiter']);
								if ($cells){
									?>
									<tr>
										<?php
										foreach ($cells as $key => $value) {
											?>
											<td>
												<?php if (!$rkey):?><strong><?php endif;?>
												<?php echo $value; ?>
												<?php if (!$rkey):?></strong><?php endif;?>
											</td>
											<?php
										}
										?>
									</tr>
									<?php
								}
							}
							?>
							</table>
							<?php
						}
					}
					else{
						_e('Data not found.', 'wp_all_export_plugin');
					}
				?>
				</small>
				<?php
				break;

			default:

				_e('This format is not supported.', 'wp_all_export_plugin');

				break;
		}
		wp_reset_postdata();
		?>

		</div>

	</div>

	<?php

	exit(json_encode(array('html' => ob_get_clean()))); die;
}
