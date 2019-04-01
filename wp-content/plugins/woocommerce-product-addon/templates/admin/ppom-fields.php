<?php 
/*
** PPOM New Form Meta
*/

/* 
**========== Direct access not allowed =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

	// get class instance
	$form_meta = PPOM_FIELDS_META();

	$productmeta_name 		= '';
	$enable_ajax_validation = '';
	$dynamic_price_hide  	= '';
	$send_file_attachment	= '';
	$show_cart_thumb		= '';
	$aviary_api_key 		= '';
	$productmeta_style 		= '';
	$productmeta_categories	= '';
	$product_meta_id = 0;
	$product_meta = array();
	$ppom_field_index = 1;
	
	if (isset ( $_REQUEST ['productmeta_id'] ) && $_REQUEST ['do_meta'] == 'edit') {
		
		$product_meta_id = $_REQUEST ['productmeta_id'];
		$ppom			 = new PPOM_Meta();
		$ppom_settings   = $ppom->get_settings_by_id($product_meta_id);
		
		$productmeta_name 		= (isset($ppom_settings -> productmeta_name) ? stripslashes($ppom_settings->productmeta_name) : '');
		$enable_ajax_validation = (isset($ppom_settings -> productmeta_validation) ? $ppom_settings -> productmeta_validation : '');
	    $dynamic_price_hide  	= (isset($ppom_settings -> dynamic_price_display) ? $ppom_settings -> dynamic_price_display : '');
	    $send_file_attachment  	= (isset($ppom_settings -> send_file_attachment) ? $ppom_settings -> send_file_attachment : '');
		$show_cart_thumb		= (isset($ppom_settings -> show_cart_thumb) ? $ppom_settings -> show_cart_thumb : '');
		$aviary_api_key 		= (isset($ppom_settings -> aviary_api_key) ? $ppom_settings -> aviary_api_key : '');
		$productmeta_style 		= (isset($ppom_settings -> productmeta_style) ? $ppom_settings -> productmeta_style : '');
		$productmeta_categories	= (isset($ppom_settings -> productmeta_categories) ? $ppom_settings -> productmeta_categories : '');
		$product_meta 			= json_decode ( $ppom_settings->the_meta, true );
		
		// var_dump ( $enable_ajax_validation  );
	}

	$url_cancel = add_query_arg(array('action'=>false,'productmeta_id'=>false, 'do_meta'=>false));
	
	echo '<p><a class="btn btn-primary" href="'.$url_cancel.'">'.__('&laquo; Existing Product Meta', "ppom").'</a></p>';

?>

<div class="ppom-admin-fields-wrapper">

	<!-- All fields inputs name show -->
	<div id="ppom_fields_model_id" class="ppom-modal-box ppom-fields-name-model">
	    <header> 
	        <h3><?php _e('Select Field', "ppom"); ?></h3>
	    </header>
	    <div class="ppom-modal-body">
	        <ul class="list-group list-inline">
                <?php
                foreach ( PPOM() -> inputs as $field_type => $meta ) {

                	if( $meta != NULL ){
                    	$fields_title = isset($meta -> title)? $meta -> title : null;
                    	$fields_icon = isset($meta -> icon)? $meta -> icon : null;
                    ?> 
	                    <li class="ppom_select_field list-group-item"  data-field-type="<?php echo esc_attr($field_type); ?>" >
	                        <span class="ppom-fields-icon">
	                        	<?php echo $fields_icon;  ?>
	                        </span>
	                        <span>
	                            <?php echo $fields_title;  ?>
	                        </span>
	                    </li>
                    <?php 
            		} 
                }
                ?>
            </ul>
	    </div>
	    <footer>
	    	<button type="button" class="btn btn-default close-model ppom-js-modal-close"><?php _e('Close' , "ppom"); ?></button>
	    </footer>
	</div>

	<div class="ppom-main-field-wrapper">
		<form class="ppom-save-fields-meta">

			<?php if ($product_meta_id != 0){ ?>
			<input type="hidden" name="action" value="ppom_update_form_meta">
			<?php }else{ ?>
			<input type="hidden" name="action" value="ppom_save_form_meta">
			<?php } ?>
			<input type="hidden" name="productmeta_id" value="<?php echo esc_attr($product_meta_id); ?>" >
			

			<div class="ppom-basic-setting-section">
				<h2 class="ppom-heading-style"><?php _e('Product Meta Basic Settings', "ppom"); ?><span></span></h2>
				<div class="row">
					<div class="col-md-6 col-sm-6">
						<div class="form-group">
							<label><?php _e('Meta group name', "ppom"); ?>
	                         	<span class="ppom-helper-icon" data-ppom-tooltip="ppom_tooltip" title="<?php _e('For your reference.', "ppom")?>" ><i class="dashicons dashicons-editor-help"></i></span>
	                     	</label>
							<input type="text" class="form-control" name="productmeta_name" value="<?php echo $productmeta_name?>">
						</div>
					</div>
					<div class="col-md-6 col-sm-6">
						<div class="form-group">
							<label><?php _e('Control price display on product page', "ppom"); ?>
	                         	<span class="ppom-helper-icon" data-ppom-tooltip="ppom_tooltip" title="<?php _e('Control how price table will be shown for options or disable.', "ppom")?>" ><i class="dashicons dashicons-editor-help"></i></span>
	                     	</label>
							<select name="dynamic_price_hide" class="form-control">
								<option value="no"><?php _e("Select Option", "ppom");?></option>
								<option value="hide" <?php selected($dynamic_price_hide, 'hide')?>><?php _e("Do Not Show Price Table", "ppom");?></option>
								<option value="option_sum" <?php selected($dynamic_price_hide, 'option_sum')?>><?php _e("Show Only Option's Total", "ppom");?></option>
								<option value="all_option" <?php selected($dynamic_price_hide, 'all_option')?>><?php _e("Show Each Option's Price", "ppom");?></option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6">
						<div class="form-group">
							<label><?php _e('Custom CSS', "ppom"); ?>
	                         	<span class="ppom-helper-icon" data-ppom-tooltip="ppom_tooltip" title="<?php _e('Add your own CSS.', "ppom")?>" ><i class="dashicons dashicons-editor-help"></i></span>
	                     	</label>
							<textarea id="ppom-css-editor" class="form-control" name="productmeta_style"><?php echo stripslashes($productmeta_style)?></textarea>
						</div>
					</div>
					<div class="col-md-6 col-sm-6">
						<div class="form-group">
							<label><?php _e('Apply for Categories', "ppom"); ?>
	                         	<span class="ppom-helper-icon" data-ppom-tooltip="ppom_tooltip" title="<?php _e('If you want to apply this meta against categories, type here each category SLUG per line. For All type: All. Leave blank for default.', "ppom")?>" ><i class="dashicons dashicons-editor-help"></i></span>
	                     	</label>
							<textarea class="form-control" name="productmeta_categories"><?php echo stripslashes($productmeta_categories)?></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 ppom-checkboxe-style">
						<div class="form-group">
							<label>
								<input type="checkbox" <?php checked($enable_ajax_validation, 'yes')?> name="enable_ajax_validation" value="yes" />
								<span><?php _e('Enable ajax based validation', "ppom"); ?></span>
	                         	<span class="ppom-helper-icon" data-ppom-tooltip="ppom_tooltip" title="<?php _e('Do not refresh the page until required data is provided.', "ppom")?>" ><i class="dashicons dashicons-editor-help"></i></span>
							</label>
						</div>
					</div>
				</div>
			</div>


		    <!-- saving all fields via model -->
		    <div class="ppom_save_fields_model">
		        <?php 
		        if ( $product_meta) {

		            $f_index = 1;
		            foreach ($product_meta as $field_index => $field_meta) {

		            	$field_type   = isset($field_meta['type']) ? $field_meta['type'] : '';
                        $the_title    = isset($field_meta['title']) ? $field_meta['title'] : '';
                        $the_field_id = isset($field_meta['data_name']) ? $field_meta['data_name'] : '';
                        $the_placeholder = isset($field_meta['placeholder']) ? $field_meta['placeholder'] : '';
                        $defualt_fields  = isset(PPOM() -> inputs[$field_type]-> settings) ? PPOM() -> inputs[$field_type]-> settings : array();

                        $defualt_fields = $form_meta->ppom_tabs_panel_classes($defualt_fields);
		        ?>

		                <!-- New PPOM Model  -->
		                <div id="ppom_field_model_<?php echo esc_attr($f_index); ?>" class="ppom-modal-box ppom-slider ppom_sort_id_<?php echo esc_attr($f_index); ?>">
						    <div class="ppom-model-content">
						    	
							    <header> 
							        <h3>
							        	<?php echo $field_type; ?>
							        	<span class="ppom-dataname-reader">(<?php echo $the_field_id; ?>)</span>
							        </h3>
							    </header>
							    <div class="ppom-modal-body">
							        <?php
		                            echo $form_meta->render_field_meta($defualt_fields, $field_type, $f_index, $field_meta);
		                        	?>
							    </div>
							    <footer> 
							        <span class="ppom-req-field-id"></span>
	                                <button type="button" class="btn btn-default close-model ppom-js-modal-close"><?php _e('Close', "ppom"); ?></button>
	                                <button class="btn btn-primary ppom-update-field ppom-add-fields-js-action" data-field-index='<?php echo esc_attr($f_index); ?>' data-field-type='<?php echo esc_attr($field_type); ?>' ><?php _e('Update Field', "ppom"); ?></button> 
							    </footer>
						    <?php 
	                        $ppom_field_index = $f_index;
	                        $ppom_field_index++;
	                        $f_index++;
	                        ?> 
							</div>
						</div>
		            <?php
		            }
		        }

		        echo '<input type="hidden" id="field_index" value="'.esc_attr($ppom_field_index).'">';
		        ?>
		    </div>

		    <!-- all fields append on table -->
		    <div class="table-responsive"> 
		    	<h2 class="ppom-heading-style"><?php _e('Add PPOM Fields', "ppom"); ?></h2>  
		        <table class="table ppom_field_table  table-striped">
		            <thead>
		                <tr>            
		                    <th colspan="6">
		                        <button type="button" class="btn btn-primary" data-modal-id="ppom_fields_model_id"><?php _e('Add field', "ppom"); ?></button>
		                        <button type="button" class="btn btn-danger ppom_remove_field"><?php _e('Remove', "ppom"); ?></button>
		                    </th>  
		                </tr>
		                <tr class="ppom-thead-bg">
		                    <th></th>
		                     <th class="ppom-check-all-field ppom-checkboxe-style">
								<label>
									<input type="checkbox">
									<span></span>
								</label>
		                    </th>
		                    <th><?php _e('Data Name', "ppom"); ?></th>
		                    <th><?php _e('Type', "ppom"); ?></th>
		                    <th><?php _e('Title', "ppom"); ?></th>
		                    <th><?php _e('Placeholder', "ppom"); ?></th>
		                    <th><?php _e('Required', "ppom"); ?></th>
		                    <th><?php _e('Actions', "ppom"); ?></th> 
		                </tr>                       
		            </thead>
		            <tfoot>
		                <tr class="ppom-thead-bg">
		                    <th></th>
		                    <th class="ppom-check-all-field ppom-checkboxe-style">
								<label>
									<input type="checkbox">
									<span></span>
								</label>
		                    </th>
		                    <th><?php _e('Data Name', "ppom"); ?></th>
		                    <th><?php _e('Type', "ppom"); ?></th>
		                    <th><?php _e('Title', "ppom"); ?></th>
		                    <th><?php _e('Placeholder', "ppom"); ?></th>
		                    <th><?php _e('Required', "ppom"); ?></th>
		                    <th><?php _e('Actions', "ppom"); ?></th>
		                </tr>
		                <tr>            
		                    <th colspan="12">
		                        <div class="ppom-submit-btn text-right">
		                        	<span class="ppom-meta-save-notice"></span>
		                            <input type="submit" class="btn btn-primary" value="Save Settings">
		                        </div>
		                    </th>
		                </tr> 
		            </tfoot>
		            <tbody>
	                <?php 
	                if ( $product_meta ) {

	                    $f_index = 1;
	                    foreach ($product_meta as $field_index => $field_meta) {

                            $field_type   = isset($field_meta['type']) ? $field_meta['type'] : '';
                            $the_title    = isset($field_meta['title']) ? $field_meta['title'] : '';
                            $the_field_id = isset($field_meta['data_name']) ? $field_meta['data_name'] : '';
                            $the_placeholder = isset($field_meta['placeholder']) ? $field_meta['placeholder'] : '';
                            $the_required = isset($field_meta['required']) ? $field_meta['required'] : '';
                            if ($the_required == 'on' ) {
                                $_ok = 'Yes';
                            }else{
                                $_ok = 'No';
                            }
	                ?>
	                        
	                        <tr class="row_no_<?php echo esc_attr($f_index); ?>" id="ppom_sort_id_<?php echo esc_attr($f_index); ?>">
                                <td class="ppom-sortable-handle">
                                    <i class="fa fa-arrows" aria-hidden="true"></i>
                                </td>
                                <td class="ppom-check-one-field ppom-checkboxe-style">
                                	<label>
										<input type="checkbox" value="<?php echo esc_attr($f_index); ?>">
										<span></span>
									</label>
                                </td>
                                <td class="ppom_meta_field_id"><?php echo $the_field_id; ?></td>
                                <td class="ppom_meta_field_type"><?php echo $field_type; ?></td>
                                <td class="ppom_meta_field_title"><?php echo $the_title; ?></td>
                                <td class="ppom_meta_field_plchlder"><?php echo $the_placeholder; ?></td>
                                <td class="ppom_meta_field_req"><?php echo $_ok; ?></td> 
                                <td>
                                    <button class="btn  ppom_copy_field" data-field-type="<?php echo esc_attr($field_type); ?>" title="<?php _e('Copy Field',"ppom"); ?>" id="<?php echo esc_attr($f_index); ?>"><i class="fa fa-clone" aria-hidden="true"></i></button>
                                    <button class="btn ppom-edit-field" data-modal-id="ppom_field_model_<?php echo esc_attr($f_index); ?>" id="<?php echo esc_attr($f_index); ?>" title="<?php _e('Edit Field',"ppom"); ?>"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                </td>
	                        </tr> 
	                        <?php   
	                        $ppom_field_index = $f_index;
	                        $ppom_field_index++;
	                        $f_index++;
	                    }
	                }
	            			?>
	            	</tbody>
		        </table>
		    </div>
		</form>
	</div>
</div>

<br><p><a class="btn btn-primary" href="<?php echo esc_url($url_cancel); ?>"><?php echo __('&laquo; Existing Product Meta', "ppom"); ?></a></p>

<div class="checker">
    <?php  $form_meta->render_field_settings( ); ?>
</div>