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
		$current_page = self::getCurrentPage();
		if (isset(self::$registered_pages[$current_page])) {
			call_user_func($registered_pages[$current_page], self::getCurrentPageParameters());
		} else {
			self::execErrorPage(404);
		}
	}

	public static function execErrorPage($http_error)
	{
		if (isset(self::$registered_error_pages[$http_error])) {
			call_user_func($registered_error_pages[$http_error], $http_error, self::getCurrentPageAndParameters());
		} else {
			http_response_code($http_error);
		}
		exit();
	}

	public static function getCurrentPage()
	{
		if (isset($_SERVER['PATH_INFO'])) {
			$clean_path_array = [];
			$dirty_path_array = explode('/', $_SERVER['PATH_INFO']);
			foreach ($dirty_path_array as $path_part) {
				if ($path_part !== '') {
					$clean_path_array[] = $path_part;
				}
			}
			if (isset($clean_path_array[0])) {
				return $clean_path_array[0];
			}
		}
		return Config::get('default_page');
	}

	public static function getCurrentPageParameters()
	{
		if (isset($_SERVER['PATH_INFO'])) {
			$clean_path_array = [];
			$dirty_path_array = explode('/', $_SERVER['PATH_INFO']);
			foreach ($dirty_path_array as $path_part) {
				if ($path_part !== '') {
					$clean_path_array[] = $path_part;
				}
			}
			if (count($clean_path_array) > 1) {
				array_shift($clean_path_array);
				return implode('/', $clean_path_array);
			}
		}
		return '';
	}

	public static function getCurrentPageAndParameters()
	{
		return self::getCurrentPage() . '/' . self::getCurrentPageParameters();
	}

	public static function getHttpReadyUri($full_path)
	{
		$raw_path_array = explode('/', $full_path);
		$encoded_path_array = [];
		foreach ($raw_path_array as $pathpart) {
			if ($pathpart !== '') {
				// urlencode changes spaces into pluses. That only works with get requests, not paths.
				$encoded_path_array[] = str_replace('+', '%20', urlencode($pathpart));
			}
		}
		$encoded_path = '/' . implode('/', $encoded_path_array);

		// get the base uri
		$config_base_uri = Config::get('base_uri');
		if (is_null($config_base_uri)) {
			$base_uri = trim(pathinfo($_SERVER['SCRIPT_NAME'])['dirname'], '/');
			if ($base_uri === '') {
				$base_uri = '/';
			} else {
				$base_uri = '/' . $base_uri . '/';
			}
		} else {
			$base_uri = $config_base_uri;
		}

		// append the configured script name on the end
		$base_uri = $base_uri . Config::get('index_page');

		return $base_uri . $encoded_path;
	}
}
