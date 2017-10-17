<?php
namespace GarnetDG\FileManager2;

const VERSION = '2.0.0-dev';

require_once('_system/config.php');
Config::load();

require_once('_system/log.php');
Log::setUp();

require_once('_system/loader.php');
Loader::loadAll();

Plugins::loadAll();

Router::execCurrentPage();
