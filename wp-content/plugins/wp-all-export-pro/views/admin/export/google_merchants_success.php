<div class="google-merchants-success">
    <input type="hidden" id="wpae_wp_hidden_field" class="<?php echo wp_create_nonce( '_wpnonce-download_feed' ); ?>" />
    <div class="wpae-container">
        <h3>What's next?</h3>

        <ol>
            <li>1. Go to the <a class="merchants-dashboard-url" href="https://merchants.google.com/mc/feeds/dashboard?scope=products" target="_blank">Google Merchants Dashboard</a></li>
            <li>2. Click the big blue button to create a new feed</li>
            <li>3. On Page 2, select <em>Scheduled fetch</em></li>
            <li>4. Use whatever you want for the File Name</li>
            <li>5. Use the URL below for the File URL</li>
        </ol>
    </div>
    <p class="feed-url-title">
        Export Feed URL
    </p>
    <p class="feed-url">
        <a href="<?php echo $urlToExport;?>" <?php if(php_sapi_name() != 'cli-server') { ?> target="_blank" <?php } ?> class="feed-url"><?php echo $urlToExport;  ?></a>
    </p>
</div>
