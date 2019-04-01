<?php
/*
 * Followig class handling number input control and their
* dependencies. Do not make changes in code
* Create on: 21 May, 2014
*/

class NM_Number_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Number Input', "ppom" );
		$this -> desc		= __ ( 'regular number input', "ppom" );
		$this -> icon		= __ ( '<i class="fa fa-hashtag" aria-hidden="true"></i>', 'ppom' );
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
			'placeholder' => array (
					'type' => 'text',
					'title' => __ ( 'Placeholder', 'ppom' ),
					'desc' => __ ( 'Optionally placeholder.', 'ppom' ) 
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
			'max' => array (
					'type' => 'text',
					'title' => __ ( 'Max. values', "ppom" ),
					'desc' => __ ( 'Max. values allowed, leave blank for default', "ppom" )
			),
			'min' => array (
					'type' => 'text',
					'title' => __ ( 'Min. values', "ppom" ),
					'desc' => __ ( 'Min. values allowed, leave blank for default', "ppom" )
			),
			'step' => array (
					'type' => 'text',
					'title' => __ ( 'Steps', "ppom" ),
					'desc' => __ ( 'specified legal number intervals', "ppom" )
			),
			'default_value' => array (
					'type' => 'text',
					'title' => __ ( 'Set default value', "ppom" ),
					'desc' => __ ( 'Pre-defined value for text input', "ppom" )
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
		
		$_html = '<input type="number" ';
		
		foreach ($args as $attr => $value){
			
			$_html .= $attr.'="'.stripslashes( $value ).'"';
		}
		
		if($content)
			$_html .= 'value="' . stripslashes($content	) . '"';
		
		$_html .= ' />';
		
		echo $_html;
	}
}