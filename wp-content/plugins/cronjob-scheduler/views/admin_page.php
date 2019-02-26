<div class="wrap">

    <h2>Cronjob Scheduler <small>by chrispage1</small></h2>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <?php echo $this->load_view('right_column'); ?>

            <div id="postbox-container-2" class="postbox-container">

                <!-- /* output any notifications */
                $this->output_notifications(); -->

                <?php /* begin actual page contents */ ?>
                <?php if(!$this->cron_configured()): ?>
                    <?php /* plugin misconfigured page */ ?>
                    <?php $this->load_view('plugin_misconfigured') ?>
                <?php else: ?>
                    <?php /* plugin settings page */ ?>
                    <?php $this->load_view('plugin_settings') ?>
                <?php endif; ?>
                <?php /* end actual page contents */ ?>
            </div>
        </div>
    </div>
</div>