<div id="postbox-container-1" class="postbox-container">
    <div id="side-sortables" class="meta-box-sortables ui-sortable">
        <div class="postbox">
            <h3 class="hndle">Schedules</h3>

            <div class="inside">
                <form method="POST" action="">
                    <input type="hidden" name="scheduler_nonce" value="<?php echo wp_create_nonce() ?>" />

                    <table class="widefat fixed">
                        <thead>
                            <tr>
                                <th class="column"><strong>Name</strong></th>
                                <th class="column"><strong>Minutes</strong></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach($this->get_schedules() as $key => $schedule): ?>
                                <tr>
                                    <td class="column"><?php echo esc_html($schedule['display']) ?></td>
                                    <td class="column">
                                        <?php echo number_format($schedule['interval']/60) ?>
                                        <?php if(array_key_exists($key, $this->_schedules)): ?>
                                            <input type="submit" name="deleteschedule[<?php echo esc_attr($key) ?>]" class="button button-primary button-red button-small"
                                            style="float: right" value="Delete" onclick="return confirm(\'Are you sure you want to delete this schedule?
                                            This will stop tasks that use this schedule from working next time they run.\');" />
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <tr>
                                <td class="column">
                                    <input type="text" name="schedule[display]" class="form-input-tip" size="10" placeholder="Five Minutes" autocomplete="off" />
                                </td>
                                <td class="column">
                                    <input type="text" name="schedule[interval]" class="form-input-tip" size="9" placeholder="5" autocomplete="off" />
                                </td>
                            </tr>
                            <tr>
                                <td class="column" colspan="2" style="text-align: right">
                                    <input type="submit" name="save" class="button button-primary button-large" value="Add Schedule" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <div class="postbox">
            <h3 class="hndle">Plugin Details</h3>

            <div class="inside">
                <p>
                    This plugin was designed and built by <a href="https://profiles.wordpress.org/chrispage1/" target="_blank">chrispage1</a>. It is designed to
                    make easy work of creating and managing custom WordPress cron jobs.
                </p>

                <p>
                    To make sure this plugin runs correctly, you need to create a unix cronjob
                    that runs <b>every minute</b>. The recommended settings for your
                    installation is:
                </p>

                <input type="text" class="form-input-tip" readonly="readonly" value="<?php echo $this->get_cron_string() ?>" style="font-family: monospace; width: 100%" onclick="this.focus(); this.select()" />


                <p>
                    You may have to change the above depending on your setup. If this plugin has
                    been helpful for you, then please donate to keep our projects running!
                </p>

                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="text-align: center">
                    <input type="hidden" name="cmd" value="_donations">
                    <input type="hidden" name="business" value="6FVZN7BBHGR2S">
                    <input type="hidden" name="lc" value="GB">
                    <input type="hidden" name="item_name" value="WordPress Plugins - Cronjob Scheduler">
                    <input type="hidden" name="no_note" value="0">
                    <input type="hidden" name="cn" value="Add special instructions to the seller:">
                    <input type="hidden" name="no_shipping" value="1">
                    <input type="hidden" name="currency_code" value="GBP">
                    <input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHosted">
                    <input type="hidden" name="return" value="' . esc_attr(admin_url('/options-general.php?page=cronjob_scheduler&amp;d=true')) . '">
                    <input type="hidden" name="cancel_return" value="' . esc_attr(admin_url('/options-general.php?page=cronjob_scheduler&amp;d=false')) .'">
                    <input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
                </form>
            </div>
        </div>
    </div>
</div>