<?php

/*
  WPFront User Role Editor Plugin
  Copyright (C) 2014, WPFront.com
  Website: wpfront.com
  Contact: syam@wpfront.com

  WPFront User Role Editor Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Go_Pro')) {

    /**
     * Go Pro
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Go_Pro extends WPFront_User_Role_Editor_Controller_Base {

        const MENU_SLUG = 'wpfront-user-role-editor-go-pro';

        private static $go_pro_html_url = 'https://wpfront.com/syam/wordpress-plugins/wpfront-user-role-editor/pro/comparison/';
        private static $store_url = 'https://edd.wpfront.com/';
        private $pro_html = '';
        private $has_license = FALSE;
        private $need_license = FALSE;
        private $license_status = NULL;
        private $license_key = NULL;
        private $license_key_k = NULL;
        private $license_expires = NULL;
        private $download_id = NULL;
        private $license_expired = FALSE;
        private $product = NULL;
        private $slug = NULL;
        private $error = NULL;
        private $plugin_updater = NULL;
        private $mail_objects = array();
        private $renew_url = NULL;
        private $license_home_url = NULL;

        public function __construct($main) {
            parent::__construct($main);

            $this->ajax_register('wp_ajax_wpfront_user_role_editor_license_functions', array($this, 'license_functions'));
            add_action('shutdown', array($this, 'plugins_loaded'));
        }

        public function go_pro() {
            $this->main->verify_nonce();

            if (!current_user_can('manage_options')) {
                $this->main->permission_denied();
                return;
            }

            if (!empty($_POST['license_key']) && !empty($_POST['activate'])) {
                $this->activate_license($_POST['license_key']);
            }

            if (!empty($_POST['deactivate'])) {
                $this->deactivate_license();
            }

            $options = new WPFront_User_Role_Editor_Entity_Options();

            $time_key = self::MENU_SLUG . '-html-last-update';
            $html_key = self::MENU_SLUG . '-html';

            $time = $options->get_option($time_key);

            if ($time === NULL || $time < time() - 24 * 3600) {
                $options->update_option($time_key, time());
                $result = WPFront_User_Role_Editor::wp_remote_get(self::$go_pro_html_url);
                if (!is_wp_error($result) && wp_remote_retrieve_response_code($result) == 200) {
                    $this->pro_html = wp_remote_retrieve_body($result);
                    $options->update_option($html_key, $this->pro_html);
                }
            }

            if ($this->pro_html === '') {
                $key = self::MENU_SLUG . '-html';
                $this->pro_html = $options->get_option($key);
                if ($this->pro_html === NULL)
                    $this->pro_html = '';
            }

            if ($this->pro_html === '') {
                $this->pro_html = file_get_contents($this->main->pluginDIR() . 'templates/go-pro-table');
            }

            include($this->main->pluginDIR() . 'templates/go-pro.php');
        }

        public function set_license($key = NULL, $product = NULL) {
            if ($key === NULL && $this->license_key_k === NULL)
                return;

            if ($key !== NULL) {
                $this->slug = $key;
                $this->need_license = TRUE;
                $this->license_key_k = $key . '-license-key';
                $this->product = $product;
            }

            if (is_multisite()) {
                $options = new WPFront_User_Role_Editor_Options($this->main);
                switch_to_blog($options->ms_options_blog_id());
            }

            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $this->license_key = $entity->get_option($this->license_key_k);
            if ($this->license_key !== NULL) {
                $invalid_count = $entity->get_option($this->license_key_k . '-invalid-count');
                if (empty($invalid_count)) {
                    $invalid_count = 0;
                }

                $last_checked = $entity->get_option($this->license_key_k . '-last-checked');
                if (($invalid_count > 0 || is_admin()) && !WPFront_Static_URE::doing_ajax() && $last_checked < time() - 24 * 3600) {
                    $entity->update_option($this->license_key_k . '-last-checked', time());
                    $result = $this->remote_get('check_license', $this->license_key);
                    $api_params = $result[1];
                    $result = $result[0];
                    if (!empty($result)) {
                        if (($result->activations_left === 'unlimited' || $result->activations_left >= 0) && ($result->license === 'valid' || $result->license === 'expired')) {
                            $entity->update_option($this->license_key_k . '-status', $result->license);
                            $entity->update_option($this->license_key_k . '-expires', $result->expires);
                            $entity->update_option($this->license_key_k . '-download-id', $result->download_id);
                            $entity->update_option($this->license_key_k . '-invalid-count', 0);
                        } else {
                            $invalid_count = $invalid_count + 1;
                            $entity->update_option($this->license_key_k . '-invalid-count', $invalid_count);
                            $entity->update_option($this->license_key_k . '-home-url', $api_params['url']);
                            $this->license_status = 'invalid';
                            if ($invalid_count === 1) {
                                $this->send_mail('invalid', $result, 'wpfront.com', $api_params);
                            } else if ($invalid_count > 7) {
                                $this->deactivate_license(TRUE);
                                $this->send_mail('deactivated', $result, 'wpfront.com');
                                return;
                            }
                        }
                    }
                }
                
                $this->has_license = TRUE;
                $this->license_expired = $entity->get_option($this->license_key_k . '-status') === 'expired';
                $this->license_expires = date('F d, Y', strtotime($entity->get_option($this->license_key_k . '-expires')));
                $this->download_id = $entity->get_option($this->license_key_k . '-download-id');
                if (!empty($this->download_id)) {
                    $this->renew_url = sprintf('https://wpfront.com/checkout/?edd_license_key=%s&download_id=%d', $this->license_key, $this->download_id);
                }

                //$invalid_count = $entity->get_option($this->license_key_k . '-invalid-count');
                if ($invalid_count > 0) {
                    $this->license_status = 'invalid';
                    $this->license_home_url = $entity->get_option($this->license_key_k . '-home-url');
                }

                if ($this->license_status === NULL) {
                    if ($this->license_expired) {
                        $this->license_status = 'expired';
                    } else {
                        $this->license_status = 'valid';
                    }
                }

                $this->license_key = str_repeat('X', strlen($this->license_key) - 4) . substr($this->license_key, strlen($this->license_key) - 4, 4);

                add_action('admin_notices', array($this, 'license_admin_notice'));
                
                //Software licensing change
                $this->edd_plugin_update();
                //add_action('admin_init', array($this, 'edd_plugin_update'));
            } else {
                $this->license_key = '';
                $this->has_license = FALSE;
                $this->license_expires = NULL;
            }

            if (is_multisite()) {
                restore_current_blog();
            }
        }

        private function activate_license($license) {
            if ($this->license_key_k === NULL)
                return;
            
            $license = trim($license);

            $this->license_key = $license;

            $result = $this->remote_get('activate_license', $license);
            if ($result === NULL)
                return;

            $result = $result[0];

            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $entity->delete_option($this->license_key_k);
            $entity->delete_option($this->license_key_k . '-expires');
            $entity->delete_option($this->license_key_k . '-last-checked');

            if ($result->license === 'valid' || $result->error === 'expired') {
                $entity->update_option($this->license_key_k, $license);
                $entity->update_option($this->license_key_k . '-status', $result->license === 'valid' ? 'valid' : 'expired');
                $entity->update_option($this->license_key_k . '-expires', $result->expires);
                $entity->update_option($this->license_key_k . '-last-checked', 0);
                $entity->update_option($this->license_key_k . '-invalid-count', 0);

                $this->send_mail('activate', $result, 'user');
                $this->set_license();
            } elseif ($result->error === 'no_activations_left') {
                $this->error = $this->__('ERROR') . ': ' . $this->__('License key activation limit reached.') . ' ' . sprintf('<a href="%s" target="_blank">%s</a>', 'https://wpfront.com/user-role-editor-pro/faq/#activation-limit', $this->__('More information'));
            } else {
                $this->error = $this->__('ERROR') . ': ' . $this->__('Invalid license key');
            }
        }

        private function deactivate_license($forced = FALSE) {
            if ($this->license_key_k === NULL)
                return;

            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $this->license_key = $entity->get_option($this->license_key_k);

            if (empty($this->license_key))
                return;

            $result = $this->remote_get('deactivate_license', $this->license_key);
            if ($result === NULL)
                return;

            $result = $result[0];

            if ($result->license === 'deactivated' || $forced) {
                $entity->delete_option($this->license_key_k);
                $entity->delete_option($this->license_key_k . '-expires');
                $entity->delete_option($this->license_key_k . '-last-checked');

                if (!$forced) {
                    $this->send_mail('deactivate', $result, 'user');
                }
            } else {
                $this->error = $this->__('ERROR') . ': ' . $this->__('Unable to deactivate');
            }

            $this->set_license();
        }

        private function recheck_license() {
            $entity = new WPFront_User_Role_Editor_Entity_Options();
            $entity->update_option($this->license_key_k . '-last-checked', 0);
            $this->plugin_updater->recheck();
        }

        private function remote_get($action, $license) {
            if ($this->product === NULL)
                return NULL;

            $url = home_url();
            $url = explode('?', $url);
            $url = $url[0];
            
            $api_params = array(
                'edd_action' => $action,
                'license' => urlencode($license),
                'item_name' => urlencode($this->product),
                'url' => urlencode($url),
                'plugin_version' => WPFront_User_Role_Editor::VERSION
            );

            $response = WPFront_User_Role_Editor::wp_remote_get(self::$store_url, array('body' => $api_params));
            if (is_wp_error($response)) {
                $this->error = $this->__('ERROR') . ': ' . $this->__('Unable to contact wpfront.com')
                        . '<br />'
                        . $this->__('Details') . ': ' . $response->get_error_message();
                return NULL;
            }

            $result = json_decode(wp_remote_retrieve_body($response));

            if (!is_object($result)) {
                $this->error = $this->__('ERROR') . ': ' . $this->__('Unable to parse response');
                return NULL;
            }

            $api_params['license'] = urldecode($api_params['license']);
            $api_params['item_name'] = urldecode($api_params['item_name']);
            $api_params['url'] = urldecode($api_params['url']);

            return array($result, $api_params);
        }

        public function edd_plugin_update() {
            $entity = new WPFront_User_Role_Editor_Entity_Options();

            $this->plugin_updater = new WPFront_User_Role_Editor_Plugin_Updater(self::$store_url, WPFRONT_USER_ROLE_EDITOR_PLUGIN_FILE, array(
                'version' => WPFront_User_Role_Editor::VERSION,
                'license' => $entity->get_option($this->license_key_k),
                'item_name' => $this->product,
                'author' => 'Syam Mohan'
                    ), $this->slug);
        }

        public function license_functions() {
            if (!wp_verify_nonce($_POST['_wpnonce'], $_POST['_wp_http_referer'])) {
                echo 'true';
                die();
            }

            if (!current_user_can('manage_options')) {
                echo 'true';
                die();
            }

            if (!empty($_POST['license_key']) && !empty($_POST['activate'])) {
                $this->activate_license($_POST['license_key']);
            }

            if (!empty($_POST['deactivate'])) {
                $this->deactivate_license();
            }

            if (!empty($_POST['recheck'])) {
                $this->recheck_license();
            }

            if ($this->error === NULL)
                echo 'true';
            else
                echo 'false';
            die();
        }

        public function has_license() {
            if ($this->need_license)
                return $this->has_license;

            return TRUE;
        }

        private function send_mail($action, $result, $source, $params = NULL) {
            $admin_email = get_site_option('admin_email');
            $blog_name = is_multisite() ? get_site_option('site_name') : get_option('blogname');

            if (function_exists('wp_get_current_user'))
                $current_user = wp_get_current_user();

            $to = array($admin_email);
            if (!empty($result) && !empty($result->customer_email) && $to[0] !== $result->customer_email) {
                $to[] = $result->customer_email;
            }

            $body = '<tr><td>' . $this->__('Site') . ':</td><td>' . get_site_option('siteurl') . '</td></tr>';
            $body .= '<tr><td>' . $this->__('Product') . ':</td><td>' . $this->__($this->product) . '</td></tr>';

            switch ($action) {
                case 'activate':
                    $message = $this->__('Your WPFront User Role Editor Pro license was activated on the following site.');
                    $subject = '[' . $blog_name . '] ' . $this->__('WPFront User Role Editor Pro License Activated');
                    if ($source === 'user') {
                        $body .= '<tr><td>' . $this->__('Activated By') . ':</td><td>' . $current_user->user_firstname . ' ' . $current_user->user_lastname . ' [' . $current_user->user_login . ']' . '</td></tr>';
                        $body .= '<tr><td>' . $this->__('Activated On') . ':</td><td>' . gmdate("Y-m-d H:i:s") . ' GMT </td></tr>';
                    }
                    break;
                case 'deactivate':
                    $message = $this->__('Your WPFront User Role Editor Pro license was deactivated on the following site.');
                    $subject = '[' . $blog_name . '] ' . $this->__('WPFront User Role Editor Pro License Deactivated');
                    if ($source === 'user') {
                        $body .= '<tr><td>' . $this->__('Deactivated By') . ':</td><td>' . $current_user->user_firstname . ' ' . $current_user->user_lastname . ' [' . $current_user->user_login . ']' . '</td></tr>';
                        $body .= '<tr><td>' . $this->__('Deactivated On') . ':</td><td>' . gmdate("Y-m-d H:i:s") . ' GMT </td></tr>';
                    }
                    break;
                case 'invalid':
                    $message = $this->__('Your WPFront User Role Editor Pro license is invalid on the following site. Please activate a valid license immediately for the plugin to continue working.');
                    $subject = '[' . $blog_name . '] ' . $this->__('WPFront User Role Editor Pro Invalid License');
                    $body .= '<tr><td>' . $this->__('Invalid URL') . ':</td><td>' . $params['url'] . '</td></tr>';
                    break;
                case 'deactivated':
                    $message = $this->__('Your invalid WPFront User Role Editor Pro license was deactivated on the following site. Please activate a valid license immediately for the plugin to continue working.');
                    $subject = '[' . $blog_name . '] ' . $this->__('WPFront User Role Editor Pro License Deactivated');
                    $body .= '<tr><td>' . $this->__('Deactivated On') . ':</td><td>' . gmdate("Y-m-d H:i:s") . ' GMT </td></tr>';
                    break;
            }

            $body = $message
                    . '<br /><br />'
                    . '<table>'
                    . $body
                    . '</table>';

            if (function_exists('wp_mail')) {
                wp_mail($to, $subject, $body, array('Content-Type: text/html; charset=UTF-8'));
            } else {
                $this->mail_objects[] = (OBJECT) array(
                            'to' => $to,
                            'subject' => $subject,
                            'body' => $body
                );
            }
        }

        public function plugins_loaded() {
            foreach ($this->mail_objects as $value) {
                wp_mail($value->to, $value->subject, $value->body, array('Content-Type: text/html; charset=UTF-8'));
            }
        }
        
        public function license_admin_notice() {
            $class = 'notice notice-error is-dismissible';
            
            if($this->license_status == 'invalid') {
                $message = sprintf($this->__('Your %s license is invalid.'), $this->product);
                printf( '<div class="%1$s"><p><strong>%2$s</strong></p></div>', $class, $message); 
            } elseif($this->license_status == 'expired') {
                if(current_user_can('manage_options') || current_user_can('activate_plugins') || current_user_can('install_plugins')) {
                    $renew = $this->__('Renew now');
                    if(!empty($this->renew_url)) {
                        $renew = sprintf('<a target="_blank" href="%s">%s</a>', $this->renew_url, $renew);
                    }
                    $message = sprintf($this->__('Your %s license is expired. %s to receive automatic updates.'), $this->product, $renew);
                    printf( '<div class="%1$s"><p><strong>%2$s</strong></p></div>', $class, $message); 
                }
            }
        }
    }

}