<?php if(! defined('WSAL_OPT_PREFIX')) { exit('Invalid request'); }

class WSAL_NP_Wizard extends WSAL_AbstractView
{
    public function __construct(WpSecurityAuditLog $plugin)
    {
        parent::__construct($plugin);
        add_action('wp_ajax_SaveFirstStep', array($this, 'SaveFirstStep'));
        add_action('wp_ajax_SaveChanges', array($this, 'SaveChanges'));
        add_action('wp_ajax_ShowAlertByType', array($this, 'ShowAlertByType'));
    }

    public function GetTitle() {
        return __('Email Notifications Wizard', 'email-notifications-wsal');
    }

    public function GetIcon() {
        return 'dashicons-admin-generic';
    }

    public function GetName() {
        return __('Wizard', 'email-notifications-wsal');
    }

    public function GetWeight() {
        return 8;
    }

    protected function GetSafeCatgName($name)
    {
        return strtolower(
            preg_replace('/[^A-Za-z0-9\-]/', '-', $name)
        );
    }

    public function Header()
    {
        $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../')));
        wp_enqueue_style('wsal-notif-css', $pluginPath.'/css/wizard.css');
    }
    
    public function Footer()
    {
        $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../')));
        wp_enqueue_script('wizard-js', $pluginPath.'/js/wizard.js', array('jquery'));
    }

    public function SaveFirstStep()
    {
        $notifBuilder = new WSAL_NP_Notifications($this->_plugin);
        $result = 0;
        $results = array();

        $titles = array(
            1 => "User logs in",
            2 => "New user is created",
            3 => "User changed password",
            4 => "User changed the password of another user",
            5 => "User's role has changed",
            6 => "Published content is modified",
            7 => "Content is published",
            8 => "First time user logs in",
            9 => "New plugin is installed",
            10 => "Installed plugin is activated",
            11 => "Plugin file is modified",
            12 => "New theme is installed",
            13 => "Installed theme is activated",
            14 => "Theme file is modified",
            15 => "Critical Alert is Generated"
        );

        $events = array(
            1 => "1000",
            2 => array("4000", "4001", "4012"),
            3 => "4003",
            4 => "4004",
            5 => "4002",
            6 => array("2065", "2066", "2067"),
            7 => array("2001", "2005", "2030"),
            8 => "1000",
            9 => "5000",
            10 => "5001",
            11 => "2051",
            12 => "5005",
            13 => "5006",
            14 => "2046",
            15 => "2046"
        );

        if (isset($_POST['builtIn'])) {
            $aBuiltIn = (array)json_decode(stripslashes($_POST['builtIn']));
            foreach ($aBuiltIn as $key => $value) {
                if (!empty($value)) {
                    $email = (array)$value;
                    $results[] = $notifBuilder->saveBuilt_in($key, $titles[$key], $email['email'], $events[$key]);
                } else {
                    $results[] = $notifBuilder->saveBuilt_in($key, null, null, null);
                }
            }
        }
        if (in_array(2, $results)) {
            $result = 2;
        } else if (in_array(1, $results)) {
            $result = 1;
        }

        echo json_encode($result);
        exit;
    }

    public function SaveChanges()
    {
        $notifBuilder = new WSAL_NP_Notifications($this->_plugin);
        $result = false;

        if (isset($_POST['alerts']) && isset($_POST['email']) && isset($_POST['name'])) {
            $email = trim($_POST['email']);
            $title = trim($_POST['name']);
            $name = $this->GetSafeCatgName($title);
            $events = json_decode(stripslashes($_POST['alerts']));
            $result = $notifBuilder->saveBuilt_in($name, $title, $email, $events, false);
        }
        
        echo json_encode($result);
        exit;
    }

    public function ShowAlertByType()
    {
        if (isset($_POST['type'])) {
            $alert = new WSAL_Alert();
            $groupedAlerts = $this->_plugin->alerts->GetCategorizedAlerts();
            foreach ($groupedAlerts as $name => $alerts) {
                $friendly_name = $this->GetSafeCatgName($name);
                if ($friendly_name == $_POST['type']) {
                    echo "<tr><td colspan=\"2\" style=\"padding-bottom: 0;\"><h2>" . $name . "</h2></td></tr>";
                    echo "<tr><th><label for=\"columns\">Alerts:</label></th>";
                    echo "<td><fieldset><label><input type=\"checkbox\" onclick=\"checkAll(this, 'alerts');\" class=\"option\">";
                    echo "<span class=\"title\">" . _e('Check All', 'email-notifications-wsal') . "</span></label></fieldset>";
                    echo "<fieldset><input type=\"hidden\" id=\"category-name\" value=\"" . $friendly_name . "\">";
                    foreach ($alerts as $alert) {
                        echo "<label for=\"" . $alert->type . "\">";
                        echo "<input type=\"checkbox\" name=\"alerts[]\" id=\"" . $alert->type . "\" class=\"option\" value=\"" . $alert->type . "\">";
                        echo "<span class=\"title\">" . $alert->type . ' (' . $alert->desc . ')' . "</span></label><br/>";
                    }
                    echo "</fieldset></td></tr>";
                }
            }
        }
        exit;
    }

