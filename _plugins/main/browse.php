<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('browse', function($path) {
	Auth::authenticate();
	MainUiTemplate::header('/' . $path);

	MainUiTemplate::footer();
});
