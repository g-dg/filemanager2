<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

require_once('template.php');

require_once('about.php');
require_once('account.php');
require_once('admin.php');
require_once('browse.php');
require_once('error.php');
require_once('file.php');
require_once('login.php');
require_once('logout.php');
require_once('properties.php');
require_once('settings.php');

Router::registerPage('index', function() {
	Router::redirect('/browse');
});
