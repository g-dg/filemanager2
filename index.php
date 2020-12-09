<?php
namespace GarnetDG\FileManager;

define('GARNETDG_FILEMANAGER_VERSION', '2.5.1');
define('GARNETDG_FILEMANAGER_COPYRIGHT', 'Copyright &copy; 2017-2018,2020 Garnet DeGelder');

set_time_limit(3600);
ignore_user_abort(true);

require_once('_system/config.php');
require_once('_system/log.php');

if (Config::get('debug')) {
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
}

Log::info('Request started');

require_once('_system/loader.php');
Loader::loadAll();

Extensions::loadAll();

Router::execCurrentPage();

Log::info('Request finished successfully in ' . sprintf('%.4f', microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) . ' seconds');
