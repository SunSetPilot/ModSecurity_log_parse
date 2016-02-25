<?php
//the project is based on PHP7.0.2
//ini_set('zend.ze1_compatibility_mode', 'off');

ini_set('display_errors', 1);
ini_set('max_execution_time', 1000);
ini_set("memory_limit", "1024M");
error_reporting(0);
date_default_timezone_set("PRC");

//Define base path
defined('BASE_PATH')
|| define('BASE_PATH', realpath(dirname(__FILE__) . '/'));

//Define path to application directory
defined('APP_PATH')
|| define('APP_PATH', BASE_PATH . '/application');

//Define application environment
defined('APP_ENV')
|| define('APP_ENV', (getenv('APP_ENV') ? getenv('APP_ENV') : 'dev'));

if (APP_ENV == 'dev') error_reporting(E_ALL ^ E_NOTICE);

defined('CONFIGS_PATH')
||define('CONFIGS_PATH',APP_PATH.'/configs');
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    BASE_PATH . '/library/vendor',
//    APP_PATH,
    get_include_path()
)));
require_once 'autoload.php';
$o_run = new Run;
$o_run->init();



