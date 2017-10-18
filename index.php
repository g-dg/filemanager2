<?php
namespace GarnetDG\FileManager2;

//TODO: remove error reporting for release
error_reporting(E_ALL);
ini_set('display_errors', 'On');

define('GARNETDG_FILEMANAGER2_VERSION', '2.0.0-dev');

require_once('_system/config.php');
Config::load();

require_once('_system/log.php');
Log::setup();

require_once('_system/loader.php');
Loader::loadAll();

Plugins::loadAll();

Router::execCurrentPage();
