<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2><?php _e('Amazon SES Stats', 'wp-ses') ?></h2>


    <h3><?php _e('Sending limits', 'wp-ses') ?></h3>

    <?php
    /*
      $quota=Array
      (
      [Max24HourSend] => 1000.0
      [MaxSendRate] => 1.0
      [SentLast24Hours] => 0.0
      [RequestId] => fb44c891-2de3-11e0-a311-6b03de78268d
      // SendRemaining
      // SendUsage
      )
     */
    ?>
    <table>
        <tr><td><?php _e('Max24HourSend', 'wp-ses') ?>&nbsp;</td><td><?php echo $quota['Max24HourSend'] ?></td><td>&nbsp;<i><?php _e('Max email quota for 24 hours period', 'wp-ses') ?></i></td></tr>
        <tr><td><?php _e('MaxSendRate', 'wp-ses') ?>&nbsp;</td><td><?php echo $quota['MaxSendRate'] ?> /s</td><td>&nbsp;<i><?php _e('Max email sending rate par second', 'wp-ses') ?></i></td></tr>
        <tr><td><?php _e('SentLast24Hours', 'wp-ses') ?>&nbsp;</td><td><?php echo $quota['SentLast24Hours'] ?></td><td>&nbsp;<i><?php _e('Emails sent for the last 24 hours', 'wp-ses') ?></i></td></tr>
        <tr><td><?php _e('SendRemaining', 'wp-ses') ?>&nbsp;</td><td><?php echo $quota['SendRemaining'] ?></td><td>&nbsp;<i><?php _e('Email sending quota remaining', 'wp-ses') ?></i></td></tr>
        <tr><td><?php _e('SendUsage', 'wp-ses') ?>&nbsp;</td><td><?php echo $quota['SendUsage'] ?> %</td><td>&nbsp;<i><?php _e('Usage percentage per 24h', 'wp-ses') ?></i></td></tr>
    </table>

    <br />&nbsp;
    <h3><?php _e('Sending Stats', 'wp-ses') ?></h3>
    <?php _e('Last 15 days of email statistics', 'wp-ses') ?>
    <br />
    <?php _e('Each line contains statistics for a 15 minutes period of sending activity. <br />Periods without any activity are not shown', 'wp-ses') ?>
    <br />&nbsp;
    <?php /*
      $stats=Array
      (
      [SendDataPoints] => Array
      (
      [0] => Array
      (
      [Bounces] => 0
      [Complaints] => 0
      [DeliveryAttempts] => 2
      [Rejects] => 0
      [Timestamp] => 2011-01-28T19:46:00Z
      )

      [1] => Array
      (
      [Bounces] => 0
      [Complaints] => 0
      [DeliveryAttempts] => 1
      [Rejects] => 0
      [Timestamp] => 2011-01-28T18:31:00Z
      )

      )

      [RequestId] => fbb69dc5-2de3-11e0-be28-bdf6df22c846
      )
     */
    ?>
    <table cellpadding="2">
        <tr style="background-color:#ccc">
            <td><?php _e('Timestamp', 'wp-ses') ?>&nbsp;</td>
            <td><?php _e('DeliveryAttempts', 'wp-ses') ?>&nbsp;</td>
            <td><?php _e('Bounces', 'wp-ses') ?>&nbsp;</td>
            <td><?php _e('Complaints', 'wp-ses') ?>&nbsp;</td>
            <td><?php _e('Rejects', 'wp-ses') ?>&nbsp;</td>
            <td><?php _e('Total Ok', 'wp-ses') ?>&nbsp;</td>
            <td><?php _e('Total Errors', 'wp-ses') ?>&nbsp;</td>
        </tr>
        <?php
        $i = 1;
        if (is_array($stats['SendDataPoints'])) {
            foreach ($stats['SendDataPoints'] as $point) {
                if ($i % 2 == 0) {
                    $color = ' style="background-color:#ddd"';
                } else {
                    $color = '';
                }
                $i++;
                ?><tr <?php echo $color; ?>>
                    <td><?php echo $point['Timestamp']; ?>&nbsp;</td>
                    <td><?php echo $point['DeliveryAttempts']; ?></td>
                    <td><?php echo $point['Bounces']; ?></td>
                    <td><?php echo $point['Complaints']; ?></td>
                    <td><?php echo $point['Rejects']; ?></td>
                    <td><?php echo $point['DeliveryAttempts'] - $point['Bounces'] - $point['Complaints'] - $point['Rejects']; ?></td>
                    <td><?php echo $point['Bounces'] + $point['Complaints'] + $point['Rejects']; ?></td>
                </tr>
                <?php
            }
        } else {
            // no data
        }
        ?>
    </table>
    <br />&nbsp;

    <div style="width:80%">
        <?php
        if (function_exists('sd_rss_widget')) {
            //	sd_rss_widget(array('num'=>3));
        }
        ?>
    </div>
</div>