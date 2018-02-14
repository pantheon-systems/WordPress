<?php
function pmxe_admin_head(){
	$input = new PMXE_Input();
	$export_id = $input->get('id', false);
	$export_action = $input->get('action', false);	
	if ($export_id){
		?>
		<script type="text/javascript">
			var export_id = '<?php echo $export_id; ?>';			
		</script>
		<?php
	}

	$wp_all_export_ajax_nonce = wp_create_nonce("wp_all_export_secure");

	?>
		<script type="text/javascript" id="googleMerchantsInit">
			if(typeof GoogleMerchants != 'undefined') {
				GoogleMerchants.constant('NONCE', '<?php echo $wp_all_export_ajax_nonce; ?>');
			}
			var ajaxurl = '<?php echo admin_url( "admin-ajax.php" ); ?>';
			var export_action = '<?php echo $export_action; ?>';
			var wp_all_export_security = '<?php echo $wp_all_export_ajax_nonce; ?>';
		</script>
	<?php
}