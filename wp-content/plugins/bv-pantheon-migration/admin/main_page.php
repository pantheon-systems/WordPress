<?php
		$_error = NULL;
		if (array_key_exists('error', $_REQUEST)) {
			$_error = $_REQUEST['error'];
		}
?>
		<div class="logo-container" style="padding: 50px 0px 10px 20px">
			<a href="http://blogvault.net/" style="padding-right: 20px;"><img src="<?php echo plugins_url($this->getPluginLogo(), __FILE__); ?>" /></a>
		</div>

		<div id="wrapper toplevel_page_ptn-automated-migration">
			<form id="ptn_migrate_form" dummy=">" action="<?php echo $this->bvinfo->appUrl(); ?>/home/migrate" onsubmit="document.getElementById('migratesubmit').disabled = true;" style="padding:0 2% 2em 1%;" method="post" name="signup">
				<h1>Migrate Site to Pantheon</h1>
				<p><font size="3">This plugin makes it very easy to migrate your site to Pantheon</font></p>
<?php if ($_error == "email") {
	echo '<div class="error" style="padding-bottom:0.5%;"><p>There is already an account with this email.</p></div>';
} else if ($_error == "blog") {
	echo '<div class="error" style="padding-bottom:0.5%;"><p>Could not create an account. Please contact <a href="http://blogvault.net/contact/">blogVault Support</a><br />
		<font color="red">NOTE: We do not support automated migration of locally hosted sites.</font></p></div>';
} else if (($_error == "custom") && isset($_REQUEST['bvnonce']) && wp_verify_nonce($_REQUEST['bvnonce'], "bvnonce")) {
	echo '<div class="error" style="padding-bottom:0.5%;"><p>'.base64_decode($_REQUEST['message']).'</p></div>';
}
?>
				<input type="hidden" name="bvsrc" value="wpplugin" />
				<input type="hidden" name="migrate" value="pantheon" />
				<input type="hidden" name="type" value="sftp" />
<?php echo $this->siteInfoTags(); ?>
				<p>No Pantheon site yet? Start by <a href="https://dashboard.pantheon.io/sites/migrate">migrating an existing site</a> on your Pantheon dashboard.</p>
				<div class="row-fluid">
					<div class="span5" style="border-right: 1px solid #EEE; padding-top:1%;">
						<label class="control-label" for="input02">Pantheon Site Name</label>
						<div class="control-group">
							<div class="controls">
								<input type="text" class="input-large" name="newurl">
							</div>
						</div>
						<label class="control-label" for="input01">Machine Token</label>
						<div class="control-group">
							<div class="controls">
								<input type="text" class="input-large" name="machine_token">
							</div>
						</div>
<?php if (array_key_exists('auth_required_source', $_REQUEST)) { ?>
						<div id="source-auth">
							<label class="control-label" for="input02" style="color:red">User <small>(for this site)</small></label>
							<div class="control-group">
								<div class="controls">
									<input type="text" class="input-large" name="httpauth_src_user">
								</div>
							</div>
							<label class="control-label" for="input02" style="color:red">Password <small>(for this site)</small></label>
							<div class="control-group">
								<div class="controls">
									<input type="password" class="input-large" name="httpauth_src_password">
								</div>
							</div>
						</div>
<?php } ?>
<?php if (array_key_exists('auth_required_dest', $_REQUEST)) { ?>
            <label class="control-label" for="input02" style="color:red">Username <small>(for Pantheon Install)</small></label>
            <div class="control-group">
              <div class="controls">
                <input type="text" class="input-large" name="httpauth_dest_user">
              </div>
            </div>
            <label class="control-label" for="input02" style="color:red">Password <small>(for Pantheon Install)</small></label>
            <div class="control-group">
              <div class="controls">
                <input type="password" class="input-large" name="httpauth_dest_password">
              </div>
            </div>
<?php } ?>
						<div class="control-group">
						  <div class="controls">
								<br><input type="checkbox" name="consent" onchange="document.getElementById('migratesubmit').disabled = !this.checked;" value="1"/>I agree to Blogvault <a href="https://blogvault.net/tos" target="_blank" rel="noopener noreferrer">Terms of Service</a> and <a href="https://blogvault.net/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>
							</div>
						</div>
						</div>

					</div>
				</div>
				<input type='submit' disabled id='migratesubmit' value='Migrate'>
			</form>
		</div> <!-- wrapper ends here -->