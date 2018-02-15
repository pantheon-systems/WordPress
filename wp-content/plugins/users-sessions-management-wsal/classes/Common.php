<?php

class WSAL_User_Management_Common
{
    public $wsal = null;

    public function __construct(WpSecurityAuditLog $wsal)
    {
        $this->wsal = $wsal;
    }

    public function AddGlobalOption($option, $value)
    {
        $this->wsal->SetGlobalOption($option, $value);
    }

    public function DeleteGlobalOption($option)
    {
        return $this->wsal->DeleteByName($option);
    }

    public function GetOptionByName($option, $default = 0)
    {
        return $this->wsal->GetGlobalOption($option, $default);
    }

    public function SetMultiSessions($status, $emails = null)
    {
        $opt_name = "user-management-multi-sessions-notify";
        $this->SaveNotify($opt_name, $status, $emails);
    }

    public function SetBlocked($status, $emails = null)
    {
        $opt_name = "user-management-blocked-notify";
        $this->SaveNotify($opt_name, $status, $emails);
    }

    public function GetMultiSessions()
    {
        $opt_name = "user-management-multi-sessions-notify";
        $result = $this->GetOptionByName($opt_name);
        return $result;
    }

    public function GetBlocked()
    {
        $opt_name = "user-management-blocked-notify";
        $result = $this->GetOptionByName($opt_name);
        return $result;
    }

    public function GetUserRoles($user_id, $blog_role, $userblog_id)
    {
        $userRoles = array();

        if (is_multisite() && is_super_admin($user_id)) {
            $userRoles[] = 'Superadmin';
        }

        if (!empty($blog_role)) {
            $userRoles[] = ucwords($blog_role);
        } else {
            $theuser = new WP_User($user_id, $userblog_id);
            if (!empty($theuser->roles) && is_array($theuser->roles)) {
                foreach ($theuser->roles as $role) {
                    $userRoles[] = ucwords($role);
                }
            }
        }

        if (empty($userRoles)) {
            $userRoles[] = '<i>N/A</i>';
        }
        return implode(", ", array_unique($userRoles));
    }

    /**
     * Alerts Timestamp
     * Server's timezone or WordPress' timezone
     */
    public function GetGmtOffset()
    {
        $timezone = $this->wsal->settings->GetTimezone();
        $gmt_offset = 0;
        if ($timezone) {
            $gmt_offset = get_option('gmt_offset') * HOUR_IN_SECONDS;
        } else {
            $gmt_offset = date('Z');
        }
        return $gmt_offset;
    }

    /**
     * Datetime used in the Alerts.
     */
    public function GetDatetimeFormat() {
        return $this->wsal->settings->GetDatetimeFormat();
    }

    /**
     * Date Format from WordPress General Settings.
     */
    public function GetDateFormat() {
        return $this->wsal->settings->GetDateFormat();
    }

    /**
     * Time Format from WordPress General Settings.
     */
    public function GetTimeFormat() {
        return $this->wsal->settings->GetTimeFormat();
    }

    public function CountSessionsByUser($user_id)
    {
        $session_tokens = get_user_meta($user_id, 'session_tokens', true);
        if (!is_array($session_tokens) && is_string($session_tokens)) {
            $session_tokens = maybe_unserialize($session_tokens);
        }
        return count($session_tokens);
    }

    /**
     * Get all users with active sessions
     * @param int $blog_id
     * @return WP_User_Query
     */
    public function GetUsersWithSessions($blog_id = 0)
    {
        $args = array(
            'number'     => 9999,
            'blog_id'    => $blog_id,
            'meta_query' => array(
                array(
                    'key'     => 'session_tokens',
                    'compare' => 'EXISTS',
                ),
            ),
        );
        $users = new WP_User_Query($args);
        return $users;
    }

    /**
     * Get all raw session meta from all users
     * @return array
     */
    public function GetAllSessionsRaw()
    {
        global $wpdb;

        $results  = array();
        $sessions = $wpdb->get_results("SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = 'session_tokens' LIMIT 0, 9999");
        $sessions = wp_list_pluck($sessions, 'meta_value');
        $sessions = array_map('maybe_unserialize', $sessions);

        foreach ($sessions as $session) {
            if (!is_array($session) && is_string($session)) {
                $session = maybe_unserialize($session);
            }
            if (is_array($session)) {
                $results = array_merge($results, $session);
            }
        }
        return (array) $results;
    }

