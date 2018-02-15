<?php

function pmxe_wp_ajax_wpae_available_rules(){

	if ( ! check_ajax_referer( 'wp_all_export_secure', 'security', false )){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	if ( ! current_user_can( PMXE_Plugin::$capabilities ) ){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	ob_start();

	$input = new PMXE_Input();
	
	$post = $input->post('data', array());

	?>
	<select id="wp_all_export_rule">
		<option value=""><?php _e('Select Rule', 'wp_all_export_plugin'); ?></option>
		<?php
		if (strpos($post['selected'], 'tx_') === 0 || strpos($post['selected'], 'product_tx') === 0){
			?>										

				<!-- Taxonomies -->
				<option value="in"><?php echo __('In', 'wp_all_export_plugin') . ' ' . ucwords(str_replace(array("product_tx", "tx_", "_"), array("", "", " "), $post['selected'])); ?></option>
				<option value="not_in"><?php echo __('Not In', 'wp_all_export_plugin') . ' ' . ucwords(str_replace(array("product_tx", "tx_", "_"), array("", "", " "), $post['selected'])); ?></option>

				<!-- Custom Fields -->
				<!--option value="between">BETWEEN</option-->
			
			<?php
		}
		elseif( in_array($post['selected'], array('post_date', 'post_modified', 'user_registered', 'comment_date', 'cf__completed_date')) )
		{
			?>
			<option value="equals"><?php _e('equals', 'wp_all_export_plugin'); ?></option>
			<option value="not_equals"><?php _e("doesn't equal", 'wp_all_export_plugin'); ?></option>
			<option value="greater"><?php _e('newer than', 'wp_all_export_plugin');?></option>
			<option value="equals_or_greater"><?php _e('equal to or newer than', 'wp_all_export_plugin'); ?></option>
			<option value="less"><?php _e('older than', 'wp_all_export_plugin'); ?></option>
			<option value="equals_or_less"><?php _e('equal to or older than', 'wp_all_export_plugin'); ?></option>

			<option value="contains"><?php _e('contains', 'wp_all_export_plugin'); ?></option>
			<option value="not_contains"><?php _e("doesn't contain", 'wp_all_export_plugin'); ?></option>
			<option value="is_empty"><?php _e('is empty', 'wp_all_export_plugin'); ?></option>
			<option value="is_not_empty"><?php _e('is not empty', 'wp_all_export_plugin'); ?></option>
			<?php
		}
		elseif( in_array($post['selected'], array('wp_capabilities')))
		{
			?>
			<option value="contains"><?php _e('contains', 'wp_all_export_plugin'); ?></option>
			<option value="not_contains"><?php _e("doesn't contain", 'wp_all_export_plugin'); ?></option>
			<?php
		}
		elseif ( in_array($post['selected'], array('user_login', 'user_nicename', 'user_role', 'user_email', 'display_name', 'first_name', 'last_name', 'nickname', 'description', 
			'post_status', 'post_title', 'post_content', 'comment_author_email', 'comment_author_url', 'comment_author_IP', 'comment_agent', 
			'comment_type', 'comment_content') ) ) 
		{
			?>
			<option value="equals"><?php _e('equals', 'wp_all_export_plugin'); ?></option>
			<option value="not_equals"><?php _e("doesn't equal", 'wp_all_export_plugin'); ?></option>
			<option value="contains"><?php _e('contains', 'wp_all_export_plugin'); ?></option>
			<option value="not_contains"><?php _e("doesn't contain", 'wp_all_export_plugin'); ?></option>
			<option value="is_empty"><?php _e('is empty', 'wp_all_export_plugin'); ?></option>
			<option value="is_not_empty"><?php _e('is not empty', 'wp_all_export_plugin'); ?></option>
			<?php
		}
		elseif ( in_array($post['selected'], array('term_parent_slug') ) )
		{
			?>
			<option value="equals"><?php _e('equals', 'wp_all_export_plugin'); ?></option>
			<option value="not_equals"><?php _e("doesn't equal", 'wp_all_export_plugin'); ?></option>
			<option value="greater"><?php _e('greater than', 'wp_all_export_plugin');?></option>
			<option value="equals_or_greater"><?php _e('equal to or greater than', 'wp_all_export_plugin'); ?></option>
			<option value="less"><?php _e('less than', 'wp_all_export_plugin'); ?></option>
			<option value="equals_or_less"><?php _e('equal to or less than', 'wp_all_export_plugin'); ?></option>
			<option value="is_empty"><?php _e('is empty', 'wp_all_export_plugin'); ?></option>
			<option value="is_not_empty"><?php _e('is not empty', 'wp_all_export_plugin'); ?></option>
			<?php
		}
		else
		{
			?>
			<option value="equals"><?php _e('equals', 'wp_all_export_plugin'); ?></option>
			<option value="not_equals"><?php _e("doesn't equal", 'wp_all_export_plugin'); ?></option>
			<option value="greater"><?php _e('greater than', 'wp_all_export_plugin');?></option>
			<option value="equals_or_greater"><?php _e('equal to or greater than', 'wp_all_export_plugin'); ?></option>
			<option value="less"><?php _e('less than', 'wp_all_export_plugin'); ?></option>
			<option value="equals_or_less"><?php _e('equal to or less than', 'wp_all_export_plugin'); ?></option>

			<option value="contains"><?php _e('contains', 'wp_all_export_plugin'); ?></option>
			<option value="not_contains"><?php _e("doesn't contain", 'wp_all_export_plugin'); ?></option>
			<option value="is_empty"><?php _e('is empty', 'wp_all_export_plugin'); ?></option>
			<option value="is_not_empty"><?php _e('is not empty', 'wp_all_export_plugin'); ?></option>
			<?php
		}
	?>
	</select>
	<?php

	exit(json_encode(array('html' => ob_get_clean()))); die;

}