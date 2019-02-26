<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>

    <h2><?php _e('WP SES Settings', 'wp-ses') ?></h2>

    <div class="wpses-container">

    <?php if ($wpses_options['from_email'] == '' || $wpses_options['credentials_ok'] != 1 || !wpses_sender_confirmed()) { ?>
    <div class="wpses-issues notice error">
        <ul>
            <?php
            if ($wpses_options['from_email'] == '') {
                echo '<li>';
                _e("Sender Email is not set.", 'wp-ses');
                echo '</li>';
            }

            if ($wpses_options['credentials_ok'] != 1) {
                echo '<li>';
                _e("Amazon API Keys are not valid, or you did not finalize your Amazon SES registration.", 'wp-ses');
                echo '</li>';
            }

            if (!wpses_sender_confirmed()) {
                echo '<li>';
                _e("Sender Email has not been confirmed yet.", 'wp-ses');
                echo '</li>';
            }
            ?>
        </ul>
        <?php if ($wpses_options['credentials_ok'] != 1 || !wpses_sender_confirmed()) { ?>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <?php wp_nonce_field('wpses'); ?>
            <input type="submit" name="refresh" class="button" value="<?php _e('Check Again', 'wp-ses') ?>" />
        </form>
        <?php } ?>
    </div>
    <?php } ?>

    <?php if ($wpses_options['active'] == 1 && (!defined('WP_SES_HIDE_STATS') or (false == WP_SES_HIDE_STATS))) { ?>
    <div class="wpses-header-block">
        <?php _e('You can check your sending limits and stats under Dashboard -> SES Stats', 'wp-ses'); ?>
    </div>
    <?php } ?>

    <div class="wpses-status wpses-header-block">

        <?php
        if ($wpses_options['active'] == 1) {
            $status = __("ON", 'wp-ses');
        }
        else {
            $status = __("OFF", 'wp-ses');
        }
        ?>

        <p>
            <?php _e("Sending emails through Amazon SES is currently", 'wp-ses'); ?>
            <strong><?php echo $status; ?></strong>
        </p>

        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <?php wp_nonce_field('wpses'); ?>
            <p class="wpses-submit">
            <?php if ($wpses_options['active'] == 1) { ?>
                <input type="submit" name="deactivate" class="button" value="<?php _e('Turn OFF', 'wp-ses') ?>" />
            </p>
            <p class="wpses-activation-desc"><?php _e('If you turn this off outgoing email will be delivered by the default WordPress method, but you\'ll still be able to test email delivery through SES using the tools below.', 'wp-ses') ?></p>
            <?php } else { ?>
                <input type="submit" name="activate" class="button" value="<?php _e('Turn ON', 'wp-ses') ?>" />
            </p>
            <p class="wpses-force-activation"><label><input  type="checkbox" name="force" value="1" /><?php _e("Force it ON even if there are issues detected", 'wp-ses'); ?></label></p>
            <p class="wpses-activation-desc wpses-force-activation-desc"><?php _e("Check this if you use IAM credentials, have validated sender emails for the region you're using, production email test is OK but you can't activate the plugin.", 'wp-ses' ); ?></p>
            <p class="wpses-activation-desc"><?php _e('Warning: Activate only if your account is in production mode.<br />Once activated, all outgoing emails will go through Amazon SES and will NOT be sent to any email while in sandbox mode.', 'wp-ses') ?></p>
            <?php } ?>
        </form>

    </div>

    <h3><?php _e('Sender Email', 'wp-ses') ?></h3>
    <?php _e('These settings replace the default sender email used by your site.', 'wp-ses') ?>
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <?php wp_nonce_field('wpses'); ?>
        <table class="form-table">
            <tr><th scope="row"><?php _e('Sender Email', 'wp-ses') ?></th>
                <td>
                    <?php if (!defined('WP_SES_FROM')) { ?>
                        <input type="text" name="from_email" value="<?php echo $wpses_options['from_email']; ?>" class="regular-text" /><p class="description"><?php _e('(Has to be a valid email)', 'wp-ses') ?></p>
                        <?php
                    } else {
                        echo WP_SES_FROM, ' ';
                        _e('(defined by your admin)', 'wp-ses');
                    }
                    ?>
                </td></tr>
            <tr><th scope="row"><?php _e('Name', 'wp-ses') ?></th>
                <td><input type="text" name="from_name" value="<?php echo $wpses_options['from_name']; ?>" class="regular-text" /></td></tr>
            <tr><th scope="row"><?php _e('Return Path', 'wp-ses') ?></th>
                <td>
                    <?php if (!defined('WP_SES_RETURNPATH')) { ?>
                        <input type="text" name="return_path" value="<?php echo $wpses_options['return_path']; ?>" class="regular-text" /><p class="description"><?php _e('You can specify a return email (not required).<br />Delivery Status notification messages will be sent to this address.', 'wp-ses') ?></p>
                        <?php
                    } else {
                        echo WP_SES_RETURNPATH, ' ';
                        _e('(defined by your admin)', 'wp-ses');
                    }
                    ?>
                </td></tr>
            <tr><th scope="row"><?php _e('Reply To', 'wp-ses') ?></th>
                <td>
                    <?php if (!defined('WP_SES_REPLYTO') or ('' == WP_SES_REPLYTO)) { ?>
                        <input type="text" name="reply_to" value="<?php echo $wpses_options['reply_to']; ?>" class="regular-text" /><p class="description"><?php _e('You can specify a reply To Email (not required).<br />Replies to your messages will be sent to this address.<br />Set to "headers" to extract Reply-to from email headers.', 'wp-ses') ?></p>
                        <?php
                    } else {
                        echo WP_SES_REPLYTO, ' ';
                        _e('(defined by your admin)', 'wp-ses');
                    }
                    ?>
                </td></tr>
        </table>

        <h3><?php _e("Amazon API Keys", 'wp-ses') ?></h3>
        <?php if (!WP_SES_RESTRICTED) { ?>
            <p><?php _e('Please insert here your API keys obtained from Amazon Web Services. It\'s best to use an IAM user. Make sure you give it at least the following permissions : ListIdentities, SendEmail, SendRawEmail, VerifyEmailIdentity, DeleteIdentity, Remove GetSendQuota, GetSendStatistics', 'wp-ses') ?></p>
            <table class="form-table">
                <tr><th scope="row"><?php _e('Access Key ID:', 'wp-ses') ?></th>
                    <td><input type="text" name="access_key" value="<?php echo $wpses_options['access_key']; ?>" class="regular-text" /></td></tr>
                <tr><th scope="row"><?php _e('Secret Access Key:', 'wp-ses') ?></th>
                    <td><input type="text" name="secret_key" value="<?php echo $wpses_options['secret_key']; ?>" class="regular-text" /></td></tr>

                <tr><th scope="row"><?php _e('Region', 'wp-ses') ?></th>
                    <td><select name="endpoint">
                            <option value="email.us-east-1.amazonaws.com" <?php
        if ('email.us-east-1.amazonaws.com' == $wpses_options['endpoint']) {
            echo 'selected';
        }
        ?>>US East (N. Virginia)</option>
                            <option value="email.us-west-2.amazonaws.com" <?php
        if ('email.us-west-2.amazonaws.com' == $wpses_options['endpoint']) {
            echo 'selected';
        }
            ?>>US West (Oregon)</option>
                            <option value="email.eu-west-1.amazonaws.com" <?php
                                if ('email.eu-west-1.amazonaws.com' == $wpses_options['endpoint']) {
                                    echo 'selected';
                                }
                                ?>>EU (Ireland)</option>
                        </select>
                        <p class="description"><?php _e('You\'ll need to validate sender emails for each region you want to use', 'wp-ses') ?></p>
                    </td></tr>
            </table>
<?php } else { // restricted access    ?>
    <?php _e('Amazon Web Services API info has already been filled in by your administrator.', 'wp-ses') ?>
<?php } ?>
        <input type="hidden" name="action" value="update" />
        <!-- input type="hidden" name="page_options" value="wpses_options" / -->
        <p class="submit" style="clear:both">
            <input type="submit" name="save" class="button button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
    <br />&nbsp;
    <?php if (!defined('WP_SES_HIDE_VERIFIED') or (false == WP_SES_HIDE_VERIFIED)) { ?>
        <h3><?php _e("Confirmed Senders", 'wp-ses') ?></h3>
        <?php _e('Only confirmed senders are able to send an email via SES', 'wp-ses') ?><br />
    <?php _e('The following senders are known:', 'wp-ses') ?>
        <br />
                <?php
                //print_r($autorized);
                //$senders
                ?>
        <div style="width:70%">
            <table class="form-table">
                <tr style="background-color:#ccc; font-weight:bold;"><td><?php _e('Email', 'wp-ses') ?></td><td><?php _e('Request Id', 'wp-ses') ?></td><td><?php _e('Confirmed', 'wp-ses') ?></td><td><?php _e('Action', 'wp-ses') ?></td></tr>
                <?php
                $i = 0;
                foreach ($senders as $email => $props) {
                    if ($i % 2 == 0) {
                        $color = ' style="background-color:#ddd"';
                    } else {
                        $color = '';
                    }
                    echo("<tr $color>");
                    echo("<td>$email</td>");
                    echo("<td>");
                    print_r($props[0]);
                    echo("</td>");
                    if ($props[1]) {
                        $valide = __('Yes', 'wp-ses');
                    } else {
                        $valide = __('No', 'wp-ses');
                    }
                    echo("<td>" . $valide . "</td>");
                    echo("<td>");
                    if ($props[1] and !WP_SES_RESTRICTED) {
                        // remove this email
                        ?>
                        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <?php wp_nonce_field('wpses'); ?><input type="hidden" name="email" value="<?php echo $email ?>">
                            <!-- div class="submit" -->
                            <input type="submit" name="removeemail" value="<?php _e('Remove', 'wp-ses') ?>" onclick="return confirm('<?php _e('Are you sure you want to remove this confirmed sender?', 'wp-ses') ?>')"/>
                            <!-- /div -->
                        </form>
                        <?php
                    }
                    echo(" </td>");

                    echo("</tr>");
                    $i++;
                }
                ?>
            </table>
        </div>
        <?php } ?>
