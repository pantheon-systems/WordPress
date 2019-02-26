<style>
    .video_content
    {
        padding: 10px;
        border: 1px solid lightgrey;
        width: 560px;
        box-shadow: 10px 10px 5px #888888;
    }
    .postbox-custom
    {
        padding-bottom: 20px;
    }
    .left_mention
    {
        float: left;
        margin: 20px;
    }
    .premium-li
    {
        padding: 5px;
    }
    .premium-ul
    {
        list-style: none;
        font-size: 15px;
    }
    .premium-ol
    {
        list-style: none;
        padding: 5px 15px;
        font-size: 14px;
    }
</style>
<div class="wrap postbox main_content">
    <center>
        <h2><?php echo __( 'Welcome to Product Import Export', 'wf_csv_import_export');?></h2>
        <p>
            <?php echo __( 'WooCommerce Product Import Export can be much easier than you expect', 'wf_csv_import_export');?>
            <br>
            <?php echo __( 'Go through our  Tutorial video to get an overall idea on how it works.', 'wf_csv_import_export');?>
        </p>
    </center>
</div>
<div class="wrap postbox postbox-custom">
    <center>
        <h2><?php echo __( 'Product Import Export', 'wf_csv_import_export');?></h2>
        <hr>
        <div style="margin-bottom:20px !important;">
            <div class="video_content">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/L-01qI1EZWE" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
        <hr>
        <div>
            <center>
                <div style="margin: 5px;;margin-top: 15px !important">
                    <a class="button button-primary" href="<?php echo admin_url("admin.php?page=wf_woocommerce_csv_im_ex"); ?>" target="_blank"><?php echo __( 'Product Import Export', 'wf_csv_import_export');?></a>
                </div>
            </center>
        </div>
    </center>
</div>