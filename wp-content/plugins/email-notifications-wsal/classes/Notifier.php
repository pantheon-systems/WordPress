<?php if(! defined('WSAL_OPT_PREFIX')) { exit('Invalid request'); }
/**
 * Class WSAL_Notifier
 * Loop through notifications and check if any matches the current generated alert
 * @author wp.kytten
 */

class WSAL_NP_Notifier extends WSAL_AbstractLogger
{
    // Alert data
    private $_alertDate = null;
    private $_emailAddress = '';
    private $_alertID = null;
    private $_alertData = null;
    // Notification data
    private $_s1Data = null;
    private $_s2Data = null;
    private $_s3Data = null;
    private $_isBuiltIn = false;
    // Cache
    private $_notifications = null;
    private $_cacheExpire = 43200; // 12h (60*60*12)

    public function Log($type, $data = array(), $date = null, $siteid = null, $migrated = false)
    {
        $this->_alertID = $type;
        $this->_alertData = $data;
        $this->_alertDate = $date;

        $nb = new WSAL_NP_NotificationBuilder();

        $this->_s1Data = $nb->GetSelect1Data();
        $this->_s2Data = $nb->GetSelect2Data();
        $this->_s3Data = $nb->GetSelect3Data();

        // cache notifications
        // see: http://codex.wordpress.org/Class_Reference/WP_Object_Cache
        $this->_notifications = wp_cache_get(WSAL_CACHE_KEY);

        if (false === $this->_notifications) {
            $this->_notifications = $this->plugin->wsalCommon->GetNotifications();
            wp_cache_set(WSAL_CACHE_KEY, $this->_notifications, null, $this->_cacheExpire);
        }
        $this->_notifyIfConditionMatch();
    }

    private function _notifyIfConditionMatch()
    {
        if (empty($this->_notifications)) {
            return;
        }
        // go through each notification
        foreach ($this->_notifications as $k => $v) {
            $notInfo = unserialize($v->option_value);
            $enabled = intval($notInfo->status);

            if ($enabled == 0) {
                continue;
            }
            
            $skip = false;
            if (!empty($notInfo->firstTimeLogin) && $this->_alertID == 1000) {
                $usersLoginList = $this->plugin->GetGlobalOption('users_login_list');
                if (!empty($usersLoginList)) {
                    if (in_array($this->_alertData['Username'], $usersLoginList)) {
                        $skip = true;
                    } else {
                        array_push($usersLoginList, $this->_alertData['Username']);
                        $this->plugin->SetGlobalOption('users_login_list', $usersLoginList);
                    }
                } else {
                    $usersLoginList = array();
                    array_push($usersLoginList, $this->_alertData['Username']);
                    $this->plugin->SetGlobalOption('users_login_list', $usersLoginList);
                }
            }
            if ($skip) {
                continue;
            }

            $conditions = $notInfo->triggers;
            $num = count($conditions);
            $title = $notInfo->title;
            $this->_emailAddress = $notInfo->email;
            
            if (!empty($notInfo->built_in)) {
                $this->_isBuiltIn = true;
            } else {
                $this->_isBuiltIn = false;
            }

            //#! one condition
            if ($num == 1) {
                $condition = $conditions[0];
                $s1 = $this->_s1Data[$condition['select1']];
                $s2 = $this->_s2Data[$condition['select2']];
                $s3 = $this->_s3Data[$condition['select3']];
                $i1 = $condition['input1'];
                $this->_checkIfConditionMatch($s1, $s2, $s3, $i1, $title, true);
            }
            //#! n conditions
            else {
                $testArray = array();
                $groups = $notInfo->viewState;
                $lastId = 0;
                foreach ($groups as $i => $entry) {
                    $i = $lastId;
                    if (is_string($entry)) {
                        array_push($testArray, $conditions[$i]);
                        $lastId++;
                    } elseif (is_array($entry)) {
                        $new = array();
                        foreach ($entry as $k => $item) {
                            array_push($new, $conditions[$lastId]);
                            $lastId++;
                        }
                        array_push($testArray, $new);
                    }
                }
                // Validate conditions
                $exp = new WSAL_NP_Expression($this, $this->_s1Data, $this->_s2Data, $this->_s3Data, $title);
                $result = $exp->EvaluateConditions($testArray);
                if ($result) {
                    $this->_sendNotificationEmail($title);
                }
            }
            /* Trigger Critical alert*/
            $alert = $this->plugin->alerts->GetAlert($this->_alertID);
            if (!empty($notInfo->isCritical) && $alert->code == 'E_CRITICAL') {
                $this->_sendNotificationEmail($title);
            }
        }
    }

