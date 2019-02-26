<?php
/*
 * This our new world, Inshallah 29 aug, 2013
 */
//global $this;
//$this -> pa(PPOM() -> inputs);

$productmeta_name 		= '';
$enable_ajax_validation = '';
$dynamic_price_hide  	= '';
$send_file_attachment	= '';
$show_cart_thumb		= '';
$aviary_api_key 		= '';
$productmeta_style 		= '';
$product_meta 			= '';
$product_meta_id		= 0;
$productmeta_categories	= '';

if (isset ( $_REQUEST ['productmeta_id'] ) && $_REQUEST ['do_meta'] == 'edit') {
	
	$product_meta_id = $_REQUEST ['productmeta_id'];
	$ppom		= new PPOM_Meta();
	$ppom_settings = $ppom->get_settings_by_id($product_meta_id);
	
	$productmeta_name 		= (isset($ppom_settings -> productmeta_name) ? stripslashes($ppom_settings->productmeta_name) : '');
	$enable_ajax_validation = (isset($ppom_settings -> productmeta_validation) ? $ppom_settings -> productmeta_validation : '');
    $dynamic_price_hide  	= (isset($ppom_settings -> dynamic_price_display) ? $ppom_settings -> dynamic_price_display : '');
    $send_file_attachment  	= (isset($ppom_settings -> send_file_attachment) ? $ppom_settings -> send_file_attachment : '');
	$show_cart_thumb		= (isset($ppom_settings -> show_cart_thumb) ? $ppom_settings -> show_cart_thumb : '');
	$aviary_api_key 		= (isset($ppom_settings -> aviary_api_key) ? $ppom_settings -> aviary_api_key : '');
	$productmeta_style 		= (isset($ppom_settings -> productmeta_style) ? $ppom_settings -> productmeta_style : '');
	$productmeta_categories	= (isset($ppom_settings -> productmeta_categories) ? $ppom_settings -> productmeta_categories : '');
	$product_meta 			= json_decode ( $ppom_settings->the_meta, true );
	
	// var_dump($single_productmeta->the_meta);
	// ppom_pa ( $product_meta );
}
$url_cancel = add_query_arg(array('action'=>false,'productmeta_id'=>false, 'do_meta'=>false));
	
echo '<p><a class="btn btn-primary" href="'.$url_cancel.'">'.__('&laquo; Existing Product Meta', "ppom").'</a></p>';

?>

