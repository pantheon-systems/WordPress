<style type="text/css">
    .wrap .msg { background: #abffb8; padding:1em; }
    .wrap .err { background: #ffcc91; padding:1em; }
    .wrap > .img { float:right; max-width: 50%; height:auto; margin-left:1em; }
    .wrap > .settings { clear:both; margin-top:1.6em; border:1px solid #fff; padding:1em 1em 0; }
    .settings label { margin:0; font-weight:bold; }
    .settings #revoffers_site_id { width:100%; max-width:16em; }
    .settings .desc { font-size:.9em; color:#e06868; margin:0; }
    .settings .pre { display:inline-block; min-width:6em; margin-right:.5em; }
    .wrap > .debug { font-size:.9em; opacity:.8; margin:1em; float:right; }
</style>
<div class="wrap">
    <img class="img" src="<?php echo esc_url(plugins_url('/images/revoffers_dark.png', __FILE__)) ?>"/>
    <h1>RevOffers Advertiser Integration</h1>
    <p>
        Thank you for joining our growing network of advertisers! This plugin
        ensures that you get the most value out of our conversion network.
    </p>
    <p>
        If you have any questions, please
        <a href="https://www.revoffers.com/contact" target="_blank">contact us
        here</a>.
    </p>

    <form class="settings" method="post" action="options.php">
        <?php wp_nonce_field('update-options'); ?>
        <?php settings_fields('revoffers'); ?>
        <div class="field">
            <label for="revoffers_site_id">RevOffers Site ID</label>
            <div style="margin-bottom:8px">This identifies your web property when your website is available from multiple domains.</div>
            <span class="pre">Default value:</span> <strong style="display:inline-block;padding:4px;"><?php echo esc_attr($defaultHost); ?></strong><br/>
            <span class="pre">Your value:</span> <input type="text" id="revoffers_site_id" name="revoffers_site_id" value="<?php echo esc_attr(get_option('revoffers_site_id')); ?>" placeholder="<use default>"/>
            <p class="desc">DO NOT ENTER A VALUE HERE UNLESS DIRECTED BY A REVOFFERS REPRESENTATIVE!</p>
        </div>
        <?php submit_button(); ?>
    </form>

    <div class="debug">
        Debug: <a href="<?php echo admin_url('admin-ajax.php') ?>?action=revoffers_orders_debug&asText=1" download>Download order-debug file</a>
        | Alt: <a href="<?php echo admin_url('admin-ajax.php') ?>?action=revoffers_orders_debug&timeout=60" download>#2</a>
    </div>
</div>
