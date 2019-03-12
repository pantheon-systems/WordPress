<?php
/*
 * Followig class handling file input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_File_wooproduct extends PPOM_Inputs{
	
	/*
	 * input control settings
	 */
	var $title, $desc, $settings;
	
	
	/*
	 * this var is pouplated with current plugin meta 
	 */
	var $plugin_meta;
	
	function __construct(){
		
		$this -> plugin_meta = ppom_get_plugin_meta();
		
		$this -> title 		= __ ( 'File Input', "ppom" );
		$this -> desc		= __ ( 'regular file input', "ppom" );
		$this -> icon		= __ ( '<i class="fa fa-file" aria-hidden="true"></i>', 'ppom' );
		$this -> settings	= self::get_settings();
		
	}
	
	private function get_settings(){
		
		return array (
			'title' => array (
					'type' => 'text',
					'title' => __ ( 'Title', "ppom" ),
					'desc' => __ ( 'It will be shown as field label', "ppom" ) 
			),
			'data_name' => array (
					'type' => 'text',
					'title' => __ ( 'Data name', "ppom" ),
					'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', "ppom" ) 
			),
			'description' => array (
					'type' => 'textarea',
					'title' => __ ( 'Description', "ppom" ),
					'desc' => __ ( 'Small description, it will be display near name title.', "ppom" ) 
			),
			'error_message' => array (
					'type' => 'text',
					'title' => __ ( 'Error message', "ppom" ),
					'desc' => __ ( 'Insert the error message for validation.', "ppom" ) 
			),
			'file_cost' => array (
					'type' => 'text',
					'title' => __ ( 'File cost/price', "ppom" ),
					'desc' => __ ( 'This will be added into cart', "ppom" )
			),
			'class' => array (
					'type' => 'text',
					'title' => __ ( 'Class', "ppom" ),
					'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', "ppom" ) 
			),
			'width' => array (
					'type' => 'select',
					'title' => __ ( 'Width', 'ppom' ),
					'desc' => __ ( 'Select width column.', "ppom"),
					'options'	=> ppom_get_input_cols(),
					'default'	=> 12,
			),
			'button_label_select' => array (
					'type' => 'text',
					'title' => __ ( 'Button label (select files)', "ppom" ),
					'desc' => __ ( 'Type button label e.g: Select Photos', "ppom" ) 
			),
			'button_class' => array (
					'type' => 'text',
					'title' => __ ( 'Button class', "ppom" ),
					'desc' => __ ( 'Type class for both (select, upload) buttons', "ppom" ) 
			),			
			'files_allowed' => array (
					'type' => 'text',
					'title' => __ ( 'Files allowed', "ppom" ),
					'desc' => __ ( 'Type number of files allowed per upload by user, e.g: 3', "ppom" ) 
			),
			'file_types' => array (
					'type' => 'text',
					'title' => __ ( 'File types', "ppom" ),
					'desc' => __ ( 'File types allowed seperated by comma, e.g: jpg,pdf,zip', "ppom" ),
					'default' => 'jpg,pdf,zip',
			),
			'file_size' => array (
					'type' => 'text',
					'title' => __ ( 'File size', "ppom" ),
					'desc' => __ ( 'Type size with units in kb|mb per file uploaded by user, e.g: 3mb', "ppom" ),
					'default' => '1mb',
			),
			// 'language_opt' => array (
			// 		'type' => 'select',
			// 		'title' => __ ( 'Select Language', "ppom" ),
			// 		'desc' => __ ( 'Select language for uploader alerts and messages.', "ppom" ),
			// 		'options' => ppom_get_plupload_languages(), 
			// ),

			'visibility' => array (
					'type' => 'select',
					'title' => __ ( 'Visibility', 'ppom' ),
					'desc' => __ ( 'Set field visibility based on user.', "ppom"),
					'options'	=> ppom_field_visibility_options(),
					'default'	=> 'everyone',
			),
			'visibility_role' => array (
					'type' => 'text',
					'title' => __ ( 'User Roles', 'ppom' ),
					'desc' => __ ( 'Role separated by comma.', "ppom"),
					'hidden' => true,
			),
			'desc_tooltip' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show tooltip (PRO)', 'ppom' ),
					'desc' => __ ( 'Show Description in Tooltip with Help Icon', 'ppom' )
			),
			'required' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Required', "ppom" ),
					'desc' => __ ( 'Select this if it must be required.', "ppom" ) 
			),
			'onetime' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Fixed Fee', "ppom" ),
					'desc' => __ ( 'Add one time fee to cart total.', "ppom" ) 
			),
			'onetime_taxable' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Fee Taxable?', "ppom" ),
					'desc' => __ ( 'Calculate Tax for Fixed Fee', "ppom" ) 
			),	
			// 'photo_editing' => array (
			// 		'type' => 'checkbox',
			// 		'title' => __ ( 'Enable photo editing', "ppom" ),
			// 		'desc' => __ ( 'Allow users to edit photos by Aviary API, make sure that Aviary API Key is set in previous tab.', "ppom" ) 
			// ),
			// 'editing_tools' => array (
			// 		'type' => 'checkbox',
			// 		'title' => __ ( 'Editing Options', "ppom" ),
			// 		'desc' => __ ( 'Select editing options', "ppom" ),
			// 		'options' => array (
			// 				'enhance' => 'Enhancements',
			// 				'effects' => 'Filters',
			// 				'frames' => 'Frames',
			// 				'stickers' => 'Stickers',
			// 				'orientation' => 'Orientation',
			// 				'focus' => 'Focus',
			// 				'resize' => 'Resize',
			// 				'crop' => 'Crop',
			// 				'warmth' => 'Warmth',
			// 				'brightness' => 'Brightness',
			// 				'contrast' => 'Contrast',
			// 				'saturation' => 'Saturation',
			// 				'sharpness' => 'Sharpness',
			// 				'colorsplash' => 'Colorsplash',
			// 				'draw' => 'Draw',
			// 				'text' => 'Text',
			// 				'redeye' => 'Red-Eye',
			// 				'whiten' => 'Whiten teeth',
			// 				'blemish' => 'Remove skin blemishes' 
			// 		) 
			// ),
			'logic' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Enable Conditions', "ppom" ),
					'desc' => __ ( 'Tick it to turn conditional logic to work below', "ppom" )
			),
			'conditions' => array (
					'type' => 'html-conditions',
					'title' => __ ( 'Conditions', "ppom" ),
					'desc' => __ ( 'Tick it to turn conditional logic to work below', "ppom" )
			),
		);
	}
	
	
	/*
	 * @params: args
	*/
	function render_input($args, $content=""){

		// Setting pluploader language for alerts (added in 6.6)
		if ($args['language'] != '') {
			$plugin_meta = get_plugin_meta_wooproduct();
			wp_enqueue_script( 'pluploader-language', $plugin_meta['url'].'/js/plupload-2.1.2/js/i18n/'.$args['language'].'.js');
		}
		
		$container_height = ($args['dragdrop']) ? 'auto' : '30px' ;
		$_html = '<div class="container_buttons" style="height: '.$container_height.' ;">';
			$_html .= '<div class="btn_center">';
			$_html .= '<a id="selectfiles-'.$args['id'].'" href="javascript:;" class="select_button '.$args['button-class'].'">' . $args['button-label-select'] . '</a>';
			$_html .= '</div>';
			
			
		$_html .= '</div>';		//container_buttons

		if($args['dragdrop']){
			
			$_html .= '<div class="droptext">';
				if($this -> if_browser_is_ie())
					$_html .= __('Drag file(s) in this box', "ppom");
				else 
					$_html .= __('Drag file(s) or directory in this box', "ppom");
			$_html .= '</div>';
		}
    	
    	$_html .= '<div id="filelist-'.$args['id'].'" class="filelist"></div>';
    	
    	
    	echo $_html;
    	
    	$this -> get_input_js($args);
	}
	
	
	/*
	 * following function is rendering JS needed for input
	 */
	function get_input_js($args){
		
		// nothing since 8.4
	}
}