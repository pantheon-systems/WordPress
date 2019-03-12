<?php

/*
  Plugin Name: WP SES
  Version: 0.8.1
  Plugin URI: https://wordpress.org/plugins/wp-ses/
  Description: Uses Amazon SES for sending all site email
  Author: Delicious Brains Inc
  Author URI: https://deliciousbrains.com
 * Text Domain: wp-ses
 * Domain Path: /
 */

define('WPSES_VERSION', 0.721);

// TODO
// stats cache (beware of directory)
// logs of mails sent (inc details ?)
// traiter les erreurs (stocker contenu) pour les re-tenter plus tard ?
// Mailqueue
// limits (check once per hour (or  faster) and stop if near limit)
// blacklist, mail delivery handling
// dashboard integration (main stats without extra page)
// Add error display for test messages
// add attachments (contact form 7) : see https://github.com/daniel-zahariev/php-aws-ses
// retrieve the security credentials from the instance metadata service for EC2 : cf Christian at tellnes

if (defined('WP_SES_ACCESS_KEY') and defined('WP_SES_SECRET_KEY')) {
    define('WP_SES_RESTRICTED', true);
} else {
    define('WP_SES_RESTRICTED', false);
}

if (is_admin()) {
    // TODO : Ask before activate
    // include_once(WP_PLUGIN_DIR.'/wp-ses/sdrssw.php');
    add_action('init', 'wpses_init');
    add_action('admin_menu', 'wpses_admin_menu');
    register_activation_hook(__FILE__, 'wpses_install');
    register_deactivation_hook(__FILE__, 'wpses_uninstall');
}

//require_once (WP_PLUGIN_DIR . '/wp-ses/ses.class.0.8.6.php');
// May be in wpmu folder, thanks to @positonic
require_once plugin_dir_path( __FILE__ ) . 'ses.class.0.8.6.php';

function wpses_init() {
    load_plugin_textdomain('wp-ses', false, basename(dirname(__FILE__)));
    wpses_admin_warnings();
}

add_filter('wp_mail_from', 'wpses_from', 1);
add_filter('wp_mail_from_name', 'wpses_from_name', 1);

function wpses_php_warning() {
    if ( version_compare( PHP_VERSION, '5.5', '<' ) ) {
        $capability = is_multisite() ? 'manage_network_options' : 'manage_options';

        if ( ! current_user_can( $capability ) ) {
            return;
        }

        $user_id = get_current_user_id();
        
        if ( get_user_meta( $user_id, 'wpses_dismissed_php_warning_notice' ) ) {
            return;
        }

        $message = sprintf(
            __( '<strong>WP SES Warning</strong> &mdash; This site is running on PHP %1$s. In the near future we will release a new version of WP SES that will require PHP 5.5 or later. It will not work on PHP %1$s. We recommend you contact the company who hosts this web site and have them move it to a server running PHP 5.5 or later. We currently recommend PHP 7.2.'),
            PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION
        );

        ?>
        <div class="notice notice-warning wpses-notice is-dismissible">
            <p><?php echo $message; ?></p>
        </div>
        <?php
    }
}
add_action( 'admin_notices', 'wpses_php_warning' );

function wpses_dismiss_php_notice() {
    if ( ! is_admin() || ! wp_verify_nonce( sanitize_key( $_POST['_nonce'] ), sanitize_key( $_POST['action'] ) ) ) {
        wp_die( __( 'Cheatin&#8217; eh?', 'wp-ses' ) );
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.', 'wp-ses' ) );
    }

    $user_id = get_current_user_id();

    add_user_meta( $user_id, 'wpses_dismissed_php_warning_notice', 1 );

    wp_send_json( array( 'success' => 1 ) );
}
add_action( 'wp_ajax_wp-ses-dismiss-notice', 'wpses_dismiss_php_notice' );

function wpses_enqueue_admin_notice() {
    $src = wpses_get_asset_url( 'js/script.js' );
    wp_enqueue_script( 'wp-ses-script', $src, array( 'jquery' ), false, true );


    wp_localize_script( 'wp-ses-script', 'wpses', array(
        'dismiss_notice_nonce' => wp_create_nonce( 'wp-ses-dismiss-notice' ),
        'dismiss_notice_error' => __( 'Error dismissing notice.', 'wp-ses')
    ) );
}
add_action( 'admin_enqueue_scripts', 'wpses_enqueue_admin_notice' );

