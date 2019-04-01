<?php 

#-----------------------------------------------------------------#
# Create Meta
#-----------------------------------------------------------------#
function nectar_create_meta_box( $post, $meta_box )
{
	
    if( !is_array($meta_box) ) return false;
    
    if( isset($meta_box['description']) && $meta_box['description'] != '' ){
    	echo '<p>'. $meta_box['description'] .'</p>';
    }
    
	wp_nonce_field( basename(__FILE__), 'nectar_meta_box_nonce' );
	echo '<table class="form-table nectar-metabox-table">';
 	
	$count = 0;
	
	foreach( $meta_box['fields'] as $field ){
		
		$meta = get_post_meta( $post->ID, $field['id'], true );
		
		$inline = null;
		if(isset($field['extra'])) { $inline = true; }
		
		if($inline == null) {
			
		echo '<tr><th><label for="'. $field['id'] .'"><strong>'. $field['name'] .'</strong>
			  <span>'. $field['desc'] .'</span></label></th>';
		}

		
		switch( $field['type'] ){	
			case 'text': 
				echo '<td><input type="text" name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'" value="'. ($meta ? $meta : $field['std']) .'" size="30" /></td>';
				break;	
				
			case 'textarea':
				echo '<td><textarea name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'" rows="8" cols="5">'. ($meta ? $meta : $field['std']) .'</textarea></td>';
				break;
			case 'media_textarea':
				echo '<td><div style="display:none;" class="attr_placeholder" data-poster="" data-media-mp4="" data-media-ogv=""></div><textarea name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'" rows="8" cols="5">'. ($meta ? $meta : $field['std']) .'</textarea></td>';
				break;
			
			case 'editor' :
				$settings = array(
		            'textarea_name' => 'nectar_meta['. $field['id'] .']',
		            'editor_class' => '',
		            'wpautop' => true
		        );
		        wp_editor($meta, $field['id'], $settings );
				
				break;

			case 'slim_editor' :
				$settings = array(
		            'textarea_name' => 'nectar_meta['. $field['id'] .']',
		            'editor_class' => 'slim',
		            'wpautop' => true
		        );
		        echo'<td>';
		        	wp_editor($meta, $field['id'], $settings );
				echo '</td>';
				break;
			case 'file':
				 
				echo '<td><input type="hidden" id="' . $field['id'] . '" name="nectar_meta[' . $field['id'] . ']" value="' . ($meta ? $meta : $field['std']) . '" />';
		        echo '<img class="redux-opts-screenshot" id="redux-opts-screenshot-' . $field['id'] . '" src="' . ($meta ? $meta : $field['std']) . '" />';
		        if( ($meta ? $meta : $field['std']) == '') {$remove = ' style="display:none;"'; $upload = ''; } else {$remove = ''; $upload = ' style="display:none;"'; }
		        echo ' <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);"class="redux-opts-upload button-secondary"' . $upload . ' rel-id="' . $field['id'] . '">' . esc_html__('Upload', 'salient') . '</a>';
		        echo ' <a href="javascript:void(0);" class="redux-opts-upload-remove"' . $remove . ' rel-id="' . $field['id'] . '">' . esc_html__('Remove Upload', 'salient') . '</a></td>';
		        
				break;
				
 			case 'color':
			
				 if(get_bloginfo('version') >= '3.5') {
		            wp_enqueue_style('wp-color-picker');
		            wp_enqueue_script(
		                'redux-opts-field-color-js',
		                NECTAR_FRAMEWORK_DIRECTORY . 'options/fields/color/field_color.js',
		                array('wp-color-picker'),
		                time(),
		                true
		            );
		        } else {
		            wp_enqueue_script(
		                'redux-opts-field-color-js', 
		                NECTAR_FRAMEWORK_DIRECTORY . 'options/fields/color/field_color_farb.js', 
		                array('jquery', 'farbtastic'),
		                time(),
		                true
		            );
		        }
				
				if(get_bloginfo('version') >= '3.5') {
		            echo '<td><input type="text" id="' . $field['id'] . '" name="nectar_meta[' . $field['id'] . ']" value="' . ($meta ? $meta : $field['std']) . '" class=" popup-colorpicker" style="width: 70px;" data-default-color="' . ($meta ? $meta : $field['std']) . '"/></td>';
		        } else {
		            echo '<div class="farb-popup-wrapper">';
		            echo '<input type="text" id="' . $field['id'] . '" name="nectar_meta[' . $field['id'] . ']" value="' . ($meta ? $meta : $field['std']) . '" class=" popup-colorpicker" style="width:70px;"/>';
		            echo '<div class="farb-popup"><div class="farb-popup-inside"><div id="' . $field['id'] . 'picker" class="color-picker"></div></div></div>';
		            echo '</div>';
		        }
				
				
				break;
				
			case 'media':
				 
				echo '<td><input type="text" class="file_display_text" id="' . $field['id'] . '" name="nectar_meta[' . $field['id'] . ']" value="' . ($meta ? $meta : $field['std']) . '" />';
		        if( ($meta ? $meta : $field['std']) == '') {$remove = ' style="display:none;"'; $upload = ''; } else {$remove = ''; $upload = ' style="display:none;"'; }
		        echo ' <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);"class="redux-opts-media-upload button-secondary"' . $upload . ' rel-id="' . $field['id'] . '">' . esc_html__('Add Media', 'salient') . '</a>';
		        echo ' <a href="javascript:void(0);" class="redux-opts-upload-media-remove"' . $remove . ' rel-id="' . $field['id'] . '">' . esc_html__('Remove Media', 'salient') . '</a></td>';
		        
				break;
				
			case 'images':
			    echo '<td><input type="button" class="button" name="' . $field['id'] . '" id="nectar_images_upload" value="' . $field['std'] .'" /></td>';
			    break;
				
			case 'select':
				echo'<td><select name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'">';
				foreach( $field['options'] as $key => $option ){
					echo '<option value="' . $key . '"';
					if( $meta ){ 
						if( $meta == $key ) echo ' selected="selected"'; 
					} else {
						if( $field['std'] == $key ) echo ' selected="selected"'; 
					}
					echo'>'. $option .'</option>';
				}
				echo'</select></td>';
				break;
			case 'choice_below' :
				
				wp_register_style(
	                'redux-opts-jquery-ui-custom-css',
	                apply_filters('redux-opts-ui-theme',  NECTAR_FRAMEWORK_DIRECTORY . 'options/css/custom-theme/jquery-ui-1.10.0.custom.css'),
	                '',
	                time(),
	                'all'
	            );
				 wp_enqueue_style('redux-opts-jquery-ui-custom-css');
		         wp_enqueue_script(
		            'redux-opts-field-button_set-js', 
		            NECTAR_FRAMEWORK_DIRECTORY . 'options/fields/button_set/field_button_set.js', 
		            array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
		            time(),
		            true
		        );
				echo '<td colspan="8">';
				    echo '<fieldset class="buttonset '.$field['id'].'" >';
						foreach( $field['options'] as $key => $option ){
				
							echo '<input type="radio" id="nectar_meta_'. $key .'" name="nectar_meta['. $field["id"] .']" value="'. $key .'" ';
							if( $meta ){ 
								if( $meta == $key ) echo ' checked="checked"'; 
							} else {
								if( $field['std'] == $key ) echo ' checked="checked"';
							}
							echo ' /> ';
							echo '<label for="nectar_meta_'. $key .'"> '.$option.'</label>';
							
						}
					echo '</fieldset>';
				echo '</td>';
				break;
			case 'multi-select':
				echo'<td><select multiple="multiple" name="nectar_meta['. $field['id'] .'][]" id="'. $field['id'] .'">';
				foreach( $field['options'] as $key => $option ){
					echo '<option value="' . $key . '"';
					if( $meta ){
						
						echo (is_array($meta) && in_array($key, $meta)) ? ' selected="selected"' : '';
           				 
						if( $meta == $key ) echo ' selected="selected"'; 
					} else {
						if( $field['std'] == $key ) echo ' selected="selected"'; 
					}
					echo'>'. $option .'</option>';
				}
				echo'</select></td>';
				break;
				
			case 'slide_alignment' :
				
				wp_register_style(
	                'redux-opts-jquery-ui-custom-css',
	                apply_filters('redux-opts-ui-theme',  NECTAR_FRAMEWORK_DIRECTORY . 'options/css/custom-theme/jquery-ui-1.10.0.custom.css'),
	                '',
	                time(),
	                'all'
	            );
				 wp_enqueue_style('redux-opts-jquery-ui-custom-css');
		         wp_enqueue_script(
		            'redux-opts-field-button_set-js', 
		            NECTAR_FRAMEWORK_DIRECTORY . 'options/fields/button_set/field_button_set.js', 
		            array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
		            time(),
		            true
		        );
				echo '<td>';
				    echo '<fieldset class="buttonset">';
						foreach( $field['options'] as $key => $option ){
				
							echo '<input type="radio" id="nectar_meta_'. $key .'" name="nectar_meta['. $field["id"] .']" value="'. $key .'" ';
							if( $meta ){ 
								if( $meta == $key ) echo ' checked="checked"'; 
							} else {
								if( $field['std'] == $key ) echo ' checked="checked"';
							}
							echo ' /> ';
							echo '<label for="nectar_meta_'. $key .'"> '.$option.'</label>';
							
						}
					echo '</fieldset>';
				echo '</td>';
				break;
			case 'radio':
				echo '<td>';
				foreach( $field['options'] as $key => $option ){
					echo '<label class="radio-label"><input type="radio" name="nectar_meta['. $field['id'] .']" value="'. $key .'" class="radio"';
					if( $meta ){ 
						if( $meta == $key ) echo ' checked="checked"'; 
					} else {
						if( $field['std'] == $key ) echo ' checked="checked"';
					}
					echo ' /> '. $option .'</label> ';
				}
				echo '</td>';
				break;
			case 'slider_button_text':
				if($field['extra'] == 'first'){
					$count++;
					echo '<tr><td><label><strong>Button #'.$count.'</strong> <span>Configure your button here.</span> </label></td>';
				}
				echo '<td class="inline">';
				if($inline != null) {
					echo '<label for="'. $field['id'] .'"><strong>'. $field['name'] .'</strong>
			 		 <span>'. $field['desc'] .'</span></label>';
				}
				echo '<input type="text" name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'" value="'. ($meta ? $meta : $field['std']) .'" size="30"  />';
				echo '</td>';
				break;
			case 'slider_button_textarea':
				if($field['extra'] == 'first'){
					$count++;
					echo '<tr><td><label><strong>Button #'.$count.'</strong> <span>Configure your button here.</span> </label></td>';
				}
				echo '<td class="inline">';
				if($inline != null) {
					echo '<label for="'. $field['id'] .'"><strong>'. $field['name'] .'</strong>
			 		 <span>'. $field['desc'] .'</span></label>';
				}
				echo '<textarea name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'">'.($meta ? $meta : $field['std']) .'</textarea>';
				echo '</td>';
				break;
				
			case 'slider_button_select':
				echo '<td class="inline">';
				if($inline != null) {
					echo '<label for="'. $field['id'] .'"><strong>'. $field['name'] .'</strong>
			 		 <span>'. $field['desc'] .'</span></label>';
				}
				echo'<select name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'">';
				foreach( $field['options'] as $key => $option ){
					echo '<option value="' . $key . '"';
					if( $meta ){ 
						if( $meta == $key ) echo ' selected="selected"'; 
					} else {
						if( $field['std'] == $key ) echo ' selected="selected"'; 
					}
					echo'>'. $option .'</option>';
				}
				echo'</select></td>';
				if($field['extra'] == 'last'){
					echo '</tr>';
				}
				break;
			case 'checkbox':
				if(!empty($field['extra']) && $field['extra'] == 'first2'){
					echo '<tr><th><label><strong>Scroll Effect</strong> <span>Choose your desired scroll effect here.</span> </label></th>';
				}

			    echo '<td>';		 
			    $val = '';
					$activated_checkbox = '';
					$starting_disabled = '';
					$starting_enabled = '';

                if( $meta ) {
                    if( $meta == 'on' ) {
											$val = ' checked="checked"';
											$activated_checkbox = 'activated';
											$starting_enabled = 'selected';
										}
										else {
											$starting_disabled = 'selected';
										}
                } else {
                    if( $field['std'] == 'on' ) $val = ' checked="checked"';
                }
								
								echo '<div class="switch-options salient '.$activated_checkbox.'">';
								echo '<label class="cb-enable '.$starting_enabled.'"><span>' . __("On", 'salient') . '</span></label>';
								echo '<label class="cb-disable '.$starting_disabled.'"><span>' . __("Off", 'salient') . '</span></label>';

                echo '<input type="hidden" name="nectar_meta['. $field['id'] .']" value="off" />
                <input type="checkbox" id="'. $field['id'] .'" name="nectar_meta['. $field['id'] .']" value="on"'. $val .' /> ';
								
								echo '</div>';
								
                 if(!empty($field['extra']) && $field['extra'] == 'first2' || !empty($field['extra']) && $field['extra'] == 'last'){
               	   echo '<br/><br/><label for="'. $field['id'] .'"><strong>'. $field['name'] .'</strong><span>'. $field['desc'] .'</span></label>'; 
                }

			    echo '</td>';
			    if(!empty($field['extra']) && $field['extra'] == 'last'){
					echo '</tr>';
				}
			    break;
			case 'caption_pos' :
				
				wp_register_style(
	                'redux-opts-jquery-ui-custom-css',
	                apply_filters('redux-opts-ui-theme',  NECTAR_FRAMEWORK_DIRECTORY . 'options/css/custom-theme/jquery-ui-1.10.0.custom.css'),
	                '',
	                time(),
	                'all'
	            );
				 wp_enqueue_style('redux-opts-jquery-ui-custom-css');
		         wp_enqueue_script(
		            'redux-opts-field-button_set-js', 
		            NECTAR_FRAMEWORK_DIRECTORY . 'options/fields/button_set/field_button_set.js', 
		            array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
		            time(),
		            true
		        );
				if($field['extra'] == 'first'){
					echo '<tr><td><label><strong>Slide Content Alignment</strong> <span>Configure the position for your slides content</span> </label></td>';
				}
				if($field['extra'] == 'first2'){
					echo '<tr><th><label><strong>Header Content Alignment</strong> <span>Configure the position for your slides content</span> </label></th>';
				}
				echo '<td class="content-alignment"> <label><strong>'.$field['desc'].'</strong><span>Select Your Alignment</span></label>';
				    echo '<fieldset class="buttonset">';
						foreach( $field['options'] as $key => $option ){
				
							echo '<input type="radio" id="nectar_meta_'. $key .'" name="nectar_meta['. $field["id"] .']" value="'. $key .'" ';
							if( $meta ){ 
								if( $meta == $key ) echo ' checked="checked"'; 
							} else {
								if( $field['std'] == $key ) echo ' checked="checked"';
							}
							echo ' /> ';
							echo '<label for="nectar_meta_'. $key .'"> '.$option.'</label>';
							
						}
					echo '</fieldset>';
				echo '</td>';
				if($field['extra'] == 'last'){
					echo '</tr>';
				}

				break;
			
			case 'canvas_shape_group':

				//static or animated shape
			/*
				$field['options'] = array('static' => 'Static', 'sequenced' => 'Sequenced');

				echo'<td>
				<h4>Type</h4>
				<select name="nectar_meta[nectar_slider_canvas_shape_type_1]" id="nectar_slider_canvas_shape_type_1">';

				foreach( $field['options'] as $key => $option ){
					echo '<option value="' . $key . '"';
					if( $meta ){ 
						if( $meta == $key ) echo ' selected="selected"'; 
					} else {
						if( $field['std'] == $key ) echo ' selected="selected"'; 
					}
					echo'>'. $option .'</option>';
				}
				echo'</select></td>';
*/

				if ( function_exists( 'wp_enqueue_media' ) ) {
	               
	            
	            } else {
	                wp_enqueue_script( 'media-upload' );
	                wp_enqueue_script( 'thickbox' );
	                wp_enqueue_style( 'thickbox' );
	            }

	           /*  wp_enqueue_script(
	                'redux-field-gallery-js',
	                 NECTAR_FRAMEWORK_DIRECTORY . 'options/fields/upload/gallery.js',
	                array( 'jquery' ),
	                time(),
	                true
	            ); */


			    echo '<td>
				<fieldset id="'. $field['class'].'" class="redux-field-container redux-field redux-container-gallery" data-id="opt-gallery" data-type="gallery">
			    <div class="screenshot">';

	            if ( ! empty( $meta) ) {
	                $ids = explode( ',', $meta);

	                foreach ( $ids as $attachment_id ) {
	                    $img = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
	                    echo '<img class="redux-option-image" id="image_' . $field['id'] . '_' . $attachment_id . '" src="' . $img[0] . '" target="_blank" rel="external" />';
	                }
	            }

	            echo '</div>';
	            echo '<a href="#" onclick="return false;" id="edit-gallery" class="gallery-attachments button button-primary">' . __( 'Add/Edit Images', 'redux-framework' ) . '</a> ';
	            echo '<a href="#" onclick="return false;" id="clear-gallery" class="gallery-attachments button">' . __( 'Clear Images', 'redux-framework' ) . '</a>';
	            echo '<input type="hidden" class="gallery_values " value="' . esc_attr( $meta ) . '" name="nectar_meta['. $field["id"] .']" />
	            </fieldset></td>';


				break;
		}
		
	
		
		if($inline == null) {
			echo '</tr>';
		}
	}
 
	echo '</table>';
}


#-----------------------------------------------------------------#
# Save Meta
#-----------------------------------------------------------------#

function nectar_save_meta_box( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
	
	if ( !isset($_POST['nectar_meta']) || !isset($_POST['nectar_meta_box_nonce']) || !wp_verify_nonce( $_POST['nectar_meta_box_nonce'], basename( __FILE__ ) ) )
		return;
	
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) return;
	} 
	else {
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
	}
 
	foreach( $_POST['nectar_meta'] as $key=>$val ) {
		//skip processing editor fields
		if($key == '_nectar_portfolio_extra_content' || $key == '_nectar_portfolio_custom_grid_item_content') {
			update_post_meta( $post_id, $key, $val );
		}
		else {
			$val = wp_kses_post( $val );
			update_post_meta( $post_id, $key, $val );
		}
		
	} // loop.
	
} //end nectar_save_meta_box.

add_action( 'save_post', 'nectar_save_meta_box' );

?>