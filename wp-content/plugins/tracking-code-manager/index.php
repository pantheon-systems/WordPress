<?php
/*
Plugin Name: Tracking Code Manager
Plugin URI: http://intellywp.com/tracking-code-manager/
Description: A plugin to manage ALL your tracking code and conversion pixels, simply. Compatible with Facebook Ads, Google Adwords, WooCommerce, Easy Digital Downloads, WP eCommerce.
Author: IntellyWP
Author URI: http://intellywp.com/
Email: info@intellywp.com
Version: 1.11.7
*/
if(defined('TCMP_PLUGIN_NAME')) {
    function tcmp_admin_notices() {
        global $tcmp; ?>
        <div style="clear:both"></div>
        <div class="error iwp" style="padding:10px;">
            <?php $tcmp->Lang->P('PluginProAlreadyInstalled'); ?>
        </div>
        <div style="clear:both"></div>
    <?php }
    add_action('admin_notices', 'tcmp_admin_notices');
    return;
}
define('TCMP_PLUGIN_PREFIX', 'TCMP_');
define('TCMP_PLUGIN_FILE',__FILE__);
define('TCMP_PLUGIN_SLUG', 'tracking-code-manager');
define('TCMP_PLUGIN_NAME', 'Tracking Code Manager');
define('TCMP_PLUGIN_VERSION', '1.11.7');
define('TCMP_PLUGIN_AUTHOR', 'IntellyWP');

define('TCMP_PLUGIN_DIR', dirname(__FILE__).'/');
define('TCMP_PLUGIN_ASSETS_URI', plugins_url( 'assets/', __FILE__ ));
define('TCMP_PLUGIN_IMAGES_URI', plugins_url( 'assets/images/', __FILE__ ));

define('TCMP_LOGGER', FALSE);
define('TCMP_AUTOSAVE_LANG', FALSE);

define('TCMP_QUERY_POSTS_OF_TYPE', 1);
define('TCMP_QUERY_POST_TYPES', 2);
define('TCMP_QUERY_CATEGORIES', 3);
define('TCMP_QUERY_TAGS', 4);
define('TCMP_QUERY_CONVERSION_PLUGINS', 5);
define('TCMP_QUERY_TAXONOMY_TYPES', 6);
define('TCMP_QUERY_TAXONOMIES_OF_TYPE', 7);

define('TCMP_INTELLYWP_SITE', 'http://www.intellywp.com/');
define('TCMP_INTELLYWP_ENDPOINT', TCMP_INTELLYWP_SITE.'wp-content/plugins/intellywp-manager/data.php');
define('TCMP_PAGE_FAQ', TCMP_INTELLYWP_SITE.'tracking-code-manager');
define('TCMP_PAGE_PREMIUM', TCMP_INTELLYWP_SITE.'tracking-code-manager');
define('TCMP_PAGE_MANAGER', admin_url().'options-general.php?page='.TCMP_PLUGIN_SLUG);
define('TCMP_PLUGIN_URI', plugins_url('/', __FILE__ ));

define('TCMP_POSITION_HEAD', 0);
define('TCMP_POSITION_BODY', 1);
define('TCMP_POSITION_FOOTER', 2);
define('TCMP_POSITION_CONVERSION', 3);

define('TCMP_TRACK_MODE_CODE', 0);
define('TCMP_TRACK_PAGE_ALL', 0);
define('TCMP_TRACK_PAGE_SPECIFIC', 1);

define('TCMP_DEVICE_TYPE_MOBILE', 'mobile');
define('TCMP_DEVICE_TYPE_TABLET', 'tablet');
define('TCMP_DEVICE_TYPE_DESKTOP', 'desktop');
define('TCMP_DEVICE_TYPE_ALL', 'all');

define('TCMP_TAB_EDITOR', 'editor');
define('TCMP_TAB_EDITOR_URI', TCMP_PAGE_MANAGER.'&tab='.TCMP_TAB_EDITOR);
define('TCMP_TAB_MANAGER', 'manager');
define('TCMP_TAB_MANAGER_URI', TCMP_PAGE_MANAGER.'&tab='.TCMP_TAB_MANAGER);
define('TCMP_TAB_SETTINGS', 'settings');
define('TCMP_TAB_SETTINGS_URI', TCMP_PAGE_MANAGER.'&tab='.TCMP_TAB_SETTINGS);
define('TCMP_TAB_DOCS', 'docs');
define('TCMP_TAB_DOCS_URI', 'http://support.intellywp.com/category/57-tracking-code-manager');
define('TCMP_TAB_DOCS_DCV_URI', 'http://support.intellywp.com/article/28-dynamic-conversion-values');
define('TCMP_TAB_ABOUT', 'about');
define('TCMP_TAB_ABOUT_URI', TCMP_PAGE_MANAGER.'&tab='.TCMP_TAB_ABOUT);
define('TCMP_TAB_WHATS_NEW', 'whatsnew');
define('TCMP_TAB_WHATS_NEW_URI', TCMP_PAGE_MANAGER.'&tab='.TCMP_TAB_WHATS_NEW);

define('TCMP_SNIPPETS_LIMIT', 6);

include_once(dirname(__FILE__).'/autoload.php');
tcmp_include_php(dirname(__FILE__).'/includes/');

global $tcmp;
$tcmp=new TCMP_Singleton();
$tcmp->init();

function TCMP_QS($name, $default='') {
    global $tcmp;
    $result=$tcmp->Utils->qs($name, $default);
    return $result;
}
//SANITIZED METHODS
function TCMP_SQS($name, $default='') {
    $result=TCMP_QS($name, $default);
    $result=sanitize_text_field($result);
    return $result;
}
function TCMP_ISQS($name, $default=0) {
    $result=TCMP_SQS($name, $default);
    $result=floatval($result);
    return $result;
}
function TCMP_BSQS($name, $default=0) {
    global $tcmp;
    $result=$tcmp->Utils->bqs($name, $default);
    return $result;
}
function TCMP_ASQS($name, $default=array()) {
    $result=TCMP_QS($name, $default);
    if(is_array($result)) {
        foreach ($result as $k=>$v) {
            $result[$k]=sanitize_text_field($v);
        }
    } else {
        $result=sanitize_text_field($result);
    }
    return $result;
}