function wpses_install() {
    global $wpdb, $wpses_options;
    if (!get_option('wpses_options')) {
        add_option('wpses_options', array(
            'from_email' => '',
            'return_path' => '',
            'from_name' => 'WordPress',
            'access_key' => '',
            'secret_key' => '',
            'endpoint' => 'email.us-east-1.amazonaws.com',
            'credentials_ok' => 0,
            'sender_ok' => 0,
            'last_ses_check' => 0, // timestamp of last quota check
            'force' => 0,
            'log' => 0,
            'active' => 1, // reset to 0 if not pluggable or config change.
            'version' => '0' // Version of the db
                // TODO: garder liste des ids des demandes associ�es � chaque email.
                // afficher : email, id demande , valid� ?
        ));
        wpses_getoptions();
        //$wpses_options = get_option('wpses_options');
        @ mkdir(WP_PLUGIN_DIR . '/wp-ses/log/');
    }
}

function wpses_options() {
    global $wpdb, $wpses_options;
    global $current_user;
    //get_currentuserinfo();
    wp_get_current_user(); // Thanks @jmichaelward
    if (!in_array('administrator', $current_user->roles)) {
        //die('Not admin');
    }
    $authorized = '';
    if (($wpses_options['access_key'] != '') and ( $wpses_options['secret_key'] != '')) {
        $authorized = wpses_getverified();
    }
    $senders = (array) get_option('wpses_senders');
    // ajouter dans senders les verified absents
    $updated = false;
    if ('' != $authorized) {
        if (!is_array($authorized)) {
            $authorized = array($authorized);
        }
        foreach ($authorized as $email) {
            // if (false !==strpos($email, '@')) {
            // identity if full email
            if (!array_key_exists($email, $senders)) {
                $senders[$email] = array(
                    -1, // Got an Id, but not request id
                    TRUE
                );
                $updated = true;
            } else {
                if (!$senders[$email][1]) {
                    // activate the new emails
                    $senders[$email][1] = true;
                    $updated = true;
                }
            }
            /* } else {
              // identity is domain only
              // Must check with all senders
              // No request id here, just tag as valid
              foreach($senders as $email=>$detail) {


              }
              } */
        }
        // remove old senders
        foreach ($senders as $email => $info) {
            if ($info[1] and ! in_array($email, $authorized)) {
                $senders[$email][1] = false;
                // echo 'remove '.$email.' ';
                $updated = true;
            }
        }
    }

    if ($updated) {
        update_option('wpses_senders', $senders);
    }

    $wpses_options['sender_ok'] = 0;

    if (($wpses_options['from_email'] != '')) {
        if ($senders[$wpses_options['from_email']][1] === TRUE) { //
            // email exp enregistré non vide et listé, on peut donc supposer que credentials ok et exp ok.
            if ($wpses_options['credentials_ok'] == 0) {
                $wpses_options['credentials_ok'] = 1;
                wpses_log('Credentials ok');
                update_option('wpses_options', $wpses_options);
            }
            if ($wpses_options['sender_ok'] == 0) {
                $wpses_options['sender_ok'] = 1;
                wpses_log('Sender Ok');
                update_option('wpses_options', $wpses_options);
            }
        } else {
            //if ($senders[$wpses_options['from_email']][1] !== TRUE) { //
            //$wpses_options['sender_ok'] = 0;
            //wpses_log('Sender not OK');
            //update_option('wpses_options', $wpses_options);
        }
        //if (!isset($senders[$wpses_options['from_email']])) {
        if (0 == $wpses_options['sender_ok']) {
            // email is not known, but domain could be listed
            list($user, $domain) = explode('@', $wpses_options['from_email']);
            if ($senders[$domain][1] === TRUE) {
                // domain is validated
                //$senders[$wpses_options['from_email']] = array(-1, true);
                $wpses_options['sender_ok'] = 1;
                wpses_log('Sender domain ok');
                $wpses_options['credentials_ok'] = 1;
                update_option('wpses_options', $wpses_options);
            } else {
                //$senders[$wpses_options['from_email']] = array(-1, false);
            }
        }
    }

    if ((($wpses_options['sender_ok'] != 1) and ( $wpses_options['force'] != 1)) or ( $wpses_options['credentials_ok'] != 1)) {
        $wpses_options['active'] = 0;
        wpses_log('Deactivate sender_ok=' . $wpses_options['sender_ok'] . ' Force=' . $wpses_options['force'] . ' credentials_ok=' . $wpses_options['credentials_ok']);
        update_option('wpses_options', $wpses_options);
    }

    if (!empty($_POST['activate'])) {
        $wpses_options['force'] = 0;
        if (($wpses_options['sender_ok'] == 1) and ( $wpses_options['credentials_ok'] == 1)) {
            $wpses_options['active'] = 1;
            wpses_log('Normal activation');
            update_option('wpses_options', $wpses_options);
            echo '<div id="message" class="updated fade">
			<p>' . __('Sending emails through Amazon SES has been turned <strong>ON</strong>', 'wp-ses') . '</p>
			</div>' . "\n";
        }
        else {
            echo '<div id="message" class="error notice">
            <p>' . __('Could not turn ON sending emails through Amazon SES. Please resolve the issues below.', 'wp-ses') . '</p>
            </div>' . "\n";
        }
        if (isset($_POST['force']) and 1 == $_POST['force']) {
            // bad hack to force plugin activation with IAM credentials
            $wpses_options['sender_ok'] = 1;
            $wpses_options['credentials_ok'] = 1;
            $wpses_options['active'] = 1;
            $wpses_options['force'] = 1;
            wpses_log('Forced activation');
            update_option('wpses_options', $wpses_options);
            echo '<div id="message" class="updated fade">
            <p>' . __('Sending emails through Amazon SES has been forced <strong>ON</strong>', 'wp-ses') . '</p>
            </div>' . "\n";
        }
    }
    if (!empty($_POST['deactivate'])) {
        $wpses_options['active'] = 0;
        wpses_log('Manual deactivation');
        update_option('wpses_options', $wpses_options);
        echo '<div id="message" class="updated fade">
			<p>' . __('Sending emails through Amazon SES has been turned <strong>OFF</strong>', 'wp-ses') . '</p>
			</div>' . "\n";
    }
    if (!empty($_POST['activatelogs'])) {

        @ mkdir(WP_PLUGIN_DIR . '/wp-ses/log/');
        @ touch(WP_PLUGIN_DIR . '/wp-ses/log/wpses.log');
        if (!file_exists(WP_PLUGIN_DIR . '/wp-ses/log/wpses.log')) {
            echo '<div id="message" class="updated">
	    <p>' . __('Unable to create dir ', 'wp-ses') . WP_PLUGIN_DIR . '/wp-ses/log/' . __(' Please create it and give WP proper rights ', 'wp-ses') . '</p>
            </div>' . "\n";
        } else {
            @ unlink(WP_PLUGIN_DIR . '/wp-ses/log/wpses.log');
            echo '<div id="message" class="updated fade">
			<p>' . __('Logging enabled', 'wp-ses') . '</p>
			</div>' . "\n";
            $wpses_options['log'] = 1;
            update_option('wpses_options', $wpses_options);
            wpses_log('Start Logging');
        }
    }
    if (!empty($_POST['deactivatelogs'])) {
        $wpses_options['log'] = 0;
        update_option('wpses_options', $wpses_options);
        @ unlink(WP_PLUGIN_DIR . '/wp-ses/log/wpses.log');
        echo '<div id="message" class="updated fade">
	<p>' . __('Logging disabled and logs cleared', 'wp-ses') . '</p>
	</div>' . "\n";
    }
    if (!empty($_POST['viewlogs'])) {
        if (file_exists(WP_PLUGIN_DIR . '/wp-ses/log/wpses.log')) {
            echo nl2br(file_get_contents(WP_PLUGIN_DIR . '/wp-ses/log/wpses.log'));
            die();
        } else {
            echo '<div id="message" class="updated fade">
	<p>' . __('No log file', 'wp-ses') . '</p>
	</div>' . "\n";
        }
    }
    if (!empty($_POST['save'])) {
        //check_admin_referer();
        //$wpses_options['active'] = trim($_POST['active']);
        if ($wpses_options['from_email'] != trim($_POST['from_email'])) {
            wpses_log('From Email changed, reset state');
            $wpses_options['sender_ok'] = 0;
            $wpses_options['active'] = 0;
        }
        if (!defined('WP_SES_FROM')) {
            $wpses_options['from_email'] = trim($_POST['from_email']);
        }
        if (!defined('WP_SES_RETURNPATH')) {
            $wpses_options['return_path'] = trim($_POST['return_path']);
        }
        if ($wpses_options['return_path'] == '') {
            $wpses_options['return_path'] = $wpses_options['from_email'];
        }
        if (!defined('WP_SES_REPLYTO')) {
            $wpses_options['reply_to'] = trim($_POST['reply_to']);
            if ($wpses_options['reply_to'] == '') {
                $wpses_options['reply_to'] = $wpses_options['from_email'];
            }
        }
        $wpses_options['from_name'] = trim($_POST['from_name']); //
        $wpses_options['endpoint'] = trim($_POST['endpoint']); //
        // TODO si mail diff�re, relancer proc�dure check => resetter sender_ok si besoin

        if (($wpses_options['access_key'] != trim($_POST['access_key'])) or ( $wpses_options['secret_key'] != trim($_POST['secret_key']))) {
            wpses_log('API Keys changed, reset state and deactivate');
            $wpses_options['credentials_ok'] = 0;
            $wpses_options['sender_ok'] = 0;
            $wpses_options['active'] = 0;
        }
        if (!WP_SES_RESTRICTED) {
            $wpses_options['access_key'] = trim($_POST['access_key']); //
            $wpses_options['secret_key'] = trim($_POST['secret_key']); //
        }
        // TODO si credentials different, resetter credentials_ok

        update_option('wpses_options', $wpses_options);
        echo '<div id="message" class="updated fade"><p>' . __('Settings updated', 'wp-ses') . '</p></div>' . "\n";
    }
    wpses_getoptions();
    //$wpses_options = get_option('wpses_options');
    // validation cle amazon
    if (!WP_SES_RESTRICTED) { // server side check.
        // validation email envoi
        if (!empty($_POST['addemail'])) {
            wpses_verify_sender_step1($wpses_options['from_email']);
        }
        // remove verified email
        if (!empty($_POST['removeemail'])) {
            wpses_remove_sender($_POST['email']);
        }
        // envoi mail test
        if (!empty($_POST['testemail'])) {
            wpses_log('Test email request');
            wpses_test_email($wpses_options['from_email']);
        }
        // envoi mail test prod
        if (!empty($_POST['prodemail'])) {
            wpses_log('Prod email request');
            wpses_prod_email($_POST['prod_email_to'], $_POST['prod_email_subject'], $_POST['prod_email_content']);
        }
    }
    include ('admin.tmpl.php');
}

