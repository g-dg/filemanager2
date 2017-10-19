<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class ErrorPages
{
	public static function registerErrorPages()
	{
		Router::registerErrorPage(404,  __CLASS__ . '::errorPage');
	}

	public static function errorPage($http_error, $page)
	{
		echo 'Error: ' . $http_error . ': ' . $page;
	}
}

Loader::registerInit(__NAMESPACE__ . '\ErrorPages::registerErrorPages');
