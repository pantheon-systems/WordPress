<?php 

function pmxe_admin_notices() {
		
	// notify user if history folder is not writable
	$uploads = wp_upload_dir();			

	$input = new PMXE_Input();
	$messages = $input->get('pmxe_nt', array());
	if ($messages) {
		is_array($messages) or $messages = array($messages);
		foreach ($messages as $type => $m) {			
			in_array((string)$type, array('updated', 'error')) or $type = 'updated';
			?>
			<div class="<?php echo $type ?>"><p><?php echo $m ?></p></div>
			<?php 
		}
	}
}