// TODO
function wpses_uninstall() {
    // delete_option('wpses_options');
    // Do not delete, else we loose the version number
    // TODO: add an uninstall link ? Not a big deal since we added very little overhead
}

function wpses_log($message) {
    global $wpses_options;
    $message = time() . "\t" . $message . "\r\n";
    if ($wpses_options['log']) {
        error_log($message, 3, WP_PLUGIN_DIR . '/wp-ses/log/wpses.log');
    }
}

function wpses_admin_warnings() {
    global $wpses_options;
    if (!function_exists('curl_version')) {

        function wpses_curl_warning() {
            global $wpses_options;
            echo "<div id='wpses-curl-warning' class='updated fade'><p><strong>" . __("WP SES - CURL extension not available. SES Won't work without Curl. Ask your host.", 'wp-ses') . "</strong></p></div>";
        }

        add_action('admin_notices', 'wpses_curl_warning');
        return;
    }
    $active = $wpses_options['active'];
    if ($active <= 0 && (!isset($_GET['page']) || 'wp-ses/wp-ses.php' !== $_GET['page'])) {

        function wpses_warning() {
            global $wpses_options;
            echo "<div id='wpses-warning' class='updated fade'><p>" . __("<strong>WP SES</strong> &mdash; Some configuration is required before your site's emails will be sent through Amazon SES. ", 'wp-ses') .
            '<a href="options-general.php?page=wp-ses/wp-ses.php">' . __("Settings &rarr; WP SES", 'wp-ses') . '</a>' . "</p></div>";
        }

        add_action('admin_notices', 'wpses_warning');
        return;
    }
}

