<?php
/*
 * Followig class handling radio input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Radio_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Radio Input', "ppom" );
		$this -> desc		= __ ( 'regular radio input', "ppom" );
		$this -> icon		= __ ( '<i class="fa fa-dot-circle-o" aria-hidden="true"></i>', 'ppom' );
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
				'desc' => __ ( 'Small description, it will be diplay near name title.', "ppom" ) 
			),
			'error_message' => array (
				'type' => 'text',
				'title' => __ ( 'Error message', "ppom" ),
				'desc' => __ ( 'Insert the error message for validation.', "ppom" ) 
			),
			'options' => array (
				'type' => 'paired',
				'title' => __ ( 'Add options', "ppom" ),
				'desc' => __ ( 'Type option with price (optionally)', "ppom" )
			),
			
			/*'show_price' => array (
						'type' => 'checkbox',
						'title' => __ ( 'Show price', 'ppom' ),
						'desc' => __ ( 'Show price on front end with options', 'ppom' ) 
				),*/
				
			'selected' => array (
				'type' => 'text',
				'title' => __ ( 'Selected option', "ppom" ),
				'desc' => __ ( 'Type option name given in (Add Options) tab if you want already selected.', "ppom" ) 
			),
			'class' => array (
				'type' => 'text',
				'title' => __ ( 'Class', "ppom" ),
				'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', "ppom" ) 
			),
			'width' => array (
				'type' => 'select',
				'title' => __ ( 'Width', 'ppom' ),
				'desc' => __ ( 'Select width column', "ppom"),
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
			'onetime' => array (
				'type' => 'checkbox',
				'title' => __ ( 'Fixed Fee', "ppom" ),
				'desc' => __ ( 'Add one time fee to cart total.', "ppom" ) 
			),
			'onetime_taxable' => array (
				'type' => 'checkbox',
				'title' => __ ( 'Fixed Fee Taxable?', "ppom" ),
				'desc' => __ ( 'Calculate Tax for Fixed Fee', "ppom" ) 
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
	 * @params: $options
	*/
	function render_input($args, $options="", $default=""){
		
		$_html = '';
		foreach($options as $opt)
		{

			if($opt['price']){
				
				//if in percent
				if(strpos($opt['price'],'%') !== false){
					$output	= stripslashes(trim($opt['option'])) .' (+ ' . $opt['price'].')';	
				}else{
					$output	= stripslashes(trim($opt['option'])) .' (+ ' . wc_price($opt['price']).')';
				}
				
			}else{
				$output	= stripslashes(trim($opt['option']));
			}			
			
			$field_id = $args['name'] . '-meta-'.strtolower ( preg_replace ( "![^a-z0-9]+!i", "_", $opt['option'] ) );
			
			$_html .= '<label for="'.$field_id.'"> <input id="'.$field_id.'" data-price="'.$opt['price'].'" type="radio" ';
			
			foreach ($args as $attr => $value){
					
				$_html .= $attr.'="'.stripslashes( $value ).'"';
			}
		
			// data-value for optionprice for fixed price
			// since 6.4
			$data_value = $args['field_label'].'-'.$opt['option'];
			
			$_html .= ' value="'.$opt['option'].'" ';
			$_html	.= 'data-value="'.$data_value.'" ';
			$_html	.= checked($default, $opt['option'], false).'> ';
			$_html .= $output;
		
			$_html .= '</label> ';
		}
		
		echo $_html;
	}
}