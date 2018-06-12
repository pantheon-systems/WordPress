<?php
/*
Plugin Name: Qiigo Integration
Plugin URI: http://qiigo.com/
Description: Custom CRM Intregration with ExtraView and FranConnect
Version: 1.0
Author: Qiigo
Author URI: http://qiigo.com/
License: All Rights Reserved
*/

defined('ABSPATH') or die( 'No script kiddies please!' );
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

/*ini_set('display_errors', 1);
error_reporting(E_ALL);*/

$libPath = dirname(__FILE__) . DS . 'lib' . DS;
require_once($libPath.'Autoloader.php');

\Qiigo\Plugin\Integration\Autoloader::Register('Qiigo\Plugin\Integration', $libPath);
$plugin = \Qiigo\Plugin\Integration\Plugin::Setup();
