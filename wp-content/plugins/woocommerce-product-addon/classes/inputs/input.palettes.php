<?php
/*
 * Followig class handling radio input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Palettes_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Color Palettes', 'ppom' );
		$this -> desc		= __ ( 'color boxes', 'ppom' );
		$this -> icon		= __ ( '<i class="fa fa-user-plus" aria-hidden="true"></i>', 'ppom' );
		$this -> settings	= self::get_settings();
		
	}
	
	private function get_settings(){
		
		return array (
			'title' => array (
					'type' => 'text',
					'title' => __ ( 'Title', 'ppom' ),
					'desc' => __ ( 'It will be shown as field label', 'ppom' ) 
			),
			'data_name' => array (
					'type' => 'text',
					'title' => __ ( 'Data name', 'ppom' ),
					'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', 'ppom' ) 
			),
			'description' => array (
					'type' => 'textarea',
					'title' => __ ( 'Description', 'ppom' ),
					'desc' => __ ( 'Small description, it will be display near name title.', 'ppom' ) 
			),
			'error_message' => array (
					'type' => 'text',
					'title' => __ ( 'Error message', 'ppom' ),
					'desc' => __ ( 'Insert the error message for validation.', 'ppom' ) 
			),
			'class' => array (
					'type' => 'text',
					'title' => __ ( 'Class', 'ppom' ),
					'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', 'ppom' ) 
			),
			'width' => array (
					'type' => 'select',
					'title' => __ ( 'Width', 'ppom' ),
					'desc' => __ ( 'Select width column.', "ppom"),
					'options'	=> ppom_get_input_cols(),
					'default'	=> 12,
			),
			'options' => array (
						'type' => 'paired',
						'title' => __ ( 'Add colors', 'ppom' ),
						'desc' => __ ( 'Type color code with price (optionally). To write label, use #colorcode - White', 'ppom' )
			),

			/*'show_price' => array (
						'type' => 'checkbox',
						'title' => __ ( 'Show price', 'ppom' ),
						'desc' => __ ( 'Show price on front end with options', 'ppom' ) 
				),*/
			
			'selected' => array (
					'type' => 'text',
					'title' => __ ( 'Selected color', 'ppom' ),
					'desc' => __ ( 'Type color code given in (Add Options) tab if you want already selected.', 'ppom' ) 
			),
			'color_width' => array (
					'type' => 'text',
					'title' => __ ( 'Color width', 'ppom' ),
					'desc' => __ ( 'default is 50, e.g: 75', 'ppom' ) 
			),
			'color_height' => array (
					'type' => 'text',
					'title' => __ ( 'Color height', 'ppom' ),
					'desc' => __ ( 'default is 50, e.g: 100', 'ppom' ) 
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
					'title' => __ ( 'Required', 'ppom' ),
					'desc' => __ ( 'Select this if it must be required.', 'ppom' ) 
			),
			'onetime' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Fixed Fee', 'ppom' ),
					'desc' => __ ( 'Add one time fee to cart total.', 'ppom' ) 
			),
			'onetime_taxable' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Fixed Fee Taxable?', 'ppom' ),
					'desc' => __ ( 'Calculate Tax for Fixed Fee', 'ppom' ) 
			),
			'circle' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show as Circle', 'ppom' ),
					'desc' => __ ( 'It will display color palettes as circle', 'ppom' )
			),
			'multiple_allowed' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Multiple selections?', 'ppom' ),
					'desc' => __ ( 'Allow users to select more then one palette?.', 'ppom' )
			),
			'logic' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Enable Conditions', 'ppom' ),
					'desc' => __ ( 'Tick it to turn conditional logic to work below', 'ppom' )
			),
			'conditions' => array (
					'type' => 'html-conditions',
					'title' => __ ( 'Conditions', 'ppom' ),
					'desc' => __ ( 'Tick it to turn conditional logic to work below', 'ppom' )
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
			// First Separate color code and label
			$color_label_arr = explode('-', $opt['option']);
			$color_code = trim($color_label_arr[0]);
			$color_label = '';
			if(isset($color_label_arr[1])){
				$color_label = trim($color_label_arr[1]);
			}

			if($opt['price']){
				$output	=  wc_price($opt['price']);
			}else{
				$output	= '';
			}
			
			$field_id = $args['name'] . '-meta-'.strtolower ( preg_replace ( "![^a-z0-9]+!i", "_", $opt['option'] ) );
			
			$_html .= '<label for="'.$field_id.'"> <input id="'.$field_id.'" data-price="'.$opt['price'].'" type="radio" ';
			
			foreach ($args as $attr => $value){
					
				$_html .= $attr.'="'.stripslashes( $value ).'"';
			}
		
			$_html .= ' value="'.$opt['option'].'" '.checked($default, $opt['option'], false).'>';
			
			if(isset($args['disabletooltip']) && $args['disabletooltip'] != 'on'){
				$_html .= '<div class="palette-box" title="'.$color_label.'" style="background-color:'.trim($color_code).';">'.$output.'</div>';
			} else {
				$_html .= '<div class="palette-box" title="'.$color_label.'" style="background-color:'.trim($color_code).';">'.$color_label.'<br>'.$output.'</div>';
			}
			
		
			$_html .= '</label>';
		}
		if(isset($args['disabletooltip']) && $args['disabletooltip'] != 'on'){
			$_html .= '<script>
				jQuery( document ).ready(function(){
				    jQuery( ".nm-color-palette " ).tooltip({
				      position: {
				        my: "center bottom-20",
				        at: "center top",
				        using: function( position, feedback ) {
				          jQuery( this ).css( position );
				          jQuery( "<div>" )
				            .addClass( "arrow" )
				            .addClass( feedback.vertical )
				            .addClass( feedback.horizontal )
				            .appendTo( this );
				        }
				      }
				    });
				});
			</script>';
		}		
		
		echo $_html;
	}
}