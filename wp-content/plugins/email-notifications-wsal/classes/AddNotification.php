<?php if(! defined('WSAL_OPT_PREFIX')) { exit('Invalid request'); }

class WSAL_NP_AddNotification extends WSAL_AbstractView
{

    public function GetTitle() {
        return __('Add New Email Notification', 'email-notifications-wsal');
    }

    public function GetIcon() {
        return 'dashicons-admin-generic';
    }

    public function GetName() {
        return __('Add notification', 'email-notifications-wsal');
    }

    public function GetWeight() {
        return 8;
    }

    public function Header()
    {
        $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../')));
        wp_enqueue_style('wsal-jq-datepick-css', $pluginPath.'/js/jquery.datepick/smoothness.datepick.css');
        wp_enqueue_style('wsal-jq-timepick-css', $pluginPath.'/js/jquery.timeentry/jquery.timeentry.css');
        wp_enqueue_style('wsal-notif-css', $pluginPath.'/css/styles.css');
        wp_enqueue_script('wsal-markup-js', $pluginPath.'/js/markup.js/src/markup.min.js', array('jquery'));
        echo "<script type='text/javascript'>";
        echo "var dateFormat = '" . $this->_plugin->wsalCommon->DateValidFormat() . "';";
        echo "var show24Hours = '" . $this->_plugin->wsalCommon->Show24Hours() . "';";
        echo "</script>";
        wp_enqueue_script('wsal-notif-utils-js', $pluginPath.'/js/wsal-notification-utils.js', array('jquery'));
        wp_enqueue_script('wsal-validator-js', $pluginPath.'/js/wsal-form-validator.js', array('jquery'));
        wp_enqueue_script('wsal-groups-js', $pluginPath.'/js/wsal-groups.js', array('jquery'));
        echo '<script type="text/javascript">';
            include(realpath(dirname(__FILE__).'/../').'/js/wsal-translator.js');
        echo '</script>';
        wp_enqueue_script('wsal-jq-datetime-pick-plugin-js', $pluginPath.'/js/jquery.datepick/jquery.plugin.min.js', array('jquery'));
        wp_enqueue_script('wsal-jq-datepick-js', $pluginPath.'/js/jquery.datepick/jquery.datepick.min.js', array('jquery'));
        wp_enqueue_script('wsal-jq-timepick-js', $pluginPath.'/js/jquery.timeentry/jquery.timeentry.min.js', array('jquery'));
    }
    
    public function Footer()
    {}