function wpses_admin_menu() {
    $hook_suffix = add_options_page('wpses', __('WP SES', 'wp-ses'), 'manage_options', __FILE__, 'wpses_options');
    add_action( 'load-' . $hook_suffix, 'wpses_load_assets' );

    // Quota and Stats
    if (!defined('WP_SES_HIDE_STATS') or ( false == WP_SES_HIDE_STATS)) {
        $hook_suffix = add_submenu_page('index.php', 'SES Stats', 'SES Stats', 'manage_options', 'wp-ses/ses-stats.php');
        add_action( 'load-' . $hook_suffix, 'wpses_load_assets' );
    }
}

function wpses_get_asset_url( $asset ) {
    $plugin_dir_path    = plugin_dir_path( __FILE__ );
    $plugin_folder_name = basename( $plugin_dir_path );
    $plugins_url = trailingslashit( plugins_url( $plugin_folder_name ) );
    return $plugins_url . 'asset/' . ltrim( $asset, '/' );
}

function wpses_load_assets() {
    $version            = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : WPSES_VERSION;

    $src = wpses_get_asset_url( 'css/styles.css' );
    wp_enqueue_style( 'wp-ses-styles', $src, array(), $version );
}

function wpses_from($mail_from_email) {
    global $wpses_options;
    $from_email = $wpses_options['from_email'];
    if (empty($from_email)) {
        return $mail_from_email;
    } else {
        return $from_email;
    }
}

