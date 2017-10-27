<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Resources
{
	protected static $registered_resources = [];

	public static function register($name, $callback)
	{
		if (!isset(self::$registered_resources[$name])) {
			self::$registered_resources[$name] = $callback;
		} else {
			Log::error('The resource "' . $name . '" has already been registered');
		}
	}
}

Router::registerPage('resource', function($resource) {
	if (isset(self::$registered_resources[$resource])) {
		call_user_func($registered_resources[$resource]);
	} else {
		Router::execErrorPage(404);
	}
});
