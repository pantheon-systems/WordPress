<?php
/*
Plugin Name: Qiigo Local Integration
Plugin URI: http://qiigo.com/
Description: Custom CRM Intregration with ExtraView and FranConnect for Local Sites
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

\Qiigo\Plugin\LocalIntegration\Autoloader::Register('Qiigo\Plugin\LocalIntegration', $libPath);
$plugin = \Qiigo\Plugin\LocalIntegration\Plugin::Setup();