    /**
     * Check whether or not a condition matches anything in the Request $data
     * @param string $s1
     * @param string $s2
     * @param string $s3
     * @param string $i1
     * @param null|string $title  The title of the alert
     * @param bool $sendEmail     Whether or not to send the notification email. Defaults to false
     * @return bool
     */
    function _checkIfConditionMatch($s1, $s2, $s3, $i1, $title = null, $sendEmail = false)
    {
        $date_format = $this->plugin->settings->GetDateFormat();
        $time_format = $this->plugin->settings->GetTimeFormat();
        $gmt_offset_sec = $this->GetTimezone();

        if ($s3 == 'IS EQUAL') {
            // Default - $type == ALERT CODE
            $value = $this->_alertID;

            if ($s2 == 'DATE') {
                $value = date($date_format);
            } elseif ($s2 == 'TIME') {
                $value = date($time_format);
            } elseif ($s2 == 'USERNAME') {
                $uid = (isset($this->_alertData['CurrentUserID']) ? intval($this->_alertData['CurrentUserID']): null);
                if (empty($uid)) { // will happen "on login"
                    // this will be populated
                    if (isset($this->_alertData['Username']) && !empty($this->_alertData['Username'])) {
                        $value = $this->_alertData['Username'];
                    }
                } else {
                    $user = get_user_by('id', $uid);
                    if ($user === false) {
                        $value = '';
                    } else {
                        $value = $user->user_login;
                    }
                }
            } elseif ($s2 == 'USER ROLE') {
                $roles = $this->_alertData['CurrentUserRoles'];
                foreach ($roles as $role) {
                    if (strcasecmp($i1, $role)==0) {
                        if ($sendEmail) {
                            return $this->_sendNotificationEmail($title);
                        } else {
                            return true;
                        }
                    }
                }
            } elseif ($s2 == 'SOURCE IP') {
                $value = $this->_alertData['ClientIP'];
            } elseif ($s2 == 'PAGE ID' || $s2 == 'POST ID' || $s2 == 'CUSTOM POST ID') {
                $pid = intval($i1);
                if (empty($pid) || !isset($this->_alertData['PostID'])) {
                    return false;
                }
                $dpid = intval($this->_alertData['PostID']);

                if ($pid <> $dpid) {
                    return false;
                }

                $postType = strtolower($this->_alertData['PostType']);

                if ($s2 == 'POST ID' && 'post' == $postType) {
                    if ($sendEmail) {
                        return $this->_sendNotificationEmail($title);
                    } else {
                        return true;
                    }
                } elseif ($s2 == 'PAGE ID' && 'page' == $postType) {
                    if ($sendEmail) {
                        return $this->_sendNotificationEmail($title);
                    } else {
                        return true;
                    }
                } elseif ($s2 == 'CUSTOM POST ID' && ($postType!='post' && $postType!='page')) {
                    if ($sendEmail) {
                        return $this->_sendNotificationEmail($title);
                    } else {
                        return true;
                    }
                }
            } elseif ($s2 == 'SITE DOMAIN') {
                $sid = intval($i1);
                $blog_id = get_current_blog_id();
                if (empty($sid)) {
                    return false;
                }

                if ($sid <> $blog_id) {
                    return false;
                }
                if ($sendEmail) {
                    return $this->_sendNotificationEmail($title);
                } else {
                    return true;
                }
            } elseif ($s2 == 'POST TYPE') {
                $postType = (isset($this->_alertData['PostType']) ? strtolower($this->_alertData['PostType']) : null);
                if (!empty($postType)) {
                    if ($postType == strtolower($i1)) {
                        if ($sendEmail) {
                            return $this->_sendNotificationEmail($title);
                        } else {
                            return true;
                        }
                    }
                }
            }
            // equality test - except user role
            if ($value == $i1) {
                if ($sendEmail) {
                    return $this->_sendNotificationEmail($title);
                } else {
                    return true;
                }
            }
        }
        // Valid only for: SOURCE IP
        elseif ($s3 == 'CONTAINS') {
            if ($s2 == 'SOURCE IP') {
                if (false !== strpos($this->_alertData['ClientIP'], $i1)) {
                    if ($sendEmail) {
                        return $this->_sendNotificationEmail($title);
                    } else {
                        return true;
                    }
                }
            }
        }
        // DATE & TIME ONLY
        elseif ($s3 == 'IS AFTER') {
            if ($s2 == 'DATE') {
                $today = date($date_format);
                $tstr = strtotime($today);
                $value = strtotime(str_replace('-', '/', $i1));
                if ($tstr > $value) {
                    if ($sendEmail) {
                        return $this->_sendNotificationEmail($title);
                    } else {
                        return true;
                    }
                }
            } elseif ($s2 == 'TIME') {
                $today = date($time_format);
                $tstr = strtotime($today) + $gmt_offset_sec;
                $value = strtotime($i1);
                if ($tstr > $value) {
                    if ($sendEmail) {
                        return $this->_sendNotificationEmail($title);
                    } else {
                        return true;
                    }
                }
            }
        }
        // TIME ONLY
        elseif ($s3 == 'IS BEFORE') {
            if ($s2 == 'TIME') {
                $today = date($time_format);
                $tstr = strtotime($today) + $gmt_offset_sec;
                $value = strtotime($i1);
                if ($tstr < $value) {
                    if ($sendEmail) {
                        return $this->_sendNotificationEmail($title);
                    } else {
                        return true;
                    }
                }
            }
        }
        // USERNAME && USER ROLE && SOURCE IP
        elseif ($s3 == 'IS NOT') {
            if ($s2 == 'USERNAME') {
                $uid = isset($this->_alertData['CurrentUserID']) ? $this->_alertData['CurrentUserID'] : false;
                if ($uid === false) {
                    $user = get_user_by('login', $i1);
                } else {
                    $user = get_user_by('id', $uid);
                }
                if ($user === false) {
                    return false;
                }
                $value = $user->user_login;
                if ($value != $i1) {
                    if ($sendEmail) {
                        return $this->_sendNotificationEmail($title);
                    } else {
                        return true;
                    }
                }
            } elseif ($s2 == 'USER ROLE') {
                $roleFound = false;
                $roles = $this->_alertData['CurrentUserRoles'];
                foreach ($roles as $role) {
                    if (strcasecmp($i1, $role)==0) {
                        $roleFound = true;
                    }
                }
                if (!$roleFound) {
                    if ($sendEmail) {
                        return $this->_sendNotificationEmail($title);
                    } else {
                        return true;
                    }
                }
            } elseif ($s2 == 'SOURCE IP') {
                $value = $this->_alertData['ClientIP'];
                if ($i1 != $value) {
                    if ($sendEmail) {
                        return $this->_sendNotificationEmail($title);
                    } else {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Send the notification email
     * @param string $title    The Notification title
     * @return bool
     */
    public function _sendNotificationEmail($title = '')
    {
        $date_format = $this->plugin->settings->GetDateFormat();
        $wp_time_format = get_option('time_format');
        $search = array('a', 'T', ' ');
        $replace = array('A', '', '');
        $time_format = str_replace($search, $replace, $wp_time_format);
        $gmt_offset_sec = $this->GetTimezone();

        $date_time_format = $date_format . ' @' . $time_format;

        if (WSAL_DEBUG_NOTIFICATIONS) {
            error_log("WP Security Audit Log Notification");
            error_log("Email address: ".$this->_emailAddress);
            error_log("Alert ID: ".$this->_alertID);
        }
        if (empty($this->_emailAddress)) {
            return false;
        }

        $alert = $this->plugin->alerts->GetAlert($this->_alertID);
        $alertMessage = $alert->GetMessage((array)$this->_alertData);
        $uid = isset($this->_alertData['CurrentUserID']) ? $this->_alertData['CurrentUserID'] : null;
        $username = __('System', 'email-notifications-wsal');
        if (empty($uid)) { // will happen "on login"
            // this will be populated
            if (isset($this->_alertData['Username']) && !empty($this->_alertData['Username'])) {
                $username = $this->_alertData['Username'];
            }
        } else {
            $user = get_user_by('id', $uid);
            if ($user !== false) {
                $username = $user->user_login;
            }
        }

        if ($this->_alertDate) {
            $date = $this->_alertDate;
        } else {
            $date = date($date_time_format, microtime(true) + $gmt_offset_sec);
        }

        $headers = "MIME-Version: 1.0\r\n";
        $builtInSubject = ($this->_isBuiltIn) ? "Built-in Notification" : '';
        $subjectAppend = sprintf(__('%s on website %s Triggered', 'email-notifications-wsal'), $title, get_bloginfo('name'));
        $subject = $builtInSubject . ' ' . $subjectAppend;

        $builtInBody = ($this->_isBuiltIn) ? 'Built-in email notification' : 'Email Notification';
        $content = '<p>'.$builtInBody.sprintf(__(' <strong>%s</strong> was triggered. Below are the notification details:', 'email-notifications-wsal'), $title).'</p>';
        $content .= '<ul>';
        $content .= '<li>'.__('Alert ID', 'email-notifications-wsal').': '.$this->_alertID.'</li>';
        $content .= '<li>'.__('Username', 'email-notifications-wsal').': '.$username.'</li>';
        $_userRoles = isset($this->_alertData['CurrentUserRoles'])  ? $this->_alertData['CurrentUserRoles'] : null;
        $userRole = '';
        if (isset($_userRoles[0]) && !empty($_userRoles[0])) {
            if (count($_userRoles) > 1) {
                $userRole = implode(', ', $_userRoles);
            } else {
                $userRole = $_userRoles[0];
            }
        }
        $content .= '<li>'.__('User role', 'email-notifications-wsal').': '.$userRole.'</li>';
        $content .= '<li>'.__('IP address', 'email-notifications-wsal').': '.$this->_alertData['ClientIP'].'</li>';
        $content .= '<li>'.__('Alert Message', 'email-notifications-wsal').': '.$alertMessage.'</li>';
        $content .= '<li>'.__('Alert generated on', 'email-notifications-wsal').': '.$date.'</li>';
        $content .= '</ul>';
        $content .= '<p>'.__('Monitoring of WordPress and Email Notifications provided by <a href="http://www.wpsecurityauditlog.com">WP Security Audit Log, WordPress most comprehensive audit trail plugin</a>.', 'email-notifications-wsal').'</p>';
        //@see: http://codex.wordpress.org/Function_Reference/wp_mail
        add_filter('wp_mail_content_type', array($this, '_set_html_content_type'));

        add_filter('wp_mail_from', array($this, 'custom_wp_mail_from'));
        add_filter('wp_mail_from_name', array($this, 'custom_wp_mail_from_name'));

        $result = wp_mail($this->_emailAddress, $subject, $content, $headers);
        // Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
        remove_filter('wp_mail_content_type', array($this, '_set_html_content_type'));
        remove_filter('wp_mail_from', array($this, 'custom_wp_mail_from'));
        remove_filter('wp_mail_from_name', array($this, 'custom_wp_mail_from_name'));
        if (WSAL_DEBUG_NOTIFICATIONS) {
            error_log("Email success: ".print_r($result, true));
        }
        return $result;
    }

    private function GetTimezone()
    {
        $gmt_offset_sec = 0;
        $timezone = $this->plugin->settings->GetTimezone();
        if ($timezone) {
            $gmt_offset_sec = get_option('gmt_offset') * HOUR_IN_SECONDS;
        } else {
            $gmt_offset_sec = date('Z');
        }
        return $gmt_offset_sec;
    }

    final public function _set_html_content_type()
    {
        return 'text/html';
    }

    final public function custom_wp_mail_from($original_email_from)
    {
        $email_from = $this->plugin->GetGlobalOption('from-email');
        if (!empty($email_from)) {
            return $email_from;
        } else {
            return $original_email_from;
        }
    }

    final public function custom_wp_mail_from_name($original_email_from_name)
    {
        $email_from_name = $this->plugin->GetGlobalOption('display-name');
        if (!empty($email_from_name)) {
            return $email_from_name;
        } else {
            return $original_email_from_name;
        }
    }
}
