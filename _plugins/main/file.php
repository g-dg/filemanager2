<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('file', function($path) {
	$path_array = explode('/', trim($path, '/'));
	Session::start($path_array[0]);
	Auth::authenticate();
	array_shift($path_array);
	$file = implode('/', $path_array);
	HttpRange::send($file);
});

Router::registerPage('download', function($file) {
	Auth::authenticate();
	HttpRange::send($file, true);
});
