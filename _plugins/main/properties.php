<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('properties', function($path) {
	Auth::authenticate();
	MainUiTemplate::header('Properties of /'.$path);

	MainUiTemplate::footer();
});
