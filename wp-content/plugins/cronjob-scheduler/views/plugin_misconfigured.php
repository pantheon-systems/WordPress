<p>
  For Cronjob Scheduler to run reliably, you need to disable the default WordPress
  cron job schedules and setup a unix cronjob. Please add the following to the end of your
  <span style="font-family: monospace">
    <?php echo esc_html($this->get_wp_config_path() ? $this->get_wp_config_path() : 'wp-config.php') ?>
  </span> file.</p>

  <blockquote><pre>define(&#39;DISABLE_WP_CRON&#39;, true);</pre></blockquote>

  <p>
    Once this has been added, this page will no longer be displayed. It is important
    that you also setup a cron job to run every minute, the recommended setting for your
    installation is:
  </p>

  <blockquote><pre style="word-wrap: break-word"><?php echo $this->get_cron_string() ?></pre></blockquote>