<?php
/*
 * Followig class handling pre-uploaded image control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Image_wooproduct extends PPOM_Inputs{
	
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
		
		$this -> title 		= __ ( 'Images', 'ppom' );
		$this -> desc		= __ ( 'Images selection', 'ppom' );
		$this -> icon		= __ ( '<i class="fa fa-picture-o" aria-hidden="true"></i>', 'ppom' );
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
			'images' => array (
					'type' => 'pre-images',
					'title' => __ ( 'Select images', 'ppom' ),
					'desc' => __ ( 'Select images from media library', 'ppom' )
			),	
			'selected' => array (
					'type' => 'text',
					'title' => __ ( 'Selected image', 'ppom' ),
					'desc' => __ ( 'Type option title given in (Add Images) tab if you want it already selected.', 'ppom' )
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
			'multiple_allowed' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Multiple selections?', 'ppom' ),
					'desc' => __ ( 'Allow users to select more then one images?.', 'ppom' )
			),
			'show_popup' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Popup', 'ppom' ),
					'desc' => __ ( 'Show big image on hover', 'ppom' )
			),
					
			'legacy_view' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Enable legacy view', 'ppom' ),
					'desc' => __ ( 'Tick it to turn on old boxes view for images', 'ppom' )
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
	function render_input($args, $images = "", $default_selected = ""){
		
		// nm_personalizedproduct_pa();
		$_html = '';
		
		// Checking if old view is enabled for images with boxes
		if($args['legacy_view'] == 'on'){
			$_html = '<div class="pre_upload_image_box">';
				
			$img_index = 0;
			$popup_width	= $args['popup-width'] == '' ? 600 : $args['popup-width'];
			$popup_height	= $args['popup-height'] == '' ? 450 : $args['popup-height'];
			
			if ($images) {
				
				foreach ($images as $image){
						
					
					$_html .= '<div class="pre_upload_image">';
					if($image['id'] != ''){
						if( isset($image['url']) && $image['url'] != '' )
							$_html .= '<a href="'.$image['url'].'"><img src="'.wp_get_attachment_thumb_url( $image['id'] ).'" /></a>';
						else
							$_html .= '<img src="'.wp_get_attachment_thumb_url( $image['id'] ).'" />';
					}else{
						if( isset($image['url']) && $image['url'] != '' )
							$_html .= '<a href="'.$image['url'].'"><img width="150" height="150" src="'.$image['link'].'" /></a>';
						else {
							$_html .= '<img width="150" height="150" src="'.$image['link'].'" />';
						}
					}
					
						
					// for bigger view
					$_html	.= '<div style="display:none" id="pre_uploaded_image_' . $args['id'].'-'.$img_index.'"><img style="margin: 0 auto;display: block;" src="' . $image['link'] . '" /></div>';
						
					$_html	.= '<div class="input_image">';
					if ($args['multiple-allowed'] == 'on') {
						$_html	.= '<input type="checkbox" data-price="'.$image['price'].'" data-title="'.stripslashes( $image['title'] ).'" name="'.$args['name'].'[]" value="'.esc_attr(json_encode($image)).'" />';
					}else{
						
						//default selected
						$checked = ($image['title'] == $default_selected ? 'checked = "checked"' : '' );
						$_html	.= '<input type="radio" data-price="'.$image['price'].'" data-title="'.stripslashes( $image['title'] ).'" data-type="'.stripslashes( $args['data-type'] ).'" name="'.$args['name'].'" value="'.esc_attr(json_encode($image)).'" '.$checked.' />';
					}
						
					
					$price = '';
					if(function_exists('wc_price') && $image['price'] > 0)
						$price = wc_price( $image['price'] );
					
					// image big view	 
					$_html	.= '<a href="#TB_inline?width='.$popup_width.'&height='.$popup_height.'&inlineId=pre_uploaded_image_' . $args['id'].'-'.$img_index.'" class="thickbox" title="' . $image['title'] . '"><img width="15" src="' . $this -> plugin_meta['url'] . '/images/zoom.png" /></a>';
					$_html	.= '<div class="p_u_i_name">'.stripslashes( $image['title'] ) . ' ' . $price . '</div>';
					$_html	.= '</div>';	//input_image
						
						
					$_html .= '</div>';
						
					$img_index++;
				}
			}
			
			$_html .= '<div style="clear:both"></div>';		//container_buttons
				
			$_html .= '</div>';		//container_buttons
			

		} else {
			
			$_html = '<div class="nm-boxes-outer">';
				
			$img_index = 0;
			$popup_width	= $args['popup-width'] == '' ? 600 : $args['popup-width'];
			$popup_height	= $args['popup-height'] == '' ? 450 : $args['popup-height'];
			
			if ($images) {
				
				foreach ($images as $image){
						
					$_html .= '<label><div class="pre_upload_image">';
					if ($args['multiple-allowed'] == 'on') {
						$_html	.= '<input type="checkbox" data-price="'.$image['price'].'" data-title="'.stripslashes( $image['title'] ).'" name="'.$args['name'].'[]" value="'.esc_attr(json_encode($image)).'" />';
					}else{
						
						//default selected
						$checked = ($image['title'] == $default_selected ? 'checked = "checked"' : '' );
						$_html	.= '<input type="radio" data-price="'.$image['price'].'" data-title="'.stripslashes( $image['title'] ).'" data-type="'.stripslashes( $args['data-type'] ).'" name="'.$args['name'].'" value="'.esc_attr(json_encode($image)).'" '.$checked.' />';
					}					
					if($image['id'] != ''){
						if( isset($image['url']) && $image['url'] != '' )
							$_html .= '<a href="'.$image['url'].'"><img src="'.wp_get_attachment_thumb_url( $image['id'] ).'" /></a>';
						else
							$_html .= '<img data-image-tooltip="'.wp_get_attachment_url($image['id']).'" class="nm-enlarge-image" src="'.wp_get_attachment_thumb_url( $image['id'] ).'" />';
					}else{
						if( isset($image['url']) && $image['url'] != '' )
							$_html .= '<a href="'.$image['url'].'"><img width="150" height="150" src="'.$image['link'].'" /></a>';
						else {
							$_html .= '<img class="nm-enlarge-image" data-image-tooltip="'.$image['link'].'" src="'.$image['link'].'" />';
						}
					}
					
						
						
					$_html .= '</div></label>';
						
					$img_index++;
				}
			}
			
			$_html .= '<div style="clear:both"></div>';		//container_buttons
				
			$_html .= '</div>';		//container_buttons
		}
		
		echo $_html;
		
		$this -> get_input_js($args);
	}
	
	
	/*
	 * following function is rendering JS needed for input
	*/
	function get_input_js($args){
		?>
			
					<script type="text/javascript">	
					<!--
					jQuery(function($){
						  if($('.nm-enlarge-image').length){
						    $('.nm-enlarge-image').imageTooltip({
							  xOffset: 5,
							  yOffset: 5
						    });
						  }	
						// pre upload image click selection
						/*$(".pre_upload_image").click(function(){

							if($(this).find('input:checkbox').attr("checked") === 'checked'){
								$(this).find('input:checkbox').attr("checked", false);
							}else{
								$(this).find('input:radio, input:checkbox').attr("checked", "checked");
							}

						});*/
						
					});
					
					//--></script>
					<?php
			}
}