<?php if (!WP_SES_RESTRICTED) { ?>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <?php wp_nonce_field('wpses'); ?>
            <!-- todo : que si email defini, que si pas dans la liste  -->
            <br />
    <?php _e('Add the following email: ', 'wp-ses') ?><?php echo $wpses_options['from_email']; ?><?php _e(' to senders.', 'wp-ses') ?>

            <p class="wpses-submit">
                <input type="submit" name="addemail" class="button" value="<?php _e('Add This Email', 'wp-ses') ?>" />
            </p>
        </form>
        <br />&nbsp;

        <h3><?php _e('Test Email', 'wp-ses') ?></h3>
    <?php _e('Click on this button to send a test email (via Amazon SES) to the sender email.', 'wp-ses') ?>
        <br />
        <!-- todo: que si email expediteur valid� -->
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <?php wp_nonce_field('wpses'); ?>
            <p class="wpses-submit">
                <input type="submit" name="testemail" class="button" value="<?php _e("Send Test Email", 'wp-ses') ?>" />
            </p>
        </form>
        <br />&nbsp;
        <h3><?php _e('Production Mode Test', 'wp-ses') ?></h3>
    <?php _e('Once Amazon puts your account into production mode, you can begin to send mail to any address<br />Use the form below to test this before fully activating the plugin on your site.', 'wp-ses') ?>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <?php wp_nonce_field('wpses'); ?>
            <table class="form-table" >
                <tr><th scope="row"><?php _e('Send Email To ', 'wp-ses') ?></th>
                    <td><input type="text" name="prod_email_to" value="" class="regular-text" /></td></tr>
                <tr><th scope="row"><?php _e('Subject', 'wp-ses') ?></th>
                    <td><input type="text" name="prod_email_subject" value="" class="regular-text" /></td></tr>
                <tr><th scope="row"><?php _e('Message', 'wp-ses') ?></th>
                    <td><textarea cols="80" rows="5" name="prod_email_content"></textarea></td></tr>
            </table>
            <p class="wpses-submit">
                <input type="submit" name="prodemail" class="button" value="<?php _e("Send Full Test Email", 'wp-ses') ?>" />
            </p>
        </form>

        <?php } ?>
    <br />&nbsp;
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <h3><?php _e('Logs', 'wp-ses') ?></h3>
        <?php wp_nonce_field('wpses'); ?>
        <?php if ($wpses_options['log']) { ?>
        <p><?php _e('Logging is enabled', 'wp-ses') ?></p>
            <input type="submit" name="deactivatelogs" class="button" value="<?php _e('Disable Logging and Clear Logs', 'wp-ses') ?>" />
&nbsp;     <input type="submit" name="viewlogs" class="button" value="<?php _e('View Logs', 'wp-ses') ?>" />
        <?php } else { ?>
        <p><?php _e('Logging is disabled', 'wp-ses') ?></p>
            <input type="submit" name="activatelogs" class="button" value="<?php _e('Enable Logging', 'wp-ses') ?>" />
        <?php } ?>
    </form>
    <div style="width:80%">
<?php
if (function_exists('sd_rss_widget')) {
    //	sd_rss_widget(array('num'=>3));
}
?>
    </div>
    <?php include 'sidebar.tmpl.php'; ?>
    </div>
</div>