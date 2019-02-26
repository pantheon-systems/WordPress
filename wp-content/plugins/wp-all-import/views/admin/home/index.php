<div class="wrap">
	<?php
		$homeurl = "http://www.wpallimport.com/adminpanel/index.php?v=".urlencode(PMXI_Plugin::getInstance()->getVersion());
		$contents = @file_get_contents($homeurl);
		if ( ! $contents) {
			?>
			<iframe src='<?php echo $homeurl; ?>' width='600'></iframe><br />
			<?php
		} else {
			echo $contents;
		}
	?>
</div>