function wpses_from_name($mail_from_name) {
    global $wpses_options;
    $from_name = $wpses_options['from_name'];
    if (empty($from_name)) {
        return $mail_from_name;
    } else {
        return $from_name;
    }
}

function wpses_message_step1done() {
    global $WPSESMSG;
    echo "<div id='wpses-warning' class='updated fade'><p><strong>" . __("A confirmation request has been sent. You will receive at the stated email a confirmation request from amazon SES. You MUST click on the provided link in order to confirm your sender Email.<br />SES Answer - ", 'wp-ses') . $WPSESMSG . "</strong></p></div>";
}

/**
 *
 * @global type $wpses_options
 * @global type $SES
 * @return Fetches verifiedIdentities
 */
function wpses_getverified() {
    global $wpses_options;
    global $SES;
    wpses_check_SES();
    @ $result = $SES->listIdentities();
    if (is_array($result)) {
        wpses_log(count($result) . ' Verified Identities.');
        return $result['Identities'];
    } else {
        wpses_log('No verified Identity.');
        return NULL;
    }
}

function wpses_check_SES() {
    global $wpses_options;
    global $SES;
    if (!isset($SES)) {
        $SES = new SimpleEmailService($wpses_options['access_key'], $wpses_options['secret_key'], $wpses_options['endpoint']);
    }
}

/*
  function wpses_error_handler($level, $message, $file, $line, $context) {
  global $WPSESMSG;
  $WPSESMSG = __('SES Error: ', 'wp-ses') . $message;
  wpses_log("SES Error\t" . $message['Error']['Code'] . "\t" . $message['Error']['Message'] . "\t" . $message['Error']['RequestId']);
  return (true); //And prevent the PHP error handler from continuing
  }
 */

