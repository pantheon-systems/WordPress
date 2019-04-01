<?php
/*
 * Followig class handling price matrix based on quantity provied in range
 * like 1-25
* dependencies. Do not make changes in code
* Create on: 10 February, 2017
*/

class NM_BulkQuantity_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Bulk Quantity', "ppom" );
		$this -> desc		= __ ( 'Price/Quantity', "ppom" );
		$this -> settings	= self::get_settings();
		
	}
	
	
	
	
	private function get_settings(){
		
		return array (
				
						'info' => array (
								'type' => 'info',
								'title' => __ ( 'Please Get Bulk Quantity Addon to Enable this feature.', "ppom" ),
						),
						
						
						
				);
	}
	
	
	/*
	 * @params: args
	*/
	function render_input($args, $options){

		_e('Sorry please upgrade to Pro version', "ppom");

		
	}

}