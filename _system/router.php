<?php
namespace GarnetDG\FileManager2;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Router
{
	protected static $registered_pages = [];
	protected static $registered_error_pages = [];

	public static function registerPage($name, $callback)
	{
		self::$registered_pages[$name] = $callback;
	}

	public static function registerErrorPage($http_error, $callback)
	{
		self::$registered_error_pages[$http_error] = $callback;
	}

	public static function execCurrentPage()
	{

	}

	public static function getBaseHttpPath()
	{

	}

	public static function getCurrentPath()
	{

	}
}