<input type="hidden" name="productmeta_id" value="<?php echo esc_attr($product_meta_id)?>">
<div id="nmpersonalizedproduct-form-generator-new" style="background: #f1f1f1; padding: 10px;">


	<div id="formbox-1">
		<h2 class="produc-basic-settings">
			<?php _e('Product Meta Basic Settings', "ppom"); ?>
			<span class="dashicons dashicons-minus" style="float: right"></span>
			<div class="clear"></div>
		</h2>
		<hr>
		<table id="form-main-settings" border="0"
			width="100%"
			cellpadding="0" cellspacing="0">
			<tr>
				<td class="td-50">
				<span class="input-heading"><?php _e('Meta group name', "ppom")?></span>
				<input type="text" class="form-style" name="productmeta_name" value="<?php echo $productmeta_name?>" />
				<p class="s-font"><?php _e('For your reference', "ppom")?></p>
				</td>
				<td class="td-50">
					<span class="input-heading"><?php _e('Control price display on product page.', "ppom")?></span>
				
					<select name="dynamic_price_hide" class="form-style">
						<option value="no"><?php _e("Select Option", "ppom");?></option>
						<option value="hide" <?php selected($dynamic_price_hide, 'hide')?>><?php _e("Do Not Show Price Table", "ppom");?></option>
						<option value="option_sum" <?php selected($dynamic_price_hide, 'option_sum')?>><?php _e("Show Only Option's Total", "ppom");?></option>
						<option value="all_option" <?php selected($dynamic_price_hide, 'all_option')?>><?php _e("Show Each Option's Price", "ppom");?></option>
					</select>
					<br />
					<p class="s-font"><?php _e('Control how price table will be shown for options or disable.', "ppom")?></p>
				</td>
			</tr>
            <tr>
            	<td class="td-50">
					<input type="checkbox" <?php checked($enable_ajax_validation, 'yes')?> name="enable_ajax_validation" value="yes" />
					<?php _e('Enable ajax based validation? (BETA Feature)', "ppom")?>
					<p class="s-font"><?php _e('Do not refresh the page until required data is provided', "ppom")?></p>
				</td>
				
				<!--<td class="td-50">
					<input type="checkbox" <?php checked($send_file_attachment, 'yes')?> name="send_file_attachment" value="yes" />
					<?php _e('Receive file in E-mail as attachment?', "ppom")?>
					<p class="s-font"><?php _e('This will send an extra email with all files as Attachment. File URLs already sent with default WooCommerce Invoice.', "ppom")?></p>
				</td>
			</tr>-->
			<tr>
				<td class="td-50">
					<span class="input-heading"><?php _e('Input CSS', "ppom")?></span>
					<textarea class="ppom-option-textrea" name="productmeta_style"><?php echo stripslashes($productmeta_style)?></textarea> <br />
					<p class="s-font"><?php _e('Add your own CSS.', "ppom")?></p>
				</td>
			
				<td class="td-50">
					<span class="input-heading"><?php _e('Apply for Categories', "ppom")?></span>
					<textarea class="ppom-option-textrea" name="productmeta_categories"><?php echo stripslashes($productmeta_categories)?></textarea>
					<p class="s-font"><?php _e('If you want to apply this meta against categories, type here each category SLUG per line. For All type: All. Leave blank for default', "ppom")?></p>
				</td>
			</tr> 
			
		</table>

	</div>

	<!--------------------- END formbox-1 ---------------------------------------->

	<div id="formbox-2" style="background: #f1f1f1;">
		<h2><?php _e('PPOM Fields', "ppom"); ?></h2>
		<hr>
		<p>
		<?php _e('Select input type below and drag it on right side. Then set more options', "ppom")?>
		</p>
		<div id="form-meta-bttons-new">

			<ul id="nm-input-types">
		<?php
		
		foreach ( PPOM() -> inputs as $type => $meta ) {
			
			if( $meta != NULL ){
				
				echo '<li data-inputtype="' . $type . '">';
				echo '<div><h3><span class="top-heading-text-new button-secondary widefat title">';echo $meta -> title.'</span>';
				echo '<span class="top-heading-icons-new dashicons ui-icon-arrow-4"></span>';
				echo '<span class="top-heading-icons-new dashicons ui-icon-placehorder"></span>';
				echo '<span class="top-heading-icons-new dashicons ui-icon-placehorder-copy"></span>';
				echo '<span style="clear:both;display:block"></span>';
				echo '</h3>';
				
				// this function Defined below
				echo render_input_settings ( $meta -> settings );
				
				echo '</div></li>';
				// echo '<div><p>'.$data['desc'].'</p></div>';
				
			}
			
		}
		?>
		</ul>
		
		<?php do_action('ppom_after_ppom_field_admin');?>
		</div>


		<div id="form-meta-setting" class="postbox-container">

			<div id="postcustoms">
				<h3>
					<span style="float: left;font-weight: 200;"><?php _e('Drag FORM fields here', "ppom")?></span>
					<!-- <span style="float: right"><span style="float: right"
						title="<?php _e('Collapse all', "ppom")?>"
						class="dashicons dashicons-arrow-up-alt2"></span><span
						title="<?php _e('Expand all', "ppom")?>"
						class="dashicons dashicons-arrow-down-alt2"></span></span> <span
						class="clearfix"></span> -->
					<span class="dashicons dashicons-minus" style="float: right"></span>
					<div class="clear"></div>
				</h3>
				<div class="inside" style="background-color: #fff;">
					<ul id="meta-input-holder">
					<?php render_existing_form_meta($product_meta, PPOM() -> inputs)?>
					</ul>
				</div>
			</div>
		</div>

		<div class="clearfix"></div>
	</div>
	<div class="save-settings">
		<button class="btn btn-primary pull-right"
			onclick="save_form_meta()"><?php _e('Save settings', "ppom")?></button>
		<span id="nm-saving-form" style="display:none" class="pull-right"><img alt="saving..." src="<?php echo esc_url(PPOM_URL.'/images/loading.gif')?>"></span>
		<div class="clear"></div>
	</div>
</div>

<!-- ui dialogs -->
<div id="remove-meta-confirm"
	title="<?php _e('Are you sure?', "ppom")?>">
	<p>
		<span class="ui-icon ui-icon-alert"
			style="float: left; margin: 0 7px 20px 0;"></span>
  <?php _e('Are you sure to remove this input field?', "ppom")?></p>
</div>