    public function Render()
    {
        if (!$this->_plugin->settings->CurrentUserCan('edit')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'email-notifications-wsal'));
        }

        $oNP = $this->_plugin->views->FindByClassName('WSAL_NP_Notifications');
        if (false === $oNP) {
            $oNP = new WSAL_NP_Notifications($this->_plugin);
        }
        ?>
        <div class="wrap">
            <h2 id="wsal-tabs" class="nav-tab-wrapper">
                <a href="#tab-first" class="nav-tab"><?php _e('Enable Recommended<br>Security Notifications'); ?></a>
                <a href="#tab-second" class="nav-tab disabled"><?php _e('Select Alerts<br>Categories'); ?></a>
                <a href="#tab-third" class="nav-tab disabled"><?php _e('Select the<br>specific change'); ?></a>
                <a href="#tab-fourth" class="nav-tab disabled"><?php _e('<br>Save changes<br>'); ?></a>
                <a href="#tab-fifth" class="nav-tab disabled"><?php _e('<br>Finish<br>'); ?></a>
            </h2>
            <div class="nav-tabs">
                <!-- Tab Enable Recommended Security Notifications -->
                <table class="form-table wsal-tab" id="tab-first">
                    <tbody class="widefat">
                        <tr>
                            <td colspan="2">
                                <h2><?php _e('Enable Pre-built Notifications', 'email-notifications-wsal'); ?></h2>
                                <p>
                                    <span class="description"><?php _e('In this first step of the wizard you can enable the recommended security related email notifications. If you do not want to enable any of these alerts click Skip this Step.', 'email-notifications-wsal'); ?></span><br>
                                    <span class="description"><?php _e('Tick any of the alerts below to enable them.', 'email-notifications-wsal'); ?></span>
                                </p>
                            </td>
                        </tr>
                        <?php
                        $checked = array();
                        $email = array();

                        $aBuilt_in = $this->_plugin->wsalCommon->GetBuiltIn();
                        if (!empty($aBuilt_in) && count($aBuilt_in) > 0) {
                            foreach ($aBuilt_in as $k => $v) {
                                $optValue = unserialize($v->option_value);
                                $checked[] = $optValue->viewState[0];
                                $email[$optValue->id] = $optValue->email;
                            }
                        }
                        ?>
                        <tr>
                            <th><label for="columns"><?php _e('Alert me when:', 'email-notifications-wsal'); ?></label></th>
                            <td>
                                <fieldset>
                                    <label for="built-in">
                                        <input type="checkbox" id="built-in" onclick="checkAll(this, 'built-in');" class="option">
                                        <span class="option-title"><?php _e('Check All', 'email-notifications-wsal'); ?></span>
                                    </label>
                                    <br/>
                                    <label for="1">
                                        <input type="checkbox" name="built-in[]" id="1" class="built-in" <?php echo(in_array("trigger_id_1", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('User logs in', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-1" id="email-1" placeholder="Email *" value="<?php echo(!empty($email[1])? $email[1] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="2">
                                        <input type="checkbox" name="built-in[]" id="2" class="built-in" <?php echo(in_array("trigger_id_2", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('New user is created', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-2" id="email-2" placeholder="Email *" value="<?php echo(!empty($email[2])? $email[2] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="3">
                                        <input type="checkbox" name="built-in[]" id="3" class="built-in" <?php echo(in_array("trigger_id_3", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('User changed password', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-3" id="email-3" placeholder="Email *" value="<?php echo(!empty($email[3])? $email[3] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="4">
                                        <input type="checkbox" name="built-in[]" id="4" class="built-in" <?php echo(in_array("trigger_id_4", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('User changed the password of another user', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-4" id="email-4" placeholder="Email *" value="<?php echo(!empty($email[4])? $email[4] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="5">
                                        <input type="checkbox" name="built-in[]" id="5" class="built-in" <?php echo(in_array("trigger_id_5", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e("User's role has changed", 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-5" id="email-5" placeholder="Email *" value="<?php echo(!empty($email[5])? $email[5] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="6">
                                        <input type="checkbox" name="built-in[]" id="6" class="built-in" <?php echo(in_array("trigger_id_6", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('Published content is modified', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-6" id="email-6" placeholder="Email *" value="<?php echo(!empty($email[6])? $email[6] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="7">
                                        <input type="checkbox" name="built-in[]" id="7" class="built-in" <?php echo(in_array("trigger_id_7", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('Content is published', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-7" id="email-7" placeholder="Email *" value="<?php echo(!empty($email[7])? $email[7] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="8">
                                        <input type="checkbox" name="built-in[]" id="8" class="built-in" <?php echo(in_array("trigger_id_8", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('First time user logs in', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-8" id="email-8" placeholder="Email *" value="<?php echo(!empty($email[8])? $email[8] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="9">
                                        <input type="checkbox" name="built-in[]" id="9" class="built-in" <?php echo(in_array("trigger_id_9", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('New plugin is installed', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-9" id="email-9" placeholder="Email *" value="<?php echo(!empty($email[9])? $email[9] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="10">
                                        <input type="checkbox" name="built-in[]" id="10" class="built-in" <?php echo(in_array("trigger_id_10", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('Installed plugin is activated', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-10" id="email-10" placeholder="Email *" value="<?php echo(!empty($email[10])? $email[10] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="11">
                                        <input type="checkbox" name="built-in[]" id="11" class="built-in" <?php echo(in_array("trigger_id_11", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('Plugin file is modified', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-11" id="email-11" placeholder="Email *" value="<?php echo(!empty($email[11])? $email[11] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="12">
                                        <input type="checkbox" name="built-in[]" id="12" class="built-in" <?php echo(in_array("trigger_id_12", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('New theme is installed', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-12" id="email-12" placeholder="Email *" value="<?php echo(!empty($email[12])? $email[12] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="13">
                                        <input type="checkbox" name="built-in[]" id="13" class="built-in" <?php echo(in_array("trigger_id_13", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('Installed theme is activated', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-13" id="email-13" placeholder="Email *" value="<?php echo(!empty($email[13])? $email[13] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="14">
                                        <input type="checkbox" name="built-in[]" id="14" class="built-in" <?php echo(in_array("trigger_id_14", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('Theme file is modified', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-14" id="email-14" placeholder="Email *" value="<?php echo(!empty($email[14])? $email[14] : null)?>">
                                    </label>
                                    <br/>
                                    <label for="15">
                                        <input type="checkbox" name="built-in[]" id="15" class="built-in" <?php echo(in_array("trigger_id_15", $checked)? 'checked' : '')?>>
                                        <span class="option-title"><?php _e('Critical Alert is Generated', 'email-notifications-wsal'); ?></span>
                                        <input type="text" class="option-email" name="email-15" id="email-15" placeholder="Email *" value="<?php echo(!empty($email[15])? $email[15] : null)?>">
                                    </label>
                                    <br/>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p>
                                    <span class="description"><?php _e('To specify multiple email addresses separate them with a comma.', 'email-notifications-wsal'); ?></span>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="widefat">
                        <tr>
                            <td colspan="2" class="buttons">
                                <button type="button" class="button-primary" id="save-first-step">Next</button>
                                <button type="button" class="button-secondary" onclick="nextStep('second');">Skip Step</button>
                                <a class="button-secondary" href="<?php echo esc_attr($oNP->GetUrl()); ?>">Cancel Wizard</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- Tab Select Alerts Categories -->
                <table class="form-table wsal-tab" id="tab-second">
                    <tbody class="widefat">
                        <tr>
                            <td colspan="2">
                                <h2><?php _e('Step 1:', 'email-notifications-wsal'); ?></h2>
                                <p>
                                    <span class="description"><?php _e('Select for which alert categories you would like to setup email notifications.', 'email-notifications-wsal'); ?></span>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="columns"><?php _e('Type of changes:', 'email-notifications-wsal'); ?></label></th>
                            <td>
                                <fieldset>
                                    <label for="type_1">
                                        <input type="radio" name="types[]" id="type_1" value="other-user-activity" class="option">
                                        <span class="title"><?php _e('User Changes', 'email-notifications-wsal'); ?></span>
                                    </label>
                                    <br/>
                                    <label for="type_2">
                                        <input type="radio" name="types[]" id="type_2" value="user-profiles" class="option">
                                        <span class="title"><?php _e('User Profile', 'email-notifications-wsal'); ?></span>
                                    </label>
                                    <br/>
                                    <label for="type_3">
                                        <input type="radio" name="types[]" id="type_3" value="plugins---themes" class="option">
                                        <span class="title"><?php _e('Plugin & Theme', 'email-notifications-wsal'); ?></span>
                                    </label>
                                    <br/>
                                    <label for="type_4">
                                        <input type="radio" name="types[]" id="type_4" value="blog-posts" class="option">
                                        <span class="title"><?php _e('Posts', 'email-notifications-wsal'); ?></span>
                                    </label>
                                    <br/>
                                    <label for="type_5">
                                        <input type="radio" name="types[]" id="type_5" value="pages" class="option">
                                        <span class="title"><?php _e('Pages', 'email-notifications-wsal'); ?></span>
                                    </label>
                                    <br/>
                                    <label for="type_6">
                                        <input type="radio" name="types[]" id="type_6" value="custom-posts" class="option">
                                        <span class="title"><?php _e('Custom Post Types', 'email-notifications-wsal'); ?></span>
                                    </label>
                                    <br/>
                                    <label for="type_7">
                                        <input type="radio" name="types[]" id="type_7" value="menus" class="option">
                                        <span class="title"><?php _e('Menus', 'email-notifications-wsal'); ?></span>
                                    </label>
                                    <br/>
                                    <label for="type_8">
                                        <input type="radio" name="types[]" id="type_8" value="widgets" class="option">
                                        <span class="title"><?php _e('Widgets', 'email-notifications-wsal'); ?></span>
                                    </label>
                                    <br/>
                                    <label for="type_9">
                                        <input type="radio" name="types[]" id="type_9" value="system-activity" class="option">
                                        <span class="title"><?php _e('WordPress Settings', 'email-notifications-wsal'); ?></span>
                                    </label>
                                    <br/>
                                    <label for="type_10">
                                        <input type="radio" name="types[]" id="type_10" value="bbpress-forum" class="option">
                                        <span class="title"><?php _e('bbPress', 'email-notifications-wsal'); ?></span>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="widefat">
                        <tr>
                            <td colspan="2" class="buttons">
                                <button type="button" class="button-primary" id="button-types" onclick="goToThirdStep();">Next</button>
                                <a class="button-secondary" href="<?php echo esc_attr($oNP->GetUrl()); ?>">Cancel Wizard</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- Tab Select the specific change -->
                <table class="form-table wsal-tab" id="tab-third">
                    <tbody class="widefat">
                        <tr>
                            <td colspan="2">
                                <h2><?php _e('Step 2:', 'email-notifications-wsal'); ?></h2>
                                <p>
                                    <span class="description"><?php _e('Select the specific change for which you would like to be alerted.', 'email-notifications-wsal'); ?></span><br>
                                </p>
                            </td>
                        </tr>
                        <tr id="loading">
                            <td colspan="2" style="text-align: center;">
                                <?php $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../'))); ?>
                                <img src="<?php echo $pluginPath; ?>/img/loading.gif">
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="widefat" id="category-alerts">
                        <!-- Generate from the ajax -->
                    </tbody>
                    <tbody class="widefat">
                        <tr>
                            <td colspan="2" class="buttons">
                                <button type="button" class="button-secondary" id="backToCategory">Back</button>
                                <button type="button" id="save-button" class="button-primary" onclick="nextStep('fourth');">Next</button>
                                <a class="button-secondary" href="<?php echo esc_attr($oNP->GetUrl()); ?>">Cancel Wizard</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- Tab Save changes -->
                <table class="form-table wsal-tab" id="tab-fourth">
                    <tbody class="widefat">
                        <tr>
                            <td colspan="2">
                                <h2><?php _e('Step 3:', 'email-notifications-wsal'); ?></h2>
                            </td>
                        </tr>
                        <tr>
                            <th style="padding-bottom: 5px;"><label for="columns"><?php _e('Send an email to:', 'email-notifications-wsal'); ?></label></th>
                            <td style="padding-bottom: 5px;">
                                <fieldset>
                                    <input type="email" class="option-email" placeholder="Email *" id="notifications-email" value="">
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top: 0;">
                                <p>
                                    <span class="description"><?php _e('To specify multiple email addresses separate them with a comma.', 'email-notifications-wsal'); ?></span>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="columns"><?php _e('Email Notification Name:', 'email-notifications-wsal'); ?></label></th>
                            <td>
                                <fieldset>
                                    <input type="text" class="option-name" placeholder="Name" id="notifications-name">
                                </fieldset>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="widefat">
                        <tr>
                            <td colspan="2" class="buttons">
                                <button type="button" class="button-secondary" id="backToAlerts">Back</button>
                                <button type="button" id="save-button" class="button-primary" onclick="saveNotifications();">Save Changes</button>
                                <a class="button-secondary" href="<?php echo esc_attr($oNP->GetUrl()); ?>">Cancel Wizard</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- Tab Finish -->
                <table class="form-table wsal-tab" id="tab-fifth">
                    <tbody class="widefat">
                        <tr>
                            <td>
                                <div class="finish-box">
                                    <h2><?php _e('To configure more alerts launch this wizard again or click the Add New button in the Email Notifications Trigger Builder tab to manually build a notification with the Trigger Builder.', 'email-notifications-wsal'); ?></h2>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="widefat">
                        <tr>
                            <td colspan="2" class="buttons">
                                <a class="button-secondary" href="#tab-second" onclick="window.location.reload(true);">Configure Another Alert</a>
                                <a class="button-primary" href="<?php echo esc_attr($oNP->GetUrl()); ?>">Exit Wizard</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}
