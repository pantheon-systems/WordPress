<?php
/**
 * PPOM Fields Manager Class
**/

/* 
**========== Direct access not allowed =========== 
*/ 
if( ! defined('ABSPATH') ) die('Not Allowed');
 

 class PPOM_Fields_Meta {
 
    private static $ins;
    

    function __construct() {
              
        add_action('admin_enqueue_scripts', array($this, 'load_script'));
    }
    

    public static function get_instance() {
        // create a new object if it doesn't exist.
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }
    

    /* 
    **============ Load all scripts =========== 
    */ 
    function load_script($hook) {

		if( ! isset($_GET['page']) || $_GET['page'] != "ppom") return;
        
        // Bootstrap Files
        wp_enqueue_style('PPOM-bs', PPOM_URL."/scripts/bootstrap.min.css");
        // wp_enqueue_script('PPOM-bs', PPOM_URL."/scripts/bootstrap.min.js", array('jquery'), PPOM_VERSION, true);        

        // Bulk Quantity Addon JS File
        wp_enqueue_script('PPOM-bulkquantity', PPOM_URL."/scripts/ppom-bulkquantity.js", array('jquery'), PPOM_VERSION, true);

        // PPOM Meta Table File
        wp_enqueue_script('PPOM-meta-table', PPOM_URL."/scripts/ppom-meta-table.js", array('jquery'), PPOM_VERSION, true);
        
        // Font-awesome File
        if( ppom_load_fontawesome() ) {
        	wp_enqueue_style('PPOM-fontawsome', PPOM_URL."/scripts/font-awesome/css/font-awesome.css");
        }

        // Swal Files
        wp_enqueue_style('PPOM-swal', PPOM_URL."/scripts/sweetalert.css");
        wp_enqueue_script('PPOM-swal', PPOM_URL."/scripts/sweetalert.js", array('jquery'), PPOM_VERSION, true); 
        
        // Select2 Files
        wp_enqueue_style('PPOM-select2', PPOM_URL."/scripts/select2.css");
        wp_enqueue_script('PPOM-select2', PPOM_URL."/scripts/select2.js", array('jquery'), PPOM_VERSION, true);
        
        // Tabletojson JS File 
        wp_enqueue_script('PPOM-tabletojson', PPOM_URL."/js/admin/jquery.tabletojson.min.js", array('jquery'), PPOM_VERSION, true);

        // Datatable Files
        wp_enqueue_style('PPOM-datatables', PPOM_URL."/js/datatable/datatables.min.css");
        wp_enqueue_script('PPOM-datatables', PPOM_URL."/js/datatable/jquery.dataTables.min.js", array('jquery'), PPOM_VERSION, true);

        // Description Tooltips JS File
        wp_enqueue_script('PPOM-tooltip', PPOM_URL."/scripts/ppom-tooltip.js", array('jquery'), PPOM_VERSION, true);

        // codemirror files
        wp_enqueue_style('PPOM-codemirror-css', PPOM_URL."/scripts/codemirror/codemirror.min.css", '', PPOM_VERSION);
        wp_enqueue_script('PPOM-codemirror-js', PPOM_URL."/scripts/codemirror/codemirror.js", array('jquery'), PPOM_VERSION, true);
        wp_enqueue_script('PPOM-codemirror-css-js', PPOM_URL."/scripts/codemirror/css.js", array('jquery'), PPOM_VERSION, true);
        
        // PPOM Admin Files
        wp_enqueue_style('PPOM-field', PPOM_URL."/scripts/ppom-admin.css", '', PPOM_VERSION);
        wp_enqueue_script('PPOM-field', PPOM_URL."/scripts/ppom-admin.js", array('PPOM-swal','PPOM-select2','PPOM-tabletojson','PPOM-datatables','PPOM-tooltip','jquery-ui-core', 'jquery-ui-sortable'), PPOM_VERSION, true);

		wp_enqueue_media ();

        $ppom_admin_meta = array(
	      'plugin_admin_page' => admin_url( 'admin.php?page=ppom'),
	      'loader'    => PPOM_URL.'/images/loading.gif',
	    );

        // localize ppom_vars
	    wp_localize_script( 'PPOM-field', 'ppom_vars', $ppom_admin_meta);
	    wp_localize_script( 'PPOM-meta-table', 'ppom_vars', $ppom_admin_meta);
    }


    /* 
    **============ Render all fields =========== 
    */
    function render_field_settings() {
        // ppom_pa(PPOM() -> inputs);
        
        $html  = '';        
        $html .= '<div id="ppom-fields-wrapper">';
        foreach( PPOM() -> inputs as $fields_type => $meta ) {
           	
           	$field_title = isset($meta -> title) ? $meta -> title : null;
           	$field_desc  = isset($meta -> desc) ? $meta -> desc : null;
           	$settings    = isset($meta -> settings) ? $meta -> settings : array();

           	$settings = $this->ppom_tabs_panel_classes($settings);

            // new model
            $html .= '<div class="ppom-modal-box ppom-slider ppom-field-'.esc_attr($fields_type).'">';
			    $html .= '<header>';
			        $html .= '<h3>'.sprintf(__("%s","ppom"), $field_title).'</h3>';
			    $html .= '</header>';
			    $html .= '<div class="ppom-modal-body">';

			        $html .= $this->render_field_meta($settings, $fields_type);

			    $html .= '</div>';
			    $html .= '<footer>';
			    	$html .= '<span class="ppom-req-field-id"></span>';
                   	$html .= '<button type="button" class="btn btn-default ppom-close-checker ppom-close-fields ppom-js-modal-close" style="margin-right: 5px;">'.esc_html__( 'close', 'ppom' ).'</button>';
                    $html .= '<button type="button" class="btn btn-primary ppom-field-checker ppom-add-field" data-field-type="'.esc_attr($field_title).'">'.esc_html__( 'Add Field', 'ppom' ).'</button>';
			    $html .= '</footer>';
			$html .= '</div>';
        }

        $html .= '</div>';
        echo $html;
    }

    /* 
    **============ Render all fields meta =========== 
    */
    function render_field_meta($field_meta, $fields_type, $field_index='', $save_meta='') {
    	// ppom_pa($save_meta);
    	$html  = '';
       	$html .= '<div data-table-id="'.esc_attr($fields_type).'" class="row ppom-tabs ppom-fields-actions" data-field-no="'.esc_attr($field_index).'">';
       		$html .= '<input type="hidden" name="ppom['.$field_index.'][type]" value="'.$fields_type.'" class="ppom-meta-field" data-metatype="type">';
			$html .= '<div class="col-md-12 ppom-tabs-header">';
				

				$ppom_field_tabs = $this->ppom_fields_tabs();
				foreach ($ppom_field_tabs as $tab_index => $tab_meta) {
					
					$tab_label  = isset($tab_meta['label']) ? $tab_meta['label'] : '';
					$tab_class  = isset($tab_meta['class']) ? $tab_meta['class'] : '';
					$tab_depend = isset($tab_meta['field_depend']) ? $tab_meta['field_depend'] : array();
					$not_allowed = isset($tab_meta['not_allowed']) ? $tab_meta['not_allowed'] : array();
					$tab_class  = implode(' ',$tab_class);

					if ( in_array('all', $tab_depend) && !in_array($fields_type, $not_allowed)) {
					
						$html .= '<label for="'.esc_attr($tab_index).'" id="'.esc_attr($tab_index).'" class="'.esc_attr($tab_class).'">'.$tab_label.'</label>';
					}else if( in_array($fields_type, $tab_depend) ){
						
						$html .= '<label for="'.esc_attr($tab_index).'" id="'.esc_attr($tab_index).'" class="'.esc_attr($tab_class).'">'.$tab_label.'</label>';
					}
				}

			
			$html .= '</div>';
        if ($field_meta) {
            
            foreach ($field_meta as $fields_meta_key => $meta) {
                
                $title      = isset($meta['title']) ? $meta['title'] : '';
                $desc       = isset($meta['desc']) ? $meta['desc'] : '';   
                $type       = isset($meta['type']) ? $meta['type'] : '';
                $link       = isset($meta['link']) ? $meta['link'] : '';
                $values     = isset($save_meta[$fields_meta_key]) ? $save_meta[$fields_meta_key] : '';

                $default_value		= isset($meta ['default']) ? $meta ['default'] : '';
                // ppom_pa($fields_meta_key);
			
				if ( empty( $values) ){
					$values = $default_value;
				}


				$panel_classes = isset($meta['tabs_class']) ? $meta['tabs_class'] : array('ppom_handle_fields_tab', 'col-md-6', 'col-sm-6');
				$panel_classes[] = 'ppom-control-all-fields-tabs';

				if ($type == 'checkbox') {
					$panel_classes[] = 'ppom-checkboxe-style';
				}
				if (!empty($panel_classes)) {
					$panel_classes = implode(' ',$panel_classes);
				}

                $html .= '<div data-meta-id="'.esc_attr($fields_meta_key).'" class="'.esc_attr($panel_classes).'">';
	                $html .= '<div class="form-group">';

	                    $html .= '<label>'.sprintf(__("%s","ppom"), $title).'';
	                        $html .= '<span class="ppom-helper-icon" data-ppom-tooltip="ppom_tooltip" title="'.sprintf(__("%s","ppom"),$desc).'">';
	                            $html .= '<i class="dashicons dashicons-editor-help"></i>';
	                        $html .= '</span>'.$link.'';
	                    $html .= '</label>';
	                    $html .= $this-> render_all_input_types( $fields_meta_key, $meta, $fields_type, $field_index, $values );

	                $html .= '</div>';
	            $html .= '</div>';
                  
            }
        }

        $html .= '</div>';

        return $html;        
    }


	/*
	* this function is rendring input field for settings
	*/
	function render_all_input_types($name, $data, $fields_type, $field_index, $values ) {
		// ppom_pa($values);

		$type		   = (isset( $data ['type'] ) ? $data ['type'] : '');
		
		$options	   = (isset( $data ['options'] ) ? $data ['options'] : '');
		$placeholders  = isset($data['placeholders']) ? $data['placeholders'] : '';
		
		$existing_name = 'name="ppom['.esc_attr($field_index).']['.esc_attr($name).']"';

		$plugin_meta   = ppom_get_plugin_meta();
		$html_input    = '';
		
		if(!is_array($values))
			$values = stripslashes($values);
		
		switch ($type) {
			
			case 'number':
			case 'text' :
				// ppom_pa($values);
				$html_input .= '<input data-metatype="'.esc_attr($name).'" type="'.esc_attr($type).'"  value="' . esc_html( $values ). '" class="form-control ppom-meta-field"';

				if( $field_index != '') {

                  $html_input .= $existing_name;
                }

				$html_input .= '>';
				break;
			
			case 'textarea' :

				$html_input .= '<textarea data-metatype="'.esc_attr($name).'" class="form-control ppom-meta-field ppom-adjust-box-height"';
				
				if( $field_index != '') {

                  $html_input .= $existing_name;
                }
				
				$html_input .= '>' . esc_html( $values ) . '</textarea>';

				break;
			
			case 'select' :

				$html_input .= '<select id="'.$name.'" data-metatype="'.esc_attr($name).'" class="form-control ppom-meta-field"';
				
				if( $field_index != '') {

                  $html_input .= $existing_name;
                }

				$html_input .= '>';

				foreach ( $options as $key => $val ) {
					$selected = ($key == $values) ? 'selected="selected"' : '';
					$html_input .= '<option value="' . $key . '" ' . $selected . '>' . esc_html( $val ) . '</option>';
				}
				$html_input .= '</select>';

				break;
			
			case 'paired' :
				
				$plc_option = (!empty($placeholders)) ? $placeholders[0] : __('Option',"ppom");
				$plc_price = (!empty($placeholders)) ? $placeholders[1] : __('Price (optional)', "ppom");
			
				$weight_unit = get_option('woocommerce_weight_unit');
				$plc_weight = (isset($placeholders[2]) && !empty($placeholders)) ? $placeholders[2] : __("Weight-{$weight_unit} (PRO only)", "ppom");
				if( ppom_pro_is_installed() ) {
					$plc_weight = (isset($placeholders[2]) && !empty($placeholders)) ? $placeholders[2] : __("Weight-{$weight_unit} (optional)", "ppom");
				}
			
				$plc_id = (isset($placeholders[3]) && !empty($placeholders)) ? $placeholders[3] : __('Unique Option ID)', "ppom");

				$opt_index0  = 1;
				$html_input .= '<ul class="ppom-options-container ppom-options-sortable">';
				
				if($values){
					// ppom_pa($values);
					$last_array_id = max(array_keys($values));

					foreach ($values as $opt_index => $option){

						$weight = isset($option['weight']) ? $option['weight'] : '';
						
						$option_id = ppom_get_option_id($option);
						$html_input .= '<li class="data-options ppom-sortable-handle" style="display: flex;">';
							$html_input .= '<span class="dashicons dashicons-move"></span>';
							$html_input .= '<input type="text" class="option-title form-control ppom-option-keys" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][option]" value="'.esc_attr(stripslashes($option['option'])).'" placeholder="'.$plc_option.'" data-metatype="option">';
							$html_input .= '<input type="text" class="option-price form-control ppom-option-keys" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][price]" value="'.esc_attr($option['price']).'" placeholder="'.$plc_price.'" data-metatype="price">';
							

							$html_input .= '<input type="text" class="option-weight form-control ppom-option-keys" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][weight]" value="'.esc_attr($weight).'" placeholder="'.$plc_weight.'" data-metatype="weight">';

							$html_input .= '<input type="text" class="option-id form-control ppom-option-keys" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][id]" value="'.esc_attr($option_id).'" placeholder="'.$plc_id.'" data-metatype="id">';
							$html_input .= '<button class="btn btn-success ppom-add-option" data-option-type="paired"><i class="fa fa-plus" aria-hidden="true"></i></button>';
						$html_input .= '</li>';

						$opt_index0 =  $last_array_id;
                    	$opt_index0++;

					}
				}else{
					$html_input .= '<li class="data-options" style="display: flex;">';
						$html_input .= '<span class="dashicons dashicons-move"></span>';
						$html_input .= '<input type="text" class="option-title form-control ppom-option-keys" placeholder="'.$plc_option.'" data-metatype="option">';
						$html_input .= '<input type="text" class="option-price form-control ppom-option-keys" placeholder="'.$plc_price.'" data-metatype="price">';
						
						$html_input .= '<input type="text" class="option-weight form-control ppom-option-keys" placeholder="'.$plc_weight.'" data-metatype="weight">';

						$html_input .= '<input type="text" class="option-id form-control ppom-option-keys" placeholder="'.$plc_id.'" data-metatype="id">';
						$html_input .= '<button class="btn btn-success ppom-add-option" data-option-type="paired"><i class="fa fa-plus" aria-hidden="true"></i></button>';
					$html_input .= '</li>';
				}
				$html_input .= '<input type="hidden" id="ppom-meta-opt-index" value="'.esc_attr($opt_index0).'">';
				$html_input	.= '<ul/>';
				
				break;


				case 'font_paired' :
				
				$plc_option = (!empty($placeholders)) ? $placeholders[0] : __('Data Name',"ppom");
				$plc_price = (!empty($placeholders)) ? $placeholders[1] : __('Font Name', "ppom");
			
				$opt_index0  = 1;
				$html_input .= '<ul class="ppom-options-container ppom-options-sortable">';
				
				if($values){
					$last_array_id = max(array_keys($values));

					foreach ($values as $opt_index => $option){

						$weight = isset($option['weight']) ? $option['weight'] : '';
						
						$html_input .= '<li class="data-options ppom-sortable-handle" style="display: flex;">';
							$html_input .= '<span class="dashicons dashicons-move"></span>';
							$html_input .= '<input type="text" class="option-title form-control ppom-option-keys" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][dataname]" value="'.esc_attr(stripslashes($option['dataname'])).'" placeholder="'.$plc_option.'" data-metatype="dataname">';
							$html_input .= '<input type="text" class="form-control ppom-option-keys" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][font_name]" value="'.esc_attr($option['font_name']).'" placeholder="'.$plc_price.'" data-metatype="font_name">';
							

							$html_input .= '<button class="btn btn-success ppom-add-option" data-option-type="paired"><i class="fa fa-plus" aria-hidden="true"></i></button>';
						$html_input .= '</li>';

						$opt_index0 =  $last_array_id;
                    	$opt_index0++;

					}
				}else{
					$html_input .= '<li class="data-options" style="display: flex;">';
						$html_input .= '<span class="dashicons dashicons-move"></span>';
						$html_input .= '<input type="text" class="option-title form-control ppom-option-keys" placeholder="'.$plc_option.'" data-metatype="dataname">';
						$html_input .= '<input type="text" class="form-control ppom-option-keys" placeholder="'.$plc_price.'" data-metatype="font_name">';

						$html_input .= '<button class="btn btn-success ppom-add-option" data-option-type="paired"><i class="fa fa-plus" aria-hidden="true"></i></button>';
					$html_input .= '</li>';
				}
				$html_input .= '<input type="hidden" id="ppom-meta-opt-index" value="'.esc_attr($opt_index0).'">';
				$html_input	.= '<ul/>';
				
				break;
				
			case 'paired-quantity' :
				
				$opt_index0  = 1;
				$html_input .= '<ul class="ppom-options-container">';
				
				if($values){

					$last_array_id = max(array_keys($values));

					foreach ($values as $opt_index => $option){
						$html_input .= '<li class="data-options" style="display: flex;">';
							$html_input .= '<span class="dashicons dashicons-move"></span>';
							$html_input .= '<input type="text" class="form-control ppom-option-keys" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][option]" value="'.esc_attr(stripslashes($option['option'])).'" placeholder="'.__('option',"ppom").'" data-metatype="option">';
							$html_input .= '<input type="text" class="form-control ppom-option-keys" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][price]" value="'.esc_attr($option['price']).'" placeholder="'.__('price (if any)',"ppom").'" data-metatype="price" >';
							$html_input .= '<input type="text" class="form-control ppom-option-keys" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][min]" value="'.esc_attr($option['min']).'" placeholder="'.__('Min. Qty',"ppom").'" data-metatype="min" >';
							$html_input .= '<input type="text" class="form-control ppom-option-keys" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][max]" value="'.esc_attr($option['max']).'" placeholder="'.__('Max. Qty',"ppom").'" data-metatype="max">';
							$html_input .= '<button class="btn btn-success ppom-add-option" data-option-type="paired-quantity"><i class="fa fa-plus" aria-hidden="true"></i></button>';
						$html_input .= '</li>';

						$opt_index0 =  $last_array_id;
                    	$opt_index0++;
					}
				}else{
					$html_input .= '<li class="data-options" style="display: flex;">';
						$html_input .= '<span class="dashicons dashicons-move"></span>';
						$html_input .= '<input type="text" class="form-control ppom-option-keys" placeholder="'.__('option',"ppom").'" data-metatype="option">';
						$html_input .= '<input type="text" class="form-control ppom-option-keys" placeholder="'.__('price (if any)',"ppom").'" data-metatype="price">';
						$html_input .= '<input type="text" class="form-control ppom-option-keys" placeholder="'.__('Min. Qty',"ppom").'" data-metatype="min">';
						$html_input .= '<input type="text" class="form-control ppom-option-keys" placeholder="'.__('Max. Qty',"ppom").'" data-metatype="max">';
						$html_input .= '<button class="btn btn-success ppom-add-option" data-option-type="paired-quantity"><i class="fa fa-plus" aria-hidden="true"></i></button>';
					$html_input .= '</li>';
				}
				
				$html_input .= '<input type="hidden" id="ppom-meta-opt-index" value="'.esc_attr($opt_index0).'">';
				$html_input	.= '<ul/>';
				
				break;
				
			case 'paired-measure' :
				
				$html_input .= '<ul class="ppom-options-container">';
				
				$add_option_img = $plugin_meta['url'].'/images/plus.png';
				$del_option_img = $plugin_meta['url'].'/images/minus.png';
				$plc_id = (!empty($placeholders)) ? $placeholders[2] : __('Unique ID)', "ppom");
				
				if($value){
					foreach ($value as $option){
						
						$option_id = ppom_get_option_id($option);
						
						$html_input .= '<li class="data-options">';
						$html_input .= '<span class="dashicons dashicons-move"></span>';
						$html_input .= '<input type="text" name="options[option]" value="'.esc_attr(stripslashes($option['option'])).'" placeholder="'.__('Unit',"ppom").'">';
						$html_input .= '<input type="text" name="options[price]" value="'.esc_attr($option['price']).'" placeholder="'.__('price (if any)',"ppom").'">';
						$html_input .= '<input type="text" class="option-id" name="options[id]" value="'.esc_attr($option_id).'" placeholder="'.$plc_id.'">';
						$html_input	.= '<img class="add_option" src="'.esc_url($add_option_img).'" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
						$html_input	.= '<img class="remove_option" src="'.esc_url($del_option_img).'" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
						$html_input .= '</li>';
					}
				}else{
					$html_input .= '<li class="data-options">';
					$html_input .= '<span class="dashicons dashicons-move"></span>';
					$html_input .= '<input type="text" name="options[option]" placeholder="'.__('Unit',"ppom").'">';
					$html_input .= '<input type="text" name="options[price]" placeholder="'.__('price (if any)',"ppom").'">';
					$html_input .= '<input type="text" class="option-id" name="options[id]" placeholder="'.$plc_id.'">';
					$html_input	.= '<img class="add_option" src="'.esc_url($add_option_img).'" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
					$html_input	.= '<img class="remove_option" src="'.esc_url($del_option_img).'" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
					$html_input .= '</li>';
				}
				
				$html_input	.= '<ul/>';
				
				break;
				
			case 'paired-cropper' :
				
				$opt_index0  = 1;
				$html_input .= '<ul class="ppom-options-container ppom-cropper-boundary">';
				
				if($values){
					// ppom_pa($values);
					$last_array_id = max(array_keys($values));
					foreach ($values as $opt_index => $option){
												
						$html_input .= '<li class="data-options" style=display:flex;>';
							$html_input .= '<span class="dashicons dashicons-move"></span>';
							$html_input .= '<input type="text" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][option]" value="'.esc_attr(stripslashes($option['option'])).'" placeholder="'.__('Label',"ppom").'" class="form-control ppom-option-keys" data-metatype="option">';
							$html_input .= '<input type="text" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][width]" value="'.esc_attr(stripslashes($option['width'])).'" placeholder="'.__('Width',"ppom").'" class="form-control ppom-option-keys" data-metatype="width">';
							$html_input .= '<input type="text" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][height]" value="'.esc_attr($option['height']).'" placeholder="'.__('Height',"ppom").'" class="form-control ppom-option-keys" data-metatype="height">';
							$html_input .= '<input type="text" name="ppom['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][price]" value="'.esc_attr($option['price']).'" placeholder="'.__('Price (optional)',"ppom").'" class="form-control ppom-option-keys" data-metatype="price">';
							$html_input .= '<button class="btn btn-success ppom-add-option" data-option-type="paired-cropper"><i class="fa fa-plus" aria-hidden="true"></i></button>';
						$html_input .= '</li>';

						$opt_index0 =  $last_array_id;
                    	$opt_index0++;
					}
				}else{
					$html_input .= '<li class="data-options" style=display:flex;>';
						$html_input .= '<span class="dashicons dashicons-move"></span>';
						$html_input .= '<input type="text" placeholder="'.__('option',"ppom").'" class="form-control ppom-option-keys" data-metatype="option">';
						$html_input .= '<input type="text" placeholder="'.__('Width',"ppom").'" class="form-control ppom-option-keys" data-metatype="width">';
						$html_input .= '<input type="text" placeholder="'.__('Height',"ppom").'" class="form-control ppom-option-keys" data-metatype="height">';
						$html_input .= '<input type="text" placeholder="'.__('Price (optional)',"ppom").'" class="form-control ppom-option-keys" data-metatype="price">';
						$html_input .= '<button class="btn btn-success ppom-add-option" data-option-type="paired-cropper"><i class="fa fa-plus" aria-hidden="true"></i></button>';
					$html_input .= '</li>';
				}
					$html_input .= '<input type="hidden" id="ppom-meta-opt-index" value="'.esc_attr($opt_index0).'">';
				$html_input	.= '<ul/>';
				
				break;
				
			case 'checkbox' :
				
				if ($options) {
					foreach ( $options as $key => $val ) {
						
						parse_str ( $values, $saved_data );
						$checked = '';
						if ( isset( $saved_data ['editing_tools'] ) && $saved_data ['editing_tools']) {
							if (in_array($key, $saved_data['editing_tools'])) {
								$checked = 'checked="checked"';
							}else{
								$checked = '';
							}
						}
						
						// For event Calendar Addon
						if ( isset( $saved_data ['cal_addon_disable_days'] ) && $saved_data ['cal_addon_disable_days']) {
							if (in_array($key, $saved_data['cal_addon_disable_days'])) {
								$checked = 'checked="checked"';
							}else{
								$checked = '';
							}
						}
						// $html_input .= '<option value="' . $key . '" ' . $selected . '>' . $val . '</option>';
						$html_input .= '<label style="float:left;">';
							$html_input .= '<input type="checkbox" value="' . $key . '" name="ppom['.esc_attr($field_index).']['.esc_attr($name).'][]" ' . $checked . '> ' . $val . '<br>';
							$html_input .= '<span></span>';
						$html_input .= '</label>';
					}
				} else {
					$checked = ( (isset($values) && $values != '' ) ? 'checked = "checked"' : '' );
						
						$html_input .= '<label style="float:left;">';
							$html_input .= '<input type="checkbox" class="ppom-meta-field" data-metatype="'.esc_attr($name).'" ' . $checked . '';
					
							if( $field_index != '') {

		                  		$html_input .= $existing_name;
		                	}
					
							$html_input .= '>';
							
							$html_input .= '<span></span>';
						$html_input .= '</label>';

				}
				break;
				
			case 'html-conditions' :
				
				$condition_index = 1;
				$rule_i = 1;
				if($values){
					// ppom_pa($values);
					$condition_rules = isset($values['rules']) ? $values['rules'] : array();
					$last_array_id   = max(array_keys($condition_rules));

					$visibility_show = ($values['visibility'] == 'Show') ? 'selected="selected"' : '';
					$visibility_hide = ($values['visibility'] == 'Hide') ? 'selected="selected"' : '';
					$bound_all       = ($values['bound'] == 'All') ? 'selected="selected"' : '';
					$bound_any       = ($values['bound'] == 'Any') ? 'selected="selected"' : '';
					
					$html_input	= '<div class="row ppom-condition-style-wrap">';
						$html_input	.= '<div class="col-md-3 col-sm-3">';
							$html_input	.= '<select name="ppom['.esc_attr($field_index).'][conditions][visibility]" class="form-control ppom-condition-visible-bound" data-metatype="visibility">';
								$html_input .= '<option '.$visibility_show.'>'.__( 'Show', 'ppom' ).'</option>';
								$html_input .= '<option '.$visibility_hide.'>'.__( 'Hide', 'ppom' ).'</option>';
							$html_input	.= '</select>';
						$html_input .= '</div>';

						$html_input	.= '<div class="col-md-2 col-sm-2">';
							$html_input .= '<p>'.__( 'only if', 'ppom' ).'</p>';
						$html_input .= '</div>';

						$html_input	.= '<div class="col-md-3 col-sm-3">';
							$html_input	.= '<select name="ppom['.esc_attr($field_index).'][conditions][bound]" class="form-control ppom-condition-visible-bound" data-metatype="bound">';
								$html_input .= '<option '.$bound_all.'>'.__( 'All', 'ppom' ).'</option>';
								$html_input .= '<option '.$bound_any.'>'.__( 'Any', 'ppom' ).'</option>';
							$html_input	.= '</select>';
						$html_input .= '</div>';

						$html_input	.= '<div class="col-md-4 col-sm-4">';
							$html_input .='<p>'.__( 'of the following matches', 'ppom' ).'</p>';
						$html_input .= '</div>';
					$html_input .= '</div>';

					$html_input .= '<div class="row ppom-condition-clone-js">';
					foreach ($condition_rules as $rule_index => $condition){

						$element_values   = isset($condition['element_values']) ? stripslashes($condition['element_values']) : '';
						$element          = isset($condition['elements']) ? stripslashes($condition['elements']) : '';
						$operator_is 	  = ($condition['operators'] == 'is') ? 'selected="selected"' : '';
						$operator_not 	  = ($condition['operators'] == 'not') ? 'selected="selected"' : '';
						$operator_greater = ($condition['operators'] == 'greater than') ? 'selected="selected"' : '';
						$operator_less 	  = ($condition['operators'] == 'less than') ? 'selected="selected"' : '';
						
							$html_input .= '<div class="webcontact-rules" id="rule-box-'.esc_attr($rule_i).'">';
								$html_input .= '<div class="col-md-12 col-sm-12"><label>'.__('Rule ', "ppom") . $rule_i++ .'</label></div>';
								
								// conditional elements
								$html_input .= '<div class="col-md-4 col-sm-4">';
									$html_input .= '<select name="ppom['.esc_attr($field_index).'][conditions][rules]['.esc_attr($rule_index).'][elements]" class="form-control ppom-conditional-keys" data-metatype="elements"
										data-existingvalue="'.esc_attr($element).'" >';
										$html_input .= '<option>'.$element.'</option>';
									$html_input .= '</select>';
								$html_input .= '</div>';

								// is value meta
								$html_input .= '<div class="col-md-2 col-sm-2">';
									$html_input .= '<select name="ppom['.esc_attr($field_index).'][conditions][rules]['.esc_attr($rule_index).'][operators]" class="form-control ppom-conditional-keys" data-metatype="operators">';
										$html_input	.= '<option '.$operator_is.'>'. __('is', "ppom").'</option>';
										$html_input .= '<option '.$operator_not.'>'. __('not', "ppom").'</option>';
										$html_input .= '<option '.$operator_greater.'>'. __('greater than', "ppom").'</option>';
										$html_input .= '<option '.$operator_less.'>'. __('less than', "ppom").'</option>';
									$html_input	.= '</select> ';
								$html_input .= '</div>';

								// conditional elements values
								$html_input .= '<div class="col-md-4 col-sm-4">';
									$html_input .= '<input type="text" name="ppom['.esc_attr($field_index).'][conditions][rules]['.esc_attr($rule_index).'][element_values]" class="form-control ppom-conditional-keys" value="'.esc_attr($element_values).'" placeholder="Enter Option" data-metatype="element_values">';
								$html_input .= '</div>';

								// Add and remove btn
								$html_input .= '<div class="col-md-2 col-sm-2">';
									$html_input .= '<button class="btn btn-success ppom-add-rule" data-index="5"><i class="fa fa-plus" aria-hidden="true"></i></button>';
								$html_input .= '</div>';
							$html_input .= '</div>';

						$condition_index = $last_array_id;
                    	$condition_index++;
					}
					$html_input .= '</div>';
				}else{

					$html_input .= '<div class="row ppom-condition-style-wrap">';
						$html_input	.= '<div class="col-md-4 col-sm-4">';
							$html_input	.= '<select class="form-control ppom-condition-visible-bound" data-metatype="visibility">';
								$html_input .= '<option>'.__('Show', "ppom").'</option>';
								$html_input .= '<option>'. __('Hide', "ppom").'</option>';
							$html_input	.= '</select> ';
						$html_input .= '</div>';
						$html_input	.= '<div class="col-md-4 col-sm-4">';
							$html_input	.= '<select class="form-control ppom-condition-visible-bound" data-metatype="bound">';
								$html_input .= '<option>'. __('All', "ppom").'</option>';
								$html_input .= '<option>'. __('Any', "ppom").'</option>';
							$html_input	.= '</select> ';
						$html_input .= '</div>';
						$html_input	.= '<div class="col-md-4 col-sm-4">';
							$html_input .='<p>'. __(' of the following matches', "ppom").'</p>';
						$html_input .= '</div>';
					$html_input .= '</div>';

					$html_input .= '<div class="row ppom-condition-clone-js">';
						$html_input .= '<div class="webcontact-rules" id="rule-box-'.esc_attr($rule_i).'">';
							$html_input .= '<div class="col-md-12 col-sm-12"><label>'.__('Rule ', "ppom") . $rule_i++ .'</label></div>';
							
							// conditional elements
							$html_input .= '<div class="col-md-4 col-sm-4">';
								$html_input .= '<select data-metatype="elements" class="ppom-conditional-keys form-control"></select>';
							$html_input .= '</div>';
							
							// is
							$html_input .= '<div class="col-md-2 col-sm-2">';
								$html_input .= '<select data-metatype="operators" class="ppom-conditional-keys form-control">';
									$html_input	.= '<option>'. __('is', "ppom").'</option>';
									$html_input .= '<option>'. __('not', "ppom").'</option>';
									$html_input .= '<option>'. __('greater than', "ppom").'</option>';
									$html_input .= '<option>'. __('less than', "ppom").'</option>';
								$html_input	.= '</select> ';
							$html_input .= '</div>';

							// conditional elements values
							$html_input .= '<div class="col-md-4 col-sm-4">';
								$html_input .= '<input type="text" class="form-control ppom-conditional-keys" placeholder="Enter Option" data-metatype="element_values">';
							$html_input .= '</div>';

							// Add and remove btn
							$html_input .= '<div class="col-md-2 col-sm-2">';
								$html_input .= '<button class="btn btn-success ppom-add-rule" data-index="5"><i class="fa fa-plus" aria-hidden="true"></i></button>';
							$html_input .= '</div>';

						$html_input .= '</div>';
					$html_input .= '</div>';
				}
				$html_input .= '<input type="hidden" class="ppom-condition-last-id" value="'.esc_attr($condition_index).'">';

				break;
				
				case 'pre-images' :
				
					$html_input	.= '<div class="pre-upload-box table-responsive">';
					
						$html_input	.= '<button class="btn btn-info ppom-pre-upload-image-btn" data-metatype="images">'.__('Select/Upload Image', "ppom").'</button>';
						// ppom_pa($value);

						$opt_index0  = 0;
						$html_input .= '<ul class="ppom-options-container">';
						if ($values) {
							
							$last_array_id = max(array_keys($values));

							foreach ($values as $opt_index => $pre_uploaded_image){
						
								$image_link = (isset($pre_uploaded_image['link']) ? $pre_uploaded_image['link'] : '');
								$image_id   = (isset($pre_uploaded_image['id']) ? $pre_uploaded_image['id'] : '');
								$image_url  = (isset($pre_uploaded_image['url']) ? $pre_uploaded_image['url'] : '');
								
								$image_name = isset($pre_uploaded_image['link']) ? basename($pre_uploaded_image['link']) : '';

								$html_input .= '<li class="data-options">';
									$html_input .= '<span class="dashicons dashicons-move" style="margin-bottom: 7px;margin-top: 2px;"></span>';	
									$html_input .= '<span class="ppom-uploader-img-title">'.$image_name.'</span>';
									$html_input .= '<div style="display: flex;">';
										$html_input .= '<div class="ppom-uploader-img-center">';
											$html_input .= '<img width="60" src="'.esc_url($image_link).'" style="width: 34px;">';
										$html_input .= '</div>';
										$html_input .= '<input type="hidden" name="ppom['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][link]" value="'.esc_url($image_link).'">';
										$html_input .= '<input type="hidden" name="ppom['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][id]" value="'.esc_attr($image_id).'">';
										$html_input .= '<input class="form-control" type="text" placeholder="Title" value="'.esc_attr(stripslashes($pre_uploaded_image['title'])).'" name="ppom['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][title]">';
										$html_input .= '<input class="form-control" type="text" placeholder="Price (fix or %)" value="'.esc_attr(stripslashes($pre_uploaded_image['price'])).'" name="ppom['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][price]">';
										$html_input .= '<input class="form-control" type="text" placeholder="URL" value="'.esc_url(stripslashes($pre_uploaded_image['url'])).'" name="ppom['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][url]">';
										$html_input .= '<button class="btn btn-danger ppom-pre-upload-delete" style="height: 35px;"><i class="fa fa-times" aria-hidden="true"></i></button>';
									$html_input .= '</div>';
								$html_input .= '</li>';

								$opt_index0 =  $last_array_id;
	                    		$opt_index0++;
							}
						}
						$html_input .= '</ul>';
						$html_input .= '<input type="hidden" id="ppom-meta-opt-index" value="'.esc_attr($opt_index0).'">';
					
					$html_input .= '</div>';
				
				break;

				case 'imageselect' :
				
					$html_input	.= '<div class="pre-upload-box table-responsive">';
					
						$html_input	.= '<button class="btn btn-info ppom-pre-upload-image-btn" data-metatype="imageselect">'.__('Select/Upload Image', "ppom").'</button>';

						$opt_index0  = 0;
						$html_input .= '<ul class="ppom-options-container">';
						if ($values) {
							
							$last_array_id = max(array_keys($values));

							foreach ($values as $opt_index => $pre_uploaded_image){
						
								$image_link = (isset($pre_uploaded_image['link']) ? $pre_uploaded_image['link'] : '');
								$image_id   = (isset($pre_uploaded_image['id']) ? $pre_uploaded_image['id'] : '');
								$image_description  = (isset($pre_uploaded_image['description']) ? $pre_uploaded_image['description'] : '');
								
								$image_name = isset($pre_uploaded_image['link']) ? basename($pre_uploaded_image['link']) : '';

								$html_input .= '<li class="data-options">';
									$html_input .= '<span class="dashicons dashicons-move" style="margin-bottom: 7px;margin-top: 2px;"></span>';	
									$html_input .= '<span class="ppom-uploader-img-title">'.$image_name.'</span>';
									$html_input .= '<div style="display: flex;">';
										$html_input .= '<div class="ppom-uploader-img-center">';
											$html_input .= '<img width="60" src="'.esc_url($image_link).'" style="width: 34px;">';
										$html_input .= '</div>';
										$html_input .= '<input type="hidden" name="ppom['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][link]" value="'.esc_url($image_link).'">';
										$html_input .= '<input type="hidden" name="ppom['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][id]" value="'.esc_attr($image_id).'">';
										$html_input .= '<input class="form-control" type="text" placeholder="Title" value="'.esc_attr(stripslashes($pre_uploaded_image['title'])).'" name="ppom['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][title]">';
										$html_input .= '<input class="form-control" type="text" placeholder="Price" value="'.esc_attr(stripslashes($pre_uploaded_image['price'])).'" name="ppom['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][price]">';
										$html_input .= '<input class="form-control" type="text" placeholder="Description" value="'.esc_attr($image_description).'" name="ppom['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][description]">';
										$html_input .= '<button class="btn btn-danger ppom-pre-upload-delete" style="height: 35px;"><i class="fa fa-times" aria-hidden="true"></i></button>';
									$html_input .= '</div>';
								$html_input .= '</li>';

								$opt_index0 =  $last_array_id;
	                    		$opt_index0++;
							}
						}
						$html_input .= '</ul>';
						$html_input .= '<input type="hidden" id="ppom-meta-opt-index" value="'.esc_attr($opt_index0).'">';
					
					$html_input .= '</div>';
				
				break;
				
				case 'pre-audios' :
				
					$html_input	.= '<div class="pre-upload-box">';
					$html_input	.= '<button class="btn btn-info ppom-pre-upload-image-btn" data-metatype="audio">'.__('Select Audio/Video', "ppom").'</button>';
					
					$html_input .= '<ul class="ppom-options-container">';
					$opt_index0  = 0;
						// ppom_pa($values);
					if ($values) {
						$last_array_id = max(array_keys($values));
						foreach ($values as $opt_index => $pre_uploaded_image){
					
							$image_link  = (isset($pre_uploaded_image['link']) ? $pre_uploaded_image['link'] : '');
							$image_id    = (isset($pre_uploaded_image['id']) ? $pre_uploaded_image['id'] : '');
							$image_url   = (isset($pre_uploaded_image['url']) ? $pre_uploaded_image['url'] : '');
							$media_title = (isset($pre_uploaded_image['title']) ? stripslashes($pre_uploaded_image['title']) : '');
							$media_price = (isset($pre_uploaded_image['price']) ? stripslashes($pre_uploaded_image['price']) : '');
							
							$html_input .= '<li class="data-options">';
								$html_input .= '<span class="dashicons dashicons-move" style="margin-bottom: 7px;margin-top: 2px;"></span>';
								$html_input .= '<div style="display: flex;">';
									$html_input .= '<div class="ppom-uploader-img-center">';
										$html_input .= '<span class="dashicons dashicons-admin-media" style="margin-top: 5px;"></span>';
									$html_input .= '</div>';
									$html_input .= '<input type="hidden" name="ppom['.esc_attr($field_index).'][audio]['.esc_attr($opt_index).'][link]" value="'.esc_url($image_link).'">';
									$html_input .= '<input type="hidden" name="ppom['.esc_attr($field_index).'][audio]['.esc_attr($opt_index).'][id]" value="'.esc_attr($image_id).'">';
									$html_input .= '<input class="form-control" type="text" placeholder="Title" value="'.esc_attr($media_title).'" name="ppom['.esc_attr($field_index).'][audio]['.esc_attr($opt_index).'][title]">';
									$html_input .= '<input class="form-control" type="text" placeholder="Price (fix or %)" value="'.esc_attr($media_price).'" name="ppom['.esc_attr($field_index).'][audio]['.esc_attr($opt_index).'][price]">';
									$html_input .= '<button class="btn btn-danger ppom-pre-upload-delete" style="height: 35px;"><i class="fa fa-times" aria-hidden="true"></i></button>';
								$html_input .= '</div>';
							$html_input .= '</li>';

							$opt_index0 =  $last_array_id;
                    		$opt_index0++;
					
						}
					}
						$html_input .= '</ul>';
						$html_input .= '<input type="hidden" id="ppom-meta-opt-index" value="'.esc_attr($opt_index0).'">';
					$html_input .= '</div>';
				
				break;
				
				/**
				 * new addon: bulk quantity
				 * @since 7.1
				 **/
				case 'bulk-quantity' :
				
				$bulk_data = json_decode($values, true);
				// ppom_pa($bulk_data[0]);
				$html_input .= '<div class="ppom-bulk-quantity-wrapper">';
					$html_input .= '<div class="table-content">';
						$html_input .= '<div class="ppom-bulk-action-wrap">';
							$html_input .= '<div class="ppom-bulkquantity-qty-wrap">';
						    	$html_input .= '<button class="btn btn-primary ppom-add-bulk-qty-row">Add Qty Range</button>';
						    	$html_input .= '<input type="text" class="ppom-bulk-qty-val form-control">';
						    $html_input .= '</div>';
							$html_input .= '<div class="ppom-bulkquantity-variation-wrap">';
						    	$html_input .= '<button class="btn btn-primary ppom-add-bulk-variation-col">Add Variation</button>';
								$html_input .= '<input type="text" class="ppom-bulk-variation-val form-control">';
							$html_input .= '</div>';
						$html_input .= '</div>';
					    $html_input .= '<div class="table-responsive">';
					        $html_input .= '<table class="table">';
					            $html_input .= '<thead>';
					                $html_input .= '<tr>';

					                if ($values) {
			                			foreach ($bulk_data[0] as $title => $value) {
											$deleteIcon = ($title != 'Quantity Range' && $title != 'Base Price') ? '<span class="remove ppom-rm-bulk-variation"><i class="fa fa-times" aria-hidden="true"></i></span>' : '' ;
											$html_input .= '<th>'.$title.' '.$deleteIcon.'</th>';
										}
					                }else{
					                    $html_input .= '<th>Quantity Range</th>';
					                    $html_input .= '<th>Base Price</th>';
					                }

					                $html_input .= '</tr>';
					            $html_input .= '</thead>';
					            $html_input .= '<tbody>';

					            if ($values) {
						            foreach ($bulk_data as $row => $data) {
						                $html_input .= '<tr>';
						            	foreach ($data as $key => $value) {
						            		if ($key == 'Quantity Range') {
						            			$add_class = 'ppom-bulk-qty-val-picker form-control';
						            			$td_class  = 'ppom-bulkqty-adjust-cross';
						            		}else{
						            			$add_class = 'form-control';
						            			$td_class  = '';
						            		}
						            		$resetArr = reset($data);
											$delRow = ($resetArr == $value) ? '<span class="remove ppom-rm-bulk-qty"><i class="fa fa-times" aria-hidden="true"></i></span>' : '' ;
						            		if (1) {
												$html_input .= '<td class="'.$td_class.'" id="'.$td_class.'">'.$delRow.'<input type="text" class="'.$add_class.'" value="'.$value.'"></td>';
											}
						            	}
						                $html_input .= '</tr>';
						            }
					            }else {
					            	$html_input .= '<tr>';
					            		$html_input .= '<td class="ppom-bulkqty-adjust-cross" id="ppom-bulkqty-adjust-cross">';
					                    	$html_input .= '<span class="remove ppom-rm-bulk-qty"><i class="fa fa-times" aria-hidden="true"></i></span>';
					                    	$html_input .= '<input type="text" class="form-control ppom-bulk-qty-val-picker" placeholder="1-10" />';
					                    $html_input .= '</td>';
					                    $html_input .= '<td><input type="text" class="form-control" /></td>';
				            	 	$html_input .= '</tr>';
					            }
					            
					            $html_input .= '</tbody>';
					        $html_input .= '</table>';
					    $html_input .= '</div>';
					    $html_input .= '<div class="text-right">';
					    	$html_input .= '<button class="btn btn-info ppom-save-bulk-json">Save Changing</button>';
					    	$html_input .= '<button class="btn btn-success ppom-edit-bulk-json">Edit Changing</button>';
					    	
					    	if ($values) {
					    		$html_input .=	"<input type='hidden' name='ppom[".esc_attr($field_index)."][options]' class='ppom-saved-bulk-data ppom-meta-field' value='".json_encode($bulk_data)."' data-metatype='options'>";
					    	} else {
					    		$html_input .=	"<input type='hidden' class='ppom-saved-bulk-data ppom-meta-field' data-metatype='options'>";
					    	}
					    	
					    

					    $html_input .= '</div>';

					$html_input .= '</div>';
				$html_input .= '</div>';
				
				break;
		}
		
		return apply_filters('render_input_types', $html_input, $type, $name, $values, $options);
	}
    

    function ppom_fields_tabs(){
	
		$tabs = array();

		$tabs = array ( 
				'fields_tab' => array (
						'label' => __ ( 'Fields', 'ppom' ),
						'class' => array('ppom-tabs-label', 'ppom-active-tab'),
						'field_depend'=> array('all')
				),
				'condition_tab' => array (
						'label' => __ ( 'Conditions', 'ppom' ),
						'class' => array('ppom-tabs-label','ppom-condition-tab-js'),
						'field_depend'=> array('all'),
						'not_allowed'=> array('hidden','koll')
				),
				'add_option_tab' => array (
						'label' => __ ( 'Add Options', 'ppom' ),
						'class' => array('ppom-tabs-label'),
						'field_depend'=> array('select','radio','checkbox','cropper','quantities','pricematrix','palettes','fixedprice','bulkquantity')
				),
				'add_images_tab' => array (
						'label' => __ ( 'Add Images', 'ppom' ),
						'class' => array('ppom-tabs-label'),
						'field_depend'=> array('image','imageselect')
				),
				'add_audio_video_tab' => array (
						'label' => __ ( 'Add Audio/Video', 'ppom' ),
						'class' => array('ppom-tabs-label'),
						'field_depend'=> array('audio')
				),

				// Font Picker Addon tabs
				'fonts_family_tab' => array (
						'label' => __ ( 'Fonts Family', 'ppom' ),
						'class' => array('ppom-tabs-label'),
						'field_depend'=> array('fonts')
				),
				'custom_fonts_tab' => array (
						'label' => __ ( 'Custom Fonts', 'ppom' ),
						'class' => array('ppom-tabs-label'),
						'field_depend'=> array('fonts')
				),

				
			);

		return apply_filters('ppom_fields_tabs_show', $tabs);

	}


	function ppom_tabs_panel_classes($settings){


		foreach ($settings as $fields_meta_key => $meta) {

			$type       = isset($meta['type']) ? $meta['type'] : '';

			if ($type == 'html-conditions') {

				$settings['conditions']['tabs_class'] = array('ppom_handle_condition_tab','col-md-12');
			}else if($type == 'paired' || $type == 'paired-cropper' || $type == 'paired-quantity' || $type == 'bulk-quantity') { 
				//Bulk Quantity Addon Tabs
				//Fixed Price Addon Tabs

				$settings['options']['tabs_class'] = array('ppom_handle_add_option_tab','col-md-12');
			}else if( $type == 'pre-images' || $type == 'imageselect') { // Image DropDown Addon Tabs

				$settings['images']['tabs_class'] = array('ppom_handle_add_images_tab','col-md-12');
			}else if( $type == 'pre-audios' ) {

				$settings['audio']['tabs_class'] = array('ppom_handle_add_audio_video_tab','col-md-12');
			}else if($fields_meta_key== 'logic') {
				
				$settings['logic']['tabs_class'] = array('ppom_handle_condition_tab','col-md-12');
			}

			// Fonts Picker Addon tabs
			if ($fields_meta_key == 'fonts') {
				$settings['fonts']['tabs_class'] = array('ppom_handle_fonts_family_tab','col-md-12');
			}elseif ($fields_meta_key == 'custom_fonts') {
				$settings['custom_fonts']['tabs_class'] = array('ppom_handle_custom_fonts_tab','col-md-12');
			}

		}

		return apply_filters('ppom_tabs_panel_classes', $settings);
	}

}

PPOM_FIELDS_META();
function PPOM_FIELDS_META(){
    return PPOM_Fields_Meta::get_instance();
}