<?php
function render_input_settings($settings, $values = '') {
	
	// ppom_pa($values);
	$setting_html = '<table cellspacing="0" cellpadding="0" class="ppom-meta-settings-table">';
	if ($settings != '') {
		
		foreach ( $settings as $meta_type => $data ) {
			
			$default_value		= isset($data ['default']) ? $data ['default'] : '';
			
			$data_values = $default_value;
			if ( !empty( $values [$meta_type]) ){
				$data_values = $values [$meta_type];
			}
			
			$tr_class	= "{$meta_type}";
			
			if( empty($data_values) && isset($data['hidden'])) {
				// $hidden		= isset($data['hidden'])  ? $data['hidden'] : false;
				$tr_class	.= ' ppom-field-hide';
			}
			
			if(($meta_type == 'editing_tools' || $meta_type == 'photo_editing') && !ppom_is_aviary_installed()){
				continue;
			}
			
			$colspan	= ($data ['type'] == 'html-conditions' ? 'colspan="2"' : '' );
			
			$td_class_1 = 'table-column-title';
			$td_class_2 = 'table-column-input';
			$td_class_3 = 'table-column-desc';
			
			 
			$setting_html .= '<tr class="'.trim($tr_class).'">';
			$setting_html .= '<td class="'.esc_attr($td_class_1).'">' . $data ['title'] . '</td>';
			
			$input_data_options	= (isset( $data ['options'] ) ? $data ['options'] : '');
			$description		= isset($data ['desc']) ? $data ['desc'] : '';
			
			$placeholders		= isset($data['placeholders']) ? $data['placeholders'] : '';
			
			
			
			$setting_html	.= '<td '.$colspan.' class="'.esc_attr($td_class_2).'" ';
			$setting_html	.= 'data-type="' . $data ['type'] . '" data-name="' . $meta_type . '">' . render_input_types ( $meta_type, $data_values, $data ) . '</td>';
				
			//removing the desc column for type: html-conditions
			if ($data ['type'] != 'html-conditions') {
				$setting_html	.= '<td  class="'.esc_attr($td_class_3).'">' . stripslashes($description) . '</td>';;
			}
			
			$setting_html .= '</tr>';
		}
		
	}
	
	$setting_html .= '</table>';
	
	return $setting_html;
}

/*
 * this function is rendring input field for settings
 */
