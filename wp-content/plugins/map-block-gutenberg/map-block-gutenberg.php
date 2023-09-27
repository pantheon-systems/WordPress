<?php
/*
Plugin Name: Map Block for Google Maps
Description: Simple, no-nonsense map block powered by Google Maps for Gutenberg editor.
Author: WebFactory Ltd
Version: 1.32
Author URI: https://www.webfactoryltd.com/
Text Domain: map-block-gutenberg

  Copyright 2018 - 2022  WebFactory Ltd  (email : support@webfactoryltd.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// die if accessed directly
if (!defined('ABSPATH')) {
  die();
}


class wf_map_block
{
  static $version;

  // get plugin version from header
  static function get_plugin_version()
  {
    $plugin_data = get_file_data(__FILE__, array('version' => 'Version'), 'plugin');
    self::$version = $plugin_data['version'];

    return $plugin_data['version'];
  } // get_plugin_version


  // hook things up
  static function init()
  {
    if (is_admin()) {
      if (false === self::check_gutenberg()) {
        return false;
      }

      add_filter(
        'plugin_action_links_' . basename(dirname(__FILE__)) . '/' . basename(__FILE__),
        array(__CLASS__, 'plugin_action_links')
      );
      add_filter('plugin_row_meta', array(__CLASS__, 'plugin_meta_links'), 10, 2);

      add_action('enqueue_block_editor_assets', array(__CLASS__, 'enqueue_block_editor_assets'));

      add_action('wp_ajax_gmw_map_block_save_key', array(__CLASS__, 'save_key'));
    }
  } // init


  static function save_key()
  {
    check_ajax_referer('map-block-gutenberg_save_api_key');

    $key = substr(sanitize_html_class(strip_tags(@$_POST['api_key'])), 0, 64);
    update_option('gmw-map-block-key', $key);

    echo $key;
    die();
  } // save_key


  // some things have to be loaded earlier
  static function plugins_loaded()
  {
    self::$version = self::get_plugin_version();
  } // plugins_loaded


  // add links to plugins page
  static function plugin_action_links($links)
  {
    $gutenberg_link = '<a href="' . admin_url('post-new.php?post_type=page') . '" title="' . __('Create a new page using the Gutenberg editor', 'map-block-gutenberg') . '">' . __('Create with Gutenberg', 'map-block-gutenberg') . '</a>';

    array_unshift($links, $gutenberg_link);

    return $links;
  } // plugin_action_links


  // add links to plugin's description in plugins table
  static function plugin_meta_links($links, $file)
  {
    $support_link = '<a target="_blank" href="https://wordpress.org/support/plugin/map-block-gutenberg" title="' . __('Problems? We are here to help!', 'map-block-gutenberg') . '">' . __('Support', 'map-block-gutenberg') . '</a>';
    $review_link = '<a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/map-block-gutenberg?filter=5#pages" title="' . __('If you like it, please review the plugin', 'map-block-gutenberg') . '">' . __('Review the plugin', 'map-block-gutenberg') . '</a>';

    if ($file == plugin_basename(__FILE__)) {
      $links[] = $support_link;
      $links[] = $review_link;
    }

    return $links;
  } // plugin_meta_links


  // enqueue block files
  static function enqueue_block_editor_assets()
  {
    // enqueue the bundled block JS file
    wp_register_script(
      'wf-map-block',
      plugins_url('/assets/js/editor.blocks.js', __FILE__),
      ['wp-editor', 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components'],
      self::$version
    );

    $api_key = get_option('gmw-map-block-key') ? get_option('gmw-map-block-key') : 'AIzaSyAjyDspiPfzEfjRSS5fQzm-3jHFjHxeXB4';
    $wf_map_block = array(
      'api_key' => $api_key,
      'nonce_save_api_key' => wp_create_nonce('map-block-gutenberg_save_api_key'),
      '_description' => __('Simple yet powerful map block powered by Google Maps.', 'map-block-gutenberg'),
      '_map' => __('Map', 'map-block-gutenberg'),
      '_map_lc' => __('map', 'map-block-gutenberg'),
      '_location_lc' => __('location', 'map-block-gutenberg'),
      '_address' => __('Address', 'map-block-gutenberg'),
      '_zoom' => __('Zoom', 'map-block-gutenberg'),
      '_height' => __('Height', 'map-block-gutenberg'),
      '_api_key' => __('API Key', 'map-block-gutenberg'),
      '_api_info_start' => __('Please create your own API key on the', 'map-block-gutenberg'),
      '_api_info_console' => __('Google Console', 'map-block-gutenberg'),
      '_api_info_end' => __('This is a requirement enforced by Google.', 'map-block-gutenberg')
    );
    wp_localize_script('wf-map-block', 'wf_map_block', $wf_map_block);

    wp_enqueue_script('wf-map-block');

    // enqueue optional editor only styles
    wp_enqueue_style(
      'wf-map-block',
      plugins_url('/assets/css/blocks.editor.css', __FILE__),
      ['wp-editor'],
      self::$version
    );
  } // enqueue_block_editor_assets


  // check if Gutenberg is available
  static function check_gutenberg()
  {
    if (false === defined('GUTENBERG_VERSION') && false === version_compare(get_bloginfo('version'), '5.0', '>=')) {
      add_action('admin_notices', array(__CLASS__, 'notice_gutenberg_missing'));
      return false;
    }
  } // check_gutenberg


  // complain if Gutenberg is not available
  static function notice_gutenberg_missing()
  {
    echo '<div class="error"><p>';
    echo __('Map Block for Google Maps requires the Gutenberg plugin to work. It is after all a block for Gutenberg.', 'map-block-gutenberg') . '<br>';
    echo sprintf(__('Install the <a href="%s" target="_blank">Gutenberg plugin</a> or update your WordPress core and this notice will go away.', 'map-block-gutenberg'), 'https://wordpress.org/plugins/gutenberg/');
    echo '</p></div>';
  } // notice_gutenberg_missing
} // class


// get the party started
add_action('init', array('wf_map_block', 'init'));
add_action('plugins_loaded', array('wf_map_block', 'plugins_loaded'));