    /**
     * Get all sessions from all users
     * @param int $blog_id
     * @return array
     */
    public function GetAllSessions($blog_id = 0)
    {
        $results  = array();
        $users    = $this->GetUsersWithSessions($blog_id)->get_results();
        $sessions = $this->GetAllSessionsRaw();
        foreach ($users as $user) {
            $user_sessions = get_user_meta($user->ID, 'session_tokens', true);
            if (!is_array($user_sessions) && is_string($user_sessions)) {
                $user_sessions = maybe_unserialize($user_sessions);
            }
            foreach ($sessions as $session) {
                if (is_array($user_sessions)) {
                    foreach ($user_sessions as $token_hash => $user_session) {
                        // Loose comparison needed
                        if ($user_session == $session) {
                            $results[] = array(
                                'user_id'    => $user->ID,
                                'username'   => $user->user_login,
                                'name'       => $user->display_name,
                                'email'      => $user->user_email,
                                'role'       => !empty($user->roles[0]) ? $user->roles[0] : '',
                                'blog_id'    => $blog_id,
                                'created'    => $user_session['login'],
                                'expiration' => $user_session['expiration'],
                                'ip'         => $user_session['ip'],
                                'user_agent' => $user_session['ua'],
                                'token_hash' => $token_hash,
                            );
                        }
                    }
                }
            }
        }
        return array_unique($results, SORT_REGULAR);
    }

    /**
     * Destroy a specfic session for a specfic user
     * @param int     $user_id
     * @param string  $token_hash
     * @return void
     */
    public function DestroyUserSession($user_id, $token_hash)
    {
        $session_tokens = get_user_meta($user_id, 'session_tokens', true);
        if (!is_array($session_tokens) && is_string($session_tokens)) {
            $session_tokens = maybe_unserialize($session_tokens);
        }
        if (isset($session_tokens[$token_hash])) {
            unset($session_tokens[$token_hash]);
        }

        if (empty($session_tokens)) {
            delete_user_meta($user_id, 'session_tokens');
        } else {
            update_user_meta($user_id, 'session_tokens', $session_tokens);
        }
    }

    public function GetLastUserAlert($value, $session, $blog_id = 0)
    {
        $lastAlert = null;

        $userId = get_user_by('login', $value);
        $userId = $userId ? $userId->ID : -1;
        if ($userId == -1) {
            $userId = get_user_by('slug', $value);
            $userId = $userId ? $userId->ID : -1;
        }

        $query = new WSAL_Models_OccurrenceQuery();
        $query->addMetaJoin();
        $query->addCondition(
            '( meta.name = "SessionID" AND TRIM(BOTH "\"" FROM meta.value) = TRIM(BOTH "\"" FROM %s) )',
            json_encode($session)
        );
        if ($blog_id) {
            $query->addCondition("site_id = %s ", $blog_id);
        }
        $result = $this->ExcuteQuery($query);

        if (empty($result)) {
            $query = new WSAL_Models_OccurrenceQuery();
            $query->addMetaJoin();
            $query->addORCondition(
                array(
                    '( meta.name = "Username" AND TRIM(BOTH "\"" FROM meta.value) = TRIM(BOTH "\"" FROM %s) ) ' => json_encode($value),
                    '( meta.name = "CurrentUserID" AND TRIM(BOTH "\"" FROM meta.value) = TRIM(BOTH "\"" FROM %s) )' => json_encode($userId)
                )
            );
            if ($blog_id) {
                $query->addCondition("site_id = %s ", $blog_id);
            }
            $result = $this->ExcuteQuery($query);
        }

        if (!empty($result)) {
            $lastAlert = $result[0];
            $lastAlert->message = $lastAlert->GetMessage(array($this, 'meta_formatter'));
        } else {
            $lastAlert = new stdClass();
            $lastAlert->message = "No activity";
        }

        return $lastAlert;
    }

    private function ExcuteQuery($query)
    {
        $query->addOrderBy("created_on", true);
        $query->setLimit(1);
        return $query->getAdapter()->Execute($query);
    }

