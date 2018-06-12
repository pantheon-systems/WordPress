<h2>
    <?php _e('Import Into Google Merchants Center', 'wp_all_export_plugin') ?>
</h2>

<p>
    <?php _e('Now that your export has been set up, you need to create a feed in Google Merchants Center and give it the URL of your export file from WP All Export.', 'wp_all_export_plugin'); ?>
</p>

<p>1. Go to the Goole Merchants Center Dashboard</p>
<p>2. Click the big blue button to create a new feed</p>
<p>3. On Page 2, select <em>Scheduled Field</em></p>
<p>4. Use whatever you want for the File Name</p>
<p>5. Use the URL below for the File URL</p>
<p><strong>Export Feed URL: <a href="<?php echo $file_path; ?>"><?php echo $file_path; ?></a></strong></p>
