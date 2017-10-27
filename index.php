<?php
namespace GarnetDG\FileManager;

//TODO: remove error reporting for release
error_reporting(E_ALL);
ini_set('display_errors', 'On');

define('GARNETDG_FILEMANAGER_VERSION', '2.0.0-dev');

set_time_limit(3600);

require_once('_system/config.php');

require_once('_system/log.php');

Log::info('Request started');

require_once('_system/loader.php');
Loader::loadAll();

Plugins::loadAll();

Router::execCurrentPage();

Log::info('Request finished successfully in ' . sprintf('%.4f', microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) . ' seconds');
