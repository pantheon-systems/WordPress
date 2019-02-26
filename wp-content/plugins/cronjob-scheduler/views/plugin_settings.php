<?php if(!defined('ABSPATH')) exit; ?>
<p>
    Please use the section below to configure your recurring cron jobs. If you want
    to change an existing cron, you should delete the existing schedule and create a
    new one. Actions prefixed with <span style="font-family: Monospace">wp_</span> are
    system reserved and cannot be created or deleted. Due to the way WordPress handles
    cron jobs, it may take up to 2 minutes for a 1 minute cron to be executed.
</p>

    <form method="POST" action="" class="responsive-table">
        <input type="hidden" name="scheduler_nonce" value="<?php echo wp_create_nonce() ?>" />

        <table class="widefat fixed">
            <thead>
                <tr>
                    <th class="column" style="width: 180px"><strong>Cron Action</strong></th>
                    <th class="column" style="width: 80px"><strong>Action Exists</strong></th>
                    <th class="column" style="width: 100px"><strong>Run Schedule</strong></th>
                    <th class="column" style="width: 130px"><strong>Next Run</strong></th>
                    <th class="column" style="width: 80px"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($this->parse_crons() as $attributes): ?>

                    <?php $date_format = get_option('date_format') . ' ' . get_option('time_format'); ?>

                    <tr>
                        <td class="column" style="white-space: nowrap">
                            <input type="text" class="form-input-tip"
                            readonly="readonly" value="<?php echo esc_attr($attributes['hook']) ?>" style="font-family: Monospace; width: 100%"
                            onclick="this.focus(); this.select()" /></pre>
                        </td>
                        <td class="column">
                            <?php echo (has_action($attributes['hook']) ? 'Yes' : '<strong style="color: #FF0000">No</strong>') ?>
                        </td>
                        <td class="column">
                            <?php echo esc_html($attributes['display_name']) ?><br /><small>
                            <?php echo number_format($attributes['interval']/60) ?> minutes</small>
                        </td>
                        <td class="column">
                            <?php echo date($date_format, $attributes['last_run']) ?>
                        </td>
                        <td class="column">
                            <input type="submit" name="trigger[<?php echo esc_attr($attributes['uniqid']) ?>]" value="Run"
                            class="button button-primary button-small" onclick="return confirm(\'Are you sure you want to run this now?\')" />

                            <input type="submit" name="delete[<?php echo esc_attr($attributes['uniqid']) ?>]" value="Delete"
                            class="button button-primary button-red button-small" onclick="return confirm(\'Are you sure you want to delete this cron?\');" />
                        </td>
                    </tr>

                <?php unset($date_format); endforeach; ?>


                <?php $schedule_function = (isset($_POST['cron']['method']) ? esc_attr($_POST['cron']['method']) : null); ?>
                <?php $schedule_interval = (isset($_POST['cron']['schedule']) ? esc_attr($_POST['cron']['schedule']) : null); ?>

                <tr>
                    <td class="column"><input type="text" class="form-input-tip" style="font-family: Monospace; width: 100%"
                    name="cron[method]" value="<?php echo esc_attr($schedule_function) ?>" autocomplete="off" /></td>

                    <td class="column"></td>
                    <td class="column">
                        <select name="cron[schedule]">
                            <?php foreach($this->get_schedules() as $key => $schedule): ?>
                                <?php printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr($key),
                                    ($schedule_interval == $key ? ' selected="selected"' : null),
                                    esc_html($schedule['display'])
                                ) ?>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td class="column" colspan="2">
                        <input type="submit" value="Create Cronjob" class="button button-primary button-large" />
                    </td>
                </tr>


                <?php unset($schedule_function, $schedule_interval); ?>

            </tbody>
        </table>
    </form>

    <h2>Action Template</h2>
    <p>
        You can use the template below to create a new cron action.  In the example, the Cron Action
        would  be called <span style="font-family: monospace">my_cronjob_action</span>
    </p>

    <blockquote style="font-family: monospace">
        <span style="color: #000080; font-weight: bold">function</span> my_cronjob_action () {<br />
        <span style="color: #008800; font-style: italic">&nbsp;&nbsp;&nbsp;&nbsp;// code to execute on cron run</span><br />
        } add_action(<span style="color: #0000FF">&#39;my_cronjob_action&#39;</span>,
        <span style="color: #0000FF">&#39;my_cronjob_action&#39;</span>);
    </blockquote>

    <p>
        You can create custom actions by editing this plugins <span style="font-family: monospace">cronjobs.php</span> file.
        This file requires you to have an understanding of PHP,
        <a href="<?php echo call_user_func((defined('MULTISITE') ? 'network_' : null) . 'admin_url', 'plugin-editor.php?file=' . basename($options['plugin_dir']) . '%2Fcronjobs.php') ?>">edit this file</a>.
    </p>