function render_input_types($name, $value = '', $data) {
	
	$type			= (isset( $data ['type'] ) ? $data ['type'] : '');
	$options		= (isset( $data ['options'] ) ? $data ['options'] : '');
	$placeholders	= isset($data['placeholders']) ? $data['placeholders'] : '';
	
	
	$plugin_meta = ppom_get_plugin_meta();
	$html_input = '';
	
	if(!is_array($value))
		$value = stripslashes($value);
	
	switch ($type) {
		
		case 'number':
		case 'text' :
			$html_input .= '<input type="'.esc_attr($type).'" name="' . esc_attr($name) . '" value="' . esc_html( $value ). '">';
			break;
		
		case 'textarea' :
			$html_input .= '<textarea name="' . esc_attr($name) . '">' . esc_html( $value ) . '</textarea>';
			break;
		
		case 'select' :
			$html_input .= '<select id="'.$name.'" name="' . esc_attr($name) . '">';
			foreach ( $options as $key => $val ) {
				$selected = ($key == $value) ? 'selected="selected"' : '';
				$html_input .= '<option value="' . $key . '" ' . $selected . '>' . esc_html( $val ) . '</option>';
			}
			$html_input .= '</select>';
			break;
		
		case 'paired' :
			
			$plc_option = (!empty($placeholders)) ? $placeholders[0] : __('Option','ppom');
			$plc_price = (!empty($placeholders)) ? $placeholders[1] : __('Price (optional)', 'ppom');
			
			$weight_unit = get_option('woocommerce_weight_unit');
			$plc_weight = (isset($placeholders[2]) && !empty($placeholders)) ? $placeholders[2] : __("Weight-{$weight_unit} (PRO only)", 'ppom');
			if( ppom_pro_is_installed() ) {
				$plc_weight = (isset($placeholders[2]) && !empty($placeholders)) ? $placeholders[2] : __("Weight-{$weight_unit} (optional)", 'ppom');
			}
			
			$plc_id = (isset($placeholders[3]) && !empty($placeholders)) ? $placeholders[3] : __('Unique Option ID)', 'ppom');
			
			$add_option_img = $plugin_meta['url'].'/images/plus.png';
			$del_option_img = $plugin_meta['url'].'/images/minus.png';
			
			
			$html_input .= '<ul class="ppom-options-container">';
			
			
			if($value){
				foreach ($value as $option){
					
					$weight = isset($option['weight']) ? $option['weight'] : '';
					$option_id = ppom_get_option_id($option);
					
					$html_input .= '<li class="data-options">';
					$html_input .= '<span class="dashicons dashicons-move"></span>';
					$html_input .= '<input type="text" class="option-title" name="options[option]" value="'.esc_attr(stripslashes($option['option'])).'" placeholder="'.$plc_option.'">';
					$html_input .= '<input type="text" class="option-price" name="options[price]" value="'.esc_attr($option['price']).'" placeholder="'.$plc_price.'">';
					$html_input .= '<input type="text" class="option-weight" name="options[weight]" value="'.esc_attr($weight).'" placeholder="'.$plc_weight.'">';
					
					$html_input .= '<input type="text" class="option-id" name="options[id]" value="'.esc_attr($option_id).'" placeholder="'.$plc_id.'">';
					$html_input	.= '<img class="add_option" src="'.esc_url($add_option_img).'" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
					$html_input	.= '<img class="remove_option" src="'.esc_url($del_option_img).'" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
					$html_input .= '</li>';
				}
			}else{
				$html_input .= '<li class="data-options">';
				$html_input .= '<span class="dashicons dashicons-move"></span>';
				$html_input .= '<input type="text" class="option-title" name="options[option]" placeholder="'.$plc_option.'">';
				$html_input .= '<input type="text" class="option-price" name="options[price]" placeholder="'.$plc_price.'">';
				$html_input .= '<input type="text" class="option-weight" name="options[weight]" placeholder="'.$plc_weight.'">';
				$html_input .= '<input type="text" class="option-id" name="options[id]" placeholder="'.$plc_id.'">';
				$html_input	.= '<img class="add_option" src="'.esc_url($add_option_img).'" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
				$html_input	.= '<img class="remove_option" src="'.esc_url($del_option_img).'" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
				$html_input .= '</li>';
			}
			
			$html_input	.= '<ul/>';
			
			break;
			
			
		case 'paired-quantity' :
			
			$html_input .= '<ul class="ppom-options-container">';
			
			$add_option_img = $plugin_meta['url'].'/images/plus.png';
			$del_option_img = $plugin_meta['url'].'/images/minus.png';
			
			if($value){
				foreach ($value as $option){
					$html_input .= '<li class="data-options">';
					$html_input .= '<span class="dashicons dashicons-move"></span>';
					$html_input .= '<input type="text" name="options[option]" value="'.esc_attr(stripslashes($option['option'])).'" placeholder="'.__('option',"ppom").'">';
					$html_input .= '<input type="text" name="options[price]" value="'.esc_attr($option['price']).'" placeholder="'.__('price (if any)',"ppom").'">';
					$html_input .= '<input type="text" name="options[min]" value="'.esc_attr($option['min']).'" placeholder="'.__('Min. Qty',"ppom").'">';
					$html_input .= '<input type="text" name="options[max]" value="'.esc_attr($option['max']).'" placeholder="'.__('Max. Qty',"ppom").'">';
					$html_input	.= '<img class="add_option" src="'.esc_url($add_option_img).'" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
					$html_input	.= '<img class="remove_option" src="'.esc_url($del_option_img).'" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
					$html_input .= '</li>';
				}
			}else{
				$html_input .= '<li class="data-options">';
				$html_input .= '<span class="dashicons dashicons-move"></span>';
				$html_input .= '<input type="text" name="options[option]" placeholder="'.__('option',"ppom").'">';
				$html_input .= '<input type="text" name="options[price]" placeholder="'.__('price (if any)',"ppom").'">';
				$html_input .= '<input type="text" name="options[min]" placeholder="'.__('Min. Qty',"ppom").'">';
				$html_input .= '<input type="text" name="options[max]" placeholder="'.__('Max. Qty',"ppom").'">';
				$html_input	.= '<img class="add_option" src="'.esc_url($add_option_img).'" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
				$html_input	.= '<img class="remove_option" src="'.esc_url($del_option_img).'" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
				$html_input .= '</li>';
			}
			
			$html_input	.= '<ul/>';
			
			break;
			
		case 'paired-measure' :
			
			$html_input .= '<ul class="ppom-options-container">';
			
			$add_option_img = $plugin_meta['url'].'/images/plus.png';
			$del_option_img = $plugin_meta['url'].'/images/minus.png';
			$plc_id = (!empty($placeholders)) ? $placeholders[2] : __('Unique ID)', 'ppom');
			
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
			
			$html_input .= '<ul class="ppom-options-container ppom-cropper-boundary">';
			
			// var_dump($value); exit;
			$add_option_img = $plugin_meta['url'].'/images/plus.png';
			$del_option_img = $plugin_meta['url'].'/images/minus.png';
			
			if($value){
				foreach ($value as $option){
					$html_input .= '<li class="data-options">';
					$html_input .= '<span class="dashicons dashicons-move"></span>';
					$html_input .= '<input type="text" name="options[option]" value="'.esc_attr(stripslashes($option['option'])).'" placeholder="'.__('Label',"ppom").'">';
					$html_input .= '<input type="text" name="options[width]" value="'.esc_attr(stripslashes($option['width'])).'" placeholder="'.__('Width',"ppom").'">';
					$html_input .= '<input type="text" name="options[height]" value="'.esc_attr($option['height']).'" placeholder="'.__('Height',"ppom").'">';
					$html_input .= '<input type="text" name="options[price]" value="'.esc_attr($option['price']).'" placeholder="'.__('Price (optional)',"ppom").'">';
					$html_input	.= '<img class="add_option" src="'.esc_url($add_option_img).'" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
					$html_input	.= '<img class="remove_option" src="'.esc_url($del_option_img).'" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
					$html_input .= '</li>';
				}
			}else{
				$html_input .= '<li class="data-options">';
				$html_input .= '<span class="dashicons dashicons-move"></span>';
				$html_input .= '<input type="text" name="options[option]" placeholder="'.__('option',"ppom").'">';
				$html_input .= '<input type="text" name="options[width]" placeholder="'.__('Width',"ppom").'">';
				$html_input .= '<input type="text" name="options[height]" placeholder="'.__('Height',"ppom").'">';
				$html_input .= '<input type="text" name="options[price]" placeholder="'.__('Price (optional)',"ppom").'">';
				$html_input	.= '<img class="add_option" src="'.esc_url($add_option_img).'" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
				$html_input	.= '<img class="remove_option" src="'.esc_url($del_option_img).'" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
				$html_input .= '</li>';
			}
			
			$html_input	.= '<ul/>';
			
			break;
			
		case 'checkbox' :
			
			if ($options) {
				foreach ( $options as $key => $val ) {
					
					parse_str ( $value, $saved_data );
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
					$html_input .= '<input type="checkbox" value="' . $key . '" name="' . $name . '[]" ' . $checked . '> ' . $val . '<br>';
				}
			} else {
				$checked = ( (isset($value) && $value != '' ) ? 'checked = "checked"' : '' );
					
				$html_input .= '<input type="checkbox" name="' . $name . '" ' . $checked . '>';
			}
			break;
			
		case 'html-conditions' :
			
			$add_option_img = $plugin_meta['url'].'/images/plus.png';
			$del_option_img = $plugin_meta['url'].'/images/minus.png';
			
			
			$rule_i = 1;
			if($value){
				
	
					$visibility_show = ($value['visibility'] == 'Show') ? 'selected="selected"' : '';
					$visibility_hide = ($value['visibility'] == 'Hide') ? 'selected="selected"' : '';
					
					$html_input	 = '<select name="condition_visibility">';
					/*$html_input .= '<option '.$visibility_show.'>'.__('Show',"ppom").'</option>';
					$html_input .= '<option '.$visibility_hide.'>'.__('Hide', "ppom").'</option>';*/
					$html_input .= '<option '.$visibility_show.'>Show</option>';
					$html_input .= '<option '.$visibility_hide.'>Hide</option>';
					$html_input	.= '</select> ';
					
					
					$html_input .= __('only if', "ppom");
					
					$bound_all = ($value['bound'] == 'All') ? 'selected="selected"' : '';
					$bound_any = ($value['bound'] == 'Any') ? 'selected="selected"' : '';
					
					$html_input	.= '<select name="condition_bound">';
					/*$html_input 	.= '<option '.$bound_all.'>'.__('All',"ppom").'</option>';
					$html_input .= '<option '.$bound_any.'>'.__('Any', "ppom").'</option>';*/
					$html_input 	.= '<option '.$bound_all.'>All</option>';
					$html_input .= '<option '.$bound_any.'>Any</option>';
					$html_input	.= '</select> ';
						
					$html_input .= __(' of the following matches', "ppom");
					
					
				foreach ($value['rules'] as $condition){
					
					$element_values = isset($condition['element_values']) ? stripslashes($condition['element_values']) : '';
					// conditional elements
					$html_input .= '<div class="webcontact-rules" id="rule-box-'.$rule_i.'">';
					$html_input .= '<br><strong>'.__('Rule # ', "ppom") . $rule_i++ .'</strong><br>';
					$html_input .= '<select name="condition_elements" data-existingvalue="'.$condition['elements'].'" onblur="load_conditional_values(this)"></select>';
					
					// is
					
					$operator_is 		= ($condition['operators'] == 'is') ? 'selected="selected"' : '';
					$operator_not 		= ($condition['operators'] == 'not') ? 'selected="selected"' : '';
					$operator_greater 	= ($condition['operators'] == 'greater than') ? 'selected="selected"' : '';
					$operator_less 		= ($condition['operators'] == 'less than') ? 'selected="selected"' : '';
					
					$html_input .= '<select name="condition_operators">';
					/*$html_input	.= '<option '.$operator_is.'>'.__('is',"ppom").'</option>';
					$html_input .= '<option '.$operator_not.'>'.__('not', "ppom").'</option>';
					$html_input .= '<option '.$operator_greater.'>'.__('greater then', "ppom").'</option>';
					$html_input .= '<option '.$operator_less.'>'.__('less then', "ppom").'</option>';*/
					$html_input	.= '<option '.$operator_is.'>is</option>';
					$html_input .= '<option '.$operator_not.'>not</option>';
					$html_input .= '<option '.$operator_greater.'>greater than</option>';
					$html_input .= '<option '.$operator_less.'>less than</option>';
					$html_input	.= '</select> ';
					
					// conditional elements values
					$html_input .= '<select name="condition_element_values" data-existingvalue="'.esc_attr($element_values).'"></select>';
					$html_input	.= '<img class="add_rule" src="'.esc_url($add_option_img).'" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
					$html_input	.= '<img class="remove_rule" src="'.esc_url($del_option_img).'" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
					$html_input .= '</div>';
					
				}
			}else{

					
				$html_input	 = '<select name="condition_visibility">';
				$html_input .= '<option>Show</option>';
				$html_input .= '<option>Hide</option>';
				$html_input	.= '</select> ';
					
				$html_input	.= '<select name="condition_bound">';
				$html_input .= '<option>All</option>';
				$html_input .= '<option>Any</option>';
				$html_input	.= '</select> ';
					
				$html_input .= __(' of the following matches', "ppom");
				// conditional elements
				
				$html_input .= '<div class="webcontact-rules" id="rule-box-'.$rule_i.'">';
				$html_input .= '<br><strong>'.__('Rule # ', "ppom") . $rule_i++ .'</strong><br>';
				$html_input .= '<select name="condition_elements" data-existingvalue="" onblur="load_conditional_values(this)"></select>';
					
				// is
					
				$html_input .= '<select name="condition_operators">';
				$html_input	.= '<option>is</option>';
				$html_input .= '<option>not</option>';
				$html_input .= '<option>greater than</option>';
				$html_input .= '<option>less than</option>';
				$html_input	.= '</select> ';
					
				// conditional elements values
				$html_input .= '<select name="condition_element_values" data-existingvalue=""></select>';
				$html_input	.= '<img class="add_rule" src="'.esc_url($add_option_img).'" title="add rule" alt="add rule" style="cursor:pointer; margin:0 3px;">';
				$html_input	.= '<img class="remove_rule" src="'.esc_url($add_option_img).'" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;">';
				$html_input .= '</div>';
			}

			break;
			
			case 'pre-images' :
			
				
				//$html_input	.= '<textarea name="pre_upload_images">'.$pre_uploaded_images.'</textarea>';
				$html_input	.= '<div class="pre-upload-box">';
				$html_input	.= '<input name="pre_upload_image_button" type="button" value="'.__('Select/Upload Image', "ppom").'" />';
				// ppom_pa($value);
				
				
				if ($value) {
					
					$html_input .= '<ul class="ppom-options-container">';
					foreach ($value as $pre_uploaded_image){
				
						$image_link = (isset($pre_uploaded_image['link']) ? $pre_uploaded_image['link'] : '');
						$image_id = (isset($pre_uploaded_image['id']) ? $pre_uploaded_image['id'] : '');
						$image_url = (isset($pre_uploaded_image['url']) ? $pre_uploaded_image['url'] : '');
						
						$image_name = isset($pre_uploaded_image['link']) ? basename($pre_uploaded_image['link']) : '';
						
						$html_input .= '<li class="data-options">';
						$html_input .= '<span class="dashicons dashicons-move"></span>';
						$html_input .='<table>';
						$html_input .= '<tr>';
						$html_input .= '<td colspan="2">';
						$html_input .= $image_name;
						$html_input .= '</td>';
						$html_input .= '</tr>';
						$html_input .= '<tr>';
						$html_input .= '<td><img width="75" src="'.esc_url($image_link).'"></td>';
						$html_input .= '<input type="hidden" name="pre-upload-link" value="'.esc_url($image_link).'">';
						$html_input .= '<input type="hidden" name="pre-upload-id" value="'.esc_attr($image_id).'">';
						$html_input .= '<td><input style="width:100px" type="text" placeholder="Title" value="'.esc_attr(stripslashes($pre_uploaded_image['title'])).'" name="pre-upload-title"><br>';
						$html_input .= '<input style="width:100px" type="text" placeholder="Price (fix or %)" value="'.esc_attr(stripslashes($pre_uploaded_image['price'])).'" name="pre-upload-price"><br>';
						$html_input .= '<input style="width:100px" type="text" placeholder="URL" value="'.esc_url(stripslashes($pre_uploaded_image['url'])).'" name="pre-upload-url"><br>';
						$html_input .= '<input style="width:100px; color:red" name="pre-upload-delete" type="button" class="button" value="Delete"><br>';
						$html_input .= '</td></tr>';
						$html_input .= '</table>';
						$html_input .= '</li>';
				
					}
					
					$html_input .= '</ul>';
					//$pre_uploaded_images = $value;
				}
				
				$html_input .= '</div>';
			
			break;
			
			case 'imageselect' :
			
				
				//$html_input	.= '<textarea name="pre_upload_images">'.$pre_uploaded_images.'</textarea>';
				$html_input	.= '<div class="imageselect-box">';
				$html_input	.= '<input name="imageselect_button" type="button" value="'.__('Select/Upload Image', "ppom").'" />';
				
				
				if ($value) {
					
					$html_input .= '<ul class="ppom-options-container">';
					foreach ($value as $pre_uploaded_image){
				
						$image_link 	= (isset($pre_uploaded_image['link']) ? $pre_uploaded_image['link'] : '');
						$image_id		= (isset($pre_uploaded_image['id']) ? $pre_uploaded_image['id'] : '');
						$description	= (isset($pre_uploaded_image['description']) ? $pre_uploaded_image['description'] : '');
						
						$image_name = isset($pre_uploaded_image['link']) ? basename($pre_uploaded_image['link']) : '';
						
						$html_input .= '<li class="data-options">';
						$html_input .= '<span class="dashicons dashicons-move"></span>';
						$html_input .='<table>';
						$html_input .= '<tr>';
						$html_input .= '<td colspan="2">';
						$html_input .= $image_name;
						$html_input .= '</td>';
						$html_input .= '</tr>';
						$html_input .= '<tr>';
						$html_input .= '<td><img width="75" src="'.esc_url($image_link).'"></td>';
						$html_input .= '<input type="hidden" name="imageselect-link" value="'.esc_url($image_link).'">';
						$html_input .= '<input type="hidden" name="imageselect-id" value="'.esc_attr($image_id).'">';
						$html_input .= '<td><input style="width:100px" type="text" placeholder="Title" value="'.esc_attr(stripslashes($pre_uploaded_image['title'])).'" name="imageselect-title"><br>';
						$html_input .= '<input style="width:100px" type="text" placeholder="Price (fix or %)" value="'.esc_attr(stripslashes($pre_uploaded_image['price'])).'" name="imageselect-price"><br>';
						$html_input .= '<input style="width:100px" type="text" placeholder="Description" value="'.esc_attr(stripslashes($description)).'" name="imageselect-description"><br>';
						$html_input .= '<input style="width:100px; color:red" name="imageselect-delete" type="button" class="button" value="Delete"><br>';
						$html_input .= '</td></tr>';
						$html_input .= '</table>';
						$html_input .= '</li>';
				
					}
					
					$html_input .= '</ul>';
					//$pre_uploaded_images = $value;
				}
				
				$html_input .= '</div>';
			
			break;
			
			case 'pre-audios' :
			
				
				//$html_input	.= '<textarea name="pre_upload_images">'.$pre_uploaded_images.'</textarea>';
				$html_input	.= '<div class="pre-upload-box">';
				$html_input	.= '<input name="pre_upload_image_button" type="button" value="'.__('Select Audio/Video', "ppom").'" />';
				
				if ($value) {
					
					$html_input .= '<ul class="ppom-options-container">';
					foreach ($value as $pre_uploaded_image){
				
						$image_link = (isset($pre_uploaded_image['link']) ? $pre_uploaded_image['link'] : '');
						$image_id = (isset($pre_uploaded_image['id']) ? $pre_uploaded_image['id'] : '');
						$image_url = (isset($pre_uploaded_image['url']) ? $pre_uploaded_image['url'] : '');
						$media_title = (isset($pre_uploaded_image['title']) ? stripslashes($pre_uploaded_image['title']) : '');
						$media_price = (isset($pre_uploaded_image['price']) ? stripslashes($pre_uploaded_image['price']) : '');
						
						$html_input .= '<li class="data-options">';
						$html_input .= '<span class="dashicons dashicons-move"></span>';
						$html_input .='<table>';
						$html_input .= '<tr>';
						$html_input .= '<td><span class="dashicons dashicons-admin-media"></span></td>';
						$html_input .= '<input type="hidden" name="pre-upload-link" value="'.esc_url($image_link).'">';
						$html_input .= '<input type="hidden" name="pre-upload-id" value="'.esc_attr($image_id).'">';
						$html_input .= '<td><input style="width:100px" type="text" value="'.esc_attr($media_title).'" name="pre-upload-title"><br>';
						$html_input .= '<input style="width:100px" type="text" value="'.esc_attr($media_price).'" name="pre-upload-price"><br>';
						$html_input .= '<input style="width:100px; color:red" name="pre-upload-delete" type="button" class="button" value="Delete"><br>';
						$html_input .= '</td></tr>';
						$html_input .= '</table>';
						$html_input .= '</li>';
				
					}
					
					$html_input .= '</ul>';
					//$pre_uploaded_images = $value;
				}
				
				$html_input .= '</div>';
			
			break;
			
			/**
			 * new addon: bulk quantity
			 * @since 7.1
			 **/
			 case 'bulk-quantity' :
			
			if($value){
				$bulk_data = json_decode($value, true);

				$html_input .= '<div class="bulk-quantity-wrap">';
				$html_input .= '<table border="1" id="mtable">';
				$html_input .= '<thead><tr>';

					foreach ($bulk_data[0] as $title => $value) {
						$deleteIcon = ($title != 'Quantity Range' && $title != 'Base Price') ? '<span class="dashicons dashicons-dismiss delete-col" style="cursor: pointer;color: red;"></span>' : '' ;
						$html_input .= '<td>'.$title.' '.$deleteIcon.'</td>';
					}

				$html_input .= '</tr></thead>';
				$html_input .= '<tbody>';
				
					foreach ($bulk_data as $row => $data) {

						$html_input .= '<tr>';

						foreach ($data as $key => $value) {
							$resetArr = reset($data);
							$delRow = ($resetArr == $value) ? '<span class="dashicons dashicons-dismiss delete-row" style="cursor: pointer;color: red;"></span>' : '' ;
							if (1) {
								$html_input .= '<td>'.$delRow.'<input type="text" value="'.$value.'"></td>';
							}
						}

						$html_input .= '</tr>';

					}
				$html_input .= '</tbody>';
				$html_input .= '</table><br>';

				$html_input .= '<input placeholder="1-10" class="small-text qty-val" /><button id="irow">'.__("Add Qty Range", "ppom").'</button><br><br>';
				$html_input .= '<input placeholder="Variation" class="small-text var-val" /><button id="icol">'.__("Add Variation", "ppom").'</button>';
				$html_input .= "<input type='hidden' name='options' class='saving-bulk-qty' value='".json_encode($bulk_data)."' />";
				$html_input .= '<br><br><button class="save-bulk-data button button-primary">'.__("Save Changes", "ppom").'</button>';
				$html_input .= '</div>';
			}else{
				$html_input .= '<div class="bulk-quantity-wrap">';
				$html_input .= '<table border="1" id="mtable">';
				$html_input .= '<thead><tr><td>'.__('Quantity Range', "ppom").'</td><td>'.__('Base Price', "ppom").'</td></tr></thead>';
				$html_input .= '<tbody><tr><td contenteditable="true">1-10</td><td><input type="text" class="small-text" /></td></tr></tbody>';
				$html_input .= '</table><br>';

				$html_input .= '<input placeholder="1-10" class="small-text qty-val" /><button id="irow">'.__("Add Qty Range", "ppom").'</button><br><br>';
				$html_input .= '<input placeholder="Variation" class="small-text var-val" /><button id="icol">'.__("Add Variation", "ppom").'</button>';
				$html_input .= '<input type="hidden" name="options" class="saving-bulk-qty" />';
				$html_input .= '<br><br><button class="save-bulk-data button button-primary">'.__("Save Changes", "ppom").'</button>';
				$html_input .= '</div>';
			}
			
			break;
	}
	
	return apply_filters('render_input_types', $html_input, $type, $name, $value, $options);
}


/*
 * this function is rendering the existing form meta
 */
function render_existing_form_meta($product_meta, $types) {
	
	if ($product_meta) {
		foreach ( $product_meta as $key => $meta ) {
			
			$type = $meta ['type'];
			$name = isset($meta['data_name']) ? $meta['data_name'] : '';
			
			if( ! isset($types[$type] -> settings) ) continue;
			
			// ppom_pa($types);
			
			echo '<li data-inputtype="' . esc_attr($type) . '"><div class="inputdata">';
			echo '<h3><span class="top-heading-text-new">' . esc_attr($name) . ' (' . esc_attr($type) . ')</span>';
			echo '<span class="top-heading-icons-new dashicons dashicons-image-flip-vertical"></span>';
			echo '<span class="top-heading-icons-new dashicons dashicons-trash"></span>';
			echo '<span class="top-heading-icons-new dashicons dashicons-image-rotate-right"></span>';
			echo '<span style="clear:both;display:block"></span></h3>';
			
			echo render_input_settings ( $types[$type] -> settings, $meta );
			
			echo '</div></li>';
		}
	}
}

?>