// start email verification (mail from amazon to sender, requesting validation)
function wpses_verify_sender_step1($mail) {
    global $wpses_options;
    global $SES, $WPSESMSG;
    wpses_check_SES();
    $WPSESMSG = '';
    // dans la chaine : Sender - InvalidClientTokenId  si auth pas correct
    // Sender - OptInRequired
    // The AWS Access Key Id needs a subscription for the service: si cl� aws ok, mais pas d'abo au service amazon lors de la verif d'un mail
    // inscription depuis aws , verif phone.
    //Use our custom handler
    set_error_handler('wpses_error_handler');
    try {
        $rid = $SES->verifyEmailIdentity($mail);
        $senders = get_option('wpses_senders');
        if ($rid <> '') {
            $senders[$mail] = array(
                $rid['RequestId'],
                false
            );
            $wpses_options['credentials_ok'] = 1;
            wpses_log('credentials_ok, sender verified');
            update_option('wpses_options', $wpses_options);
            update_option('wpses_senders', $senders);
        }
        //echo("rid ");
        //print_r($rid);
    } catch (Exception $e) {
        $WPSESMSG = __('Got exception: ', 'wp-ses') . $e->getMessage() . "\n";
        wpses_log('SES Exception ' . $e->getMessage());
    }
    restore_error_handler();
    $WPSESMSG .= ' id ' . var_export($rid, true);
    wpses_message_step1done();
    //	add_action('admin_notices', 'wpses_message_step1done'); // no : too late for this !
}

function wpses_remove_sender($mail) {
    global $wpses_options;
    global $SES, $WPSESMSG;
    wpses_check_SES();
    $WPSESMSG = '';
    $rid = $SES->deleteVerifiedEmailAddress($mail);
    $WPSESMSG .= ' id ' . var_export($rid, true);
    echo "<div id='wpses-warning' class='updated fade'><p><strong>" . $mail . '<br />' . __("This email address has been removed from verified senders.", 'wp-ses') . "</strong></p></div>";
}

/**
 * Returns true is sender email is defined and allowed by ses
 * @global type $wpses_options
 */
function wpses_sender_confirmed() {
    global $wpses_options;
    if ($wpses_options['from_email'] != '') {
        $senders = (array) get_option('wpses_senders');
        if ($senders[$wpses_options['from_email']][1]) {
            // email matches
            return true;
        }
        // email is not known, but domain could be listed
        list($user, $domain) = explode('@', $wpses_options['from_email']);
        if ($senders[$domain][1] === TRUE) {
            //echo($domain." domain validated ");
            return true;
        }
    }
    return false;
}

function wpses_message_testdone() {
    global $WPSESMSG;
    echo "<div id='wpses-warning' class='updated fade'><p><strong>" . __("Test message has been sent to your sender Email address.<br />SES Answer - ", 'wp-ses') . $WPSESMSG . "</strong></p></div>";
}

function wpses_test_email($mail) {
    global $wpses_options;
    global $SES, $WPSESMSG;
    wpses_check_SES();
    $WPSESMSG = '';
    $rid = wpses_mail($wpses_options['from_email'], __('WP SES - Test Message', 'wp-ses'), __("This is WP SES Test message. It has been sent via Amazon SES Service.\nAll looks fine !\n\n", 'wp-ses'));
    $WPSESMSG .= ' id ' . var_export($rid, true);
    wpses_message_testdone();
}

function wpses_prod_email($mail, $subject, $content) {
    global $wpses_options;
    global $SES, $WPSESMSG;
    wpses_check_SES();
    $WPSESMSG = '';
    $rid = wpses_mail($mail, $subject, $content);
    $WPSESMSG .= ' id ' . var_export($rid, true);
    echo "<div id='wpses-warning' class='updated fade'><p><strong>" . __("Test message has been sent.<br />SES Answer - ", 'wp-ses') . $WPSESMSG . "</strong></p></div>";
}

function wpses_error_handler($errno, $errstr, $errfile, $errline) {
    switch ($errno) {
        case E_USER_WARNING:
            if (is_admin()) {
                echo "<div id='wpses-warning' class='updated fade'><p><strong>" . __("Error : ", 'wp-ses') . "[$errno] $errstr" . "</strong></p></div>";
            }
            wpses_log("Error\t$errstr");
            break;
    }
    /* Don't execute PHP internal error handler */
    return true;
}