    public function Render()
    {
        if (!$this->_plugin->settings->CurrentUserCan('edit')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'email-notifications-wsal'));
        }

        if (! $this->_plugin->wsalCommon->CanAddNotification()) {
            ?><div class="error"><p><?php _e('You have reached the maximum number of notifications you can add.', 'email-notifications-wsal'); ?></p></div><?php
            return;
        }

        // flag for postbacks
        $__wsal_is_postback = false;

        $rm = strtoupper($_SERVER['REQUEST_METHOD']);
        if ($rm == 'POST' && isset($_POST['wsal_add_notification_field'])) {
            // verify nonce
            if (!wp_verify_nonce($_POST['wsal_add_notification_field'], 'wsal_add_notification_action')) {
                wp_die(__('You do not have sufficient permissions to access this page.', 'email-notifications-wsal'));
            }

            if (isset($_POST['wsal_form_data'])) {
                $notifBuilder = new WSAL_NP_NotificationBuilder();
                $notification = $notifBuilder->decodeFromString($_POST['wsal_form_data']);
                $this->_plugin->wsalCommon->SaveNotification($notifBuilder, $notification);
                $__wsal_is_postback = true;
            }
            // not a valid request
            else {
                wp_die(__('You do not have sufficient permissions to access this page.', 'email-notifications-wsal'));
            }
        }
        $pluginDirPath = realpath(dirname(__FILE__).'/../').'/';
        ?>
        <div class="wrap">
            <div id="wsal-error-container" class="invalid" style="display:none;"><p></p></div>
            <form id="wsal-trigger-form" method="post">
                <div id="wsal-section-title"></div>
                <div class="postbox wsal-helpbox">
                    <div class="inside">
                        <p><?php _e('Configure triggers that should be matched for a notification email to be sent in this section.
                                        You can add up to 5 triggers and use the AND and OR operands to join them together.', 'wpsal-notifications');?></p>
                    </div>
                </div>
                <div id="wsal-triggers-view">
                    <h3 id="wsal-sub-heading" class="f-container">
                        <span class="f-left" style="margin-top: 4px;"><?php _e('Triggers', 'email-notifications-wsal');?></span>
                        <span class="f-left" style="margin-left: 36px;"><input id="wsal-button-add-trigger" type="button" class="button-secondary" value="+ <?php _e('Add Trigger', 'email-notifications-wsal');?>"/></span>
                    </h3>

                    <div id="wsal-header-top-bar"></div>

                    <div style="overflow:hidden; min-height: 1px; clear: both;">
                        <?php /*[ Content dynamically added here ]*/ ?>
                        <div id="wsal_content_js"></div>
                    </div>
                </div>
                <pre id="wsal_error_triggers" style="display: none;"></pre>
                <div id="wsal-section-email"></div>

                <input type="hidden" id="wsal-form-data" name="wsal_form_data"/>
                <?php wp_nonce_field('wsal_add_notification_action', 'wsal_add_notification_field'); ?>                   
            </form>

            <script type="text/javascript" id="wsalModel">
                // This object will only be populated on POST
                var wsalModelWp = (wsalModelWp ? JSON.parse(wsalModelWp) : null);
                <?php include($pluginDirPath.'js/wsal-notification-model.inc.js'); ?>

                jQuery(document).ready(function($){
                    // so we can repopulate fields in case of errors
                    jQuery.WSAL_EDIT_VIEW = <?php echo $__wsal_is_postback ? 1 : 0;?>;
                    <?php include($pluginDirPath.'js/wsal-notifications-view.inc.js'); ?>
                });
            </script>

            <script type="text/template" id="scriptTitle">
                <input type="text" size="30" autocomplete="off" id="wsal-notif-title" placeholder="<?php _e('Title', 'email-notifications-wsal');?> *" value="{{info.title|clean}}" maxlength="125"/>
                {{if errors.titleMissing}}<label class="error" for="wsal-notif-title">{{errors.titleMissing}}</label>{{/if}}
                {{if errors.titleInvalid}}<label class="error" for="wsal-notif-title">{{errors.titleInvalid}}</label>{{/if}}
            </script>

            <script type="text/template" id="scriptEmail">
                <p>
                    <span>{{info.emailLabel}}</span>
                    <input type="text" id="wsal-notif-email" placeholder="<?php _e('Email', 'email-notifications-wsal');?> *" value="{{info.email|clean}}" />
                    {{if errors.emailMissing}}<label class="error" for="wsal-notif-email">{{errors.emailMissing}}</label>{{/if}}
                    {{if errors.emailInvalid}}<label class="error" for="wsal-notif-email">{{errors.emailInvalid}}</label>{{/if}}
                    <input type="submit" id="wsal-submit" name="wsal-submit" value="{{buttons.addNotifButton}}" class="button-primary"/>
                </p>
                <p class="wsal-helptext"><?php _e('Specify the email address or email addresses which should receive the notification once the trigger is matched. To specify multiple email addresses, separate them with a comma (,).', 'wpsal-notifications');?></p>
            </script>

            <script type="text/template" id="scriptTrigger">
                <div id="trigger_id_{{lastId}}" class="wsal_trigger">
                    <div class="wsal-fly">
                        <div class="wsal-s1">
                            {{if numTriggers|ormore>2}}
                                <span class="custom-dropdown custom-dropdown--default custom-dropdown--small">
                                    <select id="select_1_{{lastId}}" class="js_s1 custom-dropdown__select custom-dropdown__select--default">
                                        {{select1.data}}<option value="{{.|upcase|clean}}" {{if #|first}}selected="selected"{{/if}}>{{.|upcase|clean}}</option>{{/select1.data}}
                                    </select>
                                    <input type="hidden" id="select_1_{{lastId}}_hidden" value="0"/>
                                </span>
                            {{/if}}
                        </div>

                        <div class="wsal-s2">
                            <span class="custom-dropdown custom-dropdown--default custom-dropdown--small">
                                <select id="select_2_{{lastId}}" class="js_s2 custom-dropdown__select custom-dropdown__select--default">
                                    {{select2.data}}<option value="{{.|upcase|clean}}" {{if #|first}}selected="selected"{{/if}}>{{.|upcase|clean}}</option>{{/select2.data}}
                                </select>
                               <input type="hidden" id="select_2_{{lastId}}_hidden" value="0"/>
                            </span>
                        </div>

                        <div class="wsal-s3">
                            <span class="custom-dropdown custom-dropdown--default custom-dropdown--small">
                                <select id="select_3_{{lastId}}" class="js_s3 custom-dropdown__select custom-dropdown__select--default">
                                    {{select3.data}}<option value="{{.|upcase|clean}}" {{if #|first}}selected="selected"{{/if}}>{{.|upcase|clean}}</option>{{/select3.data}}
                                </select>
                                <input type="hidden" id="select_3_{{lastId}}_hidden" value="0"/>
                            </span>
                        </div>
                    </div>
                    <div class="wsal-fly dd">
                        <input id="input_1_{{lastId}}" class="wsal-trigger-input" value="{{input1|clean}}" placeholder="Required *" maxlength="50"/>
                        <input type="button" id="deleteButton_{{lastId}}"
                               value="{{deleteButton}}"
                               data-removeid = "trigger_id_{{lastId}}"
                               class="button-secondary"/>
                        {{if numTriggers|ormore>2}}
                        <div class="wsal_options_dd">
                            <div>
                            <span class="custom-dropdown custom-dropdown--default custom-dropdown--small">
                                <select id="wsal_options_{{lastId}}" class="custom-dropdown__select custom-dropdown__select--default wsal_dd_options"></select>
                            </span>
                            </div>
                        </div>
                        {{/if}}
                    </div>
                </div>
            </script>
        </div>
        <?php
    }
}