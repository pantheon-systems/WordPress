<?php if(!class_exists('WSAL_User_Management_Plugin')){ exit('You are not allowed to view this page.'); }

/**
 * List plugin view
 */
class WSAL_User_Management_Views extends WSAL_AbstractView
{
    protected $_gmt_offset = 0;

    public function __construct(WpSecurityAuditLog $plugin)
    {
        parent::__construct($plugin);
        add_action('admin_notices', array($this, 'WsalNoticesSecurityPlugin'));
        add_action('network_admin_notices', array($this, 'WsalNoticesSecurityPlugin'));
        add_action('wp_ajax_SessionAutoRefresh', array($this, 'SessionAutoRefresh'));
        $this->RegisterNotice('users-sessions-management-wsal-plugin');
        $this->_gmt_offset = $this->_plugin->usermanagement->common->GetGmtOffset();
    }

    public function WsalNoticesSecurityPlugin()
    {
        if (is_main_site()) {
            $licenseValid = $this->_plugin->licensing->IsLicenseValid('users-sessions-management-wsal.php');
            $class = $this->_plugin->views->FindByClassName('WSAL_Views_Licensing');
            if (false === $class) {
                $class = new WSAL_Views_Licensing($this->_plugin);
            }
            $licensingPageUrl = esc_attr($class->GetUrl());
            if (!$this->IsNoticeDismissed('users-sessions-management-wsal-plugin') && !$licenseValid) {
                ?><div class="updated" data-notice-name="users-sessions-management-wsal-plugin">
                <p><?php _e(sprintf('Remember to <a href="%s">enter your plugin license code</a> for the <strong>User Management Wsal</strong>,
                                to benefit from updates and support.', $licensingPageUrl), 'users-sessions-management-wsal');?>
                    &nbsp;&nbsp;&nbsp;<a href="javascript:;" class="wsal-dismiss-notification"><?php _e('Dismiss this notice', 'users-sessions-management-wsal'); ?></a></p>
                </div><?php
            }
        }
    }

    public function GetTitle()
    {
        return __('Users Sessions & management', 'users-sessions-management-wsal');
    }

    public function GetIcon()
    {
        return 'dashicons-admin-generic';
    }

    public function GetName()
    {
        return __('Users Sessions & management', 'users-sessions-management-wsal');
    }

    public function GetWeight()
    {
        return 12;
    }

    public function Header()
    {
        $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../')));
        wp_enqueue_style('wsal-security-css', $pluginPath.'/css/style.css');
    }

    public function Footer()
    {
        $pluginPath = plugins_url(basename(realpath(dirname(__FILE__).'/../')));
        wp_enqueue_script('wsal-security-js', $pluginPath.'/js/script.js', array('jquery'));
    }

    public function SessionAutoRefresh()
    {
        if (!isset($_REQUEST['sessions_count'])) {
            die('Session count parameter expected.');
        }
        if (!isset($_REQUEST['blog_id'])) {
            die('Session count parameter expected.');
        }

        $old = (int)$_REQUEST['sessions_count'];
        
        $current_blog_id = (int)$_REQUEST['blog_id'];
        $results = $this->_plugin->usermanagement->common->GetAllSessions($current_blog_id);
        $new = count($results);

        if ($old == $new) {
            echo 'false';
        } else {
            echo $new;
        }
        die;
    }

    protected function Save()
    {
        $this->_plugin->usermanagement->common->AddGlobalOption('user-management-allow-multi-sessions', $_POST['MultiSessions']);

        if (isset($_POST["AlertMultiSessions"])) {
            $emails = trim($_POST['AlertMultiSessionsEmails']);
            $this->_plugin->usermanagement->common->SetMultiSessions(1, $emails);
        } else {
            $this->_plugin->usermanagement->common->SetMultiSessions(0);
        }
        if (isset($_POST["AlertBlocked"])) {
            $emails = trim($_POST['AlertBlockedEmails']);
            $this->_plugin->usermanagement->common->SetBlocked(1, $emails);
        } else {
            $this->_plugin->usermanagement->common->SetBlocked(0);
        }
    }

    public function Render()
    {
        if (!$this->_plugin->settings->CurrentUserCan('edit')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'users-sessions-management-wsal'));
        }

        if (!empty($_GET['_wpnonce']) && !empty($_GET['action']) && 'destroy_session' === $_GET['action'] && !empty($_GET['user_id']) && !empty($_GET['token_hash'])) {
            $user_id = absint($_GET['user_id']);
            if (false === wp_verify_nonce($_GET['_wpnonce'], sprintf('destroy_session_nonce-%d', $user_id))) {
                wp_die(__('No sessions.', 'users-sessions-management-wsal'));
            }
            $this->_plugin->usermanagement->common->DestroyUserSession($user_id, $_GET['token_hash']);
        }

        if (isset($_POST['submit'])) {
            try {
                $this->Save();
                ?><div class="updated">
                    <p><?php _e('Settings have been saved.', 'users-sessions-management-wsal'); ?></p>
                </div><?php
            } catch (Exception $ex) {
                ?><div class="error"><p><?php _e('Error: ', 'users-sessions-management-wsal'); ?><?php echo $ex->getMessage(); ?></p></div><?php
            }
        }

        $columns = array(
            'username'   => __('Username', 'users-sessions-management-wsal'),
            'created'    => __('Created', 'users-sessions-management-wsal'),
            'expiration' => __('Expires', 'users-sessions-management-wsal'),
            'ip'         => __('Source IP', 'users-sessions-management-wsal'),
            'alert'      => __('Last Alert', 'users-sessions-management-wsal'),
            'action'     => __('Action', 'users-sessions-management-wsal')
        );

        $current_blog_id = (int)$this->get_view_site_id();

        $results = $this->_plugin->usermanagement->common->GetAllSessions($current_blog_id);
        $sorted  = array();
        $spp     = !empty($_GET['sessions_per_page']) ? absint($_GET['sessions_per_page']) : 10;
        $paged   = !empty($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset  = absint(($paged - 1) * $spp);
        $orderby = !empty($_GET['orderby']) ? $_GET['orderby'] : 'created';
        $order   = !empty($_GET['order']) ? $_GET['order'] : 'desc';

        foreach ($results as $result) {
            if ('ip' === $orderby) {
                $sorted[] = str_replace('.', '', $result[ $orderby ]);
            } else {
                $sorted[] = $result[$orderby];
            }
        }

        if ('asc' == $order) {
            array_multisort($sorted, SORT_ASC, $results);
        } else {
            array_multisort($sorted, SORT_DESC, $results);
        }

        $total_sessions              = count($results);
        $sessions_administrator_role = $this->_plugin->usermanagement->common->CountAdministratorRole($current_blog_id);
        $pages                       = absint(ceil($total_sessions / $spp));

        $results = array_slice($results, $offset, $spp);

        switch ($order) {
            case 'asc':
                $order_flip = 'desc';
                break;
            case 'desc':
                $order_flip = 'asc';
                break;
            default:
                $order_flip = 'desc';
        }

        $users = $this->_plugin->usermanagement->common->GetUsersWithSessions($current_blog_id);

        ob_start();

        $first_link = $base_link  = add_query_arg(
            array(
                'page' => 'wsal-user-management-views',
            ),
            admin_url('admin.php')
        );
        $last_link  = add_query_arg(array('paged' => $pages), $first_link);
        $prev_link  = ($paged > 2) ? add_query_arg(array('paged' => absint($paged - 1), 'sessions_per_page' => $spp), $first_link) : $first_link;
        $next_link  = ($pages > $paged) ? add_query_arg(array('paged' => absint($paged + 1), 'sessions_per_page' => $spp), $first_link) : $last_link;

        $datetimeFormat = $this->_plugin->usermanagement->common->GetDatetimeFormat();
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                SessionAutoRefresh(<?php echo json_encode(array('token' => $total_sessions, 'blog_id' => $current_blog_id)); ?>);
            });
        </script>
        <div class="tablenav-pages">
            <span class="displaying-num"><?php printf(__('%s items', 'users-sessions-management-wsal'), number_format($total_sessions)) ?></span>
            <?php
            if ($pages > 1) { ?>
                <span class="pagination-links">
                    <a class="first-page<?php echo (1 === $paged) ? ' disabled' : null ?>" title="<?php esc_attr_e('Go to the first page') ?>" href="<?php echo esc_url($first_link) ?>">«</a>
                    <a class="prev-page<?php echo (1 === $paged) ? ' disabled' : null ?>" title="<?php esc_attr_e('Go to the previous page') ?>" href="<?php echo esc_url($prev_link) ?>">‹</a>
                    <span class="paging-input">
                        <?php echo absint($paged) ?> <?php _e('of') ?> <span class="total-pages"><?php echo absint($pages) ?></span>
                    </span>
                    <a class="next-page<?php echo ($pages === $paged) ? ' disabled' : null ?>" title="<?php esc_attr_e('Go to the next page') ?>" href="<?php echo esc_url($next_link) ?>">›</a>
                    <a class="last-page<?php echo ($pages === $paged) ? ' disabled' : null ?>" title="<?php esc_attr_e('Go to the last page') ?>" href="<?php echo esc_url($last_link) ?>">»</a>
                </span>
                <?php
            } ?>
        </div>
        <?php
            $pagination = ob_get_clean();
        ?>
        <div class="wrap">
            <h2 id="wsal-tabs" class="nav-tab-wrapper">
                <a href="#tab-list" class="nav-tab"><?php _e('Logged In Users', 'users-sessions-management-wsal');?></a>
                <a href="#tab-rules" class="nav-tab"><?php _e('Logins Management', 'users-sessions-management-wsal');?></a>
            </h2>
            <div class="nav-tabs">
                <div class="wsal-tab" id="tab-list">
                    <p><?php _e('Total number of sessions with Administrator Role: ', 'users-sessions-management-wsal') ?> <strong><?php echo number_format($sessions_administrator_role) ?></strong></p>
                    <form id="sessionsForm" method="post">
                        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
                        <input type="hidden" id="wsal-cbid" name="wsal-cbid" value="<?php echo esc_attr(isset($_REQUEST['wsal-cbid']) ? $_REQUEST['wsal-cbid'] : '0'); ?>" />
                        <div class="tablenav top"> 
                            <?php
                            // show site alerts widget
                            if ($this->is_multisite() && $this->is_main_blog()) {
                                $curr = $this->get_view_site_id();
                                ?>
                                Show:<div class="wsal-ssa">
                                    <?php
                                    if ($this->get_site_count() > 15) { ?>
                                        <?php $curr = $curr ? get_blog_details($curr) : null; ?>
                                        <?php $curr = $curr ? ($curr->blogname . ' (' . $curr->domain . ')') : 'Network-wide Logins'; ?>
                                        <input type="text" value="<?php echo esc_attr($curr); ?>"/>
                                        <?php
                                    } else { ?>
                                        <select onchange="WsalSsasChange(value);">
                                            <option value="0"><?php _e('Network-wide Logins', 'users-sessions-management-wsal'); ?></option>
                                            <?php
                                            foreach ($this->get_sites() as $info) { ?>
                                                <option value="<?php echo $info->blog_id; ?>" <?php if ($info->blog_id == $curr) echo 'selected="selected"';?>>
                                                    <?php echo esc_html($info->blogname) . ' (' . esc_html($info->domain) . ')';?>
                                                </option>
                                                <?php
                                            } ?>
                                        </select>
                                        <?php
                                    } ?>
                                </div><?php
                            } ?>
                            <?php echo $pagination // xss ok ?>
                            <br class="clear">
                        </div>
                        <?php
                        if (empty($results)) { ?>
                            <p>Currently there are no active user sessions on this site.</p>
                            <?php
                        } else { ?>
                            <table class="wp-list-table widefat fixed users">
                                <thead>
                                    <tr>
                                        <?php
                                        foreach ($columns as $slug => $name) { ?>
                                            <?php if ($slug == 'action') { ?>
                                                <th scope="col" class="manage-column column-<?php echo esc_attr($slug) ?>"><span><?php echo esc_html($name) ?></span></th>
                                            <?php } else { ?>
                                                <th scope="col" class="manage-column column-<?php echo esc_attr($slug) ?> <?php echo ($slug === $orderby) ? 'sorted' : 'sortable' ?> <?php echo ($slug === $orderby && $order) ? esc_attr(strtolower($order)) : 'desc' ?>">
                                                    <a href="<?php echo esc_url(add_query_arg(array('orderby' => $slug, 'order' => ($slug === $orderby) ? esc_attr($order_flip) : 'asc' ))) ?>">
                                                        <span><?php echo esc_html($name) ?></span>
                                                        <span class="sorting-indicator"></span>
                                                    </a>
                                                </th>
                                            <?php } ?>
                                        <?php
                                        } ?>
                                    </tr>
                                </thead>
                                <tbody id="the-list">
                                    <?php
                                    $i = 0;
                                    foreach ($results as $result) {
                                        $i++;
                                        $user_id     = absint($result['user_id']);
                                        $edit_link   = add_query_arg(
                                            array(
                                                'wp_http_referer' => urlencode(wp_unslash($_SERVER['REQUEST_URI'])),
                                            ),
                                            self_admin_url(sprintf('user-edit.php?user_id=%d', $user_id))
                                        );
                                        $destroy_link = add_query_arg(
                                            array(
                                                'action'     => 'destroy_session',
                                                'user_id'    => $user_id,
                                                'token_hash' => $result['token_hash'],
                                                '_wpnonce'   => wp_create_nonce(sprintf('destroy_session_nonce-%d', $user_id)),
                                            )
                                        );
                                        $created = str_replace('$$$', substr(number_format(fmod($result['created'] + $this->_gmt_offset, 1), 3), 2), date($datetimeFormat, $result['created'] + $this->_gmt_offset));
                                        $expiration = str_replace('$$$', substr(number_format(fmod($result['expiration'] + $this->_gmt_offset, 1), 3), 2), date($datetimeFormat, $result['expiration'] + $this->_gmt_offset));
                                        $last_alert = $this->_plugin->usermanagement->common->GetLastUserAlert($result['username'], $result['token_hash'], $current_blog_id);
                                        $user = get_user_by('id', $user_id);
                                        ?>
                                        <tr <?php echo ( 0 !== $i % 2 ) ? 'class="alternate"' : '' ?>>
                                            <td class="username column-username" data-colname="Username">
                                                <?php echo get_avatar($user_id, 32) ?>
                                                <a href="<?php echo esc_url($edit_link) ?>" target="_blank">
                                                    <?php echo esc_html($user->display_name) ?>
                                                </a>
                                                <br>
                                                <?php
                                                    echo $this->_plugin->usermanagement->common->GetUserRoles($user_id, $result['role'], $current_blog_id);
                                                ?>
                                                <br><br>
                                                <span><strong>Session ID: </strong><?php echo esc_html($result['token_hash']) ?></span>
                                            </td>
                                            <td class="created column-created" data-colname="Created">
                                                <?php echo $created; ?>
                                            </td>
                                            <td class="expiration column-expiration" data-colname="Expires">
                                                <?php echo $expiration; ?>
                                            </td>
                                            <td class="ip column-ip" data-colname="Source IP">
                                                <a target="_blank" href="http://whatismyipaddress.com/ip/<?php echo $result['ip'] ?>"><?php echo esc_html($result['ip']) ?></a>
                                            </td>
                                            <td class="alert column-alert" data-colname="Last Alert">
                                                <?php echo $last_alert->message; ?>
                                            </td>
                                            <td class="action column-action" data-colname="Action">
                                                <?php
                                                if (wp_get_session_token() != $result['token_hash']) { ?>
                                                    <a href="<?php echo esc_url($destroy_link) ?>" class="button"><?php _e('Destroy Session', 'users-sessions-management-wsal') ?></a>
                                                <?php
                                                } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <?php
                                        foreach ($columns as $slug => $name) { ?>
                                            <?php if ($slug == 'action') { ?>
                                                <th scope="col" class="manage-column column-<?php echo esc_attr($slug) ?>"><span><?php echo esc_html($name) ?></span></th>
                                            <?php } else { ?>
                                                <th scope="col" class="manage-column column-<?php echo esc_attr($slug) ?> <?php echo ($slug === $orderby) ? 'sorted' : 'sortable' ?> <?php echo ($slug === $orderby && $order) ? esc_attr(strtolower($order)) : 'desc' ?>">
                                                    <a href="<?php echo esc_url(add_query_arg(array('orderby' => $slug, 'order' => ($slug === $orderby) ? esc_attr($order_flip) : 'asc' ))) ?>">
                                                        <span><?php echo esc_html($name) ?></span>
                                                        <span class="sorting-indicator"></span>
                                                    </a>
                                                </th>
                                            <?php } ?>
                                        <?php
                                        } ?>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php } ?>
                        <div class="tablenav bottom">
                            <?php echo $pagination // xss ok ?>
                            <br class="clear">
                        </div>
                    </form>
                </div>
                <!-- Tab Logins Management -->
                <div class="wsal-tab" id="tab-rules">
                    <form id="wsal-rules" method="POST" autocomplete="off">
                        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
                        <table class="form-table widefat">
                            <tbody>
                                <tr>
                                    <th><label for="allow"><?php _e('Multiple sessions with the same user', 'users-sessions-management-wsal'); ?></label></th>
                                    <td>
                                        <fieldset>
                                            <?php $is_allow = $this->_plugin->usermanagement->common->GetOptionByName('user-management-allow-multi-sessions'); ?>
                                            <label for="allow">
                                                <input type="radio" name="MultiSessions" id="allow" style="margin-top: 2px;" <?=(!$is_allow) ? 'checked="checked"' : ''; ?> value="0">
                                                <span><?php _e('Allow', 'users-sessions-management-wsal'); ?></span>
                                            </label>
                                            <br/>
                                            <label for="block">
                                                <input type="radio" name="MultiSessions" id="block" style="margin-top: 2px;" <?=($is_allow) ? 'checked="checked"' : ''; ?> value="1">
                                                <span><?php _e('Block', 'users-sessions-management-wsal'); ?></span>
                                            </label>
                                            <br/>
                                            <span class="description"><?php _e('By allowing multiple sessions two or more people can login to WordPress using the same username. By blocking them, once a person is logged in with a username, if another person tries to login with the 
            same username they will be blocked.', 'users-sessions-management-wsal'); ?></span>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="AlertMultiSessions"><?php _e('Alert the following email addresses when there are multiple sessions with the same user', 'users-sessions-management-wsal'); ?></label></th>
                                    <td>
                                        <fieldset>
                                            <?php $multiSessions = $this->_plugin->usermanagement->common->GetMultiSessions(); ?>
                                            <label for="AlertMultiSessions">
                                                <input type="checkbox" name="AlertMultiSessions" value="1" id="AlertMultiSessions" <?=!empty($multiSessions->status) ? ' checked="checked"' : ''; ?>> <?php _e('Alert the following users', 'users-sessions-management-wsal'); ?>
                                            </label>
                                            <br/>
                                            <?php $multiSessionsEmails = !empty($multiSessions->emails) ?  $multiSessions->emails : ''; ?>
                                            <input type="text" class="emailsAlert" id="AlertMultiSessionsEmails" name="AlertMultiSessionsEmails" value="<?=$multiSessionsEmails?>" style="display: block; width: 250px;" placeholder="Email *">
                                            <span class="description"><?php _e('Should you allow multiple same user sessions, and multiple people log in with the same WordPress user, an email alert is sent to the specified email addresses.', 'users-sessions-management-wsal'); ?></span>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="AlertBlocked"><?php _e('Alert the following email addresses when a user session is blocked', 'users-sessions-management-wsal'); ?></label></th>
                                    <td>
                                        <fieldset>
                                            <?php $blocked = $this->_plugin->usermanagement->common->GetBlocked(); ?>
                                            <label for="AlertBlocked">
                                                <input type="checkbox" name="AlertBlocked" value="1" id="AlertBlocked" <?=!empty($blocked->status) ? ' checked="checked"' : ''; ?>> <?php _e('Alert the following users', 'wp-security-audit-log'); ?>
                                            </label>
                                            <br/>
                                            <?php $blockedEmails = !empty($blocked->emails) ?  $blocked->emails : ''; ?>
                                            <input type="text" class="emailsAlert" id="AlertBlockedEmails" name="AlertBlockedEmails" value="<?=$blockedEmails?>" style="display: block; width: 250px;" placeholder="Email *">
                                            <span class="description"><?php _e('Should you deny multiple same user sessions, when a user login with same username is blocked, an email alert is sent to the specified email addresses.', 'users-sessions-management-wsal'); ?></span>
                                        </fieldset>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit">
                            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * @param int|null $limit Maximum number of sites to return (null = no limit).
     * @return object Object with keys: blog_id, blogname, domain
     */
    public function get_sites($limit = null)
    {
        global $wpdb;
        
        $sql = 'SELECT blog_id, domain FROM ' . $wpdb->blogs;
        if (!is_null($limit)) {
            $sql .= ' LIMIT ' . $limit;
        }
        $res = $wpdb->get_results($sql);
        foreach ($res as $row) {
            $row->blogname = get_blog_option($row->blog_id, 'blogname');
        }
        return $res;
    }

    /**
     * @return int The number of sites on the network.
     */
    public function get_site_count()
    {
        global $wpdb;
        $sql = 'SELECT COUNT(*) FROM ' . $wpdb->blogs;
        return (int)$wpdb->get_var($sql);
    }

    protected function is_multisite()
    {
        return $this->_plugin->IsMultisite();
    }
    
    protected function is_main_blog()
    {
        return get_current_blog_id() == 1;
    }

    protected function is_specific_view()
    {
        return isset($_REQUEST['wsal-cbid']) && $_REQUEST['wsal-cbid'] != '0';
    }
    
    protected function get_specific_view()
    {
        return isset($_REQUEST['wsal-cbid']) ? (int)$_REQUEST['wsal-cbid'] : 0;
    }
    
    protected function get_view_site_id()
    {
        switch (true) {
            // non-multisite
            case !$this->is_multisite():
                return 0;
            // multisite + main site view
            case $this->is_main_blog() && !$this->is_specific_view():
                return 0;
            // multisite + switched site view
            case $this->is_main_blog() && $this->is_specific_view():
                return $this->get_specific_view();
            // multisite + local site view
            default:
                return get_current_blog_id();
        }
    }
}