// returns msg id
function wpses_mail($to, $subject, $message, $headers = '', $attachments = '') {
    global $SES;
    global $wpses_options;
    global $wp_better_emails;
    // headers can be sent as array, too. convert them to string to avoid further complications.
    if (is_array($headers)) {
        $headers = implode("\r\n", $headers);
    }
    if (is_array($to)) {
        $to = implode(",", $to);
    }
    extract(apply_filters('wp_mail', compact('to', 'subject', 'message', 'headers')));
    wpses_log('wpses_mail ' . $to . "\t" . $headers);
    wpses_check_SES();

    if (isset($wp_better_emails)) {
        // From wpbe plugin, not efficient nor elegant - Will do better next time.
        // Could just call the php filter on a adhoc object, to be less dependant on the implementation of wpbe code.
        $txt = wp_specialchars_decode($message, ENT_QUOTES);
        $txt = $wp_better_emails->set_email_template($txt, 'plaintext_template');
        $txt = apply_filters('wpbe_plaintext_body', $wp_better_emails->template_vars_replacement($txt));
        /** HTML ******************************************************* */
        $html = $wp_better_emails->esc_textlinks($message);
        $html = nl2br(make_clickable($html));
        $html = $wp_better_emails->set_email_template($html);
        $html = apply_filters('wpbe_html_body', $wp_better_emails->template_vars_replacement($html));
    } else {
        $message = preg_replace('/<(http:.*)>/', '$1', $message);
        $message = preg_replace('/<(https:.*)>/', '$1', $message); // bad hack - handle httpS as well.
        $html = $message;
        $txt = strip_tags($html);
        if (strlen($html) == strlen($txt)) {
            $html = ''; // que msg text
        }
        // no html entity in txt.
        $txt = html_entity_decode($txt, ENT_NOQUOTES, 'UTF-8');
    }
    // TODO: option pour que TXT si msg html, ou les deux comme ici par défaut.
    $m = new SimpleEmailServiceMessage();


    // To: may contain comma separated emails. If so, explode and add them all.
    // what to do if more than 50 ? (SES limit)
    if (preg_match('/,/im', $to)) {
        $to = explode(',', $to);
        foreach ($to as $toline) {
            $m->addTo($toline);
        }
    } else {
        $m->addTo($to);
    }
    $m->setReturnPath($wpses_options['return_path']);
    $from = $wpses_options['from_name'] . ' <' . $wpses_options['from_email'] . '>';
    if ('' != $wpses_options['reply_to']) {
        if ('headers' == strtolower($wpses_options['reply_to'])) {
            // extract replyto from headers
            wpses_log('extract headers');
            $rto = array();
            //if (preg_match('/^Reply-To: ([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4})\b/imsU', $headers, $rto)) {
            //if (preg_match('/^Reply-To: (.*)\b/imsU', $headers, $rto)) {
            if (preg_match('/^Reply-To: (.*)/im', $headers, $rto)) {
                // does only support one email for now.
                $m->addReplyTo($rto[1]);
                wpses_log('add Reply-To ' . $rto[1]);
            }
            if (preg_match('/^From: (.*)/im', $headers, $rto)) {
                // Uses "From:" header - was /isU which broke things, see https://wordpress.org/support/topic/gravity-forms-18205-latest-contact-form-7-403-latest-not-working
                $from = $rto[1];
                wpses_log('add from ' . $rto[1]);
            }
            // Handle multiple cc and bcc: from headers too ? Guess so... TODO
            if ('' != $headers) {
                $headers = str_replace("\r\n", "\n", $headers);
                //wpses_log('headers are ' . $headers);
                $lines = explode("\n", $headers);
                foreach ($lines as $line) {
                    wpses_log('Line ' . $line);
                    if (preg_match('/^cc: (.*)/im', $line, $cc)) {
                        $m->addCC($cc[1]);
                        wpses_log('add cc ' . $cc[1]);
                    }
                    if (preg_match('/^Bcc: (.*)/im', $line, $Bcc)) {
                        $m->addBCC($Bcc[1]);
                        wpses_log('add Bcc ' . $Bcc[1]);
                    }
                }
            }
            // Test : use full headers if provided
            //$m->addCustomHeader($headers);
        } else {
            $m->addReplyTo($wpses_options['reply_to']);
        }
    }
    $m->setFrom($from);
    $m->setSubject($subject);
    if ($html == '') { // que texte
        $m->setMessageFromString($txt);
    } else {
        $m->setMessageFromString($txt, $html);
    }
    // Attachments
    if ('' != $attachments) {
        if (!is_array($attachments)) {
            $attachments = explode("\n", $attachments);
        }
        // Now we got an array
        foreach ($attachments as $afile) {
            $m->addAttachmentFromFile(basename($afile), $afile);
        }
    }

    set_error_handler('wpses_error_handler');
    $res = $SES->sendEmail($m);
    restore_error_handler();

    // Call custom Hook
    do_action('wpses_mailsent', $to, $subject, $message, $headers, $attachments);

    if (is_array($res)) {
        wpses_log('SES id=' . $res['MessageId']);
        return $res['MessageId'];
    } else {
        return NULL;
    }
}

