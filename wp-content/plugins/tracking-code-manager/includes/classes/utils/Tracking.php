<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Usage tracking
 *
 * @access public
 * @since  1.8.2
 * @return void
 */
class TCMP_Tracking {

    public function __construct() {
        //We send once a week (while tracking is allowed) to check in which can be used
        //to determine active sites
        add_action('tcmp_weekly_scheduled_events', array($this, 'sendTracking'));

        //add_action('tcmp_tracking_on', array($this, 'enableTracking'));
        //add_action('tcmp_tracking_off', array($this, 'disableTracking'));
        //add_action('admin_notices', array($this, 'admin_notice'));
    }

    private function getThemeData() {
        $theme_data     = wp_get_theme();
        $theme          = array(
            'name'       => $theme_data->display( 'Name', false, false ),
            'theme_uri'  => $theme_data->display( 'ThemeURI', false, false ),
            'version'    => $theme_data->display( 'Version', false, false ),
            'author'     => $theme_data->display( 'Author', false, false ),
            'author_uri' => $theme_data->display( 'AuthorURI', false, false ),
        );
        $theme_template = $theme_data->get_template();
        if ( $theme_template !== '' && $theme_data->parent() ) {
            $theme['template'] = array(
                'version'    => $theme_data->parent()->display( 'Version', false, false ),
                'name'       => $theme_data->parent()->display( 'Name', false, false ),
                'theme_uri'  => $theme_data->parent()->display( 'ThemeURI', false, false ),
                'author'     => $theme_data->parent()->display( 'Author', false, false ),
                'author_uri' => $theme_data->parent()->display( 'AuthorURI', false, false ),
            );
        }
        else {
            $theme['template'] = '';
        }
        unset( $theme_template );
        return $theme;
    }
    private function getPluginData() {
        //retrieve plugins (active/inactive) list
        if(!function_exists('get_plugins')) {
            include ABSPATH . '/wp-admin/includes/plugin.php';
        }

        $plugins=array();
        $active_plugin = get_option( 'active_plugins' );
        foreach ( $active_plugin as $plugin_path ) {
            if ( ! function_exists( 'get_plugin_data' ) ) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }

            $plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path );

            $slug= str_replace( '/' . basename( $plugin_path ), '', $plugin_path );
            $plugins[ $slug ] = array(
                'version'    => $plugin_info['Version'],
                'name'       => $plugin_info['Name'],
                'plugin_uri' => $plugin_info['PluginURI'],
                'author'     => $plugin_info['AuthorName'],
                'author_uri' => $plugin_info['AuthorURI'],
            );
        }
        unset( $active_plugins, $plugin_path );
        return $plugins;
    }
    //obtain tracking data into an associative array
    public function getData() {
        global $tcmp;

        //retrieve blog info
        $result['wp_url']=home_url();
        $result['wp_version']=get_bloginfo('version');
        $result['wp_language']=get_bloginfo('language');
        $result['wp_wpurl']=get_bloginfo('wpurl');
        $result['wp_admin_email']=get_bloginfo('admin_email');

        $result['plugins']=$this->getPluginData();
        $result['theme']=$this->getThemeData();

        //to obtain for each post type its count
        $post_types=$tcmp->Utils->query(TCMP_QUERY_POST_TYPES);
        $data=array();
        foreach ($post_types as $v) {
            $v=$v['id'];
            $data[$v]=intval(wp_count_posts($v)->publish);
        }
        $result['post_types']=$data;

        //plugin configuration without secret-code
        $data=array();
        $keys=$tcmp->Manager->keys();
        foreach($keys as $k) {
            $v=$tcmp->Manager->get($k);
            //to allow us to receive a part of the code and to protect user privacy
            //we use this data only to know which SaaS services are used
            //and not to use your data
            $v['code']=substr($v['code'], 0, 100);
            $data[]=$v;
        }
        $result['iwpm_plugin_name']=TCMP_PLUGIN_SLUG;
        $result['iwpm_plugin_version']=TCMP_PLUGIN_VERSION;
        $result['iwpm_plugin_data']=$data;
        $result['iwpm_plugin_install_date']=$tcmp->Options->getPluginInstallDate();
        $result['iwpm_plugin_update_date']=$tcmp->Options->getPluginUpdateDate();

        $result['iwpm_license_key']=$tcmp->Options->getLicenseKey();
        $result['iwpm_license_status']=$tcmp->Options->isLicenseSuccess();
        $result['iwpm_tracking_enable']=$tcmp->Options->isTrackingEnable();
        $result['iwpm_logger_enable']=$tcmp->Options->isLoggerEnable();
        $result['iwpm_feedback_email']=$tcmp->Options->getFeedbackEmail();

        //var_dump($result);
        return $result;
    }

    //send tracking data info to our server
    public function sendTracking($override = FALSE) {
        global $tcmp;

        $result=-1;
        if(!$override && !$tcmp->Options->isTrackingEnable())
            return $result;

        // Send a maximum of once per week
        $last_send=$tcmp->Options->getTrackingLastSend();
        if(!$override && $last_send>strtotime('-1 week'))
            return $result;

        //add_filter('https_local_ssl_verify', '__return_false');
        //add_filter('https_ssl_verify', '__return_false');
        //add_filter('block_local_requests', '__return_false');

        $data=$tcmp->Utils->remotePost('usage', $this->getData());
        if($data) {
            $result=intval($data['id']);
            $tcmp->Options->setTrackingLastSend(time());
        }
        return $result;
    }

    public function enableTracking() {
        global $tcmp;

        $tcmp->Options->setTrackingEnable(TRUE);
        $tcmp->Options->setTrackingNotice(FALSE);
        $this->sendTracking(TRUE);
    }
    public function disableTracking() {
        global $tcmp;

        $tcmp->Options->setTrackingEnable(FALSE);
        $tcmp->Options->setTrackingNotice(FALSE);
        $this->sendTracking(TRUE);
    }
}