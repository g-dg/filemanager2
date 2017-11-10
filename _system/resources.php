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

	public static function serveFile($filename)
	{
		if (file_exists($filename) && is_readable($filename)) {
			$cache_time = Config::get('_resources_cache_time', 3600);
			header('Content-Type: ' . Filesystem::getContentType($filename, true));
			header('Content-Length: ' . filesize($filename));
			header('Expires: ' . gmdate('D, d M Y H:i:s ', time() + $cache_time) . 'GMT');
			header('Pragma: cache');
			header('Cache-Control: max-age=' . $cache_time . ', private');
			fpassthru(@fopen($filename, 'rb'));
		} else {
			Router::execErrorPage(404);
		}
	}

	public static function serve($resource)
	{
		if (isset(self::$registered_resources[$resource])) {
			call_user_func(self::$registered_resources[$resource]);
		} else {
			Router::execErrorPage(404);
		}
	}
}

Router::registerPage('resource', __NAMESPACE__ . '\\Resources::serve');
