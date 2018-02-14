<?php
function pmxi_admin_head(){
	?>	
	<style type="text/css">
		#toplevel_page_pmxi-admin-home ul li:last-child{
			display: none;
		}
	</style>
	<?php	
	
	$input = new PMXI_Input();
	$get_params = $input->get(array(
		'id' => false,
		'action' => false
	));
	
	if ($get_params['id']){
		?>
		<script type="text/javascript">
			var import_id = '<?php echo $get_params["id"]; ?>';			
		</script>
		<?php
	}

	$wp_all_import_ajax_nonce = '';

	if ( get_current_user_id() and current_user_can( PMXI_Plugin::$capabilities )) {

		$wp_all_import_ajax_nonce = wp_create_nonce( "wp_all_import_secure" );		

	}

	?>
	<script type="text/javascript">
		var ajaxurl = '<?php echo admin_url( "admin-ajax.php" ); ?>';
		var import_action = '<?php echo $get_params["action"]; ?>';			
		var wp_all_import_security = '<?php echo $wp_all_import_ajax_nonce; ?>';
	</script>
	<?php
}