    public function AlertByEmail($type, $result, $user)
    {
        error_log("WP Users Management Sessions Alert");
        $url = $this->GetPageUrl();
        $timestamp = time() + $this->GetGmtOffset();
        $current_date = date($this->GetDateFormat(), $timestamp);
        $current_time = date($this->GetTimeFormat(), $timestamp);
        $current_ip = $this->wsal->settings->GetMainClientIP();
        $site_url = get_site_url();

        $headers = "MIME-Version: 1.0\r\n";

        switch ($type) {
            case 'multiple':
                $subject = sprintf(__('Multiple Same Users Sessions Alert on %s', 'users-sessions-management-wsal'), get_bloginfo('name'));
                $content = '<p>Two or more people are logged in to WordPress at ' . $site_url . ' with the username <strong>' . $user->display_name . '</strong>. Here are the session details:</p>';
                $content .= $this->GetSessionsByUserId($user->ID, $type);
                break;
            
            case 'blocked':
                $subject = sprintf(__('User Login Attempt Blocked on %s', 'users-sessions-management-wsal'), get_bloginfo('name'));
                $content = '<p>Someone tried to login to the WordPress at ' . $site_url . ' with the username <strong>' . $user->display_name . '</strong>. Since there was already an existing session with that user this login was blocked.</p>';
                $content .= $this->GetSessionsByUserId($user->ID, $type);
                $content .= '<p><strong>Blocked Session:</strong><br>Login attempted on: ' . $current_date . ' ' . $current_time . '<br>Source IP of login attempt: ' . $current_ip . '</p>';
                break;
        }
        $content .= '<p>' . sprintf(__('Click <a href="%s">here</a> to login to your WordPress and see all the logged in sessions and terminate any of them.', 'users-sessions-management-wsal'), $url) . '</p>';
        add_filter('wp_mail_content_type', array($this, '_set_html_content_type'));

        add_filter('wp_mail_from', array($this, 'custom_wp_mail_from'));
        add_filter('wp_mail_from_name', array($this, 'custom_wp_mail_from_name'));

        $res = wp_mail($result->emails, $subject, $content, $headers);

        remove_filter('wp_mail_content_type', array($this, '_set_html_content_type'));
        remove_filter('wp_mail_from', array($this, 'custom_wp_mail_from'));
        remove_filter('wp_mail_from_name', array($this, 'custom_wp_mail_from_name'));
        error_log("Email success: ".print_r($res, true));
    }

    /**
     * Get sessions from Administrator users role
     * @param int $blog_id
     * @return array
     */
    public function CountAdministratorRole($blog_id = 0)
    {
        $aRoles = array();
        $results = $this->GetAllSessions($blog_id);
        foreach ($results as $result) {
            if ($result['role'] == 'administrator') {
                $aRoles[] = $result;
            }
        }
        return count($aRoles);
    }