function wpses_default_options($array, $value) {
    global $wpses_options;
    foreach ($array as $key) {
        if (!isset($wpses_options[$key])) {
            $wpses_options[$key] = '';
        }
    }
}

function wpses_getoptions() {
    global $wpses_options;
    $wpses_options = get_option('wpses_options');
    if (!is_array($wpses_options)) {
        $wpses_options = array();
    }
    wpses_default_options(array('reply_to', 'access_key', 'secret_key', 'from_email', 'from_name', 'return_path'), '');
    wpses_default_options(array('sender_ok', 'credentials_ok'), 1);
    wpses_default_options(array('force', 'log', 'active'), 0);

    if (defined('WP_SES_ENDPOINT')) {
        $wpses_options['endpoint'] = WP_SES_ENDPOINT;
    }
    if (!isset($wpses_options['endpoint'])) {
        $wpses_options['endpoint'] = 'email.us-east-1.amazonaws.com';
    }
    if ('' == $wpses_options['endpoint']) {
        $wpses_options['endpoint'] = 'email.us-east-1.amazonaws.com';
    }
    if (defined('WP_SES_ACCESS_KEY')) {
        $wpses_options['access_key'] = WP_SES_ACCESS_KEY;
    }
    if (defined('WP_SES_SECRET_KEY')) {
        $wpses_options['secret_key'] = WP_SES_SECRET_KEY;
    }
    if (defined('WP_SES_RETURNPATH')) {
        if ('' != WP_SES_RETURNPATH) {
            $wpses_options['return_path'] = WP_SES_RETURNPATH;
        }
    }
    if (defined('WP_SES_FROM')) {
        if ('' != WP_SES_FROM) {
            $wpses_options['from_email'] = WP_SES_FROM;
        }
    }
    if (defined('WP_SES_REPLYTO')) {
        if ('' != WP_SES_REPLYTO) {
            $wpses_options['reply_to'] = WP_SES_REPLYTO;
        }
    }
    if (defined('WP_SES_AUTOACTIVATE')) {
        if (WP_SES_AUTOACTIVATE) {
            $wpses_options['active'] = 1;
        }
    }
}

global $wpses_options;
if (!isset($wpses_options)) {
    wpses_getoptions();
}
// Test : always auto-activate
//$wpses_options['active'] = 1;

if ($wpses_options['active'] == 1) {
    if (!function_exists('wp_mail')) {

        function wp_mail($to, $subject, $message, $headers = '', $attachments = '') {
            global $wpses_options;
            $id = wpses_mail($to, $subject, $message, $headers, $attachments);
            if ($id != '') {
                return true;
            } else {
                return false;
            }
        }

    } else {
        wpses_log("ERROR\twp_mail override by another plugin !!");

        function wpses_warningmail() {
            echo "<div id='wpses-warning' class='updated fade'><p><strong>" . __('Another plugin did override wp-mail function. Please de-activate the other plugin if you want WP SES to work properly.', 'wp-ses') . "</strong></p></div>";
        }

        add_action('admin_notices', 'wpses_warningmail');
        // Desactiver "active" si actif.
        if ($wpses_options['active'] == 1) {
            wpses_log("Then deactivating plugin");
            try {
                $func = new ReflectionFunction('wp_mail');
                wpses_log('wp_mail already defined in ' . $func->getFileName());
            } catch (Exception $e) {

            }

            $wpses_options['active'] = 0;
            update_option('wpses_options', $wpses_options);
        }
    }
    $SES = new SimpleEmailService($wpses_options['access_key'], $wpses_options['secret_key'], $wpses_options['endpoint']);
}

$WPSESMSG = '';