    /**
     * Check Cookie Login data
     * @param string $username
     * @return bool
     */
    public function CheckLoggedInCookie($username)
    {
        $site_id = $this->GetCurrentSiteId();
        if (isset($_COOKIE['wordpress_known_user_cookie'])) {
            $cookieArr = explode('|', $_COOKIE['wordpress_known_user_cookie']);
            $cookie_login = $cookieArr[0];
            $cookie_site_id = $cookieArr[1];
            $cookie_hash = $cookieArr[2];

            if ($cookie_login == $username && $cookie_site_id == $site_id) {
                $current_user = get_user_by('login', $cookie_login);

                $session_tokens = get_user_meta($current_user->ID, 'session_tokens', true);
                if (!is_array($session_tokens) && is_string($session_tokens)) {
                    $session_tokens = maybe_unserialize($session_tokens);
                }
                foreach ($session_tokens as $hash_key => $session_token) {
                    if ($cookie_hash == $hash_key) {
                        $this->DestroyUserSession($current_user->ID, $cookie_hash);
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Set custom cookie for recognize known users
     * @param int $user_id
     */
    public function setCustomCookie($user_id)
    {
        $site_id = $this->GetCurrentSiteId();
        $session_tokens = get_user_meta($user_id, 'session_tokens', true);
        if (!is_array($session_tokens) && is_string($session_tokens)) {
            $session_tokens = maybe_unserialize($session_tokens);
        }
        $current_user = get_user_by('id', $user_id);
        $expiration = 0;
        $token = '';
        $secure = is_ssl();
        foreach ($session_tokens as $token_hash => $session_token) {
            if ($expiration < $session_token['expiration']) {
                $expiration = $session_token['expiration'];
                $token = $token_hash;
            }
        }

        $logged_in_cookie = $current_user->user_login . '|' . $site_id . '|' . $token;
        
        setcookie('wordpress_known_user_cookie', $logged_in_cookie, $expiration, COOKIEPATH, COOKIE_DOMAIN, $secure, true);
        if (COOKIEPATH != SITECOOKIEPATH) {
            setcookie('wordpress_known_user_cookie', $logged_in_cookie, $expiration, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true);
        }
    }

    private function SaveNotify($opt_name, $status, $emails)
    {
        if ($status == 1) {
            $data = new stdClass();
            $data->status = $status;
            $data->emails = $emails;
            $result = $this->AddGlobalOption($opt_name, $data);
        } else {
            $this->DeleteGlobalOption("wsal-".$opt_name);
        }
    }

    private function GetPageUrl()
    {
        $class = $this->wsal->views->FindByClassName('WSAL_User_Management_Views');
        if (false === $class) {
            $class = new WSAL_User_Management_Views($this->wsal);
        }
        return esc_attr($class->GetUrl());
    }

    private function GetSessionsByUserId($user_id, $type)
    {
        $session_tokens = get_user_meta($user_id, 'session_tokens', true);
        $content = '';
        if (!empty($session_tokens)) {
            if (!is_array($session_tokens) && is_string($session_tokens)) {
                $session_tokens = maybe_unserialize($session_tokens);
            }
            if ($type == 'multiple') {
                $content = '<ul style="padding:0;">';
            } else {
                $content = '<p>';
            }
            foreach ($session_tokens as $key => $session) {
                $offset = $this->GetGmtOffset();
                $date = date($this->GetDateFormat(), $session['login'] + $offset);
                $time = date($this->GetTimeFormat(), $session['login'] + $offset);
                if ($type == 'multiple') {
                    $content .= '<li><p>Session ID: ' . $key . '<br>Date: ' . $date . '<br>Time: ' . $time . '<br>Source IP: ' . $session['ip'] . '</p></li>';
                } else {
                    $content .= '<strong>Existing session:</strong><br>Session ID: ' . $key . '<br>Date Created: ' . $date . '<br>Time Created: ' . $time . '<br>Source IP: ' . $session['ip'] . '<br>';
                }
            }
            if ($type == 'multiple') {
                $content .= '</ul>';
            } else {
                $content .= '</p>';
            }
        }
        return $content;
    }

    public function GetSessionIPs($user_id)
    {
        $ip_addresses = array();
        $session_tokens = get_user_meta($user_id, 'session_tokens', true);
        if (!empty($session_tokens)) {
            if (!is_array($session_tokens) && is_string($session_tokens)) {
                $session_tokens = maybe_unserialize($session_tokens);
            }
            foreach ($session_tokens as $key => $session) {
                array_push($ip_addresses, $session['ip']);
            }
        }
        $ip_addresses = array_unique($ip_addresses);
        return implode(", ", $ip_addresses);
    }

    final public function _set_html_content_type()
    {
        return 'text/html';
    }

    final public function custom_wp_mail_from($original_email_from)
    {
        $email_from = $this->GetOptionByName('from-email');
        if (!empty($email_from)) {
            return $email_from;
        } else {
            return $original_email_from;
        }
    }

    final public function custom_wp_mail_from_name($original_email_from_name)
    {
        $email_from_name = $this->GetOptionByName('display-name');
        if (!empty($email_from_name)) {
            return $email_from_name;
        } else {
            return $original_email_from_name;
        }
    }

    public function meta_formatter($name, $value)
    {
        switch (true) {
            case $name == '%Message%':
                return esc_html($value);

            case $name == '%RevisionLink%':
                if (!empty($value) && $value != 'NULL') {
                    return '<br>Click <a target="_blank" href="'.$value.'">here</a> to see the content changes.';
                } else {
                    return "";
                }

            case $name == '%CommentLink%':
            case $name == '%CommentMsg%':
                return $value;

            case $name == '%EditorLinkPost%':
                return ' <a target="_blank" href="'.esc_url($value).'">View the post</a>';
                
            case $name == '%EditorLinkPage%':
                return ' <a target="_blank" href="'.esc_url($value).'">View the page</a>';
                
            case $name == '%CategoryLink%':
                return ' <a target="_blank" href="'.esc_url($value).'">View the category</a>';

            case $name == '%EditorLinkForum%':
                return ' <a target="_blank" href="'.esc_url($value).'">View the forum</a>';
                
            case $name == '%EditorLinkTopic%':
                return ' <a target="_blank" href="'.esc_url($value).'">View the topic</a>';

                // Meta value
            case in_array($name, array('%MetaValue%', '%MetaValueOld%', '%MetaValueNew%')):
                return '<strong>' . (
                    strlen($value) > 50 ? (esc_html(substr($value, 0, 50)) . '&hellip;') :  esc_html($value)
                ) . '</strong>';

            case $name == '%ClientIP%':
                if (is_string($value)) {
                    return '<strong>' . str_replace(array("\"", "[", "]"), "", $value) . '</strong>';
                } else {
                    return '<i>unknown</i>';
                }
                // Link
            case strncmp($value, 'http://', 7) === 0:
            case strncmp($value, 'https://', 7) === 0:
                return '<a href="' . esc_html($value) . '"'
                    . ' title="' . esc_html($value) . '"'
                    . ' target="_blank">'
                        . esc_html(parse_url($value, PHP_URL_HOST)) . '/&hellip;/'
                        . esc_html(basename(parse_url($value, PHP_URL_PATH)))
                    . '</a>';
            default:
                return '<strong>' . esc_html($value) . '</strong>';
        }
    }

    /**
     * Get the current site_id
     * @return int $site_id
     */
    public function GetCurrentSiteId()
    {
        $site_id = (function_exists('get_current_blog_id') ? get_current_blog_id() : 0);
        return $site_id;
    }
}
